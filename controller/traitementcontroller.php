<?php
require_once __DIR__ . '/../models/traitement.php';
require_once __DIR__ . '/../config/config.php';

class TraitementController {

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
        $stmt = $this->pdo->prepare("SELECT * FROM traitement");
        $stmt->execute();
        return $stmt->fetchAll();
    }

    // 🔹 Ajouter
    public function creerTraitement($donnees) {

        $stmt = $this->pdo->prepare("INSERT INTO traitement (nom, type_traitement, dosage, duree, effets_secondaires) 
                             VALUES (?, ?, ?, ?, ?)");

        $stmt->execute([
            $donnees['nom'],
            $donnees['type_traitement'],
            $donnees['dosage'],
            $donnees['duree'],
            $donnees['effets_secondaires']
        ]);

        return true;
    }

    // 🔹 Supprimer
    public function supprimerTraitement($id) {

        $stmt = $this->pdo->prepare("DELETE FROM traitement WHERE id_traitement = ?");
        $stmt->execute([$id]);
    }

    // 🔹 Récupérer un traitement
    public function recupererTraitement($id) {

        $stmt = $this->pdo->prepare("SELECT * FROM traitement WHERE id_traitement = ?");
        $stmt->execute([$id]);

        return $stmt->fetch();
    }

    // 🔹 Modifier
    public function modifierTraitement($id, $donnees) {

        $stmt = $this->pdo->prepare("UPDATE traitement 
                             SET nom=?, type_traitement=?, dosage=?, duree=?, effets_secondaires=? 
                             WHERE id_traitement=?");

        $stmt->execute([
            $donnees['nom'],
            $donnees['type_traitement'],
            $donnees['dosage'],
            $donnees['duree'],
            $donnees['effets_secondaires'],
            $id
        ]);
    }

    // 🔹 Afficher allergies associées (relation)
    public function obtenirAllergiesAssociees($id_traitement) {

        $stmt = $this->pdo->prepare("
            SELECT a.* FROM allergie a
            INNER JOIN allergie_traitement at 
            ON a.id_allergie = at.id_allergie
            WHERE at.id_traitement = ?
        ");

        $stmt->execute([$id_traitement]);
        return $stmt->fetchAll();
    }

    /**
     * Jointure filtrée par traitement (table de liaison + INNER JOIN).
     */
    public function afficherAssociationsParTraitement(int $idTraitement): array
    {
        if ($idTraitement <= 0) {
            return [];
        }
        $stmt = $this->pdo->prepare("
            SELECT at.id_allergie, at.id_traitement,
                   a.nom AS allergie_nom, t.nom AS traitement_nom, t.type_traitement
            FROM allergie_traitement at
            INNER JOIN allergie a ON a.id_allergie = at.id_allergie
            INNER JOIN traitement t ON t.id_traitement = at.id_traitement
            WHERE at.id_traitement = ?
            ORDER BY a.nom ASC
        ");
        $stmt->execute([$idTraitement]);
        return $stmt->fetchAll();
    }

    public function supprimerLiensTraitement(int $idTraitement): void
    {
        $stmt = $this->pdo->prepare('DELETE FROM allergie_traitement WHERE id_traitement = ?');
        $stmt->execute([$idTraitement]);
    }

    /**
     * Traite GET/POST pour la liste traitements (suppression).
     *
     * @return array{message:string,erreurs:array,rows:array}
     */
    public function traiterRequetePageTraitements(): array
    {
        $message = isset($_GET['message']) ? (string) $_GET['message'] : '';
        $erreurs = [];

        if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_GET['action'] ?? '') === 'supprimer') {
            $id = (int) ($_POST['id_traitement'] ?? 0);
            if ($id <= 0) {
                $erreurs[] = 'Identifiant invalide.';
            } else {
                try {
                    $this->supprimerLiensTraitement($id);
                    $this->supprimerTraitement($id);
                    header('Location: traitements.php?message=' . rawurlencode('Traitement supprimé.'));
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
     * Traite GET/POST pour traitement_form (création / édition).
     *
     * @return array{mode:string,row:?array,ancien:array,erreurs:array,message:string}
     */
    public function traiterRequetePageTraitementForm(): array
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
                    $this->creerTraitement($_POST);
                    header('Location: traitements.php?message=' . rawurlencode('Traitement ajouté.'));
                    exit;
                }
                if ($actionPost === 'modifier') {
                    $id = (int) ($_POST['id_traitement'] ?? 0);
                    $this->modifierTraitement($id, $_POST);
                    header('Location: traitements.php?message=' . rawurlencode('Traitement modifié.'));
                    exit;
                }
            } catch (Throwable $e) {
                $erreurs[] = $e->getMessage() ?: 'Erreur.';
                if ($actionPost === 'modifier') {
                    $action = 'editer';
                    $id = (int) ($_POST['id_traitement'] ?? 0);
                    $row = $id > 0 ? $this->recupererTraitement($id) : null;
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
                $row = $id > 0 ? $this->recupererTraitement($id) : null;
            }
            if (!$row) {
                header('Location: traitements.php?message=' . rawurlencode('Traitement introuvable.'));
                exit;
            }
        } else {
            header('Location: traitements.php');
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