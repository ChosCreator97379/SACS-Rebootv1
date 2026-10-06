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

$prestamo = $prestamosServicio->obtenerPrestamo($codigo);

if (!$prestamo) {
    http_response_code(404);
    $error = 'Préstamo no encontrado';
    $query = http_build_query(['module' => 'prestamos', 'action' => 'listado', 'error' => $error]);
    header('Location: index.php?' . $query);
    exit;
}

// Obtener cronograma
$cronograma = $prestamosServicio->obtenerCronograma($codigo);
$totalCuotas = $prestamosServicio->contarCuotas($codigo);
$cuotasPagadas = $prestamosServicio->obtenerCuotasPagadas($codigo);
$cuotasPendientes = $prestamosServicio->obtenerCuotasPendientes($codigo);
$cuotasVencidas = $prestamosServicio->obtenerCuotasVencidas($codigo);

// Paginación
$pagina = max(1, (int) ($_GET['pagina'] ?? 1));
$porPagina = 20;
$totalPaginas = max(1, ceil($totalCuotas / $porPagina));
$offset = ($pagina - 1) * $porPagina;
$cronogramaPaginado = array_slice($cronograma, $offset, $porPagina);

// Filtro por estado
$filtroEstado = trim((string) ($_GET['estado'] ?? ''));
if ($filtroEstado !== '') {
    if ($filtroEstado === 'pagadas') {
        $cronogramaPaginado = $cuotasPagadas;
        $totalCuotas = count($cuotasPagadas);
    } elseif ($filtroEstado === 'pendientes') {
        $cronogramaPaginado = $cuotasPendientes;
        $totalCuotas = count($cuotasPendientes);
    } elseif ($filtroEstado === 'vencidas') {
        $cronogramaPaginado = $cuotasVencidas;
        $totalCuotas = count($cuotasVencidas);
    }
    $totalPaginas = max(1, ceil($totalCuotas / $porPagina));
    $cronogramaPaginado = array_slice($cronogramaPaginado, $offset, $porPagina);
}
?>
<!doctype html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Cronograma de Cuotas | SACS Nuevo</title>
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

        .filters {
            display: flex;
            gap: 12px;
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

        .form-group select {
            width: 100%;
            padding: 10px 12px;
            border: 1px solid var(--border);
            border-radius: 6px;
            font-size: 14px;
            font-family: inherit;
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
                <h1>Cronograma de Cuotas - Préstamo #<?= $escape($prestamo['codigo']) ?></h1>
                <div class="user">
                    <div class="user-avatar"><?= $escape($displayName) ?></div>
                    <div>
                        <div class="user-name"><?= $escape($usuario) ?></div>
                        <div class="user-profile"><?= $escape($profile) ?></div>
                    </div>
                </div>
            </div>
            <p class="intro">Cronograma de cuotas del préstamo</p>

            <section class="panel">
                <h2>Resumen</h2>
                <div class="summary-grid">
                    <div class="summary-item">
                        <span class="summary-label">Total Cuotas</span>
                        <span class="summary-value"><?= $escape($totalCuotas) ?></span>
                    </div>
                    <div class="summary-item">
                        <span class="summary-label">Pagadas</span>
                        <span class="summary-value" style="color: var(--green);"><?= $escape(count($cuotasPagadas)) ?></span>
                    </div>
                    <div class="summary-item">
                        <span class="summary-label">Pendientes</span>
                        <span class="summary-value" style="color: var(--gold);"><?= $escape(count($cuotasPendientes)) ?></span>
                    </div>
                    <div class="summary-item">
                        <span class="summary-label">Vencidas</span>
                        <span class="summary-value" style="color: var(--red);"><?= $escape(count($cuotasVencidas)) ?></span>
                    </div>
                </div>
            </section>

            <section class="panel">
                <h2>Cronograma</h2>
                <div class="filters">
                    <div class="form-group" style="flex: 1;">
                        <label for="estado">Filtrar por Estado</label>
                        <select id="estado" onchange="window.location.href='index.php?module=prestamos&action=cronograma&codigo=<?= $escape($codigo) ?>&estado=' + this.value">
                            <option value="">Todas</option>
                            <option value="pagadas" <?= $filtroEstado === 'pagadas' ? 'selected' : '' ?>>Pagadas</option>
                            <option value="pendientes" <?= $filtroEstado === 'pendientes' ? 'selected' : '' ?>>Pendientes</option>
                            <option value="vencidas" <?= $filtroEstado === 'vencidas' ? 'selected' : '' ?>>Vencidas</option>
                        </select>
                    </div>
                </div>

                <?php if (empty($cronogramaPaginado)): ?>
                    <div class="empty">No hay cuotas registradas</div>
                <?php else: ?>
                    <div style="overflow-x: auto;">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>N°</th>
                                    <th>Fecha</th>
                                    <th class="right">Capital</th>
                                    <th class="right">Interés</th>
                                    <th class="right">Total</th>
                                    <th class="right">Saldo</th>
                                    <th>Estado</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($cronogramaPaginado as $row): ?>
                                    <tr>
                                        <td><?= $escape($row['numero']) ?></td>
                                        <td><?= $escape($row['fecha']) ?></td>
                                        <td class="right"><?= number_format($row['capital'], 2) ?></td>
                                        <td class="right"><?= number_format($row['interes'], 2) ?></td>
                                        <td class="right"><?= number_format($row['total'], 2) ?></td>
                                        <td class="right"><?= number_format($row['saldo'], 2) ?></td>
                                        <td>
                                            <?php
                                            $estadoClase = 'badge-info';
                                            $estadoTexto = $row['estado'];
                                            switch ($row['estado']) {
                                                case 'PAGADO': $estadoClase = 'badge-success'; $estadoTexto = 'Pagada'; break;
                                                case 'VIGENTE': $estadoClase = 'badge-warning'; $estadoTexto = 'Pendiente'; break;
                                                case 'CANCELADO': $estadoClase = 'badge-danger'; $estadoTexto = 'Cancelada'; break;
                                            }
                                            ?>
                                            <span class="badge <?= $estadoClase ?>"><?= $escape($estadoTexto) ?></span>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                    
                    <?php if ($totalPaginas > 1): ?>
                        <div class="pagination">
                            <?php if ($pagina > 1): ?>
                                <a href="index.php?module=prestamos&action=cronograma&codigo=<?= $escape($codigo) ?>&estado=<?= $escape($filtroEstado) ?>&pagina=<?= $pagina - 1 ?>">Anterior</a>
                            <?php endif; ?>
                            <span>Página <?= $pagina ?> de <?= $totalPaginas ?> (<?= $totalCuotas ?> registros)</span>
                            <?php if ($pagina < $totalPaginas): ?>
                                <a href="index.php?module=prestamos&action=cronograma&codigo=<?= $escape($codigo) ?>&estado=<?= $escape($filtroEstado) ?>&pagina=<?= $pagina + 1 ?>">Siguiente</a>
                            <?php endif; ?>
                        </div>
                    <?php endif; ?>
                <?php endif; ?>
            </section>

            <div style="margin-top: 24px;">
                <a href="index.php?module=prestamos&action=listado" class="btn btn-secondary">Volver al Listado</a>
            </div>
        </main>
    </div>
</body>
</html>
