<?php
$traitement = $data['traitement'] ?? null;
$allergies = $data['allergies'] ?? [];
?>

<?php if (!$traitement): ?>
    <p>Traitement introuvable.</p>
    <p><a href="traitement_public.php?action=traitements">Retour a la liste</a></p>
<?php else: ?>
    <p><a href="traitement_public.php?action=traitements">Retour a la liste</a></p>
    <h2><?php echo htmlspecialchars($traitement['nom']); ?></h2>
    <p><strong>Type:</strong> <?php echo htmlspecialchars($traitement['type_traitement']); ?></p>
    <p><strong>Dosage:</strong> <?php echo htmlspecialchars($traitement['dosage']); ?></p>
    <p><strong>Duree:</strong> <?php echo htmlspecialchars($traitement['duree']); ?></p>
    <p><strong>Effets secondaires:</strong> <?php echo htmlspecialchars($traitement['effets_secondaires']); ?></p>

    <?php if (!empty($allergies)): ?>
        <h3>Allergies associees</h3>
        <ul>
            <?php foreach ($allergies as $allergie): ?>
                <li><?php echo htmlspecialchars($allergie['nom']); ?> (<?php echo htmlspecialchars($allergie['type']); ?>)</li>
            <?php endforeach; ?>
        </ul>
    <?php endif; ?>
<?php endif; ?>
