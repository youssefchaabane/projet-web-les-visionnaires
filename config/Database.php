<?php
/**
 * Classe de connection à la base de données avec PDO
 * Respecte les principes de la POO et du pattern Singleton
 */
class Database {
    private static $instance = null;
    private $pdo;
    private $host = 'localhost';
    private $db_name = 'gestion_allergies';
    private $user = 'root';
    private $password = '';
    private $charset = 'utf8mb4';

    private function __construct() {
        try {
            $dsn = "mysql:host={$this->host};charset={$this->charset}";
            $pdoConnect = new PDO($dsn, $this->user, $this->password);
            $pdoConnect->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

            // Créer la base de données si elle n'existe pas
            $pdoConnect->exec("CREATE DATABASE IF NOT EXISTS {$this->db_name}");
            
            // Sélectionner la base de données
            $dsn = "mysql:host={$this->host};dbname={$this->db_name};charset={$this->charset}";
            $this->pdo = new PDO($dsn, $this->user, $this->password);
            $this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            
            // Créer les tables si elles n'existent pas
            $this->createTables();
        } catch (PDOException $e) {
            die("Erreur de connexion à la base de données : " . $e->getMessage());
        }
    }

    /**
     * Retourne l'instance singleton de la base de données
     */
    public static function getInstance() {
        if (self::$instance === null) {
            self::$instance = new Database();
        }
        return self::$instance;
    }

    /**
     * Crée les tables de la base de données
     */
    private function createTables() {
        try {
            // Table Allergie
            $sqlAllergie = "
            CREATE TABLE IF NOT EXISTS allergie (
                id_allergie INT AUTO_INCREMENT PRIMARY KEY,
                nom VARCHAR(100) NOT NULL UNIQUE,
                description TEXT,
                niveau_danger VARCHAR(50) NOT NULL,
                symptomes TEXT,
                type VARCHAR(50) NOT NULL,
                date_creation TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                date_modification TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
            ";
            
            // Table Traitement
            $sqlTraitement = "
            CREATE TABLE IF NOT EXISTS traitement (
                id_traitement INT AUTO_INCREMENT PRIMARY KEY,
                nom VARCHAR(100) NOT NULL,
                type_traitement VARCHAR(100),
                dosage VARCHAR(100),
                duree VARCHAR(100),
                effets_secondaires TEXT,
                date_creation TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                date_modification TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
            ";
            
            // Table de jointure Allergie_Traitement
            $sqlJointure = "
            CREATE TABLE IF NOT EXISTS allergie_traitement (
                id INT AUTO_INCREMENT PRIMARY KEY,
                id_allergie INT NOT NULL,
                id_traitement INT NOT NULL,
                date_ajout TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                FOREIGN KEY (id_allergie) REFERENCES allergie(id_allergie) ON DELETE CASCADE,
                FOREIGN KEY (id_traitement) REFERENCES traitement(id_traitement) ON DELETE CASCADE,
                UNIQUE KEY unique_allergie_traitement (id_allergie, id_traitement)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
            ";

            $this->pdo->exec($sqlAllergie);
            $this->pdo->exec($sqlTraitement);
            $this->pdo->exec($sqlJointure);

            // Table Categorie
            $sqlCategorie = "
            CREATE TABLE IF NOT EXISTS categorie (
                id_cat INT AUTO_INCREMENT PRIMARY KEY,
                nom_cat VARCHAR(100) NOT NULL UNIQUE,
                description_cat TEXT NOT NULL,
                lieu_stockage VARCHAR(100),
                temp_conseille FLOAT,
                delai_alerte_jours INT DEFAULT 7,
                date_creation TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                date_modification TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
            ";

            // Table Produit
            $sqlProduit = "
            CREATE TABLE IF NOT EXISTS produit (
                id_prod INT AUTO_INCREMENT PRIMARY KEY,
                nom_prod VARCHAR(100) NOT NULL,
                date_expiration DATE NOT NULL,
                poids_produit FLOAT NOT NULL,
                quantite_dispo INT NOT NULL DEFAULT 0,
                id_cat INT,
                date_creation TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                date_modification TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                FOREIGN KEY (id_cat) REFERENCES categorie(id_cat) ON DELETE SET NULL
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
            ";

            $this->pdo->exec($sqlCategorie);
            $this->pdo->exec($sqlProduit);
        } catch (PDOException $e) {
            die("Erreur lors de la création des tables : " . $e->getMessage());
        }
    }

    /**
     * Retourne la connexion PDO
     */
    public function getConnection() {
        return $this->pdo;
    }

    // Empêcher le clonage
    private function __clone() {}

    // Empêcher la désérialisation
    public function __wakeup() {
        throw new Exception("Cannot unserialize a Singleton");
    }
}
?>
