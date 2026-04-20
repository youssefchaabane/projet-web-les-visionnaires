<?php
/**
 * Vue - Gestion des catégories (Admin)
 * Liste avec CRUD
 */
$baseUrl = '/gestion-stock';
$pageTitle = 'Gestion Catégories - Admin';
include __DIR__ . '/../layouts/header.php';
?>

<div class="container-fluid my-5">
    <div class="row mb-4">
        <div class="col-md-8">
            <h2>🏷️ Gestion des Catégories</h2>
        </div>
        <div class="col-md-4 text-end">
            <a href="categorie_formulaire.php" class="btn btn-primary">➕ Ajouter</a>
            <a href="dashboard.php" class="btn btn-secondary">Retour</a>
        </div>
    </div>

    <!-- Recherche -->
    <div class="card mb-4">
        <div class="card-body">
            <input type="text" id="search-categorie" class="form-control" placeholder="Rechercher par nom...">
        </div>
    </div>

    <!-- Liste des catégories -->
    <div class="table-responsive">
        <table class="table table-striped table-hover">
            <thead class="table-dark">
                <tr>
                    <th>Nom</th>
                    <th>Description</th>
                    <th>Produits</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody id="categories-tbody">
                <tr><td colspan="4" class="text-center text-muted">Chargement...</td></tr>
            </tbody>
        </table>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        chargerCategories();
        document.getElementById('search-categorie').addEventListener('input', chargerCategories);
    });

    async function chargerCategories() {
        try {
            const search = document.getElementById('search-categorie').value;
            let url = '../../../index.php?action=categories';
            if (search) url += '&search=' + encodeURIComponent(search);
            
            const response = await fetch(url);
            const data = await response.json();
            
            const tbody = document.getElementById('categories-tbody');
            tbody.innerHTML = '';
            
            if (data.success && data.data.length > 0) {
                data.data.forEach(categorie => {
                    const tr = document.createElement('tr');
                    tr.innerHTML = `
                        <td><strong>${categorie.nom_cat}</strong></td>
                        <td>${categorie.description_cat || 'N/A'}</td>
                        <td><span class="badge bg-info">${categorie.produits_count || 0}</span></td>
                        <td>
                            <a href="categorie_formulaire.php?id=${categorie.id_cat}" class="btn btn-sm btn-warning">✏️</a>
                            <button onclick="supprimerCategorie(${categorie.id_cat})" class="btn btn-sm btn-danger" 
                                    ${(categorie.produits_count || 0) > 0 ? 'disabled' : ''}>🗑️</button>
                        </td>
                    `;
                    tbody.appendChild(tr);
                });
            } else {
                tbody.innerHTML = '<tr><td colspan="4" class="text-center text-muted">Aucune catégorie trouvée</td></tr>';
            }
        } catch (error) {
            console.error('Erreur:', error);
        }
    }

    async function supprimerCategorie(id) {
        if (!confirm('Confirmer la suppression ?')) return;
        
        try {
            const response = await fetch('../../../index.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ action: 'supprimer_categorie', id: id })
            });
            
            const data = await response.json();
            alert(data.message);
            if (data.success) chargerCategories();
        } catch (error) {
            console.error('Erreur:', error);
            alert('Erreur lors de la suppression');
        }
    }
</script>

<?php include __DIR__ . '/../layouts/footer.php'; ?>
