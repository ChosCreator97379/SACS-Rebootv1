<?php

declare(strict_types=1);

namespace Sacs\Services;

use PDO;
use RuntimeException;

final class PagoRifaServicio
{
    public function __construct(
        private readonly PDO $pdo
    ) {
    }

    /**
     * Ejecuta un pago de rifa (Operación 12)
     *
     * @param array $datos Datos del formulario
     * @param array $datosContables Datos contables (codigoContable, tipoAsiento, descripcionContable)
     * @return array Resultado de la operación
     */
    public function ejecutarPagoRifa(array $datos, array $datosContables = []): array
    {
        $logFile = dirname(__DIR__, 2) . '/logs/pago_rifa.log';
        
        // Asegurar que el directorio de logs existe
        if (!is_dir(dirname($logFile))) {
            mkdir(dirname($logFile), 0755, true);
        }

        try {
            // Registrar inicio de operación
            $logMessage = sprintf(
                "[%s] INICIO - Cliente: %s, Periodo: %s\n",
                date('Y-m-d H:i:s'),
                $datos['cliente'],
                $datos['periodo'] ?? ''
            );
            file_put_contents($logFile, $logMessage, FILE_APPEND);

            $pdo = $this->pdo;
            $pdo->beginTransaction();

            // 1. Obtener rifa pendiente del cliente
            $rifa = $this->obtenerRifaPendiente($pdo, (int) $datos['cliente']);
            if ($rifa === null) {
                throw new RuntimeException('No hay rifa pendiente para este cliente');
            }

            // 2. Validar que el monto sea válido
            $monto = (float) $datos['monto'];
            if ($monto <= 0) {
                throw new RuntimeException('El monto debe ser mayor a cero');
            }

            // 3. Calcular nuevo sobregiro si existe
            $sobregiro = (float) ($rifa['f64_rifar'] ?? 0);
            $nuevoSobregiro = max(0, $sobregiro - $monto);

            // 4. Generar código de movimiento
            $codigoMovimiento = $this->generarCodigoMovimiento($pdo);

            // 5. Actualizar rifa
            $this->actualizarRifa($pdo, (int) $rifa['f64_codigo'], $monto, $nuevoSobregiro);

            // 6. Insertar movimiento de pago
            $this->insertarMovimientoPago(
                $pdo,
                $codigoMovimiento,
                $datos,
                $monto
            );

            // 7. Insertar en diario contable si se proporcionaron datos
            if (!empty($datosContables['codigoContable'])) {
                $this->insertarDiarioContable($pdo, $codigoMovimiento, $datos, $datosContables, $monto);
            }

            $pdo->commit();

            // Registrar éxito
            $logMessage = sprintf(
                "[%s] EXITO - Movimiento: %d, Cliente: %s, Monto: %.2f, Nuevo Sobregiro: %.2f\n",
                date('Y-m-d H:i:s'),
                $codigoMovimiento,
                $datos['cliente'],
                $monto,
                $nuevoSobregiro
            );
            file_put_contents($logFile, $logMessage, FILE_APPEND);

            return [
                'exito' => true,
                'codigo_movimiento' => $codigoMovimiento,
                'mensaje' => 'Pago de rifa procesado correctamente',
                'nuevo_sobregiro' => $nuevoSobregiro,
            ];

        } catch (RuntimeException $e) {
            if ($pdo->inTransaction()) {
                $pdo->rollBack();
            }
            $logMessage = sprintf(
                "[%s] ERROR RUNTIME - Cliente: %s, Error: %s\n",
                date('Y-m-d H:i:s'),
                $datos['cliente'] ?? '',
                $e->getMessage()
            );
            file_put_contents($logFile, $logMessage, FILE_APPEND);

            return [
                'exito' => false,
                'error' => $e->getMessage(),
                'mensaje' => 'Error en pago de rifa: ' . $e->getMessage(),
            ];
        } catch (\Throwable $e) {
            if ($pdo->inTransaction()) {
                $pdo->rollBack();
            }
            $logMessage = sprintf(
                "[%s] ERROR INESPERADO - Cliente: %s, Error: %s\n",
                date('Y-m-d H:i:s'),
                $datos['cliente'] ?? '',
                $e->getMessage()
            );
            file_put_contents($logFile, $logMessage, FILE_APPEND);

            return [
                'exito' => false,
                'error' => 'Error inesperado: ' . $e->getMessage(),
                'mensaje' => 'Error inesperado al procesar pago de rifa',
            ];
        }
    }

    /**
     * Obtiene rifa pendiente del cliente
     */
    private function obtenerRifaPendiente(PDO $pdo, int $clienteId): ?array
    {
        $sql = "SELECT f64_codigo, f64_socio, f64_periodo, f64_rifap, f64_rifad, f64_rifar
                FROM f64_rifa
                WHERE CAST(f64_socio AS UNSIGNED) = :cliente
                  AND (f64_rifap > 0 OR f64_rifar > 0)
                ORDER BY f64_periodo DESC
                LIMIT 1";
        $stmt = $pdo->prepare($sql);
        $stmt->execute(['cliente' => $clienteId]);
        $result = $stmt->fetch();
        return $result === false ? null : $result;
    }

    /**
     * Actualiza rifa con el pago
     */
    private function actualizarRifa(PDO $pdo, int $rifaId, float $montoPagado, float $nuevoSobregiro): void
    {
        $sql = "UPDATE f64_rifa
                SET f64_rifad = f64_rifad + :montoPagado,
                    f64_rifar = :nuevoSobregiro,
                    f64_fechaPago = NOW(),
                    f64_formaPago = 'EFECTIVO'
                WHERE f64_codigo = :rifa";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([
            'montoPagado' => $montoPagado,
            'nuevoSobregiro' => $nuevoSobregiro,
            'rifa' => $rifaId
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
     * Inserta movimiento de pago en f15_movimiento
     */
    private function insertarMovimientoPago(PDO $pdo, int $codigoMovimiento, array $datos, float $monto): void
    {
        $sql = "INSERT INTO f15_movimiento (
                    f15_codigo, f15_fecha, f15_operacion, f15_cliente,
                    f15_mretiro, f15_formaPago, f15_usuaCrea, f15_fechaCrea, f15_obs, f15_obs2
                ) VALUES (
                    :codigo, NOW(), 12, :cliente,
                    :monto, :formaPago, :usuario, NOW(), :obs, :obs2
                )";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([
            'codigo' => $codigoMovimiento,
            'cliente' => trim($datos['cliente']),
            'monto' => $monto,
            'formaPago' => $datos['formaPago'] ?? 'EFECTIVO',
            'usuario' => $datos['usuario'] ?? 'SISTEMA',
            'obs' => $datos['obs'] ?? 'PAGO DE RIFA',
            'obs2' => $datos['obs2'] ?? ''
        ]);
    }

    /**
     * Inserta en diario contable
     */
    private function insertarDiarioContable(PDO $pdo, int $codigoMovimiento, array $datos, array $datosContables, float $monto): void
    {
        $tipoAsiento = $datosContables['tipoAsiento'] ?? 'haber';
        
        $sql = "INSERT INTO f37_diario_contable (
                    f37_movimiento, f37_fecha, f37_codigo_contable, f37_operacion,
                    f37_debe, f37_haber, f37_descripcion, f37_usuario_crea, f37_fecha_crea, f37_estado
                ) VALUES (
                    :movimiento, CURDATE(), :codigoContable, 12,
                    :debe, :haber, :descripcion, :usuario, NOW(), 'ACTIVO'
                )";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([
            'movimiento' => $codigoMovimiento,
            'codigoContable' => $datosContables['codigoContable'] ?? '1651',
            'debe' => $tipoAsiento === 'debe' ? $monto : 0,
            'haber' => $tipoAsiento === 'haber' ? $monto : 0,
            'descripcion' => $datosContables['descripcionContable'] ?? 'Pago Rifa',
            'usuario' => $datos['usuario'] ?? 'SISTEMA'
        ]);
    }
}
