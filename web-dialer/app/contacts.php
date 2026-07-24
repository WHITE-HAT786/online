<?php
require_once __DIR__ . '/../includes/auth_guard.php';

$pageTitle   = 'Contacts';
$activeMenu  = 'contacts';
$sipStatus   = 'registered';
$notifCount  = 3;
$breadcrumb  = [
  ['label' => 'Dashboard', 'href' => 'dashboard.php'],
  ['label' => 'Contacts'],
];

// Load from DB
$stmt = db()->prepare("SELECT * FROM pkg_contact WHERE user_id=? ORDER BY first_name ASC, last_name ASC");
$stmt->execute([auth_user_id()]);
$rows = $stmt->fetchAll();

$contacts = array_map(function($r){
  return [
    'id'         => (int)$r['id'],
    'initials'   => strtoupper(substr($r['first_name'],0,1) . substr($r['last_name']??'',0,1)),
    'name'       => trim($r['first_name'].' '.($r['last_name']??'')),
    'company'    => $r['company'],
    'phone'      => $r['phone'],
    'phone_type' => $r['phone_type'],
    'email'      => $r['email'],
    'group'      => $r['group_name'],
    'avatar'     => $r['avatar_color'],
  ];
}, $rows);

// Group tag color map
$groupColor = [
  'Customers' => 'blue',
  'Partners'  => 'purple',
  'Clients'   => 'green',
  'Suppliers' => 'orange',
];

include __DIR__ . '/../includes/header.php';
?>

<div class="card contacts-card">
  <!-- ============ Header ============ -->
  <div class="contacts-head">
    <div>
      <div class="contacts-title">Contacts</div>
      <div class="contacts-sub">Organize and manage your contacts for quick and easy calling.</div>
    </div>
    <div class="contacts-head-actions">
      <button class="btn-outline">
        <i class="fa-solid fa-arrow-up-from-bracket"></i> Import
      </button>
      <button class="btn-outline">
        <i class="fa-solid fa-arrow-down-to-bracket"></i> Export
      </button>
      <button class="btn btn-primary" data-modal-open="addContactModal">
        <i class="fa-solid fa-plus"></i> Add Contact
      </button>
    </div>
  </div>

  <!-- ============ Toolbar ============ -->
  <div class="contacts-toolbar">
    <div class="toolbar-search">
      <i class="fa-solid fa-magnifying-glass"></i>
      <input type="text" placeholder="Search by name, number or email..." />
    </div>

    <div class="toolbar-select">
      <select>
        <option>All Groups</option>
        <option>Customers</option>
        <option>Partners</option>
        <option>Clients</option>
        <option>Suppliers</option>
      </select>
      <i class="fa-solid fa-chevron-down caret"></i>
    </div>

    <div class="toolbar-right">
      <div class="toolbar-select small">
        <select>
          <option>10 per page</option>
          <option>25 per page</option>
          <option>50 per page</option>
          <option>100 per page</option>
        </select>
        <i class="fa-solid fa-chevron-down caret"></i>
      </div>
      <button class="icon-btn-outline" aria-label="Filter">
        <i class="fa-solid fa-filter"></i>
      </button>
    </div>
  </div>

  <!-- ============ Table ============ -->
  <div class="contacts-table-wrap">
    <table class="contacts-table">
      <thead>
        <tr>
          <th class="col-check">
            <label class="checkbox"><input type="checkbox" id="checkAll" /><span></span></label>
          </th>
          <th class="col-name">
            <button class="col-sort">Name <i class="fa-solid fa-caret-down"></i></button>
          </th>
          <th>Phone Number</th>
          <th>Email</th>
          <th>Group</th>
          <th class="col-actions">Actions</th>
        </tr>
      </thead>
      <tbody>
        <?php foreach ($contacts as $c):
          $groupCls = $groupColor[$c['group']] ?? 'blue';
        ?>
        <tr data-id="<?= (int)$c['id'] ?>">
          <td class="col-check">
            <label class="checkbox"><input type="checkbox" /><span></span></label>
          </td>
          <td>
            <div class="cell-name">
              <div class="avatar-circle avatar-<?= $c['avatar'] ?>"><?= htmlspecialchars($c['initials']) ?></div>
              <div>
                <div class="name-primary"><?= htmlspecialchars($c['name']) ?></div>
                <div class="name-secondary"><?= htmlspecialchars($c['company']) ?></div>
              </div>
            </div>
          </td>
          <td>
            <div class="cell-phone">
              <i class="fa-solid fa-phone phone-ic"></i>
              <div>
                <div class="phone-num"><?= htmlspecialchars($c['phone']) ?></div>
                <div class="phone-type"><?= htmlspecialchars($c['phone_type']) ?></div>
              </div>
            </div>
          </td>
          <td>
            <div class="cell-email">
              <i class="fa-regular fa-envelope email-ic"></i>
              <?= htmlspecialchars($c['email']) ?>
            </div>
          </td>
          <td>
            <span class="tag tag-<?= $groupCls ?>"><?= htmlspecialchars($c['group']) ?></span>
          </td>
          <td class="col-actions">
            <button class="row-action row-edit" title="Edit"><i class="fa-solid fa-pen"></i></button>
            <button class="row-action row-delete" title="Delete"><i class="fa-regular fa-trash-can"></i></button>
          </td>
        </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  </div>

  <!-- ============ Footer / Pagination ============ -->
  <div class="contacts-footer">
    <div class="pagination-info">Showing 1 to 10 of 45 contacts</div>
    <nav class="pagination">
      <button class="page-btn" disabled><i class="fa-solid fa-chevron-left"></i></button>
      <button class="page-btn active">1</button>
      <button class="page-btn">2</button>
      <button class="page-btn">3</button>
      <button class="page-btn">4</button>
      <button class="page-btn">5</button>
      <button class="page-btn"><i class="fa-solid fa-chevron-right"></i></button>
    </nav>
  </div>
</div>

<?php
// -------- Add Contact modal --------
$modalId    = 'addContactModal';
$modalTitle = 'Add Contact';
$modalIcon  = 'fa-user-plus';
$modalSize  = 'lg';
$modalBody  = '
  <div style="display:grid;gap:16px;">
    <div style="display:grid;grid-template-columns:1fr 1fr;gap:16px;">
      <div>
        <label style="font-size:13px;font-weight:600;display:block;margin-bottom:6px;">First Name</label>
        <input type="text" placeholder="John"
               style="width:100%;height:44px;border:1px solid var(--border);border-radius:10px;padding:0 14px;font-family:inherit;font-size:14px;">
      </div>
      <div>
        <label style="font-size:13px;font-weight:600;display:block;margin-bottom:6px;">Last Name</label>
        <input type="text" placeholder="Doe"
               style="width:100%;height:44px;border:1px solid var(--border);border-radius:10px;padding:0 14px;font-family:inherit;font-size:14px;">
      </div>
    </div>
    <div>
      <label style="font-size:13px;font-weight:600;display:block;margin-bottom:6px;">Company</label>
      <input type="text" placeholder="Acme Inc."
             style="width:100%;height:44px;border:1px solid var(--border);border-radius:10px;padding:0 14px;font-family:inherit;font-size:14px;">
    </div>
    <div style="display:grid;grid-template-columns:1.4fr 1fr;gap:16px;">
      <div>
        <label style="font-size:13px;font-weight:600;display:block;margin-bottom:6px;">Phone Number</label>
        <input type="tel" placeholder="+1 (202) 555-0143"
               style="width:100%;height:44px;border:1px solid var(--border);border-radius:10px;padding:0 14px;font-family:inherit;font-size:14px;">
      </div>
      <div>
        <label style="font-size:13px;font-weight:600;display:block;margin-bottom:6px;">Type</label>
        <select style="width:100%;height:44px;border:1px solid var(--border);border-radius:10px;padding:0 14px;font-family:inherit;font-size:14px;background:#fff;">
          <option>Mobile</option><option>Work</option><option>Home</option>
        </select>
      </div>
    </div>
    <div>
      <label style="font-size:13px;font-weight:600;display:block;margin-bottom:6px;">Email</label>
      <input type="email" placeholder="john@example.com"
             style="width:100%;height:44px;border:1px solid var(--border);border-radius:10px;padding:0 14px;font-family:inherit;font-size:14px;">
    </div>
    <div>
      <label style="font-size:13px;font-weight:600;display:block;margin-bottom:6px;">Group</label>
      <select style="width:100%;height:44px;border:1px solid var(--border);border-radius:10px;padding:0 14px;font-family:inherit;font-size:14px;background:#fff;">
        <option>Customers</option><option>Partners</option><option>Clients</option><option>Suppliers</option>
      </select>
    </div>
  </div>';
$modalFooter = '<button class="btn btn-ghost" data-modal-close>Cancel</button>
                <button class="btn btn-primary"><i class="fa-solid fa-plus"></i> Save Contact</button>';
include __DIR__ . '/../includes/modal.php';
?>

<?php include __DIR__ . '/../includes/footer.php'; ?>
