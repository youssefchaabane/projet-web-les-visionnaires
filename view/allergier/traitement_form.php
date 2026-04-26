<?php
declare(strict_types=1);
session_start();
if (!function_exists('h')) {
    function h(?string $s): string
    {
        return htmlspecialchars((string) $s, ENT_QUOTES, 'UTF-8');
    }
}
require_once __DIR__ . '/../../controller/traitementcontroller.php';

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

$typeTraitementCle = traitement_type_cle(tf('type_traitement', $ancien, $row));
?>
<!DOCTYPE html>
<html lang="fr">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title><?php echo $mode === 'editer' ? 'Modifier' : 'Ajouter'; ?> traitement</title>
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
  <h1><?php echo $mode === 'editer' ? 'Modifier un traitement' : 'Ajouter un traitement'; ?></h1>

  <?php if ($message !== ''): ?><div class="msg msg-success"><?php echo h($message); ?></div><?php endif; ?>
  <?php if ($erreurs): ?>
    <div class="msg msg-error"><ul><?php foreach ($erreurs as $e): ?><li><?php echo h($e); ?></li><?php endforeach; ?></ul></div>
  <?php endif; ?>

  <div class="form-panel">
  <form class="allergier-form" data-form-type="traitement" method="post" action="traitement_form.php?action=<?php echo $mode === 'editer' ? 'modifier' : 'creer'; ?>" novalidate>
    <div class="allergier-form-errors alert-error" role="alert"></div>
    <?php if ($mode === 'editer'): ?>
      <input type="hidden" name="id_traitement" value="<?php echo (int) ($row['id_traitement'] ?? 0); ?>">
    <?php endif; ?>

    <div class="form-group">
      <label for="nom">Nom *</label>
      <input id="nom" name="nom" type="text" value="<?php echo h(tf('nom', $ancien, $row)); ?>" required data-minlength="2">
    </div>
    <div class="form-group">
      <label for="type_traitement">Type de traitement *</label>
      <select id="type_traitement" name="type_traitement" required>
        <option value="">— Choisir un type —</option>
        <?php foreach ($TRAITEMENT_TYPES as $valeur => $libelle): ?>
        <option value="<?php echo h($valeur); ?>"<?php echo $typeTraitementCle === $valeur ? ' selected' : ''; ?>><?php echo h($libelle); ?></option>
        <?php endforeach; ?>
      </select>
    </div>
    <div class="form-group">
      <label for="dosage">Dosage * <span class="hint">ex. 10mg, 500mg</span></label>
      <input id="dosage" name="dosage" type="text" value="<?php echo h(tf('dosage', $ancien, $row)); ?>" required>
    </div>
    <div class="form-group">
      <label for="duree">Durée * <span class="hint">ex. 7 jours, 1mois</span></label>
      <input id="duree" name="duree" type="text" value="<?php echo h(tf('duree', $ancien, $row)); ?>" required>
    </div>
    <div class="form-group">
      <label for="effets_secondaires">Effets secondaires *</label>
      <textarea id="effets_secondaires" name="effets_secondaires" rows="3" required data-minlength="5"><?php echo h(tf('effets_secondaires', $ancien, $row)); ?></textarea>
    </div>

    <div class="form-actions">
      <a class="btn btn-secondary" href="traitements.php">Annuler</a>
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
