<?php
declare(strict_types=1);

header('Content-Type: application/json; charset=utf-8');

require_once __DIR__ . '/AiTraitementController.php';

$nom = isset($_GET['nom']) ? trim($_GET['nom']) : '';
$type = isset($_GET['type_traitement']) ? trim($_GET['type_traitement']) : '';

if ($nom === '' || $type === '') {
    echo json_encode(['error' => 'Le nom et le type sont requis', 'details' => null]);
    exit;
}

try {
    $ai = new AiTraitementController();

    // Demander la suggestion à l'IA
    $details = $ai->suggererDetailsTraitement($nom, $type);

    if ($details) {
        echo json_encode(['success' => true, 'details' => $details]);
    } else {
        echo json_encode(['error' => 'L\'IA n\'a pas pu fournir de détails', 'details' => null]);
    }
} catch (Throwable $e) {
    echo json_encode(['error' => 'Erreur serveur: ' . $e->getMessage(), 'details' => null]);
}
