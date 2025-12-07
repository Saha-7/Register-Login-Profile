<?php
require_once __DIR__ . '/../utils/cors.php';
require_once __DIR__ . '/../config/database.php';

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

// Validate email format
if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Invalid email format']);
    exit();
}

// Validate password length
if (strlen($password) < 6) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Password must be at least 6 characters']);
    exit();
}

try {
    $conn = getMySQLConnection();
    
    // Check if email already exists using Prepared Statement
    $stmt = $conn->prepare("SELECT id FROM users WHERE email = ?");
    $stmt->execute([$email]);
    
    if ($stmt->fetch()) {
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => 'Email already registered']);
        exit();
    }
    
    // Hash password
    $hashedPassword = password_hash($password, PASSWORD_BCRYPT);
    
    // Insert user using Prepared Statement
    $stmt = $conn->prepare("INSERT INTO users (email, password) VALUES (?, ?)");
    $stmt->execute([$email, $hashedPassword]);
    
    http_response_code(201);
    echo json_encode([
        'success' => true, 
        'message' => 'Registration successful! Please login.'
    ]);
    
} catch(Exception $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Registration failed']);
}
?>
