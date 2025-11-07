<?php
require_once __DIR__ . '/database.php';
require_once __DIR__ . '/jwt_helper.php';

header("Content-Type: application/json");

if ($_SERVER['REQUEST_METHOD'] !== 'PUT') {
    http_response_code(405);
    echo json_encode(["error" => "Method not allowed"]);
    exit;
}

$user = authenticateJWT();
$data = json_decode(file_get_contents("php://input"), true);

$CategoryId = $data['CategoryId'] ?? null;
$categoryName = trim($data['categoryName'] ?? '');
$type = strtolower(trim($data['type'] ?? ''));

if (!$CategoryId || !$categoryName || !in_array($type, ['income', 'expense'])) {
    http_response_code(400);
    echo json_encode(["error" => "Invalid input"]);
    exit;
}

try {
    $pdo = getDatabaseConnection();

    // ✅ Check if another category with the same name exists
    $check = $pdo->prepare("SELECT COUNT(*) FROM categories WHERE LOWER(categoryName) = LOWER(?) AND user_id = ? AND CategoryId != ?");
    $check->execute([$categoryName, $user['id'], $CategoryId]);
    $exists = $check->fetchColumn();

    if ($exists > 0) {
        http_response_code(409);
        echo json_encode(["error" => "Category name already exists"]);
        exit;
    }

    // ✅ Update category
    $stmt = $pdo->prepare("UPDATE categories SET categoryName = ?, type = ? WHERE CategoryId = ? AND user_id = ?");
    $stmt->execute([$categoryName, $type, $CategoryId, $user['id']]);

    echo json_encode(["success" => true, "message" => "Category updated successfully"]);
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(["error" => $e->getMessage()]);
}
