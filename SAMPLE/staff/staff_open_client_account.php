<?php
session_start();
include('conf/config.php'); // Includes the $mysqli connection
include('conf/check_login.php');
check_login(); // Ensure the user is logged in

$staff_id = $_SESSION['staff_id']; // Get logged-in staff ID

// Check if client_id is set in the URL
if (isset($_GET['client_id']) && !empty($_GET['client_id'])) {
    $client_id = $_GET['client_id'];

    // Fetch client details
    $client_sql = "SELECT name, national_id, phone, address, email FROM clients WHERE client_id = ?";
    $stmt = $mysqli->prepare($client_sql);
    $stmt->bind_param("i", $client_id);
    $stmt->execute();
    $client_result = $stmt->get_result();

    if ($client_result->num_rows > 0) {
        $client = $client_result->fetch_assoc();
    } else {
        echo "<script>alert('No client found with the provided ID.'); window.history.back();</script>";
        exit;
    }
} else {
    echo "<script>alert('Client ID is not set.'); window.history.back();</script>";
    exit;
}

// Fetch available account types
$acc_types_sql = "SELECT acctype_id, name, rate FROM acc_types";
$acc_types_result = $mysqli->query($acc_types_sql);

// Handle form submission (open account)
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($client)) {
    $acc_name = $_POST['account_name'];
    $acc_type_id = $_POST['account_type'];
    $account_number = rand(1000000000, 9999999999);

    // Fetch selected account type details
    $acc_type_sql = "SELECT name, rate FROM acc_types WHERE acctype_id = ?";
    $stmt_acc = $mysqli->prepare($acc_type_sql);
    $stmt_acc->bind_param("i", $acc_type_id);
    $stmt_acc->execute();
    $acc_type_result = $stmt_acc->get_result();
    $acc_type = $acc_type_result->fetch_assoc();

    if (!$acc_type) {
        echo "<script>alert('Invalid account type selected.');</script>";
        exit;
    }

    // Restrict staff from opening Organization Account
    if ($acc_type['name'] === "Organization Account") {
        echo "<script>alert('Only the Admin can open an Organization Account.');</script>";
        exit;
    }

    // Insert into bankaccounts table
    $insert_sql = "INSERT INTO bankaccounts 
        (acc_name, account_number, acc_type, acc_rates, status, client_id, client_name, client_national_id, client_phoneno, client_email, client_adr, created_at) 
        VALUES (?, ?, ?, ?, 'Active', ?, ?, ?, ?, ?, ?, NOW())";

    $stmt_insert = $mysqli->prepare($insert_sql);
    $stmt_insert->bind_param(
        "sssdisssss",
        $acc_name,
        $account_number,
        $acc_type['name'],
        $acc_type['rate'],
        $client_id,
        $client['name'],
        $client['national_id'],
        $client['phone'],
        $client['email'],
        $client['address']
    );

    if ($stmt_insert->execute()) {
        echo "<script>alert('Account opened successfully!');</script>";
    } else {
        echo "<script>alert('Error opening account: " . $stmt_insert->error . "');</script>";
    }
}
?>



<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Open New iBanking Account</title>
    <link rel="stylesheet" href="staff_open_client_account.css"> <!-- Link to external CSS -->
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
<div class="container">
    <h2>Open <?php echo isset($client['name']) ? $client['name'] : 'iBanking'; ?> Account</h2>
    <div class="account-form">
        <form action="" method="POST">
            <div class="form-row">
                <label for="client_name">Client Name</label>
                <input type="text" id="client_name" name="client_name" value="<?php echo isset($client['name']) ? $client['name'] : ''; ?>" disabled>
            </div>
            <div class="form-row">
                <label for="client_number">Client Number</label>
                <input type="text" id="client_number" value="iBank-CLIENT-<?php echo rand(1000, 9999); ?>" disabled>
            </div>
            <div class="form-row">
                <label for="client_national_id">Client National ID No.</label>
                <input type="text" id="client_national_id" value="<?php echo isset($client['national_id']) ? $client['national_id'] : ''; ?>" disabled>
            </div>
            <div class="form-row">
                <label for="client_address">Client Address</label>
                <input type="text" id="client_address" value="<?php echo isset($client['address']) ? $client['address'] : ''; ?>" disabled>
            </div>
            <div class="form-row">
                <label for="client_email">Client Email</label>
                <input type="email" id="client_email" value="<?php echo isset($client['email']) ? $client['email'] : ''; ?>" disabled>
            </div>
            <div class="form-row">
                <label for="account_type">Account Type</label>
                <select name="account_type" id="account_type" required>
                    <option value="" disabled selected>Select Account Type</option>
                    <?php
                    while ($row = $acc_types_result->fetch_assoc()) {
                        echo "<option value='{$row['acctype_id']}'>{$row['name']}</option>";
                    }
                    ?>
                </select>
            </div>
            <div class="form-row">
                <label for="account_name">Account Name</label>
                <input type="text" name="account_name" id="account_name" required>
            </div>
            <div class="form-row">
                <label for="account_number">Account Number</label>
                <input type="text" id="account_number" value="<?php echo rand(1000000000, 9999999999); ?>" disabled>
            </div>
            <div class="form-row">
                <button type="submit" class="open-account-btn">Open iBanking Account</button>
            </div>
        </form>
    </div>
</div>
</body>
</html>

<?php
$mysqli->close();
?>
