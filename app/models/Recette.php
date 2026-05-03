<?php
/**
 * Classe Recette - Model
 */
class Recette
{
    private $id_recette;
    private $nom;
    private $description;
    private $date_creation;

    public function __construct(
        $nom = null,
        $description = null,
        $id_recette = null,
        $date_creation = null
    ) {
        $this->id_recette = $id_recette;
        $this->nom = $nom;
        $this->description = $description;
        $this->date_creation = $date_creation;
    }

    public function getId() { return $this->id_recette; }
    public function getNom() { return $this->nom; }
    public function getDescription() { return $this->description; }
    public function getDateCreation() { return $this->date_creation; }

    public function setId($id) { $this->id_recette = $id; return $this; }
    public function setNom($nom) { $this->nom = htmlspecialchars(trim($nom)); return $this; }
    public function setDescription($desc) { $this->description = htmlspecialchars(trim($desc)); return $this; }
}
