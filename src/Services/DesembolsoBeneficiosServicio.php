<?php

declare(strict_types=1);

namespace Sacs\Services;

use PDO;
use RuntimeException;

final class DesembolsoBeneficiosServicio
{
    public function __construct(
        private readonly PDO $pdo
    ) {
    }

    /**
     * Ejecuta un desembolso de beneficios (Operación 7 - Desembolso Beneficios)
     *
     * @param array $datos Datos del formulario
     * @param array $datosContables Datos contables (codigoContable, tipoAsiento, descripcionContable)
     * @return array Resultado de la operación
     */
    public function ejecutarDesembolsoBeneficio(array $datos, array $datosContables = []): array
    {
        $logFile = dirname(__DIR__, 2) . '/logs/desembolso_beneficios.log';
        
        // Asegurar que el directorio de logs existe
        if (!is_dir(dirname($logFile))) {
            mkdir(dirname($logFile), 0755, true);
        }

        try {
            // Registrar inicio de operación
            $logMessage = sprintf(
                "[%s] INICIO - Cliente: %s, Beneficio: %s, Monto: %s\n",
                date('Y-m-d H:i:s'),
                $datos['cliente'],
                $datos['beneficioId'] ?? '',
                $datos['monto'] ?? ''
            );
            file_put_contents($logFile, $logMessage, FILE_APPEND);

            $pdo = $this->pdo;
            $pdo->beginTransaction();

            // 1. Validar datos básicos
            if (trim($datos['cliente']) === '' || trim($datos['beneficioId']) === '' || trim($datos['monto']) === '') {
                throw new RuntimeException('Cliente, beneficio y monto son requeridos');
            }

            $monto = (float) $datos['monto'];
            $beneficioId = (int) $datos['beneficioId'];
            
            if ($monto <= 0) {
                throw new RuntimeException('El monto debe ser mayor a cero');
            }

            // 2. Verificar que el beneficio existe y está pendiente
            $beneficio = $this->obtenerBeneficio($pdo, $beneficioId, $datos['cliente']);
            if ($beneficio === false) {
                throw new RuntimeException('Beneficio no encontrado o no pertenece al cliente');
            }
            if (strtoupper($beneficio['f21_estado']) !== 'PENDIENTE') {
                throw new RuntimeException('El beneficio ya ha sido procesado');
            }

            // 3. Generar código de movimiento
            $codigoMovimiento = $this->generarCodigoMovimiento($pdo);

            // 4. Insertar movimiento de desembolso de beneficio
            $this->insertarMovimientoBeneficio(
                $pdo,
                $codigoMovimiento,
                $datos,
                $monto,
                $beneficioId
            );

            // 5. Actualizar estado del beneficio a DESEMBOLSADO
            $this->actualizarBeneficio($pdo, $beneficioId);

            // 6. Insertar en diario contable si se proporcionaron datos
            if (!empty($datosContables['codigoContable'])) {
                $this->insertarDiarioContable($pdo, $codigoMovimiento, $datos, $datosContables, $monto);
            }

            $pdo->commit();

            // Registrar éxito
            $logMessage = sprintf(
                "[%s] EXITO - Movimiento: %d, Cliente: %s, Beneficio: %d, Monto: %.2f\n",
                date('Y-m-d H:i:s'),
                $codigoMovimiento,
                $datos['cliente'],
                $beneficioId,
                $monto
            );
            file_put_contents($logFile, $logMessage, FILE_APPEND);

            return [
                'exito' => true,
                'codigo_movimiento' => $codigoMovimiento,
                'mensaje' => 'Desembolso de beneficio procesado correctamente',
                'monto' => $monto,
                'beneficio_id' => $beneficioId,
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
                'mensaje' => 'Error en desembolso de beneficio: ' . $e->getMessage(),
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
                'mensaje' => 'Error inesperado al procesar desembolso de beneficio',
            ];
        }
    }

    /**
     * Obtiene un beneficio pendiente del cliente
     */
    private function obtenerBeneficio(PDO $pdo, int $beneficioId, string $clienteCodigo): array|false
    {
        $sql = "SELECT f21_codigo, f21_socio, f21_importe, f21_estado, f21_beneficiario
                FROM f21_beneficios
                WHERE f21_codigo = :codigo AND f21_socio = :socio
                LIMIT 1";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([
            'codigo' => $beneficioId,
            'socio' => $clienteCodigo
        ]);
        return $stmt->fetch();
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
     * Inserta movimiento de desembolso de beneficio en f15_movimiento
     */
    private function insertarMovimientoBeneficio(PDO $pdo, int $codigoMovimiento, array $datos, float $monto, int $beneficioId): void
    {
        $sql = "INSERT INTO f15_movimiento (
                    f15_codigo, f15_fecha, f15_operacion, f15_cliente,
                    f15_mbeneficio, f15_beneficio, f15_cheque, f15_mletras,
                    f15_formaPago, f15_usuaCrea, f15_fechaCrea, f15_obs, f15_obs2
                ) VALUES (
                    :codigo, NOW(), 7, :cliente,
                    :monto, :beneficioId, :cheque, :montoLetras,
                    :formaPago, :usuario, NOW(), :obs, :obs2
                )";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([
            'codigo' => $codigoMovimiento,
            'cliente' => trim($datos['cliente']),
            'monto' => $monto,
            'beneficioId' => $beneficioId,
            'cheque' => $datos['cheque'] ?? '',
            'montoLetras' => $datos['montoLetras'] ?? '',
            'formaPago' => $datos['formaPago'] ?? 'EFECTIVO',
            'usuario' => $datos['usuario'] ?? 'SISTEMA',
            'obs' => $datos['obs'] ?? 'DESEMBOLSO DE BENEFICIO',
            'obs2' => $datos['obs2'] ?? ''
        ]);
    }

    /**
     * Actualiza el estado del beneficio a DESEMBOLSADO
     */
    private function actualizarBeneficio(PDO $pdo, int $beneficioId): void
    {
        $sql = "UPDATE f21_beneficios SET f21_estado = 'DESEMBOLSADO' WHERE f21_codigo = :codigo";
        $stmt = $pdo->prepare($sql);
        $stmt->execute(['codigo' => $beneficioId]);
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
                    :movimiento, CURDATE(), :codigoContable, 7,
                    :debe, :haber, :descripcion, :usuario, NOW(), 'ACTIVO'
                )";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([
            'movimiento' => $codigoMovimiento,
            'codigoContable' => $datosContables['codigoContable'] ?? '1651',
            'debe' => $tipoAsiento === 'debe' ? $monto : 0,
            'haber' => $tipoAsiento === 'haber' ? $monto : 0,
            'descripcion' => $datosContables['descripcionContable'] ?? 'Desembolso de Beneficio',
            'usuario' => $datos['usuario'] ?? 'SISTEMA'
        ]);
    }

    /**
     * Obtiene beneficios pendientes de un cliente
     */
    public function obtenerBeneficiosPendientes(string $clienteCodigo): array
    {
        $sql = "SELECT f21_codigo, f21_importe, f21_beneficiario, f21_fecha, f21_tipo
                FROM f21_beneficios
                WHERE f21_socio = :socio AND f21_estado = 'PENDIENTE'
                ORDER BY f21_fecha DESC";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute(['socio' => $clienteCodigo]);
        return $stmt->fetchAll();
    }
}
