<?php
require_once __DIR__ . '/../bootstrap.php';
auth_logout();

// If accessed as page → redirect. If AJAX → JSON.
if (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) || ($_SERVER['REQUEST_METHOD'] === 'POST' && !isset($_GET['redirect']))) {
  json_ok([], 'Logged out');
} else {
  header('Location: ../../app/login.php');
  exit;
}
