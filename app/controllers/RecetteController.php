<?php
require_once __DIR__ . '/../../config/Database.php';
require_once __DIR__ . '/../models/Recette.php';

/**
 * RecetteController - API REST
 */
class RecetteController
{
    // ===== CREATE =====
    public static function creer()
    {
        try {
            $data = json_decode(file_get_contents('php://input'), true) ?? $_POST;
            
            $pdo = Config::getConnexion();
            $sql = "INSERT INTO recette (nom, description) VALUES (:nom, :description)";
            
            $stmt = $pdo->prepare($sql);
            $resultat = $stmt->execute([
                ':nom' => htmlspecialchars(trim($data['nom'] ?? '')),
                ':description' => htmlspecialchars(trim($data['description'] ?? ''))
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
                echo json_encode(['success' => false, 'message' => 'Erreur lors de la création']);
            }
        } catch (PDOException $e) {
            http_response_code(500);
            echo json_encode(['success' => false, 'message' => 'Erreur: ' . $e->getMessage()]);
        }
    }

    // ===== READ ALL =====
    public static function obtenirTous()
    {
        try {
            $page = intval($_GET['page'] ?? 1);
            $limite = intval($_GET['limite'] ?? 10);
            $offset = ($page - 1) * $limite;
            
            $pdo = Config::getConnexion();
            $tri = htmlspecialchars($_GET['tri'] ?? 'nom');
            $ordre = strtoupper($_GET['ordre'] ?? 'ASC') === 'DESC' ? 'DESC' : 'ASC';

            // Whitelist
            $colonnes_valides = ['id_recette', 'nom', 'description', 'date_creation'];
            if (!in_array($tri, $colonnes_valides)) $tri = 'nom';

            $sql = "SELECT * FROM recette ORDER BY $tri $ordre LIMIT :limite OFFSET :offset";
            
            $stmt = $pdo->prepare($sql);
            $stmt->bindValue(':limite', $limite, PDO::PARAM_INT);
            $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
            $stmt->execute();

            $recettes = $stmt->fetchAll();
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
            echo json_encode(['success' => false, 'message' => 'Erreur: ' . $e->getMessage()]);
        }
    }

    // ===== COUNT TOTAL =====
    public static function obtenirNombreTotal()
    {
        try {
            $pdo = Config::getConnexion();
            $sql = "SELECT COUNT(*) as total FROM recette";
            $stmt = $pdo->query($sql);
            $result = $stmt->fetch();
            return $result['total'] ?? 0;
        } catch (PDOException $e) { return 0; }
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

            $pdo = Config::getConnexion();
            $sql = "UPDATE recette SET nom = :nom, description = :description WHERE id_recette = :id";
            
            $stmt = $pdo->prepare($sql);
            $resultat = $stmt->execute([
                ':nom' => htmlspecialchars(trim($data['nom'] ?? '')),
                ':description' => htmlspecialchars(trim($data['description'] ?? '')),
                ':id' => $id
            ]);

            if ($resultat) {
                echo json_encode(['success' => true, 'message' => 'Recette mise à jour avec succès']);
            } else {
                http_response_code(400);
                echo json_encode(['success' => false, 'message' => 'Erreur lors de la mise à jour']);
            }
        } catch (PDOException $e) {
            http_response_code(500);
            echo json_encode(['success' => false, 'message' => 'Erreur: ' . $e->getMessage()]);
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

            $pdo = Config::getConnexion();
            $sql = "DELETE FROM recette WHERE id_recette = :id";
            $stmt = $pdo->prepare($sql);
            $resultat = $stmt->execute([':id' => $id]);

            if ($resultat) {
                echo json_encode(['success' => true, 'message' => 'Recette supprimée avec succès']);
            } else {
                http_response_code(400);
                echo json_encode(['success' => false, 'message' => 'Erreur lors de la suppression']);
            }
        } catch (PDOException $e) {
            http_response_code(500);
            echo json_encode(['success' => false, 'message' => 'Erreur: ' . $e->getMessage()]);
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
            $sql = "SELECT * FROM recette WHERE nom LIKE :terme OR description LIKE :terme ORDER BY nom ASC";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([':terme' => '%' . $terme . '%']);

            $recettes = $stmt->fetchAll();
            echo json_encode(['success' => true, 'recettes' => $recettes, 'count' => count($recettes)]);
        } catch (PDOException $e) {
            http_response_code(500);
            echo json_encode(['success' => false, 'message' => 'Erreur: ' . $e->getMessage()]);
        }
    }
}
