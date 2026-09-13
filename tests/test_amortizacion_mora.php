<?php

declare(strict_types=1);

require dirname(__DIR__) . '/config/bootstrap.php';

use Sacs\Services\AmortizacionCalculadora;

// Caso con mora: Movimiento 122012862
$prestamoReal = [
    'f16_codigo' => 38760,
    'f16_saldo' => 4920.83, // Después del pago
    'f16_cuotasPend' => 15, // Después del pago
    'f16_sobre' => 2600.85, // Sobregiro existente antes del pago
    'f16_gastosa' => 94.02, // Gastos administrativos antes del pago
    'f16_gastosm' => 15, // Mora antes del pago
    'f16_otros' => 0,
    'f16_otros2' => 0,
];

$cuotaReal = [
    'f17_prestamo' => 38760,
    'f17_ncuota' => 1,
    'f17_fecha' => '2024-01-20',
    'f17_capital' => 199.29,
    'f17_interes' => 162.5,
    'f17_total' => 361.79,
    'f17_saldo' => 12800.7,
    'f17_estado' => 'VIGENTE',
];

$movimientoReal = [
    'f15_codigo' => 122012862,
    'f15_fecha' => '2026-08-20',
    'f15_operacion' => 2,
    'f15_cliente' => 8004897,
    'f15_prestamo' => 38760,
    'f15_maprestamo' => 756.24,
    'f15_gastosa' => 103.46,
    'f15_gastosm' => 10,
    'f15_gastos1' => 0,
    'f15_gastos2' => 0,
];

$formValores = [
    'gastosAdm' => 103.46,
    'gastosMora' => 10,
    'otros1' => 0,
    'otros2' => 0,
];

$montoPagado = 869.70; // Total del movimiento: 756.24 + 103.46 + 10

echo "=== PRUEBA DE CARACTERIZACIÓN: AMORTIZACIÓN AEE CON MORA ===\n\n";
echo "Movimiento real: f15_codigo = {$movimientoReal['f15_codigo']}\n";
echo "Préstamo: f16_codigo = {$prestamoReal['f16_codigo']}\n";
echo "Cuota: f17_ncuota = {$cuotaReal['f17_ncuota']}\n\n";

echo "--- DATOS DEL MOVIMIENTO REAL ---\n";
echo "Capital pagado: {$movimientoReal['f15_maprestamo']}\n";
echo "Gastos administrativos: {$movimientoReal['f15_gastosa']}\n";
echo "Mora: {$movimientoReal['f15_gastosm']}\n";
echo "Otros1: {$movimientoReal['f15_gastos1']}\n";
echo "Otros2: {$movimientoReal['f15_gastos2']}\n";
echo "Total pagado: " . ($movimientoReal['f15_maprestamo'] + $movimientoReal['f15_gastosa'] + $movimientoReal['f15_gastosm'] + $movimientoReal['f15_gastos1'] + $movimientoReal['f15_gastos2']) . "\n\n";

// Reconstruir estado ANTES del pago
$prestamoAntes = $prestamoReal;
$prestamoAntes['f16_cuotasPend'] = 16; // Antes del pago
$prestamoAntes['f16_saldo'] = 4920.83 + 756.24; // Saldo aproximado antes del pago

echo "--- EJECUTANDO CÁLCULO ---\n";
$resultado = AmortizacionCalculadora::calcularAmortizacionAEE(
    $prestamoAntes,
    $cuotaReal,
    $formValores,
    $montoPagado
);

echo "\n--- RESULTADO DEL CÁLCULO ---\n";
echo "Distribución:\n";
echo "  Capital: {$resultado['distribucion']['capital']}\n";
echo "  Gastos administrativos: {$resultado['distribucion']['gastos_administrativos']}\n";
echo "  Mora: {$resultado['distribucion']['mora']}\n";
echo "  Otros1: {$resultado['distribucion']['otros1']}\n";
echo "  Otros2: {$resultado['distribucion']['otros2']}\n";
echo "  Total distribuido: {$resultado['distribucion']['total_distribuido']}\n\n";

echo "Movimiento calculado:\n";
echo "  Monto: {$resultado['movimiento']['monto']}\n";
echo "  Gastos administrativos: {$resultado['movimiento']['gastosa']}\n";
echo "  Mora: {$resultado['movimiento']['gastosm']}\n";
echo "  Otros1: {$resultado['movimiento']['gastos1']}\n";
echo "  Otros2: {$resultado['movimiento']['gastos2']}\n\n";

echo "Préstamo actualizado:\n";
echo "  Cuotas pendientes: {$resultado['prestamo_actualizado']['cuotas_pendientes']}\n";
echo "  Saldo: {$resultado['prestamo_actualizado']['saldo']}\n";
echo "  Gastos administrativos pendientes: {$resultado['prestamo_actualizado']['gastosa']}\n";
echo "  Mora pendiente: {$resultado['prestamo_actualizado']['gastosm']}\n";
echo "  Sobregiro: {$resultado['prestamo_actualizado']['sobre']}\n\n";

echo "--- VALIDACIÓN CON LEGACY ---\n";
$diferencias = AmortizacionCalculadora::validarConLegacy(
    $resultado,
    $movimientoReal,
    $prestamoReal
);

if (empty($diferencias)) {
    echo "✅ Cálculo VALIDADO: coincide con datos legacy\n";
} else {
    echo "❌ Cálculo NO coincide con datos legacy:\n";
    foreach ($diferencias as $diferencia) {
        echo "  - $diferencia\n";
    }
}
