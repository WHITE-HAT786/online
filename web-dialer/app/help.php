<?php
$pageTitle   = 'Help Center';
$activeMenu  = 'help';
$currentDate = 'May 20, 2025';
$currentTime = '10:30 AM';
$sipStatus   = 'registered';
$notifCount  = 3;
$breadcrumb  = [
  ['label' => 'Dashboard', 'href' => 'dashboard.php'],
  ['label' => 'Help Center'],
];

$quickLinks = [
  ['icon'=>'fa-book-open',    'color'=>'blue',   'title'=>'Getting Started', 'desc'=>'New to WebDialer? Start here to learn the basics.'],
  ['icon'=>'fa-phone',        'color'=>'green',  'title'=>'Dialer Guide',    'desc'=>'Learn how to make calls, manage contacts and more.'],
  ['icon'=>'fa-users',        'color'=>'purple', 'title'=>'SIP Accounts',    'desc'=>'Set up and configure your SIP accounts.'],
  ['icon'=>'fa-gear',         'color'=>'orange', 'title'=>'Settings',        'desc'=>'Customize your preferences and application settings.'],
];

$articles = [
  ['icon'=>'fa-user-plus',       'color'=>'blue',   'title'=>'How to Add a SIP Account',      'desc'=>'Step-by-step guide to add and configure a new SIP account.',    'tag'=>'SIP Accounts',    'tagColor'=>'blue',   'views'=>'1.2K'],
  ['icon'=>'fa-phone',           'color'=>'green',  'title'=>'Making Your First Call',        'desc'=>'Learn how to make outbound calls using WebDialer.',             'tag'=>'Dialer',          'tagColor'=>'green',  'views'=>'982'],
  ['icon'=>'fa-microphone',      'color'=>'purple', 'title'=>'Call Quality Troubleshooting',  'desc'=>'Tips to improve call quality and fix common issues.',           'tag'=>'Troubleshooting', 'tagColor'=>'orange', 'views'=>'756'],
  ['icon'=>'fa-credit-card',     'color'=>'orange', 'title'=>'Billing & Subscription FAQ',    'desc'=>'Find answers to common billing and subscription questions.',    'tag'=>'Billing',         'tagColor'=>'green',  'views'=>'643'],
  ['icon'=>'fa-shield-halved',   'color'=>'red',    'title'=>'Security Best Practices',       'desc'=>'Best practices to keep your account and calls secure.',         'tag'=>'Security',        'tagColor'=>'red',    'views'=>'512'],
];

$systemStatus = [
  ['label'=>'Dialer Service',   'status'=>'Operational'],
  ['label'=>'SIP Service',      'status'=>'Operational'],
  ['label'=>'Call Recording',   'status'=>'Operational'],
  ['label'=>'Web Application',  'status'=>'Operational'],
];

include __DIR__ . '/../includes/header.php';
?>

<!-- ============ HERO ============ -->
<section class="help-hero">
  <div class="help-hero-illus">
    <div class="illus-page">
      <div class="illus-lines"><span></span><span></span><span></span><span></span></div>
    </div>
    <div class="illus-q"><i class="fa-solid fa-question"></i></div>
    <div class="illus-leaf leaf-l"></div>
    <div class="illus-leaf leaf-r"></div>
  </div>

  <div class="help-hero-body">
    <h2 class="help-hero-title">How can we help you?</h2>
    <p class="help-hero-sub">Search our help center for articles, guides and tutorials.</p>

    <div class="help-search">
      <input type="text" placeholder="Search for help articles..." />
      <button aria-label="Search"><i class="fa-solid fa-magnifying-glass"></i></button>
    </div>

    <div class="help-popular">
      <span class="help-popular-label">Popular searches:</span>
      <a href="#" class="help-chip">SIP Account Setup</a>
      <a href="#" class="help-chip">Call Quality</a>
      <a href="#" class="help-chip">Troubleshooting</a>
      <a href="#" class="help-chip">Billing &amp; Subscription</a>
    </div>
  </div>
</section>

<!-- ============ MAIN GRID ============ -->
<div class="help-grid">
  <div class="help-main">
    <!-- Quick links -->
    <div class="help-section-title">Quick Links</div>
    <div class="quicklinks">
      <?php foreach ($quickLinks as $ql): ?>
        <a href="#" class="qlink">
          <div class="qlink-icon <?= $ql['color'] ?>"><i class="fa-solid <?= $ql['icon'] ?>"></i></div>
          <div class="qlink-title"><?= htmlspecialchars($ql['title']) ?></div>
          <div class="qlink-desc"><?= htmlspecialchars($ql['desc']) ?></div>
          <div class="qlink-arrow"><i class="fa-solid fa-arrow-right"></i></div>
        </a>
      <?php endforeach; ?>
    </div>

    <!-- Popular articles -->
    <div class="card articles-card">
      <div class="articles-head">
        <div class="help-section-title inline">Popular Articles</div>
        <a href="#" class="articles-viewall">View All Articles <i class="fa-solid fa-arrow-right"></i></a>
      </div>

      <div class="articles-list">
        <?php foreach ($articles as $a): ?>
        <a href="#" class="article-row">
          <div class="article-icon <?= $a['color'] ?>"><i class="fa-solid <?= $a['icon'] ?>"></i></div>
          <div class="article-info">
            <div class="article-title"><?= htmlspecialchars($a['title']) ?></div>
            <div class="article-desc"><?= htmlspecialchars($a['desc']) ?></div>
          </div>
          <span class="tag tag-<?= $a['tagColor'] ?>"><?= htmlspecialchars($a['tag']) ?></span>
          <div class="article-views">
            <i class="fa-regular fa-eye"></i> <?= htmlspecialchars($a['views']) ?>
          </div>
        </a>
        <?php endforeach; ?>
      </div>

      <a href="#" class="articles-viewall-bottom">
        View All Articles <i class="fa-solid fa-arrow-right"></i>
      </a>
    </div>
  </div>

  <!-- ============ RIGHT COLUMN ============ -->
  <aside class="help-side">
    <div class="card">
      <div class="help-section-title inline">Contact Support</div>
      <p class="help-side-sub">Can't find what you're looking for? We're here to help!</p>

      <a href="#" class="support-row">
        <div class="support-ic blue"><i class="fa-solid fa-comments"></i></div>
        <div class="support-info">
          <div class="support-title">Live Chat</div>
          <div class="support-sub">Chat with our support team</div>
        </div>
        <span class="support-online">Online</span>
      </a>

      <a href="mailto:support@webdialer.com" class="support-row">
        <div class="support-ic purple"><i class="fa-regular fa-envelope"></i></div>
        <div class="support-info">
          <div class="support-title">Email Support</div>
          <div class="support-sub">support@webdialer.com</div>
          <div class="support-note">We reply within 24 hours</div>
        </div>
      </a>

      <a href="tel:+12025550143" class="support-row">
        <div class="support-ic green"><i class="fa-solid fa-phone"></i></div>
        <div class="support-info">
          <div class="support-title">Phone Support</div>
          <div class="support-sub">+1 (202) 555-0143</div>
          <div class="support-note">Mon - Fri, 9AM - 6PM (EST)</div>
        </div>
      </a>
    </div>

    <div class="card">
      <div class="status-head">
        <div>
          <div class="help-section-title inline">System Status</div>
          <div class="status-overall">All Systems Operational</div>
        </div>
        <span class="status-ok-icon"><i class="fa-solid fa-check"></i></span>
      </div>

      <div class="status-list">
        <?php foreach ($systemStatus as $s): ?>
        <div class="status-row">
          <span class="status-dot"></span>
          <span class="status-label"><?= htmlspecialchars($s['label']) ?></span>
          <span class="status-value"><?= htmlspecialchars($s['status']) ?></span>
        </div>
        <?php endforeach; ?>
      </div>

      <a href="#" class="status-viewpage">
        View Status Page <i class="fa-solid fa-arrow-right"></i>
      </a>
    </div>
  </aside>
</div>

<?php include __DIR__ . '/../includes/footer.php'; ?>
