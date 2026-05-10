<?php
declare(strict_types=1);
session_start();

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

if (!function_exists('h')) {
    function h(?string $s): string
    {
        return htmlspecialchars((string) $s, ENT_QUOTES, 'UTF-8');
    }
}

require_once __DIR__ . '/../pub/controller/PublicationController.php';
require_once __DIR__ . '/../pub/controller/CommentaireController.php';

$pubController = new PublicationController();
$comController = new CommentaireController();

$userId = (int) ($_SESSION['user_id'] ?? 0);
$msgSuccess = '';
$msgError = '';

// Handle client POST actions (like, signal, add comment)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';

    if ($action === 'ajouter_commentaire') {
        $idPub = (int) ($_POST['id_pub'] ?? 0);
        $contenu = trim((string) ($_POST['contenu'] ?? ''));
        $note = isset($_POST['note']) && $_POST['note'] !== '' ? (int) $_POST['note'] : null;

        $res = $comController->ajouterCommentaire([
            'id_pub' => $idPub,
            'contenu' => $contenu,
            'note' => $note
        ]);

        if ($res['success'] ?? false) {
            $msgSuccess = "Votre commentaire a été publié avec succès !";
        } else {
            $msgError = $res['errors']['contenu'] ?? "Une erreur est survenue lors de l'ajout.";
        }
    } elseif ($action === 'liker_com') {
        $idCom = (int) ($_POST['id_commentaire'] ?? 0);
        $comController->likerCommentaire($idCom);
        $msgSuccess = "Vous avez aimé ce commentaire !";
    } elseif ($action === 'signaler_pub') {
        $idPub = (int) ($_POST['id_pub'] ?? 0);
        $pubController->signalerPublication($idPub);
        $msgSuccess = "La publication a été signalée aux modérateurs.";
    } elseif ($action === 'signaler_com') {
        $idCom = (int) ($_POST['id_commentaire'] ?? 0);
        $comController->signalerCommentaire($idCom);
        $msgSuccess = "Le commentaire a été signalé aux modérateurs.";
    }
}

// Fetch all publications with their average rating and total comments count
$publications = $pubController->getPublicationsWithStats();

$pageTitle = 'Espace de Discussion & Publications';
require __DIR__ . '/partials/header.php';
?>

<style>
    /* Premium glassmorphism design */
    .pub-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(320px, 1fr));
        gap: 24px;
        margin-top: 20px;
    }

    .pub-card {
        background: rgba(255, 255, 255, 0.08);
        backdrop-filter: blur(12px);
        -webkit-backdrop-filter: blur(12px);
        border: 1px solid rgba(255, 255, 255, 0.15);
        border-radius: 16px;
        overflow: hidden;
        display: flex;
        flex-direction: column;
        transition: all 0.3s ease;
        color: #ffffff;
        position: relative;
    }

    .pub-card:hover {
        transform: translateY(-6px);
        border-color: rgba(178, 242, 187, 0.3);
        box-shadow: 0 12px 40px rgba(16, 185, 129, 0.2);
    }

    .pub-media {
        width: 100%;
        height: 180px;
        object-fit: cover;
        background: linear-gradient(135deg, #0a3d2a, #059669);
    }

    .pub-card-content {
        padding: 20px;
        flex: 1;
        display: flex;
        flex-direction: column;
    }

    .pub-meta {
        font-size: 11px;
        color: #b2f2bb;
        text-transform: uppercase;
        letter-spacing: 1px;
        margin-bottom: 8px;
        font-weight: 600;
        display: flex;
        justify-content: space-between;
    }

    .pub-title {
        font-size: 19px;
        font-weight: 700;
        color: #ffffff;
        margin: 0 0 10px 0;
        line-height: 1.3;
    }

    .pub-body-preview {
        font-size: 13px;
        color: #e0e0e0;
        line-height: 1.5;
        margin-bottom: 20px;
        display: -webkit-box;
        -webkit-line-clamp: 3;
        -webkit-box-orient: vertical;
        overflow: hidden;
    }

    .pub-footer {
        display: flex;
        justify-content: space-between;
        align-items: center;
        border-top: 1px solid rgba(255, 255, 255, 0.08);
        padding-top: 14px;
        margin-top: auto;
    }

    .stat-badge {
        font-size: 12px;
        color: #ccc;
        display: flex;
        align-items: center;
        gap: 6px;
    }

    .btn-read-more {
        padding: 8px 16px;
        background: rgba(178, 242, 187, 0.1);
        color: #b2f2bb;
        border: 1px solid rgba(178, 242, 187, 0.25);
        border-radius: 8px;
        font-size: 12px;
        font-weight: 600;
        cursor: pointer;
        text-decoration: none;
        transition: all 0.25s ease;
    }

    .btn-read-more:hover {
        background: #b2f2bb;
        color: #0a3d2a;
    }

    /* Details Overlay / Modal Style on same page */
    .details-panel {
        display: none;
        background: rgba(255, 255, 255, 0.1);
        backdrop-filter: blur(16px);
        -webkit-backdrop-filter: blur(16px);
        border: 1px solid rgba(255, 255, 255, 0.15);
        border-radius: 16px;
        padding: 24px;
        box-shadow: 0 8px 32px rgba(0, 0, 0, 0.25);
        color: #ffffff;
        margin-top: 24px;
        animation: slideDown 0.4s ease;
    }

    @keyframes slideDown {
        from { opacity: 0; transform: translateY(-20px); }
        to { opacity: 1; transform: translateY(0); }
    }

    .details-panel h2 {
        color: #b2f2bb;
        font-size: 24px;
        margin-bottom: 8px;
    }

    .com-box {
        background: rgba(255, 255, 255, 0.03);
        border: 1px solid rgba(255, 255, 255, 0.06);
        border-radius: 12px;
        padding: 16px;
        margin-bottom: 12px;
    }

    .com-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        font-size: 12px;
        color: #aaa;
        margin-bottom: 8px;
    }

    .stars {
        color: #fbbf24;
    }

    /* Rating Selector */
    .rating-selector {
        display: flex;
        gap: 8px;
        margin-bottom: 12px;
    }

    .rating-star {
        font-size: 20px;
        color: #555;
        cursor: pointer;
        transition: color 0.2s;
    }

    .rating-star.selected, .rating-star:hover {
        color: #fbbf24;
    }

    /* Inputs */
    .input-com {
        width: 100%;
        padding: 12px 16px;
        background: rgba(255, 255, 255, 0.05);
        border: 1px solid rgba(178, 242, 187, 0.3);
        border-radius: 12px;
        color: #fff;
        outline: none;
        resize: none;
        font-size: 14px;
    }

    .input-com:focus {
        border-color: #10b981;
        background: rgba(255, 255, 255, 0.1);
    }

    .btn-action {
        padding: 8px 14px;
        border-radius: 8px;
        font-size: 12px;
        font-weight: 600;
        cursor: pointer;
        transition: all 0.2s;
        border: none;
        background: rgba(255,255,255,0.05);
        color: #fff;
        display: inline-flex;
        align-items: center;
        gap: 6px;
    }

    .btn-action:hover {
        background: rgba(255,255,255,0.1);
        transform: translateY(-1px);
    }

    .btn-submit-com {
        padding: 12px 24px;
        background: linear-gradient(135deg, #10b981, #059669);
        color: #fff;
        border: none;
        border-radius: 12px;
        font-weight: 600;
        font-size: 14px;
        cursor: pointer;
        transition: all 0.25s ease;
        margin-top: 10px;
    }

    .btn-submit-com:hover {
        background: linear-gradient(135deg, #059669, #047857);
        transform: translateY(-1px);
        box-shadow: 0 4px 12px rgba(16, 185, 129, 0.3);
    }
</style>

<div class="fo-card" style="background: none; border: none; padding: 0;">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
        <h1 style="color: #ffffff; font-size: 26px; font-weight: 700;">📰 Espace de Discussion & Communauté</h1>
        <div style="display:flex; gap:10px;">
            <input type="text" id="search-input" placeholder="🔍 Rechercher une publication..." oninput="filterPublications()" style="padding: 10px 16px; border: 1px solid rgba(178,242,187,0.3); border-radius: 10px; background: rgba(255,255,255,0.05); color:#fff; font-size:14px; outline:none; width: 250px;">
        </div>
    </div>

    <?php if ($msgSuccess !== ''): ?>
        <div style="background: rgba(16, 185, 129, 0.15); border: 1px solid rgba(16, 185, 129, 0.3); color: #34d399; padding: 12px 16px; border-radius: 12px; margin-bottom: 20px; font-weight: 600;">
            ✅ <?= h($msgSuccess) ?>
        </div>
    <?php endif; ?>

    <?php if ($msgError !== ''): ?>
        <div style="background: rgba(239, 68, 68, 0.15); border: 1px solid rgba(239, 68, 68, 0.3); color: #f87171; padding: 12px 16px; border-radius: 12px; margin-bottom: 20px; font-weight: 600;">
            ⚠️ <?= h($msgError) ?>
        </div>
    <?php endif; ?>

    <!-- DETAILS EXPANDED VIEW (Anchored at the top if clicked) -->
    <div id="pub-details-panel" class="details-panel">
        <div style="display:flex; justify-content:space-between; align-items:center;">
            <div class="pub-meta" style="margin-bottom: 0;" id="det-meta">PUBLIÉ LE ...</div>
            <button class="btn-action" onclick="closeDetails()" style="background:none; border:none; color:#ef4444; font-size:18px;">&times; Fermer</button>
        </div>
        <h2 id="det-title">Titre de la publication</h2>
        <div style="display: flex; gap: 20px; margin-bottom: 20px; flex-wrap: wrap;">
            <img id="det-img" src="" style="width: 100%; max-height: 250px; object-fit: cover; border-radius: 12px; display: none;">
            <p id="det-content" style="font-size: 15px; line-height: 1.6; color: #eee; white-space: pre-line;">Contenu complet...</p>
        </div>

        <div style="display:flex; gap:10px; margin-bottom: 24px; border-bottom: 1px solid rgba(255,255,255,0.1); padding-bottom: 16px;">
            <form id="form-signal" method="POST" action="">
                <input type="hidden" name="action" value="signaler_pub">
                <input type="hidden" name="id_pub" id="det-id_pub">
                <button type="submit" class="btn-action" style="color: #f87171;"><span style="font-size:14px;">🚩</span> Signaler la publication</button>
            </form>
            <button class="btn-action btn-ai" id="det-ai-summary" onclick="loadAISummaryDetails()"><span style="font-size:14px;">✨</span> Obtenir résumé IA</button>
        </div>

        <!-- AI Details Card inside panel -->
        <div id="ai-details-box" style="display:none; background: rgba(168, 85, 247, 0.1); border: 1px solid rgba(168, 85, 247, 0.25); border-radius: 12px; padding: 16px; margin-bottom: 20px;">
            <h4 style="color: #c084fc; margin-top: 0; margin-bottom: 8px;">✨ Résumé intelligent IA :</h4>
            <p id="ai-details-text" style="font-size: 13px; color: #e0e0e0; line-height: 1.5; margin-bottom: 12px;"></p>
            <h4 style="color: #c084fc; margin-bottom: 8px;">✨ Suggestion de réponse :</h4>
            <p id="ai-details-suggest" style="font-size: 13px; color: #e0e0e0; line-height: 1.5; margin: 0;"></p>
        </div>

        <!-- Comments list -->
        <h3 style="color:#b2f2bb; font-size:18px; margin-bottom:16px;">💬 Commentaires (<span id="det-com-count">0</span>)</h3>
        <div id="comments-container" style="max-height: 350px; overflow-y:auto; margin-bottom: 24px; padding-right: 8px;">
            <!-- Rendered via JavaScript -->
        </div>

        <!-- Add a comment form -->
        <h3 style="color:#b2f2bb; font-size:18px; margin-bottom:16px;">✍️ Ajouter un Commentaire</h3>
        <form method="POST" action="">
            <input type="hidden" name="action" value="ajouter_commentaire">
            <input type="hidden" name="id_pub" id="form-id_pub">

            <div style="margin-bottom: 12px;">
                <label style="display:block; font-size:12px; color:#aaa; margin-bottom: 6px;">Donnez une note (facultatif) :</label>
                <div class="rating-selector">
                    <span class="rating-star" onclick="setRating(1)" data-val="1">★</span>
                    <span class="rating-star" onclick="setRating(2)" data-val="2">★</span>
                    <span class="rating-star" onclick="setRating(3)" data-val="3">★</span>
                    <span class="rating-star" onclick="setRating(4)" data-val="4">★</span>
                    <span class="rating-star" onclick="setRating(5)" data-val="5">★</span>
                </div>
                <input type="hidden" name="note" id="form-note" value="">
            </div>

            <div>
                <textarea name="contenu" id="form-contenu" rows="4" class="input-com" required placeholder="Saisissez votre commentaire ici... (Merci de rester respectueux)"></textarea>
            </div>

            <button type="submit" class="btn-submit-com">Publier mon commentaire</button>
        </form>
    </div>

    <!-- MAIN GRID OF PUBLICATIONS -->
    <div class="pub-grid" id="publications-grid">
        <?php if (empty($publications)): ?>
            <p style="text-align: center; color: #aaa; width: 100%; grid-column: 1/-1;">Aucune publication disponible pour le moment.</p>
        <?php else: ?>
            <?php foreach ($publications as $pub): ?>
                <div class="pub-card" data-titre="<?= h(strtolower($pub['titre'])) ?>" data-contenu="<?= h(strtolower($pub['contenu'])) ?>">
                    <?php if (!empty($pub['media_url'])): ?>
                        <img class="pub-media" src="<?= h($pub['media_url']) ?>" onerror="this.src='../logo.png'; this.style.objectFit='contain';">
                    <?php else: ?>
                        <div class="pub-media" style="display: flex; align-items: center; justify-content: center; font-size: 50px;">📰</div>
                    <?php endif; ?>

                    <div class="pub-card-content">
                        <div class="pub-meta">
                            <span>📅 <?= date('d/m/Y', strtotime($pub['date_publication'])) ?></span>
                            <span>Auteur #<?= $pub['id_user'] ?></span>
                        </div>

                        <h3 class="pub-title"><?= h($pub['titre']) ?></h3>
                        <p class="pub-body-preview"><?= h($pub['contenu']) ?></p>

                        <div class="pub-footer">
                            <div style="display:flex; gap:12px;">
                                <span class="stat-badge">💬 <?= $pub['total_commentaires'] ?></span>
                                <?php if ($pub['note_moyenne'] > 0): ?>
                                    <span class="stat-badge" style="color:#fbbf24;">★ <?= number_format((float)$pub['note_moyenne'], 1) ?></span>
                                <?php endif; ?>
                            </div>
                            <button class="btn-read-more" onclick="showDetails(<?= $pub['id_pub'] ?>, '<?= h(addslashes($pub['titre'])) ?>', '<?= h(addslashes($pub['contenu'])) ?>', '<?= h(addslashes($pub['media_url'] ?? '')) ?>', '<?= date('d/m/Y H:i', strtotime($pub['date_publication'])) ?>')">Voir & Commenter</button>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>
</div>

<script>
    function filterPublications() {
        const query = document.getElementById('search-input').value.toLowerCase().trim();
        const cards = document.querySelectorAll('.pub-card');

        cards.forEach(card => {
            const titre = card.getAttribute('data-titre') || '';
            const contenu = card.getAttribute('data-contenu') || '';

            if (titre.includes(query) || contenu.includes(query)) {
                card.style.display = '';
            } else {
                card.style.display = 'none';
            }
        });
    }

    let currentPubIdForAI = 0;

    function showDetails(idPub, titre, contenu, mediaUrl, dateStr) {
        currentPubIdForAI = idPub;
        document.getElementById('det-title').innerText = titre;
        document.getElementById('det-content').innerText = contenu;
        document.getElementById('det-meta').innerText = `PUBLIÉ LE ${dateStr} - REF #${idPub}`;
        
        const img = document.getElementById('det-img');
        if (mediaUrl && mediaUrl !== '') {
            img.src = mediaUrl;
            img.style.display = 'block';
        } else {
            img.style.display = 'none';
        }

        document.getElementById('det-id_pub').value = idPub;
        document.getElementById('form-id_pub').value = idPub;
        document.getElementById('form-contenu').value = '';
        setRating(0);

        // Hide AI box
        document.getElementById('ai-details-box').style.display = 'none';

        // Scroll dynamically to details panel
        const panel = document.getElementById('pub-details-panel');
        panel.style.display = 'block';
        panel.scrollIntoView({ behavior: 'smooth' });

        // Load comments via ajax
        loadComments(idPub);
    }

    function closeDetails() {
        document.getElementById('pub-details-panel').style.display = 'none';
    }

    function setRating(val) {
        document.getElementById('form-note').value = val > 0 ? val : '';
        document.querySelectorAll('.rating-star').forEach(star => {
            const starVal = parseInt(star.getAttribute('data-val'));
            if (starVal <= val) {
                star.classList.add('selected');
            } else {
                star.classList.remove('selected');
            }
        });
    }

    function loadComments(idPub) {
        const container = document.getElementById('comments-container');
        container.innerHTML = '<div style="text-align:center; padding: 20px;"><div class="spinner"></div></div>';

        fetch(`publications_client_api.php?action=coms_getByPub&id_pub=${idPub}`)
            .then(r => r.json())
            .then(res => {
                if (res.success && res.data) {
                    document.getElementById('det-com-count').innerText = res.data.length;
                    renderCommentsList(res.data);
                } else {
                    container.innerHTML = '<p style="text-align:center; color:#aaa;">Impossible de charger les commentaires.</p>';
                }
            })
            .catch(() => {
                container.innerHTML = '<p style="text-align:center; color:#aaa;">Erreur réseau.</p>';
            });
    }

    function renderCommentsList(list) {
        const container = document.getElementById('comments-container');
        if (list.length === 0) {
            container.innerHTML = '<p style="text-align:center; color:#aaa; font-style:italic;">Aucun commentaire. Soyez le premier à donner votre avis !</p>';
            return;
        }

        let html = '';
        list.forEach(com => {
            let starsHtml = '';
            if (com.note) {
                starsHtml = '<span class="stars">' + '★'.repeat(com.note) + '☆'.repeat(5 - com.note) + '</span>';
            }

            html += `
                <div class="com-box">
                    <div class="com-header">
                        <span>📅 Posté le ${com.date_commentaire}</span>
                        ${starsHtml}
                    </div>
                    <p style="font-size:13px; color:#e0e0e0; margin: 0 0 10px 0; line-height:1.4;">${com.contenu}</p>
                    <div style="display:flex; justify-content:space-between; align-items:center;">
                        <button class="btn-action" onclick="likeComment(${com.id_commentaire}, this)">👍 Utile (${com.likes_count || 0})</button>
                        <button class="btn-action" onclick="signalComment(${com.id_commentaire})" style="color:#f87171;">🚩 Signaler</button>
                    </div>
                </div>
            `;
        });
        container.innerHTML = html;
    }

    function likeComment(idCom, btn) {
        const formData = new FormData();
        formData.append('action', 'liker_com');
        formData.append('id_commentaire', idCom);

        fetch('', {
            method: 'POST',
            body: formData
        }).then(() => {
            // Quick UI update
            btn.style.color = '#34d399';
            btn.disabled = true;
            loadComments(currentPubIdForAI);
        });
    }

    function signalComment(idCom) {
        if (confirm('Voulez-vous vraiment signaler ce commentaire ?')) {
            const formData = new FormData();
            formData.append('action', 'signaler_com');
            formData.append('id_commentaire', idCom);

            fetch('', {
                method: 'POST',
                body: formData
            }).then(() => {
                alert('Le commentaire a été signalé.');
            });
        }
    }

    function loadAISummaryDetails() {
        const btn = document.getElementById('det-ai-summary');
        const box = document.getElementById('ai-details-box');
        const summaryText = document.getElementById('ai-details-text');
        const suggestText = document.getElementById('ai-details-suggest');

        btn.disabled = true;
        btn.innerText = '✨ Génération IA...';
        box.style.display = 'block';
        summaryText.innerText = 'Connexion à OpenAI / Groq en cours...';
        suggestText.innerText = 'Analyse de la publication et des commentaires...';

        fetch(`publications_client_api.php?action=ai_getSummary&id_pub=${currentPubIdForAI}`)
            .then(r => r.json())
            .then(res => {
                btn.disabled = false;
                btn.innerHTML = '<span style="font-size:14px;">✨</span> Obtenir résumé IA';

                if (res.success && res.summary) {
                    summaryText.innerText = res.summary;
                    suggestText.innerText = res.suggestion || 'Aucune suggestion disponible.';
                } else {
                    summaryText.innerText = 'Impossible de générer le résumé (Veuillez configurer votre clé API).';
                    suggestText.innerText = 'Service IA non disponible.';
                }
            })
            .catch(() => {
                btn.disabled = false;
                btn.innerHTML = '<span style="font-size:14px;">✨</span> Obtenir résumé IA';
                summaryText.innerText = 'Une erreur réseau est survenue.';
                suggestText.innerText = '';
            });
    }
</script>

<?php require __DIR__ . '/partials/footer.php'; ?>
