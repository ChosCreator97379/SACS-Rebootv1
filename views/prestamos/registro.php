<?php
$escape = static fn (mixed $value): string => htmlspecialchars((string) ($value ?? ''), ENT_QUOTES, 'UTF-8');

$prestamosServicio = new \Sacs\Services\PrestamosServicio();
$sociosServicio = new \Sacs\Services\SociosServicio();

$usuario = $_SESSION['sacs_user']['username'] ?? 'SISTEMA';
$displayName = substr($usuario, 0, 2);
$profile = 'Administrador';

// Obtener listado de socios para autocompletado
$socios = $sociosServicio->listarSocios([], 1, 1000);
?>
<!doctype html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Registrar Préstamo | SACS Nuevo</title>
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
            --green: #0f766e;
            --red: #991b1b;
            --border: #e2e8f0;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif;
            background: var(--paper);
            color: var(--ink);
            line-height: 1.5;
        }

        .app {
            display: flex;
            min-height: 100vh;
        }

        .sidebar {
            width: 280px;
            background: var(--navy);
            color: white;
            padding: 20px;
            display: flex;
            flex-direction: column;
        }

        .brand {
            display: flex;
            align-items: center;
            gap: 12px;
            margin-bottom: 30px;
            text-decoration: none;
            color: white;
        }

        .brand-mark {
            display: grid;
            place-items: center;
            width: 38px;
            height: 38px;
            border: 2px solid var(--gold);
            border-radius: 50%;
            color: var(--gold);
            font-family: Georgia, serif;
            font-size: 20px;
        }

        .brand-name {
            font-family: Georgia, serif;
            font-size: 22px;
            letter-spacing: .02em;
        }

        .brand-note {
            display: block;
            margin-top: 3px;
            color: #9bb3b7;
            font-size: 11px;
            letter-spacing: .12em;
            text-transform: uppercase;
        }

        .nav-label {
            font-size: 11px;
            text-transform: uppercase;
            letter-spacing: 1px;
            color: rgba(255,255,255,0.5);
            margin: 20px 0 10px;
        }

        .nav {
            display: flex;
            flex-direction: column;
            gap: 4px;
        }

        .nav-link {
            padding: 10px 12px;
            border-radius: 6px;
            text-decoration: none;
            color: rgba(255,255,255,0.8);
            font-size: 14px;
            transition: all 0.2s;
        }

        .nav-link:hover {
            background: rgba(255,255,255,0.1);
            color: white;
        }

        .nav-link.active {
            background: var(--teal);
            color: white;
        }

        .main {
            flex: 1;
            padding: 30px;
            overflow-y: auto;
        }

        .header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
        }

        .header h1 {
            font-size: 24px;
            font-weight: 600;
        }

        .user {
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .user-avatar {
            width: 36px;
            height: 36px;
            background: var(--teal);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 600;
        }

        .user-name {
            font-weight: 500;
        }

        .user-profile {
            font-size: 12px;
            color: var(--muted);
        }

        .intro {
            color: var(--muted);
            margin-bottom: 30px;
        }

        .panel {
            background: var(--panel);
            border-radius: 8px;
            border: 1px solid var(--border);
            padding: 24px;
            margin-bottom: 20px;
        }

        .panel h2 {
            font-size: 18px;
            font-weight: 600;
            margin-bottom: 20px;
        }

        .form-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 16px;
        }

        .form-group {
            margin-bottom: 16px;
        }

        .form-group label {
            display: block;
            font-size: 13px;
            font-weight: 500;
            margin-bottom: 6px;
            color: var(--muted);
        }

        .form-group input,
        .form-group select,
        .form-group textarea {
            width: 100%;
            padding: 10px 12px;
            border: 1px solid var(--border);
            border-radius: 6px;
            font-size: 14px;
            font-family: inherit;
        }

        .form-group input:focus,
        .form-group select:focus,
        .form-group textarea:focus {
            outline: none;
            border-color: var(--teal);
        }

        .btn {
            padding: 10px 20px;
            border: none;
            border-radius: 6px;
            font-size: 14px;
            font-weight: 500;
            cursor: pointer;
            text-decoration: none;
            display: inline-block;
        }

        .btn-primary {
            background: var(--teal);
            color: white;
        }

        .btn-primary:hover {
            background: #166160;
        }

        .btn-secondary {
            background: var(--muted);
            color: white;
        }

        .btn-secondary:hover {
            background: #56606d;
        }

        .success {
            background: #dcfce7;
            color: #166534;
            padding: 12px;
            border-radius: 6px;
            margin-bottom: 20px;
        }

        .error {
            background: #fee2e2;
            color: #991b1b;
            padding: 12px;
            border-radius: 6px;
            margin-bottom: 20px;
        }

        .garantes-section {
            margin-top: 24px;
            padding-top: 24px;
            border-top: 1px solid var(--border);
        }

        .garante-row {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 16px;
            margin-bottom: 16px;
        }
    </style>
</head>
<body>
    <div class="app">
        <aside class="sidebar">
            <a href="index.php" class="brand">
                <div class="brand-mark">S</div>
                <div>
                    <div class="brand-name">SACS</div>
                    <div class="brand-note">gestion social</div>
                </div>
            </a>
            <div class="nav-label">Préstamos</div>
            <nav class="nav">
                <a href="index.php?module=prestamos&action=listado" class="nav-link">Listado de Préstamos</a>
                <a href="index.php?module=prestamos&action=registro" class="nav-link active">Registrar Préstamo</a>
            </nav>
            <div class="nav-label">Reportes</div>
            <nav class="nav">
                <a href="index.php?module=prestamos&action=reporte_general" class="nav-link">Reporte General</a>
                <a href="index.php?module=prestamos&action=reporte_consolidado" class="nav-link">Reporte Consolidado</a>
                <a href="index.php?module=prestamos&action=reporte_cuota" class="nav-link">Reporte por Cuota</a>
            </nav>
        </aside>
        </aside>

        <main class="main">
            <div class="header">
                <h1>Registrar Préstamo</h1>
                <div class="user">
                    <div class="user-avatar"><?= $escape($displayName) ?></div>
                    <div>
                        <div class="user-name"><?= $escape($usuario) ?></div>
                        <div class="user-profile"><?= $escape($profile) ?></div>
                    </div>
                </div>
            </div>
            <p class="intro">Registrar nuevo préstamo en el sistema</p>

            <?php if (isset($_GET['success'])): ?>
                <div class="success"><?= $escape($_GET['success']) ?></div>
            <?php endif; ?>

            <?php if (isset($_GET['error'])): ?>
                <div class="error"><?= $escape($_GET['error']) ?></div>
            <?php endif; ?>

            <section class="panel">
                <h2>Datos del Préstamo</h2>
                <form method="post" action="index.php">
                    <input type="hidden" name="module" value="prestamos">
                    <input type="hidden" name="action" value="registrar">
                    <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">
                    
                    <div class="form-grid">
                        <div class="form-group">
                            <label for="cliente">Cliente *</label>
                            <input type="text" id="cliente" name="cliente" list="lista-socios" required placeholder="Código de cliente">
                            <datalist id="lista-socios">
                                <?php foreach ($socios as $socio): ?>
                                    <option value="<?= $escape($socio['F02_ID_CLIENTE']) ?>">
                                        <?= $escape($socio['F02_ID_CLIENTE'] . ' - ' . $socio['F01_APE_PAT'] . ' ' . $socio['F01_APE_MAT'] . ', ' . $socio['F01_NOMB_PRI']) ?>
                                    </option>
                                <?php endforeach; ?>
                            </datalist>
                        </div>
                        
                        <div class="form-group">
                            <label for="importe">Importe *</label>
                            <input type="number" id="importe" name="importe" step="0.01" min="0" required placeholder="0.00">
                        </div>
                        
                        <div class="form-group">
                            <label for="cuotas">Cuotas *</label>
                            <input type="number" id="cuotas" name="cuotas" min="1" required placeholder="Número de cuotas">
                        </div>
                        
                        <div class="form-group">
                            <label for="interes">Tasa de Interés (%) *</label>
                            <input type="number" id="interes" name="interes" step="0.01" min="0" required placeholder="0.00">
                        </div>
                        
                        <div class="form-group">
                            <label for="fecha_desembolso">Fecha de Desembolso *</label>
                            <input type="date" id="fecha_desembolso" name="fecha_desembolso" required>
                        </div>
                        
                        <div class="form-group">
                            <label for="producto">Producto</label>
                            <input type="text" id="producto" name="producto" placeholder="Producto">
                        </div>
                        
                        <div class="form-group">
                            <label for="tramite">Trámite</label>
                            <div style="display: flex; gap: 8px;">
                                <input type="text" id="tramite" name="tramite" placeholder="Número de trámite" style="flex: 1;">
                                <button type="button" onclick="abrirPopupTramite()" class="btn btn-secondary" style="padding: 10px 16px;">Crear Trámite</button>
                            </div>
                        </div>
                    </div>
                    
                    <div class="garantes-section">
                        <h3>Garantes (Opcional)</h3>
                        <div class="garante-row">
                            <div class="form-group">
                                <label for="garante">Garante 1</label>
                                <input type="text" id="garante" name="garante" list="lista-socios" placeholder="Código de garante">
                                <datalist id="lista-socios">
                                    <?php foreach ($socios as $socio): ?>
                                        <option value="<?= $escape($socio['F02_ID_CLIENTE']) ?>">
                                            <?= $escape($socio['F02_ID_CLIENTE'] . ' - ' . $socio['F01_APE_PAT'] . ' ' . $socio['F01_APE_MAT'] . ', ' . $socio['F01_NOMB_PRI']) ?>
                                        </option>
                                    <?php endforeach; ?>
                                </datalist>
                            </div>
                            
                            <div class="form-group">
                                <label for="garante2">Garante 2</label>
                                <input type="text" id="garante2" name="garante2" list="lista-socios" placeholder="Código de garante">
                                <datalist id="lista-socios">
                                    <?php foreach ($socios as $socio): ?>
                                        <option value="<?= $escape($socio['F02_ID_CLIENTE']) ?>">
                                            <?= $escape($socio['F02_ID_CLIENTE'] . ' - ' . $socio['F01_APE_PAT'] . ' ' . $socio['F01_APE_MAT'] . ', ' . $socio['F01_NOMB_PRI']) ?>
                                        </option>
                                    <?php endforeach; ?>
                                </datalist>
                            </div>
                            
                            <div class="form-group">
                                <label for="garante3">Garante 3</label>
                                <input type="text" id="garante3" name="garante3" list="lista-socios" placeholder="Código de garante">
                                <datalist id="lista-socios">
                                    <?php foreach ($socios as $socio): ?>
                                        <option value="<?= $escape($socio['F02_ID_CLIENTE']) ?>">
                                            <?= $escape($socio['F02_ID_CLIENTE'] . ' - ' . $socio['F01_APE_PAT'] . ' ' . $socio['F01_APE_MAT'] . ', ' . $socio['F01_NOMB_PRI']) ?>
                                        </option>
                                    <?php endforeach; ?>
                                </datalist>
                            </div>
                            
                            <div class="form-group">
                                <label for="garante4">Garante 4</label>
                                <input type="text" id="garante4" name="garante4" list="lista-socios" placeholder="Código de garante">
                                <datalist id="lista-socios">
                                    <?php foreach ($socios as $socio): ?>
                                        <option value="<?= $escape($socio['F02_ID_CLIENTE']) ?>">
                                            <?= $escape($socio['F02_ID_CLIENTE'] . ' - ' . $socio['F01_APE_PAT'] . ' ' . $socio['F01_APE_MAT'] . ', ' . $socio['F01_NOMB_PRI']) ?>
                                        </option>
                                    <?php endforeach; ?>
                                </datalist>
                            </div>
                        </div>
                    </div>

                    <div style="display: flex; gap: 12px; margin-top: 24px;">
                        <button type="submit" class="btn btn-primary">Registrar Préstamo</button>
                        <a href="index.php?module=prestamos&action=listado" class="btn btn-secondary">Cancelar</a>
                    </div>
                </form>
            </section>
        </main>
    </div>

    <script>
        function abrirPopupTramite() {
            const cliente = document.getElementById('cliente').value.trim();

            if (!cliente) {
                alert('Primero debe ingresar el código del cliente');
                return;
            }

            const url = 'index.php?module=tramites&action=registrar&socio_codigo=' + encodeURIComponent(cliente) + '&popup=1';
            const popup = window.open(url, 'tramite', 'width=800,height=600,scrollbars=yes,resizable=yes');

            if (!popup) {
                alert('No se pudo abrir la ventana emergente. Verifique que su navegador permita ventanas emergentes.');
            }
        }

        // Escuchar mensajes desde el popup
        window.addEventListener('message', function(event) {
            if (event.data && event.data.tramiteNumero) {
                document.getElementById('tramite').value = event.data.tramiteNumero;
            }
        });
    </script>
</body>
</html>
