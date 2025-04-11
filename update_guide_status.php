<?php
session_start();
require_once '../database.php';
require_once '../includes/email_helper.php'; // Include the email helper
header('Content-Type: application/json');

// Enable error reporting for debugging
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Log the incoming request
error_log("Update guide status request received: " . print_r($_POST, true));

// Check if user is logged in and is admin
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    http_response_code(401);
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit();
}

if (!isset($_POST['guide_id']) || !isset($_POST['action'])) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Missing required parameters']);
    exit();
}

$guide_id = intval($_POST['guide_id']);
$action = $_POST['action'];
$send_email = isset($_POST['send_email']) && $_POST['send_email'] === 'true';

// Log the processed parameters
error_log("Processing: guide_id=$guide_id, action=$action, send_email=" . ($send_email ? 'true' : 'false'));

// Validate action
if ($action !== 'approve' && $action !== 'deny') {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Invalid action']);
    exit();
}

try {
    // Get guide email for notification
    $stmt = $conn->prepare("SELECT full_name, email FROM Guide WHERE guide_id = ?");
    if (!$stmt) {
        throw new Exception("Prepare statement failed: " . $conn->error);
    }
    
    $stmt->bind_param("i", $guide_id);
    
    if (!$stmt->execute()) {
        throw new Exception("Execute failed: " . $stmt->error);
    }
    
    $result = $stmt->get_result();
    
    if ($result->num_rows === 0) {
        http_response_code(404);
        echo json_encode(['success' => false, 'message' => 'Guide not found']);
        exit();
    }
    
    $guide = $result->fetch_assoc();
    $guide_name = $guide['full_name'];
    $guide_email = $guide['email'];
    
    // Update guide status - use 'active' instead of 'approved' and 'inactive' instead of 'denied'
    // to match the ENUM values in your database
    $status = ($action === 'approve') ? 'active' : 'inactive';
    
    // Log the status value for debugging
    error_log("Setting status to: '$status' for guide_id: $guide_id");
    
    $update_stmt = $conn->prepare("UPDATE Guide SET status = ? WHERE guide_id = ?");
    
    if (!$update_stmt) {
        throw new Exception("Prepare update statement failed: " . $conn->error);
    }
    
    $update_stmt->bind_param("si", $status, $guide_id);
    
    if (!$update_stmt->execute()) {
        throw new Exception("Failed to update guide status: " . $update_stmt->error);
    }
    
    // Send email notification if requested
    $mail_sent = false;
    if ($send_email && !empty($guide_email)) {
        // Prepare email content
        $subject = ($action === 'approve') 
            ? "Your Guide Me Registration Has Been Approved" 
            : "Guide Me Registration Status Update";
        
        if ($action === 'approve') {
            $message = "
                <div style='font-family: Arial, sans-serif; max-width: 600px; margin: 0 auto; padding: 20px; border: 1px solid #eee; border-radius: 10px;'>
                    <h2 style='color: #3a4b5e; text-align: center;'>Registration Approved!</h2>
                    <p>Dear $guide_name,</p>
                    <p>Congratulations! Your guide registration has been approved. You can now log in to your account and start using all guide features.</p>
                    <div style='background-color: #f8f9fa; padding: 15px; border-radius: 5px; text-align: center; margin: 20px 0;'>
                        <a href='http://localhost/FYPC/guide/guide_login.php' style='display: inline-block; background-color: #f3a42e; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px; font-weight: bold;'>Login Now</a>
                    </div>
                    <p>Thank you for joining our platform!</p>
                    <p>Best regards,<br>The Guide Me Team</p>
                </div>
            ";
        } else {
            $message = "
                <div style='font-family: Arial, sans-serif; max-width: 600px; margin: 0 auto; padding: 20px; border: 1px solid #eee; border-radius: 10px;'>
                    <h2 style='color: #3a4b5e; text-align: center;'>Registration Status Update</h2>
                    <p>Dear $guide_name,</p>
                    <p>We regret to inform you that your guide registration has been denied.</p>
                    <p>If you have any questions or would like to provide additional information to support your application, please contact our support team.</p>
                    <p>Best regards,<br>The Guide Me Team</p>
                </div>
            ";
        }
        
        // Use the email helper function with HTML content
        $mail_sent = send_email($guide_email, $subject, $message, 'Guide Me Team', 'noreply@guideme.com', true);
        
        // Log the email attempt with more details
        error_log("Email attempt to $guide_email: " . ($mail_sent ? "Success" : "Failed"));
    }
    
    echo json_encode([
        'success' => true, 
        'message' => "Guide " . ($action === 'approve' ? 'approved' : 'denied') . " successfully",
        'email_sent' => $mail_sent
    ]);
    
} catch (Exception $e) {
    error_log("Error in update_guide_status.php: " . $e->getMessage());
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}

$conn->close();
?>