<?php
include 'database.php'; // Ensure database connection

$query = "SELECT * FROM services";
$result = mysqli_query($conn, $query);

while ($row = mysqli_fetch_assoc($result)) {
?>
    <div class="service-card">
        <img src="uploads/<?php echo htmlspecialchars($row['image_name']); ?>" alt="<?php echo htmlspecialchars($row['name']); ?>">
        <h3><?php echo htmlspecialchars($row['name']); ?></h3>
        <p><?php echo htmlspecialchars($row['description']); ?></p>
    </div>
<?php
}
?>
