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

// Obtener datos del préstamo y cliente
$clienteCodigo = trim($_GET['client'] ?? '');
$prestamoId = filter_var($_GET['loan'] ?? null, FILTER_VALIDATE_INT, ['options' => ['min_range' => 1]]);

if ($clienteCodigo === '' || $prestamoId === false || $prestamoId === null) {
    http_response_code(400);
    $error = 'Datos inválidos';
    require dirname(__DIR__) . '/home.php';
    exit;
}

// Obtener datos del cliente
$cliente = $repository->findClientProfile($clienteCodigo);
if ($cliente === null) {
    http_response_code(404);
    $error = 'Cliente no encontrado';
    require dirname(__DIR__) . '/home.php';
    exit;
}

// Obtener datos del préstamo
$stmt = $database->prepare("SELECT * FROM f16_prestamo WHERE f16_codigo = ? AND f16_estado = 4");
$stmt->execute([$prestamoId]);
$prestamo = $stmt->fetch();

if ($prestamo === false) {
    http_response_code(404);
    $error = 'Préstamo no encontrado o no está activo';
    require dirname(__DIR__) . '/home.php';
    exit;
}

// Obtener cuotas vigentes del préstamo
$stmt = $database->prepare("SELECT * FROM f17_cuotasprestamo WHERE f17_prestamo = ? AND f17_estado = 'VIGENTE' AND f17_ncuota > 0 ORDER BY f17_fecha, f17_ncuota LIMIT 50");
$stmt->execute([$prestamoId]);
$cuotas = $stmt->fetchAll();

$displayName = htmlspecialchars($user['username'], ENT_QUOTES, 'UTF-8');
$profile = htmlspecialchars((string) $user['profile'], ENT_QUOTES, 'UTF-8');
$userId = htmlspecialchars((string) $user['id'], ENT_QUOTES, 'UTF-8');
$supervisor = $user['supervisor'] === 'S';
$csrfToken = (string) $_SESSION['csrf_token'];

// Funciones de formato
$escape = static fn (mixed $value): string => htmlspecialchars((string) ($value ?? ''), ENT_QUOTES, 'UTF-8');
$money = static fn (mixed $value): string => $value === null || $value === '' ? '-' : number_format((float) $value, 2, '.', ',');
?>

<!doctype html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Amortización AEE | SACS Nuevo</title>
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
        .brand { display: flex; align-items: center; gap: 12px; padding: 0 12px 34px; }
        .brand-mark { display: grid; place-items: center; width: 38px; height: 38px; border: 2px solid var(--gold); border-radius: 50%; color: var(--gold); font-family: Georgia, serif; font-size: 20px; }
        .brand-name { font-family: Georgia, serif; font-size: 22px; letter-spacing: .02em; }
        .brand-note { display: block; margin-top: 3px; color: #9bb3b7; font-size: 11px; letter-spacing: .12em; text-transform: uppercase; }
        .nav-label { padding: 0 12px 10px; color: #86a6ad; font-size: 10px; font-weight: bold; letter-spacing: .14em; text-transform: uppercase; }
        .nav { display: grid; gap: 5px; }
        .nav-link { display: flex; align-items: center; gap: 11px; padding: 11px 12px; border-left: 3px solid transparent; border-radius: 4px; color: #b8ccce; font-size: 14px; }
        .nav-link:hover, .nav-link.active { border-left-color: var(--gold); background: var(--navy-soft); color: #fff; }
        .nav-icon { width: 19px; color: #9dbfc1; font-size: 12px; text-align: center; }
        .sidebar-footer { margin-top: auto; padding: 18px 12px 0; border-top: 1px solid rgba(214, 230, 226, .14); color: #9bb3b7; font-size: 12px; line-height: 1.5; }
        .main { min-width: 0; padding: 27px clamp(22px, 4vw, 58px) 42px; }
        .topbar { display: flex; align-items: center; justify-content: space-between; gap: 20px; margin-bottom: 45px; }
        .breadcrumb { color: var(--muted); font-size: 12px; letter-spacing: .08em; text-transform: uppercase; }
        .user-menu { display: flex; align-items: center; gap: 12px; }
        .avatar { display: grid; place-items: center; width: 38px; height: 38px; border-radius: 50%; background: var(--teal); color: #fff; font-family: Georgia, serif; font-size: 17px; }
        .user-copy { line-height: 1.2; text-align: right; }
        .user-name { display: block; font-size: 14px; font-weight: bold; }
        .user-role { display: block; margin-top: 3px; color: var(--muted); font-size: 11px; }
        .logout { margin-left: 8px; color: var(--teal); font-size: 12px; font-weight: bold; }
        .eyebrow { margin: 0 0 10px; color: var(--teal); font-size: 12px; font-weight: bold; letter-spacing: .16em; text-transform: uppercase; }
        h1 { max-width: 680px; margin: 0; font-family: Georgia, serif; font-size: clamp(32px, 4vw, 52px); font-weight: normal; line-height: 1.05; }
        .intro { max-width: 590px; margin: 15px 0 34px; color: var(--muted); font-size: 15px; line-height: 1.6; }
        .info-box { padding: 25px; border: 1px solid var(--line); background: var(--panel); border-radius: 7px; box-shadow: var(--shadow); margin-bottom: 20px; }
        .info-box h3 { margin: 0 0 15px; font-family: Georgia, serif; font-size: 22px; font-weight: normal; }
        .info-box p { margin: 0 0 8px; font-size: 14px; }
        .info-box p:last-child { margin-bottom: 0; }
        .info-box strong { color: var(--ink); }
        .form-row { display: grid; grid-template-columns: repeat(2, minmax(0, 1fr)); gap: 15px; margin-bottom: 15px; }
        .form-group { margin-bottom: 15px; }
        .form-group label { display: block; margin-bottom: 8px; color: var(--muted); font-size: 11px; letter-spacing: .12em; text-transform: uppercase; font-weight: bold; }
        .form-group input, .form-group select, .form-group textarea { width: 100%; padding: 12px; border: 1px solid #b9c7c7; border-radius: 4px; font: inherit; font-size: 14px; }
        .form-group input:focus, .form-group select:focus, .form-group textarea:focus { outline: none; border-color: var(--teal); }
        .form-group textarea { resize: vertical; min-height: 80px; }
        .form-group input[readonly] { background: #f8f9fa; color: var(--muted); }
        .btn { padding: 12px 20px; border: 0; border-radius: 4px; background: var(--teal); color: #fff; cursor: pointer; font: inherit; font-size: 14px; font-weight: bold; }
        .btn:hover { background: var(--navy); }
        .btn-secondary { background: var(--muted); }
        .btn-secondary:hover { background: var(--navy); }
        .error { padding: 15px; margin-bottom: 20px; border: 1px solid #f8d7da; background: #fde8e8; border-radius: 7px; color: #721c24; font-size: 14px; }
        .cuota-info { margin-top: 15px; padding: 15px; background: #eaf2ef; border-radius: 4px; border: 1px solid var(--line); }
        .cuota-info h4 { margin: 0 0 10px; font-family: Georgia, serif; font-size: 18px; font-weight: normal; }
        .cuota-info p { margin: 0 0 5px; font-size: 13px; }
        @media (max-width: 900px) { .shell { grid-template-columns: 1fr; } .sidebar { padding: 18px; } .brand { padding-bottom: 20px; } .nav { grid-template-columns: repeat(3, minmax(0, 1fr)); } .nav-label, .sidebar-footer { display: none; } .main { padding-top: 24px; } }
        @media (max-width: 650px) { .topbar { align-items: flex-start; flex-direction: column; margin-bottom: 34px; } .user-menu { width: 100%; justify-content: flex-start; } .user-copy { text-align: left; } .form-row { grid-template-columns: 1fr; } h1 { font-size: 38px; } }
    </style>
</head>
<body>
<div class="shell">
    <aside class="sidebar">
        <a class="brand" href="index.php" aria-label="SACS Nuevo, inicio">
            <span class="brand-mark">S</span>
            <span><span class="brand-name">SACS</span><span class="brand-note">gestion social</span></span>
        </a>

        <div class="nav-label">Espacios de trabajo</div>
        <nav class="nav" aria-label="Navegacion principal">
            <a class="nav-link" href="index.php"><span class="nav-icon">01</span>Resumen</a>
            <a class="nav-link" href="index.php?module=caja"><span class="nav-icon">02</span>Caja</a>
            <a class="nav-link" href="#socios"><span class="nav-icon">03</span>Socios</a>
            <a class="nav-link" href="#prestamos"><span class="nav-icon">04</span>Prestamos</a>
        </nav>

        <div class="sidebar-footer">
            Usuario: <?= $escape($user['username']) ?><br>
            Perfil: <?= $escape($profile) ?><br>
            ID: <?= $escape($userId) ?><br>
            Supervisor: <?= $supervisor ? 'Sí' : 'No' ?>
        </div>
    </aside>

    <main class="main">
        <div class="topbar">
            <div class="breadcrumb">Caja / Amortización AEE</div>
            <div class="user-menu">
                <div class="avatar"><?= strtoupper(substr($user['username'], 0, 1)) ?></div>
                <div class="user-copy">
                    <span class="user-name"><?= $escape($user['username']) ?></span>
                    <span class="user-role">Perfil <?= $escape($profile) ?></span>
                </div>
                <a class="logout" href="index.php?logout=1">Cerrar sesión</a>
            </div>
        </div>

        <p class="eyebrow">Operación de caja</p>
        <h1>Amortización AEE</h1>
        <p class="intro">Registro de pago de cuota de préstamo siguiendo el flujo legacy del sistema.</p>

        <?php if (isset($_GET['error'])): ?>
            <div class="error">
                <?= $escape($_GET['error']) ?>
            </div>
        <?php endif; ?>

        <div class="info-box">
            <h3>Información del Cliente</h3>
            <p><strong>Nombre:</strong> <?= $escape($cliente['full_name'] ?? 'Sin nombre') ?></p>
            <p><strong>Documento:</strong> <?= $escape($cliente['document'] ?? '') ?></p>
            <p><strong>Código:</strong> <?= $escape($clienteCodigo) ?></p>
        </div>

        <div class="info-box">
            <h3>Información del Préstamo</h3>
            <p><strong>Código:</strong> <?= $escape((string)($prestamo['f16_codigo'] ?? '')) ?></p>
            <p><strong>Importe:</strong> <?= $money($prestamo['f16_importe'] ?? 0) ?></p>
            <p><strong>Saldo:</strong> <?= $money($prestamo['f16_saldo'] ?? 0) ?></p>
            <p><strong>Cuotas Pendientes:</strong> <?= $escape((string)($prestamo['f16_cuotasPend'] ?? 0)) ?></p>
            <p><strong>Gastos Administrativos:</strong> <?= $money($prestamo['f16_gastosa'] ?? 0) ?></p>
            <p><strong>Mora:</strong> <?= $money($prestamo['f16_gastosm'] ?? 0) ?></p>
            <p><strong>Sobregiro:</strong> <?= $money($prestamo['f16_sobre'] ?? 0) ?></p>
        </div>

        <form action="index.php" method="POST">
            <input type="hidden" name="csrf_token" value="<?= $escape($csrfToken) ?>">
            <input type="hidden" name="module" value="caja">
            <input type="hidden" name="action" value="amortizar">
            <input type="hidden" name="operacion" value="2">
            <input type="hidden" name="cliente" value="<?= $escape($clienteCodigo) ?>">
            <input type="hidden" name="prestamo" value="<?= $escape((string)($prestamoId)) ?>">

            <h3>Selección de Cuota</h3>
            <div class="form-group">
                <label for="cuota">Cuota a Pagar</label>
                <select id="cuota" name="cuota" required>
                    <option value="">-- Seleccione Cuota --</option>
                    <?php foreach ($cuotas as $cuota): ?>
                        <option value="<?= $escape((string)$cuota['f17_ncuota']) ?>"
                                data-capital="<?= $money($cuota['f17_capital'] ?? 0) ?>"
                                data-interes="<?= $money($cuota['f17_interes'] ?? 0) ?>"
                                data-total="<?= $money($cuota['f17_total'] ?? 0) ?>"
                                data-fecha="<?= $escape($cuota['f17_fecha']) ?>">
                            Cuota <?= $escape((string)$cuota['f17_ncuota']) ?> - 
                            <?= $escape($cuota['f17_fecha']) ?> - 
                            Total: <?= $money($cuota['f17_total'] ?? 0) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div id="cuota-info" class="cuota-info" style="display: none;">
                <h4>Detalles de la Cuota Seleccionada</h4>
                <p><strong>Capital:</strong> <span id="capital-display">-</span></p>
                <p><strong>Interés:</strong> <span id="interes-display">-</span></p>
                <p><strong>Total:</strong> <span id="total-display">-</span></p>
                <p><strong>Fecha:</strong> <span id="fecha-display">-</span></p>
            </div>

            <h3>Datos del Pago</h3>
            <div class="form-row">
                <div class="form-group">
                    <label for="amortiza">Amortización (Capital)</label>
                    <input type="text" id="amortiza" name="amortiza" step="0.01" placeholder="0.00">
                </div>
                <div class="form-group">
                    <label for="gastosAdm">Gastos Administrativos</label>
                    <input type="text" id="gastosAdm" name="gastosAdm" step="0.01" value="<?= $money($prestamo['f16_gastosa'] ?? 0) ?>">
                </div>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label for="gastosMora">Mora</label>
                    <input type="text" id="gastosMora" name="gastosMora" step="0.01" value="<?= $money($prestamo['f16_gastosm'] ?? 0) ?>">
                </div>
                <div class="form-group">
                    <label for="otros1">Otros 1</label>
                    <input type="text" id="otros1" name="otros1" step="0.01" value="<?= $money($prestamo['f16_otros'] ?? 0) ?>">
                </div>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label for="otros2">Otros 2</label>
                    <input type="text" id="otros2" name="otros2" step="0.01" value="<?= $money($prestamo['f16_otros2'] ?? 0) ?>">
                </div>
                <div class="form-group">
                    <label for="sobregiro">Sobregiro</label>
                    <input type="text" id="sobregiro" name="sobregiro" step="0.01" value="<?= $money($prestamo['f16_sobre'] ?? 0) ?>" readonly>
                </div>
            </div>

            <div class="form-group">
                <label for="monto">Total a Pagar</label>
                <input type="text" id="monto" name="monto" step="0.01" readonly>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label for="formaPago">Forma de Pago</label>
                    <select id="formaPago" name="formaPago">
                        <option value="">-- Seleccione --</option>
                        <option value="EFECTIVO">Efectivo</option>
                        <option value="DEPOSITO">Depósito</option>
                        <option value="TRANSFERENCIA">Transferencia</option>
                        <option value="CHEQUE">Cheque</option>
                        <option value="PLANILLA">Planilla</option>
                    </select>
                </div>
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
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label for="tipoAsiento">Tipo de Asiento</label>
                    <select id="tipoAsiento" name="tipoAsiento">
                        <option value="haber">Haber (Ingreso)</option>
                        <option value="debe">Debe (Egreso)</option>
                    </select>
                </div>
                <div class="form-group">
                    <label for="descripcionContable">Descripción Contable</label>
                    <input type="text" id="descripcionContable" name="descripcionContable" value="AMORTIZACION AEE">
                </div>
            </div>

            <div class="form-group">
                <label for="fecha">Fecha</label>
                <input type="date" id="fecha" name="fecha" value="<?= date('Y-m-d') ?>">
            </div>

            <div class="form-group">
                <label for="obs">Observaciones</label>
                <textarea id="obs" name="obs"></textarea>
            </div>

            <div class="form-group">
                <label for="obs2">Observaciones 2</label>
                <textarea id="obs2" name="obs2"></textarea>
            </div>

            <div class="form-row">
                <button type="submit" class="btn">Grabar Amortización</button>
                <a href="index.php?module=caja&profile=client&client=<?= $escape($clienteCodigo) ?>" class="btn btn-secondary">Cancelar</a>
            </div>
        </form>
    </main>
</div>

<script>
    // Función para calcular el total automáticamente (similar al legacy)
    function calcularTotal() {
        const amortiza = parseFloat(document.getElementById('amortiza').value.replace(/,/g, '')) || 0;
        const gastosAdm = parseFloat(document.getElementById('gastosAdm').value.replace(/,/g, '')) || 0;
        const gastosMora = parseFloat(document.getElementById('gastosMora').value.replace(/,/g, '')) || 0;
        const otros1 = parseFloat(document.getElementById('otros1').value.replace(/,/g, '')) || 0;
        const otros2 = parseFloat(document.getElementById('otros2').value.replace(/,/g, '')) || 0;
        // Sobregiro NO se suma al total, es un estado del préstamo, no un concepto de pago

        const total = amortiza + gastosAdm + gastosMora + otros1 + otros2;
        document.getElementById('monto').value = total.toFixed(2);
    }
    
    // Event listeners para cálculo automático
    ['amortiza', 'gastosAdm', 'gastosMora', 'otros1', 'otros2', 'sobregiro'].forEach(id => {
        document.getElementById(id).addEventListener('input', calcularTotal);
    });
    
    // Calcular total al cargar la página (con valores pre-cargados)
    document.addEventListener('DOMContentLoaded', calcularTotal);
    
    // Mostrar información de la cuota seleccionada
    document.getElementById('cuota').addEventListener('change', function() {
        const selectedOption = this.options[this.selectedIndex];
        if (this.value) {
            document.getElementById('cuota-info').style.display = 'block';
            document.getElementById('capital-display').textContent = selectedOption.dataset.capital;
            document.getElementById('interes-display').textContent = selectedOption.dataset.interes;
            document.getElementById('total-display').textContent = selectedOption.dataset.total;
            document.getElementById('fecha-display').textContent = selectedOption.dataset.fecha;
            
            // Pre-llenar amortización con el capital de la cuota
            document.getElementById('amortiza').value = selectedOption.dataset.capital.replace(/,/g, '');
            calcularTotal();
        } else {
            document.getElementById('cuota-info').style.display = 'none';
        }
    });
</script>
</body>
</html>
