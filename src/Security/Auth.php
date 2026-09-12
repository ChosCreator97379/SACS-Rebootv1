<?php

declare(strict_types=1);

namespace Sacs\Security;

use Sacs\Repositories\UserRepository;

final class Auth
{
    private const SESSION_KEY = 'sacs_user';

    public function __construct(private readonly UserRepository $users)
    {
    }

    public function attempt(string $username, string $password): bool
    {
        $user = $this->users->findActiveByCredentials($username, $password);
        if ($user === null) {
            return false;
        }

        session_regenerate_id(true);
        $_SESSION[self::SESSION_KEY] = [
            'id' => (int) $user['f06_id_usuario'],
            'username' => (string) $user['f06_nombre'],
            'profile' => (int) $user['f06_perfil'],
            'supervisor' => (string) ($user['f06_supervisor'] ?? ''),
        ];

        return true;
    }

    public function user(): ?array
    {
        return $_SESSION[self::SESSION_KEY] ?? null;
    }

    public function check(): bool
    {
        return $this->user() !== null;
    }

    public function logout(): void
    {
        $_SESSION = [];
        if (ini_get('session.use_cookies')) {
            $parameters = session_get_cookie_params();
            setcookie(session_name(), '', time() - 42000, $parameters['path'], $parameters['domain'], (bool) $parameters['secure'], (bool) $parameters['httponly']);
        }
        session_destroy();
    }
}
