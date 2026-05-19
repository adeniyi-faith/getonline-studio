<?php
/**
 * Typography
 *
 * @package Overflow
 */

CSCO_Kirki::add_panel(
	'typography', array(
		'title'    => esc_html__( 'Typography', 'overflow' ),
		'priority' => 30,
	)
);

CSCO_Kirki::add_section(
	'typography_general', array(
		'title'    => esc_html__( 'General', 'overflow' ),
		'panel'    => 'typography',
		'priority' => 10,
	)
);

CSCO_Kirki::add_field(
	'csco_theme_mod', array(
		'type'     => 'typography',
		'settings' => 'font_base',
		'label'    => esc_html__( 'Base Font', 'overflow' ),
		'section'  => 'typography_general',
		'default'  => array(
			'font-family'    => 'Lora',
			'variant'        => 'regular',
			'subsets'        => array( 'latin' ),
			'font-size'      => '1rem',
			'letter-spacing' => '0',
		),
		'choices'  => apply_filters( 'powerkit_fonts_choices', array(
			'variant' => array(
				'300',
				'regular',
				'italic',
				'500',
				'700',
				'700italic',
			),
		) ),
		'priority' => 10,
		'output'   => apply_filters( 'csco_font_base', array(
			array(
				'element' => 'body',
			),
		) ),
	)
);

CSCO_Kirki::add_field(
	'csco_theme_mod', array(
		'type'        => 'typography',
		'settings'    => 'font_primary',
		'label'       => esc_html__( 'Primary Font', 'overflow' ),
		'description' => esc_html__( 'Used for buttons, categories and tags, post meta links and other actionable elements.', 'overflow' ),
		'section'     => 'typography_general',
		'default'     => array(
			'font-family'    => 'jost',
			'variant'        => '500',
			'subsets'        => array( 'latin' ),
			'font-size'      => '0.6875rem',
			'letter-spacing' => '0.125em',
			'text-transform' => 'uppercase',
		),
		'choices'     => apply_filters( 'powerkit_fonts_choices', array(
			'variant' => array(
				'regular',
				'500',
				'700',
			),
		) ),
		'priority'    => 10,
		'output'      => apply_filters( 'csco_font_primary', array(
			array(
				'element' => '.cs-font-primary, button, .button, input[type="button"], input[type="reset"], input[type="submit"], .no-comments, .text-action, .cs-link-more, .archive-wrap .more-link, .share-total, .nav-links, .comment-reply-link, .post-tags a, .post-sidebar-tags a, .tagcloud a, .meta-author a, .post-categories a, .read-more, .navigation.pagination .nav-links > span, .navigation.pagination .nav-links > a, .subcategories .cs-nav-link, .entry-meta-details .pk-share-buttons-count, .entry-meta-details .pk-share-buttons-label, .pk-font-primary, .navbar-dropdown-btn-follow, .footer-instagram .instagram-username, .navbar-follow-instagram .navbar-follow-text, .navbar-follow-youtube .navbar-follow-text, .navbar-follow-facebook .navbar-follow-text, .pk-twitter-counters .number, .pk-instagram-counters .number, .navbar-follow .navbar-follow-counters .number',
			),
		) ),
	)
);

CSCO_Kirki::add_field(
	'csco_theme_mod', array(
		'type'        => 'typography',
		'settings'    => 'font_secondary',
		'label'       => esc_html__( 'Secondary Font', 'overflow' ),
		'description' => esc_html__( 'Used for post meta, image captions and other secondary elements.', 'overflow' ),
		'section'     => 'typography_general',
		'default'     => array(
			'font-family'    => 'jost',
			'subsets'        => array( 'latin' ),
			'variant'        => '500',
			'font-size'      => '0.75rem',
			'letter-spacing' => '0',
			'text-transform' => 'none',
		),
		'choices'     => apply_filters( 'powerkit_fonts_choices', array(
			'variant' => array(
				'regular',
				'500',
				'700',
			),
		) ),
		'priority'    => 10,
		'output'      => apply_filters( 'csco_font_secondary', array(
			array(
				'element' => 'input[type="text"], input[type="email"], input[type="url"], input[type="password"], input[type="search"], input[type="number"], input[type="tel"], input[type="range"], input[type="date"], input[type="month"], input[type="week"], input[type="time"], input[type="datetime"], input[type="datetime-local"], input[type="color"], select, textarea, label, .cs-font-secondary, .post-meta, .archive-count, .page-subtitle, .site-description, figcaption, .wp-block-image figcaption, .wp-block-audio figcaption, .wp-block-embed figcaption, .wp-block-pullquote cite, .wp-block-pullquote footer, .wp-block-pullquote .wp-block-pullquote__citation, .post-format-icon, .comment-metadata, .says, .logged-in-as, .must-log-in, .wp-caption-text, .widget_rss ul li .rss-date, blockquote cite, .wp-block-quote cite, div[class*="meta-"], span[class*="meta-"], .navbar-brand .tagline, small, .post-sidebar-shares .total-shares, .cs-breadcrumbs, .cs-homepage-category-count, .navbar-follow-counters, .searchwp-live-search-no-results em, .searchwp-live-search-no-min-chars:after, .pk-font-secondary, .pk-instagram-counters, .pk-twitter-counters, .pk-post-item .pk-post-number, .footer-copyright, .cs-trending-posts .cs-post-number, .pk-instagram-item .pk-instagram-data .pk-meta, .navbar-follow-button .navbar-follow-text',
			),
		) ),
	)
);

CSCO_Kirki::add_field(
	'csco_theme_mod', array(
		'type'     => 'typography',
		'settings' => 'font_post_content',
		'label'    => esc_html__( 'Post Content', 'overflow' ),
		'section'  => 'typography_general',
		'default'  => array(
			'font-family'    => 'inherit',
			'variant'        => 'inherit',
			'subsets'        => array( 'latin' ),
			'font-size'      => 'inherit',
			'letter-spacing' => 'inherit',
		),
		'choices'  => apply_filters( 'powerkit_fonts_choices', array(
			'variant' => array(
				'300',
				'regular',
				'italic',
				'500',
				'700',
				'700italic',
			),
		) ),
		'priority' => 10,
		'output'   => apply_filters( 'csco_font_post_content', array(
			array(
				'element' => '.entry-content',
			),
		) ),
	)
);

CSCO_Kirki::add_field(
	'csco_theme_mod', array(
		'type'     => 'typography',
		'settings' => 'font_accent',
		'label'    => esc_html__( 'Accent Font', 'overflow' ),
		'section'  => 'typography_general',
		'default'  => array(
			'font-family'    => 'Shadows Into Light',
			'subsets'        => array( 'latin' ),
			'variant'        => 'regular',
			'font-size'      => '1.25rem',
			'letter-spacing' => '0',
			'line-height'    => '1',
			'text-transform' => 'none',
		),
		'choices'  => apply_filters( 'powerkit_fonts_choices', array() ),
		'priority' => 10,
		'output'   => apply_filters( 'csco_font_accent', array(
			array(
				'element' => '.navbar-follow-title, .subscribe-title, .trending-title',
			),
		) ),
	)
);

CSCO_Kirki::add_section(
	'typography_logos', array(
		'title'    => esc_html__( 'Logos', 'overflow' ),
		'panel'    => 'typography',
		'priority' => 10,
	)
);

CSCO_Kirki::add_field(
	'csco_theme_mod', array(
		'type'        => 'typography',
		'settings'    => 'font_main_logo',
		'label'       => esc_html__( 'Main Logo', 'overflow' ),
		'description' => esc_html__( 'The main logo is used in the navigation bar and mobile view of your website.', 'overflow' ),
		'section'     => 'typography_logos',
		'default'     => array(
			'font-family'    => 'jost',
			'font-size'      => '1.25rem',
			'variant'        => '400',
			'subsets'        => array( 'latin' ),
			'letter-spacing' => '0.125em',
			'text-transform' => 'uppercase',
		),
		'choices'     => apply_filters( 'powerkit_fonts_choices', array() ),
		'priority'    => 10,
		'output'      => apply_filters( 'csco_font_accent', array(
			array(
				'element' => '.site-title',
			),
		) ),
	)
);

CSCO_Kirki::add_field(
	'csco_theme_mod', array(
		'type'            => 'typography',
		'settings'        => 'font_large_logo',
		'label'           => esc_html__( 'Large Logo', 'overflow' ),
		'section'         => 'typography_logos',
		'default'         => array(
			'font-family'    => 'jost',
			'font-size'      => '2rem',
			'variant'        => '400',
			'subsets'        => array( 'latin' ),
			'letter-spacing' => '0.125em',
			'text-transform' => 'uppercase',
		),
		'description'     => esc_html__( 'The large logo is used in the site header in desktop view.', 'overflow' ),
		'choices'         => apply_filters( 'powerkit_fonts_choices', array() ),
		'priority'        => 10,
		'active_callback' => array(
			array(
				'setting'  => 'header_layout',
				'operator' => '==',
				'value'    => 'large',
			),
		),
		'output'          => apply_filters( 'csco_font_large_logo', array(
			array(
				'element' => '.large-title',
			),
		) ),
	)
);

CSCO_Kirki::add_field(
	'csco_theme_mod', array(
		'type'        => 'typography',
		'settings'    => 'font_footer_logo',
		'label'       => esc_html__( 'Footer Logo', 'overflow' ),
		'description' => esc_html__( 'The footer logo is used in the site footer in desktop and mobile view.', 'overflow' ),
		'section'     => 'typography_logos',
		'default'     => array(
			'font-family'    => 'jost',
			'font-size'      => '2rem',
			'variant'        => '400',
			'subsets'        => array( 'latin' ),
			'letter-spacing' => '0.125em',
			'text-transform' => 'uppercase',
		),
		'choices'     => apply_filters( 'powerkit_fonts_choices', array() ),
		'priority'    => 10,
		'output'      => apply_filters( 'csco_font_footer_logo', array(
			array(
				'element' => '.footer-title',
			),
		) ),
	)
);

CSCO_Kirki::add_section(
	'typography_headings', array(
		'title'    => esc_html__( 'Headings', 'overflow' ),
		'panel'    => 'typography',
		'priority' => 10,
	)
);

CSCO_Kirki::add_field(
	'csco_theme_mod', array(
		'type'     => 'typography',
		'settings' => 'font_headings',
		'label'    => esc_html__( 'Headings', 'overflow' ),
		'section'  => 'typography_headings',
		'default'  => array(
			'font-family'    => 'jost',
			'variant'        => '400',
			'subsets'        => array( 'latin' ),
			'letter-spacing' => '-0.025em',
			'text-transform' => 'none',
		),
		'choices'  => apply_filters( 'powerkit_fonts_choices', array() ),
		'priority' => 10,
		'output'   => apply_filters( 'csco_font_headings', array(
			array(
				'element' => 'h1, h2, h3, h4, h5, h6, .h1, .h2, .h3, .h4, .h5, .h6, .comment-author .fn, blockquote, .pk-font-heading, .post-sidebar-date .reader-text, .wp-block-quote, .wp-block-cover .wp-block-cover-image-text, .wp-block-cover .wp-block-cover-text, .wp-block-cover h2, .wp-block-cover-image .wp-block-cover-image-text, .wp-block-cover-image .wp-block-cover-text, .wp-block-cover-image h2, .wp-block-pullquote p, p.has-drop-cap:not(:focus):first-letter, .pk-font-heading',
			),
		) ),
	)
);

CSCO_Kirki::add_field(
	'csco_theme_mod', array(
		'type'        => 'typography',
		'settings'    => 'font_title_block',
		'label'       => esc_html__( 'Section Titles', 'overflow' ),
		'description' => esc_html__( 'Used for widget, related posts and other sections\' titles.', 'overflow' ),
		'section'     => 'typography_headings',
		'default'     => array(
			'font-family'    => 'jost',
			'variant'        => '500',
			'subsets'        => array( 'latin' ),
			'font-size'      => '0.75rem',
			'letter-spacing' => '0.125em',
			'text-transform' => 'uppercase',
			'color'          => '#000000',
		),
		'choices'     => apply_filters( 'powerkit_fonts_choices', array() ),
		'priority'    => 10,
		'output'      => apply_filters( 'csco_font_title_block', array(
			array(
				'element' => '.title-block, .pk-font-block',
			),
		) ),
	)
);

CSCO_Kirki::add_section(
	'typography_navigation', array(
		'title'    => esc_html__( 'Navigation', 'overflow' ),
		'panel'    => 'typography',
		'priority' => 10,
	)
);

CSCO_Kirki::add_field(
	'csco_theme_mod', array(
		'type'        => 'typography',
		'settings'    => 'font_menu',
		'label'       => esc_html__( 'Menu Font', 'overflow' ),
		'description' => esc_html__( 'Used for main top level menu elements.', 'overflow' ),
		'section'     => 'typography_navigation',
		'default'     => array(
			'font-family'    => 'jost',
			'variant'        => 'regular',
			'subsets'        => array( 'latin' ),
			'font-size'      => '0.75rem',
			'letter-spacing' => '0.075em',
			'text-transform' => 'uppercase',
		),
		'choices'     => apply_filters( 'powerkit_fonts_choices', array() ),
		'priority'    => 10,
		'output'      => apply_filters( 'csco_font_menu', array(
			array(
				'element' => '.navbar-nav > li > a, .cs-mega-menu-child > a, .widget_archive li, .widget_categories li, .widget_meta li a, .widget_nav_menu .menu > li > a, .widget_pages .page_item a',
			),
		) ),
	)
);

CSCO_Kirki::add_field(
	'csco_theme_mod', array(
		'type'        => 'typography',
		'settings'    => 'font_submenu',
		'label'       => esc_html__( 'Submenu Font', 'overflow' ),
		'description' => esc_html__( 'Used for submenu elements.', 'overflow' ),
		'section'     => 'typography_navigation',
		'default'     => array(
			'font-family'    => 'jost',
			'subsets'        => array( 'latin' ),
			'variant'        => 'regular',
			'font-size'      => '0.75rem',
			'letter-spacing' => '0.075em',
			'text-transform' => 'uppercase',
		),
		'choices'     => apply_filters( 'powerkit_fonts_choices', array() ),
		'priority'    => 10,
		'output'      => apply_filters( 'csco_font_submenu', array(
			array(
				'element' => '.navbar-nav .sub-menu > li > a, .widget_categories .children li a, .widget_nav_menu .sub-menu > li > a',
			),
		) ),
	)
);
