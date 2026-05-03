<?php
declare(strict_types=1);

if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}

function app_base_from_script(): string
{
    $scriptName = str_replace('\\', '/', (string) ($_SERVER['SCRIPT_NAME'] ?? ''));
    return (string) preg_replace('#/view/[^/]+$#', '', $scriptName);
}

function require_login(): void
{
    if (!isset($_SESSION['user_id']) || !isset($_SESSION['role'])) {
        $base = app_base_from_script();
        header('Location: ' . $base . '/view/login.php');
        exit;
    }
}

function require_admin(): void
{
    require_login();
    if ($_SESSION['role'] !== 'admin') {
        http_response_code(403);
        echo 'Accès refusé. Administrateur requis.';
        exit;
    }
}
