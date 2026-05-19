<?php
/**
 * MASTER SITEMAP INDEX
 * Points Google to smaller city-specific chunks.
 */
define('WP_USE_THEMES', false);
require_once(__DIR__ . '/wp/wp-load.php');

$active_cities = get_posts([
    'post_type'   => 'pseo_location',
    'post_status' => 'publish',
    'numberposts' => -1,
    'orderby'     => 'post_name',
    'order'       => 'ASC'
]);

while (ob_get_level() > 0) ob_end_clean();
header("Content-Type: text/xml;charset=UTF-8");

$base_url = (isset($_SERVER['HTTPS']) ? "https" : "http") . "://" . $_SERVER['HTTP_HOST'];

echo '<?xml version="1.0" encoding="UTF-8"?>' . "\n";
echo '<sitemapindex xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">' . "\n";

// 1. Link to the Core Hubs & Listicles
echo "  <sitemap>\n";
echo "    <loc>{$base_url}/sitemap-core.xml</loc>\n";
echo "  </sitemap>\n";

// 2. Link to every City-specific Sitemap
foreach ($active_cities as $city) {
    echo "  <sitemap>\n";
    echo "    <loc>" . esc_url("{$base_url}/sitemap-city-{$city->post_name}.xml") . "</loc>\n";
    echo "    <lastmod>" . date('Y-m-d', strtotime($city->post_modified)) . "</lastmod>\n";
    echo "  </sitemap>\n";
}

echo '</sitemapindex>';