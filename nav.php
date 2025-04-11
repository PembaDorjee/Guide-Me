<nav id="mainNav">
  <div class="menu-toggle" id="mobileMenu">
    <i class="fas fa-bars"></i>
  </div>
  <div class="logo">Guide <span>Me</span></div>
  <div class="nav-links" id="navLinks">
    <a href="#home" class="nav-link active">Home</a>
    <a href="#services" class="nav-link">Services</a>
    <a href="#guides" class="nav-link">Guides</a>
    <a href="#contact" class="nav-link">Contact</a>
  </div>
  
  <div class="profile-container" id="profileTrigger">
    <i class="fas fa-user-circle profile-icon"></i>
  </div>

  <div class="profile-dropdown" id="profileDropdown">
    <ul class="profile-menu">
      <li class="profile-menu-item" onclick="location.href='user_profile.php'">
        <i class="fas fa-user"></i>
        <span>View Profile</span>
      </li>
      <li class="profile-menu-item" onclick="location.href='../user_profile/settings.php'">
        <i class="fas fa-cog"></i>
        <span>Account Settings</span>
      </li>
      <li class="profile-menu-item" onclick="location.href='../user_profile/notifications.php'">
        <i class="fas fa-bell"></i>
        <span>Notifications</span>
      </li>
      <li class="profile-menu-item" onclick="location.href='../logout.php'">
        <i class="fas fa-sign-out-alt"></i>
        <span>Logout</span>
      </li>
    </ul>
  </div>
</nav>