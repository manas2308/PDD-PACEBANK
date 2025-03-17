<?php
session_start();
include 'conf/config.php';
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Redirect if user is not logged in
if (!isset($_SESSION['client_id'])) {
    echo "<script>alert('Unauthorized access. Please login first.'); window.location.href='user_login.php';</script>";
    exit;
}

$client_id = $_SESSION['client_id'];
$email = "";

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
    echo "<script>alert('User details not found. Please login again.'); window.location.href='user_login.php';</script>";
    exit;
}

$stmt->close();
$mysqli->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Verify OTP - Banking App</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
</head>
<style>
    body {
        background-image: url('/SAMPLE/IMAGES/index1.avif');
        background-size: cover;
        background-position: center;
        background-repeat: no-repeat;
        background-attachment: fixed;
        height: 100vh;
        margin: 0;
        padding: 0;
    }
</style>

<body class="bg-light">
<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-md-5">
            <div class="card shadow">
                <div class="card-header bg-primary text-white text-center">
                    <h3>Verify OTP</h3>
                </div>
                <div class="card-body">
                    
                    <!-- Send OTP Form -->
                    <form id="sendOtpForm">
                        <div class="mb-3">
                            <label for="email" class="form-label">Registered Email Address</label>
                            <input type="text" id="email" name="email" class="form-control" value="<?php echo htmlspecialchars($email); ?>" readonly>
                        </div>
                        <button type="submit" class="btn btn-primary w-100">Send OTP</button>
                    </form>

                    <!-- Enter OTP Form (Hidden Initially) -->
                    <form id="verifyOtpForm" style="display: none;">
                        <div class="mb-3">
                            <label for="otp" class="form-label">Enter OTP</label>
                            <input type="text" id="otp" name="otp" class="form-control" required>
                        </div>
                        <button type="submit" class="btn btn-success w-100">Verify OTP</button>
                    </form>

                    <!-- Message Area -->
                    <div id="message" class="mt-3 text-center"></div>

                </div>
            </div>
        </div>
    </div>
</div>

<script>
$(document).ready(function() {
    
    // Send OTP
    $("#sendOtpForm").submit(function(event) {
        event.preventDefault();
        $.ajax({
            url: "../sendOtp.php",
            type: "POST",
            success: function(response) {
                let data = JSON.parse(response);
                if (data.status === "success") {
                    $("#message").html('<div class="alert alert-success">OTP Sent Successfully</div>');
                    $("#sendOtpForm").hide();
                    $("#verifyOtpForm").show();
                } else {
                    $("#message").html('<div class="alert alert-danger">' + data.message + '</div>');
                }
            }
        });
    });

    // Verify OTP
    $("#verifyOtpForm").submit(function(event) {
        event.preventDefault();
        $.ajax({
            url: "../verify_otp.php",
            type: "POST",
            data: { otp: $("#otp").val() },
            success: function(response) {
                let data = JSON.parse(response);
                if (data.status === "success") {
                    $("#message").html('<div class="alert alert-success">OTP Verified! Redirecting...</div>');
                    setTimeout(function() {
                        window.location.href = "client_dashboard.php";
                    }, 2000);
                } else {
                    $("#message").html('<div class="alert alert-danger">' + data.message + '</div>');
                }
            }
        });
    });

});
</script>

</body>
</html>
