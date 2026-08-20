<?php
// api/_chat_history.php

if (!isset($pdo)) {
    require_once __DIR__ . '/_db_connect.php';
}

/**
 * Initializes a new conversation row and returns the newly generated integer ID.
 */
function create_conversation($title = 'Luntian Chat Thread') {
    global $pdo;
    try {
        $stmt = $pdo->prepare("INSERT INTO conversations (title, created_at) VALUES (:title, NOW()) RETURNING id");
        $stmt->execute([':title' => $title]);
        return intval($stmt->fetchColumn());
    } catch (Exception $e) {
        error_log("Failed to create conversation: " . $e->getMessage());
        return null;
    }
}

/**
 * Fetches all past messages for a given thread sorted chronologically.
 * Supports both 'message_text' and 'content' DB column names as fallbacks.
 */
function get_messages_for_conversation($conversation_id) {
    global $pdo;
    try {
        // Query ordered strictly by id ASC to preserve exact conversation sequence
        $stmt = $pdo->prepare("
            SELECT role, 
                   COALESCE(message_text, content, '') AS message_text 
            FROM messages 
            WHERE conversation_id = :conversation_id 
            ORDER BY id ASC
        ");
        $stmt->execute([':conversation_id' => intval($conversation_id)]);
        
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $messages = [];
        
        if ($rows) {
            foreach ($rows as $row) {
                // Ensure valid OpenAI/Groq roles
                $raw_role = strtolower(trim($row['role']));
                $role = ($raw_role === 'assistant' || $raw_role === 'model' || $raw_role === 'bot') ? 'assistant' : 'user';

                if (!empty($row['message_text'])) {
                    $messages[] = [
                        'role'    => $role,
                        'content' => $row['message_text'],
                        'message_text' => $row['message_text'] // Duplicate key for backward compatibility
                    ];
                }
            }
        }
        return $messages;
        
    } catch (Exception $e) {
        error_log("Failed to fetch message history: " . $e->getMessage());
        return [];
    }
}

/**
 * Appends a new chat log entry using primary column 'message_text' and fallback column 'content'.
 */
function add_message($conversation_id, $role, $content) {
    global $pdo;
    try {
        // Normalizing role before storing
        $clean_role = (strtolower($role) === 'assistant' || strtolower($role) === 'bot') ? 'assistant' : 'user';

        // Check columns dynamically to prevent query crashes across schema versions
        $stmt = $pdo->prepare("
            INSERT INTO messages (conversation_id, role, message_text, created_at) 
            VALUES (:conversation_id, :role, :message_text, NOW())
        ");
        
        return $stmt->execute([
            ':conversation_id' => intval($conversation_id),
            ':role'            => $clean_role,
            ':message_text'    => $content
        ]);
    } catch (Exception $e) {
        // Secondary fallback insert if schema relies on 'content' instead of 'message_text'
        try {
            $fallback = $pdo->prepare("
                INSERT INTO messages (conversation_id, role, content, created_at) 
                VALUES (:conversation_id, :role, :content, NOW())
            ");
            return $fallback->execute([
                ':conversation_id' => intval($conversation_id),
                ':role'            => $clean_role,
                ':content'         => $content
            ]);
        } catch (Exception $fallback_err) {
            error_log("Failed to insert chat log message: " . $e->getMessage());
            return false;
        }
    }
}
