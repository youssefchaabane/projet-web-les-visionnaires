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

$mapAlTypes = [
    'medicament' => 'Médicament',
    'environnement' => 'Environnementale',
    'alimentaire' => 'Alimentaire',
    'contact' => 'De contact (peau)',
    'animale' => 'Animale',
    'insecte' => 'Piqûres d’insectes',
    'autre' => 'Autre',
];
$mapAlNiveaux = [
    'tres_leger' => 'Très léger',
    'leger' => 'Léger',
    'modere' => 'Modéré',
    'eleve' => 'Élevé',
    'critique' => 'Critique',
];
$mapTrTypes = [
    'antihistaminique' => 'Antihistaminique',
    'corticoide' => 'Corticoïde',
    'bronchodilatateur' => 'Bronchodilatateur',
    'decongestionnant' => 'Décongestionnant',
    'adrenaline' => 'Adrénaline (urgence)',
    'immunotherapie' => 'Immunothérapie',
    'autre' => 'Autre',
];

function index_allergie_type_cle(string $brut): string
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

function index_allergie_niveau_cle(string $brut): string
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

function index_traitement_type_cle(string $brut): string
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

$ac = AllergieController::getInstance();
$tc = TraitementController::getInstance();

$allergies = $ac->afficherListeAdmin();
$traitements = $tc->afficherListeAdmin();
$associations = $ac->afficherAssociationsJoinToutes();
?>
<!DOCTYPE html>
<html lang="fr">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>ECOSAVE — Allergies &amp; traitements</title>
<link rel="stylesheet" href="../asset/css/index.css?v=3">
<script src="../asset/allergier.js?v=3" defer></script>
</head>
<body>

<input type="radio" name="front-tab" id="front-tab-allergies" class="front-tab-input" checked>
<input type="radio" name="front-tab" id="front-tab-traitements" class="front-tab-input">
<input type="radio" name="front-tab" id="front-tab-associations" class="front-tab-input">

<header class="site-header">
  <div class="site-header__brand">
    <img class="site-header__logo" src="../asset/img/logo-ecosave.png" alt="ECOSAVE">
  </div>
  <nav class="site-nav" aria-label="Navigation principale">
    <label for="front-tab-allergies" class="site-nav__tab">Allergies</label>
    <label for="front-tab-traitements" class="site-nav__tab">Traitements</label>
    <label for="front-tab-associations" class="site-nav__tab">Associations</label>
    <a class="site-nav__cta" href="admin.php">Administration</a>
  </nav>
</header>

<section class="hero">
  <div class="hero__inner">
    <h1>Mange mieux, gaspille moins</h1>
    <p>Consultez les allergies, les traitements et leurs associations (lecture seule).</p>
    <button type="button" class="btn-hero" onclick="location.href='admin.php'">Espace gestion</button>
  </div>
</section>

<main class="front-sections">
<section class="section front-panel" id="allergies" data-front-panel="allergies">
  <div class="panel-head">
    <div>
      <h2 class="section__title">Allergies</h2>
      <p class="section__subtitle"><?php echo count($allergies); ?> enregistrement(s)</p>
    </div>
  </div>
  <div class="card-grid">
    <?php if (!$allergies): ?>
      <div class="empty-card">Aucune allergie disponible.</div>
    <?php else: foreach ($allergies as $r): ?>
      <article class="info-card">
        <div class="info-card__top">
          <h3><?php echo h((string) ($r['nom'] ?? '')); ?></h3>
          <span class="badge"><?php
            $rn = (string) ($r['niveau_danger'] ?? '');
            $kn = index_allergie_niveau_cle($rn);
            echo h($kn !== '' ? ($mapAlNiveaux[$kn] ?? $kn) : $rn);
          ?></span>
        </div>
        <p class="info-card__meta">Type : <?php
          $rt = (string) ($r['type'] ?? '');
          $kt = index_allergie_type_cle($rt);
          echo h($kt !== '' ? ($mapAlTypes[$kt] ?? $kt) : $rt);
        ?></p>
        <p class="info-card__text"><?php echo h((string) ($r['symptomes'] ?? '')); ?></p>
      </article>
    <?php endforeach; endif; ?>
  </div>
</section>

<section class="section front-panel" id="traitements" data-front-panel="traitements">
  <div class="panel-head">
    <div>
      <h2 class="section__title">Traitements</h2>
      <p class="section__subtitle"><?php echo count($traitements); ?> enregistrement(s)</p>
    </div>
  </div>
  <div class="card-grid">
    <?php if (!$traitements): ?>
      <div class="empty-card">Aucun traitement disponible.</div>
    <?php else: foreach ($traitements as $r): ?>
      <article class="info-card">
        <div class="info-card__top">
          <h3><?php echo h((string) ($r['nom'] ?? '')); ?></h3>
          <span class="badge"><?php
            $rtr = (string) ($r['type_traitement'] ?? '');
            $ktr = index_traitement_type_cle($rtr);
            echo h($ktr !== '' ? ($mapTrTypes[$ktr] ?? $ktr) : $rtr);
          ?></span>
        </div>
        <p class="info-card__meta">Dosage : <?php echo h((string) ($r['dosage'] ?? '')); ?></p>
        <p class="info-card__meta">Durée : <?php echo h((string) ($r['duree'] ?? '')); ?></p>
        <p class="info-card__text"><?php echo h((string) ($r['effets_secondaires'] ?? '')); ?></p>
      </article>
    <?php endforeach; endif; ?>
  </div>
</section>

<section class="section front-panel" id="associations" data-front-panel="associations">
  <div class="panel-head">
    <div>
      <h2 class="section__title">Associations</h2>
      <p class="section__subtitle"><?php echo count($associations); ?> lien(s) allergie / traitement</p>
    </div>
  </div>
  <div class="card-grid">
    <?php if (!$associations): ?>
      <div class="empty-card">Aucune association disponible.</div>
    <?php else: foreach ($associations as $r): ?>
      <article class="info-card">
        <div class="info-card__top">
          <h3><?php echo h((string) ($r['allergie_nom'] ?? '')); ?></h3>
          <span class="badge">Association</span>
        </div>
        <p class="info-card__meta">Traitement : <?php echo h((string) ($r['traitement_nom'] ?? '')); ?></p>
        <p class="info-card__text">Type de traitement : <?php
          $rtr2 = (string) ($r['type_traitement'] ?? '');
          $ktr2 = index_traitement_type_cle($rtr2);
          echo h($ktr2 !== '' ? ($mapTrTypes[$ktr2] ?? $ktr2) : $rtr2);
        ?></p>
      </article>
    <?php endforeach; endif; ?>
  </div>
</section>
</main>

<footer class="site-footer">
  <p>2026 ECOSAVE — données issues de la base (affichage public, lecture seule)</p>
</footer>

</body>
</html>
