<!doctype html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Inicio | SACS Nuevo</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 0; background: #f4f6f8; color: #1f2933; }
        main { max-width: 720px; margin: 10vh auto; padding: 32px; background: #fff; border: 1px solid #d9e1e8; border-radius: 8px; }
        h1 { margin-top: 0; }
        .data { padding: 16px; background: #f0f7fa; border-left: 4px solid #155e75; }
        a { color: #155e75; }
    </style>
</head>
<body>
<main>
    <h1>Inicio</h1>
    <p>La autenticacion funciona contra la base de pruebas del sistema legacy.</p>
    <div class="data">
        <p><strong>Usuario:</strong> <?= htmlspecialchars($user['username'], ENT_QUOTES, 'UTF-8') ?></p>
        <p><strong>Perfil:</strong> <?= htmlspecialchars((string) $user['profile'], ENT_QUOTES, 'UTF-8') ?></p>
        <p><strong>ID:</strong> <?= htmlspecialchars((string) $user['id'], ENT_QUOTES, 'UTF-8') ?></p>
    </div>
    <p><a href="index.php?logout=1">Cerrar sesion</a></p>
</main>
</body>
</html>
