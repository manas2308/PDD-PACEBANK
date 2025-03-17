<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>PaceBank Account Balance</title>
  <link rel="stylesheet" href="../BACKEND/check_bankbalance.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
</head>
<body>
<?php include "admin_sidebar.php" ;?>
<div class="container">
  <h1>PaceBanking Corporation Balance Enquiry</h1>

  <?php
    $account_number = $_GET['account_number'];
    $mysqli = new mysqli("localhost", "root", "", "banking");

    if ($mysqli->connect_error) {
        die("Connection failed: " . $mysqli->connect_error);
    }

    // Fetch account details
    $account_query = "SELECT acc_name, acc_type, acc_rates, client_name, client_email, client_phoneno, client_national_id, amount, created_at FROM bankaccounts WHERE account_number = ?";
    $stmt = $mysqli->prepare($account_query);
    $stmt->bind_param('s', $account_number);
    $stmt->execute();
    $account_result = $stmt->get_result();
    $account = $account_result->fetch_assoc();

    // Fetch transactions
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
  ?>

  <div class="account-details">
    <p><strong>Account Holder:</strong> <?php echo $account['client_name']; ?></p>
    <p><strong>Account Number:</strong> <?php echo $account_number; ?></p>
    <p><strong>Account Type:</strong> <?php echo $account['acc_type']; ?></p>
    <p><strong>Interest Rate:</strong> <?php echo $account['acc_rates']; ?>%</p>
    <p><strong>Email:</strong> <?php echo $account['client_email']; ?></p>
    <p><strong>Phone:</strong> <?php echo $account['client_phoneno']; ?></p>
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

  <button onclick="window.print()">Print</button>
</div>

<?php
  $stmt->close();
  $mysqli->close();
?>
</body>
</html>
