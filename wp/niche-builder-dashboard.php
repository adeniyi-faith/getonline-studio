<?php
/**
 * NICHE AUTOBUILDER DASHBOARD
 * Place at: /niche-builder-dashboard.php
 * Access at: https://getonlinestudio.com/niche-builder-dashboard.php?key=AUTOBOT_778899
 */

define('PSEO_CRON_KEY', 'AUTOBOT_778899');

if (!isset($_GET['key']) || $_GET['key'] !== PSEO_CRON_KEY) {
    http_response_code(403);
    die('Forbidden.');
}

$wp_paths = [__DIR__ . '/wp-load.php', __DIR__ . '/wp/wp-load.php', dirname(__DIR__) . '/wp-load.php'];
foreach ($wp_paths as $path) {
    if (file_exists($path)) { require_once($path); break; }
}

$service_formats = [
    'website-designer', 'website-developer',
    'web-design-agency', 'website-design-services', 'branding-agency'
];

// Load all niches
$niche_posts = get_posts([
    'post_type'   => 'pseo_niche',
    'numberposts' => -1,
    'post_status' => 'any',
    'orderby'     => 'title',
    'order'       => 'ASC'
]);

// Count totals
$total_combos   = count($niche_posts) * count($service_formats);
$total_done     = 0;
$total_stale    = 0;
$total_missing  = 0;
$refresh_cutoff = date('Y-m-d H:i:s', strtotime('-60 days'));

$niche_rows = [];
foreach ($niche_posts as $np) {
    $row = [
        'name'    => $np->post_title,
        'slug'    => $np->post_name,
        'id'      => $np->ID,
        'formats' => [],
        'log'     => get_post_meta($np->ID, '_pseo_build_log', true) ?: [],
    ];
    foreach ($service_formats as $fmt) {
        $themes    = get_post_meta($np->ID, $fmt . '_ai_themes', true);
        $built_at  = get_post_meta($np->ID, $fmt . '_built_at', true);
        $reality   = get_post_meta($np->ID, 'reality_p1', true); // individual key check
        $feat      = get_post_meta($np->ID, $fmt . '_feat_headline', true);

        $has_blob       = !empty($themes) && count((array)$themes) >= 5;
        $has_individual = !empty($reality);
        $has_core       = !empty($feat);
        $is_stale       = $has_blob && !empty($built_at) && $built_at < $refresh_cutoff;

        if ($has_blob && $has_individual && $has_core) {
            $status = 'done';
            $total_done++;
        } elseif ($has_blob && !$has_individual) {
            $status = 'old'; // built before v3, has blob but no individual keys
            $total_stale++;
        } elseif ($is_stale) {
            $status = 'stale';
            $total_stale++;
        } else {
            $status = 'missing';
            $total_missing++;
        }

        $row['formats'][$fmt] = [
            'status'         => $status,
            'built_at'       => $built_at ?: null,
            'has_blob'       => $has_blob,
            'has_individual' => $has_individual,
            'has_core'       => $has_core,
        ];
    }
    $niche_rows[] = $row;
}

$last_run = get_option('pseo_builder_last_run', []);
$pct_done = $total_combos > 0 ? round(($total_done / $total_combos) * 100) : 0;

?><!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Niche Autobuilder Dashboard — GetOnline Studio</title>
<style>
  * { box-sizing: border-box; margin: 0; padding: 0; }
  body { background: #0a0a0a; color: #e2e8f0; font-family: 'Segoe UI', sans-serif; font-size: 14px; }
  a { color: #a78bfa; text-decoration: none; }

  .top-bar { background: #111; border-bottom: 1px solid #1e1e2e; padding: 16px 24px; display: flex; justify-content: space-between; align-items: center; position: sticky; top: 0; z-index: 100; }
  .top-bar h1 { font-size: 18px; font-weight: 700; color: #fff; }
  .top-bar .tag { font-size: 11px; background: #7c3aed; color: #fff; padding: 3px 10px; border-radius: 99px; font-weight: 700; margin-left: 10px; }

  .container { max-width: 1400px; margin: 0 auto; padding: 24px; }

  /* Stats row */
  .stats { display: grid; grid-template-columns: repeat(auto-fit, minmax(160px, 1fr)); gap: 16px; margin-bottom: 28px; }
  .stat-card { background: #111; border: 1px solid #1e1e2e; border-radius: 12px; padding: 20px; }
  .stat-card .num { font-size: 32px; font-weight: 800; }
  .stat-card .lbl { font-size: 11px; color: #64748b; text-transform: uppercase; letter-spacing: .08em; margin-top: 4px; }
  .stat-card.green .num { color: #22c55e; }
  .stat-card.yellow .num { color: #eab308; }
  .stat-card.red .num { color: #ef4444; }
  .stat-card.purple .num { color: #a78bfa; }

  /* Progress bar */
  .progress-wrap { background: #111; border: 1px solid #1e1e2e; border-radius: 12px; padding: 20px; margin-bottom: 28px; }
  .progress-wrap h3 { font-size: 13px; color: #94a3b8; margin-bottom: 12px; }
  .bar-bg { background: #1e1e2e; border-radius: 99px; height: 12px; }
  .bar-fill { background: linear-gradient(90deg, #7c3aed, #22c55e); border-radius: 99px; height: 12px; transition: width .5s; }
  .bar-label { margin-top: 8px; font-size: 13px; color: #94a3b8; }
  .bar-label strong { color: #fff; }

  /* Last run panel */
  .last-run { background: #111; border: 1px solid #1e1e2e; border-radius: 12px; padding: 20px; margin-bottom: 28px; }
  .last-run h3 { font-size: 13px; color: #94a3b8; margin-bottom: 12px; }
  .last-run .run-meta { display: flex; gap: 24px; flex-wrap: wrap; }
  .run-meta span { font-size: 13px; }
  .run-meta .val { color: #fff; font-weight: 600; }
  .status-pill { display: inline-block; padding: 2px 10px; border-radius: 99px; font-size: 11px; font-weight: 700; }
  .status-pill.success { background: #14532d; color: #22c55e; }
  .status-pill.failed  { background: #450a0a; color: #ef4444; }
  .status-pill.complete { background: #1e3a5f; color: #60a5fa; }

  /* Actions */
  .actions { display: flex; gap: 12px; margin-bottom: 28px; flex-wrap: wrap; }
  .btn { display: inline-flex; align-items: center; gap: 6px; padding: 10px 20px; border-radius: 8px; font-size: 13px; font-weight: 700; cursor: pointer; border: none; text-decoration: none; }
  .btn-purple { background: #7c3aed; color: #fff; }
  .btn-red    { background: #991b1b; color: #fff; }
  .btn-dark   { background: #1e1e2e; color: #94a3b8; border: 1px solid #2d2d3e; }
  .btn:hover { opacity: .85; }

  /* Search */
  .search-wrap { margin-bottom: 16px; }
  .search-wrap input { width: 100%; max-width: 400px; background: #111; border: 1px solid #1e1e2e; color: #fff; padding: 10px 16px; border-radius: 8px; font-size: 14px; outline: none; }
  .search-wrap input:focus { border-color: #7c3aed; }

  /* Filter tabs */
  .filters { display: flex; gap: 8px; margin-bottom: 16px; flex-wrap: wrap; }
  .filter-btn { padding: 6px 14px; border-radius: 99px; font-size: 12px; font-weight: 700; cursor: pointer; border: 1px solid #1e1e2e; background: #111; color: #64748b; }
  .filter-btn.active, .filter-btn:hover { background: #7c3aed; color: #fff; border-color: #7c3aed; }

  /* Table */
  .table-wrap { background: #111; border: 1px solid #1e1e2e; border-radius: 12px; overflow: hidden; }
  table { width: 100%; border-collapse: collapse; }
  thead { background: #0d0d1a; }
  thead th { padding: 12px 16px; text-align: left; font-size: 11px; text-transform: uppercase; letter-spacing: .08em; color: #64748b; font-weight: 700; white-space: nowrap; }
  tbody tr { border-top: 1px solid #1a1a2e; transition: background .15s; }
  tbody tr:hover { background: #13131f; }
  tbody td { padding: 12px 16px; vertical-align: middle; }
  .niche-name { font-weight: 700; color: #fff; }
  .niche-slug { font-size: 11px; color: #475569; font-family: monospace; }

  /* Format pills */
  .fmt-pills { display: flex; gap: 6px; flex-wrap: wrap; }
  .fmt-pill { font-size: 10px; font-weight: 700; padding: 3px 8px; border-radius: 6px; white-space: nowrap; }
  .fmt-pill.done    { background: #14532d; color: #22c55e; }
  .fmt-pill.old     { background: #3b1f00; color: #fb923c; }
  .fmt-pill.stale   { background: #422006; color: #eab308; }
  .fmt-pill.missing { background: #1a0a0a; color: #ef4444; border: 1px solid #3f1212; }

  /* Individual key check */
  .key-check { font-size: 12px; }
  .key-check .yes { color: #22c55e; }
  .key-check .no  { color: #ef4444; }

  /* Build log */
  .log-toggle { font-size: 11px; color: #7c3aed; cursor: pointer; }
  .log-entries { display: none; margin-top: 8px; }
  .log-entries.open { display: block; }
  .log-entry { font-size: 11px; color: #64748b; padding: 3px 0; border-bottom: 1px solid #1a1a2e; }
  .log-entry:last-child { border: none; }

  /* Log modal */
  .log-modal { display:none; position:fixed; inset:0; background:rgba(0,0,0,.8); z-index:999; overflow-y:auto; padding:40px 16px; }
  .log-modal.open { display:block; }
  .log-box { background:#111; border:1px solid #1e1e2e; border-radius:12px; max-width:700px; margin:0 auto; padding:24px; }
  .log-box h3 { color:#fff; margin-bottom:16px; }
  .log-line { font-size:12px; font-family:monospace; color:#94a3b8; padding:3px 0; border-bottom:1px solid #1a1a2e; }
  .log-close { float:right; background:#7c3aed; color:#fff; border:none; padding:6px 14px; border-radius:6px; cursor:pointer; font-weight:700; }

  @media(max-width:640px) {
    .stats { grid-template-columns: 1fr 1fr; }
    .fmt-pills { gap: 4px; }
  }
</style>
</head>
<body>

<div class="top-bar">
  <div style="display:flex;align-items:center;gap:8px;">
    <h1>GO. Niche Autobuilder</h1>
    <span class="tag">v3.0</span>
  </div>
  <span style="font-size:12px;color:#475569;">Dashboard · <?= date('D, d M Y H:i') ?></span>
</div>

<div class="container">

  <!-- Stats -->
  <div class="stats">
    <div class="stat-card purple">
      <div class="num"><?= count($niche_posts) ?></div>
      <div class="lbl">Total Niches</div>
    </div>
    <div class="stat-card purple">
      <div class="num"><?= $total_combos ?></div>
      <div class="lbl">Total Combos</div>
    </div>
    <div class="stat-card green">
      <div class="num"><?= $total_done ?></div>
      <div class="lbl">Fully Built (v3)</div>
    </div>
    <div class="stat-card yellow">
      <div class="num"><?= $total_stale ?></div>
      <div class="lbl">Old / Needs Rebuild</div>
    </div>
    <div class="stat-card red">
      <div class="num"><?= $total_missing ?></div>
      <div class="lbl">Not Built Yet</div>
    </div>
  </div>

  <!-- Progress -->
  <div class="progress-wrap">
    <h3>Overall Progress</h3>
    <div class="bar-bg"><div class="bar-fill" style="width:<?= $pct_done ?>%"></div></div>
    <div class="bar-label"><strong><?= $pct_done ?>%</strong> complete — <?= $total_done ?> of <?= $total_combos ?> combos fully built with v3 dual-save</div>
  </div>

  <!-- Last Run -->
  <?php if (!empty($last_run)): ?>
  <div class="last-run">
    <h3>Last Cron Run</h3>
    <div class="run-meta">
      <span>Time: <span class="val"><?= esc_html($last_run['time'] ?? '—') ?></span></span>
      <span>Status:
        <span class="status-pill <?= esc_attr($last_run['status'] ?? 'failed') ?>">
          <?= strtoupper(esc_html($last_run['status'] ?? 'unknown')) ?>
        </span>
      </span>
      <span>Queue New: <span class="val"><?= (int)($last_run['queue_new'] ?? 0) ?></span></span>
      <span>Queue Stale: <span class="val"><?= (int)($last_run['queue_stale'] ?? 0) ?></span></span>
      <?php if (!empty($last_run['built'])): ?>
      <span>Built: <span class="val"><?= implode(', ', array_map('esc_html', $last_run['built'])) ?></span></span>
      <?php endif; ?>
    </div>
    <?php if (!empty($last_run['log'])): ?>
    <div style="margin-top:12px;">
      <span class="log-toggle" onclick="toggleLog('last-run-log')">▶ Show execution log</span>
      <div class="log-entries" id="last-run-log">
        <?php foreach ($last_run['log'] as $line): ?>
        <div class="log-line"><?= esc_html($line) ?></div>
        <?php endforeach; ?>
      </div>
    </div>
    <?php endif; ?>
  </div>
  <?php endif; ?>

  <!-- Actions -->
  <div class="actions">
    <a class="btn btn-purple" href="?key=<?= PSEO_CRON_KEY ?>&run=1" onclick="return confirm('Run autobuilder now?')">▶ Run Autobuilder Now</a>
    <a class="btn btn-red" href="niche-autobuilder-v3.php?key=<?= PSEO_CRON_KEY ?>&reset=1" onclick="return confirm('This resets ALL build timestamps. Every niche will be rebuilt on next cron runs. Are you sure?')">🔄 Reset All &amp; Rebuild</a>
    <a class="btn btn-dark" href="javascript:location.reload()">↺ Refresh</a>
  </div>

  <?php
  // Manual run trigger from dashboard
  if (!empty($_GET['run']) && $_GET['run'] === '1') {
      echo '<div style="background:#14532d;color:#22c55e;padding:12px 16px;border-radius:8px;margin-bottom:20px;font-size:13px;">✅ Autobuilder triggered. Refresh in 60 seconds to see results.</div>';
      // Trigger via non-blocking HTTP call
      wp_remote_get(site_url('/wp/niche-autobuilder-v3.php?key=' . PSEO_CRON_KEY), ['timeout' => 1, 'blocking' => false]);
  }
  ?>

  <!-- Filter + Search -->
  <div class="search-wrap">
    <input type="text" id="niche-search" placeholder="Search niches..." onkeyup="filterTable()">
  </div>
  <div class="filters">
    <div class="filter-btn active" onclick="setFilter('all', this)">All</div>
    <div class="filter-btn" onclick="setFilter('missing', this)">❌ Not Built</div>
    <div class="filter-btn" onclick="setFilter('old', this)">🟠 Old (Pre-v3)</div>
    <div class="filter-btn" onclick="setFilter('stale', this)">🟡 Stale</div>
    <div class="filter-btn" onclick="setFilter('done', this)">✅ Done</div>
  </div>

  <!-- Table -->
  <div class="table-wrap">
    <table id="niche-table">
      <thead>
        <tr>
          <th>Niche</th>
          <th>Individual Keys<br><span style="font-weight:400;text-transform:none;letter-spacing:0;">(reality_p1, pos1)</span></th>
          <th>Formats Status</th>
          <th>Last Built</th>
          <th>Build Log</th>
        </tr>
      </thead>
      <tbody>
        <?php foreach ($niche_rows as $row):
          // Determine worst status across formats for filtering
          $statuses = array_column($row['formats'], 'status');
          $row_status = in_array('missing', $statuses) ? 'missing' : (in_array('old', $statuses) ? 'old' : (in_array('stale', $statuses) ? 'stale' : 'done'));
          $has_individual = get_post_meta($row['id'], 'reality_p1', true);
          $latest_built = '';
          foreach ($row['formats'] as $fdata) {
              if (!empty($fdata['built_at']) && $fdata['built_at'] > $latest_built) $latest_built = $fdata['built_at'];
          }
        ?>
        <tr data-status="<?= $row_status ?>" data-name="<?= strtolower($row['name']) ?>">
          <td>
            <div class="niche-name"><?= esc_html($row['name']) ?></div>
            <div class="niche-slug"><?= esc_html($row['slug']) ?></div>
          </td>
          <td class="key-check">
            <?php if ($has_individual): ?>
              <span class="yes">✅ Saved</span>
            <?php else: ?>
              <span class="no">❌ Missing</span>
            <?php endif; ?>
          </td>
          <td>
            <div class="fmt-pills">
              <?php foreach ($row['formats'] as $fmt => $fdata):
                $short = str_replace(['website-', 'web-design-', '-agency', '-services', 'branding'], ['', '', '', '', 'brand'], $fmt);
              ?>
              <span class="fmt-pill <?= $fdata['status'] ?>" title="<?= esc_attr($fmt) ?>"><?= esc_html($short) ?></span>
              <?php endforeach; ?>
            </div>
          </td>
          <td style="font-size:12px;color:#64748b;white-space:nowrap;">
            <?= $latest_built ? date('d M Y H:i', strtotime($latest_built)) : '<span style="color:#ef4444">Never</span>' ?>
          </td>
          <td>
            <?php if (!empty($row['log'])): ?>
            <span class="log-toggle" onclick="toggleLog('log-<?= $row['id'] ?>')">▶ <?= count($row['log']) ?> entries</span>
            <div class="log-entries" id="log-<?= $row['id'] ?>">
              <?php foreach ($row['log'] as $entry): ?>
              <div class="log-entry">
                <?= esc_html(date('d M H:i', strtotime($entry['time']))) ?> —
                <?= esc_html($entry['format']) ?> —
                <?= esc_html($entry['reason']) ?> —
                <?= (int)$entry['themes'] ?> themes, <?= (int)$entry['keys'] ?> keys
              </div>
              <?php endforeach; ?>
            </div>
            <?php else: ?>
            <span style="color:#334155;font-size:11px;">No history</span>
            <?php endif; ?>
          </td>
        </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  </div>

  <!-- Legend -->
  <div style="margin-top:20px;display:flex;gap:16px;flex-wrap:wrap;font-size:12px;color:#64748b;">
    <span><span class="fmt-pill done">done</span> Fully built with v3 (individual keys saved)</span>
    <span><span class="fmt-pill old">old</span> Built before v3 — has blob but no individual keys</span>
    <span><span class="fmt-pill stale">stale</span> Built but older than 60 days</span>
    <span><span class="fmt-pill missing">missing</span> Not built yet</span>
  </div>

</div>

<script>
var currentFilter = 'all';

function setFilter(status, btn) {
  currentFilter = status;
  document.querySelectorAll('.filter-btn').forEach(b => b.classList.remove('active'));
  btn.classList.add('active');
  filterTable();
}

function filterTable() {
  var search = document.getElementById('niche-search').value.toLowerCase();
  document.querySelectorAll('#niche-table tbody tr').forEach(function(row) {
    var matchFilter = currentFilter === 'all' || row.dataset.status === currentFilter;
    var matchSearch = row.dataset.name.includes(search);
    row.style.display = (matchFilter && matchSearch) ? '' : 'none';
  });
}

function toggleLog(id) {
  var el = document.getElementById(id);
  el.classList.toggle('open');
  var btn = el.previousElementSibling;
  btn.textContent = el.classList.contains('open')
    ? btn.textContent.replace('▶', '▼')
    : btn.textContent.replace('▼', '▶');
}

// Auto-refresh every 90 seconds if there are missing/old niches
<?php if ($total_missing > 0 || $total_stale > 0): ?>
setTimeout(function() { location.reload(); }, 90000);
<?php endif; ?>
</script>

</body>
</html>
