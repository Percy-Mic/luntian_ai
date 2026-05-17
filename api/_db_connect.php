<?php
// api/_db_connect.php

if (!defined('DB_HOST')) {
    require_once __DIR__ . '/_config.php';
}

try {
    // Check if configuration successfully populated the environment array variables
    if (DB_HOST === null || DB_NAME === null) {
        throw new Exception("Database configuration values are unassigned or failed to load from Vercel.");
    }

    // Aiven PostgreSQL strictly requires sslmode=require over TLS/SSL transit
    $dsn = "pgsql:host=" . DB_HOST . ";port=" . DB_PORT . ";dbname=" . DB_NAME . ";sslmode=require";
    
    $pdo = new PDO($dsn, DB_USER, DB_PASS, [
        PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES   => false,
    ]);

} catch (Throwable $e) {
    // Log the error silently to Vercel logs without breaking JSON formats
    error_log("Luntian Database Link Error: " . $e->getMessage());
    
    header('Content-Type: application/json');
    echo json_encode([
        'error' => 'Database connection configuration exception.',
        'details' => $e->getMessage()
    ]);
    exit;
}
