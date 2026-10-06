<?php

declare(strict_types=1);

require dirname(__DIR__) . '/../config/bootstrap.php';

use Sacs\Core\Database;
use Sacs\Repositories\CajaRepository;
use Sacs\Services\PagoIntegralServicio;
use Sacs\Security\Auth;

$auth = new Auth(new Sacs\Repositories\UserRepository());
if (!$auth->check()) {
    header('Location: index.php');
    exit;
}

$user = $auth->user();
$repository = new CajaRepository();
$database = Database::connection();
$pagoIntegralServicio = new PagoIntegralServicio();

$clienteCodigo = trim((string) ($_GET['client'] ?? ''));

if ($clienteCodigo === '') {
    http_response_code(400);
    $error = 'Cliente no especificado';
    require dirname(__DIR__) . '/views/home.php';
    exit;
}

// Obtener datos del cliente
$stmt = $database->prepare("
    SELECT
        CAST(c.F02_ID_CLIENTE AS UNSIGNED) AS client_id,
        TRIM(CONCAT_WS(' ', p.F01_APE_PAT, p.F01_APE_MAT, p.F01_NOMB_PRI, p.F01_NOMB_SEG)) AS full_name,
        p.F01_NUM_DOC AS document,
        p.F01_CONDICION AS condicion
    FROM f02_cliente c
    INNER JOIN f01_persona p ON p.F01_ID_PERSONA = c.F02_ID_PERSONA
    WHERE CAST(c.F02_ID_CLIENTE AS UNSIGNED) = CAST(:client AS UNSIGNED)
    LIMIT 1
");
$stmt->execute(['client' => $clienteCodigo]);
$cliente = $stmt->fetch();

if ($cliente === false) {
    http_response_code(404);
    $error = 'Cliente no encontrado';
    require dirname(__DIR__) . '/views/home.php';
    exit;
}

$prestamosActivos = $pagoIntegralServicio->obtenerPrestamosActivos($clienteCodigo);

$displayName = htmlspecialchars($user['username'], ENT_QUOTES, 'UTF-8');
$profile = htmlspecialchars((string) $user['profile'], ENT_QUOTES, 'UTF-8');
$userId = htmlspecialchars((string) $user['id'], ENT_QUOTES, 'UTF-8');
$supervisor = $user['supervisor'] === 'S';

$escape = static fn (mixed $value): string => htmlspecialchars((string) ($value ?? ''), ENT_QUOTES, 'UTF-8');
$money = static fn (mixed $value): string => $value === null || $value === '' ? '-' : number_format((float) $value, 2, '.', ',');
?>
<!doctype html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Pago Integral - Seleccionar Préstamos | SACS Nuevo</title>
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
        .summary { display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 16px; margin-bottom: 24px; }
        .summary div { background: var(--panel); padding: 16px; border-radius: 8px; }
        .summary .label { font-size: 11px; color: var(--muted); text-transform: uppercase; letter-spacing: .08em; margin-bottom: 4px; }
        .summary .value { font-size: 18px; font-weight: 600; }
        .summary .name { font-size: 14px; }
        .form-row { display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 16px; margin-bottom: 16px; }
        .form-group { display: flex; flex-direction: column; }
        .form-group label { font-size: 12px; font-weight: 600; color: var(--muted); margin-bottom: 6px; }
        .form-group input, .form-group select, .form-group textarea { padding: 10px 12px; border: 1px solid var(--line); border-radius: 6px; font-size: 13px; font-family: inherit; }
        .form-group input:focus, .form-group select:focus, .form-group textarea:focus { outline: none; border-color: var(--teal); }
        .form-group input[readonly] { background: var(--paper); }
        .form-actions { display: flex; gap: 12px; margin-top: 24px; }
        .btn { padding: 12px 24px; border: none; border-radius: 6px; font-size: 13px; font-weight: 600; cursor: pointer; transition: background .15s; }
        .btn-primary { background: var(--teal); color: white; }
        .btn-primary:hover { background: #166664; }
        .btn-secondary { background: var(--paper); color: var(--ink); }
        .btn-secondary:hover { background: #e8ebe9; }
        .empty { color: var(--muted); font-style: italic; padding: 20px 0; text-align: center; }
        table { width: 100%; border-collapse: collapse; }
        th { text-align: left; font-size: 11px; font-weight: 600; color: var(--muted); text-transform: uppercase; letter-spacing: .08em; padding: 12px 16px; border-bottom: 1px solid var(--line); }
        td { padding: 12px 16px; border-bottom: 1px solid var(--line); font-size: 13px; }
        td.amount { font-family: monospace; }
        .checkbox-group { margin-bottom: 16px; }
        .checkbox-label { display: flex; align-items: center; gap: 8px; font-size: 13px; }
        .checkbox-label input { width: auto; }
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
            <div class="nav-label">Caja</div>
            <nav class="nav">
                <a href="index.php?module=caja&profile=client&client=<?= rawurlencode($clienteCodigo) ?>" class="nav-link">Perfil Cliente</a>
                <a href="index.php?module=caja&action=pago_integral_seleccion&client=<?= rawurlencode($clienteCodigo) ?>" class="nav-link active">Pago Integral</a>
                <a href="index.php?module=caja&action=aportes_fonbes_form&client=<?= rawurlencode($clienteCodigo) ?>" class="nav-link">Aportes FONBES</a>
                <a href="index.php?module=caja&action=aportes_coopam_form&client=<?= rawurlencode($clienteCodigo) ?>" class="nav-link">Aportes COOPAM</a>
                <a href="index.php?module=caja&action=desembolso_prestamo_form&client=<?= rawurlencode($clienteCodigo) ?>" class="nav-link">Desembolso Préstamo</a>
                <a href="index.php?module=caja&action=sobregiro_prestamo_form&client=<?= rawurlencode($clienteCodigo) ?>" class="nav-link">Sobregiro Préstamo</a>
                <a href="index.php?module=caja&action=amortizar_form&client=<?= rawurlencode($clienteCodigo) ?>" class="nav-link">Amortización</a>
                <a href="index.php?module=caja&action=cancelacion_aee_form&client=<?= rawurlencode($clienteCodigo) ?>" class="nav-link">Cancelación AEE</a>
            </nav>
        </aside>

        <main class="main">
            <div class="header">
                <h1>Pago Integral - Seleccionar Préstamos</h1>
                <div class="user">
                    <div class="user-avatar"><?= $displayName[0] ?></div>
                    <div>
                        <div class="user-name"><?= $displayName ?></div>
                        <div class="user-profile"><?= $profile ?></div>
                    </div>
                </div>
            </div>

            <section class="summary">
                <div><span class="label">Socio</span><div class="name"><?= $escape($cliente['full_name'] ?: 'Sin nombre') ?></div></div>
                <div><span class="label">Código</span><div class="value"><?= $escape($clienteCodigo) ?></div></div>
                <div><span class="label">Documento</span><div class="value"><?= $escape($cliente['document'] ?: '-') ?></div></div>
                <div><span class="label">Condición</span><div class="value"><?= $escape($cliente['condicion'] ?: '-') ?></div></div>
            </section>

            <?php if (empty($prestamosActivos)): ?>
                <section class="panel">
                    <h2>Préstamos Activos</h2>
                    <p class="empty">No hay préstamos activos para este cliente.</p>
                    <div class="form-actions">
                        <a href="index.php?module=caja&profile=client&client=<?= rawurlencode($clienteCodigo) ?>" class="btn btn-secondary">Volver al Perfil</a>
                    </div>
                </section>
            <?php else: ?>
                <section class="panel">
                    <h2>Seleccionar Préstamos</h2>
                    <p style="margin-bottom: 16px; color: var(--muted);">Seleccione los préstamos para los que desea realizar el pago integral:</p>

                    <div class="checkbox-group">
                        <label class="checkbox-label">
                            <input type="checkbox" id="selectAll" onchange="toggleAllCheckboxes(this)">
                            <span>Seleccionar todos</span>
                        </label>
                    </div>

                    <table>
                        <thead>
                            <tr>
                                <th style="width: 50px;"></th>
                                <th>Código</th>
                                <th>Importe</th>
                                <th>Cuotas Pendientes</th>
                                <th>Saldo</th>
                                <th>Sobregiro</th>
                                <th>Fecha Desembolso</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($prestamosActivos as $prestamo): ?>
                                <?php
                                $cuotaActual = $pagoIntegralServicio->obtenerCuotaActual((int) $prestamo['codigo']);
                                ?>
                                <tr>
                                    <td>
                                        <input type="checkbox" name="prestamos[]" value="<?= $escape($prestamo['codigo']) ?>" class="prestamo-checkbox">
                                    </td>
                                    <td><?= $escape($prestamo['codigo']) ?></td>
                                    <td class="amount"><?= $money($prestamo['importe']) ?></td>
                                    <td><?= $escape($prestamo['cuotas_pendientes']) ?></td>
                                    <td class="amount"><?= $money($prestamo['saldo']) ?></td>
                                    <td class="amount"><?= $money($prestamo['sobregiro']) ?></td>
                                    <td><?= $escape($prestamo['fecha_desembolso']) ?></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>

                    <div class="form-actions">
                        <button type="button" class="btn btn-primary" onclick="submitForm()">Continuar</button>
                        <a href="index.php?module=caja&profile=client&client=<?= rawurlencode($clienteCodigo) ?>" class="btn btn-secondary">Cancelar</a>
                    </div>
                </section>
            <?php endif; ?>
        </main>
    </div>

    <script>
        function toggleAllCheckboxes(source) {
            const checkboxes = document.querySelectorAll('.prestamo-checkbox');
            checkboxes.forEach(checkbox => {
                checkbox.checked = source.checked;
            });
        }

        function validateSelection() {
            const checkboxes = document.querySelectorAll('.prestamo-checkbox:checked');
            if (checkboxes.length === 0) {
                alert('Debe seleccionar al menos un préstamo.');
                return false;
            }
            return true;
        }

        function submitForm() {
            if (!validateSelection()) {
                return;
            }

            const checkboxes = document.querySelectorAll('.prestamo-checkbox:checked');
            const prestamos = [];
            checkboxes.forEach(checkbox => {
                prestamos.push(checkbox.value);
            });

            const url = 'index.php?module=caja&action=pago_integral_form&client=<?= rawurlencode($clienteCodigo) ?>' + 
                         prestamos.map(p => '&prestamos[]=' + encodeURIComponent(p)).join('');
            window.location.href = url;
        }
    </script>
</body>
</html>
