<?php
session_start();
require 'db.php';
require 'vendor/autoload.php'; // Include PHPMailer

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

error_reporting(E_ALL);
ini_set('display_errors', 1);

// Check if user is logged in 
if (!isset($_SESSION['client_id'])) {
    echo json_encode(["status" => "error", "message" => "Unauthorized access. Please login."]);
    exit;
}

$client_id = $_SESSION['client_id'];

// Fetch user's email from the database
$query = "SELECT email FROM clients WHERE client_id = ?";
$stmt = $mysqli->prepare($query);
$stmt->bind_param("i", $client_id);
$stmt->execute();
$stmt->store_result();

if ($stmt->num_rows > 0) {
    $stmt->bind_result($email);
    $stmt->fetch();
} else {
    echo json_encode(["status" => "error", "message" => "User email not found."]);
    exit;
}

$stmt->close();

// Generate a random 6-digit OTP
$otp = rand(100000, 999999);

// Store OTP in session (valid for 5 minutes)
$_SESSION['otp'] = $otp;
$_SESSION['otp_expiry'] = time() + 300; // 5 minutes

// Send OTP via Email
$mail = new PHPMailer(true);
try {
    $mail->isSMTP();
    $mail->Host = 'smtp.gmail.com'; // SMTP Server
    $mail->SMTPAuth = true;
    $mail->Username = 'manasfrnd@gmail.com'; // Your Gmail ID
    $mail->Password = 'sppj elwk mlqs ofuu'; // Your App Password
    $mail->SMTPSecure = 'tls';
    $mail->Port = 587;

    $mail->setFrom('your_email@gmail.com', 'Pace Banking'); // Sender
    $mail->addAddress($email); // Recipient

    $mail->isHTML(true);
    $mail->Subject = "Your OTP for Pace Banking Login";
    $mail->Body    = "<h3>Your OTP is: <strong>$otp</strong></h3><p>It is valid for 5 minutes.</p>";

    if ($mail->send()) {
        echo json_encode(["status" => "success", "message" => "OTP sent successfully to your email."]);
    } else {
        echo json_encode(["status" => "error", "message" => "Failed to send OTP."]);
    }
} catch (Exception $e) {
    echo json_encode(["status" => "error", "message" => "Mailer Error: " . $mail->ErrorInfo]);
}
?>
