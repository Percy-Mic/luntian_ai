<?php
// api/config.php - Luntian AI Production Configuration

// Check Vercel environment variables first; if not found, check defined constants
if (!defined('OPENAI_API_KEY')) {
    $env_key = getenv('OPENAI_API_KEY') ?: getenv('GROQ_API_KEY');
    if ($env_key) {
        define('OPENAI_API_KEY', $env_key);
    }
}

// Media storage reference path specifications
if (!defined('UPLOAD_DIR')) {
    define('UPLOAD_DIR', __DIR__ . '/../public/assets/uploads/');
}
if (!defined('UPLOAD_URL')) {
    define('UPLOAD_URL', '/assets/uploads/');
}<?php
// api/config.php - Luntian AI Production Configuration

// Check Vercel environment variables first; if not found, check defined constants
if (!defined('OPENAI_API_KEY')) {
    $env_key = getenv('OPENAI_API_KEY') ?: getenv('GROQ_API_KEY');
    if ($env_key) {
        define('OPENAI_API_KEY', $env_key);
    }
}

// Media storage reference path specifications
if (!defined('UPLOAD_DIR')) {
    define('UPLOAD_DIR', __DIR__ . '/../public/assets/uploads/');
}
if (!defined('UPLOAD_URL')) {
    define('UPLOAD_URL', '/assets/uploads/');
}
