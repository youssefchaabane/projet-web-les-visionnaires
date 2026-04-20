<?php
/**
 * Vue - Accueil (FrontOffice)
 */
$baseUrl = '/gestion-recettes';
$pageTitle = 'Accueil - Gestion des Recettes';
include __DIR__ . '/../layouts/header.php';
?>

<section class="hero py-5" style="background: linear-gradient(135deg, #2e7d32 0%, #66bb6a 100%); color: #ffffff;">
    <div class="container text-center">
        <h1 class="display-3 fw-bold">🥘 Passion Recettes</h1>
        <p class="lead fs-4">Découvrez des saveurs uniques et des plats faits maison</p>
        <a href="index.php?action=obtenirTous" class="btn btn-success btn-lg mt-3 px-5 py-3 rounded-pill shadow" style="background-color: #1b5e20; border: none;">Explorer les recettes</a>
    </div>
</section>

<div class="container my-5">
    <!-- Formulaire de recherche -->
    <section class="mb-5">
        <div class="card border-0 shadow-sm rounded-4 p-4" style="background: #e8f5e9;">
            <form method="GET" action="index.php" class="row g-3 justify-content-center">
                <input type="hidden" name="action" value="rechercher">
                <div class="col-md-8">
                    <input type="text" name="terme" class="form-control form-control-lg border-0 shadow-none px-4" 
                           placeholder="🔍 Rechercher un plat, un ingrédient..." required>
                </div>
                <div class="col-auto">
                    <button type="submit" class="btn btn-success btn-lg px-4" style="background-color: #2e7d32; border: none;">Recette !</button>
                </div>
            </form>
        </div>
    </section>

    <!-- Dernières recettes -->
    <section>
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2 class="fw-bold">🌟 Les Incontournables</h2>
            <a href="index.php?action=obtenirTous" class="text-success text-decoration-none">Voir tout →</a>
        </div>
        <div id="recipe-list" class="row g-4">
            <!-- Les recettes seront chargées via AJAX ou PHP si le contrôleur le fait -->
            <div class="text-center py-5 text-muted">
                Chargement des gourmandises...
            </div>
        </div>
    </section>
</div>

<script>
// Petit script pour charger les 3 dernières recettes sur l'accueil
async function loadRecent() {
    try {
        const res = await fetch('../../../index.php?controller=Recette&action=obtenirTous&limite=3');
        const data = await res.json();
        if(data.success && data.recettes) {
            const list = document.getElementById('recipe-list');
            if(data.recettes.length === 0) {
                list.innerHTML = '<div class="alert alert-info py-4 text-center">Aucune recette disponible pour le moment.</div>';
                return;
            }
            list.innerHTML = data.recettes.map(r => `
                <div class="col-md-4">
                    <div class="card h-100 border-0 shadow-sm rounded-4 overflow-hidden recipe-card-hover">
                        <div class="card-body p-4">
                            <span class="badge bg-${r.difficulte === 'facile' ? 'success' : (r.difficulte === 'moyen' ? 'warning' : 'danger')} mb-3">
                                ${r.difficulte.toUpperCase()}
                            </span>
                            <h4 class="card-title fw-bold">${r.nom}</h4>
                            <p class="card-text text-muted mb-4">${r.description.substring(0, 90)}...</p>
                            <div class="d-flex justify-content-between align-items-center mt-auto pt-3 border-top">
                                <span class="small text-muted">👥 ${r.nombre_personnes} pers.</span>
                                <span class="small text-muted">⏱️ ${parseInt(r.temps_preparation) + parseInt(r.temps_cuisson)} min</span>
                            </div>
                        </div>
                    </div>
                </div>
            `).join('');
        }
    } catch(e) {}
}
window.onload = loadRecent;
</script>

<style>
.recipe-card-hover { transition: all 0.3s ease; }
.recipe-card-hover:hover { transform: translateY(-10px); box-shadow: 0 15px 30px rgba(0,0,0,0.1) !important; }
</style>

<?php include __DIR__ . '/../layouts/footer.php'; ?>
