<?php
require_once __DIR__ . '/../bootstrap.php';
require_once __DIR__ . '/../middleware/auth.php';
$uid = auth_user_id();
$in  = read_json_body();
$id  = (int)($in['id'] ?? 0);
if (!$id) json_err('id required', 422);

$fields = ['first_name','last_name','company','phone','phone_type','email','group_name'];
$sets = []; $args = [];
foreach ($fields as $f) if (array_key_exists($f, $in)) { $sets[] = "$f = ?"; $args[] = $in[$f]; }
if (!$sets) json_err('No fields to update', 400);
$args[] = $id; $args[] = $uid;
db()->prepare("UPDATE pkg_contact SET ".implode(',', $sets)." WHERE id=? AND user_id=?")->execute($args);
json_ok([], 'Contact updated');
