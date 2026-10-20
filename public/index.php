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

// Obtener módulo y acción
$module = trim((string) ($_GET['module'] ?? $_POST['module'] ?? ''));
$action = trim((string) ($_GET['action'] ?? $_POST['action'] ?? ''));

$error = null;
$username = '';

// Módulo Préstamos
if ($module === 'prestamos') {
    // Si es POST, tomar action solo de POST, si no, de GET
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $action = $_POST['action'] ?? '';
    } else {
        $action = $_GET['action'] ?? '';
    }

    // GET: Listado de Préstamos
    if ($action === 'listado') {
        require dirname(__DIR__) . '/views/prestamos/listado.php';
        exit;
    }

    // GET: Detalle de Préstamo
    if ($action === 'detalle') {
        // Por ahora redirigir al listado
        $query = http_build_query(['module' => 'prestamos', 'action' => 'listado']);
        header('Location: index.php?' . $query);
        exit;
    }

    // GET: Registro de Préstamo
    if ($action === 'registro') {
        require dirname(__DIR__) . '/views/prestamos/registro.php';
        exit;
    }

    // GET: Editar Préstamo
    if ($action === 'editar') {
        require dirname(__DIR__) . '/views/prestamos/editar.php';
        exit;
    }

    // GET: Aprobar Préstamo
    if ($action === 'aprobar') {
        require dirname(__DIR__) . '/views/prestamos/aprobar.php';
        exit;
    }

    // GET: Gestión de Estado
    if ($action === 'gestion_estado') {
        require dirname(__DIR__) . '/views/prestamos/gestion_estado.php';
        exit;
    }

    // GET: Cronograma
    if ($action === 'cronograma') {
        require dirname(__DIR__) . '/views/prestamos/cronograma.php';
        exit;
    }

    // GET: Resumen
    if ($action === 'resumen') {
        require dirname(__DIR__) . '/views/prestamos/resumen.php';
        exit;
    }

    // GET: Detalle Completo
    if ($action === 'detalle_completo') {
        require dirname(__DIR__) . '/views/prestamos/detalle_completo.php';
        exit;
    }

    // GET: Reporte General
    if ($action === 'reporte_general') {
        require dirname(__DIR__) . '/views/prestamos/reporte_general.php';
        exit;
    }

    // GET: Reporte Consolidado
    if ($action === 'reporte_consolidado') {
        require dirname(__DIR__) . '/views/prestamos/reporte_consolidado.php';
        exit;
    }

    // GET: Reporte por Cuota
    if ($action === 'reporte_cuota') {
        require dirname(__DIR__) . '/views/prestamos/reporte_cuota.php';
        exit;
    }

    // GET: Popup Trámite después de registrar préstamo
    if ($action === 'popup_tramite') {
        require dirname(__DIR__) . '/views/prestamos/popup_tramite.php';
        exit;
    }

    // POST: Registrar Préstamo
    if ($action === 'registrar' && $_SERVER['REQUEST_METHOD'] === 'POST') {
        $csrfToken = (string) ($_POST['csrf_token'] ?? '');
        if (!hash_equals((string) $_SESSION['csrf_token'], $csrfToken)) {
            $error = 'La solicitud no es válida.';
            $query = http_build_query(['module' => 'prestamos', 'action' => 'registro', 'error' => $error]);
            header('Location: index.php?' . $query);
            exit;
        }

        $prestamosServicio = new \Sacs\Services\PrestamosServicio();
        $usuario = $_SESSION['sacs_user']['username'] ?? 'SISTEMA';

        $datos = [
            'cliente' => trim((string) ($_POST['cliente'] ?? '')),
            'importe' => trim((string) ($_POST['importe'] ?? '')),
            'cuotas' => trim((string) ($_POST['cuotas'] ?? '')),
            'interes' => trim((string) ($_POST['interes'] ?? '')),
            'fecha_desembolso' => trim((string) ($_POST['fecha_desembolso'] ?? '')),
            'producto' => trim((string) ($_POST['producto'] ?? '')),
            'tramite' => trim((string) ($_POST['tramite'] ?? '')),
            'garante' => trim((string) ($_POST['garante'] ?? '')),
            'garante2' => trim((string) ($_POST['garante2'] ?? '')),
            'garante3' => trim((string) ($_POST['garante3'] ?? '')),
            'garante4' => trim((string) ($_POST['garante4'] ?? ''))
        ];

        $resultado = $prestamosServicio->registrarPrestamo($datos, $usuario);

        if ($resultado['success']) {
            $query = http_build_query(['module' => 'prestamos', 'action' => 'listado', 'success' => $resultado['message']]);
        } else {
            $query = http_build_query(['module' => 'prestamos', 'action' => 'registro', 'error' => $resultado['message']]);
        }

        header('Location: index.php?' . $query);
        exit;
    }

    // POST: Actualizar Préstamo
    if ($action === 'actualizar' && $_SERVER['REQUEST_METHOD'] === 'POST') {
        $csrfToken = (string) ($_POST['csrf_token'] ?? '');
        if (!hash_equals((string) $_SESSION['csrf_token'], $csrfToken)) {
            $error = 'La solicitud no es válida.';
            $query = http_build_query(['module' => 'prestamos', 'action' => 'editar', 'codigo' => $_POST['codigo'] ?? '', 'error' => $error]);
            header('Location: index.php?' . $query);
            exit;
        }

        $prestamosServicio = new \Sacs\Services\PrestamosServicio();
        $usuario = $_SESSION['sacs_user']['username'] ?? 'SISTEMA';
        $codigo = trim((string) ($_POST['codigo'] ?? ''));

        $datos = [
            'cliente' => trim((string) ($_POST['cliente'] ?? '')),
            'importe' => trim((string) ($_POST['importe'] ?? '')),
            'cuotas' => trim((string) ($_POST['cuotas'] ?? '')),
            'interes' => trim((string) ($_POST['interes'] ?? '')),
            'fecha_desembolso' => trim((string) ($_POST['fecha_desembolso'] ?? '')),
            'producto' => trim((string) ($_POST['producto'] ?? '')),
            'tramite' => trim((string) ($_POST['tramite'] ?? '')),
            'garante' => trim((string) ($_POST['garante'] ?? '')),
            'garante2' => trim((string) ($_POST['garante2'] ?? '')),
            'garante3' => trim((string) ($_POST['garante3'] ?? '')),
            'garante4' => trim((string) ($_POST['garante4'] ?? ''))
        ];

        // Verificar si se debe regenerar el cronograma
        $regenerarCronograma = isset($_POST['regenerar_cronograma']) && $_POST['regenerar_cronograma'] === '1';

        if ($regenerarCronograma) {
            $resultado = $prestamosServicio->regenerarCronograma($codigo, $datos, $usuario);
        } else {
            $resultado = $prestamosServicio->actualizarPrestamo($codigo, $datos, $usuario);
        }

        if ($resultado['success']) {
            $query = http_build_query(['module' => 'prestamos', 'action' => 'editar', 'codigo' => $codigo, 'success' => $resultado['message']]);
        } else {
            $query = http_build_query(['module' => 'prestamos', 'action' => 'editar', 'codigo' => $codigo, 'error' => $resultado['message']]);
        }

        header('Location: index.php?' . $query);
        exit;
    }

    // POST: Aprobar/Rechazar Préstamo
    if ($action === 'aprobar_accion' && $_SERVER['REQUEST_METHOD'] === 'POST') {
        $csrfToken = (string) ($_POST['csrf_token'] ?? '');
        if (!hash_equals((string) $_SESSION['csrf_token'], $csrfToken)) {
            $error = 'La solicitud no es válida.';
            $query = http_build_query(['module' => 'prestamos', 'action' => 'listado', 'error' => $error]);
            header('Location: index.php?' . $query);
            exit;
        }

        $prestamosServicio = new \Sacs\Services\PrestamosServicio();
        $usuario = $_SESSION['sacs_user']['username'] ?? 'SISTEMA';
        $codigo = trim((string) ($_POST['codigo'] ?? ''));
        $accion = trim((string) ($_POST['accion'] ?? ''));

        if ($accion === 'aprobar') {
            $resultado = $prestamosServicio->aprobarPrestamo($codigo, $usuario);
        } elseif ($accion === 'rechazar') {
            $motivo = trim((string) ($_POST['motivo'] ?? ''));
            $resultado = $prestamosServicio->rechazarPrestamo($codigo, $motivo, $usuario);
        } else {
            $resultado = ['success' => false, 'message' => 'Acción no válida'];
        }

        if ($resultado['success']) {
            $query = http_build_query(['module' => 'prestamos', 'action' => 'listado', 'success' => $resultado['message']]);
        } else {
            $query = http_build_query(['module' => 'prestamos', 'action' => 'aprobar', 'codigo' => $codigo, 'error' => $resultado['message']]);
        }

        header('Location: index.php?' . $query);
        exit;
    }

    // POST: Gestión de Estado
    if ($action === 'gestion_estado_accion' && $_SERVER['REQUEST_METHOD'] === 'POST') {
        $csrfToken = (string) ($_POST['csrf_token'] ?? '');
        if (!hash_equals((string) $_SESSION['csrf_token'], $csrfToken)) {
            $error = 'La solicitud no es válida.';
            $query = http_build_query(['module' => 'prestamos', 'action' => 'listado', 'error' => $error]);
            header('Location: index.php?' . $query);
            exit;
        }

        $prestamosServicio = new \Sacs\Services\PrestamosServicio();
        $usuario = $_SESSION['sacs_user']['username'] ?? 'SISTEMA';
        $codigo = trim((string) ($_POST['codigo'] ?? ''));
        $accion = trim((string) ($_POST['accion'] ?? ''));
        $motivo = trim((string) ($_POST['motivo'] ?? ''));

        switch ($accion) {
            case 'cancelar':
                $resultado = $prestamosServicio->cancelarPrestamo($codigo, $motivo, $usuario);
                break;
            case 'anular':
                $resultado = $prestamosServicio->anularPrestamo($codigo, $motivo, $usuario);
                break;
            case 'activar':
                $resultado = $prestamosServicio->activarPrestamo($codigo, $usuario);
                break;
            default:
                $resultado = ['success' => false, 'message' => 'Acción no válida'];
        }

        if ($resultado['success']) {
            $query = http_build_query(['module' => 'prestamos', 'action' => 'listado', 'success' => $resultado['message']]);
        } else {
            $query = http_build_query(['module' => 'prestamos', 'action' => 'gestion_estado', 'codigo' => $codigo, 'error' => $resultado['message']]);
        }

        header('Location: index.php?' . $query);
        exit;
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Solo procesar login si hay username y password (formulario de login)
    if (isset($_POST['username']) && isset($_POST['password'])) {
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

    // Procesar POST de trámites (fuera del bloque de autenticación)
    $module = trim((string) ($_POST['module'] ?? ''));
    $action = trim((string) ($_POST['action'] ?? ''));

    if ($module === 'tramites' && $action === 'registrar') {
        $tramitesServicio = new \Sacs\Services\TramitesServicio();
        $csrfToken = (string) ($_POST['csrf_token'] ?? '');
        if (!hash_equals((string) $_SESSION['csrf_token'], $csrfToken)) {
            $query = http_build_query(['module' => 'tramites', 'action' => 'registrar', 'error' => 'La solicitud no es válida.']);
            header('Location: index.php?' . $query);
            exit;
        }

        try {
            $usuario = $_SESSION['sacs_user']['username'] ?? 'SISTEMA';
            $tipo = trim((string) ($_POST['tipo'] ?? ''));

            $numero = trim((string) ($_POST['numero'] ?? ''));
            if (empty($numero) && !empty($tipo)) {
                $resultadoNumero = $tramitesServicio->generarNumeroTramite((int) $tipo);
                if ($resultadoNumero['success']) {
                    $numero = $resultadoNumero['numero'];
                } else {
                    throw new Exception('Error al generar número de trámite: ' . $resultadoNumero['message']);
                }
            }

            if (!empty($numero) && !empty($tipo) && $tramitesServicio->verificarTramiteExiste($numero, $tipo)) {
                $resultadoNumero = $tramitesServicio->generarNumeroTramite((int) $tipo);
                if ($resultadoNumero['success']) {
                    $numero = $resultadoNumero['numero'];
                } else {
                    throw new Exception('Error al generar número de trámite: ' . $resultadoNumero['message']);
                }
            }

            if (empty($numero)) {
                throw new Exception('El número de trámite es requerido');
            }

            $rutas = ['ruta' => null, 'ruta2' => null, 'ruta3' => null];
            if (isset($_FILES['archivo1']) && $_FILES['archivo1']['error'] === UPLOAD_ERR_OK) {
                $resultado = $tramitesServicio->cargarArchivo($_FILES['archivo1'], 'ruta');
                if ($resultado['success']) {
                    $rutas['ruta'] = $resultado['ruta'];
                }
            }
            if (isset($_FILES['archivo2']) && $_FILES['archivo2']['error'] === UPLOAD_ERR_OK) {
                $resultado = $tramitesServicio->cargarArchivo($_FILES['archivo2'], 'ruta2');
                if ($resultado['success']) {
                    $rutas['ruta2'] = $resultado['ruta'];
                }
            }
            if (isset($_FILES['archivo3']) && $_FILES['archivo3']['error'] === UPLOAD_ERR_OK) {
                $resultado = $tramitesServicio->cargarArchivo($_FILES['archivo3'], 'ruta3');
                if ($resultado['success']) {
                    $rutas['ruta3'] = $resultado['ruta'];
                }
            }

            $datos = [
                'numero' => $numero,
                'tipo' => $tipo,
                'descripcion' => trim((string) ($_POST['descripcion'] ?? '')),
                'fisico' => trim((string) ($_POST['fisico'] ?? 'NO')),
                'destino' => trim((string) ($_POST['destino'] ?? '')),
                'fecha' => trim((string) ($_POST['fecha'] ?? date('Y-m-d'))),
                'obs' => trim((string) ($_POST['obs'] ?? '')),
                'obs2' => trim((string) ($_POST['obs2'] ?? '')),
                'obs3' => trim((string) ($_POST['obs3'] ?? '')),
                'referencia' => trim((string) ($_POST['referencia'] ?? '')),
                'respuesta' => trim((string) ($_POST['respuesta'] ?? '')),
                'nombres' => trim((string) ($_POST['nombres'] ?? '')),
                'socio' => trim((string) ($_POST['socio'] ?? '')),
                'socio_dni' => trim((string) ($_POST['socio_dni'] ?? '')),
                'importe' => trim((string) ($_POST['importe'] ?? '')),
                'cuotas' => trim((string) ($_POST['cuotas'] ?? '')),
                'tramitante' => trim((string) ($_POST['tramitante'] ?? '')),
                'ref_documento' => trim((string) ($_POST['ref_documento'] ?? '')),
                'tramitante_cel' => trim((string) ($_POST['tramitante_cel'] ?? '')),
                'tramitante_email' => trim((string) ($_POST['tramitante_email'] ?? '')),
                'tramitante_email2' => trim((string) ($_POST['tramitante_email2'] ?? '')),
                'cuenta' => trim((string) ($_POST['cuenta'] ?? '')),
                'interno' => '1',
                'hora' => date('H:i:s'),
                'adjunto' => 'NO'
            ];

            $datos = array_merge($datos, $rutas);

            $resultado = $tramitesServicio->registrarTramite($datos, $usuario);

            if ($resultado['success']) {
                // Verificar si es modo popup
                $popupMode = ($_POST['popup'] ?? '') === '1';

                if ($popupMode) {
                    // Renderizar vista de éxito con el número de trámite
                    $_GET['tramite_numero'] = $resultado['numero'];
                    require dirname(__DIR__) . '/views/tramites/popup_success.php';
                    exit;
                } else {
                    // Redirigir al listado normal
                    $query = http_build_query(['module' => 'tramites', 'action' => 'listar', 'success' => $resultado['message']]);
                    header('Location: index.php?' . $query);
                    exit;
                }
            } else {
                $query = http_build_query(['module' => 'tramites', 'action' => 'registrar', 'error' => $resultado['message']]);
                header('Location: index.php?' . $query);
                exit;
            }

        } catch (Exception $e) {
            $query = http_build_query(['module' => 'tramites', 'action' => 'registrar', 'error' => 'Error al registrar trámite: ' . $e->getMessage()]);
            header('Location: index.php?' . $query);
            exit;
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
                    'usuario' => $user['username'] ?? 'SISTEMA',
                ];

                // Códigos contables por componente
                $codigosContables = [
                    'sobregiro' => [
                        'codigo' => trim((string) ($_POST['sobregiro_codigo'] ?? '')),
                        'tipo' => trim((string) ($_POST['sobregiro_tipo'] ?? '')),
                        'monto' => $datos['sobregiro']
                    ],
                    'amortizacion' => [
                        'codigo' => trim((string) ($_POST['amortizacion_codigo'] ?? '')),
                        'tipo' => trim((string) ($_POST['amortizacion_tipo'] ?? '')),
                        'monto' => $datos['amortizacion']
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
                    'usuario' => $user['username'] ?? 'SISTEMA',
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
                    'usuario' => $user['username'] ?? 'SISTEMA',
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
                    'usuario' => $user['username'] ?? 'SISTEMA',
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
                    'usuario' => $user['username'] ?? 'SISTEMA',
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
                    'usuario' => $user['username'] ?? 'SISTEMA',
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
                    'usuario' => $user['username'] ?? 'SISTEMA',
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
                    'usuario' => $user['username'] ?? 'SISTEMA',
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
                    'usuario' => $user['username'] ?? 'SISTEMA',
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
                    'usuario' => $user['username'] ?? 'SISTEMA',
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
                    'usuario' => $user['username'] ?? 'SISTEMA',
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
                    'usuario' => $user['username'] ?? 'SISTEMA',
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

    // Procesar POST de pago integral
    if (($_POST['action'] ?? '') === 'pago_integral' && $_SERVER['REQUEST_METHOD'] === 'POST') {
        $error = null;

        // Validar CSRF
        $csrfToken = (string) ($_POST['csrf_token'] ?? '');
        if (!hash_equals((string) $_SESSION['csrf_token'], $csrfToken)) {
            $error = 'La solicitud no es válida. CSRF token mismatch.';
        } else {
            try {
                $datos = [
                    'cliente' => trim((string) ($_POST['cliente'] ?? '')),
                    'prestamos' => $_POST['prestamos'] ?? [],
                    'formaPago' => trim((string) ($_POST['formaPago'] ?? '')),
                    'observaciones' => trim((string) ($_POST['observaciones'] ?? '')),
                ];

                // Procesar códigos contables globales
                $codigosContables = [];
                foreach (['aportesFonbes', 'aportesCoopam', 'sobregiroFonbes', 'sobregiroCoopam'] as $componente) {
                    if (!empty($_POST['codigosContables'][$componente]['codigo'])) {
                        $codigosContables[$componente] = [
                            'codigoContable' => trim((string) $_POST['codigosContables'][$componente]['codigo']),
                            'tipoAsiento' => trim((string) ($_POST['codigosContables'][$componente]['tipo'] ?? 'HABER')),
                            'descripcionContable' => trim((string) ($_POST['descripcionContable'] ?? '')),
                        ];
                    }
                }

                // Procesar códigos contables por préstamo
                if (!empty($_POST['codigosContables']['prestamos'])) {
                    $codigosContables['prestamos'] = [];
                    foreach ($_POST['codigosContables']['prestamos'] as $prestamoCodigo => $codigos) {
                        $codigosContables['prestamos'][$prestamoCodigo] = [];
                        foreach (['sobregiro', 'amortizacion'] as $componente) {
                            if (!empty($codigos[$componente]['codigo'])) {
                                $codigosContables['prestamos'][$prestamoCodigo][$componente] = [
                                    'codigoContable' => trim((string) $codigos[$componente]['codigo']),
                                    'tipoAsiento' => trim((string) ($codigos[$componente]['tipo'] ?? 'HABER')),
                                    'descripcionContable' => trim((string) ($_POST['descripcionContable'] ?? '')),
                                ];
                            }
                        }
                    }
                }

                // Validaciones básicas
                if ($datos['cliente'] === '' || empty($datos['prestamos'])) {
                    $error = 'Cliente y préstamos son requeridos';
                } else {
                    // Llamar al servicio de pago integral
                    require __DIR__ . '/../src/Services/PagoIntegralServicio.php';
                    $servicio = new \Sacs\Services\PagoIntegralServicio();
                    $resultado = $servicio->ejecutarPagoIntegral($datos, $codigosContables);

                    if ($resultado['exito']) {
                        // Redirigir al perfil del cliente con mensaje de éxito
                        $query = http_build_query([
                            'module' => 'caja',
                            'profile' => 'client',
                            'client' => $datos['cliente'],
                            'success' => 'Pago integral procesado correctamente. ' . count($resultado['movimientos']) . ' movimientos generados.'
                        ]);
                        header('Location: index.php?' . $query);
                        exit;
                    } else {
                        $error = $resultado['mensaje'] ?? 'No fue posible procesar el pago integral';
                    }
                }
            } catch (Throwable $exception) {
                $error = 'Error inesperado: ' . $exception->getMessage();
            }
        }

        // Redirigir al formulario con error
        $prestamosParam = implode('&prestamos[]=', $datos['prestamos']);
        $query = http_build_query(['module' => 'caja', 'action' => 'pago_integral_form', 'client' => $datos['cliente'], 'error' => $error]);
        if (!empty($datos['prestamos'])) {
            $query .= '&prestamos[]=' . implode('&prestamos[]=', $datos['prestamos']);
        }
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

// Módulo Tarifarios
if ($module === 'tarifarios') {
    // GET: Listado de Tarifarios
    if ($action === 'listado') {
        require dirname(__DIR__) . '/views/tarifarios/listado.php';
        exit;
    }

    // GET: Registro de Tarifario
    if ($action === 'registro') {
        require dirname(__DIR__) . '/views/tarifarios/registro.php';
        exit;
    }

    // POST: Registrar Tarifario
    if ($action === 'registrar' && $_SERVER['REQUEST_METHOD'] === 'POST') {
        $csrfToken = (string) ($_POST['csrf_token'] ?? '');
        if (!hash_equals((string) $_SESSION['csrf_token'], $csrfToken)) {
            $error = 'La solicitud no es válida.';
            $query = http_build_query(['module' => 'tarifarios', 'action' => 'registro', 'error' => $error]);
            header('Location: index.php?' . $query);
            exit;
        }

        $tarifariosServicio = new \Sacs\Services\TarifariosServicio(\Sacs\Core\Database::connection());

        $datos = [
            'codigo' => (int) ($_POST['codigo'] ?? 0),
            'concepto' => trim((string) ($_POST['concepto'] ?? '')),
            'valor' => trim((string) ($_POST['valor'] ?? '')),
            'fecha_desde' => trim((string) ($_POST['fecha_desde'] ?? '')),
            'fecha_hasta' => trim((string) ($_POST['fecha_hasta'] ?? '')),
            'producto' => trim((string) ($_POST['producto'] ?? ''))
        ];

        // Validar campos requeridos
        if (empty($datos['codigo'])) {
            $error = 'El código es requerido';
            $query = http_build_query(['module' => 'tarifarios', 'action' => 'registro', 'error' => $error]);
            header('Location: index.php?' . $query);
            exit;
        }

        if (empty($datos['concepto'])) {
            $error = 'El concepto es requerido';
            $query = http_build_query(['module' => 'tarifarios', 'action' => 'registro', 'error' => $error]);
            header('Location: index.php?' . $query);
            exit;
        }

        if (empty($datos['valor'])) {
            $error = 'El valor es requerido';
            $query = http_build_query(['module' => 'tarifarios', 'action' => 'registro', 'error' => $error]);
            header('Location: index.php?' . $query);
            exit;
        }

        if (empty($datos['fecha_desde'])) {
            $error = 'La fecha desde es requerida';
            $query = http_build_query(['module' => 'tarifarios', 'action' => 'registro', 'error' => $error]);
            header('Location: index.php?' . $query);
            exit;
        }

        if (empty($datos['producto'])) {
            $error = 'El producto es requerido';
            $query = http_build_query(['module' => 'tarifarios', 'action' => 'registro', 'error' => $error]);
            header('Location: index.php?' . $query);
            exit;
        }

        $resultado = $tarifariosServicio->registrarTarifario($datos);

        if ($resultado['success']) {
            $query = http_build_query(['module' => 'tarifarios', 'action' => 'registro', 'success' => $resultado['message']]);
        } else {
            $query = http_build_query(['module' => 'tarifarios', 'action' => 'registro', 'error' => $resultado['message']]);
        }

        header('Location: index.php?' . $query);
        exit;
    }

    // GET: Edición de Tarifario
    if ($action === 'editar') {
        require dirname(__DIR__) . '/views/tarifarios/editar.php';
        exit;
    }

    // POST: Actualizar Tarifario
    if ($action === 'actualizar' && $_SERVER['REQUEST_METHOD'] === 'POST') {
        $csrfToken = (string) ($_POST['csrf_token'] ?? '');
        if (!hash_equals((string) $_SESSION['csrf_token'], $csrfToken)) {
            $error = 'La solicitud no es válida.';
            $query = http_build_query(['module' => 'tarifarios', 'action' => 'editar', 'codigo' => $_POST['codigo'] ?? 0, 'error' => $error]);
            header('Location: index.php?' . $query);
            exit;
        }

        $tarifariosServicio = new \Sacs\Services\TarifariosServicio(\Sacs\Core\Database::connection());

        $codigo = (int) ($_POST['codigo'] ?? 0);
        $datos = [
            'concepto' => trim((string) ($_POST['concepto'] ?? '')),
            'valor' => trim((string) ($_POST['valor'] ?? '')),
            'fecha_desde' => trim((string) ($_POST['fecha_desde'] ?? '')),
            'fecha_hasta' => trim((string) ($_POST['fecha_hasta'] ?? '')),
            'producto' => trim((string) ($_POST['producto'] ?? '')),
            'estado' => trim((string) ($_POST['estado'] ?? 'A'))
        ];

        // Validar campos requeridos
        if (empty($datos['concepto'])) {
            $error = 'El concepto es requerido';
            $query = http_build_query(['module' => 'tarifarios', 'action' => 'editar', 'codigo' => $codigo, 'error' => $error]);
            header('Location: index.php?' . $query);
            exit;
        }

        if (empty($datos['valor'])) {
            $error = 'El valor es requerido';
            $query = http_build_query(['module' => 'tarifarios', 'action' => 'editar', 'codigo' => $codigo, 'error' => $error]);
            header('Location: index.php?' . $query);
            exit;
        }

        if (empty($datos['fecha_desde'])) {
            $error = 'La fecha desde es requerida';
            $query = http_build_query(['module' => 'tarifarios', 'action' => 'editar', 'codigo' => $codigo, 'error' => $error]);
            header('Location: index.php?' . $query);
            exit;
        }

        if (empty($datos['producto'])) {
            $error = 'El producto es requerido';
            $query = http_build_query(['module' => 'tarifarios', 'action' => 'editar', 'codigo' => $codigo, 'error' => $error]);
            header('Location: index.php?' . $query);
            exit;
        }

        $resultado = $tarifariosServicio->actualizarTarifario($codigo, $datos);

        if ($resultado['success']) {
            $query = http_build_query(['module' => 'tarifarios', 'action' => 'editar', 'codigo' => $codigo, 'success' => $resultado['message']]);
        } else {
            $query = http_build_query(['module' => 'tarifarios', 'action' => 'editar', 'codigo' => $codigo, 'error' => $resultado['message']]);
        }

        header('Location: index.php?' . $query);
        exit;
    }

    // GET: Eliminación de Tarifario
    if ($action === 'eliminar') {
        require dirname(__DIR__) . '/views/tarifarios/eliminar.php';
        exit;
    }

    // POST: Eliminar Tarifario
    if ($action === 'eliminar' && $_SERVER['REQUEST_METHOD'] === 'POST') {
        $csrfToken = (string) ($_POST['csrf_token'] ?? '');
        if (!hash_equals((string) $_SESSION['csrf_token'], $csrfToken)) {
            $error = 'La solicitud no es válida.';
            $query = http_build_query(['module' => 'tarifarios', 'action' => 'eliminar', 'codigo' => $_POST['codigo'] ?? 0, 'error' => $error]);
            header('Location: index.php?' . $query);
            exit;
        }

        $tarifariosServicio = new \Sacs\Services\TarifariosServicio(\Sacs\Core\Database::connection());

        $codigo = (int) ($_POST['codigo'] ?? 0);
        $resultado = $tarifariosServicio->eliminarTarifario($codigo);

        if ($resultado['success']) {
            $query = http_build_query(['module' => 'tarifarios', 'action' => 'listado', 'success' => $resultado['message']]);
        } else {
            $query = http_build_query(['module' => 'tarifarios', 'action' => 'eliminar', 'codigo' => $codigo, 'error' => $resultado['message']]);
        }

        header('Location: index.php?' . $query);
        exit;
    }
}

// Módulo Productos
if ($module === 'productos') {
    // GET: Listado de Productos
    if ($action === 'listado') {
        require dirname(__DIR__) . '/views/productos/listado.php';
        exit;
    }

    // GET: Registro de Producto
    if ($action === 'registro') {
        require dirname(__DIR__) . '/views/productos/registro.php';
        exit;
    }

    // POST: Registrar Producto
    if ($action === 'registrar' && $_SERVER['REQUEST_METHOD'] === 'POST') {
        $csrfToken = (string) ($_POST['csrf_token'] ?? '');
        if (!hash_equals((string) $_SESSION['csrf_token'], $csrfToken)) {
            $error = 'La solicitud no es válida.';
            $query = http_build_query(['module' => 'productos', 'action' => 'registro', 'error' => $error]);
            header('Location: index.php?' . $query);
            exit;
        }

        $productosServicio = new \Sacs\Services\ProductosServicio(\Sacs\Core\Database::connection());

        $datos = [
            'codigo' => (int) ($_POST['codigo'] ?? 0),
            'nombre' => trim((string) ($_POST['nombre'] ?? '')),
            'cronograma' => trim((string) ($_POST['cronograma'] ?? '')),
            'moneda' => trim((string) ($_POST['moneda'] ?? '')),
            'max_cuotas' => trim((string) ($_POST['max_cuotas'] ?? '')),
            'monto_min' => trim((string) ($_POST['monto_min'] ?? '')),
            'monto_max' => trim((string) ($_POST['monto_max'] ?? '')),
            'con_garante' => trim((string) ($_POST['con_garante'] ?? 'N'))
        ];

        // Validar campos requeridos
        if (empty($datos['codigo'])) {
            $error = 'El código es requerido';
            $query = http_build_query(['module' => 'productos', 'action' => 'registro', 'error' => $error]);
            header('Location: index.php?' . $query);
            exit;
        }

        if (empty($datos['nombre'])) {
            $error = 'El nombre es requerido';
            $query = http_build_query(['module' => 'productos', 'action' => 'registro', 'error' => $error]);
            header('Location: index.php?' . $query);
            exit;
        }

        if (empty($datos['cronograma'])) {
            $error = 'El cronograma es requerido';
            $query = http_build_query(['module' => 'productos', 'action' => 'registro', 'error' => $error]);
            header('Location: index.php?' . $query);
            exit;
        }

        if (empty($datos['moneda'])) {
            $error = 'La moneda es requerida';
            $query = http_build_query(['module' => 'productos', 'action' => 'registro', 'error' => $error]);
            header('Location: index.php?' . $query);
            exit;
        }

        if (empty($datos['max_cuotas'])) {
            $error = 'El máximo de cuotas es requerido';
            $query = http_build_query(['module' => 'productos', 'action' => 'registro', 'error' => $error]);
            header('Location: index.php?' . $query);
            exit;
        }

        if (empty($datos['monto_min'])) {
            $error = 'El monto mínimo es requerido';
            $query = http_build_query(['module' => 'productos', 'action' => 'registro', 'error' => $error]);
            header('Location: index.php?' . $query);
            exit;
        }

        if (empty($datos['monto_max'])) {
            $error = 'El monto máximo es requerido';
            $query = http_build_query(['module' => 'productos', 'action' => 'registro', 'error' => $error]);
            header('Location: index.php?' . $query);
            exit;
        }

        $resultado = $productosServicio->registrarProducto($datos);

        if ($resultado['success']) {
            $query = http_build_query(['module' => 'productos', 'action' => 'registro', 'success' => $resultado['message']]);
        } else {
            $query = http_build_query(['module' => 'productos', 'action' => 'registro', 'error' => $resultado['message']]);
        }

        header('Location: index.php?' . $query);
        exit;
    }

    // GET: Edición de Producto
    if ($action === 'editar') {
        require dirname(__DIR__) . '/views/productos/editar.php';
        exit;
    }

    // POST: Actualizar Producto
    if ($action === 'actualizar' && $_SERVER['REQUEST_METHOD'] === 'POST') {
        $csrfToken = (string) ($_POST['csrf_token'] ?? '');
        if (!hash_equals((string) $_SESSION['csrf_token'], $csrfToken)) {
            $error = 'La solicitud no es válida.';
            $query = http_build_query(['module' => 'productos', 'action' => 'editar', 'codigo' => $_POST['codigo'] ?? 0, 'error' => $error]);
            header('Location: index.php?' . $query);
            exit;
        }

        $productosServicio = new \Sacs\Services\ProductosServicio(\Sacs\Core\Database::connection());

        $codigo = (int) ($_POST['codigo'] ?? 0);
        $datos = [
            'nombre' => trim((string) ($_POST['nombre'] ?? '')),
            'cronograma' => trim((string) ($_POST['cronograma'] ?? '')),
            'moneda' => trim((string) ($_POST['moneda'] ?? '')),
            'max_cuotas' => trim((string) ($_POST['max_cuotas'] ?? '')),
            'monto_min' => trim((string) ($_POST['monto_min'] ?? '')),
            'monto_max' => trim((string) ($_POST['monto_max'] ?? '')),
            'con_garante' => trim((string) ($_POST['con_garante'] ?? 'N')),
            'estado' => trim((string) ($_POST['estado'] ?? 'A'))
        ];

        // Validar campos requeridos
        if (empty($datos['nombre'])) {
            $error = 'El nombre es requerido';
            $query = http_build_query(['module' => 'productos', 'action' => 'editar', 'codigo' => $codigo, 'error' => $error]);
            header('Location: index.php?' . $query);
            exit;
        }

        if (empty($datos['cronograma'])) {
            $error = 'El cronograma es requerido';
            $query = http_build_query(['module' => 'productos', 'action' => 'editar', 'codigo' => $codigo, 'error' => $error]);
            header('Location: index.php?' . $query);
            exit;
        }

        if (empty($datos['moneda'])) {
            $error = 'La moneda es requerida';
            $query = http_build_query(['module' => 'productos', 'action' => 'editar', 'codigo' => $codigo, 'error' => $error]);
            header('Location: index.php?' . $query);
            exit;
        }

        if (empty($datos['max_cuotas'])) {
            $error = 'El máximo de cuotas es requerido';
            $query = http_build_query(['module' => 'productos', 'action' => 'editar', 'codigo' => $codigo, 'error' => $error]);
            header('Location: index.php?' . $query);
            exit;
        }

        if (empty($datos['monto_min'])) {
            $error = 'El monto mínimo es requerido';
            $query = http_build_query(['module' => 'productos', 'action' => 'editar', 'codigo' => $codigo, 'error' => $error]);
            header('Location: index.php?' . $query);
            exit;
        }

        if (empty($datos['monto_max'])) {
            $error = 'El monto máximo es requerido';
            $query = http_build_query(['module' => 'productos', 'action' => 'editar', 'codigo' => $codigo, 'error' => $error]);
            header('Location: index.php?' . $query);
            exit;
        }

        $resultado = $productosServicio->actualizarProducto($codigo, $datos);

        if ($resultado['success']) {
            $query = http_build_query(['module' => 'productos', 'action' => 'editar', 'codigo' => $codigo, 'success' => $resultado['message']]);
        } else {
            $query = http_build_query(['module' => 'productos', 'action' => 'editar', 'codigo' => $codigo, 'error' => $resultado['message']]);
        }

        header('Location: index.php?' . $query);
        exit;
    }

    // GET: Eliminación de Producto
    if ($action === 'eliminar') {
        require dirname(__DIR__) . '/views/productos/eliminar.php';
        exit;
    }

    // POST: Eliminar Producto
    if ($action === 'eliminar' && $_SERVER['REQUEST_METHOD'] === 'POST') {
        $csrfToken = (string) ($_POST['csrf_token'] ?? '');
        if (!hash_equals((string) $_SESSION['csrf_token'], $csrfToken)) {
            $error = 'La solicitud no es válida.';
            $query = http_build_query(['module' => 'productos', 'action' => 'eliminar', 'codigo' => $_POST['codigo'] ?? 0, 'error' => $error]);
            header('Location: index.php?' . $query);
            exit;
        }

        $productosServicio = new \Sacs\Services\ProductosServicio(\Sacs\Core\Database::connection());

        $codigo = (int) ($_POST['codigo'] ?? 0);
        $resultado = $productosServicio->eliminarProducto($codigo);

        if ($resultado['success']) {
            $query = http_build_query(['module' => 'productos', 'action' => 'listado', 'success' => $resultado['message']]);
        } else {
            $query = http_build_query(['module' => 'productos', 'action' => 'eliminar', 'codigo' => $codigo, 'error' => $resultado['message']]);
        }

        header('Location: index.php?' . $query);
        exit;
    }
}

// Módulo Tarifas de Aportes
if ($module === 'tarifas_aportes') {
    // GET: Listado de Tarifas de Aportes
    if ($action === 'listado') {
        require dirname(__DIR__) . '/views/tarifas_aportes/listado.php';
        exit;
    }

    // GET: Registro de Tarifa de Aporte
    if ($action === 'registro') {
        require dirname(__DIR__) . '/views/tarifas_aportes/registro.php';
        exit;
    }

    // POST: Registrar Tarifa de Aporte
    if ($action === 'registrar' && $_SERVER['REQUEST_METHOD'] === 'POST') {
        $csrfToken = (string) ($_POST['csrf_token'] ?? '');
        if (!hash_equals((string) $_SESSION['csrf_token'], $csrfToken)) {
            $error = 'La solicitud no es válida.';
            $query = http_build_query(['module' => 'tarifas_aportes', 'action' => 'registro', 'error' => $error]);
            header('Location: index.php?' . $query);
            exit;
        }

        $tarifariosServicio = new \Sacs\Services\TarifariosServicio(\Sacs\Core\Database::connection());

        $datos = [
            'codigo' => (int) ($_POST['codigo'] ?? 0),
            'concepto' => trim((string) ($_POST['concepto'] ?? '')),
            'valor' => trim((string) ($_POST['valor'] ?? '')),
            'fecha_desde' => trim((string) ($_POST['fecha_desde'] ?? '')),
            'fecha_hasta' => trim((string) ($_POST['fecha_hasta'] ?? '')),
            'producto' => null
        ];

        // Validar campos requeridos
        if (empty($datos['codigo'])) {
            $error = 'El código es requerido';
            $query = http_build_query(['module' => 'tarifas_aportes', 'action' => 'registro', 'error' => $error]);
            header('Location: index.php?' . $query);
            exit;
        }

        if (empty($datos['concepto'])) {
            $error = 'El concepto es requerido';
            $query = http_build_query(['module' => 'tarifas_aportes', 'action' => 'registro', 'error' => $error]);
            header('Location: index.php?' . $query);
            exit;
        }

        if (empty($datos['valor'])) {
            $error = 'El valor es requerido';
            $query = http_build_query(['module' => 'tarifas_aportes', 'action' => 'registro', 'error' => $error]);
            header('Location: index.php?' . $query);
            exit;
        }

        if (empty($datos['fecha_desde'])) {
            $error = 'La fecha desde es requerida';
            $query = http_build_query(['module' => 'tarifas_aportes', 'action' => 'registro', 'error' => $error]);
            header('Location: index.php?' . $query);
            exit;
        }

        $resultado = $tarifariosServicio->registrarTarifario($datos);

        if ($resultado['success']) {
            $_SESSION['message'] = $resultado['message'];
            header('Location: index.php?module=tarifas_aportes&action=listado');
        } else {
            $_SESSION['error'] = $resultado['message'];
            header('Location: index.php?module=tarifas_aportes&action=registro');
        }
        exit;
    }

    // GET: Edición de Tarifa de Aporte
    if ($action === 'editar') {
        require dirname(__DIR__) . '/views/tarifas_aportes/editar.php';
        exit;
    }

    // POST: Actualizar Tarifa de Aporte
    if ($action === 'actualizar' && $_SERVER['REQUEST_METHOD'] === 'POST') {
        $csrfToken = (string) ($_POST['csrf_token'] ?? '');
        if (!hash_equals((string) $_SESSION['csrf_token'], $csrfToken)) {
            $error = 'La solicitud no es válida.';
            $query = http_build_query(['module' => 'tarifas_aportes', 'action' => 'editar', 'codigo' => $_POST['codigo'] ?? 0, 'error' => $error]);
            header('Location: index.php?' . $query);
            exit;
        }

        $tarifariosServicio = new \Sacs\Services\TarifariosServicio(\Sacs\Core\Database::connection());

        $datos = [
            'codigo' => (int) ($_POST['codigo'] ?? 0),
            'concepto' => trim((string) ($_POST['concepto'] ?? '')),
            'valor' => trim((string) ($_POST['valor'] ?? '')),
            'fecha_desde' => trim((string) ($_POST['fecha_desde'] ?? '')),
            'fecha_hasta' => trim((string) ($_POST['fecha_hasta'] ?? '')),
            'estado' => trim((string) ($_POST['estado'] ?? 'A')),
            'producto' => null
        ];

        // Validar campos requeridos
        if (empty($datos['codigo'])) {
            $error = 'El código es requerido';
            $query = http_build_query(['module' => 'tarifas_aportes', 'action' => 'editar', 'codigo' => $datos['codigo'], 'error' => $error]);
            header('Location: index.php?' . $query);
            exit;
        }

        if (empty($datos['concepto'])) {
            $error = 'El concepto es requerido';
            $query = http_build_query(['module' => 'tarifas_aportes', 'action' => 'editar', 'codigo' => $datos['codigo'], 'error' => $error]);
            header('Location: index.php?' . $query);
            exit;
        }

        if (empty($datos['valor'])) {
            $error = 'El valor es requerido';
            $query = http_build_query(['module' => 'tarifas_aportes', 'action' => 'editar', 'codigo' => $datos['codigo'], 'error' => $error]);
            header('Location: index.php?' . $query);
            exit;
        }

        if (empty($datos['fecha_desde'])) {
            $error = 'La fecha desde es requerida';
            $query = http_build_query(['module' => 'tarifas_aportes', 'action' => 'editar', 'codigo' => $datos['codigo'], 'error' => $error]);
            header('Location: index.php?' . $query);
            exit;
        }

        $resultado = $tarifariosServicio->actualizarTarifario($datos);

        if ($resultado['success']) {
            $_SESSION['message'] = $resultado['message'];
            header('Location: index.php?module=tarifas_aportes&action=listado');
        } else {
            $_SESSION['error'] = $resultado['message'];
            header('Location: index.php?module=tarifas_aportes&action=editar&codigo=' . $datos['codigo']);
        }
        exit;
    }

    // GET: Eliminación de Tarifa de Aporte
    if ($action === 'eliminar') {
        require dirname(__DIR__) . '/views/tarifas_aportes/eliminar.php';
        exit;
    }

    // POST: Eliminar Tarifa de Aporte
    if ($action === 'eliminar' && $_SERVER['REQUEST_METHOD'] === 'POST') {
        $csrfToken = (string) ($_POST['csrf_token'] ?? '');
        if (!hash_equals((string) $_SESSION['csrf_token'], $csrfToken)) {
            $error = 'La solicitud no es válida.';
            $query = http_build_query(['module' => 'tarifas_aportes', 'action' => 'eliminar', 'codigo' => $_POST['codigo'] ?? 0, 'error' => $error]);
            header('Location: index.php?' . $query);
            exit;
        }

        $tarifariosServicio = new \Sacs\Services\TarifariosServicio(\Sacs\Core\Database::connection());

        $codigo = (int) ($_POST['codigo'] ?? 0);
        $resultado = $tarifariosServicio->eliminarTarifario($codigo);

        if ($resultado['success']) {
            $_SESSION['message'] = $resultado['message'];
            header('Location: index.php?module=tarifas_aportes&action=listado');
        } else {
            $_SESSION['error'] = $resultado['message'];
            header('Location: index.php?module=tarifas_aportes&action=eliminar&codigo=' . $codigo);
        }
        exit;
    }
}

// Módulo Tarifas de Beneficios
if ($module === 'tarifas_beneficios') {
    // GET: Listado de Tarifas de Beneficios
    if ($action === 'listado') {
        require dirname(__DIR__) . '/views/tarifas_beneficios/listado.php';
        exit;
    }

    // GET: Registro de Tarifa de Beneficio
    if ($action === 'registro') {
        require dirname(__DIR__) . '/views/tarifas_beneficios/registro.php';
        exit;
    }

    // POST: Registrar Tarifa de Beneficio
    if ($action === 'registrar' && $_SERVER['REQUEST_METHOD'] === 'POST') {
        $csrfToken = (string) ($_POST['csrf_token'] ?? '');
        if (!hash_equals((string) $_SESSION['csrf_token'], $csrfToken)) {
            $error = 'La solicitud no es válida.';
            $query = http_build_query(['module' => 'tarifas_beneficios', 'action' => 'registro', 'error' => $error]);
            header('Location: index.php?' . $query);
            exit;
        }

        $tarifariosServicio = new \Sacs\Services\TarifariosServicio(\Sacs\Core\Database::connection());

        $datos = [
            'codigo' => (int) ($_POST['codigo'] ?? 0),
            'concepto' => trim((string) ($_POST['concepto'] ?? '')),
            'valor' => trim((string) ($_POST['valor'] ?? '')),
            'fecha_desde' => trim((string) ($_POST['fecha_desde'] ?? '')),
            'fecha_hasta' => trim((string) ($_POST['fecha_hasta'] ?? '')),
            'producto' => '6'
        ];

        // Validar campos requeridos
        if (empty($datos['codigo'])) {
            $error = 'El código es requerido';
            $query = http_build_query(['module' => 'tarifas_beneficios', 'action' => 'registro', 'error' => $error]);
            header('Location: index.php?' . $query);
            exit;
        }

        if (empty($datos['concepto'])) {
            $error = 'El concepto es requerido';
            $query = http_build_query(['module' => 'tarifas_beneficios', 'action' => 'registro', 'error' => $error]);
            header('Location: index.php?' . $query);
            exit;
        }

        if (empty($datos['valor'])) {
            $error = 'El valor es requerido';
            $query = http_build_query(['module' => 'tarifas_beneficios', 'action' => 'registro', 'error' => $error]);
            header('Location: index.php?' . $query);
            exit;
        }

        if (empty($datos['fecha_desde'])) {
            $error = 'La fecha desde es requerida';
            $query = http_build_query(['module' => 'tarifas_beneficios', 'action' => 'registro', 'error' => $error]);
            header('Location: index.php?' . $query);
            exit;
        }

        $resultado = $tarifariosServicio->registrarTarifario($datos);

        if ($resultado['success']) {
            $_SESSION['message'] = $resultado['message'];
            header('Location: index.php?module=tarifas_beneficios&action=listado');
        } else {
            $_SESSION['error'] = $resultado['message'];
            header('Location: index.php?module=tarifas_beneficios&action=registro');
        }
        exit;
    }

    // GET: Edición de Tarifa de Beneficio
    if ($action === 'editar') {
        require dirname(__DIR__) . '/views/tarifas_beneficios/editar.php';
        exit;
    }

    // POST: Actualizar Tarifa de Beneficio
    if ($action === 'actualizar' && $_SERVER['REQUEST_METHOD'] === 'POST') {
        $csrfToken = (string) ($_POST['csrf_token'] ?? '');
        if (!hash_equals((string) $_SESSION['csrf_token'], $csrfToken)) {
            $error = 'La solicitud no es válida.';
            $query = http_build_query(['module' => 'tarifas_beneficios', 'action' => 'editar', 'codigo' => $_POST['codigo'] ?? 0, 'error' => $error]);
            header('Location: index.php?' . $query);
            exit;
        }

        $tarifariosServicio = new \Sacs\Services\TarifariosServicio(\Sacs\Core\Database::connection());

        $datos = [
            'codigo' => (int) ($_POST['codigo'] ?? 0),
            'concepto' => trim((string) ($_POST['concepto'] ?? '')),
            'valor' => trim((string) ($_POST['valor'] ?? '')),
            'fecha_desde' => trim((string) ($_POST['fecha_desde'] ?? '')),
            'fecha_hasta' => trim((string) ($_POST['fecha_hasta'] ?? '')),
            'estado' => trim((string) ($_POST['estado'] ?? 'A')),
            'producto' => '6'
        ];

        // Validar campos requeridos
        if (empty($datos['codigo'])) {
            $error = 'El código es requerido';
            $query = http_build_query(['module' => 'tarifas_beneficios', 'action' => 'editar', 'codigo' => $datos['codigo'], 'error' => $error]);
            header('Location: index.php?' . $query);
            exit;
        }

        if (empty($datos['concepto'])) {
            $error = 'El concepto es requerido';
            $query = http_build_query(['module' => 'tarifas_beneficios', 'action' => 'editar', 'codigo' => $datos['codigo'], 'error' => $error]);
            header('Location: index.php?' . $query);
            exit;
        }

        if (empty($datos['valor'])) {
            $error = 'El valor es requerido';
            $query = http_build_query(['module' => 'tarifas_beneficios', 'action' => 'editar', 'codigo' => $datos['codigo'], 'error' => $error]);
            header('Location: index.php?' . $query);
            exit;
        }

        if (empty($datos['fecha_desde'])) {
            $error = 'La fecha desde es requerida';
            $query = http_build_query(['module' => 'tarifas_beneficios', 'action' => 'editar', 'codigo' => $datos['codigo'], 'error' => $error]);
            header('Location: index.php?' . $query);
            exit;
        }

        $resultado = $tarifariosServicio->actualizarTarifario($datos);

        if ($resultado['success']) {
            $_SESSION['message'] = $resultado['message'];
            header('Location: index.php?module=tarifas_beneficios&action=listado');
        } else {
            $_SESSION['error'] = $resultado['message'];
            header('Location: index.php?module=tarifas_beneficios&action=editar&codigo=' . $datos['codigo']);
        }
        exit;
    }

    // GET: Eliminación de Tarifa de Beneficio
    if ($action === 'eliminar') {
        require dirname(__DIR__) . '/views/tarifas_beneficios/eliminar.php';
        exit;
    }

    // POST: Eliminar Tarifa de Beneficio
    if ($action === 'eliminar' && $_SERVER['REQUEST_METHOD'] === 'POST') {
        $csrfToken = (string) ($_POST['csrf_token'] ?? '');
        if (!hash_equals((string) $_SESSION['csrf_token'], $csrfToken)) {
            $error = 'La solicitud no es válida.';
            $query = http_build_query(['module' => 'tarifas_beneficios', 'action' => 'eliminar', 'codigo' => $_POST['codigo'] ?? 0, 'error' => $error]);
            header('Location: index.php?' . $query);
            exit;
        }

        $tarifariosServicio = new \Sacs\Services\TarifariosServicio(\Sacs\Core\Database::connection());

        $codigo = (int) ($_POST['codigo'] ?? 0);
        $resultado = $tarifariosServicio->eliminarTarifario($codigo);

        if ($resultado['success']) {
            $_SESSION['message'] = $resultado['message'];
            header('Location: index.php?module=tarifas_beneficios&action=listado');
        } else {
            $_SESSION['error'] = $resultado['message'];
            header('Location: index.php?module=tarifas_beneficios&action=eliminar&codigo=' . $codigo);
        }
        exit;
    }
}

// Módulo Perfiles
if ($module === 'perfiles') {
    // GET: Listado de Perfiles
    if ($action === 'listado') {
        require dirname(__DIR__) . '/views/perfiles/listado.php';
        exit;
    }

    // GET: Registro de Perfil
    if ($action === 'registro') {
        require dirname(__DIR__) . '/views/perfiles/registro.php';
        exit;
    }

    // POST: Registrar Perfil
    if ($action === 'registrar' && $_SERVER['REQUEST_METHOD'] === 'POST') {
        $csrfToken = (string) ($_POST['csrf_token'] ?? '');
        if (!hash_equals((string) $_SESSION['csrf_token'], $csrfToken)) {
            $error = 'La solicitud no es válida.';
            $query = http_build_query(['module' => 'perfiles', 'action' => 'registro', 'error' => $error]);
            header('Location: index.php?' . $query);
            exit;
        }

        $perfilesServicio = new \Sacs\Services\PerfilesServicio(\Sacs\Core\Database::connection());

        $datos = [
            'codigo' => trim((string) ($_POST['codigo'] ?? '')),
            'nombre' => trim((string) ($_POST['nombre'] ?? ''))
        ];

        // Validar campos requeridos
        if (empty($datos['codigo'])) {
            $error = 'El código es requerido';
            $query = http_build_query(['module' => 'perfiles', 'action' => 'registro', 'error' => $error]);
            header('Location: index.php?' . $query);
            exit;
        }

        if (empty($datos['nombre'])) {
            $error = 'El nombre es requerido';
            $query = http_build_query(['module' => 'perfiles', 'action' => 'registro', 'error' => $error]);
            header('Location: index.php?' . $query);
            exit;
        }

        $resultado = $perfilesServicio->registrarPerfil($datos);

        if ($resultado['success']) {
            $query = http_build_query(['module' => 'perfiles', 'action' => 'registro', 'success' => $resultado['message']]);
        } else {
            $query = http_build_query(['module' => 'perfiles', 'action' => 'registro', 'error' => $resultado['message']]);
        }

        header('Location: index.php?' . $query);
        exit;
    }

    // GET: Edición de Perfil
    if ($action === 'editar') {
        require dirname(__DIR__) . '/views/perfiles/editar.php';
        exit;
    }

    // POST: Actualizar Perfil
    if ($action === 'actualizar' && $_SERVER['REQUEST_METHOD'] === 'POST') {
        $csrfToken = (string) ($_POST['csrf_token'] ?? '');
        if (!hash_equals((string) $_SESSION['csrf_token'], $csrfToken)) {
            $error = 'La solicitud no es válida.';
            $query = http_build_query(['module' => 'perfiles', 'action' => 'editar', 'id' => $_POST['id'] ?? 0, 'error' => $error]);
            header('Location: index.php?' . $query);
            exit;
        }

        $perfilesServicio = new \Sacs\Services\PerfilesServicio(\Sacs\Core\Database::connection());

        $id = (int) ($_POST['id'] ?? 0);
        $datos = [
            'nombre' => trim((string) ($_POST['nombre'] ?? ''))
        ];

        // Validar campos requeridos
        if (empty($datos['nombre'])) {
            $error = 'El nombre es requerido';
            $query = http_build_query(['module' => 'perfiles', 'action' => 'editar', 'id' => $id, 'error' => $error]);
            header('Location: index.php?' . $query);
            exit;
        }

        $resultado = $perfilesServicio->actualizarPerfil($id, $datos);

        if ($resultado['success']) {
            $query = http_build_query(['module' => 'perfiles', 'action' => 'editar', 'id' => $id, 'success' => $resultado['message']]);
        } else {
            $query = http_build_query(['module' => 'perfiles', 'action' => 'editar', 'id' => $id, 'error' => $resultado['message']]);
        }

        header('Location: index.php?' . $query);
        exit;
    }

    // GET: Eliminación de Perfil
    if ($action === 'eliminar') {
        require dirname(__DIR__) . '/views/perfiles/eliminar.php';
        exit;
    }

    // POST: Eliminar Perfil
    if ($action === 'eliminar' && $_SERVER['REQUEST_METHOD'] === 'POST') {
        $csrfToken = (string) ($_POST['csrf_token'] ?? '');
        if (!hash_equals((string) $_SESSION['csrf_token'], $csrfToken)) {
            $error = 'La solicitud no es válida.';
            $query = http_build_query(['module' => 'perfiles', 'action' => 'eliminar', 'id' => $_POST['id'] ?? 0, 'error' => $error]);
            header('Location: index.php?' . $query);
            exit;
        }

        $perfilesServicio = new \Sacs\Services\PerfilesServicio(\Sacs\Core\Database::connection());

        $id = (int) ($_POST['id'] ?? 0);
        $resultado = $perfilesServicio->eliminarPerfil($id);

        if ($resultado['success']) {
            $query = http_build_query(['module' => 'perfiles', 'action' => 'listado', 'success' => $resultado['message']]);
        } else {
            $query = http_build_query(['module' => 'perfiles', 'action' => 'eliminar', 'id' => $id, 'error' => $resultado['message']]);
        }

        header('Location: index.php?' . $query);
        exit;
    }
}

// Módulo Usuarios
if ($module === 'usuarios') {
    // GET: Listado de Usuarios
    if ($action === 'listado') {
        require dirname(__DIR__) . '/views/usuarios/listado.php';
        exit;
    }

    // GET: Registro de Usuario
    if ($action === 'registro') {
        require dirname(__DIR__) . '/views/usuarios/registro.php';
        exit;
    }

    // POST: Registrar Usuario
    if ($action === 'registrar' && $_SERVER['REQUEST_METHOD'] === 'POST') {
        $csrfToken = (string) ($_POST['csrf_token'] ?? '');
        if (!hash_equals((string) $_SESSION['csrf_token'], $csrfToken)) {
            $error = 'La solicitud no es válida.';
            $query = http_build_query(['module' => 'usuarios', 'action' => 'registro', 'error' => $error]);
            header('Location: index.php?' . $query);
            exit;
        }

        $usuariosServicio = new \Sacs\Services\UsuariosServicio(\Sacs\Core\Database::connection());

        $datos = [
            'nombre' => trim((string) ($_POST['nombre'] ?? '')),
            'password' => trim((string) ($_POST['password'] ?? '')),
            'confirmar_password' => trim((string) ($_POST['confirmar_password'] ?? '')),
            'perfil' => trim((string) ($_POST['perfil'] ?? ''))
        ];

        $errores = $usuariosServicio->validarUsuario($datos);

        if (!empty($errores)) {
            $error = implode(', ', $errores);
            $query = http_build_query(['module' => 'usuarios', 'action' => 'registro', 'error' => $error]);
            header('Location: index.php?' . $query);
            exit;
        }

        $resultado = $usuariosServicio->registrarUsuario($datos);

        if ($resultado['success']) {
            $query = http_build_query(['module' => 'usuarios', 'action' => 'registro', 'success' => $resultado['message']]);
        } else {
            $query = http_build_query(['module' => 'usuarios', 'action' => 'registro', 'error' => $resultado['message']]);
        }

        header('Location: index.php?' . $query);
        exit;
    }

    // GET: Edición de Usuario
    if ($action === 'editar') {
        require dirname(__DIR__) . '/views/usuarios/editar.php';
        exit;
    }

    // POST: Actualizar Usuario
    if ($action === 'actualizar' && $_SERVER['REQUEST_METHOD'] === 'POST') {
        $csrfToken = (string) ($_POST['csrf_token'] ?? '');
        if (!hash_equals((string) $_SESSION['csrf_token'], $csrfToken)) {
            $error = 'La solicitud no es válida.';
            $query = http_build_query(['module' => 'usuarios', 'action' => 'editar', 'id' => $_POST['id'] ?? 0, 'error' => $error]);
            header('Location: index.php?' . $query);
            exit;
        }

        $usuariosServicio = new \Sacs\Services\UsuariosServicio(\Sacs\Core\Database::connection());

        $id = (int) ($_POST['id'] ?? 0);
        $datos = [
            'nombre' => trim((string) ($_POST['nombre'] ?? '')),
            'perfil' => trim((string) ($_POST['perfil'] ?? '')),
            'estado' => trim((string) ($_POST['estado'] ?? 'A')),
            'password' => trim((string) ($_POST['password'] ?? '')),
            'confirmar_password' => trim((string) ($_POST['confirmar_password'] ?? ''))
        ];

        // Validar campos requeridos
        if (empty($datos['nombre'])) {
            $error = 'El nombre de usuario es requerido';
            $query = http_build_query(['module' => 'usuarios', 'action' => 'editar', 'id' => $id, 'error' => $error]);
            header('Location: index.php?' . $query);
            exit;
        }

        if (empty($datos['perfil'])) {
            $error = 'El perfil es requerido';
            $query = http_build_query(['module' => 'usuarios', 'action' => 'editar', 'id' => $id, 'error' => $error]);
            header('Location: index.php?' . $query);
            exit;
        }

        // Validar contraseña si se está cambiando
        if (!empty($datos['password'])) {
            if (strlen($datos['password']) < 6) {
                $error = 'La contraseña debe tener al menos 6 caracteres';
                $query = http_build_query(['module' => 'usuarios', 'action' => 'editar', 'id' => $id, 'error' => $error]);
                header('Location: index.php?' . $query);
                exit;
            }

            if ($datos['password'] !== $datos['confirmar_password']) {
                $error = 'Las contraseñas no coinciden';
                $query = http_build_query(['module' => 'usuarios', 'action' => 'editar', 'id' => $id, 'error' => $error]);
                header('Location: index.php?' . $query);
                exit;
            }
        } else {
            // No cambiar contraseña
            unset($datos['password']);
            unset($datos['confirmar_password']);
        }

        $resultado = $usuariosServicio->actualizarUsuario($id, $datos);

        if ($resultado['success']) {
            $query = http_build_query(['module' => 'usuarios', 'action' => 'editar', 'id' => $id, 'success' => $resultado['message']]);
        } else {
            $query = http_build_query(['module' => 'usuarios', 'action' => 'editar', 'id' => $id, 'error' => $resultado['message']]);
        }

        header('Location: index.php?' . $query);
        exit;
    }

    // GET: Eliminación de Usuario
    if ($action === 'eliminar') {
        require dirname(__DIR__) . '/views/usuarios/eliminar.php';
        exit;
    }

    // POST: Eliminar Usuario
    if ($action === 'eliminar' && $_SERVER['REQUEST_METHOD'] === 'POST') {
        $csrfToken = (string) ($_POST['csrf_token'] ?? '');
        if (!hash_equals((string) $_SESSION['csrf_token'], $csrfToken)) {
            $error = 'La solicitud no es válida.';
            $query = http_build_query(['module' => 'usuarios', 'action' => 'eliminar', 'id' => $_POST['id'] ?? 0, 'error' => $error]);
            header('Location: index.php?' . $query);
            exit;
        }

        $usuariosServicio = new \Sacs\Services\UsuariosServicio(\Sacs\Core\Database::connection());

        $id = (int) ($_POST['id'] ?? 0);
        $resultado = $usuariosServicio->eliminarUsuario($id);

        if ($resultado['success']) {
            $query = http_build_query(['module' => 'usuarios', 'action' => 'listado', 'success' => $resultado['message']]);
        } else {
            $query = http_build_query(['module' => 'usuarios', 'action' => 'eliminar', 'id' => $id, 'error' => $resultado['message']]);
        }

        header('Location: index.php?' . $query);
        exit;
    }

    // GET: Cambio de Contraseña
    if ($action === 'cambiar_clave') {
        require dirname(__DIR__) . '/views/usuarios/cambiar_clave.php';
        exit;
    }

    // POST: Cambiar Contraseña
    if ($action === 'cambiar_clave' && $_SERVER['REQUEST_METHOD'] === 'POST') {
        $csrfToken = (string) ($_POST['csrf_token'] ?? '');
        if (!hash_equals((string) $_SESSION['csrf_token'], $csrfToken)) {
            $error = 'La solicitud no es válida.';
            $query = http_build_query(['module' => 'usuarios', 'action' => 'cambiar_clave', 'error' => $error]);
            header('Location: index.php?' . $query);
            exit;
        }

        $usuariosServicio = new \Sacs\Services\UsuariosServicio(\Sacs\Core\Database::connection());

        $usuarioActual = $_SESSION['sacs_user']['username'] ?? 'SISTEMA';
        $passwordActual = trim((string) ($_POST['password_actual'] ?? ''));
        $nuevaPassword = trim((string) ($_POST['nueva_password'] ?? ''));
        $confirmarPassword = trim((string) ($_POST['confirmar_password'] ?? ''));

        // Validar campos requeridos
        if (empty($passwordActual)) {
            $error = 'La contraseña actual es requerida';
            $query = http_build_query(['module' => 'usuarios', 'action' => 'cambiar_clave', 'error' => $error]);
            header('Location: index.php?' . $query);
            exit;
        }

        if (empty($nuevaPassword)) {
            $error = 'La nueva contraseña es requerida';
            $query = http_build_query(['module' => 'usuarios', 'action' => 'cambiar_clave', 'error' => $error]);
            header('Location: index.php?' . $query);
            exit;
        }

        if (strlen($nuevaPassword) < 6) {
            $error = 'La nueva contraseña debe tener al menos 6 caracteres';
            $query = http_build_query(['module' => 'usuarios', 'action' => 'cambiar_clave', 'error' => $error]);
            header('Location: index.php?' . $query);
            exit;
        }

        if ($nuevaPassword !== $confirmarPassword) {
            $error = 'Las contraseñas no coinciden';
            $query = http_build_query(['module' => 'usuarios', 'action' => 'cambiar_clave', 'error' => $error]);
            header('Location: index.php?' . $query);
            exit;
        }

        // Verificar contraseña actual
        $usuarioData = $usuariosServicio->obtenerUsuarioPorNombre($usuarioActual);
        
        if (!$usuarioData) {
            $error = 'Usuario no encontrado';
            $query = http_build_query(['module' => 'usuarios', 'action' => 'cambiar_clave', 'error' => $error]);
            header('Location: index.php?' . $query);
            exit;
        }

        if ($usuarioData['password'] !== $passwordActual) {
            $error = 'La contraseña actual es incorrecta';
            $query = http_build_query(['module' => 'usuarios', 'action' => 'cambiar_clave', 'error' => $error]);
            header('Location: index.php?' . $query);
            exit;
        }

        // Cambiar contraseña
        $resultado = $usuariosServicio->cambiarPassword($usuarioData['id'], $nuevaPassword);

        if ($resultado['success']) {
            $query = http_build_query(['module' => 'usuarios', 'action' => 'cambiar_clave', 'success' => $resultado['message']]);
        } else {
            $query = http_build_query(['module' => 'usuarios', 'action' => 'cambiar_clave', 'error' => $resultado['message']]);
        }

        header('Location: index.php?' . $query);
        exit;
    }
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

        if (($_GET['action'] ?? '') === 'pago_integral_seleccion') {
            if ($clientId !== '') {
                require dirname(__DIR__) . '/views/caja/pago_integral_seleccion.php';
                exit;
            }
            http_response_code(400);
            $error = 'Datos inválidos para la selección de pago integral';
            require dirname(__DIR__) . '/views/home.php';
            exit;
        }

        if (($_GET['action'] ?? '') === 'pago_integral_form') {
            if ($clientId !== '' && !empty($_GET['prestamos'])) {
                require dirname(__DIR__) . '/views/caja/pago_integral.php';
                exit;
            }
            http_response_code(400);
            $error = 'Datos inválidos para el formulario de pago integral';
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
    if ($module === 'socios') {
        $sociosServicio = new SociosServicio();
        // Si es POST, tomar action solo de POST, si no, de GET
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $action = $_POST['action'] ?? '';
        } else {
            $action = $_GET['action'] ?? '';
        }

        // GET: Listado de socios
        if ($action === 'listado') {
            $filtros = [
                'criterio' => trim((string) ($_GET['criterio'] ?? '')),
                'departamento' => trim((string) ($_GET['departamento'] ?? '')),
                'provincia' => trim((string) ($_GET['provincia'] ?? '')),
                'distrito' => trim((string) ($_GET['distrito'] ?? '')),
                'condicion' => trim((string) ($_GET['condicion'] ?? '')),
                'situacion' => trim((string) ($_GET['situacion'] ?? '')),
                'estado' => trim((string) ($_GET['estado'] ?? '')),
                'edad_min' => trim((string) ($_GET['edad_min'] ?? '')),
                'edad_max' => trim((string) ($_GET['edad_max'] ?? '')),
            ];
            
            // Paginación
            $pagina = max(1, (int) ($_GET['pagina'] ?? 1));
            $porPagina = 10;
            $offset = ($pagina - 1) * $porPagina;
            
            $socios = $sociosServicio->listarSocios($filtros, $offset, $porPagina);
            $totalSocios = $sociosServicio->contarSocios($filtros);
            $totalPaginas = (int) ceil($totalSocios / $porPagina);
            
            // Cargar datos para filtros (cargar todos al inicio)
            $departamentos = $sociosServicio->obtenerDepartamentos();
            $provincias = $sociosServicio->obtenerTodasProvincias();
            $distritos = $sociosServicio->obtenerTodosDistritos();
            $condiciones = $sociosServicio->obtenerCondiciones();
            $situaciones = $sociosServicio->obtenerSituaciones();
            
            require dirname(__DIR__) . '/views/socios/listado.php';
            exit;
        }

        // GET: Información personal del socio
        if ($action === 'info') {
            $clienteCodigo = trim((string) ($_GET['client'] ?? ''));
            
            if ($clienteCodigo === '') {
                http_response_code(400);
                $error = 'Cliente no especificado';
                require dirname(__DIR__) . '/views/home.php';
                exit;
            }
            
            $socio = $sociosServicio->obtenerSocioDetalle($clienteCodigo);
            
            if (empty($socio)) {
                http_response_code(404);
                $error = 'Cliente no encontrado';
                require dirname(__DIR__) . '/views/home.php';
                exit;
            }
            
            require dirname(__DIR__) . '/views/socios/info.php';
            exit;
        }

        // GET: Aportes FONBES del socio
        if ($action === 'aportes_fonbes') {
            $clienteCodigo = trim((string) ($_GET['client'] ?? ''));

            if ($clienteCodigo === '') {
                http_response_code(400);
                $error = 'Cliente no especificado';
                require dirname(__DIR__) . '/views/home.php';
                exit;
            }

            require dirname(__DIR__) . '/views/socios/aportes_fonbes.php';
            exit;
        }

        // GET: Aportes COOPAM del socio
        if ($action === 'aportes_coopam') {
            $clienteCodigo = trim((string) ($_GET['client'] ?? ''));

            if ($clienteCodigo === '') {
                http_response_code(400);
                $error = 'Cliente no especificado';
                require dirname(__DIR__) . '/views/home.php';
                exit;
            }

            require dirname(__DIR__) . '/views/socios/aportes_coopam.php';
            exit;
        }

        // GET: Gestión de estado del socio
        if ($action === 'gestion_estado') {
            $clienteCodigo = trim((string) ($_GET['client'] ?? ''));

            if ($clienteCodigo === '') {
                http_response_code(400);
                $error = 'Cliente no especificado';
                require dirname(__DIR__) . '/views/home.php';
                exit;
            }

            require dirname(__DIR__) . '/views/socios/gestion_estado.php';
            exit;
        }

        // GET: Beneficios del socio
        if ($action === 'beneficios') {
            $clienteCodigo = trim((string) ($_GET['client'] ?? ''));

            if ($clienteCodigo === '') {
                http_response_code(400);
                $error = 'Cliente no especificado';
                require dirname(__DIR__) . '/views/home.php';
                exit;
            }

            require dirname(__DIR__) . '/views/socios/beneficios.php';
            exit;
        }

        // GET: Formulario nuevo beneficio
        if ($action === 'beneficio_nuevo') {
            $clienteCodigo = trim((string) ($_GET['client'] ?? ''));

            if ($clienteCodigo === '') {
                http_response_code(400);
                $error = 'Cliente no especificado';
                require dirname(__DIR__) . '/views/home.php';
                exit;
            }

            require dirname(__DIR__) . '/views/socios/beneficio_form.php';
            exit;
        }

        // GET: Formulario editar beneficio
        if ($action === 'beneficio_editar') {
            $clienteCodigo = trim((string) ($_GET['client'] ?? ''));
            $codigo = trim((string) ($_GET['codigo'] ?? ''));

            if ($clienteCodigo === '' || $codigo === '') {
                http_response_code(400);
                $error = 'Cliente o código no especificado';
                require dirname(__DIR__) . '/views/home.php';
                exit;
            }

            require dirname(__DIR__) . '/views/socios/beneficio_form.php';
            exit;
        }

        // AJAX: Obtener provincias
        if ($action === 'get_provincias') {
            $departamento = trim((string) ($_GET['departamento'] ?? ''));
            if ($departamento === '') {
                header('Content-Type: application/json');
                echo json_encode([]);
                exit;
            }

            $provincias = $sociosServicio->obtenerProvincias($departamento);
            header('Content-Type: application/json');
            echo json_encode($provincias);
            exit;
        }

        // AJAX: Obtener distritos
        if ($action === 'get_distritos') {
            $departamento = trim((string) ($_GET['departamento'] ?? ''));
            $provincia = trim((string) ($_GET['provincia'] ?? ''));
            if ($departamento === '' || $provincia === '') {
                header('Content-Type: application/json');
                echo json_encode([]);
                exit;
            }

            $distritos = $sociosServicio->obtenerDistritos($departamento, $provincia);
            header('Content-Type: application/json');
            echo json_encode($distritos);
            exit;
        }

        // POST: Guardar beneficio (crear/editar)
        if ($action === 'beneficio_guardar' && $_SERVER['REQUEST_METHOD'] === 'POST') {
            $csrfToken = (string) ($_POST['csrf_token'] ?? '');
            if (!hash_equals((string) $_SESSION['csrf_token'], $csrfToken)) {
                $error = 'La solicitud no es válida.';
                $query = http_build_query(['module' => 'socios', 'action' => 'beneficios', 'client' => $_POST['cliente'] ?? '', 'error' => $error]);
                header('Location: index.php?' . $query);
                exit;
            }

            try {
                $modo = trim((string) ($_POST['modo'] ?? ''));
                $clienteCodigo = trim((string) ($_POST['cliente'] ?? ''));
                $codigo = trim((string) ($_POST['codigo'] ?? ''));

                $datos = [
                    'socio' => $clienteCodigo,
                    'nombres' => trim((string) ($_POST['nombres'] ?? '')),
                    'beneficiario' => trim((string) ($_POST['beneficiario'] ?? '')),
                    'dni1' => trim((string) ($_POST['dni1'] ?? '')),
                    'direccion1' => trim((string) ($_POST['direccion1'] ?? '')),
                    'dni2' => trim((string) ($_POST['dni2'] ?? '')) ?: null,
                    'direccion2' => trim((string) ($_POST['direccion2'] ?? '')) ?: null,
                    'tipo_pa1' => trim((string) ($_POST['tipo_pa1'] ?? '')) ?: null,
                    'tipo_pa2' => trim((string) ($_POST['tipo_pa2'] ?? '')) ?: null,
                    'tipo' => trim((string) ($_POST['tipo'] ?? '')),
                    'importe' => trim((string) ($_POST['importe'] ?? '')),
                    'fecha_falle' => trim((string) ($_POST['fecha_falle'] ?? '')) ?: null,
                    'lugar_falle' => trim((string) ($_POST['lugar_falle'] ?? '')) ?: null,
                    'tramite' => trim((string) ($_POST['tramite'] ?? '')) ?: null,
                    'vigencia' => trim((string) ($_POST['vigencia'] ?? '')) ?: null,
                    'obs' => trim((string) ($_POST['obs'] ?? '')) ?: null,
                    'acredita' => isset($_POST['acredita']) ? 1 : 0,
                    'aporta' => isset($_POST['aporta']) ? 1 : 0,
                ];

                if ($modo === 'nuevo') {
                    $resultado = $sociosServicio->registrarBeneficio($datos, $_SESSION['user']['username'] ?? 'SISTEMA');
                } else {
                    $resultado = $sociosServicio->actualizarBeneficio((int) $codigo, $datos, $_SESSION['user']['username'] ?? 'SISTEMA');
                }

                if ($resultado['success']) {
                    $query = http_build_query(['module' => 'socios', 'action' => 'beneficios', 'client' => $clienteCodigo, 'success' => $resultado['message']]);
                    header('Location: index.php?' . $query);
                    exit;
                } else {
                    $query = http_build_query(['module' => 'socios', 'action' => $modo === 'nuevo' ? 'beneficio_nuevo' : 'beneficio_editar', 'client' => $clienteCodigo, 'codigo' => $codigo, 'error' => $resultado['message']]);
                    header('Location: index.php?' . $query);
                    exit;
                }

            } catch (Exception $e) {
                $error = 'Error al guardar beneficio: ' . $e->getMessage();
                $query = http_build_query(['module' => 'socios', 'action' => $modo === 'nuevo' ? 'beneficio_nuevo' : 'beneficio_editar', 'client' => $_POST['cliente'] ?? '', 'codigo' => $_POST['codigo'] ?? '', 'error' => $error]);
                header('Location: index.php?' . $query);
                exit;
            }
        }

        // GET: Eliminar beneficio
        if ($action === 'beneficio_eliminar') {
            $csrfToken = (string) ($_GET['csrf_token'] ?? '');
            if (!hash_equals((string) $_SESSION['csrf_token'], $csrfToken)) {
                $error = 'La solicitud no es válida.';
                $query = http_build_query(['module' => 'socios', 'action' => 'beneficios', 'client' => $_GET['client'] ?? '', 'error' => $error]);
                header('Location: index.php?' . $query);
                exit;
            }

            try {
                $codigo = trim((string) ($_GET['codigo'] ?? ''));
                $clienteCodigo = trim((string) ($_GET['client'] ?? ''));

                if ($codigo === '') {
                    throw new Exception('Código no especificado');
                }

                $resultado = $sociosServicio->eliminarBeneficio((int) $codigo, $_SESSION['user']['username'] ?? 'SISTEMA');

                if ($resultado['success']) {
                    $query = http_build_query(['module' => 'socios', 'action' => 'beneficios', 'client' => $clienteCodigo, 'success' => $resultado['message']]);
                    header('Location: index.php?' . $query);
                    exit;
                } else {
                    $query = http_build_query(['module' => 'socios', 'action' => 'beneficios', 'client' => $clienteCodigo, 'error' => $resultado['message']]);
                    header('Location: index.php?' . $query);
                    exit;
                }

            } catch (Exception $e) {
                $error = 'Error al eliminar beneficio: ' . $e->getMessage();
                $query = http_build_query(['module' => 'socios', 'action' => 'beneficios', 'client' => $_GET['client'] ?? '', 'error' => $error]);
                header('Location: index.php?' . $query);
                exit;
            }
        }

        // GET: Listado de familiares
        if ($action === 'familiares') {
            $clienteCodigo = trim((string) ($_GET['client'] ?? ''));
            
            if ($clienteCodigo === '') {
                http_response_code(400);
                $error = 'Cliente no especificado';
                require dirname(__DIR__) . '/views/home.php';
                exit;
            }
            
            require dirname(__DIR__) . '/views/socios/familiares.php';
            exit;
        }

        // GET: Buscar familiar (persona)
        if ($action === 'familiar_buscar') {
            require dirname(__DIR__) . '/views/socios/familiar_buscar.php';
            exit;
        }

        // GET: Editar parentesco de familiar
        if ($action === 'familiar_editar') {
            $clienteCodigo = trim((string) ($_GET['client'] ?? ''));
            $idPersona = trim((string) ($_GET['persona'] ?? ''));
            
            if ($clienteCodigo === '' || $idPersona === '') {
                http_response_code(400);
                $error = 'Cliente o persona no especificado';
                require dirname(__DIR__) . '/views/home.php';
                exit;
            }
            
            require dirname(__DIR__) . '/views/socios/familiar_editar.php';
            exit;
        }

        // POST: Agregar familiar
        if ($action === 'familiar_agregar' && $_SERVER['REQUEST_METHOD'] === 'POST') {
            $csrfToken = (string) ($_POST['csrf_token'] ?? '');
            if (!hash_equals((string) $_SESSION['csrf_token'], $csrfToken)) {
                $error = 'La solicitud no es válida.';
                $query = http_build_query(['module' => 'socios', 'action' => 'familiares', 'client' => $_POST['client'] ?? '', 'error' => $error]);
                header('Location: index.php?' . $query);
                exit;
            }

            try {
                $clienteCodigo = trim((string) ($_POST['client'] ?? ''));
                $idPersona = trim((string) ($_POST['persona'] ?? ''));
                $tipoParentesco = trim((string) ($_POST['parentesco'] ?? ''));

                if ($clienteCodigo === '' || $idPersona === '' || $tipoParentesco === '') {
                    throw new Exception('Datos incompletos');
                }

                $resultado = $sociosServicio->registrarFamiliar($clienteCodigo, $idPersona, $tipoParentesco, $_SESSION['user']['username'] ?? 'SISTEMA');

                if ($resultado['success']) {
                    $query = http_build_query(['module' => 'socios', 'action' => 'familiares', 'client' => $clienteCodigo, 'success' => $resultado['message']]);
                    header('Location: index.php?' . $query);
                    exit;
                } else {
                    $query = http_build_query(['module' => 'socios', 'action' => 'familiares', 'client' => $clienteCodigo, 'error' => $resultado['message']]);
                    header('Location: index.php?' . $query);
                    exit;
                }

            } catch (Exception $e) {
                $error = 'Error al agregar familiar: ' . $e->getMessage();
                $query = http_build_query(['module' => 'socios', 'action' => 'familiares', 'client' => $_POST['client'] ?? '', 'error' => $error]);
                header('Location: index.php?' . $query);
                exit;
            }
        }

        // POST: Actualizar parentesco de familiar
        if ($action === 'familiar_actualizar' && $_SERVER['REQUEST_METHOD'] === 'POST') {
            $csrfToken = (string) ($_POST['csrf_token'] ?? '');
            if (!hash_equals((string) $_SESSION['csrf_token'], $csrfToken)) {
                $error = 'La solicitud no es válida.';
                $query = http_build_query(['module' => 'socios', 'action' => 'familiares', 'client' => $_POST['client'] ?? '', 'error' => $error]);
                header('Location: index.php?' . $query);
                exit;
            }

            try {
                $clienteCodigo = trim((string) ($_POST['client'] ?? ''));
                $idPersona = trim((string) ($_POST['persona'] ?? ''));
                $tipoParentesco = trim((string) ($_POST['parentesco'] ?? ''));

                if ($clienteCodigo === '' || $idPersona === '' || $tipoParentesco === '') {
                    throw new Exception('Datos incompletos');
                }

                $resultado = $sociosServicio->actualizarParentesco($clienteCodigo, $idPersona, $tipoParentesco, $_SESSION['user']['username'] ?? 'SISTEMA');

                if ($resultado['success']) {
                    $query = http_build_query(['module' => 'socios', 'action' => 'familiares', 'client' => $clienteCodigo, 'success' => $resultado['message']]);
                    header('Location: index.php?' . $query);
                    exit;
                } else {
                    $query = http_build_query(['module' => 'socios', 'action' => 'familiares', 'client' => $clienteCodigo, 'error' => $resultado['message']]);
                    header('Location: index.php?' . $query);
                    exit;
                }

            } catch (Exception $e) {
                $error = 'Error al actualizar parentesco: ' . $e->getMessage();
                $query = http_build_query(['module' => 'socios', 'action' => 'familiares', 'client' => $_POST['client'] ?? '', 'error' => $error]);
                header('Location: index.php?' . $query);
                exit;
            }
        }

        // GET: Eliminar familiar
        if ($action === 'familiar_eliminar') {
            $csrfToken = (string) ($_GET['csrf_token'] ?? '');
            if (!hash_equals((string) $_SESSION['csrf_token'], $csrfToken)) {
                $error = 'La solicitud no es válida.';
                $query = http_build_query(['module' => 'socios', 'action' => 'familiares', 'client' => $_GET['client'] ?? '', 'error' => $error]);
                header('Location: index.php?' . $query);
                exit;
            }

            try {
                $clienteCodigo = trim((string) ($_GET['client'] ?? ''));
                $idPersona = trim((string) ($_GET['persona'] ?? ''));

                if ($clienteCodigo === '' || $idPersona === '') {
                    throw new Exception('Datos incompletos');
                }

                $resultado = $sociosServicio->eliminarFamiliar($clienteCodigo, $idPersona, $_SESSION['user']['username'] ?? 'SISTEMA');

                if ($resultado['success']) {
                    $query = http_build_query(['module' => 'socios', 'action' => 'familiares', 'client' => $clienteCodigo, 'success' => $resultado['message']]);
                    header('Location: index.php?' . $query);
                    exit;
                } else {
                    $query = http_build_query(['module' => 'socios', 'action' => 'familiares', 'client' => $clienteCodigo, 'error' => $resultado['message']]);
                    header('Location: index.php?' . $query);
                    exit;
                }

            } catch (Exception $e) {
                $error = 'Error al eliminar familiar: ' . $e->getMessage();
                $query = http_build_query(['module' => 'socios', 'action' => 'familiares', 'client' => $_GET['client'] ?? '', 'error' => $error]);
                header('Location: index.php?' . $query);
                exit;
            }
        }

        // GET: Listado de rifas
        if ($action === 'rifas') {
            $clienteCodigo = trim((string) ($_GET['client'] ?? ''));
            
            if ($clienteCodigo === '') {
                http_response_code(400);
                $error = 'Cliente no especificado';
                require dirname(__DIR__) . '/views/home.php';
                exit;
            }
            
            require dirname(__DIR__) . '/views/socios/rifas.php';
            exit;
        }

        // GET: Nueva rifa
        if ($action === 'rifa_nuevo') {
            $clienteCodigo = trim((string) ($_GET['client'] ?? ''));
            
            if ($clienteCodigo === '') {
                http_response_code(400);
                $error = 'Cliente no especificado';
                require dirname(__DIR__) . '/views/home.php';
                exit;
            }
            
            require dirname(__DIR__) . '/views/socios/rifa_form.php';
            exit;
        }

        // GET: Editar rifa
        if ($action === 'rifa_editar') {
            $clienteCodigo = trim((string) ($_GET['client'] ?? ''));
            $codigo = trim((string) ($_GET['codigo'] ?? ''));
            
            if ($clienteCodigo === '' || $codigo === '') {
                http_response_code(400);
                $error = 'Cliente o código no especificado';
                require dirname(__DIR__) . '/views/home.php';
                exit;
            }
            
            require dirname(__DIR__) . '/views/socios/rifa_form.php';
            exit;
        }

        // POST: Guardar rifa
        if ($action === 'rifa_guardar' && $_SERVER['REQUEST_METHOD'] === 'POST') {
            $csrfToken = (string) ($_POST['csrf_token'] ?? '');
            if (!hash_equals((string) $_SESSION['csrf_token'], $csrfToken)) {
                $error = 'La solicitud no es válida.';
                $query = http_build_query(['module' => 'socios', 'action' => 'rifas', 'client' => $_POST['client'] ?? '', 'error' => $error]);
                header('Location: index.php?' . $query);
                exit;
            }

            try {
                $modo = trim((string) ($_POST['modo'] ?? ''));
                $clienteCodigo = trim((string) ($_POST['client'] ?? ''));
                
                $datos = [
                    'socio' => $clienteCodigo,
                    'periodo' => trim((string) ($_POST['periodo'] ?? '')),
                    'rifap' => trim((string) ($_POST['rifap'] ?? '0')),
                    'rifad' => trim((string) ($_POST['rifad'] ?? '0')),
                    'rifar' => trim((string) ($_POST['rifar'] ?? '0')),
                    'fechaPago' => trim((string) ($_POST['fechaPago'] ?? '')),
                    'formaPago' => trim((string) ($_POST['formaPago'] ?? '')),
                ];

                if ($datos['periodo'] === '') {
                    throw new Exception('Periodo es requerido');
                }

                if ($modo === 'nuevo') {
                    $resultado = $sociosServicio->registrarRifa($datos, $_SESSION['user']['username'] ?? 'SISTEMA');
                } else {
                    $codigo = trim((string) ($_POST['codigo'] ?? ''));
                    if ($codigo === '') {
                        throw new Exception('Código no especificado');
                    }
                    $resultado = $sociosServicio->actualizarRifa((int) $codigo, $datos, $_SESSION['user']['username'] ?? 'SISTEMA');
                }

                if ($resultado['success']) {
                    $query = http_build_query(['module' => 'socios', 'action' => 'rifas', 'client' => $clienteCodigo, 'success' => $resultado['message']]);
                    header('Location: index.php?' . $query);
                    exit;
                } else {
                    $query = http_build_query(['module' => 'socios', 'action' => $modo === 'nuevo' ? 'rifa_nuevo' : 'rifa_editar', 'client' => $clienteCodigo, 'codigo' => $_POST['codigo'] ?? '', 'error' => $resultado['message']]);
                    header('Location: index.php?' . $query);
                    exit;
                }

            } catch (Exception $e) {
                $error = 'Error al guardar rifa: ' . $e->getMessage();
                $query = http_build_query(['module' => 'socios', 'action' => 'rifas', 'client' => $_POST['client'] ?? '', 'error' => $error]);
                header('Location: index.php?' . $query);
                exit;
            }
        }

        // GET: Eliminar rifa
        if ($action === 'rifa_eliminar') {
            $csrfToken = (string) ($_GET['csrf_token'] ?? '');
            if (!hash_equals((string) $_SESSION['csrf_token'], $csrfToken)) {
                $error = 'La solicitud no es válida.';
                $query = http_build_query(['module' => 'socios', 'action' => 'rifas', 'client' => $_GET['client'] ?? '', 'error' => $error]);
                header('Location: index.php?' . $query);
                exit;
            }

            try {
                $clienteCodigo = trim((string) ($_GET['client'] ?? ''));
                $codigo = trim((string) ($_GET['codigo'] ?? ''));

                if ($clienteCodigo === '' || $codigo === '') {
                    throw new Exception('Datos incompletos');
                }

                $resultado = $sociosServicio->eliminarRifa((int) $codigo, $_SESSION['user']['username'] ?? 'SISTEMA');

                if ($resultado['success']) {
                    $query = http_build_query(['module' => 'socios', 'action' => 'rifas', 'client' => $clienteCodigo, 'success' => $resultado['message']]);
                    header('Location: index.php?' . $query);
                    exit;
                } else {
                    $query = http_build_query(['module' => 'socios', 'action' => 'rifas', 'client' => $clienteCodigo, 'error' => $resultado['message']]);
                    header('Location: index.php?' . $query);
                    exit;
                }

            } catch (Exception $e) {
                $error = 'Error al eliminar rifa: ' . $e->getMessage();
                $query = http_build_query(['module' => 'socios', 'action' => 'rifas', 'client' => $_GET['client'] ?? '', 'error' => $error]);
                header('Location: index.php?' . $query);
                exit;
            }
        }

        // POST: Guardar multa
        if ($action === 'multa_guardar' && $_SERVER['REQUEST_METHOD'] === 'POST') {
            $csrfToken = (string) ($_POST['csrf_token'] ?? '');
            if (!hash_equals((string) $_SESSION['csrf_token'], $csrfToken)) {
                $error = 'La solicitud no es válida.';
                $query = http_build_query(['module' => 'socios', 'action' => 'multas', 'client' => $_POST['client'] ?? '', 'error' => $error]);
                header('Location: index.php?' . $query);
                exit;
            }

            try {
                $modo = trim((string) ($_POST['modo'] ?? ''));
                $clienteCodigo = trim((string) ($_POST['client'] ?? ''));
                
                $datos = [
                    'socio' => $clienteCodigo,
                    'periodo' => trim((string) ($_POST['periodo'] ?? '')),
                    'multap' => trim((string) ($_POST['multap'] ?? '0')),
                    'multad' => trim((string) ($_POST['multad'] ?? '0')),
                    'multar' => trim((string) ($_POST['multar'] ?? '0')),
                    'fechaPago' => trim((string) ($_POST['fechaPago'] ?? '')),
                    'formaPago' => trim((string) ($_POST['formaPago'] ?? '')),
                ];

                if ($datos['periodo'] === '') {
                    throw new Exception('Periodo es requerido');
                }

                if ($modo === 'nuevo') {
                    $resultado = $sociosServicio->registrarMulta($datos, $_SESSION['user']['username'] ?? 'SISTEMA');
                } else {
                    $codigo = trim((string) ($_POST['codigo'] ?? ''));
                    if ($codigo === '') {
                        throw new Exception('Código no especificado');
                    }
                    $resultado = $sociosServicio->actualizarMulta((int) $codigo, $datos, $_SESSION['user']['username'] ?? 'SISTEMA');
                }

                if ($resultado['success']) {
                    $query = http_build_query(['module' => 'socios', 'action' => 'multas', 'client' => $clienteCodigo, 'success' => $resultado['message']]);
                    header('Location: index.php?' . $query);
                    exit;
                } else {
                    $query = http_build_query(['module' => 'socios', 'action' => $modo === 'nuevo' ? 'multa_nuevo' : 'multa_editar', 'client' => $clienteCodigo, 'codigo' => $_POST['codigo'] ?? '', 'error' => $resultado['message']]);
                    header('Location: index.php?' . $query);
                    exit;
                }

            } catch (Exception $e) {
                $error = 'Error al guardar multa: ' . $e->getMessage();
                $query = http_build_query(['module' => 'socios', 'action' => 'multas', 'client' => $_POST['client'] ?? '', 'error' => $error]);
                header('Location: index.php?' . $query);
                exit;
            }
        }

        // GET: Listado de multas
        if ($action === 'multas') {
            $clienteCodigo = trim((string) ($_GET['client'] ?? ''));
            
            if ($clienteCodigo === '') {
                http_response_code(400);
                $error = 'Cliente no especificado';
                require dirname(__DIR__) . '/views/home.php';
                exit;
            }
            
            require dirname(__DIR__) . '/views/socios/multas.php';
            exit;
        }

        // GET: Nueva multa
        if ($action === 'multa_nuevo') {
            $clienteCodigo = trim((string) ($_GET['client'] ?? ''));
            
            if ($clienteCodigo === '') {
                http_response_code(400);
                $error = 'Cliente no especificado';
                require dirname(__DIR__) . '/views/home.php';
                exit;
            }
            
            require dirname(__DIR__) . '/views/socios/multa_form.php';
            exit;
        }

        // GET: Editar multa
        if ($action === 'multa_editar') {
            $clienteCodigo = trim((string) ($_GET['client'] ?? ''));
            $codigo = trim((string) ($_GET['codigo'] ?? ''));
            
            if ($clienteCodigo === '' || $codigo === '') {
                http_response_code(400);
                $error = 'Cliente o código no especificado';
                require dirname(__DIR__) . '/views/home.php';
                exit;
            }
            
            require dirname(__DIR__) . '/views/socios/multa_form.php';
            exit;
        }

        // GET: Eliminar multa
        if ($action === 'multa_eliminar') {
            $csrfToken = (string) ($_GET['csrf_token'] ?? '');
            if (!hash_equals((string) $_SESSION['csrf_token'], $csrfToken)) {
                $error = 'La solicitud no es válida.';
                $query = http_build_query(['module' => 'socios', 'action' => 'multas', 'client' => $_GET['client'] ?? '', 'error' => $error]);
                header('Location: index.php?' . $query);
                exit;
            }

            try {
                $clienteCodigo = trim((string) ($_GET['client'] ?? ''));
                $codigo = trim((string) ($_GET['codigo'] ?? ''));

                if ($clienteCodigo === '' || $codigo === '') {
                    throw new Exception('Datos incompletos');
                }

                $resultado = $sociosServicio->eliminarMulta((int) $codigo, $_SESSION['user']['username'] ?? 'SISTEMA');

                if ($resultado['success']) {
                    $query = http_build_query(['module' => 'socios', 'action' => 'multas', 'client' => $clienteCodigo, 'success' => $resultado['message']]);
                    header('Location: index.php?' . $query);
                    exit;
                } else {
                    $query = http_build_query(['module' => 'socios', 'action' => 'multas', 'client' => $clienteCodigo, 'error' => $resultado['message']]);
                    header('Location: index.php?' . $query);
                    exit;
                }

            } catch (Exception $e) {
                $error = 'Error al eliminar multa: ' . $e->getMessage();
                $query = http_build_query(['module' => 'socios', 'action' => 'multas', 'client' => $_GET['client'] ?? '', 'error' => $error]);
                header('Location: index.php?' . $query);
                exit;
            }
        }

        // POST: Guardar cheque
        if ($action === 'cheque_guardar' && $_SERVER['REQUEST_METHOD'] === 'POST') {
            $csrfToken = (string) ($_POST['csrf_token'] ?? '');
            if (!hash_equals((string) $_SESSION['csrf_token'], $csrfToken)) {
                $error = 'La solicitud no es válida.';
                $query = http_build_query(['module' => 'socios', 'action' => 'cheques', 'error' => $error]);
                header('Location: index.php?' . $query);
                exit;
            }

            try {
                $modo = trim((string) ($_POST['modo'] ?? ''));
                
                $datos = [
                    'nombre' => trim((string) ($_POST['nombre'] ?? '')),
                    'monto' => trim((string) ($_POST['monto'] ?? '')),
                    'montoLetras' => trim((string) ($_POST['montoLetras'] ?? '')),
                    'fecha' => trim((string) ($_POST['fecha'] ?? '')),
                    'cheque' => trim((string) ($_POST['cheque'] ?? '')),
                    'motivo' => trim((string) ($_POST['motivo'] ?? '')),
                    'centro' => trim((string) ($_POST['centro'] ?? '')),
                    'condicion' => trim((string) ($_POST['condicion'] ?? '')),
                ];

                if ($datos['nombre'] === '' || $datos['monto'] === '' || $datos['montoLetras'] === '') {
                    throw new Exception('Nombre, monto y monto en letras son requeridos');
                }

                if ($modo === 'nuevo') {
                    $resultado = $sociosServicio->registrarCheque($datos, $_SESSION['user']['username'] ?? 'SISTEMA');
                } else {
                    $codigo = trim((string) ($_POST['codigo'] ?? ''));
                    if ($codigo === '') {
                        throw new Exception('Código no especificado');
                    }
                    $resultado = $sociosServicio->actualizarCheque((int) $codigo, $datos, $_SESSION['user']['username'] ?? 'SISTEMA');
                }

                if ($resultado['success']) {
                    $query = http_build_query(['module' => 'socios', 'action' => 'cheques', 'success' => $resultado['message']]);
                    header('Location: index.php?' . $query);
                    exit;
                } else {
                    $query = http_build_query(['module' => 'socios', 'action' => $modo === 'nuevo' ? 'cheque_nuevo' : 'cheque_editar', 'codigo' => $_POST['codigo'] ?? '', 'error' => $resultado['message']]);
                    header('Location: index.php?' . $query);
                    exit;
                }

            } catch (Exception $e) {
                $error = 'Error al guardar cheque: ' . $e->getMessage();
                $query = http_build_query(['module' => 'socios', 'action' => 'cheques', 'error' => $error]);
                header('Location: index.php?' . $query);
                exit;
            }
        }

        // GET: Listado de cheques
        if ($action === 'cheques') {
            require dirname(__DIR__) . '/views/socios/cheques.php';
            exit;
        }

        // GET: Nuevo cheque
        if ($action === 'cheque_nuevo') {
            require dirname(__DIR__) . '/views/socios/cheque_form.php';
            exit;
        }

        // GET: Editar cheque
        if ($action === 'cheque_editar') {
            $codigo = trim((string) ($_GET['codigo'] ?? ''));
            
            if ($codigo === '') {
                http_response_code(400);
                $error = 'Código no especificado';
                require dirname(__DIR__) . '/views/home.php';
                exit;
            }
            
            require dirname(__DIR__) . '/views/socios/cheque_form.php';
            exit;
        }

        // GET: Eliminar cheque
        if ($action === 'cheque_eliminar') {
            $csrfToken = (string) ($_GET['csrf_token'] ?? '');
            if (!hash_equals((string) $_SESSION['csrf_token'], $csrfToken)) {
                $error = 'La solicitud no es válida.';
                $query = http_build_query(['module' => 'socios', 'action' => 'cheques', 'error' => $error]);
                header('Location: index.php?' . $query);
                exit;
            }

            try {
                $codigo = trim((string) ($_GET['codigo'] ?? ''));

                if ($codigo === '') {
                    throw new Exception('Código no especificado');
                }

                $resultado = $sociosServicio->eliminarCheque((int) $codigo, $_SESSION['user']['username'] ?? 'SISTEMA');

                if ($resultado['success']) {
                    $query = http_build_query(['module' => 'socios', 'action' => 'cheques', 'success' => $resultado['message']]);
                    header('Location: index.php?' . $query);
                    exit;
                } else {
                    $query = http_build_query(['module' => 'socios', 'action' => 'cheques', 'error' => $resultado['message']]);
                    header('Location: index.php?' . $query);
                    exit;
                }

            } catch (Exception $e) {
                $error = 'Error al eliminar cheque: ' . $e->getMessage();
                $query = http_build_query(['module' => 'socios', 'action' => 'cheques', 'error' => $error]);
                header('Location: index.php?' . $query);
                exit;
            }
        }

        // GET: Carta
        if ($action === 'carta') {
            require dirname(__DIR__) . '/views/socios/carta.php';
            exit;
        }

        // POST: Guardar carta
        if ($action === 'carta_guardar' && $_SERVER['REQUEST_METHOD'] === 'POST') {
            $csrfToken = (string) ($_POST['csrf_token'] ?? '');
            if (!hash_equals((string) $_SESSION['csrf_token'], $csrfToken)) {
                $error = 'La solicitud no es válida.';
                $query = http_build_query(['module' => 'socios', 'action' => 'carta', 'client' => $_POST['client'] ?? '', 'error' => $error]);
                header('Location: index.php?' . $query);
                exit;
            }

            try {
                $sociosServicio = new \Sacs\Services\SociosServicio();
                $clienteCodigo = trim((string) ($_POST['client'] ?? ''));
                $firmo = isset($_POST['firmo']) && $_POST['firmo'] === '1' ? '1' : '0';
                $carta = trim((string) ($_POST['carta'] ?? ''));
                $fechaBanco = trim((string) ($_POST['fecha_banco'] ?? ''));
                $obsCarta = trim((string) ($_POST['obs_carta'] ?? ''));
                $usuario = $_SESSION['sacs_user']['username'] ?? 'SISTEMA';

                if (empty($clienteCodigo)) {
                    throw new Exception('El código de cliente es requerido');
                }

                // Obtener código de persona
                $socio = $sociosServicio->obtenerSocioPorCliente($clienteCodigo);
                if (!$socio) {
                    throw new Exception('Socio no encontrado');
                }

                $codigoPersona = (string) $socio['F01_ID_PERSONA'];

                // Actualizar carta
                $resultado = $sociosServicio->actualizarCarta($codigoPersona, $firmo, $carta, $fechaBanco, $obsCarta, $usuario);

                if ($resultado['success']) {
                    $query = http_build_query(['module' => 'socios', 'action' => 'info', 'client' => $clienteCodigo, 'success' => 'Carta guardada correctamente']);
                    header('Location: index.php?' . $query);
                    exit;
                } else {
                    $error = 'Error al guardar la carta';
                    $query = http_build_query(['module' => 'socios', 'action' => 'carta', 'client' => $clienteCodigo, 'error' => $error]);
                    header('Location: index.php?' . $query);
                    exit;
                }

            } catch (Exception $e) {
                $error = 'Error al guardar carta: ' . $e->getMessage();
                $query = http_build_query(['module' => 'socios', 'action' => 'carta', 'client' => $_POST['client'] ?? '', 'error' => $error]);
                header('Location: index.php?' . $query);
                exit;
            }
        }

        // GET: Sobregiro
        if ($action === 'sobregiro') {
            require dirname(__DIR__) . '/views/socios/sobregiro.php';
            exit;
        }

        // GET: Aportaciones Completas
        if ($action === 'aportes_completos') {
            require dirname(__DIR__) . '/views/socios/aportes_completos.php';
            exit;
        }

        // GET: Descuentos
        if ($action === 'descuentos') {
            require dirname(__DIR__) . '/views/socios/descuentos.php';
            exit;
        }

        // GET: Nuevo Descuento
        if ($action === 'descuento_nuevo') {
            require dirname(__DIR__) . '/views/socios/descuento_form.php';
            exit;
        }

        // GET: Editar Descuento
        if ($action === 'descuento_editar') {
            require dirname(__DIR__) . '/views/socios/descuento_form.php';
            exit;
        }

        // POST: Guardar Descuento
        if ($action === 'descuento_guardar' && $_SERVER['REQUEST_METHOD'] === 'POST') {
            $csrfToken = (string) ($_POST['csrf_token'] ?? '');
            if (!hash_equals((string) $_SESSION['csrf_token'], $csrfToken)) {
                $error = 'La solicitud no es válida.';
                $query = http_build_query(['module' => 'socios', 'action' => 'descuentos', 'client' => $_POST['client'] ?? '', 'error' => $error]);
                header('Location: index.php?' . $query);
                exit;
            }

            try {
                $sociosServicio = new \Sacs\Services\SociosServicio();
                $clienteCodigo = trim((string) ($_POST['client'] ?? ''));
                $modo = trim((string) ($_POST['modo'] ?? 'nuevo'));
                $periodo = trim((string) ($_POST['periodo'] ?? ''));
                $usuario = $_SESSION['sacs_user']['username'] ?? 'SISTEMA';

                if (empty($clienteCodigo)) {
                    throw new Exception('El código de cliente es requerido');
                }

                if (empty($periodo)) {
                    throw new Exception('El periodo es requerido');
                }

                // Obtener código de persona
                $socio = $sociosServicio->obtenerSocioPorCliente($clienteCodigo);
                if (!$socio) {
                    throw new Exception('Socio no encontrado');
                }

                $codigoPersona = (string) $socio['F01_ID_PERSONA'];

                $datos = [
                    'aportacion_pendiente' => (float) ($_POST['aportacion_pendiente'] ?? 0),
                    'aportacion_actual' => (float) ($_POST['aportacion_actual'] ?? 0),
                    'aportacion_deducida' => (float) ($_POST['aportacion_deducida'] ?? 0),
                    'monto_pendiente' => (float) ($_POST['monto_pendiente'] ?? 0),
                    'monto_actual' => (float) ($_POST['monto_actual'] ?? 0),
                    'monto_deducido' => (float) ($_POST['monto_deducido'] ?? 0),
                    'amortizacion' => (float) ($_POST['amortizacion'] ?? 0),
                    'mora_activa' => (float) ($_POST['mora_activa'] ?? 0),
                    'mora_deducida' => (float) ($_POST['mora_deducida'] ?? 0),
                    'interes_activo' => (float) ($_POST['interes_activo'] ?? 0),
                    'interes_deducido' => (float) ($_POST['interes_deducido'] ?? 0),
                    'otro1' => (float) ($_POST['otro1'] ?? 0),
                    'otro2' => (float) ($_POST['otro2'] ?? 0),
                    'mora_pendiente' => (float) ($_POST['mora_pendiente'] ?? 0),
                    'tipo' => trim((string) ($_POST['tipo'] ?? ''))
                ];

                if ($modo === 'nuevo') {
                    // Verificar si ya existe descuento para este periodo
                    $existente = $sociosServicio->obtenerDescuento($codigoPersona, $periodo);
                    if ($existente) {
                        throw new Exception('Ya existe un descuento para este periodo');
                    }
                    $resultado = $sociosServicio->registrarDescuento($codigoPersona, $periodo, $datos, $usuario);
                } else {
                    $periodoOriginal = trim((string) ($_POST['periodo_original'] ?? ''));
                    $resultado = $sociosServicio->actualizarDescuento($codigoPersona, $periodoOriginal, $datos, $usuario);
                }

                if ($resultado['success']) {
                    $query = http_build_query(['module' => 'socios', 'action' => 'descuentos', 'client' => $clienteCodigo, 'success' => $modo === 'nuevo' ? 'Descuento registrado correctamente' : 'Descuento actualizado correctamente']);
                    header('Location: index.php?' . $query);
                    exit;
                } else {
                    $error = 'Error al guardar descuento';
                    $query = http_build_query(['module' => 'socios', 'action' => $modo === 'nuevo' ? 'descuento_nuevo' : 'descuento_editar', 'client' => $clienteCodigo, 'periodo' => $periodo, 'error' => $error]);
                    header('Location: index.php?' . $query);
                    exit;
                }

            } catch (Exception $e) {
                $error = 'Error al guardar descuento: ' . $e->getMessage();
                $query = http_build_query(['module' => 'socios', 'action' => $modo === 'nuevo' ? 'descuento_nuevo' : 'descuento_editar', 'client' => $_POST['client'] ?? '', 'periodo' => $_POST['periodo'] ?? '', 'error' => $error]);
                header('Location: index.php?' . $query);
                exit;
            }
        }

        // GET: Eliminar Descuento
        if ($action === 'descuento_eliminar') {
            $csrfToken = (string) ($_GET['csrf_token'] ?? '');
            if (!hash_equals((string) $_SESSION['csrf_token'], $csrfToken)) {
                $error = 'La solicitud no es válida.';
                $query = http_build_query(['module' => 'socios', 'action' => 'descuentos', 'client' => $_GET['client'] ?? '', 'error' => $error]);
                header('Location: index.php?' . $query);
                exit;
            }

            try {
                $sociosServicio = new \Sacs\Services\SociosServicio();
                $clienteCodigo = trim((string) ($_GET['client'] ?? ''));
                $periodo = trim((string) ($_GET['periodo'] ?? ''));
                $usuario = $_SESSION['sacs_user']['username'] ?? 'SISTEMA';

                if (empty($clienteCodigo) || empty($periodo)) {
                    throw new Exception('Parámetros inválidos');
                }

                // Obtener código de persona
                $socio = $sociosServicio->obtenerSocioPorCliente($clienteCodigo);
                if (!$socio) {
                    throw new Exception('Socio no encontrado');
                }

                $codigoPersona = (string) $socio['F01_ID_PERSONA'];

                $resultado = $sociosServicio->eliminarDescuento($codigoPersona, $periodo, $usuario);

                if ($resultado['success']) {
                    $query = http_build_query(['module' => 'socios', 'action' => 'descuentos', 'client' => $clienteCodigo, 'success' => 'Descuento eliminado correctamente']);
                    header('Location: index.php?' . $query);
                    exit;
                } else {
                    $error = 'Error al eliminar descuento';
                    $query = http_build_query(['module' => 'socios', 'action' => 'descuentos', 'client' => $clienteCodigo, 'error' => $error]);
                    header('Location: index.php?' . $query);
                    exit;
                }

            } catch (Exception $e) {
                $error = 'Error al eliminar descuento: ' . $e->getMessage();
                $query = http_build_query(['module' => 'socios', 'action' => 'descuentos', 'client' => $_GET['client'] ?? '', 'error' => $error]);
                header('Location: index.php?' . $query);
                exit;
            }
        }

        // GET: Socios Atrasados
        if ($action === 'socios_atrasados') {
            require dirname(__DIR__) . '/views/socios/socios_atrasados.php';
            exit;
        }

        // GET: Socios Candidatos
        if ($action === 'socios_candidatos') {
            require dirname(__DIR__) . '/views/socios/socios_candidatos.php';
            exit;
        }

        // GET: Socios Pronóstico
        if ($action === 'socios_pronostico') {
            require dirname(__DIR__) . '/views/socios/socios_pronostico.php';
            exit;
        }

        // GET: Observaciones
        if ($action === 'observaciones') {
            require dirname(__DIR__) . '/views/socios/observaciones.php';
            exit;
        }

        // POST: Guardar observación
        if ($action === 'observacion_guardar' && $_SERVER['REQUEST_METHOD'] === 'POST') {
            $csrfToken = (string) ($_POST['csrf_token'] ?? '');
            if (!hash_equals((string) $_SESSION['csrf_token'], $csrfToken)) {
                $error = 'La solicitud no es válida.';
                $query = http_build_query(['module' => 'socios', 'action' => 'observaciones', 'client' => $_POST['client'] ?? '', 'error' => $error]);
                header('Location: index.php?' . $query);
                exit;
            }

            try {
                $sociosServicio = new \Sacs\Services\SociosServicio();
                $clienteCodigo = trim((string) ($_POST['client'] ?? ''));
                $titulo = trim((string) ($_POST['titulo'] ?? ''));
                $observacion = trim((string) ($_POST['observacion'] ?? ''));
                $usuario = $_SESSION['sacs_user']['username'] ?? 'SISTEMA';

                if (empty($clienteCodigo)) {
                    throw new Exception('El código de cliente es requerido');
                }

                if (empty($titulo)) {
                    throw new Exception('El título es requerido');
                }

                if (empty($observacion)) {
                    throw new Exception('La observación es requerida');
                }

                // Obtener código de persona
                $socio = $sociosServicio->obtenerSocioPorCliente($clienteCodigo);
                if (!$socio) {
                    throw new Exception('Socio no encontrado');
                }

                $codigoPersona = (string) $socio['F01_ID_PERSONA'];

                // Actualizar observación en f01_persona
                $resultadoActualizacion = $sociosServicio->actualizarObservacion($codigoPersona, $titulo, $observacion, $usuario);

                // Registrar en historial f28_obs_detalle
                $resultadoHistorial = $sociosServicio->registrarObservacionHistorial($codigoPersona, $titulo, $observacion, $usuario);

                if ($resultadoActualizacion['success'] && $resultadoHistorial['success']) {
                    $query = http_build_query(['module' => 'socios', 'action' => 'info', 'client' => $clienteCodigo, 'success' => 'Observación guardada correctamente']);
                    header('Location: index.php?' . $query);
                    exit;
                } else {
                    $error = 'Error al guardar la observación';
                    $query = http_build_query(['module' => 'socios', 'action' => 'observaciones', 'client' => $clienteCodigo, 'error' => $error]);
                    header('Location: index.php?' . $query);
                    exit;
                }

            } catch (Exception $e) {
                $error = 'Error al guardar observación: ' . $e->getMessage();
                $query = http_build_query(['module' => 'socios', 'action' => 'observaciones', 'client' => $_POST['client'] ?? '', 'error' => $error]);
                header('Location: index.php?' . $query);
                exit;
            }
        }

        // GET: Exportar socios CSV
        if ($action === 'exportar_socios_csv') {
            try {
                $exportacionServicio = new \Sacs\Services\ExportacionServicio();
                
                $filtros = [
                    'nombre' => trim((string) ($_GET['nombre'] ?? '')),
                    'documento' => trim((string) ($_GET['documento'] ?? '')),
                    'centro' => trim((string) ($_GET['centro'] ?? '')),
                    'condicion' => trim((string) ($_GET['condicion'] ?? '')),
                    'estado' => trim((string) ($_GET['estado'] ?? '')),
                ];
                
                $csv = $exportacionServicio->exportarSociosCSV($filtros);
                
                $filename = 'socios_' . date('Y-m-d') . '.csv';
                
                header('Content-Type: text/csv; charset=utf-8');
                header('Content-Disposition: attachment; filename="' . $filename . '"');
                header('Pragma: no-cache');
                header('Expires: 0');
                
                echo $csv;
                exit;
                
            } catch (Exception $e) {
                $error = 'Error al exportar socios: ' . $e->getMessage();
                $query = http_build_query(['module' => 'socios', 'action' => 'listado', 'error' => $error]);
                header('Location: index.php?' . $query);
                exit;
            }
        }

        // GET: Exportar familiares CSV
        if ($action === 'exportar_familiares_csv') {
            try {
                $exportacionServicio = new \Sacs\Services\ExportacionServicio();
                
                $filtros = [
                    'cliente_codigo' => trim((string) ($_GET['client'] ?? '')),
                    'inactivos' => trim((string) ($_GET['inactivos'] ?? '')) === 'true',
                ];
                
                $csv = $exportacionServicio->exportarFamiliaresCSV($filtros);
                
                $filename = 'familiares_' . date('Y-m-d') . '.csv';
                
                header('Content-Type: text/csv; charset=utf-8');
                header('Content-Disposition: attachment; filename="' . $filename . '"');
                header('Pragma: no-cache');
                header('Expires: 0');
                
                echo $csv;
                exit;
                
            } catch (Exception $e) {
                $error = 'Error al exportar familiares: ' . $e->getMessage();
                $query = http_build_query(['module' => 'socios', 'action' => 'familiares', 'client' => $_GET['client'] ?? '', 'error' => $error]);
                header('Location: index.php?' . $query);
                exit;
            }
        }

        // GET: Exportar aportes FONBES CSV
        if ($action === 'exportar_aportes_fonbes_csv') {
            try {
                $exportacionServicio = new \Sacs\Services\ExportacionServicio();
                
                $filtros = [
                    'cliente_codigo' => trim((string) ($_GET['client'] ?? '')),
                ];
                
                $csv = $exportacionServicio->exportarAportesFonbesCSV($filtros);
                
                $filename = 'aportes_fonbes_' . date('Y-m-d') . '.csv';
                
                header('Content-Type: text/csv; charset=utf-8');
                header('Content-Disposition: attachment; filename="' . $filename . '"');
                header('Pragma: no-cache');
                header('Expires: 0');
                
                echo $csv;
                exit;
                
            } catch (Exception $e) {
                $error = 'Error al exportar aportes FONBES: ' . $e->getMessage();
                $query = http_build_query(['module' => 'socios', 'action' => 'aportes_fonbes', 'client' => $_GET['client'] ?? '', 'error' => $error]);
                header('Location: index.php?' . $query);
                exit;
            }
        }

        // GET: Exportar aportes COOPAM CSV
        if ($action === 'exportar_aportes_coopam_csv') {
            try {
                $exportacionServicio = new \Sacs\Services\ExportacionServicio();
                
                $filtros = [
                    'cliente_codigo' => trim((string) ($_GET['client'] ?? '')),
                ];
                
                $csv = $exportacionServicio->exportarAportesCoopamCSV($filtros);
                
                $filename = 'aportes_coopam_' . date('Y-m-d') . '.csv';
                
                header('Content-Type: text/csv; charset=utf-8');
                header('Content-Disposition: attachment; filename="' . $filename . '"');
                header('Pragma: no-cache');
                header('Expires: 0');
                
                echo $csv;
                exit;
                
            } catch (Exception $e) {
                $error = 'Error al exportar aportes COOPAM: ' . $e->getMessage();
                $query = http_build_query(['module' => 'socios', 'action' => 'aportes_coopam', 'client' => $_GET['client'] ?? '', 'error' => $error]);
                header('Location: index.php?' . $query);
                exit;
            }
        }

        // GET: Exportar beneficios CSV
        if ($action === 'exportar_beneficios_csv') {
            try {
                $exportacionServicio = new \Sacs\Services\ExportacionServicio();
                
                $filtros = [
                    'cliente_codigo' => trim((string) ($_GET['client'] ?? '')),
                    'fecha_desde' => trim((string) ($_GET['fecha_desde'] ?? '')),
                    'fecha_hasta' => trim((string) ($_GET['fecha_hasta'] ?? '')),
                ];
                
                $csv = $exportacionServicio->exportarBeneficiosCSV($filtros);
                
                $filename = 'beneficios_' . date('Y-m-d') . '.csv';
                
                header('Content-Type: text/csv; charset=utf-8');
                header('Content-Disposition: attachment; filename="' . $filename . '"');
                header('Pragma: no-cache');
                header('Expires: 0');
                
                echo $csv;
                exit;
                
            } catch (Exception $e) {
                $error = 'Error al exportar beneficios: ' . $e->getMessage();
                $query = http_build_query(['module' => 'socios', 'action' => 'beneficios', 'client' => $_GET['client'] ?? '', 'error' => $error]);
                header('Location: index.php?' . $query);
                exit;
            }
        }

        // GET: Exportar rifas CSV
        if ($action === 'exportar_rifas_csv') {
            try {
                $exportacionServicio = new \Sacs\Services\ExportacionServicio();
                
                $filtros = [
                    'cliente_codigo' => trim((string) ($_GET['client'] ?? '')),
                    'fecha_desde' => trim((string) ($_GET['fecha_desde'] ?? '')),
                    'fecha_hasta' => trim((string) ($_GET['fecha_hasta'] ?? '')),
                ];
                
                $csv = $exportacionServicio->exportarRifasCSV($filtros);
                
                $filename = 'rifas_' . date('Y-m-d') . '.csv';
                
                header('Content-Type: text/csv; charset=utf-8');
                header('Content-Disposition: attachment; filename="' . $filename . '"');
                header('Pragma: no-cache');
                header('Expires: 0');
                
                echo $csv;
                exit;
                
            } catch (Exception $e) {
                $error = 'Error al exportar rifas: ' . $e->getMessage();
                $query = http_build_query(['module' => 'socios', 'action' => 'rifas', 'client' => $_GET['client'] ?? '', 'error' => $error]);
                header('Location: index.php?' . $query);
                exit;
            }
        }

        // GET: Exportar multas CSV
        if ($action === 'exportar_multas_csv') {
            try {
                $exportacionServicio = new \Sacs\Services\ExportacionServicio();
                
                $filtros = [
                    'cliente_codigo' => trim((string) ($_GET['client'] ?? '')),
                    'fecha_desde' => trim((string) ($_GET['fecha_desde'] ?? '')),
                    'fecha_hasta' => trim((string) ($_GET['fecha_hasta'] ?? '')),
                ];
                
                $csv = $exportacionServicio->exportarMultasCSV($filtros);
                
                $filename = 'multas_' . date('Y-m-d') . '.csv';
                
                header('Content-Type: text/csv; charset=utf-8');
                header('Content-Disposition: attachment; filename="' . $filename . '"');
                header('Pragma: no-cache');
                header('Expires: 0');
                
                echo $csv;
                exit;
                
            } catch (Exception $e) {
                $error = 'Error al exportar multas: ' . $e->getMessage();
                $query = http_build_query(['module' => 'socios', 'action' => 'multas', 'client' => $_GET['client'] ?? '', 'error' => $error]);
                header('Location: index.php?' . $query);
                exit;
            }
        }

        // GET: Exportar cheques CSV
        if ($action === 'exportar_cheques_csv') {
            try {
                $exportacionServicio = new \Sacs\Services\ExportacionServicio();
                
                $filtros = [
                    'fecha_desde' => trim((string) ($_GET['fecha_desde'] ?? '')),
                    'fecha_hasta' => trim((string) ($_GET['fecha_hasta'] ?? '')),
                ];
                
                $csv = $exportacionServicio->exportarChequesCSV($filtros);
                
                $filename = 'cheques_' . date('Y-m-d') . '.csv';
                
                header('Content-Type: text/csv; charset=utf-8');
                header('Content-Disposition: attachment; filename="' . $filename . '"');
                header('Pragma: no-cache');
                header('Expires: 0');
                
                echo $csv;
                exit;
                
            } catch (Exception $e) {
                $error = 'Error al exportar cheques: ' . $e->getMessage();
                $query = http_build_query(['module' => 'socios', 'action' => 'cheques', 'error' => $error]);
                header('Location: index.php?' . $query);
                exit;
            }
        }

        // GET: Exportar socios Excel
        if ($action === 'exportar_socios_excel') {
            try {
                $exportacionServicio = new \Sacs\Services\ExportacionServicio();
                
                $filtros = [
                    'nombre' => trim((string) ($_GET['nombre'] ?? '')),
                    'documento' => trim((string) ($_GET['documento'] ?? '')),
                    'centro' => trim((string) ($_GET['centro'] ?? '')),
                    'condicion' => trim((string) ($_GET['condicion'] ?? '')),
                    'estado' => trim((string) ($_GET['estado'] ?? '')),
                ];
                
                $excel = $exportacionServicio->exportarSociosExcel($filtros);
                
                $filename = 'socios_' . date('Y-m-d') . '.xlsx';
                
                header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
                header('Content-Disposition: attachment; filename="' . $filename . '"');
                header('Pragma: no-cache');
                header('Expires: 0');
                
                echo $excel;
                exit;
                
            } catch (Exception $e) {
                $error = 'Error al exportar socios: ' . $e->getMessage();
                $query = http_build_query(['module' => 'socios', 'action' => 'listado', 'error' => $error]);
                header('Location: index.php?' . $query);
                exit;
            }
        }

        // GET: Exportar familiares Excel
        if ($action === 'exportar_familiares_excel') {
            try {
                $exportacionServicio = new \Sacs\Services\ExportacionServicio();
                
                $filtros = [
                    'cliente_codigo' => trim((string) ($_GET['client'] ?? '')),
                    'inactivos' => trim((string) ($_GET['inactivos'] ?? '')) === 'true',
                ];
                
                $excel = $exportacionServicio->exportarFamiliaresExcel($filtros);
                
                $filename = 'familiares_' . date('Y-m-d') . '.xlsx';
                
                header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
                header('Content-Disposition: attachment; filename="' . $filename . '"');
                header('Pragma: no-cache');
                header('Expires: 0');
                
                echo $excel;
                exit;
                
            } catch (Exception $e) {
                $error = 'Error al exportar familiares: ' . $e->getMessage();
                $query = http_build_query(['module' => 'socios', 'action' => 'familiares', 'client' => $_GET['client'] ?? '', 'error' => $error]);
                header('Location: index.php?' . $query);
                exit;
            }
        }

        // GET: Exportar aportes FONBES Excel
        if ($action === 'exportar_aportes_fonbes_excel') {
            try {
                $exportacionServicio = new \Sacs\Services\ExportacionServicio();
                
                $filtros = [
                    'cliente_codigo' => trim((string) ($_GET['client'] ?? '')),
                ];
                
                $excel = $exportacionServicio->exportarAportesFonbesExcel($filtros);
                
                $filename = 'aportes_fonbes_' . date('Y-m-d') . '.xlsx';
                
                header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
                header('Content-Disposition: attachment; filename="' . $filename . '"');
                header('Pragma: no-cache');
                header('Expires: 0');
                
                echo $excel;
                exit;
                
            } catch (Exception $e) {
                $error = 'Error al exportar aportes FONBES: ' . $e->getMessage();
                $query = http_build_query(['module' => 'socios', 'action' => 'aportes_fonbes', 'client' => $_GET['client'] ?? '', 'error' => $error]);
                header('Location: index.php?' . $query);
                exit;
            }
        }

        // GET: Exportar aportes COOPAM Excel
        if ($action === 'exportar_aportes_coopam_excel') {
            try {
                $exportacionServicio = new \Sacs\Services\ExportacionServicio();
                
                $filtros = [
                    'cliente_codigo' => trim((string) ($_GET['client'] ?? '')),
                ];
                
                $excel = $exportacionServicio->exportarAportesCoopamExcel($filtros);
                
                $filename = 'aportes_coopam_' . date('Y-m-d') . '.xlsx';
                
                header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
                header('Content-Disposition: attachment; filename="' . $filename . '"');
                header('Pragma: no-cache');
                header('Expires: 0');
                
                echo $excel;
                exit;
                
            } catch (Exception $e) {
                $error = 'Error al exportar aportes COOPAM: ' . $e->getMessage();
                $query = http_build_query(['module' => 'socios', 'action' => 'aportes_coopam', 'client' => $_GET['client'] ?? '', 'error' => $error]);
                header('Location: index.php?' . $query);
                exit;
            }
        }

        // GET: Exportar beneficios Excel
        if ($action === 'exportar_beneficios_excel') {
            try {
                $exportacionServicio = new \Sacs\Services\ExportacionServicio();
                
                $filtros = [
                    'cliente_codigo' => trim((string) ($_GET['client'] ?? '')),
                    'fecha_desde' => trim((string) ($_GET['fecha_desde'] ?? '')),
                    'fecha_hasta' => trim((string) ($_GET['fecha_hasta'] ?? '')),
                ];
                
                $excel = $exportacionServicio->exportarBeneficiosExcel($filtros);
                
                $filename = 'beneficios_' . date('Y-m-d') . '.xlsx';
                
                header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
                header('Content-Disposition: attachment; filename="' . $filename . '"');
                header('Pragma: no-cache');
                header('Expires: 0');
                
                echo $excel;
                exit;
                
            } catch (Exception $e) {
                $error = 'Error al exportar beneficios: ' . $e->getMessage();
                $query = http_build_query(['module' => 'socios', 'action' => 'beneficios', 'client' => $_GET['client'] ?? '', 'error' => $error]);
                header('Location: index.php?' . $query);
                exit;
            }
        }

        // GET: Exportar rifas Excel
        if ($action === 'exportar_rifas_excel') {
            try {
                $exportacionServicio = new \Sacs\Services\ExportacionServicio();
                
                $filtros = [
                    'cliente_codigo' => trim((string) ($_GET['client'] ?? '')),
                    'fecha_desde' => trim((string) ($_GET['fecha_desde'] ?? '')),
                    'fecha_hasta' => trim((string) ($_GET['fecha_hasta'] ?? '')),
                ];
                
                $excel = $exportacionServicio->exportarRifasExcel($filtros);
                
                $filename = 'rifas_' . date('Y-m-d') . '.xlsx';
                
                header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
                header('Content-Disposition: attachment; filename="' . $filename . '"');
                header('Pragma: no-cache');
                header('Expires: 0');
                
                echo $excel;
                exit;
                
            } catch (Exception $e) {
                $error = 'Error al exportar rifas: ' . $e->getMessage();
                $query = http_build_query(['module' => 'socios', 'action' => 'rifas', 'client' => $_GET['client'] ?? '', 'error' => $error]);
                header('Location: index.php?' . $query);
                exit;
            }
        }

        // GET: Exportar multas Excel
        if ($action === 'exportar_multas_excel') {
            try {
                $exportacionServicio = new \Sacs\Services\ExportacionServicio();
                
                $filtros = [
                    'cliente_codigo' => trim((string) ($_GET['client'] ?? '')),
                    'fecha_desde' => trim((string) ($_GET['fecha_desde'] ?? '')),
                    'fecha_hasta' => trim((string) ($_GET['fecha_hasta'] ?? '')),
                ];
                
                $excel = $exportacionServicio->exportarMultasExcel($filtros);
                
                $filename = 'multas_' . date('Y-m-d') . '.xlsx';
                
                header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
                header('Content-Disposition: attachment; filename="' . $filename . '"');
                header('Pragma: no-cache');
                header('Expires: 0');
                
                echo $excel;
                exit;
                
            } catch (Exception $e) {
                $error = 'Error al exportar multas: ' . $e->getMessage();
                $query = http_build_query(['module' => 'socios', 'action' => 'multas', 'client' => $_GET['client'] ?? '', 'error' => $error]);
                header('Location: index.php?' . $query);
                exit;
            }
        }

        // GET: Exportar cheques Excel
        if ($action === 'exportar_cheques_excel') {
            try {
                $exportacionServicio = new \Sacs\Services\ExportacionServicio();
                
                $filtros = [
                    'fecha_desde' => trim((string) ($_GET['fecha_desde'] ?? '')),
                    'fecha_hasta' => trim((string) ($_GET['fecha_hasta'] ?? '')),
                ];
                
                $excel = $exportacionServicio->exportarChequesExcel($filtros);
                
                $filename = 'cheques_' . date('Y-m-d') . '.xlsx';
                
                header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
                header('Content-Disposition: attachment; filename="' . $filename . '"');
                header('Pragma: no-cache');
                header('Expires: 0');
                
                echo $excel;
                exit;
                
            } catch (Exception $e) {
                $error = 'Error al exportar cheques: ' . $e->getMessage();
                $query = http_build_query(['module' => 'socios', 'action' => 'cheques', 'error' => $error]);
                header('Location: index.php?' . $query);
                exit;
            }
        }

        // GET: Exportar socios PDF
        if ($action === 'exportar_socios_pdf') {
            try {
                $exportacionServicio = new \Sacs\Services\ExportacionServicio();
                
                $filtros = [
                    'nombre' => trim((string) ($_GET['nombre'] ?? '')),
                    'documento' => trim((string) ($_GET['documento'] ?? '')),
                    'centro' => trim((string) ($_GET['centro'] ?? '')),
                    'condicion' => trim((string) ($_GET['condicion'] ?? '')),
                    'estado' => trim((string) ($_GET['estado'] ?? '')),
                ];
                
                $pdf = $exportacionServicio->exportarSociosPDF($filtros);
                
                $filename = 'socios_' . date('Y-m-d') . '.pdf';
                
                header('Content-Type: application/pdf');
                header('Content-Disposition: attachment; filename="' . $filename . '"');
                header('Pragma: no-cache');
                header('Expires: 0');
                
                echo $pdf;
                exit;
                
            } catch (Exception $e) {
                $error = 'Error al exportar socios: ' . $e->getMessage();
                $query = http_build_query(['module' => 'socios', 'action' => 'listado', 'error' => $error]);
                header('Location: index.php?' . $query);
                exit;
            }
        }

        // GET: Exportar familiares PDF
        if ($action === 'exportar_familiares_pdf') {
            try {
                $exportacionServicio = new \Sacs\Services\ExportacionServicio();
                
                $filtros = [
                    'cliente_codigo' => trim((string) ($_GET['client'] ?? '')),
                    'inactivos' => trim((string) ($_GET['inactivos'] ?? '')) === 'true',
                ];
                
                $pdf = $exportacionServicio->exportarFamiliaresPDF($filtros);
                
                $filename = 'familiares_' . date('Y-m-d') . '.pdf';
                
                header('Content-Type: application/pdf');
                header('Content-Disposition: attachment; filename="' . $filename . '"');
                header('Pragma: no-cache');
                header('Expires: 0');
                
                echo $pdf;
                exit;
                
            } catch (Exception $e) {
                $error = 'Error al exportar familiares: ' . $e->getMessage();
                $query = http_build_query(['module' => 'socios', 'action' => 'familiares', 'client' => $_GET['client'] ?? '', 'error' => $error]);
                header('Location: index.php?' . $query);
                exit;
            }
        }

        // GET: Exportar aportes FONBES PDF
        if ($action === 'exportar_aportes_fonbes_pdf') {
            try {
                $exportacionServicio = new \Sacs\Services\ExportacionServicio();
                
                $filtros = [
                    'cliente_codigo' => trim((string) ($_GET['client'] ?? '')),
                ];
                
                $pdf = $exportacionServicio->exportarAportesFonbesPDF($filtros);
                
                $filename = 'aportes_fonbes_' . date('Y-m-d') . '.pdf';
                
                header('Content-Type: application/pdf');
                header('Content-Disposition: attachment; filename="' . $filename . '"');
                header('Pragma: no-cache');
                header('Expires: 0');
                
                echo $pdf;
                exit;
                
            } catch (Exception $e) {
                $error = 'Error al exportar aportes FONBES: ' . $e->getMessage();
                $query = http_build_query(['module' => 'socios', 'action' => 'aportes_fonbes', 'client' => $_GET['client'] ?? '', 'error' => $error]);
                header('Location: index.php?' . $query);
                exit;
            }
        }

        // GET: Exportar aportes COOPAM PDF
        if ($action === 'exportar_aportes_coopam_pdf') {
            try {
                $exportacionServicio = new \Sacs\Services\ExportacionServicio();
                
                $filtros = [
                    'cliente_codigo' => trim((string) ($_GET['client'] ?? '')),
                ];
                
                $pdf = $exportacionServicio->exportarAportesCoopamPDF($filtros);
                
                $filename = 'aportes_coopam_' . date('Y-m-d') . '.pdf';
                
                header('Content-Type: application/pdf');
                header('Content-Disposition: attachment; filename="' . $filename . '"');
                header('Pragma: no-cache');
                header('Expires: 0');
                
                echo $pdf;
                exit;
                
            } catch (Exception $e) {
                $error = 'Error al exportar aportes COOPAM: ' . $e->getMessage();
                $query = http_build_query(['module' => 'socios', 'action' => 'aportes_coopam', 'client' => $_GET['client'] ?? '', 'error' => $error]);
                header('Location: index.php?' . $query);
                exit;
            }
        }

        // GET: Exportar beneficios PDF
        if ($action === 'exportar_beneficios_pdf') {
            try {
                $exportacionServicio = new \Sacs\Services\ExportacionServicio();
                
                $filtros = [
                    'cliente_codigo' => trim((string) ($_GET['client'] ?? '')),
                    'fecha_desde' => trim((string) ($_GET['fecha_desde'] ?? '')),
                    'fecha_hasta' => trim((string) ($_GET['fecha_hasta'] ?? '')),
                ];
                
                $pdf = $exportacionServicio->exportarBeneficiosPDF($filtros);
                
                $filename = 'beneficios_' . date('Y-m-d') . '.pdf';
                
                header('Content-Type: application/pdf');
                header('Content-Disposition: attachment; filename="' . $filename . '"');
                header('Pragma: no-cache');
                header('Expires: 0');
                
                echo $pdf;
                exit;
                
            } catch (Exception $e) {
                $error = 'Error al exportar beneficios: ' . $e->getMessage();
                $query = http_build_query(['module' => 'socios', 'action' => 'beneficios', 'client' => $_GET['client'] ?? '', 'error' => $error]);
                header('Location: index.php?' . $query);
                exit;
            }
        }

        // GET: Exportar rifas PDF
        if ($action === 'exportar_rifas_pdf') {
            try {
                $exportacionServicio = new \Sacs\Services\ExportacionServicio();
                
                $filtros = [
                    'cliente_codigo' => trim((string) ($_GET['client'] ?? '')),
                    'fecha_desde' => trim((string) ($_GET['fecha_desde'] ?? '')),
                    'fecha_hasta' => trim((string) ($_GET['fecha_hasta'] ?? '')),
                ];
                
                $pdf = $exportacionServicio->exportarRifasPDF($filtros);
                
                $filename = 'rifas_' . date('Y-m-d') . '.pdf';
                
                header('Content-Type: application/pdf');
                header('Content-Disposition: attachment; filename="' . $filename . '"');
                header('Pragma: no-cache');
                header('Expires: 0');
                
                echo $pdf;
                exit;
                
            } catch (Exception $e) {
                $error = 'Error al exportar rifas: ' . $e->getMessage();
                $query = http_build_query(['module' => 'socios', 'action' => 'rifas', 'client' => $_GET['client'] ?? '', 'error' => $error]);
                header('Location: index.php?' . $query);
                exit;
            }
        }

        // GET: Exportar multas PDF
        if ($action === 'exportar_multas_pdf') {
            try {
                $exportacionServicio = new \Sacs\Services\ExportacionServicio();
                
                $filtros = [
                    'cliente_codigo' => trim((string) ($_GET['client'] ?? '')),
                    'fecha_desde' => trim((string) ($_GET['fecha_desde'] ?? '')),
                    'fecha_hasta' => trim((string) ($_GET['fecha_hasta'] ?? '')),
                ];
                
                $pdf = $exportacionServicio->exportarMultasPDF($filtros);
                
                $filename = 'multas_' . date('Y-m-d') . '.pdf';
                
                header('Content-Type: application/pdf');
                header('Content-Disposition: attachment; filename="' . $filename . '"');
                header('Pragma: no-cache');
                header('Expires: 0');
                
                echo $pdf;
                exit;
                
            } catch (Exception $e) {
                $error = 'Error al exportar multas: ' . $e->getMessage();
                $query = http_build_query(['module' => 'socios', 'action' => 'multas', 'client' => $_GET['client'] ?? '', 'error' => $error]);
                header('Location: index.php?' . $query);
                exit;
            }
        }

        // GET: Exportar cheques PDF
        if ($action === 'exportar_cheques_pdf') {
            try {
                $exportacionServicio = new \Sacs\Services\ExportacionServicio();
                
                $filtros = [
                    'fecha_desde' => trim((string) ($_GET['fecha_desde'] ?? '')),
                    'fecha_hasta' => trim((string) ($_GET['fecha_hasta'] ?? '')),
                ];
                
                $pdf = $exportacionServicio->exportarChequesPDF($filtros);
                
                $filename = 'cheques_' . date('Y-m-d') . '.pdf';
                
                header('Content-Type: application/pdf');
                header('Content-Disposition: attachment; filename="' . $filename . '"');
                header('Pragma: no-cache');
                header('Expires: 0');
                
                echo $pdf;
                exit;
                
            } catch (Exception $e) {
                $error = 'Error al exportar cheques: ' . $e->getMessage();
                $query = http_build_query(['module' => 'socios', 'action' => 'cheques', 'error' => $error]);
                header('Location: index.php?' . $query);
                exit;
            }
        }

        // POST: Eliminar socio
        if ($action === 'eliminar_socio' && $_SERVER['REQUEST_METHOD'] === 'POST') {
            $csrfToken = (string) ($_POST['csrf_token'] ?? '');
            if (!hash_equals((string) $_SESSION['csrf_token'], $csrfToken)) {
                $error = 'La solicitud no es válida.';
                $query = http_build_query(['module' => 'socios', 'action' => 'gestion_estado', 'client' => $_POST['cliente'] ?? '', 'error' => $error]);
                header('Location: index.php?' . $query);
                exit;
            }

            try {
                $idPersona = trim((string) ($_POST['id_persona'] ?? ''));
                $tipo = trim((string) ($_POST['tipo'] ?? ''));
                $fechaUltimoAporte = trim((string) ($_POST['fecha_ultimo_aporte'] ?? '')) ?: null;
                $clienteCodigo = trim((string) ($_POST['cliente'] ?? ''));

                if ($idPersona === '' || $tipo === '') {
                    throw new Exception('Datos incompletos');
                }

                $resultado = $sociosServicio->eliminarSocio($idPersona, $tipo, $_SESSION['user']['username'] ?? 'SISTEMA', $fechaUltimoAporte);

                if ($resultado['success']) {
                    $query = http_build_query(['module' => 'socios', 'action' => 'info', 'client' => $clienteCodigo, 'success' => $resultado['message']]);
                    header('Location: index.php?' . $query);
                    exit;
                } else {
                    $query = http_build_query(['module' => 'socios', 'action' => 'gestion_estado', 'client' => $clienteCodigo, 'error' => $resultado['message']]);
                    header('Location: index.php?' . $query);
                    exit;
                }

            } catch (Exception $e) {
                $error = 'Error al eliminar socio: ' . $e->getMessage();
                $query = http_build_query(['module' => 'socios', 'action' => 'gestion_estado', 'client' => $_POST['cliente'] ?? '', 'error' => $error]);
                header('Location: index.php?' . $query);
                exit;
            }
        }

        // POST: Activar socio
        if ($action === 'activar_socio' && $_SERVER['REQUEST_METHOD'] === 'POST') {
            $csrfToken = (string) ($_POST['csrf_token'] ?? '');
            if (!hash_equals((string) $_SESSION['csrf_token'], $csrfToken)) {
                $error = 'La solicitud no es válida.';
                $query = http_build_query(['module' => 'socios', 'action' => 'gestion_estado', 'client' => $_POST['cliente'] ?? '', 'error' => $error]);
                header('Location: index.php?' . $query);
                exit;
            }

            try {
                $idPersona = trim((string) ($_POST['id_persona'] ?? ''));
                $clienteCodigo = trim((string) ($_POST['cliente'] ?? ''));

                if ($idPersona === '') {
                    throw new Exception('Datos incompletos');
                }

                $resultado = $sociosServicio->activarSocio($idPersona, $_SESSION['user']['username'] ?? 'SISTEMA');

                if ($resultado['success']) {
                    $query = http_build_query(['module' => 'socios', 'action' => 'info', 'client' => $clienteCodigo, 'success' => $resultado['message']]);
                    header('Location: index.php?' . $query);
                    exit;
                } else {
                    $query = http_build_query(['module' => 'socios', 'action' => 'gestion_estado', 'client' => $clienteCodigo, 'error' => $resultado['message']]);
                    header('Location: index.php?' . $query);
                    exit;
                }

            } catch (Exception $e) {
                $error = 'Error al activar socio: ' . $e->getMessage();
                $query = http_build_query(['module' => 'socios', 'action' => 'gestion_estado', 'client' => $_POST['cliente'] ?? '', 'error' => $error]);
                header('Location: index.php?' . $query);
                exit;
            }
        }

        // POST: Ocultar/mostrar socio
        if ($action === 'ocultar_socio' && $_SERVER['REQUEST_METHOD'] === 'POST') {
            $csrfToken = (string) ($_POST['csrf_token'] ?? '');
            if (!hash_equals((string) $_SESSION['csrf_token'], $csrfToken)) {
                $error = 'La solicitud no es válida.';
                $query = http_build_query(['module' => 'socios', 'action' => 'gestion_estado', 'client' => $_POST['cliente'] ?? '', 'error' => $error]);
                header('Location: index.php?' . $query);
                exit;
            }

            try {
                $idPersona = trim((string) ($_POST['id_persona'] ?? ''));
                $visible = (int) ($_POST['visible'] ?? 1);
                $clienteCodigo = trim((string) ($_POST['cliente'] ?? ''));

                if ($idPersona === '') {
                    throw new Exception('Datos incompletos');
                }

                $resultado = $sociosServicio->ocultarSocio($idPersona, $visible, $_SESSION['user']['username'] ?? 'SISTEMA');

                if ($resultado['success']) {
                    $query = http_build_query(['module' => 'socios', 'action' => 'info', 'client' => $clienteCodigo, 'success' => $resultado['message']]);
                    header('Location: index.php?' . $query);
                    exit;
                } else {
                    $query = http_build_query(['module' => 'socios', 'action' => 'gestion_estado', 'client' => $clienteCodigo, 'error' => $resultado['message']]);
                    header('Location: index.php?' . $query);
                    exit;
                }

            } catch (Exception $e) {
                $error = 'Error al cambiar visibilidad: ' . $e->getMessage();
                $query = http_build_query(['module' => 'socios', 'action' => 'gestion_estado', 'client' => $_POST['cliente'] ?? '', 'error' => $error]);
                header('Location: index.php?' . $query);
                exit;
            }
        }

        // GET: Editar socio
        if ($action === 'editar') {
            $clienteCodigo = trim((string) ($_GET['client'] ?? ''));
            
            if ($clienteCodigo === '') {
                http_response_code(400);
                $error = 'Cliente no especificado';
                require dirname(__DIR__) . '/views/home.php';
                exit;
            }
            
            $socio = $sociosServicio->obtenerSocioDetalle($clienteCodigo);
            
            if (empty($socio)) {
                http_response_code(404);
                $error = 'Cliente no encontrado';
                require dirname(__DIR__) . '/views/home.php';
                exit;
            }
            
            require dirname(__DIR__) . '/views/socios/editar.php';
            exit;
        }

        // POST: Actualizar socio
        if ($action === 'actualizar' && $_SERVER['REQUEST_METHOD'] === 'POST') {
            error_log("POST actualizar socio recibido");
            $csrfToken = (string) ($_POST['csrf_token'] ?? '');
            if (!hash_equals((string) $_SESSION['csrf_token'], $csrfToken)) {
                error_log("Error CSRF en actualizar socio");
                $error = 'La solicitud no es válida.';
                $clienteCodigo = trim((string) ($_POST['cliente_codigo'] ?? ''));
                $socio = $sociosServicio->obtenerSocioDetalle($clienteCodigo);
                require dirname(__DIR__) . '/views/socios/editar.php';
                exit;
            }

            try {
                $idPersona = (int) ($_POST['id_persona'] ?? 0);
                $clienteCodigo = trim((string) ($_POST['cliente_codigo'] ?? ''));

                error_log("idPersona: $idPersona, clienteCodigo: $clienteCodigo");

                if ($idPersona === 0 || $clienteCodigo === '') {
                    throw new Exception('ID de persona o código de cliente no especificado');
                }

                $datosPersona = [
                    'ape_pat' => trim((string) ($_POST['ape_pat'] ?? '')),
                    'ape_mat' => trim((string) ($_POST['ape_mat'] ?? '')),
                    'nomb_pri' => trim((string) ($_POST['nomb_pri'] ?? '')),
                    'nomb_seg' => trim((string) ($_POST['nomb_seg'] ?? '')),
                    'tpo_doc' => trim((string) ($_POST['tpo_doc'] ?? '')),
                    'num_doc' => trim((string) ($_POST['num_doc'] ?? '')),
                    'sexo' => trim((string) ($_POST['sexo'] ?? '')),
                    'fe_nac' => trim((string) ($_POST['fe_nac'] ?? '')) ?: null,
                    'stdo_civil' => trim((string) ($_POST['stdo_civil'] ?? '')),
                    'stdo_persn' => trim((string) ($_POST['stdo_persn'] ?? '')),
                    'cta_correo' => trim((string) ($_POST['cta_correo'] ?? '')),
                    'telf_fijo' => trim((string) ($_POST['telf_fijo'] ?? '')),
                    'telf_cel' => trim((string) ($_POST['telf_cel'] ?? '')),
                    'departamento' => trim((string) ($_POST['departamento'] ?? '')),
                    'provincia' => trim((string) ($_POST['provincia'] ?? '')),
                    'distrito' => trim((string) ($_POST['distrito'] ?? '')),
                    'zona' => trim((string) ($_POST['zona'] ?? '')),
                    'via' => trim((string) ($_POST['via'] ?? '')),
                    'int' => trim((string) ($_POST['int'] ?? '')),
                    'ext' => trim((string) ($_POST['ext'] ?? '')),
                    'piso' => trim((string) ($_POST['piso'] ?? '')),
                    'condicion' => trim((string) ($_POST['condicion'] ?? '')),
                    'situacion' => trim((string) ($_POST['situacion'] ?? '')),
                    'centro' => trim((string) ($_POST['centro'] ?? '')),
                    'cuenta' => trim((string) ($_POST['cuenta'] ?? '')),
                    'ingreso' => trim((string) ($_POST['ingreso'] ?? '')),
                    'aportacion' => trim((string) ($_POST['aportacion'] ?? '')),
                    'saldo' => trim((string) ($_POST['saldo'] ?? '')),
                    'observacion' => trim((string) ($_POST['observacion'] ?? '')),
                ];

                // Solo enviar campos con claves foráneas si tienen valores válidos
                if (!empty($_POST['id_nacionalidad'])) {
                    $datosPersona['id_nacionalidad'] = trim((string) ($_POST['id_nacionalidad']));
                }
                if (!empty($_POST['id_instruccion'])) {
                    $datosPersona['id_instruccion'] = trim((string) ($_POST['id_instruccion']));
                }
                if (!empty($_POST['id_profesion'])) {
                    $datosPersona['id_profesion'] = trim((string) ($_POST['id_profesion']));
                }
                if (!empty($_POST['dpto_nac'])) {
                    $datosPersona['dpto_nac'] = trim((string) ($_POST['dpto_nac']));
                }
                if (!empty($_POST['prov_nac'])) {
                    $datosPersona['prov_nac'] = trim((string) ($_POST['prov_nac']));
                }
                if (!empty($_POST['distri_nac'])) {
                    $datosPersona['distri_nac'] = trim((string) ($_POST['distri_nac']));
                }
                if (!empty($_POST['desc_coment'])) {
                    $datosPersona['desc_coment'] = trim((string) ($_POST['desc_coment']));
                }
                if (!empty($_POST['documento'])) {
                    $datosPersona['documento'] = trim((string) ($_POST['documento']));
                }


                $resultado = $sociosServicio->actualizarSocio($idPersona, $datosPersona, $_SESSION['user']['username'] ?? 'SISTEMA');

                $query = http_build_query(['module' => 'socios', 'action' => 'info', 'client' => $clienteCodigo, 'success' => 'Socio actualizado correctamente']);
                header('Location: index.php?' . $query);
                exit;

            } catch (Exception $e) {
                $error = 'Error al actualizar socio: ' . $e->getMessage();
                $clienteCodigo = trim((string) ($_POST['cliente_codigo'] ?? ''));
                $socio = $sociosServicio->obtenerSocioDetalle($clienteCodigo);
                require dirname(__DIR__) . '/views/socios/editar.php';
                exit;
            }
        }

        // GET: Historial de cambios del socio
        if ($action === 'auditoria') {
            $clienteCodigo = trim((string) ($_GET['client'] ?? ''));
            
            if ($clienteCodigo === '') {
                http_response_code(400);
                $error = 'Cliente no especificado';
                require dirname(__DIR__) . '/views/home.php';
                exit;
            }
            
            $historial = $sociosServicio->obtenerHistorialCambios($clienteCodigo);
            $socio = $sociosServicio->obtenerSocioDetalle($clienteCodigo);
            
            if (empty($socio)) {
                http_response_code(404);
                $error = 'Cliente no encontrado';
                require dirname(__DIR__) . '/views/home.php';
                exit;
            }
            
            require dirname(__DIR__) . '/views/socios/auditoria.php';
            exit;
        }

        // GET: Lista completa de todos los cambios de auditoría
        if ($action === 'auditoria_general') {
            $historial = $sociosServicio->obtenerTodosLosCambios(100);
            require dirname(__DIR__) . '/views/socios/auditoria_general.php';
            exit;
        }

        // AJAX: Obtener provincias por departamento
        if ($action === 'obtener_provincias') {
            header('Content-Type: application/json');
            $departamento = trim((string) ($_GET['departamento'] ?? ''));
            $provincias = $sociosServicio->obtenerProvincias($departamento);
            echo json_encode($provincias);
            exit;
        }

        // AJAX: Obtener distritos por departamento y provincia
        if ($action === 'obtener_distritos') {
            header('Content-Type: application/json');
            $departamento = trim((string) ($_GET['departamento'] ?? ''));
            $provincia = trim((string) ($_GET['provincia'] ?? ''));
            $distritos = $sociosServicio->obtenerDistritos($departamento, $provincia);
            echo json_encode($distritos);
            exit;
        }

        // AJAX: Obtener todas las provincias
        if ($action === 'obtener_todas_provincias') {
            header('Content-Type: application/json');
            $sql = "SELECT DISTINCT 
                        SUBSTRING(ubigeo_codigo, 3, 2) as codigo,
                        ubigeo_provincia as nombre
                    FROM f05_ubigeo 
                    WHERE ubigeo_provincia IS NOT NULL
                    ORDER BY ubigeo_provincia ASC";
            $stmt = Database::connection()->query($sql);
            $provincias = $stmt->fetchAll();
            echo json_encode($provincias);
            exit;
        }

        // AJAX: Obtener todos los distritos
        if ($action === 'obtener_todos_distritos') {
            header('Content-Type: application/json');
            $sql = "SELECT DISTINCT 
                        SUBSTRING(ubigeo_codigo, 5, 2) as codigo,
                        ubigeo_distrito as nombre
                    FROM f05_ubigeo 
                    WHERE ubigeo_distrito IS NOT NULL
                    ORDER BY ubigeo_distrito ASC";
            $stmt = Database::connection()->query($sql);
            $distritos = $stmt->fetchAll();
            echo json_encode($distritos);
            exit;
        }

        // AJAX: Obtener departamentos
        if ($action === 'obtener_departamentos') {
            header('Content-Type: application/json');
            try {
                $departamentos = $sociosServicio->obtenerDepartamentos();
                echo json_encode($departamentos);
            } catch (Exception $e) {
                echo json_encode(['error' => $e->getMessage()]);
            }
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

                $resultado = $sociosServicio->registrarSocio($datosPersona, $datosCliente, $_SESSION['user']['username'] ?? 'SISTEMA');

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

    // Módulo Trámites
    if ($module === 'tramites') {
        $tramitesServicio = new \Sacs\Services\TramitesServicio();
        // Si es POST, tomar action solo de POST, si no, de GET
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $action = $_POST['action'] ?? '';
        } else {
            $action = $_GET['action'] ?? '';
        }

        // GET: Listado de trámites
        if ($action === 'listar') {
            $pagina = (int) ($_GET['pagina'] ?? 1);
            $porPagina = 25;

            // Construir filtros solo con valores no vacíos
            $filtros = [];
            if (!empty(trim($_GET['busqueda'] ?? ''))) {
                $filtros['busqueda'] = trim($_GET['busqueda']);
            }
            if (!empty(trim($_GET['tipo'] ?? ''))) {
                $filtros['tipo'] = trim($_GET['tipo']);
            }
            if (!empty(trim($_GET['estado'] ?? ''))) {
                $filtros['estado'] = trim($_GET['estado']);
            }
            if (!empty(trim($_GET['socio'] ?? ''))) {
                $filtros['socio'] = trim($_GET['socio']);
            }

            $resultado = $tramitesServicio->listarTramites($filtros, $pagina, $porPagina);
            $tiposDocumentos = $tramitesServicio->obtenerTiposDocumentos();

            $tramites = $resultado['tramites'];
            $totalPaginas = $resultado['totalPaginas'];
            $paginaActual = $resultado['pagina'];
            
            require dirname(__DIR__) . '/views/tramites/listado.php';
            exit;
        }

        // GET: Ver detalle de trámite
        if ($action === 'ver') {
            $codigo = trim((string) ($_GET['codigo'] ?? ''));
            
            if ($codigo === '') {
                http_response_code(400);
                $error = 'Código no especificado';
                require dirname(__DIR__) . '/views/home.php';
                exit;
            }
            
            $tramite = $tramitesServicio->obtenerTramite((int) $codigo);
            $historico = $tramitesServicio->obtenerHistorico((int) $codigo);
            
            if (empty($tramite)) {
                http_response_code(404);
                $error = 'Trámite no encontrado';
                require dirname(__DIR__) . '/views/home.php';
                exit;
            }
            
            require dirname(__DIR__) . '/views/tramites/ver.php';
            exit;
        }

        // GET: Registrar trámite
        if ($action === 'registrar') {
            $tiposDocumentos = $tramitesServicio->obtenerTiposDocumentos();
            $correlativos = $tramitesServicio->obtenerTodosCorrelativos();

            require dirname(__DIR__) . '/views/tramites/registrar.php';
            exit;
        }

        // GET: Buscar socio AJAX
        if ($action === 'buscar_socio_ajax') {
            header('Content-Type: application/json');
            $codigo = trim((string) ($_GET['codigo'] ?? ''));

            if ($codigo === '') {
                echo json_encode(['success' => false, 'message' => 'Código no especificado']);
                exit;
            }

            $sociosServicio = new \Sacs\Services\SociosServicio();
            $socio = $sociosServicio->obtenerSocioDetalle($codigo);

            if ($socio) {
                echo json_encode(['success' => true, 'socio' => $socio]);
            } else {
                echo json_encode(['success' => false, 'message' => 'Socio no encontrado']);
            }
            exit;
        }

        // GET: Editar trámite
        if ($action === 'editar') {
            $codigo = trim((string) ($_GET['codigo'] ?? ''));

            if ($codigo === '') {
                http_response_code(400);
                $error = 'Código no especificado';
                require dirname(__DIR__) . '/views/home.php';
                exit;
            }

            $tramite = $tramitesServicio->obtenerTramite((int) $codigo);

            if (empty($tramite)) {
                http_response_code(404);
                $error = 'Trámite no encontrado';
                require dirname(__DIR__) . '/views/home.php';
                exit;
            }

            if (($tramite['estado'] ?? '') === 'ELIMINADO') {
                http_response_code(400);
                $error = 'No se puede editar un trámite eliminado';
                require dirname(__DIR__) . '/views/home.php';
                exit;
            }

            require dirname(__DIR__) . '/views/tramites/editar.php';
            exit;
        }

        // POST: Actualizar trámite (dentro del bloque de trámites)
        if ($action === 'actualizar' && $_SERVER['REQUEST_METHOD'] === 'POST') {
            $csrfToken = (string) ($_POST['csrf_token'] ?? '');
            if (!hash_equals((string) $_SESSION['csrf_token'], $csrfToken)) {
                $error = 'La solicitud no es válida.';
                $codigo = trim((string) ($_POST['codigo'] ?? ''));
                $tramite = $tramitesServicio->obtenerTramite((int) $codigo);
                require dirname(__DIR__) . '/views/tramites/editar.php';
                exit;
            }

            try {
                $codigo = trim((string) ($_POST['codigo'] ?? ''));
                $usuario = $_SESSION['sacs_user']['username'] ?? 'SISTEMA';
                
                // Procesar archivos adjuntos (solo si se cargan nuevos)
                $rutas = ['ruta' => null, 'ruta2' => null, 'ruta3' => null];
                if (isset($_FILES['archivo1']) && $_FILES['archivo1']['error'] === UPLOAD_ERR_OK) {
                    $resultado = $tramitesServicio->cargarArchivo($_FILES['archivo1'], 'ruta');
                    if ($resultado['success']) {
                        $rutas['ruta'] = $resultado['ruta'];
                    }
                }
                if (isset($_FILES['archivo2']) && $_FILES['archivo2']['error'] === UPLOAD_ERR_OK) {
                    $resultado = $tramitesServicio->cargarArchivo($_FILES['archivo2'], 'ruta2');
                    if ($resultado['success']) {
                        $rutas['ruta2'] = $resultado['ruta'];
                    }
                }
                if (isset($_FILES['archivo3']) && $_FILES['archivo3']['error'] === UPLOAD_ERR_OK) {
                    $resultado = $tramitesServicio->cargarArchivo($_FILES['archivo3'], 'ruta3');
                    if ($resultado['success']) {
                        $rutas['ruta3'] = $resultado['ruta'];
                    }
                }

                $datos = [
                    'descripcion' => trim((string) ($_POST['descripcion'] ?? '')),
                    'fisico' => trim((string) ($_POST['fisico'] ?? 'NO')),
                    'destino' => trim((string) ($_POST['destino'] ?? '')),
                    'fecha' => trim((string) ($_POST['fecha'] ?? '')),
                    'estado' => trim((string) ($_POST['estado'] ?? 'REGISTRADO')),
                    'obs' => trim((string) ($_POST['obs'] ?? '')),
                    'obs2' => trim((string) ($_POST['obs2'] ?? '')),
                    'obs3' => trim((string) ($_POST['obs3'] ?? '')),
                    'referencia' => trim((string) ($_POST['referencia'] ?? '')),
                    'respuesta' => trim((string) ($_POST['respuesta'] ?? '')),
                    'importe' => trim((string) ($_POST['importe'] ?? '')),
                    'cuotas' => trim((string) ($_POST['cuotas'] ?? '')),
                    'tramitante' => trim((string) ($_POST['tramitante'] ?? '')),
                    'tramitante_dni' => trim((string) ($_POST['tramitante_dni'] ?? '')),
                    'tramitante_cel' => trim((string) ($_POST['tramitante_cel'] ?? '')),
                    'tramitante_email' => trim((string) ($_POST['tramitante_email'] ?? '')),
                    'tramitante_email2' => trim((string) ($_POST['tramitante_email2'] ?? '')),
                    'ref_docu' => trim((string) ($_POST['ref_docu'] ?? '')),
                    'cuenta' => trim((string) ($_POST['cuenta'] ?? '')),
                    'fechaE' => date('Y-m-d')
                ];
                
                // Agregar rutas de archivos solo si se cargaron nuevos
                $datos = array_merge($datos, array_filter($rutas, function($v) { return $v !== null; }));

                $resultado = $tramitesServicio->actualizarTramite((int) $codigo, $datos, $usuario);

                if ($resultado['success']) {
                    $query = http_build_query(['module' => 'tramites', 'action' => 'ver', 'codigo' => $codigo, 'success' => $resultado['message']]);
                    header('Location: index.php?' . $query);
                    exit;
                } else {
                    $error = $resultado['message'];
                    $tramite = $tramitesServicio->obtenerTramite((int) $codigo);
                    require dirname(__DIR__) . '/views/tramites/editar.php';
                    exit;
                }

            } catch (Exception $e) {
                $error = 'Error al actualizar trámite: ' . $e->getMessage();
                $codigo = trim((string) ($_POST['codigo'] ?? ''));
                $tramite = $tramitesServicio->obtenerTramite((int) $codigo);
                require dirname(__DIR__) . '/views/tramites/editar.php';
                exit;
            }
        }

        // GET: Eliminar trámite
        if ($action === 'eliminar') {
            $csrfToken = (string) ($_GET['csrf_token'] ?? '');
            if (!hash_equals((string) $_SESSION['csrf_token'], $csrfToken)) {
                $error = 'La solicitud no es válida.';
                $query = http_build_query(['module' => 'tramites', 'action' => 'listar', 'error' => $error]);
                header('Location: index.php?' . $query);
                exit;
            }

            try {
                $codigo = trim((string) ($_GET['codigo'] ?? ''));
                $usuario = $_SESSION['sacs_user']['username'] ?? 'SISTEMA';

                if ($codigo === '') {
                    throw new Exception('Código no especificado');
                }

                $resultado = $tramitesServicio->eliminarTramite((int) $codigo, $usuario);

                if ($resultado['success']) {
                    $query = http_build_query(['module' => 'tramites', 'action' => 'listar', 'success' => $resultado['message']]);
                    header('Location: index.php?' . $query);
                    exit;
                } else {
                    $query = http_build_query(['module' => 'tramites', 'action' => 'listar', 'error' => $resultado['message']]);
                    header('Location: index.php?' . $query);
                    exit;
                }

            } catch (Exception $e) {
                $error = 'Error al eliminar trámite: ' . $e->getMessage();
                $query = http_build_query(['module' => 'tramites', 'action' => 'listar', 'error' => $error]);
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
