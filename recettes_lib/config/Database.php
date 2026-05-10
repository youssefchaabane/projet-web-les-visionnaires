<?php
/**
 * RecConfig - Connexion PDO pour le module Recettes
 * Utilise la base gestion_allergies (tables rec_recette, rec_detail_recette)
 */
class RecConfig
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
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
          ]
        );
      } catch (Exception $e) {
        die('Erreur DB Recettes: ' . $e->getMessage());
      }
    }
    return self::$pdo;
  }
}
