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
Metier::repondreExportPdfSiDemande('allergies');
require_once __DIR__ . '/../../controller/allergiecontroller.php';

$active = 'allergies';

$controller = AllergieController::getInstance();
extract($controller->traiterRequetePageAllergies(), EXTR_OVERWRITE);

$metier = new Metier();
$triAllergie = Metier::triAllergieDepuisGet($_GET);
$rows = $metier->rechercherAllergies(Metier::termeBarreDepuisGet($_GET), $triAllergie);

$pdfQueryAl = ['export' => 'pdf', 'tri' => $triAllergie];
if (Metier::termeBarreDepuisGet($_GET) !== '') {
    $pdfQueryAl['q'] = Metier::termeBarreDepuisGet($_GET);
}
$hrefPdfAllergies = 'allergies.php?' . http_build_query($pdfQueryAl);

$mapTypes = [
    'medicament' => 'Médicament',
    'environnement' => 'Environnementale',
    'alimentaire' => 'Alimentaire',
    'contact' => 'De contact (peau)',
    'animale' => 'Animale',
    'insecte' => 'Piqûres d’insectes',
    'autre' => 'Autre',
];
$mapNiveaux = [
    'tres_leger' => 'Très léger',
    'leger' => 'Léger',
    'modere' => 'Modéré',
    'eleve' => 'Élevé',
    'critique' => 'Critique',
];

function allergies_liste_type_cle(string $brut): string
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

function allergies_liste_niveau_cle(string $brut): string
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
?>
<!DOCTYPE html>
<html lang="fr">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Allergies</title>
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
  <div class="page-head">
    <h1>Allergies</h1>
    <div style="display:flex;gap:8px;flex-wrap:wrap;align-items:center;">
      <a class="btn btn-secondary" href="<?php echo h($hrefPdfAllergies); ?>">Export PDF</a>
      <a class="btn btn-primary" href="allergie_form.php?action=ajouter">Ajouter</a>
    </div>
  </div>

  <?php if (isset($_GET['pdf_err'])): ?>
    <div class="msg msg-error"><?php echo h((string) $_GET['pdf_err']); ?></div>
  <?php endif; ?>
  <?php if ($message !== ''): ?><div class="msg msg-success"><?php echo h($message); ?></div><?php endif; ?>
  <?php if ($erreurs): ?>
    <div class="msg msg-error"><ul><?php foreach ($erreurs as $e): ?><li><?php echo h($e); ?></li><?php endforeach; ?></ul></div>
  <?php endif; ?>

  <div class="form-panel" style="max-width:100%;margin-bottom:20px;">
    <form method="get" action="allergies.php" class="assoc-filtre" style="display:flex;flex-wrap:wrap;gap:12px;align-items:flex-end;">
      <input type="hidden" name="tri" value="<?php echo h($triAllergie); ?>">
      <div class="form-group" style="margin-bottom:0;flex:1;min-width:220px;">
        <label for="q">Recherche</label>
        <input id="q" type="search" name="q" value="<?php echo h((string) ($_GET['q'] ?? '')); ?>" placeholder="Nom, type, niveau, description, symptômes, n°…" autocomplete="off">
      </div>
      <div class="form-actions" style="margin-top:0;">
        <button type="submit" class="btn btn-primary">Rechercher</button>
        <a class="btn btn-secondary" href="allergies.php">Effacer</a>
      </div>
    </form>
    <p class="muted" style="margin:12px 0 0;font-size:0.9rem;">Trier (ordre alphabétique / logique) :</p>
    <div style="display:flex;flex-wrap:wrap;gap:8px;margin-top:8px;">
      <?php
      $qAl = (string) ($_GET['q'] ?? '');
      $liensTriAl = [
          'nom' => 'Nom',
          'type' => 'Type',
          'niveau_danger' => 'Niveau',
      ];
      foreach ($liensTriAl as $cle => $lib):
          $params = ['tri' => $cle];
          if ($qAl !== '') {
              $params['q'] = $qAl;
          }
          $href = 'allergies.php?' . http_build_query($params);
          $isOn = $triAllergie === $cle;
          ?>
      <a class="btn btn-sm<?php echo $isOn ? ' btn-primary' : ' btn-secondary'; ?>" href="<?php echo h($href); ?>"><?php echo h($lib); ?></a>
      <?php endforeach; ?>
    </div>
  </div>

  <div class="table-panel">
  <table>
    <thead>
      <tr>
        <th>Nom</th>
        <th>Type</th>
        <th>Niveau</th>
        <th>Description</th>
        <th>Symptômes</th>
        <th></th>
      </tr>
    </thead>
    <tbody>
      <?php if (!$rows): ?>
        <tr><td colspan="6" class="empty">Aucune allergie.</td></tr>
      <?php else: foreach ($rows as $r): ?>
        <tr>
          <td><strong><?php echo h((string) ($r['nom'] ?? '')); ?></strong></td>
          <td><?php
            $rawT = (string) ($r['type'] ?? '');
            $kT = allergies_liste_type_cle($rawT);
            echo h($kT !== '' ? ($mapTypes[$kT] ?? $kT) : $rawT);
          ?></td>
          <td><?php
            $rawN = (string) ($r['niveau_danger'] ?? '');
            $kN = allergies_liste_niveau_cle($rawN);
            echo h($kN !== '' ? ($mapNiveaux[$kN] ?? $kN) : $rawN);
          ?></td>
          <td class="cell-clip"><?php echo h((string) ($r['description'] ?? '')); ?></td>
          <td class="cell-clip"><?php echo h((string) ($r['symptomes'] ?? '')); ?></td>
          <td class="actions">
            <a class="btn btn-sm" href="allergie_form.php?action=editer&amp;id=<?php echo (int) ($r['id_allergie'] ?? 0); ?>">Modifier</a>
            <form method="post" action="allergies.php?action=supprimer" style="display:inline;">
              <input type="hidden" name="id_allergie" value="<?php echo (int) ($r['id_allergie'] ?? 0); ?>">
              <button type="submit" class="btn btn-sm btn-danger" data-confirm="Supprimer cette allergie ?">Supprimer</button>
            </form>
          </td>
        </tr>
      <?php endforeach; endif; ?>
    </tbody>
  </table>
  </div>
</main>
</div>
</body>
</html>
