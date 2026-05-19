<?php
/**
 * Powerkit Filters
 *
 * @package Overflow
 */

/**
 * Register Post Archive Share Buttons Location
 *
 * @param array $locations List of Locations.
 */
function csco_share_buttons_after_content( $locations = array() ) {

	$locations['after-content'] = array(
		'shares'         => array( 'facebook', 'twitter', 'pinterest' ),
		'name'           => esc_html__( 'After Post Content', 'overflow' ),
		'location'       => 'after-content',
		'mode'           => 'mixed',
		'before'         => '',
		'after'          => '',
		'display'        => true,
		'fields'         => array(
			'display_total'   => true,
			'display_count'   => true,
			'schemes'         => array( 'default', 'bold-bg', 'bold' ),
			'count_locations' => array( 'inside' ),
		),
		'scheme'         => 'bold-bg',
		'count_location' => 'inside',
	);

	return $locations;
}
add_filter( 'powerkit_share_buttons_locations', 'csco_share_buttons_after_content' );

/**
 * Register Post Archive Share Buttons Location
 *
 * @param array $locations List of Locations.
 */
function csco_share_buttons_post_meta( $locations = array() ) {

	$locations['post_meta'] = array(
		'shares'         => array( 'facebook', 'twitter', 'pinterest' ),
		'name'           => esc_html__( 'Post Archive', 'overflow' ),
		'location'       => 'post_meta',
		'mode'           => 'cached',
		'before'         => '',
		'after'          => '',
		'display'        => true,
		'meta'           => array(
			'icons'  => true,
			'titles' => false,
			'labels' => false,
		),
		// Display only the specified layouts and color schemes.
		'fields'         => array(
			'layouts'         => array( 'simple' ),
			'schemes'         => array( 'default', 'bold' ),
			'count_locations' => array( 'inside' ),
		),
		'display_total'  => false,
		'layout'         => 'simple',
		'scheme'         => 'default',
		'count_location' => 'inside',
	);

	return $locations;
}
add_filter( 'powerkit_share_buttons_locations', 'csco_share_buttons_post_meta' );

/**
 * Register Post Featured Share Buttons Location
 *
 * @param array $locations List of Locations.
 */
function csco_share_buttons_featured_post( $locations = array() ) {

	$locations['featured_post'] = array(
		'shares'         => array( 'facebook', 'twitter', 'pinterest' ),
		'name'           => esc_html__( 'Featured Post', 'overflow' ),
		'location'       => 'featured_post',
		'mode'           => 'cached',
		'before'         => '',
		'after'          => '',
		'display'        => true,
		'meta'           => array(
			'icons'  => true,
			'titles' => false,
			'labels' => false,
		),
		// Display only the specified layouts and color schemes.
		'fields'         => array(
			'layouts'         => array( 'simple' ),
			'schemes'         => array( 'default', 'bold' ),
			'count_locations' => array( 'inside' ),
		),
		'display_total'  => false,
		'layout'         => 'simple',
		'scheme'         => 'default',
		'count_location' => 'inside',
	);

	return $locations;
}
add_filter( 'powerkit_share_buttons_locations', 'csco_share_buttons_featured_post' );

/**
 * Register Post Header Share Buttons Location
 *
 * @param array $locations List of Locations.
 */
function csco_share_buttons_post_header( $locations = array() ) {

	$locations['post_header'] = array(
		'shares'         => array( 'facebook', 'twitter', 'pinterest' ),
		'name'           => esc_html__( 'Post Header', 'overflow' ),
		'location'       => 'post_header',
		'mode'           => 'mixed',
		'before'         => '',
		'after'          => '',
		'display'        => true,
		'meta'           => array(
			'icons'  => true,
			'titles' => false,
			'labels' => false,
		),
		// Display only the specified layouts and color schemes.
		'fields'         => array(
			'display_total'   => true,
			'display_count'   => true,
			'layouts'         => array( 'default' ),
			'schemes'         => array( 'default', 'bold-bg', 'bold' ),
			'count_locations' => array( 'inside' ),
		),
		'layout'         => 'default',
		'scheme'         => 'bold-bg',
		'count_location' => 'inside',
	);

	return $locations;
}
add_filter( 'powerkit_share_buttons_locations', 'csco_share_buttons_post_header' );


/**
 * Register Floated Share Buttons Location
 *
 * @param array $locations List of Locations.
 */
function csco_share_buttons_post_sidebar( $locations = array() ) {

	$locations['post_sidebar'] = array(
		'shares'         => array( 'facebook', 'twitter', 'pinterest', 'mail' ),
		'name'           => esc_html__( 'Entry Sidebar', 'overflow' ),
		'location'       => 'post_sidebar',
		'mode'           => 'mixed',
		'before'         => '',
		'after'          => '',
		'display'        => true,
		'meta'           => array(
			'icons'  => true,
			'titles' => false,
			'labels' => false,
		),
		// Display only the specified layouts and color schemes.
		'fields'         => array(
			'display_total'   => true,
			'display_count'   => true,
			'layouts'         => array( 'simple' ),
			'schemes'         => array( 'default', 'bold-bg', 'bold' ),
			'count_locations' => array( 'inside' ),
		),
		'layout'         => 'simple',
		'scheme'         => 'bold-bg',
		'count_location' => 'inside',
	);

	unset( $locations['before-content'] );

	return $locations;
}
add_filter( 'powerkit_share_buttons_locations', 'csco_share_buttons_post_sidebar' );

/**
 * Change Total Output of Floated Share Buttons
 *
 * @param bool   $output  The output.
 * @param string $class   The class.
 * @param int    $count   The count.
 */
function csco_powerkit_share_buttons_total_output( $output, $class, $count ) {

	if ( false !== strpos( $class, 'pk-share-buttons-post_sidebar' ) ) {
		ob_start();
		?>
		<div class="pk-share-buttons-caption cs-font-secondary">
			<span class="pk-share-buttons-count"> <?php echo esc_html( $count ); ?> </span>
			<?php esc_html_e( 'people shared the story', 'overflow' ); ?>
		</div>
		<?php
		$output = ob_get_clean();
	}

	return $output;
}
add_filter( 'powerkit_share_buttons_total_output', 'csco_powerkit_share_buttons_total_output', 10, 3 );

/**
 * Register Floated Share Buttons Location
 */
function csco_powerkit_widget_author_image_size() {
	return 'csco-thumbnail-uncropped';
}
add_filter( 'powerkit_widget_author_image_size', 'csco_powerkit_widget_author_image_size' );

/**
 * Change Contributors widget post author description length.
 */
function csco_powerkit_widget_contributors_description_length() {
	return 80;
}
add_filter( 'powerkit_widget_contributors_description_length', 'csco_powerkit_widget_contributors_description_length' );

/**
 * Change Default Template for featured posts
 *
 * @param array $templates The templates.
 */
function csco_powerkit_featured_posts_default( $templates = array() ) {

	$templates['list']['func']     = 'csco_powerkit_featured_default_template';
	$templates['numbered']['func'] = 'csco_powerkit_featured_default_template';
	$templates['large']['func']    = 'csco_powerkit_featured_default_template';

	return $templates;
}
add_filter( 'powerkit_featured_posts_templates', 'csco_powerkit_featured_posts_default' );

/**
 * Add Slider Template for featured posts
 *
 * @param array $templates The templates.
 */
function csco_powerkit_featured_posts_slider( $templates = array() ) {

	$templates['inverse'] = array(
		'name' => esc_html__( 'Inverse', 'overflow' ),
		'func' => 'csco_powerkit_featured_default_template',
	);

	return $templates;
}
add_filter( 'powerkit_featured_posts_templates', 'csco_powerkit_featured_posts_slider' );

/**
 * Featured Default Template Callback
 *
 * @param  array $posts    Array of posts.
 * @param  array $params   Array of params.
 * @param  array $instance Widget instance.
 */
function csco_powerkit_featured_default_template( $posts, $params, $instance ) {

	$class = null;

	// Thumbnail size.
	switch ( $params['template'] ) {
		case 'large':
			$thumbnail_size = 'csco-thumbnail-alternative';
			break;
		default:
			$thumbnail_size = 'csco-small';
			break;
	}

	if ( 'inverse' === $params['template'] || 'large' === $params['template'] ) {

		$class_card = 'inverse' === $params['template'] ? 'cs-bg-dark' : '';
		?>
		<article <?php post_class(); ?>>
			<div class="pk-post-inner pk-overlay-thumbnail">
				<div class="cs-overlay cs-overlay-hover cs-overlay-ratio cs-ratio-landscape cs-bg-dark">
					<div class="cs-overlay-background">
						<?php the_post_thumbnail( 'csco-thumbnail-alternative' ); ?>
						<?php csco_get_video_background( 'inverse' ); ?>
					</div>
					<div class="cs-overlay-content">
						<?php csco_the_post_format_icon(); ?>

						<?php if ( 'large' === $params['template'] ) : ?>
							<span class="read-more"><?php echo esc_html( get_theme_mod( 'misc_label_readmore', __( 'Read More', 'overflow' ) ) ); ?></span>
						<?php endif; ?>

						<?php csco_get_post_meta( array( 'views', 'reading_time' ), (bool) $params['post_meta_compact'], true, $params['post_meta'] ); ?>
					</div>
					<a href="<?php the_permalink(); ?>" class="cs-overlay-link"></a>
				</div>
			</div>
			<div class="pk-post-inner pk-post-data <?php echo esc_attr( $class_card ); ?>">
				<div class="pk-data-wrap">
					<?php
					if ( function_exists( 'csco_get_post_meta' ) && $params['post_meta_category'] ) {
						csco_get_post_meta( 'category' );
					}
					?>

					<?php if ( get_the_title() ) { ?>
						<h5 class="h5 entry-title <?php echo esc_attr( 'inverse' === $params['template'] ? 'title-stroke' : '' ); ?>">
							<a href="<?php the_permalink(); ?>" title="<?php the_title_attribute(); ?>"><?php the_title(); ?></a>
						</h5>
					<?php } ?>

					<?php csco_get_post_meta( array( 'author', 'date', 'shares', 'comments' ), (bool) $params['post_meta_compact'], true, $params['post_meta'] ); ?>
				</div>
			</div>
		</article>
	<?php
	} else {
	?>
		<article <?php post_class(); ?>>
			<div class="pk-post-outer">
				<?php if ( has_post_thumbnail() ) { ?>
					<div class="pk-post-inner pk-post-thumbnail">
						<a href="<?php the_permalink(); ?>" class="post-thumbnail">
							<?php the_post_thumbnail( $thumbnail_size ); ?>

							<?php if ( 'numbered' === $params['template'] ) : ?>
								<span class="pk-post-number">
									<?php echo esc_html( $posts->current_post + 1 ); ?>
								</span>
							<?php endif; ?>
						</a>
					</div>
				<?php } ?>

				<div class="pk-post-inner pk-post-data">
					<?php
					if ( function_exists( 'csco_get_post_meta' ) && $params['post_meta_category'] ) {
						csco_get_post_meta( 'category' );
					}
					?>
					<h6 class="entry-title">
						<a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
					</h6>
					<?php csco_get_post_meta( array_diff( $params['post_meta'], array( 'category' ) ), (bool) $params['post_meta_compact'] ); ?>
				</div>
			</div>
		</article>
	<?php
	}
}

/**
 * Add new image selector for Lightbox
 *
 * @param string $selectors List selectors.
 */
function csco_powerkit_lightbox_image_selector( $selectors ) {
	$selectors[] = '.single .post-media img';

	return $selectors;
}

add_filter( 'powerkit_lightbox_image_selectors', 'csco_powerkit_lightbox_image_selector' );

/**
 * Exclude Inline Posts posts from related posts block
 *
 * @param array $args Array of WP_Query args.
 */
function csco_related_posts_args( $args ) {
	global $powerkit_inline_posts;
	if ( ! $powerkit_inline_posts ) {
		return $args;
	}
	$post__not_in         = $args['post__not_in'];
	$post__not_in         = array_unique( array_merge( $post__not_in, $powerkit_inline_posts ) );
	$args['post__not_in'] = $post__not_in;
	return $args;
}

add_filter( 'csco_related_posts_args', 'csco_related_posts_args' );

/**
 * Remove Default Styles
 */
add_action( 'wp_enqueue_scripts', function() {
	wp_dequeue_style( 'powerkit-widget-posts' );
} );
