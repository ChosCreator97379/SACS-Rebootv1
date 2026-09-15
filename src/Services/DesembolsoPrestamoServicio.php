<?php

declare(strict_types=1);

namespace Sacs\Services;

use PDO;
use RuntimeException;

/**
 * Servicio para procesar desembolso de préstamos (Operación 3)
 * Basado en CrearCajaAction.registrar() - Operación 3
 */
final class DesembolsoPrestamoServicio
{
    public function __construct(
        private readonly PDO $pdo
    ) {
    }

    /**
     * Ejecuta un desembolso de préstamo (Operación 3)
     *
     * @param array $datos Datos del formulario
     * @param array $datosContables Datos contables (codigoContable, tipoAsiento, descripcionContable)
     * @return array Resultado de la operación
     */
    public function ejecutarDesembolsoPrestamo(array $datos, array $datosContables = []): array
    {
        $logFile = dirname(__DIR__, 2) . '/logs/desembolso_prestamo.log';
        
        // Asegurar que el directorio de logs existe
        if (!is_dir(dirname($logFile))) {
            mkdir(dirname($logFile), 0755, true);
        }
        
        try {
            // Registrar inicio de operación
            $logMessage = sprintf(
                "[%s] INICIO - Cliente: %s, Préstamo: %d, Monto: %.2f\n",
                date('Y-m-d H:i:s'),
                $datos['cliente'],
                $datos['prestamo'],
                $datos['monto']
            );
            file_put_contents($logFile, $logMessage, FILE_APPEND);

            // Iniciar transacción
            $this->pdo->beginTransaction();

            // 1. Obtener datos del préstamo
            $prestamo = $this->obtenerPrestamo($this->pdo, (int)$datos['prestamo']);
            if ($prestamo === null) {
                throw new RuntimeException('Préstamo no encontrado');
            }

            // 2. Generar código de movimiento
            $codigoMovimiento = $this->generarCodigoMovimiento($this->pdo);

            // 3. Insertar movimiento (f15_movimiento)
            $this->insertarMovimiento($this->pdo, $codigoMovimiento, $datos, $prestamo);

            // 4. Actualizar préstamo (desembolso)
            $this->actualizarPrestamoDesembolso($this->pdo, (int)$datos['prestamo'], $prestamo);

            // 5. Insertar en diario contable si se proporcionaron datos
            if (!empty($datosContables)) {
                $this->insertarDiarioContable($this->pdo, $codigoMovimiento, $datos, $datosContables);
            }

            $this->pdo->commit();
            
            // Registrar éxito
            $logMessage = sprintf(
                "[%s] EXITO - Movimiento: %d, Cliente: %s, Préstamo: %d\n",
                date('Y-m-d H:i:s'),
                $codigoMovimiento,
                $datos['cliente'],
                $datos['prestamo']
            );
            file_put_contents($logFile, $logMessage, FILE_APPEND);
            
            return [
                'exito' => true,
                'codigo_movimiento' => $codigoMovimiento,
                'mensaje' => 'Desembolso de préstamo procesado correctamente',
            ];
            
        } catch (RuntimeException $e) {
            // Rollback ante error
            if ($this->pdo->inTransaction()) {
                $this->pdo->rollBack();
            }
            // Registrar error
            $logMessage = sprintf(
                "[%s] ERROR RUNTIME - Cliente: %s, Préstamo: %d, Error: %s\n",
                date('Y-m-d H:i:s'),
                $datos['cliente'] ?? 'N/A',
                $datos['prestamo'] ?? 'N/A',
                $e->getMessage()
            );
            file_put_contents($logFile, $logMessage, FILE_APPEND);
            
            return [
                'exito' => false,
                'error' => $e->getMessage(),
                'mensaje' => 'Error en desembolso de préstamo: ' . $e->getMessage(),
            ];
        } catch (\PDOException $e) {
            // Rollback ante error de base de datos
            if ($this->pdo->inTransaction()) {
                $this->pdo->rollBack();
            }
            // Registrar error de base de datos
            $logMessage = sprintf(
                "[%s] ERROR DB - Cliente: %s, Préstamo: %d, Error: %s\n",
                date('Y-m-d H:i:s'),
                $datos['cliente'] ?? 'N/A',
                $datos['prestamo'] ?? 'N/A',
                $e->getMessage()
            );
            file_put_contents($logFile, $logMessage, FILE_APPEND);
            
            return [
                'exito' => false,
                'error' => $e->getMessage(),
                'mensaje' => 'Error de base de datos en desembolso de préstamo: ' . $e->getMessage(),
            ];
        }
    }

    /**
     * Obtiene datos del préstamo
     */
    private function obtenerPrestamo(PDO $pdo, int $prestamoCodigo): ?array
    {
        $sql = "SELECT * FROM f16_prestamo WHERE f16_codigo = ? LIMIT 1";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$prestamoCodigo]);
        $result = $stmt->fetch();
        return $result ?: null;
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
     * Inserta movimiento en f15_movimiento
     */
    private function insertarMovimiento(PDO $pdo, int $codigoMovimiento, array $datos, array $prestamo): void
    {
        $sql = "INSERT INTO f15_movimiento
                (f15_codigo, f15_fecha, f15_operacion, f15_cliente, f15_prestamo, f15_maprestamo,
                 f15_gastosa, f15_formaPago, f15_usuaCrea, f15_fechaCrea, f15_obs, f15_obs2)
                VALUES (:codigo, :fecha, :operacion, :cliente, :prestamo, :maprestamo,
                        :gastosa, :formaPago, :usuario, NOW(), :obs, :obs2)";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([
            'codigo' => $codigoMovimiento,
            'fecha' => $datos['fecha'] ?? date('Y-m-d'),
            'operacion' => 3,
            'cliente' => trim($datos['cliente']),
            'prestamo' => $datos['prestamo'],
            'maprestamo' => $prestamo['f16_importe'] ?? 0,
            'gastosa' => $prestamo['f16_gastosa'] ?? 0,
            'formaPago' => trim($datos['formaPago'] ?? ''),
            'usuario' => trim($datos['usuario'] ?? 'SISTEMA'),
            'obs' => trim($datos['obs'] ?? ''),
            'obs2' => trim($datos['obs2'] ?? ''),
        ]);
    }

    /**
     * Actualiza el préstamo para marcarlo como desembolsado
     */
    private function actualizarPrestamoDesembolso(PDO $pdo, int $prestamoCodigo, array $prestamo): void
    {
        // Actualizar estado a desembolsado (estado 4 según el legacy)
        // Actualizar monto desembolsado
        // Actualizar fecha de desembolso
        $sql = "UPDATE f16_prestamo 
                SET f16_estado = 4,
                    f16_desembolso = f16_importe,
                    f16_fechaUP = NOW()
                WHERE f16_codigo = ?";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$prestamoCodigo]);
    }

    /**
     * Inserta en diario contable
     */
    private function insertarDiarioContable(PDO $pdo, int $codigoMovimiento, array $datos, array $datosContables): void
    {
        $sql = "INSERT INTO f37_diario_contable
                (f37_movimiento, f37_fecha, f37_codigo_contable, f37_operacion, f37_descripcion,
                 f37_debe, f37_haber, f37_usuario_crea, f37_fecha_crea, f37_estado)
                VALUES (:movimiento, :fecha, :codigoContable, :operacion, :descripcion,
                        :debe, :haber, :usuario, NOW(), 'ACTIVO')";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([
            'movimiento' => $codigoMovimiento,
            'fecha' => $datos['fecha'] ?? date('Y-m-d'),
            'codigoContable' => trim($datosContables['codigoContable'] ?? ''),
            'operacion' => 3,
            'descripcion' => trim($datosContables['descripcionContable'] ?? 'DESEMBOLSO PRESTAMO'),
            'debe' => 0,
            'haber' => (float)($datos['monto'] ?? 0),
            'usuario' => trim($datos['usuario'] ?? 'SISTEMA'),
        ]);
    }
}
