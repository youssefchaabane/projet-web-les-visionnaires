<?php
$allergie = $data['allergie'] ?? null;
?>

<?php if (!$allergie): ?>
    <p>Allergie introuvable.</p>
    <p><a href="index.php?action=allergies">Retour a la liste</a></p>
<?php else: ?>
    <p><a href="index.php?action=allergies">Retour a la liste</a></p>
    <h2><?php echo htmlspecialchars($allergie['nom']); ?></h2>
    <p><strong>Type:</strong> <?php echo htmlspecialchars($allergie['type']); ?></p>
    <p><strong>Niveau:</strong> <?php echo htmlspecialchars($allergie['niveau_danger']); ?></p>
    <p><strong>Description:</strong> <?php echo htmlspecialchars($allergie['description']); ?></p>
    <p><strong>Symptomes:</strong> <?php echo htmlspecialchars($allergie['symptomes']); ?></p>
<?php endif; ?>
