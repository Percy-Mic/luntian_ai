<?php
try {
    $dsn = getenv('DATABASE_URL'); 
    if (!$dsn) {
        throw new Exception("DATABASE_URL is not set in Vercel.");
    }
    $pdo = new PDO($dsn);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (Exception $e) {
    error_log("DB Connection Error: " . $e->getMessage());
    die("Database connection failed. Check Vercel logs.");
}
