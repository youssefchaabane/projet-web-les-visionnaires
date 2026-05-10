<?php
/**
 * Classe DetailRecette - Model (pur data model)
 * Contient UNIQUEMENT: attributs + constructeur + getters + setters
 */
class DetailRecette
{
    // Propriétés privées
    private $id_detail;
    private $id_recette;
    private $ingredient;
    private $quantite;
    private $etape;

    /**
     * Constructeur paramétré
     */
    public function __construct(
        $id_recette = null,
        $ingredient = null,
        $quantite = null,
        $etape = null,
        $id_detail = null
    ) {
        $this->id_detail = $id_detail;
        $this->id_recette = $id_recette;
        $this->ingredient = $ingredient;
        $this->quantite = $quantite;
        $this->etape = $etape;
    }

    // ===== GETTERS =====
    public function getIdDetail() { return $this->id_detail; }
    public function getIdRecette() { return $this->id_recette; }
    public function getIngredient() { return $this->ingredient; }
    public function getQuantite() { return $this->quantite; }
    public function getEtape() { return $this->etape; }

    // ===== SETTERS (Fluent Interface) =====
    public function setIdDetail($id_detail) { 
        $this->id_detail = $id_detail; 
        return $this; 
    }
    
    public function setIdRecette($id_recette) { 
        $this->id_recette = intval($id_recette); 
        return $this; 
    }
    
    public function setIngredient($ingredient) { 
        $this->ingredient = htmlspecialchars(trim($ingredient)); 
        return $this; 
    }
    
    public function setQuantite($quantite) { 
        $this->quantite = htmlspecialchars(trim($quantite)); 
        return $this; 
    }
    
    public function setEtape($etape) { 
        $this->etape = htmlspecialchars(trim($etape)); 
        return $this; 
    }
}
?>
