<?php
/**
 * REALITY WRITER DASHBOARD v1.1 — Mobile First
 * Upload to site root.
 * https://getonlinestudio.com/reality-dashboard.php?key=AUTOBOT_778899
 */

define('REALITY_CRON_KEY', 'AUTOBOT_778899');

if (empty($_GET['key']) || $_GET['key'] !== REALITY_CRON_KEY) {
    http_response_code(403); die('Forbidden.');
}

$wp_paths = [__DIR__.'/wp-load.php', __DIR__.'/wp/wp-load.php', dirname(__DIR__).'/wp-load.php'];
foreach ($wp_paths as $p) { if (file_exists($p)) { require_once $p; break; } }

$niche_posts = get_posts(['post_type'=>'pseo_niche','numberposts'=>-1,'post_status'=>'any','orderby'=>'title','order'=>'ASC']);
$total = count($niche_posts);
$done = $missing = 0;
$rows = [];

foreach ($niche_posts as $np) {
    $built_at = get_post_meta($np->ID, '_reality_built_at', true);
    $reality  = get_post_meta($np->ID, 'reality_p1', true);
    $preview  = get_post_meta($np->ID, '_reality_preview', true) ?: [];
    $is_done  = !empty($built_at) && !empty($reality);
    if ($is_done) $done++; else $missing++;
    $rows[] = ['id'=>$np->ID,'name'=>$np->post_title,'slug'=>$np->post_name,'built_at'=>$built_at,'is_done'=>$is_done,'reality'=>$reality,'preview'=>$preview];
}

$pct      = $total > 0 ? round(($done/$total)*100) : 0;
$last_run = get_option('reality_writer_last_run', []);
$triggered = false;

if (!empty($_GET['run'])) {
    wp_remote_get(site_url('/wp/reality-writer.php?key='.REALITY_CRON_KEY), ['timeout'=>1,'blocking'=>false]);
    $triggered = true;
}
?><!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Reality Writer — GO. Studio</title>
<style>
*{box-sizing:border-box;margin:0;padding:0}
body{background:#07070f;color:#e2e8f0;font-family:'Segoe UI',sans-serif;font-size:15px;line-height:1.5;-webkit-text-size-adjust:100%}

/* TOP BAR */
.topbar{background:#0f0f1a;border-bottom:1px solid #1e1e3a;padding:14px 16px;display:flex;justify-content:space-between;align-items:center;position:sticky;top:0;z-index:50}
.topbar h1{font-size:16px;font-weight:800;color:#fff}
.badge{background:#4f46e5;color:#fff;font-size:10px;font-weight:700;padding:2px 7px;border-radius:99px;margin-left:6px;vertical-align:middle}
.topbar .t{font-size:11px;color:#475569}

/* WRAP */
.wrap{max-width:600px;margin:0 auto;padding:16px}

/* ALERT */
.alert{padding:12px 16px;border-radius:10px;font-size:13px;font-weight:600;margin-bottom:16px;border:1px solid}
.alert.ok{background:#052e16;color:#22c55e;border-color:#14532d}

/* STATS GRID */
.stats{display:grid;grid-template-columns:1fr 1fr;gap:10px;margin-bottom:16px}
.stat{background:#0f0f1a;border:1px solid #1e1e3a;border-radius:12px;padding:16px 14px}
.stat .n{font-size:34px;font-weight:800;line-height:1}
.stat .l{font-size:11px;color:#64748b;text-transform:uppercase;letter-spacing:.06em;margin-top:4px}
.g .n{color:#22c55e}.r .n{color:#ef4444}.p .n{color:#a78bfa}

/* PROGRESS */
.card{background:#0f0f1a;border:1px solid #1e1e3a;border-radius:12px;padding:16px;margin-bottom:14px}
.card h3{font-size:11px;color:#64748b;text-transform:uppercase;letter-spacing:.06em;margin-bottom:12px}
.bar-bg{background:#1e1e3a;border-radius:99px;height:12px;overflow:hidden}
.bar-fill{height:12px;border-radius:99px;background:linear-gradient(90deg,#4f46e5,#22c55e)}
.bar-meta{margin-top:8px;font-size:13px;color:#94a3b8}
.bar-meta strong{color:#fff;font-size:20px}

/* LAST RUN */
.run-row{display:flex;flex-wrap:wrap;gap:10px;font-size:13px}
.run-row .val{color:#fff;font-weight:700}
.pill{display:inline-block;padding:2px 9px;border-radius:99px;font-size:11px;font-weight:700}
.pill.success{background:#052e16;color:#22c55e}
.pill.failed{background:#2d0a0a;color:#ef4444}
.pill.complete{background:#0f172a;color:#60a5fa}
.pill.reset{background:#1c1004;color:#f59e0b}

/* LOG */
.tog{font-size:12px;color:#6d28d9;cursor:pointer;margin-top:10px;display:inline-block}
.logbox{display:none;margin-top:8px;background:#06060f;border:1px solid #1a1a2e;border-radius:8px;padding:10px;max-height:180px;overflow-y:auto}
.logbox.open{display:block}
.logline{font-size:11px;font-family:monospace;color:#64748b;padding:2px 0}

/* ACTIONS */
.actions{display:flex;flex-direction:column;gap:8px;margin-bottom:14px}
.btn{display:flex;align-items:center;justify-content:center;gap:6px;padding:13px 16px;border-radius:10px;font-size:14px;font-weight:700;cursor:pointer;border:none;text-decoration:none;text-align:center;-webkit-tap-highlight-color:transparent}
.btn:active{opacity:.7}
.bi{background:#4f46e5;color:#fff}
.br{background:#7f1d1d;color:#fff}
.bs{background:#1e1e3a;color:#94a3b8}

/* FILTERS */
.filters{display:flex;gap:6px;flex-wrap:wrap;margin-bottom:12px}
.fbtn{padding:8px 14px;border-radius:99px;font-size:13px;font-weight:700;cursor:pointer;border:1px solid #1e1e3a;background:#0f0f1a;color:#64748b;-webkit-tap-highlight-color:transparent}
.fbtn.on{background:#4f46e5;color:#fff;border-color:#4f46e5}

/* SEARCH */
.srch{width:100%;background:#0f0f1a;border:1px solid #1e1e3a;color:#fff;padding:12px 14px;border-radius:10px;font-size:14px;outline:none;margin-bottom:12px}
.srch:focus{border-color:#4f46e5}

/* NICHE CARDS */
.niche-list{display:flex;flex-direction:column;gap:8px}
.ncard{background:#0f0f1a;border:1px solid #1e1e3a;border-radius:12px;padding:14px;transition:border-color .2s}
.ncard.done{border-left:3px solid #22c55e}
.ncard.missing{border-left:3px solid #ef4444}
.ncard-top{display:flex;justify-content:space-between;align-items:flex-start;gap:8px}
.nname{font-weight:700;color:#fff;font-size:15px}
.nslug{font-size:11px;color:#334155;font-family:monospace;margin-top:2px}
.sbadge{display:inline-block;padding:3px 10px;border-radius:6px;font-size:11px;font-weight:700;white-space:nowrap;flex-shrink:0}
.sbadge.done{background:#052e16;color:#22c55e}
.sbadge.missing{background:#2d0a0a;color:#ef4444}
.preview{font-size:12px;color:#64748b;margin-top:8px;line-height:1.6}
.pos-tags{display:flex;gap:5px;flex-wrap:wrap;margin-top:8px}
.pos-tag{background:#1e1e3a;color:#a78bfa;font-size:11px;padding:3px 8px;border-radius:5px}
.built-time{font-size:11px;color:#334155;margin-top:8px}
.expand-btn{font-size:12px;color:#4f46e5;cursor:pointer;margin-top:6px;display:inline-block}
.full-reality{display:none;margin-top:8px;background:#06060f;border:1px solid #1a1a2e;border-radius:8px;padding:10px;font-size:12px;color:#94a3b8;line-height:1.7}
.full-reality.open{display:block}

.footer-note{margin-top:20px;font-size:11px;color:#334155;text-align:center;padding-bottom:20px}
</style>
</head>
<body>

<div class="topbar">
  <div><h1>Reality Writer<span class="badge">v1.1</span></h1></div>
  <span class="t"><?= date('d M · H:i') ?></span>
</div>

<div class="wrap">

<?php if ($triggered): ?>
<div class="alert ok">Writer triggered. Refresh in 60 seconds.</div>
<?php endif; ?>

<!-- Stats -->
<div class="stats">
  <div class="stat p"><div class="n"><?= $total ?></div><div class="l">Niches</div></div>
  <div class="stat p"><div class="n"><?= $pct ?>%</div><div class="l">Complete</div></div>
  <div class="stat g"><div class="n"><?= $done ?></div><div class="l">Done</div></div>
  <div class="stat r"><div class="n"><?= $missing ?></div><div class="l">Remaining</div></div>
</div>

<!-- Progress -->
<div class="card">
  <h3>Overall Progress</h3>
  <div class="bar-bg"><div class="bar-fill" style="width:<?= $pct ?>%"></div></div>
  <div class="bar-meta"><strong><?= $done ?></strong> of <?= $total ?> niches have rich national copy</div>
</div>

<!-- Last Run -->
<?php if (!empty($last_run)): ?>
<div class="card">
  <h3>Last Run</h3>
  <div class="run-row">
    <span><?= esc_html($last_run['time'] ?? '—') ?></span>
    <span><span class="pill <?= esc_attr($last_run['status'] ?? 'failed') ?>"><?= strtoupper(esc_html($last_run['status'] ?? '?')) ?></span></span>
    <?php if (!empty($last_run['built'])): ?>
    <span>Wrote: <span class="val"><?= esc_html(implode(', ', $last_run['built'])) ?></span></span>
    <?php endif; ?>
    <?php if (isset($last_run['remaining'])): ?>
    <span>Left: <span class="val"><?= (int)$last_run['remaining'] ?></span></span>
    <?php endif; ?>
  </div>
  <?php if (!empty($last_run['log'])): ?>
  <span class="tog" onclick="tog('run-log')">▶ Show log</span>
  <div class="logbox" id="run-log">
    <?php foreach ($last_run['log'] as $line): ?>
    <div class="logline"><?= esc_html($line) ?></div>
    <?php endforeach; ?>
  </div>
  <?php endif; ?>
</div>
<?php endif; ?>

<!-- Actions -->
<div class="actions">
  <a class="btn bi" href="?key=<?= REALITY_CRON_KEY ?>&run=1">▶ Run Writer Now</a>
  <a class="btn br" href="wp/reality-writer.php?key=<?= REALITY_CRON_KEY ?>&reset=1" onclick="return confirm('Reset all? Every niche will be rewritten.')">🔄 Reset &amp; Rewrite All</a>
  <a class="btn bs" href="javascript:location.reload()">↺ Refresh</a>
</div>

<!-- Search + Filter -->
<input class="srch" type="text" id="srch" placeholder="Search niches..." oninput="filter()">
<div class="filters">
  <div class="fbtn on" onclick="setF('all',this)">All (<?= $total ?>)</div>
  <div class="fbtn" onclick="setF('done',this)">✅ Done (<?= $done ?>)</div>
  <div class="fbtn" onclick="setF('missing',this)">❌ Missing (<?= $missing ?>)</div>
</div>

<!-- Niche Cards -->
<div class="niche-list" id="nlist">
<?php foreach ($rows as $r): ?>
<div class="ncard <?= $r['is_done'] ? 'done' : 'missing' ?>" data-status="<?= $r['is_done'] ? 'done' : 'missing' ?>" data-name="<?= strtolower(esc_attr($r['name'])) ?>">

  <div class="ncard-top">
    <div>
      <div class="nname"><?= esc_html($r['name']) ?></div>
      <div class="nslug"><?= esc_html($r['slug']) ?></div>
    </div>
    <span class="sbadge <?= $r['is_done'] ? 'done' : 'missing' ?>"><?= $r['is_done'] ? '✅ Done' : '❌ Missing' ?></span>
  </div>

  <?php if ($r['is_done'] && !empty($r['preview'])): ?>
    <div class="preview"><?= esc_html($r['preview']['reality_p1'] ?? '') ?></div>
    <?php
    $pos_titles = array_filter([
        $r['preview']['pos1_title'] ?? '',
        $r['preview']['pos2_title'] ?? '',
        $r['preview']['pos3_title'] ?? '',
        $r['preview']['pos4_title'] ?? '',
    ]);
    if (!empty($pos_titles)): ?>
    <div class="pos-tags">
      <?php foreach ($pos_titles as $pt): ?>
      <span class="pos-tag"><?= esc_html($pt) ?></span>
      <?php endforeach; ?>
    </div>
    <?php endif; ?>
    <?php if (!empty($r['reality'])): ?>
    <span class="expand-btn" onclick="tog('r<?= $r['id'] ?>')">▶ Read full reality_p1</span>
    <div class="full-reality" id="r<?= $r['id'] ?>"><?= esc_html($r['reality']) ?></div>
    <?php endif; ?>
    <div class="built-time">Built: <?= date('d M Y H:i', strtotime($r['built_at'])) ?></div>
  <?php else: ?>
    <div class="preview" style="color:#334155">Will be written on next run.</div>
  <?php endif; ?>

</div>
<?php endforeach; ?>
</div>

<div class="footer-note">Cron runs every 10 min · 2 niches per batch<?= $missing > 0 ? ' · Auto-refreshing every 60s' : '' ?></div>

</div>

<script>
var cf = 'all';
function setF(f, btn) {
  cf = f;
  document.querySelectorAll('.fbtn').forEach(b => b.classList.remove('on'));
  btn.classList.add('on');
  filter();
}
function filter() {
  var s = document.getElementById('srch').value.toLowerCase();
  document.querySelectorAll('.ncard').forEach(function(c) {
    var mf = cf === 'all' || c.dataset.status === cf;
    var ms = c.dataset.name.includes(s);
    c.style.display = (mf && ms) ? '' : 'none';
  });
}
function tog(id) {
  var el = document.getElementById(id);
  el.classList.toggle('open');
  var btn = el.previousElementSibling;
  if (btn) btn.textContent = el.classList.contains('open') ? btn.textContent.replace('▶','▼') : btn.textContent.replace('▼','▶');
}
<?php if ($missing > 0): ?>
setTimeout(() => location.reload(), 60000);
<?php endif; ?>
</script>
</body>
</html>
