<?php
$host = 'localhost';
$dbname = 'banking';
$username = 'root';
$password = '';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Fetch account details
    if (isset($_GET['id'])) {
        $account_id = $_GET['id'];
        $stmt = $pdo->prepare("SELECT * FROM bankaccounts WHERE account_id = ?");
        $stmt->execute([$account_id]);
        $account = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$account) {
            die("Account not found.");
        }

        // Ensure client_national_id and client_phone exist
        $client_national_id = $account['client_national_id'] ?? 'N/A';
        $client_phone = $account['client_phoneno'] ?? 'N/A';
    } else {
        die("Invalid request.");
    }

    // Deposit money
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $transaction_amt = $_POST['amount_deposited'];
        $tr_code = bin2hex(random_bytes(8)); // Generate random transaction code

        $stmt = $pdo->prepare("INSERT INTO transactions 
            (tr_code, account_id, acc_name, account_number, acc_type, acc_amount, tr_type, tr_status, client_id, client_name, client_national_id, transaction_amt, client_phone, created_at) 
            VALUES (?, ?, ?, ?, ?, ?, 'Deposit', 'Completed', ?, ?, ?, ?, ?, NOW())");

        $stmt->execute([
            $tr_code, $account['account_id'], $account['acc_name'], $account['account_number'], 
            $account['acc_type'], $account['amount'], $account['client_id'], $account['client_name'], 
            $client_national_id, $transaction_amt, $client_phone
        ]);

        echo "<script>alert('Money Deposited Successfully!'); window.location.href='manage_acc_openings.php';</script>";
        exit;
    }
} catch (PDOException $e) {
    die("Database Error: " . $e->getMessage());
}

// Generate transaction code
$transaction_code = bin2hex(random_bytes(8));
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Deposit Money</title>
    <link rel="stylesheet" href="manage_acc_openings.css">
    <link rel="stylesheet" href="../BACKEND/deposit_money.css">
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
<img src="../IMAGES/logo.PNG" alt="PaceBank Logo">PaceBank</div>
    <hr style="border: 1px solid black; width: 100%;">
    <ul class="nav">
        <li><a href="../FRONTEND/admin_dashboard.php"><i class="fas fa-tachometer-alt"></i> Dashboard</a></li>
        <li><a href="../BACKEND/admin_profile.php"><i class="fas fa-user-circle"></i> Profile</a></li>
        <li class="dropdown">
            <a href="#"><i class="fas fa-users"></i> Clients ▾</a>
            <ul class="dropdown-menu">
                <li><a href="../BACKEND/admin_add_client.php"><i class="fas fa-user-plus"></i> Add Client</a></li>
                <li><a href="../BACKEND/admin_manage_client.php"><i class="fas fa-user-cog"></i> Manage Client</a></li>
            </ul>
        </li>
        <li class="dropdown">
            <a href="#"><i class="fas fa-user-tie"></i> Staff ▾</a>
            <ul class="dropdown-menu">
                <li><a href="../BACKEND/admin_staff.php"><i class="fas fa-user-plus"></i> Add Staff</a></li>
                <li><a href="../BACKEND/admin_manage_staff.php"><i class="fas fa-user-cog"></i> Manage Staff</a></li>
            </ul>
        </li>
        <li class="dropdown">
            <a href="#"><i class="fas fa-university"></i> Accounts ▾</a>
            <ul class="dropdown-menu">
                <li><a href="../BACKEND/add_account_type.php"><i class="fas fa-plus-circle"></i> Add Account type</a></li>
                <li><a href="../BACKEND/manage_accounttype.php"><i class="fas fa-edit"></i> Manage Account type</a></li>
                <li><a href="../BACKEND/admin_open_account.php"><i class="fas fa-folder-plus"></i> Open Acc</a></li>
                <li><a href="../BACKEND/manage_acc_openings.php"><i class="fas fa-folder-open"></i> Manage Acc openings</a></li>
            </ul>
        </li>
        <li class="dropdown">
            <a href="#"><i class="fas fa-dollar-sign"></i> Finances ▾</a>
            <ul class="dropdown-menu">
                <li><a href="../BACKEND/pages_deposits.php"><i class="fas fa-piggy-bank"></i> Deposits</a></li>
                <li><a href="../BACKEND/pages_withdrawal.php"><i class="fas fa-wallet"></i> Withdrawals</a></li>
                <li><a href="../BACKEND/transfer_details.php"><i class="fas fa-exchange-alt"></i> Transfers</a></li>
                <li><a href="../BACKEND/balance_details.php"><i class="fas fa-balance-scale"></i> Balance Enquiries</a></li>
            </ul>
        </li>
        <li><a href="../BACKEND/admintransactions.php"><i class="fas fa-money-check-alt"></i> Transactions</a></li>
        <h4>Advanced Modules</h4>
        <li class="dropdown">
            <a href="#"><i class="fas fa-file-alt"></i> Financial Reports ▾</a>
            <ul class="dropdown-menu">
                <li><a href="../BACKEND/transaction_deposit.php"><i class="fas fa-file-invoice-dollar"></i> Deposits</a></li>
                <li><a href="../BACKEND/transaction_withdrawal.php"><i class="fas fa-file-invoice-dollar"></i> Withdrawals</a></li>
                <li><a href="../BACKEND/transaction_transfer.php"><i class="fas fa-file-invoice-dollar"></i> Transfers</a></li>
            </ul>
        </li>
        <li><a href="#"><i class="fas fa-cogs"></i> System Settings</a></li>
        <li><a href="../BACKEND/admin_logout.php"><i class="fas fa-sign-out-alt"></i> Log Out</a></li>
    </ul>
</div>

<div class="container">
    <h2>Deposit Money</h2>
    <form method="post">
        <label>Client Name</label>
        <input type="text" value="<?= htmlspecialchars($account['client_name']) ?>" readonly>

        <label>Client National ID No.</label>
        <input type="text" value="<?= htmlspecialchars($client_national_id) ?>" readonly>

        <label>Client Phone Number</label>
        <input type="text" value="<?= htmlspecialchars($client_phone) ?>" readonly>

        <label>Account Name</label>
        <input type="text" value="<?= htmlspecialchars($account['acc_name']) ?>" readonly>

        <label>Account Number</label>
        <input type="text" value="<?= htmlspecialchars($account['account_number']) ?>" readonly>

        <label>Account Type | Category</label>
        <input type="text" value="<?= htmlspecialchars($account['acc_type']) ?>" readonly>

        <label>Transaction Code</label>
        <input type="text" value="<?= htmlspecialchars($transaction_code) ?>" readonly>

        <label>Amount Deposited (₹)</label>
        <input type="number" name="amount_deposited" required>

        <button type="submit" class="deposit-btn">Deposit Funds</button>
    </form>
</div>

</body>
</html>
