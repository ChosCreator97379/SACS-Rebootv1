<?php

declare(strict_types=1);

namespace Sacs\Services;

use PDO;
use RuntimeException;

final class PagoSobregiroFonbesServicio
{
    public function __construct(
        private readonly PDO $pdo
    ) {
    }

    /**
     * Ejecuta un pago de sobregiro de aportación FONBES (Operación 14)
     *
     * @param array $datos Datos del formulario
     * @param array $datosContables Datos contables (codigoContable, tipoAsiento, descripcionContable)
     * @return array Resultado de la operación
     */
    public function ejecutarPagoSobregiroFonbes(array $datos, array $datosContables = []): array
    {
        $logFile = dirname(__DIR__, 2) . '/logs/pago_sobregiro_fonbes.log';
        
        // Asegurar que el directorio de logs existe
        if (!is_dir(dirname($logFile))) {
            mkdir(dirname($logFile), 0755, true);
        }

        try {
            // Registrar inicio de operación
            $logMessage = sprintf(
                "[%s] INICIO - Cliente: %s, Periodos: %s\n",
                date('Y-m-d H:i:s'),
                $datos['cliente'],
                $datos['periodos'] ?? ''
            );
            file_put_contents($logFile, $logMessage, FILE_APPEND);

            $pdo = $this->pdo;
            $pdo->beginTransaction();

            // 1. Obtener aportaciones FONBES con sobregiro del cliente
            $aportaciones = $this->obtenerAportacionesConSobregiro($pdo, (int) $datos['cliente']);
            if ($aportaciones === []) {
                throw new RuntimeException('No hay aportaciones FONBES con sobregiro para este cliente');
            }

            // 2. Calcular total a pagar (suma de diferencias)
            $totalPagar = 0;
            foreach ($aportaciones as $aportacion) {
                $diferencia = (float) $aportacion['f22_diferencia'];
                if ($diferencia > 0) {
                    $totalPagar += $diferencia;
                }
            }

            if ($totalPagar <= 0) {
                throw new RuntimeException('No hay sobregiro pendiente de pago');
            }

            // 3. Validar monto
            $montoPagado = (float) $datos['monto'];
            if ($montoPagado <= 0) {
                throw new RuntimeException('El monto debe ser mayor a cero');
            }

            // 4. Generar código de movimiento
            $codigoMovimiento = $this->generarCodigoMovimiento($pdo);

            // 5. Actualizar aportaciones (reducir diferencia)
            $this->actualizarAportaciones($pdo, $aportaciones, $montoPagado);

            // 6. Insertar movimiento de pago
            $this->insertarMovimientoPago(
                $pdo,
                $codigoMovimiento,
                $datos,
                $montoPagado
            );

            // 7. Insertar en diario contable si se proporcionaron datos
            if (!empty($datosContables['codigoContable'])) {
                $this->insertarDiarioContable($pdo, $codigoMovimiento, $datos, $datosContables, $montoPagado);
            }

            $pdo->commit();

            // Registrar éxito
            $logMessage = sprintf(
                "[%s] EXITO - Movimiento: %d, Cliente: %s, Monto: %.2f\n",
                date('Y-m-d H:i:s'),
                $codigoMovimiento,
                $datos['cliente'],
                $montoPagado
            );
            file_put_contents($logFile, $logMessage, FILE_APPEND);

            return [
                'exito' => true,
                'codigo_movimiento' => $codigoMovimiento,
                'mensaje' => 'Pago de sobregiro FONBES procesado correctamente',
                'monto_pagado' => $montoPagado,
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
                'mensaje' => 'Error en pago de sobregiro FONBES: ' . $e->getMessage(),
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
                'mensaje' => 'Error inesperado al procesar pago de sobregiro FONBES',
            ];
        }
    }

    /**
     * Obtiene aportaciones FONBES con sobregiro del cliente
     */
    private function obtenerAportacionesConSobregiro(PDO $pdo, int $clienteId): array
    {
        $sql = "SELECT f22_socio, f22_periodo, f22_aportacionP, f22_aportacionD, f22_diferencia, f22_aporte_id
                FROM f22_aportes
                WHERE f22_socio = :cliente
                  AND f22_diferencia > 0
                ORDER BY f22_periodo ASC";
        $stmt = $pdo->prepare($sql);
        $stmt->execute(['cliente' => $clienteId]);
        return $stmt->fetchAll();
    }

    /**
     * Actualiza aportaciones reduciendo la diferencia
     */
    private function actualizarAportaciones(PDO $pdo, array $aportaciones, float $montoPagado): void
    {
        $montoRestante = $montoPagado;
        
        foreach ($aportaciones as $aportacion) {
            if ($montoRestante <= 0) {
                break;
            }
            
            $diferencia = (float) $aportacion['f22_diferencia'];
            if ($diferencia <= 0) {
                continue;
            }
            
            $pagoAportacion = min($diferencia, $montoRestante);
            $nuevaDiferencia = $diferencia - $pagoAportacion;
            $nuevoPagado = (float) $aportacion['f22_aportacionD'] + $pagoAportacion;
            
            $sql = "UPDATE f22_aportes
                    SET f22_aportacionD = :nuevoPagado,
                        f22_diferencia = :nuevaDiferencia,
                        f22_fechaPago = NOW()
                    WHERE f22_socio = :socio
                      AND f22_periodo = :periodo
                      AND f22_aporte_id = :aporteId";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([
                'nuevoPagado' => $nuevoPagado,
                'nuevaDiferencia' => $nuevaDiferencia,
                'socio' => $aportacion['f22_socio'],
                'periodo' => $aportacion['f22_periodo'],
                'aporteId' => $aportacion['f22_aporte_id']
            ]);
            
            $montoRestante -= $pagoAportacion;
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
     * Inserta movimiento de pago en f15_movimiento
     */
    private function insertarMovimientoPago(PDO $pdo, int $codigoMovimiento, array $datos, float $monto): void
    {
        $sql = "INSERT INTO f15_movimiento (
                    f15_codigo, f15_fecha, f15_operacion, f15_cliente,
                    f15_maportacion, f15_formaPago, f15_usuaCrea, f15_fechaCrea, f15_obs, f15_obs2
                ) VALUES (
                    :codigo, NOW(), 14, :cliente,
                    :monto, :formaPago, :usuario, NOW(), :obs, :obs2
                )";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([
            'codigo' => $codigoMovimiento,
            'cliente' => trim($datos['cliente']),
            'monto' => $monto,
            'formaPago' => $datos['formaPago'] ?? 'EFECTIVO',
            'usuario' => $datos['usuario'] ?? 'SISTEMA',
            'obs' => $datos['obs'] ?? 'PAGO DE SOBREGIRO DE APORTACION FONBES',
            'obs2' => $datos['obs2'] ?? ''
        ]);
    }

    /**
     * Inserta en diario contable
     */
    private function insertarDiarioContable(PDO $pdo, int $codigoMovimiento, array $datos, array $datosContables, float $monto): void
    {
        $tipoAsiento = strtolower($datosContables['tipoAsiento'] ?? 'haber');
        
        $sql = "INSERT INTO f37_diario_contable (
                    f37_movimiento, f37_fecha, f37_codigo_contable, f37_operacion,
                    f37_debe, f37_haber, f37_descripcion, f37_usuario_crea, f37_fecha_crea, f37_estado
                ) VALUES (
                    :movimiento, CURDATE(), :codigoContable, 14,
                    :debe, :haber, :descripcion, :usuario, NOW(), 'ACTIVO'
                )";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([
            'movimiento' => $codigoMovimiento,
            'codigoContable' => $datosContables['codigoContable'] ?? '1651',
            'debe' => $tipoAsiento === 'debe' ? $monto : 0,
            'haber' => $tipoAsiento === 'haber' ? $monto : 0,
            'descripcion' => $datosContables['descripcionContable'] ?? 'Pago Sobregiro FONBES',
            'usuario' => $datos['usuario'] ?? 'SISTEMA'
        ]);
    }
}
