<?php
require_once __DIR__ . '/pub/config/config.php';

try {
    $pdo = PubConfig::getConnexion();
    $sql = file_get_contents(__DIR__ . '/pub_migration.sql');
    
    // On exécute le script SQL (plusieurs requêtes séparées si nécessaire)
    // PDO::exec ne gère pas toujours bien les fichiers SQL complexes, 
    // mais ici ce sont des requêtes simples.
    $pdo->exec($sql);
    
    echo "<div style='font-family: sans-serif; padding: 20px; background: #d1fae5; border: 1px solid #10b981; border-radius: 8px; color: #065f46; max-width: 600px; margin: 40px auto;'>";
    echo "<h1 style='margin-top:0;'>✅ Base de données réparée !</h1>";
    echo "<p>Les tables <strong>publication</strong> et <strong>commentaire</strong> ont été créées avec succès dans la base 'projet-web'.</p>";
    echo "<a href='view/publications_admin.php' style='display:inline-block; padding: 10px 20px; background: #10b981; color: white; text-decoration: none; border-radius: 6px; font-weight: bold;'>Accéder à l'administration</a>";
    echo "</div>";
    
} catch (Exception $e) {
    echo "<div style='font-family: sans-serif; padding: 20px; background: #fee2e2; border: 1px solid #ef4444; border-radius: 8px; color: #991b1b; max-width: 600px; margin: 40px auto;'>";
    echo "<h1 style='margin-top:0;'>❌ Erreur</h1>";
    echo "<p>" . $e->getMessage() . "</p>";
    echo "</div>";
}
