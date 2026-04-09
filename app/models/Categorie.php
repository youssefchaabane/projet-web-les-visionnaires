<?php
require_once __DIR__ . '/../../config/Database.php';

/**
 * Modèle Categorie
 */
class Categorie {
    private $pdo;
    private $id_cat;
    private $nom_cat;
    private $description_cat;
    private $lieu_stockage;
    private $temp_conseille;
    private $delai_alerte_jours;

    const TYPES_LIEU = ['Frigorifique', 'Congélateur', 'Température ambiante', 'Darkness', 'Humidité controlée'];

    public function __construct() {
        $this->pdo = Database::getInstance()->getConnection();
    }

    // Setters
    public function setNomCat($nom) {
        if (strlen($nom) < 3 || strlen($nom) > 100) {
            throw new Exception('Le nom doit contenir entre 3 et 100 caractères');
        }
        $this->nom_cat = $nom;
    }

    public function setDescriptionCat($desc) {
        if (strlen($desc) < 5) {
            throw new Exception('La description doit contenir au minimum 5 caractères');
        }
        $this->description_cat = $desc;
    }

    public function setLieuStockage($lieu) {
        $this->lieu_stockage = $lieu;
    }

    public function setTempConseille($temp) {
        $this->temp_conseille = floatval($temp);
    }

    public function setDelaiAlertJours($delai) {
        $this->delai_alerte_jours = intval($delai);
    }

    // Getters
    public function getIdCat() { return $this->id_cat; }
    public function getNomCat() { return $this->nom_cat; }
    public function getDescriptionCat() { return $this->description_cat; }
    public function getLieuStockage() { return $this->lieu_stockage; }
    public function getTempConseille() { return $this->temp_conseille; }
    public function getDelaiAlertJours() { return $this->delai_alerte_jours; }

    /**
     * Créer une nouvelle catégorie
     */
    public function creer() {
        $stmt = $this->pdo->prepare("
            INSERT INTO categorie (nom_cat, description_cat, lieu_stockage, temp_conseille, delai_alerte_jours)
            VALUES (?, ?, ?, ?, ?)
        ");
        return $stmt->execute([
            $this->nom_cat,
            $this->description_cat,
            $this->lieu_stockage,
            $this->temp_conseille,
            $this->delai_alerte_jours
        ]);
    }

    /**
     * Mettre à jour une catégorie
     */
    public function mettreAJour($id) {
        $stmt = $this->pdo->prepare("
            UPDATE categorie 
            SET nom_cat = ?, description_cat = ?, lieu_stockage = ?, temp_conseille = ?, delai_alerte_jours = ?
            WHERE id_cat = ?
        ");
        return $stmt->execute([
            $this->nom_cat,
            $this->description_cat,
            $this->lieu_stockage,
            $this->temp_conseille,
            $this->delai_alerte_jours,
            $id
        ]);
    }

    /**
     * Supprimer une catégorie
     */
    public function supprimer($id) {
        $stmt = $this->pdo->prepare("DELETE FROM categorie WHERE id_cat = ?");
        return $stmt->execute([$id]);
    }

    /**
     * Obtenir une catégorie par ID
     */
    public function obtenirParId($id) {
        $stmt = $this->pdo->prepare("SELECT * FROM categorie WHERE id_cat = ?");
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    /**
     * Obtenir toutes les catégories
     */
    public function obtenirTous($limite = 10, $offset = 0) {
        $stmt = $this->pdo->prepare("
            SELECT * FROM categorie 
            ORDER BY nom_cat ASC 
            LIMIT " . intval($limite) . " OFFSET " . intval($offset) . "
        ");
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Obtenir le nombre total de catégories
     */
    public function obtenirNombreTotalCategories() {
        $stmt = $this->pdo->query("SELECT COUNT(*) as total FROM categorie");
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result['total'];
    }
}
