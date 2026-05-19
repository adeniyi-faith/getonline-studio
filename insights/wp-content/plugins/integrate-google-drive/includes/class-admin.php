<?php

namespace IGD;

defined( 'ABSPATH' ) || exit;

class Admin {
	/**
	 * @var null
	 */
	protected static $instance = null;

	private $pages = [];

	public function __construct() {
		add_action( 'admin_menu', [ $this, 'admin_menu' ] );

		add_action( 'admin_init', [ $this, 'init_update' ] );

		// Remove admin notices from plugin pages
		add_action( 'admin_init', [ $this, 'show_review_popup' ] );

		// admin body class
		add_filter( 'admin_body_class', [ $this, 'admin_body_class' ] );

		//Handle custom app authorization
		add_action( 'admin_init', [ $this, 'app_authorization' ] );

		add_action( 'admin_notices', [ $this, 'display_notices' ] );

		// Redirect URL after activation
		igd_fs()->add_filter( 'connect_url', [ $this, 'redirect_after_activation' ] );

	}

	public function redirect_after_activation( $url ) {

		try {
			$className = 'Freemius';
			$reflector = new \ReflectionClass( $className );
			$file      = $reflector->getFileName();

			// Return original URL if file is not found or not inside 'integrate-google-drive' directory
			if ( $file === false || strpos( $file, 'integrate-google-drive' ) === false ) {
				return $url;
			}

		} catch ( \ReflectionException $e ) {
			// Optionally log the error here for debugging
			return $url;
		}

		if ( igd_fs()->is_premium() ) {
			return $url;
		}

		// Parse the URL into its components
		$url_parts = wp_parse_url( $url );

		// Parse existing query parameters into an array, or initialize empty if none
		$query_params = [];
		if ( ! empty( $url_parts['query'] ) ) {
			parse_str( $url_parts['query'], $query_params );
		}

		// Set or replace the 'page' parameter
		$query_params['page'] = 'integrate-google-drive-getting-started';

		// Rebuild the query string with updated parameters
		$url_parts['query'] = http_build_query( $query_params );

		// Rebuild the full URL safely
		$new_url = $url_parts['scheme'] . '://' . $url_parts['host'];

		if ( ! empty( $url_parts['path'] ) ) {
			$new_url .= $url_parts['path'];
		}

		if ( ! empty( $url_parts['query'] ) ) {
			$new_url .= '?' . $url_parts['query'];
		}

		if ( ! empty( $url_parts['fragment'] ) ) {
			$new_url .= '#' . $url_parts['fragment'];
		}


		// Escape the URL before returning
		return esc_url_raw( $new_url );
	}

	public function display_notices() {

		if ( ! current_user_can( 'manage_options' ) ) {
			return;
		}

		// Needs migration notice
		$migration_status = get_option( 'igd_migration_1_5_1_status' );

		if ( 'run' === $migration_status || 'running' === $migration_status ) {
			ob_start();
			include IGD_INCLUDES . '/views/notice/migration-1.5.1.php';
			$notice_html = ob_get_clean();

			igd()->add_notice( 'warning igd-migration-notice warning', $notice_html );

			return;
		}

		// Account authentication lost notice
		$accounts = Account::instance()->get_accounts();
		if ( ! empty( $accounts ) ) {

			$icon_url       = esc_url( IGD_ASSETS . '/images/drive.png' );
			$refresh_url    = esc_url( admin_url( 'admin.php?page=integrate-google-drive-settings&tab=accounts' ) );
			$plugin_name    = esc_html__( 'Integrate Google Drive', 'integrate-google-drive' );
			$refresh_text   = esc_html__( 'Refresh', 'integrate-google-drive' );
			$alt_text       = esc_attr__( 'Google Drive icon', 'integrate-google-drive' );
			$lost_auth_text = esc_html__( 'lost authorization for account', 'integrate-google-drive' );

			foreach ( $accounts as $account ) {
				if ( empty( $account['lost'] ) && empty( $account['is_lost'] ) ) {
					continue; // skip accounts without lost auth
				}

				$email = esc_html( $account['email'] );

				$msg = sprintf(
					'<img src="%1$s" width="32" alt="%2$s" /> <strong>%3$s</strong> %4$s <strong>%5$s</strong>. <a class="button" href="%6$s">%7$s</a>',
					$icon_url,
					$alt_text,
					$plugin_name,
					$lost_auth_text,
					$email,
					$refresh_url,
					$refresh_text
				);

				igd()->add_notice( 'error igd-lost-auth-notice', $msg );
			}
		}

		// Account reconnection notice
		if ( get_option( 'igd_account_notice' ) ) {
			ob_start();
			include IGD_INCLUDES . '/views/notice/account.php';
			$notice_html = ob_get_clean();
			igd()->add_notice( 'info igd-account-notice error', $notice_html );
		}

	}

	public function admin_body_class( $classes ) {
		$admin_pages = Admin::instance()->get_pages();

		global $current_screen;

		if ( is_object( $current_screen ) && in_array( $current_screen->id, $admin_pages ) ) {
			$key = array_search( $current_screen->id, $admin_pages );

			$classes .= ' igd-admin-page igd_' . $key . ' ';
		}

		return $classes;
	}

	public function show_review_popup() {

		if ( ! current_user_can( 'manage_options' ) ) {
			return;
		}

		// Rating notice
		if ( 'off' != get_option( 'igd_rating_notice' ) && 'off' != get_transient( 'igd_rating_notice_interval' ) ) {
			add_filter( 'igd_localize_data', function ( $data ) {
				$data['showReviewPopup'] = true;

				return $data;
			} );

		}
	}

	public function app_authorization() {
		if ( isset( $_GET['action'] ) && 'integrate-google-drive-authorization' == sanitize_key( $_GET['action'] ) ) {

			// Remove 'action' from params
			unset( $_GET['action'] );

			// Decode and sanitize the 'state' parameter
			$state_url = base64_decode( sanitize_text_field( $_GET['state'] ) );

			// Validate the URL
			if ( false === filter_var( $state_url, FILTER_VALIDATE_URL ) ) {
				// Handle invalid URL
				wp_safe_redirect( home_url() );
				exit;
			}

			// Check if the URL belongs to the current website domain
			$current_domain  = wp_parse_url( home_url(), PHP_URL_HOST );
			$redirect_domain = wp_parse_url( $state_url, PHP_URL_HOST );

			if ( $current_domain !== $redirect_domain ) {
				// Redirect or error handling if the domain is not the current domain
				wp_safe_redirect( home_url() );
				exit();
			}

			// Build the redirect URL
			$params       = http_build_query( $_GET );
			$redirect_url = esc_url_raw( $state_url . '&' . $params );

			// Execute the redirect
			wp_redirect( $redirect_url );
			exit();
		}
	}

	public function init_update() {

		if ( current_user_can( 'manage_options' ) ) {

			if ( ! class_exists( 'IGD\Update' ) ) {
				require_once IGD_INCLUDES . '/class-update.php';
			}

			$updater = Update::instance();

			if ( $updater->needs_update() ) {
				$updater->perform_updates();
			}
		}
	}

	public function admin_menu() {

		$main_menu_added = false;

		$access_rights = [
			'file_browser'      => [
				'view'          => [ 'IGD\App', 'view' ],
				'title'         => __( 'File Browser - Integrate Google Drive', 'integrate-google-drive' ),
				'submenu_title' => __( 'File Browser', 'integrate-google-drive' )
			],
			'shortcode_builder' => [
				'view'          => [ 'IGD\Shortcode', 'view' ],
				'title'         => __( 'Module Builder - Integrate Google Drive', 'integrate-google-drive' ),
				'submenu_title' => __( 'Module Builder', 'integrate-google-drive' )
			],
			'proof_selections'  => [
				'view'          => [ 'IGD\Proof_Selections', 'view' ],
				'title'         => __( 'Proof Selections - Integrate Google Drive', 'integrate-google-drive' ),
				'submenu_title' => '',
			],
			'private_files'     => [
				'view'          => [ 'IGD\Private_Folders', 'view' ],
				'title'         => __( 'Users Private Files - Integrate Google Drive', 'integrate-google-drive' ),
				'submenu_title' => __( 'Users Private Files', 'integrate-google-drive' )
			],
			'getting_started'   => [
				'view'          => [ $this, 'render_getting_started_page' ],
				'title'         => __( 'Getting Started - Integrate Google Drive', 'integrate-google-drive' ),
				'submenu_title' => __( 'Getting Started', 'integrate-google-drive' )
			],
			'statistics'        => [
				'view'          => [ 'IGD\Statistics', 'view' ],
				'title'         => __( 'Statistics - Integrate Google Drive', 'integrate-google-drive' ),
				'submenu_title' => __( 'Statistics', 'integrate-google-drive' )
			],
			'settings'          => [
				'view'          => [ $this, 'render_settings_page' ],
				'title'         => __( 'Settings - Integrate Google Drive', 'integrate-google-drive' ),
				'submenu_title' => __( 'Settings', 'integrate-google-drive' )
			]
		];

		// Check statistics access
		if ( ! igd_fs()->can_use_premium_code__premium_only() || ! igd_get_settings( 'enableStatistics', false ) ) {
			unset( $access_rights['statistics'] );
		}

		foreach ( $access_rights as $access_right => $page_config ) {

			$can_access = igd_user_can_access( $access_right );

			if ( 'proof_selections' === $access_right ) {
				$can_access = igd_user_can_access( 'shortcode_builder' );
			}

			if ( $can_access ) {
				if ( ! $main_menu_added ) {
					$this->pages[ $access_right . '_page' ] = $this->add_main_menu_page( $page_config['title'], $page_config['submenu_title'], $page_config['view'] );
					$main_menu_added                        = true;
				} else {
					$this->pages[ $access_right . '_page' ] = $this->add_submenu_page( $page_config['title'], $page_config['submenu_title'], $page_config['view'], $access_right );
				}
			}

		}

	}

	private function add_main_menu_page( $title, $submenu_title, $view ) {

		$page = add_menu_page(
			__( 'Integrate Google Drive', 'integrate-google-drive' ),
			__( 'Google Drive', 'integrate-google-drive' ),
			'read',
			'integrate-google-drive',
			$view,
			IGD_ASSETS . '/images/drive.png',
			11
		);

		add_submenu_page( 'integrate-google-drive', $title, $submenu_title, 'read', 'integrate-google-drive' );

		return $page;
	}

	private function add_submenu_page( $title, $submenu_title, $view, $slug, $priority = 90 ) {

		$slug = str_replace( '_', '-', $slug );

		return add_submenu_page( 'integrate-google-drive', $title, $submenu_title, 'read', 'integrate-google-drive-' . $slug, $view, $priority );
	}

	public function render_getting_started_page() {
		include_once IGD_INCLUDES . '/views/getting-started/index.php';
	}

	public function render_settings_page() { ?>
        <div id="igd-settings"></div>
	<?php }

	public function get_pages() {
		return array_filter( $this->pages );
	}

	/**
	 * @return Admin|null
	 */
	public static function instance() {
		if ( is_null( self::$instance ) ) {
			self::$instance = new self();
		}

		return self::$instance;
	}

}

Admin::instance();