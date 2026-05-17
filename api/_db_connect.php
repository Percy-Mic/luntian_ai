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

<?php
// api/_db_connect.php

if (!defined('DB_HOST')) {
    require_once __DIR__ . '/_config.php';
}

try {
    // CRITICAL FIX: Explicitly appending Aiven's required SSL query mode
    $dsn = "pgsql:host=" . DB_HOST . ";port=" . DB_PORT . ";dbname=" . DB_NAME . ";sslmode=require";
    
    // Instantiate PDO connection securely
    $pdo = new PDO($dsn, DB_USER, DB_PASS, [
        PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES   => false,
    ]);

} catch (PDOException $e) {
    // Log the actual system tracking message silently inside Vercel's terminal
    error_log("Luntian DB Connection Error: " . $e->getMessage());
    
    // Send a structured JSON response so app.js can catch it and display a clean error on screen
    header('Content-Type: application/json');
    echo json_encode([
        'error' => 'Hindi makakonekta sa structural database. Subukan muli mamaya.'
    ]);
    exit;
}
