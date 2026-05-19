<?php

namespace IGD;

defined( 'ABSPATH' ) || exit;

class DiviExtension extends \DiviExtension {

	/**
	 * The gettext domain for the extension's translations.
	 *
	 * @since 1.0.0
	 *
	 * @var string
	 */
	public $gettext_domain = 'integrate-google-drive';

	/**
	 * The extension's WP Plugin name.
	 *
	 * @since 1.0.0
	 *
	 * @var string
	 */
	public $name = 'igd-divi-extension';

	/**
	 * The extension's version
	 *
	 * @since 1.0.0
	 *
	 * @var string
	 */
	public $version = '1.0.0';

	/**
	 * \DiviExtension() constructor.
	 *
	 * @param string $name
	 * @param array $args
	 */
	public function __construct( $name = 'igd-divi-extension', $args = array() ) {

		$this->plugin_dir     = plugin_dir_path( __FILE__ );
		$this->plugin_dir_url = plugin_dir_url( $this->plugin_dir );

		parent::__construct( $name, $args );

		// Enqueue scripts when Divi builder is ready in frontend
		add_action( 'et_builder_ready', array( $this, 'enqueue_builder_scripts' ) );

		// Load scripts in admin editor
		add_action( 'admin_enqueue_scripts', function ( $hook ) {

			if ( ! in_array( $hook, [ 'post.php', 'post-new.php' ] ) ) {
				return;
			}

			Enqueue::instance()->admin_scripts( '', false );
		} );

		// Fix RankMath Conflict
		if ( class_exists( 'RankMath' ) ) {
			add_filter( 'script_loader_tag', array( $this, 'modify_script_loader_tag' ), 11, 3 );
		}
	}

	public function enqueue_builder_scripts() {

		if ( function_exists( 'et_theme_builder_enqueue_scripts' ) ) {
			et_theme_builder_enqueue_scripts(); //fix the scripts issue
		}

		// Fix RankMath Conflict
		if ( class_exists( 'RankMath' ) ) {
			$rm_divi = new \RankMath\Divi\Divi();
			$rm_divi->register_rankmath_react();

			wp_enqueue_script( 'rm-react' );
			wp_enqueue_script( 'rm-react-dom' );
		}

		Enqueue::instance()->admin_scripts( '', false );
	}

	public function modify_script_loader_tag( $tag, $handle, $src ) {

		// Modify script tag if certain class is found
		if ( strpos( $tag, 'et_fb_ignore_iframe' ) !== false ) {
			$tag = str_replace( 'et_fb_ignore_iframe', '', $tag );
		}

		return $tag;
	}


}

new DiviExtension();