<?php
// MySQL Connection (Local XAMPP)
function getMySQLConnection() {
    $host = 'localhost';
    $dbname = 'guvi_internship';
    $username = 'root';
    $password = ''; // Default XAMPP password is empty
    
    try {
        $conn = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
        $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        return $conn;
    } catch(PDOException $e) {
        http_response_code(500);
        echo json_encode(['success' => false, 'message' => 'Database connection failed: ' . $e->getMessage()]);
        exit();
    }
}

// MongoDB Connection (Cloud - MongoDB Atlas)
function getMongoDBConnection() {
    require_once __DIR__ . '/../../vendor/autoload.php';
    
    try {
        // OPTION 1: Using MongoDB Atlas (Recommended - Cloud)
        // Get this from MongoDB Atlas: Database -> Connect -> Connect your application
        $connectionString = "mongodb+srv://guvi_user:YOUR_PASSWORD@cluster0.xxxxx.mongodb.net/?retryWrites=true&w=majority";
        
        // OPTION 2: Using Local MongoDB (if installed)
        // Uncomment below and comment above if using local MongoDB
        // $connectionString = "mongodb://localhost:27017";
        
        $client = new MongoDB\Client($connectionString);
        return $client->guvi_internship->profiles;
    } catch(Exception $e) {
        http_response_code(500);
        echo json_encode(['success' => false, 'message' => 'MongoDB connection failed: ' . $e->getMessage()]);
        exit();
    }
}
?>