<?php
/**
 * The template for displaying the footer
 *
 * Contains the closing of the #content div and all content after
 *
 * @package Overflow
 */

?>

						<?php do_action( 'csco_main_content_end' ); ?>

					</div><!-- .main-content -->

					<?php do_action( 'csco_main_content_after' ); ?>

				</div><!-- .cs-container -->

				<?php do_action( 'csco_site_content_end' ); ?>

			</div><!-- .site-content -->

			<?php do_action( 'csco_site_content_after' ); ?>

			<?php do_action( 'csco_footer_before' ); ?>

			<footer id="colophon" class="site-footer">
				<?php
				// Subscription.
				$subscription_form = get_theme_mod( 'footer_subscribe', false );

				if ( shortcode_exists( 'powerkit_subscription_form' ) && $subscription_form ) {
					$title = get_theme_mod( 'footer_subscribe_title', esc_html__( 'Subscribe to Our Newsletter', 'overflow' ) );
					$name  = get_theme_mod( 'footer_subscribe_name', false );
					?>
					<div class="footer-subscribe">
						<div class="cs-container">
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
					</div>
					<?php
				}
				?>

				<?php
				// Instagram Timeline.
				$username = get_theme_mod( 'footer_instagram_username' );

				if ( $username && csco_powerkit_module_enabled( 'instagram_integration' ) ) {
					?>
					<div class="footer-instagram">
						<?php
							powerkit_instagram_get_recent( array(
								'user_id' => $username,
								'number'  => apply_filters( 'csco_instagram_footer_number', 6 ),
								'columns' => apply_filters( 'csco_instagram_footer_columns', 6 ),
								'size'    => 'small',
								'target'  => '_blank',
							) );
						?>
						<a class="instagram-username" target="_blank" href="https://www.instagram.com/<?php echo esc_attr( $username ); ?>">
							@<?php echo esc_html( $username ); ?>
						</a>
					</div>
					<?php
				}
				?>

				<div class="footer-info">

					<div class="site-info cs-bg-dark">
						<?php
						// Logo.
						$logo_id = get_theme_mod( 'footer_logo' );
						if ( $logo_id ) {
							?>
							<span class="site-title footer-title" href="<?php echo esc_url( home_url( '/' ) ); ?>" rel="home">
								<?php csco_get_retina_image( $logo_id, array( 'alt' => get_bloginfo( 'name' ) ) ); ?>
							</span>
							<?php
						} else {
							?>
							<div class="footer-title"><?php echo wp_kses_post( get_bloginfo( 'name' ) ); ?></div>
							<?php
						}
						?>

						<?php
						// Navigation.
						if ( has_nav_menu( 'footer' ) ) {
							wp_nav_menu(
								array(
									'theme_location'  => 'footer',
									'menu_class'      => 'navbar-nav',
									'container'       => 'nav',
									'container_class' => 'navbar-footer',
									'depth'           => 1,
								)
							);
						}
						?>

						<?php
						// Social links.
						$social_in_footer = get_theme_mod( 'footer_social_links', false );
						if ( csco_powerkit_module_enabled( 'social_links' ) && $social_in_footer ) {
							$scheme  = get_theme_mod( 'footer_social_links_scheme', 'light' );
							$maximum = get_theme_mod( 'footer_social_links_maximum', 4 );
							$counts  = get_theme_mod( 'footer_social_links_counts', true );

							powerkit_social_links( false, false, $counts, 'nav', $scheme, 'mixed', $maximum );
						}
						?>

						<?php
						/* translators: %s: Author name. */
						$footer_text = get_theme_mod( 'footer_text', sprintf( esc_html__( 'Designed & Developed by %s', 'overflow' ), '<a href="' . esc_url( csco_get_theme_data( 'AuthorURI' ) ) . '">Code Supply Co.</a>' ) );
						if ( $footer_text ) {
							?>
							<div class="footer-copyright">
								<?php echo do_shortcode( $footer_text ); ?>
							</div>
							<?php
						}
						?>
					</div>

				</div>

			</footer>

			<?php do_action( 'csco_footer_after' ); ?>

		</div>

	</div><!-- .site-inner -->

	<?php do_action( 'csco_site_end' ); ?>

</div><!-- .site -->

<?php do_action( 'csco_site_after' ); ?>

<?php wp_footer(); ?>
</body>
</html>
