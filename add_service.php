<?php
session_start();
require_once '../database.php';

// Check if user is logged in and is admin
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../login.php?error=unauthorized");
    exit();
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get form data
    $name = $_POST['name'] ?? '';
    $description = $_POST['description'] ?? '';
    $price = $_POST['price'] ?? 0;
    $duration = $_POST['duration'] ?? 1;
    $difficulty = $_POST['difficulty'] ?? 'Moderate';
    $location = $_POST['location'] ?? '';
    $status = $_POST['status'] ?? 'active';
    $featured = isset($_POST['featured']) ? 1 : 0;
    
    // Validate required fields
    $errors = [];
    
    if (empty($name)) {
        $errors[] = "Service name is required";
    }
    
    if (empty($description)) {
        $errors[] = "Description is required";
    }
    
    if (!is_numeric($price) || $price < 0) {
        $errors[] = "Price must be a valid number";
    }
    
    if (!is_numeric($duration) || $duration < 1) {
        $errors[] = "Duration must be at least 1 day";
    }
    
    // If no errors, process image upload and save service
    if (empty($errors)) {
        $imagePath = '';
        
        // Handle image upload
        if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
            $uploadDir = '../uploads/services/';
            
            // Create directory if it doesn't exist
            if (!file_exists($uploadDir)) {
                mkdir($uploadDir, 0777, true);
            }
            
            // Generate unique filename
            $fileName = uniqid() . '_' . basename($_FILES['image']['name']);
            $uploadFile = $uploadDir . $fileName;
            
            // Check if file is an image
            $imageFileType = strtolower(pathinfo($uploadFile, PATHINFO_EXTENSION));
            $allowedTypes = ['jpg', 'jpeg', 'png', 'gif'];
            
            if (!in_array($imageFileType, $allowedTypes)) {
                $errors[] = "Only JPG, JPEG, PNG & GIF files are allowed";
            } else {
                // Upload the file
                if (move_uploaded_file($_FILES['image']['tmp_name'], $uploadFile)) {
                    $imagePath = 'uploads/services/' . $fileName;
                } else {
                    $errors[] = "Failed to upload image";
                }
            }
        }
        
        // If still no errors, save to database
        if (empty($errors)) {
            $query = "INSERT INTO services (name, description, price, duration, difficulty, location, image, status, featured, created_at) 
                      VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, NOW())";
            
            $stmt = $conn->prepare($query);
            $stmt->bind_param("ssddsssi", $name, $description, $price, $duration, $difficulty, $location, $imagePath, $status, $featured);
            
            if ($stmt->execute()) {
                // Redirect to services page with success message
                header("Location: services.php?success=Service added successfully");
                exit();
            } else {
                $errors[] = "Database error: " . $conn->error;
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Add New Service - Admin Dashboard</title>
  <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;500;600;700&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
  <style>
    /* Same styles as services.php */
    /* ... (copy all the styles from services.php) ... */
    
    /* Additional styles for form */
    .form-container {
      background-color: white;
      border-radius: var(--border-radius);
      padding: 2rem;
      box-shadow: var(--box-shadow);
    }
    
    .form-group {
      margin-bottom: 1.5rem;
    }
    
    .form-label {
      display: block;
      margin-bottom: 0.5rem;
      font-weight: 500;
      color: #555;
    }
    
    .form-control {
      width: 100%;
      padding: 0.75rem 1rem;
      border: 1px solid #ddd;
      border-radius: 6px;
      font-size: 1rem;
      transition: border-color 0.3s;
    }
    
    .form-control:focus {
      border-color: var(--secondary-color);
      outline: none;
    }
    
    textarea.form-control {
      min-height: 150px;
      resize: vertical;
    }
    
    .form-select {
      width: 100%;
      padding: 0.75rem 1rem;
      border: 1px solid #ddd;
      border-radius: 6px;
      font-size: 1rem;
      appearance: none;
      background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='16' height='16' fill='%23555' viewBox='0 0 16 16'%3E%3Cpath d='M7.247 11.14 2.451 5.658C1.885 5.013 2.345 4 3.204 4h9.592a1 1 0 0 1 .753 1.659l-4.796 5.48a1 1 0 0 1-1.506 0z'/%3E%3C/svg%3E");
      background-repeat: no-repeat;
      background-position: right 1rem center;
      background-size: 16px 12px;
    }
    
    .form-check {
      display: flex;
      align-items: center;
      gap: 0.5rem;
      margin-bottom: 0.5rem;
    }
    
    .form-check-input {
      width: 18px;
      height: 18px;
    }
    
    .btn-group {
      display: flex;
      gap: 1rem;
      margin-top: 2rem;
    }
    
    .btn {
      padding: 0.75rem 1.5rem;
      border-radius: 6px;
      font-weight: 500;
      cursor: pointer;
      transition: all 0.3s;
    }
    
    .btn-primary {
      background-color: var(--secondary-color);
      color: white;
      border: none;
    }
    
    .btn-primary:hover {
      background-color: #d48826;
    }
    
    .btn-secondary {
      background-color: #6c757d;
      color: white;
      border: none;
    }
    
    .btn-secondary:hover {
      background-color: #5a6268;
    }
    
    .error-list {
      background-color: rgba(220, 53, 69, 0.1);
      border: 1px solid rgba(220, 53, 69, 0.2);
      color: var(--danger-color);
      padding: 1rem;
      border-radius: var(--border-radius);
      margin-bottom: 1.5rem;
    }
    
    .error-list ul {
      margin: 0;
      padding-left: 1.5rem;
    }
    
    .image-preview {
      width: 100%;
      height: 200px;
      border-radius: 8px;
      border: 1px dashed #ddd;
      display: flex;
      align-items: center;
      justify-content: center;
      margin-bottom: 1rem;
      overflow: hidden;
    }
    
    .image-preview img {
      max-width: 100%;
      max-height: 100%;
      object-fit: contain;
    }
    
    .file-input-wrapper {
      position: relative;
      overflow: hidden;
      display: inline-block;
    }
    
    .file-input-wrapper input[type=file] {
      position: absolute;
      left: 0;
      top: 0;
      opacity: 0;
      width: 100%;
      height: 100%;
      cursor: pointer;
    }
    
    .file-input-btn {
      background-color: #f8f9fa;
      border: 1px solid #ddd;
      padding: 0.5rem 1rem;
      border-radius: 4px;
      font-size: 0.9rem;
      cursor: pointer;
      display: inline-block;
    }
    
    .file-name {
      margin-left: 0.5rem;
      font-size: 0.9rem;
      color: #777;
    }
  </style>
</head>
<body>
  <!-- Sidebar -->
  <aside class="sidebar">
    <div class="sidebar-header">
      <div class="logo">
        <i class="fas fa-mountain"></i>
        Guide<span>Me</span>
      </div>
    </div>
    
    <ul class="sidebar-menu">
      <li><a href="index.php"><i class="fas fa-tachometer-alt"></i> Dashboard</a></li>
      <li><a href="users.php"><i class="fas fa-users"></i> Users</a></li>
      <li><a href="guides.php"><i class="fas fa-hiking"></i> Guides</a></li>
      <li><a href="services.php" class="active"><i class="fas fa-map-marked-alt"></i> Services</a></li>
      <li><a href="bookings.php"><i class="fas fa-calendar-check"></i> Bookings</a></li>
      <li><a href="reviews.php"><i class="fas fa-star"></i> Reviews</a></li>
      <li><a href="messages.php"><i class="fas fa-envelope"></i> Messages</a></li>
      <li><a href="settings.php"><i class="fas fa-cog"></i> Settings</a></li>
      <li><a href="../logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a></li>
    </ul>
  </aside>
  
  <!-- Header -->
  <header class="header">
    <button class="toggle-sidebar">
      <i class="fas fa-bars"></i>
    </button>
    
    <div class="header-right">
      <div class="notification">
        <i class="fas fa-bell"></i>
        <span class="notification-count">3</span>
      </div>
      
      <div class="admin-profile">
        <img src="https://randomuser.me/api/portraits/men/32.jpg" alt="Admin" class="admin-avatar">
        <div class="admin-info">
          <h4>Admin User</h4>
          <p>Administrator</p>
        </div>
      </div>
    </div>
  </header>
  
  <!-- Main Content -->
  <main class="main-content">
    <div class="services-header">
      <h1 class="page-title">Add New Service</h1>
    </div>
    
    <?php if (!empty($errors)): ?>
    <div class="error-list">
      <ul>
        <?php foreach ($errors as $error): ?>
          <li><?php echo htmlspecialchars($error); ?></li>
        <?php endforeach; ?>
      </ul>
    </div>
    <?php endif; ?>
    
    <div class="form-container">
      <form action="add_service.php" method="post" enctype="multipart/form-data">
        <div class="form-group">
          <label for="name" class="form-label">Service Name</label>
          <input type="text" id="name" name="name" class="form-control" value="<?php echo isset($name) ? htmlspecialchars($name) : ''; ?>" required>
        </div>
        
        <div class="form-group">
          <label for="description" class="form-label">Description</label>
          <textarea id="description" name="description" class="form-control" required><?php echo isset($description) ? htmlspecialchars($description) : ''; ?></textarea>
        </div>
        
        <div class="form-group">
          <label for="price" class="form-label">Price ($)</label>
          <input type="number" id="price" name="price" class="form-control" min="0" step="0.01" value="<?php echo isset($price) ? htmlspecialchars($price) : ''; ?>" required>
        </div>
        
        <div class="form-group">
          <label for="duration" class="form-label">Duration (days)</label>
          <input type="number" id="duration" name="duration" class="form-control" min="1" value="<?php echo isset($duration) ? htmlspecialchars($duration) : ''; ?>" required>
        </div>
        
        <div class="form-group">
          <label for="difficulty" class="form-label">Difficulty Level</label>
          <select id="difficulty" name="difficulty" class="form-select">
            <option value="Easy" <?php echo (isset($difficulty) && $difficulty === 'Easy') ? 'selected' : ''; ?>>Easy</option>
            <option value="Moderate" <?php echo (isset($difficulty) && $difficulty === 'Moderate') ? 'selected' : ''; ?>>Moderate</option>
            <option value="Difficult" <?php echo (isset($difficulty) && $difficulty === 'Difficult') ? 'selected' : ''; ?>>Difficult</option>
            <option value="Extreme" <?php echo (isset($difficulty) && $difficulty === 'Extreme') ? 'selected' : ''; ?>>Extreme</option>
          </select>
        </div>
        
        <div class="form-group">
          <label for="location" class="form-label">Location</label>
          <input type="text" id="location" name="location" class="form-control" value="<?php echo isset($location) ? htmlspecialchars($location) : ''; ?>" required>
        </div>
        
        <div class="form-group">
          <label class="form-label">Service Image</label>
          <div class="image-preview" id="imagePreview">
            <span id="imagePreviewText">No image selected</span>
          </div>
          <div class="file-input-wrapper">
            <span class="file-input-btn">Choose Image</span>
            <input type="file" id="image" name="image" accept="image/*">
          </div>
          <span class="file-name" id="fileName"></span>
        </div>
        
        <div class="form-group">
          <label class="form-label">Status</label>
          <div class="form-check">
            <input type="radio" id="statusActive" name="status" value="active" <?php echo (!isset($status) || $status === 'active') ? 'checked' : ''; ?>>
            <label for="statusActive">Active</label>
          </div>
          <div class="form-check">
            <input type="radio" id="statusInactive" name="status" value="inactive" <?php echo (isset($status) && $status === 'inactive') ? 'checked' : ''; ?>>
            <label for="statusInactive">Inactive</label>
          </div>
        </div>
        
        <div class="form-check">
          <input type="checkbox" id="featured" name="featured" value="1" <?php echo (isset($featured) && $featured) ? 'checked' : ''; ?>>
          <label for="featured">Featured Service</label>
        </div>
        
        <div class="btn-group">
          <a href="services.php" class="btn btn-secondary">Cancel</a>
          <button type="submit" class="btn btn-primary">Save Service</button>
        </div>
      </form>
    </div>
  </main>
  
  <script>
    // Toggle sidebar on mobile
    const toggleBtn = document.querySelector('.toggle-sidebar');
    const sidebar = document.querySelector('.sidebar');
    
    toggleBtn.addEventListener('click', () => {
      sidebar.classList.toggle('active');
    });
    
    // Close sidebar when clicking outside on mobile
    document.addEventListener('click', (e) => {
      if (window.innerWidth < 992) {
        if (!sidebar.contains(e.target) && !toggleBtn.contains(e.target)) {
          sidebar.classList.remove('active');
        }
      }
    });
    
    // Image preview
    const imageInput = document.getElementById('image');
    const imagePreview = document.getElementById('imagePreview');
    const imagePreviewText = document.getElementById('imagePreviewText');
    const fileName = document.getElementById('fileName');
    
    imageInput.addEventListener('change', function() {
      const file = this.files[0];
      if (file) {
        const reader = new FileReader();
        
        reader.onload = function(e) {
          imagePreviewText.style.display = 'none';
          
          // Check if there's already an image and remove it
          const existingImg = imagePreview.querySelector('img');
          if (existingImg) {
            existingImg.remove();
          }
          
          // Create new image element
          const img = document.createElement('img');
          img.src = e.target.result;
          imagePreview.appendChild(img);
          
          // Display file name
          fileName.textContent = file.name;
        };
        
        reader.readAsDataURL(file);
      }
    });
  </script>
</body>
</html>