<?php
declare(strict_types=1);

/**
 * stock_api.php — Endpoint AJAX pour stock_client.php
 * Remplace la référence manquante à ../gestion-stock/index.php
 */

header('Content-Type: application/json; charset=utf-8');

// Empêcher l'accès sans session
session_start();
if (!isset($_SESSION['user_id'])) {
    http_response_code(401);
    echo json_encode(['success' => false, 'error' => 'Non autorisé']);
    exit;
}

// Connexion via la config du projet
require_once __DIR__ . '/../config/config.php';

$pdo = config::getConnexion();

$action = $_GET['action'] ?? '';

try {
    switch ($action) {

        // ── Catégories ──────────────────────────────────────────────────────
        case 'categorie_getAll':
            $stmt = $pdo->query(
                'SELECT id_cat, nom_cat, description_cat, lieu_stockage,
                        temp_conseille, delai_alerte_jours
                 FROM categorie
                 ORDER BY nom_cat ASC'
            );
            $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
            echo json_encode(['success' => true, 'data' => $rows]);
            break;

        case 'categorie_getById':
            $id = (int)($_GET['id'] ?? 0);
            $stmt = $pdo->prepare('SELECT * FROM categorie WHERE id_cat = :id');
            $stmt->execute([':id' => $id]);
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            echo json_encode(['success' => true, 'data' => $row ?: null]);
            break;

        // ── Produits ────────────────────────────────────────────────────────
        case 'produit_getAll':
            $search = trim($_GET['search'] ?? '');
            $id_cat = isset($_GET['id_cat']) && $_GET['id_cat'] !== ''
                      ? (int)$_GET['id_cat']
                      : null;

            $sql    = 'SELECT id_prod, nom_prod, date_expiration,
                              poids_produit, quantite_dispo, id_cat
                       FROM produit';
            $params = [];
            $where  = [];

            if ($search !== '') {
                $where[]          = 'nom_prod LIKE :search';
                $params[':search'] = '%' . $search . '%';
            }
            if ($id_cat !== null) {
                $where[]          = 'id_cat = :id_cat';
                $params[':id_cat'] = $id_cat;
            }
            if ($where) {
                $sql .= ' WHERE ' . implode(' AND ', $where);
            }
            $sql .= ' ORDER BY id_prod DESC';

            $stmt = $pdo->prepare($sql);
            $stmt->execute($params);
            $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
            echo json_encode(['success' => true, 'data' => $rows]);
            break;

        case 'produit_getById':
            $id = (int)($_GET['id'] ?? 0);
            $stmt = $pdo->prepare('SELECT * FROM produit WHERE id_prod = :id');
            $stmt->execute([':id' => $id]);
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            echo json_encode(['success' => true, 'data' => $row ?: null]);
            break;

        case 'produit_getBasStock':
            $stmt = $pdo->query(
                'SELECT * FROM produit WHERE quantite_dispo <= 5
                 ORDER BY quantite_dispo ASC'
            );
            $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
            echo json_encode(['success' => true, 'data' => $rows]);
            break;

        case 'produit_getRupture':
            $stmt = $pdo->query(
                'SELECT * FROM produit WHERE quantite_dispo = 0'
            );
            $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
            echo json_encode(['success' => true, 'data' => $rows]);
            break;

        // ── Stats rapides ───────────────────────────────────────────────────
        case 'stats':
            $total    = $pdo->query('SELECT COUNT(*) FROM produit')->fetchColumn();
            $basStock = $pdo->query('SELECT COUNT(*) FROM produit WHERE quantite_dispo <= 5')->fetchColumn();
            $rupture  = $pdo->query('SELECT COUNT(*) FROM produit WHERE quantite_dispo = 0')->fetchColumn();
            $cats     = $pdo->query('SELECT COUNT(*) FROM categorie')->fetchColumn();
            
            // Nouvelles statistiques demandées
            $facteurs = $pdo->query('SELECT COUNT(*) FROM eco_facteur_emission')->fetchColumn();
            $recettes = $pdo->query('SELECT COUNT(*) FROM rec_recette')->fetchColumn();
            
            // Distribution par catégorie pour le graphique
            $dist = $pdo->query('
                SELECT c.nom_cat as label, COUNT(p.id_prod) as value 
                FROM categorie c 
                LEFT JOIN produit p ON c.id_cat = p.id_cat 
                GROUP BY c.id_cat
            ')->fetchAll(PDO::FETCH_ASSOC);
            
            echo json_encode([
                'success' => true,
                'data' => [
                    'total_produits'   => (int)$total,
                    'bas_stock'        => (int)$basStock,
                    'rupture'          => (int)$rupture,
                    'total_categories' => (int)$cats,
                    'total_facteurs'   => (int)$facteurs,
                    'total_recettes'   => (int)$recettes,
                    'distribution'     => $dist
                ]
            ]);
            break;

        // ── IA Groq : Génération description catégorie ─────────────────────
        case 'openai_generate_description':
            $data = json_decode(file_get_contents('php://input'), true);
            $categoryName = trim($data['category_name'] ?? '');

            if ($categoryName === '') {
                echo json_encode(['success' => false, 'error' => 'Nom de catégorie manquant']);
                break;
            }

            $payload = [
                'model'    => config::GROQ_MODEL,
                'messages' => [
                    [
                        'role'    => 'system',
                        'content' => "Tu es un assistant expert en gestion de stock alimentaire et écologique. "
                                   . "Tu génères des descriptions courtes, claires et professionnelles pour des catégories de produits en stock. "
                                   . "Réponds UNIQUEMENT avec la description, sans titre ni formatage markdown, en français, en 2-3 phrases maximum."
                    ],
                    [
                        'role'    => 'user',
                        'content' => "Génère une description professionnelle pour la catégorie de stock nommée : \"$categoryName\"."
                    ]
                ],
                'temperature' => 0.7,
                'max_tokens'  => 200
            ];

            $ch = curl_init(config::GROQ_ENDPOINT);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($payload));
            curl_setopt($ch, CURLOPT_HTTPHEADER, [
                'Content-Type: application/json',
                'Authorization: Bearer ' . config::GROQ_API_KEY
            ]);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

            $response = curl_exec($ch);
            $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            curl_close($ch);

            if ($response && $httpCode === 200) {
                $json = json_decode($response, true);
                $description = $json['choices'][0]['message']['content'] ?? null;
                if ($description) {
                    // Nettoyer le markdown si présent
                    $description = trim(preg_replace('/^```(?:json)?\s*([\s\S]*?)\s*```$/i', '$1', $description));
                    echo json_encode(['success' => true, 'description' => $description]);
                } else {
                    echo json_encode(['success' => false, 'error' => 'Réponse IA invalide']);
                }
            } else {
                echo json_encode(['success' => false, 'error' => "Erreur API Groq (HTTP $httpCode)"]);
            }
            break;

        default:
            http_response_code(400);
            echo json_encode(['success' => false, 'error' => 'Action inconnue : ' . htmlspecialchars($action)]);
    }

} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'error' => 'Erreur base de données : ' . $e->getMessage()]);
}
