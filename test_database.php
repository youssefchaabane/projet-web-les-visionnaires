<?php
/**
 * Script de diagnostic - Vérifie la connexion BD et les tables
 */
header('Content-Type: application/json; charset=utf-8');

require_once __DIR__ . '/config/Database.php';

try {
    $db = Database::getInstance()->getConnection();
    
    // Test 1: Vérifier que la DB existe
    $result = $db->query("SELECT DATABASE()");
    $row = $result->fetch_assoc();
    
    $diagnostics = [
        'database_connected' => true,
        'database_name' => $row['DATABASE()'] ?? 'Inconnu',
        'tables' => [],
        'errors' => []
    ];
    
    // Test 2: Vérifier les tables
    $tables = ['categorie', 'produit'];
    foreach ($tables as $table) {
        $result = $db->query("SHOW TABLES LIKE '$table'");
        if ($result->num_rows > 0) {
            $diagnostics['tables'][$table] = 'OK';
        } else {
            $diagnostics['tables'][$table] = 'MANQUANTE - table not found';
            $diagnostics['errors'][] = "Table '$table' n'existe pas";
        }
    }
    
    // Test 3: Vérifier les colonnes de categorie
    if ($diagnostics['tables']['categorie'] === 'OK') {
        $result = $db->query("SHOW COLUMNS FROM categorie");
        $diagnostics['categorie_columns'] = [];
        while ($col = $result->fetch_assoc()) {
            $diagnostics['categorie_columns'][] = $col['Field'];
        }
    }
    
    // Test 4: Vérifier les colonnes de produit
    if ($diagnostics['tables']['produit'] === 'OK') {
        $result = $db->query("SHOW COLUMNS FROM produit");
        $diagnostics['produit_columns'] = [];
        while ($col = $result->fetch_assoc()) {
            $diagnostics['produit_columns'][] = $col['Field'];
        }
    }
    
    // Test 5: Compter les données
    if ($diagnostics['tables']['categorie'] === 'OK') {
        $result = $db->query("SELECT COUNT(*) as count FROM categorie");
        $row = $result->fetch_assoc();
        $diagnostics['categories_count'] = $row['count'];
    }
    
    if ($diagnostics['tables']['produit'] === 'OK') {
        $result = $db->query("SELECT COUNT(*) as count FROM produit");
        $row = $result->fetch_assoc();
        $diagnostics['produits_count'] = $row['count'];
    }
    
    echo json_encode($diagnostics, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
    
} catch (Exception $e) {
    echo json_encode([
        'error' => $e->getMessage(),
        'database_connected' => false
    ]);
}
?>
