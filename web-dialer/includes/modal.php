<?php
/**
 * Reusable modal component.
 *
 * PAGE-SPECIFIC USAGE (set variables BEFORE include):
 *
 *   $modalId     = 'confirmDelete';
 *   $modalTitle  = 'Delete Contact';
 *   $modalIcon   = 'fa-triangle-exclamation';
 *   $modalSize   = 'sm';   // sm | md | lg
 *   $modalBody   = '<p>Are you sure?</p>';
 *   $modalFooter = '<button class="btn btn-ghost" data-modal-close>Cancel</button>
 *                   <button class="btn btn-danger">Delete</button>';
 *   include __DIR__ . '/modal.php';
 *
 * TRIGGER FROM ANY BUTTON:
 *   <button data-modal-open="confirmDelete">Delete</button>
 *
 * NOTE:
 *   footer.php auto-includes this file WITHOUT any variables set,
 *   which renders the shared fallback modal id="appModal" you can
 *   inject content into via JS.
 */
$modalId     = $modalId     ?? 'appModal';
$modalTitle  = $modalTitle  ?? 'Dialog';
$modalIcon   = $modalIcon   ?? 'fa-circle-info';
$modalBody   = $modalBody   ?? '<p>Modal content goes here.</p>';
$modalFooter = $modalFooter ?? '<button class="btn btn-ghost" data-modal-close>Close</button>';
$modalSize   = $modalSize   ?? 'md';
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
