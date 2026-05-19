<?php
// NICHE AUTO-BUILDER v3.0 — DUAL-SAVE ENGINE
// What's new in v3:
// - Saves reality_p1–p4 and pos1–pos4 as INDIVIDUAL post meta keys
//   so niche-hub.php (national page) can find them directly
// - Everything niche-landing.php uses still works exactly the same
// - Dashboard-compatible: logs progress to _build_log post meta
// - Reset tool: hit ?key=AUTOBOT_778899&reset=1 to force-reprocess all niches
//
// Cron Job (Every 15 Minutes):
// */15 * * * * curl -s "https://getonlinestudio.com/wp/niche-autobuilder-v3.php?key=AUTOBOT_778899" > /dev/null

set_time_limit(0);
ignore_user_abort(true);

// --- CONFIGURATION ---
define('PSEO_CRON_KEY',     'AUTOBOT_778899');
define('PSEO_BATCH_SIZE',   1);
define('PSEO_MAX_RETRIES',  3);
define('PSEO_REFRESH_DAYS', 60);
define('GEMINI_API_KEY',    'AIzaSyC1DPDJzt5psEkIDgqK3XztuVLgXnDwwZM');

// 1. Security Check
if (!isset($_GET['key']) || $_GET['key'] !== PSEO_CRON_KEY) {
    http_response_code(403);
    die('Forbidden: Invalid Cron Key.');
}

// 2. Boot WordPress silently
$wp_paths = [
    __DIR__ . '/wp-load.php',
    __DIR__ . '/wp/wp-load.php',
    dirname(__DIR__) . '/wp-load.php'
];
$wp_loaded = false;
foreach ($wp_paths as $path) {
    if (file_exists($path)) { require_once($path); $wp_loaded = true; break; }
}
if (!$wp_loaded) die("Fatal Error: Could not find wp-load.php");

$execution_log = ["🤖 Niche Auto-Builder v3.0 (Dual-Save Engine) Started at " . current_time('mysql')];

// ─── RESET TOOL ───────────────────────────────────────────────────────────────
// Visit ?key=AUTOBOT_778899&reset=1 to wipe all _built_at timestamps.
// This forces the autobuilder to reprocess every niche on the next cron runs.
if (!empty($_GET['reset']) && $_GET['reset'] === '1') {
    $all_niches = get_posts(['post_type' => 'pseo_niche', 'numberposts' => -1, 'post_status' => 'any']);
    $formats    = ['website-designer', 'website-developer', 'web-design-agency', 'website-design-services', 'branding-agency'];
    $wiped      = 0;
    foreach ($all_niches as $np) {
        foreach ($formats as $fmt) {
            delete_post_meta($np->ID, $fmt . '_built_at');
            $wiped++;
        }
    }
    die("✅ Reset complete. Wiped {$wiped} timestamps. All niches will be reprocessed on next cron runs.");
}
// ─────────────────────────────────────────────────────────────────────────────

// 3. Load city-niche.json
$city_niche_candidates = [
    dirname(__DIR__) . '/city-niche.json',
    __DIR__ . '/city-niche.json',
    dirname(dirname(__DIR__)) . '/city-niche.json'
];
$city_niche_data = [];
foreach ($city_niche_candidates as $path) {
    if (file_exists($path)) {
        $city_niche_data = json_decode(file_get_contents($path), true) ?: [];
        $execution_log[] = "📦 Loaded city-niche.json (" . count($city_niche_data) . " entries) from: " . $path;
        break;
    }
}
if (empty($city_niche_data)) {
    $execution_log[] = "⚠️  city-niche.json not found. Prompts will use generic context only.";
}

// 4. Load Master Niches from WordPress DB
$master_niches = [];
$niche_posts   = get_posts([
    'post_type'   => 'pseo_niche',
    'numberposts' => -1,
    'post_status' => 'any',
    'orderby'     => 'title',
    'order'       => 'ASC'
]);
foreach ($niche_posts as $np) {
    $master_niches[$np->post_name] = $np->post_title;
}
if (empty($master_niches)) {
    die("Fatal: No niches found in pseo_niche post type.");
}
$execution_log[] = "📋 Loaded " . count($master_niches) . " niches from database.";

// 5. Service formats
$service_formats = [
    'website-designer'        => 'Website Designer',
    'website-developer'       => 'Website Developer',
    'web-design-agency'       => 'Web Design Agency',
    'website-design-services' => 'Design Services',
    'branding-agency'         => 'Branding Agency'
];

// 6. Load active cities
$active_cities = get_posts([
    'post_type'   => 'pseo_location',
    'numberposts' => -1,
    'post_status' => 'publish',
    'orderby'     => 'title',
    'order'       => 'ASC'
]);
$city_map = [];
foreach ($active_cities as $loc) {
    $city_map[$loc->post_name] = $loc->post_title;
}
$execution_log[] = "🏙️  Loaded " . count($city_map) . " active cities.";

// 7. Aggregate city-niche.json intelligence for a niche across all cities
function get_niche_city_context($niche_slug, $city_niche_data, $city_map) {
    $ctx = [
        'city_examples'          => [],
        'competitive_landscapes' => [],
        'local_insights'         => [],
        'top_ranked_edges'       => [],
        'what_most_lack'         => [],
        'pricing_low'            => [],
        'pricing_high'           => [],
    ];
    foreach ($city_map as $city_slug => $city_name) {
        $key = "{$city_slug}_{$niche_slug}";
        if (!isset($city_niche_data[$key])) continue;
        $e = $city_niche_data[$key];
        if (!empty($e['estimated_businesses']) && !empty($e['pct_without_website']))
            $ctx['city_examples'][] = "{$city_name}: ~{$e['estimated_businesses']} businesses, {$e['pct_without_website']}% without a website";
        if (!empty($e['competitive_landscape']) && count($ctx['competitive_landscapes']) < 3)
            $ctx['competitive_landscapes'][] = "[{$city_name}] " . $e['competitive_landscape'];
        if (!empty($e['local_insight']) && count($ctx['local_insights']) < 3)
            $ctx['local_insights'][] = "[{$city_name}] " . $e['local_insight'];
        if (!empty($e['what_most_lack']) && count($ctx['what_most_lack']) < 2)
            $ctx['what_most_lack'][] = "[{$city_name}] " . $e['what_most_lack'];
        if (!empty($e['top_ranked_edge']) && count($ctx['top_ranked_edges']) < 2)
            $ctx['top_ranked_edges'][] = "[{$city_name}] " . $e['top_ranked_edge'];
        if (!empty($e['avg_cost_low']))  $ctx['pricing_low'][]  = $e['avg_cost_low'];
        if (!empty($e['avg_cost_high'])) $ctx['pricing_high'][] = $e['avg_cost_high'];
    }
    $ctx['pricing_summary'] = '';
    if (!empty($ctx['pricing_low']) && !empty($ctx['pricing_high'])) {
        $ctx['pricing_summary'] = "Nigerian market pricing ranges from ₦" . number_format(min($ctx['pricing_low'])) . " to ₦" . number_format(max($ctx['pricing_high'])) . ".";
    }
    return $ctx;
}

// 8. Build the intelligence brief
function build_niche_intelligence_brief($niche_name, $niche_slug, $city_niche_data, $city_map) {
    $ctx   = get_niche_city_context($niche_slug, $city_niche_data, $city_map);
    $upper = strtoupper($niche_name);
    $brief = "=== REAL NIGERIAN MARKET INTELLIGENCE: {$upper} ===\n\n";
    if (!empty($ctx['city_examples'])) {
        $brief .= "MARKET SIZE ACROSS NIGERIAN CITIES:\n";
        foreach (array_slice($ctx['city_examples'], 0, 5) as $ex) $brief .= "  - {$ex}\n";
        $brief .= "\n";
    }
    if (!empty($ctx['pricing_summary']))
        $brief .= "PRICING CONTEXT (use ₦ only, never $):\n  {$ctx['pricing_summary']}\n\n";
    if (!empty($ctx['competitive_landscapes'])) {
        $brief .= "COMPETITIVE LANDSCAPE:\n";
        foreach ($ctx['competitive_landscapes'] as $cl) $brief .= "  - {$cl}\n";
        $brief .= "\n";
    }
    if (!empty($ctx['what_most_lack'])) {
        $brief .= "WHAT MOST {$upper} BUSINESSES LACK ONLINE:\n";
        foreach ($ctx['what_most_lack'] as $wml) $brief .= "  - {$wml}\n";
        $brief .= "\n";
    }
    if (!empty($ctx['top_ranked_edges'])) {
        $brief .= "WHAT WINNING {$upper} WEBSITES DO DIFFERENTLY:\n";
        foreach ($ctx['top_ranked_edges'] as $tre) $brief .= "  - {$tre}\n";
        $brief .= "\n";
    }
    if (!empty($ctx['local_insights'])) {
        $brief .= "HYPER-LOCAL INSIGHTS BY CITY:\n";
        foreach ($ctx['local_insights'] as $li) $brief .= "  - {$li}\n";
        $brief .= "\n";
    }
    $brief .= "=== END OF MARKET INTELLIGENCE ===\n";
    return $brief;
}

// 9. System Prompts
function getSingleThemeSystemPrompt($angle_instruction, $niche_brief) {
    return <<<EOT
You are a world-class programmatic SEO copywriter for GetOnline Studio, a Nigerian digital agency.
Generate a highly persuasive narrative for a specific digital service targeting a specific niche IN NIGERIA.

{$niche_brief}

CRITICAL ANGLE FOR THIS GENERATION:
{$angle_instruction}

STRICT RULES:
1. Short punchy sentences. Grade 6 reading level. NO TECH JARGON.
2. Speak directly to the business owner using "you" and "your".
3. YOU MUST USE "{city_name}" at least 3 times — this is a dynamic placeholder, do not replace it.
4. YOU MUST USE "{niche_name}" instead of hardcoding the niche name — this is a dynamic placeholder.
5. Use Spintax {word1|word2|word3} for verbs and adjectives to create variation.
6. Draw on the REAL NIGERIAN MARKET INTELLIGENCE above. Make copy feel authentic and specific.
7. PRICING: Always use ₦ (Naira). Never use $ or USD.
8. OUTPUT MUST BE VALID JSON matching the schema exactly.

JSON SCHEMA:
{
  "hero_subheadline": "1-2 sentences. Speak to the specific pain point using {city_name}.",
  "reality_p1": "Rich paragraph. Ground reader in actual {city_name} search behaviour. Use real market insight.",
  "reality_p2": "Rich paragraph. What happens without a platform? Be specific to this niche.",
  "reality_p3": "Rich paragraph. What does a platform unlock for this niche specifically?",
  "reality_p4": "Rich paragraph. Why act now in {city_name}? Use competitive landscape data.",
  "pos1_title": "Benefit Card 1 Title",
  "pos1_desc": "2-3 sentences explaining the benefit.",
  "pos2_title": "Benefit Card 2 Title",
  "pos2_desc": "2-3 sentences.",
  "pos3_title": "Benefit Card 3 Title",
  "pos3_desc": "2-3 sentences.",
  "pos4_title": "Benefit Card 4 Title",
  "pos4_desc": "2-3 sentences."
}
EOT;
}

function getCoreSystemPrompt($niche_brief) {
    return <<<EOT
You are a world-class programmatic SEO copywriter for GetOnline Studio, a Nigerian digital agency.
Generate static infrastructure (Features, FAQs, CTA) for a specific digital service targeting a specific niche in Nigeria.

{$niche_brief}

STRICT RULES:
1. NO TECH JARGON. Speak plain English to Nigerian business owners.
2. Draw on the REAL NIGERIAN MARKET INTELLIGENCE above.
3. FAQs must answer what real Nigerian business owners actually ask.
4. PRICING: NEVER use "dollars", "USD", or "$". ONLY use ₦.
5. Make every feature specific to this niche — never generic.
6. NEVER hardcode any city name. Use {city_name} placeholder instead.
7. OUTPUT MUST BE VALID JSON matching the schema exactly.

JSON SCHEMA:
{
  "feat_headline": "Niche-specific headline for What We Build section.",
  "feat_subline": "1 sentence demonstrating our deep knowledge of this niche.",
  "f1_title": "Feature 1 Title", "f1_desc": "Niche-specific detailed explanation.",
  "f2_title": "Feature 2 Title", "f2_desc": "...",
  "f3_title": "Feature 3 Title", "f3_desc": "...",
  "f4_title": "Feature 4 Title", "f4_desc": "...",
  "f5_title": "Feature 5 Title", "f5_desc": "...",
  "f6_title": "Feature 6 Title", "f6_desc": "...",
  "faq1_q": "A Nigerian business owner question about cost",
  "faq1_a": "Detailed answer using ₦ pricing from the market intelligence above.",
  "faq2_q": "Question about how long it takes",
  "faq2_a": "Honest, detailed answer.",
  "faq3_q": "Question about SEO and getting found on Google in Nigeria",
  "faq3_a": "Detailed answer.",
  "faq4_q": "Question about redesigning an existing website",
  "faq4_a": "Detailed answer.",
  "faq5_q": "A niche-specific question only this type of Nigerian business would ask",
  "faq5_a": "Highly specific, detailed answer that shows deep niche knowledge.",
  "cta_aspiration": "The niche owner's ultimate business goal — one short, powerful sentence."
}
EOT;
}

// 10. City Placeholder Sanitizer
function sanitize_city_placeholders($data) {
    $cities = [
        'Port Harcourt', 'Benin City', 'Ibadan', 'Ilorin', 'Warri',
        'Maiduguri', 'Onitsha', 'Osogbo', 'Enugu', 'Kaduna',
        'Lagos', 'Abuja', 'Kano', 'Asaba', 'Owerri',
        'Calabar', 'Sokoto', 'Umuahia', 'Uyo', 'Zaria',
        'Minna', 'Akure', 'Jos', 'Aba',
        'Victoria Island', 'Lekki', 'Ikeja', 'Ikoyi', 'Yaba',
        'Surulere', 'Ajah', 'Festac', 'Gbagada', 'Maryland',
        'Wuse', 'Maitama', 'Garki', 'Asokoro', 'Gwarinpa',
        'GRA', 'Trans Amadi', 'Bodija', 'Rayfield', 'Barnawa',
        'Sabon Gari', 'Wuse 2', 'Anglo Jos',
    ];
    usort($cities, function($a, $b) { return strlen($b) - strlen($a); });
    array_walk_recursive($data, function(&$value) use ($cities) {
        if (!is_string($value)) return;
        foreach ($cities as $city) {
            $value = preg_replace('/\bin\s+' . preg_quote($city, '/') . '\b/i', 'in {city_name}', $value);
            $value = preg_replace('/\blike\s+' . preg_quote($city, '/') . '\b/i', 'like {city_name}', $value);
            $value = preg_replace('/\bacross\s+' . preg_quote($city, '/') . '\b/i', 'across {city_name}', $value);
            $value = preg_replace('/\bfrom\s+' . preg_quote($city, '/') . '\b/i', 'from {city_name}', $value);
            $value = preg_replace('/\b' . preg_quote($city, '/') . '\b/', '{city_name}', $value);
        }
        $value = preg_replace('/\{city_name\}(,?\s+)\{city_name\}/', '{city_name}', $value);
    });
    return $data;
}

// 11. Gemini API Caller
function call_gemini_with_retries($system_instruction, $user_prompt) {
    $api_url = "https://generativelanguage.googleapis.com/v1beta/models/gemini-2.5-flash:generateContent?key=" . GEMINI_API_KEY;
    $payload = json_encode([
        'system_instruction' => ['parts' => [['text' => $system_instruction]]],
        'contents'           => [['role' => 'user', 'parts' => [['text' => $user_prompt]]]],
        'generationConfig'   => ['temperature' => 0.7, 'responseMimeType' => 'application/json']
    ]);
    for ($attempt = 1; $attempt <= PSEO_MAX_RETRIES; $attempt++) {
        $response = wp_remote_post($api_url, [
            'body'    => $payload,
            'headers' => ['Content-Type' => 'application/json'],
            'timeout' => 60
        ]);
        if (is_wp_error($response)) { sleep(3); continue; }
        $body   = wp_remote_retrieve_body($response);
        $data   = json_decode($body, true);
        if (isset($data['candidates'][0]['content']['parts'][0]['text'])) {
            $raw    = $data['candidates'][0]['content']['parts'][0]['text'];
            $clean  = trim(str_replace(['```json', '```'], '', $raw));
            $parsed = json_decode($clean, true);
            if ($parsed) return $parsed;
        }
        sleep(3);
    }
    return false;
}

// 12. Build Queue — NEW items + STALE items
$refresh_cutoff = date('Y-m-d H:i:s', strtotime('-' . PSEO_REFRESH_DAYS . ' days'));
$queue          = [];
$new_count      = 0;
$stale_count    = 0;

foreach ($master_niches as $niche_slug => $niche_name) {
    $post = get_page_by_path($niche_slug, OBJECT, 'pseo_niche');
    if (!$post) {
        $post_id = wp_insert_post([
            'post_title'  => $niche_name,
            'post_name'   => $niche_slug,
            'post_type'   => 'pseo_niche',
            'post_status' => 'draft'
        ]);
    } else {
        $post_id = $post->ID;
    }

    foreach ($service_formats as $format_slug => $format_name) {
        $saved_themes  = get_post_meta($post_id, $format_slug . '_ai_themes', true);
        $feat_headline = get_post_meta($post_id, $format_slug . '_feat_headline', true);
        $built_at      = get_post_meta($post_id, $format_slug . '_built_at', true);

        $is_missing = empty($saved_themes) || count((array)$saved_themes) < 5 || empty($feat_headline);
        $is_stale   = !$is_missing && !empty($built_at) && $built_at < $refresh_cutoff;

        if ($is_missing) {
            $queue[]   = ['niche_slug' => $niche_slug, 'niche_name' => $niche_name, 'format_slug' => $format_slug, 'format_name' => $format_name, 'post_id' => $post_id, 'reason' => 'new'];
            $new_count++;
        } elseif ($is_stale) {
            $queue[]   = ['niche_slug' => $niche_slug, 'niche_name' => $niche_name, 'format_slug' => $format_slug, 'format_name' => $format_name, 'post_id' => $post_id, 'reason' => 'stale'];
            $stale_count++;
        }
    }
}

// New items always before stale
usort($queue, fn($a, $b) => ($a['reason'] === 'new' ? 0 : 1) - ($b['reason'] === 'new' ? 0 : 1));

if (empty($queue)) {
    // Log to a global option so the dashboard can read it
    update_option('pseo_builder_last_run', [
        'time'    => current_time('mysql'),
        'status'  => 'complete',
        'message' => 'All niche matrices are fully built and fresh.',
        'log'     => $execution_log,
    ]);
    die("✅ Queue is empty. All Niche Matrices are fully built and fresh!");
}

$batch = array_slice($queue, 0, PSEO_BATCH_SIZE);
$execution_log[] = "📊 Queue: {$new_count} new items, {$stale_count} stale (>" . PSEO_REFRESH_DAYS . " days old). Processing batch of " . count($batch) . "...";

// 13. Theme angles
$theme_angles = [
    "THEME 1 (Growth & Efficiency): Focus entirely on ROI, getting more leads, saving time with automation, and standard business growth. Make it sound like a smart, efficient business decision.",
    "THEME 2 (Premium Authority): Focus entirely on looking like the most expensive, elite option in the city. Talk about commanding higher rates, building undeniable trust, and repelling bargain hunters.",
    "THEME 3 (Aggressive Dominance): Focus entirely on stealing competitor traffic, dominating local search results, and aggressive client acquisition. Use a sharp, highly competitive tone.",
    "THEME 4 (Trust & Compliance): Focus entirely on proving legal, corporate, and structural credibility. The angle is 'clients won't pay unless they feel totally secure.'",
    "THEME 5 (Community & Convenience): Focus entirely on making it ridiculously easy for local consumers to buy. The angle is 'convenience wins the neighborhood.'"
];

// 14. Process the Batch
$successful_builds = [];

foreach ($batch as $item) {
    $label           = $item['reason'] === 'stale' ? '♻️  REFRESH' : '🆕 NEW';
    $execution_log[] = "⏳ {$label}: {$item['niche_name']} × {$item['format_name']}...";

    $niche_brief = build_niche_intelligence_brief(
        $item['niche_name'],
        $item['niche_slug'],
        $city_niche_data,
        $city_map
    );

    $generated_themes = [];
    $failed_theme     = false;

    // CALLS 1-5: Generate 5 themed narrative variants
    foreach ($theme_angles as $index => $angle) {
        $theme_num       = $index + 1;
        $execution_log[] = "   -> Generating Theme {$theme_num}...";

        $sys_prompt  = getSingleThemeSystemPrompt($angle, $niche_brief);
        $user_prompt = "Write a persuasive narrative now. Service: {$item['format_name']}. Niche: {$item['niche_name']}.

MANDATORY PLACEHOLDER RULES:
1. Use the literal text {city_name} every time you refer to a city or location. Use it AT LEAST 3 times.
2. Use the literal text {niche_name} every time you refer to the niche type.
3. NEVER hardcode any city name (Lagos, Abuja, Kano, Port Harcourt, or ANY other city).
4. The Nigerian market intelligence is for CONTEXT AND INSPIRATION only. Replace city names with {city_name}.";

        $theme_data = call_gemini_with_retries($sys_prompt, $user_prompt);

        if ($theme_data && isset($theme_data['hero_subheadline'])) {
            $generated_themes[] = $theme_data;
        } else {
            $failed_theme    = true;
            $execution_log[] = "   ❌ Failed on Theme {$theme_num}. Aborting this matrix.";
            break;
        }
        sleep(3);
    }

    if ($failed_theme) continue;

    // CALL 6: Core Infrastructure
    $execution_log[] = "   -> Generating Core Infrastructure...";
    $core_sys    = getCoreSystemPrompt($niche_brief);
    $core_prompt = "Generate niche-specific features and FAQs. Service: {$item['format_name']}. Niche: {$item['niche_name']}.

MANDATORY RULES:
1. NEVER hardcode any city name. Use {city_name} placeholder instead.
2. Use ₦ for ALL pricing. Never use $ or USD.
3. Write features and FAQs specific to {$item['niche_name']} businesses in Nigeria.
4. FAQ answers must be detailed and use real ₦ pricing from the market intelligence.";
    $core_data = call_gemini_with_retries($core_sys, $core_prompt);

    if (!$core_data || !isset($core_data['feat_headline'])) {
        $execution_log[] = "   ❌ Failed on Core Infrastructure. Aborting.";
        continue;
    }

    // Sanitize all output
    $generated_themes = sanitize_city_placeholders($generated_themes);
    $core_data        = sanitize_city_placeholders($core_data);

    // ─── SAVE: BIG BLOB (niche-landing.php reads this — DO NOT CHANGE) ─────────
    update_post_meta($item['post_id'], $item['format_slug'] . '_ai_themes', $generated_themes);

    // ─── SAVE: CORE KEYS (features + FAQs) ───────────────────────────────────
    $core_keys = [
        'feat_headline', 'feat_subline',
        'f1_title', 'f1_desc', 'f2_title', 'f2_desc', 'f3_title', 'f3_desc',
        'f4_title', 'f4_desc', 'f5_title', 'f5_desc', 'f6_title', 'f6_desc',
        'faq1_q', 'faq1_a', 'faq2_q', 'faq2_a', 'faq3_q', 'faq3_a',
        'faq4_q', 'faq4_a', 'faq5_q', 'faq5_a', 'cta_aspiration'
    ];
    foreach ($core_keys as $key) {
        if (isset($core_data[$key])) {
            update_post_meta($item['post_id'], $item['format_slug'] . '_' . $key, wp_kses_post($core_data[$key]));
        }
    }

    // ─── NEW v3: SAVE INDIVIDUAL REALITY + POS KEYS ──────────────────────────
    // Pick the best theme (Theme 1 = Growth & Efficiency, most universally useful)
    // and save its reality_p1–p4 and pos1–pos4 as individual post meta keys.
    // This is what niche-hub.php (the national page) reads directly.
    $best_theme = $generated_themes[0] ?? [];
    $individual_keys = [
        'reality_p1', 'reality_p2', 'reality_p3', 'reality_p4',
        'pos1_title', 'pos1_desc',
        'pos2_title', 'pos2_desc',
        'pos3_title', 'pos3_desc',
        'pos4_title', 'pos4_desc',
        'hero_subheadline',
    ];
    $saved_individual = 0;
    foreach ($individual_keys as $key) {
        if (!empty($best_theme[$key])) {
            update_post_meta($item['post_id'], $key, wp_kses_post($best_theme[$key]));
            // Also save format-specific version so niche-landing can use it too
            update_post_meta($item['post_id'], $item['format_slug'] . '_' . $key, wp_kses_post($best_theme[$key]));
            $saved_individual++;
        }
    }
    $execution_log[] = "   ✅ Saved {$saved_individual} individual keys (reality + pos) for niche-hub.php";
    // ─────────────────────────────────────────────────────────────────────────

    // Stamp build time
    update_post_meta($item['post_id'], $item['format_slug'] . '_built_at', current_time('mysql'));

    // Publish the niche
    wp_update_post(['ID' => $item['post_id'], 'post_status' => 'publish']);

    // ─── SAVE BUILD LOG for dashboard ────────────────────────────────────────
    $existing_log = get_post_meta($item['post_id'], '_pseo_build_log', true) ?: [];
    array_unshift($existing_log, [
        'time'   => current_time('mysql'),
        'format' => $item['format_name'],
        'reason' => $item['reason'],
        'themes' => count($generated_themes),
        'keys'   => $saved_individual,
    ]);
    // Keep only last 10 entries per niche
    update_post_meta($item['post_id'], '_pseo_build_log', array_slice($existing_log, 0, 10));
    // ─────────────────────────────────────────────────────────────────────────

    $success_msg         = "{$item['niche_name']} × {$item['format_name']} ({$item['reason']})";
    $successful_builds[] = $success_msg;
    $execution_log[]     = "✅ Success: " . $success_msg;
}

// 15. Save global run status for dashboard
update_option('pseo_builder_last_run', [
    'time'        => current_time('mysql'),
    'status'      => empty($successful_builds) ? 'failed' : 'success',
    'built'       => $successful_builds,
    'queue_new'   => $new_count,
    'queue_stale' => $stale_count,
    'log'         => $execution_log,
]);

// 16. Admin Email
if (!empty($successful_builds)) {
    $admin_email = get_option('admin_email');
    $subject     = "🤖 Niche Auto-Builder v3.0: " . count($successful_builds) . " Matrices Built/Refreshed";
    $message     = "Dual-Save 6-step sequence completed:\n\n";
    foreach ($successful_builds as $combo) { $message .= "- $combo\n"; }
    $refresh_days = PSEO_REFRESH_DAYS;
    $message    .= "\nQueue remaining — New: {$new_count} | Stale (>{$refresh_days}d): {$stale_count}\n\n";
    $message    .= "Execution Log:\n" . implode("\n", $execution_log);
    wp_mail($admin_email, $subject, $message, ['Content-Type: text/plain; charset=UTF-8']);
}

echo "Cron Job Completed.\n";
print_r($execution_log);
