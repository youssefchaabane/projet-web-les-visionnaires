<?php
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../model/Publication.php';

require_once __DIR__ . '/../services/AIContentFilter.php';
require_once __DIR__ . '/../services/AIContentAssistant.php';

class PublicationController
{
    private $pdo;

    public function __construct()
    {
        $this->pdo = Config::getConnexion();

        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        if (!isset($_SESSION['signalements'])) {
            $_SESSION['signalements'] = [];
        }
    }

    private function validatePublication(array $data): array
    {
        $errors = [];

        $titre = trim((string)($data['titre'] ?? ''));
        $contenu = trim((string)($data['contenu'] ?? ''));
        $mediaUrl = trim((string)($data['media_url'] ?? ''));
        $idUser = (int)($data['id_user'] ?? 0);

        if (strlen($titre) < 3 || strlen($titre) > 255) {
            $errors['titre'] = 'Le titre doit contenir entre 3 et 255 caractères.';
        }

        if ($contenu === '') {
            $errors['contenu'] = 'Le contenu est obligatoire.';
        }

        if ($mediaUrl !== '' && strlen($mediaUrl) > 512) {
            $errors['media_url'] = 'URL média trop longue.';
        }

        if ($idUser < 1) {
            $errors['id_user'] = 'id_user invalide.';
        }

        return $errors;
    }

    public function ajouterPublication($data, $contenu = null, $mediaUrl = null, $idUser = null): array
    {
        if (!is_array($data)) {
            $data = [
                'titre' => $data,
                'contenu' => $contenu,
                'media_url' => $mediaUrl,
                'id_user' => $idUser
            ];
        }

        $errors = $this->validatePublication($data);

        if (!empty($errors)) {
            return ['success' => false, 'errors' => $errors, 'message' => implode(' | ', $errors)];
        }

        $titre = trim((string)$data['titre']);
        $contenu = trim((string)$data['contenu']);

        if (class_exists('AIContentFilter')) {
            $checkTitre = AIContentFilter::detectBadWordsAdvanced($titre);
            if (!empty($checkTitre['blocked'])) {
                return [
                    'success' => false,
                    'errors' => ['titre' => 'Contenu inapproprié détecté dans le titre.'],
                    'message' => 'Contenu inapproprié détecté dans le titre.'
                ];
            }

            $checkContenu = AIContentFilter::detectBadWordsAdvanced($contenu);
            if (!empty($checkContenu['blocked'])) {
                return [
                    'success' => false,
                    'errors' => ['contenu' => 'Contenu inapproprié détecté.'],
                    'message' => 'Contenu inapproprié détecté.'
                ];
            }

        }

        date_default_timezone_set('Europe/Rome');
        $datePublication = date('Y-m-d H:i:s');

        $sql = 'INSERT INTO publication (titre, contenu, date_publication, media_url, id_user)
                VALUES (:titre, :contenu, :date_publication, :media_url, :id_user)';

        $st = $this->pdo->prepare($sql);
        $st->bindValue(':titre', $titre);
        $st->bindValue(':contenu', $contenu);
        $st->bindValue(':date_publication', $datePublication);
        $st->bindValue(':media_url', trim((string)($data['media_url'] ?? '')) !== '' ? trim((string)$data['media_url']) : null);
        $st->bindValue(':id_user', (int)$data['id_user'], PDO::PARAM_INT);
        $st->execute();

        return ['success' => true, 'message' => 'Publication ajoutée.'];
    }

    public function afficherPublications(): array
    {
        $sql = 'SELECT id_pub, titre, contenu, date_publication, media_url, id_user
                FROM publication
                ORDER BY date_publication DESC';

        return $this->pdo->query($sql)->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getPublicationsFiltered(string $filter): array
    {
        $allowedFilters = ['az', 'za', 'note_asc', 'note_desc', 'id_asc', 'id_desc'];

        if (!in_array($filter, $allowedFilters, true)) {
            $filter = 'az';
        }

        switch ($filter) {
            case 'za':
                $orderBy = 'p.titre DESC';
                break;
            case 'note_asc':
                $orderBy = 'note_moyenne ASC';
                break;
            case 'note_desc':
                $orderBy = 'note_moyenne DESC';
                break;
            case 'id_asc':
                $orderBy = 'p.id_pub ASC';
                break;
            case 'id_desc':
                $orderBy = 'p.id_pub DESC';
                break;
            case 'az':
            default:
                $orderBy = 'p.titre ASC';
                break;
        }

        $sql = "SELECT p.id_pub, p.titre, p.contenu, p.date_publication, p.media_url, p.id_user,
                       IFNULL(AVG(c.note), 0) AS note_moyenne
                FROM publication p
                LEFT JOIN commentaire c ON p.id_pub = c.id_pub
                GROUP BY p.id_pub, p.titre, p.contenu, p.date_publication, p.media_url, p.id_user
                ORDER BY $orderBy";

        return $this->pdo->query($sql)->fetchAll(PDO::FETCH_ASSOC);
    }

    public function searchPublications(string $keyword): array
    {
        $keyword = '%' . trim($keyword) . '%';

        $sql = 'SELECT id_pub, titre, contenu, date_publication, media_url, id_user
                FROM publication
                WHERE titre LIKE :keyword
                   OR contenu LIKE :keyword
                   OR id_pub LIKE :keyword
                   OR id_user LIKE :keyword
                ORDER BY date_publication DESC';

        $st = $this->pdo->prepare($sql);
        $st->bindValue(':keyword', $keyword);
        $st->execute();

        return $st->fetchAll(PDO::FETCH_ASSOC);
    }

    public function afficherPublicationParId(int $idPub): ?array
    {
        $sql = 'SELECT id_pub, titre, contenu, date_publication, media_url, id_user
                FROM publication
                WHERE id_pub = ?';

        $st = $this->pdo->prepare($sql);
        $st->execute([$idPub]);
        $row = $st->fetch(PDO::FETCH_ASSOC);

        return $row ?: null;
    }

    public function getPublicationById(int $idPub): ?array
    {
        return $this->afficherPublicationParId($idPub);
    }

    public function modifierPublication($idPub, $data, $contenu = null, $mediaUrl = null, $idUser = null): array
    {
        if (!is_array($data)) {
            $data = [
                'titre' => $data,
                'contenu' => $contenu,
                'media_url' => $mediaUrl,
                'id_user' => $idUser
            ];
        }

        $errors = $this->validatePublication($data);

        if (!empty($errors)) {
            return ['success' => false, 'errors' => $errors, 'message' => implode(' | ', $errors)];
        }

        $titre = trim((string)$data['titre']);
        $contenu = trim((string)$data['contenu']);

        if (class_exists('AIContentFilter')) {
            $checkTitre = AIContentFilter::detectBadWordsAdvanced($titre);
            if (!empty($checkTitre['blocked'])) {
                return ['success' => false, 'errors' => ['titre' => 'Contenu inapproprié détecté dans le titre.']];
            }

            $checkContenu = AIContentFilter::detectBadWordsAdvanced($contenu);
            if (!empty($checkContenu['blocked'])) {
                return ['success' => false, 'errors' => ['contenu' => 'Contenu inapproprié détecté.']];
            }

        }

        $sql = 'UPDATE publication
                SET titre = ?, contenu = ?, media_url = ?, id_user = ?
                WHERE id_pub = ?';

        $st = $this->pdo->prepare($sql);
        $st->execute([
            $titre,
            $contenu,
            trim((string)($data['media_url'] ?? '')) !== '' ? trim((string)$data['media_url']) : null,
            (int)$data['id_user'],
            (int)$idPub
        ]);

        return ['success' => true, 'message' => 'Publication modifiée.'];
    }

    public function supprimerPublication(int $idPub): array
    {
        $sql = 'DELETE FROM commentaire WHERE id_pub = ?';
        $st = $this->pdo->prepare($sql);
        $st->execute([$idPub]);

        $sql = 'DELETE FROM publication WHERE id_pub = ?';
        $st = $this->pdo->prepare($sql);
        $st->execute([$idPub]);

        return ['success' => true, 'message' => 'Publication supprimée.'];
    }

    public function likerPublication(int $idPub): array
    {
        return ['success' => true, 'message' => 'Interaction enregistrée.'];
    }

    public function signalerPublication(int $idPub): array
    {
        $_SESSION['signalements'][] = [
            'type' => 'publication',
            'id' => $idPub,
            'date_signalement' => date('Y-m-d H:i:s'),
            'etat' => 'En attente'
        ];

        return ['success' => true, 'message' => 'Publication signalée.'];
    }

    public function listerSignalements(): array
    {
        return $_SESSION['signalements'] ?? [];
    }

    public function validerSignalement(int $index): array
    {
        if (!isset($_SESSION['signalements'][$index])) {
            return ['success' => false, 'message' => 'Signalement introuvable.'];
        }

        $signalement = $_SESSION['signalements'][$index];

        if (($signalement['type'] ?? '') === 'publication') {
            $this->supprimerPublication((int)$signalement['id']);
        }

        $_SESSION['signalements'][$index]['etat'] = 'Validé';

        return ['success' => true, 'message' => 'Signalement validé.'];
    }

    public function rejeterSignalement(int $index): array
    {
        if (!isset($_SESSION['signalements'][$index])) {
            return ['success' => false, 'message' => 'Signalement introuvable.'];
        }

        $_SESSION['signalements'][$index]['etat'] = 'Rejeté';

        return ['success' => true, 'message' => 'Signalement rejeté.'];
    }

    public function getDetailAvecJointure(int $idPub): array
    {
        $sql = 'SELECT 
                    p.id_pub,
                    p.titre,
                    p.contenu AS contenu_publication,
                    p.date_publication,
                    p.media_url,
                    p.id_user,
                    c.id_commentaire,
                    c.contenu AS contenu_commentaire,
                    c.note,
                    c.likes_count,
                    c.date_commentaire
                FROM publication p
                LEFT JOIN commentaire c ON p.id_pub = c.id_pub
                WHERE p.id_pub = ?
                ORDER BY c.date_commentaire DESC';

        $st = $this->pdo->prepare($sql);
        $st->execute([$idPub]);

        return $st->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getPublicationsWithStats(): array
    {
        $sql = 'SELECT 
                    p.id_pub,
                    p.titre,
                    p.contenu,
                    p.date_publication,
                    p.media_url,
                    p.id_user,
                    COUNT(c.id_commentaire) AS total_commentaires,
                    IFNULL(AVG(c.note), 0) AS note_moyenne
                FROM publication p
                LEFT JOIN commentaire c ON p.id_pub = c.id_pub
                GROUP BY p.id_pub, p.titre, p.contenu, p.date_publication, p.media_url, p.id_user
                ORDER BY p.date_publication DESC';

        return $this->pdo->query($sql)->fetchAll(PDO::FETCH_ASSOC);
    }

    private function formatDatePdf($date): string
    {
        if (empty($date) || $date === '0000-00-00' || $date === '0000-00-00 00:00:00') {
            return 'Date non disponible';
        }

        $timestamp = strtotime($date);

        if ($timestamp === false) {
            return 'Date non disponible';
        }

        return date('d/m/Y', $timestamp);
    }

    public function exportPdf(): void
    {
        while (ob_get_level()) {
            ob_end_clean();
        }

        require_once __DIR__ . '/../lib/fpdf/fpdf.php';

        $publications = $this->getPublicationsWithStats();

        $pdf = new FPDF('P', 'mm', 'A4');
        $pdf->SetMargins(15, 15, 15);
        $pdf->SetAutoPageBreak(true, 15);
        $pdf->AddPage();

        $pdf->SetFont('Arial', 'B', 18);
        $pdf->Cell(180, 12, 'Liste des publications', 0, 1, 'C');
        $pdf->Ln(8);

        $pdf->SetFont('Arial', 'B', 9);
        $pdf->Cell(12, 8, 'ID', 1, 0, 'C');
        $pdf->Cell(38, 8, 'Titre', 1, 0, 'C');
        $pdf->Cell(55, 8, 'Contenu', 1, 0, 'C');
        $pdf->Cell(35, 8, 'Date', 1, 0, 'C');
        $pdf->Cell(20, 8, 'Note', 1, 0, 'C');
        $pdf->Cell(25, 8, 'Coms', 1, 1, 'C');

        $pdf->SetFont('Arial', '', 8);

        foreach ($publications as $pub) {
            $pdf->Cell(12, 6, (string)$pub['id_pub'], 1, 0, 'C');
            $pdf->Cell(38, 6, substr((string)$pub['titre'], 0, 25), 1, 0, 'L');
            $pdf->Cell(55, 6, substr((string)$pub['contenu'], 0, 35), 1, 0, 'L');
            $pdf->Cell(35, 6, $this->formatDatePdf($pub['date_publication']), 1, 0, 'C');
            $pdf->Cell(20, 6, number_format((float)$pub['note_moyenne'], 1), 1, 0, 'C');
            $pdf->Cell(25, 6, (string)$pub['total_commentaires'], 1, 1, 'C');
        }

        $pdf->Output('D', 'publications_export.pdf');
        exit;
    }

    public function getAISummary(int $idPub): array
    {
        $publication = $this->afficherPublicationParId($idPub);

        if (!$publication) {
            return ['success' => false, 'data' => null, 'error' => 'Publication introuvable.'];
        }

        $details = $this->getDetailAvecJointure($idPub);
        $comments = [];

        foreach ($details as $row) {
            if (!empty($row['id_commentaire'])) {
                $comments[] = [
                    'contenu' => $row['contenu_commentaire'] ?? '',
                    'note' => $row['note'] ?? null,
                    'likes_count' => $row['likes_count'] ?? 0,
                    'date_commentaire' => $row['date_commentaire'] ?? ''
                ];
            }
        }

        if (class_exists('AIContentAssistant')) {
            return AIContentAssistant::summarizePublication($publication, $comments);
        }

        return ['success' => false, 'data' => null, 'error' => 'Service IA indisponible.'];
    }

    public function getAISuggestedResponse(int $idPub): array
    {
        $publication = $this->afficherPublicationParId($idPub);

        if (!$publication) {
            return ['success' => false, 'data' => null, 'error' => 'Publication introuvable.'];
        }

        $details = $this->getDetailAvecJointure($idPub);
        $comments = [];

        foreach ($details as $row) {
            if (!empty($row['id_commentaire'])) {
                $comments[] = [
                    'contenu' => $row['contenu_commentaire'] ?? '',
                    'note' => $row['note'] ?? null,
                    'likes_count' => $row['likes_count'] ?? 0,
                    'date_commentaire' => $row['date_commentaire'] ?? ''
                ];
            }
        }

        if (class_exists('AIContentAssistant')) {
            return AIContentAssistant::suggestResponse($publication, $comments);
        }

        return ['success' => false, 'data' => null, 'error' => 'Service IA indisponible.'];
    }

    public function generateSummaryAI(int $idPub): array
    {
        return $this->getAISummary($idPub);
    }

    public function generateSuggestionAI(int $idPub): array
    {
        return $this->getAISuggestedResponse($idPub);
    }

    /**
     * Génère une suggestion IA pour une publication (méthode simplifiée)
     * 
     * Utilise AIContentAssistant::generateSuggestionAI() pour créer
     * une réponse professionnelle basée uniquement sur le titre et contenu.
     * 
     * @param int $id_pub ID de la publication
     * @return array ['success'=>bool, 'data'=>string|null, 'error'=>string|null]
     */
    public function suggestionIA(int $id_pub): array
    {
        // Validation
        if ($id_pub < 1) {
            return [
                'success' => false,
                'data' => null,
                'error' => 'ID publication invalide.'
            ];
        }

        // Récupération de la publication via PDO
        $publication = $this->afficherPublicationParId($id_pub);

        if (!$publication) {
            return [
                'success' => false,
                'data' => null,
                'error' => 'Publication introuvable (ID: ' . $id_pub . ').'
            ];
        }

        // Combine titre + contenu
        $titre = $publication['titre'] ?? '';
        $contenu = $publication['contenu'] ?? '';
        $text = "Titre: " . $titre . "\nContenu: " . $contenu;

        // Appel au service IA
        if (class_exists('AIContentAssistant')) {
            return AIContentAssistant::generateSuggestionAI($text);
        }

        return [
            'success' => false,
            'data' => null,
            'error' => 'Service IA indisponible.'
        ];
    }

    public function countPublications(): int
    {
        $st = $this->pdo->prepare('SELECT COUNT(*) FROM publication');
        $st->execute();
        return (int) $st->fetchColumn();
    }

    public function countPublicationsSignalees(): int
    {
        $count = 0;
        foreach ($_SESSION['signalements'] ?? [] as $s) {
            if (($s['type'] ?? '') === 'publication' && ($s['etat'] ?? '') === 'a_moderer') {
                $count++;
            }
        }
        return $count;
    }

    public function getPublicationsPaginated(int $limit, int $offset): array
    {
        $sql = 'SELECT p.id_pub, p.titre, p.contenu, p.date_publication, p.media_url, p.id_user,
                       IFNULL(AVG(c.note), 0) AS note_moyenne
                FROM publication p
                LEFT JOIN commentaire c ON p.id_pub = c.id_pub
                GROUP BY p.id_pub, p.titre, p.contenu, p.date_publication, p.media_url, p.id_user
                ORDER BY p.date_publication DESC
                LIMIT :lim OFFSET :off';
        $st = $this->pdo->prepare($sql);
        $st->bindValue(':lim', $limit, PDO::PARAM_INT);
        $st->bindValue(':off', $offset, PDO::PARAM_INT);
        $st->execute();
        return $st->fetchAll(PDO::FETCH_ASSOC);
    }
}