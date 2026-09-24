<?php
$escape = static fn (mixed $value): string => htmlspecialchars((string) ($value ?? ''), ENT_QUOTES, 'UTF-8');
$money = static function (mixed $value): string {
    return $value === null || $value === '' ? '-' : number_format((float) $value, 2, '.', ',');
};
?>
<!doctype html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Detalle de Movimiento #<?= $escape($movement['id']) ?> | SACS Nuevo</title>
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
        .panel { padding: 24px; border: 1px solid var(--line); border-radius: 7px; background: var(--panel); margin-bottom: 24px; }
        .panel h2 { margin: 0 0 20px; font-size: 18px; font-weight: 400; }
        .detail-row { display: flex; justify-content: space-between; padding: 12px 0; border-bottom: 1px solid var(--line); }
        .detail-row:last-child { border-bottom: none; }
        .detail-label { color: var(--muted); font-size: 12px; font-weight: bold; letter-spacing: .04em; }
        .detail-value { font-size: 14px; }
        .detail-value.highlight { color: var(--teal); font-weight: bold; }
        .btn-back { padding: 8px 16px; border: 1px solid var(--line); border-radius: 4px; background: var(--panel); color: var(--navy); cursor: pointer; font-size: 13px; }
        .btn-back:hover { background: #e7f0ed; }
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
                <a href="index.php?module=caja" class="nav-link">Movimientos</a>
                <a href="index.php?module=caja&action=diario_contable" class="nav-link">Diario Contable</a>
            </nav>
        </aside>

        <main class="main">
            <div class="header">
                <h1>Detalle de Movimiento #<?= $escape($movement['id']) ?></h1>
                <a href="index.php?module=caja&client=<?= $escape($movement['client_id']) ?>" class="btn-back">Volver a Movimientos</a>
            </div>
            <p class="intro">Información detallada del movimiento seleccionado.</p>

            <div class="panel">
                <h2>Información General</h2>
                <div class="detail-row">
                    <span class="detail-label">Código de Movimiento</span>
                    <span class="detail-value highlight">#<?= $escape($movement['id']) ?></span>
                </div>
                <div class="detail-row">
                    <span class="detail-label">Fecha de Operación</span>
                    <span class="detail-value"><?= $escape($movement['operation_date']) ?></span>
                </div>
                <div class="detail-row">
                    <span class="detail-label">Operación</span>
                    <span class="detail-value"><?= $escape($movement['operation_name']) ?> (<?= $escape($movement['operation_id']) ?>)</span>
                </div>
                <div class="detail-row">
                    <span class="detail-label">Usuario</span>
                    <span class="detail-value"><?= $escape($movement['created_by'] ?? '-') ?></span>
                </div>
                <div class="detail-row">
                    <span class="detail-label">Observación</span>
                    <span class="detail-value"><?= $escape($movement['observation'] ?? '-') ?></span>
                </div>
            </div>

            <div class="panel">
                <h2>Información del Cliente</h2>
                <div class="detail-row">
                    <span class="detail-label">Código de Cliente</span>
                    <span class="detail-value highlight">#<?= $escape($movement['client_id']) ?></span>
                </div>
                <div class="detail-row">
                    <span class="detail-label">Nombre del Cliente</span>
                    <span class="detail-value"><?= $escape($movement['client_name'] ?? 'Sin nombre registrado') ?></span>
                </div>
            </div>

            <div class="panel">
                <h2>Detalles del Monto</h2>
                <?php if (isset($movement['loan_payment_amount'])): ?>
                    <div class="detail-row">
                        <span class="detail-label">Amortización (Capital)</span>
                        <span class="detail-value highlight"><?= $money($movement['loan_payment_amount']) ?></span>
                    </div>
                <?php endif; ?>
                <?php if (isset($movement['admin_expense']) && $movement['admin_expense'] > 0): ?>
                    <div class="detail-row">
                        <span class="detail-label">Gastos Administrativos</span>
                        <span class="detail-value highlight"><?= $money($movement['admin_expense']) ?></span>
                    </div>
                <?php endif; ?>
                <?php if (isset($movement['late_fee']) && $movement['late_fee'] > 0): ?>
                    <div class="detail-row">
                        <span class="detail-label">Gastos Mora</span>
                        <span class="detail-value highlight"><?= $money($movement['late_fee']) ?></span>
                    </div>
                <?php endif; ?>
                <?php if (isset($movement['other_one']) && $movement['other_one'] > 0): ?>
                    <div class="detail-row">
                        <span class="detail-label">Otros 1</span>
                        <span class="detail-value highlight"><?= $money($movement['other_one']) ?></span>
                    </div>
                <?php endif; ?>
                <?php if (isset($movement['other_two']) && $movement['other_two'] > 0): ?>
                    <div class="detail-row">
                        <span class="detail-label">Otros 2</span>
                        <span class="detail-value highlight"><?= $money($movement['other_two']) ?></span>
                    </div>
                <?php endif; ?>
                <?php if (isset($movement['contribution_amount'])): ?>
                    <div class="detail-row">
                        <span class="detail-label">Aportación</span>
                        <span class="detail-value highlight"><?= $money($movement['contribution_amount']) ?></span>
                    </div>
                <?php endif; ?>
                <?php if (isset($movement['disbursement_amount'])): ?>
                    <div class="detail-row">
                        <span class="detail-label">Desembolso</span>
                        <span class="detail-value highlight"><?= $money($movement['disbursement_amount']) ?></span>
                    </div>
                <?php endif; ?>
                <?php if (isset($movement['benefit_amount'])): ?>
                    <div class="detail-row">
                        <span class="detail-label">Beneficio</span>
                        <span class="detail-value highlight"><?= $money($movement['benefit_amount']) ?></span>
                    </div>
                <?php endif; ?>
                <?php if (isset($movement['fine_amount'])): ?>
                    <div class="detail-row">
                        <span class="detail-label">Multa</span>
                        <span class="detail-value highlight"><?= $money($movement['fine_amount']) ?></span>
                    </div>
                <?php endif; ?>
                <?php if (isset($movement['raffle_amount'])): ?>
                    <div class="detail-row">
                        <span class="detail-label">Rifa</span>
                        <span class="detail-value highlight"><?= $money($movement['raffle_amount']) ?></span>
                    </div>
                <?php endif; ?>
                <?php if (isset($movement['refund_amount'])): ?>
                    <div class="detail-row">
                        <span class="detail-label">Devolución</span>
                        <span class="detail-value highlight"><?= $money($movement['refund_amount']) ?></span>
                    </div>
                <?php endif; ?>
                <?php if (isset($movement['expense_amount'])): ?>
                    <div class="detail-row">
                        <span class="detail-label">Egreso</span>
                        <span class="detail-value highlight"><?= $money($movement['expense_amount']) ?></span>
                    </div>
                <?php endif; ?>
                <?php if (isset($movement['income_amount'])): ?>
                    <div class="detail-row">
                        <span class="detail-label">Ingreso</span>
                        <span class="detail-value highlight"><?= $money($movement['income_amount']) ?></span>
                    </div>
                <?php endif; ?>
                <div class="detail-row">
                    <span class="detail-label">Forma de Pago</span>
                    <span class="detail-value"><?= $escape($movement['payment_method'] ?? '-') ?></span>
                </div>
                <?php
                // Calcular total
                $total = 0;
                if (isset($movement['loan_payment_amount'])) $total += (float)$movement['loan_payment_amount'];
                if (isset($movement['admin_expense'])) $total += (float)$movement['admin_expense'];
                if (isset($movement['late_fee'])) $total += (float)$movement['late_fee'];
                if (isset($movement['other_one'])) $total += (float)$movement['other_one'];
                if (isset($movement['other_two'])) $total += (float)$movement['other_two'];
                if (isset($movement['contribution_amount'])) $total += (float)$movement['contribution_amount'];
                if (isset($movement['disbursement_amount'])) $total += (float)$movement['disbursement_amount'];
                if (isset($movement['benefit_amount'])) $total += (float)$movement['benefit_amount'];
                if (isset($movement['fine_amount'])) $total += (float)$movement['fine_amount'];
                if (isset($movement['raffle_amount'])) $total += (float)$movement['raffle_amount'];
                if (isset($movement['refund_amount'])) $total += (float)$movement['refund_amount'];
                if (isset($movement['expense_amount'])) $total += (float)$movement['expense_amount'];
                if (isset($movement['income_amount'])) $total += (float)$movement['income_amount'];
                ?>
                <div class="detail-row" style="border-top: 2px solid var(--navy); margin-top: 8px; padding-top: 12px;">
                    <span class="detail-label" style="font-size: 14px;">TOTAL</span>
                    <span class="detail-value highlight" style="font-size: 16px;"><?= $money($total) ?></span>
                </div>
            </div>

            <?php if (!empty($movement['accounting_entries'])): ?>
                <div class="panel">
                    <h2>Asientos Contables</h2>
                    <?php foreach ($movement['accounting_entries'] as $entry): ?>
                        <div class="detail-row">
                            <span class="detail-label"><?= $escape($entry['account_code'] ?? '') ?> - <?= $escape($entry['account_name'] ?? '') ?></span>
                            <span class="detail-value <?= $entry['debit'] > 0 ? 'highlight' : '' ?>">
                                <?= $entry['debit'] > 0 ? 'Debe: ' . $money($entry['debit']) : 'Haber: ' . $money($entry['credit']) ?>
                            </span>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </main>
    </div>
</body>
</html>