<?php
session_start();
include 'conf/config.php';
include 'conf/check_login.php';

check_login(); // Ensure the user is logged in

// Fetch the staff details based on session staff_id
$staff_id = $_SESSION['staff_id'];
$sql = "SELECT name, email, phone, password FROM staff WHERE staff_id = ?";
$stmt = $mysqli->prepare($sql);

if (!$stmt) {
    die("Error preparing SQL statement: " . $mysqli->error);
}

$stmt->bind_param("i", $staff_id);
$stmt->execute();
$result = $stmt->get_result();
$staff = $result->fetch_assoc();
$stmt->close();

// Handle profile update
if (isset($_POST['update_profile'])) {
    $name = $_POST['name'];
    $email = $_POST['email'];
    $phone = $_POST['phone'];

    $update_sql = "UPDATE staff SET name=?, email=?, phone=? WHERE staff_id=?";
    $stmt = $mysqli->prepare($update_sql);

    if (!$stmt) {
        die("Error preparing update statement: " . $mysqli->error);
    }

    $stmt->bind_param("sssi", $name, $email, $phone, $staff_id);

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

    if ($old_password === $staff['password']) { // Plain text comparison; consider hashing for security
        if ($new_password === $confirm_password) {
            $update_password_sql = "UPDATE staff SET password=? WHERE staff_id=?";
            $stmt = $mysqli->prepare($update_password_sql);

            if (!$stmt) {
                die("Error preparing password update statement: " . $mysqli->error);
            }

            $stmt->bind_param("si", $new_password, $staff_id);

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
    <title>Staff Profile</title>
    <link rel="stylesheet" href="staff_profile.css">
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
<div class="sidebar">
<div class="logo">
    <img src="../IMAGES/logo.PNG" alt="PaceBank Logo">
    PaceBank
</div>
    <hr style="border: 1px solid black; width: 100%;">
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
                <li><a href="../staff/staff_add_accounttype.php"><i class="fas fa-plus-circle"></i> Add Account type</a></li>
                <li><a href="../staff/staff_manage_accounttype.php"><i class="fas fa-edit"></i> Manage Account type</a></li>
                <li><a href="../staff/staff_open_account.php"><i class="fas fa-folder-plus"></i> Open Acc</a></li>
                <li><a href="../staff/staff_manage_accopenings.php"><i class="fas fa-folder-open"></i> Manage Acc openings</a></li>
            </ul>
        </li>
        <li class="dropdown">
            <a href="#"><i class="fas fa-dollar-sign"></i> Finances ▾</a>
            <ul class="dropdown-menu">
                <li><a href="../staff/staff_deposits.php"><i class="fas fa-piggy-bank"></i> Deposits</a></li>
                <li><a href="../staff/staff_withdrawal.php"><i class="fas fa-wallet"></i> Withdrawals</a></li>
                <li><a href="../staff/staff_transferdetails.php"><i class="fas fa-exchange-alt"></i> Transfers</a></li>
                <li><a href="../staff/staff_balances.php"><i class="fas fa-balance-scale"></i> Balance Enquiries</a></li>
            </ul>
        </li>
        <li><a href="../staff/staff_transactionengine.php"><i class="fas fa-money-check-alt"></i> Transactions</a></li>
        <h4>Advanced Modules</h4>
        <li class="dropdown">
            <a href="#"><i class="fas fa-file-alt"></i> Financial Reports ▾</a>
            <ul class="dropdown-menu">
                <li><a href="../staff/staff_transaction_deposit.php"><i class="fas fa-file-invoice-dollar"></i> Deposits</a></li>
                <li><a href="../staff/staff_transaction_withdrawal.php"><i class="fas fa-file-invoice-dollar"></i> Withdrawals</a></li>
                <li><a href="../staff/staff_transaction_transfer.php"><i class="fas fa-file-invoice-dollar"></i> Transfers</a></li>
            </ul>
        </li>
        <li><a href="../staff/staff_logout.php"><i class="fas fa-sign-out-alt"></i> Log Out</a></li>
    </ul>
</div>
<div class="wrapper">
    <div class="main-content">
        <header>
            <h2>Staff Profile</h2>
            <p class="breadcrumbs">Dashboard / Profile</p>
        </header>

        <div class="profile-section">
            <form method="post" action="">
                <div class="update-section">
                    <h3>Update Profile</h3>
                    <label for="name">Name:</label>
                    <input type="text" name="name" value="<?php echo htmlspecialchars($staff['name'] ?? ''); ?>" required>
                    <label for="email">Email:</label>
                    <input type="email" name="email" value="<?php echo htmlspecialchars($staff['email'] ?? ''); ?>" required>
                    <label for="phone">Phone:</label>
                    <input type="text" name="phone" value="<?php echo htmlspecialchars($staff['phone'] ?? ''); ?>" required>
                    <button type="submit" name="update_profile" class="update-btn">Update Profile</button>
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
</body>
</html>
