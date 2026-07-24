<?php
/**
 * Place a call via Asterisk AMI Originate.
 *   POST { sip_id, to_number, from_number? }
 */
require_once __DIR__ . '/../bootstrap.php';
require_once __DIR__ . '/../middleware/auth.php';
require_once __DIR__ . '/../../asterisk/AsteriskManager.php';

$uid = auth_user_id();
$in  = read_json_body();
$err = validate($in, ['to_number' => 'required']);
if ($err) json_err('Destination number is required', 422, $err);

// Pick SIP account: explicit id or user default
$sipId = (int)($in['sip_id'] ?? 0);
if ($sipId) {
  $stmt = db()->prepare("SELECT * FROM pkg_sip WHERE id=? AND user_id=? LIMIT 1");
  $stmt->execute([$sipId, $uid]);
} else {
  $stmt = db()->prepare("SELECT * FROM pkg_sip WHERE user_id=? AND is_default=1 LIMIT 1");
  $stmt->execute([$uid]);
}
$sip = $stmt->fetch();
if (!$sip) json_err('No SIP account available', 400);

$ast = new AsteriskManager();
$channel = ($ast->isEnabled() ? 'PJSIP/' : 'SIP/') . $sip['sip_username'];
$callerId = $sip['caller_id'] ?: ($in['from_number'] ?? '');
$resp = $ast->originate($channel, $in['to_number'], $callerId);

// Log call
$stmt = db()->prepare("INSERT INTO pkg_call
  (user_id, sip_id, direction, from_number, to_number, status, channel, unique_id, started_at)
  VALUES (?,?,?,?,?,?,?,?,NOW())");
$stmt->execute([
  $uid, $sip['id'], 'outgoing',
  $callerId ?: $sip['sip_username'],
  $in['to_number'],
  'completed',
  $channel,
  $resp['unique_id'] ?? null,
]);
$callId = (int)db()->lastInsertId();

json_ok(['call_id' => $callId, 'channel' => $channel, 'asterisk' => $resp], 'Call initiated');
