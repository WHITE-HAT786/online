<?php
/**
 * Shared header for AUTHENTICATED pages (dashboard, dialer, contacts, …)
 * Include this from any page in /app/.
 *
 * Override BEFORE including:
 *   $pageTitle, $activeMenu, $currentDate, $currentTime,
 *   $sipStatus ('registered'|'offline'), $notifCount, $user
 */
$pageTitle    = $pageTitle    ?? 'Dashboard';
$activeMenu   = $activeMenu   ?? 'dashboard';
$currentDate  = $currentDate  ?? 'May 20, 2025';
$currentTime  = $currentTime  ?? '10:30 AM';
$sipStatus    = $sipStatus    ?? 'registered';
$notifCount   = $notifCount   ?? 3;
$breadcrumb   = $breadcrumb   ?? []; // e.g. [['label'=>'Dashboard','href'=>'dashboard.php'],['label'=>'Contacts']]
$siteTitle    = 'WebDialer';
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title><?= htmlspecialchars($pageTitle) ?> — <?= $siteTitle ?></title>

  <link rel="stylesheet"
        href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" />
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap"
        rel="stylesheet" />
  <link rel="stylesheet" href="../assets/css/style.css?v=<?= @filemtime(__DIR__ . '/../assets/css/style.css') ?: time() ?>" />
  <script>
    // Apply stored theme immediately to avoid FOUC
    (function(){try{
      var t = localStorage.getItem('wd_theme') || 'light';
      if (t === 'system') t = window.matchMedia('(prefers-color-scheme: dark)').matches ? 'dark' : 'light';
      document.documentElement.setAttribute('data-theme', t);
    }catch(e){}})();
  </script>
  <script src="../assets/js/api.js?v=<?= @filemtime(__DIR__ . '/../assets/js/api.js') ?: time() ?>"></script>
</head>
<body>
  <div class="app">
    <?php include __DIR__ . '/sidebar.php'; ?>

    <div class="main">
      <header class="topbar">
        <div class="topbar-left">
          <button class="icon-btn" id="sidebarToggle" aria-label="Toggle sidebar">
            <i class="fa-solid fa-bars"></i>
          </button>
          <div class="page-heading">
            <h1 class="page-title"><?= htmlspecialchars($pageTitle) ?></h1>
            <?php if (!empty($breadcrumb)): ?>
              <nav class="breadcrumb" aria-label="Breadcrumb">
                <?php foreach ($breadcrumb as $i => $bc): ?>
                  <?php if ($i > 0): ?><i class="fa-solid fa-angle-right sep"></i><?php endif; ?>
                  <?php if (!empty($bc['href']) && $i < count($breadcrumb) - 1): ?>
                    <a href="<?= htmlspecialchars($bc['href']) ?>"><?= htmlspecialchars($bc['label']) ?></a>
                  <?php else: ?>
                    <span class="current"><?= htmlspecialchars($bc['label']) ?></span>
                  <?php endif; ?>
                <?php endforeach; ?>
              </nav>
            <?php endif; ?>
          </div>
        </div>

        <div class="topbar-right">
          <div class="sip-badge <?= $sipStatus === 'registered' ? 'ok' : 'off' ?>">
            <span class="dot"></span>
            SIP <?= $sipStatus === 'registered' ? 'Registered' : 'Offline' ?>
          </div>

          <button class="icon-btn notif" aria-label="Notifications">
            <i class="fa-regular fa-bell"></i>
            <?php if ($notifCount > 0): ?>
              <span class="badge"><?= (int)$notifCount ?></span>
            <?php endif; ?>
          </button>

          <div class="datetime">
            <i class="fa-regular fa-calendar"></i>
            <span class="date"><?= htmlspecialchars($currentDate) ?></span>
            <span class="time"><?= htmlspecialchars($currentTime) ?></span>
            <i class="fa-solid fa-chevron-down caret"></i>
          </div>
        </div>
      </header>

      <main class="content">
