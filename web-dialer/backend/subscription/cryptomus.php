<?php
/**
 * Cryptomus payment gateway client.
 * Docs: https://doc.cryptomus.com/business/payments
 *
 * Signing rule:
 *   md5( base64_encode(json_encode(payload)) . PAYMENT_API_KEY )
 */

// ---- Credentials (edit or override with env vars) --------------------------
if (!defined('CRYPTOMUS_ENABLED'))          define('CRYPTOMUS_ENABLED', true);
if (!defined('CRYPTOMUS_API_URL'))          define('CRYPTOMUS_API_URL', 'https://api.cryptomus.com/v1/payment');
if (!defined('CRYPTOMUS_MERCHANT_UUID'))    define('CRYPTOMUS_MERCHANT_UUID', getenv('CRYPTOMUS_MERCHANT_UUID')    ?: '1c168952-d61f-4349-8230-088e3e53d311');
if (!defined('CRYPTOMUS_PAYMENT_API_KEY'))  define('CRYPTOMUS_PAYMENT_API_KEY', getenv('CRYPTOMUS_PAYMENT_API_KEY')?: '1MGTi8eLvZYOrg3xS6SisS3hPVfazS26ju7KfXezMgDxoKBy8O55xacrME67uHa16tJSP6k9PEG4QY0dHtbWDyt1MawyVGm1wYkH2R54qFy2NbnN6w2itct1rpmwrXaZ');
if (!defined('CRYPTOMUS_WEBHOOK_SECRET'))   define('CRYPTOMUS_WEBHOOK_SECRET', getenv('CRYPTOMUS_WEBHOOK_SECRET') ?: '583e79ee-b7d2-405a-b66a-f17372784287');
if (!defined('CRYPTOMUS_MIN_USD_AMOUNT'))   define('CRYPTOMUS_MIN_USD_AMOUNT', 1);
// ---------------------------------------------------------------------------

class Cryptomus
{
  public static function createPayment(array $data): array
  {
    return self::request('', $data);
  }

  public static function paymentInfo(string $order_id): array
  {
    return self::request('/info', ['order_id' => $order_id]);
  }

  public static function verifyWebhook(array $payload, string $providedSign): bool
  {
    unset($payload['sign']);
    $json = json_encode($payload, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    $sign = md5(base64_encode($json) . CRYPTOMUS_WEBHOOK_SECRET);
    return hash_equals($sign, (string)$providedSign);
  }

  private static function request(string $path, array $payload): array
  {
    $url  = CRYPTOMUS_API_URL . $path;
    $json = json_encode($payload, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    $sign = md5(base64_encode($json) . CRYPTOMUS_PAYMENT_API_KEY);

    $ch = curl_init($url);
    curl_setopt_array($ch, [
      CURLOPT_POST            => true,
      CURLOPT_POSTFIELDS      => $json,
      CURLOPT_RETURNTRANSFER  => true,
      CURLOPT_TIMEOUT         => 15,
      CURLOPT_HTTPHEADER      => [
        'Content-Type: application/json',
        'merchant: ' . CRYPTOMUS_MERCHANT_UUID,
        'sign: ' . $sign,
      ],
    ]);
    $body = curl_exec($ch);
    $code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $err  = curl_error($ch);
    curl_close($ch);

    if ($err) return ['state' => -1, 'message' => $err];
    return json_decode($body, true) ?? ['state' => -1, 'raw' => $body, 'http' => $code];
  }
}
