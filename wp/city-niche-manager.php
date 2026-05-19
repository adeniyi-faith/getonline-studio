<?php
/**
 * GETONLINE STUDIO - CITY-NICHE JSON MANAGER
 * Dashboard to view, add, edit and delete entries in the root city-niche.json file.
 * Upgraded with: Missing Combos Tab, Bulk AI Generation, CSV Export, and Global Rules.
 */

// 1. Boot up WordPress silently
define('WP_USE_THEMES', false);
require_once(__DIR__ . '/wp-load.php');

// 2. Security Check
if (!is_user_logged_in() || !current_user_can('manage_options')) {
    wp_redirect(home_url('/wp/u-login.php?redirect_to=' . urlencode($_SERVER['REQUEST_URI'])));
    exit;
}

$json_path = dirname(ABSPATH) . '/city-niche.json'; 
$rules_path = dirname(ABSPATH) . '/niche-rules.json';

// Ensure files exist
if (!file_exists($json_path)) {
    file_put_contents($json_path, json_encode(['_readme' => 'City x Niche intersection data.']));
}
if (!file_exists($rules_path)) {
    file_put_contents($rules_path, json_encode([]));
}

$json_data = json_decode(file_get_contents($json_path), true) ?: [];
$niche_rules = json_decode(file_get_contents($rules_path), true) ?: [];
$message = '';

// ---------------------------------------------------------
// CSV EXPORT LOGIC
// ---------------------------------------------------------
if (isset($_GET['export']) && $_GET['export'] === 'csv') {
    // Flush any WP output buffers so headers are clean
    if (ob_get_level()) ob_end_clean();
    header('Content-Type: text/csv; charset=utf-8');
    header('Content-Disposition: attachment; filename=getonline-city-niche-data-' . date('Y-m-d') . '.csv');
    header('Cache-Control: no-cache, must-revalidate');
    header('Pragma: no-cache');
    $output = fopen('php://output', 'w');
    // UTF-8 BOM so Excel opens Nigerian characters (₦, etc.) correctly
    fputs($output, "\xEF\xBB\xBF");
    
    // CSV Headers
    fputcsv($output, ['Key', 'City', 'Niche', 'Est. Businesses', '% Without Website', 'Without Website', 'With Website', 'Low Cost (NGN)', 'Typical Cost (NGN)', 'High Cost (NGN)', 'Insight', 'Edge', 'What They Lack', 'Last Updated']);
    
    foreach ($json_data as $key => $data) {
        if ($key === '_readme') continue;
        fputcsv($output, [
            $key, 
            $data['city'] ?? '', 
            $data['niche'] ?? '', 
            $data['estimated_businesses'] ?? 0,
            $data['pct_without_website'] ?? 0, 
            $data['without_website'] ?? 0, 
            $data['with_website'] ?? 0,
            $data['avg_cost_low'] ?? 0, 
            $data['avg_cost_typical'] ?? 0, 
            $data['avg_cost_high'] ?? 0,
            $data['local_insight'] ?? '',
            $data['top_ranked_edge'] ?? '',
            $data['what_most_lack'] ?? '',
            $data['last_updated'] ?? ''
        ]);
    }
    fclose($output);
    exit;
}

// ---------------------------------------------------------
// FORM HANDLING (Including Global Rules)
// ---------------------------------------------------------
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    
    // Save Global Niche Rules
    if (isset($_POST['action']) && $_POST['action'] === 'save_rules') {
        $rules_to_save = $_POST['rules'] ?? [];
        $sanitized_rules = [];
        foreach($rules_to_save as $n_slug => $prices) {
            $sanitized_rules[sanitize_title($n_slug)] = [
                'low' => intval($prices['low']),
                'typical' => intval($prices['typical']),
                'high' => intval($prices['high'])
            ];
        }
        file_put_contents($rules_path, json_encode($sanitized_rules, JSON_PRETTY_PRINT));
        $niche_rules = $sanitized_rules;
        $message = "Global Niche Pricing Rules saved successfully.";
    }

    if (isset($_POST['action']) && $_POST['action'] === 'delete_entry') {
        $entry_key = sanitize_text_field($_POST['entry_key']);
        if (isset($json_data[$entry_key])) {
            unset($json_data[$entry_key]);
            file_put_contents($json_path, json_encode($json_data, JSON_PRETTY_PRINT));
            $message = "Deleted $entry_key successfully.";
        }
    }

    if (isset($_POST['action']) && $_POST['action'] === 'add_entry') {
        $city = sanitize_title($_POST['city']);
        $niche = sanitize_title($_POST['niche']);
        $key = "{$city}_{$niche}";
        
        $new_entry = [
            "city" => $city,
            "niche" => $niche,
            "estimated_businesses" => intval($_POST['estimated_businesses']),
            "pct_without_website" => intval($_POST['pct_without_website']),
            "without_website" => intval($_POST['estimated_businesses']) * (intval($_POST['pct_without_website'])/100),
            "with_website" => intval($_POST['estimated_businesses']) - (intval($_POST['estimated_businesses']) * (intval($_POST['pct_without_website'])/100)),
            "avg_cost_low" => intval($_POST['avg_cost_low'] ?? 120000),
            "avg_cost_typical" => intval($_POST['avg_cost_typical']),
            "avg_cost_high" => intval($_POST['avg_cost_high'] ?? 800000),
            "maintenance_monthly" => intval($_POST['maintenance_monthly'] ?? 25000),
            "competitive_landscape" => sanitize_textarea_field($_POST['competitive_landscape']),
            "top_ranked_edge" => sanitize_textarea_field($_POST['top_ranked_edge']),
            "what_most_lack" => sanitize_textarea_field($_POST['what_most_lack']),
            "local_insight" => sanitize_textarea_field($_POST['local_insight']),
            "unique_faq" => sanitize_text_field($_POST['unique_faq']),
            "unique_faq_answer" => sanitize_textarea_field($_POST['unique_faq_answer']),
            "projects_completed" => 0,
            "urgency_note" => sanitize_text_field($_POST['urgency_note']),
            "last_updated" => current_time('mysql')
        ];

        $json_data[$key] = $new_entry;
        file_put_contents($json_path, json_encode($json_data, JSON_PRETTY_PRINT));
        $message = "Added new data for $key successfully.";
    }

    if (isset($_POST['action']) && $_POST['action'] === 'edit_entry') {
        $key = sanitize_text_field($_POST['entry_key']);
        
        if (isset($json_data[$key])) {
            $json_data[$key]['estimated_businesses'] = intval($_POST['estimated_businesses']);
            $json_data[$key]['pct_without_website'] = intval($_POST['pct_without_website']);
            $json_data[$key]['without_website'] = intval($_POST['estimated_businesses']) * (intval($_POST['pct_without_website'])/100);
            $json_data[$key]['with_website'] = intval($_POST['estimated_businesses']) - $json_data[$key]['without_website'];
            
            $json_data[$key]['avg_cost_low'] = intval($_POST['avg_cost_low']);
            $json_data[$key]['avg_cost_typical'] = intval($_POST['avg_cost_typical']);
            $json_data[$key]['avg_cost_high'] = intval($_POST['avg_cost_high']);
            
            $json_data[$key]['competitive_landscape'] = sanitize_textarea_field($_POST['competitive_landscape']);
            $json_data[$key]['top_ranked_edge'] = sanitize_textarea_field($_POST['top_ranked_edge']);
            $json_data[$key]['what_most_lack'] = sanitize_textarea_field($_POST['what_most_lack']);
            $json_data[$key]['local_insight'] = sanitize_textarea_field($_POST['local_insight']);
            $json_data[$key]['unique_faq'] = sanitize_text_field($_POST['unique_faq']);
            $json_data[$key]['unique_faq_answer'] = sanitize_textarea_field($_POST['unique_faq_answer']);
            $json_data[$key]['urgency_note'] = sanitize_text_field($_POST['urgency_note']);
            $json_data[$key]['last_updated'] = current_time('mysql');

            file_put_contents($json_path, json_encode($json_data, JSON_PRETTY_PRINT));
            $message = "Updated data for $key successfully.";
        }
    }
}

// ---------------------------------------------------------
// AJAX: IMMEDIATE & BULK AI GENERATION (GEMINI)
// ---------------------------------------------------------
if (isset($_GET['ajax']) && $_GET['ajax'] === 'generate_ai') {
    header('Content-Type: application/json');
    
    $city_slug = sanitize_title($_GET['city']);
    $niche_slug = sanitize_title($_GET['niche']);
    
    $city_post = get_page_by_path($city_slug, OBJECT, 'pseo_location');
    $niche_post = get_page_by_path($niche_slug, OBJECT, 'pseo_niche');
    
    if(!$city_post || !$niche_post) {
        die(json_encode(['error' => 'Invalid city or niche']));
    }
    
    $city_name = $city_post->post_title;
    $niche_name = $niche_post->post_title;

    // Load Global Rules to feed the AI
    $rule = $niche_rules[$niche_slug] ?? [];
    $min_low = !empty($rule['low']) ? $rule['low'] : 120000;
    $target_typ = !empty($rule['typical']) ? $rule['typical'] : 350000;
    $target_high = !empty($rule['high']) ? $rule['high'] : 1200000;

    $api_key = "AIzaSyC1DPDJzt5psEkIDgqK3XztuVLgXnDwwZM"; 
    $api_model = "gemini-2.5-flash"; 

    $system_prompt = "You are a master business analyst and SEO strategist for GetOnline Studio in Nigeria.
Your task is to generate hyper-realistic, highly specific market data for the intersection of a specific Niche inside a specific City in Nigeria.
The data MUST be returned strictly as a JSON object matching the exact schema provided.
Do not use markdown blocks like ```json. Return pure JSON only.

STRICT RULES:
1. NEVER set 'avg_cost_low' to anything less than {$min_low}. {$min_low} Naira is the absolute minimum floor price.
2. For 'estimated_businesses', use highly realistic, conservative estimates based on standard Nigerian economic realities. Do not exaggerate.

SCHEMA REQUIREMENTS:
{
  \"estimated_businesses\": [Integer. Realistic estimate of how many of this business exist in this specific Nigerian city. E.g., 200 to 5000],
  \"pct_without_website\": [Integer 1-100. Usually between 70 to 95 for Nigerian local businesses],
  \"avg_cost_low\": [Integer. Website cost lower bound in Naira. STRICT RULE: MUST BE {$min_low} OR HIGHER],
  \"avg_cost_typical\": [Integer. Typical cost in Naira, e.g. {$target_typ}],
  \"avg_cost_high\": [Integer. Enterprise cost in Naira e.g. {$target_high}],
  \"maintenance_monthly\": [Integer. Monthly maintenance in Naira e.g. 25000],
  \"competitive_landscape\": \"[Paragraph. Highly specific to this city and niche. Mention local behaviors, how they currently operate. Max 50 words.]\",
  \"top_ranked_edge\": \"[Paragraph. What do the few successful ones with websites actually do right? Max 40 words.]\",
  \"what_most_lack\": \"[Paragraph. Their biggest digital mistake or missed opportunity. Max 40 words.]\",
  \"local_insight\": \"[Paragraph. A deep, Nigerian-context insight about this niche in this specific city. Max 40 words.]\",
  \"unique_faq\": \"Why does a $niche_name in $city_name need a corporate website?\",
  \"unique_faq_answer\": \"[Paragraph. Strong, persuasive answer pitching GetOnline Studio platforms. Max 50 words.]\",
  \"urgency_note\": \"Available for $city_name $niche_name digital infrastructure projects this month.\"
}";

    $user_prompt = "Generate the JSON market data for City: $city_name and Niche: $niche_name.";

    // CRITICAL FIX: Clean URL
    $url = "https://generativelanguage.googleapis.com/v1beta/models/{$api_model}:generateContent?key={$api_key}";

    $payload = [
        'system_instruction' => ['parts' => [['text' => $system_prompt]]],
        'contents' => [['role' => 'user', 'parts' => [['text' => $user_prompt]]]],
        'generationConfig' => [
            'temperature' => 0.7,
            'responseMimeType' => 'application/json'
        ]
    ];

    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($payload));
    curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
    $response = curl_exec($ch);
    $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    if ($http_code !== 200) {
        die(json_encode(['error' => "API Call failed with status code $http_code."]));
    }

    $response_data = json_decode($response, true);
    $generated_text = $response_data['candidates'][0]['content']['parts'][0]['text'] ?? '';

    if (empty($generated_text)) {
        die(json_encode(['error' => 'API returned an empty response.']));
    }

    $clean_json = trim(str_replace(['```json', '```'], '', $generated_text));
    $new_entry_data = json_decode($clean_json, true);

    if (json_last_error() !== JSON_ERROR_NONE || !is_array($new_entry_data)) {
        die(json_encode(['error' => 'Failed to parse AI response. Response was: ' . substr($clean_json, 0, 100)]));
    }

    // Auto-save logic for the bulk "Generate Now" feature
    if (isset($_GET['save']) && $_GET['save'] === '1') {
        $new_entry_data['city'] = $city_slug;
        $new_entry_data['niche'] = $niche_slug;
        $new_entry_data['without_website'] = intval($new_entry_data['estimated_businesses']) * (intval($new_entry_data['pct_without_website'])/100);
        $new_entry_data['with_website'] = intval($new_entry_data['estimated_businesses']) - $new_entry_data['without_website'];
        $new_entry_data['last_updated'] = current_time('mysql');
        
        $json_data["{$city_slug}_{$niche_slug}"] = $new_entry_data;
        file_put_contents($json_path, json_encode($json_data, JSON_PRETTY_PRINT));
        
        echo json_encode(['status' => 'success', 'data' => $new_entry_data]);
        exit;
    }

    echo json_encode($new_entry_data);
    exit;
}

// Fetch WP data to calculate missing combinations
$locations = get_posts(['post_type' => 'pseo_location', 'numberposts' => -1, 'post_status' => 'publish', 'orderby' => 'title', 'order' => 'ASC']);
$niches = get_posts(['post_type' => 'pseo_niche', 'numberposts' => -1, 'post_status' => 'publish', 'orderby' => 'title', 'order' => 'ASC']);

// Re-calculate Stats
$total_possible = count($locations) * count($niches);
$total_entries = count($json_data) - (isset($json_data['_readme']) ? 1 : 0);
$missing_count = max(0, $total_possible - $total_entries);
$progress_pct = $total_possible > 0 ? round(($total_entries / $total_possible) * 100) : 0;
$display_data = array_reverse($json_data, true);

// Get last sync time
$last_sync_timestamp = file_exists($json_path) ? filemtime($json_path) : time();
$time_diff = human_time_diff($last_sync_timestamp, current_time('timestamp'));
$exact_time = date('M j, Y - g:i A', $last_sync_timestamp);

// Build Missing Combinations Array
$missing_combos = [];
foreach ($locations as $loc) {
    foreach ($niches as $niche) {
        $k = $loc->post_name . '_' . $niche->post_name;
        if (!isset($json_data[$k])) {
            $missing_combos[] = [
                'city_slug' => $loc->post_name,
                'city_name' => $loc->post_title,
                'niche_slug' => $niche->post_name,
                'niche_name' => $niche->post_title,
            ];
        }
    }
}

?>
<!DOCTYPE html>
<html lang="en" class="dark">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>City-Niche Database | GetOnline Studio</title>
    <!-- CRITICAL FIX: Cleaned CSS/JS URLs -->
    <link href="https://fonts.googleapis.com/css2?family=Manrope:wght@400;500;600;700&family=Syne:wght@600;700&display=swap" rel="stylesheet">
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
                    fontFamily: { 'syne': ['Syne', 'sans-serif'], 'manrope': ['Manrope', 'sans-serif'] }
                }
            }
        }
    </script>
    <style>
        body { background-color: #0a0a0a; color: #e9d5ff; font-family: 'Manrope', sans-serif; }
        ::-webkit-scrollbar { width: 6px; height: 6px; }
        ::-webkit-scrollbar-track { background: #0a0a0a; }
        ::-webkit-scrollbar-thumb { background: #333; border-radius: 10px; }
        @keyframes slideInUp { from { transform: translateY(100%); opacity: 0; } to { transform: translateY(0); opacity: 1; } }
        .toast { animation: slideInUp 0.3s ease forwards; }
        .modal-overlay { transition: opacity 0.3s ease; }
        .modal-panel { transition: transform 0.3s ease; }
        
        input[type="number"], input[type="text"], textarea { font-size: 16px !important; }
        @media (min-width: 768px) {
            input[type="number"], input[type="text"], textarea { font-size: 14px !important; }
        }
        
        /* Custom Checkbox Color */
        .bulk-cb { accent-color: #7e22ce; }
    </style>
</head>
<body class="flex h-[100dvh] overflow-hidden antialiased">

    <?php if($message): ?>
    <div id="toast" class="toast fixed bottom-4 right-4 left-4 md:left-auto md:bottom-6 md:right-6 z-[100] bg-success-green/20 border border-success-green/40 text-success-green px-6 py-4 md:py-3 rounded-lg backdrop-blur-md flex items-center gap-3 shadow-lg">
        <i data-lucide="check-circle" class="w-5 h-5 flex-shrink-0"></i>
        <span class="font-bold text-sm"><?= $message ?></span>
    </div>
    <script>setTimeout(() => { document.getElementById('toast').style.display = 'none'; }, 4000);</script>
    <?php endif; ?>

    <aside class="w-64 bg-panel-dark border-r border-white/5 flex flex-col h-full hidden md:flex flex-shrink-0">
        <div class="h-20 flex items-center px-6 border-b border-white/5">
            <span class="font-syne font-bold text-2xl text-white tracking-wider">GO<span class="text-sharp-purple">.</span></span>
        </div>
        <nav class="flex-1 px-4 py-6 space-y-2">
            <a href="studio-admin.php" class="flex items-center gap-3 px-4 py-3 rounded-lg text-sm font-medium text-lavender/60 hover:text-white hover:bg-white/5 transition-colors">
                <i data-lucide="arrow-left" class="w-4 h-4"></i> Command Center
            </a>
            <a href="#" class="flex items-center gap-3 px-4 py-3 rounded-lg text-sm font-medium bg-sharp-purple/10 text-white border border-sharp-purple/20">
                <i data-lucide="database" class="w-4 h-4 text-sharp-purple"></i> JSON Manager
            </a>
        </nav>
    </aside>

    <main class="flex-1 flex flex-col h-full overflow-y-auto relative w-full">
        <header class="h-auto min-h-[5rem] py-4 border-b border-white/5 flex flex-col md:flex-row items-start md:items-center justify-between px-5 md:px-8 bg-panel-dark/50 backdrop-blur-md sticky top-0 z-10 flex-shrink-0 gap-3">
            <div class="flex items-center gap-3 w-full md:w-auto">
                <a href="studio-admin.php" class="md:hidden w-10 h-10 rounded-full bg-white/5 flex items-center justify-center text-lavender/70 hover:text-white flex-shrink-0">
                    <i data-lucide="arrow-left" class="w-5 h-5"></i>
                </a>
                <div>
                    <h2 class="font-syne text-lg md:text-xl text-white font-semibold">City-Niche Manager</h2>
                    <div class="flex flex-wrap items-center gap-2 md:gap-4 mt-1">
                        <p class="text-[10px] md:text-xs text-success-green flex items-center gap-1"><i data-lucide="check-circle" class="w-3 h-3"></i> Managing JSON</p>
                        <span class="text-white/20 text-xs hidden md:inline">|</span>
                        <p class="text-[10px] md:text-xs text-lavender/50 flex items-center gap-1" title="<?= $exact_time ?>"><i data-lucide="clock" class="w-3 h-3"></i> Sync: <?= $time_diff ?> ago</p>
                    </div>
                </div>
            </div>
        </header>

        <div class="p-4 md:p-8 max-w-7xl mx-auto w-full space-y-6 md:space-y-8">
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div class="bg-gradient-to-r from-sharp-purple/20 to-transparent border border-sharp-purple/30 p-5 md:p-6 rounded-xl relative overflow-hidden">
                    <div class="absolute -right-4 -bottom-4 opacity-10 text-sharp-purple"><i data-lucide="database" class="w-32 h-32"></i></div>
                    <div class="relative z-10">
                        <p class="text-xs text-lavender/50 mb-1 uppercase tracking-widest font-bold">Generated Datasets</p>
                        <h3 class="font-syne text-3xl md:text-4xl font-bold text-white"><?= number_format($total_entries) ?> <span class="text-lg md:text-2xl text-lavender/30">/ <?= number_format($total_possible) ?></span></h3>
                        <div class="w-full bg-matte-black border border-white/10 rounded-full h-2 mt-4 overflow-hidden">
                            <div class="h-full bg-sharp-purple rounded-full" style="width: <?= $progress_pct ?>%"></div>
                        </div>
                        <div class="flex justify-between items-center mt-2">
                            <p class="text-[10px] md:text-xs text-lavender/40"><span class="text-white font-bold"><?= number_format($missing_count) ?></span> missing combinations</p>
                            <p class="text-[10px] md:text-xs font-bold text-sharp-purple"><?= $progress_pct ?>%</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Tabbed Interface & Database Area -->
            <div class="bg-card-dark border border-white/5 rounded-xl overflow-hidden flex flex-col">
                
                <!-- Tabs Header -->
                <div class="flex flex-wrap items-center justify-between border-b border-white/5 bg-panel-dark/40 px-4 pt-4 gap-4">
                    <div class="flex gap-4 md:gap-6">
                        <button onclick="switchView('live')" id="tab-live" class="pb-4 border-b-2 border-sharp-purple text-white font-bold text-sm transition-all focus:outline-none">Live Data</button>
                        <button onclick="switchView('missing')" id="tab-missing" class="pb-4 border-b-2 border-transparent text-lavender/50 hover:text-white font-bold text-sm transition-all focus:outline-none">
                            Missing <span class="bg-white/10 px-2 py-0.5 rounded-full text-[10px] ml-1"><?= number_format($missing_count) ?></span>
                        </button>
                        <button onclick="switchView('rules')" id="tab-rules" class="pb-4 border-b-2 border-transparent text-lavender/50 hover:text-white font-bold text-sm transition-all focus:outline-none">
                            Global Rules
                        </button>
                    </div>
                    <div class="pb-4 flex flex-wrap gap-3">
                        <a href="?export=csv" class="px-4 py-2 bg-white/5 text-lavender/70 border border-white/10 rounded-lg text-xs font-bold hover:bg-white/10 hover:text-white transition-all flex items-center gap-2">
                            <i data-lucide="download" class="w-3.5 h-3.5"></i> Export CSV
                        </a>
                        <button onclick="openAddModal()" class="px-4 py-2 bg-sharp-purple text-white rounded-lg text-xs font-bold shadow-[0_0_15px_rgba(126,34,206,0.3)] hover:bg-white hover:text-matte-black transition-all flex items-center gap-2">
                            <i data-lucide="plus" class="w-3.5 h-3.5"></i> Add New
                        </button>
                    </div>
                </div>

                <!-- LIVE DATA VIEW -->
                <div id="view-live" class="w-full">
                    <div class="w-full">
                        <table class="w-full text-left border-collapse">
                            <thead class="hidden md:table-header-group">
                                <tr class="bg-matte-black text-xs uppercase tracking-widest text-lavender/40 font-syne">
                                    <th class="p-4 border-b border-white/5">Key (City & Niche)</th>
                                    <th class="p-4 border-b border-white/5">Market Analysis</th>
                                    <th class="p-4 border-b border-white/5">Local SEO Insight</th>
                                    <th class="p-4 border-b border-white/5 text-right w-32">Actions</th>
                                </tr>
                            </thead>
                            <tbody class="block md:table-row-group">
                                <?php if ($total_entries === 0): ?>
                                    <tr class="block md:table-row"><td colspan="4" class="block md:table-cell p-8 text-center text-lavender/40">No city-niche data found. Use the Missing Combos tab to generate.</td></tr>
                                <?php endif; ?>

                                <?php foreach($display_data as $key => $data): 
                                    if($key === '_readme') continue;
                                    $json_string = htmlspecialchars(json_encode($data), ENT_QUOTES, 'UTF-8');
                                ?>
                                <tr class="block md:table-row hover:bg-white/5 border-b border-white/5 transition-colors group p-4 md:p-0 relative">
                                    
                                    <div class="absolute top-4 right-4 md:hidden flex gap-2 z-10">
                                        <button type="button" onclick="openEditModal('<?= esc_js($key) ?>', <?= $json_string ?>)" class="w-8 h-8 rounded border border-blue-500/30 bg-blue-500/10 text-blue-400 flex items-center justify-center">
                                            <i data-lucide="edit-2" class="w-4 h-4"></i>
                                        </button>
                                    </div>

                                    <td class="block md:table-cell md:p-4 align-top mb-3 md:mb-0 pr-12 md:pr-4">
                                        <div class="text-sm font-bold text-white mb-2 md:mb-1"><?= esc_html($key) ?></div>
                                        <div class="flex flex-wrap gap-1.5 md:gap-1">
                                            <span class="px-2 py-1 md:py-0.5 bg-white/5 border border-white/10 rounded text-[10px] text-lavender/60 uppercase tracking-widest"><?= ucwords(str_replace('-', ' ', $data['city'] ?? '')) ?></span>
                                            <span class="px-2 py-1 md:py-0.5 bg-sharp-purple/10 border border-sharp-purple/20 text-sharp-purple rounded text-[10px] uppercase tracking-widest font-bold"><?= ucwords(str_replace('-', ' ', $data['niche'] ?? '')) ?></span>
                                        </div>
                                        <?php if(isset($data['last_updated'])): ?>
                                            <div class="text-[9px] text-lavender/30 mt-2 font-mono">Updated: <?= date('M j, Y', strtotime($data['last_updated'])) ?></div>
                                        <?php endif; ?>
                                    </td>
                                    <td class="block md:table-cell md:p-4 align-top mb-4 md:mb-0">
                                        <div class="grid grid-cols-2 md:grid-cols-2 gap-2 text-xs">
                                            <div class="bg-matte-black p-2 md:p-2 rounded border border-white/5">
                                                <span class="block text-[9px] text-lavender/40 uppercase mb-0.5">Total Size</span>
                                                <span class="text-white font-mono"><?= number_format($data['estimated_businesses'] ?? 0) ?></span>
                                            </div>
                                            <div class="bg-matte-black p-2 md:p-2 rounded border border-white/5">
                                                <span class="block text-[9px] text-yellow-400 uppercase mb-0.5">No Website</span>
                                                <span class="text-yellow-400 font-mono"><?= $data['pct_without_website'] ?? 0 ?>%</span>
                                            </div>
                                            <div class="col-span-2 bg-matte-black p-2.5 md:p-2 rounded border border-white/5 flex justify-between items-center">
                                                <span class="block text-[9px] text-success-green uppercase">Typical Cost</span>
                                                <span class="text-success-green font-mono font-bold text-sm md:text-xs">₦<?= number_format($data['avg_cost_typical'] ?? 0) ?></span>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="block md:table-cell md:p-4 align-top mb-2 md:mb-0">
                                        <p class="text-xs text-lavender/70 line-clamp-3 leading-relaxed mb-2" title="<?= esc_attr($data['local_insight'] ?? '') ?>">
                                            <span class="text-sharp-purple font-bold">Insight:</span> <?= esc_html($data['local_insight'] ?? 'No insight fetched.') ?>
                                        </p>
                                        <p class="text-[10px] text-lavender/40 line-clamp-2">
                                            <span class="font-bold text-lavender/60">Edge:</span> <?= esc_html($data['top_ranked_edge'] ?? '') ?>
                                        </p>
                                    </td>
                                    <td class="hidden md:table-cell p-4 align-top text-right">
                                        <div class="flex justify-end gap-2 opacity-100 sm:opacity-0 sm:group-hover:opacity-100 transition-opacity">
                                            <button type="button" onclick="openEditModal('<?= esc_js($key) ?>', <?= $json_string ?>)" title="Edit Data" class="w-8 h-8 rounded border border-blue-500/30 bg-blue-500/10 text-blue-400 hover:bg-blue-500 hover:text-white transition-colors inline-flex items-center justify-center">
                                                <i data-lucide="edit-2" class="w-4 h-4"></i>
                                            </button>
                                            <form method="POST" class="inline" onsubmit="return confirm('Are you sure you want to completely delete this data block?');">
                                                <input type="hidden" name="action" value="delete_entry">
                                                <input type="hidden" name="entry_key" value="<?= esc_attr($key) ?>">
                                                <button type="submit" title="Delete Data" class="w-8 h-8 rounded border border-red-500/30 bg-red-500/10 text-red-400 hover:bg-red-500 hover:text-white transition-colors inline-flex items-center justify-center">
                                                    <i data-lucide="trash-2" class="w-4 h-4"></i>
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                    
                                    <td class="block md:hidden mt-3 pt-3 border-t border-white/5 text-right">
                                        <form method="POST" class="inline" onsubmit="return confirm('Are you sure you want to completely delete this data block?');">
                                            <input type="hidden" name="action" value="delete_entry">
                                            <input type="hidden" name="entry_key" value="<?= esc_attr($key) ?>">
                                            <button type="submit" class="text-[10px] font-bold text-red-400 uppercase tracking-widest flex items-center gap-1 ml-auto py-1">
                                                <i data-lucide="trash-2" class="w-3 h-3"></i> Delete Entry
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- MISSING COMBOS VIEW -->
                <div id="view-missing" class="w-full hidden">
                    <div class="p-4 border-b border-white/5 flex justify-between items-center bg-black/20">
                        <p class="text-xs text-lavender/50 max-w-sm">Select combinations below to generate them instantly via Gemini.</p>
                        <button id="btn-generate-selected" onclick="generateSelected()" class="px-4 py-2 bg-blue-500/20 text-blue-400 border border-blue-500/30 rounded-lg text-xs font-bold hover:bg-blue-500 hover:text-white transition-all flex items-center gap-2 flex-shrink-0">
                            <i data-lucide="zap" class="w-3.5 h-3.5"></i> Generate Selected
                        </button>
                    </div>
                    <div class="overflow-x-auto max-h-[600px] overflow-y-auto relative w-full">
                        <table class="w-full text-left border-collapse min-w-[500px]">
                            <thead class="sticky top-0 bg-matte-black z-10">
                                <tr class="text-xs uppercase tracking-widest text-lavender/40 font-syne">
                                    <th class="p-4 border-b border-white/5 w-12">
                                        <input type="checkbox" onclick="toggleSelectAll(this)" class="bulk-cb w-4 h-4 rounded border-white/20 bg-black cursor-pointer">
                                    </th>
                                    <th class="p-4 border-b border-white/5">City</th>
                                    <th class="p-4 border-b border-white/5">Niche</th>
                                    <th class="p-4 border-b border-white/5 text-right w-32">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (empty($missing_combos)): ?>
                                    <tr><td colspan="4" class="p-8 text-center text-success-green font-bold">All combinations generated! 100% complete.</td></tr>
                                <?php else: ?>
                                    <?php foreach($missing_combos as $mc): ?>
                                    <tr class="border-b border-white/5 hover:bg-white/5 transition-colors">
                                        <td class="p-4">
                                            <input type="checkbox" class="bulk-cb cb-item w-4 h-4 rounded border-white/20 bg-black cursor-pointer" data-city="<?= esc_attr($mc['city_slug']) ?>" data-niche="<?= esc_attr($mc['niche_slug']) ?>">
                                        </td>
                                        <td class="p-4 text-sm text-white font-bold"><?= esc_html($mc['city_name']) ?></td>
                                        <td class="p-4 text-sm text-lavender/80"><?= esc_html($mc['niche_name']) ?></td>
                                        <td class="p-4 text-right">
                                            <button type="button" onclick="generateMissingItem('<?= esc_js($mc['city_slug']) ?>', '<?= esc_js($mc['niche_slug']) ?>', this)" class="btn-gen-single px-3 py-1.5 bg-sharp-purple/10 text-sharp-purple border border-sharp-purple/20 rounded text-xs font-bold hover:bg-sharp-purple hover:text-white transition-all inline-flex items-center gap-1.5">
                                                <i data-lucide="zap" class="w-3.5 h-3.5"></i> Generate
                                            </button>
                                        </td>
                                    </tr>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- GLOBAL RULES VIEW -->
                <div id="view-rules" class="w-full hidden">
                    <div class="p-4 border-b border-white/5 bg-black/20">
                        <p class="text-xs text-lavender/50">Set absolute minimums and baseline targets for each niche. When Gemini generates new cities for a niche, it will strictly obey these prices.</p>
                    </div>
                    <form method="POST" class="w-full">
                        <input type="hidden" name="action" value="save_rules">
                        <div class="overflow-x-auto max-h-[600px] overflow-y-auto relative w-full">
                            <table class="w-full text-left border-collapse min-w-[600px]">
                                <thead class="sticky top-0 bg-matte-black z-10">
                                    <tr class="text-xs uppercase tracking-widest text-lavender/40 font-syne">
                                        <th class="p-4 border-b border-white/5">Niche</th>
                                        <th class="p-4 border-b border-white/5">Base / Low Cost (₦)</th>
                                        <th class="p-4 border-b border-white/5">Typical Cost (₦)</th>
                                        <th class="p-4 border-b border-white/5">High Cost (₦)</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach($niches as $niche): 
                                        $rule = $niche_rules[$niche->post_name] ?? [];
                                        $low = !empty($rule['low']) ? $rule['low'] : 120000;
                                        $typ = !empty($rule['typical']) ? $rule['typical'] : 250000;
                                        $high = !empty($rule['high']) ? $rule['high'] : 450000;
                                    ?>
                                    <tr class="border-b border-white/5 hover:bg-white/5 transition-colors">
                                        <td class="p-4 text-sm text-white font-bold"><?= esc_html($niche->post_title) ?></td>
                                        <td class="p-4">
                                            <input type="number" name="rules[<?= esc_attr($niche->post_name) ?>][low]" value="<?= $low ?>" class="w-full bg-black/50 border border-white/10 rounded p-2 text-xs text-white focus:border-sharp-purple outline-none">
                                        </td>
                                        <td class="p-4">
                                            <input type="number" name="rules[<?= esc_attr($niche->post_name) ?>][typical]" value="<?= $typ ?>" class="w-full bg-success-green/10 border border-success-green/30 rounded p-2 text-xs text-success-green font-bold focus:border-success-green outline-none">
                                        </td>
                                        <td class="p-4">
                                            <input type="number" name="rules[<?= esc_attr($niche->post_name) ?>][high]" value="<?= $high ?>" class="w-full bg-black/50 border border-white/10 rounded p-2 text-xs text-white focus:border-sharp-purple outline-none">
                                        </td>
                                    </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                        <div class="p-4 bg-card-dark border-t border-white/5 flex justify-end">
                            <button type="submit" class="px-6 py-2.5 bg-sharp-purple text-white rounded-lg text-xs font-bold shadow-[0_0_15px_rgba(126,34,206,0.3)] hover:bg-white hover:text-matte-black transition-all flex items-center gap-2">
                                <i data-lucide="save" class="w-4 h-4"></i> Save Global Rules
                            </button>
                        </div>
                    </form>
                </div>

            </div>
        </div>
    </main>

    <!-- DYNAMIC MODAL (For Add & Edit) -->
    <div id="edit-modal" class="fixed inset-0 z-50 hidden">
        <div class="absolute inset-0 bg-black/80 backdrop-blur-sm modal-overlay" onclick="closeEditModal()"></div>
        
        <div class="absolute inset-y-0 right-0 w-full md:w-[600px] bg-panel-dark border-l border-white/10 shadow-2xl flex flex-col modal-panel translate-x-full">
            
            <div class="h-auto min-h-[4rem] py-4 border-b border-white/5 flex items-center justify-between px-4 md:px-6 bg-card-dark flex-shrink-0">
                <div id="modal-edit-header" class="w-full flex justify-between items-center pr-4">
                    <div>
                        <h3 class="font-syne text-base md:text-lg font-bold text-white">Edit Entry</h3>
                        <p class="text-[10px] md:text-xs text-sharp-purple font-mono mt-1" id="modal-key-display">city_niche</p>
                    </div>
                    <button type="button" id="btn-ai-regenerate" onclick="autoFillWithAI()" class="hidden px-3 py-1.5 bg-blue-500/10 text-blue-400 border border-blue-500/20 rounded text-xs font-bold hover:bg-blue-500 hover:text-white transition-all flex items-center gap-1.5">
                        <i data-lucide="sparkles" class="w-3.5 h-3.5"></i> AI Regenerate
                    </button>
                </div>
                
                <div id="modal-add-header" class="hidden flex-col gap-3 w-full pr-4">
                    <div class="flex justify-between items-center w-full">
                        <h3 class="font-syne text-base md:text-lg font-bold text-white">Add New Entry</h3>
                        <button type="button" id="btn-ai-autofill" onclick="autoFillWithAI()" class="px-3 py-1.5 bg-blue-500/10 text-blue-400 border border-blue-500/20 rounded text-xs font-bold hover:bg-blue-500 hover:text-white transition-all flex items-center gap-1.5">
                            <i data-lucide="sparkles" class="w-3.5 h-3.5"></i> Auto-Fill with AI
                        </button>
                    </div>
                    <div class="flex flex-wrap md:flex-nowrap gap-2">
                        <select name="city" form="edit-form" id="add-city-select" class="w-full md:w-auto bg-matte-black border border-white/10 text-white rounded p-2 text-xs outline-none focus:border-sharp-purple">
                            <?php foreach($locations as $loc): ?>
                                <option value="<?= esc_attr($loc->post_name) ?>"><?= esc_html($loc->post_title) ?></option>
                            <?php endforeach; ?>
                        </select>
                        <select name="niche" form="edit-form" id="add-niche-select" class="w-full md:w-auto bg-matte-black border border-white/10 text-white rounded p-2 text-xs outline-none focus:border-sharp-purple">
                            <?php foreach($niches as $niche): ?>
                                <option value="<?= esc_attr($niche->post_name) ?>"><?= esc_html($niche->post_title) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>

                <button onclick="closeEditModal()" class="w-10 h-10 md:w-8 md:h-8 flex items-center justify-center rounded-lg bg-white/5 text-lavender/50 hover:bg-white/10 hover:text-white transition-colors flex-shrink-0 self-start">
                    <i data-lucide="x" class="w-5 h-5 md:w-4 md:h-4"></i>
                </button>
            </div>

            <div class="flex-1 overflow-y-auto p-4 md:p-6 scroll-smooth">
                <form method="POST" id="edit-form" class="space-y-6">
                    <input type="hidden" name="action" id="modal-action" value="edit_entry">
                    <input type="hidden" name="entry_key" id="modal-entry-key">

                    <div class="grid grid-cols-2 gap-4 border-b border-white/5 pb-6">
                        <div>
                            <label class="block text-[10px] uppercase tracking-widest text-lavender/50 mb-2">Est. Businesses</label>
                            <input type="number" name="estimated_businesses" id="mod-est" class="w-full bg-matte-black border border-white/10 rounded p-3 text-base md:text-sm text-white focus:border-sharp-purple outline-none">
                        </div>
                        <div>
                            <label class="block text-[10px] uppercase tracking-widest text-lavender/50 mb-2">% No Website</label>
                            <input type="number" name="pct_without_website" id="mod-pct" class="w-full bg-matte-black border border-white/10 rounded p-3 text-base md:text-sm text-white focus:border-sharp-purple outline-none">
                        </div>
                    </div>

                    <!-- PRICING CONTROLS -->
                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 border-b border-white/5 pb-6">
                        <div class="col-span-1 sm:col-span-3">
                            <h4 class="text-xs font-bold text-white mb-1">Pricing Configuration (Naira)</h4>
                            <p class="text-[10px] text-lavender/40 mb-3">Controls the calculator and dynamic range displays.</p>
                        </div>
                        <div>
                            <label class="block text-[10px] uppercase tracking-widest text-lavender/50 mb-2">Low Cost</label>
                            <input type="number" name="avg_cost_low" id="mod-cost-low" class="w-full bg-matte-black border border-white/10 rounded p-3 text-base md:text-sm text-white focus:border-sharp-purple outline-none">
                        </div>
                        <div>
                            <label class="block text-[10px] uppercase tracking-widest text-success-green mb-2">Typical Cost</label>
                            <input type="number" name="avg_cost_typical" id="mod-cost-typical" class="w-full bg-success-green/10 border border-success-green/30 rounded p-3 text-base md:text-sm text-success-green font-mono font-bold focus:border-success-green outline-none">
                        </div>
                        <div>
                            <label class="block text-[10px] uppercase tracking-widest text-lavender/50 mb-2">High Cost</label>
                            <input type="number" name="avg_cost_high" id="mod-cost-high" class="w-full bg-matte-black border border-white/10 rounded p-3 text-base md:text-sm text-white focus:border-sharp-purple outline-none">
                        </div>
                    </div>

                    <div class="space-y-4">
                        <div>
                            <label class="block text-[10px] uppercase tracking-widest text-sharp-purple mb-2">Competitive Landscape</label>
                            <textarea name="competitive_landscape" id="mod-land" rows="3" class="w-full bg-matte-black border border-white/10 rounded-lg p-3 text-base md:text-sm text-white focus:border-sharp-purple outline-none"></textarea>
                        </div>
                        <div>
                            <label class="block text-[10px] uppercase tracking-widest text-sharp-purple mb-2">Top Ranked Edge</label>
                            <textarea name="top_ranked_edge" id="mod-edge" rows="3" class="w-full bg-matte-black border border-white/10 rounded-lg p-3 text-base md:text-sm text-white focus:border-sharp-purple outline-none"></textarea>
                        </div>
                        <div>
                            <label class="block text-[10px] uppercase tracking-widest text-sharp-purple mb-2">What Most Lack</label>
                            <textarea name="what_most_lack" id="mod-lack" rows="3" class="w-full bg-matte-black border border-white/10 rounded-lg p-3 text-base md:text-sm text-white focus:border-sharp-purple outline-none"></textarea>
                        </div>
                        <div>
                            <label class="block text-[10px] uppercase tracking-widest text-blue-400 mb-2">Local SEO Insight</label>
                            <textarea name="local_insight" id="mod-insight" rows="3" class="w-full bg-matte-black border border-white/10 rounded-lg p-3 text-base md:text-sm text-white focus:border-sharp-purple outline-none"></textarea>
                        </div>
                        
                        <div class="p-4 bg-white/5 border border-white/10 rounded-lg space-y-4 mt-4">
                            <div>
                                <label class="block text-[10px] uppercase tracking-widest text-lavender/50 mb-2">Unique FAQ Question</label>
                                <input type="text" name="unique_faq" id="mod-faq-q" class="w-full bg-matte-black border border-white/10 rounded p-3 text-base md:text-sm text-white focus:border-sharp-purple outline-none">
                            </div>
                            <div>
                                <label class="block text-[10px] uppercase tracking-widest text-lavender/50 mb-2">FAQ Answer</label>
                                <textarea name="unique_faq_answer" id="mod-faq-a" rows="3" class="w-full bg-matte-black border border-white/10 rounded-lg p-3 text-base md:text-sm text-white focus:border-sharp-purple outline-none"></textarea>
                            </div>
                        </div>

                        <div class="pb-6">
                            <label class="block text-[10px] uppercase tracking-widest text-lavender/50 mb-2">Urgency Note</label>
                            <input type="text" name="urgency_note" id="mod-urgency" class="w-full bg-matte-black border border-white/10 rounded p-3 text-base md:text-sm text-white focus:border-sharp-purple outline-none">
                        </div>
                    </div>
                </form>
            </div>
            
            <div class="p-4 md:p-6 border-t border-white/5 bg-card-dark flex-shrink-0 flex gap-3 justify-end sticky bottom-0 z-20">
                <button type="button" onclick="closeEditModal()" class="px-5 py-3 md:py-2.5 bg-white/5 text-white rounded-lg text-sm font-bold hover:bg-white/10 transition-colors w-full md:w-auto">Cancel</button>
                <button type="submit" form="edit-form" id="modal-submit-btn" class="px-6 py-3 md:py-2.5 bg-sharp-purple text-white rounded-lg text-sm font-bold shadow-[0_0_15px_rgba(126,34,206,0.3)] hover:bg-white hover:text-matte-black transition-all flex items-center justify-center gap-2 w-full md:w-auto">
                    <i data-lucide="save" class="w-4 h-4"></i> Save Changes
                </button>
            </div>
        </div>
    </div>

    <script>
        lucide.createIcons();

        const modal = document.getElementById('edit-modal');
        const modalPanel = modal.querySelector('.modal-panel');

        function switchView(view) {
            document.getElementById('view-live').classList.toggle('hidden', view !== 'live');
            document.getElementById('view-missing').classList.toggle('hidden', view !== 'missing');
            document.getElementById('view-rules').classList.toggle('hidden', view !== 'rules');
            
            const activeClass = 'pb-4 border-b-2 border-sharp-purple text-white font-bold text-sm transition-all focus:outline-none';
            const inactiveClass = 'pb-4 border-b-2 border-transparent text-lavender/50 hover:text-white font-bold text-sm transition-all focus:outline-none';

            document.getElementById('tab-live').className = view === 'live' ? activeClass : inactiveClass;
            document.getElementById('tab-missing').className = view === 'missing' ? activeClass : inactiveClass;
            document.getElementById('tab-rules').className = view === 'rules' ? activeClass : inactiveClass;
        }

        function toggleSelectAll(masterCb) {
            document.querySelectorAll('.cb-item').forEach(cb => {
                if(!cb.disabled && !cb.closest('tr').classList.contains('opacity-50')) {
                    cb.checked = masterCb.checked;
                }
            });
        }

        async function generateMissingItem(citySlug, nicheSlug, btnElement) {
            const originalHtml = btnElement.innerHTML;
            btnElement.innerHTML = '<div class="w-3.5 h-3.5 rounded-full border-2 border-sharp-purple border-t-transparent animate-spin"></div>';
            btnElement.disabled = true;

            try {
                const res = await fetch(`?ajax=generate_ai&save=1&city=${citySlug}&niche=${nicheSlug}`);
                const data = await res.json();
                
                if (data.status === 'success') {
                    btnElement.innerHTML = '<i data-lucide="check" class="w-4 h-4"></i> Done';
                    btnElement.classList.replace('bg-sharp-purple/10', 'bg-success-green/10');
                    btnElement.classList.replace('text-sharp-purple', 'text-success-green');
                    btnElement.classList.replace('border-sharp-purple/20', 'border-success-green/20');
                    
                    const row = btnElement.closest('tr');
                    row.classList.add('opacity-50', 'pointer-events-none');
                    const cb = row.querySelector('.cb-item');
                    if(cb) cb.checked = false;
                } else {
                    alert('Error: ' + (data.error || 'Failed to generate.'));
                    btnElement.innerHTML = originalHtml;
                    btnElement.disabled = false;
                }
            } catch(e) {
                alert('Network Error occurred while generating.');
                btnElement.innerHTML = originalHtml;
                btnElement.disabled = false;
            }
            if (typeof lucide !== 'undefined') lucide.createIcons();
        }

        async function generateSelected() {
            const checkboxes = document.querySelectorAll('.cb-item:checked');
            if (checkboxes.length === 0) return alert('Select at least one combination first.');
            
            const btn = document.getElementById('btn-generate-selected');
            btn.innerHTML = `Generating (0/${checkboxes.length})...`;
            btn.disabled = true;

            let count = 0;
            for (const cb of checkboxes) {
                const row = cb.closest('tr');
                const genBtn = row.querySelector('.btn-gen-single');
                const citySlug = cb.dataset.city;
                const nicheSlug = cb.dataset.niche;
                
                await generateMissingItem(citySlug, nicheSlug, genBtn);
                count++;
                btn.innerHTML = `Generating (${count}/${checkboxes.length})...`;
                
                // Small 2-second delay to avoid hitting Gemini rate limits
                await new Promise(r => setTimeout(r, 2000)); 
            }

            btn.innerHTML = '<i data-lucide="check-circle" class="w-4 h-4"></i> All Selected Generated';
            
            // Reload page to refresh stats and live data table
            setTimeout(() => window.location.reload(), 1500);
        }

        function openEditModal(key, data) {
            document.body.style.overflow = 'hidden'; 
            
            document.getElementById('modal-action').value = 'edit_entry';
            document.getElementById('modal-edit-header').classList.remove('hidden');
            document.getElementById('modal-edit-header').classList.add('flex');
            document.getElementById('btn-ai-regenerate').classList.remove('hidden');
            
            document.getElementById('modal-add-header').classList.add('hidden');
            document.getElementById('modal-add-header').classList.remove('flex');
            document.getElementById('modal-submit-btn').innerHTML = '<i data-lucide="save" class="w-4 h-4"></i> Save Changes';

            document.getElementById('modal-entry-key').value = key;
            document.getElementById('modal-key-display').innerText = key;
            
            const parts = key.split('_');
            document.getElementById('add-city-select').value = parts[0];
            document.getElementById('add-niche-select').value = parts[1];

            document.getElementById('mod-est').value = data.estimated_businesses || 0;
            document.getElementById('mod-pct').value = data.pct_without_website || 0;
            document.getElementById('mod-cost-low').value = data.avg_cost_low || 120000;
            document.getElementById('mod-cost-typical').value = data.avg_cost_typical || 0;
            document.getElementById('mod-cost-high').value = data.avg_cost_high || 800000;
            
            document.getElementById('mod-land').value = data.competitive_landscape || '';
            document.getElementById('mod-edge').value = data.top_ranked_edge || '';
            document.getElementById('mod-lack').value = data.what_most_lack || '';
            document.getElementById('mod-insight').value = data.local_insight || '';
            document.getElementById('mod-faq-q').value = data.unique_faq || '';
            document.getElementById('mod-faq-a').value = data.unique_faq_answer || '';
            document.getElementById('mod-urgency').value = data.urgency_note || '';

            showModal();
        }

        function openAddModal() {
            document.body.style.overflow = 'hidden'; 
            
            document.getElementById('modal-action').value = 'add_entry';
            document.getElementById('modal-edit-header').classList.add('hidden');
            document.getElementById('modal-edit-header').classList.remove('flex');
            document.getElementById('btn-ai-regenerate').classList.add('hidden');
            
            document.getElementById('modal-add-header').classList.remove('hidden');
            document.getElementById('modal-add-header').classList.add('flex');
            document.getElementById('modal-submit-btn').innerHTML = '<i data-lucide="plus" class="w-4 h-4"></i> Save Entry';

            document.getElementById('edit-form').reset();
            
            document.getElementById('mod-cost-low').value = 120000;
            document.getElementById('mod-cost-typical').value = 350000;
            document.getElementById('mod-cost-high').value = 800000;
            document.getElementById('mod-pct').value = 85;

            showModal();
        }

        async function autoFillWithAI() {
            const city = document.getElementById('add-city-select').value;
            const niche = document.getElementById('add-niche-select').value;
            
            const btn1 = document.getElementById('btn-ai-autofill');
            const btn2 = document.getElementById('btn-ai-regenerate');
            const originalText1 = btn1.innerHTML;
            const originalText2 = btn2.innerHTML;
            const loadingText = '<div class="w-3.5 h-3.5 rounded-full border-2 border-blue-500 border-t-transparent animate-spin"></div> Thinking...';
            
            btn1.innerHTML = loadingText;
            btn1.disabled = true;
            btn2.innerHTML = loadingText;
            btn2.disabled = true;

            try {
                const response = await fetch(`?ajax=generate_ai&city=${city}&niche=${niche}`);
                const data = await response.json();

                if (data.error) {
                    alert('AI Error: ' + data.error);
                } else {
                    document.getElementById('mod-est').value = data.estimated_businesses || 0;
                    document.getElementById('mod-pct').value = data.pct_without_website || 85;
                    document.getElementById('mod-cost-low').value = data.avg_cost_low || 120000;
                    document.getElementById('mod-cost-typical').value = data.avg_cost_typical || 0;
                    document.getElementById('mod-cost-high').value = data.avg_cost_high || 800000;
                    document.getElementById('mod-land').value = data.competitive_landscape || '';
                    document.getElementById('mod-edge').value = data.top_ranked_edge || '';
                    document.getElementById('mod-lack').value = data.what_most_lack || '';
                    document.getElementById('mod-insight').value = data.local_insight || '';
                    document.getElementById('mod-faq-q').value = data.unique_faq || '';
                    document.getElementById('mod-faq-a').value = data.unique_faq_answer || '';
                    document.getElementById('mod-urgency').value = data.urgency_note || '';
                    
                    const inputs = document.querySelectorAll('#edit-form input, #edit-form textarea');
                    inputs.forEach(el => {
                        el.classList.add('border-success-green', 'bg-success-green/10');
                        setTimeout(() => el.classList.remove('border-success-green', 'bg-success-green/10'), 1500);
                    });
                }
            } catch (err) {
                alert('Connection failed. Please try again.');
            }

            btn1.innerHTML = originalText1;
            btn1.disabled = false;
            btn2.innerHTML = originalText2;
            btn2.disabled = false;
            lucide.createIcons();
        }

        function showModal() {
            modal.classList.remove('hidden');
            const scrollArea = modal.querySelector('.overflow-y-auto');
            if(scrollArea) scrollArea.scrollTop = 0;

            setTimeout(() => {
                modalPanel.classList.remove('translate-x-full');
                modalPanel.classList.add('translate-x-0');
            }, 10);
            
            lucide.createIcons();
        }

        function closeEditModal() {
            document.body.style.overflow = ''; 
            
            modalPanel.classList.remove('translate-x-0');
            modalPanel.classList.add('translate-x-full');
            setTimeout(() => {
                modal.classList.add('hidden');
            }, 300);
        }
    </script>
</body>
</html>