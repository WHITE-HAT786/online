<?php
require_once __DIR__ . '/../includes/auth_guard.php';

// Page-level config
$pageTitle   = 'Dashboard';
$activeMenu  = 'dashboard';
$sipStatus   = 'registered';
$notifCount  = 3;

$uid = auth_user_id();

// ---- Live stats for the week ----
$startCur = date('Y-m-d 00:00:00', strtotime('monday this week'));
$row = db()->prepare("SELECT COUNT(*) c,
    SUM(direction='outgoing') o, SUM(direction='incoming') i,
    SUM(direction='missed') m, COALESCE(SUM(duration_sec),0) d
  FROM pkg_call WHERE user_id=? AND started_at >= ?");
$row->execute([$uid, $startCur]);
$stats = $row->fetch();

// ---- SIP accounts (top 3) ----
$sipRows = db()->prepare("SELECT * FROM pkg_sip WHERE user_id=? ORDER BY is_default DESC, id ASC LIMIT 3");
$sipRows->execute([$uid]);
$sipList = $sipRows->fetchAll();

// ---- Recent calls (5) ----
$rc = db()->prepare("SELECT c.*, s.account_name AS sip_name FROM pkg_call c
  LEFT JOIN pkg_sip s ON s.id=c.sip_id WHERE c.user_id=? ORDER BY c.started_at DESC LIMIT 5");
$rc->execute([$uid]);
$recentCalls = $rc->fetchAll();

include __DIR__ . '/../includes/header.php';
?>

<!-- ============= STATS ============= -->
<section class="stats-grid">
  <div class="stat">
    <div class="stat-icon blue"><i class="fa-solid fa-phone"></i></div>
    <div>
      <div class="stat-label">Total Calls</div>
      <div class="stat-value"><?= (int)$stats['c'] ?></div>
      <div class="stat-delta"><i class="fa-solid fa-arrow-up"></i> This week</div>
    </div>
  </div>

  <div class="stat">
    <div class="stat-icon green"><i class="fa-solid fa-arrow-up-right-from-square"></i></div>
    <div>
      <div class="stat-label">Outgoing Calls</div>
      <div class="stat-value"><?= (int)$stats['o'] ?></div>
      <div class="stat-delta"><i class="fa-solid fa-arrow-up"></i> This week</div>
    </div>
  </div>

  <div class="stat">
    <div class="stat-icon purple"><i class="fa-solid fa-arrow-down"></i></div>
    <div>
      <div class="stat-label">Incoming Calls</div>
      <div class="stat-value"><?= (int)$stats['i'] ?></div>
      <div class="stat-delta"><i class="fa-solid fa-arrow-up"></i> This week</div>
    </div>
  </div>

  <div class="stat">
    <div class="stat-icon orange"><i class="fa-regular fa-clock"></i></div>
    <div>
      <div class="stat-label">Total Duration</div>
      <div class="stat-value"><?= fmt_duration((int)$stats['d']) ?></div>
      <div class="stat-delta"><i class="fa-solid fa-arrow-up"></i> This week</div>
    </div>
  </div>
</section>

<!-- ============= DIALER + SIP + RECENT ============= -->
<section class="grid-3">
  <!-- Dialer -->
  <div class="card dialer">
    <div class="card-title">Dialer</div>

    <div class="dial-input">
      <div class="dial-country">
        <span class="flag"></span>
        <i class="fa-solid fa-caret-down"></i>
      </div>
      <input type="text" placeholder="Enter number or name" />
      <button class="clear" aria-label="Clear"><i class="fa-solid fa-circle-xmark"></i></button>
    </div>

    <div class="keypad">
      <button class="key" data-val="1"><div class="num">1</div><span class="sub">&nbsp;</span></button>
      <button class="key" data-val="2"><div class="num">2</div><span class="sub">ABC</span></button>
      <button class="key" data-val="3"><div class="num">3</div><span class="sub">DEF</span></button>
      <button class="key" data-val="4"><div class="num">4</div><span class="sub">GHI</span></button>
      <button class="key" data-val="5"><div class="num">5</div><span class="sub">JKL</span></button>
      <button class="key" data-val="6"><div class="num">6</div><span class="sub">MNO</span></button>
      <button class="key" data-val="7"><div class="num">7</div><span class="sub">PQRS</span></button>
      <button class="key" data-val="8"><div class="num">8</div><span class="sub">TUV</span></button>
      <button class="key" data-val="9"><div class="num">9</div><span class="sub">WXYZ</span></button>
      <button class="key" data-val="*"><div class="num">*</div><span class="sub">&nbsp;</span></button>
      <button class="key" data-val="0"><div class="num">0</div><span class="sub">+</span></button>
      <button class="key" data-val="#"><div class="num">#</div><span class="sub">&nbsp;</span></button>
    </div>

    <div class="dial-actions">
      <button class="dial-btn" title="Add contact"><i class="fa-solid fa-user-plus"></i></button>
      <button class="dial-btn call" title="Call"><i class="fa-solid fa-phone"></i></button>
      <button class="dial-btn" title="Backspace"><i class="fa-solid fa-delete-left"></i></button>
    </div>
  </div>

  <!-- SIP Accounts -->
  <div class="card">
    <div class="card-header">
      <div class="card-title">SIP Accounts</div>
      <button class="btn-add" data-modal-open="addAccountModal">
        <i class="fa-solid fa-plus"></i> Add Account
      </button>
    </div>

    <div class="sip-list">
      <?php foreach ($sipList as $s):
        $c = $s['icon_color']; $on = $s['status'] === 'registered';
      ?>
      <div class="sip-row">
        <div class="sip-avatar <?= $c === 'green' ? 'g' : ($c === 'blue' ? 'b' : 'p') ?>"><i class="fa-solid <?= e($s['icon']) ?>"></i></div>
        <div>
          <div class="sip-name"><?= e($s['account_name']) ?></div>
          <div class="sip-domain"><?= e($s['sip_username']) ?>@<?= e($s['sip_server']) ?></div>
        </div>
        <span class="sip-status <?= $on ? 'on' : 'off' ?>"><?= $on ? 'Registered' : 'Offline' ?></span>
        <button class="sip-more"><i class="fa-solid fa-ellipsis-vertical"></i></button>
      </div>
      <?php endforeach; ?>
      <?php if (empty($sipList)): ?>
        <div style="color:var(--muted);padding:20px 0;text-align:center;">No SIP accounts yet.</div>
      <?php endif; ?>
    </div>

    <a href="sip-accounts.php" class="view-all">
      View All Accounts <i class="fa-solid fa-arrow-right"></i>
    </a>
  </div>

  <!-- Recent Calls -->
  <div class="card">
    <div class="card-header">
      <div class="card-title">Recent Calls</div>
      <a href="recent-calls.php" class="card-link">View All</a>
    </div>

    <div class="call-list">
      <?php foreach ($recentCalls as $c):
        $init = strtoupper(substr($c['from_number'] ?? '?', -2));
        $dir  = $c['direction'];
        $cls  = $dir === 'missed' ? 'miss' : ($dir === 'outgoing' ? 'out' : 'in');
        $lbl  = $dir === 'missed' ? 'Missed' : gmdate('i:s', (int)$c['duration_sec']);
        $ico  = $dir === 'missed' ? 'fa-phone-slash' : 'fa-phone';
      ?>
      <div class="call-row">
        <div class="call-avatar"><?= e($init) ?></div>
        <div class="call-info">
          <div class="call-name"><?= e($dir === 'outgoing' ? $c['to_number'] : $c['from_number']) ?></div>
          <div class="call-num"><?= e($c['sip_name'] ?? '') ?></div>
        </div>
        <div class="call-meta">
          <div class="call-time"><?= date('g:i A', strtotime($c['started_at'])) ?></div>
          <div class="call-dur <?= $cls ?>"><i class="fa-solid <?= $ico ?>"></i> <?= e($lbl) ?></div>
        </div>
      </div>
      <?php endforeach; ?>
      <?php if (empty($recentCalls)): ?>
        <div style="color:var(--muted);padding:20px 0;text-align:center;">No calls yet.</div>
      <?php endif; ?>
    </div>
  </div>
</section>

<!-- ============= CHART + ACTIVITY ============= -->
<section class="grid-2">
  <div class="card">
    <div class="chart-head">
      <div class="card-title">Call Statistics</div>
      <div class="legend">
        <span><span class="swatch g"></span> Outgoing</span>
        <span><span class="swatch b"></span> Incoming</span>
      </div>
      <select class="select-inline">
        <option>This Week</option>
        <option>Last Week</option>
        <option>This Month</option>
      </select>
    </div>

    <div class="chart-wrap">
      <svg viewBox="0 0 700 260" width="100%" height="260" preserveAspectRatio="none">
        <defs>
          <linearGradient id="gGreen" x1="0" x2="0" y1="0" y2="1">
            <stop offset="0%"  stop-color="#10b981" stop-opacity=".28"/>
            <stop offset="100%" stop-color="#10b981" stop-opacity="0"/>
          </linearGradient>
          <linearGradient id="gBlue" x1="0" x2="0" y1="0" y2="1">
            <stop offset="0%"  stop-color="#1a56ff" stop-opacity=".22"/>
            <stop offset="100%" stop-color="#1a56ff" stop-opacity="0"/>
          </linearGradient>
        </defs>

        <g stroke="#eef2f9" stroke-width="1">
          <line x1="40" y1="30"  x2="690" y2="30"/>
          <line x1="40" y1="85"  x2="690" y2="85"/>
          <line x1="40" y1="140" x2="690" y2="140"/>
          <line x1="40" y1="195" x2="690" y2="195"/>
          <line x1="40" y1="230" x2="690" y2="230"/>
        </g>
        <g fill="#8b93a7" font-size="11" font-family="Inter">
          <text x="10" y="34">80</text>
          <text x="10" y="89">60</text>
          <text x="10" y="144">40</text>
          <text x="10" y="199">20</text>
          <text x="18" y="245">0</text>
        </g>

        <path d="M60,140 L155,135 L250,55 L345,155 L440,120 L535,125 L630,55"
              fill="none" stroke="#10b981" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"/>
        <path d="M60,140 L155,135 L250,55 L345,155 L440,120 L535,125 L630,55 L630,230 L60,230 Z"
              fill="url(#gGreen)"/>

        <path d="M60,180 L155,170 L250,120 L345,190 L440,175 L535,180 L630,170"
              fill="none" stroke="#1a56ff" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"/>
        <path d="M60,180 L155,170 L250,120 L345,190 L440,175 L535,180 L630,170 L630,230 L60,230 Z"
              fill="url(#gBlue)"/>

        <g fill="#10b981">
          <circle cx="60"  cy="140" r="3.5"/><circle cx="155" cy="135" r="3.5"/>
          <circle cx="250" cy="55"  r="3.5"/><circle cx="345" cy="155" r="3.5"/>
          <circle cx="440" cy="120" r="3.5"/><circle cx="535" cy="125" r="3.5"/>
          <circle cx="630" cy="55"  r="3.5"/>
        </g>
        <g fill="#1a56ff">
          <circle cx="60"  cy="180" r="3.5"/><circle cx="155" cy="170" r="3.5"/>
          <circle cx="250" cy="120" r="3.5"/><circle cx="345" cy="190" r="3.5"/>
          <circle cx="440" cy="175" r="3.5"/><circle cx="535" cy="180" r="3.5"/>
          <circle cx="630" cy="170" r="3.5"/>
        </g>

        <g fill="#8b93a7" font-size="11" font-family="Inter" text-anchor="middle">
          <text x="60"  y="252">May 14</text>
          <text x="155" y="252">May 15</text>
          <text x="250" y="252">May 16</text>
          <text x="345" y="252">May 17</text>
          <text x="440" y="252">May 18</text>
          <text x="535" y="252">May 19</text>
          <text x="630" y="252">May 20</text>
        </g>
      </svg>
    </div>
  </div>

  <div class="card">
    <div class="card-header">
      <div class="card-title">Activity Feed</div>
      <a href="#" class="card-link">View All</a>
    </div>

    <div class="activity">
      <div class="act-row">
        <div class="act-icon ok"><i class="fa-solid fa-check"></i></div>
        <div>
          <div class="act-title">SIP Account "Twilio Account" registered</div>
          <div class="act-time">10:28 AM</div>
        </div>
      </div>
      <div class="act-row">
        <div class="act-icon call"><i class="fa-solid fa-phone"></i></div>
        <div>
          <div class="act-title">Outgoing call to +1 (202) 555-0143</div>
          <div class="act-time">10:28 AM</div>
        </div>
      </div>
      <div class="act-row">
        <div class="act-icon rec"><i class="fa-solid fa-microphone"></i></div>
        <div>
          <div class="act-title">Call recording completed</div>
          <div class="act-time">10:26 AM</div>
        </div>
      </div>
      <div class="act-row">
        <div class="act-icon miss"><i class="fa-solid fa-phone-slash"></i></div>
        <div>
          <div class="act-title">Missed call from +1 (202) 555-0177</div>
          <div class="act-time">10:15 AM</div>
        </div>
      </div>
    </div>
  </div>
</section>

<?php
// Page-specific modal for "Add SIP Account"
$modalId    = 'addAccountModal';
$modalTitle = 'Add SIP Account';
$modalIcon  = 'fa-server';
$modalBody  = '
  <div style="display:grid;gap:14px;">
    <div>
      <label style="font-size:13px;font-weight:600;">Account Name</label>
      <input type="text" placeholder="e.g. Twilio Account"
             style="width:100%;height:44px;border:1px solid var(--border);border-radius:10px;padding:0 14px;margin-top:6px;font-family:inherit;font-size:14px;">
    </div>
    <div>
      <label style="font-size:13px;font-weight:600;">SIP Domain</label>
      <input type="text" placeholder="sip.provider.com"
             style="width:100%;height:44px;border:1px solid var(--border);border-radius:10px;padding:0 14px;margin-top:6px;font-family:inherit;font-size:14px;">
    </div>
    <div style="display:grid;grid-template-columns:1fr 1fr;gap:14px;">
      <div>
        <label style="font-size:13px;font-weight:600;">Username</label>
        <input type="text" placeholder="username"
               style="width:100%;height:44px;border:1px solid var(--border);border-radius:10px;padding:0 14px;margin-top:6px;font-family:inherit;font-size:14px;">
      </div>
      <div>
        <label style="font-size:13px;font-weight:600;">Password</label>
        <input type="password" placeholder="••••••••"
               style="width:100%;height:44px;border:1px solid var(--border);border-radius:10px;padding:0 14px;margin-top:6px;font-family:inherit;font-size:14px;">
      </div>
    </div>
  </div>';
$modalFooter = '<button class="btn btn-ghost" data-modal-close>Cancel</button>
                <button class="btn btn-primary"><i class="fa-solid fa-plus"></i> Add Account</button>';
include __DIR__ . '/../includes/modal.php';
?>

<?php include __DIR__ . '/../includes/footer.php'; ?>
