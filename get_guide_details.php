<?php
session_start();
require_once '../database.php';
header('Content-Type: application/json');

// Check if user is logged in and is admin
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    http_response_code(401);
    echo json_encode(['error' => 'Unauthorized']);
    exit();
}

if (!isset($_GET['id'])) {
    http_response_code(400);
    echo json_encode(['error' => 'Guide ID is required']);
    exit();
}

$guide_id = intval($_GET['id']);

try {
    // Get guide basic information using the correct column names from your database
    $stmt = $conn->prepare("SELECT guide_id as id, full_name as name, email, phone, 
                           title, specialization, IFNULL(experience, 0) as experience, 
                           IFNULL(bio, '') as bio, IFNULL(avatar_path, '') as avatar_path,
                           IFNULL(languages, '') as languages, IFNULL(social_media, '') as social_media,
                           IFNULL(worked_with, '') as worked_with, IFNULL(certifications, '') as certifications,
                           IFNULL(certification_files, '') as certification_files, 
                           IFNULL(achievements, '') as achievements, IFNULL(stats, '') as stats,
                           IFNULL(verification_code, '') as verification_code, 
                           created_at, IFNULL(updated_at, '') as updated_at, status
                           FROM Guide WHERE guide_id = ?");
    
    if (!$stmt) {
        throw new Exception("Prepare statement failed: " . $conn->error);
    }
    
    $stmt->bind_param("i", $guide_id);
    
    if (!$stmt->execute()) {
        throw new Exception("Execute failed: " . $stmt->error);
    }
    
    $result = $stmt->get_result();
    
    if ($result->num_rows === 0) {
        http_response_code(404);
        echo json_encode(['error' => 'Guide not found']);
        exit();
    }
    
    $guide = $result->fetch_assoc();
    
    // Parse JSON fields if they exist
    $jsonFields = ['social_media', 'worked_with', 'certifications', 'achievements', 'stats'];
    foreach ($jsonFields as $field) {
        if (!empty($guide[$field]) && $guide[$field] !== null) {
            try {
                $decoded = json_decode($guide[$field], true);
                if ($decoded !== null) {
                    $guide[$field] = $decoded;
                }
            } catch (Exception $e) {
                // If JSON parsing fails, keep as string
                error_log("Failed to parse JSON for field $field: " . $e->getMessage());
            }
        }
    }
    
    // Keep the original certification_files string for display
    $guide['raw_certification_files'] = $guide['certification_files'];
    
    // Format certification files as documents array for consistency with frontend
    $guide['documents'] = [];
    if (!empty($guide['certification_files']) && $guide['certification_files'] !== null) {
        $certFiles = explode(',', $guide['certification_files']);
        foreach ($certFiles as $file) {
            $file = trim($file);
            if (!empty($file)) {
                $fileInfo = pathinfo($file);
                $isImage = in_array(strtolower($fileInfo['extension'] ?? ''), ['jpg', 'jpeg', 'png', 'gif']);
                
                $guide['documents'][] = [
                    'name' => $fileInfo['basename'] ?? 'Document',
                    'path' => $file,
                    'type' => $isImage ? 'image/jpeg' : 'application/pdf',
                    'url' => 'http://localhost/FYPC/' . $file
                ];
            }
        }
    }
    
    echo json_encode($guide);
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['error' => $e->getMessage()]);
    // Log the error for debugging
    error_log("Error in get_guide_details.php: " . $e->getMessage());
}

$conn->close();
?>