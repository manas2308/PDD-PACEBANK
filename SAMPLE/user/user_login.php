<?php
session_start();
include 'conf/config.php';
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Function to generate CAPTCHA text
function generateCaptchaText($length = 6) {
    return substr(str_shuffle("ABCDEFGHJKLMNPQRSTUVWXYZ23456789"), 0, $length);
}

// Generate CAPTCHA only if it's not set (Avoid regenerating every page load)
if (!isset($_SESSION['captcha'])) {
    $_SESSION['captcha'] = generateCaptchaText();
}

// Generate CAPTCHA Image
if (isset($_GET['captcha'])) {
    header("Content-Type: image/png");
    $image = imagecreate(150, 50);
    $bg_color = imagecolorallocate($image, 255, 255, 255);
    $text_color = imagecolorallocate($image, 0, 0, 0);
    imagestring($image, 5, 30, 15, $_SESSION['captcha'], $text_color);
    imagepng($image);
    imagedestroy($image);
    exit;
}

// Handle login request
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = trim($_POST['name']);
    $password = $_POST['password'];
    $captcha_code = strtoupper(trim($_POST['captcha_code']));

    // Validate CAPTCHA
    if (!isset($_SESSION['captcha']) || $captcha_code !== strtoupper($_SESSION['captcha'])) {
        echo "<script>alert('Incorrect CAPTCHA. Please try again.'); window.location.href='user_login.php';</script>";
        exit;
    }

    // Reset CAPTCHA after validation
    unset($_SESSION['captcha']);

    // Validate User Login
    $query = "SELECT client_id, name, password, phone FROM clients WHERE name = ?";
    $stmt = $mysqli->prepare($query);
    $stmt->bind_param("s", $name);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows > 0) {
        $stmt->bind_result($client_id, $db_name, $hashed_password, $phone);
        $stmt->fetch();

        if (password_verify($password, $hashed_password)) {
            $_SESSION['client_id'] = $client_id;
            $_SESSION['name'] = $db_name;
            
            // Redirect to send_otp.php
            header("Location: send_otp.php");
            exit;
        } else {
            echo "<script>alert('Incorrect password. Please try again.'); window.location.href='user_login.php';</script>";
        }
    } else {
        echo "<script>alert('User not found. Please sign up.'); window.location.href='signup.php';</script>";
    }

    $stmt->close();
    $mysqli->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Banking App</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <script>
        function refreshCaptcha() {
            document.getElementById('captcha_img').src = 'user_login.php?captcha=1&rand=' + Math.random();
        }
    </script>
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
                    <h3>PACE BANK </h3>
                </div>
                <div class="card-body">
                    <form action="" method="POST">
                        <div class="mb-3">
                            <label for="name" class="form-label">User Name</label>
                            <input type="text" id="name" name="name" class="form-control" placeholder="Enter your username" required>
                        </div>

                        <div class="mb-3">
                            <label for="password" class="form-label">Password</label>
                            <input type="password" id="password" name="password" class="form-control" placeholder="Enter your password" required>
                        </div>

                        <div class="mb-3">
                            <label for="captcha_code" class="form-label">Enter CAPTCHA</label>
                            <div class="d-flex">
                                <input type="text" id="captcha_code" name="captcha_code" class="form-control me-2" placeholder="Enter CAPTCHA" required>
                                <img id="captcha_img" src="user_login.php?captcha=1" alt="CAPTCHA">
                                <button type="button" class="btn btn-outline-secondary ms-2" onclick="refreshCaptcha()">⟳</button>
                            </div>
                        </div>
                        <button type="submit" class="btn btn-primary w-100">Login</button>
                        <div class="text-center mt-3">
                            <a href="../index.php">Forgot your password?</a>
                        </div>
                    
                    <div class="text-center mt-3">
                        Don't have an account? <a href="user_signup.php">Sign Up Here</a>.
                    </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

</body>
</html>
