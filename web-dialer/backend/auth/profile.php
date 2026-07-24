<?php
require_once __DIR__ . '/../bootstrap.php';
require_once __DIR__ . '/../middleware/auth.php';

$uid = auth_user_id();

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
  $u = db()->prepare("SELECT id, full_name, username, email, phone, avatar, timezone, language FROM pkg_user WHERE id = ?");
  $u->execute([$uid]);
  json_ok($u->fetch() ?: []);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $in = read_json_body();
  $err = validate($in, ['fullname'=>'required|min:2','email'=>'required|email']);
  if ($err) json_err('Please check your form fields', 422, $err);

  db()->prepare("UPDATE pkg_user SET full_name=?, email=?, phone=?, timezone=?, language=? WHERE id=?")
      ->execute([
        trim($in['fullname']),
        trim($in['email']),
        $in['phone'] ?? null,
        $in['timezone'] ?? null,
        $in['language'] ?? null,
        $uid,
      ]);
  // Refresh session snapshot
  $_SESSION['user']['full_name'] = trim($in['fullname']);
  $_SESSION['user']['email']     = trim($in['email']);
  $_SESSION['user']['phone']     = $in['phone'] ?? null;
  json_ok([], 'Profile updated');
}

json_err('Method not allowed', 405);
