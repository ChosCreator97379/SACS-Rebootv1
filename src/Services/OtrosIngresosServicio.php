<?php

declare(strict_types=1);

namespace Sacs\Services;

use PDO;
use RuntimeException;

final class OtrosIngresosServicio
{
    public function __construct(
        private readonly PDO $pdo
    ) {
    }

    /**
     * Ejecuta un ingreso genérico (Operación 16 - Otros Ingresos)
     *
     * @param array $datos Datos del formulario
     * @param array $datosContables Datos contables (codigoContable, tipoAsiento, descripcionContable)
     * @return array Resultado de la operación
     */
    public function ejecutarOtroIngreso(array $datos, array $datosContables = []): array
    {
        $logFile = dirname(__DIR__, 2) . '/logs/otros_ingresos.log';
        
        // Asegurar que el directorio de logs existe
        if (!is_dir(dirname($logFile))) {
            mkdir(dirname($logFile), 0755, true);
        }

        try {
            // Registrar inicio de operación
            $logMessage = sprintf(
                "[%s] INICIO - Cliente: %s, Monto: %s\n",
                date('Y-m-d H:i:s'),
                $datos['cliente'],
                $datos['monto'] ?? ''
            );
            file_put_contents($logFile, $logMessage, FILE_APPEND);

            $pdo = $this->pdo;
            $pdo->beginTransaction();

            // 1. Validar datos básicos
            if (trim($datos['cliente']) === '' || trim($datos['monto']) === '') {
                throw new RuntimeException('Cliente y monto son requeridos');
            }

            $monto = (float) $datos['monto'];
            if ($monto <= 0) {
                throw new RuntimeException('El monto debe ser mayor a cero');
            }

            // 2. Generar código de movimiento
            $codigoMovimiento = $this->generarCodigoMovimiento($pdo);

            // 3. Insertar movimiento de ingreso
            $this->insertarMovimientoIngreso(
                $pdo,
                $codigoMovimiento,
                $datos,
                $monto
            );

            // 4. Insertar en diario contable si se proporcionaron datos
            if (!empty($datosContables['codigoContable'])) {
                $this->insertarDiarioContable($pdo, $codigoMovimiento, $datos, $datosContables, $monto);
            }

            $pdo->commit();

            // Registrar éxito
            $logMessage = sprintf(
                "[%s] EXITO - Movimiento: %d, Cliente: %s, Monto: %.2f\n",
                date('Y-m-d H:i:s'),
                $codigoMovimiento,
                $datos['cliente'],
                $monto
            );
            file_put_contents($logFile, $logMessage, FILE_APPEND);

            return [
                'exito' => true,
                'codigo_movimiento' => $codigoMovimiento,
                'mensaje' => 'Otro ingreso procesado correctamente',
                'monto' => $monto,
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
                'mensaje' => 'Error en otro ingreso: ' . $e->getMessage(),
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
                'mensaje' => 'Error inesperado al procesar otro ingreso',
            ];
        }
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
     * Inserta movimiento de ingreso en f15_movimiento
     */
    private function insertarMovimientoIngreso(PDO $pdo, int $codigoMovimiento, array $datos, float $monto): void
    {
        $sql = "INSERT INTO f15_movimiento (
                    f15_codigo, f15_fecha, f15_operacion, f15_cliente,
                    f15_ingreso, f15_formaPago, f15_usuaCrea, f15_fechaCrea, f15_obs, f15_obs2
                ) VALUES (
                    :codigo, NOW(), 16, :cliente,
                    :monto, :formaPago, :usuario, NOW(), :obs, :obs2
                )";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([
            'codigo' => $codigoMovimiento,
            'cliente' => trim($datos['cliente']),
            'monto' => $monto,
            'formaPago' => $datos['formaPago'] ?? 'EFECTIVO',
            'usuario' => $datos['usuario'] ?? 'SISTEMA',
            'obs' => $datos['obs'] ?? 'OTRO INGRESO',
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
                    :movimiento, CURDATE(), :codigoContable, 16,
                    :debe, :haber, :descripcion, :usuario, NOW(), 'ACTIVO'
                )";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([
            'movimiento' => $codigoMovimiento,
            'codigoContable' => $datosContables['codigoContable'] ?? '1651',
            'debe' => $tipoAsiento === 'debe' ? $monto : 0,
            'haber' => $tipoAsiento === 'haber' ? $monto : 0,
            'descripcion' => $datosContables['descripcionContable'] ?? 'Otro Ingreso',
            'usuario' => $datos['usuario'] ?? 'SISTEMA'
        ]);
    }
}
