<?php
require_once __DIR__ . '/../../config/Database.php';
require_once __DIR__ . '/../models/Recette.php';
require_once __DIR__ . '/../lib/PdfGenerator.php';

/**
 * RecetteController - API REST
 * Tous les CRUD retournent du JSON
 */
class RecetteController
{
    // ===== CREATE =====
    public static function creer()
    {
        try {
            $data = json_decode(file_get_contents('php://input'), true) ?? $_POST;
            
            $pdo = RecRecRecConfig::getConnexion();
            $sql = "INSERT INTO rec_recette (nom, description, nombre_personnes, temps_preparation, temps_cuisson, difficulte, calories_totales, image_url, id_user) 
                    VALUES (:nom, :description, :nombre_personnes, :temps_preparation, :temps_cuisson, :difficulte, :calories_totales, :image_url, :id_user)";
            
            $stmt = $pdo->prepare($sql);
            $resultat = $stmt->execute([
                ':nom' => htmlspecialchars(trim($data['nom'] ?? '')),
                ':description' => htmlspecialchars(trim($data['description'] ?? '')),
                ':nombre_personnes' => intval($data['nombre_personnes'] ?? 0),
                ':temps_preparation' => intval($data['temps_preparation'] ?? 0),
                ':temps_cuisson' => intval($data['temps_cuisson'] ?? 0),
                ':difficulte' => htmlspecialchars(trim($data['difficulte'] ?? 'moyen')),
                ':calories_totales' => intval($data['calories_totales'] ?? 0),
                ':image_url' => isset($data['image_url']) ? htmlspecialchars(trim($data['image_url'])) : null,
                ':id_user' => intval($data['id_user'] ?? 0)
            ]);

            if ($resultat) {
                http_response_code(201);
                echo json_encode([
                    'success' => true,
                    'message' => 'Recette crÃ©Ã©e avec succÃ¨s',
                    'id' => $pdo->lastInsertId()
                ]);
            } else {
                http_response_code(400);
                echo json_encode([
                    'success' => false,
                    'message' => 'Erreur lors de la crÃ©ation'
                ]);
            }
        } catch (PDOException $e) {
            http_response_code(500);
            echo json_encode([
                'success' => false,
                'message' => 'Erreur: ' . $e->getMessage()
            ]);
        }
    }

    // ===== READ ONE =====
    public static function obtenirParId()
    {
        try {
            $id = intval($_GET['id'] ?? 0);
            
            if (!$id) {
                http_response_code(400);
                echo json_encode(['success' => false, 'message' => 'ID requis']);
                return;
            }
            
            $pdo = RecRecRecConfig::getConnexion();
            $sql = "SELECT * FROM rec_recette WHERE id_recette = :id";
            
            $stmt = $pdo->prepare($sql);
            $stmt->execute([':id' => $id]);
            $data = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($data) {
                echo json_encode([
                    'success' => true,
                    'rec_recette' => $data
                ]);
            } else {
                http_response_code(404);
                echo json_encode([
                    'success' => false,
                    'message' => 'Recette non trouvÃ©e'
                ]);
            }
        } catch (PDOException $e) {
            http_response_code(500);
            echo json_encode([
                'success' => false,
                'message' => 'Erreur: ' . $e->getMessage()
            ]);
        }
    }

    // ===== READ ALL (Pagination + Tri) =====
    public static function obtenirTous()
    {
        try {
            $page = intval($_GET['page'] ?? 1);
            $limite = intval($_GET['limite'] ?? 10);
            $sortBy = self::sanitizeSortField($_GET['sort_by'] ?? 'date_creation');
            $order = strtoupper($_GET['order'] ?? 'DESC');
            $order = in_array($order, ['ASC', 'DESC']) ? $order : 'DESC';
            $offset = ($page - 1) * $limite;
            
            $pdo = RecRecRecConfig::getConnexion();
            $sql = "SELECT * FROM rec_recette ORDER BY $sortBy $order LIMIT :limite OFFSET :offset";
            
            $stmt = $pdo->prepare($sql);
            $stmt->bindValue(':limite', $limite, PDO::PARAM_INT);
            $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
            $stmt->execute();

            $recettes = [];
            while ($data = $stmt->fetch(PDO::FETCH_ASSOC)) {
                $recettes[] = $data;
            }

            $total = self::obtenirNombreTotal();

            echo json_encode([
                'success' => true,
                'recettes' => $recettes,
                'pagination' => [
                    'page' => $page,
                    'limite' => $limite,
                    'total' => $total,
                    'total_pages' => ceil($total / $limite),
                    'sort_by' => $sortBy,
                    'order' => $order
                ]
            ]);
        } catch (PDOException $e) {
            http_response_code(500);
            echo json_encode([
                'success' => false,
                'message' => 'Erreur: ' . $e->getMessage()
            ]);
        }
    }

    // ===== COUNT TOTAL =====
    public static function obtenirNombreTotal()
    {
        try {
            $pdo = RecRecRecConfig::getConnexion();
            $sql = "SELECT COUNT(*) as total FROM rec_recette";
            $stmt = $pdo->query($sql);
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            return $result['total'] ?? 0;
        } catch (PDOException $e) {
            return 0;
        }
    }

    // ===== UPDATE =====
    public static function mettre_a_jour()
    {
        try {
            $id = intval($_GET['id'] ?? 0);
            $data = json_decode(file_get_contents('php://input'), true) ?? $_POST;
            
            if (!$id) {
                http_response_code(400);
                echo json_encode(['success' => false, 'message' => 'ID requis']);
                return;
            }

            // VÃ©rifier que la recette existe
            $recette = self::recette_existe($id);
            if (!$recette) {
                http_response_code(404);
                echo json_encode(['success' => false, 'message' => 'Recette non trouvÃ©e']);
                return;
            }

            $pdo = RecRecRecConfig::getConnexion();
            $sql = "UPDATE rec_recette SET 
                    nom = :nom,
                    description = :description,
                    nombre_personnes = :nombre_personnes,
                    temps_preparation = :temps_preparation,
                    temps_cuisson = :temps_cuisson,
                    difficulte = :difficulte,
                    calories_totales = :calories_totales,
                    image_url = :image_url
                    WHERE id_recette = :id";
            
            $stmt = $pdo->prepare($sql);
            $resultat = $stmt->execute([
                ':nom' => htmlspecialchars(trim($data['nom'] ?? $recette['nom'])),
                ':description' => htmlspecialchars(trim($data['description'] ?? $recette['description'])),
                ':nombre_personnes' => intval($data['nombre_personnes'] ?? $recette['nombre_personnes']),
                ':temps_preparation' => intval($data['temps_preparation'] ?? $recette['temps_preparation']),
                ':temps_cuisson' => intval($data['temps_cuisson'] ?? $recette['temps_cuisson']),
                ':difficulte' => htmlspecialchars(trim($data['difficulte'] ?? $recette['difficulte'])),
                ':calories_totales' => intval($data['calories_totales'] ?? $recette['calories_totales']),
                ':image_url' => isset($data['image_url']) ? htmlspecialchars(trim($data['image_url'])) : $recette['image_url'],
                ':id' => $id
            ]);

            if ($resultat) {
                echo json_encode([
                    'success' => true,
                    'message' => 'Recette mise Ã  jour avec succÃ¨s'
                ]);
            } else {
                http_response_code(400);
                echo json_encode([
                    'success' => false,
                    'message' => 'Erreur lors de la mise Ã  jour'
                ]);
            }
        } catch (PDOException $e) {
            http_response_code(500);
            echo json_encode([
                'success' => false,
                'message' => 'Erreur: ' . $e->getMessage()
            ]);
        }
    }

    // ===== DELETE =====
    public static function supprimer()
    {
        try {
            $id = intval($_GET['id'] ?? 0);
            
            if (!$id) {
                http_response_code(400);
                echo json_encode(['success' => false, 'message' => 'ID requis']);
                return;
            }

            // VÃ©rifier que la recette existe
            if (!self::recette_existe($id)) {
                http_response_code(404);
                echo json_encode(['success' => false, 'message' => 'Recette non trouvÃ©e']);
                return;
            }

            $pdo = RecRecRecConfig::getConnexion();
            $sql = "DELETE FROM rec_recette WHERE id_recette = :id";
            $stmt = $pdo->prepare($sql);
            $resultat = $stmt->execute([':id' => $id]);

            if ($resultat) {
                echo json_encode([
                    'success' => true,
                    'message' => 'Recette supprimÃ©e avec succÃ¨s'
                ]);
            } else {
                http_response_code(400);
                echo json_encode([
                    'success' => false,
                    'message' => 'Erreur lors de la suppression'
                ]);
            }
        } catch (PDOException $e) {
            http_response_code(500);
            echo json_encode([
                'success' => false,
                'message' => 'Erreur: ' . $e->getMessage()
            ]);
        }
    }

    // ===== SEARCH =====
    public static function rechercher()
    {
        try {
            $terme = htmlspecialchars(trim($_GET['terme'] ?? ''));
            $sortBy = self::sanitizeSortField($_GET['sort_by'] ?? 'date_creation');
            $order = strtoupper($_GET['order'] ?? 'DESC');
            $order = in_array($order, ['ASC', 'DESC']) ? $order : 'DESC';
            $limite = intval($_GET['limite'] ?? 50);
            $offset = intval($_GET['offset'] ?? 0);

            if (!$terme) {
                http_response_code(400);
                echo json_encode(['success' => false, 'message' => 'Terme de recherche requis']);
                return;
            }

            $pdo = RecRecRecConfig::getConnexion();
            $sql = "SELECT DISTINCT recette.* FROM rec_recette 
                    LEFT JOIN detail_recette ON recette.id_recette = detail_recette.id_recette 
                    WHERE recette.nom LIKE :terme 
                       OR recette.description LIKE :terme 
                       OR detail_recette.ingredient LIKE :terme 
                    ORDER BY $sortBy $order 
                    LIMIT :limite OFFSET :offset";

            $stmt = $pdo->prepare($sql);
            $stmt->bindValue(':terme', '%' . $terme . '%', PDO::PARAM_STR);
            $stmt->bindValue(':limite', $limite, PDO::PARAM_INT);
            $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
            $stmt->execute();

            $recettes = $stmt->fetchAll(PDO::FETCH_ASSOC);

            echo json_encode([
                'success' => true,
                'recettes' => $recettes,
                'count' => count($recettes),
                'pagination' => [
                    'page' => floor($offset / $limite) + 1,
                    'limite' => $limite,
                    'sort_by' => $sortBy,
                    'order' => $order
                ]
            ]);
        } catch (PDOException $e) {
            http_response_code(500);
            echo json_encode([
                'success' => false,
                'message' => 'Erreur: ' . $e->getMessage()
            ]);
        }
    }

    // ===== STATISTIQUES =====
    public static function obtenirStatistiques()
    {
        try {
            $pdo = RecRecRecConfig::getConnexion();
            $sql = "SELECT 
                        COUNT(*) AS total,
                        SUM(CASE WHEN difficulte = 'facile' THEN 1 ELSE 0 END) AS facile,
                        SUM(CASE WHEN difficulte = 'moyen' THEN 1 ELSE 0 END) AS moyen,
                        SUM(CASE WHEN difficulte = 'difficile' THEN 1 ELSE 0 END) AS difficile,
                        ROUND(AVG(calories_totales), 0) AS moyenne_calories,
                        ROUND(AVG(temps_preparation + temps_cuisson), 0) AS moyenne_temps
                    FROM rec_recette";
            $stmt = $pdo->query($sql);
            $stats = $stmt->fetch(PDO::FETCH_ASSOC);

            $top = [];
            $stmt2 = $pdo->query("SELECT nom, calories_totales FROM rec_recette ORDER BY calories_totales DESC LIMIT 3");
            while ($row = $stmt2->fetch(PDO::FETCH_ASSOC)) {
                $top[] = $row;
            }

            echo json_encode([
                'success' => true,
                'statistics' => [
                    'total' => intval($stats['total']),
                    'facile' => intval($stats['facile']),
                    'moyen' => intval($stats['moyen']),
                    'difficile' => intval($stats['difficile']),
                    'moyenne_calories' => intval($stats['moyenne_calories']),
                    'moyenne_temps' => intval($stats['moyenne_temps']),
                    'top_calories' => $top
                ]
            ]);
        } catch (PDOException $e) {
            http_response_code(500);
            echo json_encode([
                'success' => false,
                'message' => 'Erreur: ' . $e->getMessage()
            ]);
        }
    }

    // ===== EXPORT PDF =====
    public static function exportPdf()
    {
        try {
            $terme = trim($_GET['terme'] ?? '');
            $sortBy = self::sanitizeSortField($_GET['sort_by'] ?? 'date_creation');
            $order = strtoupper($_GET['order'] ?? 'DESC');
            $order = in_array($order, ['ASC', 'DESC']) ? $order : 'DESC';

            $pdo = RecRecRecConfig::getConnexion();
            $sql = "SELECT * FROM rec_recette";
            $params = [];

            if ($terme !== '') {
                $sql .= " WHERE nom LIKE :terme OR description LIKE :terme";
                $params[':terme'] = '%' . $terme . '%';
            }

            $sql .= " ORDER BY $sortBy $order";
            $stmt = $pdo->prepare($sql);
            $stmt->execute($params);
            $recettes = $stmt->fetchAll(PDO::FETCH_ASSOC);

            $title = $terme !== '' ? 'Recettes - Recherche : ' . $terme : 'Liste complÃ¨te des recettes';
            $lines = [
                'Recettes exportÃ©es',
                'Date : ' . date('d/m/Y H:i'),
                str_repeat('-', 90)
            ];

            foreach ($recettes as $r) {
                $lines[] = 'Nom : ' . $r['nom'];
                $lines[] = 'Description : ' . $r['description'];
                $lines[] = 'Personnes : ' . $r['nombre_personnes'] . ' | DifficultÃ© : ' . $r['difficulte'];
                $lines[] = 'PrÃ©pa : ' . $r['temps_preparation'] . ' min | Cuisson : ' . $r['temps_cuisson'] . ' min | Calories : ' . $r['calories_totales'];
                $lines[] = 'AjoutÃ©e le : ' . date('d/m/Y', strtotime($r['date_creation']));
                $lines[] = str_repeat('-', 90);
            }

            PdfGenerator::outputPdf($title, $lines);
        } catch (PDOException $e) {
            http_response_code(500);
            echo json_encode([
                'success' => false,
                'message' => 'Erreur: ' . $e->getMessage()
            ]);
        }
    }

    private static function sanitizeSortField(string $field): string
    {
        $allowed = ['nom', 'nombre_personnes', 'temps_preparation', 'temps_cuisson', 'difficulte', 'calories_totales', 'date_creation'];
        return in_array($field, $allowed, true) ? $field : 'date_creation';
    }

    // ===== UTILITY: VÃ©rifier si recette existe =====
    private static function recette_existe($id)
    {
        try {
            $pdo = RecRecRecConfig::getConnexion();
            $sql = "SELECT * FROM rec_recette WHERE id_recette = :id";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([':id' => $id]);
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            return null;
        }
    }

    // ===== UTILITY: Constantes =====
    public static function obtenirConstantes()
    {
        echo json_encode([
            'success' => true,
            'constantes' => [
                'difficultes' => Recette::DIFFICULTES
            ]
        ]);
    }
}
?>

