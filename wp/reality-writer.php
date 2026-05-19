<?php
/**
 * NATIONAL REALITY WRITER v3.0
 *
 * Simple. Clean. No category buckets.
 * Just passes the niche name directly to Gemini and lets it write.
 *
 * Manual run:
 * https://getonlinestudio.com/wp/reality-writer.php?key=AUTOBOT_778899
 *
 * Reset all niches (force rewrite):
 * https://getonlinestudio.com/wp/reality-writer.php?key=AUTOBOT_778899&reset=1
 *
 * Cron (every 10 minutes):
 * 10 * * * * curl -s "https://getonlinestudio.com/wp/reality-writer.php?key=AUTOBOT_778899" > /dev/null
 */

set_time_limit(0);
ignore_user_abort(true);

define('RW_KEY',        'AUTOBOT_778899');
define('RW_GEMINI_KEY', 'AIzaSyC1DPDJzt5psEkIDgqK3XztuVLgXnDwwZM');

// ── Security ──────────────────────────────────────────────────────────────────
if (empty($_GET['key']) || $_GET['key'] !== RW_KEY) {
    http_response_code(403); die('Forbidden.');
}

// ── Boot WordPress ────────────────────────────────────────────────────────────
$loaded = false;
foreach ([__DIR__.'/wp-load.php', __DIR__.'/wp/wp-load.php', dirname(__DIR__).'/wp-load.php'] as $p) {
    if (file_exists($p)) { require_once $p; $loaded = true; break; }
}
if (!$loaded || !function_exists('get_posts')) die('Fatal: WordPress not loaded.');

$log = ['Reality Writer v3.0 started at ' . current_time('mysql')];

// ── Reset ─────────────────────────────────────────────────────────────────────
if (!empty($_GET['reset']) && $_GET['reset'] === '1') {
    $all = get_posts(['post_type' => 'pseo_niche', 'numberposts' => -1, 'post_status' => 'any']);
    foreach ($all as $np) delete_post_meta($np->ID, '_reality_built_at');
    update_option('reality_writer_last_run', [
        'time'    => current_time('mysql'),
        'status'  => 'reset',
        'message' => 'Reset done. ' . count($all) . ' niches cleared.',
    ]);
    die('Reset complete. ' . count($all) . ' niches cleared. Visit the script again to start writing.');
}

// ── Load all niches ───────────────────────────────────────────────────────────
$niche_posts = get_posts([
    'post_type'   => 'pseo_niche',
    'numberposts' => -1,
    'post_status' => 'any',
    'orderby'     => 'title',
    'order'       => 'ASC',
]);
if (empty($niche_posts)) die('Fatal: No niches found in pseo_niche.');
$log[] = 'Found ' . count($niche_posts) . ' niches.';

// ── Build queue ───────────────────────────────────────────────────────────────
$queue = [];
foreach ($niche_posts as $np) {
    if (empty(get_post_meta($np->ID, '_reality_built_at', true))) {
        $queue[] = ['id' => $np->ID, 'name' => $np->post_title, 'slug' => $np->post_name];
    }
}

if (empty($queue)) {
    update_option('reality_writer_last_run', [
        'time'    => current_time('mysql'),
        'status'  => 'complete',
        'message' => 'All ' . count($niche_posts) . ' niches are done.',
        'log'     => $log,
    ]);
    die('All ' . count($niche_posts) . ' niches have national reality copy. Hit reset to rewrite.');
}

// One niche per run — full focus, full quality
$item = $queue[0];
$log[] = count($queue) . ' niches remaining. Writing: ' . $item['name'];

// ── The Prompt ────────────────────────────────────────────────────────────────
// No categories. No buckets. Just the niche name, the brand context,
// and clear instructions. Gemini already knows every industry deeply.

$niche = $item['name']; // e.g. "Accounting Firm", "Restaurant", "Oil & Gas Company"

$system_prompt =
'You are a senior copywriter at GetOnline Studio, a registered Nigerian digital agency.' . "\n\n" .

'ABOUT GETONLINE STUDIO:' . "\n" .
'We do not just build websites. We help businesses get online and scale their entire operations.' . "\n" .
'We build starter websites for businesses getting online for the first time.' . "\n" .
'We also build advanced custom solutions: portals, dashboards, mobile apps, API integrations, booking systems, and enterprise platforms.' . "\n" .
'Our clients range from small business owners launching their first site to established companies needing a full digital upgrade.' . "\n\n" .

'YOUR TASK:' . "\n" .
'Write the national reality section for the ' . $niche . ' page on our website.' . "\n" .
'This section must make a ' . $niche . ' owner in Nigeria stop, read, and feel like we completely understand their world.' . "\n\n" .

'WHO IS READING THIS:' . "\n" .
'PRIMARY: ' . $niche . ' owners and operators in Nigeria who have no website yet. They rely on WhatsApp, word of mouth, or foot traffic. They do not realise how much business they are losing every day.' . "\n" .
'SECONDARY: ' . $niche . ' businesses that already have a website but it is outdated, slow, poorly designed, or not generating any real results. They need a redesign or upgrade.' . "\n\n" .

'WHAT THE COPY MUST DO:' . "\n" .
'1. Make the ' . $niche . ' owner feel deeply understood — like this was written by someone who has worked inside their industry.' . "\n" .
'2. Show them what they are losing without a strong digital presence. Be specific to ' . $niche . ' businesses — not generic.' . "\n" .
'3. Show them what becomes possible with a great website or platform. Cover real benefits: more clients, more revenue, brand credibility, global reach, partnership opportunities, 24/7 availability, trust, and competitive advantage.' . "\n" .
'4. Address both audiences — those starting fresh and those who need an upgrade.' . "\n" .
'5. Mention that GetOnline Studio goes beyond basic websites — portals, custom tools, and scalable platforms for businesses ready to grow further.' . "\n\n" .

'AI OVERVIEW OPTIMISATION:' . "\n" .
'Each paragraph must open with a clear, standalone statement that directly answers a question a ' . $niche . ' owner would search on Google, such as:' . "\n" .
'"Why does a ' . $niche . ' need a website in Nigeria?"' . "\n" .
'"What does a ' . $niche . ' website cost in Nigeria?"' . "\n" .
'"How can a ' . $niche . ' get more clients through a website?"' . "\n" .
'This structure makes our content extractable by Google AI Overviews and ChatGPT.' . "\n\n" .

'RULES:' . "\n" .
'1. Write specifically about ' . $niche . ' businesses. Every sentence must feel like it belongs only on this page.' . "\n" .
'2. Write the niche name directly — do not use placeholder tags like {niche_name} or {city_name}.' . "\n" .
'3. Each paragraph: 4 to 6 sentences. Rich, specific, and persuasive.' . "\n" .
'4. Outcome cards: name a specific, tangible result a ' . $niche . ' would actually care about. Not generic marketing language.' . "\n" .
'5. Reference Nigeria specifically — Nigerian clients, Nigerian market, Nigerian search behaviour.' . "\n" .
'6. Do not mention specific cities — this is a national page.' . "\n" .
'7. Use Naira (₦) for any pricing references. Never USD.' . "\n" .
'8. OUTPUT ONLY VALID JSON. No text before or after the JSON object.' . "\n\n" .

'JSON SCHEMA — return exactly these 12 keys:' . "\n" .
'{' . "\n" .
'  "reality_p1": "Opening paragraph. Start with a direct statement answering why a ' . $niche . ' needs a website in Nigeria. Make it specific, urgent, and grounded in how Nigerian clients actually search and decide.",'. "\n" .
'  "reality_p2": "What a ' . $niche . ' loses without a website or with a poor one. Who walks past them? What contracts, clients, or opportunities go to competitors? Speak to both the starter and the redesign audience.",'. "\n" .
'  "reality_p3": "What becomes possible when a ' . $niche . ' has a great digital presence. Cover multiple benefits — new clients, revenue growth, credibility, partnerships, global visibility, 24/7 service, and custom solutions for businesses ready to scale further.",'. "\n" .
'  "reality_p4": "The competitive urgency paragraph. Other ' . $niche . ' businesses are already investing in their digital presence. What does falling behind look like? End with how GetOnline Studio is the right partner — not just for websites but for full digital growth.",'. "\n" .
'  "pos1_title": "A specific outcome a ' . $niche . ' cares about. Max 6 words.",' . "\n" .
'  "pos1_desc": "3 sentences explaining this outcome specifically for a ' . $niche . ' in Nigeria.",' . "\n" .
'  "pos2_title": "A different specific outcome. Max 6 words.",' . "\n" .
'  "pos2_desc": "3 sentences.",' . "\n" .
'  "pos3_title": "Another specific outcome. Max 6 words.",' . "\n" .
'  "pos3_desc": "3 sentences.",' . "\n" .
'  "pos4_title": "Final specific outcome. Max 6 words.",' . "\n" .
'  "pos4_desc": "3 sentences."' . "\n" .
'}';

$user_prompt =
    'Write the national reality copy now for: ' . $niche . "\n\n" .
    'This is for Nigerian ' . $niche . ' owners. Make every sentence feel like it was written specifically for them.' . "\n" .
    'Write the niche name directly — do not use {niche_name} or any placeholder tags.' . "\n" .
    'Return only the JSON object. Nothing else.';

// ── Gemini API ────────────────────────────────────────────────────────────────
function rw_call_gemini($system, $user) {
    $url = 'https://generativelanguage.googleapis.com/v1beta/models/gemini-2.5-flash:generateContent?key=' . RW_GEMINI_KEY;
    $payload = json_encode([
        'system_instruction' => ['parts' => [['text' => $system]]],
        'contents'           => [['role' => 'user', 'parts' => [['text' => $user]]]],
        'generationConfig'   => ['temperature' => 0.8, 'responseMimeType' => 'application/json'],
    ]);
    for ($i = 0; $i < 3; $i++) {
        $r = wp_remote_post($url, [
            'body'    => $payload,
            'headers' => ['Content-Type' => 'application/json'],
            'timeout' => 90,
        ]);
        if (is_wp_error($r)) { sleep(4); continue; }
        $d = json_decode(wp_remote_retrieve_body($r), true);
        if (!empty($d['candidates'][0]['content']['parts'][0]['text'])) {
            $raw = trim(str_replace(['```json', '```'], '', $d['candidates'][0]['content']['parts'][0]['text']));
            $p   = json_decode($raw, true);
            if ($p && isset($p['reality_p1'])) return $p;
        }
        sleep(4);
    }
    return false;
}

// ── Call Gemini ───────────────────────────────────────────────────────────────
$result = rw_call_gemini($system_prompt, $user_prompt);

if (!$result) {
    $log[] = 'FAILED for ' . $item['name'] . '. Gemini returned nothing valid. Will retry next run.';
    update_option('reality_writer_last_run', [
        'time'      => current_time('mysql'),
        'status'    => 'failed',
        'built'     => [],
        'remaining' => count($queue),
        'total'     => count($niche_posts),
        'log'       => $log,
    ]);
    die('Failed for ' . $item['name'] . '. Will retry on next cron run.');
}

// ── Belt-and-suspenders: remove any stray placeholder tags ────────────────────
array_walk_recursive($result, function(&$v) use ($niche) {
    if (!is_string($v)) return;
    $v = str_replace(['{niche_name}', '{niche_plural}', '{city_name}'], [$niche, $niche . 's', 'Nigeria'], $v);
});

// ── Save to post meta ─────────────────────────────────────────────────────────
$keys  = ['reality_p1','reality_p2','reality_p3','reality_p4','pos1_title','pos1_desc','pos2_title','pos2_desc','pos3_title','pos3_desc','pos4_title','pos4_desc'];
$saved = 0;
foreach ($keys as $k) {
    if (!empty($result[$k])) {
        update_post_meta($item['id'], $k, wp_kses_post($result[$k]));
        $saved++;
    }
}

// Stamp so this niche is not reprocessed
update_post_meta($item['id'], '_reality_built_at', current_time('mysql'));

// Save preview snippet for the dashboard
update_post_meta($item['id'], '_reality_preview', [
    'reality_p1' => substr($result['reality_p1'] ?? '', 0, 200) . '...',
    'pos1_title' => $result['pos1_title'] ?? '',
    'pos2_title' => $result['pos2_title'] ?? '',
    'pos3_title' => $result['pos3_title'] ?? '',
    'pos4_title' => $result['pos4_title'] ?? '',
]);

wp_update_post(['ID' => $item['id'], 'post_status' => 'publish']);

$remaining = count($queue) - 1;
$log[]     = 'Done: ' . $item['name'] . ' — ' . $saved . '/12 keys saved. ' . $remaining . ' niches remaining.';

// ── Save global run status for dashboard ──────────────────────────────────────
update_option('reality_writer_last_run', [
    'time'      => current_time('mysql'),
    'status'    => 'success',
    'built'     => [$item['name']],
    'remaining' => $remaining,
    'total'     => count($niche_posts),
    'log'       => $log,
]);

// ── Email notification ────────────────────────────────────────────────────────
wp_mail(
    get_option('admin_email'),
    'Reality Writer v3: ' . $item['name'] . ' done — ' . $remaining . ' remaining',
    implode("\n", $log) . "\n\n--- reality_p1 preview ---\n" . substr($result['reality_p1'] ?? '', 0, 500),
    ['Content-Type: text/plain; charset=UTF-8']
);

echo 'Done: ' . $item['name'] . ' — ' . $remaining . " remaining.\n";
print_r($log);
