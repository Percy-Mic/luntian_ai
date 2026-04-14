<?php
// config.php — Luntian AI Configuration

// Fetching Environment Variables from Vercel
define('OPENAI_API_KEY', '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');

define('DB_HOST', getenv('POSTGRES_HOST'));
define('DB_NAME', getenv('POSTGRES_DATABASE'));
define('DB_USER', getenv('POSTGRES_USER'));
define('DB_PASS', getenv('POSTGRES_PASSWORD'));
define('DB_PORT', getenv('POSTGRES_PORT') ?: '18500');

// Path where uploaded/generated assets live (web-accessible)
define('UPLOAD_DIR', __DIR__ . '/assets/uploads/');
define('UPLOAD_URL', '/assets/uploads/');
