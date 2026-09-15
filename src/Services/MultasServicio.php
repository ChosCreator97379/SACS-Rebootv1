<?php

declare(strict_types=1);

namespace Sacs\Services;

use PDO;
use RuntimeException;

/**
 * Servicio para procesar pagos de multas (Operación 11)
 * Basado en CrearCajaAction.registrar() - Operación 11
 */
final class MultasServicio
{
    public function __construct(
        private readonly PDO $pdo
    ) {
    }

    /**
     * Ejecuta un pago de multa (Operación 11)
     *
     * @param array $datos Datos del formulario
     * @param array $datosContables Datos contables (codigoContable, tipoAsiento, descripcionContable)
     * @return array Resultado de la operación
     */
    public function ejecutarPagoMulta(array $datos, array $datosContables = []): array
    {
        $logFile = dirname(__DIR__, 2) . '/logs/multas.log';
        
        // Asegurar que el directorio de logs existe
        if (!is_dir(dirname($logFile))) {
            mkdir(dirname($logFile), 0755, true);
        }
        
        try {
            // Registrar inicio de operación
            $logMessage = sprintf(
                "[%s] INICIO - Cliente: %s, Multa ID: %d, Monto: %.2f\n",
                date('Y-m-d H:i:s'),
                $datos['cliente'],
                $datos['multa'],
                $datos['monto']
            );
            file_put_contents($logFile, $logMessage, FILE_APPEND);

            // Iniciar transacción
            $this->pdo->beginTransaction();

            // 1. Obtener datos de la multa
            $multa = $this->obtenerMulta($this->pdo, (int)$datos['multa']);
            if ($multa === null) {
                throw new RuntimeException('Multa no encontrada');
            }

            // 2. Generar código de movimiento
            $codigoMovimiento = $this->generarCodigoMovimiento($this->pdo);

            // 3. Insertar movimiento (f15_movimiento)
            $this->insertarMovimiento($this->pdo, $codigoMovimiento, $datos, $multa);

            // 4. Actualizar multa (f63_multa)
            $this->actualizarMultaPago($this->pdo, (int)$datos['multa'], $datos, $multa);

            // 5. Insertar en diario contable si se proporcionaron datos
            if (!empty($datosContables)) {
                $this->insertarDiarioContable($this->pdo, $codigoMovimiento, $datos, $datosContables);
            }

            $this->pdo->commit();
            
            // Registrar éxito
            $logMessage = sprintf(
                "[%s] EXITO - Movimiento: %d, Cliente: %s, Multa ID: %d\n",
                date('Y-m-d H:i:s'),
                $codigoMovimiento,
                $datos['cliente'],
                $datos['multa']
            );
            file_put_contents($logFile, $logMessage, FILE_APPEND);
            
            return [
                'exito' => true,
                'codigo_movimiento' => $codigoMovimiento,
                'mensaje' => 'Pago de multa procesado correctamente',
            ];
            
        } catch (RuntimeException $e) {
            // Rollback ante error
            if ($this->pdo->inTransaction()) {
                $this->pdo->rollBack();
            }
            // Registrar error
            $logMessage = sprintf(
                "[%s] ERROR RUNTIME - Cliente: %s, Multa ID: %d, Error: %s\n",
                date('Y-m-d H:i:s'),
                $datos['cliente'] ?? 'N/A',
                $datos['multa'] ?? 'N/A',
                $e->getMessage()
            );
            file_put_contents($logFile, $logMessage, FILE_APPEND);
            
            return [
                'exito' => false,
                'error' => $e->getMessage(),
                'mensaje' => 'Error en pago de multa: ' . $e->getMessage(),
            ];
        } catch (\PDOException $e) {
            // Rollback ante error de base de datos
            if ($this->pdo->inTransaction()) {
                $this->pdo->rollBack();
            }
            // Registrar error de base de datos
            $logMessage = sprintf(
                "[%s] ERROR DB - Cliente: %s, Multa ID: %d, Error: %s\n",
                date('Y-m-d H:i:s'),
                $datos['cliente'] ?? 'N/A',
                $datos['multa'] ?? 'N/A',
                $e->getMessage()
            );
            file_put_contents($logFile, $logMessage, FILE_APPEND);
            
            return [
                'exito' => false,
                'error' => $e->getMessage(),
                'mensaje' => 'Error de base de datos en pago de multa: ' . $e->getMessage(),
            ];
        }
    }

    /**
     * Obtiene datos de la multa
     */
    private function obtenerMulta(PDO $pdo, int $multaCodigo): ?array
    {
        $sql = "SELECT * FROM f63_multa WHERE f63_codigo = ? LIMIT 1";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$multaCodigo]);
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
    private function insertarMovimiento(PDO $pdo, int $codigoMovimiento, array $datos, array $multa): void
    {
        $sql = "INSERT INTO f15_movimiento
                (f15_codigo, f15_fecha, f15_operacion, f15_cliente, f15_mamulta,
                 f15_formaPago, f15_usuaCrea, f15_fechaCrea, f15_obs, f15_obs2)
                VALUES (:codigo, :fecha, :operacion, :cliente, :mamulta,
                        :formaPago, :usuario, NOW(), :obs, :obs2)";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([
            'codigo' => $codigoMovimiento,
            'fecha' => $datos['fecha'] ?? date('Y-m-d'),
            'operacion' => 11,
            'cliente' => trim($datos['cliente']),
            'mamulta' => $datos['monto'] ?? 0,
            'formaPago' => trim($datos['formaPago'] ?? ''),
            'usuario' => trim($datos['usuario'] ?? 'SISTEMA'),
            'obs' => trim($datos['obs'] ?? ''),
            'obs2' => trim($datos['obs2'] ?? ''),
        ]);
    }

    /**
     * Actualiza la multa para registrar el pago
     */
    private function actualizarMultaPago(PDO $pdo, int $multaCodigo, array $datos, array $multa): void
    {
        // Calcular nuevo monto pagado y restante
        $montoPagado = floatval($datos['monto'] ?? 0);
        $multaPagadaActual = floatval($multa['f63_multad'] ?? 0);
        $multaRestanteActual = floatval($multa['f63_multar'] ?? 0);
        
        $nuevaMultaPagada = $multaPagadaActual + $montoPagado;
        $nuevaMultaRestante = max(0, $multaRestanteActual - $montoPagado);
        
        // Actualizar multa
        $sql = "UPDATE f63_multa 
                SET f63_multad = ?,
                    f63_multar = ?,
                    f63_fechaPago = NOW(),
                    f63_formaPago = ?
                WHERE f63_codigo = ?";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([
            $nuevaMultaPagada,
            $nuevaMultaRestante,
            trim($datos['formaPago'] ?? ''),
            $multaCodigo
        ]);
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
            'operacion' => 11,
            'descripcion' => trim($datosContables['descripcionContable'] ?? 'PAGO MULTA'),
            'debe' => 0,
            'haber' => (float)($datos['monto'] ?? 0),
            'usuario' => trim($datos['usuario'] ?? 'SISTEMA'),
        ]);
    }
}
