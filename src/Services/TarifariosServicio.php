<?php

declare(strict_types=1);

namespace Sacs\Services;

use PDO;

/**
 * Servicio para gestión de tarifarios
 */
final class TarifariosServicio
{
    public function __construct(
        private readonly PDO $pdo
    ) {
    }

    /**
     * Listar tarifarios
     */
    public function listarTarifarios(): array
    {
        $sql = "SELECT 
                    f19_codigo as codigo,
                    f19_concepto as concepto,
                    f19_valor as valor,
                    f19_fechaDesde as fecha_desde,
                    f19_fechaHasta as fecha_hasta,
                    f19_estado as estado,
                    f19_producto as producto
                FROM f19_tarifario
                ORDER BY f19_codigo";
        
        $stmt = $this->pdo->query($sql);
        
        return $stmt->fetchAll();
    }

    /**
     * Obtener tarifario por código
     */
    public function obtenerTarifario(int $codigo): ?array
    {
        $sql = "SELECT 
                    f19_codigo as codigo,
                    f19_concepto as concepto,
                    f19_valor as valor,
                    f19_fechaDesde as fecha_desde,
                    f19_fechaHasta as fecha_hasta,
                    f19_estado as estado,
                    f19_producto as producto
                FROM f19_tarifario
                WHERE f19_codigo = :codigo";
        
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute(['codigo' => $codigo]);
        
        $result = $stmt->fetch();
        
        return $result ?: null;
    }

    /**
     * Obtener siguiente código de tarifario
     */
    public function obtenerSiguienteCodigo(): int
    {
        $sql = "SELECT MAX(f19_codigo) as max_codigo FROM f19_tarifario";
        
        $stmt = $this->pdo->query($sql);
        $result = $stmt->fetch();
        
        return ($result['max_codigo'] ?? 0) + 1;
    }

    /**
     * Obtener productos para dropdown
     */
    public function obtenerProductos(): array
    {
        $sql = "SELECT 
                    f31_codigo as codigo,
                    f31_nombre as nombre
                FROM f31_productos
                WHERE f31_estado = 'A'
                ORDER BY f31_codigo";
        
        $stmt = $this->pdo->query($sql);
        
        return $stmt->fetchAll();
    }

    /**
     * Obtener nombre del producto
     */
    public function obtenerNombreProducto(int $productoId): ?string
    {
        $sql = "SELECT f31_nombre 
                FROM f31_productos 
                WHERE f31_codigo = :id";
        
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute(['id' => $productoId]);
        
        $result = $stmt->fetch();
        
        return $result['f31_nombre'] ?? null;
    }

    /**
     * Registrar tarifario
     */
    public function registrarTarifario(array $datos): array
    {
        try {
            $this->pdo->beginTransaction();

            $sql = "INSERT INTO f19_tarifario (
                        f19_codigo,
                        f19_concepto,
                        f19_valor,
                        f19_fechaDesde,
                        f19_fechaHasta,
                        f19_estado,
                        f19_producto
                    ) VALUES (
                        :codigo,
                        :concepto,
                        :valor,
                        :fecha_desde,
                        :fecha_hasta,
                        'A',
                        :producto
                    )";

            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([
                'codigo' => $datos['codigo'],
                'concepto' => $datos['concepto'],
                'valor' => $datos['valor'],
                'fecha_desde' => $datos['fecha_desde'],
                'fecha_hasta' => $datos['fecha_hasta'],
                'producto' => $datos['producto'] ?? null
            ]);

            $this->pdo->commit();

            return [
                'success' => true,
                'message' => 'Tarifario registrado correctamente'
            ];
        } catch (\PDOException $e) {
            $this->pdo->rollBack();
            return [
                'success' => false,
                'message' => 'Error al registrar tarifario: ' . $e->getMessage()
            ];
        }
    }

    /**
     * Actualizar tarifario
     */
    public function actualizarTarifario(int $codigo, array $datos): array
    {
        try {
            $this->pdo->beginTransaction();

            $sql = "UPDATE f19_tarifario 
                    SET f19_concepto = :concepto,
                        f19_valor = :valor,
                        f19_fechaDesde = :fecha_desde,
                        f19_fechaHasta = :fecha_hasta,
                        f19_estado = :estado,
                        f19_producto = :producto
                    WHERE f19_codigo = :codigo";

            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([
                'concepto' => $datos['concepto'],
                'valor' => $datos['valor'],
                'fecha_desde' => $datos['fecha_desde'],
                'fecha_hasta' => $datos['fecha_hasta'],
                'estado' => $datos['estado'],
                'producto' => $datos['producto'],
                'codigo' => $codigo
            ]);

            $this->pdo->commit();

            return [
                'success' => true,
                'message' => 'Tarifario actualizado correctamente'
            ];
        } catch (\PDOException $e) {
            $this->pdo->rollBack();
            return [
                'success' => false,
                'message' => 'Error al actualizar tarifario: ' . $e->getMessage()
            ];
        }
    }

    /**
     * Eliminar tarifario
     */
    public function eliminarTarifario(int $codigo): array
    {
        try {
            $this->pdo->beginTransaction();

            $sql = "DELETE FROM f19_tarifario WHERE f19_codigo = :codigo";

            $stmt = $this->pdo->prepare($sql);
            $stmt->execute(['codigo' => $codigo]);

            $this->pdo->commit();

            return [
                'success' => true,
                'message' => 'Tarifario eliminado correctamente'
            ];
        } catch (\PDOException $e) {
            $this->pdo->rollBack();
            return [
                'success' => false,
                'message' => 'Error al eliminar tarifario: ' . $e->getMessage()
            ];
        }
    }
}
