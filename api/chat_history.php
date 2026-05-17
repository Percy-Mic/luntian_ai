<?php
// api/chat_history.php

/**
 * Creates a brand new conversation thread record in the database
 */
function create_conversation($title = 'Chat via web') {
    global $pdo;
    try {
        $stmt = $pdo->prepare("INSERT INTO conversations (title, created_at) VALUES (:title, NOW()) RETURNING id");
        $stmt->execute(['title' => $title]);
        
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result['id'] ?? $pdo->lastInsertId();
    } catch (Exception $e) {
        error_log("Failed to create conversation: " . $e->getMessage());
        throw new Exception("Database creation error: " . $e->getMessage());
    }
}

/**
 * Saves an explicit message row to a conversation thread
 */
function add_message($conversation_id, $role, $content) {
    global $pdo;
    try {
        // Checking if your PostgreSQL table uses 'content' or 'message_text'
        $query = "INSERT INTO messages (conversation_id, role, content, created_at) 
                  VALUES (:conversation_id, :role, :content, NOW())";
                  
        $stmt = $pdo->prepare($query);
        $stmt->execute([
            'conversation_id' => intval($conversation_id),
            'role' => $role,
            'content' => $content
        ]);
        return true;
    } catch (Exception $e) {
        error_log("Failed to save message: " . $e->getMessage());
        throw new Exception("Database save error: " . $e->getMessage());
    }
}

/**
 * Fetches historical chat dialogue sequences for LLM context injection
 */
function get_messages_for_conversation($conversation_id) {
    global $pdo;
    try {
        $stmt = $pdo->prepare("SELECT role, content FROM messages WHERE conversation_id = :conversation_id ORDER BY created_at ASC");
        $stmt->execute(['conversation_id' => intval($conversation_id)]);
        
        // FIX: Clean return statement with no stray characters or brackets outside the block
        return $stmt->fetchAll(PDO::FETCH_ASSOC) ?: [];
    } catch (Exception $e) {
        error_log("Failed to fetch message history: " . $e->getMessage());
        return [];
    }
}
