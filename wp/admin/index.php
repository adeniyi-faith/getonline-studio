<?php
/**
 * Admin dashboard home — quick overview + links into the rest of the
 * command center.
 */
require_once __DIR__ . '/_auth.php';

$portfolio_count = wp_count_posts('portfolio_project');
$pages_count = wp_count_posts('site_page');

$page_title = 'Dashboard';
$active = 'dashboard';
include __DIR__ . '/_layout_top.php';
?>

<p class="text-lavender/60 max-w-2xl mb-10">
    This is where you manage the frontend of getonlinestudio.com — add portfolio
    case studies, create new pages, and control the navigation menu and contact
    details shown across the site. No code required.
</p>

<div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-10">
    <a href="/wp/admin/portfolio.php" class="block border border-lavender/10 rounded-xl p-6 hover:border-sharp-purple/50 hover:bg-white/[0.02] transition-all">
        <div class="text-3xl font-syne font-bold mb-1"><?php echo (int) ($portfolio_count->publish ?? 0); ?></div>
        <div class="text-lavender/50 text-sm mb-4">Published portfolio projects</div>
        <span class="text-xs uppercase tracking-widest text-sharp-purple">Manage Portfolio &rarr;</span>
    </a>
    <a href="/wp/admin/pages.php" class="block border border-lavender/10 rounded-xl p-6 hover:border-sharp-purple/50 hover:bg-white/[0.02] transition-all">
        <div class="text-3xl font-syne font-bold mb-1"><?php echo (int) ($pages_count->publish ?? 0); ?></div>
        <div class="text-lavender/50 text-sm mb-4">Published site pages</div>
        <span class="text-xs uppercase tracking-widest text-sharp-purple">Manage Pages &rarr;</span>
    </a>
    <a href="/wp/admin/settings.php" class="block border border-lavender/10 rounded-xl p-6 hover:border-sharp-purple/50 hover:bg-white/[0.02] transition-all">
        <div class="text-3xl font-syne font-bold mb-1"><i data-lucide="sliders-horizontal" class="w-7 h-7"></i></div>
        <div class="text-lavender/50 text-sm mb-4">Site menu, contact info, socials</div>
        <span class="text-xs uppercase tracking-widest text-sharp-purple">Open Settings &rarr;</span>
    </a>
</div>

<div class="border border-lavender/10 rounded-xl p-6 max-w-2xl">
    <h2 class="font-syne text-lg mb-3">Looking for the city/service SEO pages?</h2>
    <p class="text-sm text-lavender/50 mb-4">Those are managed separately in the pSEO Command Center, which handles the location and niche landing pages across the site.</p>
    <a href="/wp/studio-admin.php" class="text-xs uppercase tracking-widest border border-lavender/30 px-4 py-2 rounded-full hover:bg-lavender hover:text-matte-black transition-all">Open pSEO Command Center</a>
</div>

<?php include __DIR__ . '/_layout_bottom.php'; ?>
