<?php
declare(strict_types=1);
ob_start();
session_start();

if (($_SESSION['role'] ?? '') !== 'admin') {
    header('Location: login.php');
    exit;
}

if (!function_exists('h')) {
    function h(?string $s): string
    {
        return htmlspecialchars((string) $s, ENT_QUOTES, 'UTF-8');
    }
}

require_once __DIR__ . '/../controller/metier.php';
Metier::repondreExportPdfSiDemande('traitements');
require_once __DIR__ . '/../controller/traitementcontroller.php';

$active = 'traitements';

$controller = TraitementController::getInstance();
extract($controller->traiterRequetePageTraitements(), EXTR_OVERWRITE);

$metier = new Metier();
$triTraitement = Metier::triTraitementDepuisGet($_GET);
$rows = $metier->rechercherTraitements(Metier::termeBarreDepuisGet($_GET), $triTraitement);

$pdfQueryTr = ['export' => 'pdf', 'tri' => $triTraitement];
if (Metier::termeBarreDepuisGet($_GET) !== '') {
    $pdfQueryTr['q'] = Metier::termeBarreDepuisGet($_GET);
}
$hrefPdfTraitements = 'traitements.php?' . http_build_query($pdfQueryTr);

$mapTypesTrait = [
    'antihistaminique' => 'Antihistaminique',
    'corticoide' => 'Corticoïde',
    'bronchodilatateur' => 'Bronchodilatateur',
    'decongestionnant' => 'Décongestionnant',
    'adrenaline' => 'Adrénaline (urgence)',
    'immunotherapie' => 'Immunothérapie',
    'autre' => 'Autre',
];

function traitements_liste_type_cle(string $brut): string
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

$pageTitle = 'Gestion des Traitements';
require __DIR__ . '/partials/header.php';
?>

<style>
    /* Styling for glassmorphic elements and tables in modern palette */
    .admin-sub-nav {
        display: flex;
        gap: 12px;
        margin-bottom: 20px;
    }
    .sub-nav-btn {
        padding: 8px 16px;
        border-radius: 20px;
        text-decoration: none;
        font-weight: 600;
        font-size: 14px;
        background: rgba(255,255,255,0.2);
        color: #ffffff;
        border: 1px solid rgba(255,255,255,0.3);
        transition: all 0.25s ease;
    }
    .sub-nav-btn:hover, .sub-nav-btn.active {
        background: #ffffff;
        color: #065f46;
        border-color: #ffffff;
    }
    .crud-card {
        background: rgba(255, 255, 255, 0.95);
        border: 1px solid rgba(178, 242, 187, 0.25);
        padding: 24px;
        border-radius: 16px;
        box-shadow: 0 10px 25px rgba(0, 0, 0, 0.08);
        backdrop-filter: blur(10px);
        color: #1f2937;
        margin-bottom: 24px;
    }
    .page-head {
        display: flex;
        align-items: center;
        justify-content: space-between;
        margin-bottom: 24px;
        flex-wrap: wrap;
        gap: 12px;
    }
    .page-head h1 {
        color: #065f46;
        font-size: 24px;
        margin: 0;
        text-shadow: none;
    }
    .crud-btn {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        padding: 10px 18px;
        border-radius: 24px;
        text-decoration: none;
        font-weight: 600;
        font-size: 14px;
        border: 1px solid rgba(0,0,0,0.1);
        background: #ffffff;
        color: #374151;
        transition: all 0.25s cubic-bezier(0.4, 0, 0.2, 1);
        cursor: pointer;
    }
    .crud-btn:hover {
        transform: translateY(-2px);
        box-shadow: 0 6px 12px rgba(0,0,0,0.06);
        background: #fdfdfd;
        border-color: #10b981;
    }
    .crud-btn.primary {
        background: linear-gradient(135deg, #10b981 0%, #059669 100%);
        color: #ffffff;
        border: none;
    }
    .crud-btn.primary:hover {
        background: linear-gradient(135deg, #059669 0%, #047857 100%);
        box-shadow: 0 8px 16px rgba(16, 185, 129, 0.25);
    }
    .crud-btn.danger {
        background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%);
        color: #ffffff;
        border: none;
    }
    .crud-btn.danger:hover {
        background: linear-gradient(135deg, #dc2626 0%, #b91c1c 100%);
        box-shadow: 0 8px 16px rgba(239, 68, 68, 0.25);
    }
    .crud-btn.sm {
        padding: 6px 12px;
        font-size: 12px;
        border-radius: 16px;
    }
    .search-panel {
        background: #f9fafb;
        border: 1px solid #e5e7eb;
        border-radius: 12px;
        padding: 16px;
        margin-bottom: 24px;
    }
    .search-form {
        display: flex;
        flex-wrap: wrap;
        gap: 12px;
        align-items: flex-end;
    }
    .form-group {
        display: flex;
        flex-direction: column;
        gap: 6px;
        flex: 1;
        min-width: 220px;
    }
    .form-group label {
        font-size: 13px;
        font-weight: 600;
        color: #374151;
    }
    .form-group input {
        width: 100%;
        padding: 10px 14px;
        border: 1px solid #d1d5db;
        border-radius: 8px;
        font-size: 14px;
        background: #ffffff;
        color: #1f2937;
        transition: border-color 0.2s, box-shadow 0.2s;
    }
    .form-group input:focus {
        outline: none;
        border-color: #10b981;
        box-shadow: 0 0 0 3px rgba(16, 185, 129, 0.15);
    }
    .table-container {
        overflow-x: auto;
        border: 1px solid #e5e7eb;
        border-radius: 12px;
        background: #ffffff;
    }
    .crud-table {
        width: 100%;
        border-collapse: collapse;
        text-align: left;
        font-size: 14px;
    }
    .crud-table th {
        background: #f3f4f6;
        padding: 14px 16px;
        color: #374151;
        font-weight: 600;
        border-bottom: 1px solid #e5e7eb;
    }
    .crud-table td {
        padding: 14px 16px;
        border-bottom: 1px solid #e5e7eb;
        color: #4b5563;
    }
    .crud-table tr:last-child td {
        border-bottom: none;
    }
    .crud-table tr:hover {
        background: #f9fafb;
    }
    .cell-clip {
        max-width: 240px;
        overflow: hidden;
        text-overflow: ellipsis;
        white-space: nowrap;
    }
    .msg {
        padding: 12px 16px;
        border-radius: 8px;
        margin-bottom: 20px;
        font-size: 14px;
        font-weight: 500;
    }
    .msg-success {
        background: #d1fae5;
        color: #065f46;
        border: 1px solid #a7f3d0;
    }
    .msg-error {
        background: #fee2e2;
        color: #991b1b;
        border: 1px solid #fca5a5;
    }
    .msg-error ul {
        margin: 6px 0 0 16px;
        padding: 0;
    }
    .empty {
        text-align: center;
        padding: 32px;
        color: #9ca3af;
        font-style: italic;
    }

    /* Modal Styling */
    .confirm-modal {
        position: fixed;
        inset: 0;
        background: rgba(0, 0, 0, 0.5);
        backdrop-filter: blur(4px);
        display: flex;
        align-items: center;
        justify-content: center;
        z-index: 1000;
    }
    .confirm-modal[hidden] {
        display: none !important;
    }
    .confirm-modal__dialog {
        background: #ffffff;
        border-radius: 16px;
        padding: 24px;
        max-width: 400px;
        width: 90%;
        text-align: center;
        box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1);
        border: 1px solid #e5e7eb;
    }
    .confirm-modal__icon {
        font-size: 40px;
        margin-bottom: 12px;
    }
    .confirm-modal__title {
        font-size: 18px;
        font-weight: 700;
        color: #111827;
        margin-bottom: 8px;
        text-shadow: none;
    }
    .confirm-modal__text {
        font-size: 14px;
        color: #4b5563;
        margin-bottom: 20px;
    }
    .confirm-modal__actions {
        display: flex;
        justify-content: center;
        gap: 12px;
    }
</style>

<div class="admin-sub-nav">
    <a class="sub-nav-btn" href="allergier_admin.php">🏠 Tableau de bord</a>
    <a class="sub-nav-btn" href="allergies.php">🌿 Allergies</a>
    <a class="sub-nav-btn active" href="traitements.php">💊 Traitements</a>
    <a class="sub-nav-btn" href="associations.php">🔗 Associations</a>
    <a class="sub-nav-btn" href="allergier_admin.php?page=statistiques">📊 Statistiques</a>
</div>

<div class="crud-card">
    <div class="page-head">
        <h1>Traitements</h1>
        <div style="display:flex;gap:8px;flex-wrap:wrap;align-items:center;">
            <a href="?export=pdf&q=<?= rawurlencode(Metier::termeBarreDepuisGet($_GET)) ?>&tri=<?= h($triTraitement) ?>" class="btn-export-pdf" target="_blank">📄 Export PDF</a>
            <a class="crud-btn primary" href="traitement_form.php?action=ajouter">➕ Ajouter un traitement</a>
        </div>
    </div>

    <?php if (isset($_GET['pdf_err'])): ?>
        <div class="msg msg-error"><?php echo h((string) $_GET['pdf_err']); ?></div>
    <?php endif; ?>
    <?php if ($message !== ''): ?><div class="msg msg-success"><?php echo h($message); ?></div><?php endif; ?>
    <?php if ($erreurs): ?>
        <div class="msg msg-error"><ul><?php foreach ($erreurs as $e): ?><li><?php echo h($e); ?></li><?php endforeach; ?></ul></div>
    <?php endif; ?>

    <div class="search-panel">
        <form method="get" action="traitements.php" class="search-form">
            <input type="hidden" name="tri" value="<?php echo h($triTraitement); ?>">
            <div class="form-group">
                <label for="q">Recherche multicritère</label>
                <input id="q" type="search" name="q" value="<?php echo h((string) ($_GET['q'] ?? '')); ?>" placeholder="Rechercher par nom, type, dosage, durée, effets secondaires..." autocomplete="off">
            </div>
            <div style="display:flex; gap:8px;">
                <button type="submit" class="crud-btn primary">Rechercher</button>
                <a class="crud-btn" href="traitements.php">Réinitialiser</a>
            </div>
        </form>
        
        <p style="margin: 16px 0 8px; font-size:13px; font-weight:600; color:#374151;">Trier par :</p>
        <div style="display:flex; flex-wrap:wrap; gap:8px;">
            <?php
            $qTr = (string) ($_GET['q'] ?? '');
            $liensTriTr = [
                'nom' => 'Nom alphabétique',
                'type_traitement' => 'Catégorie type',
                'dosage' => 'Dosage prescrit',
            ];
            foreach ($liensTriTr as $cle => $lib):
                $params = ['tri' => $cle];
                if ($qTr !== '') {
                    $params['q'] = $qTr;
                }
                $href = 'traitements.php?' . http_build_query($params);
                $isOn = $triTraitement === $cle;
                ?>
                <a class="crud-btn sm <?php echo $isOn ? 'primary' : ''; ?>" href="<?php echo h($href); ?>"><?php echo h($lib); ?></a>
            <?php endforeach; ?>
        </div>
    </div>

    <div class="table-container">
        <table class="crud-table">
            <thead>
                <tr>
                    <th>Nom</th>
                    <th>Type</th>
                    <th>Dosage</th>
                    <th>Durée</th>
                    <th>Effets secondaires</th>
                    <th style="text-align:center;">Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php if (!$rows): ?>
                    <tr><td colspan="6" class="empty">Aucun traitement enregistré.</td></tr>
                <?php else: foreach ($rows as $r): ?>
                    <tr>
                        <td><strong><?php echo h((string) ($r['nom'] ?? '')); ?></strong></td>
                        <td><?php
                            $rawTr = (string) ($r['type_traitement'] ?? '');
                            $kTr = traitements_liste_type_cle($rawTr);
                            echo h($kTr !== '' ? ($mapTypesTrait[$kTr] ?? $kTr) : $rawTr);
                        ?></td>
                        <td><?php echo h((string) ($r['dosage'] ?? '')); ?></td>
                        <td><?php echo h((string) ($r['duree'] ?? '')); ?></td>
                        <td class="cell-clip" title="<?php echo h((string) ($r['effets_secondaires'] ?? '')); ?>"><?php echo h((string) ($r['effets_secondaires'] ?? '')); ?></td>
                        <td style="text-align:center; white-space:nowrap; display: flex; gap: 8px; justify-content: center;">
                            <a class="crud-btn sm" href="traitement_form.php?action=editer&amp;id=<?php echo (int) ($r['id_traitement'] ?? 0); ?>">✏️ Modifier</a>
                            <form method="post" action="traitements.php?action=supprimer" style="display:inline-block;">
                                <input type="hidden" name="id_traitement" value="<?php echo (int) ($r['id_traitement'] ?? 0); ?>">
                                <button type="submit" class="crud-btn sm danger" data-confirm="Voulez-vous vraiment supprimer le traitement « <?php echo h((string) ($r['nom'] ?? '')); ?> » ? Cette action est irréversible.">🗑️ Supprimer</button>
                            </form>
                        </td>
                    </tr>
                <?php endforeach; endif; ?>
            </tbody>
        </table>
    </div>
</div>

<script src="../assets/js/allergier.js" defer></script>

<?php require __DIR__ . '/partials/footer.php'; ?>
