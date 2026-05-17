<?php
try {
    $dsn = getenv('DATABASE_URL');
    if (!$dsn) {
        throw new Exception("DATABASE_URL environment variable is missing.");
    }
    
    $pdo = new PDO($dsn);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (Exception $e) {
    error_log("DB Connection Error: " . $e->getMessage());
    
    // Send a proper JSON header and message so app.js can catch it cleanly
    header('Content-Type: application/json');
    http_response_code(500);
    echo json_encode(['error' => 'Database connection failed. Control panel variables may be misconfigured.']);
    exit;
}
