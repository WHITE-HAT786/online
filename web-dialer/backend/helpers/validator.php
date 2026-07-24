<?php
/**
 * Simple input validator.
 * Usage: $v = validate($input, ['email' => 'required|email', 'name' => 'required|min:2']);
 */
function validate(array $input, array $rules): array {
  $errors = [];
  foreach ($rules as $field => $ruleset) {
    $value = trim((string)($input[$field] ?? ''));
    foreach (explode('|', $ruleset) as $rule) {
      [$name, $arg] = array_pad(explode(':', $rule, 2), 2, null);
      switch ($name) {
        case 'required':
          if ($value === '') $errors[$field][] = "$field is required";
          break;
        case 'email':
          if ($value !== '' && !filter_var($value, FILTER_VALIDATE_EMAIL)) $errors[$field][] = "$field must be a valid email";
          break;
        case 'min':
          if ($value !== '' && mb_strlen($value) < (int)$arg) $errors[$field][] = "$field must be at least $arg chars";
          break;
        case 'max':
          if ($value !== '' && mb_strlen($value) > (int)$arg) $errors[$field][] = "$field must be at most $arg chars";
          break;
        case 'numeric':
          if ($value !== '' && !is_numeric($value)) $errors[$field][] = "$field must be numeric";
          break;
        case 'in':
          $allowed = explode(',', $arg ?? '');
          if ($value !== '' && !in_array($value, $allowed, true)) $errors[$field][] = "$field must be one of: $arg";
          break;
      }
    }
  }
  return $errors;
}
