<?php

class Allergie {

    // 🔹 Attributs
    private $id_allergie;
    private $nom;
    private $description;
    private $niveau_danger;
    private $symptomes;
    private $type;

    // 🔹 Constructeur paramétré
    public function __construct(
        $id_allergie = null,
        $nom = "",
        $description = "",
        $niveau_danger = "",
        $symptomes = "",
        $type = ""
    ) {
        $this->id_allergie = $id_allergie;
        $this->nom = $nom;
        $this->description = $description;
        $this->niveau_danger = $niveau_danger;
        $this->symptomes = $symptomes;
        $this->type = $type;
    }

    // 🔹 Getters
    public function getId() {
        return $this->id_allergie;
    }

    public function getNom() {
        return $this->nom;
    }

    public function getDescription() {
        return $this->description;
    }

    public function getNiveauDanger() {
        return $this->niveau_danger;
    }

    public function getSymptomes() {
        return $this->symptomes;
    }

    public function getType() {
        return $this->type;
    }

    // 🔹 Setters
    public function setId($id) {
        $this->id_allergie = $id;
    }

    public function setNom($nom) {
        $this->nom = $nom;
    }

    public function setDescription($description) {
        $this->description = $description;
    }

    public function setNiveauDanger($niveau) {
        $this->niveau_danger = $niveau;
    }

    public function setSymptomes($symptomes) {
        $this->symptomes = $symptomes;
    }

    public function setType($type) {
        $this->type = $type;
    }
}
?>
