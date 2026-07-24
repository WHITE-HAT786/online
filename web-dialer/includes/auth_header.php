<?php
/**
 * Header for UNAUTHENTICATED pages (login, signup, forgot-password).
 * No sidebar / no topbar — just the split-screen auth layout.
 *
 * Override BEFORE including:
 *   $pageTitle, $authSubtitle
 */
$pageTitle    = $pageTitle    ?? 'Sign In';
$authSubtitle = $authSubtitle ?? 'Sign in to your WebDialer account';
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title><?= htmlspecialchars($pageTitle) ?> — WebDialer</title>

  <link rel="stylesheet"
        href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" />
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap"
        rel="stylesheet" />
  <link rel="stylesheet" href="../assets/css/auth.css?v=<?= @filemtime(__DIR__ . '/../assets/css/auth.css') ?: time() ?>" />
</head>
<body>
  <div class="auth-page">
    <!-- LEFT (branding) -->
    <aside class="auth-left">
      <div class="auth-logo">
        <div class="auth-logo-icon"><i class="fa-solid fa-wave-square"></i></div>
        <div class="auth-logo-text">WebDialer</div>
      </div>

      <h1 class="auth-headline">
        Smart Calling.
        <span class="accent">Better Connections.</span>
      </h1>

      <p class="auth-subtitle"><?= htmlspecialchars($authSubtitle) ?></p>

      <div class="auth-features">
        <div class="auth-feature">
          <div class="auth-feature-icon"><i class="fa-solid fa-phone"></i></div>
          <div>
            <div class="auth-feature-title">Crystal Clear Calls</div>
            <div class="auth-feature-desc">High quality calls with ultimate reliability.</div>
          </div>
        </div>
        <div class="auth-feature">
          <div class="auth-feature-icon"><i class="fa-solid fa-chart-simple"></i></div>
          <div>
            <div class="auth-feature-title">Advanced Analytics</div>
            <div class="auth-feature-desc">Real-time insights and detailed reports.</div>
          </div>
        </div>
        <div class="auth-feature">
          <div class="auth-feature-icon"><i class="fa-solid fa-shield-halved"></i></div>
          <div>
            <div class="auth-feature-title">Secure &amp; Reliable</div>
            <div class="auth-feature-desc">Enterprise grade security for your business.</div>
          </div>
        </div>
        <div class="auth-feature">
          <div class="auth-feature-icon"><i class="fa-solid fa-users"></i></div>
          <div>
            <div class="auth-feature-title">Scalable Solution</div>
            <div class="auth-feature-desc">Built to scale with your business needs.</div>
          </div>
        </div>
      </div>

      <div class="auth-world"></div>
    </aside>

    <!-- RIGHT (form container) -->
    <main class="auth-right">
