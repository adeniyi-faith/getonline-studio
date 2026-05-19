<?php
/**
 * Design
 *
 * @package Overflow
 */

CSCO_Kirki::add_section(
	'design', array(
		'title'    => esc_html__( 'Design', 'overflow' ),
		'priority' => 20,
	)
);

/**
 * -------------------------------------------------------------------------
 * Colors
 * -------------------------------------------------------------------------
 */

CSCO_Kirki::add_section(
	'design_base', array(
		'title'    => esc_html__( 'design', 'overflow' ),
		'panel'    => 'design',
		'priority' => 10,
	)
);

CSCO_Kirki::add_field(
	'csco_theme_mod', array(
		'type'     => 'color',
		'settings' => 'color_primary',
		'label'    => esc_html__( 'Primary Color', 'overflow' ),
		'section'  => 'design',
		'priority' => 10,
		'default'  => '#fd79a8',
		'output'   => apply_filters( 'csco_color_primary', array(
			array(
				'element'  => 'a:hover, .entry-content a, .must-log-in a, blockquote:before, .cs-bg-dark .pk-social-links-scheme-bold:not(.pk-social-links-scheme-light-rounded) .pk-social-links-link .pk-social-links-icon, .navbar-follow-title, .subscribe-title, .trending-title',
				'property' => 'color',
			),
			array(
				'element'  => '.cs-bg-dark .pk-social-links-scheme-light-rounded .pk-social-links-link:hover .pk-social-links-icon, article .cs-overlay .post-categories a:hover, .post-format-icon > a:hover, .cs-list-articles > li > a:hover:before, .design-heading-stroke-line .title-stroke:after, .design-title-stroke-line .title-stroke-block:after, .pk-bg-primary, .pk-button-primary, .pk-badge-primary, h2.pk-heading-numbered:before, .pk-post-item .pk-post-thumbnail a:hover .pk-post-number, .cs-trending-post .cs-post-thumbnail a:hover .cs-post-number, .cs-video-tools .cs-player-control:hover',
				'property' => 'background-color',
			),
			array(
				'element'       => '.design-border-radius .pk-widget-posts-template-default .pk-post-item .pk-post-thumbnail a:hover:after, .cs-trending-post .cs-post-thumbnail a:hover:after, .navbar-follow-instagram .navbar-follow-avatar-link:hover:after, .widget .pk-instagram-feed .pk-avatar-link:hover:after, .widget .pk-twitter-link:hover:after',
				'property'      => 'background',
				'value_pattern' => 'linear-gradient(-45deg, $, #FFFFFF)',
			),
			array(
				'element'       => '.design-heading-stroke-dotted .title-stroke:after, .design-title-stroke-dotted .title-stroke-block:after',
				'property'      => 'background-image',
				'value_pattern' => 'radial-gradient(circle, $ 1.5px, transparent 2px)',
			),
		) ),
	)
);

CSCO_Kirki::add_field(
	'csco_theme_mod', array(
		'type'      => 'color',
		'settings'  => 'color_overlay',
		'label'     => esc_html__( 'Overlay Color', 'overflow' ),
		'section'   => 'design',
		'priority'  => 10,
		'default'   => 'rgba(0,0,0,0.25)',
		'transport' => 'auto',
		'choices'   => array(
			'alpha' => true,
		),
		'output'    => apply_filters( 'csco_color_overlay', array(
			array(
				'element'  => '.cs-overlay-background:after, .cs-overlay-hover:hover .cs-overlay-background:after, .cs-overlay-hover:focus .cs-overlay-background:after, .gallery-type-justified .gallery-item > .caption, .pk-zoom-icon-popup:after, .pk-widget-posts .pk-post-thumbnail:hover a:after',
				'property' => 'background-color',
			),
		) ),
	)
);

CSCO_Kirki::add_field(
	'csco_theme_mod', array(
		'type'            => 'color',
		'settings'        => 'color_large_header_bg',
		'label'           => esc_html__( 'Header Background', 'overflow' ),
		'section'         => 'design',
		'default'         => '#FFFFFF',
		'priority'        => 10,
		'active_callback' => array(
			array(
				'setting'  => 'header_layout',
				'operator' => '==',
				'value'    => 'large',
			),
		),
		'output'          => array(
			array(
				'element'  => '.header-large .navbar-topbar',
				'property' => 'background-color',
			),
		),
	)
);

CSCO_Kirki::add_field(
	'csco_theme_mod', array(
		'type'     => 'color',
		'settings' => 'color_navbar_bg',
		'label'    => esc_html__( 'Navigation Bar Background', 'overflow' ),
		'section'  => 'design',
		'default'  => '#FFFFFF',
		'priority' => 10,
		'output'   => array(
			array(
				'element'  => '.navbar-primary, .offcanvas-header',
				'property' => 'background-color',
			),
		),
	)
);

CSCO_Kirki::add_field(
	'csco_theme_mod', array(
		'type'     => 'color',
		'settings' => 'color_navbar_submenu',
		'label'    => esc_html__( 'Navigation Submenu Background', 'overflow' ),
		'section'  => 'design',
		'default'  => '#000000',
		'priority' => 10,
		'output'   => array(
			array(
				'element'  => '.navbar-nav .sub-menu, .navbar-nav .cs-mega-menu-has-categories .cs-mm-categories, .navbar-primary .navbar-dropdown-container',
				'property' => 'background-color',
			),
			array(
				'element'  => '.navbar-nav > li.menu-item-has-children > .sub-menu:after, .navbar-primary .navbar-dropdown-container:after',
				'property' => 'border-bottom-color',
			),
		),
	)
);

/**
 * -------------------------------------------------------------------------
 * Design
 * -------------------------------------------------------------------------
 */

CSCO_Kirki::add_field(
	'csco_theme_mod', array(
		'type'     => 'radio',
		'settings' => 'design_text_alignment',
		'label'    => esc_html__( 'Text Alignment', 'overflow' ),
		'section'  => 'design',
		'default'  => 'center',
		'priority' => 10,
		'choices'  => array(
			'center' => esc_html__( 'Center', 'overflow' ),
			'left'   => esc_html__( 'Left', 'overflow' ),
		),
	)
);

CSCO_Kirki::add_field(
	'csco_theme_mod', array(
		'type'              => 'dimension',
		'settings'          => 'design_border_radius',
		'label'             => esc_html__( 'Border Radius', 'overflow' ),
		'description'       => esc_html__( 'For example: 30px. If the input is empty, original value will be used.', 'overflow' ),
		'section'           => 'design',
		'default'           => '30px',
		'priority'          => 10,
		'sanitize_callback' => 'esc_html',
		'output'            => apply_filters( 'csco_design_border_radius', array(
			array(
				'element'  => 'button, input[type="button"], input[type="reset"], input[type="submit"], .wp-block-button:not(.is-style-squared) .wp-block-button__link, .button, .archive-wrap .more-link, .pk-button, .pk-scroll-to-top, .cs-overlay .post-categories a, .site-search [type="search"], .subcategories .cs-nav-link, .post-header .pk-share-buttons-wrap .pk-share-buttons-link, .pk-dropcap-borders:first-letter, .pk-dropcap-bg-inverse:first-letter, .pk-dropcap-bg-light:first-letter, .widget-area .pk-subscribe-with-name input[type="text"], .widget-area .pk-subscribe-with-name button, .widget-area .pk-subscribe-with-bg input[type="text"], .widget-area .pk-subscribe-with-bg button, .footer-instagram .instagram-username',
				'property' => 'border-radius',
			),
			array(
				'element'     => '.pk-subscribe-with-name input[type="text"], .pk-subscribe-with-bg input[type="text"]',
				'property'    => 'border-radius',
				'media_query' => '@media (max-width: 599px)',
			),
			array(
				'element'  => '.cs-input-group input[type="search"], .pk-subscribe-form-wrap input[type="text"]:first-child',
				'property' => 'border-top-left-radius',
			),
			array(
				'element'  => '.cs-input-group input[type="search"], .pk-subscribe-form-wrap input[type="text"]:first-child',
				'property' => 'border-bottom-left-radius',
			),
		) ),
	)
);

CSCO_Kirki::add_field(
	'csco_theme_mod', array(
		'type'     => 'radio',
		'settings' => 'design_edge_type',
		'label'    => esc_html__( 'Edge Type', 'overflow' ),
		'section'  => 'design',
		'default'  => 'straight',
		'priority' => 10,
		'choices'  => array(
			'straight' => esc_html__( 'Straight', 'overflow' ),
			'brush'    => esc_html__( 'Brush', 'overflow' ),
			'slanted'  => esc_html__( 'Slanted', 'overflow' ),
			'wave'     => esc_html__( 'Wave', 'overflow' ),
		),
	)
);

CSCO_Kirki::add_field(
	'csco_theme_mod', array(
		'type'     => 'radio',
		'settings' => 'design_heading_stroke',
		'label'    => esc_html__( 'Heading Stroke Type', 'overflow' ),
		'section'  => 'design',
		'default'  => 'dotted',
		'priority' => 10,
		'choices'  => array(
			'dotted' => esc_html__( 'Dotted', 'overflow' ),
			'brush'  => esc_html__( 'Brush', 'overflow' ),
			'line'   => esc_html__( 'Line', 'overflow' ),
			'zigzag' => esc_html__( 'Zigzag', 'overflow' ),
			'wave'   => esc_html__( 'Wave', 'overflow' ),
			'none'   => esc_html__( 'None', 'overflow' ),
		),
	)
);

CSCO_Kirki::add_field(
	'csco_theme_mod', array(
		'type'      => 'radio',
		'settings'  => 'design_title_stroke',
		'label'     => esc_html__( 'Title Block Stroke Type', 'overflow' ),
		'section'   => 'design',
		'default'   => 'dotted',
		'priority'  => 10,
		'transport' => 'refresh',
		'choices'   => array(
			'dotted' => esc_html__( 'Dotted', 'overflow' ),
			'brush'  => esc_html__( 'Brush', 'overflow' ),
			'zigzag' => esc_html__( 'Zigzag', 'overflow' ),
			'wave'   => esc_html__( 'Wave', 'overflow' ),
			'line'   => esc_html__( 'Line', 'overflow' ),
			'none'   => esc_html__( 'None', 'overflow' ),
		),
	)
);
