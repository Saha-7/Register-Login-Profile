<?php

// Load environment variables from .env file
require_once __DIR__ . '/../../vendor/autoload.php';

// Initialize dotenv to read .env file
$dotenv = Dotenv\Dotenv::createImmutable(__DIR__ . '/../../');
$dotenv->load();

function getRedisConnection() {
    require_once __DIR__ . '/../../vendor/autoload.php';
    
    try {
        // Read from .env file and connect to Redis
        $redis = new Predis\Client([
            'scheme' => $_ENV['REDIS_SCHEME'],        // tcp or tls
            'host'   => $_ENV['REDIS_HOST'],          // redis-14399.crce217...
            'port'   => (int)$_ENV['REDIS_PORT'],     // 14399
            'username' => $_ENV['REDIS_USERNAME'],    // default
            'password' => $_ENV['REDIS_PASSWORD'],    // Your password
            'database' => 0                           // Database number
        ]);
        
        // Test connection
        $redis->ping();
        return $redis;
    } catch(Exception $e) {
        http_response_code(500);
        echo json_encode(['success' => false, 'message' => 'Redis connection failed: ' . $e->getMessage()]);
        exit();
    }
}
?>