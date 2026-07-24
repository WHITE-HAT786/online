<?php
/**
 * Cryptomus webhook — receives payment status updates.
 * URL: /backend/subscription/webhook.php  (set as url_callback when creating payment)
 */
require_once __DIR__ . '/../bootstrap.php';
require_once __DIR__ . '/cryptomus.php';

$raw     = file_get_contents('php://input');
$payload = json_decode($raw, true) ?: [];
$sign    = $payload['sign'] ?? '';

if (!Cryptomus::verifyWebhook($payload, $sign)) {
  log_line('cryptomus', 'webhook_invalid_sign', $payload);
  http_response_code(400);
  echo 'invalid sign'; exit;
}

$orderId = $payload['order_id'] ?? '';
$status  = $payload['status']   ?? 'pending';   // paid | paid_over | check | fail | wrong_amount ...

$stmt = db()->prepare("SELECT user_id, plan FROM pkg_payment WHERE order_id=? LIMIT 1");
$stmt->execute([$orderId]);
$pay = $stmt->fetch();
if (!$pay) { http_response_code(404); echo 'unknown order'; exit; }

db()->prepare("UPDATE pkg_payment SET status=?, raw=? WHERE order_id=?")
    ->execute([$status, json_encode($payload), $orderId]);

// On successful payment, activate the plan.
if (in_array($status, ['paid','paid_over'], true)) {
  $planPrice = ['Basic'=>9.00, 'Professional'=>29.00, 'Enterprise'=>59.00][$pay['plan']] ?? 0;
  $limits = [
    'Basic'        => ['sip'=>1, 'user'=>1, 'min'=>1000,   'rec'=>5],
    'Professional' => ['sip'=>10,'user'=>5, 'min'=>5000,   'rec'=>50],
    'Enterprise'   => ['sip'=>999,'user'=>999,'min'=>999999,'rec'=>200],
  ][$pay['plan']] ?? ['sip'=>1,'user'=>1,'min'=>1000,'rec'=>5];

  db()->prepare("UPDATE pkg_subscription SET plan_name=?, price=?, status='active',
      next_billing = DATE_ADD(CURDATE(), INTERVAL 30 DAY),
      sip_limit=?, user_limit=?, minute_limit=?, recording_gb=?
    WHERE user_id=?")
    ->execute([$pay['plan'], $planPrice, $limits['sip'], $limits['user'], $limits['min'], $limits['rec'], $pay['user_id']]);

  db()->prepare("INSERT INTO pkg_notification(user_id,type,title,message)
    VALUES(?,?,?,?)")
    ->execute([$pay['user_id'], 'billing', 'Subscription activated', "Your {$pay['plan']} plan is now active."]);
}

http_response_code(200);
echo 'ok';
