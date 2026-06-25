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

$active_niches = get_posts([
    'post_type'   => 'pseo_niche',
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

$base_url = 'https://getonlinestudio.com';

$locations_lastmod = !empty($active_cities)
    ? date('Y-m-d', max(array_map(fn($c) => strtotime($c->post_modified), $active_cities)))
    : date('Y-m-d');

$niches_lastmod = !empty($active_niches)
    ? date('Y-m-d', max(array_map(fn($n) => strtotime($n->post_modified), $active_niches)))
    : date('Y-m-d');

echo '<?xml version="1.0" encoding="UTF-8"?>' . "\n";
echo '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">' . "\n";

// --- 1. Homepage ---
echo "  <url>\n";
echo "    <loc>{$base_url}/</loc>\n";
echo "    <lastmod>{$locations_lastmod}</lastmod>\n";
echo "    <changefreq>weekly</changefreq>\n";
echo "    <priority>1.0</priority>\n";
echo "  </url>\n";

// --- 2. Core Static Pages ---
$static_pages = [
    '/about/'          => ['priority' => '0.6', 'changefreq' => 'yearly'],
    '/contact/'        => ['priority' => '0.7', 'changefreq' => 'yearly'],
    '/privacy-policy/' => ['priority' => '0.3', 'changefreq' => 'yearly'],
];
foreach ($static_pages as $path => $meta) {
    echo "  <url>\n";
    echo "    <loc>{$base_url}{$path}</loc>\n";
    echo "    <changefreq>{$meta['changefreq']}</changefreq>\n";
    echo "    <priority>{$meta['priority']}</priority>\n";
    echo "  </url>\n";
}

// --- 3. Services Index ---
echo "  <url>\n";
echo "    <loc>{$base_url}/services/</loc>\n";
echo "    <lastmod>{$niches_lastmod}</lastmod>\n";
echo "    <changefreq>weekly</changefreq>\n";
echo "    <priority>0.9</priority>\n";
echo "  </url>\n";

// --- 4. Service Niche Hubs (/services/{niche}/) ---
foreach ($active_niches as $niche) {
    echo "  <url>\n";
    echo "    <loc>" . esc_url("{$base_url}/services/{$niche->post_name}/") . "</loc>\n";
    echo "    <lastmod>" . date('Y-m-d', strtotime($niche->post_modified)) . "</lastmod>\n";
    echo "    <changefreq>monthly</changefreq>\n";
    echo "    <priority>0.8</priority>\n";
    echo "  </url>\n";
}

// --- 5. Main Locations Index ---
echo "  <url>\n";
echo "    <loc>{$base_url}/locations/</loc>\n";
echo "    <lastmod>{$locations_lastmod}</lastmod>\n";
echo "    <changefreq>daily</changefreq>\n";
echo "    <priority>0.9</priority>\n";
echo "  </url>\n";

// --- 6. City Hubs (/locations/lagos/) ---
foreach ($active_cities as $city) {
    echo "  <url>\n";
    echo "    <loc>" . esc_url("{$base_url}/locations/{$city->post_name}/") . "</loc>\n";
    echo "    <lastmod>" . date('Y-m-d', strtotime($city->post_modified)) . "</lastmod>\n";
    echo "    <changefreq>weekly</changefreq>\n";
    echo "    <priority>0.9</priority>\n";
    echo "  </url>\n";
}

// --- 7. Surround Sound Listicles ---
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