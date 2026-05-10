<?php
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../model/Commentaire.php';
require_once __DIR__ . '/../services/AIContentFilter.php';

class CommentaireController
{
    private $pdo;

    public function __construct()
    {
        $this->pdo = PubConfig::getConnexion();
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        if (!isset($_SESSION['signalements'])) {
            $_SESSION['signalements'] = [];
        }
    }

    private function validateCommentaire(array $data): array
    {
        $errors = [];
        $contenu = trim((string) ($data['contenu'] ?? ''));
        $idPub = (int) ($data['id_pub'] ?? 0);
        $noteRaw = $data['note'] ?? '';

        if ($contenu === '') {
            $errors['contenu'] = 'Le commentaire est obligatoire.';
        }
        if ($idPub < 1) {
            $errors['id_pub'] = 'Publication invalide.';
        }
        if ($noteRaw !== '' && $noteRaw !== null) {
            $note = (int) $noteRaw;
            if ($note < 1 || $note > 5) {
                $errors['note'] = 'La note doit etre comprise entre 1 et 5.';
            }
        }

        return $errors;
    }

    public function ajouterCommentaire(array $data): array
    {
        $errors = $this->validateCommentaire($data);
        if (!empty($errors)) {
            return ['success' => false, 'errors' => $errors];
        }

        // DETECTION IA AVANCEE
        $contenu = trim((string) $data['contenu']);
        $checkContenu = AIContentFilter::detectBadWordsAdvanced($contenu);
        if ($checkContenu['blocked']) {
            return ['success' => false, 'errors' => ['contenu' => 'Contenu inapproprie detecte. (Toxicite: ' . $checkContenu['score'] . ')']];
        }

        $date_commentaire = date('Y-m-d');
        $sql = 'INSERT INTO commentaire (note, contenu, date_commentaire, likes_count, id_pub) VALUES (:note, :contenu, :date_commentaire, 0, :id_pub)';
        $st = $this->pdo->prepare($sql);
        $noteValue = ($data['note'] === '' || $data['note'] === null) ? null : (int) $data['note'];
        $st->bindValue(':note', $noteValue);
        $st->bindValue(':contenu', $contenu);
        $st->bindValue(':date_commentaire', $date_commentaire);
        $st->bindValue(':id_pub', (int) $data['id_pub'], PDO::PARAM_INT);
        $st->execute();

        return ['success' => true, 'message' => 'Commentaire ajoute.'];
    }

    public function afficherCommentairesParPublication(int $idPub): array
    {
        $st = $this->pdo->prepare(
            'SELECT id_commentaire, note, contenu, date_commentaire, likes_count, id_pub
             FROM commentaire WHERE id_pub = ? ORDER BY date_commentaire ASC'
        );
        $st->execute([$idPub]);
        return $st->fetchAll();
    }

    public function afficherTousLesCommentaires(): array
    {
        $sql = 'SELECT id_commentaire, note, contenu, date_commentaire, likes_count, id_pub
                FROM commentaire ORDER BY date_commentaire DESC';
        return $this->pdo->query($sql)->fetchAll();
    }

    public function afficherCommentaireParId(int $idCommentaire): ?array
    {
        $st = $this->pdo->prepare(
            'SELECT id_commentaire, note, contenu, date_commentaire, likes_count, id_pub
             FROM commentaire WHERE id_commentaire = ?'
        );
        $st->execute([$idCommentaire]);
        $row = $st->fetch();
        return $row ?: null;
    }

    /**
     * Alias de afficherCommentaireParId() pour compatibilité
     */
    public function getCommentaireById(int $idCommentaire): ?array
    {
        return $this->afficherCommentaireParId($idCommentaire);
    }

    public function modifierCommentaire(int $idCommentaire, array $data): array
    {
        $errors = [];
        $contenu = trim((string) ($data['contenu'] ?? ''));
        $noteRaw = $data['note'] ?? '';

        if ($contenu === '') {
            $errors['contenu'] = 'Le commentaire est obligatoire.';
        }
        if ($noteRaw !== '' && $noteRaw !== null) {
            $note = (int) $noteRaw;
            if ($note < 1 || $note > 5) {
                $errors['note'] = 'La note doit etre comprise entre 1 et 5.';
            }
        }
        if (!empty($errors)) {
            return ['success' => false, 'errors' => $errors];
        }

        // DETECTION IA AVANCEE
        $checkContenu = AIContentFilter::detectBadWordsAdvanced($contenu);
        if ($checkContenu['blocked']) {
            return ['success' => false, 'errors' => ['contenu' => 'Contenu inapproprie detecte. (Toxicite: ' . $checkContenu['score'] . ')']];
        }

        $noteValue = ($noteRaw === '' || $noteRaw === null) ? null : (int) $noteRaw;
        $st = $this->pdo->prepare(
            'UPDATE commentaire SET note = ?, contenu = ? WHERE id_commentaire = ?'
        );
        $st->execute([$noteValue, $contenu, $idCommentaire]);

        return ['success' => true, 'message' => 'Commentaire modifie.'];
    }

    public function supprimerCommentaire(int $idCommentaire): array
    {
        $st = $this->pdo->prepare('DELETE FROM commentaire WHERE id_commentaire = ?');
        $st->execute([$idCommentaire]);
        return ['success' => true, 'message' => 'Commentaire supprime.'];
    }

    public function likerCommentaire(int $idCommentaire): array
    {
        $st = $this->pdo->prepare(
            'UPDATE commentaire SET likes_count = likes_count + 1 WHERE id_commentaire = ?'
        );
        $st->execute([$idCommentaire]);
        return ['success' => true, 'message' => 'Like enregistre.'];
    }

    public function countCommentaires(): int
    {
        $st = $this->pdo->prepare('SELECT COUNT(*) FROM commentaire');
        $st->execute();
        return (int) $st->fetchColumn();
    }

    public function countAllCommentaires(): int
    {
        $st = $this->pdo->prepare('SELECT COUNT(*) FROM commentaire');
        $st->execute();
        return (int) $st->fetchColumn();
    }

    public function countCommentairesByPublication(int $idPub): int
    {
        $st = $this->pdo->prepare('SELECT COUNT(*) FROM commentaire WHERE id_pub = ?');
        $st->execute([$idPub]);
        return (int) $st->fetchColumn();
    }

    public function getAverageNoteByPublication(int $idPub): ?float
    {
        $st = $this->pdo->prepare('SELECT AVG(note) FROM commentaire WHERE id_pub = ?');
        $st->execute([$idPub]);
        $val = $st->fetchColumn();
        if ($val === false || $val === null) {
            return null;
        }
        return (float) $val;
    }

    public function countCommentairesRecents(): int
    {
        $st = $this->pdo->prepare(
            "SELECT COUNT(*) FROM commentaire WHERE date_commentaire >= DATE_SUB(CURDATE(), INTERVAL 7 DAY)"
        );
        $st->execute();
        return (int) $st->fetchColumn();
    }

    public function signalerCommentaire(int $idCommentaire): array
    {
        $_SESSION['signalements'][] = [
            'type' => 'commentaire',
            'id' => $idCommentaire,
            'date_signalement' => date('Y-m-d H:i:s'),
            'etat' => 'a_moderer',
        ];

        return ['success' => true, 'message' => 'Commentaire signale.'];
    }

    public function searchCommentairesAdmin(string $keyword): array
    {
        $search = '%' . $keyword . '%';
        $sql = 'SELECT id_commentaire, note, contenu, date_commentaire, likes_count, id_pub 
                FROM commentaire 
                WHERE CAST(id_commentaire AS CHAR) LIKE ? 
                   OR CAST(id_pub AS CHAR) LIKE ? 
                   OR CAST(note AS CHAR) LIKE ? 
                   OR contenu LIKE ? 
                   OR CAST(likes_count AS CHAR) LIKE ?';
        $st = $this->pdo->prepare($sql);
        $st->execute([$search, $search, $search, $search, $search]);
        return $st->fetchAll();
    }

    public function getCommentairesFilteredAdmin(string $filter): array
    {
        $allowedFilters = ['az', 'za', 'likes_asc', 'likes_desc'];
        if (!in_array($filter, $allowedFilters, true)) {
            $filter = 'likes_desc';
        }

        switch ($filter) {
            case 'az':
                $orderBy = 'contenu ASC';
                break;
            case 'za':
                $orderBy = 'contenu DESC';
                break;
            case 'likes_asc':
                $orderBy = 'likes_count ASC';
                break;
            case 'likes_desc':
                $orderBy = 'likes_count DESC';
                break;
            default:
                $orderBy = 'likes_count DESC';
        }

        $sql = "SELECT id_commentaire, note, contenu, date_commentaire, likes_count, id_pub 
                FROM commentaire 
                ORDER BY {$orderBy}";

        return $this->pdo->query($sql)->fetchAll();
    }

    public function searchAndFilterCommentairesAdmin(string $keyword, string $filter): array
    {
        $allowedFilters = ['az', 'za', 'likes_asc', 'likes_desc'];
        if (!in_array($filter, $allowedFilters, true)) {
            $filter = 'likes_desc';
        }

        switch ($filter) {
            case 'az':
                $orderBy = 'contenu ASC';
                break;
            case 'za':
                $orderBy = 'contenu DESC';
                break;
            case 'likes_asc':
                $orderBy = 'likes_count ASC';
                break;
            case 'likes_desc':
                $orderBy = 'likes_count DESC';
                break;
            default:
                $orderBy = 'likes_count DESC';
        }

        $search = '%' . $keyword . '%';
        $sql = "SELECT id_commentaire, note, contenu, date_commentaire, likes_count, id_pub 
                FROM commentaire 
                WHERE CAST(id_commentaire AS CHAR) LIKE ? 
                   OR CAST(id_pub AS CHAR) LIKE ? 
                   OR CAST(note AS CHAR) LIKE ? 
                   OR contenu LIKE ? 
                   OR CAST(likes_count AS CHAR) LIKE ? 
                ORDER BY {$orderBy}";

        $st = $this->pdo->prepare($sql);
        $st->execute([$search, $search, $search, $search, $search]);
        return $st->fetchAll();
    }
}

