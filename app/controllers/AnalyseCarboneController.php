<?php
require_once __DIR__ . '/../../config/Database.php';
require_once __DIR__ . '/../models/AnalyseCarbone.php';

/**
 * AnalyseCarboneController - API REST
 */
class AnalyseCarboneController
{
    // ===== CREATE =====
    public static function creer()
    {
        try {
            $data = json_decode(file_get_contents('php://input'), true) ?? $_POST;
            
            $pdo = Config::getConnexion();
            $sql = "INSERT INTO analyse_carbone (score_co2_total, niveau_impact, date_calcul, methode_calcul, id_recette) 
                    VALUES (:score_co2_total, :niveau_impact, :date_calcul, :methode_calcul, :id_recette)";
            
            $stmt = $pdo->prepare($sql);
            $resultat = $stmt->execute([
                ':score_co2_total' => floatval($data['score_co2_total'] ?? 0),
                ':niveau_impact' => htmlspecialchars(trim($data['niveau_impact'] ?? '')),
                ':date_calcul' => $data['date_calcul'] ?? date('Y-m-d'),
                ':methode_calcul' => htmlspecialchars(trim($data['methode_calcul'] ?? '')),
                ':id_recette' => intval($data['id_recette'] ?? 0)
            ]);

            if ($resultat) {
                http_response_code(201);
                echo json_encode([
                    'success' => true,
                    'message' => 'Analyse carbone créée avec succès',
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
            $tri = htmlspecialchars($_GET['tri'] ?? 'date_calcul');
            $ordre = strtoupper($_GET['ordre'] ?? 'DESC') === 'DESC' ? 'DESC' : 'ASC';

            // Whitelist
            $colonnes_valides = ['id_analyse', 'score_co2_total', 'niveau_impact', 'date_calcul', 'methode_calcul', 'nom_recette'];
            if (!in_array($tri, $colonnes_valides)) $tri = 'date_calcul';
            
            $sql = "SELECT ac.*, r.nom as nom_recette FROM analyse_carbone ac
                    LEFT JOIN recette r ON ac.id_recette = r.id_recette
                    ORDER BY $tri $ordre LIMIT :limite OFFSET :offset";
            
            $stmt = $pdo->prepare($sql);
            $stmt->bindValue(':limite', $limite, PDO::PARAM_INT);
            $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
            $stmt->execute();

            $analyses = $stmt->fetchAll();
            $total = self::obtenirNombreTotal();

            echo json_encode([
                'success' => true,
                'analyses' => $analyses,
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
            $sql = "SELECT COUNT(*) as total FROM analyse_carbone";
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
            $sql = "UPDATE analyse_carbone SET 
                    score_co2_total = :score_co2_total,
                    niveau_impact = :niveau_impact,
                    date_calcul = :date_calcul,
                    methode_calcul = :methode_calcul,
                    id_recette = :id_recette
                    WHERE id_analyse = :id";
            
            $stmt = $pdo->prepare($sql);
            $resultat = $stmt->execute([
                ':score_co2_total' => floatval($data['score_co2_total'] ?? 0),
                ':niveau_impact' => htmlspecialchars(trim($data['niveau_impact'] ?? '')),
                ':date_calcul' => $data['date_calcul'] ?? date('Y-m-d'),
                ':methode_calcul' => htmlspecialchars(trim($data['methode_calcul'] ?? '')),
                ':id_recette' => intval($data['id_recette'] ?? 0),
                ':id' => $id
            ]);

            if ($resultat) {
                echo json_encode(['success' => true, 'message' => 'Analyse carbone mise à jour avec succès']);
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
            $sql = "DELETE FROM analyse_carbone WHERE id_analyse = :id";
            $stmt = $pdo->prepare($sql);
            $resultat = $stmt->execute([':id' => $id]);

            if ($resultat) {
                echo json_encode(['success' => true, 'message' => 'Analyse carbone supprimée avec succès']);
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
            $sql = 'SELECT ac.*, r.nom AS nom_recette FROM analyse_carbone AS ac LEFT JOIN recette AS r ON ac.id_recette = r.id_recette WHERE r.nom LIKE :terme OR ac.methode_calcul LIKE :terme OR ac.niveau_impact LIKE :terme OR ac.score_co2_total LIKE :terme OR ac.date_calcul LIKE :terme ORDER BY ac.date_calcul DESC';
                      $stmt = $pdo->prepare($sql);
            $stmt->execute([':terme' => '%' . $terme . '%']);

            $analyses = $stmt->fetchAll();
            echo json_encode(['success' => true, 'analyses' => $analyses, 'count' => count($analyses)]);
        } catch (PDOException $e) {
            http_response_code(500);
            echo json_encode(['success' => false, 'message' => 'Erreur: ' . $e->getMessage()]);
        }
    }
}
