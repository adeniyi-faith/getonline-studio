<?php
/**
 * Miscellaneous Settings
 *
 * @package Overflow
 */

CSCO_Kirki::add_section(
	'miscellaneous', array(
		'title'    => esc_html__( 'Miscellaneous Settings', 'overflow' ),
		'priority' => 60,
	)
);

CSCO_Kirki::add_field(
	'csco_theme_mod', array(
		'type'     => 'checkbox',
		'settings' => 'misc_published_date',
		'label'    => esc_html__( 'Display published date instead of modified date', 'overflow' ),
		'section'  => 'miscellaneous',
		'default'  => true,
		'priority' => 10,
	)
);

CSCO_Kirki::add_field(
	'csco_theme_mod', array(
		'type'     => 'text',
		'settings' => 'misc_search_placeholder',
		'label'    => esc_html__( 'Search Form Placeholder', 'overflow' ),
		'section'  => 'miscellaneous',
		'default'  => esc_html__( 'Enter your search topic', 'overflow' ),
		'priority' => 10,
	)
);

CSCO_Kirki::add_field(
	'csco_theme_mod', array(
		'type'     => 'text',
		'settings' => 'misc_label_readmore',
		'label'    => esc_html__( '"Read More" Button Label', 'overflow' ),
		'section'  => 'miscellaneous',
		'default'  => esc_html__( 'Read More', 'overflow' ),
		'priority' => 10,
	)
);

CSCO_Kirki::add_field(
	'csco_theme_mod', array(
		'type'     => 'radio',
		'settings' => 'misc_classic_gallery_alignment',
		'label'    => esc_html__( 'Alignment of Galleries in Classic Block', 'overflow' ),
		'section'  => 'miscellaneous',
		'default'  => 'default',
		'priority' => 10,
		'choices'  => array(
			'default' => esc_html__( 'Default', 'overflow' ),
			'wide'    => esc_html__( 'Wide', 'overflow' ),
			'large'   => esc_html__( 'Large', 'overflow' ),
		),
	)
);

CSCO_Kirki::add_field(
	'csco_theme_mod', array(
		'type'     => 'checkbox',
		'settings' => 'misc_sticky_sidebar',
		'label'    => esc_html__( 'Sticky Sidebar', 'overflow' ),
		'section'  => 'miscellaneous',
		'default'  => true,
		'priority' => 10,
	)
);

CSCO_Kirki::add_field(
	'csco_theme_mod', array(
		'type'            => 'radio',
		'settings'        => 'misc_sticky_sidebar_method',
		'label'           => esc_html__( 'Sticky Method', 'overflow' ),
		'section'         => 'miscellaneous',
		'default'         => 'stick-to-top',
		'priority'        => 10,
		'choices'         => array(
			'stick-to-top'    => esc_html__( 'Sidebar top edge', 'overflow' ),
			'stick-to-bottom' => esc_html__( 'Sidebar bottom edge', 'overflow' ),
			'stick-last'      => esc_html__( 'Last widget top edge', 'overflow' ),
		),
		'active_callback' => array(
			array(
				'setting'  => 'misc_sticky_sidebar',
				'operator' => '==',
				'value'    => true,
			),
		),
	)
);

CSCO_Kirki::add_field(
	'csco_theme_mod', array(
		'type'        => 'radio',
		'settings'    => 'webfonts_load_method',
		'label'       => esc_html__( 'Webfonts Load Method', 'overflow' ),
		'description' => esc_html__( 'Please', 'overflow' ) . ' <a href="' . add_query_arg( array( 'action' => 'kirki-reset-cache' ), get_site_url() ) . '" target="_blank">' . esc_html__( 'reset font cache', 'overflow' ) . '</a> ' . esc_html__( 'after saving.', 'overflow' ),
		'section'     => 'miscellaneous',
		'default'     => 'async',
		'priority'    => 10,
		'choices'     => array(
			'async' => esc_html__( 'Asynchronous', 'overflow' ),
			'link'  => esc_html__( 'Render-Blocking', 'overflow' ),
		),
	)
);
