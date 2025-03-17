<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Client Profile</title>
    <link rel="stylesheet" href="staff_client_profile.css">
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
<?php
// Start the session
session_start();

// Include database configuration and authentication check
include('conf/config.php');
include('conf/check_login.php');
check_login();
// Ensure the staff is logged in
if (!isset($_SESSION['staff_id'])) {
    header("Location: ../staff/staff_login.php");
    exit();
}

// Fetch specific client details based on client_id
$client_id = isset($_GET['client_id']) ? $_GET['client_id'] : null;

if ($client_id) {
    $sql = "SELECT client_id, name, national_id, phone, address, email, password FROM clients WHERE client_id = ?";
    $stmt = $mysqli->prepare($sql);
    $stmt->bind_param("i", $client_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result && $result->num_rows > 0) {
        $client = $result->fetch_assoc();
    } else {
        $client = null;
        echo "<script>alert('No client found with the provided client_id');</script>";
    }
} else {
    echo "<script>alert('No client_id provided');</script>";
}

// Handle Profile Update
if (isset($_POST['update_profile'])) {
    $name = $_POST['name'];
    $email = $_POST['email'];
    $phone = $_POST['phone'];
    $address = $_POST['address'];

    $update_sql = "UPDATE clients SET name=?, email=?, phone=?, address=? WHERE client_id=?";
    $stmt = $conn->prepare($update_sql);
    $stmt->bind_param("ssssi", $name, $email, $phone, $address, $client_id);
    
    if ($stmt->execute()) {
        echo "<script>alert('Client profile updated successfully');</script>";
        header("Refresh:0");
    } else {
        echo "Error updating client profile: " . $conn->error;
    }
}

// Handle Password Update without Hashing (Consider using password_hash in production)
if (isset($_POST['update_password'])) {
    $old_password = $_POST['old_password'];
    $new_password = $_POST['new_password'];
    $confirm_password = $_POST['confirm_password'];

    if ($old_password === $client['password']) {
        if ($new_password === $confirm_password) {
            $update_password_sql = "UPDATE clients SET password=? WHERE client_id=?";
            $stmt = $conn->prepare($update_password_sql);
            $stmt->bind_param("si", $new_password, $client_id);
            
            if ($stmt->execute()) {
                echo "<script>alert('Password updated successfully');</script>";
            } else {
                echo "Error updating password: " . $mysqli->error;
            }
        } else {
            echo "<script>alert('New passwords do not match. Please try again.');</script>";
        }
    } else {
        echo "<script>alert('Old password is incorrect. Please try again.');</script>";
    }
}

$mysqli->close();
?>


    <div class="container">
 
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

        <div class="main-content">
            <header>
                <div class="page-title">Manage Client Profile</div>
                <div class="breadcrumbs">Dashboard / Clients / Manage</div>
            </header>

            <div class="profile-section">
                <!-- Form for Updating Client Profile -->
                <form method="post" action="">
                    <div class="update-section">
                        <h2>Update Client Profile</h2>
                        <label for="name">Name:</label>
                        <input type="text" name="name" value="<?php echo $client['name']; ?>" required>

                        <label for="email">Email:</label>
                        <input type="email" name="email" value="<?php echo $client['email']; ?>" required>

                        <label for="phone">Phone:</label>
                        <input type="text" name="phone" value="<?php echo $client['phone']; ?>" required>

                        <label for="address">Address:</label>
                        <input type="text" name="address" value="<?php echo $client['address']; ?>" required>

                        <button type="submit" name="update_profile">Update Profile</button>
                    </div>
                </form>

                <!-- Form for Updating Password -->
                <form method="post" action="">
                    <div class="update-password-section">
                        <h2>Update Password</h2>
                        <label for="old_password">Old Password:</label>
                        <input type="password" name="old_password" required>

                        <label for="new_password">New Password:</label>
                        <input type="password" name="new_password" required>

                        <label for="confirm_password">Confirm New Password:</label>
                        <input type="password" name="confirm_password" required>

                        <button type="submit" name="update_password">Update Password</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</body>
</html>
