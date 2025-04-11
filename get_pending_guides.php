<?php
session_start();
require_once '../database.php';
header('Content-Type: application/json');

// Check if user is logged in and is admin
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    http_response_code(401);
    echo json_encode(['error' => 'Unauthorized']);
    exit();
}

try {
    // Query for guides with pending status
    $query = "SELECT guide_id as id, full_name as name, email, phone, 
              avatar_path, experience, specialization, bio,
              created_at, status FROM Guide WHERE status = 'pending'
              ORDER BY created_at DESC";
    
    $result = $conn->query($query);
    
    if (!$result) {
        throw new Exception("Error executing query: " . $conn->error);
    }
    
    $guides = [];
    while ($row = $result->fetch_assoc()) {
        $guides[] = $row;
    }
    
    echo json_encode($guides);
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['error' => $e->getMessage()]);
}

$conn->close();
?>