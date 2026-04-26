<?php

declare(strict_types=1);

require_once __DIR__ . '/../config/config.php';

/**
 * Métier back-office : barre de recherche unique (`q`) + tri alphabétique / logique (`tri`, liste blanche).
 */
class Metier
{
    private PDO $pdo;

    public function __construct()
    {
        $this->pdo = config::getConnexion();
    }

    /**
     * @param array<string,mixed> $get typiquement $_GET
     */
    public static function termeBarreDepuisGet(array $get, string $cle = 'q'): string
    {
        return isset($get[$cle]) ? trim((string) $get[$cle]) : '';
    }

    /** Colonne de tri pour la liste allergies (GET `tri`). */
    public static function triAllergieDepuisGet(array $get): string
    {
        $t = isset($get['tri']) ? strtolower(trim((string) $get['tri'])) : 'nom';

        return in_array($t, ['nom', 'type', 'niveau_danger'], true) ? $t : 'nom';
    }

    /** Colonne de tri pour la liste traitements (GET `tri`). */
    public static function triTraitementDepuisGet(array $get): string
    {
        $t = isset($get['tri']) ? strtolower(trim((string) $get['tri'])) : 'nom';

        return in_array($t, ['nom', 'type_traitement', 'dosage'], true) ? $t : 'nom';
    }

    /** Mode de tri associations : allergie ou traitement (GET `tri`). */
    public static function triAssociationDepuisGet(array $get): string
    {
        $t = isset($get['tri']) ? strtolower(trim((string) $get['tri'])) : 'allergie';

        return in_array($t, ['allergie', 'traitement'], true) ? $t : 'allergie';
    }

    /**
     * @return list<array<string,mixed>>
     */
    public function rechercherAllergies(?string $terme = null, string $triCol = 'nom'): array
    {
        $order = $this->clauseOrderAllergie($triCol);
        $t = $terme !== null ? trim($terme) : '';
        if ($t === '') {
            $stmt = $this->pdo->query('SELECT * FROM allergie ' . $order);

            return $stmt ? $stmt->fetchAll(PDO::FETCH_ASSOC) : [];
        }

        $like = '%' . $t . '%';
        $sql = 'SELECT * FROM allergie WHERE (
            `nom` LIKE ? OR `type` LIKE ? OR `niveau_danger` LIKE ? OR `description` LIKE ? OR `symptomes` LIKE ?
            OR CAST(`id_allergie` AS CHAR) LIKE ?
        ) ' . $order;
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([$like, $like, $like, $like, $like, $like]);

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * @return list<array<string,mixed>>
     */
    public function rechercherTraitements(?string $terme = null, string $triCol = 'nom'): array
    {
        $order = $this->clauseOrderTraitement($triCol);
        $t = $terme !== null ? trim($terme) : '';
        if ($t === '') {
            $stmt = $this->pdo->query('SELECT * FROM traitement ' . $order);

            return $stmt ? $stmt->fetchAll(PDO::FETCH_ASSOC) : [];
        }

        $like = '%' . $t . '%';
        $sql = 'SELECT * FROM traitement WHERE (
            `nom` LIKE ? OR `type_traitement` LIKE ? OR `dosage` LIKE ? OR `duree` LIKE ? OR `effets_secondaires` LIKE ?
            OR CAST(`id_traitement` AS CHAR) LIKE ?
        ) ' . $order;
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([$like, $like, $like, $like, $like, $like]);

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * @return list<array<string,mixed>>
     */
    public function rechercherAssociations(int $idAllergieFiltre, int $idTraitementFiltre, ?string $terme = null, string $triMode = 'allergie'): array
    {
        $parts = [];
        $bind = [];

        if ($idAllergieFiltre > 0) {
            $parts[] = 'at.id_allergie = ?';
            $bind[] = $idAllergieFiltre;
        }
        if ($idTraitementFiltre > 0) {
            $parts[] = 'at.id_traitement = ?';
            $bind[] = $idTraitementFiltre;
        }

        $t = $terme !== null ? trim($terme) : '';
        if ($t !== '') {
            $like = '%' . $t . '%';
            $parts[] = '(
                a.nom LIKE ? OR t.nom LIKE ? OR t.type_traitement LIKE ?
                OR CAST(at.id_allergie AS CHAR) LIKE ? OR CAST(at.id_traitement AS CHAR) LIKE ?
            )';
            array_push($bind, $like, $like, $like, $like, $like);
        }

        $whereSql = $parts === [] ? '1=1' : implode(' AND ', $parts);
        $orderSql = $triMode === 'traitement'
            ? ' ORDER BY t.nom ASC, a.nom ASC'
            : ' ORDER BY a.nom ASC, t.nom ASC';

        $sql = '
            SELECT at.id_allergie,
                   at.id_traitement,
                   a.nom AS allergie_nom,
                   t.nom AS traitement_nom,
                   t.type_traitement
            FROM allergie_traitement at
            INNER JOIN allergie a ON a.id_allergie = at.id_allergie
            INNER JOIN traitement t ON t.id_traitement = at.id_traitement
            WHERE ' . $whereSql . $orderSql;

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($bind);

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    private function clauseOrderAllergie(string $triCol): string
    {
        $map = [
            'nom' => ' ORDER BY `nom` ASC',
            'type' => ' ORDER BY `type` ASC',
            'niveau_danger' => ' ORDER BY `niveau_danger` ASC',
        ];

        return isset($map[$triCol]) ? $map[$triCol] : ' ORDER BY `nom` ASC';
    }

    private function clauseOrderTraitement(string $triCol): string
    {
        $map = [
            'nom' => ' ORDER BY `nom` ASC',
            'type_traitement' => ' ORDER BY `type_traitement` ASC',
            'dosage' => ' ORDER BY `dosage` ASC',
        ];

        return isset($map[$triCol]) ? $map[$triCol] : ' ORDER BY `nom` ASC';
    }


    /**
     * Si la page liste est appelée avec ?export=pdf, génère le PDF (mêmes filtres / tri que l’affichage) puis termine le script.
     * À appeler tout en haut de allergies.php / traitements.php / associations.php, juste après require metier.php.
     *
     * @param 'allergies'|'traitements'|'associations' $page
     */
    public static function repondreExportPdfSiDemande(string $page): void
    {
        if (($_GET['export'] ?? '') !== 'pdf') {
            return;
        }
        $m = new self();
        try {
            if ($page === 'allergies') {
                $m->exporterPdfAllergies(self::termeBarreDepuisGet($_GET), self::triAllergieDepuisGet($_GET));
            } elseif ($page === 'traitements') {
                $m->exporterPdfTraitements(self::termeBarreDepuisGet($_GET), self::triTraitementDepuisGet($_GET));
            } elseif ($page === 'associations') {
                $m->exporterPdfAssociations(
                    (int) ($_GET['id_allergie'] ?? 0),
                    (int) ($_GET['id_traitement'] ?? 0),
                    self::termeBarreDepuisGet($_GET),
                    self::triAssociationDepuisGet($_GET)
                );
            }
        } catch (Throwable $e) {
            $script = basename($_SERVER['SCRIPT_NAME'] ?? 'index.php');
            header('Location: ' . $script . '?pdf_err=' . rawurlencode($e->getMessage() ?: 'Erreur export PDF.'));
            exit;
        }
        exit;
    }

    public function exporterPdfAllergies(?string $terme, string $triCol, string $nomFichier = 'allergies.pdf'): void
    {
        self::chargerFpdf();
        $lignes = $this->rechercherAllergies($terme, $triCol);

        $pdf = new FPDF();
        $pdf->AddPage();
        $pdf->SetFont('Arial', 'B', 14);
        $pdf->Cell(0, 10, self::pdfTexte('Allergies'), 0, 1);
        $pdf->SetFont('Arial', '', 8);
        $pdf->Cell(0, 6, self::pdfTexte('Tri : ' . $triCol . (trim((string) $terme) !== '' ? ' | Recherche : ' . $terme : '')), 0, 1);
        $pdf->Ln(2);
        $pdf->SetFont('Arial', 'B', 7);
        $w = [12, 30, 22, 20, 48, 58];
        self::pdfLigneTableau($pdf, $w, ['ID', 'Nom', 'Type', 'Niv.', 'Description', 'Symptomes'], true);
        $pdf->SetFont('Arial', '', 6);
        foreach ($lignes as $r) {
            self::pdfLigneTableau($pdf, $w, [
                (string) ($r['id_allergie'] ?? ''),
                self::pdfTexte(self::pdfTronquer((string) ($r['nom'] ?? ''), 22)),
                self::pdfTexte(self::pdfTronquer((string) ($r['type'] ?? ''), 16)),
                self::pdfTexte(self::pdfTronquer((string) ($r['niveau_danger'] ?? ''), 12)),
                self::pdfTexte(self::pdfTronquer((string) ($r['description'] ?? ''), 40)),
                self::pdfTexte(self::pdfTronquer((string) ($r['symptomes'] ?? ''), 48)),
            ], false);
        }
        $pdf->Output('D', $nomFichier);
    }

    public function exporterPdfTraitements(?string $terme, string $triCol, string $nomFichier = 'traitements.pdf'): void
    {
        self::chargerFpdf();
        $lignes = $this->rechercherTraitements($terme, $triCol);

        $pdf = new FPDF();
        $pdf->AddPage();
        $pdf->SetFont('Arial', 'B', 14);
        $pdf->Cell(0, 10, self::pdfTexte('Traitements'), 0, 1);
        $pdf->SetFont('Arial', '', 8);
        $pdf->Cell(0, 6, self::pdfTexte('Tri : ' . $triCol . (trim((string) $terme) !== '' ? ' | Recherche : ' . $terme : '')), 0, 1);
        $pdf->Ln(2);
        $pdf->SetFont('Arial', 'B', 8);
        $w = [14, 40, 32, 22, 18, 64];
        self::pdfLigneTableau($pdf, $w, ['ID', 'Nom', 'Type', 'Dosage', 'Duree', 'Effets'], true);
        $pdf->SetFont('Arial', '', 7);
        foreach ($lignes as $r) {
            self::pdfLigneTableau($pdf, $w, [
                (string) ($r['id_traitement'] ?? ''),
                self::pdfTexte(self::pdfTronquer((string) ($r['nom'] ?? ''), 28)),
                self::pdfTexte(self::pdfTronquer((string) ($r['type_traitement'] ?? ''), 22)),
                self::pdfTexte(self::pdfTronquer((string) ($r['dosage'] ?? ''), 16)),
                self::pdfTexte(self::pdfTronquer((string) ($r['duree'] ?? ''), 12)),
                self::pdfTexte(self::pdfTronquer((string) ($r['effets_secondaires'] ?? ''), 48)),
            ], false);
        }
        $pdf->Output('D', $nomFichier);
    }

    public function exporterPdfAssociations(int $idAllergieFiltre, int $idTraitementFiltre, ?string $terme, string $triMode, string $nomFichier = 'associations.pdf'): void
    {
        self::chargerFpdf();
        $lignes = $this->rechercherAssociations($idAllergieFiltre, $idTraitementFiltre, $terme, $triMode);

        $pdf = new FPDF();
        $pdf->AddPage();
        $pdf->SetFont('Arial', 'B', 14);
        $pdf->Cell(0, 10, self::pdfTexte('Associations'), 0, 1);
        $pdf->SetFont('Arial', '', 8);
        $pdf->Cell(0, 6, self::pdfTexte('Tri : ' . $triMode . (trim((string) $terme) !== '' ? ' | Recherche : ' . $terme : '')), 0, 1);
        $pdf->Ln(2);
        $pdf->SetFont('Arial', 'B', 8);
        $w = [16, 16, 58, 58, 42];
        self::pdfLigneTableau($pdf, $w, ['Id al.', 'Id tr.', 'Allergie', 'Traitement', 'Type'], true);
        $pdf->SetFont('Arial', '', 7);
        foreach ($lignes as $r) {
            self::pdfLigneTableau($pdf, $w, [
                (string) ($r['id_allergie'] ?? ''),
                (string) ($r['id_traitement'] ?? ''),
                self::pdfTexte(self::pdfTronquer((string) ($r['allergie_nom'] ?? ''), 32)),
                self::pdfTexte(self::pdfTronquer((string) ($r['traitement_nom'] ?? ''), 32)),
                self::pdfTexte(self::pdfTronquer((string) ($r['type_traitement'] ?? ''), 24)),
            ], false);
        }
        $pdf->Output('D', $nomFichier);
    }

    private static function chargerFpdf(): void
    {
        if (class_exists('FPDF', false)) {
            return;
        }

        $cheminProjet = __DIR__ . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'fpdf' . DIRECTORY_SEPARATOR . 'fpdf.php';
        if (is_file($cheminProjet) && is_readable($cheminProjet)) {
            require_once $cheminProjet;

            return;
        }

        // Aucun dossier app/fpdf : récupération unique dans le répertoire temporaire (hors projet).
        $base = rtrim(sys_get_temp_dir(), DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR . 'ecosave_fpdf_cache';
        if (!is_dir($base) && !@mkdir($base, 0700, true) && !is_dir($base)) {
            throw new RuntimeException('Impossible de créer le cache FPDF dans le dossier temporaire.');
        }

        $fpdfFile = $base . DIRECTORY_SEPARATOR . 'fpdf.php';
        if (!is_file($fpdfFile)) {
            $src = @file_get_contents('https://raw.githubusercontent.com/Setasign/FPDF/master/fpdf.php');
            if ($src === false || $src === '') {
                throw new RuntimeException('Téléchargement de FPDF impossible (vérifiez allow_url_fopen ou le pare-feu).');
            }
            if (@file_put_contents($fpdfFile, $src) === false) {
                throw new RuntimeException('Écriture du fichier FPDF temporaire impossible.');
            }
        }

        $fontDir = $base . DIRECTORY_SEPARATOR . 'font';
        if (!is_dir($fontDir) && !@mkdir($fontDir, 0700, true) && !is_dir($fontDir)) {
            throw new RuntimeException('Impossible de créer le dossier de polices FPDF temporaire.');
        }

        $fichiersFont = [
            'courier.php', 'courierb.php', 'courierbi.php', 'courieri.php',
            'helvetica.php', 'helveticab.php', 'helveticabi.php', 'helveticai.php',
            'symbol.php', 'times.php', 'timesb.php', 'timesbi.php', 'timesi.php', 'zapfdingbats.php',
        ];
        foreach ($fichiersFont as $nom) {
            $p = $fontDir . DIRECTORY_SEPARATOR . $nom;
            if (is_file($p)) {
                continue;
            }
            $url = 'https://raw.githubusercontent.com/Setasign/FPDF/master/font/' . rawurlencode($nom);
            $contenu = @file_get_contents($url);
            if ($contenu === false || $contenu === '') {
                throw new RuntimeException('Téléchargement de la police FPDF impossible : ' . $nom);
            }
            if (@file_put_contents($p, $contenu) === false) {
                throw new RuntimeException('Écriture de la police FPDF temporaire impossible : ' . $nom);
            }
        }

        if (!defined('FPDF_FONTPATH')) {
            define('FPDF_FONTPATH', $fontDir . DIRECTORY_SEPARATOR);
        }

        require_once $fpdfFile;
    }

    private static function pdfTronquer(string $texte, int $longueur): string
    {
        if (function_exists('mb_substr')) {
            return mb_substr($texte, 0, $longueur, 'UTF-8');
        }

        return substr($texte, 0, $longueur);
    }

    private static function pdfTexte(string $texte): string
    {
        $c = @mb_convert_encoding($texte, 'ISO-8859-1', 'UTF-8');
        if ($c !== false && $c !== '') {
            return $c;
        }

        return $texte;
    }

    /**
     * @param object $pdf Instance FPDF
     * @param list<float|int> $largeurs
     * @param list<string> $textes
     */
    private static function pdfLigneTableau(object $pdf, array $largeurs, array $textes, bool $entete): void
    {
        $h = $entete ? 8 : 7;
        if ($entete) {
            $pdf->SetFillColor(220, 220, 220);
        }
        $n = count($largeurs);
        for ($i = 0; $i < $n; $i++) {
            $pdf->Cell(
                (float) $largeurs[$i],
                (float) $h,
                $textes[$i] ?? '',
                1,
                0,
                'L',
                $entete
            );
        }
        $pdf->Ln();
    }

    // ——— Statistiques (back-office, corps HTML affiché depuis admin.php) ———

    /**
     * @return list<array{type:string,nb:int}>
     */
    public function statsAllergiesParType(): array
    {
        $stmt = $this->pdo->query('SELECT `type`, COUNT(*) AS nb FROM allergie GROUP BY `type` ORDER BY nb DESC, `type` ASC');

        return $this->statsMapperNb($stmt);
    }

    /**
     * @return list<array{niveau_danger:string,nb:int}>
     */
    public function statsAllergiesParNiveau(): array
    {
        $stmt = $this->pdo->query('SELECT niveau_danger, COUNT(*) AS nb FROM allergie GROUP BY niveau_danger ORDER BY nb DESC, niveau_danger ASC');

        return $this->statsMapperNbNiveau($stmt);
    }

    /**
     * @return list<array{type:string,niveau_danger:string,nb:int}>
     */
    public function statsAllergiesCroiseTypeNiveau(): array
    {
        $stmt = $this->pdo->query('SELECT `type`, niveau_danger, COUNT(*) AS nb FROM allergie GROUP BY `type`, niveau_danger ORDER BY `type` ASC, niveau_danger ASC');
        if ($stmt === false) {
            return [];
        }

        return array_map(static function (array $r): array {
            return [
                'type' => (string) ($r['type'] ?? ''),
                'niveau_danger' => (string) ($r['niveau_danger'] ?? ''),
                'nb' => (int) ($r['nb'] ?? 0),
            ];
        }, $stmt->fetchAll(PDO::FETCH_ASSOC));
    }

    /**
     * @return list<array{type_traitement:string,nb:int}>
     */
    public function statsTraitementsParType(): array
    {
        $stmt = $this->pdo->query('SELECT type_traitement, COUNT(*) AS nb FROM traitement GROUP BY type_traitement ORDER BY nb DESC, type_traitement ASC');

        return $this->statsMapperNbTypeTrait($stmt);
    }

    /**
     * @return list<array{dosage:string,nb:int}>
     */
    public function statsTraitementsParDosage(): array
    {
        $stmt = $this->pdo->query('SELECT dosage, COUNT(*) AS nb FROM traitement GROUP BY dosage ORDER BY nb DESC, dosage ASC');

        return $this->statsMapperNbDosage($stmt);
    }

    /**
     * @return list<array{type_traitement:string,dosage:string,nb:int}>
     */
    public function statsTraitementsCroiseTypeDosage(): array
    {
        $stmt = $this->pdo->query('SELECT type_traitement, dosage, COUNT(*) AS nb FROM traitement GROUP BY type_traitement, dosage ORDER BY type_traitement ASC, dosage ASC');
        if ($stmt === false) {
            return [];
        }

        return array_map(static function (array $r): array {
            return [
                'type_traitement' => (string) ($r['type_traitement'] ?? ''),
                'dosage' => (string) ($r['dosage'] ?? ''),
                'nb' => (int) ($r['nb'] ?? 0),
            ];
        }, $stmt->fetchAll(PDO::FETCH_ASSOC));
    }

    /**
     * @return list<array{type_traitement:string,dosage:string,nb:int}>
     */
    public function statsAssociationsParTypeEtDosage(): array
    {
        $sql = '
            SELECT t.type_traitement AS type_traitement, t.dosage AS dosage, COUNT(*) AS nb
            FROM allergie_traitement at
            INNER JOIN traitement t ON t.id_traitement = at.id_traitement
            GROUP BY t.type_traitement, t.dosage
            ORDER BY nb DESC, t.type_traitement ASC, t.dosage ASC
        ';
        $stmt = $this->pdo->query($sql);
        if ($stmt === false) {
            return [];
        }

        return array_map(static function (array $r): array {
            return [
                'type_traitement' => (string) ($r['type_traitement'] ?? ''),
                'dosage' => (string) ($r['dosage'] ?? ''),
                'nb' => (int) ($r['nb'] ?? 0),
            ];
        }, $stmt->fetchAll(PDO::FETCH_ASSOC));
    }

    /**
     * Associations : effectifs par type de traitement uniquement.
     *
     * @return list<array{type_traitement:string,nb:int}>
     */
    public function statsAssociationsParTypeSeul(): array
    {
        $sql = '
            SELECT t.type_traitement AS type_traitement, COUNT(*) AS nb
            FROM allergie_traitement at
            INNER JOIN traitement t ON t.id_traitement = at.id_traitement
            GROUP BY t.type_traitement
            ORDER BY nb DESC, t.type_traitement ASC
        ';
        $stmt = $this->pdo->query($sql);

        return $this->statsMapperNbTypeTrait($stmt);
    }

    /**
     * Associations : effectifs par dosage du traitement uniquement.
     *
     * @return list<array{dosage:string,nb:int}>
     */
    public function statsAssociationsParDosageSeul(): array
    {
        $sql = '
            SELECT t.dosage AS dosage, COUNT(*) AS nb
            FROM allergie_traitement at
            INNER JOIN traitement t ON t.id_traitement = at.id_traitement
            GROUP BY t.dosage
            ORDER BY nb DESC, t.dosage ASC
        ';
        $stmt = $this->pdo->query($sql);

        return $this->statsMapperNbDosage($stmt);
    }

    /**
     * Affiche le contenu principal des statistiques (HTML). Appelé depuis admin.php uniquement.
     *
     * @param array<string,mixed> $query typiquement $_GET (vues : vue_allergies, vue_traitements, vue_associations)
     */
    public function afficherCorpsStatistiquesHtml(array $query = []): void
    {
        $statsAlType = $this->statsAllergiesParType();
        $statsAlNiveau = $this->statsAllergiesParNiveau();
        $statsTrType = $this->statsTraitementsParType();
        $statsTrDosage = $this->statsTraitementsParDosage();
        $statsAssocType = $this->statsAssociationsParTypeSeul();
        $statsAssocDosage = $this->statsAssociationsParDosageSeul();

        $mapAlTypes = self::statsMapAllergieTypes();
        $mapAlNiveaux = self::statsMapAllergieNiveaux();
        $mapTrTypes = self::statsMapTraitementTypes();

        $vueAl = isset($query['vue_allergies']) ? strtolower(trim((string) $query['vue_allergies'])) : 'type';
        if (!in_array($vueAl, ['type', 'niveau'], true)) {
            $vueAl = 'type';
        }
        $vueTr = isset($query['vue_traitements']) ? strtolower(trim((string) $query['vue_traitements'])) : 'type';
        if (!in_array($vueTr, ['type', 'dosage'], true)) {
            $vueTr = 'type';
        }
        $vueAsc = isset($query['vue_associations']) ? strtolower(trim((string) $query['vue_associations'])) : 'type';
        if (!in_array($vueAsc, ['type', 'dosage'], true)) {
            $vueAsc = 'type';
        }

        echo '<h1>Statistiques</h1>';
        echo '<p class="intro">Camemberts compacts : pour chaque bloc (<strong>allergies</strong>, <strong>traitements</strong>, <strong>associations</strong>), choisissez la répartition affichée. Chaque part a une couleur distincte.</p>';

        // Allergies — choix type / niveau + un seul camembert compact
        echo '<section class="stat-section" aria-labelledby="stat-al"><h2 id="stat-al">Allergies</h2>';
        echo '<p class="muted">Données issues de la table <code>allergie</code>.</p>';
        echo '<div class="stat-choice-box" role="group" aria-labelledby="stat-al-choice-titre">';
        echo '<p id="stat-al-choice-titre" class="stat-choice-title">Afficher la répartition</p>';
        echo '<form class="stat-choice-form" method="get" action="admin.php#stat-al">';
        echo '<input type="hidden" name="page" value="statistiques">';
        self::statsEchoChampsCachesVues('allergies', $vueAl, $vueTr, $vueAsc);
        echo '<label class="stat-choice-item"><input type="radio" name="vue_allergies" value="type"' . ($vueAl === 'type' ? ' checked' : '') . ' onchange="sessionStorage.setItem(\'ecosave_stats_anchor\',\'stat-al\');this.form.submit()"> Par type</label>';
        echo '<label class="stat-choice-item"><input type="radio" name="vue_allergies" value="niveau"' . ($vueAl === 'niveau' ? ' checked' : '') . ' onchange="sessionStorage.setItem(\'ecosave_stats_anchor\',\'stat-al\');this.form.submit()"> Par niveau de danger</label>';
        echo '</form></div>';
        echo '<div class="stat-card stat-card--solo"><h3>' . ($vueAl === 'niveau' ? 'Par niveau de danger' : 'Par type') . '</h3>';
        if ($vueAl === 'niveau') {
            $piecesAl = [];
            foreach ($statsAlNiveau as $r) {
                $piecesAl[] = [
                    'label' => self::statsLibelleNiveauAllergie((string) ($r['niveau_danger'] ?? ''), $mapAlNiveaux),
                    'nb' => (int) ($r['nb'] ?? 0),
                ];
            }
        } else {
            $piecesAl = [];
            foreach ($statsAlType as $r) {
                $piecesAl[] = [
                    'label' => self::statsLibelleTypeAllergie((string) ($r['type'] ?? ''), $mapAlTypes),
                    'nb' => (int) ($r['nb'] ?? 0),
                ];
            }
        }
        self::statsEchoCamembert($piecesAl, true);
        echo '</div>';
        echo '</section>';

        // Traitements — choix type / dosage + un seul camembert
        echo '<section class="stat-section" aria-labelledby="stat-tr"><h2 id="stat-tr">Traitements</h2>';
        echo '<p class="muted">Données issues de la table <code>traitement</code>.</p>';
        echo '<div class="stat-choice-box" role="group" aria-labelledby="stat-tr-choice-titre">';
        echo '<p id="stat-tr-choice-titre" class="stat-choice-title">Afficher la répartition</p>';
        echo '<form class="stat-choice-form" method="get" action="admin.php#stat-tr">';
        echo '<input type="hidden" name="page" value="statistiques">';
        self::statsEchoChampsCachesVues('traitements', $vueAl, $vueTr, $vueAsc);
        echo '<label class="stat-choice-item"><input type="radio" name="vue_traitements" value="type"' . ($vueTr === 'type' ? ' checked' : '') . ' onchange="sessionStorage.setItem(\'ecosave_stats_anchor\',\'stat-tr\');this.form.submit()"> Par type</label>';
        echo '<label class="stat-choice-item"><input type="radio" name="vue_traitements" value="dosage"' . ($vueTr === 'dosage' ? ' checked' : '') . ' onchange="sessionStorage.setItem(\'ecosave_stats_anchor\',\'stat-tr\');this.form.submit()"> Par dosage</label>';
        echo '</form></div>';
        echo '<div class="stat-card stat-card--solo"><h3>' . ($vueTr === 'dosage' ? 'Par dosage' : 'Par type') . '</h3>';
        if ($vueTr === 'dosage') {
            $piecesTr = [];
            foreach ($statsTrDosage as $r) {
                $d = (string) ($r['dosage'] ?? '');
                $piecesTr[] = [
                    'label' => $d !== '' ? $d : '—',
                    'nb' => (int) ($r['nb'] ?? 0),
                ];
            }
        } else {
            $piecesTr = [];
            foreach ($statsTrType as $r) {
                $piecesTr[] = [
                    'label' => self::statsLibelleTypeTraitement((string) ($r['type_traitement'] ?? ''), $mapTrTypes),
                    'nb' => (int) ($r['nb'] ?? 0),
                ];
            }
        }
        self::statsEchoCamembert($piecesTr, true);
        echo '</div>';
        echo '</section>';

        // Associations — choix type / dosage + un seul camembert
        echo '<section class="stat-section" aria-labelledby="stat-as"><h2 id="stat-as">Associations</h2>';
        echo '<p class="muted">Liens dans <code>allergie_traitement</code>, agrégés par type ou par dosage du traitement lié.</p>';
        echo '<div class="stat-choice-box" role="group" aria-labelledby="stat-as-choice-titre">';
        echo '<p id="stat-as-choice-titre" class="stat-choice-title">Afficher la répartition</p>';
        echo '<form class="stat-choice-form" method="get" action="admin.php#stat-as">';
        echo '<input type="hidden" name="page" value="statistiques">';
        self::statsEchoChampsCachesVues('associations', $vueAl, $vueTr, $vueAsc);
        echo '<label class="stat-choice-item"><input type="radio" name="vue_associations" value="type"' . ($vueAsc === 'type' ? ' checked' : '') . ' onchange="sessionStorage.setItem(\'ecosave_stats_anchor\',\'stat-as\');this.form.submit()"> Par type de traitement</label>';
        echo '<label class="stat-choice-item"><input type="radio" name="vue_associations" value="dosage"' . ($vueAsc === 'dosage' ? ' checked' : '') . ' onchange="sessionStorage.setItem(\'ecosave_stats_anchor\',\'stat-as\');this.form.submit()"> Par dosage</label>';
        echo '</form></div>';
        echo '<div class="stat-card stat-card--solo">';
        if ($statsAssocType === [] && $statsAssocDosage === []) {
            echo '<p class="empty">Aucune association en base.</p>';
        } else {
            echo '<h3>' . ($vueAsc === 'dosage' ? 'Par dosage' : 'Par type de traitement') . '</h3>';
            if ($vueAsc === 'dosage') {
                $piecesAsc = [];
                foreach ($statsAssocDosage as $r) {
                    $d = (string) ($r['dosage'] ?? '');
                    $piecesAsc[] = [
                        'label' => $d !== '' ? $d : '—',
                        'nb' => (int) ($r['nb'] ?? 0),
                    ];
                }
                self::statsEchoCamembert($piecesAsc, true);
            } else {
                $piecesAsc = [];
                foreach ($statsAssocType as $r) {
                    $piecesAsc[] = [
                        'label' => self::statsLibelleTypeTraitement((string) ($r['type_traitement'] ?? ''), $mapTrTypes),
                        'nb' => (int) ($r['nb'] ?? 0),
                    ];
                }
                self::statsEchoCamembert($piecesAsc, true);
            }
        }
        echo '</div>';
        echo '</section>';
    }

    /**
     * Conserve les autres sélecteurs de vue lors d’un changement (GET).
     *
     * @param 'allergies'|'traitements'|'associations' $exclure
     */
    private static function statsEchoChampsCachesVues(string $exclure, string $vueAl, string $vueTr, string $vueAsc): void
    {
        if ($exclure !== 'allergies') {
            echo '<input type="hidden" name="vue_allergies" value="' . htmlspecialchars($vueAl, ENT_QUOTES, 'UTF-8') . '">';
        }
        if ($exclure !== 'traitements') {
            echo '<input type="hidden" name="vue_traitements" value="' . htmlspecialchars($vueTr, ENT_QUOTES, 'UTF-8') . '">';
        }
        if ($exclure !== 'associations') {
            echo '<input type="hidden" name="vue_associations" value="' . htmlspecialchars($vueAsc, ENT_QUOTES, 'UTF-8') . '">';
        }
    }

    /**
     * Couleur distincte pour la part d’index i parmi n parts (répartition uniforme sur le cercle des teintes).
     */
    private static function statsCouleurPart(int $i, int $n): string
    {
        if ($n < 1) {
            $n = 1;
        }
        $hue = (int) round(fmod($i * (360.0 / (float) $n) + 12.0, 360.0));

        return sprintf('hsl(%d, 72%%, 44%%)', $hue);
    }

    /**
     * Affiche un camembert SVG + légende.
     *
     * @param list<array{label:string,nb:int}> $pieces
     */
    private static function statsEchoCamembert(array $pieces, bool $svgCompact = false): void
    {
        $total = 0;
        foreach ($pieces as $p) {
            $total += max(0, (int) ($p['nb'] ?? 0));
        }
        if ($total <= 0) {
            echo '<p class="empty">Aucune donnée.</p>';

            return;
        }

        $cx = 50.0;
        $cy = 50.0;
        $R = 46.0;

        $nonZero = [];
        foreach ($pieces as $p) {
            if ((int) ($p['nb'] ?? 0) > 0) {
                $nonZero[] = $p;
            }
        }
        if ($nonZero === []) {
            echo '<p class="empty">Aucune donnée.</p>';

            return;
        }

        $nParts = count($nonZero);
        $svgClass = 'stat-pie-svg' . ($svgCompact ? ' stat-pie-svg--compact' : '');
        // Taille fixe en pixels (attributs + style) : certains navigateurs ignorent mal le seul CSS sur les SVG.
        // Taille « confort » : lisible sans occuper toute la page (compact = page statistiques).
        $svgPx = $svgCompact ? 112 : 128;

        echo '<div class="stat-pie-wrap"><div class="stat-pie-chart" role="presentation">';
        echo '<svg viewBox="0 0 100 100" width="' . $svgPx . '" height="' . $svgPx . '" class="' . htmlspecialchars($svgClass, ENT_QUOTES, 'UTF-8') . '" xmlns="http://www.w3.org/2000/svg" aria-hidden="true" style="width:' . $svgPx . 'px;height:' . $svgPx . 'px;max-width:100%;display:block;flex-shrink:0">';

        if ($nParts === 1) {
            $c0 = self::statsCouleurPart(0, 1);
            echo '<circle cx="' . $cx . '" cy="' . $cy . '" r="' . $R . '" fill="' . htmlspecialchars($c0, ENT_QUOTES, 'UTF-8') . '"/>';
        } else {
            $theta = -M_PI / 2;
            $idx = 0;
            foreach ($nonZero as $p) {
                $nb = (int) ($p['nb'] ?? 0);
                if ($nb <= 0) {
                    continue;
                }
                $slice = ($nb / $total) * 2.0 * M_PI;
                $c = self::statsCouleurPart($idx, $nParts);
                $idx++;
                if ($slice >= 2.0 * M_PI - 1e-9) {
                    echo '<circle cx="' . $cx . '" cy="' . $cy . '" r="' . $R . '" fill="' . htmlspecialchars($c, ENT_QUOTES, 'UTF-8') . '"/>';
                    break;
                }
                $x0 = $cx + $R * cos($theta);
                $y0 = $cy + $R * sin($theta);
                $theta1 = $theta + $slice;
                $x1 = $cx + $R * cos($theta1);
                $y1 = $cy + $R * sin($theta1);
                $large = $slice > M_PI ? 1 : 0;
                $d = sprintf(
                    'M %.5f %.5f L %.5f %.5f A %.5f %.5f 0 %d 1 %.5f %.5f Z',
                    $cx,
                    $cy,
                    $x0,
                    $y0,
                    $R,
                    $R,
                    $large,
                    $x1,
                    $y1
                );
                echo '<path d="' . htmlspecialchars($d, ENT_QUOTES, 'UTF-8') . '" fill="' . htmlspecialchars($c, ENT_QUOTES, 'UTF-8') . '"/>';
                $theta = $theta1;
            }
        }

        echo '</svg></div>';
        echo '<ul class="stat-pie-legend">';
        $idx = 0;
        foreach ($nonZero as $p) {
            $nb = (int) ($p['nb'] ?? 0);
            if ($nb <= 0) {
                continue;
            }
            $c = self::statsCouleurPart($idx, $nParts);
            $idx++;
            $lab = htmlspecialchars((string) ($p['label'] ?? ''), ENT_QUOTES, 'UTF-8');
            $pct = round(100.0 * $nb / $total, 1);
            echo '<li><span class="stat-pie-swatch" style="background:' . htmlspecialchars($c, ENT_QUOTES, 'UTF-8') . '"></span>';
            echo '<span class="stat-pie-lab">' . $lab . '</span> ';
            echo '<span class="stat-pie-val">' . $nb . ' (' . $pct . '%)</span></li>';
        }
        echo '</ul></div>';
    }

    /** @param PDOStatement|false $stmt */
    private function statsMapperNb($stmt): array
    {
        if ($stmt === false) {
            return [];
        }

        return array_map(static function (array $r): array {
            return [
                'type' => (string) ($r['type'] ?? ''),
                'nb' => (int) ($r['nb'] ?? 0),
            ];
        }, $stmt->fetchAll(PDO::FETCH_ASSOC));
    }

    /** @param PDOStatement|false $stmt */
    private function statsMapperNbNiveau($stmt): array
    {
        if ($stmt === false) {
            return [];
        }

        return array_map(static function (array $r): array {
            return [
                'niveau_danger' => (string) ($r['niveau_danger'] ?? ''),
                'nb' => (int) ($r['nb'] ?? 0),
            ];
        }, $stmt->fetchAll(PDO::FETCH_ASSOC));
    }

    /** @param PDOStatement|false $stmt */
    private function statsMapperNbTypeTrait($stmt): array
    {
        if ($stmt === false) {
            return [];
        }

        return array_map(static function (array $r): array {
            return [
                'type_traitement' => (string) ($r['type_traitement'] ?? ''),
                'nb' => (int) ($r['nb'] ?? 0),
            ];
        }, $stmt->fetchAll(PDO::FETCH_ASSOC));
    }

    /** @param PDOStatement|false $stmt */
    private function statsMapperNbDosage($stmt): array
    {
        if ($stmt === false) {
            return [];
        }

        return array_map(static function (array $r): array {
            return [
                'dosage' => (string) ($r['dosage'] ?? ''),
                'nb' => (int) ($r['nb'] ?? 0),
            ];
        }, $stmt->fetchAll(PDO::FETCH_ASSOC));
    }

    /** @return array<string,string> */
    private static function statsMapAllergieTypes(): array
    {
        return [
            'medicament' => 'Médicament',
            'environnement' => 'Environnementale',
            'alimentaire' => 'Alimentaire',
            'contact' => 'De contact (peau)',
            'animale' => 'Animale',
            'insecte' => 'Piqûres d’insectes',
            'autre' => 'Autre',
        ];
    }

    /** @return array<string,string> */
    private static function statsMapAllergieNiveaux(): array
    {
        return [
            'tres_leger' => 'Très léger',
            'leger' => 'Léger',
            'modere' => 'Modéré',
            'eleve' => 'Élevé',
            'critique' => 'Critique',
        ];
    }

    /** @return array<string,string> */
    private static function statsMapTraitementTypes(): array
    {
        return [
            'antihistaminique' => 'Antihistaminique',
            'corticoide' => 'Corticoïde',
            'bronchodilatateur' => 'Bronchodilatateur',
            'decongestionnant' => 'Décongestionnant',
            'adrenaline' => 'Adrénaline (urgence)',
            'immunotherapie' => 'Immunothérapie',
            'autre' => 'Autre',
        ];
    }

    private static function statsAllergieTypeCle(string $brut): string
    {
        $t = str_replace(['é', 'è', 'ê'], 'e', mb_strtolower(trim($brut), 'UTF-8'));
        if ($t === 'environnementale') {
            return 'environnement';
        }
        $connus = [
            'medicament', 'environnement', 'alimentaire', 'contact', 'animale', 'insecte', 'autre',
        ];
        if (in_array($t, $connus, true)) {
            return $t;
        }
        if (mb_strpos($t, 'contact') !== false) {
            return 'contact';
        }
        if (mb_strpos($t, 'insect') !== false || mb_strpos($t, 'piqu') !== false) {
            return 'insecte';
        }
        if (mb_strpos($t, 'animal') !== false) {
            return 'animale';
        }

        return '';
    }

    private static function statsAllergieNiveauCle(string $brut): string
    {
        $n = str_replace(['é', 'è', 'ê'], 'e', mb_strtolower(trim($brut), 'UTF-8'));
        $n = str_replace([' ', '-'], '_', $n);
        if ($n === 'tresleger') {
            return 'tres_leger';
        }
        $allowed = ['tres_leger', 'leger', 'modere', 'eleve', 'critique'];
        if (in_array($n, $allowed, true)) {
            return $n;
        }

        return '';
    }

    private static function statsTraitementTypeCle(string $brut): string
    {
        $s = str_replace(['é', 'è', 'ê'], 'e', mb_strtolower(trim($brut), 'UTF-8'));
        $connus = [
            'antihistaminique', 'corticoide', 'bronchodilatateur', 'decongestionnant',
            'adrenaline', 'immunotherapie', 'autre',
        ];
        if (in_array($s, $connus, true)) {
            return $s;
        }
        if (mb_strpos($s, 'antihist') !== false) {
            return 'antihistaminique';
        }
        if (mb_strpos($s, 'cortic') !== false) {
            return 'corticoide';
        }
        if (mb_strpos($s, 'broncho') !== false) {
            return 'bronchodilatateur';
        }
        if (mb_strpos($s, 'decongest') !== false) {
            return 'decongestionnant';
        }
        if (mb_strpos($s, 'adrenal') !== false || mb_strpos($s, 'epineph') !== false) {
            return 'adrenaline';
        }
        if (mb_strpos($s, 'immuno') !== false) {
            return 'immunotherapie';
        }

        return '';
    }

    /**
     * @param array<string,string> $map
     */
    private static function statsLibelleTypeAllergie(string $brut, array $map): string
    {
        $k = self::statsAllergieTypeCle($brut);

        return $k !== '' ? ($map[$k] ?? $k) : ($brut !== '' ? $brut : '—');
    }

    /**
     * @param array<string,string> $map
     */
    private static function statsLibelleNiveauAllergie(string $brut, array $map): string
    {
        $k = self::statsAllergieNiveauCle($brut);

        return $k !== '' ? ($map[$k] ?? $k) : ($brut !== '' ? $brut : '—');
    }

    /**
     * @param array<string,string> $map
     */
    private static function statsLibelleTypeTraitement(string $brut, array $map): string
    {
        $k = self::statsTraitementTypeCle($brut);

        return $k !== '' ? ($map[$k] ?? $k) : ($brut !== '' ? $brut : '—');
    }
}


