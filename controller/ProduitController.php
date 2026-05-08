<?php
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../model/Produit.php';

/**
 * ProduitController - Gestion des produits et du stock
 * Version PDO
 */
class ProduitController {
    private $db;

    public function __construct() {
        $this->db = config::getConnexion();
    }

    /**
     * CRUD: CREATE - Ajouter un produit
     */
    public function create($nom_prod, $id_cat, $date_expiration = null, $poids_produit = null, $quantite_dispo = 0) {
        try {
            $sql = 'INSERT INTO produit (nom_prod, date_expiration, poids_produit, quantite_dispo, id_cat) 
                    VALUES (:nom_prod, :date_expiration, :poids_produit, :quantite_dispo, :id_cat)';
            $stmt = $this->db->prepare($sql);
            $stmt->execute([
                ':nom_prod' => $nom_prod,
                ':date_expiration' => $date_expiration,
                ':poids_produit' => (float)$poids_produit,
                ':quantite_dispo' => (int)$quantite_dispo,
                ':id_cat' => (int)$id_cat
            ]);
            
            return ['success' => true, 'id' => $this->db->lastInsertId()];
        } catch (PDOException $e) {
            error_log('Error creating produit: ' . $e->getMessage());
            return ['success' => false, 'error' => $e->getMessage()];
        }
    }

    /**
     * CRUD: READ - Récupérer tous les produits
     */
    public function getAll($search = '', $id_cat = null) {
        try {
            $query = 'SELECT p.*, c.nom_cat as categorie_nom FROM produit p 
                      LEFT JOIN categorie c ON p.id_cat = c.id_cat';
            $params = [];
            $conditions = [];
            
            if (!empty($search)) {
                $conditions[] = '(p.nom_prod LIKE :search)';
                $params[':search'] = '%' . $search . '%';
            }
            if ($id_cat !== null) {
                $conditions[] = 'p.id_cat = :id_cat';
                $params[':id_cat'] = (int)$id_cat;
            }
            
            if (count($conditions) > 0) {
                $query .= ' WHERE ' . implode(' AND ', $conditions);
            }
            
            $query .= ' ORDER BY p.id_prod DESC';
            
            $stmt = $this->db->prepare($query);
            $stmt->execute($params);
            
            $produits = [];
            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                $produits[] = Produit::fromArray($row);
            }
            
            return $produits;
        } catch (PDOException $e) {
            error_log('Error fetching produits: ' . $e->getMessage());
            return [];
        }
    }

    /**
     * CRUD: READ - Récupérer un produit par ID
     */
    public function getById($id) {
        try {
            $sql = 'SELECT p.*, c.nom_cat as categorie_nom FROM produit p 
                    LEFT JOIN categorie c ON p.id_cat = c.id_cat 
                    WHERE p.id_prod = :id_prod';
            $stmt = $this->db->prepare($sql);
            $stmt->execute([':id_prod' => (int)$id]);
            
            if ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                return Produit::fromArray($row);
            }
            
            return null;
        } catch (PDOException $e) {
            error_log('Error fetching produit: ' . $e->getMessage());
            return null;
        }
    }

    /**
     * CRUD: UPDATE - Modifier un produit
     */
    public function update($id, $nom_prod, $id_cat, $date_expiration = null, $poids_produit = null, $quantite_dispo = null) {
        try {
            $sql = 'UPDATE produit SET nom_prod = :nom_prod, date_expiration = :date_expiration, 
                    poids_produit = :poids_produit, quantite_dispo = :quantite_dispo, id_cat = :id_cat 
                    WHERE id_prod = :id_prod';
            $stmt = $this->db->prepare($sql);
            $stmt->execute([
                ':nom_prod' => $nom_prod,
                ':date_expiration' => $date_expiration,
                ':poids_produit' => (float)($poids_produit ?? 0),
                ':quantite_dispo' => (int)($quantite_dispo ?? 0),
                ':id_cat' => (int)$id_cat,
                ':id_prod' => (int)$id
            ]);
            
            return ['success' => true];
        } catch (PDOException $e) {
            error_log('Error updating produit: ' . $e->getMessage());
            return ['success' => false, 'error' => $e->getMessage()];
        }
    }

    /**
     * CRUD: DELETE - Supprimer un produit
     */
    public function delete($id) {
        try {
            $sql = 'DELETE FROM produit WHERE id_prod = :id_prod';
            $stmt = $this->db->prepare($sql);
            $stmt->execute([':id_prod' => (int)$id]);
            
            return ['success' => true];
        } catch (PDOException $e) {
            error_log('Error deleting produit: ' . $e->getMessage());
            return ['success' => false, 'error' => $e->getMessage()];
        }
    }

    /**
     * GESTION DE STOCK: Augmenter le stock (Achat/Réception)
     */
    public function augmenterStock($id_prod, $quantite) {
        if ($quantite <= 0) {
            return ['success' => false, 'error' => 'La quantité doit être positive'];
        }
        
        try {
            $sql = 'UPDATE produit SET quantite_dispo = quantite_dispo + :quantite 
                    WHERE id_prod = :id_prod';
            $stmt = $this->db->prepare($sql);
            $stmt->execute([
                ':quantite' => (int)$quantite,
                ':id_prod' => (int)$id_prod
            ]);
            
            return ['success' => true];
        } catch (PDOException $e) {
            error_log('Error augmenting stock: ' . $e->getMessage());
            return ['success' => false, 'error' => $e->getMessage()];
        }
    }

    /**
     * GESTION DE STOCK: Diminuer le stock (Vente/Consommation)
     */
    public function diminuerStock($id_prod, $quantite) {
        if ($quantite <= 0) {
            return ['success' => false, 'error' => 'La quantité doit être positive'];
        }
        
        try {
            // Vérifier qu'il y a assez de stock
            $produit = $this->getById($id_prod);
            if ($produit->getQuantiteDispo() < $quantite) {
                return ['success' => false, 'error' => 'Stock insuffisant. Disponible: ' . $produit->getQuantiteDispo()];
            }
            
            $sql = 'UPDATE produit SET quantite_dispo = quantite_dispo - :quantite 
                    WHERE id_prod = :id_prod';
            $stmt = $this->db->prepare($sql);
            $stmt->execute([
                ':quantite' => (int)$quantite,
                ':id_prod' => (int)$id_prod
            ]);
            
            return ['success' => true];
        } catch (PDOException $e) {
            error_log('Error diminishing stock: ' . $e->getMessage());
            return ['success' => false, 'error' => $e->getMessage()];
        }
    }

    /**
     * GESTION DE STOCK: Obtenir produits en bas de stock
     */
    public function getProduitsBasStock() {
        try {
            $sql = 'SELECT * FROM produit WHERE quantite_dispo <= 5 ORDER BY quantite_dispo ASC';
            $stmt = $this->db->prepare($sql);
            $stmt->execute();
            
            $produits = [];
            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                $produits[] = Produit::fromArray($row);
            }
            
            return $produits;
        } catch (PDOException $e) {
            error_log('Error fetching low stock: ' . $e->getMessage());
            return [];
        }
    }

    /**
     * GESTION DE STOCK: Obtenir produits en rupture
     */
    public function getProduitRupture() {
        try {
            $sql = 'SELECT * FROM produit WHERE quantite_dispo = 0';
            $stmt = $this->db->prepare($sql);
            $stmt->execute();
            
            $produits = [];
            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                $produits[] = Produit::fromArray($row);
            }
            
            return $produits;
        } catch (PDOException $e) {
            error_log('Error fetching out of stock: ' . $e->getMessage());
            return [];
        }
    }

    /**
     * Compter les produits
     */
    public function count() {
        try {
            $sql = 'SELECT COUNT(*) as total FROM produit';
            $stmt = $this->db->prepare($sql);
            $stmt->execute();
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            return (int)$row['total'];
        } catch (PDOException $e) {
            error_log('Error counting produits: ' . $e->getMessage());
            return 0;
        }
    }

    /**
     * Compter les produits en bas de stock
     */
    public function countBasStock() {
        try {
            $sql = 'SELECT COUNT(*) as total FROM produit WHERE quantite_dispo <= 5';
            $stmt = $this->db->prepare($sql);
            $stmt->execute();
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            return (int)$row['total'];
        } catch (PDOException $e) {
            error_log('Error counting low stock: ' . $e->getMessage());
            return 0;
        }
    }

    /**
     * Obtenir la valeur totale du stock (calculée par poids)
     */
    public function getValeurStockTotal() {
        $stmt = $this->db->query('SELECT SUM(quantite_dispo * poids_produit) as valeur_totale FROM produit');
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return $row['valeur_totale'] ?? 0;
    }
}
?>
