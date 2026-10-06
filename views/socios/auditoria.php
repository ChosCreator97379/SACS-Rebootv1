<?php

declare(strict_types=1);

$escape = static fn (mixed $value): string => htmlspecialchars((string) ($value ?? ''), ENT_QUOTES, 'UTF-8');
?>
<!doctype html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Historial de Cambios | SACS Nuevo</title>
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
        .header-info { display: flex; align-items: center; gap: 12px; margin-bottom: 8px; }
        .client-badge { background: var(--teal); color: white; padding: 4px 12px; border-radius: 4px; font-size: 12px; font-weight: 600; }
        .panel { background: var(--panel); border-radius: 12px; padding: 24px; box-shadow: var(--shadow); margin-bottom: 24px; }
        .panel h2 { margin: 0 0 20px; font-size: 18px; font-weight: 400; color: var(--teal); }
        .table-container { border: 1px solid var(--line); border-radius: 7px; background: var(--panel); overflow: hidden; }
        table { width: 100%; border-collapse: collapse; }
        th { text-align: left; padding: 12px 16px; background: #f8f9fa; color: var(--muted); font-size: 11px; font-weight: bold; letter-spacing: .04em; text-transform: uppercase; border-bottom: 1px solid var(--line); }
        td { padding: 12px 16px; border-bottom: 1px solid var(--line); font-size: 13px; }
        tr:last-child td { border-bottom: none; }
        tr:hover { background: #f8f9fa; }
        .badge { display: inline-block; padding: 4px 8px; border-radius: 4px; font-size: 11px; font-weight: 600; }
        .badge-creacion { background: #d4edda; color: #155724; }
        .badge-modificacion { background: #fff3cd; color: #856404; }
        .btn { padding: 8px 16px; border: none; border-radius: 4px; font-size: 13px; font-weight: 600; cursor: pointer; transition: background .15s; }
        .btn-primary { background: var(--teal); color: white; }
        .btn-primary:hover { background: #166664; }
        .btn-secondary { background: var(--paper); color: var(--ink); }
        .btn-secondary:hover { background: #e8ebe9; }
        .empty { padding: 40px; text-align: center; color: var(--muted); }
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
                    <div class="brand-note">Sistema de Administración</div>
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
                <a href="index.php?module=socios&action=aportes_fonbes&client=<?= rawurlencode($clienteCodigo) ?>" class="nav-link">Aportes FONBES</a>
                <a href="index.php?module=socios&action=aportes_coopam&client=<?= rawurlencode($clienteCodigo) ?>" class="nav-link">Aportes COOPAM</a>
                <a href="index.php?module=socios&action=gestion_estado&client=<?= rawurlencode($clienteCodigo) ?>" class="nav-link">Gestión de Estado</a>
                <a href="index.php?module=socios&action=beneficios&client=<?= rawurlencode($clienteCodigo) ?>" class="nav-link">Beneficios</a>
                <a href="index.php?module=socios&action=familiares&client=<?= rawurlencode($clienteCodigo) ?>" class="nav-link">Familiares</a>
                <a href="index.php?module=socios&action=rifas&client=<?= rawurlencode($clienteCodigo) ?>" class="nav-link">Rifas</a>
                <a href="index.php?module=socios&action=multas&client=<?= rawurlencode($clienteCodigo) ?>" class="nav-link">Multas</a>
                <a href="index.php?module=socios&action=cheques" class="nav-link">Cheques</a>
                <div class="nav-label">Auditoría</div>
                <a href="index.php?module=socios&action=auditoria&client=<?= rawurlencode($clienteCodigo) ?>" class="nav-link active">Auditoría Individual</a>
                <a href="index.php?module=socios&action=auditoria_general" class="nav-link">Auditoría General</a>
            </nav>
        </aside>

        <main class="main">
            <div class="header">
                <div>
                    <h1>Historial de Cambios</h1>
                    <div class="header-info">
                        <span class="client-badge"><?= $escape($socio['F02_ID_CLIENTE']) ?></span>
                        <span style="color: var(--muted);">
                            <?= $escape($socio['F01_APE_PAT']) ?> 
                            <?= $escape($socio['F01_APE_MAT']) ?>, 
                            <?= $escape($socio['F01_NOMB_PRI']) ?>
                        </span>
                    </div>
                </div>
                <a href="index.php?module=socios&action=info&client=<?= $escape($socio['F02_ID_CLIENTE']) ?>" class="btn btn-secondary">Volver a Info</a>
            </div>

            <section class="panel">
                <h2>Registro de Modificaciones</h2>
                <div class="table-container">
                    <?php if (empty($historial)): ?>
                        <div class="empty">
                            No hay historial de cambios registrado para este socio.
                        </div>
                    <?php else: ?>
                        <table>
                            <thead>
                                <tr>
                                    <th>Tipo de Acción</th>
                                    <th>Usuario</th>
                                    <th>Fecha y Hora</th>
                                    <th>Descripción</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($historial as $cambio): ?>
                                    <tr>
                                        <td>
                                            <span class="badge <?= $cambio['tipo_accion'] === 'CREACION' ? 'badge-creacion' : 'badge-modificacion' ?>">
                                                <?= $escape($cambio['tipo_accion']) ?>
                                            </span>
                                        </td>
                                        <td><?= $escape($cambio['usuario']) ?></td>
                                        <td><?= $escape($cambio['fecha']) ?></td>
                                        <td><?= $escape($cambio['descripcion']) ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    <?php endif; ?>
                </div>
            </section>

            <div style="display: flex; gap: 12px; margin-top: 24px;">
                <a href="index.php?module=caja&profile=client&client=<?= $escape($socio['F02_ID_CLIENTE']) ?>" class="btn btn-primary">Ver Operaciones</a>
                <a href="index.php?module=socios&action=listado" class="btn btn-secondary">Volver al Listado</a>
            </div>
        </main>
    </div>
</body>
</html>
