<?php

namespace IGD;

defined( 'ABSPATH' ) || exit();

class Update_1_5_1 {

	private static $instance;

	private function __construct() {
		$this->add_columns();
		$this->add_needs_migration_flag();
		$this->migrate_type();
		$this->flush_rewrite_rules();
		$this->migrate_restriction_settings();
	}

	private function migrate_restriction_settings() {

		$settings = igd_get_settings();

		if ( empty( $settings['enableDownloadLimits'] ) ) {
			return;
		}

		if ( ! empty( $settings['downloadsPerDay'] ) ) {
			$settings['downloadLimits'] = $settings['downloadsPerDay'];
		}

		if ( ! empty( $settings['zipDownloadsPerDay'] ) ) {
			$settings['zipDownloadLimits'] = $settings['zipDownloadsPerDay'];
		}

		if ( ! empty( $settings['bandwidthPerDay'] ) ) {
			$settings['bandwidthLimits'] = $settings['bandwidthPerDay'];
		}

	}

	private function add_needs_migration_flag() {

		if ( ! wp_next_scheduled( 'igd_migration_background_process' ) ) {
			update_option( 'igd_migration_1_5_1_status', 'run' );
			wp_schedule_single_event( time() , 'igd_migration_background_process' );
		}

	}

	public static function flush_rewrite_rules() {
		add_rewrite_rule( '^igd-modules/([0-9]+)/?$', 'index.php?igd-modules=$matches[1]', 'top' );
		flush_rewrite_rules();
	}

	public function add_columns() {
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

	/**
	 * Migrate view, and download modules to the list module.
	 */
	private function migrate_type() {
		global $wpdb;

		$table_name = $wpdb->prefix . 'integrate_google_drive_shortcodes';

		$shortcodes = $wpdb->get_results( "SELECT * FROM `$table_name`" );

		if ( empty( $shortcodes ) ) {
			return;
		}

		foreach ( $shortcodes as $shortcode ) {
			$config = maybe_unserialize( $shortcode->config );

			$type = $config['type'] ?? '';

			if ( in_array( $type, [ 'view', 'download' ] ) ) {

				if ( 'view' === $type ) {
					$config['download'] = false;
				} elseif ( 'download' === $type ) {
					$config['preview'] = false;
				}

				$type           = 'list';
				$config['type'] = $type;
			}

			$wpdb->update(
				$table_name,
				[
					'config' => maybe_serialize( $config ),
					'type'   => $type,
				],
				[ 'id' => $shortcode->id ]
			);

		}
	}

	/**
	 * Gets the singleton instance of the class.
	 *
	 * @return Update_1_5_1
	 */
	public static function instance() {
		if ( self::$instance === null ) {
			self::$instance = new self();
		}

		return self::$instance;
	}

}

Update_1_5_1::instance();
