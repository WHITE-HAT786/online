<?php
/**
 * Generic helper functions used across the backend.
 */
function e(?string $s): string { return htmlspecialchars((string)$s, ENT_QUOTES, 'UTF-8'); }

function client_ip(): string {
  foreach (['HTTP_CF_CONNECTING_IP','HTTP_X_FORWARDED_FOR','REMOTE_ADDR'] as $k) {
    if (!empty($_SERVER[$k])) return trim(explode(',', $_SERVER[$k])[0]);
  }
  return '0.0.0.0';
}

function user_agent(): string {
  return substr($_SERVER['HTTP_USER_AGENT'] ?? '', 0, 250);
}

/** Format seconds → HH:MM:SS */
function fmt_duration(int $sec): string {
  $h = floor($sec / 3600);
  $m = floor(($sec % 3600) / 60);
  $s = $sec % 60;
  return sprintf('%02d:%02d:%02d', $h, $m, $s);
}

/** Deterministic avatar color based on name */
function avatar_color_for(string $name): string {
  $pool = ['blue','green','red','orange','purple','teal','pink'];
  return $pool[abs(crc32($name)) % count($pool)];
}
