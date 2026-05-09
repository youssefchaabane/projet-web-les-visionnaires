<?php
declare(strict_types=1);
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

require_once __DIR__ . '/../Gestion_pub/controller/PublicationController.php';
require_once __DIR__ . '/../Gestion_pub/controller/CommentaireController.php';

$pubController = new PublicationController();
$comController = new CommentaireController();

$msgSuccess = '';
$msgError = '';

// Handle POST actions for CRUD
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';

    if ($action === 'ajouter_pub') {
        $titre = trim((string) ($_POST['titre'] ?? ''));
        $contenu = trim((string) ($_POST['contenu'] ?? ''));
        $mediaUrl = trim((string) ($_POST['media_url'] ?? ''));
        $idUser = (int) ($_SESSION['user_id'] ?? 1);

        $res = $pubController->ajouterPublication([
            'titre' => $titre,
            'contenu' => $contenu,
            'media_url' => $mediaUrl,
            'id_user' => $idUser
        ]);

        if ($res['success'] ?? false) {
            $msgSuccess = "La publication a été ajoutée avec succès !";
        } else {
            $msgError = $res['message'] ?? "Erreur lors de l'ajout.";
        }
    } elseif ($action === 'modifier_pub') {
        $idPub = (int) ($_POST['id_pub'] ?? 0);
        $titre = trim((string) ($_POST['titre'] ?? ''));
        $contenu = trim((string) ($_POST['contenu'] ?? ''));
        $mediaUrl = trim((string) ($_POST['media_url'] ?? ''));
        $idUser = (int) ($_POST['id_user'] ?? 1);

        $res = $pubController->modifierPublication($idPub, [
            'titre' => $titre,
            'contenu' => $contenu,
            'media_url' => $mediaUrl,
            'id_user' => $idUser
        ]);

        if ($res['success'] ?? false) {
            $msgSuccess = "La publication a été modifiée avec succès !";
        } else {
            $msgError = $res['message'] ?? "Erreur lors de la modification.";
        }
    } elseif ($action === 'supprimer_pub') {
        $idPub = (int) ($_POST['id_pub'] ?? 0);
        $res = $pubController->supprimerPublication($idPub);

        if ($res['success'] ?? false) {
            $msgSuccess = "La publication a été supprimée.";
        } else {
            $msgError = $res['message'] ?? "Erreur lors de la suppression.";
        }
    } elseif ($action === 'supprimer_com') {
        $idCom = (int) ($_POST['id_commentaire'] ?? 0);
        $res = $comController->supprimerCommentaire($idCom);

        if ($res['success'] ?? false) {
            $msgSuccess = "Le commentaire a été supprimé.";
        } else {
            $msgError = $res['message'] ?? "Erreur lors de la suppression.";
        }
    } elseif ($action === 'valider_signalement') {
        $index = (int) ($_POST['index'] ?? 0);
        $res = $pubController->validerSignalement($index);

        if ($res['success'] ?? false) {
            $msgSuccess = "Le signalement a été validé (élément supprimé).";
        } else {
            $msgError = $res['message'] ?? "Erreur de validation.";
        }
    } elseif ($action === 'rejeter_signalement') {
        $index = (int) ($_POST['index'] ?? 0);
        $res = $pubController->rejeterSignalement($index);

        if ($res['success'] ?? false) {
            $msgSuccess = "Le signalement a été rejeté.";
        } else {
            $msgError = $res['message'] ?? "Erreur de rejet.";
        }
    }
}

// Fetch stats
$publications = $pubController->afficherPublications();
$totalPubs = count($publications);
$totalComs = $comController->countAllCommentaires();
$signalements = $pubController->listerSignalements();
$totalSignalements = count($signalements);

$pageTitle = 'Administration des Publications';
require __DIR__ . '/partials/header.php';
?>

<style>
    /* Premium dark-green glassmorphic aesthetics */
    .admin-card {
        background: rgba(255, 255, 255, 0.1);
        backdrop-filter: blur(12px);
        -webkit-backdrop-filter: blur(12px);
        border: 1px solid rgba(255, 255, 255, 0.15);
        border-radius: 16px;
        padding: 24px;
        box-shadow: 0 8px 32px rgba(0, 0, 0, 0.2);
        color: #ffffff;
        margin-bottom: 24px;
    }

    .admin-card h2 {
        color: #b2f2bb;
        font-size: 22px;
        margin-bottom: 16px;
        display: flex;
        align-items: center;
        gap: 8px;
    }

    /* Sub Navigation */
    .admin-sub-nav {
        display: flex;
        flex-wrap: wrap;
        gap: 12px;
        margin-bottom: 24px;
    }

    .sub-nav-btn {
        padding: 10px 20px;
        border-radius: 999px;
        text-decoration: none;
        font-weight: 600;
        font-size: 14px;
        background: rgba(178, 242, 187, 0.1);
        color: #b2f2bb;
        border: 1px solid rgba(178, 242, 187, 0.25);
        transition: all 0.25s ease;
        cursor: pointer;
    }

    .sub-nav-btn:hover, .sub-nav-btn.active {
        background: #b2f2bb;
        color: #0a3d2a;
        border-color: #b2f2bb;
        transform: translateY(-2px);
    }

    /* Statistics Widgets */
    .stats-row {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
        gap: 20px;
        margin-bottom: 24px;
    }

    .stat-widget {
        background: rgba(255, 255, 255, 0.05);
        border: 1px solid rgba(255, 255, 255, 0.08);
        border-radius: 12px;
        padding: 20px;
        display: flex;
        align-items: center;
        gap: 16px;
        transition: all 0.3s ease;
    }

    .stat-widget:hover {
        background: rgba(255, 255, 255, 0.08);
        border-color: rgba(178, 242, 187, 0.3);
        transform: translateY(-2px);
    }

    .stat-widget-icon {
        width: 48px;
        height: 48px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 20px;
        background: rgba(178, 242, 187, 0.1);
        color: #b2f2bb;
    }

    .stat-widget-info h3 {
        font-size: 13px;
        color: #aaa;
        margin: 0 0 4px 0;
        font-weight: 500;
    }

    .stat-widget-info .num {
        font-size: 24px;
        font-weight: 700;
        color: #ffffff;
        margin: 0;
    }

    /* Filter & Search Controls */
    .controls-row {
        display: flex;
        gap: 12px;
        margin-bottom: 20px;
        flex-wrap: wrap;
        align-items: center;
    }

    .controls-row input, .controls-row select {
        padding: 10px 16px;
        border: 1px solid rgba(178, 242, 187, 0.3);
        border-radius: 10px;
        background: rgba(255, 255, 255, 0.1);
        color: #fff;
        font-size: 14px;
        outline: none;
    }

    .controls-row input {
        flex: 1;
        min-width: 200px;
    }

    /* Responsive Table */
    .table-responsive {
        width: 100%;
        overflow-x: auto;
        background: rgba(255, 255, 255, 0.02);
        border: 1px solid rgba(255, 255, 255, 0.08);
        border-radius: 12px;
    }

    .custom-table {
        width: 100%;
        border-collapse: collapse;
        text-align: left;
        color: #ffffff;
    }

    .custom-table th, .custom-table td {
        padding: 14px 18px;
        border-bottom: 1px solid rgba(255, 255, 255, 0.05);
        font-size: 14px;
    }

    .custom-table th {
        background: rgba(178, 242, 187, 0.05);
        color: #b2f2bb;
        font-weight: 600;
    }

    .custom-table tr:hover {
        background: rgba(255, 255, 255, 0.03);
    }

    /* Form Fields */
    .form-group {
        margin-bottom: 16px;
    }

    .form-group label {
        display: block;
        font-size: 13px;
        font-weight: 600;
        color: #b2f2bb;
        margin-bottom: 6px;
    }

    .form-group input, .form-group textarea, .form-group select {
        width: 100%;
        padding: 12px 16px;
        background: rgba(255, 255, 255, 0.05);
        border: 1px solid rgba(178, 242, 187, 0.3);
        border-radius: 10px;
        color: #ffffff;
        outline: none;
        transition: all 0.3s ease;
        font-size: 14px;
    }

    .form-group input:focus, .form-group textarea:focus {
        border-color: #10b981;
        background: rgba(255, 255, 255, 0.1);
        box-shadow: 0 0 10px rgba(16, 185, 129, 0.2);
    }

    /* Buttons */
    .btn-action {
        padding: 8px 14px;
        border-radius: 8px;
        font-size: 13px;
        font-weight: 600;
        cursor: pointer;
        transition: all 0.25s ease;
        border: none;
        display: inline-flex;
        align-items: center;
        gap: 6px;
        text-decoration: none;
    }

    .btn-primary {
        background: linear-gradient(135deg, #10b981, #059669);
        color: #fff;
    }

    .btn-primary:hover {
        background: linear-gradient(135deg, #059669, #047857);
        transform: translateY(-1px);
        box-shadow: 0 4px 12px rgba(16, 185, 129, 0.3);
    }

    .btn-danger {
        background: rgba(239, 68, 68, 0.2);
        color: #f87171;
        border: 1px solid rgba(239, 68, 68, 0.3);
    }

    .btn-danger:hover {
        background: #ef4444;
        color: #fff;
    }

    .btn-info {
        background: rgba(59, 130, 246, 0.2);
        color: #60a5fa;
        border: 1px solid rgba(59, 130, 246, 0.3);
    }

    .btn-info:hover {
        background: #3b82f6;
        color: #fff;
    }

    .btn-ai {
        background: rgba(168, 85, 247, 0.2);
        color: #c084fc;
        border: 1px solid rgba(168, 85, 247, 0.3);
    }

    .btn-ai:hover {
        background: #a855f7;
        color: #fff;
    }

    /* Alerts */
    .alert-box {
        padding: 12px 18px;
        border-radius: 10px;
        font-size: 14px;
        font-weight: 600;
        margin-bottom: 20px;
        display: flex;
        align-items: center;
        gap: 8px;
    }

    .alert-success {
        background: rgba(16, 185, 129, 0.15);
        color: #34d399;
        border: 1px solid rgba(16, 185, 129, 0.3);
    }

    .alert-danger {
        background: rgba(239, 68, 68, 0.15);
        color: #f87171;
        border: 1px solid rgba(239, 68, 68, 0.3);
    }

    .admin-section {
        display: none;
    }

    .admin-section.active {
        display: block;
        animation: fadeIn 0.3s ease;
    }

    @keyframes fadeIn {
        from { opacity: 0; transform: translateY(10px); }
        to { opacity: 1; transform: translateY(0); }
    }

    /* Modal for AI / Details view */
    .custom-modal {
        display: none;
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: rgba(0, 0, 0, 0.6);
        z-index: 1000;
        align-items: center;
        justify-content: center;
        backdrop-filter: blur(6px);
    }

    .custom-modal-content {
        background: #0a1914;
        border: 1px solid rgba(178, 242, 187, 0.25);
        border-radius: 16px;
        width: 90%;
        max-width: 600px;
        padding: 24px;
        box-shadow: 0 10px 40px rgba(0,0,0,0.5);
        color: #fff;
        position: relative;
    }

    .close-modal {
        position: absolute;
        top: 16px;
        right: 20px;
        font-size: 24px;
        color: #aaa;
        cursor: pointer;
    }

    .close-modal:hover {
        color: #fff;
    }
</style>

<div class="admin-sub-nav">
    <button class="sub-nav-btn active" onclick="switchSection('publications', this)">📰 Publications</button>
    <button class="sub-nav-btn" onclick="switchSection('commentaires', this)">💬 Commentaires</button>
    <button class="sub-nav-btn" onclick="switchSection('moderation', this)">⚠️ Modération (<?= $totalSignalements ?>)</button>
    <button class="sub-nav-btn" onclick="switchSection('ajouter', this)">➕ Ajouter Publication</button>
    <button class="sub-nav-btn" onclick="exportPdf()" style="margin-left: auto; background: rgba(239, 68, 68, 0.1); border-color: rgba(239, 68, 68, 0.3); color: #f87171;">📕 Exporter PDF</button>
</div>

<!-- STATS CARDS -->
<div class="stats-row">
    <div class="stat-widget">
        <div class="stat-widget-icon">📰</div>
        <div class="stat-widget-info">
            <h3>Total Publications</h3>
            <p class="num"><?= $totalPubs ?></p>
        </div>
    </div>
    <div class="stat-widget">
        <div class="stat-widget-icon">💬</div>
        <div class="stat-widget-info">
            <h3>Total Commentaires</h3>
            <p class="num"><?= $totalComs ?></p>
        </div>
    </div>
    <div class="stat-widget" style="border-color: rgba(239,68,68,0.3);">
        <div class="stat-widget-icon" style="color: #f87171; background: rgba(239,68,68,0.1);">🚩</div>
        <div class="stat-widget-info">
            <h3>Signalements actifs</h3>
            <p class="num" style="color: #f87171;"><?= $totalSignalements ?></p>
        </div>
    </div>
</div>

<?php if ($msgSuccess !== ''): ?>
    <div class="alert-box alert-success">
        <span>✅</span> <?= h($msgSuccess) ?>
    </div>
<?php endif; ?>

<?php if ($msgError !== ''): ?>
    <div class="alert-box alert-danger">
        <span>⚠️</span> <?= h($msgError) ?>
    </div>
<?php endif; ?>

<!-- SECTION: PUBLICATIONS LIST -->
<div id="sec-publications" class="admin-section active">
    <div class="admin-card">
        <h2>📰 Liste des Publications</h2>
        
        <div class="controls-row">
            <input type="text" id="search-pubs" placeholder="🔍 Rechercher par titre, contenu, auteur..." oninput="filterPubs()">
        </div>

        <div class="table-responsive">
            <table class="custom-table">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Titre</th>
                        <th>Auteur ID</th>
                        <th>Date de publication</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody id="pubs-tbody">
                    <?php if (empty($publications)): ?>
                        <tr>
                            <td colspan="5" style="text-align: center; color: #aaa;">Aucune publication trouvée.</td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($publications as $index => $pub): ?>
                            <tr data-titre="<?= h(strtolower($pub['titre'])) ?>" data-contenu="<?= h(strtolower($pub['contenu'])) ?>" data-iduser="<?= $pub['id_user'] ?>">
                                <td><?= $index + 1 ?></td>
                                <td style="font-weight: 600; color: #b2f2bb;"><?= h($pub['titre']) ?></td>
                                <td><?= $pub['id_user'] ?></td>
                                <td><?= date('d/m/Y H:i', strtotime($pub['date_publication'])) ?></td>
                                <td>
                                    <div style="display: flex; gap: 8px;">
                                        <button class="btn-action btn-ai" onclick="openAISummary(<?= $pub['id_pub'] ?>)">✨ IA Résumé</button>
                                        <button class="btn-action btn-primary" onclick="openModifierModal(<?= $pub['id_pub'] ?>, '<?= h(addslashes($pub['titre'])) ?>', '<?= h(addslashes($pub['contenu'])) ?>', '<?= h(addslashes($pub['media_url'] ?? '')) ?>', <?= $pub['id_user'] ?>)">✏️ Modifier</button>
                                        <form method="POST" action="" onsubmit="return confirm('Voulez-vous vraiment supprimer cette publication ?');" style="margin:0;">
                                            <input type="hidden" name="action" value="supprimer_pub">
                                            <input type="hidden" name="id_pub" value="<?= $pub['id_pub'] ?>">
                                            <button type="submit" class="btn-action btn-danger">🗑️ Supprimer</button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- SECTION: COMMENTAIRES LIST -->
<div id="sec-commentaires" class="admin-section">
    <div class="admin-card">
        <h2>💬 Tous les Commentaires</h2>
        
        <div class="controls-row">
            <input type="text" id="search-coms" placeholder="🔍 Rechercher dans les commentaires..." oninput="filterComs()">
        </div>

        <div class="table-responsive">
            <table class="custom-table">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>ID Pub</th>
                        <th>Commentaire</th>
                        <th>Note</th>
                        <th>Likes</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody id="coms-tbody">
                    <!-- Loaded dynamically via AJAX -->
                    <tr>
                        <td colspan="6" style="text-align: center;"><div class="spinner"></div> Chargement...</td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- SECTION: MODERATION -->
<div id="sec-moderation" class="admin-section">
    <div class="admin-card">
        <h2>⚠️ Modération et Signalements</h2>
        <p style="font-size: 14px; color: #ccc; margin-bottom: 20px;">Gérez les publications et commentaires signalés par les utilisateurs.</p>

        <div class="table-responsive">
            <table class="custom-table">
                <thead>
                    <tr>
                        <th>Index</th>
                        <th>Type</th>
                        <th>ID Élément</th>
                        <th>Date Signalement</th>
                        <th>État</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($signalements)): ?>
                        <tr>
                            <td colspan="6" style="text-align: center; color: #aaa;">Aucun signalement actif pour le moment.</td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($signalements as $index => $sig): ?>
                            <tr>
                                <td><?= $index ?></td>
                                <td style="font-weight: 600; color: #b2f2bb;"><?= h(ucfirst($sig['type'])) ?></td>
                                <td><?= $sig['id'] ?></td>
                                <td><?= date('d/m/Y H:i', strtotime($sig['date_signalement'])) ?></td>
                                <td>
                                    <span class="badge" style="background: rgba(245, 158, 11, 0.15); color: #fbbf24; border: 1px solid rgba(245, 158, 11, 0.3); padding: 4px 8px; border-radius: 6px; font-size: 12px;">
                                        <?= h($sig['etat']) ?>
                                    </span>
                                </td>
                                <td>
                                    <div style="display: flex; gap: 8px;">
                                        <form method="POST" action="" style="margin:0;">
                                            <input type="hidden" name="action" value="valider_signalement">
                                            <input type="hidden" name="index" value="<?= $index ?>">
                                            <button type="submit" class="btn-action btn-primary">✅ Valider (Supprimer)</button>
                                        </form>
                                        <form method="POST" action="" style="margin:0;">
                                            <input type="hidden" name="action" value="rejeter_signalement">
                                            <input type="hidden" name="index" value="<?= $index ?>">
                                            <button type="submit" class="btn-action btn-danger">❌ Rejeter</button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- SECTION: AJOUTER PUBLICATION -->
<div id="sec-ajouter" class="admin-section">
    <div class="admin-card">
        <h2>➕ Ajouter une Publication</h2>
        
        <form method="POST" action="">
            <input type="hidden" name="action" value="ajouter_pub">

            <div class="form-group">
                <label for="titre">Titre de la Publication</label>
                <input type="text" name="titre" id="titre" required placeholder="Ex: Notre nouvelle démarche éco-responsable">
            </div>

            <div class="form-group">
                <label for="contenu">Contenu</label>
                <textarea name="contenu" id="contenu" rows="6" required placeholder="Saisissez ici le corps de votre publication..."></textarea>
            </div>

            <div class="form-group">
                <label for="media_url">URL Média / Image (optionnel)</label>
                <input type="url" name="media_url" id="media_url" placeholder="https://exemple.com/image.png">
            </div>

            <button type="submit" class="btn-action btn-primary" style="padding: 12px 24px; font-size: 14px;">Publier maintenant</button>
        </form>
    </div>
</div>

<!-- MODAL: MODIFIER PUBLICATION -->
<div id="modifier-modal" class="custom-modal">
    <div class="custom-modal-content">
        <span class="close-modal" onclick="closeModifierModal()">&times;</span>
        <h2>✏️ Modifier la Publication</h2>
        
        <form method="POST" action="">
            <input type="hidden" name="action" value="modifier_pub">
            <input type="hidden" name="id_pub" id="edit-id_pub">
            <input type="hidden" name="id_user" id="edit-id_user">

            <div class="form-group">
                <label for="edit-titre">Titre de la Publication</label>
                <input type="text" name="titre" id="edit-titre" required>
            </div>

            <div class="form-group">
                <label for="edit-contenu">Contenu</label>
                <textarea name="contenu" id="edit-contenu" rows="6" required></textarea>
            </div>

            <div class="form-group">
                <label for="edit-media_url">URL Média / Image</label>
                <input type="url" name="media_url" id="edit-media_url">
            </div>

            <button type="submit" class="btn-action btn-primary">Enregistrer les modifications</button>
        </form>
    </div>
</div>

<!-- MODAL: AI SUMMARY -->
<div id="ai-summary-modal" class="custom-modal">
    <div class="custom-modal-content">
        <span class="close-modal" onclick="closeAISummary()">&times;</span>
        <h2 style="color: #c084fc;">✨ Intelligence Artificielle ECOSAVE</h2>
        
        <div id="ai-summary-loading" style="text-align:center; padding: 20px 0;">
            <div class="spinner" style="border-top-color:#c084fc;"></div>
            <p style="color: #aaa; font-size: 13px;">Génération du résumé intelligent via OpenAI/Groq...</p>
        </div>

        <div id="ai-summary-result" style="display:none; max-height: 400px; overflow-y: auto;">
            <h4 style="color: #b2f2bb; margin-bottom: 8px;">Résumé IA :</h4>
            <div id="ai-text-summary" style="background: rgba(255,255,255,0.05); padding: 12px; border-radius: 8px; font-size: 13px; line-height: 1.6; color:#e0e0e0; margin-bottom: 16px;"></div>

            <h4 style="color: #b2f2bb; margin-bottom: 8px;">Suggestion de réponse :</h4>
            <div id="ai-text-suggestion" style="background: rgba(255,255,255,0.05); padding: 12px; border-radius: 8px; font-size: 13px; line-height: 1.6; color:#e0e0e0;"></div>
        </div>
    </div>
</div>

<script>
    function switchSection(sectionId, btn) {
        document.querySelectorAll('.admin-section').forEach(sec => sec.classList.remove('active'));
        document.querySelectorAll('.sub-nav-btn').forEach(b => b.classList.remove('active'));
        
        document.getElementById('sec-' + sectionId).classList.add('active');
        if (btn) btn.classList.add('active');

        if (sectionId === 'commentaires') {
            loadAllCommentaires();
        }
    }

    function filterPubs() {
        const query = document.getElementById('search-pubs').value.toLowerCase().trim();
        const rows = document.querySelectorAll('#pubs-tbody tr[data-titre]');
        
        rows.forEach(row => {
            const titre = row.getAttribute('data-titre') || '';
            const contenu = row.getAttribute('data-contenu') || '';
            const iduser = row.getAttribute('data-iduser') || '';

            if (titre.includes(query) || contenu.includes(query) || iduser.includes(query)) {
                row.style.display = '';
            } else {
                row.style.display = 'none';
            }
        });
    }

    function loadAllCommentaires() {
        const tbody = document.getElementById('coms-tbody');
        tbody.innerHTML = '<tr><td colspan="6" style="text-align: center;"><div class="spinner"></div> Chargement...</td></tr>';

        fetch('publications_client_api.php?action=coms_getAll')
            .then(r => r.json())
            .then(res => {
                if (res.success && res.data) {
                    renderCommentaires(res.data);
                } else {
                    tbody.innerHTML = '<tr><td colspan="6" style="text-align: center; color: #f87171;">Impossible de charger les commentaires.</td></tr>';
                }
            });
    }

    let loadedCommentaires = [];

    function renderCommentaires(list) {
        loadedCommentaires = list;
        const tbody = document.getElementById('coms-tbody');
        if (list.length === 0) {
            tbody.innerHTML = '<tr><td colspan="6" style="text-align: center; color: #aaa;">Aucun commentaire.</td></tr>';
            return;
        }

        let html = '';
        list.forEach((com, index) => {
            html += `
                <tr data-contenu="${(com.contenu || '').toLowerCase()}">
                    <td>${index + 1}</td>
                    <td>${com.id_pub}</td>
                    <td style="color: #e0e0e0;">${com.contenu}</td>
                    <td><span class="badge badge-warning">${com.note ? com.note + '/5' : 'N/A'}</span></td>
                    <td>👍 ${com.likes_count || 0}</td>
                    <td>
                        <form method="POST" action="" onsubmit="return confirm('Voulez-vous supprimer ce commentaire ?');" style="margin:0;">
                            <input type="hidden" name="action" value="supprimer_com">
                            <input type="hidden" name="id_commentaire" value="${com.id_commentaire}">
                            <button type="submit" class="btn-action btn-danger">🗑️ Supprimer</button>
                        </form>
                    </td>
                </tr>
            `;
        });
        tbody.innerHTML = html;
    }

    function filterComs() {
        const query = document.getElementById('search-coms').value.toLowerCase().trim();
        const rows = document.querySelectorAll('#coms-tbody tr[data-contenu]');
        
        rows.forEach(row => {
            const contenu = row.getAttribute('data-contenu') || '';
            if (contenu.includes(query)) {
                row.style.display = '';
            } else {
                row.style.display = 'none';
            }
        });
    }

    function openModifierModal(idPub, titre, contenu, mediaUrl, idUser) {
        document.getElementById('edit-id_pub').value = idPub;
        document.getElementById('edit-id_user').value = idUser;
        document.getElementById('edit-titre').value = titre;
        document.getElementById('edit-contenu').value = contenu;
        document.getElementById('edit-media_url').value = mediaUrl;

        document.getElementById('modifier-modal').style.display = 'flex';
    }

    function closeModifierModal() {
        document.getElementById('modifier-modal').style.display = 'none';
    }

    function openAISummary(idPub) {
        document.getElementById('ai-summary-modal').style.display = 'flex';
        document.getElementById('ai-summary-loading').style.display = 'block';
        document.getElementById('ai-summary-result').style.display = 'none';

        // Fetch AI summary via ajax
        fetch(`publications_client_api.php?action=ai_getSummary&id_pub=${idPub}`)
            .then(r => r.json())
            .then(res => {
                document.getElementById('ai-summary-loading').style.display = 'none';
                document.getElementById('ai-summary-result').style.display = 'block';

                if (res.success && res.summary) {
                    document.getElementById('ai-text-summary').innerText = res.summary;
                    document.getElementById('ai-text-suggestion').innerText = res.suggestion || "Aucune suggestion disponible.";
                } else {
                    document.getElementById('ai-text-summary').innerText = "Désolé, impossible de générer le résumé (Assurez-vous d'avoir configuré vos clés de services de manière adéquate).";
                    document.getElementById('ai-text-suggestion').innerText = "Une erreur s'est produite lors de l'appel au service d'IA.";
                }
            })
            .catch(() => {
                document.getElementById('ai-summary-loading').style.display = 'none';
                document.getElementById('ai-summary-result').style.display = 'block';
                document.getElementById('ai-text-summary').innerText = "Une erreur réseau s'est produite.";
            });
    }

    function closeAISummary() {
        document.getElementById('ai-summary-modal').style.display = 'none';
    }

    function exportPdf() {
        window.location.href = 'publications_client_api.php?action=export_pdf';
    }
</script>

<?php require __DIR__ . '/partials/footer.php'; ?>
