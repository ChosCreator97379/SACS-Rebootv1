<?php

declare(strict_types=1);

namespace Sacs\Repositories;

use PDO;
use Sacs\Core\Database;

final class UserRepository
{
    public function findActiveByCredentials(string $username, string $password): ?array
    {
        $query = <<<'SQL'
            SELECT f06_id_usuario, f06_nombre, f06_perfil, f06_supervisor
            FROM f06_usuario
            WHERE f06_nombre = :username
              AND f06_password = :password
              AND f06_estado = 'A'
            LIMIT 1
        SQL;

        $statement = Database::connection()->prepare($query);
        $statement->execute([
            'username' => strtoupper(trim($username)),
            'password' => $password,
        ]);

        $user = $statement->fetch();
        return $user === false ? null : $user;
    }
}
