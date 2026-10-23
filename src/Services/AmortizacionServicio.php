<?php

namespace Sacs\Services;

use PDO;
use PDOException;
use RuntimeException;
use Sacs\Core\Database;

/**
 * Servicio de Amortización AEE (Amortización de Préstamos con Gestión Especial)
 * Sigue el flujo legacy del sistema original para compatibilidad
 */
class AmortizacionServicio
{
    private PDO $pdo;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    /**
     * Ejecuta una amortización AEE siguiendo el flujo legacy
     * 
     * @param array $datos Datos del formulario
     * @param array $datosContables Datos contables generales
     * @param array $codigosContables Códigos contables por componente
     * @return array Resultado de la operación
     * @throws RuntimeException Si hay error en la operación
     */
    public function ejecutarAmortizacionAEE(array $datos, array $datosContables = [], array $codigosContables = []): array
    {
        $pdo = $this->pdo;
        $logFile = dirname(__DIR__, 2) . '/logs/amortizacion.log';

        // Asegurar que el directorio de logs existe
        if (!is_dir(dirname($logFile))) {
            mkdir(dirname($logFile), 0755, true);
        }

        try {
            // Registrar inicio de operación
            $logMessage = sprintf(
                "[%s] INICIO - Cliente: %s, Préstamo: %d, Cuota: %d, Monto: %.2f\n",
                date('Y-m-d H:i:s'),
                $datos['cliente'],
                $datos['prestamo'],
                $datos['cuota'],
                $datos['monto']
            );
            file_put_contents($logFile, $logMessage, FILE_APPEND);

            // Registrar códigos contables si se proporcionaron
            if (!empty($codigosContables)) {
                $logMessage = sprintf("[%s] CODIGOS CONTABLES: %s\n", date('Y-m-d H:i:s'), json_encode($codigosContables));
                file_put_contents($logFile, $logMessage, FILE_APPEND);
            }
            // Iniciar transacción simple (como lo haría el legacy de forma correcta)
            $pdo->beginTransaction();
            
            // 1. Obtener datos del préstamo
            $prestamo = $this->obtenerPrestamo($pdo, (int)$datos['prestamo']);
            if ($prestamo === null) {
                throw new RuntimeException('Préstamo no encontrado');
            }
            
            // 2. Obtener datos de la cuota
            $cuota = $this->obtenerCuota($pdo, (int)$datos['prestamo'], (int)$datos['cuota']);
            if ($cuota === null) {
                throw new RuntimeException('Cuota no encontrada');
            }
            
            // 3. Calcular distribución usando la función pura
            $formValores = [
                'gastosAdm' => (float)($datos['gastosAdm'] ?? 0),
                'gastosMora' => (float)($datos['gastosMora'] ?? 0),
                'otros1' => (float)($datos['otros1'] ?? 0),
                'otros2' => (float)($datos['otros2'] ?? 0),
            ];
            
            $montoPagado = (float)$datos['monto'];
            $calculo = AmortizacionCalculadora::calcularAmortizacionAEE(
                $prestamo,
                $cuota,
                $formValores,
                $montoPagado
            );
            
            // 4. Generar código de movimiento (forma segura, no MAX+1)
            $codigoMovimiento = $this->generarCodigoMovimiento($pdo);

            // 5. Insertar en diario contable (múltiples filas por componente) - ANTES del movimiento
            if (!empty($codigosContables)) {
                $logMessage = sprintf("[%s] INSERTANDO DIARIO CONTABLE MÚLTIPLE\n", date('Y-m-d H:i:s'));
                file_put_contents($logFile, $logMessage, FILE_APPEND);
                $this->insertarDiarioContableMultiple($pdo, $codigoMovimiento, $datos, $codigosContables);
            } elseif (!empty($datosContables)) {
                // Mantener compatibilidad con el método anterior
                $logMessage = sprintf("[%s] INSERTANDO DIARIO CONTABLE SIMPLE\n", date('Y-m-d H:i:s'));
                file_put_contents($logFile, $logMessage, FILE_APPEND);
                $this->insertarDiarioContable($pdo, $codigoMovimiento, $datos, $datosContables);
            }

            // 6. Insertar movimiento (f15_movimiento) - una sola fila con el total
            $logMessage = sprintf("[%s] INSERTANDO MOVIMIENTO\n", date('Y-m-d H:i:s'));
            file_put_contents($logFile, $logMessage, FILE_APPEND);
            $this->insertarMovimiento($pdo, $codigoMovimiento, $datos, $calculo);

            // 7. Actualizar préstamo (f16_prestamo)
            $logMessage = sprintf("[%s] ACTUALIZANDO PRÉSTAMO\n", date('Y-m-d H:i:s'));
            file_put_contents($logFile, $logMessage, FILE_APPEND);
            $this->actualizarPrestamo($pdo, $prestamo, $calculo);

            // 8. Actualizar cuota individual (f17_cuotasprestamo)
            $logMessage = sprintf("[%s] ACTUALIZANDO CUOTA INDIVIDUAL\n", date('Y-m-d H:i:s'));
            file_put_contents($logFile, $logMessage, FILE_APPEND);
            $this->actualizarCuota($pdo, (int)$datos['prestamo'], (int)$datos['cuota'], $calculo);

            $pdo->commit();
            
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
                'calculo' => $calculo,
                'cuota' => $cuota,
            ];
            
        } catch (RuntimeException $e) {
            // Rollback ante error lógico
            if ($pdo->inTransaction()) {
                $pdo->rollBack();
            }
            
            // Registrar error
            $logMessage = sprintf(
                "[%s] ERROR RUNTIME - Cliente: %s, Préstamo: %d, Error: %s\n",
                date('Y-m-d H:i:s'),
                $datos['cliente'],
                $datos['prestamo'],
                $e->getMessage()
            );
            file_put_contents($logFile, $logMessage, FILE_APPEND);
            
            return [
                'exito' => false,
                'error' => $e->getMessage(),
                'mensaje' => 'Error en amortización: ' . $e->getMessage(),
            ];
        } catch (PDOException $e) {
            // Rollback ante error de base de datos
            if ($pdo->inTransaction()) {
                $pdo->rollBack();
            }
            // Registrar error de base de datos
            $logMessage = sprintf(
                "[%s] ERROR DB - Cliente: %s, Préstamo: %d, Error: %s\n",
                date('Y-m-d H:i:s'),
                $datos['cliente'],
                $datos['prestamo'],
                $e->getMessage()
            );
            file_put_contents($logFile, $logMessage, FILE_APPEND);

            return [
                'exito' => false,
                'error' => $e->getMessage(),
                'mensaje' => 'Error en amortización: ' . $e->getMessage(),
            ];
        }
    }

    /**
     * Obtiene datos de un préstamo
     */
    private function obtenerPrestamo(PDO $pdo, int $prestamoId): ?array
    {
        $sql = "SELECT * FROM f16_prestamo WHERE f16_codigo = ?";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$prestamoId]);
        return $stmt->fetch() ?: null;
    }

    /**
     * Obtiene datos de una cuota específica
     */
    private function obtenerCuota(PDO $pdo, int $prestamoId, int $cuota): ?array
    {
        $sql = "SELECT * FROM f17_cuotasprestamo 
                WHERE f17_prestamo = ? AND f17_ncuota = ?";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$prestamoId, $cuota]);
        return $stmt->fetch() ?: null;
    }

    /**
     * Genera código de movimiento
     */
    private function generarCodigoMovimiento(PDO $pdo): int
    {
        // Forma segura: usar AUTO_INCREMENT de MySQL
        // Si la tabla no tiene AUTO_INCREMENT, usar LOCK TABLES
        $sql = "SELECT MAX(f15_codigo) as max_codigo FROM f15_movimiento FOR UPDATE";
        $stmt = $pdo->prepare($sql);
        $stmt->execute();
        $result = $stmt->fetch();
        $maxCodigo = $result['max_codigo'] ?? 0;
        return $maxCodigo + 1;
    }
    
    /**
     * Inserta movimiento en f15_movimiento
     * Basado en registrarMovimiento() para operación 2
     */
    private function insertarMovimiento(PDO $pdo, int $codigoMovimiento, array $datos, array $calculo): void
    {
        $sql = "INSERT INTO f15_movimiento
                (f15_codigo, f15_fecha, f15_operacion, f15_cliente, f15_prestamo, f15_maprestamo,
                 f15_gastosa, f15_gastosm, f15_gastos1, f15_gastos2, f15_formaPago, f15_usuaCrea, f15_fechaCrea, f15_obs, f15_obs2, f15_cheque)
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([
            $codigoMovimiento,
            $datos['fecha'] ?? date('Y-m-d'),
            2,
            trim($datos['cliente']),
            $datos['prestamo'],
            $datos['amortiza'] ?? 0,  // Amortización directamente del formulario
            $datos['gastosAdm'] ?? 0,  // Gastos administrativos directamente del formulario
            $datos['gastosMora'] ?? 0,  // Gastos mora directamente del formulario
            $datos['otros1'] ?? 0,  // Otros 1 directamente del formulario
            $datos['otros2'] ?? 0,  // Otros 2 directamente del formulario
            trim($datos['formaPago'] ?? ''),
            trim($datos['usuario'] ?? 'SISTEMA'),
            date('Y-m-d H:i:s'),
            trim($datos['obs'] ?? ''),
            trim($datos['obs2'] ?? ''),
            $datos['cheque'] ?? null,
        ]);
    }

    /**
     * Actualiza préstamo en f16_prestamo
     * Basado en actualizarSaldo4() del legacy (líneas 1978-1993)
     */
    private function actualizarPrestamo(PDO $pdo, array $prestamo, array $calculo): void
    {
        $prestamoActualizado = $calculo['prestamo_actualizado'];

        $sql = "UPDATE f16_prestamo 
                SET f16_cuotasPend = ?, 
                    f16_saldo = ?,
                    f16_gastosa = ?,
                    f16_gastosm = ?,
                    f16_otros = ?,
                    f16_otros2 = ?,
                    f16_fechaUP = ?,
                    f16_sobre = ?
                WHERE f16_codigo = ?";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([
            $prestamoActualizado['cuotas_pendientes'],
            $prestamoActualizado['saldo'],
            $prestamoActualizado['gastosa'],
            $prestamoActualizado['gastosm'],
            $prestamoActualizado['otros'],
            $prestamoActualizado['otros2'],
            $prestamoActualizado['fechaUP'],
            $prestamoActualizado['sobre'],
            $prestamo['f16_codigo'],
        ]);
    }

    /**
     * Actualiza cuota individual en f17_cuotasprestamo
     * Marca la cuota como pagada y actualiza los montos
     */
    private function actualizarCuota(PDO $pdo, int $prestamoId, int $numeroCuota, array $calculo): void
    {
        $cuotaActualizada = $calculo['cuota_actualizada'] ?? [];

        $sql = "UPDATE f17_cuotasprestamo
                SET f17_estado = 'PAGADO',
                    f17_saldo = ?,
                    f17_capital = ?,
                    f17_interes = ?,
                    f17_total = ?
                WHERE f17_prestamo = ? AND f17_ncuota = ?";
        
        $stmt = $pdo->prepare($sql);
        $stmt->execute([
            $cuotaActualizada['saldo'] ?? 0,
            $cuotaActualizada['capital'] ?? 0,
            $cuotaActualizada['interes'] ?? 0,
            $cuotaActualizada['total'] ?? 0,
            $prestamoId,
            $numeroCuota
        ]);
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
        $stmt->execute([
            'movimiento' => $codigoMovimiento,
            'fecha' => $datos['fecha'] ?? date('Y-m-d'),
            'codigoContable' => trim($datosContables['codigoContable'] ?? ''),
            'operacion' => 2,
            'descripcion' => trim($datosContables['descripcionContable'] ?? 'AMORTIZACION AEE'),
            'debe' => $datosContables['tipoAsiento'] === 'debe' ? $datos['monto'] : 0.00,
            'haber' => $datosContables['tipoAsiento'] === 'haber' ? $datos['monto'] : 0.00,
            'usuario' => trim($datos['usuario'] ?? 'SISTEMA'),
        ]);
    }

    /**
     * Inserta múltiples registros en diario contable (uno por cada componente)
     */
    private function insertarDiarioContableMultiple(PDO $pdo, int $codigoMovimiento, array $datos, array $codigosContables): void
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

                $debe = $tipo === 'debe' ? $monto : 0.00;
                $haber = $tipo === 'haber' ? $monto : 0.00;

                $descripcion = 'AMORTIZACION AEE - ' . strtoupper($componente);

                $stmt->execute([
                    'movimiento' => $codigoMovimiento,
                    'fecha' => $datos['fecha'] ?? date('Y-m-d'),
                    'codigoContable' => trim($config['codigo']),
                    'operacion' => 2,
                    'descripcion' => $descripcion,
                    'debe' => $debe,
                    'haber' => $haber,
                    'usuario' => trim($datos['usuario'] ?? 'SISTEMA'),
                ]);
            }
        }
    }
}
