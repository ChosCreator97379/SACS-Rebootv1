<?php
$escape = static fn (mixed $value): string => htmlspecialchars((string) ($value ?? ''), ENT_QUOTES, 'UTF-8');

$sociosServicio = new \Sacs\Services\SociosServicio();
$clienteCodigo = $_GET['client'] ?? '';

// Obtener información del socio
$socio = $sociosServicio->obtenerSocioPorCliente($clienteCodigo);
if (!$socio) {
    header('Location: index.php?module=socios&action=listado&error=Socio no encontrado');
    exit;
}

$codigoPersona = $socio['F01_ID_PERSONA'];

// Obtener sobregiro FONBES
$sobregiroFonbes = $sociosServicio->obtenerSobregiroFonbes($codigoPersona);
$totalSobregiroFonbes = $sociosServicio->obtenerTotalSobregiroFonbes($codigoPersona);

// Obtener sobregiro COOPAM
$sobregiroCoopam = $sociosServicio->obtenerSobregiroCoopam($codigoPersona);
$totalSobregiroCoopam = $sociosServicio->obtenerTotalSobregiroCoopam($codigoPersona);

$usuario = $_SESSION['usuario'] ?? 'SISTEMA';
$displayName = substr($usuario, 0, 2);
$profile = 'Administrador';
?>
<!doctype html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Sobregiro | SACS Nuevo</title>
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
            --green: #0f766e;
            --red: #991b1b;
            --line: #dce4e1;
            --shadow: 0 18px 45px rgba(20, 43, 58, .08);
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

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

        .intro { max-width: 640px; margin: 12px 0 28px; color: var(--muted); line-height: 1.55; }
        .panel { background: var(--panel); border-radius: 12px; padding: 24px; box-shadow: var(--shadow); margin-bottom: 24px; }
        .panel h2 { margin: 0 0 20px; font-size: 18px; font-weight: 400; }

        table { width: 100%; border-collapse: collapse; }
        th { text-align: left; padding: 12px 16px; background: #f8f9fa; color: var(--muted); font-size: 11px; font-weight: bold; letter-spacing: .04em; text-transform: uppercase; border-bottom: 1px solid var(--line); }
        td { padding: 12px 16px; border-bottom: 1px solid var(--line); font-size: 13px; }
        tr:last-child td { border-bottom: none; }
        tr:hover { background: #f8f9fa; }
        td.right { text-align: right; }
        th.right { text-align: right; }

        .empty { padding: 40px; text-align: center; color: var(--muted); }
        .total { font-weight: 600; font-size: 16px; padding: 16px; background: var(--paper); border-radius: 6px; margin-top: 16px; }
        .total span { color: var(--red); }
        .btn { padding: 8px 16px; border: none; border-radius: 4px; font-size: 13px; font-weight: 600; cursor: pointer; transition: background .15s; }
        .btn-secondary { background: var(--paper); color: var(--ink); }
        .btn-secondary:hover { background: #e8ebe9; }
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
                <a href="index.php?module=socios&action=listado" class="nav-link">Listado de Socios</a>
                <a href="index.php?module=socios&action=registro" class="nav-link">Registro de Socio</a>
                <a href="index.php?module=socios&action=info&client=<?= rawurlencode($clienteCodigo) ?>" class="nav-link">Información Personal</a>
                <a href="index.php?module=socios&action=observaciones&client=<?= rawurlencode($clienteCodigo) ?>" class="nav-link">Observaciones</a>
                <a href="index.php?module=socios&action=carta&client=<?= rawurlencode($clienteCodigo) ?>" class="nav-link">Carta</a>
                <a href="index.php?module=socios&action=sobregiro&client=<?= rawurlencode($clienteCodigo) ?>" class="nav-link active">Sobregiro</a>
                <a href="index.php?module=socios&action=aportes_completos&client=<?= rawurlencode($clienteCodigo) ?>" class="nav-link">Aportes Completos</a>
                <a href="index.php?module=socios&action=descuentos&client=<?= rawurlencode($clienteCodigo) ?>" class="nav-link">Descuentos</a>
                <a href="index.php?module=socios&action=aportes_fonbes&client=<?= rawurlencode($clienteCodigo) ?>" class="nav-link">Aportes FONBES</a>
                <a href="index.php?module=socios&action=aportes_coopam&client=<?= rawurlencode($clienteCodigo) ?>" class="nav-link">Aportes COOPAM</a>
                <a href="index.php?module=socios&action=gestion_estado&client=<?= rawurlencode($clienteCodigo) ?>" class="nav-link">Gestión de Estado</a>
                <a href="index.php?module=socios&action=beneficios&client=<?= rawurlencode($clienteCodigo) ?>" class="nav-link">Beneficios</a>
                <a href="index.php?module=socios&action=familiares&client=<?= rawurlencode($clienteCodigo) ?>" class="nav-link">Familiares</a>
                <a href="index.php?module=socios&action=rifas&client=<?= rawurlencode($clienteCodigo) ?>" class="nav-link">Rifas</a>
                <a href="index.php?module=socios&action=multas&client=<?= rawurlencode($clienteCodigo) ?>" class="nav-link">Multas</a>
                <a href="index.php?module=socios&action=cheques" class="nav-link">Cheques</a>
            </nav>
            <div class="nav-label">Auditoría</div>
            <nav class="nav">
                <a href="index.php?module=socios&action=auditoria&client=<?= rawurlencode($clienteCodigo) ?>" class="nav-link">Auditoría</a>
            </nav>
        </aside>

        <main class="main">
            <div class="header">
                <h1>Sobregiro</h1>
                <div class="user">
                    <div class="user-avatar"><?= $escape($displayName) ?></div>
                    <div>
                        <div class="user-name"><?= $escape($usuario) ?></div>
                        <div class="user-profile"><?= $escape($profile) ?></div>
                    </div>
                </div>
            </div>
            <p class="intro">Sobregiro del socio: <?= $escape($socio['F01_APE_PAT'] . ' ' . $socio['F01_APE_MAT'] . ', ' . $socio['F01_NOMB_PRI']) ?></p>

            <section class="panel">
                <h2>Sobregiro FONBES</h2>
                <?php if (empty($sobregiroFonbes)): ?>
                    <div class="empty">No hay sobregiro FONBES</div>
                <?php else: ?>
                    <table class="table">
                        <thead>
                            <tr>
                                <th>Periodo</th>
                                <th>Aportación Pendiente</th>
                                <th>Aportación Pagada</th>
                                <th class="right">Sobregiro</th>
                                <th>Fecha de Pago</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($sobregiroFonbes as $row): ?>
                                <tr>
                                    <td><?= $escape($row['periodo']) ?></td>
                                    <td><?= number_format($row['aportacion_pendiente'], 2) ?></td>
                                    <td><?= number_format($row['aportacion_pagada'], 2) ?></td>
                                    <td class="right"><?= number_format($row['sobregiro'], 2) ?></td>
                                    <td><?= $escape($row['fecha_pago'] ?? 'Sin fecha') ?></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                    <div class="total">
                        Total Sobregiro FONBES: <span><?= number_format($totalSobregiroFonbes, 2) ?></span>
                    </div>
                <?php endif; ?>
            </section>

            <section class="panel">
                <h2>Sobregiro COOPAM</h2>
                <?php if (empty($sobregiroCoopam)): ?>
                    <div class="empty">No hay sobregiro COOPAM</div>
                <?php else: ?>
                    <table class="table">
                        <thead>
                            <tr>
                                <th>Periodo</th>
                                <th>Aportación Pendiente</th>
                                <th>Aportación Pagada</th>
                                <th class="right">Sobregiro</th>
                                <th>Fecha de Pago</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($sobregiroCoopam as $row): ?>
                                <tr>
                                    <td><?= $escape($row['periodo']) ?></td>
                                    <td><?= number_format($row['aportacion_pendiente'], 2) ?></td>
                                    <td><?= number_format($row['aportacion_pagada'], 2) ?></td>
                                    <td class="right"><?= number_format($row['sobregiro'], 2) ?></td>
                                    <td><?= $escape($row['fecha_pago'] ?? 'Sin fecha') ?></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                    <div class="total">
                        Total Sobregiro COOPAM: <span><?= number_format($totalSobregiroCoopam, 2) ?></span>
                    </div>
                <?php endif; ?>
            </section>

            <div style="margin-top: 24px;">
                <a href="index.php?module=socios&action=info&client=<?= rawurlencode($clienteCodigo) ?>" class="btn btn-secondary">Volver a Información Personal</a>
            </div>
        </main>
    </div>
</body>
</html>
