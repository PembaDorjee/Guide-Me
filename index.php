<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Guide Me - Home</title>
  <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;600;700&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
  <style>
    * {
      box-sizing: border-box;
      margin: 0;
      padding: 0;
      scroll-behavior: smooth;
    }

    html {
      scroll-padding-top: 80px;
    }

    body {
      font-family: 'Montserrat', sans-serif;
      background: #f0f4f8;
      color: #333;
    }

    /* Updated nav styles for better alignment */
    nav {
      background-color: #1E2A38;
      padding: 1rem 2rem;
      display: flex;
      justify-content: space-between;
      align-items: center;
      color: white;
      position: fixed;
      top: 0;
      left: 0;
      width: 100%;
      z-index: 1000;
      transition: background 0.3s;
      box-shadow: 0 2px 10px rgba(0,0,0,0.1);
    }
    
    /* Profile styles */
    .profile-container {
      position: relative;
      cursor: pointer;
    }
    
    .profile-icon {
      color: white;
      font-size: 1.5rem;
      transition: color 0.3s;
    }
    
    .profile-icon:hover {
      color: #f3a42e;
    }
    
    .profile-avatar {
      width: 40px;
      height: 40px;
      border-radius: 50%;
      object-fit: cover;
      border: 2px solid #f3a42e;
      transition: transform 0.3s;
    }
    
    .profile-avatar:hover {
      transform: scale(1.1);
    }
    
    .profile-dropdown {
      position: absolute;
      right: 0;
      top: 45px;
      background: white;
      border-radius: 8px;
      box-shadow: 0 4px 20px rgba(0,0,0,0.1);
      min-width: 200px;
      display: none;
      z-index: 1001;
      overflow: hidden;
    }
    
    .profile-dropdown.active {
      display: block;
      animation: fadeIn 0.3s ease;
    }
    
    .profile-menu {
      list-style: none;
      padding: 0;
      margin: 0;
    }
    
    .profile-menu-item {
      padding: 12px 16px;
      display: flex;
      align-items: center;
      gap: 10px;
      color: #333;
      transition: background 0.3s;
      cursor: pointer;
      border-bottom: 1px solid #f0f0f0;
    }
    
    .profile-menu-item:last-child {
      border-bottom: none;
    }
    
    .profile-menu-item:hover {
      background: #f5f5f5;
    }
    
    .profile-menu-item i {
      width: 16px;
      color: #f3a42e;
    }
    
    .logo {
      font-size: 2rem;
      font-weight: 700;
      color: #15d455;
    }

    .logo span {
      color: #f3a42e;
    }

    .nav-links {
      display: flex;
      gap: 2rem;
    }

    .nav-links a {
      color: white;
      text-decoration: none;
      font-weight: 500;
      transition: color 0.3s;
      position: relative;
    }

    .nav-links a.active::after {
      content: '';
      position: absolute;
      bottom: -6px;
      left: 0;
      width: 100%;
      height: 2px;
      background: #f3a42e;
    }

    .hero {
      background: url('uploads/home.jpg') no-repeat center center/cover;
      height: 100vh;
      display: flex;
      align-items: center;
      justify-content: center;
      text-align: center;
      position: relative;
      animation: fadeIn 2s ease;
    }

    @keyframes fadeIn {
      from { opacity: 0; transform: translateY(20px); }
      to { opacity: 1; transform: translateY(0); }
    }

    .hero::after {
      content: '';
      position: absolute;
      top: 0;
      left: 0;
      right: 0;
      bottom: 0;
      background: rgba(0, 0, 0, 0.6);
    }

    .hero-content {
      position: relative;
      z-index: 1;
      color: white;
      max-width: 700px;
      padding: 2rem;
    }

    .hero-content h1 {
      font-size: 3.5rem;
      font-weight: 700;
      margin-bottom: 1rem;
    }

    .hero-content p {
      font-size: 1.2rem;
      margin-bottom: 2rem;
    }

    .hero-content button {
      background: #f3a42e;
      color: white;
      border: none;
      padding: 0.8rem 2rem;
      font-size: 1.1rem;
      border-radius: 6px;
      cursor: pointer;
      transition: background 0.3s;
    }

    .hero-content button:hover {
      background: #d48826;
    }

    .section-title {
      text-align: center;
      margin: 6rem 0 2rem;
      font-size: 2.5rem;
      font-weight: 600;
      color: #1E2A38;
    }

    .services {
      display: grid;
      grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
      gap: 2rem;
      padding: 2rem;
      max-width: 1200px;
      margin: 0 auto;
      animation: fadeIn 2s ease;
    }

    .service-card {
      height: 350px;
      position: relative;
      overflow: hidden;
      border-radius: 12px;
      box-shadow: 0 6px 20px rgba(0, 0, 0, 0.15);
      cursor: pointer;
      transition: transform 0.4s ease, box-shadow 0.4s ease;
      display: flex;
      flex-direction: column;
    }

    .service-card:hover {
      transform: translateY(-8px);
      box-shadow: 0 12px 24px rgba(0, 0, 0, 0.2);
    }

    .service-card img {
      width: 100%;
      height: 100%;
      object-fit: cover;
      display: block;
    }

    .service-card h3 {
      position: absolute;
      bottom: 0;
      left: 0;
      right: 0;
      background: rgba(0, 0, 0, 0.5);
      color: white;
      margin: 0;
      padding: 1rem;
      font-size: 1.5rem;
      text-align: center;
      font-weight: 600;
    }

    /********************************guide*************************/

    .guides-grid {
  max-width: 1200px;
  margin: 2rem auto 4rem;
  padding: 0 2rem;
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(260px, 1fr));
  gap: 2rem;
}

.guide-card-v2 {
  background: #fff;
  border-radius: 16px;
  padding: 2rem 1.5rem;
  text-align: center;
  box-shadow: 0 8px 30px rgba(0, 0, 0, 0.08);
  transition: transform 0.3s ease, box-shadow 0.3s ease;
}

.guide-card-v2:hover {
  transform: translateY(-8px);
  box-shadow: 0 16px 40px rgba(0, 0, 0, 0.12);
}

.guide-card-v2 img {
  width: 100px;
  height: 100px;
  object-fit: cover;
  border-radius: 50%;
  margin-bottom: 1rem;
  border: 3px solid #f3a42e;
}

.guide-card-v2 h4 {
  font-size: 0.9rem;
  color: #999;
  letter-spacing: 1px;
  margin-bottom: 0.3rem;
  text-transform: uppercase;
}

.guide-card-v2 h3 {
  font-size: 1.2rem;
  font-weight: 700;
  margin-bottom: 0.5rem;
  color: #1E2A38;
}

.guide-card-v2 p {
  font-size: 0.95rem;
  color: #555;
  margin-bottom: 1rem;
}

.social-icons {
  display: flex;
  justify-content: center;
  gap: 1rem;
}

.social-icons .icon {
  width: 24px;
  height: 24px;
  filter: grayscale(100%) brightness(0.5);
  transition: filter 0.3s ease;
}

.social-icons .icon:hover {
  filter: none;
}



    /**********Contact********/

    .contact-wrapper {
    display: flex;
    flex-wrap: wrap;
    justify-content: space-between;
    background: #f5f5f5;
    padding: 4rem 2rem;
    gap: 2rem;
  }
  .contact-info, .contact-form {
    flex: 1;
    min-width: 300px;
    background: white;
    border-radius: 12px;
    padding: 2rem;
    box-shadow: 0 4px 15px rgba(0, 0, 0, 0.08);
  }
  .contact-info h3, .contact-form h3 {
    margin-bottom: 1rem;
    color: #1E2A38;
    font-size: 1.6rem;
  }
  .contact-info ul {
    list-style: none;
    padding: 0;
    margin: 1rem 0;
  }
  .contact-info ul li {
    margin: 0.6rem 0;
    font-size: 1rem;
    color: #444;
  }
  .socials {
    margin-top: 1.5rem;
    display: flex;
    align-items: center;
    gap: 1rem;
    font-size: 1.2rem;
  }
  .contact-form form {
    display: flex;
    flex-direction: column;
    gap: 1rem;
  }
  .contact-form input,
  .contact-form textarea {
    padding: 0.8rem 1rem;
    border: 1px solid #ccc;
    border-radius: 8px;
    font-size: 1rem;
  }
  .contact-form button {
    padding: 0.9rem 2rem;
    border: none;
    background: #f3a42e;
    color: white;
    font-weight: 600;
    font-size: 1rem;
    border-radius: 8px;
    cursor: pointer;
    transition: background 0.3s ease;
  }
  .contact-form button:hover {
    background: #d48826;
  }
  .contact-form small {
    font-size: 0.75rem;
    color: #666;
  }
  .map-container iframe {
    width: 100%;
    height: 300px;
    border: none;
    display: block;
  }

    footer {
      background: #1E2A38;
      color: white;
      text-align: center;
      padding: 2rem 1rem;
      margin-top: 4rem;
    }
  </style>
</head>
<body>
    <?php
    // Move session_start to the very top of the file before any HTML output
    session_start();
    require_once '../database.php';
    
    // Debug session data if needed
    // echo '<pre>'; print_r($_SESSION); echo '</pre>';
    ?>
    <!DOCTYPE html>
    <html lang="en">
    <head>
      <meta charset="UTF-8" />
      <meta name="viewport" content="width=device-width, initial-scale=1.0" />
      <title>Guide Me - Home</title>
      <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;600;700&display=swap" rel="stylesheet">
      <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
      <style>
        * {
          box-sizing: border-box;
          margin: 0;
          padding: 0;
          scroll-behavior: smooth;
        }
    
        html {
          scroll-padding-top: 80px;
        }
    
        body {
          font-family: 'Montserrat', sans-serif;
          background: #f0f4f8;
          color: #333;
        }
    
        /* Updated nav styles for better alignment */
        nav {
          background-color: #1E2A38;
          padding: 1rem 2rem;
          display: flex;
          justify-content: space-between;
          align-items: center;
          color: white;
          position: fixed;
          top: 0;
          left: 0;
          width: 100%;
          z-index: 1000;
          transition: background 0.3s;
          box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        
        /* Profile styles */
        .profile-container {
          position: relative;
          cursor: pointer;
        }
        
        .profile-icon {
          color: white;
          font-size: 1.5rem;
          transition: color 0.3s;
        }
        
        .profile-icon:hover {
          color: #f3a42e;
        }
        
        .profile-avatar {
          width: 40px;
          height: 40px;
          border-radius: 50%;
          object-fit: cover;
          border: 2px solid #f3a42e;
          transition: transform 0.3s;
        }
        
        .profile-avatar:hover {
          transform: scale(1.1);
        }
        
        .profile-dropdown {
          position: absolute;
          right: 0;
          top: 45px;
          background: white;
          border-radius: 8px;
          box-shadow: 0 4px 20px rgba(0,0,0,0.1);
          min-width: 200px;
          display: none;
          z-index: 1001;
          overflow: hidden;
        }
        
        .profile-dropdown.active {
          display: block;
          animation: fadeIn 0.3s ease;
        }
        
        .profile-menu {
          list-style: none;
          padding: 0;
          margin: 0;
        }
        
        .profile-menu-item {
          padding: 12px 16px;
          display: flex;
          align-items: center;
          gap: 10px;
          color: #333;
          transition: background 0.3s;
          cursor: pointer;
          border-bottom: 1px solid #f0f0f0;
        }
        
        .profile-menu-item:last-child {
          border-bottom: none;
        }
        
        .profile-menu-item:hover {
          background: #f5f5f5;
        }
        
        .profile-menu-item i {
          width: 16px;
          color: #f3a42e;
        }
        
        .logo {
          font-size: 2rem;
          font-weight: 700;
          color: #15d455;
        }
    
        .logo span {
          color: #f3a42e;
        }
    
        .nav-links {
          display: flex;
          gap: 2rem;
        }
    
        .nav-links a {
          color: white;
          text-decoration: none;
          font-weight: 500;
          transition: color 0.3s;
          position: relative;
        }
    
        .nav-links a.active::after {
          content: '';
          position: absolute;
          bottom: -6px;
          left: 0;
          width: 100%;
          height: 2px;
          background: #f3a42e;
        }
    
        .hero {
          background: url('uploads/home.jpg') no-repeat center center/cover;
          height: 100vh;
          display: flex;
          align-items: center;
          justify-content: center;
          text-align: center;
          position: relative;
          animation: fadeIn 2s ease;
        }
    
        @keyframes fadeIn {
          from { opacity: 0; transform: translateY(20px); }
          to { opacity: 1; transform: translateY(0); }
        }
    
        .hero::after {
          content: '';
          position: absolute;
          top: 0;
          left: 0;
          right: 0;
          bottom: 0;
          background: rgba(0, 0, 0, 0.6);
        }
    
        .hero-content {
          position: relative;
          z-index: 1;
          color: white;
          max-width: 700px;
          padding: 2rem;
        }
    
        .hero-content h1 {
          font-size: 3.5rem;
          font-weight: 700;
          margin-bottom: 1rem;
        }
    
        .hero-content p {
          font-size: 1.2rem;
          margin-bottom: 2rem;
        }
    
        .hero-content button {
          background: #f3a42e;
          color: white;
          border: none;
          padding: 0.8rem 2rem;
          font-size: 1.1rem;
          border-radius: 6px;
          cursor: pointer;
          transition: background 0.3s;
        }
    
        .hero-content button:hover {
          background: #d48826;
        }
    
        .section-title {
          text-align: center;
          margin: 6rem 0 2rem;
          font-size: 2.5rem;
          font-weight: 600;
          color: #1E2A38;
        }
    
        .services {
          display: grid;
          grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
          gap: 2rem;
          padding: 2rem;
          max-width: 1200px;
          margin: 0 auto;
          animation: fadeIn 2s ease;
        }
    
        .service-card {
          height: 350px;
          position: relative;
          overflow: hidden;
          border-radius: 12px;
          box-shadow: 0 6px 20px rgba(0, 0, 0, 0.15);
          cursor: pointer;
          transition: transform 0.4s ease, box-shadow 0.4s ease;
          display: flex;
          flex-direction: column;
        }
    
        .service-card:hover {
          transform: translateY(-8px);
          box-shadow: 0 12px 24px rgba(0, 0, 0, 0.2);
        }
    
        .service-card img {
          width: 100%;
          height: 100%;
          object-fit: cover;
          display: block;
        }
    
        .service-card h3 {
          position: absolute;
          bottom: 0;
          left: 0;
          right: 0;
          background: rgba(0, 0, 0, 0.5);
          color: white;
          margin: 0;
          padding: 1rem;
          font-size: 1.5rem;
          text-align: center;
          font-weight: 600;
        }
    
        /********************************guide*************************/
    
        .guides-grid {
      max-width: 1200px;
      margin: 2rem auto 4rem;
      padding: 0 2rem;
      display: grid;
      grid-template-columns: repeat(auto-fit, minmax(260px, 1fr));
      gap: 2rem;
    }
    
        .guide-card-v2 {
          background: #fff;
          border-radius: 16px;
          padding: 2rem 1.5rem;
          text-align: center;
          box-shadow: 0 8px 30px rgba(0, 0, 0, 0.08);
          transition: transform 0.3s ease, box-shadow 0.3s ease;
        }
    
        .guide-card-v2:hover {
          transform: translateY(-8px);
          box-shadow: 0 16px 40px rgba(0, 0, 0, 0.12);
        }
    
        .guide-card-v2 img {
          width: 100px;
          height: 100px;
          object-fit: cover;
          border-radius: 50%;
          margin-bottom: 1rem;
          border: 3px solid #f3a42e;
        }
    
        .guide-card-v2 h4 {
          font-size: 0.9rem;
          color: #999;
          letter-spacing: 1px;
          margin-bottom: 0.3rem;
          text-transform: uppercase;
        }
    
        .guide-card-v2 h3 {
          font-size: 1.2rem;
          font-weight: 700;
          margin-bottom: 0.5rem;
          color: #1E2A38;
        }
    
        .guide-card-v2 p {
          font-size: 0.95rem;
          color: #555;
          margin-bottom: 1rem;
        }
    
        .social-icons {
          display: flex;
          justify-content: center;
          gap: 1rem;
        }
    
        .social-icons .icon {
          width: 24px;
          height: 24px;
          filter: grayscale(100%) brightness(0.5);
          transition: filter 0.3s ease;
        }
    
        .social-icons .icon:hover {
          filter: none;
        }
    
    
    
        /**********Contact********/
    
        .contact-wrapper {
        display: flex;
        flex-wrap: wrap;
        justify-content: space-between;
        background: #f5f5f5;
        padding: 4rem 2rem;
        gap: 2rem;
      }
      .contact-info, .contact-form {
        flex: 1;
        min-width: 300px;
        background: white;
        border-radius: 12px;
        padding: 2rem;
        box-shadow: 0 4px 15px rgba(0, 0, 0, 0.08);
      }
      .contact-info h3, .contact-form h3 {
        margin-bottom: 1rem;
        color: #1E2A38;
        font-size: 1.6rem;
      }
      .contact-info ul {
        list-style: none;
        padding: 0;
        margin: 1rem 0;
      }
      .contact-info ul li {
        margin: 0.6rem 0;
        font-size: 1rem;
        color: #444;
      }
      .socials {
        margin-top: 1.5rem;
        display: flex;
        align-items: center;
        gap: 1rem;
        font-size: 1.2rem;
      }
      .contact-form form {
        display: flex;
        flex-direction: column;
        gap: 1rem;
      }
      .contact-form input,
      .contact-form textarea {
        padding: 0.8rem 1rem;
        border: 1px solid #ccc;
        border-radius: 8px;
        font-size: 1rem;
      }
      .contact-form button {
        padding: 0.9rem 2rem;
        border: none;
        background: #f3a42e;
        color: white;
        font-weight: 600;
        font-size: 1rem;
        border-radius: 8px;
        cursor: pointer;
        transition: background 0.3s ease;
      }
      .contact-form button:hover {
        background: #d48826;
      }
      .contact-form small {
        font-size: 0.75rem;
        color: #666;
      }
      .map-container iframe {
        width: 100%;
        height: 300px;
        border: none;
        display: block;
      }
    
        footer {
          background: #1E2A38;
          color: white;
          text-align: center;
          padding: 2rem 1rem;
          margin-top: 4rem;
        }
      </style>
    </head>
    <body>
        <!-- Remove the duplicate session_start here -->
        <nav>
          <div class="logo">Guide <span>Me</span></div>
          <div class="nav-links">
            <a href="#home" class="nav-link active">Home</a>
            <a href="#services" class="nav-link">Services</a>
            <a href="#guides" class="nav-link">Guides</a>
            <a href="#contact" class="nav-link">Contact</a>
          </div>
          <!-- User profile container -->
          <div class="profile-container" id="profileTrigger">
            <?php if(isset($_SESSION['client_id'])): ?>
              <!-- Show user avatar if logged in -->
              <img src="<?php echo isset($_SESSION['avatar_path']) && !empty($_SESSION['avatar_path']) ? '../' . $_SESSION['avatar_path'] : '../uploads/default-avatar.png'; ?>" 
                   alt="<?php echo htmlspecialchars($_SESSION['full_name'] ?? 'User'); ?>" class="profile-avatar" id="currentAvatar">
            <?php else: ?>
              <!-- Show default icon if not logged in -->
              <i class="fas fa-user-circle profile-icon"></i>
            <?php endif; ?>
          </div>
          
          <!-- Profile dropdown menu -->
          <div class="profile-dropdown" id="profileDropdown">
            <ul class="profile-menu">
              <?php if(isset($_SESSION['client_id'])): ?>
                <!-- Logged in user menu options -->
                <li class="profile-menu-item" onclick="location.href='../user_profile/index.php'">
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
              <?php else: ?>
                <!-- Guest user menu options -->
                <li class="profile-menu-item" onclick="location.href='../login.php'">
                  <i class="fas fa-sign-in-alt"></i>
                  <span>Login</span>
                </li>
                <li class="profile-menu-item" onclick="location.href='../registration.php'">
                  <i class="fas fa-user-plus"></i>
                  <span>Register</span>
                </li>
              <?php endif; ?>
            </ul>
          </div>
        </nav>
        
        <section id="home" class="hero">
          <div class="hero-content">
            <h1>Discover Your Next Adventure</h1>
            <p>Find experienced guides and breathtaking trekking routes across the Himalayas.</p>
            <button onclick="location.href='trek.html'">Explore Treks</button>
          </div> 
        </section>
    
        <!--------------------------------Services------------------------------------------------>
      
        <h2 id="services-heading" class="section-title">Our Services</h2>
        <section class="services" id="services"></section>
    
    
     <!--------------------------------Guide------------------------------------------------>
    
     <h2 id="guides" class="section-title">Our Guides</h2>
<section class="guides-grid">
  <div class="guide-card-v2">
    <img src="uploads/G1.jpg" alt="Pasang Sherpa" />
    <h4>HIGH ALTITUDE EXPERT</h4>
    <h3>Pasang Sherpa</h3>
    <p>Over 85 treks completed guiding adventurers across Everest & Annapurna.</p>
    <div class="social-icons">
      <a href="https://www.facebook.com/pembadorjee.sherpa.9256/" target="_blank">
        <img src="https://cdn.jsdelivr.net/gh/simple-icons/simple-icons/icons/facebook.svg" alt="Facebook" class="icon" />
      </a>
      <a href="https://www.instagram.com/pem_ba7/" target="_blank">
        <img src="https://cdn.jsdelivr.net/gh/simple-icons/simple-icons/icons/instagram.svg" alt="Instagram" class="icon" />
      </a>
    </div>
  </div>

  <div class="guide-card-v2">
    <img src="uploads/G2.jpg" alt="Rita Tamang" />
    <h4>CULTURAL GUIDE</h4>
    <h3>Rita Tamang</h3>
    <p>Specialist in homestay & local village treks. 60+ treks led successfully.</p>
    <div class="social-icons">
      <a href="https://www.facebook.com/pembadorjee.sherpa.9256/" target="_blank">
        <img src="https://cdn.jsdelivr.net/gh/simple-icons/simple-icons/icons/facebook.svg" alt="Facebook" class="icon" />
      </a>
      <a href="https://www.instagram.com/pem_ba7/" target="_blank">
        <img src="https://cdn.jsdelivr.net/gh/simple-icons/simple-icons/icons/instagram.svg" alt="Instagram" class="icon" />
      </a>
    </div>
  </div>

  <div class="guide-card-v2">
    <img src="uploads/G3.jpg" alt="Dawa Yangzum Sherpa" />
    <h4>REMOTE REGION GUIDE</h4>
    <h3>Dawa Yangzum Sherpa</h3>
    <p>Focused on Upper Dolpo, Mustang & Manaslu circuits. 70+ treks completed.</p>
    <div class="social-icons">
      <a href="https://www.facebook.com/pembadorjee.sherpa.9256/" target="_blank">
        <img src="https://cdn.jsdelivr.net/gh/simple-icons/simple-icons/icons/facebook.svg" alt="Facebook" class="icon" />
      </a>
      <a href="https://www.instagram.com/pem_ba7/" target="_blank">
        <img src="https://cdn.jsdelivr.net/gh/simple-icons/simple-icons/icons/instagram.svg" alt="Instagram" class="icon" />
      </a>
    </div>
  </div>
</section>
     

    <!------------------Contact us----------------------------->

    <h2 id="contact" class="section-title">Contact Us</h2>
<section class="contact-wrapper">
  <div class="contact-info">
    <h3>Get In Touch</h3>
    <p>If you have any questions about trekking, need custom trip planning, or want to connect with an experienced guide — we're here to help. Reach out and let's start your next adventure!</p>
    <ul>
      <li><strong>📍 Address:</strong> Kathmandu, Nepal</li>
      <li><strong>📞 Phone:</strong> +977-9818560121</li>
      <li><strong>📧 Email:</strong> contact@guideme.com</li>
    </ul>
    <div class="socials">
      <span>Follow Us:</span>
      <a href="https://www.facebook.com/pembadorjee.sherpa.9256/" target="_blank">
        <img src="https://cdn.jsdelivr.net/gh/simple-icons/simple-icons/icons/facebook.svg" alt="Facebook" style="width: 24px; height: 24px;" />
      </a>
      <a href="https://www.instagram.com/pem_ba7/" target="_blank">
        <img src="https://cdn.jsdelivr.net/gh/simple-icons/simple-icons/icons/instagram.svg" alt="Instagram" style="width: 24px; height: 24px;" />
      </a>
      <a href="https://twitter.com" target="_blank">
        <img src="https://cdn.jsdelivr.net/gh/simple-icons/simple-icons/icons/twitter.svg" alt="Twitter" style="width: 24px; height: 24px;" />
      </a>
      <a href="https://www.linkedin.com" target="_blank">
        <img src="https://cdn.jsdelivr.net/gh/simple-icons/simple-icons/icons/linkedin.svg" alt="LinkedIn" style="width: 24px; height: 24px;" />
      </a>
    </div>
  </div>
  <div class="contact-form">
    <h3>Send a Message</h3>
    <form action="https://formspree.io/f/mnqejjwa" method="POST">
      <input type="text" name="name" placeholder="Your Name" required>
      <input type="email" name="email" placeholder="E-mail Address" required>
      <textarea name="message" rows="5" placeholder="Your Message" required></textarea>
      <small>By submitting you agree to our Privacy Policy.</small>
      <button type="submit">Submit</button>
    </form>
  </div>
</section>
<div class="map-container">
  <iframe src="https://www.google.com/maps?q=Kathmandu%2C%20Nepal&z=14&output=embed" frameborder="0" allowfullscreen></iframe>
</div>
  
    <footer id="contact">
      <p>&copy; 2025 Guide Me | Explore the Himalayas with trusted local guides.</p>
    </footer>
  
    <script>
      document.addEventListener('DOMContentLoaded', function() {
        // Navigation active state
        const navLinks = document.querySelectorAll('.nav-link');
        window.addEventListener('scroll', () => {
          const fromTop = window.scrollY;
          navLinks.forEach(link => {
            const section = document.querySelector(link.getAttribute('href'));
            if (
              section.offsetTop <= fromTop + 80 &&
              section.offsetTop + section.offsetHeight > fromTop + 80
            ) {
              navLinks.forEach(link => link.classList.remove('active'));
              link.classList.add('active');
            }
          });
        });

        // Profile dropdown logic
        const profileTrigger = document.getElementById('profileTrigger');
        const profileDropdown = document.getElementById('profileDropdown');
        
        profileTrigger.addEventListener('click', (e) => {
            e.stopPropagation();
            profileDropdown.classList.toggle('active');
        });
        
        // Close dropdown when clicking outside
        document.addEventListener('click', () => {
            profileDropdown.classList.remove('active');
        });
        
        // Add this code to fetch user data and update avatar
        <?php if(isset($_SESSION['client_id'])): ?>
        // Fetch user data to ensure avatar is updated
        fetch('../user_profile/get_client.php')
        .then(res => res.json())
        .then(data => {
          console.log('User data fetched:', data);
          if(data.avatar) {
            // Update avatar in the navbar
            const avatarPath = '../' + data.avatar;
            document.getElementById('currentAvatar').src = avatarPath;
            console.log('Avatar updated to:', avatarPath);
            
            // Update session data via AJAX
            fetch('../update_session.php', {
              method: 'POST',
              headers: {
                'Content-Type': 'application/json',
              },
              body: JSON.stringify({
                avatar_path: data.avatar
              })
            });
          }
        })
        .catch(error => console.error('Error fetching user data:', error));
        <?php endif; ?>

        // Fetch services
        fetch('../frontend/get_services.php')
        .then(response => response.json())
        .then(data => {
          const servicesSection = document.getElementById('services');
          servicesSection.innerHTML = '';

          data.forEach(service => {
            const div = document.createElement('div');
            div.className = 'service-card';
            div.innerHTML = `
              <img src="${service.image}" alt="${service.name}">
              <h3>${service.name}</h3>
            `;
            div.addEventListener('click', () => {
              console.log(`Service clicked: ${service.name}`);
              window.location.href = `service_details.php?id=${service.id}`;
            });
            servicesSection.appendChild(div);
          });
        })
        .catch(error => console.error('Error loading services:', error));
      });
    </script>
  </body>
</html>
