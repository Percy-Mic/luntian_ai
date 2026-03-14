<?php
// includes/chat_history.php
require_once __DIR__ . '/db_connect.php';

// get messages for conversation (returns array of ['role'=>'user'|'assistant','content'=>...])
function get_messages_for_conversation($conversation_id) {
    global $mysqli;
    $stmt = $mysqli->prepare("SELECT role, content, content_type, extra FROM messages WHERE conversation_id = ? ORDER BY id ASC");
    $stmt->bind_param('i', $conversation_id);
    $stmt->execute();
    $res = $stmt->get_result();
    $out = [];
    while ($row = $res->fetch_assoc()) {
        $extra = $row['extra'] ? json_decode($row['extra'], true) : null;
        $out[] = [
            'role' => $row['role'],
            'content' => $row['content'],
            'content_type' => $row['content_type'],
            'extra' => $extra
        ];
    }
    return $out;
}

function add_message($conversation_id, $role, $content, $content_type = 'text', $extra = null) {
    global $mysqli;
    $jsonExtra = $extra ? json_encode($extra, JSON_UNESCAPED_UNICODE) : null;
    $stmt = $mysqli->prepare("INSERT INTO messages (conversation_id, role, content, content_type, extra) VALUES (?, ?, ?, ?, ?)");
    $stmt->bind_param('issss', $conversation_id, $role, $content, $content_type, $jsonExtra);
    $stmt->execute();
    return $mysqli->insert_id;
}

function create_conversation($user_id = 1, $title = 'New Chat') {
    global $mysqli;
    $stmt = $mysqli->prepare("INSERT INTO conversations (user_id, title) VALUES (?, ?)");
    $stmt->bind_param('is', $user_id, $title);
    $stmt->execute();
    return $mysqli->insert_id;
}

function list_conversations($user_id = 1) {
    global $mysqli;
    $stmt = $mysqli->prepare("SELECT id, title, created_at FROM conversations WHERE user_id = ? ORDER BY created_at DESC");
    $stmt->bind_param('i', $user_id);
    $stmt->execute();
    $res = $stmt->get_result();
    $out = [];
    while ($row = $res->fetch_assoc()) $out[] = $row;
    return $out;
}
