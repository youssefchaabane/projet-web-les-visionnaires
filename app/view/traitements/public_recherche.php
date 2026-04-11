<?php
$terme = $data['terme'] ?? '';
$traitements = $data['traitements'] ?? [];
$nombre_resultats = $data['nombre_resultats'] ?? 0;
?>

<h2>Resultats de recherche: "<?php echo htmlspecialchars($terme); ?>"</h2>
<p><?php echo (int)$nombre_resultats; ?> resultat(s)</p>

<form method="GET" action="traitement_public.php">
    <input type="hidden" name="action" value="rechercher">
    <input type="text" name="q" required value="<?php echo htmlspecialchars($terme); ?>">
    <button type="submit">Rechercher</button>
</form>

<?php if (empty($traitements)): ?>
    <p>Aucun resultat.</p>
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
<?php endif; ?>
