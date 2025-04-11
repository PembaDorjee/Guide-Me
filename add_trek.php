<?php
session_start();
require_once '../database.php';

// Check database connection
if ($conn->connect_error) {
    die("Database connection failed: " . $conn->connect_error);
}

// Verify table exists
$table_check = $conn->query("SHOW TABLES LIKE 'Treks'");
if ($table_check->num_rows == 0) {
    // Table doesn't exist, create it
    $create_table_sql = "CREATE TABLE Treks (
        id INT(11) NOT NULL AUTO_INCREMENT PRIMARY KEY,
        trek_name VARCHAR(255) NOT NULL,
        duration INT(11) NOT NULL,
        difficulty VARCHAR(50) NOT NULL,
        max_altitude INT(11),
        region VARCHAR(100) NOT NULL,
        price DECIMAL(10,2) NOT NULL,
        description TEXT NOT NULL,
        highlights TEXT,
        itinerary TEXT,
        included TEXT,
        not_included TEXT,
        best_season VARCHAR(255),
        featured_image VARCHAR(255),
        gallery_images TEXT,
        created_at DATETIME NOT NULL,
        updated_at DATETIME
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4";
    
    if (!$conn->query($create_table_sql)) {
        die("Error creating table: " . $conn->error);
    }
}

// Check if user is logged in and is admin
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit();
}

$success_message = '';
$error_message = '';

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Collect and sanitize form data
    $trek_name = trim($_POST['trek_name']);
    $duration = intval($_POST['duration']);
    $difficulty = trim($_POST['difficulty']);
    $max_altitude = intval($_POST['max_altitude']);
    $region = trim($_POST['region']);
    $price = floatval($_POST['price']);
    $description = trim($_POST['description']);
    $highlights = trim($_POST['highlights']);
    $itinerary = trim($_POST['itinerary']);
    $included = trim($_POST['included']);
    $not_included = trim($_POST['not_included']);
    $best_season = trim($_POST['best_season']);
    
    // Validate required fields
    if (empty($trek_name) || empty($duration) || empty($difficulty) || 
        empty($region) || empty($price) || empty($description)) {
        $error_message = "Please fill in all required fields.";
    } else {
        // Handle image upload
        $featured_image = '';
        if (isset($_FILES['featured_image']) && $_FILES['featured_image']['error'] === 0) {
            $upload_dir = '../uploads/treks/';
            
            // Create directory if it doesn't exist
            if (!is_dir($upload_dir)) {
                mkdir($upload_dir, 0755, true);
            }
            
            $file_name = time() . '_' . basename($_FILES['featured_image']['name']);
            $target_file = $upload_dir . $file_name;
            
            $file_type = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));
            $allowed_types = ['jpg', 'jpeg', 'png', 'webp'];
            
            if (in_array($file_type, $allowed_types)) {
                if (move_uploaded_file($_FILES['featured_image']['tmp_name'], $target_file)) {
                    $featured_image = 'uploads/treks/' . $file_name;
                } else {
                    $error_message = "Failed to upload image.";
                }
            } else {
                $error_message = "Only JPG, JPEG, PNG, and WEBP files are allowed.";
            }
        }
        
        // Process gallery images
        $gallery_images = [];
        
        if (isset($_FILES['gallery_images']) && !empty($_FILES['gallery_images']['name'][0])) {
            $upload_dir = '../uploads/treks/gallery/';
            
            // Create directory if it doesn't exist
            if (!is_dir($upload_dir)) {
                mkdir($upload_dir, 0755, true);
            }
            
            // Loop through each uploaded gallery image
            $file_count = count($_FILES['gallery_images']['name']);
            for ($i = 0; $i < $file_count && $i < 10; $i++) { // Limit to 10 images
                if ($_FILES['gallery_images']['error'][$i] === 0) {
                    $file_name = time() . '_' . $i . '_' . basename($_FILES['gallery_images']['name'][$i]);
                    $target_file = $upload_dir . $file_name;
                    
                    $file_type = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));
                    $allowed_types = ['jpg', 'jpeg', 'png', 'webp'];
                    
                    if (in_array($file_type, $allowed_types)) {
                        if (move_uploaded_file($_FILES['gallery_images']['tmp_name'][$i], $target_file)) {
                            // Fix the path to match the expected format
                            $gallery_images[] = 'uploads/treks/gallery/' . $file_name;
                        }
                    }
                }
            }
        }
        
        // Convert gallery images array to JSON
        $gallery_json = !empty($gallery_images) ? json_encode($gallery_images) : null;
        
        // Modify the error handling to show more detailed database errors
        if (empty($error_message)) {
            // Insert trek into database
            $stmt = $conn->prepare("INSERT INTO Treks (
                trek_name, duration, difficulty, max_altitude, region, price, 
                description, highlights, itinerary, included, not_included, 
                best_season, featured_image, gallery_images, created_at
            ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, NOW())");
            
            if (!$stmt) {
                $error_message = "Prepare failed: (" . $conn->errno . ") " . $conn->error;
            } else {
                // Fix the bind_param string to match the number of parameters
                $stmt->bind_param(
                    "sisisissssssss", // Changed from "sisisssssssss" to include the correct number of parameters
                    $trek_name, $duration, $difficulty, $max_altitude, $region, $price,
                    $description, $highlights, $itinerary, $included, $not_included,
                    $best_season, $featured_image, $gallery_json
                );
                
                if ($stmt->execute()) {
                    $success_message = "Trek added successfully!";
                    // Clear form data after successful submission
                    $_POST = array();
                } else {
                    $error_message = "Error: (" . $stmt->errno . ") " . $stmt->error;
                }
                
                $stmt->close();
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Add New Trek - Guide Me Admin</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
  <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap">
  <link rel="stylesheet" href="admin.css">
  <style>
    :root {
      --primary: #15d455;
      --primary-dark: #0fb846;
      --secondary: #f3a42e;
      --secondary-dark: #e08e1a;
      --dark: #1E2A38;
      --light: #f5f6f8;
      --text: #333;
      --text-light: #777;
      --border: #e0e0e0;
      --success: #28a745;
      --danger: #dc3545;
    }
    
    body {
      font-family: 'Poppins', sans-serif;
      background-color: #f0f2f5;
      color: var(--text);
      line-height: 1.6;
    }
    
    .admin-header {
      background: linear-gradient(135deg, var(--dark) 0%, #2c3e50 100%);
      color: white;
      padding: 1rem 2rem;
      display: flex;
      justify-content: space-between;
      align-items: center;
      box-shadow: 0 4px 12px rgba(0,0,0,0.1);
    }
    
    .logo {
      display: flex;
      align-items: center;
      font-size: 1.5rem;
      font-weight: 700;
    }
    
    .logo i {
      color: var(--primary);
      margin-right: 10px;
      font-size: 1.8rem;
      animation: pulse 2s infinite;
    }
    
    @keyframes pulse {
      0% { transform: scale(1); }
      50% { transform: scale(1.1); }
      100% { transform: scale(1); }
    }
    
    .logo span {
      color: var(--secondary);
    }
    
    .admin-nav {
      display: flex;
      gap: 20px;
    }
    
    .admin-nav a {
      color: white;
      text-decoration: none;
      display: flex;
      align-items: center;
      padding: 0.5rem 1rem;
      border-radius: 8px;
      transition: all 0.3s ease;
    }
    
    .admin-nav a:hover {
      background: rgba(255, 255, 255, 0.1);
      transform: translateY(-2px);
    }
    
    .admin-nav a i {
      margin-right: 8px;
    }
    
    .container {
      max-width: 1000px;
      margin: 2rem auto;
      background: #fff;
      border-radius: 12px;
      box-shadow: 0 8px 30px rgba(0, 0, 0, 0.12);
      overflow: hidden;
      transition: all 0.3s ease;
    }
    
    .container:hover {
      box-shadow: 0 12px 40px rgba(0, 0, 0, 0.15);
      transform: translateY(-5px);
    }

    .form-header {
      background: linear-gradient(135deg, var(--dark) 0%, #2c3e50 100%);
      color: white;
      padding: 1.8rem 2rem;
      position: relative;
      overflow: hidden;
    }
    
    .form-header::before {
      content: '';
      position: absolute;
      top: 0;
      left: 0;
      width: 100%;
      height: 100%;
      background: url('https://images.unsplash.com/photo-1464822759023-fed622ff2c3b?ixlib=rb-4.0.3&auto=format&fit=crop&w=1200&q=80') no-repeat center center;
      background-size: cover;
      opacity: 0.2;
      z-index: 0;
    }

    .form-header h1 {
      margin: 0;
      font-size: 2rem;
      display: flex;
      align-items: center;
      position: relative;
      z-index: 1;
    }
    
    .form-header h1 i {
      margin-right: 15px;
      color: var(--secondary);
      font-size: 2.2rem;
    }

    .form-body {
      padding: 2.5rem;
    }

    .form-group {
      margin-bottom: 1.8rem;
      position: relative;
    }

    label {
      display: block;
      margin-bottom: 0.6rem;
      font-weight: 500;
      color: var(--dark);
      transition: all 0.3s ease;
    }
    
    .form-group:focus-within label {
      color: var(--primary);
    }

    input, select, textarea {
      width: 100%;
      padding: 0.9rem 1rem;
      border: 1px solid var(--border);
      border-radius: 8px;
      font-size: 1rem;
      font-family: inherit;
      transition: all 0.3s ease;
      box-shadow: 0 2px 5px rgba(0,0,0,0.05);
    }
    
    input:focus, select:focus, textarea:focus {
      border-color: var(--primary);
      box-shadow: 0 0 0 3px rgba(21, 212, 85, 0.2);
      outline: none;
    }

    textarea {
      min-height: 140px;
      resize: vertical;
    }

    .form-row {
      display: flex;
      gap: 1.5rem;
      margin-bottom: 1.8rem;
    }

    .form-col {
      flex: 1;
    }

    .section-title {
      font-size: 1.5rem;
      color: var(--dark);
      margin: 2.5rem 0 1.5rem;
      position: relative;
      padding-bottom: 0.8rem;
      border-bottom: 2px solid #f0f0f0;
      display: flex;
      align-items: center;
    }
    
    .section-title i {
      margin-right: 10px;
      color: var(--secondary);
    }

    .btn {
      padding: 0.9rem 1.8rem;
      border-radius: 8px;
      font-weight: 600;
      text-decoration: none;
      display: inline-flex;
      align-items: center;
      justify-content: center;
      transition: all 0.3s ease;
      cursor: pointer;
      border: none;
      font-size: 1rem;
      box-shadow: 0 4px 6px rgba(0,0,0,0.1);
    }

    .btn-primary {
      background: var(--primary);
      color: white;
    }

    .btn-primary:hover {
      background: var(--primary-dark);
      transform: translateY(-2px);
      box-shadow: 0 6px 12px rgba(21, 212, 85, 0.2);
    }
    
    .btn-primary:active {
      transform: translateY(0);
    }

    .btn-secondary {
      background: var(--light);
      color: var(--dark);
    }

    .btn-secondary:hover {
      background: #e5e5e5;
      transform: translateY(-2px);
      box-shadow: 0 6px 12px rgba(0,0,0,0.05);
    }
    
    .btn-secondary:active {
      transform: translateY(0);
    }

    .btn i {
      margin-right: 10px;
      font-size: 1.1rem;
    }

    .form-actions {
      display: flex;
      justify-content: space-between;
      margin-top: 2.5rem;
      padding-top: 1.5rem;
      border-top: 1px solid var(--border);
    }

    .required::after {
      content: '*';
      color: var(--danger);
      margin-left: 4px;
    }
    
    .alert {
      padding: 1.2rem;
      border-radius: 8px;
      margin-bottom: 2rem;
      display: flex;
      align-items: center;
      animation: fadeIn 0.5s ease;
    }
    
    @keyframes fadeIn {
      from { opacity: 0; transform: translateY(-10px); }
      to { opacity: 1; transform: translateY(0); }
    }
    
    .alert i {
      margin-right: 12px;
      font-size: 1.5rem;
    }
    
    .alert-success {
      background-color: rgba(40, 167, 69, 0.1);
      color: var(--success);
      border: 1px solid rgba(40, 167, 69, 0.2);
    }
    
    .alert-danger {
      background-color: rgba(220, 53, 69, 0.1);
      color: var(--danger);
      border: 1px solid rgba(220, 53, 69, 0.2);
    }
    
    /* Custom file input */
    .file-input-container {
      position: relative;
      overflow: hidden;
      display: inline-block;
      cursor: pointer;
    }
    
    .file-input-container input[type="file"] {
      position: absolute;
      left: 0;
      top: 0;
      opacity: 0;
      cursor: pointer;
      width: 100%;
      height: 100%;
    }
    
    .file-input-button {
      display: inline-flex;
      align-items: center;
      padding: 0.8rem 1.5rem;
      background: var(--light);
      border: 1px solid var(--border);
      border-radius: 8px;
      font-weight: 500;
      color: var(--dark);
      transition: all 0.3s ease;
    }
    
    .file-input-container:hover .file-input-button {
      background: #e5e5e5;
    }
    
    .file-input-button i {
      margin-right: 8px;
      color: var(--secondary);
    }
    
    .file-name {
      margin-left: 10px;
      font-size: 0.9rem;
      color: var(--text-light);
    }
    
    .image-preview {
      max-width: 300px;
      margin-top: 15px;
      border-radius: 8px;
      overflow: hidden;
      display: none;
      box-shadow: 0 4px 15px rgba(0,0,0,0.1);
      transition: all 0.3s ease;
    }
    
    .image-preview:hover {
      transform: scale(1.02);
    }
    
    .image-preview img {
      width: 100%;
      height: auto;
      display: block;
    }
    
    /* Gallery Preview Styles */
    .gallery-preview {
      display: flex;
      flex-wrap: wrap;
      gap: 12px;
      margin-top: 15px;
    }
    
    .gallery-preview .gallery-item {
      width: 120px;
      height: 120px;
      border-radius: 8px;
      overflow: hidden;
      position: relative;
      box-shadow: 0 4px 10px rgba(0,0,0,0.1);
      transition: all 0.3s ease;
    }
    
    .gallery-preview .gallery-item:hover {
      transform: translateY(-5px);
      box-shadow: 0 8px 15px rgba(0,0,0,0.15);
    }
    
    .gallery-preview .gallery-item img {
      width: 100%;
      height: 100%;
      object-fit: cover;
    }
    
    .gallery-preview .gallery-count {
      margin-top: 12px;
      font-size: 0.9rem;
      color: var(--text-light);
      display: flex;
      align-items: center;
    }
    
    .gallery-preview .gallery-count i {
      margin-right: 5px;
      color: var(--secondary);
    }
    
    /* Input icons */
    .input-icon-container {
      position: relative;
    }
    
    .input-icon-container input,
    .input-icon-container select,
    .input-icon-container textarea {
      padding-left: 40px;
    }
    
    .input-icon {
      position: absolute;
      left: 12px;
      top: 50%;
      transform: translateY(-50%);
      color: var(--text-light);
      transition: all 0.3s ease;
    }
    
    .input-icon-container:focus-within .input-icon {
      color: var(--primary);
    }
    
    /* Responsive adjustments */
    @media (max-width: 768px) {
      .form-row {
        flex-direction: column;
        gap: 0;
      }
      
      .container {
        margin: 1rem;
        border-radius: 8px;
      }
      
      .form-body {
        padding: 1.5rem;
      }
    }
  </style>
</head>
<body>

<header class="admin-header">
  <div class="logo">
    <i class="fas fa-mountain"></i>
    <div>Guide <span>Me</span></div>
  </div>
  <div class="admin-nav">
    <a href="admin.php"><i class="fas fa-tachometer-alt"></i> Dashboard</a>
    <a href="logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a>
  </div>
</header>

<div class="container">
  <div class="form-header">
    <h1><i class="fas fa-hiking"></i> Add New Trek</h1>
  </div>
  
  <div class="form-body">
    <?php if (!empty($success_message)): ?>
      <div class="alert alert-success">
        <i class="fas fa-check-circle"></i>
        <?php echo $success_message; ?>
      </div>
    <?php endif; ?>
    
    <?php if (!empty($error_message)): ?>
      <div class="alert alert-danger">
        <i class="fas fa-exclamation-circle"></i>
        <?php echo $error_message; ?>
      </div>
    <?php endif; ?>
    
    <form id="addTrekForm" action="add_trek.php" method="POST" enctype="multipart/form-data">
      <h2 class="section-title"><i class="fas fa-info-circle"></i> Basic Information</h2>
      
      <div class="form-group">
        <label for="trek_name" class="required">Trek Name</label>
        <div class="input-icon-container">
          <i class="fas fa-route input-icon"></i>
          <input type="text" id="trek_name" name="trek_name" value="<?php echo isset($_POST['trek_name']) ? htmlspecialchars($_POST['trek_name']) : ''; ?>" required>
        </div>
      </div>
      
      <div class="form-row">
        <div class="form-col">
          <div class="form-group">
            <label for="duration" class="required">Duration (days)</label>
            <div class="input-icon-container">
              <i class="fas fa-calendar-day input-icon"></i>
              <input type="number" id="duration" name="duration" min="1" max="100" value="<?php echo isset($_POST['duration']) ? htmlspecialchars($_POST['duration']) : ''; ?>" required>
            </div>
          </div>
        </div>
        <div class="form-col">
          <div class="form-group">
            <label for="difficulty" class="required">Difficulty Level</label>
            <div class="input-icon-container">
              <i class="fas fa-tachometer-alt input-icon"></i>
              <select id="difficulty" name="difficulty" required>
                <option value="">Select Difficulty</option>
                <option value="Easy" <?php echo (isset($_POST['difficulty']) && $_POST['difficulty'] === 'Easy') ? 'selected' : ''; ?>>Easy</option>
                <option value="Moderate" <?php echo (isset($_POST['difficulty']) && $_POST['difficulty'] === 'Moderate') ? 'selected' : ''; ?>>Moderate</option>
                <option value="Challenging" <?php echo (isset($_POST['difficulty']) && $_POST['difficulty'] === 'Challenging') ? 'selected' : ''; ?>>Challenging</option>
                <option value="Difficult" <?php echo (isset($_POST['difficulty']) && $_POST['difficulty'] === 'Difficult') ? 'selected' : ''; ?>>Difficult</option>
                <option value="Very Difficult" <?php echo (isset($_POST['difficulty']) && $_POST['difficulty'] === 'Very Difficult') ? 'selected' : ''; ?>>Very Difficult</option>
              </select>
            </div>
          </div>
        </div>
      </div>
      
      <div class="form-row">
        <div class="form-col">
          <div class="form-group">
            <label for="max_altitude">Maximum Altitude (meters)</label>
            <div class="input-icon-container">
              <i class="fas fa-mountain input-icon"></i>
              <input type="number" id="max_altitude" name="max_altitude" min="0" max="9000" value="<?php echo isset($_POST['max_altitude']) ? htmlspecialchars($_POST['max_altitude']) : ''; ?>">
            </div>
          </div>
        </div>
        <div class="form-col">
          <div class="form-group">
            <label for="region" class="required">Region</label>
            <div class="input-icon-container">
              <i class="fas fa-map-marker-alt input-icon"></i>
              <select id="region" name="region" required>
                <option value="">Select Region</option>
                <option value="Everest" <?php echo (isset($_POST['region']) && $_POST['region'] === 'Everest') ? 'selected' : ''; ?>>Everest Region</option>
                <option value="Annapurna" <?php echo (isset($_POST['region']) && $_POST['region'] === 'Annapurna') ? 'selected' : ''; ?>>Annapurna Region</option>
                <option value="Langtang" <?php echo (isset($_POST['region']) && $_POST['region'] === 'Langtang') ? 'selected' : ''; ?>>Langtang Region</option>
                <option value="Manaslu" <?php echo (isset($_POST['region']) && $_POST['region'] === 'Manaslu') ? 'selected' : ''; ?>>Manaslu Region</option>
                <option value="Mustang" <?php echo (isset($_POST['region']) && $_POST['region'] === 'Mustang') ? 'selected' : ''; ?>>Mustang Region</option>
                <option value="Kanchenjunga" <?php echo (isset($_POST['region']) && $_POST['region'] === 'Kanchenjunga') ? 'selected' : ''; ?>>Kanchenjunga Region</option>
                <option value="Dolpo" <?php echo (isset($_POST['region']) && $_POST['region'] === 'Dolpo') ? 'selected' : ''; ?>>Dolpo Region</option>
                <option value="Other" <?php echo (isset($_POST['region']) && $_POST['region'] === 'Other') ? 'selected' : ''; ?>>Other</option>
              </select>
            </div>
          </div>
        </div>
      </div>
      
      <div class="form-group">
        <label for="price" class="required">Price (USD)</label>
        <div class="input-icon-container">
          <i class="fas fa-dollar-sign input-icon"></i>
          <input type="number" id="price" name="price" min="0" step="0.01" value="<?php echo isset($_POST['price']) ? htmlspecialchars($_POST['price']) : ''; ?>" required>
        </div>
      </div>
      
      <div class="form-group">
        <label for="best_season">Best Season</label>
        <div class="input-icon-container">
          <i class="fas fa-cloud-sun input-icon"></i>
          <input type="text" id="best_season" name="best_season" value="<?php echo isset($_POST['best_season']) ? htmlspecialchars($_POST['best_season']) : ''; ?>" placeholder="e.g., Spring (March-May), Autumn (September-November)">
        </div>
      </div>
      
      <div class="form-group">
        <label for="featured_image">Featured Image</label>
        <div class="file-input-container">
          <div class="file-input-button">
            <i class="fas fa-image"></i> Choose Featured Image
          </div>
          <input type="file" id="featured_image" name="featured_image" accept="image/jpeg, image/png, image/webp">
          <span class="file-name" id="featured-file-name">No file chosen</span>
        </div>
        <div id="imagePreview" class="image-preview"></div>
      </div>
      
      <!-- Gallery Section -->
      <div class="form-group">
        <label for="gallery_images">Gallery Images</label>
        <div class="file-input-container">
          <div class="file-input-button">
            <i class="fas fa-images"></i> Choose Gallery Images
          </div>
          <input type="file" id="gallery_images" name="gallery_images[]" accept="image/jpeg, image/png, image/webp" multiple>
          <span class="file-name" id="gallery-file-name">No files chosen</span>
        </div>
        <div id="galleryPreview" class="gallery-preview"></div>
        <small class="form-text text-muted">You can select up to 10 images for the trek gallery.</small>
      </div>
      
      <h2 class="section-title"><i class="fas fa-clipboard-list"></i> Trek Details</h2>
      
      <div class="form-group">
        <label for="description" class="required">Description</label>
        <textarea id="description" name="description" required placeholder="Provide a detailed description of the trek..."><?php echo isset($_POST['description']) ? htmlspecialchars($_POST['description']) : ''; ?></textarea>
      </div>
      
      <div class="form-group">
        <label for="highlights">Highlights</label>
        <textarea id="highlights" name="highlights" placeholder="Enter trek highlights, one per line"><?php echo isset($_POST['highlights']) ? htmlspecialchars($_POST['highlights']) : ''; ?></textarea>
      </div>
      
      <div class="form-group">
        <label for="itinerary">Itinerary</label>
        <textarea id="itinerary" name="itinerary" placeholder="Enter detailed day-by-day itinerary"><?php echo isset($_POST['itinerary']) ? htmlspecialchars($_POST['itinerary']) : ''; ?></textarea>
      </div>
      
      <div class="form-group">
        <label for="included">What's Included</label>
        <textarea id="included" name="included" placeholder="Enter included items, one per line"><?php echo isset($_POST['included']) ? htmlspecialchars($_POST['included']) : ''; ?></textarea>
      </div>
      
      <div class="form-group">
        <label for="not_included">What's Not Included</label>
        <textarea id="not_included" name="not_included" placeholder="Enter not included items, one per line"><?php echo isset($_POST['not_included']) ? htmlspecialchars($_POST['not_included']) : ''; ?></textarea>
      </div>
      
      <div class="form-actions">
        <a href="admin.php" class="btn btn-secondary">
          <i class="fas fa-arrow-left"></i> Cancel
        </a>
        <button type="submit" class="btn btn-primary">
          <i class="fas fa-plus-circle"></i> Add Trek
        </button>
      </div>
    </form>
  </div>
</div>

<script>
  // Image preview functionality
  document.getElementById('featured_image').addEventListener('change', function(e) {
    const file = e.target.files[0];
    const preview = document.getElementById('imagePreview');
    const fileName = document.getElementById('featured-file-name');
    
    if (file) {
      fileName.textContent = file.name;
      const reader = new FileReader();
      
      reader.onload = function(e) {
        preview.innerHTML = `<img src="${e.target.result}" alt="Preview">`;
        preview.style.display = 'block';
      }
      
      reader.readAsDataURL(file);
    } else {
      fileName.textContent = 'No file chosen';
      preview.innerHTML = '';
      preview.style.display = 'none';
    }
  });
  
  // Add gallery preview functionality
  document.getElementById('gallery_images').addEventListener('change', function(e) {
    const files = e.target.files;
    const preview = document.getElementById('galleryPreview');
    const fileName = document.getElementById('gallery-file-name');
    preview.innerHTML = '';
    
    if (files.length > 0) {
      fileName.textContent = files.length > 1 ? `${files.length} files selected` : files[0].name;
      
      // Limit to 10 images
      const maxFiles = Math.min(files.length, 10);
      
      for (let i = 0; i < maxFiles; i++) {
        const file = files[i];
        const reader = new FileReader();
        
        reader.onload = function(e) {
          const galleryItem = document.createElement('div');
          galleryItem.className = 'gallery-item';
          galleryItem.innerHTML = `
            <img src="${e.target.result}" alt="Gallery Preview">
          `;
          preview.appendChild(galleryItem);
        }
        
        reader.readAsDataURL(file);
      }
      
      if (files.length > 10) {
        const message = document.createElement('div');
        message.className = 'gallery-count';
        message.innerHTML = `<i class="fas fa-info-circle"></i> Note: Only the first 10 images will be uploaded.`;
        preview.appendChild(message);
      }
    } else {
      fileName.textContent = 'No files chosen';
    }
  });
  
  // Form validation with improved UX
  document.getElementById('addTrekForm').addEventListener('submit', function(e) {
    const trekName = document.getElementById('trek_name').value.trim();
    const duration = document.getElementById('duration').value.trim();
    const difficulty = document.getElementById('difficulty').value;
    const region = document.getElementById('region').value;
    const price = document.getElementById('price').value.trim();
    const description = document.getElementById('description').value.trim();
    
    const requiredFields = [
      { id: 'trek_name', name: 'Trek Name' },
      { id: 'duration', name: 'Duration' },
      { id: 'difficulty', name: 'Difficulty Level' },
      { id: 'region', name: 'Region' },
      { id: 'price', name: 'Price' },
      { id: 'description', name: 'Description' }
    ];
    
    const emptyFields = requiredFields.filter(field => {
      const element = document.getElementById(field.id);
      return !element.value.trim();
    });
    
    if (emptyFields.length > 0) {
      e.preventDefault();
      const fieldNames = emptyFields.map(field => field.name).join(', ');
      alert(`Please fill in the following required fields: ${fieldNames}`);
      
      // Focus the first empty field
      document.getElementById(emptyFields[0].id).focus();
      return false;
    }
    
    // Add a loading state to the submit button
    const submitBtn = this.querySelector('button[type="submit"]');
    submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Processing...';
    submitBtn.disabled = true;
    
    // Add form submission confirmation
    const formData = new FormData(this);
    
    // Show a success message after form submission
    setTimeout(() => {
      // This timeout is just for visual feedback - the actual submission happens via the form's action
      console.log('Form submitted successfully');
    }, 500);
  });
  
  // Add smooth scrolling to section titles
  document.querySelectorAll('.section-title').forEach(title => {
    title.addEventListener('click', function() {
      const nextSection = this.nextElementSibling;
      if (nextSection) {
        nextSection.scrollIntoView({ behavior: 'smooth' });
      }
    });
  });
  
  // Add success notification handling
  document.addEventListener('DOMContentLoaded', function() {
    const successAlert = document.querySelector('.alert-success');
    
    if (successAlert) {
      // Scroll to top to show the success message
      window.scrollTo({ top: 0, behavior: 'smooth' });
      
      // Highlight the success message with a subtle animation
      successAlert.style.animation = 'none';
      setTimeout(() => {
        successAlert.style.animation = 'fadeIn 0.5s ease, pulse 2s infinite';
      }, 10);
      
      // Add pulse animation
      const style = document.createElement('style');
      style.textContent = `
        @keyframes pulse {
          0% { box-shadow: 0 0 0 0 rgba(40, 167, 69, 0.4); }
          70% { box-shadow: 0 0 0 10px rgba(40, 167, 69, 0); }
          100% { box-shadow: 0 0 0 0 rgba(40, 167, 69, 0); }
        }
      `;
      document.head.appendChild(style);
      
      // Automatically redirect to admin page after successful submission
      if (successAlert.textContent.includes('Trek added successfully')) {
        setTimeout(() => {
          window.location.href = 'admin.php?success=trek_added';
        }, 3000);
      }
    }
  });
</script>

</body>
</html>