<?php
declare(strict_types=1);

require_once __DIR__ . '/partials/auth.php';
require_admin();

$pageTitle = 'Accueil';
$base = app_base_from_script();
$urlListe = $base . '/view/liste.php';
$urlStatistiques = $base . '/view/statistiques.php';
require __DIR__ . '/partials/header.php';
?>

<style>
.bento-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
    gap: 20px;
    padding: 30px;
    perspective: 1000px;
}
.bento-item {
    background: rgba(255, 255, 255, 0.1);
    backdrop-filter: blur(10px);
    border: 1px solid rgba(255, 255, 255, 0.2);
    border-radius: 16px;
    padding: 20px;
    display: flex;
    flex-direction: column;
    justify-content: center;
    align-items: center;
    text-align: center;
    color: #fff;
    transition: all 0.3s ease;
    cursor: pointer;
    box-shadow: 0 8px 16px rgba(0, 0, 0, 0.1);
    transform-style: preserve-3d;
}
.bento-item:hover {
    transform: translateY(-10px) scale(1.05);
    box-shadow: 0 20px 40px rgba(0, 0, 0, 0.2);
}
.bento-item.large {
    grid-column: span 2;
    grid-row: span 2;
}
.bento-item.medium {
    grid-column: span 1;
    grid-row: span 1;
}
.bento-item.wide {
    grid-column: span 2;
    grid-row: span 1;
}
.bento-item.tall {
    grid-column: span 1;
    grid-row: span 2;
}
.bento-item .icon {
    font-size: 48px;
    margin-bottom: 10px;
}
.bento-item .title {
    font-size: 18px;
    font-weight: bold;
    margin-bottom: 10px;
    color: #b2f2bb;
}
.bento-item .data {
    font-size: 32px;
    font-weight: bold;
    color: #fff;
}
.bento-item .desc {
    font-size: 14px;
    color: #e0e0e0;
}
/* 3D animations for Stock, Allergies, Recipes */
.bento-stock:hover {
    transform: rotateX(15deg) rotateY(15deg) translateY(-10px);
}
.bento-allergies:hover {
    transform: rotateX(-15deg) rotateY(15deg) translateY(-10px);
}
.bento-recipes:hover {
    transform: rotateX(15deg) rotateY(-15deg) translateY(-10px);
}
/* Carbon footprint */
.carbon-footprint {
    background: rgba(255, 255, 255, 0.15);
    backdrop-filter: blur(20px);
    border: 1px solid rgba(255, 255, 255, 0.3);
}
.carbon-footprint .progress-bars {
    width: 100%;
    margin-top: 20px;
}
.progress-bar {
    width: 100%;
    height: 20px;
    background: rgba(255, 255, 255, 0.2);
    border-radius: 10px;
    margin-bottom: 10px;
    overflow: hidden;
}
.progress-fill {
    height: 100%;
    background: linear-gradient(90deg, #b2f2bb, #0a3d2a);
    border-radius: 10px;
    transition: width 1s ease;
}
.progress-label {
    display: flex;
    justify-content: space-between;
    font-size: 12px;
    color: #b2f2bb;
    margin-bottom: 5px;
}
</style>

<div style="padding: 20px;">
    <div class="bento-grid">
        <!-- Carbon Footprint Central -->
        <div class="bento-item large carbon-footprint" style="grid-column: 2 / span 2; grid-row: 2 / span 2;">
            <div class="icon">🌍</div>
            <div class="title">Empreinte Carbone</div>
        </div>
        <!-- Utilisateurs -->
        <div class="bento-item medium" onclick="window.location.href='<?php echo htmlspecialchars($urlListe, ENT_QUOTES, 'UTF-8'); ?>'">
            <div class="icon">👥</div>
            <div class="title">Utilisateurs</div>
        </div>
        <!-- Stock -->
        <div class="bento-item medium bento-stock">
            <div class="icon">📦</div>
            <div class="title">Stock</div>
        </div>
        <!-- Allergies -->
        <div class="bento-item medium bento-allergies">
            <div class="icon">🤧</div>
            <div class="title">Allergies</div>
        </div>
        <!-- Recettes -->
        <div class="bento-item medium bento-recipes">
            <div class="icon">🍽️</div>
            <div class="title">Recettes</div>
        </div>
        <!-- fil d'actualité -->
        <div class="bento-item medium">
            <div class="icon">📰</div>
            <div class="title">fil d'actualité</div>
        </div>
            </div>
</div>

<?php require __DIR__ . '/partials/footer.php'; ?>
