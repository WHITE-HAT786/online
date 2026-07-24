<?php
/**
 * JSON response helpers.
 */
function json_response($data, int $code = 200): void {
  http_response_code($code);
  header('Content-Type: application/json; charset=utf-8');
  echo json_encode($data);
  exit;
}
function json_ok($data = [], string $message = 'OK'): void {
  json_response(['success' => true, 'message' => $message, 'data' => $data], 200);
}
function json_err(string $message, int $code = 400, $data = null): void {
  json_response(['success' => false, 'message' => $message, 'data' => $data], $code);
}
function read_json_body(): array {
  $raw = file_get_contents('php://input');
  if (!$raw) return $_POST;
  $j = json_decode($raw, true);
  return is_array($j) ? $j : $_POST;
}
