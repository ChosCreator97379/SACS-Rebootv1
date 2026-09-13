<?php
$escape = static fn (mixed $value): string => htmlspecialchars((string) ($value ?? ''), ENT_QUOTES, 'UTF-8');
$money = static fn (mixed $value): string => $value === null || $value === '' ? '-' : number_format((float) $value, 2, '.', ',');
$loanStatus = static function (mixed $status): string {
    return [
        0 => 'Inactivo',
        1 => 'Solicitado',
        2 => 'Aprobado',
        3 => 'Cancelado',
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
    <title>Cliente <?= $escape($client['client_id']) ?> | SACS Nuevo</title>
    <style>
        :root { --ink: #17212b; --muted: #687684; --paper: #f5f7f4; --panel: #fff; --navy: #142b3a; --teal: #1d7a78; --gold: #d69e47; --line: #dce4e1; }
        * { box-sizing: border-box; } body { margin: 0; background: var(--paper); color: var(--ink); font-family: "Trebuchet MS", sans-serif; } a { color: inherit; text-decoration: none; }
        .top { display: flex; align-items: center; justify-content: space-between; padding: 22px clamp(20px, 4vw, 58px); background: var(--navy); color: #fff; } .brand { font-family: Georgia, serif; font-size: 24px; } .brand small { display: block; margin-top: 4px; color: #9bb3b7; font-family: "Trebuchet MS", sans-serif; font-size: 10px; letter-spacing: .13em; text-transform: uppercase; } .back { color: #b9d2d2; font-size: 13px; }
        main { max-width: 1250px; margin: 0 auto; padding: 38px clamp(20px, 4vw, 58px); } .eyebrow { margin: 0 0 9px; color: var(--teal); font-size: 11px; font-weight: bold; letter-spacing: .16em; text-transform: uppercase; } h1 { margin: 0; font-family: Georgia, serif; font-size: clamp(32px, 4vw, 48px); font-weight: normal; } .subtitle { margin: 10px 0 28px; color: var(--muted); }
        .summary, .panel { padding: 24px; border: 1px solid var(--line); border-radius: 7px; background: var(--panel); } .summary { display: grid; grid-template-columns: 1.5fr repeat(3, 1fr); gap: 18px; margin-bottom: 18px; } .name { font-family: Georgia, serif; font-size: 24px; } .label { display: block; margin-bottom: 7px; color: var(--muted); font-size: 10px; letter-spacing: .1em; text-transform: uppercase; } .value { font-weight: bold; }
        .layout { display: grid; grid-template-columns: 1.2fr .8fr; gap: 18px; } .panel h2 { margin: 0 0 18px; font-family: Georgia, serif; font-size: 21px; font-weight: normal; } .wide { grid-column: 1 / -1; } table { width: 100%; border-collapse: collapse; font-size: 12px; } th { padding: 10px; background: #edf3f1; color: var(--muted); font-size: 10px; text-align: left; text-transform: uppercase; } td { padding: 11px 10px; border-top: 1px solid var(--line); } .amount { text-align: right; white-space: nowrap; } .state { color: var(--teal); font-weight: bold; } .muted { color: var(--muted); } .empty { color: var(--muted); font-size: 13px; } .actions { display: flex; gap: 10px; margin-bottom: 18px; } .action { padding: 9px 13px; border: 1px solid var(--line); border-radius: 4px; color: var(--teal); font-size: 12px; }
        @media (max-width: 850px) { .summary, .layout { grid-template-columns: 1fr 1fr; } .wide { grid-column: 1 / -1; } } @media (max-width: 560px) { .top { align-items: flex-start; flex-direction: column; gap: 12px; } .summary, .layout { grid-template-columns: 1fr; } .wide { grid-column: auto; } }
    </style>
</head>
<body>
<header class="top"><a class="brand" href="index.php">SACS<small>gestion social</small></a><a class="back" href="index.php?module=caja">Volver a Caja</a></header>
<main>
    <p class="eyebrow">Caja / Perfil operativo</p>
    <h1>Cliente <?= $escape($client['client_id']) ?></h1>
    <p class="subtitle">Consulta de lectura basada en las relaciones utilizadas por el sistema legacy.</p>
    <div class="actions"><a class="action" href="index.php?module=caja&amp;client=<?= rawurlencode((string) $client['client_id']) ?>">Ver movimientos</a></div>
    <section class="summary">
        <div><span class="label">Socio</span><div class="name"><?= $escape($client['full_name'] ?: 'Sin nombre') ?></div></div>
        <div><span class="label">Documento</span><div class="value"><?= $escape($client['document'] ?: '-') ?></div></div>
        <div><span class="label">Centro</span><div class="value"><?= $escape($client['center'] ?: '-') ?></div></div>
        <div><span class="label">Condicion</span><div class="value"><?= $escape($client['condition'] ?: '-') ?></div></div>
    </section>
    <div class="layout">
        <section class="panel"><h2>Prestamos</h2><?php if ($loans === []): ?><p class="empty">No hay prestamos registrados para este cliente.</p><?php else: ?><table><thead><tr><th>Codigo</th><th>Estado</th><th>Producto</th><th>Importe</th><th>Saldo</th><th>Cuotas pendientes</th><th>Acción</th></tr></thead><tbody><?php foreach ($loans as $loan): ?><tr><td>#<?= $escape($loan['id']) ?></td><td class="state"><?= $escape($loanStatus($loan['status'])) ?></td><td><?= $escape($loan['product'] ?: '-') ?></td><td class="amount"><?= $money($loan['amount']) ?></td><td class="amount"><?= $money($loan['balance']) ?></td><td><?= $escape($loan['pending_installments'] ?: '0') ?></td><td><?php if ((int)$loan['status'] === 4): ?><a class="action" href="index.php?module=caja&amp;action=amortizar_form&amp;client=<?= rawurlencode((string) $client['client_id']) ?>&amp;loan=<?= $escape($loan['id']) ?>">Amortizar</a><?php else: ?><span class="muted">-</span><?php endif; ?></td></tr><?php endforeach; ?></tbody></table><?php endif; ?></section>
        <section class="panel"><h2>Cuotas vigentes</h2><?php if ($installments === []): ?><p class="empty">No hay cuotas vigentes.</p><?php else: ?><table><thead><tr><th>Prestamo</th><th>Cuota</th><th>Fecha</th><th>Total</th><th>Accion</th></tr></thead><tbody><?php foreach ($installments as $installment): ?><tr><td>#<?= $escape($installment['loan_id']) ?></td><td><?= $escape($installment['number']) ?></td><td><?= $escape($installment['date']) ?></td><td class="amount"><?= $money($installment['total']) ?></td><td><a class="action" href="index.php?module=caja&amp;client=<?= rawurlencode((string) $client['client_id']) ?>&amp;loan=<?= $escape($installment['loan_id']) ?>&amp;installment=<?= $escape($installment['number']) ?>">Revisar cuota</a></td></tr><?php endforeach; ?></tbody></table><?php endif; ?></section>
        <section class="panel"><h2>Aportaciones Fonbes</h2><?php if ($contributions === []): ?><p class="empty">Sin aportaciones recientes.</p><?php else: ?><table><thead><tr><th>Periodo</th><th>Programado</th><th>Pagado</th><th>Pago</th></tr></thead><tbody><?php foreach ($contributions as $item): ?><tr><td><?= $escape($item['period']) ?></td><td class="amount"><?= $money($item['planned']) ?></td><td class="amount"><?= $money($item['paid']) ?></td><td><?= $escape($item['paid_date'] ?: '-') ?></td></tr><?php endforeach; ?></tbody></table><?php endif; ?><div class="actions"><a class="action" href="index.php?module=caja&amp;action=aportes_fonbes_form&amp;client=<?= rawurlencode((string) $client['client_id']) ?>">Pagar Aportes</a></div></section>
        <section class="panel"><h2>Aportaciones Coopam</h2><?php if ($copeContributions === []): ?><p class="empty">Sin aportaciones recientes.</p><?php else: ?><table><thead><tr><th>Periodo</th><th>Programado</th><th>Pagado</th><th>Pago</th></tr></thead><tbody><?php foreach ($copeContributions as $item): ?><tr><td><?= $escape($item['period']) ?></td><td class="amount"><?= $money($item['planned']) ?></td><td class="amount"><?= $money($item['paid']) ?></td><td><?= $escape($item['paid_date'] ?: '-') ?></td></tr><?php endforeach; ?></tbody></table><?php endif; ?></section>
        <section class="panel"><h2>Multas</h2><?php if ($fines === []): ?><p class="empty">Sin multas registradas.</p><?php else: ?><table><thead><tr><th>Periodo</th><th>Programada</th><th>Pagada</th><th>Pendiente</th><th>Pago</th></tr></thead><tbody><?php foreach ($fines as $item): ?><tr><td><?= $escape($item['period']) ?></td><td class="amount"><?= $money($item['planned']) ?></td><td class="amount"><?= $money($item['paid']) ?></td><td class="amount"><?= $money($item['remaining']) ?></td><td><?= $escape($item['paid_date'] ?: '-') ?></td></tr><?php endforeach; ?></tbody></table><?php endif; ?></section>
        <section class="panel"><h2>Rifas</h2><?php if ($raffles === []): ?><p class="empty">Sin rifas registradas.</p><?php else: ?><table><thead><tr><th>Periodo</th><th>Programada</th><th>Pagada</th><th>Pendiente</th><th>Pago</th></tr></thead><tbody><?php foreach ($raffles as $item): ?><tr><td><?= $escape($item['period']) ?></td><td class="amount"><?= $money($item['planned']) ?></td><td class="amount"><?= $money($item['paid']) ?></td><td class="amount"><?= $money($item['remaining']) ?></td><td><?= $escape($item['paid_date'] ?: '-') ?></td></tr><?php endforeach; ?></tbody></table><?php endif; ?></section>
        <section class="panel wide"><h2>Beneficios</h2><?php if ($benefits === []): ?><p class="empty">Sin beneficios registrados.</p><?php else: ?><table><thead><tr><th>Codigo</th><th>Fecha</th><th>Importe</th><th>Tipo</th><th>Estado</th><th>Beneficiario</th></tr></thead><tbody><?php foreach ($benefits as $item): ?><tr><td>#<?= $escape($item['id']) ?></td><td><?= $escape($item['date'] ?: '-') ?></td><td class="amount"><?= $money($item['amount']) ?></td><td><?= $escape($item['type'] ?: '-') ?></td><td class="state"><?= $escape($item['status'] ?: '-') ?></td><td><?= $escape($item['beneficiary'] ?: '-') ?></td></tr><?php endforeach; ?></tbody></table><?php endif; ?></section>
    </div>
</main>
</body>
</html>
