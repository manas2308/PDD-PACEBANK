<?php
header('Content-Type: application/json');

$host = 'localhost';
$user = 'root';
$password = '';
$db_name = 'banking';

$conn = new mysqli($host, $user, $password, $db_name);

if ($conn->connect_error) {
    die(json_encode([
        "status" => false,
        "message" => "Database connection failed: " . $conn->connect_error,
        "data" => []
    ]));
}
?>