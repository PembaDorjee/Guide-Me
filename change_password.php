<?php
session_start();
header('Content-Type: application/json');
require_once '../database.php';

// Check if user is logged in
if (!isset($_SESSION['client_id'])) {
    echo json_encode(['success' => false, 'message' => 'Not logged in']);
    exit;
}

// Check if required fields are set
if (!isset($_POST['current_password']) || !isset($_POST['new_password']) || !isset($_POST['confirm_password'])) {
    echo json_encode(['success' => false, 'message' => 'Missing required fields']);
    exit;
}

$client_id = $_SESSION['client_id'];
$current_password = $_POST['current_password'];
$new_password = $_POST['new_password'];
$confirm_password = $_POST['confirm_password'];

// Validate inputs
if (empty($current_password) || empty($new_password) || empty($confirm_password)) {
    echo json_encode(['success' => false, 'message' => 'All fields are required']);
    exit;
}

if ($new_password !== $confirm_password) {
    echo json_encode(['success' => false, 'message' => 'New passwords do not match']);
    exit;
}

try {
    // Check if we're using mysqli or PDO
    if (isset($conn)) {
        // First verify current password
        $stmt = $conn->prepare("SELECT password FROM Client WHERE client_id = ?");
        $stmt->bind_param("i", $client_id);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result && $result->num_rows > 0) {
            $user = $result->fetch_assoc();
            
            // Verify current password
            if (!password_verify($current_password, $user['password'])) {
                echo json_encode(['success' => false, 'message' => 'Current password is incorrect']);
                exit;
            }
            
            // Hash new password
            $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);
            
            // Update password
            $update_stmt = $conn->prepare("UPDATE Client SET password = ? WHERE client_id = ?");
            $update_stmt->bind_param("si", $hashed_password, $client_id);
            $success = $update_stmt->execute();
            
            if ($success) {
                echo json_encode(['success' => true, 'message' => 'Password updated successfully']);
            } else {
                echo json_encode(['success' => false, 'message' => 'Failed to update password: ' . $conn->error]);
            }
            
            $update_stmt->close();
        } else {
            echo json_encode(['success' => false, 'message' => 'User not found']);
        }
        
        $stmt->close();
    } else {
        echo json_encode(['success' => false, 'message' => 'Database connection not available']);
    }
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => 'Error: ' . $e->getMessage()]);
}
?>