<?php
require 'config/bootstrap.php';
$pdo = Sacs\Core\Database::connection();

echo "=== ESTRUCTURA f37_diario_contable ===\n";
$stmt = $pdo->query('DESCRIBE f37_diario_contable');
print_r($stmt->fetchAll());

echo "\n=== MUESTRA DE REGISTROS ===\n";
$stmt = $pdo->query('SELECT * FROM f37_diario_contable LIMIT 5');
print_r($stmt->fetchAll());
