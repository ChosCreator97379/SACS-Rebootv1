<?php

declare(strict_types=1);

namespace Sacs\Services;

use PDO;

/**
 * Servicio para gestión de productos de préstamos
 */
final class ProductosServicio
{
    public function __construct(
        private readonly PDO $pdo
    ) {
    }

    /**
     * Listar productos
     */
    public function listarProductos(): array
    {
        $sql = "SELECT 
                    f31_codigo as codigo,
                    f31_nombre as nombre,
                    f31_cronograma as cronograma,
                    f31_moneda as moneda,
                    f31_max_cuotas as max_cuotas,
                    f31_monto_min as monto_min,
                    f31_monto_max as monto_max,
                    f31_conGarante as con_garante,
                    f31_estado as estado
                FROM f31_productos
                ORDER BY f31_codigo";
        
        $stmt = $this->pdo->query($sql);
        
        return $stmt->fetchAll();
    }

    /**
     * Obtener producto por código
     */
    public function obtenerProducto(int $codigo): ?array
    {
        $sql = "SELECT 
                    f31_codigo as codigo,
                    f31_nombre as nombre,
                    f31_cronograma as cronograma,
                    f31_moneda as moneda,
                    f31_max_cuotas as max_cuotas,
                    f31_monto_min as monto_min,
                    f31_monto_max as monto_max,
                    f31_conGarante as con_garante,
                    f31_estado as estado
                FROM f31_productos
                WHERE f31_codigo = :codigo";
        
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute(['codigo' => $codigo]);
        
        $result = $stmt->fetch();
        
        return $result ?: null;
    }

    /**
     * Obtener siguiente código de producto
     */
    public function obtenerSiguienteCodigo(): int
    {
        $sql = "SELECT MAX(f31_codigo) as max_codigo FROM f31_productos";
        
        $stmt = $this->pdo->query($sql);
        $result = $stmt->fetch();
        
        return ($result['max_codigo'] ?? 0) + 1;
    }

    /**
     * Registrar producto
     */
    public function registrarProducto(array $datos): array
    {
        try {
            $this->pdo->beginTransaction();

            $sql = "INSERT INTO f31_productos (
                        f31_codigo,
                        f31_nombre,
                        f31_cronograma,
                        f31_moneda,
                        f31_max_cuotas,
                        f31_monto_min,
                        f31_monto_max,
                        f31_conGarante,
                        f31_estado
                    ) VALUES (
                        :codigo,
                        :nombre,
                        :cronograma,
                        :moneda,
                        :max_cuotas,
                        :monto_min,
                        :monto_max,
                        :con_garante,
                        'A'
                    )";

            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([
                'codigo' => $datos['codigo'],
                'nombre' => $datos['nombre'],
                'cronograma' => $datos['cronograma'],
                'moneda' => $datos['moneda'],
                'max_cuotas' => $datos['max_cuotas'],
                'monto_min' => $datos['monto_min'],
                'monto_max' => $datos['monto_max'],
                'con_garante' => $datos['con_garante']
            ]);

            $this->pdo->commit();

            return [
                'success' => true,
                'message' => 'Producto registrado correctamente'
            ];
        } catch (\PDOException $e) {
            $this->pdo->rollBack();
            return [
                'success' => false,
                'message' => 'Error al registrar producto: ' . $e->getMessage()
            ];
        }
    }

    /**
     * Actualizar producto
     */
    public function actualizarProducto(int $codigo, array $datos): array
    {
        try {
            $this->pdo->beginTransaction();

            $sql = "UPDATE f31_productos 
                    SET f31_nombre = :nombre,
                        f31_cronograma = :cronograma,
                        f31_moneda = :moneda,
                        f31_max_cuotas = :max_cuotas,
                        f31_monto_min = :monto_min,
                        f31_monto_max = :monto_max,
                        f31_conGarante = :con_garante,
                        f31_estado = :estado
                    WHERE f31_codigo = :codigo";

            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([
                'nombre' => $datos['nombre'],
                'cronograma' => $datos['cronograma'],
                'moneda' => $datos['moneda'],
                'max_cuotas' => $datos['max_cuotas'],
                'monto_min' => $datos['monto_min'],
                'monto_max' => $datos['monto_max'],
                'con_garante' => $datos['con_garante'],
                'estado' => $datos['estado'],
                'codigo' => $codigo
            ]);

            $this->pdo->commit();

            return [
                'success' => true,
                'message' => 'Producto actualizado correctamente'
            ];
        } catch (\PDOException $e) {
            $this->pdo->rollBack();
            return [
                'success' => false,
                'message' => 'Error al actualizar producto: ' . $e->getMessage()
            ];
        }
    }

    /**
     * Eliminar producto
     */
    public function eliminarProducto(int $codigo): array
    {
        try {
            $this->pdo->beginTransaction();

            // Verificar si hay préstamos con este producto
            $sqlCheck = "SELECT COUNT(*) as total 
                        FROM f16_prestamo 
                        WHERE f16_producto = :codigo";
            
            $stmtCheck = $this->pdo->prepare($sqlCheck);
            $stmtCheck->execute(['codigo' => $codigo]);
            $result = $stmtCheck->fetch();
            
            if ($result['total'] > 0) {
                return [
                    'success' => false,
                    'message' => 'No se puede eliminar el producto porque hay préstamos asociados'
                ];
            }

            $sql = "DELETE FROM f31_productos WHERE f31_codigo = :codigo";

            $stmt = $this->pdo->prepare($sql);
            $stmt->execute(['codigo' => $codigo]);

            $this->pdo->commit();

            return [
                'success' => true,
                'message' => 'Producto eliminado correctamente'
            ];
        } catch (\PDOException $e) {
            $this->pdo->rollBack();
            return [
                'success' => false,
                'message' => 'Error al eliminar producto: ' . $e->getMessage()
            ];
        }
    }
}
