<?php
$pageTitle   = 'Contacts';
$activeMenu  = 'contacts';
$currentDate = 'May 20, 2025';
$currentTime = '10:30 AM';
$sipStatus   = 'registered';
$notifCount  = 3;
$breadcrumb  = [
  ['label' => 'Dashboard', 'href' => 'dashboard.php'],
  ['label' => 'Contacts'],
];

// Sample data — replace with DB query in real usage
$contacts = [
  ['initials'=>'SM','name'=>'Sarah Miller',   'company'=>'Acme Inc.',         'phone'=>'+1 (202) 555-0143','phone_type'=>'Mobile','email'=>'sarah.miller@acme.com',      'group'=>'Customers','avatar'=>'green'],
  ['initials'=>'JB','name'=>'James Brown',    'company'=>'Globex Corporation','phone'=>'+1 (202) 555-0187','phone_type'=>'Mobile','email'=>'james.brown@globex.com',     'group'=>'Partners', 'avatar'=>'blue'],
  ['initials'=>'ET','name'=>'Emily Taylor',   'company'=>'Initech',           'phone'=>'+1 (202) 555-0129','phone_type'=>'Work',  'email'=>'emily.taylor@initech.com',   'group'=>'Clients',  'avatar'=>'red'],
  ['initials'=>'MW','name'=>'Michael Wilson', 'company'=>'Soylent Corp.',     'phone'=>'+1 (202) 555-0164','phone_type'=>'Mobile','email'=>'michael.wilson@soylent.com', 'group'=>'Customers','avatar'=>'orange'],
  ['initials'=>'OL','name'=>'Olivia Lee',     'company'=>'Umbrella Corp.',    'phone'=>'+1 (202) 555-0112','phone_type'=>'Work',  'email'=>'olivia.lee@umbrella.com',    'group'=>'Partners', 'avatar'=>'purple'],
  ['initials'=>'DC','name'=>'Daniel Clark',   'company'=>'Stark Industries',  'phone'=>'+1 (202) 555-0177','phone_type'=>'Mobile','email'=>'daniel.clark@stark.com',     'group'=>'Suppliers','avatar'=>'teal'],
  ['initials'=>'LC','name'=>'Laura Carter',   'company'=>'Wayne Enterprises', 'phone'=>'+1 (202) 555-0133','phone_type'=>'Work',  'email'=>'laura.carter@wayne.com',     'group'=>'Clients',  'avatar'=>'pink'],
];

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
        <tr>
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
