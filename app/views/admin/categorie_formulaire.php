<?php
/**
 * Vue - Formulaire Catégorie (Admin)
 */
$baseUrl = '/gestion-stock';
$id = $_GET['id'] ?? null;
$pageTitle = ($id ? 'Éditer' : 'Ajouter') . ' Catégorie - Admin';
include __DIR__ . '/../layouts/header.php';
?>

<div class="container my-5">
    <div class="row mb-4">
        <div class="col-md-8">
            <h2><?php echo $id ? '✏️ Éditer Catégorie' : '➕ Ajouter Catégorie'; ?></h2>
        </div>
        <div class="col-md-4 text-end">
            <a href="categories_gestion.php" class="btn btn-secondary">Retour</a>
        </div>
    </div>

    <div class="card">
        <div class="card-body">
            <form id="form-categorie" onsubmit="sauvegarderCategorie(event)">
                <input type="hidden" id="id-categorie">

                <div class="mb-3">
                    <label for="nom" class="form-label">Nom de la Catégorie *</label>
                    <input type="text" id="nom" class="form-control" required>
                </div>

                <div class="mb-3">
                    <label for="description" class="form-label">Description</label>
                    <textarea id="description" class="form-control" rows="3"></textarea>
                </div>

                <div class="row mb-3">
                    <div class="col-md-6">
                        <label for="lieu-stockage" class="form-label">Lieu de Stockage</label>
                        <input type="text" id="lieu-stockage" class="form-control" placeholder="Ex: Armoire A1">
                    </div>
                    <div class="col-md-6">
                        <label for="temp-conseille" class="form-label">Température Conseillée</label>
                        <input type="text" id="temp-conseille" class="form-control" placeholder="Ex: 15-25°C">
                    </div>
                </div>

                <div class="mb-3">
                    <label for="delai-alerte" class="form-label">Délai d'Alerte (jours)</label>
                    <input type="number" id="delai-alerte" class="form-control" value="30">
                </div>

                <div class="text-end">
                    <button type="submit" class="btn btn-success">✔️ Enregistrer</button>
                    <a href="categories_gestion.php" class="btn btn-secondary">Annuler</a>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        <?php if ($id): ?>
            chargerCategorie(<?php echo $id; ?>);
        <?php endif; ?>
    });

    async function chargerCategorie(id) {
        try {
            const response = await fetch('../../../index.php?action=categorie&id=' + id);
            const data = await response.json();
            
            if (data.success) {
                const categorie = data.data;
                document.getElementById('id-categorie').value = categorie.id_cat;
                document.getElementById('nom').value = categorie.nom_cat;
                document.getElementById('description').value = categorie.description_cat;
                document.getElementById('lieu-stockage').value = categorie.lieu_stockage;
                document.getElementById('temp-conseille').value = categorie.temp_conseille;
                document.getElementById('delai-alerte').value = categorie.delai_alerte_jours;
            }
        } catch (error) {
            console.error('Erreur:', error);
        }
    }

    async function sauvegarderCategorie(event) {
        event.preventDefault();
        
        const id = document.getElementById('id-categorie').value;
        const nomInput = document.getElementById('nom');

        // Validation
        if (!validerNonVide(nomInput.value)) {
            alert('Le nom est requis');
            return;
        }

        const categorieData = {
            action: id ? 'modifier_categorie' : 'ajouter_categorie',
            nom_cat: nomInput.value,
            description_cat: document.getElementById('description').value,
            lieu_stockage: document.getElementById('lieu-stockage').value,
            temp_conseille: document.getElementById('temp-conseille').value,
            delai_alerte_jours: document.getElementById('delai-alerte').value
        };

        if (id) categorieData.id = id;

        try {
            const response = await fetch('../../../index.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify(categorieData)
            });
            
            const data = await response.json();
            alert(data.message);
            if (data.success) {
                window.location.href = 'categories_gestion.php';
            }
        } catch (error) {
            console.error('Erreur:', error);
            alert('Erreur lors de l\'enregistrement');
        }
    }
</script>

<?php include __DIR__ . '/../layouts/footer.php'; ?>
