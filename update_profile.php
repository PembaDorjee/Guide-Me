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
if (!isset($_POST['full_name']) || !isset($_POST['email'])) {
    echo json_encode(['success' => false, 'message' => 'Missing required fields']);
    exit;
}

$client_id = $_SESSION['client_id'];
$full_name = trim($_POST['full_name']);
$email = trim($_POST['email']);
$phone = isset($_POST['phone']) ? trim($_POST['phone']) : null;

// Validate inputs
if (empty($full_name)) {
    echo json_encode(['success' => false, 'message' => 'Name cannot be empty']);
    exit;
}

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    echo json_encode(['success' => false, 'message' => 'Invalid email format']);
    exit;
}

try {
    // Check if we're using mysqli or PDO
    if (isset($conn)) {
        // Using mysqli
        $stmt = $conn->prepare("UPDATE clients SET full_name = ?, email = ?, phone = ? WHERE client_id = ?");
        $stmt->bind_param("sssi", $full_name, $email, $phone, $client_id);
        $success = $stmt->execute();
        
        if ($success) {
            // Update session data
            $_SESSION['full_name'] = $full_name;
            $_SESSION['email'] = $email;
            $_SESSION['phone_number'] = $phone; // Store phone as phone_number in session
            
            echo json_encode(['success' => true, 'message' => 'Profile updated successfully']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Failed to update profile: ' . $conn->error]);
        }
        
        $stmt->close();
    } else if (isset($pdo)) {
        // Using PDO
        $stmt = $pdo->prepare("UPDATE clients SET full_name = ?, email = ?, phone = ? WHERE client_id = ?");
        $success = $stmt->execute([$full_name, $email, $phone, $client_id]);
        
        if ($success) {
            // Update session data
            $_SESSION['full_name'] = $full_name;
            $_SESSION['email'] = $email;
            $_SESSION['phone_number'] = $phone; // Store phone as phone_number in session
            
            echo json_encode(['success' => true, 'message' => 'Profile updated successfully']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Failed to update profile']);
        }
    } else {
        echo json_encode(['success' => false, 'message' => 'Database connection not available']);
    }
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => 'Error: ' . $e->getMessage()]);
}
?>