<?php

declare(strict_types=1);

namespace Sacs\Services;

use PDO;
use RuntimeException;

final class DevolucionFonbesServicio
{
    public function __construct(
        private readonly PDO $pdo
    ) {
    }

    /**
     * Ejecuta una devolución de aportes FONBES (Operación 4)
     *
     * @param array $datos Datos del formulario
     * @param array $datosContables Datos contables (codigoContable, tipoAsiento, descripcionContable)
     * @return array Resultado de la operación
     */
    public function ejecutarDevolucionFonbes(array $datos, array $datosContables = []): array
    {
        $logFile = dirname(__DIR__, 2) . '/logs/devolucion_fonbes.log';
        
        // Asegurar que el directorio de logs existe
        if (!is_dir(dirname($logFile))) {
            mkdir(dirname($logFile), 0755, true);
        }

        try {
            // Registrar inicio de operación
            $logMessage = sprintf(
                "[%s] INICIO - Cliente: %s, Periodo: %s, Monto: %.2f\n",
                date('Y-m-d H:i:s'),
                $datos['cliente'],
                $datos['periodo'],
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

            // 2. Obtener aporte existente para el periodo
            $aporteExistente = $this->obtenerAportePeriodo($pdo, $datos['cliente'], $datos['periodo']);
            if ($aporteExistente === null) {
                throw new RuntimeException('No existe aporte para el periodo especificado');
            }

            // 3. Validar que el monto a devolver no exceda lo pagado
            $montoPagado = (float) ($aporteExistente['f22_aportacionD'] ?? 0);
            $montoDevolucion = (float) $datos['monto'];
            if ($montoDevolucion > $montoPagado) {
                throw new RuntimeException('El monto a devolver excede el monto pagado');
            }

            // 4. Generar código de movimiento
            $codigoMovimiento = $this->generarCodigoMovimiento($pdo);

            // 5. Actualizar aporte en f22_aportes
            $nuevoPagado = $montoPagado - $montoDevolucion;
            $nuevaDiferencia = (float) ($aporteExistente['f22_aportacionP'] ?? 0) - $nuevoPagado;
            
            $this->actualizarAporteFonbes(
                $pdo,
                $datos['cliente'],
                $datos['periodo'],
                $nuevoPagado,
                $nuevaDiferencia
            );

            // 6. Insertar movimiento
            $this->insertarMovimientoDevolucion(
                $pdo,
                $codigoMovimiento,
                $datos,
                $montoDevolucion
            );

            // 7. Insertar en diario contable si se proporcionaron datos
            if (!empty($datosContables['codigoContable'])) {
                $this->insertarDiarioContable($pdo, $codigoMovimiento, $datos, $datosContables);
            }

            $pdo->commit();

            // Registrar éxito
            $logMessage = sprintf(
                "[%s] EXITO - Movimiento: %d, Cliente: %s, Monto: %.2f\n",
                date('Y-m-d H:i:s'),
                $codigoMovimiento,
                $datos['cliente'],
                $montoDevolucion
            );
            file_put_contents($logFile, $logMessage, FILE_APPEND);

            return [
                'exito' => true,
                'codigo_movimiento' => $codigoMovimiento,
                'mensaje' => 'Devolución de aportes procesada correctamente',
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
                'mensaje' => 'Error en devolución de aportes: ' . $e->getMessage(),
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
                'mensaje' => 'Error inesperado al procesar devolución de aportes',
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
     * Obtiene aporte existente para un periodo específico
     */
    private function obtenerAportePeriodo(PDO $pdo, string $clienteCodigo, string $periodo): ?array
    {
        $sql = "SELECT f22_aportacionP, f22_aportacionD, f22_diferencia
                FROM f22_aportes
                WHERE f22_socio = CAST(:cliente AS UNSIGNED)
                  AND f22_periodo = :periodo
                LIMIT 1";
        $stmt = $pdo->prepare($sql);
        $stmt->execute(['cliente' => trim($clienteCodigo), 'periodo' => $periodo]);
        $result = $stmt->fetch();
        return $result === false ? null : $result;
    }

    /**
     * Actualiza aporte en f22_aportes
     */
    private function actualizarAporteFonbes(PDO $pdo, string $clienteCodigo, string $periodo, float $nuevoPagado, float $nuevaDiferencia): void
    {
        $sql = "UPDATE f22_aportes
                SET f22_aportacionD = :nuevoPagado,
                    f22_diferencia = :nuevaDiferencia
                WHERE f22_socio = CAST(:cliente AS UNSIGNED)
                  AND f22_periodo = :periodo";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([
            'nuevoPagado' => $nuevoPagado,
            'nuevaDiferencia' => $nuevaDiferencia,
            'cliente' => trim($clienteCodigo),
            'periodo' => $periodo
        ]);
    }

    /**
     * Genera código de movimiento de forma segura
     */
    private function generarCodigoMovimiento(PDO $pdo): int
    {
        $sql = "SELECT MAX(f15_codigo) as max_codigo FROM f15_movimiento FOR UPDATE";
        $stmt = $pdo->prepare($sql);
        $stmt->execute();
        $result = $stmt->fetch();
        $maxCodigo = (int) ($result['max_codigo'] ?? 122013479);
        return $maxCodigo + 1;
    }

    /**
     * Inserta movimiento de devolución en f15_movimiento
     */
    private function insertarMovimientoDevolucion(PDO $pdo, int $codigoMovimiento, array $datos, float $monto): void
    {
        $sql = "INSERT INTO f15_movimiento (
                    f15_codigo, f15_fecha, f15_operacion, f15_cliente,
                    f15_mretiro, f15_formaPago, f15_usuaCrea, f15_fechaCrea,
                    f15_obs, f15_obs2
                ) VALUES (
                    :codigo, NOW(), 4, :cliente,
                    :monto, :formaPago, :usuario, NOW(),
                    :obs, :obs2
                )";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([
            'codigo' => $codigoMovimiento,
            'cliente' => trim($datos['cliente']),
            'monto' => $monto,
            'formaPago' => $datos['formaPago'] ?? 'EFECTIVO',
            'usuario' => $datos['usuario'] ?? 'SISTEMA',
            'obs' => $datos['obs'] ?? '',
            'obs2' => $datos['obs2'] ?? ''
        ]);
    }

    /**
     * Inserta en diario contable
     */
    private function insertarDiarioContable(PDO $pdo, int $codigoMovimiento, array $datos, array $datosContables): void
    {
        $tipoAsiento = $datosContables['tipoAsiento'] ?? 'haber';
        $monto = (float) $datos['monto'];
        
        $sql = "INSERT INTO f37_diario_contable (
                    f37_movimiento, f37_fecha, f37_codigo_contable, f37_operacion,
                    f37_debe, f37_haber, f37_descripcion, f37_usuario_crea, f37_fecha_crea, f37_estado
                ) VALUES (
                    :movimiento, CURDATE(), :codigoContable, 4,
                    :debe, :haber, :descripcion, :usuario, NOW(), 'ACTIVO'
                )";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([
            'movimiento' => $codigoMovimiento,
            'codigoContable' => $datosContables['codigoContable'] ?? '1651',
            'debe' => $tipoAsiento === 'debe' ? $monto : 0,
            'haber' => $tipoAsiento === 'haber' ? $monto : 0,
            'descripcion' => $datosContables['descripcionContable'] ?? 'Devolución Aportes FONBES',
            'usuario' => $datos['usuario'] ?? 'SISTEMA'
        ]);
    }
}
