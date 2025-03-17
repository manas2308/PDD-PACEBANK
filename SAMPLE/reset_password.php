<?php
require 'db.php'; // Ensure this contains the correct $mysqli connection

$error = "";
$success = "";

if (isset($_GET["key"]) && isset($_GET["email"]) && isset($_GET["action"]) && $_GET["action"] == "reset") {
    $key = $_GET["key"];
    $email = $_GET["email"];
    $curDate = date("Y-m-d H:i:s");

    // Validate reset key
    $stmt = $mysqli->prepare("SELECT * FROM `password_reset_temp` WHERE `reset_key`=? AND `email`=?");
    $stmt->bind_param("ss", $key, $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 0) {
        $error = '<h2>Invalid Link</h2>
                  <p>The link is invalid or has expired.</p>
                  <p><a href="forgot_password.php">Click here</a> to request a new password reset.</p>';
    } else {
        $row = $result->fetch_assoc();
        $expDate = $row['expDate'];
        if ($expDate < $curDate) {
            $error = "<h2>Link Expired</h2>
                      <p>The reset link has expired. Request a new one.</p>
                      <p><a href='forgot_password.php'>Reset Password</a></p>";
        }
    }
    $stmt->close();
}

// Handle password reset form submission
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["action"]) && $_POST["action"] == "update") {
    $pass1 = $_POST["pass1"];
    $pass2 = $_POST["pass2"];

    if (empty($pass1) || empty($pass2)) {
        $error = "<p>Both password fields are required.</p>";
    } elseif ($pass1 !== $pass2) {
        $error = "<p>Passwords do not match. Try again.</p>";
    } else {
        // Hash the password securely
        $hashedPassword = password_hash($pass1, PASSWORD_BCRYPT);

        // Update password in database
        $stmt = $mysqli->prepare("UPDATE `clients` SET `password`=? WHERE `email`=?");
        $stmt->bind_param("ss", $hashedPassword, $email);
        if ($stmt->execute()) {
            // Delete the reset request
            $stmt = $mysqli->prepare("DELETE FROM `password_reset_temp` WHERE `email`=?");
            $stmt->bind_param("s", $email);
            $stmt->execute();

            // Success message
            $success = "<p>Password updated successfully!</p>
                        <p><a href='../SAMPLE/user/user_login.php'>Click here</a> to Login.</p>";

            // Redirect after success (optional)
            header("refresh:3;url=login.php");
        } else {
            $error = "<p>Something went wrong. Please try again.</p>";
        }
        $stmt->close();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reset Password</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
</head>
<body class="d-flex justify-content-center align-items-center vh-100">

<div class="container">
    <div class="card p-4 shadow-lg">
        <h2 class="text-center">Reset Password</h2>

        <?php if (!empty($error)): ?>
            <div class="alert alert-danger"><?= $error ?></div>
        <?php endif; ?>

        <?php if (!empty($success)): ?>
            <div class="alert alert-success"><?= $success ?></div>
        <?php else: ?>
            <form method="post">
                <input type="hidden" name="action" value="update">
                
                <div class="mb-3">
                    <label><strong>New Password:</strong></label>
                    <input type="password" name="pass1" class="form-control" required>
                </div>

                <div class="mb-3">
                    <label><strong>Confirm Password:</strong></label>
                    <input type="password" name="pass2" class="form-control" required>
                </div>

                <button type="submit" class="btn btn-primary w-100">Reset Password</button>
            </form>
        <?php endif; ?>
    </div>
</div>

</body>
</html>
