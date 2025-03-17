<?php
require_once 'conf/config.php';
require_once 'conf/check_login.php';

// Fetch account details
if (isset($_GET['id'])) {
    $account_id = $_GET['id'];
    $stmt = $mysqli->prepare("SELECT * FROM bankaccounts WHERE account_id = ?");
    $stmt->bind_param("i", $account_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $account = $result->fetch_assoc();

    if (!$account) {
        die("Account not found.");
    }

    // Ensure client_national_id and client_phone exist
    $client_national_id = $account['client_national_id'] ?? 'N/A';
    $client_phone = $account['client_phoneno'] ?? 'N/A';
} else {
    die("Invalid request.");
}

// Withdraw money
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $transaction_amt = $_POST['amount_withdrawn'];
    $tr_code = bin2hex(random_bytes(8)); // Generate random transaction code

    $stmt = $mysqli->prepare("INSERT INTO transactions 
    (tr_code, account_id, acc_name, account_number, acc_type, acc_amount, tr_type, tr_status, client_id, client_name, client_national_id, transaction_amt, client_phone, created_at) 
    VALUES (?, ?, ?, ?, ?, ?, 'Withdrawal', 'Completed', ?, ?, ?, ?, ?, NOW())");

$stmt = $mysqli->prepare("INSERT INTO transactions 
    (tr_code, account_id, acc_name, account_number, acc_type, acc_amount, tr_type, tr_status, client_id, client_name, client_national_id, transaction_amt, client_phone, created_at) 
    VALUES (?, ?, ?, ?, ?, ?, 'Withdrawal', 'Completed', ?, ?, ?, ?, ?, NOW())");

$stmt->bind_param("sissdisssds", 
    $tr_code,                      // s (string) - Transaction Code
    $account['account_id'],         // i (integer) - Account ID
    $account['acc_name'],           // s (string) - Account Name
    $account['account_number'],     // s (string) - Account Number
    $account['acc_type'],           // s (string) - Account Type
    $account['amount'],             // d (double) - Account Amount
    $account['client_id'],          // i (integer) - Client ID
    $account['client_name'],        // s (string) - Client Name
    $client_national_id,            // s (string) - Client National ID
    $transaction_amt,               // d (double) - Transaction Amount
    $client_phone                   // s (string) - Client Phone
);

$stmt->execute();



    echo "<script>alert('Money Withdrawn Successfully!'); window.location.href='staff_withdrawal.php';</script>";
    exit;
}

// Generate transaction code
$transaction_code = bin2hex(random_bytes(8));
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Withdraw Money</title>
    <link rel="stylesheet" href="../BACKEND/withdraw_money.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
</head>
<body>
<?php include "staff_sidebar.php" ; ?>
<div class="container">
    <h2>Withdraw Money</h2>
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

        <label>Amount Withdrawn ($)</label>
        <input type="number" name="amount_withdrawn" required>

        <button type="submit" class="withdraw-btn">Withdraw Funds</button>
    </form>
</div>

</body>
</html>
