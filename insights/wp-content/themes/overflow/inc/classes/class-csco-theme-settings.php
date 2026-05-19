<?php
/**
 * Theme Settings.
 *
 * @package Overflow
 */

if ( ! class_exists( 'CSCO_Theme_Settings' ) ) {

	/**
	 * This class to activate your theme and open up new opportunities.
	 */
	class CSCO_Theme_Settings {

		/**
		 * The current theme slug.
		 *
		 * @var string $theme The current theme slug.
		 */
		public $theme;

		/**
		 * The server domain.
		 *
		 * @var string $server The server domain.
		 */
		public $server;

		/**
		 * The message.
		 *
		 * @var string $msg The message.
		 */
		public $msg;

		/**
		 * Constructor.
		 */
		public function __construct() {
			$this->init();

			$this->trigger_subscribe();

			add_action( 'init', array( $this, 'redirect_settings' ) );
			add_action( 'admin_menu', array( $this, 'register_options_page' ) );
		}

		/**
		 * Initialization
		 */
		public function init() {
			// Set current theme slug.
			$this->theme = get_template();

			// Set server url.
			$this->server = $this->get_theme_data( 'AuthorURI' );
		}

		/**
		 * Get data about the theme.
		 *
		 * @param mixed $name The name of param.
		 */
		public function get_theme_data( $name ) {
			$data = wp_get_theme( $this->theme );

			return $data->get( $name );
		}

		/**
		 * Set message.
		 *
		 * @param string $text The text of message.
		 * @param string $type The type of message.
		 */
		public function set_message( $text, $type = 'error' ) {
			ob_start();
			?>
			<div class="notice notice-<?php echo esc_attr( $type ); ?>">
				<p><?php echo wp_kses( $text, 'post' ); ?></p>
			</div>
			<?php
			return ob_get_clean();
		}

		/**
		 * Register admin page
		 */
		public function register_options_page() {

			if ( $this->get_subscribe() ) {
				return;
			}

			add_theme_page( esc_html__( 'Theme Settings', 'overflow' ), esc_html__( 'Theme Settings', 'overflow' ), 'manage_options', 'csco-settings', array( $this, 'settings_page' ) );
		}

		/**
		 * Redirect to settings
		 */
		public function redirect_settings() {

			if ( $this->get_subscribe() ) {
				return;
			}

			global $pagenow;

			if ( is_admin() && 'themes.php' === $pagenow && isset( $_GET['activated'] ) ) {
				wp_safe_redirect( admin_url( 'themes.php?page=csco-settings' ) );
			}
		}

		/**
		 * Get option name with subscribe.
		 */
		public function get_subscribe() {
			return get_option( 'csco_subscribe' );
		}

		/**
		 * Update subscribe.
		 */
		public function update_subscribe() {
			return update_option( 'csco_subscribe', true );
		}

		/**
		 * Build admin page
		 */
		public function settings_page() {

			wp_verify_nonce( null );

			if ( ! current_user_can( 'manage_options' ) ) {
				wp_die( esc_html__( 'You do not have sufficient rights to view this page.', 'overflow' ) );
			}
			?>
				<div class="wrap">
					<h1><?php esc_html_e( 'Theme Settings', 'overflow' ); ?></h1>

					<?php
					// Message output.
					if ( $this->msg ) {
						echo wp_kses( $this->msg, 'post' );
					}
					?>
					<div id="poststuff">
						<div class="postbox">

							<h2 class="hndle"><span><?php esc_html_e( 'Updates', 'overflow' ); ?></span></h2>

							<div class="inside">
								<p style="font-size: 14px;margin-bottom: 0;"><?php esc_html_e( 'We set a special price for all new themes for just a few days. Get notified of all introductory, flash and seasonal sales by signing up to our updates.', 'overflow' ); ?></p>

								<form method="post" style="max-width:864px;">
									<?php wp_nonce_field(); ?>

									<table class="form-table">
										<!-- Email Address -->
										<tr>
											<th scope="row"><?php esc_html_e( 'Email Address', 'overflow' ); ?></label></th>
											<td>
												<input class="regular-text" type="text" name="email" value="<?php echo esc_attr( get_bloginfo( 'admin_email' ) ); ?>">
											</td>
										</tr>
										<!-- Updates -->
										<tr>
											<th scope="row"></th>
											<td>
											<div style="display:flex;">
												<input style="margin: 10px 15px 0 0;" id="newsletter" name="newsletter" type="checkbox" value="1">

												<label for="newsletter">
													<p><?php esc_html_e( 'By checking this box you agree to our', 'overflow' ); ?> <a href="https://codesupply.co/terms-and-conditions/" target="_blank"><?php esc_html_e( 'Terms and Conditions', 'overflow' ); ?></a> <?php esc_html_e( 'and', 'overflow' ); ?> <a href="https://codesupply.co/privacy-policy/" target="_blank"><?php esc_html_e( 'Privacy Policy', 'overflow' ); ?></a>.</p>

													<p class="description"><?php esc_html_e( 'You may opt-out any time by clicking the unsubscribe link in the footer of any email you receice from us, or by contacting us at', 'overflow' ); ?> <a target="_blank" href="mailto:support@codesupply.co"><?php esc_html_e( 'support@codesupply.co', 'overflow' ); ?></a>.</p>
												</label>
												</div>
											</td>
										</tr>
										<!-- Submitbox -->
										<tr>
											<th scope="row"></th>
											<td>
												<button name="action" value="subscribe" type="submit" class="button button-primary button-large" id="publish"><?php esc_html_e( 'Subscribe', 'overflow' ); ?></button>
											</td>
										</tr>
									</table>
								</form>
							</div>
						</div>
					</div>
				</div>
			<?php
		}

		/**
		 * Trigger subscribe.
		 */
		public function trigger_subscribe() {
			if ( ! isset( $_POST['_wpnonce'] ) || ! wp_verify_nonce( $_POST['_wpnonce'] ) ) { // Input var ok; sanitization ok.
				return;
			}

			$email = null;

			// Get action.
			if ( ! isset( $_POST['action'] ) ) { // Input var ok.
				return;
			}

			$action = sanitize_text_field( $_POST['action'] ); // Input var ok; sanitization ok.

			// Get email.
			if ( 'subscribe' === $action ) {
				if ( isset( $_POST['email'] ) && $_POST['email'] ) { // Input var ok; sanitization ok.
					$email = sanitize_email( wp_unslash( $_POST['email'] ) ); // Input var ok.
				} else {
					$this->msg = $this->set_message( esc_html__( 'Email address is considered invalid.', 'overflow' ) );
					return;
				}

				if ( ! isset( $_POST['newsletter'] ) ) { // Input var ok; sanitization ok.
					$this->msg = $this->set_message( esc_html__( 'Please agree to our terms and conditions.', 'overflow' ) );
					return;
				}

				// Get url server.
				$remote_url = sprintf( '%s/wp-json/csco/v1/simple-subscribe', $this->server );

				// Remote query.
				$response = wp_remote_post(
					$remote_url,
					array(
						'timeout'     => 45,
						'redirection' => 5,
						'httpversion' => '1.0',
						'blocking'    => true,
						'headers'     => array(),
						'body'        => array(
							'action' => $action,
							'email'  => $email,
						),
						'cookies'     => array(),
					)
				);

				if ( is_wp_error( $response ) ) {
					$this->msg = $this->set_message( esc_html__( 'No connection to the server, try another time, or contact support.', 'overflow' ) );
					return;
				}

				// Retrieve data.
				$data = wp_remote_retrieve_body( $response );

				// JSON Decode.
				$data = json_decode( $data, true );

				// Subscribe.
				if ( isset( $data['data']['subscribe'] ) && $data['data']['subscribe'] ) {
					$this->update_subscribe();

					wp_safe_redirect( admin_url( 'index.php' ) );

					exit();
				}

				// Output message.
				if ( ! isset( $data['message'] ) ) {
					$this->msg = $this->set_message( esc_html__( 'Could not receive data from the server, try another time, or contact support.', 'overflow' ) );
				} else {
					$this->msg = $data['message'];
				}
			}
		}
	}

	new CSCO_Theme_Settings();
}
