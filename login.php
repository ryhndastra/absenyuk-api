<?php
session_start();
include_once('config.php');

function sendJsonResponse($status, $message,$data = []){
    Header('Content-Type: application/json');
    echo json_encode([
        'status' => $status,
        'message' => $message,
        'data' => $data
    ]);
    exit;
}

function loginUser($conn,$email,$password){
    if(empty($email) || empty($password)){
        sendJsonResponse("Error","Email atau password tidak boleh kosong");
    }

    $query = "SELECT id, password from user where email = ?";
    if($statement = $conn->prepare($query)){
        $statement->bind_param("s", $email);
        $statement->execute();
        $statement->store_result();
        if($statement->num_rows > 0){
            $statement->bind_result($id,$db_password);
            $statement->fetch();
            if($password === $db_password){
                session_regenerate_id();
                $_SESSION['account_logged_in'] = true;
                $_SESSION['account_id'] = $id;
                $_SESSION['account_email'] = $email;
                $statement->close();
                sendJsonResponse("Success","Login berhasil",[
                    'id' => $id,
                    'email' => $email
                ]);
            }
            $statement->close();
            sendJsonResponse("Error","Email atau password salah");
        }
        $statement->close();
        sendJsonResponse("Error","Email atau password salah");
    }
    sendJsonResponse("Error","Incorrect query");
}

function checkLoggedIn(){
    if(isset($_SESSION['account_logged_in']) && $_SESSION['account_logged_in'] === true){
        sendJsonResponse("Succes","Anda sudah login",[
            'status' => true,
            'id' => $_SESSION['account_id'],
            'email' => $_SESSION['account_email']
        ]);
    }
    sendJsonResponse("Error","Anda belum login",[
        'status' => false,
    ]);

}

if($_SERVER['REQUEST_METHOD'] === 'POST'){
    if(isset($_POST['email']) && isset($_POST['password'])){
        loginUser($connection,$_POST['email'],$_POST['password']);
    }
    sendJsonResponse("Error","Missing post value");
}else if($_SERVER['REQUEST_METHOD'] === 'GET'){
    checkLoggedIn();
}else{
    sendJsonResponse("Error","Method not allowed");
}