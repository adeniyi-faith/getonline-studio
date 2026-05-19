<?php

namespace IGD;

defined('ABSPATH') || exit();

class Update_1_5_0 {
	private static $instance;

	private function __construct() {
		$this->add_statistics_columns();
	}

	/**
	 * Adds new columns to the integrate_google_drive_logs table if they don't exist.
	 */
	private function add_statistics_columns() {
		global $wpdb;

		$table_name = $wpdb->prefix . 'integrate_google_drive_logs';

		// Check and add the 'shortcode_id' column if it doesn't exist
		if (!$this->column_exists($table_name, 'shortcode_id')) {
			$wpdb->query("ALTER TABLE $table_name ADD COLUMN `shortcode_id` INT(11) NULL AFTER `id`");
		}

		// Check and add the 'page' column if it doesn't exist
		if (!$this->column_exists($table_name, 'page')) {
			$wpdb->query("ALTER TABLE $table_name ADD COLUMN `page` TEXT NOT NULL AFTER `shortcode_id`");
		}
	}

	/**
	 * Checks if a column exists in the given table.
	 *
	 * @param string $table_name The table name.
	 * @param string $column_name The column name to check.
	 * @return bool True if the column exists, false otherwise.
	 */
	private function column_exists($table_name, $column_name) {
		global $wpdb;

		$query = $wpdb->prepare(
			"SHOW COLUMNS FROM `$table_name` LIKE %s",
			$column_name
		);

		return (bool) $wpdb->get_var($query);
	}

	/**
	 * Gets the singleton instance of the class.
	 *
	 * @return Update_1_5_0
	 */
	public static function instance() {
		if (self::$instance === null) {
			self::$instance = new self();
		}

		return self::$instance;
	}
}

Update_1_5_0::instance();
