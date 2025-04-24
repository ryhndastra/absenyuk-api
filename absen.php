<?php
header('Content-Type: application/json');
session_start();
include_once('config.php');

$rawData = file_get_contents('php://input');
$dt = json_decode($rawData, true);

function sendJsonResponse($status, $message, $data = []) {
    echo json_encode([
        'status' => $status,
        'message' => $message,
        'data' => $data
    ]);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!isset($dt['id_user'], $dt['npm']) || $dt['id_user'] === "" || $dt['npm'] === "") {
        sendJsonResponse("Error", "Data tidak boleh kosong");
    }

    $id_user = $dt['id_user'];
    $npm = $dt['npm'];

    $query = "SELECT status FROM absen WHERE id_user = ? ORDER BY waktu DESC LIMIT 1";
    $stmt = $connection->prepare($query);
    $stmt->bind_param("i", $id_user);
    $stmt->execute();
    $result = $stmt->get_result();

    $status = 1; 
    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $lastStatus = $row['status'];
        $status = $lastStatus == 1 ? 0 : 1;
    }

    $insertQuery = "INSERT INTO absen (id_user, npm, waktu, status) VALUES (?, ?, NOW(), ?)";
    $insertStmt = $connection->prepare($insertQuery);
    $insertStmt->bind_param("iii", $id_user, $npm, $status);
    if ($insertStmt->execute()) {
        $message = $status == 1 ? "Berhasil absen masuk" : "Berhasil absen keluar";
        sendJsonResponse("Success", $message, ["status" => $status]);
    } else {
        sendJsonResponse("Error", "Gagal menyimpan data absensi");
    }
} else {
    sendJsonResponse("Error", "Invalid request method");
}
