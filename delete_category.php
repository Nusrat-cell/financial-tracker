<?php
require_once __DIR__ . '/database.php';
require_once __DIR__ . '/jwt_helper.php';

header("Content-Type: application/json");

if ($_SERVER['REQUEST_METHOD'] !== 'DELETE') {
    http_response_code(405);
    echo json_encode(["error" => "Method not allowed"]);
    exit;
}

$user = authenticateJWT();
$CategoryId = $_GET['CategoryId'] ?? null;

if (!$CategoryId) {
    http_response_code(400);
    echo json_encode(["error" => "Missing CategoryId"]);
    exit;
}

try {
    $pdo = getDatabaseConnection();
    $stmt = $pdo->prepare("DELETE FROM categories WHERE CategoryId = ? AND user_id = ?");
    $stmt->execute([$CategoryId, $user['id']]);
    echo json_encode(["success" => true, "message" => "Category deleted"]);
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(["error" => $e->getMessage()]);
}
