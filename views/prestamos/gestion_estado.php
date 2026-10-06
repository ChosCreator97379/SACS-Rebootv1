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

// Determinar acciones disponibles según el estado
$accionesDisponibles = [];
switch ($prestamo['estado']) {
    case 2: // Aprobado
    case 4: // Activo
        $accionesDisponibles[] = ['valor' => 'cancelar', 'nombre' => 'Cancelar Préstamo'];
        $accionesDisponibles[] = ['valor' => 'anular', 'nombre' => 'Anular Préstamo'];
        break;
    case 5: // Cancelado
        $accionesDisponibles[] = ['valor' => 'anular', 'nombre' => 'Anular Préstamo'];
        break;
    case 0: // Anulado
        $accionesDisponibles[] = ['valor' => 'activar', 'nombre' => 'Activar Préstamo'];
        break;
}
?>
<!doctype html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Gestión de Estado | SACS Nuevo</title>
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

        .btn-danger {
            background: var(--red);
            color: white;
        }

        .btn-danger:hover {
            background: #7f1d1d;
        }

        .btn-warning {
            background: var(--gold);
            color: white;
        }

        .btn-warning:hover {
            background: #b8860b;
        }

        .btn-secondary {
            background: var(--muted);
            color: white;
        }

        .btn-secondary:hover {
            background: #56606d;
        }

        .success {
            background: #dcfce7;
            color: #166534;
            padding: 12px;
            border-radius: 6px;
            margin-bottom: 20px;
        }

        .error {
            background: #fee2e2;
            color: #991b1b;
            padding: 12px;
            border-radius: 6px;
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

        .form-group select,
        .form-group textarea {
            width: 100%;
            padding: 10px 12px;
            border: 1px solid var(--border);
            border-radius: 6px;
            font-size: 14px;
            font-family: inherit;
        }

        .form-group textarea {
            min-height: 80px;
        }

        .form-group select:focus,
        .form-group textarea:focus {
            outline: none;
            border-color: var(--teal);
        }

        .actions {
            display: flex;
            gap: 12px;
            margin-top: 24px;
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
                <h1>Gestión de Estado - Préstamo #<?= $escape($prestamo['codigo']) ?></h1>
                <div class="user">
                    <div class="user-avatar"><?= $escape($displayName) ?></div>
                    <div>
                        <div class="user-name"><?= $escape($usuario) ?></div>
                        <div class="user-profile"><?= $escape($profile) ?></div>
                    </div>
                </div>
            </div>
            <p class="intro">Gestionar el estado del préstamo</p>

            <?php if (isset($_GET['success'])): ?>
                <div class="success"><?= $escape($_GET['success']) ?></div>
            <?php endif; ?>

            <?php if (isset($_GET['error'])): ?>
                <div class="error"><?= $escape($_GET['error']) ?></div>
            <?php endif; ?>

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
                        <span class="info-label">Estado Actual</span>
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
                </div>

                <?php if (empty($accionesDisponibles)): ?>
                    <div class="error">No hay acciones disponibles para este préstamo en su estado actual.</div>
                <?php else: ?>
                    <h2>Acciones Disponibles</h2>
                    <form method="post" action="index.php">
                        <input type="hidden" name="module" value="prestamos">
                        <input type="hidden" name="action" value="gestion_estado_accion">
                        <input type="hidden" name="codigo" value="<?= $escape($codigo) ?>">
                        <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">
                        
                        <div class="form-group">
                            <label for="accion">Seleccionar Acción</label>
                            <select id="accion" name="accion" required>
                                <option value="">-- Seleccionar --</option>
                                <?php foreach ($accionesDisponibles as $accion): ?>
                                    <option value="<?= $escape($accion['valor']) ?>">
                                        <?= $escape($accion['nombre']) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        
                        <div class="form-group" id="motivo-container" style="display: none;">
                            <label for="motivo">Motivo</label>
                            <textarea id="motivo" name="motivo" placeholder="Indique el motivo..."></textarea>
                        </div>
                        
                        <div class="actions">
                            <button type="submit" class="btn btn-primary">Procesar</button>
                            <a href="index.php?module=prestamos&action=listado" class="btn btn-secondary">Cancelar</a>
                        </div>
                    </form>
                <?php endif; ?>
            </section>
        </main>
    </div>

    <script>
        document.getElementById('accion').addEventListener('change', function() {
            const motivoContainer = document.getElementById('motivo-container');
            const motivo = document.getElementById('motivo');
            
            // Mostrar motivo para cancelar y anular
            if (this.value === 'cancelar' || this.value === 'anular') {
                motivoContainer.style.display = 'block';
                motivo.required = true;
            } else {
                motivoContainer.style.display = 'none';
                motivo.required = false;
            }
        });
    </script>
</body>
</html>
