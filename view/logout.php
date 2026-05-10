<?php
declare(strict_types=1);

require_once __DIR__ . '/partials/auth.php';

session_start();
$_SESSION = [];
if (ini_get('session.use_cookies')) {
    $params = session_get_cookie_params();
    setcookie(session_name(), '', time() - 42000, $params['path'], $params['domain'], (bool) $params['secure'], (bool) $params['httponly']);
}
session_destroy();

$base = app_base_from_script();
header('Location: ' . $base . '/view/login.php');
exit;
