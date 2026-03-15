<?php
// includes/db_connect.php
require_once __DIR__ . '/../config.php';

try {
    $dsn = "pgsql:host=" . DB_HOST . ";port=" . DB_PORT . ";dbname=" . DB_NAME . ";sslmode=require";
    $pdo = new PDO($dsn, DB_USER, DB_PASS, [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]);
    // Connection successful!
} catch (PDOException $e) {
    error_log("Connection failed: " . $e->getMessage());
    die("Database error. Please try again later.");
}
