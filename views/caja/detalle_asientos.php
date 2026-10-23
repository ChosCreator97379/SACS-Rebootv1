<?php
$escape = static fn (mixed $value): string => htmlspecialchars((string) ($value ?? ''), ENT_QUOTES, 'UTF-8');
$money = static function (mixed $value): string {
    return $value === null || $value === '' ? '-' : number_format((float) $value, 2, '.', ',');
};

// Preservar filtros para volver al diario
$backUrl = 'index.php?module=caja&action=diario_contable';
if (!empty($_GET['date_from'])) {
    $backUrl .= '&date_from=' . $escape($_GET['date_from']);
}
if (!empty($_GET['date_to'])) {
    $backUrl .= '&date_to=' . $escape($_GET['date_to']);
}
if (!empty($_GET['operation'])) {
    $backUrl .= '&operation=' . $escape($_GET['operation']);
}
if (!empty($_GET['client'])) {
    $backUrl .= '&client=' . $escape($_GET['client']);
}
if (!empty($_GET['accounting_code'])) {
    $backUrl .= '&accounting_code=' . $escape($_GET['accounting_code']);
}
?>
<!doctype html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Detalle de Asientos Contables | SACS Nuevo</title>
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
        .movement-info { display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 16px; padding: 20px; border: 1px solid var(--line); border-radius: 7px; background: var(--panel); margin-bottom: 24px; }
        .info-item { display: flex; flex-direction: column; gap: 4px; }
        .info-label { font-size: 11px; font-weight: bold; color: var(--muted); letter-spacing: .04em; text-transform: uppercase; }
        .info-value { font-size: 15px; font-weight: 500; }
        .table-wrap { overflow-x: auto; border: 1px solid var(--line); border-radius: 7px; background: var(--panel); }
        table { width: 100%; min-width: 1100px; border-collapse: collapse; font-size: 12px; }
        th { padding: 13px 14px; background: #edf3f1; color: #52636b; font-size: 10px; letter-spacing: .08em; text-align: left; text-transform: uppercase; white-space: nowrap; }
        td { padding: 14px; border-top: 1px solid var(--line); vertical-align: top; }
        tbody tr:hover { background: #fbfcfa; }
        .date, .amount { white-space: nowrap; }
        .amount { text-align: right; font-variant-numeric: tabular-nums; }
        .operation { color: var(--teal); font-weight: bold; }
        .debe { color: #d64545; }
        .haber { color: #1d7a78; }
        .total-row { background: #edf3f1; font-weight: bold; }
        .empty { padding: 42px; color: var(--muted); text-align: center; }
        .btn { padding: 10px 20px; border: none; border-radius: 4px; font-size: 14px; font-weight: 500; cursor: pointer; background: var(--teal); color: white; text-decoration: none; display: inline-block; }
        .btn:hover { background: #166664; }
    </style>
</head>
<body>
<header class="top"><a class="brand" href="index.php">SACS<small>gestion social</small></a><a class="back" href="<?= $backUrl ?>">Volver al Diario</a></header>
<main>
    <p class="eyebrow">Contabilidad / Diario</p>
    <h1>Detalle de Asientos Contables</h1>
    <p class="intro">Detalle completo de los asientos contables del movimiento #<?= $escape($movementId) ?></p>

    <div class="movement-info">
        <div class="info-item">
            <span class="info-label">Movimiento</span>
            <span class="info-value">#<?= $escape($movementId) ?></span>
        </div>
        <div class="info-item">
            <span class="info-label">Cliente</span>
            <span class="info-value"><?= $escape($entries[0]['client_id'] ?? '-') ?></span>
        </div>
        <div class="info-item">
            <span class="info-label">Total Asientos</span>
            <span class="info-value"><?= count($entries) ?></span>
        </div>
    </div>

    <div class="table-wrap">
        <?php if (empty($entries)): ?>
            <div class="empty">No hay asientos contables para este movimiento.</div>
        <?php else: ?>
            <table>
                <thead><tr><th>Codigo</th><th>Fecha</th><th>Operacion</th><th>Codigo Contable</th><th>Descripcion</th><th>Debe</th><th>Haber</th><th>Usuario</th><th>Estado</th></tr></thead>
                <tbody>
                    <?php 
                    $totalDebe = 0;
                    $totalHaber = 0;
                    foreach ($entries as $entry): 
                        $totalDebe += (float) ($entry['debe'] ?? 0);
                        $totalHaber += (float) ($entry['haber'] ?? 0);
                    ?>
                    <tr>
                        <td class="muted">#<?= $escape($entry['id']) ?></td>
                        <td class="date"><?= $escape($entry['date']) ?></td>
                        <td class="operation"><?= $escape($entry['operation_name'] ?? '-') ?><br><span class="muted">Op <?= $escape($entry['operation'] ?? '-') ?></span></td>
                        <td><?= $escape($entry['accounting_code'] ?? '-') ?></td>
                        <td><?= $escape($entry['description'] ?? '-') ?></td>
                        <td class="amount debe"><?= $money($entry['debe']) ?></td>
                        <td class="amount haber"><?= $money($entry['haber']) ?></td>
                        <td><?= $escape($entry['user'] ?? '-') ?></td>
                        <td><span class="muted"><?= $escape($entry['status']) ?></span></td>
                    </tr>
                    <?php endforeach; ?>
                    <tr class="total-row">
                        <td colspan="5" style="text-align: right; font-weight: bold;">TOTAL:</td>
                        <td class="amount debe"><?= $money($totalDebe) ?></td>
                        <td class="amount haber"><?= $money($totalHaber) ?></td>
                        <td colspan="2"></td>
                    </tr>
                </tbody>
            </table>
        <?php endif; ?>
    </div>

    <div style="margin-top: 24px;">
        <a href="<?= $backUrl ?>" class="btn">Volver al Diario Contable</a>
    </div>
</main>
</body>
</html>
