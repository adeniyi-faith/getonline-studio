<?php
/**
 * DYNAMIC CITY SITEMAP
 * Handles: Niches, Service Formats, and ONLY APPROVED Neighborhoods.
 */
define('WP_USE_THEMES', false);
require_once(__DIR__ . '/wp/wp-load.php');

$city_slug = isset($_GET['city']) ? sanitize_title($_GET['city']) : '';
$city_post = get_page_by_path($city_slug, OBJECT, 'pseo_location');

if (!$city_post || $city_post->post_status !== 'publish') {
    status_header(404);
    exit('City not active.');
}

// Data Fetching
$active_niches = get_posts(['post_type' => 'pseo_niche', 'post_status' => 'publish', 'numberposts' => -1]);
$active_niche_slugs = array_map(fn($n) => $n->post_name, $active_niches);
$service_formats = ['website-designer', 'website-developer', 'web-design-agency', 'website-design-services', 'branding-agency'];

// Load Neighborhoods Library
$nb_library = [];
$nb_file = __DIR__ . '/neighborhoods.json';
if (file_exists($nb_file)) {
    $nb_data = json_decode(file_get_contents($nb_file), true);
    $nb_library = $nb_data[$city_slug] ?? [];
}

// CRITICAL FIX: FETCH ONLY APPROVED NEIGHBORHOODS FROM DB
$approved_nb = get_post_meta($city_post->ID, '_pseo_active_neighborhoods', true);
if (!is_array($approved_nb)) {
    $approved_nb = [];
}

while (ob_get_level() > 0) ob_end_clean();
header("Content-Type: text/xml;charset=UTF-8");
$base_url = (isset($_SERVER['HTTPS']) ? "https" : "http") . "://" . $_SERVER['HTTP_HOST'];

echo '<?xml version="1.0" encoding="UTF-8"?>' . "\n";
echo '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">' . "\n";

// 1. City Base Niche Landing Pages
$city_lastmod = date('Y-m-d', strtotime($city_post->post_modified));
foreach ($active_niche_slugs as $n_slug) {
    foreach ($service_formats as $f_slug) {
        echo "  <url>\n";
        echo "    <loc>" . esc_url("{$base_url}/locations/{$city_slug}/{$n_slug}-{$f_slug}/") . "</loc>\n";
        echo "    <lastmod>{$city_lastmod}</lastmod>\n";
        echo "    <changefreq>monthly</changefreq>\n";
        echo "    <priority>0.7</priority>\n";
        echo "  </url>\n";
    }
}

// 2. HYPER-LOCAL: ONLY include Approved Neighborhoods
foreach ($approved_nb as $nb_slug) {
    if (!isset($nb_library[$nb_slug])) continue; // Safety check
    
    foreach ($active_niche_slugs as $n_slug) {
        foreach ($service_formats as $f_slug) {
            echo "  <url>\n";
            echo "    <loc>" . esc_url("{$base_url}/locations/{$city_slug}/{$nb_slug}/{$n_slug}-{$f_slug}/") . "</loc>\n";
            echo "    <lastmod>{$city_lastmod}</lastmod>\n";
            echo "    <changefreq>monthly</changefreq>\n";
            echo "    <priority>0.5</priority>\n";
            echo "  </url>\n";
        }
    }
}

echo '</urlset>';