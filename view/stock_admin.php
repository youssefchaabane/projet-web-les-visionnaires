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

$pageTitle = 'Administration du Stock';
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
    .admin-sub-nav {
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

    /* Buttons styled matching main project buttons */
    .btn-action {
        padding: 10px 18px;
        border: none;
        border-radius: 10px;
        font-weight: 600;
        font-size: 14px;
        cursor: pointer;
        transition: all 0.3s ease;
        display: inline-flex;
        align-items: center;
        gap: 8px;
        text-decoration: none;
    }

    .btn-primary {
        background: linear-gradient(135deg, #10b981, #059669);
        color: white;
        box-shadow: 0 4px 15px rgba(16, 185, 129, 0.3);
    }

    .btn-primary:hover {
        background: linear-gradient(135deg, #059669, #047857);
        transform: translateY(-2px);
        box-shadow: 0 6px 20px rgba(16, 185, 129, 0.4);
    }

    .btn-danger {
        background: #ef4444;
        color: white;
    }

    .btn-danger:hover {
        background: #dc2626;
        transform: translateY(-2px);
    }

    .btn-warning {
        background: #f59e0b;
        color: white;
    }

    .btn-warning:hover {
        background: #d97706;
        transform: translateY(-2px);
    }

    .btn-info {
        background: #3b82f6;
        color: white;
    }

    .btn-info:hover {
        background: #2563eb;
        transform: translateY(-2px);
    }

    /* Table styles */
    .table-responsive {
        overflow-x: auto;
    }

    .stock-table {
        width: 100%;
        border-collapse: collapse;
        margin-top: 10px;
    }

    .stock-table th, .stock-table td {
        padding: 14px 16px;
        text-align: left;
        border-bottom: 1px solid rgba(178, 242, 187, 0.15);
    }

    .stock-table th {
        background: rgba(178, 242, 187, 0.08);
        color: #b2f2bb;
        font-weight: 600;
        text-transform: uppercase;
        font-size: 12px;
        letter-spacing: 0.5px;
    }

    .stock-table tr:hover {
        background: rgba(255, 255, 255, 0.03);
    }

    /* Badges */
    .badge {
        display: inline-block;
        padding: 4px 10px;
        border-radius: 20px;
        font-size: 12px;
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

    /* Modals styled with glassmorphism */
    .stock-modal {
        display: none;
        position: fixed;
        inset: 0;
        z-index: 9999;
        background: rgba(0,0,0,0.6);
        backdrop-filter: blur(8px);
        -webkit-backdrop-filter: blur(8px);
        align-items: center;
        justify-content: center;
        opacity: 0;
        transition: opacity 0.3s ease;
    }

    .stock-modal.show {
        display: flex;
        opacity: 1;
    }

    .stock-modal-content {
        background: rgba(10, 25, 20, 0.95);
        border: 1px solid rgba(178, 242, 187, 0.25);
        border-radius: 20px;
        width: 90%;
        max-width: 580px;
        box-shadow: 0 15px 35px rgba(0,0,0,0.5);
        overflow: hidden;
        color: #ffffff;
        transform: scale(0.9);
        transition: transform 0.3s ease;
    }

    .stock-modal.show .stock-modal-content {
        transform: scale(1);
    }

    .stock-modal-header {
        background: rgba(178, 242, 187, 0.08);
        padding: 20px 24px;
        border-bottom: 1px solid rgba(178, 242, 187, 0.15);
        display: flex;
        justify-content: space-between;
        align-items: center;
    }

    .stock-modal-header h3 {
        color: #b2f2bb;
        margin: 0;
        font-size: 18px;
    }

    .stock-modal-close {
        background: none;
        border: none;
        color: #ffffff;
        font-size: 24px;
        cursor: pointer;
        opacity: 0.7;
        transition: opacity 0.2s;
    }

    .stock-modal-close:hover {
        opacity: 1;
    }

    .stock-modal-body {
        padding: 24px;
    }

    .stock-modal-footer {
        padding: 16px 24px;
        border-top: 1px solid rgba(178, 242, 187, 0.15);
        background: rgba(178, 242, 187, 0.03);
        display: flex;
        justify-content: flex-end;
        gap: 12px;
    }

    .form-group {
        margin-bottom: 18px;
    }

    .form-group label {
        display: block;
        margin-bottom: 8px;
        color: #b2f2bb;
        font-size: 13px;
        font-weight: 600;
    }

    .form-group input, .form-group select, .form-group textarea {
        width: 100%;
        padding: 11px 14px;
        border: 1px solid rgba(178, 242, 187, 0.3);
        border-radius: 8px;
        background: rgba(255, 255, 255, 0.05);
        color: #ffffff;
        font-size: 14px;
        outline: none;
        transition: all 0.3s ease;
    }

    .form-group input:focus, .form-group select:focus, .form-group textarea:focus {
        border-color: #b2f2bb;
        background: rgba(255, 255, 255, 0.1);
    }

    /* Notification */
    #stock-toast {
        position: fixed;
        top: 24px;
        right: 24px;
        padding: 16px 24px;
        border-radius: 12px;
        box-shadow: 0 10px 25px rgba(0,0,0,0.3);
        z-index: 10000;
        display: none;
        animation: slideIn 0.3s ease forwards;
        color: #ffffff;
        font-weight: 600;
        font-size: 14px;
    }

    #stock-toast.success {
        background: #065f46;
        border-left: 5px solid #34d399;
    }

    #stock-toast.error {
        background: #991b1b;
        border-left: 5px solid #f87171;
    }

    #stock-toast.warning {
        background: #92400e;
        border-left: 5px solid #fbbf24;
    }

    #stock-toast.info {
        background: #1e40af;
        border-left: 5px solid #60a5fa;
    }

    @keyframes slideIn {
        from { transform: translateX(120%); opacity: 0; }
        to { transform: translateX(0); opacity: 1; }
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

<div class="admin-sub-nav">
    <button class="sub-nav-btn active" onclick="switchStockSection('dashboard', this)">🏠 Tableau de bord</button>
    <button class="sub-nav-btn" onclick="switchStockSection('produits', this)">📦 Produits</button>
    <button class="sub-nav-btn" onclick="switchStockSection('categories', this)">🏷️ Catégories</button>
    <button class="sub-nav-btn" onclick="switchStockSection('alertes', this)">⚠️ Alertes Stock</button>
    <button class="sub-nav-btn" onclick="switchStockSection('stats', this)">📈 Statistiques</button>
    <a class="sub-nav-btn" href="stock_client.php" style="margin-left: auto;">👁️ Vue Client</a>
</div>

<!-- SECTION: DASHBOARD -->
<div id="stock-sec-dashboard" class="stock-section active">
    <div class="stock-card">
        <h2>📊 Tableau de bord global</h2>
        <div class="grid-dashboard">
            <div class="dash-stat-card">
                <div class="label">Produits</div>
                <div class="value" id="stat-total-products">0</div>
            </div>
            <div class="dash-stat-card">
                <div class="label">Catégories</div>
                <div class="value" id="stat-total-categories">0</div>
            </div>
            <div class="dash-stat-card">
                <div class="label">Stock Bas</div>
                <div class="value" id="stat-low-stock">0</div>
            </div>
            <div class="dash-stat-card">
                <div class="label">À Expirer</div>
                <div class="value" id="stat-expiring">0</div>
            </div>
        </div>

        <div class="stock-card" style="background: rgba(255,255,255,0.02); border-color: rgba(255,255,255,0.08); padding: 18px; margin-top: 20px;">
            <h3 style="color: #b2f2bb; font-size: 16px; margin-bottom: 12px; font-weight: 600;">📋 Produits Récents</h3>
            <div id="latest-products-list">
                <div class="spinner"></div>
            </div>
        </div>
    </div>
</div>

<!-- SECTION: PRODUITS -->
<div id="stock-sec-produits" class="stock-section">
    <div class="stock-card">
        <h2>📦 Gestion des Produits</h2>
        
        <div class="controls-box">
            <input type="text" id="produit-search" placeholder="🔍 Rechercher un produit..." oninput="loadProduits()">
            <select id="produit-sort" onchange="loadProduits()">
                <option value="nom_asc">Nom A → Z</option>
                <option value="nom_desc">Nom Z → A</option>
                <option value="categorie_asc">Catégorie A → Z</option>
                <option value="categorie_desc">Catégorie Z → A</option>
            </select>
            <button class="btn-action btn-primary" onclick="openProduitModal()">➕ Ajouter produit</button>
            <button class="btn-action btn-danger" onclick="exportProduitsPDF()">📄 Exporter PDF</button>
        </div>

        <div class="table-responsive">
            <div id="produits-table-container">
                <div class="spinner"></div>
            </div>
        </div>
    </div>
</div>

<!-- SECTION: CATEGORIES -->
<div id="stock-sec-categories" class="stock-section">
    <div class="stock-card">
        <h2>🏷️ Gestion des Catégories</h2>
        
        <div class="controls-box">
            <input type="text" id="categorie-search" placeholder="🔍 Rechercher une catégorie..." oninput="loadCategories()">
            <select id="categorie-sort" onchange="loadCategories()">
                <option value="nom_asc">Nom A → Z</option>
                <option value="nom_desc">Nom Z → A</option>
                <option value="lieu_asc">Lieu de stockage A → Z</option>
                <option value="lieu_desc">Lieu de stockage Z → A</option>
            </select>
            <button class="btn-action btn-primary" onclick="openCategorieModal()">➕ Ajouter catégorie</button>
            <button class="btn-action btn-danger" onclick="exportCategoriesPDF()">📄 Exporter PDF</button>
        </div>

        <div class="table-responsive">
            <div id="categories-table-container">
                <div class="spinner"></div>
            </div>
        </div>
    </div>
</div>

<!-- SECTION: ALERTES -->
<div id="stock-sec-alertes" class="stock-section">
    <div class="stock-card">
        <h2>⚠️ Alertes de Stock</h2>
        
        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(280px, 1fr)); gap: 20px;">
            <div class="stock-card" style="background: rgba(255,255,255,0.02); margin-bottom:0;">
                <h3 style="color:#f59e0b; font-size:16px; margin-bottom:12px;">📉 Produits en stock bas</h3>
                <div id="low-stock-list-container">
                    <div class="spinner"></div>
                </div>
            </div>
            <div class="stock-card" style="background: rgba(255,255,255,0.02); margin-bottom:0;">
                <h3 style="color:#ef4444; font-size:16px; margin-bottom:12px;">📅 Produits expirés ou proches de l'expiration</h3>
                <div id="expiring-list-container">
                    <div class="spinner"></div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- SECTION: STATISTIQUES -->
<div id="stock-sec-stats" class="stock-section">
    <div class="stock-card">
        <h2>📈 Statistiques du Stock</h2>
        
        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(280px, 1fr)); gap: 20px;">
            <div class="stock-card" style="background: rgba(255,255,255,0.02); margin-bottom:0;">
                <h3 style="color:#b2f2bb; font-size:16px; margin-bottom:12px;">📊 Top Catégories</h3>
                <div id="stats-top-categories">
                    <div class="spinner"></div>
                </div>
            </div>
            <div class="stock-card" style="background: rgba(255,255,255,0.02); margin-bottom:0;">
                <h3 style="color:#b2f2bb; font-size:16px; margin-bottom:12px;">🍕 État général du stock</h3>
                <div id="stats-stock-status">
                    <div class="spinner"></div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- MODALS -->

<!-- Modal: Produit -->
<div id="produit-modal" class="stock-modal">
    <div class="stock-modal-content">
        <div class="stock-modal-header">
            <h3 id="produit-modal-title">Ajouter un produit</h3>
            <button class="stock-modal-close" onclick="closeProduitModal()">&times;</button>
        </div>
        <div class="stock-modal-body">
            <form id="produit-form">
                <input type="hidden" id="produit-id" value="">
                <div class="form-group">
                    <label for="produit-nom">Nom du produit *</label>
                    <input type="text" id="produit-nom" required minlength="3" maxlength="100">
                </div>
                <div class="form-group">
                    <label for="produit-categorie">Catégorie *</label>
                    <select id="produit-categorie" required>
                        <option value="">Sélectionner une catégorie...</option>
                    </select>
                </div>
                <div class="form-group">
                    <label for="produit-expiration">Date d'expiration</label>
                    <input type="date" id="produit-expiration">
                </div>
                <div class="form-group">
                    <label for="produit-poids">Poids (kg)</label>
                    <input type="number" id="produit-poids" step="0.001" placeholder="Ex: 0.150">
                </div>
                <div class="form-group">
                    <label for="produit-quantite">Quantité disponible *</label>
                    <input type="number" id="produit-quantite" required value="0">
                </div>
            </form>
        </div>
        <div class="stock-modal-footer">
            <button class="btn-action btn-danger" onclick="closeProduitModal()">Annuler</button>
            <button class="btn-action btn-primary" onclick="saveProduit()" id="btn-save-produit">💾 Enregistrer</button>
        </div>
    </div>
</div>

<!-- Modal: Catégorie -->
<div id="categorie-modal" class="stock-modal">
    <div class="stock-modal-content">
        <div class="stock-modal-header">
            <h3 id="categorie-modal-title">Ajouter une catégorie</h3>
            <button class="stock-modal-close" onclick="closeCategorieModal()">&times;</button>
        </div>
        <div class="stock-modal-body">
            <form id="categorie-form">
                <input type="hidden" id="categorie-id" value="">
                <div class="form-group">
                    <label for="categorie-nom">Nom de la catégorie *</label>
                    <input type="text" id="categorie-nom" required minlength="3" maxlength="100">
                </div>
                <div class="form-group">
                    <label for="categorie-description">Description</label>
                    <div style="display: flex; gap: 8px;">
                        <textarea id="categorie-description" rows="3" style="flex:1; resize:none;"></textarea>
                        <button type="button" class="btn-action btn-info" id="btn-generate-desc" onclick="generateDescriptionIA()" style="height: fit-content;">✨ IA</button>
                    </div>
                </div>
                <div class="form-group">
                    <label for="categorie-lieu">Lieu de stockage</label>
                    <input type="text" id="categorie-lieu" placeholder="Ex: Armoire A1">
                </div>
                <div class="form-group">
                    <label for="categorie-temp">Température conseillée</label>
                    <input type="text" id="categorie-temp" placeholder="Ex: 15-25°C">
                </div>
                <div class="form-group">
                    <label for="categorie-delai">Délai d'alerte (jours)</label>
                    <input type="number" id="categorie-delai" value="30">
                </div>
            </form>
        </div>
        <div class="stock-modal-footer">
            <button class="btn-action btn-danger" onclick="closeCategorieModal()">Annuler</button>
            <button class="btn-action btn-primary" onclick="saveCategorie()">Enregistrer</button>
        </div>
    </div>
</div>

<!-- Modal: QR Code -->
<div id="qr-modal" class="stock-modal">
    <div class="stock-modal-content" style="max-width: 400px;">
        <div class="stock-modal-header" style="background: linear-gradient(135deg, #10b981, #059669); border-bottom: none;">
            <h3 style="color:#ffffff;">📦 Code QR du Produit</h3>
            <button class="stock-modal-close" onclick="closeQRModal()">&times;</button>
        </div>
        <div class="stock-modal-body" style="background: radial-gradient(circle, #1a2f26 0%, #0c1813 100%); text-align: center; padding: 30px;">
            <div style="background: white; padding: 20px; border-radius: 16px; display: inline-block; box-shadow: 0 10px 25px rgba(0,0,0,0.3);">
                <div id="qrcode-container"></div>
            </div>
            <h4 id="qr-product-name" style="margin-top: 15px; color: #b2f2bb; font-size: 18px; font-weight: 700;"></h4>
            
            <div style="background: rgba(255,255,255,0.05); padding: 15px; border-radius: 12px; text-align: left; margin-top: 20px; border: 1px solid rgba(178,242,187,0.15);">
                <div style="display:flex; justify-content:space-between; margin-bottom:6px; font-size:13px;"><span style="color:#b2f2bb; font-weight:600;">Stock disponible:</span> <span id="qr-stock-val" style="font-weight:700;"></span></div>
                <div style="display:flex; justify-content:space-between; font-size:13px;"><span style="color:#b2f2bb; font-weight:600;">ID Produit:</span> <span id="qr-id-val" style="font-weight:700;"></span></div>
            </div>
        </div>
        <div class="stock-modal-footer" style="justify-content: center; gap: 10px;">
            <button class="btn-action btn-danger" onclick="closeQRModal()">Fermer</button>
            <button class="btn-action btn-info" onclick="downloadQRCode()">💾 Télécharger</button>
        </div>
    </div>
</div>

<!-- TOAST CONTAINER -->
<div id="stock-toast"></div>

<!-- LIBRARIES -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/qrcodejs/1.0.0/qrcode.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/jspdf@2.5.1/dist/jspdf.umd.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/jspdf-autotable@3.8.2/dist/jspdf.plugin.autotable.min.js"></script>
<!-- stock-validation.js intégré directement (remplacement du fichier externe manquant) -->
<script>
    function validerNomProduit(nom) {
        return nom && nom.length >= 3 && nom.length <= 100;
    }

    function validerQuantite(q) {
        return !isNaN(q) && q >= 0;
    }

    function validerNomCategorie(nom) {
        return nom && nom.length >= 3 && nom.length <= 100;
    }

    const API_BASE = './stock_api.php';
    let currentProduits = [];
    let currentCategories = [];
    let currentCategoriesMap = {};

    function switchStockSection(sectionId, btn) {
        document.querySelectorAll('.stock-section').forEach(sec => sec.classList.remove('active'));
        document.querySelectorAll('.sub-nav-btn').forEach(b => b.classList.remove('active'));
        
        document.getElementById('stock-sec-' + sectionId).classList.add('active');
        if (btn) btn.classList.add('active');

        if (sectionId === 'dashboard') loadDashboardStats();
        else if (sectionId === 'produits') loadProduits();
        else if (sectionId === 'categories') loadCategories();
        else if (sectionId === 'alertes') loadAlertes();
        else if (sectionId === 'stats') loadStats();
    }

    function showToast(message, type = 'info') {
        const toast = document.getElementById('stock-toast');
        toast.className = type;
        toast.textContent = message;
        toast.style.display = 'block';
        setTimeout(() => {
            toast.style.display = 'none';
        }, 4000);
    }

    function loadDashboardStats() {
        fetch(`${API_BASE}?action=produit_getAll`)
            .then(r => r.json())
            .then(pData => {
                const produits = pData.data || [];
                currentProduits = produits;
                document.getElementById('stat-total-products').textContent = produits.length;

                const lowStock = produits.filter(p => p.quantite_dispo <= 5).length;
                document.getElementById('stat-low-stock').textContent = lowStock;

                const today = new Date();
                const expiring = produits.filter(p => p.date_expiration && new Date(p.date_expiration) <= today).length;
                document.getElementById('stat-expiring').textContent = expiring;

                // Load recent products
                const recent = produits.slice(0, 5);
                let html = `
                    <table class="stock-table">
                        <thead>
                            <tr>
                                <th>Produit</th>
                                <th>Quantité</th>
                                <th>Expiration</th>
                            </tr>
                        </thead>
                        <tbody>
                `;
                if (recent.length === 0) {
                    html += `<tr><td colspan="3" style="text-align:center;">Aucun produit disponible</td></tr>`;
                } else {
                    recent.forEach(p => {
                        const badgeClass = p.quantite_dispo === 0 ? 'badge-danger' : (p.quantite_dispo <= 5 ? 'badge-warning' : 'badge-success');
                        html += `
                            <tr>
                                <td><strong>${p.nom_prod}</strong></td>
                                <td><span class="badge ${badgeClass}">${p.quantite_dispo}</span></td>
                                <td>${p.date_expiration || 'N/A'}</td>
                            </tr>
                        `;
                    });
                }
                html += '</tbody></table>';
                document.getElementById('latest-products-list').innerHTML = html;
            });

        fetch(`${API_BASE}?action=categorie_getAll`)
            .then(r => r.json())
            .then(cData => {
                const categories = cData.data || [];
                currentCategories = categories;
                document.getElementById('stat-total-categories').textContent = categories.length;
            });
    }

    function loadCategoriesForSelect() {
        fetch(`${API_BASE}?action=categorie_getAll`)
            .then(r => r.json())
            .then(cData => {
                const categories = cData.data || [];
                let html = '<option value="">Sélectionner une catégorie...</option>';
                categories.forEach(c => {
                    html += `<option value="${c.id_cat}">${c.nom_cat}</option>`;
                });
                document.getElementById('produit-categorie').innerHTML = html;
            });
    }

    function loadProduits() {
        const query = document.getElementById('produit-search').value.trim();
        const sort = document.getElementById('produit-sort').value;

        Promise.all([
            fetch(`${API_BASE}?action=categorie_getAll`).then(r => r.json()),
            fetch(`${API_BASE}?action=produit_getAll&search=${encodeURIComponent(query)}`).then(r => r.json())
        ]).then(([cData, pData]) => {
            const categories = cData.data || [];
            const produits = pData.data || [];

            const catMap = {};
            categories.forEach(c => {
                catMap[c.id_cat] = c.nom_cat;
            });
            currentCategoriesMap = catMap;

            let sorted = [...produits];
            if (sort === 'nom_asc') sorted.sort((a,b) => a.nom_prod.localeCompare(b.nom_prod));
            else if (sort === 'nom_desc') sorted.sort((a,b) => b.nom_prod.localeCompare(a.nom_prod));
            else if (sort === 'categorie_asc') sorted.sort((a,b) => (catMap[a.id_cat] || '').localeCompare(catMap[b.id_cat] || ''));
            else if (sort === 'categorie_desc') sorted.sort((a,b) => (catMap[b.id_cat] || '').localeCompare(catMap[a.id_cat] || ''));

            let html = `
                <table class="stock-table">
                    <thead>
                        <tr>
                            <th>Nom</th>
                            <th>Catégorie</th>
                            <th>Stock</th>
                            <th>Poids</th>
                            <th>Expiration</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
            `;
            if (sorted.length === 0) {
                html += `<tr><td colspan="6" style="text-align:center;">Aucun produit trouvé</td></tr>`;
            } else {
                sorted.forEach(p => {
                    const badgeClass = p.quantite_dispo === 0 ? 'badge-danger' : (p.quantite_dispo <= 5 ? 'badge-warning' : 'badge-success');
                    html += `
                        <tr>
                            <td><strong>${p.nom_prod}</strong></td>
                            <td>${catMap[p.id_cat] || 'N/A'}</td>
                            <td>
                                <div style="display:flex; align-items:center; gap:8px;">
                                    <button class="btn-action btn-primary" style="padding:4px 8px; font-size:12px;" onclick="adjustStock(${p.id_prod}, 1)">➕</button>
                                    <span class="badge ${badgeClass}" style="min-width:30px; text-align:center;">${p.quantite_dispo}</span>
                                    <button class="btn-action btn-danger" style="padding:4px 8px; font-size:12px;" onclick="adjustStock(${p.id_prod}, -1)">➖</button>
                                </div>
                            </td>
                            <td>${p.poids_produit || 'N/A'} kg</td>
                            <td>${p.date_expiration || 'N/A'}</td>
                            <td>
                                <div style="display:flex; gap:6px;">
                                    <button class="btn-action btn-warning" style="padding:6px 10px; font-size:12px;" onclick="editProduit(${p.id_prod})">✏️</button>
                                    <button class="btn-action btn-info" style="padding:6px 10px; font-size:12px;" 
                                        data-name="${p.nom_prod.replace(/"/g, '&quot;')}" 
                                        onclick="showQRCode(${p.id_prod}, this.dataset.name, ${p.quantite_dispo})">QR</button>
                                    <button class="btn-action btn-danger" style="padding:6px 10px; font-size:12px;" onclick="deleteProduit(${p.id_prod})">🗑️</button>
                                </div>
                            </td>
                        </tr>
                    `;
                });
            }
            html += '</tbody></table>';
            document.getElementById('produits-table-container').innerHTML = html;
        });
    }

    function adjustStock(id, diff) {
        const action = diff > 0 ? 'produit_augmenterStock' : 'produit_diminuerStock';
        const q = 1;

        fetch(API_BASE + '?action=' + action, {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ id_prod: id, quantite: q })
        })
        .then(r => r.json())
        .then(res => {
            if (res.success) {
                showToast('Stock mis à jour avec succès', 'success');
                loadProduits();
                loadDashboardStats();
            } else {
                showToast(res.error || 'Erreur lors de la mise à jour', 'error');
            }
        });
    }

    function loadCategories() {
        const query = document.getElementById('categorie-search').value.trim();
        const sort = document.getElementById('categorie-sort').value;

        fetch(`${API_BASE}?action=categorie_getAll&search=${encodeURIComponent(query)}`)
            .then(r => r.json())
            .then(cData => {
                const categories = cData.data || [];
                let sorted = [...categories];

                if (sort === 'nom_asc') sorted.sort((a,b) => a.nom_cat.localeCompare(b.nom_cat));
                else if (sort === 'nom_desc') sorted.sort((a,b) => b.nom_cat.localeCompare(a.nom_cat));
                else if (sort === 'lieu_asc') sorted.sort((a,b) => (a.lieu_stockage || '').localeCompare(b.lieu_stockage || ''));
                else if (sort === 'lieu_desc') sorted.sort((a,b) => (b.lieu_stockage || '').localeCompare(a.lieu_stockage || ''));

                let html = `
                    <table class="stock-table">
                        <thead>
                            <tr>
                                <th>Nom</th>
                                <th>Description</th>
                                <th>Lieu Stockage</th>
                                <th>Température</th>
                                <th>Alerte (jours)</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                `;
                if (sorted.length === 0) {
                    html += `<tr><td colspan="6" style="text-align:center;">Aucune catégorie trouvée</td></tr>`;
                } else {
                    sorted.forEach(c => {
                        html += `
                            <tr>
                                <td><strong>${c.nom_cat}</strong></td>
                                <td>${c.description_cat || '-'}</td>
                                <td>${c.lieu_stockage || '-'}</td>
                                <td>${c.temp_conseille || '-'}</td>
                                <td>${c.delai_alerte_jours} j</td>
                                <td>
                                    <div style="display:flex; gap:6px;">
                                        <button class="btn-action btn-warning" style="padding:6px 10px; font-size:12px;" onclick="editCategorie(${c.id_cat})">✏️</button>
                                        <button class="btn-action btn-danger" style="padding:6px 10px; font-size:12px;" onclick="deleteCategorie(${c.id_cat})">🗑️</button>
                                    </div>
                                </td>
                            </tr>
                        `;
                    });
                }
                html += '</tbody></table>';
                document.getElementById('categories-table-container').innerHTML = html;
            });
    }

    function loadAlertes() {
        fetch(`${API_BASE}?action=produit_getBasStock`)
            .then(r => r.json())
            .then(res => {
                const bas = res.data || [];
                let html = '<table class="stock-table"><thead><tr><th>Produit</th><th>Quantité</th></tr></thead><tbody>';
                if (bas.length === 0) {
                    html += '<tr><td colspan="2" style="text-align:center;">Aucun produit en stock bas</td></tr>';
                } else {
                    bas.forEach(p => {
                        html += `<tr><td><strong>${p.nom_prod}</strong></td><td><span class="badge badge-warning">${p.quantite_dispo}</span></td></tr>`;
                    });
                }
                html += '</tbody></table>';
                document.getElementById('low-stock-list-container').innerHTML = html;
            });

        fetch(`${API_BASE}?action=produit_getAll`)
            .then(r => r.json())
            .then(res => {
                const all = res.data || [];
                const today = new Date();
                const expiring = all.filter(p => p.date_expiration && new Date(p.date_expiration) <= today);

                let html = '<table class="stock-table"><thead><tr><th>Produit</th><th>Expiration</th></tr></thead><tbody>';
                if (expiring.length === 0) {
                    html += '<tr><td colspan="2" style="text-align:center;">Aucun produit expiré ou proche de l\'expiration</td></tr>';
                } else {
                    expiring.forEach(p => {
                        html += `<tr><td><strong>${p.nom_prod}</strong></td><td><span class="badge badge-danger">${p.date_expiration}</span></td></tr>`;
                    });
                }
                html += '</tbody></table>';
                document.getElementById('expiring-list-container').innerHTML = html;
            });
    }

    function loadStats() {
        Promise.all([
            fetch(`${API_BASE}?action=categorie_getAll`).then(r => r.json()),
            fetch(`${API_BASE}?action=produit_getAll`).then(r => r.json())
        ]).then(([cData, pData]) => {
            const categories = cData.data || [];
            const produits = pData.data || [];

            const catCounts = {};
            produits.forEach(p => {
                catCounts[p.id_cat] = (catCounts[p.id_cat] || 0) + 1;
            });

            const topCat = categories.map(c => ({
                nom: c.nom_cat,
                count: catCounts[c.id_cat] || 0
            })).sort((a,b) => b.count - a.count).slice(0, 5);

            let topHtml = '<ul style="list-style:none; padding:0; margin:0;">';
            topCat.forEach(c => {
                topHtml += `<li style="padding:10px 0; border-bottom:1px solid rgba(178,242,187,0.15); display:flex; justify-content:space-between;"><span>🏷️ ${c.nom}</span> <strong style="color:#b2f2bb;">${c.count} produits</strong></li>`;
            });
            topHtml += '</ul>';
            document.getElementById('stats-top-categories').innerHTML = topHtml;

            const stockOk = produits.filter(p => p.quantite_dispo > 5).length;
            const stockLow = produits.filter(p => p.quantite_dispo > 0 && p.quantite_dispo <= 5).length;
            const stockEmpty = produits.filter(p => p.quantite_dispo === 0).length;

            let statusHtml = `
                <div style="display:flex; flex-direction:column; gap:12px;">
                    <div style="display:flex; justify-content:space-between; align-items:center;">
                        <span>✅ En stock (> 5):</span> <strong class="badge badge-success">${stockOk} produits</strong>
                    </div>
                    <div style="display:flex; justify-content:space-between; align-items:center;">
                        <span>⚠️ Stock bas (1-5):</span> <strong class="badge badge-warning">${stockLow} produits</strong>
                    </div>
                    <div style="display:flex; justify-content:space-between; align-items:center;">
                        <span>❌ Rupture (0):</span> <strong class="badge badge-danger">${stockEmpty} produits</strong>
                    </div>
                </div>
            `;
            document.getElementById('stats-stock-status').innerHTML = statusHtml;
        });
    }

    function openProduitModal() {
        document.getElementById('produit-id').value = '';
        document.getElementById('produit-form').reset();
        document.getElementById('produit-modal-title').textContent = 'Ajouter un produit';
        loadCategoriesForSelect();
        document.getElementById('produit-modal').classList.add('show');
    }

    function closeProduitModal() {
        document.getElementById('produit-modal').classList.remove('show');
    }

    function saveProduit() {
        const id = document.getElementById('produit-id').value;
        const nom = document.getElementById('produit-nom').value.trim();
        const cat = document.getElementById('produit-categorie').value;
        const exp = document.getElementById('produit-expiration').value || null;
        const poids = document.getElementById('produit-poids').value || null;
        const q = document.getElementById('produit-quantite').value;

        if (!validerNomProduit(nom)) {
            showToast('Nom du produit invalide', 'error');
            return;
        }
        if (!cat) {
            showToast('Sélectionnez une catégorie', 'error');
            return;
        }
        if (!validerQuantite(parseInt(q))) {
            showToast('Quantité invalide', 'error');
            return;
        }

        const action = id ? 'produit_update' : 'produit_create';
        const payload = {
            nom_prod: nom,
            id_cat: parseInt(cat),
            date_expiration: exp,
            poids_produit: poids ? parseFloat(poids) : null,
            quantite_dispo: parseInt(q)
        };
        if (id) payload.id_prod = parseInt(id);

        const btn = document.getElementById('btn-save-produit');
        const originalHtml = btn.innerHTML;
        btn.innerHTML = '⏳ Enregistrement...';
        btn.disabled = true;

        fetch(`${API_BASE}?action=${action}`, {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify(payload)
        })
        .then(r => r.json())
        .then(res => {
            if (res.success) {
                showToast(id ? 'Produit mis à jour' : 'Produit créé', 'success');
                closeProduitModal();
                loadProduits();
                loadDashboardStats();
            } else {
                showToast(res.error || 'Erreur', 'error');
            }
        })
        .finally(() => {
            btn.innerHTML = originalHtml;
            btn.disabled = false;
        });
    }

    function editProduit(id) {
        fetch(`${API_BASE}?action=produit_getById&id=${id}`)
            .then(r => r.json())
            .then(res => {
                const p = res.data;
                document.getElementById('produit-id').value = p.id_prod;
                document.getElementById('produit-nom').value = p.nom_prod;
                document.getElementById('produit-expiration').value = p.date_expiration || '';
                document.getElementById('produit-poids').value = p.poids_produit || '';
                document.getElementById('produit-quantite').value = p.quantite_dispo;
                document.getElementById('produit-modal-title').textContent = 'Modifier un produit';

                // Load categories and select the current one
                fetch(`${API_BASE}?action=categorie_getAll`)
                    .then(r => r.json())
                    .then(cData => {
                        const categories = cData.data || [];
                        let html = '<option value="">Sélectionner une catégorie...</option>';
                        categories.forEach(c => {
                            html += `<option value="${c.id_cat}" ${c.id_cat == p.id_cat ? 'selected' : ''}>${c.nom_cat}</option>`;
                        });
                        document.getElementById('produit-categorie').innerHTML = html;
                        document.getElementById('produit-modal').classList.add('show');
                    });
            });
    }

    function deleteProduit(id) {
        if (!confirm('Voulez-vous vraiment supprimer ce produit ?')) return;

        fetch(`${API_BASE}?action=produit_delete`, {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ id_prod: id })
        })
        .then(r => r.json())
        .then(res => {
            if (res.success) {
                showToast('Produit supprimé', 'success');
                loadProduits();
                loadDashboardStats();
            } else {
                showToast(res.error || 'Erreur', 'error');
            }
        });
    }

    function openCategorieModal() {
        document.getElementById('categorie-id').value = '';
        document.getElementById('categorie-form').reset();
        document.getElementById('categorie-modal-title').textContent = 'Ajouter une catégorie';
        document.getElementById('categorie-modal').classList.add('show');
    }

    function closeCategorieModal() {
        document.getElementById('categorie-modal').classList.remove('show');
    }

    function saveCategorie() {
        const id = document.getElementById('categorie-id').value;
        const nom = document.getElementById('categorie-nom').value.trim();
        const desc = document.getElementById('categorie-description').value.trim();
        const lieu = document.getElementById('categorie-lieu').value.trim();
        const temp = document.getElementById('categorie-temp').value.trim();
        const delai = document.getElementById('categorie-delai').value;

        if (!validerNomCategorie(nom)) {
            showToast('Nom de catégorie invalide', 'error');
            return;
        }

        const action = id ? 'categorie_update' : 'categorie_create';
        const payload = {
            nom_cat: nom,
            description_cat: desc,
            lieu_stockage: lieu,
            temp_conseille: temp,
            delai_alerte_jours: parseInt(delai) || 30
        };
        if (id) payload.id_cat = parseInt(id);

        fetch(`${API_BASE}?action=${action}`, {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify(payload)
        })
        .then(r => r.json())
        .then(res => {
            if (res.success) {
                showToast(id ? 'Catégorie mise à jour' : 'Catégorie créée', 'success');
                closeCategorieModal();
                loadCategories();
                loadDashboardStats();
            } else {
                showToast(res.error || 'Erreur', 'error');
            }
        });
    }

    function editCategorie(id) {
        fetch(`${API_BASE}?action=categorie_getById&id=${id}`)
            .then(r => r.json())
            .then(res => {
                const c = res.data;
                document.getElementById('categorie-id').value = c.id_cat;
                document.getElementById('categorie-nom').value = c.nom_cat;
                document.getElementById('categorie-description').value = c.description_cat || '';
                document.getElementById('categorie-lieu').value = c.lieu_stockage || '';
                document.getElementById('categorie-temp').value = c.temp_conseille || '';
                document.getElementById('categorie-delai').value = c.delai_alerte_jours;
                document.getElementById('categorie-modal-title').textContent = 'Modifier une catégorie';
                document.getElementById('categorie-modal').classList.add('show');
            });
    }

    function deleteCategorie(id) {
        if (!confirm('Voulez-vous vraiment supprimer cette catégorie ?')) return;

        fetch(`${API_BASE}?action=categorie_delete`, {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ id_cat: id })
        })
        .then(r => r.json())
        .then(res => {
            if (res.success) {
                showToast('Catégorie supprimée', 'success');
                loadCategories();
                loadDashboardStats();
            } else {
                showToast(res.error || 'Erreur', 'error');
            }
        });
    }

    function generateDescriptionIA() {
        const nomCat = document.getElementById('categorie-nom').value.trim();
        if (!nomCat) {
            showToast('Saisissez d\'abord un nom de catégorie', 'warning');
            return;
        }

        const btn = document.getElementById('btn-generate-desc');
        const originalText = btn.innerHTML;
        btn.innerHTML = '⏳...';
        btn.disabled = true;

        fetch(`${API_BASE}?action=openai_generate_description`, {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ category_name: nomCat })
        })
        .then(r => r.json())
        .then(data => {
            if (data.success) {
                document.getElementById('categorie-description').value = data.description;
                showToast('Description générée par l\'IA !', 'success');
            } else {
                showToast('Erreur lors de la génération', 'error');
            }
        })
        .catch(() => showToast('Erreur de connexion', 'error'))
        .finally(() => {
            btn.innerHTML = originalText;
            btn.disabled = false;
        });
    }

    // QR Codes
    function showQRCode(id, nom, stock) {
        const text = `PRODUIT: ${nom} | ID: ${id} | STOCK: ${stock}`;
        const container = document.getElementById('qrcode-container');
        container.innerHTML = "";
        
        document.getElementById('qr-product-name').innerText = nom;
        document.getElementById('qr-stock-val').innerText = stock;
        document.getElementById('qr-id-val').innerText = id;

        new QRCode(container, {
            text: text,
            width: 180,
            height: 180,
            colorDark: "#000000",
            colorLight: "#ffffff"
        });

        document.getElementById('qr-modal').classList.add('show');
    }

    function closeQRModal() {
        document.getElementById('qr-modal').classList.remove('show');
    }

    function downloadQRCode() {
        const canvas = document.querySelector('#qrcode-container canvas');
        if (!canvas) {
            showToast('QR Code introuvable', 'error');
            return;
        }
        const dataUrl = canvas.toDataURL("image/png");
        const productName = document.getElementById('qr-product-name').innerText;
        const link = document.createElement('a');
        link.href = dataUrl;
        link.download = `QR_${productName.replace(/\s+/g, '_')}.png`;
        link.click();
    }

    // PDF Exports (jspdf and jspdf-autotable)
    function exportProduitsPDF() {
        if (!currentProduits.length) {
            showToast('Aucun produit à exporter', 'warning');
            return;
        }

        const { jsPDF } = window.jspdf;
        const doc = new jsPDF();
        doc.setFontSize(16);
        doc.text('ECOSAVE - Liste des Produits', 14, 16);
        doc.setFontSize(10);
        doc.text(`Date: ${new Date().toLocaleDateString('fr-FR')}`, 14, 22);

        const body = currentProduits.map(p => [
            p.nom_prod || '-',
            currentCategoriesMap[p.id_cat] || '-',
            String(p.quantite_dispo ?? '-'),
            p.poids_produit ? `${p.poids_produit} kg` : '-',
            p.date_expiration || '-'
        ]);

        doc.autoTable({
            head: [['Nom', 'Catégorie', 'Quantité', 'Poids', 'Expiration']],
            body,
            startY: 28
        });

        doc.save('produits-ecosave.pdf');
        showToast('Export PDF réussi', 'success');
    }

    function exportCategoriesPDF() {
        if (!currentCategories.length) {
            showToast('Aucune catégorie à exporter', 'warning');
            return;
        }

        const { jsPDF } = window.jspdf;
        const doc = new jsPDF();
        doc.setFontSize(16);
        doc.text('ECOSAVE - Liste des Catégories', 14, 16);
        doc.setFontSize(10);
        doc.text(`Date: ${new Date().toLocaleDateString('fr-FR')}`, 14, 22);

        const body = currentCategories.map(c => [
            c.nom_cat || '-',
            c.description_cat || '-',
            c.lieu_stockage || '-',
            c.temp_conseille || '-',
            `${c.delai_alerte_jours} jours`
        ]);

        doc.autoTable({
            head: [['Nom', 'Description', 'Lieu Stockage', 'Température', 'Alerte']],
            body,
            startY: 28
        });

        doc.save('categories-ecosave.pdf');
        showToast('Export PDF réussi', 'success');
    }

    // Initial load
    document.addEventListener('DOMContentLoaded', () => {
        loadDashboardStats();
    });
</script>

<?php require __DIR__ . '/partials/footer.php'; ?>
