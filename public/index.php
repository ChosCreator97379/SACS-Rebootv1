<?php

declare(strict_types=1);

require dirname(__DIR__) . '/config/bootstrap.php';

use Sacs\Core\Database;
use Sacs\Repositories\CajaRepository;
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

    if (($_GET['module'] ?? '') === 'caja') {
        $clientId = trim((string) ($_GET['client'] ?? ''));
        if (($_GET['profile'] ?? '') === 'client' && $clientId !== '') {
            $repository = new CajaRepository();
            $client = $repository->findClientProfile($clientId);
            if ($client === null) {
                http_response_code(404);
                $message = 'Cliente no encontrado.';
                $databaseStatus = 'No aplica';
                $databaseError = null;
                require dirname(__DIR__) . '/views/home.php';
                exit;
            }
            $loans = $repository->clientLoans($clientId);
            $installments = $repository->clientInstallments($clientId);
            $contributions = $repository->clientContributions($clientId, 'f22_aportes');
            $copeContributions = $repository->clientContributions($clientId, 'f27_aportes_cope');
            $fines = $repository->clientFines($clientId);
            $raffles = $repository->clientRaffles($clientId);
            $benefits = $repository->clientBenefits($clientId);
            require dirname(__DIR__) . '/views/caja/cliente.php';
            exit;
        }

        $movementId = filter_var($_GET['movement'] ?? null, FILTER_VALIDATE_INT, ['options' => ['min_range' => 1]]);
        if ($movementId !== false && $movementId !== null) {
            $movement = (new CajaRepository())->findMovement($movementId);
            if ($movement === null) {
                http_response_code(404);
                $error = 'Movimiento no encontrado.';
                require dirname(__DIR__) . '/views/home.php';
                exit;
            }
            require dirname(__DIR__) . '/views/caja/movimiento.php';
            exit;
        }

        $filters = [
            'date_from' => trim((string) ($_GET['date_from'] ?? '')),
            'date_to' => trim((string) ($_GET['date_to'] ?? '')),
            'operation' => trim((string) ($_GET['operation'] ?? '')),
            'client' => trim((string) ($_GET['client'] ?? '')),
            'page' => max(1, (int) ($_GET['page'] ?? 1)),
            'per_page' => 25,
        ];
        $repository = new CajaRepository();
        $operations = $repository->operations();
        $movements = $repository->listMovements($filters);
        require dirname(__DIR__) . '/views/caja/movimientos.php';
        exit;
    }

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
