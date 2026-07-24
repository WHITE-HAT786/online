<?php
require_once __DIR__ . '/../includes/auth_guard.php';

$pageTitle   = 'Call Logs';
$activeMenu  = 'call-logs';
$sipStatus   = 'registered';
$notifCount  = 3;
$breadcrumb  = [
  ['label' => 'Dashboard', 'href' => 'dashboard.php'],
  ['label' => 'Call Logs'],
];
$currentPlan = [
  'name'   => 'Professional',
  'price'  => '$29.00',
  'period' => 'month',
  'renews' => 'Jun 20, 2025',
  'status' => 'active',
];

// Live counters
$uid = auth_user_id();
$r = db()->prepare("SELECT COUNT(*) c,
    SUM(direction='outgoing') o, SUM(direction='incoming') i,
    SUM(direction='missed') m, COALESCE(SUM(duration_sec),0) d
  FROM pkg_call WHERE user_id=?");
$r->execute([$uid]); $stats = $r->fetch();

// Fetch logs (last 10)
$rows = db()->prepare("SELECT c.*, s.account_name AS sip_name FROM pkg_call c
  LEFT JOIN pkg_sip s ON s.id=c.sip_id
  WHERE c.user_id=? ORDER BY c.started_at DESC LIMIT 10");
$rows->execute([$uid]);
$dbLogs = $rows->fetchAll();

$logs = array_map(function($r){
  return [
    'dt'       => date('M j, Y g:i:s A', strtotime($r['started_at'])),
    'type'     => $r['direction'],
    'from'     => $r['from_number'],
    'to'       => $r['to_number'],
    'account'  => $r['sip_name'] ?? '—',
    'duration' => fmt_duration((int)$r['duration_sec']),
    'status'   => $r['status'],
    'rec'      => !empty($r['recording_url']),
  ];
}, $dbLogs);

include __DIR__ . '/../includes/header.php';
?>

<!-- ============ Header ============ -->
<div class="calllog-head">
  <div class="calllog-title">Call Logs</div>
  <div class="calllog-sub">View and analyze your call history.</div>
</div>

<!-- ============ Stats row ============ -->
<section class="calllog-stats">
  <div class="cl-stat">
    <div class="cl-stat-icon blue"><i class="fa-solid fa-phone"></i></div>
    <div>
      <div class="cl-stat-label">Total Calls</div>
      <div class="cl-stat-value">1,250</div>
      <div class="cl-stat-delta"><i class="fa-solid fa-arrow-up"></i> 12.5% <span>vs Apr 13 - Apr 19</span></div>
    </div>
  </div>

  <div class="cl-stat">
    <div class="cl-stat-icon green"><i class="fa-solid fa-arrow-up-right"></i></div>
    <div>
      <div class="cl-stat-label">Outgoing Calls</div>
      <div class="cl-stat-value">780</div>
      <div class="cl-stat-delta"><i class="fa-solid fa-arrow-up"></i> 10.3% <span>vs Apr 13 - Apr 19</span></div>
    </div>
  </div>

  <div class="cl-stat">
    <div class="cl-stat-icon purple"><i class="fa-solid fa-arrow-down"></i></div>
    <div>
      <div class="cl-stat-label">Incoming Calls</div>
      <div class="cl-stat-value">420</div>
      <div class="cl-stat-delta"><i class="fa-solid fa-arrow-up"></i> 15.2% <span>vs Apr 13 - Apr 19</span></div>
    </div>
  </div>

  <div class="cl-stat">
    <div class="cl-stat-icon orange"><i class="fa-solid fa-xmark"></i></div>
    <div>
      <div class="cl-stat-label">Missed Calls</div>
      <div class="cl-stat-value">50</div>
      <div class="cl-stat-delta down"><i class="fa-solid fa-arrow-down"></i> 8.7% <span>vs Apr 13 - Apr 19</span></div>
    </div>
  </div>

  <div class="cl-stat">
    <div class="cl-stat-icon sky"><i class="fa-regular fa-clock"></i></div>
    <div>
      <div class="cl-stat-label">Total Duration</div>
      <div class="cl-stat-value">18:42:36</div>
      <div class="cl-stat-delta"><i class="fa-solid fa-arrow-up"></i> 11.8% <span>vs Apr 13 - Apr 19</span></div>
    </div>
  </div>
</section>

<!-- ============ Main card ============ -->
<div class="card calllog-card">
  <!-- Toolbar row 1: search + filters/export -->
  <div class="cl-toolbar">
    <div class="cl-search">
      <input type="text" placeholder="Search by number, name or SIP account..." />
      <button aria-label="Search"><i class="fa-solid fa-magnifying-glass"></i></button>
    </div>
    <div class="cl-toolbar-actions">
      <button class="btn-outline">
        <i class="fa-solid fa-filter"></i> Filters
      </button>
      <button class="btn-outline">
        <i class="fa-solid fa-arrow-down-to-bracket"></i> Export
      </button>
    </div>
  </div>

  <!-- Toolbar row 2: filters -->
  <div class="cl-filters">
    <div class="cl-select cl-select-lg">
      <i class="fa-regular fa-calendar left-ic"></i>
      <select>
        <option>May 13, 2025 - May 20, 2025</option>
        <option>Last 7 days</option>
        <option>Last 30 days</option>
        <option>This month</option>
        <option>Custom range</option>
      </select>
      <i class="fa-solid fa-chevron-down caret"></i>
    </div>

    <div class="cl-select">
      <select>
        <option>All Call Types</option>
        <option>Outgoing</option>
        <option>Incoming</option>
        <option>Missed</option>
      </select>
      <i class="fa-solid fa-chevron-down caret"></i>
    </div>

    <div class="cl-select">
      <select>
        <option>All SIP Accounts</option>
        <option>Primary Account</option>
        <option>Sales Account</option>
        <option>Support Account</option>
      </select>
      <i class="fa-solid fa-chevron-down caret"></i>
    </div>

    <div class="cl-select">
      <select>
        <option>All Status</option>
        <option>Completed</option>
        <option>Missed</option>
        <option>Failed</option>
      </select>
      <i class="fa-solid fa-chevron-down caret"></i>
    </div>
  </div>

  <!-- Table -->
  <div class="cl-table-wrap">
    <table class="cl-table">
      <thead>
        <tr>
          <th class="cl-col-num">#</th>
          <th>Date &amp; Time</th>
          <th>Type</th>
          <th>From</th>
          <th>To</th>
          <th>SIP Account</th>
          <th>Duration</th>
          <th>Status</th>
          <th class="cl-col-center">Recording</th>
          <th class="cl-col-center">Actions</th>
        </tr>
      </thead>
      <tbody>
        <?php foreach ($logs as $i => $l): ?>
        <tr>
          <td class="cl-col-num"><?= $i + 1 ?></td>
          <td class="cl-date"><?= htmlspecialchars($l['dt']) ?></td>
          <td>
            <?php if ($l['type'] === 'outgoing'): ?>
              <span class="cl-type out"><i class="fa-solid fa-arrow-up-right"></i> Outgoing</span>
            <?php elseif ($l['type'] === 'incoming'): ?>
              <span class="cl-type in"><i class="fa-solid fa-arrow-down"></i> Incoming</span>
            <?php else: ?>
              <span class="cl-type miss"><i class="fa-solid fa-xmark"></i> Missed</span>
            <?php endif; ?>
          </td>
          <td><?= htmlspecialchars($l['from']) ?></td>
          <td><?= htmlspecialchars($l['to']) ?></td>
          <td><?= htmlspecialchars($l['account']) ?></td>
          <td><?= htmlspecialchars($l['duration']) ?></td>
          <td>
            <?php if ($l['status'] === 'completed'): ?>
              <span class="cl-status done">Completed</span>
            <?php elseif ($l['status'] === 'missed'): ?>
              <span class="cl-status miss">Missed</span>
            <?php else: ?>
              <span class="cl-status fail">Failed</span>
            <?php endif; ?>
          </td>
          <td class="cl-col-center">
            <?php if ($l['rec']): ?>
              <button class="cl-play" title="Play recording"><i class="fa-solid fa-play"></i></button>
            <?php else: ?>
              <span class="cl-dash">—</span>
            <?php endif; ?>
          </td>
          <td class="cl-col-center">
            <button class="cl-more" title="More actions"><i class="fa-solid fa-ellipsis-vertical"></i></button>
          </td>
        </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  </div>

  <!-- Footer / pagination -->
  <div class="cl-footer">
    <div class="cl-info">Showing 1 to 10 of 1,250 entries</div>
    <nav class="cl-pagination">
      <button class="page-btn"><i class="fa-solid fa-chevron-left"></i></button>
      <button class="page-btn active">1</button>
      <button class="page-btn">2</button>
      <button class="page-btn">3</button>
      <span class="page-ellipsis">...</span>
      <button class="page-btn">125</button>
      <button class="page-btn"><i class="fa-solid fa-chevron-right"></i></button>
    </nav>
    <div class="cl-perpage">
      <div class="toolbar-select small">
        <select>
          <option>10 per page</option>
          <option>25 per page</option>
          <option>50 per page</option>
          <option>100 per page</option>
        </select>
        <i class="fa-solid fa-chevron-down caret"></i>
      </div>
    </div>
  </div>
</div>

<?php include __DIR__ . '/../includes/footer.php'; ?>
