<?php
// includes/chat_history.php
require_once _DIR_ . '/db_connect.php'; // Added a missing slash here

// Get messages for conversation
function get_messages_for_conversation($conversation_id) {
    global $pdo; // Use $pdo from your new db_connect.php
    
    // We used 'message_text' in our DBeaver script, so we use it here
    $stmt = $pdo->prepare("SELECT role, message_text as content FROM messages WHERE conversation_id = ? ORDER BY id ASC");
    $stmt->execute([$conversation_id]);
    
    return $stmt->fetchAll();
}

// Add a message
function add_message($conversation_id, $role, $content) {
    global $pdo;
    
    $stmt = $pdo->prepare("INSERT INTO messages (conversation_id, role, message_text) VALUES (?, ?, ?)");
    $stmt->execute([$conversation_id, $role, $content]);
    
    return $pdo->lastInsertId();
}

// Create a conversation
function create_conversation($title = 'New Chat') {
    global $pdo;
    
    // Note: We didn't include a 'user_id' column in our initial Postgres script
    $stmt = $pdo->prepare("INSERT INTO conversations (title) VALUES (?)");
    $stmt->execute([$title]);
    
    return $pdo->lastInsertId();
}

// List all conversations
function list_conversations() {
    global $pdo;
    
    $stmt = $pdo->query("SELECT id, title, created_at FROM conversations ORDER BY created_at DESC");
    return $stmt->fetchAll();
}
