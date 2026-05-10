<?php
// Copier vers config.local.php et adapter. Schéma SQL : database/gestion_allergies_schema.sql
// Vérification : ouvrir database/schema_status.php dans le navigateur.
return [
    'host' => 'localhost',
    'dbname' => 'gestion_allergies',
    'user' => 'root',
    'pass' => '',
    'charset' => 'utf8mb4',
    // null ou '' = mêmes tables publication / commentaire que la base principale
    'publication_dbname' => null,
];
