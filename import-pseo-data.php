<?php
/**
 * GETONLINE STUDIO - pSEO DATA IMPORTER
 * * Usage: 
 * Upload this to your root directory alongside your .json files.
 * Run in browser: https://yourdomain.com/import-pseo-data.php?key=getonline2026
 * * This script safely "Upserts" data. You can run it repeatedly whenever you update your JSON files.
 */

// Basic security lock to prevent unauthorized triggering
$secret_key = 'getonline2026'; // Change this to whatever you like
if (!isset($_GET['key']) || $_GET['key'] !== $secret_key) {
    die('Unauthorized access.');
}

// 1. Boot up WordPress silently
define('WP_USE_THEMES', false);
require_once(__DIR__ . '/wp/wp-load.php'); // Adjust path if your wp-load.php is elsewhere

global $wpdb;

echo "<h1>pSEO Database Import Started...</h1>";
echo "<pre>";

// =========================================================================
// STEP 1: SETUP CUSTOM DATABASE TABLE FOR CITY x NICHE MATRIX
// =========================================================================
$matrix_table = $wpdb->prefix . 'pseo_city_niche_data';
$charset_collate = $wpdb->get_charset_collate();

$sql = "CREATE TABLE IF NOT EXISTS $matrix_table (
    id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
    city_slug varchar(100) NOT NULL,
    niche_slug varchar(100) NOT NULL,
    matrix_data longtext NOT NULL, -- Storing the specific JSON fields for this combo
    updated_at datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP NOT NULL,
    PRIMARY KEY  (id),
    UNIQUE KEY city_niche (city_slug, niche_slug)
) $charset_collate;";

require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
dbDelta($sql);
echo "✔ City x Niche Matrix table verified/created.\n";


// =========================================================================
// STEP 2: IMPORT NICHES ( niches.json -> pseo_niche CPT )
// =========================================================================
$niches_file = __DIR__ . '/niches.json';
if (file_exists($niches_file)) {
    $niches_data = json_decode(file_get_contents($niches_file), true);
    
    if (is_array($niches_data)) {
        $niche_count = 0;
        foreach ($niches_data as $slug => $data) {
            // Check if niche post already exists
            $existing_post = get_page_by_path($slug, OBJECT, 'pseo_niche');
            
            $post_data = [
                'post_title'   => sanitize_text_field($data['name']),
                'post_name'    => $slug,
                'post_status'  => 'publish',
                'post_type'    => 'pseo_niche',
            ];

            if ($existing_post) {
                // Update existing
                $post_data['ID'] = $existing_post->ID;
                $post_id = wp_update_post($post_data);
                $status = "Updated";
            } else {
                // Insert new
                $post_id = wp_insert_post($post_data);
                $status = "Created";
            }

            if (!is_wp_error($post_id)) {
                // Save all the extra JSON attributes as post meta
                update_post_meta($post_id, '_pseo_niche_data', $data);
                $niche_count++;
            }
        }
        echo "✔ Processed {$niche_count} Niches.\n";
    } else {
        echo "⚠ Error parsing niches.json.\n";
    }
} else {
    echo "⚠ niches.json not found.\n";
}


// =========================================================================
// STEP 3: IMPORT CITY x NICHE MATRIX ( city-niche.json -> Custom Table )
// =========================================================================
$matrix_file = __DIR__ . '/city-niche.json';
if (file_exists($matrix_file)) {
    $matrix_data = json_decode(file_get_contents($matrix_file), true);
    
    if (is_array($matrix_data)) {
        $matrix_count = 0;
        foreach ($matrix_data as $key => $data) {
            if ($key === '_readme') continue; // Skip instructions
            
            $city_slug = sanitize_title($data['city']);
            $niche_slug = sanitize_title($data['niche']);
            $json_payload = wp_json_encode($data);

            // Upsert into custom table
            $wpdb->query(
                $wpdb->prepare(
                    "INSERT INTO $matrix_table (city_slug, niche_slug, matrix_data) 
                     VALUES (%s, %s, %s) 
                     ON DUPLICATE KEY UPDATE matrix_data = VALUES(matrix_data)",
                    $city_slug, $niche_slug, $json_payload
                )
            );
            $matrix_count++;
        }
        echo "✔ Processed {$matrix_count} City x Niche Intersections.\n";
    } else {
        echo "⚠ Error parsing city-niche.json.\n";
    }
} else {
    echo "⚠ city-niche.json not found.\n";
}


// =========================================================================
// STEP 4: IMPORT COMPETITORS ( competitors.json -> pseo_location Meta )
// =========================================================================
$competitors_file = __DIR__ . '/competitors.json';
// Or check competitors (2).json if that's the exact name uploaded
if (!file_exists($competitors_file) && file_exists(__DIR__ . '/competitors (2).json')) {
    $competitors_file = __DIR__ . '/competitors .json';
}

if (file_exists($competitors_file)) {
    $comp_data = json_decode(file_get_contents($competitors_file), true);
    
    if (is_array($comp_data)) {
        $comp_count = 0;
        foreach ($comp_data as $city_slug => $competitors_array) {
            // Find the location post
            $city_post = get_page_by_path($city_slug, OBJECT, 'pseo_location');
            
            if ($city_post) {
                // Save competitors list as meta attached to the city
                update_post_meta($city_post->ID, '_pseo_city_competitors', $competitors_array);
                $comp_count++;
            }
        }
        echo "✔ Attached competitors to {$comp_count} Cities.\n";
    } else {
        echo "⚠ Error parsing competitors.json.\n";
    }
} else {
    echo "⚠ competitors.json not found.\n";
}

echo "\n<b>Import Complete!</b> You can now safely close this window.";
echo "</pre>";
?>