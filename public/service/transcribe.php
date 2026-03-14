<?php
// api/transcribe.php
header('Content-Type: application/json');
require_once __DIR__ . '/config.php';

if (!isset($_FILES['file'])) {
    echo json_encode(['error' => 'No audio file found']);
    exit;
}

$file_path = $_FILES['file']['tmp_name'];

$ch = curl_init("https://api.groq.com/openai/v1/audio/transcriptions");
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, [
    'file' => new CURLFile($file_path, $_FILES['file']['type'], 'audio.wav'),
    'model' => 'whisper-large-v3-turbo',
    'response_format' => 'json'
]);
curl_setopt($ch, CURLOPT_HTTPHEADER, ['Authorization: Bearer ' . OPENAI_API_KEY]);

$response = curl_exec($ch);
echo $response; // Returns {"text": "What the user said"}
curl_close($ch);