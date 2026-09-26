<?php

declare(strict_types=1);

require dirname(__DIR__) . '/config/bootstrap.php';

use Sacs\Core\Database;
use Sacs\Repositories\CajaRepository;
use Sacs\Repositories\UserRepository;
use Sacs\Security\Auth;
use Sacs\Services\SociosServicio;

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

                // Códigos contables por componente
                $codigosContables = [
                    'aporte' => [
                        'codigo' => trim((string) ($_POST['aporte_codigo'] ?? '')),
                        'tipo' => trim((string) ($_POST['aporte_tipo'] ?? '')),
                        'monto' => $datos['monto']
                    ],
                    'total' => [
                        'codigo' => trim((string) ($_POST['total_codigo'] ?? '')),
                        'tipo' => trim((string) ($_POST['total_tipo'] ?? '')),
                        'monto' => $datos['monto']
                    ],
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
                    $resultado = $servicio->ejecutarPagoAportesFonbes($datos, $codigosContables);

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

                // Códigos contables por componente
                $codigosContables = [
                    'aporte' => [
                        'codigo' => trim((string) ($_POST['aporteCoopamCodigo'] ?? '')),
                        'tipo' => trim((string) ($_POST['aporteCoopamTipo'] ?? '')),
                        'monto' => (float) str_replace(',', '', $_POST['monto'] ?? '0'),
                    ],
                    'total' => [
                        'codigo' => trim((string) ($_POST['totalCodigo'] ?? '')),
                        'tipo' => trim((string) ($_POST['totalTipo'] ?? '')),
                        'monto' => (float) str_replace(',', '', $_POST['monto'] ?? '0'),
                    ],
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
                    $resultado = $servicio->ejecutarPagoAportesCoopam($datos, $codigosContables);

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

    // Procesar POST de aportes combinados FONBES + COOPAM
    if (($_POST['action'] ?? '') === 'aportes_combinados' && $_SERVER['REQUEST_METHOD'] === 'POST') {
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
                    'montoFonbes' => (float) str_replace(',', '', $_POST['montoFonbes'] ?? '0'),
                    'montoCoopam' => (float) str_replace(',', '', $_POST['montoCoopam'] ?? '0'),
                    'formaPago' => trim((string) ($_POST['formaPago'] ?? '')),
                    'obs' => trim((string) ($_POST['obs'] ?? '')),
                    'obs2' => trim((string) ($_POST['obs2'] ?? '')),
                    'usuario' => $user['username'] ?? 'SISTEMA',
                ];

                // Códigos contables FONBES por componente
                $codigosContablesFonbes = [
                    'aporte' => [
                        'codigo' => trim((string) ($_POST['aporteFonbesCodigo'] ?? '')),
                        'tipo' => trim((string) ($_POST['aporteFonbesTipo'] ?? '')),
                        'monto' => (float) str_replace(',', '', $_POST['montoFonbes'] ?? '0'),
                    ],
                    'total' => [
                        'codigo' => trim((string) ($_POST['totalFonbesCodigo'] ?? '')),
                        'tipo' => trim((string) ($_POST['totalFonbesTipo'] ?? '')),
                        'monto' => (float) str_replace(',', '', $_POST['montoFonbes'] ?? '0'),
                    ],
                ];

                // Códigos contables COOPAM por componente
                $codigosContablesCoopam = [
                    'aporte' => [
                        'codigo' => trim((string) ($_POST['aporteCoopamCodigo'] ?? '')),
                        'tipo' => trim((string) ($_POST['aporteCoopamTipo'] ?? '')),
                        'monto' => (float) str_replace(',', '', $_POST['montoCoopam'] ?? '0'),
                    ],
                    'total' => [
                        'codigo' => trim((string) ($_POST['totalCoopamCodigo'] ?? '')),
                        'tipo' => trim((string) ($_POST['totalCoopamTipo'] ?? '')),
                        'monto' => (float) str_replace(',', '', $_POST['montoCoopam'] ?? '0'),
                    ],
                ];

                // Validaciones básicas
                if ($datos['cliente'] === '' || $datos['anio'] === '' || $datos['periodo'] === '') {
                    $error = 'Datos incompletos';
                } elseif ($datos['montoFonbes'] <= 0 && $datos['montoCoopam'] <= 0) {
                    $error = 'Debe pagar al menos un aporte (FONBES o COOPAM)';
                } else {
                    // Llamar al servicio de aportes combinados
                    require __DIR__ . '/../src/Services/AportesCombinadosServicio.php';
                    $servicio = new \Sacs\Services\AportesCombinadosServicio(Database::connection());
                    $resultado = $servicio->ejecutarPagoCombinado($datos, $codigosContablesFonbes, $codigosContablesCoopam);

                    if ($resultado['exito']) {
                        // Redirigir al detalle del primer movimiento (FONBES)
                        header('Location: index.php?module=caja&movement=' . $resultado['codigo_movimiento_fonbes']);
                        exit;
                    } else {
                        $error = $resultado['mensaje'] ?? 'No fue posible procesar el pago combinado';
                    }
                }
            } catch (Throwable $exception) {
                $error = 'Error inesperado: ' . $exception->getMessage();
            }
        }

        // Redirigir al formulario con error
        $query = http_build_query(['module' => 'caja', 'action' => 'aportes_combinados_form', 'client' => $datos['cliente'], 'error' => $error]);
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

                // Códigos contables por componente
                $codigosContables = [
                    'sobregiro' => [
                        'codigo' => trim((string) ($_POST['sobregiro_codigo'] ?? '')),
                        'tipo' => trim((string) ($_POST['sobregiro_tipo'] ?? '')),
                        'monto' => $datos['sobregiro']
                    ],
                    'descuenta' => [
                        'codigo' => trim((string) ($_POST['descuenta_codigo'] ?? '')),
                        'tipo' => trim((string) ($_POST['descuenta_tipo'] ?? '')),
                        'monto' => $datos['descuenta']
                    ],
                    'gastosAdm' => [
                        'codigo' => trim((string) ($_POST['gastosAdm_codigo'] ?? '')),
                        'tipo' => trim((string) ($_POST['gastosAdm_tipo'] ?? '')),
                        'monto' => $datos['gastosAdm']
                    ],
                    'gastosMora' => [
                        'codigo' => trim((string) ($_POST['gastosMora_codigo'] ?? '')),
                        'tipo' => trim((string) ($_POST['gastosMora_tipo'] ?? '')),
                        'monto' => $datos['gastosMora']
                    ],
                    'otros1' => [
                        'codigo' => trim((string) ($_POST['otros1_codigo'] ?? '')),
                        'tipo' => trim((string) ($_POST['otros1_tipo'] ?? '')),
                        'monto' => $datos['otros1']
                    ],
                    'otros2' => [
                        'codigo' => trim((string) ($_POST['otros2_codigo'] ?? '')),
                        'tipo' => trim((string) ($_POST['otros2_tipo'] ?? '')),
                        'monto' => $datos['otros2']
                    ],
                    'monto' => [
                        'codigo' => trim((string) ($_POST['monto_codigo'] ?? '')),
                        'tipo' => trim((string) ($_POST['monto_tipo'] ?? '')),
                        'monto' => $datos['monto']
                    ]
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
                    $resultado = $servicio->ejecutarPagoSobregiroPrestamo($datos, $codigosContables);

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

    // Procesar POST de devolución FONBES (Operación 4)
    if (($_POST['action'] ?? '') === 'devolucion_fonbes' && $_SERVER['REQUEST_METHOD'] === 'POST') {
        $error = null;

        // Validar CSRF
        $csrfToken = (string) ($_POST['csrf_token'] ?? '');
        if (!hash_equals((string) $_SESSION['csrf_token'], $csrfToken)) {
            $error = 'La solicitud no es válida.';
        } else {
            try {
                $datos = [
                    'cliente' => trim((string) ($_POST['cliente'] ?? '')),
                    'periodo' => trim((string) ($_POST['periodo'] ?? '')),
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
                if ($datos['cliente'] === '' || $datos['periodo'] === '') {
                    $error = 'Cliente y periodo son requeridos';
                } elseif ($datos['monto'] <= 0) {
                    $error = 'El monto debe ser mayor a cero';
                } else {
                    // Llamar al servicio de devolución FONBES
                    require __DIR__ . '/../src/Services/DevolucionFonbesServicio.php';
                    $servicio = new \Sacs\Services\DevolucionFonbesServicio(Database::connection());
                    $resultado = $servicio->ejecutarDevolucionFonbes($datos, $datosContables);

                    if ($resultado['exito']) {
                        // Redirigir al detalle del movimiento creado
                        header('Location: index.php?module=caja&movement=' . $resultado['codigo_movimiento']);
                        exit;
                    } else {
                        $error = $resultado['mensaje'] ?? 'No fue posible procesar la devolución';
                    }
                }
            } catch (Throwable $exception) {
                $error = 'Error inesperado: ' . $exception->getMessage();
            }
        }

        // Redirigir al formulario con error
        $query = http_build_query(['module' => 'caja', 'action' => 'devolucion_fonbes_form', 'client' => $datos['cliente'], 'periodo' => $datos['periodo'], 'error' => $error]);
        header('Location: index.php?' . $query);
        exit;
    }

    // Procesar POST de devolución COOPAM (Operación 5)
    if (($_POST['action'] ?? '') === 'devolucion_coopam' && $_SERVER['REQUEST_METHOD'] === 'POST') {
        $error = null;

        // Validar CSRF
        $csrfToken = (string) ($_POST['csrf_token'] ?? '');
        if (!hash_equals((string) $_SESSION['csrf_token'], $csrfToken)) {
            $error = 'La solicitud no es válida.';
        } else {
            try {
                $datos = [
                    'cliente' => trim((string) ($_POST['cliente'] ?? '')),
                    'periodo' => trim((string) ($_POST['periodo'] ?? '')),
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
                if ($datos['cliente'] === '' || $datos['periodo'] === '') {
                    $error = 'Cliente y periodo son requeridos';
                } elseif ($datos['monto'] <= 0) {
                    $error = 'El monto debe ser mayor a cero';
                } else {
                    // Llamar al servicio de devolución COOPAM
                    require __DIR__ . '/../src/Services/DevolucionCoopamServicio.php';
                    $servicio = new \Sacs\Services\DevolucionCoopamServicio(Database::connection());
                    $resultado = $servicio->ejecutarDevolucionCoopam($datos, $datosContables);

                    if ($resultado['exito']) {
                        // Redirigir al detalle del movimiento creado
                        header('Location: index.php?module=caja&movement=' . $resultado['codigo_movimiento']);
                        exit;
                    } else {
                        $error = $resultado['mensaje'] ?? 'No fue posible procesar la devolución';
                    }
                }
            } catch (Throwable $exception) {
                $error = 'Error inesperado: ' . $exception->getMessage();
            }
        }

        // Redirigir al formulario con error
        $query = http_build_query(['module' => 'caja', 'action' => 'devolucion_coopam_form', 'client' => $datos['cliente'], 'periodo' => $datos['periodo'], 'error' => $error]);
        header('Location: index.php?' . $query);
        exit;
    }

    // Procesar POST de cancelación AEE (Operación 6)
    if (($_POST['action'] ?? '') === 'cancelacion_aee' && $_SERVER['REQUEST_METHOD'] === 'POST') {
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
                if ($datos['cliente'] === '' || $datos['prestamo'] === '') {
                    $error = 'Cliente y préstamo son requeridos';
                } else {
                    // Llamar al servicio de cancelación AEE
                    require __DIR__ . '/../src/Services/CancelacionAeeServicio.php';
                    $servicio = new \Sacs\Services\CancelacionAeeServicio(Database::connection());
                    $resultado = $servicio->ejecutarCancelacionAee($datos, $datosContables);

                    if ($resultado['exito']) {
                        // Redirigir al detalle del movimiento creado
                        header('Location: index.php?module=caja&movement=' . $resultado['codigo_movimiento']);
                        exit;
                    } else {
                        $error = $resultado['mensaje'] ?? 'No fue posible procesar la cancelación';
                    }
                }
            } catch (Throwable $exception) {
                $error = 'Error inesperado: ' . $exception->getMessage();
            }
        }

        // Redirigir al formulario con error
        $query = http_build_query(['module' => 'caja', 'action' => 'cancelacion_aee_form', 'client' => $datos['cliente'], 'loan' => $datos['prestamo'], 'error' => $error]);
        header('Location: index.php?' . $query);
        exit;
    }

    // Procesar POST de pago de rifa (Operación 12)
    if (($_POST['action'] ?? '') === 'pago_rifa' && $_SERVER['REQUEST_METHOD'] === 'POST') {
        $error = null;

        // Validar CSRF
        $csrfToken = (string) ($_POST['csrf_token'] ?? '');
        if (!hash_equals((string) $_SESSION['csrf_token'], $csrfToken)) {
            $error = 'La solicitud no es válida.';
        } else {
            try {
                $datos = [
                    'cliente' => trim((string) ($_POST['cliente'] ?? '')),
                    'periodo' => trim((string) ($_POST['periodo'] ?? '')),
                    'monto' => trim((string) ($_POST['monto'] ?? '')),
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
                if ($datos['cliente'] === '' || $datos['monto'] === '') {
                    $error = 'Cliente y monto son requeridos';
                } else {
                    // Llamar al servicio de pago de rifa
                    require __DIR__ . '/../src/Services/PagoRifaServicio.php';
                    $servicio = new \Sacs\Services\PagoRifaServicio(Database::connection());
                    $resultado = $servicio->ejecutarPagoRifa($datos, $datosContables);

                    if ($resultado['exito']) {
                        // Redirigir al detalle del movimiento creado
                        header('Location: index.php?module=caja&movement=' . $resultado['codigo_movimiento']);
                        exit;
                    } else {
                        $error = $resultado['mensaje'] ?? 'No fue posible procesar el pago de rifa';
                    }
                }
            } catch (Throwable $exception) {
                $error = 'Error inesperado: ' . $exception->getMessage();
            }
        }

        // Redirigir al formulario con error
        $query = http_build_query(['module' => 'caja', 'action' => 'pago_rifa_form', 'client' => $datos['cliente'], 'periodo' => $datos['periodo'], 'error' => $error]);
        header('Location: index.php?' . $query);
        exit;
    }

    // Procesar POST de pago de sobregiro FONBES (Operación 14)
    if (($_POST['action'] ?? '') === 'pago_sobregiro_fonbes' && $_SERVER['REQUEST_METHOD'] === 'POST') {
        $error = null;

        // Validar CSRF
        $csrfToken = (string) ($_POST['csrf_token'] ?? '');
        if (!hash_equals((string) $_SESSION['csrf_token'], $csrfToken)) {
            $error = 'La solicitud no es válida.';
        } else {
            try {
                $datos = [
                    'cliente' => trim((string) ($_POST['cliente'] ?? '')),
                    'monto' => trim((string) ($_POST['monto'] ?? '')),
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
                if ($datos['cliente'] === '' || $datos['monto'] === '') {
                    $error = 'Cliente y monto son requeridos';
                } else {
                    // Llamar al servicio de pago de sobregiro FONBES
                    require __DIR__ . '/../src/Services/PagoSobregiroFonbesServicio.php';
                    $servicio = new \Sacs\Services\PagoSobregiroFonbesServicio(Database::connection());
                    $resultado = $servicio->ejecutarPagoSobregiroFonbes($datos, $datosContables);

                    if ($resultado['exito']) {
                        // Redirigir al detalle del movimiento creado
                        header('Location: index.php?module=caja&movement=' . $resultado['codigo_movimiento']);
                        exit;
                    } else {
                        $error = $resultado['mensaje'] ?? 'No fue posible procesar el pago de sobregiro FONBES';
                    }
                }
            } catch (Throwable $exception) {
                $error = 'Error inesperado: ' . $exception->getMessage();
            }
        }

        // Redirigir al formulario con error
        $query = http_build_query(['module' => 'caja', 'action' => 'pago_sobregiro_fonbes_form', 'client' => $datos['cliente'], 'error' => $error]);
        header('Location: index.php?' . $query);
        exit;
    }

    // Procesar POST de pago de sobregiro COOPAM (Operación 15)
    if (($_POST['action'] ?? '') === 'pago_sobregiro_coopam' && $_SERVER['REQUEST_METHOD'] === 'POST') {
        $error = null;

        // Validar CSRF
        $csrfToken = (string) ($_POST['csrf_token'] ?? '');
        if (!hash_equals((string) $_SESSION['csrf_token'], $csrfToken)) {
            $error = 'La solicitud no es válida.';
        } else {
            try {
                $datos = [
                    'cliente' => trim((string) ($_POST['cliente'] ?? '')),
                    'monto' => trim((string) ($_POST['monto'] ?? '')),
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
                if ($datos['cliente'] === '' || $datos['monto'] === '') {
                    $error = 'Cliente y monto son requeridos';
                } else {
                    // Llamar al servicio de pago de sobregiro COOPAM
                    require __DIR__ . '/../src/Services/PagoSobregiroCoopamServicio.php';
                    $servicio = new \Sacs\Services\PagoSobregiroCoopamServicio(Database::connection());
                    $resultado = $servicio->ejecutarPagoSobregiroCoopam($datos, $datosContables);

                    if ($resultado['exito']) {
                        // Redirigir al detalle del movimiento creado
                        header('Location: index.php?module=caja&movement=' . $resultado['codigo_movimiento']);
                        exit;
                    } else {
                        $error = $resultado['mensaje'] ?? 'No fue posible procesar el pago de sobregiro COOPAM';
                    }
                }
            } catch (Throwable $exception) {
                $error = 'Error inesperado: ' . $exception->getMessage();
            }
        }

        // Redirigir al formulario con error
        $query = http_build_query(['module' => 'caja', 'action' => 'pago_sobregiro_coopam_form', 'client' => $datos['cliente'], 'error' => $error]);
        header('Location: index.php?' . $query);
        exit;
    }

    // Procesar POST de otras devoluciones (Operación 13)
    if (($_POST['action'] ?? '') === 'otras_devoluciones' && $_SERVER['REQUEST_METHOD'] === 'POST') {
        $error = null;

        // Validar CSRF
        $csrfToken = (string) ($_POST['csrf_token'] ?? '');
        if (!hash_equals((string) $_SESSION['csrf_token'], $csrfToken)) {
            $error = 'La solicitud no es válida.';
        } else {
            try {
                $datos = [
                    'cliente' => trim((string) ($_POST['cliente'] ?? '')),
                    'monto' => trim((string) ($_POST['monto'] ?? '')),
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
                if ($datos['cliente'] === '' || $datos['monto'] === '') {
                    $error = 'Cliente y monto son requeridos';
                } else {
                    // Llamar al servicio de otras devoluciones
                    require __DIR__ . '/../src/Services/OtrasDevolucionesServicio.php';
                    $servicio = new \Sacs\Services\OtrasDevolucionesServicio(Database::connection());
                    $resultado = $servicio->ejecutarOtraDevolucion($datos, $datosContables);

                    if ($resultado['exito']) {
                        // Redirigir al detalle del movimiento creado
                        header('Location: index.php?module=caja&movement=' . $resultado['codigo_movimiento']);
                        exit;
                    } else {
                        $error = $resultado['mensaje'] ?? 'No fue posible procesar la otra devolución';
                    }
                }
            } catch (Throwable $exception) {
                $error = 'Error inesperado: ' . $exception->getMessage();
            }
        }

        // Redirigir al formulario con error
        $query = http_build_query(['module' => 'caja', 'action' => 'otras_devoluciones_form', 'client' => $datos['cliente'], 'error' => $error]);
        header('Location: index.php?' . $query);
        exit;
    }

    // Procesar POST de otros ingresos (Operación 16)
    if (($_POST['action'] ?? '') === 'otros_ingresos' && $_SERVER['REQUEST_METHOD'] === 'POST') {
        $error = null;

        // Validar CSRF
        $csrfToken = (string) ($_POST['csrf_token'] ?? '');
        if (!hash_equals((string) $_SESSION['csrf_token'], $csrfToken)) {
            $error = 'La solicitud no es válida.';
        } else {
            try {
                $datos = [
                    'cliente' => trim((string) ($_POST['cliente'] ?? '')),
                    'monto' => trim((string) ($_POST['monto'] ?? '')),
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
                if ($datos['cliente'] === '' || $datos['monto'] === '') {
                    $error = 'Cliente y monto son requeridos';
                } else {
                    // Llamar al servicio de otros ingresos
                    require __DIR__ . '/../src/Services/OtrosIngresosServicio.php';
                    $servicio = new \Sacs\Services\OtrosIngresosServicio(Database::connection());
                    $resultado = $servicio->ejecutarOtroIngreso($datos, $datosContables);

                    if ($resultado['exito']) {
                        // Redirigir al detalle del movimiento creado
                        header('Location: index.php?module=caja&movement=' . $resultado['codigo_movimiento']);
                        exit;
                    } else {
                        $error = $resultado['mensaje'] ?? 'No fue posible procesar el otro ingreso';
                    }
                }
            } catch (Throwable $exception) {
                $error = 'Error inesperado: ' . $exception->getMessage();
            }
        }

        // Redirigir al formulario con error
        $query = http_build_query(['module' => 'caja', 'action' => 'otros_ingresos_form', 'client' => $datos['cliente'], 'error' => $error]);
        header('Location: index.php?' . $query);
        exit;
    }

    // Procesar POST de desembolso de beneficio (Operación 7)
    if (($_POST['action'] ?? '') === 'desembolso_beneficio' && $_SERVER['REQUEST_METHOD'] === 'POST') {
        $error = null;

        // Validar CSRF
        $csrfToken = (string) ($_POST['csrf_token'] ?? '');
        if (!hash_equals((string) $_SESSION['csrf_token'], $csrfToken)) {
            $error = 'La solicitud no es válida.';
        } else {
            try {
                $datos = [
                    'cliente' => trim((string) ($_POST['cliente'] ?? '')),
                    'beneficioId' => trim((string) ($_POST['beneficioId'] ?? '')),
                    'monto' => trim((string) ($_POST['monto'] ?? '')),
                    'formaPago' => trim((string) ($_POST['formaPago'] ?? '')),
                    'cheque' => trim((string) ($_POST['cheque'] ?? '')),
                    'montoLetras' => trim((string) ($_POST['montoLetras'] ?? '')),
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
                if ($datos['cliente'] === '' || $datos['beneficioId'] === '' || $datos['monto'] === '') {
                    $error = 'Cliente, beneficio y monto son requeridos';
                } else {
                    // Llamar al servicio de desembolso de beneficios
                    require __DIR__ . '/../src/Services/DesembolsoBeneficiosServicio.php';
                    $servicio = new \Sacs\Services\DesembolsoBeneficiosServicio(Database::connection());
                    $resultado = $servicio->ejecutarDesembolsoBeneficio($datos, $datosContables);

                    if ($resultado['exito']) {
                        // Redirigir al detalle del movimiento creado
                        header('Location: index.php?module=caja&movement=' . $resultado['codigo_movimiento']);
                        exit;
                    } else {
                        $error = $resultado['mensaje'] ?? 'No fue posible procesar el desembolso de beneficio';
                    }
                }
            } catch (Throwable $exception) {
                $error = 'Error inesperado: ' . $exception->getMessage();
            }
        }

        // Redirigir al formulario con error
        $query = http_build_query(['module' => 'caja', 'action' => 'desembolso_beneficio_form', 'client' => $datos['cliente'], 'benefit' => $datos['beneficioId'], 'error' => $error]);
        header('Location: index.php?' . $query);
        exit;
    }

    // Procesar POST de egresos (Operación 8)
    if (($_POST['action'] ?? '') === 'egresos' && $_SERVER['REQUEST_METHOD'] === 'POST') {
        $error = null;

        // Validar CSRF
        $csrfToken = (string) ($_POST['csrf_token'] ?? '');
        if (!hash_equals((string) $_SESSION['csrf_token'], $csrfToken)) {
            $error = 'La solicitud no es válida.';
        } else {
            try {
                $datos = [
                    'cliente' => trim((string) ($_POST['cliente'] ?? '')),
                    'monto' => trim((string) ($_POST['monto'] ?? '')),
                    'motivo' => trim((string) ($_POST['motivo'] ?? '')),
                    'favor' => trim((string) ($_POST['favor'] ?? '')),
                    'dni' => trim((string) ($_POST['dni'] ?? '')),
                    'formaPago' => trim((string) ($_POST['formaPago'] ?? '')),
                    'cheque' => trim((string) ($_POST['cheque'] ?? '')),
                    'montoLetras' => trim((string) ($_POST['montoLetras'] ?? '')),
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
                if ($datos['cliente'] === '' || $datos['monto'] === '') {
                    $error = 'Cliente y monto son requeridos';
                } else {
                    // Llamar al servicio de egresos
                    require __DIR__ . '/../src/Services/EgresosServicio.php';
                    $servicio = new \Sacs\Services\EgresosServicio(Database::connection());
                    $resultado = $servicio->ejecutarEgreso($datos, $datosContables);

                    if ($resultado['exito']) {
                        // Redirigir al detalle del movimiento creado
                        header('Location: index.php?module=caja&movement=' . $resultado['codigo_movimiento']);
                        exit;
                    } else {
                        $error = $resultado['mensaje'] ?? 'No fue posible procesar el egreso';
                    }
                }
            } catch (Throwable $exception) {
                $error = 'Error inesperado: ' . $exception->getMessage();
            }
        }

        // Redirigir al formulario con error
        $query = http_build_query(['module' => 'caja', 'action' => 'egresos_form', 'client' => $datos['cliente'], 'error' => $error]);
        header('Location: index.php?' . $query);
        exit;
    }

    // Procesar POST de amortización (independiente de parámetros GET)
    if (($_POST['action'] ?? '') === 'amortizar' && $_SERVER['REQUEST_METHOD'] === 'POST') {
        // Procesar amortización AEE
        $error = null;
        $formData = $_POST; // Guardar datos del formulario

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

                // Validar si el préstamo tiene sobregiro positivo (debe pagarse primero)
                $stmt = Database::connection()->prepare('SELECT f16_sobre FROM f16_prestamo WHERE f16_codigo = ?');
                $stmt->execute([$datos['prestamo']]);
                $prestamoData = $stmt->fetch();
                if ($prestamoData && round($prestamoData['f16_sobre'] ?? 0, 2) > 0) {
                    $error = 'El socio tiene sobregiro pendiente de pago. Debe pagar el sobregiro antes de continuar con la amortización.';
                }

                // Códigos contables por componente
                $codigosContables = [
                    'amortiza' => [
                        'codigo' => trim((string) ($_POST['amortiza_codigo'] ?? '')),
                        'tipo' => trim((string) ($_POST['amortiza_tipo'] ?? '')),
                        'monto' => $datos['amortiza']
                    ],
                    'gastosAdm' => [
                        'codigo' => trim((string) ($_POST['gastosAdm_codigo'] ?? '')),
                        'tipo' => trim((string) ($_POST['gastosAdm_tipo'] ?? '')),
                        'monto' => $datos['gastosAdm']
                    ],
                    'gastosMora' => [
                        'codigo' => trim((string) ($_POST['gastosMora_codigo'] ?? '')),
                        'tipo' => trim((string) ($_POST['gastosMora_tipo'] ?? '')),
                        'monto' => $datos['gastosMora']
                    ],
                    'otros1' => [
                        'codigo' => trim((string) ($_POST['otros1_codigo'] ?? '')),
                        'tipo' => trim((string) ($_POST['otros1_tipo'] ?? '')),
                        'monto' => $datos['otros1']
                    ],
                    'otros2' => [
                        'codigo' => trim((string) ($_POST['otros2_codigo'] ?? '')),
                        'tipo' => trim((string) ($_POST['otros2_tipo'] ?? '')),
                        'monto' => $datos['otros2']
                    ],
                    'sobregiro' => [
                        'codigo' => trim((string) ($_POST['sobregiro_codigo'] ?? '')),
                        'tipo' => trim((string) ($_POST['sobregiro_tipo'] ?? '')),
                        'monto' => $datos['sobregiro']
                    ],
                    'monto' => [
                        'codigo' => trim((string) ($_POST['monto_codigo'] ?? '')),
                        'tipo' => trim((string) ($_POST['monto_tipo'] ?? '')),
                        'monto' => $datos['monto']
                    ]
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
                    // Validar que montos > 0 tengan código contable
                    foreach ($codigosContables as $nombre => $config) {
                        if ($config['monto'] > 0) {
                            if (empty($config['codigo'])) {
                                $error = "El campo {$nombre} con monto mayor a 0 debe tener un código contable";
                                break;
                            }
                            if (empty($config['tipo'])) {
                                $error = "El campo {$nombre} con monto mayor a 0 debe tener un tipo de asiento (debe/haber)";
                                break;
                            }
                        }
                    }

                    // Validar equilibrio contable: suma de debe = suma de haber
                    if (empty($error)) {
                        $sumaDebe = 0;
                        $sumaHaber = 0;
                        foreach ($codigosContables as $config) {
                            if ($config['monto'] > 0 && !empty($config['tipo'])) {
                                if ($config['tipo'] === 'debe') {
                                    $sumaDebe += $config['monto'];
                                } elseif ($config['tipo'] === 'haber') {
                                    $sumaHaber += $config['monto'];
                                }
                            }
                        }

                        // Tolerancia pequeña para errores de punto flotante
                        if (abs($sumaDebe - $sumaHaber) > 0.01) {
                            $error = sprintf('El equilibrio contable no es correcto. Suma Debe: %.2f, Suma Haber: %.2f', $sumaDebe, $sumaHaber);
                        }
                    }
                }

                if (empty($error)) {
                    // Llamar al servicio de amortización
                    require __DIR__ . '/../src/Services/AmortizacionServicio.php';
                    $servicio = new \Sacs\Services\AmortizacionServicio(Database::connection());
                    $resultado = $servicio->ejecutarAmortizacionAEE($datos, $datosContables, $codigosContables);

                    if ($resultado['exito']) {
                        // Redirigir al detalle del movimiento creado
                        header('Location: index.php?module=caja&movement=' . $resultado['codigo_movimiento']);
                        exit;
                    } else {
                        // Verificar si el error es específico de cuenta no pagable
                        $mensajeError = $resultado['mensaje'] ?? 'No fue posible procesar la amortización';
                        if (strpos($mensajeError, 'Préstamo no encontrado') !== false || 
                            strpos($mensajeError, 'Cuota no encontrada') !== false ||
                            strpos($mensajeError, 'no está activo') !== false) {
                            // Redirigir a página específica de cuenta no pagable
                            $query = http_build_query([
                                'module' => 'caja',
                                'action' => 'cuenta_no_pagable',
                                'client' => $datos['cliente'],
                                'loan' => $datos['prestamo'],
                                'reason' => $mensajeError
                            ]);
                            header('Location: index.php?' . $query);
                            exit;
                        } else {
                            $error = $mensajeError;
                        }
                    }
                }
            } catch (Throwable $exception) {
                $error = 'Error inesperado: ' . $exception->getMessage();
            }
        }

        // Si hay error, mostrar el formulario con los datos conservados
        if ($error !== null) {
            $_SESSION['form_error'] = $error;
            $_SESSION['form_data'] = $formData;
            $query = http_build_query(['module' => 'caja', 'action' => 'amortizar_form', 'client' => $datos['cliente'], 'loan' => $datos['prestamo']]);
            header('Location: index.php?' . $query);
            exit;
        }
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

        if (($_GET['action'] ?? '') === 'aportes_combinados_form') {
            if ($clientId !== '') {
                require dirname(__DIR__) . '/views/caja/aportes_combinados.php';
                exit;
            }
            http_response_code(400);
            $error = 'Datos inválidos para el formulario de aportes combinados';
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

        if (($_GET['action'] ?? '') === 'devolucion_fonbes_form') {
            if ($clientId !== '') {
                require dirname(__DIR__) . '/views/caja/devolucion_fonbes.php';
                exit;
            }
            http_response_code(400);
            $error = 'Datos inválidos para el formulario de devolución FONBES';
            require dirname(__DIR__) . '/views/home.php';
            exit;
        }

        if (($_GET['action'] ?? '') === 'devolucion_coopam_form') {
            if ($clientId !== '') {
                require dirname(__DIR__) . '/views/caja/devolucion_coopam.php';
                exit;
            }
            http_response_code(400);
            $error = 'Datos inválidos para el formulario de devolución COOPAM';
            require dirname(__DIR__) . '/views/home.php';
            exit;
        }

        if (($_GET['action'] ?? '') === 'cancelacion_aee_form') {
            if ($clientId !== '') {
                require dirname(__DIR__) . '/views/caja/cancelacion_aee.php';
                exit;
            }
            http_response_code(400);
            $error = 'Datos inválidos para el formulario de cancelación AEE';
            require dirname(__DIR__) . '/views/home.php';
            exit;
        }

        if (($_GET['action'] ?? '') === 'pago_rifa_form') {
            if ($clientId !== '') {
                require dirname(__DIR__) . '/views/caja/pago_rifa.php';
                exit;
            }
            http_response_code(400);
            $error = 'Datos inválidos para el formulario de pago de rifa';
            require dirname(__DIR__) . '/views/home.php';
            exit;
        }

        if (($_GET['action'] ?? '') === 'pago_sobregiro_fonbes_form') {
            if ($clientId !== '') {
                require dirname(__DIR__) . '/views/caja/pago_sobregiro_fonbes.php';
                exit;
            }
            http_response_code(400);
            $error = 'Datos inválidos para el formulario de pago de sobregiro FONBES';
            require dirname(__DIR__) . '/views/home.php';
            exit;
        }

        if (($_GET['action'] ?? '') === 'pago_sobregiro_coopam_form') {
            if ($clientId !== '') {
                require dirname(__DIR__) . '/views/caja/pago_sobregiro_coopam.php';
                exit;
            }
            http_response_code(400);
            $error = 'Datos inválidos para el formulario de pago de sobregiro COOPAM';
            require dirname(__DIR__) . '/views/home.php';
            exit;
        }

        if (($_GET['action'] ?? '') === 'otras_devoluciones_form') {
            if ($clientId !== '') {
                require dirname(__DIR__) . '/views/caja/otras_devoluciones.php';
                exit;
            }
            http_response_code(400);
            $error = 'Datos inválidos para el formulario de otras devoluciones';
            require dirname(__DIR__) . '/views/home.php';
            exit;
        }

        if (($_GET['action'] ?? '') === 'otros_ingresos_form') {
            if ($clientId !== '') {
                require dirname(__DIR__) . '/views/caja/otros_ingresos.php';
                exit;
            }
            http_response_code(400);
            $error = 'Datos inválidos para el formulario de otros ingresos';
            require dirname(__DIR__) . '/views/home.php';
            exit;
        }

        if (($_GET['action'] ?? '') === 'desembolso_beneficios_form') {
            if ($clientId !== '') {
                require dirname(__DIR__) . '/views/caja/desembolso_beneficios.php';
                exit;
            }
            http_response_code(400);
            $error = 'Datos inválidos para el formulario de desembolso de beneficios';
            require dirname(__DIR__) . '/views/home.php';
            exit;
        }

        if (($_GET['action'] ?? '') === 'desembolso_beneficio_form') {
            $benefitId = filter_var($_GET['benefit'] ?? null, FILTER_VALIDATE_INT);
            if ($clientId !== '' && $benefitId !== false && $benefitId !== null) {
                require dirname(__DIR__) . '/views/caja/desembolso_beneficio.php';
                exit;
            }
            http_response_code(400);
            $error = 'Datos inválidos para el formulario de desembolso de beneficio';
            require dirname(__DIR__) . '/views/home.php';
            exit;
        }

        if (($_GET['action'] ?? '') === 'egresos_form') {
            if ($clientId !== '') {
                require dirname(__DIR__) . '/views/caja/egresos.php';
                exit;
            }
            http_response_code(400);
            $error = 'Datos inválidos para el formulario de egresos';
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

        if (($_GET['action'] ?? '') === 'cuenta_no_pagable') {
            require dirname(__DIR__) . '/views/caja/cuenta_no_pagable.php';
            exit;
        }

        $movementId = filter_var($_GET['movement'] ?? null, FILTER_VALIDATE_INT, ['options' => ['min_range' => 1]]);
        if ($movementId !== false && $movementId !== null) {
            $movement = (new CajaRepository())->findMovement($movementId);
            if ($movement === null) {
                http_response_code(404);
                $error = 'Movimiento no encontrado.';
                require dirname(__DIR__) . '/views/caja/diario_contable.php';
                exit;
            }

            require dirname(__DIR__) . '/views/caja/detalle_movimiento.php';
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
            'movement_code' => trim((string) ($_GET['movement_code'] ?? '')),
            'page' => max(1, (int) ($_GET['page'] ?? 1)),
            'per_page' => 25,
        ];
        $repository = new CajaRepository();
        $operations = $repository->operations();
        $movements = $repository->listMovements($filters);
        require dirname(__DIR__) . '/views/caja/movimientos.php';
        exit;
    }

    // Módulo Socios
    if (($_GET['module'] ?? '') === 'socios') {
        $sociosServicio = new SociosServicio();
        $action = $_GET['action'] ?? '';

        // GET: Listado de socios
        if ($action === 'listado') {
            $filtros = [
                'criterio' => trim((string) ($_GET['criterio'] ?? ''))
            ];
            $socios = $sociosServicio->listarSocios($filtros);
            require dirname(__DIR__) . '/views/socios/listado.php';
            exit;
        }

        // GET: Formulario de registro
        if ($action === 'registro') {
            $listas = $sociosServicio->obtenerListasReferencia();
            $profesiones = $listas['profesiones'] ?? [];
            $instrucciones = $listas['instrucciones'] ?? [];
            require dirname(__DIR__) . '/views/socios/registro_socio.php';
            exit;
        }

        // POST: Procesar registro
        if ($action === 'registrar' && $_SERVER['REQUEST_METHOD'] === 'POST') {
            $csrfToken = (string) ($_POST['csrf_token'] ?? '');

            if (!hash_equals((string) $_SESSION['csrf_token'], $csrfToken)) {
                $error = 'La solicitud no es válida.';
                $query = http_build_query(['module' => 'socios', 'action' => 'registro', 'error' => $error]);
                header('Location: index.php?' . $query);
                exit;
            }

            try {
                // Datos de persona
                $datosPersona = [
                    'ape_pat' => trim((string) ($_POST['ape_pat'] ?? '')),
                    'ape_mat' => trim((string) ($_POST['ape_mat'] ?? '')),
                    'nomb_pri' => trim((string) ($_POST['nomb_pri'] ?? '')),
                    'nomb_seg' => trim((string) ($_POST['nomb_seg'] ?? '')),
                    'tpo_doc' => !empty($_POST['tpo_doc']) ? (int) $_POST['tpo_doc'] : null,
                    'num_doc' => trim((string) ($_POST['num_doc'] ?? '')),
                    'sexo' => !empty($_POST['sexo']) ? (int) $_POST['sexo'] : null,
                    'fe_nac' => trim((string) ($_POST['fe_nac'] ?? '')) ?: null,
                    'stdo_civil' => !empty($_POST['stdo_civil']) ? (int) $_POST['stdo_civil'] : null,
                    'edad' => !empty($_POST['edad']) ? (int) $_POST['edad'] : null,
                    'cta_correo' => trim((string) ($_POST['cta_correo'] ?? '')),
                    'id_nacionalidad' => !empty($_POST['id_nacionalidad']) ? (int) $_POST['id_nacionalidad'] : null,
                    'id_instruccion' => !empty($_POST['id_instruccion']) ? (int) $_POST['id_instruccion'] : null,
                    'id_profesion' => !empty($_POST['id_profesion']) ? (int) $_POST['id_profesion'] : null,
                    'dpto_nac' => trim((string) ($_POST['dpto_nac'] ?? '')),
                    'prov_nac' => trim((string) ($_POST['prov_nac'] ?? '')),
                    'distri_nac' => trim((string) ($_POST['distri_nac'] ?? '')),
                    'telf_fijo' => trim((string) ($_POST['telf_fijo'] ?? '')),
                    'telf_cel' => trim((string) ($_POST['telf_cel'] ?? '')),
                    'desc_coment' => trim((string) ($_POST['desc_coment'] ?? '')),
                    'zona' => trim((string) ($_POST['zona'] ?? '')),
                    'via' => trim((string) ($_POST['via'] ?? '')),
                    'int' => trim((string) ($_POST['int'] ?? '')),
                    'ext' => trim((string) ($_POST['ext'] ?? '')),
                    'piso' => trim((string) ($_POST['piso'] ?? '')),
                    'departamento' => trim((string) ($_POST['departamento'] ?? '')),
                    'provincia' => trim((string) ($_POST['provincia'] ?? '')),
                    'distrito' => trim((string) ($_POST['distrito'] ?? '')),
                    'observacion' => trim((string) ($_POST['observacion'] ?? '')),
                    'desc_coment' => trim((string) ($_POST['desc_coment'] ?? '')),
                    'centro' => !empty($_POST['centro']) ? (int) $_POST['centro'] : null,
                    'condicion' => trim((string) ($_POST['condicion'] ?? '')),
                    'cuenta' => trim((string) ($_POST['cuenta'] ?? '')),
                    'ingreso' => !empty($_POST['ingreso']) ? (float) $_POST['ingreso'] : null,
                    'aportacion' => !empty($_POST['aportacion']) ? (float) $_POST['aportacion'] : null,
                    'saldo' => !empty($_POST['saldo']) ? (float) $_POST['saldo'] : null,
                    'documento' => trim((string) ($_POST['documento'] ?? '')),
                    'situacion' => trim((string) ($_POST['situacion'] ?? '')),
                    'fecha_retiro' => trim((string) ($_POST['fecha_retiro'] ?? '')) ?: null,
                    'fecha_falle' => trim((string) ($_POST['fecha_falle'] ?? '')) ?: null,
                    'aporta_base' => !empty($_POST['aporta_base']) ? (int) $_POST['aporta_base'] : null,
                    'aporta_sobre' => !empty($_POST['aporta_sobre']) ? (float) $_POST['aporta_sobre'] : null,
                    'sobrevi' => trim((string) ($_POST['sobrevi'] ?? '')),
                    'obs' => trim((string) ($_POST['obs'] ?? '')),
                    'fec_reingreso' => trim((string) ($_POST['fec_reingreso'] ?? '')) ?: null,
                    'aporte_cope' => !empty($_POST['aporte_cope']) ? (int) $_POST['aporte_cope'] : null,
                    'obs2' => trim((string) ($_POST['obs2'] ?? '')),
                    'titulo' => trim((string) ($_POST['titulo'] ?? '')),
                    'firma' => trim((string) ($_POST['firma'] ?? '')),
                    'carta' => trim((string) ($_POST['carta'] ?? '')),
                    'id_usua_crea' => $_SESSION['user']['username'] ?? null
                ];

                // Datos de cliente
                $datosCliente = [
                    'stdo' => 1,
                    'id_usua_crea' => $_SESSION['user']['username'] ?? null,
                    'clave' => trim((string) ($_POST['clave'] ?? '')) ?: null
                ];

                $resultado = $sociosServicio->registrarSocio($datosPersona, $datosCliente);

                $query = http_build_query(['module' => 'socios', 'action' => 'registro', 'success' => $resultado['id_cliente']]);
                header('Location: index.php?' . $query);
                exit;

            } catch (Exception $e) {
                $error = 'Error al registrar socio: ' . $e->getMessage();
                $query = http_build_query(['module' => 'socios', 'action' => 'registro', 'error' => $error]);
                header('Location: index.php?' . $query);
                exit;
            }
        }
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
