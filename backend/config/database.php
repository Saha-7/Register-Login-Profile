<?php


// Load environment variables from .env file
require_once __DIR__ . '/../../vendor/autoload.php';

// Initialize dotenv to read .env file
$dotenv = Dotenv\Dotenv::createImmutable(__DIR__ . '/../../');
$dotenv->load();

// MySQL Connection (Local XAMPP)
function getMySQLConnection() {
    // Read from .env file
    $host = $_ENV['DB_HOST'];
    $dbname = $_ENV['DB_NAME'];
    $username = $_ENV['DB_USER'];
    $password = $_ENV['DB_PASS'];
    
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
        // Read connection string from .env file
        $connectionString = $_ENV['MONGO_CONNECTION_STRING'];
        
        $client = new MongoDB\Client($connectionString);
        return $client->guvi_internship->profiles;
    } catch(Exception $e) {
        http_response_code(500);
        echo json_encode(['success' => false, 'message' => 'MongoDB connection failed: ' . $e->getMessage()]);
        exit();
    }
}
?>