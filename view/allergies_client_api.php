<?php
declare(strict_types=1);
session_start();

if (!isset($_SESSION['user_id'])) {
    header('Content-Type: application/json');
    echo json_encode(['error' => 'Non autorisé']);
    exit;
}

require_once __DIR__ . '/../controller/metier.php';
$metier = new Metier();

header('Content-Type: application/json');

$action = $_GET['action'] ?? '';

if ($action === 'allergies_getAll') {
    $data = $metier->rechercherAllergies();
    echo json_encode(['success' => true, 'data' => $data]);
    exit;
}

if ($action === 'traitements_getAll') {
    $data = $metier->rechercherTraitements();
    echo json_encode(['success' => true, 'data' => $data]);
    exit;
}

if ($action === 'associations_getAll') {
    $data = $metier->rechercherAssociations(0, 0);
    echo json_encode(['success' => true, 'data' => $data]);
    exit;
}

echo json_encode(['error' => 'Action invalide']);
