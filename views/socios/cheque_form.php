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

$codigo = trim((string) ($_GET['codigo'] ?? ''));
$modo = $codigo !== '' ? 'editar' : 'nuevo';

// Si es edición, obtener datos del cheque
$cheque = null;
if ($modo === 'editar') {
    $cheque = $sociosServicio->obtenerCheque((int) $codigo);
    if ($cheque === null) {
        http_response_code(404);
        $error = 'Cheque no encontrado';
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
    <title><?= $modo === 'nuevo' ? 'Nuevo Cheque' : 'Editar Cheque' ?> | SACS Nuevo</title>
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
                    <div class="brand-note">gestion social</div>
                </div>
            </a>
            <div class="nav-label">Socios</div>
            <nav class="nav">
                <a href="index.php" class="nav-link">Dashboard</a>
                <a href="index.php?module=socios&action=listado" class="nav-link">Listado de Socios</a>
                <a href="index.php?module=socios&action=registro" class="nav-link">Registro de Socio</a>
                <a href="index.php?module=socios&action=cheques" class="nav-link active">Cheques</a>
                <div class="nav-label">Auditoría</div>
                <a href="index.php?module=socios&action=auditoria_general" class="nav-link">Auditoría General</a>
            </nav>
        </aside>

        <main class="main">
            <div class="header">
                <h1><?= $modo === 'nuevo' ? 'Nuevo Cheque' : 'Editar Cheque' ?></h1>
                <div class="user">
                    <div class="user-avatar"><?= $displayName[0] ?></div>
                    <div>
                        <div class="user-name"><?= $displayName ?></div>
                        <div class="user-profile"><?= $profile ?></div>
                    </div>
                </div>
            </div>

            <section class="panel">
                <h2><?= $modo === 'nuevo' ? 'Registrar Nuevo Cheque' : 'Editar Cheque' ?></h2>
                <form method="POST">
                    <input type="hidden" name="csrf_token" value="<?= $escape($_SESSION['csrf_token']) ?>">
                    <input type="hidden" name="module" value="socios">
                    <input type="hidden" name="action" value="cheque_guardar">
                    <input type="hidden" name="modo" value="<?= $escape($modo) ?>">
                    <?php if ($modo === 'editar'): ?>
                        <input type="hidden" name="codigo" value="<?= $escape($codigo) ?>">
                    <?php endif; ?>
                    
                    <div class="form-row">
                        <div class="form-group">
                            <label for="nombre">Nombre del Cheque</label>
                            <input type="text" id="nombre" name="nombre" value="<?= $escape($cheque['f23_nombre'] ?? '') ?>" required>
                        </div>
                        <div class="form-group">
                            <label for="monto">Monto</label>
                            <input type="number" id="monto" name="monto" step="0.01" value="<?= $escape($cheque['f23_monto'] ?? '') ?>" required oninput="actualizarMontoLetras()">
                        </div>
                        <div class="form-group">
                            <label for="montoLetras">Monto en Letras</label>
                            <input type="text" id="montoLetras" name="montoLetras" value="<?= $escape($cheque['f23_montoLetras'] ?? '') ?>" required>
                        </div>
                    </div>
                    
                    <div class="form-row">
                        <div class="form-group">
                            <label for="fecha">Fecha</label>
                            <input type="date" id="fecha" name="fecha" value="<?= $escape($cheque['f23_fecha'] ?? '') ?>">
                        </div>
                        <div class="form-group">
                            <label for="cheque">Número de Cheque</label>
                            <input type="text" id="cheque" name="cheque" value="<?= $escape($cheque['f23_cheque'] ?? '') ?>">
                        </div>
                        <div class="form-group">
                            <label for="motivo">Motivo</label>
                            <input type="text" id="motivo" name="motivo" value="<?= $escape($cheque['f23_motivo'] ?? '') ?>">
                        </div>
                    </div>
                    
                    <div class="form-row">
                        <div class="form-group">
                            <label for="centro">Centro</label>
                            <input type="text" id="centro" name="centro" value="<?= $escape($cheque['f23_centro'] ?? '') ?>">
                        </div>
                        <div class="form-group">
                            <label for="condicion">Condición</label>
                            <input type="text" id="condicion" name="condicion" value="<?= $escape($cheque['f23_condicion'] ?? '') ?>">
                        </div>
                    </div>
                    
                    <div class="form-actions">
                        <button type="submit" class="btn btn-primary">💾 Guardar</button>
                        <a href="index.php?module=socios&action=cheques" class="btn btn-gray">🔙 Cancelar</a>
                    </div>
                </form>
            </section>
        </main>
    </div>

    <script>
        function numeroALetras(numero) {
            numero = parseFloat(numero);
            if (isNaN(numero) || numero === 0) return '';
            
            const unidades = ['', 'UNO', 'DOS', 'TRES', 'CUATRO', 'CINCO', 'SEIS', 'SIETE', 'OCHO', 'NUEVE'];
            const decenas = ['', 'DIEZ', 'VEINTE', 'TREINTA', 'CUARENTA', 'CINCUENTA', 'SESENTA', 'SETENTA', 'OCHENTA', 'NOVENTA'];
            const centenas = ['', 'CIENTO', 'DOSCIENTOS', 'TRESCIENTOS', 'CUATROCIENTOS', 'QUINIENTOS', 'SEISCIENTOS', 'SETECIENTOS', 'OCHOCIENTOS', 'NOVECIENTOS'];
            
            let entero = Math.floor(numero);
            let decimal = Math.round((numero - entero) * 100);
            
            if (entero === 0) return 'CERO';
            
            function convertirGrupo(n) {
                if (n === 0) return '';
                if (n === 1) return 'UNO';
                if (n === 2) return 'DOS';
                if (n === 3) return 'TRES';
                if (n === 4) return 'CUATRO';
                if (n === 5) return 'CINCO';
                if (n === 6) return 'SEIS';
                if (n === 7) return 'SIETE';
                if (n === 8) return 'OCHO';
                if (n === 9) return 'NUEVE';
                if (n === 10) return 'DIEZ';
                if (n === 11) return 'ONCE';
                if (n === 12) return 'DOCE';
                if (n === 13) return 'TRECE';
                if (n === 14) return 'CATORCE';
                if (n === 15) return 'QUINCE';
                if (n === 16) return 'DIECISEIS';
                if (n === 17) return 'DIECISIETE';
                if (n === 18) return 'DIECIOCHO';
                if (n === 19) return 'DIECINUEVE';
                if (n === 20) return 'VEINTE';
                if (n === 21) return 'VEINTIUNO';
                if (n === 22) return 'VEINTIDOS';
                if (n === 23) return 'VEINTITRES';
                if (n === 24) return 'VEINTICUATRO';
                if (n === 25) return 'VEINTICINCO';
                if (n === 26) return 'VEINTISEIS';
                if (n === 27) return 'VEINTISIETE';
                if (n === 28) return 'VEINTIOCHO';
                if (n === 29) return 'VEINTINUEVE';
                if (n < 100) {
                    const d = Math.floor(n / 10);
                    const u = n % 10;
                    if (u === 0) return decenas[d];
                    if (d === 2) return 'VEINTI' + unidades[u];
                    return decenas[d] + ' Y ' + unidades[u];
                }
                if (n < 1000) {
                    const c = Math.floor(n / 100);
                    const r = n % 100;
                    if (r === 0) {
                        if (c === 1) return 'CIEN';
                        return centenas[c];
                    }
                    if (c === 1) return 'CIENTO ' + convertirGrupo(r);
                    return centenas[c] + ' ' + convertirGrupo(r);
                }
                if (n < 1000000) {
                    const m = Math.floor(n / 1000);
                    const r = n % 1000;
                    if (r === 0) {
                        if (m === 1) return 'MIL';
                        return convertirGrupo(m) + ' MIL';
                    }
                    if (m === 1) return 'MIL ' + convertirGrupo(r);
                    return convertirGrupo(m) + ' MIL ' + convertirGrupo(r);
                }
                if (n < 1000000000) {
                    const mm = Math.floor(n / 1000000);
                    const r = n % 1000000;
                    if (r === 0) {
                        if (mm === 1) return 'UN MILLON';
                        return convertirGrupo(mm) + ' MILLONES';
                    }
                    if (mm === 1) return 'UN MILLON ' + convertirGrupo(r);
                    return convertirGrupo(mm) + ' MILLONES ' + convertirGrupo(r);
                }
                return '';
            }
            
            let letras = convertirGrupo(entero);
            
            if (decimal > 0) {
                letras += ' ' + decimal.toString().padStart(2, '0') + '/100';
            } else {
                letras += ' 00/100';
            }
            
            return letras;
        }

        function actualizarMontoLetras() {
            const monto = document.getElementById('monto').value;
            const montoLetras = document.getElementById('montoLetras');
            montoLetras.value = numeroALetras(monto);
        }
    </script>
</body>
</html>
