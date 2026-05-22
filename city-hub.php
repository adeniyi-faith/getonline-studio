<?php
/**
 * CITY HUB SILO TEMPLATE, v6.5 (DETERMINISTIC SEO, GRAMMAR FIX, NO LEAKAGES)
 * Routes URLs like: /locations/osogbo/ -> city-hub.php?city=osogbo
 */

// 1. Boot up WordPress silently
define('WP_USE_THEMES', false);
require_once(__DIR__ . '/wp/wp-load.php');

// ─── SUPPRESS WP HEAD/FOOTER JUNK ─────────────────────────────────────────────
add_action('after_setup_theme', function() {
    remove_action('wp_head', 'rsd_link');
    remove_action('wp_head', 'wp_generator');
    remove_action('wp_head', 'wp_shortlink_wp_head');
    remove_action('wp_head', 'rest_output_link_wp_head');
    remove_action('wp_head', 'wp_oembed_add_discovery_links');
    remove_action('wp_head', 'wp_site_icon', 99);
    remove_action('wp_head', 'print_emoji_detection_script', 7);
    remove_action('wp_print_styles', 'print_emoji_styles');
    remove_action('wp_head', 'wp_print_fonts', 50);
    add_action('wp_enqueue_scripts', function() {
        wp_dequeue_style('contact-form-7');
        wp_dequeue_script('contact-form-7');
        wp_dequeue_script('swv');
    });
});

// Catch URL Parameters FIRST so we can use it as our SEO seed
$city_slug = isset($_GET['city']) ? sanitize_title($_GET['city']) : 'lagos';

// Helper function to prevent double-encoding
function go_safe_text($text) {
    return esc_html(html_entity_decode($text ?? '', ENT_QUOTES, 'UTF-8'));
}

// NEW FUNCTION: Parse AI Markdown to HTML
// This acts as an automatic filter to convert AI asterisks into proper HTML bold/italic tags
function parse_ai_markdown($text) {
    // Convert **bold** to <strong>bold</strong>
    $text = preg_replace('/\*\*(.+?)\*\*/s', '<strong>$1</strong>', $text);
    // Convert *italic* to <em>italic</em>
    $text = preg_replace('/(?<!\*)\*(?!\*)(.+?)(?<!\*)\*(?!\*)/s', '<em>$1</em>', $text);
    return $text;
}

// DETERMINISTIC SPINTAX ENGINE
// Locks content variations permanently per-city for Google SEO safely.
// Includes a built-in grammar cleanser to catch AI typos.
function go_spin_text($text, $seed = null) {
    global $city_slug;
    $active_seed = $seed ? $seed : (!empty($city_slug) ? $city_slug : 'locked_seed');

    $text = (string) $text;
    $spun = preg_replace_callback('/\{(((?>[^\{\}]+)|(?R))*)\}/x', function ($match) use ($active_seed) {
        $inner = go_spin_text($match[1], $active_seed);
        $parts = explode('|', $inner);

        // Deterministic Hash: Ensures the same text is generated on every page refresh!
        $hash = md5($active_seed . $match[0]);
        $index = hexdec(substr($hash, 0, 8)) % count($parts);

        return $parts[$index];
    }, $text);

    // THE GRAMMAR CLEANSER: Fixes bad AI pluralizations (e.g. {business}s -> businesss)
    $cleanup = [
        'businesss'      => 'businesses',
        'companys'       => 'companies',
        'brandss'        => 'brands',
        'industrys'      => 'industries',
        'organizationss' => 'organizations',
        'agencys'        => 'agencies',
        'nichess'        => 'niches'
    ];
    return str_ireplace(array_keys($cleanup), array_values($cleanup), $spun);
}

// Verify City Exists to prevent "Fake City" indexing
$city_post = get_page_by_path($city_slug, OBJECT, 'pseo_location');
if (!$city_post || $city_post->post_status !== 'publish') {
    status_header(404);
    wp_redirect('/locations/');
    exit;
}

$city_name = go_safe_text($city_post->post_title);

// Fetch short intro (Top) and Long Pillar (Bottom)
$city_intro = get_post_meta($city_post->ID, 'city_intro', true);
$city_long_content = get_post_meta($city_post->ID, 'city_long_content', true);

// Aggressively convert the literal word "niche" into natural business terms
if (!empty($city_intro)) {
    $city_intro = preg_replace('/\bniches\b/i', '{businesses|brands|companies|organizations}', $city_intro);
    $city_intro = preg_replace('/\bniche\b/i', '{business|brand|company|organization}', $city_intro);
    $city_intro = str_ireplace(['{niche}s', '{niche}'], ['{businesses|brands|companies}', '{business|brand|company}'], $city_intro);
}

if (!empty($city_long_content)) {
    $city_long_content = preg_replace('/\bniches\b/i', '{businesses|brands|companies|organizations}', $city_long_content);
    $city_long_content = preg_replace('/\bniche\b/i', '{business|brand|company|organization}', $city_long_content);
    $city_long_content = str_ireplace(['{niche}s', '{niche}'], ['{businesses|brands|companies}', '{business|brand|company}'], $city_long_content);

    // Process the AI content to convert asterisks into proper HTML formatting
    $city_long_content = parse_ai_markdown($city_long_content);
}

// 3. Fetch Dynamic Active Cities for Cross-City Navigation
$active_cities_query = get_posts([
    'post_type'   => 'pseo_location',
    'numberposts' => -1,
    'post_status' => 'publish',
    'orderby'     => 'title',
    'order'       => 'ASC'
]);

$all_cities = [];
foreach ($active_cities_query as $c) {
    $all_cities[$c->post_name] = $c->post_title;
}

// 4. Fetch Dynamic Active Niches for Silo Links
$active_niches_query = get_posts([
    'post_type'   => 'pseo_niche',
    'numberposts' => -1,
    'post_status' => 'publish',
    'orderby'     => 'title',
    'order'       => 'ASC'
]);

$niche_icons = [
    'school' => 'book-open', 'church' => 'heart', 'hospital' => 'activity', 'law-firm' => 'scale',
    'hotel' => 'building', 'restaurant' => 'utensils', 'ecommerce' => 'shopping-cart', 'real-estate' => 'home',
    'ngo' => 'globe', 'fintech' => 'credit-card', 'software' => 'code', 'construction' => 'hard-hat',
    'pharmacy' => 'pill', 'event-planning' => 'calendar', 'beauty-spa' => 'sparkles', 'tech-startup' => 'rocket',
    'logistics' => 'truck', 'dental' => 'smile', 'crypto' => 'bitcoin', 'solar' => 'sun',
    'accounting' => 'calculator', 'bakery' => 'cookie', 'fitness-gym' => 'dumbbell', 'interior-design' => 'sofa',
    'travel-agency' => 'plane', 'photography' => 'camera', 'recruitment' => 'users', 'microfinance' => 'landmark',
    'insurance' => 'shield', 'oil-gas' => 'fuel', 'wedding-planner' => 'heart-handshake', 'catering' => 'chef-hat',
    'cleaning' => 'sparkles', 'driving-school' => 'car', 'fashion-boutique' => 'shirt', 'makeup-artist' => 'wand-2',
    'music-school' => 'music', 'pet-store' => 'paw-print', 'supermarket' => 'shopping-basket', 'waste-management' => 'recycle',
];

$master_niches = [];
foreach ($active_niches_query as $n) {
    $slug = $n->post_name;
    $icon = isset($niche_icons[$slug]) ? $niche_icons[$slug] : 'briefcase';
    $master_niches[$slug] = ['name' => $n->post_title, 'icon' => $icon];
}

// Fetch Listicles
global $wpdb;
$table_name = $wpdb->prefix . 'pseo_listicles';
$city_listicles = $wpdb->get_results(
    $wpdb->prepare("SELECT niche_slug, target_keyword FROM $table_name WHERE city_slug = %s AND status = 'publish' ORDER BY target_keyword ASC", $city_slug)
);

// Fetch Neighborhoods
$active_nb = get_post_meta($city_post->ID, '_pseo_active_neighborhoods', true);
if (!is_array($active_nb)) $active_nb = [];

$neighborhood_links = [];
$neighborhood_names = [];
$nb_file = __DIR__ . '/neighborhoods.json';
if (file_exists($nb_file) && !empty($active_nb)) {
    $nb_data = json_decode(file_get_contents($nb_file), true);
    if (isset($nb_data[$city_slug])) {
        foreach ($nb_data[$city_slug] as $n_slug => $n_name) {
            if (in_array($n_slug, $active_nb)) {
                $neighborhood_names[] = $n_name;
                $neighborhood_links[] = [
                    'name' => $n_name,
                    'url'  => "/locations/{$city_slug}/{$n_slug}/business-website-designer/"
                ];
            }
        }
    }
}

$service_formats = [
    'website-designer'        => 'Website Designer',
    'website-developer'       => 'Website Developer',
    'web-design-agency'       => 'Web Design Agency',
    'website-design-services' => 'Design Services',
    'branding-agency'         => 'Branding Agency',
];

// HOME PAGE KEYWORD LINK — deterministic per city, cycles across 5 SEO variants
$home_kw_variants = [
    'Web Developer in Nigeria',
    'Web Design Agency in Nigeria',
    'Web Designer in Nigeria',
    'Web Design Firm in Nigeria',
    'Web Design Company in Nigeria',
];
$home_kw_seed = abs(crc32($city_slug));
$home_kw_label = $home_kw_variants[$home_kw_seed % count($home_kw_variants)];

$current_year = date('Y');

// CITY HUB PRICING INTELLIGENCE
// Deterministic per-city price variation using crc32 (same seed logic as spintax engine).
// No external JSON needed; ranges are grounded in Nigerian market reality.
$price_seed = abs(crc32($city_slug));
// Base multiplier: 0.85 to 1.35 range, locked per city
$price_mult = 0.85 + (($price_seed % 100) / 200); // Gives 0.85 to 1.35

$city_min_price     = (int) round(120000 * $price_mult / 5000) * 5000;
$city_avg_price     = (int) round(270000 * $price_mult / 5000) * 5000;
$city_max_price     = (int) round(600000 * $price_mult / 5000) * 5000;
// Digital gap: 65 to 85% (locked per city)
$city_digital_gap   = 65 + ($price_seed % 21);

// SMART FAQ LOADER: AI-generated FAQs with static fallback
// get_post_meta with true can return '' (never saved) or a serialized array.
// We do a double-fetch to handle edge cases where WordPress stores it differently.
$ai_faqs = get_post_meta($city_post->ID, '_pseo_city_faqs', true);
if (!is_array($ai_faqs) || count($ai_faqs) < 4) {
    $ai_faqs_multi = get_post_meta($city_post->ID, '_pseo_city_faqs');
    if (!empty($ai_faqs_multi) && is_array($ai_faqs_multi[0]) && count($ai_faqs_multi[0]) >= 4) {
        $ai_faqs = $ai_faqs_multi[0];
    }
}

// FAQs
$raw_faqs = (is_array($ai_faqs) && count($ai_faqs) >= 4) ? $ai_faqs : [
    ["q" => "How much does a professional website cost in {$city_name}?", "a" => "Website costs in {$city_name} vary based on complexity. A basic business site differs from a robust platform. We provide custom quotes after a free discovery call."],
    ["q" => "Do you strictly serve businesses in {$city_name}?", "a" => "While we love helping brands dominate the {$city_name} market, we serve clients globally. Our remote workflows allow us to partner seamlessly anywhere."],
    ["q" => "How long does it take to build a website?", "a" => "Our average turnaround time is 7 to 14 days for standard business websites. Complex systems take longer. We map out a clear timeline before development starts."],
    ["q" => "Will my website rank on Google in {$city_name}?", "a" => "Absolutely. Every platform we build comes with core Local SEO architecture integrated to ensure Google prioritises your business for local searches."]
]; // end static fallback

$city_faqs = [];
$faq_schema = [];
foreach($raw_faqs as $faq) {
    $city_faqs[] = ["q" => go_spin_text($faq['q']), "a" => go_spin_text($faq['a'])];
    $faq_schema[] = ["@type" => "Question", "name" => go_safe_text($faq['q']), "acceptedAnswer" => ["@type" => "Answer", "text" => go_safe_text($faq['a'])]];
}

// Schema
$areas_served = [];
foreach($neighborhood_names as $nb_name) { $areas_served[] = ["@type" => "City", "name" => $nb_name]; }
if(empty($areas_served)) { $areas_served[] = ["@type" => "City", "name" => $city_name]; }

// CANONICAL BASE: Always use the frontend root domain — never home_url() which
// resolves to the /wp/ subdirectory and creates a canonical split.
define('GO_SITE_URL', 'https://getonlinestudio.com');

$local_business_schema = [
    "@context"       => "https://schema.org",
    "@type"          => ["LocalBusiness", "ProfessionalService"],
    "name"           => "GetOnline Studio",
    "alternateName"  => "GetOnline Studio {$city_name}",
    "image"          => "https://getonlinestudio.com/insights/wp-content/uploads/2026/02/GetOnline_Studio_Logo.jpg",
    "url"            => GO_SITE_URL . "/locations/{$city_slug}/",
    "telephone"      => "+2349061150443",
    "priceRange"     => "₦₦₦",
    "description"    => "Web design company and web development agency serving businesses in {$city_name}, Nigeria. We build professional websites, web apps, and digital platforms. Over 9 years of experience.",
    "foundingDate"   => "2016",
    "numberOfEmployees" => ["@type" => "QuantitativeValue", "minValue" => 5, "maxValue" => 20],
    "slogan"         => "Over 9 Years Building Nigeria's Digital Future",
    "address"        => [
        "@type"           => "PostalAddress",
        "streetAddress"   => "48 Gbongan-Ibadan Road",
        "addressLocality" => "Osogbo",
        "addressRegion"   => "Osun State",
        "postalCode"      => "230284",
        "addressCountry"  => "NG"
    ],
    "aggregateRating" => [
        "@type"       => "AggregateRating",
        "ratingValue" => "5.0",
        "reviewCount" => "7",
        "bestRating"  => "5",
        "worstRating" => "1"
    ],
    "review" => [
        ["@type" => "Review", "author" => ["@type" => "Person", "name" => "Olayinka Itunu Damilare"], "reviewRating" => ["@type" => "Rating", "ratingValue" => "5", "bestRating" => "5"], "reviewBody" => "It's not always easy working with a team when you aren't in the same location, but our experience with GetOnline Studio was top-notch. Faith made the process simple. He doesn't just build pages; he builds solutions."],
        ["@type" => "Review", "author" => ["@type" => "Person", "name" => "Mr. Mike"], "reviewRating" => ["@type" => "Rating", "ratingValue" => "5", "bestRating" => "5"], "reviewBody" => "GetOnline Studio developed a website and mobile app for us, and we couldn't be happier with the result. They made the whole process easy and delivered exactly what we needed."],
        ["@type" => "Review", "author" => ["@type" => "Person", "name" => "Amb. Dr. Jernail Singh Anand"], "reviewRating" => ["@type" => "Rating", "ratingValue" => "5", "bestRating" => "5"], "reviewBody" => "GetOnline Studio proved to be a highly trusted partner. The speed of delivery did not compromise quality. We highly recommend them for any institution seeking world-class web development."],
        ["@type" => "Review", "author" => ["@type" => "Person", "name" => "Emmanuel Amaechi"], "reviewRating" => ["@type" => "Rating", "ratingValue" => "5", "bestRating" => "5"], "reviewBody" => "GetOnline Studio delivered more than just a system. They transformed how we operate."],
        ["@type" => "Review", "author" => ["@type" => "Person", "name" => "Chief Lamina Kamiludeen Omotoyosi"], "reviewRating" => ["@type" => "Rating", "ratingValue" => "5", "bestRating" => "5"], "reviewBody" => "I proudly commend GetOnline Studio for their exceptional quality and reliable services. Their professionalism, creativity, and attention to detail consistently set them apart."],
        ["@type" => "Review", "author" => ["@type" => "Person", "name" => "Tobiloba Babalola"], "reviewRating" => ["@type" => "Rating", "ratingValue" => "5", "bestRating" => "5"], "reviewBody" => "Working with GetOnline Studio on our microfinance app was an excellent experience. He demonstrated a high level of professionalism and a clear understanding of our business needs."],
        ["@type" => "Review", "author" => ["@type" => "Person", "name" => "Mr. Ogundeji Sinmisola"], "reviewRating" => ["@type" => "Rating", "ratingValue" => "5", "bestRating" => "5"], "reviewBody" => "They really met our expectations and our church vision. Good work."],
    ],
    "areaServed"     => $areas_served,
    "hasOfferCatalog" => [
        "@type" => "OfferCatalog",
        "name"  => "Web Design Services in {$city_name}",
        "itemListElement" => [
            ["@type" => "Offer", "itemOffered" => ["@type" => "Service", "name" => "Web Design in {$city_name}"]],
            ["@type" => "Offer", "itemOffered" => ["@type" => "Service", "name" => "Web Development in {$city_name}"]],
            ["@type" => "Offer", "itemOffered" => ["@type" => "Service", "name" => "SEO Services in {$city_name}"]],
        ]
    ]
];

$breadcrumb_schema = [
    "@context" => "https://schema.org",
    "@type"    => "BreadcrumbList",
    "itemListElement" => [
        ["@type" => "ListItem", "position" => 1, "name" => "Home",      "item" => GO_SITE_URL . "/"],
        ["@type" => "ListItem", "position" => 2, "name" => "Locations", "item" => GO_SITE_URL . "/locations/"],
        ["@type" => "ListItem", "position" => 3, "name" => "Web Designer in {$city_name}", "item" => GO_SITE_URL . "/locations/{$city_slug}/"],
    ]
];

$schema_json_faq        = json_encode(["@context" => "https://schema.org", "@type" => "FAQPage", "mainEntity" => $faq_schema], JSON_UNESCAPED_SLASHES);
$schema_json_local      = json_encode($local_business_schema, JSON_UNESCAPED_SLASHES);
$schema_json_breadcrumb = json_encode($breadcrumb_schema, JSON_UNESCAPED_SLASHES);

// Rotate title between high-value keyword framings — deterministic per city
$title_variants = [
    "Web Design Company in {$city_name} | GetOnline Studio",
    "Web Designer in {$city_name} | Web Design Agency — GetOnline Studio",
    "Best Web Designer in {$city_name} | GetOnline Studio",
    "Web Design & Development Company in {$city_name} | GetOnline Studio",
    "Website Designer in {$city_name} | GetOnline Studio",
];
$title_seed = abs(crc32($city_slug . 'title'));
$meta_title = $title_variants[$title_seed % count($title_variants)];

$meta_desc = "GetOnline Studio is a web design company in {$city_name} with over 9 years of experience. We build professional websites, web apps, and digital platforms. Not a freelancer — a full-service web design and development agency.";
?>
<!DOCTYPE html>
<html lang="en" class="scroll-smooth">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">

    <title><?= go_safe_text($meta_title) ?></title>
    <meta name="description" content="<?= go_safe_text($meta_desc) ?>">
    <link rel="canonical" href="<?= GO_SITE_URL . "/locations/{$city_slug}/" ?>">

    <link rel="icon" href="https://getonlinestudio.com/insights/wp-content/uploads/2026/02/GetOnline_Studio_Logo.jpg" sizes="32x32" />
    <meta property="og:title" content="<?= go_safe_text($meta_title) ?>" />
    <meta property="og:description" content="<?= go_safe_text($meta_desc) ?>" />
    <meta property="og:type" content="website" />
    <meta property="og:url" content="<?= GO_SITE_URL . "/locations/{$city_slug}/" ?>" />
    <meta property="og:image" content="https://getonlinestudio.com/insights/wp-content/uploads/2026/02/GetOnline_Studio_Logo.jpg" />

    <script type="application/ld+json"><?= $schema_json_faq ?></script>
    <script type="application/ld+json"><?= $schema_json_local ?></script>
    <script type="application/ld+json"><?= $schema_json_breadcrumb ?></script>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Manrope:wght@300;400;600;700&family=Syne:wght@400;700;800&family=Fira+Code:wght@400;600&family=Space+Grotesk:wght@400;700&display=swap" rel="stylesheet">
    <?php wp_head(); ?>
    <script src="https://unpkg.com/lucide@latest"></script>

    <script src="https://cdn.tailwindcss.com?plugins=typography"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        'matte-black': '#101010', 'card-dark': '#0a0a0a',
                        'lavender': '#e9d5ff', 'sharp-purple': '#7e22ce',
                        'code-green': '#4ade80', 'panel-dark': '#151515'
                    },
                    fontFamily: {
                        'syne': ['Syne', 'sans-serif'], 'manrope': ['Manrope', 'sans-serif'],
                        'mono': ['Fira Code', 'monospace'], 'space': ['Space Grotesk', 'sans-serif'],
                    },
                    backgroundImage: {
                        'noise': "url('data:image/svg+xml,%3Csvg viewBox=%220 0 200 200%22 xmlns=%22http://www.w3.org/2000/svg%22%3E%3Cfilter id=%22noiseFilter%22%3E%3CfeTurbulence type=%22fractalNoise%22 baseFrequency=%220.65%22 numOctaves=%223%22 stitchTiles=%22stitch%22/%3E%3C/filter%3E%3Crect width=%22100%25%22 height=%22100%25%22 filter=%22url(%23noiseFilter)%22 opacity=%220.05%22/%3E%3C/svg%3E')",
                    },
                    animation: {
                        'float': 'float 6s ease-in-out infinite',
                    }
                }
            }
        }
    </script>

    <link rel="stylesheet" href="/assets/css/city-hub.css">
</head>
<body class="bg-matte-black bg-noise font-manrope selection:bg-sharp-purple selection:text-white relative">

    <div class="cursor-dot"></div>
    <div class="cursor-outline"></div>
    <div id="cursor-preview" class="mouse-preview"></div>

    <!-- Floating WhatsApp Widget -->
    <div class="fixed bottom-6 right-6 z-50 flex flex-col items-end pointer-events-none">
        <!-- Widget Chat Window -->
        <div id="wa-float-window" class="widget-hidden mb-4 w-[90vw] max-w-[340px] bg-card-dark border border-lavender/20 rounded-2xl shadow-2xl flex-col overflow-hidden transition-all duration-300 origin-bottom-right pointer-events-auto">
            <!-- Header -->
            <div class="bg-[#151515] p-4 border-b border-lavender/10 flex justify-between items-center cursor-pointer hover-target" onclick="toggleWaWidget()">
                <div class="flex items-center gap-3">
                    <div class="w-2 h-2 bg-green-500 rounded-full animate-pulse"></div>
                    <span class="font-syne font-bold text-white">GetOnline Studio</span>
                </div>
                <button class="text-lavender/50 hover:text-white transition-colors focus:outline-none"><i data-lucide="x" class="w-5 h-5"></i></button>
            </div>
            <!-- Body -->
            <div class="p-5">
                <p class="text-sm font-manrope text-lavender/80 mb-4 leading-relaxed">
                    Hello! What digital services do you need for your business in <span class="text-white font-bold"><?= go_safe_text($city_name) ?></span>?
                </p>
                <div class="space-y-2 mb-5 max-h-[220px] overflow-y-auto hide-scrollbar pb-2">
                    <label class="flex items-center gap-3 p-2.5 rounded-lg border border-white/5 bg-matte-black hover:border-sharp-purple/50 cursor-pointer transition-colors hover-target">
                        <input type="checkbox" value="Website Design" class="wa-service-cb w-4 h-4 accent-sharp-purple rounded focus:ring-sharp-purple">
                        <span class="text-sm text-lavender/90 font-bold">Website Design</span>
                    </label>
                    <label class="flex items-center gap-3 p-2.5 rounded-lg border border-white/5 bg-matte-black hover:border-sharp-purple/50 cursor-pointer transition-colors hover-target">
                        <input type="checkbox" value="Web Development & Portals" class="wa-service-cb w-4 h-4 accent-sharp-purple rounded focus:ring-sharp-purple">
                        <span class="text-sm text-lavender/90 font-bold">Web Development & Portals</span>
                    </label>
                    <label class="flex items-center gap-3 p-2.5 rounded-lg border border-white/5 bg-matte-black hover:border-sharp-purple/50 cursor-pointer transition-colors hover-target">
                        <input type="checkbox" value="SEO & Google Ranking" class="wa-service-cb w-4 h-4 accent-sharp-purple rounded focus:ring-sharp-purple">
                        <span class="text-sm text-lavender/90 font-bold">SEO & Google Ranking</span>
                    </label>
                    <label class="flex items-center gap-3 p-2.5 rounded-lg border border-white/5 bg-matte-black hover:border-sharp-purple/50 cursor-pointer transition-colors hover-target">
                        <input type="checkbox" value="Local SEO & Google Maps" class="wa-service-cb w-4 h-4 accent-sharp-purple rounded focus:ring-sharp-purple">
                        <span class="text-sm text-lavender/90 font-bold">Local SEO & Google Maps</span>
                    </label>
                    <label class="flex items-center gap-3 p-2.5 rounded-lg border border-white/5 bg-matte-black hover:border-sharp-purple/50 cursor-pointer transition-colors hover-target">
                        <input type="checkbox" value="Branding & Logo Design" class="wa-service-cb w-4 h-4 accent-sharp-purple rounded focus:ring-sharp-purple">
                        <span class="text-sm text-lavender/90 font-bold">Branding & Logo Design</span>
                    </label>
                    <label class="flex items-center gap-3 p-2.5 rounded-lg border border-white/5 bg-matte-black hover:border-sharp-purple/50 cursor-pointer transition-colors hover-target">
                        <input type="checkbox" value="Business Automation" class="wa-service-cb w-4 h-4 accent-sharp-purple rounded focus:ring-sharp-purple">
                        <span class="text-sm text-lavender/90 font-bold">Business Automation</span>
                    </label>
                    <label class="flex items-center gap-3 p-2.5 rounded-lg border border-white/5 bg-matte-black hover:border-sharp-purple/50 cursor-pointer transition-colors hover-target">
                        <input type="checkbox" value="CAC Registration" class="wa-service-cb w-4 h-4 accent-sharp-purple rounded focus:ring-sharp-purple">
                        <span class="text-sm text-lavender/90 font-bold">CAC Registration</span>
                    </label>
                </div>
                <button onclick="sendWaWidget()" class="w-full bg-[#25D366] text-white font-bold py-3.5 rounded-xl hover:bg-[#1ebe5d] transition-all hover-target shadow-lg shadow-[#25D366]/20 flex items-center justify-center gap-2 uppercase tracking-wide text-xs">
                    <i data-lucide="message-circle" class="w-4 h-4"></i> Continue to WhatsApp
                </button>
            </div>
        </div>

        <!-- Floating Button Trigger -->
        <button id="wa-float-btn" onclick="toggleWaWidget()" class="w-14 h-14 bg-[#25D366] rounded-full flex items-center justify-center text-white shadow-[0_4px_20px_rgba(37,211,102,0.4)] hover:bg-[#1ebe5d] hover:scale-105 transition-all focus:outline-none hover-target pointer-events-auto">
            <i data-lucide="message-circle" class="w-6 h-6"></i>
        </button>
    </div>

    <!-- Navigation -->
    <nav class="fixed top-0 w-full z-40 px-4 md:px-6 py-4 md:py-6 flex justify-between items-center mix-blend-difference text-lavender" style="pointer-events: none;">
        <a href="https://getonlinestudio.com" style="pointer-events: auto;" class="font-syne font-bold text-xl md:text-2xl hover:text-sharp-purple transition-colors hover-target">GO.</a>
        <a href="#consultation" style="pointer-events: auto;" class="text-xs md:text-sm font-bold tracking-widest uppercase border border-lavender px-4 md:px-6 py-2 rounded-full hover:bg-lavender hover:text-matte-black transition-all duration-300 bg-matte-black/20 backdrop-blur-sm hover-target">
            Start Project
        </a>
    </nav>

    <!-- SECTION 1: CITY HERO -->
    <header class="relative min-h-[85vh] flex flex-col justify-center items-center px-4 overflow-hidden border-b border-lavender/10 pt-32 pb-24 md:pb-32">
        <div class="perspective-grid"></div>
        <div class="absolute w-32 h-32 rounded-full border border-sharp-purple/20 top-[15%] left-[10%] animate-float" style="animation-delay: 0s;"></div>
        <div class="absolute w-40 h-40 border-dashed border-sharp-purple/30 rounded-full bottom-[20%] right-[10%] animate-float" style="animation-delay: 3s;"></div>

        <div class="absolute top-24 left-4 md:left-8 z-30 reveal-up" style="animation-delay: 0.1s;">
            <nav aria-label="Breadcrumb">
                <ol class="flex flex-wrap items-center gap-2 text-[10px] md:text-xs font-mono uppercase tracking-widest text-lavender/40">
                    <li><a href="/" class="hover:text-sharp-purple transition-colors hover-target">Home</a></li>
                    <li><span class="opacity-30">/</span></li>
                    <li><a href="/locations/" class="hover:text-sharp-purple transition-colors hover-target">Locations</a></li>
                    <li><span class="opacity-30">/</span></li>
                    <li><span class="text-lavender/70 truncate max-w-[120px] sm:max-w-none" aria-current="page"><?= go_safe_text($city_name) ?></span></li>
                </ol>
            </nav>
        </div>

        <div class="relative z-20 text-center max-w-5xl mx-auto mt-10 md:mt-0">
            <div class="flex items-center justify-center mb-6 md:mb-8 reveal-up">
                <div class="inline-flex items-center gap-3 font-mono text-code-green uppercase tracking-[0.2em] text-[10px] md:text-xs font-bold bg-code-green/10 px-4 py-2 rounded-full border border-code-green/20 backdrop-blur-sm">
                    <span class="relative flex h-2 w-2"><span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-code-green opacity-75"></span><span class="relative inline-flex rounded-full h-2 w-2 bg-code-green"></span></span>
                    <span>Local Agency &middot; <?= go_safe_text($city_name) ?></span>
                </div>
            </div>

            <!-- H1 rotates between "Web Designer" and "Web Design Company" per city for keyword diversity -->
            <?php
            $h1_variants = [
                "Web Designer in <br><span class=\"text-transparent text-stroke hover:text-sharp-purple transition-colors duration-300 cursor-default\">{$city_name}</span>",
                "Web Design Company in <br><span class=\"text-transparent text-stroke hover:text-sharp-purple transition-colors duration-300 cursor-default\">{$city_name}</span>",
                "Web Designer in <br><span class=\"text-transparent text-stroke hover:text-sharp-purple transition-colors duration-300 cursor-default\">{$city_name}</span>",
                "Website Designer in <br><span class=\"text-transparent text-stroke hover:text-sharp-purple transition-colors duration-300 cursor-default\">{$city_name}</span>",
                "Web Design & Development<br><span class=\"text-transparent text-stroke hover:text-sharp-purple transition-colors duration-300 cursor-default\">in {$city_name}</span>",
            ];
            $h1_seed = abs(crc32($city_slug . 'h1'));
            $h1_text = $h1_variants[$h1_seed % count($h1_variants)];
            ?>
            <h1 class="font-syne text-[8vw] md:text-[6vw] leading-[1.1] font-bold text-lavender reveal-up" style="animation-delay: 0.2s;">
                <?= $h1_text ?>
            </h1>

            <!-- Company signal: immediately clarifies we are a registered agency, not a freelancer -->
            <p class="font-mono text-[11px] text-lavender/40 tracking-widest uppercase mt-5 reveal-up" style="animation-delay: 0.3s;">
                Web Design Company &middot; Web Developer &middot; Digital Agency — <?= go_safe_text($city_name) ?>, Nigeria
            </p>

            <p class="font-manrope text-lavender/70 text-lg md:text-xl max-w-2xl mx-auto mt-6 leading-relaxed reveal-up" style="animation-delay: 0.4s;">
                <?= go_spin_text("{We partner with|Our agency helps|We engineer platforms for} {ambitious brands|industry leaders|forward-thinking businesses} in {$city_name} to {dominate the local market|build high-converting digital ecosystems|scale their operations online}.") ?>
            </p>

            <div class="mt-12 flex flex-col sm:flex-row items-center justify-center gap-4 reveal-up" style="animation-delay: 0.6s;">
                <a href="#silo-grid" class="w-full sm:w-auto bg-sharp-purple text-white px-10 py-4 rounded-full text-sm font-bold uppercase tracking-widest hover:bg-white hover:text-matte-black transition-all hover-target shadow-[0_0_30px_rgba(126,34,206,0.3)]">
                    Explore Industries
                </a>
                <a href="#services" class="w-full sm:w-auto border border-lavender/30 text-lavender px-10 py-4 rounded-full text-sm font-bold uppercase tracking-widest hover:bg-lavender hover:text-matte-black transition-all hover-target">
                    View Services
                </a>
            </div>
        </div>
    </header>

    <!-- SECTION 1.4: TRUST STATS BAR — E-E-A-T SIGNAL -->
    <section class="py-10 px-4 md:px-6 bg-[#0d0d0d] border-b border-lavender/5 relative z-10">
        <div class="max-w-5xl mx-auto">
            <div class="grid grid-cols-2 md:grid-cols-4 gap-6 text-center">
                <div>
                    <div class="font-syne text-3xl md:text-4xl font-bold text-white">9+</div>
                    <div class="font-mono text-[10px] text-sharp-purple uppercase tracking-widest mt-1">Years of Experience</div>
                </div>
                <div>
                    <div class="font-syne text-3xl md:text-4xl font-bold text-white">200+</div>
                    <div class="font-mono text-[10px] text-lavender/50 uppercase tracking-widest mt-1">Projects Delivered</div>
                </div>
                <div>
                    <div class="font-syne text-3xl md:text-4xl font-bold text-white">17</div>
                    <div class="font-mono text-[10px] text-lavender/50 uppercase tracking-widest mt-1">Nigerian Cities Served</div>
                </div>
                <div>
                    <div class="font-syne text-3xl md:text-4xl font-bold text-white">100%</div>
                    <div class="font-mono text-[10px] text-lavender/50 uppercase tracking-widest mt-1">Client Satisfaction Rate</div>
                </div>
            </div>
        </div>
    </section>

    <!-- SECTION 1.5: THE PUNCHY LOCAL INTRO -->
    <?php if (!empty($city_intro)): ?>
    <section class="py-16 md:py-24 px-4 md:px-6 bg-[#0d0d0d] relative z-10 border-b border-lavender/5">
        <div class="max-w-4xl mx-auto text-center">
            <i data-lucide="map-pin" class="w-8 h-8 text-sharp-purple mx-auto mb-6 opacity-80"></i>
            <p class="font-syne text-xl md:text-3xl text-lavender/90 leading-relaxed font-bold">
                <?= go_spin_text($city_intro) ?>
            </p>
        </div>
    </section>
    <?php endif; ?>

    <!-- SECTION 1.7: KEYWORD VARIATION COVERAGE -->
    <section class="py-12 px-4 md:px-6 bg-[#0a0a0a] border-b border-lavender/5 relative z-10">
        <div class="max-w-7xl mx-auto">
            <p class="font-mono text-[10px] text-sharp-purple tracking-widest uppercase mb-3 text-center">
                [ Our Services in <?= go_safe_text($city_name) ?> ]
            </p>
            <h2 class="font-syne text-xl md:text-2xl font-bold text-white text-center mb-8">
                Web Design Services in <?= go_safe_text($city_name) ?> — From Design to Development
            </h2>
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-4">
                <?php
                $kw_variants = [
                    ['term' => "Web Designer in {$city_name}",          'icon' => 'pen-tool',     'desc' => "Custom-crafted websites built to reflect your brand and convert visitors into paying clients."],
                    ['term' => "Web Developer in {$city_name}",         'icon' => 'code-2',       'desc' => "Full-stack development for portals, web apps, and high-performance digital platforms."],
                    ['term' => "Web Design Services in {$city_name}",   'icon' => 'layers',       'desc' => "End-to-end web design services: strategy, UI design, development, SEO, and launch."],
                    ['term' => "Web Design Agency in {$city_name}",     'icon' => 'building-2',   'desc' => "A full-service studio combining strategy, design, development, and ongoing support."],
                    ['term' => "Web Design Company in {$city_name}",    'icon' => 'briefcase',    'desc' => "A registered Nigerian company delivering enterprise-grade websites at competitive rates."],
                ];
                foreach ($kw_variants as $kv): ?>
                <div class="bg-card-dark border border-lavender/10 rounded-2xl p-6 hover:border-sharp-purple/40 transition-colors">
                    <i data-lucide="<?= esc_attr($kv['icon']) ?>" class="w-5 h-5 text-sharp-purple mb-4"></i>
                    <h3 class="font-syne text-sm font-bold text-white mb-2"><?= go_safe_text($kv['term']) ?></h3>
                    <p class="text-xs text-lavender/50 leading-relaxed font-manrope"><?= go_safe_text($kv['desc']) ?></p>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
    </section>

    <!-- SECTION 2: THE SILO GRID (SEO MAGIC) -->
    <section id="silo-grid" class="pt-24 md:pt-32 pb-12 px-4 md:px-6 bg-[#0a0a0a] relative z-10">
        <div class="max-w-7xl mx-auto">
            <div class="text-center max-w-3xl mx-auto mb-12">
                <h2 class="font-syne text-3xl md:text-5xl font-bold mb-6">Industries We Serve in <?= go_safe_text($city_name) ?>.</h2>
                <p class="font-manrope text-lavender/60 text-lg">Select your industry and service type to see how we build specialized digital platforms for your exact business model.</p>
            </div>

            <!-- Service Format Tabs -->
            <div class="flex flex-wrap justify-center gap-3 mb-12" id="format-tabs" role="tablist">
                <?php $first = true; foreach($service_formats as $fmt_slug => $fmt_name): ?>
                <button role="tab" aria-selected="<?= $first ? 'true' : 'false' ?>" aria-controls="grid-<?= $fmt_slug ?>" onclick="switchFormat('<?= $fmt_slug ?>')" id="tab-<?= $fmt_slug ?>" class="format-tab font-mono text-xs uppercase tracking-widest px-5 py-2.5 min-h-[44px] rounded-full border transition-all duration-300 hover-target <?= $first ? 'bg-sharp-purple border-sharp-purple text-white' : 'border-lavender/20 text-lavender/50 hover:border-lavender/50 hover:text-lavender' ?>">
                    <?= go_safe_text($fmt_name) ?>
                </button>
                <?php $first = false; endforeach; ?>
            </div>

            <!-- Niche Grid -->
            <?php foreach($service_formats as $fmt_slug => $fmt_name): ?>
            <div id="grid-<?= $fmt_slug ?>" role="tabpanel" class="format-grid <?= $fmt_slug !== 'website-designer' ? 'hidden' : '' ?>">
                <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4 md:gap-5">
                    <?php foreach($master_niches as $niche_slug => $data):
                        $link_url = "/locations/{$city_slug}/{$niche_slug}-{$fmt_slug}/";
                    ?>
                    <a href="<?= esc_url($link_url) ?>" class="group block bg-matte-black border border-lavender/10 p-6 rounded-2xl hover:border-sharp-purple/50 hover:bg-[#111] transition-all duration-300 hover-target md:hover:-translate-y-1 active:scale-[0.98]">
                        <div class="w-12 h-12 rounded-xl bg-sharp-purple/10 flex items-center justify-center mb-5 group-hover:bg-sharp-purple transition-colors duration-300">
                            <i data-lucide="<?= esc_attr($data['icon']) ?>" class="w-5 h-5 text-sharp-purple group-hover:text-white transition-colors duration-300"></i>
                        </div>
                        <h3 class="font-syne text-base font-bold text-white mb-1"><?= go_safe_text($data['name']) ?></h3>
                        <p class="text-[10px] text-lavender/40 group-hover:text-sharp-purple transition-colors font-mono uppercase tracking-widest"><?= go_safe_text($fmt_name) ?></p>
                    </a>
                    <?php endforeach; ?>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </section>

    <!-- SECTION 2.5: INTERACTIVE WHATSAPP FUNNEL -->
    <section class="pb-24 md:pb-32 px-4 md:px-6 bg-[#0a0a0a] border-b border-lavender/5 relative z-10">
        <div class="max-w-4xl mx-auto bg-card-dark border border-white/10 rounded-3xl p-8 md:p-12 shadow-2xl relative overflow-hidden">
            <div class="absolute top-0 right-0 w-64 h-64 bg-sharp-purple/10 rounded-full blur-[80px] pointer-events-none"></div>

            <!-- Step 1: Industry Input -->
            <div id="wa-step-1" class="relative z-10 transition-all duration-500">
                <h3 class="font-syne text-2xl md:text-3xl font-bold text-white mb-3">Don't see your industry?</h3>
                <p class="font-manrope text-lavender/60 mb-6">We build platforms for hundreds of specialized sectors in <?= go_safe_text($city_name) ?>. Tell us what you do.</p>
                <div class="flex flex-col sm:flex-row gap-4">
                    <input type="text" id="wa-industry-input" placeholder="e.g. Healthcare, Fashion, Logistics..." class="w-full bg-matte-black border border-white/10 rounded-xl p-4 text-white focus:border-sharp-purple outline-none transition-colors hover-target">
                    <button onclick="nextWaStep(2)" class="w-full sm:w-auto px-8 py-4 bg-white text-matte-black font-bold rounded-xl hover:bg-sharp-purple hover:text-white transition-colors whitespace-nowrap hover-target">Next &rarr;</button>
                </div>
            </div>

            <!-- Step 2: Goal Selection -->
            <div id="wa-step-2" class="relative z-10 hidden opacity-0 transition-all duration-500">
                <h3 class="font-syne text-2xl font-bold text-white mb-3">What's your primary goal?</h3>
                <p class="font-manrope text-lavender/60 mb-6">Select the main focus for your <span id="wa-display-industry" class="text-sharp-purple font-bold"></span> business.</p>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 mb-6">
                    <button onclick="selectWaGoal('need a brand new website')" class="wa-goal-btn p-4 border border-white/10 bg-matte-black rounded-xl text-left text-sm text-lavender/80 hover:border-sharp-purple hover:text-white transition-all hover-target flex items-center gap-3"><i data-lucide="rocket" class="w-4 h-4 text-sharp-purple"></i> Need a brand new website</button>
                    <button onclick="selectWaGoal('need to redesign an outdated website')" class="wa-goal-btn p-4 border border-white/10 bg-matte-black rounded-xl text-left text-sm text-lavender/80 hover:border-sharp-purple hover:text-white transition-all hover-target flex items-center gap-3"><i data-lucide="refresh-cw" class="w-4 h-4 text-blue-400"></i> Redesign an outdated website</button>
                    <button onclick="selectWaGoal('need custom software or a web portal')" class="wa-goal-btn p-4 border border-white/10 bg-matte-black rounded-xl text-left text-sm text-lavender/80 hover:border-sharp-purple hover:text-white transition-all hover-target flex items-center gap-3"><i data-lucide="code" class="w-4 h-4 text-code-green"></i> Need custom software / portal</button>
                    <button onclick="selectWaGoal('need help with Local SEO & Lead Generation')" class="wa-goal-btn p-4 border border-white/10 bg-matte-black rounded-xl text-left text-sm text-lavender/80 hover:border-sharp-purple hover:text-white transition-all hover-target flex items-center gap-3"><i data-lucide="trending-up" class="w-4 h-4 text-yellow-400"></i> Local SEO & Lead Generation</button>
                    <button onclick="selectWaGoal('have a custom digital project to discuss')" class="wa-goal-btn sm:col-span-2 p-4 border border-white/10 bg-matte-black rounded-xl text-center text-sm text-lavender/80 hover:border-sharp-purple hover:text-white transition-all hover-target flex items-center justify-center gap-3"><i data-lucide="more-horizontal" class="w-4 h-4 text-lavender"></i> Something else / Custom request</button>
                </div>
            </div>

            <!-- Step 3: Action -->
            <div id="wa-step-3" class="relative z-10 hidden opacity-0 transition-all duration-500 text-center py-4">
                <div class="w-16 h-16 bg-success-green/20 rounded-full flex items-center justify-center mx-auto mb-4">
                    <i data-lucide="check" class="w-8 h-8 text-code-green"></i>
                </div>
                <h3 class="font-syne text-2xl font-bold text-white mb-3">Perfect. Let's make it happen.</h3>
                <p class="font-manrope text-lavender/60 mb-8 max-w-lg mx-auto">We're ready to discuss your project. Your answers have been prefilled into WhatsApp for a quick start.</p>
                <a href="#" id="wa-final-btn" target="_blank" class="inline-flex items-center justify-center gap-3 bg-[#25D366] text-white font-syne font-bold uppercase tracking-widest text-sm px-10 py-5 rounded-full hover:bg-[#1ebe5d] transition-all shadow-[0_0_30px_rgba(37,211,102,0.3)] hover:-translate-y-1 hover-target">
                    <i data-lucide="message-circle" class="w-5 h-5"></i> Chat with our Team
                </a>
            </div>
        </div>
    </section>

    <!-- SECTION 2B: LOCAL LISTICLES SILO -->
    <?php if (!empty($city_listicles)): ?>
    <section class="py-24 md:py-32 px-4 md:px-6 bg-card-dark border-t border-lavender/10 relative z-10">
        <div class="max-w-7xl mx-auto">
            <div class="text-center max-w-3xl mx-auto mb-12">
                <h2 class="font-syne text-3xl md:text-5xl font-bold mb-6">Local Rankings & Reviews</h2>
                <p class="font-manrope text-lavender/60 text-lg">Read our editorial reviews of the top digital agencies across different industries in <?= go_safe_text($city_name) ?>.</p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6" id="listicle-grid">
                <?php
                $listicle_count = 0;
                foreach($city_listicles as $listicle):
                    $listicle_count++;
                    $dynamic_keyword = preg_replace('/\b20\d{2}\b/', $current_year, $listicle->target_keyword);
                    $hidden_class = $listicle_count > 6 ? 'hidden extra-listicle' : '';
                ?>
                <a href="/locations/<?= esc_attr($city_slug) ?>/top-<?= esc_attr($listicle->niche_slug) ?>-web-designers/" class="block bg-matte-black border border-lavender/10 p-8 rounded-3xl hover:border-sharp-purple/50 transition-all duration-300 md:hover:-translate-y-1 active:scale-[0.98] group hover-target <?= $hidden_class ?>">
                    <div class="flex items-start justify-between mb-6">
                        <div class="w-12 h-12 rounded-full bg-sharp-purple/10 flex items-center justify-center group-hover:bg-sharp-purple transition-colors">
                            <i data-lucide="award" class="w-5 h-5 text-sharp-purple group-hover:text-white transition-colors"></i>
                        </div>
                        <i data-lucide="arrow-up-right" class="w-5 h-5 text-lavender/30 group-hover:text-sharp-purple transition-colors"></i>
                    </div>
                    <h3 class="font-syne text-xl font-bold text-white mb-3"><?= go_safe_text($dynamic_keyword) ?></h3>
                    <p class="text-sm text-lavender/50 font-manrope">Read the <?= $current_year ?> guide &rarr;</p>
                </a>
                <?php
                endforeach;
                ?>
            </div>

            <?php if ($listicle_count > 6): ?>
            <div class="mt-12 text-center">
                <button id="show-more-listicles" class="inline-flex items-center gap-2 font-syne text-sm font-bold tracking-widest uppercase text-lavender hover:text-sharp-purple transition-colors hover-target border border-lavender/20 px-8 py-4 rounded-full hover:border-sharp-purple/50 focus:outline-none">
                    View All <?= $listicle_count ?> Guides <i data-lucide="chevron-down" class="w-4 h-4"></i>
                </button>
            </div>
            <?php endif; ?>
        </div>
    </section>
    <?php endif; ?>

    <!-- HYPER-LOCAL NEIGHBORHOODS -->
    <?php if (!empty($neighborhood_links)): ?>
    <section class="py-20 px-4 md:px-6 bg-[#0a0a0a] border-t border-lavender/10 relative z-10">
        <div class="max-w-6xl mx-auto text-center">
            <p class="font-mono text-[10px] text-sharp-purple tracking-widest uppercase mb-4">[ Hyper-Local Coverage ]</p>
            <h3 class="font-syne text-2xl md:text-3xl text-white font-bold mb-4">Premium Service Areas in <?= go_safe_text($city_name) ?></h3>
            <p class="font-manrope text-lavender/50 text-base mb-10 max-w-2xl mx-auto leading-relaxed">
                <?= go_spin_text("{We engineer|We deploy|We deliver} {high-converting|strategic|performance-driven} digital platforms for businesses, corporate institutions, and SMEs across {$city_name}'s {most prominent|premium|key commercial} districts.") ?>
            </p>

            <div class="flex flex-wrap justify-center gap-3">
                <?php foreach ($neighborhood_links as $link): ?>
                <a href="<?= esc_url($link['url']) ?>" class="inline-block px-5 py-2.5 bg-card-dark border border-white/10 rounded-full text-xs font-bold text-lavender/70 hover:text-white hover:border-sharp-purple hover:bg-sharp-purple/10 md:hover:-translate-y-1 active:scale-[0.98] transition-all hover-target shadow-lg shadow-black/50">
                    <?= go_safe_text($link['name']) ?>
                </a>
                <?php endforeach; ?>
            </div>
        </div>
    </section>
    <?php endif; ?>

    <!-- SECTION 3A: PRICING INTELLIGENCE -->
    <section class="py-24 md:py-32 px-4 md:px-6 bg-matte-black border-t border-lavender/10 relative z-10">
        <div class="max-w-7xl mx-auto">

            <div class="text-center max-w-3xl mx-auto mb-16">
                <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-sharp-purple/10 border border-sharp-purple/30 text-sharp-purple text-xs font-bold uppercase tracking-wider mb-6">
                    <i data-lucide="bar-chart-2" class="w-4 h-4"></i> Market Intelligence
                </div>
                <h2 class="font-syne text-3xl md:text-5xl font-bold text-white mb-4">
                    Web Design Pricing in <?= go_safe_text($city_name) ?> (<?= $current_year ?>)
                </h2>
                <p class="font-manrope text-lavender/60 text-lg">
                    Based on our project history and market analysis across <?= go_safe_text($city_name) ?>, here is what local businesses typically invest in professional web design and development services.
                </p>
            </div>

            <!-- The Digital Gap Stat -->
            <div class="bg-card-dark border border-lavender/10 rounded-3xl p-8 md:p-10 mb-12 flex flex-col md:flex-row items-center gap-8 md:gap-16">
                <div class="text-center md:text-left flex-shrink-0">
                    <div class="font-syne text-6xl md:text-8xl font-bold text-sharp-purple">
                        <?= $city_digital_gap ?>%
                    </div>
                    <p class="font-manrope text-lavender/60 text-sm mt-2">
                        of <?= go_safe_text($city_name) ?> businesses<br>have no professional website
                    </p>
                </div>
                <div class="w-full">
                    <div class="h-3 bg-white/5 rounded-full overflow-hidden mb-4">
                        <div class="h-full bg-gradient-to-r from-sharp-purple to-lavender rounded-full" style="width: <?= $city_digital_gap ?>%"></div>
                    </div>
                    <p class="font-manrope text-lavender/70 text-sm leading-relaxed">
                        That means the majority of your <?= go_safe_text($city_name) ?> competitors are completely invisible on Google. Every day without a professional platform is market share handed to someone else.
                    </p>
                </div>
            </div>

            <!-- 3-Tier Pricing Cards -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <?php
                $price_tiers = [
                    [
                        'label'   => 'Starter',
                        'tag'     => 'Business Essentials',
                        'price'   => '₦' . number_format($city_min_price) . ' to ₦' . number_format((int) round($city_min_price * 1.6 / 5000) * 5000),
                        'note'    => 'For new businesses, SMEs, and service providers who need a clean, fast, professional presence online.',
                        'days'    => '7 to 10 days',
                        'popular' => false,
                    ],
                    [
                        'label'   => 'Professional',
                        'tag'     => 'Most Popular',
                        'price'   => '₦' . number_format((int) round($city_avg_price * 0.85 / 5000) * 5000) . ' to ₦' . number_format((int) round($city_avg_price * 1.3 / 5000) * 5000),
                        'note'    => 'Full-featured platforms for law firms, hospitals, schools, hotels, and established businesses with real growth targets.',
                        'days'    => '14 to 21 days',
                        'popular' => true,
                    ],
                    [
                        'label'   => 'Enterprise',
                        'tag'     => 'Custom Systems',
                        'price'   => '₦' . number_format((int) round($city_max_price * 0.8 / 5000) * 5000) . ' +',
                        'note'    => 'Custom web applications, multi-location portals, e-commerce systems, and proprietary digital infrastructure.',
                        'days'    => '30+ days',
                        'popular' => false,
                    ],
                ];
                foreach ($price_tiers as $tier): ?>
                <div class="relative bg-card-dark border <?= $tier['popular'] ? 'border-sharp-purple' : 'border-lavender/10' ?> rounded-3xl p-8 hover:border-sharp-purple/50 transition-colors">
                    <?php if ($tier['popular']): ?>
                    <div class="absolute -top-3 left-1/2 -translate-x-1/2 bg-sharp-purple text-white text-[10px] font-bold uppercase tracking-widest px-4 py-1 rounded-full whitespace-nowrap">
                        Most Popular
                    </div>
                    <?php endif; ?>
                    <p class="font-mono text-[10px] text-sharp-purple uppercase tracking-widest mb-2"><?= go_safe_text($tier['tag']) ?></p>
                    <h3 class="font-syne text-2xl font-bold text-white mb-1"><?= go_safe_text($tier['label']) ?></h3>
                    <p class="font-syne text-xl text-lavender font-bold mb-4"><?= go_safe_text($tier['price']) ?></p>
                    <p class="font-manrope text-xs text-lavender/50 leading-relaxed mb-4"><?= go_safe_text($tier['note']) ?></p>
                    <div class="flex items-center gap-2 text-[10px] font-mono text-code-green">
                        <i data-lucide="clock" class="w-3 h-3"></i> <?= go_safe_text($tier['days']) ?> delivery
                    </div>
                </div>
                <?php endforeach; ?>
            </div>

        </div>
    </section>

    <!-- SECTION 3: CAPABILITY MARQUEE -->
    <section class="py-6 bg-sharp-purple overflow-hidden">
        <div class="marquee-container">
            <div class="marquee-content font-space font-bold text-black text-lg uppercase tracking-widest">
                <span class="mx-6">STRATEGY</span>✦<span class="mx-6">DESIGN</span>✦<span class="mx-6">DEVELOPMENT</span>✦<span class="mx-6">AUTOMATION</span>✦<span class="mx-6">SCALABILITY</span>✦<span class="mx-6">PERFORMANCE</span>✦<span class="mx-6">REVENUE</span>✦<span class="mx-6">GROWTH</span>✦
            </div>
            <div class="marquee-content font-space font-bold text-black text-lg uppercase tracking-widest">
                 <span class="mx-6">STRATEGY</span>✦<span class="mx-6">DESIGN</span>✦<span class="mx-6">DEVELOPMENT</span>✦<span class="mx-6">AUTOMATION</span>✦<span class="mx-6">SCALABILITY</span>✦<span class="mx-6">PERFORMANCE</span>✦<span class="mx-6">REVENUE</span>✦<span class="mx-6">GROWTH</span>✦
            </div>
        </div>
    </section>

    <!-- SECTION 6.5: CLIENT TESTIMONIALS -->
    <section class="py-24 md:py-32 px-4 md:px-6 bg-[#0a0a0a] border-t border-lavender/10 relative z-10 overflow-hidden">

        <!-- Subtle background glow -->
        <div class="absolute top-0 left-1/2 -translate-x-1/2 w-[600px] h-[300px] rounded-full pointer-events-none" style="background: radial-gradient(ellipse, rgba(126,34,206,0.07) 0%, transparent 70%);"></div>

        <div class="max-w-7xl mx-auto relative z-10">

            <!-- Section Header -->
            <div class="text-center max-w-3xl mx-auto mb-16 md:mb-20">
                <p class="font-mono text-[10px] text-sharp-purple tracking-widest uppercase mb-5">[ Client Testimonials ]</p>
                <h2 class="font-syne text-3xl md:text-5xl font-bold text-white leading-tight mb-6">
                    See what businesses are saying about our work.
                </h2>
                <p class="font-manrope text-lavender/60 text-lg leading-relaxed">
                    From Osogbo to Ilorin, Enugu to Abuja, and beyond Nigeria's borders.
                </p>
            </div>

            <?php
            $testimonials = [
                [
                    'quote'    => "It's not always easy working with a team when you aren't in the same location, but our experience with GetOnline Studio was top-notch. Faith made the process simple. He doesn't just build pages; he builds solutions. He turned our concept into a working prototype quickly and integrated AI features that are already helping our business. I can't recommend them enough.",
                    'name'     => 'Olayinka Itunu Damilare',
                    'role'     => 'CEO, De Kompany Consulting Services',
                    'location' => 'Ilorin, Kwara State',
                    'initials' => 'OI',
                    'color'    => '#0f6e56',
                    'tag'      => 'Business Consulting',
                ],
                [
                    'quote'    => "GetOnline Studio developed a website and mobile app for us, and we couldn't be happier with the result. They made the whole process easy and delivered exactly what we needed. Truly the best web designer in Osogbo, Osun State.",
                    'name'     => 'Mr. Mike',
                    'role'     => 'Technical Director, RaffleKings',
                    'location' => 'Nigeria',
                    'initials' => 'MK',
                    'color'    => '#185fa5',
                    'tag'      => 'Web & Mobile App',
                ],
                [
                    'quote'    => "As an international organization, the World Institute for Peace required a digital presence that reflected our authority and mission. GetOnline Studio proved to be a highly trusted partner. The speed of delivery did not compromise quality. We highly recommend them for any institution seeking world-class web development.",
                    'name'     => 'Amb. Dr. Jernail Singh Anand',
                    'role'     => 'World Foundation for Peace',
                    'location' => 'India',
                    'initials' => 'JA',
                    'color'    => '#7e22ce',
                    'tag'      => 'International Organisation',
                ],
                [
                    'quote'    => "GetOnline Studio delivered more than just a system. They transformed how we operate. They built a custom online academy where students learn at their own pace, engage with lecturers, track progress, and connect with peers.",
                    'name'     => 'Emmanuel Amaechi',
                    'role'     => 'Academic Director, Peace Academy',
                    'location' => 'Enugu Branch',
                    'initials' => 'EA',
                    'color'    => '#3b6d11',
                    'tag'      => 'Education & Academy',
                ],
                [
                    'quote'    => "I proudly commend GetOnline Studio for their exceptional quality and reliable services. Their professionalism, creativity, and attention to detail consistently set them apart. Their ability to understand clients' needs and translate them into functional, elegant digital solutions is truly admirable.",
                    'name'     => 'Chief Lamina Kamiludeen Omotoyosi',
                    'role'     => 'Executive Director, World Institute for Peace (WIP)',
                    'location' => 'Abuja',
                    'initials' => 'LK',
                    'color'    => '#854f0b',
                    'tag'      => 'Leadership & Governance',
                ],
                [
                    'quote'    => "Working with GetOnline Studio on our microfinance app was an excellent experience. He demonstrated a high level of professionalism and a clear understanding of our business needs. He consistently delivered beyond expectations. I am genuinely pleased with the outcome and highly recommend his services.",
                    'name'     => 'Tobiloba Babalola',
                    'role'     => 'Managing Director (Operations & Compliance)',
                    'location' => 'OA Global Standard Services, Osogbo',
                    'initials' => 'TB',
                    'color'    => '#993556',
                    'tag'      => 'Fintech & Microfinance',
                ],
                [
                    'quote'    => "I needed help designing a website for our church so I was referred to them. They really met our expectations and our church vision. Good work.",
                    'name'     => 'Mr. Ogundeji Sinmisola',
                    'role'     => 'Church Administrator',
                    'location' => 'Ogbomosho, Oyo State',
                    'initials' => 'OS',
                    'color'    => '#7e22ce',
                    'tag'      => 'Church & Non-profit',
                ],
            ];
            ?>

            <!-- Testimonials Swipe/Masonry Grid -->
            <div class="flex md:block overflow-x-auto md:overflow-visible snap-x snap-mandatory hide-scrollbar gap-4 pb-8 md:pb-0 md:columns-2 xl:columns-3 md:space-y-6">
                <?php foreach ($testimonials as $i => $t): ?>
                <div class="snap-center min-w-[85vw] sm:min-w-[350px] md:min-w-0 break-inside-avoid bg-matte-black border border-lavender/10 rounded-2xl p-7 md:p-8 hover:border-sharp-purple/40 transition-all duration-300 flex flex-col gap-5 group" style="--accent: <?= esc_attr($t['color']) ?>;">

                    <!-- Stars + Tag row -->
                    <div class="flex items-center justify-between gap-3 flex-wrap">
                        <span class="text-yellow-400 text-sm tracking-tight" aria-label="5 stars">★★★★★</span>
                        <span class="font-mono text-[9px] uppercase tracking-widest text-lavender/30 border border-lavender/10 px-2 py-1 rounded-full"><?= esc_html($t['tag']) ?></span>
                    </div>

                    <!-- Quote -->
                    <blockquote class="font-manrope text-sm text-lavender/80 leading-relaxed flex-1">
                        <span class="text-3xl text-sharp-purple/40 font-serif leading-none mr-1 align-bottom">&ldquo;</span><?= esc_html($t['quote']) ?>
                    </blockquote>

                    <!-- Reviewer -->
                    <div class="flex items-center gap-4 pt-4 border-t border-lavender/5 mt-auto">
                        <!-- Avatar initials -->
                        <div class="w-10 h-10 rounded-full flex-shrink-0 flex items-center justify-center font-syne font-bold text-xs text-white" style="background-color: <?= esc_attr($t['color']) ?>; opacity: 0.9;">
                            <?= esc_html($t['initials']) ?>
                        </div>
                        <div>
                            <p class="font-syne font-bold text-sm text-white leading-tight"><?= esc_html($t['name']) ?></p>
                            <p class="font-manrope text-xs text-lavender/50 leading-snug mt-0.5"><?= esc_html($t['role']) ?></p>
                            <p class="font-mono text-[9px] text-lavender/30 uppercase tracking-widest mt-1"><?= esc_html($t['location']) ?></p>
                        </div>
                    </div>

                </div>
                <?php endforeach; ?>
            </div>

            <!-- Bottom CTA strip -->
            <div class="mt-10 md:mt-16 text-center">
                <p class="font-manrope text-lavender/50 text-sm mb-6">Join these businesses, get your free discovery call today.</p>
                <button onclick="toggleWaWidget()" class="inline-flex items-center gap-3 bg-sharp-purple text-white px-10 py-4 rounded-full text-sm font-bold uppercase tracking-widest hover:bg-white hover:text-matte-black transition-all hover-target shadow-[0_0_30px_rgba(126,34,206,0.3)] focus:outline-none">
                    <i data-lucide="message-circle" class="w-4 h-4"></i> Start Your Project
                </button>
            </div>

        </div>
    </section>
    <!-- END TESTIMONIALS -->

    <!-- SECTION 4: HOW WE HELP YOU GROW (ANTI-LEAKAGE STRATEGY) -->
    <!-- Buttons now open the WhatsApp widget instead of leaving the page -->
    <section id="services" class="relative py-24 md:py-32 overflow-hidden z-20 bg-matte-black border-t border-white/5">
        <div class="max-w-7xl mx-auto w-full px-4 md:px-6">

            <div class="mb-6 reveal-up">
                <p class="font-mono text-[10px] text-sharp-purple tracking-widest uppercase mb-4">[ What We Do ]</p>
                <h2 class="font-syne text-3xl md:text-5xl font-bold mb-4 text-white">
                    Web Design & Development Services<br class="hidden md:block"> in <?= go_safe_text($city_name) ?>.
                </h2>
                <p class="font-manrope text-lavender/60 text-lg max-w-2xl mt-4 leading-relaxed">
                    GetOnline Studio is a <strong class="text-white">web design company</strong> — not a freelance web designer. We are a registered Nigerian agency with a full team of web designers, web developers, and digital strategists helping businesses in <?= go_safe_text($city_name) ?> get found, look credible, and grow online.
                </p>
            </div>

            <!-- Service Cards Grid -->
            <div class="mt-16 grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">

                <!-- 01 -->
                <button onclick="openWaWidgetWithService('Website Design')" class="w-full text-left group bg-card-dark border border-lavender/10 rounded-2xl p-8 hover:border-sharp-purple/50 hover:bg-[#111] transition-all duration-300 hover:-translate-y-1 hover-target flex flex-col gap-5 focus:outline-none">
                    <div class="flex items-start justify-between w-full">
                        <div class="w-12 h-12 rounded-xl bg-sharp-purple/10 flex items-center justify-center group-hover:bg-sharp-purple transition-colors duration-300">
                            <i data-lucide="monitor" class="w-5 h-5 text-sharp-purple group-hover:text-white transition-colors"></i>
                        </div>
                        <span class="font-mono text-xs text-lavender/20 group-hover:text-sharp-purple transition-colors">01</span>
                    </div>
                    <div>
                        <h3 class="font-syne text-xl font-bold text-white mb-2 group-hover:text-sharp-purple transition-colors">Professional Website Design</h3>
                        <p class="font-manrope text-sm text-lavender/60 leading-relaxed">Your website is the first thing potential customers see. Our expert designers build websites that look credible, load fast, and make people want to contact you, not scroll past.</p>
                    </div>
                    <div class="mt-auto flex flex-wrap gap-2">
                        <span class="text-[10px] font-mono uppercase tracking-widest text-sharp-purple border border-sharp-purple/20 px-2 py-1 rounded-full">More Customers</span>
                        <span class="text-[10px] font-mono uppercase tracking-widest text-lavender/40 border border-lavender/10 px-2 py-1 rounded-full">Trust & Credibility</span>
                    </div>
                </button>

                <!-- 02 -->
                <button onclick="openWaWidgetWithService('Web Development & Portals')" class="w-full text-left group bg-card-dark border border-lavender/10 rounded-2xl p-8 hover:border-sharp-purple/50 hover:bg-[#111] transition-all duration-300 hover:-translate-y-1 hover-target flex flex-col gap-5 focus:outline-none">
                    <div class="flex items-start justify-between w-full">
                        <div class="w-12 h-12 rounded-xl bg-sharp-purple/10 flex items-center justify-center group-hover:bg-sharp-purple transition-colors duration-300">
                            <i data-lucide="code-2" class="w-5 h-5 text-sharp-purple group-hover:text-white transition-colors"></i>
                        </div>
                        <span class="font-mono text-xs text-lavender/20 group-hover:text-sharp-purple transition-colors">02</span>
                    </div>
                    <div>
                        <h3 class="font-syne text-xl font-bold text-white mb-2 group-hover:text-sharp-purple transition-colors">Web Development & Web Apps</h3>
                        <p class="font-manrope text-sm text-lavender/60 leading-relaxed">Need something more powerful? Our professional developers build custom portals, booking systems, e-commerce stores, and web applications tailored exactly to how your business works.</p>
                    </div>
                    <div class="mt-auto flex flex-wrap gap-2">
                        <span class="text-[10px] font-mono uppercase tracking-widest text-sharp-purple border border-sharp-purple/20 px-2 py-1 rounded-full">Custom Solutions</span>
                        <span class="text-[10px] font-mono uppercase tracking-widest text-lavender/40 border border-lavender/10 px-2 py-1 rounded-full">E-Commerce</span>
                    </div>
                </button>

                <!-- 03 -->
                <button onclick="openWaWidgetWithService('SEO & Google Ranking')" class="w-full text-left group bg-card-dark border border-lavender/10 rounded-2xl p-8 hover:border-sharp-purple/50 hover:bg-[#111] transition-all duration-300 hover:-translate-y-1 hover-target flex flex-col gap-5 focus:outline-none">
                    <div class="flex items-start justify-between w-full">
                        <div class="w-12 h-12 rounded-xl bg-sharp-purple/10 flex items-center justify-center group-hover:bg-sharp-purple transition-colors duration-300">
                            <i data-lucide="search" class="w-5 h-5 text-sharp-purple group-hover:text-white transition-colors"></i>
                        </div>
                        <span class="font-mono text-xs text-lavender/20 group-hover:text-sharp-purple transition-colors">03</span>
                    </div>
                    <div>
                        <h3 class="font-syne text-xl font-bold text-white mb-2 group-hover:text-sharp-purple transition-colors">On-Page SEO & Google Ranking</h3>
                        <p class="font-manrope text-sm text-lavender/60 leading-relaxed">When someone in <?= go_safe_text($city_name) ?> searches for what you sell, do they find you or your competitor? We optimise your website so Google ranks you at the top, bringing in customers who are already looking for you.</p>
                    </div>
                    <div class="mt-auto flex flex-wrap gap-2">
                        <span class="text-[10px] font-mono uppercase tracking-widest text-sharp-purple border border-sharp-purple/20 px-2 py-1 rounded-full">More Sales</span>
                        <span class="text-[10px] font-mono uppercase tracking-widest text-lavender/40 border border-lavender/10 px-2 py-1 rounded-full">Google Traffic</span>
                    </div>
                </button>

                <!-- 04 -->
                <button onclick="openWaWidgetWithService('Local SEO & Google Maps')" class="w-full text-left group bg-card-dark border border-lavender/10 rounded-2xl p-8 hover:border-sharp-purple/50 hover:bg-[#111] transition-all duration-300 hover:-translate-y-1 hover-target flex flex-col gap-5 focus:outline-none">
                    <div class="flex items-start justify-between w-full">
                        <div class="w-12 h-12 rounded-xl bg-sharp-purple/10 flex items-center justify-center group-hover:bg-sharp-purple transition-colors duration-300">
                            <i data-lucide="map-pin" class="w-5 h-5 text-sharp-purple group-hover:text-white transition-colors"></i>
                        </div>
                        <span class="font-mono text-xs text-lavender/20 group-hover:text-sharp-purple transition-colors">04</span>
                    </div>
                    <div>
                        <h3 class="font-syne text-xl font-bold text-white mb-2 group-hover:text-sharp-purple transition-colors">Local SEO & Google Maps</h3>
                        <p class="font-manrope text-sm text-lavender/60 leading-relaxed">People search "near me" every single day. We set up and optimise your Google Business profile so your business shows up on Google Maps and in local search results, right when people in <?= go_safe_text($city_name) ?> are ready to buy.</p>
                    </div>
                    <div class="mt-auto flex flex-wrap gap-2">
                        <span class="text-[10px] font-mono uppercase tracking-widest text-sharp-purple border border-sharp-purple/20 px-2 py-1 rounded-full">Local Visibility</span>
                        <span class="text-[10px] font-mono uppercase tracking-widest text-lavender/40 border border-lavender/10 px-2 py-1 rounded-full">Google Maps</span>
                    </div>
                </button>

                <!-- 05 -->
                <button onclick="openWaWidgetWithService('Branding & Logo Design')" class="w-full text-left group bg-card-dark border border-lavender/10 rounded-2xl p-8 hover:border-sharp-purple/50 hover:bg-[#111] transition-all duration-300 hover:-translate-y-1 hover-target flex flex-col gap-5 focus:outline-none">
                    <div class="flex items-start justify-between w-full">
                        <div class="w-12 h-12 rounded-xl bg-sharp-purple/10 flex items-center justify-center group-hover:bg-sharp-purple transition-colors duration-300">
                            <i data-lucide="sparkles" class="w-5 h-5 text-sharp-purple group-hover:text-white transition-colors"></i>
                        </div>
                        <span class="font-mono text-xs text-lavender/20 group-hover:text-sharp-purple transition-colors">05</span>
                    </div>
                    <div>
                        <h3 class="font-syne text-xl font-bold text-white mb-2 group-hover:text-sharp-purple transition-colors">Brand Identity & Logo Design</h3>
                        <p class="font-manrope text-sm text-lavender/60 leading-relaxed">A professional brand makes people take you seriously before you even speak. We design logos, brand colours, and visual identities that make your business look established so you can charge what you are truly worth.</p>
                    </div>
                    <div class="mt-auto flex flex-wrap gap-2">
                        <span class="text-[10px] font-mono uppercase tracking-widest text-sharp-purple border border-sharp-purple/20 px-2 py-1 rounded-full">Look Established</span>
                        <span class="text-[10px] font-mono uppercase tracking-widest text-lavender/40 border border-lavender/10 px-2 py-1 rounded-full">Premium Pricing</span>
                    </div>
                </button>

                <!-- 06 -->
                <button onclick="openWaWidgetWithService('Website Design')" class="w-full text-left group bg-card-dark border border-lavender/10 rounded-2xl p-8 hover:border-sharp-purple/50 hover:bg-[#111] transition-all duration-300 hover:-translate-y-1 hover-target flex flex-col gap-5 focus:outline-none">
                    <div class="flex items-start justify-between w-full">
                        <div class="w-12 h-12 rounded-xl bg-sharp-purple/10 flex items-center justify-center group-hover:bg-sharp-purple transition-colors duration-300">
                            <i data-lucide="pen-tool" class="w-5 h-5 text-sharp-purple group-hover:text-white transition-colors"></i>
                        </div>
                        <span class="font-mono text-xs text-lavender/20 group-hover:text-sharp-purple transition-colors">06</span>
                    </div>
                    <div>
                        <h3 class="font-syne text-xl font-bold text-white mb-2 group-hover:text-sharp-purple transition-colors">Graphic Design</h3>
                        <p class="font-manrope text-sm text-lavender/60 leading-relaxed">From social media posts to flyers, business cards, and banners, our expert graphic designers create visuals that stop the scroll, represent your brand properly, and make your marketing materials work harder for you.</p>
                    </div>
                    <div class="mt-auto flex flex-wrap gap-2">
                        <span class="text-[10px] font-mono uppercase tracking-widest text-sharp-purple border border-sharp-purple/20 px-2 py-1 rounded-full">Better Marketing</span>
                        <span class="text-[10px] font-mono uppercase tracking-widest text-lavender/40 border border-lavender/10 px-2 py-1 rounded-full">Social Media</span>
                    </div>
                </button>

                <!-- 07 -->
                <button onclick="openWaWidgetWithService('Business Automation')" class="w-full text-left group bg-card-dark border border-lavender/10 rounded-2xl p-8 hover:border-sharp-purple/50 hover:bg-[#111] transition-all duration-300 hover:-translate-y-1 hover-target flex flex-col gap-5 focus:outline-none">
                    <div class="flex items-start justify-between w-full">
                        <div class="w-12 h-12 rounded-xl bg-sharp-purple/10 flex items-center justify-center group-hover:bg-sharp-purple transition-colors duration-300">
                            <i data-lucide="bot" class="w-5 h-5 text-sharp-purple group-hover:text-white transition-colors"></i>
                        </div>
                        <span class="font-mono text-xs text-lavender/20 group-hover:text-sharp-purple transition-colors">07</span>
                    </div>
                    <div>
                        <h3 class="font-syne text-xl font-bold text-white mb-2 group-hover:text-sharp-purple transition-colors">Business Automation & AI Tools</h3>
                        <p class="font-manrope text-sm text-lavender/60 leading-relaxed">Stop doing the same things manually every day. We set up smart systems that automatically respond to customer enquiries, send follow-up messages, take bookings, and keep your business running, even when you are offline.</p>
                    </div>
                    <div class="mt-auto flex flex-wrap gap-2">
                        <span class="text-[10px] font-mono uppercase tracking-widest text-sharp-purple border border-sharp-purple/20 px-2 py-1 rounded-full">Save Time</span>
                        <span class="text-[10px] font-mono uppercase tracking-widest text-lavender/40 border border-lavender/10 px-2 py-1 rounded-full">24/7 Responses</span>
                    </div>
                </button>

                <!-- 08 -->
                <button onclick="openWaWidgetWithService('CAC Registration')" class="w-full text-left group bg-card-dark border border-lavender/10 rounded-2xl p-8 hover:border-sharp-purple/50 hover:bg-[#111] transition-all duration-300 hover:-translate-y-1 hover-target flex flex-col gap-5 focus:outline-none">
                    <div class="flex items-start justify-between w-full">
                        <div class="w-12 h-12 rounded-xl bg-sharp-purple/10 flex items-center justify-center group-hover:bg-sharp-purple transition-colors duration-300">
                            <i data-lucide="landmark" class="w-5 h-5 text-sharp-purple group-hover:text-white transition-colors"></i>
                        </div>
                        <span class="font-mono text-xs text-lavender/20 group-hover:text-sharp-purple transition-colors">08</span>
                    </div>
                    <div>
                        <h3 class="font-syne text-xl font-bold text-white mb-2 group-hover:text-sharp-purple transition-colors">CAC Business Registration</h3>
                        <p class="font-manrope text-sm text-lavender/60 leading-relaxed">Register your business with the Corporate Affairs Commission (CAC) and make it official. A registered business opens the door to corporate clients, bank accounts, and contracts that informal businesses simply cannot access.</p>
                    </div>
                    <div class="mt-auto flex flex-wrap gap-2">
                        <span class="text-[10px] font-mono uppercase tracking-widest text-sharp-purple border border-sharp-purple/20 px-2 py-1 rounded-full">Look Legitimate</span>
                        <span class="text-[10px] font-mono uppercase tracking-widest text-lavender/40 border border-lavender/10 px-2 py-1 rounded-full">Corporate Clients</span>
                    </div>
                </button>

                <!-- 09 -->
                <button onclick="openWaWidgetWithService('Website Design')" class="w-full text-left group bg-card-dark border border-lavender/10 rounded-2xl p-8 hover:border-sharp-purple/50 hover:bg-[#111] transition-all duration-300 hover:-translate-y-1 hover-target flex flex-col gap-5 focus:outline-none">
                    <div class="flex items-start justify-between w-full">
                        <div class="w-12 h-12 rounded-xl bg-sharp-purple/10 flex items-center justify-center group-hover:bg-sharp-purple transition-colors duration-300">
                            <i data-lucide="share-2" class="w-5 h-5 text-sharp-purple group-hover:text-white transition-colors"></i>
                        </div>
                        <span class="font-mono text-xs text-lavender/20 group-hover:text-sharp-purple transition-colors">09</span>
                    </div>
                    <div>
                        <h3 class="font-syne text-xl font-bold text-white mb-2 group-hover:text-sharp-purple transition-colors">Social Media Setup & Strategy</h3>
                        <p class="font-manrope text-sm text-lavender/60 leading-relaxed">Having a social media page is not enough. It needs to look consistent, professional, and active. We set up your pages properly, create a content strategy, and help you build an audience that actually turns into paying customers.</p>
                    </div>
                    <div class="mt-auto flex flex-wrap gap-2">
                        <span class="text-[10px] font-mono uppercase tracking-widest text-sharp-purple border border-sharp-purple/20 px-2 py-1 rounded-full">More Followers</span>
                        <span class="text-[10px] font-mono uppercase tracking-widest text-lavender/40 border border-lavender/10 px-2 py-1 rounded-full">Brand Consistency</span>
                    </div>
                </button>

                <!-- 10 -->
                <button onclick="openWaWidgetWithService('Website Design')" class="w-full text-left group bg-card-dark border border-lavender/10 rounded-2xl p-8 hover:border-sharp-purple/50 hover:bg-[#111] transition-all duration-300 hover:-translate-y-1 hover-target flex flex-col gap-5 focus:outline-none">
                    <div class="flex items-start justify-between w-full">
                        <div class="w-12 h-12 rounded-xl bg-sharp-purple/10 flex items-center justify-center group-hover:bg-sharp-purple transition-colors duration-300">
                            <i data-lucide="shield-check" class="w-5 h-5 text-sharp-purple group-hover:text-white transition-colors"></i>
                        </div>
                        <span class="font-mono text-xs text-lavender/20 group-hover:text-sharp-purple transition-colors">10</span>
                    </div>
                    <div>
                        <h3 class="font-syne text-xl font-bold text-white mb-2 group-hover:text-sharp-purple transition-colors">Website Maintenance & Support</h3>
                        <p class="font-manrope text-sm text-lavender/60 leading-relaxed">Your website needs to stay fast, secure, and up-to-date at all times. Our professional support team handles updates, fixes, backups, and security so your business is never taken offline at the wrong moment.</p>
                    </div>
                    <div class="mt-auto flex flex-wrap gap-2">
                        <span class="text-[10px] font-mono uppercase tracking-widest text-sharp-purple border border-sharp-purple/20 px-2 py-1 rounded-full">Always Online</span>
                        <span class="text-[10px] font-mono uppercase tracking-widest text-lavender/40 border border-lavender/10 px-2 py-1 rounded-full">Security</span>
                    </div>
                </button>

            </div>

            <!-- Bottom CTA strip -->
            <div class="mt-16 border border-lavender/10 rounded-2xl p-8 md:p-10 bg-card-dark flex flex-col md:flex-row items-center justify-between gap-6">
                <div>
                    <p class="font-mono text-[10px] text-sharp-purple tracking-widest uppercase mb-2">[ Not Sure Where to Start? ]</p>
                    <h3 class="font-syne text-2xl md:text-3xl font-bold text-white">Talk to an expert. It's free.</h3>
                    <p class="font-manrope text-lavender/60 text-sm mt-2">Tell us about your business and we will tell you exactly what you need.</p>
                </div>
                <button onclick="toggleWaWidget()" class="flex-shrink-0 inline-flex items-center gap-3 bg-sharp-purple text-white px-8 py-4 rounded-full text-sm font-bold uppercase tracking-widest hover:bg-white hover:text-matte-black transition-all hover-target shadow-[0_0_30px_rgba(126,34,206,0.3)] whitespace-nowrap focus:outline-none">
                    <i data-lucide="message-circle" class="w-4 h-4"></i> Chat with Us
                </button>
            </div>

        </div>
    </section>

    <!-- SECTION 4B: HOW WE WORK — PROCESS SECTION -->
    <section class="py-20 md:py-28 px-4 md:px-6 bg-[#0a0a0a] border-t border-lavender/5 relative z-10">
        <div class="max-w-7xl mx-auto">
            <div class="text-center max-w-3xl mx-auto mb-16">
                <p class="font-mono text-[10px] text-sharp-purple tracking-widest uppercase mb-4">[ Our Process ]</p>
                <h2 class="font-syne text-3xl md:text-4xl font-bold text-white mb-4">
                    How We Build Your Website in <?= go_safe_text($city_name) ?>
                </h2>
                <p class="font-manrope text-lavender/60 text-base leading-relaxed">
                    <?= go_spin_text("No {guesswork|surprises|confusion}. Every project follows a {clear|structured|proven} process so you always know what is happening and when your platform will be live.") ?>
                </p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 relative">

                <!-- Connecting line — desktop only -->
                <div class="hidden lg:block absolute top-10 left-[12.5%] right-[12.5%] h-px bg-gradient-to-r from-transparent via-sharp-purple/30 to-transparent pointer-events-none"></div>

                <?php
                $process_steps = [
                    [
                        'number' => '01',
                        'icon'   => 'phone-call',
                        'title'  => 'Discovery Call',
                        'desc'   => go_spin_text("We start with a {free|no-obligation} 20-minute call to understand your business, your goals, and exactly what you need. No jargon. Just a real conversation about what will move your business forward in {$city_name}."),
                        'tag'    => 'Free · 20 Minutes',
                    ],
                    [
                        'number' => '02',
                        'icon'   => 'layout-template',
                        'title'  => 'Strategy & Design',
                        'desc'   => go_spin_text("We {map out|plan|design} your website structure, choose the right pages, and create a design that {matches your brand|reflects your business|represents your identity} and converts visitors into paying clients."),
                        'tag'    => 'Days 1 – 5',
                    ],
                    [
                        'number' => '03',
                        'icon'   => 'code-2',
                        'title'  => 'Build & Develop',
                        'desc'   => go_spin_text("Our developers {build|bring to life|engineer} your platform — {clean code|fast load speeds|mobile-first performance}, SEO architecture baked in from day one, and a system that {scales with your growth|is built to last|handles real traffic}."),
                        'tag'    => 'Days 5 – 14',
                    ],
                    [
                        'number' => '04',
                        'icon'   => 'rocket',
                        'title'  => 'Launch & Support',
                        'desc'   => go_spin_text("We {review everything|test across all devices|run final checks} with you before going live. After launch, we {stay available|remain on hand|provide ongoing support} to make sure your platform keeps performing."),
                        'tag'    => 'Day 14+',
                    ],
                ];
                foreach ($process_steps as $step): ?>
                <div class="relative bg-card-dark border border-lavender/10 rounded-2xl p-8 hover:border-sharp-purple/40 transition-colors flex flex-col gap-5">
                    <!-- Step number -->
                    <div class="flex items-center justify-between">
                        <div class="w-12 h-12 rounded-xl bg-sharp-purple/10 flex items-center justify-center">
                            <i data-lucide="<?= esc_attr($step['icon']) ?>" class="w-5 h-5 text-sharp-purple"></i>
                        </div>
                        <span class="font-mono text-2xl font-bold text-white/10"><?= esc_html($step['number']) ?></span>
                    </div>
                    <div>
                        <h3 class="font-syne text-lg font-bold text-white mb-2"><?= esc_html($step['title']) ?></h3>
                        <p class="font-manrope text-sm text-lavender/60 leading-relaxed"><?= esc_html($step['desc']) ?></p>
                    </div>
                    <div class="mt-auto">
                        <span class="font-mono text-[10px] text-sharp-purple uppercase tracking-widest border border-sharp-purple/20 px-3 py-1 rounded-full"><?= esc_html($step['tag']) ?></span>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>

            <!-- CTA strip -->
            <div class="mt-12 text-center">
                <button onclick="toggleWaWidget()" class="inline-flex items-center gap-3 font-syne text-sm font-bold uppercase tracking-widest text-lavender hover:text-white border border-lavender/20 hover:border-sharp-purple/50 px-8 py-4 rounded-full transition-all hover-target focus:outline-none">
                    Start with a Free Discovery Call <i data-lucide="arrow-right" class="w-4 h-4"></i>
                </button>
            </div>
        </div>
    </section>

    <!-- SECTION 4C: WEB DESIGN COMPANY vs FREELANCER — SEMANTIC DIFFERENTIATOR -->
    <!-- This block targets "web design company in [city]" vs "freelance web designer" queries -->
    <section class="py-16 md:py-20 px-4 md:px-6 bg-[#0d0d0d] border-t border-lavender/5 relative z-10">
        <div class="max-w-7xl mx-auto">
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-10 items-center">
                <!-- Left: Heading -->
                <div>
                    <p class="font-mono text-[10px] text-sharp-purple tracking-widest uppercase mb-4">[ Why Choose a Company Over a Freelancer ]</p>
                    <h2 class="font-syne text-2xl md:text-4xl font-bold text-white mb-4 leading-tight">
                        The Best Web Designer in <?= go_safe_text($city_name) ?> is a Team, Not a Person.
                    </h2>
                    <p class="font-manrope text-lavender/60 text-base leading-relaxed">
                        When you search for a <strong class="text-white">web designer in <?= go_safe_text($city_name) ?></strong> or a <strong class="text-white">web design company in <?= go_safe_text($city_name) ?></strong>, you want a result you can trust with your business. GetOnline Studio is a registered Nigerian web design and development company with over 9 years of experience — not a solo freelancer — which means you get a dedicated team handling your design, development, SEO, and ongoing support.
                    </p>
                    <p class="font-manrope text-lavender/60 text-base leading-relaxed mt-4">
                        Our web designers and web developers work together on every project, so your website doesn't just look good — it performs, loads fast, and gets found on Google.
                    </p>
                </div>
                <!-- Right: Comparison checklist -->
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <?php
                    $differentiators = [
                        ['icon' => 'users',        'label' => 'Full Team',          'desc' => 'Designers, developers & SEO specialists on every project.'],
                        ['icon' => 'shield-check',  'label' => 'Registered Company', 'desc' => 'Incorporated Nigerian business — accountable and reliable.'],
                        ['icon' => 'code-2',        'label' => 'Web Development',    'desc' => 'We do web design AND web development — not just templates.'],
                        ['icon' => 'trending-up',   'label' => 'SEO-First Builds',   'desc' => 'Every website we build is optimised for Google from day one.'],
                        ['icon' => 'calendar',      'label' => '9+ Years Experience','desc' => 'Over 9 years building professional websites for Nigerian businesses.'],
                    ];
                    foreach ($differentiators as $d): ?>
                    <div class="bg-card-dark border border-lavender/10 rounded-2xl p-6 flex items-start gap-4 hover:border-sharp-purple/30 transition-colors">
                        <div class="w-10 h-10 rounded-xl bg-sharp-purple/10 flex items-center justify-center flex-shrink-0 mt-0.5">
                            <i data-lucide="<?= esc_attr($d['icon']) ?>" class="w-4 h-4 text-sharp-purple"></i>
                        </div>
                        <div>
                            <h3 class="font-syne font-bold text-white text-sm mb-1"><?= esc_html($d['label']) ?></h3>
                            <p class="font-manrope text-xs text-lavender/50 leading-relaxed"><?= esc_html($d['desc']) ?></p>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
    </section>

    <!-- SECTION 5: ADVANTAGES & POSSIBILITIES -->
    <section class="py-24 md:py-32 px-4 md:px-6 bg-card-dark border-t border-lavender/10">
        <div class="max-w-6xl mx-auto text-center">
            <h2 class="font-syne text-3xl md:text-5xl mb-6">The GetOnline Advantage</h2>
            <p class="font-manrope text-lavender/60 text-lg mb-16 max-w-2xl mx-auto">Why the leading companies in <?= go_safe_text($city_name) ?> trust us to build their digital ecosystems.</p>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                <div class="p-10 border border-lavender/10 rounded-3xl bg-matte-black hover:border-sharp-purple/50 transition-colors hover-target tilt-card" onmousemove="tiltCard(event, this)" onmouseleave="resetTilt(this)">
                    <div class="w-16 h-16 mx-auto rounded-2xl bg-sharp-purple/10 flex items-center justify-center mb-6">
                        <i data-lucide="eye" class="w-8 h-8 text-sharp-purple"></i>
                    </div>
                    <h3 class="font-syne text-2xl mb-4 text-white">Clarity</h3>
                    <p class="text-sm text-lavender/60 leading-relaxed">Your message becomes impossible to misunderstand. We strip away the noise so your clients take action.</p>
                </div>
                <div class="p-10 border border-lavender/10 rounded-3xl bg-matte-black hover:border-sharp-purple/50 transition-colors hover-target tilt-card" onmousemove="tiltCard(event, this)" onmouseleave="resetTilt(this)">
                    <div class="w-16 h-16 mx-auto rounded-2xl bg-sharp-purple/10 flex items-center justify-center mb-6">
                        <i data-lucide="zap" class="w-8 h-8 text-sharp-purple"></i>
                    </div>
                    <h3 class="font-syne text-2xl mb-4 text-white">Efficiency</h3>
                    <p class="text-sm text-lavender/60 leading-relaxed">You stop doing busy work. Our automations and lead capture systems handle the repetitive tasks for you.</p>
                </div>
                <div class="p-10 border border-lavender/10 rounded-3xl bg-matte-black hover:border-sharp-purple/50 transition-colors hover-target tilt-card" onmousemove="tiltCard(event, this)" onmouseleave="resetTilt(this)">
                    <div class="w-16 h-16 mx-auto rounded-2xl bg-sharp-purple/10 flex items-center justify-center mb-6">
                        <i data-lucide="gem" class="w-8 h-8 text-sharp-purple"></i>
                    </div>
                    <h3 class="font-syne text-2xl mb-4 text-white">Value</h3>
                    <p class="text-sm text-lavender/60 leading-relaxed">You can charge what you are actually worth because your digital footprint projects absolute market leadership.</p>
                </div>
            </div>
        </div>
    </section>

    <!-- SECTION 6: SELECTED WORK -->
    <section class="py-24 md:py-32 px-4 md:px-6 bg-matte-black border-t border-lavender/10">
        <div class="max-w-7xl mx-auto">
            <div class="flex flex-col md:flex-row justify-between items-end mb-16 gap-6">
                <div>
                    <h2 class="font-syne text-3xl md:text-5xl font-bold mb-4">Selected Work</h2>
                    <p class="font-manrope text-lavender/60 text-lg">Case studies of scale and performance.</p>
                </div>
                <a href="/work" class="inline-flex items-center gap-2 text-sharp-purple font-bold tracking-widest uppercase hover:text-white transition-colors hover-target border-b border-sharp-purple pb-1">
                    View All Projects <i data-lucide="arrow-right" class="w-4 h-4"></i>
                </a>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-12 md:gap-20">
                <a href="/work/rafflekings" class="group block hover-target reveal-up">
                    <div class="relative w-full aspect-[4/3] rounded-2xl overflow-hidden mb-6 bg-card-dark border border-lavender/10">
                        <div class="absolute inset-0 bg-cover bg-top project-img filter grayscale opacity-80" style="background-image: url('https://getonlinestudio.com/insights/wp-content/uploads/2026/03/69c056ef61d91271e5147a38.jpg');"></div>
                        <div class="absolute top-4 left-4">
                            <span class="font-mono text-[10px] md:text-xs text-white bg-black/60 px-3 py-1 rounded-full backdrop-blur-md border border-white/20">01</span>
                        </div>
                    </div>
                    <div class="flex flex-col xl:flex-row xl:justify-between xl:items-start gap-4">
                        <div class="flex-1">
                            <h3 class="font-syne text-2xl md:text-3xl font-bold mb-3 group-hover:text-sharp-purple transition-colors">RAFFLEKINGS</h3>
                            <p class="font-manrope text-lavender/60 text-sm leading-relaxed max-w-md">A high-performance "Fintech-Gaming" ecosystem bridging financial security and gaming excitement with a custom backend.</p>
                        </div>
                        <div class="flex-shrink-0 mt-2 xl:mt-0">
                            <span class="font-mono text-[10px] text-sharp-purple tracking-widest uppercase border border-sharp-purple/30 px-3 py-1 rounded-full">Fintech / Web App</span>
                        </div>
                    </div>
                </a>

                <a href="/work/visionafric" class="group block hover-target reveal-up md:mt-24">
                    <div class="relative w-full aspect-[4/3] rounded-2xl overflow-hidden mb-6 bg-card-dark border border-lavender/10">
                        <div class="absolute inset-0 bg-cover bg-top project-img filter grayscale opacity-80" style="background-image: url('https://getonlinestudio.com/insights/wp-content/uploads/2026/03/69bfd90c35c39c6a8b6658d9.jpg');"></div>
                        <div class="absolute top-4 left-4">
                            <span class="font-mono text-[10px] md:text-xs text-white bg-black/60 px-3 py-1 rounded-full backdrop-blur-md border border-white/20">02</span>
                        </div>
                    </div>
                    <div class="flex flex-col xl:flex-row xl:justify-between xl:items-start gap-4">
                        <div class="flex-1">
                            <h3 class="font-syne text-2xl md:text-3xl font-bold mb-3 group-hover:text-sharp-purple transition-colors">VISIONAFRIC</h3>
                            <p class="font-manrope text-lavender/60 text-sm leading-relaxed max-w-md">An immersive "Trust Architecture" designed to legitimize their pan-African mission and drive high-ticket donations.</p>
                        </div>
                        <div class="flex-shrink-0 mt-2 xl:mt-0">
                            <span class="font-mono text-[10px] text-sharp-purple tracking-widest uppercase border border-sharp-purple/30 px-3 py-1 rounded-full">NGO / Global</span>
                        </div>
                    </div>
                </a>
            </div>
        </div>
    </section>


    <!-- SECTION 7: THE SEO PILLAR (MAGAZINE/SPLIT LAYOUT) -->
    <?php if (!empty($city_long_content)): ?>
    <section class="py-24 md:py-32 px-4 md:px-6 bg-card-dark border-t border-lavender/10 relative z-10">
        <div class="max-w-7xl mx-auto flex flex-col lg:flex-row gap-12 lg:gap-20">

            <div class="w-full lg:w-1/3">
                <div class="sticky top-32">
                    <p class="font-mono text-[10px] text-sharp-purple tracking-widest uppercase mb-4">[ Market Analysis ]</p>
                    <h2 class="font-syne text-3xl md:text-4xl font-bold mb-6 text-white leading-tight">
                        The Ultimate Guide to Digital Growth in <?= go_safe_text($city_name) ?>.
                    </h2>
                    <p class="font-manrope text-lavender/60 text-base leading-relaxed mb-8">
                        Operating a business in <?= go_safe_text($city_name) ?> requires more than just a social media presence. We break down exactly why digital infrastructure is replacing traditional marketing in this city.
                    </p>

                    <div class="bg-matte-black border border-white/5 rounded-2xl p-6 shadow-xl">
                        <div class="flex items-center gap-3 mb-4 pb-4 border-b border-white/5">
                            <div class="w-10 h-10 rounded-full bg-sharp-purple/20 flex items-center justify-center">
                                <i data-lucide="bar-chart-2" class="w-5 h-5 text-sharp-purple"></i>
                            </div>
                            <div>
                                <div class="text-white font-bold text-sm">Market Intelligence</div>
                                <div class="text-[10px] font-mono text-lavender/40 uppercase tracking-widest">Read Time: 3 Mins</div>
                            </div>
                        </div>
                        <ul class="space-y-4 font-manrope text-sm text-lavender/70">
                            <li class="flex items-start gap-2">
                                <i data-lucide="check" class="w-4 h-4 text-code-green flex-shrink-0 mt-0.5"></i>
                                <span>The cost of relying purely on Instagram & WhatsApp.</span>
                            </li>
                            <li class="flex items-start gap-2">
                                <i data-lucide="check" class="w-4 h-4 text-code-green flex-shrink-0 mt-0.5"></i>
                                <span>Why clients choose credibility over cheap prices.</span>
                            </li>
                            <li class="flex items-start gap-2">
                                <i data-lucide="check" class="w-4 h-4 text-code-green flex-shrink-0 mt-0.5"></i>
                                <span>How automated platforms capture 24/7 leads.</span>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>

            <div class="w-full lg:w-2/3 prose-pillar">
                <?= go_spin_text($city_long_content) ?>
            </div>

        </div>
    </section>
    <?php endif; ?>

    <!-- SECTION 8B: COMMON MISTAKES — DYNAMIC PER CITY -->
    <section class="py-20 md:py-28 px-4 md:px-6 bg-[#0d0d0d] border-t border-lavender/5 relative z-10">
        <div class="max-w-5xl mx-auto">
            <div class="text-center max-w-3xl mx-auto mb-14">
                <p class="font-mono text-[10px] text-sharp-purple tracking-widest uppercase mb-4">[ What to Avoid ]</p>
                <h2 class="font-syne text-3xl md:text-4xl font-bold text-white mb-4">
                    <?= go_spin_text("{Common Mistakes|Critical Errors|Costly Mistakes} in " . go_safe_text($city_name) . " — {Websites That Never Work|Web Design Traps|What Not to Do}") ?>
                </h2>
                <p class="font-manrope text-lavender/60 text-base leading-relaxed">
                    <?= go_spin_text("After {working with|delivering projects for|building platforms for} {hundreds of|over 200|countless} businesses across Nigeria, these are the {patterns|mistakes|problems} we see {most often|repeatedly|again and again} — especially in {$city_name}.") ?>
                </p>
            </div>

            <div class="space-y-5">
                <?php
                $mistakes = [
                    [
                        'number' => '01',
                        'title'  => go_spin_text("Choosing the {Cheapest|Lowest Bid|Cheapest Option} Instead of the {Best Value|Right Fit|Most Qualified Team}"),
                        'desc'   => go_spin_text("Many {$city_name} businesses {hire based on price alone|go for the cheapest quote|pick whoever charges the least} and end up with a website that {breaks within months|looks outdated on launch day|never ranks on Google}. A cheap website {costs more to fix|becomes a liability|needs to be rebuilt} than a quality one built right the first time. {Value matters more than price.|The goal is ROI, not savings.|Think of it as an asset, not an expense.}"),
                    ],
                    [
                        'number' => '02',
                        'title'  => go_spin_text("Relying {Entirely|Completely|Solely} on Instagram or WhatsApp {Instead of a Website|as a Substitute for a Website|as Their Online Presence}"),
                        'desc'   => go_spin_text("Social media {platforms are rented space|is borrowed land|is not owned property}. Algorithm changes, account bans, or platform outages can {wipe out your entire online presence overnight|make you invisible instantly|erase years of work in hours}. {$city_name} businesses that own their website {control their own visibility|are never at the mercy of a platform|always have a home base online}."),
                    ],
                    [
                        'number' => '03',
                        'title'  => go_spin_text("{Launching a Website|Going Live|Building a Website} With No {SEO Strategy|SEO Plan|Search Engine Optimisation}"),
                        'desc'   => go_spin_text("A website with no SEO is {invisible to Google|a digital billboard in the middle of a forest|a shop with no signage}. {Most businesses in {$city_name} make this mistake.|We see this constantly in {$city_name}.|This is the single most common issue we fix.} Your website needs to be {optimised from day one|built with SEO architecture|structured for Google} — not treated as an afterthought after launch."),
                    ],
                    [
                        'number' => '04',
                        'title'  => go_spin_text("{Using a Template|Buying a Generic Theme|Using a Free Website Builder} and Calling It {a Professional Website|Done|a Brand}"),
                        'desc'   => go_spin_text("{Template websites|Generic themes|Free website builders} look the same as {thousands of other businesses|every competitor in your industry|half the internet}. {In {$city_name}'s competitive market|When your clients are comparing you to others|When first impressions decide everything}, a {custom-built|professionally designed|bespoke} website is what {separates you from amateurs|signals you are a serious business|makes clients choose you}."),
                    ],
                    [
                        'number' => '05',
                        'title'  => go_spin_text("{Ignoring|Neglecting|Skipping} Mobile {Optimisation|Performance|Experience}"),
                        'desc'   => go_spin_text("{Over 80% of web searches in Nigeria happen on mobile.|Most of your {$city_name} customers are browsing on their phones.|The majority of your traffic will come from mobile devices.} A website that {loads slowly|looks broken|is hard to navigate} on mobile {loses those visitors immediately|drives customers away|means lost revenue every single day}. {Every platform we build is mobile-first by default.|We build mobile-first on every project.|Mobile performance is non-negotiable for us.}"),
                    ],
                ];
                foreach ($mistakes as $i => $mistake): ?>
                <div class="flex gap-6 bg-card-dark border border-lavender/10 rounded-2xl p-7 hover:border-sharp-purple/30 transition-colors">
                    <div class="flex-shrink-0 w-10 h-10 rounded-full bg-sharp-purple/10 flex items-center justify-center font-mono text-xs font-bold text-sharp-purple mt-0.5">
                        <?= esc_html($mistake['number']) ?>
                    </div>
                    <div>
                        <h3 class="font-syne text-base md:text-lg font-bold text-white mb-2"><?= go_safe_text($mistake['title']) ?></h3>
                        <p class="font-manrope text-sm text-lavender/60 leading-relaxed"><?= go_safe_text($mistake['desc']) ?></p>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
    </section>

    <!-- SECTION 9: CITY FAQS -->
    <section class="py-24 md:py-32 px-4 md:px-6 bg-[#0a0a0a] border-y border-lavender/10 relative z-10">
        <div class="max-w-3xl mx-auto">
            <h2 class="font-syne text-3xl md:text-5xl font-bold mb-12 text-center">Frequently Asked in <?= go_safe_text($city_name) ?>.</h2>

            <div class="space-y-4">
                <?php foreach($city_faqs as $faq): ?>
                <div class="faq-item bg-matte-black border border-lavender/10 rounded-2xl overflow-hidden hover-target">
                    <button class="w-full text-left px-6 md:px-8 py-6 flex justify-between items-center focus:outline-none" onclick="this.parentElement.classList.toggle('active')">
                        <span class="font-syne font-bold text-lg pr-4"><?= go_safe_text($faq['q']) ?></span>
                        <i data-lucide="plus" class="w-5 h-5 text-sharp-purple flex-shrink-0 transition-transform duration-300 faq-icon"></i>
                    </button>
                    <div class="faq-content bg-[#111]">
                        <p class="px-6 md:px-8 pb-8 pt-2 text-lavender/70 leading-relaxed text-sm md:text-base">
                            <?= nl2br(go_safe_text($faq['a'])) ?>
                        </p>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
    </section>

    <!-- SECTION 8: FINAL CALL TO ACTION -->
    <section id="consultation" class="py-32 md:py-40 px-4 md:px-6 text-center bg-sharp-purple text-white relative z-20 overflow-hidden">
        <div class="absolute inset-0 bg-[url('data:image/svg+xml,%3Csvg viewBox=%220 0 200 200%22 xmlns=%22http://www.w3.org/2000/svg%22%3E%3Cfilter id=%22noiseFilter%22%3E%3CfeTurbulence type=%22fractalNoise%22 baseFrequency=%220.65%22 numOctaves=%223%22 stitchTiles=%22stitch%22/%3E%3C/filter%3E%3Crect width=%22100%25%22 height=%22100%25%22 filter=%22url(%23noiseFilter)%22 opacity=%220.08%22/%3E%3C/svg%3E')] mix-blend-overlay"></div>
        <div class="max-w-4xl mx-auto reveal-up relative z-10">
            <h2 class="font-syne text-4xl md:text-7xl font-bold mb-4 text-white leading-tight">
                Ready to dominate <?= go_safe_text($city_name) ?>?<br>
                Let's make it happen.
            </h2>
            <p class="font-manrope text-lg md:text-xl mb-12 text-white/90 leading-relaxed max-w-2xl mx-auto">
                Every day without a great website is a day a potential client chose your competitor instead. Let's start your project today. Our discovery call is free and takes 20 minutes.
            </p>
            <div class="flex flex-col sm:flex-row items-center justify-center gap-4">
                <button onclick="toggleWaWidget()" class="w-full sm:w-auto bg-matte-black text-white px-10 py-5 rounded-full text-sm font-bold uppercase tracking-widest hover:bg-white hover:text-matte-black transition-all hover-target shadow-2xl focus:outline-none">
                    Start Your Project &rarr;
                </button>
                <button onclick="toggleWaWidget()" class="w-full sm:w-auto bg-[#25D366] text-white px-10 py-5 rounded-full text-sm font-bold uppercase tracking-widest hover:bg-[#1ebe5d] transition-all hover-target shadow-xl flex items-center justify-center gap-2 focus:outline-none">
                    <i data-lucide="message-circle" class="w-5 h-5"></i> Chat on WhatsApp
                </button>
            </div>
        </div>
    </section>


    <!-- CROSS-CITY NAVIGATION -->
    <section class="py-16 px-4 md:px-6 bg-[#0a0a0a] border-t border-lavender/10 relative z-10">
        <div class="max-w-7xl mx-auto">
            <p class="font-mono text-[10px] text-lavender/40 tracking-widest uppercase mb-6">[ National Coverage ]</p>

            <!-- Home page keyword link — one variant per city for SEO diversity -->
            <div class="mb-6">
                <a href="/" class="inline-flex items-center gap-2 font-manrope text-sm text-sharp-purple hover:text-white border border-sharp-purple/30 hover:border-white/30 px-4 py-2 rounded-full transition-all duration-200 hover-target">
                    <i data-lucide="home" class="w-3.5 h-3.5"></i>
                    <?= go_safe_text($home_kw_label) ?>
                </a>
            </div>

            <div class="flex flex-wrap gap-3">
                <?php foreach($all_cities as $c_slug => $c_name):
                    if ($c_slug === $city_slug) continue; ?>
                <a href="/locations/<?= esc_attr($c_slug) ?>/" class="font-manrope text-sm text-lavender/50 hover:text-lavender border border-lavender/10 hover:border-lavender/30 px-4 py-2 rounded-full transition-all duration-200 hover-target">
                    <?= go_safe_text($c_name) ?>
                </a>
                <?php endforeach; ?>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer class="pt-20 md:pt-28 pb-10 px-4 md:px-6 bg-matte-black border-t border-lavender/20 relative z-20">
        <div class="max-w-7xl mx-auto">

            <!-- Top: Big CTA + About blurb -->
            <div class="flex flex-col lg:flex-row justify-between gap-12 lg:gap-20 pb-16 border-b border-lavender/10">

                <!-- Left: LET'S TALK -->
                <div class="lg:w-1/2">
                    <h2 class="font-syne text-5xl md:text-7xl lg:text-8xl mb-4 text-lavender leading-none">LET'S<br>TALK.</h2>
                    <p class="font-manrope text-lavender/60 text-base mb-6 max-w-sm leading-relaxed">Ready to get more customers online? Our team is available right now. Reach us on WhatsApp or send an email, we respond fast.</p>
                    <div class="flex flex-col sm:flex-row gap-4">
                        <button onclick="toggleWaWidget()" class="inline-flex items-center gap-2 bg-[#25D366] text-white font-bold text-sm px-6 py-3 rounded-full hover:bg-[#1ebe5d] transition-all hover-target focus:outline-none">
                            <i data-lucide="message-circle" class="w-4 h-4"></i> +234 906 115 0443
                        </button>
                        <a href="mailto:hello@getonlinestudio.com?subject=<?= rawurlencode("Project Inquiry from {$city_name}") ?>" class="inline-flex items-center gap-2 text-sharp-purple font-manrope text-sm font-bold hover:text-white transition-colors hover-target border border-sharp-purple/30 px-6 py-3 rounded-full hover:border-white/30">
                            <i data-lucide="mail" class="w-4 h-4"></i> hello@getonlinestudio.com
                        </a>
                    </div>
                </div>

                <!-- Right: About GetOnline Studio -->
                <div class="lg:w-1/2 lg:pt-4">
                    <p class="font-mono text-[10px] text-sharp-purple tracking-widest uppercase mb-4">[ Who We Are ]</p>
                    <h3 class="font-syne text-2xl font-bold text-white mb-4">GetOnline Studio</h3>
                    <p class="font-manrope text-lavender/60 text-sm leading-relaxed mb-4">
                        GetOnline Studio is a Nigerian web design company and digital agency with over 9 years of experience helping businesses build a serious online presence. Our web designers and web developers build professional websites, SEO-optimised platforms, and automated digital systems for businesses across Nigeria.
                    </p>
                    <p class="font-manrope text-lavender/60 text-sm leading-relaxed">
                        We serve clients in <?= go_safe_text($city_name) ?> and every major city in Nigeria. Whether you are looking for a web designer, a web developer, or a full-service web design company, we are the team you need.
                    </p>
                </div>
            </div>

            <!-- Middle: Navigation Links -->
            <div class="py-12 grid grid-cols-2 md:grid-cols-4 gap-8 border-b border-lavender/10">

                <div>
                    <p class="font-mono text-[10px] text-lavender/30 tracking-widest uppercase mb-5">Company</p>
                    <ul class="space-y-3 font-manrope text-sm">
                        <li><a href="/" class="text-lavender/60 hover:text-white transition-colors hover-target">Home</a></li>
                        <li><a href="/about/" class="text-lavender/60 hover:text-white transition-colors hover-target">About Us</a></li>
                        <li><a href="/testimonials/" class="text-lavender/60 hover:text-white transition-colors hover-target">Testimonials</a></li>
                        <li><a href="/work/" class="text-lavender/60 hover:text-white transition-colors hover-target">Projects & Case Studies</a></li>
                    </ul>
                </div>

                <div>
                    <p class="font-mono text-[10px] text-lavender/30 tracking-widest uppercase mb-5">Services</p>
                    <ul class="space-y-3 font-manrope text-sm">
                        <li><a href="/services/web-design/" class="text-lavender/60 hover:text-white transition-colors hover-target">Website Design</a></li>
                        <li><a href="/services/seo/" class="text-lavender/60 hover:text-white transition-colors hover-target">SEO & Google Ranking</a></li>
                        <li><a href="/services/branding/" class="text-lavender/60 hover:text-white transition-colors hover-target">Branding & Identity</a></li>
                        <li><a href="/services/" class="text-lavender/60 hover:text-sharp-purple transition-colors hover-target font-bold">All Services &rarr;</a></li>
                    </ul>
                </div>

                <div>
                    <p class="font-mono text-[10px] text-lavender/30 tracking-widest uppercase mb-5">Locations</p>
                    <ul class="space-y-3 font-manrope text-sm">
                        <li><a href="/locations/" class="text-lavender/60 hover:text-white transition-colors hover-target">All Locations</a></li>
                        <li><a href="/locations/lagos/" class="text-lavender/60 hover:text-white transition-colors hover-target">Lagos</a></li>
                        <li><a href="/locations/abuja/" class="text-lavender/60 hover:text-white transition-colors hover-target">Abuja</a></li>
                        <li><a href="/locations/port-harcourt/" class="text-lavender/60 hover:text-white transition-colors hover-target">Port Harcourt</a></li>
                    </ul>
                </div>

                <div>
                    <p class="font-mono text-[10px] text-lavender/30 tracking-widest uppercase mb-5">Legal</p>
                    <ul class="space-y-3 font-manrope text-sm">
                        <li><a href="/privacy-policy/" class="text-lavender/60 hover:text-white transition-colors hover-target">Privacy Policy</a></li>
                        <li><a href="/terms-of-service/" class="text-lavender/60 hover:text-white transition-colors hover-target">Terms of Service</a></li>
                        <li><a href="/cookie-policy/" class="text-lavender/60 hover:text-white transition-colors hover-target">Cookie Policy</a></li>
                    </ul>
                </div>

            </div>

            <!-- Bottom: Copyright -->
            <div class="pt-8 flex flex-col md:flex-row justify-between items-center gap-4 font-manrope text-xs text-lavender/30">
                <p>GetOnline Studio &copy; <?= date('Y') ?> Proudly serving <?= go_safe_text($city_name) ?> &amp; every city in Nigeria.</p>
                <p class="font-mono uppercase tracking-widest">Digital Infrastructure. Built for Growth.</p>
            </div>

        </div>
    </footer>

    <script>
        lucide.createIcons();

        // isTouchDevice MUST be declared first — used by all interaction logic below
        const isTouchDevice = 'ontouchstart' in window || navigator.maxTouchPoints > 0;

        // ----------------------------------------------------
        // WHATSAPP WIDGET & FUNNEL LOGIC
        // ----------------------------------------------------
        const currentCityName = '<?= esc_js($city_name) ?>';

        function toggleWaWidget() {
            const waWindow = document.getElementById('wa-float-window');
            if (!waWindow) return;
            if (waWindow.classList.contains('widget-hidden')) {
                waWindow.classList.remove('widget-hidden');
                waWindow.classList.add('widget-visible');
            } else {
                waWindow.classList.remove('widget-visible');
                waWindow.classList.add('widget-hidden');
            }
        }

        function openWaWidgetWithService(serviceName) {
            const waWindow = document.getElementById('wa-float-window');
            if (!waWindow) return;
            waWindow.classList.remove('widget-hidden');
            waWindow.classList.add('widget-visible');
            document.querySelectorAll('.wa-service-cb').forEach(cb => {
                if (cb.value.includes(serviceName) || serviceName.includes(cb.value)) {
                    cb.checked = true;
                }
            });
        }

        function sendWaWidget() {
            const checkedBoxes = document.querySelectorAll('.wa-service-cb:checked');
            let selectedServices = [];
            checkedBoxes.forEach(cb => selectedServices.push(cb.value));
            let waText = selectedServices.length > 0
                ? `Hi GetOnline Studio, I am reaching out from ${currentCityName}. I am interested in: ${selectedServices.join(', ')}. Can we talk?`
                : `Hi GetOnline Studio, I am looking for a digital agency in ${currentCityName}. Let's talk!`;
            window.open(`https://wa.me/2349061150443?text=${encodeURIComponent(waText)}`, '_blank');
            toggleWaWidget();
        }

        let waIndustry = '';
        let waGoal = '';

        function nextWaStep(step) {
            if(step === 2) {
                const input = document.getElementById('wa-industry-input').value.trim();
                if(!input) { alert('Please enter your industry first to continue.'); return; }
                waIndustry = input;
                document.getElementById('wa-display-industry').innerText = waIndustry;
                document.getElementById('wa-step-1').classList.add('hidden', 'opacity-0');
                const step2 = document.getElementById('wa-step-2');
                step2.classList.remove('hidden');
                setTimeout(() => step2.classList.remove('opacity-0'), 50);
            }
        }

        function selectWaGoal(goal) {
            waGoal = goal;
            const waText = `Hi GetOnline Studio, I have a business in the ${waIndustry} industry based in ${currentCityName}. I am reaching out because I ${waGoal}. Can we talk?`;
            document.getElementById('wa-final-btn').href = `https://wa.me/2349061150443?text=${encodeURIComponent(waText)}`;
            document.getElementById('wa-step-2').classList.add('hidden', 'opacity-0');
            const step3 = document.getElementById('wa-step-3');
            step3.classList.remove('hidden');
            setTimeout(() => step3.classList.remove('opacity-0'), 50);
        }

        // ----------------------------------------------------
        // DESKTOP-ONLY: cursor, card tilt, service row hover
        // Completely skipped on touch/mobile — safe no-ops defined for inline handlers
        // ----------------------------------------------------
        function tiltCard(event, card) {}
        function resetTilt(card) {}

        if (!isTouchDevice) {
            const previewEl = document.getElementById('cursor-preview');
            const cursorDot = document.querySelector('.cursor-dot');
            const cursorOutline = document.querySelector('.cursor-outline');

            document.querySelectorAll('.service-row').forEach(row => {
                row.addEventListener('mouseenter', () => {
                    const img = row.getAttribute('data-image');
                    if (img && previewEl) { previewEl.style.backgroundImage = `url(${img})`; previewEl.classList.add('active'); }
                });
                row.addEventListener('mouseleave', () => { if (previewEl) previewEl.classList.remove('active'); });
            });

            document.querySelectorAll('[onmousemove*="tiltCard"]').forEach(card => {
                card.addEventListener('mousemove', (e) => {
                    const r = card.getBoundingClientRect();
                    const rx = ((e.clientY - r.top - r.height/2) / (r.height/2)) * -10;
                    const ry = ((e.clientX - r.left - r.width/2) / (r.width/2)) * 10;
                    card.style.transform = `perspective(1000px) rotateX(${rx}deg) rotateY(${ry}deg) scale(1.05)`;
                });
                card.addEventListener('mouseleave', () => {
                    card.style.transform = '';
                });
            });

            if (cursorDot && cursorOutline) {
                let mouseX = 0, mouseY = 0, outlineX = 0, outlineY = 0;
                window.addEventListener('mousemove', (e) => {
                    mouseX = e.clientX; mouseY = e.clientY;
                    cursorDot.style.transform = `translate(${mouseX}px, ${mouseY}px) translate(-50%, -50%)`;
                    if (previewEl && previewEl.classList.contains('active')) {
                        previewEl.style.left = `${mouseX + 50}px`;
                        previewEl.style.top = `${mouseY + 50}px`;
                    }
                });
                const animateCursor = () => {
                    outlineX += (mouseX - outlineX) * 0.15;
                    outlineY += (mouseY - outlineY) * 0.15;
                    cursorOutline.style.transform = `translate(${outlineX}px, ${outlineY}px) translate(-50%, -50%)`;
                    requestAnimationFrame(animateCursor);
                };
                animateCursor();
                const bindHover = () => {
                    document.querySelectorAll('.hover-target, a, button, [role="tab"], label').forEach(el => {
                        if (!el.hasAttribute('data-cursor-bound')) {
                            el.addEventListener('mouseenter', () => document.body.classList.add('hovering'));
                            el.addEventListener('mouseleave', () => document.body.classList.remove('hovering'));
                            el.setAttribute('data-cursor-bound', 'true');
                        }
                    });
                };
                bindHover();
                document.addEventListener('click', () => setTimeout(bindHover, 100));
            }
        }

        function switchFormat(fmt) {
            document.querySelectorAll('.format-grid').forEach(g => g.classList.add('hidden'));
            document.querySelectorAll('.format-tab').forEach(t => {
                t.classList.remove('bg-sharp-purple', 'border-sharp-purple', 'text-white');
                t.classList.add('border-lavender/20', 'text-lavender/50');
                t.setAttribute('aria-selected', 'false');
            });

            const targetGrid = document.getElementById('grid-' + fmt);
            targetGrid.classList.remove('hidden');

            const activeTab = document.getElementById('tab-' + fmt);
            activeTab.classList.add('bg-sharp-purple', 'border-sharp-purple', 'text-white');
            activeTab.classList.remove('border-lavender/20', 'text-lavender/50');
            activeTab.setAttribute('aria-selected', 'true');
            // Only reinitialise icons inside the newly shown grid, not the whole page
            lucide.createIcons({ nameAttr: 'data-lucide', nodes: [targetGrid] });
        }

        const showMoreBtn = document.getElementById('show-more-listicles');
        if (showMoreBtn) {
            showMoreBtn.addEventListener('click', () => {
                document.querySelectorAll('.extra-listicle').forEach(el => el.classList.remove('hidden'));
                showMoreBtn.style.display = 'none';
            });
        }
    </script>
    <?php wp_footer(); ?>
</body>
</html>