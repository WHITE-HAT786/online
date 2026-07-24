<?php
$pageTitle   = 'SIP Accounts';
$activeMenu  = 'sip-accounts';
$currentDate = 'May 20, 2025';
$currentTime = '10:30 AM';
$sipStatus   = 'registered';
$notifCount  = 3;
$breadcrumb  = [
  ['label' => 'Dashboard', 'href' => 'dashboard.php'],
  ['label' => 'SIP Accounts'],
];

$accounts = [
  [
    'name'=>'Twilio Account', 'default'=>true, 'sub'=>'Primary Twilio Account',
    'username'=>'company123', 'server'=>'sip.twilio.com', 'port'=>':5060 (UDP)',
    'status'=>'registered', 'caller_name'=>'John Doe', 'caller_num'=>'+1 202-555-0143',
    'last'=>'May 20, 2025', 'last_time'=>'10:28 AM',
    'icon'=>'fa-circle-nodes', 'color'=>'green',
  ],
  [
    'name'=>'Telnyx Account', 'default'=>false, 'sub'=>'Backup Telnyx',
    'username'=>'company456', 'server'=>'sip.telnyx.com', 'port'=>':5060 (TCP)',
    'status'=>'offline', 'caller_name'=>'John Doe', 'caller_num'=>'+1 202-555-0187',
    'last'=>null,
    'icon'=>'fa-tower-broadcast', 'color'=>'blue',
  ],
  [
    'name'=>'Bandwidth Account', 'default'=>false, 'sub'=>'US Number',
    'username'=>'company789', 'server'=>'sip.bandwidth.com', 'port'=>':5061 (TLS)',
    'status'=>'offline', 'caller_name'=>'John Doe', 'caller_num'=>'+1 202-555-0164',
    'last'=>null,
    'icon'=>'fa-signal', 'color'=>'purple',
  ],
];

include __DIR__ . '/../includes/header.php';
?>

<!-- ============ Page header ============ -->
<div class="sip-head">
  <div>
    <div class="sub-title">SIP Accounts</div>
    <div class="sub-sub">Manage your SIP Accounts used for making and receiving calls.</div>
  </div>
  <div class="sip-head-actions">
    <button class="btn-outline">
      <i class="fa-solid fa-arrows-rotate"></i> Refresh
    </button>
    <button class="btn btn-primary" data-modal-open="addSipModal">
      <i class="fa-solid fa-plus"></i> Add Account
    </button>
  </div>
</div>

<!-- ============ Stat cards ============ -->
<section class="sip-stats">
  <div class="sip-stat sip-stat-blue">
    <div class="sip-stat-ic"><i class="fa-solid fa-layer-group"></i></div>
    <div>
      <div class="sip-stat-label">Total Accounts</div>
      <div class="sip-stat-value">3</div>
      <div class="sip-stat-note">All SIP accounts</div>
    </div>
  </div>

  <div class="sip-stat sip-stat-green">
    <div class="sip-stat-ic"><i class="fa-solid fa-circle-check"></i></div>
    <div>
      <div class="sip-stat-label">Registered</div>
      <div class="sip-stat-value">1</div>
      <div class="sip-stat-note ok">Currently online</div>
    </div>
  </div>

  <div class="sip-stat sip-stat-orange">
    <div class="sip-stat-ic"><i class="fa-regular fa-clock"></i></div>
    <div>
      <div class="sip-stat-label">Not Registered</div>
      <div class="sip-stat-value">2</div>
      <div class="sip-stat-note">Currently offline</div>
    </div>
  </div>

  <div class="sip-stat sip-stat-purple">
    <div class="sip-stat-ic"><i class="fa-solid fa-bell-slash"></i></div>
    <div>
      <div class="sip-stat-label">Disabled</div>
      <div class="sip-stat-value">0</div>
      <div class="sip-stat-note">Not in use</div>
    </div>
  </div>
</section>

<!-- ============ Accounts table ============ -->
<div class="card sip-table-card">
  <div class="sip-table-wrap">
    <table class="sip-table">
      <thead>
        <tr>
          <th class="sip-col-num">#</th>
          <th>Account Name</th>
          <th>SIP Username</th>
          <th>SIP Server</th>
          <th>Status</th>
          <th>Caller ID</th>
          <th>Last Registered</th>
          <th class="sip-col-actions">Actions</th>
        </tr>
      </thead>
      <tbody>
        <?php foreach ($accounts as $i => $a): ?>
        <tr>
          <td class="sip-col-num"><?= $i + 1 ?></td>
          <td>
            <div class="sip-account">
              <div class="sip-account-ic sip-ic-<?= $a['color'] ?>">
                <i class="fa-solid <?= $a['icon'] ?>"></i>
              </div>
              <div>
                <div class="sip-account-name">
                  <?= htmlspecialchars($a['name']) ?>
                  <?php if ($a['default']): ?>
                    <span class="tag-default">Default</span>
                  <?php endif; ?>
                </div>
                <div class="sip-account-sub"><?= htmlspecialchars($a['sub']) ?></div>
              </div>
            </div>
          </td>
          <td class="sip-username"><?= htmlspecialchars($a['username']) ?></td>
          <td>
            <div class="sip-server"><?= htmlspecialchars($a['server']) ?></div>
            <div class="sip-port"><?= htmlspecialchars($a['port']) ?></div>
          </td>
          <td>
            <?php if ($a['status'] === 'registered'): ?>
              <span class="sip-status-dot on">
                <span class="dot"></span> Registered
              </span>
            <?php else: ?>
              <span class="sip-status-dot off">
                <span class="dot"></span> Not Registered
              </span>
            <?php endif; ?>
          </td>
          <td>
            <div class="sip-caller"><?= htmlspecialchars($a['caller_name']) ?></div>
            <div class="sip-caller-num"><?= htmlspecialchars($a['caller_num']) ?></div>
          </td>
          <td>
            <?php if (!empty($a['last'])): ?>
              <div class="sip-last-date"><?= htmlspecialchars($a['last']) ?></div>
              <div class="sip-last-time"><?= htmlspecialchars($a['last_time']) ?></div>
            <?php else: ?>
              <span class="sip-dash">—</span>
            <?php endif; ?>
          </td>
          <td class="sip-col-actions">
            <button class="sip-actions-btn" title="More"><i class="fa-solid fa-ellipsis-vertical"></i></button>
          </td>
        </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  </div>
</div>

<!-- ============ Info banner ============ -->
<section class="sip-info-banner">
  <div class="sip-info-icon"><i class="fa-solid fa-info"></i></div>
  <div class="sip-info-body">
    <div class="sip-info-title">About SIP Accounts</div>
    <div class="sip-info-text">
      SIP accounts are used to connect to VoIP providers. You can add multiple accounts and switch between them from the dialer.<br>
      Only one account can be active (registered) at a time.
    </div>
  </div>
  <button class="btn-outline sip-info-btn">
    <i class="fa-solid fa-book-open"></i> Learn More
  </button>
</section>

<?php
// -------- Add SIP Account modal --------
$modalId    = 'addSipModal';
$modalTitle = 'Add SIP Account';
$modalIcon  = 'fa-server';
$modalSize  = 'lg';
$modalBody  = '
  <div style="display:grid;gap:14px;">
    <div>
      <label style="font-size:13px;font-weight:600;display:block;margin-bottom:6px;">Account Name</label>
      <input type="text" placeholder="e.g. Twilio Account"
             style="width:100%;height:44px;border:1px solid var(--border);border-radius:10px;padding:0 14px;font-family:inherit;font-size:14px;">
    </div>
    <div style="display:grid;grid-template-columns:1.4fr 1fr;gap:14px;">
      <div>
        <label style="font-size:13px;font-weight:600;display:block;margin-bottom:6px;">SIP Server</label>
        <input type="text" placeholder="sip.provider.com"
               style="width:100%;height:44px;border:1px solid var(--border);border-radius:10px;padding:0 14px;font-family:inherit;font-size:14px;">
      </div>
      <div>
        <label style="font-size:13px;font-weight:600;display:block;margin-bottom:6px;">Port</label>
        <input type="text" value="5060"
               style="width:100%;height:44px;border:1px solid var(--border);border-radius:10px;padding:0 14px;font-family:inherit;font-size:14px;">
      </div>
    </div>
    <div style="display:grid;grid-template-columns:1fr 1fr;gap:14px;">
      <div>
        <label style="font-size:13px;font-weight:600;display:block;margin-bottom:6px;">Transport</label>
        <select style="width:100%;height:44px;border:1px solid var(--border);border-radius:10px;padding:0 14px;font-family:inherit;font-size:14px;background:#fff;">
          <option>UDP</option><option>TCP</option><option>TLS</option>
        </select>
      </div>
      <div>
        <label style="font-size:13px;font-weight:600;display:block;margin-bottom:6px;">Caller ID</label>
        <input type="text" placeholder="+1 202-555-0143"
               style="width:100%;height:44px;border:1px solid var(--border);border-radius:10px;padding:0 14px;font-family:inherit;font-size:14px;">
      </div>
    </div>
    <div style="display:grid;grid-template-columns:1fr 1fr;gap:14px;">
      <div>
        <label style="font-size:13px;font-weight:600;display:block;margin-bottom:6px;">SIP Username</label>
        <input type="text" placeholder="username"
               style="width:100%;height:44px;border:1px solid var(--border);border-radius:10px;padding:0 14px;font-family:inherit;font-size:14px;">
      </div>
      <div>
        <label style="font-size:13px;font-weight:600;display:block;margin-bottom:6px;">SIP Password</label>
        <input type="password" placeholder="••••••••"
               style="width:100%;height:44px;border:1px solid var(--border);border-radius:10px;padding:0 14px;font-family:inherit;font-size:14px;">
      </div>
    </div>
    <label style="display:flex;align-items:center;gap:10px;font-size:14px;padding-top:4px;">
      <input type="checkbox" style="width:16px;height:16px;">
      <span>Set as default account</span>
    </label>
  </div>';
$modalFooter = '<button class="btn btn-ghost" data-modal-close>Cancel</button>
                <button class="btn btn-primary"><i class="fa-solid fa-plus"></i> Add Account</button>';
include __DIR__ . '/../includes/modal.php';
?>

<?php include __DIR__ . '/../includes/footer.php'; ?>
