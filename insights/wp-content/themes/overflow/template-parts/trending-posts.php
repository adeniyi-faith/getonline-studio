<?php
/**
 * Template part for displaying trending posts section.
 *
 * @package Overflow
 */

do_action( 'csco_trending_posts_before' );
?>

<div class="section-trending-posts">

	<?php do_action( 'csco_trending_posts_start' ); ?>

		<div class="cs-container">

			<?php
			$ids = csco_get_trending_posts_ids();

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
				$title = get_theme_mod( 'trending_featured_posts_title', esc_html__( 'Check Out These Posts', 'overflow' ) );
				?>

				<div class="cs-trending-wrap">
					<?php if ( $title ) { ?>
						<div class="trending-title">
							<div class="trending-text">
								<?php echo wp_kses( $title, 'post' ); ?>
							</div>

							<div class="trending-arrow">
								<?php csco_design_arrow(); ?>
							</div>
						</div>
					<?php } ?>

					<div class="cs-trending-posts">
						<?php
						while ( $the_query->have_posts() ) :
							$the_query->the_post();
							?>
							<div class="cs-trending-post">
								<article <?php post_class(); ?>>
									<div class="cs-post-outer">
										<?php if ( has_post_thumbnail() ) { ?>
											<div class="cs-post-inner cs-post-thumbnail">
												<a href="<?php the_permalink(); ?>" class="post-thumbnail">
													<?php the_post_thumbnail( 'csco-small' ); ?>

													<span class="cs-post-number">
														<?php echo esc_html( $the_query->current_post + 1 ); ?>
													</span>
												</a>
											</div>
										<?php } ?>

										<div class="cs-post-inner cs-post-data">
											<?php
											if ( 'post' === get_post_type() ) {
												csco_get_post_meta( 'category' );
											}
											?>
											<h6 class="entry-title">
												<a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
											</h6>
										</div>
									</div>
								</article>
							</div>
							<?php
						endwhile;
						?>
					</div>

				</div>

				<?php wp_reset_postdata(); ?>

			<?php } ?>

		</div>

	<?php do_action( 'csco_trending_posts_end' ); ?>

</div>

<?php

do_action( 'csco_trending_posts_after' );
