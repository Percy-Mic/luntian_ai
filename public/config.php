<?php
// config.php — set these values for your environment
// IMPORTANT: do NOT commit this file with a real API key to public repos.

define('OPENAI_API_KEY', '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');
define('DB_HOST', 'sql308.infinityfree.com');
define('DB_NAME', 'if0_41381496_luntian_ai');
define('DB_USER', 'if0_41381496');
define('DB_PASS', '102006Pmpn1'); // set your DB password

// Path where uploaded/generated assets live (web-accessible)
define('UPLOAD_DIR', __DIR__ . '/assets/uploads/');
define('UPLOAD_URL', '/assets/uploads/'); // adjust if app served under subfolder

// Create uploads dir if missing
if (!is_dir(UPLOAD_DIR)) {
    mkdir(UPLOAD_DIR, 0755, true);
}
