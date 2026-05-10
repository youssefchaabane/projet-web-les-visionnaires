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

require_once __DIR__ . '/../controller/metier.php';
require_once __DIR__ . '/../controller/allergiecontroller.php';
require_once __DIR__ . '/../controller/traitementcontroller.php';

$ac = AllergieController::getInstance();
$tc = TraitementController::getInstance();

$nbAllergies = count($ac->afficherListeAdmin());
$nbTraitements = count($tc->afficherListeAdmin());
$nbAssoc = $ac->compterAssociations();

$page = isset($_GET['page']) ? trim((string) $_GET['page']) : '';
$afficherStats = ($page === 'statistiques');
$active = $afficherStats ? 'stats' : 'dash';
$titrePage = $afficherStats ? 'Statistiques' : 'Administration Allergies';

$pageTitle = $titrePage;
require __DIR__ . '/partials/header.php';
?>

<style>
    /* CSS Styles for elegant integrated design */
    .module-card {
        background: rgba(255, 255, 255, 0.95);
        border: 1px solid rgba(178, 242, 187, 0.25);
        padding: 24px;
        border-radius: 16px;
        box-shadow: 0 10px 25px rgba(0, 0, 0, 0.08);
        backdrop-filter: blur(10px);
        color: #1f2937;
        margin-bottom: 24px;
        transition: transform 0.3s ease, box-shadow 0.3s ease;
    }
    .module-card h2 {
        color: #10b981;
        margin-bottom: 12px;
        font-size: 22px;
        text-shadow: none;
    }
    .grid-dashboard {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(240px, 1fr));
        gap: 20px;
        margin-top: 20px;
    }
    .dash-btn-card {
        background: #ffffff;
        border: 1px solid #e5e7eb;
        border-radius: 14px;
        padding: 24px;
        text-decoration: none;
        color: #1f2937;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.03);
        transition: all 0.25s ease;
        display: flex;
        flex-direction: column;
        align-items: center;
        text-align: center;
    }
    .dash-btn-card:hover {
        transform: translateY(-4px);
        box-shadow: 0 12px 24px rgba(16, 185, 129, 0.15);
        border-color: #10b981;
    }
    .dash-btn-card .icon {
        font-size: 40px;
        margin-bottom: 12px;
    }
    .dash-btn-card h3 {
        font-size: 18px;
        margin-bottom: 6px;
        color: #065f46;
        text-shadow: none;
    }
    .dash-btn-card .count {
        font-size: 32px;
        font-weight: 800;
        color: #10b981;
        margin: 8px 0;
    }
    .dash-btn-card .desc {
        font-size: 13px;
        color: #6b7280;
    }

    /* Sub-tabs inside allergies admin space */
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

    /* Stats classes matching app/view/asset/css/admin.css */
    .stat-section {
        background: #ffffff;
        padding: 24px;
        border-radius: 14px;
        margin-bottom: 24px;
        border: 1px solid #e5e7eb;
    }
    .stat-section h2 {
        color: #065f46;
        margin-bottom: 6px;
        text-shadow: none;
    }
    .stat-choice-box {
        background: #f9fafb;
        border: 1px solid #e5e7eb;
        border-radius: 10px;
        padding: 12px 16px;
        margin-bottom: 16px;
    }
    .stat-choice-title {
        font-weight: 600;
        font-size: 14px;
        margin-bottom: 6px;
        color: #374151;
        text-shadow: none;
    }
    .stat-choice-form {
        display: flex;
        gap: 16px;
        align-items: center;
    }
    .stat-choice-item {
        display: flex;
        align-items: center;
        gap: 6px;
        font-size: 13px;
        cursor: pointer;
        color: #4b5563;
    }
    .stat-card--solo {
        background: #ffffff;
        padding: 12px 0;
    }
    .stat-card--solo h3 {
        font-size: 15px;
        color: #6b7280;
        margin-bottom: 12px;
        text-shadow: none;
    }
    .stat-pie-wrap {
        display: flex;
        align-items: center;
        gap: 24px;
        flex-wrap: wrap;
    }
    .stat-pie-chart {
        flex-shrink: 0;
    }
    .stat-pie-svg {
        display: block;
        width: 128px;
        height: 128px;
    }
    .stat-pie-svg path, .stat-pie-svg circle {
        stroke: #ffffff;
        stroke-width: 0.8;
    }
    .stat-pie-legend {
        list-style: none;
        padding: 0;
        margin: 0;
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
        gap: 8px;
        flex: 1;
    }
    .stat-pie-legend li {
        display: flex;
        align-items: center;
        gap: 8px;
        font-size: 13px;
        color: #374151;
    }
    .stat-pie-swatch {
        width: 14px;
        height: 14px;
        border-radius: 4px;
        display: inline-block;
    }
    .stat-pie-lab {
        font-weight: 500;
    }
    .stat-pie-val {
        color: #6b7280;
    }
    .intro {
        color: #ffffff;
        margin-bottom: 20px;
        font-size: 15px;
    }
</style>

<div class="admin-sub-nav">
    <a class="sub-nav-btn <?php echo $active === 'dash' ? 'active' : ''; ?>" href="allergier_admin.php">🏠 Tableau de bord</a>
    <a class="sub-nav-btn" href="allergies.php">🌿 Allergies</a>
    <a class="sub-nav-btn" href="traitements.php">💊 Traitements</a>
    <a class="sub-nav-btn" href="associations.php">🔗 Associations</a>
    <a class="sub-nav-btn <?php echo $active === 'stats' ? 'active' : ''; ?>" href="allergier_admin.php?page=statistiques">📊 Statistiques</a>
</div>

<?php if ($afficherStats): ?>
    <div class="module-card">
        <h2>Module Statistiques Allergies &amp; Traitements</h2>
        <p style="color: #6b7280; margin-bottom: 16px;">Consultez les graphiques de répartition dynamiques des données de santé enregistrées en base.</p>
        
        <?php (new Metier())->afficherCorpsStatistiquesHtml($_GET); ?>
    </div>
<?php else: ?>
    <h1 style="color:#ffffff; margin-bottom: 8px;">Gestion des Allergies &amp; Traitements</h1>
    <p class="intro">Accédez aux modules pour administrer la liste des allergies, de leurs traitements associés et des statistiques médicales.</p>

    <div class="module-card">
        <h2>Tableau de bord de l'administration</h2>
        <div class="grid-dashboard">
            <a class="dash-btn-card" href="allergies.php">
                <span class="icon">🌿</span>
                <h3>Allergies</h3>
                <div class="count"><?php echo $nbAllergies; ?></div>
                <span class="desc">Gérer les types d'allergies, les niveaux de danger et les symptômes.</span>
            </a>
            
            <a class="dash-btn-card" href="traitements.php">
                <span class="icon">💊</span>
                <h3>Traitements</h3>
                <div class="count"><?php echo $nbTraitements; ?></div>
                <span class="desc">Administrer les médicaments, les types, dosages et effets secondaires.</span>
            </a>
            
            <a class="dash-btn-card" href="associations.php">
                <span class="icon">🔗</span>
                <h3>Associations</h3>
                <div class="count"><?php echo $nbAssoc; ?></div>
                <span class="desc">Lier une allergie à son traitement adapté avec l'aide des suggestions IA.</span>
            </a>
            
            <a class="dash-btn-card" href="allergier_admin.php?page=statistiques">
                <span class="icon">📊</span>
                <h3>Statistiques</h3>
                <div class="count">📈</div>
                <span class="desc">Visualiser les camemberts de répartition dynamiques par type et niveau.</span>
            </a>
        </div>
    </div>
<?php endif; ?>

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

<?php require __DIR__ . '/partials/footer.php'; ?>
