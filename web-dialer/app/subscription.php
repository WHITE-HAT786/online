<?php
$pageTitle   = 'Subscription';
$activeMenu  = 'subscription';
$currentDate = 'May 20, 2025';
$currentTime = '10:30 AM';
$sipStatus   = 'registered';
$notifCount  = 3;
$breadcrumb  = [
  ['label' => 'Dashboard', 'href' => 'dashboard.php'],
  ['label' => 'Subscription'],
];

$plans = [
  [
    'name'=>'Basic', 'price'=>'$9.00', 'period'=>'month',
    'popular'=>false, 'current'=>false,
    'features'=>['1 SIP Account','1 User','1,000 Call Minutes','5 GB Recordings','Basic Features'],
  ],
  [
    'name'=>'Professional', 'price'=>'$29.00', 'period'=>'month',
    'popular'=>true, 'current'=>true,
    'features'=>['10 SIP Accounts','5 Users','5,000 Call Minutes','50 GB Recordings','Advanced Features','Priority Support'],
  ],
  [
    'name'=>'Enterprise', 'price'=>'$59.00', 'period'=>'month',
    'popular'=>false, 'current'=>false,
    'features'=>['Unlimited SIP Accounts','Unlimited Users','Unlimited Call Minutes','200 GB Recordings','All Advanced Features','Priority Support','Custom Integrations'],
  ],
];

$usage = [
  ['label'=>'Call Minutes',     'used'=>'1,250 / 5,000 mins', 'pct'=>25],
  ['label'=>'SIP Accounts',     'used'=>'3 / 10',             'pct'=>30],
  ['label'=>'Cloud Recordings', 'used'=>'10 GB / 50 GB',      'pct'=>20],
  ['label'=>'Seats / Users',    'used'=>'1 / 5',              'pct'=>20],
];

include __DIR__ . '/../includes/header.php';
?>

<!-- ============ Page header ============ -->
<div class="sub-head">
  <div class="sub-title">Subscription</div>
  <div class="sub-sub">Manage your plan, billing details and subscription usage.</div>
</div>

<!-- ============ Top stat cards ============ -->
<section class="sub-topcards">
  <div class="sub-topcard">
    <div class="sub-topcard-ic blue"><i class="fa-solid fa-crown"></i></div>
    <div class="sub-topcard-body">
      <div class="sub-topcard-label">Current Plan</div>
      <div class="sub-topcard-value">
        Professional
        <span class="badge-active">Active</span>
      </div>
    </div>
  </div>

  <div class="sub-topcard">
    <div class="sub-topcard-ic green"><i class="fa-regular fa-calendar"></i></div>
    <div class="sub-topcard-body">
      <div class="sub-topcard-label">Next Billing Date</div>
      <div class="sub-topcard-value">Jun 20, 2025</div>
      <div class="sub-topcard-note green">30 days remaining</div>
    </div>
  </div>

  <div class="sub-topcard">
    <div class="sub-topcard-ic orange"><i class="fa-solid fa-dollar-sign"></i></div>
    <div class="sub-topcard-body">
      <div class="sub-topcard-label">Amount</div>
      <div class="sub-topcard-value">$29.00 <span class="unit">/month</span></div>
      <div class="sub-topcard-note">Billed monthly</div>
    </div>
  </div>

  <div class="sub-topcard">
    <div class="sub-topcard-ic purple"><i class="fa-regular fa-credit-card"></i></div>
    <div class="sub-topcard-body">
      <div class="sub-topcard-label">Payment Method</div>
      <div class="sub-topcard-value">Visa •••• 4242</div>
      <div class="sub-topcard-note">Expires 12/28</div>
    </div>
    <a href="#" class="sub-topcard-edit">Edit</a>
  </div>
</section>

<!-- ============ Tabs ============ -->
<div class="reports-tabs">
  <button class="reports-tab active">Overview</button>
  <button class="reports-tab">Plans &amp; Pricing</button>
  <button class="reports-tab">Billing History</button>
  <button class="reports-tab">Payment Methods</button>
</div>

<!-- ============ Main 3-column grid ============ -->
<section class="sub-grid">
  <!-- =============== LEFT: Current Plan Details =============== -->
  <div class="card sub-plan-card">
    <div class="card-title">Current Plan Details</div>

    <div class="sub-plan-head">
      <div class="sub-plan-ic"><i class="fa-solid fa-crown"></i></div>
      <div>
        <div class="sub-plan-name">Professional Plan <span class="badge-active">Active</span></div>
        <div class="sub-plan-desc">Perfect for professionals and small teams who need advanced calling features.</div>
      </div>
    </div>

    <div class="sub-plan-rows">
      <div class="spr">
        <div class="spr-label"><i class="fa-regular fa-circle-dollar-to-slot"></i> Monthly Price</div>
        <div class="spr-value">$29.00</div>
      </div>
      <div class="spr">
        <div class="spr-label"><i class="fa-regular fa-clock"></i> Billing Cycle</div>
        <div class="spr-value">Monthly</div>
      </div>
      <div class="spr">
        <div class="spr-label"><i class="fa-regular fa-calendar"></i> Next Billing Date</div>
        <div class="spr-value">Jun 20, 2025</div>
      </div>
      <div class="spr">
        <div class="spr-label"><i class="fa-solid fa-users"></i> Seats / Users</div>
        <div class="spr-value">1 of 5</div>
      </div>
      <div class="spr">
        <div class="spr-label"><i class="fa-solid fa-server"></i> SIP Accounts</div>
        <div class="spr-value">3 of 10</div>
      </div>
      <div class="spr">
        <div class="spr-label"><i class="fa-solid fa-cloud"></i> Cloud Recordings</div>
        <div class="spr-value">10 GB of 50 GB</div>
      </div>
      <div class="spr">
        <div class="spr-label"><i class="fa-solid fa-phone"></i> Call Minutes</div>
        <div class="spr-value">1,250 of 5,000 mins</div>
      </div>
    </div>

    <div class="sub-plan-actions">
      <button class="btn btn-primary">
        <i class="fa-regular fa-credit-card"></i> Change Plan
      </button>
      <button class="btn btn-ghost">
        <i class="fa-regular fa-circle-xmark"></i> Cancel Subscription
      </button>
    </div>
  </div>

  <!-- =============== MIDDLE: Plans & Pricing =============== -->
  <div class="card sub-pricing-card">
    <div class="sub-pricing-head">
      <div class="card-title">Plans &amp; Pricing</div>
      <div class="cycle-toggle">
        <button class="ct-btn active">Monthly</button>
        <button class="ct-btn">
          Yearly <span class="save-pill">Save 20%</span>
        </button>
      </div>
    </div>

    <div class="plans-grid">
      <?php foreach ($plans as $p): ?>
        <div class="plan <?= $p['popular'] ? 'plan-popular' : '' ?>">
          <?php if ($p['popular']): ?>
            <div class="plan-ribbon">Most Popular</div>
          <?php endif; ?>
          <div class="plan-name"><?= htmlspecialchars($p['name']) ?></div>
          <div class="plan-price">
            <?= htmlspecialchars($p['price']) ?>
            <span>/<?= htmlspecialchars($p['period']) ?></span>
          </div>
          <div class="plan-billed">Billed monthly</div>

          <?php if ($p['current']): ?>
            <button class="plan-cta plan-current">Current Plan</button>
          <?php else: ?>
            <button class="plan-cta">Choose Plan</button>
          <?php endif; ?>

          <ul class="plan-features">
            <?php foreach ($p['features'] as $f): ?>
              <li><i class="fa-solid fa-check"></i> <?= htmlspecialchars($f) ?></li>
            <?php endforeach; ?>
          </ul>
        </div>
      <?php endforeach; ?>
    </div>
  </div>

  <!-- =============== RIGHT: Billing summary + Usage =============== -->
  <aside class="sub-side">
    <div class="card">
      <div class="card-title" style="margin-bottom:14px;">Billing Summary</div>
      <div class="billing-summary">
        <div class="bs-row"><span>Plan</span><span>Professional</span></div>
        <div class="bs-row"><span>Billing Cycle</span><span>Monthly</span></div>
        <div class="bs-row"><span>Amount</span><span>$29.00</span></div>
        <div class="bs-row"><span>Next Billing Date</span><span>Jun 20, 2025</span></div>
      </div>
      <button class="btn btn-ghost billing-history-btn">
        <i class="fa-regular fa-file-lines"></i> View Billing History
      </button>
    </div>

    <div class="card">
      <div class="card-title" style="margin-bottom:16px;">Usage Overview</div>
      <div class="usage-list">
        <?php foreach ($usage as $u): ?>
          <div class="usage-row">
            <div class="usage-top">
              <div class="usage-label"><?= htmlspecialchars($u['label']) ?></div>
              <div class="usage-value"><?= htmlspecialchars($u['used']) ?></div>
            </div>
            <div class="usage-bar-wrap">
              <div class="usage-bar" style="width: <?= $u['pct'] ?>%"></div>
            </div>
            <div class="usage-pct"><?= $u['pct'] ?>%</div>
          </div>
        <?php endforeach; ?>
      </div>
      <a href="#" class="usage-more">
        <i class="fa-solid fa-chart-line"></i> View Detailed Usage
      </a>
    </div>
  </aside>
</section>

<!-- ============ Contact sales banner ============ -->
<section class="custom-plan-banner">
  <div class="cpb-icon"><i class="fa-solid fa-info"></i></div>
  <div class="cpb-body">
    <div class="cpb-title">Need a custom plan?</div>
    <div class="cpb-sub">Contact our sales team for a tailored solution that fits your business needs.</div>
  </div>
  <button class="btn-outline cpb-btn">
    <i class="fa-solid fa-headset"></i> Contact Sales
  </button>
</section>

<?php include __DIR__ . '/../includes/footer.php'; ?>
