<?php

declare(strict_types=1);

namespace Sacs\Services;

use PDO;
use RuntimeException;

/**
 * Servicio para procesar pagos combinados de FONBES y COOPAM
 * Coordina ambos pagos en una sola transacción
 */
final class AportesCombinadosServicio
{
    public function __construct(
        private readonly PDO $pdo
    ) {
    }

    /**
     * Ejecuta un pago combinado de FONBES y COOPAM
     *
     * @param array $datos Datos del formulario
     * @param array $codigosContablesFonbes Códigos contables FONBES
     * @param array $codigosContablesCoopam Códigos contables COOPAM
     * @return array Resultado de la operación
     */
    public function ejecutarPagoCombinado(array $datos, array $codigosContablesFonbes, array $codigosContablesCoopam): array
    {
        $logFile = dirname(__DIR__, 2) . '/logs/aportes_combinados.log';

        // Asegurar que el directorio de logs existe
        if (!is_dir(dirname($logFile))) {
            mkdir(dirname($logFile), 0755, true);
        }

        try {
            // Registrar inicio de operación
            $logMessage = sprintf(
                "[%s] INICIO - Cliente: %s, Periodo: %s/%s, Monto FONBES: %.2f, Monto COOPAM: %.2f\n",
                date('Y-m-d H:i:s'),
                $datos['cliente'],
                $datos['periodo'],
                $datos['anio'],
                $datos['montoFonbes'] ?? 0,
                $datos['montoCoopam'] ?? 0
            );
            file_put_contents($logFile, $logMessage, FILE_APPEND);

            // 1. Validar que el socio tenga ambos tipos de aporte
            $socio = $this->obtenerSocio($this->pdo, trim($datos['cliente']));
            if ($socio === null) {
                throw new RuntimeException('Socio no encontrado');
            }

            $tieneFonbes = ($socio['F01_APORTACION'] ?? 0) > 0;
            $tieneCoopam = ($socio['F01_APORTE_COPE'] ?? 0) > 0;

            if (!$tieneFonbes && !$tieneCoopam) {
                throw new RuntimeException('Este socio no tiene aportes FONBES ni COOPAM configurados.');
            }

            // Verificar que los montos enviados coincidan con lo que tiene el socio
            if ($tieneFonbes && $datos['montoFonbes'] > 0) {
                // El socio tiene FONBES y se quiere pagar FONBES - OK
            } elseif (!$tieneFonbes && $datos['montoFonbes'] > 0) {
                throw new RuntimeException('ESTE_SOCIO_SOLO_COOPAM');
            }

            if ($tieneCoopam && $datos['montoCoopam'] > 0) {
                // El socio tiene COOPAM y se quiere pagar COOPAM - OK
            } elseif (!$tieneCoopam && $datos['montoCoopam'] > 0) {
                throw new RuntimeException('ESTE_SOCIO_SOLO_FONBES');
            }

            // Si no se quiere pagar nada, error
            if ($datos['montoFonbes'] <= 0 && $datos['montoCoopam'] <= 0) {
                throw new RuntimeException('Debe pagar al menos un aporte (FONBES o COOPAM).');
            }

            // 2. NO iniciar transacción aquí - cada servicio maneja su propia transacción
            // $this->pdo->beginTransaction();

            // 3. Preparar datos para FONBES (1 mes)
            $datosFonbes = [
                'cliente' => $datos['cliente'],
                'anio' => $datos['anio'],
                'periodo' => $datos['periodo'],
                'periodo2' => $datos['periodo'], // Mismo mes
                'anio2' => $datos['anio'],
                'monto' => $datos['montoFonbes'],
                'formaPago' => $datos['formaPago'],
                'obs' => $datos['obs'] ?? '',
                'obs2' => $datos['obs2'] ?? '',
                'usuario' => $datos['usuario'] ?? 'SISTEMA',
            ];

            // 4. Preparar datos para COOPAM (1 mes)
            $datosCoopam = [
                'cliente' => $datos['cliente'],
                'anio' => $datos['anio'],
                'periodo' => $datos['periodo'],
                'periodo2' => $datos['periodo'], // Mismo mes
                'anio2' => $datos['anio'],
                'monto' => $datos['montoCoopam'],
                'formaPago' => $datos['formaPago'],
                'obs' => $datos['obs'] ?? '',
                'obs2' => $datos['obs2'] ?? '',
                'usuario' => $datos['usuario'] ?? 'SISTEMA',
            ];

            // 5. Procesar pago FONBES (cada servicio maneja su propia transacción)
            $servicioFonbes = new AportesServicio($this->pdo);
            $resultadoFonbes = $servicioFonbes->ejecutarPagoAportesFonbes($datosFonbes, $codigosContablesFonbes);

            if (!$resultadoFonbes['exito']) {
                $logMessage = sprintf(
                    "[%s] ERROR FONBES - Cliente: %s, Error: %s\n",
                    date('Y-m-d H:i:s'),
                    $datos['cliente'],
                    $resultadoFonbes['mensaje'] ?? 'Error desconocido'
                );
                file_put_contents($logFile, $logMessage, FILE_APPEND);

                throw new RuntimeException('Error al procesar pago FONBES: ' . ($resultadoFonbes['mensaje'] ?? 'Error desconocido'));
            }

            $codigoMovimientoFonbes = $resultadoFonbes['codigo_movimiento'];

            // 6. Procesar pago COOPAM (cada servicio maneja su propia transacción)
            $servicioCoopam = new AportesCoopamServicio($this->pdo);
            $resultadoCoopam = $servicioCoopam->ejecutarPagoAportesCoopam($datosCoopam, $codigosContablesCoopam);

            if (!$resultadoCoopam['exito']) {
                // Intentar revertir el pago FONBES manualmente
                $logMessage = sprintf(
                    "[%s] ERROR COOPAM - Cliente: %s, Error: %s - Intentando revertir FONBES\n",
                    date('Y-m-d H:i:s'),
                    $datos['cliente'],
                    $resultadoCoopam['mensaje'] ?? 'Error desconocido'
                );
                file_put_contents($logFile, $logMessage, FILE_APPEND);

                // Revertir FONBES manualmente (devolución del pago)
                try {
                    require_once __DIR__ . '/DevolucionFonbesServicio.php';
                    $devolucionServicio = new DevolucionFonbesServicio($this->pdo);
                    $devolucionDatos = [
                        'cliente' => $datos['cliente'],
                        'periodo' => $datos['anio'] . '-' . str_pad($datos['periodo'], 2, '0', STR_PAD_LEFT) . '-01',
                        'monto' => $datos['montoFonbes'],
                        'formaPago' => $datos['formaPago'],
                        'obs' => 'Reversión automática por error en pago COOPAM',
                        'obs2' => '',
                        'usuario' => $datos['usuario'] ?? 'SISTEMA',
                    ];
                    $devolucionServicio->ejecutarDevolucionFonbes($devolucionDatos, []);
                } catch (\Throwable $e) {
                    $logMessage = sprintf(
                        "[%s] ERROR REVERSION FONBES - Cliente: %s, Error: %s\n",
                        date('Y-m-d H:i:s'),
                        $datos['cliente'],
                        $e->getMessage()
                    );
                    file_put_contents($logFile, $logMessage, FILE_APPEND);
                }

                throw new RuntimeException('Error al procesar pago COOPAM: ' . ($resultadoCoopam['mensaje'] ?? 'Error desconocido') . '. Se ha revertido el pago FONBES.');
            }

            $codigoMovimientoCoopam = $resultadoCoopam['codigo_movimiento'];

            // 7. NO hacer commit aquí - cada servicio ya hizo su propio commit
            // $this->pdo->commit();

            // Registrar éxito
            $logMessage = sprintf(
                "[%s] EXITO - Movimiento FONBES: %d, Movimiento COOPAM: %d, Cliente: %s\n",
                date('Y-m-d H:i:s'),
                $codigoMovimientoFonbes,
                $codigoMovimientoCoopam,
                $datos['cliente']
            );
            file_put_contents($logFile, $logMessage, FILE_APPEND);

            return [
                'exito' => true,
                'codigo_movimiento_fonbes' => $codigoMovimientoFonbes,
                'codigo_movimiento_coopam' => $codigoMovimientoCoopam,
                'mensaje' => 'Pago combinado procesado correctamente',
            ];

        } catch (RuntimeException $e) {
            // Rollback ante error (solo si hay transacción activa)
            if ($this->pdo->inTransaction()) {
                $this->pdo->rollBack();
            }

            // Registrar error
            $logMessage = sprintf(
                "[%s] ERROR RUNTIME - Cliente: %s, Error: %s\n",
                date('Y-m-d H:i:s'),
                $datos['cliente'] ?? 'N/A',
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
     * Obtiene datos del socio para validar aportes
     */
    private function obtenerSocio(PDO $pdo, string $clienteCodigo): ?array
    {
        $sql = "SELECT p.F01_APORTACION, p.F01_APORTE_COPE
                FROM f01_persona p
                INNER JOIN f02_cliente c ON c.F02_ID_PERSONA = p.F01_ID_PERSONA
                WHERE CAST(c.F02_ID_CLIENTE AS UNSIGNED) = CAST(:cliente AS UNSIGNED)
                LIMIT 1";
        $stmt = $pdo->prepare($sql);
        $stmt->execute(['cliente' => trim($clienteCodigo)]);
        $result = $stmt->fetch();
        return $result ?: null;
    }
}
