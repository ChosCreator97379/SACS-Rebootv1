<?php

declare(strict_types=1);

require dirname(__DIR__) . '/../config/bootstrap.php';

use Sacs\Core\Database;
use Sacs\Services\SociosServicio;
use Sacs\Security\Auth;

$auth = new Auth(new Sacs\Repositories\UserRepository());
if (!$auth->check()) {
    header('Location: index.php');
    exit;
}

$user = $auth->user();
$sociosServicio = new SociosServicio();

$clienteCodigo = trim((string) ($_GET['client'] ?? ''));

if ($clienteCodigo === '') {
    http_response_code(400);
    $error = 'Cliente no especificado';
    require dirname(__DIR__) . '/views/home.php';
    exit;
}

// Obtener datos del socio
$socio = $sociosServicio->obtenerSocioDetalle($clienteCodigo);
if ($socio === null) {
    http_response_code(404);
    $error = 'Socio no encontrado';
    require dirname(__DIR__) . '/views/home.php';
    exit;
}

// Obtener estado actual del socio
$estadoSocio = $sociosServicio->obtenerEstadoSocio($socio['F01_ID_PERSONA']);

// Verificar si tiene préstamos activos
$tienePrestamosActivos = $sociosServicio->tienePrestamosActivos($clienteCodigo);

$displayName = htmlspecialchars($user['username'], ENT_QUOTES, 'UTF-8');
$profile = htmlspecialchars((string) $user['profile'], ENT_QUOTES, 'UTF-8');
$userId = htmlspecialchars((string) $user['id'], ENT_QUOTES, 'UTF-8');

$escape = static fn (mixed $value): string => htmlspecialchars((string) ($value ?? ''), ENT_QUOTES, 'UTF-8');
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
            --line: #dce4e1;
            --shadow: 0 18px 45px rgba(20, 43, 58, .08);
            --red: #c33;
            --green: #1d7a78;
            --yellow: #d69e47;
        }

        * { box-sizing: border-box; }
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
        .panel { background: var(--panel); border-radius: 12px; padding: 24px; box-shadow: var(--shadow); margin-bottom: 24px; }
        .panel h2 { margin: 0 0 20px; font-size: 18px; font-weight: 400; }
        .summary { display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 16px; margin-bottom: 24px; }
        .summary div { background: var(--panel); padding: 16px; border-radius: 8px; }
        .summary .label { font-size: 11px; color: var(--muted); text-transform: uppercase; letter-spacing: .08em; margin-bottom: 4px; }
        .summary .value { font-size: 18px; font-weight: 600; }
        .summary .name { font-size: 14px; }
        .badge { padding: 4px 8px; border-radius: 4px; font-size: 11px; font-weight: 600; }
        .badge-success { background: #e6f4ea; color: var(--green); }
        .badge-warning { background: #fff8e6; color: var(--yellow); }
        .badge-danger { background: #fee; color: var(--red); }
        .form-row { display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 16px; margin-bottom: 16px; }
        .form-group { display: flex; flex-direction: column; }
        .form-group label { font-size: 12px; font-weight: 600; color: var(--muted); margin-bottom: 6px; }
        .form-group select, .form-group input { padding: 10px 12px; border: 1px solid var(--line); border-radius: 6px; font-size: 13px; font-family: inherit; }
        .form-group select:focus, .form-group input:focus { outline: none; border-color: var(--teal); }
        .form-actions { display: flex; gap: 12px; margin-top: 24px; }
        .btn { padding: 12px 24px; border: none; border-radius: 6px; font-size: 13px; font-weight: 600; cursor: pointer; transition: background .15s; text-decoration: none; display: inline-block; }
        .btn-primary { background: var(--teal); color: white; }
        .btn-primary:hover { background: #166664; }
        .btn-danger { background: var(--red); color: white; }
        .btn-danger:hover { background: #a33; }
        .btn-secondary { background: var(--paper); color: var(--ink); }
        .btn-secondary:hover { background: #e8ebe9; }
        .btn-blue { background: #3b82f6; color: white; }
        .btn-blue:hover { background: #2563eb; }
        .btn-green { background: #10b981; color: white; }
        .btn-green:hover { background: #059669; }
        .btn-purple { background: #8b5cf6; color: white; }
        .btn-purple:hover { background: #7c3aed; }
        .btn-orange { background: #f59e0b; color: white; }
        .btn-orange:hover { background: #d97706; }
        .btn-gray { background: #6b7280; color: white; }
        .btn-gray:hover { background: #4b5563; }
        .warning { background: #fff8e6; border-left: 4px solid var(--yellow); padding: 12px 16px; margin-bottom: 16px; border-radius: 4px; }
        .warning p { margin: 0; font-size: 13px; color: #8b6914; }
        .state-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 16px; }
        .state-card { background: var(--panel); padding: 16px; border-radius: 8px; border: 1px solid var(--line); }
        .state-card h3 { margin: 0 0 12px; font-size: 14px; font-weight: 600; }
        .state-card .option { display: flex; align-items: center; gap: 8px; margin-bottom: 8px; }
        .state-card .option input { margin: 0; }
        .state-card .option label { font-size: 13px; cursor: pointer; }
    </style>
</head>
<body>
    <div class="shell">
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
                <a href="index.php" class="nav-link">Dashboard</a>
                <a href="index.php?module=socios&action=listado" class="nav-link">Listado de Socios</a>
                <a href="index.php?module=socios&action=registro" class="nav-link">Registro de Socio</a>
                <a href="index.php?module=socios&action=info&client=<?= rawurlencode($clienteCodigo) ?>" class="nav-link">Información Personal</a>
                <a href="index.php?module=socios&action=observaciones&client=<?= rawurlencode($clienteCodigo) ?>" class="nav-link">Observaciones</a>
                <a href="index.php?module=socios&action=carta&client=<?= rawurlencode($clienteCodigo) ?>" class="nav-link">Carta</a>
                <a href="index.php?module=socios&action=sobregiro&client=<?= rawurlencode($clienteCodigo) ?>" class="nav-link">Sobregiro</a>
                <a href="index.php?module=socios&action=aportes_completos&client=<?= rawurlencode($clienteCodigo) ?>" class="nav-link">Aportes Completos</a>
                <a href="index.php?module=socios&action=aportes_fonbes&client=<?= rawurlencode($clienteCodigo) ?>" class="nav-link">Aportes FONBES</a>
                <a href="index.php?module=socios&action=aportes_coopam&client=<?= rawurlencode($clienteCodigo) ?>" class="nav-link">Aportes COOPAM</a>
                <a href="index.php?module=socios&action=gestion_estado&client=<?= rawurlencode($clienteCodigo) ?>" class="nav-link active">Gestión de Estado</a>
                <a href="index.php?module=socios&action=beneficios&client=<?= rawurlencode($clienteCodigo) ?>" class="nav-link">Beneficios</a>
                <a href="index.php?module=socios&action=familiares&client=<?= rawurlencode($clienteCodigo) ?>" class="nav-link">Familiares</a>
                <a href="index.php?module=socios&action=rifas&client=<?= rawurlencode($clienteCodigo) ?>" class="nav-link">Rifas</a>
                <a href="index.php?module=socios&action=multas&client=<?= rawurlencode($clienteCodigo) ?>" class="nav-link">Multas</a>
                <a href="index.php?module=socios&action=cheques" class="nav-link">Cheques</a>
                <div class="nav-label">Auditoría</div>
                <a href="index.php?module=socios&action=auditoria&client=<?= rawurlencode($clienteCodigo) ?>" class="nav-link">Auditoría Individual</a>
                <a href="index.php?module=socios&action=auditoria_general" class="nav-link">Auditoría General</a>
            </nav>
        </aside>

        <main class="main">
            <div class="header">
                <h1>Gestión de Estado</h1>
                <div class="user">
                    <div class="user-avatar"><?= $displayName[0] ?></div>
                    <div>
                        <div class="user-name"><?= $displayName ?></div>
                        <div class="user-profile"><?= $profile ?></div>
                    </div>
                </div>
            </div>

            <section class="summary">
                <div><span class="label">Socio</span><div class="name"><?= $escape($socio['F01_APE_PAT'] . ' ' . $socio['F01_APE_MAT'] . ', ' . $socio['F01_NOMB_PRI'] . ' ' . $socio['F01_NOMB_SEG']) ?></div></div>
                <div><span class="label">Código</span><div class="value"><?= $escape($clienteCodigo) ?></div></div>
                <div><span class="label">Estado Actual</span><div>
                    <?php
                    $badgeClass = 'badge-success';
                    if ($estadoSocio['estado'] == 1) {
                        $badgeClass = 'badge-success';
                    } elseif ($estadoSocio['estado'] == 8) {
                        $badgeClass = 'badge-danger';
                    } else {
                        $badgeClass = 'badge-warning';
                    }
                    ?>
                    <span class="badge <?= $badgeClass ?>"><?= $escape($estadoSocio['descripcion']) ?></span>
                </div></div>
                <div><span class="label">Visibilidad</span><div class="value"><?= $estadoSocio['visible'] == 1 ? 'Visible' : 'Oculto' ?></div></div>
            </section>

            <?php if ($tienePrestamosActivos): ?>
                <div class="warning">
                    <p><strong>⚠️ Advertencia:</strong> Este socio tiene préstamos activos. Al eliminar el socio, los préstamos no se cancelarán automáticamente.</p>
                </div>
            <?php endif; ?>

            <?php if ($estadoSocio['estado'] == 1): ?>
                <section class="panel">
                    <h2>Eliminar Socio</h2>
                    <p style="margin-bottom: 16px; color: var(--muted);">Seleccione el tipo de eliminación para el socio:</p>
                    
                    <form action="index.php" method="POST">
                        <input type="hidden" name="csrf_token" value="<?= $escape($_SESSION['csrf_token']) ?>">
                        <input type="hidden" name="module" value="socios">
                        <input type="hidden" name="action" value="eliminar_socio">
                        <input type="hidden" name="id_persona" value="<?= $escape($socio['F01_ID_PERSONA']) ?>">
                        <input type="hidden" name="cliente" value="<?= $escape($clienteCodigo) ?>">
                        
                        <div class="state-grid">
                            <div class="state-card">
                                <h3>Tipos de Eliminación</h3>
                                <div class="option">
                                    <input type="radio" id="tipo_normal" name="tipo" value="normal" required>
                                    <label for="tipo_normal">Normal (estado 0)</label>
                                </div>
                                <div class="option">
                                    <input type="radio" id="tipo_falle" name="tipo" value="falle">
                                    <label for="tipo_falle">Fallecimiento (estado 8)</label>
                                </div>
                                <div class="option">
                                    <input type="radio" id="tipo_deuda" name="tipo" value="deuda">
                                    <label for="tipo_deuda">Deuda (estado 5)</label>
                                </div>
                                <div class="option">
                                    <input type="radio" id="tipo_castigo" name="tipo" value="castigo">
                                    <label for="tipo_castigo">Castigo (estado 7)</label>
                                </div>
                                <div class="option">
                                    <input type="radio" id="tipo_final" name="tipo" value="final">
                                    <label for="tipo_final">Final (estado 2)</label>
                                </div>
                            </div>
                            
                            <div class="state-card">
                                <h3>Eliminación Parcial</h3>
                                <div class="option">
                                    <input type="radio" id="tipo_parcial" name="tipo" value="parcial">
                                    <label for="tipo_parcial">Parcial (estado 9)</label>
                                </div>
                                <div class="form-group" style="margin-top: 12px;">
                                    <label for="fecha_ultimo_aporte">Fecha Último Aporte</label>
                                    <input type="date" id="fecha_ultimo_aporte" name="fecha_ultimo_aporte">
                                </div>
                            </div>
                        </div>

                        <div class="form-actions">
                            <button type="submit" class="btn btn-danger" onclick="return confirm('¿Está seguro de eliminar este socio? Esta acción no se puede deshacer.')">🗑️ Eliminar Socio</button>
                            <a href="index.php?module=socios&action=info&client=<?= rawurlencode($clienteCodigo) ?>" class="btn btn-gray">🔙 Cancelar</a>
                        </div>
                    </form>
                </section>
            <?php else: ?>
                <section class="panel">
                    <h2>Activar Socio</h2>
                    <p style="margin-bottom: 16px; color: var(--muted);">El socio está actualmente eliminado. Puede reactivarlo:</p>
                    
                    <form action="index.php" method="POST">
                        <input type="hidden" name="csrf_token" value="<?= $escape($_SESSION['csrf_token']) ?>">
                        <input type="hidden" name="module" value="socios">
                        <input type="hidden" name="action" value="activar_socio">
                        <input type="hidden" name="id_persona" value="<?= $escape($socio['F01_ID_PERSONA']) ?>">
                        <input type="hidden" name="cliente" value="<?= $escape($clienteCodigo) ?>">
                        
                        <div class="form-actions">
                            <button type="submit" class="btn btn-green" onclick="return confirm('¿Está seguro de activar este socio?')">✅ Activar Socio</button>
                            <a href="index.php?module=socios&action=info&client=<?= rawurlencode($clienteCodigo) ?>" class="btn btn-gray">🔙 Cancelar</a>
                        </div>
                    </form>
                </section>
            <?php endif; ?>

            <section class="panel">
                <h2>Ocultar/Mostrar Socio</h2>
                <p style="margin-bottom: 16px; color: var(--muted);">Controlar la visibilidad del socio en los listados:</p>
                
                <form action="index.php" method="POST">
                    <input type="hidden" name="csrf_token" value="<?= $escape($_SESSION['csrf_token']) ?>">
                    <input type="hidden" name="module" value="socios">
                    <input type="hidden" name="action" value="ocultar_socio">
                    <input type="hidden" name="id_persona" value="<?= $escape($socio['F01_ID_PERSONA']) ?>">
                    <input type="hidden" name="cliente" value="<?= $escape($clienteCodigo) ?>">
                    
                    <div class="form-row">
                        <div class="form-group">
                            <label for="visible">Visibilidad</label>
                            <select id="visible" name="visible">
                                <option value="1" <?= $estadoSocio['visible'] == 1 ? 'selected' : '' ?>>Visible</option>
                                <option value="0" <?= $estadoSocio['visible'] == 0 ? 'selected' : '' ?>>Oculto</option>
                            </select>
                        </div>
                    </div>
                    
                    <div class="form-actions">
                        <button type="submit" class="btn btn-blue">👁️ Actualizar Visibilidad</button>
                        <a href="index.php?module=socios&action=info&client=<?= rawurlencode($clienteCodigo) ?>" class="btn btn-gray">🔙 Cancelar</a>
                    </div>
                </form>
            </section>
        </main>
    </div>
</body>
</html>
