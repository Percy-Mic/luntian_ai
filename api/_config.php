<?php
// api/_config.php - Luntian AI Production Configuration

// 1. Securely check environment variables from Vercel's isolated environment
if (!defined('OPENAI_API_KEY')) {
    $env_key = getenv('GROQ_API_KEY') ?: ($_ENV['GROQ_API_KEY'] ?? ($_SERVER['GROQ_API_KEY'] ?? getenv('OPENAI_API_KEY')));
    define('OPENAI_API_KEY', $env_key);
}

// 2. Map Vercel System Database Keys with Comprehensive Scope Check
$host = getenv('POSTGRES_HOST') ?: ($_ENV['POSTGRES_HOST'] ?? ($_SERVER['POSTGRES_HOST'] ?? ''));
$name = getenv('POSTGRES_DATABASE') ?: ($_ENV['POSTGRES_DATABASE'] ?? ($_SERVER['POSTGRES_DATABASE'] ?? ''));
$user = getenv('POSTGRES_USER') ?: ($_ENV['POSTGRES_USER'] ?? ($_SERVER['POSTGRES_USER'] ?? ''));
$pass = getenv('POSTGRES_PASSWORD') ?: ($_ENV['POSTGRES_PASSWORD'] ?? ($_SERVER['POSTGRES_PASSWORD'] ?? ''));
$port = getenv('POSTGRES_PORT') ?: ($_ENV['POSTGRES_PORT'] ?? ($_SERVER['POSTGRES_PORT'] ?? '18500'));

// 3. Define fallback-checked core system constants
define('DB_HOST', !empty($host) ? $host : null);
define('DB_NAME', !empty($name) ? $name : null);
define('DB_USER', !empty($user) ? $user : null);
define('DB_PASS', !empty($pass) ? $pass : null);
define('DB_PORT', !empty($port) ? $port : '18500');

// 4. Media storage reference path specifications
if (!defined('UPLOAD_DIR')) {
    define('UPLOAD_DIR', __DIR__ . '/../public/assets/uploads/');
}
if (!defined('UPLOAD_URL')) {
    define('UPLOAD_URL', '/assets/uploads/');
}
