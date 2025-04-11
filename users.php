<?php
session_start();
require_once '../database.php';

// Check if user is logged in and is admin
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../login.php?error=unauthorized");
    exit();
}

// Fetch all users
$users = [];
$query = "SELECT * FROM users ORDER BY created_at DESC";
$result = $conn->query($query);

if ($result && $result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $users[] = $row;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>User Management - Admin Dashboard</title>
  <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;500;600;700&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
  <style>
    /* Same styles as index.php and services.php */
    /* ... (copy all the styles from services.php) ... */
    
    /* Additional styles for users page */
    .user-avatar {
      width: 40px;
      height: 40px;
      border-radius: 50%;
      object-fit: cover;
    }
    
    .badge {
      display: inline-block;
      padding: 0.25rem 0.5rem;
      border-radius: 30px;
      font-size: 0.75rem;
      font-weight: 600;
    }
    
    .badge-success {
      background-color: rgba(40, 167, 69, 0.1);
      color: #28a745;
    }
    
    .badge-warning {
      background-color: rgba(255, 193, 7, 0.1);
      color: #ffc107;
    }
    
    .badge-danger {
      background-color: rgba(220, 53, 69, 0.1);
      color: #dc3545;
    }
    
    .badge-info {
      background-color: rgba(23, 162, 184, 0.1);
      color: #17a2b8;
    }
    
    .badge-secondary {
      background-color: rgba(108, 117, 125, 0.1);
      color: #6c757d;
    }
    
    .user-search {
      display: flex;
      gap: 1rem;
      margin-bottom: 1.5rem;
    }
    
    .search-input {
      flex: 1;
      padding: 0.75rem 1rem;
      border: 1px solid #ddd;
      border-radius: 6px;
      font-size: 1rem;
    }
    
    .search-btn {
      background-color: var(--secondary-color);
      color: white;
      border: none;
      padding: 0 1.5rem;
      border-radius: 6px;
      cursor: pointer;
      transition: background 0.3s;
    }
    
    .search-btn:hover {
      background-color: #d48826;
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
      <li><a href="users.php" class="active"><i class="fas fa-users"></i> Users</a></li>
      <li><a href="guides.php"><i class="fas fa-hiking"></i> Guides</a></li>
      <li><a href="services.php"><i class="fas fa-map-marked-alt"></i> Services</a></li>
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
        <div class="admin-info
  <!-- Main content section that needs to be completed -->
  <div class="admin-info">
    <h4>Admin User</h4>
    <p>Administrator</p>
  </div>
</div>
</div>
</header>

<!-- Main Content -->
<main class="main-content">
  <div class="users-header">
    <h1 class="page-title">User Management</h1>
    <a href="add_user.php" class="btn-add-service">
      <i class="fas fa-user-plus"></i> Add New User
    </a>
  </div>
  
  <?php if (isset($_GET['success'])): ?>
  <div class="alert alert-success">
    <?php echo htmlspecialchars($_GET['success']); ?>
  </div>
  <?php endif; ?>
  
  <?php if (isset($_GET['error'])): ?>
  <div class="alert alert-danger">
    <?php echo htmlspecialchars($_GET['error']); ?>
  </div>
  <?php endif; ?>
  
  <div class="user-search">
    <form action="users.php" method="GET" class="search-form">
      <input type="text" name="search" placeholder="Search users..." class="search-input" value="<?php echo isset($_GET['search']) ? htmlspecialchars($_GET['search']) : ''; ?>">
      <button type="submit" class="search-btn"><i class="fas fa-search"></i></button>
    </form>
  </div>
  
  <div class="card">
    <table class="services-table">
      <thead>
        <tr>
          <th>ID</th>
          <th>Avatar</th>
          <th>Name</th>
          <th>Email</th>
          <th>Role</th>
          <th>Status</th>
          <th>Joined Date</th>
          <th>Actions</th>
        </tr>
      </thead>
      <tbody>
        <?php if (count($users) > 0): ?>
          <?php foreach ($users as $user): ?>
            <tr>
              <td><?php echo $user['id']; ?></td>
              <td>
                <img src="<?php echo !empty($user['avatar']) ? '../' . $user['avatar'] : 'https://ui-avatars.com/api/?name=' . urlencode($user['name']) . '&background=random'; ?>" alt="<?php echo htmlspecialchars($user['name']); ?>" class="user-avatar">
              </td>
              <td><?php echo htmlspecialchars($user['name']); ?></td>
              <td><?php echo htmlspecialchars($user['email']); ?></td>
              <td>
                <span class="badge <?php echo $user['role'] === 'admin' ? 'badge-info' : 'badge-secondary'; ?>">
                  <?php echo ucfirst($user['role']); ?>
                </span>
              </td>
              <td>
                <span class="badge <?php echo $user['status'] === 'active' ? 'badge-success' : 'badge-danger'; ?>">
                  <?php echo ucfirst($user['status']); ?>
                </span>
              </td>
              <td><?php echo date('M d, Y', strtotime($user['created_at'])); ?></td>
              <td>
                <div class="service-actions">
                  <a href="view_user.php?id=<?php echo $user['id']; ?>" class="btn-view" title="View">
                    <i class="fas fa-eye"></i>
                  </a>
                  <a href="edit_user.php?id=<?php echo $user['id']; ?>" class="btn-edit" title="Edit">
                    <i class="fas fa-edit"></i>
                  </a>
                  <a href="javascript:void(0);" onclick="confirmDelete(<?php echo $user['id']; ?>)" class="btn-delete" title="Delete">
                    <i class="fas fa-trash"></i>
                  </a>
                </div>
              </td>
            </tr>
          <?php endforeach; ?>
        <?php else: ?>
          <tr>
            <td colspan="8" style="text-align: center; padding: 2rem;">No users found.</td>
          </tr>
        <?php endif; ?>
      </tbody>
    </table>
  </div>
  
  <div class="pagination">
    <a href="#" class="pagination-item"><i class="fas fa-chevron-left"></i></a>
    <a href="#" class="pagination-item active">1</a>
    <a href="#" class="pagination-item">2</a>
    <a href="#" class="pagination-item">3</a>
    <a href="#" class="pagination-item"><i class="fas fa-chevron-right"></i></a>
  </div>
</main>

<!-- Delete Confirmation Modal (to be implemented with JavaScript) -->
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
  
  // Confirm delete function
  function confirmDelete(userId) {
    if (confirm('Are you sure you want to delete this user? This action cannot be undone.')) {
      window.location.href = `users.php?delete=${userId}`;
    }
  }
  
  // Auto-hide alerts after 5 seconds
  setTimeout(() => {
    const alerts = document.querySelectorAll('.alert');
    alerts.forEach(alert => {
      alert.style.opacity = '0';
      setTimeout(() => {
        alert.style.display = 'none';
      }, 500);
    });
  }, 5000);
</script>
</body>
</html>