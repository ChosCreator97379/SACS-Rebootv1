<?php

declare(strict_types=1);

require dirname(__DIR__) . '/config/bootstrap.php';

use Sacs\Core\Database;
use Sacs\Repositories\UserRepository;
use Sacs\Security\Auth;

session_set_cookie_params([
    'httponly' => true,
    'samesite' => 'Lax',
]);
session_start();

$auth = new Auth(new UserRepository());

if (($_GET['logout'] ?? '') === '1') {
    $auth->logout();
    header('Location: index.php');
    exit;
}

if (!isset($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

$error = null;
$username = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim((string) ($_POST['username'] ?? ''));
    $password = (string) ($_POST['password'] ?? '');
    $csrfToken = (string) ($_POST['csrf_token'] ?? '');

    if (!hash_equals((string) $_SESSION['csrf_token'], $csrfToken)) {
        $error = 'La solicitud no es valida.';
    } elseif ($username === '' || $password === '') {
        $error = 'Ingrese usuario y clave.';
    } else {
        try {
            if ($auth->attempt($username, $password)) {
                header('Location: index.php');
                exit;
            }
            $error = 'Usuario o clave incorrectos.';
        } catch (Throwable $exception) {
            $error = 'No fue posible validar el acceso en este momento.';
        }
    }
}

if ($auth->check()) {
    $user = $auth->user();
    require dirname(__DIR__) . '/views/dashboard.php';
    exit;
}

$csrfToken = (string) $_SESSION['csrf_token'];
$databaseStatus = 'No verificada';
$databaseError = null;

if (($_GET['check'] ?? '') === 'database') {
    try {
        $pdo = Database::connection();
        $pdo->query('SELECT 1');
        $databaseStatus = 'Conexion exitosa';
    } catch (Throwable $exception) {
        $databaseStatus = 'Conexion fallida';
        $databaseError = $exception->getMessage();
    }
}

require dirname(__DIR__) . '/views/login.php';
