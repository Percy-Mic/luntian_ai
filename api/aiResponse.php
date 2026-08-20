<?php
// api/aiResponse.php

header('Content-Type: application/json');

// Enable error reporting for logs (Disable display in production)
ini_set('display_errors', 0);
error_reporting(E_ALL);

// Define API Key (Best practice: Load from environment or config file)
define('GROQ_API_KEY', getenv('GROQ_API_KEY') ?: 'YOUR_GROQ_API_KEY_HERE');

// Include your database connection ($pdo)
require_once __DIR__ . '/../config/db.php'; 

// --- DATABASE HELPER FUNCTIONS ---

/**
 * Fetch chat history for a specific conversation in chronological order
 */
function get_messages_for_conversation($pdo,$conversation_id) {
    if (!$conversation_id) return [];
    
    try {
        $stmt =$pdo->prepare("
            SELECT role, message_text 
            FROM messages 
            WHERE conversation_id = :cid 
            ORDER BY id ASC
        ");
        $stmt->execute([':cid' =>$conversation_id]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC) ?: [];
    } catch (Exception $e) {
        error_log("Failed to load chat history: " . $e->getMessage());
        return [];
    }
}

/**
 * Save a message turn (user or assistant) to the database
 */
function save_message($pdo,$conversation_id, $role,$text) {
    try {
        $stmt =$pdo->prepare("
            INSERT INTO messages (conversation_id, role, message_text, created_at) 
            VALUES (:cid, :role, :msg, NOW())
        ");
        $stmt->execute([
            ':cid'  => $conversation_id,
            ':role' => $role,
            ':msg'  => $text
        ]);
        return true;
    } catch (Exception $e) {
        error_log("Failed to save message: " . $e->getMessage());
        return false;
    }
}

/**
 * Create a new conversation record if none exists
 */
function create_new_conversation($pdo) {
    try {
        $stmt =$pdo->prepare("INSERT INTO conversations (created_at) VALUES (NOW())");
        $stmt->execute();
        return $pdo->lastInsertId();
    } catch (Exception $e) {
        error_log("Failed to create conversation: " . $e->getMessage());
        return null;
    }
}

// --- MAIN EXECUTION ---

// 1. Read Request Body
$rawInput = file_get_contents('php_input');$input = json_decode($rawInput, true) ?? $_POST;

$prompt = trim($input['prompt'] ?? '');
$conversation_id =$input['conversation_id'] ?? null;

if (empty($prompt)) {
    echo json_encode(['error' => 'Prompt cannot be empty.']);
    exit;
}

// 2. Ensure Conversation ID exists
if (!$conversation_id) {
    $conversation_id = create_new_conversation($pdo);
}

// 3. Save incoming user message to Database
save_message($pdo, $conversation_id, 'user',$prompt);

// 4. Construct System Prompt & Payload Stack
$model_messages = [
    [
        'role' => 'system',
        'content' => "You are Luntian AI, an engaging, helpful, and precise virtual assistant created by Percy Mic. " .
                     "Keep responses conversational, context-aware, and natural. " .
                     "IMPORTANT FORMATTING RULE: Never put full code blocks or multi-line code examples inside Markdown tables. " .
                     "Always output code snippets in standard triple-backtick (```) code blocks outside of tables."
    ]
];

// 5. Fetch Previous History & Map Roles for Groq API
$history = get_messages_for_conversation($pdo, $conversation_id);

foreach ($history as $msg) {
    $text = trim($msg['message_text'] ?? '');
    $raw_role = strtolower($msg['role'] ?? 'user');
    
    // Normalize DB roles to allowed Groq roles ('user' or 'assistant')
    $role = ($raw_role === 'assistant' || $raw_role === 'bot' || $raw_role === 'ai') 
        ? 'assistant' 
        : 'user';

    if (!empty($text)) {
        $model_messages[] = [
            'role' => $role,
            'content' => $text
        ];
    }
}

// 6. Send Request to Groq API using Active Model (openai/gpt-oss-120b)
$api_key = trim(GROQ_API_KEY);

$ch = curl_init("[https://api.groq.com/openai/v1/chat/completions](https://api.groq.com/openai/v1/chat/completions)");
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode([
    "model" => "openai/gpt-oss-120b",
    "messages" => $model_messages,
    "temperature" => 0.7
]));
curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 10);
curl_setopt($ch, CURLOPT_TIMEOUT, 30);
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    'Content-Type: application/json',
    'Authorization: Bearer ' . $api_key
]);

$response = curl_exec($ch);
$curl_error = curl_error($ch);
$http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

if ($curl_error) {
    error_log("cURL Error: " . $curl_error);
    echo json_encode(['error' => 'Failed to reach AI service.']);
    exit;
}

$data = json_decode($response, true);

if ($http_code !== 200 || !isset($data['choices'][0]['message']['content'])) {
    error_log("Groq API Error Response: " . $response);
    echo json_encode([
        'error' => 'AI generation failed.',
        'details' => $data['error']['message'] ?? 'Unknown error'
    ]);
    exit;
}

// 7. Extract AI Response
$ai_reply = $data['choices'][0]['message']['content'];

// 8. Save Assistant Response to DB
save_message($pdo, $conversation_id, 'assistant', $ai_reply);

// 9. Return JSON Response to Frontend
echo json_encode([
    'reply' => $ai_reply,
    'conversation_id' => $conversation_id
]);
