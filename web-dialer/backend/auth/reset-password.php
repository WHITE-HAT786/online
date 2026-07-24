<?php
require_once __DIR__ . '/../bootstrap.php';

$in = read_json_body();
$err = validate($in, ['token'=>'required','password'=>'required|min:4']);
if ($err) json_err('Missing token or password', 422, $err);

$stmt = db()->prepare("SELECT id FROM pkg_user WHERE reset_token = ? AND reset_expires > NOW() LIMIT 1");
$stmt->execute([$in['token']]);
$user = $stmt->fetch();
if (!$user) json_err('Invalid or expired reset token', 400);

db()->prepare("UPDATE pkg_user SET password=?, reset_token=NULL, reset_expires=NULL WHERE id=?")
    ->execute([$in['password'], $user['id']]);
json_ok([], 'Password updated. You can now sign in.');
