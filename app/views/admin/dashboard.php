<?php
/**
 * Vue - Tableau de bord Admin
 * Dashboard avec statistiques
 */
$baseUrl = '/gestion-stock';
$pageTitle = 'Tableau de Bord - Admin';
include __DIR__ . '/../layouts/header.php';
?>

<div class="container-fluid my-5">
    <div class="row mb-4">
        <div class="col-md-8">
            <h2>📊 Tableau de Bord</h2>
        </div>
        <div class="col-md-4 text-end">
            <span class="badge bg-success">En ligne</span>
        </div>
    </div>

    <!-- Statistiques -->
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card text-center">
                <div class="card-body">
                    <h5 class="card-title">📦 Produits</h5>
                    <h2 id="total-produits" class="text-success">0</h2>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card text-center">
                <div class="card-body">
                    <h5 class="card-title">🏷️ Catégories</h5>
                    <h2 id="total-categories" class="text-success">0</h2>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card text-center">
                <div class="card-body">
                    <h5 class="card-title">⚠️ Bas de Stock</h5>
                    <h2 id="bas-stock" class="text-warning">0</h2>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card text-center">
                <div class="card-body">
                    <h5 class="card-title">💰 Valeur Stock</h5>
                    <h2 id="valeur-stock" class="text-primary">0 TND</h2>
                </div>
            </div>
        </div>
    </div>

    <!-- Alertes Bas de Stock -->
    <div class="card mb-4">
        <div class="card-header bg-warning text-dark">
            <h5 class="mb-0">⚠️ Produits en Bas de Stock</h5>
        </div>
        <div class="card-body">
            <table class="table table-sm">
                <thead>
                    <tr>
                        <th>Produit</th>
                        <th>Catégorie</th>
                        <th>Quantité</th>
                        <th>Minimum</th>
                        <th>Prix TND</th>
                    </tr>
                </thead>
                <tbody id="alertes-bas-stock">
                    <tr><td colspan="5" class="text-center text-muted">Chargement...</td></tr>
                </tbody>
            </table>
        </div>
    </div>

    <div class="text-center">
        <a href="admin/produits_gestion.php" class="btn btn-primary me-2">📦 Gérer Produits</a>
        <a href="admin/categories_gestion.php" class="btn btn-info">🏷️ Gérer Catégories</a>
    </div>
</div>

<script>
    // Charger les statistiques au chargement
    document.addEventListener('DOMContentLoaded', function() {
        chargerStats();
    });

    async function chargerStats() {
        try {
            const response = await fetch('../../index.php?action=stats');
            const data = await response.json();
            
            if (data.success) {
                document.getElementById('total-produits').textContent = data.data.total_produits;
                document.getElementById('total-categories').textContent = data.data.total_categories;
                document.getElementById('bas-stock').textContent = data.data.bas_stock;
                document.getElementById('valeur-stock').textContent = data.data.valeur_stock.toFixed(2) + ' TND';
            }

            chargerAlertes();
        } catch (error) {
            console.error('Erreur:', error);
        }
    }

    async function chargerAlertes() {
        try {
            const response = await fetch('../../index.php?action=bas_stock');
            const data = await response.json();
            
            if (data.success && data.data.length > 0) {
                const tbody = document.getElementById('alertes-bas-stock');
                tbody.innerHTML = '';
                
                data.data.forEach(produit => {
                    const tr = document.createElement('tr');
                    tr.innerHTML = `
                        <td>${produit.nom_prod}</td>
                        <td>${produit.categorie_nom}</td>
                        <td class="text-warning fw-bold">${produit.quantite_dispo}</td>
                        <td>5</td>
                        <td>${produit.poids_produit} kg</td>
                    `;
                    tbody.appendChild(tr);
                });
            } else {
                document.getElementById('alertes-bas-stock').innerHTML = '<tr><td colspan="5" class="text-center text-success">Tous les stocks sont corrects</td></tr>';
            }
        } catch (error) {
            console.error('Erreur:', error);
        }
    }
</script>

<?php include __DIR__ . '/../layouts/footer.php'; ?>
