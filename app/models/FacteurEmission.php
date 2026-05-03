<?php
/**
 * Classe FacteurEmission - Model
 */
class FacteurEmission
{
    private $id_facteur;
    private $categorie_aliment;
    private $co2_par_kg;
    private $source_donnee;
    private $date_derniere_maj;

    public function __construct(
        $categorie_aliment = null,
        $co2_par_kg = null,
        $source_donnee = null,
        $date_derniere_maj = null,
        $id_facteur = null
    ) {
        $this->id_facteur = $id_facteur;
        $this->categorie_aliment = $categorie_aliment;
        $this->co2_par_kg = $co2_par_kg;
        $this->source_donnee = $source_donnee;
        $this->date_derniere_maj = $date_derniere_maj;
    }

    public function getId() { return $this->id_facteur; }
    public function getCategorieAliment() { return $this->categorie_aliment; }
    public function getCo2ParKg() { return $this->co2_par_kg; }
    public function getSourceDonnee() { return $this->source_donnee; }
    public function getDateDerniereMaj() { return $this->date_derniere_maj; }

    public function setId($id) { $this->id_facteur = $id; return $this; }
    public function setCategorieAliment($cat) { $this->categorie_aliment = htmlspecialchars(trim($cat)); return $this; }
    public function setCo2ParKg($co2) { $this->co2_par_kg = floatval($co2); return $this; }
    public function setSourceDonnee($src) { $this->source_donnee = htmlspecialchars(trim($src)); return $this; }
    public function setDateDerniereMaj($date) { $this->date_derniere_maj = $date; return $this; }
}
