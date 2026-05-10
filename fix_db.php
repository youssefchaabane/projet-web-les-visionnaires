<?php
require_once __DIR__ . '/config/config.php';

try {
    $pdo = config::getConnexion();
    
    $sql = "
    DROP TABLE IF EXISTS `utilisateur_allergie`;
    CREATE TABLE `utilisateur_allergie` (
      `id_user` INT NOT NULL,
      `id_allergie` INT NOT NULL,
      PRIMARY KEY (`id_user`, `id_allergie`),
      FOREIGN KEY (`id_user`) REFERENCES `utilisateur`(`id_user`) ON DELETE CASCADE,
      FOREIGN KEY (`id_allergie`) REFERENCES `allergie`(`id_allergie`) ON DELETE CASCADE
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
    ";

    $pdo->exec($sql);
    echo "Succès : La table utilisateur_allergie a été créée avec succès.<br>";
    echo "<a href='view/user_home.php'>Retourner à l'accueil utilisateur</a>";
    
} catch (Exception $e) {
    echo "Erreur : " . $e->getMessage();
}
?>
