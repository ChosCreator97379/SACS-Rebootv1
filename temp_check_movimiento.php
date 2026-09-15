<?php
require 'config/bootstrap.php';
$pdo = Sacs\Core\Database::connection();

echo "=== ESTRUCTURA f15_movimiento ===\n";
$stmt = $pdo->query('DESCRIBE f15_movimiento');
print_r($stmt->fetchAll());
