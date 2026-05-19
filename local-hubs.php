<?php
/**
 * GETONLINE STUDIO - NEIGHBORHOOD ACTIVATION CENTER
 * Standalone gatekeeper for hyper-local pSEO rollout.
 * Location: /local-hubs.php (HOME/ROOT FOLDER)
 */

// 1. Boot up WordPress silently
define('WP_USE_THEMES', false);

if (file_exists(__DIR__ . '/wp-load.php')) {
    require_once(__DIR__ . '/wp-load.php');
} elseif (file_exists(__DIR__ . '/wp/wp-load.php')) {
    require_once(__DIR__ . '/wp/wp-load.php');
} else {
    die('Error: wp-load.php not found.');
}

// 2. Security Check
if (!is_user_logged_in() || !current_user_can('manage_options')) {
    $login_url = file_exists(__DIR__ . '/wp/wp-login.php') ? '/wp/wp-login.php' : '/wp-login.php';
    wp_redirect($login_url . '?redirect_to=' . urlencode($_SERVER['REQUEST_URI']));
    exit;
}

$message = '';

// 3. Handle Activation Toggle (AJAX/POST) - BULLETPROOFED
if (isset($_POST['action']) && $_POST['action'] === 'toggle_neighborhood') {
    $city_id = intval($_POST['city_id']);
    $nb_slug = sanitize_title($_POST['neighborhood_slug']);
    $new_status = $_POST['status'] === 'active';
    
    $active_neighborhoods = get_post_meta($city_id, '_pseo_active_neighborhoods', true);
    if (!is_array($active_neighborhoods)) $active_neighborhoods = [];
    
    if ($new_status) {
        if (!in_array($nb_slug, $active_neighborhoods)) {
            $active_neighborhoods[] = $nb_slug;
        }
    } else {
        $active_neighborhoods = array_diff($active_neighborhoods, [$nb_slug]);
    }
    
    update_post_meta($city_id, '_pseo_active_neighborhoods', array_values($active_neighborhoods));
    
    // CRITICAL FIX: Wipe any sneaky PHP warnings/whitespace before sending JSON
    while (ob_get_level() > 0) { ob_end_clean(); }
    header('Content-Type: application/json');
    echo json_encode(['success' => true]);
    exit;
}

// 4. Data Fetching
$active_cities = get_posts([
    'post_type'   => 'pseo_location',
    'post_status' => 'publish',
    'numberposts' => -1,
    'orderby'     => 'title',
    'order'       => 'ASC'
]);

$nb_file = __DIR__ . '/neighborhoods.json';
$nb_library = file_exists($nb_file) ? json_decode(file_get_contents($nb_file), true) : [];

if (empty($nb_library)) {
    $message = "Warning: neighborhoods.json not found at " . $nb_file;
}

$total_possible = 0;
$total_active = 0;
foreach($active_cities as $city) {
    $city_possible = count($nb_library[$city->post_name] ?? []);
    $city_active = count(get_post_meta($city->ID, '_pseo_active_neighborhoods', true) ?: []);
    $total_possible += $city_possible;
    $total_active += $city_active;
}
?>
<!DOCTYPE html>
<html lang="en" class="dark">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Neighborhood Center | GetOnline Studio</title>
    <script src="https://unpkg.com/lucide@latest"></script>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Syne:wght@700&family=Manrope:wght@400;600&display=swap" rel="stylesheet">
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: { 'matte-black': '#0a0a0a', 'panel-dark': '#121212', 'card-dark': '#1a1a1a', 'sharp-purple': '#7e22ce' },
                    fontFamily: { 'syne': ['Syne', 'sans-serif'], 'manrope': ['Manrope', 'sans-serif'] }
                }
            }
        }
    </script>
    <style>
        body { background-color: #0a0a0a; color: #e9d5ff; font-family: 'Manrope', sans-serif; }
        .nb-btn.active { background-color: #7e22ce; color: white; border-color: #7e22ce; box-shadow: 0 0 15px rgba(126, 34, 206, 0.3); }
        .nb-btn { transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1); }
    </style>
</head>
<body class="min-h-screen p-4 md:p-8 lg:p-12">

    <div class="max-w-7xl mx-auto">
        <header class="flex flex-col md:flex-row justify-between items-start md:items-center mb-12 gap-6">
            <div>
                <a href="/wp/studio-admin.php" class="inline-flex items-center gap-2 text-lavender/40 hover:text-sharp-purple text-xs font-bold uppercase tracking-widest mb-4 transition-colors">
                    <i data-lucide="arrow-left" class="w-3 h-3"></i> Back to Command Center
                </a>
                <h1 class="font-syne text-3xl md:text-5xl text-white font-bold">Local <span class="text-sharp-purple">Hubs</span></h1>
                <p class="text-lavender/50 mt-2">Approve and deploy hyper-local service areas to Google.</p>
                <?php if ($message): ?>
                    <p class="mt-4 p-3 bg-red-500/10 border border-red-500/20 text-red-400 text-xs rounded-lg font-mono"><?= $message ?></p>
                <?php endif; ?>
            </div>

            <div class="flex gap-4">
                <div class="bg-card-dark border border-white/5 p-4 rounded-2xl text-center min-w-[140px]">
                    <div class="text-2xl font-bold text-white"><?= number_format($total_active) ?></div>
                    <div class="text-[10px] text-lavender/40 uppercase font-bold tracking-widest">Live Hubs</div>
                </div>
                <div class="bg-card-dark border border-white/5 p-4 rounded-2xl text-center min-w-[140px]">
                    <div class="text-2xl font-bold text-sharp-purple"><?= number_format($total_possible - $total_active) ?></div>
                    <div class="text-[10px] text-lavender/40 uppercase font-bold tracking-widest">In Queue</div>
                </div>
            </div>
        </header>

        <div class="mb-10 relative">
            <i data-lucide="search" class="absolute left-5 top-1/2 -translate-y-1/2 w-5 h-5 text-lavender/30"></i>
            <input type="text" id="citySearch" onkeyup="filterCities()" placeholder="Search for a city (e.g. Lagos, Abuja)..." 
                class="w-full bg-card-dark border border-white/10 rounded-2xl py-5 pl-14 pr-6 text-white focus:border-sharp-purple outline-none shadow-2xl transition-all">
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-8" id="cityGrid">
            <?php foreach ($active_cities as $city): 
                $city_nb_list = $nb_library[$city->post_name] ?? [];
                if (empty($city_nb_list)) continue;
                $active_nb = get_post_meta($city->ID, '_pseo_active_neighborhoods', true) ?: [];
            ?>
            <div class="city-card bg-card-dark border border-white/5 rounded-3xl overflow-hidden flex flex-col" data-name="<?= strtolower($city->post_title) ?>">
                <div class="p-6 border-b border-white/5 bg-panel-dark/50 flex justify-between items-center">
                    <div class="flex items-center gap-4">
                        <div class="w-12 h-12 rounded-2xl bg-sharp-purple/10 flex items-center justify-center text-sharp-purple">
                            <i data-lucide="map"></i>
                        </div>
                        <div>
                            <h3 class="font-syne text-xl text-white"><?= esc_html($city->post_title) ?></h3>
                            <p class="text-[10px] text-lavender/40 uppercase tracking-widest"><span class="active-count"><?= count($active_nb) ?></span> / <?= count($city_nb_list) ?> Areas Active</p>
                        </div>
                    </div>
                </div>
                
                <div class="p-6 flex-1">
                    <p class="text-[10px] font-bold text-lavender/20 uppercase tracking-[0.2em] mb-4">Click to Toggle Reach</p>
                    <div class="flex flex-wrap gap-2">
                        <?php foreach ($city_nb_list as $slug => $name): 
                            $is_on = is_array($active_nb) && in_array($slug, $active_nb);
                        ?>
                        <button type="button" 
                            onclick="toggleNB(<?= $city->ID ?>, '<?= $slug ?>', this)"
                            data-status="<?= $is_on ? 'active' : 'inactive' ?>"
                            class="nb-btn px-4 py-2.5 rounded-xl border text-[11px] font-bold transition-all
                            <?= $is_on ? 'active' : 'bg-matte-black text-lavender/30 border-white/5 hover:border-white/20' ?>">
                            <?= esc_html($name) ?>
                        </button>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </div>

    <script>
        lucide.createIcons();

        function filterCities() {
            const input = document.getElementById('citySearch').value.toLowerCase();
            const cards = document.querySelectorAll('.city-card');
            cards.forEach(card => {
                const name = card.getAttribute('data-name');
                card.style.display = name.includes(input) ? 'flex' : 'none';
            });
        }

        async function toggleNB(cityId, slug, btn) {
            const current = btn.getAttribute('data-status');
            const target = current === 'active' ? 'inactive' : 'active';
            
            // Optimistic UI change
            if (target === 'active') {
                btn.classList.add('active');
                btn.classList.remove('bg-matte-black', 'text-lavender/30', 'border-white/5');
            } else {
                btn.classList.remove('active');
                btn.classList.add('bg-matte-black', 'text-lavender/30', 'border-white/5');
            }
            btn.setAttribute('data-status', target);

            const body = new FormData();
            body.append('action', 'toggle_neighborhood');
            body.append('city_id', cityId);
            body.append('neighborhood_slug', slug);
            body.append('status', target);

            try {
                const res = await fetch(window.location.pathname, { method: 'POST', body });
                const data = await res.json();
                if (!data.success) {
                    alert('Server error. Refreshing.');
                    window.location.reload();
                }
            } catch (e) {
                alert('Action failed. The server blocked the request. Refreshing.');
                window.location.reload();
            }
        }
    </script>
</body>
</html>