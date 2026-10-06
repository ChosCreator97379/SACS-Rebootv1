<?php
$escape = static fn (mixed $value): string => htmlspecialchars((string) ($value ?? ''), ENT_QUOTES, 'UTF-8');

$sociosServicio = new \Sacs\Services\SociosServicio();
$clienteCodigo = $_GET['client'] ?? '';
$usuario = $_SESSION['usuario'] ?? 'SISTEMA';

// Obtener información del socio
$socio = $sociosServicio->obtenerSocioPorCliente($clienteCodigo);
if (!$socio) {
    header('Location: index.php?module=socios&action=listado&error=Socio no encontrado');
    exit;
}

$codigoPersona = $socio['F01_ID_PERSONA'];

// Obtener observación actual
$observacionActual = $sociosServicio->obtenerObservacion($codigoPersona);

// Obtener historial de observaciones
$pagina = max(1, (int) ($_GET['pagina'] ?? 1));
$porPagina = 20;
$historial = $sociosServicio->obtenerHistorialObservaciones($codigoPersona, $pagina, $porPagina);
$totalHistorial = $sociosServicio->contarHistorialObservaciones($codigoPersona);
$totalPaginas = max(1, ceil($totalHistorial / $porPagina));

$displayName = substr($usuario, 0, 2);
$profile = 'Administrador';
?>
<!doctype html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Observaciones | SACS Nuevo</title>
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
        .form-group textarea {
            width: 100%;
            padding: 10px 12px;
            border: 1px solid var(--border);
            border-radius: 6px;
            font-size: 14px;
            font-family: inherit;
        }

        .form-group textarea {
            min-height: 100px;
            resize: vertical;
        }

        .form-group input:focus,
        .form-group textarea:focus {
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

        .empty {
            text-align: center;
            padding: 40px;
            color: var(--muted);
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
    </style>
</head>
<body>
    <div class="app">
        <aside class="sidebar">
            <a href="index.php" class="brand">
                <div class="brand-mark">S</div>
                <div>
                    <div class="brand-name">SACS</div>
                    <div class="brand-note">Sistema de Administración</div>
                </div>
            </a>
            <div class="nav-label">Socios</div>
            <nav class="nav">
                <a href="index.php?module=socios&action=listado" class="nav-link">Listado de Socios</a>
                <a href="index.php?module=socios&action=registro" class="nav-link">Registro de Socio</a>
                <a href="index.php?module=socios&action=info&client=<?= rawurlencode($clienteCodigo) ?>" class="nav-link">Información Personal</a>
                <a href="index.php?module=socios&action=observaciones&client=<?= rawurlencode($clienteCodigo) ?>" class="nav-link active">Observaciones</a>
                <a href="index.php?module=socios&action=carta&client=<?= rawurlencode($clienteCodigo) ?>" class="nav-link">Carta</a>
                <a href="index.php?module=socios&action=sobregiro&client=<?= rawurlencode($clienteCodigo) ?>" class="nav-link">Sobregiro</a>
                <a href="index.php?module=socios&action=aportes_completos&client=<?= rawurlencode($clienteCodigo) ?>" class="nav-link">Aportes Completos</a>
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
                <h1>Observaciones</h1>
                <div class="user">
                    <div class="user-avatar"><?= $escape($displayName) ?></div>
                    <div>
                        <div class="user-name"><?= $escape($usuario) ?></div>
                        <div class="user-profile"><?= $escape($profile) ?></div>
                    </div>
                </div>
            </div>
            <p class="intro">Gestione las observaciones del socio: <?= $escape($socio['F01_APE_PAT'] . ' ' . $socio['F01_APE_MAT'] . ', ' . $socio['F01_NOMB_PRI']) ?></p>

            <?php if (isset($_GET['success'])): ?>
                <div class="success"><?= $escape($_GET['success']) ?></div>
            <?php endif; ?>

            <?php if (isset($_GET['error'])): ?>
                <div class="error"><?= $escape($_GET['error']) ?></div>
            <?php endif; ?>

            <section class="panel">
                <h2>Actualizar Observación</h2>
                <form method="post" action="index.php">
                    <input type="hidden" name="module" value="socios">
                    <input type="hidden" name="action" value="observacion_guardar">
                    <input type="hidden" name="client" value="<?= $escape($clienteCodigo) ?>">
                    <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">
                    
                    <div class="form-group">
                        <label for="titulo">Título</label>
                        <input type="text" id="titulo" name="titulo" value="<?= $escape($observacionActual['titulo'] ?? '') ?>" maxlength="100" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="observacion">Observación</label>
                        <textarea id="observacion" name="observacion" maxlength="500" required><?= $escape($observacionActual['observacion'] ?? '') ?></textarea>
                    </div>
                    
                    <div style="display: flex; gap: 12px;">
                        <button type="submit" class="btn btn-primary">Guardar</button>
                        <a href="index.php?module=socios&action=info&client=<?= rawurlencode($clienteCodigo) ?>" class="btn btn-secondary">Cancelar</a>
                    </div>
                </form>
            </section>

            <section class="panel">
                <h2>Historial de Observaciones</h2>
                <?php if (empty($historial)): ?>
                    <div class="empty">No hay observaciones en el historial</div>
                <?php else: ?>
                    <table class="table">
                        <thead>
                            <tr>
                                <th>Título</th>
                                <th>Detalle</th>
                                <th>Usuario</th>
                                <th>Fecha</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($historial as $obs): ?>
                                <tr>
                                    <td><?= $escape($obs['titulo']) ?></td>
                                    <td><?= $escape($obs['detalle']) ?></td>
                                    <td><?= $escape($obs['usuario']) ?></td>
                                    <td><?= date('d/m/Y H:i', strtotime($obs['fecha'])) ?></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                    
                    <?php if ($totalPaginas > 1): ?>
                        <div class="pagination">
                            <?php if ($pagina > 1): ?>
                                <a href="index.php?module=socios&action=observaciones&client=<?= rawurlencode($clienteCodigo) ?>&pagina=<?= $pagina - 1 ?>">Anterior</a>
                            <?php endif; ?>
                            <span>Página <?= $pagina ?> de <?= $totalPaginas ?> (<?= $totalHistorial ?> registros)</span>
                            <?php if ($pagina < $totalPaginas): ?>
                                <a href="index.php?module=socios&action=observaciones&client=<?= rawurlencode($clienteCodigo) ?>&pagina=<?= $pagina + 1 ?>">Siguiente</a>
                            <?php endif; ?>
                        </div>
                    <?php endif; ?>
                <?php endif; ?>
            </section>
        </main>
    </div>
</body>
</html>
