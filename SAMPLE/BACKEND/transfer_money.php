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
        $stmt = $pdo->prepare("SELECT client_name, client_national_id, client_phoneno, account_number, acc_type FROM bankaccounts WHERE account_id = ?");
        $stmt->execute([$account_id]);
        $account = $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // Fetch all account numbers for dropdown
    $stmt = $pdo->query("SELECT account_number, acc_name FROM bankaccounts");
    $accounts = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Get sender's total balance
    if (isset($account['account_number'])) {
        $stmt = $pdo->prepare("
            SELECT 
                COALESCE(SUM(CASE WHEN tr_type IN ('Deposit', 'Credit') THEN transaction_amt ELSE 0 END), 0) -
                COALESCE(SUM(CASE WHEN tr_type IN ('Withdrawal', 'Transfer', 'Debit') THEN transaction_amt ELSE 0 END), 0) 
                AS balance
            FROM transactions WHERE account_number = ?
        ");
        $stmt->execute([$account['account_number']]);
        $balance = $stmt->fetchColumn();
    }

    // Handle Transfer Request
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $tr_code = 'TR' . rand(100000, 999999);
        $sender_acc = $_POST['sender_acc'];
        $receiver_acc = $_POST['receiver_acc'];
        $receiver_name = $_POST['receiver_name'];
        $amount = $_POST['amount'];

        // Get sender details
        $stmt = $pdo->prepare("SELECT account_id, acc_name, acc_type, client_id, client_name, client_national_id, client_phoneno FROM bankaccounts WHERE account_number = ?");
        $stmt->execute([$sender_acc]);
        $sender = $stmt->fetch(PDO::FETCH_ASSOC);

        // Fetch updated balance before transaction
        $stmt = $pdo->prepare("
            SELECT 
                COALESCE(SUM(CASE WHEN tr_type IN ('Deposit', 'Credit') THEN transaction_amt ELSE 0 END), 0) -
                COALESCE(SUM(CASE WHEN tr_type IN ('Withdrawal', 'Transfer', 'Debit') THEN transaction_amt ELSE 0 END), 0) 
                AS balance
            FROM transactions WHERE account_number = ?
        ");
        $stmt->execute([$sender_acc]);
        $current_balance = $stmt->fetchColumn();

        // Check if sender has enough funds
        if ($current_balance < $amount) {
            echo "<script>alert('Insufficient funds! Your current balance is $current_balance.'); window.location='transfer_details.php?id=$account_id';</script>";
            exit;
        }

        // Insert transaction for sender
        $stmt = $pdo->prepare("INSERT INTO transactions (tr_code, account_id, acc_name, account_number, acc_type, acc_amount, tr_type, tr_status, client_id, client_name, client_national_id, transaction_amt, client_phone, receiving_acc_no, receiving_acc_name, receiving_acc_holder, created_at) 
        VALUES (?, ?, ?, ?, ?, ?, 'Transfer', 'Completed', ?, ?, ?, ?, ?, ?, ?, ?, NOW())");

        $stmt->execute([
            $tr_code, $sender['account_id'], $sender['acc_name'], $sender_acc, $sender['acc_type'], $amount,
            $sender['client_id'], $sender['client_name'], $sender['client_national_id'], $amount, 
            $sender['client_phoneno'], $receiver_acc, $receiver_name, $receiver_name
        ]);

        // Fetch receiver account details
        $stmt = $pdo->prepare("SELECT account_id, acc_name, acc_type, client_id, client_name, client_national_id, client_phoneno FROM bankaccounts WHERE account_number = ?");
        $stmt->execute([$receiver_acc]);
        $receiver = $stmt->fetch(PDO::FETCH_ASSOC);

        // Insert transaction for receiver
        $stmt = $pdo->prepare("INSERT INTO transactions (tr_code, account_id, acc_name, account_number, acc_type, acc_amount, tr_type, tr_status, client_id, client_name, client_national_id, transaction_amt, client_phone, receiving_acc_no, receiving_acc_name, receiving_acc_holder, created_at) 
        VALUES (?, ?, ?, ?, ?, ?, 'Credit', 'Completed', ?, ?, ?, ?, ?, ?, ?, ?, NOW())");

        $stmt->execute([
            $tr_code, $receiver['account_id'], $receiver['acc_name'], $receiver_acc, $receiver['acc_type'], $amount,
            $receiver['client_id'], $receiver['client_name'], $receiver['client_national_id'], $amount, 
            $receiver['client_phoneno'], $sender_acc, $sender['acc_name'], $sender['client_name']
        ]);

        echo "<script>alert('Transaction Successful! Transaction Code: $tr_code'); window.location='transfer_details.php?id=$account_id';</script>";
    }
} catch (PDOException $e) {
    die("Database Error: " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Transfer Funds</title>
    <link rel="stylesheet" href="manage_acc_openings.css">
    <link rel="stylesheet" href="transfer_money.css">
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
        <h1>Transfer Funds</h1>
        <form method="POST">
            <label>Client Name</label>
            <input type="text" name="client_name" value="<?= $account['client_name'] ?>" readonly>

            <label>Client National ID</label>
            <input type="text" name="client_national_id" value="<?= $account['client_national_id'] ?>" readonly>

            <label>Client Phone</label>
            <input type="text" name="client_phoneno" value="<?= $account['client_phoneno'] ?>" readonly>

            <label>Sender Account Number</label>
            <input type="text" name="sender_acc" value="<?= $account['account_number'] ?>" readonly>

            <label>Account Type</label>
            <input type="text" name="acc_type" value="<?= $account['acc_type'] ?>" readonly>

            <label>Current Balance</label>
            <input type="text" value="<?= number_format($balance, 2) ?>" readonly>

            <label>Receiving Account Number</label>
            <select name="receiver_acc" id="receiver_acc">
                <option value="">Select Account</option>
                <?php foreach ($accounts as $acc): ?>
                    <option value="<?= $acc['account_number'] ?>"><?= $acc['account_number'] ?></option>
                <?php endforeach; ?>
            </select>

            <label>Receiving Account Holder</label>
            <input type="text" name="receiver_name" id="receiver_name" readonly>

            <label>Transfer Amount</label>
            <input type="number" name="amount" required>

            <button type="submit">Transfer Funds</button>
        </form>
    </div>

    <script>
        document.getElementById("receiver_acc").addEventListener("change", function() {
            let accNumber = this.value;
            let accounts = <?= json_encode($accounts) ?>;
            let accountHolder = accounts.find(acc => acc.account_number === accNumber);
            document.getElementById("receiver_name").value = accountHolder ? accountHolder.acc_name : "";
        });
    </script>
</body>
</html>
