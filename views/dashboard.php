<?php
$displayName = htmlspecialchars($user['username'], ENT_QUOTES, 'UTF-8');
$profile = htmlspecialchars((string) $user['profile'], ENT_QUOTES, 'UTF-8');
$userId = htmlspecialchars((string) $user['id'], ENT_QUOTES, 'UTF-8');
$supervisor = $user['supervisor'] === 'S';
?>
<!doctype html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Panel principal | SACS Nuevo</title>
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
        .brand { display: flex; align-items: center; gap: 12px; padding: 0 12px 34px; }
        .brand-mark { display: grid; place-items: center; width: 38px; height: 38px; border: 2px solid var(--gold); border-radius: 50%; color: var(--gold); font-family: Georgia, serif; font-size: 20px; }
        .brand-name { font-family: Georgia, serif; font-size: 22px; letter-spacing: .02em; }
        .brand-note { display: block; margin-top: 3px; color: #9bb3b7; font-size: 11px; letter-spacing: .12em; text-transform: uppercase; }
        .nav-label { padding: 0 12px 10px; color: #86a6ad; font-size: 10px; font-weight: bold; letter-spacing: .14em; text-transform: uppercase; }
        .nav { display: grid; gap: 5px; }
        .nav-link { display: flex; align-items: center; gap: 11px; padding: 11px 12px; border-left: 3px solid transparent; border-radius: 4px; color: #b8ccce; font-size: 14px; }
        .nav-link:hover, .nav-link.active { border-left-color: var(--gold); background: var(--navy-soft); color: #fff; }
        .nav-icon { width: 19px; color: #9dbfc1; font-size: 12px; text-align: center; }
        .sidebar-footer { margin-top: auto; padding: 18px 12px 0; border-top: 1px solid rgba(214, 230, 226, .14); color: #9bb3b7; font-size: 12px; line-height: 1.5; }
        .main { min-width: 0; padding: 27px clamp(22px, 4vw, 58px) 42px; }
        .topbar { display: flex; align-items: center; justify-content: space-between; gap: 20px; margin-bottom: 45px; }
        .breadcrumb { color: var(--muted); font-size: 12px; letter-spacing: .08em; text-transform: uppercase; }
        .user-menu { display: flex; align-items: center; gap: 12px; }
        .avatar { display: grid; place-items: center; width: 38px; height: 38px; border-radius: 50%; background: var(--teal); color: #fff; font-family: Georgia, serif; font-size: 17px; }
        .user-copy { line-height: 1.2; text-align: right; }
        .user-name { display: block; font-size: 14px; font-weight: bold; }
        .user-role { display: block; margin-top: 3px; color: var(--muted); font-size: 11px; }
        .logout { margin-left: 8px; color: var(--teal); font-size: 12px; font-weight: bold; }
        .eyebrow { margin: 0 0 10px; color: var(--teal); font-size: 12px; font-weight: bold; letter-spacing: .16em; text-transform: uppercase; }
        h1 { max-width: 680px; margin: 0; font-family: Georgia, serif; font-size: clamp(32px, 4vw, 52px); font-weight: normal; line-height: 1.05; }
        .intro { max-width: 590px; margin: 15px 0 34px; color: var(--muted); font-size: 15px; line-height: 1.6; }
        .overview { display: grid; grid-template-columns: repeat(3, minmax(0, 1fr)); gap: 15px; margin-bottom: 28px; }
        .stat, .module-panel, .activity-panel { border: 1px solid var(--line); background: var(--panel); box-shadow: var(--shadow); }
        .stat { min-height: 125px; padding: 20px; border-radius: 7px; }
        .stat-label { color: var(--muted); font-size: 11px; letter-spacing: .12em; text-transform: uppercase; }
        .stat-value { margin-top: 19px; font-family: Georgia, serif; font-size: 25px; }
        .stat-detail { margin-top: 5px; color: var(--teal); font-size: 12px; }
        .workspace { display: grid; grid-template-columns: minmax(0, 1.35fr) minmax(280px, .85fr); gap: 20px; }
        .module-panel, .activity-panel { padding: 25px; border-radius: 7px; }
        .section-heading { display: flex; align-items: baseline; justify-content: space-between; gap: 15px; margin-bottom: 20px; }
        h2 { margin: 0; font-family: Georgia, serif; font-size: 22px; font-weight: normal; }
        .section-note { color: var(--muted); font-size: 12px; }
        .module-grid { display: grid; grid-template-columns: repeat(2, minmax(0, 1fr)); gap: 10px; }
        .module { display: block; min-height: 93px; padding: 16px; border: 1px solid var(--line); border-radius: 5px; background: #fbfcfa; }
        .module:hover { border-color: var(--teal); transform: translateY(-1px); }
        .module-symbol { color: var(--gold); font-family: Georgia, serif; font-size: 20px; }
        .module-title { display: block; margin-top: 12px; font-size: 14px; font-weight: bold; }
        .module-status { display: block; margin-top: 4px; color: var(--muted); font-size: 11px; }
        .activity-list { display: grid; gap: 18px; margin: 0; padding: 0; list-style: none; }
        .activity-item { display: grid; grid-template-columns: 9px 1fr; gap: 12px; align-items: start; }
        .activity-dot { width: 9px; height: 9px; margin-top: 5px; border-radius: 50%; background: var(--gold); }
        .activity-title { font-size: 13px; font-weight: bold; }
        .activity-copy { margin-top: 4px; color: var(--muted); font-size: 12px; line-height: 1.45; }
        .quick-search { display: flex; align-items: end; justify-content: space-between; gap: 22px; margin-bottom: 20px; padding: 22px 25px; border: 1px solid var(--line); border-radius: 7px; background: #eaf2ef; }
        .quick-copy { max-width: 480px; }
        .quick-title { margin: 0; font-family: Georgia, serif; font-size: 20px; }
        .quick-note { margin: 6px 0 0; color: var(--muted); font-size: 12px; line-height: 1.45; }
        .quick-form { display: flex; gap: 8px; min-width: min(100%, 330px); }
        .quick-form input { min-width: 0; flex: 1; padding: 10px; border: 1px solid #b9c7c7; border-radius: 4px; font: inherit; }
        .quick-form button { padding: 10px 15px; border: 0; border-radius: 4px; background: var(--teal); color: #fff; cursor: pointer; font: inherit; font-size: 12px; font-weight: bold; }
        @media (max-width: 650px) { .quick-search { align-items: stretch; flex-direction: column; } .quick-form { min-width: 0; } }
        @media (max-width: 900px) { .shell { grid-template-columns: 1fr; } .sidebar { padding: 18px; } .brand { padding-bottom: 20px; } .nav { grid-template-columns: repeat(3, minmax(0, 1fr)); } .nav-label, .sidebar-footer { display: none; } .main { padding-top: 24px; } }
        @media (max-width: 650px) { .topbar { align-items: flex-start; flex-direction: column; margin-bottom: 34px; } .user-menu { width: 100%; justify-content: flex-start; } .user-copy { text-align: left; } .overview, .workspace { grid-template-columns: 1fr; } .nav { grid-template-columns: repeat(2, minmax(0, 1fr)); } .module-grid { grid-template-columns: 1fr; } h1 { font-size: 38px; } }
    </style>
</head>
<body>
<div class="shell">
    <aside class="sidebar">
        <a class="brand" href="index.php" aria-label="SACS Nuevo, inicio">
            <span class="brand-mark">S</span>
            <span><span class="brand-name">SACS</span><span class="brand-note">gestion social</span></span>
        </a>

        <div class="nav-label">Espacios de trabajo</div>
        <nav class="nav" aria-label="Navegacion principal">
            <a class="nav-link active" href="index.php"><span class="nav-icon">01</span>Resumen</a>
            <a class="nav-link" href="#socios"><span class="nav-icon">02</span>Socios</a>
            <a class="nav-link" href="#prestamos"><span class="nav-icon">03</span>Prestamos</a>
            <a class="nav-link" href="index.php?module=caja"><span class="nav-icon">04</span>Movimientos</a>
            <a class="nav-link" href="#reportes"><span class="nav-icon">05</span>Reportes</a>
            <a class="nav-link" href="#configuracion"><span class="nav-icon">06</span>Configuracion</a>
        </nav>

        <div class="sidebar-footer">
            Sesion activa<br>
            ID <?= $userId ?><?php if ($supervisor): ?> · Supervisor<?php endif; ?>
        </div>
    </aside>

    <main class="main">
        <header class="topbar">
            <div class="breadcrumb">Panel principal / Resumen</div>
            <div class="user-menu">
                <div class="user-copy"><span class="user-name"><?= $displayName ?></span><span class="user-role">Perfil <?= $profile ?></span></div>
                <div class="avatar" aria-hidden="true"><?= strtoupper(substr($user['username'], 0, 1)) ?></div>
                <a class="logout" href="index.php?logout=1">Salir</a>
            </div>
        </header>

        <section aria-labelledby="welcome-title">
            <p class="eyebrow">Control operativo</p>
            <h1 id="welcome-title">Bienvenido, <?= $displayName ?>.</h1>
            <p class="intro">Este es el punto de partida del nuevo SACS. Los módulos se incorporarán aquí progresivamente, conservando la información de sesión y las reglas del sistema original.</p>
        </section>

        <section class="overview" aria-label="Estado de la sesion">
            <article class="stat"><div class="stat-label">Usuario conectado</div><div class="stat-value"><?= $displayName ?></div><div class="stat-detail">Acceso activo</div></article>
            <article class="stat"><div class="stat-label">Perfil actual</div><div class="stat-value">Perfil <?= $profile ?></div><div class="stat-detail"><?= $supervisor ? 'Permisos de supervisor' : 'Permisos asignados' ?></div></article>
            <article class="stat"><div class="stat-label">Identificador</div><div class="stat-value">#<?= $userId ?></div><div class="stat-detail">Base de pruebas SACS</div></article>
        </section>

        <section class="quick-search" aria-labelledby="quick-caja-title">
            <div class="quick-copy"><h2 class="quick-title" id="quick-caja-title">Consulta rápida de Caja</h2><p class="quick-note">Busca los movimientos de un cliente sin cargar el histórico completo.</p></div>
            <form class="quick-form" method="get" action="index.php">
                <input type="hidden" name="module" value="caja">
                <input name="client" inputmode="numeric" maxlength="11" required placeholder="Código de cliente" aria-label="Código de cliente">
                <button type="submit">Consultar</button>
            </form>
        </section>

        <section class="workspace" aria-label="Modulos y actividad">
            <article class="module-panel" id="socios">
                <div class="section-heading"><h2>Modulos principales</h2><span class="section-note">Preparados para integrar</span></div>
                <div class="module-grid">
                    <a class="module" href="#socios"><span class="module-symbol">S</span><span class="module-title">Socios y familiares</span><span class="module-status">Proximo modulo</span></a>
                    <a class="module" href="#prestamos"><span class="module-symbol">P</span><span class="module-title">Prestamos</span><span class="module-status">Proximo modulo</span></a>
                    <a class="module" href="index.php?module=caja"><span class="module-symbol">M</span><span class="module-title">Movimientos</span><span class="module-status">Consulta disponible</span></a>
                    <a class="module" href="#reportes"><span class="module-symbol">R</span><span class="module-title">Reportes</span><span class="module-status">Proximo modulo</span></a>
                </div>
            </article>

            <article class="activity-panel">
                <div class="section-heading"><h2>Actividad</h2><span class="section-note">Sesion actual</span></div>
                <ul class="activity-list">
                    <li class="activity-item"><span class="activity-dot"></span><div><div class="activity-title">Inicio de sesion validado</div><div class="activity-copy">Usuario <?= $displayName ?> conectado correctamente.</div></div></li>
                    <li class="activity-item"><span class="activity-dot"></span><div><div class="activity-title">Base legacy enlazada</div><div class="activity-copy">La aplicacion consulta la copia de pruebas sin modificar datos.</div></div></li>
                    <li class="activity-item"><span class="activity-dot"></span><div><div class="activity-title">Espacio listo</div><div class="activity-copy">Los modulos se agregaran conservando esta sesion.</div></div></li>
                </ul>
            </article>
        </section>
    </main>
</div>
</body>
</html>
