<?php
session_start();
include 'conf/check_login.php';
include 'conf/config.php'; // Ensuring $mysqli is available
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>iBank Advanced Reporting : Withdrawals</title>
    <link rel="stylesheet" href="../BACKEND/transaction_deposits.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/1.10.25/css/jquery.dataTables.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/buttons/1.7.1/css/buttons.dataTables.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
</head>
<body>

<?php include "staff_sidebar.php" ?>
<div class="main-content">
    <h1>iBanking Advanced Reporting : Withdrawals</h1>

    <div class="filter-container">
        <label for="timeframe">Filter By Timeframe: </label>
        <select id="timeframe">
            <option value="all">All</option>
            <option value="1m">Last 1 Month</option>
            <option value="3m">Last 3 Months</option>
            <option value="6m">Last 6 Months</option>
            <option value="1y">Last 1 Year</option>
        </select>
        <input type="text" id="searchInput" placeholder="Search..." class="search-bar">
    </div>

    <table id="withdrawalTable" class="display nowrap" style="width:100%">
        <thead>
            <tr>
                <th>#</th>
                <th>Transaction Code</th>
                <th>Account No.</th>
                <th>Amount</th>
                <th>Acc. Owner</th>
                <th>Timestamp</th>
            </tr>
        </thead>
        <tbody>
            <?php
            // Default SQL query to fetch withdrawals only
            $query = "SELECT tr_code, account_number, transaction_amt, acc_name, created_at FROM transactions WHERE tr_type = 'Withdrawal' AND account_number NOT IN (SELECT account_number FROM bankaccounts WHERE acc_type = 'Organization Account')";

            if (isset($_GET['timeframe']) && $_GET['timeframe'] !== 'all') {
                $timeframe = $_GET['timeframe'];
                switch ($timeframe) {
                    case '1m': $query .= " AND created_at >= NOW() - INTERVAL 1 MONTH"; break;
                    case '3m': $query .= " AND created_at >= NOW() - INTERVAL 3 MONTH"; break;
                    case '6m': $query .= " AND created_at >= NOW() - INTERVAL 6 MONTH"; break;
                    case '1y': $query .= " AND created_at >= NOW() - INTERVAL 1 YEAR"; break;
                }
            }
            $result = $mysqli->query($query);
            $count = 1;
            while ($row = $result->fetch_assoc()) {
                echo "<tr><td>" . $count++ . "</td><td>" . $row['tr_code'] . "</td><td>" . $row['account_number'] . "</td><td>₹" . number_format($row['transaction_amt'], 2) . "</td><td>" . $row['acc_name'] . "</td><td>" . date('d-M-Y H:i:s', strtotime($row['created_at'])) . "</td></tr>";
            }

            if ($result->num_rows > 0) {
                while ($row = $result->fetch_assoc()) {
                    echo "<tr>
                        <td>" . $count++ . "</td>
                        <td>" . $row['tr_code'] . "</td>
                        <td>" . $row['account_number'] . "</td>
                        <td>$" . number_format($row['transaction_amt'], 2) . "</td>
                        <td>" . $row['acc_name'] . "</td>
                        <td>" . date('d-M-Y H:i:s', strtotime($row['created_at'])) . "</td>
                    </tr>";
                }
            } else {
                echo "<tr><td colspan='6'>No transactions found</td></tr>";
            }
            ?>
        </tbody>
    </table>

<script src="https://code.jquery.com/jquery-3.5.1.js"></script>
<script src="https://cdn.datatables.net/1.10.25/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/buttons/1.7.1/js/dataTables.buttons.min.js"></script>
<script src="https://cdn.datatables.net/buttons/1.7.1/js/buttons.flash.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.1.3/jszip.min.js"></script>
<script src="https://cdn.datatables.net/buttons/1.7.1/js/buttons.html5.min.js"></script>
<script src="https://cdn.datatables.net/buttons/1.7.1/js/buttons.print.min.js"></script>

<script>
    $(document).ready(function () {
        var table = $('#withdrawalTable').DataTable({
            dom: 'Bfrtip',
            buttons: [
                'copy', 'csv', 'excel', 'pdf', 'print'
            ]
        });

        // Filter by timeframe
        $('#timeframe').on('change', function () {
            var selectedTimeframe = $(this).val();
            window.location.href = "?timeframe=" + selectedTimeframe;
        });

        // Search functionality
        $('#searchInput').on('keyup', function () {
            table.search(this.value).draw();
        });
    });
</script>

</body>
</html>
