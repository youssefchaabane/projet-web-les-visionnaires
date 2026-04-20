<?php
/**
 * Dashboard Admin - Gestion complète des Recettes
 * Point d'accès: http://localhost/gestion-recettes/app/views/admin.php
 */
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - Gestion Recettes</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; background-color: #f4f9f4; display: flex; color: #333; }
        .sidebar { width: 250px; background: #2e7d32; color: white; min-height: 100vh; padding: 20px; transition: all 0.3s; }
        .sidebar h2 { margin-bottom: 30px; font-size: 20px; text-align: center; border-bottom: 1px solid rgba(255,255,255,0.2); padding-bottom: 15px; }
        .sidebar a { display: block; color: rgba(255,255,255,0.8); text-decoration: none; margin: 10px 0; padding: 12px; border-radius: 8px; transition: all 0.3s; }
        .sidebar a:hover, .sidebar a.active { background: rgba(255,255,255,0.2); color: white; font-weight: bold; }
        .main { flex: 1; padding: 30px; }
        .navbar { background: white; padding: 15px 25px; box-shadow: 0 4px 12px rgba(0,0,0,0.05); margin-bottom: 30px; display: flex; justify-content: space-between; align-items: center; border-radius: 12px; }
        .navbar-brand { color: #2e7d32; font-weight: 800; font-size: 22px; }
        .stat-card { background: white; border-radius: 15px; padding: 25px; text-align: center; box-shadow: 0 10px 15px -3px rgba(0,0,0,0.1); border-bottom: 4px solid #2e7d32; }
        .stat-card h3 { color: #2e7d32; font-size: 2.5rem; font-weight: 800; margin: 10px 0; }
        .card { background: white; border-radius: 15px; box-shadow: 0 10px 15px -3px rgba(0,0,0,0.1); overflow: hidden; margin-bottom: 30px; }
        .card-header { background: #a5d6a7; color: #1b5e20; padding: 20px; font-weight: bold; display: flex; justify-content: space-between; align-items: center; }
        .table { width: 100%; border-collapse: collapse; }
        .table th { background: #f4f9f4; padding: 15px; text-align: left; color: #1b5e20; border-bottom: 2px solid #c8e6c9; }
        .table td { padding: 15px; border-bottom: 1px solid #c8e6c9; }
        .btn { padding: 10px 20px; border: none; border-radius: 8px; cursor: pointer; font-weight: 600; transition: all 0.2s; }
        .btn-primary { background: #2e7d32; color: white; }
        .btn-primary:hover { background: #1b5e20; transform: translateY(-2px); }
        .btn-sm { padding: 6px 12px; font-size: 13px; }
        .btn-warning { background: #ed8936; color: white; }
        .btn-danger { background: #e53e3e; color: white; }
        .badge { padding: 4px 12px; border-radius: 9999px; font-size: 12px; font-weight: bold; }
        .bg-facile { background: #c6f6d5; color: #22543d; }
        .bg-moyen { background: #feebc8; color: #744210; }
        .bg-difficile { background: #fed7d7; color: #742a2a; }
        .modal { display: none; position: fixed; z-index: 1000; left: 0; top: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.5); backdrop-filter: blur(4px); }
        .modal-content { background: white; margin: 5% auto; width: 90%; max-width: 600px; border-radius: 20px; overflow: hidden; animation: slideDown 0.3s ease-out; }
        @keyframes slideDown { from { transform: translateY(-50px); opacity: 0; } to { transform: translateY(0); opacity: 1; } }
        .modal-header { background: #2e7d32; color: white; padding: 25px; display: flex; justify-content: space-between; align-items: center; }
        .modal-body { padding: 30px; max-height: 70vh; overflow-y: auto; }
        .form-group { margin-bottom: 20px; }
        .form-group label { display: block; margin-bottom: 8px; font-weight: 600; color: #4a5568; }
        .form-control { width: 100%; padding: 12px; border: 2px solid #edf2f7; border-radius: 10px; transition: all 0.2s; }
        .form-control:focus { border-color: #2e7d32; outline: none; }
        .is-invalid { border-color: #e53e3e !important; }
        .error-message { color: #e53e3e; font-size: 12px; margin-top: 5px; }
    </style>
    <script src="assets/js/validation.js"></script>
</head>
<body>
    <div class="sidebar">
        <h2>🥘 GESTION RECETTES</h2>
        <a href="#" onclick="showSection('dashboard')" class="active">📊 Dashboard</a>
        <a href="#" onclick="showSection('recettes')">📜 Mes Recettes</a>
        <a href="#" onclick="showSection('stats')">📈 Statistiques</a>
    </div>

    <div class="main">
        <div class="navbar">
            <span class="navbar-brand">👩‍🍳 Espace Administration</span>
            <div id="current-time"></div>
        </div>

        <div id="dashboard" class="page-section active">
            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 25px; margin-bottom: 40px;">
                <div class="stat-card">
                    <h3 id="stat-total">0</h3>
                    <p>Total Recettes</p>
                </div>
                <div class="stat-card">
                    <h3 id="stat-facile">0</h3>
                    <p>Faciles</p>
                </div>
                <div class="stat-card">
                    <h3 id="stat-moyen">0</h3>
                    <p>Moyennes</p>
                </div>
                <div class="stat-card">
                    <h3 id="stat-difficile">0</h3>
                    <p>Difficiles</p>
                </div>
            </div>

            <div class="card">
                <div class="card-header">
                    <span>🕒 Dernières recettes ajoutées</span>
                    <button class="btn btn-primary btn-sm" onclick="openModal()">➕ Nouvelle Recette</button>
                </div>
                <div id="dashboard-list">
                    <p style="padding: 20px; text-align: center; color: #a0aec0;">Chargement...</p>
                </div>
            </div>
        </div>

        <div id="recettes" class="page-section" style="display: none;">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 25px;">
                <h2>📜 Gestion des Recettes</h2>
                <button class="btn btn-primary" onclick="openModal()">➕ Ajouter une Recette</button>
            </div>
            
            <div style="display: grid; grid-template-columns: 1fr; gap: 30px; margin-bottom: 30px;">
                <div class="card">
                    <div class="card-header">
                        <span>🥕 Ajouter ingrédients / étapes</span>
                    </div>
                    <div style="padding: 20px; background: white;">
                        <form id="detail-form">
                            <div style="display: grid; grid-template-columns: 1fr; gap: 15px;">
                                <select name="id_recette" id="d-recette" class="form-control" required>
                                    <option value="">Choisir recette</option>
                                </select>
                                <input type="text" name="ingredient" id="d-ingredient" class="form-control" placeholder="Ingrédient" required>
                                <input type="text" name="quantite" id="d-quantite" class="form-control" placeholder="Quantité" required>
                                <textarea name="etape" id="d-etape" class="form-control" placeholder="Étape de préparation" rows="2" required></textarea>
                                <button type="submit" class="btn btn-primary">Ajouter détail</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <div class="card">
                <table class="table">
                    <thead>
                        <tr>
                            <th>Nom</th>
                            <th>Personnes</th>
                            <th>Prépa / Cuisson</th>
                            <th>Difficulté</th>
                            <th>Calories</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody id="recettes-body">
                        <!-- Rempli par JS -->
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- MODAL -->
    <div id="recette-modal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h3 id="modal-title">Ajouter une Recette</h3>
                <span style="cursor: pointer; font-size: 24px;" onclick="closeModal()">&times;</span>
            </div>
            <form id="recette-form">
                <div class="modal-body">
                    <input type="hidden" id="recette-id">
                    <div class="form-group">
                        <label>Nom de la recette *</label>
                        <input type="text" name="nom" id="r-nom" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label>Description *</label>
                        <textarea name="description" id="r-desc" class="form-control" rows="3" required></textarea>
                    </div>
                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 15px;">
                        <div class="form-group">
                            <label>Nombre de personnes *</label>
                            <input type="number" name="nombre_personnes" id="r-pers" class="form-control" required min="1">
                        </div>
                        <div class="form-group">
                            <label>Difficulté *</label>
                            <select name="difficulte" id="r-diff" class="form-control" required>
                                <option value="facile">Facile</option>
                                <option value="moyen" selected>Moyen</option>
                                <option value="difficile">Difficile</option>
                            </select>
                        </div>
                    </div>
                    <div style="display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 10px;">
                        <div class="form-group">
                            <label>Prépa (min) *</label>
                            <input type="number" name="temps_preparation" id="r-tprep" class="form-control" required>
                        </div>
                        <div class="form-group">
                            <label>Cuisson (min) *</label>
                            <input type="number" name="temps_cuisson" id="r-tcuiss" class="form-control" required>
                        </div>
                        <div class="form-group">
                            <label>Calories *</label>
                            <input type="number" name="calories_totales" id="r-cal" class="form-control" required>
                        </div>
                    </div>
                </div>
                <div style="padding: 20px; border-top: 1px solid #edf2f7; text-align: right; background: #f4f9f4;">
                    <button type="button" class="btn" style="background: #edf2f7; color: #4a5568;" onclick="closeModal()">Annuler</button>
                    <button type="submit" class="btn btn-primary">Enregistrer</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        const API_URL = '../../index.php';

        function showSection(id) {
            document.querySelectorAll('.page-section').forEach(s => s.style.display = 'none');
            document.getElementById(id).style.display = 'block';
            document.querySelectorAll('.sidebar a').forEach(a => a.classList.remove('active'));
            event.target.classList.add('active');
            if(id === 'dashboard') loadDashboard();
            if(id === 'recettes') loadRecettes();
        }

        async function loadDashboard() {
            const res = await fetch(`${API_URL}?controller=Recette&action=obtenirTous`);
            const json = await res.json();
            if(json.success) {
                const list = document.getElementById('dashboard-list');
                document.getElementById('stat-total').innerText = json.pagination.total;
                
                let h = '<table class="table"><thead><tr><th>Nom</th><th>Difficulté</th></tr></thead><tbody>';
                json.recettes.slice(0, 5).forEach(r => {
                    h += `<tr><td>${r.nom}</td><td><span class="badge bg-${r.difficulte}">${r.difficulte}</span></td></tr>`;
                });
                h += '</tbody></table>';
                list.innerHTML = h;

                // Stats (Simplifié)
                document.getElementById('stat-facile').innerText = json.recettes.filter(r => r.difficulte === 'facile').length;
                document.getElementById('stat-moyen').innerText = json.recettes.filter(r => r.difficulte === 'moyen').length;
                document.getElementById('stat-difficile').innerText = json.recettes.filter(r => r.difficulte === 'difficile').length;
            }
        }

        async function loadRecettes() {
            const res = await fetch(`${API_URL}?controller=Recette&action=obtenirTous`);
            const json = await res.json();
            if(json.success) {
                const body = document.getElementById('recettes-body');
                const dRecette = document.getElementById('d-recette');
                let h = '';
                let opt = '<option value="">Choisir recette</option>';
                json.recettes.forEach(r => {
                    opt += `<option value="${r.id_recette}">${r.nom}</option>`;
                    h += `<tr>
                        <td><strong>${r.nom}</strong></td>
                        <td>${r.nombre_personnes} pers.</td>
                        <td>${r.temps_preparation} + ${r.temps_cuisson} min</td>
                        <td><span class="badge bg-${r.difficulte}">${r.difficulte}</span></td>
                        <td>${r.calories_totales} kcal</td>
                        <td>
                            <button class="btn btn-sm btn-warning" onclick='editRecette(${JSON.stringify(r)})'>✏️</button>
                            <button class="btn btn-sm btn-danger" onclick="deleteRecette(${r.id_recette})">🗑️</button>
                        </td>
                    </tr>`;
                });
                body.innerHTML = h;
                if(dRecette) dRecette.innerHTML = opt;
            }
        }

        document.getElementById('detail-form').onsubmit = async (e) => {
            e.preventDefault();
            const formData = new FormData(e.target);
            const data = Object.fromEntries(formData.entries());

            const res = await fetch(`${API_URL}?controller=DetailRecette&action=creer`, {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify(data)
            });

            const json = await res.json();
            if(json.success) {
                alert(json.message);
                e.target.reset();
            } else {
                alert("Erreur: " + json.message);
            }
        };

        function openModal(data = null) {
            const modal = document.getElementById('recette-modal');
            const form = document.getElementById('recette-form');
            form.reset();
            document.getElementById('recette-id').value = '';
            document.getElementById('modal-title').innerText = "Ajouter une Recette";
            
            if(data) {
                document.getElementById('modal-title').innerText = "Modifier la Recette";
                document.getElementById('recette-id').value = data.id_recette;
                document.getElementById('r-nom').value = data.nom;
                document.getElementById('r-desc').value = data.description;
                document.getElementById('r-pers').value = data.nombre_personnes;
                document.getElementById('r-diff').value = data.difficulte;
                document.getElementById('r-tprep').value = data.temps_preparation;
                document.getElementById('r-tcuiss').value = data.temps_cuisson;
                document.getElementById('r-cal').value = data.calories_totales;
            }
            modal.style.display = 'block';
        }

        function closeModal() {
            document.getElementById('recette-modal').style.display = 'none';
        }

        document.getElementById('recette-form').onsubmit = async (e) => {
            e.preventDefault();
            if(!validerFormulaireRecette()) return;

            const id = document.getElementById('recette-id').value;
            const action = id ? 'mettre_a_jour' : 'creer';
            const url = id ? `${API_URL}?controller=Recette&action=${action}&id=${id}` : `${API_URL}?controller=Recette&action=${action}`;
            
            const formData = new FormData(e.target);
            const data = Object.fromEntries(formData.entries());

            const res = await fetch(url, {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify(data)
            });

            const json = await res.json();
            if(json.success) {
                alert(json.message);
                closeModal();
                loadRecettes();
                loadDashboard();
            } else {
                alert("Erreur: " + json.message);
            }
        };

        async function deleteRecette(id) {
            if(!confirm("Supprimer cette recette ?")) return;
            const res = await fetch(`${API_URL}?controller=Recette&action=supprimer&id=${id}`, { method: 'DELETE' });
            const json = await res.json();
            if(json.success) {
                loadRecettes();
                loadDashboard();
            }
        }

        function editRecette(r) {
            openModal(r);
        }

        window.onload = () => {
            document.getElementById('current-time').innerText = new Date().toLocaleString();
            loadDashboard();
        };
    </script>
</body>
</html>
