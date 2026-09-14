<?php
/**
 * Shared "Back Home" nav bar used on every inner page (about, contact,
 * work, service pages, case studies, etc). The homepage (index.php) has
 * its own full nav with the site menu and is not included here.
 *
 * Optional variables a page can set before including this file:
 *   $go_accent_class — Tailwind color class for the hover state
 *                       (defaults to the brand purple). Case-study
 *                       pages that use a per-project accent color
 *                       (e.g. 'dek-accent', 'kinetic-green') set this
 *                       before the include.
 */
$go_accent_class = $go_accent_class ?? 'sharp-purple';
?>
<nav class="fixed top-0 w-full z-40 px-4 md:px-6 py-4 md:py-6 flex justify-between items-center mix-blend-difference text-lavender">
    <a href="/" class="font-syne font-bold text-xl md:text-2xl hover:text-<?php echo $go_accent_class; ?> transition-colors hover-target">GO.</a>
    <a href="/" class="text-xs md:text-sm font-bold tracking-widest uppercase border border-lavender px-4 md:px-6 py-2 rounded-full hover:bg-lavender hover:text-matte-black transition-all duration-300 bg-matte-black/20 backdrop-blur-sm hover-target">
        Back Home
    </a>
</nav>
