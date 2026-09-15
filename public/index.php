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

    // Procesar POST de aportes FONBES (independiente de parámetros GET)
    if (($_POST['action'] ?? '') === 'aportes_fonbes' && $_SERVER['REQUEST_METHOD'] === 'POST') {
        $error = null;

        // Validar CSRF
        $csrfToken = (string) ($_POST['csrf_token'] ?? '');
        if (!hash_equals((string) $_SESSION['csrf_token'], $csrfToken)) {
            $error = 'La solicitud no es válida.';
        } else {
            try {
                $datos = [
                    'cliente' => trim((string) ($_POST['cliente'] ?? '')),
                    'anio' => trim((string) ($_POST['anio'] ?? '')),
                    'periodo' => trim((string) ($_POST['periodo'] ?? '')),
                    'periodo2' => trim((string) ($_POST['periodo2'] ?? '')),
                    'anio2' => trim((string) ($_POST['anio2'] ?? $_POST['anio'] ?? '')),
                    'monto' => (float) str_replace(',', '', $_POST['monto'] ?? '0'),
                    'formaPago' => trim((string) ($_POST['formaPago'] ?? '')),
                    'obs' => trim((string) ($_POST['obs'] ?? '')),
                    'obs2' => trim((string) ($_POST['obs2'] ?? '')),
                    'usuario' => $user['username'] ?? 'SISTEMA',
                ];

                $datosContables = [
                    'codigoContable' => trim((string) ($_POST['codigoContable'] ?? '')),
                    'tipoAsiento' => trim((string) ($_POST['tipoAsiento'] ?? '')),
                    'descripcionContable' => trim((string) ($_POST['descripcionContable'] ?? '')),
                ];

                // Validaciones básicas
                if ($datos['cliente'] === '' || $datos['anio'] === '' || $datos['periodo'] === '' || $datos['periodo2'] === '') {
                    $error = 'Datos incompletos';
                } elseif ($datos['monto'] <= 0) {
                    $error = 'El monto debe ser mayor a cero';
                } else {
                    // Llamar al servicio de aportes
                    require __DIR__ . '/../src/Services/AportesServicio.php';
                    $servicio = new \Sacs\Services\AportesServicio(Database::connection());
                    $resultado = $servicio->ejecutarPagoAportesFonbes($datos, $datosContables);

                    if ($resultado['exito']) {
                        // Redirigir al detalle del movimiento creado
                        header('Location: index.php?module=caja&movement=' . $resultado['codigo_movimiento']);
                        exit;
                    } else {
                        $error = $resultado['mensaje'] ?? 'No fue posible procesar el pago de aportes';
                    }
                }
            } catch (Throwable $exception) {
                $error = 'Error inesperado: ' . $exception->getMessage();
            }
        }

        // Redirigir al formulario con error
        $query = http_build_query(['module' => 'caja', 'action' => 'aportes_fonbes_form', 'client' => $datos['cliente'], 'error' => $error]);
        header('Location: index.php?' . $query);
        exit;
    }

    // Procesar POST de aportes COOPAM (independiente de parámetros GET)
    if (($_POST['action'] ?? '') === 'aportes_coopam' && $_SERVER['REQUEST_METHOD'] === 'POST') {
        $error = null;

        // Validar CSRF
        $csrfToken = (string) ($_POST['csrf_token'] ?? '');
        if (!hash_equals((string) $_SESSION['csrf_token'], $csrfToken)) {
            $error = 'La solicitud no es válida.';
        } else {
            try {
                $datos = [
                    'cliente' => trim((string) ($_POST['cliente'] ?? '')),
                    'anio' => trim((string) ($_POST['anio'] ?? '')),
                    'periodo' => trim((string) ($_POST['periodo'] ?? '')),
                    'periodo2' => trim((string) ($_POST['periodo2'] ?? '')),
                    'anio2' => trim((string) ($_POST['anio2'] ?? $_POST['anio'] ?? '')),
                    'monto' => (float) str_replace(',', '', $_POST['monto'] ?? '0'),
                    'formaPago' => trim((string) ($_POST['formaPago'] ?? '')),
                    'obs' => trim((string) ($_POST['obs'] ?? '')),
                    'obs2' => trim((string) ($_POST['obs2'] ?? '')),
                    'usuario' => $user['username'] ?? 'SISTEMA',
                ];

                $datosContables = [
                    'codigoContable' => trim((string) ($_POST['codigoContable'] ?? '')),
                    'tipoAsiento' => trim((string) ($_POST['tipoAsiento'] ?? '')),
                    'descripcionContable' => trim((string) ($_POST['descripcionContable'] ?? '')),
                ];

                // Validaciones básicas
                if ($datos['cliente'] === '' || $datos['anio'] === '' || $datos['periodo'] === '' || $datos['periodo2'] === '') {
                    $error = 'Datos incompletos';
                } elseif ($datos['monto'] <= 0) {
                    $error = 'El monto debe ser mayor a cero';
                } else {
                    // Llamar al servicio de aportes COOPAM
                    require __DIR__ . '/../src/Services/AportesCoopamServicio.php';
                    $servicio = new \Sacs\Services\AportesCoopamServicio(Database::connection());
                    $resultado = $servicio->ejecutarPagoAportesCoopam($datos, $datosContables);

                    if ($resultado['exito']) {
                        // Redirigir al detalle del movimiento creado
                        header('Location: index.php?module=caja&movement=' . $resultado['codigo_movimiento']);
                        exit;
                    } else {
                        $error = $resultado['mensaje'] ?? 'No fue posible procesar el pago de aportes COOPAM';
                    }
                }
            } catch (Throwable $exception) {
                $error = 'Error inesperado: ' . $exception->getMessage();
            }
        }

        // Redirigir al formulario con error
        $query = http_build_query(['module' => 'caja', 'action' => 'aportes_coopam_form', 'client' => $datos['cliente'], 'error' => $error]);
        header('Location: index.php?' . $query);
        exit;
    }

    // Procesar POST de sobregiro de préstamo (Operación 10)
    if (($_POST['action'] ?? '') === 'sobregiro_prestamo' && $_SERVER['REQUEST_METHOD'] === 'POST') {
        $error = null;

        // Validar CSRF
        $csrfToken = (string) ($_POST['csrf_token'] ?? '');
        if (!hash_equals((string) $_SESSION['csrf_token'], $csrfToken)) {
            $error = 'La solicitud no es válida.';
        } else {
            try {
                $datos = [
                    'cliente' => trim((string) ($_POST['cliente'] ?? '')),
                    'prestamo' => trim((string) ($_POST['prestamo'] ?? '')),
                    'sobregiro' => (float) str_replace(',', '', $_POST['sobregiro'] ?? '0'),
                    'amortizacion' => (float) str_replace(',', '', $_POST['amortizacion'] ?? '0'),
                    'gastosAdm' => (float) str_replace(',', '', $_POST['gastosAdm'] ?? '0'),
                    'gastosMora' => (float) str_replace(',', '', $_POST['gastosMora'] ?? '0'),
                    'otros1' => (float) str_replace(',', '', $_POST['otros1'] ?? '0'),
                    'otros2' => (float) str_replace(',', '', $_POST['otros2'] ?? '0'),
                    'monto' => (float) str_replace(',', '', $_POST['monto'] ?? '0'),
                    'formaPago' => trim((string) ($_POST['formaPago'] ?? '')),
                    'obs' => trim((string) ($_POST['obs'] ?? '')),
                    'obs2' => trim((string) ($_POST['obs2'] ?? '')),
                ];

                $datosContables = [
                    'codigoContable' => trim((string) ($_POST['codigoContable'] ?? '')),
                    'tipoAsiento' => trim((string) ($_POST['tipoAsiento'] ?? '')),
                    'descripcionContable' => trim((string) ($_POST['descripcionContable'] ?? '')),
                ];

                // Validaciones básicas
                if ($datos['cliente'] === '' || $datos['prestamo'] === '') {
                    $error = 'Cliente y préstamo son requeridos';
                } elseif ($datos['monto'] <= 0) {
                    $error = 'El monto debe ser mayor a cero';
                } else {
                    // Llamar al servicio de sobregiro de préstamo
                    require __DIR__ . '/../src/Services/SobregiroPrestamoServicio.php';
                    $servicio = new \Sacs\Services\SobregiroPrestamoServicio(Database::connection());
                    $resultado = $servicio->ejecutarPagoSobregiroPrestamo($datos, $datosContables);

                    if ($resultado['exito']) {
                        // Redirigir al detalle del movimiento creado
                        header('Location: index.php?module=caja&movement=' . $resultado['codigo_movimiento']);
                        exit;
                    } else {
                        $error = $resultado['mensaje'] ?? 'No fue posible procesar el pago de sobregiro de préstamo';
                    }
                }
            } catch (Throwable $exception) {
                $error = 'Error inesperado: ' . $exception->getMessage();
            }
        }

        // Redirigir al formulario con error
        $query = http_build_query(['module' => 'caja', 'action' => 'sobregiro_prestamo_form', 'client' => $datos['cliente'], 'error' => $error]);
        header('Location: index.php?' . $query);
        exit;
    }

    // Procesar POST de multas (Operación 11)
    if (($_POST['action'] ?? '') === 'multas' && $_SERVER['REQUEST_METHOD'] === 'POST') {
        $error = null;

        // Validar CSRF
        $csrfToken = (string) ($_POST['csrf_token'] ?? '');
        if (!hash_equals((string) $_SESSION['csrf_token'], $csrfToken)) {
            $error = 'La solicitud no es válida.';
        } else {
            try {
                $datos = [
                    'cliente' => trim((string) ($_POST['cliente'] ?? '')),
                    'multa' => trim((string) ($_POST['multa'] ?? '')),
                    'monto' => (float) str_replace(',', '', $_POST['monto'] ?? '0'),
                    'formaPago' => trim((string) ($_POST['formaPago'] ?? '')),
                    'obs' => trim((string) ($_POST['obs'] ?? '')),
                    'obs2' => trim((string) ($_POST['obs2'] ?? '')),
                    'usuario' => trim((string) ($_POST['usuario'] ?? 'SISTEMA')),
                ];

                $datosContables = [
                    'codigoContable' => trim((string) ($_POST['codigoContable'] ?? '')),
                    'tipoAsiento' => trim((string) ($_POST['tipoAsiento'] ?? '')),
                    'descripcionContable' => trim((string) ($_POST['descripcionContable'] ?? '')),
                ];

                // Validaciones básicas
                if ($datos['cliente'] === '' || $datos['multa'] === '') {
                    $error = 'Cliente y multa son requeridos';
                } elseif ($datos['monto'] <= 0) {
                    $error = 'El monto debe ser mayor a cero';
                } else {
                    // Llamar al servicio de multas
                    require __DIR__ . '/../src/Services/MultasServicio.php';
                    $servicio = new \Sacs\Services\MultasServicio(Database::connection());
                    $resultado = $servicio->ejecutarPagoMulta($datos, $datosContables);

                    if ($resultado['exito']) {
                        // Redirigir al detalle del movimiento creado
                        header('Location: index.php?module=caja&movement=' . $resultado['codigo_movimiento']);
                        exit;
                    } else {
                        $error = $resultado['mensaje'] ?? 'No fue posible procesar el pago de multa';
                    }
                }
            } catch (Throwable $exception) {
                $error = 'Error inesperado: ' . $exception->getMessage();
            }
        }

        // Redirigir al formulario con error
        $_SESSION['multa_error'] = $error;
        $query = http_build_query(['module' => 'caja', 'action' => 'multas_form', 'client' => $datos['cliente'], 'multa' => $datos['multa']]);
        header('Location: index.php?' . $query);
        exit;
    }

    // Procesar POST de amortización (independiente de parámetros GET)
    if (($_POST['action'] ?? '') === 'amortizar' && $_SERVER['REQUEST_METHOD'] === 'POST') {
        // Procesar amortización AEE
        $error = null;

        // Validar CSRF
        $csrfToken = (string) ($_POST['csrf_token'] ?? '');
        if (!hash_equals((string) $_SESSION['csrf_token'], $csrfToken)) {
            $error = 'La solicitud no es válida.';
        } else {
            try {
                $datos = [
                    'cliente' => trim((string) ($_POST['cliente'] ?? '')),
                    'prestamo' => (int) ($_POST['prestamo'] ?? 0),
                    'cuota' => (int) ($_POST['cuota'] ?? 0),
                    'amortiza' => (float) str_replace(',', '', $_POST['amortiza'] ?? '0'),
                    'gastosAdm' => (float) str_replace(',', '', $_POST['gastosAdm'] ?? '0'),
                    'gastosMora' => (float) str_replace(',', '', $_POST['gastosMora'] ?? '0'),
                    'otros1' => (float) str_replace(',', '', $_POST['otros1'] ?? '0'),
                    'otros2' => (float) str_replace(',', '', $_POST['otros2'] ?? '0'),
                    'sobregiro' => (float) str_replace(',', '', $_POST['sobregiro'] ?? '0'),
                    'monto' => (float) str_replace(',', '', $_POST['monto'] ?? '0'),
                    'formaPago' => trim((string) ($_POST['formaPago'] ?? '')),
                    'fecha' => trim((string) ($_POST['fecha'] ?? date('Y-m-d'))),
                    'obs' => trim((string) ($_POST['obs'] ?? '')),
                    'obs2' => trim((string) ($_POST['obs2'] ?? '')),
                    'usuario' => $user['username'] ?? 'SISTEMA',
                    'cheque' => null,
                ];

                $datosContables = [
                    'codigoContable' => trim((string) ($_POST['codigoContable'] ?? '')),
                    'tipoAsiento' => trim((string) ($_POST['tipoAsiento'] ?? '')),
                    'descripcionContable' => trim((string) ($_POST['descripcionContable'] ?? '')),
                ];

                // Validaciones básicas
                if ($datos['cliente'] === '' || $datos['prestamo'] === 0 || $datos['cuota'] === 0) {
                    $error = 'Datos incompletos';
                } elseif ($datos['monto'] <= 0) {
                    $error = 'El monto debe ser mayor a cero';
                } else {
                    // Llamar al servicio de amortización
                    require __DIR__ . '/../src/Services/AmortizacionServicio.php';
                    $servicio = new \Sacs\Services\AmortizacionServicio(Database::connection());
                    $resultado = $servicio->ejecutarAmortizacionAEE($datos, $datosContables);

                    if ($resultado['exito']) {
                        // Redirigir al detalle del movimiento creado
                        header('Location: index.php?module=caja&movement=' . $resultado['codigo_movimiento']);
                        exit;
                    } else {
                        $error = $resultado['mensaje'] ?? 'No fue posible procesar la amortización';
                    }
                }
            } catch (Throwable $exception) {
                $error = 'Error inesperado: ' . $exception->getMessage();
            }
        }

        // Redirigir al formulario con error
        $query = http_build_query(['module' => 'caja', 'action' => 'amortizar_form', 'client' => $datos['cliente'], 'loan' => $datos['prestamo'], 'error' => $error]);
        header('Location: index.php?' . $query);
        exit;
    }

    // Procesar POST de desembolso de préstamo (Operación 3)
    if (($_POST['action'] ?? '') === 'desembolso_prestamo' && $_SERVER['REQUEST_METHOD'] === 'POST') {
        $error = null;

        // Validar CSRF
        $csrfToken = (string) ($_POST['csrf_token'] ?? '');
        if (!hash_equals((string) $_SESSION['csrf_token'], $csrfToken)) {
            $error = 'La solicitud no es válida.';
        } else {
            try {
                $datos = [
                    'cliente' => trim((string) ($_POST['cliente'] ?? '')),
                    'prestamo' => (int) ($_POST['prestamo'] ?? 0),
                    'monto' => (float) str_replace(',', '', $_POST['monto'] ?? '0'),
                    'formaPago' => trim((string) ($_POST['formaPago'] ?? '')),
                    'fecha' => trim((string) ($_POST['fecha'] ?? date('Y-m-d'))),
                    'obs' => trim((string) ($_POST['obs'] ?? '')),
                    'obs2' => trim((string) ($_POST['obs2'] ?? '')),
                    'usuario' => $user['username'] ?? 'SISTEMA',
                ];

                $datosContables = [
                    'codigoContable' => trim((string) ($_POST['codigoContable'] ?? '')),
                    'tipoAsiento' => trim((string) ($_POST['tipoAsiento'] ?? '')),
                    'descripcionContable' => trim((string) ($_POST['descripcionContable'] ?? '')),
                ];

                // Validaciones básicas
                if ($datos['cliente'] === '' || $datos['prestamo'] === 0) {
                    $error = 'Cliente y préstamo son requeridos';
                } elseif ($datos['monto'] <= 0) {
                    $error = 'El monto debe ser mayor a cero';
                } else {
                    // Llamar al servicio de desembolso de préstamo
                    require __DIR__ . '/../src/Services/DesembolsoPrestamoServicio.php';
                    $servicio = new \Sacs\Services\DesembolsoPrestamoServicio(Database::connection());
                    $resultado = $servicio->ejecutarDesembolsoPrestamo($datos, $datosContables);

                    if ($resultado['exito']) {
                        // Redirigir al detalle del movimiento creado
                        header('Location: index.php?module=caja&movement=' . $resultado['codigo_movimiento']);
                        exit;
                    } else {
                        $error = $resultado['mensaje'] ?? 'No fue posible procesar el desembolso de préstamo';
                    }
                }
            } catch (Throwable $exception) {
                $error = 'Error inesperado: ' . $exception->getMessage();
            }
        }

        // Redirigir al formulario con error
        $query = http_build_query(['module' => 'caja', 'action' => 'desembolso_prestamo_form', 'client' => $datos['cliente'], 'loan' => $datos['prestamo'], 'error' => $error]);
        header('Location: index.php?' . $query);
        exit;
    }

    if (($_GET['module'] ?? '') === 'caja') {
        $clientId = trim((string) ($_GET['client'] ?? ''));
        $loanId = filter_var($_GET['loan'] ?? null, FILTER_VALIDATE_INT, ['options' => ['min_range' => 1]]);
        $installmentNumber = filter_var($_GET['installment'] ?? null, FILTER_VALIDATE_INT, ['options' => ['min_range' => 1]]);
        if ($clientId !== '' && $loanId !== false && $loanId !== null && $installmentNumber !== false && $installmentNumber !== null) {
            $simulation = (new CajaRepository())->simulateInstallmentPayment($clientId, $loanId, $installmentNumber);
            if ($simulation === null) {
                http_response_code(404);
                $message = 'La cuota vigente no fue encontrada para el cliente indicado.';
                $databaseStatus = 'No aplica';
                $databaseError = null;
                require dirname(__DIR__) . '/views/home.php';
                exit;
            }
            require dirname(__DIR__) . '/views/caja/simulacion.php';
            exit;
        }

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
            $loansWithOverdraft = $repository->clientLoansWithOverdraft($clientId);
            $loansPendingDisbursement = $repository->clientLoansPendingDisbursement($clientId);
            $installments = $repository->clientInstallments($clientId);
            $contributions = $repository->clientContributions($clientId, 'f22_aportes');
            $copeContributions = $repository->clientContributions($clientId, 'f27_aportes_cope');
            $fines = $repository->clientFines($clientId);
            $raffles = $repository->clientRaffles($clientId);
            $benefits = $repository->clientBenefits($clientId);
            require dirname(__DIR__) . '/views/caja/cliente.php';
            exit;
        }

        if (($_GET['action'] ?? '') === 'aportes_fonbes_form') {
            if ($clientId !== '') {
                require dirname(__DIR__) . '/views/caja/aportes_fonbes.php';
                exit;
            }
            http_response_code(400);
            $error = 'Datos inválidos para el formulario de aportes';
            require dirname(__DIR__) . '/views/home.php';
            exit;
        }

        if (($_GET['action'] ?? '') === 'aportes_coopam_form') {
            if ($clientId !== '') {
                require dirname(__DIR__) . '/views/caja/aportes_coopam.php';
                exit;
            }
            http_response_code(400);
            $error = 'Datos inválidos para el formulario de aportes COOPAM';
            require dirname(__DIR__) . '/views/home.php';
            exit;
        }

        if (($_GET['action'] ?? '') === 'sobregiro_prestamo_form') {
            if ($clientId !== '') {
                require dirname(__DIR__) . '/views/caja/sobregiro_prestamo.php';
                exit;
            }
            http_response_code(400);
            $error = 'Datos inválidos para el formulario de sobregiro de préstamo';
            require dirname(__DIR__) . '/views/home.php';
            exit;
        }

        if (($_GET['action'] ?? '') === 'multas_form') {
            if ($clientId !== '') {
                require dirname(__DIR__) . '/views/caja/multas.php';
                exit;
            }
            http_response_code(400);
            $error = 'Datos inválidos para el formulario de multas';
            require dirname(__DIR__) . '/views/home.php';
            exit;
        }

        if (($_GET['action'] ?? '') === 'desembolso_prestamo_form') {
            $loanId = filter_var($_GET['loan'] ?? null, FILTER_VALIDATE_INT, ['options' => ['min_range' => 1]]);
            if ($clientId !== '' && $loanId !== false && $loanId !== null) {
                require dirname(__DIR__) . '/views/caja/desembolso_prestamo.php';
                exit;
            }
            if ($clientId !== '') {
                require dirname(__DIR__) . '/views/caja/seleccionar_desembolso.php';
                exit;
            }
            http_response_code(400);
            $error = 'Datos inválidos para el formulario de desembolso de préstamo';
            require dirname(__DIR__) . '/views/home.php';
            exit;
        }

        if (($_GET['action'] ?? '') === 'amortizar_form') {
            $loanId = filter_var($_GET['loan'] ?? null, FILTER_VALIDATE_INT, ['options' => ['min_range' => 1]]);
            if ($clientId !== '' && $loanId !== false && $loanId !== null) {
                require dirname(__DIR__) . '/views/caja/amortizacion.php';
                exit;
            }
            if ($clientId !== '') {
                require dirname(__DIR__) . '/views/caja/seleccionar_amortizacion.php';
                exit;
            }
            http_response_code(400);
            $error = 'Datos inválidos para el formulario de amortización';
            require dirname(__DIR__) . '/views/home.php';
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

        if (($_GET['action'] ?? '') === 'diario_contable') {
            $filters = [
                'date_from' => trim((string) ($_GET['date_from'] ?? '')),
                'date_to' => trim((string) ($_GET['date_to'] ?? '')),
                'operation' => trim((string) ($_GET['operation'] ?? '')),
                'client' => trim((string) ($_GET['client'] ?? '')),
                'accounting_code' => trim((string) ($_GET['accounting_code'] ?? '')),
                'page' => max(1, (int) ($_GET['page'] ?? 1)),
                'per_page' => 25,
            ];

            // Verificar si se ha aplicado algún filtro
            $hasFilter = !empty($filters['date_from']) || !empty($filters['date_to']) ||
                         !empty($filters['operation']) || !empty($filters['client']) ||
                         !empty($filters['accounting_code']);

            $repository = new CajaRepository();
            $operations = $repository->operations();

            if ($hasFilter) {
                $items = $repository->accountingEntries($filters);
                $total = $repository->accountingEntryTotal($filters);
            } else {
                $items = [];
                $total = 0;
            }

            $entries = [
                'items' => $items,
                'total' => $total,
                'page' => $filters['page'],
                'pages' => (int) ceil($total / $filters['per_page']),
                'searched' => $hasFilter,
            ];
            require dirname(__DIR__) . '/views/caja/diario_contable.php';
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
