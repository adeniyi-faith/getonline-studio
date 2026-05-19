<?php
/**
 * Homepage Settings
 *
 * @package Overflow
 */

/**
 * Removes default WordPress Static Front Page section
 * and re-adds it in our own panel with the same parameters.
 *
 * @param object $wp_customize Instance of the WP_Customize_Manager class.
 */
function csco_reorder_customizer_settings( $wp_customize ) {

	// Get current front page section parameters.
	$static_front_page = $wp_customize->get_section( 'static_front_page' );

	// Remove existing section, so that we can later re-add it to our panel.
	$wp_customize->remove_section( 'static_front_page' );

	// Re-add static front page section with a new name, but same description.
	$wp_customize->add_section( 'static_front_page', array(
		'title'           => esc_html__( 'Static Front Page', 'overflow' ),
		'priority'        => 20,
		'description'     => $static_front_page->description,
		'panel'           => 'homepage_settings',
		'active_callback' => $static_front_page->active_callback,
	) );
}
add_action( 'customize_register', 'csco_reorder_customizer_settings' );

CSCO_Kirki::add_panel(
	'homepage_settings', array(
		'title'    => esc_html__( 'Homepage Settings', 'overflow' ),
		'priority' => 50,
	)
);

CSCO_Kirki::add_section(
	'homepage_layout', array(
		'title'    => esc_html__( 'Homepage Layout', 'overflow' ),
		'priority' => 15,
		'panel'    => 'homepage_settings',
	)
);

CSCO_Kirki::add_field(
	'csco_theme_mod', array(
		'type'     => 'radio',
		'settings' => 'home_layout',
		'label'    => esc_html__( 'Layout', 'overflow' ),
		'section'  => 'homepage_layout',
		'default'  => 'list',
		'priority' => 10,
		'choices'  => array(
			'full'    => esc_html__( 'Full Post Layout', 'overflow' ),
			'list'    => esc_html__( 'List Layout', 'overflow' ),
			'grid'    => esc_html__( 'Grid Layout', 'overflow' ),
			'masonry' => esc_html__( 'Masonry Layout', 'overflow' ),
		),
	)
);

CSCO_Kirki::add_field(
	'csco_theme_mod', array(
		'type'     => 'radio',
		'settings' => 'home_sidebar',
		'label'    => esc_html__( 'Sidebar', 'overflow' ),
		'section'  => 'homepage_layout',
		'default'  => 'right',
		'priority' => 10,
		'choices'  => array(
			'right'    => esc_attr__( 'Right Sidebar', 'overflow' ),
			'left'     => esc_attr__( 'Left Sidebar', 'overflow' ),
			'disabled' => esc_attr__( 'No Sidebar', 'overflow' ),
		),
	)
);

CSCO_Kirki::add_field(
	'csco_theme_mod', array(
		'type'     => 'multicheck',
		'settings' => 'home_post_meta',
		'label'    => esc_attr__( 'Post Meta', 'overflow' ),
		'section'  => 'homepage_layout',
		'default'  => array( 'category', 'author', 'date', 'views', 'reading_time' ),
		'priority' => 10,
		'choices'  => apply_filters( 'csco_post_meta_choices', array(
			'category'     => esc_html__( 'Category', 'overflow' ),
			'author'       => esc_html__( 'Author', 'overflow' ),
			'date'         => esc_html__( 'Date', 'overflow' ),
			'shares'       => esc_html__( 'Shares', 'overflow' ),
			'views'        => esc_html__( 'Views', 'overflow' ),
			'comments'     => esc_html__( 'Comments', 'overflow' ),
			'reading_time' => esc_html__( 'Reading Time', 'overflow' ),
		) ),
	)
);

CSCO_Kirki::add_field(
	'csco_theme_mod', array(
		'type'     => 'radio',
		'settings' => 'homepage_media_preview',
		'label'    => esc_html__( 'Full Post Preview', 'overflow' ),
		'section'  => 'homepage_layout',
		'default'  => 'cropped',
		'priority' => 10,
		'choices'  => array(
			'cropped'   => esc_attr__( 'Display Cropped Image', 'overflow' ),
			'uncropped' => esc_attr__( 'Display Preview in Original Ratio', 'overflow' ),
		),
		'active_callback' => array(
			array(
				array(
					'setting'  => 'home_layout',
					'operator' => '==',
					'value'    => 'full',
				),
			),
		),
	)
);

CSCO_Kirki::add_field(
	'csco_theme_mod', array(
		'type'            => 'radio',
		'settings'        => 'home_summary',
		'label'           => esc_html__( 'Full Post Summary', 'overflow' ),
		'section'         => 'homepage_layout',
		'default'         => 'excerpt',
		'priority'        => 10,
		'choices'         => array(
			'excerpt' => esc_html__( 'Use Excerpts', 'overflow' ),
			'content' => esc_html__( 'Use Read More Tag', 'overflow' ),
		),
		'active_callback' => array(
			array(
				array(
					'setting'  => 'home_layout',
					'operator' => '==',
					'value'    => 'full',
				),
			),
		),
	)
);

CSCO_Kirki::add_field(
	'csco_theme_mod', array(
		'type'     => 'checkbox',
		'settings' => 'home_more_button',
		'label'    => esc_html__( 'Display Read More button', 'overflow' ),
		'section'  => 'homepage_layout',
		'default'  => true,
		'priority' => 10,
	)
);

CSCO_Kirki::add_field(
	'csco_theme_mod', array(
		'type'     => 'radio',
		'settings' => 'home_pagination_type',
		'label'    => esc_html__( 'Pagination', 'overflow' ),
		'section'  => 'homepage_layout',
		'default'  => 'load-more',
		'priority' => 10,
		'choices'  => array(
			'standard'  => esc_html__( 'Standard', 'overflow' ),
			'load-more' => esc_html__( 'Load More Button', 'overflow' ),
			'infinite'  => esc_html__( 'Infinite Load', 'overflow' ),
		),
	)
);

CSCO_Kirki::add_section(
	'featured_posts', array(
		'title'    => esc_html__( 'Featured Posts', 'overflow' ),
		'panel'    => 'homepage_settings',
		'priority' => 10,
	)
);

CSCO_Kirki::add_field(
	'csco_theme_mod', array(
		'type'     => 'checkbox',
		'settings' => 'featured_posts',
		'label'    => esc_html__( 'Display featured posts', 'overflow' ),
		'section'  => 'featured_posts',
		'default'  => false,
		'priority' => 10,
	)
);

CSCO_Kirki::add_field(
	'csco_theme_mod', array(
		'type'            => 'radio',
		'settings'        => 'featured_posts_type',
		'label'           => esc_html__( 'Layout', 'overflow' ),
		'section'         => 'featured_posts',
		'default'         => 'type-2',
		'priority'        => 10,
		'choices'         => array(
			'type-1' => esc_html__( 'Type 1', 'overflow' ),
			'type-2' => esc_html__( 'Type 2', 'overflow' ),
			'type-3' => esc_html__( 'Type 3', 'overflow' ),
			'type-4' => esc_html__( 'Type 4', 'overflow' ),
		),
		'active_callback' => array(
			array(
				'setting'  => 'featured_posts',
				'operator' => '==',
				'value'    => true,
			),
		),
	)
);

CSCO_Kirki::add_field(
	'csco_theme_mod', array(
		'type'            => 'radio',
		'settings'        => 'featured_posts_location',
		'label'           => esc_html__( 'Display Location', 'overflow' ),
		'section'         => 'featured_posts',
		'default'         => 'front_page',
		'priority'        => 10,
		'choices'         => array(
			'front_page' => esc_html__( 'Homepage', 'overflow' ),
			'home'       => esc_html__( 'Posts page', 'overflow' ),
		),
		'active_callback' => array(
			array(
				'setting'  => 'featured_posts',
				'operator' => '==',
				'value'    => true,
			),
			array(
				'setting'  => 'show_on_front',
				'operator' => '==',
				'value'    => 'page',
			),
		),
	)
);

CSCO_Kirki::add_field(
	'csco_theme_mod', array(
		'type'            => 'multicheck',
		'settings'        => 'featured_posts_meta',
		'label'           => esc_attr__( 'Post Meta', 'overflow' ),
		'section'         => 'featured_posts',
		'default'         => array( 'category', 'author', 'date' ),
		'priority'        => 10,
		'choices'         => apply_filters( 'csco_post_meta_choices', array(
			'category'     => esc_html__( 'Category', 'overflow' ),
			'author'       => esc_html__( 'Author', 'overflow' ),
			'date'         => esc_html__( 'Date', 'overflow' ),
			'shares'       => esc_html__( 'Shares', 'overflow' ),
			'views'        => esc_html__( 'Views', 'overflow' ),
			'comments'     => esc_html__( 'Comments', 'overflow' ),
			'reading_time' => esc_html__( 'Reading Time', 'overflow' ),
		) ),
		'active_callback' => array(
			array(
				'setting'  => 'featured_posts',
				'operator' => '==',
				'value'    => true,
			),
		),
	)
);

CSCO_Kirki::add_field(
	'csco_theme_mod', array(
		'type'            => 'text',
		'settings'        => 'featured_posts_filter_categories',
		'label'           => esc_html__( 'Filter by Categories', 'overflow' ),
		'description'     => esc_html__( 'Add comma-separated list of category slugs. For example: &laquo;travel, lifestyle, food&raquo;. Leave empty for all categories.', 'overflow' ),
		'section'         => 'featured_posts',
		'default'         => '',
		'priority'        => 10,
		'active_callback' => array(
			array(
				'setting'  => 'featured_posts',
				'operator' => '==',
				'value'    => true,
			),
		),
	)
);

CSCO_Kirki::add_field(
	'csco_theme_mod', array(
		'type'            => 'text',
		'settings'        => 'featured_posts_filter_tags',
		'label'           => esc_html__( 'Filter by Tags', 'overflow' ),
		'description'     => esc_html__( 'Add comma-separated list of tag slugs. For example: &laquo;worth-reading, top-5, playlists&raquo;. Leave empty for all tags.', 'overflow' ),
		'section'         => 'featured_posts',
		'default'         => '',
		'priority'        => 10,
		'active_callback' => array(
			array(
				'setting'  => 'featured_posts',
				'operator' => '==',
				'value'    => true,
			),
		),
	)
);

CSCO_Kirki::add_field(
	'csco_theme_mod', array(
		'type'            => 'text',
		'settings'        => 'featured_posts_filter_posts',
		'label'           => esc_html__( 'Filter by Posts', 'overflow' ),
		'description'     => esc_html__( 'Add comma-separated list of post IDs. For example: 12, 34, 145. Leave empty for all posts.', 'overflow' ),
		'section'         => 'featured_posts',
		'default'         => '',
		'priority'        => 10,
		'active_callback' => array(
			array(
				'setting'  => 'featured_posts',
				'operator' => '==',
				'value'    => true,
			),
		),
	)
);

if ( class_exists( 'Post_Views_Counter' ) ) {

	CSCO_Kirki::add_field(
		'csco_theme_mod', array(
			'type'            => 'radio',
			'settings'        => 'featured_posts_orderby',
			'label'           => esc_html__( 'Order posts by', 'overflow' ),
			'section'         => 'featured_posts',
			'default'         => 'date',
			'priority'        => 10,
			'choices'         => array(
				'date'       => esc_html__( 'Date', 'overflow' ),
				'post_views' => esc_html__( 'Views', 'overflow' ),
			),
			'active_callback' => array(
				array(
					'setting'  => 'featured_posts',
					'operator' => '==',
					'value'    => true,
				),
			),
		)
	);

	CSCO_Kirki::add_field(
		'csco_theme_mod', array(
			'type'            => 'text',
			'settings'        => 'featured_posts_time_frame',
			'label'           => esc_html__( 'Filter by Time Frame', 'overflow' ),
			'description'     => esc_html__( 'Add period of posts in English. For example: &laquo;2 months&raquo;, &laquo;14 days&raquo; or even &laquo;1 year&raquo;', 'overflow' ),
			'section'         => 'featured_posts',
			'default'         => '',
			'priority'        => 10,
			'active_callback' => array(
				array(
					'setting'  => 'featured_posts',
					'operator' => '==',
					'value'    => true,
				),
				array(
					'setting'  => 'featured_posts_orderby',
					'operator' => '==',
					'value'    => 'post_views',
				),
			),
		)
	);
}

CSCO_Kirki::add_field(
	'csco_theme_mod', array(
		'type'            => 'checkbox',
		'settings'        => 'featured_posts_more_button',
		'label'           => esc_html__( 'Display Read More button', 'overflow' ),
		'section'         => 'featured_posts',
		'default'         => true,
		'priority'        => 10,
		'active_callback' => array(
			array(
				'setting'  => 'featured_posts',
				'operator' => '==',
				'value'    => true,
			),
		),
	)
);

CSCO_Kirki::add_field(
	'csco_theme_mod', array(
		'type'            => 'checkbox',
		'settings'        => 'featured_posts_exclude',
		'label'           => esc_html__( 'Exclude featured posts from the main archive', 'overflow' ),
		'section'         => 'featured_posts',
		'default'         => false,
		'priority'        => 10,
		'active_callback' => array(
			array(
				'setting'  => 'featured_posts',
				'operator' => '==',
				'value'    => true,
			),
		),
	)
);

CSCO_Kirki::add_section(
	'trending_featured_posts', array(
		'title'    => esc_html__( 'Trending Posts', 'overflow' ),
		'panel'    => 'homepage_settings',
		'priority' => 10,
	)
);

CSCO_Kirki::add_field(
	'csco_theme_mod', array(
		'type'     => 'checkbox',
		'settings' => 'trending_featured_posts',
		'label'    => esc_html__( 'Display trending posts', 'overflow' ),
		'section'  => 'trending_featured_posts',
		'default'  => false,
		'priority' => 10,
	)
);

CSCO_Kirki::add_field(
	'csco_theme_mod', array(
		'type'              => 'text',
		'settings'          => 'trending_featured_posts_title',
		'label'             => esc_html__( 'Title', 'overflow' ),
		'section'           => 'trending_featured_posts',
		'default'           => esc_html__( 'Check Out These Posts', 'overflow' ),
		'priority'          => 10,
		'sanitize_callback' => 'wp_kses_post',
		'active_callback'   => array(
			array(
				'setting'  => 'trending_featured_posts',
				'operator' => '==',
				'value'    => true,
			),
		),
	)
);

CSCO_Kirki::add_field(
	'csco_theme_mod', array(
		'type'            => 'radio',
		'settings'        => 'trending_featured_posts_location',
		'label'           => esc_html__( 'Display Location', 'overflow' ),
		'section'         => 'trending_featured_posts',
		'default'         => 'front_page',
		'priority'        => 10,
		'choices'         => array(
			'front_page' => esc_html__( 'Homepage', 'overflow' ),
			'home'       => esc_html__( 'Posts page', 'overflow' ),
		),
		'active_callback' => array(
			array(
				'setting'  => 'trending_featured_posts',
				'operator' => '==',
				'value'    => true,
			),
			array(
				'setting'  => 'show_on_front',
				'operator' => '==',
				'value'    => 'page',
			),
		),
	)
);

CSCO_Kirki::add_field(
	'csco_theme_mod', array(
		'type'            => 'text',
		'settings'        => 'trending_featured_posts_filter_categories',
		'label'           => esc_html__( 'Filter by Categories', 'overflow' ),
		'description'     => esc_html__( 'Add comma-separated list of category slugs. For example: &laquo;travel, lifestyle, food&raquo;. Leave empty for all categories.', 'overflow' ),
		'section'         => 'trending_featured_posts',
		'default'         => '',
		'priority'        => 10,
		'active_callback' => array(
			array(
				'setting'  => 'trending_featured_posts',
				'operator' => '==',
				'value'    => true,
			),
		),
	)
);

CSCO_Kirki::add_field(
	'csco_theme_mod', array(
		'type'            => 'text',
		'settings'        => 'trending_featured_posts_filter_tags',
		'label'           => esc_html__( 'Filter by Tags', 'overflow' ),
		'description'     => esc_html__( 'Add comma-separated list of tag slugs. For example: &laquo;worth-reading, top-5, playlists&raquo;. Leave empty for all tags.', 'overflow' ),
		'section'         => 'trending_featured_posts',
		'default'         => '',
		'priority'        => 10,
		'active_callback' => array(
			array(
				'setting'  => 'trending_featured_posts',
				'operator' => '==',
				'value'    => true,
			),
		),
	)
);

CSCO_Kirki::add_field(
	'csco_theme_mod', array(
		'type'            => 'text',
		'settings'        => 'trending_featured_posts_filter_posts',
		'label'           => esc_html__( 'Filter by Posts', 'overflow' ),
		'description'     => esc_html__( 'Add comma-separated list of post IDs. For example: 12, 34, 145. Leave empty for all posts.', 'overflow' ),
		'section'         => 'trending_featured_posts',
		'default'         => '',
		'priority'        => 10,
		'active_callback' => array(
			array(
				'setting'  => 'trending_featured_posts',
				'operator' => '==',
				'value'    => true,
			),
		),
	)
);

if ( class_exists( 'Post_Views_Counter' ) ) {

	CSCO_Kirki::add_field(
		'csco_theme_mod', array(
			'type'            => 'radio',
			'settings'        => 'trending_featured_posts_orderby',
			'label'           => esc_html__( 'Order posts by', 'overflow' ),
			'section'         => 'trending_featured_posts',
			'default'         => 'date',
			'priority'        => 10,
			'choices'         => array(
				'date'       => esc_html__( 'Date', 'overflow' ),
				'post_views' => esc_html__( 'Views', 'overflow' ),
			),
			'active_callback' => array(
				array(
					'setting'  => 'trending_featured_posts',
					'operator' => '==',
					'value'    => true,
				),
			),
		)
	);

	CSCO_Kirki::add_field(
		'csco_theme_mod', array(
			'type'            => 'text',
			'settings'        => 'trending_featured_posts_time_frame',
			'label'           => esc_html__( 'Filter by Time Frame', 'overflow' ),
			'description'     => esc_html__( 'Add period of posts in English. For example: &laquo;2 months&raquo;, &laquo;14 days&raquo; or even &laquo;1 year&raquo;', 'overflow' ),
			'section'         => 'trending_featured_posts',
			'default'         => '',
			'priority'        => 10,
			'active_callback' => array(
				array(
					'setting'  => 'trending_featured_posts',
					'operator' => '==',
					'value'    => true,
				),
				array(
					'setting'  => 'trending_featured_posts_orderby',
					'operator' => '==',
					'value'    => 'post_views',
				),
			),
		)
	);
}

CSCO_Kirki::add_field(
	'csco_theme_mod', array(
		'type'            => 'checkbox',
		'settings'        => 'trending_featured_posts_exclude',
		'label'           => esc_html__( 'Exclude trending posts from the main archive', 'overflow' ),
		'description'     => esc_html__( 'If enabled, posts from the trending section will not be shown in the main archive of your homepage.', 'overflow' ),
		'section'         => 'trending_featured_posts',
		'default'         => false,
		'priority'        => 10,
		'active_callback' => array(
			array(
				'setting'  => 'trending_featured_posts',
				'operator' => '==',
				'value'    => true,
			),
		),
	)
);

CSCO_Kirki::add_field(
	'csco_theme_mod', array(
		'type'            => 'checkbox',
		'settings'        => 'trending_featured_posts_avoid_duplicate',
		'label'           => esc_html__( 'Exclude duplicate posts from the trending posts', 'overflow' ),
		'description'     => esc_html__( 'If enabled, posts from the featured posts section will not be shown in the trending section. The "Filter by Posts" option will override this option.', 'overflow' ),
		'section'         => 'trending_featured_posts',
		'default'         => false,
		'priority'        => 10,
		'active_callback' => array(
			array(
				'setting'  => 'trending_featured_posts',
				'operator' => '==',
				'value'    => true,
			),
		),
	)
);
