<?php
/**
 * NATIONAL NICHE HUB TEMPLATE, v6.0 (ULTRA-RICH PILLAR PAGE)
 * Routes URLs like: /services/restaurant/ OR /services/restaurant-website-designer/
 * Upgraded with: Full Spintax Engine, Pricing Calculator, FAQs, National Authority Copy,
 * and Contextual Vocabulary Engine (Non-Profit vs Commercial).
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

function go_safe_text($text) {
    $text = str_replace(['—', '–'], '-', (string)($text ?? ''));
    return esc_html(html_entity_decode($text, ENT_QUOTES, 'UTF-8'));
}

// DETERMINISTIC spintax, locked per niche+service combo, never random per page load
function go_spin_text($text, $seed = null) {
    global $master_seed;
    $active_seed = $seed ?? ($master_seed ?? crc32('fallback'));
    $text = (string) $text;
    return preg_replace_callback('/\{(((?>[^\{\}]+)|(?R))*)\}/x', function ($match) use ($active_seed) {
        $inner = go_spin_text($match[1], $active_seed);
        $parts = explode('|', $inner);
        $hash  = md5($active_seed . $match[0]);
        $index = hexdec(substr($hash, 0, 8)) % count($parts);
        return $parts[$index];
    }, $text);
}

// 2. Catch and Parse URL Parameters
$raw_slug = isset($_GET['niche']) ? sanitize_title($_GET['niche']) : 'business';

// Fetch all niches
$active_niches_query = get_posts(['post_type' => 'pseo_niche', 'numberposts' => -1, 'post_status' => 'publish']);
$niche_dict = [];
$niche_slugs = [];
foreach ($active_niches_query as $n) {
    $niche_dict[$n->post_name] = $n->post_title;
    $niche_slugs[] = $n->post_name;
}

if (empty($niche_slugs)) {
    $niche_slugs = ['business'];
    $niche_dict = ['business' => 'Business'];
}

usort($niche_slugs, function($a, $b) { return strlen($b) - strlen($a); });

$niche_slug   = 'business';
$service_slug = 'website-designer';

foreach ($niche_slugs as $n) {
    if (strpos($raw_slug, $n) === 0) {
        $niche_slug = $n;
        $extracted_service = str_replace($n . '-', '', $raw_slug);
        if ($extracted_service !== $n && !empty($extracted_service)) {
            $service_slug = $extracted_service;
        }
        break;
    }
}

// Ensure Niche actually exists in DB
$niche_post = get_page_by_path($niche_slug, OBJECT, 'pseo_niche');
if (!$niche_post || $niche_post->post_status !== 'publish') {
    status_header(404);
    wp_redirect('/services/');
    exit;
}

// 3. Format the Names
$niche_name   = isset($niche_dict[$niche_slug]) ? $niche_dict[$niche_slug] : ucwords(str_replace('-', ' ', $niche_slug));
$service_name = ucwords(str_replace('-', ' ', $service_slug));
if ($service_slug === 'website-designer' && $raw_slug === $niche_slug) {
    $service_name = "Digital Agency";
}

// --- NATIONAL OVERRIDES ---
$city_name = "Nigeria";
$city_slug = "nigeria"; // Setting this safely bypasses local matrix DB queries for the calculator
$exact_keyword = "{$niche_name} {$service_name} in {$city_name}";

$niche_raw_meta = get_post_meta($niche_post->ID, '_pseo_niche_data', true) ?: [];
$is_non_profit = in_array($niche_slug, ['church', 'ngo']);
$is_education  = in_array($niche_slug, ['school', 'polytechnic', 'university', 'music-school', 'driving-school', 'sports-academy']);
$is_soft_niche = $is_non_profit || $is_education;

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
    'advertising-agency'     => 'Advertising Agencies',
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
    'school'                 => 'Schools',
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
    // Finance (additional)
    'investment-company'     => 'Investment Companies',
    'asset-management'       => 'Asset Management Firms',
    'pension-fund'           => 'Pension Funds',
    'forex-trading'          => 'Forex Trading Platforms',
    // Services (additional)
    'event-centre'           => 'Event Centres',
    'cleaning-company'       => 'Cleaning Companies',
    'laundry-service'        => 'Laundry Services',
    'catering-company'       => 'Catering Companies',
];
$niche_plural = !empty($niche_raw_meta['plural'])
    ? $niche_raw_meta['plural']
    : ($niche_plural_overrides[$niche_slug] ?? $niche_name . 's');

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
} else {
    $vocab['audience']       = "high-value clients";
    $vocab['projection']     = "market leadership";
    $vocab['loss_state']     = "voluntarily handing your market share over to your competitors";
    $vocab['daily_loss']     = "a day a potential client chose your competitor instead";
    $vocab['insight_title']  = "National Search Demand";
    $vocab['growth_system']  = "growth system";
    $vocab['cta_action']     = "scale your {$niche_name}";
    $vocab['footer_tagline'] = "National Scale. Local Dominance.";
    $vocab['market_leader']  = "definitive market leader";
    $vocab['industry']       = "industry";
}

// 4. Fetch All Active Cities for the Routing Grid
$active_cities_query = get_posts(['post_type' => 'pseo_location', 'numberposts' => -1, 'post_status' => 'publish', 'orderby' => 'title', 'order' => 'ASC']);
$all_cities = [];
foreach ($active_cities_query as $c) {
    $all_cities[$c->post_name] = $c->post_title;
}

// ─── CRITICAL SEO FIX: DETERMINISTIC SEEDING ─────────────────────────────────
$master_seed = crc32("nigeria" . $niche_slug . $service_slug);
srand($master_seed);

mt_srand($master_seed + 100);
$outcome_order = [1, 2, 3, 4];
shuffle($outcome_order);

mt_srand($master_seed + 200);
$feature_order = [1, 2, 3, 4, 5, 6];
shuffle($feature_order);

mt_srand($master_seed + 300);
$faq_order = [1, 2, 3, 4, 5, 6];
shuffle($faq_order);

// ─── VOCABULARY ENGINE ───────────────────────────────────────────────────────
$service_vocab_map = [
    'website-designer' => [
        'sv_identity'  => 'website designer', 'sv_action' => '{design and build|create|launch}', 'sv_output' => '{a clean, highly professional website|a modern, easy-to-navigate website|a premium online presence}', 'sv_proof' => '{builds instant credibility|makes a perfect first impression|highlights your actual value}', 'sv_tools' => '{clear messaging, beautiful layouts, and simple contact forms|modern design principles and easy-to-use layouts|professional branding and straightforward user experiences}', 'sv_clientwin' => "{look like the absolute best option|win over customers before they even speak to you|charge premium rates because your brand looks premium}",
    ],
    'web-design-agency' => [
        'sv_identity'  => 'web design agency', 'sv_action' => '{deliver|build and manage|launch}', 'sv_output' => '{a complete digital marketing presence|a full-service website solution|an end-to-end online platform}', 'sv_proof' => '{handles your marketing while you run your business|takes the stress out of getting online|brings everything together in one place}', 'sv_tools' => '{smart design, clear copywriting, and reliable ongoing support|a dedicated team approach, modern design, and everyday reliability}', 'sv_clientwin' => '{focus on running your business while we handle the digital side|get a complete, stress-free digital upgrade|stop worrying about your website and start getting actual results}',
    ]
];
$sv = strpos($service_slug, 'agency') !== false ? $service_vocab_map['web-design-agency'] : $service_vocab_map['website-designer'];

// Strip commercial terms from SV if non-profit
if ($is_non_profit) {
    $sv['sv_clientwin'] = "{look like the absolute best option|welcome visitors before they even speak to you|build deep trust because your platform looks premium}";
}

// ─── LOAD BOILERPLATE THEMES ─────────────────────────────────────────────────
$boilerplates_file = __DIR__ . '/global-boilerplates.json';
$boilerplates = file_exists($boilerplates_file) ? json_decode(file_get_contents($boilerplates_file), true) : ['common' => [], 'themes' => [[]]];

$theme_count = count($boilerplates['themes']);
$theme_index = $master_seed % ($theme_count > 0 ? $theme_count : 1);
$selected_theme = isset($boilerplates['themes'][$theme_index]) ? $boilerplates['themes'][$theme_index] : $boilerplates['themes'][0];

$fallbacks = array_merge($boilerplates['common'], $selected_theme);

// --- NON-PROFIT BOILERPLATE OVERRIDE INTERCEPTOR ---
// Prevents e-commerce concepts (cash registers, inventory, delivery) from appearing on Church/NGO pages
if ($niche_slug === 'church') {
    $fallbacks['reality_p1'] = "The modern seeker in Nigeria values connection and authenticity. When people look for spiritual community or a church home, they search online first. If your digital presence is outdated or missing, they may never find the community you have built.";
    $fallbacks['reality_p2'] = "Mobile searches for local {niche_plural} have grown significantly across Nigeria. While your physical doors matter, your digital presence must always be welcoming. A professional church website makes it easy for new visitors to find service times, location, and how to connect.";
    $fallbacks['reality_p3'] = "We remove the friction between a searching visitor and an active member. As your {keyword}, we build a clean, welcoming platform that makes joining your church, giving online, and accessing sermons completely effortless.";
    $fallbacks['reality_p4'] = "Turn every smartphone in Nigeria into a window into your ministry. Share sermons, accept prayer requests, and grow your congregation — even when your physical doors are closed.";

    $fallbacks['pos1_title'] = "More Visitors Find Your Church";
    $fallbacks['pos1_desc'] = "We optimise your site to rank when people search 'church near me' or '[denomination] in [city]' — making it easy for new people to discover and choose your community.";
    $fallbacks['pos2_title'] = "Online Tithes and Offerings";
    $fallbacks['pos2_desc'] = "Members can give tithes, offerings, and special seeds directly from their phone via Paystack — no cash, no queue, no friction on Sunday morning.";
    $fallbacks['pos3_title'] = "Sermon Archive and Media";
    $fallbacks['pos3_desc'] = "Every message stays accessible. We build a clean sermon archive with audio, video, and series — so members and visitors can listen anytime, anywhere.";
    $fallbacks['pos4_title'] = "24/7 Ministry Presence";
    $fallbacks['pos4_desc'] = "Your website answers questions, shares service schedules, and accepts prayer requests around the clock — even when your staff is unavailable.";
} elseif ($niche_slug === 'ngo') {
    $fallbacks['reality_p1'] = "In Nigeria, trust is everything for an NGO. Donors, grant bodies, and volunteers research organisations online before they commit. If your digital presence is weak or outdated, credible funding and partnerships pass you by.";
    $fallbacks['reality_p2'] = "More donors and volunteers now discover organisations through search and social media than through direct referrals. A professional, well-structured website makes your mission visible to the right people at the right moment.";
    $fallbacks['reality_p3'] = "We remove the gap between a donor who cares and a cause that needs support. As your {keyword}, we build a clear, compelling platform that communicates your impact and makes giving or volunteering simple.";
    $fallbacks['reality_p4'] = "Turn every Nigerian with a smartphone into a potential supporter. Your website works around the clock — sharing impact reports, accepting donations, and recruiting volunteers even while your team sleeps.";

    $fallbacks['pos1_title'] = "More Donor Visibility";
    $fallbacks['pos1_desc'] = "We optimise your site to rank when institutional donors, corporate CSR teams, and individual givers search for reputable NGOs in your focus area or region.";
    $fallbacks['pos2_title'] = "Seamless Online Donations";
    $fallbacks['pos2_desc'] = "Supporters can donate to specific projects or general funds directly via Paystack — with automatic receipts and giving records generated for every transaction.";
    $fallbacks['pos3_title'] = "Credibility and Transparency";
    $fallbacks['pos3_desc'] = "Registration numbers, board profiles, audited impact reports, and partner logos — everything a grant body or corporate donor checks before they write a cheque.";
    $fallbacks['pos4_title'] = "Volunteer and Partner Pipeline";
    $fallbacks['pos4_desc'] = "A structured sign-up form brings in volunteers and programme partners continuously — with automated welcome emails so no interested person goes unacknowledged.";
} elseif ($is_education) {
    $fallbacks['reality_p1'] = "Modern parents and prospective students in Nigeria value transparency and prestige. When deciding on the right institution, they turn to the internet first. If your digital presence is outdated, they might overlook your academic excellence entirely.";
    $fallbacks['reality_p2'] = "Mobile searches for local {niche_plural} have skyrocketed across Nigeria. While your campus facilities are vital, your digital front door must always be welcoming. A highly-optimized website transforms your {niche_name} into the top choice for families researching online.";
    $fallbacks['reality_p3'] = "We eliminate the friction between a curious parent and an enrolled student. As your {keyword}, we use modern design to launch a beautiful platform that makes admissions, enquiries, and communication completely effortless.";
    $fallbacks['reality_p4'] = "Turn every smartphone in Nigeria into a window into your campus life, academic programmes, and student success stories. We give you the digital infrastructure to compete at the highest level.";

    $fallbacks['pos1_title'] = "More Admissions Enquiries";
    $fallbacks['pos1_desc'] = "We build clear, compelling admissions pathways: 'Apply Now', 'Schedule a Visit', 'Download Prospectus'. This makes it effortless for interested parents to reach out.";
    $fallbacks['pos2_title'] = "Google-Ranked for Parent Searches";
    $fallbacks['pos2_desc'] = "We optimise your platform for the exact searches parents use: 'best secondary school in Lagos', 'affordable primary school Abuja'. More searches mean more potential students.";
    $fallbacks['pos3_title'] = "Credibility & Trust Signals";
    $fallbacks['pos3_desc'] = "Accreditation badges, parent testimonials, awards, faculty profiles, and facility galleries. Everything parents needs to feel confident choosing your institution.";
    $fallbacks['pos4_title'] = "Frictionless Communication";
    $fallbacks['pos4_desc'] = "Your website answers common parent questions automatically, shares term dates, and provides instant contact. This saves your staff hours of repetitive WhatsApp replies.";
}

// Inject Niche Overrides
if ($niche_post && $niche_post->post_status === 'publish') {
    $core_keys = [
        // Reality section — written by reality-writer.php
        'reality_p1', 'reality_p2', 'reality_p3', 'reality_p4',
        // Outcome cards — written by reality-writer.php
        'pos1_title', 'pos1_desc', 'pos2_title', 'pos2_desc',
        'pos3_title', 'pos3_desc', 'pos4_title', 'pos4_desc',
        // Features & FAQs — written by niche-autobuilder
        'feat_headline', 'feat_subline',
        'f1_title', 'f1_desc', 'f2_title', 'f2_desc', 'f3_title', 'f3_desc',
        'f4_title', 'f4_desc', 'f5_title', 'f5_desc', 'f6_title', 'f6_desc',
        'faq1_q', 'faq1_a', 'faq2_q', 'faq2_a', 'faq3_q', 'faq3_a',
        'faq4_q', 'faq4_a', 'faq5_q', 'faq5_a', 'cta_aspiration',
    ];
    foreach ($core_keys as $ck) {
        $val = get_post_meta($niche_post->ID, $ck, true);
        if (!empty($val)) $fallbacks[$ck] = $val;
    }
}
if (!empty($niche_raw_meta['niche_faq_1']) && !empty($niche_raw_meta['niche_faq_1_answer'])) {
    $fallbacks['faq4_q'] = $niche_raw_meta['niche_faq_1']; $fallbacks['faq4_a'] = $niche_raw_meta['niche_faq_1_answer'];
}

// --- LSI ENGINE INJECTION ---
mt_srand($master_seed + 500);
$lsi_pool = [
    "{$niche_name} web developers in Nigeria",
    "top {$niche_name} digital agencies nationwide",
    "custom website design for {$niche_plural} in Nigeria",
    "Nigerian {$niche_name} web design experts",
    $is_soft_niche ? "trusted web designers for {$niche_plural}" : "best website creators for {$niche_plural}",
    "digital marketing for {$niche_name}s in Nigeria",
    "professional {$niche_name} web development"
];
$lsi_keyword = $lsi_pool[mt_rand(0, count($lsi_pool) - 1)];

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
];

foreach ($fallbacks as $k => $v) {
    $fallbacks[$k] = str_replace(array_keys($replacements), array_values($replacements), $v);
}

$meta = [];
foreach ($fallbacks as $k => $v) { $meta[$k] = go_spin_text($v); }

// ─── DYNAMIC PORTFOLIO, deterministic order per niche ────────────────────────
$all_projects = [
    ['name' => 'RAFFLEKINGS', 'url' => '/work/rafflekings', 'img' => 'https://getonlinestudio.com/insights/wp-content/uploads/2026/03/69c056ef61d91271e5147a38.jpg', 'tag' => 'Fintech', 'desc' => 'A custom, lightning-fast platform that scales effortlessly under load.'],
    ['name' => 'VISIONAFRIC', 'url' => '/work/visionafric', 'img' => 'https://getonlinestudio.com/insights/wp-content/uploads/2026/03/69bfd90c35c39c6a8b6658d9.jpg', 'tag' => 'NGO', 'desc' => 'A powerful storytelling platform that commands global trust.'],
    ['name' => 'DEKOMPANY', 'url' => '/work/dekompany', 'img' => 'https://getonlinestudio.com/insights/wp-content/uploads/2026/03/69bfda6324e30f2c3662f4cc.jpg', 'tag' => 'Agency', 'desc' => 'A premium consulting platform engineered for high-conversion funnels.'],
];
mt_srand($master_seed + 700);
shuffle($all_projects);
$display_projects = array_slice($all_projects, 0, 2);

// --- CONTEXTUAL SPINTAX ROUTING ---
if ($is_non_profit) {
    $reality_headline = go_spin_text("{Built for {$niche_plural} in Nigeria.|Empowering Nigerian {$niche_plural} online.|Expand your reach and impact nationwide.}");
} elseif ($is_education) {
    $reality_headline = go_spin_text("{Built for {$niche_plural} in Nigeria.|Empowering Nigerian {$niche_plural} Online.|Academic Excellence Starts with a Great Website.}");
} else {
    $reality_headline = go_spin_text("{Built for {$niche_plural} in Nigeria.|Dominating the Nigerian {$niche_name} market.|Win the market nationwide.}");
}

// ─── NATIONAL PRICING ENGINE ─────────────────────────────────────────────────
// Averages pricing across ALL cities for this niche from city-niche.json
$niche_multipliers = [
    'fintech' => 2.5, 'crypto' => 2.5, 'polytechnic' => 2.5, 'university' => 2.5,
    'microfinance' => 2.0, 'software' => 2.0, 'oil-gas' => 2.0, 'stock-brokerage' => 2.0,
    'tech-startup' => 1.8, 'ecommerce' => 1.8, 'hospital' => 1.8, 'commodity-trading' => 1.8,
    'loan-company' => 1.8, 'mortgage-company' => 1.8, 'bureau-de-change' => 1.6, 'pos-business' => 1.5,
    'real-estate' => 1.5, 'insurance' => 1.5, 'construction' => 1.5, 'law-firm' => 1.4,
    'architecture-firm' => 1.4, 'logistics' => 1.4, 'haulage-company' => 1.4, 'night-club' => 1.4,
    'hotel' => 1.4, 'accounting' => 1.3, 'travel-agency' => 1.3, 'ngo' => 1.3,
    'solar' => 1.3, 'security-company' => 1.3, 'detective-agency' => 1.3, 'advertising-agency' => 1.3,
    'cooperative-society' => 1.3, 'bar-and-lounge' => 1.3, 'sports-academy' => 1.3,
    'agriculture-company' => 1.2, 'courier-service' => 1.2, 'recruitment' => 1.2,
    'pharmacy-distributor' => 1.3, 'car-rental' => 1.3, 'printing-company' => 1.3,
    'borehole-drilling' => 1.2, 'furniture-company' => 1.2, 'electronics-store' => 1.2,
    'school' => 1.2, 'driving-school' => 1.1, 'music-school' => 1.1,
];
$niche_complexity_multiplier = isset($niche_multipliers[$niche_slug]) ? $niche_multipliers[$niche_slug] : 1.0;

// ── NICHE-SPECIFIC PRICING FEATURE LISTS ────────────────────────────────────
// Each group has three tiers: entry, standard, premium.
// Every item is a plain string — no HTML here, rendered in the loop below.
// Groups are matched in order; the first match wins. Falls back to 'default'.
$niche_pricing_groups = [

    // ── FINTECH / FINANCE ────────────────────────────────────────────────────
    'fintech' => [
        'slugs'  => ['fintech', 'microfinance', 'stock-brokerage', 'loan-company', 'mortgage-company',
                     'bureau-de-change', 'pos-business', 'crypto', 'insurance', 'cooperative-society'],
        'label'  => 'Financial Services',
        'entry'  => [
            'Up to 7 pages — Home, About, Services, Rates, FAQs, Contact, and one product or loan page',
            'Regulatory compliance page: CBN licence display, registration numbers, terms of service',
            'Secure contact and loan enquiry form with WhatsApp confirmation on every submission',
            'Trust signals: testimonials section, partner logos, and "Licensed by CBN" badge',
            'SSL certificate with bank-grade padlock — builds confidence before a visitor even reads a word',
            'Google Business Profile setup so local customers find you on Maps',
            '30-day post-launch support for edits and questions',
        ],
        'standard' => [
            'Everything in Entry Level, fully included',
            'EMI / loan repayment calculator built into the site — visitors compute their own repayments',
            'Rates and product comparison table — clearly shows your offer vs competitors',
            'Advanced SEO targeting "loan company in Lagos", "microfinance bank Nigeria", and similar searches',
            'Investor relations or partner page for institutions reviewing your credibility',
            'Secure document upload form so applicants can submit IDs and statements online',
            'Professional financial copywriting that builds trust and answers objections before they arise',
            '1-year support, security updates, weekly backups, and Google Search Console monitoring',
        ],
        'premium' => [
            'Everything in Standard Build, fully included',
            'Online loan or account application system — full end-to-end form with status tracking',
            'Paystack or Flutterwave integration for repayments, fees, or subscription collections',
            'Customer portal: login, view balance, download statements, submit support tickets',
            'Automated KYC document request and verification workflow via email and WhatsApp',
            'Multi-branch SEO: rank in every city or state your branches operate in',
            'Custom admin dashboard for your team to manage leads, approvals, and customer records',
            'Dedicated account manager, monthly analytics review, and priority 24-hour support',
        ],
    ],

    // ── OIL, GAS & ENERGY ───────────────────────────────────────────────────
    'oil-gas' => [
        'slugs'  => ['oil-gas', 'commodity-trading', 'solar', 'borehole-drilling'],
        'label'  => 'Energy & Resources',
        'entry'  => [
            'Up to 7 pages — Home, About, Services, Projects, Certifications, Contact, and a Careers page',
            'Project showcase gallery: photos, descriptions, and scale of completed contracts',
            'Certifications and HSE compliance page: display DPR, NUPRC, ISO, and safety credentials',
            'Procurement or RFQ enquiry form so corporate clients can request quotes directly',
            'Professional SSL, custom domain, and fast mobile loading for field staff and remote access',
            'Google Business Profile setup and industry directory submissions',
            '30-day post-launch support for edits and questions',
        ],
        'standard' => [
            'Everything in Entry Level, fully included',
            'Advanced SEO targeting "oil and gas company Nigeria", "solar installation Lagos", and contract-related searches',
            'Interactive services breakdown: explain each service with scope, capacity, and minimum engagement',
            'Tender and pre-qualification document page with downloadable company profile and credentials',
            'Client and partnership logos section to signal existing corporate relationships',
            'Professional technical copywriting that speaks the language of procurement officers and engineers',
            'News and project updates section to keep the site fresh and rank for ongoing keywords',
            '1-year support, security patches, weekly backups, and Search Console monitoring',
        ],
        'premium' => [
            'Everything in Standard Build, fully included',
            'Secure client portal: upload project reports, share progress updates, and manage invoices',
            'RFQ and tender management system so your team can track and respond to enquiries internally',
            'Multi-location SEO to rank across all states, terminals, and offshore zones you operate in',
            'Custom admin dashboard for document management and client communication',
            'Paystack or bank transfer integration for deposit or retainer collections',
            'LinkedIn and corporate profile integration for executive visibility',
            'Dedicated account manager, quarterly strategy review, and priority 24-hour SLA support',
        ],
    ],

    // ── EDUCATION ────────────────────────────────────────────────────────────
    'education' => [
        'slugs'  => ['university', 'polytechnic', 'school', 'music-school', 'driving-school', 'sports-academy'],
        'label'  => 'Educational Institution',
        'entry'  => [
            'Up to 7 pages — Home, About, Programmes, Admissions, Fees, Contact, and a Gallery page',
            'Admissions enquiry form with automatic email and WhatsApp reply to every parent or student',
            'Programmes and courses listing with descriptions, duration, and entry requirements',
            'Fees and payment schedule page — clear, honest, and easy to understand',
            'Gallery section for campus, classrooms, events, and student life photos',
            'Google Business Profile setup so parents find you on Google Maps when searching nearby',
            '30-day post-launch support for edits and updates',
        ],
        'standard' => [
            'Everything in Entry Level, fully included',
            'Advanced SEO targeting "best school in [city]", "university admission Nigeria", and parent search queries',
            'Online application form: students fill in details, upload documents, and receive a reference number',
            'Staff and faculty directory page — builds academic credibility and human trust',
            'Events and academic calendar section: open days, exams, and school activities',
            'Professional admissions copywriting that speaks to both parents and prospective students',
            'Testimonials and alumni success stories section to reinforce reputation',
            '1-year support, security updates, weekly backups, and Google Search Console monitoring',
        ],
        'premium' => [
            'Everything in Standard Build, fully included',
            'Full student portal: login, check results, download transcripts, and view timetables',
            'Online school fees payment via Paystack or Remita — tracked per student automatically',
            'E-learning module: upload lecture notes, videos, and assignments for enrolled students',
            'Automated admissions pipeline: application received, reviewed, shortlisted, admitted — all tracked',
            'Multi-campus SEO if you have branches in multiple cities or states',
            'Staff admin dashboard for managing content, student records, and announcements',
            'Dedicated account manager, termly performance review, and priority 24-hour support',
        ],
    ],

    // ── HOSPITALITY ──────────────────────────────────────────────────────────
    'hospitality' => [
        'slugs'  => ['hotel', 'bar-and-lounge', 'night-club', 'restaurant', 'event-centre'],
        'label'  => 'Hospitality & Events',
        'entry'  => [
            'Up to 7 pages — Home, Rooms or Menu, About, Events, Gallery, Rates, and Contact',
            'Photo gallery optimised for fast loading — rooms, food, events, and ambience',
            'WhatsApp reservation button and contact form with instant acknowledgement message',
            'Rates and packages page: clearly displayed per night, per table, or per event',
            'Google Business Profile setup and Google Maps integration with opening hours',
            'SSL certificate, custom domain, and mobile-first design for guests browsing on their phones',
            '30-day post-launch support for updates and edits',
        ],
        'standard' => [
            'Everything in Entry Level, fully included',
            'Advanced SEO targeting "hotel in Lagos", "event centre Abuja", "best bar Victoria Island", and similar searches',
            'Online reservation or booking request form with automatic WhatsApp and email confirmation',
            'TripAdvisor, Google Review, and social media feed integration on the site',
            'Events and private hire page with capacity, pricing, and enquiry form per event type',
            'Professional hospitality copywriting that sells the experience, not just the room',
            'Special offers and promotions section: update it yourself without touching code',
            '1-year support, security updates, weekly backups, and Search Console monitoring',
        ],
        'premium' => [
            'Everything in Standard Build, fully included',
            'Full online booking system with room availability calendar and instant confirmation',
            'Paystack integration for deposits, advance payments, and event bookings',
            'Loyalty or membership page with registration and exclusive member offers',
            'POS-linked menu management: update your menu from your phone and the site changes automatically',
            'Multi-location SEO if you operate more than one branch or venue',
            'Staff admin panel to manage reservations, enquiries, and offers without a developer',
            'Dedicated account manager, monthly performance review, and priority 24-hour support',
        ],
    ],

    // ── LEGAL ────────────────────────────────────────────────────────────────
    'legal' => [
        'slugs'  => ['law-firm', 'detective-agency'],
        'label'  => 'Legal & Investigative Services',
        'entry'  => [
            'Up to 7 pages — Home, Practice Areas, Our Lawyers, About the Firm, Case Results, Blog, Contact',
            'Practice areas page: clearly explains each area of law in plain language clients can understand',
            'Attorney profiles with photo, credentials, year called to bar, and specialisation',
            'Confidential consultation request form — no information shared without client consent',
            'SSL certificate and privacy-first setup — clients trust a secure, professional site',
            'Google Business Profile setup so clients find you when searching for lawyers near them',
            '30-day post-launch support for updates and edits',
        ],
        'standard' => [
            'Everything in Entry Level, fully included',
            'Advanced SEO targeting "law firm in Lagos", "corporate lawyer Nigeria", "divorce lawyer Abuja", and similar searches',
            'Case results or settlements page — shows track record without naming clients',
            'Legal blog setup: publish articles on common legal issues to attract organic search traffic',
            'FAQ section answering the questions clients ask most before calling',
            'Professional legal copywriting that is authoritative, clear, and builds confidence',
            'WhatsApp consultation booking with automated confirmation and a reminder the day before',
            '1-year support, security updates, weekly backups, and Search Console monitoring',
        ],
        'premium' => [
            'Everything in Standard Build, fully included',
            'Secure client document portal: share case files, sign agreements, and track matter progress online',
            'Online retainer payment via Paystack — clients pay deposits and fees without a bank visit',
            'Automated consultation scheduling: clients pick a slot, the system confirms and reminds both sides',
            'Multi-practice SEO: rank separately for family law, corporate law, criminal law, and more',
            'Staff admin dashboard for managing client enquiries, documents, and appointments',
            'Practice area microsites or landing pages for high-value keyword targeting',
            'Dedicated account manager, quarterly strategy review, and priority 24-hour SLA support',
        ],
    ],

    // ── HEALTHCARE ───────────────────────────────────────────────────────────
    'healthcare' => [
        'slugs'  => ['hospital', 'pharmacy-distributor', 'medical'],
        'label'  => 'Healthcare & Medical',
        'entry'  => [
            'Up to 7 pages — Home, Services, Doctors, About, Patient Info, Location, and Contact',
            'Doctor and specialist profiles with photo, qualifications, and areas of focus',
            'Services page: clearly lists each department, treatment, or product with descriptions',
            'Patient appointment enquiry form with WhatsApp and email confirmation',
            'NAFDAC or medical licence display — patients check credentials before they book',
            'Google Business Profile and Maps integration with clinic hours and emergency contact',
            '30-day post-launch support for edits and updates',
        ],
        'standard' => [
            'Everything in Entry Level, fully included',
            'Advanced SEO targeting "hospital in [city]", "best doctor near me Nigeria", "pharmacy Lagos", and similar searches',
            'Online appointment booking form by department, specialist, and preferred date',
            'Health blog: publish useful articles to attract patients searching for symptoms and treatments',
            'Patient testimonials section — social proof that reduces hesitation before booking',
            'Professional medical copywriting that is clear, reassuring, and compliant',
            'Emergency contact strip pinned at the top of every page for urgent situations',
            '1-year support, security updates, weekly backups, and Google Search Console monitoring',
        ],
        'premium' => [
            'Everything in Standard Build, fully included',
            'Full patient portal: book appointments, view results, and message the clinic securely online',
            'Paystack integration for consultation deposits, lab fees, and pharmacy payments',
            'Automated appointment reminders via WhatsApp and email — reduces no-shows significantly',
            'Telemedicine page with video consultation booking and secure intake forms',
            'Multi-location SEO if you operate clinics across multiple cities or states',
            'Staff admin dashboard: manage appointments, patient records, and announcements',
            'Dedicated account manager, monthly analytics review, and priority 24-hour support',
        ],
    ],

    // ── NON-PROFIT / NGO ─────────────────────────────────────────────────────
    'ngo' => [
        'slugs'  => ['ngo'],
        'label'  => 'NGO & Non-Profit',
        'entry'  => [
            'Up to 7 pages — Home, About, Our Work, Programmes, Donate, Media, and Contact',
            'Online donation page with fixed amounts and a custom-amount option clearly displayed',
            'Programmes and projects page: what you do, who you serve, and the impact you create',
            'Volunteer and partnership sign-up form with automated welcome email on submission',
            'Trust signals: registration number, board members, and donor partner logos',
            'Google Business Profile setup so community members and donors find you easily online',
            '30-day post-launch support for updates and edits',
        ],
        'standard' => [
            'Everything in Entry Level, fully included',
            'Advanced SEO targeting "NGO in Nigeria", "non-profit Lagos", "humanitarian organisation Abuja", and similar searches',
            'Online donation via Paystack — general fund, specific projects, and recurring giving options',
            'Impact reports and annual review page: numbers, stories, and photo evidence of your work',
            'Media gallery: photos, videos, and press coverage from field work and events',
            'Professional non-profit copywriting that communicates mission clearly and drives donor action',
            'Newsletter sign-up to keep donors, volunteers, and partners informed and engaged',
            '1-year support, security updates, weekly backups, and Search Console monitoring',
        ],
        'premium' => [
            'Everything in Standard Build, fully included',
            'Donor portal: track giving history, download receipts, and update personal details',
            'Full donation management system: project-tagged contributions, donor records, and annual statements',
            'Grant and funding page: downloadable proposal documents and trustee information for institutional donors',
            'Multi-state SEO if you operate programmes across several states or regions',
            'Automated donor acknowledgement emails and end-of-year giving summaries',
            'Staff admin dashboard for managing content, programmes, volunteer records, and donation reports',
            'Dedicated account manager, quarterly strategy review, and priority 24-hour support',
        ],
    ],

    // ── CHURCH / FAITH ORGANISATION ──────────────────────────────────────────
    'church' => [
        'slugs'  => ['church'],
        'label'  => 'Church & Faith Organisation',
        'entry'  => [
            'Up to 7 pages — Home, About, Ministries, Sermon Archive, Events, Give, and Contact',
            'Online giving page: tithes, offerings, and special project seeds with clear amounts',
            'Events calendar: Sunday services, programmes, conferences, and outreach activities',
            'Sermon archive: embed YouTube, audio, or podcast recordings neatly by series or date',
            'WhatsApp contact and prayer request form with automatic acknowledgement reply',
            'Google Business Profile setup so members and visitors find your church on Maps',
            '30-day post-launch support for updates and edits',
        ],
        'standard' => [
            'Everything in Entry Level, fully included',
            'Advanced SEO targeting "church in [city]", "RCCG branch Lagos", "best church Abuja", and similar searches',
            'Online tithe and offering via Paystack — members give from their phone before they even arrive',
            'Ministry pages: each unit (men, women, youth, children) gets its own section with contact details',
            'Monthly newsletter sign-up to keep the congregation connected and informed',
            'Professional faith-community copywriting that is warm, welcoming, and spiritually grounded',
            'Membership or visitor follow-up form so new people feel received and contacted promptly',
            '1-year support, security updates, weekly backups, and Search Console monitoring',
        ],
        'premium' => [
            'Everything in Standard Build, fully included',
            'Member portal: register, give online, access sermon archive, view service calendar, and join units',
            'Full giving management system: track tithes, issue statements, and generate reports per member',
            'Multi-branch SEO if your ministry has locations across several cities or states',
            'Live-streaming integration linked to YouTube or Facebook for Sunday and midweek services',
            'Automated giving acknowledgement messages and annual stewardship statements',
            'Admin dashboard for pastors and admin staff to manage content, events, and member records',
            'Dedicated account manager, quarterly review, and priority 24-hour support',
        ],
    ],

    // ── INVESTMENT & ASSET MANAGEMENT ────────────────────────────────────────
    'investment' => [
        'slugs'  => ['investment-company', 'asset-management', 'stock-brokerage', 'commodity-trading',
                     'forex-trading', 'pension-fund', 'bureau-de-change'],
        'label'  => 'Investment & Asset Management',
        'entry'  => [
            'Up to 7 pages — Home, About, Investment Products, Performance, FAQs, Contact, and a Legal page',
            'Investment products or funds page: clearly explains each offering, risk level, and minimum investment',
            'Regulatory credibility page: SEC registration, CBN licence, and compliance disclosures',
            'Secure enquiry form so potential investors can request a consultation without commitment',
            'Trust signals: track record, years in operation, AUM figures, and any certifications',
            'SSL certificate and professional custom domain — investor confidence starts before they read a word',
            '30-day post-launch support for edits and updates',
        ],
        'standard' => [
            'Everything in Entry Level, fully included',
            'Advanced SEO targeting "investment company Nigeria", "asset management Lagos", "best stock broker Nigeria", and similar searches',
            'Portfolio performance section: historical returns, benchmarks, and fund fact sheets',
            'Investor resources page: downloadable prospectus, product guides, and risk disclosures',
            'FAQ section addressing the most common investor concerns before they pick up the phone',
            'Professional financial copywriting that projects authority, compliance, and trustworthiness',
            'Newsletter or market update sign-up to keep existing and prospective investors engaged',
            '1-year support, security updates, weekly backups, and Google Search Console monitoring',
        ],
        'premium' => [
            'Everything in Standard Build, fully included',
            'Investor portal: login, view portfolio value, download statements, and submit redemption requests',
            'Online onboarding form: KYC document upload, risk profiling questionnaire, and account activation',
            'Paystack or bank transfer integration for initial investment and top-up payments',
            'Automated monthly statement delivery and transaction confirmation via email and WhatsApp',
            'Multi-product SEO: rank separately for each fund, asset class, or investment type you offer',
            'Compliance admin dashboard: manage client records, document expiry, and regulatory filings',
            'Dedicated account manager, quarterly performance review, and priority 24-hour SLA support',
        ],
    ],

    // ── PROFESSIONAL SERVICES ─────────────────────────────────────────────────
    'professional' => [
        'slugs'  => ['accounting', 'consulting', 'recruitment', 'advertising-agency',
                     'event-planning', 'wedding-planner', 'detective-agency', 'security-company'],
        'label'  => 'Professional Services',
        'entry'  => [
            'Up to 7 pages — Home, Services, About, Our Work or Case Studies, Team, Contact, and a Careers page',
            'Services page: each service explained clearly with scope, deliverable, and who it is for',
            'Team page with individual profiles, credentials, and areas of expertise',
            'Client logos or portfolio section to demonstrate existing relationships and results',
            'Secure contact and project enquiry form with WhatsApp follow-up on every submission',
            'Google Business Profile setup so potential clients find you when searching for your service',
            '30-day post-launch support for updates and edits',
        ],
        'standard' => [
            'Everything in Entry Level, fully included',
            'Advanced SEO targeting your exact service plus city — e.g. "accounting firm Lagos", "recruitment agency Nigeria", "security company Abuja"',
            'Case studies or results page: specific outcomes, industries served, and problems solved',
            'Blog or insights section: publish useful articles to attract organic search traffic from ideal clients',
            'Service landing pages: a dedicated page per service improves SEO and conversion significantly',
            'Professional business copywriting that positions your firm as the expert, not just a vendor',
            'Client testimonials and social proof section to reduce hesitation in new prospects',
            '1-year support, security updates, weekly backups, and Search Console monitoring',
        ],
        'premium' => [
            'Everything in Standard Build, fully included',
            'Client portal: submit briefs, review proposals, approve work, and download deliverables online',
            'Automated project intake: new enquiries go through a structured form that pre-qualifies scope',
            'Paystack or bank transfer integration for retainer invoicing and project deposit collection',
            'Multi-service SEO: rank for each individual service across multiple cities simultaneously',
            'Staff admin dashboard to manage enquiries, proposals, client records, and live projects',
            'LinkedIn and professional profile integration for key team members and thought leadership',
            'Dedicated account manager, quarterly strategy review, and priority 24-hour SLA support',
        ],
    ],

    // ── TRAVEL & HOSPITALITY SERVICES ────────────────────────────────────────
    'travel' => [
        'slugs'  => ['travel-agency', 'car-rental', 'logistics', 'courier-service', 'haulage-company'],
        'label'  => 'Travel & Logistics',
        'entry'  => [
            'Up to 7 pages — Home, Services or Destinations, About, Rates, FAQs, Contact, and a Booking page',
            'Services or packages page: clearly lists what is included, pricing, and how to book',
            'WhatsApp booking button so customers reach you directly without friction',
            'Trust signals: years in operation, number of trips handled, and client review quotes',
            'Google Business Profile setup and Maps integration with your office or pickup location',
            'SSL certificate, custom domain, and fast mobile loading for customers browsing on the go',
            '30-day post-launch support for updates and edits',
        ],
        'standard' => [
            'Everything in Entry Level, fully included',
            'Advanced SEO targeting "travel agency Nigeria", "car hire Lagos", "courier service Abuja", and similar destination or service searches',
            'Online booking or quote request form with automatic email and WhatsApp confirmation',
            'Destinations or routes page: where you go, how long it takes, and what is included',
            'Tracking or order status page for logistics clients to check their shipment progress',
            'Professional travel and logistics copywriting that builds trust and handles objections early',
            'Customer reviews integration so new visitors see real social proof before they book',
            '1-year support, security updates, weekly backups, and Search Console monitoring',
        ],
        'premium' => [
            'Everything in Standard Build, fully included',
            'Full online booking system with date selection, passenger count, and instant price calculation',
            'Paystack integration for deposits, full payments, and instalment collection',
            'Fleet or inventory management page for car rental and logistics companies',
            'Client portal: track bookings, view invoices, and download trip or shipment history',
            'Multi-city SEO to rank across all routes, destinations, or cities you operate in',
            'Staff admin dashboard to manage bookings, payments, and customer records without a developer',
            'Dedicated account manager, quarterly performance review, and priority 24-hour support',
        ],
    ],

    // ── AGRICULTURE ───────────────────────────────────────────────────────────
    'agriculture' => [
        'slugs'  => ['agriculture-company', 'fish-farming', 'poultry-farming', 'cassava-farming',
                     'rice-milling', 'palm-oil-business', 'cocoa-export'],
        'label'  => 'Agriculture & Agribusiness',
        'entry'  => [
            'Up to 7 pages — Home, Products, Farm or About, Certifications, Pricing, Contact, and a Gallery',
            'Products or produce page: what you grow or process, how it is packaged, and minimum order',
            'Farm or factory gallery: photos that build confidence for buyers and export clients',
            'Certifications and standards page: NAFDAC, SON, phytosanitary, and export documentation',
            'WhatsApp bulk order and enquiry form with automatic acknowledgement on submission',
            'Google Business Profile setup for local buyers and Google Maps visibility',
            '30-day post-launch support for updates and edits',
        ],
        'standard' => [
            'Everything in Entry Level, fully included',
            'Advanced SEO targeting "poultry farm Nigeria", "fish farm Lagos", "palm oil supplier Nigeria", and buyer-intent searches',
            'Wholesale price list or downloadable product catalogue for bulk buyers and distributors',
            'Traceability or farm-to-table story section: builds trust with export buyers and premium clients',
            'Blog or seasonal updates section: planting season, harvest news, and supply availability',
            'Professional agribusiness copywriting that speaks to both local buyers and export partners',
            'Export readiness page: shipping capacity, NAFDAC number, and contact for freight enquiries',
            '1-year support, security updates, weekly backups, and Search Console monitoring',
        ],
        'premium' => [
            'Everything in Standard Build, fully included',
            'B2B buyer portal: register as a distributor, view live stock, and place bulk orders online',
            'Paystack or bank transfer integration for order deposits and payment confirmations',
            'Inventory or harvest tracker: update available stock from your phone, buyers see it live',
            'Export documentation page with downloadable compliance certificates and shipping guides',
            'Multi-state SEO to rank across every market, city, and region you supply to',
            'Staff admin dashboard for managing orders, buyer records, and product listings',
            'Dedicated account manager, quarterly review, and priority 24-hour SLA support',
        ],
    ],

    // ── E-COMMERCE & RETAIL ──────────────────────────────────────────────────
    'ecommerce' => [
        'slugs'  => ['ecommerce', 'electronics-store', 'furniture-company', 'skincare-brand', 'fashion'],
        'label'  => 'E-Commerce & Retail',
        'entry'  => [
            'Up to 7 pages — Home, Shop, About, Contact, Returns Policy, FAQ, and a featured product page',
            'Product listing pages with photos, descriptions, pricing, and a WhatsApp order button',
            'WhatsApp-to-order flow: customer taps a button and lands in chat with product already named',
            'SSL certificate and trust badges — buyers only pay where they feel safe',
            'Google Business Profile setup for local foot traffic and online discovery',
            'Mobile-first design — most Nigerian shoppers browse and buy on a phone',
            '30-day post-launch support for product updates and edits',
        ],
        'standard' => [
            'Everything in Entry Level, fully included',
            'Full WooCommerce shop: add unlimited products, categories, and stock levels yourself',
            'Paystack checkout integration — customers pay by card, bank transfer, or USSD without leaving the site',
            'Advanced SEO targeting "buy [product] online Nigeria", "[product] Lagos delivery", and category searches',
            'Customer review and star rating system on every product page',
            'Abandoned cart recovery: email reminder to customers who left without buying',
            'Professional e-commerce copywriting: product descriptions that sell, not just describe',
            '1-year support, security updates, weekly backups, and Google Search Console monitoring',
        ],
        'premium' => [
            'Everything in Standard Build, fully included',
            'Custom storefront design: unique brand experience, not a default WooCommerce theme',
            'Bulk order and wholesale pricing system for B2B customers',
            'Inventory management dashboard: update stock, pricing, and variants from one screen',
            'Automated order confirmation, shipping update, and delivery notification via WhatsApp and email',
            'Multi-city SEO to rank for delivery searches across Lagos, Abuja, Port Harcourt, and beyond',
            'Loyalty points or discount voucher system to reward repeat customers',
            'Dedicated account manager, monthly sales analytics review, and priority 24-hour support',
        ],
    ],

    // ── REAL ESTATE & CONSTRUCTION ────────────────────────────────────────────
    'realestate' => [
        'slugs'  => ['real-estate', 'construction', 'architecture-firm', 'haulage-company', 'logistics'],
        'label'  => 'Real Estate & Construction',
        'entry'  => [
            'Up to 7 pages — Home, Properties or Projects, About, Services, Contact, Gallery, and a Careers page',
            'Property or project listings with photos, location, price, and a call-to-action button',
            'WhatsApp inspection request button on every listing or project card',
            'About page with company history, registration, and team — builds buyer confidence',
            'SSL certificate and fast mobile loading for site visitors browsing on-the-go',
            'Google Business Profile and Maps setup for physical office or showroom',
            '30-day post-launch support for listing updates and edits',
        ],
        'standard' => [
            'Everything in Entry Level, fully included',
            'Advanced SEO targeting "houses for sale in [city]", "construction company Lagos", "architect Nigeria", and similar searches',
            'Filterable property or project listings: by type, location, price, and bedroom count',
            'Virtual tour or video walk-through embed on property pages',
            'Mortgage or payment plan calculator built into each listing page',
            'Professional real estate copywriting that sells the lifestyle, not just the structure',
            'Newsletter or property alert sign-up so interested buyers get notified of new listings',
            '1-year support, security updates, weekly backups, and Search Console monitoring',
        ],
        'premium' => [
            'Everything in Standard Build, fully included',
            'Full property management portal: landlords add listings, tenants submit enquiries, admin approves',
            'Paystack integration for holding deposits, inspection fees, or instalment payments',
            'Agent or team dashboard: each agent manages their own listings and leads independently',
            'Multi-city SEO to rank across all states and cities where you list or build',
            'Automated lead follow-up: email and WhatsApp drip sequence for every new enquiry',
            'Custom admin dashboard for property inventory, client records, and contract tracking',
            'Dedicated account manager, quarterly growth review, and priority 24-hour SLA support',
        ],
    ],

    // ── DEFAULT / GENERAL BUSINESS ────────────────────────────────────────────
    'default' => [
        'slugs'  => [],
        'label'  => 'Business',
        'entry'  => [
            'Up to 7 pages — fully mobile-optimized and fast-loading on 3G and 4G',
            'WhatsApp chat button and contact form, pre-wired and tested',
            'On-page SEO on every page: titles, meta descriptions, heading structure, and image alt text',
            'Google Business Profile setup and verification support',
            'SSL certificate, security hardening, and a professional custom domain',
            'Social media links and Open Graph setup so your links look great when shared',
            '30-day post-launch support for fixes, edits, and questions',
        ],
        'standard' => [
            'Everything in Entry Level, fully included',
            'Advanced keyword SEO: competitor gap analysis, long-tail targeting, and Google Search Console setup',
            'Booking or enquiry system with automated email and WhatsApp confirmation to every lead',
            'Professional copywriting for all pages, written to convert browsers into paying clients',
            'Speed optimisation: image compression, lazy loading, and caching configured for Nigerian networks',
            'Schema markup (LocalBusiness, FAQPage, Service) for Google rich results',
            '1-year support and maintenance: security updates, weekly backups, and priority response',
            'Analytics dashboard setup so you can see exactly who visits, from where, and what they do',
        ],
        'premium' => [
            'Everything in Standard Build, fully included',
            'Custom web application or client portal built from scratch to your exact workflow',
            'Paystack and Flutterwave payment gateway integration with transaction management',
            'Business automation: lead capture, follow-up sequences, invoice generation, and CRM sync',
            'Multi-location SEO: rank in every city your business operates or serves',
            'Staff or admin dashboard to manage content, leads, and orders without touching code',
            'Dedicated account manager with monthly performance reviews and growth strategy calls',
            'Priority 24-hour response on all support requests, guaranteed SLA',
        ],
    ],
];

// Match current niche to a pricing group
$active_pricing_group = $niche_pricing_groups['default'];
foreach ($niche_pricing_groups as $group_key => $group) {
    if (in_array($niche_slug, $group['slugs'])) {
        $active_pricing_group = $group;
        break;
    }
}
$pricing_entry    = $active_pricing_group['entry'];
$pricing_standard = $active_pricing_group['standard'];
$pricing_premium  = $active_pricing_group['premium'];
// ── END NICHE PRICING FEATURES ───────────────────────────────────────────────

$json_path = __DIR__ . '/city-niche.json';
$city_niche_json = file_exists($json_path) ? json_decode(file_get_contents($json_path), true) : [];

// Collect pricing from all cities for this niche
$price_samples_low = []; $price_samples_high = [];
foreach ($city_niche_json as $combo_key => $data) {
    if (strpos($combo_key, '_' . $niche_slug) !== false && !empty($data['avg_cost_low']) && !empty($data['avg_cost_high'])) {
        $price_samples_low[]  = $data['avg_cost_low'];
        $price_samples_high[] = $data['avg_cost_high'];
    }
}

// Compute national average, apply complexity multiplier
if (!empty($price_samples_low)) {
    $raw_low  = array_sum($price_samples_low)  / count($price_samples_low);
    $raw_high = array_sum($price_samples_high) / count($price_samples_high);
    $cost_low     = round(($raw_low  * $niche_complexity_multiplier) / 10000) * 10000;
    $cost_high    = round(($raw_high * $niche_complexity_multiplier) / 10000) * 10000;
    $cost_typical = round((($cost_low + $cost_high) / 2.2) / 10000) * 10000;
    $city_count   = count($price_samples_low);
} else {
    // Fallback defaults with multiplier applied
    $cost_low     = round((120000 * $niche_complexity_multiplier) / 10000) * 10000;
    $cost_high    = round((450000 * $niche_complexity_multiplier) / 10000) * 10000;
    $cost_typical = round((250000 * $niche_complexity_multiplier) / 10000) * 10000;
    $city_count   = 41;
}

// Pricing stats for the rich SEO section
$fmt_cost_low     = '₦' . number_format($cost_low);
$fmt_cost_typical = '₦' . number_format($cost_typical);
$fmt_cost_high    = '₦' . number_format($cost_high);

// Percentage breakdowns (fixed realistic splits)
$pct_budget    = 28; // % of clients taking basic site only
$pct_standard  = 47; // % taking standard + SEO
$pct_premium   = 25; // % taking full premium build
$avg_roi_month = 3;  // months to measurable ROI

// 5. SEO Meta Data, deterministic title rotation, no random spintax in head
define('GO_SITE_URL', 'https://getonlinestudio.com');

$title_pool = [
    "{$niche_name} {$service_name} in Nigeria | GetOnline Studio",
    "Best {$niche_name} {$service_name} in Nigeria | GetOnline Studio",
    "{$niche_name} Web Design Company in Nigeria | GetOnline Studio",
    "Top {$niche_name} Website Designer in Nigeria | GetOnline Studio",
    "{$niche_name} Web Design & Development Agency in Nigeria | GetOnline Studio",
];
$title_seed  = abs(crc32($niche_slug . $service_slug . 'title'));
$meta_title  = $title_pool[$title_seed % count($title_pool)];

// Meta desc, fixed grammar, no spintax (spintax in meta desc causes "Advertising Agencys" bugs)
$meta_desc = "GetOnline Studio is a web design company specializing in {$niche_plural} across Nigeria. We build professional websites and web apps for {$niche_plural}. We are not a freelancer. We are a full team of web designers and developers.";

$og_image_slug    = sanitize_title("Top {$niche_name} {$service_name} Nigeria");
$dynamic_og_image = GO_SITE_URL . "/social/{$og_image_slug}.jpg";
$canonical_url    = GO_SITE_URL . "/services/{$raw_slug}/";

// Keyword variant grid, shown in Section 1.7
$kw_variants_national = [
    ['term' => "{$niche_name} Web Designer in Nigeria",          'icon' => 'pen-tool',   'desc' => "Custom websites built specifically for the {$niche_name} industry across Nigeria."],
    ['term' => "{$niche_name} Web Developer in Nigeria",         'icon' => 'code-2',     'desc' => "Full-stack web development: portals, booking systems, and custom web apps for {$niche_plural}."],
    ['term' => "{$niche_name} Web Design Services",              'icon' => 'layers',     'desc' => "End-to-end design services tailored to the specific needs of {$niche_plural} in Nigeria."],
    ['term' => "{$niche_name} Web Design Agency in Nigeria",     'icon' => 'building-2', 'desc' => "A full-service agency combining strategy, design, SEO, and ongoing support for {$niche_plural}."],
    ['term' => "{$niche_name} Web Design Company in Nigeria",    'icon' => 'briefcase',  'desc' => "A registered Nigerian company, not a freelancer, delivering enterprise-grade websites for {$niche_plural}."],
];

// --- NATIONAL LANDSCAPE SPINTAX ---
mt_srand($master_seed + 600);
if ($is_non_profit) {
    $landscape_headline = go_spin_text("{The Digital Shift for {$niche_plural} in Nigeria|Growing Your {$niche_name} Community Online|Why Nigerian {$niche_plural} Need a Modern Platform}");
    $landscape_copy = go_spin_text("{The Nigerian {$niche_name} community is expanding. Today, people from Lagos to Kano expect a welcoming, informative digital experience before they ever step through your doors.|Across Nigeria, the most impactful {$niche_plural} share one trait: a clear, accessible digital footprint. Whether your community is in Abuja, Port Harcourt, or nationwide, your web presence is vital for connection.|Nigeria's digital landscape is evolving. For a growing {$niche_name}, relying solely on word-of-mouth or an outdated website means missing out on reaching people who are actively searching for your community.}");
} elseif ($is_education) {
    $landscape_headline = go_spin_text("{The Digital Shift for {$niche_plural} in Nigeria|Why Nigerian Parents Research Online First|Winning Enrolments Starts with Your Website}");
    $landscape_copy = go_spin_text("{Nigerian parents now conduct thorough online research before choosing a {$niche_name}. From Lagos to Kano, the first impression your institution makes is digital, and it must project academic excellence and trustworthiness.|Across Nigeria, the most successful {$niche_plural} share one undeniable trait: a world-class digital presence. Whether your institution is in Abuja, Port Harcourt, or a growing city, your website is your most powerful admissions tool.|Nigeria's education sector is competitive. Families have more options than ever, and they research online before they ever visit a campus. Without a compelling digital presence, your {$niche_name} is invisible to the parents who matter most.}");
} else {
    $landscape_headline = go_spin_text("{The State of {$niche_plural} in Nigeria|Digital Transformation for Nigerian {$niche_plural}|Why Nigerian {$niche_plural} Must Evolve}");
    $landscape_copy = go_spin_text("{The Nigerian {$niche_name} sector is expanding rapidly. Today, high-value clients from Lagos to Kano expect a seamless, trust-building digital experience before they spend a single Naira.|Across Nigeria, the most successful {$niche_plural} share one undeniable trait: a world-class digital footprint. Whether your operations are based in Abuja, Port Harcourt, or nationwide, your web presence is your ultimate competitive advantage.|Nigeria's digital economy is accelerating. For an ambitious {$niche_name}, relying solely on word-of-mouth or an outdated website means losing local and national market share to more digitally aggressive competitors.}");
}
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
    <meta property="og:type" content="website" />
    <meta property="og:url" content="<?= esc_url($canonical_url) ?>" />
    <meta property="og:image" content="<?= esc_url($dynamic_og_image) ?>" />
    <meta name="twitter:card" content="summary_large_image">

    <?php
    // Rich Schema, Service + LocalBusiness
    $schema_service = [
        "@context"        => "https://schema.org",
        "@type"           => "Service",
        "name"            => "{$niche_name} Web Design Services in Nigeria",
        "description"     => "GetOnline Studio provides specialized web design, web development, and digital marketing services for {$niche_plural} across Nigeria.",
        "provider"        => [
            "@type"     => "LocalBusiness",
            "name"      => "GetOnline Studio",
            "url"       => GO_SITE_URL,
            "telephone" => "+2349061150443",
            "address"   => [
                "@type"           => "PostalAddress",
                "streetAddress"   => "48 Gbongan-Ibadan Road",
                "addressLocality" => "Osogbo",
                "addressRegion"   => "Osun State",
                "postalCode"      => "230284",
                "addressCountry"  => "NG"
            ]
        ],
        "areaServed"      => ["@type" => "Country", "name" => "Nigeria"],
        "serviceType"     => "{$niche_name} Web Design",
        "url"             => $canonical_url,
    ];

    // FAQ Schema, built from existing $faq_order
    $faq_schema_items = [];
    foreach ($faq_order as $i) {
        if (!empty($meta["faq{$i}_q"]) && !empty($meta["faq{$i}_a"])) {
            $faq_schema_items[] = [
                "@type"          => "Question",
                "name"           => go_safe_text($meta["faq{$i}_q"]),
                "acceptedAnswer" => ["@type" => "Answer", "text" => go_safe_text($meta["faq{$i}_a"])]
            ];
        }
    }
    $schema_faq = ["@context" => "https://schema.org", "@type" => "FAQPage", "mainEntity" => $faq_schema_items];
    ?>
    <script type="application/ld+json"><?= json_encode($schema_service, JSON_UNESCAPED_SLASHES) ?></script>
    <?php if (!empty($faq_schema_items)): ?>
    <script type="application/ld+json"><?= json_encode($schema_faq, JSON_UNESCAPED_SLASHES) ?></script>
    <?php endif; ?>

    <?php
    // BreadcrumbList schema
    $breadcrumb_hub = [
        "@context" => "https://schema.org",
        "@type"    => "BreadcrumbList",
        "itemListElement" => [
            ["@type" => "ListItem", "position" => 1, "name" => "Home",     "item" => GO_SITE_URL . "/"],
            ["@type" => "ListItem", "position" => 2, "name" => "Services", "item" => GO_SITE_URL . "/services/"],
            ["@type" => "ListItem", "position" => 3, "name" => "{$niche_name} {$service_name} in Nigeria", "item" => $canonical_url],
        ]
    ];

    // AggregateRating + Review schema on the service page
    $rating_hub = [
        "@context"       => "https://schema.org",
        "@type"          => ["LocalBusiness", "ProfessionalService"],
        "name"           => "GetOnline Studio",
        "url"            => GO_SITE_URL,
        "telephone"      => "+2349061150443",
        "foundingDate"   => "2016",
        "description"    => "Web design company with over 9 years of experience specialising in {$niche_plural} across Nigeria.",
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
            ["@type" => "Review", "author" => ["@type" => "Person", "name" => "Olayinka Itunu Damilare"], "reviewRating" => ["@type" => "Rating", "ratingValue" => "5", "bestRating" => "5"], "reviewBody" => "It's not always easy working with a team when you aren't in the same location, but our experience with GetOnline Studio was top-notch. He doesn't just build pages; he builds solutions."],
            ["@type" => "Review", "author" => ["@type" => "Person", "name" => "Mr. Mike"], "reviewRating" => ["@type" => "Rating", "ratingValue" => "5", "bestRating" => "5"], "reviewBody" => "GetOnline Studio developed a website and mobile app for us, and we couldn't be happier with the result. They made the whole process easy and delivered exactly what we needed."],
            ["@type" => "Review", "author" => ["@type" => "Person", "name" => "Amb. Dr. Jernail Singh Anand"], "reviewRating" => ["@type" => "Rating", "ratingValue" => "5", "bestRating" => "5"], "reviewBody" => "GetOnline Studio proved to be a highly trusted partner. The speed of delivery did not compromise quality. We highly recommend them for any institution seeking world-class web development."],
            ["@type" => "Review", "author" => ["@type" => "Person", "name" => "Emmanuel Amaechi"], "reviewRating" => ["@type" => "Rating", "ratingValue" => "5", "bestRating" => "5"], "reviewBody" => "GetOnline Studio delivered more than just a system. They transformed how we operate."],
            ["@type" => "Review", "author" => ["@type" => "Person", "name" => "Chief Lamina Kamiludeen Omotoyosi"], "reviewRating" => ["@type" => "Rating", "ratingValue" => "5", "bestRating" => "5"], "reviewBody" => "I proudly commend GetOnline Studio for their exceptional quality and reliable services. Their professionalism and attention to detail consistently set them apart."],
            ["@type" => "Review", "author" => ["@type" => "Person", "name" => "Tobiloba Babalola"], "reviewRating" => ["@type" => "Rating", "ratingValue" => "5", "bestRating" => "5"], "reviewBody" => "Working with GetOnline Studio was an excellent experience. He demonstrated a high level of professionalism and delivered beyond expectations."],
            ["@type" => "Review", "author" => ["@type" => "Person", "name" => "Mr. Ogundeji Sinmisola"], "reviewRating" => ["@type" => "Rating", "ratingValue" => "5", "bestRating" => "5"], "reviewBody" => "They really met our expectations and our church vision. Good work."],
        ],
    ];
    ?>
    <script type="application/ld+json"><?= json_encode($breadcrumb_hub, JSON_UNESCAPED_SLASHES) ?></script>
    <script type="application/ld+json"><?= json_encode($rating_hub, JSON_UNESCAPED_SLASHES) ?></script>

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
                        'code-green': '#4ade80',
                    },
                    fontFamily: {
                        'syne': ['Syne', 'sans-serif'], 'manrope': ['Manrope', 'sans-serif'],
                        'mono': ['Fira Code', 'monospace'], 'space': ['Space Grotesk', 'sans-serif'],
                    },
                    backgroundImage: {
                        'noise': "url('data:image/svg+xml,%3Csvg viewBox=%220 0 200 200%22 xmlns=%22http://www.w3.org/2000/svg%22%3E%3Cfilter id=%22noiseFilter%22%3E%3CfeTurbulence type=%22fractalNoise%22 baseFrequency=%220.65%22 numOctaves=%223%22 stitchTiles=%22stitch%22/%3E%3C/filter%3E%3Crect width=%22100%25%22 height=%22100%25%22 filter=%22url(%23noiseFilter)%22 opacity=%220.05%22/%3E%3C/svg%3E')",
                    },
                    animation: { 'float': 'float 6s ease-in-out infinite' }
                }
            }
        }
    </script>
    <link rel="stylesheet" href="/assets/css/niche-hub.css">
<?php wp_head(); ?>
</head>
<body class="bg-matte-black bg-noise font-manrope selection:bg-sharp-purple selection:text-white relative">

    <div class="cursor-dot"></div>
    <div class="cursor-outline"></div>

    <!-- ═══ FLOATING WHATSAPP WIDGET ═══ -->
    <div class="fixed bottom-6 right-6 z-50 flex flex-col items-end pointer-events-none">
        <!-- Chat Window -->
        <div id="wa-float-window" class="widget-hidden mb-4 w-[90vw] max-w-[340px] bg-[#0d0d0d] border border-lavender/20 rounded-2xl shadow-2xl flex-col overflow-hidden transition-all duration-300 origin-bottom-right pointer-events-auto">
            <div class="bg-[#151515] p-4 border-b border-lavender/10 flex justify-between items-center cursor-pointer" onclick="toggleWaWidget()">
                <div class="flex items-center gap-3">
                    <div class="w-2 h-2 bg-green-500 rounded-full animate-pulse"></div>
                    <span class="font-syne font-bold text-white text-sm">GetOnline Studio</span>
                </div>
                <button class="text-lavender/50 hover:text-white transition-colors focus:outline-none">
                    <i data-lucide="x" class="w-4 h-4"></i>
                </button>
            </div>
            <div class="p-5">
                <p class="text-sm font-manrope text-lavender/80 mb-5 leading-relaxed">
                    Hi! Looking for a <strong class="text-white"><?= go_safe_text($niche_name) ?> web designer</strong> in Nigeria? Tell us about your project.
                </p>
                <div class="space-y-2 mb-5">
                    <?php
                    $wa_services = ['Website Design', 'Web Development & Portals', 'SEO & Google Ranking', 'Branding & Logo Design', 'Business Automation'];
                    foreach ($wa_services as $svc): ?>
                    <label class="flex items-center gap-3 p-2.5 rounded-lg border border-white/5 bg-matte-black hover:border-sharp-purple/50 cursor-pointer transition-colors">
                        <input type="checkbox" value="<?= esc_attr($svc) ?>" class="wa-service-cb w-4 h-4 accent-sharp-purple rounded">
                        <span class="text-sm text-lavender/90 font-bold"><?= esc_html($svc) ?></span>
                    </label>
                    <?php endforeach; ?>
                </div>
                <button onclick="sendWaWidget()" class="w-full bg-[#25D366] text-white font-bold py-3.5 rounded-xl hover:bg-[#1ebe5d] transition-all shadow-lg flex items-center justify-center gap-2 uppercase tracking-wide text-xs">
                    <i data-lucide="message-circle" class="w-4 h-4"></i> Continue on WhatsApp
                </button>
            </div>
        </div>
        <!-- Trigger Button -->
        <button id="wa-float-btn" onclick="toggleWaWidget()" class="w-14 h-14 bg-[#25D366] rounded-full flex items-center justify-center text-white shadow-[0_4px_20px_rgba(37,211,102,0.4)] hover:bg-[#1ebe5d] hover:scale-105 transition-all focus:outline-none pointer-events-auto">
            <i data-lucide="message-circle" class="w-6 h-6"></i>
        </button>
    </div>

    <nav class="fixed top-0 w-full z-40 px-4 md:px-6 py-4 md:py-6 flex justify-between items-center mix-blend-difference text-lavender">
        <a href="https://getonlinestudio.com" class="font-syne font-bold text-xl md:text-2xl hover:text-sharp-purple transition-colors hover-target">GO.</a>
        <a href="#consultation" class="text-xs md:text-sm font-bold tracking-widest uppercase border border-lavender px-4 md:px-6 py-2 rounded-full hover:bg-lavender hover:text-matte-black transition-all duration-300 bg-matte-black/20 backdrop-blur-sm hover-target">
            Start Project
        </a>
    </nav>

    <!-- SECTION 1: NATIONAL HERO -->
    <header class="relative min-h-[90vh] flex flex-col justify-center items-center px-4 overflow-hidden border-b border-lavender/10 pt-32 pb-24 md:pb-32">
        <div class="perspective-grid"></div>
        <div class="absolute w-40 h-40 rounded-full border border-sharp-purple/20 top-[15%] left-[10%] animate-float" style="animation-delay: 0s;"></div>
        <div class="absolute w-32 h-32 rotate-45 border border-lavender/10 top-[30%] right-[15%] animate-float" style="animation-delay: 1.5s;"></div>

        <div class="absolute top-24 left-4 md:left-8 z-30 reveal-up" style="animation-delay: 0.1s;">
            <nav aria-label="Breadcrumb">
                <ol class="flex flex-wrap items-center gap-2 text-[10px] md:text-xs font-mono uppercase tracking-widest text-lavender/40">
                    <li><a href="/" class="hover:text-sharp-purple transition-colors hover-target">Home</a></li>
                    <li><span class="opacity-30">/</span></li>
                    <li><a href="/services/" class="hover:text-sharp-purple transition-colors hover-target">Services</a></li>
                    <li><span class="opacity-30">/</span></li>
                    <li><span class="text-lavender/70 truncate" aria-current="page"><?= go_safe_text($niche_name) ?></span></li>
                </ol>
            </nav>
        </div>

        <div class="relative z-20 text-center max-w-5xl mx-auto mt-10 md:mt-0">
            <div class="flex items-center justify-center mb-6 md:mb-8 reveal-up">
                <div class="inline-flex items-center gap-3 font-mono text-code-green uppercase tracking-[0.2em] text-[10px] md:text-xs font-bold bg-code-green/10 px-4 py-2 rounded-full border border-code-green/20 backdrop-blur-sm">
                    <span class="relative flex h-2 w-2">
                        <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-code-green opacity-75"></span>
                        <span class="relative inline-flex rounded-full h-2 w-2 bg-code-green"></span>
                    </span>
                    <span>National Authority &middot; Nigeria</span>
                </div>
            </div>

            <h1 class="font-syne text-[8vw] md:text-[5vw] leading-[1.1] font-bold text-lavender reveal-up" style="animation-delay: 0.2s;">
                <?= go_safe_text($niche_name) ?> <span class="text-transparent text-stroke hover:text-sharp-purple transition-colors duration-300 cursor-default"><?= go_safe_text($service_name) ?></span> in Nigeria
            </h1>

            <!-- Company signal, immediately below H1 -->
            <p class="font-mono text-[11px] text-lavender/40 tracking-widest uppercase mt-4 mb-2 reveal-up" style="animation-delay: 0.3s;">
                Web Design Company &middot; Web Developer &middot; Digital Agency &middot; Nigeria
            </p>

            <p class="font-manrope text-lavender/70 text-lg md:text-xl max-w-3xl mx-auto mt-6 leading-relaxed reveal-up" style="animation-delay: 0.4s;">
                <?= go_spin_text("{Generic templates don't work for {$niche_plural}.|Most {$niche_name} websites are just digital brochures that don't drive real results.|You don't just need a website, you need a {$vocab['growth_system']}.}") ?> We are a registered <strong class="text-white">web design company</strong> building specialized, high-converting platforms for the <?= go_safe_text($niche_name) ?> <?= $vocab['industry'] ?> nationwide. If you are searching for <strong class="text-white"><?= go_safe_text($lsi_keyword) ?></strong>, you have found the <?= $vocab['market_leader'] ?>.
            </p>

            <!-- Trust signals row -->
            <div class="flex flex-wrap items-center justify-center gap-6 mt-8 mb-10 reveal-up" style="animation-delay: 0.5s;">
                <?php $trust_items = ['Registered Nigerian Company', 'Full Design & Dev Team', 'SEO-First Every Build', 'Nationwide Coverage']; ?>
                <?php foreach ($trust_items as $ti): ?>
                <div class="flex items-center gap-2 text-xs font-mono text-lavender/50 uppercase tracking-widest">
                    <i data-lucide="check-circle" class="w-3.5 h-3.5 text-code-green flex-shrink-0"></i>
                    <?= esc_html($ti) ?>
                </div>
                <?php endforeach; ?>
            </div>

            <div class="flex flex-col sm:flex-row items-center justify-center gap-4 reveal-up" style="animation-delay: 0.6s;">
                <a href="#locations" class="w-full sm:w-auto bg-sharp-purple text-white px-10 py-4 rounded-full text-sm font-bold uppercase tracking-widest hover:bg-white hover:text-matte-black transition-all hover-target shadow-[0_0_30px_rgba(126,34,206,0.3)]">
                    Find Your City &rarr;
                </a>
                <button onclick="openWaWidgetWithService('Website Design')" class="w-full sm:w-auto border border-lavender/30 text-lavender px-10 py-4 rounded-full text-sm font-bold uppercase tracking-widest hover:bg-lavender hover:text-matte-black transition-all hover-target flex items-center justify-center gap-2 focus:outline-none">
                    <i data-lucide="message-circle" class="w-4 h-4"></i> Chat Now
                </button>
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

    <!-- SECTION 1.5: KEYWORD VARIATION COVERAGE, 5-card grid -->
    <section class="py-12 px-4 md:px-6 bg-[#0a0a0a] border-b border-lavender/5 relative z-10">
        <div class="max-w-7xl mx-auto">
            <p class="font-mono text-[10px] text-sharp-purple tracking-widest uppercase mb-3 text-center">
                [ <?= go_safe_text($niche_name) ?> Web Design Services in Nigeria ]
            </p>
            <h2 class="font-syne text-xl md:text-2xl font-bold text-white text-center mb-8">
                <?= go_safe_text($niche_name) ?> Web Design & Development, Every Service Your Business Needs
            </h2>
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-4">
                <?php foreach ($kw_variants_national as $kv): ?>
                <div class="bg-card-dark border border-lavender/10 rounded-2xl p-5 hover:border-sharp-purple/40 transition-colors">
                    <i data-lucide="<?= esc_attr($kv['icon']) ?>" class="w-5 h-5 text-sharp-purple mb-3"></i>
                    <h3 class="font-syne text-sm font-bold text-white mb-2"><?= go_safe_text($kv['term']) ?></h3>
                    <p class="text-xs text-lavender/50 leading-relaxed font-manrope"><?= go_safe_text($kv['desc']) ?></p>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
    </section>

    <!-- THE REALITY / PAIN POINTS -->
    <section id="process" class="py-24 md:py-32 px-4 md:px-6 bg-matte-black relative z-10 border-b border-lavender/10">
        <div class="max-w-7xl mx-auto grid grid-cols-1 lg:grid-cols-2 gap-16 lg:gap-24 items-start">
            <div class="space-y-6">
                <h2 class="font-syne text-3xl md:text-5xl font-bold mb-8"><?= go_safe_text($reality_headline) ?></h2>
                <div class="space-y-6 text-lavender/70 leading-relaxed md:text-lg">
                    <p><?= nl2br(go_safe_text($meta['reality_p1'])) ?></p>
                    <p><?= nl2br(go_safe_text($meta['reality_p2'])) ?></p>
                    <p class="pl-6 border-l-2 border-sharp-purple text-white font-medium">When <?= $vocab['audience'] ?> search for <strong><?= go_safe_text($exact_keyword) ?></strong>, your platform must project absolute <?= $vocab['projection'] ?>. <?= nl2br(go_safe_text($meta['reality_p3'])) ?></p>
                    <p><?= nl2br(go_safe_text($meta['reality_p4'])) ?></p>
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

    <!-- MID-PAGE CTA STRIP #1 — after pain points -->
    <div class="py-10 px-4 md:px-6 bg-[#0d0d0d] border-b border-lavender/10 flex flex-col sm:flex-row items-center justify-between gap-6 max-w-7xl mx-auto">
        <p class="font-syne text-lg md:text-2xl font-bold text-lavender text-center sm:text-left">
            Ready to fix this for your <span class="text-sharp-purple"><?= go_safe_text($niche_name) ?></span>? Let's talk.
        </p>
        <button onclick="openWaWidgetWithService('Website Design')" class="flex-shrink-0 inline-flex items-center gap-3 bg-sharp-purple text-white font-syne font-bold uppercase tracking-widest text-sm px-8 py-4 rounded-full hover:bg-white hover:text-matte-black transition-all duration-300 shadow-[0_0_24px_rgba(126,34,206,0.3)] hover-target focus:outline-none">
            <i data-lucide="message-circle" class="w-5 h-5"></i> Start a Conversation
        </button>
    </div>

    <!-- FEATURES -->
    <section class="py-24 md:py-32 bg-[#0d0d0d] relative z-10 border-b border-lavender/10">
        <div class="max-w-7xl mx-auto px-4 md:px-6">
            <div class="text-center max-w-3xl mx-auto mb-16 md:mb-24">
                <h2 class="font-syne text-3xl md:text-5xl font-bold mb-6"><?= go_safe_text($meta['feat_headline']) ?></h2>
                <p class="font-manrope text-lavender/60 text-lg md:text-xl">
                    As a leading <strong><?= go_safe_text($lsi_keyword) ?></strong>, we deliver more than just a website. <?= go_safe_text($meta['feat_subline']) ?>
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

    <!-- SERVICES GRID -->
    <section id="services" class="py-24 md:py-32 bg-matte-black relative z-10 border-b border-lavender/10 overflow-hidden">
        <div class="max-w-7xl mx-auto px-4 md:px-6">
            <div class="mb-6">
                <p class="font-mono text-[10px] text-sharp-purple tracking-widest uppercase mb-4">[ What We Do ]</p>
                <h2 class="font-syne text-3xl md:text-5xl font-bold mb-4 text-white">More Than a Website. A Full <?= go_safe_text($niche_name) ?> Digital Ecosystem.</h2>
                <p class="font-manrope text-lavender/60 text-lg max-w-2xl mt-4 leading-relaxed">
                    We are a <strong class="text-white">digital infrastructure agency</strong>, not just a web design company. Here is the full range of what we build for <?= go_safe_text(strtolower($niche_plural)) ?> across Nigeria.
                </p>
            </div>
            <div class="mt-12 grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
                <?php
                $hub_services = [
                    ['icon' => 'monitor',       'num' => '01', 'name' => 'Professional Website Design',       'desc' => 'Custom-crafted websites built to reflect your ' . go_safe_text(strtolower($niche_name)) . ' brand and convert visitors into paying clients.',               'tags' => ['More Customers', 'Trust & Credibility'], 'svc' => 'Website Design'],
                    ['icon' => 'code-2',         'num' => '02', 'name' => 'Web Development & Portals',         'desc' => 'Full-stack development for member portals, booking systems, dashboards, and custom ' . go_safe_text(strtolower($niche_name)) . ' platforms.',            'tags' => ['Custom Logic', 'Scalable'],             'svc' => 'Web Development & Portals'],
                    ['icon' => 'trending-up',    'num' => '03', 'name' => 'SEO & Google Ranking',              'desc' => 'Rank on the first page when clients across Nigeria search for ' . go_safe_text(strtolower($niche_name)) . ' services.',                                  'tags' => ['Page 1 Google', 'National Reach'],      'svc' => 'SEO & Google Ranking'],
                    ['icon' => 'map-pin',        'num' => '04', 'name' => 'Local SEO & Google Maps',           'desc' => 'Dominate the Maps pack in every city you operate in so nearby clients find your ' . go_safe_text(strtolower($niche_name)) . ' first.',                    'tags' => ['Maps Pack', 'City Targeting'],          'svc' => 'Local SEO & Google Maps'],
                    ['icon' => 'pen-tool',       'num' => '05', 'name' => 'Branding & Logo Design',            'desc' => 'A complete visual identity that makes your ' . go_safe_text(strtolower($niche_name)) . ' look professional and memorable from day one.',                 'tags' => ['Identity', 'Recognition'],             'svc' => 'Branding & Logo Design'],
                    ['icon' => 'cpu',            'num' => '06', 'name' => 'Business Automation',               'desc' => 'CRM integrations, auto-responses, and booking flows that run your ' . go_safe_text(strtolower($niche_name)) . ' operations 24/7.',                      'tags' => ['24/7 Ops', 'Lead Nurturing'],           'svc' => 'Business Automation'],
                    ['icon' => 'landmark',       'num' => '07', 'name' => 'CAC Registration',                  'desc' => 'Get your ' . go_safe_text(strtolower($niche_name)) . ' legally registered with the Corporate Affairs Commission, fast and stress-free.',              'tags' => ['Legal Entity', 'Corporate Trust'],      'svc' => 'CAC Registration'],
                    ['icon' => 'share-2',        'num' => '08', 'name' => 'Social Media Setup & Strategy',     'desc' => 'Professional social pages, content strategy, and audience-building designed for ' . go_safe_text(strtolower($niche_name)) . ' businesses nationwide.',  'tags' => ['More Followers', 'Brand Consistency'],  'svc' => 'Social Media Setup'],
                    ['icon' => 'shield-check',   'num' => '09', 'name' => 'Website Maintenance & Support',     'desc' => 'Security updates, backups, and priority support so your ' . go_safe_text(strtolower($niche_name)) . ' platform stays fast and always online.',          'tags' => ['Always Online', 'Security'],            'svc' => 'Website Maintenance'],
                ];
                foreach ($hub_services as $svc): ?>
                <button type="button" onclick="openWaWidgetWithService('<?= esc_js($svc['svc']) ?>')" class="w-full text-left group bg-card-dark border border-lavender/10 rounded-2xl p-8 hover:border-sharp-purple/50 hover:bg-[#111] transition-all duration-300 hover:-translate-y-1 hover-target flex flex-col gap-5 focus:outline-none active:scale-[0.98]">
                    <div class="flex items-start justify-between w-full">
                        <div class="w-12 h-12 rounded-xl bg-sharp-purple/10 flex items-center justify-center group-hover:bg-sharp-purple transition-colors duration-300">
                            <i data-lucide="<?= esc_attr($svc['icon']) ?>" class="w-5 h-5 text-sharp-purple group-hover:text-white transition-colors"></i>
                        </div>
                        <span class="font-mono text-xs text-lavender/20 group-hover:text-sharp-purple transition-colors"><?= esc_html($svc['num']) ?></span>
                    </div>
                    <div>
                        <h3 class="font-syne text-xl font-bold text-white mb-2 group-hover:text-sharp-purple transition-colors"><?= esc_html($svc['name']) ?></h3>
                        <p class="font-manrope text-sm text-lavender/60 leading-relaxed"><?= $svc['desc'] ?></p>
                    </div>
                    <div class="mt-auto flex flex-wrap gap-2">
                        <span class="text-[10px] font-mono uppercase tracking-widest text-sharp-purple border border-sharp-purple/20 px-2 py-1 rounded-full"><?= esc_html($svc['tags'][0]) ?></span>
                        <span class="text-[10px] font-mono uppercase tracking-widest text-lavender/40 border border-lavender/10 px-2 py-1 rounded-full"><?= esc_html($svc['tags'][1]) ?></span>
                    </div>
                </button>
                <?php endforeach; ?>
            </div>
            <!-- Bottom CTA strip -->
            <div class="mt-16 border border-lavender/10 rounded-2xl p-8 md:p-10 bg-card-dark flex flex-col md:flex-row items-center justify-between gap-6">
                <div>
                    <p class="font-mono text-[10px] text-sharp-purple tracking-widest uppercase mb-2">[ Not Sure Where to Start? ]</p>
                    <h3 class="font-syne text-2xl md:text-3xl font-bold text-white">Not sure where to start? Send us a message.</h3>
                    <p class="font-manrope text-lavender/60 text-sm mt-2">Tell us about your <?= go_safe_text(strtolower($niche_name)) ?> and we'll tell you exactly what you need. WhatsApp, email, or call — whatever works best for you.</p>
                </div>
                <button type="button" onclick="openWaWidgetWithService('Website Design')" class="flex-shrink-0 inline-flex items-center gap-3 bg-sharp-purple text-white px-8 py-4 rounded-full text-sm font-bold uppercase tracking-widest hover:bg-white hover:text-matte-black transition-all hover-target shadow-[0_0_30px_rgba(126,34,206,0.3)] whitespace-nowrap focus:outline-none min-h-[44px]">
                    <i data-lucide="message-circle" class="w-4 h-4"></i> Chat with Us
                </button>
            </div>
        </div>
    </section>

    <!-- MID-PAGE MARQUEE STRIP -->
    <section class="py-6 bg-sharp-purple overflow-hidden">
        <div class="marquee-container">
            <div class="marquee-content font-space font-bold text-black text-lg uppercase tracking-widest">
                <span class="mx-6">STRATEGY</span>✦<span class="mx-6">DESIGN</span>✦<span class="mx-6">DEVELOPMENT</span>✦<span class="mx-6">AUTOMATION</span>✦<span class="mx-6">SEO</span>✦<span class="mx-6">BRANDING</span>✦<span class="mx-6">PERFORMANCE</span>✦<span class="mx-6">GROWTH</span>✦
            </div>
            <div class="marquee-content font-space font-bold text-black text-lg uppercase tracking-widest">
                <span class="mx-6">STRATEGY</span>✦<span class="mx-6">DESIGN</span>✦<span class="mx-6">DEVELOPMENT</span>✦<span class="mx-6">AUTOMATION</span>✦<span class="mx-6">SEO</span>✦<span class="mx-6">BRANDING</span>✦<span class="mx-6">PERFORMANCE</span>✦<span class="mx-6">GROWTH</span>✦
            </div>
        </div>
    </section>

    <!-- NEW: NATIONAL LANDSCAPE & INSIGHTS MODULE -->
    <section class="py-24 md:py-32 px-4 md:px-6 bg-card-dark relative z-10 border-b border-lavender/10 overflow-hidden">
        <div class="absolute left-0 bottom-0 w-[500px] h-[500px] bg-sharp-purple/5 blur-[100px] rounded-full pointer-events-none"></div>
        <div class="max-w-7xl mx-auto flex flex-col md:flex-row items-center gap-12 md:gap-20">
            <div class="flex-1 space-y-6">
                <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-sharp-purple/10 border border-sharp-purple/30 text-sharp-purple text-xs font-bold uppercase tracking-wider mb-2">
                    <i data-lucide="bar-chart-3" class="w-4 h-4"></i> Industry Insight
                </div>
                <h2 class="font-syne text-3xl md:text-5xl font-bold text-white"><?= go_safe_text($landscape_headline) ?></h2>
                <p class="font-manrope text-lavender/70 leading-relaxed text-lg">
                    <?= go_safe_text($landscape_copy) ?>
                </p>
                <ul class="space-y-4 pt-4">
                    <li class="flex items-start gap-3">
                        <i data-lucide="check-circle-2" class="w-6 h-6 text-sharp-purple shrink-0"></i>
                        <span class="text-lavender/80 font-manrope">We build platforms compliant with Nigerian payment gateways (Paystack, Flutterwave).</span>
                    </li>
                    <li class="flex items-start gap-3">
                        <i data-lucide="check-circle-2" class="w-6 h-6 text-sharp-purple shrink-0"></i>
                        <span class="text-lavender/80 font-manrope">Optimized for high-speed loading on Nigerian mobile networks.</span>
                    </li>
                    <li class="flex items-start gap-3">
                        <i data-lucide="check-circle-2" class="w-6 h-6 text-sharp-purple shrink-0"></i>
                        <span class="text-lavender/80 font-manrope">Tailored specifically for the operational needs of a modern <?= go_safe_text($niche_name) ?>.</span>
                    </li>
                </ul>
            </div>
            <div class="flex-1 w-full relative">
                <div class="absolute inset-0 bg-gradient-to-tr from-sharp-purple/20 to-transparent rounded-3xl blur-2xl"></div>
                <div class="relative bg-matte-black border border-lavender/10 p-8 md:p-12 rounded-3xl shadow-2xl">
                    <div class="flex justify-between items-end mb-8 border-b border-lavender/10 pb-6">
                        <div>
                            <p class="text-[10px] font-mono text-sharp-purple uppercase tracking-widest mb-1"><?= $vocab['insight_title'] ?></p>
                            <p class="font-syne text-2xl font-bold text-white">"<?= go_safe_text($niche_name) ?> near me"</p>
                        </div>
                        <i data-lucide="trending-up" class="w-8 h-8 text-code-green"></i>
                    </div>
                    <p class="font-manrope text-lavender/60 text-sm leading-relaxed">
                        Every month, thousands of searches are made across Nigeria for trusted <strong><?= go_safe_text($niche_plural) ?></strong>. If your digital presence isn't optimized to capture and convert this nationwide traffic, you are <?= $vocab['loss_state'] ?>.
                    </p>
                </div>
            </div>
        </div>
    </section>

    <!-- SECTION 2B: COMPANY vs FREELANCER DIFFERENTIATOR -->
    <section class="py-16 md:py-20 px-4 md:px-6 bg-[#0d0d0d] border-t border-lavender/5 relative z-10">
        <div class="max-w-7xl mx-auto">
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-10 items-center">
                <div>
                    <p class="font-mono text-[10px] text-sharp-purple tracking-widest uppercase mb-4">[ Why Choose a Company Over a Freelancer ]</p>
                    <h2 class="font-syne text-2xl md:text-4xl font-bold text-white mb-4 leading-tight">
                        The Best <?= go_safe_text($niche_name) ?> Web Designer in Nigeria is a Team, Not One Person.
                    </h2>
                    <p class="font-manrope text-lavender/60 text-base leading-relaxed mb-4">
                        When you search for a <strong class="text-white"><?= go_safe_text($niche_name) ?> web designer in Nigeria</strong> or a <strong class="text-white"><?= go_safe_text($niche_name) ?> web design company</strong>, you want a result you can trust with your business. GetOnline Studio is a registered Nigerian web design and development company, not a freelancer, which means you get a dedicated team handling design, development, SEO, and ongoing support.
                    </p>
                    <p class="font-manrope text-lavender/60 text-base leading-relaxed">
                        Our web designers and web developers collaborate on every <?= go_safe_text($niche_name) ?> project, so your website doesn't just look good, it performs, loads fast, ranks on Google, and converts visitors into clients.
                    </p>
                </div>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <?php
                    $diff_cards = [
                        ['icon' => 'users',       'label' => 'Full Studio Team',       'desc' => 'Designers, developers, SEO specialists, all working on your project.'],
                        ['icon' => 'shield-check', 'label' => 'Registered Company',     'desc' => 'A incorporated Nigerian business, accountable, reliable, insured.'],
                        ['icon' => 'code-2',       'label' => 'Design + Development',   'desc' => 'We build custom, not Wix templates. Real, custom web development.'],
                        ['icon' => 'trending-up',  'label' => 'SEO Built In',           'desc' => 'Every ' . go_safe_text($niche_name) . ' site we build is optimised for Google from day one.'],
                    ];
                    foreach ($diff_cards as $dc): ?>
                    <div class="bg-card-dark border border-lavender/10 rounded-2xl p-5 flex items-start gap-4 hover:border-sharp-purple/30 transition-colors">
                        <div class="w-9 h-9 rounded-xl bg-sharp-purple/10 flex items-center justify-center flex-shrink-0 mt-0.5">
                            <i data-lucide="<?= esc_attr($dc['icon']) ?>" class="w-4 h-4 text-sharp-purple"></i>
                        </div>
                        <div>
                            <h3 class="font-syne font-bold text-white text-sm mb-1"><?= esc_html($dc['label']) ?></h3>
                            <p class="font-manrope text-xs text-lavender/50 leading-relaxed"><?= esc_html($dc['desc']) ?></p>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
    </section>

    <!-- THE NATIONAL SILO GRID -->
    <section id="locations" class="py-24 md:py-32 px-4 md:px-6 bg-[#0B0A0F] border-b border-lavender/10 relative z-10 overflow-hidden">
        <div class="absolute right-0 top-0 w-96 h-96 bg-sharp-purple/10 blur-[100px] rounded-full pointer-events-none"></div>

        <div class="max-w-7xl mx-auto">
            <div class="text-center max-w-3xl mx-auto mb-16">
                <p class="font-mono text-[10px] text-sharp-purple tracking-widest uppercase mb-4">[ Nationwide Coverage ]</p>
                <h2 class="font-syne text-3xl md:text-5xl font-bold mb-6">
                    <?= go_safe_text($niche_name) ?> Web Design Services<br class="hidden md:block"> Across Every City in Nigeria.
                </h2>
                <p class="font-manrope text-lavender/60 text-lg">We operate specialized digital hubs across Nigeria. Select your city to see hyper-local data, pricing, and strategies tailored to your specific market.</p>
            </div>

            <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-6 gap-4">
                <?php foreach($all_cities as $c_slug => $c_name):
                    $target_url = "/locations/{$c_slug}/{$niche_slug}-{$service_slug}/";
                ?>
                <a href="<?= esc_url($target_url) ?>" class="group block bg-card-dark border border-white/5 p-5 rounded-2xl hover:border-sharp-purple/50 hover:bg-[#15121c] transition-all duration-300 hover-target text-center relative overflow-hidden">
                    <div class="absolute inset-0 bg-gradient-to-b from-transparent to-sharp-purple/5 opacity-0 group-hover:opacity-100 transition-opacity"></div>
                    <i data-lucide="map-pin" class="w-5 h-5 text-lavender/30 mx-auto mb-3 group-hover:text-sharp-purple transition-colors"></i>
                    <h3 class="font-syne text-sm font-bold text-white group-hover:text-sharp-purple transition-colors"><?= go_safe_text($c_name) ?></h3>
                    <p class="font-mono text-[9px] text-lavender/30 mt-1 uppercase tracking-widest group-hover:text-sharp-purple/60 transition-colors"><?= go_safe_text($niche_name) ?></p>
                </a>
                <?php endforeach; ?>
            </div>
        </div>
    </section>

    <!-- ═══ RICH NATIONAL PRICING SECTION ═══ -->
    <section class="py-24 md:py-32 px-4 md:px-6 bg-matte-black border-b border-lavender/10 relative z-10 overflow-hidden">
        <div class="absolute right-0 bottom-0 w-[600px] h-[400px] bg-sharp-purple/5 blur-[120px] rounded-full pointer-events-none"></div>
        <div class="max-w-7xl mx-auto">

            <!-- Section Header -->
            <div class="text-center max-w-3xl mx-auto mb-16">
                <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-sharp-purple/10 border border-sharp-purple/30 text-sharp-purple text-xs font-bold uppercase tracking-wider mb-6">
                    <i data-lucide="bar-chart-2" class="w-4 h-4"></i> National Pricing Intelligence
                </div>
                <h2 class="font-syne text-3xl md:text-5xl font-bold text-white mb-6">
                    How Much Does a <?= go_safe_text($niche_name) ?> Website Cost in Nigeria?
                </h2>
                <p class="font-manrope text-lavender/60 text-lg leading-relaxed">
                    Real investment ranges for <?= go_safe_text($niche_plural) ?> across <strong class="text-white"><?= $city_count ?> Nigerian cities</strong>. Every tier below is built to justify every Naira spent.
                </p>
            </div>

            <!-- 3 Pricing Tier Cards -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-16 items-stretch">

                <!-- Entry Tier -->
                <div class="bg-card-dark border border-lavender/10 rounded-3xl p-8 flex flex-col hover:border-lavender/30 transition-colors">
                    <div class="font-mono text-[10px] text-lavender/40 uppercase tracking-widest mb-3">Entry Level</div>
                    <div class="font-syne text-4xl font-bold text-white mb-1"><?= $fmt_cost_low ?></div>
                    <div class="font-manrope text-lavender/40 text-xs mb-6">One-time investment. No recurring surprise fees.</div>
                    <div class="flex-1 space-y-3 text-sm font-manrope text-lavender/70 mb-8">
                        <?php foreach ($pricing_entry as $feature): ?>
                        <div class="flex items-start gap-3">
                            <i data-lucide="check-circle" class="w-4 h-4 text-code-green shrink-0 mt-0.5"></i>
                            <span><?= go_safe_text($feature) ?></span>
                        </div>
                        <?php endforeach; ?>
                    </div>
                    <div class="pt-6 border-t border-lavender/10 space-y-3">
                        <div class="flex items-center justify-between">
                            <span class="font-mono text-xs text-lavender/30 uppercase">Chosen by</span>
                            <span class="font-syne text-2xl font-bold text-code-green"><?= $pct_budget ?>%</span>
                        </div>
                        <a href="#pricing-calculator" onclick="window.selectedTier='entry'; if(typeof switchTier==='function') setTimeout(function(){switchTier('entry')},200);" class="w-full flex items-center justify-center gap-2 border border-code-green/40 text-code-green font-bold text-sm py-3 rounded-xl hover:bg-code-green hover:text-matte-black transition-all font-manrope">
                            Start with Entry <i data-lucide="arrow-down" class="w-4 h-4"></i>
                        </a>
                    </div>
                </div>

                <!-- Standard Tier -->
                <div class="bg-card-dark border border-sharp-purple/50 rounded-3xl p-8 flex flex-col relative shadow-[0_0_50px_rgba(126,34,206,0.2)]">
                    <div class="absolute -top-3 left-1/2 -translate-x-1/2 bg-sharp-purple text-white text-[10px] font-bold uppercase tracking-widest px-4 py-1 rounded-full whitespace-nowrap">Most Popular</div>
                    <div class="font-mono text-[10px] text-sharp-purple uppercase tracking-widest mb-3">Standard Build</div>
                    <div class="font-syne text-4xl font-bold text-white mb-1"><?= $fmt_cost_typical ?></div>
                    <div class="font-manrope text-lavender/40 text-xs mb-6">The complete package most <?= go_safe_text($niche_plural) ?> build their business on.</div>
                    <div class="flex-1 space-y-3 text-sm font-manrope text-lavender/70 mb-8">
                        <?php foreach ($pricing_standard as $feature): ?>
                        <div class="flex items-start gap-3">
                            <i data-lucide="check-circle" class="w-4 h-4 text-sharp-purple shrink-0 mt-0.5"></i>
                            <span><?= go_safe_text($feature) ?></span>
                        </div>
                        <?php endforeach; ?>
                    </div>
                    <div class="pt-6 border-t border-sharp-purple/20 space-y-3">
                        <div class="flex items-center justify-between">
                            <span class="font-mono text-xs text-lavender/30 uppercase">Chosen by</span>
                            <span class="font-syne text-2xl font-bold text-sharp-purple"><?= $pct_standard ?>%</span>
                        </div>
                        <a href="#pricing-calculator" onclick="window.selectedTier='standard'; if(typeof switchTier==='function') setTimeout(function(){switchTier('standard')},200);" class="w-full flex items-center justify-center gap-2 bg-sharp-purple text-white font-bold text-sm py-3 rounded-xl hover:shadow-[0_0_20px_rgba(126,34,206,0.4)] transition-all font-manrope">
                            Start with Standard <i data-lucide="arrow-down" class="w-4 h-4"></i>
                        </a>
                    </div>
                </div>

                <!-- Premium Tier -->
                <div class="bg-[#0d0b14] border border-lavender/20 rounded-3xl p-8 flex flex-col relative overflow-hidden hover:border-lavender/40 transition-colors">
                    <div class="absolute top-0 right-0 w-40 h-40 bg-sharp-purple/10 blur-[60px] rounded-full pointer-events-none"></div>
                    <div class="font-mono text-[10px] text-lavender/50 uppercase tracking-widest mb-3">Premium / Enterprise</div>
                    <div class="font-syne text-4xl font-bold text-white mb-1"><?= $fmt_cost_high ?>+</div>
                    <div class="font-manrope text-lavender/40 text-xs mb-6">Built for <?= go_safe_text($niche_plural) ?> that want to dominate, not just exist online.</div>
                    <div class="flex-1 space-y-3 text-sm font-manrope text-lavender/70 mb-8">
                        <?php foreach ($pricing_premium as $feature): ?>
                        <div class="flex items-start gap-3">
                            <i data-lucide="check-circle" class="w-4 h-4 text-lavender/60 shrink-0 mt-0.5"></i>
                            <span><?= go_safe_text($feature) ?></span>
                        </div>
                        <?php endforeach; ?>
                    </div>
                    <div class="pt-6 border-t border-lavender/10 space-y-4">
                        <div class="flex items-center justify-between">
                            <span class="font-mono text-xs text-lavender/30 uppercase">Chosen by</span>
                            <span class="font-syne text-2xl font-bold text-lavender/60"><?= $pct_premium ?>%</span>
                        </div>
                        <a href="#pricing-calculator" onclick="window.selectedTier='premium'; if(typeof switchTier==='function') setTimeout(function(){switchTier('premium')},200);"
                           class="w-full flex items-center justify-center gap-2 border border-lavender/30 text-lavender font-bold text-sm py-3 rounded-xl hover:bg-lavender hover:text-matte-black transition-all font-manrope">
                            <i data-lucide="message-circle" class="w-4 h-4"></i> Discuss Enterprise Scope
                        </a>
                    </div>
                </div>
            </div>

            <!-- Rich Stats Row -->
            <div class="grid grid-cols-2 md:grid-cols-4 gap-6 mb-16">
                <div class="bg-[#0a0a0a] border border-lavender/10 rounded-2xl p-6 text-center">
                    <div class="font-syne text-3xl md:text-4xl font-bold text-sharp-purple mb-2"><?= $city_count ?>+</div>
                    <div class="font-manrope text-xs text-lavender/50 uppercase tracking-widest">Nigerian Cities Covered</div>
                </div>
                <div class="bg-[#0a0a0a] border border-lavender/10 rounded-2xl p-6 text-center">
                    <div class="font-syne text-3xl md:text-4xl font-bold text-sharp-purple mb-2"><?= $avg_roi_month ?>mo</div>
                    <div class="font-manrope text-xs text-lavender/50 uppercase tracking-widest">Avg. Time to Measurable ROI</div>
                </div>
                <div class="bg-[#0a0a0a] border border-lavender/10 rounded-2xl p-6 text-center">
                    <div class="font-syne text-3xl md:text-4xl font-bold text-sharp-purple mb-2"><?= $pct_standard ?>%</div>
                    <div class="font-manrope text-xs text-lavender/50 uppercase tracking-widest">Clients Choose Standard+</div>
                </div>
                <div class="bg-[#0a0a0a] border border-lavender/10 rounded-2xl p-6 text-center">
                    <div class="font-syne text-3xl md:text-4xl font-bold text-sharp-purple mb-2">&#x20A6;0</div>
                    <div class="font-manrope text-xs text-lavender/50 uppercase tracking-widest">Hidden Fees. Ever.</div>
                </div>
            </div>

            <!-- SEO-Rich Pricing Narrative -->
            <div class="bg-[#0a0a0a] border border-lavender/10 rounded-3xl p-8 md:p-12">
                <h3 class="font-syne text-2xl md:text-3xl font-bold text-white mb-6">
                    What Affects <?= go_safe_text($niche_name) ?> Website Pricing in Nigeria?
                </h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-8 font-manrope text-lavender/60 text-sm leading-relaxed">
                    <div class="space-y-4">
                        <p>The cost of a professional <strong class="text-white"><?= go_safe_text($niche_name) ?> website in Nigeria</strong> is shaped by the systems you need, the number of pages, whether professional copywriting is included, and the specific features your industry requires.</p>
                        <p>Entry-level projects start from <strong class="text-white"><?= $fmt_cost_low ?></strong> and cover the essentials every <?= go_safe_text($niche_name) ?> needs to look credible and get found: a clean mobile-optimised site, basic SEO, WhatsApp contact, and Google Business Profile setup.</p>
                        <p>The most popular investment for <?= go_safe_text($niche_plural) ?> sits around <strong class="text-white"><?= $fmt_cost_typical ?></strong>. At this level you get features that are actually specific to your industry:</p>
                        <ul class="space-y-2 mt-2">
                            <?php foreach (array_slice($pricing_standard, 1, 4) as $feat): ?>
                            <li class="flex items-start gap-2">
                                <i data-lucide="chevron-right" class="w-4 h-4 text-sharp-purple shrink-0 mt-0.5"></i>
                                <span><?= go_safe_text($feat) ?></span>
                            </li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                    <div class="space-y-4">
                        <p>Enterprise platforms for <?= go_safe_text($niche_plural) ?> that need portals, payment systems, and automation range from <strong class="text-white"><?= $fmt_cost_typical ?> to <?= $fmt_cost_high ?>+</strong>. These include:</p>
                        <ul class="space-y-2 mt-2">
                            <?php foreach (array_slice($pricing_premium, 1, 4) as $feat): ?>
                            <li class="flex items-start gap-2">
                                <i data-lucide="chevron-right" class="w-4 h-4 text-lavender/50 shrink-0 mt-0.5"></i>
                                <span><?= go_safe_text($feat) ?></span>
                            </li>
                            <?php endforeach; ?>
                        </ul>
                        <p class="mt-4">Unlike freelancers who quote low and revise upward, <strong class="text-white">GetOnline Studio provides fixed, transparent pricing</strong> before any work begins. Your quote is your final cost.</p>
                        <p>Every <?= go_safe_text($niche_name) ?> website we build is SEO-optimised from day one, meaning your investment keeps generating leads long after launch.</p>
                    </div>
                </div>
                <div class="mt-8 pt-8 border-t border-lavender/10 flex flex-col sm:flex-row items-center justify-between gap-4">
                    <p class="font-mono text-xs text-lavender/30 uppercase tracking-widest">Pricing varies by city. Use the calculator below to build your exact quote.</p>
                    <a href="#pricing-calculator" class="text-sharp-purple font-bold text-sm hover:text-white transition-colors flex items-center gap-2 font-manrope">
                        Build My Quote <i data-lucide="arrow-down" class="w-4 h-4"></i>
                    </a>
                </div>
            </div>

        </div>
    </section>

    <!-- PRICING CALCULATOR -->
    <?php
    $calc_file = __DIR__ . '/module-calculator.php';

    // Build niche-specific calculator add-ons from $pricing_standard and $pricing_premium
    // Entry base is always included. Standard features become add-ons. Premium features are premium add-ons.
    // Prices are spread so that base + all standard add-ons ≈ $cost_typical
    // and base + all add-ons ≈ $cost_high
    $standard_addon_count = max(1, count($pricing_standard) - 1); // skip "Everything in Entry"
    $premium_addon_count  = max(1, count($pricing_premium)  - 1); // skip "Everything in Standard"

    $standard_pool = max(10000, round(($cost_typical - $cost_low) / $standard_addon_count / 5000) * 5000);
    $premium_pool  = max(15000, round(($cost_high   - $cost_typical) / $premium_addon_count / 5000) * 5000);

    $support_fee   = ($cost_low < 100000) ? 40000 : 45000;
    $fmt_base      = '₦' . number_format($cost_low);
    $fmt_typical   = '₦' . number_format($cost_typical);
    $fmt_high      = '₦' . number_format($cost_high);
    $fmt_support   = '₦' . number_format($support_fee);
    $org_label     = isset($vocab['org_label']) ? $vocab['org_label'] : $niche_plural;
    ?>

<!-- PRICING CALCULATOR -->
<section id="pricing-calculator" class="py-24 bg-[#0B0A0F] border-t border-white/5 relative overflow-hidden">
    <div class="absolute top-0 left-1/2 -translate-x-1/2 w-[800px] h-[400px] bg-sharp-purple/10 blur-[120px] rounded-full pointer-events-none"></div>
    <div class="max-w-7xl mx-auto px-6 relative z-10">

        <div class="max-w-3xl mx-auto text-center mb-16">
            <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-sharp-purple/10 border border-sharp-purple/30 text-sharp-purple text-xs font-bold uppercase tracking-wider mb-6">
                <i data-lucide="calculator" class="w-4 h-4"></i> Build Your Quote
            </div>
            <h2 class="text-3xl md:text-5xl font-extrabold text-white mb-6 tracking-tight">
                <span class="text-transparent bg-clip-text bg-gradient-to-r from-sharp-purple to-lavender"><?= go_safe_text($niche_name) ?></span> Website — Pick Your Features
            </h2>
            <p class="text-lg text-lavender/70 leading-relaxed">
                Start from the base and add exactly what your <?= go_safe_text($niche_name) ?> needs. Your estimate updates live.
                Entry starts at <strong class="text-white"><?= $fmt_base ?></strong>. Full Standard build is around <strong class="text-white"><?= $fmt_typical ?></strong>.
            </p>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 max-w-5xl mx-auto">

            <!-- Feature checkboxes -->
            <div class="lg:col-span-7 space-y-4">
                <div class="bg-panel-dark border border-white/5 rounded-2xl p-6 md:p-8">

                    <!-- Tier tabs -->
                    <div class="flex gap-2 mb-6 bg-black/30 rounded-xl p-1">
                        <button onclick="switchTier('entry')" id="tab-entry" class="calc-tab flex-1 py-2 rounded-lg text-xs font-bold uppercase tracking-wider transition-all bg-code-green/20 text-code-green">Entry</button>
                        <button onclick="switchTier('standard')" id="tab-standard" class="calc-tab flex-1 py-2 rounded-lg text-xs font-bold uppercase tracking-wider transition-all text-lavender/50 hover:text-white">Standard</button>
                        <button onclick="switchTier('premium')" id="tab-premium" class="calc-tab flex-1 py-2 rounded-lg text-xs font-bold uppercase tracking-wider transition-all text-lavender/50 hover:text-white">Premium</button>
                    </div>

                    <div class="space-y-3" id="calc-options">

                        <!-- Base — always checked, always visible -->
                        <label class="flex items-center justify-between p-4 rounded-xl border border-white/5 bg-white/5 cursor-default">
                            <div class="flex items-center gap-4">
                                <div class="flex items-center justify-center w-6 h-6 rounded border border-sharp-purple bg-sharp-purple/20">
                                    <i data-lucide="check" class="w-4 h-4 text-sharp-purple"></i>
                                </div>
                                <div>
                                    <div class="text-white font-bold text-sm"><?= go_safe_text($pricing_entry[0]) ?></div>
                                    <div class="text-lavender/50 text-xs"><?= go_safe_text($pricing_entry[1] ?? '') ?></div>
                                </div>
                            </div>
                            <div class="text-sharp-purple font-mono text-sm font-bold shrink-0 ml-3">Included</div>
                        </label>

                        <!-- Standard add-ons (skip first "Everything in Entry" line) -->
                        <?php
                        $std_features = array_slice($pricing_standard, 1);
                        foreach ($std_features as $idx => $feat):
                            $price = $standard_pool;
                            // Support & maintenance always gets its own fixed price
                            if (stripos($feat, 'support') !== false && stripos($feat, 'maintenance') !== false) {
                                $price = $support_fee;
                            }
                            $fmt_price = '₦' . number_format($price);
                        ?>
                        <label class="calc-item calc-standard flex items-center justify-between p-4 rounded-xl border border-white/5 bg-black/20 cursor-pointer hover:bg-white/5 transition-colors group">
                            <div class="flex items-center gap-4">
                                <div class="relative flex items-center justify-center w-6 h-6 rounded border border-lavender/30 bg-black/50 group-hover:border-sharp-purple transition-colors">
                                    <input type="checkbox" class="calc-checkbox absolute opacity-0 w-full h-full cursor-pointer" data-price="<?= $price ?>" data-tier="standard">
                                    <i data-lucide="check" class="w-4 h-4 text-sharp-purple opacity-0 transition-opacity"></i>
                                </div>
                                <div>
                                    <div class="text-white font-bold text-sm feature-name"><?= go_safe_text($feat) ?></div>
                                </div>
                            </div>
                            <div class="text-lavender/50 font-mono text-sm shrink-0 ml-3">+<?= $fmt_price ?></div>
                        </label>
                        <?php endforeach; ?>

                        <!-- Premium add-ons (skip first "Everything in Standard" line), hidden by default -->
                        <?php
                        $prem_features = array_slice($pricing_premium, 1);
                        foreach ($prem_features as $idx => $feat):
                            $price = $premium_pool;
                            $fmt_price = '₦' . number_format($price);
                        ?>
                        <label class="calc-item calc-premium hidden flex items-center justify-between p-4 rounded-xl border border-lavender/10 bg-black/20 cursor-pointer hover:bg-white/5 transition-colors group">
                            <div class="flex items-center gap-4">
                                <div class="relative flex items-center justify-center w-6 h-6 rounded border border-lavender/30 bg-black/50 group-hover:border-lavender/60 transition-colors">
                                    <input type="checkbox" class="calc-checkbox absolute opacity-0 w-full h-full cursor-pointer" data-price="<?= $price ?>" data-tier="premium">
                                    <i data-lucide="check" class="w-4 h-4 text-lavender/60 opacity-0 transition-opacity"></i>
                                </div>
                                <div>
                                    <div class="text-white font-bold text-sm feature-name"><?= go_safe_text($feat) ?></div>
                                </div>
                            </div>
                            <div class="text-lavender/40 font-mono text-sm shrink-0 ml-3">+<?= $fmt_price ?></div>
                        </label>
                        <?php endforeach; ?>

                        <!-- Custom needs -->
                        <div class="pt-4 mt-2 border-t border-white/5">
                            <label class="block text-white font-bold text-sm mb-2">Need something not listed?</label>
                            <textarea id="calc-custom-needs" rows="2" class="w-full bg-black/30 border border-lavender/20 rounded-xl p-4 text-lavender text-sm focus:outline-none focus:border-sharp-purple transition-colors resize-none placeholder:text-lavender/30" placeholder="E.g. I also need a membership portal, custom payment flow, multi-language support..."></textarea>
                            <p class="text-[10px] text-lavender/40 mt-1 uppercase tracking-widest">Custom features can be discussed — just send us a message and we will walk you through the options.</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Sticky total -->
            <div class="lg:col-span-5">
                <div class="bg-gradient-to-b from-sharp-purple to-[#4c1d95] rounded-2xl p-1 lg:sticky lg:top-24">
                    <div class="bg-panel-dark rounded-xl p-8 h-full flex flex-col justify-between">
                        <div>
                            <div class="text-lavender/70 font-bold text-sm uppercase tracking-wider mb-2">Estimated Investment</div>
                            <div class="text-4xl md:text-5xl font-extrabold text-white mb-1 tracking-tighter flex items-center gap-1">
                                ₦<span id="calc-total" data-base="<?= (int)$cost_low ?>"><?= number_format($cost_low) ?></span>
                            </div>
                            <div id="calc-tier-label" class="font-mono text-[10px] text-lavender/40 uppercase tracking-widest mb-6">Entry Level</div>
                            <ul class="space-y-3 mb-8" id="active-features-list">
                                <li class="flex items-start gap-2 text-sm text-lavender/80">
                                    <i data-lucide="check-circle-2" class="w-4 h-4 text-sharp-purple mt-0.5 shrink-0"></i>
                                    <span>Base <?= go_safe_text($niche_name) ?> Setup</span>
                                </li>
                            </ul>
                        </div>
                        <a href="#" id="calc-whatsapp-btn" target="_blank"
                           class="w-full flex items-center justify-center gap-2 bg-sharp-purple text-white px-6 py-4 rounded-xl font-bold hover:scale-[1.02] hover:shadow-[0_0_20px_rgba(126,34,206,0.4)] transition-all font-manrope">
                            Discuss This Project <i data-lucide="arrow-right" class="w-4 h-4"></i>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<script>
document.addEventListener('DOMContentLoaded', function() {
    var nicheName   = "<?= addslashes(go_safe_text($niche_name)) ?>";
    var pageSource  = "Niche Hub — <?= addslashes(go_safe_text($niche_name)) ?> <?= addslashes(go_safe_text($service_name)) ?> in Nigeria";
    var phoneNumber = "2349061150443";
    var basePrice   = <?= (int)$cost_low ?>;
    var tierPrices  = { entry: <?= (int)$cost_low ?>, standard: <?= (int)$cost_typical ?>, premium: <?= (int)$cost_high ?> };
    var tierLabels  = { entry: 'Entry Level', standard: 'Standard Build', premium: 'Premium / Enterprise' };
    var currentTier = window.selectedTier || 'entry';

    var totalEl      = document.getElementById('calc-total');
    var tierLabelEl  = document.getElementById('calc-tier-label');
    var featuresEl   = document.getElementById('active-features-list');
    var waBtn        = document.getElementById('calc-whatsapp-btn');
    var customInput  = document.getElementById('calc-custom-needs');

    function animateValue(el, from, to, ms) {
        var start = null;
        function step(ts) {
            if (!start) start = ts;
            var p = Math.min((ts - start) / ms, 1);
            el.textContent = Math.floor(p * (to - from) + from).toLocaleString('en-US');
            if (p < 1) requestAnimationFrame(step);
            else el.textContent = to.toLocaleString('en-US');
        }
        requestAnimationFrame(step);
    }

    function switchTier(tier) {
        currentTier = tier;
        window.selectedTier = tier;

        // Update tab styles
        ['entry','standard','premium'].forEach(function(t) {
            var tab = document.getElementById('tab-' + t);
            if (t === tier) {
                tab.className = 'calc-tab flex-1 py-2 rounded-lg text-xs font-bold uppercase tracking-wider transition-all ' +
                    (t === 'entry' ? 'bg-code-green/20 text-code-green' : t === 'standard' ? 'bg-sharp-purple/20 text-sharp-purple' : 'bg-lavender/10 text-lavender');
            } else {
                tab.className = 'calc-tab flex-1 py-2 rounded-lg text-xs font-bold uppercase tracking-wider transition-all text-lavender/50 hover:text-white';
            }
        });

        // Show/hide feature rows and check/uncheck appropriately
        document.querySelectorAll('.calc-standard').forEach(function(el) {
            el.classList.toggle('hidden', tier === 'entry');
            var cb = el.querySelector('.calc-checkbox');
            if (cb) cb.checked = (tier === 'standard' || tier === 'premium');
            var icon = el.querySelector('[data-lucide="check"]');
            if (icon) icon.style.opacity = (tier !== 'entry') ? '1' : '0';
            if (tier !== 'entry') {
                el.classList.add('border-sharp-purple/50', 'bg-sharp-purple/10');
                el.classList.remove('border-white/5', 'bg-black/20');
            } else {
                el.classList.remove('border-sharp-purple/50', 'bg-sharp-purple/10');
                el.classList.add('border-white/5', 'bg-black/20');
            }
        });

        document.querySelectorAll('.calc-premium').forEach(function(el) {
            el.classList.toggle('hidden', tier !== 'premium');
            var cb = el.querySelector('.calc-checkbox');
            if (cb) cb.checked = (tier === 'premium');
            var icon = el.querySelector('[data-lucide="check"]');
            if (icon) icon.style.opacity = (tier === 'premium') ? '1' : '0';
            if (tier === 'premium') {
                el.classList.add('border-lavender/40', 'bg-lavender/5');
                el.classList.remove('border-lavender/10', 'bg-black/20');
            } else {
                el.classList.remove('border-lavender/40', 'bg-lavender/5');
            }
        });

        calculateTotal();
        if (typeof lucide !== 'undefined') lucide.createIcons();
    }

    function calculateTotal() {
        // Always start from base floor, then add each checked item's individual price
        var total = basePrice;
        var waFeatures = ['Base ' + nicheName + ' Setup'];
        var listHTML = '<li class="flex items-start gap-2 text-sm text-lavender/80"><i data-lucide="check-circle-2" class="w-4 h-4 text-sharp-purple mt-0.5 shrink-0"></i><span>Base ' + nicheName + ' Setup</span></li>';

        document.querySelectorAll('.calc-checkbox').forEach(function(cb) {
            if (cb.checked) {
                var itemPrice = parseInt(cb.getAttribute('data-price')) || 0;
                total += itemPrice;
                var name = cb.closest('label').querySelector('.feature-name');
                if (name) {
                    waFeatures.push(name.innerText + ' (+\u20A6' + itemPrice.toLocaleString('en-US') + ')');
                    listHTML += '<li class="flex items-start gap-2 text-sm text-lavender/80"><i data-lucide="check-circle-2" class="w-4 h-4 text-sharp-purple mt-0.5 shrink-0"></i><span>' + name.innerText + '</span></li>';
                }
            }
        });

        // Enforce floor — total can never drop below basePrice
        total = Math.max(total, basePrice);

        var prev = parseInt(totalEl.textContent.replace(/,/g, '')) || basePrice;
        animateValue(totalEl, prev, total, 400);
        tierLabelEl.textContent = tierLabels[currentTier];
        featuresEl.innerHTML = listHTML;
        if (typeof lucide !== 'undefined') lucide.createIcons();

        var custom = customInput ? customInput.value.trim() : '';
        var msg = 'Hi GetOnline Studio,\n\nI\'m reaching out from your ' + pageSource + ' page.\n\nI am looking to build a ' + nicheName + ' website.\n\nSelected tier: ' + tierLabels[currentTier] + '\nEstimated budget: \u20A6' + total.toLocaleString('en-US') + '\n\nSelected features:\n- ' + waFeatures.join('\n- ');
        if (custom) msg += '\n\nAdditional requirements:\n' + custom;
        msg += '\n\nLet\'s discuss getting this started.';
        if (waBtn) waBtn.setAttribute('href', 'https://wa.me/' + phoneNumber + '?text=' + encodeURIComponent(msg));
    }

    // Manual checkbox toggles (when user is on standard/premium tier and unchecks/checks individual items)
    document.querySelectorAll('.calc-checkbox').forEach(function(cb) {
        cb.addEventListener('change', function() {
            var icon = cb.nextElementSibling;
            if (icon) icon.style.opacity = cb.checked ? '1' : '0';
            var lbl = cb.closest('label');
            if (lbl) {
                if (cb.checked) {
                    lbl.classList.add('border-sharp-purple/50', 'bg-sharp-purple/10');
                    lbl.classList.remove('border-white/5', 'bg-black/20');
                } else {
                    lbl.classList.remove('border-sharp-purple/50', 'bg-sharp-purple/10');
                    lbl.classList.add('border-white/5', 'bg-black/20');
                }
            }
            calculateTotal();
        });
    });

    if (customInput) customInput.addEventListener('input', calculateTotal);

    // Apply tier from pricing card click
    switchTier(currentTier);

    // Expose for tier buttons above
    window.switchTier = switchTier;
});
</script>

    <!-- SELECTED WORK -->
    <section class="py-24 md:py-32 px-4 md:px-6 bg-matte-black border-y border-lavender/10">
        <div class="max-w-7xl mx-auto">
            <div class="flex flex-col md:flex-row justify-between items-end mb-16 gap-6">
                <div>
                    <h2 class="font-syne text-3xl md:text-5xl font-bold mb-4">Enterprise Platforms</h2>
                    <p class="font-manrope text-lavender/60 text-lg">Case studies of scale and performance.</p>
                </div>
                <a href="/work" class="inline-flex items-center gap-2 text-sharp-purple font-bold tracking-widest uppercase hover:text-white transition-colors hover-target border-b border-sharp-purple pb-1">
                    View All Projects <i data-lucide="arrow-right" class="w-4 h-4"></i>
                </a>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-12 md:gap-20">
                <?php foreach ($display_projects as $index => $project): ?>
                <a href="<?= esc_url($project['url']) ?>" class="group block hover-target <?= $index > 0 ? 'md:mt-24' : '' ?> reveal-up">
                    <div class="relative w-full aspect-[4/3] rounded-2xl overflow-hidden mb-6 bg-card-dark border border-lavender/10">
                        <div class="absolute inset-0 bg-cover bg-top project-img filter grayscale opacity-80" style="background-image: url('<?= esc_url($project['img']) ?>');"></div>
                    </div>
                    <div class="flex flex-col xl:flex-row xl:justify-between xl:items-start gap-4">
                        <div class="flex-1">
                            <h3 class="font-syne text-2xl md:text-3xl font-bold mb-3 group-hover:text-sharp-purple transition-colors"><?= go_safe_text($project['name']) ?></h3>
                            <p class="font-manrope text-lavender/60 text-sm leading-relaxed max-w-md"><?= go_safe_text($project['desc']) ?></p>
                        </div>
                    </div>
                </a>
                <?php endforeach; ?>
            </div>
        </div>
    </section>

    <!-- TESTIMONIALS -->
    <section class="py-24 md:py-32 px-4 md:px-6 bg-card-dark border-t border-lavender/10 relative z-10">
        <div class="max-w-7xl mx-auto">
            <div class="text-center mb-12 md:mb-16">
                <p class="font-mono text-[10px] text-sharp-purple tracking-widest uppercase mb-4">[ Client Results ]</p>
                <h2 class="font-syne text-3xl md:text-4xl font-bold text-white mb-4">Trusted by <?= go_safe_text($niche_plural) ?> Across Nigeria</h2>
                <p class="font-manrope text-lavender/60 text-lg">From churches to fintechs, from Osogbo to international organisations.</p>
            </div>

            <?php
            $hub_testimonials = [
                ['quote' => "It's not always easy working with a team when you aren't in the same location, but our experience with GetOnline Studio was top-notch. Faith made the process simple. He doesn't just build pages; he builds solutions. He turned our concept into a working prototype quickly and integrated AI features that are already helping our business. I can't recommend them enough.", 'name' => 'Olayinka Itunu Damilare', 'role' => 'CEO, De Kompany Consulting Services', 'location' => 'Ilorin, Kwara State', 'initials' => 'OI', 'color' => '#0f6e56', 'tag' => 'Business Consulting'],
                ['quote' => "GetOnline Studio developed a website and mobile app for us, and we couldn't be happier with the result. They made the whole process easy and delivered exactly what we needed. Truly the best web designer in Osogbo, Osun State.", 'name' => 'Mr. Mike', 'role' => 'Technical Director, RaffleKings', 'location' => 'Nigeria', 'initials' => 'MK', 'color' => '#185fa5', 'tag' => 'Web & Mobile App'],
                ['quote' => "As an international organization, the World Institute for Peace required a digital presence that reflected our authority and mission. GetOnline Studio proved to be a highly trusted partner. The speed of delivery did not compromise quality. We highly recommend them for any institution seeking world-class web development.", 'name' => 'Amb. Dr. Jernail Singh Anand', 'role' => 'World Foundation for Peace', 'location' => 'India', 'initials' => 'JA', 'color' => '#7e22ce', 'tag' => 'International Organisation'],
                ['quote' => "GetOnline Studio delivered more than just a system. They transformed how we operate. They built a custom online academy where students learn at their own pace, engage with lecturers, track progress, and connect with peers.", 'name' => 'Emmanuel Amaechi', 'role' => 'Academic Director, Peace Academy', 'location' => 'Enugu Branch', 'initials' => 'EA', 'color' => '#3b6d11', 'tag' => 'Education & Academy'],
                ['quote' => "I proudly commend GetOnline Studio for their exceptional quality and reliable services. Their professionalism, creativity, and attention to detail consistently set them apart. Their ability to understand clients' needs and translate them into functional, elegant digital solutions is truly admirable.", 'name' => 'Chief Lamina Kamiludeen Omotoyosi', 'role' => 'Executive Director, World Institute for Peace (WIP)', 'location' => 'Abuja', 'initials' => 'LK', 'color' => '#854f0b', 'tag' => 'Leadership & Governance'],
                ['quote' => "Working with GetOnline Studio on our microfinance app was an excellent experience. He demonstrated a high level of professionalism and a clear understanding of our business needs. He consistently delivered beyond expectations. I am genuinely pleased with the outcome and highly recommend his services.", 'name' => 'Tobiloba Babalola', 'role' => 'Managing Director (Operations & Compliance)', 'location' => 'OA Global Standard Services, Osogbo', 'initials' => 'TB', 'color' => '#993556', 'tag' => 'Fintech & Microfinance'],
                ['quote' => "I needed help designing a website for our church so I was referred to them. They really met our expectations and our church vision. Good work.", 'name' => 'Mr. Ogundeji Sinmisola', 'role' => 'Church Administrator', 'location' => 'Ogbomosho, Oyo State', 'initials' => 'OS', 'color' => '#7e22ce', 'tag' => 'Church & Non-profit'],
            ];
            ?>
            <div class="flex md:block overflow-x-auto md:overflow-visible snap-x snap-mandatory hide-scrollbar gap-4 pb-8 md:pb-0 md:columns-2 xl:columns-3 md:space-y-6">
                <?php foreach ($hub_testimonials as $t): ?>
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
                <p class="font-manrope text-lavender/50 text-sm mb-6">Join these businesses across Nigeria. Send us a message and let's talk about your project.</p>
                <button onclick="openWaWidgetWithService('Website Design')" class="inline-flex items-center gap-3 bg-sharp-purple text-white px-10 py-4 rounded-full text-sm font-bold uppercase tracking-widest hover:bg-white hover:text-matte-black transition-all hover-target shadow-[0_0_30px_rgba(126,34,206,0.3)] focus:outline-none">
                    <i data-lucide="message-circle" class="w-4 h-4"></i> Start Your Project
                </button>
            </div>
        </div>
    </section>

    <!-- NEW: NATIONWIDE PROCESS MODULE -->
    <section class="py-24 md:py-32 px-4 md:px-6 bg-[#0B0A0F] border-b border-lavender/10 relative z-10">
        <div class="max-w-7xl mx-auto">
            <div class="text-center max-w-3xl mx-auto mb-16 md:mb-24">
                <p class="font-mono text-[10px] text-sharp-purple tracking-widest uppercase mb-4">[ HOW WE WORK NATIONWIDE ]</p>
                <h2 class="font-syne text-3xl md:text-5xl font-bold mb-6 text-white">Seamless Collaboration, Anywhere in Nigeria.</h2>
                <p class="font-manrope text-lavender/60 text-lg">You don't need to be in the same room as us to get a world-class platform. Our remote-first process is designed for flawless execution whether you are in Lagos, Abuja, or anywhere else.</p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-8 relative">
                <!-- Connecting Line (Desktop Only) -->
                <div class="hidden md:block absolute top-12 left-[15%] right-[15%] h-[1px] bg-gradient-to-r from-transparent via-sharp-purple/50 to-transparent"></div>

                <div class="relative text-center p-6 tilt-card hover-target" onmousemove="tiltCard(event, this)" onmouseleave="resetTilt(this)">
                    <div class="w-24 h-24 mx-auto rounded-full bg-matte-black border border-sharp-purple/30 flex items-center justify-center mb-6 relative z-10 shadow-[0_0_30px_rgba(126,34,206,0.15)]">
                        <span class="font-syne text-2xl font-bold text-sharp-purple">01</span>
                    </div>
                    <h3 class="font-syne text-xl font-bold text-white mb-3">Start a Conversation</h3>
                    <p class="font-manrope text-lavender/60 text-sm leading-relaxed">We start with a quick conversation — WhatsApp chat, email, or a call, whatever works for you — to understand your <?= go_safe_text($niche_name) ?>'s goals, target audience, and what's holding you back online.</p>
                </div>

                <div class="relative text-center p-6 tilt-card hover-target" onmousemove="tiltCard(event, this)" onmouseleave="resetTilt(this)">
                    <div class="w-24 h-24 mx-auto rounded-full bg-matte-black border border-sharp-purple/30 flex items-center justify-center mb-6 relative z-10 shadow-[0_0_30px_rgba(126,34,206,0.15)]">
                        <span class="font-syne text-2xl font-bold text-sharp-purple">02</span>
                    </div>
                    <h3 class="font-syne text-xl font-bold text-white mb-3">Architecture & Design</h3>
                    <p class="font-manrope text-lavender/60 text-sm leading-relaxed">Our team engineers a conversion-optimized layout. We share secure staging links with you for feedback, ensuring you have total visibility throughout the build.</p>
                </div>

                <div class="relative text-center p-6 tilt-card hover-target" onmousemove="tiltCard(event, this)" onmouseleave="resetTilt(this)">
                    <div class="w-24 h-24 mx-auto rounded-full bg-matte-black border border-sharp-purple/30 flex items-center justify-center mb-6 relative z-10 shadow-[0_0_30px_rgba(126,34,206,0.15)]">
                        <span class="font-syne text-2xl font-bold text-sharp-purple">03</span>
                    </div>
                    <h3 class="font-syne text-xl font-bold text-white mb-3">Launch & Dominate</h3>
                    <p class="font-manrope text-lavender/60 text-sm leading-relaxed">We push your new platform live, optimize it for Nigerian local search, integrate your payment gateways, and set up your automations to capture leads instantly.</p>
                </div>
            </div>

            <div class="mt-16 text-center">
                <p class="font-manrope text-lavender/50 text-sm mb-6">Ready to get started? Send us a message — WhatsApp, email, or a call, whichever works for you. No pressure.</p>
                <button onclick="openWaWidgetWithService('Website Design')" class="inline-flex items-center gap-3 bg-sharp-purple text-white px-10 py-4 rounded-full text-sm font-bold uppercase tracking-widest hover:bg-white hover:text-matte-black transition-all hover-target shadow-[0_0_30px_rgba(126,34,206,0.3)] focus:outline-none">
                    <i data-lucide="message-circle" class="w-4 h-4"></i> Let's Start a Conversation
                </button>
            </div>
        </div>
    </section>

    <!-- COMMON MISTAKES — NICHE-AWARE, DYNAMIC -->
    <section class="py-20 md:py-28 px-4 md:px-6 bg-[#0d0d0d] border-t border-lavender/5 relative z-10">
        <div class="max-w-5xl mx-auto">
            <div class="text-center max-w-3xl mx-auto mb-14">
                <p class="font-mono text-[10px] text-sharp-purple tracking-widest uppercase mb-4">[ What to Avoid ]</p>
                <h2 class="font-syne text-3xl md:text-4xl font-bold text-white mb-4">
                    <?= go_spin_text("{Common Mistakes|Critical Errors|Costly Mistakes} " . go_safe_text($niche_plural) . " Make {With Their Websites|Before Hiring a Web Designer|When Going Digital}") ?>
                </h2>
                <p class="font-manrope text-lavender/60 text-base leading-relaxed">
                    <?= go_spin_text("After {working with|building platforms for|delivering projects for} {hundreds of|over 200|countless} " . strtolower(go_safe_text($niche_plural)) . " across Nigeria, these are the {patterns|mistakes|problems} we see most often.") ?>
                </p>
            </div>
            <div class="space-y-5">
                <?php
                $niche_mistakes = [
                    [
                        'number' => '01',
                        'title'  => go_spin_text("Choosing the {Cheapest|Lowest Bid} Instead of the {Right Fit|Best Value}"),
                        'desc'   => go_spin_text("Many " . strtolower(go_safe_text($niche_plural)) . " {hire based on price alone|go for the cheapest quote} and end up with a website that {breaks within months|never ranks on Google|looks outdated on launch day}. A cheap website {costs more to fix|needs to be rebuilt} than a quality one done right the first time."),
                    ],
                    [
                        'number' => '02',
                        'title'  => go_spin_text("Relying {Entirely|Solely} on Social Media {Instead of|Rather Than} a Proper Website"),
                        'desc'   => go_spin_text("Social media {is rented land|is borrowed space}. Algorithm changes or account bans can {wipe out your entire presence overnight|make you invisible instantly}. " . go_safe_text($niche_plural) . " that own their website {control their own visibility|are never at the mercy of a platform}."),
                    ],
                    [
                        'number' => '03',
                        'title'  => go_spin_text("{Launching|Going Live|Building a Website} With No {SEO Plan|SEO Strategy|Search Optimisation}"),
                        'desc'   => go_spin_text("A website with no SEO is {invisible to Google|a billboard in the middle of a forest}. {This is the most common issue we fix for " . strtolower(go_safe_text($niche_plural)) . ".|We see this constantly in the " . strtolower(go_safe_text($niche_name)) . " space.} Your site needs to be {optimised from day one|built with SEO architecture} — not treated as an afterthought."),
                    ],
                    [
                        'number' => '04',
                        'title'  => go_spin_text("{Using a Template|Using a Generic Theme} and Calling It {Done|a Professional Website}"),
                        'desc'   => go_spin_text("Template websites look the same as {thousands of competitors|every other " . strtolower(go_safe_text($niche_name)) . " in Nigeria}. In a competitive market, a {custom-built|professionally designed} website is what {separates you from amateurs|signals you are a serious " . strtolower(go_safe_text($niche_name)) . "}."),
                    ],
                    [
                        'number' => '05',
                        'title'  => go_spin_text("{Ignoring|Neglecting} Mobile {Performance|Optimisation}"),
                        'desc'   => go_spin_text("Over 80% of web searches in Nigeria happen on mobile. A website that {loads slowly|looks broken} on a phone {loses those visitors immediately|means lost " . ($is_non_profit ? "supporters" : "revenue") . " every single day}. {Every platform we build is mobile-first by default.|Mobile performance is non-negotiable in our builds.}"),
                    ],
                ];
                foreach ($niche_mistakes as $mistake): ?>
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

    <!-- FAQS -->
    <section class="py-24 md:py-32 px-4 md:px-6 bg-[#0a0a0a] border-b border-lavender/10 relative z-10">
        <div class="max-w-3xl mx-auto">
            <h2 class="font-syne text-3xl md:text-5xl font-bold mb-12 text-center"><?= go_spin_text("{Questions to ask a {$exact_keyword}.|Frequently Asked Questions|What {$niche_plural} Ask Us|Common Questions About {$niche_name} Websites}") ?></h2>
            <div class="space-y-4">
                <?php foreach ($faq_order as $i): if (!empty($meta["faq{$i}_q"])): ?>
                <div class="faq-item bg-matte-black border border-lavender/10 rounded-2xl overflow-hidden hover-target">
                    <button class="w-full text-left px-6 md:px-8 py-6 flex justify-between items-center focus:outline-none" onclick="this.parentElement.classList.toggle('active')">
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

            <!-- FAQ bottom CTA bridge -->
            <div class="mt-16 bg-[#0d0d14] border border-lavender/10 rounded-2xl p-8 flex flex-col sm:flex-row items-center justify-between gap-6">
                <div>
                    <p class="font-syne text-lg font-bold text-white mb-1">Still have questions?</p>
                    <p class="font-manrope text-lavender/60 text-sm">Our team answers every message. WhatsApp us and we will respond within the hour.</p>
                </div>
                <button onclick="openWaWidgetWithService('Website Design')" class="flex-shrink-0 inline-flex items-center gap-3 bg-[#25D366] text-white font-bold text-sm px-8 py-4 rounded-full hover:bg-[#1ebe5d] transition-all hover-target shadow-xl focus:outline-none whitespace-nowrap">
                    <i data-lucide="message-circle" class="w-4 h-4"></i> Ask Us — WhatsApp, Email or Call
                </button>
            </div>
        </div>
    </section>

    <!-- FINAL CTA -->
    <section id="consultation" class="py-32 md:py-40 px-4 md:px-6 text-center bg-sharp-purple text-white relative z-20 overflow-hidden">
        <div class="absolute inset-0 bg-[url('data:image/svg+xml,%3Csvg viewBox=%220 0 200 200%22 xmlns=%22http://www.w3.org/2000/svg%22%3E%3Cfilter id=%22noiseFilter%22%3E%3CfeTurbulence type=%22fractalNoise%22 baseFrequency=%220.65%22 numOctaves=%223%22 stitchTiles=%22stitch%22/%3E%3C/filter%3E%3Crect width=%22100%25%22 height=%22100%25%22 filter=%22url(%23noiseFilter)%22 opacity=%220.08%22/%3E%3C/svg%3E')] mix-blend-overlay"></div>
        <div class="max-w-4xl mx-auto reveal-up relative z-10">
            <h2 class="font-syne text-4xl md:text-7xl font-bold mb-4 text-white leading-tight">
                Ready to <?= $vocab['cta_action'] ?>?<br>
                Let's make it happen.
            </h2>
            <p class="font-manrope text-lg md:text-xl mb-12 text-white/90 leading-relaxed max-w-2xl mx-auto">
                Every day without a great website is <?= $vocab['daily_loss'] ?>. Click your city above or message us directly to start your project today.
            </p>
            <div class="flex flex-col sm:flex-row items-center justify-center gap-4">
                <a href="#locations" class="w-full sm:w-auto bg-matte-black text-white px-10 py-5 rounded-full text-sm font-bold uppercase tracking-widest hover:bg-white hover:text-matte-black transition-all hover-target shadow-2xl">
                    Select Your City &rarr;
                </a>
                <?php $wa_message = rawurlencode("Hi GetOnline Studio,\n\nI found your {$niche_name} Digital Agency page on your Nigeria hub.\n\nI have a {$niche_name} and I am looking for a digital agency. Let's talk!"); ?>
                <a href="https://wa.me/2349061150443?text=<?= $wa_message ?>" target="_blank" class="w-full sm:w-auto bg-green-500 text-white px-10 py-5 rounded-full text-sm font-bold uppercase tracking-widest hover:bg-green-400 transition-all hover-target shadow-xl flex items-center justify-center gap-2">
                    <i data-lucide="message-circle" class="w-5 h-5"></i> Chat on WhatsApp
                </a>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer class="pt-20 md:pt-28 pb-10 px-4 md:px-6 bg-matte-black border-t border-lavender/20 relative z-20">
        <div class="max-w-7xl mx-auto">

            <!-- Top: Big CTA + About -->
            <div class="flex flex-col lg:flex-row justify-between gap-12 lg:gap-20 pb-16 border-b border-lavender/10">
                <div class="lg:w-1/2">
                    <h2 class="font-syne text-5xl md:text-7xl lg:text-8xl mb-4 text-lavender leading-none">LET'S<br>TALK.</h2>
                    <p class="font-manrope text-lavender/60 text-base mb-6 max-w-sm leading-relaxed">
                        Ready to get a professional <?= go_safe_text($niche_name) ?> website? Our team is available right now.
                    </p>
                    <div class="flex flex-col sm:flex-row gap-4">
                        <button onclick="openWaWidgetWithService('Website Design')" class="inline-flex items-center gap-2 bg-[#25D366] text-white font-bold text-sm px-6 py-3 rounded-full hover:bg-[#1ebe5d] transition-all hover-target focus:outline-none">
                            <i data-lucide="message-circle" class="w-4 h-4"></i> +234 906 115 0443
                        </button>
                        <a href="mailto:hello@getonlinestudio.com?subject=<?= rawurlencode("{$niche_name} Web Design Project") ?>" class="inline-flex items-center gap-2 text-sharp-purple font-manrope text-sm font-bold hover:text-white transition-colors hover-target border border-sharp-purple/30 px-6 py-3 rounded-full hover:border-white/30">
                            <i data-lucide="mail" class="w-4 h-4"></i> hello@getonlinestudio.com
                        </a>
                    </div>
                </div>
                <div class="lg:w-1/2 lg:pt-4">
                    <p class="font-mono text-[10px] text-sharp-purple tracking-widest uppercase mb-4">[ Who We Are ]</p>
                    <h3 class="font-syne text-2xl font-bold text-white mb-4">GetOnline Studio</h3>
                    <p class="font-manrope text-lavender/60 text-sm leading-relaxed mb-4">
                        GetOnline Studio is a Nigerian web design company and digital agency specialising in industry-specific websites. Our web designers and web developers build professional platforms for <?= go_safe_text($niche_plural) ?> and hundreds of other industries across Nigeria.
                    </p>
                    <p class="font-manrope text-lavender/60 text-sm leading-relaxed">
                        Whether you are looking for a <strong class="text-white/70"><?= go_safe_text($niche_name) ?> web designer</strong>, a <strong class="text-white/70"><?= go_safe_text($niche_name) ?> web developer</strong>, or a full-service <strong class="text-white/70"><?= go_safe_text($niche_name) ?> web design company</strong> in Nigeria, you have found us.
                    </p>
                </div>
            </div>

            <!-- Middle: Nav Links -->
            <div class="py-12 grid grid-cols-2 md:grid-cols-4 gap-8 border-b border-lavender/10">
                <div>
                    <p class="font-mono text-[10px] text-lavender/30 tracking-widest uppercase mb-5">Company</p>
                    <ul class="space-y-3 font-manrope text-sm">
                        <li><a href="/" class="text-lavender/60 hover:text-white transition-colors hover-target">Home</a></li>
                        <li><a href="/about/" class="text-lavender/60 hover:text-white transition-colors hover-target">About Us</a></li>
                        <li><a href="/work/" class="text-lavender/60 hover:text-white transition-colors hover-target">Projects</a></li>
                        <li><a href="/testimonials/" class="text-lavender/60 hover:text-white transition-colors hover-target">Testimonials</a></li>
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
                    <p class="font-mono text-[10px] text-lavender/30 tracking-widest uppercase mb-5">Key Cities</p>
                    <ul class="space-y-3 font-manrope text-sm">
                        <li><a href="/locations/lagos/<?= esc_attr($niche_slug) ?>-<?= esc_attr($service_slug) ?>/" class="text-lavender/60 hover:text-white transition-colors hover-target"><?= go_safe_text($niche_name) ?>, Lagos</a></li>
                        <li><a href="/locations/abuja/<?= esc_attr($niche_slug) ?>-<?= esc_attr($service_slug) ?>/" class="text-lavender/60 hover:text-white transition-colors hover-target"><?= go_safe_text($niche_name) ?>, Abuja</a></li>
                        <li><a href="/locations/port-harcourt/<?= esc_attr($niche_slug) ?>-<?= esc_attr($service_slug) ?>/" class="text-lavender/60 hover:text-white transition-colors hover-target"><?= go_safe_text($niche_name) ?>, Port Harcourt</a></li>
                        <li><a href="/locations/" class="text-lavender/60 hover:text-sharp-purple transition-colors hover-target font-bold">All Locations &rarr;</a></li>
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
                <p>GetOnline Studio &copy; <?= date('Y') ?> &mdash; <?= go_safe_text($niche_name) ?> Web Design Company in Nigeria.</p>
                <p class="font-mono uppercase tracking-widest"><?= $vocab['footer_tagline'] ?></p>
            </div>

        </div>
    </footer>

    <script>
        // WhatsApp Widget Toggle
        let waOpen = false;
        function toggleWaWidget() {
            waOpen = !waOpen;
            const win = document.getElementById('wa-float-window');
            if (waOpen) {
                win.classList.remove('widget-hidden');
                win.style.display = 'flex';
            } else {
                win.style.display = 'none';
                win.classList.add('widget-hidden');
            }
        }

        function sendWaWidget() {
            const checked = [...document.querySelectorAll('.wa-service-cb:checked')].map(cb => cb.value);
            const services = checked.length ? checked.join(', ') : 'website design';
            const niche = '<?= addslashes(go_safe_text($niche_name)) ?>';
            const msg = encodeURIComponent(`Hi GetOnline Studio,\n\nI'm reaching out from your ${niche} Digital Agency page (Nigeria hub).\n\nI have a ${niche} and I'm interested in: ${services}.\n\nLet's talk!`);
            window.open(`https://wa.me/2349061150443?text=${msg}`, '_blank');
        }

        function openWaWidgetWithService(serviceName) {
            const win = document.getElementById('wa-float-window');
            win.classList.remove('widget-hidden');
            win.style.display = 'flex';
            waOpen = true;
            document.querySelectorAll('.wa-service-cb').forEach(cb => {
                cb.checked = (cb.value === serviceName) || serviceName.includes(cb.value) || cb.value.includes(serviceName);
            });
            setTimeout(() => window.scrollTo({ top: document.body.scrollHeight, behavior: 'smooth' }), 80);
        }

        lucide.createIcons();

        // 3D Tilt Logic
        function tiltCard(event, card) {
            const r = card.getBoundingClientRect();
            const x = event.clientX - r.left, y = event.clientY - r.top;
            const cx = r.width / 2, cy = r.height / 2;
            card.style.transform = `perspective(1000px) rotateX(${((y-cy)/cy)*-10}deg) rotateY(${((x-cx)/cx)*10}deg) scale(1.05)`;
        }
        function resetTilt(card) { card.style.transform = `perspective(1000px) rotateX(0deg) rotateY(0deg) scale(1)`; }

        // Cursor Logic
        const cursorDot = document.querySelector('.cursor-dot');
        const cursorOutline = document.querySelector('.cursor-outline');
        const isTouchDevice = 'ontouchstart' in window || navigator.maxTouchPoints > 0;

        if (!isTouchDevice && cursorDot && cursorOutline) {
            let mouseX = 0, mouseY = 0, outlineX = 0, outlineY = 0;
            window.addEventListener('mousemove', (e) => {
                mouseX = e.clientX; mouseY = e.clientY;
                cursorDot.style.transform = `translate(${mouseX}px, ${mouseY}px) translate(-50%, -50%)`;
            });
            const animateCursor = () => {
                outlineX += (mouseX - outlineX) * 0.15; outlineY += (mouseY - outlineY) * 0.15;
                cursorOutline.style.transform = `translate(${outlineX}px, ${outlineY}px) translate(-50%, -50%)`;
                requestAnimationFrame(animateCursor);
            };
            animateCursor();
            const addHoverTargets = () => {
                document.querySelectorAll('.hover-target, a, button, label').forEach(el => {
                    if(!el.hasAttribute('data-cursor-bound')) {
                        el.addEventListener('mouseenter', () => document.body.classList.add('hovering'));
                        el.addEventListener('mouseleave', () => document.body.classList.remove('hovering'));
                        el.setAttribute('data-cursor-bound', 'true');
                    }
                });
            }
            addHoverTargets();
            document.addEventListener('click', () => setTimeout(addHoverTargets, 100));
        }
    </script>
<?php wp_footer(); ?>
</body>
</html>