<?php
require_once __DIR__ . '/../bootstrap.php';
require_once __DIR__ . '/../middleware/auth.php';
require_once __DIR__ . '/../../asterisk/AsteriskManager.php';

$uid = auth_user_id();
$in  = read_json_body();
$callId = (int)($in['call_id'] ?? 0);
if (!$callId) json_err('call_id required', 422);

$call = db()->prepare("SELECT * FROM pkg_call WHERE id=? AND user_id=?");
$call->execute([$callId, $uid]);
$row = $call->fetch();
if (!$row) json_err('Call not found', 404);

$ast = new AsteriskManager();
$resp = $ast->hangup($row['channel'] ?? '');

$dur = max(0, strtotime('now') - strtotime($row['started_at']));
db()->prepare("UPDATE pkg_call SET status='completed', duration_sec=?, ended_at=NOW() WHERE id=?")
    ->execute([$dur, $callId]);

json_ok(['duration' => fmt_duration($dur), 'asterisk' => $resp], 'Call ended');
