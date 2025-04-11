<?php
require_once '../database.php';

// Handle search and sort parameters
$search = isset($_GET['search']) ? trim($_GET['search']) : '';
$sort = isset($_GET['sort']) ? $_GET['sort'] : 'default';

// Build the SQL query with search and sort
$sql = "SELECT * FROM Treks WHERE 1=1";

// Add search condition if search parameter exists
if (!empty($search)) {
    $search_param = "%$search%";
    $sql .= " AND (trek_name LIKE ? OR region LIKE ? OR description LIKE ?)";
}

// Add sorting
switch ($sort) {
    case 'price_low':
        $sql .= " ORDER BY price ASC";
        break;
    case 'price_high':
        $sql .= " ORDER BY price DESC";
        break;
    case 'duration_short':
        $sql .= " ORDER BY duration ASC";
        break;
    case 'duration_long':
        $sql .= " ORDER BY duration DESC";
        break;
    default:
        $sql .= " ORDER BY id DESC"; // Default sort by newest
        break;
}

// For debugging - print the SQL query
// echo $sql;

// Prepare and execute the query
$stmt = $conn->prepare($sql);

// Bind search parameters if they exist
if (!empty($search)) {
    $stmt->bind_param("sss", $search_param, $search_param, $search_param);
}

$stmt->execute();
$result = $stmt->get_result();
$treks = $result->fetch_all(MYSQLI_ASSOC);

// For debugging - print the number of treks found
// echo "Found " . count($treks) . " treks";
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Treks - Guide Me</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="css/style.css">
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
        }
        
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Poppins', sans-serif;
            background-color: #f5f6f8;
            color: var(--text);
            line-height: 1.6;
        }
        
        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 20px;
        }
        
        .page-header {
            background: linear-gradient(135deg, var(--dark) 0%, #2c3e50 100%);
            color: white;
            padding: 60px 0 80px;
            position: relative;
            overflow: hidden;
        }
        
        .page-header::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: url('../uploads/header-bg.jpg') no-repeat center center;
            background-size: cover;
            opacity: 0.2;
            z-index: 0;
        }
        
        .page-header .container {
            position: relative;
            z-index: 1;
        }
        
        .page-title {
            font-size: 2.5rem;
            font-weight: 700;
            margin-bottom: 15px;
        }
        
        .page-description {
            font-size: 1.1rem;
            max-width: 600px;
            margin-bottom: 30px;
        }
        
        .search-sort-container {
            background: white;
            border-radius: 10px;
            padding: 20px;
            margin-top: -40px;
            box-shadow: 0 5px 20px rgba(0,0,0,0.1);
            display: flex;
            flex-wrap: wrap;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 30px;
        }
        
        .search-box {
            flex: 1;
            min-width: 250px;
            margin-right: 20px;
            position: relative;
        }
        
        .search-box input {
            width: 100%;
            padding: 12px 20px 12px 45px;
            border: 1px solid var(--border);
            border-radius: 8px;
            font-size: 1rem;
            transition: all 0.3s ease;
        }
        
        .search-box input:focus {
            border-color: var(--primary);
            box-shadow: 0 0 0 3px rgba(21, 212, 85, 0.2);
            outline: none;
        }
        
        .search-box i {
            position: absolute;
            left: 15px;
            top: 50%;
            transform: translateY(-50%);
            color: var(--text-light);
        }
        
        .sort-box {
            display: flex;
            align-items: center;
        }
        
        .sort-box label {
            margin-right: 10px;
            color: var(--text-light);
        }
        
        .sort-box select {
            padding: 12px 20px;
            border: 1px solid var(--border);
            border-radius: 8px;
            font-size: 1rem;
            background-color: white;
            cursor: pointer;
            transition: all 0.3s ease;
        }
        
        .sort-box select:focus {
            border-color: var(--primary);
            box-shadow: 0 0 0 3px rgba(21, 212, 85, 0.2);
            outline: none;
        }
        
        .treks-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(350px, 1fr));
            gap: 25px;
            margin-bottom: 50px;
        }
        
        .trek-card {
            background: white;
            border-radius: 10px;
            overflow: hidden;
            box-shadow: 0 5px 15px rgba(0,0,0,0.08);
            transition: all 0.3s ease;
            position: relative;
        }
        
        .trek-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 25px rgba(0,0,0,0.15);
        }
        
        .trek-image {
            height: 200px;
            overflow: hidden;
            position: relative;
        }
        
        .trek-image img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            transition: transform 0.5s ease;
        }
        
        .trek-card:hover .trek-image img {
            transform: scale(1.05);
        }
        
        .trek-badge {
            position: absolute;
            top: 15px;
            left: 15px;
            background: var(--primary);
            color: white;
            padding: 5px 10px;
            border-radius: 5px;
            font-size: 0.8rem;
            font-weight: 500;
            z-index: 1;
        }
        
        .trek-content {
            padding: 20px;
        }
        
        .trek-price {
            color: var(--primary);
            font-size: 1.3rem;
            font-weight: 700;
            margin-bottom: 10px;
        }
        
        .trek-price span {
            font-size: 0.9rem;
            color: var(--text-light);
            font-weight: 400;
        }
        
        .trek-title {
            font-size: 1.2rem;
            font-weight: 600;
            margin-bottom: 10px;
            color: var(--dark);
        }
        
        .trek-location {
            display: flex;
            align-items: center;
            color: var(--text-light);
            margin-bottom: 15px;
            font-size: 0.9rem;
        }
        
        .trek-location i {
            margin-right: 5px;
            color: var(--secondary);
        }
        
        .trek-details {
            display: flex;
            justify-content: space-between;
            border-top: 1px solid var(--border);
            padding-top: 15px;
        }
        
        .trek-detail {
            display: flex;
            flex-direction: column;
            align-items: center;
            font-size: 0.85rem;
        }
        
        .trek-detail i {
            color: var(--secondary);
            margin-bottom: 5px;
            font-size: 1rem;
        }
        
        .trek-detail span {
            color: var(--text-light);
        }
        
        .trek-link {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            z-index: 2;
            text-indent: -9999px;
        }
        
        .favorite-btn {
            position: absolute;
            top: 15px;
            right: 15px;
            background: white;
            border: none;
            width: 35px;
            height: 35px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            z-index: 3;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
            transition: all 0.3s ease;
        }
        
        .favorite-btn i {
            color: #ccc;
            font-size: 1rem;
            transition: all 0.3s ease;
        }
        
        .favorite-btn:hover {
            background: #f8f8f8;
        }
        
        .favorite-btn:hover i {
            color: #ff6b6b;
        }
        
        .no-results {
            text-align: center;
            padding: 50px 0;
            color: var(--text-light);
        }
        
        .no-results i {
            font-size: 3rem;
            color: var(--border);
            margin-bottom: 20px;
        }
        
        @media (max-width: 768px) {
            .treks-grid {
                grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
            }
            
            .search-sort-container {
                flex-direction: column;
                align-items: stretch;
            }
            
            .search-box {
                margin-right: 0;
                margin-bottom: 15px;
            }
        }
    </style>
</head>
<body>
    <?php include 'header.php'; ?>
    
    <div class="page-header">
        <div class="container">
            <h1 class="page-title">Discover Amazing Treks</h1>
            <p class="page-description">Explore the world's most breathtaking trails and embark on unforgettable adventures with our expert guides.</p>
        </div>
    </div>
    
    <div class="container">
        <form class="search-sort-container" method="GET" action="treks.php">
            <div class="search-box">
                <i class="fas fa-search"></i>
                <input type="text" name="search" placeholder="Search by trek name, location or keyword..." value="<?php echo htmlspecialchars($search); ?>">
            </div>
            <div class="sort-box">
                <label for="sort">Sort by:</label>
                <select name="sort" id="sort" onchange="this.form.submit()">
                    <option value="default" <?php echo $sort === 'default' ? 'selected' : ''; ?>>Newest First</option>
                    <option value="price_low" <?php echo $sort === 'price_low' ? 'selected' : ''; ?>>Price: Low to High</option>
                    <option value="price_high" <?php echo $sort === 'price_high' ? 'selected' : ''; ?>>Price: High to Low</option>
                    <option value="duration_short" <?php echo $sort === 'duration_short' ? 'selected' : ''; ?>>Duration: Short to Long</option>
                    <option value="duration_long" <?php echo $sort === 'duration_long' ? 'selected' : ''; ?>>Duration: Long to Short</option>
                </select>
            </div>
        </form>
        
        <?php if (count($treks) > 0): ?>
            <div class="treks-grid">
                <?php foreach ($treks as $trek): ?>
                    <div class="trek-card">
                        <div class="trek-image">
                            <?php if (!empty($trek['featured_image'])): ?>
                                <img src="../<?php echo htmlspecialchars($trek['featured_image']); ?>" alt="<?php echo htmlspecialchars($trek['trek_name']); ?>">
                            <?php else: ?>
                                <img src="../uploads/default-trek.jpg" alt="<?php echo htmlspecialchars($trek['trek_name']); ?>">
                            <?php endif; ?>
                            <div class="trek-badge">POPULAR</div>
                        </div>
                        <div class="trek-content">
                            <div class="trek-price">
                                $<?php echo number_format($trek['price'], 0); ?> <span>/person</span>
                            </div>
                            <h3 class="trek-title"><?php echo htmlspecialchars($trek['trek_name']); ?></h3>
                            <div class="trek-location">
                                <i class="fas fa-map-marker-alt"></i>
                                <?php echo htmlspecialchars($trek['region']); ?>
                            </div>
                            <div class="trek-details">
                                <div class="trek-detail">
                                    <i class="fas fa-calendar-day"></i>
                                    <span><?php echo $trek['duration']; ?> days</span>
                                </div>
                                <div class="trek-detail">
                                    <i class="fas fa-hiking"></i>
                                    <span><?php echo htmlspecialchars($trek['difficulty']); ?></span>
                                </div>
                                <div class="trek-detail">
                                    <i class="fas fa-mountain"></i>
                                    <span><?php echo !empty($trek['max_altitude']) ? number_format($trek['max_altitude']) . 'm' : 'N/A'; ?></span>
                                </div>
                            </div>
                        </div>
                        <a href="trek_details.php?id=<?php echo $trek['id']; ?>" class="trek-link">View Trek</a>
                        <button class="favorite-btn" title="Add to favorites">
                            <i class="far fa-heart"></i>
                        </button>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php else: ?>
            <div class="no-results">
                <i class="fas fa-search"></i>
                <h3>No treks found</h3>
                <p>Try adjusting your search criteria or explore our other adventures.</p>
                
                <!-- Debug information - uncomment if needed -->
                <!-- <p>SQL Query: <?php echo $sql; ?></p> -->
            </div>
        <?php endif; ?>
    </div>
    
    <?php include 'footer.php'; ?>
    
    <script>
        // Toggle favorite button
        document.querySelectorAll('.favorite-btn').forEach(btn => {
            btn.addEventListener('click', function(e) {
                e.preventDefault();
                e.stopPropagation();
                
                const icon = this.querySelector('i');
                if (icon.classList.contains('far')) {
                    icon.classList.remove('far');
                    icon.classList.add('fas');
                    icon.style.color = '#ff6b6b';
                } else {
                    icon.classList.remove('fas');
                    icon.classList.add('far');
                    icon.style.color = '#ccc';
                }
            });
        });
    </script>
</body>
</html>