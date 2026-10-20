<?php
$escape = static fn (mixed $value): string => htmlspecialchars((string) ($value ?? ''), ENT_QUOTES, 'UTF-8');
?>
<!doctype html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Listado de Trámites | SACS Nuevo</title>
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
        .filters-bar { display: grid; grid-template-columns: repeat(auto-fit, minmax(160px, 1fr)); gap: 20px; margin-bottom: 24px; padding: 24px; background: #fff; border: 1px solid var(--line); border-radius: 7px; }
        .filter-group { display: flex; flex-direction: column; gap: 6px; }
        .filter-group label { font-size: 11px; font-weight: 600; color: var(--muted); letter-spacing: .04em; text-transform: uppercase; }
        .filter-group select, .filter-group input { min-height: 36px; padding: 8px 10px; border: 1px solid #b9c7c7; border-radius: 4px; background: #fff; color: var(--ink); font: inherit; font-size: 13px; }
        .filter-group select:focus, .filter-group input:focus { outline: none; border-color: var(--teal); }
        .filter-actions { display: flex; gap: 12px; }
        .filter-actions button { min-height: 38px; padding: 8px 20px; border: none; border-radius: 4px; background: var(--teal); color: #fff; cursor: pointer; font-size: 13px; font-weight: bold; }
        .filter-actions button:hover { background: #166664; }
        .filter-actions .btn-secondary { background: #e7f0ed; color: var(--navy); }
        .filter-actions .btn-secondary:hover { background: #dce4e1; }
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
        .btn-danger { background: #dc3545; color: #fff; }
        .btn-danger:hover { background: #c82333; }
        .empty { padding: 40px; text-align: center; color: var(--muted); }
        .pagination { display: flex; align-items: center; justify-content: center; gap: 8px; margin-top: 20px; }
        .pagination-btn { padding: 8px 16px; border: 1px solid var(--line); border-radius: 4px; background: #fff; cursor: pointer; font-size: 13px; }
        .pagination-btn:hover { background: var(--paper); }
        .pagination-btn:disabled { opacity: 0.5; cursor: not-allowed; }
        .pagination-info { margin: 0 16px; font-size: 13px; color: var(--muted); }
        .badge { display: inline-block; padding: 2px 8px; border-radius: 3px; font-size: 11px; font-weight: 600; }
        .badge-registrado { background: #d4edda; color: #155724; }
        .badge-pendiente { background: #fff3cd; color: #856404; }
        .badge-derivado { background: #cce5ff; color: #004085; }
        .badge-aprobado { background: #d1ecf1; color: #0c5460; }
        .badge-finalizado { background: #d4edda; color: #155724; }
        .badge-eliminado { background: #f8d7da; color: #721c24; }
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
                    <div class="brand-note">gestion social</div>
                </div>
            </a>
            <div class="nav-label">Trámites</div>
            <nav class="nav">
                <a href="index.php" class="nav-link">Dashboard</a>
                <a href="index.php?module=tramites&action=listar" class="nav-link active">Listado de Trámites</a>
                <a href="index.php?module=tramites&action=registrar" class="nav-link">Registrar Trámite</a>
            </nav>
        </aside>
        <div class="main">
            <div class="header">
                <h1>Trámites Documentarios</h1>
                <a href="index.php?module=tramites&action=registrar" class="btn-sm btn-primary" style="padding: 8px 20px; font-size: 13px;">Nuevo Trámite</a>
            </div>
            <p class="intro">Gestión de trámites documentarios, correspondencia y expedientes del sistema.</p>

            <form method="GET" action="index.php">
                <input type="hidden" name="module" value="tramites">
                <input type="hidden" name="action" value="listar">
                
                <div class="search-bar">
                    <input type="text" name="busqueda" placeholder="Buscar por número, socio, descripción..." value="<?= $escape($_GET['busqueda'] ?? '') ?>">
                    <button type="submit">Buscar</button>
                </div>

                <div class="filters-bar">
                    <div class="filter-group">
                        <label>Tipo de Documento</label>
                        <select name="tipo">
                            <option value="">Todos</option>
                            <?php foreach ($tiposDocumentos ?? [] as $tipo): ?>
                                <option value="<?= $escape($tipo['F35_DOCUMENTO_ID']) ?>" <?= (isset($_GET['tipo']) && $_GET['tipo'] == $tipo['F35_DOCUMENTO_ID']) ? 'selected' : '' ?>>
                                    <?= $escape($tipo['F35_DOCUMENTO_DESC'] ?? '') ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="filter-group">
                        <label>Estado</label>
                        <select name="estado">
                            <option value="">Todos</option>
                            <option value="REGISTRADO" <?= (isset($_GET['estado']) && $_GET['estado'] == 'REGISTRADO') ? 'selected' : '' ?>>Registrado</option>
                            <option value="PENDIENTE" <?= (isset($_GET['estado']) && $_GET['estado'] == 'PENDIENTE') ? 'selected' : '' ?>>Pendiente</option>
                            <option value="DERIVADO" <?= (isset($_GET['estado']) && $_GET['estado'] == 'DERIVADO') ? 'selected' : '' ?>>Derivado</option>
                            <option value="APROBADO" <?= (isset($_GET['estado']) && $_GET['estado'] == 'APROBADO') ? 'selected' : '' ?>>Aprobado</option>
                            <option value="FINALIZADO" <?= (isset($_GET['estado']) && $_GET['estado'] == 'FINALIZADO') ? 'selected' : '' ?>>Finalizado</option>
                            <option value="ELIMINADO" <?= (isset($_GET['estado']) && $_GET['estado'] == 'ELIMINADO') ? 'selected' : '' ?>>Eliminado</option>
                        </select>
                    </div>
                    <div class="filter-group">
                        <label>Socio</label>
                        <input type="text" name="socio" placeholder="Código socio" value="<?= $escape($_GET['socio'] ?? '') ?>">
                    </div>
                    <div class="filter-actions">
                        <button type="submit">Filtrar</button>
                        <a href="index.php?module=tramites&action=listar" class="btn-sm btn-secondary" style="padding: 8px 20px; font-size: 13px;">Limpiar</a>
                    </div>
                </div>
            </form>

            <div class="table-container">
                <table>
                    <thead>
                        <tr>
                            <th>Número</th>
                            <th>Tipo</th>
                            <th>Descripción</th>
                            <th>Socio</th>
                            <th>Destino</th>
                            <th>Fecha</th>
                            <th>Estado</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($tramites)): ?>
                            <tr>
                                <td colspan="8" class="empty">No se encontraron trámites</td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($tramites as $tramite): ?>
                                <tr>
                                    <td><?= $escape($tramite['numero']) ?></td>
                                    <td><?= $escape($tramite['tipo']) ?></td>
                                    <td><?= $escape($tramite['descripcion']) ?></td>
                                    <td><?= $escape($tramite['socio'] ?? '-') ?></td>
                                    <td><?= $escape($tramite['destino'] ?? '-') ?></td>
                                    <td><?= $escape($tramite['fecha']) ?></td>
                                    <td>
                                        <span class="badge badge-<?= strtolower($tramite['estado']) ?>">
                                            <?= $escape($tramite['estado']) ?>
                                        </span>
                                    </td>
                                    <td>
                                        <div class="actions">
                                            <a href="index.php?module=tramites&action=ver&codigo=<?= $escape($tramite['codigo']) ?>" class="btn-sm btn-primary">Ver</a>
                                            <?php if ($tramite['estado'] !== 'ELIMINADO'): ?>
                                                <a href="index.php?module=tramites&action=editar&codigo=<?= $escape($tramite['codigo']) ?>" class="btn-sm btn-secondary">Editar</a>
                                                <a href="index.php?module=tramites&action=eliminar&codigo=<?= $escape($tramite['codigo']) ?>&csrf_token=<?= $escape($_SESSION['csrf_token'] ?? '') ?>" class="btn-sm btn-danger" onclick="return confirm('¿Está seguro de eliminar este trámite?')">Eliminar</a>
                                            <?php endif; ?>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>

            <?php if (!empty($tramites) && ($totalPaginas ?? 1) > 1): ?>
                <div class="pagination">
                    <?php
                    // Construir query string con todos los filtros
                    $queryParams = [];
                    if (isset($_GET['busqueda']) && $_GET['busqueda'] !== '') {
                        $queryParams['busqueda'] = $_GET['busqueda'];
                    }
                    if (isset($_GET['tipo']) && $_GET['tipo'] !== '') {
                        $queryParams['tipo'] = $_GET['tipo'];
                    }
                    if (isset($_GET['estado']) && $_GET['estado'] !== '') {
                        $queryParams['estado'] = $_GET['estado'];
                    }
                    if (isset($_GET['socio']) && $_GET['socio'] !== '') {
                        $queryParams['socio'] = $_GET['socio'];
                    }
                    $queryString = http_build_query($queryParams);
                    ?>
                    <?php if ($paginaActual > 1): ?>
                        <a href="index.php?module=tramites&action=listar&pagina=<?= $paginaActual - 1 ?><?= $queryString ? '&' . $queryString : '' ?>" class="pagination-btn">Anterior</a>
                    <?php else: ?>
                        <button class="pagination-btn" disabled>Anterior</button>
                    <?php endif; ?>

                    <span class="pagination-info">Página <?= $paginaActual ?> de <?= $totalPaginas ?></span>

                    <?php if ($paginaActual < $totalPaginas): ?>
                        <a href="index.php?module=tramites&action=listar&pagina=<?= $paginaActual + 1 ?><?= $queryString ? '&' . $queryString : '' ?>" class="pagination-btn">Siguiente</a>
                    <?php else: ?>
                        <button class="pagination-btn" disabled>Siguiente</button>
                    <?php endif; ?>
                </div>
            <?php endif; ?>
        </div>
    </div>
</body>
</html>
