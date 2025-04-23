<?php
$DATABASE_HOST = 'localhost';
$DATABASE_USER = 'root';
$DATABASE_NAME = 'absensi';
$DATABASE_PASS = '';
$connection = mysqli_connect($DATABASE_HOST, $DATABASE_USER, $DATABASE_PASS, $DATABASE_NAME);

if(mysqli_connect_errno()) {
    sendJsonResponse("Error", "Database connection failed", mysqli_connect_error());
}