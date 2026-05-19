<?php
/**
 * Header bar template part.
 *
 * @package AMP
 */

?>

<?php $scheme = csco_light_or_dark( get_theme_mod( 'color_navbar_bg', '#FFFFFF' ), null, ' cs-bg-dark' ); ?>

<header id="top" class="amp-wp-header <?php echo esc_attr( $scheme ); ?>" style="background-color:<?php echo esc_attr( get_theme_mod( 'color_navbar_bg', '#FFFFFF' ) ); ?>;">
	<div class="navbar-content">
		<?php
		$logo_id = get_theme_mod( 'logo' );
		if ( $logo_id ) {
			?>
			<a class="navbar-brand" href="<?php echo esc_url( home_url( '/' ) ); ?>" rel="home">
				<?php csco_get_retina_image( $logo_id, array( 'alt' => get_bloginfo( 'name' ) ), 'amp-img' ); ?>
			</a>
			<?php
		} else {
			?>
			<a class="navbar-brand site-title" href="<?php echo esc_url( home_url( '/' ) ); ?>" rel="home"><?php bloginfo( 'name' ); ?></a>
			<?php
		}
		$description = get_bloginfo( 'description' );
		if ( $description ) {
			?>
			<p class="navbar-text site-description"><?php echo wp_kses( $description, 'post' ); ?></p>
			<?php
		}
		?>
	</div>
</header>
