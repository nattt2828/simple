<?php
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    header("Access-Control-Allow-Origin: http://localhost:5173");
    header("Access-Control-Allow-Headers: Content-Type");
    header("Access-Control-Allow-Methods: GET, OPTIONS");
    exit(0);
}

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: http://localhost:5173');
header('Access-Control-Allow-Headers: Content-Type');
header('Access-Control-Allow-Methods: GET');

require 'db.php';

$filter = isset($_GET['filter']) ? $_GET['filter'] : 'All';

try {
    $sql = "SELECT * FROM todos";
    if ($filter === 'Active') {
        $sql .= " WHERE completed = 0";
    } elseif ($filter === 'Completed') {
        $sql .= " WHERE completed = 1";
    }

    $stmt = $pdo->query($sql);
    $todos = $stmt->fetchAll(PDO::FETCH_ASSOC);
    echo json_encode($todos);
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(["error" => "Failed to get todos: " . $e->getMessage()]);
}
?>
