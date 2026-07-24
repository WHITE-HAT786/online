<?php
/**
 * MySQL PDO connection.
 * Edit credentials to match your local/remote MySQL server.
 */

// --------- Edit these to match your MySQL setup ---------
define('DB_HOST', '127.0.0.1');
define('DB_PORT', '3306');
define('DB_NAME', 'webdialer');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_CHARSET', 'utf8mb4');
// --------------------------------------------------------

function db(): PDO {
  static $pdo = null;
  if ($pdo === null) {
    $dsn = 'mysql:host='.DB_HOST.';port='.DB_PORT.';dbname='.DB_NAME.';charset='.DB_CHARSET;
    try {
      $pdo = new PDO($dsn, DB_USER, DB_PASS, [
        PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES   => false,
      ]);
    } catch (PDOException $e) {
      error_log('DB connection failed: '.$e->getMessage());
      http_response_code(500);
      header('Content-Type: application/json');
      echo json_encode(['success'=>false,'message'=>'Database connection failed.']);
      exit;
    }
  }
  return $pdo;
}
