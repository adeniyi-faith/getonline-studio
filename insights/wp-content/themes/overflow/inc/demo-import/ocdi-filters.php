<?php
/**
 * One Click Demo Import Functions
 *
 * @package Overflow
 */

/**
 * Settings Page
 *
 * @param array $default_settings Default settings.
 */
function csco_ocdi_plugin_page_setup( $default_settings ) {
	$data = array(
		'parent_slug' => 'themes.php',
		'page_title'  => esc_html__( 'Demo Content', 'paper-plane' ),
		'menu_title'  => esc_html__( 'Demo Content', 'paper-plane' ),
		'capability'  => 'import',
		'menu_slug'   => 'csco-demo-content',
	);

	return $data;
}
add_filter( 'pt-ocdi/plugin_page_setup', 'csco_ocdi_plugin_page_setup' );

/**
 * Demo Content
 */
function csco_ocdi_import_files() {
	return array(
		array(
			'import_file_name'       => esc_html__( 'Demo Content', 'overflow' ),
			'import_file_url'        => 'https://cloud.codesupply.co/demo-content/demo-content.xml',
			'import_widget_file_url' => 'https://cloud.codesupply.co/demo-content/widgets.json',
		),
	);
}
add_filter( 'pt-ocdi/import_files', 'csco_ocdi_import_files' );

/**
 * Page Title
 */
function csco_ocdi_page_title() {
	return '<div class="ocdi__title-container"><h1 class="ocdi__title-container-title">' . esc_html__( 'Demo Content Import', 'overflow' ) . '</h1></div>';
}
add_filter( 'pt-ocdi/plugin_page_title', 'csco_ocdi_page_title' );

/**
 * Intro Text
 *
 * @param string $default_text Default Intro Text.
 */
function csco_ocdi_plugin_intro_text( $default_text ) {
	ob_start(); ?>
	<div class="ocdi__intro-text">
		<p class="about-description"><?php esc_html_e( 'Clicking the Import Demo Data button will import demo posts, pages, categories, comments, tags and widgets.', 'overflow' ); ?></p>
	</div>
	<?php
	$default_text = ob_get_clean();

	return $default_text;
}
add_filter( 'pt-ocdi/plugin_intro_text', 'csco_ocdi_plugin_intro_text' );

/**
 * Disable Branding
 */
add_filter( 'pt-ocdi/disable_pt_branding', '__return_true' );

/**
 * Import Setup
 */
function csco_ocdi_after_import_setup() {

	$first_admin = false;

	$main_menu = get_term_by( 'name', 'Primary', 'nav_menu' );

	set_theme_mod(
		'nav_menu_locations', array(
			'primary' => $main_menu->term_id,
			'mobile'  => $main_menu->term_id,
		)
	);

	// Get administrators.
	$users = get_users( array(
		'role'   => 'administrator',
		'fields' => array( 'ID' ),
	) );

	if ( $users ) {
		$users = wp_list_pluck( $users, 'ID' );

		$first_admin = array_shift( $users );
	}

	// Set widgets settings.
	$author   = (array) get_option( 'widget_powerkit_widget_author' );
	$sidebars = (array) get_option( 'sidebars_widgets' );

	foreach ( $sidebars as $sidebar => $widgets ) {
		foreach ( $widgets as $key => $widget ) {
			preg_match( '/(.*)-(\d*)$/', $widget, $widget_data );

			// Get widget data.
			$widget_name  = $widget_data[1];
			$widget_index = $widget_data[2];

			// Set author.
			if ( 'powerkit_widget_author' === $widget_name && $first_admin ) {
				$author[ $widget_index ]['author'] = $first_admin;
				break;
			}
		}
	}
	update_option( 'widget_powerkit_widget_author', $author );

	// Set posts on front page.
	update_option( 'show_on_front', 'posts' );

}
add_action( 'pt-ocdi/after_import', 'csco_ocdi_after_import_setup' );
