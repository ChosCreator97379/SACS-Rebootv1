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
            --paper: #e8e8e8;
            --panel: #ffffff;
            --navy: #142b3a;
            --navy-soft: #20465a;
            --teal: #1d7a78;
            --gold: #d69e47;
            --line: #dce4e1;
            --shadow: 0 18px 45px rgba(20, 43, 58, .08);
            --card-socios: #e3f2fd;
            --card-socios-dark: #1976d2;
            --card-prestamos: #f3e5f5;
            --card-prestamos-dark: #7b1fa2;
            --card-caja: #e8f5e9;
            --card-caja-dark: #388e3c;
            --card-usuarios: #fff3e0;
            --card-usuarios-dark: #f57c00;
            --card-config: #fce4ec;
            --card-config-dark: #c2185b;
            --card-tramites: #e0f2f1;
            --card-tramites-dark: #009688;
            --card-reportes: #f5f5f5;
            --card-reportes-dark: #616161;
        }

        * { box-sizing: border-box; }
        body { margin: 0; background: var(--paper); color: var(--ink); font-family: "Trebuchet MS", sans-serif; }
        a { color: inherit; text-decoration: none; }
        .container { max-width: 1400px; margin: 0 auto; padding: 40px 20px; }
        .header { display: flex; align-items: center; justify-content: space-between; margin-bottom: 40px; }
        .header-info { flex: 1; }
        .eyebrow { margin: 0 0 10px; color: var(--teal); font-size: 12px; font-weight: bold; letter-spacing: .16em; text-transform: uppercase; }
        h1 { margin: 0; font-family: Georgia, serif; font-size: clamp(32px, 4vw, 48px); font-weight: normal; line-height: 1.1; }
        .user-info { display: flex; align-items: center; gap: 16px; }
        .avatar { display: grid; place-items: center; width: 48px; height: 48px; border-radius: 50%; background: var(--teal); color: #fff; font-family: Georgia, serif; font-size: 20px; }
        .user-details { text-align: right; }
        .user-name { display: block; font-size: 14px; font-weight: bold; }
        .user-role { display: block; margin-top: 4px; color: var(--muted); font-size: 12px; }
        .logout { display: inline-block; margin-top: 4px; color: var(--teal); font-size: 12px; font-weight: bold; }

        .quick-search-section { margin-bottom: 50px; }
        .quick-search-title { margin: 0 0 20px; font-family: Georgia, serif; font-size: 24px; font-weight: normal; color: var(--navy); }
        .quick-search-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 20px; }
        .quick-search-card { display: flex; align-items: center; gap: 20px; padding: 25px; background: var(--panel); border: 2px solid var(--line); border-radius: 12px; box-shadow: var(--shadow); transition: all 0.3s; }
        .quick-search-card:hover { border-color: var(--teal); transform: translateY(-2px); }
        .quick-search-icon { width: 50px; height: 50px; display: grid; place-items: center; border-radius: 10px; background: var(--teal); color: #fff; font-size: 24px; }
        .quick-search-content { flex: 1; }
        .quick-search-title-card { margin: 0 0 8px; font-size: 16px; font-weight: bold; }
        .quick-search-desc { margin: 0; color: var(--muted); font-size: 13px; }
        .quick-form { display: flex; gap: 10px; margin-top: 15px; }
        .quick-form input { flex: 1; padding: 10px 15px; border: 1px solid var(--line); border-radius: 6px; font: inherit; }
        .quick-form input:focus { outline: none; border-color: var(--teal); }
        .quick-form button { padding: 10px 20px; border: 0; border-radius: 6px; background: var(--teal); color: #fff; cursor: pointer; font: inherit; font-size: 13px; font-weight: bold; transition: background 0.2s; }
        .quick-form button:hover { background: #166664; }

        .modules-section { margin-bottom: 50px; }
        .modules-title { margin: 0 0 30px; font-family: Georgia, serif; font-size: 28px; font-weight: normal; color: var(--navy); }
        .modules-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(280px, 1fr)); gap: 25px; }
        .module-card { display: block; padding: 35px 30px; background: var(--panel); border: 2px solid var(--line); border-radius: 16px; box-shadow: var(--shadow); transition: all 0.3s; position: relative; overflow: hidden; }
        .module-card:hover { transform: translateY(-4px); box-shadow: 0 24px 55px rgba(20, 43, 58, .12); }
        .module-card::before { content: ''; position: absolute; top: 0; left: 0; right: 0; height: 6px; background: var(--card-color); }
        .module-icon { width: 60px; height: 60px; display: grid; place-items: center; border-radius: 12px; background: var(--card-bg); color: var(--card-color); font-size: 28px; margin-bottom: 20px; }
        .module-title { display: block; margin: 0 0 10px; font-size: 20px; font-weight: bold; color: var(--navy); }
        .module-desc { display: block; margin: 0 0 20px; color: var(--muted); font-size: 14px; line-height: 1.5; }
        .module-status { display: inline-block; padding: 6px 12px; border-radius: 20px; font-size: 11px; font-weight: bold; background: var(--card-bg); color: var(--card-color); }
        .module-status.available { background: #e8f5e9; color: #388e3c; }
        .module-status.soon { background: #fff3e0; color: #f57c00; }

        .card-socios { --card-color: var(--card-socios-dark); --card-bg: var(--card-socios); }
        .card-prestamos { --card-color: var(--card-prestamos-dark); --card-bg: var(--card-prestamos); }
        .card-caja { --card-color: var(--card-caja-dark); --card-bg: var(--card-caja); }
        .card-usuarios { --card-color: var(--card-usuarios-dark); --card-bg: var(--card-usuarios); }
        .card-config { --card-color: var(--card-config-dark); --card-bg: var(--card-config); }
        .card-tramites { --card-color: var(--card-tramites-dark); --card-bg: var(--card-tramites); }
        .card-reportes { --card-color: var(--card-reportes-dark); --card-bg: var(--card-reportes); }

        @media (max-width: 768px) {
            .header { flex-direction: column; align-items: flex-start; gap: 20px; }
            .user-info { width: 100%; justify-content: space-between; }
            .user-details { text-align: left; }
            .quick-search-grid { grid-template-columns: 1fr; }
            .modules-grid { grid-template-columns: 1fr; }
        }
    </style>
</head>
<body>
<div class="container">
    <header class="header">
        <div class="header-info">
            <p class="eyebrow">Panel Principal</p>
            <h1>Bienvenido, <?= $displayName ?></h1>
        </div>
        <div class="user-info">
            <div class="user-details">
                <span class="user-name"><?= $displayName ?></span>
                <span class="user-role">Perfil <?= $profile ?></span>
                <a class="logout" href="index.php?logout=1">Cerrar sesión</a>
            </div>
            <div class="avatar"><?= strtoupper(substr($user['username'], 0, 1)) ?></div>
        </div>
    </header>

    <section class="quick-search-section">
        <h2 class="quick-search-title">Consultas Rápidas</h2>
        <div class="quick-search-grid">
            <div class="quick-search-card">
                <div class="quick-search-icon">👤</div>
                <div class="quick-search-content">
                    <h3 class="quick-search-title-card">Consulta de Socio</h3>
                    <p class="quick-search-desc">Busca información completa de un socio</p>
                    <form class="quick-form" method="get" action="index.php">
                        <input type="hidden" name="module" value="socios">
                        <input type="hidden" name="action" value="consulta_rapida">
                        <input name="client" inputmode="numeric" maxlength="11" required placeholder="Código de socio">
                        <button type="submit">Consultar</button>
                    </form>
                </div>
            </div>

            <div class="quick-search-card">
                <div class="quick-search-icon">💰</div>
                <div class="quick-search-content">
                    <h3 class="quick-search-title-card">Consulta de Caja</h3>
                    <p class="quick-search-desc">Busca movimientos de un cliente</p>
                    <form class="quick-form" method="get" action="index.php">
                        <input type="hidden" name="module" value="caja">
                        <input name="client" inputmode="numeric" maxlength="11" required placeholder="Código de cliente">
                        <button type="submit">Consultar</button>
                    </form>
                </div>
            </div>

            <div class="quick-search-card">
                <div class="quick-search-icon">📋</div>
                <div class="quick-search-content">
                    <h3 class="quick-search-title-card">Consulta de Préstamo</h3>
                    <p class="quick-search-desc">Busca el detalle de un préstamo</p>
                    <form class="quick-form" method="get" action="index.php">
                        <input type="hidden" name="module" value="prestamos">
                        <input type="hidden" name="action" value="detalle_completo">
                        <input name="codigo" inputmode="numeric" maxlength="11" required placeholder="Código de préstamo">
                        <button type="submit">Consultar</button>
                    </form>
                </div>
            </div>
        </div>
    </section>

    <section class="modules-section">
        <h2 class="modules-title">Módulos del Sistema</h2>
        <div class="modules-grid">
            <a class="module-card card-socios" href="index.php?module=socios&action=listado">
                <div class="module-icon">👥</div>
                <span class="module-title">Socios</span>
                <span class="module-desc">Gestión de socios, familiares, beneficios y más</span>
                <span class="module-status available">Disponible</span>
            </a>

            <a class="module-card card-prestamos" href="index.php?module=prestamos&action=listado">
                <div class="module-icon">💳</div>
                <span class="module-title">Préstamos</span>
                <span class="module-desc">Gestión de préstamos, amortizaciones y desembolsos</span>
                <span class="module-status available">Disponible</span>
            </a>

            <a class="module-card card-caja" href="index.php?module=caja">
                <div class="module-icon">💰</div>
                <span class="module-title">Movimientos de Caja</span>
                <span class="module-desc">Registro y consulta de operaciones financieras</span>
                <span class="module-status available">Disponible</span>
            </a>

            <a class="module-card card-usuarios" href="index.php?module=usuarios&action=listado">
                <div class="module-icon">🔐</div>
                <span class="module-title">Usuarios</span>
                <span class="module-desc">Gestión de usuarios y perfiles del sistema</span>
                <span class="module-status available">Disponible</span>
            </a>

            <a class="module-card card-caja" href="index.php?module=caja&action=diario_contable">
                <div class="module-icon">📊</div>
                <span class="module-title">Diario Contable</span>
                <span class="module-desc">Consultas de asientos contables y movimientos</span>
                <span class="module-status available">Disponible</span>
            </a>

            <a class="module-card card-config" href="index.php?module=productos&action=listado">
                <div class="module-icon">⚙️</div>
                <span class="module-title">Configuración</span>
                <span class="module-desc">Configuración de productos, tarifas y parámetros</span>
                <span class="module-status available">Disponible</span>
            </a>

            <a class="module-card card-tramites" href="index.php?module=tramites&action=listar">
                <div class="module-icon">📝</div>
                <span class="module-title">Trámites</span>
                <span class="module-desc">Gestión de trámites documentarios</span>
                <span class="module-status available">Disponible</span>
            </a>

            <a class="module-card card-reportes" href="#reportes">
                <div class="module-icon">📈</div>
                <span class="module-title">Reportes</span>
                <span class="module-desc">Reportes y estadísticas del sistema</span>
                <span class="module-status soon">Próximamente</span>
            </a>
        </div>
    </section>
</div>
</body>
</html>
