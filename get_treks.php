<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json");

include_once "../database.php"; // or use your actual connection path

$sql = "SELECT * FROM treks";
$result = $conn->query($sql);

$treks = [];

if ($result && $result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $treks[] = $row;
    }
}

echo json_encode($treks);
$conn->close();
?>
