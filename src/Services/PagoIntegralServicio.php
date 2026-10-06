<?php

declare(strict_types=1);

namespace Sacs\Services;

use PDO;
use RuntimeException;
use Sacs\Core\Database;

/**
 * Servicio para procesar Pagos Integrales
 * Combina múltiples operaciones en una sola transacción:
 * - Aportes FONBES
 * - Aportes COOPAM
 * - Sobregiro de aportaciones FONBES
 * - Sobregiro de aportaciones COOPAM
 * - Sobregiro de préstamo (por cada préstamo seleccionado)
 * - Amortización de cuota actual (por cada préstamo seleccionado)
 */
final class PagoIntegralServicio
{
    private PDO $pdo;

    public function __construct()
    {
        $this->pdo = Database::connection();
    }

    /**
     * Obtiene préstamos activos del socio (estado 4)
     */
    public function obtenerPrestamosActivos(string $clienteCodigo): array
    {
        $sql = "SELECT
                    f16_codigo as codigo,
                    f16_importe as importe,
                    f16_cuotasPend as cuotas_pendientes,
                    f16_saldo as saldo,
                    f16_aportacion as sobregiro,
                    f16_fechaDes as fecha_desembolso
                FROM f16_prestamo
                WHERE f16_cliente = :cliente
                AND f16_estado = 4
                ORDER BY f16_codigo ASC";

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute(['cliente' => $clienteCodigo]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Obtiene la cuota actual de un préstamo (la que debe pagarse hoy)
     * Prioridad: vencida hoy -> vencida recientemente -> próxima a vencer
     */
    public function obtenerCuotaActual(int $prestamoCodigo): ?array
    {
        $sql = "SELECT 
                    f17_ncuota as numero_cuota,
                    f17_capital as capital,
                    f17_interes as interes,
                    f17_interesSG as interesSG,
                    f17_mora as mora,
                    f17_moraSG as moraSG,
                    f17_total as total,
                    f17_fecha as fecha_vencimiento,
                    f17_estado as estado
                FROM f17_cuotasprestamo
                WHERE f17_prestamo = :prestamo
                AND f17_estado = 'VIGENTE'
                AND f17_ncuota > 0
                ORDER BY 
                    CASE 
                        WHEN f17_fecha <= CURDATE() THEN 1
                        ELSE 2
                    END,
                    f17_fecha ASC
                LIMIT 1";

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute(['prestamo' => $prestamoCodigo]);
        $cuota = $stmt->fetch(PDO::FETCH_ASSOC);

        return $cuota ?: null;
    }

    /**
     * Calcula pendientes globales del socio con desglose detallado
     */
    public function calcularPendientesGlobales(string $clienteCodigo): array
    {
        $pendientes = [
            'aportesFonbes' => 0,
            'aportesFonbesDetalle' => [],
            'aportesCoopam' => 0,
            'aportesCoopamDetalle' => [],
            'sobregiroFonbes' => 0,
            'sobregiroCoopam' => 0,
        ];

        // Aportes FONBES pendientes (últimos 12 periodos con diferencia)
        $sqlFonbes = "SELECT 
                        f22_periodo as periodo,
                        f22_aportacionP as programado,
                        f22_aportacionD as pagado,
                        f22_diferencia as diferencia
                      FROM f22_aportes
                      WHERE f22_socio = :cliente
                      AND f22_periodo >= DATE_SUB(CURDATE(), INTERVAL 12 MONTH)
                      AND f22_diferencia > 0
                      ORDER BY f22_periodo DESC";
        $stmt = $this->pdo->prepare($sqlFonbes);
        $stmt->execute(['cliente' => $clienteCodigo]);
        $aportesFonbes = $stmt->fetchAll(PDO::FETCH_ASSOC);

        foreach ($aportesFonbes as $aporte) {
            $pendientes['aportesFonbes'] += (float) $aporte['diferencia'];
            $pendientes['aportesFonbesDetalle'][] = $aporte;
        }

        // Aportes COOPAM pendientes (últimos 12 periodos con diferencia)
        $sqlCoopam = "SELECT 
                        f27_periodo as periodo,
                        f27_aportacionP as programado,
                        f27_aportacionD as pagado,
                        f27_diferencia as diferencia
                      FROM f27_aportes_cope
                      WHERE f27_socio = :cliente
                      AND f27_periodo >= DATE_SUB(CURDATE(), INTERVAL 12 MONTH)
                      AND f27_diferencia > 0
                      ORDER BY f27_periodo DESC";
        $stmt = $this->pdo->prepare($sqlCoopam);
        $stmt->execute(['cliente' => $clienteCodigo]);
        $aportesCoopam = $stmt->fetchAll(PDO::FETCH_ASSOC);

        foreach ($aportesCoopam as $aporte) {
            $pendientes['aportesCoopam'] += (float) $aporte['diferencia'];
            $pendientes['aportesCoopamDetalle'][] = $aporte;
        }

        // Sobregiro de aportaciones FONBES (diferencia positiva)
        $sqlSobregiroFonbes = "SELECT 
                                f22_periodo as periodo,
                                f22_diferencia as diferencia
                              FROM f22_aportes
                              WHERE f22_socio = :cliente
                              AND f22_diferencia > 0
                              ORDER BY f22_periodo DESC";
        $stmt = $this->pdo->prepare($sqlSobregiroFonbes);
        $stmt->execute(['cliente' => $clienteCodigo]);
        $sobregiroFonbes = $stmt->fetchAll(PDO::FETCH_ASSOC);

        foreach ($sobregiroFonbes as $item) {
            $pendientes['sobregiroFonbes'] += (float) $item['diferencia'];
        }

        // Sobregiro de aportaciones COOPAM (diferencia positiva)
        $sqlSobregiroCoopam = "SELECT 
                                f27_periodo as periodo,
                                f27_diferencia as diferencia
                              FROM f27_aportes_cope
                              WHERE f27_socio = :cliente
                              AND f27_diferencia > 0
                              ORDER BY f27_periodo DESC";
        $stmt = $this->pdo->prepare($sqlSobregiroCoopam);
        $stmt->execute(['cliente' => $clienteCodigo]);
        $sobregiroCoopam = $stmt->fetchAll(PDO::FETCH_ASSOC);

        foreach ($sobregiroCoopam as $item) {
            $pendientes['sobregiroCoopam'] += (float) $item['diferencia'];
        }

        return $pendientes;
    }

    /**
     * Calcula pendientes por préstamo con desglose detallado
     */
    public function calcularPendientesPorPrestamo(int $prestamoCodigo): array
    {
        $pendientes = [
            'sobregiro' => 0,
            'cuota' => null,
            'cuotaDetalle' => null,
            'subtotal' => 0,
            'prestamoInfo' => null,
        ];

        // Obtener datos del préstamo
        $sql = "SELECT 
                    f16_sobre as sobregiro,
                    f16_saldo as saldo,
                    f16_importe as importe,
                    f16_producto as producto,
                    f16_interes as interes,
                    f16_cuotas as cuotas_total,
                    f16_gastosa as gastos_adm,
                    f16_gastosm as gastos_mora,
                    f16_otros as otros1,
                    f16_otros2 as otros2
                FROM f16_prestamo
                WHERE f16_codigo = :prestamo";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute(['prestamo' => $prestamoCodigo]);
        $prestamo = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($prestamo) {
            $pendientes['sobregiro'] = (float) ($prestamo['sobregiro'] ?? 0);
            $pendientes['prestamoInfo'] = [
                'importe' => (float) ($prestamo['importe'] ?? 0),
                'producto' => $prestamo['producto'] ?? '-',
                'interes' => (float) ($prestamo['interes'] ?? 0),
                'cuotas_total' => $prestamo['cuotas_total'] ?? '-',
                'gastos_adm' => (float) ($prestamo['gastos_adm'] ?? 0),
                'gastos_mora' => (float) ($prestamo['gastos_mora'] ?? 0),
                'otros1' => (float) ($prestamo['otros1'] ?? 0),
                'otros2' => (float) ($prestamo['otros2'] ?? 0),
            ];

            // Obtener cuota actual con desglose
            $cuota = $this->obtenerCuotaActual($prestamoCodigo);
            if ($cuota) {
                $pendientes['cuota'] = $cuota;
                $pendientes['cuotaDetalle'] = [
                    'numero_cuota' => $cuota['numero_cuota'],
                    'capital' => (float) $cuota['capital'],
                    'interes' => (float) $cuota['interes'],
                    'interesSG' => (float) ($cuota['interesSG'] ?? 0),
                    'mora' => (float) ($cuota['mora'] ?? 0),
                    'moraSG' => (float) ($cuota['moraSG'] ?? 0),
                    'total' => (float) $cuota['total'],
                    'fecha_vencimiento' => $cuota['fecha_vencimiento'],
                ];
                $pendientes['subtotal'] = $pendientes['sobregiro'] + (float) $cuota['total'];
            } else {
                $pendientes['subtotal'] = $pendientes['sobregiro'];
            }
        }

        return $pendientes;
    }

    /**
     * Ejecuta el pago integral reutilizando los servicios existentes
     */
    public function ejecutarPagoIntegral(array $datos, array $codigosContables = []): array
    {
        $logFile = dirname(__DIR__, 2) . '/logs/pago_integral.log';

        // Asegurar que el directorio de logs existe
        if (!is_dir(dirname($logFile))) {
            mkdir(dirname($logFile), 0755, true);
        }

        try {
            // Registrar inicio de operación
            $logMessage = sprintf(
                "[%s] INICIO - Cliente: %s, Préstamos: %s\n",
                date('Y-m-d H:i:s'),
                $datos['cliente'],
                implode(', ', $datos['prestamos'])
            );
            file_put_contents($logFile, $logMessage, FILE_APPEND);

            $this->pdo->beginTransaction();

            $movimientosGenerados = [];
            $clienteCodigo = $datos['cliente'];
            $prestamosSeleccionados = $datos['prestamos'];

            // 1. Procesar componentes globales
            $pendientesGlobales = $this->calcularPendientesGlobales($clienteCodigo);

            // Aportes FONBES
            if ($pendientesGlobales['aportesFonbes'] > 0) {
                $logMessage = sprintf("[%s] Procesando Aportes FONBES\n", date('Y-m-d H:i:s'));
                file_put_contents($logFile, $logMessage, FILE_APPEND);

                $aportesServicio = new AportesServicio($this->pdo);
                $datosAportesFonbes = [
                    'cliente' => $clienteCodigo,
                    'anio' => date('Y'),
                    'periodo' => 1,
                    'periodo2' => 12,
                    'monto' => $pendientesGlobales['aportesFonbes'],
                    'formaPago' => $datos['formaPago'],
                    'obs' => $datos['observaciones'] ?? '',
                ];

                if (isset($codigosContables['aportesFonbes'])) {
                    $resultado = $aportesServicio->ejecutarPagoAportesFonbes(
                        $datosAportesFonbes,
                        $codigosContables['aportesFonbes']
                    );
                } else {
                    $resultado = $aportesServicio->ejecutarPagoAportesFonbes($datosAportesFonbes);
                }

                if ($resultado['exito']) {
                    $movimientosGenerados[] = [
                        'tipo' => 'Aportes FONBES',
                        'codigo' => $resultado['codigo_movimiento'],
                    ];
                }
            }

            // Aportes COOPAM
            if ($pendientesGlobales['aportesCoopam'] > 0) {
                $logMessage = sprintf("[%s] Procesando Aportes COOPAM\n", date('Y-m-d H:i:s'));
                file_put_contents($logFile, $logMessage, FILE_APPEND);

                $aportesCoopamServicio = new AportesCoopamServicio($this->pdo);
                $datosAportesCoopam = [
                    'cliente' => $clienteCodigo,
                    'anio' => date('Y'),
                    'periodo' => 1,
                    'periodo2' => 12,
                    'monto' => $pendientesGlobales['aportesCoopam'],
                    'formaPago' => $datos['formaPago'],
                    'obs' => $datos['observaciones'] ?? '',
                ];

                if (isset($codigosContables['aportesCoopam'])) {
                    $resultado = $aportesCoopamServicio->ejecutarPagoAportesCoopam(
                        $datosAportesCoopam,
                        $codigosContables['aportesCoopam']
                    );
                } else {
                    $resultado = $aportesCoopamServicio->ejecutarPagoAportesCoopam($datosAportesCoopam);
                }

                if ($resultado['exito']) {
                    $movimientosGenerados[] = [
                        'tipo' => 'Aportes COOPAM',
                        'codigo' => $resultado['codigo_movimiento'],
                    ];
                }
            }

            // Sobregiro FONBES
            if ($pendientesGlobales['sobregiroFonbes'] > 0) {
                $logMessage = sprintf("[%s] Procesando Sobregiro FONBES\n", date('Y-m-d H:i:s'));
                file_put_contents($logFile, $logMessage, FILE_APPEND);

                $sobregiroFonbesServicio = new PagoSobregiroFonbesServicio($this->pdo);
                $datosSobregiroFonbes = [
                    'cliente' => $clienteCodigo,
                    'monto' => $pendientesGlobales['sobregiroFonbes'],
                    'formaPago' => $datos['formaPago'],
                    'obs' => $datos['observaciones'] ?? '',
                ];

                if (isset($codigosContables['sobregiroFonbes'])) {
                    $resultado = $sobregiroFonbesServicio->ejecutarPagoSobregiroFonbes(
                        $datosSobregiroFonbes,
                        $codigosContables['sobregiroFonbes']
                    );
                } else {
                    $resultado = $sobregiroFonbesServicio->ejecutarPagoSobregiroFonbes($datosSobregiroFonbes);
                }

                if ($resultado['exito']) {
                    $movimientosGenerados[] = [
                        'tipo' => 'Sobregiro FONBES',
                        'codigo' => $resultado['codigo_movimiento'],
                    ];
                }
            }

            // Sobregiro COOPAM
            if ($pendientesGlobales['sobregiroCoopam'] > 0) {
                $logMessage = sprintf("[%s] Procesando Sobregiro COOPAM\n", date('Y-m-d H:i:s'));
                file_put_contents($logFile, $logMessage, FILE_APPEND);

                $sobregiroCoopamServicio = new PagoSobregiroCoopamServicio($this->pdo);
                $datosSobregiroCoopam = [
                    'cliente' => $clienteCodigo,
                    'monto' => $pendientesGlobales['sobregiroCoopam'],
                    'formaPago' => $datos['formaPago'],
                    'obs' => $datos['observaciones'] ?? '',
                ];

                if (isset($codigosContables['sobregiroCoopam'])) {
                    $resultado = $sobregiroCoopamServicio->ejecutarPagoSobregiroCoopam(
                        $datosSobregiroCoopam,
                        $codigosContables['sobregiroCoopam']
                    );
                } else {
                    $resultado = $sobregiroCoopamServicio->ejecutarPagoSobregiroCoopam($datosSobregiroCoopam);
                }

                if ($resultado['exito']) {
                    $movimientosGenerados[] = [
                        'tipo' => 'Sobregiro COOPAM',
                        'codigo' => $resultado['codigo_movimiento'],
                    ];
                }
            }

            // 2. Procesar componentes por préstamo
            foreach ($prestamosSeleccionados as $prestamoCodigo) {
                $pendientesPrestamo = $this->calcularPendientesPorPrestamo((int) $prestamoCodigo);
                $codigosPrestamo = $codigosContables['prestamos'][$prestamoCodigo] ?? [];

                // Sobregiro del préstamo
                if ($pendientesPrestamo['sobregiro'] > 0) {
                    $logMessage = sprintf("[%s] Procesando Sobregiro Préstamo #%d\n", date('Y-m-d H:i:s'), $prestamoCodigo);
                    file_put_contents($logFile, $logMessage, FILE_APPEND);

                    $sobregiroPrestamoServicio = new SobregiroPrestamoServicio();
                    $datosSobregiroPrestamo = [
                        'cliente' => $clienteCodigo,
                        'prestamo' => $prestamoCodigo,
                        'monto' => $pendientesPrestamo['sobregiro'],
                        'sobregiro' => $pendientesPrestamo['sobregiro'],
                        'descuenta' => 0,
                        'gastosAdm' => 0,
                        'gastosMora' => 0,
                        'otros1' => 0,
                        'otros2' => 0,
                        'formaPago' => $datos['formaPago'],
                        'obs' => $datos['observaciones'] ?? '',
                    ];

                    if (isset($codigosPrestamo['sobregiro'])) {
                        $resultado = $sobregiroPrestamoServicio->ejecutarPagoSobregiroPrestamo(
                            $datosSobregiroPrestamo,
                            $codigosPrestamo['sobregiro']
                        );
                    } else {
                        $resultado = $sobregiroPrestamoServicio->ejecutarPagoSobregiroPrestamo($datosSobregiroPrestamo);
                    }

                    if ($resultado['exito']) {
                        $movimientosGenerados[] = [
                            'tipo' => "Sobregiro Préstamo #{$prestamoCodigo}",
                            'codigo' => $resultado['codigo_movimiento'],
                        ];
                    }
                }

                // Amortización de cuota actual
                if ($pendientesPrestamo['cuota'] !== null) {
                    $logMessage = sprintf("[%s] Procesando Amortización Préstamo #%d, Cuota #%d\n", date('Y-m-d H:i:s'), $prestamoCodigo, $pendientesPrestamo['cuota']['numero_cuota']);
                    file_put_contents($logFile, $logMessage, FILE_APPEND);

                    $amortizacionServicio = new AmortizacionServicio($this->pdo);
                    $datosAmortizacion = [
                        'cliente' => $clienteCodigo,
                        'prestamo' => $prestamoCodigo,
                        'cuota' => $pendientesPrestamo['cuota']['numero_cuota'],
                        'monto' => $pendientesPrestamo['cuota']['total'],
                        'gastosAdm' => 0,
                        'gastosMora' => 0,
                        'otros1' => 0,
                        'otros2' => 0,
                        'formaPago' => $datos['formaPago'],
                        'obs' => $datos['observaciones'] ?? '',
                    ];

                    if (isset($codigosPrestamo['amortizacion'])) {
                        $resultado = $amortizacionServicio->ejecutarAmortizacionAEE(
                            $datosAmortizacion,
                            [],
                            $codigosPrestamo['amortizacion']
                        );
                    } else {
                        $resultado = $amortizacionServicio->ejecutarAmortizacionAEE($datosAmortizacion);
                    }

                    if ($resultado['exito']) {
                        $movimientosGenerados[] = [
                            'tipo' => "Amortización Préstamo #{$prestamoCodigo}",
                            'codigo' => $resultado['codigo_movimiento'],
                        ];
                    }
                }
            }

            $this->pdo->commit();

            // Registrar éxito
            $logMessage = sprintf(
                "[%s] EXITO - Cliente: %s, Movimientos: %d\n",
                date('Y-m-d H:i:s'),
                $clienteCodigo,
                count($movimientosGenerados)
            );
            file_put_contents($logFile, $logMessage, FILE_APPEND);

            return [
                'exito' => true,
                'movimientos' => $movimientosGenerados,
                'mensaje' => 'Pago integral procesado correctamente',
            ];

        } catch (RuntimeException $e) {
            // Rollback ante error
            if ($this->pdo->inTransaction()) {
                $this->pdo->rollBack();
            }

            // Registrar error
            $logMessage = sprintf(
                "[%s] ERROR - Cliente: %s, Error: %s\n",
                date('Y-m-d H:i:s'),
                $datos['cliente'] ?? 'N/A',
                $e->getMessage()
            );
            file_put_contents($logFile, $logMessage, FILE_APPEND);

            return [
                'exito' => false,
                'mensaje' => $e->getMessage(),
            ];
        }
    }
}
