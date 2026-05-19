<?php
/**
 * Assets
 *
 * All enqueues of scripts and styles.
 *
 * @package Overflow
 */

if ( ! function_exists( 'csco_content_width' ) ) {
	/**
	 * Set the content width in pixels, based on the theme's design and stylesheet.
	 *
	 * Priority 0 to make it available to lower priority callbacks.
	 *
	 * @global int $content_width
	 */
	function csco_content_width() {
		$GLOBALS['content_width'] = apply_filters( 'csco_content_width', 1200 );
	}
}
add_action( 'after_setup_theme', 'csco_content_width', 0 );

if ( ! function_exists( 'csco_editor_style' ) ) {
	/**
	 * Add callback for custom editor stylesheets.
	 */
	function csco_editor_style() {
		// Add support for editor styles.
		add_theme_support( 'editor-styles' );

		// Add support custom fonts for editor styles.
		add_editor_style( '/css/custom-fonts.css' );

		// Enqueue editor styles.
		if ( is_rtl() ) {
			add_editor_style( csco_style( '/css/editor-style-rtl.css' ) );
		} else {
			add_editor_style( csco_style( '/css/editor-style.css' ) );
		}
	}
}
add_action( 'current_screen', 'csco_editor_style' );

if ( ! function_exists( 'csco_enqueue_scripts' ) ) {
	/**
	 * Enqueue scripts and styles.
	 */
	function csco_enqueue_scripts() {

		$version = csco_get_theme_data( 'Version' );

		// Register vendor scripts.
		wp_register_script( 'colcade', get_template_directory_uri() . '/js/colcade.js', array( 'jquery' ), '0.2.0', true );
		wp_register_script( 'object-fit-images', get_template_directory_uri() . '/js/ofi.min.js', array(), '3.2.3', true );

		// Register theme scripts.
		wp_register_script( 'csco-scripts', get_template_directory_uri() . '/js/scripts.js', array(
			'jquery',
			'imagesloaded',
			'colcade',
			'object-fit-images',
		), $version, true );

		// Enqueue theme scripts.
		wp_enqueue_script( 'csco-scripts' );

		// Enqueue comment reply script.
		if ( is_singular() && comments_open() && get_option( 'thread_comments' ) ) {
			wp_enqueue_script( 'comment-reply' );
		}

		// Register theme styles.
		wp_register_style( 'csco-styles', csco_style( get_template_directory_uri() . '/style.css' ), array(), $version );

		// Enqueue theme styles.
		wp_enqueue_style( 'csco-styles' );

		// Add RTL support.
		wp_style_add_data( 'csco-styles', 'rtl', 'replace' );

		// Dequeue Contact Form 7 styles.
		wp_dequeue_style( 'contact-form-7' );

	}
}
add_action( 'wp_enqueue_scripts', 'csco_enqueue_scripts' );
