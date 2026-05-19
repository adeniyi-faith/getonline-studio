<?php
/**
 * Post Meta Helper Functions
 *
 * These helper functions return post meta, if its enabled in WordPress Customizer.
 *
 * @package Overflow
 */

if ( ! function_exists( 'csco_get_post_meta' ) ) {
	/**
	 * Post Meta
	 *
	 * A wrapper function that returns all post meta types either
	 * in an ordered list <ul> or as a single element <span>.
	 *
	 * @param mixed $meta    Contains post meta types.
	 * @param bool  $compact If compact version shall be displayed.
	 * @param bool  $echo    Echo or return.
	 * @param mixed $allowed Allowed meta types (array: list types, true: auto definition, option name: get value of option).
	 */
	function csco_get_post_meta( $meta, $compact = false, $echo = true, $allowed = null ) {

		// Return if no post meta types provided.
		if ( ! $meta ) {
			return;
		}

		if ( is_string( $allowed ) || true === $allowed ) {
			$option_default = null;

			$option_name = is_string( $allowed ) ? $allowed : csco_get_archive_option( 'post_meta' );

			if ( class_exists( 'Kirki' ) && property_exists( 'Kirki', 'all_fields' ) && isset( Kirki::$all_fields[ $option_name ]['default'] ) ) {
				$option_default = Kirki::$all_fields[ $option_name ]['default'];
			} elseif ( class_exists( 'Kirki' ) && property_exists( 'Kirki', 'fields' ) && isset( Kirki::$fields[ $option_name ]['default'] ) ) {
				$option_default = Kirki::$fields[ $option_name ]['default'];
			} elseif ( isset( CSCO_Kirki::$fields[ $option_name ]['default'] ) ) {
				$option_default = CSCO_Kirki::$fields[ $option_name ]['default'];
			}

			$allowed = get_theme_mod( $option_name, $option_default );
		}

		if ( ! is_array( $allowed ) && ! $allowed ) {
			// Set default allowed post meta types.
			$allowed = apply_filters( 'csco_post_meta', array( 'date', 'category', 'comments', 'shares', 'views', 'reading_time', 'author' ) );
		}

		if ( is_array( $meta ) ) {
			// Intersect provided and allowed meta types.
			$meta = array_intersect( $meta, $allowed );
		}

		$output = null;

		if ( $meta && is_array( $meta ) ) {

			$output .= '<ul class="post-meta">';

			// Add normal meta types to the list.
			foreach ( $meta as $type ) {
				$function = "csco_get_meta_$type";
				$output  .= $function( 'li', $compact );
			}

			$output .= '</ul>';

		} else {
			if ( in_array( $meta, $allowed, true ) ) {
				// Output single meta type.
				$function = "csco_get_meta_$meta";
				$output  .= $function( 'div', $compact );
			}
		}

		if ( $echo ) {
			echo (string) $output; // XSS ok.
		} else {
			return $output;
		}
	}
}

if ( ! function_exists( 'csco_get_meta_category' ) ) {
	/**
	 * Post Сategory
	 *
	 * @param string $tag     Element tag, i.e. div or span.
	 * @param bool   $compact If compact version shall be displayed.
	 * @param int    $post_id Post ID.
	 */
	function csco_get_meta_category( $tag = 'span', $compact = false, $post_id = null ) {

		if ( ! $post_id ) {
			$post_id = get_the_ID();
		}

		$output = '<' . esc_html( $tag ) . ' class="meta-category">';

		$output .= get_the_category_list( '', '', $post_id );

		$output .= '</' . esc_html( $tag ) . '>';

		return $output;
	}
}

if ( ! function_exists( 'csco_get_meta_date' ) ) {
	/**
	 * Post Date
	 *
	 * @param string $tag     Element tag, i.e. div or span.
	 * @param bool   $compact If compact version shall be displayed.
	 */
	function csco_get_meta_date( $tag = 'span', $compact = false ) {

		$output = '<' . esc_html( $tag ) . ' class="meta-date">';

		if ( false === $compact ) {
			$time_string = get_the_date();
		} else {
			$time_string = get_the_date( 'd.m.y' );
		}

		if ( get_the_time( 'd.m.Y H:i' ) !== get_the_modified_time( 'd.m.Y H:i' ) ) {
			if ( ! get_theme_mod( 'misc_published_date', true ) ) {
				$time_string = get_the_modified_date();
			}
		}

		$output .= apply_filters( 'csco_post_meta_date_output', $time_string );

		$output .= '</' . esc_html( $tag ) . '>';

		return $output;
	}
}

if ( ! function_exists( 'csco_get_meta_author' ) ) {
	/**
	 * Post Author
	 *
	 * @param string $tag     Element tag, i.e. div or span.
	 * @param bool   $compact If compact version shall be displayed.
	 */
	function csco_get_meta_author( $tag = 'span', $compact = true ) {

		$authors = array( get_the_author_meta( 'ID' ) );

		$output = '<' . esc_attr( $tag ) . ' class="meta-author"><span class="by">' . esc_html__( 'By', 'overflow' ) . '</span>';

		if ( csco_coauthors_enabled() ) {
			$authors = csco_get_coauthors();
		}

		if ( $authors ) {

			$counter = 0;

			foreach ( $authors as & $author ) {

				$output .= $counter > 0 ? sprintf( '<span class="sep">%s</span>', esc_html__( 'and', 'overflow' ) ) : '';

				$author_id    = isset( $author->ID ) ? $author->ID : $author;
				$display_name = isset( $author->display_name ) ? $author->display_name : get_the_author_meta( 'display_name', $author_id );
				$posts_url    = get_author_posts_url( $author_id, isset( $author->user_nicename ) ? $author->user_nicename : '' );

				$avatar = get_avatar( $author_id, apply_filters( 'csco_meta_avatar_size', 20 ) );

				$output .= sprintf( '<span class="author"><a class="url fn n" href="%1$s" title="%2$s">%3$s</a></span>',
					esc_url( $posts_url ),
					/* translators: %s: author name */
					esc_attr( sprintf( __( 'View all posts by %s', 'overflow' ), $display_name ) ),
					( false === $compact ? $avatar : '' ) . $display_name
				);

				$counter++;
			}
		}

		$output .= '</' . esc_html( $tag ) . '>';

		return $output;

	}
}

if ( ! function_exists( 'csco_get_meta_comments' ) ) {
	/**
	 * Post Comments
	 *
	 * @param string $tag     Element tag, i.e. div or span.
	 * @param bool   $compact If compact version shall be displayed.
	 */
	function csco_get_meta_comments( $tag = 'span', $compact = false ) {

		if ( ! comments_open( get_the_ID() ) ) {
			return;
		}

		$output  = '<' . esc_html( $tag ) . ' class="meta-comments">';
		$output .= '<i class="cs-icon cs-icon-message-square"></i>';

		if ( true === $compact ) {
			ob_start();
			comments_popup_link( '0', '1', '%', 'comments-link', '' );
			$output .= ob_get_clean();
		} else {
			ob_start();
			comments_popup_link( esc_html__( 'No comments', 'overflow' ), esc_html__( 'One comment', 'overflow' ), '% ' . esc_html__( 'comments', 'overflow' ), 'comments-link', '' );
			$output .= ob_get_clean();
		}

		$output .= '</' . esc_html( $tag ) . '>';

		return $output;
	}
}

if ( ! function_exists( 'csco_get_meta_reading_time' ) ) {
	/**
	 * Post Reading Time
	 *
	 * @param string $tag     Element tag, i.e. div or span.
	 * @param bool   $compact If compact version shall be displayed.
	 */
	function csco_get_meta_reading_time( $tag = 'span', $compact = false ) {

		if ( ! csco_powerkit_module_enabled( 'reading_time' ) ) {
			return;
		}

		$reading_time = powerkit_get_post_reading_time();

		$output  = '<' . esc_html( $tag ) . ' class="meta-reading-time">';
		$output .= '<i class="cs-icon cs-icon-clock"></i>';

		if ( true === $compact ) {
			$output .= intval( $reading_time ) . ' ' . esc_html__( 'min', 'overflow' );
		} else {
			/* translators: %s number of minutes */
			$output .= esc_html( sprintf( _n( '%s minute read', '%s minute read', $reading_time, 'overflow' ), $reading_time ) );
		}

		$output .= '</' . esc_html( $tag ) . '>';

		return $output;
	}
}

if ( ! function_exists( 'csco_get_meta_views' ) ) {
	/**
	 * Post Views
	 *
	 * @param string $tag     Element tag, i.e. div or span.
	 * @param bool   $compact If compact version shall be displayed.
	 */
	function csco_get_meta_views( $tag = 'span', $compact = false ) {

		if ( ! class_exists( 'Post_Views_Counter' ) ) {
			return;
		}

		$views = pvc_get_post_views();

		// Don't display if minimum threshold is not met.
		if ( $views < apply_filters( 'csco_minimum_views', 1 ) ) {
			return;
		}

		$output  = '<' . esc_html( $tag ) . ' class="meta-views">';
		$output .= '<i class="cs-icon cs-icon-activity"></i>';

		$views_rounded = csco_get_round_number( $views );

		if ( true === $compact ) {
			$output .= esc_html( $views_rounded );
		} else {
			if ( $views > 1000 ) {
				$output .= $views_rounded . ' ' . esc_html__( 'views', 'overflow' );
			} else {
				/* translators: %s number of post views */
				$output .= esc_html( sprintf( _n( '%s view', '%s views', $views, 'overflow' ), $views ) );
			}
		}

		$output .= '</' . esc_html( $tag ) . '>';

		return $output;

	}
}

if ( ! function_exists( 'csco_get_meta_shares' ) ) {
	/**
	 * Post Shares
	 *
	 * @param string $tag     Element tag, i.e. div or span.
	 * @param bool   $compact If compact version shall be displayed.
	 */
	function csco_get_meta_shares( $tag = 'span', $compact = false ) {

		if ( ! csco_powerkit_module_enabled( 'share_buttons' ) ) {
			return;
		}

		$output = '<' . esc_html( $tag ) . ' class="meta-shares">';

		$accounts = get_option( 'powerkit_share_buttons_post_meta_multiple_list', array( 'facebook', 'twitter', 'pinterest' ) );

		// Share Count.
		$shares = powerkit_share_buttons_get_total_count( $accounts, get_the_ID(), null, true );

		$shares_rounded = powerkit_share_buttons_count_format( $shares );

		// Don't display if minimum threshold is not met.
		if ( $shares < apply_filters( 'csco_minimum_shares', 1 ) ) {
			return;
		}

		ob_start();
		?>
			<span class="total">
				<i class="cs-icon cs-icon-share"></i>
				<?php
				if ( true === $compact ) {
					echo esc_html( $shares_rounded );
				} else {
					if ( $shares > 1000 ) {
						echo esc_html( $shares_rounded ) . ' ' . esc_html__( 'shares', 'overflow' );
					} else {
						/* translators: %s number of post shares */
						echo esc_html( sprintf( _n( '%s share', '%s shares', $shares, 'overflow' ), $shares ) );
					}
				}
				?>
			</span>
		<?php

		$output .= ob_get_clean();

		$output .= '</' . esc_html( $tag ) . '>';

		return $output;

	}
}
