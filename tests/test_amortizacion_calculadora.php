<?php

declare(strict_types=1);

require dirname(__DIR__) . '/config/bootstrap.php';

use Sacs\Services\AmortizacionCalculadora;

// Datos reales del movimiento 122013090
$prestamoReal = [
    'f16_codigo' => 43232,
    'f16_saldo' => 9166.10, // Después del pago
    'f16_cuotasPend' => 22, // Después del pago
    'f16_sobre' => 365.71, // Sobregiro existente antes del pago
    'f16_gastosa' => 227.48, // Gastos administrativos antes del pago
    'f16_gastosm' => 0,
    'f16_otros' => 0,
    'f16_otros2' => 0,
];

$cuotaReal = [
    'f17_prestamo' => 43232,
    'f17_ncuota' => 1,
    'f17_fecha' => '2026-09-10',
    'f17_capital' => 359.86,
    'f17_interes' => 124.99,
    'f17_total' => 484.86,
    'f17_saldo' => 9640.13,
    'f17_estado' => 'VIGENTE',
];

$movimientoReal = [
    'f15_codigo' => 122013090,
    'f15_fecha' => '2026-08-26',
    'f15_operacion' => 2,
    'f15_cliente' => 34584,
    'f15_prestamo' => 43232,
    'f15_maprestamo' => 359.87,
    'f15_gastosa' => 233.32,
    'f15_gastosm' => 0,
    'f15_gastos1' => 0,
    'f15_gastos2' => 0,
];

// Valores del formulario (simulados según el movimiento)
$formValores = [
    'gastosAdm' => 233.32,
    'gastosMora' => 0,
    'otros1' => 0,
    'otros2' => 0,
];

$montoPagado = 593.19; // Total del movimiento

echo "=== PRUEBA DE CARACTERIZACIÓN: AMORTIZACIÓN AEE ===\n\n";
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

echo "--- DATOS DEL PRÉSTAMO DESPUÉS DEL PAGO ---\n";
echo "Saldo: {$prestamoReal['f16_saldo']}\n";
echo "Cuotas pendientes: {$prestamoReal['f16_cuotasPend']}\n";
echo "Sobregiro: {$prestamoReal['f16_sobre']}\n\n";

echo "--- DATOS DE LA CUOTA ---\n";
echo "Capital: {$cuotaReal['f17_capital']}\n";
echo "Interés: {$cuotaReal['f17_interes']}\n";
echo "Total: {$cuotaReal['f17_total']}\n";
echo "Saldo de cuota: {$cuotaReal['f17_saldo']}\n\n";

// Necesitamos reconstruir el estado ANTES del pago para el cálculo
$prestamoAntes = $prestamoReal;
$prestamoAntes['f16_cuotasPend'] = 23; // Antes del pago
$prestamoAntes['f16_saldo'] = 9166.10 + 359.86; // Saldo aproximado antes del pago

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

echo "Metadata:\n";
echo "  ind: {$resultado['metadata']['ind']}\n";
echo "  Sobregiro calculado: {$resultado['metadata']['sobregiro_calculado']}\n";
echo "  Sobregiro existente: {$resultado['metadata']['sobregiro_existente']}\n";
echo "  Cuota mensual: {$resultado['metadata']['cuota_mensual']}\n";
echo "  Amortización cuota: {$resultado['metadata']['amortizacion_cuota']}\n\n";

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
