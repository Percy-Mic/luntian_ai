<?php
// api/aiResponse.php

// 1. Prevent buffer artifacts or notices from ruining JSON output
if (ob_get_length()) ob_clean();

ini_set('display_errors', 0);
error_reporting(E_ALL);

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Headers: Content-Type, Authorization');
header('Access-Control-Allow-Methods: POST, OPTIONS');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    exit(0);
}

try {
    require_once __DIR__ . '/_config.php';
    require_once __DIR__ . '/_db_connect.php';
    require_once __DIR__ . '/_chat_history.php';

    // 2. Parse Inbound Request Data
    $raw_input = file_get_contents('php://input');
    $input = json_decode($raw_input, true) ?? $_POST;
    
    $prompt = trim($input['prompt'] ?? ($input['message'] ?? ''));
    $conversation_id = isset($input['conversation_id']) ? $input['conversation_id'] : null;

    if (empty($prompt)) {
        http_response_code(400);
        echo json_encode(['error' => 'Prompt text string cannot be empty.']);
        exit;
    }

    // 3. Handle Conversation Persistence
    if (!$conversation_id || strpos($conversation_id, 'temp-') === 0 || $conversation_id === 'null') {
        $conversation_id = create_conversation('Luntian Chat Thread');
        if (!$conversation_id) {
            throw new Exception("Database failed to initialize a new conversation ID row.");
        }
    } else {
        $conversation_id = intval($conversation_id);
    }

    // 4. Base System Instruction Stack
    $model_messages = [
        [
            'role' => 'system',
            'content' => "You are Luntian AI, a helpful virtual assistant created by Percy Mic. Keep answers clean, conversational, and precise."
        ]
    ];

    // 5. Load History Records (Checks both message_text and content keys)
    $history = get_messages_for_conversation($conversation_id);

    if (is_array($history)) {
        foreach ($history as $msg) {
            // Support both standard DB field names
            $text = $msg['message_text'] ?? ($msg['content'] ?? '');
            
            // Map role accurately for OpenAI API standard ('user' vs 'assistant')
            $raw_role = strtolower($msg['role'] ?? 'user');
            $role = ($raw_role === 'assistant' || $raw_role === 'model' || $raw_role === 'bot') ? 'assistant' : 'user';

            if (!empty($text)) {
                $model_messages[] = [
                    'role' => $role,
                    'content' => $text
                ];
            }
        }
    }

    // 6. Append Incoming Prompt to DB and Payload Stack
    add_message($conversation_id, 'user', $prompt);
    $model_messages[] = [
        'role' => 'user',
        'content' => $prompt
    ];

    // 7. Verify API Key
    $api_key = defined('OPENAI_API_KEY') ? OPENAI_API_KEY : (getenv('OPENAI_API_KEY') ?: null);
    if (empty($api_key)) {
        throw new Exception("Groq system API token key is missing or undefined inside config constants.");
    }

    // 8. Execute Request to Groq Endpoint
    $ch = curl_init("https://api.groq.com/openai/v1/chat/completions");
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode([
        "model" => "openai/gpt-oss-120b",
        "messages" => array_values($model_messages)
    ]));
    curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 10);
    curl_setopt($ch, CURLOPT_TIMEOUT, 25);
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'Content-Type: application/json',
        'Authorization: Bearer ' . $api_key
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

    // 9. Save Assistant Response to Database
    add_message($conversation_id, 'assistant', $reply);

    // 10. Clean Return Payload
    if (ob_get_length()) ob_clean();
    echo json_encode([
        'reply' => $reply,
        'conversation_id' => $conversation_id
    ]);
    exit;

} catch (Throwable $t) {
    if (ob_get_length()) ob_clean();
    http_response_code(200);
    echo json_encode([
        'error' => true,
        'reply' => "⚠️ Engine Sync Alert: " . $t->getMessage(),
        'conversation_id' => $conversation_id ?? null,
        'debug_details' => [
            'file' => basename($t->getFile()),
            'line' => $t->getLine()
        ]
    ]);
    exit;
}
