<?php

declare(strict_types=1);

namespace Sacs\Services;

use PDO;
use RuntimeException;

/**
 * Servicio para procesar pagos de aportes COOPAM (Operación 9)
 * Basado en CrearCajaAction.registrarAportacionCope()
 */
final class AportesCoopamServicio
{
    public function __construct(
        private readonly PDO $pdo
    ) {
    }

    /**
     * Ejecuta un pago de aportes COOPAM (Operación 9)
     *
     * @param array $datos Datos del formulario
     * @param array $datosContables Datos contables (codigoContable, tipoAsiento, descripcionContable)
     * @return array Resultado de la operación
     */
    public function ejecutarPagoAportesCoopam(array $datos, array $datosContables = []): array
    {
        $logFile = dirname(__DIR__, 2) . '/logs/aportes_cope.log';

        // Asegurar que el directorio de logs existe
        if (!is_dir(dirname($logFile))) {
            mkdir(dirname($logFile), 0755, true);
        }

        try {
            // Registrar inicio de operación
            $logMessage = sprintf(
                "[%s] INICIO - Cliente: %s, Monto: %.2f\n",
                date('Y-m-d H:i:s'),
                $datos['cliente'],
                $datos['monto']
            );
            file_put_contents($logFile, $logMessage, FILE_APPEND);

            // Iniciar transacción
            $this->pdo->beginTransaction();

            // 1. Obtener datos del socio
            $socio = $this->obtenerSocio($this->pdo, trim($datos['cliente']));
            if ($socio === null) {
                throw new RuntimeException('Socio no encontrado');
            }

            // 2. Obtener monto de aporte COPE del socio
            // NOTA: El legacy Java permite sobrescribir este valor con el campo 'apeno' del formulario,
            // pero el formulario PHP actual no tiene ese campo. Si se requiere ajuste manual en el futuro,
            // agregar el campo al formulario y usar: $montoAporteCope = !empty($datos['apeno']) ? (float)$datos['apeno'] : $this->obtenerMontoAporteCope(...);
            $montoAporteCope = $this->obtenerMontoAporteCopePrivate($this->pdo, trim($datos['cliente']));
            if ($montoAporteCope === null) {
                throw new RuntimeException('No se encontró monto de aporte COPE para el socio');
            }

            // 3. Generar código de movimiento
            $codigoMovimiento = $this->generarCodigoMovimiento($this->pdo);

            // 4. Procesar pagos por mes
            $montoTotal = (float) $datos['monto'];
            $periodoInicio = (int) $datos['periodo'];
            $periodoFin = (int) $datos['periodo2'];
            $anio = $datos['anio'];
            $numMeses = $periodoFin - $periodoInicio;
            $montoPorMes = $numMeses > 0 ? $montoTotal / ($numMeses + 1) : $montoTotal;

            $periodosNuevos = [];
            $periodosYaPagados = [];
            $montoTotalNuevo = 0;

            for ($mes = $periodoInicio; $mes <= $periodoFin; $mes++) {
                $mesStr = str_pad((string) $mes, 2, '0', STR_PAD_LEFT);
                $periodo = "{$anio}-{$mesStr}-01";

                // Validar si el periodo ya existe
                $yaExiste = $this->validarAporteExistente($this->pdo, trim($datos['cliente']), $periodo);

                if ($yaExiste) {
                    // Verificar si está completamente pagado
                    if ($this->validarAporteCompletamentePagado($this->pdo, trim($datos['cliente']), $periodo)) {
                        $periodosYaPagados[] = $periodo;
                        continue; // Saltar este periodo
                    } else {
                        // Existe pero no está completamente pagado, actualizar el existente
                        $montoPagado = $numMeses > 0 ? $montoPorMes : $montoTotal;
                        $this->actualizarAporteCoopam(
                            $this->pdo,
                            trim($datos['cliente']),
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
                $diferencia = $montoAporteCope - $montoPagado;

                // Insertar aporte en f27_aportes_cope
                $this->insertarAporteCoopam(
                    $this->pdo,
                    $datos['cliente'],
                    $periodo,
                    $montoAporteCope,
                    $montoPagado,
                    $diferencia
                );

                $periodosNuevos[] = $periodo;
                $montoTotalNuevo += $montoPagado;
            }

            // Si no se procesó ningún periodo nuevo, retornar error
            if (empty($periodosNuevos)) {
                throw new RuntimeException('Todos los periodos seleccionados ya están pagados. Periodos: ' . implode(', ', $periodosYaPagados));
            }

            // 5. Insertar movimiento (con monto de periodos nuevos)
            $this->insertarMovimientoAporteCoopam(
                $this->pdo,
                $codigoMovimiento,
                $datos,
                $montoTotalNuevo
            );

            // 6. Insertar en diario contable (múltiples filas por componente)
            if (!empty($datosContables)) {
                $numPeriodosNuevos = count($periodosNuevos);
                $montoPorMesNuevo = $numPeriodosNuevos > 0 ? $montoTotalNuevo / $numPeriodosNuevos : $montoTotalNuevo;
                $this->insertarDiarioContableMultiple($this->pdo, $codigoMovimiento, $datos, $datosContables, $montoPorMesNuevo, $numPeriodosNuevos - 1);
            }

            $this->pdo->commit();

            // Registrar éxito
            $logMessage = sprintf(
                "[%s] EXITO - Movimiento: %d, Cliente: %s, Monto: %.2f\n",
                date('Y-m-d H:i:s'),
                $codigoMovimiento,
                $datos['cliente'],
                $montoTotal
            );
            file_put_contents($logFile, $logMessage, FILE_APPEND);

            return [
                'exito' => true,
                'codigo_movimiento' => $codigoMovimiento,
                'mensaje' => 'Pago de aportes COOPAM procesado correctamente',
            ];

        } catch (RuntimeException $e) {
            // Rollback ante error
            if ($this->pdo->inTransaction()) {
                $this->pdo->rollBack();
            }

            // Registrar error
            $logMessage = sprintf(
                "[%s] ERROR - Cliente: %s, Mensaje: %s\n",
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

    /**
     * Obtiene datos del socio
     */
    private function obtenerSocio(PDO $pdo, string $clienteCodigo): ?array
    {
        $sql = "SELECT F02_ID_CLIENTE, F01_CONDICION, F01_APE_PAT, F01_APE_MAT, F01_NOMB_PRI, F01_NOMB_SEG
                FROM f02_cliente
                INNER JOIN f01_persona ON F02_ID_PERSONA = F01_ID_PERSONA
                WHERE CAST(F02_ID_CLIENTE AS UNSIGNED) = CAST(? AS UNSIGNED)
                LIMIT 1";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$clienteCodigo]);
        $result = $stmt->fetch();
        return $result ?: null;
    }

    /**
     * Obtiene el monto de aporte COPE del socio (método público para uso en vistas)
     */
    public function obtenerMontoAporteCope(string $clienteCodigo): ?float
    {
        return $this->obtenerMontoAporteCopePrivate($this->pdo, $clienteCodigo);
    }

    /**
     * Obtiene el monto de aporte COPE del socio (método privado interno)
     */
    private function obtenerMontoAporteCopePrivate(PDO $pdo, string $clienteCodigo): ?float
    {
        $sql = "SELECT p.F01_APORTE_COPE
                FROM f02_cliente c
                INNER JOIN f01_persona p ON p.F01_ID_PERSONA = c.F02_ID_PERSONA
                WHERE CAST(c.F02_ID_CLIENTE AS UNSIGNED) = CAST(? AS UNSIGNED)
                LIMIT 1";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$clienteCodigo]);
        $result = $stmt->fetch();
        return $result ? (float) $result['F01_APORTE_COPE'] : null;
    }

    /**
     * Genera código de movimiento de forma segura
     */
    private function generarCodigoMovimiento(PDO $pdo): int
    {
        $sql = "SELECT MAX(f15_codigo) AS max_codigo FROM f15_movimiento FOR UPDATE";
        $stmt = $pdo->query($sql);
        $result = $stmt->fetch();
        $maxCodigo = $result['max_codigo'] ?? 0;
        return $maxCodigo + 1;
    }

    /**
     * Valida si existe un aporte para un socio y periodo específico
     */
    private function validarAporteExistente(PDO $pdo, string $clienteCodigo, string $periodo): bool
    {
        $sql = "SELECT COUNT(*) as total FROM f27_aportes_cope
                WHERE f27_socio = CAST(:cliente AS UNSIGNED)
                  AND f27_periodo = :periodo";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([
            'cliente' => trim($clienteCodigo),
            'periodo' => $periodo
        ]);
        $result = $stmt->fetch();

        return (int) ($result['total'] ?? 0) > 0;
    }

    /**
     * Valida si un aporte está completamente pagado
     */
    private function validarAporteCompletamentePagado(PDO $pdo, string $clienteCodigo, string $periodo): bool
    {
        $sql = "SELECT COUNT(*) as total FROM f27_aportes_cope
                WHERE f27_socio = CAST(:cliente AS UNSIGNED)
                  AND f27_periodo = :periodo
                  AND f27_aportacionD >= f27_aportacionP";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([
            'cliente' => trim($clienteCodigo),
            'periodo' => $periodo
        ]);
        $result = $stmt->fetch();

        return (int) ($result['total'] ?? 0) > 0;
    }

    /**
     * Actualiza un aporte existente reemplazando el valor del pago (según legacy)
     */
    private function actualizarAporteCoopam(PDO $pdo, string $clienteCodigo, string $periodo, float $nuevoPago): void
    {
        // Primero obtener el monto planificado para calcular la diferencia
        $sql = "SELECT f27_aportacionP FROM f27_aportes_cope
                WHERE f27_socio = CAST(:cliente AS UNSIGNED)
                  AND f27_periodo = :periodo
                LIMIT 1";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([
            'cliente' => trim($clienteCodigo),
            'periodo' => $periodo
        ]);
        $result = $stmt->fetch();

        if (!$result) {
            throw new RuntimeException('No se encontró el aporte para actualizar');
        }

        $aporteP = (float) $result['f27_aportacionP'];
        $diferencia = $aporteP - $nuevoPago;

        // Actualizar el registro
        $sql = "UPDATE f27_aportes_cope
                SET f27_aportacionD = :aportacionD,
                    f27_diferencia = :diferencia,
                    f27_fechaPago = :fechaPago
                WHERE f27_socio = CAST(:cliente AS UNSIGNED)
                  AND f27_periodo = :periodo";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([
            'cliente' => trim($clienteCodigo),
            'periodo' => $periodo,
            'aportacionD' => $nuevoPago,
            'diferencia' => $diferencia,
            'fechaPago' => date('Y-m-d')
        ]);
    }

    /**
     * Inserta aporte en f27_aportes_cope
     */
    private function insertarAporteCoopam(
        PDO $pdo,
        string $clienteCodigo,
        string $periodo,
        float $aporteP,
        float $aporteD,
        float $diferencia
    ): void {
        $sql = "INSERT INTO f27_aportes_cope
                (f27_socio, f27_periodo, f27_aportacionP, f27_aportacionD, f27_diferencia, f27_aportacion, f27_fechaPago)
                VALUES (:socio, :periodo, :aportacionP, :aportacionD, :diferencia, :aportacion, :fechaPago)";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([
            'socio' => trim($clienteCodigo),
            'periodo' => $periodo,
            'aportacionP' => $aporteP,
            'aportacionD' => $aporteD,
            'diferencia' => $diferencia,
            'aportacion' => $aporteP,
            'fechaPago' => date('Y-m-d')
        ]);
    }

    /**
     * Inserta movimiento en f15_movimiento
     */
    private function insertarMovimientoAporteCoopam(PDO $pdo, int $codigoMovimiento, array $datos, float $montoTotal): void
    {
        $sql = "INSERT INTO f15_movimiento
                (f15_codigo, f15_fecha, f15_operacion, f15_cliente, f15_maprestamo,
                 f15_formaPago, f15_usuaCrea, f15_fechaCrea, f15_obs, f15_obs2, f15_std)
                VALUES (:codigo, :fecha, :operacion, :cliente, :maprestamo,
                        :formaPago, :usuario, NOW(), :obs, :obs2, :cuota)";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([
            'codigo' => $codigoMovimiento,
            'fecha' => date('Y-m-d'),
            'operacion' => 9,
            'cliente' => trim($datos['cliente']),
            'maprestamo' => $montoTotal,
            'formaPago' => trim($datos['formaPago'] ?? ''),
            'usuario' => trim($datos['usuario'] ?? 'SISTEMA'),
            'obs' => trim($datos['obs'] ?? ''),
            'obs2' => trim($datos['obs2'] ?? ''),
            'cuota' => 0,
        ]);
    }

    /**
     * Inserta múltiples registros en diario contable (por componente y por mes)
     */
    private function insertarDiarioContableMultiple(PDO $pdo, int $codigoMovimiento, array $datos, array $datosContables, float $montoPorMes, int $numMeses): void
    {
        $sql = "INSERT INTO f37_diario_contable
                (f37_movimiento, f37_fecha, f37_codigo_contable, f37_operacion, f37_descripcion,
                 f37_debe, f37_haber, f37_usuario_crea, f37_fecha_crea, f37_estado)
                VALUES (:movimiento, :fecha, :codigoContable, :operacion, :descripcion,
                        :debe, :haber, :usuario, NOW(), 'ACTIVO')";

        $stmt = $pdo->prepare($sql);

        // Asumimos que datosContables tiene la estructura:
        // ['aporte' => ['codigo' => '...', 'tipo' => '...', 'monto' => ...],
        //  'total' => ['codigo' => '...', 'tipo' => '...', 'monto' => ...]]
        foreach ($datosContables as $componente => $config) {
            // Solo insertar si hay monto mayor a 0 y tiene código contable
            if ($config['monto'] > 0 && !empty($config['codigo']) && !empty($config['tipo'])) {
                $tipo = $config['tipo'];
                $monto = $config['monto'];

                if ($componente === 'aporte' && $numMeses > 0) {
                    // Insertar una fila por cada mes para el componente aporte
                    for ($i = 0; $i <= $numMeses; $i++) {
                        $debe = $tipo === 'debe' ? $montoPorMes : 0.00;
                        $haber = $tipo === 'haber' ? $montoPorMes : 0.00;

                        $descripcion = 'PAGO APORTES COOPAM - MES ' . ($i + 1);

                        $stmt->execute([
                            'movimiento' => $codigoMovimiento,
                            'fecha' => date('Y-m-d'),
                            'codigoContable' => trim($config['codigo']),
                            'operacion' => 9,
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

                    $descripcion = $componente === 'total' ? 'PAGO APORTES COOPAM - TOTAL' : 'PAGO APORTES COOPAM';

                    $stmt->execute([
                        'movimiento' => $codigoMovimiento,
                        'fecha' => date('Y-m-d'),
                        'codigoContable' => trim($config['codigo']),
                        'operacion' => 9,
                        'descripcion' => $descripcion,
                        'debe' => $debe,
                        'haber' => $haber,
                        'usuario' => trim($datos['usuario'] ?? 'SISTEMA'),
                    ]);
                }
            }
        }
    }

    /**
     * Inserta registro en diario contable (método antiguo, por compatibilidad)
     */
    private function insertarDiarioContable(PDO $pdo, int $codigoMovimiento, array $datos, array $datosContables): void
    {
        $sql = "INSERT INTO f37_diario_contable
                (f37_movimiento, f37_fecha, f37_codigo_contable, f37_operacion, f37_descripcion,
                 f37_debe, f37_haber, f37_usuario_crea, f37_fecha_crea, f37_estado)
                VALUES (:movimiento, :fecha, :codigoContable, :operacion, :descripcion,
                        :debe, :haber, :usuario, NOW(), 'ACTIVO')";
        $stmt = $pdo->prepare($sql);

        $tipoAsiento = $datosContables['tipoAsiento'] ?? 'haber';
        $monto = (float) $datos['monto'];

        $debe = $tipoAsiento === 'debe' ? $monto : 0.00;
        $haber = $tipoAsiento === 'haber' ? $monto : 0.00;

        $stmt->execute([
            'movimiento' => $codigoMovimiento,
            'fecha' => date('Y-m-d'),
            'codigoContable' => trim($datosContables['codigoContable'] ?? '1651'),
            'operacion' => 9,
            'descripcion' => trim($datosContables['descripcionContable'] ?? 'PAGO APORTES COOPAM'),
            'debe' => $debe,
            'haber' => $haber,
            'usuario' => trim($datos['usuario'] ?? 'SISTEMA'),
        ]);
    }
}
