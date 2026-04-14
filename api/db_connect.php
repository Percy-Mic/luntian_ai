<?php
// includes/db_connect.php
require_once __DIR__ . '/config.php';

// Use the full string from Aiven: postgresql://user:pass@host:port/dbname?sslmode=require
$dsn = getenv('DATABASE_URL'); 

try {
    $pdo = new PDO($dsn);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    // This will now show up in your Vercel Logs
    error_log("Connection failed: " . $e->getMessage());
    exit;
}
