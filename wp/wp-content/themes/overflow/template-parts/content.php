<?php
/**
 * Template part for displaying posts
 *
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/
 *
 * @package Overflow
 */

global $wp_query;

$post_type = get_post_type();

// Var Archive type.
if ( get_query_var( 'csco_archive_layout' ) ) {
	$archive_layout = get_theme_mod( get_query_var( 'csco_archive_layout' ), 'list' );
} else {
	$archive_layout = get_theme_mod( csco_get_archive_option( 'layout' ), 'list' );
}
?>

<article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
	<div class="post-outer">

		<?php
		$orientation = 'cs-overlay-ratio cs-ratio-landscape';
		$image_size  = 'csco-thumbnail';

		if ( 'masonry' === $archive_layout ) {
			$image_size  = 'csco-thumbnail-uncropped';
			$orientation = null;
		}

		if ( 'list' === $archive_layout && 'disabled' === csco_get_page_sidebar() ) {
			$image_size = 'csco-medium';
		}
		?>

		<?php if ( has_post_thumbnail() ) { ?>
		<div class="post-inner">
			<div class="entry-thumbnail">
				<div class="cs-overlay cs-overlay-hover  cs-bg-dark <?php echo esc_attr( $orientation ); ?>">
					<div class="cs-overlay-background">
						<?php the_post_thumbnail( $image_size ); ?>
						<?php csco_get_video_background( 'archive' ); ?>
					</div>
					<?php if ( 'post' === $post_type ) { ?>
					<div class="cs-overlay-content">
						<span class="read-more"><?php echo esc_html( get_theme_mod( 'misc_label_readmore', __( 'Read More', 'overflow' ) ) ); ?></span>
						<?php csco_get_post_meta( array( 'views', 'reading_time' ), false, true, true ); ?>
						<?php csco_the_post_format_icon(); ?>
					</div>
					<?php } ?>
					<a href="<?php the_permalink(); ?>" class="cs-overlay-link"></a>
				</div>
			</div>
		</div>
		<?php } ?>

		<div class="post-inner">
			<header class="entry-header">
				<?php csco_get_post_meta( 'category', false, true, true ); ?>

				<?php

				// Post Title.
				the_title( '<h2 class="entry-title"><a href="' . esc_url( get_permalink() ) . '" rel="bookmark">', '</a></h2>' );

				// Post Meta.
				if ( 'post' === $post_type ) {
					csco_get_post_meta( array( 'author', 'date', 'shares', 'comments' ), false, true, true );
				}
				?>
			</header><!-- .entry-header -->

			<div class="entry-excerpt">
				<?php
				the_excerpt();
				?>
			</div><!-- .entry-excerpt -->

			<?php
			$more_button = get_theme_mod( csco_get_archive_option( 'more_button' ), true );
			$post_share  = csco_powerkit_module_enabled( 'share_buttons' ) && powerkit_share_buttons_exists( 'post_meta' );

			if ( $more_button || $post_share ) {
			?>
				<div class="entry-details">
					<?php if ( $more_button ) { ?>
						<div class="entry-more">
							<a class="button cs-link-more" href="<?php echo esc_url( get_permalink() ); ?>">
								<?php echo esc_html( get_theme_mod( 'misc_label_readmore', __( 'Read More', 'overflow' ) ) ); ?>
							</a>
						</div>
					<?php } ?>

					<?php if ( $post_share ) { ?>
						<div class="post-share">
							<?php powerkit_share_buttons_location( 'post_meta' ); ?>
						</div>
					<?php } ?>
				</div>
			<?php } ?>

		</div><!-- .post-inner -->

	</div><!-- .post-outer -->
</article><!-- #post-<?php the_ID(); ?> -->
