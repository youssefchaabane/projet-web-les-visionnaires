<?php
declare(strict_types=1);

// VIEW — suppression + redirect (PRG). PAS DE SQL.
require_once __DIR__ . '/../controller/utilisateurC.php';
require_once __DIR__ . '/partials/auth.php';
require_admin();

$controller = new UtilisateurC();
$id = isset($_GET['id']) ? (int) $_GET['id'] : 0;
$scriptName = str_replace('\\', '/', (string) ($_SERVER['SCRIPT_NAME'] ?? ''));
$appBase = (string) preg_replace('#/view/[^/]+$#', '', $scriptName);
$urlListe = str_replace(' ', '%20', $appBase . '/view/liste.php');

if ($id > 0) {
    $controller->supprimer($id);
}

header('Location: ' . $urlListe);
exit;

