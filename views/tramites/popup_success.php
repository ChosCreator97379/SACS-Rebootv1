<?php
$escape = static fn (mixed $value): string => htmlspecialchars((string) ($value ?? ''), ENT_QUOTES, 'UTF-8');
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Trámite Registrado</title>
    <style>
        body {
            font-family: "Trebuchet MS", sans-serif;
            background: #f5f7f4;
            display: flex;
            align-items: center;
            justify-content: center;
            min-height: 100vh;
            margin: 0;
        }
        .success-container {
            background: #fff;
            border: 1px solid #dce4e1;
            border-radius: 7px;
            padding: 40px;
            text-align: center;
            max-width: 400px;
        }
        .success-icon {
            font-size: 48px;
            color: #1d7a78;
            margin-bottom: 20px;
        }
        .success-message {
            font-size: 18px;
            color: #17212b;
            margin-bottom: 20px;
        }
        .success-detail {
            font-size: 14px;
            color: #697586;
            margin-bottom: 30px;
        }
        .tramite-number {
            font-size: 18px;
            font-weight: bold;
            color: #1d7a78;
            margin-bottom: 20px;
            padding: 15px;
            background: #f8f9fa;
            border: 2px solid #1d7a78;
            border-radius: 4px;
            cursor: pointer;
            user-select: all;
        }
        .tramite-number:hover {
            background: #e8f0f0;
        }
        .copy-hint {
            font-size: 12px;
            color: #697586;
            margin-bottom: 20px;
            font-style: italic;
        }
        .btn {
            padding: 10px 24px;
            border: none;
            border-radius: 4px;
            font-size: 14px;
            font-weight: bold;
            cursor: pointer;
            background: #1d7a78;
            color: white;
        }
        .btn:hover {
            background: #166664;
        }
    </style>
</head>
<body>
    <div class="success-container">
        <div class="success-icon">✓</div>
        <div class="success-message">Trámite Registrado</div>
        <div class="tramite-number" onclick="copiarNumero()" title="Clic para copiar">
            <?= $escape($_GET['tramite_numero'] ?? '') ?>
        </div>
        <div class="copy-hint">Clic en el número para copiarlo al portapapeles</div>
        <div class="success-detail">
            El trámite se ha registrado correctamente.
        </div>
        <button type="button" onclick="window.close()" class="btn">Cerrar Ventana</button>
    </div>

    <script>
        function copiarNumero() {
            const numero = '<?= $escape($_GET['tramite_numero'] ?? '') ?>';
            navigator.clipboard.writeText(numero).then(() => {
                alert('Número copiado: ' + numero);
            }).catch(err => {
                // Fallback para navegadores antiguos
                const textarea = document.createElement('textarea');
                textarea.value = numero;
                document.body.appendChild(textarea);
                textarea.select();
                document.execCommand('copy');
                document.body.removeChild(textarea);
                alert('Número copiado: ' + numero);
            });
        }
    </script>
</body>
</html>
