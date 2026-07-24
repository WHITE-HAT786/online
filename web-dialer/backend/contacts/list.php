<?php
require_once __DIR__ . '/../bootstrap.php';
require_once __DIR__ . '/../middleware/auth.php';
$uid = auth_user_id();

$search = trim($_GET['search'] ?? '');
$group  = trim($_GET['group']  ?? '');
$sql    = "SELECT * FROM pkg_contact WHERE user_id = ?";
$args   = [$uid];
if ($search !== '') {
  $sql .= " AND (first_name LIKE ? OR last_name LIKE ? OR phone LIKE ? OR email LIKE ?)";
  $like = "%$search%"; array_push($args, $like, $like, $like, $like);
}
if ($group !== '' && $group !== 'All Groups') {
  $sql .= " AND group_name = ?"; $args[] = $group;
}
$sql .= " ORDER BY first_name ASC, last_name ASC";

$stmt = db()->prepare($sql); $stmt->execute($args);
json_ok(['contacts' => $stmt->fetchAll()]);
