<?php

declare(strict_types=1);

$escape = static fn (mixed $value): string => htmlspecialchars((string) ($value ?? ''), ENT_QUOTES, 'UTF-8');
?>
<!doctype html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Información Personal | SACS Nuevo</title>
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
        .panel { background: var(--panel); border-radius: 12px; padding: 24px; box-shadow: var(--shadow); margin-bottom: 24px; }
        .panel h2 { margin: 0 0 20px; font-size: 18px; font-weight: 400; color: var(--teal); }
        .panel h3 { margin: 0 0 16px; font-size: 14px; font-weight: 600; color: var(--muted); text-transform: uppercase; letter-spacing: .08em; }
        .info-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 20px; }
        .info-item { display: flex; flex-direction: column; gap: 4px; }
        .info-label { font-size: 11px; font-weight: 600; color: var(--muted); text-transform: uppercase; letter-spacing: .04em; }
        .info-value { font-size: 14px; }
        .btn { padding: 8px 16px; border: none; border-radius: 4px; font-size: 13px; font-weight: 600; cursor: pointer; transition: background .15s; text-decoration: none; display: inline-block; }
        .btn-primary { background: var(--teal); color: white; }
        .btn-primary:hover { background: #166664; }
        .btn-danger { background: #dc2626; color: white; }
        .btn-danger:hover { background: #b91c1c; }
        .btn-secondary { background: var(--paper); color: var(--ink); }
        .btn-secondary:hover { background: #e8ebe9; }
        .btn-blue { background: #3b82f6; color: white; }
        .btn-blue:hover { background: #2563eb; }
        .btn-green { background: #10b981; color: white; }
        .btn-green:hover { background: #059669; }
        .btn-purple { background: #8b5cf6; color: white; }
        .btn-purple:hover { background: #7c3aed; }
        .btn-orange { background: #f59e0b; color: white; }
        .btn-orange:hover { background: #d97706; }
        .btn-gray { background: #6b7280; color: white; }
        .btn-gray:hover { background: #4b5563; }
        .btn-sm { padding: 4px 10px; font-size: 11px; }
        .status-badge { display: inline-block; padding: 4px 8px; border-radius: 4px; font-size: 11px; font-weight: 600; }
        .status-active { background: #d4edda; color: #155724; }
        .status-inactive { background: #f8d7da; color: #721c24; }
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
            <div class="nav-label">Socios</div>
            <nav class="nav">
                <a href="index.php" class="nav-link">Dashboard</a>
                <a href="index.php?module=socios&action=listado" class="nav-link">Listado de Socios</a>
                <a href="index.php?module=socios&action=registro" class="nav-link">Registro de Socio</a>
                <a href="index.php?module=socios&action=info&client=<?= rawurlencode($clienteCodigo) ?>" class="nav-link active">Información Personal</a>
                <a href="index.php?module=socios&action=observaciones&client=<?= rawurlencode($clienteCodigo) ?>" class="nav-link">Observaciones</a>
                <a href="index.php?module=socios&action=carta&client=<?= rawurlencode($clienteCodigo) ?>" class="nav-link">Carta</a>
                <a href="index.php?module=socios&action=sobregiro&client=<?= rawurlencode($clienteCodigo) ?>" class="nav-link">Sobregiro</a>
                <a href="index.php?module=socios&action=aportes_completos&client=<?= rawurlencode($clienteCodigo) ?>" class="nav-link">Aportes Completos</a>
                <a href="index.php?module=socios&action=descuentos&client=<?= rawurlencode($clienteCodigo) ?>" class="nav-link">Descuentos</a>
                <a href="index.php?module=socios&action=aportes_fonbes&client=<?= rawurlencode($clienteCodigo) ?>" class="nav-link">Aportes FONBES</a>
                <a href="index.php?module=socios&action=aportes_coopam&client=<?= rawurlencode($clienteCodigo) ?>" class="nav-link">Aportes COOPAM</a>
                <a href="index.php?module=socios&action=gestion_estado&client=<?= rawurlencode($clienteCodigo) ?>" class="nav-link">Gestión de Estado</a>
                <a href="index.php?module=socios&action=beneficios&client=<?= rawurlencode($clienteCodigo) ?>" class="nav-link">Beneficios</a>
                <a href="index.php?module=tramites&action=listar&socio=<?= rawurlencode($clienteCodigo) ?>" class="nav-link">Trámites</a>
                <a href="index.php?module=socios&action=familiares&client=<?= rawurlencode($clienteCodigo) ?>" class="nav-link">Familiares</a>
                <a href="index.php?module=socios&action=rifas&client=<?= rawurlencode($clienteCodigo) ?>" class="nav-link">Rifas</a>
                <a href="index.php?module=socios&action=multas&client=<?= rawurlencode($clienteCodigo) ?>" class="nav-link">Multas</a>
                <a href="index.php?module=socios&action=cheques" class="nav-link">Cheques</a>
                <div class="nav-label">Auditoría</div>
                <a href="index.php?module=socios&action=auditoria&client=<?= rawurlencode($clienteCodigo) ?>" class="nav-link">Auditoría Individual</a>
                <a href="index.php?module=socios&action=auditoria_general" class="nav-link">Auditoría General</a>
            </nav>
        </aside>

        <main class="main">
            <div class="header">
                <h1>Información Personal</h1>
                <a href="index.php?module=socios&action=listado" class="btn btn-secondary">Volver al Listado</a>
            </div>

            <section class="panel">
                <h2>Información General</h2>
                <div class="info-grid">
                    <div class="info-item">
                        <span class="info-label">Código de Cliente</span>
                        <span class="info-value"><?= $escape($socio['F02_ID_CLIENTE']) ?></span>
                    </div>
                    <div class="info-item">
                        <span class="info-label">Estado</span>
                        <span class="info-value">
                            <span class="status-badge <?= $socio['cliente_estado'] == 1 ? 'status-active' : 'status-inactive' ?>">
                                <?= $socio['cliente_estado'] == 1 ? 'Activo' : 'Inactivo' ?>
                            </span>
                        </span>
                    </div>
                    <div class="info-item">
                        <span class="info-label">Nombre Completo</span>
                        <span class="info-value">
                            <?= $escape($socio['F01_APE_PAT']) ?> 
                            <?= $escape($socio['F01_APE_MAT']) ?>, 
                            <?= $escape($socio['F01_NOMB_PRI']) ?>
                            <?php if ($socio['F01_NOMB_SEG']): ?>
                                <?= $escape($socio['F01_NOMB_SEG']) ?>
                            <?php endif; ?>
                        </span>
                    </div>
                    <div class="info-item">
                        <span class="info-label">Tipo de Documento</span>
                        <span class="info-value"><?= $escape($socio['F01_TPO_DOC']) ?></span>
                    </div>
                    <div class="info-item">
                        <span class="info-label">Número de Documento</span>
                        <span class="info-value"><?= $escape($socio['F01_NUM_DOC']) ?></span>
                    </div>
                    <div class="info-item">
                        <span class="info-label">Sexo</span>
                        <span class="info-value"><?= $escape($socio['F01_SEXO']) ?></span>
                    </div>
                    <div class="info-item">
                        <span class="info-label">Fecha de Nacimiento</span>
                        <span class="info-value"><?= \Sacs\Services\SociosServicio::formatearFechaParaMostrar($socio['F01_FE_NAC']) ?></span>
                    </div>
                    <div class="info-item">
                        <span class="info-label">Edad</span>
                        <span class="info-value"><?= \Sacs\Services\SociosServicio::calcularEdad($socio['F01_FE_NAC']) ?> años</span>
                    </div>
                    <div class="info-item">
                        <span class="info-label">Estado Civil</span>
                        <span class="info-value"><?= $escape($socio['F01_STDO_CIVIL']) ?></span>
                    </div>
                    <div class="info-item">
                        <span class="info-label">Estado Personal</span>
                        <span class="info-value"><?= $escape($socio['F01_STDO_PERSN']) ?></span>
                    </div>
                </div>
            </section>

            <section class="panel">
                <h2>Información de Contacto</h2>
                <div class="info-grid">
                    <div class="info-item">
                        <span class="info-label">Correo Electrónico</span>
                        <span class="info-value"><?= $escape($socio['F01_CTA_CORREO']) ?></span>
                    </div>
                    <div class="info-item">
                        <span class="info-label">Teléfono Fijo</span>
                        <span class="info-value"><?= $escape($socio['F01_TELF_FIJO']) ?></span>
                    </div>
                    <div class="info-item">
                        <span class="info-label">Teléfono Celular</span>
                        <span class="info-value"><?= $escape($socio['F01_TELF_CEL']) ?></span>
                    </div>
                </div>
            </section>

            <section class="panel">
                <h2>Dirección</h2>
                <div class="info-grid">
                    <div class="info-item">
                        <span class="info-label">Zona</span>
                        <span class="info-value"><?= $escape($socio['F01_ZONA']) ?></span>
                    </div>
                    <div class="info-item">
                        <span class="info-label">Vía</span>
                        <span class="info-value"><?= $escape($socio['F01_VIA']) ?></span>
                    </div>
                    <div class="info-item">
                        <span class="info-label">Interior</span>
                        <span class="info-value"><?= $escape($socio['F01_INT']) ?></span>
                    </div>
                    <div class="info-item">
                        <span class="info-label">Exterior</span>
                        <span class="info-value"><?= $escape($socio['F01_EXT']) ?></span>
                    </div>
                    <div class="info-item">
                        <span class="info-label">Piso</span>
                        <span class="info-value"><?= $escape($socio['F01_PISO']) ?></span>
                    </div>
                    <div class="info-item">
                        <span class="info-label">Departamento</span>
                        <span class="info-value"><?= $escape($socio['F01_DEPARTAMENTO']) ?></span>
                    </div>
                    <div class="info-item">
                        <span class="info-label">Provincia</span>
                        <span class="info-value"><?= $escape($socio['F01_PROVINCIA']) ?></span>
                    </div>
                    <div class="info-item">
                        <span class="info-label">Distrito</span>
                        <span class="info-value"><?= $escape($socio['F01_DISTRITO']) ?></span>
                    </div>
                </div>
            </section>

            <section class="panel">
                <h2>Información Académica y Laboral</h2>
                <div class="info-grid">
                    <div class="info-item">
                        <span class="info-label">Nacionalidad</span>
                        <span class="info-value"><?= $escape($socio['nacionalidad_nombre']) ?></span>
                    </div>
                    <div class="info-item">
                        <span class="info-label">Instrucción</span>
                        <span class="info-value"><?= $escape($socio['instruccion_nombre']) ?></span>
                    </div>
                    <div class="info-item">
                        <span class="info-label">Profesión</span>
                        <span class="info-value"><?= $escape($socio['profesion_nombre']) ?></span>
                    </div>
                    <div class="info-item">
                        <span class="info-label">Ingreso</span>
                        <span class="info-value"><?= $escape($socio['F01_INGRESO']) ?></span>
                    </div>
                </div>
            </section>

            <section class="panel">
                <h2>Información de Lugar de Nacimiento</h2>
                <div class="info-grid">
                    <div class="info-item">
                        <span class="info-label">Departamento de Nacimiento</span>
                        <span class="info-value"><?= $escape($socio['F01_DPTO_NAC']) ?></span>
                    </div>
                    <div class="info-item">
                        <span class="info-label">Provincia de Nacimiento</span>
                        <span class="info-value"><?= $escape($socio['F01_PROV_NAC']) ?></span>
                    </div>
                    <div class="info-item">
                        <span class="info-label">Distrito de Nacimiento</span>
                        <span class="info-value"><?= $escape($socio['F01_DISTRI_NAC']) ?></span>
                    </div>
                </div>
            </section>

            <section class="panel">
                <h2>Información del Socio</h2>
                <div class="info-grid">
                    <div class="info-item">
                        <span class="info-label">Condición</span>
                        <span class="info-value"><?= $escape($socio['F01_CONDICION']) ?></span>
                    </div>
                    <div class="info-item">
                        <span class="info-label">Situación</span>
                        <span class="info-value"><?= $escape($socio['F01_SITUACION']) ?></span>
                    </div>
                    <div class="info-item">
                        <span class="info-label">Centro</span>
                        <span class="info-value"><?= $escape($socio['F01_CENTRO']) ?></span>
                    </div>
                    <div class="info-item">
                        <span class="info-label">Cuenta</span>
                        <span class="info-value"><?= $escape($socio['F01_CUENTA']) ?></span>
                    </div>
                    <div class="info-item">
                        <span class="info-label">Aportación</span>
                        <span class="info-value"><?= $escape($socio['F01_APORTACION']) ?></span>
                    </div>
                    <div class="info-item">
                        <span class="info-label">Saldo</span>
                        <span class="info-value"><?= $escape($socio['F01_SALDO']) ?></span>
                    </div>
                    <div class="info-item">
                        <span class="info-label">Fecha de Retiro</span>
                        <span class="info-value"><?= $escape($socio['F01_FECHA_RETIRO']) ?></span>
                    </div>
                    <div class="info-item">
                        <span class="info-label">Fecha de Fallecimiento</span>
                        <span class="info-value"><?= $escape($socio['F01_FECHA_FALLE']) ?></span>
                    </div>
                </div>
            </section>

            <section class="panel">
                <h2>Observaciones</h2>
                <div class="info-grid">
                    <div class="info-item" style="grid-column: 1 / -1;">
                        <span class="info-label">Observación Actual</span>
                        <span class="info-value"><?= $escape($socio['F01_OBS2'] ?? 'Sin observaciones') ?></span>
                    </div>
                </div>
                <div style="margin-top: 16px; display: flex; gap: 12px;">
                    <a href="index.php?module=socios&action=observaciones&client=<?= $escape($socio['F02_ID_CLIENTE']) ?>" class="btn btn-primary">📝 Gestionar Observaciones</a>
                    <a href="index.php?module=socios&action=descuentos&client=<?= $escape($socio['F02_ID_CLIENTE']) ?>" class="btn btn-secondary">💳 Descuentos</a>
                    <a href="index.php?module=socios&action=beneficios&client=<?= $escape($socio['F02_ID_CLIENTE']) ?>" class="btn btn-secondary">🎁 Beneficios</a>
                    <a href="index.php?module=socios&action=familiares&client=<?= $escape($socio['F02_ID_CLIENTE']) ?>" class="btn btn-secondary">👨‍👩‍👧‍👦 Familiares</a>
                    <a href="index.php?module=socios&action=rifas&client=<?= $escape($socio['F02_ID_CLIENTE']) ?>" class="btn btn-secondary">🎟️ Rifas</a>
                    <a href="index.php?module=socios&action=multas&client=<?= $escape($socio['F02_ID_CLIENTE']) ?>" class="btn btn-secondary">⚠️ Multas</a>
                </div>
            </section>

            <section class="panel">
                <h2>Carta</h2>
                <div class="info-grid">
                    <div class="info-item">
                        <span class="info-label">Firmó</span>
                        <span class="info-value"><?= isset($socio['F01_FIRMO']) && $socio['F01_FIRMO'] == '1' ? 'Sí' : 'No' ?></span>
                    </div>
                    <div class="info-item">
                        <span class="info-label">Carta</span>
                        <span class="info-value"><?= $escape($socio['F01_CARTA'] ?? 'Sin carta') ?></span>
                    </div>
                    <div class="info-item">
                        <span class="info-label">Fecha del Banco</span>
                        <span class="info-value"><?= $escape($socio['F01_FECHA_BCO'] ?? 'Sin fecha') ?></span>
                    </div>
                    <div class="info-item" style="grid-column: 1 / -1;">
                        <span class="info-label">Observación de Carta</span>
                        <span class="info-value"><?= $escape($socio['F01_OBS_CARTA'] ?? 'Sin observación') ?></span>
                    </div>
                </div>
                <div style="margin-top: 16px;">
                    <a href="index.php?module=socios&action=carta&client=<?= $escape($socio['F02_ID_CLIENTE']) ?>" class="btn btn-primary">📝 Gestionar Carta</a>
                    <a href="index.php?module=socios&action=sobregiro&client=<?= $escape($socio['F02_ID_CLIENTE']) ?>" class="btn btn-primary" style="margin-left: 12px;">💰 Ver Sobregiro</a>
                </div>
            </section>

            <div style="display: flex; gap: 12px; margin-top: 24px; flex-wrap: wrap;">
                <a href="index.php?module=socios&action=editar&client=<?= $escape($socio['F02_ID_CLIENTE']) ?>" class="btn btn-blue">✏️ Editar Socio</a>
                <a href="index.php?module=socios&action=gestion_estado&client=<?= $escape($socio['F02_ID_CLIENTE']) ?>" class="btn btn-danger">⚙️ Gestionar Estado</a>
                <a href="index.php?module=socios&action=beneficios&client=<?= $escape($socio['F02_ID_CLIENTE']) ?>" class="btn btn-purple">💎 Beneficios</a>
                <a href="index.php?module=socios&action=familiares&client=<?= $escape($socio['F02_ID_CLIENTE']) ?>" class="btn btn-blue">👨‍👩‍👧 Familiares</a>
                <a href="index.php?module=socios&action=rifas&client=<?= $escape($socio['F02_ID_CLIENTE']) ?>" class="btn btn-orange">🎫 Rifas</a>
                <a href="index.php?module=socios&action=multas&client=<?= $escape($socio['F02_ID_CLIENTE']) ?>" class="btn btn-danger">⚠️ Multas</a>
                <a href="index.php?module=socios&action=cheques" class="btn btn-blue">📄 Cheques</a>
                <a href="index.php?module=socios&action=aportes_fonbes&client=<?= $escape($socio['F02_ID_CLIENTE']) ?>" class="btn btn-green">💰 Aportes FONBES</a>
                <a href="index.php?module=socios&action=aportes_coopam&client=<?= $escape($socio['F02_ID_CLIENTE']) ?>" class="btn btn-green">💰 Aportes COOPAM</a>
                <a href="index.php?module=socios&action=auditoria&client=<?= $escape($socio['F02_ID_CLIENTE']) ?>" class="btn btn-purple">📋 Ver Historial de Cambios</a>
                <a href="index.php?module=caja&profile=client&client=<?= $escape($socio['F02_ID_CLIENTE']) ?>" class="btn btn-orange">💼 Ver Operaciones</a>
                <a href="index.php?module=socios&action=listado" class="btn btn-gray">🔙 Volver al Listado</a>
            </div>
        </main>
    </div>
</body>
</html>
