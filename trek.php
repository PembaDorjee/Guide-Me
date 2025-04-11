<?php
session_start();
require_once '../database.php';

error_reporting(E_ALL);
ini_set('display_errors', 1);

// Handle search and sorting
$search = isset($_GET['search']) ? $_GET['search'] : '';
$sort = isset($_GET['sort']) ? $_GET['sort'] : 'trek_name';
$order = isset($_GET['order']) ? $_GET['order'] : 'ASC';

try {
    // Modified query to match actual table columns
    $sql = "SELECT trek_id, trek_name, duration, difficulty, max_altitude, region, price, 
            description, highlights, itinerary, included, not_included, best_season, featured_image 
            FROM treks 
            WHERE trek_name LIKE ? 
            ORDER BY $sort $order";
    $stmt = $conn->prepare($sql);
    $searchTerm = "%$search%";
    $stmt->bind_param("s", $searchTerm);
    $stmt->execute();
    $result = $stmt->get_result();
    $treks = $result->fetch_all(MYSQLI_ASSOC);
} catch (Exception $e) {
    error_log("Database Error: " . $e->getMessage());
    die("Database Error: " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Available Treks</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="css/trek.css">
</head>
<body>
    <div class="search-sort-container">
        <form method="GET" class="search-form">
            <input type="text" name="search" placeholder="Search treks..." value="<?php echo htmlspecialchars($search); ?>">
            <select name="sort">
                <option value="trek_name" <?php echo $sort === 'trek_name' ? 'selected' : ''; ?>>Name</option>
                <option value="price" <?php echo $sort === 'price' ? 'selected' : ''; ?>>Price</option>
                <option value="duration" <?php echo $sort === 'duration' ? 'selected' : ''; ?>>Duration</option>
                <option value="difficulty" <?php echo $sort === 'difficulty' ? 'selected' : ''; ?>>Difficulty</option>
            </select>
            <select name="order">
                <option value="ASC" <?php echo $order === 'ASC' ? 'selected' : ''; ?>>Ascending</option>
                <option value="DESC" <?php echo $order === 'DESC' ? 'selected' : ''; ?>>Descending</option>
            </select>
            <button type="submit">Apply</button>
        </form>
    </div>

    <div class="treks-container">
        <?php if (empty($treks)): ?>
            <p>No treks found.</p>
        <?php else: ?>
            <?php foreach ($treks as $trek): ?>
                
                    <div class="trek-card">
                        <img src="<?php echo htmlspecialchars('../' . $trek['featured_image']); ?>" 
                             alt="<?php echo htmlspecialchars($trek['trek_name']); ?>"
                             onerror="this.onerror=null; this.src='../uploads/treks/default-trek.jpg';">
                        <div class="trek-details">
                            <h2><?php echo htmlspecialchars($trek['trek_name']); ?></h2>
                            <p class="location"><i class="fas fa-map-marker-alt"></i> <?php echo htmlspecialchars($trek['region'] ?? 'Region not specified'); ?></p>
                            <p class="duration"><i class="fas fa-clock"></i> <?php echo htmlspecialchars($trek['duration'] ?? '0'); ?> days</p>
                            <p class="difficulty"><i class="fas fa-mountain"></i> Difficulty: <?php echo htmlspecialchars($trek['difficulty'] ?? 'Not specified'); ?></p>
                            <p class="altitude"><i class="fas fa-arrow-up"></i> Max Altitude: <?php echo htmlspecialchars($trek['max_altitude'] ?? '0'); ?>m</p>
                            <p class="price"><i class="fas fa-tag"></i> NPR <?php echo number_format($trek['price'] ?? 0); ?></p>
                            <button onclick="location.href='trek_details.php?id=<?php echo $trek['trek_id']; ?>'">View Details</button>
                        </div>
                    </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>

    <!-- Add this for debugging -->
    <?php if (isset($_GET['debug'])): ?>
        <div style="margin: 20px; padding: 20px; background: #f5f5f5;">
            <pre><?php print_r($treks); ?></pre>
        </div>
    <?php endif; ?>
</body>
</html>
