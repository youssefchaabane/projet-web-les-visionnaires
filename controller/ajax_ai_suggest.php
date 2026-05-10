<?php
declare(strict_types=1);

header('Content-Type: application/json; charset=utf-8');

require_once __DIR__ . '/allergiecontroller.php';
require_once __DIR__ . '/traitementcontroller.php';
require_once __DIR__ . '/AiController.php';

$idAllergie = isset($_GET['id_allergie']) ? (int)$_GET['id_allergie'] : 0;

if ($idAllergie <= 0) {
    echo json_encode(['error' => 'ID Allergie invalide', 'id_traitement' => null]);
    exit;
}

try {
    $ac = AllergieController::getInstance();
    $tc = TraitementController::getInstance();
    $ai = new AiController();

    // 1. Récupérer l'allergie
    $allergie = $ac->recupererAllergie($idAllergie);
    if (!$allergie) {
        echo json_encode(['error' => 'Allergie introuvable', 'id_traitement' => null]);
        exit;
    }

    // 2. Récupérer tous les traitements disponibles
    $traitements = $tc->afficherListeAdmin();
    if (empty($traitements)) {
        echo json_encode(['error' => 'Aucun traitement disponible', 'id_traitement' => null]);
        exit;
    }

    // 3. Demander la suggestion à l'IA
    $idSuggere = $ai->suggererTraitement($allergie, $traitements);

    echo json_encode(['success' => true, 'id_traitement' => $idSuggere]);
} catch (Throwable $e) {
    echo json_encode(['error' => 'Erreur serveur: ' . $e->getMessage(), 'id_traitement' => null]);
}
?>
