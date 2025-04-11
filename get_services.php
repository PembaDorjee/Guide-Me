<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json");

// Connect to DB using relative path
require_once '../database.php';  // go up one folder to access it

$sql = "SELECT * FROM services";
$result = $conn->query($sql);

$services = [];

if ($result && $result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $services[] = $row;
    }
}

echo json_encode($services);
$conn->close();
?>
