<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Guide Me - Adventure Awaits</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: Arial, sans-serif;
        }
        body {
            background: #f4f4f4;
        }
        nav {
            background: #333;
            color: white;
            padding: 1rem 2rem;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .nav-links {
            list-style: none;
            display: flex;
            gap: 1.5rem;
        }
        .nav-links a {
            color: white;
            text-decoration: none;
            font-size: 1rem;
        }
        .hero {
            background: url('frontend/home.jpg') no-repeat center center/cover;
            height: 60vh;
            display: flex;
            align-items: center;
            justify-content: center;
            text-align: center;
            color: white;
            position: relative;
        }
        .hero::after {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.5);
        }
        .hero-content {
            position: relative;
            z-index: 2;
        }
        .hero h1 {
            font-size: 3rem;
            font-weight: bold;
        }
        .hero p {
            font-size: 1.2rem;
            margin: 10px 0;
        }
        .search-bar {
            margin-top: 1rem;
            display: flex;
            justify-content: center;
            gap: 0.5rem;
        }
        .search-bar input {
            padding: 0.8rem;
            width: 50%;
            border-radius: 5px;
            border: none;
            font-size: 1rem;
        }
        .search-bar button {
            padding: 0.8rem;
            background: #f90;
            border: none;
            color: white;
            cursor: pointer;
            font-size: 1.1rem;
            border-radius: 5px;
            transition: background-color 0.3s ease;
        }
        .search-bar button:hover {
            background-color: #e68200;
        }
        .services {
            text-align: center;
            padding: 4rem 2rem;
        }
        .service-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 1rem;
            padding: 2rem;
        }
        .service-card {
            background: white;
            padding: 1.5rem;
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            text-align: center;
        }
        .service-card img {
            width: 100%;
            border-radius: 10px;
        }
        .service-card h3 {
            margin: 10px 0;
        }
        .about {
            padding: 4rem 2rem;
            text-align: center;
            background: white;
        }
    </style>
</head>
<body>
    <nav>
        <div class="logo">Guide Me</div>
        <ul class="nav-links">
            <li><a href="#">Home</a></li>
            <li><a href="#">Services</a></li>
            <li><a href="#">About</a></li>
            <li><a href="#">Contact</a></li>
        </ul>
    </nav>
    <section class="hero">
        <div class="hero-content">
            <h1>Adventure Awaits</h1>
            <p>Find the best trekking, hiking, and mountaineering experiences in Nepal.</p>
            <div class="search-bar">
                <input type="text" placeholder="Search for treks, guides, or agencies">
                <button>Search</button>
            </div>
        </div>
    </section>
    <section class="services">
        <h2>Our Services</h2>
        <div class="service-grid">
            <?php include 'fetch_services.php'; ?>
        </div>
    </section>
    <section class="about">
        <h2>About Us</h2>
        <p>We provide expert guides and customized adventure experiences to make your journey in Nepal unforgettable.</p>
    </section>
</body>
</html>
