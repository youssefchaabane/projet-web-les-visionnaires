<?php
/**
 * Dashboard Client - Consultation des allergies et traitements
 * Style ECOSAVE (Vert écologique)
 * Point d'accès: http://localhost/gestion-allergies/app/views/client-dashboard.php
 */
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mon Espace - ECOSAVE</title>
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
        
        header a {
            color: white;
            background: rgba(255,255,255,0.2);
            padding: 10px 20px;
            border-radius: 5px;
            text-decoration: none;
            font-size: 14px;
            transition: all 0.3s;
        }
        
        header a:hover {
            background: rgba(255,255,255,0.3);
            transform: translateY(-2px);
            box-shadow: 0 4px 8px rgba(0,0,0,0.2);
        }
        
        nav {
            color: white;
        }
        
        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 40px 20px;
        }
        
        h1 {
            color: #2e7d32;
            margin-bottom: 10px;
        }
        
        .subtitle {
            color: #666;
            margin-bottom: 30px;
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
            font-size: 16px;
            font-weight: 500;
            color: #666;
            transition: all 0.3s;
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
            padding: 15px;
            margin-bottom: 20px;
            border-radius: 5px;
            color: #1b5e20;
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
            font-size: 14px;
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
        }
        
        .item-card p {
            margin: 5px 0;
            color: #666;
            font-size: 14px;
        }
        
        .badge {
            display: inline-block;
            padding: 5px 10px;
            border-radius: 12px;
            font-size: 12px;
            font-weight: 600;
            margin-right: 8px;
        }
        
        .badge.type {
            background: #c8e6c9;
            color: #1b5e20;
        }
        
        .badge.danger-critique {
            background: #ffcdd2;
            color: #c62828;
        }
        
        .badge.danger-élevé {
            background: #ffe0b2;
            color: #e65100;
        }
        
        .badge.danger-moyen {
            background: #bbdefb;
            color: #1565c0;
        }
        
        .badge.danger-faible {
            background: #c8e6c9;
            color: #2e7d32;
        }
        
        .badge.efficacite-élevée {
            background: #c8e6c9;
            color: #1b5e20;
        }
        
        .badge.efficacite-moyenne {
            background: #ffe0b2;
            color: #e65100;
        }
        
        .badge.efficacite-faible {
            background: #ffcdd2;
            color: #c62828;
        }
        
        .loading {
            text-align: center;
            padding: 40px;
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
        
        .empty-state {
            text-align: center;
            padding: 60px 20px;
            color: #999;
        }
        
        .empty-state h3 {
            color: #666;
            margin-bottom: 10px;
        }
        
        .pagination {
            display: flex;
            justify-content: center;
            gap: 10px;
            margin-top: 30px;
        }
        
        .btn {
            padding: 8px 12px;
            border: 1px solid #ddd;
            background: white;
            color: #666;
            border-radius: 5px;
            cursor: pointer;
            font-size: 14px;
            transition: all 0.3s;
        }
        
        .btn:hover:not(:disabled) {
            background: #f4f9f4;
            border-color: #2e7d32;
            color: #2e7d32;
        }
        
        .btn:disabled {
            opacity: 0.5;
            cursor: not-allowed;
        }
        
        footer {
            background-color: #2e7d32;
            color: white;
            text-align: center;
            padding: 20px;
            margin-top: 60px;
        }
    </style>
</head>
<body>
    <!-- Header -->
    <header>
        <div class="logo">🌱 ECOSAVE</div>
        <nav>
            <span style="margin-right: 20px;">Espace Client</span>
            <a href="admin.php" target="_blank" title="Accéder au panneau admin">⚙️ Admin</a>
        </nav>
    </header>

    <!-- Main Content -->
    <div class="container">
        <h1>💚 Mon Espace Santé</h1>
        <p class="subtitle">Consultez les informations sur les allergies et les traitements disponibles</p>

        <!-- Tabs -->
        <div class="tabs">
            <button class="tab-btn active" onclick="switchTab(event, 'allergies')">
                🦠 Allergies
            </button>
            <button class="tab-btn" onclick="switchTab(event, 'traitements')">
                💊 Traitements
            </button>
        </div>

        <!-- ALLERGIES TAB -->
        <div id="allergies" class="tab-content active">
            <div class="info-box">
                ℹ️ Vous pouvez consulter ici toutes les allergies connues et leurs caractéristiques.
            </div>

            <div class="search-box">
                <input type="text" id="allergie-search" placeholder="🔍 Rechercher une allergie...">
            </div>

            <div id="allergies-list" class="items-list">
                <div class="loading">
                    <div class="spinner"></div>
                </div>
            </div>

            <div class="pagination">
                <button class="btn" id="allergies-prev" onclick="changeAllergiesPage(-1)">← Précédent</button>
                <span id="allergies-page-info" style="min-width: 150px; text-align: center; line-height: 34px;"></span>
                <button class="btn" id="allergies-next" onclick="changeAllergiesPage(1)">Suivant →</button>
            </div>
        </div>

        <!-- TRAITEMENTS TAB -->
        <div id="traitements" class="tab-content">
            <div class="info-box">
                ℹ️ Découvrez les différents traitements disponibles pour les allergies.
            </div>

            <div class="search-box">
                <input type="text" id="traitement-search" placeholder="🔍 Rechercher un traitement...">
            </div>

            <div id="traitements-list" class="items-list">
                <div class="loading">
                    <div class="spinner"></div>
                </div>
            </div>

            <div class="pagination">
                <button class="btn" id="traitements-prev" onclick="changeTraitementsPage(-1)">← Précédent</button>
                <span id="traitements-page-info" style="min-width: 150px; text-align: center; line-height: 34px;"></span>
                <button class="btn" id="traitements-next" onclick="changeTraitementsPage(1)">Suivant →</button>
            </div>
        </div>
    </div>

    <!-- Footer -->
    <footer>
        © 2026 ECOSAVE - Plateforme de Gestion des Allergies
    </footer>

    <script>
        const API_BASE = 'http://localhost/gestion-allergies/index.php';
        let allergies_page = 1, allergies_total = 0;
        let traitements_page = 1, traitements_total = 0;

        function switchTab(event, tabName) {
            document.querySelectorAll('.tab-content').forEach(t => t.classList.remove('active'));
            document.querySelectorAll('.tab-btn').forEach(b => b.classList.remove('active'));
            document.getElementById(tabName).classList.add('active');
            event.target.classList.add('active');
            
            if (tabName === 'allergies') chargerAllergies();
            if (tabName === 'traitements') chargerTraitements();
        }

        // ===== ALLERGIES =====
        async function chargerAllergies() {
            try {
                const response = await fetch(`${API_BASE}?controller=Allergie&action=obtenirTous&page=${allergies_page}&limite=6`);
                const data = await response.json();
                
                if (data.success) {
                    allergies_total = data.pagination.total_pages;
                    afficherAllergies(data.allergies);
                    document.getElementById('allergies-page-info').textContent = 
                        `Page ${data.pagination.page} / ${data.pagination.total_pages}`;
                    document.getElementById('allergies-prev').disabled = allergies_page === 1;
                    document.getElementById('allergies-next').disabled = allergies_page === data.pagination.total_pages;
                }
            } catch (error) {
                console.error('Erreur:', error);
                document.getElementById('allergies-list').innerHTML = 
                    '<div class="empty-state"><h3>Erreur de connexion</h3></div>';
            }
        }

        function afficherAllergies(allergies) {
            if (allergies.length === 0) {
                document.getElementById('allergies-list').innerHTML = 
                    '<div class="empty-state"><h3>Aucune allergie trouvée</h3></div>';
                return;
            }

            let html = '';
            allergies.forEach(a => {
                const dangerClass = `danger-${a.niveau_danger.toLowerCase()}`;
                html += `
                    <div class="item-card">
                        <h5>${a.nom}</h5>
                        <div>
                            <span class="badge type">${a.type}</span>
                            <span class="badge ${dangerClass}">${a.niveau_danger}</span>
                        </div>
                        ${a.description ? `<p><strong>Description:</strong> ${a.description}</p>` : ''}
                        ${a.symptomes ? `<p><strong>Symptômes:</strong> ${a.symptomes}</p>` : ''}
                        <div id="traitements-${a.id_allergie}" style="margin-top: 10px; padding: 10px; background: #f9f9f9; border-radius: 5px; font-size: 13px;">
                            <p style="color: #999; margin: 0;">Chargement des traitements...</p>
                        </div>
                    </div>
                `;
            });
            
            document.getElementById('allergies-list').innerHTML = html;
            
            // Charger les traitements pour chaque allergie
            allergies.forEach(a => {
                chargerTraitementsAllergie(a.id_allergie);
            });
        }

        async function chargerTraitementsAllergie(id_allergie) {
            try {
                const response = await fetch(`${API_BASE}?controller=Allergie&action=obtenirTraitements&id=${id_allergie}`);
                const data = await response.json();
                
                if (data.success) {
                    const container = document.getElementById(`traitements-${id_allergie}`);
                    if (data.traitements && data.traitements.length > 0) {
                        let html = '<p style="font-weight: bold; margin: 0 0 8px 0; color: #2e7d32;">💊 Traitements associés:</p>';
                        data.traitements.forEach(t => {
                            html += `<p style="margin: 5px 0; padding: 5px; background: white; border-radius: 3px;"><strong>${t.nom}</strong>`;
                            if (t.efficacite) html += ` <span style="color: #666;">(Efficacité: ${t.efficacite})</span>`;
                            html += `</p>`;
                        });
                        container.innerHTML = html;
                    } else {
                        container.innerHTML = '<p style="color: #999; margin: 0; font-style: italic;">Aucun traitement disponible pour cette allergie</p>';
                    }
                }
            } catch (error) {
                console.error('Erreur:', error);
            }
        }

        function changeAllergiesPage(direction) {
            const newPage = allergies_page + direction;
            if (newPage > 0 && newPage <= allergies_total) {
                allergies_page = newPage;
                chargerAllergies();
            }
        }

        document.getElementById('allergie-search').addEventListener('input', async (e) => {
            const terme = e.target.value.trim();
            if (terme === '') {
                allergies_page = 1;
                chargerAllergies();
                return;
            }

            try {
                const response = await fetch(`${API_BASE}?controller=Allergie&action=rechercher&terme=${encodeURIComponent(terme)}`);
                const data = await response.json();
                if (data.success) {
                    afficherAllergies(data.allergies);
                    document.getElementById('allergies-page-info').textContent = `${data.count} résultat(s)`;
                }
            } catch (error) {
                console.error('Erreur de recherche:', error);
            }
        });

        // ===== TRAITEMENTS =====
        async function chargerTraitements() {
            try {
                const response = await fetch(`${API_BASE}?controller=Traitement&action=obtenirTous&page=${traitements_page}&limite=6`);
                const data = await response.json();
                
                if (data.success) {
                    traitements_total = data.pagination.total_pages;
                    afficherTraitements(data.traitements);
                    document.getElementById('traitements-page-info').textContent = 
                        `Page ${data.pagination.page} / ${data.pagination.total_pages}`;
                    document.getElementById('traitements-prev').disabled = traitements_page === 1;
                    document.getElementById('traitements-next').disabled = traitements_page === data.pagination.total_pages;
                }
            } catch (error) {
                console.error('Erreur:', error);
                document.getElementById('traitements-list').innerHTML = 
                    '<div class="empty-state"><h3>Erreur de connexion</h3></div>';
            }
        }

        function afficherTraitements(traitements) {
            if (traitements.length === 0) {
                document.getElementById('traitements-list').innerHTML = 
                    '<div class="empty-state"><h3>Aucun traitement trouvé</h3></div>';
                return;
            }

            let html = '';
            traitements.forEach(t => {
                const efficaciteClass = `efficacite-${(t.efficacite || 'faible').toLowerCase()}`;
                
                html += `
                    <div class="item-card">
                        <h5>${t.nom}</h5>
                        <div>
                            <span class="badge type">${t.type_allergie}</span>
                            <span class="badge ${efficaciteClass}">${t.efficacite || 'Non spécifiée'}</span>
                        </div>
                        ${t.description ? `<p><strong>Description:</strong> ${t.description}</p>` : ''}
                        ${t.principe_actif ? `<p><strong>Principe actif:</strong> ${t.principe_actif}</p>` : ''}
                        ${t.posologie ? `<p><strong>Posologie:</strong> ${t.posologie}</p>` : ''}
                    </div>
                `;
            });
            
            document.getElementById('traitements-list').innerHTML = html;
        }

        function changeTraitementsPage(direction) {
            const newPage = traitements_page + direction;
            if (newPage > 0 && newPage <= traitements_total) {
                traitements_page = newPage;
                chargerTraitements();
            }
        }

        document.getElementById('traitement-search').addEventListener('input', async (e) => {
            const terme = e.target.value.trim();
            if (terme === '') {
                traitements_page = 1;
                chargerTraitements();
                return;
            }

            try {
                const response = await fetch(`${API_BASE}?controller=Traitement&action=rechercher&terme=${encodeURIComponent(terme)}`);
                const data = await response.json();
                if (data.success) {
                    afficherTraitements(data.traitements);
                    document.getElementById('traitements-page-info').textContent = `${data.count} résultat(s)`;
                }
            } catch (error) {
                console.error('Erreur de recherche:', error);
            }
        });

        // Charge initial
        chargerAllergies();
    </script>
</body>
</html>
