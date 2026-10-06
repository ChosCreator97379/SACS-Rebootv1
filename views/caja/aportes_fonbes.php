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
$filtroAportes = trim((string) ($_GET['filtro'] ?? 'todos')); // todos, pagados, pendientes

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
        p.F01_APORTACION AS aporte_fonbes,
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

// Obtener aportes según filtro
$whereClause = "WHERE f22_socio = :client";
$params = ['client' => $clienteCodigo];

if ($filtroAportes === 'pagados') {
    $whereClause .= " AND f22_aportacionD > 0 AND f22_aportacionD >= f22_aportacionP";
} elseif ($filtroAportes === 'pendientes') {
    $whereClause .= " AND (f22_aportacionD = 0 OR f22_aportacionD < f22_aportacionP)";
}

$stmt = $database->prepare("
    SELECT f22_periodo AS period, f22_aportacionP AS planned, f22_aportacionD AS paid, f22_fechaPago AS paid_date
    FROM f22_aportes
    $whereClause
    ORDER BY f22_periodo DESC
    LIMIT 12
");
$stmt->execute($params);
$aportes = $stmt->fetchAll();

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
    <title>Pago de Aportes FONBES | SACS Nuevo</title>
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
        .alert { padding: 15px; margin-bottom: 20px; border-radius: 6px; }
        .alert-error { background: #fee; color: #c33; border: 1px solid #fcc; }
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
                <a href="index.php?module=caja&action=aportes_fonbes_form&client=<?= rawurlencode($clienteCodigo) ?>" class="nav-link active">Aportes FONBES</a>
                <a href="index.php?module=caja&action=aportes_coopam_form&client=<?= rawurlencode($clienteCodigo) ?>" class="nav-link">Aportes COOPAM</a>
                <a href="index.php?module=caja&action=aportes_combinados_form&client=<?= rawurlencode($clienteCodigo) ?>" class="nav-link">Pago FONBES + COOPAM</a>
                <a href="index.php?module=caja&action=desembolso_prestamo_form&client=<?= rawurlencode($clienteCodigo) ?>" class="nav-link">Desembolso Préstamo</a>
                <a href="index.php?module=caja&action=sobregiro_prestamo_form&client=<?= rawurlencode($clienteCodigo) ?>" class="nav-link">Sobregiro Préstamo</a>
                <a href="index.php?module=caja&action=amortizar_form&client=<?= rawurlencode($clienteCodigo) ?>" class="nav-link">Amortización</a>
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
                <h1>Pago de Aportes FONBES</h1>
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
                <div><span class="label">Aporte FONBES</span><div class="value"><?= $money($cliente['aporte_fonbes']) ?></div></div>
            </section>

    <section class="panel">
        <h2>Aportes</h2>
        <div class="form-row" style="margin-bottom: 16px;">
            <div class="form-group">
                <label for="filtro">Filtrar por:</label>
                <select id="filtro" name="filtro" onchange="aplicarFiltro()">
                    <option value="todos" <?= $filtroAportes === 'todos' ? 'selected' : '' ?>>Todos</option>
                    <option value="pagados" <?= $filtroAportes === 'pagados' ? 'selected' : '' ?>>Pagados (completos)</option>
                    <option value="pendientes" <?= $filtroAportes === 'pendientes' ? 'selected' : '' ?>>Pendientes (incompletos)</option>
                </select>
            </div>
        </div>
        <?php if (empty($aportes)): ?>
            <p class="empty">No hay aportes registrados con este filtro</p>
        <?php else: ?>
            <table>
                <thead>
                    <tr>
                        <th>Periodo</th>
                        <th>Planificado</th>
                        <th>Pagado</th>
                        <th>Fecha Pago</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($aportes as $aporte): ?>
                        <tr>
                            <td><?= $escape($aporte['period']) ?></td>
                            <td class="amount"><?= $money($aporte['planned']) ?></td>
                            <td class="amount"><?= $money($aporte['paid']) ?></td>
                            <td><?= $escape($aporte['paid_date']) ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php endif; ?>
    </section>

    <section class="panel">
        <h2>Registrar Pago</h2>
        <form action="index.php" method="POST">
            <input type="hidden" name="csrf_token" value="<?= $escape($csrfToken) ?>">
            <input type="hidden" name="module" value="caja">
            <input type="hidden" name="action" value="aportes_fonbes">
            <input type="hidden" name="operacion" value="1">
            <input type="hidden" name="cliente" value="<?= $escape($clienteCodigo) ?>">

            <h3>Periodo de Pago</h3>
            <div class="form-row">
                <div class="form-group">
                    <label for="anio">Año</label>
                    <input type="number" id="anio" name="anio" value="<?= date('Y') ?>" required min="2000" max="2100">
                </div>
                <div class="form-group">
                    <label for="periodo">Mes Inicio</label>
                    <select id="periodo" name="periodo" required>
                        <?php for ($i = 1; $i <= 12; $i++): ?>
                            <option value="<?= $i ?>" <?= $i === (int) date('m') ? 'selected' : '' ?>>
                                <?= str_pad((string) $i, 2, '0', STR_PAD_LEFT) ?>
                            </option>
                        <?php endfor; ?>
                    </select>
                </div>
                <div class="form-group">
                    <label for="periodo2">Mes Fin</label>
                    <select id="periodo2" name="periodo2" required>
                        <?php for ($i = 1; $i <= 12; $i++): ?>
                            <option value="<?= $i ?>" <?= $i === (int) date('m') ? 'selected' : '' ?>>
                                <?= str_pad((string) $i, 2, '0', STR_PAD_LEFT) ?>
                            </option>
                        <?php endfor; ?>
                    </select>
                </div>
            </div>

            <h3>Datos del Pago</h3>
            <div class="form-row">
                <div class="form-group">
                    <label for="monto">Monto Total</label>
                    <input type="text" id="monto" name="monto" step="0.01" required placeholder="0.00" readonly>
                </div>
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

            <input type="hidden" id="montoAporteFonbes" value="<?= $money($cliente['aporte_fonbes']) ?>">
            <input type="hidden" id="clienteCondicion" value="<?= $escape($cliente['condicion'] ?? '') ?>">

            <h3>Códigos Contables</h3>
            <div class="form-row">
                <div class="form-group">
                    <label for="aporte_codigo">Aporte FONBES - Código</label>
                    <input type="text" id="aporte_codigo" name="aporte_codigo" list="listaCodigosContables" placeholder="Código automático">
                    <datalist id="listaCodigosContables">
                        <?php foreach ($codigoContable as $codigo): ?>
                            <option value="<?= $escape($codigo['code']) ?>">
                                <?= $escape($codigo['code']) ?> - <?= $escape($codigo['name']) ?>
                            </option>
                        <?php endforeach; ?>
                    </datalist>
                </div>
                <div class="form-group">
                    <label for="aporte_tipo">Aporte FONBES - Tipo</label>
                    <select id="aporte_tipo" name="aporte_tipo">
                        <option value="haber">Haber</option>
                        <option value="debe">Debe</option>
                    </select>
                </div>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label for="total_codigo">Total - Código</label>
                    <input type="text" id="total_codigo" name="total_codigo" list="listaCodigosContables" placeholder="Código automático">
                </div>
                <div class="form-group">
                    <label for="total_tipo">Total - Tipo</label>
                    <select id="total_tipo" name="total_tipo">
                        <option value="debe">Debe</option>
                        <option value="haber">Haber</option>
                    </select>
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
                <button type="submit" class="btn">Grabar Pago de Aportes</button>
                <a href="index.php?module=caja&profile=client&client=<?= $escape($clienteCodigo) ?>" class="btn btn-secondary">Cancelar</a>
            </div>
        </form>
    </section>
</main>

<script>
    function aplicarFiltro() {
        const filtro = document.getElementById('filtro').value;
        const url = new URL(window.location.href);
        url.searchParams.set('filtro', filtro);
        window.location.href = url.toString();
    }

    function calcularTotalAportes() {
        const mesInicio = parseInt(document.getElementById('periodo').value) || 0;
        const mesFin = parseInt(document.getElementById('periodo2').value) || 0;
        const montoAporte = parseFloat(document.getElementById('montoAporteFonbes').value.replace(/,/g, '')) || 0;

        if (mesInicio > 0 && mesFin > 0 && montoAporte > 0) {
            const numMeses = (mesFin - mesInicio) + 1;
            if (numMeses > 0) {
                const total = numMeses * montoAporte;
                document.getElementById('monto').value = total.toFixed(2);
            } else {
                document.getElementById('monto').value = '0.00';
            }
        } else {
            document.getElementById('monto').value = '0.00';
        }
    }

    function obtenerCodigoSegunCondicion(codigoBase, condicion) {
        if (!condicion) return codigoBase;

        const condicionUpper = condicion.toUpperCase().trim();

        if (condicionUpper === 'SOCIO') {
            return '46933';
        } else if (condicionUpper === 'ACTIVO') {
            return '469331';
        } else if (condicionUpper === 'PENSIONISTA') {
            return '469332';
        } else if (condicionUpper === 'FEBAN') {
            return '469333';
        } else if (condicionUpper === 'OTROS SOCIOS') {
            return '469334';
        } else {
            return '46933'; // Default
        }

        return codigoBase;
    }

    function obtenerNombreCodigoContable(codigo) {
        const datalist = document.getElementById('listaCodigosContables');
        if (!datalist) return '';

        for (const option of datalist.options) {
            const optionValue = option.value.trim();
            if (optionValue.startsWith(codigo) || optionValue.startsWith(codigo + ' ')) {
                return optionValue;
            }
        }

        // Si no encuentra exactamente, buscar el código base sin el último dígito
        if (codigo.length > 1) {
            const codigoBase = codigo.slice(0, -1);
            for (const option of datalist.options) {
                const optionValue = option.value.trim();
                if (optionValue.startsWith(codigoBase) || optionValue.startsWith(codigoBase + ' ')) {
                    return optionValue;
                }
            }
        }

        return codigo;
    }

    function actualizarCodigoCompleto(inputId) {
        const input = document.getElementById(inputId);
        if (!input) return;

        const codigo = input.value.trim();
        if (codigo) {
            const nombreCompleto = obtenerNombreCodigoContable(codigo);
            if (nombreCompleto !== codigo) {
                input.value = nombreCompleto;
            }
        }
    }

    function cargarCodigosContablesAutomaticos() {
        const clienteCondicion = document.getElementById('clienteCondicion').value;
        const formaPago = document.getElementById('formaPago').value;
        const monto = parseFloat(document.getElementById('monto').value.replace(/,/g, '')) || 0;

        console.log('Cargando códigos contables. Forma de pago:', formaPago, 'Condición:', clienteCondicion);

        // Aporte FONBES - Código 46933 según condición
        const codigoAporte = obtenerCodigoSegunCondicion('46933', clienteCondicion);
        document.getElementById('aporte_codigo').value = codigoAporte;
        document.getElementById('aporte_tipo').value = 'haber';
        actualizarCodigoCompleto('aporte_codigo');

        // Total - Según forma de pago
        let codigoTotal;
        if (formaPago === 'EFECTIVO') {
            codigoTotal = '1011';
        } else {
            codigoTotal = '10612';
        }

        document.getElementById('total_codigo').value = codigoTotal;
        document.getElementById('total_tipo').value = 'debe';
        actualizarCodigoCompleto('total_codigo');

        console.log('Código aporte determinado:', codigoAporte);
        console.log('Código total determinado:', codigoTotal);
    }

    // Event listeners para recalcular total cuando cambian los meses
    document.getElementById('periodo').addEventListener('change', calcularTotalAportes);
    document.getElementById('periodo2').addEventListener('change', calcularTotalAportes);

    // Event listener para cargar códigos contables cuando cambia la forma de pago
    document.getElementById('formaPago').addEventListener('change', cargarCodigosContablesAutomaticos);

    // Calcular total y cargar códigos contables al cargar la página
    document.addEventListener('DOMContentLoaded', function() {
        calcularTotalAportes();
        cargarCodigosContablesAutomaticos();
    });
</script>
</body>
</html>
