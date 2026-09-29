<?php

declare(strict_types=1);

$escape = static fn (mixed $value): string => htmlspecialchars((string) ($value ?? ''), ENT_QUOTES, 'UTF-8');
?>
<!doctype html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Editar Socio | SACS Nuevo</title>
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
        .form-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 20px; }
        .form-group { display: flex; flex-direction: column; gap: 6px; }
        .form-group label { font-size: 11px; font-weight: 600; color: var(--muted); letter-spacing: .04em; text-transform: uppercase; }
        .form-group input, .form-group select, .form-group textarea { min-height: 36px; padding: 8px 10px; border: 1px solid #b9c7c7; border-radius: 4px; background: #fff; color: var(--ink); font: inherit; font-size: 13px; }
        .form-group input:focus, .form-group select:focus, .form-group textarea:focus { outline: none; border-color: var(--teal); }
        .form-group textarea { min-height: 80px; resize: vertical; }
        .form-actions { display: flex; gap: 12px; margin-top: 24px; }
        .btn { padding: 8px 16px; border: none; border-radius: 4px; font-size: 13px; font-weight: 600; cursor: pointer; transition: background .15s; }
        .btn-primary { background: var(--teal); color: white; }
        .btn-primary:hover { background: #166664; }
        .btn-secondary { background: var(--paper); color: var(--ink); }
        .btn-secondary:hover { background: #e8ebe9; }
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
                <h1>Editar Socio</h1>
                <a href="index.php?module=socios&action=info&client=<?= $escape($socio['F02_ID_CLIENTE']) ?>" class="btn btn-secondary">Cancelar</a>
            </div>

            <form method="POST" action="index.php?module=socios&action=actualizar" class="panel">
                <input type="hidden" name="csrf_token" value="<?= $escape($_SESSION['csrf_token'] ?? '') ?>">
                <input type="hidden" name="id_persona" value="<?= $escape($socio['F01_ID_PERSONA']) ?>">
                <input type="hidden" name="cliente_codigo" value="<?= $escape($socio['F02_ID_CLIENTE']) ?>">

                <h2>Información Personal</h2>
                <div class="form-grid">
                    <div class="form-group">
                        <label>Apellido Paterno</label>
                        <input type="text" name="ape_pat" value="<?= $escape($socio['F01_APE_PAT']) ?>" required>
                    </div>
                    <div class="form-group">
                        <label>Apellido Materno</label>
                        <input type="text" name="ape_mat" value="<?= $escape($socio['F01_APE_MAT']) ?>">
                    </div>
                    <div class="form-group">
                        <label>Nombre Principal</label>
                        <input type="text" name="nomb_pri" value="<?= $escape($socio['F01_NOMB_PRI']) ?>" required>
                    </div>
                    <div class="form-group">
                        <label>Nombre Secundario</label>
                        <input type="text" name="nomb_seg" value="<?= $escape($socio['F01_NOMB_SEG']) ?>">
                    </div>
                    <div class="form-group">
                        <label>Tipo Documento</label>
                        <input type="number" name="tpo_doc" value="<?= $escape($socio['F01_TPO_DOC']) ?>">
                    </div>
                    <div class="form-group">
                        <label>Número Documento</label>
                        <input type="text" name="num_doc" value="<?= $escape($socio['F01_NUM_DOC']) ?>">
                    </div>
                    <div class="form-group">
                        <label>Sexo</label>
                        <select name="sexo">
                            <option value="">Seleccionar</option>
                            <option value="1" <?= $socio['F01_SEXO'] == 1 ? 'selected' : '' ?>>Masculino</option>
                            <option value="2" <?= $socio['F01_SEXO'] == 2 ? 'selected' : '' ?>>Femenino</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Fecha Nacimiento</label>
                        <input type="date" name="fe_nac" value="<?= $escape($socio['F01_FE_NAC']) ?>">
                    </div>
                    <div class="form-group">
                        <label>Estado Civil</label>
                        <input type="number" name="stdo_civil" value="<?= $escape($socio['F01_STDO_CIVIL']) ?>">
                    </div>
                    <div class="form-group">
                        <label>Estado Personal</label>
                        <input type="number" name="stdo_persn" value="<?= $escape($socio['F01_STDO_PERSN']) ?>">
                    </div>
                </div>

                <h2>Contacto</h2>
                <div class="form-grid">
                    <div class="form-group">
                        <label>Correo Electrónico</label>
                        <input type="email" name="cta_correo" value="<?= $escape($socio['F01_CTA_CORREO']) ?>">
                    </div>
                    <div class="form-group">
                        <label>Teléfono Fijo</label>
                        <input type="text" name="telf_fijo" value="<?= $escape($socio['F01_TELF_FIJO']) ?>">
                    </div>
                    <div class="form-group">
                        <label>Teléfono Celular</label>
                        <input type="text" name="telf_cel" value="<?= $escape($socio['F01_TELF_CEL']) ?>">
                    </div>
                </div>

                <h2>Ubicación</h2>
                <div class="form-grid">
                    <div class="form-group">
                        <label>Departamento</label>
                        <input type="text" name="departamento" value="<?= $escape($socio['F01_DEPARTAMENTO']) ?>">
                    </div>
                    <div class="form-group">
                        <label>Provincia</label>
                        <input type="text" name="provincia" value="<?= $escape($socio['F01_PROVINCIA']) ?>">
                    </div>
                    <div class="form-group">
                        <label>Distrito</label>
                        <input type="text" name="distrito" value="<?= $escape($socio['F01_DISTRITO']) ?>">
                    </div>
                    <div class="form-group">
                        <label>Zona</label>
                        <input type="text" name="zona" value="<?= $escape($socio['F01_ZONA']) ?>">
                    </div>
                    <div class="form-group">
                        <label>Vía</label>
                        <input type="text" name="via" value="<?= $escape($socio['F01_VIA']) ?>">
                    </div>
                    <div class="form-group">
                        <label>Interior</label>
                        <input type="text" name="int" value="<?= $escape($socio['F01_INT']) ?>">
                    </div>
                    <div class="form-group">
                        <label>Exterior</label>
                        <input type="text" name="ext" value="<?= $escape($socio['F01_EXT']) ?>">
                    </div>
                    <div class="form-group">
                        <label>Piso</label>
                        <input type="text" name="piso" value="<?= $escape($socio['F01_PISO']) ?>">
                    </div>
                </div>

                <h2>Información del Socio</h2>
                <div class="form-grid">
                    <div class="form-group">
                        <label>Condición</label>
                        <input type="text" name="condicion" value="<?= $escape($socio['F01_CONDICION']) ?>">
                    </div>
                    <div class="form-group">
                        <label>Situación</label>
                        <input type="text" name="situacion" value="<?= $escape($socio['F01_SITUACION']) ?>">
                    </div>
                    <div class="form-group">
                        <label>Centro</label>
                        <input type="number" name="centro" value="<?= $escape($socio['F01_CENTRO']) ?>">
                    </div>
                    <div class="form-group">
                        <label>Cuenta</label>
                        <input type="text" name="cuenta" value="<?= $escape($socio['F01_CUENTA']) ?>">
                    </div>
                    <div class="form-group">
                        <label>Ingreso</label>
                        <input type="number" step="0.01" name="ingreso" value="<?= $escape($socio['F01_INGRESO']) ?>">
                    </div>
                    <div class="form-group">
                        <label>Aportación</label>
                        <input type="number" step="0.01" name="aportacion" value="<?= $escape($socio['F01_APORTACION']) ?>">
                    </div>
                    <div class="form-group">
                        <label>Saldo</label>
                        <input type="number" step="0.01" name="saldo" value="<?= $escape($socio['F01_SALDO']) ?>">
                    </div>
                    <div class="form-group">
                        <label>Edad</label>
                        <input type="number" name="edad" value="<?= $escape($socio['F01_EDAD']) ?>">
                    </div>
                </div>

                <h2>Observaciones</h2>
                <div class="form-grid">
                    <div class="form-group" style="grid-column: 1 / -1;">
                        <label>Observaciones</label>
                        <textarea name="observacion"><?= $escape($socio['F01_OBSERVACION']) ?></textarea>
                    </div>
                </div>

                <div class="form-actions">
                    <button type="submit" class="btn btn-primary">Guardar Cambios</button>
                    <a href="index.php?module=socios&action=info&client=<?= $escape($socio['F02_ID_CLIENTE']) ?>" class="btn btn-secondary">Cancelar</a>
                </div>
            </form>
        </main>
    </div>
</body>
</html>
