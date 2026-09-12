<?php
$escape = static fn (mixed $value): string => htmlspecialchars((string) ($value ?? ''), ENT_QUOTES, 'UTF-8');
$money = static function (mixed $value): string {
    return $value === null || $value === '' ? '-' : number_format((float) $value, 2, '.', ',');
};
$query = $_GET;
$previousPage = max(1, $movements['page'] - 1);
$nextPage = min($movements['pages'], $movements['page'] + 1);
?>
<!doctype html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Movimientos de Caja | SACS Nuevo</title>
    <style>
        :root { --ink: #17212b; --muted: #687684; --paper: #f5f7f4; --panel: #fff; --navy: #142b3a; --teal: #1d7a78; --gold: #d69e47; --line: #dce4e1; }
        * { box-sizing: border-box; }
        body { margin: 0; background: var(--paper); color: var(--ink); font-family: "Trebuchet MS", sans-serif; }
        a { color: inherit; text-decoration: none; }
        .top { display: flex; align-items: center; justify-content: space-between; gap: 20px; padding: 22px clamp(20px, 4vw, 58px); background: var(--navy); color: #fff; }
        .brand { font-family: Georgia, serif; font-size: 24px; }
        .brand small { display: block; margin-top: 4px; color: #9bb3b7; font-family: "Trebuchet MS", sans-serif; font-size: 10px; letter-spacing: .13em; text-transform: uppercase; }
        .back { color: #b9d2d2; font-size: 13px; }
        main { max-width: 1440px; margin: 0 auto; padding: 38px clamp(20px, 4vw, 58px); }
        .eyebrow { margin: 0 0 9px; color: var(--teal); font-size: 11px; font-weight: bold; letter-spacing: .16em; text-transform: uppercase; }
        h1 { margin: 0; font-family: Georgia, serif; font-size: clamp(32px, 4vw, 48px); font-weight: normal; }
        .intro { max-width: 640px; margin: 12px 0 28px; color: var(--muted); line-height: 1.55; }
        .filters { display: grid; grid-template-columns: repeat(4, minmax(130px, 1fr)) auto; gap: 12px; align-items: end; padding: 18px; border: 1px solid var(--line); border-radius: 7px; background: var(--panel); }
        label { display: grid; gap: 6px; color: var(--muted); font-size: 11px; font-weight: bold; letter-spacing: .04em; }
        input, select, button { min-height: 38px; padding: 8px 10px; border: 1px solid #b9c7c7; border-radius: 4px; background: #fff; color: var(--ink); font: inherit; }
        button { border-color: var(--teal); background: var(--teal); color: #fff; cursor: pointer; font-size: 13px; font-weight: bold; }
        .summary { display: flex; justify-content: space-between; gap: 20px; margin: 25px 0 12px; color: var(--muted); font-size: 13px; }
        .table-wrap { overflow-x: auto; border: 1px solid var(--line); border-radius: 7px; background: var(--panel); }
        table { width: 100%; min-width: 980px; border-collapse: collapse; font-size: 12px; }
        th { padding: 13px 14px; background: #edf3f1; color: #52636b; font-size: 10px; letter-spacing: .08em; text-align: left; text-transform: uppercase; white-space: nowrap; }
        td { padding: 14px; border-top: 1px solid var(--line); vertical-align: top; }
        tbody tr:hover { background: #fbfcfa; }
        .date, .amount { white-space: nowrap; }
        .amount { text-align: right; font-variant-numeric: tabular-nums; }
        .operation { color: var(--teal); font-weight: bold; }
        .client-link { color: var(--teal); font-weight: bold; }
        .muted { color: var(--muted); }
        .pagination { display: flex; justify-content: flex-end; gap: 8px; margin-top: 16px; }
        .page-link { padding: 8px 12px; border: 1px solid var(--line); border-radius: 4px; background: var(--panel); color: var(--teal); font-size: 12px; }
        .page-link.disabled { color: #a8b4b4; pointer-events: none; }
        .empty { padding: 42px; color: var(--muted); text-align: center; }
        @media (max-width: 900px) { .filters { grid-template-columns: repeat(2, minmax(130px, 1fr)); } .filters button { width: 100%; } }
        @media (max-width: 520px) { .top { align-items: flex-start; flex-direction: column; } .filters { grid-template-columns: 1fr; } .summary { align-items: flex-start; flex-direction: column; } }
    </style>
</head>
<body>
<header class="top"><a class="brand" href="index.php">SACS<small>gestion social</small></a><a class="back" href="index.php">Volver al resumen</a></header>
<main>
    <p class="eyebrow">Caja / Consulta</p>
    <h1>Movimientos</h1>
    <p class="intro">Consulta paginada del libro operativo de Caja. Esta pantalla es de solo lectura y no modifica movimientos, saldos ni cuotas.</p>

    <form class="filters" method="get">
        <input type="hidden" name="module" value="caja">
        <label>Desde<input type="date" name="date_from" value="<?= $escape($filters['date_from']) ?>"></label>
        <label>Hasta<input type="date" name="date_to" value="<?= $escape($filters['date_to']) ?>"></label>
        <label>Operacion<select name="operation"><option value="">Todas</option><?php foreach ($operations as $operation): ?><option value="<?= $escape($operation['id']) ?>" <?= (string) $filters['operation'] === (string) $operation['id'] ? 'selected' : '' ?>><?= $escape($operation['id'] . ' - ' . $operation['name']) ?></option><?php endforeach; ?></select></label>
        <label>Cliente<input name="client" inputmode="numeric" maxlength="11" required value="<?= $escape($filters['client']) ?>" placeholder="Codigo de socio"></label>
        <button type="submit">Filtrar</button>
    </form>

    <div class="summary"><span><?= number_format($movements['total']) ?> movimientos encontrados</span><span>Pagina <?= $movements['page'] ?> de <?= $movements['pages'] ?></span></div>
    <div class="table-wrap">
        <?php if (!$movements['searched']): ?>
            <div class="empty">Ingrese un codigo de cliente para consultar sus movimientos.</div>
        <?php elseif ($movements['items'] === []): ?>
            <div class="empty">No se encontraron movimientos para el cliente y filtros indicados.</div>
        <?php else: ?>
            <table>
                <thead><tr><th>Codigo</th><th>Fecha</th><th>Operacion</th><th>Cliente</th><th>Importe principal</th><th>Forma de pago</th><th>Usuario</th><th>Observacion</th></tr></thead>
                <tbody><?php foreach ($movements['items'] as $movement): ?>
                    <?php $principal = $movement['loan_payment_amount'] ?? $movement['contribution_amount'] ?? $movement['disbursement_amount'] ?? $movement['benefit_amount'] ?? $movement['fine_amount'] ?? $movement['raffle_amount'] ?? $movement['refund_amount'] ?? $movement['expense_amount'] ?? $movement['income_amount']; ?>
                    <?php $clientLabel = trim((string) ($movement['client_name'] ?? '')) ?: 'Sin nombre registrado'; ?>
                    <tr><td class="muted"><a href="?module=caja&amp;movement=<?= $escape($movement['id']) ?>">#<?= $escape($movement['id']) ?></a></td><td class="date"><?= $escape($movement['operation_date']) ?></td><td class="operation"><?= $escape($movement['operation_name']) ?><br><span class="muted">Operacion <?= $escape($movement['operation_id']) ?></span></td><td><a class="client-link" href="?module=caja&amp;profile=client&amp;client=<?= rawurlencode((string) $movement['client_id']) ?>">Cliente #<?= $escape($movement['client_id']) ?></a><br><span class="muted"><?= $escape($clientLabel) ?></span></td><td class="amount"><?= $money($principal) ?></td><td><?= $escape($movement['payment_method'] ?: '-') ?></td><td><?= $escape($movement['created_by'] ?: '-') ?></td><td class="muted"><?= $escape($movement['observation'] ?: '-') ?></td></tr>
                <?php endforeach; ?></tbody>
            </table>
        <?php endif; ?>
    </div>
    <nav class="pagination" aria-label="Paginacion"><a class="page-link <?= $movements['page'] <= 1 ? 'disabled' : '' ?>" href="?<?= $escape(http_build_query(array_merge($query, ['page' => $previousPage]))) ?>">Anterior</a><a class="page-link <?= $movements['page'] >= $movements['pages'] ? 'disabled' : '' ?>" href="?<?= $escape(http_build_query(array_merge($query, ['page' => $nextPage]))) ?>">Siguiente</a></nav>
</main>
</body>
</html>
