<?php
session_start();
include 'conf/config.php';
include 'conf/check_login.php';

check_login();

if (!isset($_GET['id'])) {
    die("Account ID not provided.");
}

$account_id = $_GET['id'];

// Fetch account details based on account_id
$query = "SELECT acc_name, account_number, acc_type, acc_rates, client_name, client_email, client_phoneno, client_national_id, amount, created_at FROM bankaccounts WHERE account_id = ?";
$stmt = $mysqli->prepare($query);
$stmt->bind_param('i', $account_id);
$stmt->execute();
$account_result = $stmt->get_result();
$account = $account_result->fetch_assoc();

if (!$account) {
    die("Account not found.");
}

$account_number = $account['account_number'];

// Fetch transactions related to this account
$transaction_query = "SELECT tr_type, transaction_amt FROM transactions WHERE account_number = ?";
$stmt = $mysqli->prepare($transaction_query);
$stmt->bind_param('s', $account_number);
$stmt->execute();
$transaction_result = $stmt->get_result();

$deposits = $withdrawals = $transfers = 0;
while ($transaction = $transaction_result->fetch_assoc()) {
    if ($transaction['tr_type'] === 'Deposit') {
        $deposits += $transaction['transaction_amt'];
    } elseif ($transaction['tr_type'] === 'Withdrawal') {
        $withdrawals += $transaction['transaction_amt'];
    } elseif ($transaction['tr_type'] === 'Transfer') {
        $transfers += $transaction['transaction_amt'];
    }
}

$subtotal = $deposits - ($withdrawals + $transfers);
$interest_rate = $account['acc_rates'];
$banking_interest = ($subtotal * $interest_rate) / 100;
$total_balance = $subtotal + $banking_interest;

$stmt->close();
$mysqli->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>PaceBank Account Balance</title>
  <link rel="stylesheet" href="check_acc_balance.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
  
  <style>
    @media print {
      .sidebar, button {
        display: none;
      }
    }
  </style>
</head>
<body>

<?php include "sidebar.php"; ?>

<div class="container">
  <h1>Pace Bank Balance Enquiry</h1>

  <div class="print-content">  
    <div class="account-details">
      <p><strong>Account Holder:</strong> <?php echo htmlspecialchars($account['client_name']); ?></p>
      <p><strong>Account Number:</strong> <?php echo htmlspecialchars($account['account_number']); ?></p>
      <p><strong>Account Type:</strong> <?php echo htmlspecialchars($account['acc_type']); ?></p>
      <p><strong>Interest Rate:</strong> <?php echo htmlspecialchars($account['acc_rates']); ?>%</p>
      <p><strong>Email:</strong> <?php echo htmlspecialchars($account['client_email']); ?></p>
      <p><strong>Phone:</strong> <?php echo htmlspecialchars($account['client_phoneno']); ?></p>
    </div>

    <div class="tables-container">
      <table>
        <thead>
          <tr>
            <th>Deposits</th>
            <th>Withdrawals</th>
            <th>Transfers</th>
            <th>Subtotal</th>
          </tr>
        </thead>
        <tbody>
          <tr>
            <td>₹<?php echo number_format($deposits, 2); ?></td>
            <td>₹<?php echo number_format($withdrawals, 2); ?></td>
            <td>₹<?php echo number_format($transfers, 2); ?></td>
            <td>₹<?php echo number_format($subtotal, 2); ?></td>
          </tr>
        </tbody>
      </table>

      <table>
        <thead>
          <tr>
            <th>Funds In</th>
            <th>Funds Out</th>
            <th>Sub Total</th>
            <th>Banking Interest</th>
            <th>Total Balance</th>
          </tr>
        </thead>
        <tbody>
          <tr>
            <td>₹<?php echo number_format($deposits, 2); ?></td>
            <td>₹<?php echo number_format($withdrawals + $transfers, 2); ?></td>
            <td>₹<?php echo number_format($subtotal, 2); ?></td>
            <td>₹<?php echo number_format($banking_interest, 2); ?></td>
            <td>₹<?php echo number_format($total_balance, 2); ?></td>
          </tr>
        </tbody>
      </table>
    </div>
  </div>  

  <button onclick="window.print()">Print</button>
</div>
</body>
</html>
