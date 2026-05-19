<?php
/**
 * The template part for displaying post subscribe section.
 *
 * @package Overflow
 */

// Subscription.
$subscription_form = get_theme_mod( 'post_subscribe', false );

if ( shortcode_exists( 'powerkit_subscription_form' ) && $subscription_form ) {
	$title = get_theme_mod( 'post_subscribe_title', esc_html__( 'Sign Up for Our Newsletters', 'overflow' ) );
	$name  = get_theme_mod( 'post_subscribe_name', false );

	do_action( 'csco_post_subscribe_before' );
	?>
	<div class="post-subscribe">

		<div class="subscribe-wrap">
			<?php if ( $title ) { ?>
				<div class="subscribe-title">
					<div class="subscribe-text">
						<?php echo wp_kses( $title, 'post' ); ?>
					</div>

					<div class="subscribe-arrow">
						<?php csco_design_arrow(); ?>
					</div>
				</div>
			<?php } ?>

			<?php echo do_shortcode( sprintf( '[powerkit_subscription_form display_name="%s" text=""]', $name ) ); ?>
		</div>

	</div>

	<?php
	do_action( 'csco_post_subscribe_after' );
}
