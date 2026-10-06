<?php

declare(strict_types=1);

require dirname(__DIR__) . '/../config/bootstrap.php';

use Sacs\Core\Database;
use Sacs\Repositories\CajaRepository;
use Sacs\Security\Auth;

$auth = new Auth(new Sacs\Repositories\UserRepository());
if (!$auth->check()) {
    header('Location: index.php');
    exit;
}

$user = $auth->user();
$repository = new CajaRepository();
$database = Database::connection();
$codigoContable = $repository->accountingCodes();

$clienteCodigo = trim((string) ($_GET['client'] ?? ''));
$beneficioId = filter_var($_GET['benefit'] ?? null, FILTER_VALIDATE_INT);

if ($clienteCodigo === '' || $beneficioId === false || $beneficioId === null) {
    http_response_code(400);
    $error = 'Cliente o beneficio no especificado';
    require dirname(__DIR__) . '/views/home.php';
    exit;
}

// Obtener datos del cliente
$stmt = $database->prepare("
    SELECT
        CAST(c.F02_ID_CLIENTE AS UNSIGNED) AS client_id,
        TRIM(CONCAT_WS(' ', p.F01_APE_PAT, p.F01_APE_MAT, p.F01_NOMB_PRI, p.F01_NOMB_SEG)) AS full_name,
        p.F01_NUM_DOC AS document,
        p.F01_STDO_PERSN AS `condition`
    FROM f02_cliente c
    INNER JOIN f01_persona p ON p.F01_ID_PERSONA = c.F02_ID_PERSONA
    WHERE CAST(c.F02_ID_CLIENTE AS UNSIGNED) = CAST(:client AS UNSIGNED)
    LIMIT 1
");
$stmt->execute(['client' => $clienteCodigo]);
$cliente = $stmt->fetch();

if ($cliente === false) {
    http_response_code(404);
    $error = 'Cliente no encontrado';
    require dirname(__DIR__) . '/views/home.php';
    exit;
}

// Obtener datos del beneficio
require dirname(__DIR__, 2) . '/src/Services/DesembolsoBeneficiosServicio.php';
$servicio = new \Sacs\Services\DesembolsoBeneficiosServicio($database);
$beneficiosPendientes = $servicio->obtenerBeneficiosPendientes($clienteCodigo);
$beneficio = null;

foreach ($beneficiosPendientes as $b) {
    if ((int) $b['f21_codigo'] === $beneficioId) {
        $beneficio = $b;
        break;
    }
}

if ($beneficio === null) {
    http_response_code(404);
    $error = 'Beneficio no encontrado o ya procesado';
    require dirname(__DIR__) . '/views/home.php';
    exit;
}

$displayName = htmlspecialchars($user['username'], ENT_QUOTES, 'UTF-8');
$profile = htmlspecialchars((string) $user['profile'], ENT_QUOTES, 'UTF-8');
$userId = htmlspecialchars((string) $user['id'], ENT_QUOTES, 'UTF-8');
$supervisor = $user['supervisor'] === 'S';
$csrfToken = (string) $_SESSION['csrf_token'];

$escape = static fn (mixed $value): string => htmlspecialchars((string) ($value ?? ''), ENT_QUOTES, 'UTF-8');
$money = static fn (mixed $value): string => $value === null || $value === '' ? '-' : number_format((float) $value, 2, '.', ',');
?>
<!doctype html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Desembolso de Beneficio | SACS Nuevo</title>
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
        .nav-link { padding: 10px 12px; border-radius: 6px; color: #e7f0ed; font-size: 13px; transition: background .15s; }
        .nav-link:hover { background: var(--navy-soft); }
        .nav-link.active { background: var(--teal); }
        .main { padding: 40px 48px; overflow-y: auto; }
        .header { display: flex; align-items: center; justify-content: space-between; margin-bottom: 32px; }
        .header h1 { margin: 0; font-size: 28px; font-weight: 400; }
        .user { display: flex; align-items: center; gap: 12px; }
        .user-avatar { width: 36px; height: 36px; border-radius: 50%; background: var(--teal); color: white; display: grid; place-items: center; font-size: 14px; font-weight: bold; }
        .user-name { font-size: 13px; }
        .user-profile { font-size: 11px; color: var(--muted); }
        .panel { background: var(--panel); border-radius: 12px; padding: 24px; box-shadow: var(--shadow); margin-bottom: 24px; }
        .panel h2 { margin: 0 0 20px; font-size: 18px; font-weight: 400; }
        .summary { display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 16px; margin-bottom: 24px; }
        .summary div { background: var(--panel); padding: 16px; border-radius: 8px; }
        .summary .label { font-size: 11px; color: var(--muted); text-transform: uppercase; letter-spacing: .08em; margin-bottom: 4px; }
        .summary .value { font-size: 18px; font-weight: 600; }
        .summary .name { font-size: 14px; }
        .form-row { display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 16px; margin-bottom: 16px; }
        .form-group { display: flex; flex-direction: column; }
        .form-group label { font-size: 12px; font-weight: 600; color: var(--muted); margin-bottom: 6px; }
        .form-group input, .form-group select, .form-group textarea { padding: 10px 12px; border: 1px solid var(--line); border-radius: 6px; font-size: 13px; font-family: inherit; }
        .form-group input:focus, .form-group select:focus, .form-group textarea:focus { outline: none; border-color: var(--teal); }
        .form-group input[readonly] { background: var(--paper); }
        .form-actions { display: flex; gap: 12px; margin-top: 24px; }
        .btn { padding: 12px 24px; border: none; border-radius: 6px; font-size: 13px; font-weight: 600; cursor: pointer; transition: background .15s; }
        .btn-primary { background: var(--teal); color: white; }
        .btn-primary:hover { background: #166664; }
        .btn-secondary { background: var(--paper); color: var(--ink); }
        .btn-secondary:hover { background: #e8ebe9; }
        .alert { padding: 15px; margin-bottom: 20px; border-radius: 6px; }
        .alert-error { background: #fee; color: #c33; border: 1px solid #fcc; }
        @media (max-width: 900px) { .shell { grid-template-columns: 1fr; } .sidebar { display: none; } }
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
            <nav class="nav">
                <a href="index.php?module=caja&profile=client&client=<?= rawurlencode($clienteCodigo) ?>" class="nav-link">Perfil Cliente</a>
                <a href="index.php?module=caja&action=aportes_fonbes_form&client=<?= rawurlencode($clienteCodigo) ?>" class="nav-link">Aportes FONBES</a>
                <a href="index.php?module=caja&action=aportes_coopam_form&client=<?= rawurlencode($clienteCodigo) ?>" class="nav-link">Aportes COOPAM</a>
                <a href="index.php?module=caja&action=aportes_combinados_form&client=<?= rawurlencode($clienteCodigo) ?>" class="nav-link">Pago FONBES + COOPAM</a>
                <a href="index.php?module=caja&action=desembolso_prestamo_form&client=<?= rawurlencode($clienteCodigo) ?>" class="nav-link">Desembolso Préstamo</a>
                <a href="index.php?module=caja&action=sobregiro_prestamo_form&client=<?= rawurlencode($clienteCodigo) ?>" class="nav-link">Sobregiro Préstamo</a>
                <a href="index.php?module=caja&action=amortizar_form&client=<?= rawurlencode($clienteCodigo) ?>" class="nav-link">Amortización</a>
                <a href="index.php?module=caja&action=multas_form&client=<?= rawurlencode($clienteCodigo) ?>" class="nav-link">Multas</a>
                <a href="index.php?module=caja&action=devolucion_fonbes_form&client=<?= rawurlencode($clienteCodigo) ?>" class="nav-link">Devolución FONBES</a>
                <a href="index.php?module=caja&action=devolucion_coopam_form&client=<?= rawurlencode($clienteCodigo) ?>" class="nav-link">Devolución COOPAM</a>
                <a href="index.php?module=caja&action=cancelacion_aee_form&client=<?= rawurlencode($clienteCodigo) ?>" class="nav-link">Cancelación AEE</a>
                <a href="index.php?module=caja&action=pago_rifa_form&client=<?= rawurlencode($clienteCodigo) ?>" class="nav-link">Pago Rifa</a>
                <a href="index.php?module=caja&action=pago_sobregiro_fonbes_form&client=<?= rawurlencode($clienteCodigo) ?>" class="nav-link">Pago Sobregiro FONBES</a>
                <a href="index.php?module=caja&action=pago_sobregiro_coopam_form&client=<?= rawurlencode($clienteCodigo) ?>" class="nav-link">Pago Sobregiro COOPAM</a>
                <a href="index.php?module=caja&action=otras_devoluciones_form&client=<?= rawurlencode($clienteCodigo) ?>" class="nav-link">Otras Devoluciones</a>
                <a href="index.php?module=caja&action=otros_ingresos_form&client=<?= rawurlencode($clienteCodigo) ?>" class="nav-link">Otros Ingresos</a>
                <a href="index.php?module=caja&action=desembolso_beneficios_form&client=<?= rawurlencode($clienteCodigo) ?>" class="nav-link">Desembolso Beneficios</a>
                <a href="index.php?module=caja&action=egresos_form&client=<?= rawurlencode($clienteCodigo) ?>" class="nav-link">Egresos</a>
            </nav>
        </aside>

        <main class="main">
            <div class="header">
                <h1>Desembolso de Beneficio</h1>
                <div class="user">
                    <div class="user-avatar"><?= $displayName[0] ?></div>
                    <div>
                        <div class="user-name"><?= $displayName ?></div>
                        <div class="user-profile"><?= $profile ?></div>
                    </div>
                </div>
            </div>

            <?php if (isset($_GET['error'])): ?>
                <div class="alert alert-error">
                    <?= $escape($_GET['error']) ?>
                </div>
            <?php endif; ?>

            <section class="summary">
                <div><span class="label">Socio</span><div class="name"><?= $escape($cliente['full_name'] ?: 'Sin nombre') ?></div></div>
                <div><span class="label">Código</span><div class="value"><?= $escape($clienteCodigo) ?></div></div>
                <div><span class="label">Documento</span><div class="value"><?= $escape($cliente['document'] ?: '-') ?></div></div>
                <div><span class="label">Condición</span><div class="value"><?= $escape($cliente['condition'] ?: '-') ?></div></div>
            </section>

            <section class="panel">
                <h2>Información del Beneficio</h2>
                <div class="summary">
                    <div><span class="label">Código Beneficio</span><div class="value">#<?= $escape($beneficio['f21_codigo']) ?></div></div>
                    <div><span class="label">Beneficiario</span><div class="name"><?= $escape($beneficio['f21_beneficiario'] ?: '-') ?></div></div>
                    <div><span class="label">Tipo</span><div class="value"><?= $escape($beneficio['f21_tipo'] ?: '-') ?></div></div>
                    <div><span class="label">Fecha</span><div class="value"><?= $escape($beneficio['f21_fecha'] ?: '-') ?></div></div>
                    <div><span class="label">Importe</span><div class="value"><?= $money($beneficio['f21_importe']) ?></div></div>
                </div>
            </section>

            <section class="panel">
                <h2>Formulario de Desembolso</h2>
                <p style="color: var(--muted); margin-bottom: 20px;">Operación 7 - Desembolso de beneficios siguiendo el flujo legacy del sistema.</p>
                
                <form method="POST" action="index.php">
                    <input type="hidden" name="module" value="caja">
                    <input type="hidden" name="action" value="desembolso_beneficio">
                    <input type="hidden" name="cliente" value="<?= $escape($clienteCodigo) ?>">
                    <input type="hidden" name="beneficioId" value="<?= $escape($beneficioId) ?>">
                    <input type="hidden" name="monto" value="<?= $escape($beneficio['f21_importe']) ?>">
                    <input type="hidden" name="usuario" value="<?= $escape($displayName) ?>">
                    <input type="hidden" name="csrf_token" value="<?= $escape($csrfToken) ?>">

                    <div class="form-row">
                        <div class="form-group">
                            <label for="montoDisplay">Monto a Desembolsar</label>
                            <input type="text" id="montoDisplay" value="<?= $money($beneficio['f21_importe']) ?>" readonly>
                        </div>
                    </div>

                    <div class="form-row">
                        <div class="form-group">
                            <label for="formaPago">Forma de Pago</label>
                            <select id="formaPago" name="formaPago" required>
                                <option value="EFECTIVO">Efectivo</option>
                                <option value="DEPOSITO">Depósito</option>
                                <option value="TRANSFERENCIA">Transferencia</option>
                                <option value="CHEQUE">Cheque</option>
                            </select>
                        </div>
                    </div>

                    <div class="form-row">
                        <div class="form-group">
                            <label for="cheque">Número de Cheque (si aplica)</label>
                            <input type="text" id="cheque" name="cheque" placeholder="Número de cheque">
                        </div>
                        <div class="form-group">
                            <label for="montoLetras">Monto en Letras</label>
                            <input type="text" id="montoLetras" name="montoLetras" placeholder="Monto en letras">
                        </div>
                    </div>

                    <div class="form-row">
                        <div class="form-group">
                            <label for="codigoContable">Código Contable</label>
                            <input type="text" id="codigoContable" name="codigoContable" list="codigosContables" 
                                   value="1651" placeholder="Código contable">
                            <datalist id="codigosContables">
                                <?php foreach ($codigoContable as $codigo): ?>
                                    <option value="<?= $escape($codigo['code']) ?>">
                                        <?= $escape($codigo['code']) ?> - <?= $escape($codigo['name']) ?>
                                    </option>
                                <?php endforeach; ?>
                            </datalist>
                        </div>
                        <div class="form-group">
                            <label for="tipoAsiento">Tipo de Asiento</label>
                            <select id="tipoAsiento" name="tipoAsiento" required>
                                <option value="debe">Debe (Egreso)</option>
                                <option value="haber">Haber (Ingreso)</option>
                            </select>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="descripcionContable">Descripción Contable</label>
                        <input type="text" id="descripcionContable" name="descripcionContable" 
                                   value="Desembolso de Beneficio - <?= $escape($beneficio['f21_codigo']) ?>"
                                   placeholder="Descripción del asiento contable">
                    </div>

                    <div class="form-group">
                        <label for="obs">Observaciones</label>
                        <textarea id="obs" name="obs" rows="3" placeholder="Observaciones adicionales">DESEMBOLSO DE BENEFICIO</textarea>
                    </div>

                    <div class="form-group">
                        <label for="obs2">Observaciones Adicionales</label>
                        <textarea id="obs2" name="obs2" rows="2" placeholder="Observaciones secundarias"></textarea>
                    </div>

                    <div class="form-actions">
                        <button type="submit" class="btn btn-primary">Procesar Desembolso</button>
                        <a href="index.php?module=caja&action=desembolso_beneficios_form&client=<?= rawurlencode($clienteCodigo) ?>" class="btn btn-secondary">Cancelar</a>
                    </div>
                </form>
            </section>
        </main>
    </div>
</body>
</html>
