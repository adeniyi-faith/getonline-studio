<?php
/**
 * GETONLINE STUDIO - pSEO COMMAND CENTER (WordPress Headless Version)
 * Automatically connects to your database via WP. No passwords needed.
 * Upgraded with: The AI Theme Rotation Engine (State Machine) & Visual Completion Dashboard
 */

// 1. Boot up WordPress silently
define('WP_USE_THEMES', false);
require_once(__DIR__ . '/wp-load.php');

// 2. Security Check: Ensure the user is a logged-in WP Admin
// Redirects to your custom mobile-optimized login page
if (!is_user_logged_in() || !current_user_can('manage_options')) {
    wp_redirect('https://getonlinestudio.com/wp/u-login.php?redirect_to=' . urlencode($_SERVER['REQUEST_URI']));
    exit;
}

$message = '';
$active_tab = 'dashboard';

// Define standard service formats for the new matrix architecture
$service_formats = [
    'website-designer'        => 'Website Designer',
    'website-developer'       => 'Website Developer',
    'web-design-agency'       => 'Web Design Agency',
    'website-design-services' => 'Design Services',
    'branding-agency'         => 'Branding Agency'
];

// ---------------------------------------------------------
// FORM HANDLING (Saves data securely using WordPress functions)
// ---------------------------------------------------------
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    
    // A. Save Niche Content (UPGRADED FOR JSON THEMES)
    if (isset($_POST['action']) && $_POST['action'] === 'save_niche') {
        $niche_slug  = sanitize_title($_POST['niche_slug']);
        $format_slug = sanitize_title($_POST['format_slug']); // Capture the new format slug
        $niche_name  = ucwords(str_replace('-', ' ', $niche_slug));
        
        $existing_post = get_page_by_path($niche_slug, OBJECT, 'pseo_niche');
        
        $post_data = [
            'post_title'  => $niche_name,
            'post_name'   => $niche_slug,
            'post_type'   => 'pseo_niche',
            'post_status' => 'publish'
        ];

        if ($existing_post) {
            $post_data['ID'] = $existing_post->ID;
            $post_id = wp_update_post($post_data);
        } else {
            $post_id = wp_insert_post($post_data);
        }

        if (!is_wp_error($post_id)) {
            // 1. Save the Static Core Fields
            $core_fields = [
                'feat_headline', 'feat_subline',
                'f1_title', 'f1_desc', 'f2_title', 'f2_desc', 'f3_title', 'f3_desc', 'f4_title', 'f4_desc', 'f5_title', 'f5_desc', 'f6_title', 'f6_desc',
                'faq1_q', 'faq1_a', 'faq2_q', 'faq2_a', 'faq3_q', 'faq3_a', 'faq4_q', 'faq4_a', 'faq5_q', 'faq5_a',
                'cta_aspiration'
            ];
            foreach ($core_fields as $field) {
                if (isset($_POST[$field])) {
                    update_post_meta($post_id, $format_slug . '_' . $field, wp_kses_post($_POST[$field]));
                }
            }

            // 2. Save the 5 Rotating Themes as a JSON array
            if (!empty($_POST['ai_themes_json'])) {
                $themes_data = json_decode(stripslashes($_POST['ai_themes_json']), true);
                if (is_array($themes_data)) {
                    update_post_meta($post_id, $format_slug . '_ai_themes', $themes_data);
                }
            }

            $message = "Blueprint saved securely for: " . $niche_name . " (" . $service_formats[$format_slug] . ")";
            $active_tab = 'niches';
        }
    }
    
    // B. Save City Context Content
    if (isset($_POST['action']) && $_POST['action'] === 'save_city_context') {
        $city_id = intval($_POST['city_id']);
        $city_intro = wp_kses_post($_POST['city_intro']);
        
        if($city_id > 0) {
            update_post_meta($city_id, 'city_intro', $city_intro);
            $message = "Local SEO Context saved successfully.";
            $active_tab = 'cities';
        }
    }

    // C. Save Global SEO Settings
    if (isset($_POST['action']) && $_POST['action'] === 'save_seo') {
        update_option('pseo_global_settings', [
            'meta_title' => sanitize_text_field($_POST['meta_title']),
            'meta_desc'  => sanitize_textarea_field($_POST['meta_desc']),
            'og_image'   => esc_url_raw($_POST['og_image'])
        ]);
        $message = "Global SEO configuration updated.";
        $active_tab = 'seo';
    }
    
    // D. Toggle Location Status
    if (isset($_POST['action']) && $_POST['action'] === 'toggle_location') {
        $post_id = intval($_POST['post_id']);
        $new_status = sanitize_text_field($_POST['new_status']);
        wp_update_post(['ID' => $post_id, 'post_status' => $new_status]);
        $message = "City status updated.";
        $active_tab = 'locations';
    }

    // E. Save Listicle (Article Engine)
    if (isset($_POST['action']) && $_POST['action'] === 'save_listicle') {
        global $wpdb;
        $table_name = $wpdb->prefix . 'pseo_listicles';
        
        $city_slug = sanitize_title($_POST['listicle_city']);
        $niche_slug = sanitize_title($_POST['listicle_niche']);
        
        // Suppress errors temporarily in case the table hasn't fully registered yet
        $suppress = $wpdb->suppress_errors(true);
        $existing = $wpdb->get_var($wpdb->prepare("SELECT id FROM $table_name WHERE city_slug = %s AND niche_slug = %s", $city_slug, $niche_slug));
        
        $data = [
            'target_keyword' => sanitize_text_field($_POST['target_keyword']),
            'meta_title'     => sanitize_text_field($_POST['meta_title']),
            'content'        => wp_kses_post($_POST['content']), // Allows HTML from the AI
            'status'         => sanitize_text_field($_POST['status']),
            'updated_at'     => current_time('mysql')
        ];

        if ($existing) {
            $wpdb->update($table_name, $data, ['id' => $existing]);
        } else {
            $data['city_slug'] = $city_slug;
            $data['niche_slug'] = $niche_slug;
            $data['created_at'] = current_time('mysql');
            $wpdb->insert($table_name, $data);
        }
        $wpdb->suppress_errors($suppress);
        
        // Auto-publish the niche to ensure the listicle instantly appears in the sitemap
        if ($data['status'] === 'publish') {
            $existing_niche = get_page_by_path($niche_slug, OBJECT, 'pseo_niche');
            if (!$existing_niche || $existing_niche->post_status !== 'publish') {
                wp_insert_post([
                    'ID'          => $existing_niche ? $existing_niche->ID : 0,
                    'post_title'  => ucwords(str_replace('-', ' ', $niche_slug)),
                    'post_name'   => $niche_slug,
                    'post_type'   => 'pseo_niche',
                    'post_status' => 'publish'
                ]);
            }

            // --- INSTANT INDEXING TRIGGER ---
            require_once(__DIR__ . '/class-pseo-indexer.php');
            $indexer = new PSEO_Instant_Indexer();
            $new_url = "https://getonlinestudio.com/locations/{$city_slug}/top-{$niche_slug}-web-designers/";
            $indexer->ping_all_networks($new_url);
            // --------------------------------
        }
        
        $message = "Listicle Article saved to high-performance table!";
        $active_tab = 'listicles';
    }

    // G. Ping Indexer Manually
    if (isset($_POST['action']) && $_POST['action'] === 'ping_indexer') {
        $city_slug = sanitize_title($_POST['city_slug']);
        $niche_slug = sanitize_title($_POST['niche_slug']);
        
        require_once(__DIR__ . '/class-pseo-indexer.php');
        $indexer = new PSEO_Instant_Indexer();
        $url = "https://getonlinestudio.com/locations/{$city_slug}/top-{$niche_slug}-web-designers/";
        
        $success = $indexer->ping_all_networks($url);
        
        if ($success) {
            $message = "Successfully pinged Google & IndexNow for {$city_slug}/{$niche_slug}!";
        } else {
            $message = "Index ping failed. Check your server logs.";
        }
        $active_tab = 'listicles';
    }

    // H. Bulk Add Locations
    if (isset($_POST['action']) && $_POST['action'] === 'bulk_add_locations') {
        $raw_input = sanitize_text_field($_POST['locations_list']);
        $items = array_filter(array_map('trim', explode(',', $raw_input)));
        $added = 0;
        foreach ($items as $item) {
            $slug = sanitize_title($item);
            if (!get_page_by_path($slug, OBJECT, 'pseo_location')) {
                wp_insert_post([
                    'post_title' => ucwords($item),
                    'post_name' => $slug,
                    'post_type' => 'pseo_location',
                    'post_status' => 'publish'
                ]);
                $added++;
            }
        }
        $message = "Added $added new city/cities successfully.";
        $active_tab = 'locations';
    }

    // I. Bulk Add Niches
    if (isset($_POST['action']) && $_POST['action'] === 'bulk_add_niches') {
        $raw_input = sanitize_text_field($_POST['niches_list']);
        $items = array_filter(array_map('trim', explode(',', $raw_input)));
        $added = 0;
        foreach ($items as $item) {
            $slug = sanitize_title($item);
            if (!get_page_by_path($slug, OBJECT, 'pseo_niche')) {
                wp_insert_post([
                    'post_title' => ucwords($item),
                    'post_name' => $slug,
                    'post_type' => 'pseo_niche',
                    'post_status' => 'draft'
                ]);
                $added++;
            }
        }
        $message = "Added $added new niche(s) successfully.";
        $active_tab = 'niches';
    }
}

// F. AJAX: Load a single listicle row without page reload
if (isset($_GET['ajax']) && $_GET['ajax'] === 'load_listicle' && is_user_logged_in()) {
    global $wpdb;
    $city_slug  = sanitize_title($_GET['city']  ?? 'lagos');
    $niche_slug = sanitize_title($_GET['niche'] ?? 'law-firm');
    $table      = $wpdb->prefix . 'pseo_listicles';
    $suppress   = $wpdb->suppress_errors(true);
    $row        = $wpdb->get_row(
        $wpdb->prepare("SELECT * FROM $table WHERE city_slug = %s AND niche_slug = %s", $city_slug, $niche_slug),
        ARRAY_A
    );
    $wpdb->suppress_errors($suppress);
    wp_send_json($row ?: ['target_keyword' => '', 'meta_title' => '', 'content' => '', 'status' => 'draft']);
    exit;
}


// ---------------------------------------------------------
// DATA FETCHING FOR UI
// ---------------------------------------------------------
$locations = get_posts([
    'post_type' => 'pseo_location',
    'numberposts' => -1,
    'post_status' => ['publish', 'draft'],
    'orderby' => 'title',
    'order' => 'ASC'
]);

$all_niches_posts = get_posts(['post_type' => 'pseo_niche', 'numberposts' => -1, 'post_status' => ['publish', 'draft']]);
$niche_statuses = [];
$master_niches = [];

// Populate Dynamic Niches from Database
foreach($all_niches_posts as $np) { 
    $master_niches[$np->post_name] = $np->post_title;
    $niche_statuses[$np->post_name] = $np->post_status; 
}

// Ensure the core 40 default niches are ALWAYS included in the matrix/dropdowns
$hardcoded_niches = [
    'school'            => 'School',
    'church'            => 'Church',
    'hospital'          => 'Hospital',
    'law-firm'          => 'Law Firm',
    'hotel'             => 'Hotel',
    'restaurant'        => 'Restaurant',
    'ecommerce'         => 'E-commerce Store',
    'real-estate'       => 'Real Estate',
    'ngo'               => 'NGO',
    'fintech'           => 'Fintech Startup',
    'software'          => 'Software Company',
    'construction'      => 'Construction Company',
    'pharmacy'          => 'Pharmacy',
    'event-planning'    => 'Event Planning',
    'beauty-spa'        => 'Beauty Spa',
    'tech-startup'      => 'Tech Startup',
    'logistics'         => 'Logistics Company',
    'dental'            => 'Dental Clinic',
    'crypto'            => 'Crypto Trading',
    'solar'             => 'Solar Energy',
    'accounting'        => 'Accounting Firm',
    'bakery'            => 'Bakery',
    'fitness-gym'       => 'Fitness Gym',
    'interior-design'   => 'Interior Design',
    'travel-agency'     => 'Travel Agency',
    'photography'       => 'Photography Studio',
    'recruitment'       => 'Recruitment Agency',
    'microfinance'      => 'Microfinance Bank',
    'insurance'         => 'Insurance Company',
    'oil-gas'           => 'Oil and Gas Company',
    'wedding-planner'   => 'Wedding Planner',
    'catering'          => 'Catering Services',
    'cleaning'          => 'Cleaning Services',
    'driving-school'    => 'Driving School',
    'fashion-boutique'  => 'Fashion Boutique',
    'makeup-artist'     => 'Makeup Artist',
    'music-school'      => 'Music School',
    'pet-store'         => 'Pet Store',
    'supermarket'       => 'Supermarket',
    'waste-management'  => 'Waste Management',
];

// Merge defaults with DB
foreach ($hardcoded_niches as $slug => $name) {
    if (!isset($master_niches[$slug])) {
        $master_niches[$slug] = $name;
    }
}
asort($master_niches); // Sort alphabetically for clean UI

$active_cities_count = wp_count_posts('pseo_location')->publish ?? 0;
// We now calculate active niches accurately based on the merged array
$active_niches_count = count(array_filter($niche_statuses, fn($status) => $status === 'publish'));
$total_generated_pages = $active_cities_count * $active_niches_count;

// ─── NEIGHBOURHOOD PAGE COUNT ─────────────────────────────────────────────────
$total_approved_neighborhoods = 0;
$nb_library_path = __DIR__ . '/neighborhoods.json';
$nb_library_all  = file_exists($nb_library_path) ? (json_decode(file_get_contents($nb_library_path), true) ?? []) : [];
foreach ($locations as $loc) {
    if ($loc->post_status !== 'publish') continue;
    $approved = get_post_meta($loc->ID, '_pseo_active_neighborhoods', true);
    if (is_array($approved)) $total_approved_neighborhoods += count($approved);
}
$neighborhood_pages = $total_approved_neighborhoods * $active_niches_count * 5;
// Grand total is calculated after $total_published_listicles is known (further below)

$settings = get_option('pseo_global_settings', [
    'meta_title' => 'Top {niche} Website Designer in {city} | GetOnline Studio',
    'meta_desc' => '',
    'og_image' => ''
]);

// Fetch Local Competitors JSON for the AI Engine
$competitors_json = '{}';
$competitors_path_1 = '/home2/worldin6/public_html/getonlinestudio.com/wp/competitors.json'; 
$competitors_path_2 = __DIR__ . '/competitors.json'; 

if (file_exists($competitors_path_1)) {
    $competitors_json = file_get_contents($competitors_path_1);
} elseif (file_exists($competitors_path_2)) {
    $competitors_json = file_get_contents($competitors_path_2);
}

// Handle Active Niche & Format Tabs
$edit_niche_slug = isset($_GET['edit_niche']) ? sanitize_title($_GET['edit_niche']) : 'law-firm';
$edit_format_slug = isset($_GET['edit_format']) ? sanitize_title($_GET['edit_format']) : 'website-designer';
if (isset($_GET['edit_niche'])) { $active_tab = 'niches'; }

// Handle Active City Tab
$edit_city_id = isset($_GET['edit_city']) ? intval($_GET['edit_city']) : ($locations[0]->ID ?? 0);
if (isset($_GET['edit_city'])) { $active_tab = 'cities'; }

// Handle Active Listicle Tab
$edit_listicle_city = isset($_GET['listicle_city']) ? sanitize_title($_GET['listicle_city']) : 'lagos';
$edit_listicle_niche = isset($_GET['listicle_niche']) ? sanitize_title($_GET['listicle_niche']) : 'law-firm';
if (isset($_GET['listicle_city'])) { $active_tab = 'listicles'; }

// Fetch existing listicle from custom DB table
global $wpdb;
$listicle_table = $wpdb->prefix . 'pseo_listicles';
$suppress = $wpdb->suppress_errors(true);
$existing_listicle = $wpdb->get_row($wpdb->prepare("SELECT * FROM $listicle_table WHERE city_slug = %s AND niche_slug = %s", $edit_listicle_city, $edit_listicle_niche), ARRAY_A);
$wpdb->suppress_errors($suppress);

if (!$existing_listicle) {
    $existing_listicle = [
        'target_keyword' => '',
        'meta_title' => '',
        'content' => '',
        'status' => 'draft'
    ];
}

// Fetch Current User for Profile Image
$current_user = wp_get_current_user();
$avatar_url = get_avatar_url($current_user->ID, ['size' => 80]); 

$existing_niche_post = get_page_by_path($edit_niche_slug, OBJECT, 'pseo_niche');
$db_themes = [];
$niche_core_meta = [];

if ($existing_niche_post) {
    // Fetch 5 Themes Array (Upgraded from 3)
    $db_themes = get_post_meta($existing_niche_post->ID, $edit_format_slug . '_ai_themes', true);
    if (empty($db_themes) || !is_array($db_themes)) {
        $db_themes = [];
    }
    // Ensure we have exactly 5 theme slots
    while(count($db_themes) < 5) {
        $db_themes[] = [
            'opening_label'=>'', 'opening_angle'=>'', 'hero_subheadline'=>'', 
            'reality_p1'=>'', 'reality_p2'=>'', 'reality_p3'=>'', 'reality_p4'=>'', 
            'pos1_title'=>'', 'pos1_desc'=>'', 'pos2_title'=>'', 'pos2_desc'=>'', 
            'pos3_title'=>'', 'pos3_desc'=>'', 'pos4_title'=>'', 'pos4_desc'=>''
        ];
    }
    
    // Fetch Static Core Meta
    $core_keys = [
        'feat_headline', 'feat_subline', 
        'f1_title', 'f1_desc', 'f2_title', 'f2_desc', 'f3_title', 'f3_desc', 'f4_title', 'f4_desc', 'f5_title', 'f5_desc', 'f6_title', 'f6_desc', 
        'faq1_q', 'faq1_a', 'faq2_q', 'faq2_a', 'faq3_q', 'faq3_a', 'faq4_q', 'faq4_a', 'faq5_q', 'faq5_a', 
        'cta_aspiration'
    ];
    foreach ($core_keys as $key) { 
        $niche_core_meta[$key] = get_post_meta($existing_niche_post->ID, $edit_format_slug . '_' . $key, true); 
    }
}

// Convert Themes array to JSON for Javascript
$js_themes_data = json_encode($db_themes);

// ─── NEW: FETCH COMPLETION STATUS FOR ALL FORMATS ────────────────────────────
$format_completion_status = [];
if ($existing_niche_post) {
    foreach ($service_formats as $f_slug => $f_name) {
        $saved_themes = get_post_meta($existing_niche_post->ID, $f_slug . '_ai_themes', true);
        $theme_count = 0;
        
        // Count how many valid themes exist for this format
        if (!empty($saved_themes) && is_array($saved_themes)) {
            foreach ($saved_themes as $theme) {
                // A theme is considered "valid" if it has at least a hero headline saved
                if (!empty($theme['hero_subheadline'])) {
                    $theme_count++;
                }
            }
        }
        $format_completion_status[$f_slug] = $theme_count;
    }
} else {
    foreach ($service_formats as $f_slug => $f_name) {
        $format_completion_status[$f_slug] = 0;
    }
}
// ─────────────────────────────────────────────────────────────────────────────

// Fetch ALL listicles for Coverage Grid and Database View
global $wpdb;
$listicle_table   = $wpdb->prefix . 'pseo_listicles';
$suppress         = $wpdb->suppress_errors(true);
$all_listicles_detailed = $wpdb->get_results("SELECT id, city_slug, niche_slug, target_keyword, status, updated_at FROM $listicle_table ORDER BY updated_at DESC", ARRAY_A);
$wpdb->suppress_errors($suppress);

$listicle_map = [];
$total_published_listicles = 0;
$total_draft_listicles = 0;

foreach ((array)$all_listicles_detailed as $row) {
    $listicle_map[$row['city_slug']][$row['niche_slug']] = $row['status'];
    if ($row['status'] === 'publish') {
        $total_published_listicles++;
    } else {
        $total_draft_listicles++;
    }
}

$publish_queue = [];
foreach ($locations as $loc) {
    if ($loc->post_status !== 'publish') continue; // only active cities
    foreach ($master_niches as $n_slug => $n_name) {
        $lst_status = $listicle_map[$loc->post_name][$n_slug] ?? 'empty';
        if ($lst_status === 'empty') {
            $publish_queue[] = [
                'city_slug'  => $loc->post_name,
                'city_name'  => $loc->post_title,
                'niche_slug' => $n_slug,
                'niche_name' => $n_name,
            ];
        }
    }
}
$publish_queue_display = array_slice($publish_queue, 0, 20);
$queue_total = count($publish_queue);

// ─── GRAND TOTAL ──────────────────────────────────────────────────────────────
$city_niche_pages  = $active_cities_count * $active_niches_count * 5;
$grand_total_pages = $city_niche_pages + $neighborhood_pages + $total_published_listicles;
?>
<!DOCTYPE html>
<html lang="en" class="dark">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>pSEO Command Center | GetOnline Studio</title>

    <!-- PWA Meta Tags -->
    <meta name="application-name" content="Studio pSEO">
    <meta name="mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">
    <meta name="apple-mobile-web-app-title" content="Studio pSEO">
    <meta name="theme-color" content="#7e22ce">
    <meta name="msapplication-TileColor" content="#7e22ce">
    <meta name="msapplication-TileImage" content="https://getonlinestudio.com/insights/wp-content/uploads/2026/02/GetOnline_Studio_Logo.jpg">

    <!-- Icons & Manifest -->
    <link rel="icon" type="image/jpeg" href="https://getonlinestudio.com/insights/wp-content/uploads/2026/02/GetOnline_Studio_Logo.jpg">
    <link rel="apple-touch-icon" href="https://getonlinestudio.com/insights/wp-content/uploads/2026/02/GetOnline_Studio_Logo.jpg">
    <link rel="manifest" href="/wp/manifest.json">

    <link href="https://fonts.googleapis.com/css2?family=Manrope:wght@300;400;500;600;700&family=Syne:wght@400;600;700&display=swap" rel="stylesheet">
    <script src="https://unpkg.com/lucide@latest"></script>
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            darkMode: 'class',
            theme: {
                extend: {
                    colors: {
                        'matte-black': '#0a0a0a', 'panel-dark': '#121212', 'card-dark': '#1a1a1a',
                        'lavender': '#e9d5ff', 'sharp-purple': '#7e22ce', 'success-green': '#4ade80',
                    },
                    fontFamily: { 'syne': ['Syne', 'sans-serif'], 'manrope': ['Manrope', 'sans-serif'], }
                }
            }
        }
    </script>
    <style>
        body { background-color: #0a0a0a; color: #e9d5ff; font-family: 'Manrope', sans-serif; }
        ::-webkit-scrollbar { width: 6px; height: 6px; }
        ::-webkit-scrollbar-track { background: #0a0a0a; }
        ::-webkit-scrollbar-thumb { background: #333; border-radius: 10px; }
        ::-webkit-scrollbar-thumb:hover { background: #7e22ce; }
        .toggle-checkbox:checked { right: 0; border-color: #4ade80; }
        .toggle-checkbox:checked + .toggle-label { background-color: #4ade80; }
        .toggle-checkbox { right: 4px; z-index: 1; transition: all 0.3s; }
        .toggle-label { transition: all 0.3s; }
        .tab-content { display: none; opacity: 0; transition: opacity 0.3s ease; }
        .tab-content.active { display: block; opacity: 1; }
        #sidebar { transition: transform 0.3s ease-in-out; }
        #mobile-overlay { transition: opacity 0.3s ease-in-out; }
        @keyframes slideInUp { from { transform: translateY(100%); opacity: 0; } to { transform: translateY(0); opacity: 1; } }
        .toast { animation: slideInUp 0.3s ease forwards; }
        
        input, textarea, select { font-size: 16px !important; }
        @media (min-width: 768px) {
            input, textarea, select { font-size: 14px !important; }
        }
        textarea { transition: border-color 0.2s; }
    </style>
</head>
<body class="flex h-[100dvh] overflow-hidden antialiased relative">

    <?php if($message): ?>
    <div id="toast" class="toast fixed bottom-4 left-4 right-4 md:left-auto md:w-max md:bottom-6 md:right-6 z-[100] bg-success-green/20 border border-success-green/40 text-success-green px-6 py-4 md:py-3 rounded-lg backdrop-blur-md flex items-center gap-3 shadow-[0_0_20px_rgba(74,222,128,0.2)]">
        <i data-lucide="check-circle" class="w-5 h-5 flex-shrink-0"></i>
        <span class="font-manrope font-bold text-sm leading-snug"><?= $message ?></span>
    </div>
    <script>setTimeout(() => { document.getElementById('toast').style.display = 'none'; }, 4000);</script>
    <?php endif; ?>

    <div id="mobile-overlay" class="fixed inset-0 bg-black/80 z-40 hidden md:hidden opacity-0 backdrop-blur-sm" onclick="toggleSidebar()"></div>

    <aside id="sidebar" class="fixed md:relative inset-y-0 left-0 w-64 md:w-64 lg:w-72 bg-panel-dark border-r border-white/5 flex flex-col h-[100dvh] flex-shrink-0 z-50 transform -translate-x-full md:translate-x-0">
        <div class="h-16 md:h-20 flex items-center justify-between px-6 border-b border-white/5 flex-shrink-0">
            <div class="flex items-center">
                <span class="font-syne font-bold text-2xl text-white tracking-wider">GO<span class="text-sharp-purple">.</span></span>
                <span class="ml-3 font-mono text-[10px] text-sharp-purple border border-sharp-purple/30 px-2 py-0.5 rounded-full hidden sm:inline-block">WP Engine</span>
            </div>
            <button class="md:hidden text-lavender/50 hover:text-white p-2" onclick="toggleSidebar()"><i data-lucide="x" class="w-6 h-6"></i></button>
        </div>

        <nav class="flex-1 px-4 py-6 space-y-2 overflow-y-auto pb-20 md:pb-6">
            <button onclick="switchTab('dashboard')" class="nav-btn w-full flex items-center gap-3 px-4 py-3 rounded-lg text-sm font-medium transition-colors <?= $active_tab == 'dashboard' ? 'bg-sharp-purple/10 text-white border border-sharp-purple/20' : 'text-lavender/60 hover:text-white hover:bg-white/5 border border-transparent' ?>" id="btn-dashboard">
                <i data-lucide="layout-dashboard" class="w-4 h-4 <?= $active_tab == 'dashboard' ? 'text-sharp-purple' : '' ?>"></i> Overview
            </button>
            <button onclick="switchTab('matrix')" class="nav-btn w-full flex items-center gap-3 px-4 py-3 rounded-lg text-sm font-medium transition-colors <?= $active_tab == 'matrix' ? 'bg-sharp-purple/10 text-white border border-sharp-purple/20' : 'text-lavender/60 hover:text-white hover:bg-white/5 border border-transparent' ?>" id="btn-matrix">
                <i data-lucide="grid" class="w-4 h-4 <?= $active_tab == 'matrix' ? 'text-sharp-purple' : '' ?>"></i> Content Matrix
            </button>
            <button onclick="switchTab('locations')" class="nav-btn w-full flex items-center gap-3 px-4 py-3 rounded-lg text-sm font-medium transition-colors <?= $active_tab == 'locations' ? 'bg-sharp-purple/10 text-white border border-sharp-purple/20' : 'text-lavender/60 hover:text-white hover:bg-white/5 border border-transparent' ?>" id="btn-locations">
                <i data-lucide="toggle-right" class="w-4 h-4 <?= $active_tab == 'locations' ? 'text-sharp-purple' : '' ?>"></i> Location Engine
            </button>
            <button onclick="switchTab('cities')" class="nav-btn w-full flex items-center gap-3 px-4 py-3 rounded-lg text-sm font-medium transition-colors <?= $active_tab == 'cities' ? 'bg-sharp-purple/10 text-white border border-sharp-purple/20' : 'text-lavender/60 hover:text-white hover:bg-white/5 border border-transparent' ?>" id="btn-cities">
                <i data-lucide="map-pin" class="w-4 h-4 <?= $active_tab == 'cities' ? 'text-sharp-purple' : '' ?>"></i> City Context Editor
            </button>
            <button onclick="switchTab('niches')" class="nav-btn w-full flex items-center gap-3 px-4 py-3 rounded-lg text-sm font-medium transition-colors <?= $active_tab == 'niches' ? 'bg-sharp-purple/10 text-white border border-sharp-purple/20' : 'text-lavender/60 hover:text-white hover:bg-white/5 border border-transparent' ?>" id="btn-niches">
                <i data-lucide="briefcase" class="w-4 h-4 <?= $active_tab == 'niches' ? 'text-sharp-purple' : '' ?>"></i> Niche AI Engine
            </button>
            <button onclick="switchTab('seo')" class="nav-btn w-full flex items-center gap-3 px-4 py-3 rounded-lg text-sm font-medium transition-colors <?= $active_tab == 'seo' ? 'bg-sharp-purple/10 text-white border border-sharp-purple/20' : 'text-lavender/60 hover:text-white hover:bg-white/5 border border-transparent' ?>" id="btn-seo">
                <i data-lucide="search" class="w-4 h-4 <?= $active_tab == 'seo' ? 'text-sharp-purple' : '' ?>"></i> SEO Formulas
            </button>
            <button onclick="switchTab('listicles')" class="nav-btn w-full flex items-center gap-3 px-4 py-3 rounded-lg text-sm font-medium transition-colors <?= $active_tab == 'listicles' ? 'bg-sharp-purple/10 text-white border border-sharp-purple/20' : 'text-lavender/60 hover:text-white hover:bg-white/5 border border-transparent' ?>" id="btn-listicles">
                <i data-lucide="book-open" class="w-4 h-4 <?= $active_tab == 'listicles' ? 'text-sharp-purple' : '' ?>"></i> Article Engine
            </button>
            <button onclick="switchTab('social')" class="nav-btn w-full flex items-center gap-3 px-4 py-3 rounded-lg text-sm font-medium transition-colors <?= $active_tab == 'social' ? 'bg-sharp-purple/10 text-white border border-sharp-purple/20' : 'text-lavender/60 hover:text-white hover:bg-white/5 border border-transparent' ?>" id="btn-social">
                <i data-lucide="share-2" class="w-4 h-4 <?= $active_tab == 'social' ? 'text-sharp-purple' : '' ?>"></i> Social AI Engine
            </button>

            <div class="mt-4 pt-4 border-t border-white/5 space-y-1">
                <p class="px-4 text-[9px] font-bold uppercase tracking-widest text-lavender/30 mb-2">External Tools</p>
                <a href="https://getonlinestudio.com/wp/city-niche-manager.php" target="_blank" class="w-full flex items-center gap-3 px-4 py-3 rounded-lg text-sm font-medium text-lavender/60 hover:text-white hover:bg-white/5 transition-colors border border-transparent">
                    <i data-lucide="map" class="w-4 h-4"></i> City Niche Manager
                </a>
                <a href="https://getonlinestudio.com/wp/studio-data-manager.php" target="_blank" class="w-full flex items-center gap-3 px-4 py-3 rounded-lg text-sm font-medium text-lavender/60 hover:text-white hover:bg-white/5 transition-colors border border-transparent">
                    <i data-lucide="database" class="w-4 h-4"></i> Data Manager
                </a>
                <a href="https://getonlinestudio.com/local-hubs.php" target="_blank" class="w-full flex items-center gap-3 px-4 py-3 rounded-lg text-sm font-medium text-lavender/60 hover:text-white hover:bg-white/5 transition-colors border border-transparent">
                    <i data-lucide="map-pin" class="w-4 h-4"></i> Local Hubs
                </a>
                <a href="https://getonlinestudio.com/city-pillar-editor.php" target="_blank" class="w-full flex items-center gap-3 px-4 py-3 rounded-lg text-sm font-medium text-lavender/60 hover:text-white hover:bg-white/5 transition-colors border border-transparent">
                    <i data-lucide="pencil-ruler" class="w-4 h-4"></i> City Pillar Editor
                </a>
                <a href="/sitemap.xml" target="_blank" class="w-full flex items-center gap-3 px-4 py-3 rounded-lg text-sm font-medium text-lavender/60 hover:text-white hover:bg-white/5 transition-colors border border-transparent">
                    <i data-lucide="external-link" class="w-4 h-4"></i> View XML Sitemap
                </a>
                <a href="/wp/pwa-install.php" class="w-full flex items-center gap-3 px-4 py-3 rounded-lg text-sm font-medium text-lavender/60 hover:text-white hover:bg-white/5 transition-colors border border-transparent">
                    <i data-lucide="download" class="w-4 h-4"></i> Install App
                </a>
            </div>
        </nav>

        <div class="p-4 border-t border-white/5 flex-shrink-0 bg-panel-dark">
            <a href="<?= wp_logout_url(home_url()) ?>" class="w-full flex items-center justify-center gap-2 bg-white/10 text-white font-bold text-xs uppercase tracking-widest py-3 rounded-lg hover:bg-red-500/20 hover:text-red-400 transition-all shadow-[0_0_15px_rgba(255,255,255,0.05)] border border-white/10">
                <i data-lucide="log-out" class="w-4 h-4"></i> Logout
            </a>
        </div>
    </aside>

    <main class="flex-1 flex flex-col h-[100dvh] overflow-hidden bg-matte-black relative w-full min-w-0">
        
        <header class="h-16 md:h-20 border-b border-white/5 flex items-center justify-between px-4 md:px-8 bg-panel-dark/50 backdrop-blur-md z-10 flex-shrink-0">
            <div class="flex items-center gap-4">
                <button class="md:hidden text-lavender hover:text-white p-2 -ml-2" onclick="toggleSidebar()">
                    <i data-lucide="menu" class="w-6 h-6"></i>
                </button>
                <div class="hidden sm:block">
                    <h2 class="font-syne text-xl text-white font-semibold">Command Center</h2>
                    <p class="text-xs text-success-green flex items-center gap-1"><i data-lucide="check-circle" class="w-3 h-3"></i> Connected securely</p>
                </div>
            </div>
            <div class="flex items-center gap-4">
                <a href="/" target="_blank" class="hidden sm:flex text-sm text-lavender/70 hover:text-white items-center gap-2 transition-colors">
                    <i data-lucide="external-link" class="w-4 h-4"></i> Live Site
                </a>
                <div class="w-8 h-8 md:w-10 md:h-10 rounded-full bg-sharp-purple flex items-center justify-center text-white font-bold text-sm md:text-base font-syne border border-white/20 shadow-[0_0_10px_rgba(126,34,206,0.5)] overflow-hidden">
                    <?php if($avatar_url): ?>
                        <img src="<?= esc_url($avatar_url) ?>" alt="<?= esc_attr($current_user->display_name) ?>" class="w-full h-full object-cover">
                    <?php else: ?>
                        <?= strtoupper(substr($current_user->display_name, 0, 1)) ?>
                    <?php endif; ?>
                </div>
            </div>
        </header>

        <div class="flex-1 overflow-y-auto p-4 md:p-8 relative scroll-smooth pb-24 md:pb-12 w-full max-w-full">
            <div class="absolute top-0 right-0 w-64 h-64 md:w-96 md:h-96 bg-sharp-purple/5 rounded-full blur-[100px] pointer-events-none"></div>

            <div id="tab-dashboard" class="tab-content <?= $active_tab == 'dashboard' ? 'active' : '' ?>">
                <div class="flex justify-between items-end mb-6 md:mb-8">
                    <h1 class="font-syne text-2xl md:text-3xl font-bold text-white">System Overview</h1>
                </div>

                <div class="grid grid-cols-1 gap-4 md:gap-6 mb-6">
                    <!-- Grand Total Banner -->
                    <div class="bg-gradient-to-r from-sharp-purple/20 to-transparent border border-sharp-purple/30 p-5 md:p-6 rounded-xl flex items-center justify-between">
                        <div>
                            <p class="text-xs md:text-sm text-lavender/50 mb-1">Total Indexed Pages (All Sources)</p>
                            <h3 class="font-syne text-4xl md:text-5xl font-bold text-white"><?= number_format($grand_total_pages) ?></h3>
                            <p class="text-[10px] md:text-xs text-lavender/50 mt-2 flex flex-wrap gap-x-3 gap-y-1">
                                <span><span class="text-sharp-purple font-bold"><?= number_format($city_niche_pages) ?></span> city/niche pages</span>
                                <span class="text-lavender/20">+</span>
                                <span><span class="text-blue-400 font-bold"><?= number_format($neighborhood_pages) ?></span> neighbourhood pages</span>
                                <span class="text-lavender/20">+</span>
                                <span><span class="text-success-green font-bold"><?= number_format($total_published_listicles) ?></span> listicles</span>
                            </p>
                        </div>
                        <div class="w-12 h-12 md:w-14 md:h-14 rounded-full bg-sharp-purple/20 flex items-center justify-center flex-shrink-0">
                            <i data-lucide="globe" class="w-6 h-6 text-sharp-purple"></i>
                        </div>
                    </div>
                </div>

                <div class="grid grid-cols-2 md:grid-cols-4 gap-4 md:gap-6 mb-8">
                    <!-- Active Cities -->
                    <div class="bg-card-dark border border-white/5 p-5 md:p-6 rounded-xl flex items-center justify-between">
                        <div>
                            <p class="text-xs text-lavender/50 mb-1">Active Cities</p>
                            <h3 class="font-syne text-3xl font-bold text-white"><?= $active_cities_count ?> <span class="text-base text-lavender/30">/ <?= count($locations) ?></span></h3>
                            <p class="text-[10px] text-lavender/50 mt-2">WP Publish Status</p>
                        </div>
                        <div class="w-10 h-10 rounded-full bg-blue-500/10 flex items-center justify-center flex-shrink-0"><i data-lucide="map" class="w-5 h-5 text-blue-500"></i></div>
                    </div>
                    <!-- Active Niches -->
                    <div class="bg-card-dark border border-white/5 p-5 md:p-6 rounded-xl flex items-center justify-between">
                        <div>
                            <p class="text-xs text-lavender/50 mb-1">Active Niches</p>
                            <h3 class="font-syne text-3xl font-bold text-white"><?= $active_niches_count ?> <span class="text-base text-lavender/30">/ <?= count($master_niches) ?></span></h3>
                            <p class="text-[10px] text-lavender/50 mt-2">WP Publish Status</p>
                        </div>
                        <div class="w-10 h-10 rounded-full bg-success-green/10 flex items-center justify-center flex-shrink-0"><i data-lucide="file-text" class="w-5 h-5 text-success-green"></i></div>
                    </div>
                    <!-- Neighbourhoods -->
                    <div class="bg-card-dark border border-white/5 p-5 md:p-6 rounded-xl flex items-center justify-between">
                        <div>
                            <p class="text-xs text-lavender/50 mb-1">Neighbourhoods</p>
                            <h3 class="font-syne text-3xl font-bold text-white"><?= number_format($total_approved_neighborhoods) ?></h3>
                            <p class="text-[10px] text-lavender/50 mt-2"><?= number_format($neighborhood_pages) ?> pages generated</p>
                        </div>
                        <div class="w-10 h-10 rounded-full bg-blue-400/10 flex items-center justify-center flex-shrink-0"><i data-lucide="map-pin" class="w-5 h-5 text-blue-400"></i></div>
                    </div>
                    <!-- Listicles -->
                    <div class="bg-card-dark border border-white/5 p-5 md:p-6 rounded-xl flex items-center justify-between">
                        <div>
                            <p class="text-xs text-lavender/50 mb-1">Listicles</p>
                            <h3 class="font-syne text-3xl font-bold text-white"><?= number_format($total_published_listicles) ?> <span class="text-base text-lavender/30">/ <?= number_format($total_published_listicles + $total_draft_listicles) ?></span></h3>
                            <p class="text-[10px] text-yellow-400 mt-2"><?= number_format($total_draft_listicles) ?> drafts pending</p>
                        </div>
                        <div class="w-10 h-10 rounded-full bg-yellow-500/10 flex items-center justify-center flex-shrink-0"><i data-lucide="newspaper" class="w-5 h-5 text-yellow-400"></i></div>
                    </div>
                </div>

                <div class="bg-card-dark border border-white/5 rounded-xl p-5 md:p-6 mb-8">
                    <h3 class="font-syne text-sm font-bold text-white mb-4">Page Breakdown by Service Format</h3>
                    <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-5 gap-3">
                        <?php
                        $formats = [
                            'website-designer'        => ['label' => 'Website Designer',  'color' => 'text-sharp-purple', 'bg' => 'bg-sharp-purple/10'],
                            'website-developer'       => ['label' => 'Website Developer',  'color' => 'text-blue-400',    'bg' => 'bg-blue-500/10'],
                            'web-design-agency'       => ['label' => 'Web Design Agency',  'color' => 'text-yellow-400',  'bg' => 'bg-yellow-500/10'],
                            'website-design-services' => ['label' => 'Design Services',    'color' => 'text-pink-400',    'bg' => 'bg-pink-500/10'],
                            'branding-agency'         => ['label' => 'Branding Agency',    'color' => 'text-success-green','bg' => 'bg-success-green/10'],
                        ];
                        foreach ($formats as $fmt_slug => $fmt):
                            $fmt_pages = $active_cities_count * count($master_niches);
                        ?>
                        <div class="<?= $fmt['bg'] ?> border border-white/5 rounded-lg p-3 text-center">
                            <p class="font-syne font-bold text-lg <?= $fmt['color'] ?>"><?= number_format($fmt_pages) ?></p>
                            <p class="font-mono text-[9px] text-lavender/40 uppercase tracking-widest mt-1 leading-tight"><?= $fmt['label'] ?></p>
                        </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>

            <div id="tab-matrix" class="tab-content <?= $active_tab == 'matrix' ? 'active' : '' ?>">
                <div class="flex flex-col sm:flex-row justify-between items-start sm:items-end mb-6 md:mb-8 border-b border-white/5 pb-6 gap-4">
                    <div>
                        <h1 class="font-syne text-2xl md:text-3xl font-bold text-white mb-2">Completion Matrix</h1>
                        <p class="text-xs md:text-sm text-lavender/60">Overview of your Niche × City pairs. Content blueprints apply to all 5 service formats.</p>
                    </div>
                    <div class="flex flex-wrap gap-3 text-[10px] md:text-xs font-mono uppercase tracking-widest bg-card-dark p-3 rounded-lg border border-white/5">
                        <div class="flex items-center gap-2"><div class="w-3 h-3 bg-success-green rounded-sm shadow-[0_0_8px_rgba(74,222,128,0.5)] flex-shrink-0"></div> Live</div>
                        <div class="flex items-center gap-2"><div class="w-3 h-3 bg-yellow-500 rounded-sm flex-shrink-0"></div> Draft</div>
                        <div class="flex items-center gap-2"><div class="w-3 h-3 bg-white/5 border border-white/10 rounded-sm flex-shrink-0"></div> Empty</div>
                    </div>
                </div>

                <div class="mb-5 p-3 bg-sharp-purple/10 border border-sharp-purple/20 rounded-lg flex items-center gap-3">
                    <i data-lucide="layers" class="w-4 h-4 text-sharp-purple flex-shrink-0"></i>
                    <p class="text-xs text-lavender/70">Each cell below represents <strong class="text-white">5 pages</strong> (one per service format). A green cell means the niche blueprint is published and all 5 format pages are live for that city.</p>
                </div>

                <div class="md:hidden flex items-center justify-end gap-2 text-[10px] text-lavender/50 mb-3 font-mono uppercase tracking-widest">
                    <i data-lucide="move-horizontal" class="w-4 h-4 text-sharp-purple animate-pulse"></i> Swipe matrix to view cities
                </div>

                <div class="bg-card-dark border border-white/5 rounded-xl overflow-x-auto pb-4 relative w-full">
                    <table class="w-full text-left border-collapse min-w-[800px]">
                        <thead>
                            <tr>
                                <th class="p-4 border-b border-white/10 text-xs font-syne text-lavender/50 uppercase sticky left-0 bg-card-dark z-20 w-48 shadow-[4px_0_10px_rgba(0,0,0,0.5)]">Niche</th>
                                <?php foreach($locations as $loc): ?>
                                <th class="p-2 border-b border-white/10 align-bottom">
                                    <div class="w-8 mx-auto -rotate-45 transform origin-bottom-left whitespace-nowrap text-[10px] font-mono text-lavender/50 pb-2">
                                        <?= esc_html($loc->post_title) ?>
                                    </div>
                                </th>
                                <?php endforeach; ?>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach($master_niches as $n_slug => $n_name):
                                $n_stat = isset($niche_statuses[$n_slug]) ? $niche_statuses[$n_slug] : 'empty';
                            ?>
                            <tr class="hover:bg-white/5 transition-colors border-b border-white/5">
                                <td class="p-4 text-xs text-white font-semibold sticky left-0 bg-card-dark z-10 shadow-[4px_0_10px_rgba(0,0,0,0.5)] whitespace-nowrap">
                                    <a href="?edit_niche=<?= $n_slug ?>" class="hover:text-sharp-purple transition-colors block w-full"><?= $n_name ?></a>
                                </td>
                                <?php foreach($locations as $loc):
                                    $l_stat = $loc->post_status;
                                    if ($n_stat === 'publish' && $l_stat === 'publish') {
                                        $color = 'bg-success-green shadow-[0_0_5px_rgba(74,222,128,0.4)]';
                                    } elseif ($n_stat === 'empty') {
                                        $color = 'bg-white/5 border border-white/10';
                                    } else {
                                        $color = 'bg-yellow-500';
                                    }
                                ?>
                                <td class="p-2 text-center">
                                    <a href="?edit_niche=<?= $n_slug ?>" class="block w-6 h-6 md:w-4 md:h-4 mx-auto rounded-md md:rounded-sm <?= $color ?> hover:scale-125 transition-transform"></a>
                                </td>
                                <?php endforeach; ?>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>

            <div id="tab-locations" class="tab-content <?= $active_tab == 'locations' ? 'active' : '' ?>">
                <div class="flex flex-col sm:flex-row justify-between items-start sm:items-end mb-6 md:mb-8 border-b border-white/5 pb-6 gap-4">
                    <div>
                        <h1 class="font-syne text-2xl md:text-3xl font-bold text-white mb-2">Location Engine</h1>
                        <p class="text-xs md:text-sm text-lavender/60">Toggle cities on/off globally across the site.</p>
                    </div>
                </div>

                <div class="bg-card-dark border border-white/5 rounded-xl p-5 mb-8">
                    <h3 class="font-syne text-lg font-bold text-white mb-2">Bulk Add Cities</h3>
                    <p class="text-[10px] uppercase tracking-widest text-lavender/50 mb-4">Paste multiple cities separated by commas.</p>
                    <form method="POST" class="flex flex-col sm:flex-row gap-4 items-end">
                        <input type="hidden" name="action" value="bulk_add_locations">
                        <div class="flex-1 w-full">
                            <input type="text" name="locations_list" placeholder="e.g. Asaba, Owerri, Warri..." class="w-full bg-matte-black border border-white/10 rounded-lg p-3 text-white focus:border-sharp-purple outline-none text-sm" required>
                        </div>
                        <button type="submit" class="w-full sm:w-auto px-6 py-3 bg-sharp-purple text-white rounded-lg text-sm font-bold shadow-[0_0_15px_rgba(126,34,206,0.3)] hover:bg-white hover:text-matte-black transition-all whitespace-nowrap">
                            Add Cities
                        </button>
                    </form>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-4">
                    <?php if (empty($locations)): ?>
                        <p class="text-lavender/50 col-span-full">No locations found. Make sure the auto-seed function fired.</p>
                    <?php endif; ?>
                    <?php foreach($locations as $loc): 
                        $is_active = ($loc->post_status === 'publish');
                    ?>
                    <div class="bg-card-dark border <?= $is_active ? 'border-success-green/30' : 'border-white/5 opacity-70' ?> p-4 md:p-5 rounded-xl flex items-center justify-between transition-all min-w-0">
                        <div class="flex items-center gap-3 min-w-0 pr-3">
                            <div class="w-8 h-8 rounded-lg <?= $is_active ? 'bg-success-green/10' : 'bg-white/5' ?> flex items-center justify-center flex-shrink-0">
                                <i data-lucide="map-pin" class="w-4 h-4 <?= $is_active ? 'text-success-green' : 'text-lavender/50' ?>"></i>
                            </div>
                            <div class="min-w-0">
                                <h3 class="font-syne text-sm md:text-base font-bold text-white truncate"><?= esc_html($loc->post_title) ?></h3>
                            </div>
                        </div>
                        <form method="POST" class="m-0 p-0 flex-shrink-0">
                            <input type="hidden" name="action" value="toggle_location">
                            <input type="hidden" name="post_id" value="<?= $loc->ID ?>">
                            <input type="hidden" name="new_status" value="<?= $is_active ? 'draft' : 'publish' ?>">
                            <button type="submit" class="relative inline-block w-10 align-middle select-none transition duration-200 ease-in flex-shrink-0">
                                <span class="toggle-checkbox absolute block w-5 h-5 rounded-full bg-white border-4 appearance-none cursor-pointer <?= $is_active ? 'right-0 border-success-green' : 'right-4 border-gray-300' ?>"></span>
                                <span class="toggle-label block overflow-hidden h-5 rounded-full cursor-pointer <?= $is_active ? 'bg-success-green' : 'bg-white/20' ?>"></span>
                            </button>
                        </form>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>

            <div id="tab-cities" class="tab-content <?= $active_tab == 'cities' ? 'active' : '' ?>">
                <div class="flex flex-col sm:flex-row justify-between items-start sm:items-end mb-6 md:mb-8 border-b border-white/5 pb-6 gap-4">
                    <div class="w-full sm:w-auto">
                        <h1 class="font-syne text-2xl md:text-3xl font-bold text-white mb-2">City Context AI</h1>
                        <p class="text-xs md:text-sm text-lavender/60">Inject highly specific, local SEO context into the DOM.</p>
                    </div>
                    
                    <select onchange="window.location.href='?edit_city='+this.value" class="bg-card-dark border border-white/10 text-white px-4 py-3 md:py-2 rounded-lg text-base md:text-sm outline-none focus:border-sharp-purple w-full sm:w-auto max-h-60 overflow-y-auto">
                        <?php foreach($locations as $loc): ?>
                            <option value="<?= $loc->ID ?>" <?= $edit_city_id == $loc->ID ? 'selected' : '' ?>><?= esc_html($loc->post_title) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <?php 
                $selected_city_name = 'Lagos';
                $selected_city_intro = '';
                foreach($locations as $loc) {
                    if($loc->ID == $edit_city_id) {
                        $selected_city_name = $loc->post_title;
                        $selected_city_intro = get_post_meta($loc->ID, 'city_intro', true);
                        break;
                    }
                }
                ?>

                <div class="bg-card-dark border border-white/5 rounded-xl overflow-hidden max-w-4xl">
                    <form method="POST" id="city-form" class="m-0 p-0">
                        <input type="hidden" name="action" value="save_city_context">
                        <input type="hidden" name="city_id" value="<?= esc_attr($edit_city_id) ?>">

                        <div class="p-4 md:p-6 border-b border-white/5 bg-panel-dark/40 flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
                            <div class="flex items-center gap-3 min-w-0">
                                <i data-lucide="map" class="w-5 h-5 text-blue-500 flex-shrink-0"></i>
                                <h3 class="font-syne text-base md:text-lg font-bold text-white truncate">Local Context: <span class="text-blue-500" id="current-city-name"><?= esc_html($selected_city_name) ?></span></h3>
                            </div>
                            <div class="flex flex-col sm:flex-row items-stretch sm:items-center gap-2 sm:gap-3 w-full md:w-auto">
                                <button type="button" onclick="generateCityContextWithAI()" class="w-full sm:w-auto px-4 py-3 md:py-2 bg-blue-600/20 text-blue-400 border border-blue-500/30 rounded-lg text-sm font-bold hover:bg-blue-600 hover:text-white transition-all flex items-center justify-center gap-2">
                                    <i data-lucide="sparkles" class="w-4 h-4"></i> Auto-Write
                                </button>
                                <button type="submit" class="w-full sm:w-auto px-6 py-3 md:py-2 bg-success-green text-black rounded-lg text-sm font-bold shadow-[0_0_15px_rgba(74,222,128,0.3)] hover:bg-white transition-all flex items-center justify-center gap-2">
                                    <i data-lucide="save" class="w-4 h-4"></i> Save Context
                                </button>
                            </div>
                        </div>

                        <div class="p-4 md:p-8">
                            <div class="p-4 bg-yellow-500/10 border border-yellow-500/20 rounded-lg text-xs md:text-sm text-lavender/80 flex gap-3 mb-6">
                                <i data-lucide="alert-triangle" class="w-5 h-5 text-yellow-500 flex-shrink-0"></i>
                                <p><strong>Dynamic Context:</strong> Use <code>{niche}</code> in your text. You can also use Spintax <code>{word1|word2}</code>. It auto-spins for EVERY niche page in this city.</p>
                            </div>

                            <label class="block text-xs uppercase tracking-widest text-lavender/50 mb-2">⚑ Local Context Paragraph (3-4 sentences)</label>
                            <textarea name="city_intro" rows="8" placeholder="e.g. {Having partnered with|Working alongside} {niche}s near..." class="w-full bg-matte-black border border-white/10 rounded-lg p-4 text-base md:text-sm text-white focus:border-blue-500 outline-none transition-colors"><?= esc_textarea($selected_city_intro) ?></textarea>
                        </div>
                    </form>
                </div>
            </div>

            <!-- TAB NICHES (THE 5-THEME AI ENGINE) -->
            <div id="tab-niches" class="tab-content <?= $active_tab == 'niches' ? 'active' : '' ?>">
                
                <div class="flex flex-col sm:flex-row justify-between items-start sm:items-end mb-6 md:mb-8 border-b border-white/5 pb-6 gap-4">
                    <div class="w-full sm:w-auto">
                        <h1 class="font-syne text-2xl md:text-3xl font-bold text-white mb-2">Content Blueprint <span class="text-sharp-purple">v3</span></h1>
                        <p class="text-xs md:text-sm text-lavender/60">Generate and rotate 5 distinct narrative themes per service format.</p>
                    </div>
                </div>

                <div class="bg-card-dark border border-white/5 rounded-xl p-5 mb-8">
                    <h3 class="font-syne text-lg font-bold text-white mb-2">Bulk Add Niches</h3>
                    <p class="text-[10px] uppercase tracking-widest text-lavender/50 mb-4">Paste multiple niches separated by commas.</p>
                    <form method="POST" class="flex flex-col sm:flex-row gap-4 items-end">
                        <input type="hidden" name="action" value="bulk_add_niches">
                        <div class="flex-1 w-full">
                            <input type="text" name="niches_list" placeholder="e.g. Plumber, Electrician, Barber..." class="w-full bg-matte-black border border-white/10 rounded-lg p-3 text-white focus:border-sharp-purple outline-none text-sm" required>
                        </div>
                        <button type="submit" class="w-full sm:w-auto px-6 py-3 bg-sharp-purple text-white rounded-lg text-sm font-bold shadow-[0_0_15px_rgba(126,34,206,0.3)] hover:bg-white hover:text-matte-black transition-all whitespace-nowrap">
                            Add Niches
                        </button>
                    </form>
                </div>

                <div class="flex flex-col sm:flex-row items-center justify-end mb-4 gap-2">
                    <select onchange="window.location.href='?edit_niche='+this.value+'&edit_format=<?= $edit_format_slug ?>'" class="bg-card-dark border border-white/10 text-white px-4 py-3 md:py-2 rounded-lg text-base md:text-sm outline-none focus:border-sharp-purple w-full sm:w-auto max-h-60 overflow-y-auto">
                        <?php foreach($master_niches as $slug => $name): ?>
                            <option value="<?= $slug ?>" <?= $edit_niche_slug === $slug ? 'selected' : '' ?>><?= $name ?></option>
                        <?php endforeach; ?>
                    </select>
                    
                    <select onchange="window.location.href='?edit_niche=<?= $edit_niche_slug ?>&edit_format='+this.value" class="bg-card-dark border border-white/10 text-white px-4 py-3 md:py-2 rounded-lg text-base md:text-sm outline-none focus:border-sharp-purple w-full sm:w-auto max-h-60 overflow-y-auto">
                        <?php foreach($service_formats as $f_slug => $f_name): ?>
                            <option value="<?= $f_slug ?>" <?= $edit_format_slug === $f_slug ? 'selected' : '' ?>><?= $f_name ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <!-- ─── NEW: VISUAL COMPLETION DASHBOARD ─── -->
                <div class="bg-card-dark border border-white/5 rounded-xl p-4 md:p-5 mb-8">
                    <div class="flex items-center justify-between mb-4">
                        <div>
                            <h3 class="font-syne text-sm font-bold text-white">Blueprint Status: <span class="text-sharp-purple"><?= $master_niches[$edit_niche_slug] ?? '' ?></span></h3>
                            <p class="text-[10px] text-lavender/50 mt-1">Shows how many AI themes (out of 5) are saved for each service format.</p>
                        </div>
                    </div>
                    
                    <div class="grid grid-cols-2 md:grid-cols-5 gap-3">
                        <?php foreach ($service_formats as $f_slug => $f_name): 
                            $count = $format_completion_status[$f_slug];
                            $is_current = ($f_slug === $edit_format_slug);
                            
                            // Determine visual state based on completion
                            if ($count >= 5) {
                                $border_color = 'border-success-green/30';
                                $bg_color = 'bg-success-green/10';
                                $text_color = 'text-success-green';
                                $icon = '<i data-lucide="check-circle" class="w-3 h-3 text-success-green"></i>';
                            } elseif ($count > 0) {
                                $border_color = 'border-yellow-500/30';
                                $bg_color = 'bg-yellow-500/10';
                                $text_color = 'text-yellow-500';
                                $icon = '<i data-lucide="alert-circle" class="w-3 h-3 text-yellow-500"></i>';
                            } else {
                                $border_color = 'border-white/5';
                                $bg_color = 'bg-matte-black';
                                $text_color = 'text-lavender/40';
                                $icon = '<i data-lucide="circle" class="w-3 h-3 text-lavender/20"></i>';
                            }
                        ?>
                        <a href="?edit_niche=<?= $edit_niche_slug ?>&edit_format=<?= $f_slug ?>" 
                           class="block p-3 rounded-lg border <?= $border_color ?> <?= $bg_color ?> hover:border-sharp-purple/50 transition-all <?= $is_current ? 'ring-2 ring-sharp-purple ring-offset-2 ring-offset-[#0a0a0a]' : '' ?>">
                            <div class="flex items-center justify-between mb-2">
                                <span class="text-[10px] uppercase tracking-widest <?= $text_color ?> font-bold line-clamp-1"><?= $f_name ?></span>
                                <?= $icon ?>
                            </div>
                            <div class="flex items-end gap-1">
                                <span class="font-syne text-xl font-bold <?= $text_color ?>"><?= $count ?></span>
                                <span class="text-[10px] <?= $text_color ?> opacity-60 mb-1">/ 5</span>
                            </div>
                        </a>
                        <?php endforeach; ?>
                    </div>
                </div>
                <!-- ──────────────────────────────────────── -->

                <div class="bg-card-dark border border-white/5 rounded-xl overflow-visible md:overflow-hidden relative">
                    <form method="POST" id="niche-form" class="m-0 p-0">
                        <input type="hidden" name="action" value="save_niche">
                        <input type="hidden" name="niche_slug" value="<?= esc_attr($edit_niche_slug) ?>">
                        <input type="hidden" name="format_slug" value="<?= esc_attr($edit_format_slug) ?>">
                        <!-- Hidden field to store the JSON string of the 5 themes before saving -->
                        <input type="hidden" name="ai_themes_json" id="ai-themes-json" value='<?= esc_attr($js_themes_data) ?>'>

                        <div id="draft-alert" class="hidden m-4 md:m-6 p-4 bg-yellow-500/10 border border-yellow-500/30 rounded-lg flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
                            <div class="flex items-center gap-3">
                                <i data-lucide="history" class="w-5 h-5 text-yellow-500 flex-shrink-0"></i>
                                <div>
                                    <h4 class="text-sm font-bold text-white">Unsaved Draft Recovered</h4>
                                    <p class="text-[10px] md:text-xs text-lavender/60">We found auto-saved text from your last session.</p>
                                </div>
                            </div>
                            <div class="flex gap-2 w-full sm:w-auto mt-2 sm:mt-0">
                                <button type="button" onclick="discardDraft()" class="px-4 py-2 bg-white/5 hover:bg-white/10 text-white rounded-lg text-xs font-bold transition-colors w-full sm:w-auto">Discard</button>
                                <button type="button" onclick="restoreDraft()" class="px-4 py-2 bg-yellow-500 hover:bg-yellow-400 text-black rounded-lg text-xs font-bold transition-colors w-full sm:w-auto shadow-[0_0_10px_rgba(234,179,8,0.3)]">Restore Draft</button>
                            </div>
                        </div>

                        <div class="p-4 md:p-6 border-b border-white/5 bg-panel-dark/40 flex flex-col md:flex-row justify-between items-start md:items-center gap-4 rounded-t-xl">
                            <div class="flex items-center gap-3 min-w-0 w-full md:w-auto justify-between md:justify-start">
                                <div class="flex items-center gap-2 min-w-0">
                                    <i data-lucide="edit-3" class="w-5 h-5 text-sharp-purple flex-shrink-0"></i>
                                    <h3 class="font-syne text-base md:text-lg font-bold text-white truncate">Editing: <span class="text-sharp-purple" id="current-niche-name"><?= $master_niches[$edit_niche_slug] ?></span> (<span id="current-format-name"><?= $service_formats[$edit_format_slug] ?></span>)</h3>
                                </div>
                                <?php if($existing_niche_post && $existing_niche_post->post_status === 'publish'): ?>
                                    <span class="px-2 py-1 bg-success-green/20 text-success-green text-[10px] uppercase tracking-wider rounded border border-success-green/30 whitespace-nowrap">Active</span>
                                <?php else: ?>
                                    <span class="px-2 py-1 bg-yellow-500/20 text-yellow-500 text-[10px] uppercase tracking-wider rounded border border-yellow-500/30 whitespace-nowrap">Draft</span>
                                <?php endif; ?>
                            </div>
                            <div class="flex flex-col sm:flex-row items-stretch sm:items-center gap-2 sm:gap-3 w-full md:w-auto">
                                <button type="submit" onclick="prepareThemesForSave()" class="w-full sm:w-auto px-8 py-3 bg-sharp-purple text-white rounded-lg text-sm font-bold shadow-[0_0_15px_rgba(126,34,206,0.3)] hover:bg-white hover:text-matte-black transition-all flex items-center justify-center gap-2">
                                    <i data-lucide="save" class="w-4 h-4"></i> Save Entire Blueprint
                                </button>
                            </div>
                        </div>

                        <div class="p-4 md:p-8 space-y-12 pb-16">
                            
                            <div class="p-4 bg-blue-500/10 border border-blue-500/20 rounded-lg text-xs md:text-sm text-lavender/80 flex gap-3">
                                <i data-lucide="info" class="w-5 h-5 text-blue-400 flex-shrink-0"></i>
                                <p><strong>Automated Fields:</strong> The H1 Headline, URL, and general layout elements are generated globally. You only need to fill out the format/niche-specific ⚑ SWAP text below.</p>
                            </div>

                            <!-- AREA 1: ROTATING THEMES (State Machine) -->
                            <div class="border-2 border-sharp-purple/30 bg-sharp-purple/5 rounded-xl p-4 md:p-6 relative">
                                <div class="absolute -top-3 left-4 md:left-6 bg-sharp-purple text-white text-[10px] font-bold px-3 py-1 rounded tracking-widest uppercase z-10 shadow-md">Area 1: Rotating Narrative</div>
                                
                                <p class="text-xs text-lavender/60 mb-4 mt-2">These sections rotate automatically based on the city URL. Write 5 distinct themes so Google doesn't see duplicate copy.</p>
                                
                                <!-- THEME SWITCHER TABS (Expanded to 5) -->
                                <div class="flex gap-2 mb-6 bg-matte-black p-1.5 rounded-lg border border-white/5 inline-flex w-full md:w-auto overflow-x-auto">
                                    <button type="button" onclick="switchThemeTab(0)" id="theme-btn-0" class="px-4 py-2 text-xs font-bold rounded bg-sharp-purple text-white whitespace-nowrap transition-all">Theme 1: Growth</button>
                                    <button type="button" onclick="switchThemeTab(1)" id="theme-btn-1" class="px-4 py-2 text-xs font-bold rounded text-lavender/60 hover:text-white hover:bg-white/5 whitespace-nowrap transition-all">Theme 2: Premium</button>
                                    <button type="button" onclick="switchThemeTab(2)" id="theme-btn-2" class="px-4 py-2 text-xs font-bold rounded text-lavender/60 hover:text-white hover:bg-white/5 whitespace-nowrap transition-all">Theme 3: Dominance</button>
                                    <button type="button" onclick="switchThemeTab(3)" id="theme-btn-3" class="px-4 py-2 text-xs font-bold rounded text-lavender/60 hover:text-white hover:bg-white/5 whitespace-nowrap transition-all">Theme 4: Trust</button>
                                    <button type="button" onclick="switchThemeTab(4)" id="theme-btn-4" class="px-4 py-2 text-xs font-bold rounded text-lavender/60 hover:text-white hover:bg-white/5 whitespace-nowrap transition-all">Theme 5: Convenience</button>
                                </div>

                                <div class="flex justify-end mb-4">
                                    <button type="button" onclick="generateThemeAI()" class="px-5 py-2.5 bg-blue-600/20 text-blue-400 border border-blue-500/30 rounded-lg text-sm font-bold hover:bg-blue-600 hover:text-white transition-all flex items-center gap-2">
                                        <i data-lucide="sparkles" class="w-4 h-4"></i> Auto-Write This Theme
                                    </button>
                                </div>

                                <div class="space-y-6">
                                    <div>
                                        <label class="block text-[10px] md:text-xs uppercase tracking-widest text-sharp-purple mb-2">⚑ Hero Sub-Headline (1-2 sentences)</label>
                                        <p class="text-xs text-lavender/40 mb-3">Speak directly to the pain point or missed opportunity of this specific niche without this service.</p>
                                        <textarea id="theme_hero_subheadline" rows="3" class="w-full bg-matte-black border border-white/10 rounded-lg p-3 text-base md:text-sm text-white focus:border-sharp-purple outline-none transition-colors"></textarea>
                                    </div>
                                    <div class="grid grid-cols-1 xl:grid-cols-2 gap-8">
                                        <div class="space-y-4">
                                            <h5 class="text-sm font-bold text-white mb-2">The Reality (Left Column)</h5>
                                            <?php 
                                            $p_hints = [
                                                1 => 'How do people in this niche get found?', 
                                                2 => 'What happens without a good website? (Consequences)', 
                                                3 => 'What does a great website unlock for this niche?', 
                                                4 => 'Why now? (Urgency / Market context)'
                                            ];
                                            for($i=1; $i<=4; $i++): ?>
                                            <div>
                                                <label class="block text-[10px] uppercase tracking-widest text-lavender/50 mb-1">⚑ SWAP: Paragraph <?= $i ?></label>
                                                <textarea id="theme_reality_p<?= $i ?>" rows="3" placeholder="<?= $p_hints[$i] ?>" class="w-full bg-matte-black border border-white/10 rounded-lg p-3 text-base md:text-sm text-white focus:border-sharp-purple outline-none"></textarea>
                                            </div>
                                            <?php endfor; ?>
                                        </div>
                                        <div>
                                            <h5 class="text-sm font-bold text-white mb-2">Outcome Cards (Right Column)</h5>
                                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                                <?php for($i=1; $i<=4; $i++): ?>
                                                <div class="bg-matte-black p-3 rounded-lg border border-white/5">
                                                    <div class="text-[10px] uppercase tracking-widest text-lavender/40 font-bold mb-2">Card 0<?= $i ?></div>
                                                    <input type="text" id="theme_pos<?= $i ?>_title" placeholder="Title" class="w-full bg-card-dark border border-white/10 rounded p-2 text-base md:text-sm text-white mb-2 focus:border-sharp-purple outline-none">
                                                    <textarea id="theme_pos<?= $i ?>_desc" rows="2" placeholder="Desc" class="w-full bg-card-dark border border-white/10 rounded p-2 text-base md:text-sm text-white focus:border-sharp-purple outline-none"></textarea>
                                                </div>
                                                <?php endfor; ?>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- AREA 2: STATIC CORE -->
                            <div class="bg-matte-black p-4 md:p-6 rounded-xl border border-white/5 relative">
                                <div class="absolute -top-3 left-4 md:left-6 bg-white/10 text-lavender text-[10px] font-bold px-3 py-1 rounded tracking-widest uppercase z-10 shadow-md">Area 2: Static Core Infrastructure</div>
                                
                                <div class="flex justify-between items-center mb-6 mt-2">
                                    <p class="text-xs text-lavender/50">These sections remain static across all page variations for this niche format.</p>
                                    <button type="button" onclick="generateCoreAI()" class="px-4 py-2 bg-blue-600/10 text-blue-400 border border-blue-500/20 rounded-lg text-xs font-bold hover:bg-blue-600 hover:text-white transition-all flex items-center gap-1.5">
                                        <i data-lucide="sparkles" class="w-3.5 h-3.5"></i> Auto-Write Core
                                    </button>
                                </div>

                                <!-- Features -->
                                <div class="space-y-4 border-b border-white/5 pb-8 mb-8">
                                    <h5 class="text-sm font-bold text-white">Features Section</h5>
                                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                        <div>
                                            <label class="block text-[10px] uppercase tracking-widest text-lavender/50 mb-2">⚑ SWAP: Section Headline</label>
                                            <input type="text" name="feat_headline" value="<?= esc_attr($niche_core_meta['feat_headline'] ?? '') ?>" placeholder="Headline" class="w-full bg-card-dark border border-white/10 rounded-lg p-3 text-sm text-white focus:border-sharp-purple outline-none">
                                        </div>
                                        <div>
                                            <label class="block text-[10px] uppercase tracking-widest text-lavender/50 mb-2">⚑ SWAP: Section Sub-line</label>
                                            <input type="text" name="feat_subline" value="<?= esc_attr($niche_core_meta['feat_subline'] ?? '') ?>" placeholder="Sub-headline" class="w-full bg-card-dark border border-white/10 rounded-lg p-3 text-sm text-white focus:border-sharp-purple outline-none">
                                        </div>
                                    </div>
                                    
                                    <h5 class="text-sm font-bold text-white mb-4 mt-6">6 Feature Cards (Title + Description)</h5>
                                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
                                        <?php 
                                        $f_hints = [
                                            1 => ['Niche-Specific Pages', 'e.g. Dedicated pages for each specialty...'],
                                            2 => ['Trust Element', 'e.g. Attorney profiles with bios...'],
                                            3 => ['Lead Capture / Enquiry', 'e.g. Consultation booking form...'],
                                            4 => ['Social Proof', 'e.g. Case results + testimonials...'],
                                            5 => ['SEO Architecture', 'e.g. Set up to appear for Lagos searches...'],
                                            6 => ['Mobile-First Design', 'e.g. Perfect experience on phones...']
                                        ];
                                        for($i=1; $i<=6; $i++): ?>
                                        <div class="bg-card-dark p-3 rounded-lg border border-white/5">
                                            <div class="w-6 h-6 rounded bg-sharp-purple/20 text-sharp-purple flex items-center justify-center mb-3 text-[10px] font-bold">F<?= $i ?></div>
                                            <input type="text" name="f<?= $i ?>_title" value="<?= esc_attr($niche_core_meta['f'.$i.'_title'] ?? '') ?>" placeholder="<?= $f_hints[$i][0] ?>" class="w-full bg-matte-black border border-white/10 rounded p-2 text-sm text-white mb-2 focus:border-sharp-purple outline-none">
                                            <textarea name="f<?= $i ?>_desc" rows="2" placeholder="<?= $f_hints[$i][1] ?>" class="w-full bg-matte-black border border-white/10 rounded p-2 text-sm text-white focus:border-sharp-purple outline-none"><?= esc_textarea($niche_core_meta['f'.$i.'_desc'] ?? '') ?></textarea>
                                        </div>
                                        <?php endfor; ?>
                                    </div>
                                </div>

                                <!-- FAQs -->
                                <div class="space-y-4 border-b border-white/5 pb-8 mb-8">
                                    <h5 class="text-sm font-bold text-white flex items-center justify-between">FAQs (Generates Schema)</h5>
                                    <p class="mt-2 text-xs text-lavender/40 mb-5">Write 5 FAQs specific to this niche. The section headline is auto-generated.</p>
                                    <?php 
                                    $faq_hints = [1 => 'Cost', 2 => 'Timeline', 3 => 'SEO / Ranking', 4 => 'Objection', 5 => 'Redesign / Specific'];
                                    for($f=1; $f<=5; $f++): ?>
                                    <div class="p-3 border border-white/5 rounded-lg bg-card-dark grid grid-cols-1 lg:grid-cols-3 gap-3">
                                        <div class="lg:col-span-1">
                                            <div class="text-[10px] uppercase tracking-widest text-lavender/50 mb-2">FAQ #<?= $f ?> (<?= $faq_hints[$f] ?>)</div>
                                            <input type="text" name="faq<?= $f ?>_q" value="<?= esc_attr($niche_core_meta["faq{$f}_q"] ?? '') ?>" placeholder="Question <?= $f ?>?" class="w-full bg-matte-black border border-white/10 rounded p-2 text-sm text-white outline-none">
                                        </div>
                                        <div class="lg:col-span-2">
                                            <div class="text-[10px] uppercase tracking-widest text-lavender/50 mb-2 invisible hidden lg:block">Answer</div>
                                            <textarea name="faq<?= $f ?>_a" rows="2" placeholder="Answer..." class="w-full bg-matte-black border border-white/10 rounded p-2 text-sm text-white outline-none"><?= esc_textarea($niche_core_meta["faq{$f}_a"] ?? '') ?></textarea>
                                        </div>
                                    </div>
                                    <?php endfor; ?>
                                </div>

                                <!-- CTA -->
                                <div>
                                    <h5 class="text-sm font-bold text-white mb-2">Final CTA Aspiration</h5>
                                    <label class="block text-[10px] md:text-xs uppercase tracking-widest text-lavender/50 mb-2">⚑ SWAP: CTA Headline Line 1 (Aspiration)</label>
                                    <p class="text-xs text-lavender/40 mb-3">Line 2 is automatically set to "Let's make it happen."</p>
                                    <input type="text" name="cta_aspiration" value="<?= esc_attr($niche_core_meta['cta_aspiration'] ?? '') ?>" placeholder="e.g. Your law firm deserves to be found." class="w-full bg-card-dark border border-white/10 rounded-lg p-3 text-sm text-white focus:border-sharp-purple outline-none">
                                </div>

                            </div>

                        </div>
                    </form>
                </div>
            </div>

            <div id="tab-seo" class="tab-content <?= $active_tab == 'seo' ? 'active' : '' ?>">
                 <div class="flex flex-col sm:flex-row justify-between items-start sm:items-end mb-6 md:mb-8 border-b border-white/5 pb-6 gap-4">
                    <div>
                        <h1 class="font-syne text-2xl md:text-3xl font-bold text-white mb-2">SEO Formula Engine</h1>
                        <p class="text-xs md:text-sm text-lavender/60">Define global variables. Saves to WP Options table.</p>
                    </div>
                </div>

                <form method="POST" class="bg-card-dark border border-white/5 rounded-xl p-5 md:p-8">
                    <input type="hidden" name="action" value="save_seo">

                    <div class="mb-8 p-4 bg-blue-500/10 border border-blue-500/20 rounded-lg flex gap-3">
                        <i data-lucide="info" class="w-5 h-5 text-blue-400 flex-shrink-0 mt-0.5"></i>
                        <p class="text-xs md:text-sm text-lavender/80 leading-relaxed">Use <code class="text-blue-400 bg-black px-1 rounded">{niche}</code> and <code class="text-blue-400 bg-black px-1 rounded">{city}</code> to inject dynamic values.</p>
                    </div>

                    <div class="space-y-6">
                        <div>
                            <label class="block text-[10px] md:text-xs uppercase tracking-widest text-lavender/50 mb-2">Global Meta Title Formula</label>
                            <input type="text" name="meta_title" value="<?= esc_attr($settings['meta_title']) ?>" class="w-full bg-[#111111] border border-white/10 rounded-lg p-3 text-white focus:border-sharp-purple outline-none font-mono text-base md:text-sm">
                        </div>
                        <div>
                            <label class="block text-[10px] md:text-xs uppercase tracking-widest text-lavender/50 mb-2">Global Meta Description Formula</label>
                            <textarea name="meta_desc" rows="4" class="w-full bg-[#111111] border border-white/10 rounded-lg p-3 text-white focus:border-sharp-purple outline-none font-mono text-base md:text-sm"><?= esc_textarea($settings['meta_desc']) ?></textarea>
                        </div>
                        <div>
                            <label class="block text-[10px] md:text-xs uppercase tracking-widest text-lavender/50 mb-2">Global Open Graph Image URL</label>
                            <input type="url" name="og_image" value="<?= esc_attr($settings['og_image']) ?>" class="w-full bg-[#111111] border border-white/10 rounded-lg p-3 text-white focus:border-sharp-purple outline-none font-mono text-base md:text-sm">
                        </div>
                        
                        <div class="pt-4 border-t border-white/5">
                            <button type="submit" class="w-full sm:w-auto px-8 py-4 md:py-3 bg-white text-matte-black rounded-lg text-sm font-bold hover:bg-sharp-purple hover:text-white transition-all shadow-lg shadow-white/5 hover:shadow-sharp-purple/30">Save Configuration</button>
                        </div>
                    </div>
                </form>
            </div>

            <div id="tab-listicles" class="tab-content <?= $active_tab == 'listicles' ? 'active' : '' ?>">

                <div class="flex flex-col xl:flex-row justify-between items-start xl:items-end mb-6 md:mb-8 border-b border-white/5 pb-6 gap-4">
                    <div class="w-full xl:w-auto">
                        <h1 class="font-syne text-2xl md:text-3xl font-bold text-white mb-1">Surround Sound AI</h1>
                        <p class="text-xs md:text-sm text-lavender/60">Generate 1,000-word aggregator listicles to dominate SERPs.</p>
                    </div>

                    <div class="flex flex-wrap items-center gap-2 bg-card-dark border border-white/10 p-1 rounded-lg w-full xl:w-auto">
                        <button onclick="setListicleView('editor')" id="lstbtn-editor"
                            class="relative px-3 sm:px-4 py-2 rounded-md text-xs font-bold transition-all bg-sharp-purple text-white whitespace-nowrap">
                            Editor
                        </button>
                        <button onclick="setListicleView('coverage')" id="lstbtn-coverage"
                            class="relative px-3 sm:px-4 py-2 rounded-md text-xs font-bold transition-all text-lavender/60 hover:text-white whitespace-nowrap">
                            Coverage Grid
                        </button>
                        <button onclick="setListicleView('queue')" id="lstbtn-queue"
                            class="relative px-3 sm:px-4 py-2 rounded-md text-xs font-bold transition-all text-lavender/60 hover:text-white whitespace-nowrap">
                            Publish Queue
                            <?php if ($queue_total > 0): ?>
                            <span class="absolute -top-1 -right-1 w-4 h-4 bg-sharp-purple rounded-full text-[9px] flex items-center justify-center font-bold text-white">
                                <?= $queue_total > 99 ? '99+' : $queue_total ?>
                            </span>
                            <?php endif; ?>
                        </button>
                        <button onclick="setListicleView('database')" id="lstbtn-database"
                            class="relative px-3 sm:px-4 py-2 rounded-md text-xs font-bold transition-all text-lavender/60 hover:text-white flex items-center gap-1.5 whitespace-nowrap">
                            <i data-lucide="database" class="w-3.5 h-3.5"></i> Database
                        </button>
                    </div>
                </div>

                <div id="lst-view-editor">

                    <div class="flex gap-2 w-full mb-6">
                        <select id="lst-city-select" onchange="loadListicleAjax()"
                            class="bg-card-dark border border-white/10 text-white px-3 py-2 rounded-lg text-sm outline-none focus:border-sharp-purple w-full sm:w-auto">
                            <?php foreach ($locations as $loc): ?>
                                <option value="<?= esc_attr($loc->post_name) ?>"
                                    <?= $edit_listicle_city === $loc->post_name ? 'selected' : '' ?>>
                                    <?= esc_html($loc->post_title) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                        <select id="lst-niche-select" onchange="loadListicleAjax()"
                            class="bg-card-dark border border-white/10 text-white px-3 py-2 rounded-lg text-sm outline-none focus:border-sharp-purple w-full sm:w-auto">
                            <?php foreach ($master_niches as $slug => $name): ?>
                                <option value="<?= esc_attr($slug) ?>"
                                    <?= $edit_listicle_niche === $slug ? 'selected' : '' ?>>
                                    <?= esc_html($name) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                        <div id="lst-ajax-spinner" class="hidden items-center justify-center px-3">
                            <div class="w-4 h-4 rounded-full border-2 border-sharp-purple border-t-transparent animate-spin"></div>
                        </div>
                    </div>

                    <div class="bg-card-dark border border-white/5 rounded-xl overflow-hidden relative">
                        <form method="POST" id="listicle-form" class="m-0 p-0">
                            <input type="hidden" name="action" value="save_listicle">
                            <input type="hidden" id="lst-hidden-city" name="listicle_city" value="<?= esc_attr($edit_listicle_city) ?>">
                            <input type="hidden" id="lst-hidden-niche" name="listicle_niche" value="<?= esc_attr($edit_listicle_niche) ?>">

                            <div class="p-4 md:p-6 border-b border-white/5 bg-panel-dark/40 flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
                                <div class="flex items-center gap-3 min-w-0">
                                    <i data-lucide="file-text" class="w-5 h-5 text-sharp-purple flex-shrink-0"></i>
                                    <h3 class="font-syne text-base md:text-lg font-bold text-white truncate">
                                        Top
                                        <span class="text-sharp-purple" id="listicle-niche-name">
                                            <?= $master_niches[$edit_listicle_niche] ?? '' ?>
                                        </span>
                                        in
                                        <span class="text-sharp-purple" id="listicle-city-name">
                                            <?= ucwords(str_replace('-', ' ', $edit_listicle_city)) ?>
                                        </span>
                                    </h3>
                                    <span id="lst-status-badge"
                                        class="<?= ($existing_listicle['status'] === 'publish') ? 'bg-success-green/20 text-success-green border-success-green/30' : 'bg-yellow-500/20 text-yellow-500 border-yellow-500/30' ?> text-[10px] px-2 py-1 rounded border uppercase tracking-widest whitespace-nowrap font-bold">
                                        <?= ($existing_listicle['status'] === 'publish') ? 'Published' : 'Draft' ?>
                                    </span>
                                </div>
                                <div class="flex flex-col sm:flex-row items-stretch sm:items-center gap-2 sm:gap-3 w-full md:w-auto">
                                    <select name="status" id="lst-status-select"
                                        class="bg-matte-black border border-white/10 text-white px-3 py-2 rounded-lg text-sm outline-none focus:border-sharp-purple">
                                        <option value="publish" <?= $existing_listicle['status'] === 'publish' ? 'selected' : '' ?>>Published</option>
                                        <option value="draft"   <?= $existing_listicle['status'] === 'draft'   ? 'selected' : '' ?>>Draft</option>
                                    </select>
                                    <button type="button" onclick="generateListicleWithAI()"
                                        class="w-full sm:w-auto px-4 py-2 bg-blue-600/20 text-blue-400 border border-blue-500/30 rounded-lg text-sm font-bold hover:bg-blue-600 hover:text-white transition-all flex items-center justify-center gap-2">
                                        <i data-lucide="sparkles" class="w-4 h-4"></i> AI Generate
                                    </button>
                                    <button type="submit"
                                        class="w-full sm:w-auto px-6 py-2 bg-success-green text-black rounded-lg text-sm font-bold shadow-[0_0_15px_rgba(74,222,128,0.3)] hover:bg-white transition-all flex items-center justify-center gap-2">
                                        <i data-lucide="save" class="w-4 h-4"></i> Save
                                    </button>
                                </div>
                            </div>

                            <div class="p-4 md:p-8 space-y-6">
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                    <div>
                                        <label class="block text-[10px] md:text-xs uppercase tracking-widest text-lavender/50 mb-2">Target Keyword</label>
                                        <input type="text" name="target_keyword" id="lst-target-keyword"
                                            value="<?= esc_attr($existing_listicle['target_keyword']) ?>"
                                            placeholder="e.g. Top Web Design Agencies in Lagos"
                                            class="w-full bg-matte-black border border-white/10 rounded-lg p-3 text-white focus:border-sharp-purple outline-none text-sm" required>
                                    </div>
                                    <div>
                                        <label class="block text-[10px] md:text-xs uppercase tracking-widest text-lavender/50 mb-2">Meta Title</label>
                                        <input type="text" name="meta_title" id="lst-meta-title"
                                            value="<?= esc_attr($existing_listicle['meta_title']) ?>"
                                            placeholder="e.g. 5 Best Law Firm Web Designers in Abuja (<?= date('Y') ?>)"
                                            class="w-full bg-matte-black border border-white/10 rounded-lg p-3 text-white focus:border-sharp-purple outline-none text-sm" required>
                                    </div>
                                </div>

                                <div>
                                    <div class="flex items-center justify-between mb-2">
                                        <label class="block text-[10px] md:text-xs uppercase tracking-widest text-lavender/50">
                                            Article Content (Raw HTML)
                                        </label>
                                        <div class="flex items-center gap-3">
                                            <span id="lst-word-count-badge"
                                                class="text-[10px] font-mono font-bold px-2 py-1 rounded border transition-all
                                                       <?= str_word_count(strip_tags($existing_listicle['content'] ?? '')) >= 1000
                                                            ? 'text-success-green bg-success-green/10 border-success-green/30'
                                                            : 'text-yellow-400 bg-yellow-500/10 border-yellow-500/30' ?>">
                                                <?= number_format(str_word_count(strip_tags($existing_listicle['content'] ?? ''))) ?> words
                                            </span>
                                            <span class="text-[10px] text-lavender/30 font-mono">target: 1,000+</span>
                                        </div>
                                    </div>

                                    <div class="flex items-center gap-2 mb-3">
                                        <button type="button" onclick="setContentView('source')" id="cv-btn-source"
                                            class="px-3 py-1.5 text-[10px] font-bold rounded border bg-sharp-purple/20 text-sharp-purple border-sharp-purple/30 transition-all">
                                            HTML Source
                                        </button>
                                        <button type="button" onclick="setContentView('preview')" id="cv-btn-preview"
                                            class="px-3 py-1.5 text-[10px] font-bold rounded border bg-white/5 text-lavender/50 border-white/10 hover:text-white transition-all">
                                            Rendered Preview
                                        </button>
                                    </div>

                                    <div id="lst-source-view">
                                        <textarea name="content" id="lst-content-textarea" rows="20"
                                            oninput="updateWordCount(this)"
                                            class="w-full bg-[#0a0a0a] border border-white/10 rounded-lg p-4 text-white font-mono text-xs md:text-sm focus:border-sharp-purple outline-none leading-relaxed"><?= esc_textarea($existing_listicle['content']) ?></textarea>
                                    </div>

                                    <div id="lst-preview-view" class="hidden">
                                        <div id="lst-preview-frame"
                                            class="w-full min-h-64 bg-white text-[#111] rounded-lg p-6 md:p-8 prose prose-sm max-w-none overflow-auto text-base leading-relaxed">
                                            <?= $existing_listicle['content'] ?>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>

                <div id="lst-view-coverage" class="hidden">

                    <div class="mb-5 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                        <p class="text-sm text-lavender/60">
                            <?= count($all_listicles_detailed ?? []) ?> listicles saved ·
                            <span class="text-success-green"><?= $total_published_listicles ?> published</span> ·
                            <span class="text-yellow-400"><?= $total_draft_listicles ?> draft</span> ·
                            <span class="text-lavender/40"><?= $queue_total ?> empty</span>
                        </p>
                        <div class="flex flex-wrap gap-3 text-[10px] font-mono uppercase tracking-widest bg-card-dark p-3 rounded-lg border border-white/5">
                            <div class="flex items-center gap-2"><div class="w-3 h-3 bg-success-green rounded-sm shadow-[0_0_8px_rgba(74,222,128,0.5)]"></div> Published</div>
                            <div class="flex items-center gap-2"><div class="w-3 h-3 bg-yellow-500 rounded-sm"></div> Draft</div>
                            <div class="flex items-center gap-2"><div class="w-3 h-3 bg-white/5 border border-white/10 rounded-sm"></div> Empty</div>
                        </div>
                    </div>

                    <div class="md:hidden flex items-center justify-end gap-2 text-[10px] text-lavender/50 mb-3 font-mono uppercase tracking-widest">
                        <i data-lucide="move-horizontal" class="w-4 h-4 text-sharp-purple animate-pulse"></i> Swipe to view all cities
                    </div>

                    <div class="bg-card-dark border border-white/5 rounded-xl overflow-x-auto pb-4 w-full">
                        <table class="w-full text-left border-collapse min-w-[900px]">
                            <thead>
                                <tr>
                                    <th class="p-4 border-b border-white/10 text-xs font-syne text-lavender/50 uppercase sticky left-0 bg-card-dark z-20 w-44 shadow-[4px_0_10px_rgba(0,0,0,0.5)]">
                                        Niche
                                    </th>
                                    <?php foreach ($locations as $loc): ?>
                                    <th class="p-2 border-b border-white/10 align-bottom">
                                        <div class="w-8 mx-auto -rotate-45 transform origin-bottom-left whitespace-nowrap text-[10px] font-mono text-lavender/50 pb-2">
                                            <?= esc_html($loc->post_title) ?>
                                        </div>
                                    </th>
                                    <?php endforeach; ?>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($master_niches as $n_slug => $n_name): ?>
                                <tr class="hover:bg-white/5 transition-colors border-b border-white/5">
                                    <td class="p-4 text-xs text-white font-semibold sticky left-0 bg-card-dark z-10 shadow-[4px_0_10px_rgba(0,0,0,0.5)] whitespace-nowrap">
                                        <?= esc_html($n_name) ?>
                                    </td>
                                    <?php foreach ($locations as $loc):
                                        $lst_st = $listicle_map[$loc->post_name][$n_slug] ?? 'empty';
                                        if ($lst_st === 'publish') {
                                            $dot_cls = 'bg-success-green shadow-[0_0_5px_rgba(74,222,128,0.5)]';
                                            $title   = 'Published';
                                        } elseif ($lst_st === 'draft') {
                                            $dot_cls = 'bg-yellow-500';
                                            $title   = 'Draft';
                                        } else {
                                            $dot_cls = 'bg-white/5 border border-white/10';
                                            $title   = 'Empty — click to create';
                                        }
                                    ?>
                                    <td class="p-2 text-center">
                                        <button type="button"
                                            title="<?= $title ?>"
                                            onclick="jumpToEditor('<?= esc_js($loc->post_name) ?>','<?= esc_js($n_slug) ?>')"
                                            class="block w-5 h-5 mx-auto rounded-sm <?= $dot_cls ?> hover:scale-125 transition-transform cursor-pointer">
                                        </button>
                                    </td>
                                    <?php endforeach; ?>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>

                <div id="lst-view-queue" class="hidden">

                    <div class="flex items-center justify-between mb-6 flex-wrap gap-4">
                        <div>
                            <p class="text-sm text-lavender/60">
                                <span class="text-white font-bold"><?= $queue_total ?></span> combinations still need a listicle.
                                Showing the first <span class="text-white font-bold"><?= count($publish_queue_display) ?></span> below.
                            </p>
                            <p class="text-xs text-lavender/40 mt-1">Only active cities are included. Inactive cities are excluded.</p>
                        </div>
                        <label class="flex items-center gap-2 cursor-pointer select-none">
                            <span class="text-xs text-lavender/60 font-medium">Auto-advance after save</span>
                            <div class="relative">
                                <input type="checkbox" id="auto-advance-toggle" class="sr-only" checked>
                                <div class="w-9 h-5 bg-white/10 rounded-full peer-checked:bg-sharp-purple transition-colors" id="auto-advance-track"></div>
                                <div class="absolute top-0.5 left-0.5 w-4 h-4 bg-white rounded-full transition-transform" id="auto-advance-thumb"></div>
                            </div>
                        </label>
                    </div>

                    <?php if (empty($publish_queue_display)): ?>
                    <div class="p-12 border border-success-green/20 bg-success-green/5 rounded-xl text-center">
                        <i data-lucide="check-circle" class="w-10 h-10 text-success-green mx-auto mb-3"></i>
                        <h3 class="font-syne text-lg font-bold text-white mb-2">All caught up!</h3>
                        <p class="text-sm text-lavender/60">Every active city × niche combination has a listicle. Nice work.</p>
                    </div>
                    <?php else: ?>

                    <div class="space-y-3" id="queue-list">
                        <?php foreach ($publish_queue_display as $i => $item): ?>
                        <div class="queue-item bg-card-dark border border-white/5 rounded-xl p-4 flex items-center justify-between gap-4 hover:border-sharp-purple/20 transition-all"
                            data-city-slug="<?= esc_attr($item['city_slug']) ?>"
                            data-niche-slug="<?= esc_attr($item['niche_slug']) ?>"
                            data-city-name="<?= esc_attr($item['city_name']) ?>"
                            data-niche-name="<?= esc_attr($item['niche_name']) ?>"
                            id="queue-item-<?= $i ?>">
                            <div class="flex items-center gap-4 min-w-0">
                                <div class="w-8 h-8 rounded-lg bg-white/5 flex items-center justify-center text-[10px] font-bold text-lavender/40 font-mono flex-shrink-0">
                                    <?= str_pad($i + 1, 2, '0', STR_PAD_LEFT) ?>
                                </div>
                                <div class="min-w-0">
                                    <div class="flex items-center gap-2 flex-wrap">
                                        <span class="text-sm font-bold text-white"><?= esc_html($item['niche_name']) ?></span>
                                        <span class="text-lavender/30">×</span>
                                        <span class="text-sm text-lavender/70"><?= esc_html($item['city_name']) ?></span>
                                    </div>
                                    <div class="text-[10px] text-lavender/30 font-mono mt-0.5 truncate">
                                        Top <?= esc_html($item['niche_name']) ?> Web Designers in <?= esc_html($item['city_name']) ?>
                                    </div>
                                </div>
                            </div>
                            <div class="flex items-center gap-2 flex-shrink-0">
                                <button type="button"
                                    onclick="loadQueueItem(<?= $i ?>)"
                                    class="generate-queue-btn px-4 py-2 bg-sharp-purple/20 text-sharp-purple border border-sharp-purple/30 rounded-lg text-xs font-bold hover:bg-sharp-purple hover:text-white transition-all flex items-center gap-2">
                                    <i data-lucide="sparkles" class="w-3.5 h-3.5"></i>
                                    Generate
                                </button>
                                <button type="button"
                                    onclick="jumpToEditor('<?= esc_js($item['city_slug']) ?>','<?= esc_js($item['niche_slug']) ?>')"
                                    class="px-3 py-2 bg-white/5 text-lavender/60 border border-white/10 rounded-lg text-xs font-bold hover:text-white transition-all">
                                    Open
                                </button>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    </div>

                    <?php if ($queue_total > 20): ?>
                    <p class="text-center text-xs text-lavender/40 mt-6">
                        + <?= number_format($queue_total - 20) ?> more combinations not shown.
                        Switch to Coverage Grid for the full picture.
                    </p>
                    <?php endif; ?>

                    <?php endif; ?>
                </div>

                <div id="lst-view-database" class="hidden">
                    
                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 mb-6">
                        <div class="bg-success-green/10 border border-success-green/20 rounded-xl p-5">
                            <p class="text-xs text-success-green/70 mb-1 font-bold uppercase tracking-widest">Published Listicles</p>
                            <h3 class="font-syne text-3xl font-bold text-success-green"><?= number_format($total_published_listicles) ?></h3>
                        </div>
                        <div class="bg-yellow-500/10 border border-yellow-500/20 rounded-xl p-5">
                            <p class="text-xs text-yellow-500/70 mb-1 font-bold uppercase tracking-widest">Draft Listicles</p>
                            <h3 class="font-syne text-3xl font-bold text-yellow-500"><?= number_format($total_draft_listicles) ?></h3>
                        </div>
                        <div class="bg-sharp-purple/10 border border-sharp-purple/20 rounded-xl p-5">
                            <p class="text-xs text-sharp-purple/70 mb-1 font-bold uppercase tracking-widest">Total Aggregator Pages</p>
                            <h3 class="font-syne text-3xl font-bold text-sharp-purple"><?= number_format($total_published_listicles + $total_draft_listicles) ?></h3>
                        </div>
                    </div>

                    <div class="bg-card-dark border border-white/5 rounded-xl overflow-hidden flex flex-col">
                        <div class="p-4 border-b border-white/5 flex items-center justify-between gap-4 flex-wrap bg-panel-dark/40">
                            <div class="relative w-full md:w-96">
                                <i data-lucide="search" class="w-4 h-4 text-lavender/40 absolute left-3 top-1/2 -translate-y-1/2"></i>
                                <input type="text" id="db-search" onkeyup="filterDatabase()" placeholder="Search by city, niche, or keyword..." class="w-full bg-matte-black border border-white/10 rounded-lg pl-9 pr-4 py-2 text-sm text-white focus:border-sharp-purple outline-none transition-colors">
                            </div>
                        </div>

                        <div class="overflow-x-auto w-full">
                            <table class="w-full text-left border-collapse min-w-[1000px]">
                                <thead>
                                    <tr class="bg-matte-black/50">
                                        <th class="p-4 border-b border-white/5 text-xs font-syne text-lavender/50 uppercase tracking-widest font-bold">Content Page</th>
                                        <th class="p-4 border-b border-white/5 text-xs font-syne text-lavender/50 uppercase tracking-widest font-bold">Target SEO Keyword</th>
                                        <th class="p-4 border-b border-white/5 text-xs font-syne text-lavender/50 uppercase tracking-widest font-bold text-center">Status</th>
                                        <th class="p-4 border-b border-white/5 text-xs font-syne text-lavender/50 uppercase tracking-widest font-bold text-center">Last Updated</th>
                                        <th class="p-4 border-b border-white/5 text-xs font-syne text-lavender/50 uppercase tracking-widest font-bold text-right">Actions</th>
                                    </tr>
                                </thead>
                                <tbody id="db-table-body">
                                    <?php if (empty($all_listicles_detailed)): ?>
                                    <tr>
                                        <td colspan="5" class="p-8 text-center text-lavender/40 text-sm">No articles generated yet. Head to the Queue to get started.</td>
                                    </tr>
                                    <?php else: ?>
                                        <?php foreach ($all_listicles_detailed as $art): 
                                            $live_url = "https://getonlinestudio.com/locations/{$art['city_slug']}/top-{$art['niche_slug']}-web-designers/";
                                            $is_pub = $art['status'] === 'publish';
                                            $time_ago = human_time_diff(strtotime($art['updated_at']), current_time('timestamp')) . ' ago';
                                        ?>
                                        <tr class="db-row hover:bg-white/5 transition-colors border-b border-white/5 group">
                                            <td class="p-4">
                                                <div class="flex items-center gap-3">
                                                    <div class="w-8 h-8 rounded bg-white/5 flex items-center justify-center flex-shrink-0">
                                                        <i data-lucide="file-text" class="w-4 h-4 <?= $is_pub ? 'text-success-green' : 'text-yellow-500' ?>"></i>
                                                    </div>
                                                    <div>
                                                        <div class="text-sm font-bold text-white db-search-text"><?= ucwords(str_replace('-', ' ', $art['niche_slug'])) ?></div>
                                                        <div class="text-[10px] text-lavender/50 font-mono mt-0.5 db-search-text">in <?= ucwords(str_replace('-', ' ', $art['city_slug'])) ?></div>
                                                    </div>
                                                </div>
                                            </td>
                                            <td class="p-4">
                                                <div class="text-sm text-lavender/80 db-search-text"><?= esc_html($art['target_keyword']) ?></div>
                                            </td>
                                            <td class="p-4 text-center">
                                                <span class="inline-flex items-center justify-center px-2 py-1 rounded border text-[10px] uppercase tracking-widest font-bold <?= $is_pub ? 'bg-success-green/10 text-success-green border-success-green/20' : 'bg-yellow-500/10 text-yellow-500 border-yellow-500/20' ?>">
                                                    <?= $is_pub ? 'Published' : 'Draft' ?>
                                                </span>
                                            </td>
                                            <td class="p-4 text-center">
                                                <span class="text-xs font-mono text-lavender/50" title="<?= esc_attr($art['updated_at']) ?>">
                                                    <?= $time_ago ?>
                                                </span>
                                            </td>
                                            <td class="p-4 text-right">
                                                <div class="flex justify-end gap-2 opacity-100 sm:opacity-0 sm:group-hover:opacity-100 transition-opacity">
                                                    <form method="POST" class="inline" onsubmit="return confirm('Force ping this URL to Google and IndexNow?');">
                                                        <input type="hidden" name="action" value="ping_indexer">
                                                        <input type="hidden" name="city_slug" value="<?= esc_attr($art['city_slug']) ?>">
                                                        <input type="hidden" name="niche_slug" value="<?= esc_attr($art['niche_slug']) ?>">
                                                        <button type="submit" title="Force Index Ping" class="w-8 h-8 rounded border border-blue-500/30 bg-blue-500/10 text-blue-400 hover:bg-blue-500 hover:text-white transition-colors flex items-center justify-center">
                                                            <i data-lucide="rss" class="w-3.5 h-3.5"></i>
                                                        </button>
                                                    </form>
                                                    <button onclick="jumpToEditor('<?= esc_js($art['city_slug']) ?>','<?= esc_js($art['niche_slug']) ?>')" title="Edit Content" class="w-8 h-8 rounded border border-sharp-purple/30 bg-sharp-purple/10 text-sharp-purple hover:bg-sharp-purple hover:text-white transition-colors flex items-center justify-center">
                                                        <i data-lucide="edit-2" class="w-3.5 h-3.5"></i>
                                                    </button>
                                                    <?php if($is_pub): ?>
                                                    <a href="<?= esc_url($live_url) ?>" target="_blank" title="View Live Page" class="w-8 h-8 rounded border border-white/10 bg-white/5 text-lavender/70 hover:bg-white hover:text-matte-black transition-colors flex items-center justify-center">
                                                        <i data-lucide="external-link" class="w-3.5 h-3.5"></i>
                                                    </a>
                                                    <?php endif; ?>
                                                </div>
                                            </td>
                                        </tr>
                                        <?php endforeach; ?>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

            </div>

            <div id="tab-social" class="tab-content <?= $active_tab == 'social' ? 'active' : '' ?>">

                <div class="flex flex-col sm:flex-row justify-between items-start sm:items-end mb-6 md:mb-8 border-b border-white/5 pb-6 gap-4">
                    <div>
                        <h1 class="font-syne text-2xl md:text-3xl font-bold text-white">Social AI Engine <span class="text-sharp-purple">v2</span></h1>
                        <p class="text-xs md:text-sm text-lavender/60">6 post formats × 5 platforms × 40 niches. Zero prompt required.</p>
                    </div>
                    <div class="flex items-center gap-2 bg-card-dark border border-white/10 p-1 rounded-lg flex-shrink-0">
                        <button onclick="setSocialMode('single')" id="mode-btn-single" class="px-4 py-2 rounded-md text-xs font-bold transition-all bg-sharp-purple text-white">Single Post</button>
                        <button onclick="setSocialMode('calendar')" id="mode-btn-calendar" class="px-4 py-2 rounded-md text-xs font-bold transition-all text-lavender/60 hover:text-white">Weekly Calendar</button>
                    </div>
                </div>

                <div class="grid grid-cols-1 xl:grid-cols-4 gap-6 md:gap-8">

                    <div class="xl:col-span-1 space-y-5">

                        <div class="bg-card-dark border border-white/5 rounded-xl p-5">
                            <label class="block text-[10px] uppercase tracking-widest text-lavender/50 mb-3">① Platform</label>
                            <div class="space-y-2">
                                <label class="flex items-center gap-3 p-3 rounded-lg border border-white/10 hover:border-blue-500 cursor-pointer transition-colors bg-matte-black has-[:checked]:border-blue-500 has-[:checked]:bg-blue-500/5">
                                    <input type="radio" name="social_platform" value="LinkedIn" checked>
                                    <i data-lucide="linkedin" class="w-4 h-4 text-blue-500 flex-shrink-0"></i>
                                    <div>
                                        <span class="text-sm text-white font-medium block">LinkedIn</span>
                                        <span class="text-[10px] text-lavender/40">Formal, spaced, authoritative</span>
                                    </div>
                                </label>
                                <label class="flex items-center gap-3 p-3 rounded-lg border border-white/10 hover:border-blue-600 cursor-pointer transition-colors bg-matte-black has-[:checked]:border-blue-600 has-[:checked]:bg-blue-600/5">
                                    <input type="radio" name="social_platform" value="Facebook">
                                    <i data-lucide="facebook" class="w-4 h-4 text-blue-600 flex-shrink-0"></i>
                                    <div>
                                        <span class="text-sm text-white font-medium block">Facebook</span>
                                        <span class="text-[10px] text-lavender/40">Community, relatable, warm</span>
                                    </div>
                                </label>
                                <label class="flex items-center gap-3 p-3 rounded-lg border border-white/10 hover:border-success-green cursor-pointer transition-colors bg-matte-black has-[:checked]:border-success-green has-[:checked]:bg-success-green/5">
                                    <input type="radio" name="social_platform" value="WhatsApp">
                                    <i data-lucide="message-circle" class="w-4 h-4 text-success-green flex-shrink-0"></i>
                                    <div>
                                        <span class="text-sm text-white font-medium block">WhatsApp</span>
                                        <span class="text-[10px] text-lavender/40">Punchy, direct, conversational</span>
                                    </div>
                                </label>
                                <label class="flex items-center gap-3 p-3 rounded-lg border border-white/10 hover:border-pink-500 cursor-pointer transition-colors bg-matte-black has-[:checked]:border-pink-500 has-[:checked]:bg-pink-500/5">
                                    <input type="radio" name="social_platform" value="Instagram">
                                    <i data-lucide="instagram" class="w-4 h-4 text-pink-500 flex-shrink-0"></i>
                                    <div>
                                        <span class="text-sm text-white font-medium block">Instagram</span>
                                        <span class="text-[10px] text-lavender/40">Visual-first, hooks, emojis</span>
                                    </div>
                                </label>
                                <label class="flex items-center gap-3 p-3 rounded-lg border border-white/10 hover:border-sky-400 cursor-pointer transition-colors bg-matte-black has-[:checked]:border-sky-400 has-[:checked]:bg-sky-400/5">
                                    <input type="radio" name="social_platform" value="X (Twitter)">
                                    <i data-lucide="twitter" class="w-4 h-4 text-sky-400 flex-shrink-0"></i>
                                    <div>
                                        <span class="text-sm text-white font-medium block">X / Twitter</span>
                                        <span class="text-[10px] text-lavender/40">Thread-style, bold takes</span>
                                    </div>
                                </label>
                            </div>
                        </div>

                        <div class="bg-card-dark border border-white/5 rounded-xl p-5">
                            <label class="block text-[10px] uppercase tracking-widest text-lavender/50 mb-3">② Target Niche <span class="text-sharp-purple">(zero-prompt)</span></label>
                            <select id="social_niche" class="w-full bg-matte-black border border-white/10 text-white px-3 py-3 rounded-lg text-sm outline-none focus:border-sharp-purple">
                                <option value="">— No niche (use custom topic) —</option>
                                <?php foreach($master_niches as $slug => $name): ?>
                                <option value="<?= $name ?>"><?= $name ?></option>
                                <?php endforeach; ?>
                            </select>
                            <p class="text-[10px] text-lavender/40 mt-2">Select a niche to skip typing a prompt entirely.</p>
                        </div>

                        <div class="bg-card-dark border border-white/5 rounded-xl p-5">
                            <label class="block text-[10px] uppercase tracking-widest text-lavender/50 mb-3">③ Post Format</label>
                            <div class="space-y-2">
                                <label class="flex items-start gap-3 p-3 rounded-lg border border-white/10 hover:border-sharp-purple/50 cursor-pointer transition-colors bg-matte-black has-[:checked]:border-sharp-purple has-[:checked]:bg-sharp-purple/5">
                                    <input type="radio" name="social_format" value="auto" checked class="mt-0.5 flex-shrink-0">
                                    <div><span class="text-sm text-white font-medium flex items-center gap-1.5"><i data-lucide="shuffle" class="w-3 h-3 text-sharp-purple"></i> Auto-Mix</span><span class="text-[10px] text-lavender/40 block mt-0.5">AI picks format per variation for variety</span></div>
                                </label>
                                <label class="flex items-start gap-3 p-3 rounded-lg border border-white/10 hover:border-sharp-purple/50 cursor-pointer transition-colors bg-matte-black has-[:checked]:border-sharp-purple has-[:checked]:bg-sharp-purple/5">
                                    <input type="radio" name="social_format" value="infrastructure" class="mt-0.5 flex-shrink-0">
                                    <div><span class="text-sm text-white font-medium flex items-center gap-1.5"><i data-lucide="zap" class="w-3 h-3 text-sharp-purple"></i> Infrastructure Story</span><span class="text-[10px] text-lavender/40 block mt-0.5">Small biz vs big company. Solution: systems.</span></div>
                                </label>
                                <label class="flex items-start gap-3 p-3 rounded-lg border border-white/10 hover:border-sharp-purple/50 cursor-pointer transition-colors bg-matte-black has-[:checked]:border-sharp-purple has-[:checked]:bg-sharp-purple/5">
                                    <input type="radio" name="social_format" value="stat-bomb" class="mt-0.5 flex-shrink-0">
                                    <div><span class="text-sm text-white font-medium flex items-center gap-1.5"><i data-lucide="bar-chart-2" class="w-3 h-3 text-sharp-purple"></i> Stat Bomb</span><span class="text-[10px] text-lavender/40 block mt-0.5">Shocking truth → insight → solution.</span></div>
                                </label>
                                <label class="flex items-start gap-3 p-3 rounded-lg border border-white/10 hover:border-sharp-purple/50 cursor-pointer transition-colors bg-matte-black has-[:checked]:border-sharp-purple has-[:checked]:bg-sharp-purple/5">
                                    <input type="radio" name="social_format" value="hot-take" class="mt-0.5 flex-shrink-0">
                                    <div><span class="text-sm text-white font-medium flex items-center gap-1.5"><i data-lucide="flame" class="w-3 h-3 text-sharp-purple"></i> Controversial Take</span><span class="text-[10px] text-lavender/40 block mt-0.5">Challenge popular belief. Drive discussion.</span></div>
                                </label>
                                <label class="flex items-start gap-3 p-3 rounded-lg border border-white/10 hover:border-sharp-purple/50 cursor-pointer transition-colors bg-matte-black has-[:checked]:border-sharp-purple has-[:checked]:bg-sharp-purple/5">
                                    <input type="radio" name="social_format" value="case-study" class="mt-0.5 flex-shrink-0">
                                    <div><span class="text-sm text-white font-medium flex items-center gap-1.5"><i data-lucide="trending-up" class="w-3 h-3 text-sharp-purple"></i> Case Study</span><span class="text-[10px] text-lavender/40 block mt-0.5">Client transformation. Before → After.</span></div>
                                </label>
                                <label class="flex items-start gap-3 p-3 rounded-lg border border-white/10 hover:border-sharp-purple/50 cursor-pointer transition-colors bg-matte-black has-[:checked]:border-sharp-purple has-[:checked]:bg-sharp-purple/5">
                                    <input type="radio" name="social_format" value="behind-scenes" class="mt-0.5 flex-shrink-0">
                                    <div><span class="text-sm text-white font-medium flex items-center gap-1.5"><i data-lucide="layers" class="w-3 h-3 text-sharp-purple"></i> Behind The Build</span><span class="text-[10px] text-lavender/40 block mt-0.5">Show process, craft, and expertise.</span></div>
                                </label>
                                <label class="flex items-start gap-3 p-3 rounded-lg border border-white/10 hover:border-sharp-purple/50 cursor-pointer transition-colors bg-matte-black has-[:checked]:border-sharp-purple has-[:checked]:bg-sharp-purple/5">
                                    <input type="radio" name="social_format" value="direct-offer" class="mt-0.5 flex-shrink-0">
                                    <div><span class="text-sm text-white font-medium flex items-center gap-1.5"><i data-lucide="target" class="w-3 h-3 text-sharp-purple"></i> Direct Offer / CTA</span><span class="text-[10px] text-lavender/40 block mt-0.5">Specific pitch. Use sparingly.</span></div>
                                </label>
                                <label class="flex items-start gap-3 p-3 rounded-lg border border-white/10 hover:border-sharp-purple/50 cursor-pointer transition-colors bg-matte-black has-[:checked]:border-sharp-purple has-[:checked]:bg-sharp-purple/5">
                                    <input type="radio" name="social_format" value="fomo" class="mt-0.5 flex-shrink-0">
                                    <div><span class="text-sm text-white font-medium flex items-center gap-1.5"><i data-lucide="clock" class="w-3 h-3 text-sharp-purple"></i> FOMO / Urgency</span><span class="text-[10px] text-lavender/40 block mt-0.5">Competitors are getting platforms right now.</span></div>
                                </label>
                                <label class="flex items-start gap-3 p-3 rounded-lg border border-white/10 hover:border-sharp-purple/50 cursor-pointer transition-colors bg-matte-black has-[:checked]:border-sharp-purple has-[:checked]:bg-sharp-purple/5">
                                    <input type="radio" name="social_format" value="branding" class="mt-0.5 flex-shrink-0">
                                    <div><span class="text-sm text-white font-medium flex items-center gap-1.5"><i data-lucide="aperture" class="w-3 h-3 text-sharp-purple"></i> Branding Post</span><span class="text-[10px] text-lavender/40 block mt-0.5">Sell the idea of brand identity + platform.</span></div>
                                </label>
                            </div>
                        </div>

                        <div class="bg-card-dark border border-white/5 rounded-xl p-5">
                            <label class="block text-[10px] uppercase tracking-widest text-lavender/50 mb-2">④ Custom Topic <span class="text-lavender/30">(optional override)</span></label>
                            <p class="text-[10px] text-lavender/40 mb-3">Leave blank if a niche is selected above.</p>
                            <textarea id="social_topic" rows="3" class="w-full bg-matte-black border border-white/10 rounded-lg p-3 text-sm text-white focus:border-sharp-purple outline-none transition-colors" placeholder="e.g. Lawyers losing high-ticket clients because they rely only on Instagram..."></textarea>
                        </div>

                        <button type="button" onclick="generateSocialPostsWithAI()" class="w-full px-6 py-4 bg-sharp-purple text-white rounded-xl text-sm font-bold shadow-[0_0_20px_rgba(126,34,206,0.35)] hover:bg-white hover:text-matte-black transition-all flex items-center justify-center gap-2">
                            <i data-lucide="sparkles" class="w-5 h-5"></i> Generate Posts
                        </button>

                        <div class="bg-card-dark border border-white/5 rounded-xl p-4">
                            <label class="block text-[10px] uppercase tracking-widest text-lavender/50 mb-3">⚡ Quick-Fire Niches</label>
                            <div class="flex flex-wrap gap-2">
                                <?php
                                $quick_niches = ['Law Firm', 'Restaurant', 'Hospital', 'Real Estate', 'Hotel', 'Dental Clinic', 'Fintech Startup', 'School'];
                                foreach($quick_niches as $qn): ?>
                                <button type="button" onclick="quickNiche('<?= $qn ?>')" class="px-3 py-1.5 bg-matte-black border border-white/10 text-lavender/70 hover:border-sharp-purple hover:text-white rounded-lg text-[11px] font-medium transition-all"><?= $qn ?></button>
                                <?php endforeach; ?>
                            </div>
                        </div>

                    </div>

                    <div class="xl:col-span-3 flex flex-col gap-4" id="social-results-container">
                        <div id="social-empty-state" class="p-10 border border-white/5 border-dashed rounded-xl flex flex-col items-center justify-center text-center text-lavender/40 min-h-[400px]">
                            <i data-lucide="pen-tool" class="w-10 h-10 mb-4 opacity-30"></i>
                            <p class="text-sm font-medium mb-2">Ready to generate</p>
                            <p class="text-xs max-w-xs">Pick a platform, select a niche or enter a topic, choose a format — then hit Generate. No prompt needed if a niche is selected.</p>
                            <div class="mt-6 grid grid-cols-3 gap-3 text-[10px] w-full max-w-sm">
                                <div class="bg-matte-black border border-white/5 rounded-lg p-3 text-center">
                                    <div class="text-sharp-purple font-bold text-lg font-syne">6</div>
                                    <div class="text-lavender/40">Post Formats</div>
                                </div>
                                <div class="bg-matte-black border border-white/5 rounded-lg p-3 text-center">
                                    <div class="text-blue-400 font-bold text-lg font-syne">5</div>
                                    <div class="text-lavender/40">Platforms</div>
                                </div>
                                <div class="bg-matte-black border border-white/5 rounded-lg p-3 text-center">
                                    <div class="text-success-green font-bold text-lg font-syne">40</div>
                                    <div class="text-lavender/40">Niches</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div id="social-calendar-view" class="hidden mt-8">
                    <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between mb-6 border-b border-white/5 pb-4 gap-4">
                        <div>
                            <h3 class="font-syne text-lg font-bold text-white">7-Day Content Calendar</h3>
                            <p class="text-xs text-lavender/50">One post per day. Varied formats. Maximum reach.</p>
                        </div>
                        <button onclick="generateWeeklyCalendar()" class="px-5 py-3 bg-sharp-purple text-white rounded-lg text-sm font-bold hover:bg-white hover:text-matte-black transition-all flex items-center gap-2 flex-shrink-0">
                            <i data-lucide="calendar" class="w-4 h-4"></i> Generate Full Week
                        </button>
                    </div>
                    <div id="calendar-grid" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-4">
                        <div class="col-span-full p-10 border border-white/5 border-dashed rounded-xl text-center text-lavender/40">
                            <i data-lucide="calendar" class="w-8 h-8 mb-3 mx-auto opacity-30"></i>
                            <p class="text-sm">Select a niche above, then click Generate Full Week.</p>
                        </div>
                    </div>
                </div>

            </div>
        
        <div id="ai-loading-overlay" class="fixed inset-0 bg-black/90 z-[100] hidden items-center justify-center backdrop-blur-sm px-4">
            <div class="bg-card-dark border border-white/10 rounded-xl p-6 md:p-8 max-w-xl w-full flex flex-col items-center text-center shadow-2xl">
                <div class="w-16 h-16 rounded-full bg-blue-500/10 flex items-center justify-center mb-4 relative">
                    <i data-lucide="bot" class="w-8 h-8 text-blue-400 relative z-10"></i>
                    <div class="absolute inset-0 rounded-full border-2 border-blue-500 border-t-transparent animate-spin"></div>
                </div>
                <h3 id="ai-overlay-title" class="font-syne text-xl font-bold text-white mb-2">AI is Writing...</h3>
                <p id="ai-overlay-subtitle" class="text-sm text-lavender/60 mb-4">Generating custom copy based on your SEO Blueprint.</p>
                <div class="w-full bg-matte-black border border-white/5 rounded-full h-2 mb-3 overflow-hidden">
                    <div id="ai-progress-bar" class="h-full bg-sharp-purple rounded-full transition-all duration-500" style="width:0%"></div>
                </div>
                <div class="flex items-center justify-between w-full mb-4 px-1">
                    <span class="text-[10px] text-lavender/40 font-mono">Streaming tokens...</span>
                    <span id="ai-progress-pct" class="text-[10px] text-sharp-purple font-mono font-bold">0%</span>
                </div>
                <div class="w-full bg-matte-black border border-white/5 rounded-lg p-4 text-left h-32 overflow-y-auto mt-4">
                    <p id="ai-stream-output" class="text-xs font-mono text-blue-300 leading-relaxed opacity-70">Initializing stream...</p>
                </div>
            </div>
        </div>

    </main>

    <script>
        // Pass PHP variable to JS safely
        const localCompetitorsData = <?= $competitors_json ?>;

        lucide.createIcons();

        const sidebar = document.getElementById('sidebar');
        const mobileOverlay = document.getElementById('mobile-overlay');
        let isSidebarOpen = false;

        function toggleSidebar() {
            isSidebarOpen = !isSidebarOpen;
            if (isSidebarOpen) {
                sidebar.classList.remove('-translate-x-full');
                sidebar.classList.add('translate-x-0');
                mobileOverlay.classList.remove('hidden');
                setTimeout(() => mobileOverlay.classList.remove('opacity-0'), 10);
            } else {
                sidebar.classList.remove('translate-x-0');
                sidebar.classList.add('-translate-x-full');
                mobileOverlay.classList.add('opacity-0');
                setTimeout(() => mobileOverlay.classList.add('hidden'), 300);
            }
        }

        function switchTab(tabId) {
            document.querySelectorAll('.tab-content').forEach(tab => tab.classList.remove('active'));
            document.querySelectorAll('.nav-btn').forEach(btn => {
                btn.classList.remove('bg-sharp-purple/10', 'text-white', 'border-sharp-purple/20');
                btn.classList.add('text-lavender/60', 'border-transparent');
                const icon = btn.querySelector('i');
                if(icon) icon.classList.remove('text-sharp-purple');
            });

            document.getElementById('tab-' + tabId).classList.add('active');
            const activeBtn = document.getElementById('btn-' + tabId);
            if (activeBtn) {
                activeBtn.classList.remove('text-lavender/60', 'border-transparent');
                activeBtn.classList.add('bg-sharp-purple/10', 'text-white', 'border-sharp-purple/20');
                const activeIcon = activeBtn.querySelector('i');
                if(activeIcon) activeIcon.classList.add('text-sharp-purple');
            }

            if (window.innerWidth < 768 && isSidebarOpen) toggleSidebar();
        }

        // ==========================================
        // STATE MACHINE FOR THEMES (JS)
        // ==========================================
        let currentThemeIndex = 0;
        let themesData = [];
        
        // 1. Load initial JSON from hidden input
        try {
            themesData = JSON.parse(document.getElementById('ai-themes-json').value || '[]');
            while (themesData.length < 5) {
                themesData.push({});
            }
        } catch(e) { themesData = [{}, {}, {}, {}, {}]; }

        const themeFields = [
            'hero_subheadline', 
            'reality_p1', 'reality_p2', 'reality_p3', 'reality_p4',
            'pos1_title', 'pos1_desc', 'pos2_title', 'pos2_desc', 'pos3_title', 'pos3_desc', 'pos4_title', 'pos4_desc'
        ];

        function saveCurrentThemeToState() {
            themeFields.forEach(field => {
                const el = document.getElementById('theme_' + field);
                if (el) themesData[currentThemeIndex][field] = el.value;
            });
            // Update the hidden input immediately
            document.getElementById('ai-themes-json').value = JSON.stringify(themesData);
        }

        function loadThemeFromState(index) {
            themeFields.forEach(field => {
                const el = document.getElementById('theme_' + field);
                if (el) {
                    el.value = themesData[index][field] || '';
                    if (el.tagName === 'TEXTAREA') resizeTextarea(el);
                }
            });
        }

        function switchThemeTab(index) {
            saveCurrentThemeToState();
            currentThemeIndex = index;
            loadThemeFromState(index);
            
            // UI Toggle
            for(let i=0; i<5; i++) {
                const btn = document.getElementById('theme-btn-' + i);
                if(btn) {
                    if(i === index) {
                        btn.className = 'px-4 py-2 text-xs font-bold rounded bg-sharp-purple text-white whitespace-nowrap transition-all';
                    } else {
                        btn.className = 'px-4 py-2 text-xs font-bold rounded text-lavender/60 hover:text-white hover:bg-white/5 whitespace-nowrap transition-all';
                    }
                }
            }
        }

        // Call this right before form submission to ensure final keystrokes are caught
        function prepareThemesForSave() {
            saveCurrentThemeToState();
        }

        // ==========================================
        // TEXTAREA EXPANSION & AUTO-SAVE ENGINE
        // ==========================================
        function resizeTextarea(el) {
            el.style.height = 'auto';
            el.style.height = (el.scrollHeight) + 'px';
        }

        function setupTextareas() {
            document.querySelectorAll('textarea').forEach(ta => {
                ta.style.overflow = 'hidden';
                ta.style.resize = 'none';
                if (!ta.style.minHeight) ta.style.minHeight = '60px';
                
                ta.removeEventListener('input', ta._resizeHandler);
                ta._resizeHandler = function() { resizeTextarea(this); };
                ta.addEventListener('input', ta._resizeHandler);
                
                setTimeout(() => resizeTextarea(ta), 50);
            });
        }

        const currentNicheSlug = document.querySelector('[name="niche_slug"]')?.value;
        const draftKey = currentNicheSlug ? 'pseo_draft_' + currentNicheSlug : null;
        let draftData = {};

        function saveDraft() {
            if (!draftKey) return;
            const form = document.getElementById('niche-form');
            if(!form) return;
            saveCurrentThemeToState(); // Ensure state is fresh before draft save
            const formData = new FormData(form);
            const data = {};
            formData.forEach((value, key) => { if(key !== 'action' && key !== 'niche_slug') data[key] = value; });
            localStorage.setItem(draftKey, JSON.stringify(data));
        }

        function checkDraft() {
            if (!draftKey) return;
            const saved = localStorage.getItem(draftKey);
            if (saved) {
                draftData = JSON.parse(saved);
                const draftAlert = document.getElementById('draft-alert');
                if (draftAlert) {
                    draftAlert.classList.remove('hidden');
                    draftAlert.classList.add('flex');
                }
            }
            document.getElementById('niche-form')?.addEventListener('input', saveDraft);
        }

        function restoreDraft() {
            for (const key in draftData) {
                const el = document.querySelector(`[name="${key}"]`);
                if (el) {
                    el.value = draftData[key];
                    if(el.tagName === 'TEXTAREA') resizeTextarea(el);
                    el.classList.add('border-yellow-500');
                    setTimeout(() => el.classList.remove('border-yellow-500'), 1500);
                }
            }
            // Restore JSON state if present
            if (draftData['ai_themes_json']) {
                try {
                    themesData = JSON.parse(draftData['ai_themes_json']);
                    loadThemeFromState(currentThemeIndex);
                } catch(e) {}
            }
            discardDraft();
        }

        function discardDraft() {
            if (draftKey) localStorage.removeItem(draftKey);
            const draftAlert = document.getElementById('draft-alert');
            if (draftAlert) {
                draftAlert.classList.add('hidden');
                draftAlert.classList.remove('flex');
            }
        }

        document.getElementById('niche-form')?.addEventListener('submit', () => {
            if(draftKey) localStorage.removeItem(draftKey);
        });

        document.addEventListener('DOMContentLoaded', () => {
            loadThemeFromState(0);
            setupTextareas();
            checkDraft();
        });

        // ==========================================
        // GEMINI AI INTEGRATION
        // ==========================================
        const apiKey = "AIzaSyC1DPDJzt5psEkIDgqK3XztuVLgXnDwwZM";
        const apiModel = "gemini-2.5-flash-lite";

        // AI 2: Generate City Context
        const citySystemPrompt = `You are an SEO expert writing for GetOnline Studio (a premium digital agency). 
Your task is to write a highly specific, 3-4 sentence "Location Context" paragraph for a specific city in Nigeria. 

CRITICAL RULES:
1. You MUST mention 2-3 specific, real-world locations in that city (e.g., business districts, major streets, notable landmarks) to build local SEO relevance.
2. You MUST use the exact placeholder "{niche}" at least twice so the text dynamically adapts to the industry the user is viewing.
3. You MUST use Spintax formatting {option 1|option 2|option 3} for verbs and adjectives to ensure it reads differently on every page.
4. DO NOT claim we have a physical office there. Frame it as "having partnered with organizations near [Landmark]" or "building platforms for {niche}s in [City]".
5. DO NOT call the client a "local business" or say they will "dominate the local market." Many clients operate nationally or globally. Instead, use expansive terms like "scale their operations", "build absolute authority", or "expand their reach".
6. Keep it factual and professional. Avoid overly poetic words like "bustling", "vibrant tapestry", "nestled".
7. Output ONLY the raw text. No markdown.`;

        // AI 3: Generate 1,000-Word Listicle
        function getListicleSystemPrompt(citySlug) {
            let competitorRule = `4. THE COMPETITORS (#2 to #5): You MUST use REAL web design agencies, IT firms, tech companies, or digital marketing brands that actually operate in [City] (or Nigeria broadly). Do NOT use generic archetypes like 'Freelancers' or 'DIY Builders'—that insults the reader's intelligence. Find real local companies.`;
            
            if (localCompetitorsData && localCompetitorsData[citySlug] && localCompetitorsData[citySlug].length > 0) {
                const shuffled = [...localCompetitorsData[citySlug]].sort(() => 0.5 - Math.random());
                const selected = shuffled.slice(0, 4);
                
                competitorRule = `4. THE COMPETITORS (#2 to #5 - STRICTLY ENFORCED): 
You MUST use the following 4 specific local companies for spots #2, #3, #4, and #5. I have provided their real names and actual business descriptions below. 
DO NOT invent names. Use their descriptions to craft highly accurate, 150-word reviews detailing how their real-world capabilities apply to this specific [Niche]. If their phone number or website link is publicly known, mention it naturally in their review.\n`;
                
                selected.forEach((comp, index) => {
                    competitorRule += `   Rank #${index + 2}: ${comp}\n`;
                });
            }

            return `You are an elite SEO copywriter, trained in the exact writing style of Brian Dean (Backlinko).
Your task is to write a highly-ranked, comprehensive 1,000+ word "Listicle" article (Aggregator SEO).
The user will provide a City and a Niche. Your goal is to review the "Top [Niche] Web Designers in [City]".

WHO WE ARE (GETONLINE STUDIO CONTEXT):
You are writing this on behalf of GetOnline Studio. Our mission is helping brands and businesses GET ONLINE and SCALE. 
Our primary audience: Business owners who currently do NOT have a website and are losing customers because of it.
Our secondary audience: Businesses looking for a redesign because their current website is broken, ugly, or not bringing in sales.
Our advanced audience: Ambitious brands and high-tech clients who need custom software, highly functional web apps, and complex integrations.

TONE & STYLE RULES (BRIAN DEAN STYLE - CRITICAL):
1. Use ridiculously short sentences. Often just one line.
2. Write at a 6th-grade reading level. Keep the core messaging accessible. No jargon. No academic fluff.
3. Use "Bucket Brigades" to keep people reading (e.g., "Here is the deal:", "But there is a catch.", "Think about it.").
4. Speak directly to the reader using "you" and "your". Make them feel understood.
5. NEVER use AI buzzwords like: "In today's digital age," "competitive landscape," "seamless," "elevate," "unlock," or "delve."
6. Balance the messaging: Focus mostly on plain English benefits ("attracting customers", "stress-free setup"), but explicitly state our massive capability range: we build stress-free websites for beginners, AND highly functional web apps/custom software for tech-heavy clients.
7. Lots of white space. Every HTML <p> paragraph MUST be 1 to 3 sentences MAX.

CRITICAL RULES:
1. OUTPUT FORMAT: Return ONLY valid JSON with three keys: "target_keyword", "meta_title", and "content".
2. HTML & JSON SAFETY: The "content" value MUST be raw, beautifully formatted HTML. Use <h2>, <h3>, <p>, <ul>, <li>, <strong>, <table>. DO NOT wrap the JSON in markdown blocks like \`\`\`json. CRITICAL: Use SINGLE QUOTES ('') for all HTML attributes (e.g., <table class='table'>) to avoid breaking the JSON string.
3. UNIQUE INTRO HEADER: Do NOT start every article with generic phrasing like "Why Your [Niche] Needs a Website". Write a highly creative, completely unique H2 for the introduction. Focus on the cost of inaction, industry pain points, or massive upside.
4. COMPARISON TABLE: Immediately after the intro, output a clean HTML <table class='table'> comparing the 5 agencies. Columns must be: Agency Name, Best For, Standout Feature.
5. THE #1 SPOT: "GetOnline Studio" MUST ALWAYS be the #1 ranked agency. Start our review by bolding key phrases explaining exactly why having a website is crucial for this specific niche. Then, explicitly highlight our range.
${competitorRule}
7. PROS & CONS: For competitors #2 through #5, end their review with an unordered list <ul> containing exactly 2 Pros and 1 Con. Be objective.
8. BUYING GUIDE: Create an <h2>What to Look For in a [Niche] Web Designer</h2> section. Give 4 actionable tips.
9. THE FINAL VERDICT: End the article with an <h2>The Final Verdict</h2> section.

JSON FORMAT:
{
  "target_keyword": "Best [Niche] Web Design Agency in [City]",
  "meta_title": "Top 5 [Niche] Web Designers in [City] (${new Date().getFullYear()} Rankings)",
  "content": "<h2>[Insert Your Unique, Punchy H2 Here]</h2><p>...</p><table class='table'>...</table><h3>1. GetOnline Studio</h3><p>...</p><h3>2. [Real Local Agency Name]</h3><p>...</p><ul><li><strong>Pro:</strong>...</li><li><strong>Con:</strong>...</li></ul><h2>What to Look For...</h2><p>...</p><h2>The Final Verdict</h2><p>...</p>"
}`;
        }

        // ============================================================
        // CORE AI STREAM ENGINE
        // ==========================================

        async function triggerGeminiStream(systemInstruction, userPrompt, onUpdate, onComplete, onError, isJson = true) {
            const overlay      = document.getElementById('ai-loading-overlay');
            const streamOutput = document.getElementById('ai-stream-output');
            const overlayTitle = document.getElementById('ai-overlay-title');
            const overlaySubtitle = document.getElementById('ai-overlay-subtitle');

            overlay.classList.remove('hidden');
            overlay.classList.add('flex');
            if (overlayTitle)    overlayTitle.innerText    = 'AI is Writing...';
            if (overlaySubtitle) overlaySubtitle.innerText = 'Generating custom copy based on your Blueprint.';
            streamOutput.innerText = 'Connecting to Gemini AI...';

            try {
                const response = await fetch(
                    `https://generativelanguage.googleapis.com/v1beta/models/${apiModel}:streamGenerateContent?alt=sse&key=${apiKey}`,
                    {
                        method: 'POST',
                        headers: { 'Content-Type': 'application/json' },
                        body: JSON.stringify({
                            system_instruction: { parts: [{ text: systemInstruction }] },
                            contents: [{ role: 'user', parts: [{ text: userPrompt }] }],
                            generationConfig: {
                                temperature: 0.7,
                                maxOutputTokens: 10000,
                                responseMimeType: isJson ? 'application/json' : 'text/plain'
                            }
                        })
                    }
                );

                if (!response.ok) throw new Error(response.statusText);

                const reader  = response.body.getReader();
                const decoder = new TextDecoder();
                let fullText  = '';

                while (true) {
                    const { done, value } = await reader.read();
                    if (done) break;

                    const chunk = decoder.decode(value, { stream: true });
                    const lines = chunk.split('\n');

                    for (const line of lines) {
                        if (line.startsWith('data: ')) {
                            const jsonStr = line.slice(6).trim();
                            if (!jsonStr || jsonStr === '[DONE]') continue;
                            try {
                                const parsed = JSON.parse(jsonStr);
                                const token  = parsed?.candidates?.[0]?.content?.parts?.[0]?.text || '';
                                if (token) {
                                    fullText += token;
                                    streamOutput.innerText = fullText.length > 250 ? '...' + fullText.slice(-250) : fullText;
                                    streamOutput.scrollTop = streamOutput.scrollHeight;

                                    if (overlayTitle && overlayTitle.dataset.mode === 'calendar') {
                                        const dayMatches = (fullText.match(/"day"\s*:/g) || []).length;
                                        const pct = Math.min(Math.round((dayMatches / 7) * 100), 95);
                                        const bar = document.getElementById('ai-progress-bar');
                                        const pctLabel = document.getElementById('ai-progress-pct');
                                        if (bar)      bar.style.width = pct + '%';
                                        if (pctLabel) pctLabel.innerText = pct + '%';
                                        if (overlaySubtitle) overlaySubtitle.innerText = `Building day ${Math.min(dayMatches + 1, 7)} of 7...`;
                                    }
                                }
                            } catch (_) {}
                        }
                    }
                }

                let finalData = fullText;
                if (isJson) {
                    const cleanText = fullText.replace(/```json/g, '').replace(/```/g, '').trim();
                    finalData = JSON.parse(cleanText);
                }

                if(onComplete) onComplete(finalData);

                const bar = document.getElementById('ai-progress-bar');
                const pctLabel = document.getElementById('ai-progress-pct');
                if (bar)      { bar.style.width = '100%'; bar.classList.add('bg-success-green'); }
                if (pctLabel) pctLabel.innerText = '100%';
                streamOutput.innerText = 'Generation complete! Updating UI...';
                if (overlayTitle)    overlayTitle.innerText    = 'Done!';
                if (overlaySubtitle) overlaySubtitle.innerText = 'Rendering your content now...';

                setTimeout(() => {
                    overlay.classList.add('hidden');
                    overlay.classList.remove('flex');
                    if (overlayTitle) delete overlayTitle.dataset.mode;
                    if (bar) { bar.style.width = '0%'; bar.classList.remove('bg-success-green'); }
                    if (pctLabel) pctLabel.innerText = '0%';
                }, 900);

            } catch (error) {
                console.error('AI Error:', error);
                streamOutput.innerHTML = `<span class="text-red-400">Failed: ${error.message}</span>`;
                setTimeout(() => { overlay.classList.add('hidden'); overlay.classList.remove('flex'); }, 4000);
            }
        }

        // ==========================================
        // THEME ROTATION AI GENERATORS
        // ==========================================
        async function generateThemeAI() {
            const nicheName = document.getElementById('current-niche-name').innerText;
            const formatName = document.getElementById('current-format-name').innerText;
            
            const themePrompts = [
                "THEME 1 (Growth & Efficiency): Focus entirely on ROI, getting more leads, saving time with automation, and standard business growth. Make it sound like a smart, efficient business decision.",
                "THEME 2 (Premium Authority): Focus entirely on looking like the most expensive, elite option in the city. Talk about commanding higher rates, building undeniable trust, and repelling bargain hunters.",
                "THEME 3 (Aggressive Dominance): Focus entirely on stealing competitor traffic, dominating local search results, and aggressive client acquisition. Use a sharp, highly competitive tone.",
                "THEME 4 (Trust & Compliance): Focus entirely on proving legal, corporate, and structural credibility. The angle is 'clients won't pay unless they feel totally secure.'",
                "THEME 5 (Community & Convenience): Focus entirely on making it ridiculously easy for local consumers to buy. The angle is 'convenience wins the neighborhood.'"
            ];
            
            const selectedPrompt = themePrompts[currentThemeIndex] || themePrompts[0];
            
            const systemPrompt = `You are a world-class programmatic SEO copywriter for GetOnline Studio. Generate a highly persuasive narrative for the service: ${formatName}.
            
CRITICAL ANGLE FOR THIS GENERATION:
${selectedPrompt}

STRICT PROGRAMMATIC SEO RULES:
1. Short punchy sentences. Grade 6 reading level. NO TECH JARGON (do not say "DOM", "React", "Headless").
2. Speak directly to the business owner using "you" and "your".
3. YOU MUST USE THE EXACT SHORTCODE "{city_name}" at least 3 or 4 times in your text so the system can inject the local city. (e.g. "Clients in {city_name} are searching...")
4. YOU MUST USE THE EXACT SHORTCODE "{niche_name}" instead of hardcoding the niche. (e.g. "Your {niche_name} business...")
5. Use Spintax formatting {word1|word2|word3} for verbs and adjectives to ensure maximum variation. NEVER use {verb_action} or {verb_adjective} etc

Return ONLY a JSON object matching these exact keys:
{
  "hero_subheadline": "1-2 sentences. Speak to the pain point using {city_name}.",
  "reality_p1": "Rich paragraph. Ground reader in actual {city_name} search behaviour.",
  "reality_p2": "Rich paragraph. What happens without a platform?",
  "reality_p3": "Rich paragraph. What does a platform unlock?",
  "reality_p4": "Rich paragraph. Why act now in {city_name}?",
  "pos1_title": "Card 1 Title", "pos1_desc": "2-3 sentences explaining benefit.",
  "pos2_title": "Card 2 Title", "pos2_desc": "...",
  "pos3_title": "Card 3 Title", "pos3_desc": "...",
  "pos4_title": "Card 4 Title", "pos4_desc": "..."
}`;

            triggerGeminiStream(systemPrompt, "Generate the narrative now.", null, (data) => {
                for (const key in data) {
                    const el = document.getElementById('theme_' + key);
                    if (el) {
                        el.value = data[key];
                        if (el.tagName === 'TEXTAREA') resizeTextarea(el);
                        el.classList.add('border-success-green');
                        setTimeout(() => el.classList.remove('border-success-green'), 1500);
                    }
                }
                saveCurrentThemeToState();
            });
        }

        async function generateCoreAI() {
            const nicheName = document.getElementById('current-niche-name').innerText;
            const formatName = document.getElementById('current-format-name').innerText;
            
            const systemPrompt = `You are a world-class SEO copywriter for GetOnline Studio. Generate the static features and FAQs for the service: ${formatName}, targeting the niche: ${nicheName}.
            
RULES:
1. NO TECH JARGON. Speak plain English.
2. Return ONLY a JSON object matching these exact keys:
{
  "feat_headline": "Headline for What We Build.",
  "feat_subline": "1 sentence framing our niche knowledge.",
  "f1_title": "Feature 1 Title", "f1_desc": "Detailed explanation.",
  "f2_title": "Feature 2 Title", "f2_desc": "...",
  "f3_title": "Feature 3 Title", "f3_desc": "...",
  "f4_title": "Feature 4 Title", "f4_desc": "...",
  "f5_title": "Feature 5 Title", "f5_desc": "...",
  "f6_title": "Feature 6 Title", "f6_desc": "...",
  "faq1_q": "Question about Cost", "faq1_a": "Answer 1",
  "faq2_q": "Question about Timeline", "faq2_a": "Answer 2",
  "faq3_q": "Question about SEO", "faq3_a": "Answer 3",
  "faq4_q": "Question about Redesign", "faq4_a": "Answer 4",
  "faq5_q": "Niche specific Question", "faq5_a": "Answer 5",
  "cta_aspiration": "Name the niche's ultimate goal."
}`;

            triggerGeminiStream(systemPrompt, "Generate the core features and FAQs.", null, (data) => {
                for (const key in data) {
                    const el = document.querySelector(`[name="${key}"]`);
                    if (el) {
                        el.value = data[key];
                        if (el.tagName === 'TEXTAREA') resizeTextarea(el);
                        el.classList.add('border-success-green');
                        setTimeout(() => el.classList.remove('border-success-green'), 1500);
                    }
                }
            });
        }

        async function generateCityContextWithAI() {
            const cityName = document.getElementById('current-city-name').innerText;
            if (!confirm(`Generate local SEO context for ${cityName}?`)) return;

            triggerGeminiStream(
                citySystemPrompt,
                `Write the 3-4 sentence local context paragraph for ${cityName}, Nigeria. Make sure to mention actual, real-world business districts or areas in ${cityName}. Remember to include {niche} and Spintax formatting.`,
                null,
                (text) => {
                    const el = document.querySelector(`[name="city_intro"]`);
                    if (el) {
                        el.value = text;
                        if (el.tagName === 'TEXTAREA') resizeTextarea(el);
                        el.classList.add('border-success-green');
                        setTimeout(() => el.classList.remove('border-success-green'), 1500);
                    }
                },
                null,
                false
            );
        }
        
        async function generateListicleWithAI() {
            const cityName = document.getElementById('listicle-city-name').innerText;
            const nicheName = document.getElementById('listicle-niche-name').innerText;
            const citySlug = document.getElementById('lst-hidden-city').value;
            
            if (!confirm(`Generate a 1,000+ word SEO listicle for ${nicheName} in ${cityName}? This will take a moment and overwrite the current editor content.`)) return;

            triggerGeminiStream(
                getListicleSystemPrompt(citySlug),
                `Write a 1,000+ word aggregator listicle for the niche: ${nicheName} in the city: ${cityName}, Nigeria. Remember, GetOnline Studio is #1.`,
                null,
                (data) => {
                    if(data.target_keyword) {
                        const kwEl = document.getElementById('lst-target-keyword');
                        if (kwEl) kwEl.value = data.target_keyword;
                    }
                    if(data.meta_title) {
                        const mtEl = document.getElementById('lst-meta-title');
                        if (mtEl) mtEl.value = data.meta_title;
                    }
                    if(data.content) {
                        const el = document.querySelector('#listicle-form textarea[name="content"]');
                        if (el) {
                            el.value = data.content;
                            resizeTextarea(el);
                            el.classList.add('border-success-green');
                            setTimeout(() => el.classList.remove('border-success-green'), 1500);
                        }
                    }
                },
                null,
                true
            );
        }

        // ============================================================
        // SOCIAL ENGINE v2 — Platform specs, format prompts, system prompt
        // ============================================================

        const platformSpecs = {
            'LinkedIn': {
                tone: 'Professional, authoritative, educational. This is a long-form platform — do NOT write thin posts. Write 250–400 words minimum. Use aggressive white space: every 1-2 sentences gets its own line. Open with a single-line hook that stops the scroll (no intro, no "I want to talk about"). Build the argument slowly — educate, then inspire, then challenge, then call to action. Paragraphs should be 1-3 lines max. Use numbered lists or short bullet points sparingly to break up heavy sections. The tone is a confident expert talking directly to a business owner, not a marketer writing ad copy.',
                length: 'MINIMUM 250 words. Target 300–400 words. LinkedIn rewards depth — thin posts get buried. Do not cut short.',
                hashtags: 'MANDATORY: End with exactly 5–7 relevant professional hashtags on their own line. Examples: #WebDevelopment #DigitalTransformation #NigerianBusiness #GetOnline #SmartWebsites #Branding #BusinessGrowth — mix niche-specific and broad reach tags.',
                cta: 'End the post body (before hashtags) with a clear call to action: visit getonlinestudio.com, send a DM, or drop a comment. Then on a new line add the link: https://getonlinestudio.com'
            },
            'Facebook': {
                tone: 'Warm, community-focused, relatable. Tell a story or ask a question. Sound like a knowledgeable friend giving advice at a business meeting. Use "you" and "your" constantly. Short paragraphs. Emojis are welcome but not excessive — 2-4 max.',
                length: '150–250 words. Long enough to deliver value, short enough to hold attention.',
                hashtags: 'MANDATORY: End with 3–5 broad + niche hashtags. Examples: #GetOnline #NigerianBusiness #WebsiteDesign #SmallBusiness #BusinessTips',
                cta: 'Ask readers to comment, share, or tag a business owner. Then include the link on its own line: https://getonlinestudio.com'
            },
            'WhatsApp': {
                tone: 'Direct, punchy, broadcast-message style. Like a voice note converted to text. Every sentence is its own paragraph. Short. Sharp. No fluff. Bold key phrases with *asterisks*.',
                length: '100–160 words. Punchy enough to be read in full on a phone screen.',
                hashtags: 'MANDATORY: End with 2–3 hashtags max. #GetOnline #SmartWebsites #NigerianBusiness',
                cta: 'End with a direct action line, then the link on its own line: https://getonlinestudio.com'
            },
            'Instagram': {
                tone: 'Visual-first, benefit-driven, emoji-forward. The first line is EVERYTHING — it must be irresistible before the "more" cut. Use line breaks after every 1-2 sentences. Relatable and aspirational. Make them feel seen, then show them the possibility.',
                length: '120–200 words. First 2 lines must hook before the fold.',
                hashtags: 'MANDATORY: After 2 blank lines, add 6–10 hashtags mixing niche, location, and broad. #WebDevelopment #NigeriaWebsite #GetOnlineStudio #SmartWebsites #DigitalBranding #BusinessOwner #NaijaBusinesses',
                cta: 'Tell them to click link in bio OR DM a keyword. Then include: https://getonlinestudio.com'
            },
            'X (Twitter)': {
                tone: 'Bold, opinionated, slightly polarising. Format as a numbered thread 1/ 2/ 3/ etc. Each tweet under 280 chars. First tweet must earn the click to expand. Challenge conventional wisdom. Short, declarative statements.',
                length: 'Thread of 5–7 tweets. Number each one: 1/, 2/, etc.',
                hashtags: 'MANDATORY: Add 2–3 hashtags in the final tweet only. #GetOnline #WebDevelopment #NigerianBusiness',
                cta: 'Final tweet is the CTA. Include the link: https://getonlinestudio.com'
            }
        };

        const formatPrompts = {
            'infrastructure': (niche, platform) => `Write a social post for GetOnline Studio targeting ${niche || 'Nigerian business'} owners who currently have NO website — just a WhatsApp number and an Instagram page. The message: a social media page is rented land. A smart digital platform is infrastructure you OWN. GetOnline Studio doesn't just build websites — they build platforms: AI-powered, automated, professional systems that work 24/7 while you sleep. Think custom inquiry systems, automated follow-ups, booking engines, branded client portals. Frame it as the difference between a market stall and a proper storefront. Platform: ${platform}.`,

            'stat-bomb': (niche, platform) => `Write a social post that opens with a hard-hitting, credible stat or truth about ${niche || 'Nigerian businesses'} operating without a proper online presence. Then unpack it: what are they losing daily? Clients who searched and found a competitor instead. Opportunities that died because there was no professional platform to point people to. The solution isn't "get a website" — it's getting a SMART PLATFORM built by GetOnline Studio: automated, branded, converting, professional. Platform: ${platform}.`,

            'hot-take': (niche, platform) => `Write a controversial post that challenges a dangerous belief held by most ${niche || 'Nigerian business'} owners: that Instagram followers = business success. The truth: followers don't pay bills. Clients do. And clients look for credibility — a real website, a professional brand, a platform that answers their questions even at midnight. GetOnline Studio helps ${niche || 'businesses'} stop performing for algorithms and start BUILDING. Position social media as a traffic tool, and the smart platform as the destination. Platform: ${platform}.`,

            'case-study': (niche, platform) => `Write a before/after story about a ${niche || 'Nigerian business'} that was active on social media but had no real online presence beyond that. They got discovered, but clients couldn't verify them. They lost deals to less-capable competitors who had proper websites. GetOnline Studio built them a smart platform: professional brand identity, a website that explained their services, automated inquiry handling, and a system that built trust before any call was made. After: more inquiries, better clients, faster close rates. Use "a client" — no real names. Be specific and concrete. Platform: ${platform}.`,

            'behind-scenes': (niche, platform) => `Write a "behind the build" post about what GetOnline Studio actually does when building a platform for a ${niche || 'business'} — it's not template-dragging. It starts with understanding the business model, the client journey, and what action needs to happen online. Then: brand strategy, platform architecture, content structure, smart automations, AI integrations where relevant. The result is a living system — not a digital brochure. Position GetOnline Studio as a digital infrastructure partner, not a design shop. Platform: ${platform}.`,

            'direct-offer': (niche, platform) => `Write a direct, confident offer post for GetOnline Studio targeting ${niche || 'Nigerian business'} owners who are still operating without a proper online presence in 2025. No shame — but urgency. Every day without a platform is a day clients are finding someone else. What GetOnline Studio offers: a complete digital platform — professional website, brand identity, smart automations, AI-ready architecture — built specifically for ${niche || 'your business type'}. Not templates. Not shortcuts. Real platforms. The CTA should be specific: visit getonlinestudio.com to start or DM directly. Platform: ${platform}.`,

            'fomo': (niche, platform) => `Write a FOMO-driven post targeting ${niche || 'Nigerian business'} owners. Their competitors are getting smart platforms built right now. Clients are Googling, comparing, making decisions based on who looks more credible online. The ${niche || 'business'} with no website — or a bad one — is invisible in that moment. GetOnline Studio is helping businesses across Nigeria get online properly: real brand, smart platform, automated systems. The question isn't IF they should — it's whether they want to keep watching others win. Platform: ${platform}.`,

            'branding': (niche, platform) => `Write a post about why a ${niche || 'Nigerian business'} needs more than a website — they need a BRAND. GetOnline Studio builds both. Branding is the reason clients choose you over someone cheaper. It's your logo, your colours, your tone of voice, your visual identity — and how all of that comes alive on a professional platform. A smart website without a strong brand is a missed opportunity. A strong brand without a digital home is invisible. GetOnline Studio builds both as one integrated system. Platform: ${platform}.`,

            'auto': (niche, platform) => `You will choose the BEST post format for each variation to ensure maximum variety and impact. The niche is: ${niche || 'Nigerian businesses'}. Platform: ${platform}. Available formats: Infrastructure Story, Stat Bomb, Controversial Take (social media vs real platform), Case Study, Behind The Build, Direct Offer, FOMO Post, Branding Post. Use a COMPLETELY DIFFERENT format, angle, and emotional tone for each of the 3 variations. Do not repeat formats.`
        };

        const socialSystemPromptV2 = `You are the lead content strategist and ghostwriter for GetOnline Studio — a Nigerian digital infrastructure company.

WHAT GETONLINE STUDIO ACTUALLY IS (read this carefully):
GetOnline Studio is NOT a web design company. They are a DIGITAL INFRASTRUCTURE company. They help businesses, individuals, brands, and organizations GET ONLINE — many of whom currently have zero online presence beyond a WhatsApp number and an Instagram page. They educate, inspire, convince, and create urgency. They sell the IDEA of getting a proper digital home before they sell the service.

Their work includes:
- Professional websites and web platforms (not templates — real, custom-built systems)
- Brand identity and visual design (logos, colour systems, tone of voice)
- Smart web architecture with AI integrations and automations
- Custom dashboards, CRM pipelines, booking systems, client portals
- Web applications and custom software solutions
- Full digital transformation for all kinds of Nigerian businesses

Their TARGET AUDIENCE is Nigerian business owners, entrepreneurs, brands, and organizations who:
- Are active on social media but have no real website or platform
- Don't yet understand why a proper digital platform matters
- Need to be educated, inspired, shown FOMO, and convinced
- Are losing clients to competitors who look more credible online

THE EMOTIONAL JOURNEY TO CREATE IN EVERY POST:
Awareness → Recognition ("that's me") → Education → Desire → FOMO or Urgency → Call to Action

THE BRAND VOICE:
- Confident, direct, human. Like a knowledgeable business partner talking straight.
- Educational without being preachy. The goal is to make the reader feel smarter AND more urgent.
- Uses real talk: "Your WhatsApp number is not a business." "Instagram followers don't pay bills."
- Nigeria-aware: understands hustle culture, the informal economy, the aspiration to look professional.
- Sells the IDEA first, the service second.

STRUCTURE VARIETY — every post must use a different structure:
- Hook types: Bold claim / Uncomfortable truth / Story opener / Direct address ("If you run a [niche]...") / Shocking stat / "Unpopular opinion:" / Question that makes them stop
- Body types: Narrative arc / Numbered insight list / Contrast structure (Before vs After / Them vs You) / Single big idea expanded slowly / Problem→Cost→Solution
- Closing types: Open question that invites comments / Direct CTA / Observation that lingers / Challenge

LINKEDIN-SPECIFIC RULES (CRITICAL — apply when platform is LinkedIn):
- MINIMUM 280 words. Do NOT write short LinkedIn posts. LinkedIn's algorithm rewards long-form.
- Every 1-2 sentences gets its own line. Aggressive white space is non-negotiable.
- Build an argument: educate for 60% of the post, inspire for 20%, then CTA for 20%.
- No bullet points with dashes. Use numbered lists if needed.
- The post must feel like a mini-essay or column — substantial, not a quick thought.

FORBIDDEN — NEVER USE IN ANY POST:
- "In Nigeria's competitive landscape" / "In today's digital age" / "In the digital era"
- "Elevate", "Unlock potential", "Take your business to the next level", "Seamless", "Bustling", "Vibrant"
- "Web design company" or "design agency" — always say "digital infrastructure" or "platform development"
- Identical opening structures across the 3 variations
- Bullet points starting with dashes (—)
- Generic CTAs like "contact us today" without specificity

MANDATORY FOR EVERY SINGLE POST — NON-NEGOTIABLE:
1. Hashtags: MUST be included at the end of every post. Count depends on platform spec.
2. Link: MUST end with https://getonlinestudio.com on its own line, after the hashtags.
3. If you skip hashtags or the link, the output is WRONG.

OUTPUT FORMAT — return ONLY valid JSON, no markdown, no explanation outside the JSON:
{
  "platform": "...",
  "niche": "...",
  "format_requested": "...",
  "posts": [
    { "variation": 1, "format_used": "Infrastructure Story", "hook_type": "Bold claim", "post": "Full post text including hashtags and link..." },
    { "variation": 2, "format_used": "Stat Bomb", "hook_type": "Stat opener", "post": "Full post text including hashtags and link..." },
    { "variation": 3, "format_used": "Controversial Take", "hook_type": "Unpopular opinion", "post": "Full post text including hashtags and link..." }
  ]
}`;

        const calendarSystemPrompt = `You are the lead content strategist for GetOnline Studio — a Nigerian digital infrastructure company that helps businesses GET ONLINE with smart platforms, brand identity, AI integrations, automations, and custom web systems.

Their audience: Nigerian business owners who currently live on WhatsApp and Instagram with no real website or digital platform. The job of every post is to educate them, inspire them, show them what they're missing, make them feel FOMO, and drive them to getonlinestudio.com.

Generate exactly 7 social media posts for a full week content calendar. Each day MUST use a different format, a completely different angle, and a different emotional tone.

CRITICAL RULES FOR EVERY SINGLE POST:
1. Hashtags are MANDATORY. Every post must end with relevant hashtags (platform-appropriate count).
2. The link https://getonlinestudio.com is MANDATORY on its own line at the very end of every post.
3. LinkedIn posts must be MINIMUM 280 words — no thin posts.
4. NEVER say "web design company" — always say "digital infrastructure" or "platform development".
5. NEVER use: "competitive landscape", "today's digital age", "seamless", "elevate", "unlock potential".
6. Sell the IDEA first (why they need a platform), then position GetOnline Studio as the answer.

Day format schedule:
- Monday: Infrastructure Story — the website as a business system, not a brochure
- Tuesday: Stat Bomb — open with a hard truth about businesses without online presence
- Wednesday: Controversial Take — Instagram/WhatsApp is NOT a business platform
- Thursday: Case Study — before/after of a business that got online properly
- Friday: Behind The Build — what GetOnline Studio actually builds and how
- Saturday: Direct Offer — specific, confident pitch with CTA
- Sunday: FOMO / Inspirational — competitors are getting platforms built right now

Return ONLY this valid JSON:
{
  "week": [
    { "day": "Monday",    "format": "Infrastructure Story",    "post": "Full post text with hashtags and https://getonlinestudio.com at the end..." },
    { "day": "Tuesday",   "format": "Stat Bomb",               "post": "Full post text with hashtags and https://getonlinestudio.com at the end..." },
    { "day": "Wednesday", "format": "Controversial Take",      "post": "Full post text with hashtags and https://getonlinestudio.com at the end..." },
    { "day": "Thursday",  "format": "Case Study",              "post": "Full post text with hashtags and https://getonlinestudio.com at the end..." },
    { "day": "Friday",    "format": "Behind The Build",        "post": "Full post text with hashtags and https://getonlinestudio.com at the end..." },
    { "day": "Saturday",  "format": "Direct Offer",            "post": "Full post text with hashtags and https://getonlinestudio.com at the end..." },
    { "day": "Sunday",    "format": "FOMO / Inspirational",    "post": "Full post text with hashtags and https://getonlinestudio.com at the end..." }
  ]
}`;

        // ============================================================
        // SOCIAL ENGINE v2 — Core Functions
        // ============================================================

        let socialMode = 'single';

        function setSocialMode(mode) {
            socialMode = mode;
            const onCls  = 'px-4 py-2 rounded-md text-xs font-bold transition-all bg-sharp-purple text-white';
            const offCls = 'px-4 py-2 rounded-md text-xs font-bold transition-all text-lavender/60 hover:text-white';
            document.getElementById('mode-btn-single').className    = mode === 'single'   ? onCls : offCls;
            document.getElementById('mode-btn-calendar').className  = mode === 'calendar' ? onCls : offCls;
            document.getElementById('social-calendar-view').classList.toggle('hidden', mode !== 'calendar');
            document.getElementById('social-results-container').classList.toggle('hidden', mode === 'calendar');
        }

        function quickNiche(niche) {
            document.getElementById('social_niche').value = niche;
            document.getElementById('social_topic').value = '';
            generateSocialPostsWithAI();
        }

        async function generateSocialPostsWithAI() {
            const platform = document.querySelector('input[name="social_platform"]:checked')?.value || 'LinkedIn';
            const niche    = document.getElementById('social_niche')?.value || '';
            const topic    = document.getElementById('social_topic')?.value?.trim() || '';
            const format   = document.querySelector('input[name="social_format"]:checked')?.value || 'auto';

            if (!niche && !topic) {
                alert('Please select a niche or enter a custom topic.');
                return;
            }

            const platformSpec   = platformSpecs[platform] || platformSpecs['LinkedIn'];
            const resolvedPrompt = topic || (formatPrompts[format]?.(niche, platform) || `Generate posts for a ${niche} targeting ${platform}`);

            const userPrompt = `Platform: ${platform}
Platform Tone Spec: ${platformSpec.tone}
Target Length: ${platformSpec.length}
Hashtag Style: ${platformSpec.hashtags}
CTA Style: ${platformSpec.cta}

Niche: ${niche || 'General Nigerian business'}
Format requested: ${format}

Topic / Angle:
${resolvedPrompt}

Generate 3 structurally different variations. Each MUST use a different hook type, body structure, and ending. Make them feel genuinely different from one another.`;

            await triggerGeminiStream(
                socialSystemPromptV2,
                userPrompt,
                null,
                (result) => renderSocialResults(result, platform, niche),
                null,
                true
            );
        }

        function renderSocialResults(result, platform, niche) {
            const container  = document.getElementById('social-results-container');
            const emptyState = document.getElementById('social-empty-state');
            if (emptyState) emptyState.remove();
            container.innerHTML = '';
            container.classList.remove('hidden');

            const platformColors = {
                'LinkedIn':   'text-blue-400 border-blue-500/30 bg-blue-500/5',
                'Facebook':   'text-blue-500 border-blue-600/30 bg-blue-600/5',
                'WhatsApp':   'text-success-green border-success-green/30 bg-success-green/5',
                'Instagram':  'text-pink-400 border-pink-500/30 bg-pink-500/5',
                'X (Twitter)':'text-sky-400 border-sky-400/30 bg-sky-400/5',
            };
            const pc = platformColors[platform] || 'text-lavender border-white/10 bg-white/5';

            container.innerHTML = `
                <div class="flex items-center justify-between mb-1 flex-wrap gap-2">
                    <div class="flex items-center gap-2 flex-wrap">
                        <span class="text-[10px] px-3 py-1.5 rounded-full border font-bold uppercase tracking-widest ${pc}">${platform}</span>
                        ${niche ? `<span class="text-[10px] px-3 py-1.5 rounded-full border border-sharp-purple/30 bg-sharp-purple/5 text-sharp-purple font-bold uppercase tracking-widest">${niche}</span>` : ''}
                    </div>
                    <button onclick="generateSocialPostsWithAI()" class="px-3 py-1.5 bg-white/5 hover:bg-white/10 text-lavender/70 hover:text-white border border-white/10 rounded-lg text-xs font-bold transition-all flex items-center gap-1.5">
                        <i data-lucide="refresh-cw" class="w-3 h-3"></i> Regenerate
                    </button>
                </div>
            `;

            const formatColors = {
                'Infrastructure Story': 'text-sharp-purple bg-sharp-purple/10 border-sharp-purple/30',
                'Stat Bomb':            'text-yellow-400 bg-yellow-500/10 border-yellow-500/30',
                'Controversial Take':   'text-red-400 bg-red-500/10 border-red-500/30',
                'Case Study':           'text-success-green bg-success-green/10 border-success-green/30',
                'Behind The Build':     'text-blue-400 bg-blue-500/10 border-blue-500/30',
                'Direct Offer':         'text-pink-400 bg-pink-500/10 border-pink-500/30',
            };

            const posts = result?.posts || [];

            if (posts.length === 0) {
                container.innerHTML += `<div class="p-4 bg-red-500/10 border border-red-500/30 text-red-400 rounded-xl text-sm">Failed to parse AI response. Please try again.</div>`;
                return;
            }

            posts.forEach((post) => {
                const fmtKey   = Object.keys(formatColors).find(k => post.format_used?.toLowerCase().includes(k.toLowerCase().split(' ')[0]));
                const fmtColor = fmtKey ? formatColors[fmtKey] : 'text-lavender/70 bg-white/5 border-white/10';
                const words    = (post.post || '').trim().split(/\s+/).filter(Boolean).length;

                container.innerHTML += `
                    <div class="bg-card-dark border border-white/5 rounded-xl overflow-hidden hover:border-sharp-purple/20 transition-all">
                        <div class="flex items-center justify-between p-4 border-b border-white/5 bg-panel-dark/40 flex-wrap gap-2">
                            <div class="flex items-center gap-2 flex-wrap">
                                <span class="text-[10px] font-bold px-2 py-1 rounded bg-sharp-purple/20 text-sharp-purple uppercase tracking-widest">Var 0${post.variation}</span>
                                ${post.format_used ? `<span class="text-[10px] font-bold px-2 py-1 rounded border ${fmtColor} uppercase tracking-widest">${post.format_used}</span>` : ''}
                                ${post.hook_type   ? `<span class="text-[10px] text-lavender/40 font-mono hidden sm:inline">Hook: ${post.hook_type}</span>` : ''}
                            </div>
                            <button type="button" onclick="copySocialPost(this)" class="px-3 py-1.5 bg-white/5 border border-white/10 text-lavender/70 hover:text-white hover:bg-white/10 rounded-lg text-xs font-bold transition-all flex items-center gap-1.5">
                                <i data-lucide="copy" class="w-3.5 h-3.5"></i> Copy
                            </button>
                        </div>
                        <div class="p-5">
                            <div class="post-content font-manrope text-sm text-lavender/90 leading-relaxed whitespace-pre-wrap">${escapeHtml(post.post)}</div>
                        </div>
                        <div class="px-5 pb-4 flex items-center justify-between border-t border-white/5 pt-3">
                            <span class="text-[10px] text-lavender/30 font-mono">${words} words</span>
                            <button type="button" onclick="toggleEditPost(this)" class="text-[10px] text-lavender/40 hover:text-white flex items-center gap-1 transition-colors">
                                <i data-lucide="edit-2" class="w-3 h-3"></i> Edit inline
                            </button>
                        </div>
                    </div>
                `;
            });

            lucide.createIcons();
        }

        async function generateWeeklyCalendar() {
            const platform = document.querySelector('input[name="social_platform"]:checked')?.value || 'LinkedIn';
            const niche    = document.getElementById('social_niche')?.value || 'Nigerian business';
            const platformSpec = platformSpecs[platform];

            document.getElementById('calendar-grid').innerHTML = '<div class="col-span-full text-center text-lavender/50 text-sm py-12 flex flex-col items-center gap-3"><div class="w-8 h-8 rounded-full border-2 border-sharp-purple border-t-transparent animate-spin"></div>Starting calendar generation...</div>';
            
            const overlayTitle = document.getElementById('ai-overlay-title');
            const overlaySubtitle = document.getElementById('ai-overlay-subtitle');
            if (overlayTitle) {
                overlayTitle.dataset.mode = 'calendar';
                overlayTitle.innerText = 'Building Your 7-Day Calendar...';
            }
            if (overlaySubtitle) overlaySubtitle.innerText = 'Building day 1 of 7...';

            const calPrompt = `Platform: ${platform}
Platform Spec: ${platformSpec.tone}
Length: ${platformSpec.length}
Hashtags: ${platformSpec.hashtags}
Niche Focus: ${niche}

Generate a 7-day content calendar. Use the format schedule: Monday=Infrastructure Story, Tuesday=Stat Bomb, Wednesday=Controversial Take, Thursday=Case Study, Friday=Behind The Build, Saturday=Direct Offer, Sunday=Inspirational/Motivational.`;

            await triggerGeminiStream(
                calendarSystemPrompt,
                calPrompt,
                null,
                (result) => renderCalendar(result),
                null,
                true
            );
        }

        function renderCalendar(result) {
            const calGrid = document.getElementById('calendar-grid');
            calGrid.innerHTML = '';
            const week = result?.week || [];

            const dayColors = [
                'border-sharp-purple/30', 'border-blue-500/30', 'border-red-500/30',
                'border-success-green/30', 'border-blue-400/30', 'border-pink-500/30', 'border-yellow-500/30'
            ];

            week.forEach((day, i) => {
                const words = (day.post || '').trim().split(/\s+/).filter(Boolean).length;
                calGrid.innerHTML += `
                    <div class="bg-card-dark border ${dayColors[i] || 'border-white/10'} rounded-xl overflow-hidden flex flex-col">
                        <div class="p-4 border-b border-white/5 bg-panel-dark/40 flex items-center justify-between">
                            <div>
                                <div class="font-syne font-bold text-white text-sm">${day.day}</div>
                                <div class="text-[10px] text-lavender/40 uppercase tracking-widest">${day.format}</div>
                            </div>
                            <button onclick="copySocialPost(this)" class="p-2 bg-white/5 border border-white/10 text-lavender/70 hover:text-white rounded-lg transition-all">
                                <i data-lucide="copy" class="w-3.5 h-3.5"></i>
                            </button>
                        </div>
                        <div class="p-4 flex-1">
                            <div class="post-content font-manrope text-xs text-lavender/80 leading-relaxed whitespace-pre-wrap line-clamp-6">${escapeHtml(day.post)}</div>
                        </div>
                        <div class="px-4 pb-3 flex items-center justify-between border-t border-white/5 pt-2">
                            <span class="text-[10px] text-lavender/30 font-mono">${words}w</span>
                            <button onclick="expandCalendarPost(this)" class="text-[10px] text-sharp-purple hover:text-white transition-colors">View full ↗</button>
                        </div>
                    </div>
                `;
            });

            lucide.createIcons();
        }

        function expandCalendarPost(btn) {
            const postDiv = btn.closest('.bg-card-dark').querySelector('.post-content');
            postDiv.classList.toggle('line-clamp-6');
            btn.textContent = postDiv.classList.contains('line-clamp-6') ? 'View full ↗' : 'Collapse ↑';
        }

        function toggleEditPost(btn) {
            const postDiv = btn.closest('.bg-card-dark').querySelector('.post-content');
            const isEditing = postDiv.contentEditable === 'true';
            postDiv.contentEditable = isEditing ? 'false' : 'true';
            postDiv.classList.toggle('border', !isEditing);
            postDiv.classList.toggle('border-sharp-purple/50', !isEditing);
            postDiv.classList.toggle('rounded', !isEditing);
            postDiv.classList.toggle('p-2', !isEditing);
            if (!isEditing) postDiv.focus();
            btn.innerHTML = isEditing
                ? '<i data-lucide="edit-2" class="w-3 h-3"></i> Edit inline'
                : '<i data-lucide="check" class="w-3 h-3"></i> Done editing';
            lucide.createIcons();
        }

        function escapeHtml(text) {
            const map = {'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#039;'};
            return (text || '').replace(/[&<>"']/g, m => map[m]);
        }

        function copySocialPost(btn) {
            const postContent = btn.closest('.bg-card-dark')?.querySelector('.post-content');
            const text = postContent?.innerText || '';
            const textArea = document.createElement('textarea');
            textArea.value = text;
            document.body.appendChild(textArea);
            textArea.select();
            try {
                document.execCommand('copy');
                const orig = btn.innerHTML;
                btn.innerHTML = `<i data-lucide="check" class="w-3.5 h-3.5"></i> Copied!`;
                btn.classList.add('text-success-green', 'border-success-green/40');
                lucide.createIcons();
                setTimeout(() => {
                    btn.innerHTML = orig;
                    btn.classList.remove('text-success-green', 'border-success-green/40');
                    lucide.createIcons();
                }, 2000);
            } catch(e) { console.error('Copy failed', e); }
            document.body.removeChild(textArea);
        }

        /* ======================================================
           ARTICLE ENGINE — Enhanced JS
           ====================================================== */

        function setListicleView(view) {
            ['editor','coverage','queue', 'database'].forEach(v => {
                document.getElementById('lst-view-' + v)?.classList.add('hidden');
                const btn = document.getElementById('lstbtn-' + v);
                if (btn) {
                    btn.className = 'relative px-3 sm:px-4 py-2 rounded-md text-xs font-bold transition-all whitespace-nowrap ' +
                        (v === view ? 'bg-sharp-purple text-white' : 'text-lavender/60 hover:text-white');
                    if(v === 'database' && view !== 'database') btn.innerHTML = '<i data-lucide="database" class="w-3.5 h-3.5 inline-block align-text-bottom mr-1"></i> Database';
                    if(v === 'database' && view === 'database') btn.innerHTML = '<i data-lucide="database" class="w-3.5 h-3.5 inline-block align-text-bottom mr-1"></i> Database';
                    lucide.createIcons();
                }
            });
            document.getElementById('lst-view-' + view)?.classList.remove('hidden');
        }

        function setContentView(mode) {
            const srcBtn  = document.getElementById('cv-btn-source');
            const prvBtn  = document.getElementById('cv-btn-preview');
            const srcView = document.getElementById('lst-source-view');
            const prvView = document.getElementById('lst-preview-view');
            const frame   = document.getElementById('lst-preview-frame');
            const on  = 'px-3 py-1.5 text-[10px] font-bold rounded border bg-sharp-purple/20 text-sharp-purple border-sharp-purple/30 transition-all';
            const off = 'px-3 py-1.5 text-[10px] font-bold rounded border bg-white/5 text-lavender/50 border-white/10 hover:text-white transition-all';
            if (mode === 'source') {
                srcView.classList.remove('hidden');
                prvView.classList.add('hidden');
                srcBtn.className = on; prvBtn.className = off;
            } else {
                const html = document.getElementById('lst-content-textarea')?.value || '';
                frame.innerHTML = html;
                srcView.classList.add('hidden');
                prvView.classList.remove('hidden');
                srcBtn.className = off; prvBtn.className = on;
            }
        }

        function updateWordCount(textarea) {
            const text   = textarea.value.replace(/<[^>]*>/g, ' ').replace(/\s+/g, ' ').trim();
            const words  = text ? text.split(' ').filter(Boolean).length : 0;
            const badge  = document.getElementById('lst-word-count-badge');
            if (!badge) return;
            badge.textContent = words.toLocaleString() + ' words';
            const isGood = words >= 1000;
            badge.className = 'text-[10px] font-mono font-bold px-2 py-1 rounded border transition-all ' +
                (isGood
                    ? 'text-success-green bg-success-green/10 border-success-green/30'
                    : 'text-yellow-400 bg-yellow-500/10 border-yellow-500/30');
        }

        async function loadListicleAjax(citySlug, nicheSlug) {
            const cityEl  = document.getElementById('lst-city-select');
            const nicheEl = document.getElementById('lst-niche-select');
            const spinner = document.getElementById('lst-ajax-spinner');
            const city    = citySlug  || cityEl?.value;
            const niche   = nicheSlug || nicheEl?.value;
            if (!city || !niche) return;

            if (citySlug  && cityEl)  cityEl.value  = citySlug;
            if (nicheSlug && nicheEl) nicheEl.value = nicheSlug;

            document.getElementById('lst-hidden-city').value  = city;
            document.getElementById('lst-hidden-niche').value = niche;

            const cityName  = cityEl?.options[cityEl?.selectedIndex]?.text || city;
            const nicheName = nicheEl?.options[nicheEl?.selectedIndex]?.text || niche;
            const cityNameEl  = document.getElementById('listicle-city-name');
            const nicheNameEl = document.getElementById('listicle-niche-name');
            if (cityNameEl)  cityNameEl.textContent  = cityName;
            if (nicheNameEl) nicheNameEl.textContent = nicheName;

            spinner?.classList.remove('hidden');
            spinner?.classList.add('flex');

            try {
                const url = `${window.location.pathname}?ajax=load_listicle&city=${encodeURIComponent(city)}&niche=${encodeURIComponent(niche)}`;
                const res  = await fetch(url);
                const data = await res.json();

                const kw     = document.getElementById('lst-target-keyword');
                const mt     = document.getElementById('lst-meta-title');
                const ct     = document.getElementById('lst-content-textarea');
                const st     = document.getElementById('lst-status-select');
                const badge  = document.getElementById('lst-status-badge');

                if (kw) kw.value = data.target_keyword || '';
                if (mt) mt.value = data.meta_title     || '';
                if (ct) { ct.value = data.content || ''; updateWordCount(ct); resizeTextarea(ct); }
                if (st) st.value = data.status || 'draft';
                if (badge) {
                    const isPub = data.status === 'publish';
                    badge.textContent = isPub ? 'Published' : 'Draft';
                    badge.className = 'text-[10px] px-2 py-1 rounded border uppercase tracking-widest whitespace-nowrap font-bold ' +
                        (isPub ? 'bg-success-green/20 text-success-green border-success-green/30'
                               : 'bg-yellow-500/20 text-yellow-500 border-yellow-500/30');
                }

                const frame = document.getElementById('lst-preview-frame');
                if (frame && !document.getElementById('lst-preview-view')?.classList.contains('hidden')) {
                    frame.innerHTML = data.content || '';
                }

            } catch (e) {
                console.error('AJAX load failed:', e);
            } finally {
                spinner?.classList.add('hidden');
                spinner?.classList.remove('flex');
            }
        }

        function jumpToEditor(citySlug, nicheSlug) {
            setListicleView('editor');
            loadListicleAjax(citySlug, nicheSlug);
            document.getElementById('tab-listicles')?.scrollIntoView({ behavior: 'smooth', block: 'start' });
        }

        function filterDatabase() {
            const input = document.getElementById("db-search");
            const filter = input.value.toLowerCase();
            const nodes = document.querySelectorAll('.db-row');

            nodes.forEach(row => {
                let match = false;
                row.querySelectorAll('.db-search-text').forEach(cell => {
                    if (cell.innerText.toLowerCase().indexOf(filter) > -1) {
                        match = true;
                    }
                });
                row.style.display = match ? "" : "none";
            });
        }

        const autoAdvanceToggle = document.getElementById('auto-advance-toggle');
        const autoAdvanceTrack  = document.getElementById('auto-advance-track');
        const autoAdvanceThumb  = document.getElementById('auto-advance-thumb');

        function updateAutoAdvanceUI() {
            if (!autoAdvanceToggle) return;
            const on = autoAdvanceToggle.checked;
            if (autoAdvanceTrack) autoAdvanceTrack.style.backgroundColor = on ? '#7e22ce' : '';
            if (autoAdvanceThumb) autoAdvanceThumb.style.transform = on ? 'translateX(16px)' : '';
        }
        autoAdvanceToggle?.addEventListener('change', updateAutoAdvanceUI);
        updateAutoAdvanceUI(); 

        let currentQueueIndex = -1;
        async function loadQueueItem(index) {
            const item = document.querySelector(`#queue-item-${index}`);
            if (!item) return;

            currentQueueIndex = index;
            const citySlug  = item.dataset.citySlug;
            const nicheSlug = item.dataset.nicheSlug;

            document.querySelectorAll('.queue-item').forEach(el => {
                el.classList.remove('border-sharp-purple/40', 'bg-sharp-purple/5');
            });
            item.classList.add('border-sharp-purple/40', 'bg-sharp-purple/5');

            const btn = item.querySelector('.generate-queue-btn');
            if (btn) {
                btn.innerHTML = '<div class="w-3.5 h-3.5 rounded-full border-2 border-sharp-purple border-t-transparent animate-spin"></div> Loading...';
                btn.disabled = true;
            }

            setListicleView('editor');
            await loadListicleAjax(citySlug, nicheSlug);

            if (btn) {
                btn.innerHTML = '<i data-lucide="check" class="w-3.5 h-3.5"></i> Loaded';
                btn.className = 'generate-queue-btn px-4 py-2 bg-success-green/20 text-success-green border border-success-green/30 rounded-lg text-xs font-bold flex items-center gap-2';
                lucide.createIcons();
            }

            document.getElementById('tab-listicles')?.scrollIntoView({ behavior: 'smooth', block: 'start' });

            await new Promise(r => setTimeout(r, 600));
            generateListicleWithAI();
        }

        document.getElementById('listicle-form')?.addEventListener('submit', function () {
            const toggle = document.getElementById('auto-advance-toggle');
            if (!toggle?.checked) return;

            const nextIndex = currentQueueIndex + 1;
            const nextItem  = document.querySelector(`#queue-item-${nextIndex}`);

            if (nextItem) {
                sessionStorage.setItem('pseo_queue_autoadvance', currentQueueIndex);
            }
        });

        window.addEventListener('DOMContentLoaded', () => {
            const nextIndex = sessionStorage.getItem('pseo_queue_autoadvance');
            if (nextIndex !== null) {
                sessionStorage.removeItem('pseo_queue_autoadvance');
                const idx = parseInt(nextIndex);
                if (!isNaN(idx)) {
                    setTimeout(() => {
                        switchTab('listicles');
                        setListicleView('queue');
                        setTimeout(() => loadQueueItem(idx), 400);
                    }, 800);
                }
            }
        });

        const _originalGenerateListicle = generateListicleWithAI;
        generateListicleWithAI = async function () {
            const cityEl  = document.getElementById('lst-city-select');
            const nicheEl = document.getElementById('lst-niche-select');

            const cityName  = cityEl?.options[cityEl?.selectedIndex]?.text || '';
            const nicheName = nicheEl?.options[nicheEl?.selectedIndex]?.text || '';
            document.getElementById('listicle-city-name').textContent  = cityName;
            document.getElementById('listicle-niche-name').textContent = nicheName;

            await _originalGenerateListicle();
        };
        /* ---- PWA: Register Service Worker ---- */
        if ('serviceWorker' in navigator) {
            navigator.serviceWorker.register('/wp/sw.js', { scope: '/wp/' })
                .then(reg => console.log('[pSEO PWA] SW registered:', reg.scope))
                .catch(err => console.warn('[pSEO PWA] SW failed:', err));
        }

        /* ---- PWA: Intercept beforeinstallprompt to show in-app banner ---- */
        let _pwaPrompt = null;
        window.addEventListener('beforeinstallprompt', (e) => {
            e.preventDefault();
            _pwaPrompt = e;
            // Show a subtle install nudge in the sidebar footer area
            const logoutBtn = document.querySelector('aside .flex-shrink-0 a');
            if (logoutBtn && !document.getElementById('pwa-install-nudge')) {
                const nudge = document.createElement('a');
                nudge.id = 'pwa-install-nudge';
                nudge.href = '#';
                nudge.className = 'w-full flex items-center justify-center gap-2 bg-sharp-purple/10 text-sharp-purple font-bold text-xs uppercase tracking-widest py-3 rounded-lg hover:bg-sharp-purple hover:text-white transition-all border border-sharp-purple/20 mb-2';
                nudge.innerHTML = '<i data-lucide="download" class="w-4 h-4"></i> Install App';
                nudge.addEventListener('click', async (ev) => {
                    ev.preventDefault();
                    if (!_pwaPrompt) { window.location.href = '/wp/pwa-install.php'; return; }
                    _pwaPrompt.prompt();
                    const { outcome } = await _pwaPrompt.userChoice;
                    if (outcome === 'accepted') nudge.remove();
                    _pwaPrompt = null;
                });
                logoutBtn.parentNode.insertBefore(nudge, logoutBtn);
                lucide.createIcons();
            }
        });
    </script>
</body>
</html>