<?php
require_once 'config.php';

try {
    // This pulls the full postgresql:// URI from Vercel
    $dsn = getenv('DATABASE_URL'); 
    $pdo = new PDO($dsn);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    error_log("Connection Error: " . $e->getMessage());
    die("Connection failed.");
}
