<?php

namespace IGD;

defined( 'ABSPATH' ) || exit();

class Update_1_5_2 {
	private static $instance;

	private function __construct() {
		$this->add_migration_setting_flag();
	}

	public function add_migration_setting_flag() {
		$settings = get_option( 'igd_settings', [] );

		$settings['shouldMigrate'] = true;

		update_option( 'igd_settings', $settings );
	}

	/**
	 * Gets the singleton instance of the class.
	 *
	 * @return Update_1_5_2
	 */
	public static function instance() {
		if ( self::$instance === null ) {
			self::$instance = new self();
		}

		return self::$instance;
	}
}

Update_1_5_2::instance();
