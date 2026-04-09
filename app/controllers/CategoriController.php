<?php
require_once __DIR__ . '/../models/Categorie.php';

/**
 * Contrôleur Categorie - Gère les actions CRUD et la logique métier
 */
class CategoriController {
    private $categorie;

    public function __construct() {
        $this->categorie = new Categorie();
    }

    /**
     * Affiche la liste des catégories
     */
    public function afficherListeAdmin() {
        $page = isset($_REQUEST['page']) ? max(1, intval($_REQUEST['page'])) : 1;
        $limite = 10;
        $offset = ($page - 1) * $limite;

        $categories = $this->categorie->obtenirTous($limite, $offset);
        $total = $this->categorie->obtenirNombreTotalCategories();
        $nombre_pages = ceil($total / $limite);

        return [
            'categories' => $categories,
            'page' => $page,
            'nombre_pages' => $nombre_pages,
            'total' => $total
        ];
    }

    /**
     * Affiche le formulaire d'ajout de catégorie
     */
    public function afficherFormulaireAjout() {
        return [
            'titre' => 'Ajouter une catégorie',
            'lieux' => Categorie::TYPES_LIEU
        ];
    }

    /**
     * Crée une nouvelle catégorie
     */
    public function creerCategorie($donnees) {
        $erreurs = [];

        try {
            $this->categorie->setNomCat($donnees['nom_cat'] ?? '');
            $this->categorie->setDescriptionCat($donnees['description_cat'] ?? '');
            $this->categorie->setLieuStockage($donnees['lieu_stockage'] ?? '');
            $this->categorie->setTempConseille($donnees['temp_conseille'] ?? 0);
            $this->categorie->setDelaiAlertJours($donnees['delai_alerte_jours'] ?? 7);

            if ($this->categorie->creer()) {
                return ['succes' => true, 'message' => 'Catégorie créée avec succès'];
            } else {
                return ['succes' => false, 'erreurs' => ['Erreur lors de la création']];
            }
        } catch (Exception $e) {
            return ['succes' => false, 'erreurs' => [$e->getMessage()]];
        }
    }

    /**
     * Affiche le formulaire d'édition de catégorie
     */
    public function afficherFormulaireEdition($id) {
        $categorie_data = $this->categorie->obtenirParId($id);
        if (!$categorie_data) {
            return ['erreur' => 'Catégorie non trouvée'];
        }
        return [
            'categorie' => $categorie_data,
            'lieux' => Categorie::TYPES_LIEU
        ];
    }

    /**
     * Met à jour une catégorie
     */
    public function mettreAJourCategorie($id, $donnees) {
        $erreurs = [];

        try {
            $this->categorie->setNomCat($donnees['nom_cat'] ?? '');
            $this->categorie->setDescriptionCat($donnees['description_cat'] ?? '');
            $this->categorie->setLieuStockage($donnees['lieu_stockage'] ?? '');
            $this->categorie->setTempConseille($donnees['temp_conseille'] ?? 0);
            $this->categorie->setDelaiAlertJours($donnees['delai_alerte_jours'] ?? 7);

            if ($this->categorie->mettreAJour($id)) {
                return ['succes' => true, 'message' => 'Catégorie modifiée avec succès'];
            } else {
                return ['succes' => false, 'erreurs' => ['Erreur lors de la modification']];
            }
        } catch (Exception $e) {
            return ['succes' => false, 'erreurs' => [$e->getMessage()]];
        }
    }

    /**
     * Supprime une catégorie
     */
    public function supprimerCategorie($id) {
        if ($this->categorie->supprimer($id)) {
            return ['succes' => true, 'message' => 'Catégorie supprimée avec succès'];
        } else {
            return ['succes' => false, 'erreur' => 'Erreur lors de la suppression'];
        }
    }
}
