<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json");

include 'database.php'; // Include your existing database connection

// Confirm the database connection is established
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Fetch users from the 'users' table
$sql = "SELECT id, name, email FROM users";
$result = $conn->query($sql);

// Check if the query was successful
if (!$result) {
    die("Query failed: " . $conn->error);
} else {
    // If query was successful, output some debug info
    echo "Query executed successfully<br>";
}

// Initialize an empty array to store users
$users = [];

// Check if there are any results
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $users[] = $row;
    }
    // Output the users data for debugging
    echo "<pre>";
    print_r($users);
    echo "</pre>";
} else {
    // If no users found, output this message
    echo "No users found.";
}

// Return JSON response with user data (if available)
echo json_encode($users);

// Close connection
$conn->close();
?>
