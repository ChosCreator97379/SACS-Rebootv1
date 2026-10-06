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
$prestamosSeleccionados = $_GET['prestamos'] ?? [];

if ($clienteCodigo === '' || empty($prestamosSeleccionados)) {
    http_response_code(400);
    $error = 'Cliente o préstamos no especificados';
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

// Calcular pendientes globales
$pendientesGlobales = $pagoIntegralServicio->calcularPendientesGlobales($clienteCodigo);
$subtotalGlobal = $pendientesGlobales['aportesFonbes'] + $pendientesGlobales['aportesCoopam'] + $pendientesGlobales['sobregiroFonbes'] + $pendientesGlobales['sobregiroCoopam'];

// Calcular pendientes por préstamo
$pendientesPorPrestamo = [];
$subtotalPrestamos = 0;
foreach ($prestamosSeleccionados as $prestamoCodigo) {
    $pendientes = $pagoIntegralServicio->calcularPendientesPorPrestamo((int) $prestamoCodigo);
    $pendientesPorPrestamo[$prestamoCodigo] = $pendientes;
    $subtotalPrestamos += $pendientes['subtotal'];
}

$totalPagar = $subtotalGlobal + $subtotalPrestamos;

// Obtener códigos contables
$codigosContables = $repository->accountingCodes();

$displayName = htmlspecialchars($user['username'], ENT_QUOTES, 'UTF-8');
$profile = htmlspecialchars((string) $user['profile'], ENT_QUOTES, 'UTF-8');
$userId = htmlspecialchars((string) $user['id'], ENT_QUOTES, 'UTF-8');
$supervisor = $user['supervisor'] === 'S';
$csrfToken = (string) $_SESSION['csrf_token'];

$escape = static fn (mixed $value): string => htmlspecialchars((string) ($value ?? ''), ENT_QUOTES, 'UTF-8');
$money = static fn (mixed $value): string => $value === null || $value === '' ? '-' : number_format((float) $value, 2, '.', ',');
?>
<!doctype html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Pago Integral | SACS Nuevo</title>
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
        .summary .total-highlight { background: var(--gold); color: var(--ink); }
        .summary .total-highlight .label { color: var(--ink); }
        .summary .total-highlight .value { color: var(--ink); }
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
        .amount-input { width: 100px; text-align: right; padding: 8px; border: 1px solid var(--line); border-radius: 4px; font-family: monospace; }
        .amount-input:focus { outline: none; border-color: var(--teal); }
        .total-row { background: var(--paper); }
        .total-row td { font-weight: 600; }
        .alert { padding: 15px; margin-bottom: 20px; border-radius: 6px; }
        .alert-error { background: #fee; color: #c33; border: 1px solid #fcc; }
        .section-separator { margin-top: 32px; margin-bottom: 32px; }
        .panel h3 { margin-top: 24px; }
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
                <h1>Pago Integral</h1>
                <div class="user">
                    <div class="user-avatar"><?= $displayName[0] ?></div>
                    <div>
                        <div class="user-name"><?= $displayName ?></div>
                        <div class="user-profile"><?= $profile ?></div>
                    </div>
                </div>
            </div>

            <?php if (isset($_GET['error'])): ?>
                <div class="alert alert-error">
                    <?= $escape($_GET['error']) ?>
                </div>
            <?php endif; ?>

            <section class="summary">
                <div><span class="label">Socio</span><div class="name"><?= $escape($cliente['full_name'] ?: 'Sin nombre') ?></div></div>
                <div><span class="label">Código</span><div class="value"><?= $escape($clienteCodigo) ?></div></div>
                <div><span class="label">Documento</span><div class="value"><?= $escape($cliente['document'] ?: '-') ?></div></div>
                <div><span class="label">Condición</span><div class="value"><?= $escape($cliente['condicion'] ?: '-') ?></div></div>
            </section>

            <!-- Componentes Globales -->
            <section class="panel">
                <h2>Componentes Globales</h2>
                
                <!-- Aportes FONBES -->
                <h3>Aportes FONBES</h3>
                <?php if (empty($pendientesGlobales['aportesFonbesDetalle'])): ?>
                    <p class="empty">No hay aportes FONBES pendientes</p>
                <?php else: ?>
                    <table style="margin-bottom: 20px;">
                        <thead>
                            <tr>
                                <th>Periodo</th>
                                <th>Programado</th>
                                <th>Pagado</th>
                                <th>Diferencia</th>
                                <th>Código Contable</th>
                                <th>Tipo</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($pendientesGlobales['aportesFonbesDetalle'] as $index => $detalle): ?>
                                <tr>
                                    <td><?= $escape($detalle['periodo']) ?></td>
                                    <td class="amount"><?= $money($detalle['programado']) ?></td>
                                    <td class="amount"><?= $money($detalle['pagado']) ?></td>
                                    <td>
                                        <input type="text" name="montos[globales][aportesFonbes][<?= $index ?>]" 
                                               value="<?= $money($detalle['diferencia']) ?>" 
                                               class="amount-input" step="0.01" onchange="calcularTotales()">
                                    </td>
                                    <td>
                                        <input type="text" name="codigosContables[globales][aportesFonbes][<?= $index ?>][codigo]" 
                                               list="codigos-contables" value="1651" class="amount-input">
                                    </td>
                                    <td>
                                        <select name="codigosContables[globales][aportesFonbes][<?= $index ?>][tipo]" class="amount-input">
                                            <option value="HABER">Haber</option>
                                            <option value="DEBE">Debe</option>
                                        </select>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                <?php endif; ?>

                <!-- Aportes COOPAM -->
                <h3>Aportes COOPAM</h3>
                <?php if (empty($pendientesGlobales['aportesCoopamDetalle'])): ?>
                    <p class="empty">No hay aportes COOPAM pendientes</p>
                <?php else: ?>
                    <table style="margin-bottom: 20px;">
                        <thead>
                            <tr>
                                <th>Periodo</th>
                                <th>Programado</th>
                                <th>Pagado</th>
                                <th>Diferencia</th>
                                <th>Código Contable</th>
                                <th>Tipo</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($pendientesGlobales['aportesCoopamDetalle'] as $index => $detalle): ?>
                                <tr>
                                    <td><?= $escape($detalle['periodo']) ?></td>
                                    <td class="amount"><?= $money($detalle['programado']) ?></td>
                                    <td class="amount"><?= $money($detalle['pagado']) ?></td>
                                    <td>
                                        <input type="text" name="montos[globales][aportesCoopam][<?= $index ?>]" 
                                               value="<?= $money($detalle['diferencia']) ?>" 
                                               class="amount-input" step="0.01" onchange="calcularTotales()">
                                    </td>
                                    <td>
                                        <input type="text" name="codigosContables[globales][aportesCoopam][<?= $index ?>][codigo]" 
                                               list="codigos-contables" value="1652" class="amount-input">
                                    </td>
                                    <td>
                                        <select name="codigosContables[globales][aportesCoopam][<?= $index ?>][tipo]" class="amount-input">
                                            <option value="HABER">Haber</option>
                                            <option value="DEBE">Debe</option>
                                        </select>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                <?php endif; ?>

                <!-- Sobregiros -->
                <h3>Sobregiros de Aportaciones</h3>
                <table>
                    <thead>
                        <tr>
                            <th>Componente</th>
                            <th>Monto</th>
                            <th>Código Contable</th>
                            <th>Tipo</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>Sobregiro FONBES</td>
                            <td>
                                <input type="text" name="montos[globales][sobregiroFonbes]" 
                                       id="monto-sobregiro-fonbes"
                                       value="<?= $money($pendientesGlobales['sobregiroFonbes']) ?>" 
                                       class="amount-input" step="0.01" onchange="calcularTotales()">
                            </td>
                            <td>
                                <input type="text" name="codigosContables[globales][sobregiroFonbes][codigo]" 
                                       list="codigos-contables" value="1653" class="amount-input">
                            </td>
                            <td>
                                <select name="codigosContables[globales][sobregiroFonbes][tipo]" class="amount-input">
                                    <option value="HABER">Haber</option>
                                    <option value="DEBE">Debe</option>
                                </select>
                            </td>
                        </tr>
                        <tr>
                            <td>Sobregiro COOPAM</td>
                            <td>
                                <input type="text" name="montos[globales][sobregiroCoopam]" 
                                       id="monto-sobregiro-coopam"
                                       value="<?= $money($pendientesGlobales['sobregiroCoopam']) ?>" 
                                       class="amount-input" step="0.01" onchange="calcularTotales()">
                            </td>
                            <td>
                                <input type="text" name="codigosContables[globales][sobregiroCoopam][codigo]" 
                                       list="codigos-contables" value="1654" class="amount-input">
                            </td>
                            <td>
                                <select name="codigosContables[globales][sobregiroCoopam][tipo]" class="amount-input">
                                    <option value="HABER">Haber</option>
                                    <option value="DEBE">Debe</option>
                                </select>
                            </td>
                        </tr>
                        <tr class="total-row">
                            <td><strong>Subtotal Global</strong></td>
                            <td class="amount"><strong id="subtotal-global"><?= $money($subtotalGlobal) ?></strong></td>
                            <td></td>
                            <td></td>
                        </tr>
                    </tbody>
                </table>
            </section>

            <!-- Componentes por Préstamo -->
            <?php foreach ($prestamosSeleccionados as $prestamoCodigo): ?>
                <?php $pendientes = $pendientesPorPrestamo[$prestamoCodigo]; ?>
                <section class="panel">
                    <h2>Préstamo #<?= $escape($prestamoCodigo) ?></h2>
                    
                    <?php if ($pendientes['prestamoInfo']): ?>
                        <div class="summary" style="margin-bottom: 16px;">
                            <div><span class="label">Importe Total</span><div class="value"><?= $money($pendientes['prestamoInfo']['importe']) ?></div></div>
                            <div><span class="label">Producto</span><div class="value"><?= $escape($pendientes['prestamoInfo']['producto'] ?? '-') ?></div></div>
                            <div><span class="label">Interés</span><div class="value"><?= $money($pendientes['prestamoInfo']['interes']) ?>%</div></div>
                            <div><span class="label">Cuotas Totales</span><div class="value"><?= $escape($pendientes['prestamoInfo']['cuotas_total'] ?? '-') ?></div></div>
                        </div>
                    <?php endif; ?>

                    <?php if ($pendientes['cuotaDetalle']): ?>
                        <h3>Cuota a Pagar</h3>
                        <div class="summary" style="margin-bottom: 16px;">
                            <div><span class="label">Número de Cuota</span><div class="value">Cuota #<?= $escape($pendientes['cuotaDetalle']['numero_cuota']) ?></div></div>
                            <div><span class="label">Fecha Vencimiento</span><div class="value"><?= $escape($pendientes['cuotaDetalle']['fecha_vencimiento']) ?></div></div>
                        </div>
                        <table style="margin-bottom: 20px;">
                            <thead>
                                <tr>
                                    <th>Componente</th>
                                    <th>Monto</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>Capital (Amortización)</td>
                                    <td class="amount"><?= $money($pendientes['cuotaDetalle']['capital']) ?></td>
                                </tr>
                                <tr>
                                    <td>Interés (Gastos Administrativos)</td>
                                    <td class="amount"><?= $money($pendientes['cuotaDetalle']['interes']) ?></td>
                                </tr>
                                <?php if ($pendientes['cuotaDetalle']['mora'] > 0): ?>
                                <tr>
                                    <td>Mora</td>
                                    <td class="amount"><?= $money($pendientes['cuotaDetalle']['mora']) ?></td>
                                </tr>
                                <?php endif; ?>
                                <tr class="total-row">
                                    <td><strong>Total Cuota</strong></td>
                                    <td class="amount"><strong><?= $money($pendientes['cuotaDetalle']['total']) ?></strong></td>
                                </tr>
                            </tbody>
                        </table>
                    <?php else: ?>
                        <p class="empty">No hay cuota vigente para pagar</p>
                    <?php endif; ?>

                    <div class="section-separator"></div>

                    <!-- Sección de Sobregiro del Préstamo -->
                    <h3>Sobregiro del Préstamo</h3>
                    <table>
                        <thead>
                            <tr>
                                <th>Componente</th>
                                <th>Monto</th>
                                <th>Código Contable</th>
                                <th>Tipo</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>Sobregiro a Pagar</td>
                                <td>
                                    <input type="text" name="montos[prestamos][<?= $escape($prestamoCodigo) ?>][sobregiro]" 
                                           id="monto-sobregiro-<?= $escape($prestamoCodigo) ?>"
                                           value="<?= $money($pendientes['sobregiro']) ?>" 
                                           class="amount-input" step="0.01" onchange="calcularTotales()">
                                </td>
                                <td>
                                    <input type="text" name="codigosContables[prestamos][<?= $escape($prestamoCodigo) ?>][sobregiro][codigo]" 
                                           list="codigos-contables" value="1655" class="amount-input">
                                </td>
                                <td>
                                    <select name="codigosContables[prestamos][<?= $escape($prestamoCodigo) ?>][sobregiro][tipo]" class="amount-input">
                                        <option value="HABER">Haber</option>
                                        <option value="DEBE">Debe</option>
                                    </select>
                                </td>
                            </tr>
                            <tr>
                                <td>Gastos Administrativos (Sobregiro)</td>
                                <td>
                                    <input type="text" name="montos[prestamos][<?= $escape($prestamoCodigo) ?>][gastos_adm]" 
                                           id="monto-gastos-adm-<?= $escape($prestamoCodigo) ?>"
                                           value="<?= $money($pendientes['prestamoInfo']['gastos_adm'] ?? 0) ?>" 
                                           class="amount-input" step="0.01" onchange="calcularTotales()">
                                </td>
                                <td>
                                    <input type="text" name="codigosContables[prestamos][<?= $escape($prestamoCodigo) ?>][gastos_adm][codigo]" 
                                           list="codigos-contables" value="1657" class="amount-input">
                                </td>
                                <td>
                                    <select name="codigosContables[prestamos][<?= $escape($prestamoCodigo) ?>][gastos_adm][tipo]" class="amount-input">
                                        <option value="HABER">Haber</option>
                                        <option value="DEBE">Debe</option>
                                    </select>
                                </td>
                            </tr>
                            <tr>
                                <td>Mora (Sobregiro)</td>
                                <td>
                                    <input type="text" name="montos[prestamos][<?= $escape($prestamoCodigo) ?>][gastos_mora]" 
                                           id="monto-gastos-mora-<?= $escape($prestamoCodigo) ?>"
                                           value="<?= $money($pendientes['prestamoInfo']['gastos_mora'] ?? 0) ?>" 
                                           class="amount-input" step="0.01" onchange="calcularTotales()">
                                </td>
                                <td>
                                    <input type="text" name="codigosContables[prestamos][<?= $escape($prestamoCodigo) ?>][gastos_mora][codigo]" 
                                           list="codigos-contables" value="1658" class="amount-input">
                                </td>
                                <td>
                                    <select name="codigosContables[prestamos][<?= $escape($prestamoCodigo) ?>][gastos_mora][tipo]" class="amount-input">
                                        <option value="HABER">Haber</option>
                                        <option value="DEBE">Debe</option>
                                    </select>
                                </td>
                            </tr>
                            <tr>
                                <td>Otros 1</td>
                                <td>
                                    <input type="text" name="montos[prestamos][<?= $escape($prestamoCodigo) ?>][otros1]" 
                                           id="monto-otros1-<?= $escape($prestamoCodigo) ?>"
                                           value="<?= $money($pendientes['prestamoInfo']['otros1'] ?? 0) ?>" 
                                           class="amount-input" step="0.01" onchange="calcularTotales()">
                                </td>
                                <td>
                                    <input type="text" name="codigosContables[prestamos][<?= $escape($prestamoCodigo) ?>][otros1][codigo]" 
                                           list="codigos-contables" value="1659" class="amount-input">
                                </td>
                                <td>
                                    <select name="codigosContables[prestamos][<?= $escape($prestamoCodigo) ?>][otros1][tipo]" class="amount-input">
                                        <option value="HABER">Haber</option>
                                        <option value="DEBE">Debe</option>
                                    </select>
                                </td>
                            </tr>
                            <tr>
                                <td>Otros 2</td>
                                <td>
                                    <input type="text" name="montos[prestamos][<?= $escape($prestamoCodigo) ?>][otros2]" 
                                           id="monto-otros2-<?= $escape($prestamoCodigo) ?>"
                                           value="<?= $money($pendientes['prestamoInfo']['otros2'] ?? 0) ?>" 
                                           class="amount-input" step="0.01" onchange="calcularTotales()">
                                </td>
                                <td>
                                    <input type="text" name="codigosContables[prestamos][<?= $escape($prestamoCodigo) ?>][otros2][codigo]" 
                                           list="codigos-contables" value="1660" class="amount-input">
                                </td>
                                <td>
                                    <select name="codigosContables[prestamos][<?= $escape($prestamoCodigo) ?>][otros2][tipo]" class="amount-input">
                                        <option value="HABER">Haber</option>
                                        <option value="DEBE">Debe</option>
                                    </select>
                                </td>
                            </tr>
                            <tr class="total-row">
                                <td><strong>Subtotal Sobregiro</strong></td>
                                <td class="amount"><strong id="subtotal-sobregiro-<?= $escape($prestamoCodigo) ?>"><?= $money($pendientes['sobregiro'] + ($pendientes['prestamoInfo']['gastos_adm'] ?? 0) + ($pendientes['prestamoInfo']['gastos_mora'] ?? 0) + ($pendientes['prestamoInfo']['otros1'] ?? 0) + ($pendientes['prestamoInfo']['otros2'] ?? 0)) ?></strong></td>
                                <td></td>
                                <td></td>
                            </tr>
                        </tbody>
                    </table>

                    <div class="section-separator"></div>

                    <!-- Sección de Amortización -->
                    <?php if ($pendientes['cuotaDetalle']): ?>
                        <h3>Amortización de Cuota</h3>
                        <table>
                            <thead>
                                <tr>
                                    <th>Componente</th>
                                    <th>Monto</th>
                                    <th>Código Contable</th>
                                    <th>Tipo</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>Amortización (Capital)</td>
                                    <td>
                                        <input type="text" name="montos[prestamos][<?= $escape($prestamoCodigo) ?>][amortizacion_capital]" 
                                               id="monto-amortizacion-capital-<?= $escape($prestamoCodigo) ?>"
                                               value="<?= $money($pendientes['cuotaDetalle']['capital']) ?>" 
                                               class="amount-input" step="0.01" onchange="calcularTotales()">
                                    </td>
                                    <td>
                                        <input type="text" name="codigosContables[prestamos][<?= $escape($prestamoCodigo) ?>][amortizacion_capital][codigo]" 
                                               list="codigos-contables" value="1656" class="amount-input">
                                    </td>
                                    <td>
                                        <select name="codigosContables[prestamos][<?= $escape($prestamoCodigo) ?>][amortizacion_capital][tipo]" class="amount-input">
                                            <option value="HABER">Haber</option>
                                            <option value="DEBE">Debe</option>
                                        </select>
                                    </td>
                                </tr>
                                <tr>
                                    <td>Interés (Gastos Administrativos)</td>
                                    <td>
                                        <input type="text" name="montos[prestamos][<?= $escape($prestamoCodigo) ?>][amortizacion_interes]" 
                                               id="monto-amortizacion-interes-<?= $escape($prestamoCodigo) ?>"
                                               value="<?= $money($pendientes['cuotaDetalle']['interes']) ?>" 
                                               class="amount-input" step="0.01" onchange="calcularTotales()">
                                    </td>
                                    <td>
                                        <input type="text" name="codigosContables[prestamos][<?= $escape($prestamoCodigo) ?>][amortizacion_interes][codigo]" 
                                               list="codigos-contables" value="1661" class="amount-input">
                                    </td>
                                    <td>
                                        <select name="codigosContables[prestamos][<?= $escape($prestamoCodigo) ?>][amortizacion_interes][tipo]" class="amount-input">
                                            <option value="HABER">Haber</option>
                                            <option value="DEBE">Debe</option>
                                        </select>
                                    </td>
                                </tr>
                                <?php if ($pendientes['cuotaDetalle']['mora'] > 0): ?>
                                <tr>
                                    <td>Mora (Cuota)</td>
                                    <td>
                                        <input type="text" name="montos[prestamos][<?= $escape($prestamoCodigo) ?>][amortizacion_mora]" 
                                               id="monto-amortizacion-mora-<?= $escape($prestamoCodigo) ?>"
                                               value="<?= $money($pendientes['cuotaDetalle']['mora']) ?>" 
                                               class="amount-input" step="0.01" onchange="calcularTotales()">
                                    </td>
                                    <td>
                                        <input type="text" name="codigosContables[prestamos][<?= $escape($prestamoCodigo) ?>][amortizacion_mora][codigo]" 
                                               list="codigos-contables" value="1662" class="amount-input">
                                    </td>
                                    <td>
                                        <select name="codigosContables[prestamos][<?= $escape($prestamoCodigo) ?>][amortizacion_mora][tipo]" class="amount-input">
                                            <option value="HABER">Haber</option>
                                            <option value="DEBE">Debe</option>
                                        </select>
                                    </td>
                                </tr>
                                <?php endif; ?>
                                <tr class="total-row">
                                    <td><strong>Subtotal Amortización</strong></td>
                                    <td class="amount"><strong id="subtotal-amortizacion-<?= $escape($prestamoCodigo) ?>"><?= $money($pendientes['cuotaDetalle']['total']) ?></strong></td>
                                    <td></td>
                                    <td></td>
                                </tr>
                            </tbody>
                        </table>
                    <?php endif; ?>

                    <!-- Resumen Total del Préstamo -->
                    <h3>Resumen Total del Préstamo</h3>
                    <table>
                        <thead>
                            <tr>
                                <th>Componente</th>
                                <th>Monto</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>Subtotal Sobregiro</td>
                                <td class="amount" id="display-subtotal-sobregiro-<?= $escape($prestamoCodigo) ?>"><?= $money($pendientes['sobregiro'] + ($pendientes['prestamoInfo']['gastos_adm'] ?? 0) + ($pendientes['prestamoInfo']['gastos_mora'] ?? 0) + ($pendientes['prestamoInfo']['otros1'] ?? 0) + ($pendientes['prestamoInfo']['otros2'] ?? 0)) ?></td>
                            </tr>
                            <?php if ($pendientes['cuotaDetalle']): ?>
                            <tr>
                                <td>Subtotal Amortización</td>
                                <td class="amount" id="display-subtotal-amortizacion-<?= $escape($prestamoCodigo) ?>"><?= $money($pendientes['cuotaDetalle']['total']) ?></td>
                            </tr>
                            <?php endif; ?>
                            <tr class="total-row">
                                <td><strong>Subtotal Préstamo</strong></td>
                                <td class="amount"><strong id="subtotal-<?= $escape($prestamoCodigo) ?>"><?= $money($pendientes['subtotal']) ?></strong></td>
                            </tr>
                        </tbody>
                    </table>
                </section>
            <?php endforeach; ?>

            <!-- Resumen y Formulario -->
            <section class="panel">
                <h2>Resumen del Pago</h2>
                <div class="summary">
                    <div><span class="label">Subtotal Global</span><div class="value" id="display-subtotal-global"><?= $money($subtotalGlobal) ?></div></div>
                    <div><span class="label">Subtotal Préstamos</span><div class="value" id="display-subtotal-prestamos"><?= $money($subtotalPrestamos) ?></div></div>
                    <div class="total-highlight"><span class="label"><strong>Total a Pagar</strong></span><div class="value"><strong id="display-total-pagar"><?= $money($totalPagar) ?></strong></div></div>
                </div>
            </section>

            <section class="panel">
                <h2>Datos Generales</h2>
                <form action="index.php" method="POST">
                    <input type="hidden" name="csrf_token" value="<?= $escape($csrfToken) ?>">
                    <input type="hidden" name="module" value="caja">
                    <input type="hidden" name="action" value="pago_integral">
                    <input type="hidden" name="cliente" value="<?= $escape($clienteCodigo) ?>">
                    <?php foreach ($prestamosSeleccionados as $prestamoCodigo): ?>
                        <input type="hidden" name="prestamos[]" value="<?= $escape($prestamoCodigo) ?>">
                    <?php endforeach; ?>

                    <datalist id="codigos-contables">
                        <?php foreach ($codigosContables as $codigo): ?>
                            <option value="<?= $escape($codigo['code']) ?>"><?= $escape($codigo['code']) ?> - <?= $escape($codigo['name']) ?></option>
                        <?php endforeach; ?>
                    </datalist>

                    <div class="form-row">
                        <div class="form-group">
                            <label>Forma de Pago</label>
                            <select name="formaPago" required>
                                <option value="">Seleccione...</option>
                                <option value="EFECTIVO">Efectivo</option>
                                <option value="DEPOSITO">Depósito</option>
                                <option value="TRANSFERENCIA">Transferencia</option>
                                <option value="CHEQUE">Cheque</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label>Descripción Contable</label>
                            <input type="text" name="descripcionContable" placeholder="Descripción...">
                        </div>
                    </div>
                    <div class="form-row">
                        <div class="form-group" style="grid-column: 1 / -1;">
                            <label>Observaciones</label>
                            <textarea name="observaciones" rows="3" placeholder="Observaciones del pago integral..."></textarea>
                        </div>
                    </div>

                    <div class="form-actions">
                        <button type="submit" class="btn btn-primary">Procesar Pago Integral</button>
                        <a href="index.php?module=caja&action=pago_integral_seleccion&client=<?= rawurlencode($clienteCodigo) ?>" class="btn btn-secondary">Volver</a>
                    </div>
                </form>
            </section>
        </main>
    </div>

    <script>
        function parseMoney(value) {
            if (!value || value === '-') return 0;
            // Remove commas and parse as float
            return parseFloat(value.replace(/,/g, '')) || 0;
        }

        function formatMoney(value) {
            return value.toFixed(2).replace(/\B(?=(\d{3})+(?!\d))/g, ",");
        }

        function calcularTotales() {
            // Calcular subtotal global (aportes)
            let subtotalAportes = 0;
            <?php foreach ($pendientesGlobales['aportesFonbesDetalle'] as $index => $detalle): ?>
                const aporteFonbes<?= $index ?> = parseMoney(document.querySelector('input[name="montos[globales][aportesFonbes][<?= $index ?>]"]')?.value);
                subtotalAportes += aporteFonbes<?= $index ?>;
            <?php endforeach; ?>
            <?php foreach ($pendientesGlobales['aportesCoopamDetalle'] as $index => $detalle): ?>
                const aporteCoopam<?= $index ?> = parseMoney(document.querySelector('input[name="montos[globales][aportesCoopam][<?= $index ?>]"]')?.value);
                subtotalAportes += aporteCoopam<?= $index ?>;
            <?php endforeach; ?>

            // Calcular sobregiros globales
            const sobregiroFonbes = parseMoney(document.getElementById('monto-sobregiro-fonbes')?.value);
            const sobregiroCoopam = parseMoney(document.getElementById('monto-sobregiro-coopam')?.value);
            const subtotalGlobal = subtotalAportes + sobregiroFonbes + sobregiroCoopam;

            // Calcular subtotal por préstamos
            let subtotalPrestamos = 0;
            <?php foreach ($prestamosSeleccionados as $prestamoCodigo): ?>
                // Sobregiro del préstamo
                const sobregiro<?= $escape($prestamoCodigo) ?> = parseMoney(document.getElementById('monto-sobregiro-<?= $escape($prestamoCodigo) ?>')?.value);
                const gastosAdm<?= $escape($prestamoCodigo) ?> = parseMoney(document.getElementById('monto-gastos-adm-<?= $escape($prestamoCodigo) ?>')?.value);
                const gastosMora<?= $escape($prestamoCodigo) ?> = parseMoney(document.getElementById('monto-gastos-mora-<?= $escape($prestamoCodigo) ?>')?.value);
                const otros1<?= $escape($prestamoCodigo) ?> = parseMoney(document.getElementById('monto-otros1-<?= $escape($prestamoCodigo) ?>')?.value);
                const otros2<?= $escape($prestamoCodigo) ?> = parseMoney(document.getElementById('monto-otros2-<?= $escape($prestamoCodigo) ?>')?.value);
                
                const subtotalSobregiro<?= $escape($prestamoCodigo) ?> = sobregiro<?= $escape($prestamoCodigo) ?> + gastosAdm<?= $escape($prestamoCodigo) ?> + gastosMora<?= $escape($prestamoCodigo) ?> + otros1<?= $escape($prestamoCodigo) ?> + otros2<?= $escape($prestamoCodigo) ?>;

                // Amortización
                let subtotalAmortizacion<?= $escape($prestamoCodigo) ?> = 0;
                const amortizacionCapital<?= $escape($prestamoCodigo) ?> = parseMoney(document.getElementById('monto-amortizacion-capital-<?= $escape($prestamoCodigo) ?>')?.value);
                const amortizacionInteres<?= $escape($prestamoCodigo) ?> = parseMoney(document.getElementById('monto-amortizacion-interes-<?= $escape($prestamoCodigo) ?>')?.value);
                const amortizacionMora<?= $escape($prestamoCodigo) ?> = parseMoney(document.getElementById('monto-amortizacion-mora-<?= $escape($prestamoCodigo) ?>')?.value) || 0;
                
                subtotalAmortizacion<?= $escape($prestamoCodigo) ?> = amortizacionCapital<?= $escape($prestamoCodigo) ?> + amortizacionInteres<?= $escape($prestamoCodigo) ?> + amortizacionMora<?= $escape($prestamoCodigo) ?>;

                const subtotalPrestamo<?= $escape($prestamoCodigo) ?> = subtotalSobregiro<?= $escape($prestamoCodigo) ?> + subtotalAmortizacion<?= $escape($prestamoCodigo) ?>;
                subtotalPrestamos += subtotalPrestamo<?= $escape($prestamoCodigo) ?>;

                // Actualizar displays
                const subtotalSobregiroElement<?= $escape($prestamoCodigo) ?> = document.getElementById('subtotal-sobregiro-<?= $escape($prestamoCodigo) ?>');
                if (subtotalSobregiroElement<?= $escape($prestamoCodigo) ?>) {
                    subtotalSobregiroElement<?= $escape($prestamoCodigo) ?>.textContent = formatMoney(subtotalSobregiro<?= $escape($prestamoCodigo) ?>);
                }

                const displaySubtotalSobregiro<?= $escape($prestamoCodigo) ?> = document.getElementById('display-subtotal-sobregiro-<?= $escape($prestamoCodigo) ?>');
                if (displaySubtotalSobregiro<?= $escape($prestamoCodigo) ?>) {
                    displaySubtotalSobregiro<?= $escape($prestamoCodigo) ?>.textContent = formatMoney(subtotalSobregiro<?= $escape($prestamoCodigo) ?>);
                }

                const subtotalAmortizacionElement<?= $escape($prestamoCodigo) ?> = document.getElementById('subtotal-amortizacion-<?= $escape($prestamoCodigo) ?>');
                if (subtotalAmortizacionElement<?= $escape($prestamoCodigo) ?>) {
                    subtotalAmortizacionElement<?= $escape($prestamoCodigo) ?>.textContent = formatMoney(subtotalAmortizacion<?= $escape($prestamoCodigo) ?>);
                }

                const displaySubtotalAmortizacion<?= $escape($prestamoCodigo) ?> = document.getElementById('display-subtotal-amortizacion-<?= $escape($prestamoCodigo) ?>');
                if (displaySubtotalAmortizacion<?= $escape($prestamoCodigo) ?>) {
                    displaySubtotalAmortizacion<?= $escape($prestamoCodigo) ?>.textContent = formatMoney(subtotalAmortizacion<?= $escape($prestamoCodigo) ?>);
                }

                const subtotalElement<?= $escape($prestamoCodigo) ?> = document.getElementById('subtotal-<?= $escape($prestamoCodigo) ?>');
                if (subtotalElement<?= $escape($prestamoCodigo) ?>) {
                    subtotalElement<?= $escape($prestamoCodigo) ?>.textContent = formatMoney(subtotalPrestamo<?= $escape($prestamoCodigo) ?>);
                }
            <?php endforeach; ?>

            // Calcular total
            const totalPagar = subtotalGlobal + subtotalPrestamos;

            // Actualizar display
            document.getElementById('subtotal-global').textContent = formatMoney(subtotalGlobal);
            document.getElementById('display-subtotal-global').textContent = formatMoney(subtotalGlobal);
            document.getElementById('display-subtotal-prestamos').textContent = formatMoney(subtotalPrestamos);
            document.getElementById('display-total-pagar').textContent = formatMoney(totalPagar);
        }

        // Inicializar al cargar
        document.addEventListener('DOMContentLoaded', calcularTotales);
    </script>
</body>
</html>
