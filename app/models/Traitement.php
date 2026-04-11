<?php
require_once __DIR__ . '/../../config/config.php';

/**
 * Classe Traitement - Modèle pour gérer les traitements
 * Respecte le pattern MVC et utilise PDO pour la persistance
 */
class Traitement {
    private $db;
    private $pdo;
    private $id_traitement;
    private $nom;
    private $type_traitement;
    private $dosage;
    private $duree;
    private $effets_secondaires;
    private $erreurs = [];

    // Constantes de validation
    const TYPES_TRAITEMENT = ['antihistaminique', 'anti-inflammatoire', 'corticoïde', 'bronchodilatateur', 'urgence', 'autre'];
    const NOM_MIN_LENGTH = 3;
    const NOM_MAX_LENGTH = 100;

    public function __construct() {
        $this->pdo = config::getConnexion();
    }

    // Getters
    public function getId() { return $this->id_traitement; }
    public function getNom() { return $this->nom; }
    public function getType() { return $this->type_traitement; }
    public function getTypeTraitement() { return $this->type_traitement; }
    public function getPosologie() { return $this->dosage; }
    public function getDosage() { return $this->dosage; }
    public function getDescription() { return $this->effets_secondaires; }
    public function getDuree() { return $this->duree; }
    public function getEffetsSecondaires() { return $this->effets_secondaires; }
    public function getCasUrgence() { return 0; }
    public function getErreurs() { return $this->erreurs; }

    // Setters
    public function setNom($nom) { $this->nom = htmlspecialchars(trim($nom)); }
    public function setType($type) { $this->type_traitement = htmlspecialchars(trim($type)); }
    public function setTypeTraitement($type) { $this->type_traitement = htmlspecialchars(trim($type)); }
    public function setPosologie($posologie) { $this->dosage = htmlspecialchars(trim($posologie)); }
    public function setDosage($dosage) { $this->dosage = htmlspecialchars(trim($dosage)); }
    public function setDescription($description) { $this->effets_secondaires = htmlspecialchars(trim($description)); }
    public function setDuree($duree) { $this->duree = htmlspecialchars(trim($duree)); }
    public function setEffetsSecondaires($effets) { $this->effets_secondaires = htmlspecialchars(trim($effets)); }
    public function setCasUrgence($cas_urgence) { }

    /**
     * Valide les données du traitement (validation côté serveur, pas HTML5)
     */
    public function valider() {
        $this->erreurs = [];

        // Validation du nom
        if (empty($this->nom)) {
            $this->erreurs['nom'] = "Le nom est requis";
        } elseif (strlen($this->nom) < self::NOM_MIN_LENGTH) {
            $this->erreurs['nom'] = "Le nom doit contenir au moins " . self::NOM_MIN_LENGTH . " caractères";
        } elseif (strlen($this->nom) > self::NOM_MAX_LENGTH) {
            $this->erreurs['nom'] = "Le nom ne doit pas dépasser " . self::NOM_MAX_LENGTH . " caractères";
        }

        // Validation du type de traitement
        if (empty($this->type_traitement)) {
            $this->erreurs['type_traitement'] = "Le type de traitement est requis";
        } elseif (!in_array($this->type_traitement, self::TYPES_TRAITEMENT)) {
            $this->erreurs['type_traitement'] = "Le type de traitement est invalide";
        }

        // Validation du dosage
        if (empty($this->dosage)) {
            $this->erreurs['dosage'] = "Le dosage est requis";
        } elseif (strlen($this->dosage) < 3) {
            $this->erreurs['dosage'] = "Le dosage doit contenir au moins 3 caractères";
        }

        // Validation de la durée
        if (empty($this->duree)) {
            $this->erreurs['duree'] = "La durée est requise";
        } elseif (strlen($this->duree) < 3) {
            $this->erreurs['duree'] = "La durée doit contenir au moins 3 caractères";
        }

        // Validation des effets secondaires
        if (empty($this->effets_secondaires)) {
            $this->erreurs['effets_secondaires'] = "Les effets secondaires sont requis";
        } elseif (strlen($this->effets_secondaires) < 5) {
            $this->erreurs['effets_secondaires'] = "Les effets secondaires doivent contenir au moins 5 caractères";
        }

        return empty($this->erreurs);
    }

    /**
     * Crée un nouveau traitement dans la base de données
     */
    public function creer() {
        if (!$this->valider()) {
            return false;
        }

        try {
            // Insérer le nouveau traitement
            $sql = "INSERT INTO traitement (nom, type_traitement, dosage, duree, effets_secondaires) 
                    VALUES (:nom, :type_traitement, :dosage, :duree, :effets_secondaires)";
            $stmt = $this->pdo->prepare($sql);

            $stmt->bindParam(':nom', $this->nom);
            $stmt->bindParam(':type_traitement', $this->type_traitement);
            $stmt->bindParam(':dosage', $this->dosage);
            $stmt->bindParam(':duree', $this->duree);
            $stmt->bindParam(':effets_secondaires', $this->effets_secondaires);

            if ($stmt->execute()) {
                $this->id_traitement = $this->pdo->lastInsertId();
                return true;
            }
            return false;
        } catch (PDOException $e) {
            $this->erreurs['db'] = "Erreur de base de données : " . $e->getMessage();
            return false;
        }
    }

    /**
     * Récupère un traitement par son ID
     */
    public function obtenirParId($id) {
        try {
            $sql = "SELECT * FROM traitement WHERE id_traitement = :id";
            $stmt = $this->pdo->prepare($sql);
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);
            $stmt->execute();

            if ($stmt->rowCount() > 0) {
                $data = $stmt->fetch(PDO::FETCH_ASSOC);
                $this->chargerDonnees($data);
                return true;
            }
            return false;
        } catch (PDOException $e) {
            $this->erreurs['db'] = "Erreur de base de données : " . $e->getMessage();
            return false;
        }
    }

    /**
     * Récupère tous les traitements
     */
    public function obtenirTous($limite = 100, $offset = 0) {
        try {
            $sql = "SELECT * FROM traitement ORDER BY date_modification DESC LIMIT :limite OFFSET :offset";
            $stmt = $this->pdo->prepare($sql);
            $stmt->bindParam(':limite', $limite, PDO::PARAM_INT);
            $stmt->bindParam(':offset', $offset, PDO::PARAM_INT);
            $stmt->execute();

            $traitements = [];
            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                $traitements[] = $row;
            }
            return $traitements;
        } catch (PDOException $e) {
            $this->erreurs['db'] = "Erreur de base de données : " . $e->getMessage();
            return [];
        }
    }

    /**
     * Récupère le nombre total de traitements
     */
    public function obtenirNombreTotalTraitements() {
        try {
            $sql = "SELECT COUNT(*) as total FROM traitement";
            $stmt = $this->pdo->query($sql);
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            return $result['total'];
        } catch (PDOException $e) {
            return 0;
        }
    }

    /**
     * Recherche des traitements par nom ou type
     */
    public function rechercher($terme) {
        try {
            $terme = "%{$terme}%";
            $sql = "SELECT * FROM traitement 
                    WHERE nom LIKE :terme OR type_traitement LIKE :terme 
                    ORDER BY nom ASC";
            $stmt = $this->pdo->prepare($sql);
            $stmt->bindParam(':terme', $terme);
            $stmt->execute();

            $traitements = [];
            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                $traitements[] = $row;
            }
            return $traitements;
        } catch (PDOException $e) {
            return [];
        }
    }

    /**
     * Met à jour un traitement existant
     */
    public function mettrAJour() {
        if (empty($this->id_traitement)) {
            $this->erreurs['id'] = "L'ID du traitement est requis";
            return false;
        }

        if (!$this->valider()) {
            return false;
        }

        try {
            // Mettre à jour le traitement
            $sql = "UPDATE traitement 
                    SET nom = :nom, type_traitement = :type_traitement, 
                        dosage = :dosage, duree = :duree, effets_secondaires = :effets_secondaires
                    WHERE id_traitement = :id";
            $stmt = $this->pdo->prepare($sql);

            $stmt->bindParam(':nom', $this->nom);
            $stmt->bindParam(':type_traitement', $this->type_traitement);
            $stmt->bindParam(':dosage', $this->dosage);
            $stmt->bindParam(':duree', $this->duree);
            $stmt->bindParam(':effets_secondaires', $this->effets_secondaires);
            $stmt->bindParam(':id', $this->id_traitement, PDO::PARAM_INT);

            return $stmt->execute();
        } catch (PDOException $e) {
            $this->erreurs['db'] = "Erreur de base de données : " . $e->getMessage();
            return false;
        }
    }

    /**
     * Supprime un traitement
     */
    public function supprimer() {
        if (empty($this->id_traitement)) {
            $this->erreurs['id'] = "L'ID du traitement est requis";
            return false;
        }

        try {
            $sql = "DELETE FROM traitement WHERE id_traitement = :id";
            $stmt = $this->pdo->prepare($sql);
            $stmt->bindParam(':id', $this->id_traitement, PDO::PARAM_INT);
            return $stmt->execute();
        } catch (PDOException $e) {
            $this->erreurs['db'] = "Erreur de base de données : " . $e->getMessage();
            return false;
        }
    }

    /**
     * Charge les données dans l'objet
     */
    private function chargerDonnees($data) {
        $this->id_traitement = $data['id_traitement'];
        $this->nom = $data['nom'];
        $this->type_traitement = $data['type_traitement'] ?? '';
        $this->dosage = $data['dosage'] ?? '';
        $this->duree = $data['duree'] ?? '';
        $this->effets_secondaires = $data['effets_secondaires'] ?? '';
    }

    /**
     * Récupère les traitements par type
     */
    public function obtenirParType($type) {
        try {
            $sql = "SELECT * FROM traitement WHERE type_traitement = :type ORDER BY nom ASC";
            $stmt = $this->pdo->prepare($sql);
            $stmt->bindParam(':type', $type);
            $stmt->execute();

            $traitements = [];
            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                $traitements[] = $row;
            }
            return $traitements;
        } catch (PDOException $e) {
            return [];
        }
    }

    /**
     * Récupère les allergies traitées par ce traitement
     */
    public function obtenirAllergiesAssociees() {
        try {
            $sql = "SELECT a.* FROM allergie a 
                    INNER JOIN allergie_traitement at ON a.id_allergie = at.id_allergie
                    WHERE at.id_traitement = :id_traitement";
            $stmt = $this->pdo->prepare($sql);
            $stmt->bindParam(':id_traitement', $this->id_traitement, PDO::PARAM_INT);
            $stmt->execute();

            $allergies = [];
            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                $allergies[] = $row;
            }
            return $allergies;
        } catch (PDOException $e) {
            return [];
        }
    }
}
?>



