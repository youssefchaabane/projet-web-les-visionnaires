<?php
/**
 * Dashboard Admin - Gestion complète du stock
 * Style ECOSAVE (Vert écologique)
 * Structure basée sur gestion-allergies
 * Point d'accès: http://localhost/gestion-stock/app/views/admin.php
 */
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - ECOSAVE Stock</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f9f4;
            display: flex;
        }

        .sidebar {
            width: 240px;
            background: #2e7d32;
            color: white;
            min-height: 100vh;
            padding: 20px;
            position: fixed;
            left: 0;
            top: 0;
            overflow-y: auto;
        }

        .sidebar h2 {
            margin-bottom: 30px;
            font-size: 18px;
            color: white;
            text-align: center;
        }

        .sidebar a {
            display: block;
            color: white;
            text-decoration: none;
            margin: 15px 0;
            padding: 12px 15px;
            border-radius: 5px;
            transition: all 0.3s;
            cursor: pointer;
            font-size: 14px;
        }

        .sidebar a:hover,
        .sidebar a.active {
            background: rgba(255,255,255,0.2);
            color: white;
            transform: translateX(5px);
        }

        .main {
            flex: 1;
            padding: 20px;
            margin-left: 240px;
        }

        .navbar {
            background: white;
            padding: 15px 20px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
            margin-bottom: 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            border-radius: 8px;
        }

        .navbar-brand {
            color: #2e7d32;
            font-weight: bold;
            font-size: 18px;
        }

        .navbar-buttons {
            display: flex;
            gap: 15px;
            align-items: center;
        }

        .navbar-buttons a,
        .navbar-buttons button {
            color: white;
            background: #2e7d32;
            padding: 10px 20px;
            border-radius: 5px;
            text-decoration: none;
            font-size: 13px;
            border: none;
            cursor: pointer;
            transition: all 0.3s;
        }

        .navbar-buttons a:hover,
        .navbar-buttons button:hover {
            background: #1b5e20;
            transform: translateY(-2px);
            box-shadow: 0 4px 8px rgba(46, 125, 50, 0.3);
        }

        h2 {
            color: #2e7d32;
            margin-bottom: 20px;
            font-size: 22px;
        }

        .row {
            display: flex;
            gap: 20px;
            flex-wrap: wrap;
            margin-bottom: 20px;
        }

        .col-md-3 {
            flex: 1;
            min-width: 200px;
        }

        .col-md-6 {
            flex: 1;
            min-width: 280px;
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
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
        }

        .stat-card h3 {
            color: #2e7d32;
            font-size: 2rem;
            font-weight: bold;
            margin: 10px 0;
        }

        .stat-card p {
            color: #666;
            margin: 0;
            font-size: 13px;
        }

        .card {
            background: white;
            border-radius: 8px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
            overflow: hidden;
            margin-bottom: 20px;
        }

        .card-header {
            background: #66bb6a;
            color: white;
            padding: 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .card-header h5 {
            margin: 0;
            font-size: 16px;
        }

        .card-body {
            padding: 20px;
        }

        .card-body.p-0 {
            padding: 0;
        }

        .table {
            width: 100%;
            border-collapse: collapse;
        }

        .table thead th {
            background: #f4f9f4;
            border-bottom: 2px solid #e0e0e0;
            padding: 12px;
            text-align: left;
            font-weight: 600;
            color: #333;
            text-transform: uppercase;
            font-size: 0.875rem;
        }

        .table tbody td {
            border-bottom: 1px solid #e0e0e0;
            padding: 12px;
        }

        .table tbody tr:hover {
            background: #f9f9f9;
        }

        .btn {
            padding: 8px 12px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 13px;
            transition: all 0.3s;
            margin-right: 5px;
            display: inline-block;
            text-decoration: none;
        }

        .btn-primary {
            background: #2e7d32;
            color: white;
        }

        .btn-primary:hover {
            background: #1b5e20;
        }

        .btn-sm {
            padding: 5px 10px;
            font-size: 12px;
        }

        .btn-info {
            background: #66bb6a;
            color: white;
        }

        .btn-info:hover {
            background: #2e7d32;
        }

        .btn-danger {
            background: #d32f2f;
            color: white;
        }

        .btn-danger:hover {
            background: #b71c1c;
        }

        .btn-success {
            background: #388e3c;
            color: white;
        }

        .btn-success:hover {
            background: #2e7d32;
        }

        .btn-warning {
            background: #f57c00;
            color: white;
        }

        .btn-warning:hover {
            background: #e65100;
        }

        .badge {
            display: inline-block;
            padding: 5px 10px;
            border-radius: 12px;
            font-size: 11px;
            font-weight: 600;
            margin-right: 5px;
        }

        .badge.bg-success {
            background: #388e3c;
            color: white;
        }

        .badge.bg-warning {
            background: #f57c00;
            color: white;
        }

        .badge.bg-danger {
            background: #d32f2f;
            color: white;
        }

        .badge.bg-info {
            background: #0288d1;
            color: white;
        }

        .form-group {
            margin-bottom: 15px;
        }

        .form-group label {
            display: block;
            margin-bottom: 5px;
            color: #333;
            font-weight: 500;
            font-size: 13px;
        }

        .form-control,
        .form-select {
            width: 100%;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 5px;
            font-size: 13px;
        }

        .form-control:focus,
        .form-select:focus {
            border-color: #2e7d32;
            outline: none;
            box-shadow: 0 0 0 3px rgba(46, 125, 50, 0.1);
        }

        .modal {
            display: none;
            position: fixed;
            z-index: 1000;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0,0,0,0.4);
        }

        .modal.show {
            display: block;
        }

        .modal-content {
            background-color: white;
            margin: 5% auto;
            padding: 0;
            width: 90%;
            max-width: 600px;
            border-radius: 8px;
            box-shadow: 0 4px 16px rgba(0,0,0,0.2);
        }

        .modal-header {
            background: #2e7d32;
            color: white;
            padding: 20px;
            border-radius: 8px 8px 0 0;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .modal-body {
            padding: 20px;
        }

        .modal-footer {
            padding: 20px;
            border-top: 1px solid #eee;
            text-align: right;
        }

        .btn-close {
            background: none;
            border: none;
            font-size: 24px;
            color: white;
            cursor: pointer;
        }

        .page-section {
            display: none;
        }

        .page-section.active {
            display: block;
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

        .mb-3 {
            margin-bottom: 1rem;
        }

        .mb-4 {
            margin-bottom: 1.5rem;
        }

        .mt-4 {
            margin-top: 1.5rem;
        }

        .text-end {
            text-align: right;
        }

        .search-box {
            margin-bottom: 15px;
            display: flex;
            gap: 10px;
        }

        .search-box input {
            flex: 1;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 5px;
            font-size: 13px;
        }

        .search-box input:focus {
            border-color: #2e7d32;
            outline: none;
            box-shadow: 0 0 0 3px rgba(46, 125, 50, 0.1);
        }

        .btn-export {
            background: #b71c1c;
            color: white;
        }

        .btn-export:hover {
            background: #8e0000;
        }

        .alert {
            padding: 15px;
            margin-bottom: 20px;
            border-radius: 5px;
            border-left: 4px solid;
        }

        .alert-info {
            background: #e3f2fd;
            color: #1565c0;
            border-left-color: #1976d2;
        }

        .alert-success {
            background: #e8f5e9;
            color: #2e7d32;
            border-left-color: #4caf50;
        }

        .alert-danger {
            background: #ffebee;
            color: #c62828;
            border-left-color: #f44336;
        }

        .alert-warning {
            background: #fff3e0;
            color: #e65100;
            border-left-color: #ff9800;
        }

        /* Notification System */
        #notification {
            position: fixed;
            top: 20px;
            right: 20px;
            max-width: 400px;
            padding: 15px 20px;
            border-radius: 8px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.2);
            display: none;
            z-index: 10000;
            animation: slideIn 0.3s ease;
            font-size: 14px;
        }

        #notification.show {
            display: block;
        }

        #notification.success {
            background: #e8f5e9;
            color: #2e7d32;
            border-left: 4px solid #4caf50;
        }

        #notification.error {
            background: #ffebee;
            color: #c62828;
            border-left: 4px solid #f44336;
        }

        #notification.warning {
            background: #fff3e0;
            color: #e65100;
            border-left: 4px solid #ff9800;
        }

        #notification.info {
            background: #e3f2fd;
            color: #1565c0;
            border-left: 4px solid #1976d2;
        }

        #notification .close-btn {
            float: right;
            background: none;
            border: none;
            font-size: 18px;
            cursor: pointer;
            margin-top: -2px;
            opacity: 0.7;
            transition: opacity 0.3s;
        }

        #notification .close-btn:hover {
            opacity: 1;
        }

        @keyframes slideIn {
            from {
                transform: translateX(430px);
                opacity: 0;
            }
            to {
                transform: translateX(0);
                opacity: 1;
            }
        }

        @keyframes slideOut {
            from {
                transform: translateX(0);
                opacity: 1;
            }
            to {
                transform: translateX(430px);
                opacity: 0;
            }
        }

        @media (max-width: 768px) {
            .sidebar {
                width: 100%;
                height: auto;
                position: relative;
                min-height: auto;
            }

            .main {
                margin-left: 0;
            }

            .col-md-3,
            .col-md-6 {
                flex: 1;
                min-width: 100%;
            }

            .navbar {
                flex-direction: column;
                gap: 10px;
                text-align: center;
            }

            #notification {
                max-width: calc(100% - 40px);
                right: 20px;
                left: 20px;
            }
        }

        /* QR Code Modal - Premium Design */
        #qr-modal .modal-content {
            border-radius: 20px;
            overflow: hidden;
            box-shadow: 0 15px 35px rgba(0,0,0,0.2);
            border: none;
        }
        #qr-modal .modal-header {
            background: linear-gradient(135deg, #1b5e20, #2e7d32);
            color: white;
            padding: 15px 20px;
        }
        #qr-modal .modal-body {
            background: linear-gradient(135deg, #f8f9fa 0%, #e8f5e9 100%);
            padding: 30px;
        }
        .qr-display-area {
            background: white;
            padding: 25px;
            border-radius: 20px;
            display: flex;
            flex-direction: column;
            align-items: center;
            box-shadow: 0 10px 25px rgba(27, 94, 32, 0.1);
            margin-bottom: 25px;
            transition: transform 0.3s ease;
        }
        .qr-display-area:hover {
            transform: translateY(-5px);
        }
        #qrcode-container {
            padding: 10px;
            background: white;
            border-radius: 10px;
        }
        #qrcode-container img, #qrcode-container canvas {
            max-width: 100%;
            height: auto;
            display: block;
        }
        .qr-info-card {
            background: white;
            padding: 20px;
            border-radius: 15px;
            width: 100%;
            text-align: left;
            border-left: 5px solid #1b5e20;
            box-shadow: 0 4px 12px rgba(0,0,0,0.03);
        }
        .qr-info-item {
            margin-bottom: 8px;
            font-size: 15px;
            display: flex;
            justify-content: space-between;
            border-bottom: 1px dashed #eee;
            padding-bottom: 5px;
        }
        .qr-info-item:last-child {
            border-bottom: none;
        }
        .qr-info-label {
            font-weight: 600;
            color: #666;
        }
        .qr-info-value {
            font-weight: 700;
            color: #1b5e20;
        }
        .btn-qr-action {
            background: #1b5e20;
            color: white;
            border: none;
            padding: 5px 12px;
            border-radius: 6px;
            font-weight: 600;
            transition: all 0.3s;
        }
        .btn-qr-action:hover {
            background: #2e7d32;
            transform: translateY(-2px);
            box-shadow: 0 4px 8px rgba(0,0,0,0.15);
        }


    </style>
</head>
<body>
    <!-- Sidebar -->
    <div class="sidebar">
        <h2>🌱 ECOSAVE</h2>
        <a href="#" onclick="showSection('dashboard')" class="active">📊 Dashboard</a>
        <a href="#" onclick="showSection('produits')">📦 Produits</a>
        <a href="#" onclick="showSection('categories')">🏷️ Catégories</a>
        <a href="#" onclick="showSection('alertes')">⚠️ Alertes Stock</a>
        <a href="#" onclick="showSection('stats')">📈 Statistiques</a>
        <hr style="border: none; border-top: 1px solid rgba(255,255,255,0.2); margin: 20px 0;">
        <a href="./client-dashboard.php" target="_blank">👁️ Vue Client</a>
        <a href="../../index.php">🏠 Accueil</a>
    </div>

    <!-- Main Content -->
    <div class="main">
        <!-- Navbar -->
        <div class="navbar">
            <span class="navbar-brand">🌱 ECOSAVE - Gestion du Stock</span>
            <div class="navbar-buttons">
                <span style="color: #333; font-size: 12px;">Admin • <span id="current-time"></span></span>
                <a href="./client-dashboard.php" target="_blank">👁️ Aperçu Client</a>
            </div>
        </div>

        <!-- DASHBOARD SECTION -->
        <div id="dashboard" class="page-section active">
            <h2>📊 Dashboard</h2>
            
            <div class="row">
                <div class="col-md-3">
                    <div class="stat-card">
                        <h3 id="total-produits">0</h3>
                        <p>Produits</p>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="stat-card">
                        <h3 id="total-categories">0</h3>
                        <p>Catégories</p>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="stat-card">
                        <h3 id="stock-bas">0</h3>
                        <p>Stock Bas</p>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="stat-card">
                        <h3 id="expiries">0</h3>
                        <p>À Expirer</p>
                    </div>
                </div>
            </div>

            <div class="card mt-4">
                <div class="card-header">
                    <h5>📋 Produits récents</h5>
                </div>
                <div class="card-body">
                    <div id="latest-produits" class="loading">
                        <div class="spinner"></div>
                    </div>
                </div>
            </div>
        </div>

        <!-- PRODUITS SECTION -->
        <div id="produits" class="page-section">
            <h2>📦 Gestion des Produits</h2>
            
            <div class="search-box">
                <input type="text" id="produit-search" class="form-control" placeholder="🔍 Rechercher un produit..." oninput="loadProduits()">
                <select id="produit-sort" class="form-select" onchange="onProduitSortChange()" style="max-width: 220px;">
                    <option value="nom_asc">Tri par Nom A → Z</option>
                    <option value="nom_desc">Tri par Nom Z → A</option>
                    <option value="categorie_asc">Tri par Catégorie A → Z</option>
                    <option value="categorie_desc">Tri par Catégorie Z → A</option>
                </select>
                <button class="btn btn-primary" onclick="openProduitModal()">➕ Ajouter produit</button>
                <button class="btn btn-export" onclick="exportProduitsPDF()">📄 Exporter PDF</button>
            </div>

            <div class="card">
                <div class="card-body p-0">
                    <div id="produits-list" class="loading">
                        <div class="spinner"></div>
                    </div>
                </div>
            </div>
        </div>

        <!-- CATEGORIES SECTION -->
        <div id="categories" class="page-section">
            <h2>🏷️ Gestion des Catégories</h2>

            <div class="search-box">
                <input type="text" id="categorie-search" class="form-control" placeholder="🔍 Rechercher une catégorie..." oninput="loadCategories()">
                <select id="categorie-sort" class="form-select" onchange="onCategorieSortChange()" style="max-width: 220px;">
                    <option value="nom_asc">Tri par Nom A → Z</option>
                    <option value="nom_desc">Tri par Nom Z → A</option>
                    <option value="lieu_asc">Tri par Lieu de stockage A → Z</option>
                    <option value="lieu_desc">Tri par Lieu de stockage Z → A</option>
                </select>
                <button class="btn btn-primary" onclick="openCategorieModal()">➕ Ajouter catégorie</button>
                <button class="btn btn-export" onclick="exportCategoriesPDF()">📄 Exporter PDF</button>
            </div>

            <div class="card">
                <div class="card-body p-0">
                    <div id="categories-list" class="loading">
                        <div class="spinner"></div>
                    </div>
                </div>
            </div>
        </div>

        <!-- ALERTES SECTION -->
        <div id="alertes" class="page-section">
            <h2>⚠️ Alertes Stock</h2>
            
<div id="low-stock-notification" style="margin-bottom: 20px; display: none;">
                <div class="alert alert-warning">
                    <strong>⚠️ <span id="low-stock-count">0</span> produit(s) en stock bas </strong>
                    <span id="critical-rupture" style="float: right; display: none;">🚨 <span id="rupture-count">0</span> rupture(s)</span>
                </div>
            </div>
            <div class="card">
                <div class="card-header">
                    <h5>Stock Bas <span id="stock-bas-badge" class="badge bg-warning" style="display: none;"></span></h5>
                </div>
                <div class="card-body">
                    <div id="bas-stock-list" class="loading">
                        <div class="spinner"></div>
                    </div>
                </div>
            </div>

            <div class="card">
                <div class="card-header">
                    <h5>Produits à Expirer</h5>
                </div>
                <div class="card-body">
                    <div id="expiring-list" class="loading">
                        <div class="spinner"></div>
                    </div>
                </div>
            </div>
        </div>

        <!-- STATS SECTION -->
        <div id="stats" class="page-section">
            <h2>📈 Statistiques</h2>
            
            <div class="alert alert-info">
                <strong>ℹ️ Informations:</strong> Section de statistiques avancées sur le stock.
            </div>

            <div class="row">
                <div class="col-md-6">
                    <div class="card">
                        <div class="card-header">
                            <h5>Top Catégories (réel)</h5>
                        </div>
                        <div class="card-body" id="stats-top-categories">
                            <div class="spinner" style="margin: 20px auto;"></div>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="card">
                        <div class="card-header">
                            <h5>État du Stock</h5>
                        </div>
                        <div class="card-body" id="stats-expiration">
                            <canvas id="stock-status-chart" style="max-height: 260px;"></canvas>
                            <div id="stock-status-legend" style="margin-top: 12px;"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Notification Container -->
    <div id="notification">
        <button class="close-btn" onclick="closeNotification()">×</button>
        <span id="notification-message"></span>
    </div>

    <!-- Modals -->
    <div id="produit-modal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h5 id="produit-modal-title">Ajouter/Éditer Produit</h5>
                <button class="btn-close" onclick="closeProduitModal()">&times;</button>
            </div>
            <div class="modal-body">
                <form id="produit-form">
                    <div class="form-group">
                        <label>Nom du Produit *</label>
                        <input type="text" id="produit-nom" class="form-control" required minlength="3" maxlength="100" pattern="^(?!\d+$).*" title="Minimum 3 caractères, ne peut pas être seulement des chiffres">
                    </div>
                    <div class="form-group">
                        <label>Catégorie *</label>
                        <select id="produit-categorie" class="form-select" required>
                            <option value="">Sélectionner une catégorie...</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Date d'Expiration</label>
                        <input type="date" id="produit-expiration" class="form-control">
                    </div>
                    <div class="form-group">
                        <label>Poids (kg)</label>
                        <input type="number" id="produit-poids" class="form-control" step="0.001">
                    </div>
                    <div class="form-group">
                        <label>Quantité Disponible *</label>
                        <input type="number" id="produit-quantite" class="form-control" required>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button class="btn btn-secondary" onclick="closeProduitModal()">Annuler</button>
                <button class="btn btn-primary" onclick="saveProduit()">Enregistrer</button>
            </div>
        </div>
    </div>

    <div id="categorie-modal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h5 id="categorie-modal-title">Ajouter/Éditer Catégorie</h5>
                <button class="btn-close" onclick="closeCategorieModal()">&times;</button>
            </div>
            <div class="modal-body">
                <form id="categorie-form">
                    <div class="form-group">
                        <label>Nom de la Catégorie *</label>
                        <input type="text" id="categorie-nom" class="form-control" required minlength="3" maxlength="100" pattern="^(?!\d+$).*" title="Minimum 3 caractères, ne peut pas être seulement des chiffres">
                    </div>
                    <div class="form-group">
                        <label>Description</label>
                        <div style="display: flex; gap: 10px; align-items: flex-start;">
                            <textarea id="categorie-description" class="form-control" rows="3" style="flex: 1;"></textarea>
                            <button type="button" id="btn-generate-desc" class="btn btn-info" onclick="generateDescriptionIA()" style="margin-top: 0;" title="Générer une description avec l'IA">✨ IA</button>
                        </div>
                    </div>
                    <div class="form-group">
                        <label>Lieu de Stockage</label>
                        <input type="text" id="categorie-lieu" class="form-control">
                    </div>
                    <div class="form-group">
                        <label>Température Conseillée</label>
                        <input type="text" id="categorie-temp" class="form-control" placeholder="Ex: 15-25°C">
                    </div>
                    <div class="form-group">
                        <label>Délai d'Alerte (jours)</label>
                        <input type="number" id="categorie-delai" class="form-control" value="30">
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button class="btn btn-secondary" onclick="closeCategorieModal()">Annuler</button>
                <button class="btn btn-primary" onclick="saveCategorie()">Enregistrer</button>
            </div>
        </div>
    </div>

    <!-- Modal QR Code -->
    <div id="qr-modal" class="modal">
        <div class="modal-content" style="max-width: 400px;">
            <div class="modal-header">
                <h5 class="modal-title">📦 Code QR du Produit</h5>
                <button class="btn-close" onclick="closeQRModal()" style="color: white; font-size: 24px; background: none; border: none; cursor: pointer;">&times;</button>
            </div>
            <div class="modal-body text-center">
                <div class="qr-display-area">
                    <div id="qrcode-container"></div>
                    <h4 id="qr-product-name" style="margin-top: 15px; color: #1b5e20; font-weight: 700;"></h4>
                </div>
                <div class="qr-info-card">
                    <div class="qr-info-item"><span class="qr-info-label">Produit :</span> <span id="qr-product-name-val" class="qr-info-value"></span></div>
                    <div class="qr-info-item"><span class="qr-info-label">Stock :</span> <span id="qr-stock-val" class="qr-info-value"></span></div>
                    <div class="qr-info-item"><span class="qr-info-label">ID Interne :</span> <span id="qr-id-val" class="qr-info-value"></span></div>
                    <p style="margin-top: 15px; font-size: 11px; color: #888; text-align: center; font-style: italic;">Scan sécurisé • Système ECOSAVE</p>
                </div>
            </div>
            <div class="modal-footer" style="background: #eee; gap: 10px;">
                <button class="btn btn-secondary" onclick="closeQRModal()">Fermer</button>
                <button class="btn btn-info" onclick="downloadQRCode()" style="background: #0288d1; border: none;">💾 Télécharger</button>
                <button class="btn btn-primary" onclick="window.print()" style="background: #1b5e20; border: none;">🖨️ Imprimer</button>
            </div>
        </div>
    </div>


    <!-- QR Code Library -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/qrcodejs/1.0.0/qrcode.min.js"></script>


    <script>

        function updateTime() {
            const now = new Date();
            const hours = String(now.getHours()).padStart(2, '0');
            const minutes = String(now.getMinutes()).padStart(2, '0');
            document.getElementById('current-time').textContent = `${hours}:${minutes}`;
        }
        
        updateTime();
        setInterval(updateTime, 60000);

        // Page Section Management
        // Modal de confirmation personnalisée
        let confirmCallback = null;
        let currentProduits = [];
        let currentCategories = [];
        let currentCategoriesMap = {};
        let currentProduitSort = 'nom_asc';
        let currentCategorieSort = 'nom_asc';
        let stockStatusChart = null;

        function showConfirmation(message) {
            const modal = document.createElement('div');
            modal.style.cssText = `
                position: fixed;
                top: 0;
                left: 0;
                width: 100%;
                height: 100%;
                background: rgba(0,0,0,0.5);
                display: flex;
                align-items: center;
                justify-content: center;
                z-index: 10001;
            `;

            const content = document.createElement('div');
            content.style.cssText = `
                background: white;
                border-radius: 8px;
                padding: 30px;
                max-width: 400px;
                box-shadow: 0 4px 12px rgba(0,0,0,0.3);
                text-align: center;
            `;

            const title = document.createElement('h3');
            title.textContent = '⚠️ Confirmation';
            title.style.cssText = 'margin-bottom: 15px; color: #333;';

            const msg = document.createElement('p');
            msg.textContent = message;
            msg.style.cssText = 'margin-bottom: 20px; color: #666; font-size: 14px;';

            const buttonsDiv = document.createElement('div');
            buttonsDiv.style.cssText = 'display: flex; gap: 10px; justify-content: center;';

            const cancelBtn = document.createElement('button');
            cancelBtn.textContent = 'Annuler';
            cancelBtn.style.cssText = `
                padding: 10px 20px;
                border: 1px solid #ddd;
                border-radius: 5px;
                background: #f5f5f5;
                cursor: pointer;
                font-size: 14px;
                transition: all 0.3s;
            `;
            cancelBtn.onmouseover = () => cancelBtn.style.background = '#e0e0e0';
            cancelBtn.onmouseout = () => cancelBtn.style.background = '#f5f5f5';
            cancelBtn.onclick = () => {
                document.body.removeChild(modal);
                if (confirmCallback) confirmCallback(false);
            };

            const confirmBtn = document.createElement('button');
            confirmBtn.textContent = 'Supprimer';
            confirmBtn.style.cssText = `
                padding: 10px 20px;
                border: none;
                border-radius: 5px;
                background: #c62828;
                color: white;
                cursor: pointer;
                font-size: 14px;
                transition: all 0.3s;
            `;
            confirmBtn.onmouseover = () => confirmBtn.style.background = '#b71c1c';
            confirmBtn.onmouseout = () => confirmBtn.style.background = '#c62828';
            confirmBtn.onclick = () => {
                document.body.removeChild(modal);
                if (confirmCallback) confirmCallback(true);
            };

            buttonsDiv.appendChild(cancelBtn);
            buttonsDiv.appendChild(confirmBtn);

            content.appendChild(title);
            content.appendChild(msg);
            content.appendChild(buttonsDiv);
            modal.appendChild(content);
            document.body.appendChild(modal);

            // Focus sur le bouton Annuler par défaut
            cancelBtn.focus();
        }

        function showSection(sectionId) {
            document.querySelectorAll('.page-section').forEach(section => {
                section.classList.remove('active');
            });
            document.getElementById(sectionId).classList.add('active');

            document.querySelectorAll('.sidebar a').forEach(link => {
                link.classList.remove('active');
            });
            event.target.classList.add('active');

            // Load data for specific sections
            if (sectionId === 'dashboard') {
                loadDashboardStats();
            } else if (sectionId === 'produits') {
                loadProduits();
            } else if (sectionId === 'categories') {
                loadCategories();
            } else if (sectionId === 'alertes') {
                loadAlertes();
            } else if (sectionId === 'stats') {
                loadStats();
            }
        }

        // Modal Functions
        function openProduitModal() {
            document.getElementById('produit-modal').classList.add('show');
            document.getElementById('produit-modal-title').textContent = 'Ajouter Produit';
            document.getElementById('produit-form').dataset.editId = '';
            loadCategoriesForSelect();
        }

        function closeProduitModal() {
            document.getElementById('produit-modal').classList.remove('show');
            document.getElementById('produit-form').reset();
            document.getElementById('produit-form').dataset.editId = '';
        }

        function openCategorieModal() {
            document.getElementById('categorie-modal').classList.add('show');
            document.getElementById('categorie-modal-title').textContent = 'Ajouter Catégorie';
            document.getElementById('categorie-form').dataset.editId = '';
        }

        function closeCategorieModal() {
            document.getElementById('categorie-modal').classList.remove('show');
            document.getElementById('categorie-form').reset();
            document.getElementById('categorie-form').dataset.editId = '';
        }

        // API Functions - Version 2 (Debug amélioré)
        function loadDashboardStats() {
            console.log('=== loadDashboardStats START ===');
            console.log('Page location:', window.location.href);
            
            // Vérifier que les éléments HTML existent
            const totalProdEl = document.getElementById('total-produits');
            const totalCatEl = document.getElementById('total-categories');
            const stockBasEl = document.getElementById('stock-bas');
            const expiriesEl = document.getElementById('expiries');
            const latestEl = document.getElementById('latest-produits');
            
            console.log('HTML Elements found:', {
                totalProd: !!totalProdEl,
                totalCat: !!totalCatEl,
                stockBas: !!stockBasEl,
                expiries: !!expiriesEl,
                latest: !!latestEl
            });
            
            if (!totalProdEl || !totalCatEl) {
                console.error('ERROR: Required HTML elements not found!');
                return;
            }
            
            // Fonction utilitaire pour les fetches
            const apiCall = (action) => {
                const url = '/gestion-stock/index.php?action=' + action;
                console.log('Calling API:', url);
                return fetch(url)
                    .then(r => {
                        console.log(`Response for ${action}:`, r.status, r.statusText);
                        if (!r.ok) throw new Error(`HTTP ${r.status}`);
                        return r.json();
                    })
                    .catch(err => {
                        console.error(`Error fetching ${action}:`, err);
                        throw err;
                    });
            };
            
            // Load stats
            Promise.all([
                apiCall('produit_getAll'),
                apiCall('categorie_getAll')
            ])
            .then(([prodResp, catResp]) => {
                console.log('Got responses:', { prodResp, catResp });
                
                const produits = prodResp.data || [];
                const categories = catResp.data || [];
                
                console.log('Data summary:', {
                    produits: produits.length,
                    categories: categories.length
                });
                
                // Update stats
                totalProdEl.textContent = produits.length;
                totalCatEl.textContent = categories.length;
                
                if (stockBasEl) {
                    const basStock = produits.filter(p => p.quantite_dispo <= 5).length;
                    stockBasEl.textContent = basStock;
                }
                
                if (expiriesEl) {
                    const expiries = produits.filter(p => p.date_expiration && new Date(p.date_expiration) < new Date()).length;
                    expiriesEl.textContent = expiries;
                }
                
                // Build categories map
                const catMap = {};
                categories.forEach(cat => {
                    catMap[cat.id_cat] = cat.nom_cat;
                });
                
                // Load recent products
                if (latestEl) {
                    const recent = produits.slice(0, 5);
                    if (recent.length === 0) {
                        latestEl.innerHTML = '<p style="text-align: center; color: #999;">Aucun produit</p>';
                    } else {
                        let html = '<table class="table"><thead><tr><th>Produit</th><th>Catégorie</th><th>Quantité</th><th>Expiration</th></tr></thead><tbody>';
                        recent.forEach(p => {
                            html += `<tr><td>${p.nom_prod}</td><td>${catMap[p.id_cat] || '-'}</td><td>${p.quantite_dispo}</td><td>${p.date_expiration || 'N/A'}</td></tr>`;
                        });
                        html += '</tbody></table>';
                        latestEl.innerHTML = html;
                    }
                }
                
                console.log('=== loadDashboardStats END (SUCCESS) ===');
            })
            .catch(err => {
                console.error('FATAL ERROR:', err);
                if (totalProdEl) totalProdEl.textContent = '❌ Erreur';
                if (latestEl) latestEl.innerHTML = '<p style="color: red; text-align: center;">Erreur de connexion: ' + err.message + '</p>';
            });
        }

        function onProduitSortChange() {
            const sortSelect = document.getElementById('produit-sort');
            if (sortSelect) {
                currentProduitSort = sortSelect.value;
                loadProduits();
            }
        }

        function onCategorieSortChange() {
            const sortSelect = document.getElementById('categorie-sort');
            if (sortSelect) {
                currentCategorieSort = sortSelect.value;
                loadCategories();
            }
        }

        function sortCategories(categories) {
            const sorted = [...categories];
            switch (currentCategorieSort) {
                case 'nom_asc':
                    return sorted.sort((a, b) => a.nom_cat.localeCompare(b.nom_cat, 'fr', { sensitivity: 'base' }));
                case 'nom_desc':
                    return sorted.sort((a, b) => b.nom_cat.localeCompare(a.nom_cat, 'fr', { sensitivity: 'base' }));
                case 'lieu_asc':
                    return sorted.sort((a, b) => {
                        const aLieu = (a.lieu_stockage || '').localeCompare(b.lieu_stockage || '', 'fr', { sensitivity: 'base' });
                        return aLieu !== 0 ? aLieu : a.nom_cat.localeCompare(b.nom_cat, 'fr', { sensitivity: 'base' });
                    });
                case 'lieu_desc':
                    return sorted.sort((a, b) => {
                        const bLieu = (b.lieu_stockage || '').localeCompare(a.lieu_stockage || '', 'fr', { sensitivity: 'base' });
                        return bLieu !== 0 ? bLieu : a.nom_cat.localeCompare(b.nom_cat, 'fr', { sensitivity: 'base' });
                    });
                default:
                    return sorted;
            }
        }

        function sortProduits(produits, categoriesMap) {
            const sorted = [...produits];
            switch (currentProduitSort) {
                case 'nom_asc':
                    return sorted.sort((a, b) => a.nom_prod.localeCompare(b.nom_prod, 'fr', { sensitivity: 'base' }));
                case 'nom_desc':
                    return sorted.sort((a, b) => b.nom_prod.localeCompare(a.nom_prod, 'fr', { sensitivity: 'base' }));
                case 'categorie_asc':
                    return sorted.sort((a, b) => {
                        const aCat = currentCategoriesMap[a.id_cat] || '';
                        const bCat = currentCategoriesMap[b.id_cat] || '';
                        const categoryCompare = aCat.localeCompare(bCat, 'fr', { sensitivity: 'base' });
                        return categoryCompare !== 0 ? categoryCompare : a.nom_prod.localeCompare(b.nom_prod, 'fr', { sensitivity: 'base' });
                    });
                case 'categorie_desc':
                    return sorted.sort((a, b) => {
                        const aCat = currentCategoriesMap[a.id_cat] || '';
                        const bCat = currentCategoriesMap[b.id_cat] || '';
                        const categoryCompare = bCat.localeCompare(aCat, 'fr', { sensitivity: 'base' });
                        return categoryCompare !== 0 ? categoryCompare : b.nom_prod.localeCompare(a.nom_prod, 'fr', { sensitivity: 'base' });
                    });
                default:
                    return sorted;
            }
        }

        function renderProduitsTable(produits, categoriesMap) {
            if (!produits.length) {
                document.getElementById('produits-list').innerHTML = '<p style="text-align: center; padding: 20px; color: #999;">Aucun produit trouvé</p>';
                return;
            }

            let html = '<table class="table"><thead><tr><th>Nom</th><th>Catégorie</th><th>Quantité</th><th>Poids</th><th>Expiration</th><th>Actions</th></tr></thead><tbody>';
            produits.forEach(p => {
                const statusClass = p.quantite_dispo === 0 ? 'bg-danger' : (p.quantite_dispo <= 5 ? 'bg-warning' : 'bg-success');
                const nomCategorie = categoriesMap[p.id_cat] || '-';
                html += `<tr>
                    <td>${p.nom_prod}</td>
                    <td>${nomCategorie}</td>
                    <td><span class="badge ${statusClass}">${p.quantite_dispo}</span></td>
                    <td>${p.poids_produit || 'N/A'} kg</td>
                    <td>${p.date_expiration || 'N/A'}</td>
                    <td>
                        <button class="btn btn-sm btn-info" onclick="editProduit(${p.id_prod})" title="Modifier">✏️</button>
                        <button class="btn btn-sm btn-qr-action" onclick="showQRCode(${p.id_prod}, '${p.nom_prod.replace(/'/g, "\\'")}', '${p.quantite_dispo}')" title="Générer QR Code">QR</button>
                        <button class="btn btn-sm btn-danger" onclick="deleteProduit(${p.id_prod})" title="Supprimer">🗑️</button>
                    </td>
                </tr>`;
            });
            html += '</tbody></table>';
            document.getElementById('produits-list').innerHTML = html;
        }

        function loadProduits() {
            const searchTerm = document.getElementById('produit-search').value.trim();
            const sortSelect = document.getElementById('produit-sort');
            if (sortSelect) {
                currentProduitSort = sortSelect.value;
            }

            // D'abord charger les catégories pour créer un mapping
            fetch('/gestion-stock/index.php?action=categorie_getAll')
                .then(r => r.json())
                .then(categoriesData => {
                    // Créer un mapping id_cat -> nom_cat
                    const categoriesMap = {};
                    (categoriesData.data || []).forEach(cat => {
                        categoriesMap[cat.id_cat] = cat.nom_cat;
                    });
                    currentCategoriesMap = categoriesMap;

                    // Ensuite charger les produits
                    return fetch('/gestion-stock/index.php?action=produit_getAll&search=' + encodeURIComponent(searchTerm))
                        .then(r => r.json())
                        .then(data => ({
                            produits: data.data || [],
                            categoriesMap: categoriesMap
                        }));
                })
                .then(({ produits, categoriesMap }) => {
                    currentProduits = produits;
                    const sortedProduits = sortProduits(produits, categoriesMap);
                    renderProduitsTable(sortedProduits, categoriesMap);
                });
        }

        function loadCategories() {
            const searchTerm = document.getElementById('categorie-search').value.trim();
            fetch('/gestion-stock/index.php?action=categorie_getAll&search=' + encodeURIComponent(searchTerm))
                .then(r => r.json())
                .then(data => {
                    const categories = data.data || [];
                    currentCategories = categories;

                    if (categories.length === 0) {
                        document.getElementById('categories-list').innerHTML = '<p style="text-align: center; padding: 20px; color: #999;">Aucune catégorie trouvée</p>';
                        return;
                    }
                    
                    const sortedCategories = sortCategories(categories);
                    let html = '<table class="table"><thead><tr><th>Nom</th><th>Description</th><th>Lieu Stockage</th><th>Température</th><th>Actions</th></tr></thead><tbody>';
                    sortedCategories.forEach(c => {
                        html += `<tr>
                            <td>${c.nom_cat}</td>
                            <td>${c.description_cat || '-'}</td>
                            <td>${c.lieu_stockage || '-'}</td>
                            <td>${c.temp_conseille || '-'}</td>
                            <td>
                                <button class="btn btn-sm btn-info" onclick="editCategorie(${c.id_cat})">✏️</button>
                                <button class="btn btn-sm btn-danger" onclick="deleteCategorie(${c.id_cat})">🗑️</button>
                            </td>
                        </tr>`;
                    });
                    html += '</tbody></table>';
                    document.getElementById('categories-list').innerHTML = html;
                });
        }


function loadAlertes() {
            // Load low stock first for notification
            fetch('/gestion-stock/index.php?action=produit_getBasStock')
                .then(r => r.json())
                .then(data => {
                    const basStockProduits = data.data || [];
                    const countBas = basStockProduits.length;
                    const ruptures = basStockProduits.filter(p => p.quantite_dispo === 0).length;
                    
                    // Update notification UI
                    const notification = document.getElementById('low-stock-notification');
                    const countEl = document.getElementById('low-stock-count');
                    const badgeEl = document.getElementById('stock-bas-badge');
                    const ruptureEl = document.getElementById('critical-rupture');
                    const ruptureCountEl = document.getElementById('rupture-count');
                    
                    countEl.textContent = countBas;
                    badgeEl.textContent = countBas;
                    ruptureCountEl.textContent = ruptures;
                    
                    if (countBas > 0) {
                        notification.style.display = 'block';
                        badgeEl.style.display = 'inline';
                        
                        if (ruptures > 0) {
                            ruptureEl.style.display = 'inline';
                            notification.className = 'alert alert-danger'; // Critical red
                        } else {
                            ruptureEl.style.display = 'none';
                            notification.className = 'alert alert-warning'; // Orange warning
                        }
                    } else {
                        notification.style.display = 'none';
                        badgeEl.style.display = 'none';
                    }
                    
                    // Update table
                    if (countBas === 0) {
                        document.getElementById('bas-stock-list').innerHTML = '<p style="text-align: center; padding: 20px; color: #999;">✅ Aucun produit en bas de stock</p>';
                    } else {
                        let html = '<table class="table"><thead><tr><th>Produit</th><th>Quantité</th><th>Catégorie</th><th>Action</th></tr></thead><tbody>';
                        basStockProduits.forEach(p => {
                            const statusClass = p.quantite_dispo === 0 ? 'bg-danger' : 'bg-warning';
                            html += `<tr><td><strong>${p.nom_prod}</strong></td><td><span class="badge ${statusClass}">${p.quantite_dispo}</span></td><td>${p.categorie_nom || '-'}</td><td><button class="btn btn-sm btn-primary" onclick="augmenterStock(${p.id_prod})" title="Augmenter stock">➕</button></td></tr>`;
                        });
                        html += '</tbody></table>';
                        document.getElementById('bas-stock-list').innerHTML = html;
                    }
                });
            
            // Expiring products (unchanged)
            fetch('/gestion-stock/index.php?action=produit_getAll')
                .then(r => r.json())
                .then(data => {
                    const today = new Date();
                    const expiring = (data.data || []).filter(p => p.date_expiration && new Date(p.date_expiration) <= today);
                    
                    if (expiring.length === 0) {
                        document.getElementById('expiring-list').innerHTML = '<p style="text-align: center; padding: 20px; color: #999;">Aucun produit à expirer</p>';
                        return;
                    }
                    
                    let html = '<table class="table"><thead><tr><th>Produit</th><th>Expiration</th><th>Jours</th></tr></thead><tbody>';
                    expiring.forEach(p => {
                        const expDate = new Date(p.date_expiration);
                        const days = Math.ceil((expDate - today) / (1000 * 60 * 60 * 24));
                        html += `<tr><td>${p.nom_prod}</td><td>${p.date_expiration}</td><td><span class="badge bg-danger">${days} jours</span></td></tr>`;
                    });
                    html += '</tbody></table>';
                    document.getElementById('expiring-list').innerHTML = html;
                });
        }

        // Auto-refresh low stock notification every 30 seconds when on alertes tab
        let alertesInterval = null;
        function startAlertesAutoRefresh() {
            if (alertesInterval) clearInterval(alertesInterval);
            alertesInterval = setInterval(() => {
                if (document.getElementById('alertes').classList.contains('active')) {
                    loadAlertes();
                }
            }, 30000);
        }

        // Start auto-refresh when switching to alertes
        document.addEventListener('click', function(e) {
            if (e.target.textContent.includes('Alertes Stock') || e.target.onclick?.toString().includes('alertes')) {
                setTimeout(startAlertesAutoRefresh, 1000);
            }
        });

        function loadStats() {
            Promise.all([
                fetch('/gestion-stock/index.php?action=categorie_getAll').then(r => r.json()),
                fetch('/gestion-stock/index.php?action=produit_getAll').then(r => r.json())
            ]).then(([categoriesData, produitsData]) => {
                const categories = categoriesData.data || [];
                const produits = produitsData.data || [];

                // 1) Top catégories basé sur les produits réels (et pas produits_count API)
                const categoryNameById = {};
                categories.forEach(c => {
                    categoryNameById[c.id_cat] = c.nom_cat;
                });

                const countByCategoryId = {};
                produits.forEach(p => {
                    const key = p.id_cat;
                    countByCategoryId[key] = (countByCategoryId[key] || 0) + 1;
                });

                const topCategories = categories
                    .map(c => ({
                        nom: c.nom_cat,
                        total: c.produits_count ?? (countByCategoryId[c.id_cat] || 0)
                    }))
                    .filter(c => c.total > 0)
                    .sort((a, b) => b.total - a.total)
                    .slice(0, 6);

                let topCatHtml = '<ul style="list-style: none; padding: 0;">';
                if (topCategories.length === 0) {
                    topCatHtml += '<li style="padding: 8px 0; color: #999;">Aucune donnée disponible</li>';
                } else {
                    topCategories.forEach(c => {
                        topCatHtml += `<li style="padding: 8px 0; border-bottom: 1px solid #eee;"><strong>${c.nom}</strong>: ${c.total} produits</li>`;
                    });
                }
                topCatHtml += '</ul>';
                document.getElementById('stats-top-categories').innerHTML = topCatHtml;

                // 2) Statistique circulaire: état du stock
                const stockOk = produits.filter(p => p.quantite_dispo > 5).length;
                const stockBas = produits.filter(p => p.quantite_dispo > 0 && p.quantite_dispo <= 5).length;
                const stockRupture = produits.filter(p => p.quantite_dispo === 0).length;

                if (window.Chart) {
                    const ctx = document.getElementById('stock-status-chart');
                    if (ctx) {
                        if (stockStatusChart) {
                            stockStatusChart.destroy();
                        }

                        stockStatusChart = new Chart(ctx, {
                            type: 'doughnut',
                            data: {
                                labels: ['OK', 'Bas', 'Rupture'],
                                datasets: [{
                                    data: [stockOk, stockBas, stockRupture],
                                    backgroundColor: ['#388e3c', '#f57c00', '#d32f2f'],
                                    borderColor: ['#ffffff', '#ffffff', '#ffffff'],
                                    borderWidth: 2
                                }]
                            },
                            options: {
                                responsive: true,
                                maintainAspectRatio: false,
                                plugins: {
                                    legend: {
                                        display: false
                                    }
                                }
                            }
                        });
                    }
                }

                document.getElementById('stock-status-legend').innerHTML = `
                    <p><span class="badge bg-success">OK</span>: ${stockOk} produits</p>
                    <p><span class="badge bg-warning">Bas</span>: ${stockBas} produits</p>
                    <p><span class="badge bg-danger">Rupture</span>: ${stockRupture} produits</p>
                `;
            }).catch(() => {
                document.getElementById('stats-top-categories').innerHTML = '<p style="color:#c62828;">Erreur de chargement des statistiques</p>';
                document.getElementById('stats-expiration').innerHTML = '<p style="color:#c62828;">Erreur de chargement des statistiques</p>';
            });
        }

        function loadCategoriesForSelect() {
            fetch('/gestion-stock/index.php?action=categorie_getAll')
                .then(r => r.json())
                .then(data => {
                    const categories = data.data || [];
                    let html = '<option value="">Sélectionner une catégorie...</option>';
                    categories.forEach(c => {
                        html += `<option value="${c.id_cat}">${c.nom_cat}</option>`;
                    });
                    document.getElementById('produit-categorie').innerHTML = html;
                });
        }

        function saveProduit() {
            const nom = document.getElementById('produit-nom').value.trim();
            const id_cat = document.getElementById('produit-categorie').value;
            const date_expiration = document.getElementById('produit-expiration').value || null;
            const poids_produit = document.getElementById('produit-poids').value || 0;
            const quantite_dispo = document.getElementById('produit-quantite').value;
            const editId = document.getElementById('produit-form').dataset.editId;
            
            // Utiliser la validation du fichier stock-validation.js
            if (!validerNomProduit(nom)) {
                showNotification('⚠️ ' + getValidationErrorMessage('nom_invalide'), 'warning');
                return;
            }
            
            if (!id_cat) {
                showNotification('⚠️ ' + getValidationErrorMessage('categorie_requise'), 'warning');
                return;
            }
            
            if (!quantite_dispo || !validerQuantite(parseInt(quantite_dispo))) {
                showNotification('⚠️ ' + getValidationErrorMessage('quantite_invalide'), 'warning');
                return;
            }
            
            const action = editId ? 'produit_update' : 'produit_create';
            const data = {
                nom_prod: nom,
                id_cat: parseInt(id_cat),
                date_expiration: date_expiration,
                poids_produit: parseFloat(poids_produit),
                quantite_dispo: parseInt(quantite_dispo)
            };
            
            if (editId) {
                data.id_prod = parseInt(editId);
            }
            
            fetch('/gestion-stock/index.php?action=' + action, {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify(data)
            })
            .then(r => r.json())
            .then(data => {
                if (data.success) {
                    showNotification(editId ? '✅ Produit mis à jour avec succès' : '✅ Produit enregistré avec succès', 'success');
                    closeProduitModal();
                    loadProduits();
                    loadDashboardStats();
                } else {
                    showNotification('❌ Erreur: ' + (data.error || 'Impossible d\'enregistrer'), 'error');
                }
            });
        }

        function saveCategorie() {
            const nom = document.getElementById('categorie-nom').value.trim();
            const description = document.getElementById('categorie-description').value;
            const lieu = document.getElementById('categorie-lieu').value;
            const temp = document.getElementById('categorie-temp').value;
            const delai = document.getElementById('categorie-delai').value;
            const editId = document.getElementById('categorie-form').dataset.editId;
            
            // Utiliser la validation du fichier stock-validation.js
            if (!validerNomCategorie(nom)) {
                showNotification('⚠️ ' + getValidationErrorMessage('nom_invalide'), 'warning');
                return;
            }
            
            if (!validerDescription(description)) {
                showNotification('⚠️ ' + getValidationErrorMessage('description_trop_longue'), 'warning');
                return;
            }
            
            const action = editId ? 'categorie_update' : 'categorie_create';
            const data = {
                nom_cat: nom,
                description_cat: description,
                lieu_stockage: lieu,
                temp_conseille: temp,
                delai_alerte_jours: parseInt(delai) || 30
            };
            
            if (editId) {
                data.id_cat = parseInt(editId);
            }
            
            fetch('/gestion-stock/index.php?action=' + action, {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify(data)
            })
            .then(r => r.json())
            .then(data => {
                if (data.success) {
                    showNotification(editId ? '✅ Catégorie mise à jour avec succès' : '✅ Catégorie enregistrée avec succès', 'success');
                    closeCategorieModal();
                    loadCategories();
                    loadDashboardStats();
                } else {
                    showNotification('❌ Erreur: ' + (data.error || 'Impossible d\'enregistrer'), 'error');
                }
            });
        }

        function deleteProduit(id) {
            confirmCallback = (confirmed) => {
                if (!confirmed) return;
                
                fetch('/gestion-stock/index.php?action=produit_delete', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({ id_prod: id })
                })
                .then(r => r.json())
                .then(data => {
                    if (data.success) {
                        showNotification('✅ Produit supprimé avec succès', 'success');
                        loadProduits();
                        loadDashboardStats();
                    } else {
                        showNotification('❌ Erreur: ' + (data.error || 'Impossible de supprimer'), 'error');
                    }
                });
            };
            showConfirmation('Êtes-vous sûr de vouloir supprimer ce produit ?');
        }

        function deleteCategorie(id) {
            confirmCallback = (confirmed) => {
                if (!confirmed) return;
                
                fetch('/gestion-stock/index.php?action=categorie_delete', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({ id_cat: id })
                })
                .then(r => r.json())
                .then(data => {
                    if (data.success) {
                        showNotification('✅ Catégorie supprimée avec succès', 'success');
                        loadCategories();
                        loadDashboardStats();
                    } else {
                        showNotification('❌ Erreur: ' + (data.error || 'Impossible de supprimer'), 'error');
                    }
                });
            };
            showConfirmation('Êtes-vous sûr de vouloir supprimer cette catégorie ?');
        }

        function editProduit(id) {
            fetch('/gestion-stock/index.php?action=produit_getById&id=' + id)
                .then(r => r.json())
                .then(data => {
                    if (!data.success) {
                        showNotification('❌ Produit non trouvé', 'error');
                        return;
                    }
                    const p = data.data;
                    document.getElementById('produit-nom').value = p.nom_prod;
                    document.getElementById('produit-categorie').value = p.id_cat || '';
                    document.getElementById('produit-expiration').value = p.date_expiration || '';
                    document.getElementById('produit-poids').value = p.poids_produit || '';
                    document.getElementById('produit-quantite').value = p.quantite_dispo || '';
                    document.getElementById('produit-form').dataset.editId = id;
                    document.getElementById('produit-modal-title').textContent = 'Éditer Produit';
                    openProduitModal();
                })
                .catch(err => {
                    console.error('Erreur:', err);
                    showNotification('❌ Erreur lors du chargement du produit', 'error');
                });
        }

        function editCategorie(id) {
            fetch('/gestion-stock/index.php?action=categorie_getById&id=' + id)
                .then(r => r.json())
                .then(data => {
                    if (!data.success) {
                        showNotification('❌ Catégorie non trouvée', 'error');
                        return;
                    }
                    const c = data.data;
                    document.getElementById('categorie-nom').value = c.nom_cat;
                    document.getElementById('categorie-description').value = c.description_cat || '';
                    document.getElementById('categorie-lieu').value = c.lieu_stockage || '';
                    document.getElementById('categorie-temp').value = c.temp_conseille || '';
                    document.getElementById('categorie-delai').value = c.delai_alerte_jours || 30;
                    document.getElementById('categorie-form').dataset.editId = id;
                    document.getElementById('categorie-modal-title').textContent = 'Éditer Catégorie';
                    openCategorieModal();
                })
                .catch(err => {
                    console.error('Erreur:', err);
                    showNotification('❌ Erreur lors du chargement de la catégorie', 'error');
                });
        }



        // Fonctions QR Code
        function showQRCode(id, nom, stock) {
            console.log("Generating QR for:", id, nom, stock);
            
            // Helper pour l'encodage UTF-8 (essentiel pour les accents sur iPhone/Mobile)
            const utf8Encode = (str) => {
                return unescape(encodeURIComponent(str));
            };

            const text = utf8Encode(`PRODUIT: ${nom} | ID: ${id} | STOCK: ${stock}`);
            
            const container = document.getElementById('qrcode-container');
            container.innerHTML = "";
            
            try {
                // Afficher la modal d'abord
                const modal = document.getElementById('qr-modal');
                modal.style.display = 'block';
                
                // Mettre à jour les infos textuelles
                document.getElementById('qr-product-name').innerText = nom;
                document.getElementById('qr-product-name-val').innerText = nom;
                document.getElementById('qr-stock-val').innerText = stock;
                document.getElementById('qr-id-val').innerText = id;

                // Petit délai pour s'assurer que le container est prêt et visible
                setTimeout(() => {
                    modal.classList.add('show');
                    
                    try {
                        new QRCode(container, {
                            text: text,
                            width: 256,
                            height: 256,
                            colorDark : "#000000",
                            colorLight : "#ffffff",
                            correctLevel : QRCode.CorrectLevel.M
                        });
                        console.log("QR Code generated successfully");
                    } catch (qrErr) {
                        console.error("QR Library Error:", qrErr);
                        container.innerHTML = `<p style="color:red; font-size:12px;">Erreur bibliothèque QR: ${qrErr.message}</p>`;
                    }
                }, 50);
                
            } catch (err) {
                console.error("General QR Error:", err);
                showNotification('Erreur lors de la génération du QR Code', 'error');
            }
        }

        function closeQRModal() {
            document.getElementById('qr-modal').classList.remove('show');
            setTimeout(() => document.getElementById('qr-modal').style.display = 'none', 300);
        }

        function downloadQRCode() {
            const container = document.getElementById('qrcode-container');
            const img = container.querySelector('img');
            const canvas = container.querySelector('canvas');
            const productName = document.getElementById('qr-product-name').innerText;
            
            let dataUrl = '';
            if (img && img.src) {
                dataUrl = img.src;
            } else if (canvas) {
                dataUrl = canvas.toDataURL("image/png");
            }
            
            if (dataUrl) {
                const link = document.createElement('a');
                link.href = dataUrl;
                link.download = `QR_${productName.replace(/\s+/g, '_')}.png`;
                document.body.appendChild(link);
                link.click();
                document.body.removeChild(link);
            } else {
                showNotification('Erreur: Impossible de récupérer l\'image du QR Code', 'error');
            }
        }

        function augmenterStock(id) {
            const quantite = prompt('Quantité à ajouter:');
            if (quantite && quantite > 0) {
                fetch('/gestion-stock/index.php?action=produit_augmenterStock', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({ id_prod: id, quantite: parseInt(quantite) })
                })
                .then(r => r.json())
                .then(data => {
                    if (data.success) {
                        showNotification('✅ Stock augmenté avec succès', 'success');
                        loadAlertes();
                        loadDashboardStats();
                    } else {
                        showNotification('❌ Erreur lors de la mise à jour du stock', 'error');
                    }
                });
            }
        }

        // Close modals on background click
        window.onclick = function(event) {
            const produitModal = document.getElementById('produit-modal');
            const categorieModal = document.getElementById('categorie-modal');
            const qrModal = document.getElementById('qr-modal');
            
            if (event.target === produitModal) {
                closeProduitModal();
            }
            if (event.target === categorieModal) {
                closeCategorieModal();
            }
            if (event.target === qrModal) {
                closeQRModal();
            }
        }

        function generateDescriptionIA() {
            const nomCat = document.getElementById('categorie-nom').value.trim();
            if (!nomCat) {
                showNotification('⚠️ Veuillez d\'abord saisir un nom de catégorie', 'warning');
                return;
            }

            const btn = document.getElementById('btn-generate-desc');
            const originalText = btn.innerHTML;
            btn.innerHTML = '⏳...';
            btn.disabled = true;

            fetch('/gestion-stock/index.php?action=openai_generate_description', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ category_name: nomCat })
            })
            .then(r => r.json())
            .then(data => {
                if (data.success) {
                    document.getElementById('categorie-description').value = data.description;
                    showNotification('✅ Description générée avec succès', 'success');
                } else {
                    showNotification('❌ Erreur: ' + (data.error || 'Erreur lors de la génération'), 'error');
                }
            })
            .catch(err => {
                console.error(err);
                showNotification('❌ Erreur de connexion', 'error');
            })
            .finally(() => {
                btn.innerHTML = originalText;
                btn.disabled = false;
            });
        }

        // Notification System
        function showNotification(message, type = 'info') {
            const notification = document.getElementById('notification');
            const messageEl = document.getElementById('notification-message');
            
            notification.classList.remove('success', 'error', 'warning', 'info');
            notification.classList.add('show', type);
            messageEl.textContent = message;
            
            setTimeout(() => closeNotification(), 4000);
        }

        function closeNotification() {
            const notification = document.getElementById('notification');
            notification.classList.remove('show');
        }

        function exportProduitsPDF() {
            if (!window.jspdf || !window.jspdf.jsPDF || typeof window.jspdf.jsPDF !== 'function') {
                showNotification('❌ Bibliothèque PDF non disponible', 'error');
                return;
            }

            if (!currentProduits.length) {
                showNotification('⚠️ Aucun produit à exporter', 'warning');
                return;
            }

            const { jsPDF } = window.jspdf;
            const doc = new jsPDF();
            doc.setFontSize(14);
            doc.text('ECOSAVE - Liste des Produits', 14, 16);
            doc.setFontSize(10);
            doc.text(`Date: ${new Date().toLocaleDateString('fr-FR')}`, 14, 22);

            const body = currentProduits.map(p => [
                p.nom_prod || '-',
                p.categorie_nom || '-',
                String(p.quantite_dispo ?? '-'),
                `${p.poids_produit || '-'} kg`,
                p.date_expiration || '-'
            ]);

            doc.autoTable({
                head: [['Nom', 'Categorie', 'Quantite', 'Poids', 'Expiration']],
                body,
                startY: 28
            });

            doc.save('produits-ecosave.pdf');
            showNotification('✅ Export PDF des produits terminé', 'success');
        }

        function exportCategoriesPDF() {
            if (!window.jspdf || !window.jspdf.jsPDF || typeof window.jspdf.jsPDF !== 'function') {
                showNotification('❌ Bibliothèque PDF non disponible', 'error');
                return;
            }

            if (!currentCategories.length) {
                showNotification('⚠️ Aucune catégorie à exporter', 'warning');
                return;
            }

            const { jsPDF } = window.jspdf;
            const doc = new jsPDF();
            doc.setFontSize(14);
            doc.text('ECOSAVE - Liste des Categories', 14, 16);
            doc.setFontSize(10);
            doc.text(`Date: ${new Date().toLocaleDateString('fr-FR')}`, 14, 22);

            const body = currentCategories.map(c => [
                c.nom_cat || '-',
                c.description_cat || '-',
                c.lieu_stockage || '-',
                c.temp_conseille || '-'
            ]);

            doc.autoTable({
                head: [['Nom', 'Description', 'Lieu stockage', 'Temperature']],
                body,
                startY: 28
            });

            doc.save('categories-ecosave.pdf');
            showNotification('✅ Export PDF des catégories terminé', 'success');
        }

        // Initialize on page load
        document.addEventListener('DOMContentLoaded', function() {
            const produitSearch = document.getElementById('produit-search');
            const categorieSearch = document.getElementById('categorie-search');
            if (produitSearch) produitSearch.addEventListener('input', loadProduits);
            if (categorieSearch) categorieSearch.addEventListener('input', loadCategories);

            loadDashboardStats();
        });
    </script>
    <script src="https://cdn.jsdelivr.net/npm/jspdf@2.5.1/dist/jspdf.umd.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/jspdf-autotable@3.8.2/dist/jspdf.plugin.autotable.min.js"></script>
    <script src="/gestion-stock/assets/js/stock-validation.js"></script>
</body></html>
