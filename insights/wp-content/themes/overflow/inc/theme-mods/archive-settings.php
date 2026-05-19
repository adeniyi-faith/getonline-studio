<?php
/**
 * Archive Settings
 *
 * @package Overflow
 */

CSCO_Kirki::add_section(
	'archive_settings', array(
		'title'    => esc_html__( 'Archive Settings', 'overflow' ),
		'priority' => 50,
	)
);

CSCO_Kirki::add_field(
	'csco_theme_mod', array(
		'type'     => 'radio',
		'settings' => 'archive_layout',
		'label'    => esc_html__( 'Layout', 'overflow' ),
		'section'  => 'archive_settings',
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
		'settings' => 'archive_sidebar',
		'label'    => esc_html__( 'Sidebar', 'overflow' ),
		'section'  => 'archive_settings',
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
		'settings' => 'archive_post_meta',
		'label'    => esc_attr__( 'Post Meta', 'overflow' ),
		'section'  => 'archive_settings',
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
		'settings' => 'archive_media_preview',
		'label'    => esc_html__( 'Full Post Preview', 'overflow' ),
		'section'  => 'archive_settings',
		'default'  => 'cropped',
		'priority' => 10,
		'choices'  => array(
			'cropped'   => esc_attr__( 'Display Cropped Image', 'overflow' ),
			'uncropped' => esc_attr__( 'Display Preview in Original Ratio', 'overflow' ),
		),
		'active_callback' => array(
			array(
				array(
					'setting'  => 'archive_layout',
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
		'settings'        => 'archive_summary',
		'label'           => esc_html__( 'Full Post Summary', 'overflow' ),
		'section'         => 'archive_settings',
		'default'         => 'excerpt',
		'priority'        => 10,
		'choices'         => array(
			'excerpt' => esc_html__( 'Use Excerpts', 'overflow' ),
			'content' => esc_html__( 'Use Read More Tag', 'overflow' ),
		),
		'active_callback' => array(
			array(
				array(
					'setting'  => 'archive_layout',
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
		'settings' => 'archive_more_button',
		'label'    => esc_html__( 'Display Read More button', 'overflow' ),
		'section'  => 'archive_settings',
		'default'  => true,
		'priority' => 10,
	)
);

CSCO_Kirki::add_field(
	'csco_theme_mod', array(
		'type'     => 'radio',
		'settings' => 'archive_pagination_type',
		'label'    => esc_html__( 'Pagination', 'overflow' ),
		'section'  => 'archive_settings',
		'default'  => 'load-more',
		'priority' => 10,
		'choices'  => array(
			'standard'  => esc_html__( 'Standard', 'overflow' ),
			'load-more' => esc_html__( 'Load More Button', 'overflow' ),
			'infinite'  => esc_html__( 'Infinite Load', 'overflow' ),
		),
	)
);
