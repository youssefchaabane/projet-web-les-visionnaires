<?php
declare(strict_types=1);

require_once __DIR__ . '/partials/auth.php';
require_login(); // Vérifier que l'utilisateur est bien connecté

require_once __DIR__ . '/../controller/chatbotController.php';
require_once __DIR__ . '/../controller/utilisateurC.php';

header('Content-Type: application/json');

$userId = (int) ($_SESSION['user_id'] ?? 0);
$userController = new UtilisateurC();
$userProfile = $userController->recuperer($userId);

if (!$userProfile) {
    echo json_encode(['error' => 'Utilisateur non trouvé']);
    exit;
}

$chatbotController = new ChatbotController();

$action = $_GET['action'] ?? 'chat';

if ($action === 'clear') {
    $chatbotController->viderHistorique($userId);
    echo json_encode(['success' => true]);
    exit;
}

if ($action === 'history') {
    $history = $chatbotController->recupererHistorique($userId, 30);
    echo json_encode(['success' => true, 'history' => $history]);
    exit;
}

// Action de chat (POST)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $inputData = json_decode(file_get_contents('php://input'), true);
    $message = trim((string) ($inputData['message'] ?? ''));

    if ($message === '') {
        echo json_encode(['error' => 'Le message ne peut pas être vide']);
        exit;
    }

    try {
        $response = $chatbotController->genererReponse($userId, $message, $userProfile);
        echo json_encode(['success' => true, 'response' => $response]);
    } catch (Throwable $e) {
        echo json_encode(['error' => 'Une exception s’est produite : ' . $e->getMessage()]);
    }
    exit;
}

echo json_encode(['error' => 'Requête de chat invalide']);
