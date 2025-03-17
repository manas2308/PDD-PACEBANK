<?php
session_start(); // Ensure the session is started

// Assuming the client ID is stored in the session upon login
$client_id = $_SESSION['client_id'] ?? null;

if (!$client_id) {
    // If client is not logged in or client_id is missing, redirect to login page
    header("Location: ../user/user_login.php");
    exit;
}

$mysqli = new mysqli("localhost", "root", "", "banking");

if ($mysqli->connect_error) {
    die("Connection failed: " . $mysqli->connect_error);
}

// Date range filter logic
$filterCondition = "WHERE client_id = ?"; // Only fetch transactions for the logged-in client
if (isset($_GET['date_range'])) {
    $date_range = $_GET['date_range'];
    $current_date = date("Y-m-d");

    switch ($date_range) {
        case "1_month":
            $filterCondition .= " AND created_at >= DATE_SUB('$current_date', INTERVAL 1 MONTH)";
            break;
        case "3_months":
            $filterCondition .= " AND created_at >= DATE_SUB('$current_date', INTERVAL 3 MONTH)";
            break;
        case "6_months":
            $filterCondition .= " AND created_at >= DATE_SUB('$current_date', INTERVAL 6 MONTH)";
            break;
        case "1_year":
            $filterCondition .= " AND created_at >= DATE_SUB('$current_date', INTERVAL 1 YEAR)";
            break;
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Transaction History</title>
    <link rel="stylesheet" href="../BACKEND/admintransactions.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/1.11.5/css/jquery.dataTables.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
</head>
<body>
<?php include "sidebar.php"; ?>
<div class="main-content">
    <h1>iBanking Transaction History</h1>

    <!-- Filter Form -->
    <form method="GET" class="filter-form">
        <label for="date_range">Select Date Range:</label>
        <select name="date_range" id="date_range">
            <option value="">All</option>
            <option value="1_month">Last 1 Month</option>
            <option value="3_months">Last 3 Months</option>
            <option value="6_months">Last 6 Months</option>
            <option value="1_year">Last 1 Year</option>
        </select>
        <button type="submit">Filter</button>
    </form>

    <!-- Print Button -->
    <button onclick="window.print();" class="print-btn">Print Transactions</button>

    <table id="transactionsTable" class="display">
        <thead>
            <tr>
                <th>#</th>
                <th>Transaction Code</th>
                <th>Account Number</th>
                <th>Type</th>
                <th>Amount</th>
                <th>Account Name</th>
                <th>Timestamp</th>
            </tr>
        </thead>
        <tbody>
            <?php
            // Fetch the logged-in client's transactions
            $query = "SELECT tr_id, tr_code, account_number, tr_type, transaction_amt, acc_name, created_at 
                      FROM transactions $filterCondition ORDER BY created_at DESC";
            $stmt = $mysqli->prepare($query);
            $stmt->bind_param('i', $client_id); // Bind the client ID to the query
            $stmt->execute();
            $res = $stmt->get_result();
            $cnt = 1;
            while ($row = $res->fetch_object()) {
                $typeBadge = "";
                if ($row->tr_type == 'Deposit') {
                    $typeBadge = "<span class='badge badge-success'>Deposit</span>";
                } elseif ($row->tr_type == 'Withdrawal') {
                    $typeBadge = "<span class='badge badge-danger'>Withdrawal</span>";
                } else {
                    $typeBadge = "<span class='badge badge-primary'>Transfer</span>";
                }
            ?>
                <tr>
                    <td><?php echo $cnt; ?></td>
                    <td><?php echo $row->tr_code; ?></td>
                    <td><?php echo $row->account_number; ?></td>
                    <td><?php echo $typeBadge; ?></td>
                    <td>₹<?php echo $row->transaction_amt; ?></td>
                    <td><?php echo $row->acc_name; ?></td>
                    <td><?php echo date("d-M-Y h:i:s", strtotime($row->created_at)); ?></td>
                </tr>
            <?php $cnt++;
            } ?>
        </tbody>
    </table>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>
<script>
    $(document).ready(function () {
        $('#transactionsTable').DataTable();
    });
</script>
</body>
</html>
