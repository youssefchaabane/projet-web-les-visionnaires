<?php
/**
 * Point d'entrée API REST - Minimal
 * Routes vers les Controllers
 */

require_once 'config/Database.php';
require_once 'app/models/Recette.php';
require_once 'app/controllers/RecetteController.php';
require_once 'app/models/DetailRecette.php';
require_once 'app/controllers/DetailRecetteController.php';

// Déterminer le contrôleur et l'action
$controller = $_GET['controller'] ?? 'Recette';
$action = $_GET['action'] ?? 'obtenirTous';

// Construire le nom de la classe
$controllerClass = $controller . 'Controller';

// Vérifier que la classe existe
if (!class_exists($controllerClass)) {
    http_response_code(404);
    echo json_encode(['success' => false, 'message' => "Contrôleur $controllerClass non trouvé"]);
    exit;
}

// Vérifier que la méthode existe
if (!method_exists($controllerClass, $action)) {
    http_response_code(404);
    echo json_encode(['success' => false, 'message' => "Action $action non trouvée"]);
    exit;
}

// Header JSON
header('Content-Type: application/json; charset=utf-8');

try {
    // Appeler la méthode du contrôleur
    $controllerClass::$action();
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Erreur: ' . $e->getMessage()]);
}
