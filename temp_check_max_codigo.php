<?php
require 'config/bootstrap.php';
$pdo = Sacs\Core\Database::connection();

echo "=== MAX f15_codigo ===\n";
$stmt = $pdo->query('SELECT MAX(f15_codigo) AS max_codigo FROM f15_movimiento');
print_r($stmt->fetch());

echo "\n=== Últimos 5 movimientos ===\n";
$stmt = $pdo->query('SELECT f15_codigo, f15_operacion, f15_cliente FROM f15_movimiento ORDER BY f15_codigo DESC LIMIT 5');
print_r($stmt->fetchAll());
