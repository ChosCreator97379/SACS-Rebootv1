<?php

declare(strict_types=1);

require dirname(__DIR__) . '/config/bootstrap.php';

use Sacs\Services\AmortizacionCalculadora;

// Caso con otros1 y otros2: Movimiento 122012265
$prestamoReal = [
    'f16_codigo' => 42443,
    'f16_saldo' => 0.00, // Después del pago (préstamo cancelado)
    'f16_cuotasPend' => 0, // Después del pago
    'f16_sobre' => 0, // Sobregiro después del pago
    'f16_gastosa' => 0, // Gastos administrativos después del pago
    'f16_gastosm' => 0, // Mora después del pago
    'f16_otros' => 0, // Otros1 después del pago
    'f16_otros2' => 0, // Otros2 después del pago
];

$cuotaReal = [
    'f17_prestamo' => 42443,
    'f17_ncuota' => 1,
    'f17_fecha' => '2026-08-20',
    'f17_capital' => 55.89,
    'f17_interes' => 0,
    'f17_total' => 55.89,
    'f17_saldo' => 0,
    'f17_estado' => 'VIGENTE',
];

$movimientoReal = [
    'f15_codigo' => 122012265,
    'f15_fecha' => '2026-08-20',
    'f15_operacion' => 2,
    'f15_cliente' => 349135,
    'f15_prestamo' => 42443,
    'f15_maprestamo' => 55.89,
    'f15_gastosa' => 145.72,
    'f15_gastosm' => 0,
    'f15_gastos1' => 300,
    'f15_gastos2' => 300,
];

$formValores = [
    'gastosAdm' => 145.72,
    'gastosMora' => 0,
    'otros1' => 300,
    'otros2' => 300,
];

$montoPagado = 801.61; // Total del movimiento: 55.89 + 145.72 + 300 + 300

echo "=== PRUEBA DE CARACTERIZACIÓN: AMORTIZACIÓN AEE CON OTROS1 Y OTROS2 ===\n\n";
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
$prestamoAntes['f16_cuotasPend'] = 1; // Antes del pago
$prestamoAntes['f16_saldo'] = 55.89; // Saldo antes del pago
$prestamoAntes['f16_gastosa'] = 145.72; // Gastos administrativos antes del pago
$prestamoAntes['f16_otros'] = 300; // Otros1 antes del pago
$prestamoAntes['f16_otros2'] = 300; // Otros2 antes del pago

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
