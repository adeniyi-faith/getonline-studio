<?php
/**
 * Header Settings
 *
 * @package Overflow
 */

CSCO_Kirki::add_section(
	'header', array(
		'title'    => esc_html__( 'Header Settings', 'overflow' ),
		'priority' => 40,
	)
);

CSCO_Kirki::add_field(
	'csco_theme_mod', array(
		'type'     => 'radio',
		'settings' => 'header_layout',
		'label'    => esc_html__( 'Header Layout', 'overflow' ),
		'section'  => 'header',
		'default'  => 'large',
		'priority' => 10,
		'choices'  => array(
			'default' => esc_html__( 'Compact', 'overflow' ),
			'large'   => esc_html__( 'Large (Default)', 'overflow' ),
		),
	)
);

CSCO_Kirki::add_field(
	'csco_theme_mod', array(
		'type'            => 'checkbox',
		'settings'        => 'header_tagline',
		'label'           => esc_html__( 'Display tagline', 'overflow' ),
		'section'         => 'header',
		'default'         => false,
		'priority'        => 10,
		'active_callback' => array(
			array(
				'setting'  => 'header_layout',
				'operator' => '==',
				'value'    => 'large',
			),
		),
	)
);

CSCO_Kirki::add_field(
	'csco_theme_mod', array(
		'type'            => 'dimension',
		'settings'        => 'header_height',
		'label'           => esc_html__( 'Header Height', 'overflow' ),
		'section'         => 'header',
		'default'         => 'auto',
		'priority'        => 10,
		'output'          => array(
			array(
				'element'  => '.navbar-topbar .navbar-wrap',
				'property' => 'min-height',
			),
		),
		'active_callback' => array(
			array(
				'setting'  => 'header_layout',
				'operator' => '==',
				'value'    => 'large',
			),
		),
	)
);

CSCO_Kirki::add_field(
	'csco_theme_mod', array(
		'type'     => 'dimension',
		'settings' => 'header_nav_height',
		'label'    => esc_html__( 'Navigation Bar Height', 'overflow' ),
		'section'  => 'header',
		'default'  => '60px',
		'priority' => 10,
		'output'   => array(
			array(
				'element'  => '.navbar-primary .navbar-wrap, .navbar-primary .navbar-content',
				'property' => 'height',
			),
			array(
				'element'       => '.offcanvas-header',
				'property'      => 'flex',
				'value_pattern' => '0 0 $',
			),
			array(
				'element'       => '.post-sidebar-shares',
				'property'      => 'top',
				'value_pattern' => 'calc( $ + 20px )',
			),
			array(
				'element'       => '.admin-bar .post-sidebar-shares',
				'property'      => 'top',
				'value_pattern' => 'calc( $ + 52px )',
			),
			array(
				'element'       => '.header-large .post-sidebar-shares',
				'property'      => 'top',
				'value_pattern' => 'calc( $ * 2 + 52px )',
			),
			array(
				'element'       => '.header-large.admin-bar .post-sidebar-shares',
				'property'      => 'top',
				'value_pattern' => 'calc( $ * 2 + 52px )',
			),
		),
	)
);


CSCO_Kirki::add_field(
	'csco_theme_mod', array(
		'type'        => 'checkbox',
		'settings'    => 'navbar_sticky',
		'label'       => esc_html__( 'Make navigation bar sticky', 'overflow' ),
		'description' => esc_html__( 'Enabling this option will make navigation bar visible when scrolling.', 'overflow' ),
		'section'     => 'header',
		'default'     => true,
		'priority'    => 10,
	)
);

CSCO_Kirki::add_field(
	'csco_theme_mod', array(
		'type'            => 'checkbox',
		'settings'        => 'effects_navbar_scroll',
		'label'           => esc_html__( 'Enable the smart sticky feature', 'overflow' ),
		'description'     => esc_html__( 'Enabling this option will reveal navigation bar when scrolling up and hide it when scrolling down.', 'overflow' ),
		'section'         => 'header',
		'default'         => true,
		'priority'        => 10,
		'active_callback' => array(
			array(
				'setting'  => 'navbar_sticky',
				'operator' => '==',
				'value'    => true,
			),
		),
	)
);

CSCO_Kirki::add_field(
	'csco_theme_mod', array(
		'type'     => 'checkbox',
		'settings' => 'header_offcanvas',
		'label'    => esc_html__( 'Display offcanvas toggle button', 'overflow' ),
		'section'  => 'header',
		'default'  => true,
		'priority' => 10,
	)
);

CSCO_Kirki::add_field(
	'csco_theme_mod', array(
		'type'     => 'checkbox',
		'settings' => 'header_search_button',
		'label'    => esc_html__( 'Display search button', 'overflow' ),
		'section'  => 'header',
		'default'  => true,
		'priority' => 10,
	)
);

if ( csco_powerkit_module_enabled( 'social_links' ) ) {
	CSCO_Kirki::add_field(
		'csco_theme_mod', array(
			'type'     => 'checkbox',
			'settings' => 'header_social_links',
			'label'    => esc_html__( 'Display social links', 'overflow' ),
			'section'  => 'header',
			'default'  => false,
			'priority' => 10,
		)
	);

	CSCO_Kirki::add_field(
		'csco_theme_mod', array(
			'type'            => 'select',
			'settings'        => 'header_social_links_scheme',
			'label'           => esc_html__( 'Color scheme', 'overflow' ),
			'section'         => 'header',
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
					'setting'  => 'header_social_links',
					'operator' => '==',
					'value'    => true,
				),
			),
		)
	);

	CSCO_Kirki::add_field(
		'csco_theme_mod', array(
			'type'            => 'number',
			'settings'        => 'header_social_links_maximum',
			'label'           => esc_html__( 'Maximum Number of Social Links', 'overflow' ),
			'section'         => 'header',
			'default'         => 3,
			'priority'        => 10,
			'active_callback' => array(
				array(
					'setting'  => 'header_social_links',
					'operator' => '==',
					'value'    => true,
				),
			),
		)
	);

	CSCO_Kirki::add_field(
		'csco_theme_mod', array(
			'type'            => 'checkbox',
			'settings'        => 'header_social_links_counts',
			'label'           => esc_html__( 'Display social counts', 'overflow' ),
			'section'         => 'header',
			'default'         => true,
			'priority'        => 10,
			'active_callback' => array(
				array(
					'setting'  => 'header_social_links',
					'operator' => '==',
					'value'    => true,
				),
			),
		)
	);
}

CSCO_Kirki::add_field(
	'csco_theme_mod', array(
		'type'            => 'radio',
		'settings'        => 'header_follow',
		'label'           => esc_html__( 'Right column', 'overflow' ),
		'description'     => csco_powerkit_module_enabled( 'social_follow' ) ? '<a target="_self" href="' . admin_url( '/options-general.php?page=powerkit_social_follow&action=powerkit_reset_cache' ) . '" role="button">' . esc_attr__( 'Clear cache', 'overflow' ) . '</a>' : null,
		'section'         => 'header',
		'default'         => 'none',
		'priority'        => 10,
		'choices'         => csco_header_follow_choices(),
		'active_callback' => array(
			array(
				'setting'  => 'header_layout',
				'operator' => '==',
				'value'    => 'large',
			),
		),
	)
);

CSCO_Kirki::add_field(
	'csco_theme_mod', array(
		'type'              => 'text',
		'settings'          => 'header_follow_button_title',
		'label'             => esc_attr__( 'Title', 'overflow' ),
		'section'           => 'header',
		'default'           => esc_attr__( 'Subscribe to Newsletter', 'overflow' ),
		'priority'          => 10,
		'sanitize_callback' => 'wp_kses_post',
		'active_callback'   => array(
			array(
				'setting'  => 'header_layout',
				'operator' => '==',
				'value'    => 'large',
			),
			array(
				'setting'  => 'header_follow',
				'operator' => '==',
				'value'    => 'button',
			),
		),
	)
);

CSCO_Kirki::add_field(
	'csco_theme_mod', array(
		'type'              => 'textarea',
		'settings'          => 'header_follow_button_description',
		'label'             => esc_attr__( 'Description', 'overflow' ),
		'section'           => 'header',
		'default'           => esc_attr__( 'Get notified of the best deals on our WordPress themes.', 'overflow' ),
		'priority'          => 10,
		'sanitize_callback' => 'wp_kses_post',
		'active_callback'   => array(
			array(
				'setting'  => 'header_layout',
				'operator' => '==',
				'value'    => 'large',
			),
			array(
				'setting'  => 'header_follow',
				'operator' => '==',
				'value'    => 'button',
			),
		),
	)
);

CSCO_Kirki::add_field(
	'csco_theme_mod', array(
		'type'              => 'text',
		'settings'          => 'header_follow_button_label',
		'label'             => esc_attr__( 'Label', 'overflow' ),
		'section'           => 'header',
		'default'           => esc_attr__( 'Subscribe', 'overflow' ),
		'priority'          => 10,
		'sanitize_callback' => 'wp_kses_post',
		'active_callback'   => array(
			array(
				'setting'  => 'header_layout',
				'operator' => '==',
				'value'    => 'large',
			),
			array(
				'setting'  => 'header_follow',
				'operator' => '==',
				'value'    => 'button',
			),
		),
	)
);


CSCO_Kirki::add_field(
	'csco_theme_mod', array(
		'type'            => 'text',
		'settings'        => 'header_follow_button_link',
		'label'           => esc_attr__( 'Link', 'overflow' ),
		'section'         => 'header',
		'default'         => '',
		'priority'        => 10,
		'active_callback' => array(
			array(
				'setting'  => 'header_layout',
				'operator' => '==',
				'value'    => 'large',
			),
			array(
				'setting'  => 'header_follow',
				'operator' => '==',
				'value'    => 'button',
			),
		),
	)
);

if ( csco_powerkit_module_enabled( 'social_follow' ) ) {
	CSCO_Kirki::add_field(
		'csco_theme_mod', array(
			'type'              => 'text',
			'settings'          => 'header_follow_instagram_title',
			'label'             => esc_attr__( 'Title', 'overflow' ),
			'section'           => 'header',
			'default'           => 'Follow me on Instagram',
			'priority'          => 10,
			'sanitize_callback' => 'wp_kses_post',
			'active_callback'   => array(
				array(
					'setting'  => 'header_layout',
					'operator' => '==',
					'value'    => 'large',
				),
				array(
					'setting'  => 'header_follow',
					'operator' => '==',
					'value'    => 'instagram',
				),
			),
		)
	);

	CSCO_Kirki::add_field(
		'csco_theme_mod', array(
			'type'            => 'text',
			'settings'        => 'header_follow_instagram_user',
			'label'           => esc_attr__( 'User ID', 'overflow' ),
			'section'         => 'header',
			'default'         => '',
			'priority'        => 10,
			'active_callback' => array(
				array(
					'setting'  => 'header_layout',
					'operator' => '==',
					'value'    => 'large',
				),
				array(
					'setting'  => 'header_follow',
					'operator' => '==',
					'value'    => 'instagram',
				),
			),
		)
	);

	CSCO_Kirki::add_field(
		'csco_theme_mod', array(
			'type'              => 'text',
			'settings'          => 'header_follow_youtube_title',
			'label'             => esc_attr__( 'Title', 'overflow' ),
			'section'           => 'header',
			'default'           => 'Follow me on YouTube',
			'priority'          => 10,
			'sanitize_callback' => 'wp_kses_post',
			'active_callback'   => array(
				array(
					'setting'  => 'header_layout',
					'operator' => '==',
					'value'    => 'large',
				),
				array(
					'setting'  => 'header_follow',
					'operator' => '==',
					'value'    => 'youtube',
				),
			),
		)
	);

	CSCO_Kirki::add_field(
		'csco_theme_mod', array(
			'type'            => 'select',
			'settings'        => 'header_follow_youtube_type',
			'label'           => esc_attr__( 'YouTube Channel Type', 'overflow' ),
			'section'         => 'header',
			'default'         => 'user',
			'priority'        => 10,
			'choices'         => array(
				'user'    => esc_html__( 'User', 'overflow' ),
				'channel' => esc_html__( 'Channel', 'overflow' ),
			),
			'active_callback' => array(
				array(
					'setting'  => 'header_layout',
					'operator' => '==',
					'value'    => 'large',
				),
				array(
					'setting'  => 'header_follow',
					'operator' => '==',
					'value'    => 'youtube',
				),
			),
		)
	);

	CSCO_Kirki::add_field(
		'csco_theme_mod', array(
			'type'            => 'text',
			'settings'        => 'header_follow_youtube_user',
			'label'           => esc_attr__( 'YouTube User ID', 'overflow' ),
			'section'         => 'header',
			'default'         => '',
			'priority'        => 10,
			'active_callback' => array(
				array(
					'setting'  => 'header_layout',
					'operator' => '==',
					'value'    => 'large',
				),
				array(
					'setting'  => 'header_follow',
					'operator' => '==',
					'value'    => 'youtube',
				),
				array(
					'setting'  => 'header_follow_youtube_type',
					'operator' => '==',
					'value'    => 'user',
				),
			),
		)
	);

	CSCO_Kirki::add_field(
		'csco_theme_mod', array(
			'type'            => 'text',
			'settings'        => 'header_follow_youtube_channel',
			'label'           => esc_attr__( 'YouTube Channel ID', 'overflow' ),
			'section'         => 'header',
			'default'         => '',
			'priority'        => 10,
			'active_callback' => array(
				array(
					'setting'  => 'header_layout',
					'operator' => '==',
					'value'    => 'large',
				),
				array(
					'setting'  => 'header_follow',
					'operator' => '==',
					'value'    => 'youtube',
				),
				array(
					'setting'  => 'header_follow_youtube_type',
					'operator' => '==',
					'value'    => 'channel',
				),
			),
		)
	);

	CSCO_Kirki::add_field(
		'csco_theme_mod', array(
			'type'              => 'text',
			'settings'          => 'header_follow_facebook_title',
			'label'             => esc_attr__( 'Title', 'overflow' ),
			'section'           => 'header',
			'default'           => 'Follow me on Facebook',
			'priority'          => 10,
			'sanitize_callback' => 'wp_kses_post',
			'active_callback'   => array(
				array(
					'setting'  => 'header_layout',
					'operator' => '==',
					'value'    => 'large',
				),
				array(
					'setting'  => 'header_follow',
					'operator' => '==',
					'value'    => 'facebook',
				),
			),
		)
	);

	CSCO_Kirki::add_field(
		'csco_theme_mod', array(
			'type'            => 'text',
			'settings'        => 'header_follow_facebook_user',
			'label'           => esc_attr__( 'Facebook User', 'overflow' ),
			'section'         => 'header',
			'default'         => '',
			'priority'        => 10,
			'active_callback' => array(
				array(
					'setting'  => 'header_layout',
					'operator' => '==',
					'value'    => 'large',
				),
				array(
					'setting'  => 'header_follow',
					'operator' => '==',
					'value'    => 'facebook',
				),
			),
		)
	);
}
