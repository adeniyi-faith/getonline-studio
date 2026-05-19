<?php
// PSEO AUTO-BUILDER (CRON WORKER)
// Runs autonomously in the background to build the Listicle Publish Queue.
// Recommended Cron Job (Every 20 Minutes):
// */20 * * * * curl -s "https://yourdomain.com/pseo-autobuilder.php?key=AUTOBOT_778899" > /dev/null

set_time_limit(0); // Prevents the server from timing out during long AI generations
ignore_user_abort(true); // Allows the script to finish even if you close your browser tab

// --- CONFIGURATION ---
define('PSEO_CRON_KEY', 'AUTOBOT_778899'); // Change this to a secure random string
define('PSEO_BATCH_SIZE', 1); // Changed to 1 listicle to focus AI brainpower
define('PSEO_MAX_RETRIES', 3); // How many times to retry if the AI fails
define('GEMINI_API_KEY', 'AIzaSyC1DPDJzt5psEkIDgqK3XztuVLgXnDwwZM');

// 1. Security Check
if (!isset($_GET['key']) || $_GET['key'] !== PSEO_CRON_KEY) {
    http_response_code(403);
    die('Forbidden: Invalid Cron Key.');
}

// 2. Boot up WordPress silently
$wp_paths = [ __DIR__ . '/wp-load.php', __DIR__ . '/wp/wp-load.php', dirname(__DIR__) . '/wp-load.php' ];
$wp_loaded = false;
foreach ($wp_paths as $path) {
    if (file_exists($path)) { require_once($path); $wp_loaded = true; break; }
}
if (!$wp_loaded) die("Fatal Error: Could not find wp-load.php");

// Log start
$execution_log = ["🤖 PSEO Auto-Builder Started at " . current_time('mysql')];

// 3. Dynamically load Master Niches from WordPress database
// This replaces the old hardcoded array so new niches added via admin panel
// are automatically picked up on the next cron run — no code changes needed.
$master_niches = [];
$niche_posts = get_posts([
    'post_type'   => 'pseo_niche',
    'numberposts' => -1,
    'post_status' => 'publish',
    'orderby'     => 'title',
    'order'       => 'ASC'
]);

foreach ($niche_posts as $niche) {
    $master_niches[$niche->post_name] = $niche->post_title;
}

// Fallback: if no niches found in DB, die with helpful message
if (empty($master_niches)) {
    die("Fatal: No published niches found in pseo_niche post type. Add and publish niches via your Command Center admin panel first.");
}

$execution_log[] = "📋 Loaded " . count($master_niches) . " niches from database.";

// 4. Find the Queue (Active Cities without Listicles)
$active_cities = get_posts(['post_type' => 'pseo_location', 'numberposts' => -1, 'post_status' => 'publish', 'orderby' => 'title', 'order' => 'ASC']);

global $wpdb;
$table_name = $wpdb->prefix . 'pseo_listicles';
$all_listicles = $wpdb->get_results("SELECT city_slug, niche_slug FROM $table_name", ARRAY_A);

$listicle_map = [];
foreach ((array)$all_listicles as $row) { $listicle_map[$row['city_slug']][$row['niche_slug']] = true; }

$queue = [];
foreach ($active_cities as $loc) {
    foreach ($master_niches as $n_slug => $n_name) {
        if (!isset($listicle_map[$loc->post_name][$n_slug])) {
            $queue[] = ['city_slug' => $loc->post_name, 'city_name' => $loc->post_title, 'niche_slug' => $n_slug, 'niche_name' => $n_name];
        }
    }
}

if (empty($queue)) {
    die("Queue is empty. Everything is built!");
}

// Slice batch
$batch = array_slice($queue, 0, PSEO_BATCH_SIZE);
$execution_log[] = "Found " . count($queue) . " items in queue. Processing batch of " . count($batch) . "...";

// Fetch Local Competitors JSON for dynamic injection
$competitors_json = '{}';
$competitors_path_1 = '/home2/worldin6/public_html/getonlinestudio.com/wp/competitors.json';
$competitors_path_2 = __DIR__ . '/competitors.json';
if (file_exists($competitors_path_1)) { $competitors_json = file_get_contents($competitors_path_1); } 
elseif (file_exists($competitors_path_2)) { $competitors_json = file_get_contents($competitors_path_2); }
$localCompetitorsData = json_decode($competitors_json, true) ?: [];

// 5. The Advanced Listicle System Prompt Generator
function getListicleSystemPrompt($citySlug, $cityName, $nicheName, $localCompetitorsData) {
    $competitorRule = "5. THE COMPETITORS (#2 to #5): You MUST use REAL web design agencies, IT firms, tech companies, or digital marketing brands that actually operate in {$cityName} (or Nigeria broadly). Do NOT use generic archetypes.";
    
    if (isset($localCompetitorsData[$citySlug]) && count($localCompetitorsData[$citySlug]) >= 4) {
        $shuffled = $localCompetitorsData[$citySlug];
        shuffle($shuffled);
        $selected = array_slice($shuffled, 0, 4);
        
        $competitorRule = "5. THE COMPETITORS (#2 to #5 - STRICTLY ENFORCED):\nYou MUST use the following 4 specific local companies for spots #2, #3, #4, and #5. DO NOT invent names. If their phone number or website link is publicly known, mention it naturally in their review.\n";
        foreach ($selected as $index => $comp) { $competitorRule .= "   Rank #" . ($index + 2) . ": {$comp}\n"; }
    }

    return <<<EOT
You are an elite SEO copywriter, trained in the exact writing style of Brian Dean (Backlinko).
Your task is to write a highly-ranked, comprehensive 1,000+ word "Listicle" article.
Target: Top {$nicheName} Web Designers and Developers in {$cityName}.

WHO WE ARE (GETONLINE STUDIO CONTEXT):
You are writing this on behalf of GetOnline Studio. Our mission is helping brands and businesses GET ONLINE and SCALE. 
Our primary audience: Business owners who currently do NOT have a website and are losing customers because of it.
Our secondary audience: Businesses looking for a redesign because their current website is broken, ugly, or not bringing in sales.
Our advanced audience: Ambitious brands and high-tech clients who need custom software, highly functional web apps, and complex integrations.

TONE & STYLE RULES:
1. Use ridiculously short sentences. Often just one line.
2. Write at a 6th-grade reading level. Keep the core messaging accessible. 
3. Use "Bucket Brigades" to keep people reading (e.g., "Here's the deal:", "Think about it.", "But there's a catch.").
4. Speak directly to the reader using "you" and "your". Make them feel understood.
5. Balance the messaging: Focus mostly on plain English benefits ("attracting customers", "stress-free setup"), but explicitly state our massive capability range: we build stress-free websites for beginners, AND highly functional web apps/custom software for tech-heavy clients.

CRITICAL CONTENT RULES:
1. OUTPUT FORMAT: Return ONLY valid JSON with three keys: "target_keyword", "meta_title", and "content".
2. HTML & JSON SAFETY: The "content" value MUST be raw, beautifully formatted HTML. Use <h2>, <h3>, <p>, <ul>, <li>, <strong>, <table>. CRITICAL: Use SINGLE QUOTES ('') for all HTML attributes (e.g., <table class='table'>) to avoid breaking the JSON string.
3. HOW WE RANKED: Immediately after the intro, create an <h2>How We Ranked These Agencies</h2> section. Provide a brief paragraph and an unordered list <ul> of 3 to 4 ranking criteria used to judge them (e.g., Local {$cityName} Reputation, Tech Capabilities, Pricing & Value, Client Success).
4. COMPARISON TABLE: Immediately after the "How We Ranked" section, output a clean HTML <table> comparing the 5 agencies. Columns: Agency Name, Best For, Standout Feature.
5. THE #1 SPOT: "GetOnline Studio" MUST ALWAYS be the #1 ranked agency. Start our review by bolding key phrases explaining exactly why having a website is crucial for this specific niche. Then, explicitly highlight our range: mention that we provide simple, stress-free websites to attract local customers, BUT we also engineer custom software, web applications, and highly functional apps for ambitious brands ready to scale.
{$competitorRule}
7. PROS & CONS: For competitors #2 through #5, end their review with an unordered list <ul> containing exactly 2 Pros and 1 Con. Be objective. Frame their "Con" around being too complicated, using generic templates that look like everyone else's, or lacking the advanced software development skills that we offer.
8. BUYING GUIDE: Create an <h2>What to Look For in a {$nicheName} Web Designer</h2> section. Provide 4 actionable tips STRICTLY formatted as an HTML unordered list (<ul><li><strong>Tip Name:</strong> Description</li></ul>). DO NOT use numbered lists or plain text paragraphs here.
9. THE FINAL VERDICT: End the article with an <h2>The Final Verdict</h2> section. Write a closing paragraph stating that while the other agencies listed are fine for general services, GetOnline Studio specializes in helping {$nicheName} businesses get online smoothly, while also possessing the hardcore engineering skills to build custom software and apps for brands ready to dominate the {$cityName} market.

JSON FORMAT:
{
  "target_keyword": "Best {$nicheName} Web Design Agency in {$cityName}",
  "meta_title": "Top 5 {$nicheName} Web Designers in {$cityName} (2026 Rankings)",
  "content": "<h2>Why Your {$nicheName} in {$cityName} Needs a Real Website</h2><p>...</p><h2>How We Ranked These Agencies</h2><ul><li><strong>...</strong></li></ul><table class='table'>...</table><h3>1. GetOnline Studio</h3><p>...</p><h3>2. [Competitor Name]</h3><p>...</p><ul><li><strong>Pro:</strong>...</li><li><strong>Con:</strong>...</li></ul><h2>What to Look For...</h2><ul><li><strong>Speed:</strong> ...</li><li><strong>Custom Apps:</strong> ...</li></ul><h2>The Final Verdict</h2><p>...</p>"
}
EOT;
}

// 6. Gemini API Call Function with built-in retries
function call_gemini_with_retries($system_instruction, $user_prompt) {
    $api_url = "https://generativelanguage.googleapis.com/v1beta/models/gemini-2.5-flash:generateContent?key=" . GEMINI_API_KEY;
    
    $payload = json_encode([
        'system_instruction' => ['parts' => [['text' => $system_instruction]]],
        'contents' => [['role' => 'user', 'parts' => [['text' => $user_prompt]]]],
        'generationConfig' => [ 'temperature' => 0.7, 'responseMimeType' => 'application/json' ]
    ]);

    for ($attempt = 1; $attempt <= PSEO_MAX_RETRIES; $attempt++) {
        $response = wp_remote_post($api_url, [
            'body'    => $payload,
            'headers' => ['Content-Type' => 'application/json'],
            'timeout' => 45 // Wait up to 45 seconds for a long article
        ]);

        if (is_wp_error($response)) {
            sleep(2); continue; // API timeout or network error
        }

        $body = wp_remote_retrieve_body($response);
        $data = json_decode($body, true);

        if (isset($data['candidates'][0]['content']['parts'][0]['text'])) {
            $raw_json = $data['candidates'][0]['content']['parts'][0]['text'];
            $clean_json = trim(str_replace(['```json', '```'], '', $raw_json));
            $parsed = json_decode($clean_json, true);
            
            if ($parsed && isset($parsed['content']) && strlen($parsed['content']) > 500) {
                return $parsed; // Success!
            }
        }
        
        sleep(2); // Failed parsing or too short, sleep and retry
    }
    
    return false; // Exhausted retries
}

// 7. Process the Batch
$successful_builds = [];

foreach ($batch as $item) {
    $execution_log[] = "⏳ Generating listicle for {$item['niche_name']} in {$item['city_name']}...";
    
    $system_prompt = getListicleSystemPrompt($item['city_slug'], $item['city_name'], $item['niche_name'], $localCompetitorsData);
    $user_prompt = "Write a 1,000+ word aggregator listicle with a Comparison Table, Pros/Cons, and Buying Guide for the niche: {$item['niche_name']} in the city: {$item['city_name']}, Nigeria.";
    
    $ai_data = call_gemini_with_retries($system_prompt, $user_prompt);
    
    if ($ai_data) {
        // Save to Database
        $wpdb->insert($table_name, [
            'city_slug'      => $item['city_slug'],
            'niche_slug'     => $item['niche_slug'],
            'target_keyword' => sanitize_text_field($ai_data['target_keyword']),
            'meta_title'     => sanitize_text_field($ai_data['meta_title']),
            'content'        => wp_kses_post($ai_data['content']),
            'status'         => 'publish',
            'created_at'     => current_time('mysql'),
            'updated_at'     => current_time('mysql')
        ]);
        
        // Ensure Niche Parent is Published
        $existing_niche = get_page_by_path($item['niche_slug'], OBJECT, 'pseo_niche');
        if (!$existing_niche || $existing_niche->post_status !== 'publish') {
            wp_insert_post([
                'ID'          => $existing_niche ? $existing_niche->ID : 0,
                'post_title'  => ucwords(str_replace('-', ' ', $item['niche_slug'])),
                'post_name'   => $item['niche_slug'],
                'post_type'   => 'pseo_niche',
                'post_status' => 'publish'
            ]);
        }
        
        // Trigger Instant Indexing if class exists
        $new_url = "https://getonlinestudio.com/locations/{$item['city_slug']}/top-{$item['niche_slug']}-web-designers/";
        if (file_exists(__DIR__ . '/class-pseo-indexer.php')) {
            require_once(__DIR__ . '/class-pseo-indexer.php');
            $indexer = new PSEO_Instant_Indexer();
            $indexer->ping_all_networks($new_url);
        }

        $successful_builds[] = $new_url;
        $execution_log[] = "✅ Success: $new_url";
    } else {
        $execution_log[] = "❌ Failed: {$item['niche_name']} in {$item['city_name']} (Exhausted retries)";
    }
}

// 8. Send Admin Email Notification
if (!empty($successful_builds)) {
    $admin_email = get_option('admin_email');
    $subject = "🤖 pSEO Auto-Builder: " . count($successful_builds) . " New Listicles Published";
    
    $message = "Your Programmatic SEO engine just autonomously built and published new content:\n\n";
    foreach ($successful_builds as $url) {
        $message .= "- $url\n";
    }
    
    $message .= "\nTotal items remaining in queue: " . (count($queue) - count($successful_builds)) . "\n\n";
    $message .= "Execution Log:\n" . implode("\n", $execution_log);
    
    wp_mail($admin_email, $subject, $message, ['Content-Type: text/plain; charset=UTF-8']);
}

echo "Cron Job Completed.\n";
print_r($execution_log);