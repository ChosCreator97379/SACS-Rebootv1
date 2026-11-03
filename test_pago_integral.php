<?php
require 'config/bootstrap.php';

$pdo = new PDO(
    'mysql:host=' . ($_ENV['DB_HOST'] ?? 'localhost') . ';dbname=' . ($_ENV['DB_NAME'] ?? 'sacs') . ';charset=utf8mb4',
    $_ENV['DB_USER'] ?? 'root',
    $_ENV['DB_PASS'] ?? '',
    [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
);

echo "=== PRUEBA DE PAGO INTEGRAL ===\n\n";

// Usar el servicio de pago integral
$pagoIntegralServicio = new \Sacs\Services\PagoIntegralServicio();

// Préstamo de prueba: 36614
$prestamoCodigo = 36614;

echo "Préstamo: " . $prestamoCodigo . "\n\n";

// Probar obtener cuotas parciales
echo "=== PROBANDO obtenerCuotasParciales() ===\n\n";
$cuotasParciales = $pagoIntegralServicio->obtenerCuotasParciales($prestamoCodigo);

if (empty($cuotasParciales)) {
    echo "No hay cuotas parciales\n\n";
} else {
    echo "Cuotas parciales encontradas: " . count($cuotasParciales) . "\n\n";
    foreach ($cuotasParciales as $cuota) {
        echo "  Cuota " . $cuota['numero_cuota'] . ":\n";
        echo "    Estado: " . $cuota['estado'] . "\n";
        echo "    Total: " . $cuota['total'] . "\n";
        echo "    Faltante: " . $cuota['faltante'] . "\n";
        echo "    Fecha: " . $cuota['fecha_vencimiento'] . "\n";
        echo "    ---\n";
    }
}

// Probar obtener cuota actual
echo "=== PROBANDO obtenerCuotaActual() ===\n\n";
$cuotaActual = $pagoIntegralServicio->obtenerCuotaActual($prestamoCodigo);

if ($cuotaActual === null) {
    echo "No hay cuota actual (todas pagadas)\n\n";
} else {
    echo "Cuota actual:\n";
    echo "  Número: " . $cuotaActual['numero_cuota'] . "\n";
    echo "  Estado: " . $cuotaActual['estado'] . "\n";
    echo "  Total: " . $cuotaActual['total'] . "\n";
    echo "  Faltante: " . $cuotaActual['faltante'] . "\n";
    echo "  Fecha: " . $cuotaActual['fecha_vencimiento'] . "\n\n";
}

// Verificar directamente en la vista
echo "=== VERIFICANDO VISTA v_cuotas_pendientes ===\n\n";
$stmt = $pdo->query("SELECT * FROM v_cuotas_pendientes WHERE f17_prestamo = $prestamoCodigo ORDER BY f17_ncuota LIMIT 10");
$cuotasVista = $stmt->fetchAll(PDO::FETCH_ASSOC);

foreach ($cuotasVista as $c) {
    echo "Cuota " . $c['f17_ncuota'] . ": " . $c['estado_calculado'] . " | Total: " . $c['total_original'] . " | Faltante: " . $c['saldo_pendiente'] . "\n";
}

echo "\n=== PRUEBA COMPLETADA ===\n";
