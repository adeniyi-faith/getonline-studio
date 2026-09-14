<?php
/**
 * Site Pages manager — create/edit/delete simple structured pages
 * (a hero + a stack of content blocks) that appear at /{slug}
 * automatically, rendered by /page-view.php. To put a new page in the
 * site menu, add it from Navigation & Settings once it's published.
 */
require_once __DIR__ . '/_auth.php';

$admin_message = '';
$admin_error = '';

function go_pages_dir() {
    $dir = __DIR__ . '/../../data/pages';
    if (!is_dir($dir)) {
        mkdir($dir, 0755, true);
    }
    return $dir;
}

/** Write (or remove) the public JSON snapshot for one page. */
function go_sync_page_json($post_id, $slug, $publish) {
    $path = go_pages_dir() . '/' . $slug . '.json';

    if (!$publish) {
        if (file_exists($path)) unlink($path);
        return;
    }

    $data = [
        'title'             => get_the_title($post_id),
        'slug'              => $slug,
        'hero_heading'      => get_post_meta($post_id, 'go_hero_heading', true),
        'hero_subheading'   => get_post_meta($post_id, 'go_hero_subheading', true),
        'hero_image'        => get_post_meta($post_id, 'go_hero_image', true),
        'meta_description'  => get_post_meta($post_id, 'go_meta_description', true),
        'sections'          => get_post_meta($post_id, 'go_sections', true) ?: [],
    ];
    file_put_contents($path, json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));
}

// ---------------------------------------------------------------
// Handle form submission
// ---------------------------------------------------------------
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['go_action'])) {

    if ($_POST['go_action'] === 'save' && check_admin_referer('go_save_page')) {
        $title = sanitize_text_field($_POST['title'] ?? '');
        if ($title === '') {
            $admin_error = 'A page title is required.';
        } else {
            $post_id = isset($_POST['post_id']) ? (int) $_POST['post_id'] : 0;
            $is_publish = isset($_POST['publish']);

            $post_data = [
                'post_title'  => $title,
                'post_type'   => 'site_page',
                'post_status' => $is_publish ? 'publish' : 'draft',
            ];
            if ($post_id) {
                $post_data['ID'] = $post_id;
                // Keep the original slug stable once a page is created, so
                // published links never silently break when the title changes.
            } else {
                $post_data['post_name'] = sanitize_title($_POST['slug'] ?: $title);
            }

            $old_slug = $post_id ? get_post_field('post_name', $post_id) : '';
            $post_id = $post_id ? wp_update_post($post_data) : wp_insert_post($post_data);

            if (is_wp_error($post_id) || !$post_id) {
                $admin_error = 'Could not save the page. Please try again.';
            } else {
                update_post_meta($post_id, 'go_hero_heading', sanitize_text_field($_POST['hero_heading'] ?? ''));
                update_post_meta($post_id, 'go_hero_subheading', sanitize_textarea_field($_POST['hero_subheading'] ?? ''));
                update_post_meta($post_id, 'go_hero_image', esc_url_raw($_POST['hero_image'] ?? ''));
                update_post_meta($post_id, 'go_meta_description', sanitize_textarea_field($_POST['meta_description'] ?? ''));

                $sections = json_decode(stripslashes($_POST['sections_json'] ?? '[]'), true);
                update_post_meta($post_id, 'go_sections', is_array($sections) ? $sections : []);

                $slug = get_post_field('post_name', $post_id);
                if ($old_slug && $old_slug !== $slug) {
                    go_sync_page_json($post_id, $old_slug, false);
                }
                go_sync_page_json($post_id, $slug, $is_publish);

                wp_redirect('/wp/admin/pages.php?saved=1');
                exit;
            }
        }
    }

    if ($_POST['go_action'] === 'delete' && check_admin_referer('go_delete_page')) {
        $post_id = (int) ($_POST['post_id'] ?? 0);
        if ($post_id) {
            $slug = get_post_field('post_name', $post_id);
            wp_delete_post($post_id, true);
            if ($slug) go_sync_page_json($post_id, $slug, false);
        }
        wp_redirect('/wp/admin/pages.php?deleted=1');
        exit;
    }
}

if (isset($_GET['saved'])) $admin_message = 'Page saved.';
if (isset($_GET['deleted'])) $admin_message = 'Page deleted.';

// ---------------------------------------------------------------
// Load state: either the edit form for one page, or the list
// ---------------------------------------------------------------
$editing = null;
if (isset($_GET['edit'])) {
    $editing = get_post((int) $_GET['edit']);
    if (!$editing || $editing->post_type !== 'site_page') {
        $editing = null;
    }
}

$page_title = $editing ? 'Edit Page' : 'Site Pages';
$active = 'pages';
include __DIR__ . '/_layout_top.php';
?>

<?php if ($editing !== null || isset($_GET['new'])):
    $post_id = $editing->ID ?? 0;
    $title = $editing->post_title ?? '';
    $slug = $post_id ? $editing->post_name : '';
    $hero_heading = $post_id ? get_post_meta($post_id, 'go_hero_heading', true) : '';
    $hero_subheading = $post_id ? get_post_meta($post_id, 'go_hero_subheading', true) : '';
    $hero_image = $post_id ? get_post_meta($post_id, 'go_hero_image', true) : '';
    $meta_description = $post_id ? get_post_meta($post_id, 'go_meta_description', true) : '';
    $sections = $post_id ? get_post_meta($post_id, 'go_sections', true) : [];
    $is_published = $post_id ? ($editing->post_status === 'publish') : false;
?>
    <a href="/wp/admin/pages.php" class="text-sm text-lavender/50 hover:text-lavender mb-6 inline-block">&larr; Back to all pages</a>

    <form id="content-form" method="post" class="max-w-3xl space-y-6">
        <?php echo wp_nonce_field('go_save_page', '_wpnonce', true, false); ?>
        <input type="hidden" name="go_action" value="save">
        <?php if ($post_id): ?><input type="hidden" name="post_id" value="<?php echo (int) $post_id; ?>"><?php endif; ?>

        <div>
            <label class="go-label">Page title</label>
            <input type="text" name="title" required value="<?php echo htmlspecialchars($title); ?>" class="go-input" placeholder="Refund Policy">
        </div>

        <?php if (!$post_id): ?>
        <div>
            <label class="go-label">URL (leave blank to auto-generate from the title)</label>
            <div class="flex items-center gap-2">
                <span class="text-lavender/40 text-sm">getonlinestudio.com/</span>
                <input type="text" name="slug" value="" class="go-input" placeholder="refund-policy">
            </div>
        </div>
        <?php else: ?>
        <div>
            <label class="go-label">URL</label>
            <p class="go-input bg-transparent border-dashed text-lavender/50">getonlinestudio.com/<?php echo htmlspecialchars($slug); ?></p>
        </div>
        <?php endif; ?>

        <div>
            <label class="go-label">Hero heading</label>
            <input type="text" name="hero_heading" value="<?php echo htmlspecialchars($hero_heading); ?>" class="go-input" placeholder="Refund Policy">
        </div>

        <div>
            <label class="go-label">Hero subheading</label>
            <textarea name="hero_subheading" rows="2" class="go-input"><?php echo htmlspecialchars($hero_subheading); ?></textarea>
        </div>

        <div>
            <label class="go-label">Hero image URL (optional)</label>
            <input type="url" name="hero_image" value="<?php echo htmlspecialchars($hero_image); ?>" class="go-input" placeholder="https://...">
        </div>

        <div>
            <label class="go-label">Meta description (for search engines, optional)</label>
            <textarea name="meta_description" rows="2" class="go-input"><?php echo htmlspecialchars($meta_description); ?></textarea>
        </div>

        <div class="flex items-center gap-2">
            <input type="checkbox" id="publish" name="publish" <?php echo $is_published ? 'checked' : ''; ?> class="w-4 h-4">
            <label for="publish" class="text-sm text-lavender/70">Published (visible on the live site)</label>
        </div>

        <div class="border-t border-lavender/10 pt-6">
            <div class="flex items-center justify-between mb-3">
                <h3 class="font-syne text-lg">Page content</h3>
                <select id="add-section-type" class="go-input w-auto">
                    <option value="heading">Heading</option>
                    <option value="paragraph">Paragraph</option>
                    <option value="image">Image</option>
                    <option value="quote">Quote</option>
                    <option value="stat_row">Stat row</option>
                    <option value="faq">FAQ group</option>
                </select>
            </div>
            <button type="button" id="add-section-btn" class="text-xs uppercase tracking-widest border border-lavender/30 px-4 py-2 rounded-full hover:bg-lavender hover:text-matte-black transition-all mb-4">+ Add block</button>
            <div id="sections-list"></div>
            <input type="hidden" name="sections_json" id="sections-json">
        </div>

        <div class="flex gap-3 pt-4">
            <button type="submit" class="bg-sharp-purple text-white px-6 py-3 rounded-full text-sm font-bold uppercase tracking-widest hover:bg-white hover:text-matte-black transition-all">Save Page</button>
            <a href="/wp/admin/pages.php" class="px-6 py-3 rounded-full text-sm border border-lavender/20 hover:bg-white/5 transition-all">Cancel</a>
        </div>
    </form>

    <script src="/wp/admin/_sections-builder.js"></script>
    <script>
        GoSectionsBuilder.init({
            listEl: document.getElementById('sections-list'),
            hiddenInputEl: document.getElementById('sections-json'),
            formEl: document.getElementById('content-form'),
            initial: <?php echo wp_json_encode(is_array($sections) ? $sections : []); ?>,
        });
        document.getElementById('add-section-btn').addEventListener('click', function () {
            var type = document.getElementById('add-section-type').value;
            GoSectionsBuilder.addRow(type, {});
        });
    </script>

<?php else: ?>

    <div class="flex justify-end mb-6">
        <a href="/wp/admin/pages.php?new=1" class="bg-sharp-purple text-white px-5 py-2.5 rounded-full text-sm font-bold uppercase tracking-widest hover:bg-white hover:text-matte-black transition-all">+ New Page</a>
    </div>

    <?php
    $pages = get_posts([
        'post_type'   => 'site_page',
        'post_status' => 'any',
        'numberposts' => -1,
        'orderby'     => 'title',
        'order'       => 'ASC',
    ]);
    ?>

    <?php if (empty($pages)): ?>
        <p class="text-lavender/50">No pages yet. Click "New Page" to add your first one.</p>
    <?php else: ?>
        <div class="border border-lavender/10 rounded-xl overflow-hidden">
            <table class="w-full text-sm">
                <thead class="bg-white/[0.03] text-left text-lavender/50 uppercase text-xs tracking-widest">
                    <tr>
                        <th class="px-5 py-3">Page</th>
                        <th class="px-5 py-3">Status</th>
                        <th class="px-5 py-3">URL</th>
                        <th class="px-5 py-3 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-lavender/10">
                    <?php foreach ($pages as $p): ?>
                        <tr>
                            <td class="px-5 py-4 font-syne"><?php echo htmlspecialchars($p->post_title); ?></td>
                            <td class="px-5 py-4">
                                <span class="text-xs px-2 py-1 rounded-full <?php echo $p->post_status === 'publish' ? 'bg-green-500/10 text-green-400' : 'bg-yellow-500/10 text-yellow-400'; ?>">
                                    <?php echo htmlspecialchars(ucfirst($p->post_status)); ?>
                                </span>
                            </td>
                            <td class="px-5 py-4 text-lavender/40">/<?php echo htmlspecialchars($p->post_name); ?></td>
                            <td class="px-5 py-4 text-right space-x-3">
                                <a href="/wp/admin/pages.php?edit=<?php echo (int) $p->ID; ?>" class="text-sharp-purple hover:text-white">Edit</a>
                                <form method="post" class="inline" onsubmit="return confirm('Delete this page? This cannot be undone.');">
                                    <?php echo wp_nonce_field('go_delete_page', '_wpnonce', true, false); ?>
                                    <input type="hidden" name="go_action" value="delete">
                                    <input type="hidden" name="post_id" value="<?php echo (int) $p->ID; ?>">
                                    <button type="submit" class="text-red-400/80 hover:text-red-400">Delete</button>
                                </form>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php endif; ?>

<?php endif; ?>

<?php include __DIR__ . '/_layout_bottom.php'; ?>
