<?php
/**
 * SERVICES INDEX SILO TEMPLATE (BRAND MATCHED)
 * Place this in your root directory.
 * Routes URLs like: /services/ -> services-index.php
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

// Helper function to prevent double-encoding
function go_safe_text($text) {
    return esc_html(html_entity_decode($text ?? '', ENT_QUOTES, 'UTF-8'));
}

// 2. Fetch all ACTIVE niches from the WordPress Database
$active_niches = get_posts([
    'post_type'   => 'pseo_niche',
    'numberposts' => -1,
    'post_status' => 'publish',
    'orderby'     => 'title',
    'order'       => 'ASC'
]);

// 3. SEO Meta Data
$current_year = date('Y');
$meta_title = "Industry-Specific Web Design Services | GetOnline Studio";
$meta_desc = "GetOnline Studio provides premium, high-converting web design and development services engineered for specific industries. Browse our specialized digital solutions.";
?>
<!DOCTYPE html>
<html lang="en" class="scroll-smooth overflow-x-hidden">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title><?= $meta_title; ?></title>
    <meta name="description" content="<?= $meta_desc; ?>">
    <link rel="canonical" href="https://<?= $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'] ?>" />

    <!-- Open Graph -->
    <meta property="og:title" content="<?= esc_attr($meta_title) ?>" />
    <meta property="og:description" content="<?= esc_attr($meta_desc) ?>" />
    <meta property="og:type" content="website" />
    <meta property="og:url" content="https://<?= $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'] ?>" />
    <meta property="og:image" content="https://getonlinestudio.com/insights/wp-content/uploads/2026/02/GetOnline_Studio_Logo.jpg" />

    <!-- Brand Fonts: Manrope (Body) & Syne (Headings) -->
    <link href="https://fonts.googleapis.com/css2?family=Manrope:wght@300;400;500;600;700&family=Syne:wght@400;600;700;800&display=swap" rel="stylesheet">

    <!-- Tailwind CSS -->
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
                    fontFamily: { 'syne': ['Syne', 'sans-serif'], 'manrope': ['Manrope', 'sans-serif'] }
                }
            }
        }
    </script>
    <!-- Lucide Icons -->
    <script src="https://unpkg.com/lucide@latest"></script>
    <link rel="stylesheet" href="/assets/css/services-index.css">
<?php wp_head(); ?>
</head>
<body class="antialiased relative selection:bg-sharp-purple selection:text-white overflow-x-hidden w-full flex flex-col min-h-screen">

    <!-- Navbar -->
    <nav class="fixed w-full z-50 top-0 transition-all duration-300 bg-matte-black/80 backdrop-blur-lg border-b border-white/5">
        <div class="max-w-7xl mx-auto px-6 h-16 md:h-20 flex items-center justify-between">
            <a href="/" class="flex items-center gap-2 group z-50">
                <span class="font-syne font-bold text-xl md:text-2xl tracking-tighter text-white">GO<span class="text-sharp-purple">.</span></span>
                <span class="font-manrope text-[10px] font-bold uppercase tracking-widest text-lavender/50 hidden sm:block border-l border-white/10 pl-2 ml-1">Services</span>
            </a>
            <div class="flex items-center gap-4">
                <a href="/locations/" class="text-xs md:text-sm font-bold text-white bg-sharp-purple px-5 md:px-6 py-2 md:py-2.5 rounded-full hover:shadow-[0_0_20px_rgba(126,34,206,0.4)] transition-all">
                    View All Locations
                </a>
            </div>
        </div>
    </nav>

    <!-- Main Content Wrapper -->
    <main class="pt-32 pb-24 flex-grow">
        <div class="max-w-7xl mx-auto px-6">

            <!-- Breadcrumbs -->
            <div class="flex items-center gap-2 text-[10px] sm:text-xs font-mono uppercase tracking-widest text-lavender/40 mb-12 overflow-x-auto whitespace-nowrap pb-2">
                <a href="/" class="hover:text-sharp-purple transition-colors">Home</a>
                <span>/</span>
                <span class="text-lavender/80">Services</span>
            </div>

            <!-- Header -->
            <header class="mb-16 relative">
                <div class="absolute top-0 left-0 w-[400px] h-[400px] bg-sharp-purple/10 rounded-full blur-[100px] pointer-events-none z-0"></div>
                <div class="relative z-10 max-w-3xl">
                    <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-sharp-purple/10 border border-sharp-purple/20 text-sharp-purple text-[10px] font-bold uppercase tracking-widest mb-6">
                        <i data-lucide="layers" class="w-3 h-3"></i> Industry Solutions
                    </div>
                    <h1 class="font-syne text-4xl sm:text-5xl lg:text-7xl font-extrabold text-white leading-[1.1] tracking-tight mb-6">
                        Specialized Digital <br/><span class="text-transparent bg-clip-text bg-gradient-to-r from-white to-lavender/60">Services</span>
                    </h1>
                    <p class="text-lg md:text-xl text-lavender/80 font-medium leading-relaxed max-w-2xl">
                        Generic websites don't convert. We engineer highly-optimized, industry-specific digital platforms designed to dominate search rankings and generate revenue.
                    </p>
                </div>
            </header>

            <!-- Dynamic Services Grid -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 relative z-10">
                <?php if (empty($active_niches)): ?>
                    <div class="col-span-full bg-card-dark border border-white/5 rounded-2xl p-12 text-center">
                        <p class="text-lavender/50 text-lg">No active services found. Add some niches in the GetOnline Studio dashboard.</p>
                    </div>
                <?php else: ?>
                    <?php foreach ($active_niches as $niche):
                        $niche_slug = $niche->post_name;
                        $niche_name = go_safe_text($niche->post_title);
                        // Build the default hub URL (e.g., /services/law-firm-website-designer/)
                        $hub_url = "/services/{$niche_slug}-website-designer/";
                    ?>
                        <a href="<?= esc_url($hub_url); ?>" class="bg-card-dark border border-white/5 p-8 rounded-2xl group hover:border-sharp-purple/40 transition-all flex flex-col h-full hover:-translate-y-1 shadow-[0_10px_30px_rgba(0,0,0,0.2)]">
                            <div class="w-12 h-12 rounded-xl bg-panel-dark border border-white/10 flex items-center justify-center mb-6 text-sharp-purple group-hover:bg-sharp-purple group-hover:text-white transition-all">
                                <i data-lucide="layout" class="w-5 h-5"></i>
                            </div>
                            <h3 class="font-syne font-bold text-xl text-white mb-3"><?= $niche_name; ?> <br/>Web Design</h3>
                            <p class="text-lavender/60 text-sm font-medium leading-relaxed mb-6 flex-grow">
                                Custom digital infrastructure built specifically for the <?= strtolower($niche_name); ?> industry.
                            </p>
                            <div class="flex items-center gap-2 text-xs font-bold text-sharp-purple uppercase tracking-widest mt-auto">
                                Explore Service <i data-lucide="arrow-right" class="w-4 h-4 group-hover:translate-x-1 transition-transform"></i>
                            </div>
                        </a>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>

            <!-- Global CTA -->
            <div class="mt-24 bg-panel-dark border border-white/10 rounded-[2.5rem] p-12 lg:p-16 text-center relative overflow-hidden shadow-2xl">
                <div class="absolute inset-0 bg-gradient-to-br from-sharp-purple/10 to-transparent pointer-events-none"></div>
                <div class="relative z-10">
                    <h2 class="font-syne text-3xl lg:text-4xl font-extrabold mb-4 text-white">Don't see your industry?</h2>
                    <p class="text-lavender/70 mb-8 max-w-xl mx-auto text-base font-medium">
                        We build custom, high-performance web applications for all enterprise sectors. Contact our team to discuss your specific requirements.
                    </p>
                    <a href="/contact" class="inline-flex items-center justify-center gap-2 bg-white text-matte-black hover:bg-sharp-purple hover:text-white px-8 py-4 rounded-full font-bold text-sm transition-all shadow-[0_0_15px_rgba(255,255,255,0.1)]">
                        Contact Us <i data-lucide="mail" class="w-4 h-4"></i>
                    </a>
                </div>
            </div>

        </div>
    </main>

    <!-- Footer -->
    <footer class="border-t border-white/5 py-12 bg-panel-dark mt-auto">
        <div class="max-w-7xl mx-auto px-6 flex flex-col md:flex-row justify-between items-center gap-6">
            <p class="text-xs font-mono text-lavender/40 uppercase tracking-widest">&copy; <?= $current_year; ?> GetOnline Studio. All rights reserved.</p>
            <div class="flex items-center gap-6 text-sm font-bold text-lavender/60">
                <a href="/locations/" class="hover:text-white transition-colors">Locations</a>
                <a href="/" class="hover:text-white transition-colors">Main Website</a>
            </div>
        </div>
    </footer>

    <script>
        lucide.createIcons();
    </script>
<?php wp_footer(); ?>
</body>
</html>