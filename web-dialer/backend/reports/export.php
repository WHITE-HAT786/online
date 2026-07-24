<?php
require_once __DIR__ . '/../bootstrap.php';
require_once __DIR__ . '/../middleware/auth.php';
$uid  = auth_user_id();
$from = $_GET['from'] ?? date('Y-m-d', strtotime('-30 days'));
$to   = $_GET['to']   ?? date('Y-m-d');

$stmt = db()->prepare("SELECT c.*, s.account_name AS sip_name FROM pkg_call c
  LEFT JOIN pkg_sip s ON s.id=c.sip_id
  WHERE c.user_id=? AND DATE(c.started_at) BETWEEN ? AND ?
  ORDER BY c.started_at DESC");
$stmt->execute([$uid, $from, $to]);

header('Content-Type: text/csv; charset=utf-8');
header('Content-Disposition: attachment; filename="call_report_'.date('Y-m-d').'.csv"');
$out = fopen('php://output', 'w');
fputcsv($out, ['id','direction','from','to','sip','status','duration_sec','started_at','ended_at']);
while ($r = $stmt->fetch()) {
  fputcsv($out, [$r['id'],$r['direction'],$r['from_number'],$r['to_number'],$r['sip_name'],$r['status'],$r['duration_sec'],$r['started_at'],$r['ended_at']]);
}
fclose($out);
exit;
