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

require_once __DIR__ . '/../config/config.php';
$userId = (int) ($_SESSION['user_id'] ?? 0);
$pdo = config::getConnexion();

$msgSuccess = '';
$msgError = '';

// Handle Association / Dissociation POST Action
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['action_allergy'])) {
        $action = $_POST['action_allergy'];
        if ($action === 'add') {
            $idAllergie = (int) ($_POST['id_allergie'] ?? 0);
            if ($idAllergie > 0) {
                $chk = $pdo->prepare('SELECT COUNT(*) FROM utilisateur_allergie WHERE id_user = ? AND id_allergie = ?');
                $chk->execute([$userId, $idAllergie]);
                if ((int) $chk->fetchColumn() === 0) {
                    $ins = $pdo->prepare('INSERT INTO utilisateur_allergie (id_user, id_allergie) VALUES (?, ?)');
                    if ($ins->execute([$userId, $idAllergie])) {
                        $msgSuccess = "L'allergie a été ajoutée avec succès à votre profil !";
                    } else {
                        $msgError = "Une erreur est survenue lors de l'ajout.";
                    }
                } else {
                    $msgError = "Cette allergie est déjà enregistrée dans votre profil.";
                }
            } else {
                $msgError = "Veuillez sélectionner une allergie valide.";
            }
        } elseif ($action === 'delete') {
            $idAllergie = (int) ($_POST['id_allergie'] ?? 0);
            if ($idAllergie > 0) {
                $del = $pdo->prepare('DELETE FROM utilisateur_allergie WHERE id_user = ? AND id_allergie = ?');
                if ($del->execute([$userId, $idAllergie])) {
                    $msgSuccess = "L'allergie a été retirée de votre profil.";
                } else {
                    $msgError = "Une erreur est survenue lors du retrait.";
                }
            }
        }
    }
}

// Fetch user active allergies
$stmtUser = $pdo->prepare('SELECT a.* FROM allergie a INNER JOIN utilisateur_allergie ua ON a.id_allergie = ua.id_allergie WHERE ua.id_user = ? ORDER BY a.nom ASC');
$stmtUser->execute([$userId]);
$userAllergies = $stmtUser->fetchAll(PDO::FETCH_ASSOC);

// Fetch all available allergies excluding already associated ones
$stmtAllAvail = $pdo->prepare('SELECT id_allergie, nom FROM allergie WHERE id_allergie NOT IN (SELECT id_allergie FROM utilisateur_allergie WHERE id_user = ?) ORDER BY nom ASC');
$stmtAllAvail->execute([$userId]);
$availableAllergies = $stmtAllAvail->fetchAll(PDO::FETCH_ASSOC);

$pageTitle = 'Mes Allergies & Santé';
require __DIR__ . '/partials/header.php';
?>

<style>
    /* Premium glassmorphism styles matching ECOSAVE platform */
    .client-card {
        background: rgba(255, 255, 255, 0.1);
        backdrop-filter: blur(12px);
        -webkit-backdrop-filter: blur(12px);
        border: 1px solid rgba(255, 255, 255, 0.15);
        border-radius: 16px;
        padding: 24px;
        box-shadow: 0 8px 32px rgba(0, 0, 0, 0.2);
        color: #ffffff;
        margin-bottom: 24px;
        transition: transform 0.3s ease, box-shadow 0.3s ease;
    }
    
    .client-card:hover {
        transform: translateY(-4px);
        box-shadow: 0 12px 40px rgba(16, 185, 129, 0.15);
    }

    .client-card h2 {
        color: #b2f2bb;
        font-size: 22px;
        margin-bottom: 16px;
        display: flex;
        align-items: center;
        gap: 8px;
    }

    /* Sub Navigation */
    .client-sub-nav {
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

    /* Controls box */
    .controls-box {
        display: flex;
        gap: 12px;
        margin-bottom: 20px;
        flex-wrap: wrap;
        align-items: center;
    }

    .controls-box input, .controls-box select {
        padding: 10px 16px;
        border: 1px solid rgba(178, 242, 187, 0.3);
        border-radius: 10px;
        background: rgba(255, 255, 255, 0.1);
        color: #fff;
        font-size: 14px;
        outline: none;
        transition: all 0.3s ease;
    }

    .controls-box input:focus, .controls-box select:focus {
        border-color: #b2f2bb;
        background: rgba(255, 255, 255, 0.15);
    }

    .controls-box input {
        flex: 1;
        min-width: 200px;
    }

    /* Grid of Cards */
    .info-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(290px, 1fr));
        gap: 20px;
    }

    .detail-card {
        background: rgba(255, 255, 255, 0.05);
        border: 1px solid rgba(255, 255, 255, 0.1);
        border-radius: 12px;
        padding: 20px;
        transition: all 0.3s ease;
        position: relative;
    }

    .detail-card:hover {
        background: rgba(255, 255, 255, 0.08);
        border-color: rgba(178, 242, 187, 0.3);
        transform: translateY(-4px);
    }

    .detail-card h3 {
        font-size: 18px;
        color: #b2f2bb;
        margin-bottom: 8px;
    }

    .detail-card .meta-badge {
        display: inline-block;
        background: rgba(178, 242, 187, 0.1);
        color: #b2f2bb;
        padding: 4px 8px;
        border-radius: 6px;
        font-size: 11px;
        font-weight: 600;
        margin-bottom: 12px;
        border: 1px solid rgba(178, 242, 187, 0.15);
    }

    .detail-card .info-line {
        font-size: 13px;
        color: #e0e0e0;
        margin-bottom: 6px;
        display: flex;
        justify-content: space-between;
    }

    .detail-card .info-line span:first-child {
        font-weight: 600;
        color: #b2f2bb;
    }

    /* Badges */
    .badge {
        display: inline-block;
        padding: 4px 10px;
        border-radius: 20px;
        font-size: 11px;
        font-weight: 600;
    }

    .badge-danger {
        background: rgba(239, 68, 68, 0.15);
        color: #f87171;
        border: 1px solid rgba(239, 68, 68, 0.3);
    }

    .badge-warning {
        background: rgba(245, 158, 11, 0.15);
        color: #fbbf24;
        border: 1px solid rgba(245, 158, 11, 0.3);
    }

    .badge-success {
        background: rgba(16, 185, 129, 0.15);
        color: #34d399;
        border: 1px solid rgba(16, 185, 129, 0.3);
    }

    /* Custom Form & Badges on Mes Allergies */
    .allergy-badge {
        display: inline-flex;
        align-items: center;
        background: linear-gradient(135deg, rgba(239, 68, 68, 0.1), rgba(239, 68, 68, 0.15));
        border: 1px solid rgba(239, 68, 68, 0.25);
        color: #f87171;
        padding: 10px 18px;
        border-radius: 12px;
        font-size: 14px;
        font-weight: 600;
        margin: 6px;
        transition: all 0.25s ease;
    }

    .allergy-badge:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(239, 68, 68, 0.15);
    }

    .allergy-badge form {
        display: inline-flex;
        margin-left: 10px;
    }

    .allergy-delete-btn {
        background: none;
        border: none;
        color: #ef4444;
        cursor: pointer;
        font-size: 16px;
        font-weight: 700;
        padding: 0;
        line-height: 1;
        transition: transform 0.2s;
    }

    .allergy-delete-btn:hover {
        transform: scale(1.3);
        color: #f87171;
    }

    .allergy-add-form {
        display: flex;
        gap: 12px;
        margin-top: 20px;
        width: 100%;
        flex-wrap: wrap;
    }

    .allergy-select {
        flex: 1;
        min-width: 200px;
        padding: 12px 16px;
        border: 1px solid rgba(178, 242, 187, 0.3);
        border-radius: 12px;
        font-size: 14px;
        outline: none;
        background: rgba(255, 255, 255, 0.05);
        color: #fff;
    }

    .allergy-select option {
        background: #0a1914;
        color: #fff;
    }

    .allergy-btn-submit {
        padding: 12px 24px;
        background: linear-gradient(135deg, #10b981, #059669);
        color: #fff;
        border: none;
        border-radius: 12px;
        font-weight: 600;
        font-size: 14px;
        cursor: pointer;
        transition: all 0.25s ease;
    }

    .allergy-btn-submit:hover {
        background: linear-gradient(135deg, #059669, #047857);
        transform: translateY(-1px);
        box-shadow: 0 4px 12px rgba(16, 185, 129, 0.3);
    }

    .alert {
        padding: 12px 16px;
        border-radius: 12px;
        font-size: 14px;
        font-weight: 600;
        margin-bottom: 20px;
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

    .spinner {
        width: 40px;
        height: 40px;
        border: 3px solid rgba(178, 242, 187, 0.1);
        border-top: 3px solid #b2f2bb;
        border-radius: 50%;
        animation: spin 1s linear infinite;
        margin: 40px auto;
    }

    @keyframes spin {
        0% { transform: rotate(0deg); }
        100% { transform: rotate(360deg); }
    }

    .client-section {
        display: none;
    }

    .client-section.active {
        display: block;
        animation: fadeIn 0.3s ease;
    }

    @keyframes fadeIn {
        from { opacity: 0; transform: translateY(10px); }
        to { opacity: 1; transform: translateY(0); }
    }
</style>

<div class="client-sub-nav">
    <button class="sub-nav-btn active" onclick="switchSection('mes-allergies', this)">🤧 Mes Allergies</button>
    <button class="sub-nav-btn" onclick="switchSection('toutes-allergies', this)">🚫 Liste des Allergies</button>
    <button class="sub-nav-btn" onclick="switchSection('traitements', this)">💊 Liste des Traitements</button>
    <button class="sub-nav-btn" onclick="switchSection('associations', this)">🔗 Associations</button>
    <?php if (($_SESSION['role'] ?? '') === 'admin'): ?>
        <a class="sub-nav-btn" href="allergier_admin.php" style="margin-left: auto;">⚙️ Gestion Admin</a>
    <?php endif; ?>
</div>

<!-- SECTION: MES ALLERGIES -->
<div id="sec-mes-allergies" class="client-section active">
    <div class="client-card">
        <h2>🤧 Mes Allergies Déclarées</h2>

        <?php if ($msgSuccess !== ''): ?>
            <div class="alert alert-success">
                <span>✅</span> <?php echo h($msgSuccess); ?>
            </div>
        <?php endif; ?>

        <?php if ($msgError !== ''): ?>
            <div class="alert alert-danger">
                <span>⚠️</span> <?php echo h($msgError); ?>
            </div>
        <?php endif; ?>

        <div style="margin-bottom: 25px;">
            <p style="font-size: 14px; color: #ccc; margin-bottom: 20px;">Configurez les allergies enregistrées dans votre profil personnel pour que notre Assistant IA ECOSAVE puisse vous conseiller sur mesure.</p>
            
            <div style="display: flex; flex-wrap: wrap; margin: -6px;">
                <?php if (empty($userAllergies)): ?>
                    <p style="font-size: 14px; font-style: italic; color: #94a3b8; padding: 20px; text-align: center; width: 100%;">Aucune allergie enregistrée pour le moment. Restez en sécurité !</p>
                <?php else: ?>
                    <?php foreach ($userAllergies as $all): ?>
                        <div class="allergy-badge">
                            🤧 <?php echo h($all['nom']); ?>
                            <form method="POST" action="" class="allergie-del-form">
                                <input type="hidden" name="action_allergy" value="delete">
                                <input type="hidden" name="id_allergie" value="<?php echo $all['id_allergie']; ?>">
                                <button type="button" class="allergy-delete-btn" title="Retirer" onclick="confirmRetireAllergie(this)">&times;</button>
                            </form>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </div>

        <!-- Formulaire d'ajout d'allergie -->
        <form method="POST" action="" class="allergy-add-form" style="border-top: 1px solid rgba(178,242,187,0.15); padding-top: 20px;">
            <input type="hidden" name="action_allergy" value="add">
            <select name="id_allergie" class="allergy-select" required>
                <option value="">-- Ajouter une allergie... --</option>
                <?php foreach ($availableAllergies as $all): ?>
                    <option value="<?php echo $all['id_allergie']; ?>"><?php echo h($all['nom']); ?></option>
                <?php endforeach; ?>
            </select>
            <button type="submit" class="allergy-btn-submit">Associer à mon profil</button>
        </form>
    </div>
</div>

<!-- SECTION: TOUTES LES ALLERGIES -->
<div id="sec-toutes-allergies" class="client-section">
    <div class="client-card">
        <h2>🚫 Toutes les Allergies Répertoriées</h2>
        
        <div class="controls-box">
            <input type="text" id="search-allergies" placeholder="🔍 Rechercher une allergie par nom, type, niveau de danger..." oninput="filterAllergies()">
        </div>

        <div id="allergies-container" class="info-grid">
            <div class="spinner"></div>
        </div>
    </div>
</div>

<!-- SECTION: TRAITEMENTS -->
<div id="sec-traitements" class="client-section">
    <div class="client-card">
        <h2>💊 Tous les Traitements Médicaux</h2>
        
        <div class="controls-box">
            <input type="text" id="search-traitements" placeholder="🔍 Rechercher un traitement médical..." oninput="filterTraitements()">
        </div>

        <div id="traitements-container" class="info-grid">
            <div class="spinner"></div>
        </div>
    </div>
</div>

<!-- SECTION: ASSOCIATIONS -->
<div id="sec-associations" class="client-section">
    <div class="client-card">
        <h2>🔗 Associations Allergies & Traitements</h2>
        <p style="color: #ccc; font-size: 14px; margin-bottom: 20px;">Découvrez quels traitements sont recommandés pour chaque allergie répertoriée.</p>
        
        <div class="controls-box">
            <input type="text" id="search-associations" placeholder="🔍 Rechercher une association (allergie ou traitement)..." oninput="filterAssociations()">
        </div>

        <div id="associations-container" style="display: flex; flex-direction: column; gap: 20px;">
            <div class="spinner"></div>
        </div>
    </div>
</div>

<script>
    const API_BASE_ALLERGY = 'allergies_client_api.php';
    let loadedAllergies = [];
    let loadedTraitements = [];
    let loadedAssociations = [];

    function confirmRetireAllergie(btn) {
        const form = btn.closest('form');
        customConfirm({
            type: 'warning',
            icon: '🤧',
            badge: '⚠️ Gestion des allergies',
            title: 'Retirer cette allergie ?',
            message: 'Cette allergie sera retirée de votre profil de santé. Vous pourrez la réassocier à tout moment depuis cette page.',
            labelOk: 'Retirer',
            onConfirm: () => form.submit()
        });
    }

    function switchSection(sectionId, btn) {
        document.querySelectorAll('.client-section').forEach(sec => sec.classList.remove('active'));
        document.querySelectorAll('.sub-nav-btn').forEach(b => b.classList.remove('active'));
        
        document.getElementById('sec-' + sectionId).classList.add('active');
        if (btn) btn.classList.add('active');

        if (sectionId === 'toutes-allergies') loadAllergies();
        else if (sectionId === 'traitements') loadTraitements();
        else if (sectionId === 'associations') loadAssociations();
    }

    function loadAllergies() {
        const container = document.getElementById('allergies-container');
        container.innerHTML = '<div class="spinner"></div>';

        fetch(`${API_BASE_ALLERGY}?action=allergies_getAll`)
            .then(r => r.json())
            .then(res => {
                loadedAllergies = res.data || [];
                renderAllergies(loadedAllergies);
            });
    }

    function filterAllergies() {
        const query = document.getElementById('search-allergies').value.toLowerCase().trim();
        const filtered = loadedAllergies.filter(a => 
            a.nom.toLowerCase().includes(query) || 
            (a.type && a.type.toLowerCase().includes(query)) || 
            (a.niveau_danger && a.niveau_danger.toLowerCase().includes(query)) ||
            (a.symptomes && a.symptomes.toLowerCase().includes(query))
        );
        renderAllergies(filtered);
    }

    function renderAllergies(list) {
        const container = document.getElementById('allergies-container');
        if (list.length === 0) {
            container.innerHTML = '<p style="grid-column: 1/-1; text-align: center; color: #94a3b8; padding: 20px;">Aucune allergie trouvée.</p>';
            return;
        }

        let html = '';
        list.forEach(a => {
            const dangerBadge = a.niveau_danger === 'Élevé' || a.niveau_danger === 'Critique' || a.niveau_danger === 'Haut' ? 'badge-danger' : (a.niveau_danger === 'Modéré' || a.niveau_danger === 'Moyen' ? 'badge-warning' : 'badge-success');
            html += `
                <div class="detail-card">
                    <h3>🤧 ${a.nom}</h3>
                    <div class="meta-badge">${a.type || 'N/A'}</div>
                    <div class="info-line"><span>Danger:</span> <span class="badge ${dangerBadge}">${a.niveau_danger}</span></div>
                    <div class="info-line" style="flex-direction:column; align-items:flex-start; margin-top:10px;">
                        <span style="font-size:12px; margin-bottom:4px;">Description:</span>
                        <p style="font-size:12px; color:#ccc; margin:0;">${a.description || 'Aucune description disponible.'}</p>
                    </div>
                    <div class="info-line" style="flex-direction:column; align-items:flex-start; margin-top:10px;">
                        <span style="font-size:12px; margin-bottom:4px;">Symptômes:</span>
                        <p style="font-size:12px; color:#ccc; margin:0; font-style:italic;">${a.symptomes || 'N/A'}</p>
                    </div>
                </div>
            `;
        });
        container.innerHTML = html;
    }

    function loadTraitements() {
        const container = document.getElementById('traitements-container');
        container.innerHTML = '<div class="spinner"></div>';

        fetch(`${API_BASE_ALLERGY}?action=traitements_getAll`)
            .then(r => r.json())
            .then(res => {
                loadedTraitements = res.data || [];
                renderTraitements(loadedTraitements);
            });
    }

    function filterTraitements() {
        const query = document.getElementById('search-traitements').value.toLowerCase().trim();
        const filtered = loadedTraitements.filter(t => 
            t.nom.toLowerCase().includes(query) || 
            (t.type_traitement && t.type_traitement.toLowerCase().includes(query)) ||
            (t.effets_secondaires && t.effets_secondaires.toLowerCase().includes(query))
        );
        renderTraitements(filtered);
    }

    function renderTraitements(list) {
        const container = document.getElementById('traitements-container');
        if (list.length === 0) {
            container.innerHTML = '<p style="grid-column: 1/-1; text-align: center; color: #94a3b8; padding: 20px;">Aucun traitement trouvé.</p>';
            return;
        }

        let html = '';
        list.forEach(t => {
            html += `
                <div class="detail-card">
                    <h3>💊 ${t.nom}</h3>
                    <div class="meta-badge">${t.type_traitement || 'N/A'}</div>
                    <div class="info-line"><span>Dosage:</span> <strong>${t.dosage || 'N/A'}</strong></div>
                    <div class="info-line"><span>Durée:</span> <strong>${t.duree || 'N/A'}</strong></div>
                    <div class="info-line" style="flex-direction:column; align-items:flex-start; margin-top:10px;">
                        <span style="font-size:12px; margin-bottom:4px;">Effets Secondaires:</span>
                        <p style="font-size:12px; color:#f87171; margin:0; font-style:italic;">${t.effets_secondaires || 'Aucun effet secondaire répertorié.'}</p>
                    </div>
                </div>
            `;
        });
        container.innerHTML = html;
    }

    function loadAssociations() {
        const container = document.getElementById('associations-container');
        container.innerHTML = '<div class="spinner"></div>';

        fetch(`${API_BASE_ALLERGY}?action=associations_getAll`)
            .then(r => r.json())
            .then(res => {
                loadedAssociations = res.data || [];
                renderAssociations(loadedAssociations);
            });
    }

    function filterAssociations() {
        const query = document.getElementById('search-associations').value.toLowerCase().trim();
        const filtered = loadedAssociations.filter(a => 
            a.allergie_nom.toLowerCase().includes(query) || 
            a.traitement_nom.toLowerCase().includes(query) ||
            (a.type_traitement && a.type_traitement.toLowerCase().includes(query))
        );
        renderAssociations(filtered);
    }

    function renderAssociations(list) {
        const container = document.getElementById('associations-container');
        if (list.length === 0) {
            container.innerHTML = '<p style="text-align: center; color: #94a3b8; padding: 20px;">Aucune association trouvée.</p>';
            return;
        }

        // Group by allergy
        const groups = {};
        list.forEach(item => {
            if (!groups[item.id_allergie]) {
                groups[item.id_allergie] = {
                    nom: item.allergie_nom,
                    traitements: []
                };
            }
            groups[item.id_allergie].traitements.push({
                nom: item.traitement_nom,
                type: item.type_traitement
            });
        });

        let html = '';
        Object.keys(groups).forEach(id => {
            const g = groups[id];
            html += `
                <div class="client-card" style="background: rgba(255,255,255,0.03); margin-bottom: 0;">
                    <h2>🤧 Allergie : ${g.nom}</h2>
                    <h4 style="color: #b2f2bb; font-size: 14px; margin-bottom: 10px; border-top: 1px solid rgba(178,242,187,0.15); padding-top: 10px;">💊 Traitements associés (${g.traitements.length})</h4>
                    <div class="info-grid" style="grid-template-columns: repeat(auto-fill, minmax(220px, 1fr));">
            `;

            g.traitements.forEach(t => {
                html += `
                    <div class="detail-card" style="background: rgba(255,255,255,0.02); padding: 12px; border-radius: 8px;">
                        <h4 style="font-size: 14px; color: #fff; margin-bottom: 6px;">💊 ${t.nom}</h4>
                        <div style="font-size: 11px; color: #b2f2bb; font-weight: 600;">Type: ${t.type || 'N/A'}</div>
                    </div>
                `;
            });

            html += '</div></div>';
        });

        container.innerHTML = html;
    }
</script>

<?php require __DIR__ . '/partials/footer.php'; ?>
