<?php
/**
 * The template part for displaying post header section.
 *
 * @package Overflow
 */

$page_header = csco_get_page_header_type();

$class = sprintf( 'entry-header-%s', $page_header );

// Check if post has an image attached.
if ( has_post_thumbnail() ) {
	$class .= ' entry-header-thumbnail';
}
?>

<section class="entry-header <?php echo esc_attr( $class ); ?>">

	<div class="entry-header-inner">

		<?php do_action( 'csco_singular_entry_header_start' ); ?>

		<?php if ( is_singular( 'post' ) ) { ?>
			<div class="entry-inline-meta">
				<?php csco_get_post_meta( 'category', false, true, 'post_meta' ); ?>
			</div>
		<?php } ?>

		<?php if ( get_the_title() ) { ?>
			<?php the_title( '<h1 class="entry-title' . ( is_singular() ? ' title-stroke' : null ) . '">', '</h1>' ); ?>
		<?php } ?>

		<?php
		if ( is_singular( 'post' ) ) {
			csco_get_post_meta( array( 'views', 'reading_time' ), false, true, 'post_meta' );

			csco_post_media();
		}
		?>

		<?php
		if ( 'page' === get_post_type() ) {
			$excerpt_enabled = get_theme_mod( 'page_excerpt', true ) && has_excerpt();
		} else {
			$excerpt_enabled = get_theme_mod( 'post_excerpt', true ) && has_excerpt();
		}

		if ( $excerpt_enabled ) {
			?>
			<div class="post-excerpt"><?php the_excerpt(); ?></div>
			<?php
		}
		?>

		<?php
		if ( is_singular( 'post' ) ) {
			$post_shares = csco_has_post_meta( 'shares' ) && csco_powerkit_module_enabled( 'share_buttons' ) && powerkit_share_buttons_exists( 'post_header' );

			if ( csco_has_post_meta( 'author' ) || csco_has_post_meta( 'date' ) || csco_has_post_meta( 'comments' ) || $post_shares ) {
			?>
				<div class="entry-meta-details">
					<?php
					add_filter( 'csco_meta_avatar_size', 'csco_post_header_avatar_size' );

					csco_get_post_meta( array( 'author', 'date', 'comments' ), false, true, 'post_meta' );

					$delete_filter = sprintf( 'remove_%s', 'filter' );

					$delete_filter( 'csco_meta_avatar_size', 'csco_post_header_avatar_size' );

					if ( $post_shares ) {
						powerkit_share_buttons_location( 'post_header' );
					}
					?>
				</div>
			<?php
			}
		}
		?>

	</div>

</section>
