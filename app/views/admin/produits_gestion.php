<?php
/**
 * Vue - Gestion des produits (Admin)
 * Liste avec CRUD
 */
$baseUrl = '/gestion-stock';
$pageTitle = 'Gestion Produits - Admin';
include __DIR__ . '/../layouts/header.php';
?>

<div class="container-fluid my-5">
    <div class="row mb-4">
        <div class="col-md-8">
            <h2>📦 Gestion des Produits</h2>
        </div>
        <div class="col-md-4 text-end">
            <a href="produit_formulaire.php" class="btn btn-primary">➕ Ajouter</a>
            <a href="dashboard.php" class="btn btn-secondary">Retour</a>
        </div>
    </div>

    <!-- Recherche -->
    <div class="card mb-4">
        <div class="card-body">
            <div class="row">
                <div class="col-md-6">
                    <input type="text" id="search-produit" class="form-control" placeholder="Rechercher par nom...">
                </div>
                <div class="col-md-6">
                    <select id="filter-categorie" class="form-control">
                        <option value="">Toutes les catégories</option>
                    </select>
                </div>
            </div>
        </div>
    </div>

    <!-- Liste des produits -->
    <div class="table-responsive">
        <table class="table table-striped table-hover">
            <thead class="table-dark">
                <tr>
                    <th>Nom</th>
                    <th>Catégorie</th>
                    <th>Quantité</th>
                    <th>Prix (TND)</th>
                    <th>Statut</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody id="produits-tbody">
                <tr><td colspan="6" class="text-center text-muted">Chargement...</td></tr>
            </tbody>
        </table>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        chargerCategoriesFiltre();
        chargerProduits();
        
        // Événements de recherche/filtre
        document.getElementById('search-produit').addEventListener('input', chargerProduits);
        document.getElementById('filter-categorie').addEventListener('change', chargerProduits);
    });

    async function chargerCategoriesFiltre() {
        try {
            const response = await fetch('../../../index.php?action=categories');
            const data = await response.json();
            
            if (data.success) {
                const select = document.getElementById('filter-categorie');
                data.data.forEach(cat => {
                    const option = document.createElement('option');
                    option.value = cat.id_cat;
                    option.textContent = cat.nom_cat;
                    select.appendChild(option);
                });
            }
        } catch (error) {
            console.error('Erreur:', error);
        }
    }

    async function chargerProduits() {
        try {
            const search = document.getElementById('search-produit').value;
            const categorie = document.getElementById('filter-categorie').value;
            let url = '../../../index.php?action=produits';
            if (search) url += '&search=' + encodeURIComponent(search);
            if (categorie) url += '&categorie=' + categorie;
            
            const response = await fetch(url);
            const data = await response.json();
            
            const tbody = document.getElementById('produits-tbody');
            tbody.innerHTML = '';
            
            if (data.success && data.data.length > 0) {
                data.data.forEach(produit => {
                    const tr = document.createElement('tr');
                    let statut = '';
                    let badge = '';
                    
                    if (produit.quantite_dispo === 0) {
                        statut = 'Rupture';
                        badge = 'bg-danger';
                    } else if (produit.quantite_dispo <= 5) {
                        statut = 'Bas';
                        badge = 'bg-warning';
                    } else {
                        statut = 'OK';
                        badge = 'bg-success';
                    }
                    
                    tr.innerHTML = `
                        <td><strong>${produit.nom_prod}</strong></td>
                        <td>${produit.categorie_nom}</td>
                        <td>${produit.quantite_dispo}</td>
                        <td>${produit.poids_produit} kg</td>
                        <td><span class="badge ${badge}">${statut}</span></td>
                        <td>
                            <a href="produit_formulaire.php?id=${produit.id_prod}" class="btn btn-sm btn-warning">✏️</a>
                            <button onclick="supprimerProduit(${produit.id_prod})" class="btn btn-sm btn-danger">🗑️</button>
                        </td>
                    `;
                    tbody.appendChild(tr);
                });
            } else {
                tbody.innerHTML = '<tr><td colspan="6" class="text-center text-muted">Aucun produit trouvé</td></tr>';
            }
        } catch (error) {
            console.error('Erreur:', error);
        }
    }

    async function supprimerProduit(id) {
        if (!confirm('Confirmer la suppression ?')) return;
        
        try {
            const response = await fetch('../../../index.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ action: 'supprimer_produit', id: id })
            });
            
            const data = await response.json();
            alert(data.message);
            if (data.success) chargerProduits();
        } catch (error) {
            console.error('Erreur:', error);
            alert('Erreur lors de la suppression');
        }
    }
</script>

<?php include __DIR__ . '/../layouts/footer.php'; ?>
