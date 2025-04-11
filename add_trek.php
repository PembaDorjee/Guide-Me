<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json");
header("Access-Control-Allow-Methods: POST");

include '../database.php';

// Get raw POST data
$json = file_get_contents('php://input');
$data = json_decode($json, true);

if (json_last_error() !== JSON_ERROR_NONE) {
    http_response_code(400);
    die(json_encode([
        "success" => false,
        "error" => "Invalid JSON: " . json_last_error_msg()
    ]));
}

// Validate required fields
$required = ['name', 'duration', 'difficulty', 'region', 'altitude', 'price', 'description', 'image', 'service_id'];
$missing = array_diff($required, array_keys($data));
if (!empty($missing)) {
    http_response_code(400);
    die(json_encode([
        "success" => false,
        "error" => "Missing required fields",
        "missing_fields" => array_values($missing)
    ]));
}

try {
    // Prepare statement
    $stmt = $conn->prepare("INSERT INTO treks 
        (name, duration, difficulty, region, altitude, price, description, image, service_id) 
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
    
    // Bind parameters
    $stmt->bind_param("ssssddssi", 
        $data['name'],
        $data['duration'],
        $data['difficulty'],
        $data['region'],
        (float)$data['altitude'],
        (float)$data['price'],
        $data['description'],
        $data['image'],
        (int)$data['service_id']
    );

    // Execute and respond
    if ($stmt->execute()) {
        echo json_encode([
            "success" => true,
            "message" => "Trek added successfully",
            "trek_id" => $conn->insert_id
        ]);
    } else {
        http_response_code(500);
        echo json_encode([
            "success" => false,
            "error" => "Database error: " . $stmt->error
        ]);
    }
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        "success" => false,
        "error" => "Server error: " . $e->getMessage()
    ]);
}

$conn->close();
?>