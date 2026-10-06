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

// Obtener aportaciones completas
$pagina = max(1, (int) ($_GET['pagina'] ?? 1));
$porPagina = 20;
$aportaciones = $sociosServicio->obtenerAportacionesCompletas($codigoPersona, $pagina, $porPagina);
$totalAportaciones = $sociosServicio->contarAportacionesCompletas($codigoPersona);
$totalPaginas = max(1, ceil($totalAportaciones / $porPagina));

// Obtener totales
$totalAportadoFonbes = $sociosServicio->obtenerTotalAportadoFonbes($codigoPersona);
$totalAportadoCoopam = $sociosServicio->obtenerTotalAportadoCoopam($codigoPersona);
$fechaUltimoAporte = $sociosServicio->obtenerFechaUltimoAporteFonbes($codigoPersona);

$usuario = $_SESSION['usuario'] ?? 'SISTEMA';
$displayName = substr($usuario, 0, 2);
$profile = 'Administrador';
?>
<!doctype html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Aportaciones Completas | SACS Nuevo</title>
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
            --border: #e2e8f0;
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

        .intro {
            color: var(--muted);
            margin-bottom: 30px;
        }

        .panel {
            background: var(--panel);
            border-radius: 8px;
            border: 1px solid var(--border);
            padding: 24px;
            margin-bottom: 20px;
        }

        .panel h2 {
            font-size: 18px;
            font-weight: 600;
            margin-bottom: 20px;
        }

        .table {
            width: 100%;
            border-collapse: collapse;
        }

        .table th,
        .table td {
            padding: 12px;
            text-align: left;
            border-bottom: 1px solid var(--border);
        }

        .table th {
            font-weight: 600;
            font-size: 13px;
            color: var(--muted);
            background: var(--paper);
        }

        .table tr:hover {
            background: var(--paper);
        }

        .table td.right {
            text-align: right;
        }

        .table th.right {
            text-align: right;
        }

        .empty {
            text-align: center;
            padding: 40px;
            color: var(--muted);
        }

        .total {
            font-weight: 600;
            font-size: 16px;
            padding: 16px;
            background: var(--paper);
            border-radius: 6px;
            margin-top: 16px;
        }

        .total span {
            color: var(--green);
        }

        .pagination {
            display: flex;
            justify-content: center;
            align-items: center;
            gap: 12px;
            margin-top: 20px;
        }

        .pagination a {
            padding: 8px 16px;
            background: var(--panel);
            border: 1px solid var(--border);
            border-radius: 6px;
            text-decoration: none;
            color: var(--ink);
        }

        .pagination a:hover {
            background: var(--paper);
        }

        .pagination span {
            color: var(--muted);
        }

        .btn {
            padding: 10px 20px;
            border: none;
            border-radius: 6px;
            font-size: 14px;
            font-weight: 500;
            cursor: pointer;
            text-decoration: none;
            display: inline-block;
        }

        .btn-secondary {
            background: var(--muted);
            color: white;
        }

        .btn-secondary:hover {
            background: #56606d;
        }
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
                <a href="index.php?module=socios&action=sobregiro&client=<?= rawurlencode($clienteCodigo) ?>" class="nav-link">Sobregiro</a>
                <a href="index.php?module=socios&action=aportes_completos&client=<?= rawurlencode($clienteCodigo) ?>" class="nav-link active">Aportes Completos</a>
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
                <h1>Aportaciones Completas</h1>
                <div class="user">
                    <div class="user-avatar"><?= $escape($displayName) ?></div>
                    <div>
                        <div class="user-name"><?= $escape($usuario) ?></div>
                        <div class="user-profile"><?= $escape($profile) ?></div>
                    </div>
                </div>
            </div>
            <p class="intro">Aportaciones completas del socio: <?= $escape($socio['F01_APE_PAT'] . ' ' . $socio['F01_APE_MAT'] . ', ' . $socio['F01_NOMB_PRI']) ?></p>

            <section class="panel">
                <h2>Resumen de Aportaciones</h2>
                <div class="total">
                    Total Aportado FONBES: <span><?= number_format($totalAportadoFonbes, 2) ?></span>
                </div>
                <div class="total">
                    Total Aportado COOPAM: <span><?= number_format($totalAportadoCoopam, 2) ?></span>
                </div>
                <div class="total">
                    Fecha Último Aporte FONBES: <span><?= $escape($fechaUltimoAporte ?? 'Sin aportes') ?></span>
                </div>
            </section>

            <section class="panel">
                <h2>Listado de Aportaciones</h2>
                <?php if (empty($aportaciones)): ?>
                    <div class="empty">No hay aportaciones registradas</div>
                <?php else: ?>
                    <table class="table">
                        <thead>
                            <tr>
                                <th>Tipo</th>
                                <th>Periodo</th>
                                <th>Aportación</th>
                                <th>Aportación Pagada</th>
                                <th class="right">Sobregiro</th>
                                <th>Fecha de Pago</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($aportaciones as $row): ?>
                                <tr>
                                    <td><?= $escape($row['tipo']) ?></td>
                                    <td><?= $escape($row['periodo']) ?></td>
                                    <td><?= number_format($row['aportacion'], 2) ?></td>
                                    <td><?= number_format($row['aportacion_pagada'], 2) ?></td>
                                    <td class="right"><?= number_format($row['sobregiro'], 2) ?></td>
                                    <td><?= $escape($row['fecha_pago'] ?? 'Sin fecha') ?></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                    
                    <?php if ($totalPaginas > 1): ?>
                        <div class="pagination">
                            <?php if ($pagina > 1): ?>
                                <a href="index.php?module=socios&action=aportes_completos&client=<?= rawurlencode($clienteCodigo) ?>&pagina=<?= $pagina - 1 ?>">Anterior</a>
                            <?php endif; ?>
                            <span>Página <?= $pagina ?> de <?= $totalPaginas ?> (<?= $totalAportaciones ?> registros)</span>
                            <?php if ($pagina < $totalPaginas): ?>
                                <a href="index.php?module=socios&action=aportes_completos&client=<?= rawurlencode($clienteCodigo) ?>&pagina=<?= $pagina + 1 ?>">Siguiente</a>
                            <?php endif; ?>
                        </div>
                    <?php endif; ?>
                <?php endif; ?>
            </section>

            <div style="margin-top: 24px;">
                <a href="index.php?module=socios&action=info&client=<?= rawurlencode($clienteCodigo) ?>" class="btn btn-secondary">Volver a Información Personal</a>
            </div>
        </main>
    </div>
</body>
</html>
