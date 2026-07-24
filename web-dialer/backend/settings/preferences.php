<?php
require_once __DIR__ . '/../bootstrap.php';
require_once __DIR__ . '/../middleware/auth.php';
$uid = auth_user_id();

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
  $s = db()->prepare("SELECT * FROM pkg_setting WHERE user_id=?");
  $s->execute([$uid]);
  json_ok($s->fetch() ?: []);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $in = read_json_body();
  $fields = ['default_caller_id','default_sip_id','call_recording','auto_answer','dial_pad_sound',
             'call_end_sound','theme','primary_color','data_retention','date_format','two_factor_enabled'];
  $sets=[]; $args=[];
  foreach ($fields as $f) if (array_key_exists($f, $in)) { $sets[]="$f=?"; $args[]=$in[$f]; }
  if (!$sets) json_err('No fields to update', 400);
  $args[] = $uid;
  db()->prepare("UPDATE pkg_setting SET ".implode(',', $sets)." WHERE user_id=?")->execute($args);
  json_ok([], 'Preferences saved');
}
json_err('Method not allowed', 405);
