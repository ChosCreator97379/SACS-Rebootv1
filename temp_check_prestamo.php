<?php
require 'config/bootstrap.php';
$pdo = Sacs\Core\Database::connection();

echo "=== ESTRUCTURA f16_prestamo ===\n";
$stmt = $pdo->query('DESCRIBE f16_prestamo');
print_r($stmt->fetchAll());
