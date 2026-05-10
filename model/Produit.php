<?php
/**
 * Modèle Produit - DTO (Data Transfer Object)
 * Gestion du stock inclus
 * 
 */
class Produit {
    private $id_prod;
    private $nom_prod;
    private $date_expiration;
    private $poids_produit;
    private $quantite_dispo;
    private $id_cat;
    private $date_creation;
    private $date_modification;

    public function __construct($data = []) {
        $this->id_prod = $data['id_prod'] ?? null;
        $this->nom_prod = $data['nom_prod'] ?? '';
        $this->date_expiration = $data['date_expiration'] ?? null;
        $this->poids_produit = $data['poids_produit'] ?? 0;
        $this->quantite_dispo = $data['quantite_dispo'] ?? 0;
        $this->id_cat = $data['id_cat'] ?? null;
        $this->date_creation = $data['date_creation'] ?? null;
        $this->date_modification = $data['date_modification'] ?? null;
    }

    public static function fromArray($row) {
        return new self($row);
    }

    public function toArray() {
        return [
            'id_prod' => $this->id_prod,
            'nom_prod' => $this->nom_prod,
            'date_expiration' => $this->date_expiration,
            'poids_produit' => $this->poids_produit,
            'quantite_dispo' => $this->quantite_dispo,
            'id_cat' => $this->id_cat,
            'date_creation' => $this->date_creation,
            'date_modification' => $this->date_modification
        ];
    }

    // ============ GETTERS ============
    public function getIdProd() {
        return $this->id_prod;
    }

    public function getNomProd() {
        return $this->nom_prod;
    }

    public function getDateExpiration() {
        return $this->date_expiration;
    }

    public function getPoidsProduit() {
        return $this->poids_produit;
    }

    public function getQuantiteDispo() {
        return $this->quantite_dispo;
    }

    public function getIdCat() {
        return $this->id_cat;
    }

    public function getDateCreation() {
        return $this->date_creation;
    }

    public function getDateModification() {
        return $this->date_modification;
    }

    // ============ SETTERS ============
    public function setNomProd($nom_prod) {
        $this->nom_prod = $nom_prod;
        return $this;
    }

    public function setDateExpiration($date_expiration) {
        $this->date_expiration = $date_expiration;
        return $this;
    }

    public function setPoidsProduit($poids_produit) {
        $this->poids_produit = (float)$poids_produit;
        return $this;
    }

    public function setQuantiteDispo($quantite_dispo) {
        $this->quantite_dispo = (int)$quantite_dispo;
        return $this;
    }

    public function setIdCat($id_cat) {
        $this->id_cat = (int)$id_cat;
        return $this;
    }

    public function setDateCreation($date_creation) {
        $this->date_creation = $date_creation;
        return $this;
    }

    public function setDateModification($date_modification) {
        $this->date_modification = $date_modification;
        return $this;
    }

    /**
     * Vérifie si le produit est en bas de stock
     */
    public function isStockBas() {
        return $this->quantite_dispo <= 5; // Seuil par défaut
    }

    /**
     * Obtient le statut du stock
     */
    public function getStatusStock() {
        if ($this->quantite_dispo === 0) return 'RUPTURE';
        if ($this->isStockBas()) return 'BAS';
        return 'OK';
    }

    /**
     * Vérifie si le produit est expiré
     */
    public function isExpired() {
        if (!$this->date_expiration) return false;
        return strtotime($this->date_expiration) < time();
    }
}
?>
