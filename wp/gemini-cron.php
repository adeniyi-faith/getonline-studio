<?php
/**
 * GETONLINE STUDIO - GEMINI CRON AUTOMATION v2.0
 * Generates 5 missing city_niche pairs per run via Gemini.
 * Sends a single batch email report after every run.
 *
 * HOW TO USE:
 * Set up a cron job in cPanel to run this file every 15 minutes:
 * curl -s "https://getonlinestudio.com/wp/gemini-cron.php?secret=faith_studio_2026" > /dev/null
 */

// --- OVERRIDE PHP EXECUTION LIMIT ---
set_time_limit(300); // 5 minutes max

// --- CONFIGURATION ---
$admin_email  = 'mrfaithadeniyi@gmail.com';
$secret_key   = 'faith_studio_2026';
$api_key      = "AIzaSyDm2dX5no2AauwbP9prOIx9ZAFFjw7rcj8";
$api_model    = "gemini-2.5-flash";
$batch_size   = 1;

// --- LOCK FILE ---
$lock_file = sys_get_temp_dir() . '/gemini-cron.lock';
if (file_exists($lock_file)) {
    $lock_age = time() - filemtime($lock_file);
    if ($lock_age < 270) {
        die(json_encode(['status' => 'skipped', 'message' => 'Another instance is already running.']));
    }
    unlink($lock_file);
}
file_put_contents($lock_file, getmypid());
register_shutdown_function(function() use ($lock_file) {
    if (file_exists($lock_file)) unlink($lock_file);
});

// --- SECURITY CHECK ---
if (!isset($_GET['secret']) || $_GET['secret'] !== $secret_key) {
    die(json_encode(['status' => 'error', 'message' => 'Unauthorized.']));
}

// --- BOOT WORDPRESS ---
define('WP_USE_THEMES', false);
require_once(__DIR__ . '/wp-load.php');

// --- LOAD JSON DATA ---
$json_path = dirname(ABSPATH) . '/city-niche.json';
$json_data = file_exists($json_path) ? json_decode(file_get_contents($json_path), true) : [];

// --- FETCH LOCATIONS & NICHES ---
$locations = get_posts(['post_type' => 'pseo_location', 'numberposts' => -1, 'post_status' => 'publish']);
$niches    = get_posts(['post_type' => 'pseo_niche',    'numberposts' => -1, 'post_status' => 'publish']);

$total_possible = count($locations) * count($niches);
$total_entries  = count($json_data) - (isset($json_data['_readme']) ? 1 : 0);
$missing_count  = max(0, $total_possible - $total_entries);

// --- FIND MISSING PAIRS ---
shuffle($locations);
shuffle($niches);

$missing_pairs = [];
foreach ($locations as $loc) {
    foreach ($niches as $niche) {
        $key = $loc->post_name . '_' . $niche->post_name;
        if (!isset($json_data[$key])) {
            $missing_pairs[] = ['city' => $loc, 'niche' => $niche, 'key' => $key];
        }
        if (count($missing_pairs) >= $batch_size) break 2;
    }
}

// --- ALL DONE ---
if (empty($missing_pairs)) {
    $subject = "🟢 pSEO Cron Report: 100% COMPLETE!";
    $body    = "How far Boss,\n\nAmazing news! The Gemini automation has successfully generated all {$total_possible} City/Niche combinations.\n\nYour database is 100% full. You can now log into cPanel and pause the cron job.\n\nWell done on your end!";
    wp_mail($admin_email, $subject, $body);
    die(json_encode(['status' => 'success', 'message' => 'All combinations already generated.']));
}

// --- LOAD NICHE RULES ---
$rules_path  = dirname(ABSPATH) . '/niche-rules.json';
$niche_rules = file_exists($rules_path) ? json_decode(file_get_contents($rules_path), true) : [];

// --- DATA DIR ---
$data_dir = dirname(ABSPATH) . '/data/';
if (!is_dir($data_dir)) mkdir($data_dir, 0755, true);

// --- PROCESS EACH PAIR ---
$generated  = [];
$failed     = [];
$pairs_done = 0;

foreach ($missing_pairs as $pair) {
    $target_city  = $pair['city'];
    $target_niche = $pair['niche'];
    $target_key   = $pair['key'];
    $city_name    = $target_city->post_title;
    $niche_name   = $target_niche->post_title;

    $rule        = $niche_rules[$target_niche->post_name] ?? [];
    $min_low     = !empty($rule['low'])     ? $rule['low']     : 120000;
    $target_typ  = !empty($rule['typical']) ? $rule['typical'] : 350000;
    $target_high = !empty($rule['high'])    ? $rule['high']    : 1200000;

    $system_prompt = "You are a master business analyst and SEO strategist for GetOnline Studio in Nigeria.
Your task is to generate hyper-realistic, highly specific market data for the intersection of a specific Niche inside a specific City in Nigeria.
The data MUST be returned strictly as a JSON object matching the exact schema provided.
Do not use markdown blocks like \`\`\`json. Return pure JSON only.

STRICT RULES:
1. NEVER set 'avg_cost_low' to anything less than {$min_low}. {$min_low} Naira is the absolute minimum floor price.
2. For 'estimated_businesses', use highly realistic, conservative estimates based on standard Nigerian economic realities. Do not exaggerate.

SCHEMA REQUIREMENTS:
{
  \"city\": \"{$target_city->post_name}\",
  \"niche\": \"{$target_niche->post_name}\",
  \"estimated_businesses\": [Integer. Realistic estimate of how many of this business exist in this specific Nigerian city. E.g., 200 to 5000],
  \"pct_without_website\": [Integer 1-100. Usually between 70 to 95 for Nigerian local businesses],
  \"without_website\": [Integer. The math: estimated_businesses * (pct_without_website/100)],
  \"with_website\": [Integer. The remainder],
  \"avg_cost_low\": [Integer. Website cost lower bound in Naira. STRICT RULE: MUST BE {$min_low} OR HIGHER],
  \"avg_cost_typical\": [Integer. Typical cost in Naira, e.g. around {$target_typ}],
  \"avg_cost_high\": [Integer. Enterprise cost in Naira e.g. around {$target_high}],
  \"maintenance_monthly\": [Integer. Monthly maintenance in Naira e.g. 25000],
  \"competitive_landscape\": \"[Paragraph. Highly specific to this city and niche. Mention local behaviors, how they currently operate (WhatsApp, physical lobbying, etc). Max 50 words.]\",
  \"top_ranked_edge\": \"[Paragraph. What do the few successful ones with websites actually do right? E.g., SEO, Booking portals. Max 40 words.]\",
  \"what_most_lack\": \"[Paragraph. Their biggest digital mistake or missed opportunity. Max 40 words.]\",
  \"local_insight\": \"[Paragraph. A deep, Nigerian-context insight about this niche in this specific city. Max 40 words.]\",
  \"unique_faq\": \"Why does a {$niche_name} in {$city_name} need a corporate website?\",
  \"unique_faq_answer\": \"[Paragraph. Strong, persuasive answer pitching GetOnline Studio platforms. Max 50 words.]\",
  \"projects_completed\": 0,
  \"urgency_note\": \"Available for {$city_name} {$niche_name} digital infrastructure projects this month.\"
}";

    $user_prompt = "Generate the JSON market data for City: {$city_name} and Niche: {$niche_name}.";

    $url     = "https://generativelanguage.googleapis.com/v1beta/models/{$api_model}:generateContent?key={$api_key}";
    $payload = [
        'system_instruction' => ['parts' => [['text' => $system_prompt]]],
        'contents'           => [['role' => 'user', 'parts' => [['text' => $user_prompt]]]],
        'generationConfig'   => ['temperature' => 0.7, 'responseMimeType' => 'application/json']
    ];

    // Pause between calls to avoid Gemini rate limiting (429)
    if ($pairs_done > 0) sleep(5);

    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($payload));
    curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
    curl_setopt($ch, CURLOPT_TIMEOUT, 45);
    $response  = curl_exec($ch);
    $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    if ($http_code !== 200) {
        $failed[] = "{$city_name} x {$niche_name} (API error {$http_code})";
        continue;
    }

    $response_data  = json_decode($response, true);
    $generated_text = $response_data['candidates'][0]['content']['parts'][0]['text'] ?? '';

    if (!$generated_text) {
        $failed[] = "{$city_name} x {$niche_name} (empty response)";
        continue;
    }

    $clean_json     = trim(str_replace(['```json', '```'], '', $generated_text));
    $new_entry_data = json_decode($clean_json, true);

    if (json_last_error() !== JSON_ERROR_NONE || !is_array($new_entry_data)) {
        $failed[] = "{$city_name} x {$niche_name} (JSON parse error)";
        continue;
    }

    $new_entry_data['last_updated'] = current_time('mysql');

    // Save to master JSON (atomic)
    $json_data[$target_key] = $new_entry_data;
    $temp_master = $json_path . '.tmp';
    file_put_contents($temp_master, json_encode($json_data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
    rename($temp_master, $json_path);

    // Save to per-city file (atomic)
    $city_file      = $data_dir . $target_city->post_name . '-niche.json';
    $temp_city_file = $data_dir . $target_city->post_name . '-niche.tmp.json';
    $city_data      = file_exists($city_file) ? (json_decode(file_get_contents($city_file), true) ?: []) : [];
    $city_data[$target_key] = $new_entry_data;
    file_put_contents($temp_city_file, json_encode($city_data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
    rename($temp_city_file, $city_file);

    $generated[] = ['label' => "{$city_name} x {$niche_name}", 'cost' => $new_entry_data['avg_cost_typical']];
    $pairs_done++;
}

// --- SEND BATCH EMAIL ---
$new_total    = $total_entries + $pairs_done;
$new_missing  = max(0, $missing_count - $pairs_done);
$percent_done = round(($new_total / $total_possible) * 100, 1);

$subject = "⚡ pSEO Batch Report: {$pairs_done} Pairs Generated ({$percent_done}% Done)";

$body  = "How far Boss,\n\n";
$body .= "The Gemini Cron just completed a batch run. Here's the update:\n\n";
$body .= "✅ GENERATED THIS RUN ({$pairs_done} pairs):\n";
foreach ($generated as $g) {
    $body .= "- {$g['label']} — ₦" . number_format($g['cost']) . "\n";
}
if (!empty($failed)) {
    $body .= "\n⚠️ FAILED THIS RUN (" . count($failed) . "):\n";
    foreach ($failed as $f) {
        $body .= "- {$f}\n";
    }
}
$body .= "\n📊 PROGRESS REPORT:\n";
$body .= "- Total Generated So Far: " . number_format($new_total) . " ({$percent_done}%)\n";
$body .= "- Remaining: " . number_format($new_missing) . "\n";
$body .= "- Total Combinations: " . number_format($total_possible) . "\n\n";
$body .= "Next batch fires in 15 minutes.\n\n";
$body .= "Keep building!\nCommand Center Auto-Bot";

wp_mail($admin_email, $subject, $body);

echo json_encode([
    'status'    => 'success',
    'generated' => $pairs_done,
    'failed'    => count($failed),
    'progress'  => $percent_done . '%'
]);
?>
