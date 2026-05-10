<?php
require __DIR__ . '/config/config.php';
$pdo = config::getConnexion();
$st = $pdo->query('SHOW TABLES');
print_r($st->fetchAll(PDO::FETCH_COLUMN));
