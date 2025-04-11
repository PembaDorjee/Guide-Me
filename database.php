<?php
   $servername = "localhost";
   $username = "root";
   $password = "";
   $db_name = "trekDB";
  

// Create connection
$conn = new mysqli($servername, $username, $password, $db_name);

// Check connection
if ($conn->connect_error) {
  die("Database connection failed: " . $conn->connect_error);
}
echo "";
?>

  