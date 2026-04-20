<?php
/**
 * Dashboard Client - Consultation du stock
 * Style ECOSAVE (Vert écologique)
 * Structure basée sur gestion-allergies
 * Point d'accès: http://localhost/gestion-stock/app/views/client-dashboard.php
 */
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mon Espace - ECOSAVE Stock</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f9f4;
        }
        
        header {
            background-color: #2e7d32;
            color: white;
            padding: 15px 40px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
        }
        
        .logo {
            font-size: 24px;
            font-weight: bold;
        }
        
        header a,
        header button {
            color: white;
            background: rgba(255,255,255,0.2);
            padding: 10px 20px;
            border-radius: 5px;
            text-decoration: none;
            font-size: 13px;
            border: none;
            cursor: pointer;
            transition: all 0.3s;
            margin-left: 10px;
        }
        
        header a:hover,
        header button:hover {
            background: rgba(255,255,255,0.3);
            transform: translateY(-2px);
            box-shadow: 0 4px 8px rgba(0,0,0,0.2);
        }
        
        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 40px 20px;
        }
        
        h1 {
            color: #2e7d32;
            margin-bottom: 10px;
            font-size: 28px;
        }
        
        .subtitle {
            color: #666;
            margin-bottom: 30px;
            font-size: 14px;
        }
        
        .tabs {
            display: flex;
            gap: 20px;
            margin-bottom: 20px;
            border-bottom: 2px solid #e0e0e0;
        }
        
        .tab-btn {
            background: none;
            border: none;
            padding: 12px 20px;
            border-bottom: 3px solid transparent;
            cursor: pointer;
            font-size: 15px;
            font-weight: 500;
            color: #666;
            transition: all 0.3s;
            margin-bottom: -2px;
        }
        
        .tab-btn:hover {
            color: #2e7d32;
            border-bottom-color: #2e7d32;
        }
        
        .tab-btn.active {
            color: #2e7d32;
            border-bottom-color: #2e7d32;
        }
        
        .tab-content {
            display: none;
        }
        
        .tab-content.active {
            display: block;
        }
        
        .info-box {
            background: #e8f5e9;
            border-left: 4px solid #2e7d32;
            padding: 15px 20px;
            margin-bottom: 20px;
            border-radius: 5px;
            color: #1b5e20;
            font-size: 13px;
        }
        
        .search-box {
            margin-bottom: 20px;
        }
        
        .search-box input {
            width: 100%;
            max-width: 400px;
            padding: 12px 15px;
            border: 2px solid #ddd;
            border-radius: 5px;
            font-size: 13px;
            transition: all 0.3s;
        }
        
        .search-box input:focus {
            border-color: #2e7d32;
            outline: none;
            box-shadow: 0 0 0 3px rgba(46, 125, 50, 0.1);
        }
        
        .items-list {
            display: grid;
            gap: 20px;
        }
        
        .item-card {
            background: white;
            border-left: 4px solid #66bb6a;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
            transition: all 0.3s;
        }
        
        .item-card:hover {
            box-shadow: 0 4px 12px rgba(0,0,0,0.15);
            transform: translateY(-2px);
        }
        
        .item-card h5 {
            color: #2e7d32;
            margin-bottom: 10px;
            font-size: 16px;
        }
        
        .item-card p {
            margin: 5px 0;
            color: #666;
            font-size: 13px;
        }
        
        .badge {
            display: inline-block;
            padding: 5px 10px;
            border-radius: 12px;
            font-size: 11px;
            font-weight: 600;
            margin-right: 8px;
            margin-bottom: 5px;
        }
        
        .badge.category {
            background: #c8e6c9;
            color: #1b5e20;
        }
        
        .badge.status-ok {
            background: #c8e6c9;
            color: #1b5e20;
        }
        
        .badge.status-warning {
            background: #ffe0b2;
            color: #e65100;
        }
        
        .badge.status-danger {
            background: #ffcdd2;
            color: #c62828;
        }
        
        .details {
            margin-top: 12px;
            padding-top: 12px;
            border-top: 1px solid #e0e0e0;
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
            gap: 10px;
            font-size: 12px;
        }
        
        .detail-item {
            display: flex;
            justify-content: space-between;
        }
        
        .detail-label {
            font-weight: 600;
            color: #666;
        }
        
        .detail-value {
            color: #333;
        }
        
        .table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 15px;
        }
        
        .table thead th {
            background: #f4f9f4;
            border-bottom: 2px solid #e0e0e0;
            padding: 12px;
            text-align: left;
            font-weight: 600;
            color: #333;
            text-transform: uppercase;
            font-size: 12px;
        }
        
        .table tbody td {
            border-bottom: 1px solid #e0e0e0;
            padding: 12px;
            font-size: 13px;
        }
        
        .table tbody tr:hover {
            background: #f9f9f9;
        }
        
        .empty-state {
            text-align: center;
            padding: 60px 20px;
            color: #999;
        }
        
        .empty-state-icon {
            font-size: 3rem;
            margin-bottom: 20px;
        }
        
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 20px;
            margin-bottom: 30px;
        }
        
        .stat-card {
            background: white;
            border-radius: 8px;
            padding: 20px;
            text-align: center;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
            border-left: 4px solid #2e7d32;
            transition: all 0.3s;
        }
        
        .stat-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 4px 12px rgba(0,0,0,0.15);
        }
        
        .stat-card .value {
            font-size: 28px;
            font-weight: bold;
            color: #2e7d32;
            margin: 10px 0;
        }
        
        .stat-card .label {
            color: #666;
            font-size: 13px;
        }
        
        footer {
            background: #2e7d32;
            color: white;
            text-align: center;
            padding: 20px;
            margin-top: 40px;
            font-size: 12px;
        }
        
        .loading {
            text-align: center;
            padding: 40px;
            color: #999;
        }
        
        .spinner {
            width: 50px;
            height: 50px;
            border: 4px solid #e0e0e0;
            border-top: 4px solid #2e7d32;
            border-radius: 50%;
            animation: spin 1s linear infinite;
            margin: 0 auto;
        }

        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }
        
        @media (max-width: 768px) {
            header {
                flex-direction: column;
                gap: 15px;
                padding: 15px 20px;
            }
            
            .tabs {
                gap: 0;
                overflow-x: auto;
                flex-wrap: nowrap;
            }
            
            .tab-btn {
                white-space: nowrap;
                padding: 12px 15px;
                font-size: 13px;
            }
            
            .container {
                padding: 20px 15px;
            }
            
            .stats-grid {
                grid-template-columns: 1fr;
            }
            
            .details {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>
<body>
    <!-- Header -->
    <header>
        <div class="logo">🌱 ECOSAVE - Stock</div>
        <div>
            <button onclick="location.href='admin.php'" title="Accès Admin">⚙️ Admin</button>
            <button onclick="location.href='../../index.php'" title="Retour à l'accueil">🏠 Accueil</button>
        </div>
    </header>

    <!-- Main Container -->
    <div class="container">
        <h1>📦 Consultez Notre Stock</h1>
        <p class="subtitle">Explorez nos produits et catégories disponibles</p>

        <!-- Stats Section -->
        <div class="stats-grid">
            <div class="stat-card">
                <div class="value" id="stat-total">0</div>
                <div class="label">Produits Total</div>
            </div>
            <div class="stat-card">
                <div class="value" id="stat-categories">0</div>
                <div class="label">Catégories</div>
            </div>
            <div class="stat-card">
                <div class="value" id="stat-disponibles">0</div>
                <div class="label">Disponibles</div>
            </div>
            <div class="stat-card">
                <div class="value" id="stat-expiring">0</div>
                <div class="label">À Surveiller</div>
            </div>
        </div>

        <!-- Tabs -->
        <div class="tabs">
            <button class="tab-btn active" data-tab="tous">📦 Tous les Produits</button>
            <button class="tab-btn" data-tab="categories">🏷️ Par Catégorie</button>
            <button class="tab-btn" data-tab="disponibles">✅ Disponibles</button>
            <button class="tab-btn" data-tab="alertes">⚠️ Alertes</button>
        </div>

        <!-- TAB: Tous les Produits -->
        <div id="tous" class="tab-content active">
            <div class="search-box">
                <input type="text" id="search-tous" placeholder="🔍 Rechercher un produit..." onkeyup="filterTab('tous')">
            </div>
            
            <div class="info-box">
                <strong>💡 Conseil:</strong> Tous nos produits sont affichés ci-dessous. Cliquez sur un produit pour voir plus de détails.
            </div>

            <div id="tous-list" class="items-list">
                <div class="loading">
                    <div class="spinner"></div>
                </div>
            </div>
        </div>

        <!-- TAB: Par Catégorie -->
        <div id="categories" class="tab-content">
            <div class="search-box">
                <input type="text" id="search-categories" placeholder="🔍 Rechercher une catégorie..." onkeyup="filterTab('categories')">
            </div>

            <div id="categories-list" class="items-list">
                <div class="loading">
                    <div class="spinner"></div>
                </div>
            </div>
        </div>

        <!-- TAB: Disponibles -->
        <div id="disponibles" class="tab-content">
            <div class="search-box">
                <input type="text" id="search-disponibles" placeholder="🔍 Rechercher dans les disponibles..." onkeyup="filterTab('disponibles')">
            </div>

            <div id="disponibles-list" class="items-list">
                <div class="loading">
                    <div class="spinner"></div>
                </div>
            </div>
        </div>

        <!-- TAB: Alertes -->
        <div id="alertes" class="tab-content">
            <div class="info-box">
                <strong>⚠️ Attention:</strong> Ces produits nécessitent votre attention - Stock bas ou en cours d'expiration.
            </div>

            <div id="alertes-list" class="items-list">
                <div class="loading">
                    <div class="spinner"></div>
                </div>
            </div>
        </div>
    </div>

    <!-- Footer -->
    <footer>
        <p>&copy; 2026 ECOSAVE - Gestion du Stock. Tous droits réservés.</p>
    </footer>

    <script>
        // Tab Management
        document.querySelectorAll('.tab-btn').forEach(btn => {
            btn.addEventListener('click', function() {
                const tabName = this.dataset.tab;
                
                // Remove active from all buttons and contents
                document.querySelectorAll('.tab-btn').forEach(b => b.classList.remove('active'));
                document.querySelectorAll('.tab-content').forEach(c => c.classList.remove('active'));
                
                // Add active to clicked button and corresponding content
                this.classList.add('active');
                document.getElementById(tabName).classList.add('active');
                
                // Load content for specific tab
                if (tabName === 'tous') {
                    loadTousProduits();
                } else if (tabName === 'categories') {
                    loadCategories();
                } else if (tabName === 'disponibles') {
                    loadDisponibles();
                } else if (tabName === 'alertes') {
                    loadAlertes();
                }
            });
        });

        // Filter functions
        function filterTab(tab) {
            const searchInput = document.getElementById(`search-${tab}`);
            const items = document.querySelectorAll(`#${tab}-list .item-card`);
            
            if (!searchInput) return;
            
            const filter = searchInput.value.toLowerCase();
            items.forEach(item => {
                const text = item.textContent.toLowerCase();
                item.style.display = text.includes(filter) ? 'block' : 'none';
            });
        }

        // Data Loading Functions (placeholders)
        function loadTousProduits() {
            console.log('Loading all products...');
            // À implémenter avec données réelles
            displaySampleProducts();
        }

        function loadCategories() {
            console.log('Loading categories...');
            // À implémenter avec données réelles
            displaySampleCategories();
        }

        function loadDisponibles() {
            console.log('Loading disponibles...');
            // À implémenter avec données réelles
            displaySampleDisponibles();
        }

        function loadAlertes() {
            console.log('Loading alertes...');
            // À implémenter avec données réelles
            displaySampleAlertes();
        }

        // Data Loading Functions
        function loadTousProduits() {
            fetch('../../index.php?action=produit_getAll')
                .then(r => r.json())
                .then(data => {
                    const produits = data.data || [];
                    if (produits.length === 0) {
                        document.getElementById('tous-list').innerHTML = '<p style="text-align: center; padding: 40px; color: #999;">Aucun produit trouvé</p>';
                        return;
                    }
                    
                    let html = '';
                    produits.forEach(p => {
                        const statusClass = p.quantite_dispo === 0 ? 'status-danger' : (p.quantite_dispo <= 5 ? 'status-warning' : 'status-ok');
                        const statusText = p.quantite_dispo === 0 ? 'Rupture' : (p.quantite_dispo <= 5 ? 'Stock Limité' : 'Disponible');
                        
                        html += `
                            <div class="item-card">
                                <h5>📦 ${p.nom_prod}</h5>
                                <p>${p.categorie_nom || 'Catégorie'}</p>
                                <p>
                                    <span class="badge category">${p.categorie_nom || '-'}</span>
                                    <span class="badge ${statusClass === 'status-ok' ? 'status-ok' : (statusClass === 'status-warning' ? 'status-warning' : 'status-danger')}">${statusText}</span>
                                </p>
                                <div class="details">
                                    <div class="detail-item">
                                        <span class="detail-label">Poids:</span>
                                        <span class="detail-value">${p.poids_produit || 'N/A'} kg</span>
                                    </div>
                                    <div class="detail-item">
                                        <span class="detail-label">Quantité:</span>
                                        <span class="detail-value">${p.quantite_dispo}</span>
                                    </div>
                                    <div class="detail-item">
                                        <span class="detail-label">Expiration:</span>
                                        <span class="detail-value">${p.date_expiration || 'N/A'}</span>
                                    </div>
                                </div>
                            </div>
                        `;
                    });
                    document.getElementById('tous-list').innerHTML = html;
                });
        }

        function loadCategories() {
            // Charger les catégories ET les produits
            Promise.all([
                fetch('/gestion-stock/index.php?action=categorie_getAll').then(r => r.json()),
                fetch('/gestion-stock/index.php?action=produit_getAll').then(r => r.json())
            ])
            .then(([categoriesData, produitsData]) => {
                const categories = categoriesData.data || [];
                const produits = produitsData.data || [];
                
                if (categories.length === 0) {
                    document.getElementById('categories-list').innerHTML = '<p style="text-align: center; padding: 40px; color: #999;">Aucune catégorie trouvée</p>';
                    return;
                }
                
                let html = '';
                categories.forEach(c => {
                    // Filtrer les produits de cette catégorie
                    const produitsDeCette = produits.filter(p => p.id_cat === c.id_cat);
                    
                    html += `
                        <div class="item-card" style="border-left: 4px solid #2e7d32; margin-bottom: 20px;">
                            <h5>🏷️ ${c.nom_cat}</h5>
                            <p style="color: #666; font-size: 13px;">${c.description_cat || 'Pas de description'}</p>
                            <div class="details">
                                <div class="detail-item">
                                    <span class="detail-label">Lieu:</span>
                                    <span class="detail-value">${c.lieu_stockage || '-'}</span>
                                </div>
                                <div class="detail-item">
                                    <span class="detail-label">Température:</span>
                                    <span class="detail-value">${c.temp_conseille || '-'}</span>
                                </div>
                                <div class="detail-item">
                                    <span class="detail-label">Alerte:</span>
                                    <span class="detail-value">${c.delai_alerte_jours} jours</span>
                                </div>
                            </div>
                            
                            <div style="margin-top: 15px; padding-top: 15px; border-top: 1px solid #e0e0e0;">
                                <h6 style="color: #2e7d32; margin-bottom: 10px;">📦 Produits (${produitsDeCette.length})</h6>
                    `;
                    
                    if (produitsDeCette.length === 0) {
                        html += '<p style="color: #999; font-size: 12px;">Aucun produit dans cette catégorie</p>';
                    } else {
                        html += '<div style="display: flex; flex-wrap: wrap; gap: 10px;">';
                        produitsDeCette.forEach(p => {
                            const statusClass = p.quantite_dispo === 0 ? 'status-danger' : (p.quantite_dispo <= 5 ? 'status-warning' : 'status-ok');
                            const statusText = p.quantite_dispo === 0 ? 'Rupture' : (p.quantite_dispo <= 5 ? 'Stock Limité' : 'Disponible');
                            
                            html += `
                                <div style="
                                    flex: 1;
                                    min-width: 250px;
                                    padding: 12px;
                                    background: #f9f9f9;
                                    border-radius: 5px;
                                    border: 1px solid #e0e0e0;
                                ">
                                    <div style="font-weight: 600; color: #333; margin-bottom: 5px;">📦 ${p.nom_prod}</div>
                                    <div style="font-size: 12px; color: #666;">
                                        <span class="badge ${statusClass === 'status-ok' ? 'status-ok' : (statusClass === 'status-warning' ? 'status-warning' : 'status-danger')}">${statusText}</span>
                                    </div>
                                    <div style="margin-top: 8px; font-size: 12px; color: #666;">
                                        <div>📊 Quantité: <strong>${p.quantite_dispo}</strong></div>
                                        <div>⚖️ Poids: <strong>${p.poids_produit || 'N/A'} kg</strong></div>
                                        <div>📅 Expiration: <strong>${p.date_expiration || 'N/A'}</strong></div>
                                    </div>
                                </div>
                            `;
                        });
                        html += '</div>';
                    }
                    
                    html += `
                            </div>
                        </div>
                    `;
                });
                document.getElementById('categories-list').innerHTML = html;
            });
        }

        function loadDisponibles() {
            fetch('../../index.php?action=produit_getAll')
                .then(r => r.json())
                .then(data => {
                    const disponibles = (data.data || []).filter(p => p.quantite_dispo > 0);
                    if (disponibles.length === 0) {
                        document.getElementById('disponibles-list').innerHTML = '<p style="text-align: center; padding: 40px; color: #999;">Aucun produit disponible</p>';
                        return;
                    }
                    
                    let html = '';
                    disponibles.forEach(p => {
                        html += `
                            <div class="item-card">
                                <h5>✅ ${p.nom_prod}</h5>
                                <p>${p.categorie_nom || 'Catégorie'}</p>
                                <p>
                                    <span class="badge category">${p.categorie_nom || '-'}</span>
                                    <span class="badge status-ok">En Stock</span>
                                </p>
                                <div class="details">
                                    <div class="detail-item">
                                        <span class="detail-label">Quantité:</span>
                                        <span class="detail-value">${p.quantite_dispo}</span>
                                    </div>
                                    <div class="detail-item">
                                        <span class="detail-label">Poids:</span>
                                        <span class="detail-value">${p.poids_produit || 'N/A'} kg</span>
                                    </div>
                                </div>
                            </div>
                        `;
                    });
                    document.getElementById('disponibles-list').innerHTML = html;
                });
        }

        function loadAlertes() {
            fetch('../../index.php?action=produit_getAll')
                .then(r => r.json())
                .then(data => {
                    const today = new Date();
                    const alertes = (data.data || []).filter(p => 
                        p.quantite_dispo <= 5 || 
                        (p.date_expiration && new Date(p.date_expiration) <= new Date(today.getTime() + 7*24*60*60*1000))
                    );
                    
                    if (alertes.length === 0) {
                        document.getElementById('alertes-list').innerHTML = '<p style="text-align: center; padding: 40px; color: #999;">Aucune alerte</p>';
                        return;
                    }
                    
                    let html = '';
                    alertes.forEach(p => {
                        const badgeClass = p.quantite_dispo === 0 ? 'status-danger' : (p.quantite_dispo <= 5 ? 'status-warning' : 'status-ok');
                        const badgeText = p.quantite_dispo === 0 ? 'Rupture' : (p.quantite_dispo <= 5 ? 'Stock Bas' : 'À Surveiller');
                        
                        html += `
                            <div class="item-card">
                                <h5>⚠️ ${p.nom_prod}</h5>
                                <p>
                                    <span class="badge ${badgeClass}">${badgeText}</span>
                                </p>
                                <div class="details">
                                    <div class="detail-item">
                                        <span class="detail-label">Quantité:</span>
                                        <span class="detail-value">${p.quantite_dispo}</span>
                                    </div>
                                    <div class="detail-item">
                                        <span class="detail-label">Expiration:</span>
                                        <span class="detail-value">${p.date_expiration || 'N/A'}</span>
                                    </div>
                                </div>
                            </div>
                        `;
                    });
                    document.getElementById('alertes-list').innerHTML = html;
                });
        }

        function updateStats() {
            fetch('../../index.php?action=produit_getAll')
                .then(r => r.json())
                .then(data => {
                    const produits = data.data || [];
                    const disponibles = produits.filter(p => p.quantite_dispo > 0).length;
                    const expiring = produits.filter(p => p.date_expiration && new Date(p.date_expiration) < new Date(new Date().getTime() + 7*24*60*60*1000)).length;
                    
                    document.getElementById('stat-total').textContent = produits.length;
                    document.getElementById('stat-disponibles').textContent = disponibles;
                    document.getElementById('stat-expiring').textContent = expiring;
                });
            
            fetch('../../index.php?action=categorie_getAll')
                .then(r => r.json())
                .then(data => {
                    document.getElementById('stat-categories').textContent = (data.data || []).length;
                });
        }

        // Initialize on page load
        document.addEventListener('DOMContentLoaded', function() {
            loadTousProduits();
            updateStats();
        });
    </script>
</body>
</html>
