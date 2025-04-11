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

// Get role from query parameter
$role = isset($_GET['role']) ? $_GET['role'] : '';

try {
    if ($role === 'guide') {
        // For guides, only show active guides in the Our Guides tab
        $query = "SELECT guide_id as id, full_name as name, email, phone, 
                  avatar_path, experience, specialization, bio,
                  created_at, status FROM Guide WHERE status = 'active'
                  ORDER BY created_at DESC";
        
        $result = $conn->query($query);
        
        if (!$result) {
            throw new Exception("Error executing query: " . $conn->error);
        }
        
        $users = [];
        while ($row = $result->fetch_assoc()) {
            $users[] = $row;
        }
        
        echo json_encode($users);
    } else if ($role === 'client') {
        // For clients, show all clients
        $query = "SELECT client_id as id, full_name as name, email, phone, 
                  avatar_path, created_at, status FROM Client
                  ORDER BY created_at DESC";
        
        $result = $conn->query($query);
        
        if (!$result) {
            throw new Exception("Error executing query: " . $conn->error);
        }
        
        $users = [];
        while ($row = $result->fetch_assoc()) {
            $users[] = $row;
        }
        
        echo json_encode($users);
    } else {
        // Invalid role
        http_response_code(400);
        echo json_encode(['error' => 'Invalid role']);
    }
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['error' => $e->getMessage()]);
    // Log the error for debugging
    error_log("Error in get_users.php: " . $e->getMessage());
}

$conn->close();
?>