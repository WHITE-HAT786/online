<?php
require_once __DIR__ . '/../bootstrap.php';
require_once __DIR__ . '/../middleware/auth.php';
$uid = auth_user_id();
$id  = (int)($_GET['id'] ?? 0);

$call = db()->prepare("SELECT c.*, s.account_name AS sip_name FROM pkg_call c
  LEFT JOIN pkg_sip s ON s.id = c.sip_id WHERE c.id=? AND c.user_id=? LIMIT 1");
$call->execute([$id, $uid]);
$row = $call->fetch();
if (!$row) json_err('Not found', 404);

$notes = db()->prepare("SELECT * FROM pkg_call_note WHERE call_id=? ORDER BY created_at DESC");
$notes->execute([$id]);
$row['notes'] = $notes->fetchAll();

$dtmf = db()->prepare("SELECT digit, created_at FROM pkg_call_dtmf WHERE call_id=? ORDER BY created_at ASC");
$dtmf->execute([$id]);
$row['dtmf'] = $dtmf->fetchAll();

json_ok($row);
