<?php
// Point d'entrée API pour la gestion du stock

// Chargement des contrôleurs
require_once __DIR__ . '/controller/CategorieController.php';
require_once __DIR__ . '/controller/ProduitController.php';
require_once __DIR__ . '/controller/OpenAIController.php';

// Instanciation des contrôleurs
$categorieController = new CategorieController();
$produitController = new ProduitController();
$openAIController = new OpenAIController();

// Récupérer la méthode HTTP et l'action requise
$method = $_SERVER['REQUEST_METHOD'];
$action = $_GET['action'] ?? 'index';

// Récupérer les données POST
$postData = json_decode(file_get_contents('php://input'), true) ?? $_POST;

// ====================================================
// ROUTES API - CATEGORIES
// ====================================================

if (strpos($action, 'categorie_') === 0) {
    try {
        $subaction = substr($action, 10); // Enlever le préfixe 'categorie_'
        header('Content-Type: application/json; charset=utf-8');

        switch ($subaction) {
        case 'create':
            if ($method === 'POST') {
                $nom_cat = $postData['nom_cat'] ?? '';
                $description_cat = $postData['description_cat'] ?? '';
                $lieu_stockage = $postData['lieu_stockage'] ?? '';
                $temp_conseille = $postData['temp_conseille'] ?? '';
                $delai_alerte_jours = (int)($postData['delai_alerte_jours'] ?? 30);
                echo json_encode($categorieController->create($nom_cat, $description_cat, $lieu_stockage, $temp_conseille, $delai_alerte_jours));
            }
            break;

        case 'getAll':
            $search = $_GET['search'] ?? '';
            $result = $categorieController->getAll($search);
            echo json_encode(['success' => true, 'data' => array_map(fn($c) => $c->toArray(), $result)]);
            break;

        case 'getById':
            $id = $_GET['id'] ?? 0;
            $result = $categorieController->getById($id);
            if ($result) {
                echo json_encode(['success' => true, 'data' => $result->toArray()]);
            } else {
                echo json_encode(['success' => false, 'error' => 'Catégorie non trouvée']);
            }
            break;

        case 'update':
            if ($method === 'POST') {
                $id_cat = $postData['id_cat'] ?? 0;
                $nom_cat = $postData['nom_cat'] ?? '';
                $description_cat = $postData['description_cat'] ?? '';
                $lieu_stockage = $postData['lieu_stockage'] ?? '';
                $temp_conseille = $postData['temp_conseille'] ?? '';
                $delai_alerte_jours = (int)($postData['delai_alerte_jours'] ?? 30);
                echo json_encode($categorieController->update($id_cat, $nom_cat, $description_cat, $lieu_stockage, $temp_conseille, $delai_alerte_jours));
            }
            break;

        case 'delete':
            if ($method === 'POST') {
                $id_cat = $postData['id_cat'] ?? 0;
                echo json_encode($categorieController->delete($id_cat));
            }
            break;
        }
    } catch (Exception $e) {
        error_log("ERROR: " . $e->getMessage());
        echo json_encode(['success' => false, 'error' => $e->getMessage()]);
    }
}

// ====================================================
// ROUTES API - OPENAI
// ====================================================

elseif (strpos($action, 'openai_') === 0) {
    try {
        $subaction = substr($action, 7); // Enlever le préfixe 'openai_'
        header('Content-Type: application/json; charset=utf-8');

        switch ($subaction) {
        case 'generate_description':
            if ($method === 'POST') {
                $categoryName = $postData['category_name'] ?? '';
                if (empty($categoryName)) {
                    echo json_encode(['success' => false, 'error' => 'Le nom de la catégorie est requis']);
                } else {
                    $description = $openAIController->generateCategoryDescription($categoryName);
                    echo json_encode(['success' => true, 'description' => $description]);
                }
            }
            break;
        }
    } catch (Exception $e) {
        error_log("ERROR: " . $e->getMessage());
        echo json_encode(['success' => false, 'error' => $e->getMessage()]);
    }
}

// ====================================================
// ROUTES API - PRODUITS
// ====================================================

elseif (strpos($action, 'produit_') === 0) {
    $subaction = substr($action, 8); // Enlever le préfixe 'produit_'
    header('Content-Type: application/json; charset=utf-8');

    switch ($subaction) {
        case 'create':
            if ($method === 'POST') {
                $nom_prod = $postData['nom_prod'] ?? '';
                $id_cat = (int)($postData['id_cat'] ?? 0);
                $date_expiration = $postData['date_expiration'] ?? null;
                $poids_produit = (float)($postData['poids_produit'] ?? 0);
                $quantite_dispo = (int)($postData['quantite_dispo'] ?? 0);
                echo json_encode($produitController->create($nom_prod, $id_cat, $date_expiration, $poids_produit, $quantite_dispo));
            }
            break;

        case 'getAll':
            $search = $_GET['search'] ?? '';
            $id_cat = isset($_GET['id_cat']) ? (int)$_GET['id_cat'] : null;
            $result = $produitController->getAll($search, $id_cat);
            echo json_encode(['success' => true, 'data' => array_map(fn($p) => $p->toArray(), $result)]);
            break;

        case 'getById':
            $id = $_GET['id'] ?? 0;
            $result = $produitController->getById($id);
            if ($result) {
                echo json_encode(['success' => true, 'data' => $result->toArray()]);
            } else {
                echo json_encode(['success' => false, 'error' => 'Produit non trouvé']);
            }
            break;

        case 'update':
            if ($method === 'POST') {
                $id_prod = $postData['id_prod'] ?? 0;
                $nom_prod = $postData['nom_prod'] ?? '';
                $id_cat = (int)($postData['id_cat'] ?? 0);
                $date_expiration = $postData['date_expiration'] ?? null;
                $poids_produit = (float)($postData['poids_produit'] ?? 0);
                $quantite_dispo = (int)($postData['quantite_dispo'] ?? 0);
                echo json_encode($produitController->update($id_prod, $nom_prod, $id_cat, $date_expiration, $poids_produit, $quantite_dispo));
            }
            break;

        case 'delete':
            if ($method === 'POST') {
                $id_prod = $postData['id_prod'] ?? 0;
                echo json_encode($produitController->delete($id_prod));
            }
            break;

        case 'augmenterStock':
            if ($method === 'POST') {
                $id_prod = $postData['id_prod'] ?? 0;
                $quantite = (int)($postData['quantite'] ?? 0);
                echo json_encode($produitController->augmenterStock($id_prod, $quantite));
            }
            break;

        case 'diminuerStock':
            if ($method === 'POST') {
                $id_prod = $postData['id_prod'] ?? 0;
                $quantite = (int)($postData['quantite'] ?? 0);
                echo json_encode($produitController->diminuerStock($id_prod, $quantite));
            }
            break;

        case 'getBasStock':
            $result = $produitController->getProduitsBasStock();
            echo json_encode(['success' => true, 'data' => array_map(fn($p) => $p->toArray(), $result)]);
            break;

        case 'getRupture':
            $result = $produitController->getProduitRupture();
            echo json_encode(['success' => true, 'data' => array_map(fn($p) => $p->toArray(), $result)]);
            break;
    }
}

// ====================================================
// ROUTES API - STATISTIQUES
// ====================================================

elseif ($action === 'stats') {
    header('Content-Type: application/json; charset=utf-8');
    $stats = [
        'total_categories' => $categorieController->count(),
        'total_produits' => $produitController->count(),
        'produits_bas_stock' => $produitController->countBasStock(),
        'valeur_stock' => $produitController->getValeurStockTotal()
    ];
    echo json_encode(['success' => true, 'data' => $stats]);
}

else {
    header('Content-Type: application/json; charset=utf-8');
    header('HTTP/1.0 404 Not Found');
    echo json_encode(['error' => 'Action non trouvée']);
}
?>
