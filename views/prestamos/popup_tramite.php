<?php
$escape = static fn (mixed $value): string => htmlspecialchars((string) ($value ?? ''), ENT_QUOTES, 'UTF-8');

$codigo = trim((string) ($_GET['codigo'] ?? ''));
$cliente = trim((string) ($_GET['cliente'] ?? ''));
$success = $_GET['success'] ?? '';
?>
<!doctype html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Préstamo Registrado | SACS Nuevo</title>
    <style>
        :root {
            --ink: #17212b;
            --muted: #697586;
            --paper: #f5f7f4;
            --panel: #ffffff;
            --navy: #142b3a;
            --teal: #1d7a78;
            --green: #0f766e;
            --red: #991b1b;
            --border: #e2e8f0;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif;
            background: var(--paper);
            color: var(--ink);
            line-height: 1.5;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            padding: 20px;
        }

        .card {
            background: var(--panel);
            border-radius: 12px;
            border: 1px solid var(--border);
            padding: 40px;
            max-width: 500px;
            width: 100%;
            text-align: center;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }

        .icon {
            width: 80px;
            height: 80px;
            background: var(--green);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 24px;
            font-size: 40px;
            color: white;
        }

        h1 {
            font-size: 24px;
            font-weight: 600;
            margin-bottom: 12px;
            color: var(--ink);
        }

        .message {
            color: var(--muted);
            margin-bottom: 32px;
        }

        .success-text {
            color: var(--green);
            font-weight: 500;
        }

        .actions {
            display: flex;
            gap: 12px;
            justify-content: center;
        }

        .btn {
            padding: 12px 24px;
            border: none;
            border-radius: 8px;
            font-size: 14px;
            font-weight: 500;
            cursor: pointer;
            text-decoration: none;
            display: inline-block;
        }

        .btn-primary {
            background: var(--teal);
            color: white;
        }

        .btn-primary:hover {
            background: #166160;
        }

        .btn-secondary {
            background: var(--muted);
            color: white;
        }

        .btn-secondary:hover {
            background: #56606d;
        }
    </style>
</head>
<body>
    <div class="card">
        <div class="icon">✓</div>
        <h1>¡Préstamo Registrado!</h1>
        <p class="message">
            <span class="success-text"><?= $escape($success) ?></span><br>
            Código: <?= $escape($codigo) ?><br>
            Socio: <?= $escape($cliente) ?>
        </p>
        <div class="actions">
            <a href="index.php?module=prestamos&action=listado" class="btn btn-secondary">Volver al Listado</a>
        </div>
    </div>

    <script>
        // Abrir ventana emergente de trámite automáticamente
        window.onload = function() {
            setTimeout(function() {
                const url = 'index.php?module=tramites&action=registrar&socio_codigo=<?= $escape($cliente) ?>&popup=1';
                window.open(url, 'tramite', 'width=800,height=600');
            }, 1000);
        };
    </script>
</body>
</html>
