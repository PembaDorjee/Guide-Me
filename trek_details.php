<?php
session_start();
require_once '../database.php';

error_reporting(E_ALL);
ini_set('display_errors', 1);

if (!isset($_GET['id'])) {
    header('Location: trek.php');
    exit();
}

$trek_id = $_GET['id'];

try {
    // Modified query to use trek_id
    $sql = "SELECT * FROM treks WHERE trek_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $trek_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $trek = $result->fetch_assoc();

    if (!$trek) {
        throw new Exception("Trek not found");
    }
} catch (Exception $e) {
    error_log("Error: " . $e->getMessage());
    die("Error loading trek details. Please try again later.");
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($trek['trek_name']); ?> - Details</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="css/trek_details.css">
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;500;600;700&display=swap" rel="stylesheet">
</head>
<body>
    <div class="trek-details-container">
        <div class="trek-header">
            <img src="<?php echo htmlspecialchars('../' . $trek['featured_image']); ?>" 
                 alt="<?php echo htmlspecialchars($trek['trek_name']); ?>"
                 class="featured-image">
            <div class="trek-title">
                <h1><?php echo htmlspecialchars($trek['trek_name']); ?></h1>
                <div class="trek-meta">
                    <span><i class="fas fa-map-marker-alt"></i> <?php echo htmlspecialchars($trek['region']); ?></span>
                    <span><i class="fas fa-hiking"></i> <?php echo htmlspecialchars($trek['duration']); ?> days</span>
                    <span><i class="fas fa-mountain"></i> <?php echo htmlspecialchars($trek['difficulty']); ?></span>
                    <span><i class="fas fa-arrow-up"></i> <?php echo htmlspecialchars($trek['max_altitude']); ?>m</span>
                    <span><i class="fas fa-tag"></i> NPR <?php echo number_format($trek['price']); ?></span>
                    <span><i class="fas fa-calendar-alt"></i> Best Season: <?php echo htmlspecialchars($trek['best_season'] ?? 'Year-round'); ?></span>
                </div>
            </div>
        </div>

        <div class="trek-content">
            <section class="description">
                <h2><i class="fas fa-info-circle"></i> Description</h2>
                <p><?php echo nl2br(htmlspecialchars($trek['description'])); ?></p>
            </section>

            <?php if (!empty($trek['gallery_images'])): ?>
            <section class="gallery">
                <h2><i class="fas fa-images"></i> Gallery</h2>
                
                <!-- Grid gallery for desktop -->
                <div class="gallery-grid">
                    <?php 
                    $gallery = json_decode($trek['gallery_images'], true);
                    if (is_array($gallery)) {
                        foreach ($gallery as $image) {
                            $image_path = '../' . trim($image, '/');
                            echo '<div class="gallery-item">';
                            echo '<img src="' . htmlspecialchars($image_path) . '" alt="Gallery Image">';
                            echo '</div>';
                        }
                    }
                    ?>
                </div>
                
                <!-- Slideshow for mobile -->
                <div class="slideshow-container">
                    <?php 
                    if (is_array($gallery)) {
                        $slideIndex = 1;
                        foreach ($gallery as $image) {
                            $image_path = '../' . trim($image, '/');
                            echo '<div class="slide">';
                            echo '<img src="' . htmlspecialchars($image_path) . '" alt="Gallery Image">';
                            echo '</div>';
                            $slideIndex++;
                        }
                    }
                    ?>
                    <a class="prev" id="prevSlide">&#10094;</a>
                    <a class="next" id="nextSlide">&#10095;</a>
                </div>
                
                <div class="dot-container">
                    <?php 
                    if (is_array($gallery)) {
                        for ($i = 1; $i <= count($gallery); $i++) {
                            echo '<span class="dot" data-slide="' . $i . '"></span>';
                        }
                    }
                    ?>
                </div>
            </section>
            <?php endif; ?>

            <section class="highlights">
                <h2><i class="fas fa-star"></i> Highlights</h2>
                <div class="highlights-content">
                    <?php echo nl2br(htmlspecialchars($trek['highlights'])); ?>
                </div>
            </section>

            <section class="itinerary">
                <h2><i class="fas fa-route"></i> Itinerary</h2>
                <div class="itinerary-content">
                    <?php echo nl2br(htmlspecialchars($trek['itinerary'])); ?>
                </div>
            </section>

            <div class="trek-info-grid">
                <section class="included">
                    <h2><i class="fas fa-check-circle"></i> What's Included</h2>
                    <div class="included-content">
                        <?php echo nl2br(htmlspecialchars($trek['included'])); ?>
                    </div>
                </section>

                <section class="not-included">
                    <h2><i class="fas fa-times-circle"></i> Not Included</h2>
                    <div class="not-included-content">
                        <?php echo nl2br(htmlspecialchars($trek['not_included'])); ?>
                    </div>
                </section>
            </div>
        </div>

        <div class="booking-section">
            <div class="booking-cta">
                <div class="price-display">
                    <span class="price-label">Price per person</span>
                    <span class="price-value">NPR <?php echo number_format($trek['price']); ?></span>
                </div>
                <button onclick="location.href='booking.php?trek_id=<?php echo $trek['trek_id']; ?>'" class="book-now">
                    Book This Trek <i class="fas fa-arrow-right"></i>
                </button>
            </div>
        </div>
    </div>

    <script>
    // Add lightbox functionality for gallery images
    document.addEventListener('DOMContentLoaded', function() {
        // Lightbox for desktop gallery
        const galleryItems = document.querySelectorAll('.gallery-item img');
        
        galleryItems.forEach(img => {
            img.addEventListener('click', function() {
                createLightbox(this.src);
            });
        });

        // Slideshow functionality
        let slideIndex = 1;
        const slides = document.querySelectorAll('.slide');
        const dots = document.querySelectorAll('.dot');
        const prevSlide = document.getElementById('prevSlide');
        const nextSlide = document.getElementById('nextSlide');

        // Initialize slideshow
        if (slides.length > 0) {
            showSlides(slideIndex);
            
            // Add event listeners for navigation
            if (prevSlide) {
                prevSlide.addEventListener('click', function() {
                    showSlides(slideIndex -= 1);
                });
            }
            
            if (nextSlide) {
                nextSlide.addEventListener('click', function() {
                    showSlides(slideIndex += 1);
                });
            }
            
            // Add event listeners for dots
            dots.forEach(dot => {
                dot.addEventListener('click', function() {
                    showSlides(slideIndex = parseInt(this.getAttribute('data-slide')));
                });
            });
            
            // Add lightbox for slideshow images
            const slideImages = document.querySelectorAll('.slide img');
            slideImages.forEach(img => {
                img.addEventListener('click', function() {
                    createLightbox(this.src);
                });
            });
        }

        // Function to show slides
        function showSlides(n) {
            if (!slides.length) return;
            
            if (n > slides.length) {slideIndex = 1}    
            if (n < 1) {slideIndex = slides.length}
            
            // Hide all slides
            for (let i = 0; i < slides.length; i++) {
                slides[i].style.display = "none";  
            }
            
            // Remove active class from all dots
            for (let i = 0; i < dots.length; i++) {
                dots[i].className = dots[i].className.replace(" active-dot", "");
            }
            
            // Show current slide and activate dot
            slides[slideIndex-1].style.display = "block";  
            dots[slideIndex-1].className += " active-dot";
        }

        // Function to create lightbox
        function createLightbox(imgSrc) {
            const lightbox = document.createElement('div');
            lightbox.className = 'lightbox';
            
            const lightboxImg = document.createElement('img');
            lightboxImg.src = imgSrc;
            
            const closeBtn = document.createElement('span');
            closeBtn.className = 'lightbox-close';
            closeBtn.innerHTML = '&times;';
            
            lightbox.appendChild(lightboxImg);
            lightbox.appendChild(closeBtn);
            document.body.appendChild(lightbox);
            
            closeBtn.addEventListener('click', function() {
                document.body.removeChild(lightbox);
            });
            
            lightbox.addEventListener('click', function(e) {
                if (e.target === lightbox) {
                    document.body.removeChild(lightbox);
                }
            });
        }
    });
    </script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Gallery slider functionality
            const slides = document.querySelectorAll('.slide');
            const dots = document.querySelectorAll('.dot');
            const prevBtn = document.getElementById('prevBtn');
            const nextBtn = document.getElementById('nextBtn');
            let currentIndex = 0;
            
            function showSlide(index) {
                // Hide all slides
                slides.forEach(slide => {
                    slide.classList.remove('active');
                });
                
                // Remove active class from all dots
                dots.forEach(dot => {
                    dot.classList.remove('active-dot');
                });
                
                // Show the current slide and activate dot
                slides[index].classList.add('active');
                dots[index].classList.add('active-dot');
                currentIndex = index;
            }
            
            // Next button click
            if (nextBtn) {
                nextBtn.addEventListener('click', function() {
                    currentIndex = (currentIndex + 1) % slides.length;
                    showSlide(currentIndex);
                });
            }
            
            // Previous button click
            if (prevBtn) {
                prevBtn.addEventListener('click', function() {
                    currentIndex = (currentIndex - 1 + slides.length) % slides.length;
                    showSlide(currentIndex);
                });
            }
            
            // Dot clicks
            dots.forEach((dot, index) => {
                dot.addEventListener('click', function() {
                    showSlide(index);
                });
            });
            
            // Lightbox functionality for gallery images
            const galleryImages = document.querySelectorAll('.slide img');
            
            galleryImages.forEach(img => {
                img.addEventListener('click', function() {
                    const lightbox = document.createElement('div');
                    lightbox.className = 'lightbox';
                    
                    const lightboxImg = document.createElement('img');
                    lightboxImg.src = this.src;
                    
                    const closeBtn = document.createElement('span');
                    closeBtn.className = 'lightbox-close';
                    closeBtn.innerHTML = '&times;';
                    
                    lightbox.appendChild(lightboxImg);
                    lightbox.appendChild(closeBtn);
                    document.body.appendChild(lightbox);
                    
                    closeBtn.addEventListener('click', function() {
                        document.body.removeChild(lightbox);
                    });
                    
                    lightbox.addEventListener('click', function(e) {
                        if (e.target === lightbox) {
                            document.body.removeChild(lightbox);
                        }
                    });
                });
            });
        });
        </script>
</body>
</html>