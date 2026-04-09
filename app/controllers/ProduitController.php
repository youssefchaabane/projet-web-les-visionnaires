<?php
require_once __DIR__ . '/../models/Produit.php';
require_once __DIR__ . '/../models/Categorie.php';

/**
 * Contrôleur Produit - Gère les actions CRUD et la logique métier
 */
class ProduitController {
    private $produit;
    private $categorie;

    public function __construct() {
        $this->produit = new Produit();
        $this->categorie = new Categorie();
    }

    /**
     * Affiche la liste des produits
     */
    public function afficherListeAdmin() {
        $page = isset($_REQUEST['page']) ? max(1, intval($_REQUEST['page'])) : 1;
        $limite = 10;
        $offset = ($page - 1) * $limite;

        $produits = $this->produit->obtenirTous($limite, $offset);
        $total = $this->produit->obtenirNombreTotalProduits();
        $nombre_pages = ceil($total / $limite);

        return [
            'produits' => $produits,
            'page' => $page,
            'nombre_pages' => $nombre_pages,
            'total' => $total
        ];
    }

    /**
     * Affiche le formulaire d'ajout de produit
     */
    public function afficherFormulaireAjout() {
        $categories = $this->categorie->obtenirTous(100, 0);
        return [
            'titre' => 'Ajouter un produit',
            'categories' => $categories
        ];
    }

    /**
     * Crée un nouveau produit
     */
    public function creerProduit($donnees) {
        $erreurs = [];

        try {
            $this->produit->setNomProd($donnees['nom_prod'] ?? '');
            $this->produit->setDateExpiration($donnees['date_expiration'] ?? '');
            $this->produit->setPoidsProduit($donnees['poids_produit'] ?? 0);
            $this->produit->setQuantiteDispo($donnees['quantite_dispo'] ?? 0);
            $this->produit->setIdCat($donnees['id_cat'] ?? 0);

            if ($this->produit->creer()) {
                return ['succes' => true, 'message' => 'Produit créé avec succès'];
            } else {
                return ['succes' => false, 'erreurs' => ['Erreur lors de la création']];
            }
        } catch (Exception $e) {
            return ['succes' => false, 'erreurs' => [$e->getMessage()]];
        }
    }

    /**
     * Affiche le formulaire d'édition de produit
     */
    public function afficherFormulaireEdition($id) {
        $produit_data = $this->produit->obtenirParId($id);
        if (!$produit_data) {
            return ['erreur' => 'Produit non trouvé'];
        }
        $categories = $this->categorie->obtenirTous(100, 0);
        return [
            'produit' => $produit_data,
            'categories' => $categories
        ];
    }

    /**
     * Met à jour un produit
     */
    public function mettreAJourProduit($id, $donnees) {
        $erreurs = [];

        try {
            $this->produit->setNomProd($donnees['nom_prod'] ?? '');
            $this->produit->setDateExpiration($donnees['date_expiration'] ?? '');
            $this->produit->setPoidsProduit($donnees['poids_produit'] ?? 0);
            $this->produit->setQuantiteDispo($donnees['quantite_dispo'] ?? 0);
            $this->produit->setIdCat($donnees['id_cat'] ?? 0);

            if ($this->produit->mettreAJour($id)) {
                return ['succes' => true, 'message' => 'Produit modifié avec succès'];
            } else {
                return ['succes' => false, 'erreurs' => ['Erreur lors de la modification']];
            }
        } catch (Exception $e) {
            return ['succes' => false, 'erreurs' => [$e->getMessage()]];
        }
    }

    /**
     * Supprime un produit
     */
    public function supprimerProduit($id) {
        if ($this->produit->supprimer($id)) {
            return ['succes' => true, 'message' => 'Produit supprimé avec succès'];
        } else {
            return ['succes' => false, 'erreur' => 'Erreur lors de la suppression'];
        }
    }
}
