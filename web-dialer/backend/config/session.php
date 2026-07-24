<?php
/**
 * Session bootstrap + helpers.
 */
if (session_status() === PHP_SESSION_NONE) {
  ini_set('session.use_strict_mode', 1);
  ini_set('session.cookie_httponly', 1);
  ini_set('session.cookie_samesite', 'Lax');
  session_set_cookie_params([
    'lifetime' => 0,
    'path'     => '/',
    'httponly' => true,
    'samesite' => 'Lax',
  ]);
  session_start();
}

function auth_user_id(): ?int {
  return isset($_SESSION['user_id']) ? (int)$_SESSION['user_id'] : null;
}

function auth_user(): ?array {
  return $_SESSION['user'] ?? null;
}

function auth_check(): bool {
  return auth_user_id() !== null;
}

function auth_login(array $user): void {
  session_regenerate_id(true);
  $_SESSION['user_id'] = (int)$user['id'];
  $_SESSION['user']    = [
    'id'        => (int)$user['id'],
    'full_name' => $user['full_name'],
    'username'  => $user['username'],
    'email'     => $user['email'],
    'phone'     => $user['phone']  ?? null,
    'avatar'    => $user['avatar'] ?? 'https://i.pravatar.cc/200?img=15',
  ];
}

function auth_logout(): void {
  $_SESSION = [];
  if (ini_get('session.use_cookies')) {
    $p = session_get_cookie_params();
    setcookie(session_name(), '', time() - 42000, $p['path'], $p['domain'] ?? '', $p['secure'] ?? false, $p['httponly'] ?? false);
  }
  session_destroy();
}
