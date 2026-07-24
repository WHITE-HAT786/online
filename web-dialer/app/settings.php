<?php
$pageTitle   = 'Settings';
$activeMenu  = 'settings';
$currentDate = 'May 20, 2025';
$currentTime = '10:30 AM';
$sipStatus   = 'registered';
$notifCount  = 3;
$breadcrumb  = [
  ['label' => 'Dashboard', 'href' => 'dashboard.php'],
  ['label' => 'Settings'],
];

$settingsNav = [
  ['key'=>'profile',       'label'=>'Profile Settings',    'icon'=>'fa-user'],
  ['key'=>'sip',           'label'=>'SIP & Calls',         'icon'=>'fa-phone'],
  ['key'=>'audio',         'label'=>'Audio / Video',       'icon'=>'fa-volume-high'],
  ['key'=>'notifications', 'label'=>'Notifications',       'icon'=>'fa-bell'],
  ['key'=>'security',      'label'=>'Security',            'icon'=>'fa-shield-halved'],
  ['key'=>'preferences',   'label'=>'Preferences',         'icon'=>'fa-sliders'],
  ['key'=>'integrations',  'label'=>'Integrations',        'icon'=>'fa-plug'],
  ['key'=>'plan',          'label'=>'Plan & Subscription', 'icon'=>'fa-credit-card'],
];
$activeSetting = 'profile';

include __DIR__ . '/../includes/header.php';
?>

<!-- ============ Page header ============ -->
<div class="sub-head">
  <div class="sub-title">Settings</div>
  <div class="sub-sub">Manage your account, preferences and application settings.</div>
</div>

<!-- ============ Layout ============ -->
<div class="settings-layout">
  <!-- =============== Nav sidebar =============== -->
  <aside class="settings-nav">
    <?php foreach ($settingsNav as $s): ?>
      <a href="#<?= $s['key'] ?>"
         class="settings-nav-item <?= $activeSetting === $s['key'] ? 'active' : '' ?>">
        <i class="fa-solid <?= $s['icon'] ?>"></i>
        <span><?= htmlspecialchars($s['label']) ?></span>
      </a>
    <?php endforeach; ?>
  </aside>

  <!-- =============== Main grid =============== -->
  <div class="settings-main">
    <!-- ---------- Profile Information ---------- -->
    <div class="card settings-card">
      <div class="settings-card-title">Profile Information</div>

      <div class="profile-form">
        <div class="avatar-upload">
          <img src="https://i.pravatar.cc/200?img=15" alt="Profile" />
          <button type="button" class="avatar-camera" aria-label="Change photo">
            <i class="fa-solid fa-camera"></i>
          </button>
        </div>

        <div class="profile-fields">
          <div class="field">
            <label>Full Name</label>
            <input type="text" value="John Doe" />
          </div>

          <div class="field">
            <label>Email Address</label>
            <input type="email" value="john.doe@example.com" />
          </div>

          <div class="field field-inline">
            <label>Phone Number</label>
            <div class="phone-input-group">
              <div class="phone-country">
                <span class="flag"></span>
                <i class="fa-solid fa-caret-down"></i>
              </div>
              <input type="tel" value="+1 (202) 555-0143" />
            </div>
          </div>

          <div class="field field-inline">
            <label>Language</label>
            <div class="select-native">
              <select>
                <option>English (US)</option>
                <option>English (UK)</option>
                <option>Spanish</option>
                <option>French</option>
                <option>German</option>
              </select>
              <i class="fa-solid fa-chevron-down caret"></i>
            </div>
          </div>

          <div class="field field-inline">
            <label>Time Zone</label>
            <div class="select-native">
              <select>
                <option>(UTC-05:00) Eastern Time (US &amp; Canada)</option>
                <option>(UTC-06:00) Central Time (US &amp; Canada)</option>
                <option>(UTC-07:00) Mountain Time (US &amp; Canada)</option>
                <option>(UTC-08:00) Pacific Time (US &amp; Canada)</option>
              </select>
              <i class="fa-solid fa-chevron-down caret"></i>
            </div>
          </div>
        </div>
      </div>

      <div class="form-actions">
        <button class="btn btn-primary">
          <i class="fa-regular fa-floppy-disk"></i> Save Changes
        </button>
      </div>
    </div>

    <!-- ---------- Change Password ---------- -->
    <div class="card settings-card">
      <div class="settings-card-title">Change Password</div>

      <div class="pw-form">
        <div class="pw-row">
          <label>Current Password</label>
          <div class="pw-input">
            <input type="password" placeholder="Enter current password" />
            <button type="button" class="toggle-eye-2" onclick="togglePw(this)"><i class="fa-regular fa-eye"></i></button>
          </div>
        </div>
        <div class="pw-row">
          <label>New Password</label>
          <div class="pw-input">
            <input type="password" placeholder="Enter new password" />
            <button type="button" class="toggle-eye-2" onclick="togglePw(this)"><i class="fa-regular fa-eye"></i></button>
          </div>
        </div>
        <div class="pw-row">
          <label>Confirm New Password</label>
          <div class="pw-input">
            <input type="password" placeholder="Confirm new password" />
            <button type="button" class="toggle-eye-2" onclick="togglePw(this)"><i class="fa-regular fa-eye"></i></button>
          </div>
        </div>
      </div>

      <div class="form-actions">
        <button class="btn btn-primary">
          <i class="fa-solid fa-key"></i> Update Password
        </button>
      </div>
    </div>

    <!-- ---------- Call Settings ---------- -->
    <div class="card settings-card">
      <div class="settings-card-title">Call Settings</div>

      <div class="call-settings">
        <div class="cs-row">
          <div class="cs-label">
            Default Caller ID
            <i class="fa-regular fa-circle-question" title="Choose which caller ID to use"></i>
          </div>
          <div class="select-native cs-control">
            <select>
              <option>John Doe &lt;+1 (202) 555-0143&gt;</option>
              <option>Sales &lt;+1 (202) 555-0187&gt;</option>
            </select>
            <i class="fa-solid fa-chevron-down caret"></i>
          </div>
        </div>

        <div class="cs-row">
          <div class="cs-label">Default SIP Account</div>
          <div class="select-native cs-control">
            <select>
              <option>Twilio Account</option>
              <option>Telnyx Account</option>
              <option>Bandwidth Account</option>
            </select>
            <i class="fa-solid fa-chevron-down caret"></i>
          </div>
        </div>

        <div class="cs-row">
          <div class="cs-label">Call Recording</div>
          <label class="switch">
            <input type="checkbox" checked>
            <span class="slider"></span>
          </label>
        </div>

        <div class="cs-row">
          <div class="cs-label">
            Auto Answer Calls
            <i class="fa-regular fa-circle-question"></i>
          </div>
          <label class="switch">
            <input type="checkbox">
            <span class="slider"></span>
          </label>
        </div>

        <div class="cs-row">
          <div class="cs-label">Dial Pad Sound</div>
          <label class="switch">
            <input type="checkbox" checked>
            <span class="slider"></span>
          </label>
        </div>

        <div class="cs-row">
          <div class="cs-label">
            Call End Sound
            <i class="fa-regular fa-circle-question"></i>
          </div>
          <label class="switch">
            <input type="checkbox">
            <span class="slider"></span>
          </label>
        </div>
      </div>
    </div>

    <!-- ---------- Two-Factor Authentication ---------- -->
    <div class="card settings-card">
      <div class="settings-card-title">
        Two-Factor Authentication
        <span class="badge-active">Enabled</span>
      </div>
      <p class="settings-card-desc">Add an extra layer of security to your account.</p>

      <div class="tfa-row">
        <div>
          <div class="tfa-name">Authenticator App</div>
          <div class="tfa-desc">Use an authenticator app to generate codes.</div>
        </div>
        <button class="btn-outline tfa-manage">
          <i class="fa-solid fa-shield-halved"></i> Manage 2FA
        </button>
      </div>
    </div>

    <!-- ---------- Theme & Appearance ---------- -->
    <div class="card settings-card">
      <div class="settings-card-title">Theme &amp; Appearance</div>

      <div class="theme-label">Theme</div>
      <div class="theme-grid">
        <button class="theme-opt active">
          <i class="fa-regular fa-sun"></i> Light
        </button>
        <button class="theme-opt">
          <i class="fa-regular fa-moon"></i> Dark
        </button>
        <button class="theme-opt">
          <i class="fa-solid fa-display"></i> System
        </button>
      </div>

      <div class="theme-label">Primary Color</div>
      <div class="color-grid">
        <button class="color-swatch active" style="background:#1a56ff"><i class="fa-solid fa-check"></i></button>
        <button class="color-swatch" style="background:#10b981"></button>
        <button class="color-swatch" style="background:#7c3aed"></button>
        <button class="color-swatch" style="background:#f59e0b"></button>
        <button class="color-swatch" style="background:#ef4444"></button>
        <button class="color-swatch" style="background:#14b8a6"></button>
      </div>
    </div>

    <!-- ---------- Other Settings ---------- -->
    <div class="card settings-card">
      <div class="settings-card-title">Other Settings</div>

      <div class="other-grid">
        <div class="field">
          <label>Data Retention</label>
          <div class="select-native">
            <select>
              <option>6 Months</option>
              <option>1 Year</option>
              <option>2 Years</option>
              <option>Forever</option>
            </select>
            <i class="fa-solid fa-chevron-down caret"></i>
          </div>
        </div>

        <div class="field">
          <label>Date Format</label>
          <div class="select-native">
            <select>
              <option>May 20, 2025 (MM DD, YYYY)</option>
              <option>20/05/2025 (DD/MM/YYYY)</option>
              <option>2025-05-20 (YYYY-MM-DD)</option>
            </select>
            <i class="fa-solid fa-chevron-down caret"></i>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

<?php include __DIR__ . '/../includes/footer.php'; ?>
