<?php
require_once __DIR__ . '/../bootstrap.php';
require_once __DIR__ . '/../middleware/auth.php';
$uid = auth_user_id();

$stmt = db()->prepare("SELECT first_name,last_name,company,phone,phone_type,email,group_name FROM pkg_contact WHERE user_id=? ORDER BY first_name");
$stmt->execute([$uid]);

// Send CSV
header('Content-Type: text/csv; charset=utf-8');
header('Content-Disposition: attachment; filename="contacts.csv"');
$out = fopen('php://output', 'w');
fputcsv($out, ['first_name','last_name','company','phone','phone_type','email','group_name']);
while ($r = $stmt->fetch()) fputcsv($out, $r);
fclose($out);
exit;
