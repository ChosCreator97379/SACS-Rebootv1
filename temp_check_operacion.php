<?php
require 'config/bootstrap.php';
$pdo = Sacs\Core\Database::connection();

echo "=== ESTRUCTURA f14_operacion ===\n";
$stmt = $pdo->query('DESCRIBE f14_operacion');
print_r($stmt->fetchAll());
