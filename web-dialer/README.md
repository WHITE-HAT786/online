# WebDialer

Modular PHP template with shared header, sidebar, footer and modal.

## Project structure

```
web-dialer/
├── app/                         ← all page-level PHP files live here
│   ├── login.php
│   ├── signup.php
│   ├── dashboard.php
│   └── (contacts.php, dialer.php, settings.php, …)
├── includes/                    ← shared partials (never opened directly)
│   ├── header.php               ← <head> + topbar + sidebar (authenticated)
│   ├── sidebar.php              ← left navigation + user card
│   ├── footer.php               ← closes tags + auto-includes modal + JS
│   ├── modal.php                ← reusable modal component
│   ├── auth_header.php          ← <head> + branding panel (login/signup)
│   └── auth_footer.php          ← closes tags + loads JS
└── assets/
    ├── css/
    │   ├── style.css            ← dashboard / authenticated pages
    │   └── auth.css             ← login / signup / forgot-password
    └── js/app.js                ← sidebar toggle, modal, password toggle, keypad
```

## Path resolution

Every page in `app/` uses these relative paths — no configuration needed:

| From `app/*.php`   | Resolves to                     |
| ------------------ | ------------------------------- |
| `../includes/…`    | `web-dialer/includes/…`         |
| `../assets/css/…`  | `web-dialer/assets/css/…`       |
| `../assets/js/…`   | `web-dialer/assets/js/…`        |
| `dashboard.php`    | sibling in `app/` (sidebar links) |

## Creating a new authenticated page

```php
<?php
// app/contacts.php
$pageTitle  = 'Contacts';
$activeMenu = 'contacts';           // matches "key" in sidebar.php $menu
include __DIR__ . '/../includes/header.php';
?>

  <!-- YOUR PAGE HTML HERE -->
  <div class="card">
    <div class="card-title">Contacts</div>
    …
  </div>

<?php include __DIR__ . '/../includes/footer.php'; ?>
```

## Creating a new auth page (no sidebar)

```php
<?php
// app/forgot-password.php
$pageTitle    = 'Forgot Password';
$authSubtitle = 'Enter your email and we\'ll send a reset link.';
include __DIR__ . '/../includes/auth_header.php';
?>

  <div class="auth-card">
    <h2 class="auth-welcome">Reset Password</h2>
    <!-- form fields … -->
  </div>

<?php include __DIR__ . '/../includes/auth_footer.php'; ?>
```

## Overridable variables

### Authenticated pages (before `header.php`)
| Variable       | Default        | Purpose                              |
| -------------- | -------------- | ------------------------------------ |
| `$pageTitle`   | `Dashboard`    | `<title>` and topbar heading         |
| `$activeMenu`  | `dashboard`    | Highlights sidebar item              |
| `$currentDate` | `May 20, 2025` | Topbar date pill                     |
| `$currentTime` | `10:30 AM`     | Topbar time pill                     |
| `$sipStatus`   | `registered`   | `registered` (green) or `offline`    |
| `$notifCount`  | `3`            | Bell icon badge                      |
| `$user`        | Demo user      | `name`, `email`, `avatar`, `status`  |

### Auth pages (before `auth_header.php`)
| Variable        | Default                             |
| --------------- | ----------------------------------- |
| `$pageTitle`    | `Sign In`                           |
| `$authSubtitle` | Marketing tagline in left panel     |

## Using the modal

Set the modal vars anywhere in the page **before** the include, then trigger
it from any button using `data-modal-open="yourId"`:

```php
<?php
$modalId     = 'confirmDelete';
$modalTitle  = 'Delete Contact';
$modalIcon   = 'fa-triangle-exclamation';
$modalSize   = 'sm';   // sm | md | lg
$modalBody   = '<p>Are you sure?</p>';
$modalFooter = '<button class="btn btn-ghost" data-modal-close>Cancel</button>
                <button class="btn btn-danger">Delete</button>';
include __DIR__ . '/../includes/modal.php';
?>
```

```html
<button data-modal-open="confirmDelete">Delete</button>
```

Close by clicking the ✕, the backdrop, any element with `data-modal-close`,
or pressing `Esc`.

`footer.php` also auto-renders a fallback shared modal (`id="appModal"`)
that you can populate dynamically from JavaScript.

## Run locally

```bash
cd web-dialer
php -S localhost:8000
# → http://localhost:8000/app/login.php
# → http://localhost:8000/app/signup.php
# → http://localhost:8000/app/dashboard.php
```
