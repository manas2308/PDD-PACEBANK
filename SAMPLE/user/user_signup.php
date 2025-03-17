<?php
include 'conf/config.php'; // Database connection

// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = $_POST['name'];
    $national_id = $_POST['national_id'];
    $phone = $_POST['phone'];
    $address = $_POST['address'];
    $email = $_POST['email'];
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];

    // Validate phone number (10 digits, Indian format)
    if (!preg_match('/^[6-9]\d{9}$/', $phone)) {
        echo "<script>alert('Invalid phone number. It must be a 10-digit Indian number starting with 6-9.'); window.location.href='signup.php';</script>";
        exit;
    }

    // Validate email format (@gmail.com only)
    if (!filter_var($email, FILTER_VALIDATE_EMAIL) || !str_ends_with($email, '@gmail.com')) {
        echo "<script>alert('Invalid email format. Only Gmail accounts are allowed.'); window.location.href='signup.php';</script>";
        exit;
    }

    // Validate strong password
    if (!preg_match('/^(?=.*[A-Z])(?=.*[a-z])(?=.*\d)(?=.*[@$!%*?&])[A-Za-z\d@$!%*?&]{8,}$/', $password)) {
        echo "<script>alert('Password must be at least 8 characters long and include an uppercase letter, lowercase letter, number, and special character.'); window.location.href='signup.php';</script>";
        exit;
    }

    // Check if passwords match
    if ($password !== $confirm_password) {
        echo "<script>alert('Passwords do not match!'); window.location.href='signup.php';</script>";
        exit;
    }

    // Hash the password
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);

    // Check if national_id or email already exists
    $check_sql = "SELECT * FROM clients WHERE national_id = ? OR email = ?";
    $stmt = $mysqli->prepare($check_sql);
    $stmt->bind_param("ss", $national_id, $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        echo "<script>alert('National ID or Email is already registered.'); window.location.href='signup.php';</script>";
    } else {
        // Insert data into clients table
        $insert_sql = "INSERT INTO clients (name, national_id, phone, address, email, password) 
                       VALUES (?, ?, ?, ?, ?, ?)";
        $stmt = $mysqli->prepare($insert_sql);
        $stmt->bind_param("ssssss", $name, $national_id, $phone, $address, $email, $hashed_password);

        if ($stmt->execute()) {
            echo "<script>alert('Signed up successfully! Redirecting to login.'); window.location.href='user_login.php';</script>";
            exit;
        } else {
            echo "<script>alert('Error: Unable to register.'); window.location.href='signup.php';</script>";
        }
    }

    // Close connection
    $stmt->close();
    $mysqli->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sign Up - Banking App</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
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
<body class="bg-light">
<a href="../index.html" class="corner-image">
        <img src="../IMAGES/download.png" alt="Logo">
    </a>
<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="card shadow">
                <div class="card-header bg-primary text-white text-center">
                    <h3>REGISTER HERE</h3>
                </div>
                <div class="card-body">
                    <form action="" method="POST">
                        <div class="mb-3">
                            <label for="name" class="form-label">User Name</label>
                            <input type="text" id="name" name="name" class="form-control" placeholder="Enter your user name" required>
                        </div>

                        <div class="mb-3">
                            <label for="national_id" class="form-label">Aadhar Number</label>
                            <input type="text" id="national_id" name="national_id" class="form-control" placeholder="Enter your Aadhar" required>
                        </div>

                        <div class="mb-3">
                            <label for="phone" class="form-label">Phone Number</label>
                            <input type="tel" id="phone" name="phone" class="form-control" placeholder="Enter your phone number" required>
                        </div>

                        <div class="mb-3">
                            <label for="address" class="form-label">Address</label>
                            <input type="text" id="address" name="address" class="form-control" placeholder="Enter your address" required>
                        </div>

                        <div class="mb-3">
                            <label for="email" class="form-label">Email</label>
                            <input type="email" id="email" name="email" class="form-control" placeholder="Enter your email" required>
                        </div>

                        <div class="mb-3">
                            <label for="password" class="form-label">Password</label>
                            <input type="password" id="password" name="password" class="form-control" placeholder="Enter your password" required>
                        </div>

                        <div class="mb-3">
                            <label for="confirm-password" class="form-label">Confirm Password</label>
                            <input type="password" id="confirm-password" name="confirm_password" class="form-control" placeholder="Re-enter your password" required>
                        </div>

                        <button type="submit" class="btn btn-primary w-100">Sign Up</button>
                    </form>
                    <div class="text-center mt-3">
                        Already have an account? <a href="user_login.php">Login here</a>.
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

</body>
</html>