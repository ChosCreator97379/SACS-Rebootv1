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
$filtroAportes = trim((string) ($_GET['filtro'] ?? 'todos'));

if ($clienteCodigo === '') {
    http_response_code(400);
    $error = 'Cliente no especificado';
    require dirname(__DIR__) . '/views/home.php';
    exit;
}

// Obtener datos del cliente
$cliente = $repository->findClientProfile($clienteCodigo);
if ($cliente === null) {
    http_response_code(404);
    $error = 'Cliente no encontrado';
    require dirname(__DIR__) . '/views/home.php';
    exit;
}

// Obtener montos de aportes
require_once dirname(__DIR__) . '/../src/Services/AportesCoopamServicio.php';
$aportesCoopamServicio = new \Sacs\Services\AportesCoopamServicio($database);
$montoAporteCopeRaw = $aportesCoopamServicio->obtenerMontoAporteCope($clienteCodigo);
$montoAporteCope = $montoAporteCopeRaw ?? 0;

// Buscar clientes con ambos aportes configurados para recomendación
$sqlRecomendacion = "SELECT CAST(c.F02_ID_CLIENTE AS UNSIGNED) as cliente,
                            CONCAT(p.F01_APE_PAT, ' ', p.F01_NOMB_PRI) as nombre,
                            p.F01_APORTACION,
                            p.F01_APORTE_COPE
                    FROM f01_persona p
                    INNER JOIN f02_cliente c ON c.F02_ID_PERSONA = p.F01_ID_PERSONA
                    WHERE p.F01_APORTACION > 0 AND p.F01_APORTE_COPE > 0
                    LIMIT 5";
$stmtRecomendacion = $database->prepare($sqlRecomendacion);
$stmtRecomendacion->execute();
$clientesRecomendados = $stmtRecomendacion->fetchAll();

require_once dirname(__DIR__) . '/../src/Services/AportesServicio.php';
$aportesFonbesServicio = new \Sacs\Services\AportesServicio($database);
$montoAporteFonbesRaw = $aportesFonbesServicio->obtenerMontoAporteFonbes($clienteCodigo);
$montoAporteFonbes = $montoAporteFonbesRaw ?? 0;

// Obtener aportes combinados con filtro
$aportesCombinados = $repository->clientCombinedContributions($clienteCodigo, $filtroAportes);

// Datos del socio para códigos contables automáticos
$condicion = strtoupper(trim($cliente['condition'] ?? ''));
$aporteFonbesCodigo = match($condicion) {
    'SOCIO' => '46933',
    'ACTIVO' => '469331',
    'PENSIONISTA' => '469332',
    'FEBAN' => '469333',
    'OTROS SOCIOS' => '469334',
    default => '46933'
};

$aporteCoopamCodigo = match($condicion) {
    'SOCIO' => '46932',
    'ACTIVO' => '469321',
    'PENSIONISTA' => '469322',
    'FEBAN' => '469323',
    'OTROS SOCIOS' => '469324',
    default => '469321'
};

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
    <title>Pago FONBES + COOPAM | SACS Nuevo</title>
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
        .panel h3 { margin: 0 0 16px; font-size: 14px; font-weight: 600; color: var(--muted); text-transform: uppercase; letter-spacing: .08em; }
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
        .alert-warning { background: #ffc; color: #963; border: 1px solid #cc9; }
        .alert-info { background: #eef; color: #36c; border: 1px solid #ccf; }
        table { width: 100%; border-collapse: collapse; }
        th { text-align: left; font-size: 11px; font-weight: 600; color: var(--muted); text-transform: uppercase; letter-spacing: .08em; padding: 12px 16px; border-bottom: 1px solid var(--line); }
        td { padding: 12px 16px; border-bottom: 1px solid var(--line); font-size: 13px; }
        td.amount { font-family: monospace; }
        .empty { color: var(--muted); font-style: italic; padding: 20px 0; text-align: center; }
        select.select-filter { padding: 5px; border-radius: 4px; border: 1px solid var(--line); font-size: 12px; margin-left: 10px; }
    </style>
</head>
<body>
    <div class="shell">
        <aside class="sidebar">
            <a href="index.php" class="brand">
                <div class="brand-mark">S</div>
                <div>
                    <div class="brand-name">SACS</div>
                    <div class="brand-note">Sistema de Administración</div>
                </div>
            </a>
            <div class="nav-label">Caja</div>
            <nav class="nav">
                <a href="index.php?module=caja&profile=client&client=<?= rawurlencode($clienteCodigo) ?>" class="nav-link">Perfil Cliente</a>
                <a href="index.php?module=caja&action=aportes_fonbes_form&client=<?= rawurlencode($clienteCodigo) ?>" class="nav-link">Aportes FONBES</a>
                <a href="index.php?module=caja&action=aportes_coopam_form&client=<?= rawurlencode($clienteCodigo) ?>" class="nav-link">Aportes COOPAM</a>
                <a href="index.php?module=caja&action=aportes_combinados_form&client=<?= rawurlencode($clienteCodigo) ?>" class="nav-link active">Pago FONBES + COOPAM</a>
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
                <h1>Pago FONBES + COOPAM</h1>
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
                    <?php if (strpos($_GET['error'], 'ESTE_SOCIO_SOLO_COOPAM') !== false): ?>
                        <br><br>
                        <a href="index.php?module=caja&action=aportes_coopam_form&client=<?= rawurlencode($clienteCodigo) ?>" class="btn btn-primary" style="display: inline-block; margin-top: 10px;">Ir a pago de COOPAM</a>
                    <?php elseif (strpos($_GET['error'], 'ESTE_SOCIO_SOLO_FONBES') !== false): ?>
                        <br><br>
                        <a href="index.php?module=caja&action=aportes_fonbes_form&client=<?= rawurlencode($clienteCodigo) ?>" class="btn btn-primary" style="display: inline-block; margin-top: 10px;">Ir a pago de FONBES</a>
                    <?php endif; ?>
                </div>
            <?php endif; ?>

            <section class="summary">
                <div><span class="label">Socio</span><div class="name"><?= $escape($cliente['client_id'] ?? '') ?></div></div>
                <div><span class="label">Nombre</span><div class="value"><?= $escape($cliente['full_name'] ?? 'Sin nombre') ?></div></div>
                <div><span class="label">Documento</span><div class="value"><?= $escape($cliente['document'] ?? '-') ?></div></div>
                <div><span class="label">Condicion</span><div class="value"><?= $escape($cliente['condition'] ?? '-') ?></div></div>
                <div><span class="label">Código FONBES</span><div class="value"><?= $escape($aporteFonbesCodigo) ?></div></div>
                <div><span class="label">Código COOPAM</span><div class="value"><?= $escape($aporteCoopamCodigo) ?></div></div>
            </section>

    <?php if (!empty($clientesRecomendados)): ?>
    <section class="panel" style="background: #e8f5e9; border: 1px solid #81c784;">
        <h2 style="color: #2e7d32;">Clientes recomendados para probar pago combinado</h2>
        <table>
            <thead>
                <tr>
                    <th>Socio</th>
                    <th>Nombre</th>
                    <th>FONBES</th>
                    <th>COOPAM</th>
                    <th>Acción</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($clientesRecomendados as $clienteRec): ?>
                <tr>
                    <td><?= $escape($clienteRec['cliente']) ?></td>
                    <td><?= $escape($clienteRec['nombre']) ?></td>
                    <td class="amount"><?= $money($clienteRec['F01_APORTACION']) ?></td>
                    <td class="amount"><?= $money($clienteRec['F01_APORTE_COPE']) ?></td>
                    <td>
                        <a href="index.php?module=caja&action=aportes_combinados_form&client=<?= rawurlencode((string) $clienteRec['cliente']) ?>" class="btn btn-primary" style="padding: 6px 12px; font-size: 11px;">Probar</a>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </section>
    <?php endif; ?>

    <section class="panel">
        <h2>Aportes
            <select class="select-filter" onchange="aplicarFiltro()">
                <option value="todos" <?= $filtroAportes === 'todos' ? 'selected' : '' ?>>Todos</option>
                <option value="pagados" <?= $filtroAportes === 'pagados' ? 'selected' : '' ?>>Pagados (completos)</option>
                <option value="pendientes" <?= $filtroAportes === 'pendientes' ? 'selected' : '' ?>>Pendientes (incompletos)</option>
            </select>
        </h2>
        <?php if (empty($aportesCombinados)): ?>
            <p class="empty">No hay aportes registrados para este cliente.</p>
        <?php else: ?>
            <table>
                <thead>
                    <tr>
                        <th>Tipo</th>
                        <th>Periodo</th>
                        <th>Planificado</th>
                        <th>Pagado</th>
                        <th>Fecha Pago</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($aportesCombinados as $aporte): ?>
                        <tr>
                            <td><?= $escape($aporte['tipo']) ?></td>
                            <td><?= $escape($aporte['period']) ?></td>
                            <td class="amount"><?= $money($aporte['planned']) ?></td>
                            <td class="amount"><?= $money($aporte['paid']) ?></td>
                            <td><?= $escape($aporte['paid_date']) ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php endif; ?>
    </section>

            <section class="panel">
                <h2>Registrar Pago Combinado</h2>
                <form action="index.php" method="POST">
                    <input type="hidden" name="csrf_token" value="<?= $escape($csrfToken) ?>">
                    <input type="hidden" name="module" value="caja">
                    <input type="hidden" name="action" value="aportes_combinados">
                    <input type="hidden" name="cliente" value="<?= $escape($clienteCodigo) ?>">

                    <h3>Periodo de Pago</h3>
                    <div class="form-row">
                        <div class="form-group">
                            <label for="anio">Año</label>
                            <input type="number" id="anio" name="anio" value="<?= date('Y') ?>" required min="2000" max="2100">
                        </div>
                        <div class="form-group">
                            <label for="periodo">Mes</label>
                            <select id="periodo" name="periodo" required>
                                <?php for ($i = 1; $i <= 12; $i++): ?>
                                    <option value="<?= $i ?>" <?= $i === (int) date('m') ? 'selected' : '' ?>>
                                        <?= str_pad((string) $i, 2, '0', STR_PAD_LEFT) ?>
                                    </option>
                                <?php endfor; ?>
                            </select>
                        </div>
                    </div>

                    <h3>Montos de Aporte</h3>
                    <div class="form-row">
                        <div class="form-group">
                            <label for="montoFonbes">Monto FONBES</label>
                            <input type="text" id="montoFonbes_display" step="0.01" readonly value="<?= $money($montoAporteFonbes) ?>" <?= $montoAporteFonbes > 0 ? '' : 'disabled style="background: #f5f5f5;"' ?>>
                            <input type="hidden" name="montoFonbes" value="<?= $montoAporteFonbes ?>">
                            <?php if ($montoAporteFonbes == 0): ?>
                                <small style="color: var(--muted);">Este socio no tiene FONBES configurado</small>
                            <?php endif; ?>
                        </div>
                        <div class="form-group">
                            <label for="montoCoopam">Monto COOPAM</label>
                            <input type="text" id="montoCoopam_display" step="0.01" readonly value="<?= $money($montoAporteCope) ?>" <?= $montoAporteCope > 0 ? '' : 'disabled style="background: #f5f5f5;"' ?>>
                            <input type="hidden" name="montoCoopam" value="<?= $montoAporteCope ?>">
                            <?php if ($montoAporteCope == 0): ?>
                                <small style="color: var(--muted);">Este socio no tiene COOPAM configurado</small>
                            <?php endif; ?>
                        </div>
                    </div>

                    <h3>Datos del Pago</h3>
                    <div class="form-row">
                        <div class="form-group">
                            <label for="formaPago">Forma de Pago</label>
                            <select id="formaPago" name="formaPago" required onchange="actualizarCodigosTotales()">
                                <option value="">Seleccione</option>
                                <option value="EFECTIVO">Efectivo</option>
                                <option value="DEPOSITO">Depósito</option>
                                <option value="TRANSFERENCIA">Transferencia</option>
                                <option value="CHEQUE">Cheque</option>
                            </select>
                        </div>
                    </div>

                    <h3>Códigos Contables FONBES</h3>
                    <div class="form-row">
                        <div class="form-group">
                            <label for="aporteFonbesCodigo">Aporte FONBES - Código</label>
                            <input type="text" id="aporteFonbesCodigo" name="aporteFonbesCodigo" value="<?= $escape($aporteFonbesCodigo) ?>" readonly>
                        </div>
                        <div class="form-group">
                            <label for="aporteFonbesTipo">Aporte FONBES - Tipo</label>
                            <select id="aporteFonbesTipo" name="aporteFonbesTipo">
                                <option value="haber" selected>Haber (Ingreso)</option>
                                <option value="debe">Debe (Egreso)</option>
                            </select>
                        </div>
                    </div>
                    <div class="form-row">
                        <div class="form-group">
                            <label for="totalFonbesCodigo">Total FONBES - Código</label>
                            <input type="text" id="totalFonbesCodigo" name="totalFonbesCodigo" value="1011" readonly>
                        </div>
                        <div class="form-group">
                            <label for="totalFonbesTipo">Total FONBES - Tipo</label>
                            <select id="totalFonbesTipo" name="totalFonbesTipo">
                                <option value="debe" selected>Debe (Egreso)</option>
                                <option value="haber">Haber (Ingreso)</option>
                            </select>
                        </div>
                    </div>

                    <h3>Códigos Contables COOPAM</h3>
                    <div class="form-row">
                        <div class="form-group">
                            <label for="aporteCoopamCodigo">Aporte COOPAM - Código</label>
                            <input type="text" id="aporteCoopamCodigo" name="aporteCoopamCodigo" value="<?= $escape($aporteCoopamCodigo) ?>" readonly>
                        </div>
                        <div class="form-group">
                            <label for="aporteCoopamTipo">Aporte COOPAM - Tipo</label>
                            <select id="aporteCoopamTipo" name="aporteCoopamTipo">
                                <option value="haber" selected>Haber (Ingreso)</option>
                                <option value="debe">Debe (Egreso)</option>
                            </select>
                        </div>
                    </div>
                    <div class="form-row">
                        <div class="form-group">
                            <label for="totalCoopamCodigo">Total COOPAM - Código</label>
                            <input type="text" id="totalCoopamCodigo" name="totalCoopamCodigo" value="1011" readonly>
                        </div>
                        <div class="form-group">
                            <label for="totalCoopamTipo">Total COOPAM - Tipo</label>
                            <select id="totalCoopamTipo" name="totalCoopamTipo">
                                <option value="debe" selected>Debe (Egreso)</option>
                                <option value="haber">Haber (Ingreso)</option>
                            </select>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="obs">Observación</label>
                        <textarea id="obs" name="obs" rows="3"></textarea>
                    </div>

                    <div class="form-group">
                        <label for="obs2">Observación 2</label>
                        <textarea id="obs2" name="obs2" rows="2"></textarea>
                    </div>

                    <div class="form-actions">
                        <button type="submit" class="btn btn-primary">Registrar Pago Combinado</button>
                        <a href="index.php?module=caja&profile=client&client=<?= rawurlencode($clienteCodigo) ?>" class="btn btn-secondary">Cancelar</a>
                    </div>
                </form>
            </section>
        </main>

        <script>
            function aplicarFiltro() {
                const filtro = document.querySelector('select[onchange="aplicarFiltro()"]').value;
                const url = new URL(window.location.href);
                url.searchParams.set('filtro', filtro);
                window.location.href = url.toString();
            }

            function actualizarCodigosTotales() {
                const formaPago = document.getElementById('formaPago').value;
                const codigoTotal = formaPago === 'EFECTIVO' ? '1011' : '10612';
                document.getElementById('totalFonbesCodigo').value = codigoTotal;
                document.getElementById('totalCoopamCodigo').value = codigoTotal;
            }
        </script>
    </body>
</html>
