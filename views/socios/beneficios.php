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
$fechaDesde = trim((string) ($_GET['fecha_desde'] ?? ''));
$fechaHasta = trim((string) ($_GET['fecha_hasta'] ?? ''));

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

// Obtener beneficios
$beneficios = $sociosServicio->obtenerBeneficios($clienteCodigo, $fechaDesde ?: null, $fechaHasta ?: null);

$displayName = htmlspecialchars($user['username'], ENT_QUOTES, 'UTF-8');
$profile = htmlspecialchars((string) $user['profile'], ENT_QUOTES, 'UTF-8');
$userId = htmlspecialchars((string) $user['id'], ENT_QUOTES, 'UTF-8');

$escape = static fn (mixed $value): string => htmlspecialchars((string) ($value ?? ''), ENT_QUOTES, 'UTF-8');
$money = static fn (mixed $value): string => $value === null || $value === '' ? '-' : number_format((float) $value, 2, '.', ',');
?>
<!doctype html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Beneficios | SACS Nuevo</title>
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
            --red: #dc2626;
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
        .form-group input { padding: 10px 12px; border: 1px solid var(--line); border-radius: 6px; font-size: 13px; font-family: inherit; }
        .form-group input:focus { outline: none; border-color: var(--teal); }
        .form-actions { display: flex; gap: 12px; margin-top: 24px; }
        .btn { padding: 12px 24px; border: none; border-radius: 6px; font-size: 13px; font-weight: 600; cursor: pointer; transition: background .15s; text-decoration: none; display: inline-block; }
        .btn-primary { background: var(--teal); color: white; }
        .btn-primary:hover { background: #166664; }
        .btn-danger { background: var(--red); color: white; }
        .btn-danger:hover { background: #b91c1c; }
        .btn-secondary { background: var(--paper); color: var(--ink); }
        .btn-secondary:hover { background: #e8ebe9; }
        .btn-blue { background: #3b82f6; color: white; }
        .btn-blue:hover { background: #2563eb; }
        .btn-green { background: #10b981; color: white; }
        .btn-green:hover { background: #059669; }
        .btn-purple { background: #8b5cf6; color: white; }
        .btn-purple:hover { background: #7c3aed; }
        .btn-orange { background: #f59e0b; color: white; }
        .btn-orange:hover { background: #d97706; }
        .btn-gray { background: #6b7280; color: white; }
        .btn-gray:hover { background: #4b5563; }
        .empty { color: var(--muted); font-style: italic; padding: 20px 0; text-align: center; }
        table { width: 100%; border-collapse: collapse; }
        th { text-align: left; font-size: 11px; font-weight: 600; color: var(--muted); text-transform: uppercase; letter-spacing: .08em; padding: 12px 16px; border-bottom: 1px solid var(--line); }
        td { padding: 12px 16px; border-bottom: 1px solid var(--line); font-size: 13px; }
        td.amount { font-family: monospace; }
        .badge { padding: 4px 8px; border-radius: 4px; font-size: 11px; font-weight: 600; }
        .badge-success { background: #e6f4ea; color: #1d7a78; }
        .badge-danger { background: #fee; color: #c33; }
        .action-btn { padding: 6px 12px; font-size: 11px; border-radius: 4px; border: none; cursor: pointer; margin-right: 4px; }
        .action-btn-edit { background: #3b82f6; color: white; }
        .action-btn-delete { background: #dc2626; color: white; }
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
                <a href="index.php?module=socios&action=aportes_coopam&client=<?= rawurlencode($clienteCodigo) ?>" class="nav-link">Aportes COOPAM</a>
                <a href="index.php?module=socios&action=gestion_estado&client=<?= rawurlencode($clienteCodigo) ?>" class="nav-link">Gestión de Estado</a>
                <a href="index.php?module=socios&action=beneficios&client=<?= rawurlencode($clienteCodigo) ?>" class="nav-link active">Beneficios</a>
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
                <h1>Beneficios</h1>
                <div style="display: flex; gap: 12px;">
                    <a href="index.php?module=socios&action=exportar_beneficios_csv&client=<?= rawurlencode($clienteCodigo) ?>&fecha_desde=<?= $escape($fechaDesde) ?>&fecha_hasta=<?= $escape($fechaHasta) ?>" class="btn btn-secondary" style="padding: 8px 16px; font-size: 13px;">📄 CSV</a>
                    <a href="index.php?module=socios&action=exportar_beneficios_excel&client=<?= rawurlencode($clienteCodigo) ?>&fecha_desde=<?= $escape($fechaDesde) ?>&fecha_hasta=<?= $escape($fechaHasta) ?>" class="btn btn-secondary" style="padding: 8px 16px; font-size: 13px;">� Excel</a>
                    <a href="index.php?module=socios&action=exportar_beneficios_pdf&client=<?= rawurlencode($clienteCodigo) ?>&fecha_desde=<?= $escape($fechaDesde) ?>&fecha_hasta=<?= $escape($fechaHasta) ?>" class="btn btn-secondary" style="padding: 8px 16px; font-size: 13px;">📋 PDF</a>
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
                <div><span class="label">Total Beneficios</span><div class="value"><?= count($beneficios) ?></div></div>
            </section>

            <section class="panel">
                <h2>Filtros</h2>
                <form method="get" class="form-row">
                    <input type="hidden" name="module" value="socios">
                    <input type="hidden" name="action" value="beneficios">
                    <input type="hidden" name="client" value="<?= $escape($clienteCodigo) ?>">
                    <div class="form-group">
                        <label for="fecha_desde">Fecha Desde</label>
                        <input type="date" id="fecha_desde" name="fecha_desde" value="<?= $escape($fechaDesde) ?>">
                    </div>
                    <div class="form-group">
                        <label for="fecha_hasta">Fecha Hasta</label>
                        <input type="date" id="fecha_hasta" name="fecha_hasta" value="<?= $escape($fechaHasta) ?>">
                    </div>
                    <div class="form-group" style="display: flex; align-items: flex-end;">
                        <button type="submit" class="btn btn-primary">Filtrar</button>
                    </div>
                </form>
            </section>

            <section class="panel">
                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
                    <h2 style="margin: 0;">Listado de Beneficios</h2>
                    <a href="index.php?module=socios&action=beneficio_nuevo&client=<?= rawurlencode($clienteCodigo) ?>" class="btn btn-green">💎 Nuevo Beneficio</a>
                </div>
                <?php if (empty($beneficios)): ?>
                    <p class="empty">No hay beneficios registrados para este socio</p>
                <?php else: ?>
                    <table>
                        <thead>
                            <tr>
                                <th>Código</th>
                                <th>Beneficiario</th>
                                <th>Tipo</th>
                                <th>Parentesco</th>
                                <th>Importe</th>
                                <th>Fecha</th>
                                <th>Estado</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($beneficios as $beneficio): ?>
                                <?php
                                $badgeClass = $beneficio['estado'] === 'REGISTRADO' ? 'badge-success' : 'badge-danger';
                                ?>
                                <tr>
                                    <td><?= $escape($beneficio['codigo']) ?></td>
                                    <td><?= $escape($beneficio['beneficiario']) ?></td>
                                    <td><?= $escape($beneficio['tipo']) ?></td>
                                    <td><?= $escape($beneficio['tipo_pa1']) ?></td>
                                    <td class="amount"><?= $money($beneficio['importe']) ?></td>
                                    <td><?= $escape($beneficio['fecha']) ?></td>
                                    <td><span class="badge <?= $badgeClass ?>"><?= $escape($beneficio['estado']) ?></span></td>
                                    <td>
                                        <?php if ($beneficio['estado'] === 'REGISTRADO'): ?>
                                            <a href="index.php?module=socios&action=beneficio_editar&codigo=<?= $escape($beneficio['codigo']) ?>&client=<?= rawurlencode($clienteCodigo) ?>" class="action-btn action-btn-edit">✏️ Editar</a>
                                            <button onclick="eliminarBeneficio(<?= $escape($beneficio['codigo']) ?>, '<?= rawurlencode($clienteCodigo) ?>')" class="action-btn action-btn-delete">🗑️ Eliminar</button>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                <?php endif; ?>

                <div class="form-actions">
                    <a href="index.php?module=socios&action=info&client=<?= rawurlencode($clienteCodigo) ?>" class="btn btn-gray">🔙 Volver a Información</a>
                </div>
            </section>
        </main>
    </div>

    <script>
        function eliminarBeneficio(codigo, cliente) {
            if (confirm('¿Está seguro de eliminar este beneficio? Esta acción no se puede deshacer.')) {
                window.location.href = 'index.php?module=socios&action=beneficio_eliminar&codigo=' + codigo + '&client=' + cliente + '&csrf_token=<?= $escape($_SESSION['csrf_token']) ?>';
            }
        }
    </script>
</body>
</html>
