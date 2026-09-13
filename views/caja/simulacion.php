<?php
$escape = static fn (mixed $value): string => htmlspecialchars((string) ($value ?? ''), ENT_QUOTES, 'UTF-8');
$money = static fn (mixed $value): string => number_format((float) $value, 2, '.', ',');
$components = [
    'Capital' => $simulation['capital'],
    'Interes' => $simulation['interest'],
    'Mora de cuota' => $simulation['installment_late_fee'],
    'Sobregiro' => $simulation['overdraft'],
    'Interes de sobregiro' => $simulation['overdraft_interest'],
    'Mora de sobregiro' => $simulation['overdraft_late_fee'],
];
?>
<!doctype html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Simulacion de pago | SACS Nuevo</title>
    <style>
        :root { --ink:#17212b; --muted:#687684; --paper:#f5f7f4; --panel:#fff; --navy:#142b3a; --teal:#1d7a78; --gold:#d69e47; --line:#dce4e1; }
        * { box-sizing:border-box; } body { margin:0; background:var(--paper); color:var(--ink); font-family:"Trebuchet MS",sans-serif; } a { color:inherit; text-decoration:none; }
        .top { display:flex; justify-content:space-between; padding:22px clamp(20px,4vw,58px); background:var(--navy); color:#fff; } .brand { font-family:Georgia,serif; font-size:24px; } .brand small { display:block; margin-top:4px; color:#9bb3b7; font-family:"Trebuchet MS",sans-serif; font-size:10px; letter-spacing:.13em; text-transform:uppercase; } .back { color:#b9d2d2; font-size:13px; }
        main { max-width:850px; margin:0 auto; padding:38px clamp(20px,4vw,58px); } .eyebrow { margin:0 0 9px; color:var(--teal); font-size:11px; font-weight:bold; letter-spacing:.16em; text-transform:uppercase; } h1 { margin:0; font-family:Georgia,serif; font-size:clamp(32px,4vw,48px); font-weight:normal; } .intro { color:var(--muted); line-height:1.5; }
        .notice { margin:22px 0; padding:15px 18px; border-left:4px solid var(--gold); background:#fff8e9; color:#6d572e; font-size:13px; line-height:1.5; } .grid { display:grid; grid-template-columns:1fr 1fr; gap:18px; } .panel { padding:24px; border:1px solid var(--line); border-radius:7px; background:var(--panel); } h2 { margin:0 0 18px; font-family:Georgia,serif; font-size:21px; font-weight:normal; } dl { display:grid; grid-template-columns:1fr auto; gap:12px; margin:0; font-size:13px; } dt { color:var(--muted); } dd { margin:0; font-weight:bold; text-align:right; } .components { display:grid; gap:11px; } .component { display:flex; justify-content:space-between; padding-bottom:9px; border-bottom:1px solid var(--line); font-size:13px; } .component strong { color:var(--teal); } .total { margin-top:18px; padding-top:15px; border-top:2px solid var(--navy); font-size:16px; font-weight:bold; } .wide { grid-column:1 / -1; } .muted { color:var(--muted); font-size:12px; line-height:1.5; } .back-button { display:inline-block; margin-top:20px; padding:10px 14px; border:1px solid var(--line); border-radius:4px; color:var(--teal); font-size:12px; }
        @media (max-width:650px) { .top { align-items:flex-start; flex-direction:column; gap:12px; } .grid { grid-template-columns:1fr; } .wide { grid-column:auto; } }
    </style>
</head>
<body>
<header class="top"><a class="brand" href="index.php">SACS<small>gestion social</small></a><a class="back" href="index.php?module=caja&amp;profile=client&amp;client=<?= rawurlencode((string) $simulation['client_id']) ?>">Volver al cliente</a></header>
<main>
    <p class="eyebrow">Caja / Simulacion</p>
    <h1>Pago de cuota #<?= $escape($simulation['installment_number']) ?></h1>
    <p class="intro">Prestamo #<?= $escape($simulation['loan_id']) ?> · Fecha <?= $escape($simulation['installment_date']) ?> · Esta pantalla calcula una referencia y no modifica la base de datos.</p>
    <div class="notice"><strong>Solo simulacion:</strong> no se ejecutan INSERT, UPDATE ni DELETE. Las tablas que se modificarian en un pago real son: <?= $escape(implode(', ', $simulation['writes'])) ?>.</div>
    <div class="grid">
        <section class="panel"><h2>Estado antes del pago</h2><dl><dt>Saldo del prestamo</dt><dd><?= $money($simulation['loan_balance']) ?></dd><dt>Cuotas pendientes</dt><dd><?= $escape($simulation['pending_installments']) ?></dd><dt>Estado del prestamo</dt><dd><?= $escape($simulation['loan_status']) ?></dd></dl></section>
        <section class="panel"><h2>Componentes</h2><div class="components"><?php foreach ($components as $label => $value): ?><div class="component"><span><?= $escape($label) ?></span><strong><?= $money($value) ?></strong></div><?php endforeach; ?><div class="total"><span>Total simulado</span><strong><?= $money($simulation['simulated_total']) ?></strong></div></div></section>
        <section class="panel wide"><h2>Resultado estimado</h2><dl><dt>Saldo posterior estimado</dt><dd><?= $money($simulation['simulated_balance']) ?></dd><dt>Cuota seleccionada</dt><dd>#<?= $escape($simulation['installment_number']) ?></dd></dl><p class="muted">El saldo estimado descuenta únicamente el capital de esta cuota. Mora, sobregiro y otros conceptos deben validarse contra las reglas completas del legacy antes de implementar el pago real.</p></section>
    </div>
    <a class="back-button" href="index.php?module=caja&amp;profile=client&amp;client=<?= rawurlencode((string) $simulation['client_id']) ?>">Volver al perfil</a>
</main>
</body>
</html>
