<?php
/**
 * Classe Database - Singleton PDO
 * Gère la connexion à la base de données MySQL
 */
class Database {
    private static $instance = null;
    private $connexion = null;
    private $db_name = 'gestion_allergies';
    private $db_user = 'root';
    private $db_password = '';
    private $db_host = 'localhost';

    /**
     * Constructeur privé (Singleton)
     */
    private function __construct() {
        try {
            $dsn = "mysql:host={$this->db_host}";
            
            // Vérifier si la base de données existe
            $temp_pdo = new PDO($dsn, $this->db_user, $this->db_password);
            
            // Créer la base de données si elle n'existe pas
            $temp_pdo->exec("CREATE DATABASE IF NOT EXISTS {$this->db_name} CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
            
            // Maintenant se connecter à la base de données
            $dsn = "mysql:host={$this->db_host};dbname={$this->db_name};charset=utf8mb4";
            $this->connexion = new PDO($dsn, $this->db_user, $this->db_password);
            
            // Configuration PDO
            $this->connexion->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $this->connexion->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
            
            // Créer les tables si elles n'existent pas
            $this->creerTables();
            
        } catch (PDOException $e) {
            die("Erreur de connexion à la base de données: " . $e->getMessage());
        }
    }

    /**
     * Créer les tables nécessaires
     */
    private function creerTables() {
        $sql = "
            -- Table allergie
            CREATE TABLE IF NOT EXISTS allergie (
                id_allergie INT PRIMARY KEY AUTO_INCREMENT,
                nom VARCHAR(100) UNIQUE NOT NULL,
                description TEXT NOT NULL,
                niveau_danger ENUM('léger', 'modéré', 'sévère', 'critique') NOT NULL DEFAULT 'modéré',
                symptomes TEXT NOT NULL,
                type ENUM('alimentaire', 'respiratoire', 'cutané', 'médicale', 'autre') NOT NULL DEFAULT 'autre',
                date_creation TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                date_modification TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

            -- Table traitement
            CREATE TABLE IF NOT EXISTS traitement (
                id_traitement INT PRIMARY KEY AUTO_INCREMENT,
                nom VARCHAR(100) NOT NULL,
                type_traitement ENUM('urgence', 'antihistaminique', 'anti-inflammatoire', 'corticoïde', 'bronchodilatateur', 'autre') NOT NULL DEFAULT 'autre',
                dosage TEXT NOT NULL,
                duree VARCHAR(100) NOT NULL,
                effets_secondaires TEXT NOT NULL,
                date_creation TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                date_modification TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

            -- Table association allergie-traitement
            CREATE TABLE IF NOT EXISTS allergie_traitement (
                id INT PRIMARY KEY AUTO_INCREMENT,
                id_allergie INT NOT NULL,
                id_traitement INT NOT NULL,
                date_association TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                UNIQUE KEY unique_association (id_allergie, id_traitement),
                FOREIGN KEY (id_allergie) REFERENCES allergie(id_allergie) ON DELETE CASCADE,
                FOREIGN KEY (id_traitement) REFERENCES traitement(id_traitement) ON DELETE CASCADE
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

            -- Index pour les recherches
            CREATE INDEX IF NOT EXISTS idx_allergie_nom ON allergie(nom);
            CREATE INDEX IF NOT EXISTS idx_allergie_niveau ON allergie(niveau_danger);
            CREATE INDEX IF NOT EXISTS idx_traitement_nom ON traitement(nom);
            CREATE INDEX IF NOT EXISTS idx_traitement_type ON traitement(type_traitement);
        ";

        try {
            $this->connexion->exec($sql);
        } catch (PDOException $e) {
            // Les tables existent déjà, c'est normal
        }
    }

    /**
     * Obtenir l'instance unique de la base de données (Singleton)
     */
    public static function getInstance() {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    /**
     * Obtenir la connexion PDO
     */
    public function getConnexion() {
        return $this->connexion;
    }

    /**
     * Préparer une requête SQL
     */
    public function prepare($sql) {
        return $this->connexion->prepare($sql);
    }

    /**
     * Exécuter une requête SQL directement
     */
    public function exec($sql) {
        return $this->connexion->exec($sql);
    }

    /**
     * Obtenir le dernier ID inséré
     */
    public function lastInsertId() {
        return $this->connexion->lastInsertId();
    }

    /**
     * Début de transaction
     */
    public function beginTransaction() {
        return $this->connexion->beginTransaction();
    }

    /**
     * Commit de transaction
     */
    public function commit() {
        return $this->connexion->commit();
    }

    /**
     * Rollback de transaction
     */
    public function rollback() {
        return $this->connexion->rollback();
    }

    /**
     * Empêcher le clonage du Singleton
     */
    private function __clone() {}

    /**
     * Empêcher la sérialisation du Singleton
     */
    public function __sleep() {
        throw new Exception("Cannot serialize a singleton");
    }

    /**
     * Empêcher la désérialisation du Singleton
     */
    public function __wakeup() {
        throw new Exception("Cannot unserialize a singleton");
    }
}
?>
