<?php

declare(strict_types=1);

require dirname(__DIR__) . '/../config/bootstrap.php';

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

// Si es edición, obtener datos de la multa
$multa = null;
if ($modo === 'editar') {
    $multa = $sociosServicio->obtenerMulta((int) $codigo);
    if ($multa === null) {
        http_response_code(404);
        $error = 'Multa no encontrada';
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
    <title><?= $modo === 'nuevo' ? 'Nueva Multa' : 'Editar Multa' ?> | SACS Nuevo</title>
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
        .form-row { display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 16px; margin-bottom: 16px; }
        .form-group { display: flex; flex-direction: column; }
        .form-group label { font-size: 12px; font-weight: 600; color: var(--muted); margin-bottom: 6px; }
        .form-group input, .form-group select { padding: 10px 12px; border: 1px solid var(--line); border-radius: 6px; font-size: 13px; font-family: inherit; }
        .form-group input:focus, .form-group select:focus { outline: none; border-color: var(--teal); }
        .form-actions { display: flex; gap: 12px; margin-top: 24px; }
        .btn { padding: 12px 24px; border: none; border-radius: 6px; font-size: 13px; font-weight: 600; cursor: pointer; transition: background .15s; text-decoration: none; display: inline-block; }
        .btn-primary { background: var(--teal); color: white; }
        .btn-primary:hover { background: #166664; }
        .btn-secondary { background: var(--paper); color: var(--ink); }
        .btn-secondary:hover { background: #e8ebe9; }
        .btn-gray { background: #6b7280; color: white; }
        .btn-gray:hover { background: #4b5563; }
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
                <a href="index.php?module=socios&action=beneficios&client=<?= rawurlencode($clienteCodigo) ?>" class="nav-link">Beneficios</a>
                <a href="index.php?module=socios&action=familiares&client=<?= rawurlencode($clienteCodigo) ?>" class="nav-link">Familiares</a>
                <a href="index.php?module=socios&action=rifas&client=<?= rawurlencode($clienteCodigo) ?>" class="nav-link">Rifas</a>
                <a href="index.php?module=socios&action=multas&client=<?= rawurlencode($clienteCodigo) ?>" class="nav-link active">Multas</a>
                <a href="index.php?module=socios&action=cheques" class="nav-link">Cheques</a>
                <div class="nav-label">Auditoría</div>
                <a href="index.php?module=socios&action=auditoria&client=<?= rawurlencode($clienteCodigo) ?>" class="nav-link">Auditoría Individual</a>
                <a href="index.php?module=socios&action=auditoria_general" class="nav-link">Auditoría General</a>
            </nav>
        </aside>

        <main class="main">
            <div class="header">
                <h1><?= $modo === 'nuevo' ? 'Nueva Multa' : 'Editar Multa' ?></h1>
                <div class="user">
                    <div class="user-avatar"><?= $displayName[0] ?></div>
                    <div>
                        <div class="user-name"><?= $displayName ?></div>
                        <div class="user-profile"><?= $profile ?></div>
                    </div>
                </div>
            </div>

            <section class="panel">
                <h2><?= $modo === 'nuevo' ? 'Registrar Nueva Multa' : 'Editar Multa' ?></h2>
                <form method="POST">
                    <input type="hidden" name="csrf_token" value="<?= $escape($_SESSION['csrf_token']) ?>">
                    <input type="hidden" name="module" value="socios">
                    <input type="hidden" name="action" value="multa_guardar">
                    <input type="hidden" name="modo" value="<?= $escape($modo) ?>">
                    <input type="hidden" name="client" value="<?= $escape($clienteCodigo) ?>">
                    <?php if ($modo === 'editar'): ?>
                        <input type="hidden" name="codigo" value="<?= $escape($codigo) ?>">
                    <?php endif; ?>
                    
                    <div class="form-row">
                        <div class="form-group">
                            <label for="periodo">Periodo</label>
                            <input type="date" id="periodo" name="periodo" value="<?= $escape($multa['f63_periodo'] ?? '') ?>" required>
                        </div>
                        <div class="form-group">
                            <label for="multap">Multa Propuesta</label>
                            <input type="number" id="multap" name="multap" step="0.01" value="<?= $escape($multa['f63_multap'] ?? '0') ?>">
                        </div>
                        <div class="form-group">
                            <label for="multad">Multa Definitiva</label>
                            <input type="number" id="multad" name="multad" step="0.01" value="<?= $escape($multa['f63_multad'] ?? '0') ?>">
                        </div>
                    </div>
                    
                    <div class="form-row">
                        <div class="form-group">
                            <label for="multar">Multa Sobregiro</label>
                            <input type="number" id="multar" name="multar" step="0.01" value="<?= $escape($multa['f63_multar'] ?? '0') ?>">
                        </div>
                        <div class="form-group">
                            <label for="fechaPago">Fecha de Pago</label>
                            <input type="date" id="fechaPago" name="fechaPago" value="<?= $escape($multa['f63_fechaPago'] ?? '') ?>">
                        </div>
                        <div class="form-group">
                            <label for="formaPago">Forma de Pago</label>
                            <input type="text" id="formaPago" name="formaPago" value="<?= $escape($multa['f63_formaPago'] ?? '') ?>">
                        </div>
                    </div>
                    
                    <div class="form-actions">
                        <button type="submit" class="btn btn-primary">💾 Guardar</button>
                        <a href="index.php?module=socios&action=multas&client=<?= rawurlencode($clienteCodigo) ?>" class="btn btn-gray">🔙 Cancelar</a>
                    </div>
                </form>
            </section>
        </main>
    </div>
</body>
</html>
