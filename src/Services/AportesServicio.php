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
     * @param array $datosContables Datos contables (codigoContable, tipoAsiento, descripcionContable)
     * @return array Resultado de la operación
     */
    public function ejecutarPagoAportesFonbes(array $datos, array $datosContables = []): array
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

            for ($mes = $periodoInicio; $mes <= $periodoFin; $mes++) {
                $mesStr = str_pad((string) $mes, 2, '0', STR_PAD_LEFT);
                $periodo = "{$datos['anio']}-{$mesStr}-01";

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
            }

            // 7. Insertar movimiento
            $this->insertarMovimientoAporte(
                $pdo,
                $codigoMovimiento,
                $datos,
                $montoTotal
            );

            // 8. Insertar en diario contable si se proporcionaron datos
            if (!empty($datosContables)) {
                $this->insertarDiarioContable($pdo, $codigoMovimiento, $datos, $datosContables);
            }

            $pdo->commit();

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
                'mensaje' => 'Pago de aportes procesado correctamente',
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
     * Obtiene aporte final (equivalente a getAportacionFinal2 del legacy)
     */
    private function obtenerAporteFinal(PDO $pdo, string $clienteCodigo): float
    {
        // TODO: Implementar lógica de getAportacionFinal2
        // Por ahora retorna 0 hasta analizar el DAO correspondiente
        return 0.0;
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
                (f22_socio, f22_periodo, f22_aportacionP, f22_aportacionD, f22_diferencia)
                VALUES (:socio, :periodo, :aportacionP, :aportacionD, :diferencia)";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([
            'socio' => trim($clienteCodigo),
            'periodo' => $periodo,
            'aportacionP' => $aporteP,
            'aportacionD' => $aporteD,
            'diferencia' => $diferencia,
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
     * Inserta registro en diario contable
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
            'operacion' => 1,
            'descripcion' => trim($datosContables['descripcionContable'] ?? 'PAGO APORTES FONBES'),
            'debe' => $debe,
            'haber' => $haber,
            'usuario' => trim($datos['usuario'] ?? 'SISTEMA'),
        ]);
    }
}
