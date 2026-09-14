<?php
/**
 * Plugin Name: GetOnline Studio Site CMS
 * Description: Registers the "no-code" content types behind the admin
 * dashboard at /wp/admin/ — Portfolio Projects and Site Pages — plus the
 * go_site_settings option (navigation menu, contact details, socials).
 * Rendering for public visitors does NOT depend on this plugin: saving
 * a project/page/settings from the admin also writes a plain JSON copy
 * under /data/, and the public pages read that JSON instead of booting
 * WordPress, so the site stays fast and resilient even if WordPress or
 * its database has a bad day.
 * Version: 1.0
 * Author: GetOnline Studio
 */

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

// 1. Register 'Portfolio Project' Custom Post Type
function getonline_register_portfolio_cpt() {
    $labels = array(
        'name'          => 'Portfolio Projects',
        'singular_name' => 'Portfolio Project',
        'menu_name'     => 'Portfolio',
        'add_new'       => 'Add New Project',
        'add_new_item'  => 'Add New Project',
        'edit_item'     => 'Edit Project',
        'all_items'     => 'All Projects',
    );

    $args = array(
        'labels'        => $labels,
        'public'        => false, // Rendered by our own portfolio-view.php, not WP's front end
        'show_ui'       => true,
        'show_in_menu'  => true,
        'menu_position' => 22,
        'menu_icon'     => 'dashicons-portfolio',
        'supports'      => array('title'), // Everything else is custom meta, edited at /wp/admin/portfolio.php
        'has_archive'   => false,
    );

    register_post_type('portfolio_project', $args);
}
add_action('init', 'getonline_register_portfolio_cpt');

// 2. Register 'Site Page' Custom Post Type
function getonline_register_site_page_cpt() {
    $labels = array(
        'name'          => 'Site Pages',
        'singular_name' => 'Site Page',
        'menu_name'     => 'Site Pages',
        'add_new'       => 'Add New Page',
        'add_new_item'  => 'Add New Page',
        'edit_item'     => 'Edit Page',
        'all_items'     => 'All Pages',
    );

    $args = array(
        'labels'        => $labels,
        'public'        => false, // Rendered by our own page-view.php, not WP's front end
        'show_ui'       => true,
        'show_in_menu'  => true,
        'menu_position' => 23,
        'menu_icon'     => 'dashicons-media-document',
        'supports'      => array('title'),
        'has_archive'   => false,
    );

    register_post_type('site_page', $args);
}
add_action('init', 'getonline_register_site_page_cpt');

// 3. Seed a default go_site_settings option (nav menu + contact + socials)
// so the admin Settings screen and the public header/footer always have
// something sane to fall back to, even before anyone saves anything.
function getonline_seed_site_settings() {
    if (get_option('go_site_settings') !== false) {
        return;
    }

    update_option('go_site_settings', array(
        'nav_items' => array(
            array('label' => 'Work',      'url' => '/work',      'order' => 10),
            array('label' => 'Services',  'url' => '/services',  'order' => 20),
            array('label' => 'About',     'url' => '/about',     'order' => 30),
            array('label' => 'Locations', 'url' => '/locations/', 'order' => 40),
            array('label' => 'Contact',   'url' => '/contact',   'order' => 50),
        ),
        'contact_email'     => 'hello@getonlinestudio.com',
        'whatsapp_primary'  => '2348108275013',
        'whatsapp_secondary' => '2348080732660',
        'social' => array(
            'instagram' => '',
            'twitter'   => '',
            'linkedin'  => '',
        ),
    ));
}
add_action('init', 'getonline_seed_site_settings');
