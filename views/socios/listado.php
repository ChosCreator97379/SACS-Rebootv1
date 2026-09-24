<?php
$escape = static fn (mixed $value): string => htmlspecialchars((string) ($value ?? ''), ENT_QUOTES, 'UTF-8');
?>
<!doctype html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Listado de Socios | SACS Nuevo</title>
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
        .search-bar { display: flex; gap: 12px; margin-bottom: 24px; }
        .search-bar input { flex: 1; min-height: 38px; padding: 8px 10px; border: 1px solid #b9c7c7; border-radius: 4px; background: #fff; color: var(--ink); font: inherit; }
        .search-bar input:focus { outline: none; border-color: var(--teal); }
        .search-bar button { min-height: 38px; padding: 8px 20px; border: none; border-radius: 4px; background: var(--teal); color: #fff; cursor: pointer; font-size: 13px; font-weight: bold; }
        .search-bar button:hover { background: #166664; }
        .table-container { border: 1px solid var(--line); border-radius: 7px; background: var(--panel); overflow: hidden; }
        table { width: 100%; border-collapse: collapse; }
        th { text-align: left; padding: 12px 16px; background: #f8f9fa; color: var(--muted); font-size: 11px; font-weight: bold; letter-spacing: .04em; text-transform: uppercase; border-bottom: 1px solid var(--line); }
        td { padding: 12px 16px; border-bottom: 1px solid var(--line); font-size: 13px; }
        tr:last-child td { border-bottom: none; }
        tr:hover { background: #f8f9fa; }
        .actions { display: flex; gap: 8px; }
        .btn-sm { padding: 4px 10px; font-size: 11px; border: none; border-radius: 3px; cursor: pointer; }
        .btn-primary { background: var(--teal); color: #fff; }
        .btn-primary:hover { background: #166664; }
        .btn-secondary { background: #e7f0ed; color: var(--navy); }
        .btn-secondary:hover { background: #dce4e1; }
        .empty { padding: 40px; text-align: center; color: var(--muted); }
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
            <div class="nav-label">Socios</div>
            <nav class="nav">
                <a href="index.php" class="nav-link">Dashboard</a>
                <a href="index.php?module=socios&action=listado" class="nav-link active">Listado de Socios</a>
                <a href="index.php?module=socios&action=registro" class="nav-link">Registro de Socio</a>
            </nav>
        </aside>

        <main class="main">
            <div class="header">
                <h1>Listado de Socios</h1>
                <a href="index.php?module=socios&action=registro" class="btn-sm btn-primary" style="padding: 8px 16px; font-size: 13px;">Nuevo Socio</a>
            </div>
            <p class="intro">Busque y gestione los socios del sistema.</p>

            <form method="get" class="search-bar">
                <input type="hidden" name="module" value="socios">
                <input type="hidden" name="action" value="listado">
                <input type="text" name="criterio" placeholder="Buscar por nombre, apellido o código..." value="<?= $escape($_GET['criterio'] ?? '') ?>">
                <button type="submit">Buscar</button>
            </form>

            <div class="table-container">
                <?php if (empty($socios)): ?>
                    <div class="empty">
                        No se encontraron socios.
                    </div>
                <?php else: ?>
                    <table>
                        <thead>
                            <tr>
                                <th>Código</th>
                                <th>Nombre Completo</th>
                                <th>Documento</th>
                                <th>Teléfono</th>
                                <th>Correo</th>
                                <th>Condición</th>
                                <th>Situación</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($socios as $socio): ?>
                                <tr>
                                    <td><?= $escape($socio['F02_ID_CLIENTE']) ?></td>
                                    <td>
                                        <?= $escape($socio['F01_APE_PAT']) ?> 
                                        <?= $escape($socio['F01_APE_MAT']) ?>, 
                                        <?= $escape($socio['F01_NOMB_PRI']) ?>
                                        <?php if ($socio['F01_NOMB_SEG']): ?>
                                            <?= $escape($socio['F01_NOMB_SEG']) ?>
                                        <?php endif; ?>
                                    </td>
                                    <td><?= $escape($socio['F01_NUM_DOC']) ?></td>
                                    <td><?= $escape($socio['F01_TELF_CEL'] ?: $socio['F01_TELF_FIJO']) ?></td>
                                    <td><?= $escape($socio['F01_CTA_CORREO']) ?></td>
                                    <td><?= $escape($socio['F01_CONDICION']) ?></td>
                                    <td><?= $escape($socio['F01_SITUACION'] ?: 'Activo') ?></td>
                                    <td>
                                        <div class="actions">
                                            <a href="index.php?module=caja&profile=client&client=<?= $escape($socio['F02_ID_CLIENTE']) ?>" class="btn-sm btn-primary">Ver</a>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                <?php endif; ?>
            </div>
        </main>
    </div>
</body>
</html>
