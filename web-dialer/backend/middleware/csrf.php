<?php
/**
 * CSRF token helpers.
 *   csrf_token()      → generate/get token for this session
 *   csrf_check($t)    → validates supplied token
 *   csrf_enforce()    → aborts request with 403 if token missing/invalid
 */
require_once __DIR__ . '/../config/session.php';
require_once __DIR__ . '/../helpers/response.php';

function csrf_token(): string {
  if (empty($_SESSION['_csrf'])) {
    $_SESSION['_csrf'] = bin2hex(random_bytes(32));
  }
  return $_SESSION['_csrf'];
}

function csrf_check(?string $token): bool {
  return !empty($token) && !empty($_SESSION['_csrf']) && hash_equals($_SESSION['_csrf'], $token);
}

function csrf_enforce(): void {
  $t = $_POST['_csrf'] ?? $_SERVER['HTTP_X_CSRF_TOKEN'] ?? null;
  if (!$t) {
    $j = json_decode(file_get_contents('php://input'), true);
    $t = $j['_csrf'] ?? null;
  }
  if (!csrf_check($t)) json_err('Invalid CSRF token', 403);
}
