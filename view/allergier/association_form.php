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
require_once __DIR__ . '/../../controller/traitementcontroller.php';

$active = 'associations';

$ac = AllergieController::getInstance();
$tc = TraitementController::getInstance();
extract($ac->traiterRequetePageAssociationForm($tc), EXTR_OVERWRITE);
?>
<!DOCTYPE html>
<html lang="fr">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Nouvelle association</title>
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
  <h1>Nouvelle association</h1>
  <p class="intro">Liez une allergie déjà enregistrée à un traitement déjà enregistré.</p>

  <?php if ($message !== ''): ?><div class="msg msg-success"><?php echo h($message); ?></div><?php endif; ?>
  <?php if ($erreurs): ?>
    <div class="msg msg-error"><ul><?php foreach ($erreurs as $e): ?><li><?php echo h($e); ?></li><?php endforeach; ?></ul></div>
  <?php endif; ?>

  <div class="form-panel">
  <form class="allergier-form" data-form-type="association" method="post" action="association_form.php" novalidate>
    <div class="allergier-form-errors alert-error" role="alert"></div>

    <div class="form-group">
      <label for="id_allergie">Allergie *</label>
      <select id="id_allergie" name="id_allergie" required>
        <option value="">— Choisir —</option>
        <?php foreach ($allergies as $a): ?>
          <option value="<?php echo (int) ($a['id_allergie'] ?? 0); ?>" <?php echo (string)($ancien['id_allergie'] ?? '') === (string)($a['id_allergie'] ?? '') ? 'selected' : ''; ?>>
            <?php echo h((string) ($a['nom'] ?? '')); ?>
          </option>
        <?php endforeach; ?>
      </select>
    </div>
    <div class="form-group">
      <label for="id_traitement">Traitement *</label>
      <select id="id_traitement" name="id_traitement" required>
        <option value="">— Choisir —</option>
        <?php foreach ($traitements as $t): ?>
          <option value="<?php echo (int) ($t['id_traitement'] ?? 0); ?>" <?php echo (string)($ancien['id_traitement'] ?? '') === (string)($t['id_traitement'] ?? '') ? 'selected' : ''; ?>>
            <?php echo h((string) ($t['nom'] ?? '')); ?>
          </option>
        <?php endforeach; ?>
      </select>
    </div>

    <div class="form-actions">
      <a class="btn btn-secondary" href="associations.php">Annuler</a>
      <button type="submit" class="btn btn-primary">Créer l'association</button>
    </div>
  </form>
  </div>
</main>
</div>
</body>
</html>
