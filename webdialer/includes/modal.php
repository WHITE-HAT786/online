<?php
/**
 * Reusable Modal
 * -----------------------------------------------------------------------------
 * Usage from any page:
 *
 *   <?php
 *     $modalId      = 'addAccountModal';
 *     $modalTitle   = 'Add SIP Account';
 *     $modalIcon    = 'fa-server';
 *     $modalBody    = '<p>Your content HTML here…</p>';
 *     $modalFooter  = '<button class="btn btn-ghost" data-modal-close>Cancel</button>
 *                      <button class="btn btn-primary">Save</button>';
 *     include __DIR__ . '/includes/modal.php';
 *   ?>
 *
 * Trigger from any button:
 *   <button data-modal-open="addAccountModal">Add Account</button>
 *
 * Or use the shared empty modal below (id="appModal") and inject content via JS.
 * -----------------------------------------------------------------------------
 */

// Fallbacks – renders a single reusable shared modal when no vars are set
$modalId     = $modalId     ?? 'appModal';
$modalTitle  = $modalTitle  ?? 'Dialog';
$modalIcon   = $modalIcon   ?? 'fa-circle-info';
$modalBody   = $modalBody   ?? '<p>Modal content goes here.</p>';
$modalFooter = $modalFooter ?? '<button class="btn btn-ghost" data-modal-close>Close</button>';
$modalSize   = $modalSize   ?? 'md'; // sm | md | lg
?>
<div class="modal" id="<?= htmlspecialchars($modalId) ?>" role="dialog" aria-modal="true" aria-hidden="true">
  <div class="modal-backdrop" data-modal-close></div>
  <div class="modal-dialog modal-<?= htmlspecialchars($modalSize) ?>">
    <header class="modal-header">
      <div class="modal-title">
        <span class="modal-icon"><i class="fa-solid <?= htmlspecialchars($modalIcon) ?>"></i></span>
        <?= htmlspecialchars($modalTitle) ?>
      </div>
      <button class="modal-close" data-modal-close aria-label="Close">
        <i class="fa-solid fa-xmark"></i>
      </button>
    </header>
    <div class="modal-body">
      <?= $modalBody ?>
    </div>
    <?php if (!empty($modalFooter)): ?>
      <footer class="modal-footer">
        <?= $modalFooter ?>
      </footer>
    <?php endif; ?>
  </div>
</div>
