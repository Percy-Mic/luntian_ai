<?php
// api/chat.php
header('Content-Type: application/json');

require_once __DIR__ . 'config.php';
require_once __DIR__ . 'db_connect.php';
require_once __DIR__ . 'chat_history.php';

// Parse Input
$input = json_decode(file_get_contents('php://input'), true) ?? $_POST;
$prompt = $input['prompt'] ?? ($input['message'] ?? '');
$conversation_id = isset($input['conversation_id']) ? intval($input['conversation_id']) : null;

if (!$conversation_id) {

    $conversation_id = create_conversation(1, 'Chat via web');

}



add_message($conversation_id, 'user', $prompt, 'text');



// Prepare Messages for Groq

$model_messages = [];

$model_messages[] = [

  'role' => 'system',

  'content' => "You are Luntian, a friendly assistant developed by Percy Mic. Be concise and helpful."

];



$history = get_messages_for_conversation($conversation_id);

foreach ($history as $m) {

    $model_messages[] = ['role' => $m['role'], 'content' => $m['content']];

}



$url = "https://api.groq.com/openai/v1/chat/completions";

$data = [

    "model" => "llama-3.3-70b-versatile",

    "messages" => $model_messages

];



$ch = curl_init($url);



// --- CONNECTION FIXES ---

curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

curl_setopt($ch, CURLOPT_POST, true);

curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));



// 1. Force IPv4 (Prevents many 10054 errors on local machines)

curl_setopt($ch, CURLOPT_IPRESOLVE, CURL_IPRESOLVE_V4);



// 2. Disable SSL Verification (Since you're on Localhost)

curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);



// 3. Set standard timeouts

curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 10);

curl_setopt($ch, CURLOPT_TIMEOUT, 30);



// 4. Headers (Added 'Expect:' to prevent issues with larger payloads)

curl_setopt($ch, CURLOPT_HTTPHEADER, [

    'Content-Type: application/json',

    'Authorization: Bearer ' . OPENAI_API_KEY,

    'Expect:'

]);



$response = curl_exec($ch);



if ($response === false) {

    $err = curl_error($ch);

    echo json_encode(['error' => 'Connection failed', 'curl_error' => $err]);

    curl_close($ch);

    exit;

}



$code = curl_getinfo($ch, CURLINFO_HTTP_CODE);

curl_close($ch);



if ($code !== 200) {

    http_response_code($code);

    echo json_encode(['error' => 'Groq API error', 'status_code' => $code, 'details' => json_decode($response, true)]);

    exit;

}



$res = json_decode($response, true);

$assistant_text = $res['choices'][0]['message']['content'] ?? 'No response.';



add_message($conversation_id, 'assistant', $assistant_text, 'text', $res);



echo json_encode(['reply' => $assistant_text, 'conversation_id' => $conversation_id]);
