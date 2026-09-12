<!doctype html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Acceso | SACS Nuevo</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 0; background: #eef2f5; color: #1f2933; }
        main { max-width: 420px; margin: 10vh auto; padding: 32px; background: #fff; border: 1px solid #d9e1e8; border-radius: 8px; }
        h1 { margin-top: 0; }
        label { display: block; margin: 16px 0 6px; font-weight: 600; }
        input { box-sizing: border-box; width: 100%; padding: 10px; border: 1px solid #aab7c4; border-radius: 4px; }
        button { margin-top: 20px; width: 100%; padding: 11px; border: 0; border-radius: 4px; background: #155e75; color: #fff; cursor: pointer; }
        .error { padding: 10px; background: #fff5f5; border-left: 4px solid #c53030; }
    </style>
</head>
<body>
<main>
    <h1>SACS Nuevo</h1>
    <p>Ingrese sus credenciales del sistema legacy.</p>
    <?php if ($error !== null): ?>
        <p class="error"><?= htmlspecialchars($error, ENT_QUOTES, 'UTF-8') ?></p>
    <?php endif; ?>
    <form method="post" action="index.php">
        <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrfToken, ENT_QUOTES, 'UTF-8') ?>">
        <label for="username">Usuario</label>
        <input id="username" name="username" maxlength="200" required autocomplete="username" value="<?= htmlspecialchars($username, ENT_QUOTES, 'UTF-8') ?>">
        <label for="password">Clave</label>
        <input id="password" name="password" type="password" maxlength="60" required autocomplete="current-password">
        <button type="submit">Ingresar</button>
    </form>
</main>
</body>
</html>
