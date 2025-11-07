<?php
require_once __DIR__ . '/database.php';
require_once __DIR__ . '/jwt_helper.php';

header("Content-Type: application/json");

$user = authenticateJWT();
if (!$user) {
    http_response_code(401);
    echo json_encode(["error" => "Unauthorized"]);
    exit;
}

try {
    $pdo = getDatabaseConnection();
    $stmt = $pdo->prepare("SELECT * FROM categories WHERE user_id = ?");
    $stmt->execute([$user['id']]);
    echo json_encode(["categories" => $stmt->fetchAll()]);
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(["error" => $e->getMessage()]);
}
