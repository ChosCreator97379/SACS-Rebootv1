<?php
require dirname(__DIR__) . '/../config/bootstrap.php';

use Sacs\Services\SociosServicio;

$sociosServicio = new SociosServicio();

// Obtener datos de ubigeo
$ubigeos = $sociosServicio->obtenerTodosUbigeos();

// Generar arrays JavaScript
$ubigeosPorDepartamento = [];
$ubigeosPorProvincia = [];

foreach ($ubigeos as $ubigeo) {
    $depCod = $ubigeo['departamento_codigo'];
    $prov = $ubigeo['provincia'];
    $dist = $ubigeo['distrito'];
    
    if (!isset($ubigeosPorDepartamento[$depCod])) {
        $ubigeosPorDepartamento[$depCod] = [];
    }
    
    if (!in_array($prov, $ubigeosPorDepartamento[$depCod])) {
        $ubigeosPorDepartamento[$depCod][] = $prov;
    }
    
    $key = $depCod . '|' . $prov;
    if (!isset($ubigeosPorProvincia[$key])) {
        $ubigeosPorProvincia[$key] = [];
    }
    
    if (!in_array($dist, $ubigeosPorProvincia[$key])) {
        $ubigeosPorProvincia[$key][] = $dist;
    }
}

$profesiones = [];
$instrucciones = [];

$escape = static fn (mixed $value): string => htmlspecialchars((string) ($value ?? ''), ENT_QUOTES, 'UTF-8');
?>
<!doctype html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Registro de Socio | SACS Nuevo</title>
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
        .layout { display: grid; grid-template-columns: repeat(2, minmax(0, 1fr)); gap: 24px; }
        .panel { padding: 24px; border: 1px solid var(--line); border-radius: 7px; background: var(--panel); }
        .panel h2 { margin: 0 0 20px; font-size: 18px; font-weight: 400; }
        .form-group { margin-bottom: 16px; }
        .form-group label { display: block; margin-bottom: 6px; color: var(--muted); font-size: 11px; font-weight: bold; letter-spacing: .04em; }
        .form-group input, .form-group select { min-height: 38px; padding: 8px 10px; border: 1px solid #b9c7c7; border-radius: 4px; background: #fff; color: var(--ink); font: inherit; width: 100%; }
        .form-group input:focus, .form-group select:focus { outline: none; border-color: var(--teal); }
        .form-group .error { color: #c53030; font-size: 12px; margin-top: 4px; }
        .form-row { display: grid; grid-template-columns: repeat(2, minmax(0, 1fr)); gap: 12px; }
        .form-row-3 { display: grid; grid-template-columns: repeat(3, minmax(0, 1fr)); gap: 12px; }
        .actions { display: flex; gap: 12px; margin-top: 24px; }
        button { min-height: 38px; padding: 8px 20px; border: none; border-radius: 4px; background: var(--teal); color: #fff; cursor: pointer; font-size: 13px; font-weight: bold; }
        button:hover { background: #166664; }
        button.secondary { background: #e7f0ed; color: var(--navy); }
        button.secondary:hover { background: #dce4e1; }
        .alert { padding: 16px; border-radius: 7px; margin-bottom: 24px; }
        .alert-error { background: #fed7d7; color: #c53030; border: 1px solid #feb2b2; }
        .alert-success { background: #c6f6d5; color: #2f855a; border: 1px solid #9ae6b4; }
        @media (max-width: 900px) { .shell { grid-template-columns: 1fr; } .sidebar { display: none; } .layout { grid-template-columns: 1fr; } }
    </style>
</head>
<body data-ubigeos-departamento="<?php echo htmlspecialchars(json_encode($ubigeosPorDepartamento), ENT_QUOTES, 'UTF-8'); ?>" data-ubigeos-provincia="<?php echo htmlspecialchars(json_encode($ubigeosPorProvincia), ENT_QUOTES, 'UTF-8'); ?>">
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
                <a href="index.php?module=socios&action=registro" class="nav-link active">Registro de Socio</a>
            </nav>
        </aside>

        <main class="main">
            <div class="header">
                <h1>Registro de Socio</h1>
            </div>
            <p class="intro">Complete el formulario para registrar un nuevo socio en el sistema.</p>

            <?php if (isset($_GET['error'])): ?>
                <div class="alert alert-error">
                    <?= $escape($_GET['error']) ?>
                </div>
            <?php endif; ?>

            <?php if (isset($_GET['success'])): ?>
                <div class="alert alert-success">
                    Socio registrado exitosamente. Código de cliente: <?= $escape($_GET['success']) ?>
                </div>
            <?php endif; ?>

            <form method="post" action="index.php?module=socios&action=registrar">
                <input type="hidden" name="csrf_token" value="<?= $escape($_SESSION['csrf_token'] ?? '') ?>">

                <div class="layout">
                    <div class="panel">
                        <h2>Datos Personales</h2>
                        
                        <div class="form-row">
                            <div class="form-group">
                                <label for="ape_pat">Apellido Paterno *</label>
                                <input type="text" id="ape_pat" name="ape_pat" required>
                            </div>
                            <div class="form-group">
                                <label for="ape_mat">Apellido Materno</label>
                                <input type="text" id="ape_mat" name="ape_mat">
                            </div>
                        </div>

                        <div class="form-row">
                            <div class="form-group">
                                <label for="nomb_pri">Primer Nombre *</label>
                                <input type="text" id="nomb_pri" name="nomb_pri" required>
                            </div>
                            <div class="form-group">
                                <label for="nomb_seg">Segundo Nombre</label>
                                <input type="text" id="nomb_seg" name="nomb_seg">
                            </div>
                        </div>

                        <div class="form-row">
                            <div class="form-group">
                                <label for="tpo_doc">Tipo Documento</label>
                                <select id="tpo_doc" name="tpo_doc">
                                    <option value="">Seleccione</option>
                                    <option value="1">DNI</option>
                                    <option value="2">Carnet Extranjería</option>
                                    <option value="3">Pasaporte</option>
                                </select>
                            </div>
                            <div class="form-group">
                                <label for="num_doc">Número Documento</label>
                                <input type="text" id="num_doc" name="num_doc">
                            </div>
                        </div>

                        <div class="form-row">
                            <div class="form-group">
                                <label for="sexo">Sexo</label>
                                <select id="sexo" name="sexo">
                                    <option value="">Seleccione</option>
                                    <option value="1">Masculino</option>
                                    <option value="2">Femenino</option>
                                </select>
                            </div>
                            <div class="form-group">
                                <label for="fe_nac">Fecha Nacimiento</label>
                                <input type="date" id="fe_nac" name="fe_nac">
                            </div>
                        </div>

                        <div class="form-row">
                            <div class="form-group">
                                <label for="stdo_civil">Estado Civil</label>
                                <select id="stdo_civil" name="stdo_civil">
                                    <option value="">Seleccione</option>
                                    <option value="1">Soltero</option>
                                    <option value="2">Casado</option>
                                    <option value="3">Divorciado</option>
                                    <option value="4">Viudo</option>
                                </select>
                            </div>
                            <div class="form-group">
                                <label for="edad">Edad</label>
                                <input type="number" id="edad" name="edad" min="0">
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="cta_correo">Correo Electrónico</label>
                            <input type="email" id="cta_correo" name="cta_correo">
                        </div>
                    </div>

                    <div class="panel">
                        <h2>Datos de Ubicación</h2>
                        
                        <div class="form-group">
                            <label for="desc_coment">Comentarios / Dirección</label>
                            <textarea id="desc_coment" name="desc_coment" rows="2" style="width: 100%; padding: 8px 10px; border: 1px solid #b9c7c7; border-radius: 4px; background: #fff; color: var(--ink); font: inherit;"></textarea>
                        </div>

                        <div class="form-row-3">
                            <div class="form-group">
                                <label for="departamento">Departamento</label>
                                <select id="departamento" name="departamento">
                                    <option value="">Seleccione</option>
                                    <option value="01">Amazonas</option>
                                    <option value="02">Ancash</option>
                                    <option value="03">Apurímac</option>
                                    <option value="04">Arequipa</option>
                                    <option value="05">Ayacucho</option>
                                    <option value="06">Cajamarca</option>
                                    <option value="07">Callao</option>
                                    <option value="08">Cusco</option>
                                    <option value="09">Huancavelica</option>
                                    <option value="10">Huánuco</option>
                                    <option value="11">Ica</option>
                                    <option value="12">Junín</option>
                                    <option value="13">La Libertad</option>
                                    <option value="14">Lambayeque</option>
                                    <option value="15">Lima</option>
                                    <option value="16">Loreto</option>
                                    <option value="17">Madre de Dios</option>
                                    <option value="18">Moquegua</option>
                                    <option value="19">Pasco</option>
                                    <option value="20">Piura</option>
                                    <option value="21">Puno</option>
                                    <option value="22">San Martín</option>
                                    <option value="23">Tacna</option>
                                    <option value="24">Tumbes</option>
                                    <option value="25">Ucayali</option>
                                </select>
                            </div>
                            <div class="form-group">
                                <label for="provincia">Provincia</label>
                                <select id="provincia" name="provincia">
                                    <option value="">Seleccione</option>
                                </select>
                            </div>
                            <div class="form-group">
                                <label for="distrito">Distrito</label>
                                <select id="distrito" name="distrito">
                                    <option value="">Seleccione</option>
                                </select>
                            </div>
                        </div>

                        <div class="form-row">
                            <div class="form-group">
                                <label for="telf_fijo">Teléfono Fijo</label>
                                <input type="tel" id="telf_fijo" name="telf_fijo">
                            </div>
                            <div class="form-group">
                                <label for="telf_cel">Teléfono Celular</label>
                                <input type="tel" id="telf_cel" name="telf_cel">
                            </div>
                        </div>
                    </div>

                    <div class="panel">
                        <h2>Datos Laborales</h2>
                        
                        <div class="form-row">
                            <div class="form-group">
                                <label for="id_profesion">Profesión</label>
                                <select id="id_profesion" name="id_profesion">
                                    <option value="">Seleccione</option>
                                    <?php foreach ($profesiones ?? [] as $profesion): ?>
                                        <option value="<?= $escape($profesion['F09_ID_PROFESION']) ?>">
                                            <?= $escape($profesion['F09_DESC_PROF']) ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="form-group">
                                <label for="id_instruccion">Instrucción</label>
                                <select id="id_instruccion" name="id_instruccion">
                                    <option value="">Seleccione</option>
                                    <?php foreach ($instrucciones ?? [] as $instruccion): ?>
                                        <option value="<?= $escape($instruccion['F10_ID_INSTRUCCION']) ?>">
                                            <?= $escape($instruccion['F10_DESC_INSTRUCC']) ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>

                        <div class="form-row">
                            <div class="form-group">
                                <label for="ingreso">Ingreso Mensual</label>
                                <input type="number" id="ingreso" name="ingreso" step="0.01" min="0">
                            </div>
                            <div class="form-group">
                                <label for="condicion">Condición</label>
                                <select id="condicion" name="condicion">
                                    <option value="">Seleccione</option>
                                    <option value="ACTIVO">Activo</option>
                                    <option value="PENSIONISTA">Pensionista</option>
                                    <option value="INDEPENDIENTE">Independiente</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="panel">
                        <h2>Datos Adicionales</h2>
                        
                        <div class="form-row">
                            <div class="form-group">
                                <label for="cuenta">Cuenta</label>
                                <input type="text" id="cuenta" name="cuenta">
                            </div>
                            <div class="form-group">
                                <label for="aportacion">Aportación</label>
                                <input type="number" id="aportacion" name="aportacion" step="0.01" min="0">
                            </div>
                        </div>

                        <div class="form-row">
                            <div class="form-group">
                                <label for="saldo">Saldo</label>
                                <input type="number" id="saldo" name="saldo" step="0.01" min="0">
                            </div>
                            <div class="form-group">
                                <label for="documento">Documento</label>
                                <input type="text" id="documento" name="documento">
                            </div>
                        </div>

                        <div class="form-row">
                            <div class="form-group">
                                <label for="situacion">Situación</label>
                                <select id="situacion" name="situacion">
                                    <option value="">Seleccione</option>
                                    <option value="ACTIVO">Activo</option>
                                    <option value="INACTIVO">Inactivo</option>
                                    <option value="BAJA">Baja</option>
                                    <option value="FALLECIDO">Fallecido</option>
                                </select>
                            </div>
                            <div class="form-group">
                                <label for="fecha_retiro">Fecha Retiro</label>
                                <input type="date" id="fecha_retiro" name="fecha_retiro">
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="observacion">Observaciones</label>
                            <textarea id="observacion" name="observacion" rows="3" style="width: 100%; padding: 8px 10px; border: 1px solid #b9c7c7; border-radius: 4px; background: #fff; color: var(--ink); font: inherit;"></textarea>
                        </div>

                        <div class="form-group">
                            <label for="clave">Contraseña (opcional)</label>
                            <input type="password" id="clave" name="clave">
                        </div>
                    </div>

                    <div class="panel">
                        <h2>Datos de Dirección Detallada</h2>
                        
                        <div class="form-row">
                            <div class="form-group">
                                <label for="zona">Zona</label>
                                <input type="text" id="zona" name="zona">
                            </div>
                            <div class="form-group">
                                <label for="via">Vía</label>
                                <input type="text" id="via" name="via">
                            </div>
                        </div>

                        <div class="form-row-3">
                            <div class="form-group">
                                <label for="int">Interior</label>
                                <input type="text" id="int" name="int">
                            </div>
                            <div class="form-group">
                                <label for="ext">Exterior</label>
                                <input type="text" id="ext" name="ext">
                            </div>
                            <div class="form-group">
                                <label for="piso">Piso</label>
                                <input type="text" id="piso" name="piso">
                            </div>
                        </div>
                    </div>

                    <div class="panel">
                        <h2>Datos de Aportes</h2>
                        
                        <div class="form-row">
                            <div class="form-group">
                                <label for="aporta_base">Aporta Base</label>
                                <input type="number" id="aporta_base" name="aporta_base" min="0">
                            </div>
                            <div class="form-group">
                                <label for="aporta_sobre">Aporta Sobre</label>
                                <input type="number" id="aporta_sobre" name="aporta_sobre" step="0.01" min="0">
                            </div>
                        </div>

                        <div class="form-row">
                            <div class="form-group">
                                <label for="sobrevi">Sobre Nombre</label>
                                <input type="text" id="sobrevi" name="sobrevi">
                            </div>
                            <div class="form-group">
                                <label for="aporte_cope">Aporte COPE</label>
                                <input type="number" id="aporte_cope" name="aporte_cope" min="0" value="0">
                            </div>
                        </div>
                    </div>

                    <div class="panel">
                        <h2>Datos Adicionales</h2>
                        
                        <div class="form-row">
                            <div class="form-group">
                                <label for="titulo">Título</label>
                                <input type="text" id="titulo" name="titulo">
                            </div>
                            <div class="form-group">
                                <label for="carta">Carta</label>
                                <input type="text" id="carta" name="carta">
                            </div>
                        </div>

                        <div class="form-row">
                            <div class="form-group">
                                <label for="firma">Firma</label>
                                <select id="firma" name="firma">
                                    <option value="">Seleccione</option>
                                    <option value="S">Sí</option>
                                    <option value="N">No</option>
                                </select>
                            </div>
                            <div class="form-group">
                                <label for="fec_reingreso">Fecha Reingreso</label>
                                <input type="date" id="fec_reingreso" name="fec_reingreso">
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="obs">Observación 1</label>
                            <textarea id="obs" name="obs" rows="2" style="width: 100%; padding: 8px 10px; border: 1px solid #b9c7c7; border-radius: 4px; background: #fff; color: var(--ink); font: inherit;"></textarea>
                        </div>

                        <div class="form-group">
                            <label for="obs2">Observación 2</label>
                            <textarea id="obs2" name="obs2" rows="2" style="width: 100%; padding: 8px 10px; border: 1px solid #b9c7c7; border-radius: 4px; background: #fff; color: var(--ink); font: inherit;"></textarea>
                        </div>
                    </div>
                </div>

                <div class="actions">
                    <button type="submit">Registrar Socio</button>
                    <button type="button" class="secondary" onclick="window.location.href='index.php?module=socios&action=listado'">Cancelar</button>
                </div>
            </form>
        </main>
    </div>

    <script>
        // Datos de ubigeo cargados desde PHP
        var ubigeosPorDepartamento = {
            <?php
            foreach ($ubigeosPorDepartamento as $dep => $provs) {
                echo "'" . addslashes($dep) . "': [";
                foreach ($provs as $prov) {
                    echo "'" . addslashes($prov) . "',";
                }
                echo "],";
            }
            ?>
        };
        
        var ubigeosPorProvincia = {
            <?php
            foreach ($ubigeosPorProvincia as $key => $dists) {
                echo "'" . addslashes($key) . "': [";
                foreach ($dists as $dist) {
                    echo "'" . addslashes($dist) . "',";
                }
                echo "],";
            }
            ?>
        };

        // Cargar provincias cuando se selecciona un departamento
        document.getElementById('departamento').addEventListener('change', function() {
            const departamento = this.value;
            const provinciaSelect = document.getElementById('provincia');
            const distritoSelect = document.getElementById('distrito');

            // Limpiar selects
            provinciaSelect.innerHTML = '<option value="">Seleccione</option>';
            distritoSelect.innerHTML = '<option value="">Seleccione</option>';

            if (departamento === '' || !ubigeosPorDepartamento[departamento]) {
                return;
            }

            // Cargar provincias desde el array local
            ubigeosPorDepartamento[departamento].forEach(provincia => {
                const option = document.createElement('option');
                option.value = provincia;
                option.textContent = provincia;
                provinciaSelect.appendChild(option);
            });
        });

        // Cargar distritos cuando se selecciona una provincia
        document.getElementById('provincia').addEventListener('change', function() {
            const departamento = document.getElementById('departamento').value;
            const provincia = this.value;
            const distritoSelect = document.getElementById('distrito');

            // Limpiar select
            distritoSelect.innerHTML = '<option value="">Seleccione</option>';

            if (provincia === '' || departamento === '') {
                return;
            }

            const key = departamento + '|' + provincia;
            if (!ubigeosPorProvincia[key]) {
                return;
            }

            // Cargar distritos desde el array local
            ubigeosPorProvincia[key].forEach(distrito => {
                const option = document.createElement('option');
                option.value = distrito;
                option.textContent = distrito;
                distritoSelect.appendChild(option);
            });
        });
    </script>
</body>
</html>
