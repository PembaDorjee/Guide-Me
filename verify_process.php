<?php
session_start();
include 'database.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = filter_var($_POST['email'], FILTER_SANITIZE_EMAIL);
    $entered_code = trim($_POST['verification_code']);

    // Modified query to use the correct column names from your database
    // You might need to adjust these column names based on your actual database structure
    $sql = "SELECT client_id, verification_code, status FROM Client WHERE email = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows === 0) {
        header("Location: verify_email.php?email=$email&error=Email not found");
        exit;
    }

    $stmt->bind_result($client_id, $stored_code, $status);
    $stmt->fetch();

    if ($status == 1) {
        header("Location: login.php?message=Email already verified. Please log in.");
        exit;
    }

    if ($entered_code == $stored_code) {
        $update_sql = "UPDATE Client SET status = 1 WHERE email = ?";
        $update_stmt = $conn->prepare($update_sql);
        $update_stmt->bind_param("s", $email);
        
        if ($update_stmt->execute()) {
            // Show success message and redirect to login page
            header("Location: login.php?message=Registration successful! Your email has been verified. Please log in.");
            exit;
        } else {
            header("Location: verify_email.php?email=$email&error=Verification failed: " . $conn->error);
            exit;
        }
    } else {
        header("Location: verify_email.php?email=$email&error=Invalid verification code");
        exit;
    }
}

header("Location: registration.php");
exit;
?>