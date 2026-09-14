<?php
/**
 * Shared site navigation, included on every inner page. Reads the
 * menu + contact details from data/site-settings.json (written by
 * /wp/admin/settings.php) instead of booting WordPress, so this stays
 * fast on pages that have nothing else to do with WordPress. Falls
 * back to sane defaults if that file doesn't exist yet.
 *
 * Optional variables a page can set before including this file:
 *   $go_accent_class — Tailwind color class for hover states
 *                       (defaults to the brand purple). Case-study
 *                       pages that use a per-project accent color
 *                       (e.g. 'dek-accent', '[var(--accent)]') set
 *                       this before the include.
 */
$go_accent_class = $go_accent_class ?? 'sharp-purple';

$go_settings = null;
$go_settings_path = $_SERVER['DOCUMENT_ROOT'] . '/data/site-settings.json';
if (is_file($go_settings_path)) {
    $go_settings = json_decode(file_get_contents($go_settings_path), true);
}

$go_nav_items = (!empty($go_settings['nav_items']) && is_array($go_settings['nav_items']))
    ? $go_settings['nav_items']
    : [
        ['label' => 'Work',      'url' => '/work'],
        ['label' => 'Services',  'url' => '/services'],
        ['label' => 'About',     'url' => '/about'],
        ['label' => 'Locations', 'url' => '/locations/'],
        ['label' => 'Contact',   'url' => '/contact'],
    ];

$go_whatsapp = $go_settings['whatsapp_primary'] ?? '2348108275013';
$go_email = $go_settings['contact_email'] ?? 'hello@getonlinestudio.com';
?>
<div id="go-mobile-menu" class="fixed inset-0 bg-sharp-purple z-50 transform translate-x-full flex flex-col justify-center items-center text-center">
    <button id="go-close-menu" class="absolute top-6 right-6 text-matte-black font-syne font-bold text-xl p-4 hover-target">CLOSE</button>
    <nav class="flex flex-col gap-6">
        <?php foreach ($go_nav_items as $item): ?>
            <a href="<?php echo htmlspecialchars($item['url']); ?>" class="font-syne text-5xl md:text-7xl text-matte-black font-bold hover:text-white transition-colors hover-target"><?php echo htmlspecialchars(strtoupper($item['label'])); ?></a>
        <?php endforeach; ?>
    </nav>
    <div class="absolute bottom-10 flex gap-6 font-mono text-xs text-matte-black/60 uppercase tracking-widest">
        <a href="https://wa.me/<?php echo htmlspecialchars($go_whatsapp); ?>" target="_blank" class="hover:text-matte-black transition-colors">WhatsApp</a>
        <a href="mailto:<?php echo htmlspecialchars($go_email); ?>" class="hover:text-matte-black transition-colors">Email</a>
    </div>
</div>

<nav class="fixed top-0 w-full z-40 px-4 md:px-6 py-4 md:py-6 flex justify-between items-center mix-blend-difference text-lavender">
    <a href="/" class="font-syne font-bold text-xl md:text-2xl hover:text-<?php echo $go_accent_class; ?> transition-colors hover-target">GO.</a>
    <button id="go-open-menu" class="text-xs md:text-sm font-bold tracking-widest uppercase border border-lavender px-4 md:px-6 py-2 rounded-full hover:bg-lavender hover:text-matte-black transition-all duration-300 bg-matte-black/20 backdrop-blur-sm hover-target">
        Menu
    </button>
</nav>

<script>
    (function () {
        var menu = document.getElementById('go-mobile-menu');
        var openBtn = document.getElementById('go-open-menu');
        var closeBtn = document.getElementById('go-close-menu');
        if (!menu || !openBtn || !closeBtn) return;
        openBtn.addEventListener('click', function () {
            menu.classList.remove('translate-x-full');
            document.body.style.overflow = 'hidden';
        });
        closeBtn.addEventListener('click', function () {
            menu.classList.add('translate-x-full');
            document.body.style.overflow = '';
        });
    })();
</script>
