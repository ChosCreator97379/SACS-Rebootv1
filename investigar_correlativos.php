<?php
require_once __DIR__ . '/config/bootstrap.php';

use Sacs\Core\Database;

$db = Database::connection();

echo "=== CORRELATIVOS DE DOCUMENTOS ===\n";
$sql = "SELECT CORRELATIVO_TIPO_DOC, CORRELATIVO_SERIE, CORRELATIVO_NUMERO_ACTUAL, CORRELATIVO_NUMERO_INICIO, CORRELATIVO_NUMERO_FIN FROM f36_correlativos_documentos";
$stmt = $db->query($sql);
$correlativos = $stmt->fetchAll(PDO::FETCH_ASSOC);

foreach ($correlativos as $correlativo) {
    echo "Tipo: {$correlativo['CORRELATIVO_TIPO_DOC']}, Serie: {$correlativo['CORRELATIVO_SERIE']}, Número Actual: {$correlativo['CORRELATIVO_NUMERO_ACTUAL']}, Inicio: {$correlativo['CORRELATIVO_NUMERO_INICIO']}, Fin: {$correlativo['CORRELATIVO_NUMERO_FIN']}\n";
}

echo "\n=== ÚLTIMOS TRÁMITES REGISTRADOS ===\n";
$sql = "SELECT f29_codigo, f29_numero, f29_tipo, f29_fecha FROM f29_tramite ORDER BY f29_codigo DESC LIMIT 20";
$stmt = $db->query($sql);
$tramites = $stmt->fetchAll(PDO::FETCH_ASSOC);

foreach ($tramites as $tramite) {
    echo "Código: {$tramite['f29_codigo']}, Número: {$tramite['f29_numero']}, Tipo: {$tramite['f29_tipo']}, Fecha: {$tramite['f29_fecha']}\n";
}

echo "\n=== TIPOS DE DOCUMENTO ===\n";
$sql = "SELECT * FROM f35_tipo_documento LIMIT 5";
$stmt = $db->query($sql);
$tipos = $stmt->fetchAll(PDO::FETCH_ASSOC);

if (!empty($tipos)) {
    $columnas = array_keys($tipos[0]);
    echo "Columnas: " . implode(', ', $columnas) . "\n";
    foreach ($tipos as $tipo) {
        echo print_r($tipo, true) . "\n";
    }
}

echo "\n=== TIPOS DE DOCUMENTO ACTIVOS ===\n";
$sql = "SELECT * FROM f35_tipo_documento WHERE F35_DOCUMENTO_ESTADO = 1 ORDER BY F35_DOCUMENTO_ID";
$stmt = $db->query($sql);
$tipos = $stmt->fetchAll(PDO::FETCH_ASSOC);

if (!empty($tipos)) {
    foreach ($tipos as $tipo) {
        echo "ID: {$tipo['F35_DOCUMENTO_ID']}, DESC: {$tipo['F35_DOCUMENTO_DESC']}, ABRE: {$tipo['F35_DOCUMENTO_ABRE']}, ESTADO: {$tipo['F35_DOCUMENTO_ESTADO']}\n";
    }
}

echo "\n=== TIPOS DE DOCUMENTO CON INTERNO = 1 ===\n";
$sql = "SELECT * FROM f35_tipo_documento WHERE F35_DOCUMENTO_INTERNO = 1 ORDER BY F35_DOCUMENTO_ID";
$stmt = $db->query($sql);
$tipos = $stmt->fetchAll(PDO::FETCH_ASSOC);

if (!empty($tipos)) {
    foreach ($tipos as $tipo) {
        echo "ID: {$tipo['F35_DOCUMENTO_ID']}, DESC: {$tipo['F35_DOCUMENTO_DESC']}, ABRE: {$tipo['F35_DOCUMENTO_ABRE']}, INTERNO: {$tipo['F35_DOCUMENTO_INTERNO']}\n";
    }
}

echo "\n=== TIPOS DE DOCUMENTO CON INTERNO = 9 ===\n";
$sql = "SELECT * FROM f35_tipo_documento WHERE F35_DOCUMENTO_INTERNO = 9 ORDER BY F35_DOCUMENTO_ID";
$stmt = $db->query($sql);
$tipos = $stmt->fetchAll(PDO::FETCH_ASSOC);

if (!empty($tipos)) {
    foreach ($tipos as $tipo) {
        echo "ID: {$tipo['F35_DOCUMENTO_ID']}, DESC: {$tipo['F35_DOCUMENTO_DESC']}, ABRE: {$tipo['F35_DOCUMENTO_ABRE']}, INTERNO: {$tipo['F35_DOCUMENTO_INTERNO']}\n";
    }
}

echo "\n=== TIPOS 22, 23, 24 ===\n";
$sql = "SELECT * FROM f35_tipo_documento WHERE F35_DOCUMENTO_ID IN (22, 23, 24)";
$stmt = $db->query($sql);
$tipos = $stmt->fetchAll(PDO::FETCH_ASSOC);

if (!empty($tipos)) {
    foreach ($tipos as $tipo) {
        echo "ID: {$tipo['F35_DOCUMENTO_ID']}, DESC: {$tipo['F35_DOCUMENTO_DESC']}, ABRE: {$tipo['F35_DOCUMENTO_ABRE']}, INTERNO: {$tipo['F35_DOCUMENTO_INTERNO']}, ESTADO: {$tipo['F35_DOCUMENTO_ESTADO']}\n";
    }
}

echo "\n=== ANÁLISIS DE NÚMEROS DE TRÁMITES ===\n";
$sql = "SELECT f29_numero, f29_tipo FROM f29_tramite WHERE f29_numero LIKE '%-2026' ORDER BY f29_codigo DESC LIMIT 30";
$stmt = $db->query($sql);
$tramites = $stmt->fetchAll(PDO::FETCH_ASSOC);

foreach ($tramites as $tramite) {
    echo "Número: {$tramite['f29_numero']}, Tipo: {$tramite['f29_tipo']}\n";
}

echo "\n=== DESTINOS EN TRÁMITES REGISTRADOS ===\n";
$sql = "SELECT DISTINCT f29_destino FROM f29_tramite WHERE f29_destino IS NOT NULL AND f29_destino != '' ORDER BY f29_destino LIMIT 50";
$stmt = $db->query($sql);
$destinos = $stmt->fetchAll(PDO::FETCH_ASSOC);

foreach ($destinos as $destino) {
    echo "- {$destino['f29_destino']}\n";
}

echo "\n=== TABLA DE DESTINOS (si existe) ===\n";
$tablas = $db->query("SHOW TABLES LIKE '%destino%'")->fetchAll(PDO::FETCH_COLUMN);
if (!empty($tablas)) {
    echo "Tablas encontradas: " . implode(', ', $tablas) . "\n";
    foreach ($tablas as $tabla) {
        echo "\n=== Contenido de $tabla ===\n";
        $sql = "SELECT * FROM $tabla LIMIT 10";
        $stmt = $db->query($sql);
        $filas = $stmt->fetchAll(PDO::FETCH_ASSOC);
        if (!empty($filas)) {
            $columnas = array_keys($filas[0]);
            echo "Columnas: " . implode(', ', $columnas) . "\n";
            foreach ($filas as $fila) {
                echo print_r($fila, true) . "\n";
            }
        } else {
            echo "Tabla vacía\n";
        }
    }
} else {
    echo "No se encontró tabla de destinos\n";
}

echo "\n=== TABLA DE USUARIOS ===\n";
$tablas = $db->query("SHOW TABLES LIKE '%usuario%'")->fetchAll(PDO::FETCH_COLUMN);
if (!empty($tablas)) {
    echo "Tablas encontradas: " . implode(', ', $tablas) . "\n";
    foreach ($tablas as $tabla) {
        echo "\n=== Contenido de $tabla ===\n";
        $sql = "SELECT * FROM $tabla LIMIT 20";
        $stmt = $db->query($sql);
        $filas = $stmt->fetchAll(PDO::FETCH_ASSOC);
        if (!empty($filas)) {
            $columnas = array_keys($filas[0]);
            echo "Columnas: " . implode(', ', $columnas) . "\n";
            foreach ($filas as $fila) {
                echo print_r($fila, true) . "\n";
            }
        } else {
            echo "Tabla vacía\n";
        }
    }
} else {
    echo "No se encontró tabla de usuarios\n";
}
