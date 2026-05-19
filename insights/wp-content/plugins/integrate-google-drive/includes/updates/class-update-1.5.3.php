<?php

namespace IGD;

defined( 'ABSPATH' ) || exit();

class Update_1_5_3 {
	private static $instance;

	private function __construct() {
		$this->maybe_create_selections_table();

		$this->maybe_alter_columns();

		$this->update_woocommerce_uploader_settings();
	}

	public function maybe_create_selections_table() {
		global $wpdb;

		$table_name = $wpdb->prefix . 'integrate_google_drive_selections';

		// Check if table exists
		if ( $wpdb->get_var( $wpdb->prepare( "SHOW TABLES LIKE %s", $table_name ) ) === $table_name ) {
			return; // Table already exists
		}

		$charset_collate = $wpdb->get_charset_collate();

		$sql = "CREATE TABLE $table_name (
        id INT UNSIGNED NOT NULL AUTO_INCREMENT,
        shortcode_id INT UNSIGNED DEFAULT NULL,
        user_id INT UNSIGNED DEFAULT NULL,
        email VARCHAR(255) NOT NULL,
        files TEXT DEFAULT NULL,
        message MEDIUMTEXT DEFAULT NULL,
        page TEXT DEFAULT NULL,
        created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
        updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        PRIMARY KEY (id)
    ) $charset_collate;";

		require_once ABSPATH . 'wp-admin/includes/upgrade.php';
		dbDelta( $sql );
	}

	public function maybe_alter_columns() {
		global $wpdb;

		$table_name = $wpdb->prefix . 'integrate_google_drive_shortcodes';

		// Add type and user_id columns to shortcodes table
		$columns_to_add = [
			'type'    => [ 'type' => 'VARCHAR(255)', 'after' => 'status' ],
			'user_id' => [ 'type' => 'BIGINT(20)', 'after' => 'type' ],
		];

		foreach ( $columns_to_add as $column_name => $column_props ) {
			$column_exists = $wpdb->get_var(
				$wpdb->prepare(
					"SHOW COLUMNS FROM `$table_name` LIKE %s",
					$column_name
				)
			);

			if ( $column_exists === null ) {
				$wpdb->query(
					"ALTER TABLE `$table_name` ADD `$column_name` {$column_props['type']} AFTER `{$column_props['after']}`"
				);
			}
		}

		// Add page column to logs table
		$logs_table_name = $wpdb->prefix . 'integrate_google_drive_logs';

		$logs_columns_to_add = [
			'shortcode_id' => [ 'type' => 'INT UNSIGNED DEFAULT NULL', 'after' => 'id' ],
			'page'         => [ 'type' => 'TEXT NULL', 'after' => 'file_id' ],
		];

		foreach ( $logs_columns_to_add as $column_name => $column_props ) {
			$column_exists = $wpdb->get_var(
				$wpdb->prepare(
					"SHOW COLUMNS FROM `$logs_table_name` LIKE %s",
					$column_name
				)
			);

			if ( $column_exists === null ) {
				$wpdb->query(
					"ALTER TABLE `$logs_table_name` ADD `$column_name` {$column_props['type']} AFTER `{$column_props['after']}`"
				);
			}
		}

	}

	public function update_woocommerce_uploader_settings() {
		global $wpdb;

		$posts = $wpdb->get_results(
			$wpdb->prepare(
				"SELECT p.ID
             FROM {$wpdb->posts} p
             INNER JOIN {$wpdb->postmeta} m ON p.ID = m.post_id
             WHERE p.post_type = 'product'
             AND p.post_status IN ('publish','draft','pending','future')
             AND m.meta_key = %s",
				'_igd_upload',
			)
		);

		foreach ( $posts as $post ) {
			$post_id = $post->ID;

			$igd_upload = get_post_meta( $post_id, '_igd_upload', true );

			if ( 'yes' !== $igd_upload ) {
				continue;
			}

			$upload_button_text = get_post_meta( $post_id, '_igd_upload_button_text', true );
			$description        = get_post_meta( $post_id, '_igd_upload_description', true );

			$max_upload_files = get_post_meta( $post_id, '_igd_upload_max_files', true );

			$min_upload_files = get_post_meta( $post_id, '_igd_upload_min_files', true );

			$max_file_size   = get_post_meta( $post_id, '_igd_upload_max_file_size', true );
			$min_file_size   = get_post_meta( $post_id, '_igd_upload_min_file_size', true );
			$file_extensions = get_post_meta( $post_id, '_igd_upload_file_types', true );

			$upload_description = igd_get_settings( 'wcUploadDescription' );

			$enable_folder_selection = igd_get_settings( 'wcUploadFolderSelection' );
			$upload_folders          = igd_get_settings( 'wcUploadFolders', [] );
			$default_upload_folder   = igd_get_settings( 'wcUploadDefaultFolder', [] );
			$upload_folder_name      = igd_get_settings( 'wooCommerceUploadFolderNameTemplate', 'Order - %wc_order_id% - %wc_product_name% (%user_email%)' );
			$upload_file_name        = igd_get_settings( 'wcUploadFileName', '%file_name%%file_extension%' );

			if ( $enable_folder_selection ) {
				if ( ! empty( $default_upload_folder ) ) {
					$upload_folder = $default_upload_folder;
				} elseif ( ! empty( $upload_folders ) ) {
					$upload_folder = $upload_folders[0];
				}
			} else {
				$parent_folder = igd_get_settings( 'wooCommerceUploadParentFolder', [] );

				if ( class_exists( 'WeDevs_Dokan' ) ) {
					$vendor_id     = get_post_field( 'post_author', $post_id );
					$parent_folder = get_user_meta( $vendor_id, '_igd_dokan_upload_parent_folder', true );

					$upload_description = get_user_meta( $vendor_id, '_igd_upload_file_description', true );
					$upload_folder_name = get_user_meta( $vendor_id, '_igd_dokan_upload_folder_name', true );
					$upload_folder_name = ! empty( $upload_folder_name ) ? $upload_folder_name : 'Order - %wc_order_id% - %wc_product_name% (%user_email%)';
				}

				$upload_folder = $parent_folder;
			}

			if ( empty( $upload_folder ) ) {
				$upload_folder = [
					'id'        => 'root',
					'accountId' => '',
				];
			}

			$module_data = [
				'type'                    => 'uploader',
				'uploadBtnText'           => $upload_button_text,
				'uploadBoxDescription'    => $description,
				'maxFileSize'             => $max_file_size,
				'minFileSize'             => $min_file_size,
				'maxFiles'                => $max_upload_files,
				'minFiles'                => $min_upload_files,
				'folders'                 => [ $upload_folder ],
				'enableUploadDescription' => $upload_description,
				'folderNameTemplate'      => $upload_folder_name,
				'uploadFileName'          => $upload_file_name,
			];

			if ( ! empty( $file_extensions ) ) {
				$module_data['allowExtensions'] = trim( $file_extensions, ',' );
			}

			update_post_meta( $post_id, '_igd_module_data', base64_encode( json_encode( $module_data ) ) );

		}
	}


	/**
	 * Gets the singleton instance of the class.
	 *
	 * @return Update_1_5_3
	 */
	public static function instance() {
		if ( self::$instance === null ) {
			self::$instance = new self();
		}

		return self::$instance;
	}
}

Update_1_5_3::instance();
