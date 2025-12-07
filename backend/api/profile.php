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
    
    // Get profile from MongoDB
    $profileCollection = getMongoDBConnection();
    $profile = $profileCollection->findOne(['email' => $email]);
    
    if ($profile) {
        http_response_code(200);
        echo json_encode([
            'success' => true,
            'profile' => [
                'email' => $profile['email'],
                'age' => $profile['age'] ?? '',
                'dob' => $profile['dob'] ?? '',
                'contact' => $profile['contact'] ?? ''
            ]
        ]);
    } else {
        // No profile yet, return empty profile
        http_response_code(200);
        echo json_encode([
            'success' => true,
            'profile' => [
                'email' => $email,
                'age' => '',
                'dob' => '',
                'contact' => ''
            ]
        ]);
    }
    
} catch(Exception $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Failed to fetch profile']);
}
?>