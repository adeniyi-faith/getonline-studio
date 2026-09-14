<?php
/**
 * Renders one admin-created portfolio project at /work/{slug}.
 * Routed here by .htaccess whenever the slug doesn't match one of the
 * hand-built case-study folders under /work/.
 */
define('WP_USE_THEMES', false);
require_once __DIR__ . '/wp/wp-load.php';
require_once __DIR__ . '/partials/render-sections.php';

$slug = isset($_GET['slug']) ? sanitize_title($_GET['slug']) : '';
$project = $slug ? get_page_by_path($slug, OBJECT, 'portfolio_project') : null;

if (!$project || $project->post_status !== 'publish') {
    http_response_code(404);
    include __DIR__ . '/404/index.html';
    exit;
}

$post_id = $project->ID;
$title = $project->post_title;
$client = get_post_meta($post_id, 'go_client', true);
$summary = get_post_meta($post_id, 'go_summary', true);
$cover_image = get_post_meta($post_id, 'go_cover_image', true);
$tags = array_filter(array_map('trim', explode(',', (string) get_post_meta($post_id, 'go_tags', true))));
$external_link = get_post_meta($post_id, 'go_external_link', true);
$accent_hex = get_post_meta($post_id, 'go_accent_hex', true) ?: '#7e22ce';
$sections = get_post_meta($post_id, 'go_sections', true) ?: [];

// Prev/next, computed from the same JSON the /work grid uses so the
// order always matches what visitors see there.
$prev = null;
$next = null;
$portfolio_json = __DIR__ . '/data/portfolio.json';
if (file_exists($portfolio_json)) {
    $all = json_decode(file_get_contents($portfolio_json), true) ?: [];
    $index = null;
    foreach ($all as $i => $row) {
        if ($row['slug'] === $slug) { $index = $i; break; }
    }
    if ($index !== null && count($all) > 1) {
        $prev_row = $all[($index - 1 + count($all)) % count($all)];
        $next_row = $all[($index + 1) % count($all)];
        $prev = ['href' => '/work/' . $prev_row['slug'], 'label' => $prev_row['title']];
        $next = ['href' => '/work/' . $next_row['slug'], 'label' => $next_row['title']];
    }
}
?>
<!DOCTYPE html>
<html lang="en" class="scroll-smooth">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title><?php echo htmlspecialchars($title); ?> | GetOnline Studio</title>
    <?php if ($summary): ?><meta name="description" content="<?php echo htmlspecialchars($summary); ?>"><?php endif; ?>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Manrope:wght@300;400;600&family=Syne:wght@400;700;800&display=swap" rel="stylesheet">

    <link rel="stylesheet" href="/assets/css/theme.css">
    <script src="/assets/js/theme.js"></script>

    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: { ...GO_COLORS },
                    fontFamily: { ...GO_FONTS },
                    backgroundImage: { 'noise': GO_NOISE_BG },
                }
            }
        }
    </script>
    <style>
        /* This project's admin-chosen accent color drives the cursor and
           every [var(--accent)] utility used by the section renderer. */
        .cursor-dot { background-color: var(--accent); }
        .cursor-outline { border-color: var(--accent); }
        body.hovering .cursor-outline { background-color: color-mix(in srgb, var(--accent) 20%, transparent); }
    </style>
</head>
<body class="bg-matte-black bg-noise font-manrope selection:text-white relative" style="--accent: <?php echo htmlspecialchars($accent_hex); ?>;">

    <div class="cursor-dot"></div>
    <div class="cursor-outline"></div>

    <?php $go_accent_class = '[var(--accent)]'; include __DIR__ . '/partials/header.php'; ?>

    <header class="relative pt-32 pb-16 px-4 md:px-6 text-center border-b border-lavender/10">
        <?php if ($client): ?>
            <p class="font-manrope text-[var(--accent)] uppercase tracking-[0.3em] text-sm mb-6 reveal-up"><?php echo htmlspecialchars($client); ?></p>
        <?php endif; ?>
        <h1 class="font-syne text-[9vw] md:text-[6vw] leading-[0.95] font-bold text-lavender reveal-up"><?php echo htmlspecialchars($title); ?></h1>
        <?php if ($summary): ?>
            <p class="font-manrope text-lavender/70 text-lg max-w-2xl mx-auto mt-8 leading-relaxed reveal-up"><?php echo htmlspecialchars($summary); ?></p>
        <?php endif; ?>
        <?php if (!empty($tags)): ?>
            <div class="flex flex-wrap justify-center gap-2 mt-6">
                <?php foreach ($tags as $tag): ?>
                    <span class="px-3 py-1 border border-lavender/30 rounded-full text-[10px] uppercase tracking-widest text-lavender/70"><?php echo htmlspecialchars($tag); ?></span>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
        <?php if ($external_link): ?>
            <a href="<?php echo htmlspecialchars($external_link); ?>" target="_blank" rel="noopener" class="inline-flex items-center gap-2 mt-8 text-sm uppercase tracking-widest border border-lavender/30 px-5 py-2.5 rounded-full hover:bg-lavender hover:text-matte-black transition-all">
                Visit Live Site &rarr;
            </a>
        <?php endif; ?>
    </header>

    <?php if ($cover_image): ?>
        <div class="px-4 md:px-6 -mt-4 relative z-20">
            <img src="<?php echo htmlspecialchars($cover_image); ?>" alt="<?php echo htmlspecialchars($title); ?>" class="w-full max-w-5xl mx-auto rounded-2xl border border-lavender/10">
        </div>
    <?php endif; ?>

    <section class="py-16 md:py-24 px-4 md:px-6 max-w-3xl mx-auto">
        <?php go_render_sections($sections); ?>
    </section>

    <?php if ($prev && $next):
        $go_prev = $prev;
        $go_next = $next;
        include __DIR__ . '/partials/project-nav.php';
    endif; ?>

    <?php $go_accent_class = '[var(--accent)]'; include __DIR__ . '/partials/footer.php'; ?>

    <script>
        const cursorDot = document.querySelector('.cursor-dot');
        const cursorOutline = document.querySelector('.cursor-outline');
        const isTouchDevice = 'ontouchstart' in window || navigator.maxTouchPoints > 0;
        if (!isTouchDevice) {
            let mouseX = 0, mouseY = 0, outlineX = 0, outlineY = 0;
            window.addEventListener('mousemove', (e) => {
                mouseX = e.clientX; mouseY = e.clientY;
                cursorDot.style.transform = `translate(${mouseX}px, ${mouseY}px) translate(-50%, -50%)`;
            });
            const animateCursor = () => {
                outlineX += (mouseX - outlineX) * 0.15;
                outlineY += (mouseY - outlineY) * 0.15;
                cursorOutline.style.transform = `translate(${outlineX}px, ${outlineY}px) translate(-50%, -50%)`;
                requestAnimationFrame(animateCursor);
            };
            animateCursor();
            document.querySelectorAll('.hover-target').forEach(el => {
                el.addEventListener('mouseenter', () => document.body.classList.add('hovering'));
                el.addEventListener('mouseleave', () => document.body.classList.remove('hovering'));
            });
        }
    </script>
</body>
</html>
