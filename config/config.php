<?php
class config
{
    private static $pdo = null;

    // --- Configuration IA (Groq Cloud) ---
    public const GROQ_ENDPOINT = 'https://api.groq.com/openai/v1/chat/completions';
    public const GROQ_API_KEY = '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';
    public const GROQ_MODEL    = 'llama-3.1-8b-instant';

    public static function getConnexion()
    {
        if (!isset(self::$pdo)) {
            $host = 'localhost';
            $dbname = 'projet-web';
            $user = 'root';
            $pass = '';
            $charset = 'utf8mb4';

            $localConfig = __DIR__ . '/config.local.php';
            if (is_readable($localConfig)) {
                $cfg = require $localConfig;
                if (is_array($cfg)) {
                    $host = $cfg['host'] ?? $host;
                    $dbname = $cfg['dbname'] ?? $dbname;
                    $user = $cfg['user'] ?? $user;
                    $pass = $cfg['pass'] ?? $pass;
                    $charset = $cfg['charset'] ?? $charset;
                }
            }

            $dsn = sprintf('mysql:host=%s;dbname=%s;charset=%s', $host, $dbname, $charset);

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
            } catch (Exception $e) {
                die('Erreur: ' . $e->getMessage());
            }
        }
        return self::$pdo;
    }

    private static function initializeSchema(): void
    {
        $queries = [
            "CREATE TABLE IF NOT EXISTS `utilisateur` (
                `id_user` INT AUTO_INCREMENT PRIMARY KEY,
                `nom_prenom` VARCHAR(255) NOT NULL,
                `email` VARCHAR(255) NOT NULL UNIQUE,
                `mot_de_passe` VARCHAR(255) NOT NULL,
                `date_creation` DATE NOT NULL,
                `role` VARCHAR(50) NOT NULL DEFAULT 'utilisateur',
                `est_actif` TINYINT(1) NOT NULL DEFAULT 1,
                `niveau_activite` VARCHAR(100) NOT NULL DEFAULT '',
                `regime_alimentaire` VARCHAR(100) NOT NULL DEFAULT '',
                `objectif_sante` VARCHAR(255) NOT NULL DEFAULT '',
                `objectif_eco` VARCHAR(255) NOT NULL DEFAULT ''
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4",
            "CREATE TABLE IF NOT EXISTS `allergie` (
                `id_allergie` INT AUTO_INCREMENT PRIMARY KEY,
                `nom` VARCHAR(255) NOT NULL,
                `description` TEXT NOT NULL,
                `niveau_danger` VARCHAR(100) NOT NULL,
                `symptomes` TEXT NOT NULL,
                `type` VARCHAR(100) NOT NULL
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4",
            "CREATE TABLE IF NOT EXISTS `utilisateur_allergie` (
                `id_user` INT NOT NULL,
                `id_allergie` INT NOT NULL,
                PRIMARY KEY (`id_user`, `id_allergie`),
                KEY (`id_allergie`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4",
            "CREATE TABLE IF NOT EXISTS `traitement` (
                `id_traitement` INT AUTO_INCREMENT PRIMARY KEY,
                `nom` VARCHAR(255) NOT NULL,
                `type_traitement` VARCHAR(255) NOT NULL,
                `dosage` VARCHAR(255) DEFAULT NULL,
                `duree` VARCHAR(255) DEFAULT NULL,
                `effets_secondaires` TEXT DEFAULT NULL
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4",
            "CREATE TABLE IF NOT EXISTS `allergie_traitement` (
                `id_allergie` INT NOT NULL,
                `id_traitement` INT NOT NULL,
                PRIMARY KEY (`id_allergie`, `id_traitement`),
                KEY (`id_traitement`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4",
        ];

        foreach ($queries as $query) {
            self::$pdo->exec($query);
        }
    }
}
