<?php
$allergies = $data['allergies'] ?? [];
$page = $data['page'] ?? 1;
$nombre_pages = $data['nombre_pages'] ?? 1;
?>

<h2>Liste des allergies</h2>

<?php if (empty($allergies)): ?>
    <p>Aucune allergie trouvee.</p>
<?php else: ?>
    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Nom</th>
                <th>Type</th>
                <th>Niveau</th>
                <th>Symptomes</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($allergies as $allergie): ?>
                <tr>
                    <td>#<?php echo htmlspecialchars($allergie['id_allergie']); ?></td>
                    <td><?php echo htmlspecialchars($allergie['nom']); ?></td>
                    <td><?php echo htmlspecialchars($allergie['type']); ?></td>
                    <td><?php echo htmlspecialchars($allergie['niveau_danger']); ?></td>
                    <td><?php echo htmlspecialchars(substr($allergie['symptomes'], 0, 80)); ?>...</td>
                    <td>
                        <a href="admin.php?action=editer&id=<?php echo (int)$allergie['id_allergie']; ?>">Editer</a>
                        |
                        <form method="POST" action="admin.php?action=supprimer" style="display:inline;">
                            <input type="hidden" name="id_allergie" value="<?php echo (int)$allergie['id_allergie']; ?>">
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
                <a href="admin.php?page=<?php echo $i; ?>" <?php echo $i === $page ? 'style="font-weight:bold"' : ''; ?>>
                    <?php echo $i; ?>
                </a>
            <?php endfor; ?>
        </nav>
    <?php endif; ?>
<?php endif; ?>
