<?php
session_start(); // Ensure the session is started

// Assuming the client ID is stored in the session upon login
$client_id = $_SESSION['client_id'] ?? null;

if (!$client_id) {
    // If client is not logged in or client_id is missing, redirect to login page
    header("Location: ../user/user_login.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>iBank Advanced Reporting: Deposits</title>
    <link rel="stylesheet" href="../BACKEND/transaction_deposits.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/1.10.25/css/jquery.dataTables.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/buttons/1.7.1/css/buttons.dataTables.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
</head>
<body>

<?php include "sidebar.php";?>
<div class="main-content">
    <h1>iBanking Advanced Reporting: Deposits</h1>

    <div class="filter-container">
        <label for="timeframe">Filter By Timeframe: </label>
        <select id="timeframe">
            <option value="all">All</option>
            <option value="1m">Last 1 Month</option>
            <option value="3m">Last 3 Months</option>
            <option value="6m">Last 6 Months</option>
            <option value="1y">Last 1 Year</option>
        </select>
      
    </div>

    <table id="depositTable" class="display nowrap" style="width:100%">
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
            $mysqli = new mysqli("localhost", "root", "", "banking");

            if ($mysqli->connect_error) {
                die("Connection failed: " . $mysqli->connect_error);
            }

            // Default SQL query to fetch deposits only for the logged-in client
            $sql = "SELECT tr_code, account_number, transaction_amt, acc_name, created_at 
                    FROM transactions 
                    WHERE tr_type = 'Deposit' AND client_id = ?";

            // Add timeframe filtering if applicable
            if (isset($_GET['timeframe'])) {
                $timeframe = $_GET['timeframe'];
                $time_condition = '';

                switch ($timeframe) {
                    case '1m':
                        $time_condition = " AND created_at >= NOW() - INTERVAL 1 MONTH";
                        break;
                    case '3m':
                        $time_condition = " AND created_at >= NOW() - INTERVAL 3 MONTH";
                        break;
                    case '6m':
                        $time_condition = " AND created_at >= NOW() - INTERVAL 6 MONTH";
                        break;
                    case '1y':
                        $time_condition = " AND created_at >= NOW() - INTERVAL 1 YEAR";
                        break;
                }

                $sql .= $time_condition;
            }

            // Prepare and execute the query
            $stmt = $mysqli->prepare($sql);
            $stmt->bind_param("i", $client_id); // Bind client_id to the query
            $stmt->execute();
            $result = $stmt->get_result();
            $count = 1;

            if ($result->num_rows > 0) {
                while ($row = $result->fetch_assoc()) {
                    echo "<tr>
                        <td>" . $count++ . "</td>
                        <td>" . $row['tr_code'] . "</td>
                        <td>" . $row['account_number'] . "</td>
                        <td>₹" . number_format($row['transaction_amt'], 2) . "</td>
                        <td>" . $row['acc_name'] . "</td>
                        <td>" . date('d-M-Y H:i:s', strtotime($row['created_at'])) . "</td>
                    </tr>";
                }
            } else {
                echo "<tr><td colspan='6'>No transactions found</td></tr>";
            }

            $stmt->close();
            $mysqli->close();
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
        var table = $('#depositTable').DataTable({
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
