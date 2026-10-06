<?php
$escape = static fn (mixed $value): string => htmlspecialchars((string) ($value ?? ''), ENT_QUOTES, 'UTF-8');

$prestamosServicio = new \Sacs\Services\PrestamosServicio();

$usuario = $_SESSION['usuario'] ?? 'SISTEMA';
$displayName = substr($usuario, 0, 2);
$profile = 'Administrador';

// Obtener filtros
$filtros = [
    'fecha_inicio' => trim((string) ($_GET['fecha_inicio'] ?? '')),
    'fecha_fin' => trim((string) ($_GET['fecha_fin'] ?? ''))
];

// Limpiar filtros vacíos
$filtros = array_filter($filtros, fn($v) => $v !== '');

$prestamos = $prestamosServicio->reportePrestamosConsolidado($filtros);
?>
<!doctype html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Reporte Consolidado de Préstamos | SACS Nuevo</title>
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

        .filters {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 16px;
            margin-bottom: 20px;
        }

        .form-group {
            margin-bottom: 16px;
        }

        .form-group label {
            display: block;
            font-size: 13px;
            font-weight: 500;
            margin-bottom: 6px;
            color: var(--muted);
        }

        .form-group input {
            width: 100%;
            padding: 10px 12px;
            border: 1px solid var(--border);
            border-radius: 6px;
            font-size: 14px;
            font-family: inherit;
        }

        .form-group input:focus {
            outline: none;
            border-color: var(--teal);
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

        .btn-primary {
            background: var(--teal);
            color: white;
        }

        .btn-primary:hover {
            background: #166160;
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
                <a href="index.php?module=prestamos&action=reporte_general" class="nav-link">Reporte General</a>
                <a href="index.php?module=prestamos&action=reporte_consolidado" class="nav-link active">Reporte Consolidado</a>
                <a href="index.php?module=prestamos&action=reporte_cuota" class="nav-link">Reporte por Cuota</a>
            </nav>
        </aside>

        <main class="main">
            <div class="header">
                <h1>Reporte Consolidado de Préstamos</h1>
                <div class="user">
                    <div class="user-avatar"><?= $escape($displayName) ?></div>
                    <div>
                        <div class="user-name"><?= $escape($usuario) ?></div>
                        <div class="user-profile"><?= $escape($profile) ?></div>
                    </div>
                </div>
            </div>
            <p class="intro">Resumen consolidado de préstamos por estado</p>

            <section class="panel">
                <h2>Filtros</h2>
                <form method="get" action="index.php">
                    <input type="hidden" name="module" value="prestamos">
                    <input type="hidden" name="action" value="reporte_consolidado">
                    
                    <div class="filters">
                        <div class="form-group">
                            <label for="fecha_inicio">Fecha Inicio</label>
                            <input type="date" id="fecha_inicio" name="fecha_inicio" value="<?= $escape($filtros['fecha_inicio'] ?? '') ?>">
                        </div>
                        <div class="form-group">
                            <label for="fecha_fin">Fecha Fin</label>
                            <input type="date" id="fecha_fin" name="fecha_fin" value="<?= $escape($filtros['fecha_fin'] ?? '') ?>">
                        </div>
                    </div>
                    
                    <button type="submit" class="btn btn-primary">Filtrar</button>
                    <a href="index.php?module=prestamos&action=reporte_consolidado" class="btn btn-secondary">Limpiar</a>
                </form>
            </section>

            <section class="panel">
                <h2>Resultados</h2>
                <?php if (empty($prestamos)): ?>
                    <div class="empty">No hay datos</div>
                <?php else: ?>
                    <div style="overflow-x: auto;">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>Estado</th>
                                    <th>Cantidad</th>
                                    <th class="right">Total Importe</th>
                                    <th class="right">Total Saldo</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($prestamos as $row): ?>
                                    <tr>
                                        <td>
                                            <?php
                                            $estadoClase = 'badge-info';
                                            $estadoTexto = $row['estado'];
                                            switch ($row['estado']) {
                                                case 1: $estadoClase = 'badge-warning'; $estadoTexto = 'Pendiente'; break;
                                                case 2: $estadoClase = 'badge-success'; $estadoTexto = 'Aprobado'; break;
                                                case 3: $estadoClase = 'badge-danger'; $estadoTexto = 'Rechazado'; break;
                                                case 4: $estadoClase = 'badge-success'; $estadoTexto = 'Activo'; break;
                                                case 5: $estadoClase = 'badge-danger'; $estadoTexto = 'Cancelado'; break;
                                                case 0: $estadoClase = 'badge-danger'; $estadoTexto = 'Anulado'; break;
                                            }
                                            ?>
                                            <span class="badge <?= $estadoClase ?>"><?= $escape($estadoTexto) ?></span>
                                        </td>
                                        <td><?= $escape($row['cantidad']) ?></td>
                                        <td class="right"><?= number_format($row['total_importe'], 2) ?></td>
                                        <td class="right"><?= number_format($row['total_saldo'], 2) ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php endif; ?>
            </section>
        </main>
    </div>
</body>
</html>
