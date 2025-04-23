<?php
session_start();

function sendJsonResponse($status, $message,$data = []){
    Header('Content-Type: application/json');
    echo json_encode([
        'status' => $status,
        'message' => $message,
        'data' => $data
    ]);
    exit;
}

if(isset($_SESSION['account_logged_in']) && $_SESSION['account_logged_in'] === true){
    session_unset();
    session_destroy();
    sendJsonResponse("Success","Logout berhasil");
}else{
    sendJsonResponse("Error","User belum login");
}