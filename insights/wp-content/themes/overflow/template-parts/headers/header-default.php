<?php
/**
 * The template part for displaying default header layout.
 *
 * @package Overflow
 */

?>

<?php $scheme = csco_light_or_dark( get_theme_mod( 'color_navbar_bg', '#FFFFFF' ), null, ' cs-bg-navbar-dark' ); ?>

<nav class="navbar navbar-primary">

	<?php do_action( 'csco_navbar_start' ); ?>

	<div class="navbar-wrap <?php echo esc_attr( $scheme ); ?>">

		<div class="navbar-container">

			<div class="navbar-content">

				<?php do_action( 'csco_navbar_content' ); ?>

			</div><!-- .navbar-content -->

		</div><!-- .navbar-container -->

	</div><!-- .navbar-wrap -->

	<?php do_action( 'csco_navbar_end' ); ?>

</nav><!-- .navbar -->
