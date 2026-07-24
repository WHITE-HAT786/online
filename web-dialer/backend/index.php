<?php
/**
 * Optional: a tiny router if you'd rather POST /backend/index.php?endpoint=auth/login
 * instead of hitting each file directly. Not required — endpoints work as flat files too.
 */
require_once __DIR__ . '/bootstrap.php';

$endpoint = ltrim((string)($_GET['endpoint'] ?? ''), '/');
$endpoint = preg_replace('#\.\.+#', '', $endpoint);
if (!$endpoint) json_err('endpoint parameter required', 400);

$file = __DIR__ . '/' . $endpoint . '.php';
if (!file_exists($file)) json_err('Unknown endpoint', 404);
require $file;
