<?php
$traitements = $data['traitements'] ?? [];
$page = $data['page'] ?? 1;
$nombre_pages = $data['nombre_pages'] ?? 1;
$total = $data['total'] ?? 0;
?>

<h2>Traitements (<?php echo (int)$total; ?>)</h2>

<form method="GET" action="traitement_public.php">
    <input type="hidden" name="action" value="rechercher">
    <input type="text" name="q" placeholder="Rechercher un traitement..." required>
    <button type="submit">Rechercher</button>
</form>

<?php if (empty($traitements)): ?>
    <p>Aucun traitement disponible.</p>
<?php else: ?>
    <ul>
        <?php foreach ($traitements as $traitement): ?>
            <li>
                <strong><?php echo htmlspecialchars($traitement['nom']); ?></strong>
                (<?php echo htmlspecialchars($traitement['type_traitement'] ?? $traitement['type'] ?? ''); ?>)
                - <a href="traitement_public.php?action=detail&id=<?php echo (int)$traitement['id_traitement']; ?>">Details</a>
            </li>
        <?php endforeach; ?>
    </ul>

    <?php if ($nombre_pages > 1): ?>
        <nav>
            <?php for ($i = 1; $i <= $nombre_pages; $i++): ?>
                <a href="traitement_public.php?action=traitements&page=<?php echo $i; ?>" <?php echo $i === $page ? 'style="font-weight:bold"' : ''; ?>>
                    <?php echo $i; ?>
                </a>
            <?php endfor; ?>
        </nav>
    <?php endif; ?>
<?php endif; ?>
