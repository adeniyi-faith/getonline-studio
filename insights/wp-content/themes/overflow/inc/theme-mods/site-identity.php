<?php
/**
 * Site Identity
 *
 * @package Overflow
 */

CSCO_Kirki::add_field(
	'csco_theme_mod', array(
		'type'        => 'image',
		'settings'    => 'logo',
		'label'       => esc_html__( 'Main Logo', 'overflow' ),
		'description' => esc_html__( 'The main logo is used in the navigation bar and mobile view of your website. Logo image will be displayed in its original image dimensions. Please upload the 2x version of your logo via Media Library with ', 'overflow' ) . '<code>@2x</code>' . esc_html__( ' suffix for supporting Retina screens. For example ', 'overflow' ) . '<code>logo@2x.png</code>' . esc_html__( '. Recommended maximum height is 40px (80px for Retina version).', 'overflow' ),
		'section'     => 'title_tagline',
		'default'     => '',
		'priority'    => 0,
		'choices'     => array(
			'save_as' => 'id',
		),
	)
);

CSCO_Kirki::add_field(
	'csco_theme_mod', array(
		'type'            => 'image',
		'settings'        => 'large_logo',
		'label'           => esc_html__( 'Large Logo', 'overflow' ),
		'description'     => esc_html__( 'The large logo is used in the site header in desktop view. Similar to the main logo, upload the 2x version of your logo via Media Library with ', 'overflow' ) . '<code>@2x</code>' . esc_html__( ' suffix for supporting Retina screens. For example ', 'overflow' ) . '<code>logo-large@2x.png</code>' . esc_html__( '. Recommended maximum height is 80px (160px for Retina version).', 'overflow' ),
		'section'         => 'title_tagline',
		'default'         => '',
		'priority'        => 0,
		'active_callback' => array(
			array(
				'setting'  => 'header_layout',
				'operator' => '==',
				'value'    => 'large',
			),
		),
		'choices'         => array(
			'save_as' => 'id',
		),
	)
);

CSCO_Kirki::add_field(
	'csco_theme_mod', array(
		'type'        => 'image',
		'settings'    => 'footer_logo',
		'label'       => esc_html__( 'Footer Logo', 'overflow' ),
		'description' => esc_html__( 'The footer logo is used in the site footer in desktop and mobile view. Similar to the main logo, upload the 2x version of your logo via Media Library with ', 'overflow' ) . '<code>@2x</code>' . esc_html__( ' suffix for supporting Retina screens. For example ', 'overflow' ) . '<code>logo-footer@2x.png</code>' . esc_html__( '. Recommended maximum height is 80px (160px for Retina version).', 'overflow' ),
		'section'     => 'title_tagline',
		'default'     => '',
		'priority'    => 0,
		'choices'     => array(
			'save_as' => 'id',
		),
	)
);
