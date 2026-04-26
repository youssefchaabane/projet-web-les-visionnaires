<?php
require_once __DIR__ . '/../models/allergie.php';
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/traitementcontroller.php';

class AllergieController {

    private $pdo;

    /** @var self|null */
    private static $instance = null;

    public function __construct() {
        $this->pdo = config::getConnexion();
    }

    public static function getInstance(): self
    {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    // 🔹 Liste
    public function afficherListeAdmin() {
        $stmt = $this->pdo->prepare("SELECT * FROM allergie");
        $stmt->execute();
        return $stmt->fetchAll();
    }

    // 🔹 Ajouter
    public function creerAllergie($donnees) {

        $stmt = $this->pdo->prepare("INSERT INTO allergie (nom, description, niveau_danger, symptomes, type) 
                             VALUES (?, ?, ?, ?, ?)");

        $stmt->execute([
            $donnees['nom'],
            $donnees['description'],
            $donnees['niveau_danger'],
            $donnees['symptomes'],
            $donnees['type']
        ]);

        return true;
    }

    // 🔹 Supprimer
    public function supprimerAllergie($id) {

        $stmt = $this->pdo->prepare("DELETE FROM allergie WHERE id_allergie = ?");
        $stmt->execute([$id]);
    }

    // 🔹 Récupérer une allergie
    public function recupererAllergie($id) {

        $stmt = $this->pdo->prepare("SELECT * FROM allergie WHERE id_allergie = ?");
        $stmt->execute([$id]);

        return $stmt->fetch();
    }

    // 🔹 Modifier
    public function modifierAllergie($id, $donnees) {

        $stmt = $this->pdo->prepare("UPDATE allergie 
                             SET nom=?, description=?, niveau_danger=?, symptomes=?, type=? 
                             WHERE id_allergie=?");

        $stmt->execute([
            $donnees['nom'],
            $donnees['description'],
            $donnees['niveau_danger'],
            $donnees['symptomes'],
            $donnees['type'],
            $id
        ]);
    }

    /**
     * Jointure (atelier) : toutes les lignes allergie_traitement avec INNER JOIN allergie + traitement.
     */
    public function afficherAssociationsJoinToutes(): array
    {
        $stmt = $this->pdo->prepare("
            SELECT at.id_allergie, at.id_traitement,
                   a.nom AS allergie_nom, t.nom AS traitement_nom, t.type_traitement
            FROM allergie_traitement at
            INNER JOIN allergie a ON a.id_allergie = at.id_allergie
            INNER JOIN traitement t ON t.id_traitement = at.id_traitement
            ORDER BY a.nom ASC, t.nom ASC
        ");
        $stmt->execute();
        return $stmt->fetchAll();
    }

    /**
     * Jointure filtrée par allergie (équivalent atelier : afficherAlbums($idGenre)).
     */
    public function afficherAssociationsParAllergie(int $idAllergie): array
    {
        if ($idAllergie <= 0) {
            return [];
        }
        $stmt = $this->pdo->prepare("
            SELECT at.id_allergie, at.id_traitement,
                   a.nom AS allergie_nom, t.nom AS traitement_nom, t.type_traitement
            FROM allergie_traitement at
            INNER JOIN allergie a ON a.id_allergie = at.id_allergie
            INNER JOIN traitement t ON t.id_traitement = at.id_traitement
            WHERE at.id_allergie = ?
            ORDER BY t.nom ASC
        ");
        $stmt->execute([$idAllergie]);
        return $stmt->fetchAll();
    }

    public function supprimerLiensAllergie(int $idAllergie): void
    {
        $stmt = $this->pdo->prepare('DELETE FROM allergie_traitement WHERE id_allergie = ?');
        $stmt->execute([$idAllergie]);
    }

    public function supprimerAssociation(int $idAllergie, int $idTraitement): void
    {
        $stmt = $this->pdo->prepare(
            'DELETE FROM allergie_traitement WHERE id_allergie = ? AND id_traitement = ?'
        );
        $stmt->execute([$idAllergie, $idTraitement]);
    }

    public function ajouterAssociation(int $idAllergie, int $idTraitement): void
    {
        $stmt = $this->pdo->prepare(
            'INSERT INTO allergie_traitement (id_allergie, id_traitement) VALUES (?, ?)'
        );
        $stmt->execute([$idAllergie, $idTraitement]);
    }

    public function compterAssociations(): int
    {
        $row = $this->pdo->query('SELECT COUNT(*) AS c FROM allergie_traitement')->fetch();
        return (int) ($row['c'] ?? 0);
    }

    /**
     * Traite GET/POST pour la page associations (liste + suppression).
     *
     * @return array{message:string,erreurs:array,rows:array,idAllergieFiltre:int,idTraitementFiltre:int,listeAllergies:array,listeTraitements:array}
     */
    public function traiterRequetePageAssociations(TraitementController $tc): array
    {
        $message = isset($_GET['message']) ? (string) $_GET['message'] : '';
        $erreurs = [];

        if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_GET['action'] ?? '') === 'supprimer') {
            $ida = (int) ($_POST['id_allergie'] ?? 0);
            $idt = (int) ($_POST['id_traitement'] ?? 0);
            try {
                if ($ida <= 0 || $idt <= 0) {
                    throw new RuntimeException('Identifiant invalide.');
                }
                $this->supprimerAssociation($ida, $idt);
                header('Location: associations.php?message=' . rawurlencode('Association supprimée.'));
                exit;
            } catch (Throwable $e) {
                $erreurs[] = $e->getMessage() ?: 'Erreur lors de la suppression.';
            }
        }

        $idAllergieFiltre = isset($_GET['id_allergie']) ? (int) $_GET['id_allergie'] : 0;
        $idTraitementFiltre = isset($_GET['id_traitement']) ? (int) $_GET['id_traitement'] : 0;

        if ($idAllergieFiltre > 0) {
            $rows = $this->afficherAssociationsParAllergie($idAllergieFiltre);
        } elseif ($idTraitementFiltre > 0) {
            $rows = $tc->afficherAssociationsParTraitement($idTraitementFiltre);
        } else {
            $rows = $this->afficherAssociationsJoinToutes();
        }

        return [
            'message' => $message,
            'erreurs' => $erreurs,
            'rows' => $rows,
            'idAllergieFiltre' => $idAllergieFiltre,
            'idTraitementFiltre' => $idTraitementFiltre,
            'listeAllergies' => $this->afficherListeAdmin(),
            'listeTraitements' => $tc->afficherListeAdmin(),
        ];
    }

    /**
     * Traite GET/POST pour le formulaire nouvelle association.
     *
     * @return array{erreurs:array,message:string,ancien:array,allergies:array,traitements:array}
     */
    public function traiterRequetePageAssociationForm(TraitementController $tc): array
    {
        $erreurs = [];
        $message = isset($_GET['message']) ? (string) $_GET['message'] : '';
        $ancien = [];

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $ancien = $_POST;
            $ida = (int) ($_POST['id_allergie'] ?? 0);
            $idt = (int) ($_POST['id_traitement'] ?? 0);
            if ($ida <= 0 || $idt <= 0) {
                $erreurs[] = 'Choisissez une allergie et un traitement.';
            } else {
                try {
                    $this->ajouterAssociation($ida, $idt);
                    header('Location: associations.php?message=' . rawurlencode('Association créée.'));
                    exit;
                } catch (Throwable $e) {
                    $erreurs[] = 'Impossible de créer (doublon ou erreur base) : ' . ($e->getMessage() ?: '');
                }
            }
        }

        return [
            'erreurs' => $erreurs,
            'message' => $message,
            'ancien' => $ancien,
            'allergies' => $this->afficherListeAdmin(),
            'traitements' => $tc->afficherListeAdmin(),
        ];
    }

    /**
     * Traite GET/POST pour la liste allergies (suppression).
     *
     * @return array{message:string,erreurs:array,rows:array}
     */
    public function traiterRequetePageAllergies(): array
    {
        $message = isset($_GET['message']) ? (string) $_GET['message'] : '';
        $erreurs = [];

        if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_GET['action'] ?? '') === 'supprimer') {
            $id = (int) ($_POST['id_allergie'] ?? 0);
            if ($id <= 0) {
                $erreurs[] = 'Identifiant invalide.';
            } else {
                try {
                    $this->supprimerLiensAllergie($id);
                    $this->supprimerAllergie($id);
                    header('Location: allergies.php?message=' . rawurlencode('Allergie supprimée.'));
                    exit;
                } catch (Throwable $e) {
                    $erreurs[] = $e->getMessage() ?: 'Erreur lors de la suppression.';
                }
            }
        }

        return [
            'message' => $message,
            'erreurs' => $erreurs,
            'rows' => $this->afficherListeAdmin(),
        ];
    }

    /**
     * Traite GET/POST pour allergie_form (création / édition).
     *
     * @return array{mode:string,row:?array,ancien:array,erreurs:array,message:string}
     */
    public function traiterRequetePageAllergieForm(): array
    {
        $action = $_GET['action'] ?? '';
        $message = isset($_GET['message']) ? (string) $_GET['message'] : '';
        $erreurs = [];
        $ancien = [];
        $row = null;
        $actionPost = '';

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $actionPost = $_GET['action'] ?? '';
            $ancien = $_POST;
            try {
                if ($actionPost === 'creer') {
                    $this->creerAllergie($_POST);
                    header('Location: allergies.php?message=' . rawurlencode('Allergie ajoutée.'));
                    exit;
                }
                if ($actionPost === 'modifier') {
                    $id = (int) ($_POST['id_allergie'] ?? 0);
                    $this->modifierAllergie($id, $_POST);
                    header('Location: allergies.php?message=' . rawurlencode('Allergie modifiée.'));
                    exit;
                }
            } catch (Throwable $e) {
                $erreurs[] = $e->getMessage() ?: 'Erreur.';
                if ($actionPost === 'modifier') {
                    $action = 'editer';
                    $id = (int) ($_POST['id_allergie'] ?? 0);
                    $row = $id > 0 ? $this->recupererAllergie($id) : null;
                } else {
                    $action = 'ajouter';
                }
            }
        }

        if ($action === 'ajouter') {
            $mode = 'ajouter';
            $row = null;
        } elseif ($action === 'editer') {
            $mode = 'editer';
            if ($row === null) {
                $id = (int) ($_GET['id'] ?? 0);
                $row = $id > 0 ? $this->recupererAllergie($id) : null;
            }
            if (!$row) {
                header('Location: allergies.php?message=' . rawurlencode('Allergie introuvable.'));
                exit;
            }
        } else {
            header('Location: allergies.php');
            exit;
        }

        return [
            'mode' => $mode,
            'row' => $row,
            'ancien' => $ancien,
            'erreurs' => $erreurs,
            'message' => $message,
        ];
    }
}
?>