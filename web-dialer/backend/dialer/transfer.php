<?php
require_once __DIR__ . '/../bootstrap.php';
require_once __DIR__ . '/../middleware/auth.php';
require_once __DIR__ . '/../../asterisk/AsteriskManager.php';
$in = read_json_body();
$ast = new AsteriskManager();
$resp = $ast->transfer($in['channel'] ?? '', $in['extension'] ?? '');
json_ok($resp, 'Call transferred');
