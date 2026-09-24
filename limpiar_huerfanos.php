<?php
require __DIR__ . '/config/bootstrap.php';

use Sacs\Core\Database;

$pdo = Database::connection();

echo "=== BÚSQUEDA EXHAUSTIVA ===\n\n";

// 1. Verificar registros en el rango problemático
$sql = "SELECT * FROM f37_diario_contable WHERE f37_movimiento BETWEEN 122013600 AND 122013620 ORDER BY f37_movimiento";
$stmt = $pdo->prepare($sql);
$stmt->execute();
$resultados = $stmt->fetchAll();

echo "1. Registros en f37_diario_contable con movimiento entre 122013600 y 122013620:\n";
foreach ($resultados as $row) {
    echo "  Movimiento: {$row['f37_movimiento']}, Código: {$row['f37_codigo']}, Descripción: {$row['f37_descripcion']}\n";
}
if (empty($resultados)) {
    echo "  No se encontraron registros en ese rango\n";
}

// 2. Verificar registros en f15_movimiento en ese rango
$sql = "SELECT * FROM f15_movimiento WHERE f15_codigo BETWEEN 122013600 AND 122013620 ORDER BY f15_codigo";
$stmt = $pdo->prepare($sql);
$stmt->execute();
$resultados = $stmt->fetchAll();

echo "\n2. Registros en f15_movimiento con código entre 122013600 y 122013620:\n";
foreach ($resultados as $row) {
    echo "  Código: {$row['f15_codigo']}, Cliente: {$row['f15_cliente']}, Operación: {$row['f15_operacion']}\n";
}
if (empty($resultados)) {
    echo "  No se encontraron registros en ese rango\n";
}

// 3. Verificar si hay algún TRIGGER en f37_diario_contable
$sql = "SHOW TRIGGERS LIKE 'f37_diario_contable'";
$stmt = $pdo->prepare($sql);
$stmt->execute();
$triggers = $stmt->fetchAll();

echo "\n3. Triggers en f37_diario_contable:\n";
foreach ($triggers as $trigger) {
    echo "  Trigger: {$trigger['Trigger']}\n";
}
if (empty($triggers)) {
    echo "  No hay triggers\n";
}

// 4. Verificar restricciones UNIQUE en f37_diario_contable
$sql = "SHOW INDEX FROM f37_diario_contable WHERE Non_unique = 0";
$stmt = $pdo->prepare($sql);
$stmt->execute();
$indices = $stmt->fetchAll();

echo "\n4. Índices UNIQUE en f37_diario_contable:\n";
foreach ($indices as $index) {
    echo "  Índice: {$index['Key_name']}, Columna: {$index['Column_name']}\n";
}

// 5. Obtener todos los códigos en f37_diario_contable mayores a 122013500
$sql = "SELECT f37_movimiento FROM f37_diario_contable WHERE f37_movimiento > 122013500 ORDER BY f37_movimiento LIMIT 20";
$stmt = $pdo->prepare($sql);
$stmt->execute();
$resultados = $stmt->fetchAll();

echo "\n5. Códigos en f37_diario_contable mayores a 122013500 (primeros 20):\n";
foreach ($resultados as $row) {
    echo "  {$row['f37_movimiento']}\n";
}

// 6. Máximos actuales
$sql = "SELECT MAX(f15_codigo) as max_codigo FROM f15_movimiento";
$stmt = $pdo->prepare($sql);
$stmt->execute();
$result = $stmt->fetch();
echo "\n6. Máximo código en f15_movimiento: " . ($result['max_codigo'] ?? 0) . "\n";

$sql = "SELECT MAX(f37_movimiento) as max_codigo FROM f37_diario_contable";
$stmt = $pdo->prepare($sql);
$stmt->execute();
$result = $stmt->fetch();
echo "Máximo código en f37_diario_contable: " . ($result['max_codigo'] ?? 0) . "\n";
