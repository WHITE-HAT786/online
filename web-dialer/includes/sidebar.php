<?php
/**
 * Sidebar — shared across every authenticated page.
 * Links point to sibling PHP files in the same /app/ folder.
 */
$menu = [
  ['key' => 'dashboard',    'label' => 'Dashboard',    'icon' => 'fa-gauge-high',       'href' => 'dashboard.php'],
  ['key' => 'dialer',       'label' => 'Dialer',       'icon' => 'fa-phone',            'href' => 'dialer.php'],
  ['key' => 'contacts',     'label' => 'Contacts',     'icon' => 'fa-address-book',     'href' => 'contacts.php'],
  ['key' => 'recent-calls', 'label' => 'Recent Calls', 'icon' => 'fa-clock-rotate-left','href' => 'recent-calls.php'],
  ['key' => 'call-logs',    'label' => 'Call Logs',    'icon' => 'fa-list',             'href' => 'call-logs.php'],
  ['key' => 'recordings',   'label' => 'Recordings',   'icon' => 'fa-microphone',       'href' => 'recordings.php'],
  ['key' => 'sip-accounts', 'label' => 'SIP Accounts', 'icon' => 'fa-server',           'href' => 'sip-accounts.php'],
  ['key' => 'settings',     'label' => 'Settings',     'icon' => 'fa-gear',             'href' => 'settings.php'],
  ['key' => 'billing',      'label' => 'Billing',      'icon' => 'fa-credit-card',      'href' => 'billing.php'],
  ['key' => 'reports',      'label' => 'Reports',      'icon' => 'fa-chart-line',       'href' => 'reports.php'],
  ['key' => 'help',         'label' => 'Help',         'icon' => 'fa-circle-question',  'href' => 'help.php'],
  ['key' => 'logout',       'label' => 'Logout',       'icon' => 'fa-right-from-bracket','href' => 'logout.php'],
];

$user = $user ?? [
  'name'   => 'John Doe',
  'email'  => 'john.doe@example.com',
  'avatar' => 'https://i.pravatar.cc/100?img=15',
  'status' => 'online',
];
?>
<aside class="sidebar" id="sidebar">
  <div class="sidebar-inner">
    <a href="dashboard.php" class="brand">
      <div class="brand-icon"><i class="fa-solid fa-wave-square"></i></div>
      <span class="brand-text">WebDialer</span>
    </a>

    <nav class="nav">
      <?php foreach ($menu as $item): ?>
        <a href="<?= $item['href'] ?>"
           class="nav-item <?= ($activeMenu ?? '') === $item['key'] ? 'active' : '' ?>">
          <i class="fa-solid <?= $item['icon'] ?>"></i>
          <span><?= $item['label'] ?></span>
        </a>
      <?php endforeach; ?>
    </nav>

    <div class="sidebar-user">
      <img src="<?= htmlspecialchars($user['avatar']) ?>" alt="" class="avatar" />
      <div class="user-meta">
        <div class="user-name"><?= htmlspecialchars($user['name']) ?></div>
        <div class="user-email"><?= htmlspecialchars($user['email']) ?></div>
        <div class="user-status <?= $user['status'] === 'online' ? 'online' : 'offline' ?>">
          <span class="dot"></span> <?= ucfirst($user['status']) ?>
        </div>
      </div>
    </div>
  </div>
</aside>
