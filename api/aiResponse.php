<?php
// api/aiResponse.php
header('Content-Type: application/json');

// Ensure error tracking logs silently instead of outputting plain text
ini_set('display_errors', 0);
error_reporting(E_ALL);

try {
    // 1. Establish Core Framework Requirements
    require_once __DIR__ . '/_config.php';
    require_once __DIR__ . '/_db_connect.php';
    require_once __DIR__ . '/_chat_history.php';

    // 2. Parse Input Securely
    $input = json_decode(file_get_contents('php://input'), true) ?? $_POST;
    $prompt = $input['prompt'] ?? ($input['message'] ?? '');
    $conversation_id = isset($input['conversation_id']) ? $input['conversation_id'] : null;

    // Filter out temporary client-side IDs from localStorage
    if (!$conversation_id || strpos($conversation_id, 'temp-') === 0) {
        $conversation_id = create_conversation('Chat via web');
    } else {
        $conversation_id = intval($conversation_id);
    }

    if (empty($prompt)) {
        http_response_code(400);
        echo json_encode(['error' => 'Prompt text field cannot be completely blank.']);
        exit;
    }

    // 3. Document Inbound Message Context to Database
    add_message($conversation_id, 'user', $prompt);

    // 4. Structure Groq LLM Chat Array
    $model_messages = [];
    $model_messages[] = [
        'role' => 'system',
        'content' => "You are Luntian, a friendly assistant developed by Percy Mic. Be concise and helpful."
    ];

    // Build historical context safely matching schema layouts
    $history = get_messages_for_conversation($conversation_id);
    if (is_array($history)) {
        foreach ($history as $m) {
            $text_content = $m['content'] ?? ($m['message_text'] ?? '');
            if (!empty($text_content)) {
                $model_messages[] = [
                    'role' => $m['role'], 
                    'content' => $text_content
                ];
            }
        }
    }

    // Ensure immediate current prompt is evaluated inside the context payload array
    if (empty($model_messages) || end($model_messages)['content'] !== $prompt) {
        $model_messages[] = ['role' => 'user', 'content' => $prompt];
    }

    // 5. Initialize Groq AI Endpoint Payload Processing
    $url = "https://api.groq.com/openai/v1/chat/completions";
    $data = [
        "model" => "llama-3.3-70b-versatile",
        "messages" => $model_messages
    ];

    // Securely pull API keys via custom environmental check structures
    $api_key = defined('OPENAI_API_KEY') ? OPENAI_API_KEY : null;

    if (empty($api_key)) {
        // Trap empty keys before initiating cURL to prevent generic 500 server crashes
        http_response_code(401);
        echo json_encode([
            'error' => 'Configuration Error',
            'details' => 'Groq system access key token is missing or unresolved from host environment variables.'
        ]);
        exit;
    }

    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
    curl_setopt($ch, CURLOPT_IPRESOLVE, CURL_IPRESOLVE_V4);

    // Handle Local Machine Test Environments safely
    if (isset($_SERVER['HTTP_HOST']) && (strpos($_SERVER['HTTP_HOST'], 'localhost') !== false || strpos($_SERVER['HTTP_HOST'], '127.0.0.1') !== false)) {
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
    }

    curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 10);
    curl_setopt($ch, CURLOPT_TIMEOUT, 30);
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'Content-Type: application/json',
        'Authorization: Bearer ' . $api_key,
        'Expect:'
    ]);

    $response = curl_exec($ch);

    if ($response === false) {
        $err = curl_error($ch);
        curl_close($ch);
        http_response_code(502);
        echo json_encode([
            'error' => 'Gateway Connection Timeout',
            'details' => 'Groq API backend infrastructure transport failed: ' . $err
        ]);
        exit;
    }

    $code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    if ($code !== 200) {
        // If Groq rejects your API key, this passes the explicit reason to app.js
        http_response_code($code);
        echo json_encode([
            'error' => 'Groq execution endpoint rejected package structure.',
            'status_code' => $code,
            'details' => json_decode($response, true)
        ]);
        exit;
    }

    // 6. Output Generation Parsing and Database Syncing
    $res = json_decode($response, true);
    $assistant_text = $res['choices'][0]['message']['content'] ?? 'No text generated.';

    add_message($conversation_id, 'assistant', $assistant_text);

    // Clean JSON response for your JavaScript layer
    echo json_encode([
        'reply' => $assistant_text, 
        'conversation_id' => $conversation_id
    ]);

} catch (Throwable $t) {
    // Structural catch block prevents unhandled runtime exceptions from leaking plain text
    http_response_code(500);
    echo json_encode([
        'error' => 'Internal server processing exception generated.',
        'details' => $t->getMessage(),
        'file' => $t->getFile(),
        'line' => $t->getLine()
    ]);
    exit;
}
