<?php

namespace Sacs\Services;

use Sacs\Core\Database;

class PrestamosServicio
{
    private \PDO $db;

    public function __construct()
    {
        $this->db = Database::connection();
    }

    /**
     * Listar préstamos con filtros y paginación
     */
    public function listarPrestamos(array $filtros = [], int $pagina = 1, int $porPagina = 10): array
    {
        $offset = ($pagina - 1) * $porPagina;
        
        $sql = "SELECT 
                    f16_codigo as codigo,
                    f16_cliente as cliente,
                    f16_importe as importe,
                    f16_cuotas as cuotas,
                    f16_saldo as saldo,
                    f16_cuotasPend as cuotas_pendientes,
                    f16_gastosa as gastos_activos,
                    f16_gastosm as gastos_mora,
                    f16_otros as otros,
                    f16_otros2 as otros2,
                    f16_cuotaMe as cuota_mensual,
                    f16_desembolso as desembolso,
                    f16_fecha as fecha,
                    f16_fechaUP as fecha_actualizacion,
                    f16_fechaDes as fecha_desembolso,
                    f16_estado as estado,
                    f16_producto as producto,
                    f16_sobre as sobre,
                    f16_interes as interes,
                    f16_garante as garante,
                    f16_garante2 as garante2,
                    f16_garante3 as garante3,
                    f16_garante4 as garante4,
                    p.F01_APE_PAT as apellido_paterno,
                    p.F01_APE_MAT as apellido_materno,
                    p.F01_NOMB_PRI as nombre_primero,
                    p.F01_NOMB_SEG as nombre_segundo
                FROM f16_prestamo f16
                INNER JOIN f02_cliente c ON f16.f16_cliente = c.F02_ID_CLIENTE
                INNER JOIN f01_persona p ON c.F02_ID_PERSONA = p.F01_ID_PERSONA
                WHERE f16_cliente IS NOT NULL";
        
        $params = [];
        
        // Filtro por cliente (titular)
        if (!empty($filtros['cliente'])) {
            $sql .= " AND f16_cliente = :cliente";
            $params['cliente'] = $filtros['cliente'];
        }
        
        // Filtro por garante
        if (!empty($filtros['garante'])) {
            $sql .= " AND (f16_garante = :garante OR f16_garante2 = :garante OR f16_garante3 = :garante OR f16_garante4 = :garante)";
            $params['garante'] = $filtros['garante'];
        }
        
        // Filtro por apellido (titular)
        if (!empty($filtros['apellido_titular'])) {
            $sql .= " AND p.F01_APE_PAT LIKE :apellido_titular";
            $params['apellido_titular'] = '%' . $filtros['apellido_titular'] . '%';
        }

        // Filtro por estado
        if (!empty($filtros['estado']) && $filtros['estado'] !== '0') {
            $sql .= " AND f16_estado = :estado";
            $params['estado'] = $filtros['estado'];
        } elseif (isset($filtros['estado']) && $filtros['estado'] === '0') {
            $sql .= " AND f16_estado <> 0";
        }
        
        // Filtro por producto
        if (!empty($filtros['producto'])) {
            $sql .= " AND f16_producto = :producto";
            $params['producto'] = $filtros['producto'];
        }
        
        // Filtro por fecha desde
        if (!empty($filtros['fecha_desde'])) {
            $sql .= " AND f16_fechaDes >= :fecha_desde";
            $params['fecha_desde'] = $filtros['fecha_desde'];
        }
        
        // Filtro por fecha hasta
        if (!empty($filtros['fecha_hasta'])) {
            $sql .= " AND f16_fechaDes <= :fecha_hasta";
            $params['fecha_hasta'] = $filtros['fecha_hasta'];
        }

        // Si se filtra por cliente, ordenar por estado en orden específico
        if (!empty($filtros['cliente'])) {
            $sql .= " ORDER BY
                CASE f16_estado
                    WHEN 4 THEN 1  -- Activo
                    WHEN 2 THEN 2  -- Aprobado
                    WHEN 1 THEN 3  -- Pendiente
                    WHEN 3 THEN 4  -- Rechazado
                    WHEN 5 THEN 5  -- Cancelado
                    WHEN 0 THEN 6  -- Anulado
                    ELSE 7
                END,
                f16_fechaDes DESC,
                f16_codigo DESC
                LIMIT :limit OFFSET :offset";
        } else {
            $sql .= " ORDER BY f16_fechaDes DESC LIMIT :limit OFFSET :offset";
        }
        
        $stmt = $this->db->prepare($sql);
        foreach ($params as $key => $value) {
            $stmt->bindValue($key, $value);
        }
        $stmt->bindValue(':limit', $porPagina, \PDO::PARAM_INT);
        $stmt->bindValue(':offset', $offset, \PDO::PARAM_INT);
        $stmt->execute();
        
        return $stmt->fetchAll();
    }

    /**
     * Contar préstamos con filtros
     */
    public function contarPrestamos(array $filtros = []): int
    {
        $sql = "SELECT COUNT(*) as total
                FROM f16_prestamo f16
                INNER JOIN f02_cliente c ON f16.f16_cliente = c.F02_ID_CLIENTE
                INNER JOIN f01_persona p ON c.F02_ID_PERSONA = p.F01_ID_PERSONA
                WHERE f16_cliente IS NOT NULL";
        
        $params = [];
        
        if (!empty($filtros['cliente'])) {
            $sql .= " AND f16_cliente = :cliente";
            $params['cliente'] = $filtros['cliente'];
        }
        
        if (!empty($filtros['garante'])) {
            $sql .= " AND (f16_garante = :garante OR f16_garante2 = :garante OR f16_garante3 = :garante OR f16_garante4 = :garante)";
            $params['garante'] = $filtros['garante'];
        }
        
        if (!empty($filtros['apellido_titular'])) {
            $sql .= " AND p.F01_APE_PAT LIKE :apellido_titular";
            $params['apellido_titular'] = '%' . $filtros['apellido_titular'] . '%';
        }
        
        if (!empty($filtros['estado']) && $filtros['estado'] !== '0') {
            $sql .= " AND f16_estado = :estado";
            $params['estado'] = $filtros['estado'];
        } elseif (isset($filtros['estado']) && $filtros['estado'] === '0') {
            $sql .= " AND f16_estado <> 0";
        }
        
        if (!empty($filtros['producto'])) {
            $sql .= " AND f16_producto = :producto";
            $params['producto'] = $filtros['producto'];
        }
        
        if (!empty($filtros['fecha_desde'])) {
            $sql .= " AND f16_fechaDes >= :fecha_desde";
            $params['fecha_desde'] = $filtros['fecha_desde'];
        }
        
        if (!empty($filtros['fecha_hasta'])) {
            $sql .= " AND f16_fechaDes <= :fecha_hasta";
            $params['fecha_hasta'] = $filtros['fecha_hasta'];
        }
        
        $stmt = $this->db->prepare($sql);
        foreach ($params as $key => $value) {
            $stmt->bindValue($key, $value);
        }
        $stmt->execute();
        
        $result = $stmt->fetch();
        
        return (int) ($result['total'] ?? 0);
    }

    /**
     * Obtener préstamo específico
     */
    public function obtenerPrestamo(string $codigo): ?array
    {
        $sql = "SELECT 
                    f16_codigo as codigo,
                    f16_cliente as cliente,
                    f16_importe as importe,
                    f16_cuotas as cuotas,
                    f16_saldo as saldo,
                    f16_cuotasPend as cuotas_pendientes,
                    f16_gastosa as gastos_activos,
                    f16_gastosm as gastos_mora,
                    f16_otros as otros,
                    f16_otros2 as otros2,
                    f16_cuotaMe as cuota_mensual,
                    f16_desembolso as desembolso,
                    f16_fecha as fecha,
                    f16_fechaUP as fecha_actualizacion,
                    f16_fechaDes as fecha_desembolso,
                    f16_estado as estado,
                    f16_producto as producto,
                    f16_sobre as sobre,
                    f16_interes as interes,
                    f16_garante as garante,
                    f16_garante2 as garante2,
                    f16_garante3 as garante3,
                    f16_garante4 as garante4,
                    f16_tramite as tramite,
                    p.F01_APE_PAT as apellido_paterno,
                    p.F01_APE_MAT as apellido_materno,
                    p.F01_NOMB_PRI as nombre_primero,
                    p.F01_NOMB_SEG as nombre_segundo
                FROM f16_prestamo f16
                INNER JOIN f02_cliente c ON f16.f16_cliente = c.F02_ID_CLIENTE
                INNER JOIN f01_persona p ON c.F02_ID_PERSONA = p.F01_ID_PERSONA
                WHERE f16_codigo = :codigo";
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute(['codigo' => $codigo]);
        
        $result = $stmt->fetch();
        
        return $result ?: null;
    }

    /**
     * Obtener préstamos de un cliente
     */
    public function obtenerPrestamosPorCliente(string $clienteCodigo): array
    {
        $sql = "SELECT 
                    f16_codigo as codigo,
                    f16_importe as importe,
                    f16_cuotas as cuotas,
                    f16_saldo as saldo,
                    f16_cuotasPend as cuotas_pendientes,
                    f16_estado as estado,
                    f16_fechaDes as fecha_desembolso,
                    f16_producto as producto
                FROM f16_prestamo
                WHERE f16_cliente = :cliente
                ORDER BY f16_fechaDes DESC";
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute(['cliente' => $clienteCodigo]);
        
        return $stmt->fetchAll();
    }

    /**
     * Obtener préstamos donde el cliente es garante
     */
    public function obtenerPrestamosPorGarante(string $clienteCodigo): array
    {
        $sql = "SELECT 
                    f16_codigo as codigo,
                    f16_importe as importe,
                    f16_cuotas as cuotas,
                    f16_saldo as saldo,
                    f16_cuotasPend as cuotas_pendientes,
                    f16_estado as estado,
                    f16_fechaDes as fecha_desembolso,
                    f16_producto as producto
                FROM f16_prestamo
                WHERE f16_garante = :cliente
                   OR f16_garante2 = :cliente
                   OR f16_garante3 = :cliente
                   OR f16_garante4 = :cliente
                ORDER BY f16_fechaDes DESC";
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute(['cliente' => $clienteCodigo]);
        
        return $stmt->fetchAll();
    }

    /**
     * Obtener estados de préstamos
     */
    public function obtenerEstados(): array
    {
        $sql = "SELECT DISTINCT f16_estado as estado, 
                    CASE f16_estado
                        WHEN 0 THEN 'Eliminado'
                        WHEN 1 THEN 'Pendiente'
                        WHEN 2 THEN 'Aprobado'
                        WHEN 3 THEN 'Rechazado'
                        WHEN 4 THEN 'Activo'
                        WHEN 5 THEN 'Cancelado'
                        ELSE 'Otro'
                    END as estado_nombre
                FROM f16_prestamo
                ORDER BY f16_estado";
        
        $stmt = $this->db->query($sql);
        
        return $stmt->fetchAll();
    }

    /**
     * Obtener productos de préstamos
     */
    public function obtenerProductos(): array
    {
        $sql = "SELECT DISTINCT f16_producto as producto
                FROM f16_prestamo
                WHERE f16_producto IS NOT NULL
                ORDER BY f16_producto";
        
        $stmt = $this->db->query($sql);
        
        return $stmt->fetchAll();
    }

    /**
     * Validar datos del préstamo
     */
    public function validarPrestamo(array $datos): array
    {
        $errores = [];
        
        if (empty($datos['cliente'])) {
            $errores[] = 'El cliente es requerido';
        }
        
        if (empty($datos['importe']) || $datos['importe'] <= 0) {
            $errores[] = 'El importe debe ser mayor a 0';
        }
        
        if (empty($datos['cuotas']) || $datos['cuotas'] <= 0) {
            $errores[] = 'El número de cuotas debe ser mayor a 0';
        }
        
        if (empty($datos['interes']) || $datos['interes'] < 0) {
            $errores[] = 'La tasa de interés debe ser mayor o igual a 0';
        }
        
        if (empty($datos['fecha_desembolso'])) {
            $errores[] = 'La fecha de desembolso es requerida';
        }
        
        return $errores;
    }

    /**
     * Calcular cuota mensual
     */
    private function calcularCuotaMensual(float $importe, int $cuotas, float $interes): float
    {
        $cuotaCapital = $importe / $cuotas;
        $cuotaInteres = $importe * ($interes / 100);
        
        return $cuotaCapital + $cuotaInteres;
    }

    /**
     * Calcular cronograma de cuotas
     */
    public function calcularCronograma(float $importe, int $cuotas, float $interes, string $fechaInicio): array
    {
        $cronograma = [];
        $cuotaMensual = $this->calcularCuotaMensual($importe, $cuotas, $interes);
        $capitalPorCuota = $importe / $cuotas;
        $saldoPendiente = $importe;

        $fecha = new \DateTime($fechaInicio);

        for ($i = 1; $i <= $cuotas; $i++) {
            $saldoPendiente -= $capitalPorCuota;

            $cronograma[] = [
                'numero' => $i,
                'fecha' => $fecha->format('Y-m-d'),
                'capital' => $capitalPorCuota,
                'interes' => $importe * ($interes / 100),
                'total' => $cuotaMensual,
                'saldo' => max(0, $saldoPendiente), // Saldo decreciente según capital pendiente
                'estado' => 'pendiente'
            ];

            $fecha->modify('+1 month');
        }

        return $cronograma;
    }

    /**
     * Generar cronograma en base de datos
     */
    public function generarCronograma(string $codigoPrestamo, array $cronograma, string $usuario): array
    {
        try {
            $sql = "INSERT INTO f17_cuotasprestamo (
                        f17_prestamo,
                        f17_ncuota,
                        f17_fecha,
                        f17_capital,
                        f17_interes,
                        f17_total,
                        f17_saldo,
                        f17_cta,
                        f17_estado,
                        f17_tramite
                    ) VALUES (
                        :prestamo,
                        :ncuota,
                        :fecha,
                        :capital,
                        :interes,
                        :total,
                        :saldo,
                        :cta,
                        :estado,
                        :tramite
                    )";
            
            $stmt = $this->db->prepare($sql);
            
            foreach ($cronograma as $cuota) {
                $stmt->execute([
                    'prestamo' => $codigoPrestamo,
                    'ncuota' => $cuota['numero'],
                    'fecha' => $cuota['fecha'],
                    'capital' => $cuota['capital'],
                    'interes' => $cuota['interes'],
                    'total' => $cuota['total'],
                    'saldo' => $cuota['saldo'], // Usar el saldo calculado (decreciente)
                    'cta' => 0,
                    'estado' => 'VIGENTE',
                    'tramite' => ''
                ]);
            }
            
            return ['success' => true, 'message' => 'Cronograma generado correctamente'];
            
        } catch (Exception $e) {
            return ['success' => false, 'message' => 'Error al generar cronograma: ' . $e->getMessage()];
        }
    }

    /**
     * Registrar nuevo préstamo
     */
    public function registrarPrestamo(array $datos, string $usuario): array
    {
        try {
            // Validar datos
            $errores = $this->validarPrestamo($datos);
            if (!empty($errores)) {
                return ['success' => false, 'message' => 'Errores de validación: ' . implode(', ', $errores)];
            }
            
            $this->db->beginTransaction();
            
            // Generar código de préstamo
            $codigoPrestamo = $this->generarCodigoPrestamo();
            
            // Calcular cuota mensual
            $cuotaMensual = $this->calcularCuotaMensual(
                (float) $datos['importe'],
                (int) $datos['cuotas'],
                (float) $datos['interes']
            );
            
            // Insertar préstamo
            $sql = "INSERT INTO f16_prestamo (
                        f16_codigo,
                        f16_cliente,
                        f16_importe,
                        f16_cuotas,
                        f16_saldo,
                        f16_cuotasPend,
                        f16_cuotaMe,
                        f16_desembolso,
                        f16_fecha,
                        f16_fechaUP,
                        f16_fechaDes,
                        f16_estado,
                        f16_producto,
                        f16_interes,
                        f16_garante,
                        f16_garante2,
                        f16_garante3,
                        f16_garante4,
                        f16_tramite,
                        f16_usuarioCrea
                    ) VALUES (
                        :codigo,
                        :cliente,
                        :importe,
                        :cuotas,
                        :saldo,
                        :cuotas_pendientes,
                        :cuota_mensual,
                        :desembolso,
                        :fecha,
                        :fecha_actualizacion,
                        :fecha_desembolso,
                        :estado,
                        :producto,
                        :interes,
                        :garante,
                        :garante2,
                        :garante3,
                        :garante4,
                        :tramite,
                        :usuario
                    )";
            
            $stmt = $this->db->prepare($sql);
            $stmt->execute([
                'codigo' => $codigoPrestamo,
                'cliente' => $datos['cliente'],
                'importe' => $datos['importe'],
                'cuotas' => $datos['cuotas'],
                'saldo' => $datos['importe'], // Inicialmente el saldo es el importe total
                'cuotas_pendientes' => $datos['cuotas'],
                'cuota_mensual' => $cuotaMensual,
                'desembolso' => 0, // Se actualiza cuando se desembolsa
                'fecha' => $datos['fecha_desembolso'],
                'fecha_actualizacion' => date('Y-m-d'),
                'fecha_desembolso' => $datos['fecha_desembolso'],
                'estado' => 1, // Estado 1: Pendiente de aprobación
                'producto' => $datos['producto'] ?? '',
                'interes' => $datos['interes'],
                'garante' => $datos['garante'] ?? '',
                'garante2' => $datos['garante2'] ?? '',
                'garante3' => $datos['garante3'] ?? '',
                'garante4' => $datos['garante4'] ?? '',
                'tramite' => $datos['tramite'] ?? '',
                'usuario' => $usuario
            ]);
            
            // Generar cronograma de cuotas
            $cronograma = $this->calcularCronograma(
                (float) $datos['importe'],
                (int) $datos['cuotas'],
                (float) $datos['interes'],
                $datos['fecha_desembolso']
            );
            
            $cronogramaResultado = $this->generarCronograma($codigoPrestamo, $cronograma, $usuario);
            
            if (!$cronogramaResultado['success']) {
                throw new \Exception($cronogramaResultado['message']);
            }
            
            $this->db->commit();

            return [
                'success' => true,
                'message' => 'Préstamo registrado correctamente',
                'codigo' => $codigoPrestamo,
                'cliente' => $datos['cliente']
            ];
            
        } catch (Exception $e) {
            $this->db->rollBack();
            return ['success' => false, 'message' => 'Error al registrar préstamo: ' . $e->getMessage()];
        }
    }

    /**
     * Generar código de préstamo
     */
    private function generarCodigoPrestamo(): string
    {
        $sql = "SELECT LPAD(MAX(f16_codigo) + 1, 8, '0') as codigo
                FROM f16_prestamo
                FOR UPDATE";
        
        $stmt = $this->db->query($sql);
        $result = $stmt->fetch();
        
        if ($result && $result['codigo']) {
            return $result['codigo'];
        }
        
        return '00000001';
    }

    /**
     * Actualizar préstamo
     */
    public function actualizarPrestamo(string $codigo, array $datos, string $usuario): array
    {
        try {
            // Validar datos
            $errores = $this->validarPrestamo($datos);
            if (!empty($errores)) {
                return ['success' => false, 'message' => 'Errores de validación: ' . implode(', ', $errores)];
            }
            
            $this->db->beginTransaction();
            
            // Calcular cuota mensual
            $cuotaMensual = $this->calcularCuotaMensual(
                (float) $datos['importe'],
                (int) $datos['cuotas'],
                (float) $datos['interes']
            );
            
            // Actualizar préstamo
            $sql = "UPDATE f16_prestamo SET
                        f16_importe = :importe,
                        f16_cuotas = :cuotas,
                        f16_cuotaMe = :cuota_mensual,
                        f16_fechaUP = :fecha_actualizacion,
                        f16_fechaDes = :fecha_desembolso,
                        f16_producto = :producto,
                        f16_interes = :interes,
                        f16_garante = :garante,
                        f16_garante2 = :garante2,
                        f16_garante3 = :garante3,
                        f16_garante4 = :garante4,
                        f16_tramite = :tramite
                    WHERE f16_codigo = :codigo";
            
            $stmt = $this->db->prepare($sql);
            $stmt->execute([
                'codigo' => $codigo,
                'importe' => $datos['importe'],
                'cuotas' => $datos['cuotas'],
                'cuota_mensual' => $cuotaMensual,
                'fecha_actualizacion' => date('Y-m-d'),
                'fecha_desembolso' => $datos['fecha_desembolso'],
                'producto' => $datos['producto'] ?? '',
                'interes' => $datos['interes'],
                'garante' => $datos['garante'] ?? '',
                'garante2' => $datos['garante2'] ?? '',
                'garante3' => $datos['garante3'] ?? '',
                'garante4' => $datos['garante4'] ?? '',
                'tramite' => $datos['tramite'] ?? ''
            ]);
            
            $this->db->commit();
            
            return [
                'success' => true,
                'message' => 'Préstamo actualizado correctamente'
            ];
            
        } catch (Exception $e) {
            $this->db->rollBack();
            return ['success' => false, 'message' => 'Error al actualizar préstamo: ' . $e->getMessage()];
        }
    }

    /**
     * Regenerar cronograma
     */
    public function regenerarCronograma(string $codigoPrestamo, array $datos, string $usuario): array
    {
        try {
            $this->db->beginTransaction();
            
            // Eliminar cronograma existente
            $sql = "DELETE FROM f17_cuotasprestamo WHERE f17_prestamo = :prestamo";
            $stmt = $this->db->prepare($sql);
            $stmt->execute(['prestamo' => $codigoPrestamo]);
            
            // Calcular nuevo cronograma
            $cronograma = $this->calcularCronograma(
                (float) $datos['importe'],
                (int) $datos['cuotas'],
                (float) $datos['interes'],
                $datos['fecha_desembolso']
            );
            
            // Generar nuevo cronograma
            $resultado = $this->generarCronograma($codigoPrestamo, $cronograma, $usuario);
            
            if (!$resultado['success']) {
                throw new \Exception($resultado['message']);
            }
            
            // Actualizar cuotas pendientes en préstamo
            $sql = "UPDATE f16_prestamo SET
                        f16_cuotasPend = :cuotas_pendientes,
                        f16_saldo = :saldo,
                        f16_cuotaMe = :cuota_mensual
                    WHERE f16_codigo = :codigo";
            
            $stmt = $this->db->prepare($sql);
            $stmt->execute([
                'codigo' => $codigoPrestamo,
                'cuotas_pendientes' => $datos['cuotas'],
                'saldo' => $datos['importe'],
                'cuota_mensual' => $this->calcularCuotaMensual(
                    (float) $datos['importe'],
                    (int) $datos['cuotas'],
                    (float) $datos['interes']
                )
            ]);
            
            $this->db->commit();
            
            return [
                'success' => true,
                'message' => 'Cronograma regenerado correctamente'
            ];
            
        } catch (Exception $e) {
            $this->db->rollBack();
            return ['success' => false, 'message' => 'Error al regenerar cronograma: ' . $e->getMessage()];
        }
    }

    /**
     * Regenerar cronograma de un préstamo existente (usando sus datos actuales)
     */
    public function regenerarCronogramaExistente(string $codigoPrestamo, string $usuario): array
    {
        try {
            $this->db->beginTransaction();

            // Obtener datos actuales del préstamo
            $sql = "SELECT f16_importe, f16_cuotas, f16_interes, f16_fechaDes
                    FROM f16_prestamo
                    WHERE f16_codigo = :codigo";
            $stmt = $this->db->prepare($sql);
            $stmt->execute(['codigo' => $codigoPrestamo]);
            $prestamo = $stmt->fetch();

            if (!$prestamo) {
                throw new \Exception('Préstamo no encontrado');
            }

            // Eliminar cronograma existente
            $sql = "DELETE FROM f17_cuotasprestamo WHERE f17_prestamo = :prestamo";
            $stmt = $this->db->prepare($sql);
            $stmt->execute(['prestamo' => $codigoPrestamo]);

            // Calcular nuevo cronograma con saldo decreciente
            $cronograma = $this->calcularCronograma(
                (float) $prestamo['f16_importe'],
                (int) $prestamo['f16_cuotas'],
                (float) $prestamo['f16_interes'],
                $prestamo['f16_fechaDes']
            );

            // Generar nuevo cronograma
            $resultado = $this->generarCronograma($codigoPrestamo, $cronograma, $usuario);

            if (!$resultado['success']) {
                throw new \Exception($resultado['message']);
            }

            $this->db->commit();

            return [
                'success' => true,
                'message' => 'Cronograma regenerado correctamente con saldo decreciente'
            ];

        } catch (Exception $e) {
            $this->db->rollBack();
            return ['success' => false, 'message' => 'Error al regenerar cronograma: ' . $e->getMessage()];
        }
    }

    /**
     * Aprobar préstamo
     */
    public function aprobarPrestamo(string $codigo, string $usuario): array
    {
        try {
            $this->db->beginTransaction();
            
            $sql = "UPDATE f16_prestamo SET
                        f16_estado = 2,
                        f16_fechaUP = :fecha_actualizacion
                    WHERE f16_codigo = :codigo";
            
            $stmt = $this->db->prepare($sql);
            $stmt->execute([
                'codigo' => $codigo,
                'fecha_actualizacion' => date('Y-m-d')
            ]);
            
            $this->db->commit();
            
            return [
                'success' => true,
                'message' => 'Préstamo aprobado correctamente'
            ];
            
        } catch (Exception $e) {
            $this->db->rollBack();
            return ['success' => false, 'message' => 'Error al aprobar préstamo: ' . $e->getMessage()];
        }
    }

    /**
     * Rechazar préstamo
     */
    public function rechazarPrestamo(string $codigo, string $motivo, string $usuario): array
    {
        try {
            $this->db->beginTransaction();
            
            $sql = "UPDATE f16_prestamo SET
                        f16_estado = 3,
                        f16_fechaUP = :fecha_actualizacion,
                        f16_obs = :motivo
                    WHERE f16_codigo = :codigo";
            
            $stmt = $this->db->prepare($sql);
            $stmt->execute([
                'codigo' => $codigo,
                'fecha_actualizacion' => date('Y-m-d'),
                'motivo' => $motivo
            ]);
            
            $this->db->commit();
            
            return [
                'success' => true,
                'message' => 'Préstamo rechazado correctamente'
            ];
            
        } catch (Exception $e) {
            $this->db->rollBack();
            return ['success' => false, 'message' => 'Error al rechazar préstamo: ' . $e->getMessage()];
        }
    }

    /**
     * Obtener préstamos pendientes de aprobación
     */
    public function obtenerPrestamosPendientesAprobacion(): array
    {
        $sql = "SELECT 
                    f16_codigo as codigo,
                    f16_cliente as cliente,
                    f16_importe as importe,
                    f16_cuotas as cuotas,
                    f16_fechaDes as fecha_desembolso,
                    f16_producto as producto,
                    p.F01_APE_PAT as apellido_paterno,
                    p.F01_APE_MAT as apellido_materno,
                    p.F01_NOMB_PRI as nombre_primero,
                    p.F01_NOMB_SEG as nombre_segundo
                FROM f16_prestamo f16
                INNER JOIN f02_cliente c ON f16.f16_cliente = c.F02_ID_CLIENTE
                INNER JOIN f01_persona p ON c.F02_ID_PERSONA = p.F01_ID_PERSONA
                WHERE f16_estado = 1
                ORDER BY f16_fechaDes DESC";
        
        $stmt = $this->db->query($sql);
        
        return $stmt->fetchAll();
    }

    /**
     * Cancelar préstamo
     */
    public function cancelarPrestamo(string $codigo, string $motivo, string $usuario): array
    {
        try {
            $this->db->beginTransaction();
            
            $sql = "UPDATE f16_prestamo SET
                        f16_estado = 5,
                        f16_fechaUP = :fecha_actualizacion,
                        f16_obs = :motivo
                    WHERE f16_codigo = :codigo";
            
            $stmt = $this->db->prepare($sql);
            $stmt->execute([
                'codigo' => $codigo,
                'fecha_actualizacion' => date('Y-m-d'),
                'motivo' => $motivo
            ]);
            
            $this->db->commit();
            
            return [
                'success' => true,
                'message' => 'Préstamo cancelado correctamente'
            ];
            
        } catch (Exception $e) {
            $this->db->rollBack();
            return ['success' => false, 'message' => 'Error al cancelar préstamo: ' . $e->getMessage()];
        }
    }

    /**
     * Anular préstamo
     */
    public function anularPrestamo(string $codigo, string $motivo, string $usuario): array
    {
        try {
            $this->db->beginTransaction();
            
            $sql = "UPDATE f16_prestamo SET
                        f16_estado = 0,
                        f16_fechaUP = :fecha_actualizacion,
                        f16_obs = :motivo
                    WHERE f16_codigo = :codigo";
            
            $stmt = $this->db->prepare($sql);
            $stmt->execute([
                'codigo' => $codigo,
                'fecha_actualizacion' => date('Y-m-d'),
                'motivo' => $motivo
            ]);
            
            $this->db->commit();
            
            return [
                'success' => true,
                'message' => 'Préstamo anulado correctamente'
            ];
            
        } catch (Exception $e) {
            $this->db->rollBack();
            return ['success' => false, 'message' => 'Error al anular préstamo: ' . $e->getMessage()];
        }
    }

    /**
     * Activar préstamo
     */
    public function activarPrestamo(string $codigo, string $usuario): array
    {
        try {
            $this->db->beginTransaction();
            
            $sql = "UPDATE f16_prestamo SET
                        f16_estado = 4,
                        f16_fechaUP = :fecha_actualizacion
                    WHERE f16_codigo = :codigo";
            
            $stmt = $this->db->prepare($sql);
            $stmt->execute([
                'codigo' => $codigo,
                'fecha_actualizacion' => date('Y-m-d')
            ]);
            
            $this->db->commit();
            
            return [
                'success' => true,
                'message' => 'Préstamo activado correctamente'
            ];
            
        } catch (Exception $e) {
            $this->db->rollBack();
            return ['success' => false, 'message' => 'Error al activar préstamo: ' . $e->getMessage()];
        }
    }

    /**
     * Obtener cronograma de cuotas
     */
    public function obtenerCronograma(string $codigoPrestamo): array
    {
        $sql = "SELECT 
                    f17_ncuota as numero,
                    f17_fecha as fecha,
                    f17_capital as capital,
                    f17_interes as interes,
                    f17_total as total,
                    f17_saldo as saldo,
                    f17_estado as estado
                FROM f17_cuotasprestamo
                WHERE f17_prestamo = :prestamo
                ORDER BY f17_ncuota ASC";
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute(['prestamo' => $codigoPrestamo]);
        
        return $stmt->fetchAll();
    }

    /**
     * Contar cuotas
     */
    public function contarCuotas(string $codigoPrestamo): int
    {
        $sql = "SELECT COUNT(*) as total
                FROM f17_cuotasprestamo
                WHERE f17_prestamo = :prestamo";
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute(['prestamo' => $codigoPrestamo]);
        
        $result = $stmt->fetch();
        
        return (int) ($result['total'] ?? 0);
    }

    /**
     * Obtener cuotas pagadas
     */
    public function obtenerCuotasPagadas(string $codigoPrestamo): array
    {
        $sql = "SELECT 
                    f17_ncuota as numero,
                    f17_fecha as fecha,
                    f17_capital as capital,
                    f17_interes as interes,
                    f17_total as total,
                    f17_saldo as saldo,
                    f17_estado as estado
                FROM f17_cuotasprestamo
                WHERE f17_prestamo = :prestamo
                  AND f17_estado = 'PAGADO'
                ORDER BY f17_ncuota ASC";
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute(['prestamo' => $codigoPrestamo]);
        
        return $stmt->fetchAll();
    }

    /**
     * Obtener cuotas pendientes
     */
    public function obtenerCuotasPendientes(string $codigoPrestamo): array
    {
        $sql = "SELECT 
                    f17_ncuota as numero,
                    f17_fecha as fecha,
                    f17_capital as capital,
                    f17_interes as interes,
                    f17_total as total,
                    f17_saldo as saldo,
                    f17_estado as estado
                FROM f17_cuotasprestamo
                WHERE f17_prestamo = :prestamo
                  AND f17_estado = 'VIGENTE'
                ORDER BY f17_ncuota ASC";
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute(['prestamo' => $codigoPrestamo]);
        
        return $stmt->fetchAll();
    }

    /**
     * Obtener cuotas vencidas
     */
    public function obtenerCuotasVencidas(string $codigoPrestamo): array
    {
        $sql = "SELECT 
                    f17_ncuota as numero,
                    f17_fecha as fecha,
                    f17_capital as capital,
                    f17_interes as interes,
                    f17_total as total,
                    f17_saldo as saldo,
                    f17_estado as estado
                FROM f17_cuotasprestamo
                WHERE f17_prestamo = :prestamo
                  AND f17_estado = 'VIGENTE'
                  AND f17_fecha < CURDATE()
                ORDER BY f17_ncuota ASC";
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute(['prestamo' => $codigoPrestamo]);
        
        return $stmt->fetchAll();
    }

    /**
     * Obtener resumen completo del préstamo (optimizado)
     */
    public function obtenerResumenPrestamo(string $codigoPrestamo): ?array
    {
        $sql = "SELECT 
                    f16_codigo as codigo,
                    f16_cliente as cliente,
                    f16_importe as importe,
                    f16_cuotas as cuotas,
                    f16_saldo as saldo,
                    f16_cuotasPend as cuotas_pendientes,
                    f16_gastosa as gastos_activos,
                    f16_gastosm as gastos_mora,
                    f16_otros as otros,
                    f16_otros2 as otros2,
                    f16_cuotaMe as cuota_mensual,
                    f16_desembolso as desembolso,
                    f16_fecha as fecha,
                    f16_fechaUP as fecha_actualizacion,
                    f16_fechaDes as fecha_desembolso,
                    f16_estado as estado,
                    f16_producto as producto,
                    f16_sobre as sobre,
                    f16_interes as interes,
                    f16_garante as garante,
                    f16_garante2 as garante2,
                    f16_garante3 as garante3,
                    f16_garante4 as garante4,
                    f16_tramite as tramite,
                    f16_obs as observaciones,
                    p.F01_APE_PAT as apellido_paterno,
                    p.F01_APE_MAT as apellido_materno,
                    p.F01_NOMB_PRI as nombre_primero,
                    p.F01_NOMB_SEG as nombre_segundo,
                    p.F01_CTA_CORREO as correo,
                    p.F01_TELF_FIJO as telefono_fijo,
                    p.F01_TELF_CEL as telefono_celular,
                    (SELECT COUNT(*) FROM f17_cuotasprestamo WHERE f17_prestamo = f16_codigo AND f17_estado = 'PAGADO') as cuotas_pagadas,
                    (SELECT COUNT(*) FROM f17_cuotasprestamo WHERE f17_prestamo = f16_codigo AND f17_estado = 'VIGENTE') as cuotas_pendientes
                FROM f16_prestamo f16
                INNER JOIN f02_cliente c ON f16.f16_cliente = c.F02_ID_CLIENTE
                INNER JOIN f01_persona p ON c.F02_ID_PERSONA = p.F01_ID_PERSONA
                WHERE f16_codigo = :codigo";
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute(['codigo' => $codigoPrestamo]);
        
        $result = $stmt->fetch();
        
        return $result ?: null;
    }

    /**
     * Obtener movimientos del préstamo
     */
    public function obtenerMovimientosPrestamo(string $codigoPrestamo): array
    {
        $sql = "SELECT 
                    f15_codigo as codigo,
                    f15_fecha as fecha,
                    f15_operacion as operacion,
                    f15_maprestamo as monto_prestamo,
                    f15_gastosa as gastos_activos,
                    f15_gastosm as gastos_mora,
                    f15_gastos1 as otros1,
                    f15_gastos2 as otros2,
                    f15_obs as observaciones,
                    f15_usuaCrea as usuario_crea
                FROM f15_movimiento
                WHERE f15_prestamo = :prestamo
                ORDER BY f15_fecha DESC
                LIMIT 20";
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute(['prestamo' => $codigoPrestamo]);
        
        return $stmt->fetchAll();
    }

    /**
     * Obtener detalle completo del préstamo con cronograma y movimientos
     */
    public function obtenerDetalleCompletoPrestamo(string $codigoPrestamo): ?array
    {
        // 1. Obtener información completa del préstamo
        $prestamo = $this->obtenerResumenPrestamo($codigoPrestamo);
        if (!$prestamo) {
            return null;
        }

        // 2. Obtener cronograma completo de cuotas
        $sqlCronograma = "SELECT
                    f17_ncuota as numero,
                    f17_fecha as fecha,
                    f17_capital as capital,
                    f17_interes as interes,
                    f17_total as total,
                    f17_saldo as saldo,
                    f17_cta as cta,
                    f17_totalA as total_acumulado,
                    f17_estado as estado,
                    f17_tramite as tramite
                FROM f17_cuotasprestamo
                WHERE f17_prestamo = :prestamo
                ORDER BY f17_ncuota ASC";

        $stmtCronograma = $this->db->prepare($sqlCronograma);
        $stmtCronograma->execute(['prestamo' => $codigoPrestamo]);
        $cronograma = $stmtCronograma->fetchAll();

        // 3. Obtener todos los movimientos del préstamo (sin límite)
        $sqlMovimientos = "SELECT
                    f15_codigo as codigo,
                    f15_fecha as fecha,
                    f15_operacion as operacion,
                    f15_maprestamo as amortizacion,
                    f15_mdesembolso as desembolso,
                    f15_gastosa as gastos_administrativos,
                    f15_gastosm as gastos_mora,
                    f15_gastos1 as otros1,
                    f15_gastos2 as otros2,
                    f15_obs as observaciones,
                    f15_obs2 as observaciones2,
                    f15_formaPago as forma_pago,
                    f15_usuaCrea as usuario_crea,
                    f15_fechaCrea as fecha_crea
                FROM f15_movimiento
                WHERE f15_prestamo = :prestamo
                ORDER BY f15_fecha DESC";

        $stmtMovimientos = $this->db->prepare($sqlMovimientos);
        $stmtMovimientos->execute(['prestamo' => $codigoPrestamo]);
        $movimientos = $stmtMovimientos->fetchAll();

        // 4. Retornar estructura completa
        return [
            'prestamo' => $prestamo,
            'cronograma' => $cronograma,
            'movimientos' => $movimientos,
        ];
    }

    /**
     * Reporte general de préstamos
     */
    public function reportePrestamosGeneral(array $filtros = [], int $pagina = 1, int $porPagina = 10): array
    {
        $where = ['1=1'];
        $params = [];
        
        if (isset($filtros['estado']) && $filtros['estado'] !== '') {
            $where[] = 'f16_estado = :estado';
            $params['estado'] = $filtros['estado'];
        }
        
        if (!empty($filtros['producto'])) {
            $where[] = 'f16_producto = :producto';
            $params['producto'] = $filtros['producto'];
        }
        
        if (!empty($filtros['fecha_inicio'])) {
            $where[] = 'f16_fechaDes >= :fecha_inicio';
            $params['fecha_inicio'] = $filtros['fecha_inicio'];
        }
        
        if (!empty($filtros['fecha_fin'])) {
            $where[] = 'f16_fechaDes <= :fecha_fin';
            $params['fecha_fin'] = $filtros['fecha_fin'];
        }
        
        $offset = ($pagina - 1) * $porPagina;
        
        $sql = "SELECT 
                    f16_codigo as codigo,
                    f16_cliente as cliente,
                    f16_importe as importe,
                    f16_saldo as saldo,
                    f16_cuotas as cuotas,
                    f16_cuotasPend as cuotas_pendientes,
                    f16_fechaDes as fecha_desembolso,
                    f16_estado as estado,
                    f16_producto as producto,
                    p.F01_APE_PAT as apellido_paterno,
                    p.F01_APE_MAT as apellido_materno,
                    p.F01_NOMB_PRI as nombre_primero
                FROM f16_prestamo f16
                INNER JOIN f02_cliente c ON f16.f16_cliente = c.F02_ID_CLIENTE
                INNER JOIN f01_persona p ON c.F02_ID_PERSONA = p.F01_ID_PERSONA
                WHERE " . implode(' AND ', $where) . "
                ORDER BY f16_fechaDes DESC
                LIMIT :limit OFFSET :offset";
        
        $stmt = $this->db->prepare($sql);
        foreach ($params as $key => $value) {
            $stmt->bindValue($key, $value);
        }
        $stmt->bindValue(':limit', $porPagina, \PDO::PARAM_INT);
        $stmt->bindValue(':offset', $offset, \PDO::PARAM_INT);
        $stmt->execute();
        
        return $stmt->fetchAll();
    }

    /**
     * Contar préstamos para reporte general
     */
    public function contarPrestamosReporte(array $filtros = []): int
    {
        $where = ['1=1'];
        $params = [];
        
        if (isset($filtros['estado']) && $filtros['estado'] !== '') {
            $where[] = 'f16_estado = :estado';
            $params['estado'] = $filtros['estado'];
        }
        
        if (!empty($filtros['producto'])) {
            $where[] = 'f16_producto = :producto';
            $params['producto'] = $filtros['producto'];
        }
        
        if (!empty($filtros['fecha_inicio'])) {
            $where[] = 'f16_fechaDes >= :fecha_inicio';
            $params['fecha_inicio'] = $filtros['fecha_inicio'];
        }
        
        if (!empty($filtros['fecha_fin'])) {
            $where[] = 'f16_fechaDes <= :fecha_fin';
            $params['fecha_fin'] = $filtros['fecha_fin'];
        }
        
        $sql = "SELECT COUNT(*) as total
                FROM f16_prestamo f16
                WHERE " . implode(' AND ', $where);
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        
        $result = $stmt->fetch();
        
        return (int) ($result['total'] ?? 0);
    }

    /**
     * Reporte consolidado de préstamos
     */
    public function reportePrestamosConsolidado(array $filtros = []): array
    {
        $where = ['1=1'];
        $params = [];
        
        if (!empty($filtros['fecha_inicio'])) {
            $where[] = 'f16_fechaDes >= :fecha_inicio';
            $params['fecha_inicio'] = $filtros['fecha_inicio'];
        }
        
        if (!empty($filtros['fecha_fin'])) {
            $where[] = 'f16_fechaDes <= :fecha_fin';
            $params['fecha_fin'] = $filtros['fecha_fin'];
        }
        
        $sql = "SELECT 
                    f16_estado as estado,
                    COUNT(*) as cantidad,
                    SUM(f16_importe) as total_importe,
                    SUM(f16_saldo) as total_saldo
                FROM f16_prestamo
                WHERE " . implode(' AND ', $where) . "
                GROUP BY f16_estado
                ORDER BY f16_estado";
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        
        return $stmt->fetchAll();
    }

    /**
     * Reporte de préstamos por cuota
     */
    public function reportePrestamosCuota(array $filtros = [], int $pagina = 1, int $porPagina = 10): array
    {
        $where = ['1=1'];
        $params = [];

        if (!empty($filtros['socio'])) {
            $where[] = 'c.F02_ID_CLIENTE = :socio';
            $params['socio'] = $filtros['socio'];
        }

        if (!empty($filtros['estado_cuota'])) {
            $where[] = 'f17_estado = :estado_cuota';
            $params['estado_cuota'] = $filtros['estado_cuota'];
        }

        if (!empty($filtros['fecha_inicio'])) {
            $where[] = 'f17_fecha >= :fecha_inicio';
            $params['fecha_inicio'] = $filtros['fecha_inicio'];
        }

        if (!empty($filtros['fecha_fin'])) {
            $where[] = 'f17_fecha <= :fecha_fin';
            $params['fecha_fin'] = $filtros['fecha_fin'];
        }
        
        $offset = ($pagina - 1) * $porPagina;
        
        $sql = "SELECT 
                    f17_prestamo as prestamo,
                    f17_ncuota as numero_cuota,
                    f17_fecha as fecha,
                    f17_capital as capital,
                    f17_interes as interes,
                    f17_total as total,
                    f17_saldo as saldo,
                    f17_estado as estado,
                    p.F01_APE_PAT as apellido_paterno,
                    p.F01_APE_MAT as apellido_materno,
                    p.F01_NOMB_PRI as nombre_primero
                FROM f17_cuotasprestamo f17
                INNER JOIN f16_prestamo f16 ON f17.f17_prestamo = f16.f16_codigo
                INNER JOIN f02_cliente c ON f16.f16_cliente = c.F02_ID_CLIENTE
                INNER JOIN f01_persona p ON c.F02_ID_PERSONA = p.F01_ID_PERSONA
                WHERE " . implode(' AND ', $where) . "
                ORDER BY f17_fecha DESC, f17_prestamo, f17_ncuota
                LIMIT :limit OFFSET :offset";
        
        $stmt = $this->db->prepare($sql);
        foreach ($params as $key => $value) {
            $stmt->bindValue($key, $value);
        }
        $stmt->bindValue(':limit', $porPagina, \PDO::PARAM_INT);
        $stmt->bindValue(':offset', $offset, \PDO::PARAM_INT);
        $stmt->execute();
        
        return $stmt->fetchAll();
    }

    /**
     * Contar cuotas para reporte
     */
    public function contarCuotasReporte(array $filtros = []): int
    {
        $cacheKey = 'reporte_cuota_count_' . md5(serialize($filtros));

        // Verificar caché por 5 minutos
        if (isset($_SESSION[$cacheKey]) && $_SESSION[$cacheKey]['time'] > time() - 300) {
            return $_SESSION[$cacheKey]['count'];
        }

        $where = ['1=1'];
        $params = [];

        if (!empty($filtros['socio'])) {
            $where[] = 'c.F02_ID_CLIENTE = :socio';
            $params['socio'] = $filtros['socio'];
        }

        if (!empty($filtros['estado_cuota'])) {
            $where[] = 'f17_estado = :estado_cuota';
            $params['estado_cuota'] = $filtros['estado_cuota'];
        }

        if (!empty($filtros['fecha_inicio'])) {
            $where[] = 'f17_fecha >= :fecha_inicio';
            $params['fecha_inicio'] = $filtros['fecha_inicio'];
        }

        if (!empty($filtros['fecha_fin'])) {
            $where[] = 'f17_fecha <= :fecha_fin';
            $params['fecha_fin'] = $filtros['fecha_fin'];
        }

        $sql = "SELECT COUNT(*) as total
                FROM f17_cuotasprestamo f17
                INNER JOIN f16_prestamo f16 ON f17.f17_prestamo = f16.f16_codigo
                INNER JOIN f02_cliente c ON f16.f16_cliente = c.F02_ID_CLIENTE
                WHERE " . implode(' AND ', $where);

        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);

        $result = $stmt->fetch();
        $count = (int) ($result['total'] ?? 0);

        // Guardar en caché
        $_SESSION[$cacheKey] = [
            'count' => $count,
            'time' => time()
        ];

        return $count;
    }
}
