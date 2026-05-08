<?php
declare(strict_types=1);
session_start();

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

if (!function_exists('h')) {
    function h(?string $s): string
    {
        return htmlspecialchars((string) $s, ENT_QUOTES, 'UTF-8');
    }
}

$pageTitle = 'Consulter Notre Stock';
require __DIR__ . '/partials/header.php';
?>

<style>
    /* Styling elements specifically matching ECOSAVE premium glassmorphism design */
    .stock-card {
        background: rgba(255, 255, 255, 0.1);
        backdrop-filter: blur(12px);
        -webkit-backdrop-filter: blur(12px);
        border: 1px solid rgba(255, 255, 255, 0.15);
        border-radius: 16px;
        padding: 24px;
        box-shadow: 0 8px 32px rgba(0, 0, 0, 0.2);
        color: #ffffff;
        margin-bottom: 24px;
        transition: transform 0.3s ease, box-shadow 0.3s ease;
    }
    
    .stock-card:hover {
        transform: translateY(-4px);
        box-shadow: 0 12px 40px rgba(16, 185, 129, 0.15);
    }

    .stock-card h2 {
        color: #b2f2bb;
        font-size: 22px;
        margin-bottom: 16px;
        display: flex;
        align-items: center;
        gap: 8px;
    }

    /* Sub Navigation */
    .client-sub-nav {
        display: flex;
        flex-wrap: wrap;
        gap: 12px;
        margin-bottom: 24px;
    }

    .sub-nav-btn {
        padding: 10px 20px;
        border-radius: 999px;
        text-decoration: none;
        font-weight: 600;
        font-size: 14px;
        background: rgba(178, 242, 187, 0.1);
        color: #b2f2bb;
        border: 1px solid rgba(178, 242, 187, 0.25);
        transition: all 0.25s ease;
        cursor: pointer;
    }

    .sub-nav-btn:hover, .sub-nav-btn.active {
        background: #b2f2bb;
        color: #0a3d2a;
        border-color: #b2f2bb;
        transform: translateY(-2px);
    }

    /* Grid layout */
    .grid-dashboard {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
        gap: 20px;
        margin-bottom: 24px;
    }

    .dash-stat-card {
        background: rgba(255, 255, 255, 0.05);
        border: 1px solid rgba(255, 255, 255, 0.1);
        border-radius: 14px;
        padding: 20px;
        text-align: center;
        box-shadow: 0 4px 15px rgba(0,0,0,0.1);
        transition: all 0.3s ease;
    }

    .dash-stat-card:hover {
        transform: translateY(-4px);
        background: rgba(255, 255, 255, 0.08);
        border-color: rgba(178, 242, 187, 0.3);
    }

    .dash-stat-card .value {
        font-size: 36px;
        font-weight: 800;
        color: #b2f2bb;
        margin: 8px 0;
    }

    .dash-stat-card .label {
        font-size: 13px;
        color: #e0e0e0;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    /* Search and Sort box */
    .controls-box {
        display: flex;
        gap: 12px;
        margin-bottom: 20px;
        flex-wrap: wrap;
        align-items: center;
    }

    .controls-box input, .controls-box select {
        padding: 10px 16px;
        border: 1px solid rgba(178, 242, 187, 0.3);
        border-radius: 10px;
        background: rgba(255, 255, 255, 0.1);
        color: #fff;
        font-size: 14px;
        outline: none;
        transition: all 0.3s ease;
    }

    .controls-box input:focus, .controls-box select:focus {
        border-color: #b2f2bb;
        background: rgba(255, 255, 255, 0.15);
    }

    .controls-box input {
        flex: 1;
        min-width: 200px;
    }

    /* Grid of Product Cards */
    .products-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
        gap: 20px;
    }

    .prod-card {
        background: rgba(255, 255, 255, 0.05);
        border: 1px solid rgba(255, 255, 255, 0.1);
        border-radius: 12px;
        padding: 20px;
        transition: all 0.3s ease;
        position: relative;
    }

    .prod-card:hover {
        background: rgba(255, 255, 255, 0.08);
        border-color: rgba(178, 242, 187, 0.3);
        transform: translateY(-4px);
    }

    .prod-card h3 {
        font-size: 18px;
        color: #b2f2bb;
        margin-bottom: 8px;
    }

    .prod-card .cat-badge {
        display: inline-block;
        background: rgba(178, 242, 187, 0.1);
        color: #b2f2bb;
        padding: 4px 8px;
        border-radius: 6px;
        font-size: 11px;
        font-weight: 600;
        margin-bottom: 12px;
        border: 1px solid rgba(178, 242, 187, 0.15);
    }

    .prod-card .info-line {
        font-size: 13px;
        color: #e0e0e0;
        margin-bottom: 6px;
        display: flex;
        justify-content: space-between;
    }

    .prod-card .info-line span:first-child {
        font-weight: 600;
        color: #b2f2bb;
    }

    /* Badges */
    .badge {
        display: inline-block;
        padding: 4px 10px;
        border-radius: 20px;
        font-size: 11px;
        font-weight: 600;
    }

    .badge-success {
        background: rgba(16, 185, 129, 0.15);
        color: #34d399;
        border: 1px solid rgba(16, 185, 129, 0.3);
    }

    .badge-warning {
        background: rgba(245, 158, 11, 0.15);
        color: #fbbf24;
        border: 1px solid rgba(245, 158, 11, 0.3);
    }

    .badge-danger {
        background: rgba(239, 68, 68, 0.15);
        color: #f87171;
        border: 1px solid rgba(239, 68, 68, 0.3);
    }

    .spinner {
        width: 40px;
        height: 40px;
        border: 3px solid rgba(178, 242, 187, 0.1);
        border-top: 3px solid #b2f2bb;
        border-radius: 50%;
        animation: spin 1s linear infinite;
        margin: 40px auto;
    }

    @keyframes spin {
        0% { transform: rotate(0deg); }
        100% { transform: rotate(360deg); }
    }

    /* Page section visibility */
    .stock-section {
        display: none;
    }

    .stock-section.active {
        display: block;
        animation: fadeIn 0.3s ease;
    }

    @keyframes fadeIn {
        from { opacity: 0; transform: translateY(10px); }
        to { opacity: 1; transform: translateY(0); }
    }
</style>

<div class="client-sub-nav">
    <button class="sub-nav-btn active" onclick="switchStockSection('tous', this)">📦 Tous les Produits</button>
    <button class="sub-nav-btn" onclick="switchStockSection('categories', this)">🏷️ Par Catégorie</button>
    <button class="sub-nav-btn" onclick="switchStockSection('disponibles', this)">✅ Disponibles</button>
    <button class="sub-nav-btn" onclick="switchStockSection('alertes', this)">⚠️ Alertes Stock</button>
    <?php if (($_SESSION['role'] ?? '') === 'admin'): ?>
        <a class="sub-nav-btn" href="stock_admin.php" style="margin-left: auto;">⚙️ Gestion Admin</a>
    <?php endif; ?>
</div>

<!-- SECTION: TOUS LES PRODUITS -->
<div id="stock-sec-tous" class="stock-section active">
    <div class="stock-card">
        <h2>📦 Tous les Produits</h2>
        
        <div class="controls-box">
            <input type="text" id="search-tous" placeholder="🔍 Rechercher un produit..." oninput="filterTous()">
        </div>

        <div id="tous-products-container" class="products-grid">
            <div class="spinner"></div>
        </div>
    </div>
</div>

<!-- SECTION: PAR CATEGORIE -->
<div id="stock-sec-categories" class="stock-section">
    <div class="stock-card">
        <h2>🏷️ Browse by Category</h2>
        
        <div class="controls-box">
            <input type="text" id="search-categories" placeholder="🔍 Rechercher une catégorie..." oninput="filterCategories()">
        </div>

        <div id="categories-container" style="display: flex; flex-direction: column; gap: 24px;">
            <div class="spinner"></div>
        </div>
    </div>
</div>

<!-- SECTION: DISPONIBLES -->
<div id="stock-sec-disponibles" class="stock-section">
    <div class="stock-card">
        <h2>✅ Produits Disponibles</h2>
        
        <div class="controls-box">
            <input type="text" id="search-disponibles" placeholder="🔍 Rechercher parmi les disponibles..." oninput="filterDisponibles()">
        </div>

        <div id="disponibles-container" class="products-grid">
            <div class="spinner"></div>
        </div>
    </div>
</div>

<!-- SECTION: ALERTES -->
<div id="stock-sec-alertes" class="stock-section">
    <div class="stock-card">
        <h2>⚠️ Alertes de Stock</h2>
        <p style="color: #e0e0e0; font-size: 14px; margin-bottom: 20px;">Ces produits nécessitent de l'attention (stock bas ou en cours d'expiration).</p>

        <div id="alertes-container" class="products-grid">
            <div class="spinner"></div>
        </div>
    </div>
</div>

<script>
    const API_BASE = '../stock-api.php';
    let currentProduits = [];
    let currentCategories = [];
    let currentCategoriesMap = {};

    function switchStockSection(sectionId, btn) {
        document.querySelectorAll('.stock-section').forEach(sec => sec.classList.remove('active'));
        document.querySelectorAll('.sub-nav-btn').forEach(b => b.classList.remove('active'));
        
        document.getElementById('stock-sec-' + sectionId).classList.add('active');
        if (btn) btn.classList.add('active');

        if (sectionId === 'tous') loadTous();
        else if (sectionId === 'categories') loadCategories();
        else if (sectionId === 'disponibles') loadDisponibles();
        else if (sectionId === 'alertes') loadAlertes();
    }

    function loadAllData() {
        return Promise.all([
            fetch(`${API_BASE}?action=categorie_getAll`).then(r => r.json()),
            fetch(`${API_BASE}?action=produit_getAll`).then(r => r.json())
        ]).then(([cData, pData]) => {
            currentCategories = cData.data || [];
            currentProduits = pData.data || [];

            currentCategoriesMap = {};
            currentCategories.forEach(c => {
                currentCategoriesMap[c.id_cat] = c.nom_cat;
            });
        });
    }

    function loadTous() {
        loadAllData().then(() => {
            renderProducts(currentProduits, 'tous-products-container');
        });
    }

    function filterTous() {
        const query = document.getElementById('search-tous').value.trim().toLowerCase();
        const filtered = currentProduits.filter(p => p.nom_prod.toLowerCase().includes(query));
        renderProducts(filtered, 'tous-products-container');
    }

    function loadCategories() {
        loadAllData().then(() => {
            renderCategories();
        });
    }

    function filterCategories() {
        const query = document.getElementById('search-categories').value.trim().toLowerCase();
        renderCategories(query);
    }

    function loadDisponibles() {
        loadAllData().then(() => {
            const disp = currentProduits.filter(p => p.quantite_dispo > 0);
            renderProducts(disp, 'disponibles-container');
        });
    }

    function filterDisponibles() {
        const query = document.getElementById('search-disponibles').value.trim().toLowerCase();
        const disp = currentProduits.filter(p => p.quantite_dispo > 0 && p.nom_prod.toLowerCase().includes(query));
        renderProducts(disp, 'disponibles-container');
    }

    function loadAlertes() {
        loadAllData().then(() => {
            const today = new Date();
            const alertes = currentProduits.filter(p => 
                p.quantite_dispo <= 5 || 
                (p.date_expiration && new Date(p.date_expiration) <= new Date(today.getTime() + 7*24*60*60*1000))
            );
            renderProducts(alertes, 'alertes-container');
        });
    }

    function renderProducts(produits, containerId) {
        const container = document.getElementById(containerId);
        if (produits.length === 0) {
            container.innerHTML = '<p style="grid-column: 1/-1; text-align: center; color: #999; padding: 20px;">Aucun produit trouvé</p>';
            return;
        }

        let html = '';
        produits.forEach(p => {
            const badgeClass = p.quantite_dispo === 0 ? 'badge-danger' : (p.quantite_dispo <= 5 ? 'badge-warning' : 'badge-success');
            const statusText = p.quantite_dispo === 0 ? 'Rupture' : (p.quantite_dispo <= 5 ? 'Stock Bas' : 'Disponible');

            html += `
                <div class="prod-card">
                    <h3>📦 ${p.nom_prod}</h3>
                    <div class="cat-badge">${currentCategoriesMap[p.id_cat] || 'N/A'}</div>
                    <div class="info-line"><span>Statut:</span> <span class="badge ${badgeClass}">${statusText}</span></div>
                    <div class="info-line"><span>Quantité:</span> <strong>${p.quantite_dispo}</strong></div>
                    <div class="info-line"><span>Poids:</span> <strong>${p.poids_produit || 'N/A'} kg</strong></div>
                    <div class="info-line"><span>Expiration:</span> <strong>${p.date_expiration || 'N/A'}</strong></div>
                </div>
            `;
        });
        container.innerHTML = html;
    }

    function renderCategories(query = '') {
        const container = document.getElementById('categories-container');
        const filtered = currentCategories.filter(c => c.nom_cat.toLowerCase().includes(query));

        if (filtered.length === 0) {
            container.innerHTML = '<p style="text-align: center; color: #999; padding: 20px;">Aucune catégorie trouvée</p>';
            return;
        }

        let html = '';
        filtered.forEach(c => {
            const categoryProducts = currentProduits.filter(p => p.id_cat === c.id_cat);
            
            html += `
                <div class="stock-card" style="background: rgba(255,255,255,0.03); margin-bottom: 0;">
                    <h2>🏷️ ${c.nom_cat}</h2>
                    <p style="color: #ccc; font-size: 13px; margin-bottom: 15px;">${c.description_cat || 'Pas de description.'}</p>
                    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(180px, 1fr)); gap: 12px; margin-bottom: 15px; font-size: 12px;">
                        <div><strong style="color: #b2f2bb;">📍 Lieu:</strong> ${c.lieu_stockage || '-'}</div>
                        <div><strong style="color: #b2f2bb;">🌡️ Température:</strong> ${c.temp_conseille || '-'}</div>
                        <div><strong style="color: #b2f2bb;">⏳ Délai d'alerte:</strong> ${c.delai_alerte_jours} jours</div>
                    </div>

                    <h4 style="color: #b2f2bb; font-size: 14px; margin-bottom: 10px; border-top: 1px solid rgba(178,242,187,0.15); padding-top: 10px;">📦 Produits associés (${categoryProducts.length})</h4>
            `;

            if (categoryProducts.length === 0) {
                html += '<p style="color: #999; font-size: 12px;">Aucun produit dans cette catégorie</p>';
            } else {
                html += '<div class="products-grid">';
                categoryProducts.forEach(p => {
                    const badgeClass = p.quantite_dispo === 0 ? 'badge-danger' : (p.quantite_dispo <= 5 ? 'badge-warning' : 'badge-success');
                    const statusText = p.quantite_dispo === 0 ? 'Rupture' : (p.quantite_dispo <= 5 ? 'Stock Bas' : 'Disponible');

                    html += `
                        <div class="prod-card" style="background: rgba(255,255,255,0.02); padding: 12px; border-radius: 8px;">
                            <h4 style="font-size: 14px; color: #fff; margin-bottom: 6px;">📦 ${p.nom_prod}</h4>
                            <div class="info-line" style="font-size: 11px;"><span>Statut:</span> <span class="badge ${badgeClass}" style="padding: 2px 6px; font-size: 9px;">${statusText}</span></div>
                            <div class="info-line" style="font-size: 11px;"><span>Quantité:</span> <strong>${p.quantite_dispo}</strong></div>
                        </div>
                    `;
                });
                html += '</div>';
            }

            html += '</div>';
        });

        container.innerHTML = html;
    }

    // Initial load
    document.addEventListener('DOMContentLoaded', () => {
        loadTous();
    });
</script>

<?php require __DIR__ . '/partials/footer.php'; ?>
