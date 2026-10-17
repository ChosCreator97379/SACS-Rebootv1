<?php
require_once __DIR__ . '/config/bootstrap.php';

use Sacs\Core\Database;

$db = Database::connection();

try {
    // Consultar estados únicos en f29_tramite
    $sql = "SELECT DISTINCT f29_estado FROM f29_tramite ORDER BY f29_estado";
    $stmt = $db->prepare($sql);
    $stmt->execute();
    $estados = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo "Estados encontrados en f29_tramite:\n";
    echo "===================================\n";
    foreach ($estados as $row) {
        echo "- " . $row['f29_estado'] . "\n";
    }

} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
