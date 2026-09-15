<?php
$message = $message ?? 'Bienvenido al sistema SACS Nuevo';
$databaseStatus = $databaseStatus ?? 'No verificada';
$databaseError = $databaseError ?? null;
?>
<!doctype html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>SACS Nuevo</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 0; background: #f4f6f8; color: #1f2933; }
        main { max-width: 720px; margin: 10vh auto; padding: 32px; background: #fff; border: 1px solid #d9e1e8; border-radius: 8px; }
        h1 { margin-top: 0; }
        .status { padding: 12px; background: #eef6ee; border-left: 4px solid #2f855a; }
        .error { padding: 12px; background: #fff5f5; border-left: 4px solid #c53030; word-break: break-word; }
        a { color: #155e75; }
    </style>
</head>
<body>
<main>
    <h1>SACS Nuevo</h1>
    <p><?= htmlspecialchars($message, ENT_QUOTES, 'UTF-8') ?></p>
    <p class="status">Estado de base de datos: <?= htmlspecialchars($databaseStatus, ENT_QUOTES, 'UTF-8') ?></p>

    <?php if ($databaseError !== null): ?>
        <p class="error"><?= htmlspecialchars($databaseError, ENT_QUOTES, 'UTF-8') ?></p>
    <?php endif; ?>

    <p><a href="?check=database">Probar conexion con la base de datos</a></p>
</main>
</body>
</html>
