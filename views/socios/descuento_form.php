<?php
$escape = static fn (mixed $value): string => htmlspecialchars((string) ($value ?? ''), ENT_QUOTES, 'UTF-8');

$sociosServicio = new \Sacs\Services\SociosServicio();
$clienteCodigo = $_GET['client'] ?? '';
$periodo = $_GET['periodo'] ?? '';

// Obtener información del socio
$socio = $sociosServicio->obtenerSocioPorCliente($clienteCodigo);
if (!$socio) {
    header('Location: index.php?module=socios&action=listado&error=Socio no encontrado');
    exit;
}

$codigoPersona = $socio['F01_ID_PERSONA'];

// Determinar si es edición o nuevo
$modo = !empty($periodo) ? 'editar' : 'nuevo';
$descuento = null;

if ($modo === 'editar') {
    $descuento = $sociosServicio->obtenerDescuento($codigoPersona, $periodo);
    if (!$descuento) {
        header('Location: index.php?module=socios&action=descuentos&client=' . rawurlencode($clienteCodigo) . '&error=Descuento no encontrado');
        exit;
    }
}

$usuario = $_SESSION['usuario'] ?? 'SISTEMA';
$displayName = substr($usuario, 0, 2);
$profile = 'Administrador';
?>
<!doctype html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= $modo === 'nuevo' ? 'Nuevo' : 'Editar' ?> Descuento | SACS Nuevo</title>
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
            width: 40px;
            height: 40px;
            background: var(--teal);
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: bold;
            font-size: 20px;
        }

        .brand-name {
            font-weight: 600;
            font-size: 18px;
        }

        .brand-note {
            font-size: 12px;
            color: rgba(255,255,255,0.7);
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

        .form-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 16px;
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
            <div class="nav-label">Socios</div>
            <nav class="nav">
                <a href="index.php?module=socios&action=listado" class="nav-link">Listado de Socios</a>
                <a href="index.php?module=socios&action=registro" class="nav-link">Registro de Socio</a>
                <a href="index.php?module=socios&action=info&client=<?= rawurlencode($clienteCodigo) ?>" class="nav-link">Información Personal</a>
                <a href="index.php?module=socios&action=observaciones&client=<?= rawurlencode($clienteCodigo) ?>" class="nav-link">Observaciones</a>
                <a href="index.php?module=socios&action=carta&client=<?= rawurlencode($clienteCodigo) ?>" class="nav-link">Carta</a>
                <a href="index.php?module=socios&action=sobregiro&client=<?= rawurlencode($clienteCodigo) ?>" class="nav-link">Sobregiro</a>
                <a href="index.php?module=socios&action=aportes_completos&client=<?= rawurlencode($clienteCodigo) ?>" class="nav-link">Aportes Completos</a>
                <a href="index.php?module=socios&action=descuentos&client=<?= rawurlencode($clienteCodigo) ?>" class="nav-link active">Descuentos</a>
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
                <h1><?= $modo === 'nuevo' ? 'Nuevo' : 'Editar' ?> Descuento</h1>
                <div class="user">
                    <div class="user-avatar"><?= $escape($displayName) ?></div>
                    <div>
                        <div class="user-name"><?= $escape($usuario) ?></div>
                        <div class="user-profile"><?= $escape($profile) ?></div>
                    </div>
                </div>
            </div>
            <p class="intro"><?= $modo === 'nuevo' ? 'Registrar' : 'Modificar' ?> descuento del socio: <?= $escape($socio['F01_APE_PAT'] . ' ' . $socio['F01_APE_MAT'] . ', ' . $socio['F01_NOMB_PRI']) ?></p>

            <?php if (isset($_GET['success'])): ?>
                <div class="success"><?= $escape($_GET['success']) ?></div>
            <?php endif; ?>

            <?php if (isset($_GET['error'])): ?>
                <div class="error"><?= $escape($_GET['error']) ?></div>
            <?php endif; ?>

            <section class="panel">
                <h2><?= $modo === 'nuevo' ? 'Nuevo' : 'Editar' ?> Descuento</h2>
                <form method="post" action="index.php">
                    <input type="hidden" name="module" value="socios">
                    <input type="hidden" name="action" value="descuento_guardar">
                    <input type="hidden" name="client" value="<?= $escape($clienteCodigo) ?>">
                    <input type="hidden" name="modo" value="<?= $escape($modo) ?>">
                    <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">
                    
                    <?php if ($modo === 'editar'): ?>
                        <input type="hidden" name="periodo_original" value="<?= $escape($periodo) ?>">
                    <?php endif; ?>
                    
                    <div class="form-grid">
                        <div class="form-group">
                            <label for="periodo">Periodo *</label>
                            <input type="text" id="periodo" name="periodo" value="<?= $escape($descuento['periodo'] ?? '') ?>" required <?= $modo === 'editar' ? 'readonly' : '' ?>>
                        </div>
                        
                        <div class="form-group">
                            <label for="tipo">Tipo</label>
                            <select id="tipo" name="tipo">
                                <option value="">Seleccionar</option>
                                <option value="1" <?= ($descuento['tipo'] ?? '') === '1' ? 'selected' : '' ?>>Tipo 1</option>
                                <option value="2" <?= ($descuento['tipo'] ?? '') === '2' ? 'selected' : '' ?>>Tipo 2</option>
                                <option value="3" <?= ($descuento['tipo'] ?? '') === '3' ? 'selected' : '' ?>>Tipo 3</option>
                            </select>
                        </div>
                        
                        <div class="form-group">
                            <label for="aportacion_pendiente">Aportación Pendiente</label>
                            <input type="number" id="aportacion_pendiente" name="aportacion_pendiente" step="0.01" value="<?= $escape($descuento['aportacion_pendiente'] ?? 0) ?>">
                        </div>
                        
                        <div class="form-group">
                            <label for="aportacion_actual">Aportación Actual</label>
                            <input type="number" id="aportacion_actual" name="aportacion_actual" step="0.01" value="<?= $escape($descuento['aportacion_actual'] ?? 0) ?>">
                        </div>
                        
                        <div class="form-group">
                            <label for="aportacion_deducida">Aportación Deducida</label>
                            <input type="number" id="aportacion_deducida" name="aportacion_deducida" step="0.01" value="<?= $escape($descuento['aportacion_deducida'] ?? 0) ?>">
                        </div>
                        
                        <div class="form-group">
                            <label for="monto_pendiente">Monto Pendiente</label>
                            <input type="number" id="monto_pendiente" name="monto_pendiente" step="0.01" value="<?= $escape($descuento['monto_pendiente'] ?? 0) ?>">
                        </div>
                        
                        <div class="form-group">
                            <label for="monto_actual">Monto Actual</label>
                            <input type="number" id="monto_actual" name="monto_actual" step="0.01" value="<?= $escape($descuento['monto_actual'] ?? 0) ?>">
                        </div>
                        
                        <div class="form-group">
                            <label for="monto_deducido">Monto Deducido</label>
                            <input type="number" id="monto_deducido" name="monto_deducido" step="0.01" value="<?= $escape($descuento['monto_deducido'] ?? 0) ?>">
                        </div>
                        
                        <div class="form-group">
                            <label for="amortizacion">Amortización</label>
                            <input type="number" id="amortizacion" name="amortizacion" step="0.01" value="<?= $escape($descuento['amortizacion'] ?? 0) ?>">
                        </div>
                        
                        <div class="form-group">
                            <label for="mora_activa">Mora Activa</label>
                            <input type="number" id="mora_activa" name="mora_activa" step="0.01" value="<?= $escape($descuento['mora_activa'] ?? 0) ?>">
                        </div>
                        
                        <div class="form-group">
                            <label for="mora_deducida">Mora Deducida</label>
                            <input type="number" id="mora_deducida" name="mora_deducida" step="0.01" value="<?= $escape($descuento['mora_deducida'] ?? 0) ?>">
                        </div>
                        
                        <div class="form-group">
                            <label for="interes_activo">Interés Activo</label>
                            <input type="number" id="interes_activo" name="interes_activo" step="0.01" value="<?= $escape($descuento['interes_activo'] ?? 0) ?>">
                        </div>
                        
                        <div class="form-group">
                            <label for="interes_deducido">Interés Deducido</label>
                            <input type="number" id="interes_deducido" name="interes_deducido" step="0.01" value="<?= $escape($descuento['interes_deducido'] ?? 0) ?>">
                        </div>
                        
                        <div class="form-group">
                            <label for="otro1">Otro 1</label>
                            <input type="number" id="otro1" name="otro1" step="0.01" value="<?= $escape($descuento['otro1'] ?? 0) ?>">
                        </div>
                        
                        <div class="form-group">
                            <label for="otro2">Otro 2</label>
                            <input type="number" id="otro2" name="otro2" step="0.01" value="<?= $escape($descuento['otro2'] ?? 0) ?>">
                        </div>
                    </div>
                    
                    <div style="display: flex; gap: 12px; margin-top: 24px;">
                        <button type="submit" class="btn btn-primary">Guardar</button>
                        <a href="index.php?module=socios&action=descuentos&client=<?= rawurlencode($clienteCodigo) ?>" class="btn btn-secondary">Cancelar</a>
                    </div>
                </form>
            </section>
        </main>
    </div>
</body>
</html>
