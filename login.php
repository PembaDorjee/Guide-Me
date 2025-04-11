<?php session_start(); ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Login</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: Arial, sans-serif;
        }
        body {
            background: url('https://source.unsplash.com/1600x900/?mountains,adventure') no-repeat center center/cover;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
        }
        .container {
            background: rgba(255, 255, 255, 0.9);
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            width: 100%;
            max-width: 400px;
        }
        .container h2 {
            text-align: center;
            color: #2c3e50;
            margin-bottom: 20px;
        }
        .error {
            color: red;
            font-size: 14px;
            text-align: center;
            margin-bottom: 10px;
            padding: 10px;
            background: #ffdddd;
            border-left: 4px solid red;
        }
        .success {
            color: green;
            font-size: 14px;
            text-align: center;
            margin-bottom: 10px;
            padding: 10px;
            background: #ddffdd;
            border-left: 4px solid green;
        }
        .form-group {
            margin-bottom: 15px;
        }
        label {
            font-size: 14px;
            color: #34495e;
        }
        input {
            width: 100%;
            padding: 10px;
            border: 1px solid #95a5a6;
            border-radius: 5px;
            margin-top: 5px;
        }
        button {
            width: 100%;
            padding: 10px;
            background: #16a085;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }
        button:hover {
            background: #1abc9c;
        }
        .register-link {
            text-align: center;
            margin-top: 10px;
        }
        .register-link a {
            color: #16a085;
            text-decoration: none;
        }
    </style>
</head>
<body>
    <div class="container">
        <h2>Login</h2>

        <!-- Display error message -->
        <?php if (isset($_SESSION['error'])): ?>
            <p class="error"><?php echo $_SESSION['error']; unset($_SESSION['error']); ?></p>
        <?php endif; ?>
        
        <!-- Display success message from URL parameter -->
        <?php if (isset($_GET['message'])): ?>
            <p class="success"><?php echo htmlspecialchars($_GET['message']); ?></p>
        <?php endif; ?>

        <form id="loginForm" action="login_process.php" method="POST">
            <div class="form-group">
                <label for="email">Email</label>
                <input type="email" id="email" name="email" required>
            </div>
            <div class="form-group">
                <label for="password">Password</label>
                <input type="password" id="password" name="password" required>
            </div>
            <div id="loginMessage"></div>
            <button type="submit">Login</button>
        </form>
        <div class="register-link">
            <p>Don't have an account? <a href="registration.php">Register here</a></p>
        </div>
    </div>
</body>
</html>

<script>
document.getElementById('loginForm').addEventListener('submit', function(e) {
    e.preventDefault();
    
    const formData = new FormData(this);
    
    fetch('login_process.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // Show success message
            document.getElementById('loginMessage').innerHTML = 
                `<div class="success">${data.message}</div>`;
            
            // Redirect to the specified URL
            setTimeout(() => {
                window.location.href = data.redirect;
            }, 1000); // Redirect after 1 second
        } else {
            // Show error message
            document.getElementById('loginMessage').innerHTML = 
                `<div class="error">${data.message}</div>`;
                
            // If there's a redirect for verification
            if (data.redirect) {
                setTimeout(() => {
                    window.location.href = data.redirect;
                }, 2000);
            }
        }
    })
    .catch(error => {
        console.error('Error:', error);
        document.getElementById('loginMessage').innerHTML = 
            '<div class="error">An error occurred. Please try again.</div>';
    });
});
</script>
</body>
</html>
