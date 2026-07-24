<?php
/**
 * Application constants — edit as needed.
 */

// Base URL of the app (adjust for your deployment)
if (!defined('APP_URL')) {
  $scheme = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
  $host   = $_SERVER['HTTP_HOST'] ?? 'localhost';
  $script = dirname($_SERVER['SCRIPT_NAME'] ?? '');
  // Trim /app or /backend/... segments to find project root
  $root   = preg_replace('#/(app|backend)(/.*)?$#', '', $script);
  define('APP_URL', $scheme.'://'.$host.($root ?: ''));
}

define('APP_NAME',        'WebDialer');
define('APP_ROOT',        dirname(__DIR__, 2));      // /web-dialer
define('APP_BACKEND',     APP_ROOT . '/backend');
define('APP_FRONTEND',    APP_ROOT . '/app');
define('APP_UPLOADS',     APP_ROOT . '/uploads');
define('APP_STORAGE',     APP_ROOT . '/storage');

define('SESSION_LIFETIME', 60 * 60 * 24 * 7); // 7 days
define('DEFAULT_TIMEZONE', 'America/New_York');
date_default_timezone_set(DEFAULT_TIMEZONE);

// Errors — turn off in production
ini_set('display_errors', 0);
ini_set('log_errors', 1);
ini_set('error_log', APP_ROOT . '/storage/error.log');
error_reporting(E_ALL);
