<?php

namespace IGD;

defined( 'ABSPATH' ) || exit();

class Update_1_4_9 {
	private static $instance;

	public function __construct() {
		$this->create_thumbnails_folder();
	}

	private static function create_thumbnails_folder() {
		if ( ! file_exists( IGD_CACHE_DIR ) ) {
			wp_mkdir_p( IGD_CACHE_DIR );
		}
	}

	public static function instance() {
		if ( null == self::$instance ) {
			self::$instance = new self;
		}

		return self::$instance;
	}

}

Update_1_4_9::instance();
