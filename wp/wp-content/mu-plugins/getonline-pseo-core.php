<?php
/**
 * Plugin Name: GetOnline Studio pSEO Core
 * Description: Core application logic for the Programmatic SEO engine. Registers Niches, Locations, and Surround Sound Listicles.
 * Version: 1.3
 * Author: GetOnline Studio
 */

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

// 1. Register 'Niche' Custom Post Type
function getonline_register_niche_cpt() {
    $labels = array(
        'name'                  => 'SEO Niches',
        'singular_name'         => 'Niche',
        'menu_name'             => 'SEO Niches',
        'add_new'               => 'Add New Niche',
        'add_new_item'          => 'Add New Niche',
        'edit_item'             => 'Edit Niche',
        'all_items'             => 'All Niches',
    );

    $args = array(
        'labels'                => $labels,
        'public'                => false, // Keep false so WP doesn't create its own frontend URLs
        'show_ui'               => true,
        'show_in_menu'          => true,
        'menu_position'         => 20,
        'menu_icon'             => 'dashicons-portfolio',
        'supports'              => array('title'), // We will use custom meta fields for content
        'has_archive'           => false,
    );

    register_post_type('pseo_niche', $args);
}
add_action('init', 'getonline_register_niche_cpt');

// 2. Register 'Location' Custom Post Type
function getonline_register_location_cpt() {
    $labels = array(
        'name'                  => 'SEO Locations',
        'singular_name'         => 'Location',
        'menu_name'             => 'SEO Locations',
        'add_new'               => 'Add New City',
        'add_new_item'          => 'Add New City',
        'edit_item'             => 'Edit City',
        'all_items'             => 'All Cities',
    );

    $args = array(
        'labels'                => $labels,
        'public'                => false, // Handled by our custom routing
        'show_ui'               => true,
        'show_in_menu'          => true,
        'menu_position'         => 21,
        'menu_icon'             => 'dashicons-location',
        'supports'              => array('title'),
        'has_archive'           => false,
    );

    register_post_type('pseo_location', $args);
}
add_action('init', 'getonline_register_location_cpt');

// 3. Auto-Populate the 17 Cities from Priority List
function getonline_pseo_seed_locations() {
    $locations = [
        'osogbo' => 'Osogbo', 
        'osun-state' => 'Osun State', 
        'lagos' => 'Lagos',
        'abuja' => 'Abuja', 
        'ibadan' => 'Ibadan', 
        'port-harcourt' => 'Port Harcourt',
        'kano' => 'Kano', 
        'kaduna' => 'Kaduna', 
        'benin-city' => 'Benin City',
        'jos' => 'Jos', 
        'ilorin' => 'Ilorin', 
        'enugu' => 'Enugu',
        'uyo' => 'Uyo', 
        'asaba' => 'Asaba', 
        'owerri' => 'Owerri',
        'warri' => 'Warri', 
        'calabar' => 'Calabar'
    ];

    // Check if locations are already seeded so we don't duplicate them
    if (!get_option('pseo_locations_seeded')) {
        foreach ($locations as $slug => $name) {
            
            // Double check if the city already exists in the database
            $existing = get_page_by_path($slug, OBJECT, 'pseo_location');
            
            if (!$existing) {
                wp_insert_post([
                    'post_title'  => $name,
                    'post_name'   => $slug,
                    'post_type'   => 'pseo_location',
                    'post_status' => 'draft', // Sets them to Draft so you can turn them on manually
                ]);
            }
        }
        
        // Save an option in the DB so this loop never runs again (saves server speed)
        update_option('pseo_locations_seeded', true);
    }
}
// Using 'init' instead of 'admin_init' so it triggers immediately on our custom dashboard!
add_action('init', 'getonline_pseo_seed_locations');

// 4. Create Custom Database Table for Listicles (High Performance)
function getonline_pseo_create_custom_tables() {
    global $wpdb;
    $table_name = $wpdb->prefix . 'pseo_listicles';
    $charset_collate = $wpdb->get_charset_collate();

    // Schema for our custom table
    $sql = "CREATE TABLE $table_name (
        id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
        city_slug varchar(100) NOT NULL,
        niche_slug varchar(100) NOT NULL,
        target_keyword varchar(255) NOT NULL,
        meta_title varchar(255) NOT NULL,
        content longtext NOT NULL,
        status varchar(20) DEFAULT 'draft' NOT NULL,
        created_at datetime DEFAULT CURRENT_TIMESTAMP NOT NULL,
        updated_at datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP NOT NULL,
        PRIMARY KEY  (id),
        UNIQUE KEY city_niche (city_slug, niche_slug)
    ) $charset_collate;";

    require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
    dbDelta($sql);

    update_option('pseo_listicles_db_version', '1.0');
}

// Auto-run the DB check so the table creates itself without needing to deactivate/reactivate the plugin
function getonline_pseo_check_db() {
    if (get_option('pseo_listicles_db_version') != '1.0') {
        getonline_pseo_create_custom_tables();
    }
}
add_action('plugins_loaded', 'getonline_pseo_check_db');