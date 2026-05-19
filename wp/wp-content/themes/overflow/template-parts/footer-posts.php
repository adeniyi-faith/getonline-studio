<?php
/**
 * Template part for displaying footer posts section.
 *
 * @package Overflow
 */

do_action( 'csco_footer_posts_before' );

$ids = csco_get_footer_posts_ids();

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
?>

<div class="section-footer-posts">

	<?php do_action( 'csco_footer_posts_start' ); ?>

		<div class="cs-container">

			<div class="cs-footer-posts">
				<?php
				while ( $the_query->have_posts() ) :
					$the_query->the_post();
					?>
					<div class="cs-footer-post">
						<article <?php post_class(); ?>>
							<div class="cs-overlay cs-overlay-hover cs-overlay-ratio cs-ratio-landscape cs-bg-dark">
								<div class="cs-overlay-background">
									<?php the_post_thumbnail( 'csco-thumbnail-alternative' ); ?>
									<?php csco_get_video_background( 'footer' ); ?>
								</div>
								<div class="cs-overlay-content">
									<?php if ( 'post' === get_post_type() ) : ?>
										<?php csco_the_post_format_icon(); ?>
										<span class="read-more"><?php echo esc_html( get_theme_mod( 'misc_label_readmore', __( 'Read More', 'overflow' ) ) ); ?></span>
										<?php csco_get_post_meta( array( 'views', 'reading_time' ), false, true, 'footer_featured_posts_meta' ); ?>
									<?php endif ?>
								</div>
								<a href="<?php the_permalink(); ?>" class="cs-overlay-link"></a>
							</div>
							<div class="cs-card">
								<?php
								if ( 'post' === get_post_type() ) {
									csco_get_post_meta( 'category', false, true, 'footer_featured_posts_meta' );
								}
								?>
								<h2 class="h5 entry-title">
									<a href="<?php the_permalink(); ?>" title="<?php the_title_attribute(); ?>"><?php the_title(); ?></a>
								</h2>
								<?php
								if ( 'post' === get_post_type() ) {
									csco_get_post_meta( array( 'author', 'date', 'shares', 'comments' ), false, true, 'footer_featured_posts_meta' );
								}
								?>
							</div>
						</article>
					</div>
					<?php
				endwhile;
				?>
			</div>

			<?php wp_reset_postdata(); ?>

		</div>

	<?php do_action( 'csco_footer_posts_end' ); ?>

</div>

<?php
}

do_action( 'csco_footer_posts_after' );
