<?php
require_once __DIR__ . '/../bootstrap.php';
require_once __DIR__ . '/../middleware/auth.php';
$uid  = auth_user_id();
$from = $_GET['from'] ?? date('Y-m-d', strtotime('-7 days'));
$to   = $_GET['to']   ?? date('Y-m-d');

$stmt = db()->prepare("SELECT DATE(started_at) AS d,
    COUNT(*) AS total,
    SUM(direction='outgoing') AS outgoing,
    SUM(direction='incoming') AS incoming,
    SUM(direction='missed')   AS missed,
    COALESCE(SUM(duration_sec),0) AS duration
  FROM pkg_call
  WHERE user_id=? AND DATE(started_at) BETWEEN ? AND ?
  GROUP BY DATE(started_at) ORDER BY d DESC");
$stmt->execute([$uid, $from, $to]);
$rows = $stmt->fetchAll();
foreach ($rows as &$r) {
  $r['duration']     = fmt_duration((int)$r['duration']);
  $r['avg_duration'] = fmt_duration($r['total'] > 0 ? (int)((int)$r['duration_sec'] ?? 0)/$r['total'] : 0);
}
json_ok(['daily' => $rows]);
