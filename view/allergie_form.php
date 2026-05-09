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

require_once __DIR__ . '/../controller/allergiecontroller.php';

$active = 'allergies';

$controller = AllergieController::getInstance();
extract($controller->traiterRequetePageAllergieForm(), EXTR_OVERWRITE);

function field(string $name, array $ancien, ?array $row): string
{
    if (array_key_exists($name, $ancien)) {
        return (string) $ancien[$name];
    }
    if ($row && array_key_exists($name, $row)) {
        return (string) $row[$name];
    }
    return '';
}

$ALLERGIE_TYPES = [
    'medicament' => 'Médicament',
    'environnement' => 'Environnementale',
    'alimentaire' => 'Alimentaire',
    'contact' => 'De contact (peau)',
    'animale' => 'Animale',
    'insecte' => 'Piqûres d’insectes',
    'autre' => 'Autre',
];

$ALLERGIE_NIVEAUX = [
    'tres_leger' => 'Très léger',
    'leger' => 'Léger',
    'modere' => 'Modéré',
    'eleve' => 'Élevé',
    'critique' => 'Critique',
];

function allergie_type_cle(string $brut): string
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

function allergie_niveau_cle(string $brut): string
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

$typeCle = allergie_type_cle(field('type', $ancien, $row));
$niveauCle = allergie_niveau_cle(field('niveau_danger', $ancien, $row));

$pageTitle = ($mode === 'editer' ? 'Modifier' : 'Ajouter') . ' une Allergie';
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
    <a class="sub-nav-btn active" href="allergies.php">🌿 Allergies</a>
    <a class="sub-nav-btn" href="traitements.php">💊 Traitements</a>
    <a class="sub-nav-btn" href="associations.php">🔗 Associations</a>
    <a class="sub-nav-btn" href="allergier_admin.php?page=statistiques">📊 Statistiques</a>
</div>

<div class="crud-card">
    <h1><?php echo $mode === 'editer' ? 'Modifier une allergie' : 'Ajouter une allergie'; ?></h1>

    <?php if ($message !== ''): ?><div class="msg msg-success"><?php echo h($message); ?></div><?php endif; ?>
    <?php if ($erreurs): ?>
        <div class="msg msg-error"><ul><?php foreach ($erreurs as $e): ?><li><?php echo h($e); ?></li><?php endforeach; ?></ul></div>
    <?php endif; ?>

    <form class="allergier-form" data-form-type="allergie" method="post" action="allergie_form.php?action=<?php echo $mode === 'editer' ? 'modifier' : 'creer'; ?>" novalidate>
        <div class="allergier-form-errors alert-error" role="alert"></div>
        <?php if ($mode === 'editer'): ?>
            <input type="hidden" name="id_allergie" value="<?php echo (int) ($row['id_allergie'] ?? 0); ?>">
        <?php endif; ?>

        <div class="form-group">
            <label for="nom">Nom de l'allergie *</label>
            <input id="nom" name="nom" type="text" value="<?php echo h(field('nom', $ancien, $row)); ?>" required data-minlength="2" placeholder="Ex: Pollen, Pénicilline, Gluten...">
        </div>
        
        <div class="form-group">
            <label for="type">Catégorie de l'allergie *</label>
            <select id="type" name="type" required>
                <option value="">— Sélectionner un type —</option>
                <?php foreach ($ALLERGIE_TYPES as $valeur => $libelle): ?>
                    <option value="<?php echo h($valeur); ?>"<?php echo $typeCle === $valeur ? ' selected' : ''; ?>><?php echo h($libelle); ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        
        <div class="form-group">
            <label for="niveau_danger">Niveau de danger *</label>
            <select id="niveau_danger" name="niveau_danger" required>
                <option value="">— Sélectionner un niveau de danger —</option>
                <?php foreach ($ALLERGIE_NIVEAUX as $valeur => $libelle): ?>
                    <option value="<?php echo h($valeur); ?>"<?php echo $niveauCle === $valeur ? ' selected' : ''; ?>><?php echo h($libelle); ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        
        <div class="form-group">
            <label for="description">Description clinique *</label>
            <textarea id="description" name="description" rows="3" required data-minlength="5" placeholder="Saisir la description médicale, les allergènes impliqués..."></textarea>
        </div>
        
        <div class="form-group">
            <label for="symptomes">Symptômes physiologiques *</label>
            <textarea id="symptomes" name="symptomes" rows="3" required data-minlength="5" placeholder="Décrire les réactions physiques provoquées (Ex: Éruptions cutanées, dyspnée...)"></textarea>
        </div>

        <div class="form-actions">
            <a class="crud-btn" href="allergies.php">Annuler</a>
            <button type="submit" class="crud-btn primary">
                <?php echo $mode === 'editer' ? '💾 Enregistrer les modifications' : '➕ Ajouter l\'allergie'; ?>
            </button>
        </div>
    </form>
</div>

<script>
    // Pre-populate textarea values to escape script loading issue
    document.getElementById('description').value = <?php echo json_encode(field('description', $ancien, $row)); ?>;
    document.getElementById('symptomes').value = <?php echo json_encode(field('symptomes', $ancien, $row)); ?>;
</script>

<script src="../assets/js/allergier.js" defer></script>

<?php require __DIR__ . '/partials/footer.php'; ?>
