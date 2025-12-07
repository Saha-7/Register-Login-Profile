<?php
function getRedisConnection() {
    require_once __DIR__ . '/../../vendor/autoload.php';
    
    try {
        $redis = new Predis\Client([
            'scheme' => 'tls',  // Changed to tls for cloud
            'host'   => 'YOUR_REDIS_HOST',  // e.g., redis-12345.c123.us-east-1-1.ec2.cloud.redislabs.com
            'port'   => YOUR_REDIS_PORT,    // e.g., 12345
            'password' => 'YOUR_REDIS_PASSWORD'
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