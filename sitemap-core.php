<?php
/**
 * CORE SITEMAP GENERATOR — v2.0
 * Contains only High-Priority Hubs and Listicles.
 */
define('WP_USE_THEMES', false);
require_once(__DIR__ . '/wp/wp-load.php');

// 1. Fetch active data
$active_cities = get_posts([
    'post_type'   => 'pseo_location',
    'post_status' => 'publish',
    'numberposts' => -1
]);

// 2. Fetch published listicles from custom table
global $wpdb;
$listicle_table = $wpdb->prefix . 'pseo_listicles';
$suppress = $wpdb->suppress_errors(true);
$published_listicles = $wpdb->get_results("SELECT city_slug, niche_slug, updated_at FROM $listicle_table WHERE status = 'publish'");
$wpdb->suppress_errors($suppress);

while (ob_get_level() > 0) ob_end_clean();
header("Content-Type: text/xml;charset=UTF-8");

$base_url = (isset($_SERVER['HTTPS']) ? "https" : "http") . "://" . $_SERVER['HTTP_HOST'];

echo '<?xml version="1.0" encoding="UTF-8"?>' . "\n";
echo '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">' . "\n";

// --- 1. Main Index Pages ---
echo "  <url>\n";
echo "    <loc>{$base_url}/locations/</loc>\n";
echo "    <lastmod>" . date('Y-m-d') . "</lastmod>\n";
echo "    <changefreq>daily</changefreq>\n";
echo "    <priority>1.0</priority>\n";
echo "  </url>\n";

// --- 2. City Hubs (/locations/lagos/) ---
foreach ($active_cities as $city) {
    echo "  <url>\n";
    echo "    <loc>" . esc_url("{$base_url}/locations/{$city->post_name}/") . "</loc>\n";
    echo "    <lastmod>" . date('Y-m-d', strtotime($city->post_modified)) . "</lastmod>\n";
    echo "    <changefreq>weekly</changefreq>\n";
    echo "    <priority>0.9</priority>\n";
    echo "  </url>\n";
}

// --- 3. Surround Sound Listicles ---
if (!empty($published_listicles)) {
    foreach ($published_listicles as $listicle) {
        echo "  <url>\n";
        echo "    <loc>" . esc_url("{$base_url}/locations/{$listicle->city_slug}/top-{$listicle->niche_slug}-web-designers/") . "</loc>\n";
        echo "    <lastmod>" . date('Y-m-d', strtotime($listicle->updated_at)) . "</lastmod>\n";
        echo "    <changefreq>monthly</changefreq>\n";
        echo "    <priority>0.8</priority>\n";
        echo "  </url>\n";
    }
}

echo '</urlset>';