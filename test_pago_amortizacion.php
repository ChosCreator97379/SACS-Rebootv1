<?php
require 'config/bootstrap.php';

$pdo = new PDO(
    'mysql:host=' . ($_ENV['DB_HOST'] ?? 'localhost') . ';dbname=' . ($_ENV['DB_NAME'] ?? 'sacs') . ';charset=utf8mb4',
    $_ENV['DB_USER'] ?? 'root',
    $_ENV['DB_PASS'] ?? '',
    [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
);

echo "=== PRUEBA DE PAGO DE AMORTIZACIÓN ===\n\n";

// Datos de prueba (préstamo 36614)
$datosPrueba = [
    'cliente' => '21210', // Cliente del préstamo 36614
    'prestamo' => 36614,
    'cuota' => 44, // Cuota VIGENTE
    'monto' => 126.81, // Monto de la cuota
    'amortiza' => 100.00, // Capital a pagar
    'gastosAdm' => 10.00,
    'gastosMora' => 0,
    'otros1' => 0,
    'otros2' => 0,
    'fecha' => date('Y-m-d'),
    'formaPago' => 'EFECTIVO',
    'usuario' => 'PRUEBA',
    'obs' => 'Prueba de amortización con nueva estructura',
];

echo "Datos de prueba:\n";
echo "  Préstamo: " . $datosPrueba['prestamo'] . "\n";
echo "  Cuota: " . $datosPrueba['cuota'] . "\n";
echo "  Monto: " . $datosPrueba['monto'] . "\n";
echo "  Amortiza: " . $datosPrueba['amortiza'] . "\n\n";

// Verificar estado antes del pago
echo "=== ESTADO ANTES DEL PAGO ===\n\n";

// Verificar f15_aplicaciones_cuotas antes
$stmt = $pdo->query("SELECT COUNT(*) as total FROM f15_aplicaciones_cuotas WHERE f17_prestamo = 36614");
$antes = $stmt->fetch();
echo "Aplicaciones en f15_aplicaciones_cuotas: " . $antes['total'] . "\n";

// Verificar f17_cuotasprestamo_nuevo antes
$stmt = $pdo->query("SELECT * FROM f17_cuotasprestamo_nuevo WHERE f17_prestamo = 36614 AND f17_ncuota = 44");
$cuotaAntes = $stmt->fetch();
if ($cuotaAntes) {
    echo "Cuota 5 en f17_cuotasprestamo_nuevo: Estado=" . $cuotaAntes['f17_estado'] . ", Total=" . $cuotaAntes['f17_total'] . "\n";
}

// Verificar f17_cuotasprestamo antes (debe mantenerse igual)
$stmt = $pdo->query("SELECT * FROM f17_cuotasprestamo WHERE f17_prestamo = 36614 AND f17_ncuota = 44");
$cuotaOriginalAntes = $stmt->fetch();
if ($cuotaOriginalAntes) {
    echo "Cuota 5 en f17_cuotasprestamo (original): Estado=" . $cuotaOriginalAntes['f17_estado'] . ", Total=" . $cuotaOriginalAntes['f17_total'] . "\n";
}

echo "\n=== EJECUTANDO AMORTIZACIÓN ===\n\n";

// Ejecutar amortización usando el servicio
try {
    $amortizacionServicio = new \Sacs\Services\AmortizacionServicio($pdo);
    $resultado = $amortizacionServicio->ejecutarAmortizacionAEE($datosPrueba);

    if ($resultado['exito']) {
        echo "✅ Amortización exitosa\n";
        echo "   Código movimiento: " . $resultado['codigo_movimiento'] . "\n\n";
    } else {
        echo "❌ Amortización fallida: " . $resultado['error'] . "\n\n";
        exit(1);
    }
} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n\n";
    exit(1);
}

// Verificar estado después del pago
echo "=== ESTADO DESPUÉS DEL PAGO ===\n\n";

// Verificar f15_aplicaciones_cuotas después
$stmt = $pdo->query("SELECT COUNT(*) as total FROM f15_aplicaciones_cuotas WHERE f17_prestamo = 36614");
$despues = $stmt->fetch();
echo "Aplicaciones en f15_aplicaciones_cuotas: " . $despues['total'] . " (antes: " . $antes['total'] . ")\n";

if ($despues['total'] > $antes['total']) {
    echo "✅ Se registró nueva aplicación\n\n";

    // Mostrar la última aplicación
    $stmt = $pdo->query("SELECT * FROM f15_aplicaciones_cuotas WHERE f17_prestamo = 36614 ORDER BY f17_codigo DESC LIMIT 1");
    $aplicacion = $stmt->fetch();
    echo "Última aplicación:\n";
    echo "  f15_codigo: " . $aplicacion['f15_codigo'] . "\n";
    echo "  f17_ncuota: " . $aplicacion['f17_ncuota'] . "\n";
    echo "  f17_capital_aplicado: " . $aplicacion['f17_capital_aplicado'] . "\n";
    echo "  f17_total_aplicado: " . $aplicacion['f17_total_aplicado'] . "\n";
} else {
    echo "❌ No se registró aplicación\n\n";
}

// Verificar f17_cuotasprestamo_nuevo después
$stmt = $pdo->query("SELECT * FROM f17_cuotasprestamo_nuevo WHERE f17_prestamo = 36614 AND f17_ncuota = 44");
$cuotaDespues = $stmt->fetch();
if ($cuotaDespues) {
    echo "\nCuota 5 en f17_cuotasprestamo_nuevo: Estado=" . $cuotaDespues['f17_estado'] . ", Total=" . $cuotaDespues['f17_total'] . "\n";
    if ($cuotaAntes && $cuotaAntes['f17_estado'] !== $cuotaDespues['f17_estado']) {
        echo "✅ Estado cambió de " . $cuotaAntes['f17_estado'] . " a " . $cuotaDespues['f17_estado'] . "\n";
    }
}

// Verificar f17_cuotasprestamo después (NO debe cambiar)
$stmt = $pdo->query("SELECT * FROM f17_cuotasprestamo WHERE f17_prestamo = 36614 AND f17_ncuota = 44");
$cuotaOriginalDespues = $stmt->fetch();
if ($cuotaOriginalDespues) {
    echo "Cuota 5 en f17_cuotasprestamo (original): Estado=" . $cuotaOriginalDespues['f17_estado'] . ", Total=" . $cuotaOriginalDespues['f17_total'] . "\n";
    if ($cuotaOriginalAntes && $cuotaOriginalAntes['f17_estado'] === $cuotaOriginalDespues['f17_estado']) {
        echo "✅ Tabla original NO se modificó (correcto)\n";
    } else {
        echo "❌ Tabla original se modificó (incorrecto)\n";
    }
}

// Verificar vista v_cuotas_pendientes
echo "\n=== VERIFICANDO VISTA v_cuotas_pendientes ===\n\n";
$stmt = $pdo->query("SELECT * FROM v_cuotas_pendientes WHERE f17_prestamo = 36614 AND f17_ncuota = 44");
$vista = $stmt->fetch();
if ($vista) {
    echo "Vista para cuota 5:\n";
    echo "  estado_calculado: " . $vista['estado_calculado'] . "\n";
    echo "  total_original: " . $vista['total_original'] . "\n";
    echo "  total_aplicado: " . $vista['total_aplicado'] . "\n";
    echo "  saldo_pendiente: " . $vista['saldo_pendiente'] . "\n";
}

echo "\n=== PRUEBA COMPLETADA ===\n";
