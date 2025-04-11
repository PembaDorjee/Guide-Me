<?php
// Include database connection
include '../database.php';

// Enable error reporting for debugging but capture it instead of displaying
ini_set('display_errors', 0);
error_reporting(E_ALL);

// Set headers for JSON response
header('Content-Type: application/json');

// Check if the request is POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
    exit;
}

// Check if client_id is provided
if (!isset($_POST['client_id']) || empty($_POST['client_id'])) {
    echo json_encode(['success' => false, 'message' => 'Client ID is required']);
    exit;
}

$clientId = intval($_POST['client_id']);
$role = isset($_POST['role']) ? $_POST['role'] : 'client';

// Log received data for debugging
error_log("Attempting to delete client with ID: $clientId, Role: $role");

try {
    // Check if connection is valid
    if (!$conn || $conn->connect_error) {
        throw new Exception("Database connection failed: " . ($conn ? $conn->connect_error : "Connection object is null"));
    }
    
    // First, check if client exists
    $checkStmt = $conn->prepare("SELECT client_id FROM Client WHERE client_id = ?");
    if (!$checkStmt) {
        throw new Exception("Prepare statement error: " . $conn->error);
    }
    
    $checkStmt->bind_param("i", $clientId);
    $checkStmt->execute();
    $result = $checkStmt->get_result();
    
    if ($result->num_rows === 0) {
        // Log the query for debugging
        error_log("Client not found with ID: $clientId");
        echo json_encode(['success' => false, 'message' => 'Client not found']);
        exit;
    }
    
    // Delete the client
    $stmt = $conn->prepare("DELETE FROM Client WHERE client_id = ?");
    if (!$stmt) {
        throw new Exception("Prepare statement error: " . $conn->error);
    }
    
    $stmt->bind_param("i", $clientId);
    $stmt->execute();
    
    if ($stmt->affected_rows > 0) {
        echo json_encode(['success' => true, 'message' => 'Client deleted successfully']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Client could not be deleted: ' . $conn->error]);
    }
} catch (Exception $e) {
    error_log("Error in delete_user.php: " . $e->getMessage());
    echo json_encode(['success' => false, 'message' => 'Database error: ' . $e->getMessage()]);
}

// Close connection
if ($conn) {
    $conn->close();
}
?>