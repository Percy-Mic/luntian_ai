<?php
// includes/db_connect.php
require_once __DIR__ . '/config.php';

try {
    // Replace this with your actual Service URI from Aiven
    $service_uri = getenv('DATABASE_URL'); 
    $pdo = new PDO($service_uri);
} catch (PDOException $e) {
    echo "Connection error: " . $e->getMessage();
}
?>
