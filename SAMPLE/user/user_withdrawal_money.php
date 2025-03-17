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

// Fetch account details (excluding balance)
$stmt = $mysqli->prepare("SELECT account_id, account_number, acc_name, acc_type, pin, client_id, client_name, client_national_id, client_phoneno FROM bankaccounts WHERE account_id = ?");
if (!$stmt) {
    die("SQL Error (Fetching account): " . $mysqli->error);
}
$stmt->bind_param("i", $account_id);
$stmt->execute();
$result = $stmt->get_result();
$account = $result->fetch_assoc();

// If no account found, stop execution
if (!$account) {
    die("Error: Account not found.");
}

// Handle withdrawal with PIN verification
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['withdraw'])) {
    $entered_pin = trim($_POST['pin']);
    $withdrawal_amt = isset($_POST['amount_withdrawn']) ? floatval($_POST['amount_withdrawn']) : 0;

    if ($entered_pin === strval($account['pin'])) {
        if ($withdrawal_amt > 0) {
            $tr_code = bin2hex(random_bytes(8));

            // Insert transaction record
            $stmt = $mysqli->prepare("INSERT INTO transactions 
                (tr_code, account_id, acc_name, account_number, acc_type, transaction_amt, tr_type, tr_status, client_id, client_name, client_national_id, client_phone, created_at) 
                VALUES (?, ?, ?, ?, ?, ?, 'Withdrawal', 'Completed', ?, ?, ?, ?, NOW())");

            if (!$stmt) {
                die("SQL Error (Transaction Insert): " . $mysqli->error);
            }

            $stmt->bind_param("sisssissss", 
                $tr_code, 
                $account['account_id'], 
                $account['acc_name'], 
                $account['account_number'], 
                $account['acc_type'], 
                $withdrawal_amt, 
                $account['client_id'], 
                $account['client_name'], 
                $account['client_national_id'], 
                $account['client_phoneno']
            );

            if ($stmt->execute()) {
                echo "<script>alert('Withdrawal Successful!'); window.location.href='client_dashboard.php';</script>";
            } else {
                echo "<script>alert('Withdrawal failed. Try again!');</script>";
            }
        } else {
            echo "<script>alert('Invalid withdrawal amount!');</script>";
        }
    } else {
        echo "<script>alert('Incorrect PIN! Try again.');</script>";
    }
}
// Handle setting a new PIN
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['set_pin'])) {
    $new_pin = trim($_POST['new_pin']);
    
    // Validate new PIN (must be 6 digits)
    if (preg_match('/^\d{6}$/', $new_pin)) {
        // Update account with the new PIN
        $stmt = $mysqli->prepare("UPDATE bankaccounts SET pin = ? WHERE account_id = ?");
        if (!$stmt) {
            die("SQL Error (Setting PIN): " . $mysqli->error);
        }
        $stmt->bind_param("si", $new_pin, $account_id);
        
        if ($stmt->execute()) {
            echo "<script>alert('PIN set successfully!'); window.location.href='client_dashboard.php';</script>";
        } else {
            echo "<script>alert('Failed to set PIN. Try again!');</script>";
        }
    } else {
        echo "<script>alert('Invalid PIN format! Must be 6 digits.');</script>";
    }
}

?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Withdraw Money</title>
    <link rel="stylesheet" href="user_deposit_money.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
</head>
<body>
    <?php include("sidebar.php"); ?>
    <h1 class="page-heading">Withdraw Money</h1>
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
        </div>

    <!-- Deposit Funds Button -->
<!-- Withdraw Funds Button -->
<div class="text-center mt-4">
    <button type="button" class="btn btn-primary px-4" onclick="openGatewayPopup()">Withdraw Funds</button>
    <?php if (!$account['pin']) { ?>
        <button type="button" class="btn btn-warning px-4" onclick="showSetPinPopup()">Set PIN</button>
    <?php } ?>
</div>

<!-- Enter PIN Popup -->
<div id="pinPopup" class="popup">
    <form method="post" id="withdrawForm">
        <label>Amount Withdrawn ($)</label>
        <input type="number" name="amount_withdrawn" required>
        <h3>Enter PIN</h3>
        <input type="password" name="pin" maxlength="6" pattern="\d{6}" required>
        <button type="submit" name="withdraw">Confirm</button>
        <button type="button" onclick="closePinPopup()">Cancel</button>
    </form>
</div>

<!-- Withdraw Success Popup -->
<div id="successPopup" class="popup">
    <h3>Withdrawal Successful!</h3>
    <p>Your funds have been withdrawn successfully.</p>
    <button onclick="redirectToDashboard()">OK</button>
</div>
<!-- Set PIN Popup -->
<div id="setPinPopup" class="popup">
    <form method="post" id="setPinForm">
        <h3>Set Your New PIN</h3>
        <label>Enter New PIN</label>
        <input type="password" name="new_pin" maxlength="6" pattern="\d{6}" required>
        <button type="submit" name="set_pin">Set PIN</button>
        <button type="button" onclick="closeSetPinPopup()">Cancel</button>
    </form>
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
    function openGatewayPopup() {
        let gatewayWindow = window.open("", "Payment Gateway", "width=400,height=300");

        if (!gatewayWindow || gatewayWindow.closed || typeof gatewayWindow.closed == 'undefined') {
            alert("Popup blocked! Please allow popups for this site.");
            return;
        }

        gatewayWindow.document.write(`
            <html>
            <head>
                <title>Processing Payment</title>
                <style>
                    body { text-align: center; font-family: Arial, sans-serif; padding: 50px; }
                    h2 { color: #007bff; }
                </style>
            </head>
            <body>
                <h2>Redirecting to Payment Gateway...</h2>
                <p>Please wait...</p>
            </body>
            </html>
        `);

        setTimeout(function() {
            gatewayWindow.close();
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

    document.getElementById('withdrawForm').addEventListener('submit', function(event) {
        event.preventDefault();

        // Allow normal form submission for database update
        this.submit();
    });
    function showSetPinPopup() {
    document.getElementById('setPinPopup').style.display = 'block';
}

function closeSetPinPopup() {
    document.getElementById('setPinPopup').style.display = 'none';
}

    
</script>

</body>
</html>
