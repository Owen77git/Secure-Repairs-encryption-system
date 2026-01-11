<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST');
header('Access-Control-Allow-Headers: Content-Type');

$host = 'localhost';
$dbname = 'secure_repair';
$username = 'root';
$password = '';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    $input = json_decode(file_get_contents('php://input'), true);
    $password = isset($input['password']) ? strtoupper(trim($input['password'])) : '';
    $hours = isset($input['hours']) ? intval($input['hours']) : 24;
    
    $stmt = $pdo->prepare("
        UPDATE cctv_log 
        SET expires_at = DATE_ADD(expires_at, INTERVAL ? HOUR) 
        WHERE password = ?
    ");
    $stmt->execute([$hours, $password]);
    
    echo json_encode([
        'success' => true,
        'message' => 'Access extended successfully'
    ]);
    
} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => 'Error: ' . $e->getMessage()
    ]);
}
?>