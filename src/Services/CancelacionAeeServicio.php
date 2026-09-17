<?php

declare(strict_types=1);

namespace Sacs\Services;

use PDO;
use RuntimeException;

final class CancelacionAeeServicio
{
    public function __construct(
        private readonly PDO $pdo
    ) {
    }

    /**
     * Ejecuta una cancelación de préstamo AEE (Operación 6)
     *
     * @param array $datos Datos del formulario
     * @param array $datosContables Datos contables (codigoContable, tipoAsiento, descripcionContable)
     * @return array Resultado de la operación
     */
    public function ejecutarCancelacionAee(array $datos, array $datosContables = []): array
    {
        $logFile = dirname(__DIR__, 2) . '/logs/cancelacion_aee.log';
        
        // Asegurar que el directorio de logs existe
        if (!is_dir(dirname($logFile))) {
            mkdir(dirname($logFile), 0755, true);
        }

        try {
            // Registrar inicio de operación
            $logMessage = sprintf(
                "[%s] INICIO - Cliente: %s, Préstamo: %s\n",
                date('Y-m-d H:i:s'),
                $datos['cliente'],
                $datos['prestamo']
            );
            file_put_contents($logFile, $logMessage, FILE_APPEND);

            $pdo = $this->pdo;
            $pdo->beginTransaction();

            // 1. Obtener datos del préstamo
            $prestamo = $this->obtenerPrestamo($pdo, (int) $datos['prestamo']);
            if ($prestamo === null) {
                throw new RuntimeException('Préstamo no encontrado');
            }

            // 2. Validar que el préstamo esté activo (estado 4)
            if ((int) $prestamo['f16_estado'] !== 4) {
                throw new RuntimeException('El préstamo no está activo para cancelación');
            }

            // 3. Validar que el cliente coincida
            if ((int) $prestamo['f16_cliente'] !== (int) $datos['cliente']) {
                throw new RuntimeException('El préstamo no pertenece al cliente especificado');
            }

            // 4. Calcular total a cancelar (saldo + sobregiro)
            $saldo = (float) ($prestamo['f16_saldo'] ?? 0);
            $sobregiro = (float) ($prestamo['f16_aportacion'] ?? 0);
            $totalCancelacion = $saldo + $sobregiro;

            // 5. Generar código de movimiento
            $codigoMovimiento = $this->generarCodigoMovimiento($pdo);

            // 6. Cancelar cuotas pendientes del préstamo
            $this->cancelarCuotas($pdo, (int) $datos['prestamo']);

            // 7. Cancelar el préstamo (actualizar estado y fecha de cancelación)
            $this->cancelarPrestamo($pdo, (int) $datos['prestamo'], $totalCancelacion);

            // 8. Insertar movimiento de cancelación
            $this->insertarMovimientoCancelacion(
                $pdo,
                $codigoMovimiento,
                $datos,
                $prestamo,
                $totalCancelacion
            );

            // 9. Insertar en diario contable si se proporcionaron datos
            if (!empty($datosContables['codigoContable'])) {
                $this->insertarDiarioContable($pdo, $codigoMovimiento, $datos, $datosContables, $totalCancelacion);
            }

            $pdo->commit();

            // Registrar éxito
            $logMessage = sprintf(
                "[%s] EXITO - Movimiento: %d, Préstamo: %s, Total: %.2f\n",
                date('Y-m-d H:i:s'),
                $codigoMovimiento,
                $datos['prestamo'],
                $totalCancelacion
            );
            file_put_contents($logFile, $logMessage, FILE_APPEND);

            return [
                'exito' => true,
                'codigo_movimiento' => $codigoMovimiento,
                'mensaje' => 'Cancelación de préstamo procesada correctamente',
            ];

        } catch (RuntimeException $e) {
            if ($pdo->inTransaction()) {
                $pdo->rollBack();
            }
            $logMessage = sprintf(
                "[%s] ERROR RUNTIME - Préstamo: %s, Error: %s\n",
                date('Y-m-d H:i:s'),
                $datos['prestamo'] ?? '',
                $e->getMessage()
            );
            file_put_contents($logFile, $logMessage, FILE_APPEND);

            return [
                'exito' => false,
                'error' => $e->getMessage(),
                'mensaje' => 'Error en cancelación de préstamo: ' . $e->getMessage(),
            ];
        } catch (\Throwable $e) {
            if ($pdo->inTransaction()) {
                $pdo->rollBack();
            }
            $logMessage = sprintf(
                "[%s] ERROR INESPERADO - Préstamo: %s, Error: %s\n",
                date('Y-m-d H:i:s'),
                $datos['prestamo'] ?? '',
                $e->getMessage()
            );
            file_put_contents($logFile, $logMessage, FILE_APPEND);

            return [
                'exito' => false,
                'error' => 'Error inesperado: ' . $e->getMessage(),
                'mensaje' => 'Error inesperado al procesar cancelación de préstamo',
            ];
        }
    }

    /**
     * Obtiene datos del préstamo
     */
    private function obtenerPrestamo(PDO $pdo, int $prestamoId): ?array
    {
        $sql = "SELECT f16_codigo, f16_cliente, f16_estado, f16_saldo, f16_gastosa, f16_gastosm, 
                       f16_otros, f16_otros2, f16_aportacion, f16_cuotasPend, f16_total
                FROM f16_prestamo
                WHERE f16_codigo = :prestamo
                LIMIT 1";
        $stmt = $pdo->prepare($sql);
        $stmt->execute(['prestamo' => $prestamoId]);
        $result = $stmt->fetch();
        return $result === false ? null : $result;
    }

    /**
     * Cancela cuotas pendientes del préstamo
     */
    private function cancelarCuotas(PDO $pdo, int $prestamoId): void
    {
        $sql = "UPDATE f17_cuotasprestamo
                SET f17_estado = 'CANCELADO'
                WHERE f17_prestamo = :prestamo
                  AND f17_estado = 'PENDIENTE'";
        $stmt = $pdo->prepare($sql);
        $stmt->execute(['prestamo' => $prestamoId]);
    }

    /**
     * Cancela el préstamo (actualiza estado y fecha de cancelación)
     */
    private function cancelarPrestamo(PDO $pdo, int $prestamoId, float $totalCancelacion): void
    {
        $sql = "UPDATE f16_prestamo
                SET f16_estado = 5,
                    f16_fechaCancela = CURDATE(),
                    f16_saldo = 0,
                    f16_aportacion = 0,
                    f16_gastosa = 0,
                    f16_gastosm = 0,
                    f16_otros = 0,
                    f16_otros2 = 0,
                    f16_cuotasPend = 0
                WHERE f16_codigo = :prestamo";
        $stmt = $pdo->prepare($sql);
        $stmt->execute(['prestamo' => $prestamoId]);
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
     * Inserta movimiento de cancelación en f15_movimiento
     */
    private function insertarMovimientoCancelacion(PDO $pdo, int $codigoMovimiento, array $datos, array $prestamo, float $total): void
    {
        $sql = "INSERT INTO f15_movimiento (
                    f15_codigo, f15_fecha, f15_operacion, f15_cliente, f15_prestamo,
                    f15_maprestamo, f15_gastosa, f15_gastosm, f15_gastos1, f15_gastos2,
                    f15_formaPago, f15_usuaCrea, f15_fechaCrea, f15_obs, f15_obs2
                ) VALUES (
                    :codigo, NOW(), 6, :cliente, :prestamo,
                    :saldo, :gastosa, :gastosm, :otros1, :otros2,
                    :formaPago, :usuario, NOW(), :obs, :obs2
                )";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([
            'codigo' => $codigoMovimiento,
            'cliente' => trim($datos['cliente']),
            'prestamo' => $prestamo['f16_codigo'],
            'saldo' => $prestamo['f16_saldo'] ?? 0,
            'gastosa' => $prestamo['f16_gastosa'] ?? 0,
            'gastosm' => $prestamo['f16_gastosm'] ?? 0,
            'otros1' => $prestamo['f16_otros'] ?? 0,
            'otros2' => $prestamo['f16_otros2'] ?? 0,
            'formaPago' => $datos['formaPago'] ?? 'EFECTIVO',
            'usuario' => $datos['usuario'] ?? 'SISTEMA',
            'obs' => $datos['obs'] ?? 'CANCELACIÓN DE PRÉSTAMO',
            'obs2' => $datos['obs2'] ?? ''
        ]);
    }

    /**
     * Inserta en diario contable
     */
    private function insertarDiarioContable(PDO $pdo, int $codigoMovimiento, array $datos, array $datosContables, float $monto): void
    {
        $tipoAsiento = $datosContables['tipoAsiento'] ?? 'debe';
        
        $sql = "INSERT INTO f37_diario_contable (
                    f37_movimiento, f37_fecha, f37_codigo_contable, f37_operacion, 
                    f37_debe, f37_haber, f37_descripcion, f37_usuario_crea, f37_fecha_crea, f37_estado
                ) VALUES (
                    :movimiento, CURDATE(), :codigoContable, 6,
                    :debe, :haber, :descripcion, :usuario, NOW(), 'ACTIVO'
                )";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([
            'movimiento' => $codigoMovimiento,
            'codigoContable' => $datosContables['codigoContable'] ?? '1651',
            'debe' => $tipoAsiento === 'debe' ? $monto : 0,
            'haber' => $tipoAsiento === 'haber' ? $monto : 0,
            'descripcion' => $datosContables['descripcionContable'] ?? 'Cancelación Préstamo AEE',
            'usuario' => $datos['usuario'] ?? 'SISTEMA'
        ]);
    }
}
