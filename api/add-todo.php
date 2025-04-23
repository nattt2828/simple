<?php
// add-todo.php - Add a new todo

// Handle CORS preflight
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    header("Access-Control-Allow-Origin: http://localhost:5173");
    header("Access-Control-Allow-Headers: Content-Type");
    header("Access-Control-Allow-Methods: POST, OPTIONS");
    exit(0);
}

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: http://localhost:5173');
header('Access-Control-Allow-Headers: Content-Type');
header('Access-Control-Allow-Methods: POST');

require 'db.php';

// Get JSON data from request
$data = json_decode(file_get_contents("php://input"), true);

if (!isset($data['text'])) {
    http_response_code(400);
    echo json_encode(["error" => "Missing required field: text"]);
    exit;
}

try {
    $stmt = $pdo->prepare("INSERT INTO todos (text, completed) VALUES (?, ?)");
    $completed = isset($data['completed']) ? $data['completed'] : false;
    $stmt->execute([$data['text'], $completed ? 1 : 0]);

    $newId = $pdo->lastInsertId();
    echo json_encode([
        "id" => $newId,
        "text" => $data['text'],
        "completed" => $completed
    ]);
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(["error" => "Failed to add todo: " . $e->getMessage()]);
}
?>
