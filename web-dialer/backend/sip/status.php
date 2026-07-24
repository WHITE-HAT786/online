<?php
require_once __DIR__ . '/../bootstrap.php';
require_once __DIR__ . '/../middleware/auth.php';
$uid = auth_user_id();
$id  = (int)($_GET['id'] ?? 0);
$stmt = db()->prepare("SELECT id, account_name, status, last_registered FROM pkg_sip WHERE id=? AND user_id=?");
$stmt->execute([$id, $uid]);
json_ok($stmt->fetch() ?: []);
