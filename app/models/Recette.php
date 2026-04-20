<?php
/**
 * Classe Recette - Model (pur data model)
 * Contient UNIQUEMENT: attributs + constantes + constructeur + getters + setters
 * PAS de CRUD, PAS de requêtes PDO (delegué au Controller)
 */
class Recette
{
    // Propriétés privées
    private $id_recette;
    private $nom;
    private $description;
    private $nombre_personnes;
    private $temps_preparation;
    private $temps_cuisson;
    private $difficulte;
    private $calories_totales;
    private $id_user;
    private $date_creation;
    private $date_modification;

    // Constantes
    const DIFFICULTES = ['facile', 'moyen', 'difficile'];

    /**
     * Constructeur paramétré
     */
    public function __construct(
        $nom = null,
        $description = null,
        $nombre_personnes = null,
        $temps_preparation = null,
        $temps_cuisson = null,
        $difficulte = 'moyen',
        $calories_totales = null,
        $id_user = null,
        $id_recette = null
    ) {
        $this->id_recette = $id_recette;
        $this->nom = $nom;
        $this->description = $description;
        $this->nombre_personnes = $nombre_personnes;
        $this->temps_preparation = $temps_preparation;
        $this->temps_cuisson = $temps_cuisson;
        $this->difficulte = $difficulte;
        $this->calories_totales = $calories_totales;
        $this->id_user = $id_user;
    }

    // ===== GETTERS =====
    public function getId() { return $this->id_recette; }
    public function getNom() { return $this->nom; }
    public function getDescription() { return $this->description; }
    public function getNombrePersonnes() { return $this->nombre_personnes; }
    public function getTempsPreparation() { return $this->temps_preparation; }
    public function getTempsCuisson() { return $this->temps_cuisson; }
    public function getDifficulte() { return $this->difficulte; }
    public function getCaloriesTotales() { return $this->calories_totales; }
    public function getIdUser() { return $this->id_user; }
    public function getDateCreation() { return $this->date_creation; }
    public function getDateModification() { return $this->date_modification; }

    // ===== SETTERS (Fluent Interface) =====
    public function setIdRecette($id_recette) { $this->id_recette = $id_recette; return $this; }
    
    public function setNom($nom) { 
        $this->nom = htmlspecialchars(trim($nom)); 
        return $this; 
    }
    
    public function setDescription($description) { 
        $this->description = htmlspecialchars(trim($description)); 
        return $this; 
    }
    
    public function setNombrePersonnes($nombre_personnes) { 
        $this->nombre_personnes = intval($nombre_personnes); 
        return $this; 
    }
    
    public function setTempsPreparation($temps_preparation) { 
        $this->temps_preparation = intval($temps_preparation); 
        return $this; 
    }
    
    public function setTempsCuisson($temps_cuisson) { 
        $this->temps_cuisson = intval($temps_cuisson); 
        return $this; 
    }
    
    public function setDifficulte($difficulte) { 
        $this->difficulte = htmlspecialchars(trim($difficulte)); 
        return $this; 
    }
    
    public function setCaloriesTotales($calories_totales) { 
        $this->calories_totales = intval($calories_totales); 
        return $this; 
    }
    
    public function setIdUser($id_user) { 
        $this->id_user = intval($id_user); 
        return $this; 
    }
}
?>
