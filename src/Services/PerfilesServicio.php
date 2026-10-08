<?php

declare(strict_types=1);

namespace Sacs\Services;

use PDO;

/**
 * Servicio para gestión de perfiles
 */
final class PerfilesServicio
{
    public function __construct(
        private readonly PDO $pdo
    ) {
    }

    /**
     * Listar perfiles
     */
    public function listarPerfiles(): array
    {
        $sql = "SELECT f08_codigo as id, f08_nombre as nombre 
                FROM f08_perfil 
                ORDER BY f08_codigo";
        
        $stmt = $this->pdo->query($sql);
        
        return $stmt->fetchAll();
    }

    /**
     * Obtener perfil por ID
     */
    public function obtenerPerfil(int $id): ?array
    {
        $sql = "SELECT f08_codigo as id, f08_nombre as nombre 
                FROM f08_perfil 
                WHERE f08_codigo = :id";
        
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute(['id' => $id]);
        
        $result = $stmt->fetch();
        
        return $result ?: null;
    }

    /**
     * Registrar perfil
     */
    public function registrarPerfil(array $datos): array
    {
        try {
            $this->pdo->beginTransaction();

            $sql = "INSERT INTO f08_perfil (
                        f08_codigo,
                        f08_nombre
                    ) VALUES (
                        :codigo,
                        :nombre
                    )";

            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([
                'codigo' => $datos['codigo'],
                'nombre' => $datos['nombre']
            ]);

            $this->pdo->commit();

            return [
                'success' => true,
                'message' => 'Perfil registrado correctamente'
            ];
        } catch (\PDOException $e) {
            $this->pdo->rollBack();
            return [
                'success' => false,
                'message' => 'Error al registrar perfil: ' . $e->getMessage()
            ];
        }
    }

    /**
     * Actualizar perfil
     */
    public function actualizarPerfil(int $id, array $datos): array
    {
        try {
            $this->pdo->beginTransaction();

            $sql = "UPDATE f08_perfil 
                    SET f08_nombre = :nombre
                    WHERE f08_codigo = :id";

            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([
                'nombre' => $datos['nombre'],
                'id' => $id
            ]);

            $this->pdo->commit();

            return [
                'success' => true,
                'message' => 'Perfil actualizado correctamente'
            ];
        } catch (\PDOException $e) {
            $this->pdo->rollBack();
            return [
                'success' => false,
                'message' => 'Error al actualizar perfil: ' . $e->getMessage()
            ];
        }
    }

    /**
     * Eliminar perfil
     */
    public function eliminarPerfil(int $id): array
    {
        try {
            $this->pdo->beginTransaction();

            // Verificar si hay usuarios con este perfil
            $sqlCheck = "SELECT COUNT(*) as total 
                        FROM f06_usuario 
                        WHERE f06_perfil = :id";
            
            $stmtCheck = $this->pdo->prepare($sqlCheck);
            $stmtCheck->execute(['id' => $id]);
            $result = $stmtCheck->fetch();
            
            if ($result['total'] > 0) {
                return [
                    'success' => false,
                    'message' => 'No se puede eliminar el perfil porque hay usuarios asignados'
                ];
            }

            $sql = "DELETE FROM f08_perfil WHERE f08_codigo = :id";

            $stmt = $this->pdo->prepare($sql);
            $stmt->execute(['id' => $id]);

            $this->pdo->commit();

            return [
                'success' => true,
                'message' => 'Perfil eliminado correctamente'
            ];
        } catch (\PDOException $e) {
            $this->pdo->rollBack();
            return [
                'success' => false,
                'message' => 'Error al eliminar perfil: ' . $e->getMessage()
            ];
        }
    }
}
