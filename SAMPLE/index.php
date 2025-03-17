<?php
require 'db.php'; // Ensure this file contains $mysqli

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'vendor/autoload.php';

date_default_timezone_set('Asia/Kolkata'); // Set timezone to India

$error = "";
$success = "";

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["email"])) {
    $email = filter_var($_POST["email"], FILTER_SANITIZE_EMAIL);
    $email = filter_var($email, FILTER_VALIDATE_EMAIL);

    if (!$email) {
        $error = "Invalid email address! Please enter a valid email.";
    } else {
        if (!$mysqli) {
            die("Database connection failed: " . $mysqli->connect_error);
        }

        // Check if email exists
        $stmt = $mysqli->prepare("SELECT * FROM clients WHERE email = ?");
        if (!$stmt) {
            die("SQL Prepare Error: " . $mysqli->error);
        }

        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows == 0) {
            $error = "No user is registered with this email address!";
        }
        $stmt->close();
    }

    if (empty($error)) {
        // Generate reset key
        $expDate = date("Y-m-d H:i:s", strtotime('+1 day'));
        $key = md5(uniqid(rand(), true));

        // Insert reset key into database
        $stmt = $mysqli->prepare("INSERT INTO password_reset_temp (email, reset_key, expDate) VALUES (?, ?, ?)");
        if (!$stmt) {
            die("SQL Insert Error: " . $mysqli->error);
        }
        $stmt->bind_param("sss", $email, $key, $expDate);
        $stmt->execute();
        $stmt->close();

        // Email setup
        $mail = new PHPMailer(true);
        try {
            $mail->isSMTP();
            $mail->Host = "smtp.gmail.com";
            $mail->SMTPAuth = true;
            $mail->Username = "manasfrnd@gmail.com";
            $mail->Password = "sppj elwk mlqs ofuu";
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
            $mail->Port = 587;

            $mail->setFrom("noreply@yourwebsite.com", "Your Website");
            $mail->addAddress($email);
            $mail->isHTML(true);
            $mail->Subject = "Password Reset Request";

            $resetLink = "http://localhost/SAMPLE/reset_password.php?key=$key&email=$email";
            $mail->Body = "
                <p>Dear User,</p>
                <p>Please click the link below to reset your password:</p>
                <p><a href='$resetLink'>$resetLink</a></p>
                <p>This link will expire in 24 hours.</p>
                <p>If you didn't request a password reset, please ignore this email.</p>
                <p>Best Regards,<br>Your Website Team</p>
            ";

            $mail->send();
            $success = "An email has been sent to reset your password.";
        } catch (Exception $e) {
            $error = "Mailer Error: {$mail->ErrorInfo}";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Forgot Password</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
</head>
<body class="d-flex justify-content-center align-items-center vh-100 bg-light">

<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="card shadow-lg">
                <div class="card-body p-4">
                    <h3 class="text-center mb-4">Forgot Password</h3>

                    <?php if (!empty($error)): ?>
                        <div class="alert alert-danger"><?= $error ?></div>
                    <?php endif; ?>

                    <?php if (!empty($success)): ?>
                        <div class="alert alert-success"><?= $success ?></div>
                    <?php else: ?>
                        <form method="post">
                            <div class="mb-3">
                                <label for="email" class="form-label">Enter Your Email Address:</label>
                                <input type="email" name="email" id="email" class="form-control" placeholder="username@email.com" required />
                            </div>

                            <button type="submit" class="btn btn-primary w-100">Reset Password</button>
                        </form>
                    <?php endif; ?>

                    <div class="text-center mt-3">
                        <a href="../SAMPLE/user/user_login.php" class="text-decoration-none">Back to Login</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

</body>
</html>
