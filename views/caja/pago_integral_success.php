<?php
$escape = static fn (mixed $value): string => htmlspecialchars((string) ($value ?? ''), ENT_QUOTES, 'UTF-8');
$money = static fn (mixed $value): string => $value === null || $value === '' ? '-' : number_format((float) $value, 2, '.', ',');

$clienteCodigo = $_GET['client'] ?? '';
$movimientos = $_GET['movimientos'] ?? [];
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pago Integral Exitoso</title>
    <style>
        body {
            font-family: "Trebuchet MS", sans-serif;
            background: #f5f7f4;
            display: flex;
            align-items: center;
            justify-content: center;
            min-height: 100vh;
            margin: 0;
            padding: 20px;
        }
        .success-container {
            background: #fff;
            border: 1px solid #dce4e1;
            border-radius: 7px;
            padding: 40px;
            max-width: 600px;
            width: 100%;
        }
        .success-icon {
            font-size: 48px;
            color: #1d7a78;
            margin-bottom: 20px;
            text-align: center;
        }
        .success-message {
            font-size: 24px;
            color: #17212b;
            margin-bottom: 20px;
            text-align: center;
            font-weight: bold;
        }
        .success-detail {
            font-size: 14px;
            color: #697586;
            margin-bottom: 30px;
            text-align: center;
        }
        .movimientos-list {
            margin-bottom: 30px;
        }
        .movimiento-item {
            background: #f8f9fa;
            border: 1px solid #dce4e1;
            border-radius: 4px;
            padding: 15px;
            margin-bottom: 10px;
        }
        .movimiento-tipo {
            font-weight: bold;
            color: #1d7a78;
            font-size: 14px;
            margin-bottom: 5px;
        }
        .movimiento-codigo {
            font-size: 13px;
            color: #697586;
            margin-bottom: 3px;
        }
        .movimiento-monto {
            font-size: 13px;
            color: #17212b;
            font-weight: bold;
            margin-bottom: 5px;
        }
        .desglose {
            margin-top: 10px;
            padding-top: 10px;
            border-top: 1px solid #dce4e1;
        }
        .desglose-item {
            font-size: 12px;
            color: #697586;
            margin-bottom: 3px;
        }
        .desglose-label {
            font-weight: 600;
            color: #17212b;
        }
        .no-movimientos {
            text-align: center;
            color: #697586;
            font-style: italic;
            padding: 20px;
            background: #f8f9fa;
            border-radius: 4px;
            margin-bottom: 30px;
        }
        .btn {
            padding: 12px 24px;
            border: none;
            border-radius: 4px;
            font-size: 14px;
            font-weight: bold;
            cursor: pointer;
            background: #1d7a78;
            color: white;
            text-decoration: none;
            display: inline-block;
            text-align: center;
        }
        .btn:hover {
            background: #166664;
        }
        .btn-secondary {
            background: #697586;
            margin-right: 10px;
        }
        .btn-secondary:hover {
            background: #5a6369;
        }
        .actions {
            text-align: center;
        }
    </style>
</head>
<body>
    <div class="success-container">
        <div class="success-icon">✓</div>
        <div class="success-message">Pago Integral Exitoso</div>
        <div class="success-detail">
            El pago integral se ha procesado correctamente.
        </div>

        <?php if (!empty($movimientos)): ?>
            <div class="movimientos-list">
                <h3 style="margin: 0 0 15px; font-size: 16px; color: #17212b;">Movimientos Generados:</h3>
                <?php foreach ($movimientos as $movimiento): ?>
                    <div class="movimiento-item">
                        <div class="movimiento-tipo"><?= $escape($movimiento['tipo']) ?></div>
                        <div class="movimiento-codigo">Código de Movimiento: <?= $escape($movimiento['codigo']) ?></div>
                        <div class="movimiento-monto">Monto Total: <?= $money($movimiento['monto']) ?></div>
                        
                        <?php if (!empty($movimiento['desglose'])): ?>
                            <div class="desglose">
                                <?php if (isset($movimiento['desglose']['capital'])): ?>
                                    <div class="desglose-item">
                                        <span class="desglose-label">Capital:</span> <?= $money($movimiento['desglose']['capital']) ?>
                                    </div>
                                <?php endif; ?>
                                <?php if (isset($movimiento['desglose']['interes'])): ?>
                                    <div class="desglose-item">
                                        <span class="desglose-label">Interés:</span> <?= $money($movimiento['desglose']['interes']) ?>
                                    </div>
                                <?php endif; ?>
                                <?php if (isset($movimiento['desglose']['mora'])): ?>
                                    <div class="desglose-item">
                                        <span class="desglose-label">Mora:</span> <?= $money($movimiento['desglose']['mora']) ?>
                                    </div>
                                <?php endif; ?>
                                <?php if (isset($movimiento['desglose']['sobregiro'])): ?>
                                    <div class="desglose-item">
                                        <span class="desglose-label">Sobregiro:</span> <?= $money($movimiento['desglose']['sobregiro']) ?>
                                    </div>
                                <?php endif; ?>
                                <?php if (isset($movimiento['desglose']['gastos_adm'])): ?>
                                    <div class="desglose-item">
                                        <span class="desglose-label">Gastos Adm:</span> <?= $money($movimiento['desglose']['gastos_adm']) ?>
                                    </div>
                                <?php endif; ?>
                                <?php if (isset($movimiento['desglose']['gastos_mora'])): ?>
                                    <div class="desglose-item">
                                        <span class="desglose-label">Gastos Mora:</span> <?= $money($movimiento['desglose']['gastos_mora']) ?>
                                    </div>
                                <?php endif; ?>
                                <?php if (isset($movimiento['desglose']['otros1'])): ?>
                                    <div class="desglose-item">
                                        <span class="desglose-label">Otros 1:</span> <?= $money($movimiento['desglose']['otros1']) ?>
                                    </div>
                                <?php endif; ?>
                                <?php if (isset($movimiento['desglose']['otros2'])): ?>
                                    <div class="desglose-item">
                                        <span class="desglose-label">Otros 2:</span> <?= $money($movimiento['desglose']['otros2']) ?>
                                    </div>
                                <?php endif; ?>
                                <?php if (is_array($movimiento['desglose']) && !isset($movimiento['desglose']['capital'])): ?>
                                    <?php foreach ($movimiento['desglose'] as $periodo => $monto): ?>
                                        <div class="desglose-item">
                                            <span class="desglose-label">Periodo <?= $escape((int) $periodo + 1) ?>:</span> <?= $money($monto) ?>
                                        </div>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </div>
                        <?php endif; ?>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php else: ?>
            <div class="no-movimientos">
                No se generaron movimientos (todos los montos eran 0).
            </div>
        <?php endif; ?>

        <div class="actions">
            <a href="index.php?module=caja&action=pago_integral_seleccion&client=<?= rawurlencode($clienteCodigo) ?>" class="btn btn-secondary">Nuevo Pago Integral</a>
            <a href="index.php?module=socios&action=consulta_rapida&client=<?= rawurlencode($clienteCodigo) ?>" class="btn">Volver al Perfil</a>
        </div>
    </div>
</body>
</html>
