<?php
session_start();
include 'conf/config.php';
include 'conf/check_login.php';

check_login();

// Fetch the client details based on session client_id
$client_id = $_SESSION['client_id'];
$sql = "SELECT name, national_id, phone, address, email FROM clients WHERE client_id = ?";
$stmt = $mysqli->prepare($sql);

if (!$stmt) {
    die("Error preparing SQL statement: " . $mysqli->error);
}

$stmt->bind_param("i", $client_id);
$stmt->execute();
$result = $stmt->get_result();
$client = $result->fetch_assoc();
$stmt->close();

// Handle profile update
if (isset($_POST['update_profile'])) {
    $name = $_POST['name'];
    $national_id = $_POST['national_id'];
    $phone = $_POST['phone'];
    $address = $_POST['address'];
    $email = $_POST['email'];

    $update_sql = "UPDATE clients SET name=?, national_id=?, phone=?, address=?, email=? WHERE client_id=?";
    $stmt = $mysqli->prepare($update_sql);

    if (!$stmt) {
        die("Error preparing update statement: " . $mysqli->error);
    }

    $stmt->bind_param("sssssi", $name, $national_id, $phone, $address, $email, $client_id);

    if ($stmt->execute()) {
        echo "<script>alert('Profile updated successfully');</script>";
        header("Refresh:0");
    } else {
        echo "Error updating profile: " . $mysqli->error;
    }
    $stmt->close();
}

// Handle password change
if (isset($_POST['change_password'])) {
    $old_password = $_POST['old_password'];
    $new_password = $_POST['new_password'];
    $confirm_password = $_POST['confirm_password'];

    // Fetch current password from DB
    $password_sql = "SELECT password FROM clients WHERE client_id = ?";
    $stmt = $mysqli->prepare($password_sql);
    $stmt->bind_param("i", $client_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $client_password = $result->fetch_assoc();
    $stmt->close();

    if (password_verify($old_password, $client_password['password'])) {
        if ($new_password === $confirm_password) {
            $new_hashed_password = password_hash($new_password, PASSWORD_BCRYPT);
            $update_password_sql = "UPDATE clients SET password=? WHERE client_id=?";
            $stmt = $mysqli->prepare($update_password_sql);

            if (!$stmt) {
                die("Error preparing password update statement: " . $mysqli->error);
            }

            $stmt->bind_param("si", $new_hashed_password, $client_id);

            if ($stmt->execute()) {
                echo "<script>alert('Password changed successfully');</script>";
            } else {
                echo "Error changing password: " . $mysqli->error;
            }
            $stmt->close();
        } else {
            echo "<script>alert('New passwords do not match');</script>";
        }
    } else {
        echo "<script>alert('Old password is incorrect');</script>";
    }
}

$mysqli->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Client Profile</title>
    <link rel="stylesheet" href="user_profile.css">
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
<div class="mobile-menu" onclick="toggleSidebar()">☰</div>

<div class="sidebar" id="sidebar">
<div class="logo">
    <img src="../IMAGES/logo.PNG" alt="PaceBank Logo">
    PaceBank
</div>

    <hr style="border: 1px solid black; width: 100%;">
    <ul class="nav">
        <li><a href="../user/client_dashboard.php"><i class="fas fa-tachometer-alt"></i> Dashboard</a></li>
        <li><a href="../user/user_profile.php"><i class="fas fa-user-circle"></i> Profile</a></li>
        <li class="dropdown">
            <a href="#"><i class="fas fa-university"></i> Accounts ▾</a>
            <ul class="dropdown-menu">
                <li><a href="../user/client_open_account.php"><i class="fas fa-folder-plus"></i> Open Acc</a></li>
                <li><a href="../user/user_accounts.php"><i class="fas fa-folder-open"></i> My Accounts</a></li>
            </ul>
        </li>
        <li class="dropdown">
            <a href="#"><i class="fas fa-dollar-sign"></i> Finances ▾</a>
            <ul class="dropdown-menu">
                <li><a href="../user/user_withdrawal.php"><i class="fas fa-wallet"></i> Withdrawals</a></li>
                <li><a href="../user/user_transfer.php"><i class="fas fa-exchange-alt"></i> Transfers</a></li>
                <li><a href="../user/user_balance.php"><i class="fas fa-balance-scale"></i> Balance Enquiries</a></li>
            </ul>
        </li>
        <li><a href="../user/user_transaction.php"><i class="fas fa-money-check-alt"></i> Transactions</a></li>
        <li class="dropdown">
            <br>
            <h4>Advanced Modules</h4>
            <br>
            <a href="#"><i class="fas fa-file-alt"></i> Financial Reports ▾</a>
            <ul class="dropdown-menu">
                <li><a href="../user/transaction_userdeposit.php"><i class="fas fa-file-invoice-dollar"></i> Deposits</a></li>
                <li><a href="../user/transaction_userwithdrawal.php"><i class="fas fa-file-invoice-dollar"></i> Withdrawals</a></li>
                <li><a href="../user/transaction_usertransfer.php"><i class="fas fa-file-invoice-dollar"></i> Transfers</a></li>
            </ul>
        </li>
        <li><a href="../user/limit_check.php"><i class="fas fa-user-circle"></i> Limit Check</a></li>
        <li><a href="../user/user_logout.php"><i class="fas fa-sign-out-alt"></i> Log Out</a></li>
    </ul>
</div>

<div class="wrapper">
    <div class="main-content">
        <header>
            <h2>Client Profile</h2>
            <p class="breadcrumbs">Dashboard / Profile</p>
        </header>

        <div class="profile-section">
            <form method="post" action="">
                <div class="update-section">
                    <h3>Update Profile</h3>
                    <label for="name">Name:</label>
                    <input type="text" name="name" value="<?php echo htmlspecialchars($client['name'] ?? ''); ?>" required>
                    
                    <label for="national_id">National ID:</label>
                    <input type="text" name="national_id" value="<?php echo htmlspecialchars($client['national_id'] ?? ''); ?>" required>
                    
                    <label for="phone">Phone:</label>
                    <input type="text" name="phone" value="<?php echo htmlspecialchars($client['phone'] ?? ''); ?>" required>
                    
                    <label for="address">Address:</label>
                    <input type="text" name="address" value="<?php echo htmlspecialchars($client['address'] ?? ''); ?>" required>
                    
                    <label for="email">Email:</label>
                    <input type="email" name="email" value="<?php echo htmlspecialchars($client['email'] ?? ''); ?>" required>
                    
                    <!---<button type="submit" name="update_profile" class="update-btn">Update Profile</button>--->
                </div>
            </form>

            <form method="post" action="">
                <div class="password-section">
                    <h3>Change Password</h3>
                    <label for="old_password">Old Password:</label>
                    <input type="password" name="old_password" required>
                    <label for="new_password">New Password:</label>
                    <input type="password" name="new_password" required>
                    <label for="confirm_password">Confirm New Password:</label>
                    <input type="password" name="confirm_password" required>
                    <button type="submit" name="change_password" class="change-btn">Change Password</button>
                </div>
            </form>
        </div>
    </div>
</div>
<script>
function toggleSidebar() {
    document.getElementById("sidebar").classList.toggle("active");
}

// Close sidebar when clicking outside
document.addEventListener("click", function (event) {
    let sidebar = document.getElementById("sidebar");
    let menuButton = document.querySelector(".mobile-menu");

    // Close sidebar if clicking outside of it
    if (!sidebar.contains(event.target) && !menuButton.contains(event.target)) {
        sidebar.classList.remove("active");
    }
});
</script>
</body>
</html>
