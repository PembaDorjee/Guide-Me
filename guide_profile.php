<?php
session_start();
require_once '../database.php';

// Check if guide_id is provided in URL
$guide_id = isset($_GET['id']) ? intval($_GET['id']) : 0;

// If no guide_id provided and user is logged in as guide, use their ID
if ($guide_id === 0 && isset($_SESSION['guide_id'])) {
    $guide_id = $_SESSION['guide_id'];
}

// If still no guide_id, redirect to guides listing
if ($guide_id === 0) {
    header("Location: guides.php");
    exit();
}

// Fetch guide data
$stmt = $conn->prepare("SELECT * FROM Guide WHERE guide_id = ?");
$stmt->bind_param("i", $guide_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    // Guide not found
    header("Location: guides.php?error=guide_not_found");
    exit();
}

$guide = $result->fetch_assoc();
$stmt->close();

// Fetch guide certifications
$cert_stmt = $conn->prepare("SELECT * FROM GuideCertification WHERE guide_id = ?");
$cert_stmt->bind_param("i", $guide_id);
$cert_stmt->execute();
$certifications = $cert_stmt->get_result()->fetch_all(MYSQLI_ASSOC);
$cert_stmt->close();

// Fetch guide reviews
$review_stmt = $conn->prepare("
    SELECT r.*, c.full_name as client_name 
    FROM GuideReview r 
    JOIN Client c ON r.client_id = c.client_id 
    WHERE r.guide_id = ? 
    ORDER BY r.review_date DESC 
    LIMIT 5
");
$review_stmt->bind_param("i", $guide_id);
$review_stmt->execute();
$reviews = $review_stmt->get_result()->fetch_all(MYSQLI_ASSOC);
$review_stmt->close();

// Calculate average rating
$avg_rating = 0;
$review_count = count($reviews);
if ($review_count > 0) {
    $total_rating = 0;
    foreach ($reviews as $review) {
        $total_rating += $review['rating'];
    }
    $avg_rating = round($total_rating / $review_count, 1);
}

// Fetch guide availability/bookings
$booking_stmt = $conn->prepare("
    SELECT start_date, end_date 
    FROM Booking 
    WHERE guide_id = ? AND status IN ('confirmed', 'completed')
");
$booking_stmt->bind_param("i", $guide_id);
$booking_stmt->execute();
$bookings = $booking_stmt->get_result()->fetch_all(MYSQLI_ASSOC);
$booking_stmt->close();

// Process bookings into array of booked dates for calendar
$booked_dates = [];
foreach ($bookings as $booking) {
    $start = new DateTime($booking['start_date']);
    $end = new DateTime($booking['end_date']);
    $interval = new DateInterval('P1D');
    $date_range = new DatePeriod($start, $interval, $end->modify('+1 day'));
    
    foreach ($date_range as $date) {
        $booked_dates[] = $date->format('Y-m-d');
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($guide['full_name']); ?> | Guide Me</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&family=Montserrat:wght@400;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
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
        
        .profile-container {
            max-width: 1300px;
            margin: 50px auto;
            padding: 0 25px;
            perspective: 1000px;
        }
        
        .profile-card {
            background: white;
            border-radius: 20px;
            box-shadow: var(--card-shadow);
            overflow: hidden;
            margin-bottom: 60px;
            transform-style: preserve-3d;
            transition: all 0.5s cubic-bezier(0.175, 0.885, 0.32, 1.1);
        }
        
        .profile-card:hover {
            transform: translateY(-10px);
            box-shadow: 0 30px 70px -15px rgba(0, 0, 0, 0.2);
        }
        
        .profile-header {
            background: var(--primary-dark);
            color: white;
            padding: 60px 40px;
            text-align: center;
            position: relative;
            overflow: hidden;
            clip-path: polygon(0 0, 100% 0, 100% 90%, 0 100%);
        }
        
        .profile-avatar {
            width: 160px;
            height: 160px;
            border-radius: 50%;
            border: 5px solid rgba(255,255,255,0.3);
            object-fit: cover;
            margin-bottom: 25px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.2);
            animation: float 6s ease-in-out infinite;
        }
        
        @keyframes float {
            0% { transform: translateY(0px); }
            50% { transform: translateY(-15px); }
            100% { transform: translateY(0px); }
        }
        
        .profile-name {
            font-family: 'Montserrat', sans-serif;
            font-size: 2.5rem;
            font-weight: 700;
            margin-bottom: 5px;
            text-shadow: 0 2px 5px rgba(0,0,0,0.1);
            position: relative;
            display: inline-block;
        }
        
        .profile-name::after {
            content: "";
            position: absolute;
            bottom: -10px;
            left: 50%;
            transform: translateX(-50%);
            width: 60px;
            height: 3px;
            background: var(--accent-color);
            border-radius: 3px;
        }
        
        .profile-title {
            font-size: 1.3rem;
            opacity: 0.9;
            margin-bottom: 25px;
            font-weight: 300;
        }
        
        .profile-social {
            display: flex;
            justify-content: center;
            gap: 20px;
            margin-bottom: 30px;
        }
        
        .profile-social a {
            color: white;
            font-size: 1.4rem;
            transition: all 0.3s ease;
            width: 45px;
            height: 45px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            background: rgba(255,255,255,0.1);
        }
        
        .profile-social a:hover {
            transform: translateY(-5px) scale(1.1);
            background: var(--accent-color);
            box-shadow: 0 5px 15px rgba(243, 164, 46, 0.3);
        }
        
        .profile-badge {
            display: inline-block;
            background: rgba(255,255,255,0.15);
            padding: 8px 20px;
            border-radius: 50px;
            font-size: 0.95rem;
            margin: 0 8px 12px 0;
            border: 1px solid rgba(255,255,255,0.2);
            transition: all 0.3s ease;
        }
        
        .profile-badge:hover {
            background: var(--accent-color);
            transform: translateY(-2px);
        }
        
        .profile-badge i {
            margin-right: 8px;
        }
        
        .profile-body {
            padding: 50px;
            display: flex;
            flex-wrap: wrap;
        }
        
        .profile-main {
            flex: 1;
            min-width: 300px;
            padding-right: 40px;
        }
        
        .profile-sidebar {
            width: 380px;
            border-left: 1px solid rgba(0,0,0,0.08);
            padding-left: 40px;
        }
        
        .section-title {
            color: var(--primary-dark);
            font-family: 'Montserrat', sans-serif;
            font-size: 1.5rem;
            font-weight: 700;
            margin-bottom: 25px;
            display: flex;
            align-items: center;
            position: relative;
        }
        
        .section-title::after {
            content: "";
            position: absolute;
            bottom: -8px;
            left: 0;
            width: 50px;
            height: 3px;
            background: var(--accent-color);
            border-radius: 3px;
        }
        
        .section-title i {
            margin-right: 15px;
            font-size: 1.4em;
            color: var(--accent-color);
        }
        
        .profile-bio {
            margin-bottom: 40px;
            line-height: 1.8;
            font-size: 1.05rem;
            color: var(--text-dark);
        }
        
        .info-group {
            margin-bottom: 30px;
        }
        
        .info-label {
            font-weight: 600;
            color: var(--primary-dark);
            margin-bottom: 10px;
            display: flex;
            align-items: center;
            font-size: 1.05rem;
        }
        
        .info-label i {
            margin-right: 12px;
            font-size: 1.1em;
            color: var(--accent-color);
            width: 30px;
            height: 30px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            background: rgba(243, 164, 46, 0.1);
        }
        
        .info-value {
            padding-left: 42px;
            color: var(--text-dark);
            font-size: 1rem;
        }
        
        .language-list {
            display: flex;
            flex-wrap: wrap;
            gap: 12px;
        }
        
        .language-item {
            background: rgba(243, 164, 46, 0.1);
            color: var(--primary-dark);
            padding: 8px 18px;
            border-radius: 50px;
            font-size: 0.95rem;
            font-weight: 500;
            transition: all 0.3s ease;
            display: flex;
            align-items: center;
        }
        
        .language-item:hover {
            background: rgba(243, 164, 46, 0.2);
            transform: translateY(-3px);
        }
        
        .language-item i {
            margin-right: 8px;
            color: var(--accent-color);
        }
        
        .document-card {
            background: white;
            border-radius: 12px;
            padding: 20px;
            margin-bottom: 20px;
            display: flex;
            align-items: center;
            box-shadow: 0 5px 15px rgba(0,0,0,0.05);
            transition: all 0.3s ease;
            border: 1px solid rgba(0,0,0,0.05);
        }
        
        .document-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 25px rgba(0,0,0,0.1);
            border-color: rgba(243, 164, 46, 0.2);
        }
        
        .document-icon {
            font-size: 2rem;
            color: white;
            margin-right: 20px;
            width: 60px;
            height: 60px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            background: var(--primary-dark);
            flex-shrink: 0;
        }
        
        .document-info h5 {
            font-size: 1.1rem;
            margin-bottom: 5px;
            color: var(--text-dark);
        }
        
        .document-info p {
            font-size: 0.85rem;
            color: var(--text-light);
            margin-bottom: 0;
        }
        
        .btn-contact {
            background: var(--accent-color);
            color: white;
            border: none;
            padding: 15px 35px;
            border-radius: 50px;
            font-weight: 600;
            display: inline-flex;
            align-items: center;
            transition: all 0.3s ease;
            margin-top: 30px;
            box-shadow: 0 4px 15px rgba(243, 164, 46, 0.3);
            font-size: 1.05rem;
        }
        
        .btn-contact:hover {
            background: #e69527;
            transform: translateY(-3px);
            box-shadow: 0 6px 20px rgba(243, 164, 46, 0.4);
        }
        
        .btn-contact:active {
            transform: translateY(0);
        }
        
        .btn-contact i {
            margin-right: 10px;
            font-size: 1.2rem;
            transition: transform 0.3s ease;
        }
        
        .btn-contact:hover i {
            transform: translateX(3px);
        }
        
        .rating {
            color: #ffc107;
            font-size: 1.3rem;
            margin-bottom: 20px;
        }
        
        .review-card {
            background: white;
            border-radius: 15px;
            padding: 25px;
            margin-bottom: 25px;
            box-shadow: 0 10px 20px rgba(0,0,0,0.05);
            transition: all 0.3s ease;
            border: 1px solid rgba(0,0,0,0.05);
        }
        
        .review-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 15px 30px rgba(0,0,0,0.1);
        }
        
        .review-header {
            display: flex;
            align-items: center;
            margin-bottom: 15px;
        }
        
        .review-avatar {
            width: 55px;
            height: 55px;
            border-radius: 50%;
            object-fit: cover;
            margin-right: 15px;
            border: 3px solid rgba(243, 164, 46, 0.1);
        }
        
        .review-name {
            font-weight: 600;
            margin-bottom: 5px;
            color: var(--text-dark);
        }
        
        .review-date {
            font-size: 0.85rem;
            color: var(--text-light);
        }
        
        .review-text {
            color: var(--text-dark);
            line-height: 1.7;
        }
        
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 15px;
            margin-bottom: 30px;
        }
        
        .stat-item {
            background: white;
            border-radius: 12px;
            padding: 20px;
            text-align: center;
            box-shadow: 0 5px 15px rgba(0,0,0,0.05);
            transition: all 0.3s ease;
            border: 1px solid rgba(0,0,0,0.05);
        }
        
        .stat-item:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 25px rgba(0,0,0,0.1);
        }
        
        .stat-number {
            font-size: 2rem;
            font-weight: 700;
            color: var(--primary-dark);
            margin-bottom: 5px;
            font-family: 'Montserrat', sans-serif;
        }
        
        .stat-label {
            font-size: 0.9rem;
            color: var(--text-light);
        }
        
        .stat-icon {
            font-size: 1.8rem;
            margin-bottom: 15px;
            color: var(--accent-color);
        }
        
        </style>
        </head>
        <body>
        <div class="profile-container">
        <div class="profile-card">
        <div class="profile-header">
        <img src="<?php echo !empty($guide['profile_image']) ? htmlspecialchars($guide['profile_image']) : 'https://randomuser.me/api/portraits/men/32.jpg'; ?>" alt="Guide Avatar" class="profile-avatar">
        <h1 class="profile-name"><?php echo htmlspecialchars($guide['full_name']); ?></h1>
        <p class="profile-title"><?php echo htmlspecialchars($guide['specialization']); ?></p>
        
        <div class="profile-social">
        <?php if (!empty($guide['facebook'])): ?>
        <a href="<?php echo htmlspecialchars($guide['facebook']); ?>"><i class="fab fa-facebook-f"></i></a>
        <?php endif; ?>
        <?php if (!empty($guide['instagram'])): ?>
        <a href="<?php echo htmlspecialchars($guide['instagram']); ?>"><i class="fab fa-instagram"></i></a>
        <?php endif; ?>
        <?php if (!empty($guide['linkedin'])): ?>
        <a href="<?php echo htmlspecialchars($guide['linkedin']); ?>"><i class="fab fa-linkedin-in"></i></a>
        <?php endif; ?>
        <?php if (!empty($guide['whatsapp'])): ?>
        <a href="https://wa.me/<?php echo htmlspecialchars(preg_replace('/[^0-9]/', '', $guide['phone'])); ?>"><i class="fab fa-whatsapp"></i></a>
        <?php endif; ?>
        </div>
        
        <div>
        <?php 
        // Display badges if available
        $badges = !empty($guide['badges']) ? explode(',', $guide['badges']) : [];
        foreach ($badges as $badge): 
        $badge = trim($badge);
        if (!empty($badge)):
        ?>
        <span class="profile-badge"><i class="fas fa-mountain"></i> <?php echo htmlspecialchars($badge); ?></span>
        <?php 
        endif;
        endforeach; 
        ?>
        </div>
        </div>
        
        <div class="profile-body">
        <div class="profile-main">
        <div class="profile-bio">
        <?php echo nl2br(htmlspecialchars($guide['bio'])); ?>
        </div>
        
        <div class="stats-grid">
        <div class="stat-item">
        <div class="stat-icon"><i class="fas fa-mountain"></i></div>
        <div class="stat-number"><?php echo htmlspecialchars($guide['experience_years'] ?? '0'); ?></div>
        <div class="stat-label">Years Experience</div>
        </div>
        <div class="stat-item">
        <div class="stat-icon"><i class="fas fa-globe-asia"></i></div>
        <div class="stat-number"><?php echo htmlspecialchars($guide['expeditions_count'] ?? '0'); ?></div>
        <div class="stat-label">Expeditions</div>
        </div>
        <div class="stat-item">
        <div class="stat-icon"><i class="fas fa-users"></i></div>
        <div class="stat-number"><?php echo htmlspecialchars($guide['clients_count'] ?? '0'); ?></div>
        <div class="stat-label">Clients Guided</div>
        </div>
        </div>
        
        <div class="info-group">
        <div class="info-label">
        <i class="fas fa-envelope"></i> Email
        </div>
        <div class="info-value"><?php echo htmlspecialchars($guide['email']); ?></div>
        </div>
        
        <div class="info-group">
        <div class="info-label">
        <i class="fas fa-phone-alt"></i> Phone
        </div>
        <div class="info-value"><?php echo htmlspecialchars($guide['phone'] ?? 'Not provided'); ?></div>
        </div>
        
        <button class="btn-contact">
        <i class="fas fa-paper-plane"></i> Contact for Expedition
        </button>
        </div>
        
        <div class="profile-sidebar">
        <!-- Booking Calendar Section -->
        <div class="booking-calendar">
        <div class="section-title">
        <i class="fas fa-calendar-check"></i> Availability
        </div>
        <div class="calendar-header">
        <input type="text" id="booking-date" class="form-control" placeholder="Select dates..." readonly>
        </div>
        <div id="calendar"></div>
        <div class="booking-legend">
        <div class="legend-item">
        <span class="legend-color legend-available"></span>
        <span>Available</span>
        </div>
        <div class="legend-item">
        <span class="legend-color legend-booked"></span>
        <span>Booked</span>
        </div>
        <div class="legend-item">
        <span class="legend-color legend-selected"></span>
        <span>Selected</span>
        </div>
        </div>
        <button class="btn-book">
        <i class="fas fa-calendar-plus"></i> Book Expedition
        </button>
        </div>
        
        <div class="section-title">
        <i class="fas fa-certificate"></i> Certifications
        </div>
        
        <?php foreach ($certifications as $cert): ?>
        <div class="document-card">
        <div class="document-icon">
        <i class="fas fa-certificate"></i>
        </div>
        <div class="document-info">
        <h5><?php echo htmlspecialchars($cert['certification_name']); ?></h5>
        <p><?php echo htmlspecialchars($cert['issuing_organization']); ?> • <?php echo htmlspecialchars($cert['certification_level']); ?></p>
        </div>
        </div>
        <?php endforeach; ?>
        
        <div class="section-title">
        <i class="fas fa-star"></i> Reviews
        </div>
        
        <div class="rating">
        <?php 
        // Display stars based on average rating
        for ($i = 1; $i <= 5; $i++) {
            if ($i <= floor($avg_rating)) {
                echo '<i class="fas fa-star"></i>';
            } elseif ($i - 0.5 <= $avg_rating) {
                echo '<i class="fas fa-star-half-alt"></i>';
            } else {
                echo '<i class="far fa-star"></i>';
            }
        }
        ?>
        <span style="color: var(--text-dark); font-size: 1.1rem; margin-left: 8px;"><?php echo $avg_rating; ?> (<?php echo $review_count; ?> reviews)</span>
        </div>
        
        <?php foreach ($reviews as $review): ?>
        <div class="review-card">
        <div class="review-header">
        <img src="https://randomuser.me/api/portraits/men/<?php echo rand(1, 99); ?>.jpg" alt="Reviewer" class="review-avatar">
        <div>
        <div class="review-name"><?php echo htmlspecialchars($review['client_name']); ?></div>
        <div class="review-date"><?php echo date('F j, Y', strtotime($review['review_date'])); ?> • <?php echo htmlspecialchars($review['expedition_name']); ?></div>
        </div>
        </div>
        <div class="rating">
        <?php 
        // Display stars based on review rating
        for ($i = 1; $i <= 5; $i++) {
            if ($i <= $review['rating']) {
                echo '<i class="fas fa-star"></i>';
            } else {
                echo '<i class="far fa-star"></i>';
            }
        }
        ?>
        </div>
        <p class="review-text"><?php echo htmlspecialchars($review['review_text']); ?></p>
        </div>
        <?php endforeach; ?>
        </div>
        </div>
        </div>
        
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
        <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
        <script>
        document.addEventListener('DOMContentLoaded', function() {
        // Floating animation for profile avatar
        const avatar = document.querySelector('.profile-avatar');
        if (avatar) {
        setInterval(() => {
        avatar.style.animation = 'float 6s ease-in-out infinite';
        }, 100);
        }
        
        // Initialize booking calendar with dates from database
        const bookedDates = <?php echo json_encode($booked_dates); ?>;
        
        const calendar = flatpickr("#booking-date", {
        inline: true,
        mode: "range",
        minDate: "today",
        disable: [
        function(date) {
        // Disable booked dates
        return bookedDates.includes(dateStr(date));
        }
        ],
        onDayCreate: function(dObj, dStr, fp, dayElem) {
        // Mark booked dates
        if (bookedDates.includes(dateStr(dayElem.dateObj))) {
        dayElem.classList.add("booked");
        }
        },
        onChange: function(selectedDates, dateStr, instance) {
        // Handle date selection
        console.log("Selected dates:", selectedDates.map(dateStr));
        }
        });
        
        // Helper function to format date as YYYY-MM-DD
        function dateStr(date) {
        return date.toISOString().split('T')[0];
        }
        
        // Book button click handler
        document.querySelector('.btn-book').addEventListener('click', function() {
        const selectedDates = calendar.selectedDates;
        if (selectedDates.length === 2) {
        const start = selectedDates[0].toLocaleDateString();
        const end = selectedDates[1].toLocaleDateString();
        
        // Redirect to booking page with guide ID and dates
        window.location.href = `book_guide.php?guide_id=<?php echo $guide_id; ?>&start=${dateStr(selectedDates[0])}&end=${dateStr(selectedDates[1])}`;
        } else {
        alert("Please select a date range for your expedition.");
        }
        });
        });
        </script>
        </body>
        </html>

.booking-calendar {
    background: white;
    border-radius: 15px;
    padding: 25px;
    margin-bottom: 30px;
    box-shadow: 0 10px 20px rgba(0,0,0,0.05);
    border: 1px solid rgba(0,0,0,0.05);
}

.calendar-header {
    margin-bottom: 15px;
}

.flatpickr-calendar {
    width: 100% !important;
    box-shadow: none !important;
    border: 1px solid rgba(0,0,0,0.1);
    border-radius: 10px;
    padding: 10px;
}

.booking-legend {
    display: flex;
    justify-content: space-between;
    margin: 15px 0;
}

.legend-item {
    display: flex;
    align-items: center;
    font-size: 0.85rem;
}

.legend-color {
    width: 15px;
    height: 15px;
    border-radius: 50%;
    margin-right: 8px;
}

.legend-available {
    background-color: #ffffff;
    border: 1px solid #e0e0e0;
}

.legend-booked {
    background-color: #ffcdd2;
    border: 1px solid #ef9a9a;
}

.legend-selected {
    background-color: #c8e6c9;
    border: 1px solid #81c784;
}

.btn-book {
    background: var(--accent-color);
    color: white;
    border: none;
    padding: 12px 25px;
    border-radius: 50px;
    font-weight: 600;
    display: flex;
    align-items: center;
    justify-content: center;
    width: 100%;
    transition: all 0.3s ease;
    box-shadow: 0 4px 15px rgba(243, 164, 46, 0.3);
    font-size: 1rem;
    margin-top: 15px;
}

.btn-book:hover {
    background: #e69527;
    transform: translateY(-3px);
    box-shadow: 0 6px 20px rgba(243, 164, 46, 0.4);
}

.btn-book i {
    margin-right: 10px;
}

/* Style for booked dates */
.flatpickr-day.booked {
    background-color: #ffcdd2 !important;
    border-color: #ef9a9a !important;
    color: #b71c1c !important;
}

.flatpickr-day.selected {
    background-color: #c8e6c9 !important;
    border-color: #81c784 !important;
    color: #2e7d32 !important;
}