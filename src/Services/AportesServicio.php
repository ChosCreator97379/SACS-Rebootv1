<?php

declare(strict_types=1);

namespace Sacs\Services;

use PDO;
use RuntimeException;

final class AportesServicio
{
    public function __construct(
        private readonly PDO $pdo
    ) {
    }

    /**
     * Ejecuta un pago de aportes FONBES (Operación 1)
     *
     * @param array $datos Datos del formulario
     * @param array $codigosContables Códigos contables por componente
     * @return array Resultado de la operación
     */
    public function ejecutarPagoAportesFonbes(array $datos, array $codigosContables = []): array
    {
        $logFile = dirname(__DIR__, 2) . '/logs/aportes.log';
        
        // Asegurar que el directorio de logs existe
        if (!is_dir(dirname($logFile))) {
            mkdir(dirname($logFile), 0755, true);
        }

        try {
            // Registrar inicio de operación
            $logMessage = sprintf(
                "[%s] INICIO - Cliente: %s, Periodo: %s/%s a %s/%s, Monto: %.2f\n",
                date('Y-m-d H:i:s'),
                $datos['cliente'],
                $datos['periodo'],
                $datos['anio'],
                $datos['periodo2'],
                $datos['anio2'],
                $datos['monto']
            );
            file_put_contents($logFile, $logMessage, FILE_APPEND);

            $pdo = $this->pdo;
            $pdo->beginTransaction();

            // 1. Obtener datos del socio
            $socio = $this->obtenerSocio($pdo, $datos['cliente']);
            if ($socio === null) {
                throw new RuntimeException('Socio no encontrado');
            }

            // 2. Obtener aporte base del socio
            $aporteBase = (float) ($socio['F01_APORTACION'] ?? 0);
            
            // 3. Obtener aporte final (getAportacionFinal2 del legacy)
            $aporteFinal = $this->obtenerAporteFinal($pdo, $datos['cliente']);
            $aporteTotal = $aporteBase + $aporteFinal;

            // 4. Calcular número de meses
            $periodoInicio = (int) $datos['periodo'];
            $periodoFin = (int) $datos['periodo2'];
            $numMeses = $periodoFin - $periodoInicio;

            // 5. Generar código de movimiento
            $codigoMovimiento = $this->generarCodigoMovimiento($pdo);

            // 6. Procesar pagos por mes
            $montoTotal = (float) $datos['monto'];
            $montoPorMes = $numMeses > 0 ? $montoTotal / ($numMeses + 1) : $montoTotal;

            $periodosYaPagados = [];
            $periodosNuevos = [];
            $montoTotalNuevo = 0;

            for ($mes = $periodoInicio; $mes <= $periodoFin; $mes++) {
                $mesStr = str_pad((string) $mes, 2, '0', STR_PAD_LEFT);
                $periodo = "{$datos['anio']}-{$mesStr}-01";

                // Verificar si ya existe un aporte para este periodo
                $yaExiste = $this->validarAporteExistente($pdo, $datos['cliente'], $periodo);
                $logMessage = sprintf(
                    "[%s] DEBUG - Periodo: %s, Cliente: %s, Ya existe: %s\n",
                    date('Y-m-d H:i:s'),
                    $periodo,
                    $datos['cliente'],
                    $yaExiste ? 'SI' : 'NO'
                );
                file_put_contents($logFile, $logMessage, FILE_APPEND);

                if ($yaExiste) {
                    // Verificar si está completamente pagado
                    if ($this->validarAporteCompletamentePagado($pdo, $datos['cliente'], $periodo)) {
                        $periodosYaPagados[] = $periodo;
                        continue; // Saltar este periodo
                    } else {
                        // Existe pero no está completamente pagado, actualizar el existente
                        $montoPagado = $numMeses > 0 ? $montoPorMes : $montoTotal;
                        $this->actualizarAporteFonbes(
                            $pdo,
                            $datos['cliente'],
                            $periodo,
                            $montoPagado
                        );
                        // Agregar a periodos procesados y al monto total
                        $periodosNuevos[] = $periodo;
                        $montoTotalNuevo += $montoPagado;
                        continue;
                    }
                }

                $montoPagado = $numMeses > 0 ? $montoPorMes : $montoTotal;
                $diferencia = $aporteTotal - $montoPagado;

                // Insertar aporte en f22_aportes
                $this->insertarAporteFonbes(
                    $pdo,
                    $datos['cliente'],
                    $periodo,
                    $aporteTotal,
                    $montoPagado,
                    $diferencia
                );

                $periodosNuevos[] = $periodo;
                $montoTotalNuevo += $montoPagado;
            }

            // Si no se procesó ningún periodo nuevo, retornar error
            if (empty($periodosNuevos)) {
                $logMessage = sprintf(
                    "[%s] DEBUG BLOQUEO - Periodos nuevos: 0, Periodos ya pagados: %s\n",
                    date('Y-m-d H:i:s'),
                    implode(', ', $periodosYaPagados)
                );
                file_put_contents($logFile, $logMessage, FILE_APPEND);
                throw new RuntimeException('Todos los periodos seleccionados ya están pagados. Periodos: ' . implode(', ', $periodosYaPagados));
            }

            $logMessage = sprintf(
                "[%s] DEBUG FINAL - Periodos nuevos: %d, Monto total nuevo: %.2f\n",
                date('Y-m-d H:i:s'),
                count($periodosNuevos),
                $montoTotalNuevo
            );
            file_put_contents($logFile, $logMessage, FILE_APPEND);

            // 7. Insertar movimiento
            $this->insertarMovimientoAporte(
                $pdo,
                $codigoMovimiento,
                $datos,
                $montoTotalNuevo
            );

            // 8. Insertar en diario contable (múltiples filas por componente) - PRIMERO
            if (!empty($codigosContables)) {
                $numPeriodosNuevos = count($periodosNuevos);
                $montoPorMesNuevo = $numPeriodosNuevos > 0 ? $montoTotalNuevo / $numPeriodosNuevos : $montoTotalNuevo;
                // Pasamos $numPeriodosNuevos - 1 porque el bucle usa <= en lugar de <
                $this->insertarDiarioContableMultiple($pdo, $codigoMovimiento, $datos, $codigosContables, $montoPorMesNuevo, $numPeriodosNuevos - 1);
            }

            $pdo->commit();

            // Registrar éxito
            $logMessage = sprintf(
                "[%s] EXITO - Movimiento: %d, Cliente: %s, Monto: %.2f\n",
                date('Y-m-d H:i:s'),
                $codigoMovimiento,
                $datos['cliente'],
                $montoTotalNuevo
            );
            file_put_contents($logFile, $logMessage, FILE_APPEND);

            // Construir mensaje de resultado
            $mensaje = 'Pago de aportes procesado correctamente';
            if (!empty($periodosYaPagados)) {
                $mensaje .= '. Periodos ya pagados (omitidos): ' . implode(', ', $periodosYaPagados);
            }
            $mensaje .= '. Periodos nuevos procesados: ' . count($periodosNuevos);

            return [
                'exito' => true,
                'codigo_movimiento' => $codigoMovimiento,
                'mensaje' => $mensaje,
                'periodos_ya_pagados' => $periodosYaPagados,
                'periodos_nuevos' => $periodosNuevos,
            ];

        } catch (RuntimeException $e) {
            if ($pdo->inTransaction()) {
                $pdo->rollBack();
            }
            $logMessage = sprintf(
                "[%s] ERROR RUNTIME - Cliente: %s, Error: %s\n",
                date('Y-m-d H:i:s'),
                $datos['cliente'],
                $e->getMessage()
            );
            file_put_contents($logFile, $logMessage, FILE_APPEND);

            return [
                'exito' => false,
                'error' => $e->getMessage(),
                'mensaje' => 'Error en pago de aportes: ' . $e->getMessage(),
            ];
        } catch (\Throwable $e) {
            if ($pdo->inTransaction()) {
                $pdo->rollBack();
            }
            $logMessage = sprintf(
                "[%s] ERROR INESPERADO - Cliente: %s, Error: %s\n",
                date('Y-m-d H:i:s'),
                $datos['cliente'],
                $e->getMessage()
            );
            file_put_contents($logFile, $logMessage, FILE_APPEND);

            return [
                'exito' => false,
                'error' => 'Error inesperado: ' . $e->getMessage(),
                'mensaje' => 'Error inesperado al procesar pago de aportes',
            ];
        }
    }

    /**
     * Obtiene datos del socio
     */
    private function obtenerSocio(PDO $pdo, string $clienteCodigo): ?array
    {
        $sql = "SELECT p.F01_APORTACION
                FROM f01_persona p
                INNER JOIN f02_cliente c ON c.F02_ID_PERSONA = p.F01_ID_PERSONA
                WHERE CAST(c.F02_ID_CLIENTE AS UNSIGNED) = CAST(:cliente AS UNSIGNED)
                LIMIT 1";
        $stmt = $pdo->prepare($sql);
        $stmt->execute(['cliente' => trim($clienteCodigo)]);
        $result = $stmt->fetch();
        return $result === false ? null : $result;
    }

    /**
     * Obtiene el monto de aporte FONBES del socio (método público para uso en vistas)
     */
    public function obtenerMontoAporteFonbes(string $clienteCodigo): ?float
    {
        $socio = $this->obtenerSocio($this->pdo, $clienteCodigo);
        if ($socio === null) {
            return null;
        }
        return (float) ($socio['F01_APORTACION'] ?? 0);
    }

    /**
     * Verifica si ya existe un aporte para un socio y periodo específico
     * No verifica si está completamente pagado, solo si existe el registro
     */
    private function validarAporteExistente(PDO $pdo, string $clienteCodigo, string $periodo): bool
    {
        $sql = "SELECT COUNT(*) as total FROM f22_aportes
                WHERE f22_socio = CAST(:cliente AS UNSIGNED)
                  AND f22_periodo = :periodo";
        $stmt = $pdo->prepare($sql);
        $stmt->execute(['cliente' => trim($clienteCodigo), 'periodo' => $periodo]);
        $result = $stmt->fetch();
        return (int) ($result['total'] ?? 0) > 0;
    }

    /**
     * Verifica si un aporte está completamente pagado
     */
    private function validarAporteCompletamentePagado(PDO $pdo, string $clienteCodigo, string $periodo): bool
    {
        $sql = "SELECT COUNT(*) as total FROM f22_aportes
                WHERE f22_socio = CAST(:cliente AS UNSIGNED)
                  AND f22_periodo = :periodo
                  AND f22_aportacionD >= f22_aportacionP";
        $stmt = $pdo->prepare($sql);
        $stmt->execute(['cliente' => trim($clienteCodigo), 'periodo' => $periodo]);
        $result = $stmt->fetch();
        return (int) ($result['total'] ?? 0) > 0;
    }

    /**
     * Actualiza un aporte existente reemplazando el valor del pago (según legacy)
     */
    private function actualizarAporteFonbes(PDO $pdo, string $clienteCodigo, string $periodo, float $nuevoPago): void
    {
        // Obtener el aporte planificado para calcular la diferencia
        $stmt = $pdo->prepare("SELECT f22_aportacionP FROM f22_aportes WHERE f22_socio = CAST(:cliente AS UNSIGNED) AND f22_periodo = :periodo LIMIT 1");
        $stmt->execute(['cliente' => trim($clienteCodigo), 'periodo' => $periodo]);
        $result = $stmt->fetch();
        $aporteP = (float) ($result['f22_aportacionP'] ?? 0);
        $diferencia = $aporteP - $nuevoPago;

        $sql = "UPDATE f22_aportes
                SET f22_aportacionD = :nuevoPago,
                    f22_diferencia = :diferencia,
                    f22_fechaPago = :fechaPago
                WHERE f22_socio = CAST(:cliente AS UNSIGNED)
                  AND f22_periodo = :periodo";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([
            'nuevoPago' => $nuevoPago,
            'diferencia' => $diferencia,
            'fechaPago' => date('Y-m-d'),
            'cliente' => trim($clienteCodigo),
            'periodo' => $periodo
        ]);
    }

    /**
     * Obtiene aporte final (equivalente a getAportacionFinal2 del legacy)
     * Suma los aportes base de los familiares del socio activos
     * (F01_STDO_PERSN = 1 y tipo de parentesco < 11)
     */
    private function obtenerAporteFinal(PDO $pdo, string $clienteCodigo): float
    {
        $sql = "SELECT SUM(p.F01_APORTA_BASE) as total_aportes
                FROM f01_persona p
                INNER JOIN f12_familiar f ON f.f12_id_persona = p.f01_id_persona
                WHERE f.f12_id_cliente = CAST(:cliente AS UNSIGNED)
                  AND p.F01_STDO_PERSN = 1
                  AND f.f12_id_tpo_parent < 11";
        $stmt = $pdo->prepare($sql);
        $stmt->execute(['cliente' => trim($clienteCodigo)]);
        $result = $stmt->fetch();
        return (float) ($result['total_aportes'] ?? 0);
    }

    /**
     * Genera código de movimiento de forma segura
     */
    private function generarCodigoMovimiento(PDO $pdo): int
    {
        $sql = "SELECT MAX(f15_codigo) + 1 AS next_id FROM f15_movimiento FOR UPDATE";
        $stmt = $pdo->prepare($sql);
        $stmt->execute();
        $result = $stmt->fetch();
        return (int) ($result['next_id'] ?? 1);
    }

    /**
     * Inserta aporte en f22_aportes
     */
    private function insertarAporteFonbes(
        PDO $pdo,
        string $clienteCodigo,
        string $periodo,
        float $aporteP,
        float $aporteD,
        float $diferencia
    ): void {
        $sql = "INSERT INTO f22_aportes
                (f22_socio, f22_periodo, f22_aportacionP, f22_aportacionD, f22_diferencia, f22_fechaPago)
                VALUES (:socio, :periodo, :aportacionP, :aportacionD, :diferencia, :fechaPago)";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([
            'socio' => trim($clienteCodigo),
            'periodo' => $periodo,
            'aportacionP' => $aporteP,
            'aportacionD' => $aporteD,
            'diferencia' => $diferencia,
            'fechaPago' => date('Y-m-d'),
        ]);
    }

    /**
     * Inserta movimiento en f15_movimiento
     */
    private function insertarMovimientoAporte(
        PDO $pdo,
        int $codigoMovimiento,
        array $datos,
        float $monto
    ): void {
        $sql = "INSERT INTO f15_movimiento 
                (f15_codigo, f15_fecha, f15_operacion, f15_cliente, f15_maportacion, 
                 f15_formaPago, f15_usuaCrea, f15_obs, f15_obs2)
                VALUES (:codigo, :fecha, :operacion, :cliente, :monto, 
                        :formaPago, :usuario, :obs, :obs2)";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([
            'codigo' => $codigoMovimiento,
            'fecha' => date('Y-m-d'),
            'operacion' => 1, // Operación 1 = Pagos de aportes FONBES
            'cliente' => trim($datos['cliente']),
            'monto' => $monto,
            'formaPago' => trim($datos['formaPago'] ?? ''),
            'usuario' => trim($datos['usuario'] ?? 'SISTEMA'),
            'obs' => trim($datos['obs'] ?? ''),
            'obs2' => trim($datos['obs2'] ?? ''),
        ]);
    }

    /**
     * Formatea fecha de DD/MM/YYYY a YYYY-MM-DD
     */
    private function formatearFecha(string $fecha): string
    {
        $partes = explode('/', $fecha);
        if (count($partes) === 3) {
            return "{$partes[2]}-{$partes[1]}-{$partes[0]}";
        }
        return $fecha;
    }

    /**
     * Inserta múltiples registros en diario contable (uno por cada componente)
     * Para aportes, inserta una fila por cada mes para el componente "aporte"
     */
    private function insertarDiarioContableMultiple(PDO $pdo, int $codigoMovimiento, array $datos, array $codigosContables, float $montoPorMes, int $numMeses): void
    {
        $sql = "INSERT INTO f37_diario_contable
                (f37_movimiento, f37_fecha, f37_codigo_contable, f37_operacion, f37_descripcion,
                 f37_debe, f37_haber, f37_usuario_crea, f37_fecha_crea, f37_estado)
                VALUES (:movimiento, :fecha, :codigoContable, :operacion, :descripcion,
                        :debe, :haber, :usuario, NOW(), 'ACTIVO')";

        $stmt = $pdo->prepare($sql);

        foreach ($codigosContables as $componente => $config) {
            // Solo insertar si hay monto mayor a 0 y tiene código contable
            if ($config['monto'] > 0 && !empty($config['codigo']) && !empty($config['tipo'])) {
                $tipo = $config['tipo'];
                $monto = $config['monto'];

                if ($componente === 'aporte' && $numMeses > 0) {
                    // Insertar una fila por cada mes para el componente aporte
                    for ($i = 0; $i <= $numMeses; $i++) {
                        $debe = $tipo === 'debe' ? $montoPorMes : 0.00;
                        $haber = $tipo === 'haber' ? $montoPorMes : 0.00;

                        $descripcion = 'PAGO APORTES FONBES - MES ' . ($i + 1);

                        $stmt->execute([
                            'movimiento' => $codigoMovimiento,
                            'fecha' => date('Y-m-d'),
                            'codigoContable' => trim($config['codigo']),
                            'operacion' => 1,
                            'descripcion' => $descripcion,
                            'debe' => $debe,
                            'haber' => $haber,
                            'usuario' => trim($datos['usuario'] ?? 'SISTEMA'),
                        ]);
                    }
                } else {
                    // Para el total u otros componentes, insertar una sola fila
                    $debe = $tipo === 'debe' ? $monto : 0.00;
                    $haber = $tipo === 'haber' ? $monto : 0.00;

                    $descripcion = 'PAGO APORTES FONBES - ' . strtoupper($componente);

                    $stmt->execute([
                        'movimiento' => $codigoMovimiento,
                        'fecha' => date('Y-m-d'),
                        'codigoContable' => trim($config['codigo']),
                        'operacion' => 1,
                        'descripcion' => $descripcion,
                        'debe' => $debe,
                        'haber' => $haber,
                        'usuario' => trim($datos['usuario'] ?? 'SISTEMA'),
                    ]);
                }
            }
        }
    }
}
