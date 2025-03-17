<?php
session_start();
include('conf/config.php');
include('conf/check_login.php');
check_login(); // Ensure the user is logged in

$staff_id = $_SESSION['staff_id']; // Get logged-in staff ID

// Add client to the database
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $client_name = $_POST['client_name'];
    $contact = $_POST['contact'];
    $email = $_POST['email'];
    $address = $_POST['address'];
    $national_id = $_POST['national_id'];
    $password = $_POST['password'];

    $hashed_password = password_hash($password, PASSWORD_DEFAULT); // Secure password hash

    // Insert client details into the 'clients' table
    $sql = "INSERT INTO clients (name, national_id, phone, address, email, password) 
            VALUES (?, ?, ?, ?, ?, ?)";
    $stmt = $mysqli->prepare($sql);
    $stmt->bind_param("ssssss", $client_name, $national_id, $contact, $address, $email, $hashed_password);

    if ($stmt->execute()) {
        echo "<script>alert('Client account added successfully');</script>";
    } else {
        echo "Error: " . $stmt->error;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create Client Account</title>
    <link rel="stylesheet" href="staff_add_client.css"> <!-- Custom CSS -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
</head>
<style>
.logo {
    display: flex;
    align-items: center;
    font-size: 20px;
    font-weight: bold;
    color: white;
}

.logo img {
    width: 60px; /* Adjust as needed */
    height: auto;
    margin-right: 8px; /* Space between image and text */
}
</style>
<body>
    <div class="container">
    <div class="sidebar">
    <div class="logo">
    <img src="../IMAGES/logo.PNG" alt="PaceBank Logo">
    PaceBank
</div>
    <hr>
    <ul class="nav">
        <li><a href="../staff/staff_dashboard.php"><i class="fas fa-tachometer-alt"></i> Dashboard</a></li>
        <li><a href="../staff/staff_profile.php"><i class="fas fa-user-circle"></i> Profile</a></li>
        <li class="dropdown">
            <a href="#"><i class="fas fa-users"></i> Clients ▾</a>
            <ul class="dropdown-menu">
                <li><a href="../staff/staff_add_client.php"><i class="fas fa-user-plus"></i> Add Client</a></li>
                <li><a href="../staff/staff_manage_client.php"><i class="fas fa-user-cog"></i> Manage Client</a></li>
            </ul>
        </li>
        <li class="dropdown">
            <a href="#"><i class="fas fa-university"></i> Accounts ▾</a>
            <ul class="dropdown-menu">
                <li><a href="#"><i class="fas fa-plus-circle"></i> Add Account type</a></li>
                <li><a href="#"><i class="fas fa-edit"></i> Manage Account type</a></li>
                <li><a href="#"><i class="fas fa-folder-plus"></i> Open Acc</a></li>
                <li><a href="#"><i class="fas fa-folder-open"></i> Manage Acc openings</a></li>
            </ul>
        </li>
        <li class="dropdown">
            <a href="#"><i class="fas fa-dollar-sign"></i> Finances ▾</a>
            <ul class="dropdown-menu">
                <li><a href="#"><i class="fas fa-piggy-bank"></i> Deposits</a></li>
                <li><a href="#"><i class="fas fa-wallet"></i> Withdrawals</a></li>
                <li><a href="#"><i class="fas fa-exchange-alt"></i> Transfers</a></li>
                <li><a href="#"><i class="fas fa-balance-scale"></i> Balance Enquiries</a></li>
            </ul>
        </li>
        <li><a href="../BACKEND/admintransactions.php"><i class="fas fa-money-check-alt"></i> Transactions</a></li>
        <h4>Advanced Modules</h4>
        <li class="dropdown">
            <a href="#"><i class="fas fa-file-alt"></i> Financial Reports ▾</a>
            <ul class="dropdown-menu">
                <li><a href="#"><i class="fas fa-file-invoice-dollar"></i> Deposits</a></li>
                <li><a href="#"><i class="fas fa-file-invoice-dollar"></i> Withdrawals</a></li>
                <li><a href="#"><i class="fas fa-file-invoice-dollar"></i> Transfers</a></li>
            </ul>
        </li>
        <li><a href="#"><i class="fas fa-sign-out-alt"></i> Log Out</a></li>
    </ul>
</div>

        <div class="main-content">
            <header>
                <div class="page-title">Create Client Account</div>
                <div class="breadcrumbs">Dashboard / Clients / Add</div>
            </header>

            <div class="form-section">
                <h2>Create Client Account</h2>
                <form method="post" action="">
                    <div class="form-group">
                        <label for="client_name">Client Name:</label>
                        <input type="text" name="client_name" required>
                    </div>

                    <div class="form-group">
                        <label for="contact">Contact:</label>
                        <input type="text" name="contact" required>
                    </div>

                    <div class="form-group">
                        <label for="email">Email:</label>
                        <input type="email" name="email" required>
                    </div>

                    <div class="form-group">
                        <label for="national_id">National ID No.:</label>
                        <input type="text" name="national_id" required>
                    </div>

                    <div class="form-group">
                        <label for="password">Password:</label>
                        <input type="password" name="password" required>
                    </div>

                    <div class="form-group">
                        <label for="address">Address:</label>
                        <input type="text" name="address" required>
                    </div>

                    <button type="submit">Add Client</button>
                </form>
            </div>
        </div>
    </div>

</body>
</html>
