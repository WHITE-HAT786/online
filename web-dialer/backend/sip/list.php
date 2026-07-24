<?php
require_once __DIR__ . '/../bootstrap.php';
require_once __DIR__ . '/../middleware/auth.php';
$uid = auth_user_id();

$rows = db()->prepare("SELECT * FROM pkg_sip WHERE user_id=? ORDER BY is_default DESC, id ASC");
$rows->execute([$uid]);
$all = $rows->fetchAll();

$counts = ['total'=>count($all), 'registered'=>0, 'offline'=>0, 'disabled'=>0];
foreach ($all as $r) $counts[$r['status']] = ($counts[$r['status']] ?? 0) + 1;

json_ok(['accounts' => $all, 'counts' => $counts]);
