<?php
require_once __DIR__ . '/../utils/cors.php';
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../config/redis.php';

// Get JSON data from request
$data = json_decode(file_get_contents('php://input'), true);

// Validate input
if (!isset($data['email']) || !isset($data['password'])) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Email and password are required']);
    exit();
}

$email = trim($data['email']);
$password = trim($data['password']);

try {
    $conn = getMySQLConnection();
    
    // Get user using Prepared Statement
    $stmt = $conn->prepare("SELECT id, email, password FROM users WHERE email = ?");
    $stmt->execute([$email]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$user) {
        http_response_code(401);
        echo json_encode(['success' => false, 'message' => 'Invalid credentials']);
        exit();
    }
    
    // Verify password
    if (!password_verify($password, $user['password'])) {
        http_response_code(401);
        echo json_encode(['success' => false, 'message' => 'Invalid credentials']);
        exit();
    }
    
    // Generate session token
    $sessionToken = bin2hex(random_bytes(32));
    
    // Store session in Redis
    $redis = getRedisConnection();
    $sessionData = json_encode([
        'user_id' => $user['id'],
        'email' => $user['email'],
        'login_time' => time()
    ]);
    
    // Store session for 24 hours
    $redis->setex($sessionToken, 86400, $sessionData);
    
    http_response_code(200);
    echo json_encode([
        'success' => true,
        'message' => 'Login successful',
        'sessionToken' => $sessionToken,
        'email' => $user['email']
    ]);
    
} catch(Exception $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Login failed']);
}
?>