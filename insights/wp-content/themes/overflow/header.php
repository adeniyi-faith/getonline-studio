<?php
/**
 * The template for displaying the header
 *
 * Displays all of the head element and everything up until the "site-content" div.
 *
 * @package Overflow
 */

?>
<!doctype html>
<html <?php language_attributes(); ?>>
<head>
	<meta charset="<?php bloginfo( 'charset' ); ?>">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<link rel="profile" href="http://gmpg.org/xfn/11">

	<?php wp_head(); ?>
</head>

<body <?php body_class(); ?>>

<?php do_action( 'csco_site_before' ); ?>

<div id="page" class="site">

	<?php do_action( 'csco_site_start' ); ?>

	<div class="site-inner">

		<?php do_action( 'csco_header_before' ); ?>

		<header id="masthead" class="site-header">
			<?php
			if ( 'large' === get_theme_mod( 'header_layout', 'large' ) ) {
				get_template_part( 'template-parts/headers/header-large' );
			} else {
				get_template_part( 'template-parts/headers/header-default' );
			}
			?>
		</header><!-- #masthead -->

		<?php do_action( 'csco_header_after' ); ?>

		<div class="site-primary">

			<?php do_action( 'csco_site_content_before' ); ?>

			<div <?php csco_site_content_class(); ?>>

				<?php do_action( 'csco_site_content_start' ); ?>

				<div class="cs-container">

					<?php do_action( 'csco_main_content_before' ); ?>

					<div id="content" class="main-content">

						<?php do_action( 'csco_main_content_start' ); ?>
