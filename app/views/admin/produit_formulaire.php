<?php
/**
 * Vue - Formulaire Produit (Admin)
 */
$baseUrl = '/gestion-stock';
$id = $_GET['id'] ?? null;
$pageTitle = ($id ? 'Éditer' : 'Ajouter') . ' Produit - Admin';
include __DIR__ . '/../layouts/header.php';
?>

<div class="container my-5">
    <div class="row mb-4">
        <div class="col-md-8">
            <h2><?php echo $id ? '✏️ Éditer Produit' : '➕ Ajouter Produit'; ?></h2>
        </div>
        <div class="col-md-4 text-end">
            <a href="produits_gestion.php" class="btn btn-secondary">Retour</a>
        </div>
    </div>

    <div class="card">
        <div class="card-body">
            <form id="form-produit" onsubmit="sauvegarderProduit(event)">
                <input type="hidden" id="id-produit">

                <div class="row mb-3">
                    <div class="col-md-6">
                        <label for="nom" class="form-label">Nom du Produit *</label>
                        <input type="text" id="nom" class="form-control" required>
                    </div>
                    <div class="col-md-6">
                        <label for="categorie" class="form-label">Catégorie *</label>
                        <select id="categorie" class="form-control" required>
                            <option value="">-- Sélectionner --</option>
                        </select>
                    </div>
                </div>

                <div class="row mb-3">
                    <div class="col-md-4">
                        <label for="prix" class="form-label">Prix (TND) *</label>
                        <input type="number" id="prix" class="form-control" step="0.001" required>
                    </div>
                    <div class="col-md-4">
                        <label for="quantite" class="form-label">Quantité *</label>
                        <input type="number" id="quantite" class="form-control" required>
                    </div>
                    <div class="col-md-4">
                        <label for="quantite-min" class="form-label">Quantité Min *</label>
                        <input type="number" id="quantite-min" class="form-control" required>
                    </div>
                </div>

                <div class="mb-3">
                    <label for="description" class="form-label">Description</label>
                    <textarea id="description" class="form-control" rows="4"></textarea>
                </div>

                <div class="text-end">
                    <button type="submit" class="btn btn-success">✔️ Enregistrer</button>
                    <a href="produits_gestion.php" class="btn btn-secondary">Annuler</a>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        chargerCategories();
        
        <?php if ($id): ?>
            chargerProduit(<?php echo $id; ?>);
        <?php endif; ?>
    });

    async function chargerCategories() {
        try {
            const response = await fetch('../../../index.php?action=categories');
            const data = await response.json();
            
            if (data.success) {
                const select = document.getElementById('categorie');
                data.data.forEach(cat => {
                    const option = document.createElement('option');
                    option.value = cat.id_categorie;
                    option.textContent = cat.nom;
                    select.appendChild(option);
                });
            }
        } catch (error) {
            console.error('Erreur:', error);
        }
    }

    async function chargerProduit(id) {
        try {
            const response = await fetch('../../../index.php?action=produit&id=' + id);
            const data = await response.json();
            
            if (data.success) {
                const produit = data.data;
                document.getElementById('id-produit').value = produit.id_prod;
                document.getElementById('nom').value = produit.nom_prod;
                document.getElementById('categorie').value = produit.id_cat;
                document.getElementById('date-expiration').value = produit.date_expiration;
                document.getElementById('poids-produit').value = produit.poids_produit;
                document.getElementById('quantite-dispo').value = produit.quantite_dispo;
            }
        } catch (error) {
            console.error('Erreur:', error);
        }
    }

    async function sauvegarderProduit(event) {
        event.preventDefault();
        
        const id = document.getElementById('id-produit').value;
        const nomInput = document.getElementById('nom');
        const poidsInput = document.getElementById('poids-produit');
        const quantiteInput = document.getElementById('quantite-dispo');

        // Validation
        if (!validerNonVide(nomInput.value)) {
            alert('Le nom est requis');
            return;
        }
        if (!validerNombrePositif(poidsInput.value)) {
            alert('Le poids doit être un nombre positif');
            return;
        }
        if (!validerNombreEntier(quantiteInput.value)) {
            alert('La quantité doit être un nombre entier positif');
            return;
        }

        const produitData = {
            action: id ? 'modifier_produit' : 'ajouter_produit',
            nom_prod: nomInput.value,
            id_cat: document.getElementById('categorie').value,
            date_expiration: document.getElementById('date-expiration').value,
            poids_produit: poidsInput.value,
            quantite_dispo: quantiteInput.value
        };

        if (id) produitData.id = id;

        try {
            const response = await fetch('../../../index.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify(produitData)
            });
            
            const data = await response.json();
            alert(data.message);
            if (data.success) {
                window.location.href = 'produits_gestion.php';
            }
        } catch (error) {
            console.error('Erreur:', error);
            alert('Erreur lors de l\'enregistrement');
        }
    }
</script>

<?php include __DIR__ . '/../layouts/footer.php'; ?>
