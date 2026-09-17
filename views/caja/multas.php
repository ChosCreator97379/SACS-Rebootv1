<?php
declare(strict_types=1);

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
$clienteCodigo = $_GET['client'] ?? '';
$multaId = $_GET['multa'] ?? '';

$cliente = null;
$multa = null;
$multasPendientes = [];

if ($clienteCodigo !== '') {
    $cliente = $repository->findClientProfile($clienteCodigo);
    $multasPendientes = $repository->clientPendingFines($clienteCodigo);
}

if ($multaId !== '') {
    foreach ($multasPendientes as $m) {
        if ((string)$m['id'] === $multaId) {
            $multa = $m;
            break;
        }
    }
}

$escape = fn(mixed $s): string => htmlspecialchars((string)($s ?? ''), ENT_QUOTES, 'UTF-8');
$money = fn(float $amount): string => number_format($amount, 2, '.', ',');
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pago de Multas - SACS</title>
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
        .intro { max-width: 640px; margin: 12px 0 28px; color: var(--muted); line-height: 1.55; }
        .layout { display: grid; grid-template-columns: 1fr; gap: 24px; }
        .panel { background: var(--panel); border-radius: 12px; padding: 24px; box-shadow: var(--shadow); margin-bottom: 24px; }
        .panel h2 { margin: 0 0 20px; font-size: 18px; font-weight: 400; }
        .panel h3 { margin: 0 0 16px; font-size: 14px; font-weight: 600; color: var(--muted); text-transform: uppercase; letter-spacing: .08em; }
        .client-info { display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 16px; margin-bottom: 20px; }
        .info-item { }
        .info-label { font-size: 11px; color: var(--muted); text-transform: uppercase; letter-spacing: .08em; margin-bottom: 4px; }
        .info-value { font: inherit; font-size: 14px; font-weight: 600; }
        .form-group { margin-bottom: 16px; }
        .form-group label { display: block; font-size: 12px; font-weight: 600; color: var(--muted); margin-bottom: 6px; }
        .form-group input, .form-group select, .form-group textarea { padding: 10px 12px; border: 1px solid var(--line); border-radius: 6px; font-size: 13px; font-family: inherit; }
        .form-group input:focus, .form-group select:focus, .form-group textarea:focus { outline: none; border-color: var(--teal); }
        .form-row { display: grid; grid-template-columns: 1fr 1fr; gap: 16px; }
        .form-actions { display: flex; gap: 12px; margin-top: 24px; }
        .btn { padding: 12px 24px; border: none; border-radius: 6px; font-size: 13px; font-weight: 600; cursor: pointer; transition: background .15s; }
        .btn-primary { background: var(--teal); color: white; }
        .btn-primary:hover { background: #166664; }
        .btn-secondary { background: var(--paper); color: var(--ink); }
        .btn-secondary:hover { background: #e8ebe9; }
        .alert { padding: 15px; margin-bottom: 20px; border-radius: 6px; }
        .alert-error { background: #fee; color: #c33; border: 1px solid #fcc; }
        .alert-success { background: #efe; color: #3c3; border: 1px solid #cfc; }
        table { width: 100%; border-collapse: collapse; }
        th { text-align: left; font-size: 11px; font-weight: 600; color: var(--muted); text-transform: uppercase; letter-spacing: .08em; padding: 12px 16px; border-bottom: 1px solid var(--line); }
        td { padding: 12px 16px; border-bottom: 1px solid var(--line); font-size: 13px; }
        td.amount { font-variant-numeric: tabular-nums; }
        .empty { padding: 42px; color: var(--muted); text-align: center; }
        @media (max-width: 900px) { .shell { grid-template-columns: 1fr; } .sidebar { display: none; } }
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
                <a href="index.php?module=caja&profile=client&client=<?= $escape($clienteCodigo) ?>" class="nav-link">Perfil Cliente</a>
                <a href="index.php?module=caja&action=aportes_fonbes_form&client=<?= $escape($clienteCodigo) ?>" class="nav-link">Aportes FONBES</a>
                <a href="index.php?module=caja&action=aportes_coopam_form&client=<?= $escape($clienteCodigo) ?>" class="nav-link">Aportes COOPAM</a>
                <a href="index.php?module=caja&action=desembolso_prestamo_form&client=<?= $escape($clienteCodigo) ?>" class="nav-link">Desembolso Préstamo</a>
                <a href="index.php?module=caja&action=sobregiro_prestamo_form&client=<?= $escape($clienteCodigo) ?>" class="nav-link">Sobregiro Préstamo</a>
                <a href="index.php?module=caja&action=amortizar_form&client=<?= $escape($clienteCodigo) ?>" class="nav-link">Amortización</a>
                <a href="index.php?module=caja&action=multas_form&client=<?= $escape($clienteCodigo) ?>" class="nav-link active">Multas</a>
                <a href="index.php?module=caja&action=devolucion_fonbes_form&client=<?= $escape($clienteCodigo) ?>" class="nav-link">Devolución FONBES</a>
                <a href="index.php?module=caja&action=devolucion_coopam_form&client=<?= $escape($clienteCodigo) ?>" class="nav-link">Devolución COOPAM</a>
                <a href="index.php?module=caja&action=cancelacion_aee_form&client=<?= $escape($clienteCodigo) ?>" class="nav-link">Cancelación AEE</a>
                <a href="index.php?module=caja&action=pago_rifa_form&client=<?= $escape($clienteCodigo) ?>" class="nav-link">Pago Rifa</a>
                <a href="index.php?module=caja&action=pago_sobregiro_fonbes_form&client=<?= $escape($clienteCodigo) ?>" class="nav-link">Pago Sobregiro FONBES</a>
                <a href="index.php?module=caja&action=pago_sobregiro_coopam_form&client=<?= $escape($clienteCodigo) ?>" class="nav-link">Pago Sobregiro COOPAM</a>
                <a href="index.php?module=caja&action=otras_devoluciones_form&client=<?= $escape($clienteCodigo) ?>" class="nav-link">Otras Devoluciones</a>
                <a href="index.php?module=caja&action=otros_ingresos_form&client=<?= $escape($clienteCodigo) ?>" class="nav-link">Otros Ingresos</a>
                <a href="index.php?module=caja&action=desembolso_beneficios_form&client=<?= $escape($clienteCodigo) ?>" class="nav-link">Desembolso Beneficios</a>
                <a href="index.php?module=caja&action=egresos_form&client=<?= $escape($clienteCodigo) ?>" class="nav-link">Egresos</a>
            </nav>
        </aside>

        <main class="main">
            <div class="header">
                <h1>Pago de Multas</h1>
            </div>
            <p class="intro">Operación 11 - Pago de multas del cliente siguiendo el flujo legacy del sistema.</p>

            <?php if ($cliente === null): ?>
                <div class="panel">
                    <p class="empty">No se ha seleccionado un cliente.</p>
                </div>
            <?php else: ?>
                <div class="layout">
                    <section class="panel">
                        <h2>Información del Cliente</h2>
                        <div class="client-info">
                            <div class="info-item">
                                <div class="info-label">Código</div>
                                <div class="info-value">#<?= $escape($cliente['client_id']) ?></div>
                            </div>
                            <div class="info-item">
                                <div class="info-label">Nombre</div>
                                <div class="info-value"><?= $escape($cliente['full_name']) ?></div>
                            </div>
                            <div class="info-item">
                                <div class="info-label">Documento</div>
                                <div class="info-value"><?= $escape($cliente['document']) ?></div>
                            </div>
                            <div class="info-item">
                                <div class="info-label">Condición</div>
                                <div class="info-value"><?= $escape($cliente['condition']) ?></div>
                            </div>
                        </div>
                    </section>

                    <?php if ($multa === null): ?>
                        <section class="panel">
                            <h2>Multas Pendientes de Pago</h2>
                            <?php if (empty($multasPendientes)): ?>
                                <p class="empty">No hay multas pendientes para este cliente.</p>
                            <?php else: ?>
                                <table>
                                    <thead>
                                        <tr>
                                            <th>Código</th>
                                            <th>Periodo</th>
                                            <th>Multa Planeada</th>
                                            <th>Multa Pagada</th>
                                            <th>Multa Restante</th>
                                            <th>Acción</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($multasPendientes as $m): ?>
                                            <tr>
                                                <td>#<?= $escape((string)$m['id']) ?></td>
                                                <td><?= $escape($m['period']) ?></td>
                                                <td class="amount"><?= $money((float)$m['planned']) ?></td>
                                                <td class="amount"><?= $money((float)$m['paid']) ?></td>
                                                <td class="amount"><?= $money((float)$m['remaining']) ?></td>
                                                <td>
                                                    <a href="index.php?module=caja&action=multas_form&client=<?= $escape($clienteCodigo) ?>&multa=<?= $escape((string)$m['id']) ?>" 
                                                       class="btn btn-primary" style="padding: 6px 12px; font-size: 12px;">Pagar</a>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            <?php endif; ?>
                        </section>
                    <?php else: ?>
                        <section class="panel">
                            <h2>Detalle de la Multa</h2>
                            <div class="client-info">
                                <div class="info-item">
                                    <div class="info-label">Código</div>
                                    <div class="info-value">#<?= $escape((string)$multa['id']) ?></div>
                                </div>
                                <div class="info-item">
                                    <div class="info-label">Periodo</div>
                                    <div class="info-value"><?= $escape($multa['period']) ?></div>
                                </div>
                                <div class="info-item">
                                    <div class="info-label">Multa Planeada</div>
                                    <div class="info-value"><?= $money((float)$multa['planned']) ?></div>
                                </div>
                                <div class="info-item">
                                    <div class="info-label">Multa Pagada</div>
                                    <div class="info-value"><?= $money((float)$multa['paid']) ?></div>
                                </div>
                                <div class="info-item">
                                    <div class="info-label">Multa Restante</div>
                                    <div class="info-value"><?= $money((float)$multa['remaining']) ?></div>
                                </div>
                            </div>
                        </section>

                        <section class="panel">
                            <h2>Registrar Pago de Multa</h2>
                            <?php if (isset($_SESSION['multa_error'])): ?>
                                <div class="alert alert-error">
                                    <?= $escape($_SESSION['multa_error']) ?>
                                </div>
                                <?php unset($_SESSION['multa_error']); ?>
                            <?php endif; ?>

                            <?php if (isset($_SESSION['multa_success'])): ?>
                                <div class="alert alert-success">
                                    <?= $escape($_SESSION['multa_success']) ?>
                                </div>
                                <?php unset($_SESSION['multa_success']); ?>
                            <?php endif; ?>

                            <form method="POST" action="index.php">
                                <input type="hidden" name="module" value="caja">
                                <input type="hidden" name="action" value="multas">
                                <input type="hidden" name="cliente" value="<?= $escape($clienteCodigo) ?>">
                                <input type="hidden" name="multa" value="<?= $escape((string)$multa['id']) ?>">
                                <input type="hidden" name="usuario" value="<?= $escape($user['username'] ?? 'SISTEMA') ?>">
                                <input type="hidden" name="csrf_token" value="<?= $escape($_SESSION['csrf_token'] ?? '') ?>">

                                <div class="form-group">
                                    <label for="monto">Monto a Pagar</label>
                                    <input type="number" id="monto" name="monto" step="0.01" min="0" required
                                           value="<?= $money((float)$multa['remaining']) ?>">
                                </div>

                                <div class="form-group">
                                    <label for="formaPago">Forma de Pago</label>
                                    <select id="formaPago" name="formaPago" required>
                                        <option value="">Seleccione forma de pago</option>
                                        <option value="EFECTIVO">Efectivo</option>
                                        <option value="DEPOSITO">Depósito</option>
                                        <option value="TRANSFERENCIA">Transferencia</option>
                                        <option value="CHEQUE">Cheque</option>
                                    </select>
                                </div>

                                <div class="form-row">
                                    <div class="form-group">
                                        <label for="codigoContable">Código Contable</label>
                                        <input type="text" id="codigoContable" name="codigoContable" list="codigosContables" value="1651">
                                        <datalist id="codigosContables">
                                            <?php
                                            $codigos = $repository->accountingCodes();
                                            foreach ($codigos as $codigo): ?>
                                                <option value="<?= $escape($codigo['codigo']) ?>">
                                                    <?= $escape($codigo['codigo']) ?> - <?= $escape($codigo['nombre_cuenta']) ?>
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
                                    <textarea id="descripcionContable" name="descripcionContable" rows="2">PAGO MULTA</textarea>
                                </div>

                                <div class="form-group">
                                    <label for="obs">Observaciones</label>
                                    <textarea id="obs" name="obs" rows="2"></textarea>
                                </div>

                                <div class="form-actions">
                                    <button type="submit" class="btn btn-primary">Registrar Pago</button>
                                    <a href="index.php?module=caja&action=multas_form&client=<?= $escape($clienteCodigo) ?>" class="btn btn-secondary">Cancelar</a>
                                </div>
                            </form>
                        </section>
                    <?php endif; ?>
                </div>
            <?php endif; ?>
        </main>
    </div>
</body>
</html>
