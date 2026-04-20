<?php
require_once __DIR__ . '/../../config/Database.php';
require_once __DIR__ . '/../models/DetailRecette.php';

/**
 * DetailRecetteController - API REST pour les Ingrédients / Etapes
 */
class DetailRecetteController
{
    // ===== CREATE =====
    public static function creer()
    {
        try {
            $data = json_decode(file_get_contents('php://input'), true) ?? $_POST;
            
            $pdo = Config::getConnexion();
            $sql = "INSERT INTO detail_recette (id_recette, ingredient, quantite, etape) 
                    VALUES (:id_recette, :ingredient, :quantite, :etape)";
            
            $stmt = $pdo->prepare($sql);
            $resultat = $stmt->execute([
                ':id_recette' => intval($data['id_recette'] ?? 0),
                ':ingredient' => htmlspecialchars(trim($data['ingredient'] ?? '')),
                ':quantite' => htmlspecialchars(trim($data['quantite'] ?? '')),
                ':etape' => htmlspecialchars(trim($data['etape'] ?? ''))
            ]);

            if ($resultat) {
                http_response_code(201);
                echo json_encode([
                    'success' => true,
                    'message' => 'Détail ajouté avec succès',
                    'id' => $pdo->lastInsertId()
                ]);
            } else {
                http_response_code(400);
                echo json_encode([
                    'success' => false,
                    'message' => 'Erreur lors de l\'ajout du détail'
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

    // ===== READ ALL BY RECETTE =====
    public static function obtenirParRecette()
    {
        try {
            $id_recette = intval($_GET['id_recette'] ?? 0);
            
            if (!$id_recette) {
                http_response_code(400);
                echo json_encode(['success' => false, 'message' => 'ID recette requis']);
                return;
            }
            
            $pdo = Config::getConnexion();
            $sql = "SELECT * FROM detail_recette WHERE id_recette = :id_recette";
            
            $stmt = $pdo->prepare($sql);
            $stmt->execute([':id_recette' => $id_recette]);

            $details = [];
            while ($data = $stmt->fetch(PDO::FETCH_ASSOC)) {
                $details[] = $data;
            }

            echo json_encode([
                'success' => true,
                'details' => $details
            ]);
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
                echo json_encode(['success' => false, 'message' => 'ID de détail requis']);
                return;
            }

            $pdo = Config::getConnexion();
            $sql = "DELETE FROM detail_recette WHERE id_detail = :id";
            $stmt = $pdo->prepare($sql);
            $resultat = $stmt->execute([':id' => $id]);

            if ($resultat) {
                echo json_encode([
                    'success' => true,
                    'message' => 'Détail supprimé avec succès'
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
}
?>
