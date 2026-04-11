<?php
$traitement = $data['traitement'] ?? [];
$types = $data['types'] ?? [];
$mode = $mode ?? (isset($traitement['id_traitement']) ? 'editer' : 'ajouter');
$action_form = $mode === 'editer' ? 'modifier' : 'creer';
?>

<h2><?php echo $mode === 'editer' ? 'Editer un traitement' : 'Ajouter un traitement'; ?></h2>

<form method="POST" action="traitement.php?action=<?php echo $action_form; ?>">
    <?php if ($mode === 'editer'): ?>
        <input type="hidden" name="id_traitement" value="<?php echo (int)($traitement['id_traitement'] ?? 0); ?>">
    <?php endif; ?>

    <label for="nom">Nom</label>
    <input type="text" id="nom" name="nom" required value="<?php echo htmlspecialchars($traitement['nom'] ?? ''); ?>">

    <label for="type_traitement">Type</label>
    <select id="type_traitement" name="type_traitement" required>
        <option value="">-- Choisir --</option>
        <?php foreach ($types as $type): ?>
            <option value="<?php echo htmlspecialchars($type); ?>" <?php echo (($traitement['type_traitement'] ?? '') === $type) ? 'selected' : ''; ?>>
                <?php echo htmlspecialchars(ucfirst($type)); ?>
            </option>
        <?php endforeach; ?>
    </select>

    <label for="dosage">Dosage</label>
    <input type="text" id="dosage" name="dosage" required value="<?php echo htmlspecialchars($traitement['dosage'] ?? ''); ?>">

    <label for="duree">Duree</label>
    <input type="text" id="duree" name="duree" value="<?php echo htmlspecialchars($traitement['duree'] ?? ''); ?>">

    <label for="effets_secondaires">Description / Effets secondaires</label>
    <textarea id="effets_secondaires" name="effets_secondaires" required><?php echo htmlspecialchars($traitement['effets_secondaires'] ?? ''); ?></textarea>

    <button type="submit"><?php echo $mode === 'editer' ? 'Mettre a jour' : 'Ajouter'; ?></button>
    <a href="traitement.php">Annuler</a>
</form>
