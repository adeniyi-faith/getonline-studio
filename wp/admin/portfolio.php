<?php
/**
 * Portfolio manager — create/edit/delete case-study projects that show
 * up on /work and get their own page at /work/{slug} automatically,
 * rendered by /portfolio-view.php. No file creation required.
 */
require_once __DIR__ . '/_auth.php';

$admin_message = '';
$admin_error = '';

/**
 * Rebuild the public data/portfolio.json snapshot so /work and
 * portfolio-view.php never need to boot WordPress just to list cards.
 */
function go_rebuild_portfolio_json() {
    $posts = get_posts([
        'post_type'      => 'portfolio_project',
        'post_status'    => 'publish',
        'numberposts'    => -1,
        'orderby'        => 'meta_value_num',
        'meta_key'       => 'go_order',
        'order'          => 'ASC',
    ]);

    $rows = [];
    foreach ($posts as $p) {
        $rows[] = [
            'slug'          => $p->post_name,
            'title'         => $p->post_title,
            'client'        => get_post_meta($p->ID, 'go_client', true),
            'summary'       => get_post_meta($p->ID, 'go_summary', true),
            'cover_image'   => get_post_meta($p->ID, 'go_cover_image', true),
            'tags'          => array_filter(array_map('trim', explode(',', (string) get_post_meta($p->ID, 'go_tags', true)))),
            'external_link' => get_post_meta($p->ID, 'go_external_link', true),
            'accent_hex'    => get_post_meta($p->ID, 'go_accent_hex', true) ?: '#7e22ce',
            'order'         => (int) get_post_meta($p->ID, 'go_order', true),
        ];
    }

    $dir = __DIR__ . '/../../data';
    if (!is_dir($dir)) {
        mkdir($dir, 0755, true);
    }
    file_put_contents($dir . '/portfolio.json', json_encode($rows, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));
}

// ---------------------------------------------------------------
// Handle form submission
// ---------------------------------------------------------------
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['go_action'])) {

    if ($_POST['go_action'] === 'save' && check_admin_referer('go_save_portfolio')) {
        $title = sanitize_text_field($_POST['title'] ?? '');
        if ($title === '') {
            $admin_error = 'A project title is required.';
        } else {
            $post_id = isset($_POST['post_id']) ? (int) $_POST['post_id'] : 0;
            $post_data = [
                'post_title'  => $title,
                'post_type'   => 'portfolio_project',
                'post_status' => isset($_POST['publish']) ? 'publish' : 'draft',
            ];
            if ($post_id) {
                $post_data['ID'] = $post_id;
            }
            $post_id = $post_id ? wp_update_post($post_data) : wp_insert_post($post_data);

            if (is_wp_error($post_id) || !$post_id) {
                $admin_error = 'Could not save the project. Please try again.';
            } else {
                update_post_meta($post_id, 'go_client', sanitize_text_field($_POST['client'] ?? ''));
                update_post_meta($post_id, 'go_summary', sanitize_textarea_field($_POST['summary'] ?? ''));
                update_post_meta($post_id, 'go_cover_image', esc_url_raw($_POST['cover_image'] ?? ''));
                update_post_meta($post_id, 'go_tags', sanitize_text_field($_POST['tags'] ?? ''));
                update_post_meta($post_id, 'go_external_link', esc_url_raw($_POST['external_link'] ?? ''));
                $accent = preg_match('/^#[0-9a-fA-F]{6}$/', $_POST['accent_hex'] ?? '') ? $_POST['accent_hex'] : '#7e22ce';
                update_post_meta($post_id, 'go_accent_hex', $accent);
                update_post_meta($post_id, 'go_order', (int) ($_POST['order'] ?? 0));

                $sections = json_decode(stripslashes($_POST['sections_json'] ?? '[]'), true);
                update_post_meta($post_id, 'go_sections', is_array($sections) ? $sections : []);

                go_rebuild_portfolio_json();
                wp_redirect('/wp/admin/portfolio.php?saved=1');
                exit;
            }
        }
    }

    if ($_POST['go_action'] === 'delete' && check_admin_referer('go_delete_portfolio')) {
        $post_id = (int) ($_POST['post_id'] ?? 0);
        if ($post_id) {
            wp_delete_post($post_id, true);
            go_rebuild_portfolio_json();
        }
        wp_redirect('/wp/admin/portfolio.php?deleted=1');
        exit;
    }
}

if (isset($_GET['saved'])) $admin_message = 'Project saved and published to /work.';
if (isset($_GET['deleted'])) $admin_message = 'Project deleted.';

// ---------------------------------------------------------------
// Load state: either the edit form for one project, or the list
// ---------------------------------------------------------------
$editing = null;
if (isset($_GET['edit'])) {
    $editing = get_post((int) $_GET['edit']);
    if (!$editing || $editing->post_type !== 'portfolio_project') {
        $editing = null;
    }
}

$page_title = $editing ? 'Edit Project' : 'Portfolio';
$active = 'portfolio';
include __DIR__ . '/_layout_top.php';
?>

<?php if ($editing !== null || isset($_GET['new'])):
    $post_id = $editing->ID ?? 0;
    $title = $editing->post_title ?? '';
    $client = $post_id ? get_post_meta($post_id, 'go_client', true) : '';
    $summary = $post_id ? get_post_meta($post_id, 'go_summary', true) : '';
    $cover_image = $post_id ? get_post_meta($post_id, 'go_cover_image', true) : '';
    $tags = $post_id ? get_post_meta($post_id, 'go_tags', true) : '';
    $external_link = $post_id ? get_post_meta($post_id, 'go_external_link', true) : '';
    $accent_hex = $post_id ? (get_post_meta($post_id, 'go_accent_hex', true) ?: '#7e22ce') : '#7e22ce';
    $order = $post_id ? (int) get_post_meta($post_id, 'go_order', true) : 0;
    $sections = $post_id ? get_post_meta($post_id, 'go_sections', true) : [];
    $is_published = $post_id ? ($editing->post_status === 'publish') : true;
?>
    <a href="/wp/admin/portfolio.php" class="text-sm text-lavender/50 hover:text-lavender mb-6 inline-block">&larr; Back to all projects</a>

    <form id="content-form" method="post" class="max-w-3xl space-y-6">
        <?php echo wp_nonce_field('go_save_portfolio', '_wpnonce', true, false); ?>
        <input type="hidden" name="go_action" value="save">
        <?php if ($post_id): ?><input type="hidden" name="post_id" value="<?php echo (int) $post_id; ?>"><?php endif; ?>

        <div>
            <label class="go-label">Project title</label>
            <input type="text" name="title" required value="<?php echo htmlspecialchars($title); ?>" class="go-input" placeholder="RaffleKings">
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div>
                <label class="go-label">Client</label>
                <input type="text" name="client" value="<?php echo htmlspecialchars($client); ?>" class="go-input" placeholder="RaffleKings Ltd">
            </div>
            <div>
                <label class="go-label">Tags (comma separated)</label>
                <input type="text" name="tags" value="<?php echo htmlspecialchars($tags); ?>" class="go-input" placeholder="Web Design, Fintech">
            </div>
        </div>

        <div>
            <label class="go-label">Short summary (shown on the /work grid)</label>
            <textarea name="summary" rows="3" class="go-input"><?php echo htmlspecialchars($summary); ?></textarea>
        </div>

        <div>
            <label class="go-label">Cover image URL</label>
            <input type="url" name="cover_image" value="<?php echo htmlspecialchars($cover_image); ?>" class="go-input" placeholder="https://...">
            <p class="text-xs text-lavender/40 mt-1">Upload the image in the <a href="/wp/wp-admin/upload.php" target="_blank" class="underline hover:text-lavender">Media Library</a> first, then paste its URL here.</p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <div>
                <label class="go-label">Accent color</label>
                <input type="color" name="accent_hex" value="<?php echo htmlspecialchars($accent_hex); ?>" class="go-input h-11 p-1">
            </div>
            <div>
                <label class="go-label">External link (optional)</label>
                <input type="url" name="external_link" value="<?php echo htmlspecialchars($external_link); ?>" class="go-input" placeholder="https://client-site.com">
            </div>
            <div>
                <label class="go-label">Order (lower shows first)</label>
                <input type="number" name="order" value="<?php echo (int) $order; ?>" class="go-input">
            </div>
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
            <button type="submit" class="bg-sharp-purple text-white px-6 py-3 rounded-full text-sm font-bold uppercase tracking-widest hover:bg-white hover:text-matte-black transition-all">Save Project</button>
            <a href="/wp/admin/portfolio.php" class="px-6 py-3 rounded-full text-sm border border-lavender/20 hover:bg-white/5 transition-all">Cancel</a>
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
        <a href="/wp/admin/portfolio.php?new=1" class="bg-sharp-purple text-white px-5 py-2.5 rounded-full text-sm font-bold uppercase tracking-widest hover:bg-white hover:text-matte-black transition-all">+ New Project</a>
    </div>

    <?php
    $projects = get_posts([
        'post_type'   => 'portfolio_project',
        'post_status' => 'any',
        'numberposts' => -1,
        'orderby'     => 'meta_value_num',
        'meta_key'    => 'go_order',
        'order'       => 'ASC',
    ]);
    ?>

    <?php if (empty($projects)): ?>
        <p class="text-lavender/50">No projects yet. Click "New Project" to add your first one.</p>
    <?php else: ?>
        <div class="border border-lavender/10 rounded-xl overflow-hidden">
            <table class="w-full text-sm">
                <thead class="bg-white/[0.03] text-left text-lavender/50 uppercase text-xs tracking-widest">
                    <tr>
                        <th class="px-5 py-3">Project</th>
                        <th class="px-5 py-3">Client</th>
                        <th class="px-5 py-3">Status</th>
                        <th class="px-5 py-3">URL</th>
                        <th class="px-5 py-3 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-lavender/10">
                    <?php foreach ($projects as $p): ?>
                        <tr>
                            <td class="px-5 py-4 font-syne"><?php echo htmlspecialchars($p->post_title); ?></td>
                            <td class="px-5 py-4 text-lavender/60"><?php echo htmlspecialchars(get_post_meta($p->ID, 'go_client', true)); ?></td>
                            <td class="px-5 py-4">
                                <span class="text-xs px-2 py-1 rounded-full <?php echo $p->post_status === 'publish' ? 'bg-green-500/10 text-green-400' : 'bg-yellow-500/10 text-yellow-400'; ?>">
                                    <?php echo htmlspecialchars(ucfirst($p->post_status)); ?>
                                </span>
                            </td>
                            <td class="px-5 py-4 text-lavender/40">/work/<?php echo htmlspecialchars($p->post_name); ?></td>
                            <td class="px-5 py-4 text-right space-x-3">
                                <a href="/wp/admin/portfolio.php?edit=<?php echo (int) $p->ID; ?>" class="text-sharp-purple hover:text-white">Edit</a>
                                <form method="post" class="inline" onsubmit="return confirm('Delete this project? This cannot be undone.');">
                                    <?php echo wp_nonce_field('go_delete_portfolio', '_wpnonce', true, false); ?>
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
