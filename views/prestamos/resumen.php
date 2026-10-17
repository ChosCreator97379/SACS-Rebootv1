<?php
$escape = static fn (mixed $value): string => htmlspecialchars((string) ($value ?? ''), ENT_QUOTES, 'UTF-8');

$prestamosServicio = new \Sacs\Services\PrestamosServicio();

$usuario = $_SESSION['usuario'] ?? 'SISTEMA';
$displayName = substr($usuario, 0, 2);
$profile = 'Administrador';

$codigo = trim((string) ($_GET['codigo'] ?? ''));

if ($codigo === '') {
    http_response_code(400);
    $error = 'Código de préstamo no especificado';
    $query = http_build_query(['module' => 'prestamos', 'action' => 'listado', 'error' => $error]);
    header('Location: index.php?' . $query);
    exit;
}

$prestamo = $prestamosServicio->obtenerResumenPrestamo($codigo);

if (!$prestamo) {
    http_response_code(404);
    $error = 'Préstamo no encontrado';
    $query = http_build_query(['module' => 'prestamos', 'action' => 'listado', 'error' => $error]);
    header('Location: index.php?' . $query);
    exit;
}

// Obtener movimientos (solo si es necesario)
$movimientos = $prestamosServicio->obtenerMovimientosPrestamo($codigo);

// Total de cuotas (suma de pagadas + pendientes)
$totalCuotas = ($prestamo['cuotas_pagadas'] ?? 0) + ($prestamo['cuotas_pendientes'] ?? 0);
?>
<!doctype html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Resumen de Préstamo | SACS Nuevo</title>
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

        body {
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif;
            background: var(--paper);
            color: var(--ink);
            line-height: 1.5;
        }

        .app {
            display: flex;
            min-height: 100vh;
        }

        .sidebar {
            width: 280px;
            background: var(--navy);
            color: white;
            padding: 20px;
            display: flex;
            flex-direction: column;
        }

        .brand {
            display: flex;
            align-items: center;
            gap: 12px;
            margin-bottom: 30px;
            text-decoration: none;
            color: white;
        }

        .brand-mark {
            display: grid;
            place-items: center;
            width: 38px;
            height: 38px;
            border: 2px solid var(--gold);
            border-radius: 50%;
            color: var(--gold);
            font-family: Georgia, serif;
            font-size: 20px;
        }

        .brand-name {
            font-family: Georgia, serif;
            font-size: 22px;
            letter-spacing: .02em;
        }

        .brand-note {
            display: block;
            margin-top: 3px;
            color: #9bb3b7;
            font-size: 11px;
            letter-spacing: .12em;
            text-transform: uppercase;
        }

        .nav-label {
            font-size: 11px;
            text-transform: uppercase;
            letter-spacing: 1px;
            color: rgba(255,255,255,0.5);
            margin: 20px 0 10px;
        }

        .nav {
            display: flex;
            flex-direction: column;
            gap: 4px;
        }

        .nav-link {
            padding: 10px 12px;
            border-radius: 6px;
            text-decoration: none;
            color: rgba(255,255,255,0.8);
            font-size: 14px;
            transition: all 0.2s;
        }

        .nav-link:hover {
            background: rgba(255,255,255,0.1);
            color: white;
        }

        .nav-link.active {
            background: var(--teal);
            color: white;
        }

        .main {
            flex: 1;
            padding: 30px;
            overflow-y: auto;
        }

        .header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
        }

        .header h1 {
            font-size: 24px;
            font-weight: 600;
        }

        .user {
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .user-avatar {
            width: 36px;
            height: 36px;
            background: var(--teal);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 600;
        }

        .user-name {
            font-weight: 500;
        }

        .user-profile {
            font-size: 12px;
            color: var(--muted);
        }

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

        .info-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 16px;
            margin-bottom: 24px;
        }

        .info-item {
            display: flex;
            flex-direction: column;
            gap: 4px;
        }

        .info-label {
            font-size: 12px;
            font-weight: 600;
            color: var(--muted);
            text-transform: uppercase;
        }

        .info-value {
            font-size: 15px;
            font-weight: 500;
        }

        .table {
            width: 100%;
            border-collapse: collapse;
            font-size: 13px;
        }

        .table th,
        .table td {
            padding: 10px;
            text-align: left;
            border-bottom: 1px solid var(--border);
        }

        .table th {
            font-weight: 600;
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

        .badge {
            padding: 4px 8px;
            border-radius: 4px;
            font-size: 11px;
            font-weight: 600;
        }

        .badge-success {
            background: #dcfce7;
            color: #166534;
        }

        .badge-warning {
            background: #fef9c3;
            color: #854d0e;
        }

        .badge-danger {
            background: #fee2e2;
            color: #991b1b;
        }

        .badge-info {
            background: #dbeafe;
            color: #1e40af;
        }

        .summary-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
            gap: 16px;
            margin-bottom: 24px;
        }

        .summary-item {
            display: flex;
            flex-direction: column;
            gap: 4px;
        }

        .summary-label {
            font-size: 12px;
            font-weight: 600;
            color: var(--muted);
            text-transform: uppercase;
        }

        .summary-value {
            font-size: 20px;
            font-weight: 600;
        }
    </style>
</head>
<body>
    <div class="app">
        <aside class="sidebar">
            <a href="index.php" class="brand">
                <div class="brand-mark">S</div>
                <div>
                    <div class="brand-name">SACS</div>
                    <div class="brand-note">gestion social</div>
                </div>
            </a>
            <div class="nav-label">Préstamos</div>
            <nav class="nav">
                <a href="index.php?module=prestamos&action=listado" class="nav-link">Listado de Préstamos</a>
                <a href="index.php?module=prestamos&action=registro" class="nav-link">Registrar Préstamo</a>
            </nav>
            <div class="nav-label">Reportes</div>
            <nav class="nav">
                <a href="index.php?module=prestamos&action=reporte_general" class="nav-link">Reporte General</a>
                <a href="index.php?module=prestamos&action=reporte_consolidado" class="nav-link">Reporte Consolidado</a>
                <a href="index.php?module=prestamos&action=reporte_cuota" class="nav-link">Reporte por Cuota</a>
            </nav>
        </aside>

        <main class="main">
            <div class="header">
                <h1>Resumen de Préstamo #<?= $escape($prestamo['codigo']) ?></h1>
                <div class="user">
                    <div class="user-avatar"><?= $escape($displayName) ?></div>
                    <div>
                        <div class="user-name"><?= $escape($usuario) ?></div>
                        <div class="user-profile"><?= $escape($profile) ?></div>
                    </div>
                </div>
            </div>
            <p class="intro">Información completa del préstamo</p>

            <section class="panel">
                <h2>Información del Préstamo</h2>
                <div class="info-grid">
                    <div class="info-item">
                        <span class="info-label">Código</span>
                        <span class="info-value"><?= $escape($prestamo['codigo']) ?></span>
                    </div>
                    <div class="info-item">
                        <span class="info-label">Cliente</span>
                        <span class="info-value"><?= $escape($prestamo['apellido_paterno'] . ' ' . $prestamo['apellido_materno'] . ', ' . $prestamo['nombre_primero']) ?></span>
                    </div>
                    <div class="info-item">
                        <span class="info-label">Importe</span>
                        <span class="info-value"><?= number_format($prestamo['importe'], 2) ?></span>
                    </div>
                    <div class="info-item">
                        <span class="info-label">Saldo</span>
                        <span class="info-value"><?= number_format($prestamo['saldo'], 2) ?></span>
                    </div>
                    <div class="info-item">
                        <span class="info-label">Cuotas</span>
                        <span class="info-value"><?= $escape($prestamo['cuotas']) ?></span>
                    </div>
                    <div class="info-item">
                        <span class="info-label">Cuotas Pendientes</span>
                        <span class="info-value"><?= $escape($prestamo['cuotas_pendientes']) ?></span>
                    </div>
                    <div class="info-item">
                        <span class="info-label">Cuota Mensual</span>
                        <span class="info-value"><?= number_format($prestamo['cuota_mensual'], 2) ?></span>
                    </div>
                    <div class="info-item">
                        <span class="info-label">Interés</span>
                        <span class="info-value"><?= number_format($prestamo['interes'], 2) ?>%</span>
                    </div>
                    <div class="info-item">
                        <span class="info-label">Fecha Desembolso</span>
                        <span class="info-value"><?= $escape($prestamo['fecha_desembolso']) ?></span>
                    </div>
                    <div class="info-item">
                        <span class="info-label">Estado</span>
                        <span class="info-value">
                            <?php
                            $estadoClase = 'badge-info';
                            $estadoTexto = $prestamo['estado'];
                            switch ($prestamo['estado']) {
                                case 1: $estadoClase = 'badge-warning'; $estadoTexto = 'Pendiente'; break;
                                case 2: $estadoClase = 'badge-success'; $estadoTexto = 'Aprobado'; break;
                                case 3: $estadoClase = 'badge-danger'; $estadoTexto = 'Rechazado'; break;
                                case 4: $estadoClase = 'badge-success'; $estadoTexto = 'Activo'; break;
                                case 5: $estadoClase = 'badge-danger'; $estadoTexto = 'Cancelado'; break;
                                case 0: $estadoClase = 'badge-danger'; $estadoTexto = 'Anulado'; break;
                            }
                            ?>
                            <span class="badge <?= $estadoClase ?>"><?= $escape($estadoTexto) ?></span>
                        </span>
                    </div>
                    <div class="info-item">
                        <span class="info-label">Producto</span>
                        <span class="info-value"><?= $escape($prestamo['producto'] ?? '-') ?></span>
                    </div>
                </div>
            </section>

            <section class="panel">
                <h2>Resumen de Cuotas</h2>
                <div class="summary-grid">
                    <div class="summary-item">
                        <span class="summary-label">Total Cuotas</span>
                        <span class="summary-value"><?= $escape($totalCuotas) ?></span>
                    </div>
                    <div class="summary-item">
                        <span class="summary-label">Pagadas</span>
                        <span class="summary-value" style="color: var(--green);"><?= $escape($prestamo['cuotas_pagadas'] ?? 0) ?></span>
                    </div>
                    <div class="summary-item">
                        <span class="summary-label">Pendientes</span>
                        <span class="summary-value" style="color: var(--gold);"><?= $escape($prestamo['cuotas_pendientes'] ?? 0) ?></span>
                    </div>
                </div>
            </section>

            <section class="panel">
                <h2>Últimos Movimientos</h2>
                <?php if (empty($movimientos)): ?>
                    <div class="empty">No hay movimientos registrados</div>
                <?php else: ?>
                    <div style="overflow-x: auto;">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>Código</th>
                                    <th>Fecha</th>
                                    <th>Operación</th>
                                    <th class="right">Monto</th>
                                    <th>Observaciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($movimientos as $row): ?>
                                    <tr>
                                        <td><?= $escape($row['codigo']) ?></td>
                                        <td><?= $escape($row['fecha']) ?></td>
                                        <td><?= $escape($row['operacion']) ?></td>
                                        <td class="right"><?= number_format($row['monto_prestamo'], 2) ?></td>
                                        <td><?= $escape($row['observaciones'] ?? '-') ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php endif; ?>
            </section>

            <div style="margin-top: 24px;">
                <a href="index.php?module=prestamos&action=listado" class="btn btn-secondary">Volver al Listado</a>
                <a href="index.php?module=prestamos&action=detalle_completo&codigo=<?= $escape($prestamo['codigo']) ?>" class="btn btn-primary">Ver Detalle Completo</a>
            </div>
        </main>
    </div>
</body>
</html>
