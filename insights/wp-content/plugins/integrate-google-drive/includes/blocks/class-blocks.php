<?php

namespace IGD;

defined( 'ABSPATH' ) || exit();

class Blocks {

	/**
	 * @var null
	 */
	protected static $instance = null;

	public function __construct() {
		add_filter( 'block_categories_all', [ $this, 'filter_block_categories' ], 10, 2 );
		add_action( 'init', [ $this, 'register_block' ] );
	}

	public function register_block() {
		register_block_type( IGD_INCLUDES . '/blocks/build/shortcodes', [
			'render_callback' => [ $this, 'render_module_shortcode_block' ],
		] );
	}

	public function render_module_shortcode_block( $attributes, $content ) {
		$id = ! empty( $attributes['id'] ) ? $attributes['id'] : '';

		return Shortcode::instance()->render_shortcode( [ 'id' => $id ] );
	}

	function filter_block_categories( $block_categories, $editor_context ) {
		if ( ! empty( $editor_context->post ) ) {
			$new_categories = [
				[
					'slug'  => 'igd-category',
					'title' => __( 'Integrate Google Drive', 'integrate-google-drive' ),
					'icon'  => null,
				]
			];

			$block_categories = array_merge( $block_categories, $new_categories );
		}

		return $block_categories;
	}

	/**
	 * @return Blocks|null
	 */
	public static function instance() {
		if ( is_null( self::$instance ) ) {
			self::$instance = new self();
		}

		return self::$instance;
	}

}

Blocks::instance();


