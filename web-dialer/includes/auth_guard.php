<?php
/**
 * Include this AT THE TOP of every authenticated frontend page.
 *
 *   <?php require_once __DIR__ . '/../includes/auth_guard.php'; ?>
 *
 * If the user isn't logged in, they are redirected to login.php.
 */
require_once __DIR__ . '/../backend/config/config.php';
require_once __DIR__ . '/../backend/config/session.php';
require_once __DIR__ . '/../backend/config/database.php';
require_once __DIR__ . '/../backend/helpers/functions.php';

if (!auth_check()) {
  header('Location: login.php');
  exit;
}

// Merge session user with sidebar defaults so header/sidebar just work.
$u = auth_user();
$user = [
  'name'   => $u['full_name'] ?? 'User',
  'email'  => $u['email'] ?? '',
  'avatar' => $u['avatar'] ?? 'https://i.pravatar.cc/200?img=15',
  'status' => 'online',
];
$currentDate = date('M j, Y');
$currentTime = date('g:i A');
