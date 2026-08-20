<?php
// api/aiResponse.php
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

    // 1. Parse Inbound Body JSON Safely
    $raw_input = file_get_contents('php://input');
    $input = json_decode($raw_input, true) ?? $_POST;
    
    $prompt = $input['prompt'] ?? ($input['message'] ?? '');
    $conversation_id = isset($input['conversation_id']) ? $input['conversation_id'] : null;

    if (empty($prompt)) {
        http_response_code(400);
        echo json_encode(['error' => 'Prompt text string cannot be empty.']);
        exit;
    }

    // 2. Validate/Create Conversation Thread safely
    if (!$conversation_id || strpos($conversation_id, 'temp-') === 0 || $conversation_id === 'null') {
        $conversation_id = create_conversation('Luntian Chat Thread');
        if (!$conversation_id) {
            throw new Exception("Database failed to initialize a new conversation ID row.");
        }
    } else {
        $conversation_id = intval($conversation_id);
    }

    // 3. Log Inbound Prompt into Messages Table
    $saved_user_msg = add_message($conversation_id, 'user', $prompt);
    if (!$saved_user_msg) {
        // Don't kill the app, just log it internally
        error_log("Database warning: Could not write user prompt to table.");
    }

    // 4. Construct AI System Message Array Stack
    $model_messages = [
        ['role' => 'system', 'content' => "You are Luntian AI, a helpful virtual assistant created by Percy Mic. Keep answers clean, conversational, and precise. Keep answers clean, conversational, and precise. IMPORTANT FORMATTING RULES: Never wrap full code examples or hello world programs inside Markdown tables. Always provide code blocks using standard ``` language tags outside of tables to preserve mobile readability."
    ];

    // Load history records matching table layout
$history = get_messages_for_conversation($conversation_id);

if (is_array($history)) {
    foreach ($history as $msg) {
        $text = $msg['message_text'] ?? ($msg['content'] ?? '');
        $role = $msg['role'] ?? 'user';
        if (!empty($text)) {
            $model_messages[] = ['role' => $role, 'content' => $text];
        }
    }
}

// Ensure the latest incoming user message isn't duplicated if already saved in history
$last_message = end($model_messages);
if (!$last_message || $last_message['content'] !== $prompt || $last_message['role'] !== 'user') {
    $model_messages[] = ['role' => 'user', 'content' => $prompt];
}

    // Double check that the current query is appended
    if (end($model_messages)['content'] !== $prompt) {
        $model_messages[] = ['role' => 'user', 'content' => $prompt];
    }

    $api_key = defined('OPENAI_API_KEY') ? OPENAI_API_KEY : null;
    if (empty($api_key)) {
        throw new Exception("Groq system API token key is missing or undefined inside config constants.");
    }

    // 5. Send Payload Package to Verified Groq API Link
    $ch = curl_init("https://api.groq.com/openai/v1/chat/completions");
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode([
        "model" => "openai/gpt-oss-120b",
        "messages" => $model_messages
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

    // 6. Log Outbound AI Reply to Database
    add_message($conversation_id, 'assistant', $reply);

    // Return payload format matching app.js expectations
    echo json_encode([
        'reply' => $reply,
        'conversation_id' => $conversation_id
    ]);

} catch (Throwable $t) {
    // Intercept engine cracks and output them as message details instead of crashing with a 500
    http_response_code(200); 
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
