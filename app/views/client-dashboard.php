<?php
/**
 * Dashboard Client - Consultation des recettes et analyses carbone
 * Style ECOSAVE (Vert écologique)
 * Point d'accès: http://localhost/gestion-allergies/app/views/client-dashboard.php
 */
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ECOSAVE - Mon Espace Carbone</title>
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
        
        .tab-btn:hover, .tab-btn.active {
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
            grid-template-columns: repeat(auto-fill, minmax(350px, 1fr));
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
            font-size: 1.1rem;
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
        
        .badge.impact-bas { background: #c8e6c9; color: #2e7d32; }
        .badge.impact-moyen { background: #ffe0b2; color: #e65100; }
        .badge.impact-élevé { background: #ffcdd2; color: #c62828; }
        
        .loading { text-align: center; padding: 40px; }
        .spinner {
            width: 40px; height: 40px;
            border: 3px solid #e0e0e0;
            border-top: 3px solid #2e7d32;
            border-radius: 50%;
            animation: spin 1s linear infinite;
            margin: 0 auto;
        }
        
        @keyframes spin { 0% { transform: rotate(0deg); } 100% { transform: rotate(360deg); } }
        
        .empty-state { text-align: center; padding: 60px 20px; color: #999; }
        
        .pagination {
            display: flex; justify-content: center; gap: 10px; margin-top: 30px;
        }
        
        .btn {
            padding: 8px 12px; border: 1px solid #ddd; background: white;
            color: #666; border-radius: 5px; cursor: pointer; transition: all 0.3s;
        }
        
        .btn:hover:not(:disabled) { background: #f4f9f4; border-color: #2e7d32; color: #2e7d32; }
        .btn:disabled { opacity: 0.5; cursor: not-allowed; }
        
        footer {
            background-color: #2e7d32; color: white; text-align: center;
            padding: 20px; margin-top: 60px;
        }
    </style>
</head>
<body>
    <header>
        <div class="logo">🌱 ECOSAVE</div>
        <nav>
            <span style="margin-right: 20px;">Portail Public</span>
            <a href="admin.php" target="_blank">⚙️ Administration</a>
        </nav>
    </header>

    <div class="container">
        <h1>🌍 Mon Espace Carbone</h1>
        <p class="subtitle">Consultez les recettes et l'impact de votre alimentation sur la planète</p>

        <div class="tabs">
            <button class="tab-btn active" onclick="switchTab(event, 'recettes')">🥗 Recettes</button>
            <button class="tab-btn" onclick="switchTab(event, 'analyses')">📊 Analyses Carbone</button>
        </div>

        <!-- RECETTES TAB -->
        <div id="recettes" class="tab-content active">
            <div class="info-box">ℹ️ Explorez notre catalogue de recettes et découvrez leur composition.</div>
            <div class="search-box">
                <input type="text" id="recette-search" placeholder="🔍 Rechercher une recette...">
            </div>
            <div id="recettes-list" class="items-list">
                <div class="loading"><div class="spinner"></div></div>
            </div>
            <div class="pagination">
                <button class="btn" id="recettes-prev" onclick="changePage('recettes', -1)">← Précédent</button>
                <span id="recettes-page-info" style="min-width: 120px; text-align: center; line-height: 34px;"></span>
                <button class="btn" id="recettes-next" onclick="changePage('recettes', 1)">Suivant →</button>
            </div>
        </div>

        <!-- ANALYSES TAB -->
        <div id="analyses" class="tab-content">
            <div class="info-box">ℹ️ Consultez les scores de CO2 calculés pour nos recettes phares.</div>
            <div class="search-box">
                <input type="text" id="analyse-search" placeholder="🔍 Rechercher une analyse...">
            </div>
            <div id="analyses-list" class="items-list">
                <div class="loading"><div class="spinner"></div></div>
            </div>
            <div class="pagination">
                <button class="btn" id="analyses-prev" onclick="changePage('analyses', -1)">← Précédent</button>
                <span id="analyses-page-info" style="min-width: 120px; text-align: center; line-height: 34px;"></span>
                <button class="btn" id="analyses-next" onclick="changePage('analyses', 1)">Suivant →</button>
            </div>
        </div>
    </div>

    <footer>© 2026 ECOSAVE - Plateforme de Gestion de l'Empreinte Carbone</footer>

    <script>
        const API_BASE = '../../index.php';
        let state = {
            recettes: { page: 1, total: 1 },
            analyses: { page: 1, total: 1 }
        };

        function switchTab(event, tabName) {
            document.querySelectorAll('.tab-content').forEach(t => t.classList.remove('active'));
            document.querySelectorAll('.tab-btn').forEach(b => b.classList.remove('active'));
            document.getElementById(tabName).classList.add('active');
            event.currentTarget.classList.add('active');
            
            if (tabName === 'recettes') chargerRecettes();
            if (tabName === 'analyses') chargerAnalyses();
        }

        async function chargerRecettes() {
            try {
                const resp = await fetch(`${API_BASE}?controller=Recette&action=obtenirTous&page=${state.recettes.page}&limite=6`);
                if (!resp.ok) throw new Error(`HTTP ${resp.status}`);
                const data = await resp.json();
                if (data.success) {
                    state.recettes.total = data.pagination.total_pages;
                    afficherRecettes(data.recettes);
                    updatePagination('recettes', data.pagination);
                }
            } catch (e) {
                console.error(e);
                document.getElementById('recettes-list').innerHTML = `<div class="empty-state"><p class="text-danger">Erreur de connexion au serveur</p></div>`;
            }
        }

        function afficherRecettes(items) {
            const list = document.getElementById('recettes-list');
            if (!items.length) { list.innerHTML = '<div class="empty-state"><h3>Aucune recette trouvée</h3></div>'; return; }
            
            list.innerHTML = items.map(r => `
                <div class="item-card">
                    <h5>${r.nom}</h5>
                    <p>${r.description || 'Pas de description'}</p>
                    <p><small>Créée le ${r.date_creation}</small></p>
                    <div id="ana-${r.id_recette}" style="margin-top:10px"></div>
                </div>
            `).join('');

            // Fetch brief analysis for each
            items.forEach(async r => {
                const resp = await fetch(`${API_BASE}?controller=AnalyseCarbone&action=obtenirTous&page=1&limite=1000`);
                const d = await resp.json();
                const a = d.analyses.find(x => x.id_recette == r.id_recette);
                if(a) {
                    document.getElementById(`ana-${r.id_recette}`).innerHTML = `
                        <span class="badge impact-${a.niveau_impact.toLowerCase()}">Score: ${a.score_co2_total} kg CO2</span>
                    `;
                }
            });
        }

        async function chargerAnalyses() {
            try {
                const resp = await fetch(`${API_BASE}?controller=AnalyseCarbone&action=obtenirTous&page=${state.analyses.page}&limite=6`);
                if (!resp.ok) throw new Error(`HTTP ${resp.status}`);
                const data = await resp.json();
                if (data.success) {
                    state.analyses.total = data.pagination.total_pages;
                    afficherAnalyses(data.analyses);
                    updatePagination('analyses', data.pagination);
                }
            } catch (e) {
                console.error(e);
                document.getElementById('analyses-list').innerHTML = `<div class="empty-state"><p class="text-danger">Erreur de connexion au serveur</p></div>`;
            }
        }

        function afficherAnalyses(items) {
            const list = document.getElementById('analyses-list');
            if (!items.length) { list.innerHTML = '<div class="empty-state"><h3>Aucune analyse disponible</h3></div>'; return; }
            
            list.innerHTML = items.map(a => {
                const badgeClass = a.niveau_impact === 'bas' ? 'impact-bas' : a.niveau_impact === 'moyen' ? 'impact-moyen' : 'impact-élevé';
                return `
                <div class="item-card" style="border-left-color: ${a.niveau_impact === 'bas' ? '#66bb6a' : a.niveau_impact === 'moyen' ? '#ffa726' : '#ef5350'}">
                    <h5>Analyse: ${a.nom_recette || 'Recette'}</h5>
                    <div style="margin-bottom: 10px;">
                        <span class="badge ${badgeClass}">${a.niveau_impact.toUpperCase()} IMPACT</span>
                    </div>
                    <p><strong>Score CO2:</strong> <span style="font-size: 1.2rem; color: #2e7d32; font-weight: bold;">${a.score_co2_total}</span> kg</p>
                    <p><strong>Méthode:</strong> ${a.methode_calcul}</p>
                    <p><small>Calculée le ${a.date_calcul}</small></p>
                </div>
            `}).join('');
        }

        function updatePagination(type, pagin) {
            document.getElementById(`${type}-page-info`).textContent = `Page ${pagin.page} / ${pagin.total_pages}`;
            document.getElementById(`${type}-prev`).disabled = pagin.page === 1;
            document.getElementById(`${type}-next`).disabled = pagin.page === pagin.total_pages;
        }

        function changePage(type, dir) {
            state[type].page += dir;
            if (type === 'recettes') chargerRecettes(); else chargerAnalyses();
        }

        // Search
        document.getElementById('recette-search').addEventListener('input', async (e) => {
            const term = e.target.value.trim();
            if (!term) { state.recettes.page = 1; chargerRecettes(); return; }
            const resp = await fetch(`${API_BASE}?controller=Recette&action=rechercher&terme=${encodeURIComponent(term)}`);
            const data = await resp.json();
            if (data.success) {
                afficherRecettes(data.recettes);
                document.getElementById('recettes-page-info').textContent = `${data.count} résultat(s)`;
                document.getElementById('recettes-prev').disabled = true;
                document.getElementById('recettes-next').disabled = true;
            }
        });

        document.getElementById('analyse-search').addEventListener('input', async (e) => {
            const term = e.target.value.trim();
            if (!term) { state.analyses.page = 1; chargerAnalyses(); return; }
            const resp = await fetch(`${API_BASE}?controller=AnalyseCarbone&action=rechercher&terme=${encodeURIComponent(term)}`);
            const data = await resp.json();
            if (data.success) {
                afficherAnalyses(data.analyses);
                document.getElementById('analyses-page-info').textContent = `${data.analyses.length} résultat(s)`;
                document.getElementById('analyses-prev').disabled = true;
                document.getElementById('analyses-next').disabled = true;
            }
        });

        chargerRecettes();
    </script>
</body>
</html>
