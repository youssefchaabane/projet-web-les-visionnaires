<?php
require_once __DIR__ . '/../../config/config.php';

/**
 * Modèle Produit
 */
class Produit {
    private $pdo;
    private $id_prod;
    private $nom_prod;
    private $date_expiration;
    private $poids_produit;
    private $quantite_dispo;
    private $id_cat;

    public function __construct() {
        $this->pdo = config::getConnexion();
    }

    // Setters
    public function setNomProd($nom) {
        if (strlen($nom) < 3 || strlen($nom) > 100) {
            throw new Exception('Le nom doit contenir entre 3 et 100 caractères');
        }
        $this->nom_prod = $nom;
    }

    public function setDateExpiration($date) {
        $this->date_expiration = $date;
    }

    public function setPoidsProduit($poids) {
        $this->poids_produit = floatval($poids);
    }

    public function setQuantiteDispo($qtе) {
        $this->quantite_dispo = intval($qtе);
    }

    public function setIdCat($id) {
        $this->id_cat = intval($id);
    }

    // Getters
    public function getIdProd() { return $this->id_prod; }
    public function getNomProd() { return $this->nom_prod; }
    public function getDateExpiration() { return $this->date_expiration; }
    public function getPoidsProduit() { return $this->poids_produit; }
    public function getQuantiteDispo() { return $this->quantite_dispo; }
    public function getIdCat() { return $this->id_cat; }

    /**
     * Créer un nouveau produit
     */
    public function creer() {
        $stmt = $this->pdo->prepare("
            INSERT INTO produit (nom_prod, date_expiration, poids_produit, quantite_dispo, id_cat)
            VALUES (?, ?, ?, ?, ?)
        ");
        return $stmt->execute([
            $this->nom_prod,
            $this->date_expiration,
            $this->poids_produit,
            $this->quantite_dispo,
            $this->id_cat
        ]);
    }

    /**
     * Mettre à jour un produit
     */
    public function mettreAJour($id) {
        $stmt = $this->pdo->prepare("
            UPDATE produit 
            SET nom_prod = ?, date_expiration = ?, poids_produit = ?, quantite_dispo = ?, id_cat = ?
            WHERE id_prod = ?
        ");
        return $stmt->execute([
            $this->nom_prod,
            $this->date_expiration,
            $this->poids_produit,
            $this->quantite_dispo,
            $this->id_cat,
            $id
        ]);
    }

    /**
     * Supprimer un produit
     */
    public function supprimer($id) {
        $stmt = $this->pdo->prepare("DELETE FROM produit WHERE id_prod = ?");
        return $stmt->execute([$id]);
    }

    /**
     * Obtenir un produit par ID
     */
    public function obtenirParId($id) {
        $stmt = $this->pdo->prepare("
            SELECT p.*, c.nom_cat 
            FROM produit p
            LEFT JOIN categorie c ON p.id_cat = c.id_cat
            WHERE p.id_prod = ?
        ");
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    /**
     * Obtenir tous les produits
     */
    public function obtenirTous($limite = 10, $offset = 0) {
        $stmt = $this->pdo->prepare("
            SELECT p.*, c.nom_cat 
            FROM produit p
            LEFT JOIN categorie c ON p.id_cat = c.id_cat
            ORDER BY p.nom_prod ASC 
            LIMIT " . intval($limite) . " OFFSET " . intval($offset) . "
        ");
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Obtenir le nombre total de produits
     */
    public function obtenirNombreTotalProduits() {
        $stmt = $this->pdo->query("SELECT COUNT(*) as total FROM produit");
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result['total'];
    }

    /**
     * Obtenir les produits bientôt expirés
     */
    public function obtenirProduitsExpiration($jours = 7) {
        $stmt = $this->pdo->prepare("
            SELECT p.*, c.nom_cat 
            FROM produit p
            LEFT JOIN categorie c ON p.id_cat = c.id_cat
            WHERE DATE(p.date_expiration) <= DATE_ADD(NOW(), INTERVAL :jours DAY)
            AND DATE(p.date_expiration) > NOW()
            ORDER BY p.date_expiration ASC
        ");
        $stmt->execute(['jours' => intval($jours)]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
