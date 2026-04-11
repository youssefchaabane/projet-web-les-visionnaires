<?php
$traitements = $data['traitements'] ?? [];
$page = $data['page'] ?? 1;
$nombre_pages = $data['nombre_pages'] ?? 1;
?>

<h2>Liste des traitements</h2>

<?php if (empty($traitements)): ?>
    <p>Aucun traitement trouve.</p>
<?php else: ?>
    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Nom</th>
                <th>Type</th>
                <th>Dosage</th>
                <th>Duree</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($traitements as $traitement): ?>
                <tr>
                    <td>#<?php echo htmlspecialchars($traitement['id_traitement']); ?></td>
                    <td><?php echo htmlspecialchars($traitement['nom']); ?></td>
                    <td><?php echo htmlspecialchars($traitement['type_traitement'] ?? $traitement['type'] ?? ''); ?></td>
                    <td><?php echo htmlspecialchars($traitement['dosage'] ?? ''); ?></td>
                    <td><?php echo htmlspecialchars($traitement['duree'] ?? ''); ?></td>
                    <td>
                        <a href="traitement.php?action=editer&id=<?php echo (int)$traitement['id_traitement']; ?>">Editer</a>
                        |
                        <form method="POST" action="traitement.php?action=supprimer" style="display:inline;">
                            <input type="hidden" name="id_traitement" value="<?php echo (int)$traitement['id_traitement']; ?>">
                            <button type="submit" onclick="return confirm('Confirmer la suppression ?');">Supprimer</button>
                        </form>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>

    <?php if ($nombre_pages > 1): ?>
        <nav>
            <?php for ($i = 1; $i <= $nombre_pages; $i++): ?>
                <a href="traitement.php?page=<?php echo $i; ?>" <?php echo $i === $page ? 'style="font-weight:bold"' : ''; ?>>
                    <?php echo $i; ?>
                </a>
            <?php endfor; ?>
        </nav>
    <?php endif; ?>
<?php endif; ?>
