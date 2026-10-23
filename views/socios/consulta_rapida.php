<?php
$escape = static fn (mixed $value): string => htmlspecialchars((string) ($value ?? ''), ENT_QUOTES, 'UTF-8');
?>
<!doctype html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Consulta Rápida de Socio | SACS Nuevo</title>
    <style>
        :root {
            --ink: #1a202c;
            --muted: #6b7280;
            --paper: #ffffff;
            --panel: #f8f9fa;
            --navy: #2d3748;
            --navy-soft: #e2e8f0;
            --teal: #166664;
            --gold: #ecc94b;
            --line: #e5e7eb;
        }
        * { box-sizing: border-box; }
        body { margin: 0; background: var(--paper); color: var(--ink); font-family: "Trebuchet MS", sans-serif; }
        a { color: inherit; text-decoration: none; }
        .container { max-width: 1000px; margin: 40px auto; padding: 0 20px; }
        .header { display: flex; align-items: center; justify-content: space-between; margin-bottom: 30px; }
        .header h1 { margin: 0; font-family: Georgia, serif; font-size: 32px; font-weight: normal; }
        .back-link { color: var(--teal); font-size: 14px; font-weight: bold; }
        .panel { background: var(--panel); border: 1px solid var(--line); border-radius: 8px; padding: 40px; box-shadow: 0 4px 12px rgba(0,0,0,0.08); }
        .info-summary { display: grid; grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); gap: 25px; margin-bottom: 30px; }
        .info-item { display: flex; flex-direction: column; gap: 8px; padding: 15px; background: var(--panel); border-radius: 6px; border: 1px solid var(--line); }
        .info-label { font-size: 11px; font-weight: bold; color: var(--muted); text-transform: uppercase; letter-spacing: 0.04em; }
        .info-value { font-size: 16px; font-weight: 500; }
        .info-value.large { font-size: 22px; color: var(--teal); }
        .btn-expand { display: inline-block; padding: 10px 20px; background: var(--teal); color: white; border: none; border-radius: 4px; font-size: 14px; font-weight: bold; cursor: pointer; transition: background 0.15s; }
        .btn-expand:hover { background: #166664; }
        .btn-collapse { background: var(--navy); }
        .btn-collapse:hover { background: #1f3a4a; }
        .theme-toggle { position: fixed; top: 20px; right: 20px; z-index: 1000; padding: 10px 15px; background: var(--panel); border: 1px solid var(--line); border-radius: 8px; cursor: pointer; font-size: 14px; font-weight: bold; color: var(--ink); transition: all 0.3s; }
        .theme-toggle:hover { background: var(--navy-soft); }
        .theme-toggle.light { background: #e2e8f0; color: #1a202c; border-color: #cbd5e0; }
        .theme-toggle.dark { background: #2d3748; color: #e2e8f0; border-color: #4a5568; }
        .details-panel { margin-top: 30px; padding-top: 30px; border-top: 1px solid var(--line); display: none; }
        .details-panel.visible { display: block; }
        .details-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(280px, 1fr)); gap: 25px; margin-top: 25px; }
        .section-title { margin: 40px 0 20px; font-size: 18px; font-weight: bold; color: var(--teal); padding-bottom: 10px; border-bottom: 2px solid var(--line); }
        .empty { padding: 40px; text-align: center; color: var(--muted); }
    </style>
</head>
<body>
    <button class="theme-toggle light" id="themeToggle" onclick="toggleTheme()">☀️ Modo Claro</button>
    <div class="container">
        <div class="header">
            <h1>Consulta Rápida de Socio</h1>
            <a href="index.php" class="back-link">← Volver al Dashboard</a>
        </div>

        <?php if ($socio === null): ?>
            <div class="panel">
                <div class="empty">Socio no encontrado</div>
            </div>
        <?php else: ?>
            <div class="panel">
                <div class="info-summary">
                    <div class="info-item">
                        <span class="info-label">Código de Socio</span>
                        <span class="info-value large">#<?= $escape($socio['F02_ID_CLIENTE']) ?></span>
                    </div>
                    <div class="info-item">
                        <span class="info-label">Nombres y Apellidos</span>
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
                        <span class="info-label">DNI</span>
                        <span class="info-value"><?= $escape($socio['F01_NUM_DOC']) ?></span>
                    </div>
                    <div class="info-item">
                        <span class="info-label">Edad</span>
                        <span class="info-value"><?= \Sacs\Services\SociosServicio::calcularEdad($socio['F01_FE_NAC']) ?> años</span>
                    </div>
                    <div class="info-item">
                        <span class="info-label">Teléfono</span>
                        <span class="info-value"><?= $escape($socio['F01_TELF_CEL'] ?? $socio['F01_TELF_FIJO'] ?? '-') ?></span>
                    </div>
                    <div class="info-item">
                        <span class="info-label">Email</span>
                        <span class="info-value"><?= $escape($socio['F01_CTA_CORREO'] ?? '-') ?></span>
                    </div>
                </div>

                <button type="button" class="btn-expand" onclick="toggleDetails()">📋 Ver Información Completa</button>

                <div class="details-panel" id="detailsPanel">
                    <h3 class="section-title">📋 Datos Personales</h3>
                    <div class="details-grid">
                        <div class="info-item">
                            <span class="info-label">Tipo de Documento</span>
                            <span class="info-value"><?= $escape($socio['tipo_documento_nombre'] ?? 'Sin información') ?></span>
                        </div>
                        <div class="info-item">
                            <span class="info-label">Número de Documento</span>
                            <span class="info-value"><?= $escape($socio['F01_NUM_DOC'] ?? 'Sin información') ?></span>
                        </div>
                        <div class="info-item">
                            <span class="info-label">Sexo</span>
                            <span class="info-value"><?= $escape($socio['F01_SEXO_DESC'] ?? 'Sin información') ?></span>
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
                            <span class="info-value"><?= $escape($socio['F01_STDO_CIVIL_DESC'] ?? 'Sin información') ?></span>
                        </div>
                        <div class="info-item">
                            <span class="info-label">Estado Personal</span>
                            <span class="info-value"><?= $escape($socio['F01_STDO_PERSN_DESC'] ?? 'Sin información') ?></span>
                        </div>
                        <div class="info-item">
                            <span class="info-label">Nacionalidad</span>
                            <span class="info-value"><?= $escape($socio['nacionalidad_nombre'] ?? 'Sin información') ?></span>
                        </div>
                        <div class="info-item">
                            <span class="info-label">Instrucción</span>
                            <span class="info-value"><?= $escape($socio['instruccion_nombre'] ?? 'Sin información') ?></span>
                        </div>
                        <div class="info-item">
                            <span class="info-label">Profesión</span>
                            <span class="info-value"><?= $escape($socio['profesion_nombre'] ?? 'Sin información') ?></span>
                        </div>
                    </div>

                    <h3 class="section-title">📍 Dirección Actual</h3>
                    <div class="details-grid">
                        <div class="info-item">
                            <span class="info-label">Zona</span>
                            <span class="info-value"><?= $escape($socio['F01_ZONA'] ?? 'Sin información') ?></span>
                        </div>
                        <div class="info-item">
                            <span class="info-label">Vía</span>
                            <span class="info-value"><?= $escape($socio['F01_VIA'] ?? 'Sin información') ?></span>
                        </div>
                        <div class="info-item">
                            <span class="info-label">Número (Interior)</span>
                            <span class="info-value"><?= $escape($socio['F01_INT'] ?? 'Sin información') ?></span>
                        </div>
                        <div class="info-item">
                            <span class="info-label">Número (Exterior)</span>
                            <span class="info-value"><?= $escape($socio['F01_EXT'] ?? 'Sin información') ?></span>
                        </div>
                        <div class="info-item">
                            <span class="info-label">Piso</span>
                            <span class="info-value"><?= $escape($socio['F01_PISO'] ?? 'Sin información') ?></span>
                        </div>
                        <div class="info-item">
                            <span class="info-label">Departamento</span>
                            <span class="info-value"><?= $escape($socio['F01_DEPARTAMENTO_DESC'] ?? 'Sin información') ?></span>
                        </div>
                        <div class="info-item">
                            <span class="info-label">Provincia</span>
                            <span class="info-value"><?= $escape($socio['F01_PROVINCIA_DESC'] ?? 'Sin información') ?></span>
                        </div>
                        <div class="info-item">
                            <span class="info-label">Distrito</span>
                            <span class="info-value"><?= $escape($socio['F01_DISTRITO_DESC'] ?? 'Sin información') ?></span>
                        </div>
                    </div>

                    <h3 class="section-title">👤 Datos del Socio</h3>
                    <div class="details-grid">
                        <div class="info-item">
                            <span class="info-label">Condición</span>
                            <span class="info-value"><?= $escape($socio['F01_CONDICION'] ?? 'Sin información') ?></span>
                        </div>
                        <div class="info-item">
                            <span class="info-label">Situación</span>
                            <span class="info-value"><?= $escape($socio['F01_SITUACION'] ?? 'Sin información') ?></span>
                        </div>
                        <div class="info-item">
                            <span class="info-label">Centro</span>
                            <span class="info-value"><?= $escape($socio['F01_CENTRO'] ?? 'Sin información') ?></span>
                        </div>
                        <div class="info-item">
                            <span class="info-label">Cuenta</span>
                            <span class="info-value"><?= $escape($socio['F01_CUENTA'] ?? 'Sin información') ?></span>
                        </div>
                        <div class="info-item">
                            <span class="info-label">Aportación</span>
                            <span class="info-value"><?= $escape($socio['F01_APORTACION'] ?? 'Sin información') ?></span>
                        </div>
                        <div class="info-item">
                            <span class="info-label">Aportación COOPAM</span>
                            <span class="info-value"><?= $escape($socio['F01_APORTE_COPE'] ?? 'Sin información') ?></span>
                        </div>
                        <div class="info-item">
                            <span class="info-label">Saldo</span>
                            <span class="info-value"><?= $escape($socio['F01_SALDO'] ?? 'Sin información') ?></span>
                        </div>
                        <div class="info-item">
                            <span class="info-label">Ingreso</span>
                            <span class="info-value"><?= $escape($socio['F01_INGRESO'] ?? 'Sin información') ?></span>
                        </div>
                        <div class="info-item">
                            <span class="info-label">Estado del Cliente</span>
                            <span class="info-value"><?= $socio['cliente_estado'] == 1 ? 'Activo' : 'Inactivo' ?></span>
                        </div>
                    </div>

                    <h3 class="section-title">📍 Lugar de Nacimiento</h3>
                    <div class="details-grid">
                        <div class="info-item">
                            <span class="info-label">Departamento</span>
                            <span class="info-value"><?= $escape($socio['F01_DPTO_NAC_DESC'] ?? 'Sin información') ?></span>
                        </div>
                        <div class="info-item">
                            <span class="info-label">Provincia</span>
                            <span class="info-value"><?= $escape($socio['F01_PROV_NAC_DESC'] ?? 'Sin información') ?></span>
                        </div>
                        <div class="info-item">
                            <span class="info-label">Distrito</span>
                            <span class="info-value"><?= $escape($socio['F01_DISTRI_NAC_DESC'] ?? 'Sin información') ?></span>
                        </div>
                    </div>

                    <h3 class="section-title">📝 Observaciones</h3>
                    <div class="details-grid">
                        <div class="info-item" style="grid-column: 1 / -1;">
                            <span class="info-label">Observación Principal</span>
                            <span class="info-value"><?= $escape($socio['F01_OBSERVACION'] ?? 'Sin observaciones') ?></span>
                        </div>
                        <div class="info-item" style="grid-column: 1 / -1;">
                            <span class="info-label">Observación Adicional</span>
                            <span class="info-value"><?= $escape($socio['F01_OBS2'] ?? 'Sin observaciones') ?></span>
                        </div>
                    </div>

                    <h3 class="section-title">📄 Documentación</h3>
                    <div class="details-grid">
                        <div class="info-item">
                            <span class="info-label">Documento</span>
                            <span class="info-value"><?= $escape($socio['F01_DOCUMENTO'] ?? 'Sin información') ?></span>
                        </div>
                        <div class="info-item">
                            <span class="info-label">Título</span>
                            <span class="info-value"><?= $escape($socio['F01_TITULO'] ?? 'Sin información') ?></span>
                        </div>
                        <div class="info-item">
                            <span class="info-label">Firmó</span>
                            <span class="info-value"><?= $escape($socio['F01_FIRMO'] ?? 'Sin información') ?></span>
                        </div>
                        <div class="info-item">
                            <span class="info-label">Carta</span>
                            <span class="info-value"><?= $escape($socio['F01_CARTA'] ?? 'Sin información') ?></span>
                        </div>
                        <div class="info-item">
                            <span class="info-label">Fecha de Banco</span>
                            <span class="info-value"><?= \Sacs\Services\SociosServicio::formatearFechaParaMostrar($socio['F01_FECHA_BCO']) ?></span>
                        </div>
                        <div class="info-item">
                            <span class="info-label">Observación de Carta</span>
                            <span class="info-value"><?= $escape($socio['F01_OBS_CARTA'] ?? 'Sin información') ?></span>
                        </div>
                    </div>

                    <h3 class="section-title">📅 Fechas Importantes</h3>
                    <div class="details-grid">
                        <div class="info-item">
                            <span class="info-label">Fecha de Retiro</span>
                            <span class="info-value"><?= \Sacs\Services\SociosServicio::formatearFechaParaMostrar($socio['F01_FECHA_RETIRO']) ?></span>
                        </div>
                        <div class="info-item">
                            <span class="info-label">Fecha de Fallecimiento</span>
                            <span class="info-value"><?= \Sacs\Services\SociosServicio::formatearFechaParaMostrar($socio['F01_FECHA_FALLE']) ?></span>
                        </div>
                    </div>

                    <div style="margin-top: 40px; text-align: center;">
                        <button type="button" class="btn-expand btn-collapse" onclick="toggleDetails()">📋 Ocultar Información Completa</button>
                    </div>
                </div>

                <h3 class="section-title" style="margin-top: 60px;">💰 Préstamos del Socio</h3>
                
                <?php if (empty($prestamos)): ?>
                    <div class="panel" style="margin-top: 20px;">
                        <div class="empty">Este socio no tiene préstamos registrados</div>
                    </div>
                <?php else: ?>
                    <div class="panel" style="margin-top: 20px; padding: 20px;">
                        <div style="margin-bottom: 20px;">
                            <button type="button" class="btn-expand" onclick="togglePrestamos()">📋 Ver Todos los Préstamos</button>
                        </div>
                        
                        <div id="prestamosActivos">
                            <h4 style="margin: 0 0 15px; font-size: 16px; color: var(--teal);">Préstamos Activos</h4>
                            <div style="overflow-x: auto;">
                                <table style="width: 100%; border-collapse: collapse;">
                                    <thead>
                                        <tr style="background: var(--panel); border-bottom: 2px solid var(--line);">
                                            <th style="padding: 12px; text-align: left; font-size: 12px; font-weight: bold; color: var(--muted);">Código</th>
                                            <th style="padding: 12px; text-align: left; font-size: 12px; font-weight: bold; color: var(--muted);">Fecha</th>
                                            <th style="padding: 12px; text-align: right; font-size: 12px; font-weight: bold; color: var(--muted);">Importe</th>
                                            <th style="padding: 12px; text-align: right; font-size: 12px; font-weight: bold; color: var(--muted);">Saldo</th>
                                            <th style="padding: 12px; text-align: center; font-size: 12px; font-weight: bold; color: var(--muted);">Cuotas</th>
                                            <th style="padding: 12px; text-align: center; font-size: 12px; font-weight: bold; color: var(--muted);">Estado</th>
                                            <th style="padding: 12px; text-align: center; font-size: 12px; font-weight: bold; color: var(--muted);">Acción</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php 
                                        $prestamosActivos = array_filter($prestamos, fn($p) => $p['f16_estado'] == 4);
                                        foreach ($prestamosActivos as $prestamo): 
                                        ?>
                                        <tr style="border-bottom: 1px solid var(--line);">
                                            <td style="padding: 12px;">
                                                <a href="index.php?module=prestamos&action=detalle_completo&codigo=<?= $escape($prestamo['f16_codigo']) ?>" style="color: var(--teal); font-weight: bold; text-decoration: none;">
                                                    #<?= $escape($prestamo['f16_codigo']) ?>
                                                </a>
                                            </td>
                                            <td style="padding: 12px;"><?= \Sacs\Services\SociosServicio::formatearFechaParaMostrar($prestamo['f16_fecha']) ?></td>
                                            <td style="padding: 12px; text-align: right; font-weight: bold;">S/ <?= number_format($prestamo['f16_importe'], 2) ?></td>
                                            <td style="padding: 12px; text-align: right;"><?= number_format($prestamo['f16_saldo'], 2) ?></td>
                                            <td style="padding: 12px; text-align: center;"><?= $escape($prestamo['f16_cuotasPend']) ?> / <?= $escape($prestamo['f16_cuotas']) ?></td>
                                            <td style="padding: 12px; text-align: center;">
                                                <span style="padding: 4px 8px; border-radius: 4px; font-size: 11px; font-weight: bold; background: #d4edda; color: #155724;">ACTIVO</span>
                                            </td>
                                            <td style="padding: 12px; text-align: center;">
                                                <a href="index.php?module=caja&action=amortizacion&client=<?= $escape($clienteCodigo) ?>&loan=<?= $escape($prestamo['f16_codigo']) ?>" style="padding: 6px 12px; background: var(--teal); color: white; border-radius: 4px; font-size: 11px; font-weight: bold; text-decoration: none; display: inline-block;">Amortizar</a>
                                            </td>
                                        </tr>
                                        <?php endforeach; ?>
                                        <?php if (empty($prestamosActivos)): ?>
                                        <tr>
                                            <td colspan="7" style="padding: 20px; text-align: center; color: var(--muted);">No hay préstamos activos</td>
                                        </tr>
                                        <?php endif; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        
                        <div id="prestamosTodos" style="display: none; margin-top: 30px;">
                            <h4 style="margin: 0 0 15px; font-size: 16px; color: var(--teal);">Todos los Préstamos</h4>
                            <div style="overflow-x: auto;">
                                <table style="width: 100%; border-collapse: collapse;">
                                    <thead>
                                        <tr style="background: var(--panel); border-bottom: 2px solid var(--line);">
                                            <th style="padding: 12px; text-align: left; font-size: 12px; font-weight: bold; color: var(--muted);">Código</th>
                                            <th style="padding: 12px; text-align: left; font-size: 12px; font-weight: bold; color: var(--muted);">Fecha</th>
                                            <th style="padding: 12px; text-align: right; font-size: 12px; font-weight: bold; color: var(--muted);">Importe</th>
                                            <th style="padding: 12px; text-align: right; font-size: 12px; font-weight: bold; color: var(--muted);">Saldo</th>
                                            <th style="padding: 12px; text-align: center; font-size: 12px; font-weight: bold; color: var(--muted);">Cuotas</th>
                                            <th style="padding: 12px; text-align: center; font-size: 12px; font-weight: bold; color: var(--muted);">Estado</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($prestamos as $prestamo): 
                                            $estado = $prestamo['f16_estado'];
                                            $estados = [
                                                0 => ['texto' => 'ANULADO', 'color' => '#e2e3e5; color: #383d41;'],
                                                1 => ['texto' => 'PENDIENTE', 'color' => '#d1ecf1; color: #0c5460;'],
                                                2 => ['texto' => 'APROBADO', 'color' => '#fff3cd; color: #856404;'],
                                                3 => ['texto' => 'RECHAZADO', 'color' => '#f8d7da; color: #721c24;'],
                                                4 => ['texto' => 'ACTIVO', 'color' => '#d4edda; color: #155724;'],
                                                5 => ['texto' => 'CANCELADO', 'color' => '#f8d7da; color: #721c24;']
                                            ];
                                            $estadoInfo = $estados[$estado] ?? ['texto' => 'DESCONOCIDO', 'color' => '#e2e3e5; color: #383d41;'];
                                        ?>
                                        <tr style="border-bottom: 1px solid var(--line);">
                                            <td style="padding: 12px;">
                                                <a href="index.php?module=prestamos&action=detalle_completo&codigo=<?= $escape($prestamo['f16_codigo']) ?>" style="color: var(--teal); font-weight: bold; text-decoration: none;">
                                                    #<?= $escape($prestamo['f16_codigo']) ?>
                                                </a>
                                            </td>
                                            <td style="padding: 12px;"><?= \Sacs\Services\SociosServicio::formatearFechaParaMostrar($prestamo['f16_fecha']) ?></td>
                                            <td style="padding: 12px; text-align: right; font-weight: bold;">S/ <?= number_format($prestamo['f16_importe'], 2) ?></td>
                                            <td style="padding: 12px; text-align: right;"><?= number_format($prestamo['f16_saldo'], 2) ?></td>
                                            <td style="padding: 12px; text-align: center;"><?= $escape($prestamo['f16_cuotasPend']) ?> / <?= $escape($prestamo['f16_cuotas']) ?></td>
                                            <td style="padding: 12px; text-align: center;">
                                                <span style="padding: 4px 8px; border-radius: 4px; font-size: 11px; font-weight: bold; background: <?= $estadoInfo['color'] ?>"><?= $estadoInfo['texto'] ?></span>
                                            </td>
                                        </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                <?php endif; ?>

                <h3 class="section-title" style="margin-top: 60px;">⚠️ Sobregiro de Préstamos</h3>
                
                <?php if (empty($sobregiroPrestamos)): ?>
                    <div class="panel" style="margin-top: 20px;">
                        <div class="empty">Este socio no tiene sobregiro de préstamos</div>
                    </div>
                <?php else: ?>
                    <div class="panel" style="margin-top: 20px; padding: 20px;">
                        <div style="overflow-x: auto;">
                            <table style="width: 100%; border-collapse: collapse;">
                                <thead>
                                    <tr style="background: var(--panel); border-bottom: 2px solid var(--line);">
                                        <th style="padding: 12px; text-align: left; font-size: 12px; font-weight: bold; color: var(--muted);">Código de Préstamo</th>
                                        <th style="padding: 12px; text-align: right; font-size: 12px; font-weight: bold; color: var(--muted);">Sobregiro</th>
                                        <th style="padding: 12px; text-align: right; font-size: 12px; font-weight: bold; color: var(--muted);">Saldo del Préstamo</th>
                                        <th style="padding: 12px; text-align: center; font-size: 12px; font-weight: bold; color: var(--muted);">Estado</th>
                                        <th style="padding: 12px; text-align: center; font-size: 12px; font-weight: bold; color: var(--muted);">Acción</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($sobregiroPrestamos as $prestamo): 
                                        $estado = $prestamo['f16_estado'];
                                        $estadoTexto = match($estado) {
                                            0 => 'ANULADO',
                                            1 => 'PENDIENTE',
                                            2 => 'APROBADO',
                                            3 => 'RECHAZADO',
                                            4 => 'ACTIVO',
                                            5 => 'CANCELADO',
                                            default => 'DESCONOCIDO'
                                        };
                                        $estadoColor = match($estado) {
                                            4 => '#d4edda; color: #155724;',
                                            2 => '#fff3cd; color: #856404;',
                                            1 => '#d1ecf1; color: #0c5460;',
                                            5 => '#f8d7da; color: #721c24;',
                                            0 => '#e2e3e5; color: #383d41;',
                                            3 => '#f8d7da; color: #721c24;',
                                            default => '#e2e3e5; color: #383d41;'
                                        };
                                    ?>
                                    <tr style="border-bottom: 1px solid var(--line);">
                                        <td style="padding: 12px;">
                                            <a href="index.php?module=prestamos&action=detalle_completo&codigo=<?= $escape($prestamo['f16_codigo']) ?>" style="color: var(--teal); font-weight: bold; text-decoration: none;">
                                                #<?= $escape($prestamo['f16_codigo']) ?>
                                            </a>
                                        </td>
                                        <td style="padding: 12px; text-align: right; font-weight: bold; color: #dc2626;">S/ <?= number_format($prestamo['f16_sobre'], 2) ?></td>
                                        <td style="padding: 12px; text-align: right;">S/ <?= number_format($prestamo['f16_saldo'], 2) ?></td>
                                        <td style="padding: 12px; text-align: center;">
                                            <span style="padding: 4px 8px; border-radius: 4px; font-size: 11px; font-weight: bold; background: <?= $estadoColor ?>"><?= $estadoTexto ?></span>
                                        </td>
                                        <td style="padding: 12px; text-align: center;">
                                            <a href="index.php?module=caja&action=sobregiro_prestamo_form&client=<?= $escape($clienteCodigo) ?>" style="padding: 6px 12px; background: var(--teal); color: white; border-radius: 4px; font-size: 11px; font-weight: bold; text-decoration: none; display: inline-block;">Pagar Sobregiro</a>
                                        </td>
                                    </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                <?php endif; ?>

                <h3 class="section-title" style="margin-top: 60px;">💰 Aportes FONBES</h3>
                
                <?php if (empty($aportesFonbes)): ?>
                    <div class="panel" style="margin-top: 20px;">
                        <div class="empty">Este socio no tiene aportes FONBES registrados</div>
                    </div>
                <?php else: ?>
                    <div class="panel" style="margin-top: 20px; padding: 20px;">
                        <div style="margin-bottom: 20px;">
                            <button type="button" class="btn-expand" onclick="toggleAportesFonbes()">📋 Ver Todos los Aportes</button>
                        </div>
                        
                        <div id="aportesFonbesPendientes">
                            <h4 style="margin: 0 0 15px; font-size: 16px; color: var(--teal);">Aportes Pendientes de Pago</h4>
                            <div style="overflow-x: auto;">
                                <table style="width: 100%; border-collapse: collapse;">
                                    <thead>
                                        <tr style="background: var(--panel); border-bottom: 2px solid var(--line);">
                                            <th style="padding: 12px; text-align: left; font-size: 12px; font-weight: bold; color: var(--muted);">Periodo</th>
                                            <th style="padding: 12px; text-align: right; font-size: 12px; font-weight: bold; color: var(--muted);">Aportación</th>
                                            <th style="padding: 12px; text-align: right; font-size: 12px; font-weight: bold; color: var(--muted);">Pagado</th>
                                            <th style="padding: 12px; text-align: right; font-size: 12px; font-weight: bold; color: var(--muted);">Saldo</th>
                                            <th style="padding: 12px; text-align: center; font-size: 12px; font-weight: bold; color: var(--muted);">Estado</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php 
                                        $aportesPendientes = array_filter($aportesFonbes, function($a) {
                                            return $a['f22_aportacionP'] > ($a['f22_aportacionD'] ?? 0);
                                        });
                                        foreach ($aportesPendientes as $aporte): 
                                            $saldo = $aporte['f22_aportacionP'] - ($aporte['f22_aportacionD'] ?? 0);
                                        ?>
                                        <tr style="border-bottom: 1px solid var(--line);">
                                            <td style="padding: 12px;"><?= \Sacs\Services\SociosServicio::formatearFechaParaMostrar($aporte['f22_periodo']) ?></td>
                                            <td style="padding: 12px; text-align: right; font-weight: bold;">S/ <?= number_format($aporte['f22_aportacionP'], 2) ?></td>
                                            <td style="padding: 12px; text-align: right;">S/ <?= number_format($aporte['f22_aportacionD'] ?? 0, 2) ?></td>
                                            <td style="padding: 12px; text-align: right; font-weight: bold; color: #dc2626;">S/ <?= number_format($saldo, 2) ?></td>
                                            <td style="padding: 12px; text-align: center;">
                                                <span style="padding: 4px 8px; border-radius: 4px; font-size: 11px; font-weight: bold; background: #fff3cd; color: #856404;">PENDIENTE</span>
                                            </td>
                                        </tr>
                                        <?php endforeach; ?>
                                        <?php if (empty($aportesPendientes)): ?>
                                        <tr>
                                            <td colspan="5" style="padding: 20px; text-align: center; color: var(--muted);">No hay aportes pendientes</td>
                                        </tr>
                                        <?php endif; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        
                        <div id="aportesFonbesTodos" style="display: none; margin-top: 30px;">
                            <h4 style="margin: 0 0 15px; font-size: 16px; color: var(--teal);">Todos los Aportes</h4>
                            <div style="overflow-x: auto;">
                                <table style="width: 100%; border-collapse: collapse;">
                                    <thead>
                                        <tr style="background: var(--panel); border-bottom: 2px solid var(--line);">
                                            <th style="padding: 12px; text-align: left; font-size: 12px; font-weight: bold; color: var(--muted);">Periodo</th>
                                            <th style="padding: 12px; text-align: right; font-size: 12px; font-weight: bold; color: var(--muted);">Aportación</th>
                                            <th style="padding: 12px; text-align: right; font-size: 12px; font-weight: bold; color: var(--muted);">Pagado</th>
                                            <th style="padding: 12px; text-align: right; font-size: 12px; font-weight: bold; color: var(--muted);">Saldo</th>
                                            <th style="padding: 12px; text-align: center; font-size: 12px; font-weight: bold; color: var(--muted);">Estado</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($aportesFonbes as $aporte): 
                                            $saldo = $aporte['f22_aportacionP'] - ($aporte['f22_aportacionD'] ?? 0);
                                            $esPendiente = $saldo > 0;
                                        ?>
                                        <tr style="border-bottom: 1px solid var(--line);">
                                            <td style="padding: 12px;"><?= \Sacs\Services\SociosServicio::formatearFechaParaMostrar($aporte['f22_periodo']) ?></td>
                                            <td style="padding: 12px; text-align: right; font-weight: bold;">S/ <?= number_format($aporte['f22_aportacionP'], 2) ?></td>
                                            <td style="padding: 12px; text-align: right;">S/ <?= number_format($aporte['f22_aportacionD'] ?? 0, 2) ?></td>
                                            <td style="padding: 12px; text-align: right; font-weight: bold; <?= $esPendiente ? 'color: #dc2626;' : 'color: #155724;' ?>">S/ <?= number_format($saldo, 2) ?></td>
                                            <td style="padding: 12px; text-align: center;">
                                                <span style="padding: 4px 8px; border-radius: 4px; font-size: 11px; font-weight: bold; background: <?= $esPendiente ? '#fff3cd; color: #856404;' : '#d4edda; color: #155724;' ?>"><?= $esPendiente ? 'PENDIENTE' : 'PAGADO' ?></span>
                                            </td>
                                        </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        <div style="margin-top: 30px; text-align: center;">
                            <a href="index.php?module=caja&action=aportes_fonbes_form&client=<?= $escape($clienteCodigo) ?>" class="btn-expand" style="display: inline-block; text-decoration: none;">💳 Pagar Aportes FONBES</a>
                        </div>
                    </div>
                <?php endif; ?>

                <h3 class="section-title" style="margin-top: 60px;">💰 Aportes COOPAM</h3>
                
                <?php if (empty($aportesCoopam)): ?>
                    <div class="panel" style="margin-top: 20px;">
                        <div class="empty">Este socio no tiene aportes COOPAM registrados</div>
                    </div>
                <?php else: ?>
                    <div class="panel" style="margin-top: 20px; padding: 20px;">
                        <div style="margin-bottom: 20px;">
                            <button type="button" class="btn-expand" onclick="toggleAportesCoopam()">📋 Ver Todos los Aportes</button>
                        </div>
                        
                        <div id="aportesCoopamPendientes">
                            <h4 style="margin: 0 0 15px; font-size: 16px; color: var(--teal);">Aportes Pendientes de Pago</h4>
                            <div style="overflow-x: auto;">
                                <table style="width: 100%; border-collapse: collapse;">
                                    <thead>
                                        <tr style="background: var(--panel); border-bottom: 2px solid var(--line);">
                                            <th style="padding: 12px; text-align: left; font-size: 12px; font-weight: bold; color: var(--muted);">Periodo</th>
                                            <th style="padding: 12px; text-align: right; font-size: 12px; font-weight: bold; color: var(--muted);">Aportación</th>
                                            <th style="padding: 12px; text-align: right; font-size: 12px; font-weight: bold; color: var(--muted);">Pagado</th>
                                            <th style="padding: 12px; text-align: right; font-size: 12px; font-weight: bold; color: var(--muted);">Saldo</th>
                                            <th style="padding: 12px; text-align: center; font-size: 12px; font-weight: bold; color: var(--muted);">Estado</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php 
                                        $aportesPendientes = array_filter($aportesCoopam, function($a) {
                                            return $a['f27_aportacionP'] > ($a['f27_aportacionD'] ?? 0);
                                        });
                                        foreach ($aportesPendientes as $aporte): 
                                            $saldo = $aporte['f27_aportacionP'] - ($aporte['f27_aportacionD'] ?? 0);
                                        ?>
                                        <tr style="border-bottom: 1px solid var(--line);">
                                            <td style="padding: 12px;"><?= \Sacs\Services\SociosServicio::formatearFechaParaMostrar($aporte['f27_periodo']) ?></td>
                                            <td style="padding: 12px; text-align: right; font-weight: bold;">S/ <?= number_format($aporte['f27_aportacionP'], 2) ?></td>
                                            <td style="padding: 12px; text-align: right;">S/ <?= number_format($aporte['f27_aportacionD'] ?? 0, 2) ?></td>
                                            <td style="padding: 12px; text-align: right; font-weight: bold; color: #dc2626;">S/ <?= number_format($saldo, 2) ?></td>
                                            <td style="padding: 12px; text-align: center;">
                                                <span style="padding: 4px 8px; border-radius: 4px; font-size: 11px; font-weight: bold; background: #fff3cd; color: #856404;">PENDIENTE</span>
                                            </td>
                                        </tr>
                                        <?php endforeach; ?>
                                        <?php if (empty($aportesPendientes)): ?>
                                        <tr>
                                            <td colspan="5" style="padding: 20px; text-align: center; color: var(--muted);">No hay aportes pendientes</td>
                                        </tr>
                                        <?php endif; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        
                        <div id="aportesCoopamTodos" style="display: none; margin-top: 30px;">
                            <h4 style="margin: 0 0 15px; font-size: 16px; color: var(--teal);">Todos los Aportes</h4>
                            <div style="overflow-x: auto;">
                                <table style="width: 100%; border-collapse: collapse;">
                                    <thead>
                                        <tr style="background: var(--panel); border-bottom: 2px solid var(--line);">
                                            <th style="padding: 12px; text-align: left; font-size: 12px; font-weight: bold; color: var(--muted);">Periodo</th>
                                            <th style="padding: 12px; text-align: right; font-size: 12px; font-weight: bold; color: var(--muted);">Aportación</th>
                                            <th style="padding: 12px; text-align: right; font-size: 12px; font-weight: bold; color: var(--muted);">Pagado</th>
                                            <th style="padding: 12px; text-align: right; font-size: 12px; font-weight: bold; color: var(--muted);">Saldo</th>
                                            <th style="padding: 12px; text-align: center; font-size: 12px; font-weight: bold; color: var(--muted);">Estado</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($aportesCoopam as $aporte): 
                                            $saldo = $aporte['f27_aportacionP'] - ($aporte['f27_aportacionD'] ?? 0);
                                            $esPendiente = $saldo > 0;
                                        ?>
                                        <tr style="border-bottom: 1px solid var(--line);">
                                            <td style="padding: 12px;"><?= \Sacs\Services\SociosServicio::formatearFechaParaMostrar($aporte['f27_periodo']) ?></td>
                                            <td style="padding: 12px; text-align: right; font-weight: bold;">S/ <?= number_format($aporte['f27_aportacionP'], 2) ?></td>
                                            <td style="padding: 12px; text-align: right;">S/ <?= number_format($aporte['f27_aportacionD'] ?? 0, 2) ?></td>
                                            <td style="padding: 12px; text-align: right; font-weight: bold; <?= $esPendiente ? 'color: #dc2626;' : 'color: #155724;' ?>">S/ <?= number_format($saldo, 2) ?></td>
                                            <td style="padding: 12px; text-align: center;">
                                                <span style="padding: 4px 8px; border-radius: 4px; font-size: 11px; font-weight: bold; background: <?= $esPendiente ? '#fff3cd; color: #856404;' : '#d4edda; color: #155724;' ?>"><?= $esPendiente ? 'PENDIENTE' : 'PAGADO' ?></span>
                                            </td>
                                        </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        <div style="margin-top: 30px; text-align: center;">
                            <a href="index.php?module=caja&action=aportes_coopam_form&client=<?= $escape($clienteCodigo) ?>" class="btn-expand" style="display: inline-block; text-decoration: none;">💳 Pagar Aportes COOPAM</a>
                        </div>
                    </div>
                <?php endif; ?>

                <h3 class="section-title" style="margin-top: 60px;">🎁 Beneficios del Socio</h3>
                
                <?php if (empty($beneficios)): ?>
                    <div class="panel" style="margin-top: 20px;">
                        <div class="empty">Este socio no tiene beneficios registrados</div>
                    </div>
                <?php else: ?>
                    <div class="panel" style="margin-top: 20px; padding: 20px;">
                        <div style="overflow-x: auto;">
                            <table style="width: 100%; border-collapse: collapse;">
                                <thead>
                                    <tr style="background: var(--panel); border-bottom: 2px solid var(--line);">
                                        <th style="padding: 12px; text-align: left; font-size: 12px; font-weight: bold; color: var(--muted);">Código</th>
                                        <th style="padding: 12px; text-align: left; font-size: 12px; font-weight: bold; color: var(--muted);">Beneficiario</th>
                                        <th style="padding: 12px; text-align: left; font-size: 12px; font-weight: bold; color: var(--muted);">Tipo de Pago</th>
                                        <th style="padding: 12px; text-align: left; font-size: 12px; font-weight: bold; color: var(--muted);">Fecha Facturación</th>
                                        <th style="padding: 12px; text-align: right; font-size: 12px; font-weight: bold; color: var(--muted);">Importe</th>
                                        <th style="padding: 12px; text-align: center; font-size: 12px; font-weight: bold; color: var(--muted);">Estado</th>
                                        <th style="padding: 12px; text-align: center; font-size: 12px; font-weight: bold; color: var(--muted);">Acción</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($beneficios as $beneficio): 
                                        $estado = $beneficio['f21_estado'] ?? 'PENDIENTE';
                                        $estadoColor = match(strtoupper($estado)) {
                                            'ENTREGADO', 'DESEMBOLSADO' => '#d4edda; color: #155724;',
                                            'PENDIENTE' => '#fff3cd; color: #856404;',
                                            default => '#e2e3e5; color: #383d41;'
                                        };
                                        $fechaFacturacion = $beneficio['f21_fechaFa'] ? date('d/m/Y', strtotime($beneficio['f21_fechaFa'])) : 'Sin información';
                                        $fechaBeneficio = $beneficio['f21_fecha'] ? date('d/m/Y', strtotime($beneficio['f21_fecha'])) : 'Sin información';
                                    ?>
                                    <tr style="border-bottom: 1px solid var(--line);">
                                        <td style="padding: 12px; font-weight: bold; color: var(--teal);">#<?= $escape($beneficio['f21_codigo']) ?></td>
                                        <td style="padding: 12px;"><?= $escape($beneficio['f21_beneficiario'] ?: 'Sin información') ?></td>
                                        <td style="padding: 12px;"><?= $escape($beneficio['f21_tipoPa'] ?: 'Sin información') ?></td>
                                        <td style="padding: 12px;"><?= $escape($fechaFacturacion) ?></td>
                                        <td style="padding: 12px; text-align: right; font-weight: bold;">S/ <?= number_format($beneficio['f21_importe'] ?? 0, 2) ?></td>
                                        <td style="padding: 12px; text-align: center;">
                                            <span style="padding: 4px 8px; border-radius: 4px; font-size: 11px; font-weight: bold; background: <?= $estadoColor ?>"><?= $escape(strtoupper($estado)) ?></span>
                                        </td>
                                        <td style="padding: 12px; text-align: center;">
                                            <?php if (strtoupper($estado) === 'PENDIENTE'): ?>
                                                <a href="index.php?module=caja&action=desembolso_beneficio_form&client=<?= $escape($clienteCodigo) ?>&benefit=<?= $escape($beneficio['f21_codigo']) ?>" style="padding: 6px 12px; background: var(--teal); color: white; border-radius: 4px; font-size: 11px; font-weight: bold; text-decoration: none; display: inline-block;">Desembolsar</a>
                                            <?php else: ?>
                                                <span style="color: var(--muted); font-size: 11px;">-</span>
                                            <?php endif; ?>
                                        </td>
                                    </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                <?php endif; ?>

                <h3 class="section-title" style="margin-top: 60px;">⚠️ Multas del Socio</h3>
                
                <?php if (empty($multas)): ?>
                    <div class="panel" style="margin-top: 20px;">
                        <div class="empty">Este socio no tiene multas registradas</div>
                    </div>
                <?php else: ?>
                    <div class="panel" style="margin-top: 20px; padding: 20px;">
                        <div style="overflow-x: auto;">
                            <table style="width: 100%; border-collapse: collapse;">
                                <thead>
                                    <tr style="background: var(--panel); border-bottom: 2px solid var(--line);">
                                        <th style="padding: 12px; text-align: left; font-size: 12px; font-weight: bold; color: var(--muted);">Código</th>
                                        <th style="padding: 12px; text-align: left; font-size: 12px; font-weight: bold; color: var(--muted);">Periodo</th>
                                        <th style="padding: 12px; text-align: right; font-size: 12px; font-weight: bold; color: var(--muted);">Multa Programada</th>
                                        <th style="padding: 12px; text-align: right; font-size: 12px; font-weight: bold; color: var(--muted);">Multa Depositada</th>
                                        <th style="padding: 12px; text-align: right; font-size: 12px; font-weight: bold; color: var(--muted);">Saldo</th>
                                        <th style="padding: 12px; text-align: left; font-size: 12px; font-weight: bold; color: var(--muted);">Fecha de Pago</th>
                                        <th style="padding: 12px; text-align: center; font-size: 12px; font-weight: bold; color: var(--muted);">Estado</th>
                                        <th style="padding: 12px; text-align: center; font-size: 12px; font-weight: bold; color: var(--muted);">Acción</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($multas as $multa): 
                                        $multaProgramada = $multa['f63_multap'] ?? 0;
                                        $multaDepositada = $multa['f63_multad'] ?? 0;
                                        $saldo = $multaProgramada - $multaDepositada;
                                        $estado = $saldo > 0 ? 'PENDIENTE' : 'PAGADO';
                                        $estadoColor = $saldo > 0 ? '#fff3cd; color: #856404;' : '#d4edda; color: #155724;';
                                        $periodo = $multa['f63_periodo'] ? date('m/Y', strtotime($multa['f63_periodo'])) : 'Sin información';
                                        $fechaPago = $multa['f63_fechaPago'] ? date('d/m/Y H:i', strtotime($multa['f63_fechaPago'])) : 'Sin información';
                                    ?>
                                    <tr style="border-bottom: 1px solid var(--line);">
                                        <td style="padding: 12px; font-weight: bold; color: var(--teal);">#<?= $escape($multa['f63_codigo']) ?></td>
                                        <td style="padding: 12px;"><?= $escape($periodo) ?></td>
                                        <td style="padding: 12px; text-align: right;">S/ <?= number_format($multaProgramada, 2) ?></td>
                                        <td style="padding: 12px; text-align: right;">S/ <?= number_format($multaDepositada, 2) ?></td>
                                        <td style="padding: 12px; text-align: right; font-weight: bold; color: <?= $saldo > 0 ? '#dc2626' : 'var(--ink)' ?>;">S/ <?= number_format($saldo, 2) ?></td>
                                        <td style="padding: 12px;"><?= $escape($fechaPago) ?></td>
                                        <td style="padding: 12px; text-align: center;">
                                            <span style="padding: 4px 8px; border-radius: 4px; font-size: 11px; font-weight: bold; background: <?= $estadoColor ?>"><?= $estado ?></span>
                                        </td>
                                        <td style="padding: 12px; text-align: center;">
                                            <?php if ($saldo > 0): ?>
                                                <a href="index.php?module=caja&action=multas_form&client=<?= $escape($clienteCodigo) ?>&multa=<?= $escape($multa['f63_codigo']) ?>" style="padding: 6px 12px; background: var(--teal); color: white; border-radius: 4px; font-size: 11px; font-weight: bold; text-decoration: none; display: inline-block;">Pagar</a>
                                            <?php else: ?>
                                                <span style="color: var(--muted); font-size: 11px;">-</span>
                                            <?php endif; ?>
                                        </td>
                                    </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                <?php endif; ?>
            </div>
        <?php endif; ?>
    </div>

    <script>
        let isDarkMode = false;

        function toggleTheme() {
            isDarkMode = !isDarkMode;
            const root = document.documentElement;
            const btn = document.getElementById('themeToggle');
            
            if (isDarkMode) {
                // Modo oscuro
                root.style.setProperty('--ink', '#e2e8f0');
                root.style.setProperty('--muted', '#94a3b8');
                root.style.setProperty('--paper', '#1a202c');
                root.style.setProperty('--panel', '#2d3748');
                root.style.setProperty('--navy', '#1a202c');
                root.style.setProperty('--navy-soft', '#2d3748');
                root.style.setProperty('--teal', '#38b2ac');
                root.style.setProperty('--gold', '#ecc94b');
                root.style.setProperty('--line', '#4a5568');
                btn.textContent = '🌙 Modo Oscuro';
                btn.classList.remove('light');
                btn.classList.add('dark');
            } else {
                // Modo claro
                root.style.setProperty('--ink', '#17212b');
                root.style.setProperty('--muted', '#697586');
                root.style.setProperty('--paper', '#f5f7f4');
                root.style.setProperty('--panel', '#ffffff');
                root.style.setProperty('--navy', '#142b3a');
                root.style.setProperty('--navy-soft', '#20465a');
                root.style.setProperty('--teal', '#1d7a78');
                root.style.setProperty('--gold', '#d69e47');
                root.style.setProperty('--line', '#dce4e1');
                btn.textContent = '☀️ Modo Claro';
                btn.classList.remove('dark');
                btn.classList.add('light');
            }
        }

        function toggleDetails() {
            const panel = document.getElementById('detailsPanel');
            const btns = document.querySelectorAll('.btn-expand');
            
            if (panel.classList.contains('visible')) {
                panel.classList.remove('visible');
                btns.forEach(btn => {
                    btn.textContent = '📋 Ver Información Completa';
                    btn.classList.remove('btn-collapse');
                });
            } else {
                panel.classList.add('visible');
                btns.forEach(btn => {
                    btn.textContent = '📋 Ocultar Información Completa';
                    btn.classList.add('btn-collapse');
                });
            }
        }

        function togglePrestamos() {
            const activos = document.getElementById('prestamosActivos');
            const todos = document.getElementById('prestamosTodos');
            const btn = event.target;
            
            if (todos.style.display === 'none') {
                todos.style.display = 'block';
                activos.style.display = 'none';
                btn.textContent = '📋 Ver Solo Préstamos Activos';
            } else {
                todos.style.display = 'none';
                activos.style.display = 'block';
                btn.textContent = '📋 Ver Todos los Préstamos';
            }
        }

        function toggleAportesFonbes() {
            const pendientes = document.getElementById('aportesFonbesPendientes');
            const todos = document.getElementById('aportesFonbesTodos');
            const btn = event.target;
            
            if (todos.style.display === 'none') {
                todos.style.display = 'block';
                pendientes.style.display = 'none';
                btn.textContent = '📋 Ver Solo Pendientes';
            } else {
                todos.style.display = 'none';
                pendientes.style.display = 'block';
                btn.textContent = '📋 Ver Todos los Aportes';
            }
        }

        function toggleAportesCoopam() {
            const pendientes = document.getElementById('aportesCoopamPendientes');
            const todos = document.getElementById('aportesCoopamTodos');
            const btn = event.target;
            
            if (todos.style.display === 'none') {
                todos.style.display = 'block';
                pendientes.style.display = 'none';
                btn.textContent = '📋 Ver Solo Pendientes';
            } else {
                todos.style.display = 'none';
                pendientes.style.display = 'block';
                btn.textContent = '📋 Ver Todos los Aportes';
            }
        }
    </script>
</body>
</html>
