<?php
require_once __DIR__ . '/../includes/auth_guard.php';

$pageTitle   = 'Reports';
$activeMenu  = 'reports';
$sipStatus   = 'registered';
$notifCount  = 3;
$breadcrumb  = [
  ['label' => 'Dashboard', 'href' => 'dashboard.php'],
  ['label' => 'Reports'],
];

$summaryRows = [
  ['date'=>'May 20, 2025','total'=>180,'out'=>110,'in'=>60,'miss'=>10,'dur'=>'02:45:12','avg'=>'00:00:55'],
  ['date'=>'May 19, 2025','total'=>165,'out'=>105,'in'=>55,'miss'=>5, 'dur'=>'02:30:18','avg'=>'00:00:54'],
  ['date'=>'May 18, 2025','total'=>150,'out'=>90, 'in'=>55,'miss'=>5, 'dur'=>'02:10:22','avg'=>'00:00:52'],
  ['date'=>'May 17, 2025','total'=>140,'out'=>85, 'in'=>50,'miss'=>5, 'dur'=>'02:05:10','avg'=>'00:00:53'],
  ['date'=>'May 16, 2025','total'=>160,'out'=>100,'in'=>55,'miss'=>5, 'dur'=>'02:33:45','avg'=>'00:00:58'],
  ['date'=>'May 15, 2025','total'=>155,'out'=>95, 'in'=>55,'miss'=>5, 'dur'=>'02:25:30','avg'=>'00:00:56'],
  ['date'=>'May 14, 2025','total'=>145,'out'=>90, 'in'=>50,'miss'=>5, 'dur'=>'02:18:40','avg'=>'00:00:55'],
  ['date'=>'May 13, 2025','total'=>155,'out'=>105,'in'=>45,'miss'=>5, 'dur'=>'02:28:39','avg'=>'00:00:57'],
];

$topSip = [
  ['name'=>'Twilio Account',    'calls'=>520,'pct'=>41.6,'color'=>'green'],
  ['name'=>'Telnyx Account',    'calls'=>380,'pct'=>30.4,'color'=>'blue'],
  ['name'=>'Bandwidth Account', 'calls'=>210,'pct'=>16.8,'color'=>'purple'],
  ['name'=>'Plivo Account',     'calls'=>90, 'pct'=>7.2, 'color'=>'orange'],
  ['name'=>'Other Accounts',    'calls'=>50, 'pct'=>4.0, 'color'=>'yellow'],
];

include __DIR__ . '/../includes/header.php';
?>

<!-- ============ Header ============ -->
<div class="reports-head">
  <div>
    <div class="reports-title">Reports</div>
    <div class="reports-sub">Analyze your call activity and performance.</div>
  </div>
  <div class="reports-head-actions">
    <div class="cl-select cl-select-lg" style="width:280px;">
      <i class="fa-regular fa-calendar left-ic"></i>
      <select>
        <option>May 13, 2025 - May 20, 2025</option>
        <option>Last 7 days</option>
        <option>Last 30 days</option>
        <option>This month</option>
      </select>
      <i class="fa-solid fa-chevron-down caret"></i>
    </div>
    <button class="btn-outline">
      <i class="fa-solid fa-filter"></i> Filters
    </button>
  </div>
</div>

<!-- ============ Tabs ============ -->
<div class="reports-tabs">
  <button class="reports-tab active">Overview</button>
  <button class="reports-tab">Call Reports</button>
  <button class="reports-tab">SIP Reports</button>
  <button class="reports-tab">User Reports</button>
  <button class="reports-tab">Performance</button>
  <button class="reports-tab">Downloads</button>
</div>

<!-- ============ Stats ============ -->
<section class="calllog-stats">
  <div class="cl-stat">
    <div class="cl-stat-icon blue"><i class="fa-solid fa-phone"></i></div>
    <div>
      <div class="cl-stat-label">Total Calls</div>
      <div class="cl-stat-value">1,250</div>
      <div class="cl-stat-delta"><i class="fa-solid fa-arrow-up"></i> 12.5% <span>vs Apr 13 - Apr 20</span></div>
    </div>
  </div>
  <div class="cl-stat">
    <div class="cl-stat-icon green"><i class="fa-solid fa-arrow-up-right"></i></div>
    <div>
      <div class="cl-stat-label">Outgoing Calls</div>
      <div class="cl-stat-value">780</div>
      <div class="cl-stat-delta"><i class="fa-solid fa-arrow-up"></i> 10.3% <span>vs Apr 13 - Apr 20</span></div>
    </div>
  </div>
  <div class="cl-stat">
    <div class="cl-stat-icon purple"><i class="fa-solid fa-arrow-down"></i></div>
    <div>
      <div class="cl-stat-label">Incoming Calls</div>
      <div class="cl-stat-value">420</div>
      <div class="cl-stat-delta"><i class="fa-solid fa-arrow-up"></i> 15.2% <span>vs Apr 13 - Apr 20</span></div>
    </div>
  </div>
  <div class="cl-stat">
    <div class="cl-stat-icon orange"><i class="fa-solid fa-xmark"></i></div>
    <div>
      <div class="cl-stat-label">Missed Calls</div>
      <div class="cl-stat-value">50</div>
      <div class="cl-stat-delta down"><i class="fa-solid fa-arrow-down"></i> 8.7% <span>vs Apr 13 - Apr 20</span></div>
    </div>
  </div>
  <div class="cl-stat">
    <div class="cl-stat-icon sky"><i class="fa-regular fa-clock"></i></div>
    <div>
      <div class="cl-stat-label">Total Duration</div>
      <div class="cl-stat-value">18:42:36</div>
      <div class="cl-stat-delta"><i class="fa-solid fa-arrow-up"></i> 11.8% <span>vs Apr 13 - Apr 20</span></div>
    </div>
  </div>
</section>

<!-- ============ Charts row ============ -->
<section class="reports-row">
  <!-- Call Activity -->
  <div class="card">
    <div class="chart-head">
      <div class="card-title">Call Activity</div>
      <div class="legend">
        <span><span class="swatch g"></span> Outgoing</span>
        <span><span class="swatch b"></span> Incoming</span>
        <span><span class="swatch r"></span> Missed</span>
      </div>
      <select class="select-inline">
        <option>This Week</option><option>Last Week</option><option>This Month</option>
      </select>
    </div>

    <div class="chart-wrap">
      <svg viewBox="0 0 700 320" width="100%" height="320" preserveAspectRatio="none">
        <defs>
          <linearGradient id="rGreen" x1="0" x2="0" y1="0" y2="1">
            <stop offset="0%"  stop-color="#10b981" stop-opacity=".22"/>
            <stop offset="100%" stop-color="#10b981" stop-opacity="0"/>
          </linearGradient>
        </defs>

        <!-- gridlines -->
        <g stroke="#eef2f9" stroke-width="1">
          <line x1="40" y1="40"  x2="690" y2="40"/>
          <line x1="40" y1="100" x2="690" y2="100"/>
          <line x1="40" y1="160" x2="690" y2="160"/>
          <line x1="40" y1="220" x2="690" y2="220"/>
          <line x1="40" y1="270" x2="690" y2="270"/>
        </g>
        <g fill="#8b93a7" font-size="11" font-family="Inter">
          <text x="10" y="44">200</text>
          <text x="10" y="104">150</text>
          <text x="10" y="164">100</text>
          <text x="10" y="224">50</text>
          <text x="18" y="284">0</text>
        </g>

        <!-- Bars for Incoming (blue) & Missed (red) at each x -->
        <!-- May 13 -->
        <rect x="80"  y="200" width="16" height="70" fill="#dbeafe"/>
        <rect x="100" y="255" width="10" height="15" fill="#fca5a5"/>
        <!-- May 14 -->
        <rect x="168" y="205" width="16" height="65" fill="#dbeafe"/>
        <rect x="188" y="253" width="10" height="17" fill="#fca5a5"/>
        <!-- May 15 -->
        <rect x="256" y="200" width="16" height="70" fill="#dbeafe"/>
        <rect x="276" y="255" width="10" height="15" fill="#fca5a5"/>
        <!-- May 16 -->
        <rect x="344" y="200" width="16" height="70" fill="#dbeafe"/>
        <rect x="364" y="255" width="10" height="15" fill="#fca5a5"/>
        <!-- May 17 -->
        <rect x="432" y="205" width="16" height="65" fill="#dbeafe"/>
        <rect x="452" y="253" width="10" height="17" fill="#fca5a5"/>
        <!-- May 18 -->
        <rect x="520" y="200" width="16" height="70" fill="#dbeafe"/>
        <rect x="540" y="255" width="10" height="15" fill="#fca5a5"/>
        <!-- May 19 -->
        <rect x="608" y="205" width="16" height="65" fill="#dbeafe"/>
        <rect x="628" y="253" width="10" height="17" fill="#fca5a5"/>

        <!-- Outgoing area + line (green) -->
        <path d="M90,140 L180,150 L270,138 L360,145 L450,130 L540,135 L630,138 L630,270 L90,270 Z"
              fill="url(#rGreen)"/>
        <path d="M90,140 L180,150 L270,138 L360,145 L450,130 L540,135 L630,138"
              fill="none" stroke="#10b981" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"/>

        <!-- Outgoing points (with white ring) -->
        <g>
          <circle cx="90"  cy="140" r="5" fill="#fff" stroke="#10b981" stroke-width="2.5"/>
          <circle cx="180" cy="150" r="5" fill="#fff" stroke="#10b981" stroke-width="2.5"/>
          <circle cx="270" cy="138" r="5" fill="#fff" stroke="#10b981" stroke-width="2.5"/>
          <circle cx="360" cy="145" r="5" fill="#fff" stroke="#10b981" stroke-width="2.5"/>
          <circle cx="450" cy="130" r="5" fill="#fff" stroke="#10b981" stroke-width="2.5"/>
          <circle cx="540" cy="135" r="5" fill="#fff" stroke="#10b981" stroke-width="2.5"/>
          <circle cx="630" cy="138" r="5" fill="#fff" stroke="#10b981" stroke-width="2.5"/>
        </g>

        <!-- Incoming line (blue dots) -->
        <path d="M108,225 L196,230 L284,225 L372,225 L460,230 L548,225 L636,230"
              fill="none" stroke="#1a56ff" stroke-width="2" stroke-dasharray="0" opacity=".55"/>
        <g fill="#1a56ff">
          <circle cx="108" cy="225" r="3.5"/><circle cx="196" cy="230" r="3.5"/>
          <circle cx="284" cy="225" r="3.5"/><circle cx="372" cy="225" r="3.5"/>
          <circle cx="460" cy="230" r="3.5"/><circle cx="548" cy="225" r="3.5"/>
          <circle cx="636" cy="230" r="3.5"/>
        </g>

        <!-- x-axis labels -->
        <g fill="#8b93a7" font-size="11" font-family="Inter" text-anchor="middle">
          <text x="98"  y="298">May 13</text>
          <text x="186" y="298">May 14</text>
          <text x="274" y="298">May 15</text>
          <text x="362" y="298">May 16</text>
          <text x="450" y="298">May 17</text>
          <text x="538" y="298">May 18</text>
          <text x="626" y="298">May 19</text>
        </g>
      </svg>
    </div>
  </div>

  <!-- Call Distribution (donut) -->
  <div class="card">
    <div class="card-header">
      <div class="card-title">Call Distribution</div>
    </div>

    <div class="donut-wrap">
      <?php
        // pie stroke-dasharray math (r=54, circumference ~ 339.29)
        $C = 2 * pi() * 54;
      ?>
      <svg viewBox="0 0 140 140" width="180" height="180" class="donut">
        <circle cx="70" cy="70" r="54" fill="none" stroke="#eef2f9" stroke-width="22"/>
        <!-- Answered 62.4% -->
        <circle cx="70" cy="70" r="54" fill="none" stroke="#10b981" stroke-width="22"
                stroke-dasharray="<?= round($C * .624, 2) ?> <?= $C ?>"
                stroke-dashoffset="<?= round($C * .25, 2) ?>"
                transform="rotate(-90 70 70)"/>
        <!-- No Answer 33.6% -->
        <circle cx="70" cy="70" r="54" fill="none" stroke="#1a56ff" stroke-width="22"
                stroke-dasharray="<?= round($C * .336, 2) ?> <?= $C ?>"
                stroke-dashoffset="<?= round($C * (.25 - .624), 2) ?>"
                transform="rotate(-90 70 70)"/>
        <!-- Busy/Failed 4.0% -->
        <circle cx="70" cy="70" r="54" fill="none" stroke="#ef4444" stroke-width="22"
                stroke-dasharray="<?= round($C * .040, 2) ?> <?= $C ?>"
                stroke-dashoffset="<?= round($C * (.25 - .960), 2) ?>"
                transform="rotate(-90 70 70)"/>

        <!-- % labels -->
        <text x="46" y="80" fill="#fff" font-size="9" font-weight="700" font-family="Inter" text-anchor="middle">33.6%</text>
        <text x="90" y="88" fill="#fff" font-size="9" font-weight="700" font-family="Inter" text-anchor="middle">62.4%</text>
        <text x="80" y="30" fill="#fff" font-size="7" font-weight="700" font-family="Inter" text-anchor="middle">4.0%</text>
      </svg>

      <div class="donut-legend">
        <div class="dl-row">
          <span class="dl-dot" style="background:#10b981"></span>
          <span class="dl-label">Answered</span>
          <span class="dl-val">780 <em>(62.4%)</em></span>
        </div>
        <div class="dl-row">
          <span class="dl-dot" style="background:#1a56ff"></span>
          <span class="dl-label">No Answer</span>
          <span class="dl-val">420 <em>(33.6%)</em></span>
        </div>
        <div class="dl-row">
          <span class="dl-dot" style="background:#ef4444"></span>
          <span class="dl-label">Busy / Failed</span>
          <span class="dl-val">50 <em>(4.0%)</em></span>
        </div>
        <div class="dl-total">
          <span>Total Calls</span>
          <strong>1,250</strong>
        </div>
      </div>
    </div>
  </div>
</section>

<!-- ============ Summary + Top SIP row ============ -->
<section class="reports-row">
  <!-- Call Summary table -->
  <div class="card">
    <div class="card-header">
      <div class="card-title">Call Summary</div>
    </div>

    <div class="cl-table-wrap">
      <table class="cl-table summary-table">
        <thead>
          <tr>
            <th class="cl-col-num">#</th>
            <th>Date</th>
            <th>Total Calls</th>
            <th>Outgoing</th>
            <th>Incoming</th>
            <th>Missed</th>
            <th>Total Duration</th>
            <th>Avg Duration</th>
          </tr>
        </thead>
        <tbody>
          <?php foreach ($summaryRows as $i => $r): ?>
          <tr>
            <td class="cl-col-num"><?= $i + 1 ?></td>
            <td class="cl-date"><?= htmlspecialchars($r['date']) ?></td>
            <td><?= $r['total'] ?></td>
            <td><?= $r['out'] ?></td>
            <td><?= $r['in'] ?></td>
            <td><?= $r['miss'] ?></td>
            <td><?= htmlspecialchars($r['dur']) ?></td>
            <td><?= htmlspecialchars($r['avg']) ?></td>
          </tr>
          <?php endforeach; ?>
          <tr class="summary-total">
            <td colspan="2">Total / Average</td>
            <td>1,250</td>
            <td>780</td>
            <td>420</td>
            <td>50</td>
            <td>18:42:36</td>
            <td>00:00:54</td>
          </tr>
        </tbody>
      </table>
    </div>

    <div class="cl-footer">
      <div class="cl-info">Showing 1 to 8 of 8 entries</div>
      <nav class="cl-pagination">
        <button class="page-btn"><i class="fa-solid fa-chevron-left"></i></button>
        <button class="page-btn active">1</button>
        <button class="page-btn"><i class="fa-solid fa-chevron-right"></i></button>
      </nav>
      <div class="cl-perpage">
        <div class="toolbar-select small">
          <select><option>10 per page</option><option>25 per page</option></select>
          <i class="fa-solid fa-chevron-down caret"></i>
        </div>
      </div>
    </div>
  </div>

  <!-- Top SIP Accounts -->
  <div class="card">
    <div class="card-header">
      <div class="card-title">Top SIP Accounts <span class="card-title-sub">(by Calls)</span></div>
      <a href="sip-accounts.php" class="card-link">View All</a>
    </div>

    <div class="topsip-head">
      <span>SIP Account</span>
      <span>Total Calls</span>
      <span>%</span>
    </div>

    <div class="topsip-list">
      <?php foreach ($topSip as $s): ?>
        <div class="topsip-row">
          <div class="topsip-label">
            <span class="topsip-dot ts-<?= $s['color'] ?>"></span>
            <?= htmlspecialchars($s['name']) ?>
          </div>
          <div class="topsip-calls"><?= $s['calls'] ?></div>
          <div class="topsip-pct"><?= number_format($s['pct'], 1) ?>%</div>
          <div class="topsip-bar-wrap">
            <div class="topsip-bar ts-<?= $s['color'] ?>" style="width: <?= $s['pct'] ?>%"></div>
          </div>
        </div>
      <?php endforeach; ?>
    </div>

    <div class="topsip-total">
      <span>Total</span>
      <span>1,250</span>
      <span>100%</span>
    </div>
  </div>
</section>

<?php include __DIR__ . '/../includes/footer.php'; ?>
