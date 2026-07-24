<?php
require_once __DIR__ . '/../includes/auth_guard.php';

$pageTitle   = 'Dialer';
$activeMenu  = 'dialer';
$sipStatus   = 'registered';
$notifCount  = 3;

include __DIR__ . '/../includes/header.php';
?>

<div class="dialer-page">
  <!-- ============ MAIN DIALER CARD ============ -->
  <section class="card dialer-card">
    <!-- Header row -->
    <div class="dialer-head">
      <div class="dialer-title">Dialer</div>
      <button class="btn-outline" data-modal-open="keypadSettingsModal">
        <i class="fa-solid fa-gear"></i> Keypad Settings
      </button>
    </div>

    <div class="dialer-grid">
      <!-- ================= LEFT (keypad) ================= -->
      <div class="dialer-left">
        <!-- Number input row -->
        <div class="dialpad-input-row">
          <div class="dialpad-country">
            <span class="flag"></span>
            <span class="code">+1</span>
            <i class="fa-solid fa-caret-down"></i>
          </div>
          <div class="dialpad-input">
            <input id="dialpadInput" type="text" placeholder="Enter number or name" />
            <button class="clear-input" aria-label="Clear"><i class="fa-solid fa-xmark"></i></button>
          </div>
        </div>

        <!-- Big keypad -->
        <div class="dialpad">
          <button class="dp-key" data-val="1"><span class="dp-num">1</span><span class="dp-sub">&nbsp;</span></button>
          <button class="dp-key" data-val="2"><span class="dp-num">2</span><span class="dp-sub">ABC</span></button>
          <button class="dp-key" data-val="3"><span class="dp-num">3</span><span class="dp-sub">DEF</span></button>
          <button class="dp-key" data-val="4"><span class="dp-num">4</span><span class="dp-sub">GHI</span></button>
          <button class="dp-key" data-val="5"><span class="dp-num">5</span><span class="dp-sub">JKL</span></button>
          <button class="dp-key" data-val="6"><span class="dp-num">6</span><span class="dp-sub">MNO</span></button>
          <button class="dp-key" data-val="7"><span class="dp-num">7</span><span class="dp-sub">PQRS</span></button>
          <button class="dp-key" data-val="8"><span class="dp-num">8</span><span class="dp-sub">TUV</span></button>
          <button class="dp-key" data-val="9"><span class="dp-num">9</span><span class="dp-sub">WXYZ</span></button>
          <button class="dp-key" data-val="*"><span class="dp-num">*</span><span class="dp-sub">,</span></button>
          <button class="dp-key" data-val="0"><span class="dp-num">0</span><span class="dp-sub">+</span></button>
          <button class="dp-key" data-val="#"><span class="dp-num">#</span><span class="dp-sub">&nbsp;</span></button>
        </div>

        <!-- Primary actions -->
        <div class="dp-primary-actions">
          <button class="dp-action" data-modal-open="addContactModal">
            <i class="fa-solid fa-user-plus"></i>
            <span>Add Contact</span>
          </button>
          <button class="dp-action dp-call">
            <i class="fa-solid fa-phone"></i>
            <span>Call</span>
          </button>
          <button class="dp-action" id="dpClearBtn">
            <i class="fa-solid fa-delete-left"></i>
            <span>Clear</span>
          </button>
        </div>

        <!-- Secondary actions -->
        <div class="dp-secondary-actions">
          <button class="dp-toggle">
            <i class="fa-solid fa-microphone"></i>
            <span>Mute</span>
          </button>
          <button class="dp-toggle">
            <i class="fa-solid fa-pause"></i>
            <span>Hold</span>
          </button>
          <button class="dp-toggle">
            <i class="fa-solid fa-right-left"></i>
            <span>Transfer</span>
          </button>
          <button class="dp-toggle">
            <i class="fa-solid fa-circle-dot" style="color:#ef4444"></i>
            <span>Record</span>
          </button>
        </div>
      </div>

      <!-- ================= RIGHT (call panel) ================= -->
      <div class="dialer-right">
        <!-- Active call -->
        <div class="active-call-wrap">
          <div class="active-call-label">ACTIVE CALL</div>
          <div class="active-call-card">
            <div class="ac-top">
              <div class="ac-avatar">
                JD
                <span class="ac-online"></span>
              </div>
              <div class="ac-info">
                <div class="ac-name">John Doe</div>
                <div class="ac-num">+1 (202) 555-0143</div>
                <div class="ac-timer">00:02:36</div>
              </div>
              <div class="ac-signal">
                <span></span><span></span><span></span><span></span>
              </div>
            </div>
            <div class="ac-actions">
              <button class="ac-btn">
                <i class="fa-solid fa-microphone"></i>
                <span>Mute</span>
              </button>
              <button class="ac-btn">
                <i class="fa-solid fa-pause"></i>
                <span>Hold</span>
              </button>
              <button class="ac-btn">
                <i class="fa-solid fa-right-left"></i>
                <span>Transfer</span>
              </button>
              <button class="ac-btn ac-btn-end">
                <i class="fa-solid fa-phone-slash"></i>
                <span>End Call</span>
              </button>
            </div>
          </div>
        </div>

        <!-- Recent calls -->
        <div class="recent-calls-wrap">
          <div class="rc-header">
            <div class="rc-title">RECENT CALLS</div>
            <a href="call-logs.php" class="rc-viewall">View All</a>
          </div>

          <div class="rc-list">
            <div class="rc-row">
              <div class="rc-avatar rc-green">SM</div>
              <i class="fa-solid fa-arrow-up-right rc-arrow out"></i>
              <div class="rc-info">
                <div class="rc-name">Sarah Miller</div>
                <div class="rc-num">+1 (202) 555-0143</div>
              </div>
              <div class="rc-meta">
                <div class="rc-time">10:28 AM</div>
                <div class="rc-dur in"><i class="fa-solid fa-phone"></i> 02:36</div>
              </div>
            </div>

            <div class="rc-row">
              <div class="rc-avatar rc-blue">JB</div>
              <i class="fa-solid fa-arrow-down-left rc-arrow in"></i>
              <div class="rc-info">
                <div class="rc-name">James Brown</div>
                <div class="rc-num">+1 (202) 555-0187</div>
              </div>
              <div class="rc-meta">
                <div class="rc-time">10:15 AM</div>
                <div class="rc-dur in"><i class="fa-solid fa-phone"></i> 05:12</div>
              </div>
            </div>

            <div class="rc-row">
              <div class="rc-avatar rc-red">ET</div>
              <i class="fa-solid fa-xmark rc-arrow miss"></i>
              <div class="rc-info">
                <div class="rc-name">Emily Taylor</div>
                <div class="rc-num">+1 (202) 555-0129</div>
              </div>
              <div class="rc-meta">
                <div class="rc-time">09:47 AM</div>
                <div class="rc-dur miss"><i class="fa-solid fa-phone-slash"></i> Missed</div>
              </div>
            </div>

            <div class="rc-row">
              <div class="rc-avatar rc-orange">MW</div>
              <i class="fa-solid fa-arrow-up-right rc-arrow out"></i>
              <div class="rc-info">
                <div class="rc-name">Michael Wilson</div>
                <div class="rc-num">+1 (202) 555-0164</div>
              </div>
              <div class="rc-meta">
                <div class="rc-time">09:30 AM</div>
                <div class="rc-dur in"><i class="fa-solid fa-phone"></i> 01:08</div>
              </div>
            </div>

            <div class="rc-row">
              <div class="rc-avatar rc-purple">OL</div>
              <i class="fa-solid fa-arrow-down-left rc-arrow in"></i>
              <div class="rc-info">
                <div class="rc-name">Olivia Lee</div>
                <div class="rc-num">+1 (202) 555-0112</div>
              </div>
              <div class="rc-meta">
                <div class="rc-time">09:12 AM</div>
                <div class="rc-dur in"><i class="fa-solid fa-phone"></i> 03:45</div>
              </div>
            </div>
          </div>

          <a href="#" class="rc-clear">
            <i class="fa-regular fa-trash-can"></i> Clear Call History
          </a>
        </div>
      </div>
    </div>
  </section>

  <!-- ============ FOOTER META STRIP ============ -->
  <section class="card dialer-meta">
    <div class="meta-item">
      <div class="meta-label">Current Account</div>
      <div class="meta-account">
        <div class="meta-account-icon"><i class="fa-solid fa-signal"></i></div>
        <div>
          <div class="meta-account-name">Twilio Account</div>
          <div class="meta-account-status">
            <span class="dot"></span> Registered
          </div>
        </div>
      </div>
    </div>

    <div class="meta-divider"></div>

    <div class="meta-item">
      <div class="meta-label">Caller ID</div>
      <div class="meta-caller">
        <div>
          <div class="meta-caller-name">John Doe</div>
          <div class="meta-caller-num">+1 (202) 555-0143</div>
        </div>
        <i class="fa-solid fa-chevron-down caret"></i>
      </div>
    </div>

    <div class="meta-divider"></div>

    <div class="meta-item">
      <div class="meta-label">Balance</div>
      <div class="meta-balance-row">
        <div class="meta-balance">$125.50</div>
        <button class="btn-outline btn-topup">
          <i class="fa-regular fa-credit-card"></i> Top Up
        </button>
      </div>
    </div>
  </section>
</div>

<?php
// -------- Add Contact modal --------
$modalId    = 'addContactModal';
$modalTitle = 'Add Contact';
$modalIcon  = 'fa-user-plus';
$modalSize  = 'md';
$modalBody  = '
  <div style="display:grid;gap:14px;">
    <div style="display:grid;grid-template-columns:1fr 1fr;gap:14px;">
      <div>
        <label style="font-size:13px;font-weight:600;">First Name</label>
        <input type="text" placeholder="John"
               style="width:100%;height:44px;border:1px solid var(--border);border-radius:10px;padding:0 14px;margin-top:6px;font-family:inherit;font-size:14px;">
      </div>
      <div>
        <label style="font-size:13px;font-weight:600;">Last Name</label>
        <input type="text" placeholder="Doe"
               style="width:100%;height:44px;border:1px solid var(--border);border-radius:10px;padding:0 14px;margin-top:6px;font-family:inherit;font-size:14px;">
      </div>
    </div>
    <div>
      <label style="font-size:13px;font-weight:600;">Phone Number</label>
      <input type="tel" placeholder="+1 (202) 555-0143"
             style="width:100%;height:44px;border:1px solid var(--border);border-radius:10px;padding:0 14px;margin-top:6px;font-family:inherit;font-size:14px;">
    </div>
    <div>
      <label style="font-size:13px;font-weight:600;">Email (optional)</label>
      <input type="email" placeholder="john@example.com"
             style="width:100%;height:44px;border:1px solid var(--border);border-radius:10px;padding:0 14px;margin-top:6px;font-family:inherit;font-size:14px;">
    </div>
  </div>';
$modalFooter = '<button class="btn btn-ghost" data-modal-close>Cancel</button>
                <button class="btn btn-primary"><i class="fa-solid fa-plus"></i> Save Contact</button>';
include __DIR__ . '/../includes/modal.php';

// Reset so next include starts fresh
unset($modalId, $modalTitle, $modalIcon, $modalSize, $modalBody, $modalFooter);

// -------- Keypad Settings modal --------
$modalId    = 'keypadSettingsModal';
$modalTitle = 'Keypad Settings';
$modalIcon  = 'fa-gear';
$modalSize  = 'md';
$modalBody  = '
  <div style="display:grid;gap:16px;">
    <label style="display:flex;justify-content:space-between;align-items:center;">
      <span style="font-weight:500;">DTMF tones</span>
      <input type="checkbox" checked>
    </label>
    <label style="display:flex;justify-content:space-between;align-items:center;">
      <span style="font-weight:500;">Vibration on tap</span>
      <input type="checkbox">
    </label>
    <label style="display:flex;justify-content:space-between;align-items:center;">
      <span style="font-weight:500;">Show letters below numbers</span>
      <input type="checkbox" checked>
    </label>
    <label style="display:flex;justify-content:space-between;align-items:center;">
      <span style="font-weight:500;">Auto-format phone numbers</span>
      <input type="checkbox" checked>
    </label>
  </div>';
$modalFooter = '<button class="btn btn-ghost" data-modal-close>Cancel</button>
                <button class="btn btn-primary">Save Changes</button>';
include __DIR__ . '/../includes/modal.php';
?>

<?php include __DIR__ . '/../includes/footer.php'; ?>
