<?php
require_once __DIR__ . '/../models/Allergie.php';

/**
 * Contrôleur Allergie - Gère les actions CRUD et la logique métier
 * Respecte le pattern MVC
 */
class AllergiController {
    private $allergie;

    public function __construct() {
        $this->allergie = new Allergie();
    }

    /**
     * Affiche la liste des allergies (BackOffice)
     */
    public function afficherListeAdmin() {
        $page = isset($_REQUEST['page']) ? max(1, intval($_REQUEST['page'])) : 1;
        $limite = 10;
        $offset = ($page - 1) * $limite;

        $allergies = $this->allergie->obtenirTous($limite, $offset);
        $total = $this->allergie->obtenirNombreTotalallergies();
        $nombre_pages = ceil($total / $limite);

        return [
            'allergies' => $allergies,
            'page' => $page,
            'nombre_pages' => $nombre_pages,
            'total' => $total
        ];
    }

    /**
     * Affiche le formulaire d'ajout d'allergie (BackOffice)
     */
    public function afficherFormulaireAjout() {
        return [
            'titre' => 'Ajouter une allergie',
            'niveaux_danger' => Allergie::NIVEAUX_DANGER,
            'types' => Allergie::TYPES_ALLERGIE
        ];
    }

    /**
     * Crée une nouvelle allergie
     */
    public function creerAllergie($donnees) {
        $this->allergie->setNom($donnees['nom'] ?? '');
        $this->allergie->setDescription($donnees['description'] ?? '');
        $this->allergie->setNiveauDanger($donnees['niveau_danger'] ?? '');
        $this->allergie->setSymptomes($donnees['symptomes'] ?? '');
        $this->allergie->setType($donnees['type'] ?? '');

        if ($this->allergie->creer()) {
            return [
                'succes' => true,
                'message' => 'Allergie créée avec succès',
                'id' => $this->allergie->getId()
            ];
        } else {
            return [
                'succes' => false,
                'erreurs' => $this->allergie->getErreurs()
            ];
        }
    }

    /**
     * Affiche le formulaire d'édition d'allergie
     */
    public function afficherFormulaireEdition($id) {
        if (!$this->allergie->obtenirParId($id)) {
            return [
                'erreur' => 'Allergie non trouvée',
                'id' => $id
            ];
        }

        return [
            'titre' => 'Éditer une allergie',
            'allergie' => [
                'id_allergie' => $this->allergie->getId(),
                'nom' => $this->allergie->getNom(),
                'description' => $this->allergie->getDescription(),
                'niveau_danger' => $this->allergie->getNiveauDanger(),
                'symptomes' => $this->allergie->getSymptomes(),
                'type' => $this->allergie->getType()
            ],
            'niveaux_danger' => Allergie::NIVEAUX_DANGER,
            'types' => Allergie::TYPES_ALLERGIE
        ];
    }

    /**
     * Met à jour une allergie
     */
    public function mettreAJourAllergie($id, $donnees) {
        if (!$this->allergie->obtenirParId($id)) {
            return [
                'succes' => false,
                'erreur' => 'Allergie non trouvée'
            ];
        }

        $this->allergie->setNom($donnees['nom'] ?? '');
        $this->allergie->setDescription($donnees['description'] ?? '');
        $this->allergie->setNiveauDanger($donnees['niveau_danger'] ?? '');
        $this->allergie->setSymptomes($donnees['symptomes'] ?? '');
        $this->allergie->setType($donnees['type'] ?? '');

        if ($this->allergie->mettrAJour()) {
            return [
                'succes' => true,
                'message' => 'Allergie mise à jour avec succès'
            ];
        } else {
            return [
                'succes' => false,
                'erreurs' => $this->allergie->getErreurs()
            ];
        }
    }

    /**
     * Supprime une allergie
     */
    public function supprimerAllergie($id) {
        if (!$this->allergie->obtenirParId($id)) {
            return [
                'succes' => false,
                'erreur' => 'Allergie non trouvée'
            ];
        }

        if ($this->allergie->supprimer()) {
            return [
                'succes' => true,
                'message' => 'Allergie supprimée avec succès'
            ];
        } else {
            return [
                'succes' => false,
                'erreurs' => $this->allergie->getErreurs()
            ];
        }
    }

    /**
     * Affiche le détail d'une allergie (FrontOffice)
     */
    public function afficherDetailAllergie($id) {
        if (!$this->allergie->obtenirParId($id)) {
            return [
                'erreur' => 'Allergie non trouvée',
                'id' => $id
            ];
        }

        return [
            'allergie' => [
                'id_allergie' => $this->allergie->getId(),
                'nom' => $this->allergie->getNom(),
                'description' => $this->allergie->getDescription(),
                'niveau_danger' => $this->allergie->getNiveauDanger(),
                'symptomes' => $this->allergie->getSymptomes(),
                'type' => $this->allergie->getType()
            ]
        ];
    }

    /**
     * Affiche la liste des allergies fil public (FrontOffice)
     */
    public function afficherListePublique($page = 1) {
        $page = max(1, intval($page));
        $limite = 12;
        $offset = ($page - 1) * $limite;

        $allergies = $this->allergie->obtenirTous($limite, $offset);
        $total = $this->allergie->obtenirNombreTotalallergies();
        $nombre_pages = ceil($total / $limite);

        // Grouper par niveau de danger pour le FrontOffice
        $allergie_par_niveau = [];
        foreach (Allergie::NIVEAUX_DANGER as $niveau) {
            $allergie_par_niveau[$niveau] = $this->allergie->obtenirParNiveauDanger($niveau);
        }

        return [
            'allergies' => $allergies,
            'allergies_par_niveau' => $allergie_par_niveau,
            'page' => $page,
            'nombre_pages' => $nombre_pages,
            'total' => $total
        ];
    }

    /**
     * Recherche des allergies
     */
    public function rechercherAllergies($terme) {
        $allergies = $this->allergie->rechercher($terme);
        return [
            'allergies' => $allergies,
            'terme' => htmlspecialchars($terme),
            'nombre_resultats' => count($allergies)
        ];
    }
}
?>
