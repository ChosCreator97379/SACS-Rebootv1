<?php

declare(strict_types=1);

require dirname(__DIR__) . '/../config/bootstrap.php';

use Sacs\Repositories\CajaRepository;
use Sacs\Security\Auth;

$auth = new Auth(new Sacs\Repositories\UserRepository());
if (!$auth->check()) {
    header('Location: index.php');
    exit;
}

$repository = new CajaRepository();

$clienteCodigo = trim($_GET['client'] ?? '');

if ($clienteCodigo === '') {
    http_response_code(400);
    $error = 'Cliente no especificado';
    require dirname(__DIR__) . '/home.php';
    exit;
}

$cliente = $repository->findClientProfile($clienteCodigo);
if ($cliente === null) {
    http_response_code(404);
    $error = 'Cliente no encontrado';
    require dirname(__DIR__) . '/home.php';
    exit;
}

$prestamosActivos = $repository->clientLoans($clienteCodigo);

$escape = static fn (mixed $value): string => htmlspecialchars((string) ($value ?? ''), ENT_QUOTES, 'UTF-8');
$money = static fn (mixed $value): string => $value === null || $value === '' ? '-' : number_format((float) $value, 2, '.', ',');
$loanStatus = static function (mixed $status): string {
    return [
        0 => 'Inactivo',
        1 => 'Solicitado',
        2 => 'Aprobado',
        3 => 'Pendiente Desembolso',
        4 => 'Activo',
        5 => 'Rechazado',
        6 => 'Reprogramado',
    ][(int) $status] ?? 'Estado ' . (string) $status;
};
?>
<!doctype html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Seleccionar Préstamo para Amortizar | SACS Nuevo</title>
    <style>
        :root { --ink: #17212b; --muted: #687684; --paper: #f5f7f4; --panel: #fff; --navy: #142b3a; --teal: #1d7a78; --gold: #d69e47; --line: #dce4e1; }
        * { box-sizing: border-box; } body { margin: 0; background: var(--paper); color: var(--ink); font-family: "Trebuchet MS", sans-serif; }
        a { color: inherit; text-decoration: none; }
        .top { display: flex; align-items: center; justify-content: space-between; padding: 22px clamp(20px, 4vw, 58px); background: var(--navy); color: #fff; }
        .brand { font-family: Georgia, serif; font-size: 24px; }
        .brand small { display: block; margin-top: 4px; color: #9bb3b7; font-family: "Trebuchet MS", sans-serif; font-size: 10px; letter-spacing: .13em; text-transform: uppercase; }
        .back { color: #b9d2d2; font-size: 13px; }
        main { max-width: 1250px; margin: 0 auto; padding: 38px clamp(20px, 4vw, 58px); }
        .eyebrow { margin: 0 0 9px; color: var(--teal); font-size: 11px; font-weight: bold; letter-spacing: .16em; text-transform: uppercase; }
        h1 { margin: 0; font-family: Georgia, serif; font-size: clamp(32px, 4vw, 48px); font-weight: normal; }
        .subtitle { margin: 10px 0 28px; color: var(--muted); }
        .summary, .panel { padding: 24px; border: 1px solid var(--line); border-radius: 7px; background: var(--panel); }
        .summary { display: grid; grid-template-columns: 1.5fr repeat(3, 1fr); gap: 18px; margin-bottom: 18px; }
        .name { font-family: Georgia, serif; font-size: 24px; }
        .label { display: block; margin-bottom: 7px; color: var(--muted); font-size: 10px; letter-spacing: .1em; text-transform: uppercase; }
        .value { font-weight: bold; }
        .panel h2 { margin: 0 0 18px; font-family: Georgia, serif; font-size: 21px; font-weight: normal; }
        table { width: 100%; border-collapse: collapse; font-size: 12px; }
        th { padding: 10px; background: #edf3f1; color: var(--muted); font-size: 10px; text-align: left; text-transform: uppercase; }
        td { padding: 11px 10px; border-top: 1px solid var(--line); }
        .amount { text-align: right; white-space: nowrap; }
        .state { color: var(--teal); font-weight: bold; }
        .muted { color: var(--muted); }
        .empty { color: var(--muted); font-size: 13px; padding: 20px 0; text-align: center; }
        .action { padding: 9px 13px; border: 1px solid var(--line); border-radius: 4px; color: var(--teal); font-size: 12px; display: inline-block; }
        .action:hover { background: var(--navy); color: #fff; }
    </style>
</head>
<body>
<header class="top"><a class="brand" href="index.php">SACS<small>gestion social</small></a><a class="back" href="index.php?module=caja&profile=client&client=<?= rawurlencode($clienteCodigo) ?>">Volver al Perfil</a></header>
<main>
    <p class="eyebrow">Caja / Amortización</p>
    <h1>Seleccionar Préstamo</h1>
    <p class="subtitle">Seleccione un préstamo activo para amortizar para el cliente <?= $escape($cliente['client_id']) ?></p>

    <section class="summary">
        <div><span class="label">Socio</span><div class="name"><?= $escape($cliente['full_name'] ?: 'Sin nombre') ?></div></div>
        <div><span class="label">Documento</span><div class="value"><?= $escape($cliente['document'] ?: '-') ?></div></div>
        <div><span class="label">Centro</span><div class="value"><?= $escape($cliente['center'] ?: '-') ?></div></div>
        <div><span class="label">Condicion</span><div class="value"><?= $escape($cliente['condition'] ?: '-') ?></div></div>
    </section>

    <section class="panel">
        <h2>Préstamos Activos</h2>
        <?php if (empty($prestamosActivos)): ?>
            <p class="empty">No hay préstamos activos para este cliente.</p>
        <?php else: ?>
            <table>
                <thead>
                    <tr>
                        <th>Código</th>
                        <th>Estado</th>
                        <th>Producto</th>
                        <th>Importe</th>
                        <th>Saldo</th>
                        <th>Cuotas Pendientes</th>
                        <th>Acción</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($prestamosActivos as $prestamo): ?>
                        <tr>
                            <td>#<?= $escape($prestamo['id']) ?></td>
                            <td class="state"><?= $escape($loanStatus($prestamo['status'])) ?></td>
                            <td><?= $escape($prestamo['product'] ?? '-') ?></td>
                            <td class="amount"><?= $money($prestamo['amount']) ?></td>
                            <td class="amount"><?= $money($prestamo['balance']) ?></td>
                            <td><?= $escape($prestamo['pending_installments'] ?: '0') ?></td>
                            <td>
                                <a class="action" href="index.php?module=caja&action=amortizar_form&client=<?= rawurlencode($clienteCodigo) ?>&loan=<?= $escape($prestamo['id']) ?>">Amortizar</a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php endif; ?>
    </section>
</main>
</body>
</html>
