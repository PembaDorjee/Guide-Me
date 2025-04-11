<?php
// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Start session at the VERY TOP of the file
session_start();

// Check if user is logged in
if (!isset($_SESSION['client_id'])) {
    header("Location: ../login.php");
    exit();
}

// Include database connection
require_once '../database.php';

// Initialize user array with session data
$user = [
    'full_name' => $_SESSION['full_name'] ?? 'User Not Found',
    'email' => $_SESSION['email'] ?? 'N/A',
    'phone_number' => $_SESSION['phone_number'] ?? 'N/A',
    'avatar_path' => $_SESSION['avatar_path'] ?? ''
];

// Try to fetch fresh user data from database (optional)
try {
    $client_id = $_SESSION['client_id'];
    
    if (isset($conn)) {
        // Using mysqli
        $stmt = $conn->prepare("SELECT full_name, email, phone as phone_number, avatar_path FROM clients WHERE client_id = ?");
        $stmt->bind_param("i", $client_id);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result && $result->num_rows > 0) {
            $db_user = $result->fetch_assoc();
            // Update user array with fresh data
            $user = array_merge($user, $db_user);
            // Update session with fresh data
            $_SESSION['full_name'] = $db_user['full_name'];
            $_SESSION['email'] = $db_user['email'];
            $_SESSION['phone_number'] = $db_user['phone_number'];
            if(isset($db_user['avatar_path'])) {
                $_SESSION['avatar_path'] = $db_user['avatar_path'];
            }
        }
        $stmt->close();
    }
} catch (Exception $e) {
    // If database fetch fails, we'll use the session data we already have
    error_log("Database error: " . $e->getMessage());
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>User Profile | Guide Me</title>
  <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;600;700&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
  <style>
    * {
      box-sizing: border-box;
      margin: 0;
      padding: 0;
      font-family: 'Montserrat', sans-serif;
    }
    
    body {
      background-color: #f5f7fa;
      color: #333;
    }
    
    .profile-container {
      max-width: 1200px;
      margin: 80px auto 40px;
      padding: 0 20px;
    }
    
    .profile-header {
      text-align: center;
      margin-bottom: 40px;
    }
    
    .profile-header h1 {
      font-size: 2.5rem;
      color: #1E2A38;
      margin-bottom: 10px;
    }
    
    .profile-header p {
      color: #666;
      font-size: 1.1rem;
    }
    
    .profile-card {
      background: white;
      border-radius: 16px;
      box-shadow: 0 8px 30px rgba(0, 0, 0, 0.08);
      overflow: hidden;
      display: flex;
      flex-direction: column;
      max-width: 800px;
      margin: 0 auto;
    }
    
    .profile-banner {
      height: 150px;
      background: linear-gradient(135deg, #15d455, #f3a42e);
    }
    
    .profile-content {
      padding: 30px;
      position: relative;
    }
    
    .profile-avatar {
      width: 150px;
      height: 150px;
      border-radius: 50%;
      border: 5px solid white;
      object-fit: cover;
      position: absolute;
      top: -75px;
      left: 50%;
      transform: translateX(-50%);
      box-shadow: 0 4px 20px rgba(0, 0, 0, 0.15);
    }
    
    .profile-info {
      margin-top: 90px;
      text-align: center;
    }
    
    .profile-info h2 {
      font-size: 1.8rem;
      color: #1E2A38;
      margin-bottom: 5px;
    }
    
    .profile-info p {
      color: #666;
      margin-bottom: 20px;
    }
    
    .profile-details {
      display: grid;
      grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
      gap: 20px;
      margin-top: 30px;
    }
    
    .detail-card {
      background: #f9f9f9;
      border-radius: 10px;
      padding: 20px;
      transition: all 0.3s ease;
    }
    
    .detail-card:hover {
      transform: translateY(-5px);
      box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
    }
    
    .detail-card h3 {
      font-size: 1rem;
      color: #888;
      margin-bottom: 10px;
      text-transform: uppercase;
      letter-spacing: 1px;
    }
    
    .detail-card p {
      font-size: 1.2rem;
      color: #333;
      font-weight: 600;
    }
    
    .profile-actions {
      display: flex;
      justify-content: center;
      gap: 15px;
      margin-top: 30px;
    }
    
    .btn {
      padding: 12px 25px;
      border-radius: 8px;
      font-weight: 600;
      cursor: pointer;
      transition: all 0.3s ease;
      text-decoration: none;
      display: inline-flex;
      align-items: center;
      gap: 8px;
    }
    
    .btn-primary {
      background: #f3a42e;
      color: white;
      border: none;
    }
    
    .btn-primary:hover {
      background: #d48826;
      transform: translateY(-2px);
      box-shadow: 0 4px 12px rgba(243, 164, 46, 0.3);
    }
    
    .btn-outline {
      background: transparent;
      color: #333;
      border: 1px solid #ddd;
    }
    
    .btn-outline:hover {
      background: #f5f5f5;
      transform: translateY(-2px);
    }
    
    /* Navigation bar */
    .navbar {
      background-color: #1E2A38;
      padding: 1rem 2rem;
      display: flex;
      justify-content: space-between;
      align-items: center;
      position: fixed;
      top: 0;
      left: 0;
      width: 100%;
      z-index: 1000;
      box-shadow: 0 2px 10px rgba(0,0,0,0.1);
    }
    
    .navbar-logo {
      font-size: 1.5rem;
      font-weight: 700;
      color: white;
      text-decoration: none;
    }
    
    .navbar-logo span {
      color: #f3a42e;
    }
    
    .navbar-links {
      display: flex;
      gap: 1.5rem;
    }
    
    .navbar-links a {
      color: white;
      text-decoration: none;
      transition: all 0.3s ease;
    }
    
    .navbar-links a:hover {
      color: #f3a42e;
    }
    
    @media (max-width: 768px) {
      .profile-header h1 {
        font-size: 2rem;
      }
      
      .profile-avatar {
        width: 120px;
        height: 120px;
        top: -60px;
      }
      
      .profile-info {
        margin-top: 70px;
      }
      
      .profile-content {
        padding: 20px;
      }
      
      .navbar {
        padding: 0.8rem 1rem;
      }
      
      .navbar-links {
        gap: 1rem;
      }
    }
  </style>
</head>
<body>
  <!-- Simple navigation bar -->
  <div class="navbar">
    <a href="index3.php" class="navbar-logo">Guide <span>Me</span></a>
    <div class="navbar-links">
      <a href="index3.php#home">Home</a>
      <a href="index3.php#services">Services</a>
      <a href="index3.php#guides">Guides</a>
      <a href="index3.php#contact">Contact</a>
    </div>
  </div>

  <div class="profile-container">
    <div class="profile-header">
      <h1>Your Profile</h1>
      <p>Manage your account information and settings</p>
    </div>
    
    <div class="profile-card">
      <div class="profile-banner"></div>
      
      <div class="profile-content">
        <img src="<?php echo isset($user['avatar_path']) && !empty($user['avatar_path']) ? '../' . htmlspecialchars($user['avatar_path']) : 'https://placehold.co/150x150'; ?>" 
             alt="Profile Picture" class="profile-avatar" id="profileAvatar">
        
        <div class="profile-info">
          <h2><?php echo htmlspecialchars($user['full_name']); ?></h2>
          <p>Member since <?php echo date('F Y', strtotime($_SESSION['registration_date'] ?? 'now')); ?></p>
          
          <div class="profile-details">
            <div class="detail-card">
              <h3>Email Address</h3>
              <p><?php echo htmlspecialchars($user['email']); ?></p>
            </div>
            
            <div class="detail-card">
              <h3>Phone Number</h3>
              <p><?php 
                 echo !empty($user['phone_number']) ? htmlspecialchars($user['phone_number']) : 'Not provided'; 
                 // Add debugging comment
                 echo "<!-- Debug: phone_number value: " . htmlspecialchars($user['phone_number'] ?? 'null') . " -->";
                 ?>
              </p>
            </div>
          </div>
          
          <div class="profile-actions">
            <a href="#" class="btn btn-primary" id="editProfileBtn">
              <i class="fas fa-user-edit"></i> Edit Profile
            </a>
            <a href="index3.php" class="btn btn-outline">
              <i class="fas fa-arrow-left"></i> Back to Home
            </a>
          </div>
        </div>
      </div>
    </div>
  </div>

  <!-- Edit Profile Modal (hidden by default) -->
  <div id="editProfileModal" style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.5); z-index: 2000; justify-content: center; align-items: center;">
    <div style="background: white; padding: 30px; border-radius: 10px; width: 90%; max-width: 500px; position: relative;">
      <button id="closeModal" style="position: absolute; top: 15px; right: 15px; background: none; border: none; font-size: 1.5rem; cursor: pointer;">×</button>
      <h3 style="margin-bottom: 20px; color: #1E2A38;">Edit Profile</h3>
      
      <form id="updateProfileForm">
        <div style="margin-bottom: 15px;">
          <label style="display: block; margin-bottom: 5px; color: #666;">Full Name</label>
          <input type="text" name="full_name" id="editName" value="<?php echo htmlspecialchars($user['full_name']); ?>" 
                 style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 5px;">
        </div>
        
        <div style="margin-bottom: 15px;">
          <label style="display: block; margin-bottom: 5px; color: #666;">Email</label>
          <input type="email" name="email" id="editEmail" value="<?php echo htmlspecialchars($user['email']); ?>" 
                 style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 5px;">
        </div>
        
        <div style="margin-bottom: 15px;">
          <label style="display: block; margin-bottom: 5px; color: #666;">Phone Number</label>
          <input type="tel" name="phone_number" id="editPhone" value="<?php echo htmlspecialchars($user['phone_number'] ?? ''); ?>" 
                 style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 5px;">
        </div>
        
        <button type="submit" style="background: #f3a42e; color: white; border: none; padding: 12px 20px; border-radius: 5px; cursor: pointer; font-weight: 600;">
          Save Changes
        </button>
      </form>
    </div>
  </div>

  <script>
    document.addEventListener('DOMContentLoaded', function() {
      // Edit profile modal functionality
      const editProfileBtn = document.getElementById('editProfileBtn');
      const editProfileModal = document.getElementById('editProfileModal');
      const closeModal = document.getElementById('closeModal');
      
      if (editProfileBtn) {
        editProfileBtn.addEventListener('click', function(e) {
          e.preventDefault();
          editProfileModal.style.display = 'flex';
        });
      }
      
      if (closeModal) {
        closeModal.addEventListener('click', function() {
          editProfileModal.style.display = 'none';
        });
      }
      
      // Close modal when clicking outside
      window.addEventListener('click', function(e) {
        if (e.target === editProfileModal) {
          editProfileModal.style.display = 'none';
        }
      });
      
      // Form submission
      const updateProfileForm = document.getElementById('updateProfileForm');
      if (updateProfileForm) {
        updateProfileForm.addEventListener('submit', function(e) {
          e.preventDefault();
          
          const formData = new FormData(this);
          
          // Rename phone_number to phone to match database column
          const phoneNumber = formData.get('phone_number');
          formData.delete('phone_number');
          formData.append('phone', phoneNumber);
          
          fetch('../frontendc/update_profile.php', {
            method: 'POST',
            body: formData
          })
          .then(response => response.json())
          .then(data => {
            if (data.success) {
              // Update the phone number in the UI immediately
              document.querySelector('.detail-card:nth-child(2) p').textContent = phoneNumber || 'Not provided';
              alert('Profile updated successfully!');
              // Force a hard reload to ensure all data is refreshed
              window.location.href = window.location.href + '?t=' + new Date().getTime();
            } else {
              alert('Error: ' + data.message);
            }
          })
          .catch(error => {
            console.error('Error:', error);
            alert('An error occurred while updating the profile.');
          });
        });
      }
    });
  </script>
</body>
</html>