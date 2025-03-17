<?php
session_start();
include('conf/config.php'); // Configuration file for DB connection

if (isset($_POST['login'])) {
    $email = $_POST['email'];
    $password = $_POST['password']; // Normal password, no hashing

    $stmt = $mysqli->prepare("SELECT staff_id, name FROM staff WHERE email = ? AND password = ?");
    $stmt->bind_param('ss', $email, $password);
    $stmt->execute();
    $stmt->bind_result($staff_id, $name);
    $rs = $stmt->fetch();

    if ($rs) {
        $_SESSION['staff_id'] = $staff_id;
        $_SESSION['staff_name'] = $name;
        header("Location: staff_dashboard.php");
        exit;
    } else {
        $err = "Access Denied! Invalid Email or Password.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="staff_login.css">
    <title>Staff Login</title>
</head>
<style>
      /* Position the image in the top-left corner */
      .corner-image {
            position: absolute;
            top: 10px;
            left: 10px;
        }

        .corner-image img {
            width: 50px;  /* Adjust image size */
            height: auto;
        }
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
<body>
<a href="../index.html" class="corner-image">
        <img src="../IMAGES/download.png" alt="Logo">
    </a>

    <div class="login-container">
        <h1>Staff Login</h1>
        <?php if (!empty($err)) { echo "<p class='error'>$err</p>"; } ?>
        <form method="post" action="">
            <label for="email">Email</label>
            <input type="email" id="email" name="email" placeholder="Enter your email" required>

            <label for="password">Password</label>
            <input type="password" id="password" name="password" placeholder="Enter your password" required>

            <button type="submit" name="login">Login</button>
        </form>
    </div>
</body>
</html>
