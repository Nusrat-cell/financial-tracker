<?php
require_once __DIR__ . '/database.php';
require_once __DIR__ . '/jwt_helper.php';

header("Content-Type: application/json");

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(["error" => "Method not allowed"]);
    exit;
}

$user = authenticateJWT();
$data = json_decode(file_get_contents("php://input"), true);

$categoryName = trim($data['categoryName'] ?? '');
$type = strtolower(trim($data['type'] ?? ''));

if (!$categoryName || !in_array($type, ['income', 'expense'])) {
    http_response_code(400);
    echo json_encode(["error" => "Invalid category name or type"]);
    exit;
}

try {
    $pdo = getDatabaseConnection();

    // ✅ Check if category already exists for this user (case-insensitive)
    $check = $pdo->prepare("SELECT COUNT(*) FROM categories WHERE LOWER(categoryName) = LOWER(?) AND user_id = ?");
    $check->execute([$categoryName, $user['id']]);
    $exists = $check->fetchColumn();

    if ($exists > 0) {
        http_response_code(409);
        echo json_encode(["error" => "Category name already exists"]);
        exit;
    }

    // ✅ Insert new category
    $stmt = $pdo->prepare("INSERT INTO categories (user_id, categoryName, type) VALUES (?, ?, ?)");
    $stmt->execute([$user['id'], $categoryName, $type]);

    echo json_encode(["success" => true, "message" => "Category added successfully"]);
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(["error" => $e->getMessage()]);
}
