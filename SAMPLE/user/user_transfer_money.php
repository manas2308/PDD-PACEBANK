<?php
session_start();
require_once 'conf/config.php';
require_once 'conf/check_login.php';
check_login();

if (!isset($_GET['id']) || empty($_GET['id'])) {
    die("Invalid request.");
}

$account_id = intval($_GET['id']);
$transaction_code = bin2hex(random_bytes(8));

// Fetch account details
$stmt = $mysqli->prepare("SELECT * FROM bankaccounts WHERE account_id = ?");
$stmt->bind_param("i", $account_id);
$stmt->execute();
$result = $stmt->get_result();
$account = $result->fetch_assoc();
if (!$account) {
    die("Account not found.");
}

// Calculate current balance
$stmt = $mysqli->prepare("SELECT 
        COALESCE(SUM(CASE WHEN tr_type = 'Deposit' THEN transaction_amt ELSE 0 END), 0) 
        - COALESCE(SUM(CASE WHEN tr_type = 'Withdrawal' THEN transaction_amt ELSE 0 END), 0) 
        - COALESCE(SUM(CASE WHEN tr_type = 'Transfer' THEN transaction_amt ELSE 0 END), 0) 
    AS balance 
    FROM transactions 
    WHERE account_id = ?");
$stmt->bind_param("i", $account_id);
$stmt->execute();
$result = $stmt->get_result();
$row = $result->fetch_assoc();
$balance = $row['balance'] ?? 0;

// Handle Transfer
if (isset($_POST['transfer'])) {
    $entered_pin = trim($_POST['pin']);
    $transfer_amt = isset($_POST['amount_transfer']) && is_numeric($_POST['amount_transfer']) ? floatval($_POST['amount_transfer']) : -1;
    
    $recipient_acc_no = trim($_POST['receiving_acc_no']);
    $recipient_acc_name = trim($_POST['receiving_acc_name']);
    $recipient_acc_holder = trim($_POST['receiving_acc_holder']);

    // Fetch stored PIN
    $stmt = $mysqli->prepare("SELECT pin FROM bankaccounts WHERE account_id = ?");
    $stmt->bind_param("i", $account_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $account_data = $result->fetch_assoc();
    $stored_pin = strval($account_data['pin']);

    if ($entered_pin === $stored_pin) {
        if ($transfer_amt > $balance) {
            echo "<script>alert('Insufficient funds!');</script>";
        } elseif ($transfer_amt <= 0) {
            echo "<script>alert('Invalid transfer amount! Please enter a valid amount.');</script>";
        } else {
            $tr_code = bin2hex(random_bytes(8));

            // Deduct from sender
            $stmt = $mysqli->prepare("INSERT INTO transactions (tr_code, account_id, acc_name, account_number, acc_type, transaction_amt, tr_type, tr_status, client_id, client_name, client_national_id, client_phone, receiving_acc_no, receiving_acc_name, receiving_acc_holder, created_at) 
                VALUES (?, ?, ?, ?, ?, ?, 'Transfer', 'Completed', ?, ?, ?, ?, ?, ?, ?, NOW())");
            $stmt->bind_param("sisssisssssss", $tr_code, $account_id, $account['acc_name'], $account['account_number'], $account['acc_type'], $transfer_amt, $account['client_id'], $account['client_name'], $account['client_national_id'], $account['client_phoneno'], $recipient_acc_no, $recipient_acc_name, $recipient_acc_holder);
            $stmt->execute();

            // Add to recipient (manually entered details)
            $stmt = $mysqli->prepare("INSERT INTO transactions (tr_code, account_id, acc_name, account_number, acc_type, transaction_amt, tr_type, tr_status, client_id, client_name, client_national_id, client_phone, receiving_acc_no, receiving_acc_name, receiving_acc_holder, created_at) 
                VALUES (?, ?, ?, ?, ?, ?, 'Transfer', 'Completed', ?, ?, ?, ?, ?, ?, ?, NOW())");
            $stmt->bind_param("sisssisssssss", $tr_code, $recipient_acc_no, $recipient_acc_name, $recipient_acc_no, $account['acc_type'], $transfer_amt, $account['client_id'], $recipient_acc_holder, $account['client_national_id'], $account['client_phoneno'], $recipient_acc_no, $recipient_acc_name, $recipient_acc_holder);
            if ($stmt->execute()) {
                echo "<script>alert('Transfer Successful!'); window.location.href='client_dashboard.php';</script>";
            } else {
                echo "<script>alert('Transfer failed. Try again!');</script>";
            }
        }
    } else {
        echo "<script>alert('Incorrect PIN! Try again.');</script>";
    }
}
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Transfer Money</title>
    <link rel="stylesheet" href="user_deposit_money.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
</head>
<body>
<?php include "sidebar.php"; ?>
<h1 class="page-heading">Transfer Money</h1>
    <div class="container mt-5 p-4 bg-white shadow rounded">
    <form method="post">
    <div class="form-alert">
    <p>Fill all fields</p>
</div>
        <div class="row">
            <div class="col-md-6">
                <div class="mb-3">
                    <label class="form-label fw-bold">Client Name</label>
                    <input type="text" class="form-control" value="<?= htmlspecialchars($account['client_name']) ?>" readonly>
                </div>
            </div>

            <div class="col-md-6">
                <div class="mb-3">
                    <label class="form-label fw-bold">Client National ID No.</label>
                    <input type="text" class="form-control" value="<?= htmlspecialchars($account['client_national_id']) ?>" readonly>
                </div>
            </div>

            <div class="col-md-6">
                <div class="mb-3">
                    <label class="form-label fw-bold">Client Phone Number</label>
                    <input type="text" class="form-control" value="<?= htmlspecialchars($account['client_phoneno']) ?>" readonly>
                </div>
            </div>

            <div class="col-md-6">
                <div class="mb-3">
                    <label class="form-label fw-bold">Account Name</label>
                    <input type="text" class="form-control" value="<?= htmlspecialchars($account['acc_name']) ?>" readonly>
                </div>
            </div>

            <div class="col-md-6">
                <div class="mb-3">
                    <label class="form-label fw-bold">Account Number</label>
                    <input type="text" class="form-control" value="<?= htmlspecialchars($account['account_number']) ?>" readonly>
                </div>
            </div>
            <div class="col-md-6">
                <div class="mb-3">
                    <label class="form-label fw-bold">Account Number</label>
                    <input type="text" class="form-control" value="<?= htmlspecialchars($account['acc_type']) ?>" readonly>
                </div>
            </div>
            <div class="col-md-6">
                <div class="mb-3">
                    <label class="form-label fw-bold">Transaction Code</label>
                    <input type="text" class="form-control" value="<?= htmlspecialchars($transaction_code) ?>" readonly>
                </div>
            </div>
            <div class="mb-3">
    <label class="form-label">Recipient Account Number</label>
    <input type="text" class="form-control" name="receiving_acc_no" required>
</div>

<div class="mb-3">
    <label class="form-label">Recipient Account Name</label>
    <input type="text" class="form-control" name="receiving_acc_name" required>
</div>

<div class="mb-3">
    <label class="form-label">Recipient Account Holder Name</label>
    <input type="text" class="form-control" name="receiving_acc_holder" required>
</div>



<!-- Hidden field to store the transfer amount -->
<input type="hidden" id="hidden_amount_transfer" name="hidden_amount_transfer">
        </div>
        <div class="text-center mt-4">
    <button type="button" class="btn btn-primary px-4" onclick="openTransferPopup()">Transfer Funds</button>
    <?php if (!$account['pin']) { ?>
        <button type="button" class="btn btn-warning px-4" onclick="showSetPinPopup()">Set PIN</button>
    <?php } ?>
</div>
<div id="pinPopup" class="popup">
    <form method="post" id="transferForm">
        <label>Amount to Transfer ($)</label>
        <input type="number" name="amount_transfer" required>
        <h3>Enter PIN</h3>
        <input type="password" name="pin" maxlength="6" pattern="\d{6}" required>
        <button type="submit" name="transfer">Confirm</button>
        <button type="button" onclick="closePinPopup()">Cancel</button>
    </form>
</div>

<!-- Transfer Success Popup -->
<div id="successPopup" class="popup">
    <h3>Transfer Successful!</h3>
    <p>Your funds have been transferred successfully.</p>
    <button onclick="redirectToDashboard()">OK</button>
</div>

<style>
    .popup { 
        display: none; 
        position: fixed; 
        left: 50%; 
        top: 50%; 
        transform: translate(-50%, -50%); 
        background: white; 
        padding: 20px; 
        box-shadow: 0px 0px 10px gray; 
        text-align: center;
    }
</style>

<script>
    function openTransferPopup() {
        let transferWindow = window.open("", "Processing Transfer", "width=400,height=300");

        if (!transferWindow || transferWindow.closed || typeof transferWindow.closed == 'undefined') {
            alert("Popup blocked! Please allow popups for this site.");
            return;
        }

        transferWindow.document.write(`
            <html>
            <head>
                <title>Processing Transfer</title>
                <style>
                    body { text-align: center; font-family: Arial, sans-serif; padding: 50px; }
                    h2 { color: #007bff; }
                </style>
            </head>
            <body>
                <h2>Processing Your Transfer...</h2>
                <p>Please wait...</p>
            </body>
            </html>
        `);

        setTimeout(function() {
            transferWindow.close();
            showPinPopup();
        }, 3000);
    }

    function showPinPopup() {
        document.getElementById('pinPopup').style.display = 'block';
    }

    function closePinPopup() {
        document.getElementById('pinPopup').style.display = 'none';
    }

    function redirectToDashboard() {
        window.location.href = 'client_dashboard.php';
    }

    document.getElementById('transferForm').addEventListener('submit', function(event) {
        event.preventDefault();

        // Allow normal form submission for database update
        this.submit();
    });
</script>

</body>
</html>
