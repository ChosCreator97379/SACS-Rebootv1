<?php
$escape = static fn (mixed $value): string => htmlspecialchars((string) ($value ?? ''), ENT_QUOTES, 'UTF-8');
$reason = $_GET['reason'] ?? 'Estado desconocido';
$clienteCodigo = $_GET['client'] ?? '';
$prestamoId = $_GET['loan'] ?? '';
?>
<!doctype html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Cuenta No Pagable | SACS Nuevo</title>
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
            --error: #c53030;
            --error-bg: #fed7d7;
            --error-border: #feb2b2;
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
        .main { padding: 40px 48px; overflow-y: auto; }
        .header { display: flex; align-items: center; justify-content: space-between; margin-bottom: 32px; }
        .header h1 { margin: 0; font-size: 28px; font-weight: 400; }
        .intro { max-width: 640px; margin: 12px 0 28px; color: var(--muted); line-height: 1.55; }
        .error-box { padding: 24px; border: 1px solid var(--error-border); border-radius: 7px; background: var(--error-bg); margin-bottom: 24px; }
        .error-box h2 { margin: 0 0 12px; color: var(--error); font-size: 18px; font-weight: 400; }
        .error-box p { margin: 0 0 8px; color: var(--error); font-size: 14px; }
        .error-box p:last-child { margin-bottom: 0; }
        .info-box { padding: 24px; border: 1px solid var(--line); border-radius: 7px; background: var(--panel); margin-bottom: 24px; }
        .info-box h3 { margin: 0 0 16px; font-size: 16px; font-weight: 400; }
        .info-box ul { margin: 0; padding-left: 20px; }
        .info-box li { margin-bottom: 8px; font-size: 14px; }
        .actions { display: flex; gap: 12px; margin-top: 24px; }
        button { min-height: 38px; padding: 8px 20px; border: none; border-radius: 4px; background: var(--teal); color: #fff; cursor: pointer; font-size: 13px; font-weight: bold; }
        button:hover { background: #166664; }
        button.secondary { background: #e7f0ed; color: var(--navy); }
        button.secondary:hover { background: #dce4e1; }
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
                <?php if ($clienteCodigo !== ''): ?>
                    <a href="index.php?module=caja&profile=client&client=<?= rawurlencode($clienteCodigo) ?>" class="nav-link">Perfil Cliente</a>
                <?php endif; ?>
            </nav>
        </aside>

        <main class="main">
            <div class="header">
                <h1>Cuenta No Pagable</h1>
            </div>
            <p class="intro">Esta cuenta de préstamo no permite realizar pagos de amortización en este momento.</p>

            <div class="error-box">
                <h2>⚠️ No es posible procesar el pago</h2>
                <p><strong>Razón:</strong> <?= $escape($reason) ?></p>
            </div>

            <div class="info-box">
                <h3>Posibles razones por las que una cuenta no puede pagarse:</h3>
                <ul>
                    <li>El préstamo no está en estado activo</li>
                    <li>El préstamo ya ha sido cancelado completamente</li>
                    <li>El préstamo está en proceso de reprogramación</li>
                    <li>El préstamo ha sido rechazado o anulado</li>
                    <li>No hay cuotas vigentes disponibles para pago</li>
                    <li>El préstamo está en mora extrema y requiere intervención especial</li>
                </ul>
            </div>

            <div class="info-box">
                <h3>Acciones recomendadas:</h3>
                <ul>
                    <li>Verifique el estado actual del préstamo en el perfil del cliente</li>
                    <li>Consulte con el supervisor si el préstamo requiere autorización especial</li>
                    <li>Revise si existen otros préstamos activos del cliente que sí puedan pagarse</li>
                    <li>Contacte al área de crédito para más información sobre el estado del préstamo</li>
                </ul>
            </div>

            <div class="actions">
                <?php if ($clienteCodigo !== ''): ?>
                    <button onclick="window.location.href='index.php?module=caja&profile=client&client=<?= rawurlencode($clienteCodigo) ?>'">Ver Perfil del Cliente</button>
                    <button class="secondary" onclick="window.location.href='index.php?module=caja&action=amortizar_form&client=<?= rawurlencode($clienteCodigo) ?>'">Seleccionar Otro Préstamo</button>
                <?php endif; ?>
                <button class="secondary" onclick="window.location.href='index.php?module=caja'">Volver a Caja</button>
            </div>
        </main>
    </div>
</body>
</html>