<?php
// api/test_groq.php
header('Content-Type: application/json');
ini_set('display_errors', 1);
error_reporting(E_ALL);

require_once __DIR__ . '/_config.php';

$api_key = defined('OPENAI_API_KEY') ? OPENAI_API_KEY : null;

echo "--- GROQ KEY DIAGNOSTICS ---\n\n";
echo "1. Reading Key Structure:\n";
if (empty($api_key)) {
    echo "❌ ERROR: No key found in environment variables. Check Vercel Dashboard!\n";
    exit;
} else {
    echo "✅ Key detected! Starts with: " . substr($api_key, 0, 7) . "...\n\n";
}

echo "2. Sending test request directly to Groq API...\n";

$url = "https://api.groq.com/openai/v1/chat/completions";
$data = [
    "model" => "llama-3.3-70b-versatile",
    "messages" => [
        ["role" => "user", "content" => "Respond with the single word 'Healthy'"]
    ]
];

$ch = curl_init($url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    'Content-Type: application/json',
    'Authorization: Bearer ' . $api_key
]);

$response = curl_exec($ch);
$http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

echo "3. Groq Server Response Code: " . $http_code . "\n\n";
echo "4. Raw Payload Return From Groq:\n";
echo $response ? $response : "❌ Absolutely no response data returned from cURL request.";
