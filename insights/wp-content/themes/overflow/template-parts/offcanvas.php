<?php
/**
 * The template part for displaying off-canvas area.
 *
 * @package Overflow
 */

$scheme = csco_light_or_dark( get_theme_mod( 'color_navbar_bg', '#FFFFFF' ), null, ' cs-bg-navbar-dark' );

if ( csco_offcanvas_exists() ) {
?>
	<div class="site-overlay"></div>

	<div class="offcanvas">

		<div class="offcanvas-header<?php echo esc_attr( $scheme ); ?>">

			<?php do_action( 'csco_offcanvas_header_start' ); ?>

			<nav class="navbar navbar-offcanvas">

				<?php
				$logo_id = get_theme_mod( 'logo' );

				if ( $logo_id ) {
					?>
					<a class="navbar-brand" href="<?php echo esc_url( home_url( '/' ) ); ?>" rel="home">
						<?php csco_get_retina_image( $logo_id, array( 'alt' => get_bloginfo( 'name' ) ) ); ?>
					</a>
					<?php
				} else {
					?>
					<a class="offcanvas-brand site-title" href="<?php echo esc_url( home_url( '/' ) ); ?>" rel="home"><?php bloginfo( 'name' ); ?></a>
					<?php
				}
				?>

				<button type="button" class="toggle-offcanvas">
					<i class="cs-icon cs-icon-x"></i>
				</button>

			</nav>

			<?php do_action( 'csco_offcanvas_header_end' ); ?>

		</div>

		<aside class="offcanvas-sidebar">
			<div class="offcanvas-inner widget-area">
				<?php
				$locations = get_nav_menu_locations();

				// Get menu by location.
				if ( isset( $locations['primary'] ) || isset( $locations['mobile'] ) ) {

					if ( isset( $locations['primary'] ) ) {
						$location = $locations['primary'];
					}
					if ( isset( $locations['mobile'] ) ) {
						$location = $locations['mobile'];
					}

					the_widget( 'WP_Nav_Menu_Widget', array( 'nav_menu' => $location ), array(
						'before_widget' => '<div class="widget %s cs-d-lg-none">',
						'after_widget'  => '</div>',
					) );
				}
				?>

				<?php dynamic_sidebar( 'sidebar-offcanvas' ); ?>
			</div>
		</aside>
	</div>
<?php
}
