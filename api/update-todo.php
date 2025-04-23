<?php
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    header("Access-Control-Allow-Origin: http://localhost:5173", "https://nattt2828.github.io");
    header("Access-Control-Allow-Headers: Content-Type");
    header("Access-Control-Allow-Methods: PUT, OPTIONS");
    exit(0);
}

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: http://localhost:5173', "https://nattt2828.github.io");
header('Access-Control-Allow-Headers: Content-Type');
header('Access-Control-Allow-Methods: PUT');

require 'db.php';

$data = json_decode(file_get_contents("php://input"), true);

if (!isset($data['id']) || !isset($data['text'])) {
    http_response_code(400);
    echo json_encode(["error" => "Missing required fields: id and text"]);
    exit;
}

try {
    $stmt = $pdo->prepare("UPDATE todos SET text = ? WHERE id = ?");
    $stmt->execute([$data['text'], $data['id']]);

    if ($stmt->rowCount() === 0) {
        http_response_code(404);
        echo json_encode(["error" => "Todo not found"]);
        exit;
    }

    echo json_encode(["message" => "Todo updated", "id" => $data['id']]);
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(["error" => "Failed to update todo: " . $e->getMessage()]);
}
?>
