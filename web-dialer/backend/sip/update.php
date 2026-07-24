<?php
require_once __DIR__ . '/../bootstrap.php';
require_once __DIR__ . '/../middleware/auth.php';
$uid = auth_user_id();
$in  = read_json_body();
$id  = (int)($in['id'] ?? 0);
if (!$id) json_err('id required', 422);

$fields = ['account_name','subtitle','sip_username','sip_password','sip_server','sip_port','transport','caller_id','is_default','icon','icon_color'];
$sets = []; $args = [];
foreach ($fields as $f) if (array_key_exists($f, $in)) { $sets[] = "$f = ?"; $args[] = $in[$f]; }
if (!$sets) json_err('No fields to update', 400);
$args[] = $id; $args[] = $uid;

if (!empty($in['is_default'])) db()->prepare("UPDATE pkg_sip SET is_default=0 WHERE user_id=?")->execute([$uid]);

db()->prepare("UPDATE pkg_sip SET ".implode(',', $sets)." WHERE id=? AND user_id=?")->execute($args);
json_ok([], 'Account updated');
