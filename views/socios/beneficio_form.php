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
$codigo = trim((string) ($_GET['codigo'] ?? ''));
$modo = $codigo !== '' ? 'editar' : 'nuevo';

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

// Obtener listas de referencia
$tiposBeneficio = $sociosServicio->obtenerTiposBeneficio();
$tiposParentesco = $sociosServicio->obtenerTiposParentesco();

// Si es edición, cargar datos del beneficio
$beneficio = [];
if ($modo === 'editar') {
    $beneficio = $sociosServicio->obtenerBeneficio((int) $codigo);
    if ($beneficio === null) {
        http_response_code(404);
        $error = 'Beneficio no encontrado';
        require dirname(__DIR__) . '/views/home.php';
        exit;
    }
}

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
    <title><?= $modo === 'nuevo' ? 'Nuevo Beneficio' : 'Editar Beneficio' ?> | SACS Nuevo</title>
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
        .panel h3 { margin: 0 0 16px; font-size: 14px; font-weight: 600; color: var(--muted); text-transform: uppercase; letter-spacing: .08em; }
        .form-row { display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 16px; margin-bottom: 16px; }
        .form-group { display: flex; flex-direction: column; }
        .form-group label { font-size: 12px; font-weight: 600; color: var(--muted); margin-bottom: 6px; }
        .form-group input, .form-group select, .form-group textarea { padding: 10px 12px; border: 1px solid var(--line); border-radius: 6px; font-size: 13px; font-family: inherit; }
        .form-group input:focus, .form-group select:focus, .form-group textarea:focus { outline: none; border-color: var(--teal); }
        .form-group textarea { resize: vertical; min-height: 80px; }
        .form-actions { display: flex; gap: 12px; margin-top: 24px; }
        .btn { padding: 12px 24px; border: none; border-radius: 6px; font-size: 13px; font-weight: 600; cursor: pointer; transition: background .15s; text-decoration: none; display: inline-block; }
        .btn-primary { background: var(--teal); color: white; }
        .btn-primary:hover { background: #166664; }
        .btn-secondary { background: var(--paper); color: var(--ink); }
        .btn-secondary:hover { background: #e8ebe9; }
        .btn-green { background: #10b981; color: white; }
        .btn-green:hover { background: #059669; }
        .btn-gray { background: #6b7280; color: white; }
        .btn-gray:hover { background: #4b5563; }
        .checkbox-group { display: flex; align-items: center; gap: 8px; margin-top: 8px; }
        .checkbox-group input { margin: 0; }
        .checkbox-group label { font-size: 13px; cursor: pointer; }
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
                <a href="index.php?module=socios&action=aportes_fonbes&client=<?= rawurlencode($clienteCodigo) ?>" class="nav-link">Aportes FONBES</a>
                <a href="index.php?module=socios&action=aportes_coopam&client=<?= rawurlencode($clienteCodigo) ?>" class="nav-link">Aportes COOPAM</a>
                <a href="index.php?module=socios&action=gestion_estado&client=<?= rawurlencode($clienteCodigo) ?>" class="nav-link">Gestión de Estado</a>
                <a href="index.php?module=socios&action=beneficios&client=<?= rawurlencode($clienteCodigo) ?>" class="nav-link active">Beneficios</a>
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
                <h1><?= $modo === 'nuevo' ? 'Nuevo Beneficio' : 'Editar Beneficio' ?></h1>
                <div class="user">
                    <div class="user-avatar"><?= $displayName[0] ?></div>
                    <div>
                        <div class="user-name"><?= $displayName ?></div>
                        <div class="user-profile"><?= $profile ?></div>
                    </div>
                </div>
            </div>

            <section class="panel">
                <form action="index.php" method="POST">
                    <input type="hidden" name="csrf_token" value="<?= $escape($_SESSION['csrf_token']) ?>">
                    <input type="hidden" name="module" value="socios">
                    <input type="hidden" name="action" value="beneficio_guardar">
                    <input type="hidden" name="modo" value="<?= $escape($modo) ?>">
                    <input type="hidden" name="cliente" value="<?= $escape($clienteCodigo) ?>">
                    <?php if ($modo === 'editar'): ?>
                        <input type="hidden" name="codigo" value="<?= $escape($codigo) ?>">
                    <?php endif; ?>

                    <h3>Información del Beneficiario</h3>
                    <div class="form-row">
                        <div class="form-group">
                            <label for="nombres">Nombres Completos *</label>
                            <input type="text" id="nombres" name="nombres" value="<?= $escape($beneficio['nombres'] ?? '') ?>" required>
                        </div>
                        <div class="form-group">
                            <label for="beneficiario">Nombre del Beneficiario *</label>
                            <input type="text" id="beneficiario" name="beneficiario" value="<?= $escape($beneficio['beneficiario'] ?? '') ?>" required>
                        </div>
                        <div class="form-group">
                            <label for="dni1">DNI *</label>
                            <input type="text" id="dni1" name="dni1" value="<?= $escape($beneficio['dni1'] ?? '') ?>" required>
                        </div>
                    </div>
                    <div class="form-row">
                        <div class="form-group">
                            <label for="direccion1">Dirección *</label>
                            <input type="text" id="direccion1" name="direccion1" value="<?= $escape($beneficio['direccion1'] ?? '') ?>" required>
                        </div>
                    </div>

                    <h3>Información Adicional</h3>
                    <div class="form-row">
                        <div class="form-group">
                            <label for="dni2">DNI Adicional</label>
                            <input type="text" id="dni2" name="dni2" value="<?= $escape($beneficio['dni2'] ?? '') ?>">
                        </div>
                        <div class="form-group">
                            <label for="direccion2">Dirección Adicional</label>
                            <input type="text" id="direccion2" name="direccion2" value="<?= $escape($beneficio['direccion2'] ?? '') ?>">
                        </div>
                    </div>

                    <h3>Parentesco</h3>
                    <div class="form-row">
                        <div class="form-group">
                            <label for="tipo_pa1">Tipo de Parentesco 1</label>
                            <select id="tipo_pa1" name="tipo_pa1">
                                <option value="">Seleccione...</option>
                                <?php foreach ($tiposParentesco as $key => $value): ?>
                                    <option value="<?= $escape($key) ?>" <?= ($beneficio['tipo_pa1'] ?? '') === $key ? 'selected' : '' ?>><?= $escape($value) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="tipo_pa2">Tipo de Parentesco 2</label>
                            <select id="tipo_pa2" name="tipo_pa2">
                                <option value="">Seleccione...</option>
                                <?php foreach ($tiposParentesco as $key => $value): ?>
                                    <option value="<?= $escape($key) ?>" <?= ($beneficio['tipo_pa2'] ?? '') === $key ? 'selected' : '' ?>><?= $escape($value) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>

                    <h3>Datos del Beneficio</h3>
                    <div class="form-row">
                        <div class="form-group">
                            <label for="tipo">Tipo de Beneficio *</label>
                            <select id="tipo" name="tipo" required>
                                <option value="">Seleccione...</option>
                                <?php foreach ($tiposBeneficio as $key => $value): ?>
                                    <option value="<?= $escape($key) ?>" <?= ($beneficio['tipo'] ?? '') === $key ? 'selected' : '' ?>><?= $escape($value) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="importe">Importe *</label>
                            <input type="number" id="importe" name="importe" step="0.01" value="<?= $escape($beneficio['importe'] ?? '') ?>" required>
                        </div>
                    </div>

                    <h3>Datos de Fallecimiento (Opcional)</h3>
                    <div class="form-row">
                        <div class="form-group">
                            <label for="fecha_falle">Fecha de Fallecimiento</label>
                            <input type="date" id="fecha_falle" name="fecha_falle" value="<?= $escape($beneficio['fecha_falle'] ?? '') ?>">
                        </div>
                        <div class="form-group">
                            <label for="lugar_falle">Lugar de Fallecimiento</label>
                            <input type="text" id="lugar_falle" name="lugar_falle" value="<?= $escape($beneficio['lugar_falle'] ?? '') ?>">
                        </div>
                    </div>

                    <h3>Otros Datos</h3>
                    <div class="form-row">
                        <div class="form-group">
                            <label for="tramite">Trámite</label>
                            <input type="text" id="tramite" name="tramite" value="<?= $escape($beneficio['tramite'] ?? '') ?>">
                        </div>
                        <div class="form-group">
                            <label for="vigencia">Vigencia</label>
                            <input type="text" id="vigencia" name="vigencia" value="<?= $escape($beneficio['vigencia'] ?? '') ?>">
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="obs">Observaciones</label>
                        <textarea id="obs" name="obs"><?= $escape($beneficio['obs'] ?? '') ?></textarea>
                    </div>

                    <div class="form-row">
                        <div class="form-group">
                            <div class="checkbox-group">
                                <input type="checkbox" id="acredita" name="acredita" value="1" <?= ($beneficio['acredita'] ?? 0) == 1 ? 'checked' : '' ?>>
                                <label for="acredita">Acredita</label>
                            </div>
                        </div>
                        <div class="form-group">
                            <div class="checkbox-group">
                                <input type="checkbox" id="aporta" name="aporta" value="1" <?= ($beneficio['aporta'] ?? 0) == 1 ? 'checked' : '' ?>>
                                <label for="aporta">Aporta</label>
                            </div>
                        </div>
                    </div>

                    <div class="form-actions">
                        <button type="submit" class="btn btn-green">💎 Guardar Beneficio</button>
                        <a href="index.php?module=socios&action=beneficios&client=<?= rawurlencode($clienteCodigo) ?>" class="btn btn-gray">🔙 Cancelar</a>
                    </div>
                </form>
            </section>
        </main>
    </div>
</body>
</html>
