<?php
// api/_db_connect.php

if (!defined('DB_HOST')) {
    require_once __DIR__ . '/_config.php';
}

try {
    // Aiven PostgreSQL requires sslmode=require
    $dsn = "pgsql:host=" . DB_HOST . ";port=" . DB_PORT . ";dbname=" . DB_NAME . ";sslmode=require";
    
    // Establishing PDO connection with options
    $pdo = new PDO($dsn, DB_USER, DB_PASS, [
        PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES   => false,
    ]);

} catch (PDOException $e) {
    // CRITICAL: We stop plain text from breaking the JSON format
    error_log("Database Connection Failure: " . $e->getMessage());
    
    // Send a clean json response so app.js knows exactly what happened
    header('Content-Type: application/json');
    echo json_encode([
        'error' => 'Hindi makakonekta sa database backend. Mangyaring subukan muli mamaya.'
    ]);
    exit;
}
