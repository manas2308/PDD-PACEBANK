<?php
// Database connection
$host = 'localhost'; 
$username = 'root'; 
$password = ''; 
$dbname = 'banking'; 

$conn = new mysqli($host, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Fetch data for summary cards
$clientCount = $conn->query("SELECT COUNT(*) as count FROM clients")->fetch_assoc()['count'];
$staffCount = $conn->query("SELECT COUNT(*) as count FROM staff")->fetch_assoc()['count'];
$accountCount = $conn->query("SELECT COUNT(*) as count FROM bankaccounts")->fetch_assoc()['count'];
$transactionCount = $conn->query("SELECT COUNT(*) as count FROM transactions")->fetch_assoc()['count'];
// Financial summary for transaction types
$totalDeposits = $conn->query("SELECT COALESCE(SUM(transaction_amt), 0) as total FROM transactions WHERE tr_type = 'Deposit'")->fetch_assoc()['total'];
$totalWithdrawals = $conn->query("SELECT COALESCE(SUM(transaction_amt), 0) as total FROM transactions WHERE tr_type = 'Withdrawal'")->fetch_assoc()['total'];
$totalTransfers = $conn->query("SELECT COALESCE(SUM(transaction_amt), 0) as total FROM transactions WHERE tr_type = 'Transfer'")->fetch_assoc()['total'];

// Calculate wallet balance as Deposits - Withdrawals
$totalBalance = $totalDeposits - $totalWithdrawals;

// Ensure values are correctly set for chart percentage calculation
$totalTransactions = $totalDeposits + $totalWithdrawals + $totalTransfers;

$depositPercentage = $totalTransactions ? ($totalDeposits / $totalTransactions) * 100 : 0;
$withdrawalPercentage = $totalTransactions ? ($totalWithdrawals / $totalTransactions) * 100 : 0;
$transferPercentage = $totalTransactions ? ($totalTransfers / $totalTransactions) * 100 : 0;


// Fetch transaction history and filter valid transaction types
$transactions = $conn->query("SELECT tr_code, account_number, tr_type, transaction_amt, acc_name, created_at 
                              FROM transactions 
                              WHERE tr_type IN ('Deposit', 'Withdrawal', 'Transfer') 
                              ORDER BY created_at DESC LIMIT 10");

// Accounts per category
$accountData = $conn->query("SELECT acc_type, COUNT(*) as count FROM bankaccounts GROUP BY acc_type");
$accountTypes = [];
$accountCounts = [];
while ($row = $accountData->fetch_assoc()) {
    $accountTypes[] = $row['acc_type'];
    $accountCounts[] = (int)$row['count'];
}

// Close the database connection
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    <link rel="stylesheet" href="style.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    <style>
    .summary-cards, .financial-summary {
        display: flex;
        flex-wrap: wrap;
        gap: 15px;
        justify-content: center;
    }
    .card {
        flex: 1;
        min-width: 200px;
        max-width: 320px;
        padding: 20px;
        color: white;
        border-radius: 8px;
        text-align: center;
        font-size: 1.2rem;
        font-weight: bold;
        box-shadow: 0px 4px 6px rgba(0, 0, 0, 0.1);
        display: flex;
        flex-direction: column;
        align-items: center;
    }
    .card i {
        font-size: 30px; /* Icon size */
        margin-bottom: 10px;
    }
    /* Card Background Colors */
    .clients { background: #17a2b8; } /* Blue */
    .staffs { background: #dc3545; } /* Red */
    .accounts { background: #6f42c1; } /* Purple */
    .transactions { background: #28a745; } /* Green */
    .deposits { background: #007bff; } /* Blue */
    .withdrawals { background: #dc3545; } /* Red */
    .transfers { background: #28a745; } /* Green */
    .wallet { background: #6f42c1; } /* Purple */
</style>
</head>
<body>
<div class="container">
<?php include "../BACKEND/admin_sidebar.php" ; ?>


    <div class="main-content">
        <header>
            <h2 class="page-title">Admin Dashboard</h2>
        </header>

        <!-- Summary Cards -->
     <!-- Summary Cards -->
<section class="summary-cards">
    <div class="card clients">
        <i class="fas fa-users"></i>
        Clients<br><?php echo $clientCount; ?>
    </div>
    <div class="card staffs">
        <i class="fas fa-user-tie"></i>
        Staff<br><?php echo $staffCount; ?>
    </div>
    <div class="card accounts">
        <i class="fas fa-user"></i>
        Accounts<br><?php echo $accountCount; ?>
    </div>
    <div class="card transactions">
        <i class="fas fa-exchange-alt"></i>
        Transactions<br><?php echo $transactionCount; ?>
    </div>
</section>
<br>

<!-- Financial Summary -->
<section class="financial-summary">
    <div class="card deposits">
        <i class="fas fa-upload"></i>
        Deposits<br>₹<?php echo number_format($totalDeposits, 2); ?>
    </div>
    <div class="card withdrawals">
        <i class="fas fa-download"></i>
        Withdrawals<br>₹<?php echo number_format($totalWithdrawals, 2); ?>
    </div>
    <div class="card transfers">
        <i class="fas fa-exchange-alt"></i>
        Transfers<br>₹<?php echo number_format($totalTransfers, 2); ?>
    </div>
    <div class="card wallet">
        <i class="fas fa-wallet"></i>
        Wallet Balance<br>₹<?php echo number_format($totalBalance, 2); ?>
    </div>
</section>

        <!-- Charts Section -->
        <section class="charts-container">
            <div class="charts-header">
                <h3>Accounts & Transactions Overview</h3>
            </div>
            <hr style="border: 1px solid black; width: 100%;">

            <div class="charts" id="charts-section">
                <div class="chart">
                    <h3>Accounts Per Account Types</h3>
                    <canvas id="accountsChart"></canvas>
                </div>
                <div class="chart">
                    <h3>Transaction Type Percentages</h3>
                    <canvas id="transactionTypeChart"></canvas>
                </div>
            </div>
        </section>

        <!-- Transaction History Section -->
        <section class="latest-transactions">
            <h3>Transaction History</h3>
            <table class="transactions-table">
                <thead>
                    <tr>
                        <th>Transaction Code</th>
                        <th>Account No.</th>
                        <th>Type</th>
                        <th>Amount</th>
                        <th>Account Owner</th>
                        <th>Timestamp</th>
                    </tr>
                </thead>
                <tbody>
                <?php while ($transaction = $transactions->fetch_assoc()): ?>
                    <tr>
                        <td><?php echo $transaction['tr_code']; ?></td>
                        <td><?php echo $transaction['account_number']; ?></td>
                        <td class="badge <?php echo strtolower($transaction['tr_type']); ?>"><?php echo $transaction['tr_type']; ?></td>
                        <td>₹<?php echo number_format($transaction['transaction_amt'], 2); ?></td>
                        <td><?php echo $transaction['acc_name']; ?></td>
                        <td><?php echo $transaction['created_at']; ?></td>
                    </tr>
                <?php endwhile; ?>
                </tbody>
            </table>
            <button> <a href="../BACKEND/admintransactions.php">VIEW ALL</a></button>
        </section>
    </div>
</div>

<script>
    // Accounts Pie Chart
    const accountsData = {
        labels: <?php echo json_encode($accountTypes); ?>,
        datasets: [{
            label: 'Accounts',
            data: <?php echo json_encode($accountCounts); ?>,
            backgroundColor: ['#4caf50', '#2196f3', '#ff9800', '#e91e63', '#9c27b0']
        }]
    };

    new Chart(document.getElementById('accountsChart'), {
        type: 'pie',
        data: accountsData
    });

    // Transaction Type Percentages Chart
    // Transaction Type Percentages Pie Chart
    const transactionTypeDataPie = {
        labels: ['Deposits', 'Withdrawals', 'Transfers'],
        datasets: [{
            label: 'Transaction Type Distribution (%)',
            data: [<?php echo $depositPercentage; ?>, <?php echo $withdrawalPercentage; ?>, <?php echo $transferPercentage; ?>],
            backgroundColor: ['#4caf50', '#ff9800', '#2196f3']
        }]
    };

    new Chart(document.getElementById('transactionTypeChart'), {
        type: 'pie',
        data: transactionTypeDataPie,
        options: {
            responsive: true,
        }
    });


</script>
</body>
</html>
