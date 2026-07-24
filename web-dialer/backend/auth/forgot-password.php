<?php
require_once __DIR__ . '/../bootstrap.php';

$in = read_json_body();
$err = validate($in, ['email' => 'required|email']);
if ($err) json_err('Please enter a valid email', 422, $err);

$stmt = db()->prepare("SELECT id, email FROM pkg_user WHERE email = ? LIMIT 1");
$stmt->execute([trim($in['email'])]);
$user = $stmt->fetch();

// Always return success to avoid leaking whether an email exists.
if ($user) {
  $token = bin2hex(random_bytes(24));
  db()->prepare("UPDATE pkg_user SET reset_token=?, reset_expires=DATE_ADD(NOW(), INTERVAL 1 HOUR) WHERE id=?")
      ->execute([$token, $user['id']]);
  log_line('auth', 'password reset requested', ['user'=>$user['email'], 'token'=>$token]);
  // TODO: send email with a link containing $token.
}
json_ok([], 'If that email exists, a reset link has been sent.');
