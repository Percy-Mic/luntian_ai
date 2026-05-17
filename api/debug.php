<?php
// api/debug.php
header('Content-Type: application/json');

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

echo "--- AUTHENTICATION TRACE ACTIVE ---\n\n";

require_once __DIR__ . '/_config.php';

echo "Database Host: " . (defined('DB_HOST') ? DB_HOST : 'NOT SET') . "\n";
echo "Database User: " . (defined('DB_USER') && DB_USER ? '✅ PRESENT (' . DB_USER . ')' : '❌ EMPTY/NOT FOUND') . "\n";
echo "Database Pass: " . (defined('DB_PASS') && DB_PASS ? '✅ PASSWORD DETECTED IN STORAGE RUNTIME' : '❌ EMPTY - PASSWORD IS MISSING') . "\n\n";

echo "--- INITIATING HANDSHAKE ---\n";
try {
    require_once __DIR__ . '/_db_connect.php';
    echo "🎉 SUCCESS: Database connection established perfectly!\n";
} catch (Throwable $t) {
    echo "❌ CONNECT ERROR: " . $t->getMessage() . "\n";
}
