<?php
require_once __DIR__ . '/../models/Traitement.php';

/**
 * Contrôleur Traitement - Gère les actions CRUD et la logique métier
 * Respecte le pattern MVC
 */
class TraitementController {
    private $traitement;

    public function __construct() {
        $this->traitement = new Traitement();
    }

    /**
     * Affiche la liste des traitements (BackOffice)
     */
    public function afficherListeAdmin() {
        $page = isset($_REQUEST['page']) ? max(1, intval($_REQUEST['page'])) : 1;
        $limite = 10;
        $offset = ($page - 1) * $limite;

        $traitements = $this->traitement->obtenirTous($limite, $offset);
        $total = $this->traitement->obtenirNombreTotalTraitements();
        $nombre_pages = ceil($total / $limite);

        return [
            'traitements' => $traitements,
            'page' => $page,
            'nombre_pages' => $nombre_pages,
            'total' => $total
        ];
    }

    /**
     * Affiche le formulaire d'ajout de traitement (BackOffice)
     */
    public function afficherFormulaireAjout() {
        return [
            'titre' => 'Ajouter un traitement',
            'types' => Traitement::TYPES_TRAITEMENT
        ];
    }

    /**
     * Crée un nouveau traitement
     */
    public function creerTraitement($donnees) {
        $this->traitement->setNom($donnees['nom'] ?? '');
        $this->traitement->setTypeTraitement($donnees['type_traitement'] ?? $donnees['type'] ?? '');
        $this->traitement->setDosage($donnees['dosage'] ?? $donnees['posologie'] ?? '');
        $this->traitement->setDuree($donnees['duree'] ?? '');
        $this->traitement->setEffetsSecondaires($donnees['effets_secondaires'] ?? $donnees['description'] ?? '');

        if ($this->traitement->creer()) {
            return [
                'succes' => true,
                'message' => 'Traitement créé avec succès',
                'id' => $this->traitement->getId()
            ];
        } else {
            return [
                'succes' => false,
                'erreurs' => $this->traitement->getErreurs()
            ];
        }
    }

    /**
     * Affiche le formulaire d'édition de traitement
     */
    public function afficherFormulaireEdition($id) {
        if (!$this->traitement->obtenirParId($id)) {
            return [
                'erreur' => 'Traitement non trouvé',
                'id' => $id
            ];
        }

        return [
            'titre' => 'Éditer un traitement',
            'traitement' => [
                'id_traitement' => $this->traitement->getId(),
                'nom' => $this->traitement->getNom(),
                'type_traitement' => $this->traitement->getTypeTraitement(),
                'dosage' => $this->traitement->getDosage(),
                'duree' => $this->traitement->getDuree(),
                'effets_secondaires' => $this->traitement->getEffetsSecondaires()
            ],
            'types' => Traitement::TYPES_TRAITEMENT
        ];
    }

    /**
     * Met à jour un traitement
     */
    public function mettreAJourTraitement($id, $donnees) {
        if (!$this->traitement->obtenirParId($id)) {
            return [
                'succes' => false,
                'erreur' => 'Traitement non trouvé'
            ];
        }

        $this->traitement->setNom($donnees['nom'] ?? '');
        $this->traitement->setTypeTraitement($donnees['type_traitement'] ?? '');
        $this->traitement->setDosage($donnees['dosage'] ?? '');
        $this->traitement->setDuree($donnees['duree'] ?? '');
        $this->traitement->setEffetsSecondaires($donnees['effets_secondaires'] ?? '');

        if ($this->traitement->mettrAJour()) {
            return [
                'succes' => true,
                'message' => 'Traitement mis à jour avec succès'
            ];
        } else {
            return [
                'succes' => false,
                'erreurs' => $this->traitement->getErreurs()
            ];
        }
    }

    /**
     * Supprime un traitement
     */
    public function supprimerTraitement($id) {
        if (!$this->traitement->obtenirParId($id)) {
            return [
                'succes' => false,
                'erreur' => 'Traitement non trouvé'
            ];
        }

        if ($this->traitement->supprimer()) {
            return [
                'succes' => true,
                'message' => 'Traitement supprimé avec succès'
            ];
        } else {
            return [
                'succes' => false,
                'erreurs' => $this->traitement->getErreurs()
            ];
        }
    }

    /**
     * Affiche le détail d'un traitement avec ses allergies associées
     */
    public function afficherDetailTraitement($id) {
        if (!$this->traitement->obtenirParId($id)) {
            return [
                'erreur' => 'Traitement non trouvé',
                'id' => $id
            ];
        }

        return [
            'traitement' => [
                'id_traitement' => $this->traitement->getId(),
                'nom' => $this->traitement->getNom(),
                'type_traitement' => $this->traitement->getTypeTraitement(),
                'dosage' => $this->traitement->getDosage(),
                'duree' => $this->traitement->getDuree(),
                'effets_secondaires' => $this->traitement->getEffetsSecondaires()
            ],
            'allergies' => $this->traitement->obtenirAllergiesAssociees()
        ];
    }

    /**
     * Affiche la liste publique des traitements
     */
    public function afficherListePublique($page = 1) {
        $page = max(1, intval($page));
        $limite = 12;
        $offset = ($page - 1) * $limite;

        $traitements = $this->traitement->obtenirTous($limite, $offset);
        $total = $this->traitement->obtenirNombreTotalTraitements();
        $nombre_pages = ceil($total / $limite);

        // Grouper par type
        $traitements_par_type = [];
        foreach (Traitement::TYPES_TRAITEMENT as $type) {
            $traitements_par_type[$type] = $this->traitement->obtenirParType($type);
        }

        return [
            'traitements' => $traitements,
            'traitements_par_type' => $traitements_par_type,
            'page' => $page,
            'nombre_pages' => $nombre_pages,
            'total' => $total
        ];
    }

    /**
     * Recherche des traitements
     */
    public function rechercherTraitements($terme) {
        $traitements = $this->traitement->rechercher($terme);
        return [
            'traitements' => $traitements,
            'terme' => htmlspecialchars($terme),
            'nombre_resultats' => count($traitements)
        ];
    }
}
?>
