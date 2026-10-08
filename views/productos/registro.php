<?php
$escape = static fn (mixed $value): string => htmlspecialchars((string) ($value ?? ''), ENT_QUOTES, 'UTF-8');

$productosServicio = new \Sacs\Services\ProductosServicio(\Sacs\Core\Database::connection());

$usuario = $_SESSION['usuario'] ?? 'SISTEMA';
$displayName = substr($usuario, 0, 2);
$profile = 'Administrador';

$error = $_GET['error'] ?? null;
$success = $_GET['success'] ?? null;

$nextCodigo = $productosServicio->obtenerSiguienteCodigo();
?>
<!doctype html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Registrar Producto | SACS Nuevo</title>
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

        .form-group input,
        .form-group select {
            width: 100%;
            padding: 10px 12px;
            border: 1px solid var(--border);
            border-radius: 6px;
            font-size: 14px;
            font-family: inherit;
        }

        .form-group input:focus,
        .form-group select:focus {
            outline: none;
            border-color: var(--teal);
        }

        .form-group input:disabled {
            background: var(--paper);
            cursor: not-allowed;
        }

        .form-group .required {
            color: var(--red);
        }

        .form-group small {
            display: block;
            margin-top: 4px;
            font-size: 12px;
            color: var(--muted);
        }

        .form-row {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 16px;
        }

        .checkbox-group {
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .checkbox-group input[type="checkbox"] {
            width: auto;
        }

        .alert {
            padding: 12px 16px;
            border-radius: 6px;
            margin-bottom: 20px;
            font-size: 14px;
        }

        .alert-success {
            background: #dcfce7;
            color: #166534;
            border: 1px solid #bbf7d0;
        }

        .alert-error {
            background: #fee2e2;
            color: #991b1b;
            border: 1px solid #fecaca;
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

        .form-actions {
            display: flex;
            gap: 12px;
            margin-top: 24px;
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
            <div class="nav-label">Configuración</div>
            <nav class="nav">
                <a href="index.php?module=productos&action=listado" class="nav-link active">Productos</a>
                <a href="index.php?module=tarifarios&action=listado" class="nav-link">Tarifarios</a>
                <a href="index.php?module=tarifas_aportes&action=listado" class="nav-link">Tarifas de Aportes</a>
                <a href="index.php?module=tarifas_beneficios&action=listado" class="nav-link">Tarifas de Beneficios</a>
            </nav>
        </aside>

        <main class="main">
            <div class="header">
                <h1>Registrar Producto</h1>
                <div class="user">
                    <div class="user-avatar"><?= $escape($displayName) ?></div>
                    <div>
                        <div class="user-name"><?= $escape($usuario) ?></div>
                        <div class="user-profile"><?= $escape($profile) ?></div>
                    </div>
                </div>
            </div>
            <p class="intro">Complete el formulario para registrar un nuevo producto de préstamo</p>

            <?php if ($error): ?>
                <div class="alert alert-error">
                    <?= $escape($error) ?>
                </div>
            <?php endif; ?>

            <?php if ($success): ?>
                <div class="alert alert-success">
                    <?= $escape($success) ?>
                </div>
            <?php endif; ?>

            <section class="panel">
                <h2>Datos del Producto</h2>
                <form method="post" action="index.php">
                    <input type="hidden" name="module" value="productos">
                    <input type="hidden" name="action" value="registrar">
                    <input type="hidden" name="csrf_token" value="<?= $escape($_SESSION['csrf_token'] ?? '') ?>">
                    
                    <div class="form-group">
                        <label for="codigo">Código <span class="required">*</span></label>
                        <input type="number" id="codigo" name="codigo" value="<?= $escape($nextCodigo) ?>" required min="1">
                        <small>Código único para identificar el producto</small>
                    </div>

                    <div class="form-group">
                        <label for="nombre">Nombre del Producto <span class="required">*</span></label>
                        <input type="text" id="nombre" name="nombre" required>
                        <small>Nombre descriptivo del producto (ej: PRÉSTAMO PERSONAL)</small>
                    </div>

                    <div class="form-group">
                        <label for="cronograma">Cronograma <span class="required">*</span></label>
                        <input type="text" id="cronograma" name="cronograma" required>
                        <small>Tipo de cronograma (ej: MENSUAL, QUINCENAL)</small>
                    </div>

                    <div class="form-group">
                        <label for="moneda">Moneda <span class="required">*</span></label>
                        <select id="moneda" name="moneda" required>
                            <option value="">Seleccione</option>
                            <option value="SOLES">SOLES</option>
                            <option value="DOLARES">DOLARES</option>
                        </select>
                    </div>

                    <div class="form-row">
                        <div class="form-group">
                            <label for="max_cuotas">Máximo de Cuotas <span class="required">*</span></label>
                            <input type="number" id="max_cuotas" name="max_cuotas" required min="1">
                        </div>

                        <div class="form-group">
                            <label for="monto_min">Monto Mínimo <span class="required">*</span></label>
                            <input type="number" id="monto_min" name="monto_min" required min="0" step="0.01">
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="monto_max">Monto Máximo <span class="required">*</span></label>
                        <input type="number" id="monto_max" name="monto_max" required min="0" step="0.01">
                    </div>

                    <div class="form-group">
                        <label for="con_garante">Requiere Garante</label>
                        <select id="con_garante" name="con_garante">
                            <option value="N">No</option>
                            <option value="S">Sí</option>
                        </select>
                    </div>

                    <div class="form-actions">
                        <button type="submit" class="btn btn-primary">Registrar Producto</button>
                        <a href="index.php?module=productos&action=listado" class="btn btn-secondary">Cancelar</a>
                    </div>
                </form>
            </section>
        </main>
    </div>
</body>
</html>
