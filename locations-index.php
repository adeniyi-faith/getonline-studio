<?php
/**
 * LOCATIONS INDEX TEMPLATE
 * Place this in your root directory.
 * Routes URLs like: /locations/ -> locations-index.php
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

// Helper function to prevent double-encoding
function go_safe_text($text) {
    return esc_html(html_entity_decode($text ?? '', ENT_QUOTES, 'UTF-8'));
}

// 2. Fetch all ACTIVE cities from the WordPress Database
$active_cities = get_posts([
    'post_type' => 'pseo_location',
    'numberposts' => -1,
    'post_status' => 'publish', // ONLY pull cities you've toggled ON
    'orderby' => 'title',
    'order' => 'ASC'
]);

// 3. SEO Meta Data
$meta_title = "Web Design Agency Locations in Nigeria | GetOnline Studio";
$meta_desc = "GetOnline Studio provides premium, high-converting web design and development services across Nigeria. Find our localized digital services in your city.";
?>
<!DOCTYPE html>
<html lang="en" class="scroll-smooth">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">

    <!-- DYNAMIC SEO TAGS -->
    <title><?= go_safe_text($meta_title) ?></title>
    <meta name="description" content="<?= go_safe_text($meta_desc) ?>">
    <link rel="canonical" href="<?= GO_SITE_URL . '/locations/' ?>" />

    <!-- Favicon -->
    <link rel="icon" href="https://getonlinestudio.com/insights/wp-content/uploads/2026/02/GetOnline_Studio_Logo.jpg" sizes="32x32" />
    <link rel="apple-touch-icon" href="https://getonlinestudio.com/insights/wp-content/uploads/2026/02/GetOnline_Studio_Logo.jpg" />

    <!-- Open Graph -->
    <meta property="og:title" content="<?= go_safe_text($meta_title) ?>" />
    <meta property="og:description" content="<?= go_safe_text($meta_desc) ?>" />
    <meta property="og:type" content="website" />
    <meta property="og:url" content="<?= GO_SITE_URL . '/locations/' ?>" />
    <meta property="og:image" content="https://getonlinestudio.com/insights/wp-content/uploads/2026/02/GetOnline_Studio_Logo.jpg" />
    <meta property="og:image:alt" content="GetOnline Studio Logo" />

    <!-- Twitter Meta Tags -->
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="<?= go_safe_text($meta_title) ?>">
    <meta name="twitter:description" content="<?= go_safe_text($meta_desc) ?>">
    <meta name="twitter:image" content="https://getonlinestudio.com/insights/wp-content/uploads/2026/02/GetOnline_Studio_Logo.jpg">

    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Manrope:wght@300;400;600;700&family=Syne:wght@400;700;800&family=Fira+Code:wght@400;600&display=swap" rel="stylesheet">
    <script src="https://unpkg.com/lucide@latest"></script>

    <!-- Tailwind CSS -->
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
                        'syne': ['Syne', 'sans-serif'], 'manrope': ['Manrope', 'sans-serif'], 'mono': ['Fira Code', 'monospace'],
                    },
                    backgroundImage: {
                        'noise': "url('data:image/svg+xml,%3Csvg viewBox=%220 0 200 200%22 xmlns=%22http://www.w3.org/2000/svg%22%3E%3Cfilter id=%22noiseFilter%22%3E%3CfeTurbulence type=%22fractalNoise%22 baseFrequency=%220.65%22 numOctaves=%223%22 stitchTiles=%22stitch%22/%3E%3C/filter%3E%3Crect width=%22100%25%22 height=%22100%25%22 filter=%22url(%23noiseFilter)%22 opacity=%220.05%22/%3E%3C/svg%3E')",
                    },
                    animation: {
                        'spin-slow': 'spin 15s linear infinite',
                        'float': 'float 6s ease-in-out infinite',
                    }
                }
            }
        }
    </script>

    <link rel="stylesheet" href="/assets/css/locations-index.css">
<?php wp_head(); ?>
</head>
<body class="bg-matte-black bg-noise font-manrope selection:bg-sharp-purple selection:text-white relative">

    <div class="cursor-dot"></div>
    <div class="cursor-outline"></div>

    <!-- Navigation -->
    <nav class="fixed top-0 w-full z-40 px-4 md:px-6 py-4 md:py-6 flex justify-between items-center mix-blend-difference text-lavender">
        <a href="https://getonlinestudio.com" class="font-syne font-bold text-xl md:text-2xl hover:text-sharp-purple transition-colors hover-target">GO.</a>
        <a href="#consultation" class="text-xs md:text-sm font-bold tracking-widest uppercase border border-lavender px-4 md:px-6 py-2 rounded-full hover:bg-lavender hover:text-matte-black transition-all duration-300 bg-matte-black/20 backdrop-blur-sm hover-target">
            Start Project
        </a>
    </nav>

    <!-- SECTION 1: HERO -->
    <header class="relative min-h-[60vh] flex flex-col justify-center items-center px-4 overflow-hidden border-b border-lavender/10 pt-32 pb-24 md:pb-32">
        <div class="perspective-grid"></div>
        <div class="absolute w-32 h-32 rounded-full border border-sharp-purple/20 top-[15%] left-[10%] animate-float" style="animation-delay: 0s;"></div>

        <div class="relative z-20 text-center max-w-5xl mx-auto mt-10">
            <!-- Location Badge -->
            <div class="flex items-center justify-center mb-6 md:mb-8 reveal-up">
                <div class="inline-flex items-center gap-3 font-mono text-code-green uppercase tracking-[0.2em] text-[10px] md:text-xs font-bold bg-code-green/10 px-4 py-2 rounded-full border border-code-green/20 backdrop-blur-sm">
                    <span class="relative flex h-2 w-2">
                      <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-code-green opacity-75"></span>
                      <span class="relative inline-flex rounded-full h-2 w-2 bg-code-green"></span>
                    </span>
                    <span>Operating Nationwide</span>
                </div>
            </div>

            <!-- Headline -->
            <h1 class="font-syne text-[8vw] md:text-[6vw] leading-[1.1] font-bold text-lavender reveal-up" style="animation-delay: 0.2s;">
                Digital Excellence Across <br><span class="text-transparent text-stroke hover:text-sharp-purple transition-colors duration-300 cursor-default">NIGERIA</span>
            </h1>

            <p class="font-manrope text-lavender/70 text-lg md:text-xl max-w-2xl mx-auto mt-8 leading-relaxed reveal-up" style="animation-delay: 0.4s;">
                We partner with ambitious brands across the country. Select your city below to view our localized web design services and industry solutions.
            </p>
        </div>
    </header>

    <!-- SECTION 2: THE LOCATIONS GRID -->
    <section class="py-24 md:py-32 px-4 md:px-6 bg-[#0a0a0a] relative z-10 min-h-[40vh]">
        <div class="max-w-7xl mx-auto">

            <?php if (empty($active_cities)): ?>
            <div class="text-center p-12 border border-lavender/10 rounded-2xl bg-card-dark reveal-up">
                <i data-lucide="map-pin-off" class="w-12 h-12 text-sharp-purple mx-auto mb-4"></i>
                <h3 class="font-syne text-2xl font-bold text-white mb-2">No Active Cities Yet</h3>
                <p class="text-lavender/60">Log into your Command Center to publish your first location.</p>
            </div>
            <?php else: ?>

            <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4 md:gap-6">
                <?php foreach($active_cities as $city):
                    $city_slug = $city->post_name;
                    $link_url = "/locations/" . $city_slug . "/";
                ?>
                <a href="<?= esc_url($link_url) ?>" class="group block bg-matte-black border border-lavender/10 p-6 rounded-2xl hover:border-sharp-purple/50 hover:bg-[#111] transition-all duration-300 hover-target hover:-translate-y-1 reveal-up">
                    <div class="flex items-center justify-between mb-4">
                        <div class="w-12 h-12 rounded-xl bg-sharp-purple/10 flex items-center justify-center group-hover:bg-sharp-purple transition-colors duration-300">
                            <i data-lucide="map-pin" class="w-5 h-5 text-sharp-purple group-hover:text-white transition-colors duration-300"></i>
                        </div>
                        <i data-lucide="arrow-up-right" class="w-5 h-5 text-lavender/30 group-hover:text-sharp-purple transition-colors duration-300"></i>
                    </div>
                    <h3 class="font-syne text-xl font-bold text-white mb-2"><?= go_safe_text($city->post_title) ?></h3>
                    <p class="text-xs text-lavender/50 font-mono uppercase tracking-widest">
                        Local Agency Hub
                    </p>
                </a>
                <?php endforeach; ?>
            </div>

            <?php endif; ?>
        </div>
    </section>

    <!-- SECTION 3: FINAL CALL TO ACTION -->
    <section id="consultation" class="py-32 md:py-40 px-4 md:px-6 text-center bg-sharp-purple text-white relative z-20 overflow-hidden">
        <div class="absolute inset-0 bg-[url('data:image/svg+xml,%3Csvg viewBox=%220 0 200 200%22 xmlns=%22http://www.w3.org/2000/svg%22%3E%3Cfilter id=%22noiseFilter%22%3E%3CfeTurbulence type=%22fractalNoise%22 baseFrequency=%220.65%22 numOctaves=%223%22 stitchTiles=%22stitch%22/%3E%3C/filter%3E%3Crect width=%22100%25%22 height=%22100%25%22 filter=%22url(%23noiseFilter)%22 opacity=%220.08%22/%3E%3C/svg%3E')] mix-blend-overlay"></div>
        <div class="max-w-4xl mx-auto reveal-up relative z-10">
            <h2 class="font-syne text-4xl md:text-6xl font-bold mb-4 text-white leading-tight">
                Don't see your city listed?<br>
            </h2>
            <p class="font-manrope text-lg md:text-xl mb-12 text-white/90 leading-relaxed max-w-2xl mx-auto">
                We work remotely with ambitious brands all over the world. Book a free 20-minute strategy call with our team today to discuss your project.
            </p>
            <div class="flex flex-col sm:flex-row items-center justify-center gap-4">
                <a href="mailto:hello@getonlinestudio.com" class="w-full sm:w-auto bg-matte-black text-white px-10 py-5 rounded-full text-sm font-bold uppercase tracking-widest hover:bg-white hover:text-matte-black transition-all hover-target shadow-2xl">
                    Start Your Project &rarr;
                </a>
                <a href="https://wa.me/2348108275013?text=Hey%20GetOnline%20Studio!%20I'd%20like%20to%20talk%20about%20a%20project%20for%20my%20business." target="_blank" onclick="if(typeof gtag === 'function') { gtag('event', 'whatsapp_click', { 'event_category': 'contact', 'event_label': 'locations_page_footer' }); }" class="w-full sm:w-auto bg-green-500 text-white px-10 py-5 rounded-full text-sm font-bold uppercase tracking-widest hover:bg-green-400 transition-all hover-target shadow-xl flex items-center justify-center gap-2">
                    <i data-lucide="message-circle" class="w-5 h-5"></i> Chat on WhatsApp
                </a>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer class="py-12 md:py-20 px-4 md:px-6 bg-matte-black flex flex-col md:flex-row justify-between items-start md:items-end border-t border-lavender/20 relative z-20">
        <div class="w-full md:w-auto">
            <h2 class="font-syne text-5xl md:text-8xl mb-4 md:mb-6 text-lavender">LET'S TALK</h2>
            <a href="mailto:hello@getonlinestudio.com" class="text-lg md:text-2xl text-sharp-purple hover:text-white transition-colors font-manrope underline decoration-1 underline-offset-8 break-all hover-target">hello@getonlinestudio.com</a>
        </div>
        <div class="mt-10 md:mt-0 flex flex-col md:items-end gap-2 font-manrope text-xs md:text-sm uppercase tracking-widest w-full md:w-auto text-lavender/50">
            <div class="flex gap-6 mb-4 md:mb-2">
                <a href="/" class="hover:text-sharp-purple hover-target">Home</a>
                <a href="/work" class="hover:text-sharp-purple hover-target">Work</a>
                <a href="/about" class="hover:text-sharp-purple hover-target">About</a>
            </div>
            <p>GetOnline Studio &copy; <?= date('Y') ?></p>
            <p>Proudly serving Nigeria & Beyond.</p>
        </div>
    </footer>

    <script>
        lucide.createIcons();

        // High Performance Cursor Logic
        const cursorDot = document.querySelector('.cursor-dot');
        const cursorOutline = document.querySelector('.cursor-outline');
        const isTouchDevice = 'ontouchstart' in window || navigator.maxTouchPoints > 0;

        if (!isTouchDevice && cursorDot && cursorOutline) {
            let mouseX = 0, mouseY = 0, outlineX = 0, outlineY = 0;
            window.addEventListener('mousemove', (e) => {
                mouseX = e.clientX;
                mouseY = e.clientY;
                cursorDot.style.transform = `translate(${mouseX}px, ${mouseY}px) translate(-50%, -50%)`;
            });
            const animateCursor = () => {
                outlineX += (mouseX - outlineX) * 0.15;
                outlineY += (mouseY - outlineY) * 0.15;
                cursorOutline.style.transform = `translate(${outlineX}px, ${outlineY}px) translate(-50%, -50%)`;
                requestAnimationFrame(animateCursor);
            };
            animateCursor();

            const addHoverTargets = () => {
                const hoverTargets = document.querySelectorAll('.hover-target, a, button');
                hoverTargets.forEach(el => {
                    if(!el.hasAttribute('data-cursor-bound')) {
                        el.addEventListener('mouseenter', () => document.body.classList.add('hovering'));
                        el.addEventListener('mouseleave', () => document.body.classList.remove('hovering'));
                        el.setAttribute('data-cursor-bound', 'true');
                    }
                });
            }
            addHoverTargets();
        }
    </script>
<?php wp_footer(); ?>
</body>
</html>