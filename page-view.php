<?php
/**
 * Renders one admin-created "structured page" at /{slug}.
 * Routed here by .htaccess as a last resort, only when nothing else
 * (a real file/folder, or a more specific rewrite rule) matched.
 *
 * Reads from the JSON snapshot written by /wp/admin/pages.php instead
 * of booting WordPress, so a plain content page stays fast and keeps
 * working even if WordPress or its database is having a bad moment.
 */
$slug = isset($_GET['slug']) ? preg_replace('/[^a-z0-9-]/', '', strtolower($_GET['slug'])) : '';
$path = __DIR__ . '/data/pages/' . $slug . '.json';

if ($slug === '' || !file_exists($path)) {
    http_response_code(404);
    include __DIR__ . '/404/index.html';
    exit;
}

$page = json_decode(file_get_contents($path), true);
if (!is_array($page)) {
    http_response_code(404);
    include __DIR__ . '/404/index.html';
    exit;
}

require_once __DIR__ . '/partials/render-sections.php';

$title = $page['title'] ?? '';
$hero_heading = $page['hero_heading'] ?? $title;
$hero_subheading = $page['hero_subheading'] ?? '';
$hero_image = $page['hero_image'] ?? '';
$meta_description = $page['meta_description'] ?? '';
$sections = $page['sections'] ?? [];
?>
<!DOCTYPE html>
<html lang="en" class="scroll-smooth">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title><?php echo htmlspecialchars($title); ?> | GetOnline Studio</title>
    <?php if ($meta_description): ?><meta name="description" content="<?php echo htmlspecialchars($meta_description); ?>"><?php endif; ?>

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
</head>
<body class="bg-matte-black bg-noise font-manrope selection:bg-sharp-purple selection:text-white relative" style="--accent: #7e22ce;">

    <div class="cursor-dot"></div>
    <div class="cursor-outline"></div>

    <?php include __DIR__ . '/partials/header.php'; ?>

    <header class="relative pt-32 pb-16 px-4 md:px-6 text-center border-b border-lavender/10">
        <h1 class="font-syne text-[9vw] md:text-[6vw] leading-[0.95] font-bold text-lavender reveal-up"><?php echo htmlspecialchars($hero_heading); ?></h1>
        <?php if ($hero_subheading): ?>
            <p class="font-manrope text-lavender/70 text-lg max-w-2xl mx-auto mt-8 leading-relaxed reveal-up"><?php echo htmlspecialchars($hero_subheading); ?></p>
        <?php endif; ?>
    </header>

    <?php if ($hero_image): ?>
        <div class="px-4 md:px-6 -mt-4 relative z-20">
            <img src="<?php echo htmlspecialchars($hero_image); ?>" alt="<?php echo htmlspecialchars($hero_heading); ?>" class="w-full max-w-5xl mx-auto rounded-2xl border border-lavender/10">
        </div>
    <?php endif; ?>

    <section class="py-16 md:py-24 px-4 md:px-6 max-w-3xl mx-auto">
        <?php go_render_sections($sections); ?>
    </section>

    <?php include __DIR__ . '/partials/footer.php'; ?>

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
