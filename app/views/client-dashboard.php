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
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'Poppins', sans-serif; background-color: #f4f9f4; color: #4a5568; }
        header { background: #2e7d32; color: white; padding: 20px 50px; display: flex; justify-content: space-between; align-items: center; box-shadow: 0 4px 6px rgba(0,0,0,0.1); }
        .logo { font-size: 28px; font-weight: 800; letter-spacing: -1px; }
        header a { color: white; text-decoration: none; font-weight: 600; padding: 8px 16px; border-radius: 8px; background: rgba(255,255,255,0.2); transition: 0.3s; }
        header a:hover { background: rgba(255,255,255,0.3); }
        .container { max-width: 1200px; margin: 50px auto; padding: 0 20px; }
        h1 { font-size: 3rem; color: #2e7d32; margin-bottom: 40px; text-align: center; }
        .search-container { text-align: center; margin-bottom: 50px; }
        .search-input { width: 100%; max-width: 600px; padding: 15px 25px; border: none; border-radius: 30px; box-shadow: 0 10px 25px rgba(0,0,0,0.05); font-size: 18px; outline: none; transition: 0.3s; }
        .search-input:focus { box-shadow: 0 10px 25px rgba(46, 125, 50, 0.1); }
        .recipe-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(320px, 1fr)); gap: 30px; }
        .recipe-card { background: white; border-radius: 20px; overflow: hidden; box-shadow: 0 10px 15px -3px rgba(0,0,0,0.05); transition: 0.3s; position: relative; }
        .recipe-card:hover { transform: translateY(-10px); box-shadow: 0 20px 25px -5px rgba(0,0,0,0.1); }
        .recipe-info { padding: 25px; }
        .recipe-name { font-size: 22px; font-weight: 700; color: #2d3748; margin-bottom: 15px; }
        .recipe-meta { display: flex; flex-wrap: wrap; gap: 10px; margin-bottom: 20px; }
        .badge { padding: 6px 12px; border-radius: 20px; font-size: 12px; font-weight: 700; text-transform: uppercase; }
        .badge-facile { background: #c6f6d5; color: #22543d; }
        .badge-moyen { background: #feebc8; color: #744210; }
        .badge-difficile { background: #fed7d7; color: #742a2a; }
        .recipe-desc { font-size: 14px; line-height: 1.6; color: #718096; margin-bottom: 20px; }
        .recipe-footer { border-top: 1px solid #edf2f7; padding-top: 15px; display: flex; justify-content: space-between; font-size: 13px; color: #a0aec0; }
        .loading { text-align: center; padding: 100px; }
        .spinner { border: 4px solid rgba(0,0,0,0.1); border-left-color: #2e7d32; border-radius: 50%; width: 40px; height: 40px; animation: spin 1s linear infinite; margin: 0 auto 20px; }
        @keyframes spin { 100% { transform: rotate(360deg); } }
        footer { text-align: center; padding: 40px; margin-top: 100px; background: #2e7d32; color: white; }
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

        async function loadRecettes(page = 1, search = '') {
            const url = search 
                ? `${API_URL}?controller=Recette&action=rechercher&terme=${encodeURIComponent(search)}`
                : `${API_URL}?controller=Recette&action=obtenirTous&page=${page}&limite=9`;
            
            try {
                const res = await fetch(url);
                const data = await res.json();
                if(data.success) {
                    renderRecettes(data.recettes || data.allergies); // Match both JSON formats
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
                        <div id="details-${r.id_recette}" style="margin-top: 15px; font-size: 13px;">
                            <button class="btn" style="padding: 6px 12px; border: none; border-radius: 5px; background: #c6f6d5; color: #22543d; cursor: pointer; font-weight: bold;" onclick="loadDetails(${r.id_recette})">Voir ingrédients / étapes</button>
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
                h += `<button onclick="goToPage(${i})" style="padding: 10px 18px; border: none; border-radius: 8px; background: ${i === currentPage ? '#2e7d32' : 'white'}; color: ${i === currentPage ? 'white' : '#4a5568'}; cursor: pointer; font-weight: bold; box-shadow: 0 4px 6px rgba(0,0,0,0.05);">${i}</button>`;
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
            if(search.length > 2) loadRecettes(1, search);
            else if(search.length === 0) loadRecettes(1);
        };

        window.onload = () => loadRecettes();
    </script>
</body>
</html>
