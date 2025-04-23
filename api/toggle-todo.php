<?php
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    header("Access-Control-Allow-Origin: http://localhost:5173");
    header("Access-Control-Allow-Headers: Content-Type");
    header("Access-Control-Allow-Methods: PATCH, OPTIONS");
    exit(0);
}

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: http://localhost:5173');
header('Access-Control-Allow-Headers: Content-Type');
header('Access-Control-Allow-Methods: PATCH');

require 'db.php';

$data = json_decode(file_get_contents("php://input"), true);

if (!isset($data['id'])) {
    http_response_code(400);
    echo json_encode(["error" => "Missing required field: id"]);
    exit;
}

try {
    $stmt = $pdo->prepare("SELECT completed FROM todos WHERE id = ?");
    $stmt->execute([$data['id']]);
    $todo = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$todo) {
        http_response_code(404);
        echo json_encode(["error" => "Todo not found"]);
        exit;
    }

    $newStatus = $todo['completed'] ? 0 : 1;

    $updateStmt = $pdo->prepare("UPDATE todos SET completed = ? WHERE id = ?");
    $updateStmt->execute([$newStatus, $data['id']]);

    echo json_encode([
        "id" => $data['id'],
        "completed" => (bool)$newStatus
    ]);
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(["error" => "Failed to toggle todo: " . $e->getMessage()]);
}
?>
