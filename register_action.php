<?php
session_start();
header('Content-Type: application/json');
include 'database.php';
require 'vendor/autoload.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

// Enable error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        // Validate required fields
        $required = ['fullName', 'email', 'password', 'confirmPassword', 'terms'];
        foreach ($required as $field) {
            if (empty($_POST[$field])) {
                throw new Exception(ucfirst($field) . ' is required');
            }
        }

        // Validate inputs
        $fullName = trim(strip_tags($_POST['fullName']));
        $email = strtolower(filter_var(trim($_POST['email']), FILTER_SANITIZE_EMAIL));
        $phone = trim(strip_tags($_POST['phone'] ?? ''));
        $role = trim(strip_tags($_POST['role'] ?? 'user'));
        $verification_code = str_pad(rand(0, 999999), 6, '0', STR_PAD_LEFT);
        
        // Handle avatar upload
        $avatar_path = null;
        if (isset($_FILES['avatar']) && $_FILES['avatar']['error'] === UPLOAD_ERR_OK) {
            $allowedTypes = ['image/jpeg', 'image/png', 'image/webp', 'image/gif'];
            if (!in_array($_FILES['avatar']['type'], $allowedTypes)) {
                throw new Exception('Only JPG, PNG, WEBP, GIF images are allowed');
            }
            
            // Create uploads directory with proper permissions
            $upload_dir = 'uploads/avatars';
            $full_upload_dir = $_SERVER['DOCUMENT_ROOT'] . '/FYPC/' . $upload_dir;
            
            if (!file_exists($full_upload_dir)) {
                if (!mkdir($full_upload_dir, 0777, true)) {
                    throw new Exception('Failed to create upload directory');
                }
            }
            
            // Make sure the directory is writable
            chmod($full_upload_dir, 0777);
            
            // Generate a unique filename
            $ext = pathinfo($_FILES['avatar']['name'], PATHINFO_EXTENSION);
            $filename = uniqid() . '.' . $ext;
            $avatar_path = $upload_dir . '/' . $filename; // Store relative path in DB
            $full_path = $full_upload_dir . '/' . $filename; // Full path for file operations
            
            // Move the uploaded file
            if (!move_uploaded_file($_FILES['avatar']['tmp_name'], $full_path)) {
                $error = error_get_last();
                throw new Exception('Failed to upload avatar: ' . ($error ? $error['message'] : 'Unknown error'));
            }
        }
        
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            throw new Exception('Invalid email format');
        }
        
        if ($_POST['password'] !== $_POST['confirmPassword']) {
            throw new Exception('Passwords do not match');
        }
        
        $password = password_hash($_POST['password'], PASSWORD_DEFAULT);

        // Check if email is already verified
        $check = $conn->prepare("SELECT client_id FROM Client WHERE email = ? AND status = 1");
        if (!$check) throw new Exception('Database error: ' . $conn->error);
        
        $check->bind_param("s", $email);
        if (!$check->execute()) throw new Exception('Database error: ' . $check->error);
        
        $check->store_result();
        if ($check->num_rows > 0) {
            throw new Exception('Email already registered and verified');
        }

        // Insert or update user with avatar_path
        $stmt = $conn->prepare("INSERT INTO Client 
            (full_name, email, phone, password, role, verification_code, status, created_at, avatar_path)
            VALUES (?, ?, ?, ?, ?, ?, 0, NOW(), ?)
            ON DUPLICATE KEY UPDATE
            full_name = VALUES(full_name),
            phone = VALUES(phone),
            password = VALUES(password),
            role = VALUES(role),
            verification_code = VALUES(verification_code),
            status = 0,
            created_at = NOW(),
            avatar_path = VALUES(avatar_path)");
        
        if (!$stmt) throw new Exception('Database error: ' . $conn->error);
        
        $stmt->bind_param("sssssss", $fullName, $email, $phone, $password, $role, $verification_code, $avatar_path);
        
        if (!$stmt->execute()) {
            throw new Exception('Database error: ' . $stmt->error);
        }

        // Send verification email
        $mail = new PHPMailer(true);
        try {
            //Server settings
            $mail->isSMTP();
            $mail->Host = 'smtp.gmail.com';  // SMTP server
            $mail->SMTPAuth = true;
            $mail->Username = 'np03cs4a220505@heraldcollege.edu.np';  // Your Gmail address
            $mail->Password = 'wbqq vffc nzay lnvm';  // The app-specific password
            $mail->SMTPSecure = 'tls';  // Encryption type
            $mail->Port = 587;  // TCP port to connect to

            //Recipients
            $mail->setFrom('np03cs4a220505@heraldcollege.edu.np', 'Guide Me');
            $mail->addAddress($email, $fullName);

            //Content
            $mail->isHTML(true);
            $mail->Subject = 'Email Verification';
            $mail->Body = "
                <div style='font-family: Arial, sans-serif; max-width: 600px; margin: 0 auto; padding: 20px; border: 1px solid #eee; border-radius: 10px;'>
                    <h2 style='color: #4361ee; text-align: center;'>Welcome to Guide Me!</h2>
                    <p>Hello $fullName,</p>
                    <p>Thank you for registering with us. Please use the verification code below to complete your registration:</p>
                    <div style='background-color: #f8f9fa; padding: 15px; border-radius: 5px; text-align: center; margin: 20px 0;'>
                        <h3 style='margin: 0; color: #333; letter-spacing: 5px;'>$verification_code</h3>
                    </div>
                    <p>If you did not request this verification, please ignore this email.</p>
                    <p>Best regards,<br>The Guide Me Team</p>
                </div>
            ";

            $mail->send();
        } catch (Exception $e) {
            throw new Exception('Email could not be sent. Mailer Error: ' . $mail->ErrorInfo);
        }

        // Return success response
        echo json_encode([
            'success' => true,
            'message' => 'Registration successful! Please check your email for verification.',
            'redirect' => 'verify_email.php?email=' . urlencode($email)
        ]);
        
    } catch (Exception $e) {
        echo json_encode([
            'success' => false,
            'message' => $e->getMessage()
        ]);
    }
} else {
    echo json_encode([
        'success' => false,
        'message' => 'Invalid request method'
    ]);
}