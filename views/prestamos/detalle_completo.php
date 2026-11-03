<?php
$escape = static fn (mixed $value): string => htmlspecialchars((string) ($value ?? ''), ENT_QUOTES, 'UTF-8');

$prestamosServicio = new \Sacs\Services\PrestamosServicio();

$usuario = $_SESSION['sacs_user']['username'] ?? 'SISTEMA';
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

$detalle = $prestamosServicio->obtenerDetalleCompletoPrestamo($codigo);

if (!$detalle) {
    http_response_code(404);
    $error = 'Préstamo no encontrado';
    $query = http_build_query(['module' => 'prestamos', 'action' => 'listado', 'error' => $error]);
    header('Location: index.php?' . $query);
    exit;
}

$prestamo = $detalle['prestamo'];
$cronograma = $detalle['cronograma'];
$movimientos = $detalle['movimientos'];

// Cargar cronograma nuevo (f17_cuotasprestamo_nuevo)
$cronogramaNuevo = [];
try {
    $pdo = new PDO(
        'mysql:host=' . ($_ENV['DB_HOST'] ?? 'localhost') . ';dbname=' . ($_ENV['DB_NAME'] ?? 'sacs') . ';charset=utf8mb4',
        $_ENV['DB_USER'] ?? 'root',
        $_ENV['DB_PASS'] ?? '',
        [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
    );

    $sql = "SELECT
                f17_ncuota as numero,
                f17_fecha as fecha,
                f17_capital as capital,
                f17_interes as interes,
                f17_total as total,
                f17_saldo as saldo,
                f17_estado as estado
            FROM f17_cuotasprestamo_nuevo
            WHERE f17_prestamo = :codigo
            ORDER BY f17_ncuota ASC";

    $stmt = $pdo->prepare($sql);
    $stmt->execute(['codigo' => $codigo]);
    $cronogramaNuevo = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (Exception $e) {
    $cronogramaNuevo = [];
}

// Total de cuotas
$totalCuotas = count($cronograma);

// Mapeo de operaciones
$operaciones = [
    1 => 'APORTACIÓN',
    2 => 'AMORTIZACIÓN',
    3 => 'DESEMBOLSO AEE',
    4 => 'CANCELACIÓN AEE',
    5 => 'SOBREGIRO',
    6 => 'CANCELACIÓN',
    7 => 'DESEMBOLSO BENEFICIOS',
    8 => 'EGRESOS',
    10 => 'SOBREGIRO PRÉSTAMO',
];
?>
<!doctype html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Detalle Completo de Préstamo | SACS Nuevo</title>
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

        .btn-primary {
            background: var(--teal);
            color: white;
        }

        .btn-primary:hover {
            background: #166666;
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

        .table-scroll {
            overflow-x: auto;
        }

        .negative {
            color: var(--red);
        }

        .positive {
            color: var(--green);
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
                <a href="index.php?module=prestamos&action=cronograma&codigo=<?= $escape($codigo) ?>" class="nav-link">Cronograma</a>
            </nav>
            <div class="nav-label">Caja</div>
            <nav class="nav">
                <a href="index.php?module=caja" class="nav-link">Movimientos</a>
                <a href="index.php?module=caja&action=amortizar_form&client=<?= $escape($prestamo['cliente']) ?>&loan=<?= $escape($codigo) ?>" class="nav-link">Amortizar</a>
                <a href="index.php?module=caja&action=sobregiro_prestamo_form&client=<?= $escape($prestamo['cliente']) ?>&loan=<?= $escape($codigo) ?>" class="nav-link">Sobregiro</a>
            </nav>
        </aside>

        <main class="main">
            <div class="header">
                <h1>Detalle Completo de Préstamo #<?= $escape($prestamo['codigo']) ?></h1>
                <div class="user">
                    <div class="user-avatar"><?= $escape($displayName) ?></div>
                    <div>
                        <div class="user-name"><?= $escape($usuario) ?></div>
                        <div class="user-profile"><?= $escape($profile) ?></div>
                    </div>
                </div>
            </div>
            <p class="intro">Información completa del préstamo con historial de movimientos</p>

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
                        <span class="info-value">
                            <?php
                            $saldoRedondeado = $prestamo['saldo'];
                            // Solo redondear si termina en .99
                            if (abs(round($saldoRedondeado * 100) % 100 - 99) < 0.01) {
                                $saldoRedondeado = round($saldoRedondeado);
                            }
                            echo number_format($saldoRedondeado, 2);
                            ?>
                        </span>
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
                    <div class="info-item">
                        <span class="info-label">Trámite</span>
                        <span class="info-value"><?= $escape($prestamo['tramite'] ?? '-') ?></span>
                    </div>
                </div>
            </section>

            <section class="panel">
                <h2>Saldos Pendientes por Componente</h2>
                <div class="summary-grid">
                    <div class="summary-item">
                        <span class="summary-label">Sobregiro</span>
                        <span class="summary-value <?= $prestamo['sobre'] > 0 ? 'negative' : '' ?>"><?= number_format($prestamo['sobre'], 2) ?></span>
                    </div>
                    <div class="summary-item">
                        <span class="summary-label">Gastos Administrativos</span>
                        <span class="summary-value <?= $prestamo['gastos_activos'] > 0 ? 'negative' : '' ?>"><?= number_format($prestamo['gastos_activos'], 2) ?></span>
                    </div>
                    <div class="summary-item">
                        <span class="summary-label">Gastos de Mora</span>
                        <span class="summary-value <?= $prestamo['gastos_mora'] > 0 ? 'negative' : '' ?>"><?= number_format($prestamo['gastos_mora'], 2) ?></span>
                    </div>
                    <div class="summary-item">
                        <span class="summary-label">Otros 1</span>
                        <span class="summary-value <?= $prestamo['otros'] > 0 ? 'negative' : '' ?>"><?= number_format($prestamo['otros'], 2) ?></span>
                    </div>
                    <div class="summary-item">
                        <span class="summary-label">Otros 2</span>
                        <span class="summary-value <?= $prestamo['otros2'] > 0 ? 'negative' : '' ?>"><?= number_format($prestamo['otros2'], 2) ?></span>
                    </div>
                </div>
            </section>

            <section class="panel">
                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
                    <h2 style="margin: 0;">Cronograma de Cuotas</h2>
                    <a href="#" class="btn btn-secondary" onclick="event.preventDefault(); mostrarCronogramaNuevo(); return false;">Ver Cronograma Nuevo</a>
                </div>
                <?php if (empty($cronograma)): ?>
                    <div class="empty">No hay cronograma registrado</div>
                <?php else: ?>
                    <div class="table-scroll">
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
                                <?php foreach ($cronograma as $cuota): ?>
                                    <tr>
                                        <td><?= $escape($cuota['numero']) ?></td>
                                        <td><?= $escape($cuota['fecha']) ?></td>
                                        <td class="right"><?= number_format($cuota['capital'], 2) ?></td>
                                        <td class="right"><?= number_format($cuota['interes'], 2) ?></td>
                                        <td class="right"><?= number_format($cuota['total'], 2) ?></td>
                                        <td class="right"><?= number_format($cuota['saldo'], 2) ?></td>
                                        <td>
                                            <?php
                                            $estadoClase = 'badge-info';
                                            $estadoTexto = $cuota['estado'];
                                            switch ($cuota['estado']) {
                                                case 'VIGENTE': $estadoClase = 'badge-warning'; break;
                                                case 'PAGADO': $estadoClase = 'badge-success'; break;
                                                case 'CANCELADO': $estadoClase = 'badge-danger'; break;
                                            }
                                            ?>
                                            <span class="badge <?= $estadoClase ?>"><?= $escape($estadoTexto) ?></span>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php endif; ?>
            </section>

            <section class="panel">
                <h2>Historial de Movimientos</h2>
                <?php if (empty($movimientos)): ?>
                    <div class="empty">No hay movimientos registrados</div>
                <?php else: ?>
                    <div class="table-scroll">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>Código</th>
                                    <th>Fecha</th>
                                    <th>Operación</th>
                                    <th class="right">Amortización</th>
                                    <th class="right">Desembolso</th>
                                    <th class="right">Gastos Adm</th>
                                    <th class="right">Gastos Mora</th>
                                    <th class="right">Otros 1</th>
                                    <th class="right">Otros 2</th>
                                    <th class="right">Total</th>
                                    <th>Observaciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($movimientos as $mov): ?>
                                    <tr>
                                        <td><?= $escape($mov['codigo']) ?></td>
                                        <td><?= $escape($mov['fecha']) ?></td>
                                        <td><?= $escape($operaciones[$mov['operacion']] ?? 'OP-' . $mov['operacion']) ?></td>
                                        <td class="right"><?= $mov['amortizacion'] > 0 ? number_format($mov['amortizacion'], 2) : '-' ?></td>
                                        <td class="right"><?= $mov['desembolso'] > 0 ? number_format($mov['desembolso'], 2) : '-' ?></td>
                                        <td class="right"><?= $mov['gastos_administrativos'] > 0 ? number_format($mov['gastos_administrativos'], 2) : '-' ?></td>
                                        <td class="right"><?= $mov['gastos_mora'] > 0 ? number_format($mov['gastos_mora'], 2) : '-' ?></td>
                                        <td class="right"><?= $mov['otros1'] > 0 ? number_format($mov['otros1'], 2) : '-' ?></td>
                                        <td class="right"><?= $mov['otros2'] > 0 ? number_format($mov['otros2'], 2) : '-' ?></td>
                                        <td class="right">
                                            <?php
                                            $total = ($mov['amortizacion'] ?? 0) + ($mov['desembolso'] ?? 0) + ($mov['gastos_administrativos'] ?? 0) + ($mov['gastos_mora'] ?? 0) + ($mov['otros1'] ?? 0) + ($mov['otros2'] ?? 0);
                                            echo number_format($total, 2);
                                            ?>
                                        </td>
                                        <td><?= $escape($mov['observaciones'] ?? '-') ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php endif; ?>
            </section>

            <div style="margin-top: 24px;">
                <a href="index.php?module=prestamos&action=listado" class="btn btn-secondary">Volver al Listado</a>
                <a href="index.php?module=caja&action=amortizar_form&client=<?= $escape($prestamo['cliente']) ?>&loan=<?= $escape($codigo) ?>" class="btn btn-primary">Amortizar</a>
            </div>
        </main>

        <!-- Modal Cronograma Nuevo -->
        <div id="modal-cronograma-nuevo" style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.5); z-index: 1000;">
            <div style="position: absolute; top: 50%; left: 50%; transform: translate(-50%, -50%); background: white; padding: 20px; border-radius: 8px; max-width: 900px; max-height: 80vh; overflow-y: auto; width: 90%;">
                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
                    <h3 style="margin: 0;">Cronograma Nuevo (f17_cuotasprestamo_nuevo)</h3>
                    <button onclick="cerrarModalCronogramaNuevo()" style="background: none; border: none; font-size: 24px; cursor: pointer;">&times;</button>
                </div>
                <div id="cronograma-nuevo-contenido">Cargando...</div>
            </div>
        </div>

        <script>
            const cronogramaNuevoData = <?= json_encode($cronogramaNuevo) ?>;

            function mostrarCronogramaNuevo() {
                document.getElementById('modal-cronograma-nuevo').style.display = 'block';
                renderizarCronogramaNuevo();
            }

            function cerrarModalCronogramaNuevo() {
                document.getElementById('modal-cronograma-nuevo').style.display = 'none';
            }

            function renderizarCronogramaNuevo() {
                const contenido = document.getElementById('cronograma-nuevo-contenido');

                if (cronogramaNuevoData.length === 0) {
                    contenido.innerHTML = '<p>No hay cronograma nuevo registrado</p>';
                    return;
                }

                let html = '<div class="table-scroll"><table class="table" style="width: 100%; border-collapse: collapse;">';
                html += '<thead><tr><th>N°</th><th>Fecha</th><th>Capital</th><th>Interés</th><th>Total</th><th>Saldo</th><th>Estado</th></tr></thead>';
                html += '<tbody>';

                cronogramaNuevoData.forEach(cuota => {
                    let estadoClase = 'badge-info';
                    switch(cuota.estado) {
                        case 'VIGENTE': estadoClase = 'badge-warning'; break;
                        case 'PAGADO': estadoClase = 'badge-success'; break;
                        case 'CANCELADO': estadoClase = 'badge-danger'; break;
                        case 'PARCIAL': estadoClase = 'badge-info'; break;
                    }

                    html += '<tr>';
                    html += '<td>' + cuota.numero + '</td>';
                    html += '<td>' + cuota.fecha + '</td>';
                    html += '<td style="text-align: right;">' + Number(cuota.capital).toFixed(2) + '</td>';
                    html += '<td style="text-align: right;">' + Number(cuota.interes).toFixed(2) + '</td>';
                    html += '<td style="text-align: right;">' + Number(cuota.total).toFixed(2) + '</td>';
                    html += '<td style="text-align: right;">' + Number(cuota.saldo).toFixed(2) + '</td>';
                    html += '<td><span class="badge ' + estadoClase + '">' + cuota.estado + '</span></td>';
                    html += '</tr>';
                });

                html += '</tbody></table></div>';
                contenido.innerHTML = html;
            }

            // Cerrar modal al hacer clic fuera del contenido
            document.getElementById('modal-cronograma-nuevo').addEventListener('click', function(e) {
                if (e.target === this) {
                    cerrarModalCronogramaNuevo();
                }
            });
        </script>
</body>
</html>
