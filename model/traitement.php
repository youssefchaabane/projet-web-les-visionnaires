<?php

class Traitement {

    // 🔹 Attributs
    private $id_traitement;
    private $nom;
    private $type_traitement;
    private $dosage;
    private $duree;
    private $effets_secondaires;

    // 🔹 Constructeur
    public function __construct(
        $id_traitement = null,
        $nom = "",
        $type_traitement = "",
        $dosage = "",
        $duree = "",
        $effets_secondaires = ""
    ) {
        $this->id_traitement = $id_traitement;
        $this->nom = $nom;
        $this->type_traitement = $type_traitement;
        $this->dosage = $dosage;
        $this->duree = $duree;
        $this->effets_secondaires = $effets_secondaires;
    }

    // 🔹 Getters
    public function getId() {
        return $this->id_traitement;
    }

    public function getNom() {
        return $this->nom;
    }

    public function getTypeTraitement() {
        return $this->type_traitement;
    }

    public function getDosage() {
        return $this->dosage;
    }

    public function getDuree() {
        return $this->duree;
    }

    public function getEffetsSecondaires() {
        return $this->effets_secondaires;
    }

    // 🔹 Setters
    public function setId($id) {
        $this->id_traitement = $id;
    }

    public function setNom($nom) {
        $this->nom = $nom;
    }

    public function setTypeTraitement($type) {
        $this->type_traitement = $type;
    }

    public function setDosage($dosage) {
        $this->dosage = $dosage;
    }

    public function setDuree($duree) {
        $this->duree = $duree;
    }

    public function setEffetsSecondaires($effets) {
        $this->effets_secondaires = $effets;
    }
}
?>
