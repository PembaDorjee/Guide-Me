<?php
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

// Try to fetch fresh user data from database
try {
    $client_id = $_SESSION['client_id'];
    
    if (isset($conn)) {
        // Using mysqli
        $stmt = $conn->prepare("SELECT full_name, email, phone as phone_number, avatar_path FROM Client WHERE client_id = ?");
        $stmt->bind_param("i", $client_id);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result && $result->num_rows > 0) {
            $db_user = $result->fetch_assoc();
            // Update user array with fresh data
            $user = array_merge($user, $db_user);
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
  <title>Account Settings | Guide Me</title>
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
    
    .settings-container {
      max-width: 1200px;
      margin: 80px auto 40px;
      padding: 0 20px;
    }
    
    .settings-header {
      text-align: center;
      margin-bottom: 40px;
    }
    
    .settings-header h1 {
      font-size: 2.5rem;
      color: #1E2A38;
      margin-bottom: 10px;
    }
    
    .settings-header p {
      color: #666;
      font-size: 1.1rem;
    }
    
    .settings-card {
      background: white;
      border-radius: 16px;
      box-shadow: 0 8px 30px rgba(0, 0, 0, 0.08);
      overflow: hidden;
      max-width: 800px;
      margin: 0 auto;
    }
    
    .settings-tabs {
      display: flex;
      border-bottom: 1px solid #eee;
    }
    
    .settings-tab {
      padding: 15px 20px;
      cursor: pointer;
      font-weight: 600;
      color: #666;
      transition: all 0.3s ease;
      border-bottom: 3px solid transparent;
    }
    
    .settings-tab.active {
      color: #f3a42e;
      border-bottom-color: #f3a42e;
    }
    
    .settings-tab:hover {
      background: #f9f9f9;
    }
    
    .settings-content {
      padding: 30px;
    }
    
    .settings-section {
      display: none;
    }
    
    .settings-section.active {
      display: block;
      animation: fadeIn 0.3s ease;
    }
    
    @keyframes fadeIn {
      from { opacity: 0; transform: translateY(10px); }
      to { opacity: 1; transform: translateY(0); }
    }
    
    .settings-section h2 {
      font-size: 1.5rem;
      color: #1E2A38;
      margin-bottom: 20px;
      padding-bottom: 10px;
      border-bottom: 1px solid #eee;
    }
    
    .form-group {
      margin-bottom: 20px;
    }
    
    .form-group label {
      display: block;
      margin-bottom: 8px;
      font-weight: 600;
      color: #555;
    }
    
    .form-group input {
      width: 100%;
      padding: 12px 15px;
      border: 1px solid #ddd;
      border-radius: 8px;
      font-size: 1rem;
      transition: all 0.3s ease;
    }
    
    .form-group input:focus {
      border-color: #f3a42e;
      box-shadow: 0 0 0 3px rgba(243, 164, 46, 0.2);
      outline: none;
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
      border: none;
    }
    
    .btn-primary {
      background: #f3a42e;
      color: white;
    }
    
    .btn-primary:hover {
      background: #d48826;
      transform: translateY(-2px);
      box-shadow: 0 4px 12px rgba(243, 164, 46, 0.3);
    }
    
    .btn-danger {
      background: #e74c3c;
      color: white;
    }
    
    .btn-danger:hover {
      background: #c0392b;
      transform: translateY(-2px);
      box-shadow: 0 4px 12px rgba(231, 76, 60, 0.3);
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
    
    .danger-zone {
      margin-top: 40px;
      padding: 20px;
      border: 1px dashed #e74c3c;
      border-radius: 8px;
      background: rgba(231, 76, 60, 0.05);
    }
    
    .danger-zone h3 {
      color: #e74c3c;
      margin-bottom: 15px;
    }
    
    .danger-zone p {
      margin-bottom: 20px;
      color: #666;
    }
    
    /* Modal styles */
    .modal {
      display: none;
      position: fixed;
      top: 0;
      left: 0;
      width: 100%;
      height: 100%;
      background: rgba(0,0,0,0.5);
      z-index: 2000;
      justify-content: center;
      align-items: center;
    }
    
    .modal-content {
      background: white;
      padding: 30px;
      border-radius: 10px;
      width: 90%;
      max-width: 500px;
      position: relative;
    }
    
    .close-modal {
      position: absolute;
      top: 15px;
      right: 15px;
      background: none;
      border: none;
      font-size: 1.5rem;
      cursor: pointer;
    }
    
    .modal-title {
      margin-bottom: 20px;
      color: #1E2A38;
    }
    
    .modal-actions {
      display: flex;
      justify-content: flex-end;
      gap: 10px;
      margin-top: 20px;
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
      .settings-header h1 {
        font-size: 2rem;
      }
      
      .settings-content {
        padding: 20px;
      }
      
      .settings-tabs {
        flex-direction: column;
      }
      
      .settings-tab {
        border-bottom: 1px solid #eee;
        border-left: 3px solid transparent;
      }
      
      .settings-tab.active {
        border-bottom-color: #eee;
        border-left-color: #f3a42e;
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
    <a href="../frontendc/index3.php" class="navbar-logo">Guide <span>Me</span></a>
    <div class="navbar-links">
      <a href="../frontendc/index3.php#home">Home</a>
      <a href="../frontendc/index3.php#services">Services</a>
      <a href="../frontendc/index3.php#guides">Guides</a>
      <a href="../frontendc/index3.php#contact">Contact</a>
    </div>
  </div>

  <div class="settings-container">
    <div class="settings-header">
      <h1>Account Settings</h1>
      <p>Manage your account preferences and security</p>
    </div>
    
    <div class="settings-card">
      <div class="settings-tabs">
        <div class="settings-tab active" data-tab="profile">Profile Settings</div>
        <div class="settings-tab" data-tab="security">Security</div>
        <div class="settings-tab" data-tab="account">Account Management</div>
      </div>
      
      <div class="settings-content">
        <!-- Profile Settings Section -->
        <div class="settings-section active" id="profile-section">
          <h2>Profile Settings</h2>
          <form id="profileForm">
            <div class="form-group">
              <label for="fullName">Full Name</label>
              <input type="text" id="fullName" name="full_name" value="<?php echo htmlspecialchars($user['full_name']); ?>">
            </div>
            
            <div class="form-group">
              <label for="email">Email Address</label>
              <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($user['email']); ?>">
            </div>
            
            <div class="form-group">
              <label for="phone">Phone Number</label>
              <input type="tel" id="phone" name="phone" value="<?php echo htmlspecialchars($user['phone_number']); ?>">
            </div>
            
            <button type="submit" class="btn btn-primary">
              <i class="fas fa-save"></i> Save Changes
            </button>
          </form>
        </div>
        
        <!-- Security Section -->
        <div class="settings-section" id="security-section">
          <h2>Security Settings</h2>
          <form id="passwordForm">
            <div class="form-group">
              <label for="currentPassword">Current Password</label>
              <input type="password" id="currentPassword" name="current_password" required>
            </div>
            
            <div class="form-group">
              <label for="newPassword">New Password</label>
              <input type="password" id="newPassword" name="new_password" required>
            </div>
            
            <div class="form-group">
              <label for="confirmPassword">Confirm New Password</label>
              <input type="password" id="confirmPassword" name="confirm_password" required>
            </div>
            
            <button type="submit" class="btn btn-primary">
              <i class="fas fa-lock"></i> Change Password
            </button>
          </form>
        </div>
        
        <!-- Account Management Section -->
        <div class="settings-section" id="account-section">
          <h2>Account Management</h2>
          <p>Manage your account preferences and make changes to your account status.</p>
          
          <div class="danger-zone">
            <h3><i class="fas fa-exclamation-triangle"></i> Danger Zone</h3>
            <p>Once you delete your account, there is no going back. Please be certain.</p>
            <button id="deleteAccountBtn" class="btn btn-danger">
              <i class="fas fa-trash-alt"></i> Delete Account
            </button>
          </div>
        </div>
      </div>
    </div>
  </div>
  
  <!-- Delete Account Confirmation Modal -->
  <div id="deleteModal" class="modal">
    <div class="modal-content">
      <button class="close-modal" id="closeDeleteModal">&times;</button>
      <h3 class="modal-title">Delete Account</h3>
      <p>Are you sure you want to delete your account? This action cannot be undone.</p>
      <p>Please enter your password to confirm:</p>
      <form id="deleteAccountForm">
        <div class="form-group">
          <input type="password" id="deletePassword" name="password" placeholder="Enter your password" required>
        </div>
        <div class="modal-actions">
          <button type="button" class="btn btn-outline" id="cancelDelete">Cancel</button>
          <button type="submit" class="btn btn-danger">Delete Account</button>
        </div>
      </form>
    </div>
  </div>

  <script>
    document.addEventListener('DOMContentLoaded', function() {
      // Tab switching functionality
      const tabs = document.querySelectorAll('.settings-tab');
      const sections = document.querySelectorAll('.settings-section');
      
      tabs.forEach(tab => {
        tab.addEventListener('click', function() {
          const tabId = this.getAttribute('data-tab');
          
          // Remove active class from all tabs and sections
          tabs.forEach(t => t.classList.remove('active'));
          sections.forEach(s => s.classList.remove('active'));
          
          // Add active class to current tab and section
          this.classList.add('active');
          document.getElementById(`${tabId}-section`).classList.add('active');
        });
      });
      
      // Profile form submission
      const profileForm = document.getElementById('profileForm');
      profileForm.addEventListener('submit', function(e) {
        e.preventDefault();
        
        const formData = new FormData(this);
        
        fetch('../frontendc/update_profile.php', {
          method: 'POST',
          body: formData
        })
        .then(response => response.json())
        .then(data => {
          if (data.success) {
            alert('Profile updated successfully!');
          } else {
            alert('Error: ' + data.message);
          }
        })
        .catch(error => {
          console.error('Error:', error);
          alert('An error occurred while updating the profile.');
        });
      });
      
      // Password change form submission
      const passwordForm = document.getElementById('passwordForm');
      passwordForm.addEventListener('submit', function(e) {
        e.preventDefault();
        
        const newPassword = document.getElementById('newPassword').value;
        const confirmPassword = document.getElementById('confirmPassword').value;
        
        if (newPassword !== confirmPassword) {
          alert('New passwords do not match!');
          return;
        }
        
        const formData = new FormData(this);
        
        fetch('../user_profile/change_password.php', {
          method: 'POST',
          body: formData
        })
        .then(response => response.json())
        .then(data => {
          if (data.success) {
            alert('Password changed successfully!');
            passwordForm.reset();
          } else {
            alert('Error: ' + data.message);
          }
        })
        .catch(error => {
          console.error('Error:', error);
          alert('An error occurred while changing the password.');
        });
      });
      
      // Delete account modal
      const deleteAccountBtn = document.getElementById('deleteAccountBtn');
      const deleteModal = document.getElementById('deleteModal');
      const closeDeleteModal = document.getElementById('closeDeleteModal');
      const cancelDelete = document.getElementById('cancelDelete');
      
      deleteAccountBtn.addEventListener('click', function() {
        deleteModal.style.display = 'flex';
      });
      
      function closeModal() {
        deleteModal.style.display = 'none';
      }
      
      closeDeleteModal.addEventListener('click', closeModal);
      cancelDelete.addEventListener('click', closeModal);
      
      // Close modal when clicking outside
      window.addEventListener('click', function(e) {
        if (e.target === deleteModal) {
          closeModal();
        }
      });
      
      // Delete account form submission
      const deleteAccountForm = document.getElementById('deleteAccountForm');
      deleteAccountForm.addEventListener('submit', function(e) {
        e.preventDefault();
        
        const formData = new FormData(this);
        
        fetch('../user_profile/delete_account.php', {
          method: 'POST',
          body: formData
        })
        .then(response => response.json())
        .then(data => {
          if (data.success) {
            alert('Your account has been deleted successfully.');
            window.location.href = '../login.php';
          } else {
            alert('Error: ' + data.message);
          }
        })
        .catch(error => {
          console.error('Error:', error);
          alert('An error occurred while deleting the account.');
        });
      });
    });
  </script>
</body>
</html>