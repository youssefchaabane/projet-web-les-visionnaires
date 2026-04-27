<?php
/**
 * Modèle Categorie - DTO (Data Transfer Object)
 

 */
class Categorie {
    private $id_cat;
    private $nom_cat;
    private $description_cat;
    private $lieu_stockage;
    private $temp_conseille;
    private $delai_alerte_jours;
    private $date_creation;
    private $date_modification;
    private $produits_count;

    public function __construct($data = []) {
        $this->id_cat = $data['id_cat'] ?? null;
        $this->nom_cat = $data['nom_cat'] ?? '';
        $this->description_cat = $data['description_cat'] ?? '';
        $this->lieu_stockage = $data['lieu_stockage'] ?? '';
        $this->temp_conseille = $data['temp_conseille'] ?? '';
        $this->delai_alerte_jours = $data['delai_alerte_jours'] ?? 30;
        $this->date_creation = $data['date_creation'] ?? null;
        $this->date_modification = $data['date_modification'] ?? null;
        $this->produits_count = isset($data['produits_count']) ? (int)$data['produits_count'] : 0;
    }

    public static function fromArray($row) {
        return new self($row);
    }

    public function toArray() {
        return [
            'id_cat' => $this->id_cat,
            'nom_cat' => $this->nom_cat,
            'description_cat' => $this->description_cat,
            'lieu_stockage' => $this->lieu_stockage,
            'temp_conseille' => $this->temp_conseille,
            'delai_alerte_jours' => $this->delai_alerte_jours,
            'date_creation' => $this->date_creation,
            'date_modification' => $this->date_modification,
            'produits_count' => $this->produits_count
        ];
    }

    // ============ GETTERS ============
    public function getIdCat() {
        return $this->id_cat;
    }

    public function getNomCat() {
        return $this->nom_cat;
    }

    public function getDescriptionCat() {
        return $this->description_cat;
    }

    public function getLieuStockage() {
        return $this->lieu_stockage;
    }

    public function getTempConseille() {
        return $this->temp_conseille;
    }

    public function getDelaiAlertJours() {
        return $this->delai_alerte_jours;
    }

    public function getDateCreation() {
        return $this->date_creation;
    }

    public function getDateModification() {
        return $this->date_modification;
    }

    // ============ SETTERS ============
    public function setNomCat($nom_cat) {
        $this->nom_cat = $nom_cat;
        return $this;
    }

    public function setDescriptionCat($description_cat) {
        $this->description_cat = $description_cat;
        return $this;
    }

    public function setLieuStockage($lieu_stockage) {
        $this->lieu_stockage = $lieu_stockage;
        return $this;
    }

    public function setTempConseille($temp_conseille) {
        $this->temp_conseille = $temp_conseille;
        return $this;
    }

    public function setDelaiAlertJours($delai_alerte_jours) {
        $this->delai_alerte_jours = (int)$delai_alerte_jours;
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
}
?>
