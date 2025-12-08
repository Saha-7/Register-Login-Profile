<?php

// Load environment variables from .env file
require_once __DIR__ . '/../../vendor/autoload.php';

// Initialize dotenv to read .env file
$dotenv = Dotenv\Dotenv::createImmutable(__DIR__ . '/../../');
$dotenv->load();

function getRedisConnection() {
    require_once __DIR__ . '/../../vendor/autoload.php';
    
    try {
        // Read from .env file
        $redis = new Predis\Client([
            'scheme' => $_ENV['REDIS_SCHEME'],      // 'tls' for cloud, 'tcp' for local
            'host'   => $_ENV['REDIS_HOST'],
            'port'   => (int)$_ENV['REDIS_PORT'],
            'password' => $_ENV['REDIS_PASSWORD']
        ]);
        
        $redis->ping();
        return $redis;
    } catch(Exception $e) {
        http_response_code(500);
        echo json_encode(['success' => false, 'message' => 'Redis connection failed: ' . $e->getMessage()]);
        exit();
    }
}
?>