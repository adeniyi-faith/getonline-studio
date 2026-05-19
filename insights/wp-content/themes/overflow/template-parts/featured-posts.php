<?php
/**
 * Template part for displaying featured posts section.
 *
 * @package Overflow
 */

if ( is_category() ) {
	$ids = csco_get_category_posts_ids();
} else {
	$ids = csco_get_homepage_posts_ids();
}

if ( $ids ) {
	$args = array(
		'ignore_sticky_posts' => true,
		'post__in'            => $ids,
		'posts_per_page'      => count( $ids ),
		'post_type'           => array( 'post', 'page' ),
		'orderby'             => 'post__in',
	);

	$the_query = new WP_Query( $args );
}

// Determines whether there are more posts available in the loop.
if ( $ids && $the_query->have_posts() ) {

	if ( is_category() ) {
		$more_button  = get_theme_mod( 'category_featured_posts_more_button', true );
		$type         = get_theme_mod( 'category_featured_posts_type', 'type-3' );
		$meta_setting = 'category_featured_posts_meta';
	} else {
		$more_button  = get_theme_mod( 'featured_posts_more_button', true );
		$type         = get_theme_mod( 'featured_posts_type', 'type-2' );
		$meta_setting = 'featured_posts_meta';
	}

	do_action( 'csco_featured_posts_before' );
	?>

	<div class="section-featured-posts cs-posts-<?php echo esc_attr( $type ); ?>">

		<?php do_action( 'csco_featured_posts_start' ); ?>

			<div class="cs-featured-posts cs-featured-<?php echo esc_attr( $type ); ?>">
				<div class="cs-featured-container">
					<div class="cs-featured-posts">
						<?php
						while ( $the_query->have_posts() ) :
							$the_query->the_post();

							$class_overlay  = 'cs-ratio-landscape cs-overlay-no-hover';
							$class_card     = null;
							$meta_thumbnail = array();
							$meta_card      = array( 'author', 'date', 'shares', 'comments', 'views', 'reading_time' );

							// Settings.
							switch ( $type ) {
								case 'type-1':
									$thumbnail_size = 'csco-extra-large';
									$class_overlay  = 'cs-ratio-wide cs-overlay-transparent';
									break;
								case 'type-2':
									$thumbnail_size = 'csco-large';
									$class_overlay  = 'cs-ratio-standard cs-overlay-transparent';
									break;
								case 'type-3':
									$thumbnail_size = 'csco-medium';
									$class_overlay  = 'cs-ratio-landscape cs-overlay-transparent';
									$class_card     = 'cs-bg-dark';
									break;
								default:
									$thumbnail_size = 'csco-thumbnail';
									$class_overlay  = 'cs-ratio-landscape cs-overlay-hover';
									$meta_thumbnail = array( 'views', 'reading_time' );
									$meta_card      = array( 'author', 'date', 'shares', 'comments' );
									break;
							}
							?>
							<div class="cs-featured-post">
								<article <?php post_class(); ?>>
									<div class="cs-overlay cs-overlay-ratio cs-bg-dark <?php echo esc_attr( $class_overlay ); ?>">
										<div class="cs-overlay-background">
											<?php
											the_post_thumbnail( $thumbnail_size, array(
												'class' => 'pk-lazyload-disabled',
											) );
											?>
											<?php csco_get_video_background( 'featured' ); ?>
										</div>

										<?php
										ob_start();
										if ( 'type-3' === $type || 'type-4' === $type ) {
											csco_the_post_format_icon();
										}

										if ( 'post' === get_post_type() ) {
											csco_get_post_meta( $meta_thumbnail, false, true, $meta_setting );
										}

										$overlay_content = ob_get_clean();

										if ( $overlay_content ) {
										?>
											<div class="cs-overlay-content">
												<?php echo (string) $overlay_content; // XSS. ?>
											</div>
										<?php } ?>

										<a href="<?php the_permalink(); ?>" class="cs-overlay-link"></a>
									</div>
									<div class="cs-card <?php echo esc_attr( $class_card ); ?>">
										<div class="cs-card-inner">
											<?php
											if ( 'post' === get_post_type() ) {
												csco_get_post_meta( 'category', false, true, $meta_setting );
											}
											?>

											<?php if ( get_the_title() ) { ?>
												<h2 class="h4 entry-title title-stroke">
													<a href="<?php the_permalink(); ?>" title="<?php the_title_attribute(); ?>"><?php the_title(); ?></a>
												</h2>
											<?php } ?>

											<?php
											if ( 'post' === get_post_type() ) {
												csco_get_post_meta( $meta_card, false, true, $meta_setting );
											}
											?>

											<?php if ( 'type-1' === $type || 'type-2' === $type || 'type-3' === $type ) { ?>
												<div class="entry-excerpt">
													<?php the_excerpt(); ?>
												</div>
											<?php } ?>

											<?php
											$post_share = false;

											if ( 'post' === get_post_type() ) {
												$post_share = csco_powerkit_module_enabled( 'share_buttons' ) && powerkit_share_buttons_exists( 'featured_post' );
											}

											if ( $more_button || $post_share ) {
											?>
												<div class="entry-details">
													<?php if ( $more_button ) { ?>
														<div class="entry-more entry-more-dark">
															<a class="button cs-link-more" href="<?php echo esc_url( get_permalink() ); ?>">
																<?php echo esc_html( get_theme_mod( 'misc_label_readmore', __( 'Read More', 'overflow' ) ) ); ?>
															</a>
														</div>
													<?php } ?>

													<?php if ( $post_share ) { ?>
														<div class="post-share">
															<?php powerkit_share_buttons_location( 'featured_post' ); ?>
														</div>
													<?php } ?>
												</div>
											<?php } ?>
										</div>
									</div>
								</article>
							</div>
							<?php
						endwhile;
						?>
					</div>
				</div>
			</div>

		<?php do_action( 'csco_featured_posts_end' ); ?>

	</div>

	<?php wp_reset_postdata(); ?>

<?php
}

do_action( 'csco_featured_posts_after' );
