<?php

declare(strict_types=1);

require dirname(__DIR__) . '/../config/bootstrap.php';

use Sacs\Core\Database;
use Sacs\Repositories\CajaRepository;

$user = $_SESSION['sacs_user'] ?? null;
if ($user === null) {
    header('Location: index.php');
    exit;
}

$repository = new CajaRepository();
$database = Database::connection();
$codigoContable = $repository->accountingCodes();

// Obtener datos del préstamo y cliente
$clienteCodigo = trim($_GET['client'] ?? '');
$prestamoId = filter_var($_GET['loan'] ?? null, FILTER_VALIDATE_INT, ['options' => ['min_range' => 1]]);

if ($clienteCodigo === '' || $prestamoId === false || $prestamoId === null) {
    http_response_code(400);
    $error = 'Datos inválidos';
    require dirname(__DIR__) . '/home.php';
    exit;
}

// Obtener datos del cliente
$cliente = $repository->findClientProfile($clienteCodigo);
if ($cliente === null) {
    $reason = 'Cliente no encontrado en el sistema. Verifique el código de cliente.';
    $query = http_build_query([
        'module' => 'caja',
        'action' => 'cuenta_no_pagable',
        'client' => $clienteCodigo,
        'loan' => $prestamoId,
        'reason' => $reason
    ]);
    header('Location: index.php?' . $query);
    exit;
}

// Obtener datos del préstamo
$stmt = $database->prepare("SELECT * FROM f16_prestamo WHERE f16_codigo = ? AND f16_estado = 4");
$stmt->execute([$prestamoId]);
$prestamo = $stmt->fetch();

if ($prestamo === false) {
    $reason = 'Préstamo no encontrado o no está en estado activo. El préstamo puede estar cancelado, rechazado o en otro estado que no permite pagos.';
    $query = http_build_query([
        'module' => 'caja',
        'action' => 'cuenta_no_pagable',
        'client' => $clienteCodigo,
        'loan' => $prestamoId,
        'reason' => $reason
    ]);
    header('Location: index.php?' . $query);
    exit;
}

// Obtener cuotas vigentes del préstamo
$stmt = $database->prepare("SELECT * FROM f17_cuotasprestamo WHERE f17_prestamo = ? AND f17_estado = 'VIGENTE' AND f17_ncuota > 0 ORDER BY f17_fecha, f17_ncuota LIMIT 50");
$stmt->execute([$prestamoId]);
$cuotas = $stmt->fetchAll();

// Verificar si hay cuotas vigentes disponibles
if (empty($cuotas)) {
    http_response_code(400);
    $reason = 'No hay cuotas vigentes disponibles para este préstamo. El préstamo puede estar pagado completamente, cancelado o en un estado que no permite pagos.';
    $query = http_build_query([
        'module' => 'caja',
        'action' => 'cuenta_no_pagable',
        'client' => $clienteCodigo,
        'loan' => $prestamoId,
        'reason' => $reason
    ]);
    header('Location: index.php?' . $query);
    exit;
}

$displayName = htmlspecialchars($user['username'], ENT_QUOTES, 'UTF-8');
$profile = htmlspecialchars((string) $user['profile'], ENT_QUOTES, 'UTF-8');
$userId = htmlspecialchars((string) $user['id'], ENT_QUOTES, 'UTF-8');
$supervisor = $user['supervisor'] === 'S';
$csrfToken = (string) $_SESSION['csrf_token'];

// Recuperar datos del formulario si hay error previo
$formData = $_SESSION['form_data'] ?? [];
$formError = $_SESSION['form_error'] ?? null;
unset($_SESSION['form_data'], $_SESSION['form_error']);

// Funciones de formato
$escape = static fn (mixed $value): string => htmlspecialchars((string) ($value ?? ''), ENT_QUOTES, 'UTF-8');
$money = static fn (mixed $value): string => $value === null || $value === '' ? '-' : number_format((float) $value, 2, '.', ',');

// Función para obtener valor de campo (prioridad: datos recuperados, valor por defecto)
$getFormValue = static function (string $fieldName, $defaultValue = '') use ($formData): string {
    return $formData[$fieldName] ?? $defaultValue;
};

// Función para verificar si un campo está seleccionado
$getSelected = static function (string $fieldName, string $value, $defaultValue = '') use ($formData): string {
    $fieldValue = $formData[$fieldName] ?? $defaultValue;
    return $fieldValue === $value ? 'selected' : '';
};

// Variable para guardar el total esperado de la cuota
$totalEsperadoCuota = null;

// Mapeo de códigos contables por forma de pago
$codigoContablePorFormaPago = [
    'EFECTIVO' => '1011',
    'DEPOSITO' => '10612',
    'TRANSFERENCIA' => '10612',
    'CHEQUE' => '10612',
    'PLANILLA' => '10612'
];
?>

<!doctype html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Amortización AEE | SACS Nuevo</title>
    <style>
        :root {
            --ink: #17212b;
            --muted: #697586;
            --paper: #f5f7f4;
            --panel: #ffffff;
            --navy: #142b3a;
            --navy-soft: #20465a;
            --teal: #1d7a78;
            --gold: #d69e47;
            --line: #dce4e1;
            --shadow: 0 18px 45px rgba(20, 43, 58, .08);
        }

        * { box-sizing: border-box; }
        body { margin: 0; background: var(--paper); color: var(--ink); font-family: "Trebuchet MS", sans-serif; }
        a { color: inherit; text-decoration: none; }
        .shell { display: grid; grid-template-columns: 254px minmax(0, 1fr); min-height: 100vh; }
        .sidebar { display: flex; flex-direction: column; padding: 28px 18px 20px; background: var(--navy); color: #e7f0ed; }
        .brand { display: flex; align-items: center; gap: 12px; padding: 0 12px 34px; color: inherit; text-decoration: none; }
        .brand:hover { opacity: 0.9; }
        .brand-mark { display: grid; place-items: center; width: 38px; height: 38px; border: 2px solid var(--gold); border-radius: 50%; color: var(--gold); font-family: Georgia, serif; font-size: 20px; }
        .brand-name { font-family: Georgia, serif; font-size: 22px; letter-spacing: .02em; }
        .brand-note { display: block; margin-top: 3px; color: #9bb3b7; font-size: 11px; letter-spacing: .12em; text-transform: uppercase; }
        .nav-label { padding: 0 12px 10px; color: #86a6ad; font-size: 10px; font-weight: bold; letter-spacing: .14em; text-transform: uppercase; }
        .nav { display: grid; gap: 5px; }
        .nav-link { display: flex; align-items: center; gap: 11px; padding: 11px 12px; border-left: 3px solid transparent; border-radius: 4px; color: #b8ccce; font-size: 14px; }
        .nav-link:hover, .nav-link.active { border-left-color: var(--gold); background: var(--navy-soft); color: #fff; }
        .nav-icon { width: 19px; color: #9dbfc1; font-size: 12px; text-align: center; }
        .sidebar-footer { margin-top: auto; padding: 18px 12px 0; border-top: 1px solid rgba(214, 230, 226, .14); color: #9bb3b7; font-size: 12px; line-height: 1.5; }
        .main { min-width: 0; padding: 27px clamp(22px, 4vw, 58px) 42px; }
        .topbar { display: flex; align-items: center; justify-content: space-between; gap: 20px; margin-bottom: 45px; }
        .breadcrumb { color: var(--muted); font-size: 12px; letter-spacing: .08em; text-transform: uppercase; }
        .user-menu { display: flex; align-items: center; gap: 12px; }
        .avatar { display: grid; place-items: center; width: 38px; height: 38px; border-radius: 50%; background: var(--teal); color: #fff; font-family: Georgia, serif; font-size: 17px; }
        .user-copy { line-height: 1.2; text-align: right; }
        .user-name { display: block; font-size: 14px; font-weight: bold; }
        .user-role { display: block; margin-top: 3px; color: var(--muted); font-size: 11px; }
        .logout { margin-left: 8px; color: var(--teal); font-size: 12px; font-weight: bold; }
        .eyebrow { margin: 0 0 10px; color: var(--teal); font-size: 12px; font-weight: bold; letter-spacing: .16em; text-transform: uppercase; }
        h1 { max-width: 680px; margin: 0; font-family: Georgia, serif; font-size: clamp(32px, 4vw, 52px); font-weight: normal; line-height: 1.05; }
        .intro { max-width: 590px; margin: 15px 0 34px; color: var(--muted); font-size: 15px; line-height: 1.6; }
        .info-box { padding: 25px; border: 1px solid var(--line); background: var(--panel); border-radius: 7px; box-shadow: var(--shadow); margin-bottom: 20px; }
        .info-box h3 { margin: 0 0 15px; font-family: Georgia, serif; font-size: 22px; font-weight: normal; }
        .info-box p { margin: 0 0 8px; font-size: 14px; }
        .info-box p:last-child { margin-bottom: 0; }
        .info-box strong { color: var(--ink); }
        .form-row { display: grid; grid-template-columns: repeat(3, minmax(0, 1fr)); gap: 10px; margin-bottom: 15px; }
        .form-row-compact { display: grid; grid-template-columns: 2fr 1fr 1fr; gap: 8px; margin-bottom: 15px; }
        .form-group { margin-bottom: 15px; }
        .form-group label { display: block; margin-bottom: 8px; color: var(--muted); font-size: 11px; letter-spacing: .12em; text-transform: uppercase; font-weight: bold; }
        .form-group input, .form-group select, .form-group textarea { width: 100%; padding: 12px; border: 1px solid #b9c7c7; border-radius: 4px; font: inherit; font-size: 14px; }
        .form-group input:focus, .form-group select:focus, .form-group textarea:focus { outline: none; border-color: var(--teal); }
        .form-group textarea { resize: vertical; min-height: 80px; }
        .form-group input[readonly] { background: #f8f9fa; color: var(--muted); }
        .form-group input.compact, .form-group select.compact { padding: 8px 6px; font-size: 12px; }
        .btn { padding: 12px 20px; border: 0; border-radius: 4px; background: var(--teal); color: #fff; cursor: pointer; font: inherit; font-size: 14px; font-weight: bold; }
        .btn:hover { background: var(--navy); }
        .btn-secondary { background: var(--muted); }
        .btn-secondary:hover { background: var(--navy); }
        .error { padding: 15px; margin-bottom: 20px; border: 1px solid #f8d7da; background: #fde8e8; border-radius: 7px; color: #721c24; font-size: 14px; }
        .cuota-info { margin-top: 15px; padding: 15px; background: #eaf2ef; border-radius: 4px; border: 1px solid var(--line); }
        .cuota-info h4 { margin: 0 0 10px; font-family: Georgia, serif; font-size: 18px; font-weight: normal; }
        .cuota-info p { margin: 0 0 5px; font-size: 13px; }
        @media (max-width: 900px) { .shell { grid-template-columns: 1fr; } .sidebar { padding: 18px; } .brand { padding-bottom: 20px; } .nav { grid-template-columns: repeat(3, minmax(0, 1fr)); } .nav-label, .sidebar-footer { display: none; } .main { padding-top: 24px; } }
        @media (max-width: 650px) { .topbar { align-items: flex-start; flex-direction: column; margin-bottom: 34px; } .user-menu { width: 100%; justify-content: flex-start; } .user-copy { text-align: left; } .form-row { grid-template-columns: 1fr; } h1 { font-size: 38px; } }
    </style>
</head>
<body>
<div class="shell">
    <aside class="sidebar">
        <a href="index.php" class="brand">
            <div class="brand-mark">S</div>
            <div>
                <div class="brand-name">SACS</div>
                <div class="brand-note">gestion social</div>
            </div>
        </a>
        <div class="nav-label">Caja</div>
        <nav class="nav" aria-label="Navegacion principal">
            <a class="nav-link" href="index.php?module=caja&profile=client&client=<?= rawurlencode($clienteCodigo) ?>">Perfil Cliente</a>
            <a class="nav-link" href="index.php?module=caja&action=aportes_fonbes_form&client=<?= rawurlencode($clienteCodigo) ?>">Aportes FONBES</a>
            <a class="nav-link" href="index.php?module=caja&action=aportes_coopam_form&client=<?= rawurlencode($clienteCodigo) ?>">Aportes COOPAM</a>
            <a class="nav-link" href="index.php?module=caja&action=aportes_combinados_form&client=<?= rawurlencode($clienteCodigo) ?>">Pago FONBES + COOPAM</a>
            <a class="nav-link" href="index.php?module=caja&action=desembolso_prestamo_form&client=<?= rawurlencode($clienteCodigo) ?>&loan=<?= $prestamoId ?>">Desembolso Préstamo</a>
            <a class="nav-link" href="index.php?module=caja&action=sobregiro_prestamo_form&client=<?= rawurlencode($clienteCodigo) ?>">Sobregiro Préstamo</a>
            <a class="nav-link active" href="index.php?module=caja&action=amortizar_form&client=<?= rawurlencode($clienteCodigo) ?>&loan=<?= $prestamoId ?>">Amortización</a>
            <a class="nav-link" href="index.php?module=caja&action=pago_integral_seleccion&client=<?= rawurlencode($clienteCodigo) ?>">Pago Integral</a>
            <a class="nav-link" href="index.php?module=caja&action=multas_form&client=<?= rawurlencode($clienteCodigo) ?>">Multas</a>
            <a class="nav-link" href="index.php?module=caja&action=devolucion_fonbes_form&client=<?= rawurlencode($clienteCodigo) ?>">Devolución FONBES</a>
            <a class="nav-link" href="index.php?module=caja&action=devolucion_coopam_form&client=<?= rawurlencode($clienteCodigo) ?>">Devolución COOPAM</a>
            <a class="nav-link" href="index.php?module=caja&action=cancelacion_aee_form&client=<?= rawurlencode($clienteCodigo) ?>&loan=<?= $prestamoId ?>">Cancelación AEE</a>
            <a class="nav-link" href="index.php?module=caja&action=pago_rifa_form&client=<?= rawurlencode($clienteCodigo) ?>">Pago Rifa</a>
            <a class="nav-link" href="index.php?module=caja&action=pago_sobregiro_fonbes_form&client=<?= rawurlencode($clienteCodigo) ?>">Pago Sobregiro FONBES</a>
            <a class="nav-link" href="index.php?module=caja&action=pago_sobregiro_coopam_form&client=<?= rawurlencode($clienteCodigo) ?>">Pago Sobregiro COOPAM</a>
            <a class="nav-link" href="index.php?module=caja&action=otras_devoluciones_form&client=<?= rawurlencode($clienteCodigo) ?>">Otras Devoluciones</a>
            <a class="nav-link" href="index.php?module=caja&action=otros_ingresos_form&client=<?= rawurlencode($clienteCodigo) ?>">Otros Ingresos</a>
            <a class="nav-link" href="index.php?module=caja&action=desembolso_beneficios_form&client=<?= rawurlencode($clienteCodigo) ?>">Desembolso Beneficios</a>
            <a class="nav-link" href="index.php?module=caja&action=egresos_form&client=<?= rawurlencode($clienteCodigo) ?>">Egresos</a>
        </nav>

        <div class="sidebar-footer">
            Usuario: <?= $escape($user['username']) ?><br>
            Perfil: <?= $escape($profile) ?><br>
            ID: <?= $escape($userId) ?><br>
            Supervisor: <?= $supervisor ? 'Sí' : 'No' ?>
        </div>
    </aside>

    <main class="main">
        <div class="topbar">
            <div class="breadcrumb">Caja / Amortización AEE</div>
            <div class="user-menu">
                <div class="avatar"><?= strtoupper(substr($user['username'], 0, 1)) ?></div>
                <div class="user-copy">
                    <span class="user-name"><?= $escape($user['username']) ?></span>
                    <span class="user-role">Perfil <?= $escape($profile) ?></span>
                </div>
                <a class="logout" href="index.php?logout=1">Cerrar sesión</a>
            </div>
        </div>

        <p class="eyebrow">Operación de caja</p>
        <h1>Amortización AEE</h1>
        <p class="intro">Registro de pago de cuota de préstamo siguiendo el flujo legacy del sistema.</p>

        <?php if ($formError !== null): ?>
            <div class="error">
                <?= $escape($formError) ?>
            </div>
        <?php endif; ?>

        <div class="info-box">
            <h3>Información del Cliente</h3>
            <p><strong>Nombre:</strong> <?= $escape($cliente['full_name'] ?? 'Sin nombre') ?></p>
            <p><strong>Documento:</strong> <?= $escape($cliente['document'] ?? '') ?></p>
            <p><strong>Código:</strong> <?= $escape($clienteCodigo) ?></p>
            <p><strong>Condición:</strong> <?= $escape($cliente['condition'] ?? '-') ?></p>
        </div>

        <div class="info-box">
            <h3>Información del Préstamo</h3>
            <p><strong>Código:</strong> <?= $escape((string)($prestamo['f16_codigo'] ?? '')) ?></p>
            <p><strong>Importe:</strong> <?= $money($prestamo['f16_importe'] ?? 0) ?></p>
            <p><strong>Saldo:</strong> <?= $money($prestamo['f16_saldo'] ?? 0) ?></p>
            <p><strong>Cuotas Pendientes:</strong> <?= $escape((string)($prestamo['f16_cuotasPend'] ?? 0)) ?></p>
            <p><strong>Gastos Administrativos:</strong> <?= $money($prestamo['f16_gastosa'] ?? 0) ?></p>
            <p><strong>Mora:</strong> <?= $money($prestamo['f16_gastosm'] ?? 0) ?></p>
            <p><strong>Sobregiro:</strong> <?= $money($prestamo['f16_sobre'] ?? 0) ?></p>
        </div>

        <?php if (round($prestamo['f16_sobre'] ?? 0, 2) > 0): ?>
        <div class="alert-box" style="background: #fff3cd; border: 2px solid #ffc107; padding: 15px; margin-bottom: 20px; border-radius: 5px;">
            <h3 style="color: #856404; margin: 0 0 10px 0;">⚠️ ALERTA: Socio con Sobregiro</h3>
            <p style="color: #856404; margin: 0;">El socio tiene un sobregiro pendiente de pago de <?= $money($prestamo['f16_sobre']) ?>. Debe pagar el sobregiro antes de continuar con la amortización.</p>
        </div>
        <?php endif; ?>

        <form action="index.php" method="POST">
            <input type="hidden" name="csrf_token" value="<?= $escape($csrfToken) ?>">
            <input type="hidden" name="module" value="caja">
            <input type="hidden" name="action" value="amortizar">
            <input type="hidden" name="operacion" value="2">
            <input type="hidden" name="cliente" value="<?= $escape($clienteCodigo) ?>">
            <input type="hidden" name="prestamo" value="<?= $escape((string)($prestamoId)) ?>">

            <h3>Selección de Cuota</h3>
            <div class="form-group">
                <label for="cuota">Cuota a Pagar</label>
                <select id="cuota" name="cuota" required>
                    <option value="">-- Seleccione Cuota --</option>
                    <?php foreach ($cuotas as $cuota): ?>
                        <option value="<?= $escape((string)$cuota['f17_ncuota']) ?>"
                                data-capital="<?= $money($cuota['f17_capital'] ?? 0) ?>"
                                data-interes="<?= $money($cuota['f17_interes'] ?? 0) ?>"
                                data-total="<?= $money($cuota['f17_total'] ?? 0) ?>"
                                data-fecha="<?= $escape($cuota['f17_fecha']) ?>">
                            Cuota <?= $escape((string)$cuota['f17_ncuota']) ?> - 
                            <?= $escape($cuota['f17_fecha']) ?> - 
                            Total: <?= $money($cuota['f17_total'] ?? 0) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div id="cuota-info" class="cuota-info" style="display: none;">
                <h4>Detalles de la Cuota Seleccionada</h4>
                <p><strong>Capital:</strong> <span id="capital-display">-</span></p>
                <p><strong>Interés:</strong> <span id="interes-display">-</span></p>
                <p><strong>Total:</strong> <span id="total-display">-</span></p>
                <p><strong>Fecha:</strong> <span id="fecha-display">-</span></p>
            </div>

            <h3>Datos del Pago</h3>
            <div class="form-row-compact">
                <div class="form-group">
                    <label for="amortiza">Amortización (Capital)</label>
                    <input type="text" id="amortiza" name="amortiza" step="0.01" placeholder="0.00" value="<?= $escape($getFormValue('amortiza')) ?>">
                </div>
                <div class="form-group">
                    <label for="amortiza_codigo">Código</label>
                    <input type="text" id="amortiza_codigo" name="amortiza_codigo" class="compact" list="listaCodigosContables" placeholder="Código" value="<?= $escape($getFormValue('amortiza_codigo')) ?>" onchange="actualizarCodigoCompleto('amortiza_codigo')">
                </div>
                <div class="form-group">
                    <label for="amortiza_tipo">Tipo</label>
                    <select id="amortiza_tipo" name="amortiza_tipo" class="compact">
                        <option value="" <?= $getSelected('amortiza_tipo', '') ?>>-</option>
                        <option value="debe" <?= $getSelected('amortiza_tipo', 'debe') ?>>Debe</option>
                        <option value="haber" <?= $getSelected('amortiza_tipo', 'haber') ?>>Haber</option>
                    </select>
                </div>
            </div>

            <div class="form-row-compact">
                <div class="form-group">
                    <label for="gastosAdm">Gastos Administrativos(Interés)</label>
                    <input type="text" id="gastosAdm" name="gastosAdm" step="0.01" placeholder="0.00" value="<?= $escape($getFormValue('gastosAdm')) ?>">
                </div>
                <div class="form-group">
                    <label for="gastosAdm_codigo">Código</label>
                    <input type="text" id="gastosAdm_codigo" name="gastosAdm_codigo" class="compact" list="listaCodigosContables" placeholder="Código" value="<?= $escape($getFormValue('gastosAdm_codigo')) ?>" onchange="actualizarCodigoCompleto('gastosAdm_codigo')">
                </div>
                <div class="form-group">
                    <label for="gastosAdm_tipo">Tipo</label>
                    <select id="gastosAdm_tipo" name="gastosAdm_tipo" class="compact">
                        <option value="" <?= $getSelected('gastosAdm_tipo', '') ?>>-</option>
                        <option value="debe" <?= $getSelected('gastosAdm_tipo', 'debe') ?>>Debe</option>
                        <option value="haber" <?= $getSelected('gastosAdm_tipo', 'haber') ?>>Haber</option>
                    </select>
                </div>
            </div>

            <div class="form-row-compact">
                <div class="form-group">
                    <label for="gastosMora">Mora</label>
                    <input type="text" id="gastosMora" name="gastosMora" step="0.01" value="<?= $escape($getFormValue('gastosMora', $money($prestamo['f16_gastosm'] ?? 0))) ?>">
                </div>
                <div class="form-group">
                    <label for="gastosMora_codigo">Código</label>
                    <input type="text" id="gastosMora_codigo" name="gastosMora_codigo" class="compact" list="listaCodigosContables" placeholder="Código" value="<?= $escape($getFormValue('gastosMora_codigo')) ?>" onchange="actualizarCodigoCompleto('gastosMora_codigo')">
                </div>
                <div class="form-group">
                    <label for="gastosMora_tipo">Tipo</label>
                    <select id="gastosMora_tipo" name="gastosMora_tipo" class="compact">
                        <option value="" <?= $getSelected('gastosMora_tipo', '') ?>>-</option>
                        <option value="debe" <?= $getSelected('gastosMora_tipo', 'debe') ?>>Debe</option>
                        <option value="haber" <?= $getSelected('gastosMora_tipo', 'haber') ?>>Haber</option>
                    </select>
                </div>
            </div>

            <div class="form-row-compact">
                <div class="form-group">
                    <label for="otros1">Otros 1</label>
                    <input type="text" id="otros1" name="otros1" step="0.01" value="<?= $escape($getFormValue('otros1', $money($prestamo['f16_otros'] ?? 0))) ?>">
                </div>
                <div class="form-group">
                    <label for="otros1_codigo">Código</label>
                    <input type="text" id="otros1_codigo" name="otros1_codigo" class="compact" list="listaCodigosContables" placeholder="Código" value="<?= $escape($getFormValue('otros1_codigo')) ?>" onchange="actualizarCodigoCompleto('otros1_codigo')">
                </div>
                <div class="form-group">
                    <label for="otros1_tipo">Tipo</label>
                    <select id="otros1_tipo" name="otros1_tipo" class="compact">
                        <option value="" <?= $getSelected('otros1_tipo', '') ?>>-</option>
                        <option value="debe" <?= $getSelected('otros1_tipo', 'debe') ?>>Debe</option>
                        <option value="haber" <?= $getSelected('otros1_tipo', 'haber') ?>>Haber</option>
                    </select>
                </div>
            </div>

            <div class="form-row-compact">
                <div class="form-group">
                    <label for="otros2">Otros 2</label>
                    <input type="text" id="otros2" name="otros2" step="0.01" value="<?= $escape($getFormValue('otros2', $money($prestamo['f16_otros2'] ?? 0))) ?>">
                </div>
                <div class="form-group">
                    <label for="otros2_codigo">Código</label>
                    <input type="text" id="otros2_codigo" name="otros2_codigo" class="compact" list="listaCodigosContables" placeholder="Código" value="<?= $escape($getFormValue('otros2_codigo')) ?>" onchange="actualizarCodigoCompleto('otros2_codigo')">
                </div>
                <div class="form-group">
                    <label for="otros2_tipo">Tipo</label>
                    <select id="otros2_tipo" name="otros2_tipo" class="compact">
                        <option value="" <?= $getSelected('otros2_tipo', '') ?>>-</option>
                        <option value="debe" <?= $getSelected('otros2_tipo', 'debe') ?>>Debe</option>
                        <option value="haber" <?= $getSelected('otros2_tipo', 'haber') ?>>Haber</option>
                    </select>
                </div>
            </div>

            <div class="form-row-compact">
                <div class="form-group">
                    <label for="sobregiro">Sobregiro</label>
                    <?php
                    // Si el sobregiro es negativo (saldo a favor), mostrarlo como 0 (como el sistema legacy)
                    $sobregiroValor = ($prestamo['f16_sobre'] ?? 0) < 0 ? 0 : ($prestamo['f16_sobre'] ?? 0);
                    ?>
                    <input type="text" id="sobregiro" name="sobregiro" step="0.01" value="<?= $escape($getFormValue('sobregiro', $money($sobregiroValor))) ?>">
                </div>
                <div class="form-group">
                    <label for="sobregiro_codigo">Código</label>
                    <input type="text" id="sobregiro_codigo" name="sobregiro_codigo" class="compact" list="listaCodigosContables" placeholder="Código" value="<?= $escape($getFormValue('sobregiro_codigo')) ?>" onchange="actualizarCodigoCompleto('sobregiro_codigo')">
                </div>
                <div class="form-group">
                    <label for="sobregiro_tipo">Tipo</label>
                    <select id="sobregiro_tipo" name="sobregiro_tipo" class="compact">
                        <option value="" <?= $getSelected('sobregiro_tipo', '') ?>>-</option>
                        <option value="debe" <?= $getSelected('sobregiro_tipo', 'debe') ?>>Debe</option>
                        <option value="haber" <?= $getSelected('sobregiro_tipo', 'haber') ?>>Haber</option>
                    </select>
                </div>
            </div>

            <div class="form-row-compact">
                <div class="form-group">
                    <label for="monto">Total a Pagar</label>
                    <input type="text" id="monto" name="monto" step="0.01" value="<?= $escape($getFormValue('monto')) ?>">
                </div>
                <div class="form-group">
                    <label for="monto_codigo">Código</label>
                    <input type="text" id="monto_codigo" name="monto_codigo" class="compact" list="listaCodigosContables" placeholder="Código" value="<?= $escape($getFormValue('monto_codigo')) ?>" onchange="actualizarCodigoCompleto('monto_codigo')">
                </div>
                <div class="form-group">
                    <label for="monto_tipo">Tipo</label>
                    <select id="monto_tipo" name="monto_tipo" class="compact">
                        <option value="" <?= $getSelected('monto_tipo', '') ?>>-</option>
                        <option value="debe" <?= $getSelected('monto_tipo', 'debe') ?>>Debe</option>
                        <option value="haber" <?= $getSelected('monto_tipo', 'haber') ?>>Haber</option>
                    </select>
                </div>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label for="formaPago">Forma de Pago</label>
                    <select id="formaPago" name="formaPago">
                        <option value="">-- Seleccione --</option>
                        <option value="EFECTIVO" <?= $getSelected('formaPago', 'EFECTIVO') ?>>Efectivo</option>
                        <option value="DEPOSITO" <?= $getSelected('formaPago', 'DEPOSITO') ?>>Depósito</option>
                        <option value="TRANSFERENCIA" <?= $getSelected('formaPago', 'TRANSFERENCIA') ?>>Transferencia</option>
                        <option value="CHEQUE" <?= $getSelected('formaPago', 'CHEQUE') ?>>Cheque</option>
                        <option value="PLANILLA" <?= $getSelected('formaPago', 'PLANILLA') ?>>Planilla</option>
                    </select>
                </div>
            </div>

            <div class="form-group">
                <label for="fecha">Fecha</label>
                <input type="date" id="fecha" name="fecha" value="<?= $escape($getFormValue('fecha', date('Y-m-d'))) ?>">
            </div>

            <div class="form-group">
                <label for="obs">Observaciones</label>
                <textarea id="obs" name="obs"><?= $escape($getFormValue('obs')) ?></textarea>
            </div>

            <div class="form-group">
                <label for="obs2">Observaciones 2</label>
                <textarea id="obs2" name="obs2"><?= $escape($getFormValue('obs2')) ?></textarea>
            </div>

            <div class="form-actions">
                <button type="submit" class="btn">Registrar Pago</button>
                <a href="index.php?module=caja&profile=client&client=<?= rawurlencode($clienteCodigo) ?>" class="btn btn-secondary">Cancelar</a>
            </div>
        </form>
</main>
</div>

<!-- Datalist de códigos contables -->
<datalist id="listaCodigosContables">
    <?php foreach ($codigoContable as $codigo): ?>
        <option value="<?= $escape($codigo['code']) ?>">
            <?= $escape($codigo['code']) ?> - <?= $escape($codigo['name']) ?>
        </option>
    <?php endforeach; ?>
</datalist>

<script>
    // Datos del cliente para lógica de códigos contables
    const clienteCondicion = '<?= $escape($cliente['condition'] ?? '') ?>';

    // Función para actualizar el código contable con formato completo "código - nombre"
    function actualizarCodigoCompleto(codigoInputId) {
        const codigoInput = document.getElementById(codigoInputId);
        const codigo = codigoInput.value.trim();

        if (!codigo) {
            return;
        }

        // Si el código ya está en formato completo, no hacer nada
        if (codigo.includes(' - ')) {
            return;
        }

        // Buscar el nombre en el datalist
        const datalist = document.getElementById('listaCodigosContables');
        let nombreEncontrado = '';

        for (const option of datalist.options) {
            if (option.value === codigo) {
                nombreEncontrado = option.textContent.split(' - ')[1] || '';
                break;
            }
        }

        // Si se encontró el nombre, actualizar el valor con formato completo
        if (nombreEncontrado) {
            codigoInput.value = codigo + ' - ' + nombreEncontrado;
        }
    }

    // Función para calcular el total automáticamente (incluye todos los campos)
    function calcularTotal() {
        const amortiza = parseFloat(document.getElementById('amortiza').value.replace(/,/g, '')) || 0;
        const gastosAdm = parseFloat(document.getElementById('gastosAdm').value.replace(/,/g, '')) || 0;
        const gastosMora = parseFloat(document.getElementById('gastosMora').value.replace(/,/g, '')) || 0;
        const otros1 = parseFloat(document.getElementById('otros1').value.replace(/,/g, '')) || 0;
        const otros2 = parseFloat(document.getElementById('otros2').value.replace(/,/g, '')) || 0;
        const sobregiro = parseFloat(document.getElementById('sobregiro').value.replace(/,/g, '')) || 0;

        console.log('Cálculo total:', {amortiza, gastosAdm, gastosMora, otros1, otros2, sobregiro, total: amortiza + gastosAdm + gastosMora + otros1 + otros2 + sobregiro});

        const total = amortiza + gastosAdm + gastosMora + otros1 + otros2 + sobregiro;
        const totalRedondeado = Math.round((total + Number.EPSILON) * 100) / 100;
        document.getElementById('monto').value = totalRedondeado.toFixed(2);
    }

    // Event listeners para cálculo automático en todos los campos
    ['amortiza', 'gastosAdm', 'gastosMora', 'otros1', 'otros2', 'sobregiro'].forEach(id => {
        const elemento = document.getElementById(id);
        if (elemento) {
            elemento.addEventListener('input', function() {
                console.log('Cambio en campo:', id, 'Valor:', this.value);
                calcularTotal();
            });
        } else {
            console.error('Elemento no encontrado:', id);
        }
    });

    // Calcular total al cargar la página (con valores pre-cargados)
    document.addEventListener('DOMContentLoaded', function() {
        calcularTotal();
        // Cargar códigos contables al cargar si hay montos pre-cargados
        cargarCodigosContablesAutomaticos();
    });

    // Event listener para actualizar código contable del total cuando cambia la forma de pago
    document.getElementById('formaPago').addEventListener('change', cargarCodigosContablesAutomaticos);

    // Función para obtener el código contable según la condición del cliente
    function obtenerCodigoSegunCondicion(codigoBase) {
        // Si la condición es "SOCIO", no agregar el último dígito
        if (clienteCondicion === 'SOCIO') {
            return codigoBase.slice(0, -1); // Eliminar el último dígito (ej: 1651 → 165)
        }
        // Si la condición es "ACTIVO", mantener el código completo (termina en 1)
        if (clienteCondicion === 'ACTIVO') {
            return codigoBase; // Mantener (ej: 1651 → 1651)
        }
        // Si la condición es "PENSIONISTA", cambiar último dígito a 2
        if (clienteCondicion === 'PENSIONISTA') {
            return codigoBase.slice(0, -1) + '2'; // (ej: 1651 → 1652)
        }
        // Si la condición es "FEBAN", cambiar último dígito a 3
        if (clienteCondicion === 'FEBAN') {
            return codigoBase.slice(0, -1) + '3'; // (ej: 1651 → 1653)
        }
        // Si la condición es "OTROS SOCIOS", cambiar último dígito a 4
        if (clienteCondicion === 'OTROS SOCIOS') {
            return codigoBase.slice(0, -1) + '4'; // (ej: 1651 → 1654)
        }
        // Por defecto, mantener el código completo
        return codigoBase;
    }

    // Función para obtener el nombre completo del código contable del datalist
    function obtenerNombreCodigoContable(codigo) {
        const datalist = document.getElementById('listaCodigosContables');
        if (!datalist) {
            console.error('Datalist listaCodigosContables no encontrado');
            return codigo;
        }
        for (const option of datalist.options) {
            // Buscar si el código está al inicio del valor del datalist
            if (option.value.startsWith(codigo) || option.value === codigo) {
                return option.value;
            }
        }
        // Si no se encuentra exactamente, buscar el código base sin el último dígito
        if (codigo.length > 1) {
            const codigoBase = codigo.slice(0, -1);
            for (const option of datalist.options) {
                if (option.value.startsWith(codigoBase)) {
                    return option.value; // Retornar el código completo del datalist
                }
            }
        }
        return codigo; // Si no se encuentra, retorna solo el código
    }

    // Función para cargar códigos contables automáticamente
    function cargarCodigosContablesAutomaticos() {
        const amortiza = parseFloat(document.getElementById('amortiza').value.replace(/,/g, '')) || 0;
        const gastosAdm = parseFloat(document.getElementById('gastosAdm').value.replace(/,/g, '')) || 0;
        const gastosMora = parseFloat(document.getElementById('gastosMora').value.replace(/,/g, '')) || 0;
        const otros1 = parseFloat(document.getElementById('otros1').value.replace(/,/g, '')) || 0;
        const otros2 = parseFloat(document.getElementById('otros2').value.replace(/,/g, '')) || 0;
        const sobregiro = parseFloat(document.getElementById('sobregiro').value.replace(/,/g, '')) || 0;
        const formaPago = document.getElementById('formaPago').value;

        console.log('Cargando códigos contables. Forma de pago:', formaPago, 'Condición:', clienteCondicion);

        // Cargar código según forma de pago para el total
        let codigoTotal = '';
        if (formaPago === 'EFECTIVO') {
            codigoTotal = '1011';
        } else if (formaPago === 'DEPOSITO') {
            codigoTotal = '10612';
        } else if (formaPago === 'TRANSFERENCIA' || formaPago === 'CHEQUE' || formaPago === 'PLANILLA') {
            codigoTotal = '10612';
        }

        console.log('Código total determinado:', codigoTotal);

        // Amortización (Capital) - Código 1651 si monto > 0
        if (amortiza > 0) {
            const codigoBase = '1651';
            const codigoFinal = obtenerCodigoSegunCondicion(codigoBase);
            const codigoCompleto = obtenerNombreCodigoContable(codigoFinal);
            document.getElementById('amortiza_codigo').value = codigoCompleto;
            document.getElementById('amortiza_tipo').value = 'haber';
            actualizarCodigoCompleto('amortiza_codigo'); // Mostrar nombre completo
            console.log('Amortización código cargado:', codigoCompleto, '(Condición:', clienteCondicion, ')');
        }

        // Gastos Administrativos (Interés) - Código 7021 si monto > 0
        if (gastosAdm > 0) {
            const codigoBase = '7021';
            const codigoFinal = obtenerCodigoSegunCondicion(codigoBase);
            const codigoCompleto = obtenerNombreCodigoContable(codigoFinal);
            document.getElementById('gastosAdm_codigo').value = codigoCompleto;
            document.getElementById('gastosAdm_tipo').value = 'haber';
            actualizarCodigoCompleto('gastosAdm_codigo'); // Mostrar nombre completo
            console.log('Gastos Adm código cargado:', codigoCompleto, '(Condición:', clienteCondicion, ')');
        }

        // Mora - Código 7041 si monto > 0
        if (gastosMora > 0) {
            const codigoBase = '7041';
            const codigoFinal = obtenerCodigoSegunCondicion(codigoBase);
            const codigoCompleto = obtenerNombreCodigoContable(codigoFinal);
            document.getElementById('gastosMora_codigo').value = codigoCompleto;
            document.getElementById('gastosMora_tipo').value = 'haber';
            actualizarCodigoCompleto('gastosMora_codigo'); // Mostrar nombre completo
            console.log('Mora código cargado:', codigoCompleto, '(Condición:', clienteCondicion, ')');
        }

        // Otros 1 - Código 46951 si monto > 0
        if (otros1 > 0) {
            const codigoBase = '46951';
            const codigoFinal = obtenerCodigoSegunCondicion(codigoBase);
            const codigoCompleto = obtenerNombreCodigoContable(codigoFinal);
            document.getElementById('otros1_codigo').value = codigoCompleto;
            document.getElementById('otros1_tipo').value = 'haber';
            actualizarCodigoCompleto('otros1_codigo'); // Mostrar nombre completo
            console.log('Otros1 código cargado:', codigoCompleto, '(Condición:', clienteCondicion, ')');
        }

        // Otros 2 - Código 46951 si monto > 0
        if (otros2 > 0) {
            const codigoBase = '46951';
            const codigoFinal = obtenerCodigoSegunCondicion(codigoBase);
            const codigoCompleto = obtenerNombreCodigoContable(codigoFinal);
            document.getElementById('otros2_codigo').value = codigoCompleto;
            document.getElementById('otros2_tipo').value = 'haber';
            actualizarCodigoCompleto('otros2_codigo'); // Mostrar nombre completo
            console.log('Otros2 código cargado:', codigoCompleto, '(Condición:', clienteCondicion, ')');
        }

        // Sobregiro - Código 1651 si monto > 0
        if (sobregiro > 0) {
            const codigoBase = '1651';
            const codigoFinal = obtenerCodigoSegunCondicion(codigoBase);
            const codigoCompleto = obtenerNombreCodigoContable(codigoFinal);
            document.getElementById('sobregiro_codigo').value = codigoCompleto;
            document.getElementById('sobregiro_tipo').value = 'haber';
            actualizarCodigoCompleto('sobregiro_codigo'); // Mostrar nombre completo
            console.log('Sobregiro código cargado:', codigoCompleto, '(Condición:', clienteCondicion, ')');
        }

        // Total - Código según forma de pago
        if (codigoTotal) {
            const codigoCompleto = obtenerNombreCodigoContable(codigoTotal);
            document.getElementById('monto_codigo').value = codigoCompleto;
            document.getElementById('monto_tipo').value = 'debe';
            actualizarCodigoCompleto('monto_codigo'); // Mostrar nombre completo
            console.log('Total código cargado:', codigoCompleto);
        } else {
            console.log('No se cargó código total porque forma de pago está vacía o no reconocida');
        }
    }

    // Mostrar información de la cuota seleccionada
    document.getElementById('cuota').addEventListener('change', function() {
        const selectedOption = this.options[this.selectedIndex];
        if (this.value) {
            document.getElementById('cuota-info').style.display = 'block';
            document.getElementById('capital-display').textContent = selectedOption.dataset.capital;
            document.getElementById('interes-display').textContent = selectedOption.dataset.interes;
            document.getElementById('total-display').textContent = selectedOption.dataset.total;
            document.getElementById('fecha-display').textContent = selectedOption.dataset.fecha;

            // Pre-llenar amortización con el capital de la cuota
            document.getElementById('amortiza').value = selectedOption.dataset.capital.replace(/,/g, '');

            // Pre-llenar gastos administrativos(interés) con el interés de la cuota
            document.getElementById('gastosAdm').value = selectedOption.dataset.interes.replace(/,/g, '');

            // Usar el total exacto de la cuota del cronograma para evitar errores de precisión
            const cuotaTotal = parseFloat(selectedOption.dataset.total.replace(/,/g, '')) || 0;
            document.getElementById('monto').value = cuotaTotal.toFixed(2);

            // Guardar el total esperado de la cuota (para no recalcular cuando se modifica capital/interés)
            window.totalEsperadoCuota = cuotaTotal;

            // Cargar códigos contables automáticamente según montos > 0
            cargarCodigosContablesAutomaticos();

            // Ejecutar cálculo de suma de todos los montos (incluyendo sobregiro, otros, etc.)
            calcularTotal();
        } else {
            document.getElementById('cuota-info').style.display = 'none';
            window.totalEsperadoCuota = null;
        }
    });
</script>
</body>
</html>
