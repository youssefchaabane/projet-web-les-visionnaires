<?php
$terme = $data['terme'] ?? '';
$allergies = $data['allergies'] ?? [];
$nombre_resultats = $data['nombre_resultats'] ?? 0;
?>

<h2>Resultats de recherche: "<?php echo htmlspecialchars($terme); ?>"</h2>
<p><?php echo (int)$nombre_resultats; ?> resultat(s)</p>

<form method="GET" action="index.php">
    <input type="hidden" name="action" value="rechercher">
    <input type="text" name="q" required value="<?php echo htmlspecialchars($terme); ?>">
    <button type="submit">Rechercher</button>
</form>

<?php if (empty($allergies)): ?>
    <p>Aucun resultat.</p>
<?php else: ?>
    <ul>
        <?php foreach ($allergies as $allergie): ?>
            <li>
                <strong><?php echo htmlspecialchars($allergie['nom']); ?></strong>
                (<?php echo htmlspecialchars($allergie['niveau_danger']); ?>)
                - <a href="index.php?action=detail&id=<?php echo (int)$allergie['id_allergie']; ?>">Details</a>
            </li>
        <?php endforeach; ?>
    </ul>
<?php endif; ?>
