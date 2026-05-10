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

require_once __DIR__ . '/../controller/traitementcontroller.php';

$active = 'traitements';

$controller = TraitementController::getInstance();
extract($controller->traiterRequetePageTraitementForm(), EXTR_OVERWRITE);

function tf(string $name, array $ancien, ?array $row): string
{
    if (array_key_exists($name, $ancien)) {
        return (string) $ancien[$name];
    }
    if ($row && array_key_exists($name, $row)) {
        return (string) $row[$name];
    }
    return '';
}

$TRAITEMENT_TYPES = [
    'antihistaminique' => 'Antihistaminique',
    'corticoide' => 'Corticoïde',
    'bronchodilatateur' => 'Bronchodilatateur',
    'decongestionnant' => 'Décongestionnant',
    'adrenaline' => 'Adrénaline (urgence)',
    'immunotherapie' => 'Immunothérapie',
    'autre' => 'Autre',
];

function traitement_type_cle(string $brut): string
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

$valeurBrute = tf('type_traitement', $ancien, $row);
$typeTraitementCle = traitement_type_cle($valeurBrute);
$isAutreCustom = ($valeurBrute !== '' && $typeTraitementCle === '');
if ($isAutreCustom) {
    $typeTraitementCle = 'autre';
}

$pageTitle = ($mode === 'editer' ? 'Modifier' : 'Ajouter') . ' un Traitement';
require __DIR__ . '/partials/header.php';
?>

<style>
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
        max-width: 650px;
        margin-left: auto;
        margin-right: auto;
    }
    .crud-card h1 {
        color: #065f46;
        font-size: 22px;
        margin: 0 0 16px;
        text-shadow: none;
        border-bottom: 1px solid #e5e7eb;
        padding-bottom: 12px;
    }
    .form-group {
        display: flex;
        flex-direction: column;
        gap: 6px;
        margin-bottom: 18px;
    }
    .form-group label {
        font-size: 14px;
        font-weight: 600;
        color: #374151;
    }
    .form-group input, .form-group select, .form-group textarea {
        width: 100%;
        padding: 10px 14px;
        border: 1px solid #d1d5db;
        border-radius: 8px;
        font-size: 14px;
        background: #ffffff;
        color: #1f2937;
        transition: border-color 0.2s, box-shadow 0.2s;
        font-family: inherit;
    }
    .form-group input:focus, .form-group select:focus, .form-group textarea:focus {
        outline: none;
        border-color: #10b981;
        box-shadow: 0 0 0 3px rgba(16, 185, 129, 0.15);
    }
    .hint {
        font-size: 11px;
        color: #6b7280;
        margin-top: 2px;
    }
    .form-actions {
        display: flex;
        justify-content: flex-end;
        gap: 12px;
        margin-top: 24px;
        border-top: 1px solid #e5e7eb;
        padding-top: 16px;
    }
    .crud-btn {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        padding: 10px 20px;
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
    .msg {
        padding: 12px 16px;
        border-radius: 8px;
        margin-bottom: 20px;
        font-size: 14px;
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
    .alert-error {
        background: #fef2f2;
        color: #991b1b;
        border: 1px solid #fee2e2;
        padding: 12px 16px;
        border-radius: 8px;
        margin-bottom: 16px;
        display: none;
        font-size: 13px;
    }
    .alert-error ul {
        margin: 6px 0 0 16px;
        padding: 0;
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
    <h1><?php echo $mode === 'editer' ? 'Modifier un traitement' : 'Ajouter un traitement'; ?></h1>

    <?php if ($message !== ''): ?><div class="msg msg-success"><?php echo h($message); ?></div><?php endif; ?>
    <?php if ($erreurs): ?>
        <div class="msg msg-error"><ul><?php foreach ($erreurs as $e): ?><li><?php echo h($e); ?></li><?php endforeach; ?></ul></div>
    <?php endif; ?>

    <form class="allergier-form" data-form-type="traitement" method="post" action="traitement_form.php?action=<?php echo $mode === 'editer' ? 'modifier' : 'creer'; ?>" novalidate>
        <div class="allergier-form-errors alert-error" role="alert"></div>
        <?php if ($mode === 'editer'): ?>
            <input type="hidden" name="id_traitement" value="<?php echo (int) ($row['id_traitement'] ?? 0); ?>">
        <?php endif; ?>

        <div class="form-group">
            <label for="nom">Nom de la molécule ou marque *</label>
            <input id="nom" name="nom" type="text" value="<?php echo h(tf('nom', $ancien, $row)); ?>" required data-minlength="2" placeholder="Ex: Cétirizine, Ventoline, Desloratadine...">
        </div>
        
        <div class="form-group">
            <label for="type_traitement">Type de traitement pharmacologique *</label>
            <select id="type_traitement" name="type_traitement" required>
                <option value="">— Sélectionner un type —</option>
                <?php foreach ($TRAITEMENT_TYPES as $valeur => $libelle): ?>
                    <option value="<?php echo h($valeur); ?>"<?php echo $typeTraitementCle === $valeur ? ' selected' : ''; ?>><?php echo h($libelle); ?></option>
                <?php endforeach; ?>
            </select>
            <button type="button" id="btn-ai-suggest" class="crud-btn" style="margin-top: 10px; font-size: 13px; align-self: flex-start;">🪄 Suggérer les détails cliniques (IA Groq)</button>
        </div>
        
        <div class="form-group" id="group-type-autre" style="<?php echo $typeTraitementCle === 'autre' ? '' : 'display: none;'; ?>">
            <label for="type_traitement_autre">Spécifier l'autre catégorie *</label>
            <input type="text" id="type_traitement_autre" name="type_traitement_autre" value="<?php echo $isAutreCustom ? h($valeurBrute) : ''; ?>" placeholder="Saisir la catégorie personnalisée">
        </div>
        
        <div class="form-group">
            <label for="dosage">Dosage *</label>
            <input id="dosage" name="dosage" type="text" value="<?php echo h(tf('dosage', $ancien, $row)); ?>" required placeholder="Ex: 10mg, 500mg, 100µg">
            <span class="hint">Indiquer la quantité active par prise. Format standard : nombre + unité (ex: 10mg).</span>
        </div>
        
        <div class="form-group">
            <label for="duree">Durée d'administration standard *</label>
            <input id="duree" name="duree" type="text" value="<?php echo h(tf('duree', $ancien, $row)); ?>" required placeholder="Ex: 7 jours, 1 mois, Chronique">
            <span class="hint">Format recommandé : nombre + intervalle de temps (ex: 5 jours).</span>
        </div>
        
        <div class="form-group">
            <label for="effets_secondaires">Effets secondaires connus *</label>
            <textarea id="effets_secondaires" name="effets_secondaires" rows="3" required data-minlength="5" placeholder="Saisir les réactions indésirables ou effets somnolents habituels..."></textarea>
        </div>

        <div class="form-actions">
            <a class="crud-btn" href="traitements.php">Annuler</a>
            <button type="submit" class="crud-btn primary">
                <?php echo $mode === 'editer' ? '💾 Enregistrer les modifications' : '➕ Ajouter le traitement'; ?>
            </button>
        </div>
    </form>
</div>

<script>
    // Pre-populate textarea values safely
    document.getElementById('effets_secondaires').value = <?php echo json_encode(tf('effets_secondaires', $ancien, $row)); ?>;
</script>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const btnAi = document.getElementById('btn-ai-suggest');
    const inputNom = document.getElementById('nom');
    const selectType = document.getElementById('type_traitement');
    const groupAutre = document.getElementById('group-type-autre');
    const inputTypeAutre = document.getElementById('type_traitement_autre');
    const inputDosage = document.getElementById('dosage');
    const inputDuree = document.getElementById('duree');
    const textareaEffets = document.getElementById('effets_secondaires');

    if (selectType && groupAutre && inputTypeAutre) {
        selectType.addEventListener('change', function() {
            if (this.value === 'autre') {
                groupAutre.style.display = 'block';
                inputTypeAutre.required = true;
            } else {
                groupAutre.style.display = 'none';
                inputTypeAutre.required = false;
            }
        });
    }

    if (btnAi && inputNom && selectType) {
        btnAi.addEventListener('click', function() {
            const nom = inputNom.value.trim();
            let type = selectType.value.trim();
            if (type === 'autre' && inputTypeAutre) {
                type = inputTypeAutre.value.trim();
            }

            if (!nom || !type) {
                showEcosaveAlert('⚠️ Veuillez d\'abord renseigner le nom et choisir (ou préciser) le type du traitement.');
                return;
            }

            const originalText = btnAi.textContent;
            btnAi.textContent = "⚡ Analyse par l'IA en cours...";
            btnAi.disabled = true;

            fetch('../controller/ajax_ai_details_traitement.php?nom=' + encodeURIComponent(nom) + '&type_traitement=' + encodeURIComponent(type))
                .then(response => response.json())
                .then(data => {
                    if (data.success && data.details) {
                        inputDosage.value = data.details.dosage || '';
                        inputDuree.value = data.details.duree || '';
                        textareaEffets.value = data.details.effets_secondaires || '';
                        
                        // Flash background color to show automated update success
                        [inputDosage, inputDuree, textareaEffets].forEach(el => {
                            el.style.backgroundColor = '#d1fae5'; // soft light green
                            setTimeout(() => el.style.backgroundColor = '', 2000);
                        });
                    } else if (data.error) {
                        showEcosaveAlert('❌ Erreur IA : ' + data.error);
                    }
                })
                .catch(err => {
                    console.error("Erreur IA: ", err);
                    showEcosaveAlert('❌ Une erreur de communication avec l\'IA est survenue.');
                })
                .finally(() => {
                    btnAi.textContent = originalText;
                    btnAi.disabled = false;
                });
        });
    }
});
</script>

<script src="../assets/js/allergier.js" defer></script>

<script>
function showEcosaveAlert(message) {
    let box = document.querySelector('.alert-error');
    if (!box) {
        box = document.createElement('div');
        box.className = 'alert-error';
        document.querySelector('.crud-card').prepend(box);
    }
    box.textContent = message;
    box.style.display = 'block';
    box.style.animation = 'none';
    setTimeout(() => { box.style.animation = ''; box.style.display = 'none'; }, 4000);
}
</script>

<?php require __DIR__ . '/partials/footer.php'; ?>
