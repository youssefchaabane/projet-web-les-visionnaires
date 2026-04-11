<?php
$allergie = $data['allergie'] ?? [];
$types = $data['types'] ?? [];
$niveaux_danger = $data['niveaux_danger'] ?? [];
$mode = $mode ?? (isset($allergie['id_allergie']) ? 'editer' : 'ajouter');
$action_form = $mode === 'editer' ? 'modifier' : 'creer';
?>

<h2><?php echo $mode === 'editer' ? 'Editer une allergie' : 'Ajouter une allergie'; ?></h2>

<form method="POST" action="admin.php?action=<?php echo $action_form; ?>">
    <?php if ($mode === 'editer'): ?>
        <input type="hidden" name="id_allergie" value="<?php echo (int)($allergie['id_allergie'] ?? 0); ?>">
    <?php endif; ?>

    <label for="nom">Nom</label>
    <input type="text" id="nom" name="nom" required value="<?php echo htmlspecialchars($allergie['nom'] ?? ''); ?>">

    <label for="type">Type</label>
    <select id="type" name="type" required>
        <option value="">-- Choisir --</option>
        <?php foreach ($types as $type): ?>
            <option value="<?php echo htmlspecialchars($type); ?>" <?php echo (($allergie['type'] ?? '') === $type) ? 'selected' : ''; ?>>
                <?php echo htmlspecialchars(ucfirst($type)); ?>
            </option>
        <?php endforeach; ?>
    </select>

    <label for="niveau_danger">Niveau de danger</label>
    <select id="niveau_danger" name="niveau_danger" required>
        <option value="">-- Choisir --</option>
        <?php foreach ($niveaux_danger as $niveau): ?>
            <option value="<?php echo htmlspecialchars($niveau); ?>" <?php echo (($allergie['niveau_danger'] ?? '') === $niveau) ? 'selected' : ''; ?>>
                <?php echo htmlspecialchars(ucfirst($niveau)); ?>
            </option>
        <?php endforeach; ?>
    </select>

    <label for="description">Description</label>
    <textarea id="description" name="description" required><?php echo htmlspecialchars($allergie['description'] ?? ''); ?></textarea>

    <label for="symptomes">Symptomes</label>
    <textarea id="symptomes" name="symptomes" required><?php echo htmlspecialchars($allergie['symptomes'] ?? ''); ?></textarea>

    <button type="submit"><?php echo $mode === 'editer' ? 'Mettre a jour' : 'Ajouter'; ?></button>
    <a href="admin.php">Annuler</a>
</form>
