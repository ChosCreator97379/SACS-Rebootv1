<?php
$escape = static fn (mixed $value): string => htmlspecialchars((string) ($value ?? ''), ENT_QUOTES, 'UTF-8');
$money = static function (mixed $value): string {
    return $value === null || $value === '' ? '-' : number_format((float) $value, 2, '.', ',');
};
$page = $entries['page'] ?? 1;
$pages = $entries['pages'] ?? 1;
$previousPage = max(1, $page - 1);
$nextPage = min($pages, $page + 1);
?>
<!doctype html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Diario Contable | SACS Nuevo</title>
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
        table { width: 100%; min-width: 1100px; border-collapse: collapse; font-size: 12px; }
        th { padding: 13px 14px; background: #edf3f1; color: #52636b; font-size: 10px; letter-spacing: .08em; text-align: left; text-transform: uppercase; white-space: nowrap; }
        td { padding: 14px; border-top: 1px solid var(--line); vertical-align: top; }
        tbody tr:hover { background: #fbfcfa; }
        .date, .amount { white-space: nowrap; }
        .amount { text-align: right; font-variant-numeric: tabular-nums; }
        .operation { color: var(--teal); font-weight: bold; }
        .movement-link { color: var(--teal); font-weight: bold; }
        .muted { color: var(--muted); }
        .debe { color: #d64545; }
        .haber { color: #1d7a78; }
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
    <p class="eyebrow">Contabilidad / Diario</p>
    <h1>Diario Contable</h1>
    <p class="intro">Consulta paginada del diario contable. Esta pantalla muestra los asientos contables registrados en el sistema.</p>

    <form class="filters" method="get">
        <input type="hidden" name="module" value="caja">
        <input type="hidden" name="action" value="diario_contable">
        <label>Desde<input type="date" name="date_from" value="<?= $escape($filters['date_from']) ?>"></label>
        <label>Hasta<input type="date" name="date_to" value="<?= $escape($filters['date_to']) ?>"></label>
        <label>Operacion<select name="operation"><option value="">Todas</option><?php foreach ($operations as $operation): ?><option value="<?= $escape($operation['id']) ?>" <?= (string) $filters['operation'] === (string) $operation['id'] ? 'selected' : '' ?>><?= $escape($operation['id'] . ' - ' . $operation['name']) ?></option><?php endforeach; ?></select></label>
        <label>Cliente<input name="client" inputmode="numeric" maxlength="11" value="<?= $escape($filters['client']) ?>" placeholder="Codigo de cliente"></label>
        <label>Codigo Contable<input name="accounting_code" value="<?= $escape($filters['accounting_code']) ?>" placeholder="Ej: 1651"></label>
        <button type="submit">Filtrar</button>
    </form>

    <div class="summary"><span><?= number_format($entries['total'] ?? 0) ?> asientos encontrados</span><span>Pagina <?= $page ?> de <?= $pages ?></span></div>
    <div class="table-wrap">
        <?php if (!($entries['searched'] ?? false)): ?>
            <div class="empty">Aplique filtros para consultar asientos contables.</div>
        <?php elseif (($entries['items'] ?? []) === []): ?>
            <div class="empty">No se encontraron asientos contables para los filtros indicados.</div>
        <?php else: ?>
            <table>
                <thead><tr><th>Codigo</th><th>Fecha</th><th>Movimiento</th><th>Operacion</th><th>Codigo Contable</th><th>Descripcion</th><th>Debe</th><th>Haber</th><th>Usuario</th><th>Estado</th></tr></thead>
                <tbody><?php foreach ($entries['items'] ?? [] as $entry): ?>
                    <tr>
                        <td class="muted">#<?= $escape($entry['id']) ?></td>
                        <td class="date"><?= $escape($entry['date']) ?></td>
                        <td><a class="movement-link" href="?module=caja&amp;movement=<?= $escape($entry['movement_id']) ?>">#<?= $escape($entry['movement_id']) ?></a></td>
                        <td class="operation"><?= $escape($entry['operation_name'] ?? '-') ?><br><span class="muted">Op <?= $escape($entry['operation'] ?? '-') ?></span></td>
                        <td><a class="movement-link" href="?module=caja&amp;action=detalle_asientos&amp;movement=<?= $escape($entry['movement_id']) ?>&amp;date_from=<?= $escape($filters['date_from']) ?>&amp;date_to=<?= $escape($filters['date_to']) ?>&amp;operation=<?= $escape($filters['operation']) ?>&amp;client=<?= $escape($filters['client']) ?>&amp;accounting_code=<?= $escape($filters['accounting_code']) ?>"><?= $escape($entry['accounting_code'] ?? '-') ?></a></td>
                        <td><?= $escape($entry['description'] ?? '-') ?></td>
                        <td class="amount debe"><?= $money($entry['debe']) ?></td>
                        <td class="amount haber"><?= $money($entry['haber']) ?></td>
                        <td><?= $escape($entry['user'] ?? '-') ?></td>
                        <td><span class="muted"><?= $escape($entry['status']) ?></span></td>
                    </tr>
                <?php endforeach; ?></tbody>
            </table>
        <?php endif; ?>
    </div>

    <?php if ($entries['pages'] > 1): ?>
        <div class="pagination">
            <a class="page-link <?= $entries['page'] === 1 ? 'disabled' : '' ?>" href="?module=caja&amp;action=diario_contable&amp;date_from=<?= $escape($filters['date_from']) ?>&amp;date_to=<?= $escape($filters['date_to']) ?>&amp;operation=<?= $escape($filters['operation']) ?>&amp;accounting_code=<?= $escape($filters['accounting_code']) ?>&amp;page=<?= $previousPage ?>">Anterior</a>
            <a class="page-link <?= $entries['page'] === $entries['pages'] ? 'disabled' : '' ?>" href="?module=caja&amp;action=diario_contable&amp;date_from=<?= $escape($filters['date_from']) ?>&amp;date_to=<?= $escape($filters['date_to']) ?>&amp;operation=<?= $escape($filters['operation']) ?>&amp;accounting_code=<?= $escape($filters['accounting_code']) ?>&amp;page=<?= $nextPage ?>">Siguiente</a>
        </div>
    <?php endif; ?>
</main>
</body>
</html>
