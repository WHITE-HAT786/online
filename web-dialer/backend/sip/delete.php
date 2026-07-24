<?php
require_once __DIR__ . '/../bootstrap.php';
require_once __DIR__ . '/../middleware/auth.php';
$uid = auth_user_id();
$id  = (int)(read_json_body()['id'] ?? $_GET['id'] ?? 0);
if (!$id) json_err('id required', 422);
db()->prepare("DELETE FROM pkg_sip WHERE id=? AND user_id=?")->execute([$id, $uid]);
json_ok([], 'Account deleted');
