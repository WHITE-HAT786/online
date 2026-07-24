<?php
require_once __DIR__ . '/../bootstrap.php';
require_once __DIR__ . '/../middleware/auth.php';
$uid = auth_user_id();

$sub = db()->prepare("SELECT * FROM pkg_subscription WHERE user_id=?");
$sub->execute([$uid]);
$s = $sub->fetch();

$sipUsed = (int)db()->query("SELECT COUNT(*) FROM pkg_sip WHERE user_id=$uid")->fetchColumn();
$minutes = (int)(db()->query("SELECT COALESCE(SUM(duration_sec),0)/60 FROM pkg_call
    WHERE user_id=$uid AND MONTH(started_at)=MONTH(CURDATE()) AND YEAR(started_at)=YEAR(CURDATE())")->fetchColumn());

json_ok([
  'call_minutes'    => ['used'=>$minutes,     'limit'=>(int)$s['minute_limit'], 'pct'=>$s['minute_limit'] ? round($minutes/$s['minute_limit']*100) : 0],
  'sip_accounts'    => ['used'=>$sipUsed,     'limit'=>(int)$s['sip_limit'],    'pct'=>$s['sip_limit']    ? round($sipUsed/$s['sip_limit']*100)    : 0],
  'cloud_recordings'=> ['used'=>10,           'limit'=>(int)$s['recording_gb'], 'pct'=>$s['recording_gb'] ? round(10/$s['recording_gb']*100)       : 0],
  'seats_users'     => ['used'=>1,            'limit'=>(int)$s['user_limit'],   'pct'=>$s['user_limit']   ? round(1/$s['user_limit']*100)          : 0],
]);
