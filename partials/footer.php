<?php
/**
 * Shared "LET'S TALK" footer used on every inner page.
 *
 * Optional variables a page can set before including this file:
 *   $go_accent_class — Tailwind color class for the email link and
 *                       social hovers (defaults to the brand purple).
 *                       Case-study pages pass their project accent.
 *   $go_footer_extra_class — extra classes appended to the <footer>
 *                       tag (e.g. a page that needs extra top margin).
 */
$go_accent_class = $go_accent_class ?? 'sharp-purple';
$go_footer_extra_class = $go_footer_extra_class ?? '';
?>
<footer class="py-12 md:py-20 px-4 md:px-6 bg-matte-black flex flex-col md:flex-row justify-between items-start md:items-end border-t border-lavender/20 relative z-20 <?php echo htmlspecialchars($go_footer_extra_class); ?>">
    <div class="w-full md:w-auto">
        <h2 class="font-syne text-5xl md:text-8xl mb-4 md:mb-6 text-lavender">LET'S TALK</h2>
        <a href="mailto:hello@getonlinestudio.com" class="text-lg md:text-2xl text-<?php echo $go_accent_class; ?> hover:text-white transition-colors font-manrope underline decoration-1 underline-offset-8 break-all hover-target">hello@getonlinestudio.com</a>
    </div>
    <div class="mt-10 md:mt-0 flex flex-wrap gap-6 font-manrope text-xs md:text-sm uppercase tracking-widest w-full md:w-auto">
        <a href="#" class="hover:text-<?php echo $go_accent_class; ?> hover-target">Instagram</a>
        <a href="#" class="hover:text-<?php echo $go_accent_class; ?> hover-target">Twitter</a>
        <a href="#" class="hover:text-<?php echo $go_accent_class; ?> hover-target">LinkedIn</a>
    </div>
</footer>
