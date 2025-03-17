<?php
session_start();
include '../staff/conf/check_login.php'; // Ensure only authorized users access this page
include '../staff/conf/config.php'; // Database connection using $mysqli
check_login();
if ($mysqli->connect_error) {
    die("Connection failed: " . $mysqli->connect_error);
}

// Handle Rollback Transaction Request
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['tr_id'])) {
    $tr_id = intval($_POST['tr_id']);
    $delete_query = "DELETE FROM transactions WHERE tr_id = ?";
    $stmt = $mysqli->prepare($delete_query);
    if ($stmt) {
        $stmt->bind_param('i', $tr_id);
        $success = $stmt->execute();
        $stmt->close();
    } else {
        $success = false;
    }
    echo json_encode(['success' => $success]);
    exit;
}

// Date range filter logic
$filterCondition = "";
if (isset($_GET['date_range'])) {
    $date_range = $_GET['date_range'];
    $current_date = date("Y-m-d");

    switch ($date_range) {
        case "1_month":
            $filterCondition = "AND created_at >= DATE_SUB('$current_date', INTERVAL 1 MONTH)";
            break;
        case "3_months":
            $filterCondition = "AND created_at >= DATE_SUB('$current_date', INTERVAL 3 MONTH)";
            break;
        case "6_months":
            $filterCondition = "AND created_at >= DATE_SUB('$current_date', INTERVAL 6 MONTH)";
            break;
        case "1_year":
            $filterCondition = "AND created_at >= DATE_SUB('$current_date', INTERVAL 1 YEAR)";
            break;
    }
}

// Fetch transactions excluding Organization Account type
$query = "SELECT tr_id, tr_code, account_number, tr_type, transaction_amt, acc_name, created_at 
          FROM transactions 
          WHERE account_number NOT IN (
              SELECT account_number FROM bankaccounts WHERE acc_type = 'Organization Account'
          ) 
          $filterCondition 
          ORDER BY created_at DESC";

$stmt = $mysqli->prepare($query);
if (!$stmt) {
    die("SQL Error: " . $mysqli->error);
}

$stmt->execute();
$res = $stmt->get_result();
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
<?php include "staff_sidebar.php"; ?>
<div class="main-content">
    <h1>iBanking Transaction History</h1>

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
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
            <?php
            $cnt = 1;
            while ($row = $res->fetch_object()) {
                $typeBadge = "<span class='badge badge-primary'>Transfer</span>";
                if ($row->tr_type == 'Deposit') {
                    $typeBadge = "<span class='badge badge-success'>Deposit</span>";
                } elseif ($row->tr_type == 'Withdrawal') {
                    $typeBadge = "<span class='badge badge-danger'>Withdrawal</span>";
                }
            ?>
                <tr>
                    <td><?php echo $cnt; ?></td>
                    <td><?php echo htmlspecialchars($row->tr_code); ?></td>
                    <td><?php echo htmlspecialchars($row->account_number); ?></td>
                    <td><?php echo $typeBadge; ?></td>
                    <td>₹<?php echo htmlspecialchars($row->transaction_amt); ?></td>
                    <td><?php echo htmlspecialchars($row->acc_name); ?></td>
                    <td><?php echo date("d-M-Y h:i:s", strtotime($row->created_at)); ?></td>
                    <td>
                        <a class="rollback-btn" href="#" data-id="<?php echo $row->tr_id; ?>">RollBack</a>
                    </td>
                </tr>
            <?php $cnt++; } ?>
        </tbody>
    </table>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>
<script>
    $(document).ready(function () {
        $('#transactionsTable').DataTable();

        $(document).on('click', '.rollback-btn', function (event) {
            event.preventDefault();
            const row = $(this).closest('tr');
            const transactionId = $(this).data('id');

            if (confirm('Are you sure you want to rollback this transaction?')) {
                $.ajax({
                    url: '',
                    type: 'POST',
                    data: { tr_id: transactionId },
                    dataType: 'json',
                    success: function (response) {
                        if (response.success) {
                            alert('Rollback Successful');
                            row.fadeOut(300, function () {
                                $(this).remove();
                            });
                        } else {
                            alert('Rollback Failed. Try Again Later.');
                        }
                    },
                    error: function () {
                        alert('An error occurred. Please try again.');
                    }
                });
            }
        });
    });
</script>
</body>
</html>
