<?php

namespace IGD;

defined( 'ABSPATH' ) || exit();

class Migration_1_5_1 {

	private static $instance;
	private $batch_size = 50;

	// List of migration steps and their method names
	private $steps = [
		'classic_shortcodes' => 'migrate_classic_shortcodes',
		'blocks'             => 'migrate_blocks',
		'widgets'            => 'migrate_widgets',
		'modules'            => 'migrate_modules',

		'fluentforms'     => 'migrate_fluentforms',
		'wpforms'         => 'migrate_wpforms',
		'gravityforms'    => 'migrate_gravityforms',
		'formidableforms' => 'migrate_formidableforms',
		'ninjaforms'      => 'migrate_ninjaforms',
		'cf7'             => 'migrate_cf7',
	];

	// Holds the running step and offsets between AJAX calls
	private $state = [];

	public function __construct() {
		update_option( 'igd_migration_1_5_1_status', 'running' );
	}

	public static function instance() {
		if ( self::$instance === null ) {
			self::$instance = new self();
		}

		return self::$instance;
	}

	public function run_batch() {
		$this->load_state();

		$current_step   = $this->state['step'];
		$current_offset = (int) $this->state['offset'];

		// Get the next step to run
		$next_step = null;
		foreach ( $this->steps as $key => $method ) {
			if ( $key === $current_step ) {
				$next_step = $method;
				break;
			}
		}

		if ( ! $next_step ) {
			// All steps done
			$this->delete_state();

			return [
				'completed' => true,
				'message'   => __( 'All migration steps are complete.', 'integrate-google-drive' )
			];
		}

		// Run the batch for the current step
		$result = $this->$next_step( $current_offset );

		// If batch is done, move to next step
		if ( ! empty( $result['completed'] ) ) {
			$next_key      = array_search( $current_step, array_keys( $this->steps ) );
			$step_keys     = array_keys( $this->steps );
			$next_step_key = isset( $step_keys[ $next_key + 1 ] ) ? $step_keys[ $next_key + 1 ] : null;

			if ( $next_step_key ) {
				$this->state['step']   = $next_step_key;
				$this->state['offset'] = 0;
				$this->save_state();
				$result['message']   .= ' ' . sprintf( __( 'Proceeding to: %s', 'integrate-google-drive' ), $next_step_key );
				$result['completed'] = false; // Let AJAX handler continue to next step
			} else {
				$this->delete_state();
				$result['message']   .= ' ' . __( 'All migration steps are complete.', 'integrate-google-drive' );
				$result['completed'] = true;
			}
		} else {
			// Continue next batch of same step
			$this->state['offset'] = $result['offset'];
			$this->save_state();
		}

		return $result;
	}

	// --- Classic Shortcodes Migration ---
	private function migrate_classic_shortcodes( $offset ) {
		global $wpdb;
		$batch_size = $this->batch_size;
		$posts      = $wpdb->get_results( $wpdb->prepare(
			"SELECT ID, post_content, post_type
             FROM {$wpdb->posts}
             WHERE post_content LIKE %s
             AND post_status IN ('publish','draft','pending','future')
             LIMIT %d OFFSET %d",
			'%[integrate_google_drive %data=%', $batch_size, $offset
		) );

		if ( ! $posts ) {
			return [
				'completed' => true,
				'message'   => __( 'Classic shortcode migration complete.', 'integrate-google-drive' )
			];
		}

		$processed = 0;
		foreach ( $posts as $post ) {
			$content = $post->post_content;

			$pattern = get_shortcode_regex( [ 'integrate_google_drive' ] );

			$updated = false;

			$new_content = preg_replace_callback( "/$pattern/", function ( $m ) use ( &$updated, $post ) {
				if ( $m[2] !== 'integrate_google_drive' ) {
					return $m[0];
				}

				$atts = shortcode_parse_atts( $m[3] );

				if ( empty( $atts['data'] ) ) {
					return $m[0];
				}

				$config = json_decode( base64_decode( $atts['data'] ), true );

				$config = $this->migrate_list_module( $config );
				$config = $this->migrate_usage_limits( $config );

				$suffix = $this->get_post_type_suffix( $post->post_type );

				$insert_id = $this->create_module( $config, $suffix, $post->ID );

				$new_shortcode = '[integrate_google_drive id="' . $insert_id . '"]';
				$updated       = true;

				return $new_shortcode;
			}, $content );

			if ( $updated && $new_content !== $content ) {
				wp_update_post( [ 'ID' => $post->ID, 'post_content' => $new_content ] );
			}

			$processed ++;
		}

		$next_offset = $offset + $batch_size;

		return [
			'completed' => false,
			'step'      => 'Classic Shortcodes',
			'offset'    => $next_offset,
			'message'   => sprintf( __( 'Classic shortcodes: Processed %d posts... (offset %d)', 'integrate-google-drive' ), $processed, $next_offset )
		];
	}

	// --- Blocks Migration ---
	private function migrate_blocks( $offset ) {
		global $wpdb;

		$batch_size = $this->batch_size;

		$block_types = [
			'igd/browser',
			'igd/gallery',
			'igd/uploader',
			'igd/search',
			'igd/embed',
			'igd/media',
			'igd/slider',
			'igd/view',
			'igd/download'
		];

		$likes = [];
		foreach ( $block_types as $block ) {
			$likes[] = "post_content LIKE '%wp:" . $block . "%data%'";
		}
		$like_query = implode( ' OR ', $likes );

		$posts = $wpdb->get_results(
			"SELECT ID, post_content, post_type
         FROM {$wpdb->posts}
         WHERE ($like_query)
         AND post_status IN ('publish','draft','pending','future')
         LIMIT $batch_size OFFSET $offset"
		);

		if ( ! $posts ) {
			return [
				'completed' => true,
				'message'   => __( 'Block migration complete.', 'integrate-google-drive' )
			];
		}
		$processed = 0;

		// Updated pattern: match whole block JSON, non-greedy, multiline
		$block_pattern = '/<!--\s*wp:(igd\/(?:browser|gallery|uploader|search|media|embed|slider|view|download))\s+({.*?})\s*\/-->/s';

		foreach ( $posts as $post ) {
			$content = $post->post_content;
			$updated = false;

			$new_content = preg_replace_callback( $block_pattern, function ( $m ) use ( &$updated, $post ) {
				$block_json = $m[2];
				$block_data = json_decode( $block_json, true );

				if ( ! is_array( $block_data ) || empty( $block_data['data'] ) ) {
					return $m[0];
				}

				$config = $block_data['data'];

				$config = $this->migrate_list_module( $config );
				$config = $this->migrate_usage_limits( $config );

				$suffix    = $this->get_post_type_suffix( $post->post_type );
				$insert_id = $this->create_module( $config, $suffix, $post->ID );

				$updated = true;

				return '<!-- wp:igd/shortcodes {"id":' . $insert_id . '} /-->';
			}, $content );

			if ( $updated && $new_content !== $content ) {
				wp_update_post( [ 'ID' => $post->ID, 'post_content' => $new_content ] );
			}
			$processed ++;
		}

		$next_offset = $offset + $batch_size;

		return [
			'completed' => false,
			'step'      => 'Blocks',
			'offset'    => $next_offset,
			'message'   => sprintf( __( 'Blocks: Processed %d posts... (offset %d)', 'integrate-google-drive' ), $processed, $next_offset )
		];
	}

	/*--- Elementor Widgets Migration ---*/
	private function migrate_widgets( $offset ) {
		global $wpdb;
		$batch_size = $this->batch_size;
		$post_types = "'page','post','elementor_library', 'metform-form'";

		$widget_keys = [
			'igd_browser',
			'igd_gallery',
			'igd_uploader',
			'igd_embed',
			'igd_slider',
			'igd_search',
			'igd_view',
			'igd_media',
			'igd_download',
			'mf-igd-uploader',
		];

		// Query posts that have _elementor_data meta
		$posts = $wpdb->get_results(
			$wpdb->prepare(
				"SELECT p.ID,  p.post_type
             FROM {$wpdb->posts} p
             INNER JOIN {$wpdb->postmeta} m ON p.ID = m.post_id
             WHERE p.post_type IN ($post_types)
             AND p.post_status IN ('publish','draft','pending','future')
             AND m.meta_key = %s
             LIMIT %d OFFSET %d",
				'_elementor_data',
				$batch_size,
				$offset
			)
		);

		if ( ! $posts ) {
			return [
				'completed' => true,
				'message'   => __( 'Elementor widget migration complete.', 'integrate-google-drive' )
			];
		}

		$processed = 0;

		foreach ( $posts as $post ) {
			$elementor_json = get_post_meta( $post->ID, '_elementor_data', true );
			$elementor_data = json_decode( $elementor_json, true );
			if ( ! is_array( $elementor_data ) ) {
				continue;
			}

			$updated        = false;
			$elementor_data = $this->recursive_elementor_widget_migration( $elementor_data, $widget_keys, $post, $updated );

			if ( $updated ) {
				$new_content = wp_json_encode( $elementor_data );
				if ( $new_content !== $elementor_json ) {

					// Update the post meta with the new content
					$wpdb->query(
						$wpdb->prepare(
							"
                                UPDATE {$wpdb->postmeta}
                                SET meta_value = %s
                                WHERE post_id = %d AND meta_key = '_elementor_data'
                                ",
							$new_content,
							$post->ID
						)
					);
				}
			}

			$processed ++;
		}

		$next_offset = $offset + $batch_size;

		return [
			'completed' => false,
			'step'      => 'Elementor Widgets',
			'offset'    => $next_offset,
			'message'   => sprintf( __( 'Widgets: Processed %d posts... (offset %d)', 'integrate-google-drive' ), $processed, $next_offset )
		];
	}

	// --- Recursive for Elementor widgets ---
	private function recursive_elementor_widget_migration( $elements, $widget_keys, $post, &$updated ) {

		if ( ! is_array( $elements ) ) {
			return $elements;
		}

		$suffix = $this->get_post_type_suffix( $post->post_type );

		foreach ( $elements as &$el ) {

			// Migrate IGD widgets
			if (
				isset( $el['elType'], $el['widgetType'] ) &&
				$el['elType'] === 'widget' &&
				in_array( $el['widgetType'], $widget_keys, true )
			) {

				if ( ! empty( $el['settings']['module_data'] ) ) {

					// Handle if already array or JSON string
					$config = json_decode( $el['settings']['module_data'], true );

					if ( ! is_array( $config ) ) {
						continue;
					}

					$config = $this->migrate_list_module( $config );
					$config = $this->migrate_usage_limits( $config );

					if ( 'mf-igd-uploader' === $el['widgetType'] ) {
						$suffix = 'Metform';
					} else {
						$el['widgetType'] = 'igd_shortcodes';
					}

					$insert_id                   = $this->create_module( $config, $suffix, $post->ID );
					$el['settings']['module_id'] = $insert_id;

					$updated = true;
				}

			}

			// Convert old shortcode_id to module_id for igd_shortcodes widgets
			if (
				isset( $el['elType'], $el['widgetType'] ) &&
				$el['elType'] === 'widget' &&
				$el['widgetType'] === 'igd_shortcodes'
			) {
				if ( isset( $el['settings']['shortcode_id'] ) ) {
					$el['settings']['module_id'] = $el['settings']['shortcode_id'];
					$updated                     = true;
				}
			}

			// Migrate IGD fields in Elementor Forms
			if (
				isset( $el['elType'], $el['widgetType'] ) &&
				$el['elType'] === 'widget' &&
				$el['widgetType'] === 'form' &&
				! empty( $el['settings']['form_fields'] )
			) {

				$suffix = 'Elementor Form';

				foreach ( $el['settings']['form_fields'] as &$field ) {
					if (
						isset( $field['field_type'] ) &&
						$field['field_type'] === 'google_drive_upload' &&
						! empty( $field['module_data'] )
					) {
						$config = json_decode( $field['module_data'], true );

						if ( ! is_array( $config ) ) {
							continue;
						}

						unset( $config['uniqueId'], $config['isFormUploader'] );

						$module_id = $this->create_module( $config, $suffix, $post->ID );

						$field['module_id'] = $module_id;

						$updated = true;
					}
				}
			}

			// Recursively process inner elements (columns, sections, etc)
			if ( ! empty( $el['elements'] ) && is_array( $el['elements'] ) ) {
				$el['elements'] = $this->recursive_elementor_widget_migration( $el['elements'], $widget_keys, $post, $updated );
			}
		}

		return $elements;
	}

	/*--- Divi Modules Migration ---*/
	private function migrate_modules( $offset ) {
		global $wpdb;
		$batch_size = $this->batch_size;

		$module_types = [
			'igd_browser',
			'igd_gallery',
			'igd_uploader',
			'igd_embed',
			'igd_slider',
			'igd_search',
			'igd_media',
			'igd_view',
			'igd_download'
		];

		$likes = array_map( function ( $mod ) {
			return "post_content LIKE '%[$mod %data=%'";
		}, $module_types );

		$like_query = implode( ' OR ', $likes );

		$posts = $wpdb->get_results(
			"SELECT ID, post_content, post_type
		 FROM {$wpdb->posts}
		 WHERE ($like_query)
		 AND post_status IN ('publish','draft','pending','future')
		 LIMIT $batch_size OFFSET $offset"
		);

		if ( ! $posts ) {
			return [
				'completed' => true,
				'message'   => __( 'Divi module migration complete.', 'integrate-google-drive' )
			];
		}

		$processed = 0;

		// Pattern to match all relevant modules with data attribute
		$pattern = '/\[(igd_(?:browser|gallery|uploader|embed|slider|search|media|view|download))\s+([^\]]*data="[^"]+"[^\]]*)\]/';

		foreach ( $posts as $post ) {
			$content = $post->post_content;
			$updated = false;

			// Decode all module configs found in the content
			$configs = $this->decode_divi_module_data( $content );

			if ( ! $configs || ! is_array( $configs ) || count( $configs ) === 0 ) {
				// No modules found or decoding failed; skip post
				$processed ++;
				continue;
			}

			$index = 0; // Track which decoded config corresponds to which shortcode

			$new_content = preg_replace_callback( $pattern, function ( $m ) use ( &$updated, $configs, &$index, $post ) {
				$config = $configs[ $index ] ?? null;
				$index ++;

				if ( ! is_array( $config ) ) {
					// Invalid config, return original shortcode
					return $m[0];
				}

				// Run migration logic
				$config = $this->migrate_list_module( $config );
				$config = $this->migrate_usage_limits( $config );

				$suffix    = $this->get_post_type_suffix( $post->post_type );
				$insert_id = $this->create_module( $config, $suffix, $post->ID );

				$updated = true;

				return '[igd_shortcodes id="' . $insert_id . '"]';
			}, $content );

			// Build pattern to match closing shortcodes like [/igd_uploader]
			$closing_pattern = '/\[\/(' . implode( '|', $module_types ) . ')\]/i';

			// Remove all closing shortcodes for these modules
			$new_content = preg_replace( $closing_pattern, '', $new_content );

			if ( $updated && $new_content !== $content ) {
				wp_update_post( [ 'ID' => $post->ID, 'post_content' => $new_content ] );
			}

			$processed ++;
		}

		$next_offset = $offset + $batch_size;

		return [
			'completed' => false,
			'step'      => 'Divi Modules',
			'offset'    => $next_offset,
			'message'   => sprintf( __( 'Modules: Processed %d posts... (offset %d)', 'integrate-google-drive' ), $processed, $next_offset )
		];
	}

	private function decode_divi_module_data( $content ) {
		$module_types = [
			'igd_browser',
			'igd_gallery',
			'igd_uploader',
			'igd_embed',
			'igd_slider',
			'igd_search',
			'igd_media',
			'igd_view',
			'igd_download'
		];

		$pattern = '/\[(?:' . implode( '|', $module_types ) . ')[^\]]*data="([^"]+)"/';

		if ( preg_match_all( $pattern, $content, $matches ) ) {
			$decoded_configs = [];

			foreach ( $matches[1] as $data ) {
				// Decode brackets and URL encoding
				$data = str_replace( [ '%91', '%93' ], [ '[', ']' ], $data );
				$data = urldecode( $data );

				$json = html_entity_decode( $data, ENT_QUOTES | ENT_HTML5 );

				// Fix trailing commas in JSON (legacy bug fix)
				$json = preg_replace( '/,\s*]/', ']', $json );
				$json = preg_replace( '/,\s*}/', '}', $json );

				$arr = json_decode( $json, true );

				if ( json_last_error() === JSON_ERROR_NONE && is_array( $arr ) ) {
					$decoded_configs[] = $arr;
				}
			}

			return $decoded_configs; // array of decoded config arrays
		}

		return false;
	}

	// --- Fluent Forms Migration ---
	private function migrate_fluentforms( $offset ) {
		global $wpdb;

		$batch_size = $this->batch_size;

		$table = "{$wpdb->prefix}fluentform_forms";

		// if not $table exists, return completed
		if ( ! $wpdb->get_var( "SHOW TABLES LIKE '$table'" ) ) {
			return [
				'completed' => true,
				'message'   => __( 'Fluent Forms migration complete. No forms found.', 'integrate-google-drive' )
			];
		}

		$forms = $wpdb->get_results(
			$wpdb->prepare(
				"SELECT id, form_fields FROM $table
			 WHERE form_fields LIKE %s
			 LIMIT %d OFFSET %d",
				'%integrate_google_drive%',
				$batch_size,
				$offset
			)
		);

		if ( ! $forms ) {
			return [
				'completed' => true,
				'message'   => __( 'Fluent Forms migration complete.', 'integrate-google-drive' )
			];
		}

		$processed = 0;

		foreach ( $forms as $form ) {

			$form_fields = json_decode( $form->form_fields, true );
			if ( ! is_array( $form_fields ) || empty( $form_fields['fields'] ) ) {
				continue;
			}

			$updated               = false;
			$form_fields['fields'] = $this->ff_field_migration( $form_fields['fields'], $form, $updated );

			if ( $updated ) {
				$new_fields = wp_json_encode( $form_fields );

				$wpdb->update(
					$wpdb->prefix . 'fluentform_forms',
					[ 'form_fields' => $new_fields ],
					[ 'id' => $form->id ]
				);
			}

			$processed ++;
		}

		$next_offset = $offset + $batch_size;

		return [
			'completed' => false,
			'step'      => 'Fluent Forms',
			'offset'    => $next_offset,
			'message'   => sprintf( __( 'Fluent Forms: Processed %d forms... (offset %d)', 'integrate-google-drive' ), $processed, $next_offset )
		];
	}

	private function ff_field_migration( $fields, $form, &$updated ) {

		$suffix  = 'Fluent Forms';
		$form_id = $form->id;

		foreach ( $fields as &$field ) {

			if ( isset( $field['element'] ) && $field['element'] === 'integrate_google_drive' ) {

				if ( ! empty( $field['settings']['igd_data'] ) ) {

					$config = json_decode( $field['settings']['igd_data'], true );

					if ( ! is_array( $config ) ) {
						continue;
					}

					// remove uniqueId and isFormUploader from config
					unset( $config['uniqueId'], $config['isFormUploader'] );

					$module_id = $this->create_module( $config, $suffix, $form_id );

					$field['attributes']['value'] = $module_id;

					$updated = true;
				}
			}
		}

		return $fields;
	}

	// ---  WPForms Migration ---
	private function migrate_wpforms( $offset ) {
		global $wpdb;

		$batch_size = $this->batch_size;

		$forms = $wpdb->get_results(
			$wpdb->prepare(
				"SELECT ID, post_content FROM {$wpdb->posts}
			 WHERE post_type = %s
			 AND post_status IN ('publish','draft','pending','future')
			 LIMIT %d OFFSET %d",
				'wpforms',
				$batch_size,
				$offset
			)
		);

		if ( ! $forms ) {
			return [
				'completed' => true,
				'message'   => __( 'WPForms migration complete.', 'integrate-google-drive' )
			];
		}

		$processed = 0;
		$suffix    = 'WPForms';

		foreach ( $forms as $form ) {
			$form_data = json_decode( $form->post_content, true );

			if ( ! is_array( $form_data ) || empty( $form_data['fields'] ) ) {
				continue;
			}

			$form_id = $form->ID;

			$updated = false;

			foreach ( $form_data['fields'] as &$field ) {
				if (
					isset( $field['type'] ) &&
					$field['type'] === 'igd-uploader' &&
					! empty( $field['data'] )
				) {
					$config = json_decode( $field['data'], true );

					if ( ! is_array( $config ) ) {
						continue;
					}

					// Remove metadata not needed in stored config
					unset( $config['uniqueId'], $config['isFormUploader'] );

					$module_id = $this->create_module( $config, $suffix, $form_id );

					$field['module_id'] = $module_id;

					$updated = true;
				}
			}

			if ( $updated ) {
				// Update the post content with the new module IDs
				$wpdb->query(
					$wpdb->prepare(
						"
                            UPDATE {$wpdb->posts}
                            SET post_content = %s
                            WHERE ID = %d
                           ",
						wp_json_encode( $form_data ),
						$form->ID
					)
				);
			}

			$processed ++;
		}

		$next_offset = $offset + $batch_size;

		return [
			'completed' => false,
			'step'      => 'WPForms',
			'offset'    => $next_offset,
			'message'   => sprintf( __( 'WPForms: Processed %d forms... (offset %d)', 'integrate-google-drive' ), $processed, $next_offset )
		];
	}

	// --- Migrate Gravity Forms ---
	private function migrate_gravityforms( $offset ) {
		global $wpdb;

		$batch_size = $this->batch_size;

		$table = "{$wpdb->prefix}gf_form_meta";

		// if not $table exists, return completed
		if ( ! $wpdb->get_var( "SHOW TABLES LIKE '$table'" ) ) {
			return [
				'completed' => true,
				'message'   => __( 'Gravity Forms migration complete. No forms found.', 'integrate-google-drive' )
			];
		}

		$forms = $wpdb->get_results(
			$wpdb->prepare(
				"SELECT form_id, display_meta FROM $table
			 LIMIT %d OFFSET %d",
				$batch_size,
				$offset
			)
		);

		if ( ! $forms ) {
			return [
				'completed' => true,
				'message'   => __( 'Gravity Forms migration complete.', 'integrate-google-drive' )
			];
		}

		$processed = 0;

		$suffix = 'GravityForms';

		foreach ( $forms as $form ) {
			$form_id = $form->form_id;

			$display_meta = json_decode( $form->display_meta, 1 );

			if ( ! is_array( $display_meta ) || empty( $display_meta['fields'] ) ) {
				continue;
			}

			$updated = false;

			foreach ( $display_meta['fields'] as &$field ) {

				if (
					isset( $field['type'] ) &&
					$field['type'] === 'integrate_google_drive' &&
					! empty( $field['igdData'] )
				) {
					$config = is_array( $field['igdData'] )
						? $field['igdData']
						: json_decode( $field['igdData'], true );

					if ( ! is_array( $config ) || empty( $config['type'] ) ) {
						continue;
					}

					unset( $config['uniqueId'], $config['isFormUploader'] );

					$module_id = $this->create_module( $config, $suffix, $form_id );

					$field['module_id'] = $module_id;

					unset( $field['igdData'] );

					$updated = true;
				}
			}

			if ( $updated ) {
				$wpdb->update(
					$wpdb->prefix . 'gf_form_meta',
					[ 'display_meta' => json_encode( $display_meta ) ],
					[ 'form_id' => $form->form_id ]
				);
			}

			$processed ++;

		}

		$next_offset = $offset + $batch_size;

		return [
			'completed' => false,
			'step'      => 'Gravity Forms',
			'offset'    => $next_offset,
			'message'   => sprintf( __( 'Gravity Forms: Processed %d forms... (offset %d)', 'integrate-google-drive' ), $processed, $next_offset )
		];
	}

	// --- Migrate Formidable Forms ---
	private function migrate_formidableforms( $offset ) {
		global $wpdb;

		$batch_size = $this->batch_size;

		$table = "{$wpdb->prefix}frm_fields";

		// if not $table exists, return completed
		if ( ! $wpdb->get_var( "SHOW TABLES LIKE '$table'" ) ) {
			return [
				'completed' => true,
				'message'   => __( 'Formidable Forms migration complete. No fields found.', 'integrate-google-drive' )
			];
		}

		$fields = $wpdb->get_results(
			$wpdb->prepare(
				"SELECT id, form_id, field_options FROM $table
			 WHERE type = %s
			 LIMIT %d OFFSET %d",
				'integrate-google-drive',
				$batch_size,
				$offset
			)
		);

		if ( ! $fields ) {
			return [
				'completed' => true,
				'message'   => __( 'Formidable Forms migration complete.', 'integrate-google-drive' )
			];
		}

		$processed = 0;

		$suffix = 'FormidableForms';

		foreach ( $fields as $field ) {
			$options = maybe_unserialize( $field->field_options );

			if ( ! is_array( $options ) || empty( $options['igd_data'] ) ) {
				continue;
			}

			$config = json_decode( $options['igd_data'], true );

			if ( ! is_array( $config ) ) {
				continue;
			}

			\FrmField::delete_form_transient( $field->form_id );

			unset( $config['uniqueId'], $config['isFormUploader'] );

			$module_id = $this->create_module( $config, $suffix, $field->form_id );

			$options['module_id'] = $module_id;

			$wpdb->update(
				$wpdb->prefix . 'frm_fields',
				[ 'field_options' => maybe_serialize( $options ) ],
				[ 'id' => $field->id ]
			);

			$processed ++;
		}

		$next_offset = $offset + $batch_size;

		return [
			'completed' => false,
			'step'      => 'Formidable Forms',
			'offset'    => $next_offset,
			'message'   => sprintf( __( 'Formidable Forms: Processed %d fields... (offset %d)', 'integrate-google-drive' ), $processed, $next_offset )
		];
	}

	// --- Migrate Ninja Forms ---
	private function migrate_ninjaforms( $offset ) {
		global $wpdb;

		$batch_size = $this->batch_size;
		$table      = $wpdb->prefix . 'nf3_field_meta';

		// if not $table exists, return completed
		if ( ! $wpdb->get_var( "SHOW TABLES LIKE '$table'" ) ) {
			return [
				'completed' => true,
				'message'   => __( 'Ninja Forms migration complete. No fields found.', 'integrate-google-drive' )
			];
		}

		$meta_rows = $wpdb->get_results(
			$wpdb->prepare(
				"SELECT id, parent_id, `key`, value, meta_key, meta_value
			 FROM {$table}
			 WHERE `key` = %s
			 LIMIT %d OFFSET %d",
				'igd_data',
				$batch_size,
				$offset
			)
		);

		if ( ! $meta_rows ) {
			return [
				'completed' => true,
				'message'   => __( 'Ninja Forms migration complete.', 'integrate-google-drive' )
			];
		}

		$processed = 0;

		$suffix = 'NinjaForms';

		foreach ( $meta_rows as $row ) {
			$raw_data = $row->value ?: $row->meta_value;

			$config = json_decode( $raw_data, true );

			if ( ! is_array( $config ) ) {
				continue;
			}

			// Get form id from fields table by parent_id
			$form_id = $wpdb->get_var(
				$wpdb->prepare(
					"SELECT parent_id FROM {$wpdb->prefix}nf3_fields WHERE id = %d",
					$row->parent_id
				)
			);

			\WPN_Helper::delete_nf_cache( $form_id );

			unset( $config['uniqueId'], $config['isFormUploader'] );

			$module_id = $this->create_module( $config, $suffix, $row->parent_id );

			$wpdb->update(
				$table,
				[
					'key'        => 'module_id',
					'value'      => $module_id,
					'meta_key'   => 'module_id',
					'meta_value' => $module_id,
				],
				[ 'id' => $row->id ]
			);

			$processed ++;
		}

		$next_offset = $offset + $batch_size;

		return [
			'completed' => false,
			'step'      => 'Ninja Forms',
			'offset'    => $next_offset,
			'message'   => sprintf( __( 'Ninja Forms: Processed %d forms... (offset %d)', 'integrate-google-drive' ), $processed, $next_offset )
		];
	}

	// --- Migrate Contact Form 7 ---
	private function migrate_cf7( $offset ) {
		global $wpdb;

		$batch_size = $this->batch_size;

		$forms = $wpdb->get_results(
			$wpdb->prepare(
				"SELECT pm.post_id, pm.meta_value
			 FROM {$wpdb->prefix}postmeta pm
			 INNER JOIN {$wpdb->prefix}posts p ON p.ID = pm.post_id
			 WHERE pm.meta_key = %s
			 AND p.post_type = %s
			 LIMIT %d OFFSET %d",
				'_form',
				'wpcf7_contact_form',
				$batch_size,
				$offset
			)
		);

		if ( ! $forms ) {
			return [
				'completed' => true,
				'message'   => __( 'Contact Form 7 migration complete.', 'integrate-google-drive' )
			];
		}

		$processed = 0;

		foreach ( $forms as $form ) {
			$form_content = $form->meta_value;
			$updated      = false;

			// Match both [google_drive ...] and [google_drive* ...]
			$pattern = '/\[(google_drive\*?)\s+([^\]]*?)\s+data:([^\s\]]+)(.*?)\]/';

			$new_content = preg_replace_callback( $pattern, function ( $matches ) use ( &$updated, $form ) {
				list( $full, $tag, $name, $data_encoded, $extra ) = $matches;

				$decoded = json_decode( base64_decode( $data_encoded ), true );

				if ( ! is_array( $decoded ) ) {
					return $full; // fallback to original
				}

				unset( $decoded['uniqueId'], $decoded['isFormUploader'] );

				$suffix    = 'Contact Forms 7';
				$module_id = $this->create_module( $decoded, $suffix, $form->post_id );

				$updated = true;

				// Rebuild tag without data, add module_id
				$rebuilt = sprintf( '[%s %s module_id:%d%s]', $tag, $name, $module_id, $extra );

				return $rebuilt;
			}, $form_content );

			if ( $updated && $new_content !== $form_content ) {

				// Update the post meta with the new content
				$wpdb->query(
					$wpdb->prepare(
						"
                                UPDATE {$wpdb->postmeta}
                                SET meta_value = %s
                                WHERE post_id = %d AND meta_key = '_form'
                                ",
						$new_content,
						$form->post_id
					)
				);

			}

			$processed ++;
		}

		$next_offset = $offset + $batch_size;

		return [
			'completed' => false,
			'step'      => 'Contact Form 7',
			'offset'    => $next_offset,
			'message'   => sprintf( __( 'Contact Form 7: Processed %d forms... (offset %d)', 'integrate-google-drive' ), $processed, $next_offset )
		];
	}

	// --- Migration state helpers ---
	private function save_state() {
		update_option( 'igd_migration_1_5_1_state', $this->state, false );
	}

	private function load_state() {
		$default = [
			'step'   => array_key_first( $this->steps ),
			'offset' => 0
		];

		$state = get_option( 'igd_migration_1_5_1_state', $default );

		// Defensive: If $state is not array, reset it.
		if ( ! is_array( $state ) || ! isset( $state['step'] ) || ! isset( $state['offset'] ) ) {
			$state = $default;
		}

		$this->state = $state;
	}

	private function delete_state() {
		delete_option( 'igd_migration_1_5_1_state' );
		delete_option( 'igd_migration_1_5_1_status' );
	}

	private function create_module( $config, $suffix = '', $object_id = '' ) {
		global $wpdb;

		$table = $wpdb->prefix . 'integrate_google_drive_shortcodes';

		$data = [
			'title'   => '',
			'status'  => 'on',
			'type'    => $config['type'],
			'user_id' => get_current_user_id(),
			'config'  => $config,
		];

		$data_format = [ '%s', '%s', '%s', '%d', '%s' ];


		// Insert
		$wpdb->insert( $table, $data, $data_format );
		$insert_id = $wpdb->insert_id;

		if ( $insert_id ) {
			$title = sprintf( 'Module(#%s) - %s(#%s) ', $insert_id, $suffix, $object_id );

			$wpdb->update(
				$table,
				[
					'title'  => $title,
					'config' => maybe_serialize( array_merge( $config, [
						'id'    => $insert_id,
						'title' => $title
					] ) ),
				],
				[ 'id' => $insert_id ],
				[ '%s', '%s' ],
				[ '%d' ]
			);
		}

		return $insert_id;

	}

	private function get_post_type_suffix( $post_type ) {
		$suffix = '';

		$post_type_object = get_post_type_object( $post_type );
		if ( $post_type_object ) {
			$suffix = $post_type_object->labels->singular_name;
		}

		return $suffix;
	}

	private function migrate_list_module( $config ) {

		if ( in_array( $config['type'], [ 'view', 'download' ] ) ) {
			if ( 'view' === $config['type'] ) {
				$config['download'] = false;
			}

			if ( 'download' === $config['type'] ) {
				$config['preview'] = false;
			}

			$config['type'] = 'list';
		}

		return $config;
	}

	private function migrate_usage_limits( $config ) {

		if ( empty( $config['enableDownloadLimits'] ) ) {
			return $config;
		}

		if ( in_array( $config['type'], [ 'browser', 'gallery', 'search', 'media', 'slider', 'download' ] ) ) {

			if ( ! empty( $config['downloadsPerDay'] ) ) {
				$config['downloadLimits'] = $config['downloadsPerDay'];
			}

			if ( ! empty( $config['zipDownloadsPerDay'] ) ) {
				$config['zipDownloadLimits'] = $config['zipDownloadsPerDay'];
			}

			if ( ! empty( $config['bandwidthPerDay'] ) ) {
				$config['bandwidthLimits'] = $config['bandwidthPerDay'];
			}
		}

		return $config;
	}

}
