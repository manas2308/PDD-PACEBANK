<?php
session_start();
require_once 'conf/config.php';
require_once 'conf/check_login.php';
check_login();
// Fetch account details
if (isset($_GET['id'])) {
    $account_id = $_GET['id'];
    $stmt = $mysqli->prepare("SELECT client_name, client_national_id, client_phoneno, account_number, acc_type FROM bankaccounts WHERE account_id = ?");
    $stmt->bind_param("i", $account_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $account = $result->fetch_assoc();
}

// Fetch all account numbers for dropdown
$query = "SELECT account_number, acc_name FROM bankaccounts";
$result = $mysqli->query($query);
$accounts = $result->fetch_all(MYSQLI_ASSOC);

// Get sender's total balance
if (isset($account['account_number'])) {
    $stmt = $mysqli->prepare("SELECT 
                COALESCE(SUM(CASE WHEN tr_type IN ('Deposit', 'Credit') THEN transaction_amt ELSE 0 END), 0) -
                COALESCE(SUM(CASE WHEN tr_type IN ('Withdrawal', 'Transfer', 'Debit') THEN transaction_amt ELSE 0 END), 0) 
                AS balance FROM transactions WHERE account_number = ?");
    $stmt->bind_param("s", $account['account_number']);
    $stmt->execute();
    $stmt->bind_result($balance);
    $stmt->fetch();
    $stmt->close();
}

// Handle Transfer Request
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $tr_code = 'TR' . rand(100000, 999999);
    $sender_acc = $_POST['sender_acc'];
    $receiver_acc = $_POST['receiver_acc'];
    $receiver_name = $_POST['receiver_name'];
    $amount = $_POST['amount'];

    // Get sender details
    $stmt = $mysqli->prepare("SELECT account_id, acc_name, acc_type, client_id, client_name, client_national_id, client_phoneno FROM bankaccounts WHERE account_number = ?");
    $stmt->bind_param("s", $sender_acc);
    $stmt->execute();
    $sender = $stmt->get_result()->fetch_assoc();

    // Fetch updated balance before transaction
    $stmt = $mysqli->prepare("SELECT 
                COALESCE(SUM(CASE WHEN tr_type IN ('Deposit', 'Credit') THEN transaction_amt ELSE 0 END), 0) -
                COALESCE(SUM(CASE WHEN tr_type IN ('Withdrawal', 'Transfer', 'Debit') THEN transaction_amt ELSE 0 END), 0) 
                AS balance FROM transactions WHERE account_number = ?");
    $stmt->bind_param("s", $sender_acc);
    $stmt->execute();
    $stmt->bind_result($current_balance);
    $stmt->fetch();
    $stmt->close();

    if ($current_balance < $amount) {
        echo "<script>alert('Insufficient funds! Your current balance is $current_balance.'); window.location='transfer_details.php?id=$account_id';</script>";
        exit;
    }

    // Insert transaction for sender
    $stmt = $mysqli->prepare("INSERT INTO transactions (tr_code, account_id, acc_name, account_number, acc_type, acc_amount, tr_type, tr_status, client_id, client_name, client_national_id, transaction_amt, client_phone, receiving_acc_no, receiving_acc_name, receiving_acc_holder, created_at) VALUES (?, ?, ?, ?, ?, ?, 'Transfer', 'Completed', ?, ?, ?, ?, ?, ?, ?, ?, NOW())");
    $stmt->bind_param("sissssisssdsds", $tr_code, $sender['account_id'], $sender['acc_name'], $sender_acc, $sender['acc_type'], $amount, $sender['client_id'], $sender['client_name'], $sender['client_national_id'], $amount, $sender['client_phoneno'], $receiver_acc, $receiver_name, $receiver_name);
    $stmt->execute();

    // Fetch receiver account details
    $stmt = $mysqli->prepare("SELECT account_id, acc_name, acc_type, client_id, client_name, client_national_id, client_phoneno FROM bankaccounts WHERE account_number = ?");
    $stmt->bind_param("s", $receiver_acc);
    $stmt->execute();
    $receiver = $stmt->get_result()->fetch_assoc();

    // Insert transaction for receiver
    $stmt = $mysqli->prepare("INSERT INTO transactions (tr_code, account_id, acc_name, account_number, acc_type, acc_amount, tr_type, tr_status, client_id, client_name, client_national_id, transaction_amt, client_phone, receiving_acc_no, receiving_acc_name, receiving_acc_holder, created_at) VALUES (?, ?, ?, ?, ?, ?, 'Credit', 'Completed', ?, ?, ?, ?, ?, ?, ?, ?, NOW())");
    $stmt->bind_param("sissssisssdsds", $tr_code, $receiver['account_id'], $receiver['acc_name'], $receiver_acc, $receiver['acc_type'], $amount, $receiver['client_id'], $receiver['client_name'], $receiver['client_national_id'], $amount, $receiver['client_phoneno'], $sender_acc, $sender['acc_name'], $sender['client_name']);
    $stmt->execute();

    echo "<script>alert('Transaction Successful! Transaction Code: $tr_code'); window.location='staff_transferdetails.php?id=$account_id';</script>";
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Transfer Funds</title>
    <link rel="stylesheet" href="../BACKEND/transfer_money.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
</head>
<body>
<?php include "staff_sidebar.php" ; ?>
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
