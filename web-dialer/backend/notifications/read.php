<?php
require_once __DIR__ . '/../bootstrap.php';
require_once __DIR__ . '/../middleware/auth.php';
$uid = auth_user_id();
$in  = read_json_body();

if (!empty($in['all'])) {
  db()->prepare("UPDATE pkg_notification SET is_read=1 WHERE user_id=?")->execute([$uid]);
} else {
  $id = (int)($in['id'] ?? 0);
  if ($id) db()->prepare("UPDATE pkg_notification SET is_read=1 WHERE id=? AND user_id=?")->execute([$id, $uid]);
}
json_ok([], 'Marked as read');
