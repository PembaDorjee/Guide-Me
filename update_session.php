<?php
session_start();
header('Content-Type: application/json');

// Get the JSON data from the request
$data = json_decode(file_get_contents('php://input'), true);

// Update session with avatar path if provided
if (isset($data['avatar_path']) && !empty($data['avatar_path'])) {
    $_SESSION['avatar_path'] = $data['avatar_path'];
    echo json_encode(['success' => true, 'message' => 'Session updated']);
} else {
    echo json_encode(['success' => false, 'message' => 'No avatar path provided']);
}
?>