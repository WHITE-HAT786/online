<?php
require_once __DIR__ . '/../bootstrap.php';
require_once __DIR__ . '/../middleware/auth.php';
$uid = auth_user_id();
$sub = db()->prepare("SELECT * FROM pkg_subscription WHERE user_id=?");
$sub->execute([$uid]);
json_ok($sub->fetch() ?: []);
