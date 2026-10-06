<?php

declare(strict_types=1);

require dirname(__DIR__) . '/../config/bootstrap.php';

use Sacs\Core\Database;
use Sacs\Services\SociosServicio;
use Sacs\Security\Auth;

$auth = new Auth(new Sacs\Repositories\UserRepository());
if (!$auth->check()) {
    header('Location: index.php');
    exit;
}

$user = $auth->user();
$sociosServicio = new SociosServicio();

$clienteCodigo = trim((string) ($_GET['client'] ?? ''));
$filtroAportes = trim((string) ($_GET['filtro'] ?? 'todos'));

if ($clienteCodigo === '') {
    http_response_code(400);
    $error = 'Cliente no especificado';
    require dirname(__DIR__) . '/views/home.php';
    exit;
}

// Obtener datos del socio
$socio = $sociosServicio->obtenerSocioDetalle($clienteCodigo);
if ($socio === null) {
    http_response_code(404);
    $error = 'Socio no encontrado';
    require dirname(__DIR__) . '/views/home.php';
    exit;
}

// Obtener aportes COOPAM
$aportes = $sociosServicio->obtenerAportesCoopam($clienteCodigo, $filtroAportes);
$resumen = $sociosServicio->obtenerResumenAportesCoopam($clienteCodigo);

$displayName = htmlspecialchars($user['username'], ENT_QUOTES, 'UTF-8');
$profile = htmlspecialchars((string) $user['profile'], ENT_QUOTES, 'UTF-8');
$userId = htmlspecialchars((string) $user['id'], ENT_QUOTES, 'UTF-8');
$supervisor = $user['supervisor'] === 'S';

$escape = static fn (mixed $value): string => htmlspecialchars((string) ($value ?? ''), ENT_QUOTES, 'UTF-8');
$money = static fn (mixed $value): string => $value === null || $value === '' ? '-' : number_format((float) $value, 2, '.', ',');
?>
<!doctype html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Aportes COOPAM | SACS Nuevo</title>
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
        .form-group select { padding: 10px 12px; border: 1px solid var(--line); border-radius: 6px; font-size: 13px; font-family: inherit; }
        .form-group select:focus { outline: none; border-color: var(--teal); }
        .form-actions { display: flex; gap: 12px; margin-top: 24px; }
        .btn { padding: 12px 24px; border: none; border-radius: 6px; font-size: 13px; font-weight: 600; cursor: pointer; transition: background .15s; }
        .btn-primary { background: var(--teal); color: white; }
        .btn-primary:hover { background: #166664; }
        .btn-secondary { background: var(--paper); color: var(--ink); }
        .btn-secondary:hover { background: #e8ebe9; }
        .empty { color: var(--muted); font-style: italic; padding: 20px 0; text-align: center; }
        table { width: 100%; border-collapse: collapse; }
        th { text-align: left; font-size: 11px; font-weight: 600; color: var(--muted); text-transform: uppercase; letter-spacing: .08em; padding: 12px 16px; border-bottom: 1px solid var(--line); }
        td { padding: 12px 16px; border-bottom: 1px solid var(--line); font-size: 13px; }
        td.amount { font-family: monospace; }
        .badge { padding: 4px 8px; border-radius: 4px; font-size: 11px; font-weight: 600; }
        .badge-success { background: #e6f4ea; color: #1d7a78; }
        .badge-warning { background: #fff8e6; color: #d69e47; }
        .badge-danger { background: #fee; color: #c33; }
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
            <div class="nav-label">Socios</div>
            <nav class="nav">
                <a href="index.php" class="nav-link">Dashboard</a>
                <a href="index.php?module=socios&action=listado" class="nav-link">Listado de Socios</a>
                <a href="index.php?module=socios&action=registro" class="nav-link">Registro de Socio</a>
                <a href="index.php?module=socios&action=info&client=<?= rawurlencode($clienteCodigo) ?>" class="nav-link">Información Personal</a>
                <a href="index.php?module=socios&action=observaciones&client=<?= rawurlencode($clienteCodigo) ?>" class="nav-link">Observaciones</a>
                <a href="index.php?module=socios&action=carta&client=<?= rawurlencode($clienteCodigo) ?>" class="nav-link">Carta</a>
                <a href="index.php?module=socios&action=sobregiro&client=<?= rawurlencode($clienteCodigo) ?>" class="nav-link">Sobregiro</a>
                <a href="index.php?module=socios&action=aportes_completos&client=<?= rawurlencode($clienteCodigo) ?>" class="nav-link">Aportes Completos</a>
                <a href="index.php?module=socios&action=descuentos&client=<?= rawurlencode($clienteCodigo) ?>" class="nav-link">Descuentos</a>
                <a href="index.php?module=socios&action=aportes_fonbes&client=<?= rawurlencode($clienteCodigo) ?>" class="nav-link">Aportes FONBES</a>
                <a href="index.php?module=socios&action=aportes_coopam&client=<?= rawurlencode($clienteCodigo) ?>" class="nav-link active">Aportes COOPAM</a>
                <a href="index.php?module=socios&action=gestion_estado&client=<?= rawurlencode($clienteCodigo) ?>" class="nav-link">Gestión de Estado</a>
                <a href="index.php?module=socios&action=beneficios&client=<?= rawurlencode($clienteCodigo) ?>" class="nav-link">Beneficios</a>
                <a href="index.php?module=socios&action=familiares&client=<?= rawurlencode($clienteCodigo) ?>" class="nav-link">Familiares</a>
                <a href="index.php?module=socios&action=rifas&client=<?= rawurlencode($clienteCodigo) ?>" class="nav-link">Rifas</a>
                <a href="index.php?module=socios&action=multas&client=<?= rawurlencode($clienteCodigo) ?>" class="nav-link">Multas</a>
                <a href="index.php?module=socios&action=cheques" class="nav-link">Cheques</a>
                <div class="nav-label">Auditoría</div>
                <a href="index.php?module=socios&action=auditoria&client=<?= rawurlencode($clienteCodigo) ?>" class="nav-link">Auditoría Individual</a>
                <a href="index.php?module=socios&action=auditoria_general" class="nav-link">Auditoría General</a>
            </nav>
        </aside>

        <main class="main">
            <div class="header">
                <h1>Aportes COOPAM</h1>
                <div style="display: flex; gap: 12px;">
                    <a href="index.php?module=socios&action=exportar_aportes_coopam_csv&client=<?= rawurlencode($clienteCodigo) ?>" class="btn btn-secondary" style="padding: 8px 16px; font-size: 13px;">📄 CSV</a>
                    <a href="index.php?module=socios&action=exportar_aportes_coopam_excel&client=<?= rawurlencode($clienteCodigo) ?>" class="btn btn-secondary" style="padding: 8px 16px; font-size: 13px;">� Excel</a>
                    <a href="index.php?module=socios&action=exportar_aportes_coopam_pdf&client=<?= rawurlencode($clienteCodigo) ?>" class="btn btn-secondary" style="padding: 8px 16px; font-size: 13px;">📋 PDF</a>
                </div>
                <div class="user">
                    <div class="user-avatar"><?= $displayName[0] ?></div>
                    <div>
                        <div class="user-name"><?= $displayName ?></div>
                        <div class="user-profile"><?= $profile ?></div>
                    </div>
                </div>
            </div>

            <section class="summary">
                <div><span class="label">Socio</span><div class="name"><?= $escape($socio['F01_APE_PAT'] . ' ' . $socio['F01_APE_MAT'] . ', ' . $socio['F01_NOMB_PRI'] . ' ' . $socio['F01_NOMB_SEG']) ?></div></div>
                <div><span class="label">Código</span><div class="value"><?= $escape($clienteCodigo) ?></div></div>
                <div><span class="label">Documento</span><div class="value"><?= $escape($socio['F01_NUM_DOC']) ?></div></div>
                <div><span class="label">Aporte COOPAM</span><div class="value"><?= $money($socio['F01_APORTE_COPE']) ?></div></div>
            </section>

            <section class="panel">
                <h2>Resumen de Aportes</h2>
                <div class="summary">
                    <div><span class="label">Total Planificado</span><div class="value"><?= $money($resumen['total_planificado']) ?></div></div>
                    <div><span class="label">Total Pagado</span><div class="value"><?= $money($resumen['total_pagado']) ?></div></div>
                    <div><span class="label">Total Pendiente</span><div class="value" style="color: <?= ($resumen['total_pendiente'] > 0) ? '#c33' : '#1d7a78' ?>"><?= $money($resumen['total_pendiente']) ?></div></div>
                    <div><span class="label">Total Periodos</span><div class="value"><?= $escape($resumen['total_periodos']) ?></div></div>
                </div>
            </section>

            <section class="panel">
                <h2>Historial de Aportes</h2>
                <div class="form-row" style="margin-bottom: 16px;">
                    <div class="form-group">
                        <label for="filtro">Filtrar por:</label>
                        <select id="filtro" name="filtro" onchange="aplicarFiltro()">
                            <option value="todos" <?= $filtroAportes === 'todos' ? 'selected' : '' ?>>Todos</option>
                            <option value="pagados" <?= $filtroAportes === 'pagados' ? 'selected' : '' ?>>Pagados (completos)</option>
                            <option value="pendientes" <?= $filtroAportes === 'pendientes' ? 'selected' : '' ?>>Pendientes (incompletos)</option>
                        </select>
                    </div>
                </div>
                <?php if (empty($aportes)): ?>
                    <p class="empty">No hay aportes registrados con este filtro</p>
                <?php else: ?>
                    <table>
                        <thead>
                            <tr>
                                <th>Periodo</th>
                                <th>Planificado</th>
                                <th>Pagado</th>
                                <th>Diferencia</th>
                                <th>Fecha Pago</th>
                                <th>Estado</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($aportes as $aporte): ?>
                                <?php
                                $estado = 'pendiente';
                                $badgeClass = 'badge-warning';
                                if ($aporte['pagado'] > 0 && $aporte['pagado'] >= $aporte['planificado']) {
                                    $estado = 'pagado';
                                    $badgeClass = 'badge-success';
                                } elseif ($aporte['pagado'] > 0 && $aporte['pagado'] < $aporte['planificado']) {
                                    $estado = 'parcial';
                                    $badgeClass = 'badge-warning';
                                }
                                ?>
                                <tr>
                                    <td><?= $escape($aporte['periodo']) ?></td>
                                    <td class="amount"><?= $money($aporte['planificado']) ?></td>
                                    <td class="amount"><?= $money($aporte['pagado']) ?></td>
                                    <td class="amount"><?= $money($aporte['diferencia']) ?></td>
                                    <td><?= $escape($aporte['fecha_pago']) ?></td>
                                    <td><span class="badge <?= $badgeClass ?>"><?= strtoupper($escape($estado)) ?></span></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                <?php endif; ?>

                <div class="form-actions">
                    <a href="index.php?module=socios&action=info&client=<?= rawurlencode($clienteCodigo) ?>" class="btn btn-secondary">Volver a Información</a>
                    <a href="index.php?module=caja&action=aportes_coopam_form&client=<?= rawurlencode($clienteCodigo) ?>" class="btn btn-primary">Ir a Pago de Aportes</a>
                </div>
            </section>
        </main>
    </div>

    <script>
        function aplicarFiltro() {
            const filtro = document.getElementById('filtro').value;
            window.location.href = 'index.php?module=socios&action=aportes_coopam&client=<?= rawurlencode($clienteCodigo) ?>&filtro=' + filtro;
        }
    </script>
</body>
</html>
