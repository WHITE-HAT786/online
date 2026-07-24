<?php
/**
 * POST /backend/subscription/checkout.php
 * Body: { plan: "Basic" | "Professional" | "Enterprise" }
 * Returns: { url: "<cryptomus hosted payment url>" }
 */
require_once __DIR__ . '/../bootstrap.php';
require_once __DIR__ . '/../middleware/auth.php';
require_once __DIR__ . '/cryptomus.php';

$uid = auth_user_id();
$in  = read_json_body();
$plan = $in['plan'] ?? '';

$plans = [
  'Basic'        => 9.00,
  'Professional' => 29.00,
  'Enterprise'   => 59.00,
];
if (!isset($plans[$plan])) json_err('Invalid plan', 422);
$amount = max(CRYPTOMUS_MIN_USD_AMOUNT, $plans[$plan]);
$orderId = 'ORD-' . $uid . '-' . time();

// Save pending order in a lightweight table (created on the fly if missing).
db()->exec("CREATE TABLE IF NOT EXISTS pkg_payment (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  user_id INT UNSIGNED NOT NULL,
  order_id VARCHAR(60) UNIQUE NOT NULL,
  plan VARCHAR(30) NOT NULL,
  amount DECIMAL(10,2) NOT NULL,
  currency VARCHAR(10) DEFAULT 'USD',
  status VARCHAR(30) DEFAULT 'pending',
  provider VARCHAR(30) DEFAULT 'cryptomus',
  uuid VARCHAR(120) DEFAULT NULL,
  url VARCHAR(500) DEFAULT NULL,
  raw JSON DEFAULT NULL,
  created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
  updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");

$appUrl = defined('APP_URL') ? APP_URL : (($_SERVER['HTTPS']?'https':'http').'://'.$_SERVER['HTTP_HOST']);

$resp = Cryptomus::createPayment([
  'amount'      => (string)$amount,
  'currency'    => 'USD',
  'order_id'    => $orderId,
  'url_return'  => $appUrl . '/app/subscription.php?paid=1',
  'url_success' => $appUrl . '/app/subscription.php?paid=1',
  'url_callback'=> $appUrl . '/backend/subscription/webhook.php',
  'is_payment_multiple' => false,
  'lifetime'    => 3600,
  'to_currency' => 'USDT',
]);

if (($resp['state'] ?? -1) !== 0) {
  log_line('cryptomus', 'create_failed', $resp);
  json_err($resp['message'] ?? 'Could not create payment', 400, $resp);
}

db()->prepare("INSERT INTO pkg_payment(user_id, order_id, plan, amount, uuid, url, raw)
  VALUES (?,?,?,?,?,?,?)")
   ->execute([$uid, $orderId, $plan, $amount, $resp['result']['uuid'] ?? null, $resp['result']['url'] ?? null, json_encode($resp)]);

json_ok([
  'url'      => $resp['result']['url']  ?? null,
  'uuid'     => $resp['result']['uuid'] ?? null,
  'order_id' => $orderId,
  'plan'     => $plan,
  'amount'   => $amount,
], 'Checkout created');
