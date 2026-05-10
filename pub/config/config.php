<?php

class PubConfig
{
    private static $pdo = null;

    /**
     * Connexion principale PDO
     */
    public static function getConnexion(): PDO
    {
        if (self::$pdo === null) {

            $host = 'localhost';
            $dbname = 'projet-web';
            $user = 'root';
            $pass = '';
            $charset = 'utf8mb4';

            $dsn = "mysql:host=$host;dbname=$dbname;charset=$charset";

            try {
                self::$pdo = new PDO(
                    $dsn,
                    $user,
                    $pass,
                    [
                        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
                    ]
                );
                self::initializeSchema();
            } catch (PDOException $e) {
                die("Erreur de connexion à la base de données : " . $e->getMessage());
            }
        }

        return self::$pdo;
    }

    private static function initializeSchema(): void
    {
        $queries = [
            "CREATE TABLE IF NOT EXISTS `publication` (
                `id_pub` INT AUTO_INCREMENT PRIMARY KEY,
                `titre` VARCHAR(255) NOT NULL,
                `contenu` TEXT NOT NULL,
                `date_publication` DATETIME NOT NULL,
                `media_url` VARCHAR(512) DEFAULT NULL,
                `id_user` INT NOT NULL
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4",
            "CREATE TABLE IF NOT EXISTS `commentaire` (
                `id_commentaire` INT AUTO_INCREMENT PRIMARY KEY,
                `note` INT DEFAULT NULL,
                `contenu` TEXT NOT NULL,
                `date_commentaire` DATE NOT NULL,
                `likes_count` INT NOT NULL DEFAULT 0,
                `id_pub` INT NOT NULL,
                INDEX (`id_pub`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4",
        ];

        foreach ($queries as $query) {
            self::$pdo->exec($query);
        }
    }
}