<?php
require_once __DIR__ . '/../bootstrap.php';
require_once __DIR__ . '/../middleware/auth.php';

$uid = auth_user_id();

// Time window: current week (Mon-Sun) vs previous week
$startCur  = date('Y-m-d 00:00:00', strtotime('monday this week'));
$startPrev = date('Y-m-d 00:00:00', strtotime('monday last week'));
$endPrev   = date('Y-m-d 23:59:59', strtotime('sunday last week'));

$q = function(string $sql, array $args = []) {
  $s = db()->prepare($sql); $s->execute($args); return $s->fetch();
};

$cur  = $q("SELECT COUNT(*) c,
              SUM(direction='outgoing') o, SUM(direction='incoming') i,
              SUM(direction='missed') m,   COALESCE(SUM(duration_sec),0) d
            FROM pkg_call WHERE user_id=? AND started_at >= ?", [$uid, $startCur]);
$prev = $q("SELECT COUNT(*) c FROM pkg_call
            WHERE user_id=? AND started_at BETWEEN ? AND ?", [$uid, $startPrev, $endPrev]);

$delta = fn($a, $b) => $b ? round((($a - $b) / max($b,1)) * 100, 1) : 0;

json_ok([
  'total'    => (int)$cur['c'],
  'outgoing' => (int)$cur['o'],
  'incoming' => (int)$cur['i'],
  'missed'   => (int)$cur['m'],
  'duration' => fmt_duration((int)$cur['d']),
  'delta_total' => $delta((int)$cur['c'], (int)$prev['c']),
]);
