<?php
/**
 * MASTER NICHE LANDING PAGE TEMPLATE — v6.4
 * Upgraded with:
 * - Dynamic Hero Statistics (The Digital Gap Percentage)
 * - Minimalist Breadcrumb Navigation
 * - Strict Database Filtering for Active Neighborhoods (Hides inactive hubs)
 * - Contextual Vocabulary Engine & AI Database Cleanser
 * - Massive Multi-Tier Niche Pricing Matrix & Grammar Overrides
 * - SubLocality Geo-Targeted Schema
 * - [v6.3] Exhaustive Niche Plural Overrides Map (fixes "Drone Photographys" etc.)
 * - [v6.4] Direct JSON Pricing Integration & Education Tone Engine
 * - [v6.4.1] Hardcoded GO_SITE_URL to prevent /wp/ leakage in canonical, OG, and schema tags
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

// ─── SITE URL: Hardcoded to prevent /wp/ leakage from WP Core home_url() ─────
if (!defined('GO_SITE_URL')) {
    define('GO_SITE_URL', 'https://getonlinestudio.com');
}

// Helper: prevent double-encoding of ampersands and strip long dashes
function go_safe_text($text) {
    $text = str_replace(['—', '–'], '-', (string)($text ?? ''));
    return esc_html(html_entity_decode($text, ENT_QUOTES, 'UTF-8'));
}

// Spintax engine
function go_spin_text($text) {
    $text = (string) $text;
    return preg_replace_callback('/\{(((?>[^\{\}]+)|(?R))*)\}/x', function ($match) {
        $inner = go_spin_text($match[1]);
        $parts = explode('|', $inner);
        return $parts[array_rand($parts)];
    }, $text);
}

// 2. Catch URL Parameters & Prevent Empty Slug Bugs
$raw_city  = !empty($_GET['city']) ? $_GET['city'] : 'lagos';
$city_slug = sanitize_title($raw_city) ?: 'lagos';

// Verify City Exists to prevent "Fake City" indexing (404 protection)
$city_post = get_page_by_path($city_slug, OBJECT, 'pseo_location');
if (!$city_post || $city_post->post_status !== 'publish') {
    status_header(404);
    wp_redirect('/locations/');
    exit;
}

// ─── NEIGHBORHOOD INTERCEPT ENGINE ───────────────────────────────────────────
$raw_neighborhood = !empty($_GET['neighborhood']) ? $_GET['neighborhood'] : '';
$neighborhood_slug = sanitize_title($raw_neighborhood);
$neighborhood_name = '';
$nb_data = []; // Initialize neighborhood data array

if (!empty($neighborhood_slug)) {
    $neighborhoods_file = __DIR__ . '/neighborhoods.json';
    $nb_overrides_file = __DIR__ . '/neighborhoods-data.json'; // The hyper-local overrides file
    
    if (file_exists($neighborhoods_file)) {
        $neighborhood_data = json_decode(file_get_contents($neighborhoods_file), true);
        if (isset($neighborhood_data[$city_slug][$neighborhood_slug])) {
            // SECURITY: Check if neighborhood is actually activated in the Command Center
            $active_nb = get_post_meta($city_post->ID, '_pseo_active_neighborhoods', true) ?: [];
            if (in_array($neighborhood_slug, $active_nb)) {
                $neighborhood_name = $neighborhood_data[$city_slug][$neighborhood_slug];
                
                // LOAD THE HYPER-LOCAL OVERRIDES
                if (file_exists($nb_overrides_file)) {
                    $overrides_db = json_decode(file_get_contents($nb_overrides_file), true);
                    if (isset($overrides_db[$city_slug][$neighborhood_slug])) {
                        $nb_data = $overrides_db[$city_slug][$neighborhood_slug];
                    }
                }
            } else {
                status_header(404);
                wp_redirect("/locations/{$city_slug}/");
                exit;
            }
        } else {
            status_header(404);
            wp_redirect("/locations/{$city_slug}/");
            exit;
        }
    }
}
// ─────────────────────────────────────────────────────────────────────────────

$raw_slug  = !empty($_GET['niche_service']) ? $_GET['niche_service'] : 'business-website-designer';
$full_slug = sanitize_title($raw_slug) ?: 'business-website-designer';

// 3. DYNAMICALLY FETCH NICHES FOR URL PARSING
$active_niches_query = get_posts([
    'post_type'   => 'pseo_niche',
    'numberposts' => -1,
    'post_status' => ['publish', 'draft'] 
]);

$niche_dict = [];
$niche_slugs = [];
foreach ((array)$active_niches_query as $n) {
    $niche_dict[$n->post_name] = $n->post_title;
    $niche_slugs[] = $n->post_name;
}

if (empty($niche_slugs)) {
    $niche_slugs = ['business'];
    $niche_dict = ['business' => 'Business'];
}

$niche_slug   = 'business';
$service_slug = 'website-designer';

usort($niche_slugs, function($a, $b) { return strlen($b) - strlen($a); });

foreach ($niche_slugs as $n) {
    if (strpos($full_slug, $n) === 0) {
        $niche_slug   = $n;
        $service_slug = str_replace($n . '-', '', $full_slug);
        break;
    }
}

$base_city_name = go_safe_text($city_post->post_title);
if (!empty($neighborhood_name)) {
    $city_name = $neighborhood_name . ', ' . $base_city_name;
} else {
    $city_name = $base_city_name;
}

$niche_name   = isset($niche_dict[$niche_slug]) ? $niche_dict[$niche_slug] : ucwords(str_replace('-', ' ', $niche_slug));
$service_name = ucwords(str_replace('-', ' ', $service_slug));

$exact_keyword = "{$niche_name} {$service_name} in {$city_name}";

// --- TONE & VOCABULARY FLAGS ---
$is_non_profit = in_array($niche_slug, ['church', 'ngo']);
$is_education = in_array($niche_slug, ['school', 'polytechnic', 'university', 'music-school', 'driving-school', 'sports-academy']);
$is_soft_niche = $is_non_profit || $is_education;

// --- CONTEXTUAL VOCABULARY ENGINE ---
$vocab = [];
if ($is_non_profit) {
    $vocab['audience']       = "people";
    $vocab['projection']     = "authenticity and trust";
    $vocab['loss_state']     = "missing vital opportunities to expand your reach and impact";
    $vocab['daily_loss']     = "a day someone searching for community couldn't find you";
    $vocab['insight_title']  = "Community Growth Metrics";
    $vocab['growth_system']  = "platform for connection";
    $vocab['cta_action']     = "grow your {$niche_name}";
    $vocab['footer_tagline'] = "National Scale. Local Impact.";
    $vocab['market_leader']  = "trusted digital partner";
    $vocab['industry']       = "community";
    $vocab['org_label']      = "organisations";
} elseif ($is_education) {
    $vocab['audience']       = "prospective students and parents";
    $vocab['projection']     = "academic excellence and trust";
    $vocab['loss_state']     = "missing vital enrollment opportunities";
    $vocab['daily_loss']     = "a day a prospective student chose another institution";
    $vocab['insight_title']  = "Enrollment Demand";
    $vocab['growth_system']  = "enrollment platform";
    $vocab['cta_action']     = "grow your {$niche_name}";
    $vocab['footer_tagline'] = "National Scale. Educational Excellence.";
    $vocab['market_leader']  = "trusted educational institution";
    $vocab['industry']       = "education sector";
    $vocab['org_label']      = "institutions";
} else {
    $vocab['audience']       = "high-value clients";
    $vocab['projection']     = "market leadership";
    $vocab['loss_state']     = "voluntarily handing your market share over to your competitors";
    $vocab['daily_loss']     = "a day a potential client chose your competitor instead";
    $vocab['insight_title']  = "Local Search Demand";
    $vocab['growth_system']  = "growth system";
    $vocab['cta_action']     = "scale your {$niche_name}";
    $vocab['footer_tagline'] = "National Scale. Local Dominance.";
    $vocab['market_leader']  = "definitive market leader";
    $vocab['industry']       = "industry";
    $vocab['org_label']      = "businesses";
}

// ─── CRITICAL SEO FIX: DETERMINISTIC SEEDING ─────────────────────────────────
$master_seed = crc32($city_slug . $neighborhood_slug . $niche_slug . $service_slug);
srand($master_seed);

// ─── EARLY NICHE PLURAL DEFINITION (needed by LSI pool below) ────────────────
$niche_plural = $niche_name . 's'; // Fallback, full overrides map is later

// ─── LSI / SEMANTIC KEYWORD ENGINE ─────────────────────────────────────────
mt_srand($master_seed + 500);
$lsi_pool = [
    "{$niche_name} web developers in {$city_name}",
    "{$niche_name} portal creators in {$city_name}",
    "web design agency for {$niche_plural} in {$city_name}",
    "ICT platform builders for {$niche_plural} in {$city_name}",
    "custom website design for {$niche_plural} in {$city_name}",
    "digital marketing for {$niche_plural} in {$city_name}",
    "top {$niche_name} developers in {$city_name}",
    "{$city_name} {$niche_name} digital partners",
    $is_soft_niche ? "trusted web designers for {$niche_plural}" : "best website creators for {$niche_plural}"
];
$lsi_keyword = $lsi_pool[mt_rand(0, count($lsi_pool) - 1)];

// ─── MICRO-SHUFFLING ENGINE (Ensures absolute DOM uniqueness) ────────────────
mt_srand($master_seed + 100);
$outcome_order = [1, 2, 3, 4];
shuffle($outcome_order);

mt_srand($master_seed + 200);
$feature_order = [1, 2, 3, 4, 5, 6];
shuffle($feature_order);

mt_srand($master_seed + 300);
$faq_order = [1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11]; 
shuffle($faq_order);

// ─── VOCABULARY ENGINE: ACTIONS & ADJECTIVES ─────────────────────────────────
mt_srand($master_seed + 800);
if ($is_soft_niche) {
    $actions    = ['engage', 'welcome', 'support', 'guide', 'connect with', 'nurture'];
    $verbs_adj  = ['consistently', 'effortlessly', 'warmly', 'effectively', 'naturally'];
    $strong_adj = ['exceptionally', 'undeniably', 'deeply', 'absolutely', 'wonderfully'];
} else {
    $actions    = ['steal', 'capture', 'dominate', 'secure', 'claim', 'command'];
    $verbs_adj  = ['consistently', 'effortlessly', 'powerfully', 'relentlessly', 'automatically'];
    $strong_adj = ['exceptionally', 'undeniably', 'supremely', 'absolutely', 'formidably'];
}
$chosen_action     = $actions[mt_rand(0, count($actions) - 1)];
$chosen_verb_adj   = $verbs_adj[mt_rand(0, count($verbs_adj) - 1)];
$chosen_strong_adj = $strong_adj[mt_rand(0, count($strong_adj) - 1)];

// ─── FETCH CORE DATA FROM DATABASE ──────────────────────────────────────────
$niche_post = get_page_by_path($niche_slug, OBJECT, 'pseo_niche');
$niche_raw_meta = ($niche_post && !is_null($niche_post)) ? get_post_meta($niche_post->ID, '_pseo_niche_data', true) : [];

// ─────────────────────────────────────────────────────────────────────────────
// [v6.3] EXHAUSTIVE NICHE PLURAL OVERRIDES MAP
// ─────────────────────────────────────────────────────────────────────────────
$niche_plural_overrides = [
    // Photography & Creative
    'drone-photography'      => 'Drone Photography Businesses',
    'photography'            => 'Photography Studios',
    'interior-design'        => 'Interior Design Firms',
    'makeup-artist'          => 'Makeup Artists',
    'makeup-artistry'        => 'Makeup Artists',
    'tattoo-studio'          => 'Tattoo Studios',
    'nail-studio'            => 'Nail Studios',
    'yoga-studio'            => 'Yoga Studios',
    'skincare-brand'         => 'Skincare Brands',
    'fashion-boutique'       => 'Fashion Boutiques',

    // Professional Services
    'law-firm'               => 'Law Firms',
    'architecture-firm'      => 'Architecture Firms',
    'accounting'             => 'Accounting Firms',
    'consulting'             => 'Consulting Firms',
    'recruitment'            => 'Recruitment Agencies',
    'real-estate'            => 'Real Estate Brokerages',
    'event-planning'         => 'Event Planning Agencies',
    'travel-agency'          => 'Travel Agencies',
    'detective-agency'       => 'Detective Agencies',
    'car-rental'             => 'Car Rental Agencies',
    'wedding-planner'        => 'Wedding Planners',
    'security-company'       => 'Security Companies',
    'cooperative-society'    => 'Cooperative Societies',
    'bureau-de-change'       => 'Bureau de Change Operators',
    'insurance'              => 'Insurance Providers',

    // Tech & Finance
    'tech-startup'           => 'Tech Startups',
    'fintech'                => 'Fintech Startups',
    'software'               => 'Software Companies',
    'ecommerce'              => 'Ecommerce Brands',
    'crypto'                 => 'Crypto Platforms',
    'microfinance'           => 'Microfinance Institutions',
    'loan-company'           => 'Loan Companies',
    'mortgage-company'       => 'Mortgage Companies',
    'stock-brokerage'        => 'Stock Brokerages',
    'commodity-trading'      => 'Commodity Trading Companies',
    'pos-business'           => 'POS Businesses',

    // Health & Wellness
    'dental'                 => 'Dental Clinics',
    'hospital'               => 'Hospitals',
    'pharmacy'               => 'Pharmacies',
    'pharmacy-distributor'   => 'Pharmacy Distributors',
    'beauty-spa'             => 'Beauty Spas',
    'hair-salon'             => 'Hair Salons',
    'martial-arts-school'    => 'Martial Arts Schools',
    'sports-academy'         => 'Sports Academies',
    'driving-range'          => 'Driving Ranges',

    // Education
    'driving-school'         => 'Driving Schools',
    'music-school'           => 'Music Schools',
    'polytechnic'            => 'Polytechnics',
    'university'             => 'Universities',

    // Retail & Hospitality
    'bakery'                 => 'Bakeries',
    'supermarket'            => 'Supermarkets',
    'pet-store'              => 'Pet Stores',
    'electronics-store'      => 'Electronics Stores',
    'hardware-store'         => 'Hardware Stores',
    'hotel'                  => 'Hotels',
    'restaurant'             => 'Restaurants',
    'night-club'             => 'Night Clubs',
    'bar-and-lounge'         => 'Bars & Lounges',
    'car-wash'               => 'Car Wash Centers',
    'football-club'          => 'Football Clubs',

    // Manufacturing & Trade
    'furniture-company'      => 'Furniture Companies',
    'printing-company'       => 'Printing Companies',
    'haulage-company'        => 'Haulage Companies',
    'waterproofing-company'  => 'Waterproofing Companies',
    'roofing-company'        => 'Roofing Companies',
    'steel-fabrication'      => 'Steel Fabricators',
    'aluminium-fabrication'  => 'Aluminium Fabricators',
    'construction'           => 'Construction Firms',
    'borehole-drilling'      => 'Borehole Drilling Companies',

    // Logistics & Energy
    'logistics'              => 'Logistics Companies',
    'courier-service'        => 'Courier Services',
    'solar'                  => 'Solar Energy Companies',
    'oil-gas'                => 'Oil & Gas Companies',
    'waste-management'       => 'Waste Management Companies',

    // Agriculture
    'agriculture-company'    => 'Agriculture Companies',
    'cocoa-export'           => 'Cocoa Export Companies',
    'fish-farming'           => 'Fish Farms',
    'poultry-farming'        => 'Poultry Farms',
    'cassava-farming'        => 'Cassava Farms',
    'rice-milling'           => 'Rice Mills',
    'palm-oil-business'      => 'Palm Oil Businesses',

    // Non-Profit / Community
    'ngo'                    => 'NGOs',
    'church'                 => 'Churches',
];

$niche_plural = !empty($niche_raw_meta['plural'])
    ? $niche_raw_meta['plural']
    : ($niche_plural_overrides[$niche_slug] ?? $niche_name . 's');

// ─── JSON DATA INTEGRATION (Replaces old SQL Database Call) ────────────────
$json_path = __DIR__ . '/city-niche.json';
$city_niche_json = file_exists($json_path) ? json_decode(file_get_contents($json_path), true) : [];

// Always use the primary city_slug to fetch the baseline data for pricing and stats
$combo_key = "{$city_slug}_{$niche_slug}";
$matrix_data = $city_niche_json[$combo_key] ?? [];

// Setup Base Pricing Fallbacks
$matrix_data['avg_cost_low'] = !empty($matrix_data['avg_cost_low']) ? $matrix_data['avg_cost_low'] : 120000;
$matrix_data['avg_cost_typical'] = !empty($matrix_data['avg_cost_typical']) ? $matrix_data['avg_cost_typical'] : 200000;
$matrix_data['avg_cost_high'] = !empty($matrix_data['avg_cost_high']) ? $matrix_data['avg_cost_high'] : 420000;

// EXPOSE EXPLICIT PRICING VARIABLES FOR THE CALCULATOR MODULE
$cost_low = $avg_cost_low = $matrix_data['avg_cost_low'];
$cost_typical = $avg_cost_typical = $matrix_data['avg_cost_typical'];
$cost_high = $avg_cost_high = $matrix_data['avg_cost_high'];

// --- DYNAMIC HERO STAT CALCULATION ---
$digital_gap_percent = 0;
if (!empty($matrix_data['estimated_businesses']) && !empty($matrix_data['without_website'])) {
    $digital_gap_percent = round(($matrix_data['without_website'] / $matrix_data['estimated_businesses']) * 100);
}

// --- INJECT NEIGHBORHOOD OVERRIDES INTO MATRIX DATA ---
// 1. Define Niche Complexity Multipliers (Standard/Local niches default to 1.0x automatically)
$niche_multipliers = [
    // Tier 1: Enterprise, High-Security, & Tech Heavy
    'fintech' => 2.5, 'crypto' => 2.5, 'polytechnic' => 2.5, 'university' => 2.5, 
    'microfinance' => 2.0, 'software' => 2.0, 'oil-gas' => 2.0, 'stock-brokerage' => 2.0, 
    'tech-startup' => 1.8, 'ecommerce' => 1.8, 'hospital' => 1.8, 'commodity-trading' => 1.8, 
    'loan-company' => 1.8, 'mortgage-company' => 1.8, 'bureau-de-change' => 1.6, 'pos-business' => 1.5,
    
    // Tier 2: Corporate, Premium B2B, & Advanced Bookings
    'real-estate' => 1.5, 'insurance' => 1.5, 'construction' => 1.5, 'law-firm' => 1.4, 
    'architecture-firm' => 1.4, 'logistics' => 1.4, 'haulage-company' => 1.4, 'cocoa-export' => 1.4, 
    'night-club' => 1.4, 'hotel' => 1.4, 'accounting' => 1.3, 'travel-agency' => 1.3, 'ngo' => 1.3, 
    'solar' => 1.3, 'security-company' => 1.3, 'detective-agency' => 1.3, 'cooperative-society' => 1.3, 
    'bar-and-lounge' => 1.3, 'agriculture-company' => 1.2, 'courier-service' => 1.2, 'recruitment' => 1.2,
    'pharmacy-distributor' => 1.3, 'car-rental' => 1.3, 'printing-company' => 1.3, 'borehole-drilling' => 1.2,
    'waterproofing-company' => 1.2, 'roofing-company' => 1.2, 'steel-fabrication' => 1.2, 'aluminium-fabrication' => 1.2,
    'furniture-company' => 1.2, 'electronics-store' => 1.2, 'hardware-store' => 1.2, 'driving-range' => 1.3,
    'sports-academy' => 1.3, 'football-club' => 1.3, 'fish-farming' => 1.2, 'poultry-farming' => 1.2,
    'cassava-farming' => 1.2, 'rice-milling' => 1.2, 'palm-oil-business' => 1.2, 'waste-management' => 1.2
];

$niche_complexity_multiplier = isset($niche_multipliers[$niche_slug]) ? $niche_multipliers[$niche_slug] : 1.0;

if (!empty($nb_data)) {
    if (isset($nb_data['avg_cost_low'])) {
        $matrix_data['avg_cost_low'] = round(($nb_data['avg_cost_low'] * $niche_complexity_multiplier) / 10000) * 10000;
    }
    if (isset($nb_data['avg_cost_high'])) {
        $matrix_data['avg_cost_high'] = round(($nb_data['avg_cost_high'] * $niche_complexity_multiplier) / 10000) * 10000;
    }
    if (isset($matrix_data['avg_cost_low']) && isset($matrix_data['avg_cost_high'])) {
        $matrix_data['avg_cost_typical'] = round((($matrix_data['avg_cost_low'] + $matrix_data['avg_cost_high']) / 2.2) / 10000) * 10000;
    }

    if (isset($nb_data['price_context'])) $matrix_data['price_context'] = $nb_data['price_context'];
    if (isset($nb_data['competitive_landscape'])) $matrix_data['competitive_landscape'] = $nb_data['competitive_landscape'];
    if (isset($nb_data['local_insight'])) $matrix_data['local_insight'] = $nb_data['local_insight'];
}

// --- THE AI DATABASE CLEANSER (INTERCEPTOR) ---
if ($is_non_profit && !empty($matrix_data)) {
    $repl_nonprofit_db = [
        'businesses' => 'organisations', 'business' => 'organisation', 'customers' => 'supporters',
        'clients' => 'community members', 'revenue' => 'impact', 'money' => 'time', 'sales' => 'support',
        'competitors' => 'other organisations', 'competitor' => 'another option', 'buyers' => 'members',
        'commercial' => 'community', 'industry' => 'sector', 'market share' => 'community reach',
    ];
    array_walk_recursive($matrix_data, function(&$value) use ($repl_nonprofit_db) {
        if (is_string($value)) {
            $value = str_ireplace(array_keys($repl_nonprofit_db), array_values($repl_nonprofit_db), $value);
        }
    });
} elseif ($is_education && !empty($matrix_data)) {
    $repl_edu_db = [
        'businesses' => 'institutions', 'business' => 'institution', 'customers' => 'students',
        'clients' => 'parents and students', 'revenue' => 'enrollment', 'money' => 'time', 'sales' => 'admissions',
        'competitors' => 'other institutions', 'competitor' => 'another school', 'buyers' => 'applicants',
        'commercial' => 'academic', 'industry' => 'education sector', 'market share' => 'enrollment figures',
        'dominate' => 'lead', 'crushing' => 'outpacing'
    ];
    array_walk_recursive($matrix_data, function(&$value) use ($repl_edu_db) {
        if (is_string($value)) {
            $value = str_ireplace(array_keys($repl_edu_db), array_values($repl_edu_db), $value);
        }
    });
}

$raw_competitors = get_post_meta($city_post->ID, '_pseo_city_competitors', true) ?: [];
$city_competitors = [];
foreach ((array)$raw_competitors as $comp_str) {
    $parts = explode(':', $comp_str);
    if (!empty($parts[0])) $city_competitors[] = trim($parts[0]);
}

// ─── SERVICE-TYPE VOCABULARY REGISTER ────────────────────────────────────────
$service_vocab_map = [
    'website-designer' => [
        'sv_identity'  => 'website designer',
        'sv_action'    => '{design and build|create|launch}',
        'sv_output'    => '{a clean, highly professional website|a modern, easy-to-navigate website|a premium online presence}',
        'sv_proof'     => '{builds instant credibility|makes a perfect first impression|highlights your actual value}',
        'sv_angle'     => '{visual-first|design-driven|aesthetics-led|brand-forward}',
        'sv_tools'     => '{clear messaging, beautiful layouts, and simple contact forms|modern design principles and easy-to-use layouts|professional branding and straightforward user experiences}',
        'sv_clientwin' => "{look like the absolute best option in town|win over customers before they even speak to you|charge premium rates because your brand looks premium}",
    ],
    'website-developer' => [
        'sv_identity'  => 'website developer',
        'sv_action'    => '{build|develop|create}',
        'sv_output'    => '{a fast, reliable website|a highly functional web platform|a smooth, glitch-free digital presence}',
        'sv_proof'     => '{loads instantly on mobile phones|ranks perfectly on Google searches|works perfectly around the clock}',
        'sv_angle'     => '{technically rigorous|performance-obsessed|code-quality-focused|engineering-led}',
        'sv_tools'     => '{fast-loading pages, secure hosting, and mobile-friendly layouts|clean code, proper security, and seamless mobile responsiveness}',
        'sv_clientwin' => '{never lose a customer to a slow, frustrating web page again|get found much easier on Google|own a dependable platform that grows with your business}',
    ],
    'web-design-agency' => [
        'sv_identity'  => 'web design agency',
        'sv_action'    => '{deliver|build and manage|launch}',
        'sv_output'    => '{a complete digital marketing presence|a full-service website solution|an end-to-end online platform}',
        'sv_proof'     => '{handles your marketing while you run your business|takes the stress out of getting online|brings everything together in one place}',
        'sv_angle'     => '{full-service|team-backed|strategy-driven|results-focused}',
        'sv_tools'     => '{smart design, clear copywriting, and reliable ongoing support|a dedicated team approach, modern design, and everyday reliability}',
        'sv_clientwin' => '{focus on running your business while we handle the digital side|get a complete, stress-free digital upgrade|stop worrying about your website and start getting actual results}',
    ],
    'design-services' => [
        'sv_identity'  => 'design service provider',
        'sv_action'    => '{craft|design|create}',
        'sv_output'    => '{a tailored, industry-specific website|a highly focused digital presence|a custom web platform}',
        'sv_proof'     => '{speaks directly to what your specific customers care about|highlights your unique expertise|shows exactly why you are different from generic competitors}',
        'sv_angle'     => '{specialist|authority-grade|expert-backed|industry-specific}',
        'sv_tools'     => '{industry-specific layouts, persuasive wording, and clear call-to-actions|targeted messaging, professional imagery, and smart layouts}',
        'sv_clientwin' => "{stop looking like every other generic business|attract the exact type of clients you actually want to work with|communicate your true value instantly}",
    ],
    'branding-agencies' => [
        'sv_identity'  => 'branding agency',
        'sv_action'    => '{build|shape|develop}',
        'sv_output'    => '{a powerful, trustworthy brand presence|a highly respected digital identity|a website that perfectly captures your brand}',
        'sv_proof'     => '{makes people remember you|commands respect instantly|turns your business into a recognized local name}',
        'sv_angle'     => '{brand-first|ROI-focused|consultative|business-objective-driven}',
        'sv_tools'     => '{consistent visuals, powerful storytelling, and strategic positioning|professional branding, clear messaging, and trust-building design}',
        'sv_clientwin' => "{become the go-to choice in your area|build deep trust with your audience|stand out as a true leader in your field}",
    ],
];

$sv = $service_vocab_map['website-designer']; 
$exact_format_slug = 'website-designer'; 

if (strpos($service_slug, 'developer') !== false) {
    $sv = $service_vocab_map['website-developer'];
    $exact_format_slug = 'website-developer';
} elseif (strpos($service_slug, 'agency') !== false && strpos($service_slug, 'branding') === false) {
    $sv = $service_vocab_map['web-design-agency'];
    $exact_format_slug = 'web-design-agency';
} elseif (strpos($service_slug, 'branding') !== false) {
    $sv = $service_vocab_map['branding-agencies'];
    $exact_format_slug = 'branding-agency';
} elseif (strpos($service_slug, 'services') !== false) {
    $sv = $service_vocab_map['design-services'];
    $exact_format_slug = 'website-design-services';
}

// Strip commercial terms from SV if soft niche
if ($is_non_profit) {
    $sv['sv_clientwin'] = "{look like the absolute best option|welcome visitors before they even speak to you|build deep trust because your platform looks premium}";
} elseif ($is_education) {
    $sv['sv_clientwin'] = "{look like the premier institution in your area|impress parents before they even speak to you|build absolute trust because your platform looks world-class}";
}

// ─── THE NEW DYNAMIC COPY THEME ENGINE ───────────────────────────────────────
$boilerplates_file = __DIR__ . '/global-boilerplates.json';
$_bp_raw = file_exists($boilerplates_file) ? json_decode(file_get_contents($boilerplates_file), true) : null;
$boilerplates = [
    'common' => (isset($_bp_raw['common']) && is_array($_bp_raw['common'])) ? $_bp_raw['common'] : [],
    'themes' => (isset($_bp_raw['themes']) && is_array($_bp_raw['themes']) && count($_bp_raw['themes']) > 0) ? $_bp_raw['themes'] : [[]],
];

$theme_count = count($boilerplates['themes']);
$theme_index = $master_seed % ($theme_count > 0 ? $theme_count : 1);
$selected_theme = isset($boilerplates['themes'][$theme_index]) ? $boilerplates['themes'][$theme_index] : [];

$db_themes = ($niche_post && !is_null($niche_post)) ? get_post_meta($niche_post->ID, $exact_format_slug . '_ai_themes', true) : [];
if (!empty($db_themes) && is_array($db_themes)) {
    $db_theme_count = count($db_themes);
    if ($db_theme_count > 0) {
        $safe_db_index  = $theme_index % $db_theme_count;
        $raw_ai_theme   = isset($db_themes[$safe_db_index]) && is_array($db_themes[$safe_db_index]) ? $db_themes[$safe_db_index] : [];
        $active_ai_theme = array_filter($raw_ai_theme, function($value) { return is_string($value) && !empty(trim($value)); });
        $selected_theme  = array_merge($selected_theme, $active_ai_theme);
    }
}

$fallbacks = array_merge($boilerplates['common'], $selected_theme);

// --- NON-PROFIT & EDUCATION BOILERPLATE OVERRIDE INTERCEPTOR ---
if ($is_non_profit) {
    $fallbacks['reality_p1'] = "The modern seeker in {$city_name} values connection and authenticity. When people look for spiritual guidance or community support, they turn to the internet first. If your digital presence is outdated, they might miss out on the incredible community you've built.";
    $fallbacks['reality_p2'] = "Mobile searches for local {niche_plural} have skyrocketed. While your physical location is vital, your digital doors must always be open. A welcoming, highly-optimized website transforms your {niche_name} into a beacon for your neighborhood.";
    $fallbacks['reality_p3'] = "We eliminate the friction between a curious visitor and an active community member. As your {keyword}, we use modern design to launch a beautiful platform that makes joining your community the easiest part of their week.";
    $fallbacks['reality_p4'] = "Turn every smartphone in {$city_name} into a portal to your ministry. We give you the technology to scale your impact without boundaries.";

    $fallbacks['pos1_title'] = "Increase Local Visibility";
    $fallbacks['pos1_desc'] = "We optimize your platform to rank #1 when people in specific {$city_name} neighborhoods search for spiritual community or support near them.";
    $fallbacks['pos2_title'] = "Streamline Communication";
    $fallbacks['pos2_desc'] = "Your website clearly answers common questions, shares service times, and provides instant access to resources, saving your staff hours of manual replies.";
    $fallbacks['pos3_title'] = "Seamless Online Giving";
    $fallbacks['pos3_desc'] = "We integrate secure, seamless payment gateways like Paystack and Flutterwave, allowing members to safely send tithes, offerings, or donations online.";
    $fallbacks['pos4_title'] = "24/7 Digital Ministry";
    $fallbacks['pos4_desc'] = "While your physical doors are closed, your website is actively sharing sermons, accepting prayer requests, and welcoming new visitors.";
} elseif ($is_education) {
    $fallbacks['reality_p1'] = "Modern parents and prospective students in {$city_name} value transparency and prestige. When deciding on the right institution, they turn to the internet first. If your digital presence is outdated, they might overlook your academic excellence.";
    $fallbacks['reality_p2'] = "Mobile searches for local {niche_plural} have skyrocketed. While your campus facilities are vital, your digital front door must always be welcoming. A highly-optimized website transforms your {niche_name} into the top choice for families.";
    $fallbacks['reality_p3'] = "We eliminate the friction between a curious parent and an enrolled student. As your {keyword}, we use modern design to launch a beautiful platform that makes admissions and communication effortless.";
    $fallbacks['reality_p4'] = "Turn every smartphone in {$city_name} into a portal to your admissions office. We give you the technology to scale your enrollment without boundaries.";

    $fallbacks['pos1_title'] = "Increase Enrollment Visibility";
    $fallbacks['pos1_desc'] = "We optimize your platform to rank #1 when families in specific {$city_name} neighborhoods search for quality education near them.";
    $fallbacks['pos2_title'] = "Streamline Admissions";
    $fallbacks['pos2_desc'] = "Your website clearly answers common questions, shares curriculum details, and provides instant access to admission forms, saving your staff hours.";
    $fallbacks['pos3_title'] = "Seamless Online Payments";
    $fallbacks['pos3_desc'] = "We integrate secure payment gateways like Paystack and Flutterwave, allowing parents to safely pay tuition and fees online.";
    $fallbacks['pos4_title'] = "24/7 Digital Campus";
    $fallbacks['pos4_desc'] = "While your physical gates are closed, your website is actively sharing student achievements, accepting applications, and welcoming new visitors.";
}

if ($niche_post && $niche_post->post_status === 'publish') {
    $core_keys = [
        'feat_headline', 'feat_subline', 
        'f1_title', 'f1_desc', 'f2_title', 'f2_desc', 'f3_title', 'f3_desc', 'f4_title', 'f4_desc', 'f5_title', 'f5_desc', 'f6_title', 'f6_desc', 
        'faq1_q', 'faq1_a', 'faq2_q', 'faq2_a', 'faq3_q', 'faq3_a', 'faq4_q', 'faq4_a', 'faq5_q', 'faq5_a',
        // GSO Content Engine — extended prose & FAQs
        'reality_p5', 'reality_p6',
        'faq6_q', 'faq6_a', 'faq7_q', 'faq7_a', 'faq8_q', 'faq8_a',
        'faq9_q', 'faq9_a', 'faq10_q', 'faq10_a', 'faq11_q', 'faq11_a',
        'cta_aspiration'
    ];
    foreach ($core_keys as $ck) {
        $format_specific_val = get_post_meta($niche_post->ID, $exact_format_slug . '_' . $ck, true);
        $val = !empty($format_specific_val) ? $format_specific_val : get_post_meta($niche_post->ID, $ck, true);
        if (!empty($val)) $fallbacks[$ck] = $val;
    }
}

if (!empty($niche_raw_meta['niche_faq_1']) && !empty($niche_raw_meta['niche_faq_1_answer'])) {
    $fallbacks['faq4_q'] = $niche_raw_meta['niche_faq_1'];
    $fallbacks['faq4_a'] = $niche_raw_meta['niche_faq_1_answer'];
}
if (!empty($niche_raw_meta['niche_faq_2']) && !empty($niche_raw_meta['niche_faq_2_answer'])) {
    $fallbacks['faq5_q'] = $niche_raw_meta['niche_faq_2'];
    $fallbacks['faq5_a'] = $niche_raw_meta['niche_faq_2_answer'];
}
if (!empty($matrix_data['what_most_lack'])) $fallbacks['reality_p2'] = $matrix_data['what_most_lack'];
if (!empty($matrix_data['local_insight'])) $fallbacks['reality_p4'] = $matrix_data['local_insight'];
if (!empty($matrix_data['unique_faq']) && !empty($matrix_data['unique_faq_answer'])) {
    $fallbacks['faq3_q'] = $matrix_data['unique_faq'];
    $fallbacks['faq3_a'] = $matrix_data['unique_faq_answer'];
}

$replacements = [
    '{city_name}'        => $city_name,
    '{niche_name}'       => $niche_name,
    '{niche_plural}'     => $niche_plural,
    '{service_name}'     => $service_name,
    '{exact_keyword}'    => $exact_keyword,
    '{keyword}'          => $exact_keyword,
    '{lsi_keyword}'      => $lsi_keyword,
    '{sv_identity}'      => $sv['sv_identity'],
    '{sv_action}'        => $sv['sv_action'],
    '{sv_output}'        => $sv['sv_output'],
    '{sv_proof}'         => $sv['sv_proof'],
    '{sv_tools}'         => $sv['sv_tools'],
    '{sv_clientwin}'     => $sv['sv_clientwin'],
    '{verb_action}'      => $chosen_action,
    '{verb_adjective}'   => $chosen_verb_adj,
    '{adjective_strong}' => $chosen_strong_adj
];

foreach ($fallbacks as $k => $v) {
    $fallbacks[$k] = str_replace(array_keys($replacements), array_values($replacements), $v);
}

// ─── FINAL CLEANSE: NOUNS AND VERBS OVERRIDES ───────────────────────────────

$niche_noun_overrides = [
    'law-firm'          => ['business' => 'practice',   'businesses' => 'practices'],
    'architecture'      => ['business' => 'firm',       'businesses' => 'firms'],
    'architecture-firm' => ['business' => 'practice',   'businesses' => 'practices'],
    'accounting'        => ['business' => 'firm',       'businesses' => 'firms'],
    'consulting'        => ['business' => 'firm',       'businesses' => 'firms'],
    'travel-agency'     => ['business' => 'operations', 'businesses' => 'operations'],
    'recruitment'       => ['business' => 'agency',     'businesses' => 'agencies'],
    'real-estate'       => ['business' => 'brokerage',  'businesses' => 'brokerages'],
    'event-planning'    => ['business' => 'agency',     'businesses' => 'agencies'],
    'photography'       => ['business' => 'studio',     'businesses' => 'studios'],
    'interior-design'   => ['business' => 'studio',     'businesses' => 'studios'],
    'dental'            => ['business' => 'clinic',     'businesses' => 'clinics'],
    'hospital'          => ['business' => 'facility',   'businesses' => 'facilities'],
    'pharmacy'          => ['business' => 'store',      'businesses' => 'stores'],
    'school'            => ['business' => 'institution','businesses' => 'institutions'],
    'driving-school'    => ['business' => 'academy',    'businesses' => 'academies'],
    'music-school'      => ['business' => 'academy',    'businesses' => 'academies'],
    'microfinance'      => ['business' => 'institution','businesses' => 'institutions'],
    'crypto'            => ['business' => 'platform',   'businesses' => 'platforms'],
    'insurance'         => ['business' => 'provider',   'businesses' => 'providers'],
    'tech-startup'      => ['business' => 'company',    'businesses' => 'companies'],
    'fintech'           => ['business' => 'startup',    'businesses' => 'startups'],
    'software'          => ['business' => 'company',    'businesses' => 'companies'],
    'oil-gas'           => ['business' => 'corporation','businesses' => 'corporations'],
    'logistics'         => ['business' => 'company',    'businesses' => 'companies'],
    'construction'      => ['business' => 'firm',       'businesses' => 'firms'],
    'hotel'             => ['business' => 'property',   'businesses' => 'properties'],
    'restaurant'        => ['business' => 'establishment','businesses'=>'establishments'],
    'beauty-spa'        => ['business' => 'salon',      'businesses' => 'salons'],
    'ecommerce'         => ['business' => 'brand',      'businesses' => 'brands'],
    'fashion-boutique'  => ['business' => 'brand',      'businesses' => 'brands'],
    'pet-store'         => ['business' => 'operations', 'businesses' => 'operations'],
    'supermarket'       => ['business' => 'outlet',     'businesses' => 'outlets'],
    'bakery'            => ['business' => 'bakery',     'businesses' => 'bakeries'],
    'waste-management'  => ['business' => 'company',    'businesses' => 'companies'],
    'wedding-planner'   => ['business' => 'agency',     'businesses' => 'agencies'],
    'polytechnic'       => ['business' => 'institution','businesses' => 'institutions'],
    'university'        => ['business' => 'institution','businesses' => 'institutions'],
    'car-rental'        => ['business' => 'agency',     'businesses' => 'agencies'],
    'printing-company'  => ['business' => 'press',      'businesses' => 'presses'],
    'courier-service'   => ['business' => 'service',    'businesses' => 'services'],
    'security-company'  => ['business' => 'firm',       'businesses' => 'firms'],
    'detective-agency'  => ['business' => 'agency',     'businesses' => 'agencies'],
    'cooperative-society'=>['business' => 'society',    'businesses' => 'societies'],
    'bureau-de-change'  => ['business' => 'operations', 'businesses' => 'operations'],
    'fish-farming'      => ['business' => 'farm',       'businesses' => 'farms'],
    'poultry-farming'   => ['business' => 'farm',       'businesses' => 'farms'],
    'cassava-farming'   => ['business' => 'farm',       'businesses' => 'farms'],
    'rice-milling'      => ['business' => 'mill',       'businesses' => 'mills'],
    'night-club'        => ['business' => 'club',       'businesses' => 'clubs'],
    'bar-and-lounge'    => ['business' => 'lounge',     'businesses' => 'lounges'],
    'car-wash'          => ['business' => 'center',     'businesses' => 'centers'],
    'sports-academy'    => ['business' => 'academy',    'businesses' => 'academies'],
    'football-club'     => ['business' => 'club',       'businesses' => 'clubs'],
    'yoga-studio'       => ['business' => 'studio',     'businesses' => 'studios'],
    'martial-arts-school'=>['business' => 'school',     'businesses' => 'schools'],
    'tattoo-studio'     => ['business' => 'studio',     'businesses' => 'studios'],
    'nail-studio'       => ['business' => 'studio',     'businesses' => 'studios'],
    'hair-salon'        => ['business' => 'salon',      'businesses' => 'salons'],
    'skincare-brand'    => ['business' => 'brand',      'businesses' => 'brands'],
];

if (!$is_non_profit) {
    if (isset($niche_noun_overrides[$niche_slug])) {
        $overrides = $niche_noun_overrides[$niche_slug];
        foreach ($fallbacks as $k => $v) {
            $v = preg_replace('/\bbusinesses\b/i', $overrides['businesses'], $v);
            $v = preg_replace('/\bbusiness\b/i', $overrides['business'], $v);
            $fallbacks[$k] = $v;
        }
    }
}

if ($is_non_profit) {
    $repl_nonprofit = [
        'businesses' => 'organisations', 'business' => 'organisation', 'customers' => 'supporters',
        'clients' => 'community members', 'revenue' => 'impact', 'money' => 'time', 'sales' => 'support',
        'competitors' => 'other organisations', 'competitor' => 'another option',
        "charge what you're worth" => 'build absolute trust', 'buy from you' => 'support your cause',
        'commercial' => 'community', 'industry' => 'sector', 'market share' => 'community reach',
    ];
    foreach ($fallbacks as $k => $v) { $fallbacks[$k] = str_ireplace(array_keys($repl_nonprofit), array_values($repl_nonprofit), $v); }
} elseif ($is_education) {
    $repl_edu = [
        'businesses' => 'institutions', 'business' => 'institution',
        'customers' => 'students',
        'clients' => 'parents and students', 'revenue' => 'enrollments', 'money' => 'time', 'sales' => 'admissions',
        'competitors' => 'other institutions', 'competitor' => 'another school',
        "charge what you're worth" => 'build lasting prestige', 'buy from you' => 'enroll with you',
        'commercial' => 'academic', 'industry' => 'education sector', 'market share' => 'enrollment growth',
        'dominate' => 'lead', 'crushing' => 'outperforming'
    ];
    foreach ($fallbacks as $k => $v) { $fallbacks[$k] = str_ireplace(array_keys($repl_edu), array_values($repl_edu), $v); }
}

$meta = [];
foreach ($fallbacks as $k => $v) { $meta[$k] = go_spin_text($v); }

// ─── DYNAMIC PORTFOLIO SHUFFLER ──────────────────────────────────────────────
$all_projects = [
    ['name' => 'RAFFLEKINGS', 'url' => '/work/rafflekings', 'img' => 'https://getonlinestudio.com/insights/wp-content/uploads/2026/03/69c056ef61d91271e5147a38.jpg', 'tag' => 'Fintech', 'desc' => 'A custom, lightning-fast platform that scales effortlessly under load.'],
    ['name' => 'VISIONAFRIC', 'url' => '/work/visionafric', 'img' => 'https://getonlinestudio.com/insights/wp-content/uploads/2026/03/69bfd90c35c39c6a8b6658d9.jpg', 'tag' => 'NGO', 'desc' => 'A powerful storytelling platform that commands global trust.'],
    ['name' => 'DEKOMPANY', 'url' => '/work/dekompany', 'img' => 'https://getonlinestudio.com/insights/wp-content/uploads/2026/03/69bfda6324e30f2c3662f4cc.jpg', 'tag' => 'Agency', 'desc' => 'A premium consulting platform engineered for high-conversion funnels.'],
];
shuffle($all_projects);
$display_projects = array_slice($all_projects, 0, 2);

if ($is_non_profit) {
    $reality_headline = go_spin_text("{Digital trust for {$niche_plural}.|Built for {$city_name} {$niche_plural}.}");
} elseif ($is_education) {
    $reality_headline = go_spin_text("{Academic excellence online.|Built for {$city_name} {$niche_plural}.|Empowering education in {$city_name}.}");
} else {
    $reality_headline = go_spin_text("{Built for {$city_name} {$niche_plural}.|Dominating the {$city_name} market.|Win the {$city_name} market.}");
}

// --- CITY / NEIGHBORHOOD INTRO ENGINE ---
$city_intro = '';
if (!empty($nb_data['neighborhood_uniqueness'])) {
    $city_intro = $nb_data['neighborhood_uniqueness'];
    $city_intro = str_replace(['{Niche}', '{niche}', '{Service}', '{service}'], [$niche_name, strtolower($niche_name), $service_name, strtolower($service_name)], $city_intro);
    $city_intro = go_spin_text($city_intro); 
} elseif (isset($city_post)) {
    $city_intro = get_post_meta($city_post->ID, 'city_intro', true);
    if (empty($city_intro) && !empty($city_post->post_content)) {
        $city_intro = wp_strip_all_tags($city_post->post_content);
    }
    if (!empty($city_intro)) {
        $city_intro = str_replace(['{Niche}', '{niche}', '{Service}', '{service}'], [$niche_name, strtolower($niche_name), $service_name, strtolower($service_name)], $city_intro);
        $city_intro = go_spin_text($city_intro); 
    }
}

// ─── INTERNAL LINK ENGINE ────────────────────────────────────────────────────
$active_cities_query = get_posts(['post_type' => 'pseo_location', 'numberposts' => -1, 'post_status' => 'publish', 'orderby' => 'title', 'order' => 'ASC']);
$all_cities = [];
foreach ((array)$active_cities_query as $c) { $all_cities[$c->post_name] = $c->post_title; }
if (empty($all_cities)) $all_cities = [$city_slug => $base_city_name];

$other_niches = array_diff_key($niche_dict, [$niche_slug => '']);
$num_niches_to_pick = min(4, count($other_niches));
$horizontal_links = [];

if ($num_niches_to_pick > 0) {
    mt_srand($master_seed + 99); 
    $random_related_keys = array_rand($other_niches, $num_niches_to_pick);
    foreach ((array)$random_related_keys as $picked_niche) {
        $url_path = !empty($neighborhood_slug) ? "/locations/{$city_slug}/{$neighborhood_slug}/" : "/locations/{$city_slug}/";
        $horizontal_links[] = [
            'label' => $niche_dict[$picked_niche] . ' ' . $service_name . ' in ' . $city_name, 
            'url'   => "{$url_path}{$picked_niche}-{$service_slug}/"
        ];
    }
}

$other_city_slugs = array_values(array_filter(array_keys($all_cities), fn($c) => $c !== $city_slug));
mt_srand($master_seed + 999);
shuffle($other_city_slugs);

$vertical_links = [];
foreach (array_slice($other_city_slugs, 0, 4) as $picked_city) {
    $vertical_links[] = ['label' => $niche_name . ' ' . $service_name . ' in ' . $all_cities[$picked_city], 'url' => "/locations/{$picked_city}/{$niche_slug}-{$service_slug}/"];
}

// --- NEIGHBORHOOD HUB LINKS (STRICTLY FILTERED BY DATABASE) ---
$neighborhood_links = [];
$neighborhoods_file = __DIR__ . '/neighborhoods.json';
if (file_exists($neighborhoods_file)) {
    $neighborhood_data = json_decode(file_get_contents($neighborhoods_file), true);
    if (isset($neighborhood_data[$city_slug])) {
        $active_nb = get_post_meta($city_post->ID, '_pseo_active_neighborhoods', true);
        if (!is_array($active_nb)) $active_nb = [];
        
        foreach ($neighborhood_data[$city_slug] as $n_slug => $n_name) {
            if ($neighborhood_slug === $n_slug) continue;
            if (in_array($n_slug, $active_nb)) {
                $neighborhood_links[] = [
                    'label' => $niche_name . ' ' . $service_name . ' in ' . $n_name,
                    'url'   => "/locations/{$city_slug}/{$n_slug}/{$niche_slug}-{$service_slug}/"
                ];
            }
        }
        
        if (count($neighborhood_links) > 12) {
            mt_srand($master_seed + 123);
            shuffle($neighborhood_links);
            $neighborhood_links = array_slice($neighborhood_links, 0, 12);
        }
    }
}

$hub_link_url = "/locations/{$city_slug}/";
$hub_link_variants = [
    "Web Design Company in {$base_city_name} — All Services",
    "Website Designer in {$base_city_name} — All Services",
    "Website Developer in {$base_city_name} — All Services",
    "Website Design Firm in {$base_city_name} — All Services",
    "Web Design Agency in {$base_city_name} — All Services",
];
$hub_link_label = $hub_link_variants[abs(crc32($niche_slug)) % count($hub_link_variants)];
$hub_link = ['label' => $hub_link_label, 'url' => $hub_link_url];

// 4. Dynamic SEO Meta Generation
$global_settings = get_option('pseo_global_settings', [
    'meta_title' => '{Top|Best|Premium} {niche} {service} in {city} | GetOnline Studio',
    'meta_desc'  => '',
    'og_image'   => 'https://getonlinestudio.com/insights/wp-content/uploads/2026/02/GetOnline_Studio_Logo.jpg',
]);

$_meta_desc_variants = [
    "Looking for the best {$niche_name} Web Designer in {$city_name}? We build powerful websites for {$niche_plural} and organisations. Get started today.",
    "Do you need the best {$niche_name} Web Design Agency in {$city_name} for your organisation? We build professional websites and apps that help you achieve your vision.",
    "Searching for a trusted {$niche_name} {$service_name} in {$city_name}? GetOnline Studio builds high-converting digital platforms that bring real results for {$niche_plural}.",
    "Need a reliable {$niche_name} {$service_name} in {$city_name}? We design and build custom websites that make your {$niche_name} stand out and attract more {$vocab['audience']}.",
    "Looking for the top {$niche_name} {$service_name} in {$city_name}? Our team builds modern, fast, and beautiful websites tailored specifically for {$niche_plural} in Nigeria.",
];
$_meta_desc_index = abs($master_seed) % count($_meta_desc_variants);
$meta_desc = $_meta_desc_variants[$_meta_desc_index];

// ─── FINAL SPIN SEED ─────────────────────────────────────────────────────────
mt_srand($master_seed + 700);

$meta_title = go_spin_text(str_replace(['{niche}', '{city}', '{service}'], [$niche_name, $city_name, $service_name], $global_settings['meta_title']));

// ─── CANONICAL URL — Hardcoded to prevent /wp/ leakage from WP Core ──────────
if (!empty($neighborhood_slug)) {
    $canonical_url = GO_SITE_URL . "/locations/{$city_slug}/{$neighborhood_slug}/{$niche_slug}-{$service_slug}/";
} else {
    $canonical_url = GO_SITE_URL . "/locations/{$city_slug}/{$niche_slug}-{$service_slug}/";
}

// Schema uses the same clean canonical
$local_schema = [
    "@context"    => "https://schema.org",
    "@type"       => "ProfessionalService",
    "name"        => "GetOnline Studio " . $city_name,
    "url"         => $canonical_url,
    "logo"        => "https://getonlinestudio.com/insights/wp-content/uploads/2026/02/GetOnline_Studio_Logo.jpg",
    "image"       => "https://getonlinestudio.com/insights/wp-content/uploads/2026/02/GetOnline_Studio_Logo.jpg",
    "description" => wp_strip_all_tags($meta_desc),
    "telephone"   => "+2349061150443",
    "email"       => "hello@getonlinestudio.com",
    "address"     => [
        "@type"           => "PostalAddress", 
        "addressLocality" => $base_city_name, 
        "addressCountry"  => "NG"
    ],
];

// Hyper-Local Schema Override
if (!empty($neighborhood_name)) {
    $local_schema["areaServed"] = [
        "@type" => "Place",
        "name"  => $neighborhood_name,
        "containedInPlace" => [
            "@type" => "City",
            "name"  => $base_city_name
        ]
    ];
} else {
    $local_schema["areaServed"] = [
        "@type"          => "City", 
        "name"           => $base_city_name, 
        "addressCountry" => "NG"
    ];
}

$local_schema_json = json_encode($local_schema, JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT);

// 6. FAQ Schema
$faq_schema = [];
foreach ($faq_order as $i) {
    if (!empty($meta["faq{$i}_q"]) && !empty($meta["faq{$i}_a"])) {
        $faq_schema[] = [
            "@type" => "Question", "name" => wp_strip_all_tags(go_safe_text($meta["faq{$i}_q"])),
            "acceptedAnswer" => ["@type" => "Answer", "text" => wp_strip_all_tags(go_safe_text($meta["faq{$i}_a"]))],
        ];
    }
}
$faq_schema_json = !empty($faq_schema) ? json_encode(["@context" => "https://schema.org", "@type" => "FAQPage", "mainEntity" => $faq_schema], JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT) : '';

// =============================================================================
// MACRO-MODULARITY HTML RENDERING ENGINE
// =============================================================================
$html_blocks = [];

// MODULE 1: LOCAL FOCUS (Qualitative City Story)
ob_start();
if (!empty($city_intro)): ?>
<section class="py-16 md:py-20 px-4 md:px-6 bg-[#0a0a0a] border-y border-lavender/10 relative z-10">
    <div class="max-w-4xl mx-auto">
        <div class="bg-sharp-purple/10 border border-sharp-purple/20 p-8 md:p-12 rounded-3xl relative overflow-hidden">
            <div class="absolute top-0 left-0 w-2 h-full bg-sharp-purple"></div>
            <div class="flex items-center gap-3 mb-4">
                <div class="w-10 h-10 rounded-full bg-sharp-purple/20 flex items-center justify-center">
                    <i data-lucide="map-pin" class="w-5 h-5 text-sharp-purple"></i>
                </div>
                <h3 class="font-syne text-lg font-bold tracking-widest uppercase text-white"><?= go_safe_text($city_name) ?> Market Focus</h3>
            </div>
            <div class="font-manrope text-lavender/90 leading-relaxed text-base md:text-lg">
                <?= nl2br(go_safe_text($city_intro)) ?>
            </div>
        </div>
    </div>
</section>
<?php endif; 
$html_blocks['local_focus'] = ob_get_clean();

// MODULE 1B: THE DIGITAL GAP (Quantitative Data Integration)
ob_start();
if (!empty($matrix_data['estimated_businesses'])): 
    $est = number_format($matrix_data['estimated_businesses']);
    $with_web = number_format($matrix_data['with_website']);
    $without_web = number_format($matrix_data['without_website']);
    
    $landscape_text = !empty($matrix_data['competitive_landscape']) ? $matrix_data['competitive_landscape'] : "The top-ranking " . strtolower($niche_plural) . " in {$base_city_name} all share one thing: a professional web presence that captures customers before competitors even know they're searching.";
    $edge_text = !empty($matrix_data['top_ranked_edge']) ? $matrix_data['top_ranked_edge'] : "";
?>
<section class="py-16 md:py-24 px-4 md:px-6 bg-[#0B0A0F] border-b border-lavender/10 relative z-10">
    <div class="max-w-5xl mx-auto">
        <div class="bg-gradient-to-r from-[#15121c] to-transparent border border-lavender/10 p-8 md:p-12 rounded-3xl relative overflow-hidden">
            <div class="absolute right-0 top-0 w-64 h-64 bg-sharp-purple/5 blur-[80px] rounded-full pointer-events-none"></div>
            
            <h3 class="font-syne text-2xl md:text-4xl font-bold mb-6 text-white tracking-tight">The <?= go_safe_text($niche_name) ?> Digital Landscape in <?= go_safe_text($base_city_name) ?></h3>
            
            <p class="font-manrope text-lavender/80 leading-relaxed text-lg mb-6">
                There are an estimated <strong class="text-white"><?= $est ?></strong> <?= strtolower(go_safe_text($niche_plural)) ?> operating in <?= go_safe_text($base_city_name) ?>. Of these, fewer than <strong class="text-white"><?= $with_web ?></strong> have a functional website — leaving over <strong class="text-sharp-purple"><?= $without_web ?> <?= $vocab['org_label'] ?> invisible</strong> to <?= $vocab['audience'] ?> who search online before deciding where to go.
            </p>
            
            <p class="font-manrope text-lavender/60 leading-relaxed text-base mb-4">
                <?= go_safe_text($landscape_text) ?>
            </p>

            <?php if ($edge_text): ?>
            <p class="font-manrope text-lavender/60 leading-relaxed text-base">
                <strong class="text-white">The Winning Edge:</strong> <?= go_safe_text($edge_text) ?>
            </p>
            <?php endif; ?>
        </div>
    </div>
</section>
<?php endif; 
$html_blocks['digital_gap'] = ob_get_clean();

// MODULE 2: THE REALITY (Pain Points)
ob_start(); ?>
<section class="py-24 md:py-32 px-4 md:px-6 bg-matte-black relative z-10 border-b border-lavender/10">
    <div class="max-w-7xl mx-auto grid grid-cols-1 lg:grid-cols-2 gap-16 lg:gap-24 items-start">
        <div class="space-y-6">
            <h2 class="font-syne text-3xl md:text-5xl font-bold mb-8"><?= go_safe_text($reality_headline) ?></h2>
            <div class="space-y-6 text-lavender/70 leading-relaxed md:text-lg">
                <p><?= nl2br(go_safe_text($meta['reality_p1'])) ?></p>
                <p><?= nl2br(go_safe_text($meta['reality_p2'])) ?></p>
                <p class="pl-6 border-l-2 border-sharp-purple text-white font-medium">When <?= $vocab['audience'] ?> search for <strong><?= go_safe_text($exact_keyword) ?></strong>, your platform must project absolute <?= $vocab['projection'] ?>. <?= nl2br(go_safe_text($meta['reality_p3'])) ?></p>
                <p><?= nl2br(go_safe_text($meta['reality_p4'])) ?></p>
                <?php if (!empty($meta['reality_p5'])): ?>
                <p><?= nl2br(go_safe_text($meta['reality_p5'])) ?></p>
                <?php endif; ?>
                <?php if (!empty($meta['reality_p6'])): ?>
                <p><?= nl2br(go_safe_text($meta['reality_p6'])) ?></p>
                <?php endif; ?>
            </div>
        </div>
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 md:gap-6">
            <?php $outcome_display_num = 1; foreach ($outcome_order as $i): if (!empty($meta["pos{$i}_title"])): ?>
            <div class="bg-card-dark border border-lavender/10 p-6 md:p-8 rounded-3xl hover:-translate-y-2 transition-transform duration-300 hover-target">
                <div class="text-sharp-purple font-mono text-sm mb-4 tracking-widest border-b border-white/5 pb-4">0<?= $outcome_display_num++ ?> / OUTCOME</div>
                <h3 class="font-syne text-xl font-bold mb-3"><?= go_safe_text($meta["pos{$i}_title"]) ?></h3>
                <p class="text-sm text-lavender/60 leading-relaxed"><?= go_safe_text($meta["pos{$i}_desc"]) ?></p>
            </div>
            <?php endif; endforeach; ?>
        </div>
    </div>
</section>
<?php $html_blocks['reality'] = ob_get_clean();

// MODULE 3: FEATURES (What We Build)
ob_start(); ?>
<section class="py-24 md:py-32 bg-[#0d0d0d] relative z-10 border-b border-lavender/10">
    <div class="max-w-7xl mx-auto px-4 md:px-6">
        <div class="text-center max-w-3xl mx-auto mb-16 md:mb-24">
            <h2 class="font-syne text-3xl md:text-5xl font-bold mb-6"><?= go_safe_text($meta['feat_headline']) ?></h2>
            <p class="font-manrope text-lavender/60 text-lg md:text-xl">
                Looking for a highly-rated <strong><?= go_safe_text($exact_keyword) ?></strong>? <?= go_safe_text($meta['feat_subline']) ?>
            </p>
        </div>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            <?php $icons = ['layout-template', 'shield-check', 'mouse-pointer-click', 'star', 'search', 'smartphone'];
            foreach ($feature_order as $i): if (!empty($meta["f{$i}_title"])): ?>
            <div class="bg-card-dark border border-lavender/10 p-8 rounded-3xl group hover:border-sharp-purple/50 transition-colors duration-500 hover-target">
                <div class="w-14 h-14 rounded-2xl bg-sharp-purple/10 flex items-center justify-center mb-6 group-hover:bg-sharp-purple transition-colors duration-500">
                    <i data-lucide="<?= $icons[$i-1] ?>" class="w-6 h-6 text-sharp-purple group-hover:text-white transition-colors duration-500"></i>
                </div>
                <h3 class="font-syne text-2xl font-bold mb-4"><?= go_safe_text($meta["f{$i}_title"]) ?></h3>
                <p class="text-lavender/60 leading-relaxed text-sm"><?= go_safe_text($meta["f{$i}_desc"]) ?></p>
            </div>
            <?php endif; endforeach; ?>
        </div>
    </div>
</section>
<?php $html_blocks['features'] = ob_get_clean();

// MODULE 4: STATS PROOF
ob_start();
$display_stats = [];
if (!empty($matrix_data['estimated_businesses'])) {
    $display_stats[] = [
        'num'   => number_format($matrix_data['estimated_businesses']),
        'label' => $niche_plural . ' in ' . $base_city_name,
        'sub'   => 'Estimated active ' . strtolower($niche_plural) . ' in this market',
    ];
}
if (!empty($matrix_data['without_website'])) {
    $display_stats[] = [
        'num'   => number_format($matrix_data['without_website']),
        'label' => 'Have No Website',
        'sub'   => 'Invisible to clients searching online right now',
    ];
}
if ($digital_gap_percent > 0) {
    $display_stats[] = [
        'num'   => $digital_gap_percent . '%',
        'label' => 'The Digital Gap',
        'sub'   => 'Your opportunity to stand out and capture the market',
    ];
}
?>
<section class="py-16 px-4 md:px-6 bg-[#0d0d0d] border-b border-lavender/10">
    <div class="max-w-5xl mx-auto grid grid-cols-1 md:grid-cols-3 gap-6 md:gap-8">
        <?php if (!empty($display_stats)): foreach ($display_stats as $stat): ?>
        <div class="text-center p-8 border border-lavender/10 rounded-3xl bg-matte-black hover:border-sharp-purple/30 transition-colors hover-target">
            <div class="font-syne text-4xl md:text-5xl font-bold text-sharp-purple mb-3"><?= go_safe_text($stat['num']) ?></div>
            <div class="font-syne text-sm font-bold uppercase tracking-widest text-lavender mb-2"><?= go_safe_text($stat['label']) ?></div>
            <div class="text-xs text-lavender/50"><?= go_safe_text($stat['sub']) ?></div>
        </div>
        <?php endforeach; endif; ?>
    </div>
</section>
<?php $html_blocks['stats'] = ob_get_clean();

// MODULE 5: ADVANTAGES
ob_start(); ?>
<section class="py-24 md:py-32 px-4 md:px-6 bg-card-dark border-b border-lavender/10">
    <div class="max-w-6xl mx-auto text-center">
        <h2 class="font-syne text-3xl md:text-5xl mb-6"><?= go_spin_text("{The GetOnline Advantage|Why Choose Us|Our Edge in {$city_name}|The {$niche_name} Digital Toolkit}") ?></h2>
        <p class="font-manrope text-lavender/60 text-lg mb-16 max-w-2xl mx-auto"><?= go_spin_text("{Why the leading {$niche_plural} in {$city_name} trust us to build their digital presence.|How we help {$city_name} {$niche_plural} lead the market.|The preferred choice for ambitious {$niche_plural} in {$city_name}.}") ?></p>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
            <div class="p-10 border border-lavender/10 rounded-3xl bg-matte-black hover:border-sharp-purple/50 transition-colors hover-target tilt-card" onmousemove="tiltCard(event, this)" onmouseleave="resetTilt(this)">
                <div class="w-16 h-16 mx-auto rounded-2xl bg-sharp-purple/10 flex items-center justify-center mb-6"><i data-lucide="eye" class="w-8 h-8 text-sharp-purple"></i></div>
                <h3 class="font-syne text-2xl mb-4 text-white">Clarity</h3>
                <p class="text-sm text-lavender/60 leading-relaxed">Your message becomes impossible to misunderstand. We strip away the noise so your audience takes action.</p>
            </div>
            <div class="p-10 border border-lavender/10 rounded-3xl bg-matte-black hover:border-sharp-purple/50 transition-colors hover-target tilt-card" onmousemove="tiltCard(event, this)" onmouseleave="resetTilt(this)">
                <div class="w-16 h-16 mx-auto rounded-2xl bg-sharp-purple/10 flex items-center justify-center mb-6"><i data-lucide="zap" class="w-8 h-8 text-sharp-purple"></i></div>
                <h3 class="font-syne text-2xl mb-4 text-white">Efficiency</h3>
                <p class="text-sm text-lavender/60 leading-relaxed">You stop doing busy work. Our automations and contact systems handle the repetitive tasks for you.</p>
            </div>
            <div class="p-10 border border-lavender/10 rounded-3xl bg-matte-black hover:border-sharp-purple/50 transition-colors hover-target tilt-card" onmousemove="tiltCard(event, this)" onmouseleave="resetTilt(this)">
                <div class="w-16 h-16 mx-auto rounded-2xl bg-sharp-purple/10 flex items-center justify-center mb-6"><i data-lucide="gem" class="w-8 h-8 text-sharp-purple"></i></div>
                <h3 class="font-syne text-2xl mb-4 text-white">Value</h3>
                <p class="text-sm text-lavender/60 leading-relaxed">Your digital footprint projects absolute <?= $vocab['projection'] ?>, instantly elevating how people perceive you.</p>
            </div>
        </div>
    </div>
</section>
<?php $html_blocks['advantages'] = ob_get_clean();

// MODULE 6: PORTFOLIO
ob_start(); ?>
<section class="py-24 md:py-32 px-4 md:px-6 bg-matte-black border-b border-lavender/10 z-10 relative">
    <div class="max-w-7xl mx-auto">
        <div class="flex flex-col md:flex-row justify-between items-end mb-16 gap-6">
            <div>
                <h2 class="font-syne text-3xl md:text-5xl font-bold mb-4"><?= go_spin_text("{Selected Work|Recent Projects|Our Portfolio|Featured Case Studies|Platforms We've Built}") ?></h2>
                <p class="font-manrope text-lavender/60 text-lg"><?= go_safe_text($meta['trust_signal']) ?></p>
            </div>
            <a href="/work" class="inline-flex items-center gap-2 text-sharp-purple font-bold tracking-widest uppercase hover:text-white transition-colors hover-target border-b border-sharp-purple pb-1">
                View All Projects <i data-lucide="arrow-right" class="w-4 h-4"></i>
            </a>
        </div>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-12 md:gap-16">
            <?php foreach ($display_projects as $index => $project): ?>
            <a href="<?= esc_url($project['url']) ?>" class="group block hover-target <?= $index > 0 ? 'md:mt-16' : '' ?>">
                <div class="relative w-full aspect-[4/3] rounded-2xl overflow-hidden mb-6 bg-card-dark border border-lavender/10">
                    <div class="absolute inset-0 bg-cover bg-top project-img filter grayscale opacity-80" style="background-image: url('<?= esc_url($project['img']) ?>');"></div>
                </div>
                <div class="flex justify-between items-start gap-4">
                    <div>
                        <h3 class="font-syne text-2xl font-bold mb-2 group-hover:text-sharp-purple transition-colors"><?= go_safe_text($project['name']) ?></h3>
                        <p class="text-lavender/60 text-sm max-w-sm"><?= go_safe_text($project['desc']) ?></p>
                    </div>
                    <span class="font-mono text-[10px] text-sharp-purple tracking-widest uppercase border border-sharp-purple/30 px-3 py-1 rounded-full whitespace-nowrap"><?= go_safe_text($project['tag']) ?></span>
                </div>
            </a>
            <?php endforeach; ?>
        </div>
    </div>
</section>
<?php $html_blocks['portfolio'] = ob_get_clean();

// MODULE 7: SERVICES (10 Real Services — mirrors city hub)
ob_start(); ?>
<section id="services" class="relative py-24 md:py-32 overflow-hidden z-20 bg-matte-black border-b border-lavender/10">
    <div class="max-w-7xl mx-auto w-full px-4 md:px-6">
        <div class="mb-6 reveal-up">
            <p class="font-mono text-[10px] text-sharp-purple tracking-widest uppercase mb-4">[ What We Do ]</p>
            <h2 class="font-syne text-3xl md:text-5xl font-bold mb-4 text-white"><?= go_spin_text("{How We Help Your {$niche_name} Get More Customers.|Beyond Web Design. A Full Digital Ecosystem.|More Than Just a Website for Your {$niche_name}.}") ?></h2>
            <p class="font-manrope text-lavender/60 text-lg max-w-2xl mt-4 leading-relaxed">
                We are not just a web design company. We are a <strong class="text-white">digital infrastructure agency</strong>, helping <?= go_safe_text(strtolower($niche_plural)) ?> in <?= go_safe_text($city_name) ?> look professional online, get found by more <?= $vocab['audience'] ?>, and <?= $vocab['cta_action'] ?>.
            </p>
        </div>
        <div class="mt-16 grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
            <button type="button" onclick="openWaWidgetWithService('Website Design')" class="w-full text-left group bg-card-dark border border-lavender/10 rounded-2xl p-8 hover:border-sharp-purple/50 hover:bg-[#111] transition-all duration-300 hover:-translate-y-1 hover-target flex flex-col gap-5 focus:outline-none active:scale-[0.98]">
                <div class="flex items-start justify-between w-full">
                    <div class="w-12 h-12 rounded-xl bg-sharp-purple/10 flex items-center justify-center group-hover:bg-sharp-purple transition-colors duration-300"><i data-lucide="monitor" class="w-5 h-5 text-sharp-purple group-hover:text-white transition-colors"></i></div>
                    <span class="font-mono text-xs text-lavender/20 group-hover:text-sharp-purple transition-colors">01</span>
                </div>
                <div>
                    <h3 class="font-syne text-xl font-bold text-white mb-2 group-hover:text-sharp-purple transition-colors">Professional Website Design</h3>
                    <p class="font-manrope text-sm text-lavender/60 leading-relaxed">Custom-crafted websites built to reflect your <?= go_safe_text(strtolower($niche_name)) ?> brand and convert visitors into paying <?= $vocab['audience'] ?>.</p>
                </div>
                <div class="mt-auto flex flex-wrap gap-2">
                    <span class="text-[10px] font-mono uppercase tracking-widest text-sharp-purple border border-sharp-purple/20 px-2 py-1 rounded-full">More Customers</span>
                    <span class="text-[10px] font-mono uppercase tracking-widest text-lavender/40 border border-lavender/10 px-2 py-1 rounded-full">Trust & Credibility</span>
                </div>
            </button>
            <button type="button" onclick="openWaWidgetWithService('Web Development & Portals')" class="w-full text-left group bg-card-dark border border-lavender/10 rounded-2xl p-8 hover:border-sharp-purple/50 hover:bg-[#111] transition-all duration-300 hover:-translate-y-1 hover-target flex flex-col gap-5 focus:outline-none active:scale-[0.98]">
                <div class="flex items-start justify-between w-full">
                    <div class="w-12 h-12 rounded-xl bg-sharp-purple/10 flex items-center justify-center group-hover:bg-sharp-purple transition-colors duration-300"><i data-lucide="code-2" class="w-5 h-5 text-sharp-purple group-hover:text-white transition-colors"></i></div>
                    <span class="font-mono text-xs text-lavender/20 group-hover:text-sharp-purple transition-colors">02</span>
                </div>
                <div>
                    <h3 class="font-syne text-xl font-bold text-white mb-2 group-hover:text-sharp-purple transition-colors">Web Development & Portals</h3>
                    <p class="font-manrope text-sm text-lavender/60 leading-relaxed">Full-stack development for member portals, booking systems, dashboards, and custom <?= go_safe_text(strtolower($niche_name)) ?> platforms.</p>
                </div>
                <div class="mt-auto flex flex-wrap gap-2">
                    <span class="text-[10px] font-mono uppercase tracking-widest text-sharp-purple border border-sharp-purple/20 px-2 py-1 rounded-full">Custom Logic</span>
                    <span class="text-[10px] font-mono uppercase tracking-widest text-lavender/40 border border-lavender/10 px-2 py-1 rounded-full">Scalable</span>
                </div>
            </button>
            <button type="button" onclick="openWaWidgetWithService('SEO & Google Ranking')" class="w-full text-left group bg-card-dark border border-lavender/10 rounded-2xl p-8 hover:border-sharp-purple/50 hover:bg-[#111] transition-all duration-300 hover:-translate-y-1 hover-target flex flex-col gap-5 focus:outline-none active:scale-[0.98]">
                <div class="flex items-start justify-between w-full">
                    <div class="w-12 h-12 rounded-xl bg-sharp-purple/10 flex items-center justify-center group-hover:bg-sharp-purple transition-colors duration-300"><i data-lucide="trending-up" class="w-5 h-5 text-sharp-purple group-hover:text-white transition-colors"></i></div>
                    <span class="font-mono text-xs text-lavender/20 group-hover:text-sharp-purple transition-colors">03</span>
                </div>
                <div>
                    <h3 class="font-syne text-xl font-bold text-white mb-2 group-hover:text-sharp-purple transition-colors">SEO & Google Ranking</h3>
                    <p class="font-manrope text-sm text-lavender/60 leading-relaxed">Rank on the first page when <?= $vocab['audience'] ?> in <?= go_safe_text($city_name) ?> search for <?= go_safe_text(strtolower($niche_name)) ?> services.</p>
                </div>
                <div class="mt-auto flex flex-wrap gap-2">
                    <span class="text-[10px] font-mono uppercase tracking-widest text-sharp-purple border border-sharp-purple/20 px-2 py-1 rounded-full">Page 1 Google</span>
                    <span class="text-[10px] font-mono uppercase tracking-widest text-lavender/40 border border-lavender/10 px-2 py-1 rounded-full">Local Search</span>
                </div>
            </button>
            <button type="button" onclick="openWaWidgetWithService('Local SEO & Google Maps')" class="w-full text-left group bg-card-dark border border-lavender/10 rounded-2xl p-8 hover:border-sharp-purple/50 hover:bg-[#111] transition-all duration-300 hover:-translate-y-1 hover-target flex flex-col gap-5 focus:outline-none active:scale-[0.98]">
                <div class="flex items-start justify-between w-full">
                    <div class="w-12 h-12 rounded-xl bg-sharp-purple/10 flex items-center justify-center group-hover:bg-sharp-purple transition-colors duration-300"><i data-lucide="map-pin" class="w-5 h-5 text-sharp-purple group-hover:text-white transition-colors"></i></div>
                    <span class="font-mono text-xs text-lavender/20 group-hover:text-sharp-purple transition-colors">04</span>
                </div>
                <div>
                    <h3 class="font-syne text-xl font-bold text-white mb-2 group-hover:text-sharp-purple transition-colors">Local SEO & Google Maps</h3>
                    <p class="font-manrope text-sm text-lavender/60 leading-relaxed">Dominate the Google Maps pack so <?= $vocab['audience'] ?> nearby find your <?= go_safe_text(strtolower($niche_name)) ?> first.</p>
                </div>
                <div class="mt-auto flex flex-wrap gap-2">
                    <span class="text-[10px] font-mono uppercase tracking-widest text-sharp-purple border border-sharp-purple/20 px-2 py-1 rounded-full">Maps Pack</span>
                    <span class="text-[10px] font-mono uppercase tracking-widest text-lavender/40 border border-lavender/10 px-2 py-1 rounded-full">Nearby Searches</span>
                </div>
            </button>
            <button type="button" onclick="openWaWidgetWithService('Branding & Logo Design')" class="w-full text-left group bg-card-dark border border-lavender/10 rounded-2xl p-8 hover:border-sharp-purple/50 hover:bg-[#111] transition-all duration-300 hover:-translate-y-1 hover-target flex flex-col gap-5 focus:outline-none active:scale-[0.98]">
                <div class="flex items-start justify-between w-full">
                    <div class="w-12 h-12 rounded-xl bg-sharp-purple/10 flex items-center justify-center group-hover:bg-sharp-purple transition-colors duration-300"><i data-lucide="pen-tool" class="w-5 h-5 text-sharp-purple group-hover:text-white transition-colors"></i></div>
                    <span class="font-mono text-xs text-lavender/20 group-hover:text-sharp-purple transition-colors">05</span>
                </div>
                <div>
                    <h3 class="font-syne text-xl font-bold text-white mb-2 group-hover:text-sharp-purple transition-colors">Branding & Logo Design</h3>
                    <p class="font-manrope text-sm text-lavender/60 leading-relaxed">A complete visual identity that makes your <?= go_safe_text(strtolower($niche_name)) ?> look professional and memorable from day one.</p>
                </div>
                <div class="mt-auto flex flex-wrap gap-2">
                    <span class="text-[10px] font-mono uppercase tracking-widest text-sharp-purple border border-sharp-purple/20 px-2 py-1 rounded-full">Identity</span>
                    <span class="text-[10px] font-mono uppercase tracking-widest text-lavender/40 border border-lavender/10 px-2 py-1 rounded-full">Recognition</span>
                </div>
            </button>
            <button type="button" onclick="openWaWidgetWithService('Business Automation')" class="w-full text-left group bg-card-dark border border-lavender/10 rounded-2xl p-8 hover:border-sharp-purple/50 hover:bg-[#111] transition-all duration-300 hover:-translate-y-1 hover-target flex flex-col gap-5 focus:outline-none active:scale-[0.98]">
                <div class="flex items-start justify-between w-full">
                    <div class="w-12 h-12 rounded-xl bg-sharp-purple/10 flex items-center justify-center group-hover:bg-sharp-purple transition-colors duration-300"><i data-lucide="cpu" class="w-5 h-5 text-sharp-purple group-hover:text-white transition-colors"></i></div>
                    <span class="font-mono text-xs text-lavender/20 group-hover:text-sharp-purple transition-colors">06</span>
                </div>
                <div>
                    <h3 class="font-syne text-xl font-bold text-white mb-2 group-hover:text-sharp-purple transition-colors">Business Automation</h3>
                    <p class="font-manrope text-sm text-lavender/60 leading-relaxed">CRM integrations, auto-responses, and booking flows that run your <?= go_safe_text(strtolower($niche_name)) ?> operations 24/7.</p>
                </div>
                <div class="mt-auto flex flex-wrap gap-2">
                    <span class="text-[10px] font-mono uppercase tracking-widest text-sharp-purple border border-sharp-purple/20 px-2 py-1 rounded-full">24/7 Ops</span>
                    <span class="text-[10px] font-mono uppercase tracking-widest text-lavender/40 border border-lavender/10 px-2 py-1 rounded-full">Lead Nurturing</span>
                </div>
            </button>
            <button type="button" onclick="openWaWidgetWithService('CAC Registration')" class="w-full text-left group bg-card-dark border border-lavender/10 rounded-2xl p-8 hover:border-sharp-purple/50 hover:bg-[#111] transition-all duration-300 hover:-translate-y-1 hover-target flex flex-col gap-5 focus:outline-none active:scale-[0.98]">
                <div class="flex items-start justify-between w-full">
                    <div class="w-12 h-12 rounded-xl bg-sharp-purple/10 flex items-center justify-center group-hover:bg-sharp-purple transition-colors duration-300"><i data-lucide="landmark" class="w-5 h-5 text-sharp-purple group-hover:text-white transition-colors"></i></div>
                    <span class="font-mono text-xs text-lavender/20 group-hover:text-sharp-purple transition-colors">07</span>
                </div>
                <div>
                    <h3 class="font-syne text-xl font-bold text-white mb-2 group-hover:text-sharp-purple transition-colors">CAC Registration</h3>
                    <p class="font-manrope text-sm text-lavender/60 leading-relaxed">Get your <?= go_safe_text(strtolower($niche_name)) ?> legally registered with the Corporate Affairs Commission, fast and stress-free.</p>
                </div>
                <div class="mt-auto flex flex-wrap gap-2">
                    <span class="text-[10px] font-mono uppercase tracking-widest text-sharp-purple border border-sharp-purple/20 px-2 py-1 rounded-full">Legal Entity</span>
                    <span class="text-[10px] font-mono uppercase tracking-widest text-lavender/40 border border-lavender/10 px-2 py-1 rounded-full">Corporate Trust</span>
                </div>
            </button>
            <button type="button" onclick="openWaWidgetWithService('Social Media Setup')" class="w-full text-left group bg-card-dark border border-lavender/10 rounded-2xl p-8 hover:border-sharp-purple/50 hover:bg-[#111] transition-all duration-300 hover:-translate-y-1 hover-target flex flex-col gap-5 focus:outline-none active:scale-[0.98]">
                <div class="flex items-start justify-between w-full">
                    <div class="w-12 h-12 rounded-xl bg-sharp-purple/10 flex items-center justify-center group-hover:bg-sharp-purple transition-colors duration-300"><i data-lucide="share-2" class="w-5 h-5 text-sharp-purple group-hover:text-white transition-colors"></i></div>
                    <span class="font-mono text-xs text-lavender/20 group-hover:text-sharp-purple transition-colors">08</span>
                </div>
                <div>
                    <h3 class="font-syne text-xl font-bold text-white mb-2 group-hover:text-sharp-purple transition-colors">Social Media Setup & Strategy</h3>
                    <p class="font-manrope text-sm text-lavender/60 leading-relaxed">Professional social pages, content strategy, and audience-building designed for <?= go_safe_text(strtolower($niche_name)) ?> businesses in <?= go_safe_text($city_name) ?>.</p>
                </div>
                <div class="mt-auto flex flex-wrap gap-2">
                    <span class="text-[10px] font-mono uppercase tracking-widest text-sharp-purple border border-sharp-purple/20 px-2 py-1 rounded-full">More Followers</span>
                    <span class="text-[10px] font-mono uppercase tracking-widest text-lavender/40 border border-lavender/10 px-2 py-1 rounded-full">Brand Consistency</span>
                </div>
            </button>
            <button type="button" onclick="openWaWidgetWithService('Website Maintenance')" class="w-full text-left group bg-card-dark border border-lavender/10 rounded-2xl p-8 hover:border-sharp-purple/50 hover:bg-[#111] transition-all duration-300 hover:-translate-y-1 hover-target flex flex-col gap-5 focus:outline-none active:scale-[0.98]">
                <div class="flex items-start justify-between w-full">
                    <div class="w-12 h-12 rounded-xl bg-sharp-purple/10 flex items-center justify-center group-hover:bg-sharp-purple transition-colors duration-300"><i data-lucide="shield-check" class="w-5 h-5 text-sharp-purple group-hover:text-white transition-colors"></i></div>
                    <span class="font-mono text-xs text-lavender/20 group-hover:text-sharp-purple transition-colors">09</span>
                </div>
                <div>
                    <h3 class="font-syne text-xl font-bold text-white mb-2 group-hover:text-sharp-purple transition-colors">Website Maintenance & Support</h3>
                    <p class="font-manrope text-sm text-lavender/60 leading-relaxed">Security updates, backups, and priority support so your <?= go_safe_text(strtolower($niche_name)) ?> platform stays fast and always online.</p>
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
                <p class="font-manrope text-lavender/60 text-sm mt-2">Tell us about your <?= go_safe_text(strtolower($niche_name)) ?> and we will tell you exactly what you need.</p>
            </div>
            <button type="button" onclick="toggleWaWidget()" class="flex-shrink-0 inline-flex items-center gap-3 bg-sharp-purple text-white px-8 py-4 rounded-full text-sm font-bold uppercase tracking-widest hover:bg-white hover:text-matte-black transition-all hover-target shadow-[0_0_30px_rgba(126,34,206,0.3)] whitespace-nowrap focus:outline-none min-h-[44px]">
                <i data-lucide="message-circle" class="w-4 h-4"></i> Chat with Us
            </button>
        </div>
    </div>
</section>
<?php $html_blocks['trinity'] = ob_get_clean();

// MODULE 8: FAQs
ob_start(); ?>
<section class="py-24 md:py-32 px-4 md:px-6 bg-[#0a0a0a] border-b border-lavender/10 relative z-10">
    <div class="max-w-3xl mx-auto">
        <h2 class="font-syne text-3xl md:text-5xl font-bold mb-12 text-center"><?= go_spin_text("{Questions to ask a {$exact_keyword}.|Frequently Asked Questions|What {$city_name} {$niche_plural} Ask Us|Common Questions About {$niche_name} Websites}") ?></h2>
        <!-- VISIBLE FAQs (first 5) -->
        <div class="space-y-4" id="faq-visible">
            <?php 
            $faq_display_count = 0;
            foreach ($faq_order as $i): if (!empty($meta["faq{$i}_q"])): 
                $faq_display_count++;
                if ($faq_display_count > 5) continue;
            ?>
            <div class="faq-item bg-matte-black border border-lavender/10 rounded-2xl overflow-hidden hover-target">
                <button type="button" class="w-full text-left px-6 md:px-8 py-6 flex justify-between items-center focus:outline-none" onclick="this.parentElement.classList.toggle('active')">
                    <span class="font-syne font-bold text-lg pr-4"><?= go_safe_text($meta["faq{$i}_q"]) ?></span>
                    <i data-lucide="plus" class="w-5 h-5 text-sharp-purple flex-shrink-0 transition-transform duration-300 faq-icon"></i>
                </button>
                <div class="faq-content bg-[#111]">
                    <p class="px-6 md:px-8 pb-8 pt-2 text-lavender/70 leading-relaxed text-sm md:text-base">
                        <?= nl2br(go_safe_text($meta["faq{$i}_a"])) ?>
                    </p>
                </div>
            </div>
            <?php endif; endforeach; ?>
        </div>

        <?php
        $total_faqs = 0;
        foreach ($faq_order as $i) { if (!empty($meta["faq{$i}_q"])) $total_faqs++; }
        if ($total_faqs > 5): ?>

        <!-- SHOW MORE BUTTON -->
        <div class="flex justify-center mt-8">
            <button type="button" id="faq-show-more-btn" onclick="toggleExtraFaqs()" class="inline-flex items-center gap-2 px-6 py-3 rounded-full border border-lavender/20 text-lavender/60 hover:text-white hover:border-sharp-purple text-sm font-bold uppercase tracking-widest transition-all duration-300 hover-target focus:outline-none">
                <span id="faq-btn-label">More Questions</span>
                <i data-lucide="chevron-down" id="faq-btn-icon" class="w-4 h-4 transition-transform duration-300"></i>
            </button>
        </div>

        <!-- HIDDEN EXTRA FAQS -->
        <div id="faq-extra" class="space-y-4 transition-all duration-500 overflow-visible" style="max-height: 0; opacity: 0; pointer-events: none;">
            <?php
            $faq_display_count = 0;
            foreach ($faq_order as $i): if (!empty($meta["faq{$i}_q"])): 
                $faq_display_count++;
                if ($faq_display_count <= 5) continue;
            ?>
            <div class="faq-item bg-matte-black border border-lavender/10 rounded-2xl overflow-hidden hover-target">
                <button type="button" class="w-full text-left px-6 md:px-8 py-6 flex justify-between items-center focus:outline-none" onclick="this.parentElement.classList.toggle('active')">
                    <span class="font-syne font-bold text-lg pr-4"><?= go_safe_text($meta["faq{$i}_q"]) ?></span>
                    <i data-lucide="plus" class="w-5 h-5 text-sharp-purple flex-shrink-0 transition-transform duration-300 faq-icon"></i>
                </button>
                <div class="faq-content bg-[#111]">
                    <p class="px-6 md:px-8 pb-8 pt-2 text-lavender/70 leading-relaxed text-sm md:text-base">
                        <?= nl2br(go_safe_text($meta["faq{$i}_a"])) ?>
                    </p>
                </div>
            </div>
            <?php endif; endforeach; ?>
        </div>
        <?php endif; ?>
    </div>
</section>
<?php $html_blocks['faq'] = ob_get_clean();

// MODULE 9: PRICING CALCULATOR
ob_start();
$calc_file = __DIR__ . '/module-calculator.php';
if (file_exists($calc_file)) { include $calc_file; }
$html_blocks['calculator'] = ob_get_clean();

// ─── TESTIMONIALS BLOCK ───────────────────────────────────────────────────────
ob_start();
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
<section class="py-24 md:py-32 px-4 md:px-6 bg-card-dark border-t border-lavender/10 relative z-10">
    <div class="max-w-7xl mx-auto">
        <div class="text-center mb-12 md:mb-16">
            <p class="font-mono text-[10px] text-sharp-purple tracking-widest uppercase mb-4">[ Client Results ]</p>
            <h2 class="font-syne text-3xl md:text-4xl font-bold text-white mb-4">Trusted by Businesses Across Nigeria</h2>
            <p class="font-manrope text-lavender/60 text-lg">From churches to fintechs, across Nigeria.</p>
        </div>

        <div class="flex md:block overflow-x-auto md:overflow-visible snap-x snap-mandatory hide-scrollbar gap-4 pb-8 md:pb-0 md:columns-2 xl:columns-3 md:space-y-6">
            <?php foreach ($testimonials as $t): ?>
            <div class="snap-center min-w-[85vw] sm:min-w-[350px] md:min-w-0 break-inside-avoid bg-matte-black border border-lavender/10 rounded-2xl p-7 md:p-8 hover:border-sharp-purple/40 transition-all duration-300 flex flex-col gap-5 group">
                <div class="flex items-center justify-between gap-3 flex-wrap">
                    <span class="text-yellow-400 text-sm tracking-tight" aria-label="5 stars">★★★★★</span>
                    <span class="font-mono text-[9px] uppercase tracking-widest text-lavender/30 border border-lavender/10 px-2 py-1 rounded-full"><?= esc_html($t['tag']) ?></span>
                </div>
                <blockquote class="font-manrope text-sm text-lavender/80 leading-relaxed flex-1">
                    <span class="text-3xl text-sharp-purple/40 font-serif leading-none mr-1 align-bottom">&ldquo;</span><?= esc_html($t['quote']) ?>
                </blockquote>
                <div class="flex items-center gap-4 pt-4 border-t border-lavender/5 mt-auto">
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

        <div class="mt-10 md:mt-16 text-center">
            <p class="font-manrope text-lavender/50 text-sm mb-6">Join these businesses. Send us a message and let's start your project.</p>
            <button onclick="toggleWaWidget()" class="inline-flex items-center gap-3 bg-sharp-purple text-white px-10 py-4 rounded-full text-sm font-bold uppercase tracking-widest hover:bg-white hover:text-matte-black transition-all hover-target shadow-[0_0_30px_rgba(126,34,206,0.3)] focus:outline-none">
                <i data-lucide="message-circle" class="w-4 h-4"></i> Start Your Project
            </button>
        </div>
    </div>
</section>
<?php
$html_blocks['testimonials'] = ob_get_clean();

// MODULE 10: HOW WE WORK — PROCESS SECTION
ob_start(); ?>
<section class="py-20 md:py-28 px-4 md:px-6 bg-[#0a0a0a] border-t border-lavender/5 relative z-10">
    <div class="max-w-7xl mx-auto">
        <div class="text-center max-w-3xl mx-auto mb-16">
            <p class="font-mono text-[10px] text-sharp-purple tracking-widest uppercase mb-4">[ Our Process ]</p>
            <h2 class="font-syne text-3xl md:text-4xl font-bold text-white mb-4">
                How We Build Your <?= go_safe_text($niche_name) ?> Website in <?= go_safe_text($city_name) ?>
            </h2>
            <p class="font-manrope text-lavender/60 text-base leading-relaxed">
                No guesswork. Every project follows a clear, structured process so you always know what is happening and when your platform will be live.
            </p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 relative">
            <div class="hidden lg:block absolute top-10 left-[12.5%] right-[12.5%] h-px bg-gradient-to-r from-transparent via-sharp-purple/30 to-transparent pointer-events-none"></div>

            <div class="relative bg-card-dark border border-lavender/10 rounded-2xl p-8 hover:border-sharp-purple/40 transition-colors flex flex-col gap-5">
                <div class="flex items-center justify-between">
                    <div class="w-12 h-12 rounded-xl bg-sharp-purple/10 flex items-center justify-center">
                        <i data-lucide="message-circle" class="w-5 h-5 text-sharp-purple"></i>
                    </div>
                    <span class="font-mono text-2xl font-bold text-white/10">01</span>
                </div>
                <div>
                    <h3 class="font-syne text-lg font-bold text-white mb-2">Start a Conversation</h3>
                    <p class="font-manrope text-sm text-lavender/60 leading-relaxed">We start with a quick conversation — WhatsApp, email, or a call, whatever works for you — to understand your <?= go_safe_text($niche_name) ?>'s goals and what's holding you back online.</p>
                </div>
                <div class="mt-auto">
                    <span class="font-mono text-[10px] text-sharp-purple uppercase tracking-widest border border-sharp-purple/20 px-3 py-1 rounded-full">Free &middot; No Pressure</span>
                </div>
            </div>

            <div class="relative bg-card-dark border border-lavender/10 rounded-2xl p-8 hover:border-sharp-purple/40 transition-colors flex flex-col gap-5">
                <div class="flex items-center justify-between">
                    <div class="w-12 h-12 rounded-xl bg-sharp-purple/10 flex items-center justify-center">
                        <i data-lucide="layout-template" class="w-5 h-5 text-sharp-purple"></i>
                    </div>
                    <span class="font-mono text-2xl font-bold text-white/10">02</span>
                </div>
                <div>
                    <h3 class="font-syne text-lg font-bold text-white mb-2">Strategy & Design</h3>
                    <p class="font-manrope text-sm text-lavender/60 leading-relaxed">We map out your website structure, choose the right pages, and create a design that matches your <?= go_safe_text($niche_name) ?> brand and converts visitors into paying <?= $vocab['audience'] ?>.</p>
                </div>
                <div class="mt-auto">
                    <span class="font-mono text-[10px] text-sharp-purple uppercase tracking-widest border border-sharp-purple/20 px-3 py-1 rounded-full">Days 1 – 5</span>
                </div>
            </div>

            <div class="relative bg-card-dark border border-lavender/10 rounded-2xl p-8 hover:border-sharp-purple/40 transition-colors flex flex-col gap-5">
                <div class="flex items-center justify-between">
                    <div class="w-12 h-12 rounded-xl bg-sharp-purple/10 flex items-center justify-center">
                        <i data-lucide="code-2" class="w-5 h-5 text-sharp-purple"></i>
                    </div>
                    <span class="font-mono text-2xl font-bold text-white/10">03</span>
                </div>
                <div>
                    <h3 class="font-syne text-lg font-bold text-white mb-2">Build & Develop</h3>
                    <p class="font-manrope text-sm text-lavender/60 leading-relaxed">Our developers build your platform — fast load speeds, mobile-first performance, SEO architecture baked in from day one, and a system built to handle real <?= go_safe_text($city_name) ?> traffic.</p>
                </div>
                <div class="mt-auto">
                    <span class="font-mono text-[10px] text-sharp-purple uppercase tracking-widest border border-sharp-purple/20 px-3 py-1 rounded-full">Days 5 – 14</span>
                </div>
            </div>

            <div class="relative bg-card-dark border border-lavender/10 rounded-2xl p-8 hover:border-sharp-purple/40 transition-colors flex flex-col gap-5">
                <div class="flex items-center justify-between">
                    <div class="w-12 h-12 rounded-xl bg-sharp-purple/10 flex items-center justify-center">
                        <i data-lucide="rocket" class="w-5 h-5 text-sharp-purple"></i>
                    </div>
                    <span class="font-mono text-2xl font-bold text-white/10">04</span>
                </div>
                <div>
                    <h3 class="font-syne text-lg font-bold text-white mb-2">Launch & Support</h3>
                    <p class="font-manrope text-sm text-lavender/60 leading-relaxed">We review everything with you before going live. After launch, we stay available to make sure your <?= go_safe_text($niche_name) ?> platform keeps performing and growing.</p>
                </div>
                <div class="mt-auto">
                    <span class="font-mono text-[10px] text-sharp-purple uppercase tracking-widest border border-sharp-purple/20 px-3 py-1 rounded-full">Day 14+</span>
                </div>
            </div>
        </div>

        <div class="mt-12 text-center">
            <button onclick="toggleWaWidget()" class="inline-flex items-center gap-2 font-syne text-sm font-bold uppercase tracking-widest text-lavender hover:text-white border border-lavender/20 hover:border-sharp-purple/50 px-8 py-4 rounded-full transition-all hover-target focus:outline-none">
                Start a Conversation <i data-lucide="arrow-right" class="w-4 h-4"></i>
            </button>
        </div>
    </div>
</section>
<?php $html_blocks['process'] = ob_get_clean();

// MODULE 11: COMMON MISTAKES
ob_start(); ?>
<section class="py-20 md:py-28 px-4 md:px-6 bg-[#0d0d0d] border-t border-lavender/5 relative z-10">
    <div class="max-w-5xl mx-auto">
        <div class="text-center max-w-3xl mx-auto mb-14">
            <p class="font-mono text-[10px] text-sharp-purple tracking-widest uppercase mb-4">[ What to Avoid ]</p>
            <h2 class="font-syne text-3xl md:text-4xl font-bold text-white mb-4">
                <?= go_spin_text("{Common Mistakes|Critical Errors|Costly Mistakes} " . go_safe_text($niche_plural) . " Make {With Their Websites|Before Hiring a Web Designer|When Going Digital} in " . go_safe_text($city_name)) ?>
            </h2>
            <p class="font-manrope text-lavender/60 text-base leading-relaxed">
                <?= go_spin_text("After {working with|building platforms for|delivering projects for} {hundreds of|over 200|countless} " . strtolower(go_safe_text($niche_plural)) . " across Nigeria, these are the {patterns|mistakes|problems} we see most often — especially in " . go_safe_text($city_name) . ".") ?>
            </p>
        </div>
        <div class="space-y-5">
            <?php
            $landing_mistakes = [
                [
                    'number' => '01',
                    'title'  => go_spin_text("Choosing the {Cheapest|Lowest Bid} Instead of the {Right Fit|Best Value}"),
                    'desc'   => go_spin_text("Many " . strtolower(go_safe_text($niche_plural)) . " in " . go_safe_text($city_name) . " {hire based on price alone|go for the cheapest quote} and end up with a website that {breaks within months|never ranks on Google|looks outdated on launch day}. A cheap website {costs more to fix|needs to be rebuilt} than a quality one built right the first time."),
                ],
                [
                    'number' => '02',
                    'title'  => go_spin_text("Relying {Entirely|Solely} on Social Media {Instead of|Rather Than} a Proper Website"),
                    'desc'   => go_spin_text("Social media {is rented land|is borrowed space}. Algorithm changes or account bans can {wipe out your entire presence overnight|make you invisible instantly}. " . go_safe_text($niche_plural) . " in " . go_safe_text($city_name) . " that own their website {control their own visibility|are never at the mercy of a platform}."),
                ],
                [
                    'number' => '03',
                    'title'  => go_spin_text("{Launching|Going Live|Building a Website} With No {SEO Plan|SEO Strategy|Search Optimisation}"),
                    'desc'   => go_spin_text("A website with no SEO is {invisible to Google|a billboard in the middle of a forest}. {This is the most common issue we fix for " . strtolower(go_safe_text($niche_plural)) . " in " . go_safe_text($city_name) . ".|We see this constantly across " . go_safe_text($city_name) . ".} Your site needs to be {optimised from day one|built with SEO architecture} — not treated as an afterthought."),
                ],
                [
                    'number' => '04',
                    'title'  => go_spin_text("{Using a Template|Using a Generic Theme} and Calling It {Done|a Professional Website}"),
                    'desc'   => go_spin_text("Template websites look the same as {thousands of competitors|every other " . strtolower(go_safe_text($niche_name)) . " in " . go_safe_text($city_name) . "}. In a competitive market like " . go_safe_text($city_name) . ", a {custom-built|professionally designed} website is what {separates you from amateurs|signals you are a serious " . strtolower(go_safe_text($niche_name)) . "}."),
                ],
                [
                    'number' => '05',
                    'title'  => go_spin_text("{Ignoring|Neglecting} Mobile {Performance|Optimisation}"),
                    'desc'   => go_spin_text("Over 80% of web searches in Nigeria happen on mobile. A website that {loads slowly|looks broken} on a phone {loses those visitors immediately|means lost " . ($is_non_profit ? "supporters" : "revenue") . " every single day}. Every platform we build is mobile-first by default."),
                ],
            ];
            foreach ($landing_mistakes as $mistake): ?>
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
<?php $html_blocks['mistakes'] = ob_get_clean();

// =============================================================================
// ROUTING THE MACRO-MODULES
// =============================================================================

if (strpos($service_slug, 'designer') !== false) {
    $master_layout = ['reality', 'digital_gap', 'features', 'calculator', 'stats', 'local_focus', 'portfolio', 'advantages', 'testimonials', 'process', 'mistakes', 'trinity', 'faq', 'services'];
} elseif (strpos($service_slug, 'branding') !== false) {
    $master_layout = ['portfolio', 'reality', 'digital_gap', 'features', 'calculator', 'local_focus', 'advantages', 'stats', 'testimonials', 'process', 'mistakes', 'trinity', 'faq', 'services'];
} elseif (strpos($service_slug, 'developer') !== false) {
    $master_layout = ['stats', 'reality', 'digital_gap', 'features', 'calculator', 'portfolio', 'local_focus', 'advantages', 'testimonials', 'process', 'mistakes', 'trinity', 'faq', 'services'];
} else {
    $master_layout = ['reality', 'digital_gap', 'features', 'calculator', 'advantages', 'local_focus', 'portfolio', 'stats', 'testimonials', 'process', 'mistakes', 'trinity', 'faq', 'services'];
}

if (empty($html_blocks['digital_gap'])) $master_layout = array_values(array_diff($master_layout, ['digital_gap']));
if (empty($city_intro)) $master_layout = array_values(array_diff($master_layout, ['local_focus']));

$testimonials_pos = array_search('testimonials', $master_layout);
$master_layout_no_testimonials = array_values(array_diff($master_layout, ['testimonials']));

$max_swap_idx = count($master_layout_no_testimonials) - 3;
if ($max_swap_idx >= 4) {
    $swap_1 = rand(2, 3);
    $swap_2 = rand(4, $max_swap_idx);
    $temp = $master_layout_no_testimonials[$swap_1];
    $master_layout_no_testimonials[$swap_1] = $master_layout_no_testimonials[$swap_2];
    $master_layout_no_testimonials[$swap_2] = $temp;
}

array_splice($master_layout_no_testimonials, $testimonials_pos, 0, ['testimonials']);
$master_layout = $master_layout_no_testimonials;

?>
<!DOCTYPE html>
<html lang="en" class="scroll-smooth">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title><?= go_safe_text($meta_title) ?></title>
    <meta name="description" content="<?= go_safe_text($meta_desc) ?>">
    <link rel="canonical" href="<?= esc_url($canonical_url) ?>">
    
    <link rel="icon" href="https://getonlinestudio.com/insights/wp-content/uploads/2026/02/GetOnline_Studio_Logo.jpg" sizes="32x32" />
    <link rel="apple-touch-icon" href="https://getonlinestudio.com/insights/wp-content/uploads/2026/02/GetOnline_Studio_Logo.jpg" />

    <meta property="og:title" content="<?= go_safe_text($meta_title) ?>" />
    <meta property="og:description" content="<?= go_safe_text($meta_desc) ?>" />
    <meta property="og:type" content="article" />
    <meta property="og:url" content="<?= esc_url($canonical_url) ?>" />
    <meta property="og:image" content="<?= $global_settings['og_image'] ? esc_url($global_settings['og_image']) : 'https://getonlinestudio.com/insights/wp-content/uploads/2026/02/GetOnline_Studio_Logo.jpg' ?>" />
    
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="<?= go_safe_text($meta_title) ?>">
    <meta name="twitter:description" content="<?= go_safe_text($meta_desc) ?>">
    <meta name="twitter:image" content="<?= $global_settings['og_image'] ? esc_url($global_settings['og_image']) : 'https://getonlinestudio.com/insights/wp-content/uploads/2026/02/GetOnline_Studio_Logo.jpg' ?>">

    <script type="application/ld+json"><?= $local_schema_json ?></script>
    <?php if ($faq_schema_json): ?>
    <script type="application/ld+json"><?= $faq_schema_json ?></script>
    <?php endif; ?>

    <?php
    // BreadcrumbList schema — all items hardcoded via GO_SITE_URL
    $bc_items = [
        ["@type" => "ListItem", "position" => 1, "name" => "Home",      "item" => GO_SITE_URL . "/"],
        ["@type" => "ListItem", "position" => 2, "name" => "Locations", "item" => GO_SITE_URL . "/locations/"],
        ["@type" => "ListItem", "position" => 3, "name" => $base_city_name, "item" => GO_SITE_URL . "/locations/{$city_slug}/"],
    ];
    if (!empty($neighborhood_name)) {
        $bc_items[] = ["@type" => "ListItem", "position" => 4, "name" => $neighborhood_name, "item" => GO_SITE_URL . "/locations/{$city_slug}/{$neighborhood_slug}/"];
        $bc_items[] = ["@type" => "ListItem", "position" => 5, "name" => "{$niche_name} {$service_name}", "item" => $canonical_url];
    } else {
        $bc_items[] = ["@type" => "ListItem", "position" => 4, "name" => "{$niche_name} {$service_name}", "item" => $canonical_url];
    }
    $breadcrumb_landing = ["@context" => "https://schema.org", "@type" => "BreadcrumbList", "itemListElement" => $bc_items];

    // AggregateRating + Review schema
    $rating_landing = [
        "@context"       => "https://schema.org",
        "@type"          => ["LocalBusiness", "ProfessionalService"],
        "name"           => "GetOnline Studio " . $city_name,
        "url"            => GO_SITE_URL . "/",
        "telephone"      => "+2349061150443",
        "foundingDate"   => "2016",
        "description"    => "Web design company with over 9 years of experience, specialising in {$niche_plural} in {$city_name}, Nigeria.",
        "address"        => [
            "@type"           => "PostalAddress",
            "addressLocality" => $base_city_name,
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
            ["@type" => "Review", "author" => ["@type" => "Person", "name" => "Olayinka Itunu Damilare"], "reviewRating" => ["@type" => "Rating", "ratingValue" => "5", "bestRating" => "5"], "reviewBody" => "Our experience with GetOnline Studio was top-notch. He doesn't just build pages; he builds solutions. He turned our concept into a working prototype quickly."],
            ["@type" => "Review", "author" => ["@type" => "Person", "name" => "Mr. Mike"], "reviewRating" => ["@type" => "Rating", "ratingValue" => "5", "bestRating" => "5"], "reviewBody" => "GetOnline Studio developed a website and mobile app for us, and we couldn't be happier with the result. They made the whole process easy."],
            ["@type" => "Review", "author" => ["@type" => "Person", "name" => "Amb. Dr. Jernail Singh Anand"], "reviewRating" => ["@type" => "Rating", "ratingValue" => "5", "bestRating" => "5"], "reviewBody" => "GetOnline Studio proved to be a highly trusted partner. The speed of delivery did not compromise quality."],
            ["@type" => "Review", "author" => ["@type" => "Person", "name" => "Emmanuel Amaechi"], "reviewRating" => ["@type" => "Rating", "ratingValue" => "5", "bestRating" => "5"], "reviewBody" => "GetOnline Studio delivered more than just a system. They transformed how we operate."],
            ["@type" => "Review", "author" => ["@type" => "Person", "name" => "Chief Lamina Kamiludeen Omotoyosi"], "reviewRating" => ["@type" => "Rating", "ratingValue" => "5", "bestRating" => "5"], "reviewBody" => "I proudly commend GetOnline Studio for their exceptional quality and reliable services. Their professionalism and attention to detail consistently set them apart."],
            ["@type" => "Review", "author" => ["@type" => "Person", "name" => "Tobiloba Babalola"], "reviewRating" => ["@type" => "Rating", "ratingValue" => "5", "bestRating" => "5"], "reviewBody" => "Working with GetOnline Studio was an excellent experience. He demonstrated professionalism and delivered beyond expectations."],
            ["@type" => "Review", "author" => ["@type" => "Person", "name" => "Mr. Ogundeji Sinmisola"], "reviewRating" => ["@type" => "Rating", "ratingValue" => "5", "bestRating" => "5"], "reviewBody" => "They really met our expectations and our church vision. Good work."],
        ],
    ];
    ?>
    <script type="application/ld+json"><?= json_encode($breadcrumb_landing, JSON_UNESCAPED_SLASHES) ?></script>
    <script type="application/ld+json"><?= json_encode($rating_landing, JSON_UNESCAPED_SLASHES) ?></script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Manrope:wght@300;400;600;700&family=Syne:wght@400;700;800&family=Fira+Code:wght@400;600&family=Space+Grotesk:wght@400;700&display=swap" rel="stylesheet">
    <script src="https://unpkg.com/lucide@latest"></script>
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        'matte-black': '#101010', 'card-dark': '#0a0a0a',
                        'lavender': '#e9d5ff', 'sharp-purple': '#7e22ce',
                        'off-white': '#f5f5f5', 'code-green': '#4ade80',
                    },
                    fontFamily: {
                        'syne': ['Syne', 'sans-serif'], 'manrope': ['Manrope', 'sans-serif'],
                        'mono': ['Fira Code', 'monospace'], 'space': ['Space Grotesk', 'sans-serif'],
                    },
                    backgroundImage: {
                        'noise': "url('data:image/svg+xml,%3Csvg viewBox=%220 0 200 200%22 xmlns=%22http://www.w3.org/2000/svg%22%3E%3Cfilter id=%22noiseFilter%22%3E%3CfeTurbulence type=%22fractalNoise%22 baseFrequency=%220.65%22 numOctaves=%223%22 stitchTiles=%22stitch%22/%3E%3C/filter%3E%3Crect width=%22100%25%22 height=%22100%25%22 filter=%22url(%23noiseFilter)%22 opacity=%220.05%22/%3E%3C/svg%3E')",
                    },
                    animation: { 'spin-slow': 'spin 15s linear infinite', 'float': 'float 6s ease-in-out infinite' }
                }
            }
        }
    </script>
    <style>
        body { background-color: #101010; color: #e9d5ff; overflow-x: hidden; cursor: auto; }
        .cursor-dot, .cursor-outline { display: none; }
        .perspective-grid { position: absolute; width: 200%; height: 200%; background-image: linear-gradient(rgba(126, 34, 206, 0.3) 1px, transparent 1px), linear-gradient(90deg, rgba(126, 34, 206, 0.3) 1px, transparent 1px); background-size: 100px 100px; transform: perspective(500px) rotateX(60deg) translateY(-100px) translateZ(-200px); animation: gridMove 20s linear infinite; opacity: 0.2; pointer-events: none; }
        @keyframes gridMove { 0% { transform: perspective(500px) rotateX(60deg) translateY(0) translateZ(-200px); } 100% { transform: perspective(500px) rotateX(60deg) translateY(100px) translateZ(-200px); } }
        .reveal-up { opacity: 0; transform: translateY(50px); animation: fadeUp 1s cubic-bezier(0.25, 1, 0.5, 1) forwards; }
        @keyframes fadeUp { to { opacity: 1; transform: translateY(0); } }
        .text-stroke { -webkit-text-stroke: 1px #e9d5ff; color: transparent; transition: all 0.3s ease; }
        .marquee-container { overflow: hidden; white-space: nowrap; display: flex; position: relative; }
        .marquee-content { display: flex; flex-shrink: 0; min-width: 100%; animation: scroll 30s linear infinite; }
        @keyframes scroll { from { transform: translateX(0); } to { transform: translateX(-100%); } }
        .tilt-card { transition: transform 0.1s; transform-style: preserve-3d; }
        .service-row { transition: border-color 0.3s; }
        .service-row:hover { border-color: #7e22ce; }
        .service-row:hover h2 { color: #7e22ce; padding-left: 20px; }
        .service-row h2 { transition: all 0.4s ease; }
        .mouse-preview { position: fixed; top: 0; left: 0; width: 300px; height: 200px; background-size: cover; background-position: center; border-radius: 8px; pointer-events: none; transform: translate(-50%, -50%) scale(0); opacity: 0; z-index: 50; transition: transform 0.1s, opacity 0.3s ease; border: 1px solid rgba(126, 34, 206, 0.3); box-shadow: 0 20px 50px rgba(0,0,0,0.5); }
        .mouse-preview.active { opacity: 1; transform: translate(-50%, -50%) scale(1); }
        @media (max-width: 768px) { .mouse-preview { display: none !important; } }
        .project-img { transition: transform 1.5s cubic-bezier(0.25, 1, 0.5, 1), filter 0.5s ease; }
        .group:hover .project-img { transform: scale(1.05); filter: grayscale(0%) !important; }
        .faq-content { max-height: 0; overflow: hidden; transition: max-height 0.3s ease-out; }
        .faq-item.active .faq-content { max-height: 500px; }
        .faq-item.active .faq-icon { transform: rotate(45deg); }
        .widget-hidden { opacity: 0; transform: scale(0.95) translateY(10px); pointer-events: none; visibility: hidden; }
        .widget-visible { opacity: 1; transform: scale(1) translateY(0); pointer-events: auto; visibility: visible; }
        .hide-scrollbar { -ms-overflow-style: none; scrollbar-width: none; }
        .hide-scrollbar::-webkit-scrollbar { display: none; }
    </style>
<?php wp_head(); ?>
</head>
<body class="bg-matte-black bg-noise font-manrope selection:bg-sharp-purple selection:text-white relative">

    <!-- NAV -->
    <nav class="fixed top-0 left-0 right-0 z-40 flex justify-between items-center px-4 md:px-8 py-4 md:py-5 bg-matte-black/80 backdrop-blur-md border-b border-lavender/10 pointer-events-none">
        <a href="https://getonlinestudio.com" style="pointer-events: auto;" class="font-syne font-bold text-xl md:text-2xl hover:text-sharp-purple transition-colors hover-target">GO.</a>
        <a href="#consultation" style="pointer-events: auto;" class="text-xs md:text-sm font-bold tracking-widest uppercase border border-lavender px-4 md:px-6 py-2 rounded-full hover:bg-lavender hover:text-matte-black transition-all duration-300 bg-matte-black/20 backdrop-blur-sm hover-target">
            Start Project
        </a>
    </nav>

    <!-- Floating WhatsApp Widget -->
    <div class="fixed bottom-6 right-6 z-50 flex flex-col items-end pointer-events-none">
        <div id="wa-float-window" class="widget-hidden mb-4 w-[90vw] max-w-[340px] bg-card-dark border border-lavender/20 rounded-2xl shadow-2xl flex-col overflow-hidden transition-all duration-300 origin-bottom-right pointer-events-auto">
            <div class="bg-[#151515] p-4 border-b border-lavender/10 flex justify-between items-center cursor-pointer hover-target" onclick="toggleWaWidget()">
                <div class="flex items-center gap-3">
                    <div class="w-2 h-2 bg-green-500 rounded-full animate-pulse"></div>
                    <span class="font-syne font-bold text-white">GetOnline Studio</span>
                </div>
                <button type="button" onclick="event.stopPropagation(); toggleWaWidget();" class="text-lavender/50 hover:text-white transition-colors focus:outline-none" style="pointer-events: auto;" aria-label="Close WhatsApp widget"><i data-lucide="x" class="w-5 h-5"></i></button>
            </div>
            <div class="p-5">
                <p class="text-sm font-manrope text-lavender/80 mb-4 leading-relaxed">
                    Hello! What digital services do you need for your <span class="text-white font-bold"><?= go_safe_text($niche_name) ?></span> in <span class="text-white font-bold"><?= go_safe_text($city_name) ?></span>?
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
                    <label class="flex items-center gap-3 p-2.5 rounded-lg border border-white/5 bg-matte-black hover:border-sharp-purple/50 cursor-pointer transition-colors hover-target">
                        <input type="checkbox" value="Social Media Setup" class="wa-service-cb w-4 h-4 accent-sharp-purple rounded focus:ring-sharp-purple">
                        <span class="text-sm text-lavender/90 font-bold">Social Media Setup</span>
                    </label>
                    <label class="flex items-center gap-3 p-2.5 rounded-lg border border-white/5 bg-matte-black hover:border-sharp-purple/50 cursor-pointer transition-colors hover-target">
                        <input type="checkbox" value="Website Maintenance" class="wa-service-cb w-4 h-4 accent-sharp-purple rounded focus:ring-sharp-purple">
                        <span class="text-sm text-lavender/90 font-bold">Website Maintenance</span>
                    </label>
                </div>
                <button type="button" onclick="sendWaWidget()" class="w-full bg-[#25D366] text-white font-bold py-3.5 rounded-xl hover:bg-[#1ebe5d] transition-all hover-target shadow-lg shadow-[#25D366]/20 flex items-center justify-center gap-2 uppercase tracking-wide text-xs min-h-[44px]">
                    <i data-lucide="message-circle" class="w-4 h-4"></i> Continue to WhatsApp
                </button>
            </div>
        </div>
        <button type="button" id="wa-float-btn" onclick="toggleWaWidget()" class="w-14 h-14 bg-[#25D366] rounded-full flex items-center justify-center text-white shadow-[0_4px_20px_rgba(37,211,102,0.4)] hover:bg-[#1ebe5d] hover:scale-105 transition-all focus:outline-none hover-target pointer-events-auto">
            <i data-lucide="message-circle" class="w-6 h-6"></i>
        </button>
    </div>

    <!-- SECTION 1: HERO -->
    <header class="relative min-h-[90vh] flex flex-col justify-center items-center px-4 overflow-hidden border-b border-lavender/10 pt-32 pb-24 md:pb-32">
        <div class="perspective-grid"></div>
        <div class="absolute w-32 h-32 rounded-full border border-sharp-purple/20 top-[15%] left-[10%] animate-float" style="animation-delay: 0s;"></div>
        <div class="absolute w-24 h-24 rotate-45 border border-lavender/10 top-[20%] right-[15%] animate-float" style="animation-delay: 1.5s;"></div>
        
        <!-- BREADCRUMBS -->
        <div class="absolute top-24 left-4 md:left-8 z-30 reveal-up" style="animation-delay: 0.1s;">
            <nav aria-label="Breadcrumb">
                <ol class="flex flex-wrap items-center gap-2 text-[10px] md:text-xs font-mono uppercase tracking-widest text-lavender/40">
                    <li><a href="/" class="hover:text-sharp-purple transition-colors hover-target">Home</a></li>
                    <li><span class="opacity-30">/</span></li>
                    <li><a href="/locations/" class="hover:text-sharp-purple transition-colors hover-target">Locations</a></li>
                    <li><span class="opacity-30">/</span></li>
                    <li><a href="/locations/<?= $city_slug ?>/" class="hover:text-sharp-purple transition-colors hover-target"><?= go_safe_text($base_city_name) ?></a></li>
                    <?php if (!empty($neighborhood_name)): ?>
                    <li><span class="opacity-30">/</span></li>
                    <li><span class="text-lavender/70"><?= go_safe_text($neighborhood_name) ?></span></li>
                    <?php endif; ?>
                    <li><span class="opacity-30">/</span></li>
                    <li><span class="text-lavender/70 truncate max-w-[120px] sm:max-w-none" aria-current="page"><?= go_safe_text($niche_name) ?></span></li>
                </ol>
            </nav>
        </div>

        <div class="relative z-20 text-center max-w-5xl mx-auto mt-10 md:mt-0">
            
            <!-- THE HERO STAT BADGE -->
            <?php if ($digital_gap_percent > 50): ?>
            <div class="flex items-center justify-center mb-6 reveal-up">
                <div class="inline-flex items-center gap-2 px-4 py-2 rounded-full bg-red-500/10 border border-red-500/20 text-red-400 text-[10px] md:text-xs font-bold uppercase tracking-widest backdrop-blur-md">
                    <i data-lucide="alert-circle" class="w-4 h-4 text-red-500"></i>
                    Market Alert: <?= $digital_gap_percent ?>% of <?= go_safe_text($city_name) ?> <?= strtolower(go_safe_text($niche_plural)) ?> have no website
                </div>
            </div>
            <?php endif; ?>

            <h1 class="font-syne text-[8vw] md:text-[5vw] leading-[1.1] font-bold text-lavender reveal-up" style="animation-delay: 0.1s;">
                <?= go_safe_text($niche_name) ?> <span class="text-transparent text-stroke hover:text-sharp-purple transition-colors duration-300 cursor-default"><?= go_safe_text($service_name) ?></span> in <?= go_safe_text($city_name) ?>
            </h1>
            <p class="font-manrope text-lavender/70 text-lg md:text-xl max-w-3xl mx-auto mt-8 leading-relaxed reveal-up" style="animation-delay: 0.4s;">
                <?= go_spin_text("{Generic templates don't work for {$niche_plural}.|Most {$niche_name} websites are just digital brochures that don't drive real engagement.|You don't just need a website; you need a {$vocab['growth_system']}.}") ?> 
                <?php if ($digital_gap_percent > 0): ?>
                    In a market where <strong class="text-white"><?= $digital_gap_percent ?>% of your competitors are invisible online</strong>, we build specialized, high-converting platforms designed to make you the definitive leader in <?= go_safe_text($city_name) ?>.
                <?php else: ?>
                    We build specialized, high-converting platforms designed explicitly for the <?= go_safe_text($niche_name) ?> <?= $vocab['industry'] ?>. If you are searching for <strong><?= go_safe_text($lsi_keyword) ?></strong>, you have found the <?= $vocab['market_leader'] ?>.
                <?php endif; ?>
            </p>
            <div class="mt-12 flex flex-col sm:flex-row items-center justify-center gap-4 reveal-up" style="animation-delay: 0.6s;">
                <a href="#consultation" class="w-full sm:w-auto bg-sharp-purple text-white px-10 py-4 rounded-full text-sm font-bold uppercase tracking-widest hover:bg-white hover:text-matte-black transition-all hover-target shadow-[0_0_30px_rgba(126,34,206,0.3)]">
                    Start Your Project &rarr;
                </a>
                <?php $wa_message = rawurlencode("Hi GetOnline Studio,\n\nI found your page while searching for: {$exact_keyword}\n\nPage: {$niche_name} {$service_name} in {$city_name}\n\nI am looking for a {$niche_name} {$service_name} and would like to discuss my project.\n\nLet's talk!"); ?>
                <a href="https://wa.me/2349061150443?text=<?= $wa_message ?>" target="_blank" class="w-full sm:w-auto border border-lavender/30 text-lavender px-10 py-4 rounded-full text-sm font-bold uppercase tracking-widest hover:bg-lavender hover:text-matte-black transition-all hover-target flex items-center justify-center gap-2">
                    <i data-lucide="message-circle" class="w-4 h-4"></i> WhatsApp
                </a>
            </div>
        </div>
    </header>

    <!-- SECTION 1.4: TRUST STATS BAR -->
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
                    <div class="font-syne text-3xl md:text-4xl font-bold text-white">40+</div>
                    <div class="font-mono text-[10px] text-lavender/50 uppercase tracking-widest mt-1">Industries Served</div>
                </div>
                <div>
                    <div class="font-syne text-3xl md:text-4xl font-bold text-white">100%</div>
                    <div class="font-mono text-[10px] text-lavender/50 uppercase tracking-widest mt-1">Client Satisfaction</div>
                </div>
            </div>
        </div>
    </section>

    <!-- DYNAMIC MACRO-MODULES -->
    <main>
        <?php 
        $block_count = 0;
        foreach ($master_layout as $block_id) {
            echo $html_blocks[$block_id];
            $block_count++;
            
            if ($block_count === 3): ?>
                <div class="py-12 px-4 md:px-6 bg-[#0d0d0d] border-b border-lavender/10 flex flex-col sm:flex-row items-center justify-between gap-6 max-w-7xl mx-auto">
                    <p class="font-syne text-lg md:text-2xl font-bold text-lavender text-center sm:text-left">
                        <?= go_spin_text("{Like what you see?|Ready to scale?|Need a platform like this?}") ?> Let's talk about your <span class="text-sharp-purple"><?= go_safe_text($niche_name) ?></span> project.
                    </p>
                    <a href="https://wa.me/2349061150443?text=<?= $wa_message ?>" target="_blank" class="flex-shrink-0 inline-flex items-center gap-3 bg-[#25D366] text-white font-syne font-bold uppercase tracking-widest text-sm px-8 py-4 rounded-full hover:bg-[#1ebe5d] transition-all duration-300 shadow-[0_0_24px_rgba(37,211,102,0.25)] hover-target">
                        <i data-lucide="message-circle" class="w-5 h-5"></i> WhatsApp Us
                    </a>
                </div>
                
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
            <?php endif;
        }
        ?>
    </main>

    <!-- SECTION 8B: INTERNAL LINK SILO -->
    <section class="py-20 px-4 md:px-6 bg-[#0d0d0d] border-t border-lavender/10 relative z-10">
        <div class="max-w-6xl mx-auto">
            <div class="mb-10 pb-8 border-b border-lavender/10">
                <p class="font-mono text-[10px] text-sharp-purple tracking-widest uppercase mb-3">[ <?= go_safe_text($city_name) ?> Hub ]</p>
                <a href="<?= esc_url($hub_link['url']) ?>" class="font-syne text-base md:text-lg text-lavender hover:text-sharp-purple transition-colors duration-300 inline-flex items-center gap-3 group hover-target">
                    <i data-lucide="arrow-left" class="w-4 h-4 text-sharp-purple group-hover:-translate-x-1 transition-transform duration-300 flex-shrink-0"></i>
                    <?= go_safe_text($hub_link['label']) ?>
                </a>
            </div>

            <?php if (!empty($neighborhood_links)): ?>
            <div class="mb-10 pb-8 border-b border-white/5">
                <p class="font-mono text-[10px] text-lavender/40 tracking-widest uppercase mb-5">
                    <?= empty($neighborhood_name) ? "Hyper-Local" : "Nearby" ?> <?= go_safe_text($niche_name) ?> Hubs in <?= go_safe_text($base_city_name) ?>
                </p>
                <div class="flex flex-wrap gap-3">
                    <?php foreach ($neighborhood_links as $link): ?>
                    <a href="<?= esc_url($link['url']) ?>" class="px-4 py-2 bg-white/5 border border-white/10 rounded-full text-xs text-lavender/70 hover:text-white hover:border-sharp-purple hover:bg-sharp-purple/10 transition-all hover-target">
                        <?= go_safe_text($link['label']) ?>
                    </a>
                    <?php endforeach; ?>
                </div>
            </div>
            <?php endif; ?>

            <div class="grid md:grid-cols-2 gap-10 md:gap-16">
                <div>
                    <p class="font-mono text-[10px] text-lavender/40 tracking-widest uppercase mb-5">More Services in <?= go_safe_text($city_name) ?></p>
                    <ul class="space-y-3">
                        <?php foreach ($horizontal_links as $link): ?>
                        <li>
                            <a href="<?= esc_url($link['url']) ?>" class="font-manrope text-lavender/60 hover:text-lavender transition-colors duration-200 text-sm flex items-center gap-3 group hover-target">
                                <span class="w-5 h-px bg-sharp-purple/30 group-hover:w-8 group-hover:bg-sharp-purple transition-all duration-300 flex-shrink-0"></span>
                                <?= go_safe_text($link['label']) ?>
                            </a>
                        </li>
                        <?php endforeach; ?>
                    </ul>
                </div>
                <div>
                    <p class="font-mono text-[10px] text-lavender/40 tracking-widest uppercase mb-5"><?= go_safe_text($niche_name) ?> <?= go_safe_text($service_name) ?> in Other Cities</p>
                    <ul class="space-y-3">
                        <?php foreach ($vertical_links as $link): ?>
                        <li>
                            <a href="<?= esc_url($link['url']) ?>" class="font-manrope text-lavender/60 hover:text-lavender transition-colors duration-200 text-sm flex items-center gap-3 group hover-target">
                                <span class="w-5 h-px bg-sharp-purple/30 group-hover:w-8 group-hover:bg-sharp-purple transition-all duration-300 flex-shrink-0"></span>
                                <?= go_safe_text($link['label']) ?>
                            </a>
                        </li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            </div>
        </div>
    </section>

    <!-- SECTION 9: FINAL CTA -->
    <section id="consultation" class="py-32 md:py-40 px-4 md:px-6 text-center bg-sharp-purple text-white relative z-20 overflow-hidden">
        <div class="absolute inset-0 bg-[url('data:image/svg+xml,%3Csvg viewBox=%220 0 200 200%22 xmlns=%22http://www.w3.org/2000/svg%22%3E%3Cfilter id=%22noiseFilter%22%3E%3CfeTurbulence type=%22fractalNoise%22 baseFrequency=%220.65%22 numOctaves=%223%22 stitchTiles=%22stitch%22/%3E%3C/filter%3E%3Crect width=%22100%25%22 height=%22100%25%22 filter=%22url(%23noiseFilter)%22 opacity=%220.08%22/%3E%3C/svg%3E')] mix-blend-overlay"></div>
        <div class="max-w-4xl mx-auto reveal-up relative z-10">
            <h2 class="font-syne text-4xl md:text-7xl font-bold mb-4 text-white leading-tight">
                Ready to <?= $vocab['cta_action'] ?>?<br><?= go_spin_text("{Let's make it happen.|Let's get started.|Claim your market share.}") ?>
            </h2>
            <p class="font-manrope text-lg md:text-xl mb-12 text-white/90 leading-relaxed max-w-2xl mx-auto">
                Ready to work with the leading <strong><?= go_safe_text($niche_name) ?> <?= go_safe_text($sv['sv_identity']) ?></strong> in <strong><?= go_safe_text($city_name) ?></strong>? Every day without a great website is <?= $vocab['daily_loss'] ?>. Let's start your project today.
            </p>
            <div class="flex flex-col sm:flex-row items-center justify-center gap-4">
                <a href="mailto:hello@getonlinestudio.com?subject=<?= rawurlencode("Project Inquiry from {$city_name} for {$niche_name}") ?>" class="w-full sm:w-auto bg-matte-black text-white px-10 py-5 rounded-full text-sm font-bold uppercase tracking-widest hover:bg-white hover:text-matte-black transition-all hover-target shadow-2xl">
                    Start Your Project &rarr;
                </a>
                <a href="https://wa.me/2349061150443?text=<?= $wa_message ?>" target="_blank" class="w-full sm:w-auto bg-green-500 text-white px-10 py-5 rounded-full text-sm font-bold uppercase tracking-widest hover:bg-green-400 transition-all hover-target shadow-xl flex items-center justify-center gap-2">
                    <i data-lucide="message-circle" class="w-5 h-5"></i> Chat on WhatsApp
                </a>
            </div>
        </div>
    </section>

    <!-- FOOTER -->
    <footer class="bg-[#0d0d0d] border-t border-lavender/10 relative z-20 overflow-hidden">
        <div class="absolute inset-0 bg-[radial-gradient(circle_at_top_left,rgba(126,34,206,0.08),transparent_32%),radial-gradient(circle_at_bottom_right,rgba(34,197,94,0.05),transparent_28%)] pointer-events-none"></div>
        <div class="max-w-7xl mx-auto px-4 md:px-8 relative">

            <div class="py-16 md:py-20 grid lg:grid-cols-2 gap-12 border-b border-lavender/10">
                <div>
                    <p class="font-mono text-[10px] text-sharp-purple tracking-widest uppercase mb-4">[ Let's Build Something Strong ]</p>
                    <h2 class="font-syne text-4xl md:text-6xl font-bold text-white leading-tight mb-5">GetOnline Studio</h2>
                    <p class="font-manrope text-lavender/65 text-sm md:text-base leading-relaxed max-w-xl mb-6">
                        We build serious websites and digital systems for <?= go_safe_text($niche_plural) ?> in <?= go_safe_text($city_name) ?> and across Nigeria. From design and SEO to branding, automation, and support, we help your organisation look credible, get found, and grow with confidence.
                    </p>
                    <div class="flex flex-col sm:flex-row sm:items-center gap-4 text-sm">
                        <a href="mailto:hello@getonlinestudio.com?subject=<?= rawurlencode("Project Inquiry from {$city_name} for {$niche_name}") ?>" class="inline-flex items-center gap-2 text-sharp-purple hover:text-white transition-colors hover-target break-all">
                            <i data-lucide="mail" class="w-4 h-4"></i>
                            <span>hello@getonlinestudio.com</span>
                        </a>
                        <a href="<?= esc_url("https://wa.me/2349061150443?text={$wa_message}") ?>" target="_blank" rel="noopener noreferrer" class="inline-flex items-center gap-2 text-[#25D366] hover:text-white transition-colors hover-target">
                            <i data-lucide="message-circle" class="w-4 h-4"></i>
                            <span>+234 906 115 0443</span>
                        </a>
                    </div>
                </div>

                <div class="lg:pt-4">
                    <p class="font-mono text-[10px] text-sharp-purple tracking-widest uppercase mb-4">[ This Niche Hub ]</p>
                    <h3 class="font-syne text-2xl font-bold text-white mb-4"><?= go_safe_text($niche_name) ?> <?= go_safe_text($service_name) ?> in <?= go_safe_text($city_name) ?></h3>
                    <p class="font-manrope text-lavender/60 text-sm leading-relaxed mb-4">
                        This page is part of our location and niche ecosystem built to help <?= strtolower(go_safe_text($niche_plural)) ?> find a digital partner that understands their market, audience, and local search realities.
                    </p>
                    <p class="font-manrope text-lavender/60 text-sm leading-relaxed">
                        Whether you need a new website, stronger visibility on Google, or a smarter digital foundation for your <?= strtolower(go_safe_text($niche_name)) ?>, we can help you move with clarity.
                    </p>
                </div>
            </div>

            <div class="py-12 grid grid-cols-2 md:grid-cols-4 gap-8 border-b border-lavender/10">
                <div>
                    <p class="font-mono text-[10px] text-lavender/30 tracking-widest uppercase mb-5">Company</p>
                    <ul class="space-y-3 font-manrope text-sm">
                        <li><a href="/" class="text-lavender/60 hover:text-white transition-colors hover-target">Home</a></li>
                        <li><a href="/about/" class="text-lavender/60 hover:text-white transition-colors hover-target">About Us</a></li>
                        <li><a href="/testimonials/" class="text-lavender/60 hover:text-white transition-colors hover-target">Testimonials</a></li>
                        <li><a href="/work/" class="text-lavender/60 hover:text-white transition-colors hover-target">Projects &amp; Case Studies</a></li>
                    </ul>
                </div>
                <div>
                    <p class="font-mono text-[10px] text-lavender/30 tracking-widest uppercase mb-5">Services</p>
                    <ul class="space-y-3 font-manrope text-sm">
                        <li><a href="/services/web-design/" class="text-lavender/60 hover:text-white transition-colors hover-target">Website Design</a></li>
                        <li><a href="/services/seo/" class="text-lavender/60 hover:text-white transition-colors hover-target">SEO &amp; Google Ranking</a></li>
                        <li><a href="/services/branding/" class="text-lavender/60 hover:text-white transition-colors hover-target">Branding &amp; Identity</a></li>
                        <li><a href="/services/" class="text-lavender/60 hover:text-sharp-purple transition-colors hover-target font-bold">All Services &rarr;</a></li>
                    </ul>
                </div>
                <div>
                    <p class="font-mono text-[10px] text-lavender/30 tracking-widest uppercase mb-5">Explore</p>
                    <ul class="space-y-3 font-manrope text-sm">
                        <li><a href="<?= esc_url($hub_link['url']) ?>" class="text-lavender/60 hover:text-white transition-colors hover-target"><?= go_safe_text($hub_link['label']) ?></a></li>
                        <li><a href="/locations/" class="text-lavender/60 hover:text-white transition-colors hover-target">All Locations</a></li>
                        <?php if (!empty($horizontal_links[0])): ?><li><a href="<?= esc_url($horizontal_links[0]['url']) ?>" class="text-lavender/60 hover:text-white transition-colors hover-target"><?= go_safe_text($horizontal_links[0]['label']) ?></a></li><?php endif; ?>
                        <?php if (!empty($vertical_links[0])): ?><li><a href="<?= esc_url($vertical_links[0]['url']) ?>" class="text-lavender/60 hover:text-white transition-colors hover-target"><?= go_safe_text($vertical_links[0]['label']) ?></a></li><?php endif; ?>
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

            <div class="pt-8 pb-10 flex flex-col md:flex-row justify-between items-center gap-4 font-manrope text-xs text-lavender/30">
                <p>GetOnline Studio &copy; <?= date('Y') ?> Proudly serving <?= go_safe_text($city_name) ?> and every city in Nigeria.</p>
                <p class="font-mono uppercase tracking-widest"><?= go_safe_text($niche_name) ?> Digital Infrastructure. Built for Growth.</p>
            </div>

        </div>
    </footer>

    <script>
        lucide.createIcons();

        const isTouchDevice = 'ontouchstart' in window || navigator.maxTouchPoints > 0;

        function tiltCard(event, card) {}
        function resetTilt(card) {}

        if (!isTouchDevice) {
            document.querySelectorAll('[onmousemove*="tiltCard"]').forEach(card => {
                card.addEventListener('mousemove', (e) => {
                    const r = card.getBoundingClientRect();
                    const rx = ((e.clientY - r.top - r.height/2) / (r.height/2)) * -10;
                    const ry = ((e.clientX - r.left - r.width/2) / (r.width/2)) * 10;
                    card.style.transform = `perspective(1000px) rotateX(${rx}deg) rotateY(${ry}deg) scale(1.05)`;
                });
                card.addEventListener('mouseleave', () => { card.style.transform = ''; });
            });
        }

        function toggleWaWidget() {
            const win = document.getElementById('wa-float-window');
            if (!win) return;
            const isHidden = win.classList.contains('widget-hidden');
            win.classList.toggle('widget-hidden', !isHidden);
            win.classList.toggle('widget-visible', isHidden);
        }

        function openWaWidgetWithService(serviceName) {
            const win = document.getElementById('wa-float-window');
            if (!win) return;
            win.classList.remove('widget-hidden');
            win.classList.add('widget-visible');
            document.querySelectorAll('.wa-service-cb').forEach(cb => {
                cb.checked = (cb.value === serviceName) || serviceName.includes(cb.value) || cb.value.includes(serviceName);
            });
            setTimeout(() => window.scrollTo({ top: document.body.scrollHeight, behavior: 'smooth' }), 80);
        }

        function sendWaWidget() {
            const checked = Array.from(document.querySelectorAll('.wa-service-cb:checked')).map(cb => cb.value);
            const niche = '<?= addslashes(go_safe_text($niche_name)) ?>';
            const city  = '<?= addslashes(go_safe_text($city_name)) ?>';
            const keyword = '<?= addslashes(go_safe_text($exact_keyword)) ?>';
            const services = checked.length ? checked.join(', ') : 'General Enquiry';
            const msg = encodeURIComponent(`Hi GetOnline Studio,\n\nI found you while searching for: ${keyword}\n\nI need help with: ${services} for my ${niche} in ${city}.\n\nLet's talk!`);
            window.open(`https://wa.me/2349061150443?text=${msg}`, '_blank', 'noopener');
            toggleWaWidget();
        }

        function toggleExtraFaqs() {
            const extra = document.getElementById('faq-extra');
            const btn   = document.getElementById('faq-show-more-btn');
            const label = document.getElementById('faq-btn-label');
            const icon  = document.getElementById('faq-btn-icon');
            if (!extra) return;

            const isOpen = extra.style.opacity === '1';
            if (isOpen) {
                extra.style.maxHeight = extra.scrollHeight + 'px';
                requestAnimationFrame(() => {
                    extra.style.maxHeight    = '0';
                    extra.style.opacity      = '0';
                    extra.style.pointerEvents = 'none';
                });
                label.textContent    = 'More Questions';
                icon.style.transform = 'rotate(0deg)';
                btn.scrollIntoView({ behavior: 'smooth', block: 'center' });
            } else {
                extra.style.maxHeight    = extra.scrollHeight + 2000 + 'px';
                extra.style.opacity      = '1';
                extra.style.pointerEvents = 'auto';
                label.textContent    = 'Show Less';
                icon.style.transform = 'rotate(180deg)';
            }
        }
    </script>
<?php wp_footer(); ?>
</body>
</html>