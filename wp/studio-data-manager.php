<?php
/**
 * GETONLINE STUDIO - DATA MANAGER
 * A protected utility to Edit or Delete Niches and Locations.
 * Safely cascades changes to the custom listicles database table.
 */

// 1. Boot up WordPress silently
define('WP_USE_THEMES', false);
require_once(__DIR__ . '/wp-load.php');

// 2. Security Check: Ensure the user is a logged-in WP Admin
if (!is_user_logged_in() || !current_user_can('manage_options')) {
    wp_redirect('/u-login.php?redirect_to=' . urlencode($_SERVER['REQUEST_URI']));
    exit;
}

global $wpdb;
$listicle_table = $wpdb->prefix . 'pseo_listicles';
$message = '';
$msg_type = 'success';

// ---------------------------------------------------------
// FORM HANDLING (Safely cascades changes to DB)
// ---------------------------------------------------------
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action    = $_POST['action'] ?? '';
    $post_id   = intval($_POST['post_id'] ?? 0);
    $post_type = sanitize_text_field($_POST['post_type'] ?? '');
    
    // VERIFY POST EXISTS
    $old_post = get_post($post_id);
    
    if ($old_post) {
        $old_slug = $old_post->post_name;
        
        // --- UPDATE LOGIC ---
        if ($action === 'update') {
            $new_title = sanitize_text_field($_POST['post_title']);
            $new_slug  = sanitize_title($_POST['post_name']);
            
            if (empty($new_title) || empty($new_slug)) {
                $message = "Title and Slug cannot be empty.";
                $msg_type = 'error';
            } else {
                // 1. Update the WP Post
                wp_update_post([
                    'ID'         => $post_id,
                    'post_title' => $new_title,
                    'post_name'  => $new_slug
                ]);
                
                // 2. Cascade changes to the custom Listicles table if the slug changed
                if ($old_slug !== $new_slug) {
                    $suppress = $wpdb->suppress_errors(true);
                    if ($post_type === 'pseo_location') {
                        $wpdb->update($listicle_table, ['city_slug' => $new_slug], ['city_slug' => $old_slug]);
                    } elseif ($post_type === 'pseo_niche') {
                        $wpdb->update($listicle_table, ['niche_slug' => $new_slug], ['niche_slug' => $old_slug]);
                    }
                    $wpdb->suppress_errors($suppress);
                }
                
                $message = "Successfully updated: " . $new_title;
            }
        }
        
        // --- DELETE LOGIC ---
        if ($action === 'delete') {
            $title_deleted = $old_post->post_title;
            
            // 1. Delete the WP Post permanently
            wp_delete_post($post_id, true);
            
            // 2. Cascade delete all associated listicles to keep DB clean
            $suppress = $wpdb->suppress_errors(true);
            if ($post_type === 'pseo_location') {
                $wpdb->delete($listicle_table, ['city_slug' => $old_slug]);
            } elseif ($post_type === 'pseo_niche') {
                $wpdb->delete($listicle_table, ['niche_slug' => $old_slug]);
            }
            $wpdb->suppress_errors($suppress);
            
            $message = "Permanently deleted '{$title_deleted}' and cleaned up the database.";
            $msg_type = 'error'; // Use red color for delete notification
        }
    }
}

// ---------------------------------------------------------
// FETCH DATA FOR UI
// ---------------------------------------------------------
$locations = get_posts([
    'post_type'   => 'pseo_location',
    'numberposts' => -1,
    'post_status' => 'any',
    'orderby'     => 'title',
    'order'       => 'ASC'
]);

$niches = get_posts([
    'post_type'   => 'pseo_niche',
    'numberposts' => -1,
    'post_status' => 'any',
    'orderby'     => 'title',
    'order'       => 'ASC'
]);
?>
<!DOCTYPE html>
<html lang="en" class="dark">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>Data Manager | pSEO Command Center</title>
    
    <link rel="icon" href="https://getonlinestudio.com/insights/wp-content/uploads/2026/02/GetOnline_Studio_Logo.jpg" sizes="32x32" />
    <link href="https://fonts.googleapis.com/css2?family=Manrope:wght@300;400;500;600;700&family=Syne:wght@400;600;700&display=swap" rel="stylesheet">
    <script src="https://unpkg.com/lucide@latest"></script>
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            darkMode: 'class',
            theme: {
                extend: {
                    colors: {
                        'matte-black': '#0a0a0a', 'panel-dark': '#121212', 'card-dark': '#1a1a1a',
                        'lavender': '#e9d5ff', 'sharp-purple': '#7e22ce', 'success-green': '#4ade80',
                    },
                    fontFamily: { 'syne': ['Syne', 'sans-serif'], 'manrope': ['Manrope', 'sans-serif'], }
                }
            }
        }
    </script>
    <style>
        body { background-color: #0a0a0a; color: #e9d5ff; font-family: 'Manrope', sans-serif; overflow-y: scroll; }
        ::-webkit-scrollbar { width: 6px; height: 6px; }
        ::-webkit-scrollbar-track { background: #0a0a0a; }
        ::-webkit-scrollbar-thumb { background: #333; border-radius: 10px; }
        ::-webkit-scrollbar-thumb:hover { background: #7e22ce; }
        @keyframes slideInUp { from { transform: translateY(100%); opacity: 0; } to { transform: translateY(0); opacity: 1; } }
        .toast { animation: slideInUp 0.3s ease forwards; }
        input { font-size: 14px !important; }
    </style>
</head>
<body class="antialiased relative selection:bg-sharp-purple selection:text-white pb-24">

    <!-- TOAST NOTIFICATION -->
    <?php if($message): ?>
    <div id="toast" class="toast fixed bottom-4 right-4 z-[100] <?= $msg_type === 'success' ? 'bg-success-green/20 border-success-green/40 text-success-green shadow-[0_0_20px_rgba(74,222,128,0.2)]' : 'bg-red-500/20 border-red-500/40 text-red-400 shadow-[0_0_20px_rgba(239,68,68,0.2)]' ?> border px-6 py-4 rounded-lg backdrop-blur-md flex items-center gap-3">
        <i data-lucide="<?= $msg_type === 'success' ? 'check-circle' : 'trash-2' ?>" class="w-5 h-5 flex-shrink-0"></i>
        <span class="font-manrope font-bold text-sm leading-snug"><?= $message ?></span>
    </div>
    <script>setTimeout(() => { document.getElementById('toast').style.display = 'none'; }, 5000);</script>
    <?php endif; ?>

    <!-- NAVBAR -->
    <header class="h-16 md:h-20 border-b border-white/5 flex items-center justify-between px-4 md:px-8 bg-panel-dark/80 backdrop-blur-md sticky top-0 z-50">
        <div class="flex items-center gap-3">
            <span class="font-syne font-bold text-xl md:text-2xl text-white tracking-wider">GO<span class="text-sharp-purple">.</span></span>
            <span class="font-mono text-[10px] text-yellow-500 border border-yellow-500/30 bg-yellow-500/10 px-2 py-0.5 rounded-full uppercase tracking-widest hidden sm:inline-block">Data Manager</span>
        </div>
        <div class="flex items-center gap-4">
            <a href="/wp/studio-admin.php" class="bg-sharp-purple text-white px-5 py-2.5 rounded-full text-xs font-bold uppercase tracking-widest hover:bg-white hover:text-matte-black transition-all shadow-[0_0_15px_rgba(126,34,206,0.3)] flex items-center gap-2">
                <i data-lucide="arrow-left" class="w-4 h-4"></i> Back to Command Center
            </a>
        </div>
    </header>

    <main class="max-w-7xl mx-auto px-4 md:px-8 pt-10">
        
        <div class="mb-10 max-w-2xl">
            <h1 class="font-syne text-3xl md:text-4xl font-bold text-white mb-3">Database Corrections</h1>
            <p class="text-lavender/60 text-sm leading-relaxed">Fix typos or permanently remove locations and niches. Changing a slug here will safely cascade and auto-update all generated listicles associated with it.</p>
        </div>

        <div class="grid grid-cols-1 xl:grid-cols-2 gap-8 lg:gap-12 items-start">

            <!-- LOCATIONS TABLE -->
            <div class="bg-card-dark border border-white/5 rounded-2xl overflow-hidden flex flex-col">
                <div class="p-5 border-b border-white/5 bg-panel-dark/40 flex justify-between items-center">
                    <h3 class="font-syne text-lg font-bold text-white flex items-center gap-2">
                        <i data-lucide="map-pin" class="w-5 h-5 text-blue-400"></i> Locations
                    </h3>
                    <span class="text-xs font-mono text-lavender/40"><?= count($locations) ?> records</span>
                </div>
                <div class="overflow-x-auto w-full max-h-[600px] overflow-y-auto">
                    <table class="w-full text-left border-collapse min-w-[500px]">
                        <thead class="sticky top-0 bg-card-dark z-10 shadow-sm border-b border-white/5">
                            <tr>
                                <th class="p-4 text-[10px] font-syne text-lavender/50 uppercase tracking-widest font-bold w-1/3">City Name (Title)</th>
                                <th class="p-4 text-[10px] font-syne text-lavender/50 uppercase tracking-widest font-bold w-1/3">URL Slug</th>
                                <th class="p-4 text-[10px] font-syne text-lavender/50 uppercase tracking-widest font-bold text-right">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($locations)): ?>
                                <tr><td colspan="3" class="p-6 text-center text-lavender/40 text-sm">No locations found.</td></tr>
                            <?php endif; ?>
                            <?php foreach ($locations as $loc): ?>
                            <tr class="hover:bg-white/5 transition-colors border-b border-white/5 group">
                                <form method="POST" class="m-0 p-0">
                                    <input type="hidden" name="post_id" value="<?= $loc->ID ?>">
                                    <input type="hidden" name="post_type" value="pseo_location">
                                    
                                    <td class="p-3">
                                        <input type="text" name="post_title" value="<?= esc_attr($loc->post_title) ?>" class="w-full bg-matte-black border border-white/10 rounded px-3 py-2 text-white focus:border-blue-400 outline-none font-medium transition-colors" required>
                                    </td>
                                    <td class="p-3">
                                        <input type="text" name="post_name" value="<?= esc_attr($loc->post_name) ?>" class="w-full bg-matte-black border border-white/10 rounded px-3 py-2 text-lavender/80 focus:border-blue-400 outline-none font-mono text-xs transition-colors" required>
                                    </td>
                                    <td class="p-3 text-right">
                                        <div class="flex justify-end gap-2">
                                            <button type="submit" name="action" value="update" title="Save Changes" class="w-8 h-8 rounded border border-blue-500/30 bg-blue-500/10 text-blue-400 hover:bg-blue-500 hover:text-white transition-colors flex items-center justify-center">
                                                <i data-lucide="save" class="w-4 h-4"></i>
                                            </button>
                                            <button type="submit" name="action" value="delete" title="Delete Location" onclick="return confirm('WARNING: Are you sure you want to permanently delete <?= esc_js($loc->post_title) ?>? This will instantly delete ALL listicles associated with this city!');" class="w-8 h-8 rounded border border-red-500/30 bg-red-500/10 text-red-400 hover:bg-red-500 hover:text-white transition-colors flex items-center justify-center">
                                                <i data-lucide="trash-2" class="w-4 h-4"></i>
                                            </button>
                                        </div>
                                    </td>
                                </form>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- NICHES TABLE -->
            <div class="bg-card-dark border border-white/5 rounded-2xl overflow-hidden flex flex-col">
                <div class="p-5 border-b border-white/5 bg-panel-dark/40 flex justify-between items-center">
                    <h3 class="font-syne text-lg font-bold text-white flex items-center gap-2">
                        <i data-lucide="briefcase" class="w-5 h-5 text-sharp-purple"></i> Niches
                    </h3>
                    <span class="text-xs font-mono text-lavender/40"><?= count($niches) ?> records</span>
                </div>
                <div class="overflow-x-auto w-full max-h-[600px] overflow-y-auto">
                    <table class="w-full text-left border-collapse min-w-[500px]">
                        <thead class="sticky top-0 bg-card-dark z-10 shadow-sm border-b border-white/5">
                            <tr>
                                <th class="p-4 text-[10px] font-syne text-lavender/50 uppercase tracking-widest font-bold w-1/3">Niche Name (Title)</th>
                                <th class="p-4 text-[10px] font-syne text-lavender/50 uppercase tracking-widest font-bold w-1/3">URL Slug</th>
                                <th class="p-4 text-[10px] font-syne text-lavender/50 uppercase tracking-widest font-bold text-right">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($niches)): ?>
                                <tr><td colspan="3" class="p-6 text-center text-lavender/40 text-sm">No niches found.</td></tr>
                            <?php endif; ?>
                            <?php foreach ($niches as $niche): ?>
                            <tr class="hover:bg-white/5 transition-colors border-b border-white/5 group">
                                <form method="POST" class="m-0 p-0">
                                    <input type="hidden" name="post_id" value="<?= $niche->ID ?>">
                                    <input type="hidden" name="post_type" value="pseo_niche">
                                    
                                    <td class="p-3">
                                        <input type="text" name="post_title" value="<?= esc_attr($niche->post_title) ?>" class="w-full bg-matte-black border border-white/10 rounded px-3 py-2 text-white focus:border-sharp-purple outline-none font-medium transition-colors" required>
                                    </td>
                                    <td class="p-3">
                                        <input type="text" name="post_name" value="<?= esc_attr($niche->post_name) ?>" class="w-full bg-matte-black border border-white/10 rounded px-3 py-2 text-lavender/80 focus:border-sharp-purple outline-none font-mono text-xs transition-colors" required>
                                    </td>
                                    <td class="p-3 text-right">
                                        <div class="flex justify-end gap-2">
                                            <button type="submit" name="action" value="update" title="Save Changes" class="w-8 h-8 rounded border border-sharp-purple/30 bg-sharp-purple/10 text-sharp-purple hover:bg-sharp-purple hover:text-white transition-colors flex items-center justify-center">
                                                <i data-lucide="save" class="w-4 h-4"></i>
                                            </button>
                                            <button type="submit" name="action" value="delete" title="Delete Niche" onclick="return confirm('WARNING: Are you sure you want to permanently delete <?= esc_js($niche->post_title) ?>? This will instantly delete ALL listicles associated with this niche!');" class="w-8 h-8 rounded border border-red-500/30 bg-red-500/10 text-red-400 hover:bg-red-500 hover:text-white transition-colors flex items-center justify-center">
                                                <i data-lucide="trash-2" class="w-4 h-4"></i>
                                            </button>
                                        </div>
                                    </td>
                                </form>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>

        </div>
    </main>

    <script>
        lucide.createIcons();
    </script>
</body>
</html>