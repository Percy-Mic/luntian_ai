<?php
// api/aiResponse.php

header('Content-Type: application/json');

// Enable local error logging
ini_set('display_errors', 0);
error_reporting(E_ALL);

define('GROQ_API_KEY', getenv('GROQ_API_KEY') ?: 'YOUR_GROQ_API_KEY_HERE');

// Include DB Connection
require_once __DIR__ . '/../config/db.php'; 

// --- DATABASE HELPERS ---

function get_messages_for_conversation($pdo, $conversation_id) {
    if (!$conversation_id) return [];
    
    try {
        $stmt = $pdo->prepare("
            SELECT role, message_text 
            FROM messages 
            WHERE conversation_id = :cid 
            ORDER BY id ASC
        ");
        $stmt->execute([':cid' => $conversation_id]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC) ?: [];
    } catch (Exception $e) {
        error_log("DB Read Error: " . $e->getMessage());
        return [];
    }
}

function save_message($pdo, $conversation_id, $role, $text) {
    try {
        $stmt = $pdo->prepare("
            INSERT INTO messages (conversation_id, role, message_text) 
            VALUES (:cid, :role, :msg)
        ");
        return $stmt->execute([
            ':cid'  => $conversation_id,
            ':role' => $role,
            ':msg'  => $text
        ]);
    } catch (Exception $e) {
        error_log("DB Insert Error: " . $e->getMessage());
        return false;
    }
}

function create_new_conversation($pdo) {
    try {
        $stmt = $pdo->prepare("INSERT INTO conversations () VALUES ()");
        $stmt->execute();
        return $pdo->lastInsertId();
    } catch (Exception $e) {
        // Fallback if table requires values
        try {
            $stmt = $pdo->prepare("INSERT INTO conversations (id) VALUES (NULL)");
            $stmt->execute();
            return $pdo->lastInsertId();
        } catch (Exception $ex) {
            error_log("DB Conversation Create Error: " . $ex->getMessage());
            return null;
        }
    }
}

// --- MAIN EXECUTION ---

try {
    // FIX 1: Correct stream reader
    $rawInput = file_get_contents('php://input');
    $input = json_decode($rawInput, true) ?? $_POST;

    $prompt = trim($input['prompt'] ?? '');
    $conversation_id = $input['conversation_id'] ?? null;

    if (empty($prompt)) {
        http_response_code(400);
        echo json_encode(['error' => 'Prompt cannot be empty.']);
        exit;
    }

    if (!$conversation_id) {
        $conversation_id = create_new_conversation($pdo);
    }

    save_message($pdo, $conversation_id, 'user', $prompt);

    $model_messages = [
        [
            'role' => 'system',
            'content' => "You are Luntian AI, a helpful virtual assistant. Answer concisely and use standard Markdown formatting."
        ]
    ];

    $history = get_messages_for_conversation($pdo, $conversation_id);

    foreach ($history as $msg) {
        $text = trim($msg['message_text'] ?? '');
        $raw_role = strtolower($msg['role'] ?? 'user');
        
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

    // Call Groq API
    $api_key = trim(GROQ_API_KEY);
    $ch = curl_init("https://api.groq.com/openai/v1/chat/completions");
    curl_setopt_array($ch, [
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_POST => true,
        CURLOPT_POSTFIELDS => json_encode([
            "model" => "llama-3.3-70b-versatile",
            "messages" => $model_messages,
            "temperature" => 0.7
        ]),
        CURLOPT_CONNECTTIMEOUT => 10,
        CURLOPT_TIMEOUT => 30,
        CURLOPT_HTTPHEADER => [
            'Content-Type: application/json',
            'Authorization: Bearer ' . $api_key
        ]
    ]);

    $response = curl_exec($ch);
    $curl_error = curl_error($ch);
    $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    if ($curl_error) {
        throw new Exception("cURL Error: " . $curl_error);
    }

    $data = json_decode($response, true);

    if ($http_code !== 200 || !isset($data['choices'][0]['message']['content'])) {
        $msg = $data['error']['message'] ?? 'API Provider Failed';
        throw new Exception("Groq API Error ({$http_code}): " . $msg);
    }

    $ai_reply = $data['choices'][0]['message']['content'];

    save_message($pdo, $conversation_id, 'assistant', $ai_reply);

    echo json_encode([
        'reply' => $ai_reply,
        'conversation_id' => $conversation_id
    ]);

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'error' => 'Server Error',
        'details' => $e->getMessage()
    ]);
}
