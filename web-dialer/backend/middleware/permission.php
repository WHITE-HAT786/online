<?php
/**
 * Placeholder permission middleware.
 * Extend with roles/scopes as needed.
 */
require_once __DIR__ . '/auth.php';

function require_role(string ...$roles): void {
  // All authenticated users are 'user' for now. Extend with pkg_user.role later.
  return;
}
