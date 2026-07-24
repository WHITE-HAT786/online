<?php
require_once __DIR__ . '/../bootstrap.php';
require_once __DIR__ . '/../middleware/auth.php';
$uid = auth_user_id();

$stmt = db()->prepare("SELECT DATE_FORMAT(started_at, '%Y-%m') AS month,
    COUNT(*) AS total,
    SUM(direction='outgoing') AS outgoing,
    SUM(direction='incoming') AS incoming,
    SUM(direction='missed')   AS missed,
    COALESCE(SUM(duration_sec),0) AS duration
  FROM pkg_call WHERE user_id=?
  GROUP BY DATE_FORMAT(started_at, '%Y-%m') ORDER BY month DESC LIMIT 12");
$stmt->execute([$uid]);
$rows = $stmt->fetchAll();
foreach ($rows as &$r) $r['duration'] = fmt_duration((int)$r['duration']);
json_ok(['monthly' => $rows]);
