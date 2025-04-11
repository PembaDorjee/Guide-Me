<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>User Registration</title>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" />
  <style>
    :root {
        --primary-color: #4361ee;
        --primary-hover: #3a56d4;
        --success-color: #2ecc71;
        --error-color: #e74c3c;
        --text-color: #2b2d42;
        --text-light: #8d99ae;
        --background: #ffffff;
        --border-color: #edf2f4;
        --hover-bg: #f8f9fa;
        --border-radius: 8px;
        --box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
        --transition: all 0.2s ease-in-out;
    }

    * {
        margin: 0;
        padding: 0;
        box-sizing: border-box;
    }

    body {
        font-family: 'Inter', -apple-system, BlinkMacSystemFont, sans-serif;
        color: var(--text-color);
        background-color: #f8fafc;
        min-height: 100vh;
        display: flex;
        justify-content: center;
        align-items: center;
        padding: 20px;
    }

    .registration-container {
        max-width: 800px;
        width: 100%;
        background-color: var(--background);
        border-radius: var(--border-radius);
        box-shadow: var(--box-shadow);
        overflow: hidden;
        display: grid;
        grid-template-columns: 1fr 1fr;
    }

    .registration-image {
        background: linear-gradient(135deg, #4361ee, #3a0ca3);
        display: flex;
        flex-direction: column;
        justify-content: center;
        align-items: center;
        padding: 2rem;
        color: white;
        text-align: center;
    }

    .registration-image img {
        width: 150px;
        margin-bottom: 1.5rem;
    }

    .registration-image h2 {
        margin-bottom: 0.5rem;
        font-size: 1.5rem;
    }

    .registration-image p {
        opacity: 0.9;
        font-size: 0.9rem;
    }

    .registration-form {
        padding: 2.5rem;
    }

    .form-header {
        margin-bottom: 2rem;
        text-align: center;
    }

    .form-header h1 {
        font-size: 1.8rem;
        margin-bottom: 0.5rem;
        color: var(--primary-color);
    }

    .form-header p {
        color: var(--text-light);
        font-size: 0.9rem;
    }

    .avatar-upload {
        display: flex;
        flex-direction: column;
        align-items: center;
        margin-bottom: 1.5rem;
    }

    .avatar-preview {
        width: 100px;
        height: 100px;
        border-radius: 50%;
        border: 3px solid var(--border-color);
        overflow: hidden;
        margin-bottom: 1rem;
        position: relative;
    }

    .avatar-preview img {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }

    .avatar-placeholder {
        width: 100%;
        height: 100%;
        background-color: var(--hover-bg);
        display: flex;
        align-items: center;
        justify-content: center;
        color: var(--text-light);
    }

    .avatar-upload-btn {
        display: inline-block;
        padding: 0.5rem 1rem;
        background-color: var(--primary-color);
        color: white;
        border-radius: var(--border-radius);
        cursor: pointer;
        transition: var(--transition);
        font-size: 0.9rem;
        text-align: center;
    }

    .avatar-upload-btn:hover {
        background-color: var(--primary-hover);
    }

    #avatarInput {
        display: none;
    }

    .form-group {
        margin-bottom: 1.25rem;
    }

    .form-label {
        display: block;
        margin-bottom: 0.5rem;
        font-weight: 500;
        font-size: 0.9rem;
    }

    .form-control {
        width: 100%;
        padding: 0.75rem 1rem;
        border: 1px solid var(--border-color);
        border-radius: var(--border-radius);
        font-size: 0.95rem;
        transition: var(--transition);
    }

    .form-control:focus {
        outline: none;
        border-color: var(--primary-color);
        box-shadow: 0 0 0 2px rgba(67, 97, 238, 0.2);
    }

    .password-input {
        position: relative;
    }

    .password-toggle {
        position: absolute;
        right: 10px;
        top: 50%;
        transform: translateY(-50%);
        cursor: pointer;
        color: var(--text-light);
    }

    .form-footer {
        margin-top: 2rem;
    }

    .btn {
        width: 100%;
        padding: 0.75rem;
        border-radius: var(--border-radius);
        font-size: 1rem;
        font-weight: 500;
        cursor: pointer;
        transition: var(--transition);
        border: none;
    }

    .btn-primary {
        background-color: var(--primary-color);
        color: white;
    }

    .btn-primary:hover {
        background-color: var(--primary-hover);
    }

    .login-link {
        text-align: center;
        margin-top: 1.5rem;
        font-size: 0.9rem;
        color: var(--text-light);
    }

    .login-link a {
        color: var(--primary-color);
        text-decoration: none;
        font-weight: 500;
    }

    .login-link a:hover {
        text-decoration: underline;
    }

    .error-message {
        color: var(--error-color);
        font-size: 0.8rem;
        margin-top: 0.25rem;
        display: none;
    }

    .success-message {
        color: var(--success-color);
        font-size: 0.9rem;
        text-align: center;
        margin-top: 1rem;
        display: none;
    }

    @media (max-width: 768px) {
        .registration-container {
            grid-template-columns: 1fr;
        }
        
        .registration-image {
            padding: 1.5rem;
            display: none;
        }
        
        .registration-form {
            padding: 1.5rem;
        }
    }
</style>
</head>
<body>
  <div class="registration-container">
    <div class="registration-image">
      <img src="https://cdn-icons-png.flaticon.com/512/3135/3135715.png" alt="User registration" />
      <h2>Guide Me</h2>
      <p>Create your account and start your journey with us</p>
    </div>

    <div class="registration-form">
      <div class="form-header">
        <h1>Create Account</h1>
        <p>Fill in your details to register</p>
      </div>

      <form id="registerForm" action="register_action.php" method="POST" enctype="multipart/form-data">
  <div class="avatar-upload">
    <div class="avatar-preview" id="avatarPreview">
      <div class="avatar-placeholder">
        <i class="fas fa-user fa-2x"></i>
      </div>
    </div>
    <label for="avatarInput" class="avatar-upload-btn">
      <i class="fas fa-camera"></i> Upload Photo
    </label>
    <input type="file" id="avatarInput" name="avatar" accept="image/*" />
    <div class="error-message" id="avatarError">Please upload a profile picture</div>
  </div>

  <div class="form-group">
    <label for="fullName" class="form-label">Full Name</label>
    <input type="text" id="fullName" name="fullName" class="form-control" placeholder="Enter your full name" required />
    <div class="error-message" id="nameError">Please enter your full name</div>
  </div>

  <div class="form-group">
    <label for="email" class="form-label">Email Address</label>
    <input type="email" id="email" name="email" class="form-control" placeholder="Enter your email" required />
    <div class="error-message" id="emailError">Please enter a valid email address</div>
  </div>

  <div class="form-group">
    <label for="phone" class="form-label">Phone Number</label>
    <input type="tel" id="phone" name="phone" class="form-control" placeholder="Enter your phone number" />
    <div class="error-message" id="phoneError">Please enter a valid phone number</div>
  </div>

  <div class="form-group password-input">
    <label for="password" class="form-label">Password</label>
    <input type="password" id="password" name="password" class="form-control" placeholder="Create a password" required />
    <i class="fas fa-eye password-toggle" id="togglePassword"></i>
    <div class="error-message" id="passwordError">Password must be at least 8 characters</div>
  </div>

  <div class="form-group password-input">
    <label for="confirmPassword" class="form-label">Confirm Password</label>
    <input type="password" id="confirmPassword" name="confirmPassword" class="form-control" placeholder="Confirm your password" required />
    <i class="fas fa-eye password-toggle" id="toggleConfirmPassword"></i>
    <div class="error-message" id="confirmPasswordError">Passwords do not match</div>
  </div>

  <div class="form-group">
    <label class="form-label">
      <input type="checkbox" id="terms" name="terms" required /> I agree to the <a href="#">Terms & Conditions</a>
    </label>
    <div class="error-message" id="termsError">You must accept the terms and conditions</div>
  </div>
  <input type="hidden" name="role" value="user">

  <div class="form-footer">
    <button type="submit" class="btn btn-primary">Register Now</button>
  </div>

  <div class="success-message" id="successMessage">
    <i class="fas fa-check-circle"></i> Registration successful! Redirecting...
  </div>

  <div class="login-link">
    Already have an account? <a href="#">Log in</a>
  </div>
</form>

      
    </div>
  </div>

  <script>
    document.addEventListener('DOMContentLoaded', function () {
      const registerForm = document.getElementById('registerForm');
      const avatarInput = document.getElementById('avatarInput');
      const avatarPreview = document.getElementById('avatarPreview');
      const togglePassword = document.getElementById('togglePassword');
      const toggleConfirmPassword = document.getElementById('toggleConfirmPassword');
      const passwordInput = document.getElementById('password');
      const confirmPasswordInput = document.getElementById('confirmPassword');
      const successMessage = document.getElementById('successMessage');

      avatarInput.addEventListener('change', function (e) {
        const file = e.target.files[0];
        if (!file) return;

        if (!file.type.match('image.*')) {
          showError('avatarError', 'Please select an image file (JPEG, PNG)');
          return;
        }

        if (file.size > 2 * 1024 * 1024) {
          showError('avatarError', 'Image size should be less than 2MB');
          return;
        }

        const reader = new FileReader();
        reader.onload = function (e) {
          avatarPreview.innerHTML = `<img src="${e.target.result}" alt="Profile preview">`;
          hideError('avatarError');
        };
        reader.readAsDataURL(file);
      });

      togglePassword.addEventListener('click', function () {
        const type = passwordInput.getAttribute('type') === 'password' ? 'text' : 'password';
        passwordInput.setAttribute('type', type);
        togglePassword.classList.toggle('fa-eye-slash');
      });

      toggleConfirmPassword.addEventListener('click', function () {
        const type = confirmPasswordInput.getAttribute('type') === 'password' ? 'text' : 'password';
        confirmPasswordInput.setAttribute('type', type);
        toggleConfirmPassword.classList.toggle('fa-eye-slash');
      });

      registerForm.addEventListener('submit', function (e) {
        e.preventDefault();
        let isValid = true;
        // Validate avatar
        if (!avatarInput.files || avatarInput.files.length === 0) {
            showError('avatarError', 'Please upload a profile picture');
            isValid = false;
        } else {
            hideError('avatarError');
        }

        // Validate full name
        const fullName = document.getElementById('fullName').value.trim();
        if (fullName === '' || fullName.length < 3) {
            showError('nameError', 'Name must be at least 3 characters');
            isValid = false;
        } else {
            hideError('nameError');
        }

        // Validate email
        const email = document.getElementById('email').value.trim();
        if (!isValidEmail(email)) {
            showError('emailError', 'Please enter a valid email address');
            isValid = false;
        } else {
            hideError('emailError');
        }

        // Validate phone (optional)
        const phone = document.getElementById('phone').value.trim();
        if (phone && !/^\d{7,15}$/.test(phone)) {
            showError('phoneError', 'Please enter a valid phone number');
            isValid = false;
        } else {
            hideError('phoneError');
        }

        // Validate password
        const password = passwordInput.value.trim();
        if (password.length < 8) {
            showError('passwordError', 'Password must be at least 8 characters');
            isValid = false;
        } else {
            hideError('passwordError');
        }

        // Validate confirm password
        const confirmPassword = confirmPasswordInput.value.trim();
        if (confirmPassword !== password) {
            showError('confirmPasswordError', 'Passwords do not match');
            isValid = false;
        } else {
            hideError('confirmPasswordError');
        }

        // Validate terms checkbox
        const terms = document.getElementById('terms').checked;
        if (!terms) {
            showError('termsError', 'You must accept the terms and conditions');
            isValid = false;
        } else {
            hideError('termsError');
        }

        if (isValid) {
            const formData = new FormData(registerForm);
            
            fetch('register_action.php', {
                method: 'POST',
                body: formData
            })
            .then(response => {
                // First, check if the response is valid
                if (!response.ok) {
                    throw new Error(`HTTP error! Status: ${response.status}`);
                }
                return response.text(); // Get raw text first
            })
            .then(text => {
                console.log("Raw server response:", text); // Log the exact response
                try {
                    const data = JSON.parse(text); // Try parsing as JSON
                    if (data.success) {
                        successMessage.style.display = 'block';
                        // Redirect to verify_email.php with the email parameter
                        setTimeout(() => {
                            window.location.href = 'verify_email.php?email=' + encodeURIComponent(document.getElementById('email').value);
                        }, 2000);
                    } else {
                        alert('Registration failed: ' + (data.message || 'Unknown error'));
                    }
                } catch (e) {
                    console.error("Failed to parse JSON:", e, "Response:", text);
                    alert("Server returned invalid data. Check console for details.");
                }
            })
            .catch(error => {
                console.error("Fetch error:", error);
                alert("Registration error: " + error.message);
            });
        }
      });

      function showError(id, message) {
        const el = document.getElementById(id);
        el.textContent = message;
        el.style.display = 'block';
      }

      function hideError(id) {
        const el = document.getElementById(id);
        el.style.display = 'none';
      }

      function isValidEmail(email) {
        const regex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        return regex.test(email);
      }
    });
  </script>
</body>
</html>
