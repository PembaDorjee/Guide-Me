<?php
include 'database.php';

if (isset($_GET['email'])) {
    $email = $_GET['email'];
} else {
    // Redirect to the registration page if email is not set
    header("Location: registration.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Email Verification</title>
    <style>
        /* General Reset */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Arial', sans-serif;
            background-color: #f4f4f9;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
        }

        .verification-card {
            background: #fff;
            padding: 2rem;
            border-radius: 12px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
            max-width: 400px;
            width: 100%;
            text-align: center;
        }

        .verification-card h2 {
            font-size: 1.5rem;
            margin-bottom: 1.5rem;
            color: #333;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .verification-card h2 i {
            margin-right: 0.5rem;
            color: #4CAF50;
        }

        .verification-card input[type="text"] {
            width: 100%;
            padding: 0.75rem;
            margin-bottom: 1rem;
            border: 1px solid #ddd;
            border-radius: 8px;
            font-size: 1rem;
            outline: none;
            transition: border-color 0.3s ease;
        }

        .verification-card input[type="text"]:focus {
            border-color: #4CAF50;
        }

        .verification-card button {
            width: 100%;
            padding: 0.75rem;
            background-color: #4CAF50;
            color: #fff;
            border: none;
            border-radius: 8px;
            font-size: 1rem;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }

        .verification-card button:hover {
            background-color: #45a049;
        }

        .message {
            margin-top: 1rem;
            padding: 0.75rem;
            border-radius: 8px;
            font-size: 0.9rem;
        }

        .message.error {
            background-color: #ffebee;
            color: #c62828;
        }

        .message.success {
            background-color: #e8f5e9;
            color: #2e7d32;
        }

        /* Responsive Design */
        @media (max-width: 480px) {
            .verification-card {
                padding: 1.5rem;
            }

            .verification-card h2 {
                font-size: 1.25rem;
            }
        }
    </style>
</head>
<body>
    <div class="verification-card">
        <h2><i class="fas fa-envelope"></i> Email Verification</h2>
        <form method="POST" action="verify_process.php">
            <input type="text" name="verification_code" placeholder="Enter Verification Code" required>
            <input type="hidden" name="email" value="<?php echo htmlspecialchars($email); ?>">
            <button type="submit">Verify</button>
        </form>
        <!-- Placeholder for error/success messages -->
        <?php
        if (isset($_GET['error'])) {
            echo '<div class="message error">' . htmlspecialchars($_GET['error']) . '</div>';
        }
        if (isset($_GET['success'])) {
            echo '<div class="message success">' . htmlspecialchars($_GET['success']) . '</div>';
        }
        ?>
    </div>

    <!-- Font Awesome for Icons -->
    <script src="https://kit.fontawesome.com/your-code-here.js" crossorigin="anonymous"></script>
    <!-- No changes needed to the main file, but let's add a script to handle successful verification -->
    <script>
        // This will be triggered by the success message from verify_process.php
        function showSuccessAndRedirect(message) {
            const successDiv = document.createElement('div');
            successDiv.className = 'message success';
            successDiv.textContent = message;
            
            const form = document.querySelector('form');
            form.style.display = 'none';
            
            const card = document.querySelector('.verification-card');
            card.appendChild(successDiv);
            
            setTimeout(() => {
                window.location.href = 'login.php?message=' + encodeURIComponent(message);
            }, 2000);
        }
    </script>
</body>
</html>