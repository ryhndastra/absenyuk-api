<?php
header('Content-Type: application/json');
include_once('config.php');

function sendJsonResponse($status, $message, $data = []){
    echo json_encode([
        'status' => $status,
        'message' => $message,
        'data' => $data
    ]);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    $query = "SELECT * FROM absen ORDER BY waktu DESC";
    $result = $connection->query($query);

    $data = [];
    while ($row = $result->fetch_assoc()) {
        $data[] = $row;
    }

    sendJsonResponse("Success", "Data absen ditemukan", $data);
} else {
    sendJsonResponse("Error", "Invalid request method");
}
