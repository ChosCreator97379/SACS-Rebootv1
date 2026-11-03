<?php

error_reporting(E_ALL);
ini_set('display_errors', '0'); // No mostrar errores en el output JSON

require dirname(__DIR__) . '/../config/bootstrap.php';

use Sacs\Core\Database;

header('Content-Type: application/json');

$codigo = trim((string) ($_GET['codigo'] ?? ''));

if ($codigo === '') {
    echo json_encode(['error' => 'Código de préstamo no especificado']);
    exit;
}

try {
    $pdo = Database::connection();

    $sql = "SELECT
                f17_ncuota as numero,
                f17_fecha as fecha,
                f17_capital as capital,
                f17_interes as interes,
                f17_total as total,
                f17_saldo as saldo,
                f17_estado as estado
            FROM f17_cuotasprestamo_nuevo
            WHERE f17_prestamo = :codigo
            ORDER BY f17_ncuota ASC";

    $stmt = $pdo->prepare($sql);
    $stmt->execute(['codigo' => $codigo]);
    $cronograma = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode(['cronograma' => $cronograma]);
} catch (Exception $e) {
    echo json_encode(['error' => $e->getMessage()]);
}
