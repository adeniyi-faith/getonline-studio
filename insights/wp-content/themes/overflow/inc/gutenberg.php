<?php
/**
 * Gutenberg compatibility functions.
 *
 * @package Overflow
 */

/**
 * Enqueue editor scripts
 */
function csco_block_editor_scripts() {
	wp_enqueue_script(
		'csco-editor-scripts',
		get_template_directory_uri() . '/js/editor-scripts.js',
		array(
			'wp-editor',
			'wp-element',
			'wp-compose',
			'wp-data',
			'wp-plugins',
		),
		csco_get_theme_data( 'Version' ),
		true
	);
}
add_action( 'enqueue_block_editor_assets', 'csco_block_editor_scripts' );

/**
 * Adds classes to <div class="editor-styles-wrapper"> tag
 */
function csco_block_editor_wrapper() {
	$post_id = get_the_ID();

	if ( ! $post_id ) {
		return;
	}

	// Set post type.
	$post_type = sprintf( 'post-type-%s', get_post_type( $post_id ) );

	// Set page layout.
	$default_layout = csco_get_page_sidebar( $post_id, 'default' );
	$page_layout    = csco_get_page_sidebar( $post_id );

	if ( 'disabled' === $default_layout ) {
		$default_layout = 'sidebar-disabled';
	} else {
		$default_layout = 'sidebar-enabled';
	}

	if ( 'disabled' === $page_layout ) {
		$page_layout = 'sidebar-disabled';
	} else {
		$page_layout = 'sidebar-enabled';
	}

	// Post Sidebar.
	if ( 'post' === get_post_type( $post_id ) && csco_powerkit_module_enabled( 'share_buttons' ) && powerkit_share_buttons_exists( 'post_sidebar' ) ) {
		$post_sidebar = 'post-sidebar-enabled';
	} else {
		$post_sidebar = 'post-sidebar-disabled';
	}

	wp_enqueue_script( 'csco-editor-wrapper', get_template_directory_uri() . '/js/gutenberg-wrapper.js',
		array(
			'wp-editor',
			'wp-element',
			'wp-compose',
			'wp-data',
			'wp-plugins',
		),
		csco_get_theme_data( 'Version' ),
		true
	);

	wp_localize_script( 'csco-editor-wrapper', 'cscoGWrapper', array(
		'post_type'      => $post_type,
		'default_layout' => $default_layout,
		'page_layout'    => $page_layout,
		'post_sidebar'   => $post_sidebar,
	) );
}
add_action( 'enqueue_block_editor_assets', 'csco_block_editor_wrapper' );

/**
 * Add css selectors to output of kirki.
 */
add_filter( 'csco_color_primary', function( $rules ) {
	array_push( $rules, array(
		'element'  => csco_implode( array(
			'.editor-styles-wrapper a',
		) ),
		'property' => 'color',
		'context'  => array( 'editor' ),
	) );
	return $rules;
} );

add_filter( 'csco_font_base', function( $rules ) {
	array_push( $rules, array(
		'element' => csco_implode( array(
			'.editor-styles-wrapper.cs-editor-styles-wrapper',
		) ),
		'context' => array( 'editor' ),
	) );
	return $rules;
} );

add_filter( 'csco_color_primary', function( $rules ) {
	array_push( $rules, array(
		'element'  => csco_implode( array(
			'.editor-styles-wrapper.cs-editor-styles-wrapper .wp-block-button .wp-block-button__link:not(.has-background)',
		) ),
		'property' => 'background-color',
		'context'  => array( 'editor' ),
	) );
	return $rules;
} );

add_filter( 'csco_font_secondary', function( $rules ) {
	array_push( $rules, array(
		'element' => csco_implode( array(
			'.editor-styles-wrapper.cs-editor-styles-wrapper .wp-block-quote cite',
			'.editor-styles-wrapper.cs-editor-styles-wrapper .wp-block-quote__citation',
			'.editor-styles-wrapper.cs-editor-styles-wrapper .wp-block-image figcaption',
			'.editor-styles-wrapper.cs-editor-styles-wrapper .wp-block-audio figcaption',
			'.editor-styles-wrapper.cs-editor-styles-wrapper .wp-block-embed figcaption',
			'.editor-styles-wrapper.cs-editor-styles-wrapper .wp-block-pullquote cite',
			'.editor-styles-wrapper.cs-editor-styles-wrapper .wp-block-pullquote footer',
			'.editor-styles-wrapper.cs-editor-styles-wrapper .wp-block-pullquote .wp-block-pullquote__citation',
		) ),
		'context' => array( 'editor' ),
	) );
	return $rules;
} );

add_filter( 'csco_font_post_content', function( $rules ) {
	array_push( $rules, array(
		'element' => csco_implode( array(
			'.editor-styles-wrapper .block-editor-block-list__layout',
			'.editor-styles-wrapper .block-editor-block-list__layout p',
		) ),
		'context' => array( 'editor' ),
	) );
	return $rules;
} );

add_filter( 'csco_font_headings', function( $rules ) {
	array_push( $rules, array(
		'element' => csco_implode( array(
			'.editor-styles-wrapper.cs-editor-styles-wrapper h1',
			'.editor-styles-wrapper.cs-editor-styles-wrapper h2',
			'.editor-styles-wrapper.cs-editor-styles-wrapper h3',
			'.editor-styles-wrapper.cs-editor-styles-wrapper h4',
			'.editor-styles-wrapper.cs-editor-styles-wrapper h5',
			'.editor-styles-wrapper.cs-editor-styles-wrapper h6',
			'.editor-styles-wrapper.cs-editor-styles-wrapper .h1',
			'.editor-styles-wrapper.cs-editor-styles-wrapper .h2',
			'.editor-styles-wrapper.cs-editor-styles-wrapper .h3',
			'.editor-styles-wrapper.cs-editor-styles-wrapper .h4',
			'.editor-styles-wrapper.cs-editor-styles-wrapper .h5',
			'.editor-styles-wrapper.cs-editor-styles-wrapper .h6',
			'.editor-styles-wrapper.cs-editor-styles-wrapper .editor-post-title__input',
			'.editor-styles-wrapper.cs-editor-styles-wrapper .wp-block-quote',
			'.editor-styles-wrapper.cs-editor-styles-wrapper .wp-block-quote p',
			'.editor-styles-wrapper.cs-editor-styles-wrapper .wp-block-pullquote p',
			'.editor-styles-wrapper.cs-editor-styles-wrapper .wp-block-freeform blockquote p:first-child',
			'.editor-styles-wrapper.cs-editor-styles-wrapper .wp-block-cover .wp-block-cover-image-text',
			'.editor-styles-wrapper.cs-editor-styles-wrapper .wp-block-cover .wp-block-cover-text',
			'.editor-styles-wrapper.cs-editor-styles-wrapper .wp-block-cover-image .wp-block-cover-image-text',
			'.editor-styles-wrapper.cs-editor-styles-wrapper .wp-block-cover-image .wp-block-cover-text',
			'.editor-styles-wrapper.cs-editor-styles-wrapper .wp-block-cover-image h2',
			'.editor-styles-wrapper.cs-editor-styles-wrapper .wp-block-cover h2',
			'.editor-styles-wrapper.cs-editor-styles-wrapper p.has-drop-cap:not(:focus):first-letter',
		) ),
		'context' => array( 'editor' ),
	) );
	return $rules;
} );
