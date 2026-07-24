<?php
require_once __DIR__ . '/../bootstrap.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') json_err('Method not allowed', 405);

$in = read_json_body();
$err = validate($in, [
  'fullname' => 'required|min:2',
  'email'    => 'required|email',
  'username' => 'required|min:3',
  'password' => 'required|min:4',
]);
if ($err) json_err('Please check your form fields', 422, $err);

$stmt = db()->prepare("SELECT id FROM pkg_user WHERE username = :u OR email = :e LIMIT 1");
$stmt->execute([':u' => $in['username'], ':e' => $in['email']]);
if ($stmt->fetch()) json_err('An account with that username or email already exists', 409);

try {
  db()->beginTransaction();

  $stmt = db()->prepare("INSERT INTO pkg_user (full_name,username,email,phone,password,timezone)
    VALUES (:name,:username,:email,:phone,:password,:tz)");
  $stmt->execute([
    ':name'     => trim($in['fullname']),
    ':username' => trim($in['username']),
    ':email'    => trim($in['email']),
    ':phone'    => $in['phone']    ?? null,
    ':password' => $in['password'],
    ':tz'       => $in['timezone'] ?? '(UTC-05:00) Eastern Time (US & Canada)',
  ]);
  $uid = (int)db()->lastInsertId();

  db()->prepare("INSERT INTO pkg_setting(user_id) VALUES (?)")->execute([$uid]);
  db()->prepare("INSERT INTO pkg_subscription(user_id,plan_name,price,billing_cycle,status,next_billing,sip_limit,user_limit,minute_limit,recording_gb)
    VALUES (?, 'Basic', 9.00, 'monthly', 'active', DATE_ADD(CURDATE(), INTERVAL 30 DAY), 1, 1, 1000, 5)")->execute([$uid]);

  db()->commit();

  $u = db()->query("SELECT * FROM pkg_user WHERE id = $uid")->fetch();
  auth_login($u);
  json_ok(['user' => auth_user()], 'Account created');
} catch (Throwable $e) {
  db()->rollBack();
  log_line('errors', 'signup failed', ['e' => $e->getMessage()]);
  json_err('Could not create account. Please try again.', 500);
}
