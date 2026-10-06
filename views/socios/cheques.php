<?php

declare(strict_types=1);

require dirname(__DIR__) . '/../config/bootstrap.php';

use Sacs\Services\SociosServicio;
use Sacs\Security\Auth;

$auth = new Auth(new Sacs\Repositories\UserRepository());
if (!$auth->check()) {
    header('Location: index.php');
    exit;
}

$user = $auth->user();
$sociosServicio = new SociosServicio();

$fechaDesde = trim((string) ($_GET['fecha_desde'] ?? ''));
$fechaHasta = trim((string) ($_GET['fecha_hasta'] ?? ''));
$pagina = max(1, (int) ($_GET['pagina'] ?? 1));
$porPagina = 10;

// Obtener cheques con paginación
$cheques = $sociosServicio->obtenerCheques($fechaDesde, $fechaHasta, $pagina, $porPagina);
$totalCheques = $sociosServicio->contarCheques($fechaDesde, $fechaHasta);
$totalPaginas = (int) ceil($totalCheques / $porPagina);

$displayName = htmlspecialchars($user['username'], ENT_QUOTES, 'UTF-8');
$profile = htmlspecialchars((string) $user['profile'], ENT_QUOTES, 'UTF-8');
$userId = htmlspecialchars((string) $user['id'], ENT_QUOTES, 'UTF-8');

$escape = static fn (mixed $value): string => htmlspecialchars((string) ($value ?? ''), ENT_QUOTES, 'UTF-8');
?>
<!doctype html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Cheques | SACS Nuevo</title>
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
        .form-row { display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 16px; margin-bottom: 16px; }
        .form-group { display: flex; flex-direction: column; }
        .form-group label { font-size: 12px; font-weight: 600; color: var(--muted); margin-bottom: 6px; }
        .form-group input, .form-group select { padding: 10px 12px; border: 1px solid var(--line); border-radius: 6px; font-size: 13px; font-family: inherit; }
        .form-group input:focus, .form-group select:focus { outline: none; border-color: var(--teal); }
        .form-actions { display: flex; gap: 12px; margin-top: 24px; }
        .btn { padding: 12px 24px; border: none; border-radius: 6px; font-size: 13px; font-weight: 600; cursor: pointer; transition: background .15s; text-decoration: none; display: inline-block; }
        .btn-primary { background: var(--teal); color: white; }
        .btn-primary:hover { background: #166664; }
        .btn-danger { background: #dc2626; color: white; }
        .btn-danger:hover { background: #b91c1c; }
        .btn-secondary { background: var(--paper); color: var(--ink); }
        .btn-secondary:hover { background: #e8ebe9; }
        .btn-blue { background: #3b82f6; color: white; }
        .btn-blue:hover { background: #2563eb; }
        .btn-green { background: #10b981; color: white; }
        .btn-green:hover { background: #059669; }
        .btn-gray { background: #6b7280; color: white; }
        .btn-gray:hover { background: #4b5563; }
        .empty { color: var(--muted); font-style: italic; padding: 20px 0; text-align: center; }
        table { width: 100%; border-collapse: collapse; }
        th { text-align: left; font-size: 11px; font-weight: 600; color: var(--muted); text-transform: uppercase; letter-spacing: .08em; padding: 10px 14px; border-bottom: 1px solid var(--line); background: var(--paper); }
        td { padding: 10px 14px; border-bottom: 1px solid var(--line); font-size: 13px; line-height: 1.3; }
        tr:hover { background: var(--paper); }
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
                <a href="index.php?module=socios&action=cheques" class="nav-link active">Cheques</a>
                <div class="nav-label">Auditoría</div>
                <a href="index.php?module=socios&action=auditoria_general" class="nav-link">Auditoría General</a>
            </nav>
        </aside>

        <main class="main">
            <div class="header">
                <h1>Cheques</h1>
                <div class="user">
                    <div class="user-avatar"><?= $displayName[0] ?></div>
                    <div>
                        <div class="user-name"><?= $displayName ?></div>
                        <div class="user-profile"><?= $profile ?></div>
                    </div>
                </div>
            </div>

            <section class="summary">
                <div><span class="label">Total Cheques</span><div class="value"><?= count($cheques) ?></div></div>
            </section>

            <section class="panel">
                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
                    <h2 style="margin: 0;">Listado de Cheques</h2>
                    <div style="display: flex; gap: 12px;">
                        <a href="index.php?module=socios&action=cheque_nuevo" class="btn btn-green">📄 Nuevo Cheque</a>
                        <a href="index.php?module=socios&action=exportar_cheques_csv&fecha_desde=<?= $escape($fechaDesde) ?>&fecha_hasta=<?= $escape($fechaHasta) ?>" class="btn btn-secondary">📄 CSV</a>
                        <a href="index.php?module=socios&action=exportar_cheques_excel&fecha_desde=<?= $escape($fechaDesde) ?>&fecha_hasta=<?= $escape($fechaHasta) ?>" class="btn btn-secondary">📊 Excel</a>
                        <a href="index.php?module=socios&action=exportar_cheques_pdf&fecha_desde=<?= $escape($fechaDesde) ?>&fecha_hasta=<?= $escape($fechaHasta) ?>" class="btn btn-secondary">📋 PDF</a>
                    </div>
                </div>
                
                <form method="GET" style="margin-bottom: 20px;">
                    <input type="hidden" name="module" value="socios">
                    <input type="hidden" name="action" value="cheques">
                    <div class="form-row">
                        <div class="form-group">
                            <label for="fecha_desde">Fecha Desde</label>
                            <input type="date" id="fecha_desde" name="fecha_desde" value="<?= $escape($fechaDesde) ?>">
                        </div>
                        <div class="form-group">
                            <label for="fecha_hasta">Fecha Hasta</label>
                            <input type="date" id="fecha_hasta" name="fecha_hasta" value="<?= $escape($fechaHasta) ?>">
                        </div>
                        <div class="form-group" style="display: flex; align-items: flex-end;">
                            <button type="submit" class="btn btn-primary">🔍 Filtrar</button>
                        </div>
                    </div>
                </form>
                
                <?php if (empty($cheques)): ?>
                    <p class="empty">No hay cheques registrados</p>
                <?php else: ?>
                    <table>
                        <thead>
                            <tr>
                                <th>Código</th>
                                <th>Nombre</th>
                                <th>Monto</th>
                                <th>Monto en Letras</th>
                                <th>Fecha</th>
                                <th>Número Cheque</th>
                                <th>Motivo</th>
                                <th>Centro</th>
                                <th>Condición</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($cheques as $cheque): ?>
                                <tr>
                                    <td><?= $escape($cheque['f23_codigo']) ?></td>
                                    <td><?= $escape($cheque['f23_nombre']) ?></td>
                                    <td><?= $escape($cheque['f23_monto']) ?></td>
                                    <td><?= $escape($cheque['f23_montoLetras']) ?></td>
                                    <td><?= $escape($cheque['f23_fecha']) ?></td>
                                    <td><?= $escape($cheque['f23_cheque']) ?></td>
                                    <td><?= $escape($cheque['f23_motivo']) ?></td>
                                    <td><?= $escape($cheque['f23_centro']) ?></td>
                                    <td><?= $escape($cheque['f23_condicion']) ?></td>
                                    <td>
                                        <a href="index.php?module=socios&action=cheque_editar&codigo=<?= $escape($cheque['f23_codigo']) ?>" class="action-btn action-btn-edit">✏️ Editar</a>
                                        <button onclick="eliminarCheque('<?= $escape($cheque['f23_codigo']) ?>')" class="action-btn action-btn-delete">🗑️ Eliminar</button>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                <?php endif; ?>

                <?php if ($totalPaginas > 1): ?>
                    <div style="display: flex; justify-content: center; align-items: center; gap: 12px; margin-top: 20px; padding: 16px; background: var(--paper); border-radius: 8px;">
                        <span style="font-size: 13px; color: var(--muted);">
                            Página <?= $pagina ?> de <?= $totalPaginas ?> (<?= $totalCheques ?> registros)
                        </span>
                        <?php if ($pagina > 1): ?>
                            <a href="index.php?module=socios&action=cheques&fecha_desde=<?= $escape($fechaDesde) ?>&fecha_hasta=<?= $escape($fechaHasta) ?>&pagina=<?= $pagina - 1 ?>" class="btn btn-secondary" style="padding: 8px 16px; font-size: 12px;">⬅️ Anterior</a>
                        <?php endif; ?>
                        <?php if ($pagina < $totalPaginas): ?>
                            <a href="index.php?module=socios&action=cheques&fecha_desde=<?= $escape($fechaDesde) ?>&fecha_hasta=<?= $escape($fechaHasta) ?>&pagina=<?= $pagina + 1 ?>" class="btn btn-secondary" style="padding: 8px 16px; font-size: 12px;">Siguiente ➡️</a>
                        <?php endif; ?>
                    </div>
                <?php endif; ?>

                <div class="form-actions">
                    <a href="index.php" class="btn btn-gray">🔙 Volver al Dashboard</a>
                </div>
            </section>
        </main>
    </div>

    <script>
        function eliminarCheque(codigo) {
            if (confirm('¿Está seguro de eliminar este cheque? Esta acción no se puede deshacer.')) {
                window.location.href = 'index.php?module=socios&action=cheque_eliminar&codigo=' + codigo + '&csrf_token=<?= $escape($_SESSION['csrf_token']) ?>';
            }
        }
    </script>
</body>
</html>
