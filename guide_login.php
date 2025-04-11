<?php
session_start();
require_once '../database.php';

// Initialize variables
$errors = [];
$email = '';

// Check if user is already logged in
if (isset($_SESSION['guide_id'])) {
    // Redirect to guide dashboard
    header("Location: guide_dashboard.php");
    exit();
}

// Check if coming from registration
$registered = isset($_GET['registered']) && $_GET['registered'] == 1;

// Check if coming from verification
$verified = isset($_GET['verified']) && $_GET['verified'] == 1;

// Process form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get form data
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    
    // Validate form data
    if (empty($email)) {
        $errors[] = "Email is required";
    }
    
    if (empty($password)) {
        $errors[] = "Password is required";
    }
    
    // If no errors, check credentials
    if (empty($errors)) {
        // Prepare SQL statement
        $stmt = $conn->prepare("SELECT guide_id, full_name, email, password, status FROM Guide WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result->num_rows === 1) {
            $guide = $result->fetch_assoc();
            
            // Check if account is verified
            if ($guide['status'] == 'pending') {
                $errors[] = "Your account is pending admin approval. We'll notify you when your account is activated.";
            } else if ($guide['status'] != 'active') {
                $errors[] = "Your account is not verified. Please check your email for verification instructions.";
                // Add a link to resend verification code
                $resend_link = true;
            } else {
                // Verify password
                if (password_verify($password, $guide['password'])) {
                    // Password is correct, create session
                    $_SESSION['guide_id'] = $guide['guide_id'];
                    $_SESSION['guide_name'] = $guide['full_name'];
                    $_SESSION['guide_email'] = $guide['email'];
                    $_SESSION['user_type'] = 'guide';
                    
                    // Redirect to guide dashboard
                    header("Location: guide_dashboard.php");
                    exit();
                } else {
                    $errors[] = "Invalid email or password";
                }
            }
        } else {
            $errors[] = "Invalid email or password";
        }
        
        $stmt->close();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Guide Login | Guide Me</title>
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
        
        .login-container {
            max-width: 500px;
            width: 100%;
            padding: 0 25px;
            margin: 50px 0;
        }
        
        .login-card {
            background: white;
            border-radius: 20px;
            box-shadow: var(--card-shadow);
            overflow: hidden;
            transition: all 0.3s ease;
        }
        
        .login-header {
            background: var(--primary-dark);
            color: white;
            padding: 40px;
            text-align: center;
            position: relative;
            overflow: hidden;
            clip-path: polygon(0 0, 100% 0, 100% 90%, 0 100%);
        }
        
        .login-title {
            font-family: 'Montserrat', sans-serif;
            font-size: 2rem;
            font-weight: 700;
            margin-bottom: 15px;
            text-shadow: 0 2px 5px rgba(0,0,0,0.1);
        }
        
        .login-subtitle {
            font-size: 1rem;
            opacity: 0.9;
        }
        
        .login-body {
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
        }
        
        .form-control:focus {
            border-color: var(--accent-color);
            box-shadow: 0 0 0 3px rgba(243, 164, 46, 0.2);
        }
        
        .btn-login {
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
        
        .btn-login:hover {
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
        
        .register-link {
            text-align: center;
            margin-top: 30px;
            font-size: 1rem;
        }
        
        .register-link a {
            color: var(--accent-color);
            font-weight: 600;
            text-decoration: none;
            transition: all 0.3s ease;
        }
        
        .register-link a:hover {
            text-decoration: underline;
        }
        
        .forgot-password {
            text-align: right;
            margin-top: -15px;
            margin-bottom: 20px;
        }
        
        .forgot-password a {
            color: var(--text-light);
            font-size: 0.9rem;
            text-decoration: none;
            transition: all 0.3s ease;
        }
        
        .forgot-password a:hover {
            color: var(--accent-color);
        }
        
        .verification-success {
            padding: 20px 0;
        }
        
        .success-icon .circle-icon {
            width: 80px;
            height: 80px;
            background-color: #ff9933;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto;
            box-shadow: 0 4px 12px rgba(255, 153, 51, 0.3);
        }
        
        .success-icon .circle-icon i {
            color: white;
            font-size: 40px;
        }
    </style>
</head>
<body>
    <div class="login-container">
        <div class="login-card">
            <div class="login-header">
                <h1 class="login-title">Guide Me</h1>
                <p class="login-subtitle">Sign in to start guiding with confidence</p>
            </div>
            
            <div class="login-body">
                <?php if (!empty($errors)): ?>
                    <div class="alert alert-danger">
                        <ul class="mb-0">
                            <?php foreach ($errors as $error): ?>
                                <li><?php echo htmlspecialchars($error); ?></li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                <?php endif; ?>
                
                <?php if ($registered): ?>
                    <div class="alert alert-success">
                        <i class="fas fa-check-circle me-2"></i> Registration successful! You can now login.
                    </div>
                <?php endif; ?>
                
                <?php if ($verified): ?>
                    <div class="verification-success text-center py-4">
                        <div class="success-icon mb-3">
                            <div class="circle-icon">
                                <i class="fas fa-check"></i>
                            </div>
                        </div>
                        <h3 class="mb-2">Welcome to Guide Me!</h3>
                        <p class="text-muted mb-4">Thank you for joining us. Your email has been verified successfully. We'll review your profile and notify you when your registration is approved.</p>
                        <a href="guide_login.php" class="btn btn-primary px-4 py-2 rounded-pill">
                            Continue to Login
                        </a>
                        <div class="mt-2 small text-muted">Redirecting in <span id="countdown">15</span> seconds...</div>
                    </div>
                    <script>
                        // Countdown timer for redirect
                        let seconds = 15;
                        const countdownElement = document.getElementById('countdown');
                        
                        const countdownTimer = setInterval(function() {
                            seconds--;
                            countdownElement.textContent = seconds;
                            
                            if (seconds <= 0) {
                                clearInterval(countdownTimer);
                                window.location.href = 'guide_login.php';
                            }
                        }, 1000);
                    </script>
                <?php else: ?>
                    <form action="guide_login.php" method="POST">
                        <div class="form-group">
                            <label for="email" class="form-label">Email Address</label>
                            <input type="email" class="form-control" id="email" name="email" value="<?php echo htmlspecialchars($email); ?>" required>
                        </div>
                        
                        <div class="form-group">
                            <label for="password" class="form-label">Password</label>
                            <input type="password" class="form-control" id="password" name="password" required>
                        </div>
                        
                        <div class="forgot-password">
                            <a href="guide_forgot_password.php">Forgot Password?</a>
                        </div>
                        
                        <button type="submit" class="btn-login">
                            <i class="fas fa-sign-in-alt"></i> Login
                        </button>
                        
                        <?php if (isset($resend_link)): ?>
                            <div class="mt-3 text-center">
                                <a href="guide_verify.php?email=<?php echo urlencode($email); ?>" class="btn btn-sm btn-outline-primary">
                                    <i class="fas fa-paper-plane"></i> Resend Verification Email
                                </a>
                            </div>
                        <?php endif; ?>
                        
                        <div class="register-link">
                            Don't have an account? <a href="guide_register.php">Register here</a>
                        </div>
                    </form>
                <?php endif; ?>
            </div>
        </div>
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>