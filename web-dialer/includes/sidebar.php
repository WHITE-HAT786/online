<?php
/**
 * Sidebar — shared across every authenticated page.
 * Links point to sibling PHP files in the same /app/ folder.
 */
$menu = [
  ['key' => 'dashboard',    'label' => 'Dashboard',    'icon' => 'fa-gauge-high',       'href' => 'dashboard.php'],
  ['key' => 'dialer',       'label' => 'Dialer',       'icon' => 'fa-phone',            'href' => 'dialer.php'],
  ['key' => 'contacts',     'label' => 'Contacts',     'icon' => 'fa-address-book',     'href' => 'contacts.php'],
  ['key' => 'call-logs',    'label' => 'Call Logs',    'icon' => 'fa-list',             'href' => 'call-logs.php'],
  ['key' => 'sip-accounts', 'label' => 'SIP Accounts', 'icon' => 'fa-server',           'href' => 'sip-accounts.php'],
  ['key' => 'settings',     'label' => 'Settings',     'icon' => 'fa-gear',             'href' => 'settings.php'],
  ['key' => 'subscription', 'label' => 'Subscription', 'icon' => 'fa-credit-card',      'href' => 'subscription.php'],
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

    <?php if (!empty($currentPlan)): ?>
      <div class="sidebar-plan">
        <div class="plan-head">
          <div>
            <div class="plan-label">Current Plan</div>
            <div class="plan-name"><?= htmlspecialchars($currentPlan['name']) ?></div>
          </div>
          <?php if (($currentPlan['status'] ?? '') === 'active'): ?>
            <span class="plan-badge">Active</span>
          <?php endif; ?>
        </div>
        <div class="plan-price">
          <?= htmlspecialchars($currentPlan['price']) ?>
          <span>/ <?= htmlspecialchars($currentPlan['period'] ?? 'month') ?></span>
        </div>
        <?php if (!empty($currentPlan['renews'])): ?>
          <div class="plan-renew">Renews on <?= htmlspecialchars($currentPlan['renews']) ?></div>
        <?php endif; ?>
        <a href="subscription.php" class="plan-cta">
          <i class="fa-regular fa-credit-card"></i> View Subscription
        </a>
      </div>
    <?php endif; ?>

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
