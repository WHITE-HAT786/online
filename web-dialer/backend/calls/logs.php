<?php
require_once __DIR__ . '/../bootstrap.php';
require_once __DIR__ . '/../middleware/auth.php';
$uid = auth_user_id();

$page  = max(1, (int)($_GET['page'] ?? 1));
$per   = min(100, max(5, (int)($_GET['per'] ?? 10)));
$dir   = $_GET['direction'] ?? '';
$sipId = (int)($_GET['sip_id'] ?? 0);
$status= $_GET['status'] ?? '';
$q     = trim($_GET['q'] ?? '');

$where = ["c.user_id = ?"]; $args = [$uid];
if ($dir   && in_array($dir, ['outgoing','incoming','missed'])) { $where[] = 'c.direction = ?'; $args[] = $dir; }
if ($sipId)  { $where[] = 'c.sip_id = ?'; $args[] = $sipId; }
if ($status && in_array($status, ['completed','missed','failed','busy','no_answer'])) { $where[] = 'c.status = ?'; $args[] = $status; }
if ($q !== '') { $where[] = '(c.from_number LIKE ? OR c.to_number LIKE ? OR s.account_name LIKE ?)'; array_push($args, "%$q%", "%$q%", "%$q%"); }

$sqlBase = "FROM pkg_call c LEFT JOIN pkg_sip s ON s.id = c.sip_id WHERE ".implode(' AND ', $where);
$total   = db()->prepare("SELECT COUNT(*) $sqlBase"); $total->execute($args);
$count   = (int)$total->fetchColumn();

$offset = ($page - 1) * $per;
$sql    = "SELECT c.*, s.account_name AS sip_name $sqlBase ORDER BY c.started_at DESC LIMIT $per OFFSET $offset";
$rows   = db()->prepare($sql); $rows->execute($args);

json_ok([
  'logs'     => $rows->fetchAll(),
  'total'    => $count,
  'page'     => $page,
  'per_page' => $per,
  'pages'    => (int)ceil($count / $per),
]);
