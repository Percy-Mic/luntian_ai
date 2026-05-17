<?php
// api/db_connect.php
header('Content-Type: application/json');

try {
    // 1. Check for the unified connection URL string first (Vercel Production/Preview)
    $dsn_url = getenv('DATABASE_URL');
    
    if ($dsn_url) {
        // Parse connection URL string into components for standard PDO compliance
        $parsed_url = parse_url($dsn_url);
        if (!$parsed_url) {
            throw new Exception("Malformed DATABASE_URL environment string structural layout.");
        }
        
        $host   = $parsed_url['host'] ?? '';
        $port   = $parsed_url['port'] ?? '5432';
        $user   = $parsed_url['user'] ?? '';
        $pass   = $parsed_url['pass'] ?? '';
        $dbname = ltrim($parsed_url['path'] ?? '', '/');
        
        $dsn = "pgsql:host=$host;port=$port;dbname=$dbname;sslmode=require";
    } else {
        // 2. Fallback to individual variables if DATABASE_URL is missing (Localhost / All Environments)
        $host   = getenv('POSTGRES_HOST');
        $dbname = getenv('POSTGRES_DATABASE');
        $user   = getenv('POSTGRES_USER');
        $pass   = getenv('POSTGRES_PASSWORD');
        $port   = getenv('POSTGRES_PORT') ?: '18500'; // Default to Aiven port if unassigned
        
        if (!$host || !$dbname || !$user || !$pass) {
            throw new Exception("Database configuration incomplete. Missing both DATABASE_URL and discrete connection parameters.");
        }
        
        $dsn = "pgsql:host=$host;port=$port;dbname=$dbname;sslmode=require";
    }
    
    // 3. Initialize the PDO Instance Handshake securely
    $pdo = new PDO($dsn, $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);

} catch (Exception $e) {
    error_log("Database Connection Error: " . $e->getMessage());
    
    // Send standard structured JSON to avoid throwing raw strings that crash app.js
    http_response_code(500);
    echo json_encode([
        'error' => 'Database connection failed.',
        'details' => $e->getMessage()
    ]);
    exit;
}
