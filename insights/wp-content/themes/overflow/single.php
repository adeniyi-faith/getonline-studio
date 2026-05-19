<?php
/**
 * The template for displaying all single posts and attachments.
 *
 * @package Overflow
 */

get_header(); ?>

	<div id="primary" class="content-area">

		<?php do_action( 'csco_main_before' ); ?>

		<main id="main" class="site-main">

			<?php do_action( 'csco_main_start' ); ?>

			<?php
			while ( have_posts() ) :
				the_post();
				?>

				<?php do_action( 'csco_post_before' ); ?>

					<?php get_template_part( 'template-parts/content-singular' ); ?>

				<?php do_action( 'csco_post_after' ); ?>

			<?php endwhile; ?>

			<?php do_action( 'csco_main_end' ); ?>

		</main>

		<?php do_action( 'csco_main_after' ); ?>

	</div><!-- .content-area -->

<?php get_sidebar(); ?>
<?php get_footer(); ?>
