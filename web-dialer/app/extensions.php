<?php
require_once __DIR__ . '/../includes/auth_guard.php';

$pageTitle   = 'Extensions (WebRTC)';
$activeMenu  = 'extensions';
$sipStatus   = 'registered';
$notifCount  = 3;
$breadcrumb  = [
  ['label' => 'Dashboard', 'href' => 'dashboard.php'],
  ['label' => 'Extensions'],
];

// Ensure the table exists on first visit
db()->exec("CREATE TABLE IF NOT EXISTS pkg_extension (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  user_id INT UNSIGNED NOT NULL,
  extension VARCHAR(10) NOT NULL,
  name VARCHAR(120) NOT NULL,
  caller_id_name VARCHAR(120) DEFAULT NULL,
  caller_id_number VARCHAR(40) DEFAULT NULL,
  password VARCHAR(120) NOT NULL,
  webrtc TINYINT(1) DEFAULT 1,
  status ENUM('registered','offline','disabled') DEFAULT 'offline',
  last_registered DATETIME DEFAULT NULL,
  created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
  updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  KEY idx_user(user_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");

// Seed some demo rows the first time only
$uid = auth_user_id();
$hasAny = (int)db()->query("SELECT COUNT(*) FROM pkg_extension WHERE user_id=$uid")->fetchColumn();
if (!$hasAny) {
  $seed = db()->prepare("INSERT INTO pkg_extension
    (user_id,extension,name,caller_id_name,caller_id_number,password,webrtc,status,last_registered) VALUES (?,?,?,?,?,?,?,?,?)");
  $rows = [
    ['1001','John Doe',      'John Doe',      '+1 202-555-0143','ext1001pass',1,'registered', '2025-05-20 10:28:00'],
    ['1002','Jane Smith',    'Jane Smith',    '+1 202-555-0187','ext1002pass',1,'registered', '2025-05-20 10:22:00'],
    ['1003','Support Team',  'Support',       '+1 202-555-0199','ext1003pass',1,'registered', '2025-05-20 10:25:00'],
    ['1004','Sales Team',    'Sales',         '+1 202-555-0177','ext1004pass',1,'offline',    null],
    ['1005','Mike Brown',    'Mike Brown',    '+1 202-555-0166','ext1005pass',1,'offline',    null],
    ['1006','Conference Room','Conference',   '+1 202-555-0190','ext1006pass',0,'registered', '2025-05-20 10:15:00'],
  ];
  foreach ($rows as $r) $seed->execute(array_merge([$uid], $r));
}

// Load extensions
$stmt = db()->prepare("SELECT * FROM pkg_extension WHERE user_id=? ORDER BY extension ASC");
$stmt->execute([$uid]);
$exts = $stmt->fetchAll();

$c = ['total'=>count($exts),'registered'=>0,'offline'=>0,'disabled'=>0];
foreach ($exts as $e) { $k = $e['status']; $c[$k] = ($c[$k]??0)+1; }

include __DIR__ . '/../includes/header.php';
?>

<div class="sip-head">
  <div>
    <div class="sub-title">Extensions (WebRTC)</div>
    <div class="sub-sub">Manage your WebRTC extensions used to connect from browsers or softphones.</div>
  </div>
  <div class="sip-head-actions">
    <button class="btn-outline"><i class="fa-solid fa-arrows-rotate"></i> Refresh</button>
    <button class="btn btn-primary" data-modal-open="addExtModal"><i class="fa-solid fa-plus"></i> Add Extension</button>
  </div>
</div>

<section class="sip-stats">
  <div class="sip-stat sip-stat-blue"><div class="sip-stat-ic"><i class="fa-solid fa-users"></i></div>
    <div><div class="sip-stat-label">Total Extensions</div><div class="sip-stat-value"><?= (int)$c['total'] ?></div><div class="sip-stat-note">All extensions</div></div></div>
  <div class="sip-stat sip-stat-green"><div class="sip-stat-ic"><i class="fa-solid fa-circle-check"></i></div>
    <div><div class="sip-stat-label">Registered</div><div class="sip-stat-value"><?= (int)$c['registered'] ?></div><div class="sip-stat-note ok">Currently online</div></div></div>
  <div class="sip-stat sip-stat-orange"><div class="sip-stat-ic"><i class="fa-regular fa-clock"></i></div>
    <div><div class="sip-stat-label">Not Registered</div><div class="sip-stat-value"><?= (int)$c['offline'] ?></div><div class="sip-stat-note">Currently offline</div></div></div>
  <div class="sip-stat sip-stat-purple"><div class="sip-stat-ic"><i class="fa-solid fa-bell-slash"></i></div>
    <div><div class="sip-stat-label">Disabled</div><div class="sip-stat-value"><?= (int)$c['disabled'] ?></div><div class="sip-stat-note">Not in use</div></div></div>
</section>

<div class="card sip-table-card">
  <div class="contacts-toolbar" style="padding:16px 18px 0;">
    <div class="toolbar-search"><i class="fa-solid fa-magnifying-glass"></i>
      <input type="text" placeholder="Search by extension, name or caller ID..." /></div>
    <div class="toolbar-select"><select><option>All Status</option><option>Registered</option><option>Not Registered</option><option>Disabled</option></select><i class="fa-solid fa-chevron-down caret"></i></div>
    <div class="toolbar-right">
      <div class="toolbar-select small"><select><option>All Extensions</option></select><i class="fa-solid fa-chevron-down caret"></i></div>
      <button class="icon-btn-outline"><i class="fa-solid fa-arrows-rotate"></i></button>
    </div>
  </div>

  <div class="sip-table-wrap">
    <table class="sip-table">
      <thead><tr>
        <th class="sip-col-num">#</th><th>Extension</th><th>Name</th><th>Caller ID</th><th>Password</th><th>WebRTC</th><th>Status</th><th>Last Registered</th><th class="sip-col-actions">Actions</th>
      </tr></thead>
      <tbody>
        <?php foreach ($exts as $i => $e): ?>
        <tr data-id="<?= (int)$e['id'] ?>">
          <td class="sip-col-num"><?= $i+1 ?></td>
          <td><a href="#" style="color:var(--brand-blue);font-weight:600;"><?= e($e['extension']) ?></a></td>
          <td><?= e($e['name']) ?></td>
          <td><div class="sip-caller"><?= e($e['caller_id_name']) ?></div><div class="sip-caller-num"><?= e($e['caller_id_number']) ?></div></td>
          <td><span class="ext-pw"><span class="ext-pw-mask">••••••••••</span>
            <button class="ext-pw-eye" onclick="const m=this.parentElement.querySelector('.ext-pw-mask');m.textContent = m.textContent==='••••••••••'?'<?= e($e['password']) ?>':'••••••••••';"><i class="fa-regular fa-eye"></i></button></span></td>
          <td><?php if ($e['webrtc']): ?><span class="pill-yes">Yes</span><?php else: ?><span class="pill-no">No</span><?php endif; ?></td>
          <td><?php if ($e['status']==='registered'): ?><span class="sip-status-dot on"><span class="dot"></span> Registered</span><?php else: ?><span class="sip-status-dot off"><span class="dot"></span> Not Registered</span><?php endif; ?></td>
          <td><?php if ($e['last_registered']): ?><div class="sip-last-date"><?= date('M j, Y', strtotime($e['last_registered'])) ?></div><div class="sip-last-time"><?= date('g:i A', strtotime($e['last_registered'])) ?></div><?php else: ?><span class="sip-dash">—</span><?php endif; ?></td>
          <td class="sip-col-actions">
            <button class="row-action row-edit" title="Edit"><i class="fa-solid fa-pen"></i></button>
            <button class="sip-actions-btn" title="More"><i class="fa-solid fa-ellipsis-vertical"></i></button>
          </td>
        </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  </div>
</div>

<section class="sip-info-banner">
  <div class="sip-info-icon"><i class="fa-solid fa-info"></i></div>
  <div class="sip-info-body">
    <div class="sip-info-title">About Extensions (WebRTC)</div>
    <div class="sip-info-text">Extensions allow users to connect to Asterisk PBX using WebRTC (browser) or softphones.<br>Each extension must be unique and can be assigned to a user for inbound and outbound calls.</div>
  </div>
  <button class="btn-outline sip-info-btn"><i class="fa-solid fa-book-open"></i> Learn More</button>
</section>

<?php
$modalId='addExtModal'; $modalTitle='Add Extension'; $modalIcon='fa-headset'; $modalSize='md';
$modalBody='<div style="display:grid;gap:14px;">
  <div style="display:grid;grid-template-columns:1fr 1fr;gap:14px;">
    <div><label style="font-size:13px;font-weight:600;display:block;margin-bottom:6px;">Extension Number</label>
      <input type="text" placeholder="1007" style="width:100%;height:44px;border:1px solid var(--border);border-radius:10px;padding:0 14px;font-family:inherit;font-size:14px;"></div>
    <div><label style="font-size:13px;font-weight:600;display:block;margin-bottom:6px;">Display Name</label>
      <input type="text" placeholder="e.g. John Doe" style="width:100%;height:44px;border:1px solid var(--border);border-radius:10px;padding:0 14px;font-family:inherit;font-size:14px;"></div>
  </div>
  <div style="display:grid;grid-template-columns:1fr 1fr;gap:14px;">
    <div><label style="font-size:13px;font-weight:600;display:block;margin-bottom:6px;">Caller ID Name</label>
      <input type="text" placeholder="John Doe" style="width:100%;height:44px;border:1px solid var(--border);border-radius:10px;padding:0 14px;font-family:inherit;font-size:14px;"></div>
    <div><label style="font-size:13px;font-weight:600;display:block;margin-bottom:6px;">Caller ID Number</label>
      <input type="text" placeholder="+1 202-555-0143" style="width:100%;height:44px;border:1px solid var(--border);border-radius:10px;padding:0 14px;font-family:inherit;font-size:14px;"></div>
  </div>
  <div><label style="font-size:13px;font-weight:600;display:block;margin-bottom:6px;">Password</label>
    <input type="password" placeholder="Strong password" style="width:100%;height:44px;border:1px solid var(--border);border-radius:10px;padding:0 14px;font-family:inherit;font-size:14px;"></div>
  <label style="display:flex;align-items:center;gap:10px;font-size:14px;">
    <input type="checkbox" checked style="width:16px;height:16px;"><span>Enable WebRTC transport</span></label>
</div>';
$modalFooter='<button class="btn btn-ghost" data-modal-close>Cancel</button><button class="btn btn-primary"><i class="fa-solid fa-plus"></i> Save Extension</button>';
include __DIR__ . '/../includes/modal.php';
?>

<?php include __DIR__ . '/../includes/footer.php'; ?>
