<?php

namespace IGD;

defined( 'ABSPATH' ) || exit();

class Update_1_4_8 {
	private static $instance;

	public function __construct() {
		$this->create_cache_folder();
	}

	private static function create_cache_folder() {

		if ( ! file_exists( IGD_CACHE_DIR ) ) {
			@mkdir( IGD_CACHE_DIR, 0755 );
		}

		if ( ! is_writable( IGD_CACHE_DIR ) ) {
			@chmod( IGD_CACHE_DIR, 0755 );
		}
	}

	public static function instance() {
		if ( null == self::$instance ) {
			self::$instance = new self;
		}

		return self::$instance;
	}

}

Update_1_4_8::instance();
