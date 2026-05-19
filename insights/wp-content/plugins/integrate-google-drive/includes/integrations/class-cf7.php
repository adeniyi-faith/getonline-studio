<?php

namespace IGD;

defined( 'ABSPATH' ) || exit;

class CF7 {
	/**
	 * @var null
	 */
	protected static $instance = null;

	public function __construct() {

		add_action( 'wpcf7_admin_init', [ $this, 'add_tag_generator' ], 99 );
		add_action( 'wpcf7_init', [ $this, 'add_data_handler' ] );

		// Validate the field
		add_filter( 'wpcf7_validate_google_drive', [ $this, 'validate_field' ], 10, 2 );
		add_filter( 'wpcf7_validate_google_drive*', [ $this, 'validate_field' ], 10, 2 );

		// Create entry folder
		add_action( 'wpcf7_before_send_mail', [ $this, 'may_create_entry_folder' ] );

	}

	public function may_create_entry_folder( $contact_form ) {
		$submission = \WPCF7_Submission::get_instance();

		if ( $submission ) {
			$posted_data = $submission->get_posted_data();

			$igd_fields = [];
			foreach ( $posted_data as $key => $value ) {
				// Skip the special mail tags (e.g., _wpcf7, _wpcf7_version, etc.)
				if ( strpos( $key, '_wpcf7' ) === 0 ) {
					continue;
				}

				// Check if the field is an IGD field
				if ( strpos( $key, 'google_drive-' ) === 0 ) {
					$igd_fields[ $key ] = $value;
				}
			}

			if ( ! empty( $igd_fields ) ) {
				foreach ( $igd_fields as $key => $value ) {

					if ( empty( $value ) ) {
						continue;
					}

					$files = [];

					// Fetch file ids from the value text
					preg_match_all( '/file\/d\/(.*?)\/view/', $value, $matches );

					$file_ids = $matches[1];

					if ( empty( $file_ids ) ) {
						continue;
					}

					foreach ( $file_ids as $file_id ) {
						$files[] = App::instance()->get_file_by_id( $file_id );
					}

					if ( empty( $files ) ) {
						continue;
					}

					$option = $contact_form->scan_form_tags( [ 'name' => $key ] )[0]['options'][0];

					$module_id   = str_replace( 'module_id:', '', $option );
					$module_data = Shortcode::instance()->get_shortcode( $module_id )['config'] ?? [];

					$tag_data = [
						'form' => [
							'form_title' => $contact_form->title(),
							'form_id'    => $contact_form->id(),
						]
					];

					$upload_folder = ! empty( $module_data['folders'] ) && is_array( $module_data['folders'] ) ? reset( $module_data['folders'] )
						: [
							'id'        => 'root',
							'accountId' => '',
						];

					// Rename files
					$file_name_template = ! empty( $module_data['uploadFileName'] ) ? $module_data['uploadFileName'] : '%file_name%%file_extension%';

					// Check if the file name template contains dynamic tags
					if ( igd_contains_tags( 'field', $file_name_template ) ) {

						// Get dynamic tags by filtering the form data
						$extra_tags = $this->handle_form_field_tags( $file_name_template, $posted_data );

						$rename_files = [];
						foreach ( $files as $file ) {
							// We will rename the file name
							$tag_data['name'] = $file['name'];

							$name = igd_replace_template_tags( $tag_data, $extra_tags );

							$rename_files[] = [
								'id'   => $file['id'],
								'name' => $name,
							];
						}

						if ( ! empty( $rename_files ) ) {
							App::instance( $upload_folder['accountId'] )->rename_files( $rename_files );
						}

					}

					// Create Entry Folder
					$create_entry_folder   = ! empty( $module_data['createEntryFolders'] );
					$create_private_folder = ! empty( $module_data['createPrivateFolder'] );

					if ( ! $create_entry_folder && ! $create_private_folder ) {
						continue;
					}

					$entry_folder_name_template = ! empty( $module_data['entryFolderNameTemplate'] ) ? $module_data['entryFolderNameTemplate'] : 'Form Entry - %form_title%';

					$user_id = ! empty( $_POST['_user_id'] ) ? intval( $_POST['_user_id'] ) : null;
					if ( igd_contains_tags( 'user', $entry_folder_name_template ) ) {

						if ( $user_id ) {
							$tag_data['user'] = get_userdata( $user_id );
						}
					}

					if ( igd_contains_tags( 'post', $entry_folder_name_template ) ) {
						$referrer = wp_get_referer();

						if ( ! empty( $referrer ) ) {
							$post_id = url_to_postid( $referrer );
							if ( ! empty( $post_id ) ) {
								$tag_data['post'] = get_post( $post_id );
								if ( $tag_data['post']->post_type == 'product' ) {
									$tag_data['wc_product'] = wc_get_product( $post_id );
								}
							}
						}
					}

					// Dynamic tags
					$extra_tags = [];
					if ( igd_contains_tags( 'field', $entry_folder_name_template ) ) {
						$extra_tags = $this->handle_form_field_tags( $entry_folder_name_template, $posted_data );
					}

					$tag_data['name'] = $entry_folder_name_template;
					$folder_name      = igd_replace_template_tags( $tag_data, $extra_tags );

					// Check Private Folders
					$private_folders = ! empty( $module_data['privateFolders'] );
					if ( $private_folders && $user_id ) {
						$folders = get_user_meta( $user_id, 'igd_folders', true );

						if ( ! empty( $folders ) ) {
							$folders = array_values( array_filter( (array) $folders, function ( $item ) {
								return igd_is_dir( $item );
							} ) );
						} elseif ( $create_private_folder ) {
							$folders = Private_Folders::instance()->create_user_folder( $user_id, $module_data );
						}

						if ( ! empty( $folders ) ) {
							$module_data['folders'] = $folders;
						}

					}

					$merge_folders = isset( $module_data['mergeFolders'] ) ? filter_var( $module_data['mergeFolders'], FILTER_VALIDATE_BOOLEAN ) : false;

					Uploader::instance( $upload_folder['accountId'] )->create_entry_folder_and_move( $files, $folder_name, $upload_folder, $merge_folders, $create_entry_folder );
				}

			}
		}
	}

	private function handle_form_field_tags( $name_template, $formData ) {
		$extra_tags = [];

		// get %field_{key}% from the file name template
		preg_match_all( '/%field_([^%]+)%/', $name_template, $matches );
		$field_keys = $matches[1];

		if ( ! empty( $field_keys ) ) {
			foreach ( $formData as $field_key => $field_value ) {
				if ( ! in_array( $field_key, $field_keys ) ) {
					continue;
				}

				// Handle array values, such as checkboxes
				if ( is_array( $field_value ) ) {
					$field_value = implode( ', ', $field_value );
				}

				$extra_tags[ '%field_' . $field_key . '%' ] = $field_value;
			}

		}

		return $extra_tags;
	}

	public function validate_field( $result, $tag ) {

		// Get the submitted form data
		$submission = \WPCF7_Submission::get_instance();

		// Check if the submission exists
		if ( $submission ) {
			// Get the value of your custom field
			$value = $submission->get_posted_data( $tag->name );

			// Perform validation (for example, checking if it's required and empty)
			$is_required = ( '*' == substr( $tag->type, - 1 ) );
			if ( $is_required && empty( $value ) ) {
				// Set an error for the field if it doesn't meet the requirements
				$result->invalidate( $tag, __( 'This field is required.', 'integrate-google-drive' ) );
			}

			// Min File Uploads
			$option = $tag->options[0];

			$module_id   = str_replace( 'module_id:', '', $option );
			$module_data = Shortcode::instance()->get_shortcode( $module_id )['config'] ?? [];

			$min_file_uploads = ! empty( $module_data['minFiles'] ) ? $module_data['minFiles'] : 0;

			if ( $min_file_uploads > 0 ) {
				$files = explode( ' ),', $value );

				if ( empty( $files ) || count( $files ) < $min_file_uploads ) {
					/* translators: %d: minimum file uploads */
					$message = sprintf( __( 'Please upload at least %d files.', 'integrate-google-drive' ), $min_file_uploads );

					$result->invalidate( $tag, $message );
				}
			}


		}

		// Return the result object after validation
		return $result;
	}

	/**
	 * Add shortcode handler to CF7.
	 */
	public function add_data_handler() {
		if ( function_exists( 'wpcf7_add_form_tag' ) ) {
			wpcf7_add_form_tag( [ 'google_drive', 'google_drive*' ], [ $this, 'data_handler' ], true );
		}
	}

	public function data_handler( $tag ) {
		$tag = new \WPCF7_FormTag( $tag );

		if ( empty( $tag->name ) ) {
			return '';
		}

		// Validate our fields
		$validation_error = wpcf7_get_validation_error( $tag->name );

		$class = wpcf7_form_controls_class( $tag->type, 'upload-file-list igd-hidden' );

		if ( $validation_error ) {
			$class .= ' wpcf7-not-valid';
		}

		// Data

		$default_data = [
			'type'           => 'uploader',
			'isFormUploader' => 'cf7',
			'isRequired'     => $tag->is_required(),
		];

		$module_id = $tag->get_option( 'module_id', '', true );

		$module_data = Shortcode::instance()->get_shortcode( $module_id )['config'] ?? [];

		$data = wp_parse_args( $module_data, $default_data );

		$atts = [
			'name'          => $tag->name,
			'class'         => $class,
			'tabindex'      => $tag->get_option( 'tabindex', 'signed_int', true ),
			'aria-invalid'  => $validation_error ? 'true' : 'false',
			'aria-required' => $tag->is_required() ? 'true' : 'false',
		];

		$atts = wpcf7_format_atts( $atts );

		$return = '<div class="wpcf7-form-control-wrap" data-name="' . esc_attr( $tag->name ) . '">';
		$return .= Shortcode::instance()->render_shortcode( [], $data );
		$return .= "<input " . $atts . " />";
		$return .= "<input type='hidden' name='_user_id' value='" . get_current_user_id() . "' />";
		$return .= $validation_error;
		$return .= '</div>';

		return $return;
	}

	public function add_tag_generator() {
		if ( class_exists( 'WPCF7_TagGenerator' ) ) {
			$tag_generator = \WPCF7_TagGenerator::get_instance();

			$tag_generator->add(
				'google_drive',
				__( 'Google Drive Upload', 'integrate-google-drive' ),
				[
					$this,
					version_compare( WPCF7_VERSION, '6.0', '>=' ) ? 'tag_generator_body_v6' : 'tag_generator_body',
				],
				[
					'version' => '2',
				]
			);
		}
	}

	public function tag_generator_body_v6( $contact_form, $options = '' ) {
		$tgg = new \WPCF7_TagGeneratorGenerator( $options['content'] );

		$description = esc_html__( 'Generate a form-tag for this upload field.', 'integrate-google-drive' );

		?>
        <header class="description-box">
            <h3><?php echo esc_html__( 'Google Drive Upload', 'integrate-google-drive' ); ?></h3>

            <p>
				<?php
				$description = wp_kses(
					$description,
					array(
						'a'      => array( 'href' => true ),
						'strong' => array(),
					),
					array( 'http', 'https' )
				);

				echo $description;
				?>
            </p>
        </header>

        <div class="control-box">
			<?php

			$tgg->print( 'field_type', array(
				'with_required'  => true,
				'select_options' => array(
					'google_drive' => esc_html__( 'Google Drive Upload', 'integrate-google-drive' ),
				),
			) );

			$tgg->print( 'field_name' );


			?>

            <fieldset>
                <legend><?php echo esc_html__( 'Select Module', 'integrate-google-drive' ); ?></legend>

				<?php echo $this->get_select_field( $options['content'] . '-data' ); ?>

                <p class="description"><?php esc_html_e( 'Select an existing module or create a new one.', 'integrate-google-drive' ); ?></p>

                <button id="igd-form-uploader-config-cf7" type="button"
                        class="igd-form-uploader-trigger igd-form-uploader-trigger-cf7 igd-btn btn-primary">
                    <i class="dashicons dashicons-admin-generic"></i>
                    <span><?php esc_html_e( 'Configure', 'integrate-google-drive' ); ?></span>
                </button>

            </fieldset>

			<?php
			?>
        </div>

        <footer class="insert-box">
			<?php
			$tgg->print( 'insert_box_content' );

			$tgg->print( 'mail_tag_tip' );
			?>
        </footer>


		<?php

	}

	public function tag_generator_body( $contact_form, $args = '' ) {
		$args = wp_parse_args( $args, [] );
		$type = 'google_drive';

		$description = esc_html__( 'Generate a form-tag for this upload field.', 'integrate-google-drive' );
		?>
        <div class="control-box">
            <fieldset>
                <legend><?php echo esc_html( $description ); ?></legend>
                <table class="form-table">
                    <tbody>
                    <tr>
                        <th scope="row"><?php echo esc_html__( 'Field type', 'integrate-google-drive' ); ?></th>
                        <td>
                            <fieldset>
                                <legend
                                        class="screen-reader-text"><?php echo esc_html__( 'Field type', 'integrate-google-drive' ); ?></legend>
                                <label>
                                    <input type="checkbox"
                                           name="required"/> <?php echo esc_html__( 'Required field', 'integrate-google-drive' ); ?>
                                </label>
                            </fieldset>
                        </td>
                    </tr>

                    <tr>
                        <th scope="row">
                            <label for="<?php echo esc_attr( $args['content'] . '-name' ); ?>">
								<?php echo esc_html__( 'Name', 'integrate-google-drive' ); ?>
                            </label>
                        </th>
                        <td>
                            <input type="text" name="name" class="tg-name oneline"
                                   id="<?php echo esc_attr( $args['content'] . '-name' ); ?>"/>
                        </td>
                    </tr>

                    <tr>
                        <th scope="row">
                            <label for="<?php echo esc_attr( $args['content'] . '-data' ); ?>">
								<?php echo esc_html__( 'Module ID', 'integrate-google-drive' ); ?>
                            </label>
                        </th>
                        <td>

							<?php echo $this->get_select_field( $args['content'] . '-data' ); ?>

                            <button id="igd-form-uploader-config-cf7" type="button"
                                    class="igd-form-uploader-trigger igd-form-uploader-trigger-cf7 igd-btn btn-primary">
                                <i class="dashicons dashicons-admin-generic"></i>
                                <span><?php esc_html_e( 'Configure', 'integrate-google-drive' ); ?></span>
                            </button>
                        </td>
                    </tr>

                    </tbody>
                </table>
            </fieldset>
        </div>

        <div class="insert-box">
            <input type="text" name="<?php echo esc_attr( $type ); ?>" class="tag code" readonly="readonly"
                   onfocus="this.select()"/>

            <div class="submitbox">
                <input type="button" class="button button-primary insert-tag"
                       value="<?php echo esc_attr__( 'Insert Tag', 'integrate-google-drive' ); ?>"/>
            </div>

            <br class="clear"/>

            <p class="description mail-tag">
                <label for="<?php echo esc_attr( $args['content'] . '-mailtag' ); ?>">
					<?php printf( 'To list the uploads in your email, insert the mail-tag (%s) in the Mail tab.', '<strong><span class="mail-tag"></span></strong>' ); ?>
                    <input type="text" class="mail-tag code igd-hidden" readonly="readonly"
                           id="<?php echo esc_attr( $args['content'] . '-mailtag' ); ?>"/>
                </label>
            </p>
        </div>
		<?php
	}

	public function get_select_field( $id ) {
		$shortcodes = Shortcode::get_shortcodes();

		$shortcodes = array_filter( $shortcodes, function ( $shortcode ) {
			return 'browser' === $shortcode['type'] || 'uploader' === $shortcode['type'];
		} );

		$options = "<option value=''>" . esc_html__( 'Select Module', 'integrate-google-drive' ) . "</option>";

		if ( ! empty( $shortcodes ) ) {
			foreach ( $shortcodes as $shortcode ) {
				$options .= sprintf(
					"<option value='%s'>%s</option>",
					esc_attr( $shortcode['id'] ),
					esc_html( $shortcode['title'] )
				);
			}
		}

		return sprintf(
			'<select data-tag-part="option" data-tag-option="module_id:" name="module_id" class="option oneline" id="%s">%s</select>',
			$id,
			$options
		);

	}

	/**
	 * @return CF7|null
	 */
	public static function instance() {
		if ( is_null( self::$instance ) ) {
			self::$instance = new self();
		}

		return self::$instance;
	}

}

CF7::instance();