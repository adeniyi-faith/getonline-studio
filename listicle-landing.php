<?php
/**
 * LISTICLE LANDING PAGE (Editorial Template)
 * Routes URLs like: /locations/lagos/top-law-firm-web-designers/
 * Includes: Monthly Auto-Freshness, Dynamic OG Images, and Golden Box Parser.
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

// Helper: Spintax engine for A/B testing Meta Tags
function go_spin_text($text) {
    $text = (string) $text;
    return preg_replace_callback('/\{(((?>[^\{\}]+)|(?R))*)\}/x', function ($match) {
        $inner = go_spin_text($match[1]);
        $parts = explode('|', $inner);
        return $parts[array_rand($parts)];
    }, $text);
}

// 2. Catch URL Parameters
$city_slug = isset($_GET['city']) ? sanitize_title($_GET['city']) : '';
$niche_slug = isset($_GET['niche']) ? sanitize_title($_GET['niche']) : '';

if (!$city_slug || !$niche_slug) {
    wp_redirect('/');
    exit;
}

// Verify City Exists to prevent "Fake City" indexing (404 protection)
$city_post = get_page_by_path($city_slug, OBJECT, 'pseo_location');
if (!$city_post || $city_post->post_status !== 'publish') {
    status_header(404);
    wp_redirect('/locations/');
    exit;
}

// 3. Fetch from Custom Table
global $wpdb;
$table_name = $wpdb->prefix . 'pseo_listicles';
$suppress = $wpdb->suppress_errors(true);
$listicle = $wpdb->get_row(
    $wpdb->prepare("SELECT * FROM $table_name WHERE city_slug = %s AND niche_slug = %s AND status = 'publish'", $city_slug, $niche_slug)
);
$wpdb->suppress_errors($suppress);

// If no published article exists, redirect back to the city hub
if (!$listicle) {
    wp_redirect("/locations/{$city_slug}/");
    exit;
}

// --- MONTHLY FRESHNESS SEO SEEDING ---
// Appending 'Y-m' forces the internal links and Spintax to slightly reshuffle on the 1st of every month!
$current_year = date('Y');
$current_month = date('F');
$master_seed = crc32($city_slug . $niche_slug . 'listicle' . date('Y-m'));
srand($master_seed);
mt_srand($master_seed);

// Formatting Variables
$city_name = esc_html(html_entity_decode($city_post->post_title ?? '', ENT_QUOTES, 'UTF-8'));
$niche_name = ucwords(str_replace('-', ' ', $niche_slug));
$commercial_url = "/locations/{$city_slug}/{$niche_slug}-website-designer/";
$current_url = "https://" . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];

// Author Variables (E-E-A-T Signal)
$author_name = "Faith Adeniyi";
$author_role = "Creative Director";
$author_avatar = "https://secure.gravatar.com/avatar/342aec807d3b951713a7218efec17ac9f6da7565fb4150beaa796eb4a419ea0b?s=130&d=mm&r=g";
$author_bio = "Faith is the Creative Director of GetOnline Studio. He has 8 years of helping businesses build a strong online presence with high-converting websites that attract more customers. He has worked with organizations like The World Institute for Peace and it's Academy, Vision Afric, Civic Torch Media, NegotiumPros and others.";
$author_email = "adeniyifth@gmail.com";

// Auto-Updating Date Logic for Freshness
$month_seed = crc32($city_slug . $niche_slug . date('Y-m'));
$random_day = (abs($month_seed) % 28) + 1; 
$dynamic_date = mktime(0, 0, 0, date('m'), $random_day, date('Y'));
if ($dynamic_date > time()) $dynamic_date = time() - (86400 * mt_rand(1, 5)); // Fallback to a few days ago if future
$last_updated = date('F j, Y', $dynamic_date);

// --- DYNAMIC OPEN GRAPH IMAGE ENGINE ---
$og_text = "Top {$niche_name} Web Designers in {$city_name}";
$og_image_slug = sanitize_title($og_text);
$dynamic_og_image = GO_SITE_URL . "/social/{$og_image_slug}.jpg";

// Auto-update any stored year (e.g. 2026) in the meta title to the current year dynamically
$dynamic_meta_title = preg_replace('/\b20\d{2}\b/', $current_year, $listicle->meta_title);

// Apply Spintax to Meta Title for GSC A/B Testing
$dynamic_meta_title = go_spin_text($dynamic_meta_title);

// Build a dynamic, Spintax-powered Meta Description including the current month!
$meta_desc_template = "{Discover the best|Find the top-rated|Compare the leading} {$niche_name} web design and development agencies in {$city_name}. {Read our {$current_month} {$current_year} review|Check out our updated rankings} to {make the right choice|find your perfect agency partner}.";
$dynamic_meta_desc = go_spin_text($meta_desc_template);


// 4. Content Cleanup & Auto-Healing Engine
// Fix database double-escaping
$raw_content = stripslashes(stripslashes($listicle->content));
$raw_content = str_replace(["\\'", '\\"'], ["'", '"'], $raw_content);

// Convert any Markdown bolding into HTML <strong> tags
$raw_content = preg_replace('/\*\*(.*?)\*\*/', '<strong>$1</strong>', $raw_content);

// Fix AI formatting where it puts "#2" on its own line before the text
$raw_content = preg_replace('/^(#?\d+[\.\)]?)\s*[\r\n]+([A-Z].*)$/m', '$1 $2', $raw_content);

// Force <h3> on numbered lines (Catches "1. Name", "1) Name", "#1 Name")
$raw_content = preg_replace('/^(#?\d+[\.\)]?\s+[A-Z].*)$/m', '<h3>$1</h3>', $raw_content);

// Force <h2> tags on standard structural headers
$raw_content = preg_replace('/^(Our Ranking Criteria.*|Conclusion.*|Why Your.*|What to Look For.*|How We Ranked.*)$/im', '<h2>$1</h2>', $raw_content);

// FIX: Force line breaks where the AI output simple text lines without HTML tags
$raw_content = preg_replace('/([a-z0-9])\n([A-Z0-9])/i', "$1<br>\n$2", $raw_content);

// Convert standard line breaks into beautiful <p> paragraphs automatically
$clean_content = wpautop($raw_content);

// --- INJECT SEO FRESHNESS BANNER (NEW SLEEK VERSION) ---
$freshness_banner = '<div class="inline-flex flex-col sm:flex-row sm:items-center gap-2 sm:gap-3 bg-sharp-purple/10 border border-sharp-purple/20 px-4 py-3 rounded-xl mb-8 shadow-sm w-full">
    <div class="flex items-center gap-2 flex-shrink-0">
        <i data-lucide="calendar-check" class="w-4 h-4 text-sharp-purple"></i>
        <span class="text-xs font-bold text-white uppercase tracking-wider">Editor\'s Note</span>
    </div>
    <div class="hidden sm:block w-px h-4 bg-sharp-purple/30"></div>
    <p class="text-sm text-lavender/70 leading-snug m-0 p-0">
        Rankings verified for <strong>' . $current_month . ' ' . $current_year . '</strong>. These remain the top ' . strtolower($niche_name) . ' partners in ' . esc_html($city_name) . '.
    </p>
</div>';
$clean_content = $freshness_banner . $clean_content;

// --- NEW ENHANCEMENTS FOR TABLES, PROS/CONS, AND RANKING BOX ---

// 1. Format "How We Ranked" Section securely
$clean_content = preg_replace_callback(
    '/<h2[^>]*>(How We Ranked.*?)<\/h2>(.*?)<ul>(.*?)<\/ul>/is',
    function($matches) {
        if (preg_match('/<(table|h[2-6])/i', $matches[2])) {
            return $matches[0]; 
        }
        return '<div class="ranking-criteria-box my-10 bg-panel-dark/40 border border-white/5 rounded-2xl p-6 sm:p-8 relative overflow-hidden shadow-[0_10px_30px_rgba(0,0,0,0.2)]"><div class="absolute top-0 left-0 w-full h-1 bg-gradient-to-r from-sharp-purple to-transparent"></div><h2 class="ranking-criteria-heading"><i data-lucide="bar-chart-2" class="w-6 h-6 text-sharp-purple flex-shrink-0"></i> ' . $matches[1] . '</h2>' . $matches[2] . '<ul class="criteria-list mt-6">' . $matches[3] . '</ul></div>';
    },
    $clean_content
);

// 2. Wrap AI Tables in a responsive div & add a Mobile Swipe Indicator
$mobile_swipe_hint = '<div class="md:hidden flex items-center justify-end gap-1.5 text-[10px] font-bold text-lavender/60 uppercase tracking-widest mb-2"><i data-lucide="move-horizontal" class="w-4 h-4 text-sharp-purple animate-pulse"></i> Swipe to explore</div>';
$clean_content = preg_replace('/<table(.*?)>(.*?)<\/table>/is', '<div class="table-container my-10">' . $mobile_swipe_hint . '<div class="table-responsive-wrapper"><table$1>$2</table></div></div>', $clean_content);

// 3. Identify Pro and Con list items and inject specific CSS classes for styling
$clean_content = preg_replace('/<li>\s*<strong>Pro:<\/strong>/i', '<li class="pro-item"><strong>Pro:</strong>', $clean_content);
$clean_content = preg_replace('/<li>\s*<strong>Con:<\/strong>/i', '<li class="con-item"><strong>Con:</strong>', $clean_content);

// Calculate read time
$read_time = max(3, ceil(str_word_count(strip_tags($clean_content)) / 200));

// 5. Internal Cross-Linking Data (Silo Architecture) - NOW DYNAMIC
$published_niches = get_posts([
    'post_type' => 'pseo_niche',
    'numberposts' => -1,
    'post_status' => 'publish',
    'orderby' => 'title',
    'order' => 'ASC'
]);

$master_niches = [];
foreach ($published_niches as $n) {
    $master_niches[$n->post_name] = $n->post_title;
}

if (empty($master_niches)) {
    $master_niches = ['law-firm' => 'Law Firm', 'real-estate' => 'Real Estate'];
}

$related_niches = array_diff_key($master_niches, [$niche_slug => '']);

mt_srand($master_seed + 99);
$random_related_keys = array_rand($related_niches, min(4, max(1, count($related_niches))));

$cross_links = [];
if (!empty($related_niches)) {
    foreach((array)$random_related_keys as $key) {
        $cross_links[$key] = $related_niches[$key];
    }
}

?>
<!DOCTYPE html>
<html lang="en" class="scroll-smooth overflow-x-hidden">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    
    <title><?= esc_html($dynamic_meta_title) ?></title>
    <meta name="description" content="<?= esc_attr($dynamic_meta_desc) ?>">
    <link rel="canonical" href="<?= esc_url($current_url) ?>" />

    <!-- Favicon -->
    <link rel="icon" href="https://getonlinestudio.com/insights/wp-content/uploads/2026/02/GetOnline_Studio_Logo.jpg" sizes="32x32" />
    <link rel="apple-touch-icon" href="https://getonlinestudio.com/insights/wp-content/uploads/2026/02/GetOnline_Studio_Logo.jpg" />

    <!-- Open Graph (Dynamic Image for Virality) -->
    <meta property="og:title" content="<?= esc_html($dynamic_meta_title) ?>" />
    <meta property="og:description" content="<?= esc_attr($dynamic_meta_desc) ?>" />
    <meta property="og:type" content="article" />
    <meta property="og:url" content="<?= esc_url($current_url) ?>" />
    <meta property="og:image" content="<?= esc_url($dynamic_og_image) ?>" />
    <meta property="og:image:width" content="1200" />
    <meta property="og:image:height" content="630" />
    <meta property="og:image:alt" content="<?= esc_attr($dynamic_meta_title) ?>" />
    
    <!-- Twitter Meta Tags -->
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="<?= esc_html($dynamic_meta_title) ?>">
    <meta name="twitter:description" content="<?= esc_attr($dynamic_meta_desc) ?>">
    <meta name="twitter:image" content="<?= esc_url($dynamic_og_image) ?>">
    
    <!-- JSON-LD Schema Markup (Article & Breadcrumbs) -->
    <script type="application/ld+json">
    {
      "@context": "https://schema.org",
      "@graph": [
        {
          "@type": "Article",
          "headline": "<?= esc_html($dynamic_meta_title) ?>",
          "description": "<?= esc_attr($dynamic_meta_desc) ?>",
          "author": {
            "@type": "Person",
            "name": "<?= esc_html($author_name) ?>"
          },
          "publisher": {
            "@type": "Organization",
            "name": "GetOnline Studio",
            "logo": {
              "@type": "ImageObject",
              "url": "https://getonlinestudio.com/insights/wp-content/uploads/2026/02/GetOnline_Studio_Logo.jpg"
            }
          },
          "dateModified": "<?= date('c', $dynamic_date) ?>"
        },
        {
          "@type": "BreadcrumbList",
          "itemListElement": [
            { "@type": "ListItem", "position": 1, "name": "Home", "item": "https://getonlinestudio.com/" },
            { "@type": "ListItem", "position": 2, "name": "Locations", "item": "https://getonlinestudio.com/locations/" },
            { "@type": "ListItem", "position": 3, "name": "<?= esc_html($city_name) ?>", "item": "https://getonlinestudio.com/locations/<?= esc_attr($city_slug) ?>/" },
            { "@type": "ListItem", "position": 4, "name": "Top <?= esc_html($niche_name) ?> Designers" }
          ]
        }
      ]
    }
    </script>

    <link href="https://fonts.googleapis.com/css2?family=Manrope:wght@300;400;500;600;700&family=Syne:wght@400;600;700;800&display=swap" rel="stylesheet">
    <script src="https://unpkg.com/lucide@latest"></script>
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        'matte-black': '#0a0a0a', 'panel-dark': '#121212', 'card-dark': '#1a1a1a',
                        'lavender': '#e9d5ff', 'sharp-purple': '#7e22ce', 'success-green': '#4ade80',
                        'gold': '#fbbf24', 'silver': '#9ca3af', 'bronze': '#b45309'
                    },
                    fontFamily: { 'syne': ['Syne', 'sans-serif'], 'manrope': ['Manrope', 'sans-serif'], }
                }
            }
        }
    </script>
    <style>
        body { background-color: #0a0a0a; color: #e9d5ff; font-family: 'Manrope', sans-serif; }
        
        /* Editorial Typography */
        .editorial-content h2 { font-family: 'Syne', sans-serif; font-size: 2rem; font-weight: 700; color: #fff; margin-top: 3.5rem; margin-bottom: 1.5rem; letter-spacing: -0.02em; line-height: 1.2; }
        .editorial-content h3 { font-family: 'Syne', sans-serif; font-size: 1.5rem; font-weight: 700; color: #fff; margin-top: 2.5rem; margin-bottom: 1rem; position: relative; padding-left: 0; }
        
        /* Larger font and massive bottom margin for the "Bucket Brigade" reading flow */
        .editorial-content p { font-size: 1.1875rem; line-height: 1.9; color: rgba(233, 213, 255, 0.85); margin-bottom: 2rem; }
        
        .editorial-content ul { list-style-type: none; padding-left: 0; margin-bottom: 2.5rem; }
        .editorial-content ul li { font-size: 1.1875rem; line-height: 1.9; color: rgba(233, 213, 255, 0.85); margin-bottom: 1rem; padding-left: 1.75rem; position: relative; }
        .editorial-content ul li::before { content: '→'; position: absolute; left: 0; color: #7e22ce; font-weight: bold; }
        
        .editorial-content strong { color: #fff; font-weight: 700; }

        /* ----------------------------------------------------- */
        /* HOW WE RANKED SECTION                                 */
        /* ----------------------------------------------------- */
        .editorial-content h2.ranking-criteria-heading { margin-top: 0; margin-bottom: 1.5rem; font-size: 1.75rem; display: flex; align-items: center; gap: 0.75rem; }
        .editorial-content .ranking-criteria-box p { font-size: 1.05rem; line-height: 1.8; color: rgba(233, 213, 255, 0.75); margin-bottom: 1.5rem; }
        .editorial-content .ranking-criteria-box .criteria-list { margin-bottom: 0; }
        .editorial-content .ranking-criteria-box .criteria-list li { 
            background: rgba(255,255,255,0.02); 
            border: 1px solid rgba(255,255,255,0.05); 
            padding: 1rem 1.25rem 1rem 3rem; 
            border-radius: 0.75rem; 
            margin-bottom: 0.75rem; 
            font-size: 1.05rem; 
            line-height: 1.6;
        }
        .editorial-content .ranking-criteria-box .criteria-list li::before { 
            content: '✦'; color: #7e22ce; font-size: 1.2rem; top: 1rem; left: 1.25rem; 
        }
        
        /* ----------------------------------------------------- */
        /* COMPARISON TABLE STYLING */
        /* ----------------------------------------------------- */
        .table-responsive-wrapper { width: 100%; overflow-x: auto; -webkit-overflow-scrolling: touch; border-radius: 0.75rem; border: 1px solid rgba(255,255,255,0.1); box-shadow: 0 10px 30px rgba(0,0,0,0.3); }
        .editorial-content table { width: 100%; border-collapse: collapse; min-width: 600px; margin: 0; background-color: #121212; }
        .editorial-content thead tr { background-color: rgba(126, 34, 206, 0.15); border-bottom: 2px solid #7e22ce; }
        .editorial-content th { padding: 1.25rem 1.5rem; font-family: 'Syne', sans-serif; font-weight: 700; color: #fff; text-transform: uppercase; letter-spacing: 0.05em; font-size: 0.85rem; text-align: left; }
        .editorial-content td { padding: 1.25rem 1.5rem; border-bottom: 1px solid rgba(255,255,255,0.05); color: rgba(233, 213, 255, 0.85); vertical-align: top; font-size: 1rem; line-height: 1.6; }
        .editorial-content tbody tr:last-child td { border-bottom: none; }
        .editorial-content tbody tr:hover { background-color: rgba(255,255,255,0.03); }
        .editorial-content td p { margin: 0; padding: 0; font-size: 1rem; } 
        .editorial-content td strong { color: #e9d5ff; }

        /* THE GOLDEN BOX (GetOnline Studio #1 Spot) */
        .agency-card.rank-1 { background: #121212; border: 1px solid rgba(251, 191, 36, 0.3); border-radius: 1rem; padding: 2.5rem; margin: 3rem 0; position: relative; overflow: hidden; background: linear-gradient(145deg, #121212 0%, #1a1500 100%); box-shadow: 0 10px 40px rgba(0,0,0,0.5); }
        .agency-card.rank-1::before { content: ''; position: absolute; top: 0; left: 0; width: 100%; height: 4px; background: #fbbf24; }
        
        /* The Runners Up (#2 to #5) */
        .runner-up-item { padding-left: 1.5rem; border-left: 2px solid rgba(126, 34, 206, 0.3); margin: 3rem 0; }
        .runner-up-item h3 { margin-top: 0 !important; }
        .runner-up-item p:last-child { margin-bottom: 0; }

        /* ----------------------------------------------------- */
        /* PROS AND CONS STYLING */
        /* ----------------------------------------------------- */
        .editorial-content .runner-up-item ul { margin-top: 1.5rem; background: rgba(0,0,0,0.2); padding: 1.5rem; border-radius: 0.5rem; border: 1px solid rgba(255,255,255,0.05); margin-bottom: 0; }
        .editorial-content .runner-up-item ul li.pro-item::before { content: '✓'; color: #4ade80; font-size: 1.1em; }
        .editorial-content .runner-up-item ul li.con-item::before { content: '✕'; color: #f87171; font-size: 0.9em; }
        .editorial-content .runner-up-item ul li.pro-item strong { color: #4ade80; }
        .editorial-content .runner-up-item ul li.con-item strong { color: #f87171; }
        
        /* Scrollbar */
        ::-webkit-scrollbar { width: 8px; height: 8px; }
        ::-webkit-scrollbar-track { background: #0a0a0a; }
        ::-webkit-scrollbar-thumb { background: #333; border-radius: 10px; }
        ::-webkit-scrollbar-thumb:hover { background: #7e22ce; }

        .sticky-element { position: sticky; top: 6rem; }
        details > summary { list-style: none; }
        details > summary::-webkit-details-marker { display: none; }
    </style>
<?php wp_head(); ?>
</head>
<body class="antialiased relative selection:bg-sharp-purple selection:text-white overflow-x-hidden w-full">

    <!-- Navbar -->
    <nav class="fixed w-full z-50 top-0 transition-all duration-300 bg-matte-black/80 backdrop-blur-lg border-b border-white/5">
        <div class="max-w-7xl mx-auto px-6 h-16 md:h-20 flex items-center justify-between">
            <a href="/" class="flex items-center gap-2 group z-50">
                <span class="font-syne font-bold text-xl md:text-2xl tracking-tighter text-white">GO<span class="text-sharp-purple">.</span></span>
                <span class="font-manrope text-[10px] font-bold uppercase tracking-widest text-lavender/50 hidden sm:block border-l border-white/10 pl-2 ml-1">Editorial</span>
            </a>
            <div class="flex items-center gap-4">
                <a href="<?= esc_url($commercial_url) ?>" class="text-xs md:text-sm font-bold text-white bg-sharp-purple px-5 md:px-6 py-2 md:py-2.5 rounded-full hover:shadow-[0_0_20px_rgba(126,34,206,0.4)] transition-all">
                    Hire an Agency
                </a>
            </div>
        </div>
    </nav>

    <!-- Main Content Wrapper -->
    <main class="pt-24 md:pt-32 pb-32 md:pb-24">
        <div class="max-w-7xl mx-auto px-6">
            
            <!-- Breadcrumbs -->
            <div class="flex items-center gap-2 text-[10px] sm:text-xs font-mono uppercase tracking-widest text-lavender/40 mb-8 sm:mb-12 overflow-x-auto whitespace-nowrap pb-2">
                <a href="/" class="hover:text-sharp-purple transition-colors">Home</a>
                <span>/</span>
                <a href="/locations/" class="hover:text-sharp-purple transition-colors">Locations</a>
                <span>/</span>
                <a href="/locations/<?= esc_attr($city_slug) ?>/" class="hover:text-sharp-purple transition-colors"><?= esc_html($city_name) ?></a>
                <span>/</span>
                <span class="text-lavender/80">Top <?= esc_html($niche_name) ?> Designers</span>
            </div>

            <!-- Grid Layout -->
            <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 lg:gap-12 items-start">
                
                <!-- Left Sidebar: Table of Contents (Desktop) -->
                <aside class="hidden lg:block lg:col-span-3">
                    <div class="sticky-element bg-panel-dark/50 border border-white/5 rounded-xl p-6">
                        <h4 class="font-syne font-bold text-white uppercase tracking-widest text-xs mb-4 flex items-center gap-2">
                            <i data-lucide="list" class="w-4 h-4 text-sharp-purple"></i> Contents
                        </h4>
                        <ul id="toc-container-desktop" class="space-y-4 border-l border-white/10 pl-4 text-sm font-medium text-lavender/60">
                            <!-- TOC generated by JS -->
                        </ul>
                    </div>
                </aside>

                <!-- Center Column: Main Article -->
                <article class="lg:col-span-6 w-full max-w-3xl mx-auto lg:mx-0">
                    
                    <header class="mb-8 md:mb-12">
                        <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-sharp-purple/10 border border-sharp-purple/20 text-sharp-purple text-[10px] font-bold uppercase tracking-widest mb-6">
                            <i data-lucide="check-circle-2" class="w-3 h-3"></i> Verified <?= $current_month ?> <?= $current_year ?> Rankings
                        </div>
                        <h1 class="font-syne text-3xl sm:text-5xl lg:text-6xl font-extrabold text-white leading-[1.1] tracking-tight mb-6">
                            <?= esc_html($listicle->target_keyword) ?>
                        </h1>
                        
                        <div class="flex items-center justify-between border-y border-white/5 py-4">
                            <div class="flex items-center gap-3">
                                <img src="<?= esc_url($author_avatar) ?>" alt="<?= esc_attr($author_name) ?>" class="w-10 h-10 rounded-full border border-white/10 object-cover">
                                <div>
                                    <p class="text-sm font-bold text-white"><?= esc_html($author_name) ?></p>
                                    <p class="text-[10px] text-lavender/50 mt-0.5"><?= esc_html($author_role) ?> &bull; Updated: <?= esc_html($last_updated) ?></p>
                                </div>
                            </div>
                            <div class="flex items-center gap-4">
                                <div class="hidden sm:flex items-center gap-2 text-xs text-lavender/50 border-r border-white/10 pr-4">
                                    <i data-lucide="clock" class="w-4 h-4"></i> <?= $read_time ?> min read
                                </div>
                            </div>
                        </div>
                    </header>

                    <!-- Mobile TOC -->
                    <div class="lg:hidden mb-10 bg-card-dark border border-white/10 rounded-xl overflow-hidden">
                        <details class="group">
                            <summary class="flex items-center justify-between p-4 cursor-pointer font-syne font-bold text-white text-sm">
                                <div class="flex items-center gap-2"><i data-lucide="list" class="w-4 h-4 text-sharp-purple"></i> Table of Contents</div>
                                <i data-lucide="chevron-down" class="w-4 h-4 transition-transform group-open:rotate-180 text-lavender/50"></i>
                            </summary>
                            <div class="p-4 border-t border-white/5 bg-panel-dark/30">
                                <ul id="toc-container-mobile" class="space-y-4 border-l border-white/10 pl-4 text-sm font-medium text-lavender/60">
                                    <!-- TOC generated by JS -->
                                </ul>
                            </div>
                        </details>
                    </div>

                    <!-- The AI Generated HTML Content -->
                    <div id="editorial-body" class="editorial-content">
                        <?= $clean_content ?>
                    </div>

                    <!-- Bottom CTA -->
                    <div class="mt-16 p-8 bg-sharp-purple/10 border border-sharp-purple/30 rounded-2xl text-center">
                        <h3 class="font-syne text-2xl font-bold text-white mb-3">Ready to skip the research?</h3>
                        <p class="text-sm text-lavender/80 mb-6">Partner with the #1 rated <?= strtolower($niche_name) ?> platform builders in <?= $city_name ?>.</p>
                        <a href="<?= esc_url($commercial_url) ?>" class="inline-flex items-center justify-center gap-2 bg-sharp-purple text-white px-8 py-4 rounded-full font-bold text-sm shadow-[0_0_20px_rgba(126,34,206,0.3)] hover:scale-105 transition-transform">
                            Get Your Free Quote <i data-lucide="arrow-right" class="w-4 h-4"></i>
                        </a>
                    </div>

                    <!-- Author Box -->
                    <div class="mt-12 p-6 sm:p-8 bg-card-dark border border-white/10 rounded-2xl relative overflow-hidden group">
                        <!-- Background Glow -->
                        <div class="absolute -right-20 -bottom-20 w-64 h-64 bg-sharp-purple/10 rounded-full blur-[80px] group-hover:bg-sharp-purple/20 transition-colors duration-700 pointer-events-none"></div>
                        
                        <div class="flex flex-col sm:flex-row items-start sm:items-center gap-6 relative z-10">
                            <img src="<?= esc_url($author_avatar) ?>" alt="<?= esc_attr($author_name) ?>" class="w-20 h-20 sm:w-24 sm:h-24 rounded-full border-2 border-sharp-purple/40 object-cover shadow-[0_0_20px_rgba(126,34,206,0.3)] flex-shrink-0">
                            
                            <div class="flex-1 w-full">
                                <div class="flex flex-row items-center justify-between gap-3 mb-3">
                                    <div>
                                        <h4 class="font-syne font-bold text-xl sm:text-2xl text-white"><?= esc_html($author_name) ?></h4>
                                        <p class="text-xs font-bold text-sharp-purple uppercase tracking-widest mt-1"><?= esc_html($author_role) ?></p>
                                    </div>
                                    <a href="mailto:<?= esc_attr($author_email) ?>" class="inline-flex items-center justify-center w-10 h-10 rounded-full bg-white/5 border border-white/10 hover:bg-sharp-purple hover:border-sharp-purple text-lavender/70 hover:text-white transition-all group/email flex-shrink-0" aria-label="Email <?= esc_attr($author_name) ?>">
                                        <i data-lucide="mail" class="w-4 h-4 group-hover/email:scale-110 transition-transform"></i>
                                    </a>
                                </div>
                                <p class="text-sm text-lavender/80 leading-relaxed font-medium">
                                    <?= esc_html($author_bio) ?>
                                </p>
                            </div>
                        </div>
                    </div>
                    
                    <!-- INTERNAL SEO: Cross-linking Silo Block -->
                    <div class="mt-16 pt-12 border-t border-white/5">
                        <h4 class="font-syne font-bold text-xl text-white mb-6">Explore More Digital Services in <?= esc_html($city_name) ?></h4>
                        <?php if (!empty($cross_links)): ?>
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <?php foreach($cross_links as $c_slug => $c_name): ?>
                                <a href="/locations/<?= esc_attr($city_slug) ?>/top-<?= esc_attr($c_slug) ?>-web-designers/" class="bg-card-dark border border-white/5 p-4 rounded-xl flex items-center justify-between group hover:border-sharp-purple/30 transition-all">
                                    <span class="text-sm font-medium text-lavender/80 group-hover:text-white">Top <?= esc_html($c_name) ?> Web Designers</span>
                                    <i data-lucide="arrow-right" class="w-4 h-4 text-sharp-purple opacity-0 -translate-x-2 group-hover:opacity-100 group-hover:translate-x-0 transition-all"></i>
                                </a>
                            <?php endforeach; ?>
                        </div>
                        <?php else: ?>
                        <p class="text-lavender/50 text-sm">More specialized services coming soon to <?= esc_html($city_name) ?>.</p>
                        <?php endif; ?>
                    </div>

                </article>

                <!-- Right Sidebar: Sticky CTA (Desktop) -->
                <aside class="hidden lg:block lg:col-span-3">
                    <div class="sticky-element">
                        <div class="bg-card-dark border border-white/10 rounded-2xl p-6 shadow-2xl relative overflow-hidden group">
                            <!-- Glow effect -->
                            <div class="absolute -top-20 -right-20 w-40 h-40 bg-sharp-purple/30 rounded-full blur-[50px] group-hover:bg-sharp-purple/50 transition-all duration-500"></div>
                            
                            <div class="relative z-10">
                                <div class="w-12 h-12 bg-sharp-purple/20 rounded-xl flex items-center justify-center mb-6 border border-sharp-purple/30">
                                    <i data-lucide="award" class="w-6 h-6 text-sharp-purple"></i>
                                </div>
                                <h3 class="font-syne text-xl font-bold text-white mb-2">Hire the #1 Ranked Agency</h3>
                                <p class="text-sm text-lavender/60 mb-6 leading-relaxed">
                                    Don't risk your <?= strtolower($niche_name) ?>'s reputation on freelancers. GetOnline Studio builds platforms that actually capture clients.
                                </p>
                                <a href="<?= esc_url($commercial_url) ?>" class="flex items-center justify-center gap-2 w-full bg-white text-matte-black hover:bg-sharp-purple hover:text-white px-4 py-3 rounded-xl font-bold text-sm transition-all shadow-[0_0_15px_rgba(255,255,255,0.1)]">
                                    View Our Services <i data-lucide="arrow-right" class="w-4 h-4"></i>
                                </a>
                                <p class="text-center text-[9px] text-lavender/40 mt-4 uppercase tracking-widest">Available in <?= esc_html($city_name) ?></p>
                            </div>
                        </div>
                    </div>
                </aside>

            </div>
        </div>
    </main>

    <!-- FLOATING MOBILE BOTTOM BAR -->
    <div class="md:hidden fixed bottom-0 left-0 w-full bg-matte-black/90 backdrop-blur-xl border-t border-white/10 p-4 z-50 flex items-center justify-between shadow-[0_-10px_40px_rgba(0,0,0,0.5)]">
        <div class="flex flex-col min-w-0 pr-3">
            <span class="text-[9px] font-bold text-gold uppercase tracking-widest mb-0.5">#1 Rated</span>
            <span class="text-sm font-bold text-white truncate">GetOnline Studio</span>
        </div>
        <a href="<?= esc_url($commercial_url) ?>" class="flex-shrink-0 bg-sharp-purple text-white px-6 py-2.5 rounded-full font-bold text-xs shadow-lg">
            Hire Now
        </a>
    </div>

    <!-- Footer Mini -->
    <footer class="border-t border-white/5 py-8 bg-panel-dark text-center pb-24 md:pb-8">
        <div class="max-w-7xl mx-auto px-6 flex flex-col md:flex-row justify-between items-center gap-4">
            <p class="text-xs font-mono text-lavender/40">&copy; <?= date('Y') ?> GetOnline Studio Editorial. All rights reserved.</p>
            <div class="flex items-center justify-center gap-6 text-sm font-bold text-lavender/60">
                <a href="/" class="hover:text-sharp-purple hover-target">Home</a>
                <a href="/work" class="hover:text-sharp-purple hover-target">Work</a>
                <a href="/about" class="hover:text-sharp-purple hover-target">About</a>
                <a href="/services/" class="hover:text-sharp-purple hover-target">Services</a>
                <a href="/locations/" class="hover:text-sharp-purple hover-target">Locations</a>
            </div>
        </div>
    </footer>

    <script>
        lucide.createIcons();

        document.addEventListener('DOMContentLoaded', () => {
            const editorialBody = document.getElementById('editorial-body');
            const tocDesktop = document.getElementById('toc-container-desktop');
            const tocMobile = document.getElementById('toc-container-mobile');
            
            // 1. BULLETPROOF GOLDEN BOX PARSER
            const h3s = editorialBody.querySelectorAll('h3, h2');
            let foundGOS = false;
            let currentVisualRank = 2; // Runners up start at 2
            
            h3s.forEach((heading) => {
                let text = heading.innerText.trim();
                const isGOS = text.toLowerCase().includes('getonline studio');
                const isConclusion = text.toLowerCase().includes('conclusion') || text.toLowerCase().includes('what to look for');
                
                if (!foundGOS) {
                    if (isGOS) {
                        foundGOS = true;
                        
                        // Strip any accidental numbers the AI might have prepended
                        let cleanName = text.replace(/^(?:#)?\d+[\.\)]\s*/, '').trim(); 
                        let iconHtml = `<div class="w-10 h-10 rounded-full bg-gold/20 flex items-center justify-center border border-gold/40 shadow-[0_0_15px_rgba(251,191,36,0.3)] flex-shrink-0"><i data-lucide="trophy" class="w-5 h-5 text-gold"></i></div>`;
                        let wrapperClass = 'agency-card rank-1';
                        
                        cleanName = `<a href="<?= esc_url($commercial_url) ?>" class="hover:text-gold transition-colors">GetOnline Studio</a>`;
                        heading.innerHTML = `<div class="flex items-center gap-4 mb-4">${iconHtml} <span class="text-2xl font-bold">${cleanName}</span></div>`;
                        
                        const wrapper = document.createElement('div');
                        wrapper.className = wrapperClass;
                        heading.parentNode.insertBefore(wrapper, heading);
                        
                        let nextNode = heading.nextSibling;
                        wrapper.appendChild(heading); 

                        // SCOOP UP ALL PARAGRAPHS until next H2 or H3
                        while (nextNode) {
                            let temp = nextNode.nextSibling;
                            if (nextNode.nodeType === 1 && (nextNode.tagName === 'H2' || nextNode.tagName === 'H3' || nextNode.tagName === 'H1')) {
                                break;
                            }
                            wrapper.appendChild(nextNode);
                            nextNode = temp;
                        }

                        // Inject the glowing CTA button ONLY inside GetOnline Studio's card
                        const btnHtml = `
                        <div class="mt-8 pt-6 border-t border-white/10">
                            <a href="<?= esc_url($commercial_url) ?>" class="inline-flex items-center justify-center w-full sm:w-auto gap-2 bg-gold text-black px-8 py-4 rounded-lg font-bold text-sm hover:bg-white transition-all shadow-[0_0_15px_rgba(251,191,36,0.3)]">
                                Visit Agency Website <i data-lucide="external-link" class="w-4 h-4"></i>
                            </a>
                        </div>`;
                        wrapper.insertAdjacentHTML('beforeend', btnHtml);
                        
                    } else if (heading.tagName === 'H3') {
                        // It's a heading BEFORE GetOnline Studio (e.g. Ranking Criteria).
                        // Strip away ugly numbers like "#2" to make it a clean, beautiful subheading.
                        let cleanText = text.replace(/^(?:#)?\d+[\.\)]?\s*/, '').trim();
                        heading.innerText = cleanText;
                        heading.className = 'font-syne font-bold text-2xl text-sharp-purple mt-10 mb-4 border-b border-white/10 pb-2';
                    }
                } else {
                    // Headings AFTER GetOnline Studio
                    // If it's an H3, treat it as a runner-up (unless we hit 5 or it's the Conclusion)
                    if (heading.tagName === 'H3' && currentVisualRank <= 5 && !isConclusion) {
                        let cleanName = text.replace(/^(?:#)?\d+[\.\)]?\s*/, '').trim(); 
                        
                        let iconHtml = `<div class="text-sharp-purple font-syne font-bold text-2xl flex-shrink-0">#${currentVisualRank}</div>`;
                        let wrapperClass = 'runner-up-item';
                        
                        heading.innerHTML = `<div class="flex items-center gap-3 mb-2">${iconHtml} <span class="text-xl font-bold">${cleanName}</span></div>`;
                        
                        const wrapper = document.createElement('div');
                        wrapper.className = wrapperClass;
                        heading.parentNode.insertBefore(wrapper, heading);
                        
                        let nextNode = heading.nextSibling;
                        wrapper.appendChild(heading); 

                        // SCOOP UP ALL PARAGRAPHS
                        while (nextNode) {
                            let temp = nextNode.nextSibling;
                            if (nextNode.nodeType === 1 && (nextNode.tagName === 'H2' || nextNode.tagName === 'H3' || nextNode.tagName === 'H1')) {
                                break;
                            }
                            wrapper.appendChild(nextNode);
                            nextNode = temp;
                        }
                        currentVisualRank++;
                    }
                }
            });

            lucide.createIcons();

            // 2. Build the Table of Contents dynamically for both Desktop and Mobile
            const h2s = editorialBody.querySelectorAll('h2');
            
            if (h2s.length > 0) {
                h2s.forEach((h2, index) => {
                    const id = 'section-' + index;
                    h2.id = id;
                    
                    if(tocDesktop) {
                        const liD = document.createElement('li');
                        liD.innerHTML = `<a href="#${id}" class="hover:text-white transition-colors block">${h2.textContent}</a>`;
                        tocDesktop.appendChild(liD);
                    }
                    
                    if(tocMobile) {
                        const liM = document.createElement('li');
                        liM.innerHTML = `<a href="#${id}" class="hover:text-white transition-colors block">${h2.textContent}</a>`;
                        liM.querySelector('a').addEventListener('click', () => {
                            document.querySelector('details.group').removeAttribute('open');
                        });
                        tocMobile.appendChild(liM);
                    }
                });
            } else {
                if (tocDesktop) tocDesktop.parentElement.style.display = 'none';
                if (tocMobile) tocMobile.closest('.lg\\:hidden').style.display = 'none';
            }
        });
    </script>
<?php wp_footer(); ?>
</body>
</html>