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
     * Las cuotas pendientes se cuentan como cuotas VIGENTE del cronograma nuevo
     */
    public function obtenerPrestamosActivos(string $clienteCodigo): array
    {
        $sql = "SELECT
                    p.f16_codigo as codigo,
                    p.f16_importe as importe,
                    (SELECT COUNT(*) FROM f17_cuotasprestamo_nuevo WHERE f17_prestamo = p.f16_codigo AND f17_estado = 'VIGENTE') as cuotas_pendientes,
                    p.f16_saldo as saldo,
                    p.f16_sobre as sobregiro,
                    p.f16_fechaDes as fecha_desembolso
                FROM f16_prestamo p
                WHERE p.f16_cliente = :cliente
                AND p.f16_estado = 4
                ORDER BY p.f16_codigo ASC";

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute(['cliente' => $clienteCodigo]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Obtiene cuotas parciales de un préstamo (estado PARCIAL)
     * Usa la vista v_cuotas_pendientes para calcular el faltante automáticamente
     */
    public function obtenerCuotasParciales(int $prestamoCodigo): array
    {
        $sql = "SELECT
                    f17_ncuota as numero_cuota,
                    capital_original as capital,
                    interes_original as interes,
                    total_original as total,
                    f17_fecha as fecha_vencimiento,
                    estado_calculado as estado,
                    saldo_pendiente as faltante
                FROM v_cuotas_pendientes
                WHERE f17_prestamo = :prestamo
                AND estado_calculado = 'PARCIAL'
                ORDER BY f17_ncuota ASC";

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute(['prestamo' => $prestamoCodigo]);
        $cuotas = $stmt->fetchAll(PDO::FETCH_ASSOC);

        return $cuotas;
    }

    /**
     * Obtiene cuotas vigentes y parciales de un préstamo (para selección en pago integral)
     * Para cuotas PARCIAL, usa el total del cronograma nuevo y mantiene el interés original
     */
    public function obtenerCuotasSeleccionables(int $prestamoCodigo): array
    {
        $sql = "SELECT
                    f17_ncuota as numero_cuota,
                    f17_fecha as fecha_vencimiento,
                    f17_capital as capital_original,
                    f17_interes as interes_original,
                    f17_total as total_original,
                    f17_total as total_nuevo,
                    f17_saldo as saldo,
                    f17_estado as estado
                FROM f17_cuotasprestamo_nuevo
                WHERE f17_prestamo = :prestamo
                AND f17_estado IN ('VIGENTE', 'PARCIAL')
                ORDER BY f17_ncuota ASC";

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute(['prestamo' => $prestamoCodigo]);
        $cuotas = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Para cuotas PARCIAL, usar el total del cronograma nuevo y calcular el interés faltante
        foreach ($cuotas as &$cuota) {
            if ($cuota['estado'] === 'PARCIAL') {
                // Obtener interés ya aplicado a esta cuota
                $sqlInteres = "SELECT SUM(f17_interes_aplicado) as interes_aplicado
                               FROM f15_aplicaciones_cuotas
                               WHERE f17_prestamo = :prestamo AND f17_ncuota = :cuota";
                $stmtInteres = $this->pdo->prepare($sqlInteres);
                $stmtInteres->execute(['prestamo' => $prestamoCodigo, 'cuota' => $cuota['numero_cuota']]);
                $aplicacionInteres = $stmtInteres->fetch(PDO::FETCH_ASSOC);
                $interesYaAplicado = (float) ($aplicacionInteres['interes_aplicado'] ?? 0);

                // Calcular interés faltante
                $interesFaltante = max(0, $cuota['interes_original'] - $interesYaAplicado);
                $cuota['interes'] = $interesFaltante;

                // Calcular el capital faltante como: total del cronograma nuevo - interés faltante
                $cuota['capital'] = max(0, $cuota['total_nuevo'] - $interesFaltante);

                // Usar el total del cronograma nuevo
                $cuota['total'] = $cuota['total_nuevo'];
            } else {
                // Para cuotas VIGENTE, usar valores originales
                $cuota['capital'] = $cuota['capital_original'];
                $cuota['interes'] = $cuota['interes_original'];
                $cuota['total'] = $cuota['total_original'];
            }
        }

        return $cuotas;
    }

    /**
     * Obtiene la cuota actual de un préstamo (la que debe pagarse hoy)
     * Usa la vista v_cuotas_pendientes
     */
    public function obtenerCuotaActual(int $prestamoCodigo): ?array
    {
        $sql = "SELECT
                    f17_ncuota as numero_cuota,
                    capital_original as capital,
                    interes_original as interes,
                    total_original as total,
                    f17_fecha as fecha_vencimiento,
                    estado_calculado as estado,
                    saldo_pendiente as faltante
                FROM v_cuotas_pendientes
                WHERE f17_prestamo = :prestamo
                AND estado_calculado = 'VIGENTE'
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

            // Obtener cuota actual directamente de f17_cuotasprestamo_nuevo (sin usar vista)
            $sqlCuota = "SELECT
                            f17_ncuota as numero_cuota,
                            f17_capital as capital,
                            f17_interes as interes,
                            f17_total as total,
                            f17_fecha as fecha_vencimiento,
                            f17_estado as estado
                        FROM f17_cuotasprestamo_nuevo
                        WHERE f17_prestamo = :prestamo
                        AND f17_estado = 'VIGENTE'
                        ORDER BY f17_fecha ASC
                        LIMIT 1";
            $stmtCuota = $this->pdo->prepare($sqlCuota);
            $stmtCuota->execute(['prestamo' => $prestamoCodigo]);
            $cuota = $stmtCuota->fetch(PDO::FETCH_ASSOC);

            if ($cuota) {
                // Calcular faltante simplemente como el total (para el formulario inicial)
                $cuota['faltante'] = $cuota['total'];
                $pendientes['cuota'] = $cuota;
                $pendientes['cuotaDetalle'] = [
                    'numero_cuota' => $cuota['numero_cuota'],
                    'capital' => (float) $cuota['capital'],
                    'interes' => (float) $cuota['interes'],
                    'interesSG' => 0,
                    'mora' => 0,
                    'moraSG' => 0,
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

            // NO iniciamos transacción global aquí
            // Cada servicio individual manejará su propia transacción
            $movimientosGenerados = [];
            $clienteCodigo = $datos['cliente'];
            $prestamosSeleccionados = $datos['prestamos'];
            $montosGlobales = $datos['montosGlobales'] ?? [];
            $montosPrestamos = $datos['montosPrestamos'] ?? [];
            $cuotasSeleccionadas = $datos['cuotasSeleccionadas'] ?? [];
            $codigoContableMontoTotal = $codigosContables['codigoContableMontoTotal'] ?? '10612';

            // 1. Procesar componentes globales
            // Aportes FONBES
            if (!empty($montosGlobales['aportesFonbes'])) {
                // Calcular monto total de aportes FONBES
                $montoTotal = 0;
                $aportesFonbes = is_array($montosGlobales['aportesFonbes']) ? $montosGlobales['aportesFonbes'] : [$montosGlobales['aportesFonbes']];
                foreach ($aportesFonbes as $monto) {
                    $montoTotal += (float) $monto;
                }
                
                // Solo procesar si el monto total es mayor a 0
                if ($montoTotal > 0) {
                    $logMessage = sprintf("[%s] Procesando Aportes FONBES - Monto: %.2f\n", date('Y-m-d H:i:s'), $montoTotal);
                    file_put_contents($logFile, $logMessage, FILE_APPEND);

                    $aportesServicio = new AportesServicio($this->pdo);
                    
                    // Extraer códigos contables específicos para FONBES
                    $codigosAportesFonbes = $codigosContables['aportesFonbes'] ?? [];
                    
                    $datosAportesFonbes = [
                        'cliente' => $clienteCodigo,
                        'anio' => date('Y'),
                        'periodo' => 1,
                        'periodo2' => 12,
                        'monto' => $montoTotal,
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
                            'monto' => $montoTotal,
                            'desglose' => $montosGlobales['aportesFonbes'],
                        ];
                    }
                } else {
                    $logMessage = sprintf("[%s] OMITIENDO Aportes FONBES - Monto es 0\n", date('Y-m-d H:i:s'));
                    file_put_contents($logFile, $logMessage, FILE_APPEND);
                }
            }

            // Aportes COOPAM
            if (!empty($montosGlobales['aportesCoopam'])) {
                // Calcular monto total de aportes COOPAM
                $montoTotal = 0;
                $aportesCoopam = is_array($montosGlobales['aportesCoopam']) ? $montosGlobales['aportesCoopam'] : [$montosGlobales['aportesCoopam']];
                foreach ($aportesCoopam as $monto) {
                    $montoTotal += (float) $monto;
                }
                
                // Solo procesar si el monto total es mayor a 0
                if ($montoTotal > 0) {
                    $logMessage = sprintf("[%s] Procesando Aportes COOPAM - Monto: %.2f\n", date('Y-m-d H:i:s'), $montoTotal);
                    file_put_contents($logFile, $logMessage, FILE_APPEND);

                    $aportesCoopamServicio = new AportesCoopamServicio($this->pdo);
                    
                    $datosAportesCoopam = [
                        'cliente' => $clienteCodigo,
                        'anio' => date('Y'),
                        'periodo' => 1,
                        'periodo2' => 12,
                        'monto' => $montoTotal,
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
                            'monto' => $montoTotal,
                            'desglose' => $montosGlobales['aportesCoopam'],
                        ];
                    }
                } else {
                    $logMessage = sprintf("[%s] OMITIENDO Aportes COOPAM - Monto es 0\n", date('Y-m-d H:i:s'));
                    file_put_contents($logFile, $logMessage, FILE_APPEND);
                }
            }

            // Sobregiro FONBES
            if (!empty($montosGlobales['sobregiroFonbes']) && $montosGlobales['sobregiroFonbes'] > 0) {
                $logMessage = sprintf("[%s] Procesando Sobregiro FONBES - Monto: %.2f\n", date('Y-m-d H:i:s'), $montosGlobales['sobregiroFonbes']);
                file_put_contents($logFile, $logMessage, FILE_APPEND);

                $sobregiroFonbesServicio = new PagoSobregiroFonbesServicio($this->pdo);
                $datosSobregiroFonbes = [
                    'cliente' => $clienteCodigo,
                    'monto' => $montosGlobales['sobregiroFonbes'],
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
                        'monto' => $montosGlobales['sobregiroFonbes'],
                    ];
                }
            } else {
                $logMessage = sprintf("[%s] OMITIENDO Sobregiro FONBES - Monto es 0 o no especificado\n", date('Y-m-d H:i:s'));
                file_put_contents($logFile, $logMessage, FILE_APPEND);
            }

            // Sobregiro COOPAM
            if (!empty($montosGlobales['sobregiroCoopam']) && $montosGlobales['sobregiroCoopam'] > 0) {
                $logMessage = sprintf("[%s] Procesando Sobregiro COOPAM - Monto: %.2f\n", date('Y-m-d H:i:s'), $montosGlobales['sobregiroCoopam']);
                file_put_contents($logFile, $logMessage, FILE_APPEND);

                $sobregiroCoopamServicio = new PagoSobregiroCoopamServicio($this->pdo);
                $datosSobregiroCoopam = [
                    'cliente' => $clienteCodigo,
                    'monto' => $montosGlobales['sobregiroCoopam'],
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
                        'monto' => $montosGlobales['sobregiroCoopam'],
                    ];
                }
            } else {
                $logMessage = sprintf("[%s] OMITIENDO Sobregiro COOPAM - Monto es 0 o no especificado\n", date('Y-m-d H:i:s'));
                file_put_contents($logFile, $logMessage, FILE_APPEND);
            }

            // 2. Procesar componentes por préstamo
            foreach ($prestamosSeleccionados as $prestamoCodigo) {
                $codigosPrestamo = $codigosContables['prestamos'][$prestamoCodigo] ?? [];
                $montosPrestamo = $montosPrestamos[$prestamoCodigo] ?? [];

                // Sobregiro del préstamo
                if (!empty($montosPrestamo['sobregiro']) && $montosPrestamo['sobregiro'] > 0) {
                    $logMessage = sprintf("[%s] Procesando Sobregiro Préstamo #%d - Monto: %.2f\n", date('Y-m-d H:i:s'), $prestamoCodigo, $montosPrestamo['sobregiro']);
                    file_put_contents($logFile, $logMessage, FILE_APPEND);

                    $sobregiroPrestamoServicio = new SobregiroPrestamoServicio();
                    $datosSobregiroPrestamo = [
                        'cliente' => $clienteCodigo,
                        'prestamo' => $prestamoCodigo,
                        'monto' => $montoTotalSobregiro,
                        'sobregiro' => $montosPrestamo['sobregiro'],
                        'descuenta' => 0,
                        'gastosAdm' => $montosPrestamo['gastos_adm'] ?? 0,
                        'gastosMora' => $montosPrestamo['gastos_mora'] ?? 0,
                        'otros1' => $montosPrestamo['otros1'] ?? 0,
                        'otros2' => $montosPrestamo['otros2'] ?? 0,
                        'formaPago' => $datos['formaPago'],
                        'obs' => $datos['observaciones'] ?? '',
                    ];

                    // Filtrar códigos contables de sobregiro (componentes granulares)
                    $codigosSobregiro = [];
                    $mapeoComponentes = [
                        'sobregiro' => 'sobregiro',
                        'gastos_adm' => 'gastosAdm',
                        'gastos_mora' => 'gastosMora',
                        'otros1' => 'otros1',
                        'otros2' => 'otros2',
                    ];
                    foreach ($mapeoComponentes as $componenteFormulario => $componenteServicio) {
                        if (isset($codigosPrestamo[$componenteFormulario]) && !empty($codigosPrestamo[$componenteFormulario]['codigo'])) {
                            $codigosSobregiro[$componenteServicio] = $codigosPrestamo[$componenteFormulario];
                        }
                    }

                    // Agregar código contable del monto total (DEBE)
                    $montoTotalSobregiro = $montosPrestamo['sobregiro'] + $montosPrestamo['gastos_adm'] + $montosPrestamo['gastos_mora'] + $montosPrestamo['otros1'] + $montosPrestamo['otros2'];
                    if ($montoTotalSobregiro > 0) {
                        $codigosSobregiro['monto'] = [
                            'codigo' => $codigoContableMontoTotal,
                            'tipo' => 'debe',
                            'monto' => $montoTotalSobregiro,
                        ];
                    }

                    if (!empty($codigosSobregiro)) {
                        $resultado = $sobregiroPrestamoServicio->ejecutarPagoSobregiroPrestamo(
                            $datosSobregiroPrestamo,
                            $codigosSobregiro
                        );
                    } else {
                        $resultado = $sobregiroPrestamoServicio->ejecutarPagoSobregiroPrestamo($datosSobregiroPrestamo);
                    }

                    if ($resultado['exito']) {
                        $movimientosGenerados[] = [
                            'tipo' => "Sobregiro Préstamo #{$prestamoCodigo}",
                            'codigo' => $resultado['codigo_movimiento'],
                            'monto' => $montoTotalSobregiro,
                            'desglose' => [
                                'sobregiro' => $montosPrestamo['sobregiro'],
                                'gastos_adm' => $montosPrestamo['gastos_adm'] ?? 0,
                                'gastos_mora' => $montosPrestamo['gastos_mora'] ?? 0,
                                'otros1' => $montosPrestamo['otros1'] ?? 0,
                                'otros2' => $montosPrestamo['otros2'] ?? 0,
                            ],
                        ];
                    }
                } else {
                    $logMessage = sprintf("[%s] OMITIENDO Sobregiro Préstamo #%d - Monto es 0 o no especificado\n", date('Y-m-d H:i:s'), $prestamoCodigo);
                    file_put_contents($logFile, $logMessage, FILE_APPEND);
                }

                // Amortización de cuota seleccionada o actual
                $montoAmortizacionTotal = ($montosPrestamo['amortizacion_capital'] ?? 0) +
                                          ($montosPrestamo['amortizacion_interes'] ?? 0) +
                                          ($montosPrestamo['amortizacion_mora'] ?? 0) +
                                          ($montosPrestamo['amortizacion_otros1'] ?? 0) +
                                          ($montosPrestamo['amortizacion_otros2'] ?? 0);

                // Obtener cuota seleccionada del formulario, si no hay, usar la cuota actual
                $cuotaSeleccionada = $cuotasSeleccionadas[$prestamoCodigo] ?? null;
                
                if ($cuotaSeleccionada && $cuotaSeleccionada !== '') {
                    // Obtener la cuota seleccionada específica
                    $sqlCuota = "SELECT
                                    f17_ncuota as numero_cuota,
                                    f17_capital as capital,
                                    f17_interes as interes,
                                    f17_total as total,
                                    f17_fecha as fecha_vencimiento,
                                    f17_estado as estado
                                FROM f17_cuotasprestamo_nuevo
                                WHERE f17_prestamo = :prestamo
                                AND f17_ncuota = :cuota
                                LIMIT 1";
                    $stmtCuota = $this->pdo->prepare($sqlCuota);
                    $stmtCuota->execute(['prestamo' => $prestamoCodigo, 'cuota' => $cuotaSeleccionada]);
                    $cuota = $stmtCuota->fetch(PDO::FETCH_ASSOC);
                } else {
                    // Obtener cuota actual del préstamo (la que debe pagarse hoy)
                    // Consulta directa a f17_cuotasprestamo_nuevo para evitar vista lenta
                    $sqlCuota = "SELECT
                                    f17_ncuota as numero_cuota,
                                    f17_capital as capital,
                                    f17_interes as interes,
                                    f17_total as total,
                                    f17_fecha as fecha_vencimiento,
                                    f17_estado as estado
                                FROM f17_cuotasprestamo_nuevo
                                WHERE f17_prestamo = :prestamo
                                AND f17_estado = 'VIGENTE'
                                ORDER BY f17_fecha ASC
                                LIMIT 1";
                    $stmtCuota = $this->pdo->prepare($sqlCuota);
                    $stmtCuota->execute(['prestamo' => $prestamoCodigo]);
                    $cuota = $stmtCuota->fetch(PDO::FETCH_ASSOC);
                }

                if ($cuota === false) {
                    $logMessage = sprintf("[%s] ADVERTENCIA - No hay cuota vigente para préstamo #%d\n", date('Y-m-d H:i:s'), $prestamoCodigo);
                    file_put_contents($logFile, $logMessage, FILE_APPEND);
                    continue;
                }

                // Validar si es cuota 0 con total 0 - no registrar movimiento, solo cambiar estado
                if ($cuota['numero_cuota'] == 0 && $cuota['total'] == 0) {
                    $logMessage = sprintf("[%s] OMISIÓN - Cuota 0 con total 0 para préstamo #%d - Solo cambiando estado a PAGADO\n", date('Y-m-d H:i:s'), $prestamoCodigo);
                    file_put_contents($logFile, $logMessage, FILE_APPEND);

                    // Cambiar estado a PAGADO en f17_cuotasprestamo_nuevo (cronograma nuevo)
                    $sqlUpdateNuevo = "UPDATE f17_cuotasprestamo_nuevo
                                     SET f17_estado = 'PAGADO',
                                         f17_fechaCancela = CURDATE()
                                     WHERE f17_prestamo = :prestamo
                                     AND f17_ncuota = :cuota";
                    $stmtUpdateNuevo = $this->pdo->prepare($sqlUpdateNuevo);
                    $stmtUpdateNuevo->execute([
                        'prestamo' => $prestamoCodigo,
                        'cuota' => $cuota['numero_cuota']
                    ]);

                    // Cambiar estado a PAGADO en f17_cuotasprestamo (cronograma original para visualización)
                    $sqlUpdateOriginal = "UPDATE f17_cuotasprestamo
                                     SET f17_estado = 'PAGADO',
                                         f17_fechaCancela = CURDATE()
                                     WHERE f17_prestamo = :prestamo
                                     AND f17_ncuota = :cuota";
                    $stmtUpdateOriginal = $this->pdo->prepare($sqlUpdateOriginal);
                    $stmtUpdateOriginal->execute([
                        'prestamo' => $prestamoCodigo,
                        'cuota' => $cuota['numero_cuota']
                    ]);

                    $logMessage = sprintf("[%s] Estado cambiado a PAGADO en ambos cronogramas para cuota 0 de préstamo #%d\n", date('Y-m-d H:i:s'), $prestamoCodigo);
                    file_put_contents($logFile, $logMessage, FILE_APPEND);
                    continue;
                }

                if ($montoAmortizacionTotal > 0) {
                    $logMessage = sprintf("[%s] Procesando Amortización Préstamo #%d - Monto Total: %.2f\n", date('Y-m-d H:i:s'), $prestamoCodigo, $montoAmortizacionTotal);
                    file_put_contents($logFile, $logMessage, FILE_APPEND);

                    $amortizacionServicio = new AmortizacionServicio($this->pdo);
                    
                    $datosAmortizacion = [
                        'cliente' => $clienteCodigo,
                        'prestamo' => $prestamoCodigo,
                        'cuota' => $cuota['numero_cuota'],
                        'monto' => $montoAmortizacionTotal,
                        'amortiza' => $montosPrestamo['amortizacion_capital'] ?? 0,
                        'gastosAdm' => $montosPrestamo['amortizacion_interes'] ?? 0,
                        'gastosMora' => $montosPrestamo['amortizacion_mora'] ?? 0,
                        'otros1' => $montosPrestamo['amortizacion_otros1'] ?? 0,
                        'otros2' => $montosPrestamo['amortizacion_otros2'] ?? 0,
                        'sobregiro' => 0,
                        'formaPago' => $datos['formaPago'],
                        'obs' => $datos['observaciones'] ?? '',
                    ];

                    // Filtrar códigos contables de amortización (componentes granulares)
                    $codigosAmortizacion = [];
                    $mapeoComponentesAmortizacion = [
                        'amortizacion_capital' => 'amortiza',
                        'amortizacion_interes' => 'gastosAdm',
                        'amortizacion_mora' => 'gastosMora',
                        'amortizacion_otros1' => 'otros1',
                        'amortizacion_otros2' => 'otros2',
                    ];
                    foreach ($mapeoComponentesAmortizacion as $componenteFormulario => $componenteServicio) {
                        if (isset($codigosPrestamo[$componenteFormulario]) && !empty($codigosPrestamo[$componenteFormulario]['codigo'])) {
                            $codigosAmortizacion[$componenteServicio] = $codigosPrestamo[$componenteFormulario];
                        }
                    }

                    // Agregar código contable del monto total (DEBE)
                    if ($montoAmortizacionTotal > 0) {
                        $codigosAmortizacion['monto'] = [
                            'codigo' => $codigoContableMontoTotal,
                            'tipo' => 'debe',
                            'monto' => $montoAmortizacionTotal,
                        ];
                    }

                    if (!empty($codigosAmortizacion)) {
                        $resultado = $amortizacionServicio->ejecutarAmortizacionAEE(
                            $datosAmortizacion,
                            [],
                            $codigosAmortizacion
                        );
                    } else {
                        $resultado = $amortizacionServicio->ejecutarAmortizacionAEE($datosAmortizacion);
                    }

                    if ($resultado['exito']) {
                        $movimientosGenerados[] = [
                            'tipo' => "Amortización Préstamo #{$prestamoCodigo}",
                            'codigo' => $resultado['codigo_movimiento'],
                            'monto' => $montoAmortizacionTotal,
                            'desglose' => [
                                'capital' => $montosPrestamo['amortizacion_capital'] ?? 0,
                                'interes' => $montosPrestamo['amortizacion_interes'] ?? 0,
                                'mora' => $montosPrestamo['amortizacion_mora'] ?? 0,
                                'otros1' => $montosPrestamo['amortizacion_otros1'] ?? 0,
                                'otros2' => $montosPrestamo['amortizacion_otros2'] ?? 0,
                            ],
                        ];
                    }
                } else {
                    $logMessage = sprintf("[%s] OMITIENDO Amortización Préstamo #%d - Monto total es 0\n", date('Y-m-d H:i:s'), $prestamoCodigo);
                    file_put_contents($logFile, $logMessage, FILE_APPEND);
                }

                // Procesar cuotas parciales seleccionadas
                if (!empty($datos['cuotasParciales'][$prestamoCodigo])) {
                    $cuotasParciales = $datos['cuotasParciales'][$prestamoCodigo];
                    $logMessage = sprintf("[%s] Procesando %d cuotas parciales para préstamo #%d\n", date('Y-m-d H:i:s'), count($cuotasParciales), $prestamoCodigo);
                    file_put_contents($logFile, $logMessage, FILE_APPEND);

                    foreach ($cuotasParciales as $numeroCuota) {
                        // Obtener cuota parcial
                        $stmt = $this->pdo->prepare("SELECT * FROM f17_cuotasprestamo WHERE f17_prestamo = ? AND f17_ncuota = ?");
                        $stmt->execute([$prestamoCodigo, $numeroCuota]);
                        $cuota = $stmt->fetch(PDO::FETCH_ASSOC);

                        $logMessage = sprintf("[%s] Cuota #%d - Estado: %s, Total: %.2f, Saldo: %.2f\n", date('Y-m-d H:i:s'), $numeroCuota, $cuota['f17_estado'] ?? 'N/A', $cuota['f17_total'] ?? 0, $cuota['f17_saldo'] ?? 0);
                        file_put_contents($logFile, $logMessage, FILE_APPEND);

                        if ($cuota && $cuota['f17_estado'] === 'PARCIAL') {
                            // Calcular monto faltante
                            $montoFaltante = $cuota['f17_total'] - $cuota['f17_saldo'];

                            if ($montoFaltante > 0) {
                                $logMessage = sprintf("[%s] Completando cuota parcial #%d - Faltante: %.2f\n", date('Y-m-d H:i:s'), $numeroCuota, $montoFaltante);
                                file_put_contents($logFile, $logMessage, FILE_APPEND);

                                $datosCompletarCuota = [
                                    'cliente' => $clienteCodigo,
                                    'prestamo' => $prestamoCodigo,
                                    'cuota' => $numeroCuota,
                                    'monto' => $montoFaltante,
                                    'amortiza' => $montoFaltante, // Todo el faltante va a capital
                                    'gastosAdm' => 0,
                                    'gastosMora' => 0,
                                    'otros1' => 0,
                                    'otros2' => 0,
                                    'sobregiro' => 0,
                                    'formaPago' => $datos['formaPago'],
                                    'obs' => 'Completar cuota parcial - Pago Integral',
                                ];

                                // Usar los mismos códigos contables que la amortización
                                $codigosCompletar = $codigosAmortizacion ?? [];
                                if (!empty($codigosCompletar)) {
                                    // Ajustar montos en códigos contables
                                    foreach ($codigosCompletar as &$codigo) {
                                        if (isset($codigo['monto'])) {
                                            $codigo['monto'] = $montoFaltante;
                                        }
                                    }
                                }

                                $logMessage = sprintf("[%s] Llamando a amortización para completar cuota #%d\n", date('Y-m-d H:i:s'), $numeroCuota);
                                file_put_contents($logFile, $logMessage, FILE_APPEND);

                                $resultado = $amortizacionServicio->ejecutarAmortizacionAEE(
                                    $datosCompletarCuota,
                                    [],
                                    $codigosCompletar
                                );

                                $logMessage = sprintf("[%s] Resultado completar cuota #%d: %s\n", date('Y-m-d H:i:s'), $numeroCuota, $resultado['exito'] ? 'EXITO' : 'ERROR');
                                file_put_contents($logFile, $logMessage, FILE_APPEND);

                                if ($resultado['exito']) {
                                    $movimientosGenerados[] = [
                                        'tipo' => "Completar Cuota Parcial #{$numeroCuota} (Préstamo #{$prestamoCodigo})",
                                        'codigo' => $resultado['codigo_movimiento'],
                                        'monto' => $montoFaltante,
                                    ];
                                }
                            } else {
                                $logMessage = sprintf("[%s] OMITIENDO cuota parcial #%d - Faltante es 0\n", date('Y-m-d H:i:s'), $numeroCuota);
                                file_put_contents($logFile, $logMessage, FILE_APPEND);
                            }
                        } else {
                            $logMessage = sprintf("[%s] OMITIENDO cuota #%d - No es PARCIAL o no existe\n", date('Y-m-d H:i:s'), $numeroCuota);
                            file_put_contents($logFile, $logMessage, FILE_APPEND);
                        }
                    }
                }
            }

            // NO hay commit global ya que cada servicio maneja su propia transacción

            // 3. Insertar asiento contable general con el total de todas las operaciones
            $totalGeneral = 0;
            foreach ($movimientosGenerados as $movimiento) {
                $totalGeneral += $movimiento['monto'];
            }

            if ($totalGeneral > 0) {
                $logMessage = sprintf("[%s] Insertando asiento contable general - Total: %.2f\n", date('Y-m-d H:i:s'), $totalGeneral);
                file_put_contents($logFile, $logMessage, FILE_APPEND);

                // Usar el último código de movimiento generado para el asiento general
                $ultimoMovimiento = end($movimientosGenerados);
                $codigoMovimientoGeneral = $ultimoMovimiento['codigo'] ?? 0;

                $sql = "INSERT INTO f37_diario_contable
                        (f37_movimiento, f37_fecha, f37_codigo_contable, f37_operacion, f37_descripcion,
                         f37_debe, f37_haber, f37_usuario_crea, f37_fecha_crea, f37_estado)
                        VALUES (:movimiento, :fecha, :codigoContable, :operacion, :descripcion,
                                :debe, :haber, :usuario, NOW(), 'ACTIVO')";

                $stmt = $this->pdo->prepare($sql);
                $stmt->execute([
                    'movimiento' => $codigoMovimientoGeneral,
                    'fecha' => date('Y-m-d'),
                    'codigoContable' => $codigoContableMontoTotal,
                    'operacion' => 10,
                    'descripcion' => 'PAGO INTEGRAL - TOTAL GENERAL',
                    'debe' => $totalGeneral,
                    'haber' => 0.00,
                    'usuario' => 'SISTEMA',
                ]);
            }

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
            // NO hay rollback ya que cada servicio maneja su propia transacción

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
