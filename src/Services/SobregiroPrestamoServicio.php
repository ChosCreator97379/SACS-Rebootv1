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
     * @param array $codigosContables Códigos contables por componente
     * @return array Resultado de la operación
     */
    public function ejecutarPagoSobregiroPrestamo(array $datos, array $codigosContables = []): array
    {
        $logFile = dirname(__DIR__, 2) . '/logs/sobregiro_prestamo.log';

        // Log de datos recibidos para debugging
        $logMessage = sprintf(
            "[%s] DEBUG - Datos recibidos: %s, Códigos contables: %s\n",
            date('Y-m-d H:i:s'),
            json_encode($datos),
            json_encode($codigosContables)
        );
        file_put_contents($logFile, $logMessage, FILE_APPEND);

        try {
            $this->pdo->beginTransaction();

            // Validar datos requeridos
            $clienteCodigo = trim($datos['cliente'] ?? '');
            $prestamoCodigo = trim($datos['prestamo'] ?? '');
            $montoTotal = (float) ($datos['monto'] ?? 0);
            $sobregiro = (float) ($datos['sobregiro'] ?? 0);
            $amortizacion = (float) ($datos['amortizacion'] ?? 0);
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

            // Generar código de movimiento
            $codigoMovimiento = $this->generarCodigoMovimiento($this->pdo);

            // Insertar en diario contable (múltiples filas por componente) - PRIMERO
            if (!empty($codigosContables)) {
                $this->insertarDiarioContableMultiple($this->pdo, $codigoMovimiento, $datos, $codigosContables, $logFile);
            }

            // Insertar movimiento - DESPUÉS
            $this->insertarMovimientoSobregiro(
                $this->pdo,
                $codigoMovimiento,
                $datos,
                $sobregiro,
                $amortizacion,
                $gastosAdm,
                $gastosMora,
                $otros1,
                $otros2
            );

            // Actualizar préstamo
            $this->actualizarPrestamo(
                $this->pdo,
                (int) $prestamoCodigo,
                $sobregiro,
                $gastosAdm,
                $gastosMora,
                $otros1,
                $otros2,
                $amortizacion
            );

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
        $sql = "SELECT f16_codigo, f16_sobre, f16_gastosa, f16_gastosm, f16_otros, f16_otros2,
                       f16_saldo
                FROM f16_prestamo
                WHERE f16_codigo = :codigo
                LIMIT 1";
        $stmt = $pdo->prepare($sql);
        $stmt->execute(['codigo' => $prestamoCodigo]);
        $result = $stmt->fetch();
        return $result ?: null;
    }

    /**
     * Inserta múltiples registros en diario contable (uno por cada componente)
     */
    private function insertarDiarioContableMultiple(PDO $pdo, int $codigoMovimiento, array $datos, array $codigosContables, string $logFile): void
    {
        $sql = "INSERT INTO f37_diario_contable
                (f37_movimiento, f37_fecha, f37_codigo_contable, f37_operacion, f37_descripcion,
                 f37_debe, f37_haber, f37_usuario_crea, f37_fecha_crea, f37_estado)
                VALUES (:movimiento, :fecha, :codigoContable, :operacion, :descripcion,
                        :debe, :haber, :usuario, NOW(), 'ACTIVO')";

        $stmt = $pdo->prepare($sql);

        foreach ($codigosContables as $componente => $config) {
            // Solo insertar si hay monto mayor a 0 y tiene código contable
            if ($config['monto'] > 0 && !empty($config['codigo'])) {
                // Usar tipo proporcionado o tipo por defecto según el componente
                if (!empty($config['tipo'])) {
                    $tipo = $config['tipo'];
                } else {
                    // Tipo por defecto según componente
                    $tipo = $this->obtenerTipoPorDefecto($componente);
                }
                $monto = $config['monto'];

                $debe = $tipo === 'debe' ? $monto : 0.00;
                $haber = $tipo === 'haber' ? $monto : 0.00;

                $descripcion = 'PAGO SOBREGIRO PRESTAMO - ' . strtoupper($componente);
                
                // Para amortización, usar nombre más descriptivo
                if ($componente === 'amortizacion') {
                    $descripcion = 'PAGO SOBREGIRO PRESTAMO - AMORTIZACION ADICIONAL';
                }

                $stmt->execute([
                    'movimiento' => $codigoMovimiento,
                    'fecha' => $datos['fecha'] ?? date('Y-m-d'),
                    'codigoContable' => trim($config['codigo']),
                    'operacion' => 10,
                    'descripcion' => $descripcion,
                    'debe' => $debe,
                    'haber' => $haber,
                    'usuario' => trim($datos['usuario'] ?? 'SISTEMA'),
                ]);
            }
        }
    }

    /**
     * Obtiene el tipo por defecto según el componente
     */
    private function obtenerTipoPorDefecto(string $componente): string
    {
        // Amortización adicional: reduce saldo (debe)
        if ($componente === 'amortizacion') {
            return 'debe';
        }
        // Gastos de mora: ingreso (haber)
        if ($componente === 'gastosMora') {
            return 'haber';
        }
        // Otros: ingreso (haber)
        if ($componente === 'otros1' || $componente === 'otros2') {
            return 'haber';
        }
        // Por defecto para el resto
        return 'haber';
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
        float $amortizacion,
        float $gastosAdm,
        float $gastosMora,
        float $otros1,
        float $otros2
    ): void {
        $prestamoCodigo = trim($datos['prestamo']);
        $prestamoId = $prestamoCodigo !== '' ? (int) $prestamoCodigo : null;

        // Sumar amortización adicional al sobregiro para el movimiento
        $maprestamoTotal = $sobregiro + $amortizacion;

        $sql = "INSERT INTO f15_movimiento
                (f15_codigo, f15_fecha, f15_operacion, f15_cliente, f15_prestamo, f15_maprestamo,
                 f15_gastosa, f15_gastosm, f15_gastos1, f15_gastos2, f15_formaPago, f15_usuaCrea, f15_fechaCrea, f15_obs, f15_obs2)
                VALUES (:codigo, :fecha, :operacion, :cliente, :prestamo, :maprestamo,
                        :gastosa, :gastosm, :gastos1, :gastos2, :formaPago, :usuaCrea, NOW(), :obs, :obs2)";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([
            'codigo' => $codigoMovimiento,
            'fecha' => date('Y-m-d'),
            'operacion' => 10,
            'cliente' => trim($datos['cliente']),
            'prestamo' => $prestamoId,
            'maprestamo' => $maprestamoTotal,
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
     * Actualiza préstamo
     */
    private function actualizarPrestamo(
        PDO $pdo,
        int $prestamoCodigo,
        float $sobregiro,
        float $gastosAdm,
        float $gastosMora,
        float $otros1,
        float $otros2,
        float $descuenta
    ): void {
        // Reducir sobregiro
        $sqlSobregiro = "UPDATE f16_prestamo SET f16_sobre = GREATEST(0, f16_sobre - :sobregiro) WHERE f16_codigo = :codigo";
        $stmtSobregiro = $pdo->prepare($sqlSobregiro);
        $stmtSobregiro->execute([
            'sobregiro' => $sobregiro,
            'codigo' => $prestamoCodigo,
        ]);

        // Reducir gastos administrativos
        $sqlGastosa = "UPDATE f16_prestamo SET f16_gastosa = GREATEST(0, f16_gastosa - :gastosa) WHERE f16_codigo = :codigo";
        $stmtGastosa = $pdo->prepare($sqlGastosa);
        $stmtGastosa->execute([
            'gastosa' => $gastosAdm,
            'codigo' => $prestamoCodigo,
        ]);

        // Reducir mora
        $sqlMora = "UPDATE f16_prestamo SET f16_gastosm = GREATEST(0, f16_gastosm - :mora) WHERE f16_codigo = :codigo";
        $stmtMora = $pdo->prepare($sqlMora);
        $stmtMora->execute([
            'mora' => $gastosMora,
            'codigo' => $prestamoCodigo,
        ]);

        // Reducir otros1
        $sqlOtros1 = "UPDATE f16_prestamo SET f16_otros = GREATEST(0, f16_otros - :otros1) WHERE f16_codigo = :codigo";
        $stmtOtros1 = $pdo->prepare($sqlOtros1);
        $stmtOtros1->execute([
            'otros1' => $otros1,
            'codigo' => $prestamoCodigo,
        ]);

        // Reducir otros2
        $sqlOtros2 = "UPDATE f16_prestamo SET f16_otros2 = GREATEST(0, f16_otros2 - :otros2) WHERE f16_codigo = :codigo";
        $stmtOtros2 = $pdo->prepare($sqlOtros2);
        $stmtOtros2->execute([
            'otros2' => $otros2,
            'codigo' => $prestamoCodigo,
        ]);

        // Reducir saldo si hay amortización adicional
        if ($descuenta > 0) {
            $sqlSaldo = "UPDATE f16_prestamo SET f16_saldo = GREATEST(0, f16_saldo - :descuenta) WHERE f16_codigo = :codigo";
            $stmtSaldo = $pdo->prepare($sqlSaldo);
            $stmtSaldo->execute([
                'descuenta' => $descuenta,
                'codigo' => $prestamoCodigo,
            ]);

            // Reducir cuotas pendientes si la amortización cubre completamente una cuota
            // Obtener la primera cuota pendiente
            $sqlCuota = "SELECT f17_saldo FROM f17_cuotasprestamo 
                          WHERE f17_prestamo = :prestamo AND f17_estado = 'VIGENTE' 
                          ORDER BY f17_ncuota ASC LIMIT 1";
            $stmtCuota = $pdo->prepare($sqlCuota);
            $stmtCuota->execute(['prestamo' => $prestamoCodigo]);
            $cuota = $stmtCuota->fetch();

            if ($cuota && $descuenta >= (float) $cuota['f17_saldo']) {
                // Si la amortización cubre el saldo de la cuota, reducir cuotas pendientes
                $sqlCuotasPend = "UPDATE f16_prestamo SET f16_cuotasPend = GREATEST(0, f16_cuotasPend - 1) 
                                     WHERE f16_codigo = :codigo";
                $stmtCuotasPend = $pdo->prepare($sqlCuotasPend);
                $stmtCuotasPend->execute(['codigo' => $prestamoCodigo]);
            }
        }

        // Actualizar fecha de última actualización
        $sqlFechaUP = "UPDATE f16_prestamo SET f16_fechaUP = :fechaUP WHERE f16_codigo = :codigo";
        $stmtFechaUP = $pdo->prepare($sqlFechaUP);
        $stmtFechaUP->execute([
            'fechaUP' => date('Y-m-d'),
            'codigo' => $prestamoCodigo,
        ]);
    }
}
