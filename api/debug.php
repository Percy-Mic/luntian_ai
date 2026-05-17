<?php
// api/debug.php
header('Content-Type: application/json');

// Force PHP to spit out every single error message explicitly
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

echo json_encode([
    'debug_status' => 'Starting Luntian AI diagnostic checks...',
    'timestamp' => date('Y-m-d H:i:s')
]);
echo "\n\n--- DIAGNOSTICS LOGS BEGIN ---\n\n";

try {
    echo "1. Testing file inclusions...\n";
    
    if (!file_exists(__DIR__ . '/_config.php')) {
        throw new Exception("File missing: _config.php is not found in " . __DIR__);
    }
    require_once __DIR__ . '/_config.php';
    echo "   ✅ _config.php loaded successfully.\n\n";

    echo "2. Testing environment variables load...\n";
    echo "   DB_HOST: " . (defined('DB_HOST') ? DB_HOST : 'NOT DEFINED') . "\n";
    echo "   DB_NAME: " . (defined('DB_NAME') ? DB_NAME : 'NOT DEFINED') . "\n";
    echo "   DB_PORT: " . (defined('DB_PORT') ? DB_PORT : 'NOT DEFINED') . "\n\n";

    echo "3. Testing database connection file integration...\n";
    if (!file_exists(__DIR__ . '/_db_connect.php')) {
        throw new Exception("File missing: _db_connect.php is not found in " . __DIR__);
    }
    require_once __DIR__ . '/_db_connect.php';
    echo "   ✅ _db_connect.php executed without crashing.\n";
    echo "   ✅ PDO Instance Status: " . (isset($pdo) ? "Connected and Active" : "Variable \$pdo not found") . "\n\n";

    echo "4. Testing chat history queries file layout...\n";
    if (!file_exists(__DIR__ . '/_chat_history.php')) {
        throw new Exception("File missing: _chat_history.php is not found in " . __DIR__);
    }
    require_once __DIR__ . '/_chat_history.php';
    echo "   ✅ _chat_history.php loaded successfully.\n\n";

    echo "5. Run test query to check database schemas...\n";
    if (isset($pdo)) {
        // Checking if tables exist or if column names throw errors
        $test_query = $pdo->query("SELECT * FROM conversations LIMIT 1");
        echo "   ✅ Database 'conversations' table found.\n";
        
        $test_query2 = $pdo->query("SELECT * FROM messages LIMIT 1");
        echo "   ✅ Database 'messages' table found.\n";
    }

    echo "\n🎉 ALL ENGINE SECTIONS PASSED BASIC SANITY CHECKS!";

} catch (Throwable $t) {
    echo "\n❌ FATAL ENGINE CRASH DETECTED!\n";
    echo "   Error Message: " . $t->getMessage() . "\n";
    echo "   File: " . $t->getFile() . "\n";
    echo "   Line: " . $t->getLine() . "\n";
}
