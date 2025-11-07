<?php
require_once __DIR__ . '/database.php';
require_once __DIR__ . '/jwt_helper.php';

header('Content-Type: application/json');

$user = authenticateJWT();
$type = isset($_GET['type']) ? strtolower(trim($_GET['type'])) : null;

if (!in_array($type, ['income', 'expense'])) {
    http_response_code(400);
    echo json_encode(['error' => 'Invalid or missing type parameter']);
    exit;
}

try {
    $pdo = getDatabaseConnection();
    $stmt = $pdo->prepare("SELECT CategoryId, categoryName FROM categories WHERE user_id = ? AND type = ? ORDER BY categoryName ASC");
    $stmt->execute([$user['id'], $type]);
    $categories = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode(['categories' => $categories]);
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['error' => $e->getMessage()]);
}
