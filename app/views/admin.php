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
        :root {
            --bg: #edf6ee;
            --surface: #ffffff;
            --surface-soft: #f8faf7;
            --primary: #2e7d32;
            --primary-soft: #d8ecd7;
            --text: #1f2937;
            --muted: #617d84;
            --shadow: 0 28px 70px rgba(46, 125, 50, 0.12);
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(180deg, #f6fbf5 0%, #edf6ee 100%);
            color: var(--text);
            min-height: 100vh;
            display: flex;
        }

        a {
            color: inherit;
            text-decoration: none;
        }

        .sidebar {
            width: 280px;
            background: linear-gradient(180deg, #2e7d32 0%, #1f5f24 100%);
            color: #f5fff5;
            min-height: 100vh;
            padding: 32px 24px;
            transition: all 0.3s ease;
            box-shadow: 6px 0 30px rgba(0, 0, 0, 0.08);
        }

        .sidebar h2 {
            margin-bottom: 32px;
            font-size: 1rem;
            letter-spacing: 0.15em;
            text-transform: uppercase;
            text-align: center;
            border-bottom: 1px solid rgba(255, 255, 255, 0.18);
            padding-bottom: 18px;
        }

        .sidebar a {
            display: flex;
            align-items: center;
            gap: 10px;
            color: rgba(255, 255, 255, 0.9);
            padding: 14px 18px;
            border-radius: 14px;
            margin-bottom: 10px;
            transition: all 0.25s ease;
            font-weight: 600;
            background: transparent;
        }

        .sidebar a:hover,
        .sidebar a.active {
            background: rgba(255, 255, 255, 0.12);
            color: #ffffff;
            transform: translateX(2px);
        }

        .main {
            flex: 1;
            padding: 32px 34px;
        }

        .navbar {
            background: rgba(255, 255, 255, 0.92);
            backdrop-filter: blur(10px);
            padding: 20px 26px;
            border-radius: 24px;
            box-shadow: var(--shadow);
            margin-bottom: 30px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 16px;
        }

        .navbar-brand {
            color: var(--primary);
            font-weight: 800;
            font-size: 1.35rem;
            letter-spacing: 0.01em;
        }

        .stat-card {
            position: relative;
            overflow: hidden;
            background: linear-gradient(180deg, #ffffff 0%, #f5faf4 100%);
            border: 1px solid rgba(46, 125, 50, 0.12);
            border-radius: 24px;
            padding: 30px 24px;
            text-align: center;
            box-shadow: 0 24px 65px rgba(46, 125, 50, 0.08);
            transition: transform 0.25s ease, box-shadow 0.25s ease;
            animation: fadeInUp 0.6s ease-out both;
        }

        .stat-card:hover {
            transform: translateY(-4px);
            box-shadow: 0 32px 90px rgba(46, 125, 50, 0.12);
        }

        .stat-card::before {
            content: '';
            position: absolute;
            inset: 0;
            background: radial-gradient(circle at top center, rgba(46, 125, 50, 0.14), transparent 40%);
            opacity: 0.9;
            pointer-events: none;
        }

        .stat-card h3,
        .stat-card p {
            position: relative;
            z-index: 1;
        }

        .stat-card h3 {
            color: #1c4d20;
            font-size: 3rem;
            margin-bottom: 12px;
        }

        .stat-card p {
            color: var(--muted);
            font-size: 0.95rem;
            letter-spacing: 0.01em;
        }

        .card {
            background: var(--surface);
            border-radius: 24px;
            box-shadow: var(--shadow);
            overflow: hidden;
            margin-bottom: 30px;
        }

        .card-header {
            background: linear-gradient(90deg, rgba(46, 125, 50, 0.16), rgba(111, 213, 130, 0.16));
            color: var(--primary);
            padding: 24px 28px;
            font-weight: 700;
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 14px;
        }

        .table {
            width: 100%;
            border-collapse: separate;
            border-spacing: 0 12px;
        }

        .table th {
            background: transparent;
            padding: 16px 14px 12px;
            text-align: left;
            color: var(--primary);
            font-weight: 700;
        }

        .table td {
            background: var(--surface-soft);
            color: var(--text);
            padding: 16px 14px;
            border-top: 1px solid rgba(46, 125, 50, 0.08);
            border-bottom: 1px solid rgba(46, 125, 50, 0.08);
            transition: background 0.25s ease, transform 0.25s ease;
        }

        .table tbody tr:hover td {
            background: #f2fbf1;
            transform: translateX(4px);
        }

        .table td:first-child {
            border-radius: 16px 0 0 16px;
        }

        .table td:last-child {
            border-radius: 0 16px 16px 0;
        }

        .btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            padding: 12px 20px;
            border: none;
            border-radius: 16px;
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
            color: #ffffff;
        }

        .btn-primary:hover {
            background: #235a1c;
        }

        .btn-secondary {
            background: #f7faf7;
            color: #2f4f32;
            border: 1px solid rgba(46, 125, 50, 0.16);
        }

        .btn-secondary:hover {
            background: #eef7ef;
        }

        .btn-sm {
            padding: 10px 14px;
            font-size: 0.92rem;
        }

        .btn-warning {
            background: #f59e0b;
            color: white;
        }

        .btn-danger {
            background: #e53e3e;
            color: white;
        }

        .badge {
            display: inline-flex;
            align-items: center;
            padding: 6px 12px;
            border-radius: 999px;
            font-size: 0.82rem;
            font-weight: 700;
            letter-spacing: 0.01em;
        }

        .bg-facile {
            background: #e6f4e5;
            color: #1f5220;
        }

        .bg-moyen {
            background: #fff2d7;
            color: #8b5c04;
        }

        .bg-difficile {
            background: #fde7e7;
            color: #981b22;
        }

        .modal {
            display: none;
            position: fixed;
            z-index: 1000;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.45);
            backdrop-filter: blur(4px);
        }

        .modal-content {
            background: var(--surface);
            margin: 4% auto;
            width: 92%;
            max-width: 620px;
            border-radius: 24px;
            overflow: hidden;
            animation: slideDown 0.35s ease-out;
            box-shadow: var(--shadow);
        }

        .modal-header {
            background: var(--primary);
            color: white;
            padding: 26px 30px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .modal-body {
            padding: 30px;
            max-height: 72vh;
            overflow-y: auto;
            background: var(--surface-soft);
        }

        .form-group {
            margin-bottom: 22px;
        }

        .form-group label {
            display: block;
            margin-bottom: 10px;
            font-weight: 700;
            color: #344e35;
        }

        .form-control {
            width: 100%;
            padding: 14px 16px;
            border: 2px solid #e6f1e8;
            border-radius: 16px;
            background: #ffffff;
            transition: border-color 0.2s ease, box-shadow 0.2s ease;
        }

        .form-control:focus {
            border-color: var(--primary);
            outline: none;
            box-shadow: 0 0 0 4px rgba(46, 125, 50, 0.12);
        }

        .is-invalid {
            border-color: #e53e3e !important;
        }

        .error-message {
            color: #e53e3e;
            font-size: 12px;
            margin-top: 6px;
        }

        @keyframes slideDown {
            from {
                transform: translateY(-50px);
                opacity: 0;
            }
            to {
                transform: translateY(0);
                opacity: 1;
            }
        }

        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
    </style>
    <script src="assets/js/validation.js"></script>
</head>
<body>
    <div class="sidebar">
        <h2>🥘 GESTION RECETTES</h2>
        <a href="#" onclick="showSection('dashboard', event)" class="active">📊 Dashboard</a>
        <a href="#" onclick="showSection('recettes', event)">📜 Mes Recettes</a>
        <a href="#" onclick="showSection('stats', event)">📈 Statistiques</a>
    </div>

    <div class="main">
        <div class="navbar">
            <span class="navbar-brand">👩‍🍳 Espace Administration</span>
            <div style="display: flex; align-items: center; gap: 15px;">
                <a href="client-dashboard.php" class="btn btn-success">Front Office</a>
                <span id="current-time"></span>
            </div>
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
                <div style="display: flex; gap: 12px; align-items: center;">
                    <button class="btn btn-primary" onclick="openModal()">➕ Ajouter une Recette</button>
                    <button class="btn btn-secondary" onclick="downloadPdf()">📄 Export PDF</button>
                </div>
            </div>
            
            <div style="display: grid; grid-template-columns: 1fr auto auto; gap: 15px; margin-bottom: 20px;">
                <input type="text" id="admin-search-input" class="form-control" placeholder="Rechercher nom, description, ingrédient...">
                <select id="admin-sort-select" class="form-control" style="max-width: 220px;">
                    <option value="date_creation">Trier par date</option>
                    <option value="nom">Nom</option>
                    <option value="nombre_personnes">Personnes</option>
                    <option value="calories_totales">Calories</option>
                    <option value="difficulte">Difficulté</option>
                </select>
                <select id="admin-order-select" class="form-control" style="max-width: 180px;">
                    <option value="DESC">Décroissant</option>
                    <option value="ASC">Croissant</option>
                </select>
            </div>

            <div style="display: grid; grid-template-columns: 1fr; gap: 30px; margin-bottom: 30px;">
                <div class="card">
                    <div class="card-header">
                        <span>🥕 Ajouter ingrédients / étapes</span>
                    </div>
                    <div style="padding: 20px; background: white;">
                        <form id="detail-form" novalidate>
                            <div style="display: grid; grid-template-columns: 1fr; gap: 15px;">
                                <div style="display: flex; gap: 10px; align-items: center;">
                                    <select name="id_recette" id="d-recette" class="form-control" style="flex: 1;">
                                        <option value="">Choisir recette</option>
                                    </select>
                                    <button type="button" class="btn btn-secondary" id="ai-details-btn" onclick="generateDetailsWithAI()">✨ Générer Auto (IA)</button>
                                </div>
                                <span id="ai-details-status" style="font-size: 0.85rem; color: #4a5568; margin-top: -10px;"></span>
                                <input type="text" name="ingredient" id="d-ingredient" class="form-control" placeholder="Ingrédient">
                                <input type="text" name="quantite" id="d-quantite" class="form-control" placeholder="Quantité">
                                <textarea name="etape" id="d-etape" class="form-control" placeholder="Étape de préparation" rows="2"></textarea>
                                <button type="submit" class="btn btn-primary">Ajouter détail</button>
                            </div>
                        </form>
                        
                        <div id="details-list-container" style="margin-top: 25px; border-top: 1px solid #edf2f7; padding-top: 20px; display: none;">
                            <h4 style="margin-bottom: 15px; color: #2e7d32; font-size: 1.1rem;">Détails enregistrés pour cette recette</h4>
                            <ul id="details-list" style="list-style-type: none; padding: 0;">
                                <!-- Rempli par JS -->
                            </ul>
                        </div>
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

        <div id="stats" class="page-section" style="display: none;">
            <div class="card" style="padding: 30px;">
                <h2>📈 Répartition par difficulté</h2>
                <p style="color: #4a5568; margin-bottom: 30px; max-width: 760px;">Voici la répartition des recettes en fonction de leur niveau de difficulté. Le graphique en anneau montre les proportions de chaque catégorie.</p>
                <div style="display: flex; flex-wrap: wrap; gap: 30px; align-items: center; justify-content: center;">
                    <div style="flex: 1 1 320px; max-width: 420px; text-align: center; position: relative;">
                        <canvas id="stats-canvas" width="360" height="360" style="max-width: 100%;"></canvas>
                        <div id="stats-center-text" style="position: absolute; inset: 0; display: grid; place-items: center; pointer-events: none; color: #1f2937; font-weight: 700; font-size: 28px;"></div>
                    </div>
                    <div style="flex: 1 1 320px; max-width: 360px;">
                        <div id="stats-legend" style="display: grid; gap: 15px;"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- MODAL -->
    <div id="recette-modal" class="modal">
        <div class="modal-content">
            <div class="modal-header" style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 10px;">
                <h3 id="modal-title" data-i18n="modal_add_title" style="margin: 0;">Ajouter une Recette</h3>
                <div style="display: flex; align-items: center; gap: 15px;">
                    <div class="lang-switcher" style="display: flex; gap: 5px; background: rgba(255,255,255,0.2); padding: 5px; border-radius: 8px;">
                        <button type="button" class="btn btn-sm btn-secondary lang-btn" onclick="changeLang('fr')" id="btn-lang-fr">FR</button>
                        <button type="button" class="btn btn-sm btn-secondary lang-btn" onclick="changeLang('en')" id="btn-lang-en">EN</button>
                        <button type="button" class="btn btn-sm btn-secondary lang-btn" onclick="changeLang('ar')" id="btn-lang-ar">AR</button>
                    </div>
                    <span style="cursor: pointer; font-size: 24px;" onclick="closeModal()">&times;</span>
                </div>
            </div>
            <form id="recette-form" novalidate>
                <div class="modal-body">
                    <input type="hidden" id="recette-id">
                    <div class="form-group">
                        <label data-i18n="label_name">Nom de la recette *</label>
                        <input type="text" name="nom" id="r-nom" class="form-control">
                    </div>
                    <div class="form-group" style="display:flex; align-items:center; gap:10px; margin-bottom:12px;">
                        <button type="button" id="ai-generate-btn" class="btn btn-secondary" data-i18n="btn_ai">Générer avec AI</button>
                        <button type="button" id="ai-image-btn" class="btn btn-secondary" onclick="generateImageWithAI()">📸 Générer Image IA</button>
                        <span id="ai-status" style="color:#4a5568; font-size:0.95rem;"></span>
                    </div>
                    <div id="image-preview-container" style="display:none; text-align:center; margin-bottom: 15px;">
                        <input type="hidden" name="image_url" id="r-image-url">
                        <img id="r-image-preview" src="" alt="Aperçu" style="max-width:100%; max-height:200px; border-radius:12px; box-shadow: 0 4px 6px rgba(0,0,0,0.1);">
                    </div>
                    <div class="form-group">
                        <label data-i18n="label_desc">Description *</label>
                        <textarea name="description" id="r-desc" class="form-control" rows="3"></textarea>
                    </div>
                    <div class="form-group">
                        <label data-i18n="label_ingredients">Ingrédients</label>
                        <textarea name="ingredients" id="r-ingredients" class="form-control" rows="3" placeholder="1 tasse de farine\n2 œufs\n1 pincée de sel"></textarea>
                    </div>
                    <div class="form-group">
                        <label data-i18n="label_steps">Étapes</label>
                        <textarea name="steps" id="r-steps" class="form-control" rows="4" placeholder="1. Préchauffer le four\n2. Mélanger les ingrédients"></textarea>
                    </div>
                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 15px;">
                        <div class="form-group">
                            <label data-i18n="label_persons">Nombre de personnes *</label>
                            <input type="number" name="nombre_personnes" id="r-pers" class="form-control">
                        </div>
                        <div class="form-group">
                            <label data-i18n="label_difficulty">Difficulté *</label>
                            <select name="difficulte" id="r-diff" class="form-control">
                                <option value="facile" data-i18n="diff_easy">Facile</option>
                                <option value="moyen" data-i18n="diff_medium" selected>Moyen</option>
                                <option value="difficile" data-i18n="diff_hard">Difficile</option>
                            </select>
                        </div>
                    </div>
                    <div style="display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 10px;">
                        <div class="form-group">
                            <label data-i18n="label_prep">Prépa (min) *</label>
                            <input type="number" name="temps_preparation" id="r-tprep" class="form-control">
                        </div>
                        <div class="form-group">
                            <label data-i18n="label_cook">Cuisson (min) *</label>
                            <input type="number" name="temps_cuisson" id="r-tcuiss" class="form-control">
                        </div>
                        <div class="form-group">
                            <label data-i18n="label_calories">Calories *</label>
                            <input type="number" name="calories_totales" id="r-cal" class="form-control">
                        </div>
                    </div>
                </div>
                <div style="padding: 20px; border-top: 1px solid #edf2f7; text-align: right; background: #f4f9f4;">
                    <button type="button" class="btn" style="background: #edf2f7; color: #4a5568;" onclick="closeModal()" data-i18n="btn_cancel">Annuler</button>
                    <button type="submit" class="btn btn-primary" data-i18n="btn_save">Enregistrer</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        const API_URL = '../../index.php';

        const translations = {
            fr: {
                modal_add_title: "Ajouter une Recette",
                modal_edit_title: "Modifier la Recette",
                label_name: "Nom de la recette *",
                label_desc: "Description *",
                label_ingredients: "Ingrédients",
                label_steps: "Étapes",
                label_persons: "Nombre de personnes *",
                label_difficulty: "Difficulté *",
                label_prep: "Prépa (min) *",
                label_cook: "Cuisson (min) *",
                label_calories: "Calories *",
                btn_ai: "Générer avec AI",
                btn_cancel: "Annuler",
                btn_save: "Enregistrer",
                diff_easy: "Facile",
                diff_medium: "Moyen",
                diff_hard: "Difficile",
                ai_status_generating: "Génération en cours...",
                ai_status_success: "Contenu AI généré avec succès.",
                ai_status_empty: "Veuillez saisir le nom de la recette."
            },
            en: {
                modal_add_title: "Add a Recipe",
                modal_edit_title: "Edit Recipe",
                label_name: "Recipe Name *",
                label_desc: "Description *",
                label_ingredients: "Ingredients",
                label_steps: "Steps",
                label_persons: "Number of persons *",
                label_difficulty: "Difficulty *",
                label_prep: "Prep time (min) *",
                label_cook: "Cooking time (min) *",
                label_calories: "Calories *",
                btn_ai: "Generate with AI",
                btn_cancel: "Cancel",
                btn_save: "Save",
                diff_easy: "Easy",
                diff_medium: "Medium",
                diff_hard: "Hard",
                ai_status_generating: "Generating...",
                ai_status_success: "AI content generated successfully.",
                ai_status_empty: "Please enter a recipe name."
            },
            ar: {
                modal_add_title: "إضافة وصفة",
                modal_edit_title: "تعديل الوصفة",
                label_name: "اسم الوصفة *",
                label_desc: "الوصف *",
                label_ingredients: "المكونات",
                label_steps: "خطوات التحضير",
                label_persons: "عدد الأشخاص *",
                label_difficulty: "مستوى الصعوبة *",
                label_prep: "التحضير (دقيقة) *",
                label_cook: "الطبخ (دقيقة) *",
                label_calories: "السعرات الحرارية *",
                btn_ai: "توليد بواسطة الذكاء الاصطناعي",
                btn_cancel: "إلغاء",
                btn_save: "حفظ",
                diff_easy: "سهل",
                diff_medium: "متوسط",
                diff_hard: "صعب",
                ai_status_generating: "جاري التوليد...",
                ai_status_success: "تم توليد المحتوى بنجاح.",
                ai_status_empty: "الرجاء إدخال اسم الوصفة."
            }
        };

        let currentSearch = '';
        let currentSortBy = 'date_creation';
        let currentOrder = 'DESC';
        let currentLang = localStorage.getItem('appLang') || 'fr';
        let currentAiData = null;

        function fillData(data, lang) {
            if (!data || !data[lang]) return;
            document.getElementById('r-desc').value = data[lang].description || '';
            
            let ing = data[lang].ingredients || '';
            if (Array.isArray(ing)) ing = ing.join('\n');
            document.getElementById('r-ingredients').value = ing;
            
            let stp = data[lang].steps || '';
            if (Array.isArray(stp)) {
                stp = stp.map((s, i) => `${i + 1}. ${s}`).join('\n');
            }
            document.getElementById('r-steps').value = stp;
        }

        function changeLang(lang) {
            currentLang = lang;
            localStorage.setItem('appLang', lang);
            document.dir = lang === 'ar' ? 'rtl' : 'ltr';

            document.querySelectorAll('.lang-btn').forEach(btn => {
                btn.classList.remove('btn-primary');
                btn.classList.add('btn-secondary');
            });
            const activeBtn = document.getElementById('btn-lang-' + lang);
            if (activeBtn) {
                activeBtn.classList.remove('btn-secondary');
                activeBtn.classList.add('btn-primary');
            }

            document.querySelectorAll('[data-i18n]').forEach(el => {
                const key = el.getAttribute('data-i18n');
                if (translations[lang] && translations[lang][key]) {
                    el.innerText = translations[lang][key];
                }
            });

            if (currentAiData) {
                fillData(currentAiData, currentLang);
            }
        }

        function showSection(id, e) {
            if (e) e.preventDefault();
            document.querySelectorAll('.page-section').forEach(s => s.style.display = 'none');
            document.getElementById(id).style.display = 'block';
            document.querySelectorAll('.sidebar a').forEach(a => a.classList.remove('active'));
            if (e && e.target) e.target.classList.add('active');
            if (id === 'dashboard') loadDashboard();
            if (id === 'recettes') loadRecettes();
            if (id === 'stats') loadStats();
        }

        function animateNumber(element, target, duration = 700) {
            const start = 0;
            const startTime = performance.now();
            element.innerText = '0';

            function tick(now) {
                const elapsed = now - startTime;
                const progress = Math.min(elapsed / duration, 1);
                const current = Math.floor(progress * (target - start) + start);
                element.innerText = current;
                if (progress < 1) {
                    requestAnimationFrame(tick);
                }
            }

            requestAnimationFrame(tick);
        }

        async function loadDashboard() {
            const res = await fetch(`${API_URL}?controller=Recette&action=obtenirStatistiques`);
            const json = await res.json();
            if (json.success) {
                const stats = json.statistics;
                animateNumber(document.getElementById('stat-total'), stats.total);
                animateNumber(document.getElementById('stat-facile'), stats.facile);
                animateNumber(document.getElementById('stat-moyen'), stats.moyen);
                animateNumber(document.getElementById('stat-difficile'), stats.difficile);

                const list = document.getElementById('dashboard-list');
                let h = '<table class="table"><thead><tr><th>Nom</th><th>Difficulté</th></tr></thead><tbody>';
                const recettes = await fetch(`${API_URL}?controller=Recette&action=obtenirTous&limite=5`).then(r => r.json());
                if (recettes.success) {
                    recettes.recettes.slice(0, 5).forEach(r => {
                        h += `<tr><td>${r.nom}</td><td><span class="badge bg-${r.difficulte}">${r.difficulte}</span></td></tr>`;
                    });
                }
                h += '</tbody></table>';
                list.innerHTML = h;
            }
        }

        async function loadStats() {
            const res = await fetch(`${API_URL}?controller=Recette&action=obtenirStatistiques`);
            const json = await res.json();
            if (!json.success) {
                document.getElementById('stats-legend').innerHTML = `<div style="color:#e53e3e;">Erreur : ${json.message}</div>`;
                return;
            }

            const stats = json.statistics;
            const total = stats.total || 0;
            const segments = [
                { label: 'Facile', value: stats.facile || 0, color: '#2e7d32' },
                { label: 'Moyen', value: stats.moyen || 0, color: '#f59e0b' },
                { label: 'Difficile', value: stats.difficile || 0, color: '#ef4444' }
            ];
            renderStatsChart(segments, total);
            renderStatsLegend(segments, total);
        }

        function renderStatsChart(segments, total) {
            const canvas = document.getElementById('stats-canvas');
            const ctx = canvas.getContext('2d');
            const width = canvas.width;
            const height = canvas.height;
            const centerX = width / 2;
            const centerY = height / 2;
            const radius = Math.min(width, height) / 2 - 30;
            const lineWidth = 30;
            const duration = 900;
            const startTime = performance.now();

            const centerText = document.getElementById('stats-center-text');
            centerText.innerHTML = `<div id="stats-center-value">0</div><div style="font-size: 14px; color:#6b7280;">Recettes</div>`;
            animateNumber(document.getElementById('stats-center-value'), total, duration);

            function drawFrame(now) {
                const progress = Math.min((now - startTime) / duration, 1);
                ctx.clearRect(0, 0, width, height);
                let currentStart = -Math.PI / 2;

                if (total > 0) {
                    segments.forEach(segment => {
                        const slice = segment.value / total;
                        if (slice <= 0) return;
                        const targetEnd = currentStart + slice * 2 * Math.PI;
                        const endAngle = currentStart + (targetEnd - currentStart) * progress;
                        ctx.beginPath();
                        ctx.arc(centerX, centerY, radius, currentStart, endAngle);
                        ctx.strokeStyle = segment.color;
                        ctx.lineWidth = lineWidth;
                        ctx.lineCap = 'butt';
                        ctx.stroke();
                        currentStart = targetEnd;
                    });
                } else {
                    ctx.beginPath();
                    ctx.arc(centerX, centerY, radius, 0, 2 * Math.PI);
                    ctx.strokeStyle = '#e5e7eb';
                    ctx.lineWidth = lineWidth;
                    ctx.stroke();
                }

                ctx.beginPath();
                ctx.arc(centerX, centerY, radius - lineWidth + 4, 0, 2 * Math.PI);
                ctx.fillStyle = '#fff';
                ctx.fill();

                if (progress < 1) {
                    requestAnimationFrame(drawFrame);
                }
            }

            requestAnimationFrame(drawFrame);
        }

        function renderStatsLegend(segments, total) {
            const legend = document.getElementById('stats-legend');
            legend.innerHTML = segments.map(segment => {
                const percent = total > 0 ? Math.round(segment.value * 100 / total) : 0;
                return `
                    <div style="display:flex; align-items:center; gap:12px; padding:12px 0; border-bottom: 1px solid #e2e8f0;">
                        <span style="width: 16px; height: 16px; border-radius: 4px; background: ${segment.color}; display:inline-block;"></span>
                        <div>
                            <div style="font-weight:700; color:#111827;">${segment.label}</div>
                            <div style="font-size:13px; color:#6b7280;">${segment.value} recettes • ${percent}%</div>
                        </div>
                    </div>`;
            }).join('');
        }

        async function loadRecettes(search = '', sortBy = 'date_creation', order = 'DESC') {
            currentSearch = search;
            currentSortBy = sortBy;
            currentOrder = order;
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
                params.set('page', 1);
                params.set('limite', 50);
            }

            const res = await fetch(`${API_URL}?${params.toString()}`);
            const json = await res.json();
            if (json.success) {
                const body = document.getElementById('recettes-body');
                const dRecette = document.getElementById('d-recette');
                let h = '';
                let opt = '<option value="">Choisir recette</option>';
                json.recettes.forEach(r => {
                    opt += `<option value="${r.id_recette}">${r.nom}</option>`;
                    const imgThumb = r.image_url ? `<img src="${r.image_url}" style="width:50px; height:50px; border-radius:8px; object-fit:cover; margin-right:10px; vertical-align:middle;">` : '';
                    h += `<tr>
                        <td>${imgThumb}<strong>${r.nom}</strong></td>
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
                if (dRecette) dRecette.innerHTML = opt;
            }
        }

        function downloadPdf() {
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
        }

        document.getElementById('detail-form').onsubmit = async (e) => {
            e.preventDefault();
            if(!validerFormulaireDetail()) return;
            const formData = new FormData(e.target);
            const data = Object.fromEntries(formData.entries());

            const res = await fetch(`${API_URL}?controller=DetailRecette&action=creer`, {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify(data)
            });

            const json = await res.json();
            if(json.success) {
                e.target.elements['ingredient'].value = '';
                e.target.elements['quantite'].value = '';
                e.target.elements['etape'].value = '';
                loadDetailsForRecipe(data.id_recette);
            } else {
                alert("Erreur: " + json.message);
            }
        };

        async function loadDetailsForRecipe(id_recette) {
            const container = document.getElementById('details-list-container');
            const list = document.getElementById('details-list');
            if (!id_recette) {
                container.style.display = 'none';
                return;
            }
            
            const res = await fetch(`${API_URL}?controller=DetailRecette&action=obtenirParRecette&id_recette=${id_recette}`);
            const json = await res.json();
            
            if (json.success) {
                container.style.display = 'block';
                if (json.details.length === 0) {
                    list.innerHTML = '<li style="color: #a0aec0; font-style: italic;">Aucun détail enregistré pour le moment.</li>';
                } else {
                    let h = '';
                    json.details.forEach(d => {
                        let text = '';
                        if (d.ingredient) text = `🥕 <strong>${d.quantite ? d.quantite + ' ' : ''}</strong>${d.ingredient}`;
                        if (d.etape) text = `📝 <em>${d.etape}</em>`;
                        h += `<li style="padding: 12px 16px; background: #f8faf7; border: 1px solid #e6f1e8; margin-bottom: 8px; border-radius: 12px; display: flex; justify-content: space-between; align-items: center;">
                            <span>${text}</span>
                            <button type="button" class="btn btn-sm btn-danger" onclick="deleteDetail(${d.id_detail}, ${id_recette})" style="padding: 4px 8px; border-radius: 8px;">✕</button>
                        </li>`;
                    });
                    list.innerHTML = h;
                }
            }
        }

        async function deleteDetail(id_detail, id_recette) {
            if(!confirm("Supprimer ce détail ?")) return;
            const res = await fetch(`${API_URL}?controller=DetailRecette&action=supprimer&id=${id_detail}`, { method: 'DELETE' });
            const json = await res.json();
            if(json.success) {
                loadDetailsForRecipe(id_recette);
            }
        }

        function openModal(data = null) {
            const modal = document.getElementById('recette-modal');
            const form = document.getElementById('recette-form');
            form.reset();
            currentAiData = null;
            document.getElementById('ai-status').innerText = '';
            document.getElementById('recette-id').value = '';
            document.getElementById('modal-title').innerText = translations[currentLang].modal_add_title;
            
            document.getElementById('image-preview-container').style.display = 'none';
            document.getElementById('r-image-url').value = '';
            document.getElementById('r-image-preview').src = '';
            
            if(data) {
                document.getElementById('modal-title').innerText = translations[currentLang].modal_edit_title;
                document.getElementById('recette-id').value = data.id_recette;
                document.getElementById('r-nom').value = data.nom;
                document.getElementById('r-desc').value = data.description;
                document.getElementById('r-pers').value = data.nombre_personnes;
                document.getElementById('r-diff').value = data.difficulte;
                document.getElementById('r-tprep').value = data.temps_preparation;
                document.getElementById('r-tcuiss').value = data.temps_cuisson;
                document.getElementById('r-cal').value = data.calories_totales;
                
                if (data.image_url) {
                    document.getElementById('image-preview-container').style.display = 'block';
                    document.getElementById('r-image-url').value = data.image_url;
                    document.getElementById('r-image-preview').src = data.image_url;
                }
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

        async function generateRecipeWithAI() {
            const nameInput = document.getElementById('r-nom');
            const status = document.getElementById('ai-status');
            const button = document.getElementById('ai-generate-btn');

            const recetteName = nameInput.value.trim();
            if (!recetteName) {
                status.innerText = translations[currentLang].ai_status_empty;
                status.style.color = '#e53e3e';
                return;
            }

            button.disabled = true;
            status.style.color = '#4a5568';
            status.innerText = translations[currentLang].ai_status_generating;

            try {
                const response = await fetch('../../index.php?controller=Ai&action=genererRecette', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify({ nom_recette: recetteName })
                });

                if (!response.ok) {
                    const text = await response.text();
                    throw new Error(`Erreur serveur : ${response.status} ${response.statusText} - ${text}`);
                }

                const json = await response.json();
                if (!json.success) {
                    throw new Error(json.message || 'Réponse AI invalide.');
                }

                currentAiData = json.data;
                fillData(currentAiData, currentLang);
                
                status.style.color = '#16a34a';
                status.innerText = translations[currentLang].ai_status_success;
            } catch (error) {
                status.style.color = '#e53e3e';
                status.innerText = error.message;
                console.error('AI generation error:', error);
            } finally {
                button.disabled = false;
            }
        }

        async function generateImageWithAI() {
            const nameInput = document.getElementById('r-nom');
            const status = document.getElementById('ai-status');
            const button = document.getElementById('ai-image-btn');
            
            const recetteName = nameInput.value.trim();
            if (!recetteName) {
                status.innerText = "Veuillez d'abord saisir le nom de la recette pour générer l'image.";
                status.style.color = '#e53e3e';
                return;
            }

            button.disabled = true;
            status.style.color = '#4a5568';
            status.innerText = "🎨 Peinture de l'image en cours... (ça peut prendre 10s)";

            try {
                const response = await fetch(`${API_URL}?controller=Ai&action=genererImage`, {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({ nom_recette: recetteName })
                });

                const json = await response.json();
                if (!json.success) throw new Error(json.message);

                document.getElementById('image-preview-container').style.display = 'block';
                document.getElementById('r-image-url').value = json.image_url;
                document.getElementById('r-image-preview').src = json.image_url;

                status.style.color = '#16a34a';
                status.innerText = "Image générée avec succès !";
            } catch (error) {
                status.style.color = '#e53e3e';
                status.innerText = "Erreur Image IA : " + error.message;
            } finally {
                button.disabled = false;
            }
        }

        function editRecette(r) {
            openModal(r);
        }

        async function generateDetailsWithAI() {
            const select = document.getElementById('d-recette');
            const idRecette = select.value;
            if (!idRecette) {
                alert("Veuillez d'abord choisir une recette dans la liste.");
                return;
            }
            const nomRecette = select.options[select.selectedIndex].text;
            const status = document.getElementById('ai-details-status');
            const btn = document.getElementById('ai-details-btn');

            btn.disabled = true;
            status.innerText = "Génération IA en cours... veuillez patienter.";
            status.style.color = '#4a5568';

            try {
                const response = await fetch('../../index.php?controller=Ai&action=genererDetails', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({ nom_recette: nomRecette })
                });

                const json = await response.json();
                if (!json.success) throw new Error(json.message);

                const dataLang = json.data[currentLang] || json.data.fr;
                const ingredients = dataLang.ingredients || [];
                const steps = dataLang.steps || [];

                // Remplir visuellement les attributs (champs du formulaire)
                document.getElementById('d-ingredient').value = ingredients.join(' | ');
                document.getElementById('d-quantite').value = 'Automatique'; // Optionnel, juste pour montrer que ça a marché
                document.getElementById('d-etape').value = steps.map((s, i) => `${i+1}. ${s}`).join('\n');

                status.innerText = "Attributs remplis avec succès ! Vous pouvez maintenant cliquer sur 'Ajouter détail'.";
                status.style.color = '#16a34a';
                
            } catch(err) {
                status.innerText = "Erreur IA : " + err.message;
                status.style.color = '#e53e3e';
            } finally {
                btn.disabled = false;
            }
        }

        window.onload = () => {
            changeLang(currentLang);
            document.getElementById('current-time').innerText = new Date().toLocaleString();
            loadDashboard();
            const searchInput = document.getElementById('admin-search-input');
            const sortSelect = document.getElementById('admin-sort-select');
            const orderSelect = document.getElementById('admin-order-select');

            if (searchInput) {
                searchInput.oninput = (e) => {
                    const search = e.target.value.trim();
                    loadRecettes(search, currentSortBy, currentOrder);
                };
            }
            if (sortSelect) {
                sortSelect.onchange = (e) => {
                    currentSortBy = e.target.value;
                    loadRecettes(currentSearch, currentSortBy, currentOrder);
                };
            }
            if (orderSelect) {
                orderSelect.onchange = (e) => {
                    currentOrder = e.target.value;
                    loadRecettes(currentSearch, currentSortBy, currentOrder);
                };
            }

            const aiButton = document.getElementById('ai-generate-btn');
            if (aiButton) {
                aiButton.onclick = generateRecipeWithAI;
            }

            const recetteSelect = document.getElementById('d-recette');
            if (recetteSelect) {
                recetteSelect.addEventListener('change', (e) => {
                    loadDetailsForRecipe(e.target.value);
                });
            }
        };
    </script>
</body>
</html>
