<?php
require_once __DIR__ . '/../../config/Database.php';

/**
 * Classe Allergie - Modèle pour gérer les allergies
 * Respecte le pattern MVC et utilise PDO pour la persistance
 */
class Allergie {
    private $db;
    private $pdo;
    private $id_allergie;
    private $nom;
    private $description;
    private $niveau_danger;
    private $symptomes;
    private $type;
    private $erreurs = [];

    // Constantes de validation
    const NIVEAUX_DANGER = ['faible', 'moyen', 'élevé', 'critique'];
    const TYPES_ALLERGIE = ['alimentaire', 'médicament', 'environnemental', 'contact', 'autre'];
    const NOM_MIN_LENGTH = 3;
    const NOM_MAX_LENGTH = 100;

    public function __construct() {
        $this->db = Database::getInstance();
        $this->pdo = $this->db->getConnection();
    }

    // Getters
    public function getId() { return $this->id_allergie; }
    public function getNom() { return $this->nom; }
    public function getDescription() { return $this->description; }
    public function getNiveauDanger() { return $this->niveau_danger; }
    public function getSymptomes() { return $this->symptomes; }
    public function getType() { return $this->type; }
    public function getErreurs() { return $this->erreurs; }

    // Setters
    public function setNom($nom) { $this->nom = htmlspecialchars(trim($nom)); }
    public function setDescription($description) { $this->description = htmlspecialchars(trim($description)); }
    public function setNiveauDanger($niveau) { $this->niveau_danger = htmlspecialchars(trim($niveau)); }
    public function setSymptomes($symptomes) { $this->symptomes = htmlspecialchars(trim($symptomes)); }
    public function setType($type) { $this->type = htmlspecialchars(trim($type)); }

    /**
     * Valide les données de l'allergie (validation côté serveur, pas HTML5)
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
        } elseif (!preg_match('/^[a-zA-Z\s\-àâäéèêëïîôöùûüçœæ]+$/u', $this->nom)) {
            $this->erreurs['nom'] = "Le nom ne doit contenir que des lettres";
        }

        // Validation de la description
        if (empty($this->description)) {
            $this->erreurs['description'] = "La description est requise";
        } elseif (strlen($this->description) < 5) {
            $this->erreurs['description'] = "La description doit contenir au moins 5 caractères";
        }

        // Validation du niveau de danger
        if (empty($this->niveau_danger)) {
            $this->erreurs['niveau_danger'] = "Le niveau de danger est requis";
        } elseif (!in_array($this->niveau_danger, self::NIVEAUX_DANGER)) {
            $this->erreurs['niveau_danger'] = "Le niveau de danger est invalide";
        }

        // Validation des symptômes
        if (empty($this->symptomes)) {
            $this->erreurs['symptomes'] = "Les symptômes sont requis";
        } elseif (strlen($this->symptomes) < 5) {
            $this->erreurs['symptomes'] = "Les symptômes doivent contenir au moins 5 caractères";
        }

        // Validation du type
        if (empty($this->type)) {
            $this->erreurs['type'] = "Le type d'allergie est requis";
        } elseif (!in_array($this->type, self::TYPES_ALLERGIE)) {
            $this->erreurs['type'] = "Le type d'allergie est invalide";
        }

        return empty($this->erreurs);
    }

    /**
     * Crée une nouvelle allergie dans la base de données
     */
    public function creer() {
        if (!$this->valider()) {
            return false;
        }

        try {
            // Vérifier que l'allergie n'existe pas déjà
            $sql = "SELECT id_allergie FROM allergie WHERE nom = :nom";
            $stmt = $this->pdo->prepare($sql);
            $stmt->bindParam(':nom', $this->nom);
            $stmt->execute();

            if ($stmt->rowCount() > 0) {
                $this->erreurs['nom'] = "Une allergie avec ce nom existe déjà";
                return false;
            }

            // Insérer la nouvelle allergie
            $sql = "INSERT INTO allergie (nom, description, niveau_danger, symptomes, type) 
                    VALUES (:nom, :description, :niveau_danger, :symptomes, :type)";
            $stmt = $this->pdo->prepare($sql);

            $stmt->bindParam(':nom', $this->nom);
            $stmt->bindParam(':description', $this->description);
            $stmt->bindParam(':niveau_danger', $this->niveau_danger);
            $stmt->bindParam(':symptomes', $this->symptomes);
            $stmt->bindParam(':type', $this->type);

            if ($stmt->execute()) {
                $this->id_allergie = $this->pdo->lastInsertId();
                return true;
            }
            return false;
        } catch (PDOException $e) {
            $this->erreurs['db'] = "Erreur de base de données : " . $e->getMessage();
            return false;
        }
    }

    /**
     * Récupère une allergie par son ID
     */
    public function obtenirParId($id) {
        try {
            $sql = "SELECT * FROM allergie WHERE id_allergie = :id";
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
     * Récupère toutes les allergies
     */
    public function obtenirTous($limite = 100, $offset = 0) {
        try {
            $sql = "SELECT * FROM allergie ORDER BY date_modification DESC LIMIT :limite OFFSET :offset";
            $stmt = $this->pdo->prepare($sql);
            $stmt->bindParam(':limite', $limite, PDO::PARAM_INT);
            $stmt->bindParam(':offset', $offset, PDO::PARAM_INT);
            $stmt->execute();

            $allergies = [];
            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                $allergies[] = $row;
            }
            return $allergies;
        } catch (PDOException $e) {
            $this->erreurs['db'] = "Erreur de base de données : " . $e->getMessage();
            return [];
        }
    }

    /**
     * Récupère le nombre total d'allergies
     */
    public function obtenirNombreTotalallergies() {
        try {
            $sql = "SELECT COUNT(*) as total FROM allergie";
            $stmt = $this->pdo->query($sql);
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            return $result['total'];
        } catch (PDOException $e) {
            return 0;
        }
    }

    /**
     * Recherche des allergies par nom ou type
     */
    public function rechercher($terme) {
        try {
            $terme = "%{$terme}%";
            $sql = "SELECT * FROM allergie 
                    WHERE nom LIKE :terme OR type LIKE :terme OR symptomes LIKE :terme 
                    ORDER BY nom ASC";
            $stmt = $this->pdo->prepare($sql);
            $stmt->bindParam(':terme', $terme);
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

    /**
     * Met à jour une allergie existante
     */
    public function mettrAJour() {
        if (empty($this->id_allergie)) {
            $this->erreurs['id'] = "L'ID de l'allergie est requis";
            return false;
        }

        if (!$this->valider()) {
            return false;
        }

        try {
            // Vérifier que le nom n'est pas utilisé par une autre allergie
            $sql = "SELECT id_allergie FROM allergie WHERE nom = :nom AND id_allergie != :id";
            $stmt = $this->pdo->prepare($sql);
            $stmt->bindParam(':nom', $this->nom);
            $stmt->bindParam(':id', $this->id_allergie, PDO::PARAM_INT);
            $stmt->execute();

            if ($stmt->rowCount() > 0) {
                $this->erreurs['nom'] = "Une allergie avec ce nom existe déjà";
                return false;
            }

            // Mettre à jour l'allergie
            $sql = "UPDATE allergie 
                    SET nom = :nom, description = :description, 
                        niveau_danger = :niveau_danger, symptomes = :symptomes, type = :type
                    WHERE id_allergie = :id";
            $stmt = $this->pdo->prepare($sql);

            $stmt->bindParam(':nom', $this->nom);
            $stmt->bindParam(':description', $this->description);
            $stmt->bindParam(':niveau_danger', $this->niveau_danger);
            $stmt->bindParam(':symptomes', $this->symptomes);
            $stmt->bindParam(':type', $this->type);
            $stmt->bindParam(':id', $this->id_allergie, PDO::PARAM_INT);

            return $stmt->execute();
        } catch (PDOException $e) {
            $this->erreurs['db'] = "Erreur de base de données : " . $e->getMessage();
            return false;
        }
    }

    /**
     * Supprime une allergie
     */
    public function supprimer() {
        if (empty($this->id_allergie)) {
            $this->erreurs['id'] = "L'ID de l'allergie est requis";
            return false;
        }

        try {
            $sql = "DELETE FROM allergie WHERE id_allergie = :id";
            $stmt = $this->pdo->prepare($sql);
            $stmt->bindParam(':id', $this->id_allergie, PDO::PARAM_INT);
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
        $this->id_allergie = $data['id_allergie'];
        $this->nom = $data['nom'];
        $this->description = $data['description'];
        $this->niveau_danger = $data['niveau_danger'];
        $this->symptomes = $data['symptomes'];
        $this->type = $data['type'];
    }

    /**
     * Récupère les allergies par niveau de danger
     */
    public function obtenirParNiveauDanger($niveau) {
        try {
            $sql = "SELECT * FROM allergie WHERE niveau_danger = :niveau ORDER BY nom ASC";
            $stmt = $this->pdo->prepare($sql);
            $stmt->bindParam(':niveau', $niveau);
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

    /**
     * Récupère les allergies par type
     */
    public function obtenirParType($type) {
        try {
            $sql = "SELECT * FROM allergie WHERE type = :type ORDER BY nom ASC";
            $stmt = $this->pdo->prepare($sql);
            $stmt->bindParam(':type', $type);
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
