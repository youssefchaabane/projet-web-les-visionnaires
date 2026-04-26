<?php
declare(strict_types=1);
session_start();
if (!function_exists('h')) {
    function h(?string $s): string
    {
        return htmlspecialchars((string) $s, ENT_QUOTES, 'UTF-8');
    }
}
require_once __DIR__ . '/../../controller/metier.php';
require_once __DIR__ . '/../../controller/allergiecontroller.php';
require_once __DIR__ . '/../../controller/traitementcontroller.php';

$ac = AllergieController::getInstance();
$tc = TraitementController::getInstance();

$nbAllergies = count($ac->afficherListeAdmin());
$nbTraitements = count($tc->afficherListeAdmin());
$nbAssoc = $ac->compterAssociations();

$page = isset($_GET['page']) ? trim((string) $_GET['page']) : '';
$afficherStats = ($page === 'statistiques');
$active = $afficherStats ? 'stats' : 'dash';
$titrePage = $afficherStats ? 'Statistiques — ECOSAVE' : 'Admin — ECOSAVE';
$hrefStats = 'admin.php?page=statistiques';
?>
<!DOCTYPE html>
<html lang="fr">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title><?php echo h($titrePage); ?></title>
<link rel="stylesheet" href="../asset/css/admin.css?v=7">
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
    <a class="sidebar-nav__link<?php echo $active === 'stats' ? ' is-active' : ''; ?>" href="<?php echo h($hrefStats); ?>">
      <span class="sidebar-nav__icon">📊</span><span>Statistiques</span>
    </a>
  </nav>
</aside>
<main class="main">
<?php if ($afficherStats):
    (new Metier())->afficherCorpsStatistiquesHtml($_GET);
else: ?>
  <h1>Tableau de bord</h1>
  <p class="intro">Vue d’ensemble : accédez aux listes, formulaires et associations.</p>

  <div class="cards">
    <a class="card card-link" href="allergies.php">
      <h3>Allergies</h3>
      <p class="big"><?php echo $nbAllergies; ?></p>
      <span class="muted">Liste, ajout, modification et suppression</span>
    </a>
    <a class="card card-link" href="traitements.php">
      <h3>Traitements</h3>
      <p class="big"><?php echo $nbTraitements; ?></p>
      <span class="muted">Liste, ajout, modification et suppression</span>
    </a>
    <a class="card card-link" href="associations.php">
      <h3>Associations</h3>
      <p class="big"><?php echo $nbAssoc; ?></p>
      <span class="muted">Lier une allergie à un traitement existant</span>
    </a>
    <a class="card card-link" href="<?php echo h($hrefStats); ?>">
      <h3>Statistiques</h3>
      <p class="big">📊</p>
      <span class="muted">Types, niveaux, dosages et associations</span>
    </a>
  </div>
<?php endif; ?>
</main>
<?php if ($afficherStats): ?>
<script>
(function () {
  var k = 'ecosave_stats_anchor';
  var id = sessionStorage.getItem(k);
  if (id) {
    sessionStorage.removeItem(k);
    var el = document.getElementById(id);
    if (el) {
      el.scrollIntoView({ block: 'start', behavior: 'instant' });
    }
    return;
  }
  if (location.hash.length > 1) {
    var t = document.querySelector(location.hash);
    if (t) {
      t.scrollIntoView({ block: 'start', behavior: 'instant' });
    }
  }
})();
</script>
<?php endif; ?>
</div>
</body>
</html>
