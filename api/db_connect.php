<?php
// api/db_connect.php
header('Content-Type: application/json');

try {
    // 1. Fetch the absolute connection URL variable
    $dsn_url = getenv('DATABASE_URL');
    
    if (!$dsn_url) {
        throw new Exception("DATABASE_URL environment variable is missing on Vercel.");
    }
    
    // 2. Format connection syntax cleanly for standard PDO compliance
    // Maps standard database connection URLs (postgres:// user) to PHP Data Objects format
    $parsed_url = parse_url($dsn_url);
    
    if (!$parsed_url) {
        throw new Exception("Database URL string structural layout invalid.");
    }
    
    $host = $parsed_url['host'] ?? '';
    $port = $parsed_url['port'] ?? '5432';
    $user = $parsed_url['user'] ?? '';
    $pass = $parsed_url['pass'] ?? '';
    $dbname = ltrim($parsed_url['path'] ?? '', '/');
    
    // Construct uniform PostgreSQL PDO string format
    $dsn = "pgsql:host=$host;port=$port;dbname=$dbname;sslmode=require";
    
    // 3. Complete database instance handshake configuration
    $pdo = new PDO($dsn, $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);

} catch (Exception $e) {
    error_log("Production DB Handshake Failure: " . $e->getMessage());
    
    // Return explicit failure information safely formatted as JSON
    http_response_code(500);
    echo json_encode([
        'error' => 'Database connection failed.',
        'details' => $e->getMessage()
    ]);
    exit;
}
