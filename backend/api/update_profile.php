<?php
require_once __DIR__ . '/../utils/cors.php';
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../config/redis.php';

// Get session token from headers
$headers = getallheaders();
$sessionToken = isset($headers['Authorization']) ? $headers['Authorization'] : '';

if (empty($sessionToken)) {
    http_response_code(401);
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit();
}

// Get JSON data from request
$data = json_decode(file_get_contents('php://input'), true);

// Validate input
if (!isset($data['age']) || !isset($data['dob']) || !isset($data['contact'])) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'All fields are required']);
    exit();
}

$age = trim($data['age']);
$dob = trim($data['dob']);
$contact = trim($data['contact']);

// Basic validation
if (!is_numeric($age) || $age < 1 || $age > 120) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Invalid age']);
    exit();
}

if (!preg_match('/^\d{10}$/', $contact)) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Contact must be 10 digits']);
    exit();
}

try {
    // Verify session in Redis
    $redis = getRedisConnection();
    $sessionData = $redis->get($sessionToken);
    
    if (!$sessionData) {
        http_response_code(401);
        echo json_encode(['success' => false, 'message' => 'Session expired. Please login again.']);
        exit();
    }
    
    $session = json_decode($sessionData, true);
    $email = $session['email'];
    
    // Update or Insert profile in MongoDB
    $profileCollection = getMongoDBConnection();
    
    $profileData = [
        'email' => $email,
        'age' => (int)$age,
        'dob' => $dob,
        'contact' => $contact,
        'updated_at' => new MongoDB\BSON\UTCDateTime()
    ];
    
    $result = $profileCollection->updateOne(
        ['email' => $email],
        ['$set' => $profileData],
        ['upsert' => true]
    );
    
    http_response_code(200);
    echo json_encode([
        'success' => true,
        'message' => 'Profile updated successfully'
    ]);
    
} catch(Exception $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Failed to update profile']);
}
?>