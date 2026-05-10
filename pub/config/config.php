<?php

class Config
{
    private static $pdo = null;

    /**
     * Connexion principale PDO
     */
    public static function getConnexion(): PDO
    {
        if (self::$pdo === null) {

            $host = 'localhost';
            $dbname = 'gestion_publication'; // ⚠️ change si nécessaire
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
            } catch (PDOException $e) {
                die("Erreur de connexion à la base de données : " . $e->getMessage());
            }
        }

        return self::$pdo;
    }
}