<?php
/**
 * Filters
 *
 * Filtering native WordPress and third-party plugins' functions.
 *
 * @package Overflow
 */

if ( ! function_exists( 'csco_body_class' ) ) {
	/**
	 * Adds classes to <body> tag
	 *
	 * @param array $classes is an array of all body classes.
	 */
	function csco_body_class( $classes ) {

		// Page Layout.
		$classes[] = 'cs-page-layout-' . csco_get_page_sidebar();

		// Header Layout.
		$classes[] = 'header-' . get_theme_mod( 'header_layout', 'large' );

		// Sticky Navbar.
		if ( get_theme_mod( 'navbar_sticky', true ) ) {
			$classes[] = 'navbar-sticky-enabled';
		}

		// Smart Navbar.
		if ( get_theme_mod( 'navbar_sticky', true ) && get_theme_mod( 'effects_navbar_scroll', true ) ) {
			$classes[] = 'navbar-smart-enabled';
		}

		// Sticky Sidebar.
		if ( get_theme_mod( 'misc_sticky_sidebar', true ) ) {
			$classes[] = 'sticky-sidebar-enabled';
			$classes[] = get_theme_mod( 'misc_sticky_sidebar_method', 'stick-to-bottom' );
		}

		// Block Alignment.
		if ( is_home() || is_archive() || is_single() ) {
			$classes[] = 'block-align-enabled';
		}

		if ( is_page() ) {
			$classes[] = 'block-page-align-enabled';
		}

		// Text Alignment.
		$classes[] = 'design-text-alignment-' . get_theme_mod( 'design_text_alignment', 'center' );

		// Border Radius.
		if ( get_theme_mod( 'design_border_radius', true ) ) {
			$classes[] = 'design-border-radius';
		}

		// Edge Enabled.
		if ( 'straight' !== get_theme_mod( 'design_edge_type', 'straight' ) ) {
			$classes[] = 'design-edge-enabled';
		}

		// Edge Type.
		$classes[] = 'design-edge-' . get_theme_mod( 'design_edge_type', 'straight' );

		// Heading Stroke Type.
		$classes[] = 'design-heading-stroke-' . get_theme_mod( 'design_heading_stroke', 'dotted' );

		// Title Block Stroke Type.
		$classes[] = 'design-title-stroke-' . get_theme_mod( 'design_title_stroke', 'dotted' );

		return $classes;
	}
}
add_filter( 'body_class', 'csco_body_class' );

if ( ! function_exists( 'csco_sitecontent_class' ) ) {
	/**
	 * Adds the classes for the site-content element.
	 *
	 * @param array $classes Classes to add to the class list.
	 */
	function csco_sitecontent_class( $classes ) {
		// Page Sidebar.
		if ( 'disabled' !== csco_get_page_sidebar() ) {
			$classes[] = 'sidebar-enabled sidebar-' . csco_get_page_sidebar();
		} else {
			$classes[] = 'sidebar-disabled';
		}

		// Post Sidebar.
		if ( is_single() && csco_has_post_meta( 'shares' ) && csco_powerkit_module_enabled( 'share_buttons' ) && powerkit_share_buttons_exists( 'post_sidebar' ) ) {
			$classes[] = 'post-sidebar-enabled';
		} else {
			$classes[] = 'post-sidebar-disabled';
		}

		return $classes;
	}
}
add_filter( 'csco_site_content_class', 'csco_sitecontent_class' );

if ( ! function_exists( 'csco_remove_hentry_class' ) ) {
	/**
	 * Remove hentry from post_class
	 *
	 * @param array $classes One or more classes to add to the class list.
	 */
	function csco_remove_hentry_class( $classes ) {
		return array_diff( $classes, array( 'hentry' ) );
	}
}
add_filter( 'post_class', 'csco_remove_hentry_class' );

if ( ! function_exists( 'csco_kirki_support_rtl' ) ) {
	/**
	 * Add support rtl to Kirki
	 *
	 * @param string $style The dynamic css.
	 */
	function csco_kirki_support_rtl( $style ) {
		if ( is_rtl() ) {
			$style = str_replace( 'border-top-left-radius:', 'border-top-right-radius:', $style );
			$style = str_replace( 'border-bottom-left-radius:', 'border-bottom-right-radius:', $style );
		}

		return $style;
	}
}
add_filter( 'kirki_global_dynamic_css', 'csco_kirki_support_rtl' );
add_filter( 'kirki_csco_theme_mod_dynamic_css', 'csco_kirki_support_rtl' );

if ( ! function_exists( 'csco_register_theme_fonts' ) ) {
	/**
	 * Add new custom fonts
	 *
	 * @param array $fonts The list custom fonts.
	 */
	function csco_register_theme_fonts( $fonts ) {

		$fonts['jost'] = array(
			'name'     => esc_html__( 'Jost', 'once' ),
			'variants' => array( '300', 'regular', '500', '700' ),
		);

		return $fonts;
	}
}
add_filter( 'csco_theme_fonts', 'csco_register_theme_fonts' );

if ( ! function_exists( 'csco_overwrite_sidebar' ) ) {
	/**
	 * Overwrite Default Sidebar
	 *
	 * @param string $sidebar Sidebar slug.
	 */
	function csco_overwrite_sidebar( $sidebar ) {
		// Check Nonce.
		wp_verify_nonce( null );

		if ( isset( $_REQUEST['action'] ) && 'csco_ajax_load_nextpost' === $_REQUEST['action'] ) { // Input var ok.
			if ( is_active_sidebar( 'sidebar-loaded' ) ) {
				$sidebar = 'sidebar-loaded';
			}
		}
		return $sidebar;
	}
}
add_filter( 'csco_sidebar', 'csco_overwrite_sidebar' );

if ( ! function_exists( 'csco_tiny_mce_refresh_cache' ) ) {
	/**
	 * TinyMCE Refresh Cache.
	 *
	 * @param array $settings An array with TinyMCE config.
	 */
	function csco_tiny_mce_refresh_cache( $settings ) {

		$theme = wp_get_theme();

		$settings['cache_suffix'] = sprintf( 'v=%s', $theme->get( 'Version' ) );

		return $settings;
	}
}
add_filter( 'tiny_mce_before_init', 'csco_tiny_mce_refresh_cache' );

if ( ! function_exists( 'csco_max_srcset_image_width' ) ) {
	/**
	 * Changes max image width in srcset attribute
	 *
	 * @param int   $max_width  The maximum image width to be included in the 'srcset'. Default '1600'.
	 * @param array $size_array Array of width and height values in pixels (in that order).
	 */
	function csco_max_srcset_image_width( $max_width, $size_array ) {
		return 3840;
	}
}
add_filter( 'max_srcset_image_width', 'csco_max_srcset_image_width', 10, 2 );

if ( ! function_exists( 'csco_get_the_archive_title' ) ) {
	/**
	 * Archive Title
	 *
	 * Removes default prefixes, like "Category:" from archive titles.
	 *
	 * @param string $title Archive title.
	 */
	function csco_get_the_archive_title( $title ) {
		if ( is_category() ) {

			$title = single_cat_title( '', false );

		} elseif ( is_tag() ) {

			$title = single_tag_title( '', false );

		} elseif ( is_author() ) {

			$title = get_the_author( '', false );

		}
		return $title;
	}
}
add_filter( 'get_the_archive_title', 'csco_get_the_archive_title' );

if ( ! function_exists( 'csco_excerpt_length' ) ) {
	/**
	 * Excerpt Length
	 *
	 * @param string $length of the excerpt.
	 */
	function csco_excerpt_length( $length ) {
		return 18;
	}
}
add_filter( 'excerpt_length', 'csco_excerpt_length' );

if ( ! function_exists( 'csco_strip_shortcode_from_excerpt' ) ) {
	/**
	 * Strip shortcodes from excerpt
	 *
	 * @param string $content Excerpt.
	 */
	function csco_strip_shortcode_from_excerpt( $content ) {
		$content = strip_shortcodes( $content );
		return $content;
	}
}
add_filter( 'the_excerpt', 'csco_strip_shortcode_from_excerpt' );

if ( ! function_exists( 'csco_strip_tags_from_excerpt' ) ) {
	/**
	 * Strip HTML from excerpt
	 *
	 * @param string $content Excerpt.
	 */
	function csco_strip_tags_from_excerpt( $content ) {
		$content = strip_tags( $content );
		return $content;
	}
}
add_filter( 'the_excerpt', 'csco_strip_tags_from_excerpt' );

if ( ! function_exists( 'csco_excerpt_more' ) ) {
	/**
	 * Excerpt Suffix
	 *
	 * @param string $more suffix for the excerpt.
	 */
	function csco_excerpt_more( $more ) {
		return '&hellip;';
	}
}
add_filter( 'excerpt_more', 'csco_excerpt_more' );

if ( ! function_exists( 'csco_post_meta_process' ) ) {
	/**
	 * Pre processing post meta choices
	 *
	 * @param array $data Post meta list.
	 */
	function csco_post_meta_process( $data ) {
		if ( ! csco_powerkit_module_enabled( 'share_buttons' ) && isset( $data['shares'] ) ) {
			unset( $data['shares'] );
		}
		if ( ! csco_powerkit_module_enabled( 'reading_time' ) && isset( $data['reading_time'] ) ) {
			unset( $data['reading_time'] );
		}
		if ( ! class_exists( 'Post_Views_Counter' ) && isset( $data['views'] ) ) {
			unset( $data['views'] );
		}
		return $data;
	}
}
add_filter( 'csco_post_meta_choices', 'csco_post_meta_process' );

if ( ! function_exists( 'csco_wrap_post_gallery' ) ) {
	/**
	 * Alignment of Galleries in Classic Block
	 *
	 * @param string $html     The current output.
	 * @param array  $attr     Attributes from the gallery shortcode.
	 * @param int    $instance Numeric ID of the gallery shortcode instance.
	 */
	function csco_wrap_post_gallery( $html, $attr, $instance ) {
		switch ( get_theme_mod( 'misc_classic_gallery_alignment', 'default' ) ) {
			case 'wide':
				$wrap = 'alignwide';
				break;
			case 'large':
				$wrap = 'alignfull';
				break;
		}

		if ( ! isset( $attr['wrap'] ) && isset( $wrap ) ) {
			$attr['wrap'] = $wrap;

			// Our custom HTML wrapper.
			$html = sprintf( '<div class="%s">%s</div>', esc_attr( $wrap ), gallery_shortcode( $attr ) );
		}

		return $html;
	}
	add_filter( 'post_gallery', 'csco_wrap_post_gallery', 99, 3 );
}

if ( ! function_exists( 'csco_wp_link_pages_args' ) ) {
	/**
	 * Paginated Post Pagination
	 *
	 * @param string $args Paginated posts pagination args.
	 */
	function csco_wp_link_pages_args( $args ) {
		if ( 'next_and_number' === $args['next_or_number'] ) {
			global $page, $numpages, $multipage, $more, $pagenow;
			$args['next_or_number'] = 'number';

			$prev = '';
			$next = '';
			if ( $multipage ) {
				if ( $more ) {
					$i = $page - 1;
					if ( $i && $more ) {
						$prev .= _wp_link_page( $i );
						$prev .= $args['link_before'] . $args['previouspagelink'] . $args['link_after'] . '</a>';
					}
					$i = $page + 1;
					if ( $i <= $numpages && $more ) {
						$next .= _wp_link_page( $i );
						$next .= $args['link_before'] . $args['nextpagelink'] . $args['link_after'] . '</a>';
					}
				}
			}
			$args['before'] = $args['before'] . $prev;
			$args['after']  = $next . $args['after'];
		}
		return $args;
	}
}
add_filter( 'wp_link_pages_args', 'csco_wp_link_pages_args' );

if ( ! function_exists( 'csco_post_header_avatar_size' ) ) {
	/**
	 * Set for post header avatar size.
	 *
	 * @param int $size Avatar size.
	 */
	function csco_post_header_avatar_size( $size ) {
		return 40;
	}
}

/**
 * -------------------------------------------------------------------------
 * [ SearchWP Live Ajax Search ]
 * -------------------------------------------------------------------------
 */

if ( ! function_exists( 'csco_searchwp_live_enqueue_scripts' ) ) {
	/**
	 * Enqueue scripts and styles.
	 */
	function csco_searchwp_live_enqueue_scripts() {

		$style = sprintf( '.searchwp-live-search-no-min-chars:after { content: "%s" }', esc_html__( 'Continue typing', 'overflow' ) );

		wp_add_inline_style( 'csco-styles', $style );
	}
}
add_action( 'wp_enqueue_scripts', 'csco_searchwp_live_enqueue_scripts' );


/**
 * Remove Output the base styles.
 */
add_filter( 'searchwp_live_search_base_styles', '__return_false' );
