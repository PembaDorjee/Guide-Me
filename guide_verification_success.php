<?php
session_start();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Verification Success | Guide Me</title>
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
        
        .success-container {
            max-width: 500px;
            width: 100%;
            padding: 0 25px;
            margin: 50px 0;
        }
        
        .success-card {
            background: white;
            border-radius: 20px;
            box-shadow: var(--card-shadow);
            overflow: hidden;
            transition: all 0.3s ease;
            padding: 40px;
            text-align: center;
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
        
        .btn-continue {
            background: var(--accent-color);
            color: white;
            border: none;
            padding: 12px 30px;
            border-radius: 50px;
            font-weight: 600;
            font-size: 1rem;
            transition: all 0.3s ease;
            display: inline-flex;
            align-items: center;
            gap: 10px;
        }
        
        .btn-continue:hover {
            background: #e69527;
            transform: translateY(-3px);
            box-shadow: 0 10px 20px rgba(243, 164, 46, 0.3);
            color: white;
        }
    </style>
</head>
<body>
    <div class="success-container">
        <div class="success-card">
            <div class="success-icon mb-4">
                <div class="circle-icon">
                    <i class="fas fa-check"></i>
                </div>
            </div>
            <h3 class="mb-3">Welcome to Guide Me!</h3>
            <p class="text-muted mb-4">Your email has been verified successfully. We'll review your profile and notify you when your registration is approved.</p>
            <a href="guide_login.php" class="btn btn-continue">
                Continue to Login
            </a>
            <div class="mt-3 small text-muted">Redirecting in <span id="countdown">15</span> seconds...</div>
        </div>
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
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
</body>
</html>