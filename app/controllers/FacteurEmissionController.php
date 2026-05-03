<?php
require_once __DIR__ . '/../../config/Database.php';
require_once __DIR__ . '/../models/FacteurEmission.php';

/**
 * FacteurEmissionController - API REST
 */
class FacteurEmissionController
{
    // ===== CREATE =====
    public static function creer()
    {
        try {
            $data = json_decode(file_get_contents('php://input'), true) ?? $_POST;
            
            $pdo = Config::getConnexion();
            $sql = "INSERT INTO facteur_emission (categorie_aliment, co2_par_kg, source_donnee, date_derniere_maj) 
                    VALUES (:categorie_aliment, :co2_par_kg, :source_donnee, :date_derniere_maj)";
            
            $stmt = $pdo->prepare($sql);
            $resultat = $stmt->execute([
                ':categorie_aliment' => htmlspecialchars(trim($data['categorie_aliment'] ?? '')),
                ':co2_par_kg' => floatval($data['co2_par_kg'] ?? 0),
                ':source_donnee' => htmlspecialchars(trim($data['source_donnee'] ?? '')),
                ':date_derniere_maj' => $data['date_derniere_maj'] ?? date('Y-m-d')
            ]);

            if ($resultat) {
                http_response_code(201);
                echo json_encode([
                    'success' => true,
                    'message' => 'Facteur d\'émission créé avec succès',
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
            $tri = htmlspecialchars($_GET['tri'] ?? 'categorie_aliment');
            $ordre = strtoupper($_GET['ordre'] ?? 'ASC') === 'DESC' ? 'DESC' : 'ASC';
            
            // Whitelist for sorting to prevent injection
            $colonnes_valides = ['id_facteur', 'categorie_aliment', 'co2_par_kg', 'source_donnee', 'date_derniere_maj'];
            if (!in_array($tri, $colonnes_valides)) $tri = 'categorie_aliment';

            $sql = "SELECT * FROM facteur_emission ORDER BY $tri $ordre LIMIT :limite OFFSET :offset";
            
            $stmt = $pdo->prepare($sql);
            $stmt->bindValue(':limite', $limite, PDO::PARAM_INT);
            $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
            $stmt->execute();

            $facteurs = $stmt->fetchAll();
            $total = self::obtenirNombreTotal();

            echo json_encode([
                'success' => true,
                'facteurs' => $facteurs,
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
            $sql = "SELECT COUNT(*) as total FROM facteur_emission";
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
            $sql = "UPDATE facteur_emission SET 
                    categorie_aliment = :categorie_aliment,
                    co2_par_kg = :co2_par_kg,
                    source_donnee = :source_donnee,
                    date_derniere_maj = :date_derniere_maj
                    WHERE id_facteur = :id";
            
            $stmt = $pdo->prepare($sql);
            $resultat = $stmt->execute([
                ':categorie_aliment' => htmlspecialchars(trim($data['categorie_aliment'] ?? '')),
                ':co2_par_kg' => floatval($data['co2_par_kg'] ?? 0),
                ':source_donnee' => htmlspecialchars(trim($data['source_donnee'] ?? '')),
                ':date_derniere_maj' => $data['date_derniere_maj'] ?? date('Y-m-d'),
                ':id' => $id
            ]);

            if ($resultat) {
                echo json_encode(['success' => true, 'message' => 'Facteur d\'émission mis à jour avec succès']);
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
            $sql = "DELETE FROM facteur_emission WHERE id_facteur = :id";
            $stmt = $pdo->prepare($sql);
            $resultat = $stmt->execute([':id' => $id]);

            if ($resultat) {
                echo json_encode(['success' => true, 'message' => 'Facteur d\'émission supprimé avec succès']);
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
            $sql = "SELECT * FROM facteur_emission 
                    WHERE categorie_aliment LIKE :terme 
                    OR source_donnee LIKE :terme 
                    OR co2_par_kg LIKE :terme 
                    OR date_derniere_maj LIKE :terme
                    ORDER BY categorie_aliment ASC";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([':terme' => '%' . $terme . '%']);

            $facteurs = $stmt->fetchAll();
            echo json_encode(['success' => true, 'facteurs' => $facteurs, 'count' => count($facteurs)]);
        } catch (PDOException $e) {
            http_response_code(500);
            echo json_encode(['success' => false, 'message' => 'Erreur: ' . $e->getMessage()]);
        }
    }
}
