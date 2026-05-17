<?php
// api/aiResponse.php
header('Content-Type: application/json');

try {
    // 1. Establish Core Framework Requirements
    header('Content-Type: application/json');

    // Pulling internal configurations using Vercel's private file formatting
    require_once __DIR__ . '/_config.php';
    require_once __DIR__ . '/_db_connect.php';
    require_once __DIR__ . '/_chat_history.php';

    // Parse Input
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
        throw new Exception("Prompt text field cannot be completely blank.");
    }

    // 3. Document Inbound Message Context to Database
    add_message($conversation_id, 'user', $prompt);

    // 4. Structure Groq LLM Chat Array
    $model_messages = [];
    $model_messages[] = [
        'role' => 'system',
        'content' => "You are Luntian, a friendly assistant developed by Percy Mic. Be concise and helpful."
    ];

    // Build historical depth layers
    $history = get_messages_for_conversation($conversation_id);
    foreach ($history as $m) {
        // Fallback catch for schema row variations ('message_text' or 'content')
        $text_content = $m['message_text'] ?? ($m['content'] ?? '');
        if (!empty($text_content)) {
            $model_messages[] = [
                'role' => $m['role'], 
                'content' => $text_content
            ];
        }
    }

    // Make sure the immediate current prompt is inside the context payload array
    if (empty($model_messages) || end($model_messages)['content'] !== $prompt) {
        $model_messages[] = ['role' => 'user', 'content' => $prompt];
    }

    // 5. Initialize Groq AI Endpoint Payload Processing
    $url = "https://api.groq.com/openai/v1/chat/completions";
    $data = [
        "model" => "llama-3.3-70b-versatile",
        "messages" => $model_messages
    ];

    $ch = curl_init($url);

    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
    curl_setopt($ch, CURLOPT_IPRESOLVE, CURL_IPRESOLVE_V4);

    // Manage Local Machine Testing Environments
    if (isset($_SERVER['HTTP_HOST']) && (strpos($_SERVER['HTTP_HOST'], 'localhost') !== false || strpos($_SERVER['HTTP_HOST'], '127.0.0.1') !== false)) {
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
    }

    curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 10);
    curl_setopt($ch, CURLOPT_TIMEOUT, 30);

    // Securely pull API secret keys via Vercel Environment Context Variables
    $api_key = defined('OPENAI_API_KEY') ? OPENAI_API_KEY : (getenv('OPENAI_API_KEY') ?: getenv('GROQ_API_KEY'));

    if (empty($api_key)) {
        throw new Exception("Groq system access key token is missing from host variables engine.");
    }

    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'Content-Type: application/json',
        'Authorization: Bearer ' . $api_key,
        'Expect:'
    ]);

    $response = curl_exec($ch);

    if ($response === false) {
        $err = curl_error($ch);
        curl_close($ch);
        throw new Exception("Groq API backend infrastructure transport failed: " . $err);
    }

    $code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    if ($code !== 200) {
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

    // Structured presentation return
    echo json_encode([
        'reply' => $assistant_text, 
        'conversation_id' => $conversation_id
    ]);

} catch (Throwable $t) {
    // Catch-all to trap breaks inside JSON blocks to stop frontend interface syntax parse crashes
    http_response_code(500);
    echo json_encode([
        'error' => 'Internal server processing exception generated.',
        'details' => $t->getMessage()
    ]);
    exit;
}
