<?php
require_once __DIR__ . '/../bootstrap.php';
require_once __DIR__ . '/../middleware/auth.php';
$uid = auth_user_id();
$in  = read_json_body();
$err = validate($in, ['first_name'=>'required|min:1', 'phone'=>'required']);
if ($err) json_err('Missing required fields', 422, $err);

$color = avatar_color_for(($in['first_name'].' '.($in['last_name']??'')));
db()->prepare("INSERT INTO pkg_contact
  (user_id, first_name, last_name, company, phone, phone_type, email, group_name, avatar_color)
  VALUES (?,?,?,?,?,?,?,?,?)")
  ->execute([
    $uid,
    $in['first_name'],
    $in['last_name'] ?? null,
    $in['company']   ?? null,
    $in['phone'],
    $in['phone_type']?? 'Mobile',
    $in['email']     ?? null,
    $in['group_name']?? 'Customers',
    $color,
  ]);
json_ok(['id' => (int)db()->lastInsertId()], 'Contact added');
