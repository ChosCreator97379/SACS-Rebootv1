<?php

declare(strict_types=1);

require dirname(__DIR__) . '/../config/bootstrap.php';

use Sacs\Core\Database;
use Sacs\Repositories\CajaRepository;
use Sacs\Security\Auth;

$auth = new Auth(new Sacs\Repositories\UserRepository());
if (!$auth->check()) {
    header('Location: index.php');
    exit;
}

$user = $auth->user();
$repository = new CajaRepository();
$database = Database::connection();
$codigoContable = $repository->accountingCodes();

$clienteCodigo = trim((string) ($_GET['client'] ?? ''));

if ($clienteCodigo === '') {
    http_response_code(400);
    $error = 'Cliente no especificado';
    require dirname(__DIR__) . '/views/home.php';
    exit;
}

// Obtener datos del cliente
$cliente = $repository->findClientProfile($clienteCodigo);
if ($cliente === null) {
    http_response_code(404);
    $error = 'Cliente no encontrado';
    require dirname(__DIR__) . '/views/home.php';
    exit;
}

// Obtener préstamos del cliente (estado 4 = VIGENTE)
$stmt = $database->prepare("
    SELECT f16_codigo AS id, f16_producto AS product, f16_importe AS amount,
           f16_saldo AS balance, f16_sobre AS sobregiro, f16_gastosa AS gastos_adm,
           f16_gastosm AS mora, f16_otros AS otros1, f16_otros2 AS otros2
    FROM f16_prestamo
    WHERE f16_cliente = :client AND f16_estado = '4'
    ORDER BY f16_codigo DESC
    LIMIT 20
");
$stmt->execute(['client' => $clienteCodigo]);
$prestamos = $stmt->fetchAll();

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
    <title>Pago de Sobregiro de Préstamo | SACS Nuevo</title>
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
        .layout { display: grid; grid-template-columns: 1fr; gap: 24px; }
        table { width: 100%; border-collapse: collapse; }
        th { text-align: left; font-size: 11px; font-weight: 600; color: var(--muted); text-transform: uppercase; letter-spacing: .08em; padding: 12px 16px; border-bottom: 1px solid var(--line); }
        td { padding: 12px 16px; border-bottom: 1px solid var(--line); font-size: 13px; }
        td.amount { font-family: monospace; }
        .alert { padding: 15px; margin-bottom: 20px; border-radius: 6px; }
        .alert-error { background: #fee; color: #c33; border: 1px solid #fcc; }
        .wide { grid-column: 1 / -1; }
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
                <a href="index.php?module=caja&action=aportes_fonbes_form&client=<?= rawurlencode($clienteCodigo) ?>" class="nav-link">Aportes FONBES</a>
                <a href="index.php?module=caja&action=aportes_coopam_form&client=<?= rawurlencode($clienteCodigo) ?>" class="nav-link">Aportes COOPAM</a>
                <a href="index.php?module=caja&action=aportes_combinados_form&client=<?= rawurlencode($clienteCodigo) ?>" class="nav-link">Pago FONBES + COOPAM</a>
                <a href="index.php?module=caja&action=desembolso_prestamo_form&client=<?= rawurlencode($clienteCodigo) ?>" class="nav-link">Desembolso Préstamo</a>
                <a href="index.php?module=caja&action=sobregiro_prestamo_form&client=<?= rawurlencode($clienteCodigo) ?>" class="nav-link active">Sobregiro Préstamo</a>
                <a href="index.php?module=caja&action=amortizar_form&client=<?= rawurlencode($clienteCodigo) ?>" class="nav-link">Amortización</a>
                <a href="index.php?module=caja&action=pago_integral_seleccion&client=<?= rawurlencode($clienteCodigo) ?>" class="nav-link">Pago Integral</a>
                <a href="index.php?module=caja&action=multas_form&client=<?= rawurlencode($clienteCodigo) ?>" class="nav-link">Multas</a>
                <a href="index.php?module=caja&action=devolucion_fonbes_form&client=<?= rawurlencode($clienteCodigo) ?>" class="nav-link">Devolución FONBES</a>
                <a href="index.php?module=caja&action=devolucion_coopam_form&client=<?= rawurlencode($clienteCodigo) ?>" class="nav-link">Devolución COOPAM</a>
                <a href="index.php?module=caja&action=cancelacion_aee_form&client=<?= rawurlencode($clienteCodigo) ?>" class="nav-link">Cancelación AEE</a>
                <a href="index.php?module=caja&action=pago_rifa_form&client=<?= rawurlencode($clienteCodigo) ?>" class="nav-link">Pago Rifa</a>
                <a href="index.php?module=caja&action=pago_sobregiro_fonbes_form&client=<?= rawurlencode($clienteCodigo) ?>" class="nav-link">Pago Sobregiro FONBES</a>
                <a href="index.php?module=caja&action=pago_sobregiro_coopam_form&client=<?= rawurlencode($clienteCodigo) ?>" class="nav-link">Pago Sobregiro COOPAM</a>
                <a href="index.php?module=caja&action=otras_devoluciones_form&client=<?= rawurlencode($clienteCodigo) ?>" class="nav-link">Otras Devoluciones</a>
                <a href="index.php?module=caja&action=otros_ingresos_form&client=<?= rawurlencode($clienteCodigo) ?>" class="nav-link">Otros Ingresos</a>
                <a href="index.php?module=caja&action=desembolso_beneficios_form&client=<?= rawurlencode($clienteCodigo) ?>" class="nav-link">Desembolso Beneficios</a>
                <a href="index.php?module=caja&action=egresos_form&client=<?= rawurlencode($clienteCodigo) ?>" class="nav-link">Egresos</a>
            </nav>
        </aside>

        <main class="main">
            <div class="header">
                <h1>Pago de Sobregiro de Préstamo</h1>
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
                <div><span class="label">Socio</span><div class="name"><?= $escape($cliente['client_id'] ?? '') ?></div></div>
                <div><span class="label">Nombre</span><div class="value"><?= $escape($cliente['full_name'] ?? 'Sin nombre') ?></div></div>
                <div><span class="label">Documento</span><div class="value"><?= $escape($cliente['document'] ?? '-') ?></div></div>
                <div><span class="label">Condicion</span><div class="value"><?= $escape($cliente['condition'] ?? '-') ?></div></div>
            </section>
            
            <!-- Script con datos del cliente para usar en JavaScript -->
            <script>
                const clienteCondicion = '<?= $escape($cliente['condition'] ?? '') ?>';
            </script>

            <div class="layout">
                <section class="panel">
                    <h2>Préstamos con Sobregiro</h2>
                    <?php if (empty($prestamos)): ?>
                        <p class="empty">No hay préstamos con sobregiro para este cliente.</p>
                    <?php else: ?>
                        <table>
                            <thead>
                                <tr>
                                    <th>Código</th>
                                    <th>Producto</th>
                                    <th>Saldo</th>
                                    <th>Sobregiro</th>
                                    <th>Gastos Adm</th>
                                    <th>Mora</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($prestamos as $prestamo): ?>
                                    <tr>
                                        <td>#<?= $escape($prestamo['id']) ?></td>
                                        <td><?= $escape($prestamo['product']) ?></td>
                                        <td class="amount"><?= $money($prestamo['balance']) ?></td>
                                        <td class="amount"><?= $money($prestamo['sobregiro']) ?></td>
                                        <td class="amount"><?= $money($prestamo['gastos_adm']) ?></td>
                                        <td class="amount"><?= $money($prestamo['mora']) ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    <?php endif; ?>
                </section>

                <section class="panel">
                    <h2>Registrar Pago de Sobregiro</h2>
                    <form action="index.php" method="POST">
                        <input type="hidden" name="csrf_token" value="<?= $escape($csrfToken) ?>">
                        <input type="hidden" name="module" value="caja">
                        <input type="hidden" name="action" value="sobregiro_prestamo">
                        <input type="hidden" name="operacion" value="10">
                        <input type="hidden" name="cliente" value="<?= $escape($clienteCodigo) ?>">

                        <div class="form-group">
                            <label for="prestamo">Préstamo</label>
                            <select id="prestamo" name="prestamo" required onchange="cargarDatosPrestamo()">
                                <option value="">Seleccione préstamo</option>
                                <?php foreach ($prestamos as $prestamo): ?>
                                    <option value="<?= $escape($prestamo['id']) ?>"
                                            data-sobregiro="<?= $escape($prestamo['sobregiro']) ?>"
                                            data-gastos-adm="<?= $escape($prestamo['gastos_adm']) ?>"
                                            data-mora="<?= $escape($prestamo['mora']) ?>"
                                            data-otros1="<?= $escape($prestamo['otros1']) ?>"
                                            data-otros2="<?= $escape($prestamo['otros2']) ?>">
                                        #<?= $escape($prestamo['id']) ?> - <?= $escape($prestamo['product']) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <div class="form-row">
                            <div class="form-group">
                                <label for="sobregiro">Sobregiro a Pagar</label>
                                <input type="number" id="sobregiro" name="sobregiro" step="0.01" required placeholder="0.00">
                            </div>
                            <div class="form-group">
                                <label for="sobregiro_codigo">Código</label>
                                <input type="text" id="sobregiro_codigo" name="sobregiro_codigo" list="listaCodigosContables" placeholder="Código">
                                <datalist id="listaCodigosContables">
                                    <?php foreach ($codigoContable as $codigo): ?>
                                        <option value="<?= $escape($codigo['code']) ?>">
                                            <?= $escape($codigo['code']) ?> - <?= $escape($codigo['name']) ?>
                                        </option>
                                    <?php endforeach; ?>
                                </datalist>
                            </div>
                            <div class="form-group">
                                <label for="sobregiro_tipo">Tipo</label>
                                <select id="sobregiro_tipo" name="sobregiro_tipo">
                                    <option value="">-</option>
                                    <option value="debe">Debe</option>
                                    <option value="haber">Haber</option>
                                </select>
                            </div>
                        </div>

                        <div class="form-row">
                            <div class="form-group">
                                <label for="descuenta">Amortización Adicional (opcional)</label>
                                <input type="number" id="descuenta" name="descuenta" step="0.01" placeholder="0.00">
                            </div>
                            <div class="form-group">
                                <label for="descuenta_codigo">Código</label>
                                <input type="text" id="descuenta_codigo" name="descuenta_codigo" list="listaCodigosContables" placeholder="Código">
                                <datalist id="listaCodigosContables">
                                    <?php foreach ($codigoContable as $codigo): ?>
                                        <option value="<?= $escape($codigo['code']) ?>">
                                            <?= $escape($codigo['code']) ?> - <?= $escape($codigo['name']) ?>
                                        </option>
                                    <?php endforeach; ?>
                                </datalist>
                            </div>
                            <div class="form-group">
                                <label for="descuenta_tipo">Tipo</label>
                                <select id="descuenta_tipo" name="descuenta_tipo">
                                    <option value="">-</option>
                                    <option value="debe">Debe</option>
                                    <option value="haber">Haber</option>
                                </select>
                            </div>
                        </div>

                        <div class="form-row">
                            <div class="form-group">
                                <label for="gastosAdm">Gastos Administrativos</label>
                                <input type="number" id="gastosAdm" name="gastosAdm" step="0.01" placeholder="0.00">
                            </div>
                            <div class="form-group">
                                <label for="gastosAdm_codigo">Código</label>
                                <input type="text" id="gastosAdm_codigo" name="gastosAdm_codigo" list="listaCodigosContables" placeholder="Código">
                                <datalist id="listaCodigosContables">
                                    <?php foreach ($codigoContable as $codigo): ?>
                                        <option value="<?= $escape($codigo['code']) ?>">
                                            <?= $escape($codigo['code']) ?> - <?= $escape($codigo['name']) ?>
                                        </option>
                                    <?php endforeach; ?>
                                </datalist>
                            </div>
                            <div class="form-group">
                                <label for="gastosAdm_tipo">Tipo</label>
                                <select id="gastosAdm_tipo" name="gastosAdm_tipo">
                                    <option value="">-</option>
                                    <option value="debe">Debe</option>
                                    <option value="haber">Haber</option>
                                </select>
                            </div>
                        </div>

                        <div class="form-row">
                            <div class="form-group">
                                <label for="gastosMora">Gastos de Mora</label>
                                <input type="number" id="gastosMora" name="gastosMora" step="0.01" placeholder="0.00">
                            </div>
                            <div class="form-group">
                                <label for="gastosMora_codigo">Código</label>
                                <input type="text" id="gastosMora_codigo" name="gastosMora_codigo" list="listaCodigosContables" placeholder="Código">
                                <datalist id="listaCodigosContables">
                                    <?php foreach ($codigoContable as $codigo): ?>
                                        <option value="<?= $escape($codigo['code']) ?>">
                                            <?= $escape($codigo['code']) ?> - <?= $escape($codigo['name']) ?>
                                        </option>
                                    <?php endforeach; ?>
                                </datalist>
                            </div>
                            <div class="form-group">
                                <label for="gastosMora_tipo">Tipo</label>
                                <select id="gastosMora_tipo" name="gastosMora_tipo">
                                    <option value="">-</option>
                                    <option value="debe">Debe</option>
                                    <option value="haber">Haber</option>
                                </select>
                            </div>
                        </div>

                        <div class="form-row">
                            <div class="form-group">
                                <label for="otros1">Otros 1</label>
                                <input type="number" id="otros1" name="otros1" step="0.01" placeholder="0.00">
                            </div>
                            <div class="form-group">
                                <label for="otros1_codigo">Código</label>
                                <input type="text" id="otros1_codigo" name="otros1_codigo" list="listaCodigosContables" placeholder="Código">
                                <datalist id="listaCodigosContables">
                                    <?php foreach ($codigoContable as $codigo): ?>
                                        <option value="<?= $escape($codigo['code']) ?>">
                                            <?= $escape($codigo['code']) ?> - <?= $escape($codigo['name']) ?>
                                        </option>
                                    <?php endforeach; ?>
                                </datalist>
                            </div>
                            <div class="form-group">
                                <label for="otros1_tipo">Tipo</label>
                                <select id="otros1_tipo" name="otros1_tipo">
                                    <option value="">-</option>
                                    <option value="debe">Debe</option>
                                    <option value="haber">Haber</option>
                                </select>
                            </div>
                        </div>

                        <div class="form-row">
                            <div class="form-group">
                                <label for="otros2">Otros 2</label>
                                <input type="number" id="otros2" name="otros2" step="0.01" placeholder="0.00">
                            </div>
                            <div class="form-group">
                                <label for="otros2_codigo">Código</label>
                                <input type="text" id="otros2_codigo" name="otros2_codigo" list="listaCodigosContables" placeholder="Código">
                                <datalist id="listaCodigosContables">
                                    <?php foreach ($codigoContable as $codigo): ?>
                                        <option value="<?= $escape($codigo['code']) ?>">
                                            <?= $escape($codigo['code']) ?> - <?= $escape($codigo['name']) ?>
                                        </option>
                                    <?php endforeach; ?>
                                </datalist>
                            </div>
                            <div class="form-group">
                                <label for="otros2_tipo">Tipo</label>
                                <select id="otros2_tipo" name="otros2_tipo">
                                    <option value="">-</option>
                                    <option value="debe">Debe</option>
                                    <option value="haber">Haber</option>
                                </select>
                            </div>
                        </div>

                        <div class="form-row">
                            <div class="form-group">
                                <label for="monto">Monto Total</label>
                                <input type="number" id="monto" name="monto" step="0.01" required placeholder="0.00">
                            </div>
                            <div class="form-group">
                                <label for="monto_codigo">Código</label>
                                <input type="text" id="monto_codigo" name="monto_codigo" list="listaCodigosContables" placeholder="Código">
                                <datalist id="listaCodigosContables">
                                    <?php foreach ($codigoContable as $codigo): ?>
                                        <option value="<?= $escape($codigo['code']) ?>">
                                            <?= $escape($codigo['code']) ?> - <?= $escape($codigo['name']) ?>
                                        </option>
                                    <?php endforeach; ?>
                                </datalist>
                            </div>
                            <div class="form-group">
                                <label for="monto_tipo">Tipo</label>
                                <select id="monto_tipo" name="monto_tipo">
                                    <option value="">-</option>
                                    <option value="debe">Debe</option>
                                    <option value="haber">Haber</option>
                                </select>
                            </div>
                        </div>

                        <div class="form-row">
                            <div class="form-group">
                                <label for="formaPago">Forma de Pago</label>
                                <select id="formaPago" name="formaPago" required>
                                    <option value="">Seleccione</option>
                                    <option value="EFECTIVO">Efectivo</option>
                                    <option value="DEPOSITO">Depósito</option>
                                    <option value="TRANSFERENCIA">Transferencia</option>
                                    <option value="CHEQUE">Cheque</option>
                                </select>
                            </div>
                        </div>
                        </div>

                        <div class="form-group">
                            <label for="obs">Observación</label>
                            <textarea id="obs" name="obs" rows="3"></textarea>
                        </div>

                        <div class="form-group">
                            <label for="obs2">Observación 2</label>
                            <textarea id="obs2" name="obs2" rows="2"></textarea>
                        </div>

                        <div class="form-actions">
                            <button type="submit" class="btn btn-primary">Registrar Pago</button>
                            <a href="index.php?module=caja&profile=client&client=<?= rawurlencode($clienteCodigo) ?>" class="btn btn-secondary">Cancelar</a>
                        </div>
                    </form>
                </section>
            </div>

            <script>
        function cargarDatosPrestamo() {
            const select = document.getElementById('prestamo');
            const option = select.options[select.selectedIndex];
            
            if (option.value) {
                document.getElementById('sobregiro').value = option.dataset.sobregiro || '0';
                document.getElementById('gastosAdm').value = option.dataset.gastosAdm || '0';
                document.getElementById('gastosMora').value = option.dataset.mora || '0';
                document.getElementById('otros1').value = option.dataset.otros1 || '0';
                document.getElementById('otros2').value = option.dataset.otros2 || '0';
                
                // Calcular monto total
                calcularMontoTotal();
                
                // Cargar códigos contables automáticamente
                cargarCodigosContables();
            }
        }

        function calcularMontoTotal() {
            const sobregiro = parseFloat(document.getElementById('sobregiro').value) || 0;
            const descuenta = parseFloat(document.getElementById('descuenta').value) || 0;
            const gastosAdm = parseFloat(document.getElementById('gastosAdm').value) || 0;
            const gastosMora = parseFloat(document.getElementById('gastosMora').value) || 0;
            const otros1 = parseFloat(document.getElementById('otros1').value) || 0;
            const otros2 = parseFloat(document.getElementById('otros2').value) || 0;

            const total = sobregiro + descuenta + gastosAdm + gastosMora + otros1 + otros2;
            document.getElementById('monto').value = total.toFixed(2);
        }

        // Función para obtener el nombre completo del código contable del datalist
        function obtenerNombreCodigoContable(codigoBuscado) {
            const datalist = document.getElementById('listaCodigosContables');
            const options = datalist.options;
            
            for (let i = 0; i < options.length; i++) {
                const optionValue = options[i].value;
                if (optionValue === codigoBuscado || optionValue.startsWith(codigoBuscado + ' - ')) {
                    return optionValue;
                }
            }
            return codigoBuscado;
        }

        // Función para obtener el nombre completo del código contable del datalist
    function obtenerNombreCodigoContable(codigo) {
        const datalist = document.getElementById('listaCodigosContables');
        if (!datalist) {
            console.error('Datalist listaCodigosContables no encontrado');
            return codigo;
        }
        for (const option of datalist.options) {
            // Buscar si el código está al inicio del valor del datalist
            if (option.value.startsWith(codigo) || option.value === codigo) {
                return option.value;
            }
        }
        // Si no se encuentra exactamente, buscar el código base sin el último dígito
        if (codigo.length > 1) {
            const codigoBase = codigo.slice(0, -1);
            for (const option of datalist.options) {
                if (option.value.startsWith(codigoBase)) {
                    return option.value; // Retornar el código completo del datalist
                }
            }
        }
        return codigo; // Si no se encuentra, retorna solo el código
    }

    // Función para actualizar el código contable con formato completo "código - nombre"
    function actualizarCodigoCompleto(codigoInputId) {
        const codigoInput = document.getElementById(codigoInputId);
        const codigo = codigoInput.value.trim();

        if (!codigo) {
            return;
        }

        // Si el código ya está en formato completo, no hacer nada
        if (codigo.includes(' - ')) {
            return;
        }

        // Buscar el nombre en el datalist
        const datalist = document.getElementById('listaCodigosContables');
        let nombreEncontrado = '';

        for (const option of datalist.options) {
            if (option.value === codigo) {
                nombreEncontrado = option.textContent.split(' - ')[1] || '';
                break;
            }
        }

        // Si se encontró el nombre, actualizar el valor
        if (nombreEncontrado) {
            codigoInput.value = codigo + ' - ' + nombreEncontrado;
        }
    }

    // Función para obtener el código contable según la condición del cliente
    function obtenerCodigoSegunCondicion(codigoBase) {
        // Si la condición es "SOCIO", no agregar el último dígito
        if (clienteCondicion === 'SOCIO') {
            return codigoBase.slice(0, -1); // Eliminar el último dígito (ej: 1651 → 165)
        }
        // Si la condición es "ACTIVO", mantener el código completo (termina en 1)
        if (clienteCondicion === 'ACTIVO') {
            return codigoBase; // Mantener (ej: 1651 → 1651)
        }
        // Si la condición es "PENSIONISTA", cambiar último dígito a 2
        if (clienteCondicion === 'PENSIONISTA') {
            return codigoBase.slice(0, -1) + '2'; // (ej: 1651 → 1652)
        }
        // Si la condición es "FEBAN", cambiar último dígito a 3
        if (clienteCondicion === 'FEBAN') {
            return codigoBase.slice(0, -1) + '3'; // (ej: 1651 → 1653)
        }
        // Si la condición es "OTROS SOCIOS", cambiar último dígito a 4
        if (clienteCondicion === 'OTROS SOCIOS') {
            return codigoBase.slice(0, -1) + '4'; // (ej: 1651 → 1654)
        }
        // Por defecto, mantener el código completo
        return codigoBase;
    }

        // Función para cargar códigos contables automáticamente
        function cargarCodigosContables() {
            const sobregiro = parseFloat(document.getElementById('sobregiro').value) || 0;
            const descuenta = parseFloat(document.getElementById('descuenta').value) || 0;
            const gastosAdm = parseFloat(document.getElementById('gastosAdm').value) || 0;
            const gastosMora = parseFloat(document.getElementById('gastosMora').value) || 0;
            const otros1 = parseFloat(document.getElementById('otros1').value) || 0;
            const otros2 = parseFloat(document.getElementById('otros2').value) || 0;
            const formaPago = document.getElementById('formaPago').value;

            // Sobregiro - Código 1651 si monto > 0
            if (sobregiro > 0) {
                const codigoBase = '1651';
                const codigoFinal = obtenerCodigoSegunCondicion(codigoBase);
                const codigoCompleto = obtenerNombreCodigoContable(codigoFinal);
                document.getElementById('sobregiro_codigo').value = codigoCompleto;
                document.getElementById('sobregiro_tipo').value = 'haber';
                actualizarCodigoCompleto('sobregiro_codigo'); // Mostrar nombre completo
                console.log('Sobregiro código cargado:', codigoCompleto, '(Condición:', clienteCondicion, ')');
            }

            // Amortización/Descuenta - Código 1651 si monto > 0
            if (descuenta > 0) {
                const codigoBase = '1651';
                const codigoFinal = obtenerCodigoSegunCondicion(codigoBase);
                const codigoCompleto = obtenerNombreCodigoContable(codigoFinal);
                document.getElementById('descuenta_codigo').value = codigoCompleto;
                document.getElementById('descuenta_tipo').value = 'haber';
                actualizarCodigoCompleto('descuenta_codigo'); // Mostrar nombre completo
                console.log('Descuenta código cargado:', codigoCompleto, '(Condición:', clienteCondicion, ')');
            }

            // Gastos Administrativos - Código 7021 si monto > 0
            if (gastosAdm > 0) {
                const codigoBase = '7021';
                const codigoFinal = obtenerCodigoSegunCondicion(codigoBase);
                const codigoCompleto = obtenerNombreCodigoContable(codigoFinal);
                document.getElementById('gastosAdm_codigo').value = codigoCompleto;
                document.getElementById('gastosAdm_tipo').value = 'haber';
                actualizarCodigoCompleto('gastosAdm_codigo'); // Mostrar nombre completo
                console.log('Gastos Adm código cargado:', codigoCompleto, '(Condición:', clienteCondicion, ')');
            }

            // Mora - Código 7041 si monto > 0
            if (gastosMora > 0) {
                const codigoBase = '7041';
                const codigoFinal = obtenerCodigoSegunCondicion(codigoBase);
                const codigoCompleto = obtenerNombreCodigoContable(codigoFinal);
                document.getElementById('gastosMora_codigo').value = codigoCompleto;
                document.getElementById('gastosMora_tipo').value = 'haber';
                actualizarCodigoCompleto('gastosMora_codigo'); // Mostrar nombre completo
                console.log('Mora código cargado:', codigoCompleto, '(Condición:', clienteCondicion, ')');
            }

            // Otros 1 - Código 46951 si monto > 0
            if (otros1 > 0) {
                const codigoBase = '46951';
                const codigoFinal = obtenerCodigoSegunCondicion(codigoBase);
                const codigoCompleto = obtenerNombreCodigoContable(codigoFinal);
                document.getElementById('otros1_codigo').value = codigoCompleto;
                document.getElementById('otros1_tipo').value = 'haber';
                actualizarCodigoCompleto('otros1_codigo'); // Mostrar nombre completo
                console.log('Otros1 código cargado:', codigoCompleto, '(Condición:', clienteCondicion, ')');
            }

            // Otros 2 - Código 46951 si monto > 0
            if (otros2 > 0) {
                const codigoBase = '46951';
                const codigoFinal = obtenerCodigoSegunCondicion(codigoBase);
                const codigoCompleto = obtenerNombreCodigoContable(codigoFinal);
                document.getElementById('otros2_codigo').value = codigoCompleto;
                document.getElementById('otros2_tipo').value = 'haber';
                actualizarCodigoCompleto('otros2_codigo'); // Mostrar nombre completo
                console.log('Otros2 código cargado:', codigoCompleto, '(Condición:', clienteCondicion, ')');
            }

            // Total - Según forma de pago
            const total = parseFloat(document.getElementById('monto').value) || 0;
            if (total > 0) {
                let codigoTotal = '';
                if (formaPago === 'EFECTIVO') {
                    codigoTotal = '1011';
                } else if (formaPago === 'DEPOSITO' || formaPago === 'TRANSFERENCIA' || formaPago === 'CHEQUE' || formaPago === 'PLANILLA') {
                    codigoTotal = '10612';
                }
                
                if (codigoTotal) {
                    const codigoCompleto = obtenerNombreCodigoContable(codigoTotal);
                    document.getElementById('monto_codigo').value = codigoCompleto;
                    document.getElementById('monto_tipo').value = 'debe';
                    actualizarCodigoCompleto('monto_codigo'); // Mostrar nombre completo
                    console.log('Total código cargado:', codigoCompleto);
                }
            }
        }

        // Agregar event listeners para recalcular monto
        ['sobregiro', 'descuenta', 'gastosAdm', 'gastosMora', 'otros1', 'otros2'].forEach(id => {
            document.getElementById(id).addEventListener('input', calcularMontoTotal);
        });

        // Event listener para recargar códigos contables cuando cambia la forma de pago
        document.getElementById('formaPago').addEventListener('change', cargarCodigosContables);
    </script>
        </main>
    </div>
</body>
</html>
