<?php
declare(strict_types=1);
session_start();
if (!function_exists('h')) {
    function h(?string $s): string
    {
        return htmlspecialchars((string) $s, ENT_QUOTES, 'UTF-8');
    }
}
require_once __DIR__ . '/../../controller/allergiecontroller.php';

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
?>
<!DOCTYPE html>
<html lang="fr">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title><?php echo $mode === 'editer' ? 'Modifier' : 'Ajouter'; ?> allergie</title>
<link rel="stylesheet" href="../asset/css/admin.css">
<script src="../asset/allergier.js" defer></script>
</head>
<body>
<div class="layout">
<aside class="sidebar" aria-label="Administration">
  <div class="sidebar-brand">
    <img class="sidebar-brand__logo-img" src="../asset/img/logo-ecosave.png" alt="ECOSAVE">
    <div>
      <div class="sidebar-brand__tag">Back-office</div>
    </div>
  </div>
  <nav class="sidebar-nav">
    <a class="sidebar-nav__link sidebar-nav__link--ghost" href="index.php">
      <span class="sidebar-nav__icon">⬅</span><span>Site public</span>
    </a>
    <a class="sidebar-nav__link<?php echo $active === 'dash' ? ' is-active' : ''; ?>" href="admin.php">
      <span class="sidebar-nav__icon">🏠</span><span>Tableau de bord</span>
    </a>
    <a class="sidebar-nav__link<?php echo $active === 'allergies' ? ' is-active' : ''; ?>" href="allergies.php">
      <span class="sidebar-nav__icon">🌿</span><span>Allergies</span>
    </a>
    <a class="sidebar-nav__link<?php echo $active === 'traitements' ? ' is-active' : ''; ?>" href="traitements.php">
      <span class="sidebar-nav__icon">💊</span><span>Traitements</span>
    </a>
    <a class="sidebar-nav__link<?php echo $active === 'associations' ? ' is-active' : ''; ?>" href="associations.php">
      <span class="sidebar-nav__icon">🔗</span><span>Associations</span>
    </a>
    <a class="sidebar-nav__link<?php echo $active === 'stats' ? ' is-active' : ''; ?>" href="admin.php?page=statistiques">
      <span class="sidebar-nav__icon">📊</span><span>Statistiques</span>
    </a>
  </nav>
</aside>
<main class="main">
  <h1><?php echo $mode === 'editer' ? 'Modifier une allergie' : 'Ajouter une allergie'; ?></h1>

  <?php if ($message !== ''): ?><div class="msg msg-success"><?php echo h($message); ?></div><?php endif; ?>
  <?php if ($erreurs): ?>
    <div class="msg msg-error"><ul><?php foreach ($erreurs as $e): ?><li><?php echo h($e); ?></li><?php endforeach; ?></ul></div>
  <?php endif; ?>

  <div class="form-panel">
  <form class="allergier-form" data-form-type="allergie" method="post" action="allergie_form.php?action=<?php echo $mode === 'editer' ? 'modifier' : 'creer'; ?>" novalidate>
    <div class="allergier-form-errors alert-error" role="alert"></div>
    <?php if ($mode === 'editer'): ?>
      <input type="hidden" name="id_allergie" value="<?php echo (int) ($row['id_allergie'] ?? 0); ?>">
    <?php endif; ?>

    <div class="form-group">
      <label for="nom">Nom *</label>
      <input id="nom" name="nom" type="text" value="<?php echo h(field('nom', $ancien, $row)); ?>" required data-minlength="2">
    </div>
    <div class="form-group">
      <label for="type">Type d'allergie *</label>
      <select id="type" name="type" required>
        <option value="">— Choisir un type —</option>
        <?php foreach ($ALLERGIE_TYPES as $valeur => $libelle): ?>
        <option value="<?php echo h($valeur); ?>"<?php echo $typeCle === $valeur ? ' selected' : ''; ?>><?php echo h($libelle); ?></option>
        <?php endforeach; ?>
      </select>
    </div>
    <div class="form-group">
      <label for="niveau_danger">Niveau de danger *</label>
      <select id="niveau_danger" name="niveau_danger" required>
        <option value="">— Choisir un niveau —</option>
        <?php foreach ($ALLERGIE_NIVEAUX as $valeur => $libelle): ?>
        <option value="<?php echo h($valeur); ?>"<?php echo $niveauCle === $valeur ? ' selected' : ''; ?>><?php echo h($libelle); ?></option>
        <?php endforeach; ?>
      </select>
    </div>
    <div class="form-group">
      <label for="description">Description *</label>
      <textarea id="description" name="description" rows="3" required data-minlength="5"><?php echo h(field('description', $ancien, $row)); ?></textarea>
    </div>
    <div class="form-group">
      <label for="symptomes">Symptômes *</label>
      <textarea id="symptomes" name="symptomes" rows="3" required data-minlength="5"><?php echo h(field('symptomes', $ancien, $row)); ?></textarea>
    </div>

    <div class="form-actions">
      <a class="btn btn-secondary" href="allergies.php">Annuler</a>
      <button type="submit" class="btn btn-primary">
        <?php echo $mode === 'editer' ? 'Enregistrer' : 'Ajouter'; ?>
      </button>
    </div>
  </form>
  </div>
</main>
</div>
</body>
</html>
