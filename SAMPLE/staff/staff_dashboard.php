<?php
session_start();
include('conf/config.php');
include('conf/check_login.php');
check_login();
$staff_id = $_SESSION['staff_id']; 

// Fetch data for summary cards
$clientCount = $mysqli->query("SELECT COUNT(*) as count FROM clients")->fetch_assoc()['count'];
$staffCount = $mysqli->query("SELECT COUNT(*) as count FROM staff")->fetch_assoc()['count'];
$accountCount = $mysqli->query("SELECT COUNT(*) as count FROM bankaccounts")->fetch_assoc()['count'];
$transactionCount = $mysqli->query("SELECT COUNT(*) as count FROM transactions")->fetch_assoc()['count'];

// Financial summary for transaction types
$totalDeposits = $mysqli->query("SELECT COALESCE(SUM(transaction_amt), 0) as total FROM transactions WHERE tr_type = 'Deposit'")->fetch_assoc()['total'];
$totalWithdrawals = $mysqli->query("SELECT COALESCE(SUM(transaction_amt), 0) as total FROM transactions WHERE tr_type = 'Withdrawal'")->fetch_assoc()['total'];
$totalTransfers = $mysqli->query("SELECT COALESCE(SUM(transaction_amt), 0) as total FROM transactions WHERE tr_type = 'Transfer'")->fetch_assoc()['total'];

// Calculate wallet balance as Deposits - Withdrawals
$totalBalance = $totalDeposits - $totalWithdrawals;

// Ensure values are correctly set for chart percentage calculation
$totalTransactions = $totalDeposits + $totalWithdrawals + $totalTransfers;

$depositPercentage = $totalTransactions ? ($totalDeposits / $totalTransactions) * 100 : 0;
$withdrawalPercentage = $totalTransactions ? ($totalWithdrawals / $totalTransactions) * 100 : 0;
$transferPercentage = $totalTransactions ? ($totalTransfers / $totalTransactions) * 100 : 0;

// Fetch transaction history and exclude Organization Account transactions
$transactionsQuery = "SELECT tr_code, account_number, tr_type, transaction_amt, acc_name, created_at 
                      FROM transactions 
                      WHERE tr_type IN ('Deposit', 'Withdrawal', 'Transfer') 
                      AND account_number NOT IN (SELECT account_number FROM bankaccounts WHERE acc_type = 'Organization Account') 
                      ORDER BY created_at DESC LIMIT 10";

$transactions = $mysqli->query($transactionsQuery);

if (!$transactions) {
    die("Query Failed: " . $mysqli->error);
}

// Accounts per category
$accountData = $mysqli->query("SELECT acc_type, COUNT(*) as count FROM bankaccounts GROUP BY acc_type");
$accountTypes = [];
$accountCounts = [];
while ($row = $accountData->fetch_assoc()) {
    $accountTypes[] = $row['acc_type'];
    $accountCounts[] = (int)$row['count'];
}

// Close the database connection
$mysqli->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    <link rel="stylesheet" href="staff_dashboard.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
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
    .deposits { background: #007bff; } /* Blue */
    .withdrawals { background: #dc3545; } /* Red */
    .transfers { background: #28a745; } /* Green */
    .wallet { background: #6f42c1; } /* Purple */
    .logo {
    display: flex;
    align-items: center;
    font-size: 20px;
    font-weight: bold;
    color: white;
}

.logo img {
    width: 60px; /* Adjust as needed */
    height: auto;
    margin-right: 8px; /* Space between image and text */
}

</style>
</head>
<body>
<div class="sidebar">
<div class="logo">
    <img src="../IMAGES/logo.PNG" alt="PaceBank Logo">
    PaceBank
</div>
    <hr style="border: 1px solid black; width: 100%;">
    <ul class="nav">
        <li><a href="../staff/staff_dashboard.php"><i class="fas fa-tachometer-alt"></i> Dashboard</a></li>
        <li><a href="../staff/staff_profile.php"><i class="fas fa-user-circle"></i> Profile</a></li>
        <li class="dropdown">
            <a href="#"><i class="fas fa-users"></i> Clients ▾</a>
            <ul class="dropdown-menu">
                <li><a href="../staff/staff_add_client.php"><i class="fas fa-user-plus"></i> Add Client</a></li>
                <li><a href="../staff/staff_manage_client.php"><i class="fas fa-user-cog"></i> Manage Client</a></li>
            </ul>
        </li>
        <li class="dropdown">
            <a href="#"><i class="fas fa-university"></i> Accounts ▾</a>
            <ul class="dropdown-menu">
                <li><a href="../staff/staff_add_accounttype.php"><i class="fas fa-plus-circle"></i> Add Account type</a></li>
                <li><a href="../staff/staff_manage_accounttype.php"><i class="fas fa-edit"></i> Manage Account type</a></li>
                <li><a href="../staff/staff_open_account.php"><i class="fas fa-folder-plus"></i> Open Acc</a></li>
                <li><a href="../staff/staff_manage_accopenings.php"><i class="fas fa-folder-open"></i> Manage Acc openings</a></li>
            </ul>
        </li>
        <li class="dropdown">
            <a href="#"><i class="fas fa-dollar-sign"></i> Finances ▾</a>
            <ul class="dropdown-menu">
                <li><a href="../staff/staff_deposits.php"><i class="fas fa-piggy-bank"></i> Deposits</a></li>
                <li><a href="../staff/staff_withdrawal.php"><i class="fas fa-wallet"></i> Withdrawals</a></li>
                <li><a href="../staff/staff_transferdetails.php"><i class="fas fa-exchange-alt"></i> Transfers</a></li>
                <li><a href="../staff/staff_balances.php"><i class="fas fa-balance-scale"></i> Balance Enquiries</a></li>
            </ul>
        </li>
        <li><a href="../staff/staff_transactionengine.php"><i class="fas fa-money-check-alt"></i> Transactions</a></li>
        <h4>Advanced Modules</h4>
        <li class="dropdown">
            <a href="#"><i class="fas fa-file-alt"></i> Financial Reports ▾</a>
            <ul class="dropdown-menu">
                <li><a href="../staff/staff_transaction_deposit.php"><i class="fas fa-file-invoice-dollar"></i> Deposits</a></li>
                <li><a href="../staff/staff_transaction_withdrawal.php"><i class="fas fa-file-invoice-dollar"></i> Withdrawals</a></li>
                <li><a href="../staff/staff_transaction_transfer.php"><i class="fas fa-file-invoice-dollar"></i> Transfers</a></li>
            </ul>
        </li>
        <li><a href="../staff/staff_logout.php"><i class="fas fa-sign-out-alt"></i> Log Out</a></li>
    </ul>
</div>
<div class="container">
    <div class="main-content">
        <header>
            <h2 class="page-title">Staff Dashboard</h2>
        </header>

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
</section>

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
            <div class="chart">
                <h3>Accounts Per Account Types</h3>
                <canvas id="accountsChart"></canvas>
            </div>
            <div class="chart">
                <h3>Transaction Type Distribution</h3>
                <canvas id="transactionTypeChart"></canvas>
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
                        <td class="badge <?php echo strtolower($transaction['tr_type']); ?>">
                            <?php echo $transaction['tr_type']; ?>
                        </td>
                        <td>₹<?php echo number_format($transaction['transaction_amt'], 2); ?></td>
                        <td><?php echo $transaction['acc_name']; ?></td>
                        <td><?php echo $transaction['created_at']; ?></td>
                    </tr>
                <?php endwhile; ?>
                </tbody>
            </table>
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

    // Transaction Type Distribution Pie Chart
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
