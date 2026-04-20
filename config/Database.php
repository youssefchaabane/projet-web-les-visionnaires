<?php
class Config
{
    private static $pdo = null;

    public static function getConnexion()
    {
        if (!isset(self::$pdo)) {
            try {
                self::$pdo = new PDO(
                    'mysql:host=localhost;dbname=gestion_allergies;charset=utf8mb4',
                    'root',
                    '',
                    [
                        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                        PDO::ATTR_EMULATE_PREPARES => false
                    ]
                );
            } catch (Exception $e) {
                error_log('Database Error: ' . $e->getMessage());
                die('Erreur de connexion à la base de données: ' . $e->getMessage());
            }
        }
        return self::$pdo;
    }

    // Alias pour compatibilité avec Database::getInstance()
    public static function getInstance()
    {
        return new self();
    }

    // Méthode pour obtenir la connexion PDO
    public function getConnection()
    {
        return self::getConnexion();
    }

    private function __clone() {}
    public function __wakeup() {}
}

// Alias pour compatibilité
class Database extends Config {}
?>

