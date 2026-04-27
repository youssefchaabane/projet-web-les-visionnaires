<?php
/**
 * Dashboard Client - Consultation des Recettes
 * Point d'accès: http://localhost/gestion-recettes/app/views/client-dashboard.php
 */
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mes Recettes - Espace Gourmand</title>
    <style>
        :root {
            --bg: #eef8f1;
            --surface: #ffffff;
            --surface-soft: #f6fbf7;
            --primary: #2e7d32;
            --primary-soft: #d8ecd7;
            --text: #1f2937;
            --muted: #50686f;
            --muted-light: #718096;
            --shadow: 0 30px 70px rgba(46, 125, 50, 0.12);
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Poppins', sans-serif;
            background: radial-gradient(circle at top, rgba(46, 125, 50, 0.08), transparent 40%), linear-gradient(180deg, #f9fcf8 0%, #eef8f1 100%);
            color: var(--text);
            min-height: 100vh;
        }

        header {
            background: linear-gradient(90deg, #2e7d32 0%, #1f5f24 100%);
            color: white;
            padding: 24px 40px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            box-shadow: 0 20px 45px rgba(46, 125, 50, 0.18);
        }

        .logo {
            font-size: 1.4rem;
            font-weight: 800;
            letter-spacing: -0.05em;
            text-transform: uppercase;
        }

        header a {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            color: white;
            text-decoration: none;
            font-weight: 700;
            padding: 12px 20px;
            border-radius: 999px;
            background: rgba(255, 255, 255, 0.16);
            transition: background 0.25s ease, transform 0.25s ease;
        }

        header a:hover {
            background: rgba(255, 255, 255, 0.24);
            transform: translateY(-1px);
        }

        .container {
            max-width: 1200px;
            margin: 50px auto 0;
            padding: 0 24px 60px;
        }

        h1 {
            font-size: clamp(2.4rem, 3vw, 3.4rem);
            color: var(--primary);
            margin-bottom: 30px;
            text-align: center;
            letter-spacing: -0.03em;
        }

        .search-container {
            max-width: 940px;
            margin: 0 auto 40px;
            padding: 26px 28px;
            background: var(--surface);
            border-radius: 28px;
            box-shadow: var(--shadow);
        }

        .search-controls {
            display: flex;
            flex-wrap: wrap;
            gap: 14px;
            justify-content: center;
            margin-top: 20px;
        }

        .search-input {
            width: 100%;
            max-width: 680px;
            padding: 18px 28px;
            border: 1px solid rgba(46, 125, 50, 0.16);
            border-radius: 999px;
            box-shadow: 0 18px 40px rgba(74, 104, 85, 0.06);
            font-size: 1rem;
            outline: none;
            transition: border-color 0.25s ease, box-shadow 0.25s ease;
            background: #f8fdf7;
        }

        .search-input:focus {
            border-color: var(--primary);
            box-shadow: 0 18px 40px rgba(46, 125, 50, 0.16);
        }

        select.form-control {
            min-width: 160px;
            padding: 14px 18px;
            border-radius: 18px;
            border: 1px solid rgba(46, 125, 50, 0.16);
            background: #ffffff;
            color: var(--text);
            font-weight: 600;
            outline: none;
            box-shadow: inset 0 1px 2px rgba(0,0,0,0.04);
            transition: border-color 0.25s ease;
        }

        select.form-control:focus {
            border-color: var(--primary);
        }

        .btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            padding: 14px 22px;
            border: none;
            border-radius: 999px;
            cursor: pointer;
            font-weight: 700;
            transition: transform 0.2s ease, box-shadow 0.2s ease, background 0.2s ease;
        }

        .btn:hover {
            transform: translateY(-1px);
            box-shadow: 0 18px 30px rgba(46, 125, 50, 0.16);
        }

        .btn-primary {
            background: var(--primary);
            color: white;
        }

        .btn-secondary {
            background: var(--primary-soft);
            color: #22543d;
            box-shadow: inset 0 0 0 1px rgba(46, 125, 50, 0.18);
        }

        .btn-small {
            padding: 10px 16px;
            font-size: 0.92rem;
            border-radius: 14px;
        }

        .recipe-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
            gap: 26px;
        }

        .recipe-card {
            position: relative;
            background: var(--surface);
            border-radius: 28px;
            overflow: hidden;
            border: 1px solid rgba(46, 125, 50, 0.1);
            box-shadow: 0 20px 45px rgba(46, 125, 50, 0.08);
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }

        .recipe-card:hover {
            transform: translateY(-8px);
            box-shadow: 0 32px 70px rgba(46, 125, 50, 0.12);
        }

        .recipe-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 6px;
            background: linear-gradient(90deg, #2e7d32, #81c784);
        }

        .recipe-info {
            padding: 28px;
            position: relative;
        }

        .recipe-name {
            font-size: 1.45rem;
            font-weight: 800;
            color: var(--text);
            margin-bottom: 18px;
        }

        .recipe-meta {
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
            margin-bottom: 20px;
        }

        .badge {
            padding: 8px 14px;
            border-radius: 999px;
            font-size: 0.78rem;
            font-weight: 800;
            text-transform: uppercase;
            letter-spacing: 0.03em;
        }

        .badge-facile {
            background: #d9f7dd;
            color: #1f5626;
        }

        .badge-moyen {
            background: #fff4d2;
            color: #8b5c04;
        }

        .badge-difficile {
            background: #ffe1e1;
            color: #8b1f20;
        }

        .recipe-desc {
            font-size: 0.95rem;
            line-height: 1.75;
            color: var(--muted-light);
            margin-bottom: 22px;
            min-height: 90px;
        }

        .details-button {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 10px 16px;
            border-radius: 14px;
            border: none;
            background: #d8ecd7;
            color: #22543d;
            font-weight: 700;
            cursor: pointer;
            transition: background 0.2s ease, transform 0.2s ease;
        }

        .details-button:hover {
            background: #c0e2b6;
            transform: translateY(-1px);
        }

        .recipe-footer {
            border-top: 1px solid rgba(46, 125, 50, 0.1);
            padding-top: 18px;
            display: flex;
            flex-wrap: wrap;
            justify-content: space-between;
            gap: 10px;
            font-size: 0.9rem;
            color: var(--muted);
        }

        .loading {
            text-align: center;
            padding: 100px 0;
            grid-column: 1 / -1;
        }

        .spinner {
            border: 4px solid rgba(0,0,0,0.08);
            border-left-color: var(--primary);
            border-radius: 50%;
            width: 44px;
            height: 44px;
            animation: spin 1s linear infinite;
            margin: 0 auto 18px;
        }

        @keyframes spin {
            100% { transform: rotate(360deg); }
        }

        .pagination {
            margin-top: 40px;
            display: flex;
            justify-content: center;
            flex-wrap: wrap;
            gap: 12px;
        }

        .page-btn {
            padding: 12px 18px;
            border: none;
            border-radius: 14px;
            background: #ffffff;
            color: var(--text);
            cursor: pointer;
            font-weight: 700;
            box-shadow: 0 10px 20px rgba(46, 125, 50, 0.08);
            transition: transform 0.2s ease, background 0.2s ease, color 0.2s ease;
        }

        .page-btn:hover {
            transform: translateY(-1px);
            background: var(--surface-soft);
        }

        .page-btn.active {
            background: var(--primary);
            color: white;
        }

        footer {
            text-align: center;
            padding: 36px 20px;
            margin-top: 60px;
            background: #2e7d32;
            color: white;
            border-radius: 32px 32px 0 0;
        }
    </style>
</head>
<body>
    <header>
        <div class="logo">🥘 GESTION RECETTES</div>
        <a href="admin.php">⚙️ Admin</a>
    </header>

    <div class="container">
        <h1>Découvrez nos Recettes</h1>
        
        <div class="search-container">
            <input type="text" id="search-input" class="search-input" placeholder="🔍 Rechercher une recette par nom ou ingrédient...">
            <div class="search-controls">
                <select id="client-sort-select" class="form-control">
                    <option value="date_creation">Trier par date</option>
                    <option value="nom">Nom</option>
                    <option value="nombre_personnes">Personnes</option>
                    <option value="calories_totales">Calories</option>
                    <option value="difficulte">Difficulté</option>
                </select>
                <select id="client-order-select" class="form-control">
                    <option value="DESC">Décroissant</option>
                    <option value="ASC">Croissant</option>
                </select>
                <button id="client-pdf-btn" class="btn btn-primary">📄 Export PDF</button>
            </div>
        </div>

        <div id="recipe-list" class="recipe-grid">
            <div class="loading">
                <div class="spinner"></div>
                <p>Mise en appétit en cours...</p>
            </div>
        </div>

        <div id="pagination" style="margin-top: 50px; display: flex; justify-content: center; gap: 15px;"></div>
    </div>

    <footer>
        © 2026 Gestion Recettes - Fait avec passion pour la cuisine
    </footer>

    <script>
        const API_URL = '../../index.php';
        let currentPage = 1;
        let currentSearch = '';
        let currentSortBy = 'date_creation';
        let currentOrder = 'DESC';

        async function loadRecettes(page = 1, search = '', sortBy = 'date_creation', order = 'DESC') {
            const params = new URLSearchParams({
                controller: 'Recette',
                action: search ? 'rechercher' : 'obtenirTous',
                sort_by: sortBy,
                order: order
            });
            if (search) {
                params.set('terme', search);
                params.set('limite', 50);
            } else {
                params.set('page', page);
                params.set('limite', 9);
            }

            try {
                const res = await fetch(`${API_URL}?${params.toString()}`);
                const data = await res.json();
                if(data.success) {
                    renderRecettes(data.recettes || data.allergies);
                    if(!search) renderPagination(data.pagination);
                    else document.getElementById('pagination').innerHTML = '';
                } else {
                    document.getElementById('recipe-list').innerHTML = `<div style="grid-column: 1/-1; text-align: center; padding: 50px; color: #e53e3e;">Erreur: ${data.message}</div>`;
                }
            } catch (e) {
                console.error(e);
                document.getElementById('recipe-list').innerHTML = '<div style="grid-column: 1/-1; text-align: center; padding: 50px; color: #e53e3e;">Erreur lors du chargement des recettes.</div>';
            }
        }

        function renderRecettes(recettes) {
            const container = document.getElementById('recipe-list');
            if(!recettes || recettes.length === 0) {
                container.innerHTML = '<div style="grid-column: 1/-1; text-align: center; padding: 50px;">Aucune recette trouvée 👩‍🍳</div>';
                return;
            }

            container.innerHTML = recettes.map(r => `
                <div class="recipe-card">
                    <div class="recipe-info">
                        <div class="recipe-name">${r.nom}</div>
                        <div class="recipe-meta">
                            <span class="badge badge-${r.difficulte}">${r.difficulte}</span>
                            <span style="font-size: 13px; color: #718096;">⏱️ ${r.temps_preparation + r.temps_cuisson} min</span>
                            <span style="font-size: 13px; color: #718096;">🔥 ${r.calories_totales} kcal</span>
                        </div>
                        <p class="recipe-desc">${r.description.substring(0, 120)}...</p>
                        <div id="details-${r.id_recette}" class="details-block">
                            <button class="details-button" onclick="loadDetails(${r.id_recette})">Voir ingrédients / étapes</button>
                        </div>
                        <div class="recipe-footer" style="margin-top:20px;">
                            <span>👥 ${r.nombre_personnes} personnes</span>
                            <span>Ajoutée le ${new Date(r.date_creation).toLocaleDateString()}</span>
                        </div>
                    </div>
                </div>
            `).join('');
        }

        async function loadDetails(id) {
            const container = document.getElementById('details-' + id);
            container.innerHTML = '<span style="color:#a0aec0;">Chargement...</span>';
            try {
                const res = await fetch(`${API_URL}?controller=DetailRecette&action=obtenirParRecette&id_recette=${id}`);
                const data = await res.json();
                if(data.success && data.details.length > 0) {
                    let html = '<ul style="padding-left:20px; color:#4a5568; margin-bottom:10px; margin-top:10px;">';
                    data.details.forEach(d => {
                        html += `<li style="margin-bottom:5px;"><b>${d.ingredient}</b> (${d.quantite}) - <i>Étape: ${d.etape}</i></li>`;
                    });
                    html += '</ul>';
                    container.innerHTML = html;
                } else {
                    container.innerHTML = '<div style="margin-top:10px; color:#a0aec0;">Aucun ingrédient / étape enregistré.</div>';
                }
            } catch(e) {
                container.innerHTML = '<div style="margin-top:10px; color:#e53e3e;">Erreur lors du chargement.</div>';
            }
        }

        function renderPagination(p) {
            const container = document.getElementById('pagination');
            let h = '';
            for(let i=1; i<=p.total_pages; i++) {
                h += `<button onclick="goToPage(${i})" class="page-btn ${i === currentPage ? 'active' : ''}">${i}</button>`;
            }
            container.innerHTML = h;
        }

        function goToPage(p) {
            currentPage = p;
            loadRecettes(p);
            window.scrollTo({ top: 0, behavior: 'smooth' });
        }

        document.getElementById('search-input').oninput = (e) => {
            const search = e.target.value.trim();
            currentSearch = search;
            if(search.length > 2) {
                loadRecettes(1, search, currentSortBy, currentOrder);
            } else if(search.length === 0) {
                loadRecettes(1, '', currentSortBy, currentOrder);
            }
        };

        document.getElementById('client-sort-select').onchange = (e) => {
            currentSortBy = e.target.value;
            loadRecettes(1, currentSearch, currentSortBy, currentOrder);
        };

        document.getElementById('client-order-select').onchange = (e) => {
            currentOrder = e.target.value;
            loadRecettes(1, currentSearch, currentSortBy, currentOrder);
        };

        document.getElementById('client-pdf-btn').onclick = () => {
            const params = new URLSearchParams({
                controller: 'Recette',
                action: 'exportPdf',
                sort_by: currentSortBy,
                order: currentOrder
            });
            if (currentSearch) {
                params.set('terme', currentSearch);
            }
            window.open(`${API_URL}?${params.toString()}`, '_blank');
        };

        window.onload = () => loadRecettes();
    </script>
</body>
</html>
