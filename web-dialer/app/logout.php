<?php
require_once __DIR__ . '/../backend/config/session.php';
auth_logout();
header('Location: login.php');
exit;
