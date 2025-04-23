<?php
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    header("Access-Control-Allow-Origin: http://localhost:5173", "https://nattt2828.github.io");
    header("Access-Control-Allow-Headers: Content-Type");
    header("Access-Control-Allow-Methods: DELETE, OPTIONS");
    exit(0);
}

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: http://localhost:5173', "https://nattt2828.github.io");
header('Access-Control-Allow-Headers: Content-Type');
header('Access-Control-Allow-Methods: DELETE');

require 'db.php';

$data = json_decode(file_get_contents("php://input"), true);

if (!isset($data['id'])) {
    http_response_code(400);
    echo json_encode(["error" => "Missing required field: id"]);
    exit;
}

try {
    $stmt = $pdo->prepare("DELETE FROM todos WHERE id = ?");
    $stmt->execute([$data['id']]);

    if ($stmt->rowCount() === 0) {
        http_response_code(404);
        echo json_encode(["error" => "Todo not found"]);
        exit;
    }

    echo json_encode(["message" => "Todo deleted", "id" => $data['id']]);
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(["error" => "Failed to delete todo: " . $e->getMessage()]);
}
?>
