<?php
/**
 * Navigation & Settings — controls the menu shown on every page (see
 * partials/header.php) and the contact/social details used in the
 * footer. Saves to the go_site_settings option and mirrors a copy to
 * data/site-settings.json, which is what the public pages actually
 * read (so a page rendering never has to boot WordPress just to know
 * the menu).
 */
require_once __DIR__ . '/_auth.php';

$admin_message = '';
$admin_error = '';

function go_write_settings_json($settings) {
    $dir = __DIR__ . '/../../data';
    if (!is_dir($dir)) {
        mkdir($dir, 0755, true);
    }
    file_put_contents($dir . '/site-settings.json', json_encode($settings, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && check_admin_referer('go_save_settings')) {
    $labels = $_POST['nav_label'] ?? [];
    $urls = $_POST['nav_url'] ?? [];
    $orders = $_POST['nav_order'] ?? [];

    $nav_items = [];
    foreach ($labels as $i => $label) {
        $label = sanitize_text_field($label);
        $url = sanitize_text_field($urls[$i] ?? '');
        if ($label === '' || $url === '') {
            continue;
        }
        $nav_items[] = [
            'label' => $label,
            'url'   => $url,
            'order' => (int) ($orders[$i] ?? 0),
        ];
    }
    usort($nav_items, function ($a, $b) { return $a['order'] <=> $b['order']; });

    $settings = [
        'nav_items'          => $nav_items,
        'contact_email'      => sanitize_email($_POST['contact_email'] ?? ''),
        'whatsapp_primary'   => preg_replace('/[^0-9]/', '', $_POST['whatsapp_primary'] ?? ''),
        'whatsapp_secondary' => preg_replace('/[^0-9]/', '', $_POST['whatsapp_secondary'] ?? ''),
        'social' => [
            'instagram' => esc_url_raw($_POST['social_instagram'] ?? ''),
            'twitter'   => esc_url_raw($_POST['social_twitter'] ?? ''),
            'linkedin'  => esc_url_raw($_POST['social_linkedin'] ?? ''),
        ],
    ];

    update_option('go_site_settings', $settings);
    go_write_settings_json($settings);

    $admin_message = 'Settings saved. The site menu and footer are updated everywhere.';
}

$settings = get_option('go_site_settings', []);
$nav_items = $settings['nav_items'] ?? [];
if (empty($nav_items)) {
    $nav_items = [['label' => '', 'url' => '', 'order' => 10]];
}

$page_title = 'Navigation & Settings';
$active = 'settings';
include __DIR__ . '/_layout_top.php';
?>

<form method="post" class="max-w-3xl space-y-10">
    <?php echo wp_nonce_field('go_save_settings', '_wpnonce', true, false); ?>

    <section>
        <h2 class="font-syne text-xl mb-1">Site navigation</h2>
        <p class="text-sm text-lavender/50 mb-4">Shown on every page. Drag order isn't supported yet — use the order number (lower shows first).</p>
        <div id="nav-rows" class="space-y-3">
            <?php foreach ($nav_items as $item): ?>
                <div class="nav-row flex flex-wrap gap-3 items-center border border-lavender/10 rounded-lg p-3">
                    <input type="text" name="nav_label[]" value="<?php echo htmlspecialchars($item['label']); ?>" placeholder="Label" class="go-input flex-1 min-w-[120px]">
                    <input type="text" name="nav_url[]" value="<?php echo htmlspecialchars($item['url']); ?>" placeholder="/work" class="go-input flex-[2] min-w-[160px]">
                    <input type="number" name="nav_order[]" value="<?php echo (int) $item['order']; ?>" placeholder="Order" class="go-input w-24">
                    <button type="button" class="nav-row-remove text-red-400/70 hover:text-red-400 text-xs px-2">Remove</button>
                </div>
            <?php endforeach; ?>
        </div>
        <button type="button" id="nav-row-add" class="mt-3 text-xs uppercase tracking-widest border border-lavender/30 px-4 py-2 rounded-full hover:bg-lavender hover:text-matte-black transition-all">+ Add menu item</button>
    </section>

    <section class="border-t border-lavender/10 pt-8">
        <h2 class="font-syne text-xl mb-4">Contact details</h2>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div>
                <label class="go-label">Contact email</label>
                <input type="email" name="contact_email" value="<?php echo htmlspecialchars($settings['contact_email'] ?? ''); ?>" class="go-input">
            </div>
            <div></div>
            <div>
                <label class="go-label">WhatsApp — primary (digits only, with country code)</label>
                <input type="text" name="whatsapp_primary" value="<?php echo htmlspecialchars($settings['whatsapp_primary'] ?? ''); ?>" class="go-input" placeholder="2348108275013">
            </div>
            <div>
                <label class="go-label">WhatsApp — secondary</label>
                <input type="text" name="whatsapp_secondary" value="<?php echo htmlspecialchars($settings['whatsapp_secondary'] ?? ''); ?>" class="go-input" placeholder="2348080732660">
            </div>
        </div>
    </section>

    <section class="border-t border-lavender/10 pt-8">
        <h2 class="font-syne text-xl mb-4">Social links</h2>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <div>
                <label class="go-label">Instagram URL</label>
                <input type="url" name="social_instagram" value="<?php echo htmlspecialchars($settings['social']['instagram'] ?? ''); ?>" class="go-input">
            </div>
            <div>
                <label class="go-label">Twitter / X URL</label>
                <input type="url" name="social_twitter" value="<?php echo htmlspecialchars($settings['social']['twitter'] ?? ''); ?>" class="go-input">
            </div>
            <div>
                <label class="go-label">LinkedIn URL</label>
                <input type="url" name="social_linkedin" value="<?php echo htmlspecialchars($settings['social']['linkedin'] ?? ''); ?>" class="go-input">
            </div>
        </div>
    </section>

    <button type="submit" class="bg-sharp-purple text-white px-6 py-3 rounded-full text-sm font-bold uppercase tracking-widest hover:bg-white hover:text-matte-black transition-all">Save Settings</button>
</form>

<script>
    document.getElementById('nav-row-add').addEventListener('click', function () {
        const wrap = document.getElementById('nav-rows');
        const row = document.createElement('div');
        row.className = 'nav-row flex flex-wrap gap-3 items-center border border-lavender/10 rounded-lg p-3';
        row.innerHTML = `
            <input type="text" name="nav_label[]" placeholder="Label" class="go-input flex-1 min-w-[120px]">
            <input type="text" name="nav_url[]" placeholder="/work" class="go-input flex-[2] min-w-[160px]">
            <input type="number" name="nav_order[]" placeholder="Order" class="go-input w-24">
            <button type="button" class="nav-row-remove text-red-400/70 hover:text-red-400 text-xs px-2">Remove</button>
        `;
        row.querySelector('.nav-row-remove').addEventListener('click', () => row.remove());
        wrap.appendChild(row);
    });
    document.querySelectorAll('.nav-row-remove').forEach(function (btn) {
        btn.addEventListener('click', function () { btn.closest('.nav-row').remove(); });
    });
</script>

<?php include __DIR__ . '/_layout_bottom.php'; ?>
