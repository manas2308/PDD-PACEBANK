<?php
session_start();
include_once('conf/config.php');
include_once('conf/check_login.php');
check_login();
date_default_timezone_set('Asia/Kolkata');

$client_id = $_SESSION['client_id'];
$limit_amount = 0;
$notification = "";
$notification_class = "";
$start_date = "";
$end_date = "";
$total_withdrawn = 0;

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $start_date = $_POST['start_date'];
    $end_date = $_POST['end_date'];
    $limit_amount = floatval($_POST['limit_amount']);

    // Validate input dates
    if (!empty($start_date) && !empty($end_date)) {
        // Fetch total withdrawals within the selected date range
        $query = $mysqli->prepare("SELECT COALESCE(SUM(transaction_amt), 0) AS total_withdrawn 
                                   FROM transactions 
                                   WHERE tr_type = 'Withdrawal' 
                                   AND account_number IN (SELECT account_number FROM bankaccounts WHERE client_id = ?) 
                                   AND DATE(created_at) BETWEEN ? AND ?");
        $query->bind_param('iss', $client_id, $start_date, $end_date);
        $query->execute();
        $result = $query->get_result();
        $total_withdrawn = $result->fetch_assoc()['total_withdrawn'];

        // Compare with limit
        if ($total_withdrawn > $limit_amount) {
            $notification = "You are above your limit! Plan accordingly.";
            $notification_class = "alert-danger"; // Red notification
        } else {
            $notification = "You are using money within the limit. Good, keep it up!";
            $notification_class = "alert-success"; // Green notification
        }

        // Fetch transaction history for the selected date range
        $transactions = $mysqli->prepare("SELECT tr_code, account_number, transaction_amt, created_at 
                                          FROM transactions 
                                          WHERE tr_type = 'Withdrawal' 
                                          AND account_number IN (SELECT account_number FROM bankaccounts WHERE client_id = ?) 
                                          AND DATE(created_at) BETWEEN ? AND ?
                                          ORDER BY created_at DESC");
        $transactions->bind_param('iss', $client_id, $start_date, $end_date);
        $transactions->execute();
        $transaction_result = $transactions->get_result();
    }
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Transaction Limit Check</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" integrity="sha384-QWTKZtMZ1Nv6gX1N1e1VUZylWgqVYpZt94WolTx5WaE6Jp7U2KkB5KjNyJKm9B6F" crossorigin="anonymous">

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="stylesheet" href="limit_check.css">
</head>
<body>
<?php include "sidebar.php";?>
<div class="container mt-5">
    <h2 class="text-center">Withdrawal Limit Check</h2>
    
    <form method="POST" class="mt-4">
        <div class="row">
            <div class="col-md-4">
                <label for="start_date" class="form-label">Start Date:</label>
                <input type="date" class="form-control" id="start_date" name="start_date" required>
            </div>
            <div class="col-md-4">
                <label for="end_date" class="form-label">End Date:</label>
                <input type="date" class="form-control" id="end_date" name="end_date" required>
            </div>
            <div class="col-md-4">
                <label for="limit_amount" class="form-label">Limit Amount:</label>
                <input type="number" step="0.01" class="form-control" id="limit_amount" name="limit_amount" required>
            </div>
            <br>
        </div>
        <button type="submit" class="btn btn-primary mt-3">Compare</button>
    </form>

    <?php if (!empty($notification)): ?>
        <div class="alert <?php echo $notification_class; ?> mt-3">
            <?php echo $notification; ?>
        </div>
    <?php endif; ?>

    <?php if (!empty($start_date) && !empty($end_date)): ?>
        <h3 class="mt-4">Transaction History (Withdrawals from <?php echo $start_date; ?> to <?php echo $end_date; ?>)</h3>
        <table class="table table-bordered mt-3">
            <thead>
                <tr>
                    <th>Transaction Code</th>
                    <th>Account No.</th>
                    <th>Amount</th>
                    <th>Timestamp</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($transaction = $transaction_result->fetch_assoc()): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($transaction['tr_code']); ?></td>
                        <td><?php echo htmlspecialchars($transaction['account_number']); ?></td>
                        <td>₹<?php echo number_format($transaction['transaction_amt'], 2); ?></td>
                        <td><?php echo $transaction['created_at']; ?></td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    <?php endif; ?>
</div>

</body>
</html>
