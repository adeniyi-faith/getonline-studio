<?php
/**
 * GETONLINE STUDIO — NICHE CONTENT ENGINE
 * Generates GSO-rich prose (reality_p5, reality_p6) and extended FAQs
 * (faq6–faq11) for any niche using Gemini 2.5 Flash.
 *
 * HOW IT WORKS:
 *  1. Admin picks a niche from the dropdown
 *  2. Clicks "Generate Content"
 *  3. This file reads existing content + city-niche.json so Gemini
 *     knows exactly what NOT to repeat
 *  4. Gemini returns 2 prose paragraphs + 6 FAQs in strict JSON
 *  5. Content is saved to WP post meta on the niche post
 *  6. niche-landing.php already reads those keys — nothing else to change
 *
 * PLACEMENT: /wp/niche-content-engine.php
 * REQUIRES:  Gemini API key hardcoded below (line 31)
 */

// ── Boot WordPress silently ───────────────────────────────────────────────────
define('WP_USE_THEMES', false);
require_once(__DIR__ . '/wp-load.php');

// ── Security: Admin only ──────────────────────────────────────────────────────
if (!is_user_logged_in() || !current_user_can('manage_options')) {
    wp_redirect('/u-login.php?redirect_to=' . urlencode($_SERVER['REQUEST_URI']));
    exit;
}

// ── Gemini API Key ────────────────────────────────────────────────────────────
$gemini_api_key = 'AIzaSyC1DPDJzt5psEkIDgqK3XztuVLgXnDwwZM';
$gemini_model   = 'gemini-2.5-flash';

// ── Load city-niche.json for market data context ──────────────────────────────
$city_niche_path = dirname(__DIR__) . '/city-niche.json';
$city_niche_data = file_exists($city_niche_path)
    ? json_decode(file_get_contents($city_niche_path), true)
    : [];

// ── State vars ────────────────────────────────────────────────────────────────
$toast_msg  = '';
$toast_type = 'success'; // 'success' | 'error'
$preview    = null;      // Holds last generated content for preview panel

// =============================================================================
// AJAX HANDLER — called by JS fetch(), returns JSON
// =============================================================================
if (!empty($_GET['action']) && $_GET['action'] === 'generate') {

    header('Content-Type: application/json');

    // Validate API key
    if (empty($gemini_api_key)) {
        echo json_encode(['ok' => false, 'error' => 'GEMINI_API_KEY is not configured.']);
        exit;
    }

    $niche_id   = intval($_POST['niche_id']   ?? 0);
    $city_slug  = sanitize_title($_POST['city_slug'] ?? '');
    $niche_post = $niche_id ? get_post($niche_id) : null;

    if (!$niche_post) {
        echo json_encode(['ok' => false, 'error' => 'Niche not found.']);
        exit;
    }

    $niche_slug = $niche_post->post_name;
    $niche_name = $niche_post->post_title;

    // ── Read niche meta (plural, existing content) ────────────────────────────
    $niche_raw_meta = get_post_meta($niche_post->ID, '_pseo_niche_data', true) ?: [];
    $niche_plural   = !empty($niche_raw_meta['plural']) ? $niche_raw_meta['plural'] : $niche_name . 's';

    // ── Read existing prose so Gemini won't repeat it ─────────────────────────
    $existing_prose = [];
    foreach (['reality_p1','reality_p2','reality_p3','reality_p4'] as $key) {
        $val = get_post_meta($niche_post->ID, $key, true);
        if (!empty($val)) $existing_prose[] = $val;
    }
    // Also pull from global boilerplates as fallback context
    $boilerplates_file = dirname(__DIR__) . '/global-boilerplates.json';
    if (empty($existing_prose) && file_exists($boilerplates_file)) {
        $bp = json_decode(file_get_contents($boilerplates_file), true);
        foreach (['reality_p1','reality_p2','reality_p3','reality_p4'] as $key) {
            if (!empty($bp['common'][$key])) $existing_prose[] = $bp['common'][$key];
        }
    }

    // ── Read existing FAQs so Gemini won't duplicate them ────────────────────
    $existing_faqs = [];
    for ($i = 1; $i <= 5; $i++) {
        $q = get_post_meta($niche_post->ID, "faq{$i}_q", true);
        if (!empty($q)) $existing_faqs[] = $q;
    }
    // Also niche meta FAQs
    if (!empty($niche_raw_meta['niche_faq_1'])) $existing_faqs[] = $niche_raw_meta['niche_faq_1'];
    if (!empty($niche_raw_meta['niche_faq_2'])) $existing_faqs[] = $niche_raw_meta['niche_faq_2'];

    // ── Pull city market data if a city is selected ───────────────────────────
    $market_context = '';
    $city_name      = '';
    if (!empty($city_slug)) {
        $city_post = get_page_by_path($city_slug, OBJECT, 'pseo_location');
        if ($city_post) $city_name = $city_post->post_title;

        $combo_key   = "{$city_slug}_{$niche_slug}";
        $market_data = $city_niche_data[$combo_key] ?? [];

        if (!empty($market_data)) {
            $parts = [];
            if (!empty($market_data['estimated_businesses']))
                $parts[] = "There are approximately {$market_data['estimated_businesses']} {$niche_plural} in {$city_name}.";
            if (!empty($market_data['without_website']))
                $parts[] = "About {$market_data['without_website']} of them have no website.";
            if (!empty($market_data['competitive_landscape']))
                $parts[] = $market_data['competitive_landscape'];
            if (!empty($market_data['local_insight']))
                $parts[] = $market_data['local_insight'];
            if (!empty($market_data['what_most_lack']))
                $parts[] = $market_data['what_most_lack'];
            $market_context = implode(' ', $parts);
        }

        // Also try /data/{city_slug}.json for deeper local data
        $city_data_file = dirname(__DIR__) . "/data/{$city_slug}.json";
        if (file_exists($city_data_file)) {
            $city_extra = json_decode(file_get_contents($city_data_file), true);
            if (!empty($city_extra['description'])) $market_context .= ' ' . $city_extra['description'];
            if (!empty($city_extra['economy']))      $market_context .= ' ' . $city_extra['economy'];
        }
    }

    // ── Build the Gemini prompt ───────────────────────────────────────────────
    $city_line    = !empty($city_name) ? "City: {$city_name}, Nigeria." : "City: not specified — write for a general Nigerian city context.";
    $prose_block  = !empty($existing_prose) ? implode("\n\n", $existing_prose) : 'None yet.';
    $faqs_block   = !empty($existing_faqs)  ? implode("\n", array_map(fn($q, $i) => ($i+1).". $q", $existing_faqs, array_keys($existing_faqs))) : 'None yet.';
    $market_block = !empty($market_context) ? $market_context : 'No market data available for this combination.';

    $prompt = <<<PROMPT
You are a subject-matter expert on Nigerian business and web design, writing for GetOnline Studio.

CONTEXT:
- Niche: {$niche_name} (plural: {$niche_plural})
- City: {$city_name}, Nigeria
- Page purpose: Help {$niche_plural} in {$city_name} understand why they need a website and what it does for their business

MARKET DATA (use these specific numbers):
{$market_block}

━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━
WHAT THIS CONTENT IS FOR — READ THIS FIRST
━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━

Google AI Overviews, ChatGPT Search, and Perplexity scan pages looking for content that directly and completely answers real questions people type into search. They pull specific sentences from pages to use as citations in their answers. 

This means: every answer you write must be a complete, standalone, factually-specific answer to that exact question. If someone asked only that question, your answer must fully satisfy them with zero fluff. AI systems do not cite vague answers. They cite specific facts, specific numbers, specific named things.

The TWO PROSE PARAGRAPHS are "reality check" content that slots into the page's main narrative. They must state a specific fact about how {$niche_plural} in {$city_name} get or lose customers — something citeable, not something decorative.

The SIX FAQs are a GSO Answer Guide. These are NOT basic service questions (those are already on the page). These are the deeper, more specific questions that real {$niche_name} owners in {$city_name} type into Google and ChatGPT — questions about their specific business situation, their local market, their customers, their industry realities in Nigeria.

━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━
EXISTING PAGE CONTENT — DO NOT REPEAT OR REPHRASE ANY OF THIS
━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━
{$prose_block}

Existing FAQs already on page (pick completely different angles):
{$faqs_block}

━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━
PROSE RULES
━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━

Each paragraph must state ONE specific, citeable fact about the {$niche_name} market or customer behavior in {$city_name}. Think: what would a journalist write about this market? What would a Google AI Overview pull as a citation when someone searches "{$niche_name} website {$city_name}"?

- Max 80 words per paragraph. Short sentences.
- Must include a named specific: a real {$city_name} area, a Nigerian platform (Paystack, WhatsApp, Google Maps), a real customer behavior, or a market number from the data above.
- Grade 8 reading level. No jargon. No filler.
- Do NOT write about websites being "important" or "essential" in general terms. That is already on the page. Write about THIS niche, THIS city, THIS specific customer.

BANNED — these words make content unciteable and vague. Do not use:
landscape, ecosystem, seamlessly, robust, transformative, innovative, leverage, synergy, holistic, vibrant, thriving, bustling, game-changer, ever-evolving, dynamic, harness, empower, cutting-edge, in today's world, in the digital age

━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━
FAQ RULES
━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━

These are GSO Answer Guide questions — the specific questions a {$niche_name} owner in {$city_name} types into Google or ChatGPT when they are researching their situation. NOT basic "what do you offer" questions.

Good GSO questions look like:
- "How do {$niche_plural} in {$city_name} get found by customers online?"
- "What does a {$niche_name} website need to rank on Google in Nigeria?"
- "How do {$niche_name} customers in Nigeria search before they buy?"
- "What Nigerian payment methods should a {$niche_name} website support?"
- "Do {$niche_plural} in {$city_name} need Google My Business?"
- "What happens to a {$niche_name} that has no website in {$city_name}?"
- "How do I get my {$niche_name} to appear in Google AI Overviews?"
- "What makes a {$niche_name} website trusted by Nigerian customers?"

Each ANSWER must:
- Directly answer the exact question asked — first sentence answers it, rest of the sentences add supporting facts
- Include at least one specific fact: a number, a named platform, a named city area, a price range in naira, a named Nigerian behavior or platform
- Be 2–4 sentences, max 80 words
- Stand alone — if AI pulls only this answer, it must make complete sense with zero surrounding context
- Grade 8 reading level

━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━
RESPOND ONLY WITH THIS JSON — No markdown, no backticks, no extra text:
━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━
{
  "reality_p5": "paragraph one",
  "reality_p6": "paragraph two",
  "faq6_q":  "question",
  "faq6_a":  "answer",
  "faq7_q":  "question",
  "faq7_a":  "answer",
  "faq8_q":  "question",
  "faq8_a":  "answer",
  "faq9_q":  "question",
  "faq9_a":  "answer",
  "faq10_q": "question",
  "faq10_a": "answer",
  "faq11_q": "question",
  "faq11_a": "answer"
}
PROMPT;

    // ── Call Gemini API ───────────────────────────────────────────────────────
    $api_url  = "https://generativelanguage.googleapis.com/v1beta/models/{$gemini_model}:generateContent?key={$gemini_api_key}";
    $payload  = json_encode([
        'system_instruction' => ['parts' => [['text' => 'You are a direct, no-nonsense Nigerian business copywriter. You write short, clear sentences. You never use filler words or generic marketing language. Every sentence contains a specific fact or useful point. You always follow formatting instructions exactly.']]],
        'contents' => [['parts' => [['text' => $prompt]]]],
        'generationConfig' => [
            'temperature'     => 0.55,
            'maxOutputTokens' => 4096,
            'responseMimeType'=> 'application/json',
        ]
    ]);

    $ch = curl_init($api_url);
    curl_setopt_array($ch, [
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_POST           => true,
        CURLOPT_POSTFIELDS     => $payload,
        CURLOPT_HTTPHEADER     => ['Content-Type: application/json'],
        CURLOPT_TIMEOUT        => 60,
    ]);
    $raw      = curl_exec($ch);
    $curl_err = curl_error($ch);
    curl_close($ch);

    if ($curl_err) {
        echo json_encode(['ok' => false, 'error' => 'API connection failed: ' . $curl_err]);
        exit;
    }

    $api_response = json_decode($raw, true);
    $generated_text = $api_response['candidates'][0]['content']['parts'][0]['text'] ?? '';

    if (empty($generated_text)) {
        $err_msg = $api_response['error']['message'] ?? 'Gemini returned an empty response.';
        echo json_encode(['ok' => false, 'error' => $err_msg]);
        exit;
    }

    // ── Parse the JSON Gemini returned ───────────────────────────────────────
    $generated_text = trim($generated_text);
    // Strip markdown fences if Gemini added them despite instructions
    $generated_text = preg_replace('/^```(?:json)?\s*/i', '', $generated_text);
    $generated_text = preg_replace('/\s*```\s*$/i', '', $generated_text);

    $content = json_decode($generated_text, true);

    if (json_last_error() !== JSON_ERROR_NONE) {
        echo json_encode(['ok' => false, 'error' => 'Could not parse Gemini JSON. Raw: ' . substr($generated_text, 0, 300)]);
        exit;
    }

    // ── Validate required keys ────────────────────────────────────────────────
    $required = ['reality_p5','reality_p6','faq6_q','faq6_a','faq7_q','faq7_a','faq8_q','faq8_a','faq9_q','faq9_a','faq10_q','faq10_a','faq11_q','faq11_a'];
    $missing  = array_diff($required, array_keys($content));
    if (!empty($missing)) {
        echo json_encode(['ok' => false, 'error' => 'Missing keys in response: ' . implode(', ', $missing)]);
        exit;
    }

    // ── Quality check — flag banned filler words so the UI can warn the editor ─
    $banned_words = ['landscape','ecosystem','realm','seamlessly','robust','transformative',
        'leverage','empower','cutting-edge','innovative','solutions','synergy','holistic',
        'digital age','vibrant','thriving','bustling','game-changer','harness','dynamic',
        'ever-evolving','navigating','in the heart of'];
    $quality_warnings = [];
    $all_text = implode(' ', array_values($content));
    foreach ($banned_words as $bw) {
        if (stripos($all_text, $bw) !== false) {
            $quality_warnings[] = "Contains banned word: \"{$bw}\"";
        }
    }

    echo json_encode(['ok' => true, 'content' => $content, 'niche_id' => $niche_id, 'niche_name' => $niche_name, 'city_name' => $city_name, 'quality_warnings' => $quality_warnings]);
    exit;
}

// =============================================================================
// SAVE HANDLER — saves approved content to WP post meta
// =============================================================================
if ($_SERVER['REQUEST_METHOD'] === 'POST' && !empty($_POST['save_content'])) {

    $niche_id = intval($_POST['niche_id'] ?? 0);
    $niche_post = $niche_id ? get_post($niche_id) : null;

    if ($niche_post) {
        $keys_to_save = ['reality_p5','reality_p6','faq6_q','faq6_a','faq7_q','faq7_a','faq8_q','faq8_a','faq9_q','faq9_a','faq10_q','faq10_a','faq11_q','faq11_a'];
        $saved = 0;
        foreach ($keys_to_save as $key) {
            if (isset($_POST[$key])) {
                update_post_meta($niche_post->ID, $key, sanitize_textarea_field($_POST[$key]));
                $saved++;
            }
        }
        // Also save which city this content was written for (helpful for context tracking)
        if (!empty($_POST['city_slug'])) {
            update_post_meta($niche_post->ID, '_gso_content_city', sanitize_title($_POST['city_slug']));
        }
        update_post_meta($niche_post->ID, '_gso_content_generated_at', current_time('mysql'));

        $toast_msg  = "✓ GSO content saved to \"{$niche_post->post_title}\" — {$saved} fields updated. It will appear on all landing pages for this niche immediately.";
        $toast_type = 'success';
    } else {
        $toast_msg  = 'Save failed — niche not found.';
        $toast_type = 'error';
    }
}

// =============================================================================
// CLEAR HANDLER — wipes generated content from a niche
// =============================================================================
if ($_SERVER['REQUEST_METHOD'] === 'POST' && !empty($_POST['clear_content'])) {
    $niche_id   = intval($_POST['niche_id'] ?? 0);
    $niche_post = $niche_id ? get_post($niche_id) : null;

    if ($niche_post) {
        $keys_to_clear = ['reality_p5','reality_p6','faq6_q','faq6_a','faq7_q','faq7_a','faq8_q','faq8_a','faq9_q','faq9_a','faq10_q','faq10_a','faq11_q','faq11_a','_gso_content_city','_gso_content_generated_at'];
        foreach ($keys_to_clear as $key) delete_post_meta($niche_post->ID, $key);
        $toast_msg  = "GSO content cleared from \"{$niche_post->post_title}\".";
        $toast_type = 'error';
    }
}

// ── Fetch all niches for the UI dropdown ──────────────────────────────────────
$all_niches = get_posts([
    'post_type'   => 'pseo_niche',
    'numberposts' => -1,
    'post_status' => ['publish', 'draft'],
    'orderby'     => 'title',
    'order'       => 'ASC',
]);

// ── Fetch all cities for optional city context dropdown ───────────────────────
$all_cities = get_posts([
    'post_type'   => 'pseo_location',
    'numberposts' => -1,
    'post_status' => 'publish',
    'orderby'     => 'title',
    'order'       => 'ASC',
]);

// ── Build niche status map (which niches already have GSO content) ────────────
$niche_status = [];
foreach ($all_niches as $n) {
    $has_prose = !empty(get_post_meta($n->ID, 'reality_p5', true));
    $has_faqs  = !empty(get_post_meta($n->ID, 'faq6_q', true));
    $gen_at    = get_post_meta($n->ID, '_gso_content_generated_at', true);
    $gen_city  = get_post_meta($n->ID, '_gso_content_city', true);
    $niche_status[$n->ID] = [
        'has_prose' => $has_prose,
        'has_faqs'  => $has_faqs,
        'gen_at'    => $gen_at,
        'gen_city'  => $gen_city,
        'complete'  => $has_prose && $has_faqs,
    ];
}

$niches_done    = count(array_filter($niche_status, fn($s) => $s['complete']));
$niches_partial = count(array_filter($niche_status, fn($s) => ($s['has_prose'] || $s['has_faqs']) && !$s['complete']));
$niches_empty   = count($all_niches) - $niches_done - $niches_partial;

?>
<!DOCTYPE html>
<html lang="en" class="dark">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>GSO Content Engine | pSEO Command Center</title>

    <link rel="icon" href="https://getonlinestudio.com/insights/wp-content/uploads/2026/02/GetOnline_Studio_Logo.jpg" sizes="32x32" />
    <link href="https://fonts.googleapis.com/css2?family=Manrope:wght@300;400;500;600;700&family=Syne:wght@400;600;700&family=Fira+Code:wght@400;600&display=swap" rel="stylesheet">
    <script src="https://unpkg.com/lucide@latest"></script>
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            darkMode: 'class',
            theme: {
                extend: {
                    colors: {
                        'matte-black':  '#0a0a0a',
                        'panel-dark':   '#121212',
                        'card-dark':    '#1a1a1a',
                        'lavender':     '#e9d5ff',
                        'sharp-purple': '#7e22ce',
                        'success-green':'#4ade80',
                    },
                    fontFamily: {
                        'syne':    ['Syne', 'sans-serif'],
                        'manrope': ['Manrope', 'sans-serif'],
                        'mono':    ['Fira Code', 'monospace'],
                    }
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
        @keyframes pulse-ring { 0%,100% { opacity:.6; } 50% { opacity:1; } }
        .toast { animation: slideInUp 0.3s ease forwards; }
        textarea { font-size: 13px !important; line-height: 1.6; }
        select, input { font-size: 14px !important; }

        /* Skeleton loader */
        .skeleton { background: linear-gradient(90deg, #1a1a1a 25%, #252525 50%, #1a1a1a 75%); background-size: 200% 100%; animation: shimmer 1.5s infinite; border-radius: 6px; }
        @keyframes shimmer { 0% { background-position: 200% 0; } 100% { background-position: -200% 0; } }

        /* Status dot */
        .dot-done    { background: #4ade80; }
        .dot-partial { background: #facc15; }
        .dot-empty   { background: #374151; }

        /* Spinning loader icon */
        .spin { animation: spin 1s linear infinite; }
        @keyframes spin { to { transform: rotate(360deg); } }
    </style>
</head>
<body class="antialiased selection:bg-sharp-purple selection:text-white pb-32">

    <!-- ── TOAST ─────────────────────────────────────────────────────────── -->
    <?php if ($toast_msg): ?>
    <div id="toast" class="toast fixed bottom-4 right-4 z-[100] max-w-sm
        <?= $toast_type === 'success'
            ? 'bg-success-green/15 border-success-green/40 text-success-green shadow-[0_0_20px_rgba(74,222,128,0.15)]'
            : 'bg-red-500/15 border-red-500/40 text-red-400 shadow-[0_0_20px_rgba(239,68,68,0.15)]'
        ?> border px-5 py-4 rounded-xl backdrop-blur-md flex items-start gap-3">
        <i data-lucide="<?= $toast_type === 'success' ? 'check-circle' : 'x-circle' ?>" class="w-5 h-5 flex-shrink-0 mt-0.5"></i>
        <span class="font-manrope text-sm leading-snug"><?= esc_html($toast_msg) ?></span>
    </div>
    <script>setTimeout(() => { const t = document.getElementById('toast'); if(t) t.style.display='none'; }, 6000);</script>
    <?php endif; ?>

    <!-- ── NAVBAR ────────────────────────────────────────────────────────── -->
    <header class="h-16 md:h-20 border-b border-white/5 flex items-center justify-between px-4 md:px-8 bg-panel-dark/80 backdrop-blur-md sticky top-0 z-50">
        <div class="flex items-center gap-3">
            <span class="font-syne font-bold text-xl md:text-2xl text-white tracking-wider">GO<span class="text-sharp-purple">.</span></span>
            <span class="font-mono text-[10px] text-sharp-purple border border-sharp-purple/30 bg-sharp-purple/10 px-2 py-0.5 rounded-full uppercase tracking-widest hidden sm:inline-block">GSO Content Engine</span>
        </div>
        <div class="flex items-center gap-3">
            <a href="/wp/studio-admin.php" class="bg-sharp-purple text-white px-4 py-2 rounded-full text-xs font-bold uppercase tracking-widest hover:bg-white hover:text-matte-black transition-all shadow-[0_0_15px_rgba(126,34,206,0.3)] flex items-center gap-2">
                <i data-lucide="arrow-left" class="w-3.5 h-3.5"></i> Command Center
            </a>
        </div>
    </header>

    <main class="max-w-7xl mx-auto px-4 md:px-8 pt-10">

        <!-- ── PAGE HEADER ───────────────────────────────────────────────── -->
        <div class="mb-10 max-w-3xl">
            <h1 class="font-syne text-3xl md:text-4xl font-bold text-white mb-3">GSO Content Engine</h1>
            <p class="text-lavender/60 text-sm leading-relaxed">
                Generates hyper-specific prose and FAQs for each niche using Gemini 2.5 Flash.
                Content is saved directly to the niche post — it appears on all matching landing pages automatically.
                Gemini is shown all existing content first so it never duplicates.
            </p>
        </div>

        <!-- ── PROGRESS STATS ────────────────────────────────────────────── -->
        <div class="grid grid-cols-3 gap-4 mb-10">
            <div class="bg-card-dark border border-white/5 rounded-2xl p-5 text-center">
                <div class="font-syne text-3xl font-bold text-success-green mb-1"><?= $niches_done ?></div>
                <div class="text-xs text-lavender/50 uppercase tracking-widest font-mono">Complete</div>
            </div>
            <div class="bg-card-dark border border-white/5 rounded-2xl p-5 text-center">
                <div class="font-syne text-3xl font-bold text-yellow-400 mb-1"><?= $niches_partial ?></div>
                <div class="text-xs text-lavender/50 uppercase tracking-widest font-mono">Partial</div>
            </div>
            <div class="bg-card-dark border border-white/5 rounded-2xl p-5 text-center">
                <div class="font-syne text-3xl font-bold text-lavender/30 mb-1"><?= $niches_empty ?></div>
                <div class="text-xs text-lavender/50 uppercase tracking-widest font-mono">Not Done</div>
            </div>
        </div>

        <div class="grid grid-cols-1 xl:grid-cols-5 gap-8 items-start">

            <!-- ── LEFT PANEL: Generator Controls ────────────────────────── -->
            <div class="xl:col-span-2 space-y-6">

                <!-- Generator Card -->
                <div class="bg-card-dark border border-white/5 rounded-2xl overflow-hidden">
                    <div class="p-5 border-b border-white/5 bg-panel-dark/40">
                        <h3 class="font-syne text-lg font-bold text-white flex items-center gap-2">
                            <i data-lucide="zap" class="w-5 h-5 text-sharp-purple"></i>
                            Generate Content
                        </h3>
                        <p class="text-xs text-lavender/40 mt-1">Pick a niche and city, then generate.</p>
                    </div>
                    <div class="p-5 space-y-4">

                        <!-- Niche Selector -->
                        <div>
                            <label class="block text-xs font-mono text-lavender/50 uppercase tracking-widest mb-2">1. Select Niche *</label>
                            <select id="niche-select" class="w-full bg-matte-black border border-white/10 rounded-lg px-3 py-3 text-white focus:border-sharp-purple outline-none transition-colors appearance-none">
                                <option value="">— Choose a niche —</option>
                                <?php foreach ($all_niches as $n):
                                    $st = $niche_status[$n->ID];
                                    $dot = $st['complete'] ? '🟢' : ($st['has_prose'] || $st['has_faqs'] ? '🟡' : '⚪');
                                ?>
                                <option value="<?= $n->ID ?>" data-slug="<?= esc_attr($n->post_name) ?>">
                                    <?= $dot ?> <?= esc_html($n->post_title) ?>
                                </option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <!-- City Selector (optional) -->
                        <div>
                            <label class="block text-xs font-mono text-lavender/50 uppercase tracking-widest mb-2">
                                2. City Context <span class="text-lavender/30 normal-case font-manrope">(optional — makes content more specific)</span>
                            </label>
                            <select id="city-select" class="w-full bg-matte-black border border-white/10 rounded-lg px-3 py-3 text-white focus:border-sharp-purple outline-none transition-colors appearance-none">
                                <option value="">— General Nigeria context —</option>
                                <?php foreach ($all_cities as $c): ?>
                                <option value="<?= esc_attr($c->post_name) ?>"><?= esc_html($c->post_title) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <!-- Generate Button -->
                        <button id="generate-btn" type="button" onclick="generateContent()"
                            class="w-full bg-sharp-purple text-white py-3.5 rounded-xl text-sm font-bold uppercase tracking-widest hover:bg-white hover:text-matte-black transition-all shadow-[0_0_20px_rgba(126,34,206,0.3)] flex items-center justify-center gap-2 focus:outline-none min-h-[48px]">
                            <i data-lucide="sparkles" class="w-4 h-4" id="btn-icon"></i>
                            <span id="btn-label">Generate with Gemini</span>
                        </button>

                        <p class="text-[11px] text-lavender/30 text-center leading-relaxed">
                            Gemini reads all existing content before writing so nothing is duplicated.
                        </p>
                    </div>
                </div>

                <!-- Niche Status List -->
                <div class="bg-card-dark border border-white/5 rounded-2xl overflow-hidden">
                    <div class="p-5 border-b border-white/5 bg-panel-dark/40 flex justify-between items-center">
                        <h3 class="font-syne text-base font-bold text-white flex items-center gap-2">
                            <i data-lucide="list" class="w-4 h-4 text-lavender/50"></i> All Niches
                        </h3>
                        <span class="text-xs font-mono text-lavender/30"><?= count($all_niches) ?> total</span>
                    </div>
                    <div class="max-h-[420px] overflow-y-auto divide-y divide-white/5">
                        <?php foreach ($all_niches as $n):
                            $st = $niche_status[$n->ID];
                        ?>
                        <div class="flex items-center justify-between px-5 py-3 hover:bg-white/3 transition-colors cursor-pointer group"
                             onclick="selectNiche(<?= $n->ID ?>, '<?= esc_js($n->post_name) ?>', '<?= esc_js($n->post_title) ?>')">
                            <div class="flex items-center gap-3 min-w-0">
                                <span class="w-2 h-2 rounded-full flex-shrink-0 <?= $st['complete'] ? 'dot-done' : ($st['has_prose'] || $st['has_faqs'] ? 'dot-partial' : 'dot-empty') ?>"></span>
                                <span class="text-sm text-lavender/80 group-hover:text-white transition-colors truncate"><?= esc_html($n->post_title) ?></span>
                            </div>
                            <div class="text-right flex-shrink-0 ml-3">
                                <?php if ($st['gen_at']): ?>
                                <span class="text-[10px] font-mono text-lavender/30"><?= date('d M', strtotime($st['gen_at'])) ?></span>
                                <?php endif; ?>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    </div>
                </div>

            </div>

            <!-- ── RIGHT PANEL: Preview & Save ───────────────────────────── -->
            <div class="xl:col-span-3 space-y-5" id="preview-panel">

                <!-- Empty state -->
                <div id="empty-state" class="bg-card-dark border border-white/5 rounded-2xl p-12 text-center">
                    <div class="w-16 h-16 rounded-2xl bg-sharp-purple/10 flex items-center justify-center mx-auto mb-4">
                        <i data-lucide="sparkles" class="w-7 h-7 text-sharp-purple"></i>
                    </div>
                    <h3 class="font-syne text-lg font-bold text-white mb-2">Nothing generated yet</h3>
                    <p class="text-lavender/40 text-sm max-w-xs mx-auto leading-relaxed">
                        Pick a niche on the left and click Generate. The content preview will appear here for you to review before saving.
                    </p>
                </div>

                <!-- Skeleton (shown during loading) -->
                <div id="skeleton-state" class="hidden space-y-4">
                    <div class="bg-card-dark border border-white/5 rounded-2xl p-6 space-y-4">
                        <div class="skeleton h-4 w-1/3"></div>
                        <div class="skeleton h-20 w-full"></div>
                        <div class="skeleton h-20 w-full"></div>
                    </div>
                    <div class="bg-card-dark border border-white/5 rounded-2xl p-6 space-y-3">
                        <div class="skeleton h-4 w-1/4"></div>
                        <?php for ($i=0;$i<6;$i++): ?>
                        <div class="skeleton h-10 w-full"></div>
                        <div class="skeleton h-14 w-full"></div>
                        <?php endfor; ?>
                    </div>
                </div>

                <!-- Generated Content Preview (hidden until content arrives) -->
                <div id="content-state" class="hidden">
                    <form method="POST" id="save-form">
                        <input type="hidden" name="niche_id"   id="save-niche-id"   value="">
                        <input type="hidden" name="city_slug"  id="save-city-slug"  value="">
                        <input type="hidden" name="save_content" value="1">

                        <!-- Quality warning banner -->
                        <div id="quality-warning-box" class="hidden bg-yellow-900/30 border border-yellow-500/40 rounded-2xl p-4 mb-4">
                            <p class="font-mono text-[10px] text-yellow-400 uppercase tracking-widest mb-2">⚠ Quality Flags — Edit before saving</p>
                            <ul id="quality-warning-list" class="font-manrope text-xs text-yellow-200/80 space-y-1 list-none"></ul>
                        </div>

                        <!-- Header bar -->
                        <div class="bg-card-dark border border-white/5 rounded-2xl p-5 mb-4 flex items-center justify-between flex-wrap gap-3">
                            <div>
                                <p class="font-mono text-[10px] text-sharp-purple uppercase tracking-widest mb-1">Preview</p>
                                <h3 class="font-syne text-lg font-bold text-white" id="preview-niche-title">—</h3>
                                <p class="text-xs text-lavender/40 mt-0.5" id="preview-city-label"></p>
                            </div>
                            <div class="flex items-center gap-3">
                                <button type="button" onclick="generateContent()" class="px-4 py-2 border border-lavender/20 text-lavender/60 hover:text-white hover:border-lavender text-xs font-bold uppercase tracking-widest rounded-full transition-all focus:outline-none flex items-center gap-1.5">
                                    <i data-lucide="refresh-cw" class="w-3.5 h-3.5"></i> Regenerate
                                </button>
                                <button type="submit" class="px-5 py-2 bg-success-green text-black font-bold text-xs uppercase tracking-widest rounded-full hover:bg-green-300 transition-all shadow-[0_0_15px_rgba(74,222,128,0.25)] flex items-center gap-1.5 focus:outline-none">
                                    <i data-lucide="save" class="w-3.5 h-3.5"></i> Save to Niche
                                </button>
                            </div>
                        </div>

                        <!-- Prose Section -->
                        <div class="bg-card-dark border border-white/5 rounded-2xl overflow-hidden mb-4">
                            <div class="p-4 border-b border-white/5 bg-panel-dark/40 flex items-center gap-2">
                                <i data-lucide="align-left" class="w-4 h-4 text-lavender/50"></i>
                                <span class="font-syne text-sm font-bold text-white">GSO Prose</span>
                                <span class="text-xs text-lavender/30 font-mono ml-1">(reality_p5 & reality_p6 — slots into Reality section)</span>
                            </div>
                            <div class="p-5 space-y-4">
                                <div>
                                    <label class="block text-[10px] font-mono text-lavender/40 uppercase tracking-widest mb-2">Paragraph 5</label>
                                    <textarea name="reality_p5" id="field-reality_p5" rows="4"
                                        class="w-full bg-matte-black border border-white/10 rounded-lg px-4 py-3 text-lavender/90 focus:border-sharp-purple outline-none transition-colors resize-y leading-relaxed"></textarea>
                                </div>
                                <div>
                                    <label class="block text-[10px] font-mono text-lavender/40 uppercase tracking-widest mb-2">Paragraph 6</label>
                                    <textarea name="reality_p6" id="field-reality_p6" rows="4"
                                        class="w-full bg-matte-black border border-white/10 rounded-lg px-4 py-3 text-lavender/90 focus:border-sharp-purple outline-none transition-colors resize-y leading-relaxed"></textarea>
                                </div>
                            </div>
                        </div>

                        <!-- FAQs Section -->
                        <div class="bg-card-dark border border-white/5 rounded-2xl overflow-hidden mb-4">
                            <div class="p-4 border-b border-white/5 bg-panel-dark/40 flex items-center gap-2">
                                <i data-lucide="help-circle" class="w-4 h-4 text-lavender/50"></i>
                                <span class="font-syne text-sm font-bold text-white">Extended FAQs</span>
                                <span class="text-xs text-lavender/30 font-mono ml-1">(faq6–faq11 — hidden behind "More Questions" toggle)</span>
                            </div>
                            <div class="p-5 space-y-5">
                                <?php for ($i = 6; $i <= 11; $i++): ?>
                                <div class="border border-white/5 rounded-xl p-4 space-y-3">
                                    <span class="font-mono text-[10px] text-sharp-purple uppercase tracking-widest">FAQ <?= $i ?></span>
                                    <div>
                                        <label class="block text-[10px] font-mono text-lavender/30 uppercase tracking-widest mb-1.5">Question</label>
                                        <input type="text" name="faq<?= $i ?>_q" id="field-faq<?= $i ?>_q"
                                            class="w-full bg-matte-black border border-white/10 rounded-lg px-4 py-2.5 text-white focus:border-sharp-purple outline-none transition-colors">
                                    </div>
                                    <div>
                                        <label class="block text-[10px] font-mono text-lavender/30 uppercase tracking-widest mb-1.5">Answer</label>
                                        <textarea name="faq<?= $i ?>_a" id="field-faq<?= $i ?>_a" rows="3"
                                            class="w-full bg-matte-black border border-white/10 rounded-lg px-4 py-2.5 text-lavender/80 focus:border-sharp-purple outline-none transition-colors resize-y"></textarea>
                                    </div>
                                </div>
                                <?php endfor; ?>
                            </div>
                        </div>

                        <!-- Clear button (danger zone) -->
                        <div class="flex justify-end">
                            <button type="submit" name="clear_content" value="1"
                                onclick="return confirm('This will wipe all GSO content from this niche. Are you sure?')"
                                class="px-4 py-2 border border-red-500/20 text-red-400/60 hover:text-red-400 hover:border-red-500/50 text-xs font-mono uppercase tracking-widest rounded-full transition-all focus:outline-none flex items-center gap-1.5">
                                <i data-lucide="trash-2" class="w-3.5 h-3.5"></i> Clear GSO Content
                            </button>
                        </div>

                    </form>
                </div>

            </div>
        </div>

    </main>

    <script>
        lucide.createIcons();

        const emptyState    = document.getElementById('empty-state');
        const skeletonState = document.getElementById('skeleton-state');
        const contentState  = document.getElementById('content-state');

        // ── Select a niche from the sidebar list ─────────────────────────────
        function selectNiche(nicheId, nicheSlug, nicheTitle) {
            const sel = document.getElementById('niche-select');
            sel.value = nicheId;
        }

        // ── Main generate function ────────────────────────────────────────────
        async function generateContent() {
            const nicheSelect = document.getElementById('niche-select');
            const citySelect  = document.getElementById('city-select');
            const btn         = document.getElementById('generate-btn');
            const btnLabel    = document.getElementById('btn-label');
            const btnIcon     = document.getElementById('btn-icon');

            const nicheId  = nicheSelect.value;
            const citySlug = citySelect.value;

            if (!nicheId) {
                alert('Please select a niche first.');
                return;
            }

            // Show skeleton, hide others
            emptyState.classList.add('hidden');
            contentState.classList.add('hidden');
            skeletonState.classList.remove('hidden');

            // Loading state on button
            btn.disabled   = true;
            btnLabel.textContent = 'Generating…';
            btnIcon.classList.add('spin');

            try {
                const formData = new FormData();
                formData.append('niche_id',  nicheId);
                formData.append('city_slug', citySlug);

                const res  = await fetch('?action=generate', { method: 'POST', body: formData });
                const data = await res.json();

                if (!data.ok) {
                    skeletonState.classList.add('hidden');
                    emptyState.classList.remove('hidden');
                    alert('Generation failed: ' + data.error);
                    return;
                }

                // Populate all fields
                const c = data.content;
                const fieldKeys = [
                    'reality_p5','reality_p6',
                    'faq6_q','faq6_a','faq7_q','faq7_a',
                    'faq8_q','faq8_a','faq9_q','faq9_a',
                    'faq10_q','faq10_a','faq11_q','faq11_a'
                ];
                fieldKeys.forEach(key => {
                    const el = document.getElementById('field-' + key);
                    if (el && c[key]) el.value = c[key];
                });

                // Set hidden save fields
                document.getElementById('save-niche-id').value  = data.niche_id;
                document.getElementById('save-city-slug').value = citySlug;

                // Update header labels
                document.getElementById('preview-niche-title').textContent = data.niche_name;
                document.getElementById('preview-city-label').textContent  = data.city_name
                    ? 'Context city: ' + data.city_name
                    : 'General Nigeria context';

                // Show content panel
                skeletonState.classList.add('hidden');
                contentState.classList.remove('hidden');

                // Show quality warnings if Gemini used any banned filler words
                const warningBox = document.getElementById('quality-warning-box');
                const warningList = document.getElementById('quality-warning-list');
                if (data.quality_warnings && data.quality_warnings.length > 0) {
                    warningList.innerHTML = data.quality_warnings.map(w => `<li>⚠️ ${w}</li>`).join('');
                    warningBox.classList.remove('hidden');
                } else {
                    warningBox.classList.add('hidden');
                    warningList.innerHTML = '';
                }

            } catch (err) {
                skeletonState.classList.add('hidden');
                emptyState.classList.remove('hidden');
                alert('Network error: ' + err.message);
            } finally {
                btn.disabled = false;
                btnLabel.textContent = 'Generate with Gemini';
                btnIcon.classList.remove('spin');
            }
        }
    </script>
</body>
</html>
