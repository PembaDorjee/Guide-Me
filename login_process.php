<?php
session_start();
include 'database.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = filter_var($_POST['email'], FILTER_SANITIZE_EMAIL);
    $password = $_POST['password'];
    
    // Validate email
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        echo json_encode(['success' => false, 'message' => 'Invalid email format']);
        exit;
    }
    
    // Check if user exists and is verified
    $stmt = $conn->prepare("SELECT client_id, full_name, password, role, avatar_path, status FROM Client WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows === 0) {
        echo json_encode(['success' => false, 'message' => 'Email not found']);
        exit;
    }
    
    $user = $result->fetch_assoc();
    
    // Check if account is verified
    if ($user['status'] != 1) {
        echo json_encode([
            'success' => false, 
            'message' => 'Please verify your email first',
            'redirect' => 'verify_email.php?email=' . urlencode($email)
        ]);
        exit;
    }
    
    // Verify password
    if (password_verify($password, $user['password'])) {
        // Set session variables
        // This should be in your login_process.php file
        // After successful authentication:
        
        $_SESSION['client_id'] = $user['client_id'];
        $_SESSION['full_name'] = $user['full_name'];
        // Make sure this line exists and is setting the avatar_path correctly
        $_SESSION['avatar_path'] = $user['avatar_path'];
        $_SESSION['email'] = $email;
        $_SESSION['role'] = $user['role'];
        $_SESSION['avatar_path'] = $user['avatar_path'];
        
        // Return success response with redirect to frontendc/index.php
        // Change the redirect path from 'frontendc/index.php' to 'frontendc/index3.php'
        echo json_encode([
            'success' => true, 
            'message' => 'Login successful!',
            'redirect' => 'frontendc/index3.php'
        ]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Incorrect password']);
    }
    
    $stmt->close();
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
}
$conn->close();
?>
