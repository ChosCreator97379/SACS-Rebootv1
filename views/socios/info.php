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
        .btn { padding: 8px 16px; border: none; border-radius: 4px; font-size: 13px; font-weight: 600; cursor: pointer; transition: background .15s; }
        .btn-primary { background: var(--teal); color: white; }
        .btn-primary:hover { background: #166664; }
        .btn-secondary { background: var(--paper); color: var(--ink); }
        .btn-secondary:hover { background: #e8ebe9; }
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
                    <div class="brand-note">Sistema de Administración</div>
                </div>
            </a>
            <div class="nav-label">Socios</div>
            <nav class="nav">
                <a href="index.php" class="nav-link">Dashboard</a>
                <a href="index.php?module=socios&action=listado" class="nav-link">Listado de Socios</a>
                <a href="index.php?module=socios&action=registro" class="nav-link">Registro de Socio</a>
                <div class="nav-label">Auditoría</div>
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
                        <span class="info-value"><?= $escape($socio['F01_FE_NAC']) ?></span>
                    </div>
                    <div class="info-item">
                        <span class="info-label">Edad</span>
                        <span class="info-value"><?= $escape($socio['F01_EDAD']) ?> años</span>
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
                        <span class="info-label">Observaciones</span>
                        <span class="info-value"><?= $escape($socio['F01_OBSERVACION']) ?></span>
                    </div>
                </div>
            </section>

            <div style="display: flex; gap: 12px; margin-top: 24px;">
                <a href="index.php?module=socios&action=editar&client=<?= $escape($socio['F02_ID_CLIENTE']) ?>" class="btn btn-primary">Editar Socio</a>
                <a href="index.php?module=socios&action=auditoria&client=<?= $escape($socio['F02_ID_CLIENTE']) ?>" class="btn btn-secondary">Ver Historial de Cambios</a>
                <a href="index.php?module=caja&profile=client&client=<?= $escape($socio['F02_ID_CLIENTE']) ?>" class="btn btn-secondary">Ver Operaciones</a>
                <a href="index.php?module=socios&action=listado" class="btn btn-secondary">Volver al Listado</a>
            </div>
        </main>
    </div>
</body>
</html>
