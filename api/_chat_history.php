<?php
// api/_chat_history.php

if (!isset($pdo)) {
    require_once __DIR__ . '/_db_connect.php';
}

/**
 * Creates a new conversation thread in the database.
 */
function create_conversation($title = 'New Chat') {
    global $pdo;
    try {
        $stmt = $pdo->prepare("INSERT INTO conversations (title, created_at) VALUES (:title, NOW()) RETURNING id");
        $stmt->execute([':title' => $title]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result ? $result['id'] : null;
    } catch (Exception $e) {
        error_log("Failed to create conversation: " . $e->getMessage());
        return null;
    }
}

/**
 * Fetches history records using your actual 'message_text' column layout
 */
function get_messages_for_conversation($conversation_id) {
    global $pdo;
    try {
        // Querying message_text from your schema
        $stmt = $pdo->prepare("SELECT role, message_text FROM messages WHERE conversation_id = :conversation_id ORDER BY created_at ASC");
        $stmt->execute([':conversation_id' => intval($conversation_id)]);
        
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $messages = [];
        
        if ($rows) {
            foreach ($rows as $row) {
                $messages[] = [
                    'role'    => $row['role'],
                    'content' => $row['message_text'] // Maps message_text cleanly over to Groq's required 'content' format
                ];
            }
        }
        return $messages;
        
    } catch (Exception $e) {
        error_log("Failed to fetch message history: " . $e->getMessage());
        return [];
    }
}

/**
 * Appends a new chat log entry into your true table schema using 'message_text'
 */
function add_message($conversation_id, $role, $content) {
    global $pdo;
    try {
        // Changed column name from 'content' to 'message_text' to match your schema perfectly
        $stmt = $pdo->prepare("INSERT INTO messages (conversation_id, role, message_text, created_at) VALUES (:conversation_id, :role, :message_text, NOW())");
        return $stmt->execute([
            ':conversation_id' => intval($conversation_id),
            ':role'            => $role,
            ':message_text'    => $content
        ]);
    } catch (Exception $e) {
        // This will write the exact SQL string breakdown to your Vercel logs if it slips up
        error_log("Failed to insert chat log message: " . $e->getMessage());
        return false;
    }
}
