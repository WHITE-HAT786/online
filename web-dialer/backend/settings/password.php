<?php
require_once __DIR__ . '/../bootstrap.php';
require_once __DIR__ . '/../middleware/auth.php';
$uid = auth_user_id();
$in  = read_json_body();
$err = validate($in, [
  'current_password' => 'required',
  'new_password'     => 'required|min:4',
  'confirm_password' => 'required',
]);
if ($err) json_err('Please fill all password fields', 422, $err);
if ($in['new_password'] !== $in['confirm_password']) json_err('New passwords do not match', 422);

$u = db()->prepare("SELECT password FROM pkg_user WHERE id=?");
$u->execute([$uid]);
$row = $u->fetch();
if (!$row || (string)$row['password'] !== (string)$in['current_password']) {
  json_err('Current password is incorrect', 401);
}
db()->prepare("UPDATE pkg_user SET password=? WHERE id=?")->execute([$in['new_password'], $uid]);
json_ok([], 'Password updated');
