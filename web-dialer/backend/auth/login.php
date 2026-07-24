<?php
require_once __DIR__ . '/../bootstrap.php';
require_once __DIR__ . '/../middleware/rate-limit.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') json_err('Method not allowed', 405);
rate_limit('login', 8, 60);

$in = read_json_body();
$err = validate($in, ['username'=>'required','password'=>'required']);
if ($err) json_err('Please enter username and password', 422, $err);

$stmt = db()->prepare("SELECT * FROM pkg_user
  WHERE (username = :u OR email = :u) AND status = 'active' LIMIT 1");
$stmt->execute([':u' => trim($in['username'])]);
$user = $stmt->fetch();

// Plain text password check (per project decision)
if (!$user || (string)$user['password'] !== (string)$in['password']) {
  db()->prepare("INSERT INTO pkg_login_log(username,ip,user_agent,status) VALUES(?,?,?,'failed')")
      ->execute([$in['username'], client_ip(), user_agent()]);
  json_err('Invalid username or password', 401);
}

auth_login($user);
db()->prepare("INSERT INTO pkg_login_log(user_id,username,ip,user_agent,status) VALUES(?,?,?,?,'success')")
    ->execute([$user['id'], $user['username'], client_ip(), user_agent()]);

json_ok(['user' => auth_user()], 'Logged in');
