<?php
require_once __DIR__ . '/../bootstrap.php';
require_once __DIR__ . '/../middleware/auth.php';
$uid = auth_user_id();

if (empty($_FILES['file']['tmp_name'])) json_err('CSV file required', 422);
$fh = @fopen($_FILES['file']['tmp_name'], 'r');
if (!$fh) json_err('Could not read file', 400);

$header = fgetcsv($fh);           // first_name,last_name,company,phone,phone_type,email,group_name
$imported = 0;
$stmt = db()->prepare("INSERT INTO pkg_contact
  (user_id, first_name, last_name, company, phone, phone_type, email, group_name, avatar_color)
  VALUES (?,?,?,?,?,?,?,?,?)");

while (($row = fgetcsv($fh)) !== false) {
  if (empty($row[0]) || empty($row[3])) continue;
  $color = avatar_color_for($row[0].' '.($row[1] ?? ''));
  $stmt->execute([
    $uid,
    $row[0], $row[1] ?? null, $row[2] ?? null,
    $row[3], $row[4] ?? 'Mobile', $row[5] ?? null,
    $row[6] ?? 'Customers', $color,
  ]);
  $imported++;
}
fclose($fh);
json_ok(['imported' => $imported], "$imported contacts imported");
