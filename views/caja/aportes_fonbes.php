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
$stmt = $database->prepare("
    SELECT
        CAST(c.F02_ID_CLIENTE AS UNSIGNED) AS client_id,
        TRIM(CONCAT_WS(' ', p.F01_APE_PAT, p.F01_APE_MAT, p.F01_NOMB_PRI, p.F01_NOMB_SEG)) AS full_name,
        p.F01_NUM_DOC AS document,
        p.F01_APORTACION AS aporte_fonbes
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

// Obtener aportes pendientes
$stmt = $database->prepare("
    SELECT f22_periodo AS period, f22_aportacionP AS planned, f22_aportacionD AS paid, f22_fechaPago AS paid_date
    FROM f22_aportes
    WHERE f22_socio = :client
    ORDER BY f22_periodo DESC
    LIMIT 12
");
$stmt->execute(['client' => $clienteCodigo]);
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
    <title>Pago de Aportes FONBES - SACS Nuevo</title>
    <style>
        :root { --ink: #17212b; --muted: #687684; --paper: #f5f7f4; --panel: #fff; --navy: #142b3a; --teal: #1d7a78; --gold: #d69e47; --line: #dce4e1; }
        * { box-sizing: border-box; } body { margin: 0; background: var(--paper); color: var(--ink); font-family: "Trebuchet MS", sans-serif; } a { color: inherit; text-decoration: none; }
        .top { display: flex; align-items: center; justify-content: space-between; padding: 22px clamp(20px, 4vw, 58px); background: var(--navy); color: #fff; } .brand { font-family: Georgia, serif; font-size: 24px; } .brand small { display: block; margin-top: 4px; color: #9bb3b7; font-family: "Trebuchet MS", sans-serif; font-size: 10px; letter-spacing: .13em; text-transform: uppercase; } .back { color: #b9d2d2; font-size: 13px; }
        main { max-width: 1250px; margin: 0 auto; padding: 38px clamp(20px, 4vw, 58px); } .eyebrow { margin: 0 0 9px; color: var(--teal); font-size: 11px; font-weight: bold; letter-spacing: .16em; text-transform: uppercase; } h1 { margin: 0; font-family: Georgia, serif; font-size: clamp(32px, 4vw, 48px); font-weight: normal; } .subtitle { margin: 10px 0 28px; color: var(--muted); }
        .summary, .panel { padding: 24px; border: 1px solid var(--line); border-radius: 7px; background: var(--panel); } .summary { display: grid; grid-template-columns: 1.5fr repeat(3, 1fr); gap: 18px; margin-bottom: 18px; } .name { font-family: Georgia, serif; font-size: 24px; } .label { display: block; margin-bottom: 7px; color: var(--muted); font-size: 10px; letter-spacing: .1em; text-transform: uppercase; } .value { font-weight: bold; }
        .layout { display: grid; grid-template-columns: 1.2fr .8fr; gap: 18px; } .panel h2 { margin: 0 0 18px; font-family: Georgia, serif; font-size: 21px; font-weight: normal; } .wide { grid-column: 1 / -1; } table { width: 100%; border-collapse: collapse; font-size: 12px; } th { padding: 10px; background: #edf3f1; color: var(--muted); font-size: 10px; text-align: left; text-transform: uppercase; } td { padding: 11px 10px; border-top: 1px solid var(--line); } .amount { text-align: right; white-space: nowrap; } .state { color: var(--teal); font-weight: bold; } .muted { color: var(--muted); } .empty { color: var(--muted); font-size: 13px; } .actions { display: flex; gap: 10px; margin-bottom: 18px; } .action { padding: 9px 13px; border: 1px solid var(--line); border-radius: 4px; color: var(--teal); font-size: 12px; }
        .form-group { margin-bottom: 16px; } .form-row { display: grid; grid-template-columns: 1fr 1fr; gap: 16px; margin-bottom: 16px; } label { display: block; margin-bottom: 6px; font-size: 12px; color: var(--muted); } input, select, textarea { width: 100%; padding: 10px; border: 1px solid var(--line); border-radius: 4px; font-size: 14px; } .form-actions { display: flex; gap: 10px; margin-top: 24px; } .btn { padding: 10px 20px; background: var(--teal); color: #fff; border: none; border-radius: 4px; cursor: pointer; } .btn-secondary { background: var(--muted); } .alert { padding: 12px; border-radius: 4px; margin-bottom: 16px; } .alert-error { background: #fee; color: #c33; }
        @media (max-width: 850px) { .summary, .layout { grid-template-columns: 1fr 1fr; } .wide { grid-column: 1 / -1; } } @media (max-width: 560px) { .top { align-items: flex-start; flex-direction: column; gap: 12px; } .summary, .layout { grid-template-columns: 1fr; } .wide { grid-column: auto; } }
    </style>
</head>
<body>
<header class="top"><a class="brand" href="index.php">SACS<small>gestion social</small></a><a class="back" href="index.php?module=caja&profile=client&client=<?= rawurlencode($clienteCodigo) ?>">Volver a Cliente</a></header>
<main>
    <p class="eyebrow">Caja / Pagos</p>
    <h1>Pago de Aportes FONBES</h1>
    <p class="subtitle">Registro de pagos de aportes FONBES por periodo.</p>

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
        <h2>Aportes Pendientes</h2>
        <?php if (empty($aportes)): ?>
            <p class="empty">No hay aportes registrados</p>
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
                    <input type="text" id="monto" name="monto" step="0.01" required placeholder="0.00">
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

            <div class="form-row">
                <div class="form-group">
                    <label for="codigoContable">Código Contable</label>
                    <input type="text" id="codigoContable" name="codigoContable" list="listaCodigosContables" value="1651" placeholder="Escriba o seleccione código">
                    <datalist id="listaCodigosContables">
                        <?php foreach ($codigoContable as $codigo): ?>
                            <option value="<?= $escape($codigo['code']) ?>">
                                <?= $escape($codigo['code']) ?> - <?= $escape($codigo['name']) ?>
                            </option>
                        <?php endforeach; ?>
                    </datalist>
                </div>
                <div class="form-group">
                    <label for="tipoAsiento">Tipo de Asiento</label>
                    <select id="tipoAsiento" name="tipoAsiento">
                        <option value="haber">Haber (Ingreso)</option>
                        <option value="debe">Debe (Egreso)</option>
                    </select>
                </div>
            </div>

            <div class="form-group">
                <label for="descripcionContable">Descripción Contable</label>
                <input type="text" id="descripcionContable" name="descripcionContable" value="PAGO APORTES FONBES">
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
</body>
</html>
