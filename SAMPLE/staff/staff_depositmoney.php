<?php
session_start();
require_once 'conf/config.php';
require_once 'conf/check_login.php';
check_login();
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

    $client_national_id = $account['client_national_id'] ?? 'N/A';
    $client_phone = $account['client_phoneno'] ?? 'N/A';
} else {
    die("Invalid request.");
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Ensure the form data is sanitized
    $transaction_amt = $_POST['amount_deposited'];
    $tr_code = bin2hex(random_bytes(8));

    // Assuming these are the correct transaction type and status (modify if needed)
    $tr_type = 'Deposit';  // Modify as needed based on your logic
    $tr_status = 'Completed';  // Modify as needed

    // Ensure the transaction details are being properly inserted into the database
    $stmt = $mysqli->prepare("INSERT INTO transactions 
    (tr_code, account_id, acc_name, account_number, acc_type, transaction_amt, tr_type, tr_status, client_id, client_name, client_national_id, client_phone, created_at) 
    VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, NOW())");

    $stmt->bind_param("sissssssisss", $tr_code, $account['account_id'], $account['acc_name'], $account['account_number'], 
    $account['acc_type'], $transaction_amt, $tr_type, $tr_status, $account['client_id'], $account['client_name'], 
    $account['client_national_id'], $account['client_phoneno']);

    if ($stmt->execute()) {
        echo "<script>alert('Money Deposited Successfully!'); window.location.href='staff_deposits.php';</script>";
    } else {
        // Output any error that occurred during query execution
        echo "Error: " . $stmt->error;
    }
    exit;
}

$transaction_code = bin2hex(random_bytes(8));
?>

<!-- HTML code remains the same -->


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Deposit Money</title>
    <link rel="stylesheet" href="../BACKEND/deposit_money.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
</head>
<body>
<?php include "staff_sidebar.php" ; ?>
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

        <label>Amount Deposited </label>
        <input type="number" name="amount_deposited" required>

        <button type="submit" class="deposit-btn">Deposit Funds</button>
    </form>
</div>
</body>
</html>
