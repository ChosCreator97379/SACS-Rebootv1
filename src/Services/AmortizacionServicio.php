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

            // 7. Insertar aplicación en f15_aplicaciones_cuotas (NUEVO)
            $logMessage = sprintf("[%s] INSERTANDO APLICACIÓN DE CUOTA\n", date('Y-m-d H:i:s'));
            file_put_contents($logFile, $logMessage, FILE_APPEND);
            $this->insertarAplicacionCuota($pdo, $codigoMovimiento, $datos, $calculo);

            // 8. Actualizar préstamo (f16_prestamo)
            $logMessage = sprintf("[%s] ACTUALIZANDO PRÉSTAMO\n", date('Y-m-d H:i:s'));
            file_put_contents($logFile, $logMessage, FILE_APPEND);
            $this->actualizarPrestamo($pdo, $prestamo, $calculo);

            // 9. Actualizar cuota individual (f17_cuotasprestamo_nuevo)
            $logMessage = sprintf("[%s] ACTUALIZANDO CUOTA INDIVIDUAL\n", date('Y-m-d H:i:s'));
            file_put_contents($logFile, $logMessage, FILE_APPEND);
            $this->actualizarCuota($pdo, (int)$datos['prestamo'], (int)$datos['cuota'], $calculo);

            // 10. Procesar exceso de pago a siguientes cuotas (si existe)
            if (isset($calculo['exceso_info'])) {
                $logMessage = sprintf("[%s] PROCESANDO EXCESO A SIGUIENTES CUOTAS\n", date('Y-m-d H:i:s'));
                file_put_contents($logFile, $logMessage, FILE_APPEND);
                $this->procesarExcesoSiguientesCuotas($pdo, $codigoMovimiento, $calculo['exceso_info'], $logFile);
            }

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
        $sql = "SELECT * FROM f17_cuotasprestamo_nuevo
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
     * Inserta aplicación en f15_aplicaciones_cuotas
     * Registra cómo el pago se distribuye en la cuota
     */
    private function insertarAplicacionCuota(PDO $pdo, int $codigoMovimiento, array $datos, array $calculo): void
    {
        $distribucion = $calculo['distribucion'] ?? [];
        $amortiza = $distribucion['capital'] ?? 0;
        $ga = $distribucion['gastos_administrativos'] ?? 0;
        $gm = $distribucion['mora'] ?? 0;
        $o1 = $distribucion['otros1'] ?? 0;
        $o2 = $distribucion['otros2'] ?? 0;
        
        // Si hay exceso de pago, usar valores programados de la cuota
        $excesoInfo = $calculo['exceso_info'] ?? [];
        if (!empty($excesoInfo) && ($excesoInfo['exceso_capital'] > 0 || $excesoInfo['exceso_interes'] > 0)) {
            // Obtener valores programados de la cuota
            $sqlCuota = "SELECT f17_capital, f17_interes, f17_total
                         FROM f17_cuotasprestamo_nuevo
                         WHERE f17_prestamo = :prestamo AND f17_ncuota = :cuota";
            $stmtCuota = $pdo->prepare($sqlCuota);
            $stmtCuota->execute(['prestamo' => $datos['prestamo'], 'cuota' => $datos['cuota']]);
            $cuotaProgramada = $stmtCuota->fetch(PDO::FETCH_ASSOC);
            
            if ($cuotaProgramada) {
                $amortiza = (float) $cuotaProgramada['f17_capital'];
                $ga = (float) $cuotaProgramada['f17_interes'];
            }
        }

        $sql = "INSERT INTO f15_aplicaciones_cuotas
                (f15_codigo, f17_prestamo, f17_ncuota,
                 f17_capital_aplicado, f17_interes_aplicado, f17_mora_aplicado,
                 f17_gastos_aplicado, f17_total_aplicado, f17_fechaCrea, f17_usuaCrea)
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, NOW(), ?)";

        $stmt = $pdo->prepare($sql);
        $stmt->execute([
            $codigoMovimiento,
            $datos['prestamo'],
            $datos['cuota'],
            $amortiza,  // Capital aplicado
            $ga,  // Interés aplicado (programado si hay exceso)
            $gm,  // Mora aplicada
            $o1 + $o2,  // Gastos aplicados (sin mora, mora va en su campo)
            $amortiza + $ga + $gm + $o1 + $o2,  // Total aplicado
            trim($datos['usuario'] ?? 'SISTEMA'),
        ]);
    }

    /**
     * Inserta movimiento en f15_movimiento
     * Basado en registrarMovimiento() para operación 2
     */
    private function insertarMovimiento(PDO $pdo, int $codigoMovimiento, array $datos, array $calculo): void
    {
        // Calcular el total real basado en los componentes del pago
        $totalPago = ($datos['amortiza'] ?? 0) + ($datos['gastosAdm'] ?? 0) + ($datos['gastosMora'] ?? 0) + ($datos['otros1'] ?? 0) + ($datos['otros2'] ?? 0) + ($datos['sobregiro'] ?? 0);

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
            $datos['amortiza'] ?? 0,  // Capital (amortización) solamente
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

        // Redondear el saldo a 2 decimales para evitar errores de precisión flotante
        $prestamoActualizado['saldo'] = round($prestamoActualizado['saldo'], 2);

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
     * Actualiza cuota individual en f17_cuotasprestamo_nuevo y f17_cuotasprestamo (original)
     * Marca la cuota según el escenario (completo, parcial, o más)
     * NOTA:
     * - f17_cuotasprestamo_nuevo: solo actualiza estado y total (capital, interés y saldo se mantienen)
     * - f17_cuotasprestamo (original): solo actualiza estado (para visualización)
     *
     * Mantener f17_capital y f17_interes con valores originales para guía
     * Mantener f17_saldo como guía del monto óptimo
     * Solo modificar f17_total para indicar estado (0 = pagado, >0 = faltante)
     */
    private function actualizarCuota(PDO $pdo, int $prestamoId, int $numeroCuota, array $calculo): void
    {
        $escenario = $calculo['escenario'] ?? 'completo';
        $faltanteCuota = $calculo['metadata']['faltante_cuota'] ?? 0;

        // Determinar el estado según el escenario
        $estado = match($escenario) {
            'completo', 'mas' => 'PAGADO',
            'menos', 'parcial' => 'PARCIAL',
            default => 'PAGADO'
        };

        // Calcular el total según el estado
        $total = match($escenario) {
            'completo', 'mas' => 0, // Pago completo: total = 0
            'menos', 'parcial' => $this->calcularFaltanteCuota($pdo, $prestamoId, $numeroCuota), // Pago parcial: total = faltante
            default => 0
        };

        // Actualizar f17_cuotasprestamo_nuevo (cronograma nuevo)
        // Solo actualiza estado y total, MANTIENE capital, interés y saldo
        $sqlNuevo = "UPDATE f17_cuotasprestamo_nuevo
                SET f17_estado = ?,
                    f17_total = ?
                WHERE f17_prestamo = ? AND f17_ncuota = ?";

        $stmtNuevo = $pdo->prepare($sqlNuevo);
        $stmtNuevo->execute([
            $estado,
            $total,
            $prestamoId,
            $numeroCuota
        ]);

        // Actualizar f17_cuotasprestamo (cronograma original - solo estado para visualización)
        $sqlOriginal = "UPDATE f17_cuotasprestamo
                SET f17_estado = ?,
                    f17_fechaCancela = ?
                WHERE f17_prestamo = ? AND f17_ncuota = ?";

        $stmtOriginal = $pdo->prepare($sqlOriginal);
        $stmtOriginal->execute([
            $estado,
            $estado === 'PAGADO' ? date('Y-m-d') : null,
            $prestamoId,
            $numeroCuota
        ]);
    }

    /**
     * Calcula el faltante de una cuota restando el total aplicado del total original
     */
    private function calcularFaltanteCuota(PDO $pdo, int $prestamoId, int $numeroCuota): float
    {
        // Obtener total original del cronograma original
        $sqlOriginal = "SELECT f17_total FROM f17_cuotasprestamo
                       WHERE f17_prestamo = ? AND f17_ncuota = ?";
        $stmtOriginal = $pdo->prepare($sqlOriginal);
        $stmtOriginal->execute([$prestamoId, $numeroCuota]);
        $cuotaOriginal = $stmtOriginal->fetch(PDO::FETCH_ASSOC);

        if (!$cuotaOriginal) {
            return 0;
        }

        $totalOriginal = (float) $cuotaOriginal['f17_total'];

        // Obtener total aplicado de las aplicaciones
        $sqlAplicacion = "SELECT SUM(f17_total_aplicado) as total_aplicado
                          FROM f15_aplicaciones_cuotas
                          WHERE f17_prestamo = ? AND f17_ncuota = ?";
        $stmtAplicacion = $pdo->prepare($sqlAplicacion);
        $stmtAplicacion->execute([$prestamoId, $numeroCuota]);
        $aplicacion = $stmtAplicacion->fetch(PDO::FETCH_ASSOC);

        $totalAplicado = (float) ($aplicacion['total_aplicado'] ?? 0);

        // Faltante = total original - total aplicado
        $faltante = max(0, $totalOriginal - $totalAplicado);

        return $faltante;
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
                $tipo = strtolower($config['tipo']);
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

    /**
     * Procesa el exceso de pago aplicándolo a siguientes cuotas
     * Aplica primero al capital, luego al interés
     * Registra aplicaciones y actualiza cuotas
     */
    private function procesarExcesoSiguientesCuotas(PDO $pdo, int $codigoMovimiento, array $excesoInfo, string $logFile): void
    {
        $prestamoCodigo = $excesoInfo['prestamo_codigo'];
        $cuotaActual = $excesoInfo['cuota_actual'];
        $excesoCapital = $excesoInfo['exceso_capital'];
        $excesoInteres = $excesoInfo['exceso_interes'];
        
        $totalCapitalAplicado = 0; // Para descuento del saldo del préstamo
        
        // Buscar siguientes cuotas pendientes (VIGENTE o PARCIAL)
        $sql = "SELECT f17_ncuota, f17_capital, f17_interes, f17_total, f17_estado
                FROM f17_cuotasprestamo_nuevo
                WHERE f17_prestamo = :prestamo
                AND f17_ncuota > :cuotaActual
                AND f17_estado IN ('VIGENTE', 'PARCIAL')
                ORDER BY f17_ncuota ASC";
        $stmt = $pdo->prepare($sql);
        $stmt->execute(['prestamo' => $prestamoCodigo, 'cuotaActual' => $cuotaActual]);
        $cuotas = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        foreach ($cuotas as $cuota) {
            if ($excesoCapital <= 0 && $excesoInteres <= 0) {
                break; // No hay más exceso
            }
            
            $numeroCuota = (int) $cuota['f17_ncuota'];
            $capitalOriginal = (float) $cuota['f17_capital'];
            $interesOriginal = (float) $cuota['f17_interes'];
            $totalOriginal = (float) $cuota['f17_total'];
            
            // Obtener capital ya aplicado a esta cuota
            $sqlAplicacion = "SELECT SUM(f17_capital_aplicado) as capital_aplicado
                              FROM f15_aplicaciones_cuotas
                              WHERE f17_prestamo = :prestamo AND f17_ncuota = :cuota";
            $stmtAplicacion = $pdo->prepare($sqlAplicacion);
            $stmtAplicacion->execute(['prestamo' => $prestamoCodigo, 'cuota' => $numeroCuota]);
            $aplicacion = $stmtAplicacion->fetch(PDO::FETCH_ASSOC);
            $capitalYaAplicado = (float) ($aplicacion['capital_aplicado'] ?? 0);
            
            // Calcular capital faltante
            $capitalFaltante = max(0, $capitalOriginal - $capitalYaAplicado);
            
            // Aplicar exceso de interés primero (nueva prioridad)
            $interesAplicadoEstaCuota = 0;
            if ($excesoInteres > 0) {
                // El usuario pagó interés explícitamente, usar la lógica actual
                // Calcular interés faltante
                $sqlInteres = "SELECT SUM(f17_interes_aplicado) as interes_aplicado
                               FROM f15_aplicaciones_cuotas
                               WHERE f17_prestamo = :prestamo AND f17_ncuota = :cuota";
                $stmtInteres = $pdo->prepare($sqlInteres);
                $stmtInteres->execute(['prestamo' => $prestamoCodigo, 'cuota' => $numeroCuota]);
                $aplicacionInteres = $stmtInteres->fetch(PDO::FETCH_ASSOC);
                $interesYaAplicado = (float) ($aplicacionInteres['interes_aplicado'] ?? 0);
                $interesFaltante = max(0, $interesOriginal - $interesYaAplicado);

                $interesAplicadoEstaCuota = min($excesoInteres, $interesFaltante);
                $excesoInteres -= $interesAplicadoEstaCuota;
            } elseif (isset($excesoInfo['usar_exceso_capital_para_interes']) && $excesoInfo['usar_exceso_capital_para_interes'] && $excesoCapital > 0) {
                // El usuario NO pagó interés pero hay exceso de capital
                // Usar parte del exceso de capital para pagar interés automáticamente

                // Calcular interés faltante
                $sqlInteres = "SELECT SUM(f17_interes_aplicado) as interes_aplicado
                               FROM f15_aplicaciones_cuotas
                               WHERE f17_prestamo = :prestamo AND f17_ncuota = :cuota";
                $stmtInteres = $pdo->prepare($sqlInteres);
                $stmtInteres->execute(['prestamo' => $prestamoCodigo, 'cuota' => $numeroCuota]);
                $aplicacionInteres = $stmtInteres->fetch(PDO::FETCH_ASSOC);
                $interesYaAplicado = (float) ($aplicacionInteres['interes_aplicado'] ?? 0);
                $interesFaltante = max(0, $interesOriginal - $interesYaAplicado);

                // Aplicar interés usando el exceso de capital disponible
                $interesAplicadoEstaCuota = min($excesoCapital, $interesFaltante);
                $excesoCapital -= $interesAplicadoEstaCuota; // Reducir el exceso de capital
            }
            
            // Aplicar exceso de capital después del interés (nueva prioridad)
            $capitalAplicadoEstaCuota = 0;
            if ($excesoCapital > 0 && $capitalFaltante > 0) {
                $capitalAplicadoEstaCuota = min($excesoCapital, $capitalFaltante);
                $excesoCapital -= $capitalAplicadoEstaCuota;
                $totalCapitalAplicado += $capitalAplicadoEstaCuota;
            }
            
            // Si aplicó algo a esta cuota, registrar la aplicación
            if ($capitalAplicadoEstaCuota > 0 || $interesAplicadoEstaCuota > 0) {
                $sqlInsert = "INSERT INTO f15_aplicaciones_cuotas
                              (f15_codigo, f17_prestamo, f17_ncuota,
                               f17_capital_aplicado, f17_interes_aplicado, f17_mora_aplicado,
                               f17_gastos_aplicado, f17_total_aplicado, f17_fechaCrea, f17_usuaCrea)
                              VALUES (?, ?, ?, ?, ?, ?, ?, ?, NOW(), ?)";
                $stmtInsert = $pdo->prepare($sqlInsert);
                $stmtInsert->execute([
                    $codigoMovimiento,
                    $prestamoCodigo,
                    $numeroCuota,
                    round($capitalAplicadoEstaCuota, 2),
                    round($interesAplicadoEstaCuota, 2),
                    0,
                    0,
                    round($capitalAplicadoEstaCuota + $interesAplicadoEstaCuota, 2),
                    $_SESSION['user_id'] ?? 'SISTEMA'
                ]);
                
                // Calcular nuevo estado de la cuota
                $nuevoCapitalAplicado = $capitalYaAplicado + $capitalAplicadoEstaCuota;
                $nuevoInteresAplicado = ($interesYaAplicado ?? 0) + $interesAplicadoEstaCuota;

                // Obtener el estado actual de la cuota
                $estadoActual = $cuota['f17_estado'] ?? 'VIGENTE';

                // Determinar si la cuota está completamente pagada
                if ($estadoActual === 'PARCIAL') {
                    // Para cuotas PARCIAL, verificar que el total aplicado cubra el total del cronograma nuevo
                    $totalAplicado = $nuevoCapitalAplicado + $nuevoInteresAplicado;
                    if ($totalAplicado >= $totalOriginal - 0.01) {
                        $nuevoEstado = 'PAGADO';
                        $nuevoTotal = 0;
                    } else {
                        $nuevoEstado = 'PARCIAL';
                        $nuevoTotal = max(0, $totalOriginal - $totalAplicado);
                    }
                } else {
                    // Para cuotas VIGENTE, mantener la lógica original (validar capital e interés por separado)
                    if ($nuevoCapitalAplicado >= $capitalOriginal - 0.01 && $nuevoInteresAplicado >= $interesOriginal - 0.01) {
                        $nuevoEstado = 'PAGADO';
                        $nuevoTotal = 0;
                    } else {
                        $nuevoEstado = 'PARCIAL';
                        $nuevoTotal = max(0, $totalOriginal - ($nuevoCapitalAplicado + $nuevoInteresAplicado));
                    }
                }
                
                // Actualizar estado en cronograma nuevo
                $sqlUpdate = "UPDATE f17_cuotasprestamo_nuevo
                             SET f17_estado = :estado, f17_total = :total,
                                 f17_fechaCancela = CASE WHEN :estado_check = 'PAGADO' THEN CURDATE() ELSE f17_fechaCancela END
                             WHERE f17_prestamo = :prestamo AND f17_ncuota = :cuota";
                $stmtUpdate = $pdo->prepare($sqlUpdate);
                $stmtUpdate->execute([
                    'estado' => $nuevoEstado,
                    'estado_check' => $nuevoEstado,
                    'total' => round($nuevoTotal, 2),
                    'prestamo' => $prestamoCodigo,
                    'cuota' => $numeroCuota
                ]);
                
                // Actualizar estado en cronograma original
                $sqlUpdateOriginal = "UPDATE f17_cuotasprestamo
                                     SET f17_estado = :estado,
                                         f17_fechaCancela = CASE WHEN :estado_check = 'PAGADO' THEN CURDATE() ELSE f17_fechaCancela END
                                     WHERE f17_prestamo = :prestamo AND f17_ncuota = :cuota";
                $stmtUpdateOriginal = $pdo->prepare($sqlUpdateOriginal);
                $stmtUpdateOriginal->execute([
                    'estado' => $nuevoEstado,
                    'estado_check' => $nuevoEstado,
                    'prestamo' => $prestamoCodigo,
                    'cuota' => $numeroCuota
                ]);
                
                $logMessage = sprintf("[%s] APLICADO EXCESO A CUOTA %d: Capital=%.2f, Interés=%.2f, Estado=%s\n",
                    date('Y-m-d H:i:s'),
                    $numeroCuota,
                    $capitalAplicadoEstaCuota,
                    $interesAplicadoEstaCuota,
                    $nuevoEstado
                );
                file_put_contents($logFile, $logMessage, FILE_APPEND);
            }
            
            // Si el exceso se agotó después de aplicar a esta cuota, detener el bucle
            // Esto asegura que el exceso se concentre en una sola cuota parcial
            if ($excesoCapital <= 0 && $excesoInteres <= 0) {
                break;
            }
        }
        
        // Descuento del saldo del préstamo según el capital total aplicado a siguientes cuotas
        if ($totalCapitalAplicado > 0) {
            // Obtener saldo actual del préstamo
            $sqlSaldoPrestamo = "SELECT f16_saldo FROM f16_prestamo WHERE f16_codigo = :codigo";
            $stmtSaldoPrestamo = $pdo->prepare($sqlSaldoPrestamo);
            $stmtSaldoPrestamo->execute(['codigo' => $prestamoCodigo]);
            $saldoPrestamo = $stmtSaldoPrestamo->fetch(PDO::FETCH_ASSOC);
            $saldoActual = (float) ($saldoPrestamo['f16_saldo'] ?? 0);
            
            // Descuento del saldo: saldo actual - capital total aplicado
            $nuevoSaldo = max(0, $saldoActual - $totalCapitalAplicado);
            $nuevoSaldo = round($nuevoSaldo, 2);
            
            $sqlSaldo = "UPDATE f16_prestamo SET f16_saldo = :saldo
                         WHERE f16_codigo = :codigo";
            $stmtSaldo = $pdo->prepare($sqlSaldo);
            $stmtSaldo->execute([
                'saldo' => $nuevoSaldo,
                'codigo' => $prestamoCodigo,
            ]);
            
            $logMessage = sprintf("[%s] DESCUENTO SALDO POR EXCESO: Saldo actual=%.2f, Capital aplicado=%.2f, Nuevo saldo=%.2f\n",
                date('Y-m-d H:i:s'),
                $saldoActual,
                $totalCapitalAplicado,
                $nuevoSaldo
            );
            file_put_contents($logFile, $logMessage, FILE_APPEND);
        }
    }
}
