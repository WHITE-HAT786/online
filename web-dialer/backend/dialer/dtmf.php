<?php
require_once __DIR__ . '/../bootstrap.php';
require_once __DIR__ . '/../middleware/auth.php';
require_once __DIR__ . '/../../asterisk/AsteriskManager.php';
$uid = auth_user_id();
$in  = read_json_body();
$callId = (int)($in['call_id'] ?? 0);
$digit  = substr((string)($in['digit'] ?? ''), 0, 1);
if ($digit === '') json_err('digit required', 422);

$ast = new AsteriskManager();
$resp = $ast->playDtmf($in['channel'] ?? '', $digit);

if ($callId) {
  db()->prepare("INSERT INTO pkg_call_dtmf(call_id, digit) VALUES(?,?)")
      ->execute([$callId, $digit]);
}
json_ok($resp, 'DTMF sent');
