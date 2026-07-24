<?php
require_once __DIR__ . '/../bootstrap.php';
require_once __DIR__ . '/../middleware/auth.php';
$uid = auth_user_id();

$stmt = db()->prepare("SELECT * FROM pkg_notification WHERE user_id=? ORDER BY created_at DESC LIMIT 25");
$stmt->execute([$uid]);
$unread = (int)db()->query("SELECT COUNT(*) FROM pkg_notification WHERE user_id=$uid AND is_read=0")->fetchColumn();
json_ok(['notifications' => $stmt->fetchAll(), 'unread' => $unread]);
