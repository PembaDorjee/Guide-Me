<?php
session_start();
header('Content-Type: application/json');
require_once '../database.php';

// Check if user is logged in
if (!isset($_SESSION['client_id'])) {
    echo json_encode(['success' => false, 'message' => 'Not logged in']);
    exit;
}

// Check if password is provided
if (!isset($_POST['password'])) {
    echo json_encode(['success' => false, 'message' => 'Password is required']);
    exit;
}

$client_id = $_SESSION['client_id'];
$password = $_POST['password'];

try {
    // Check if we're using mysqli or PDO
    if (isset($conn)) {
        // First verify password
        $stmt = $conn->prepare("SELECT password FROM Client WHERE client_id = ?");
        $stmt->bind_param("i", $client_id);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result && $result->num_rows > 0) {
            $user = $result->fetch_assoc();
            
            // Verify password
            if (!password_verify($password, $user['password'])) {
                echo json_encode(['success' => false, 'message' => 'Password is incorrect']);
                exit;
            }
            
            // Delete the account
            $delete_stmt = $conn->prepare("DELETE FROM Client WHERE client_id = ?");
            $delete_stmt->bind_param("i", $client_id);
            $success = $delete_stmt->execute();
            
            if ($success) {
                // Destroy session
                session_destroy();
                echo json_encode(['success' => true, 'message' => 'Account deleted successfully']);
            } else {
                echo json_encode(['success' => false, 'message' => 'Failed to delete account: ' . $conn->error]);
            }
            
            $delete_stmt->close();
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