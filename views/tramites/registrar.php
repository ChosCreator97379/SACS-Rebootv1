<?php
$escape = static fn (mixed $value): string => htmlspecialchars((string) ($value ?? ''), ENT_QUOTES, 'UTF-8');

$tramitesServicio = new \Sacs\Services\TramitesServicio();
$sociosServicio = new \Sacs\Services\SociosServicio();

$usuario = $_SESSION['sacs_user']['username'] ?? 'SISTEMA';
$displayName = substr($usuario, 0, 2);
$profile = 'Administrador';

// Obtener tipos de documentos (interno = 1 o 2 según legacy)
$tiposDocumentos = $tramitesServicio->obtenerTiposDocumentos();

// Obtener usuarios para el campo destino
$usuarios = $tramitesServicio->obtenerUsuarios();

// Obtener listado de socios para autocompletado
$socios = $sociosServicio->listarSocios([], 1, 1000);

// Datos del socio seleccionado (si existe)
$socioSeleccionado = $socioSeleccionado ?? null;
?>
<!doctype html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Registrar Trámite | SACS Nuevo</title>
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
        .form-container { background: #fff; border: 1px solid var(--line); border-radius: 7px; padding: 32px; }
        .form-section { margin-bottom: 32px; }
        .form-section h3 { margin: 0 0 20px; font-size: 16px; font-weight: 600; color: var(--navy); }
        .form-row { display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 20px; margin-bottom: 20px; }
        .form-group { display: flex; flex-direction: column; gap: 6px; }
        .form-group label { font-size: 12px; font-weight: 600; color: var(--muted); letter-spacing: .04em; text-transform: uppercase; }
        .form-group input, .form-group select, .form-group textarea { min-height: 38px; padding: 8px 10px; border: 1px solid #b9c7c7; border-radius: 4px; background: #fff; color: var(--ink); font: inherit; font-size: 13px; }
        .form-group input:focus, .form-group select:focus, .form-group textarea:focus { outline: none; border-color: var(--teal); }
        .form-group textarea { min-height: 80px; resize: vertical; }
        .form-actions { display: flex; gap: 12px; margin-top: 32px; }
        .form-actions button { min-height: 38px; padding: 8px 24px; border: none; border-radius: 4px; background: var(--teal); color: #fff; cursor: pointer; font-size: 13px; font-weight: bold; }
        .form-actions button:hover { background: #166664; }
        .form-actions .btn-secondary { background: #e7f0ed; color: var(--navy); }
        .form-actions .btn-secondary:hover { background: #dce4e1; }
        .socio-info { background: #f8f9fa; border: 1px solid var(--line); border-radius: 4px; padding: 16px; margin-bottom: 20px; }
        .socio-info h4 { margin: 0 0 12px; font-size: 14px; font-weight: 600; }
        .socio-info p { margin: 4px 0; font-size: 13px; color: var(--muted); }
        .socio-info strong { color: var(--ink); }
        .file-upload { display: flex; flex-direction: column; gap: 8px; }
        .file-upload input[type="file"] { font-size: 12px; }
        .error { background: #f8d7da; border: 1px solid #f5c6cb; border-radius: 4px; padding: 12px 16px; margin-bottom: 20px; color: #721c24; font-size: 13px; }
        .autocomplete-wrapper { position: relative; }
        .autocomplete-suggestions { position: absolute; top: 100%; left: 0; right: 0; background: #fff; border: 1px solid var(--line); border-radius: 4px; max-height: 200px; overflow-y: auto; z-index: 1000; display: none; }
        .autocomplete-suggestions div { padding: 8px 12px; cursor: pointer; font-size: 13px; }
        .autocomplete-suggestions div:hover { background: #f8f9fa; }
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
                <a href="index.php?module=tramites&action=listar" class="nav-link">Listado de Trámites</a>
                <a href="index.php?module=tramites&action=registrar" class="nav-link active">Registrar Trámite</a>
            </nav>
        </aside>
        <div class="main">
            <div class="header">
                <h1>Registrar Trámite</h1>
                <a href="index.php?module=tramites&action=listar" class="btn-sm btn-secondary" style="padding: 8px 20px; font-size: 13px;">Volver al Listado</a>
            </div>
            <p class="intro">Complete el formulario para registrar un nuevo trámite documentario.</p>

            <?php if (isset($error)): ?>
                <div class="error"><?= $escape($error) ?></div>
            <?php endif; ?>

            <?php if (isset($_GET['error'])): ?>
                <div class="error"><?= $escape($_GET['error']) ?></div>
            <?php endif; ?>

            <form method="POST" action="index.php" enctype="multipart/form-data">
                <input type="hidden" name="module" value="tramites">
                <input type="hidden" name="action" value="registrar">
                <input type="hidden" name="csrf_token" value="<?= $escape($_SESSION['csrf_token'] ?? '') ?>">
                <input type="hidden" name="popup" value="<?= $escape($_GET['popup'] ?? '') ?>">

                <div class="form-container">
                    <!-- Búsqueda de Socio -->
                    <div class="form-section">
                        <h3>Datos del Socio</h3>
                        <div class="form-row">
                            <div class="form-group" style="flex: 0 0 200px;">
                                <label>Código de Socio</label>
                                <input type="text" name="socio_codigo" id="socio_codigo" placeholder="Código" value="<?= $escape($_GET['socio_codigo'] ?? '') ?>">
                            </div>
                            <div class="form-group">
                                <label>Acción</label>
                                <button type="button" onclick="buscarSocio()" class="btn-sm btn-primary" style="padding: 8px 16px;">Buscar</button>
                            </div>
                        </div>

                        <?php if (isset($_GET['popup']) && $_GET['popup'] === '1'): ?>
                            <div class="form-row" style="margin-top: 16px;">
                                <div class="form-group" style="grid-column: span 2;">
                                    <button type="button" onclick="window.close()" class="btn-sm btn-secondary" style="padding: 8px 16px;">Cerrar Ventana</button>
                                </div>
                            </div>
                        <?php endif; ?>

                        <div class="socio-info" id="socio_info" style="display: none;">
                            <h4>Datos del Socio</h4>
                            <p><strong>Nombre:</strong> -</p>
                            <p><strong>Código:</strong> -</p>
                            <p><strong>DNI:</strong> -</p>
                            <p><strong>Cuenta:</strong> -</p>
                            <p><strong>Celular:</strong> -</p>
                            <p><strong>Email:</strong> -</p>
                            <p><strong>Condición:</strong> -</p>
                            <p><strong>Situación:</strong> -</p>
                            <p><strong>Sobregiro:</strong> -</p>
                            <p><strong>Garantes:</strong> -</p>
                        </div>
                        <input type="hidden" name="socio" value="">
                        <input type="hidden" name="nombres" value="">
                        <input type="hidden" name="socio_dni" value="">
                        <input type="hidden" name="cuenta" value="">
                    </div>

                    <!-- Datos del Trámite -->
                    <div class="form-section">
                        <h3>Datos del Trámite</h3>
                        <div class="form-row">
                            <div class="form-group">
                                <label>Tipo de Documento</label>
                                <select name="tipo" id="tipo" required onchange="generarNumero()">
                                    <option value="">Seleccione...</option>
                                    <?php foreach ($tiposDocumentos as $tipo): ?>
                                        <option value="<?= $escape($tipo['F35_DOCUMENTO_ID']) ?>">
                                            <?= $escape($tipo['F35_DOCUMENTO_ID']) ?> - <?= $escape($tipo['F35_DOCUMENTO_DESC']) ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="form-group">
                                <label>Número</label>
                                <input type="text" name="numero" id="numero" readonly value="<?= $escape($numeroGenerado ?? '') ?>">
                            </div>
                            <div class="form-group">
                                <label>Fecha</label>
                                <input type="date" name="fecha" value="<?= date('Y-m-d') ?>" required>
                            </div>
                        </div>

                        <div class="form-row">
                            <div class="form-group" style="grid-column: span 2;">
                                <label>Descripción / Asunto</label>
                                <textarea name="descripcion" required></textarea>
                            </div>
                        </div>

                        <div class="form-row">
                            <div class="form-group">
                                <label>Destino (Derivado a)</label>
                                <select name="destino" required>
                                    <option value="">--Seleccione--</option>
                                    <?php foreach ($usuarios as $usuario): ?>
                                        <option value="<?= $escape($usuario['nombre']) ?>">
                                            <?= $escape($usuario['nombre']) ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="form-group">
                                <label>Físico</label>
                                <select name="fisico">
                                    <option value="NO">No</option>
                                    <option value="SI">Sí</option>
                                </select>
                            </div>
                        </div>

                        <div class="form-row">
                            <div class="form-group">
                                <label>Importe</label>
                                <input type="text" name="importe" placeholder="0.00">
                            </div>
                            <div class="form-group">
                                <label>Cuotas</label>
                                <input type="number" name="cuotas" placeholder="0">
                            </div>
                        </div>

                        <div class="form-row">
                            <div class="form-group">
                                <label>Tramitante</label>
                                <input type="text" name="tramitante" placeholder="Nombre del tramitante">
                            </div>
                            <div class="form-group">
                                <label>DNI Tramitante</label>
                                <input type="text" name="tramitante_dni" placeholder="DNI del tramitante">
                            </div>
                        </div>

                        <div class="form-row">
                            <div class="form-group">
                                <label>Celular Tramitante</label>
                                <input type="text" name="tramitante_cel" placeholder="Celular del tramitante">
                            </div>
                            <div class="form-group">
                                <label>Email Tramitante</label>
                                <input type="email" name="tramitante_email" placeholder="Email del tramitante">
                            </div>
                        </div>

                        <div class="form-row">
                            <div class="form-group">
                                <label>Email Tramitante 2</label>
                                <input type="email" name="tramitante_email2" placeholder="Email adicional del tramitante">
                            </div>
                            <div class="form-group">
                                <label>Ref. Documento</label>
                                <input type="text" name="ref_documento" placeholder="Referencia del documento">
                            </div>
                        </div>
                    </div>

                    <!-- Observaciones -->
                    <div class="form-section">
                        <h3>Observaciones</h3>
                        <div class="form-row">
                            <div class="form-group" style="grid-column: span 2;">
                                <label>Observación 1</label>
                                <textarea name="obs"></textarea>
                            </div>
                        </div>
                        <div class="form-row">
                            <div class="form-group" style="grid-column: span 2;">
                                <label>Observación 2</label>
                                <textarea name="obs2"></textarea>
                            </div>
                        </div>
                        <div class="form-row">
                            <div class="form-group" style="grid-column: span 2;">
                                <label>Observación 3</label>
                                <textarea name="obs3"></textarea>
                            </div>
                        </div>
                        <div class="form-row">
                            <div class="form-group" style="grid-column: span 2;">
                                <label>Referencia</label>
                                <textarea name="referencia"></textarea>
                            </div>
                        </div>
                    </div>

                    <!-- Archivos Adjuntos -->
                    <div class="form-section">
                        <h3>Archivos Adjuntos (Máximo 3)</h3>
                        <div class="form-row">
                            <div class="form-group file-upload">
                                <label>Archivo 1</label>
                                <input type="file" name="archivo1" accept=".pdf,.jpg,.jpeg,.png,.doc,.docx">
                            </div>
                            <div class="form-group file-upload">
                                <label>Archivo 2</label>
                                <input type="file" name="archivo2" accept=".pdf,.jpg,.jpeg,.png,.doc,.docx">
                            </div>
                            <div class="form-group file-upload">
                                <label>Archivo 3</label>
                                <input type="file" name="archivo3" accept=".pdf,.jpg,.jpeg,.png,.doc,.docx">
                            </div>
                        </div>
                    </div>

                    <div class="form-actions">
                        <button type="submit">Registrar Trámite</button>
                        <a href="index.php?module=tramites&action=listar" class="btn-sm btn-secondary" style="padding: 8px 24px; font-size: 13px; font-weight: bold;">Cancelar</a>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <script>
        // Definir funciones globalmente antes de DOMContentLoaded
        window.buscarSocio = function() {
            const codigo = document.getElementById('socio_codigo').value.trim();

            if (!codigo) {
                alert('Ingrese un código de socio');
                return;
            }

            // Mostrar mensaje de carga
            const btn = document.querySelector('button[onclick="buscarSocio()"]');
            const originalText = btn.textContent;
            btn.textContent = 'Buscando...';
            btn.disabled = true;

            // Buscar socio con AJAX
            fetch('index.php?module=tramites&action=buscar_socio_ajax&codigo=' + encodeURIComponent(codigo))
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        const socio = data.socio;

                        // Mostrar datos del socio
                        const nombreCompleto = (socio.F01_NOMB_PRI || '') + ' ' +
                                              (socio.F01_NOMB_SEG || '') + ' ' +
                                              (socio.F01_APE_PAT || '') + ' ' +
                                              (socio.F01_APE_MAT || '');

                        const nombreFormateado = nombreCompleto.trim() || '-';

                        const socioInfo = document.getElementById('socio_info');
                        if (socioInfo) {
                            socioInfo.style.display = 'block';
                            const parrafos = socioInfo.querySelectorAll('p');
                            if (parrafos.length >= 9) {
                                parrafos[0].innerHTML = '<strong>Nombre:</strong> ' + nombreFormateado;
                                parrafos[1].innerHTML = '<strong>Código:</strong> ' + (socio.F02_ID_CLIENTE || '-');
                                parrafos[2].innerHTML = '<strong>DNI:</strong> ' + (socio.F01_NUM_DOC || '-');
                                parrafos[3].innerHTML = '<strong>Cuenta:</strong> ' + (socio.F01_CUENTA || '-');
                                parrafos[4].innerHTML = '<strong>Celular:</strong> ' + (socio.F01_TELF_CEL || '-');
                                parrafos[5].innerHTML = '<strong>Email:</strong> ' + (socio.F01_CTA_CORREO || '-');
                                parrafos[6].innerHTML = '<strong>Condición:</strong> ' + (socio.F01_CONDICION || '-');
                                parrafos[7].innerHTML = '<strong>Situación:</strong> ' + (socio.F01_SITUACION || '-');
                                parrafos[8].innerHTML = '<strong>Sobregiro:</strong> ' + (socio.tiene_sobregiro ? 'Sí' : 'No');
                                
                                // Mostrar garantes si existen
                                const garantes = socio.garantes || [];
                                if (garantes.length > 0) {
                                    parrafos[9].innerHTML = '<strong>Garantes:</strong> ' + garantes.join(', ');
                                } else {
                                    parrafos[9].innerHTML = '<strong>Garantes:</strong> -';
                                }
                            }
                        }

                        // Actualizar campos ocultos
                        document.querySelector('input[name="socio"]').value = socio.F02_ID_CLIENTE || '';
                        document.querySelector('input[name="nombres"]').value = nombreFormateado;
                        document.querySelector('input[name="socio_dni"]').value = socio.F01_NUM_DOC || '';
                        document.querySelector('input[name="cuenta"]').value = socio.F01_CUENTA || '';
                    } else {
                        alert(data.message || 'Socio no encontrado');
                    }
                })
                .catch(error => {
                    alert('Error al buscar socio: ' + error);
                })
                .finally(() => {
                    btn.textContent = originalText;
                    btn.disabled = false;
                });
        };

        window.generarNumero = function() {
            const tipo = document.getElementById('tipo').value;
            const numeroInput = document.getElementById('numero');

            if (!tipo) {
                numeroInput.value = '';
                return;
            }

            // Buscar correlativo del tipo seleccionado
            const correlativo = window.correlativos?.find(c => c.tipo_doc == tipo);

            if (correlativo) {
                const numeroActual = parseInt(correlativo.numero_actual);
                const numeroInicio = parseInt(correlativo.numero_inicio);
                const numeroFin = parseInt(correlativo.numero_fin);
                const anioActual = new Date().getFullYear();

                // Incrementar número actual
                let nuevoNumero = numeroActual + 1;

                // Si excede el rango, reiniciar al inicio
                if (nuevoNumero > numeroFin) {
                    nuevoNumero = numeroInicio;
                }

                // Tipos 22, 23, 24 necesitan formato con cero a la izquierda (4 dígitos)
                const tiposConCero = [22, 23, 24];
                let numeroFormateado;
                if (tiposConCero.includes(parseInt(tipo))) {
                    numeroFormateado = String(nuevoNumero).padStart(4, '0');
                } else {
                    numeroFormateado = String(nuevoNumero);
                }

                // Combinar número y año (formato: NUMERO-AÑO)
                const numeroTramite = numeroFormateado + '-' + anioActual;

                numeroInput.value = numeroTramite;
            } else {
                numeroInput.value = '';
                alert('No existe correlativo para este tipo de documento');
            }
        };

        document.addEventListener('DOMContentLoaded', function() {
            // Cargar correlativos y guardar globalmente
            window.correlativos = <?= json_encode($correlativos ?? []) ?>;

            // Si es popup y hay código de socio prellenado, buscar automáticamente
            <?php if (isset($_GET['popup']) && $_GET['popup'] === '1' && !empty($_GET['socio_codigo'])): ?>
                const codigoSocio = '<?= $escape($_GET['socio_codigo']) ?>';
                if (codigoSocio) {
                    // Esperar un momento para que el DOM esté completamente listo
                    setTimeout(() => buscarSocio(), 100);
                }
            <?php endif; ?>
        });
    </script>
</body>
</html>
