<?php
$escape = static fn (mixed $value): string => htmlspecialchars((string) ($value ?? ''), ENT_QUOTES, 'UTF-8');
?>
<!doctype html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Ver Trámite | SACS Nuevo</title>
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
        .intro { max-width: 640px; margin: 12px 0 28px; color: var(--muted); line-height: 1.55; }
        .info-card { background: #fff; border: 1px solid var(--line); border-radius: 7px; padding: 24px; margin-bottom: 24px; }
        .info-card h3 { margin: 0 0 20px; font-size: 16px; font-weight: 600; color: var(--navy); }
        .info-row { display: grid; grid-template-columns: 200px 1fr; gap: 12px; margin-bottom: 12px; }
        .info-label { font-size: 12px; font-weight: 600; color: var(--muted); letter-spacing: .04em; text-transform: uppercase; }
        .info-value { font-size: 13px; color: var(--ink); }
        .badge { display: inline-block; padding: 4px 10px; border-radius: 3px; font-size: 11px; font-weight: 600; }
        .badge-registrado { background: #d4edda; color: #155724; }
        .badge-eliminado { background: #f8d7da; color: #721c24; }
        .badge-incompleto { background: #fff3cd; color: #856404; }
        .badge-actualizado { background: #cce5ff; color: #004085; }
        .table-container { border: 1px solid var(--line); border-radius: 7px; background: var(--panel); overflow: hidden; margin-top: 16px; }
        table { width: 100%; border-collapse: collapse; }
        th { text-align: left; padding: 12px 16px; background: #f8f9fa; color: var(--muted); font-size: 11px; font-weight: bold; letter-spacing: .04em; text-transform: uppercase; border-bottom: 1px solid var(--line); }
        td { padding: 12px 16px; border-bottom: 1px solid var(--line); font-size: 13px; }
        tr:last-child td { border-bottom: none; }
        .actions { display: flex; gap: 8px; }
        .btn-sm { padding: 4px 10px; font-size: 11px; border: none; border-radius: 3px; cursor: pointer; }
        .btn-primary { background: var(--teal); color: #fff; }
        .btn-primary:hover { background: #166664; }
        .btn-secondary { background: #e7f0ed; color: var(--navy); }
        .btn-secondary:hover { background: #dce4e1; }
        .btn-danger { background: #dc3545; color: #fff; }
        .btn-danger:hover { background: #c82333; }
        .file-link { color: var(--teal); text-decoration: underline; }
        .file-link:hover { color: #166664; }
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
            <div class="nav-label">Trámites</div>
            <nav class="nav">
                <a href="index.php" class="nav-link">Dashboard</a>
                <a href="index.php?module=tramites&action=listar" class="nav-link">Listado de Trámites</a>
                <a href="index.php?module=tramites&action=registrar" class="nav-link">Registrar Trámite</a>
            </nav>
        </aside>
        <div class="main">
            <div class="header">
                <h1>TRAMITE DOCUMENTARIO</h1>
                <div class="actions">
                    <a href="index.php?module=tramites&action=listar" class="btn-sm btn-secondary" style="padding: 8px 20px; font-size: 13px;">Volver al Listado</a>
                    <?php if (($tramite['estado'] ?? '') !== 'ELIMINADO'): ?>
                        <a href="index.php?module=tramites&action=editar&codigo=<?= $escape($tramite['codigo']) ?>" class="btn-sm btn-primary" style="padding: 8px 20px; font-size: 13px;">Editar</a>
                    <?php endif; ?>
                </div>
            </div>

            <!-- DATOS DEL DOCUMENTO -->
            <div class="info-card">
                <h3>DATOS DEL DOCUMENTO</h3>
                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 24px;">
                    <div>
                        <div class="info-row">
                            <div class="info-label">Número Doc.</div>
                            <div class="info-value" style="font-size: 18px; font-weight: bold; color: var(--teal);"><?= $escape($tramite['numero']) ?></div>
                        </div>
                        <div class="info-row">
                            <div class="info-label">Tipo</div>
                            <div class="info-value"><?= $escape($tramite['tipo']) ?></div>
                        </div>
                        <div class="info-row">
                            <div class="info-label">Estado</div>
                            <div class="info-value">
                                <span class="badge badge-<?= strtolower($tramite['estado']) ?>">
                                    <?= $escape($tramite['estado']) ?>
                                </span>
                            </div>
                        </div>
                        <div class="info-row">
                            <div class="info-label">Fecha Registro</div>
                            <div class="info-value"><?= $escape($tramite['fecha']) ?></div>
                        </div>
                        <div class="info-row">
                            <div class="info-label">Origen</div>
                            <div class="info-value"><?= $escape($tramite['origen'] ?? '-') ?></div>
                        </div>
                        <div class="info-row">
                            <div class="info-label">Destino</div>
                            <div class="info-value"><?= $escape($tramite['destino'] ?? '-') ?></div>
                        </div>
                        <div class="info-row">
                            <div class="info-label">Referencia</div>
                            <div class="info-value"><?= $escape($tramite['referencia'] ?? '-') ?></div>
                        </div>
                        <div class="info-row">
                            <div class="info-label">Nro. Cuenta</div>
                            <div class="info-value"><?= $escape($tramite['cuenta'] ?? '-') ?></div>
                        </div>
                    </div>
                    <div>
                        <div class="info-row">
                            <div class="info-label">Descripción</div>
                            <div class="info-value"><?= $escape($tramite['descripcion']) ?></div>
                        </div>
                        <div class="info-row">
                            <div class="info-label">Socio</div>
                            <div class="info-value"><?= $escape($tramite['socio'] ?? '-') ?></div>
                        </div>
                        <div class="info-row">
                            <div class="info-label">Nombres</div>
                            <div class="info-value"><?= $escape($tramite['nombres'] ?? '-') ?></div>
                        </div>
                        <div class="info-row">
                            <div class="info-label">Tramitante</div>
                            <div class="info-value"><?= $escape($tramite['tramitante'] ?? '-') ?></div>
                        </div>
                        <div class="info-row">
                            <div class="info-label">Tramitante DNI</div>
                            <div class="info-value"><?= $escape($tramite['socio_dni'] ?? '-') ?></div>
                        </div>
                        <div class="info-row">
                            <div class="info-label">Tramitante Cel</div>
                            <div class="info-value"><?= $escape($tramite['tramitante_cel'] ?? '-') ?></div>
                        </div>
                        <div class="info-row">
                            <div class="info-label">Tramitante Email</div>
                            <div class="info-value"><?= $escape($tramite['tramitante_email'] ?? '-') ?></div>
                        </div>
                        <div class="info-row">
                            <div class="info-label">Observación</div>
                            <div class="info-value"><?= $escape($tramite['obs'] ?? '-') ?></div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- HISTORICO DEL DOCUMENTO -->
            <div class="info-card">
                <h3>HISTORICO DEL DOCUMENTO</h3>
                <?php if (empty($historico)): ?>
                    <p style="color: var(--muted); font-size: 13px;">No hay histórico disponible</p>
                <?php else: ?>
                    <p style="color: var(--muted); font-size: 12px; margin-bottom: 12px;"><?= count($historico) ?> registros encontrados</p>
                    <div class="table-container">
                        <table>
                            <thead>
                                <tr>
                                    <th>Fecha</th>
                                    <th>Hora</th>
                                    <th>Origen</th>
                                    <th>Destino</th>
                                    <th>Estado</th>
                                    <th>Observación</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($historico as $hist): ?>
                                    <tr>
                                        <td><?= $escape($hist['f30_fecha']) ?></td>
                                        <td><?= $escape($hist['f30_hora'] ?? '-') ?></td>
                                        <td><?= $escape($hist['f30_origen'] ?? '-') ?></td>
                                        <td><?= $escape($hist['f30_destino'] ?? '-') ?></td>
                                        <td>
                                            <span class="badge badge-<?= strtolower($hist['f30_estado']) ?>">
                                                <?= $escape($hist['f30_estado']) ?>
                                            </span>
                                        </td>
                                        <td><?= $escape($hist['f30_obs'] ?? '-') ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</body>
</html>
