<?php
/**
 * Authentication middleware.
 * Blocks API requests unless the user is logged in.
 */
require_once __DIR__ . '/../config/session.php';
require_once __DIR__ . '/../helpers/response.php';

if (!auth_check()) {
  json_err('Unauthenticated', 401);
}
