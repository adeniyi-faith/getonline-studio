<?php
/**
 * Page Settings
 *
 * @package Overflow
 */

CSCO_Kirki::add_section(
	'page_settings', array(
		'title'    => esc_html__( 'Page Settings', 'overflow' ),
		'priority' => 50,
	)
);

CSCO_Kirki::add_field(
	'csco_theme_mod', array(
		'type'     => 'radio',
		'settings' => 'page_sidebar',
		'label'    => esc_html__( 'Default Sidebar', 'overflow' ),
		'section'  => 'page_settings',
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
		'type'     => 'radio',
		'settings' => 'page_header_type',
		'label'    => esc_html__( 'Page Header Type', 'overflow' ),
		'section'  => 'page_settings',
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
		'settings'        => 'page_media_preview',
		'label'           => esc_html__( 'Standard Page Header Preview', 'overflow' ),
		'section'         => 'page_settings',
		'default'         => 'cropped',
		'priority'        => 10,
		'choices'         => array(
			'cropped'   => esc_attr__( 'Display Cropped Image', 'overflow' ),
			'uncropped' => esc_attr__( 'Display Preview in Original Ratio', 'overflow' ),
		),
		'active_callback' => array(
			array(
				'setting'  => 'page_header_type',
				'operator' => '==',
				'value'    => 'standard',
			),
		),
	)
);

CSCO_Kirki::add_field(
	'csco_theme_mod', array(
		'type'     => 'checkbox',
		'settings' => 'page_excerpt',
		'label'    => esc_html__( 'Display excerpts', 'overflow' ),
		'section'  => 'page_settings',
		'default'  => true,
		'priority' => 10,
	)
);

CSCO_Kirki::add_field(
	'csco_theme_mod', array(
		'type'     => 'checkbox',
		'settings' => 'page_comments_simple',
		'label'    => esc_html__( 'Display comments without the View Comments button', 'overflow' ),
		'section'  => 'page_settings',
		'default'  => false,
		'priority' => 10,
	)
);
