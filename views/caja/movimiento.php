<?php
$escape = static fn (mixed $value): string => htmlspecialchars((string) ($value ?? ''), ENT_QUOTES, 'UTF-8');
$money = static function (mixed $value): string {
    return $value === null || $value === '' ? '-' : number_format((float) $value, 2, '.', ',');
};
$amounts = [
    'Aportacion' => $movement['contribution_amount'],
    'Retiro' => $movement['withdrawal_amount'],
    'Pago de prestamo' => $movement['loan_payment_amount'],
    'Desembolso' => $movement['disbursement_amount'],
    'Beneficio' => $movement['benefit_amount'],
    'Multa' => $movement['fine_amount'],
    'Rifa' => $movement['raffle_amount'],
    'Devolucion' => $movement['refund_amount'],
    'Egreso' => $movement['expense_amount'],
    'Ingreso' => $movement['income_amount'],
    'Gasto administrativo' => $movement['admin_expense'],
    'Mora' => $movement['late_fee'],
    'Otros 1' => $movement['other_one'],
    'Otros 2' => $movement['other_two'],
];
$amounts = array_filter($amounts, static fn (mixed $value): bool => $value !== null && $value !== '');
?>
<!doctype html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Movimiento #<?= $escape($movement['id']) ?> | SACS Nuevo</title>
    <style>
        :root { --ink: #17212b; --muted: #687684; --paper: #f5f7f4; --panel: #fff; --navy: #142b3a; --teal: #1d7a78; --gold: #d69e47; --line: #dce4e1; }
        * { box-sizing: border-box; }
        body { margin: 0; background: var(--paper); color: var(--ink); font-family: "Trebuchet MS", sans-serif; }
        a { color: inherit; text-decoration: none; }
        .top { display: flex; align-items: center; justify-content: space-between; padding: 22px clamp(20px, 4vw, 58px); background: var(--navy); color: #fff; }
        .brand { font-family: Georgia, serif; font-size: 24px; }
        .brand small { display: block; margin-top: 4px; color: #9bb3b7; font-family: "Trebuchet MS", sans-serif; font-size: 10px; letter-spacing: .13em; text-transform: uppercase; }
        .back { color: #b9d2d2; font-size: 13px; }
        main { max-width: 1100px; margin: 0 auto; padding: 38px clamp(20px, 4vw, 58px); }
        .eyebrow { margin: 0 0 9px; color: var(--teal); font-size: 11px; font-weight: bold; letter-spacing: .16em; text-transform: uppercase; }
        h1 { margin: 0; font-family: Georgia, serif; font-size: clamp(32px, 4vw, 48px); font-weight: normal; }
        .subtitle { margin: 10px 0 28px; color: var(--muted); }
        .layout { display: grid; grid-template-columns: 1fr 1fr; gap: 18px; }
        .panel { padding: 24px; border: 1px solid var(--line); border-radius: 7px; background: var(--panel); }
        h2 { margin: 0 0 18px; font-family: Georgia, serif; font-size: 21px; font-weight: normal; }
        dl { display: grid; grid-template-columns: minmax(130px, .8fr) 1.2fr; gap: 12px 20px; margin: 0; font-size: 13px; }
        dt { color: var(--muted); }
        dd { margin: 0; font-weight: bold; }
        .amount-list { display: grid; gap: 10px; margin: 0; padding: 0; list-style: none; }
        .amount-line { display: flex; justify-content: space-between; gap: 18px; padding-bottom: 9px; border-bottom: 1px solid var(--line); font-size: 13px; }
        .amount-value { color: var(--teal); font-variant-numeric: tabular-nums; font-weight: bold; }
        .wide { grid-column: 1 / -1; }
        .note { min-height: 60px; color: var(--muted); line-height: 1.5; white-space: pre-wrap; }
        .status-ok { color: var(--teal); }
        .status-muted { color: var(--muted); }
        @media (max-width: 700px) { .top { align-items: flex-start; flex-direction: column; gap: 12px; } .layout { grid-template-columns: 1fr; } .wide { grid-column: auto; } }
    </style>
</head>
<body>
<header class="top"><a class="brand" href="index.php">SACS<small>gestion social</small></a><a class="back" href="index.php?module=caja&amp;client=<?= rawurlencode((string) $movement['client_id']) ?>">Volver a movimientos</a></header>
<main>
    <p class="eyebrow">Caja / Movimiento <?= $escape($movement['id']) ?></p>
    <h1><?= $escape($movement['operation_name']) ?></h1>
    <p class="subtitle">Detalle de solo lectura. No se modifican movimientos, saldos ni cuotas.</p>
    <div class="layout">
        <section class="panel"><h2>Identificacion</h2><dl>
            <dt>Codigo</dt><dd>#<?= $escape($movement['id']) ?></dd>
            <dt>Fecha</dt><dd><?= $escape($movement['operation_date']) ?></dd>
            <dt>Operacion</dt><dd><?= $escape($movement['operation_id']) ?> - <?= $escape($movement['operation_name']) ?></dd>
            <dt>Cliente</dt><dd><?= $escape($movement['client_name'] ?: 'Sin cliente') ?> (<?= $escape($movement['client_id']) ?>)</dd>
            <dt>Forma de pago</dt><dd><?= $escape($movement['payment_method'] ?: '-') ?></dd>
            <dt>Usuario creador</dt><dd><?= $escape($movement['created_by'] ?: '-') ?></dd>
        </dl></section>
        <section class="panel"><h2>Importes registrados</h2><?php if ($amounts === []): ?><p class="status-muted">No hay importes registrados en las columnas consultadas.</p><?php else: ?><ul class="amount-list"><?php foreach ($amounts as $label => $value): ?><li class="amount-line"><span><?= $escape($label) ?></span><span class="amount-value"><?= $money($value) ?></span></li><?php endforeach; ?></ul><?php endif; ?></section>
        <section class="panel"><h2>Relaciones</h2><dl>
            <dt>Prestamo</dt><dd><?= $movement['loan_id'] === null ? '-' : '#' . $escape($movement['loan_id']) . ' · estado ' . $escape($movement['loan_status']) ?></dd>
            <dt>Cuota asociada</dt><dd><?= $escape($movement['installment'] ?: '-') ?></dd>
            <dt>Cheque</dt><dd><?= $escape($movement['check_number'] ?: '-') ?></dd>
            <dt>Serie</dt><dd><?= $escape($movement['series'] ?: '-') ?></dd>
            <dt>Diario contable</dt><dd class="<?= $movement['accounting_id'] === null ? 'status-muted' : 'status-ok' ?>"><?= $movement['accounting_id'] === null ? 'Sin asiento asociado' : 'Asiento #' . $escape($movement['accounting_id']) . ' · ' . $escape($movement['accounting_status']) ?></dd>
        </dl></section>
        <section class="panel"><h2>Auditoria</h2><dl>
            <dt>Fecha de creacion</dt><dd><?= $escape($movement['created_at'] ?: '-') ?></dd>
            <dt>Usuario modificador</dt><dd><?= $escape($movement['updated_by'] ?: '-') ?></dd>
            <dt>Fecha de modificacion</dt><dd><?= $escape($movement['updated_at'] ?: '-') ?></dd>
        </dl></section>
        <section class="panel wide"><h2>Observaciones</h2><p class="note"><?= $escape($movement['observation'] ?: 'Sin observaciones') ?></p><?php if ($movement['observation_two']): ?><p class="note"><?= $escape($movement['observation_two']) ?></p><?php endif; ?></section>
    </div>
</main>
</body>
</html>
