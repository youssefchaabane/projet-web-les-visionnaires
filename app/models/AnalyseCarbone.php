<?php
/**
 * Classe AnalyseCarbone - Model
 */
class AnalyseCarbone
{
    private $id_analyse;
    private $score_co2_total;
    private $niveau_impact;
    private $date_calcul;
    private $methode_calcul;
    private $id_recette;

    const NIVEAUX_IMPACT = ['bas', 'moyen', 'élevé'];

    public function __construct(
        $score_co2_total = null,
        $niveau_impact = null,
        $date_calcul = null,
        $methode_calcul = null,
        $id_recette = null,
        $id_analyse = null
    ) {
        $this->id_analyse = $id_analyse;
        $this->score_co2_total = $score_co2_total;
        $this->niveau_impact = $niveau_impact;
        $this->date_calcul = $date_calcul;
        $this->methode_calcul = $methode_calcul;
        $this->id_recette = $id_recette;
    }

    public function getId() { return $this->id_analyse; }
    public function getScoreCo2Total() { return $this->score_co2_total; }
    public function getNiveauImpact() { return $this->niveau_impact; }
    public function getDateCalcul() { return $this->date_calcul; }
    public function getMethodeCalcul() { return $this->methode_calcul; }
    public function getIdRecette() { return $this->id_recette; }

    public function setId($id) { $this->id_analyse = $id; return $this; }
    public function setScoreCo2Total($score) { $this->score_co2_total = floatval($score); return $this; }
    public function setNiveauImpact($impact) { $this->niveau_impact = htmlspecialchars(trim($impact)); return $this; }
    public function setDateCalcul($date) { $this->date_calcul = $date; return $this; }
    public function setMethodeCalcul($methode) { $this->methode_calcul = htmlspecialchars(trim($methode)); return $this; }
    public function setIdRecette($id_recette) { $this->id_recette = intval($id_recette); return $this; }
}
