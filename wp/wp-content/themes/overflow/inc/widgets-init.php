<?php
/**
 * Widgets Init
 *
 * Register sitebar locations for widgets.
 *
 * @package Overflow
 */

if ( ! function_exists( 'csco_widgets_init' ) ) {
	/**
	 * Register widget areas.
	 *
	 * @link https://developer.wordpress.org/themes/functionality/sidebars/#registering-a-sidebar
	 */
	function csco_widgets_init() {

		$tag = apply_filters( 'csco_section_title_tag', 'h5' );

		register_sidebar(
			array(
				'name'          => esc_html__( 'Default Sidebar', 'overflow' ),
				'id'            => 'sidebar-main',
				'before_widget' => '<div class="widget %1$s %2$s">',
				'after_widget'  => '</div>',
				'before_title'  => '<div class="title-block-wrap"><' . $tag . ' class="title-block title-stroke-block title-widget">',
				'after_title'   => '</' . $tag . '></div>',
			)
		);

		register_sidebar(
			array(
				'name'          => esc_html__( 'Off-Canvas', 'overflow' ),
				'id'            => 'sidebar-offcanvas',
				'before_widget' => '<div class="widget %1$s %2$s">',
				'after_widget'  => '</div>',
				'before_title'  => '<div class="title-block-wrap"><' . $tag . ' class="title-block title-stroke-block title-widget">',
				'after_title'   => '</' . $tag . '></div>',
			)
		);

		register_sidebar(
			array(
				'name'          => esc_html__( 'Auto Loaded Sidebar', 'overflow' ),
				'id'            => 'sidebar-loaded',
				'before_widget' => '<div class="widget %1$s %2$s">',
				'after_widget'  => '</div>',
				'before_title'  => '<div class="title-block-wrap"><' . $tag . ' class="title-block title-stroke-block title-widget">',
				'after_title'   => '</' . $tag . '></div>',
			)
		);
	}
}
add_action( 'widgets_init', 'csco_widgets_init' );
