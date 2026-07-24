<?php
require_once __DIR__ . '/../bootstrap.php';
require_once __DIR__ . '/../middleware/auth.php';
$uid = auth_user_id();

$in = read_json_body();
$err = validate($in, [
  'account_name' => 'required|min:2',
  'sip_username' => 'required',
  'sip_password' => 'required',
  'sip_server'   => 'required',
]);
if ($err) json_err('Missing required fields', 422, $err);

// If setting as default, unset others
if (!empty($in['is_default'])) {
  db()->prepare("UPDATE pkg_sip SET is_default=0 WHERE user_id=?")->execute([$uid]);
}

$stmt = db()->prepare("INSERT INTO pkg_sip
  (user_id,account_name,subtitle,sip_username,sip_password,sip_server,sip_port,transport,caller_id,is_default,icon,icon_color)
  VALUES (?,?,?,?,?,?,?,?,?,?,?,?)");
$stmt->execute([
  $uid,
  $in['account_name'],
  $in['subtitle']    ?? null,
  $in['sip_username'],
  $in['sip_password'],
  $in['sip_server'],
  (int)($in['sip_port'] ?? 5060),
  $in['transport']   ?? 'UDP',
  $in['caller_id']   ?? null,
  !empty($in['is_default']) ? 1 : 0,
  $in['icon']        ?? 'fa-circle-nodes',
  $in['icon_color']  ?? 'green',
]);
json_ok(['id' => (int)db()->lastInsertId()], 'Account added');
