<?php
require_once __DIR__ . '/../bootstrap.php';
require_once __DIR__ . '/../middleware/auth.php';
$uid = auth_user_id();

$from = $_GET['from'] ?? date('Y-m-d', strtotime('-7 days'));
$to   = $_GET['to']   ?? date('Y-m-d');

$stmt = db()->prepare("SELECT
    COUNT(*) AS total,
    SUM(direction='outgoing') AS outgoing,
    SUM(direction='incoming') AS incoming,
    SUM(direction='missed')   AS missed,
    COALESCE(SUM(duration_sec),0) AS duration
  FROM pkg_call
  WHERE user_id = ? AND DATE(started_at) BETWEEN ? AND ?");
$stmt->execute([$uid, $from, $to]);
$r = $stmt->fetch();

// distribution for donut
$dist = db()->prepare("SELECT
    SUM(status='completed') AS answered,
    SUM(status='no_answer' OR status='missed') AS no_answer,
    SUM(status='busy' OR status='failed') AS busy_failed
  FROM pkg_call WHERE user_id=? AND DATE(started_at) BETWEEN ? AND ?");
$dist->execute([$uid, $from, $to]);
$d = $dist->fetch();

// top SIP accounts
$top = db()->prepare("SELECT s.account_name, s.icon_color, COUNT(c.id) AS calls
  FROM pkg_sip s LEFT JOIN pkg_call c ON c.sip_id = s.id
  WHERE s.user_id = ?
  GROUP BY s.id ORDER BY calls DESC LIMIT 5");
$top->execute([$uid]);

json_ok([
  'stats'        => [
    'total'    => (int)$r['total'],
    'outgoing' => (int)$r['outgoing'],
    'incoming' => (int)$r['incoming'],
    'missed'   => (int)$r['missed'],
    'duration' => fmt_duration((int)$r['duration']),
  ],
  'distribution' => [
    'answered'   => (int)$d['answered'],
    'no_answer'  => (int)$d['no_answer'],
    'busy_failed'=> (int)$d['busy_failed'],
  ],
  'top_sip'      => $top->fetchAll(),
  'range'        => ['from' => $from, 'to' => $to],
]);
