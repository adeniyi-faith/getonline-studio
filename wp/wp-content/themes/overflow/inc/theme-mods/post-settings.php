<?php
/**
 * Post Settings
 *
 * @package Overflow
 */

CSCO_Kirki::add_section(
	'post_settings', array(
		'title'    => esc_html__( 'Post Settings', 'overflow' ),
		'priority' => 50,
	)
);

CSCO_Kirki::add_field(
	'csco_theme_mod', array(
		'type'     => 'radio',
		'settings' => 'post_sidebar',
		'label'    => esc_html__( 'Default Sidebar', 'overflow' ),
		'section'  => 'post_settings',
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
		'settings' => 'post_meta',
		'label'    => esc_attr__( 'Post Meta', 'overflow' ),
		'section'  => 'post_settings',
		'default'  => array( 'category', 'date', 'author', 'shares', 'views', 'reading_time' ),
		'priority' => 10,
		'choices'  => apply_filters( 'csco_post_meta_choices', array(
			'category'     => esc_html__( 'Category', 'overflow' ),
			'date'         => esc_html__( 'Date', 'overflow' ),
			'author'       => esc_html__( 'Author', 'overflow' ),
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
		'settings' => 'post_header_type',
		'label'    => esc_html__( 'Default Page Header Type', 'overflow' ),
		'section'  => 'post_settings',
		'default'  => 'standard',
		'priority' => 10,
		'choices'  => array(
			'standard' => esc_attr__( 'Standard', 'overflow' ),
			'large'    => esc_attr__( 'Large', 'overflow' ),
			'title'    => esc_attr__( 'Page Title Only', 'overflow' ),
			'none'     => esc_attr__( 'None', 'overflow' ),
		),
	)
);

CSCO_Kirki::add_field(
	'csco_theme_mod', array(
		'type'            => 'radio',
		'settings'        => 'post_media_preview',
		'label'           => esc_html__( 'Standard Page Header Preview', 'overflow' ),
		'section'         => 'post_settings',
		'default'         => 'cropped',
		'priority'        => 10,
		'choices'         => array(
			'cropped'   => esc_attr__( 'Display Cropped Image', 'overflow' ),
			'uncropped' => esc_attr__( 'Display Preview in Original Ratio', 'overflow' ),
		),
		'active_callback' => array(
			array(
				'setting'  => 'post_header_type',
				'operator' => '==',
				'value'    => 'standard',
			),
		),
	)
);

if ( csco_powerkit_module_enabled( 'opt_in_forms' ) ) {
	CSCO_Kirki::add_field(
		'csco_theme_mod', array(
			'type'     => 'checkbox',
			'settings' => 'post_subscribe',
			'label'    => esc_html__( 'Display subscribe section', 'overflow' ),
			'section'  => 'post_settings',
			'default'  => false,
			'priority' => 10,
		)
	);

	CSCO_Kirki::add_field(
		'csco_theme_mod', array(
			'type'            => 'checkbox',
			'settings'        => 'post_subscribe_name',
			'label'           => esc_html__( 'Display first name field', 'overflow' ),
			'section'         => 'post_settings',
			'default'         => false,
			'priority'        => 10,
			'active_callback' => array(
				array(
					'setting'  => 'post_subscribe',
					'operator' => '==',
					'value'    => true,
				),
			),
		)
	);

	CSCO_Kirki::add_field(
		'csco_theme_mod', array(
			'type'            => 'text',
			'settings'        => 'post_subscribe_title',
			'label'           => esc_html__( 'Title', 'overflow' ),
			'section'         => 'post_settings',
			'default'         => esc_html__( 'Subscribe to Our Newsletter', 'overflow' ),
			'priority'        => 10,
			'active_callback' => array(
				array(
					'setting'  => 'post_subscribe',
					'operator' => '==',
					'value'    => true,
				),
			),
		)
	);
}

CSCO_Kirki::add_field(
	'csco_theme_mod', array(
		'type'     => 'checkbox',
		'settings' => 'post_tags',
		'label'    => esc_html__( 'Display tags', 'overflow' ),
		'section'  => 'post_settings',
		'default'  => true,
		'priority' => 10,
	)
);

CSCO_Kirki::add_field(
	'csco_theme_mod', array(
		'type'     => 'checkbox',
		'settings' => 'post_excerpt',
		'label'    => esc_html__( 'Display excerpts', 'overflow' ),
		'section'  => 'post_settings',
		'default'  => true,
		'priority' => 10,
	)
);

CSCO_Kirki::add_field(
	'csco_theme_mod', array(
		'type'     => 'checkbox',
		'settings' => 'post_comments_simple',
		'label'    => esc_html__( 'Display comments without the View Comments button', 'overflow' ),
		'section'  => 'post_settings',
		'default'  => false,
		'priority' => 10,
	)
);

CSCO_Kirki::add_field(
	'csco_theme_mod', array(
		'type'     => 'checkbox',
		'settings' => 'related',
		'label'    => esc_html__( 'Display related section', 'overflow' ),
		'section'  => 'post_settings',
		'default'  => true,
		'priority' => 10,
	)
);

CSCO_Kirki::add_field(
	'csco_theme_mod', array(
		'type'            => 'radio',
		'settings'        => 'related_layout',
		'label'           => esc_html__( 'Related Post Layout', 'overflow' ),
		'section'         => 'post_settings',
		'default'         => 'list',
		'priority'        => 10,
		'choices'         => array(
			'list' => esc_html__( 'List', 'overflow' ),
			'grid' => esc_html__( 'Grid', 'overflow' ),
		),
		'active_callback' => array(
			array(
				'setting'  => 'related',
				'operator' => '==',
				'value'    => true,
			),
		),
	)
);

CSCO_Kirki::add_field(
	'csco_theme_mod', array(
		'type'            => 'number',
		'settings'        => 'related_number',
		'label'           => esc_html__( 'Maximum Number of Related Posts', 'overflow' ),
		'section'         => 'post_settings',
		'default'         => 6,
		'priority'        => 10,
		'active_callback' => array(
			array(
				'setting'  => 'related',
				'operator' => '==',
				'value'    => true,
			),
		),
	)
);

CSCO_Kirki::add_field(
	'csco_theme_mod', array(
		'type'            => 'text',
		'settings'        => 'related_time_frame',
		'label'           => esc_html__( 'Time Frame', 'overflow' ),
		'description'     => esc_html__( 'Add period of posts in English. For example: &laquo;2 months&raquo;, &laquo;14 days&raquo; or even &laquo;1 year&raquo;', 'overflow' ),
		'section'         => 'post_settings',
		'default'         => '',
		'priority'        => 10,
		'active_callback' => array(
			array(
				'setting'  => 'related',
				'operator' => '==',
				'value'    => true,
			),
		),
	)
);

CSCO_Kirki::add_field(
	'csco_theme_mod', array(
		'type'     => 'checkbox',
		'settings' => 'post_load_nextpost',
		'label'    => esc_html__( 'Enable the Auto Load Next Post feature', 'overflow' ),
		'section'  => 'post_settings',
		'default'  => false,
		'priority' => 10,
	)
);

CSCO_Kirki::add_field(
	'csco_theme_mod', array(
		'type'            => 'checkbox',
		'settings'        => 'post_load_nextpost_same_category',
		'label'           => esc_html__( 'Auto load posts from the same category only', 'overflow' ),
		'section'         => 'post_settings',
		'default'         => false,
		'priority'        => 10,
		'active_callback' => array(
			array(
				'setting'  => 'post_load_nextpost',
				'operator' => '==',
				'value'    => true,
			),
		),
	)
);

CSCO_Kirki::add_field(
	'csco_theme_mod', array(
		'type'            => 'checkbox',
		'settings'        => 'post_load_nextpost_reverse',
		'label'           => esc_html__( 'Auto load previous posts instead of next ones', 'overflow' ),
		'section'         => 'post_settings',
		'default'         => false,
		'priority'        => 10,
		'active_callback' => array(
			array(
				'setting'  => 'post_load_nextpost',
				'operator' => '==',
				'value'    => true,
			),
		),
	)
);
