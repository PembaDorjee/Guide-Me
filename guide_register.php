<?php
session_start();
require_once '../database.php';

// Initialize variables
$errors = [];
$success = false;

// Process form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get form data
    $full_name = trim($_POST['full_name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $confirm_password = $_POST['confirm_password'] ?? '';
    $phone = trim($_POST['phone'] ?? '');
    $title = trim($_POST['title'] ?? '');
    $specialization = trim($_POST['specialization'] ?? '');
    $experience = trim($_POST['experience'] ?? '');
    $languages = $_POST['languages'] ?? [];
    $bio = trim($_POST['bio'] ?? '');
    
    // New form fields
    $certifications = $_POST['certifications'] ?? [];
    $social_facebook = trim($_POST['social_facebook'] ?? '');
    $social_instagram = trim($_POST['social_instagram'] ?? '');
    $social_twitter = trim($_POST['social_twitter'] ?? '');
    $social_linkedin = trim($_POST['social_linkedin'] ?? '');
    $worked_with = $_POST['worked_with'] ?? [];
    $achievements = $_POST['achievements'] ?? [];
    $stats_expeditions = intval($_POST['stats_expeditions'] ?? 0);
    $stats_summits = intval($_POST['stats_summits'] ?? 0);
    $stats_countries = intval($_POST['stats_countries'] ?? 0);
    
    // Validate form data
    if (empty($full_name)) {
        $errors[] = "Full name is required";
    }
    
    if (empty($email)) {
        $errors[] = "Email is required";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = "Invalid email format";
    }
    
    if (empty($password)) {
        $errors[] = "Password is required";
    } elseif (strlen($password) < 8) {
        $errors[] = "Password must be at least 8 characters long";
    }
    
    if ($password !== $confirm_password) {
        $errors[] = "Passwords do not match";
    }
    
    if (empty($phone)) {
        $errors[] = "Phone number is required";
    }
    
    if (empty($title)) {
        $errors[] = "Professional title is required";
    }
    
    if (empty($specialization)) {
        $errors[] = "Specialization is required";
    }
    
    if (empty($experience)) {
        $errors[] = "Experience is required";
    }
    
    if (empty($languages)) {
        $errors[] = "At least one language is required";
    }
    
    if (empty($bio)) {
        $errors[] = "Bio is required";
    }
    
    // Handle file upload
    $avatar_path = '';
    if (isset($_FILES['avatar']) && $_FILES['avatar']['error'] === UPLOAD_ERR_OK) {
        $upload_dir = '../uploads/guides/';
        
        // Create directory if it doesn't exist
        if (!file_exists($upload_dir)) {
            mkdir($upload_dir, 0777, true);
        }
        
        $file_name = time() . '_' . basename($_FILES['avatar']['name']);
        $target_file = $upload_dir . $file_name;
        $file_type = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));
        
        // Check if image file is a actual image
        $check = getimagesize($_FILES['avatar']['tmp_name']);
        if ($check === false) {
            $errors[] = "File is not an image";
        }
        
        // Check file size (limit to 5MB)
        if ($_FILES['avatar']['size'] > 5000000) {
            $errors[] = "File is too large (max 5MB)";
        }
        
        // Allow certain file formats
        if ($file_type != "jpg" && $file_type != "png" && $file_type != "jpeg") {
            $errors[] = "Only JPG, JPEG, PNG files are allowed";
        }
        
        // If no errors, try to upload file
        if (empty($errors)) {
            if (move_uploaded_file($_FILES['avatar']['tmp_name'], $target_file)) {
                $avatar_path = 'uploads/guides/' . $file_name;
            } else {
                $errors[] = "Failed to upload image";
            }
        }
    }
    
    // Handle certification files upload
    $certification_files = [];
    if (isset($_FILES['certification_files']) && !empty($_FILES['certification_files']['name'][0])) {
        $upload_dir = '../uploads/certifications/';
        
        // Create directory if it doesn't exist
        if (!file_exists($upload_dir)) {
            mkdir($upload_dir, 0777, true);
        }
        
        // Loop through each uploaded file
        $file_count = count($_FILES['certification_files']['name']);
        for ($i = 0; $i < $file_count; $i++) {
            if ($_FILES['certification_files']['error'][$i] === UPLOAD_ERR_OK) {
                $file_name = time() . '_' . basename($_FILES['certification_files']['name'][$i]);
                $target_file = $upload_dir . $file_name;
                $file_type = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));
                
                // Check if file is a valid document
                if ($file_type != "pdf" && $file_type != "jpg" && $file_type != "jpeg" && $file_type != "png") {
                    $errors[] = "Only PDF, JPG, JPEG, PNG files are allowed for certifications";
                    continue;
                }
                
                // Check file size (limit to 5MB)
                if ($_FILES['certification_files']['size'][$i] > 5000000) {
                    $errors[] = "Certification file is too large (max 5MB)";
                    continue;
                }
                
                // Try to upload file
                if (move_uploaded_file($_FILES['certification_files']['tmp_name'][$i], $target_file)) {
                    $certification_files[] = 'uploads/certifications/' . $file_name;
                } else {
                    $errors[] = "Failed to upload certification file";
                }
            }
        }
    }
    
    // If no errors, insert into database
    if (empty($errors)) {
        try {
            // Hash password
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);
            
            // Generate verification code
            $verification_code = sprintf("%06d", mt_rand(100000, 999999));
            
            // Convert arrays to JSON strings
            $languages_str = implode(', ', $languages);
            $worked_with_str = json_encode($worked_with);
            $certifications_str = json_encode($certifications);
            $certification_paths_str = json_encode($certification_files);
            $achievements_str = json_encode($achievements);
            $social_media = json_encode([
                'facebook' => $social_facebook,
                'instagram' => $social_instagram,
                'twitter' => $social_twitter,
                'linkedin' => $social_linkedin
            ]);
            $stats_json = json_encode([
                'expeditions' => $stats_expeditions,
                'summits' => $stats_summits,
                'countries' => $stats_countries
            ]);
            
            // Insert guide into database
            $stmt = $conn->prepare("INSERT INTO Guide (full_name, email, password, phone, title, specialization, experience, languages, bio, avatar_path, social_media, worked_with, certifications, certification_files, achievements, stats, verification_code, status, created_at) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 'pending', NOW())");
            $stmt->bind_param("sssssssssssssssss", $full_name, $email, $hashed_password, $phone, $title, $specialization, $experience, $languages_str, $bio, $avatar_path, $social_media, $worked_with_str, $certifications_str, $certification_paths_str, $achievements_str, $stats_json, $verification_code);
            
            if ($stmt->execute()) {
                $success = true;
                
                // Send verification email
                require_once '../vendor/autoload.php';
                
                $mail = new PHPMailer\PHPMailer\PHPMailer(true);
                try {
                    //Server settings
                    $mail->isSMTP();
                    $mail->Host = 'smtp.gmail.com';  // SMTP server
                    $mail->SMTPAuth = true;
                    $mail->Username = 'np03cs4a220505@heraldcollege.edu.np';  // Your Gmail address
                    $mail->Password = 'wbqq vffc nzay lnvm';  // The app-specific password
                    $mail->SMTPSecure = PHPMailer\PHPMailer\PHPMailer::ENCRYPTION_STARTTLS;
                    $mail->Port = 25;  // Port number for Gmail SMTP
                    
                    //Recipients
                    $mail->setFrom('np03cs4a220505@heraldcollege.edu.np', 'Guide Me');
                    $mail->addAddress($email);  // Recipient's email
                    
                    // Content
                    $mail->isHTML(true);
                    $mail->Subject = 'Verify Your Guide Account';
                    $mail->Body = "
                        <div style='font-family: Arial, sans-serif; max-width: 600px; margin: 0 auto; padding: 20px; border: 1px solid #eee; border-radius: 10px;'>
                            <h2 style='color: #3a4b5e; text-align: center;'>Welcome to Guide Me!</h2>
                            <p>Hello $full_name,</p>
                            <p>Thank you for registering as a guide with us. Please use the verification code below to complete your registration:</p>
                            <div style='background-color: #f8f9fa; padding: 15px; border-radius: 5px; text-align: center; margin: 20px 0;'>
                                <h3 style='margin: 0; color: #333; letter-spacing: 5px;'>$verification_code</h3>
                            </div>
                            <p>If you did not request this verification, please ignore this email.</p>
                            <p>Best regards,<br>The Guide Me Team</p>
                        </div>
                    ";
                    
                    $mail->send();
                    
                    // Redirect to verification page
                    header("Location: guide_verify.php?email=" . urlencode($email));
                    exit();
                } catch (Exception $e) {
                    $errors[] = "Email could not be sent. Mailer Error: {$mail->ErrorInfo}";
                }
            } else {
                $errors[] = "Database error: " . $stmt->error;
            }
            
            $stmt->close();
        } catch (Exception $e) {
            $errors[] = "Error: " . $e->getMessage();
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Guide Registration | Guide Me</title>
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
            overflow-x: hidden;
        }
        
        .register-container {
            max-width: 1000px;
            margin: 50px auto;
            padding: 0 25px;
        }
        
        .register-card {
            background: white;
            border-radius: 20px;
            box-shadow: var(--card-shadow);
            overflow: hidden;
            margin-bottom: 60px;
            transition: all 0.3s ease;
        }
        
        .register-header {
            background: var(--primary-dark);
            color: white;
            padding: 40px;
            text-align: center;
            position: relative;
            overflow: hidden;
            clip-path: polygon(0 0, 100% 0, 100% 90%, 0 100%);
        }
        
        .register-title {
            font-family: 'Montserrat', sans-serif;
            font-size: 2.5rem;
            font-weight: 700;
            margin-bottom: 15px;
            text-shadow: 0 2px 5px rgba(0,0,0,0.1);
        }
        
        .register-subtitle {
            font-size: 1.1rem;
            opacity: 0.9;
            max-width: 600px;
            margin: 0 auto;
        }
        
        .register-body {
            padding: 40px;
        }
        
        .form-section {
            margin-bottom: 40px;
        }
        
        .section-title {
            color: var(--primary-dark);
            font-family: 'Montserrat', sans-serif;
            font-size: 1.3rem;
            font-weight: 600;
            margin-bottom: 20px;
            padding-bottom: 10px;
            border-bottom: 1px solid rgba(0,0,0,0.1);
            display: flex;
            align-items: center;
            position: relative;
        }
        
        .section-title::after {
            content: '';
            position: absolute;
            bottom: -1px;
            left: 0;
            width: 50px;
            height: 3px;
            background: var(--accent-color);
            border-radius: 3px;
        }
        
        .section-title i {
            margin-right: 10px;
            color: var(--accent-color);
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
        
        .form-text {
            font-size: 0.85rem;
            color: var(--text-light);
        }
        
        .avatar-preview {
            width: 150px;
            height: 150px;
            border-radius: 50%;
            border: 3px solid var(--accent-color);
            overflow: hidden;
            margin: 0 auto 20px;
            position: relative;
            background: #f0f0f0;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        
        .avatar-preview img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }
        
        .avatar-preview .placeholder {
            color: #aaa;
            font-size: 3rem;
        }
        
        .language-options {
            display: flex;
            flex-wrap: wrap;
            gap: 15px;
            margin-top: 10px;
        }
        
        .language-option {
            display: flex;
            align-items: center;
            gap: 8px;
            background: rgba(243, 164, 46, 0.1);
            padding: 8px 15px;
            border-radius: 50px;
            cursor: pointer;
            transition: all 0.3s ease;
        }
        
        .language-option:hover {
            background: rgba(243, 164, 46, 0.2);
        }
        
        .language-option input {
            margin: 0;
        }
        
        .btn-register {
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
            margin-top: 20px;
        }
        
        .btn-register:hover {
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
        
        .login-link {
            text-align: center;
            margin-top: 30px;
            font-size: 1rem;
        }
        
        .login-link a {
            color: var(--accent-color);
            font-weight: 600;
            text-decoration: none;
            transition: all 0.3s ease;
        }
        
        .login-link a:hover {
            text-decoration: underline;
        }
        
        .social-inputs {
            display: flex;
            flex-wrap: wrap;
            gap: 15px;
        }
        
        .social-input {
            flex: 1;
            min-width: 200px;
        }
        
        .social-input .input-group-text {
            background-color: var(--primary-dark);
            color: white;
            border: none;
        }
        
        .certification-item {
            background: white;
            border-radius: 10px;
            padding: 15px;
            margin-bottom: 15px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.05);
        }
        
        .achievement-item {
            margin-bottom: 10px;
        }
        
        .achievement-input {
            display: flex;
            align-items: center;
        }
    </style>
</head>
<body>
    <div class="register-container">
        <div class="register-card">
            <div class="register-header">
                <h1 class="register-title">Become a Guide</h1>
                <p class="register-subtitle">Join our community of professional guides and share your expertise with adventure seekers around the world.</p>
            </div>
            
            <div class="register-body">
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
                        <i class="fas fa-check-circle me-2"></i> Registration successful! You can now login.
                    </div>
                <?php endif; ?>
                
                <form action="guide_register.php" method="POST" enctype="multipart/form-data">
                    <div class="form-section">
                        <h3 class="section-title"><i class="fas fa-user"></i> Personal Information</h3>
                        
                        <div class="avatar-preview">
                            <img id="avatar-img" src="" alt="Profile Preview" style="display: none;">
                            <div class="placeholder"><i class="fas fa-user"></i></div>
                        </div>
                        
                        <div class="form-group text-center">
                            <label for="avatar" class="form-label">Profile Photo</label>
                            <input type="file" class="form-control" id="avatar" name="avatar" accept="image/*" style="max-width: 300px; margin: 0 auto;">
                            <div class="form-text">Upload a professional photo (JPG, PNG, max 5MB)</div>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="full_name" class="form-label">Full Name</label>
                                    <input type="text" class="form-control" id="full_name" name="full_name" value="<?php echo htmlspecialchars($full_name ?? ''); ?>" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="title" class="form-label">Professional Title</label>
                                    <input type="text" class="form-control" id="title" name="title" placeholder="e.g., Mountain Guide, Expedition Leader" value="<?php echo htmlspecialchars($title ?? ''); ?>" required>
                                </div>
                            </div>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="email" class="form-label">Email Address</label>
                                    <input type="email" class="form-control" id="email" name="email" value="<?php echo htmlspecialchars($email ?? ''); ?>" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="phone" class="form-label">Phone Number</label>
                                    <input type="tel" class="form-control" id="phone" name="phone" value="<?php echo htmlspecialchars($phone ?? ''); ?>" required>
                                </div>
                            </div>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="password" class="form-label">Password</label>
                                    <input type="password" class="form-control" id="password" name="password" required>
                                    <div class="form-text">At least 8 characters</div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="confirm_password" class="form-label">Confirm Password</label>
                                    <input type="password" class="form-control" id="confirm_password" name="confirm_password" required>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="form-section">
                        <h3 class="section-title"><i class="fas fa-mountain"></i> Professional Details</h3>
                        
                        <div class="form-group">
                            <label for="specialization" class="form-label">Specialization</label>
                            <input type="text" class="form-control" id="specialization" name="specialization" placeholder="e.g., Mountaineering, Trekking, Rock Climbing" value="<?php echo htmlspecialchars($specialization ?? ''); ?>" required>
                        </div>
                        
                        <div class="form-group">
                            <label for="experience" class="form-label">Years of Experience</label>
                            <select class="form-control" id="experience" name="experience" required>
                                <option value="">Select experience</option>
                                <option value="1-3 years" <?php echo ($experience ?? '') === '1-3 years' ? 'selected' : ''; ?>>1-3 years</option>
                                <option value="4-6 years" <?php echo ($experience ?? '') === '4-6 years' ? 'selected' : ''; ?>>4-6 years</option>
                                <option value="7-10 years" <?php echo ($experience ?? '') === '7-10 years' ? 'selected' : ''; ?>>7-10 years</option>
                                <option value="10+ years" <?php echo ($experience ?? '') === '10+ years' ? 'selected' : ''; ?>>10+ years</option>
                                <option value="20+ years" <?php echo ($experience ?? '') === '20+ years' ? 'selected' : ''; ?>>20+ years</option>
                            </select>
                        </div>
                        
                        <div class="form-group">
                            <label class="form-label">Languages Spoken</label>
                            <div class="language-options">
                                <label class="language-option">
                                    <input type="checkbox" name="languages[]" value="English" <?php echo in_array('English', $languages ?? []) ? 'checked' : ''; ?>>
                                    English
                                </label>
                                <label class="language-option">
                                    <input type="checkbox" name="languages[]" value="Spanish" <?php echo in_array('Spanish', $languages ?? []) ? 'checked' : ''; ?>>
                                    Spanish
                                </label>
                                <label class="language-option">
                                    <input type="checkbox" name="languages[]" value="French" <?php echo in_array('French', $languages ?? []) ? 'checked' : ''; ?>>
                                    French
                                </label>
                                <label class="language-option">
                                    <input type="checkbox" name="languages[]" value="German" <?php echo in_array('German', $languages ?? []) ? 'checked' : ''; ?>>
                                    German
                                </label>
                                <label class="language-option">
                                    <input type="checkbox" name="languages[]" value="Italian" <?php echo in_array('Italian', $languages ?? []) ? 'checked' : ''; ?>>
                                    Italian
                                </label>
                                <label class="language-option">
                                    <input type="checkbox" name="languages[]" value="Mandarin" <?php echo in_array('Mandarin', $languages ?? []) ? 'checked' : ''; ?>>
                                    Mandarin
                                </label>
                                <label class="language-option">
                                    <input type="checkbox" name="languages[]" value="Japanese" <?php echo in_array('Japanese', $languages ?? []) ? 'checked' : ''; ?>>
                                    Japanese
                                </label>
                                <label class="language-option">
                                    <input type="checkbox" name="languages[]" value="Russian" <?php echo in_array('Russian', $languages ?? []) ? 'checked' : ''; ?>>
                                    Russian
                                </label>
                                <label class="language-option">
                                    <input type="checkbox" name="languages[]" value="Arabic" <?php echo in_array('Arabic', $languages ?? []) ? 'checked' : ''; ?>>
                                    Arabic
                                </label>
                                <label class="language-option">
                                    <input type="checkbox" name="languages[]" value="Hindi" <?php echo in_array('Hindi', $languages ?? []) ? 'checked' : ''; ?>>
                                    Hindi
                                </label>
                                <label class="language-option">
                                    <input type="checkbox" name="languages[]" value="Nepali" <?php echo in_array('Nepali', $languages ?? []) ? 'checked' : ''; ?>>
                                    Nepali
                                </label>
                                <label class="language-option">
                                    <input type="checkbox" name="languages[]" value="Other" <?php echo in_array('Other', $languages ?? []) ? 'checked' : ''; ?>>
                                    Other
                                </label>
                            </div>
                        </div>
                        
                        <div class="form-group">
                            <label for="bio" class="form-label">Bio / About Me</label>
                            <textarea class="form-control" id="bio" name="bio" rows="5" placeholder="Tell us about yourself, your experience, achievements, and what makes you a great guide..."><?php echo htmlspecialchars($bio ?? ''); ?></textarea>
                        </div>
                    </div>
                    
                    <div class="form-section">
                        <h3 class="section-title"><i class="fas fa-certificate"></i> Certifications</h3>
                        
                        <div id="certifications-container">
                            <div class="certification-item">
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label class="form-label">Certification Name</label>
                                            <input type="text" class="form-control" name="certifications[]" placeholder="e.g., IFMGA Mountain Guide, Wilderness First Responder">
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label class="form-label">Upload Certificate (Optional)</label>
                                            <input type="file" class="form-control" name="certification_files[]" accept=".pdf,.jpg,.jpeg,.png">
                                        </div>
                                    </div>
                                </div>
                                <button type="button" class="btn btn-sm btn-outline-danger remove-cert" style="display:none;"><i class="fas fa-times"></i> Remove</button>
                            </div>
                        </div>
                        
                        <button type="button" class="btn btn-sm btn-outline-primary mt-2" id="add-certification">
                            <i class="fas fa-plus"></i> Add Another Certification
                        </button>
                    </div>
                    
                    <div class="form-section">
                        <h3 class="section-title"><i class="fas fa-link"></i> Social Media</h3>
                        
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="form-label"><i class="fab fa-facebook-f"></i> Facebook</label>
                                    <input type="text" class="form-control" name="social_facebook" placeholder="Facebook profile URL">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="form-label"><i class="fab fa-instagram"></i> Instagram</label>
                                    <input type="text" class="form-control" name="social_instagram" placeholder="Instagram profile URL">
                                </div>
                            </div>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="form-label"><i class="fab fa-twitter"></i> Twitter</label>
                                    <input type="text" class="form-control" name="social_twitter" placeholder="Twitter profile URL">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="form-label"><i class="fab fa-linkedin-in"></i> LinkedIn</label>
                                    <input type="text" class="form-control" name="social_linkedin" placeholder="LinkedIn profile URL">
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="form-section">
                        <h3 class="section-title"><i class="fas fa-handshake"></i> Professional Collaborations</h3>
                        
                        <div class="form-group">
                            <label class="form-label">Organizations/People You've Worked With</label>
                            <div id="worked-with-container">
                                <!-- Worked with items will be added here -->
                                <div class="input-group mb-3">
                                    <input type="text" class="form-control" name="worked_with[]" placeholder="e.g., National Geographic, Conrad Anker">
                                    <button class="btn btn-outline-danger remove-worked-with" type="button" style="display:none;"><i class="fas fa-times"></i></button>
                                </div>
                            </div>
                            
                            <div class="input-group mb-3">
                                <input type="text" class="form-control" id="worked-with-input" placeholder="Add another organization">
                                <button class="btn btn-outline-secondary" type="button" id="add-worked-with">
                                    <i class="fas fa-plus"></i> Add
                                </button>
                            </div>
                        </div>
                    </div>
                    
                    <div class="form-section">
                        <h3 class="section-title"><i class="fas fa-trophy"></i> Achievements</h3>
                        
                        <div id="achievements-container">
                            <div class="achievement-item">
                                <div class="achievement-input input-group mb-3">
                                    <span class="input-group-text"><i class="fas fa-medal"></i></span>
                                    <input type="text" class="form-control" name="achievements[]" placeholder="e.g., Summited Everest 3 times, First ascent of South Face">
                                </div>
                            </div>
                        </div>
                        <button type="button" class="btn btn-sm btn-outline-primary" id="add-achievement">
                            <i class="fas fa-plus"></i> Add Achievement
                        </button>
                    </div>
                    
                    <div class="form-section">
                        <h3 class="section-title"><i class="fas fa-map-marked-alt"></i> Trekking Statistics</h3>
                        
                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label class="form-label">Treks Led</label>
                                    <input type="number" class="form-control" name="stats_expeditions" min="0" placeholder="e.g., 45">
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label class="form-label">Treks Completed</label>
                                    <input type="number" class="form-control" name="stats_summits" min="0" placeholder="e.g., 28">
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label class="form-label">Countries Guided</label>
                                    <input type="number" class="form-control" name="stats_countries" min="0" placeholder="e.g., 12">
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="text-center">
                        <button type="submit" class="btn-register">
                            <i class="fas fa-user-plus"></i> Register as Guide
                        </button>
                        
                        <div class="login-link">
                            Already have an account? <a href="guide_login.php">Login here</a>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Preview uploaded avatar image
        document.getElementById('avatar').addEventListener('change', function(e) {
            const file = e.target.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    const img = document.getElementById('avatar-img');
                    img.src = e.target.result;
                    img.style.display = 'block';
                    document.querySelector('.avatar-preview .placeholder').style.display = 'none';
                }
                reader.readAsDataURL(file);
            }
        });
        
        // Add new certification field
        document.getElementById('add-certification').addEventListener('click', function() {
            const container = document.getElementById('certifications-container');
            const newItem = container.querySelector('.certification-item').cloneNode(true);
            
            // Clear input values
            newItem.querySelectorAll('input').forEach(input => {
                input.value = '';
            });
            
            // Show remove button
            const removeBtn = newItem.querySelector('.remove-cert');
            removeBtn.style.display = 'block';
            
            // Add event listener to remove button
            removeBtn.addEventListener('click', function() {
                container.removeChild(newItem);
            });
            
            container.appendChild(newItem);
        });
        
        // Add worked with
        document.getElementById('add-worked-with').addEventListener('click', function() {
            const container = document.getElementById('worked-with-container');
            const input = document.getElementById('worked-with-input');
            
            if (input.value.trim() !== '') {
                const newItem = container.querySelector('.input-group').cloneNode(true);
                newItem.querySelector('input').value = input.value;
                
                // Show and enable remove button
                const removeBtn = newItem.querySelector('.remove-worked-with');
                removeBtn.style.display = 'block';
                removeBtn.addEventListener('click', function() {
                    container.removeChild(newItem);
                });
                
                container.appendChild(newItem);
                input.value = '';
            }
        });
        
        // Add achievement
        document.getElementById('add-achievement').addEventListener('click', function() {
            const container = document.getElementById('achievements-container');
            const newItem = container.querySelector('.achievement-item').cloneNode(true);
            
            // Clear input value
            newItem.querySelector('input').value = '';
            
            // Add remove button functionality
            const removeBtn = document.createElement('button');
            removeBtn.type = 'button';
            removeBtn.className = 'btn btn-sm btn-outline-danger ms-2';
            removeBtn.innerHTML = '<i class="fas fa-times"></i>';
            removeBtn.addEventListener('click', function() {
                container.removeChild(newItem);
            });
            
            newItem.querySelector('.achievement-input').appendChild(removeBtn);
            container.appendChild(newItem);
        });
        
        // Initialize remove buttons for existing items
        document.querySelectorAll('.remove-worked-with').forEach(btn => {
            btn.addEventListener('click', function() {
                this.closest('.input-group').remove();
            });
        });
    </script>
</body>
</html>