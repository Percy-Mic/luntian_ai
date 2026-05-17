<?php
try {
    $dsn = "postgres://avnadmin:<redacted>@pg-1a941fca-percymicnono-12e0.f.aivencloud.com:18500/defaultdb?sslmode=require"; 
    if (!$dsn) {
        throw new Exception("DATABASE_URL is not set in Vercel.");
    }
    $pdo = new PDO($dsn);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (Exception $e) {
    error_log("DB Connection Error: " . $e->getMessage());
    die("Database connection failed. Check Vercel logs.");
}
