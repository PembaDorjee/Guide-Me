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
      background-image: radial-gradient(#d6e4f0 1px, transparent 1px);
      background-size: 20px 20px;
      overflow-x: hidden;
    }

    /* ========== NAVBAR ========== */
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
      transition: all 0.3s ease;
      box-shadow: 0 2px 10px rgba(0,0,0,0.1);
    }
    
    nav.scrolled {
      padding: 0.5rem 2rem;
      background-color: rgba(30, 42, 56, 0.98);
      backdrop-filter: blur(10px);
    }
    
    .logo {
      font-size: 1.8rem;
      font-weight: 700;
      color: #15d455;
      transition: all 0.3s ease;
      z-index: 1002;
    }

    .logo:hover {
      transform: scale(1.05);
    }

    .logo span {
      color: #f3a42e;
    }

    .nav-links {
      display: flex;
      gap: 2rem;
      align-items: center;
    }

    .nav-links a {
      color: white;
      text-decoration: none;
      font-weight: 500;
      transition: all 0.3s ease;
      position: relative;
      padding: 0.5rem 0;
    }

    .nav-links a::after {
      content: '';
      position: absolute;
      bottom: 0;
      left: 0;
      width: 0;
      height: 2px;
      background: #f3a42e;
      transition: width 0.3s ease;
    }

    .nav-links a:hover::after {
      width: 100%;
    }

    .nav-links a.active::after {
      width: 100%;
    }

    /* Mobile menu toggle */
    .menu-toggle {
      display: none;
      cursor: pointer;
      z-index: 1002;
    }

    .menu-toggle i {
      font-size: 1.8rem;
      color: white;
      transition: all 0.3s ease;
    }

    .menu-toggle i:hover {
      color: #f3a42e;
    }

    /* Profile styles */
    .profile-container {
      position: relative;
      cursor: pointer;
      z-index: 1002;
    }
    
    .profile-icon {
      color: white;
      font-size: 1.5rem;
      transition: all 0.3s ease;
    }
    
    .profile-icon:hover {
      color: #f3a42e;
      transform: scale(1.1);
    }
    
    .profile-avatar {
      width: 40px;
      height: 40px;
      border-radius: 50%;
      object-fit: cover;
      border: 2px solid #f3a42e;
      transition: all 0.3s ease;
    }
    
    .profile-avatar:hover {
      transform: scale(1.1);
      box-shadow: 0 0 10px rgba(243, 164, 46, 0.5);
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
      transform-origin: top right;
    }
    
    .profile-dropdown.active {
      display: block;
      animation: scaleIn 0.2s ease-out;
    }
    
    @keyframes scaleIn {
      0% { opacity: 0; transform: scale(0.9); }
      100% { opacity: 1; transform: scale(1); }
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
      transition: all 0.3s ease;
      cursor: pointer;
      border-bottom: 1px solid #f0f0f0;
    }
    
    .profile-menu-item:last-child {
      border-bottom: none;
    }
    
    .profile-menu-item:hover {
      background: #f5f5f5;
      padding-left: 20px;
    }
    
    .profile-menu-item i {
      width: 16px;
      color: #f3a42e;
      transition: all 0.3s ease;
    }
    
    .profile-menu-item:hover i {
      transform: translateX(3px);
    }

    /* Mobile menu styles */
    @media (max-width: 992px) {
      .menu-toggle {
        display: block;
        order: 1;
      }
      
      .logo {
        order: 2;
        margin: 0 auto;
      }
      
      .profile-container {
        order: 3;
      }
      
      .nav-links {
        position: fixed;
        top: 0;
        left: -100%;
        width: 80%;
        max-width: 300px;
        height: 100vh;
        background: #1E2A38;
        flex-direction: column;
        justify-content: center;
        align-items: flex-start;
        padding: 2rem;
        transition: all 0.5s ease;
        z-index: 1001;
      }
      
      .nav-links.active {
        left: 0;
        box-shadow: 5px 0 15px rgba(0,0,0,0.2);
      }
      
      .nav-links a {
        font-size: 1.2rem;
        padding: 1rem 0;
        width: 100%;
      }
      
      .profile-dropdown {
        top: auto;
        bottom: 100%;
        right: 0;
        transform-origin: bottom right;
      }
    }

    /* ========== HERO SECTION ========== */
    .hero {
      background: url('uploads/home.jpg') no-repeat center center/cover;
      height: 100vh;
      display: flex;
      align-items: center;
      justify-content: center;
      text-align: center;
      position: relative;
      animation: fadeIn 1.5s ease;
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
      transform: translateY(20px);
      opacity: 0;
      animation: slideUp 1s ease 0.3s forwards;
    }

    @keyframes slideUp {
      to { transform: translateY(0); opacity: 1; }
    }

    .hero-content h1 {
      font-size: clamp(2.5rem, 5vw, 3.5rem);
      font-weight: 700;
      margin-bottom: 1rem;
      text-shadow: 0 2px 10px rgba(0,0,0,0.3);
      line-height: 1.2;
    }

    .hero-content p {
      font-size: clamp(1rem, 2vw, 1.2rem);
      margin-bottom: 2rem;
      text-shadow: 0 1px 5px rgba(0,0,0,0.3);
    }

    .hero-content button {
      background: #f3a42e;
      color: white;
      border: none;
      padding: 0.8rem 2rem;
      font-size: 1.1rem;
      border-radius: 6px;
      cursor: pointer;
      transition: all 0.3s ease;
      position: relative;
      overflow: hidden;
      box-shadow: 0 4px 15px rgba(243, 164, 46, 0.3);
    }

    .hero-content button::after {
      content: '';
      position: absolute;
      top: 0;
      left: -100%;
      width: 100%;
      height: 100%;
      background: linear-gradient(90deg, transparent, rgba(255,255,255,0.2), transparent);
      transition: all 0.5s ease;
    }

    .hero-content button:hover {
      background: #d48826;
      transform: translateY(-3px);
      box-shadow: 0 6px 20px rgba(243, 164, 46, 0.4);
    }

    .hero-content button:hover::after {
      left: 100%;
    }

    /* ========== SECTION TITLE ========== */
    .section-title {
      text-align: center;
      margin: 6rem 0 2rem;
      font-size: clamp(2rem, 5vw, 2.5rem);
      font-weight: 600;
      color: #1E2A38;
      position: relative;
      display: inline-block;
      padding-bottom: 0.5rem;
    }

    .section-title::after {
      content: '';
      position: absolute;
      bottom: 0;
      left: 50%;
      transform: translateX(-50%);
      width: 80px;
      height: 4px;
      background: #f3a42e;
      border-radius: 2px;
    }

    /* ========== SERVICES ========== */
    .services {
      display: grid;
      grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
      gap: 2rem;
      padding: 2rem;
      max-width: 1200px;
      margin: 0 auto;
    }

    .service-card {
      height: 350px;
      position: relative;
      overflow: hidden;
      border-radius: 12px;
      box-shadow: 0 6px 20px rgba(0, 0, 0, 0.15);
      cursor: pointer;
      transition: all 0.4s ease;
      display: flex;
      flex-direction: column;
      transform: translateY(30px);
      opacity: 0;
      animation: fadeUp 0.6s ease forwards;
    }

    @keyframes fadeUp {
      to { transform: translateY(0); opacity: 1; }
    }

    .service-card:nth-child(1) { animation-delay: 0.1s; }
    .service-card:nth-child(2) { animation-delay: 0.2s; }
    .service-card:nth-child(3) { animation-delay: 0.3s; }
    .service-card:nth-child(4) { animation-delay: 0.4s; }

    .service-card:hover {
      transform: translateY(-8px);
      box-shadow: 0 12px 24px rgba(0, 0, 0, 0.2);
    }

    .service-card img {
      width: 100%;
      height: 100%;
      object-fit: cover;
      display: block;
      transition: transform 0.5s ease;
    }

    .service-card:hover img {
      transform: scale(1.05);
    }

    .service-card h3 {
      position: absolute;
      bottom: 0;
      left: 0;
      right: 0;
      background: linear-gradient(to top, rgba(0,0,0,0.8), transparent);
      color: white;
      margin: 0;
      padding: 1.5rem 1rem;
      font-size: 1.5rem;
      text-align: center;
      font-weight: 600;
      transition: all 0.3s ease;
    }

    .service-card:hover h3 {
      padding-bottom: 2rem;
    }

    /* ========== GUIDES ========== */
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
      transition: all 0.4s ease;
      transform: translateY(30px);
      opacity: 0;
      animation: fadeUp 0.6s ease forwards;
    }

    .guide-card-v2:nth-child(1) { animation-delay: 0.2s; }
    .guide-card-v2:nth-child(2) { animation-delay: 0.3s; }
    .guide-card-v2:nth-child(3) { animation-delay: 0.4s; }

    .guide-card-v2:hover {
      transform: translateY(-8px);
      box-shadow: 0 16px 40px rgba(0, 0, 0, 0.12);
    }

    .guide-card-v2 img {
      width: 120px;
      height: 120px;
      object-fit: cover;
      border-radius: 50%;
      margin-bottom: 1rem;
      border: 3px solid #f3a42e;
      transition: all 0.3s ease;
      box-shadow: 0 4px 15px rgba(0,0,0,0.1);
    }

    .guide-card-v2:hover img {
      transform: scale(1.05);
      box-shadow: 0 8px 25px rgba(0,0,0,0.15);
    }

    .guide-card-v2 h4 {
      font-size: 0.9rem;
      color: #999;
      letter-spacing: 1px;
      margin-bottom: 0.3rem;
      text-transform: uppercase;
    }

    .guide-card-v2 h3 {
      font-size: 1.3rem;
      font-weight: 700;
      margin-bottom: 0.5rem;
      color: #1E2A38;
      transition: color 0.3s ease;
    }

    .guide-card-v2:hover h3 {
      color: #f3a42e;
    }

    .guide-card-v2 p {
      font-size: 0.95rem;
      color: #555;
      margin-bottom: 1rem;
      transition: color 0.3s ease;
    }

    .guide-card-v2:hover p {
      color: #777;
    }

    .social-icons {
      display: flex;
      justify-content: center;
      gap: 1rem;
    }

    .social-icons a {
      width: 36px;
      height: 36px;
      border-radius: 50%;
      display: flex;
      align-items: center;
      justify-content: center;
      background: #f5f5f5;
      transition: all 0.3s ease;
    }

    .social-icons a:hover {
      background: #f3a42e;
      transform: translateY(-3px);
    }

    .social-icons a:hover .icon {
      filter: brightness(0) invert(1);
    }

    .social-icons .icon {
      width: 16px;
      height: 16px;
      filter: grayscale(100%) brightness(0.5);
      transition: all 0.3s ease;
    }

    /* ========== CONTACT ========== */
    .contact-wrapper {
      display: flex;
      flex-wrap: wrap;
      justify-content: space-between;
      background: #f5f5f5;
      padding: 4rem 2rem;
      gap: 2rem;
      background-image: linear-gradient(to bottom, rgba(240,244,248,0.9), rgba(240,244,248,0.9)), url('uploads/pattern.png');
      background-size: 300px;
    }
    
    .contact-info, .contact-form {
      flex: 1;
      min-width: 300px;
      background: white;
      border-radius: 12px;
      padding: 2rem;
      box-shadow: 0 4px 15px rgba(0, 0, 0, 0.08);
      transition: all 0.4s ease;
      transform: translateY(30px);
      opacity: 0;
    }
    
    .contact-info {
      animation: fadeUp 0.6s ease 0.2s forwards;
    }
    
    .contact-form {
      animation: fadeUp 0.6s ease 0.3s forwards;
    }
    
    .contact-info:hover, .contact-form:hover {
      transform: translateY(-5px);
      box-shadow: 0 8px 25px rgba(0, 0, 0, 0.12);
    }
    
    .contact-info h3, .contact-form h3 {
      margin-bottom: 1.5rem;
      color: #1E2A38;
      font-size: 1.6rem;
      position: relative;
      padding-bottom: 0.5rem;
    }
    
    .contact-info h3::after, .contact-form h3::after {
      content: '';
      position: absolute;
      bottom: 0;
      left: 0;
      width: 50px;
      height: 3px;
      background: #f3a42e;
    }
    
    .contact-info ul {
      list-style: none;
      padding: 0;
      margin: 1.5rem 0;
    }
    
    .contact-info ul li {
      margin: 1rem 0;
      font-size: 1rem;
      color: #444;
      display: flex;
      align-items: flex-start;
      gap: 10px;
    }
    
    .contact-info ul li i {
      color: #f3a42e;
      margin-top: 3px;
    }
    
    .socials {
      margin-top: 2rem;
      display: flex;
      align-items: center;
      gap: 1rem;
    }
    
    .socials a {
      width: 40px;
      height: 40px;
      border-radius: 50%;
      background: #f5f5f5;
      display: flex;
      align-items: center;
      justify-content: center;
      color: #555;
      transition: all 0.3s ease;
    }
    
    .socials a:hover {
      background: #f3a42e;
      color: white;
      transform: translateY(-3px);
    }
    
    .contact-form form {
      display: flex;
      flex-direction: column;
      gap: 1.2rem;
    }
    
    .contact-form input,
    .contact-form textarea {
      padding: 0.8rem 1rem;
      border: 1px solid #ddd;
      border-radius: 8px;
      font-size: 1rem;
      transition: all 0.3s ease;
    }
    
    .contact-form input:focus,
    .contact-form textarea:focus {
      border-color: #f3a42e;
      box-shadow: 0 0 0 3px rgba(243, 164, 46, 0.2);
      outline: none;
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
      transition: all 0.3s ease;
      position: relative;
      overflow: hidden;
    }
    
    .contact-form button::after {
      content: '';
      position: absolute;
      top: 0;
      left: -100%;
      width: 100%;
      height: 100%;
      background: linear-gradient(90deg, transparent, rgba(255,255,255,0.3), transparent);
      transition: all 0.5s ease;
    }
    
    .contact-form button:hover {
      background: #d48826;
      transform: translateY(-2px);
      box-shadow: 0 4px 12px rgba(243, 164, 46, 0.3);
    }
    
    .contact-form button:hover::after {
      left: 100%;
    }
    
    .contact-form small {
      font-size: 0.75rem;
      color: #666;
    }
    
    .map-container {
      max-width: 1200px;
      margin: 0 auto;
      padding: 0 2rem 4rem;
      border-radius: 12px;
      overflow: hidden;
      transform: translateY(30px);
      opacity: 0;
      animation: fadeUp 0.6s ease 0.4s forwards;
    }
    
    .map-container iframe {
      width: 100%;
      height: 400px;
      border: none;
      display: block;
      border-radius: 8px;
      box-shadow: 0 4px 15px rgba(0,0,0,0.1);
      transition: all 0.3s ease;
    }
    
    .map-container iframe:hover {
      box-shadow: 0 8px 25px rgba(0,0,0,0.15);
    }

    /* ========== FOOTER ========== */
    footer {
      background: #1E2A38;
      color: white;
      text-align: center;
      padding: 3rem 1rem;
      margin-top: 4rem;
      position: relative;
    }
    
    footer::before {
      content: '';
      position: absolute;
      top: 0;
      left: 0;
      right: 0;
      height: 4px;
      background: linear-gradient(to right, #15d455, #f3a42e);
    }
    
    footer p {
      margin-bottom: 1rem;
    }
    
    .footer-links {
      display: flex;
      justify-content: center;
      gap: 1.5rem;
      margin-bottom: 1.5rem;
      flex-wrap: wrap;
    }
    
    .footer-links a {
      color: white;
      text-decoration: none;
      transition: color 0.3s ease;
    }
    
    .footer-links a:hover {
      color: #f3a42e;
    }
    
    /* ========== SCROLL TO TOP ========== */
    .scroll-top {
      position: fixed;
      bottom: 30px;
      right: 30px;
      width: 50px;
      height: 50px;
      border-radius: 50%;
      background: #f3a42e;
      color: white;
      display: flex;
      align-items: center;
      justify-content: center;
      cursor: pointer;
      opacity: 0;
      visibility: hidden;
      transition: all 0.3s ease;
      z-index: 999;
      box-shadow: 0 4px 15px rgba(0,0,0,0.2);
    }
    
    .scroll-top.active {
      opacity: 1;
      visibility: visible;
    }
    
    .scroll-top:hover {
      background: #d48826;
      transform: translateY(-3px);
    }

    /* ========== MOBILE RESPONSIVE ========== */
    @media (max-width: 768px) {
      nav {
        padding: 1rem;
      }
      
      .hero-content {
        padding: 1rem;
      }
      
      .hero-content h1 {
        font-size: 2.2rem;
      }
      
      .services, .guides-grid {
        grid-template-columns: 1fr;
        padding: 1rem;
      }
      
      .contact-wrapper {
        padding: 2rem 1rem;
      }
      
      .map-container {
        padding: 0 1rem 2rem;
      }
      
      .footer-links {
        flex-direction: column;
        gap: 0.5rem;
      }
    }
  </style>
</head>
<body>
    <?php
    session_start();
    require_once '../database.php';
    ?>
    
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
    
    <section id="home" class="hero">
      <div class="hero-content">
        <h1>Discover Your Next Adventure</h1>
        <p>Find experienced guides and breathtaking trekking routes across the Himalayas.</p>
        <button onclick="location.href='trek.html'">Explore Treks <i class="fas fa-arrow-right"></i></button>
      </div> 
    </section>

    <!--------------------------------Services------------------------------------------------>
  
    <h2 id="services-heading" class="section-title">Our Services</h2>
    
    <section class="services" id="services">
      <div class="service-card" onclick="location.href='trek.php'">
        <img src="uploads/trekking.jpg" alt="Trekking">
        <h3>Trekking</h3>
      </div>
      
      <div class="service-card">
        <img src="uploads/hiking.jpeg" alt="Hiking">
        <h3>Hiking</h3>
      </div>
      
      <div class="service-card">
        <img src="uploads/mountaineering.jpg" alt="Mountaineering">
        <h3>Mountaineering</h3>
      </div>
    </section>



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
        <i class="fab fa-facebook-f icon"></i>
      </a>
      <a href="https://www.instagram.com/pem_ba7/" target="_blank">
        <i class="fab fa-instagram icon"></i>
      </a>
      <a href="#" target="_blank">
        <i class="fab fa-whatsapp icon"></i>
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
        <i class="fab fa-facebook-f icon"></i>
      </a>
      <a href="https://www.instagram.com/pem_ba7/" target="_blank">
        <i class="fab fa-instagram icon"></i>
      </a>
      <a href="#" target="_blank">
        <i class="fab fa-whatsapp icon"></i>
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
        <i class="fab fa-facebook-f icon"></i>
      </a>
      <a href="https://www.instagram.com/pem_ba7/" target="_blank">
        <i class="fab fa-instagram icon"></i>
      </a>
      <a href="#" target="_blank">
        <i class="fab fa-whatsapp icon"></i>
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
      <li><i class="fas fa-map-marker-alt"></i> <strong>Address:</strong> Kathmandu, Nepal</li>
      <li><i class="fas fa-phone-alt"></i> <strong>Phone:</strong> +977-9818560121</li>
      <li><i class="fas fa-envelope"></i> <strong>Email:</strong> contact@guideme.com</li>
      <li><i class="fas fa-clock"></i> <strong>Hours:</strong> Mon-Sat: 9AM - 6PM</li>
    </ul>
    <div class="socials">
      <span>Follow Us:</span>
      <a href="https://www.facebook.com/pembadorjee.sherpa.9256/" target="_blank">
        <i class="fab fa-facebook-f"></i>
      </a>
      <a href="https://www.instagram.com/pem_ba7/" target="_blank">
        <i class="fab fa-instagram"></i>
      </a>
      <a href="https://twitter.com" target="_blank">
        <i class="fab fa-twitter"></i>
      </a>
      <a href="https://www.linkedin.com" target="_blank">
        <i class="fab fa-linkedin-in"></i>
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
      <button type="submit">Send Message <i class="fas fa-paper-plane"></i></button>
    </form>
  </div>
</section>
<div class="map-container">
  <iframe src="https://www.google.com/maps?q=Kathmandu%2C%20Nepal&z=14&output=embed" frameborder="0" allowfullscreen></iframe>
</div>

<footer id="contact">
  <p>&copy; 2025 Guide Me | Explore the Himalayas with trusted local guides.</p>
  <div class="footer-links">
    <a href="#">Privacy Policy</a>
    <a href="#">Terms of Service</a>
    <a href="#">FAQ</a>
    <a href="#">About Us</a>
  </div>
  <p>Made with <i class="fas fa-heart" style="color: #ff4d4d;"></i> in Nepal</p>
</footer>

<a href="#" class="scroll-top" id="scrollTop">
  <i class="fas fa-arrow-up"></i>
</a>

<script>
  document.addEventListener('DOMContentLoaded', function() {
    // Mobile menu toggle
    const mobileMenu = document.getElementById('mobileMenu');
    const navLinks = document.getElementById('navLinks');
    
    mobileMenu.addEventListener('click', () => {
      navLinks.classList.toggle('active');
      mobileMenu.innerHTML = navLinks.classList.contains('active') ? 
        '<i class="fas fa-times"></i>' : '<i class="fas fa-bars"></i>';
    });
    
    // Close mobile menu when clicking on a link
    document.querySelectorAll('.nav-links a').forEach(link => {
      link.addEventListener('click', () => {
        navLinks.classList.remove('active');
        mobileMenu.innerHTML = '<i class="fas fa-bars"></i>';
      });
    });

    // Navigation scroll effect
    const nav = document.getElementById('mainNav');
    window.addEventListener('scroll', () => {
      if (window.scrollY > 50) {
        nav.classList.add('scrolled');
      } else {
        nav.classList.remove('scrolled');
      }
    });

    // Navigation active state
    const navLinkElements = document.querySelectorAll('.nav-link');
    window.addEventListener('scroll', () => {
      const fromTop = window.scrollY + 100;
      navLinkElements.forEach(link => {
        const section = document.querySelector(link.getAttribute('href'));
        if (
          section.offsetTop <= fromTop &&
          section.offsetTop + section.offsetHeight > fromTop
        ) {
          navLinkElements.forEach(link => link.classList.remove('active'));
          link.classList.add('active');
        }
      });
    });

    // Scroll to top button
    const scrollTop = document.getElementById('scrollTop');
    window.addEventListener('scroll', () => {
      if (window.scrollY > 300) {
        scrollTop.classList.add('active');
      } else {
        scrollTop.classList.remove('active');
      }
    });
    
    scrollTop.addEventListener('click', (e) => {
      e.preventDefault();
      window.scrollTo({
        top: 0,
        behavior: 'smooth'
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
      if(data.avatar) {
        // Update avatar in the navbar
        const avatarPath = '../' + data.avatar;
        document.getElementById('currentAvatar').src = avatarPath;
        
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



    // Animate elements when they come into view
    const animateOnScroll = () => {
      const elements = document.querySelectorAll('.service-card, .guide-card-v2, .contact-info, .contact-form, .map-container');
      
      elements.forEach(element => {
        const elementPosition = element.getBoundingClientRect().top;
        const windowHeight = window.innerHeight;
        
        if (elementPosition < windowHeight - 100) {
          element.style.animationPlayState = 'running';
        }
      });
    };
    
    // Run once on load
    animateOnScroll();
    
    // Run on scroll
    window.addEventListener('scroll', animateOnScroll);
  });
</script>
</body>
</html>