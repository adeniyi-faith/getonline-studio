<?php
/**
 * Template Functions
 *
 * Utility functions.
 *
 * @package Overflow
 */

if ( ! function_exists( 'csco_doing_request' ) ) {
	/**
	 * Determines whether the current request is a WordPress REST or Ajax request.
	 */
	function csco_doing_request() {
		if ( defined( 'REST_REQUEST' ) && REST_REQUEST ) {
			return true;
		}
		if ( defined( 'DOING_AJAX' ) && DOING_AJAX ) {
			return true;
		}
	}
}

if ( ! function_exists( 'csco_style' ) ) {
	/**
	 * Processing path of style.
	 *
	 * @param string $path URL to the stylesheet.
	 */
	function csco_style( $path ) {
		// Check RTL.
		if ( is_rtl() ) {
			return $path;
		}

		// Check Dev.
		$dev = get_theme_file_path( 'style-dev.css' );

		if ( file_exists( $dev ) ) {
			return str_replace( '.css', '-dev.css', $path );
		}

		return $path;
	}
}

if ( ! function_exists( 'csco_get_locale' ) ) {
	/**
	 * Get locale in uniform format.
	 */
	function csco_get_locale() {

		$csco_locale = get_locale();

		if ( preg_match( '#^[a-z]{2}\-[A-Z]{2}$#', $csco_locale ) ) {
			$csco_locale = str_replace( '-', '_', $csco_locale );
		} elseif ( preg_match( '#^[a-z]{2}$#', $csco_locale ) ) {
			if ( function_exists( 'mb_strtoupper' ) ) {
				$csco_locale .= '_' . mb_strtoupper( $csco_locale, 'UTF-8' );
			} else {
				$csco_locale .= '_' . strtoupper( $csco_locale );
			}
		}

		if ( empty( $csco_locale ) ) {
			$csco_locale = 'en_US';
		}

		return apply_filters( 'csco_locale', $csco_locale );

	}
}

if ( ! function_exists( 'csco_get_theme_data' ) ) {
	/**
	 * Get data about the theme.
	 *
	 * @param mixed $name The name of param.
	 */
	function csco_get_theme_data( $name ) {
		$theme = wp_get_theme( get_template() );

		return $theme->get( $name );
	}
}

if ( ! function_exists( 'csco_hex_is_light' ) ) {
	/**
	 * Determine whether a hex color is light.
	 *
	 * @param mixed $color Color.
	 * @return bool  True if a light color.
	 */
	function csco_hex_is_light( $color ) {
		$hex        = str_replace( '#', '', $color );
		$c_r        = hexdec( substr( $hex, 0, 2 ) );
		$c_g        = hexdec( substr( $hex, 2, 2 ) );
		$c_b        = hexdec( substr( $hex, 4, 2 ) );
		$brightness = ( ( $c_r * 299 ) + ( $c_g * 587 ) + ( $c_b * 114 ) ) / 1000;
		return $brightness > 155;
	}
}

if ( ! function_exists( 'csco_implode' ) ) {
	/**
	 * Join array elements with a string
	 *
	 * @param array  $pieces The array of strings to implode.
	 * @param string $glue   Defaults to an empty string.
	 */
	function csco_implode( $pieces, $glue = ', ' ) {
		if ( is_array( $pieces ) ) {
			return implode( $glue, $pieces );
		}
		if ( is_string( $pieces ) ) {
			return $pieces;
		}
	}
}

if ( ! function_exists( 'csco_light_or_dark' ) ) {
	/**
	 * Detect if we should use a light or dark color on a background color.
	 *
	 * @param mixed  $color Color.
	 * @param string $dark  Darkest reference.
	 *                      Defaults to '#000000'.
	 * @param string $light Lightest reference.
	 *                      Defaults to '#FFFFFF'.
	 * @return string
	 */
	function csco_light_or_dark( $color, $dark = '#000000', $light = '#FFFFFF' ) {
		return csco_hex_is_light( $color ) ? $dark : $light;
	}
}

if ( ! function_exists( 'csco_get_round_number' ) ) {
	/**
	 * Get rounded number.
	 *
	 * @param int $number    Input number.
	 * @param int $min_value Minimum value to round number.
	 * @param int $decimal   How may decimals shall be in the rounded number.
	 */
	function csco_get_round_number( $number, $min_value = 1000, $decimal = 1 ) {
		if ( $number < $min_value ) {
			return number_format_i18n( $number );
		}
		$alphabets = array(
			1000000000 => esc_html__( 'B', 'overflow' ),
			1000000    => esc_html__( 'M', 'overflow' ),
			1000       => esc_html__( 'K', 'overflow' ),
		);
		foreach ( $alphabets as $key => $value ) {
			if ( $number >= $key ) {
				return number_format_i18n( round( $number / $key, $decimal ), $decimal ) . $value;
			}
		}
	}
}

if ( ! function_exists( 'csco_the_round_number' ) ) {
	/**
	 * Echo rounded number.
	 *
	 * @param int $number    Input number.
	 * @param int $min_value Minimum value to round number.
	 * @param int $decimal   How may decimals shall be in the rounded number.
	 */
	function csco_the_round_number( $number, $min_value = 1000, $decimal = 1 ) {
		echo esc_html( csco_get_round_number( $number, $min_value, $decimal ) );
	}
}

if ( ! function_exists( 'csco_str_truncate' ) ) {
	/**
	 * Truncates string with specified length
	 *
	 * @param  string $string      Text string.
	 * @param  int    $length      Letters length.
	 * @param  string $etc         End truncate.
	 * @param  bool   $break_words Break words or not.
	 * @return string
	 */
	function csco_str_truncate( $string, $length = 80, $etc = '&hellip;', $break_words = false ) {
		if ( 0 === $length ) {
			return '';
		}

		if ( function_exists( 'mb_strlen' ) ) {

			// MultiBite string functions.
			if ( mb_strlen( $string ) > $length ) {
				$length -= min( $length, mb_strlen( $etc ) );
				if ( ! $break_words ) {
					$string = preg_replace( '/\s+?(\S+)?$/', '', mb_substr( $string, 0, $length + 1 ) );
				}

				return mb_substr( $string, 0, $length ) . $etc;
			}
		} else {

			// Default string functions.
			if ( strlen( $string ) > $length ) {
				$length -= min( $length, strlen( $etc ) );
				if ( ! $break_words ) {
					$string = preg_replace( '/\s+?(\S+)?$/', '', substr( $string, 0, $length + 1 ) );
				}

				return substr( $string, 0, $length ) . $etc;
			}
		}

		return $string;
	}
}

if ( ! function_exists( 'csco_svg_encode' ) ) {
	/**
	 * Encode svg symbols.
	 *
	 * @param string $string Text string.
	 */
	function csco_svg_encode( $string ) {

		$map = array(
			'<' => '%3C',
			'>' => '%3E',
			'#' => '%23',
		);

		foreach ( $map as $key => $value ) {
			$string = str_replace( $key, $value, $string );
		}

		return $string;
	}
}

if ( ! function_exists( 'csco_get_retina_image' ) ) {
	/**
	 * Get retina image.
	 *
	 * @param int    $attachment_id Image attachment ID.
	 * @param array  $attr          Attributes for the image markup. Default empty.
	 * @param string $type          The tag of type.
	 */
	function csco_get_retina_image( $attachment_id, $attr = array(), $type = 'img' ) {
		$attachment_url = wp_get_attachment_url( $attachment_id );

		// Retina image.
		$attached_file = get_attached_file( $attachment_id );

		if ( $attached_file ) {
			$uriinfo  = pathinfo( $attachment_url );
			$pathinfo = pathinfo( $attached_file );

			$retina_uri  = sprintf( '%s/%s@2x.%s', $uriinfo['dirname'], $uriinfo['filename'], $uriinfo['extension'] );
			$retina_file = sprintf( '%s/%s@2x.%s', $pathinfo['dirname'], $pathinfo['filename'], $pathinfo['extension'] );

			if ( file_exists( $retina_file ) ) {
				$attr['srcset'] = sprintf( '%s 1x, %s 2x', $attachment_url, $retina_uri );
			}
		}

		// Sizes.
		if ( 'amp-img' === $type ) {
			$data = wp_get_attachment_image_src( $attachment_id, 'full' );

			if ( isset( $data[1] ) ) {
				$attr['width'] = $data[1];
			}
			if ( isset( $data[2] ) ) {
				$attr['height'] = $data[2];
			}

			// Calc max height and set new width depending on proportion.
			if ( isset( $attr['width'] ) && isset( $attr['height'] ) ) {
				$max_height = get_theme_mod( 'header_nav_height', '60px' );

				$max_height = (int) str_replace( 'px', '', $max_height );

				if ( $max_height > 0 && $attr['height'] > $max_height ) {
					$attr['width'] = $attr['width'] / $attr['height'] * $max_height;

					$attr['height'] = $max_height;
				}
			}
		}

		// Attr.
		$output = null;

		foreach ( $attr as $name => $value ) {
			$output .= sprintf( ' %s="%s" ', esc_attr( $name ), esc_attr( $value ) );
		}

		// Image output.
		printf( '<%1$s src="%2$s" %3$s>', esc_attr( $type ), esc_url( $attachment_url ), $output ); // XSS ok.
	}
}

if ( ! function_exists( 'csco_offcanvas_exists' ) ) {
	/**
	 * Check if offcanvas exists.
	 */
	function csco_offcanvas_exists() {
		$locations = get_nav_menu_locations();

		if ( isset( $locations['primary'] ) || isset( $locations['mobile'] ) || is_active_sidebar( 'sidebar-offcanvas' ) ) {
			return true;
		}
	}
}

if ( ! function_exists( 'csco_site_content_class' ) ) {
	/**
	 * Display the classes for the site-content element.
	 *
	 * @param array $class Classes to add to the class list.
	 */
	function csco_site_content_class( $class = array() ) {
		$class[] = 'site-content';

		$class = apply_filters( 'csco_site_content_class', $class );

		// Separates classes with a single space, collates classes.
		echo sprintf( 'class="%s"', esc_attr( join( ' ', $class ) ) );
	}
}

if ( ! function_exists( 'csco_has_post_meta' ) ) {
	/**
	 * Check if the meta has display.
	 *
	 * @param string $meta    The name of meta.
	 * @param string $default The default value.
	 */
	function csco_has_post_meta( $meta, $default = null ) {
		if ( null === $default ) {
			$default = $meta;
		}
		return in_array( $meta, (array) get_theme_mod( 'post_meta', array( $default ) ), true );
	}
}

if ( ! function_exists( 'csco_coauthors_enabled' ) ) {
	/**
	 * Is it possible to check whether it is possible to output CoAuthors.
	 */
	function csco_coauthors_enabled() {
		if ( class_exists( 'Powerkit' ) && class_exists( 'CoAuthors_Plus' ) ) {
			return true;
		}
	}
}

if ( ! function_exists( 'csco_get_coauthors' ) ) {
	/**
	 * Return CoAuthors.
	 */
	function csco_get_coauthors() {
		$authors = array();

		if ( csco_coauthors_enabled() ) {
			$authors = get_coauthors();

			if ( apply_filters( 'csco_coauthors_sort_abc', true ) && $authors ) {
				usort( $authors, function( $a, $b ) {
					$a_name = function_exists( 'mb_strtolower' ) ? mb_strtolower( $a->display_name ) : strtolower( $a->display_name );
					$b_name = function_exists( 'mb_strtolower' ) ? mb_strtolower( $b->display_name ) : strtolower( $b->display_name );
					return strcmp( $a_name, $b_name );
				} );
			}
		}

		return $authors;
	}
}

if ( ! function_exists( 'csco_powerkit_module_enabled' ) ) {
	/**
	 * Helper function to check the status of powerkit modules
	 *
	 * @param array $name Name of module.
	 */
	function csco_powerkit_module_enabled( $name ) {
		if ( function_exists( 'powerkit_module_enabled' ) && powerkit_module_enabled( $name ) ) {
			return true;
		}
	}
}

if ( ! function_exists( 'csco_header_follow_choices' ) ) {
	/**
	 * Header Follow Choices
	 */
	function csco_header_follow_choices() {
		$header_follow = array(
			'button' => esc_attr__( 'Button', 'overflow' ),
			'none'   => esc_attr__( 'None', 'overflow' ),
		);

		if ( csco_powerkit_module_enabled( 'social_follow' ) ) {
			$header_follow = array_merge( array(
				'instagram' => esc_attr__( 'Instagram', 'overflow' ),
				'youtube'   => esc_attr__( 'YouTube', 'overflow' ),
				'facebook'  => esc_attr__( 'Facebook', 'overflow' ),
			), $header_follow );
		}

		return $header_follow;
	}
}

if ( ! function_exists( 'csco_get_youtube_video_id' ) ) {
	/**
	 * Get Youtube video ID from URL
	 *
	 * @param string $url YouTube URL.
	 */
	function csco_get_youtube_video_id( $url ) {
		preg_match( '/(http(s|):|)\/\/(www\.|)yout(.*?)\/(embed\/|watch.*?v=|)([a-z_A-Z0-9\-]{11})/i', $url, $results );

		if ( isset( $results[6] ) && $results[6] ) {
			return $results[6];
		}
	}
}

if ( ! function_exists( 'csco_get_video_background' ) ) {
	/**
	 * Get element video background
	 *
	 * @param string $location The current location.
	 * @param int    $post_id  The id of post.
	 */
	function csco_get_video_background( $location = null, $post_id = null ) {
		if ( ! $post_id ) {
			$post_id = get_the_ID();
		}

		$url   = get_post_meta( $post_id, 'csco_post_video_url', true );
		$start = get_post_meta( $post_id, 'csco_post_video_bg_start_time', true );
		$end   = get_post_meta( $post_id, 'csco_post_video_bg_end_time', true );

		if ( $location ) {
			$support = (array) get_post_meta( $post_id, 'csco_post_video_location', true );

			if ( ! in_array( $location, $support, true ) ) {
				return;
			}
		}

		$id = csco_get_youtube_video_id( $url );
		if ( $id ) {
		?>
			<div class="cs-video-wrapper" data-video-id="<?php echo esc_attr( $id ); ?>" data-video-start="<?php echo esc_attr( (int) $start ); ?>" data-video-end="<?php echo esc_attr( (int) $end ); ?>">
				<div class="cs-video-inner"></div>
			</div>
			<div class="cs-video-tools">
				<span class="cs-player-control cs-player-volume cs-player-mute"></span>
				<span class="cs-player-control cs-player-state cs-player-pause"></span>
			</div>
		<?php
		}
	}
}

if ( ! function_exists( 'csco_get_archive_location' ) ) {
	/**
	 * Returns Archive Location.
	 */
	function csco_get_archive_location() {

		global $wp_query;

		if ( isset( $wp_query->query_vars['csco_query']['location'] ) ) {

			return $wp_query->query_vars['csco_query']['location'];
		}

		if ( is_home() ) {

			return 'home';

		} else {

			return 'archive';
		}
	}
}

if ( ! function_exists( 'csco_get_archive_option' ) ) {
	/**
	 * Returns Archive Option Name.
	 *
	 * @param string $option_name The customize option name.
	 */
	function csco_get_archive_option( $option_name ) {

		return csco_get_archive_location() . '_' . $option_name;
	}
}

if ( ! function_exists( 'csco_get_page_preview' ) ) {
	/**
	 * Returns Page Preview.
	 */
	function csco_get_page_preview() {

		if ( is_home() ) {

			return apply_filters( 'csco_page_media_preview', get_theme_mod( 'homepage_media_preview', 'cropped' ) );
		}

		if ( is_singular( array( 'post', 'page' ) ) ) {

			$post_type = get_post_type( get_queried_object_id() );

			return apply_filters( 'csco_page_media_preview', get_theme_mod( $post_type . '_media_preview', 'cropped' ) );
		}

		if ( is_archive() ) {

			return apply_filters( 'csco_page_media_preview', get_theme_mod( 'archive_media_preview', 'cropped' ) );
		}

		if ( is_404() ) {

			return apply_filters( 'csco_page_media_preview', 'cropped' );
		}

		return apply_filters( 'csco_page_media_preview', 'cropped' );
	}
}

if ( ! function_exists( 'csco_get_page_sidebar' ) ) {
	/**
	 * Returns Page Sidebar: right, left or disabled.
	 *
	 * @param int    $post_id The ID of post.
	 * @param string $sidebar The sidebar of post.
	 */
	function csco_get_page_sidebar( $post_id = false, $sidebar = false ) {

		$location = apply_filters( 'csco_sidebar', 'sidebar-main' );

		if ( ! is_active_sidebar( $location ) ) {
			return 'disabled';
		}

		$home_id = false;

		if ( 'page' === get_option( 'show_on_front', 'posts' ) ) {

			$page_on_front = get_option( 'page_on_front' );

			if ( $post_id && intval( $post_id ) === intval( $page_on_front ) ) {
				$home_id = $post_id;
			}
		}

		if ( is_home() || $home_id ) {

			$show_on_front = get_option( 'show_on_front', 'posts' );

			if ( 'posts' === $show_on_front ) {

				return apply_filters( 'csco_page_sidebar', get_theme_mod( 'home_sidebar', 'right' ) );
			}

			if ( 'page' === $show_on_front ) {

				$home_id = $home_id ? $home_id : get_queried_object_id();

				// Get sidebar for the blog posts page.
				if ( ! $sidebar ) {
					$sidebar = get_post_meta( $home_id, 'csco_singular_sidebar', true );
				}

				if ( ! $sidebar || 'default' === $sidebar ) {

					return apply_filters( 'csco_page_sidebar', get_theme_mod( 'page_sidebar', 'right' ) );
				}

				return apply_filters( 'csco_page_sidebar', $sidebar );
			}
		}

		if ( is_singular( array( 'post', 'page' ) ) || $post_id ) {

			$post_id = $post_id ? $post_id : get_queried_object_id();

			// Get sidebar for current post.
			if ( ! $sidebar ) {
				$sidebar = get_post_meta( $post_id, 'csco_singular_sidebar', true );
			}

			if ( ! $sidebar || 'default' === $sidebar ) {

				$post_type = get_post_type( $post_id );

				return apply_filters( 'csco_page_sidebar', get_theme_mod( $post_type . '_sidebar', 'right' ) );
			}

			return apply_filters( 'csco_page_sidebar', $sidebar );
		}

		if ( is_archive() ) {

			return apply_filters( 'csco_page_sidebar', get_theme_mod( 'archive_sidebar', 'right' ) );
		}

		if ( is_404() ) {

			return apply_filters( 'csco_page_sidebar', 'disabled' );
		}

		return apply_filters( 'csco_page_sidebar', 'right' );
	}
}

if ( ! function_exists( 'csco_get_page_header_type' ) ) {
	/**
	 * Returns Page Header
	 */
	function csco_get_page_header_type() {

		$allow = array( 'none', 'standard', 'large', 'title' );

		if ( is_singular( array( 'post', 'page' ) ) ) {

			// Get header type for current post.
			$page_header_type = get_post_meta( get_queried_object_id(), 'csco_page_header_type', true );

			if ( ! in_array( $page_header_type, $allow, true ) || 'default' === $page_header_type ) {

				$post_type = get_post_type( get_queried_object_id() );

				return apply_filters( 'csco_page_header_type', get_theme_mod( $post_type . '_header_type', 'standard' ) );
			}

			return apply_filters( 'csco_page_header_type', $page_header_type );
		}

		return apply_filters( 'csco_page_header_type', 'standard' );
	}
}

if ( ! function_exists( 'csco_get_state_load_nextpost' ) ) {
	/**
	 * State Auto Load Next Post.
	 */
	function csco_get_state_load_nextpost() {

		if ( is_singular( 'post' ) ) {

			$page_load_nextpost = get_post_meta( get_queried_object_id(), 'csco_page_load_nextpost', true );

			if ( ! $page_load_nextpost || 'default' === $page_load_nextpost ) {

				return apply_filters( 'csco_page_load_nextpost', get_theme_mod( 'post_load_nextpost', false ) );
			}

			$page_load_nextpost = 'enabled' === $page_load_nextpost ? true : false;

			return apply_filters( 'csco_page_load_nextpost', $page_load_nextpost );
		}

		return apply_filters( 'csco_page_load_nextpost', false );
	}
}

if ( ! function_exists( 'csco_exclude_duplicate_ids' ) ) {
	/**
	 * Exclude Duplicate Posts
	 *
	 * @param string $function The name search function.
	 * @param array  $ids      Current list ids.
	 */
	function csco_exclude_duplicate_ids( $function, & $ids = array() ) {
		global $wp_query;

		$search = $function( $wp_query, false );

		if ( is_array( $search ) ) {
			$ids = array_merge( $ids, $search );
		}

		return $ids;
	}
}

if ( ! function_exists( 'csco_get_featured_posts_numbers' ) ) {
	/**
	 * Get numbers posts of featured posts
	 *
	 * @param string $type Featured type.
	 */
	function csco_get_featured_posts_numbers( $type ) {
		switch ( $type ) {
			case 'type-1':
				return 1;
			case 'type-2':
				return 1;
			case 'type-3':
				return 1;
			default:
				return 3;
		}
	}
}

if ( ! function_exists( 'csco_get_featured_posts_ids' ) ) {
	/**
	 * Get IDs of featured posts
	 *
	 * @param string $location       Featured Location.
	 * @param int    $posts_per_page Number of post to show.
	 * @param array  $post__not_in   Displays all posts except specified.
	 */
	function csco_get_featured_posts_ids( $location = null, $posts_per_page = 'auto', $post__not_in = array() ) {
		global $wp_query;

		$cache_key = md5( maybe_serialize( func_get_args() ) );

		$post_ids = get_query_var( $cache_key );

		if ( ! $post_ids ) {

			$queried_object = $wp_query->get_queried_object();

			// Get numbers posts.
			if ( 'auto' === $posts_per_page ) {
				$type = get_theme_mod( $location . 'featured_posts_type', 'type-2' );

				$posts_per_page = csco_get_featured_posts_numbers( $type );
			}

			// Set arg.
			$args = array(
				'ignore_sticky_posts' => true,
				'order'               => 'DESC',
				'posts_per_page'      => $posts_per_page,
			);

			if ( isset( $queried_object->term_id ) && 'category' === $location ) {
				$args['cat'] = $queried_object->term_id;
			}

			$location = $location ? sprintf( '%s_', $location ) : '';

			$categories = get_theme_mod( $location . 'featured_posts_filter_categories' );

			// Filter by categories.
			if ( $categories ) {
				$categories = array_map( 'trim', explode( ',', $categories ) );
				// Category.
				$args['tax_query'] = array(
					array(
						'taxonomy'         => 'category',
						'field'            => 'slug',
						'terms'            => $categories,
						'include_children' => true,
					),
				);
			}

			$tags = get_theme_mod( $location . 'featured_posts_filter_tags' );

			// Filter by tags.
			if ( $tags ) {
				// Tag.
				$args['tag'] = $tags;
			}

			$posts = get_theme_mod( $location . 'featured_posts_filter_posts' );

			// Filter by posts.
			if ( $posts ) {
				$args['post_type'] = array( 'post', 'page' );

				$args['post__in'] = explode( ',', str_replace( ' ', '', $posts ) );
			} else {
				// Exclude singular ID.
				if ( is_singular() ) {
					array_push( $post__not_in, get_the_ID() );
				}

				$args['post__not_in'] = $post__not_in;
			}

			$orderby = get_theme_mod( $location . 'featured_posts_orderby', 'date' );

			// Post order.
			if ( 'post_views' === $orderby && class_exists( 'Post_Views_Counter' ) ) {
				// Post Views.
				$args['orderby'] = 'post_views';
				// Don't hide posts without views.
				$args['views_query']['hide_empty'] = false;
				// Time Frame for Post Views.
				$time_frame = get_theme_mod( $location . 'featured_posts_time_frame' );
				if ( $time_frame ) {
					$args['date_query'] = array(
						array(
							'column' => 'post_date_gmt',
							'after'  => $time_frame . ' ago',
						),
					);
				}
			} else {
				// Date.
				$args['orderby'] = 'date';
			}

			$args = apply_filters( 'csco_' . $location . 'featured_posts_args', $args );

			$post_ids = array();

			$the_query = new WP_Query( $args );
			if ( $the_query->have_posts() ) {
				while ( $the_query->have_posts() ) {
					$the_query->the_post();
					$post_ids[] = get_the_ID();
				}
				wp_reset_postdata();
			}

			set_query_var( $cache_key, $post_ids );
		}

		return apply_filters( 'csco_' . $location . 'featured_posts_ids', $post_ids );
	}
}

/**
 * -------------------------------------
 * Get IDs of posts
 * -------------------------------------
 */

if ( ! function_exists( 'csco_get_homepage_posts_ids' ) ) {
	/**
	 * Get IDs of homepage posts
	 */
	function csco_get_homepage_posts_ids() {
		$posts_per_page = csco_get_featured_posts_numbers( get_theme_mod( 'featured_posts_type', 'type-2' ) );

		$ids = csco_get_featured_posts_ids( null, $posts_per_page );

		if ( count( $ids ) >= $posts_per_page ) {
			return $ids;
		}
	}
}

if ( ! function_exists( 'csco_get_category_posts_ids' ) ) {
	/**
	 * Get IDs of category posts
	 */
	function csco_get_category_posts_ids() {
		$posts_per_page = csco_get_featured_posts_numbers( get_theme_mod( 'category_featured_posts_type', 'type-3' ) );

		$ids = csco_get_featured_posts_ids( 'category', $posts_per_page );

		if ( count( $ids ) >= $posts_per_page ) {
			return $ids;
		}
	}
}

if ( ! function_exists( 'csco_get_trending_posts_ids' ) ) {
	/**
	 * Get IDs of trending posts
	 */
	function csco_get_trending_posts_ids() {
		// Exclude home posts.
		$post__not_in = array();
		if ( get_theme_mod( 'trending_featured_posts_avoid_duplicate' ) ) {
			csco_exclude_duplicate_ids( 'csco_exclude_featured_posts_from_homepage_query', $post__not_in );
		}

		$posts_per_page = 3;

		$ids = csco_get_featured_posts_ids( 'trending', $posts_per_page, $post__not_in );

		if ( count( $ids ) >= $posts_per_page ) {
			return $ids;
		}
	}
}

if ( ! function_exists( 'csco_get_footer_posts_ids' ) ) {
	/**
	 * Get IDs of footer posts
	 */
	function csco_get_footer_posts_ids() {
		global $wp_query;

		// Exclude home | trending | category featured posts.
		$post__not_in = array();
		if ( get_theme_mod( 'footer_featured_posts_avoid_duplicate' ) ) {

			if ( isset( $wp_query->posts ) && $wp_query->posts ) {
				$post__not_in = array_merge( $post__not_in, wp_filter_object_list( $wp_query->posts, array(), 'or', 'ID' ) );
			}

			csco_exclude_duplicate_ids( 'csco_exclude_featured_posts_from_homepage_query', $post__not_in );
			csco_exclude_duplicate_ids( 'csco_exclude_trending_posts_from_homepage_query', $post__not_in );
			csco_exclude_duplicate_ids( 'csco_exclude_featured_posts_from_category_query', $post__not_in );
		}

		$posts_per_page = 4;

		$ids = csco_get_featured_posts_ids( 'footer', $posts_per_page, $post__not_in );

		if ( count( $ids ) >= $posts_per_page ) {
			return $ids;
		}
	}
}
