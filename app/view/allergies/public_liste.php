<?php
$allergies = $data['allergies'] ?? [];
$page = $data['page'] ?? 1;
$nombre_pages = $data['nombre_pages'] ?? 1;
$total = $data['total'] ?? 0;
?>

<h2>Allergies (<?php echo (int)$total; ?>)</h2>

<form method="GET" action="index.php">
    <input type="hidden" name="action" value="rechercher">
    <input type="text" name="q" placeholder="Rechercher une allergie..." required>
    <button type="submit">Rechercher</button>
</form>

<?php if (empty($allergies)): ?>
    <p>Aucune allergie disponible.</p>
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

    <?php if ($nombre_pages > 1): ?>
        <nav>
            <?php for ($i = 1; $i <= $nombre_pages; $i++): ?>
                <a href="index.php?action=allergies&page=<?php echo $i; ?>" <?php echo $i === $page ? 'style="font-weight:bold"' : ''; ?>>
                    <?php echo $i; ?>
                </a>
            <?php endfor; ?>
        </nav>
    <?php endif; ?>
<?php endif; ?>
