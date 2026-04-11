<?php

class config
{
  private static $pdo = null;

  private static function initialiserSchema()
  {
    $sqlAllergie = "
      CREATE TABLE IF NOT EXISTS allergie (
        id_allergie INT AUTO_INCREMENT PRIMARY KEY,
        nom VARCHAR(100) NOT NULL UNIQUE,
        description TEXT,
        niveau_danger VARCHAR(50) NOT NULL,
        symptomes TEXT,
        type VARCHAR(50) NOT NULL,
        date_creation TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        date_modification TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        INDEX idx_type (type),
        INDEX idx_niveau_danger (niveau_danger)
      ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
    ";

    $sqlTraitement = "
      CREATE TABLE IF NOT EXISTS traitement (
        id_traitement INT AUTO_INCREMENT PRIMARY KEY,
        nom VARCHAR(100) NOT NULL,
        type_traitement VARCHAR(100),
        dosage VARCHAR(100),
        duree VARCHAR(100),
        effets_secondaires TEXT,
        date_creation TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        date_modification TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        INDEX idx_type_traitement (type_traitement)
      ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
    ";

    $sqlAssociation = "
      CREATE TABLE IF NOT EXISTS allergie_traitement (
        id INT AUTO_INCREMENT PRIMARY KEY,
        id_allergie INT NOT NULL,
        id_traitement INT NOT NULL,
        date_ajout TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        UNIQUE KEY unique_allergie_traitement (id_allergie, id_traitement),
        INDEX idx_id_allergie (id_allergie),
        INDEX idx_id_traitement (id_traitement),
        CONSTRAINT fk_assoc_allergie FOREIGN KEY (id_allergie)
          REFERENCES allergie(id_allergie) ON DELETE CASCADE,
        CONSTRAINT fk_assoc_traitement FOREIGN KEY (id_traitement)
          REFERENCES traitement(id_traitement) ON DELETE CASCADE
      ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
    ";

    self::$pdo->exec($sqlAllergie);
    self::$pdo->exec($sqlTraitement);
    self::$pdo->exec($sqlAssociation);
  }

  public static function getConnexion()
  {
    if (!isset(self::$pdo)) {
      try {
        $pdoRoot = new PDO(
          'mysql:host=localhost;charset=utf8mb4',
          'root',
          '',
          [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
          ]
        );
        $pdoRoot->exec('CREATE DATABASE IF NOT EXISTS projet2a33 CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci');

        self::$pdo = new PDO(
          'mysql:host=localhost;dbname=gestion_allergies;charset=utf8mb4',
          'root',
          '',
          [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
          ]
        );

        self::initialiserSchema();
      } catch (Exception $e) {
        die('Erreur: ' . $e->getMessage());
      }
    }
    return self::$pdo;
  }
}
