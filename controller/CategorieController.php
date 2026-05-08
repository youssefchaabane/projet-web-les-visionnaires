<?php
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../model/Categorie.php';
require_once __DIR__ . '/OpenAIController.php';

/**
 * CategorieController - Gestion des catégories
 * Version PDO
 */
class CategorieController {
    private $db;
    private $openAIController;

    public function __construct() {
        $this->db = config::getConnexion();
        $this->openAIController = new OpenAIController();
    }

    /**
     * CRUD: CREATE - Ajouter une catégorie
     */
    public function create($nom_cat, $description_cat = '', $lieu_stockage = '', $temp_conseille = '', $delai_alerte_jours = 30) {
        try {
            // Générer une description si elle est vide
            if (empty(trim($description_cat))) {
                $description_cat = $this->openAIController->generateCategoryDescription($nom_cat);
            }

            $sql = 'INSERT INTO categorie (nom_cat, description_cat, lieu_stockage, temp_conseille, delai_alerte_jours) 
                    VALUES (:nom_cat, :description_cat, :lieu_stockage, :temp_conseille, :delai_alerte_jours)';
            $stmt = $this->db->prepare($sql);
            $stmt->execute([
                ':nom_cat' => $nom_cat,
                ':description_cat' => $description_cat,
                ':lieu_stockage' => $lieu_stockage,
                ':temp_conseille' => $temp_conseille,
                ':delai_alerte_jours' => (int)$delai_alerte_jours
            ]);
            
            return ['success' => true, 'id' => $this->db->lastInsertId()];
        } catch (PDOException $e) {
            error_log('Error creating categorie: ' . $e->getMessage());
            return ['success' => false, 'error' => $e->getMessage()];
        }
    }

    /**
     * CRUD: READ - Récupérer toutes les catégories
     */
    public function getAll($search = '') {
        try {
            $query = 'SELECT c.id_cat, c.nom_cat, c.description_cat, c.lieu_stockage, c.temp_conseille, c.delai_alerte_jours, c.date_creation, c.date_modification, COUNT(p.id_prod) as produits_count 
                      FROM categorie c 
                      LEFT JOIN produit p ON c.id_cat = p.id_cat';
            $params = [];
            
            if (!empty($search)) {
                $query .= ' WHERE c.nom_cat LIKE :search OR c.description_cat LIKE :search';
                $params[':search'] = '%' . $search . '%';
            }
            
            $query .= ' GROUP BY c.id_cat ORDER BY c.nom_cat';
            $stmt = $this->db->prepare($query);
            $stmt->execute($params);
            
            $categories = [];
            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                $categories[] = Categorie::fromArray($row);
            }
            
            return $categories;
        } catch (PDOException $e) {
            error_log('Error fetching categories: ' . $e->getMessage());
            return [];
        }
    }

    /**
     * CRUD: READ - Récupérer une catégorie par ID
     */
    public function getById($id) {
        try {
            $sql = 'SELECT * FROM categorie WHERE id_cat = :id_cat';
            $stmt = $this->db->prepare($sql);
            $stmt->execute([':id_cat' => (int)$id]);
            
            if ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                return Categorie::fromArray($row);
            }
            
            return null;
        } catch (PDOException $e) {
            error_log('Error fetching categorie: ' . $e->getMessage());
            return null;
        }
    }

    /**
     * CRUD: UPDATE - Modifier une catégorie
     */
    public function update($id, $nom_cat, $description_cat = '', $lieu_stockage = '', $temp_conseille = '', $delai_alerte_jours = 30) {
        try {
            $sql = 'UPDATE categorie SET nom_cat = :nom_cat, description_cat = :description_cat, 
                    lieu_stockage = :lieu_stockage, temp_conseille = :temp_conseille, 
                    delai_alerte_jours = :delai_alerte_jours WHERE id_cat = :id_cat';
            $stmt = $this->db->prepare($sql);
            $stmt->execute([
                ':nom_cat' => $nom_cat,
                ':description_cat' => $description_cat,
                ':lieu_stockage' => $lieu_stockage,
                ':temp_conseille' => $temp_conseille,
                ':delai_alerte_jours' => (int)$delai_alerte_jours,
                ':id_cat' => (int)$id
            ]);
            
            return ['success' => true];
        } catch (PDOException $e) {
            error_log('Error updating categorie: ' . $e->getMessage());
            return ['success' => false, 'error' => $e->getMessage()];
        }
    }

    /**
     * CRUD: DELETE - Supprimer une catégorie
     */
    public function delete($id) {
        try {
            // Vérifier s'il y a des produits associés
            $sql = 'SELECT COUNT(*) as count FROM produit WHERE id_cat = :id_cat';
            $stmt = $this->db->prepare($sql);
            $stmt->execute([':id_cat' => (int)$id]);
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if ($row['count'] > 0) {
                return ['success' => false, 'error' => 'Impossible de supprimer. Des produits sont associés à cette catégorie.'];
            }
            
            $sql = 'DELETE FROM categorie WHERE id_cat = :id_cat';
            $stmt = $this->db->prepare($sql);
            $stmt->execute([':id_cat' => (int)$id]);
            
            return ['success' => true];
        } catch (PDOException $e) {
            error_log('Error deleting categorie: ' . $e->getMessage());
            return ['success' => false, 'error' => $e->getMessage()];
        }
    }

    /**
     * Compter les catégories
     */
    public function count() {
        try {
            $sql = 'SELECT COUNT(*) as total FROM categorie';
            $stmt = $this->db->prepare($sql);
            $stmt->execute();
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            return (int)$row['total'];
        } catch (PDOException $e) {
            error_log('Error counting categories: ' . $e->getMessage());
            return 0;
        }
    }

    /**
     * Compter les produits d'une catégorie
     */
    public function countProduits($id_cat) {
        try {
            $sql = 'SELECT COUNT(*) as total FROM produit WHERE id_cat = :id_cat';
            $stmt = $this->db->prepare($sql);
            $stmt->execute([':id_cat' => (int)$id_cat]);
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            return (int)$row['total'];
        } catch (PDOException $e) {
            error_log('Error counting products: ' . $e->getMessage());
            return 0;
        }
    }
}
?>
