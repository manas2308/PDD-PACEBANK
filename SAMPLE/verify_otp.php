<?php
session_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);

if (!isset($_SESSION['otp']) || !isset($_POST['otp'])) {
    echo json_encode(["status" => "error", "message" => "Invalid request."]);
    exit;
}

$user_otp = $_POST['otp'];

// Check if OTP is expired
if (time() > $_SESSION['otp_expiry']) {
    echo json_encode(["status" => "error", "message" => "OTP expired. Please request a new one."]);
    unset($_SESSION['otp']);
    unset($_SESSION['otp_expiry']);
    exit;
}

// Validate OTP
if ($user_otp == $_SESSION['otp']) {
    unset($_SESSION['otp']); // Clear OTP after successful verification
    unset($_SESSION['otp_expiry']);

    echo json_encode(["status" => "success", "message" => "OTP verified successfully."]);
} else {
    echo json_encode(["status" => "error", "message" => "Invalid OTP. Please try again."]);
}
?>
