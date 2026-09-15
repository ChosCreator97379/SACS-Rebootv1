<?php

declare(strict_types=1);

namespace Sacs\Services;

use PDO;
use RuntimeException;
use Sacs\Core\Database;

/**
 * Servicio para procesar pagos de sobregiro de préstamo (Operación 10)
 * Basado en el código legacy CrearCajaAction.java líneas 1901-2099
 */
class SobregiroPrestamoServicio
{
    private PDO $pdo;

    public function __construct()
    {
        $this->pdo = Database::connection();
    }

    /**
     * Ejecuta el pago de sobregiro de préstamo
     *
     * @param array $datos Datos del formulario
     * @param array $datosContables Datos contables (opcional)
     * @return array Resultado de la operación
     */
    public function ejecutarPagoSobregiroPrestamo(array $datos, array $datosContables = []): array
    {
        $logFile = dirname(__DIR__, 2) . '/logs/sobregiro_prestamo.log';

        // Log de datos recibidos para debugging
        $logMessage = sprintf(
            "[%s] DEBUG - Datos recibidos: %s, Datos contables: %s\n",
            date('Y-m-d H:i:s'),
            json_encode($datos),
            json_encode($datosContables)
        );
        file_put_contents($logFile, $logMessage, FILE_APPEND);

        try {
            $this->pdo->beginTransaction();

            // Validar datos requeridos
            $clienteCodigo = trim($datos['cliente'] ?? '');
            $prestamoCodigo = trim($datos['prestamo'] ?? '');
            $montoTotal = (float) ($datos['monto'] ?? 0);
            $sobregiro = (float) ($datos['sobregiro'] ?? 0);
            $amortizacion = (float) ($datos['descuenta'] ?? 0);
            $gastosAdm = (float) ($datos['gastosAdm'] ?? 0);
            $gastosMora = (float) ($datos['gastosMora'] ?? 0);
            $otros1 = (float) ($datos['otros1'] ?? 0);
            $otros2 = (float) ($datos['otros2'] ?? 0);

            if ($clienteCodigo === '' || $prestamoCodigo === '') {
                throw new RuntimeException('Cliente y préstamo son requeridos');
            }

            // Obtener datos del préstamo
            $prestamo = $this->obtenerPrestamo($this->pdo, (int) $prestamoCodigo);
            if ($prestamo === null) {
                throw new RuntimeException('Préstamo no encontrado');
            }

            // Distribución inicial del monto pagado (líneas 1926-2022 del legacy)
            // El legacy Java primero distribuye el monto pagado entre gastos, 
            // luego sobrescribe con el cálculo basado en el préstamo
            $montoA = $montoTotal;
            $gami = 0; // Sobregiro gastos administrativos
            $gamo = 0; // Sobregiro mora
            $otros1Sob = 0; // Sobregiro otros1
            $otros2Sob = 0; // Sobregiro otros2

            // Aplicar a gastos administrativos (líneas 1926-1948)
            if ($gastosAdm > 0) {
                if ($montoA > $gastosAdm) {
                    $montoA = $montoA - $gastosAdm;
                    if ($montoA > 0) {
                        $gami = 0;
                    } else {
                        $gami = 0; // Línea 1937 del legacy
                    }
                } else {
                    // Sobregiro gastos administrativos
                    $gami = $gastosAdm - $montoA;
                    $gastosAdm = $montoA;
                    $montoA = 0;
                }
            }

            // Aplicar a mora (líneas 1954-1977)
            if ($gastosMora > 0) {
                if ($montoA > $gastosMora) {
                    $montoA = $montoA - $gastosMora;
                    if ($montoA > 0) {
                        $gamo = 0;
                    } else {
                        $gamo = $gastosMora - $montoA;
                    }
                } else {
                    $gamo = $gastosMora - $montoA;
                    $montoA = 0;
                }
            }

            // Aplicar a otros1 (líneas 1982-2000)
            if ($otros1 > 0) {
                if ($montoA > $otros1) {
                    $montoA = $montoA - $otros1;
                    if ($montoA > 0) {
                        $otros1Sob = 0;
                    } else {
                        $otros1Sob = $otros1 - $montoA;
                    }
                } else {
                    $otros1Sob = $otros1 - $montoA;
                    $montoA = 0;
                }
            }

            // Aplicar a otros2 (líneas 2003-2022)
            if ($otros2 > 0) {
                if ($montoA > $otros2) {
                    $montoA = $montoA - $otros2;
                    if ($montoA > 0) {
                        $otros2Sob = 0;
                    } else {
                        $otros2Sob = $otros2 - $montoA;
                    }
                } else {
                    $otros2Sob = $otros2 - $montoA;
                    $montoA = 0;
                }
            }

            // Calcular sobregiros reales basados en el préstamo (líneas 2025-2040 del legacy)
            // El legacy Java sobrescribe los valores anteriores con este cálculo
            $gastosaPrestamo = (float) ($prestamo['f16_gastosa'] ?? 0);
            $moraPrestamo = (float) ($prestamo['f16_gastosm'] ?? 0);

            if ($gastosaPrestamo > 0) {
                $gami = $gastosaPrestamo - $gastosAdm;
            }

            if ($moraPrestamo > 0) {
                $gamo = $moraPrestamo - $gastosMora;
            }

            // Actualizar préstamo con nuevos valores
            $this->actualizarSaldo01(
                $this->pdo,
                (int) $prestamoCodigo,
                $gami,
                $gamo,
                $otros1Sob,
                $otros2Sob
            );

            // Actualizar sobregiro del préstamo
            if ($sobregiro > 0) {
                $aportacionPrestamo = (float) ($prestamo['f16_aportacion'] ?? 0);
                $nuevaAportacion = max(0, $aportacionPrestamo - $sobregiro);
                $this->actualizarSobregiro($this->pdo, (int) $prestamoCodigo, $nuevaAportacion);
            }

            // Actualizar saldo si hay amortización
            // NOTA: El legacy Java permite saldos negativos (líneas 2081-2086).
            // Si se requiere permitir saldos negativos, eliminar el max(0, ...) y usar:
            // $nuevoSaldo = $saldoPrestamo - $amortizacion;
            if ($amortizacion > 0) {
                $saldoPrestamo = (float) ($prestamo['f16_saldo'] ?? 0);
                $nuevoSaldo = max(0, $saldoPrestamo - $amortizacion);
                $this->actualizarSaldoSolo($this->pdo, (int) $prestamoCodigo, $nuevoSaldo);
            }

            // Generar código de movimiento
            $codigoMovimiento = $this->generarCodigoMovimiento($this->pdo);

            // Insertar movimiento
            $this->insertarMovimientoSobregiro(
                $this->pdo,
                $codigoMovimiento,
                $datos,
                $sobregiro,
                $gastosAdm,
                $gastosMora,
                $otros1,
                $otros2
            );

            // Insertar en diario contable si se proporcionaron datos
            if (!empty($datosContables)) {
                $this->insertarDiarioContable($this->pdo, $codigoMovimiento, $datos, $datosContables);
            }

            $this->pdo->commit();

            // Registrar éxito
            $logMessage = sprintf(
                "[%s] EXITO - Movimiento: %d, Cliente: %s, Préstamo: %s, Sobregiro: %.2f\n",
                date('Y-m-d H:i:s'),
                $codigoMovimiento,
                $clienteCodigo,
                $prestamoCodigo,
                $sobregiro
            );
            file_put_contents($logFile, $logMessage, FILE_APPEND);

            return [
                'exito' => true,
                'codigo_movimiento' => $codigoMovimiento,
                'mensaje' => 'Pago de sobregiro de préstamo procesado correctamente',
            ];

        } catch (RuntimeException $e) {
            // Rollback ante error
            if ($this->pdo->inTransaction()) {
                $this->pdo->rollBack();
            }

            // Registrar error
            $logMessage = sprintf(
                "[%s] ERROR - Cliente: %s, Préstamo: %s, Mensaje: %s\n",
                date('Y-m-d H:i:s'),
                $datos['cliente'] ?? 'N/A',
                $datos['prestamo'] ?? 'N/A',
                $e->getMessage()
            );
            file_put_contents($logFile, $logMessage, FILE_APPEND);

            return [
                'exito' => false,
                'mensaje' => $e->getMessage(),
            ];
        }
    }

    /**
     * Obtiene datos del préstamo
     */
    private function obtenerPrestamo(PDO $pdo, int $prestamoCodigo): ?array
    {
        $sql = "SELECT f16_codigo, f16_gastosa, f16_gastosm, f16_otros, f16_otros2,
                       f16_aportacion, f16_saldo, f16_producto
                FROM f16_prestamo
                WHERE f16_codigo = :codigo
                LIMIT 1";
        $stmt = $pdo->prepare($sql);
        $stmt->execute(['codigo' => $prestamoCodigo]);
        $result = $stmt->fetch();
        return $result ?: null;
    }

    /**
     * Actualiza gastos, mora y otros del préstamo
     */
    private function actualizarSaldo01(
        PDO $pdo,
        int $prestamoCodigo,
        float $gastosa,
        float $mora,
        float $otros,
        float $otros2
    ): void {
        $sql = "UPDATE f16_prestamo
                SET f16_gastosa = :gastosa,
                    f16_gastosm = :mora,
                    f16_otros = :otros,
                    f16_otros2 = :otros2
                WHERE f16_codigo = :codigo";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([
            'gastosa' => $gastosa,
            'mora' => $mora,
            'otros' => $otros,
            'otros2' => $otros2,
            'codigo' => $prestamoCodigo,
        ]);
    }

    /**
     * Actualiza el sobregiro (aportación) del préstamo
     */
    private function actualizarSobregiro(PDO $pdo, int $prestamoCodigo, float $aportacion): void
    {
        $sql = "UPDATE f16_prestamo
                SET f16_aportacion = :aportacion
                WHERE f16_codigo = :codigo";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([
            'aportacion' => $aportacion,
            'codigo' => $prestamoCodigo,
        ]);
    }

    /**
     * Actualiza el saldo del préstamo
     */
    private function actualizarSaldoSolo(PDO $pdo, int $prestamoCodigo, float $saldo): void
    {
        $sql = "UPDATE f16_prestamo
                SET f16_saldo = :saldo
                WHERE f16_codigo = :codigo";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([
            'saldo' => $saldo,
            'codigo' => $prestamoCodigo,
        ]);
    }

    /**
     * Genera código de movimiento de forma segura
     */
    private function generarCodigoMovimiento(PDO $pdo): int
    {
        // Usar bloqueo para evitar condiciones de carrera
        $sql = "SELECT MAX(f15_codigo) AS max_codigo FROM f15_movimiento FOR UPDATE";
        $stmt = $pdo->query($sql);
        $result = $stmt->fetch();
        $maxCodigo = (int) ($result['max_codigo'] ?? 0);

        // Si el máximo es muy pequeño, usar un valor base más alto
        if ($maxCodigo < 100000000) {
            $maxCodigo = 122013479; // Usar el último código conocido como base
        }

        $nuevoCodigo = $maxCodigo + 1;

        // Verificar que el código no exista (doble verificación)
        $checkSql = "SELECT f15_codigo FROM f15_movimiento WHERE f15_codigo = :codigo FOR UPDATE";
        $checkStmt = $pdo->prepare($checkSql);
        $checkStmt->execute(['codigo' => $nuevoCodigo]);
        if ($checkStmt->fetch()) {
            // Si existe, incrementar hasta encontrar uno libre
            do {
                $nuevoCodigo++;
                $checkStmt->execute(['codigo' => $nuevoCodigo]);
            } while ($checkStmt->fetch());
        }

        // Log para debugging
        $logFile = dirname(__DIR__, 2) . '/logs/sobregiro_prestamo.log';
        $logMessage = sprintf(
            "[%s] DEBUG - Código generado: %d (MAX anterior: %d)\n",
            date('Y-m-d H:i:s'),
            $nuevoCodigo,
            $maxCodigo
        );
        file_put_contents($logFile, $logMessage, FILE_APPEND);

        return $nuevoCodigo;
    }

    /**
     * Inserta movimiento en f15_movimiento
     */
    private function insertarMovimientoSobregiro(
        PDO $pdo,
        int $codigoMovimiento,
        array $datos,
        float $sobregiro,
        float $gastosAdm,
        float $gastosMora,
        float $otros1,
        float $otros2
    ): void {
        $prestamoCodigo = trim($datos['prestamo']);
        $prestamoId = $prestamoCodigo !== '' ? (int) $prestamoCodigo : null;

        $logFile = dirname(__DIR__, 2) . '/logs/sobregiro_prestamo.log';
        $logMessage = sprintf(
            "[%s] DEBUG - Insertando movimiento: codigo=%d, prestamo=%s, monto(sobregiro)=%.2f, miprestamo=%.2f, gastosa=%.2f, gastosm=%.2f, gastos1=%.2f, gastos2=%.2f\n",
            date('Y-m-d H:i:s'),
            $codigoMovimiento,
            $prestamoId,
            $sobregiro,
            $amortizacion,
            $gastosAdm,
            $gastosMora,
            $otros1,
            $otros2
        );
        file_put_contents($logFile, $logMessage, FILE_APPEND);

        $sql = "INSERT INTO f15_movimiento
                (f15_codigo, f15_fecha, f15_operacion, f15_cliente, f15_prestamo, f15_maprestamo,
                 f15_miprestamo, f15_gastosa, f15_gastosm, f15_gastos1, f15_gastos2, f15_formaPago, f15_usuaCrea, f15_fechaCrea, f15_obs, f15_obs2)
                VALUES (:codigo, :fecha, :operacion, :cliente, :prestamo, :maprestamo,
                        :miprestamo, :gastosa, :gastosm, :gastos1, :gastos2, :formaPago, :usuaCrea, NOW(), :obs, :obs2)";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([
            'codigo' => $codigoMovimiento,
            'fecha' => date('Y-m-d'),
            'operacion' => 10,
            'cliente' => trim($datos['cliente']),
            'prestamo' => $prestamoId,
            'maprestamo' => $sobregiro,
            'miprestamo' => $amortizacion,
            'gastosa' => $gastosAdm,
            'gastosm' => $gastosMora,
            'gastos1' => $otros1,
            'gastos2' => $otros2,
            'obs' => $datos['obs'] ?? '',
            'obs2' => $datos['obs2'] ?? '',
            'formaPago' => $datos['formaPago'] ?? 'EFECTIVO',
            'usuaCrea' => $_SESSION['user_id'] ?? 'system',
        ]);
    }

    /**
     * Inserta en diario contable
     */
    private function insertarDiarioContable(PDO $pdo, int $codigoMovimiento, array $datos, array $datosContables): void
    {
        $tipoAsiento = $datosContables['tipoAsiento'] ?? 'haber';
        $monto = (float) ($datos['sobregiro'] ?? 0);

        $debe = $tipoAsiento === 'debe' ? $monto : 0;
        $haber = $tipoAsiento === 'haber' ? $monto : 0;

        $sql = "INSERT INTO f37_diario_contable
                (f37_movimiento, f37_fecha, f37_codigo_contable, f37_operacion, f37_descripcion,
                 f37_debe, f37_haber, f37_usuario_crea, f37_fecha_crea, f37_estado)
                VALUES (:movimiento, :fecha, :codigoContable, :operacion, :descripcion,
                        :debe, :haber, :usuario, NOW(), 'ACTIVO')";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([
            'movimiento' => $codigoMovimiento,
            'fecha' => date('Y-m-d'),
            'codigoContable' => trim($datosContables['codigoContable'] ?? ''),
            'operacion' => 10,
            'descripcion' => $datosContables['descripcionContable'] ?? 'PAGO SOBREGIRO PRESTAMO',
            'debe' => $debe,
            'haber' => $haber,
            'usuario' => trim($datos['usuario'] ?? 'SISTEMA'),
        ]);
    }
}
