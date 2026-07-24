<?php
/**
 * Tiny file logger.
 */
function log_line(string $channel, string $message, array $context = []): void {
  $dir = dirname(__DIR__, 2) . '/storage';
  if (!is_dir($dir)) @mkdir($dir, 0775, true);
  $ts = date('Y-m-d H:i:s');
  $ctx = $context ? ' ' . json_encode($context) : '';
  @file_put_contents(
    $dir . '/' . $channel . '.log',
    "[$ts] $message$ctx" . PHP_EOL,
    FILE_APPEND
  );
}
