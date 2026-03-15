<?php
// config.php — set these values for your environment
// IMPORTANT: do NOT commit this file with a real API key to public repos.

define('OPENAI_API_KEY', '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');

// Use Environment Variables for security
define('DB_HOST', 'POSTGRES_HOST');
define('DB_NAME', 'POSTGRES_DATABASE');
define('DB_USER', 'POSTGRES_USER');
define('DB_PASS', 'POSTGRES_PASSWORD');
define('DB_PORT', 'POSTGRES_PORT');

// Path where uploaded/generated assets live (web-accessible)
define('UPLOAD_DIR', __DIR__ . '/assets/uploads/');
define('UPLOAD_URL', '/assets/uploads/'); // adjust if app served under subfolder

// Create uploads dir if missing
if (!is_dir(UPLOAD_DIR)) {
    mkdir(UPLOAD_DIR, 0755, true);
}
