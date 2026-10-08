<?php

declare(strict_types=1);

namespace Sacs\Services;

use PDO;
use RuntimeException;

/**
 * Servicio para gestión de usuarios
 */
final class UsuariosServicio
{
    public function __construct(
        private readonly PDO $pdo
    ) {
    }

    /**
     * Listar usuarios activos
     */
    public function listarUsuarios(array $filtros = [], int $pagina = 1, int $porPagina = 10): array
    {
        $where = ["f06_estado = 'A'"];
        $params = [];
        
        if (!empty($filtros['nombre'])) {
            $where[] = "f06_nombre LIKE :nombre";
            $params['nombre'] = '%' . $filtros['nombre'] . '%';
        }
        
        if (!empty($filtros['perfil'])) {
            $where[] = "f06_perfil = :perfil";
            $params['perfil'] = $filtros['perfil'];
        }
        
        $offset = ($pagina - 1) * $porPagina;
        
        $sql = "SELECT 
                    f06_id_usuario as id,
                    f06_nombre as nombre,
                    f06_perfil as perfil,
                    f06_estado as estado
                FROM f06_usuario
                WHERE " . implode(' AND ', $where) . "
                ORDER BY f06_id_usuario DESC
                LIMIT :limit OFFSET :offset";
        
        $stmt = $this->pdo->prepare($sql);
        foreach ($params as $key => $value) {
            $stmt->bindValue($key, $value);
        }
        $stmt->bindValue(':limit', $porPagina, \PDO::PARAM_INT);
        $stmt->bindValue(':offset', $offset, \PDO::PARAM_INT);
        $stmt->execute();
        
        return $stmt->fetchAll();
    }

    /**
     * Contar usuarios
     */
    public function contarUsuarios(array $filtros = []): int
    {
        $where = ["f06_estado = 'A'"];
        $params = [];
        
        if (!empty($filtros['nombre'])) {
            $where[] = "f06_nombre LIKE :nombre";
            $params['nombre'] = '%' . $filtros['nombre'] . '%';
        }
        
        if (!empty($filtros['perfil'])) {
            $where[] = "f06_perfil = :perfil";
            $params['perfil'] = $filtros['perfil'];
        }
        
        $sql = "SELECT COUNT(*) as total
                FROM f06_usuario
                WHERE " . implode(' AND ', $where);
        
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);
        
        $result = $stmt->fetch();
        
        return (int) ($result['total'] ?? 0);
    }

    /**
     * Obtener usuario por ID
     */
    public function obtenerUsuario(int $id): ?array
    {
        $sql = "SELECT 
                    f06_id_usuario as id,
                    f06_nombre as nombre,
                    f06_perfil as perfil,
                    f06_password as password,
                    f06_estado as estado
                FROM f06_usuario
                WHERE f06_id_usuario = :id";
        
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute(['id' => $id]);
        
        $result = $stmt->fetch();
        
        return $result ?: null;
    }

    /**
     * Obtener usuario por nombre
     */
    public function obtenerUsuarioPorNombre(string $nombre): ?array
    {
        $sql = "SELECT 
                    f06_id_usuario as id,
                    f06_nombre as nombre,
                    f06_perfil as perfil,
                    f06_password as password,
                    f06_estado as estado
                FROM f06_usuario
                WHERE f06_nombre = :nombre";
        
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute(['nombre' => $nombre]);
        
        $result = $stmt->fetch();
        
        return $result ?: null;
    }

    /**
     * Obtener perfiles
     */
    public function obtenerPerfiles(): array
    {
        $sql = "SELECT f08_codigo as id, f08_nombre as nombre 
                FROM f08_perfil 
                ORDER BY f08_codigo";
        
        $stmt = $this->pdo->query($sql);
        
        return $stmt->fetchAll();
    }

    /**
     * Obtener nombre del perfil
     */
    public function obtenerNombrePerfil(int $perfilId): ?string
    {
        $sql = "SELECT f08_nombre 
                FROM f08_perfil 
                WHERE f08_codigo = :id";
        
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute(['id' => $perfilId]);
        
        $result = $stmt->fetch();
        
        return $result['f08_nombre'] ?? null;
    }

    /**
     * Registrar usuario
     */
    public function registrarUsuario(array $datos): array
    {
        try {
            $this->pdo->beginTransaction();

            $sql = "INSERT INTO f06_usuario (
                        f06_nombre,
                        f06_perfil,
                        f06_password,
                        f06_estado
                    ) VALUES (
                        :nombre,
                        :perfil,
                        :password,
                        'A'
                    )";

            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([
                'nombre' => $datos['nombre'],
                'perfil' => $datos['perfil'],
                'password' => $datos['password']
            ]);

            $id = (int) $this->pdo->lastInsertId();

            $this->pdo->commit();

            return [
                'success' => true,
                'message' => 'Usuario registrado correctamente',
                'id' => $id
            ];
        } catch (\PDOException $e) {
            $this->pdo->rollBack();
            return [
                'success' => false,
                'message' => 'Error al registrar usuario: ' . $e->getMessage()
            ];
        }
    }

    /**
     * Validar datos de usuario
     */
    public function validarUsuario(array $datos): array
    {
        $errores = [];

        if (empty($datos['nombre'])) {
            $errores[] = 'El nombre de usuario es requerido';
        }

        if (empty($datos['password'])) {
            $errores[] = 'La contraseña es requerida';
        } elseif (strlen($datos['password']) < 6) {
            $errores[] = 'La contraseña debe tener al menos 6 caracteres';
        }

        if (empty($datos['confirmar_password'])) {
            $errores[] = 'Debe confirmar la contraseña';
        } elseif ($datos['password'] !== $datos['confirmar_password']) {
            $errores[] = 'Las contraseñas no coinciden';
        }

        if (empty($datos['perfil'])) {
            $errores[] = 'El perfil es requerido';
        }

        return $errores;
    }

    /**
     * Hash de contraseña
     */
    private function hashPassword(string $password): string
    {
        return password_hash($password, PASSWORD_DEFAULT);
    }

    /**
     * Actualizar usuario
     */
    public function actualizarUsuario(int $id, array $datos): array
    {
        try {
            $this->pdo->beginTransaction();

            $sql = "UPDATE f06_usuario 
                    SET f06_nombre = :nombre,
                        f06_perfil = :perfil";

            // Si se va a cambiar la contraseña
            if (!empty($datos['password'])) {
                $sql .= ", f06_password = :password";
            }

            // Si se va a cambiar el estado
            if (isset($datos['estado'])) {
                $sql .= ", f06_estado = :estado";
            }

            $sql .= " WHERE f06_id_usuario = :id";

            $stmt = $this->pdo->prepare($sql);
            $params = [
                'nombre' => $datos['nombre'],
                'perfil' => $datos['perfil'],
                'id' => $id
            ];

            if (!empty($datos['password'])) {
                $params['password'] = $datos['password'];
            }

            if (isset($datos['estado'])) {
                $params['estado'] = $datos['estado'];
            }

            $stmt->execute($params);

            $this->pdo->commit();

            return [
                'success' => true,
                'message' => 'Usuario actualizado correctamente'
            ];
        } catch (\PDOException $e) {
            $this->pdo->rollBack();
            return [
                'success' => false,
                'message' => 'Error al actualizar usuario: ' . $e->getMessage()
            ];
        }
    }

    /**
     * Cambiar contraseña
     */
    public function cambiarPassword(int $id, string $nuevaPassword): array
    {
        try {
            $this->pdo->beginTransaction();

            $sql = "UPDATE f06_usuario 
                    SET f06_password = :password
                    WHERE f06_id_usuario = :id";

            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([
                'password' => $nuevaPassword,
                'id' => $id
            ]);

            $this->pdo->commit();

            return [
                'success' => true,
                'message' => 'Contraseña cambiada correctamente'
            ];
        } catch (\PDOException $e) {
            $this->pdo->rollBack();
            return [
                'success' => false,
                'message' => 'Error al cambiar contraseña: ' . $e->getMessage()
            ];
        }
    }

    /**
     * Eliminar usuario (soft delete, cambia estado a B)
     */
    public function eliminarUsuario(int $id): array
    {
        try {
            $this->pdo->beginTransaction();

            $sql = "UPDATE f06_usuario 
                    SET f06_estado = 'B'
                    WHERE f06_id_usuario = :id";

            $stmt = $this->pdo->prepare($sql);
            $stmt->execute(['id' => $id]);

            $this->pdo->commit();

            return [
                'success' => true,
                'message' => 'Usuario eliminado correctamente'
            ];
        } catch (\PDOException $e) {
            $this->pdo->rollBack();
            return [
                'success' => false,
                'message' => 'Error al eliminar usuario: ' . $e->getMessage()
            ];
        }
    }
}
