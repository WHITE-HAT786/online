<?php
require_once __DIR__ . '/../bootstrap.php';
require_once __DIR__ . '/../middleware/auth.php';
$uid = auth_user_id();
$in  = read_json_body();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $err = validate($in, ['call_id' => 'required|numeric', 'note' => 'required|min:1']);
  if ($err) json_err('Missing fields', 422, $err);

  // Ensure call belongs to user
  $chk = db()->prepare("SELECT id FROM pkg_call WHERE id=? AND user_id=?");
  $chk->execute([$in['call_id'], $uid]);
  if (!$chk->fetch()) json_err('Call not found', 404);

  db()->prepare("INSERT INTO pkg_call_note(call_id, user_id, note) VALUES(?,?,?)")
      ->execute([$in['call_id'], $uid, $in['note']]);
  json_ok([], 'Note added');
}
json_err('Method not allowed', 405);
