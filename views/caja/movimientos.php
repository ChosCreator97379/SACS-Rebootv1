<?php
$escape = static fn (mixed $value): string => htmlspecialchars((string) ($value ?? ''), ENT_QUOTES, 'UTF-8');
$money = static function (mixed $value): string {
    return $value === null || $value === '' ? '-' : number_format((float) $value, 2, '.', ',');
};
$query = $_GET;
$previousPage = max(1, $movements['page'] - 1);
$nextPage = min($movements['pages'], $movements['page'] + 1);
$selectedClient = trim((string) ($filters['client'] ?? ''));
?>
<!doctype html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Movimientos de Caja | SACS Nuevo</title>
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
        .nav-link.disabled { color: #5a6b7b; pointer-events: none; }
        .main { padding: 40px 48px; overflow-y: auto; }
        .header { display: flex; align-items: center; justify-content: space-between; margin-bottom: 32px; }
        .header h1 { margin: 0; font-size: 28px; font-weight: 400; }
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
        .alert { padding: 15px; margin-bottom: 20px; border-radius: 6px; }
        .alert-error { background: #fee; color: #c33; border: 1px solid #fcc; }
        .btn { padding: 12px 24px; border: none; border-radius: 6px; font-size: 13px; font-weight: 600; cursor: pointer; transition: background .15s; }
        .btn-secondary { background: var(--paper); color: var(--ink); }
        .btn-secondary:hover { background: #e8ebe9; }
        @media (max-width: 900px) { .shell { grid-template-columns: 1fr; } .sidebar { display: none; } .filters { grid-template-columns: repeat(2, minmax(130px, 1fr)); } .filters button { width: 100%; } }
        @media (max-width: 520px) { .filters { grid-template-columns: 1fr; } .summary { align-items: flex-start; flex-direction: column; } }
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
                <?php if ($selectedClient !== ''): ?>
                    <a href="index.php?module=socios&action=consulta_rapida&client=<?= rawurlencode($selectedClient) ?>" class="nav-link">Perfil Cliente</a>
                    <a href="index.php?module=caja&action=aportes_fonbes_form&client=<?= rawurlencode($selectedClient) ?>" class="nav-link">Aportes FONBES</a>
                    <a href="index.php?module=caja&action=aportes_coopam_form&client=<?= rawurlencode($selectedClient) ?>" class="nav-link">Aportes COOPAM</a>
                    <a href="index.php?module=caja&action=aportes_combinados_form&client=<?= rawurlencode($selectedClient) ?>" class="nav-link">Pago FONBES + COOPAM</a>
                    <a href="index.php?module=caja&action=desembolso_prestamo_form&client=<?= rawurlencode($selectedClient) ?>" class="nav-link">Desembolso Préstamo</a>
                    <a href="index.php?module=caja&action=sobregiro_prestamo_form&client=<?= rawurlencode($selectedClient) ?>" class="nav-link">Sobregiro Préstamo</a>
                    <a href="index.php?module=caja&action=amortizar_form&client=<?= rawurlencode($selectedClient) ?>" class="nav-link">Amortización</a>
                    <a href="index.php?module=caja&action=pago_integral_seleccion&client=<?= rawurlencode($selectedClient) ?>" class="nav-link">Pago Integral</a>
                    <a href="index.php?module=caja&action=multas_form&client=<?= rawurlencode($selectedClient) ?>" class="nav-link">Multas</a>
                    <a href="index.php?module=caja&action=devolucion_fonbes_form&client=<?= rawurlencode($selectedClient) ?>" class="nav-link">Devolución FONBES</a>
                    <a href="index.php?module=caja&action=devolucion_coopam_form&client=<?= rawurlencode($selectedClient) ?>" class="nav-link">Devolución COOPAM</a>
                    <a href="index.php?module=caja&action=cancelacion_aee_form&client=<?= rawurlencode($selectedClient) ?>" class="nav-link">Cancelación AEE</a>
                    <a href="index.php?module=caja&action=pago_rifa_form&client=<?= rawurlencode($selectedClient) ?>" class="nav-link">Pago Rifa</a>
                    <a href="index.php?module=caja&action=pago_sobregiro_fonbes_form&client=<?= rawurlencode($selectedClient) ?>" class="nav-link">Pago Sobregiro FONBES</a>
                    <a href="index.php?module=caja&action=pago_sobregiro_coopam_form&client=<?= rawurlencode($selectedClient) ?>" class="nav-link">Pago Sobregiro COOPAM</a>
                    <a href="index.php?module=caja&action=otras_devoluciones_form&client=<?= rawurlencode($selectedClient) ?>" class="nav-link">Otras Devoluciones</a>
                    <a href="index.php?module=caja&action=otros_ingresos_form&client=<?= rawurlencode($selectedClient) ?>" class="nav-link">Otros Ingresos</a>
                    <a href="index.php?module=caja&action=desembolso_beneficios_form&client=<?= rawurlencode($selectedClient) ?>" class="nav-link">Desembolso Beneficios</a>
                    <a href="index.php?module=caja&action=egresos_form&client=<?= rawurlencode($selectedClient) ?>" class="nav-link">Egresos</a>
                <?php else: ?>
                    <span class="nav-link disabled">Perfil Cliente (seleccione cliente)</span>
                    <span class="nav-link disabled">Aportes FONBES (seleccione cliente)</span>
                    <span class="nav-link disabled">Aportes COOPAM (seleccione cliente)</span>
                    <span class="nav-link disabled">Pago FONBES + COOPAM (seleccione cliente)</span>
                    <span class="nav-link disabled">Desembolso Préstamo (seleccione cliente)</span>
                    <span class="nav-link disabled">Sobregiro Préstamo (seleccione cliente)</span>
                    <span class="nav-link disabled">Amortización (seleccione cliente)</span>
                    <span class="nav-link disabled">Pago Integral (seleccione cliente)</span>
                    <span class="nav-link disabled">Multas (seleccione cliente)</span>
                    <span class="nav-link disabled">Devolución FONBES (seleccione cliente)</span>
                    <span class="nav-link disabled">Devolución COOPAM (seleccione cliente)</span>
                    <span class="nav-link disabled">Cancelación AEE (seleccione cliente)</span>
                    <span class="nav-link disabled">Pago Rifa (seleccione cliente)</span>
                    <span class="nav-link disabled">Pago Sobregiro FONBES (seleccione cliente)</span>
                    <span class="nav-link disabled">Pago Sobregiro COOPAM (seleccione cliente)</span>
                    <span class="nav-link disabled">Otras Devoluciones (seleccione cliente)</span>
                <?php endif; ?>
            </nav>
        </aside>

        <main class="main">
            <div class="header">
                <h1>Movimientos</h1>
            </div>
            <p class="intro">Consulta paginada del libro operativo de Caja. Esta pantalla es de solo lectura y no modifica movimientos, saldos ni cuotas.</p>

            <form class="filters" method="get">
                <input type="hidden" name="module" value="caja">
                <label>Desde<input type="date" name="date_from" value="<?= $escape($filters['date_from']) ?>"></label>
                <label>Hasta<input type="date" name="date_to" value="<?= $escape($filters['date_to']) ?>"></label>
                <label>Operacion<select name="operation"><option value="">Todas</option><?php foreach ($operations as $operation): ?><option value="<?= $escape($operation['id']) ?>" <?= (string) $filters['operation'] === (string) $operation['id'] ? 'selected' : '' ?>><?= $escape($operation['id'] . ' - ' . $operation['name']) ?></option><?php endforeach; ?></select></label>
                <label>Cliente<input name="client" inputmode="numeric" maxlength="11" value="<?= $escape($filters['client']) ?>" placeholder="Codigo de socio (opcional si busca por código)"></label>
                <label>Código Movimiento<input name="movement_code" inputmode="numeric" maxlength="11" value="<?= $escape($filters['movement_code'] ?? '') ?>" placeholder="Opcional"></label>
                <button type="submit">Filtrar</button>
            </form>

            <?php if (isset($_GET['error'])): ?>
                <div class="alert alert-error">
                    <?= $escape($_GET['error']) ?>
                    <br><br>
                    <a href="index.php?module=caja" class="btn btn-secondary" style="display: inline-block; margin-top: 10px;">Limpiar búsqueda</a>
                </div>
            <?php elseif (isset($movements['client_not_found']) && $movements['client_not_found']): ?>
                <div class="alert alert-error">
                    Cliente no encontrado. El código <?= $escape($selectedClient) ?> no corresponde a ningún socio registrado.
                    <br><br>
                    <a href="index.php?module=caja" class="btn btn-secondary" style="display: inline-block; margin-top: 10px;">Limpiar búsqueda</a>
                </div>
            <?php endif; ?>

            <div class="summary"><span><?= number_format($movements['total']) ?> movimientos encontrados</span><span>Pagina <?= $movements['page'] ?> de <?= $movements['pages'] ?></span></div>
            <div class="table-wrap">
                <?php if (!$movements['searched']): ?>
                    <div class="empty">Ingrese un codigo de cliente para consultar sus movimientos.</div>
                <?php elseif ($movements['items'] === []): ?>
                    <div class="empty">No se encontraron movimientos para el cliente y filtros indicados.</div>
                <?php else: ?>
                    <table>
                        <thead><tr><th>Codigo</th><th>Fecha</th><th>Operacion</th><th>Cliente</th><th>Amortización</th><th>Gastos Admin</th><th>Gastos Mora</th><th>Otros</th><th>Total</th><th>Forma de pago</th><th>Usuario</th><th>Observacion</th></tr></thead>
                        <tbody><?php foreach ($movements['items'] as $movement): ?>
                            <?php
                            // Calcular total sumando todos los componentes
                            $total = 0;
                            if (isset($movement['loan_payment_amount'])) $total += (float)$movement['loan_payment_amount'];
                            if (isset($movement['contribution_amount'])) $total += (float)$movement['contribution_amount'];
                            if (isset($movement['disbursement_amount'])) $total += (float)$movement['disbursement_amount'];
                            if (isset($movement['benefit_amount'])) $total += (float)$movement['benefit_amount'];
                            if (isset($movement['fine_amount'])) $total += (float)$movement['fine_amount'];
                            if (isset($movement['raffle_amount'])) $total += (float)$movement['raffle_amount'];
                            if (isset($movement['refund_amount'])) $total += (float)$movement['refund_amount'];
                            if (isset($movement['expense_amount'])) $total += (float)$movement['expense_amount'];
                            if (isset($movement['income_amount'])) $total += (float)$movement['income_amount'];
                            if (isset($movement['admin_expense'])) $total += (float)$movement['admin_expense'];
                            if (isset($movement['late_fee'])) $total += (float)$movement['late_fee'];
                            if (isset($movement['other_one'])) $total += (float)$movement['other_one'];
                            if (isset($movement['other_two'])) $total += (float)$movement['other_two'];

                            // Otros (suma de otros1 y otros2)
                            $otros = 0;
                            if (isset($movement['other_one'])) $otros += (float)$movement['other_one'];
                            if (isset($movement['other_two'])) $otros += (float)$movement['other_two'];
                            ?>
                            <?php $clientLabel = trim((string) ($movement['client_name'] ?? '')) ?: 'Sin nombre registrado'; ?>
                            <tr>
                                <td class="muted"><a href="?module=caja&amp;movement=<?= $escape($movement['id']) ?>">#<?= $escape($movement['id']) ?></a></td>
                                <td class="date"><?= $escape($movement['operation_date']) ?></td>
                                <td class="operation"><?= $escape($movement['operation_name']) ?><br><span class="muted">Operacion <?= $escape($movement['operation_id']) ?></span></td>
                                <td><a class="client-link" href="?module=socios&amp;action=consulta_rapida&amp;client=<?= rawurlencode((string) $movement['client_id']) ?>">Cliente #<?= $escape($movement['client_id']) ?></a><br><span class="muted"><?= $escape($clientLabel) ?></span></td>
                                <td class="amount"><?= $money($movement['loan_payment_amount'] ?? 0) ?></td>
                                <td class="amount"><?= $money($movement['admin_expense'] ?? 0) ?></td>
                                <td class="amount"><?= $money($movement['late_fee'] ?? 0) ?></td>
                                <td class="amount"><?= $money($otros) ?></td>
                                <td class="amount" style="font-weight: bold;"><?= $money($total) ?></td>
                                <td><?= $escape($movement['payment_method'] ?: '-') ?></td>
                                <td><?= $escape($movement['created_by'] ?: '-') ?></td>
                                <td class="muted"><?= $escape($movement['observation'] ?: '-') ?></td>
                            </tr>
                        <?php endforeach; ?></tbody>
                    </table>
                <?php endif; ?>
            </div>
            <nav class="pagination" aria-label="Paginacion"><a class="page-link <?= $movements['page'] <= 1 ? 'disabled' : '' ?>" href="?<?= $escape(http_build_query(array_merge($query, ['page' => $previousPage]))) ?>">Anterior</a><a class="page-link <?= $movements['page'] >= $movements['pages'] ? 'disabled' : '' ?>" href="?<?= $escape(http_build_query(array_merge($query, ['page' => $nextPage]))) ?>">Siguiente</a></nav>
        </main>
    </div>
</body>
</html>
