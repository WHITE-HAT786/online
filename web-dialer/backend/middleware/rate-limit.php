<?php
/**
 * Rate limiter — session bucket per action key.
 *   rate_limit('login', 5, 60)  → max 5 login attempts / 60 sec.
 */
require_once __DIR__ . '/../config/session.php';
require_once __DIR__ . '/../helpers/response.php';

function rate_limit(string $key, int $max = 10, int $window = 60): void {
  $now  = time();
  $bkt  = &$_SESSION['_rate'][$key];
  if (!$bkt || ($now - $bkt['start']) > $window) {
    $bkt = ['start' => $now, 'count' => 0];
  }
  $bkt['count']++;
  if ($bkt['count'] > $max) {
    json_err('Too many requests. Please try again later.', 429);
  }
}
