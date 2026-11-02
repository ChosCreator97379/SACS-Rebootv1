<?php

declare(strict_types=1);

namespace Sacs\Services;

/**
 * Calculadora pura de amortización AEE basada en CrearCajaAction.registrar()
 * 
 * Reproduce exactamente el orden de aplicación legacy:
 * sobregiro existente → gastos administrativos → mora → otros1 → otros2 → capital
 */
final class AmortizacionCalculadora
{
    /**
     * Calcula la distribución de un pago de amortización AEE
     * 
     * @param array $prestamo Datos del préstamo (f16)
     * @param array $cuota Datos de la cuota (f17)
     * @param array $formValores Valores del formulario (gastosAdm, gastosMora, otros1, otros2)
     * @param float $montoPagado Monto recibido en caja
     * @return array Resultado del cálculo con distribución y actualizaciones
     */
    public static function calcularAmortizacionAEE(
        array $prestamo,
        array $cuota,
        array $formValores,
        float $montoPagado
    ): array {
        // Inicializar variables según legacy
        $montoA = $montoPagado;
        $sobregiro = 0.0;
        $j = 0.0; // Monto manejado en sobregiro
        $ind = 0; // Indicador de manejo de sobregiro
        
        // Datos del préstamo
        $s = (float) ($prestamo['f16_sobre'] ?? 0); // Sobregiro existente
        $saldo = (float) ($prestamo['f16_saldo'] ?? 0);
        $cuotasPend = (int) ($prestamo['f16_cuotasPend'] ?? 0);
        
        // Datos de la cuota
        $cuotaMensual = (float) ($cuota['f17_total'] ?? 0);
        $amortizacion = (float) ($cuota['f17_capital'] ?? 0);
        $resto = (float) ($cuota['f17_saldo'] ?? 0);
        
        // Valores del formulario
        $gaOriginal = (float) ($formValores['gastosAdm'] ?? 0);
        $gmOriginal = (float) ($formValores['gastosMora'] ?? 0);
        $o1Original = (float) ($formValores['otros1'] ?? 0);
        $o2Original = (float) ($formValores['otros2'] ?? 0);
        
        $ga = $gaOriginal;
        $gm = $gmOriginal;
        $o1 = $o1Original;
        $o2 = $o2Original;
        
        // Calcular sobregiro del pago (línea 1306 de CrearCajaAction)
        $sobregiro = $cuotaMensual - $montoA;
        
        // Manejo del sobregiro existente (líneas 1308-1336)
        if ($sobregiro > 0) {
            if ($s > 0) {
                if ($montoA == $s) {
                    // Pago exacto del sobregiro existente
                    $amortizapAportacion = 0;
                    $montoA = 0;
                    $j = $s;
                    $ind = 1;
                } elseif ($montoA < $s) {
                    // Pago parcial del sobregiro existente
                    $j = $s - $montoA;
                    $amortizapAportacion = $j;
                    $j = $montoA;
                    $montoA = 0;
                    $ind = 1;
                }
            }
            if ($s == 0) {
                // Crear nuevo sobregiro
                $amortizapAportacion = $sobregiro;
            }
        } else {
            $amortizapAportacion = 0;
        }
        
        // Variables para gastos pendientes
        $gami = 0.0; // Gastos administrativos pendientes
        $gamo = 0.0; // Mora pendiente
        $otros1 = 0.0;
        $otros2 = 0.0;
        $amortiza = 0.0; // Capital a amortizar
        
        // Aplicación del dinero en orden de prioridad (líneas 1358-1453)
        
        // 1. Gastos administrativos (líneas 1358-1379)
        if ($ga > 0) {
            if ($montoA > $ga) {
                $montoA = $montoA - $ga;
                if ($montoA > 0) {
                    $gami = 0;
                } else {
                    $gami = $ga - $montoA;
                }
            } else {
                $gami = $ga - $montoA;
                $ga = $montoA;
                $montoA = 0;
            }
        }
        
        // 2. Mora (líneas 1385-1408)
        if ($gm > 0) {
            if ($montoA > $gm) {
                $montoA = $montoA - $gm;
                if ($montoA > 0) {
                    $gamo = 0;
                } else {
                    $gamo = $gm - $montoA;
                }
            } else {
                $gamo = $gm - $montoA;
                $montoA = 0;
            }
        }
        
        // 3. Otros 1 (líneas 1413-1431)
        if ($o1 > 0) {
            if ($montoA > $o1) {
                $montoA = $montoA - $o1;
                if ($montoA > 0) {
                    $otros1 = 0;
                } else {
                    $otros1 = $o1 - $montoA;
                }
            } else {
                $otros1 = $o1 - $montoA;
                $montoA = 0;
            }
        }
        
        // 4. Otros 2 (líneas 1434-1453)
        if ($o2 > 0) {
            if ($montoA > $o2) {
                $montoA = $montoA - $o2;
                if ($montoA > 0) {
                    $otros2 = 0;
                } else {
                    $otros2 = $o2 - $montoA;
                }
            } else {
                $otros2 = $o2 - $montoA;
                $montoA = 0;
            }
        }
        
        // 5. Capital (línea 1458)
        $amortiza = $montoA;
        
        // Monto del movimiento según ind (líneas 1465-1469)
        if ($ind == 0) {
            $montoMovimiento = $montoA;
        } else {
            $montoMovimiento = $j;
        }
        
        // Cálculo del nuevo saldo (líneas 1471-1510)
        $r = 0.0;
        if ($saldo > $amortiza) {
            if ($sobregiro == 0) {
                $r = $saldo - $amortizacion;
            }
            if ($sobregiro > 0) {
                $r = $saldo - $amortizacion;
                $r = $r + $sobregiro;
                if ($r < $resto) {
                    $amortizapAportacion = 0;
                } else {
                    $t = $resto - $r;
                    $r = $resto;
                    $sobregiro = -$t;
                    $amortizapAportacion = $sobregiro;
                }
            }
            if ($sobregiro < 0) {
                // Cuando hay exceso de pago (sobregiro negativo), el legacy
                // RESTA el sobregiro negativo del saldo (líneas 1500-1507)
                // pero NO actualiza el campo de aportación del préstamo
                $sobregiro = -$sobregiro;
                $r = ($saldo - $amortizacion) - $sobregiro;
                // NO actualizar amortizapAportacion cuando hay exceso de pago
                // (el legacy Java no lo hace en este caso)
            }
        } else {
            // Cuando el saldo es menor o igual a la amortización (préstamo cancelado)
            $r = 0;
        }

        // Calcular cuotas restantes según el monto pagado
        $saldoCuota = (float) ($cuota['f17_saldo'] ?? 0);
        $totalCuotaProgramada = (float) ($cuota['f17_total'] ?? 0);
        $totalPagado = $amortiza + $ga + $gm + $o1 + $o2;
        $escenario = self::determinarEscenario($totalPagado, $totalCuotaProgramada, $cuotaMensual);

        if ($escenario === 'completo' || $escenario === 'mas') {
            // Pago completo o mayor: reducir cuotas pendientes
            $nuevasCuotasPend = $cuotasPend - 1;
            
            // Si pagó más, calcular cuántas cuotas adicionales se pueden eliminar
            if ($escenario === 'mas' && $r > 0) {
                $exceso = $amortiza - $saldoCuota;
                $cuotasEliminadas = floor($exceso / $amortizacion);
                $nuevasCuotasPend = max(0, $nuevasCuotasPend - $cuotasEliminadas);
            }
        } elseif ($escenario === 'menos') {
            // Pago parcial: mantener cuotas pendientes (no reducir)
            $nuevasCuotasPend = $cuotasPend;
        } else {
            // Fallback: comportamiento original
            if ($amortiza >= $saldoCuota) {
                $nuevasCuotasPend = $cuotasPend - 1;
            } else {
                $nuevasCuotasPend = $cuotasPend;
            }
        }
        
        // Retornar resultado estructurado
        return [
            'distribucion' => [
                'monto_pagado' => $montoPagado,
                'capital' => $amortiza,
                'gastos_administrativos' => $ga,
                'mora' => $gm,
                'otros1' => $o1,
                'otros2' => $o2,
                'sobregiro_manejado' => $j,
                'total_distribuido' => $amortiza + $ga + $gm + $o1 + $o2,
            ],
            'movimiento' => [
                'monto' => $montoMovimiento,
                'gastosa' => $ga, // Valor ajustado por sobregiro (línea 101)
                'gastosm' => $gm, // Valor ajustado por sobregiro (línea 116)
                'gastos1' => $o1, // Valor ajustado por sobregiro (línea 131)
                'gastos2' => $o2, // Valor ajustado por sobregiro (línea 146)
            ],
            'prestamo_actualizado' => [
                'codigo' => $prestamo['f16_codigo'],
                'cuotas_pendientes' => $nuevasCuotasPend,
                'saldo' => $r,
                'gastosa' => $gami,
                'gastosm' => $gamo,
                'otros' => $otros1,
                'otros2' => $otros2,
                'sobre' => $amortizapAportacion,
                'fechaUP' => $cuota['f17_fecha'],
            ],
            'metadata' => [
                'ind' => $ind,
                'sobregiro_calculado' => $sobregiro,
                'sobregiro_existente' => $s,
                'cuota_mensual' => $cuotaMensual,
                'amortizacion_cuota' => $amortiza,
            ],
            'cuota_actualizada' => [
                'saldo' => max(0, $saldoCuota - $amortiza),
                'capital' => min($amortiza, $saldoCuota),
                'interes' => 0,
                'total' => min($amortiza, $saldoCuota),
            ],
            'escenario' => $escenario, // Agregar el escenario detectado
        ];
    }
    
    /**
     * Determina el escenario de pago según el monto pagado vs cuota programada
     * 
     * @param float $montoPagadoCapital Monto de capital pagado
     * @param float $saldoCuota Saldo de la cuota
     * @param float $cuotaProgramada Cuota mensual programada
     * @return string 'completo', 'menos', 'mas', o 'parcial'
     */
    private static function determinarEscenario(float $montoPagadoCapital, float $totalCuotaProgramada, float $cuotaMensual): string
    {
        // Tolerancia de 0.01 para errores de redondeo
        $tolerancia = 0.01;
        
        if (abs($montoPagadoCapital - $totalCuotaProgramada) <= $tolerancia) {
            return 'completo'; // Pagó exacto
        } elseif ($montoPagadoCapital > $totalCuotaProgramada + $tolerancia) {
            return 'mas'; // Pagó más
        } elseif ($montoPagadoCapital < $saldoCuota - $tolerancia) {
            return 'menos'; // Pagó menos
        }
        
        return 'parcial'; // Pago parcial cercano al exacto
    }
    
    /**
     * Valida que el cálculo sea consistente con los datos legacy
     * 
     * @param array $resultado Resultado del cálculo
     * @param array $movimientoReal Movimiento real de la base
     * @param array $prestamoReal Préstamo real de la base
     * @return array Diferencias encontradas
     */
    public static function validarConLegacy(
        array $resultado,
        array $movimientoReal,
        array $prestamoReal
    ): array {
        $diferencias = [];
        
        // Validar movimiento
        if (abs($resultado['movimiento']['monto'] - (float)($movimientoReal['f15_maprestamo'] ?? 0)) > 0.01) {
            $diferencias[] = sprintf(
                'Monto movimiento: calculado=%.2f, real=%.2f',
                $resultado['movimiento']['monto'],
                $movimientoReal['f15_maprestamo'] ?? 0
            );
        }
        
        if (abs($resultado['movimiento']['gastosa'] - (float)($movimientoReal['f15_gastosa'] ?? 0)) > 0.01) {
            $diferencias[] = sprintf(
                'Gastos administrativos: calculado=%.2f, real=%.2f',
                $resultado['movimiento']['gastosa'],
                $movimientoReal['f15_gastosa'] ?? 0
            );
        }
        
        // Validar préstamo
        if (abs($resultado['prestamo_actualizado']['saldo'] - (float)($prestamoReal['f16_saldo'] ?? 0)) > 0.01) {
            $diferencias[] = sprintf(
                'Saldo préstamo: calculado=%.2f, real=%.2f',
                $resultado['prestamo_actualizado']['saldo'],
                $prestamoReal['f16_saldo'] ?? 0
            );
        }
        
        if ($resultado['prestamo_actualizado']['cuotas_pendientes'] != (int)($prestamoReal['f16_cuotasPend'] ?? 0)) {
            $diferencias[] = sprintf(
                'Cuotas pendientes: calculado=%d, real=%d',
                $resultado['prestamo_actualizado']['cuotas_pendientes'],
                $prestamoReal['f16_cuotasPend'] ?? 0
            );
        }
        
        return $diferencias;
    }
}
