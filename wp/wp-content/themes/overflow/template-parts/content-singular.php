<?php
/**
 * Template part singular content
 *
 * @package Overflow
 */

$post_type = get_post_type();

$layout_type = 'excerpt' === get_theme_mod( csco_get_archive_option( 'summary' ), 'excerpt' ) ? 'layout-full' : 'entry';
?>

<article id="post-<?php the_ID(); ?>" <?php post_class( is_singular() ? 'entry' : $layout_type ); ?>>

	<!-- Full Post Layout -->
	<?php
	if ( ! is_singular() ) {
		csco_post_media();
		?>
			<?php csco_get_post_meta( 'category', false, true, true ); ?>

			<div class="entry-header">
				<?php do_action( 'csco_singular_entry_header_start' ); ?>

				<?php the_title( '<h2 class="entry-title"><a href="' . esc_url( get_permalink() ) . '" rel="bookmark">', '</a></h2>' ); ?>

				<?php
				// Post Meta.
				if ( 'post' === $post_type ) {
					csco_get_post_meta( array( 'author', 'date', 'shares', 'comments' ), false, true, true );
				}
				?>

				<?php do_action( 'csco_singular_entry_header_end' ); ?>
			</div>
		<?php
	}
	?>

	<?php do_action( 'csco_singular_content_before' ); ?>

	<!-- Full Post Layout and Full Content -->
	<div class="entry-content-wrap">

		<?php do_action( 'csco_singular_content_start' ); ?>

		<div class="entry-content">

			<?php
			if ( ! is_singular() && 'excerpt' === get_theme_mod( csco_get_archive_option( 'summary' ), 'excerpt' ) ) {
				the_excerpt();

				$more_button = get_theme_mod( csco_get_archive_option( 'more_button' ), true );
				$post_meta   = csco_get_post_meta( array( 'views', 'reading_time' ), false, false, true );
				$post_share  = csco_powerkit_module_enabled( 'share_buttons' ) && powerkit_share_buttons_exists( 'post_meta' );

				if ( $more_button || $post_meta || $post_share ) {
				?>
				<div class="entry-details">
					<?php
					// Post Meta.
					if ( 'post' === $post_type ) {
						csco_get_post_meta( array( 'views', 'reading_time' ), false, true, true );
					}

					// More Button.
					if ( $more_button ) {
						?>
						<div class="entry-more">
							<a class="button cs-link-more" href="<?php echo esc_url( get_permalink() ); ?>">
								<?php echo esc_html( get_theme_mod( 'misc_label_readmore', __( 'Read More', 'overflow' ) ) ); ?>
							</a>
						</div>
						<?php
					}
					?>

					<?php if ( $post_share ) { ?>
						<div class="post-share">
							<?php powerkit_share_buttons_location( 'post_meta' ); ?>
						</div>
					<?php } ?>
				</div>
				<?php
				}
			} else {
				$more_link_text = false;

				if ( get_theme_mod( csco_get_archive_option( 'more_button' ), true ) ) {
					$more_link_text = sprintf(
						/* translators: %s: Name of current post */
						__( 'Continue reading<span class="screen-reader-text"> "%s"</span>', 'overflow' ),
						get_the_title()
					);
				}

				the_content( $more_link_text );
			}
			?>

		</div>
		<?php do_action( 'csco_singular_content_end' ); ?>
	</div>

	<?php do_action( 'csco_singular_content_after' ); ?>

</article>
