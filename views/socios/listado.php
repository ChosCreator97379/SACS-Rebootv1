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
        .empty { padding: 40px; text-align: center; color: var(--muted); }
        .pagination { display: flex; align-items: center; justify-content: center; gap: 8px; margin-top: 20px; }
        .pagination-btn { padding: 8px 16px; border: 1px solid var(--line); border-radius: 4px; background: #fff; cursor: pointer; font-size: 13px; }
        .pagination-btn:hover { background: var(--paper); }
        .pagination-btn:disabled { opacity: 0.5; cursor: not-allowed; }
        .pagination-info { margin: 0 16px; font-size: 13px; color: var(--muted); }
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
                <div class="nav-label">Auditoría</div>
                <a href="index.php?module=socios&action=auditoria_general" class="nav-link">Auditoría General</a>
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

            <form method="get" class="filters-bar">
                <input type="hidden" name="module" value="socios">
                <input type="hidden" name="action" value="listado">
                <div class="filter-group">
                    <label>Departamento</label>
                    <input type="text" name="departamento" list="listaDepartamentos" placeholder="Seleccione departamento" value="<?= $escape($_GET['departamento'] ?? '') ?>">
                    <datalist id="listaDepartamentos">
                        <?php foreach ($departamentos ?? [] as $dep): ?>
                            <option value="<?= $escape($dep['codigo']) ?>">
                                <?= $escape($dep['nombre']) ?>
                            </option>
                        <?php endforeach; ?>
                    </datalist>
                </div>
                <div class="filter-group">
                    <label>Provincia</label>
                    <input type="text" name="provincia" list="listaProvincias" placeholder="Seleccione provincia" value="<?= $escape($_GET['provincia'] ?? '') ?>">
                    <datalist id="listaProvincias">
                        <?php foreach ($provincias ?? [] as $prov): ?>
                            <option value="<?= $escape($prov['codigo']) ?>">
                                <?= $escape($prov['nombre']) ?>
                            </option>
                        <?php endforeach; ?>
                    </datalist>
                </div>
                <div class="filter-group">
                    <label>Distrito</label>
                    <input type="text" name="distrito" list="listaDistritos" placeholder="Seleccione distrito" value="<?= $escape($_GET['distrito'] ?? '') ?>">
                    <datalist id="listaDistritos">
                        <?php foreach ($distritos ?? [] as $dist): ?>
                            <option value="<?= $escape($dist['codigo']) ?>">
                                <?= $escape($dist['nombre']) ?>
                            </option>
                        <?php endforeach; ?>
                    </datalist>
                </div>
                <div class="filter-group">
                    <label>Condición</label>
                    <select name="condicion">
                        <option value="">Todas</option>
                        <?php foreach ($condiciones ?? [] as $cond): ?>
                            <option value="<?= $escape($cond['F01_CONDICION']) ?>" <?= ($_GET['condicion'] ?? '') === $cond['F01_CONDICION'] ? 'selected' : '' ?>>
                                <?= $escape($cond['F01_CONDICION']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="filter-group">
                    <label>Situación</label>
                    <select name="situacion">
                        <option value="">Todas</option>
                        <?php foreach ($situaciones ?? [] as $sit): ?>
                            <option value="<?= $escape($sit['F01_SITUACION']) ?>" <?= ($_GET['situacion'] ?? '') === $sit['F01_SITUACION'] ? 'selected' : '' ?>>
                                <?= $escape($sit['F01_SITUACION']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="filter-group">
                    <label>Estado</label>
                    <select name="estado">
                        <option value="">Todos</option>
                        <option value="1" <?= ($_GET['estado'] ?? '') === '1' ? 'selected' : '' ?>>Activo</option>
                        <option value="0" <?= ($_GET['estado'] ?? '') === '0' ? 'selected' : '' ?>>Inactivo</option>
                    </select>
                </div>
                <div class="filter-group">
                    <label>Edad Min</label>
                    <input type="number" name="edad_min" min="0" max="120" value="<?= $escape($_GET['edad_min'] ?? '') ?>">
                </div>
                <div class="filter-group">
                    <label>Edad Max</label>
                    <input type="number" name="edad_max" min="0" max="120" value="<?= $escape($_GET['edad_max'] ?? '') ?>">
                </div>
                <div class="filter-group filter-actions">
                    <label>&nbsp;</label>
                    <button type="submit">Filtrar</button>
                </div>
                <div class="filter-group filter-actions">
                    <label>&nbsp;</label>
                    <button type="button" onclick="window.location.href='index.php?module=socios&action=listado'">Limpiar</button>
                </div>
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
                                            <a href="index.php?module=socios&action=info&client=<?= $escape($socio['F02_ID_CLIENTE']) ?>" class="btn-sm btn-secondary">Info</a>
                                            <a href="index.php?module=socios&action=auditoria&client=<?= $escape($socio['F02_ID_CLIENTE']) ?>" class="btn-sm btn-secondary">Auditoría</a>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                <?php endif; ?>
            </div>

            <?php if ($totalPaginas > 1): ?>
                <div class="pagination">
                    <?php 
                    // Construir URL manteniendo los filtros actuales
                    $queryParams = $_GET;
                    unset($queryParams['pagina']);
                    $baseUrl = '?' . http_build_query($queryParams);
                    ?>
                    
                    <?php if ($pagina > 1): ?>
                        <a href="<?= $escape($baseUrl) ?>&pagina=<?= $pagina - 1 ?>" class="pagination-btn">Anterior</a>
                    <?php else: ?>
                        <button class="pagination-btn" disabled>Anterior</button>
                    <?php endif; ?>
                    
                    <span class="pagination-info">
                        Página <?= $pagina ?> de <?= $totalPaginas ?> (<?= $totalSocios ?> socios)
                    </span>
                    
                    <?php if ($pagina < $totalPaginas): ?>
                        <a href="<?= $escape($baseUrl) ?>&pagina=<?= $pagina + 1 ?>" class="pagination-btn">Siguiente</a>
                    <?php else: ?>
                        <button class="pagination-btn" disabled>Siguiente</button>
                    <?php endif; ?>
                </div>
            <?php endif; ?>
        </main>
    </div>
</body>
</html>
