<?php
session_start();
require_once 'database.php';

echo "<h1>Avatar Debug Page</h1>";

if(isset($_SESSION['client_id'])) {
    echo "<p>Logged in as: " . htmlspecialchars($_SESSION['full_name'] ?? 'Unknown') . "</p>";
    echo "<p>Session avatar_path: " . htmlspecialchars($_SESSION['avatar_path'] ?? 'Not set') . "</p>";
    
    // Query the database directly
    try {
        $stmt = $pdo->prepare("SELECT avatar_path FROM clients WHERE client_id = ?");
        $stmt->execute([$_SESSION['client_id']]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        
        echo "<p>Database avatar_path: " . htmlspecialchars($result['avatar_path'] ?? 'Not found') . "</p>";
        
        if($result && $result['avatar_path']) {
            echo "<p>Avatar image:</p>";
            echo "<img src='" . htmlspecialchars($result['avatar_path']) . "' style='max-width: 200px;'>";
        } else {
            echo "<p>No avatar found in database.</p>";
        }
    } catch (PDOException $e) {
        echo "<p>Database error: " . htmlspecialchars($e->getMessage()) . "</p>";
    }
} else {
    echo "<p>Not logged in.</p>";
}
?>