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
Metier::repondreExportPdfSiDemande('associations');
require_once __DIR__ . '/../../controller/allergiecontroller.php';
require_once __DIR__ . '/../../controller/traitementcontroller.php';

$active = 'associations';

$ac = AllergieController::getInstance();
$tc = TraitementController::getInstance();
extract($ac->traiterRequetePageAssociations($tc), EXTR_OVERWRITE);

$metier = new Metier();
$triAssoc = Metier::triAssociationDepuisGet($_GET);
$rows = $metier->rechercherAssociations(
    $idAllergieFiltre,
    $idTraitementFiltre,
    Metier::termeBarreDepuisGet($_GET),
    $triAssoc
);

$pdfQueryAs = ['export' => 'pdf', 'tri' => $triAssoc];
if (Metier::termeBarreDepuisGet($_GET) !== '') {
    $pdfQueryAs['q'] = Metier::termeBarreDepuisGet($_GET);
}
if ($idAllergieFiltre > 0) {
    $pdfQueryAs['id_allergie'] = $idAllergieFiltre;
}
if ($idTraitementFiltre > 0) {
    $pdfQueryAs['id_traitement'] = $idTraitementFiltre;
}
$hrefPdfAssociations = 'associations.php?' . http_build_query($pdfQueryAs);

$mapTypesTrait = [
    'antihistaminique' => 'Antihistaminique',
    'corticoide' => 'Corticoïde',
    'bronchodilatateur' => 'Bronchodilatateur',
    'decongestionnant' => 'Décongestionnant',
    'adrenaline' => 'Adrénaline (urgence)',
    'immunotherapie' => 'Immunothérapie',
    'autre' => 'Autre',
];

function associations_liste_type_cle(string $brut): string
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
<title>Associations</title>
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
    <h1>Associations allergie / traitement</h1>
    <div style="display:flex;gap:8px;flex-wrap:wrap;align-items:center;">
      <a class="btn btn-secondary" href="<?php echo h($hrefPdfAssociations); ?>">Export PDF</a>
      <a class="btn btn-primary" href="association_form.php">Ajouter</a>
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
    <form method="get" action="associations.php" class="assoc-filtre">
      <input type="hidden" name="tri" value="<?php echo h($triAssoc); ?>">
      <div style="display:flex;flex-wrap:wrap;gap:12px;align-items:flex-end;">
        <div class="form-group" style="margin-bottom:0;min-width:200px;">
          <label for="id_allergie">Par allergie</label>
          <select id="id_allergie" name="id_allergie" onchange="if(this.form.id_traitement)this.form.id_traitement.value='';">
            <option value="">— Toutes —</option>
            <?php foreach ($listeAllergies as $al): ?>
              <option value="<?php echo (int) ($al['id_allergie'] ?? 0); ?>"<?php echo $idAllergieFiltre === (int) ($al['id_allergie'] ?? 0) ? ' selected' : ''; ?>>
                <?php echo h((string) ($al['nom'] ?? '')); ?>
              </option>
            <?php endforeach; ?>
          </select>
        </div>
        <div class="form-group" style="margin-bottom:0;min-width:200px;">
          <label for="id_traitement">Par traitement</label>
          <select id="id_traitement" name="id_traitement" onchange="if(this.form.id_allergie)this.form.id_allergie.value='';">
            <option value="">— Tous —</option>
            <?php foreach ($listeTraitements as $tr): ?>
              <option value="<?php echo (int) ($tr['id_traitement'] ?? 0); ?>"<?php echo $idTraitementFiltre === (int) ($tr['id_traitement'] ?? 0) ? ' selected' : ''; ?>>
                <?php echo h((string) ($tr['nom'] ?? '')); ?>
              </option>
            <?php endforeach; ?>
          </select>
        </div>
        <div class="form-group" style="margin-bottom:0;flex:1;min-width:220px;">
          <label for="q">Recherche globale</label>
          <input id="q" type="search" name="q" value="<?php echo h((string) ($_GET['q'] ?? '')); ?>" placeholder="Allergie, traitement, type, n° de lien…" autocomplete="off">
        </div>
        <div class="form-actions" style="margin-top:0;">
          <button type="submit" class="btn btn-primary">Rechercher</button>
          <a class="btn btn-secondary" href="associations.php">Effacer</a>
        </div>
      </div>
    </form>
    <p class="muted" style="margin:12px 0 0;font-size:0.9rem;">Trier :</p>
    <div style="display:flex;flex-wrap:wrap;gap:8px;margin-top:8px;">
      <?php
      $qAs = (string) ($_GET['q'] ?? '');
      $liensTriAs = [
          'allergie' => 'Par allergie (A → Z)',
          'traitement' => 'Par traitement (A → Z)',
      ];
      foreach ($liensTriAs as $cle => $lib):
          $params = ['tri' => $cle];
          if ($qAs !== '') {
              $params['q'] = $qAs;
          }
          if ($idAllergieFiltre > 0) {
              $params['id_allergie'] = $idAllergieFiltre;
          }
          if ($idTraitementFiltre > 0) {
              $params['id_traitement'] = $idTraitementFiltre;
          }
          $href = 'associations.php?' . http_build_query($params);
          $isOn = $triAssoc === $cle;
          ?>
      <a class="btn btn-sm<?php echo $isOn ? ' btn-primary' : ' btn-secondary'; ?>" href="<?php echo h($href); ?>"><?php echo h($lib); ?></a>
      <?php endforeach; ?>
    </div>
  </div>

  <div class="table-panel">
  <table>
    <thead>
        <tr>
          <th>Allergie</th>
          <th>Traitement</th>
          <th>Type</th>
          <th></th>
        </tr>
    </thead>
    <tbody>
      <?php if (!$rows): ?>
        <tr><td colspan="4" class="empty">Aucune association. Ajoutez des allergies et des traitements puis créez un lien.</td></tr>
      <?php else: foreach ($rows as $r): ?>
        <tr>
          <td><strong><?php echo h((string) ($r['allergie_nom'] ?? '')); ?></strong></td>
          <td><?php echo h((string) ($r['traitement_nom'] ?? '')); ?></td>
          <td><?php
            $rawTr = (string) ($r['type_traitement'] ?? '');
            $kTr = associations_liste_type_cle($rawTr);
            echo h($kTr !== '' ? ($mapTypesTrait[$kTr] ?? $kTr) : $rawTr);
          ?></td>
          <td class="actions">
            <form method="post" action="associations.php?action=supprimer" style="display:inline;">
              <input type="hidden" name="id_allergie" value="<?php echo (int) ($r['id_allergie'] ?? 0); ?>">
              <input type="hidden" name="id_traitement" value="<?php echo (int) ($r['id_traitement'] ?? 0); ?>">
              <button type="submit" class="btn btn-sm btn-danger" data-confirm="Supprimer cette association ?">Supprimer</button>
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
