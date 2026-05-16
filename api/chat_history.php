<?php
// chat_history.php
require_once __DIR__ . '/db_connect.php'; // Corrected __DIR__ usage and added slash

// Get messages for a conversation
function get_messages_for_conversation($conversation_id) {
    global $pdo; // Use $pdo from db_connect.php
    
    $stmt = $pdo->prepare("
        SELECT role, message_text AS content 
        FROM messages 
        WHERE conversation_id = ? 
        ORDER BY id ASC
    ");
    $stmt->execute([$conversation_id]);
    
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

// Add a message
function add_message($conversation_id, $role, $content) {
    global $pdo;
    
    $stmt = $pdo->prepare("
        INSERT INTO messages (conversation_id, role, message_text) 
        VALUES (?, ?, ?)
    ");
    $stmt->execute([$conversation_id, $role, $content]);
    
    return $pdo->lastInsertId();
}

// Create a conversation
function create_conversation($title = 'New Chat') {
    global $pdo;
    
    $stmt = $pdo->prepare("
        INSERT INTO conversations (title) 
        VALUES (?)
    ");
    $stmt->execute([$title]);
    
    return $pdo->lastInsertId();
}

// List all conversations
function list_conversations() {
    global $pdo;
    
    $stmt = $pdo->query("
        SELECT id, title, created_at 
        FROM conversations 
        ORDER BY created_at DESC
    ");
    
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}
