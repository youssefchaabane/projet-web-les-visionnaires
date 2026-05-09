<?php
declare(strict_types=1);
session_start();

if (!isset($_SESSION['user_id'])) {
    header('Content-Type: application/json');
    echo json_encode(['error' => 'Non autorisé']);
    exit;
}

require_once __DIR__ . '/../Gestion_pub/controller/PublicationController.php';
require_once __DIR__ . '/../Gestion_pub/controller/CommentaireController.php';

$pubController = new PublicationController();
$comController = new CommentaireController();

$action = $_GET['action'] ?? '';

if ($action === 'coms_getAll') {
    if (($_SESSION['role'] ?? '') !== 'admin') {
        header('Content-Type: application/json');
        echo json_encode(['error' => 'Non autorisé']);
        exit;
    }
    $data = $comController->afficherTousLesCommentaires();
    header('Content-Type: application/json');
    echo json_encode(['success' => true, 'data' => $data]);
    exit;
}

if ($action === 'coms_getByPub') {
    $idPub = (int) ($_GET['id_pub'] ?? 0);
    $data = $comController->afficherCommentairesParPublication($idPub);
    header('Content-Type: application/json');
    echo json_encode(['success' => true, 'data' => $data]);
    exit;
}

if ($action === 'ai_getSummary') {
    $idPub = (int) ($_GET['id_pub'] ?? 0);
    $sumRes = $pubController->getAISummary($idPub);
    $sugRes = $pubController->getAISuggestedResponse($idPub);

    header('Content-Type: application/json');
    if (($sumRes['success'] ?? false) || ($sugRes['success'] ?? false)) {
        echo json_encode([
            'success' => true,
            'summary' => $sumRes['data'] ?? 'Aucun résumé disponible.',
            'suggestion' => $sugRes['data'] ?? 'Aucune suggestion disponible.'
        ]);
    } else {
        echo json_encode([
            'success' => false,
            'error' => $sumRes['error'] ?? $sugRes['error'] ?? 'Service IA indisponible.'
        ]);
    }
    exit;
}

if ($action === 'export_pdf') {
    if (($_SESSION['role'] ?? '') !== 'admin') {
        header('Location: login.php');
        exit;
    }
    $pubController->exportPdf();
    exit;
}

header('Content-Type: application/json');
echo json_encode(['error' => 'Action invalide']);
