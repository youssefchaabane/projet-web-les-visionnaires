<?php
declare(strict_types=1);

/**
 * Connexion PDO unique (singleton) — motif classique MVP / cours PHP MySQL.
 * Paramètres lus depuis config.local.php à la racine de config/.
 */
final class Database
{
    private static ?PDO $instance = null;

    /**
     * @return array{host:string, dbname:string, user:string, pass:string, charset:string}
     */
    private static function parametres(): array
    {
        $host = 'localhost';
        $dbname = 'gestion-allergies';
        $user = 'root';
        $pass = '';
        $charset = 'utf8mb4';

        $local = dirname(__DIR__) . '/config/config.local.php';
        if (is_readable($local)) {
            $cfg = require $local;
            if (is_array($cfg)) {
                $host = $cfg['host'] ?? $host;
                $dbname = $cfg['dbname'] ?? $dbname;
                $user = $cfg['user'] ?? $user;
                $pass = $cfg['pass'] ?? $pass;
                $charset = $cfg['charset'] ?? $charset;
            }
        }

        return compact('host', 'dbname', 'user', 'pass', 'charset');
    }

    public static function getInstance(): PDO
    {
        if (self::$instance === null) {
            $p = self::parametres();
            $dsn = sprintf(
                'mysql:host=%s;dbname=%s;charset=%s',
                $p['host'],
                $p['dbname'],
                $p['charset']
            );
            try {
                self::$instance = new PDO($dsn, $p['user'], $p['pass'], [
                    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                ]);
            } catch (Throwable $e) {
                exit('Erreur PDO : ' . htmlspecialchars($e->getMessage(), ENT_QUOTES, 'UTF-8'));
            }
        }

        return self::$instance;
    }

    /**
     * Alias express pour les modèles (comme dans certains cours « getConnection »).
     */
    public static function getConnection(): PDO
    {
        return self::getInstance();
    }
}
