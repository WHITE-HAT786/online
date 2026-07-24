# WebDialer — Modular PHP Template

Shared layout with reusable `header.php`, `sidebar.php`, `footer.php` and `modal.php`.

## File structure

```
webdialer/
├── dashboard.php               ← example page (start here)
├── includes/
│   ├── header.php              ← <head>, topbar, opens .content
│   ├── sidebar.php             ← left navigation + user card
│   ├── footer.php              ← closes .content + loads modal + JS
│   └── modal.php               ← reusable modal component
├── assets/
│   ├── css/style.css           ← all shared styling
│   └── js/app.js               ← sidebar toggle, modal, keypad
└── README.md
```

## Creating a new page

Any new page (e.g. `contacts.php`) is just:

```php
<?php
$pageTitle  = 'Contacts';
$activeMenu = 'contacts';       // matches the "key" in sidebar.php $menu
include __DIR__ . '/includes/header.php';
?>

  <!-- YOUR PAGE HTML HERE -->
  <div class="card">
    <div class="card-title">Contacts</div>
    ...
  </div>

<?php include __DIR__ . '/includes/footer.php'; ?>
```

That's it — the sidebar, topbar, footer and modal come along automatically.

## Overridable variables (set BEFORE `header.php`)

| Variable       | Default          | Purpose                                    |
| -------------- | ---------------- | ------------------------------------------ |
| `$pageTitle`   | `Dashboard`      | Shown in `<title>` and topbar heading      |
| `$activeMenu`  | `dashboard`      | Highlights the sidebar item                |
| `$currentDate` | `May 20, 2025`   | Topbar date pill                           |
| `$currentTime` | `10:30 AM`       | Topbar time pill                           |
| `$sipStatus`   | `registered`     | `registered` (green) or `offline` (red)    |
| `$notifCount`  | `3`              | Number badge on bell icon                  |
| `$user`        | John Doe demo    | Array: `name`, `email`, `avatar`, `status` |

## Using the modal

**Option A — page-specific modal** (set vars, then include):

```php
<?php
$modalId     = 'confirmDelete';
$modalTitle  = 'Delete Contact';
$modalIcon   = 'fa-triangle-exclamation';
$modalSize   = 'sm';               // sm | md | lg
$modalBody   = '<p>Are you sure you want to delete this contact?</p>';
$modalFooter = '<button class="btn btn-ghost" data-modal-close>Cancel</button>
                <button class="btn btn-danger">Delete</button>';
include __DIR__ . '/includes/modal.php';
?>
```

Trigger it from anywhere with:

```html
<button data-modal-open="confirmDelete">Delete</button>
```

**Option B — the shared empty modal** (`id="appModal"`) is rendered automatically
by `footer.php`. You can inject content into it dynamically from JS.

Close a modal with any element carrying `data-modal-close`, by clicking the
backdrop, or by pressing `Esc`.

## Requirements

- Any PHP 7+ environment (XAMPP, MAMP, `php -S localhost:8000` etc.)
- Internet access on the client for Font Awesome & Google Fonts CDNs

Run locally:

```bash
cd webdialer
php -S localhost:8000
# open http://localhost:8000/dashboard.php
```
