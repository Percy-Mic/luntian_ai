<?php
// api/aiResponse.php
header('Content-Type: application/json');

try {
    // Relative requirements based on your repository folder layout
    require_once __DIR__ . '/config.php';
    require_once __DIR__ . '/db_connect.php';
    require_once __DIR__ . '/chat_history.php';

    // Parse Input safely
    $input = json_decode(file_get_contents('php://input'), true) ?? $_POST;
    $prompt = $input['prompt'] ?? ($input['message'] ?? '');
    $conversation_id = isset($input['conversation_id']) ? $input['conversation_id'] : null;

    // Filter out temporary string IDs from localStorage
    if (!$conversation_id || strpos($conversation_id, 'temp-') === 0) {
        $conversation_id = create_conversation('Chat via web');
    } else {
        $conversation_id = intval($conversation_id);
    }

    if (empty($prompt)) {
        throw new Exception("Prompt text cannot be blank.");
    }

    // Save user message to database
    add_message($conversation_id, 'user', $prompt);

    // Prepare Messages Context History Layer for Groq
    $model_messages = [];
    $model_messages[] = [
        'role' => 'system',
        'content' => "You are Luntian, a friendly assistant developed by Percy Mic. Be concise and helpful."
    ];

    $history = get_messages_for_conversation($conversation_id);
    foreach ($history as $m) {
        // Safe check: handles database columns named either 'content' or 'message_text'
        $text_content = $m['message_text'] ?? ($m['content'] ?? '');
        if (!empty($text_content)) {
            $model_messages[] = [
                'role' => $m['role'], 
                'content' => $text_content
            ];
        }
    }

    // Append current prompt context if not duplicated in history query
    if (end($model_messages)['content'] !== $prompt) {
        $model_messages[] = ['role' => 'user', 'content' => $prompt];
    }

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

    // Dynamic environment handling: Allow unsecured peer connections ONLY on localhost
    if (isset($_SERVER['HTTP_HOST']) && (strpos($_SERVER['HTTP_HOST'], 'localhost') !== false || strpos($_SERVER['HTTP_HOST'], '127.0.0.1') !== false)) {
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
    }

    curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 10);
    curl_setopt($ch, CURLOPT_TIMEOUT, 30);

    // Verify your Groq/OpenAI key variable constant configuration name
    $api_key = defined('OPENAI_API_KEY') ? OPENAI_API_KEY : getenv('OPENAI_API_KEY');

    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'Content-Type: application/json',
        'Authorization: Bearer ' . $api_key,
        'Expect:'
    ]);

    $response = curl_exec($ch);

    if ($response === false) {
        $err = curl_error($ch);
        curl_close($ch);
        throw new Exception("Groq API cURL call connection failure: " . $err);
    }

    $code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    if ($code !== 200) {
        http_response_code($code);
        echo json_encode([
            'error' => 'Groq API interface rejected request payload.',
            'status_code' => $code,
            'details' => json_decode($response, true)
        ]);
        exit;
    }

    $res = json_decode($response, true);
    $assistant_text = $res['choices'][0]['message']['content'] ?? 'No response returned from model framework context.';

    // Save assistant text to database
    add_message($conversation_id, 'assistant', $assistant_text);

    // Clean execution output array transmission
    echo json_encode([
        'reply' => $assistant_text, 
        'conversation_id' => $conversation_id
    ]);

} catch (Throwable $t) {
    // Keeps JSON output clean even on deep structural failures
    http_response_code(500);
    echo json_encode([
        'error' => 'Backend script runtime exception encountered.',
        'details' => $t->getMessage()
    ]);
    exit;
}
