<?php
require_once __DIR__ . '/../../config/Database.php';
require_once __DIR__ . '/../models/Recette.php';

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
            
            $pdo = Config::getConnexion();
            $sql = "INSERT INTO recette (nom, description, nombre_personnes, temps_preparation, temps_cuisson, difficulte, calories_totales, id_user) 
                    VALUES (:nom, :description, :nombre_personnes, :temps_preparation, :temps_cuisson, :difficulte, :calories_totales, :id_user)";
            
            $stmt = $pdo->prepare($sql);
            $resultat = $stmt->execute([
                ':nom' => htmlspecialchars(trim($data['nom'] ?? '')),
                ':description' => htmlspecialchars(trim($data['description'] ?? '')),
                ':nombre_personnes' => intval($data['nombre_personnes'] ?? 0),
                ':temps_preparation' => intval($data['temps_preparation'] ?? 0),
                ':temps_cuisson' => intval($data['temps_cuisson'] ?? 0),
                ':difficulte' => htmlspecialchars(trim($data['difficulte'] ?? 'moyen')),
                ':calories_totales' => intval($data['calories_totales'] ?? 0),
                ':id_user' => intval($data['id_user'] ?? 0)
            ]);

            if ($resultat) {
                http_response_code(201);
                echo json_encode([
                    'success' => true,
                    'message' => 'Recette créée avec succès',
                    'id' => $pdo->lastInsertId()
                ]);
            } else {
                http_response_code(400);
                echo json_encode([
                    'success' => false,
                    'message' => 'Erreur lors de la création'
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
            
            $pdo = Config::getConnexion();
            $sql = "SELECT * FROM recette WHERE id_recette = :id";
            
            $stmt = $pdo->prepare($sql);
            $stmt->execute([':id' => $id]);
            $data = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($data) {
                echo json_encode([
                    'success' => true,
                    'recette' => $data
                ]);
            } else {
                http_response_code(404);
                echo json_encode([
                    'success' => false,
                    'message' => 'Recette non trouvée'
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

    // ===== READ ALL (Pagination) =====
    public static function obtenirTous()
    {
        try {
            $page = intval($_GET['page'] ?? 1);
            $limite = intval($_GET['limite'] ?? 10);
            $offset = ($page - 1) * $limite;
            
            $pdo = Config::getConnexion();
            $sql = "SELECT * FROM recette ORDER BY date_creation DESC LIMIT :limite OFFSET :offset";
            
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
                    'total_pages' => ceil($total / $limite)
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
            $pdo = Config::getConnexion();
            $sql = "SELECT COUNT(*) as total FROM recette";
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

            // Vérifier que la recette existe
            $recette = self::recette_existe($id);
            if (!$recette) {
                http_response_code(404);
                echo json_encode(['success' => false, 'message' => 'Recette non trouvée']);
                return;
            }

            $pdo = Config::getConnexion();
            $sql = "UPDATE recette SET 
                    nom = :nom,
                    description = :description,
                    nombre_personnes = :nombre_personnes,
                    temps_preparation = :temps_preparation,
                    temps_cuisson = :temps_cuisson,
                    difficulte = :difficulte,
                    calories_totales = :calories_totales
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
                ':id' => $id
            ]);

            if ($resultat) {
                echo json_encode([
                    'success' => true,
                    'message' => 'Recette mise à jour avec succès'
                ]);
            } else {
                http_response_code(400);
                echo json_encode([
                    'success' => false,
                    'message' => 'Erreur lors de la mise à jour'
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

            // Vérifier que la recette existe
            if (!self::recette_existe($id)) {
                http_response_code(404);
                echo json_encode(['success' => false, 'message' => 'Recette non trouvée']);
                return;
            }

            $pdo = Config::getConnexion();
            $sql = "DELETE FROM recette WHERE id_recette = :id";
            $stmt = $pdo->prepare($sql);
            $resultat = $stmt->execute([':id' => $id]);

            if ($resultat) {
                echo json_encode([
                    'success' => true,
                    'message' => 'Recette supprimée avec succès'
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
            
            if (!$terme) {
                http_response_code(400);
                echo json_encode(['success' => false, 'message' => 'Terme de recherche requis']);
                return;
            }

            $pdo = Config::getConnexion();
            $sql = "SELECT * FROM recette 
                    WHERE nom LIKE :terme 
                    OR description LIKE :terme 
                    ORDER BY date_creation DESC";
            
            $stmt = $pdo->prepare($sql);
            $stmt->execute([':terme' => '%' . $terme . '%']);

            $recettes = [];
            while ($data = $stmt->fetch(PDO::FETCH_ASSOC)) {
                $recettes[] = $data;
            }

            echo json_encode([
                'success' => true,
                'recettes' => $recettes,
                'count' => count($recettes)
            ]);
        } catch (PDOException $e) {
            http_response_code(500);
            echo json_encode([
                'success' => false,
                'message' => 'Erreur: ' . $e->getMessage()
            ]);
        }
    }

    // ===== UTILITY: Vérifier si recette existe =====
    private static function recette_existe($id)
    {
        try {
            $pdo = Config::getConnexion();
            $sql = "SELECT * FROM recette WHERE id_recette = :id";
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
