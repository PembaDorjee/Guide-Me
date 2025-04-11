<?php
session_start();
require_once '../database.php';

if (isset($_GET['email'])) {
    $email = filter_var($_GET['email'], FILTER_SANITIZE_EMAIL);
} else {
    // Redirect to the registration page if email is not set
    header("Location: guide_register.php");
    exit;
}

$errors = [];
$success = false;

// Process verification form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $entered_code = trim($_POST['verification_code']);
    
    // Get the stored verification code
    $stmt = $conn->prepare("SELECT guide_id, verification_code, status FROM Guide WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $stmt->store_result();
    
    if ($stmt->num_rows === 0) {
        $errors[] = "Email not found";
    } else {
        $stmt->bind_result($guide_id, $stored_code, $status);
        $stmt->fetch();
        
        if ($status == 'active') {
            header("Location: guide_login.php?message=Email already verified. Please log in.");
            exit;
        }
        
        if ($entered_code == $stored_code) {
            // Update guide status to active
            // When updating the guide status after email verification, set it to 'pending' instead of 'active'
            $update_stmt = $conn->prepare("UPDATE Guide SET status = 'pending', verification_code = NULL WHERE email = ? AND verification_code = ?");
            $update_stmt->bind_param("ss", $email, $verification_code);
            
            // After successful verification, update the success message to indicate admin approval is needed
            if ($update_stmt->execute()) {
                $success = true;
                
                // Redirect to verification success page or show success message
                header("Location: guide_login.php?verified=1");
                exit();
            } else {
                $errors[] = "Verification failed: " . $conn->error;
            }
            
            $update_stmt->close();
        } else {
            $errors[] = "Invalid verification code";
        }
    }
    
    $stmt->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Guide Email Verification | Guide Me</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&family=Montserrat:wght@400;600;700&display=swap" rel="stylesheet">
    <style>
        :root {
            --primary-dark: #1E2A38;
            --primary-color: #3a4b5e;
            --primary-light: #4a5d72;
            --accent-color: #f3a42e;
            --secondary-accent: #15d455;
            --light-color: #f8f9fa;
            --dark-color: #212529;
            --text-dark: #2b2d42;
            --text-light: #8d99ae;
            --card-shadow: 0 20px 50px -10px rgba(0, 0, 0, 0.15);
        }
        
        body {
            font-family: 'Poppins', sans-serif;
            background-color: #f0f4f8;
            background-image: radial-gradient(#d6e4f0 1px, transparent 1px);
            background-size: 20px 20px;
            color: var(--text-dark);
            line-height: 1.7;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        
        .verify-container {
            max-width: 500px;
            width: 100%;
            padding: 0 25px;
            margin: 50px 0;
        }
        
        .verify-card {
            background: white;
            border-radius: 20px;
            box-shadow: var(--card-shadow);
            overflow: hidden;
            transition: all 0.3s ease;
        }
        
        .verify-header {
            background: var(--primary-dark);
            color: white;
            padding: 40px;
            text-align: center;
            position: relative;
            overflow: hidden;
            clip-path: polygon(0 0, 100% 0, 100% 90%, 0 100%);
        }
        
        .verify-title {
            font-family: 'Montserrat', sans-serif;
            font-size: 2rem;
            font-weight: 700;
            margin-bottom: 15px;
            text-shadow: 0 2px 5px rgba(0,0,0,0.1);
        }
        
        .verify-subtitle {
            font-size: 1rem;
            opacity: 0.9;
        }
        
        .verify-body {
            padding: 40px;
        }
        
        .form-group {
            margin-bottom: 25px;
        }
        
        .form-label {
            font-weight: 500;
            margin-bottom: 8px;
            color: var(--text-dark);
        }
        
        .form-control {
            padding: 12px 15px;
            border-radius: 8px;
            border: 1px solid rgba(0,0,0,0.1);
            font-size: 1rem;
            transition: all 0.3s ease;
            text-align: center;
            letter-spacing: 5px;
            font-size: 1.5rem;
        }
        
        .form-control:focus {
            border-color: var(--accent-color);
            box-shadow: 0 0 0 3px rgba(243, 164, 46, 0.2);
        }
        
        .btn-verify {
            background: var(--accent-color);
            color: white;
            border: none;
            padding: 15px 35px;
            border-radius: 50px;
            font-weight: 600;
            font-size: 1.1rem;
            transition: all 0.3s ease;
            display: inline-flex;
            align-items: center;
            gap: 10px;
            width: 100%;
            justify-content: center;
        }
        
        .btn-verify:hover {
            background: #e69527;
            transform: translateY(-3px);
            box-shadow: 0 10px 20px rgba(243, 164, 46, 0.3);
        }
        
        .alert {
            border-radius: 10px;
            padding: 15px 20px;
            margin-bottom: 30px;
        }
        
        .alert-danger {
            background-color: rgba(231, 76, 60, 0.1);
            border-left: 4px solid #e74c3c;
            color: #c0392b;
        }
        
        .alert-success {
            background-color: rgba(46, 204, 113, 0.1);
            border-left: 4px solid #2ecc71;
            color: #27ae60;
        }
        
        .resend-link {
            text-align: center;
            margin-top: 30px;
            font-size: 1rem;
        }
        
        .resend-link a {
            color: var(--accent-color);
            font-weight: 600;
            text-decoration: none;
            transition: all 0.3s ease;
        }
        
        .resend-link a:hover {
            text-decoration: underline;
        }
        
        .verification-info {
            text-align: center;
            margin-bottom: 30px;
            color: var(--text-light);
        }
        
        .verification-info i {
            font-size: 3rem;
            color: var(--accent-color);
            margin-bottom: 15px;
            display: block;
        }
    </style>
</head>
<body>
    <div class="verify-container">
        <div class="verify-card">
            <div class="verify-header">
                <h1 class="verify-title">Verify Your Email</h1>
                <p class="verify-subtitle">We've sent a verification code to your email</p>
            </div>
            
            <div class="verify-body">
                <?php if (!empty($errors)): ?>
                    <div class="alert alert-danger">
                        <ul class="mb-0">
                            <?php foreach ($errors as $error): ?>
                                <li><?php echo htmlspecialchars($error); ?></li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                <?php endif; ?>
                
                <?php if ($success): ?>
                    <div class="alert alert-success">
                        <i class="fas fa-check-circle me-2"></i> Email verified successfully! You can now login.
                    </div>
                <?php endif; ?>
                
                <div class="verification-info">
                    <i class="fas fa-envelope-open-text"></i>
                    <p>We've sent a verification code to <strong><?php echo htmlspecialchars($email); ?></strong></p>
                    <p>Please check your inbox and enter the code below to verify your account.</p>
                </div>
                
                <form action="guide_verify.php?email=<?php echo urlencode($email); ?>" method="POST">
                    <div class="form-group">
                        <label for="verification_code" class="form-label">Verification Code</label>
                        <input type="text" class="form-control" id="verification_code" name="verification_code" maxlength="6" required>
                    </div>
                    
                    <button type="submit" class="btn-verify">
                        <i class="fas fa-check-circle"></i> Verify Email
                    </button>
                    
                    <div class="resend-link">
                        Didn't receive the code? <a href="guide_resend_code.php?email=<?php echo urlencode($email); ?>">Resend Code</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>