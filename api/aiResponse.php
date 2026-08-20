<?php
// Start output buffering immediately to catch rogue warnings/notices
ob_start();

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Headers: Content-Type, Authorization');
header('Access-Control-Allow-Methods: POST, OPTIONS');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    exit(0);
}

ini_set('display_errors', 0);
error_reporting(E_ALL);

try {
    require_once __DIR__ . '/_config.php';
    require_once __DIR__ . '/_db_connect.php';
    require_once __DIR__ . '/_chat_history.php';

    // Parse Input
    $raw_input = file_get_contents('php://input');
    $input = json_decode($raw_input, true) ?? $_POST;
    
    $prompt = trim($input['prompt'] ?? ($input['message'] ?? ''));
    $conversation_id = $input['conversation_id'] ?? null;

    if (empty($prompt)) {
        ob_clean();
        echo json_encode(['error' => 'Prompt text cannot be empty.']);
        exit;
    }

    // Validate/Create Conversation
    if (!$conversation_id || strpos((string)$conversation_id, 'temp-') === 0 || $conversation_id === 'null') {
        $conversation_id = create_conversation('Luntian Chat Thread');
        if (!$conversation_id) {
            throw new Exception("Database failed to initialize a new conversation ID row.");
        }
    } else {
        $conversation_id = intval($conversation_id);
    }

    // Save User Prompt
    add_message($conversation_id, 'user', $prompt);

    // System Prompt
    $model_messages = [
        [
            'role' => 'system', 
            'content' => "You are Luntian AI, a helpful virtual assistant created by Percy Mic. Keep answers clean, conversational, context-aware, and precise."
        ]
    ];

    // Load History
    $history = get_messages_for_conversation($conversation_id);
    if (is_array($history)) {
        foreach ($history as $msg) {
            $text = trim($msg['content'] ?? ($msg['message_text'] ?? ''));
            $raw_role = strtolower($msg['role'] ?? 'user');

            $role = ($raw_role === 'assistant' || $raw_role === 'bot' || $raw_role === 'ai') 
                ? 'assistant' 
                : 'user';

            if (!empty($text)) {
                $model_messages[] = ['role' => $role, 'content' => $text];
            }
        }
    }

    $api_key = defined('OPENAI_API_KEY') ? OPENAI_API_KEY : null;
    if (empty($api_key)) {
        throw new Exception("Groq API token key is missing or undefined inside config constants.");
    }

    // Send Request to Groq API
    $ch = curl_init("https://api.groq.com/openai/v1/chat/completions");
    curl_setopt_array($ch, [
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_POST => true,
        CURLOPT_POSTFIELDS => json_encode([
            "model" => "openai/gpt-oss-120b",
            "messages" => $model_messages,
            "temperature" => 0.7
        ]),
        CURLOPT_CONNECTTIMEOUT => 10,
        CURLOPT_TIMEOUT => 25,
        CURLOPT_HTTPHEADER => [
            'Content-Type: application/json',
            'Authorization: Bearer ' . $api_key
        ]
    ]);

    $response = curl_exec($ch);
    $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    if ($http_code !== 200) {
        $error_payload = json_decode($response, true);
        $err_msg = $error_payload['error']['message'] ?? "Server returned HTTP status code: " . $http_code;
        throw new Exception("Groq API Error: " . $err_msg);
    }

    $result_data = json_decode($response, true);
    $reply = $result_data['choices'][0]['message']['content'] ?? 'No response text generated.';

    // Save Assistant Response
    add_message($conversation_id, 'assistant', $reply);

    // Wipe any unexpected warnings before sending clean JSON
    ob_clean();
    echo json_encode([
        'reply' => $reply,
        'conversation_id' => $conversation_id
    ]);

} catch (Throwable $t) {
    ob_clean();
    echo json_encode([
        'error' => true,
        'reply' => "⚠️ Engine Sync Alert: " . $t->getMessage(),
        'debug_details' => [
            'file' => basename($t->getFile()),
            'line' => $t->getLine()
        ]
    ]);
    exit;
}
