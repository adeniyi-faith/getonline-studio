<?php
/**
 * Footer Settings
 *
 * @package Overflow
 */

CSCO_Kirki::add_section(
	'footer', array(
		'title'    => esc_html__( 'Footer Settings', 'overflow' ),
		'priority' => 40,
	)
);

if ( csco_powerkit_module_enabled( 'opt_in_forms' ) ) {
	CSCO_Kirki::add_field(
		'csco_theme_mod', array(
			'type'     => 'checkbox',
			'settings' => 'footer_subscribe',
			'label'    => esc_html__( 'Display subscribe section', 'overflow' ),
			'section'  => 'footer',
			'default'  => false,
			'priority' => 10,
		)
	);

	CSCO_Kirki::add_field(
		'csco_theme_mod', array(
			'type'            => 'checkbox',
			'settings'        => 'footer_subscribe_name',
			'label'           => esc_html__( 'Display first name field', 'overflow' ),
			'section'         => 'footer',
			'default'         => false,
			'priority'        => 10,
			'active_callback' => array(
				array(
					'setting'  => 'footer_subscribe',
					'operator' => '==',
					'value'    => true,
				),
			),
		)
	);

	CSCO_Kirki::add_field(
		'csco_theme_mod', array(
			'type'              => 'text',
			'settings'          => 'footer_subscribe_title',
			'label'             => esc_html__( 'Title', 'overflow' ),
			'section'           => 'footer',
			'default'           => esc_html__( 'Subscribe to Our Newsletter', 'overflow' ),
			'priority'          => 10,
			'sanitize_callback' => 'wp_kses_post',
			'active_callback'   => array(
				array(
					'setting'  => 'footer_subscribe',
					'operator' => '==',
					'value'    => true,
				),
			),
		)
	);
}

if ( csco_powerkit_module_enabled( 'instagram_integration' ) ) {
	CSCO_Kirki::add_field(
		'csco_theme_mod', array(
			'type'     => 'text',
			'settings' => 'footer_instagram_username',
			'label'    => esc_html__( 'Instagram Username', 'overflow' ),
			'section'  => 'footer',
			'default'  => '',
			'priority' => 10,
		)
	);
}

if ( csco_powerkit_module_enabled( 'social_links' ) ) {
	CSCO_Kirki::add_field(
		'csco_theme_mod', array(
			'type'     => 'checkbox',
			'settings' => 'footer_social_links',
			'label'    => esc_html__( 'Display social links', 'overflow' ),
			'section'  => 'footer',
			'default'  => false,
			'priority' => 10,
		)
	);

	CSCO_Kirki::add_field(
		'csco_theme_mod', array(
			'type'            => 'select',
			'settings'        => 'footer_social_links_scheme',
			'label'           => esc_html__( 'Color scheme', 'overflow' ),
			'section'         => 'footer',
			'default'         => 'light',
			'priority'        => 10,
			'choices'         => array(
				'light'         => esc_html__( 'Light', 'overflow' ),
				'bold'          => esc_html__( 'Bold', 'overflow' ),
				'light-rounded' => esc_html__( 'Light Rounded', 'overflow' ),
				'bold-rounded'  => esc_html__( 'Bold Rounded', 'overflow' ),
			),
			'active_callback' => array(
				array(
					'setting'  => 'footer_social_links',
					'operator' => '==',
					'value'    => true,
				),
			),
		)
	);

	CSCO_Kirki::add_field(
		'csco_theme_mod', array(
			'type'            => 'number',
			'settings'        => 'footer_social_links_maximum',
			'label'           => esc_html__( 'Maximum Number of Social Links', 'overflow' ),
			'section'         => 'footer',
			'default'         => 4,
			'priority'        => 10,
			'active_callback' => array(
				array(
					'setting'  => 'footer_social_links',
					'operator' => '==',
					'value'    => true,
				),
			),
		)
	);

	CSCO_Kirki::add_field(
		'csco_theme_mod', array(
			'type'            => 'checkbox',
			'settings'        => 'footer_social_links_counts',
			'label'           => esc_html__( 'Display counts', 'overflow' ),
			'section'         => 'footer',
			'default'         => true,
			'priority'        => 10,
			'active_callback' => array(
				array(
					'setting'  => 'footer_social_links',
					'operator' => '==',
					'value'    => true,
				),
			),
		)
	);
}

CSCO_Kirki::add_field(
	'csco_theme_mod', array(
		'type'              => 'textarea',
		'settings'          => 'footer_text',
		'label'             => esc_attr__( 'Footer Text', 'overflow' ),
		'section'           => 'footer',
		/* translators: %s: Author name. */
		'default'           => sprintf( esc_html__( 'Designed & Developed by %s', 'overflow' ), '<a href="' . esc_url( csco_get_theme_data( 'AuthorURI' ) ) . '">Code Supply Co.</a>' ),
		'priority'          => 10,
		'sanitize_callback' => 'wp_kses_post',
	)
);

CSCO_Kirki::add_field(
	'csco_theme_mod', array(
		'type'     => 'checkbox',
		'settings' => 'footer_featured_posts',
		'label'    => esc_html__( 'Display featured posts', 'overflow' ),
		'section'  => 'footer',
		'default'  => false,
		'priority' => 10,
	)
);

CSCO_Kirki::add_field(
	'csco_theme_mod', array(
		'type'            => 'multicheck',
		'settings'        => 'footer_featured_posts_meta',
		'label'           => esc_attr__( 'Post Meta', 'overflow' ),
		'section'         => 'footer',
		'default'         => array( 'category', 'author', 'date', 'views', 'reading_time' ),
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
				'setting'  => 'footer_featured_posts',
				'operator' => '==',
				'value'    => true,
			),
		),
	)
);

CSCO_Kirki::add_field(
	'csco_theme_mod', array(
		'type'            => 'text',
		'settings'        => 'footer_featured_posts_filter_categories',
		'label'           => esc_html__( 'Filter by Categories', 'overflow' ),
		'description'     => esc_html__( 'Add comma-separated list of category slugs. For example: &laquo;travel, lifestyle, food&raquo;. Leave empty for all categories.', 'overflow' ),
		'section'         => 'footer',
		'default'         => '',
		'priority'        => 10,
		'active_callback' => array(
			array(
				'setting'  => 'footer_featured_posts',
				'operator' => '==',
				'value'    => true,
			),
		),
	)
);

CSCO_Kirki::add_field(
	'csco_theme_mod', array(
		'type'            => 'text',
		'settings'        => 'footer_featured_posts_filter_tags',
		'label'           => esc_html__( 'Filter by Tags', 'overflow' ),
		'description'     => esc_html__( 'Add comma-separated list of tag slugs. For example: &laquo;worth-reading, top-5, playlists&raquo;. Leave empty for all tags.', 'overflow' ),
		'section'         => 'footer',
		'default'         => '',
		'priority'        => 10,
		'active_callback' => array(
			array(
				'setting'  => 'footer_featured_posts',
				'operator' => '==',
				'value'    => true,
			),
		),
	)
);

CSCO_Kirki::add_field(
	'csco_theme_mod', array(
		'type'            => 'text',
		'settings'        => 'footer_featured_posts_filter_posts',
		'label'           => esc_html__( 'Filter by Posts', 'overflow' ),
		'description'     => esc_html__( 'Add comma-separated list of post IDs. For example: 12, 34, 145. Leave empty for all posts.', 'overflow' ),
		'section'         => 'footer',
		'default'         => '',
		'priority'        => 10,
		'active_callback' => array(
			array(
				'setting'  => 'footer_featured_posts',
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
			'settings'        => 'footer_featured_posts_orderby',
			'label'           => esc_html__( 'Order posts by', 'overflow' ),
			'section'         => 'footer',
			'default'         => 'date',
			'priority'        => 10,
			'choices'         => array(
				'date'       => esc_html__( 'Date', 'overflow' ),
				'post_views' => esc_html__( 'Views', 'overflow' ),
			),
			'active_callback' => array(
				array(
					'setting'  => 'footer_featured_posts',
					'operator' => '==',
					'value'    => true,
				),
			),
		)
	);

	CSCO_Kirki::add_field(
		'csco_theme_mod', array(
			'type'            => 'text',
			'settings'        => 'footer_featured_posts_time_frame',
			'label'           => esc_html__( 'Filter by Time Frame', 'overflow' ),
			'description'     => esc_html__( 'Add period of posts in English. For example: &laquo;2 months&raquo;, &laquo;14 days&raquo; or even &laquo;1 year&raquo;', 'overflow' ),
			'section'         => 'footer',
			'default'         => '',
			'priority'        => 10,
			'active_callback' => array(
				array(
					'setting'  => 'footer_featured_posts',
					'operator' => '==',
					'value'    => true,
				),
				array(
					'setting'  => 'footer_featured_posts_orderby',
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
		'settings'        => 'footer_featured_posts_avoid_duplicate',
		'label'           => esc_html__( 'Exclude duplicate posts from the footer posts', 'overflow' ),
		'description'     => esc_html__( 'If enabled, posts from the featured, trending posts sections and post archive will not be shown in the footer section. The "Filter by Posts" option will override this option.', 'overflow' ),
		'section'         => 'footer',
		'default'         => false,
		'priority'        => 10,
		'active_callback' => array(
			array(
				'setting'  => 'footer_featured_posts',
				'operator' => '==',
				'value'    => true,
			),
		),
	)
);
