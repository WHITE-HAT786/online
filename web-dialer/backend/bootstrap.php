<?php
/**
 * Bootstraps every backend endpoint:
 *   config → session → DB → helpers → CORS + JSON header.
 * Endpoints just: require __DIR__ . '/../bootstrap.php';
 */
require_once __DIR__ . '/config/config.php';
require_once __DIR__ . '/config/session.php';
require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/helpers/response.php';
require_once __DIR__ . '/helpers/validator.php';
require_once __DIR__ . '/helpers/functions.php';
require_once __DIR__ . '/helpers/logger.php';

header('Content-Type: application/json; charset=utf-8');
header('X-Content-Type-Options: nosniff');
