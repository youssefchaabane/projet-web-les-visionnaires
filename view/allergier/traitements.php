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
Metier::repondreExportPdfSiDemande('traitements');
require_once __DIR__ . '/../../controller/traitementcontroller.php';

$active = 'traitements';

$controller = TraitementController::getInstance();
extract($controller->traiterRequetePageTraitements(), EXTR_OVERWRITE);

$metier = new Metier();
$triTraitement = Metier::triTraitementDepuisGet($_GET);
$rows = $metier->rechercherTraitements(Metier::termeBarreDepuisGet($_GET), $triTraitement);

$pdfQueryTr = ['export' => 'pdf', 'tri' => $triTraitement];
if (Metier::termeBarreDepuisGet($_GET) !== '') {
    $pdfQueryTr['q'] = Metier::termeBarreDepuisGet($_GET);
}
$hrefPdfTraitements = 'traitements.php?' . http_build_query($pdfQueryTr);

$mapTypesTrait = [
    'antihistaminique' => 'Antihistaminique',
    'corticoide' => 'Corticoïde',
    'bronchodilatateur' => 'Bronchodilatateur',
    'decongestionnant' => 'Décongestionnant',
    'adrenaline' => 'Adrénaline (urgence)',
    'immunotherapie' => 'Immunothérapie',
    'autre' => 'Autre',
];

function traitements_liste_type_cle(string $brut): string
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
?>
<!DOCTYPE html>
<html lang="fr">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Traitements</title>
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
    <h1>Traitements</h1>
    <div style="display:flex;gap:8px;flex-wrap:wrap;align-items:center;">
      <a class="btn btn-secondary" href="<?php echo h($hrefPdfTraitements); ?>">Export PDF</a>
      <a class="btn btn-primary" href="traitement_form.php?action=ajouter">Ajouter</a>
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
    <form method="get" action="traitements.php" class="assoc-filtre" style="display:flex;flex-wrap:wrap;gap:12px;align-items:flex-end;">
      <input type="hidden" name="tri" value="<?php echo h($triTraitement); ?>">
      <div class="form-group" style="margin-bottom:0;flex:1;min-width:220px;">
        <label for="q">Recherche</label>
        <input id="q" type="search" name="q" value="<?php echo h((string) ($_GET['q'] ?? '')); ?>" placeholder="Nom, type, dosage, durée, effets, n°…" autocomplete="off">
      </div>
      <div class="form-actions" style="margin-top:0;">
        <button type="submit" class="btn btn-primary">Rechercher</button>
        <a class="btn btn-secondary" href="traitements.php">Effacer</a>
      </div>
    </form>
    <p class="muted" style="margin:12px 0 0;font-size:0.9rem;">Trier (ordre alphabétique) :</p>
    <div style="display:flex;flex-wrap:wrap;gap:8px;margin-top:8px;">
      <?php
      $qTr = (string) ($_GET['q'] ?? '');
      $liensTriTr = [
          'nom' => 'Nom',
          'type_traitement' => 'Type',
          'dosage' => 'Dosage',
      ];
      foreach ($liensTriTr as $cle => $lib):
          $params = ['tri' => $cle];
          if ($qTr !== '') {
              $params['q'] = $qTr;
          }
          $href = 'traitements.php?' . http_build_query($params);
          $isOn = $triTraitement === $cle;
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
        <th>Dosage</th>
        <th>Durée</th>
        <th>Effets secondaires</th>
        <th></th>
      </tr>
    </thead>
    <tbody>
      <?php if (!$rows): ?>
        <tr><td colspan="6" class="empty">Aucun traitement.</td></tr>
      <?php else: foreach ($rows as $r): ?>
        <tr>
          <td><strong><?php echo h((string) ($r['nom'] ?? '')); ?></strong></td>
          <td><?php
            $rawTr = (string) ($r['type_traitement'] ?? '');
            $kTr = traitements_liste_type_cle($rawTr);
            echo h($kTr !== '' ? ($mapTypesTrait[$kTr] ?? $kTr) : $rawTr);
          ?></td>
          <td><?php echo h((string) ($r['dosage'] ?? '')); ?></td>
          <td><?php echo h((string) ($r['duree'] ?? '')); ?></td>
          <td class="cell-clip"><?php echo h((string) ($r['effets_secondaires'] ?? '')); ?></td>
          <td class="actions">
            <a class="btn btn-sm" href="traitement_form.php?action=editer&amp;id=<?php echo (int) ($r['id_traitement'] ?? 0); ?>">Modifier</a>
            <form method="post" action="traitements.php?action=supprimer" style="display:inline;">
              <input type="hidden" name="id_traitement" value="<?php echo (int) ($r['id_traitement'] ?? 0); ?>">
              <button type="submit" class="btn btn-sm btn-danger" data-confirm="Supprimer ce traitement ?">Supprimer</button>
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
