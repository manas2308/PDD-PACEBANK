<?php
session_start();
include('conf/config.php');
include('conf/check_login.php');
check_login();

if (!isset($_SESSION['client_id'])) {
    header("Location: user_login.php"); // Redirect to login if session is lost
    exit();
}

$client_id = $_SESSION['client_id'];
// Update database to mark notifications as read (Optional)
$mysqli->query("UPDATE transactions SET is_read = 1 WHERE account_number IN (SELECT account_number FROM bankaccounts WHERE client_id = '$client_id')");

// Update session variable
$_SESSION['notifications_read'] = true;

echo "success";

// Initialize variables
$totalDeposits = $totalWithdrawals = $totalTransfers = $totalBalance = 0;
$depositPercentage = $withdrawalPercentage = $transferPercentage = 0;
$transactions = [];
$accounts = [];
$accountTypes = [];

// Fetch user accounts and types
$accountQuery = $mysqli->prepare("SELECT account_number, acc_type FROM bankaccounts WHERE client_id = ?");
$accountQuery->bind_param("i", $client_id);
$accountQuery->execute();
$result = $accountQuery->get_result();
while ($row = $result->fetch_assoc()) {
    $accounts[] = $row['account_number'];
    $accountTypes[$row['acc_type']] = ($accountTypes[$row['acc_type']] ?? 0) + 1;
}
$accountQuery->close();

if (!empty($accounts)) {
    $placeholders = implode(",", array_fill(0, count($accounts), "?"));
    $types = str_repeat("s", count($accounts));

    $transactionQuery = $mysqli->prepare("SELECT tr_type, SUM(transaction_amt) AS total FROM transactions WHERE account_number IN ($placeholders) GROUP BY tr_type");
    $transactionQuery->bind_param($types, ...$accounts);
    $transactionQuery->execute();
    $transactionResult = $transactionQuery->get_result();
    while ($row = $transactionResult->fetch_assoc()) {
        if ($row['tr_type'] == 'Deposit') $totalDeposits = $row['total'];
        if ($row['tr_type'] == 'Withdrawal') $totalWithdrawals = $row['total'];
        if ($row['tr_type'] == 'Transfer') $totalTransfers = $row['total'];
    }
    $transactionQuery->close();

    // Calculate balance and percentages
    $totalBalance = $totalDeposits - $totalWithdrawals- $totalTransfers;
    $totalTransactions = $totalDeposits + $totalWithdrawals + $totalTransfers;
    if ($totalTransactions > 0) {
        $depositPercentage = ($totalDeposits / $totalTransactions) * 100;
        $withdrawalPercentage = ($totalWithdrawals / $totalTransactions) * 100;
        $transferPercentage = ($totalTransfers / $totalTransactions) * 100;
    }

    // Fetch latest transactions
    $latestTransactionsQuery = $mysqli->prepare("SELECT tr_code, account_number, acc_name,tr_type, transaction_amt, receiving_acc_name,receiving_acc_no, created_at FROM transactions WHERE account_number IN ($placeholders) ORDER BY created_at DESC LIMIT 10");
    $latestTransactionsQuery->bind_param($types, ...$accounts);
    $latestTransactionsQuery->execute();
    $transactions = $latestTransactionsQuery->get_result()->fetch_all(MYSQLI_ASSOC);
    $latestTransactionsQuery->close();
}
$mysqli->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>User Dashboard</title>
    <link rel="stylesheet" href="client_dashboard.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
</head>
<body>
    <style>
        .chart-container {
    display: flex;
    flex-wrap: wrap;
    justify-content: center;
    gap: 20px;
    margin-top: 20px;
}

.chart-box {
    flex: 1;
    min-width: 300px;
    max-width: 400px;
    background: white;
    padding: 15px;
    border-radius: 8px;
    box-shadow: 0px 4px 6px rgba(0, 0, 0, 0.1);
    text-align: center;
}

/* Responsive Charts for Mobile */
@media screen and (max-width: 768px) {
    .chart-container {
        flex-direction: column; /* Stack charts vertically */
        align-items: center;
    }

    .chart-box {
        width: 90%; /* Take most of the screen width */
        max-width: 100%;
    }
}

        .top-bar {
    display: flex;
    justify-content: space-between; /* Ensures title stays left, notifications right */
    align-items: center;
    padding: 5px 5px;
    background: #f8f9fa;
    position: relative;
}

 .financial-summary {
    
    justify-content: space-between;
    gap: 20px; /* Adds space between the summary cards */
    margin-bottom: 40px; /* Adds space below the summary cards */
}


.latest-transactions {
    margin-top: 20px; /* Add margin above the recent transactions section */
}
.notification-wrapper {
    position: relative;
    margin-left: auto; /* Pushes icon to the right */
}

.notification-icon {
    position: relative;
    font-size: 24px;
    cursor: pointer;
    color: #333;
}

.notification-badge {
    position: absolute;
    top: -5px;
    right: -5px;
    background: red;
    color: white;
    font-size: 12px;
    border-radius: 50%;
    padding: 3px 6px;
}

.notification-dropdown {
    display: none;
    position: absolute;
    top: 40px;
    right: 0;
    background: white;
    border: 1px solid #ddd;
    box-shadow: 0px 4px 8px rgba(0, 0, 0, 0.2);
    width: 250px;
    max-height: 300px;
    overflow-y: auto;
    border-radius: 5px;
    z-index: 1000;
}

.notification-dropdown h4 {
    padding: 10px;
    margin: 0;
    background: #f8f9fa;
    border-bottom: 1px solid #ddd;
    font-size: 16px;
}

.notification-dropdown ul {
    list-style: none;
    margin: 0;
    padding: 10px;
}

.notification-dropdown li {
    padding: 10px;
    border-bottom: 1px solid #eee;
    font-size: 14px;
}

.notification-dropdown li:last-child {
    border-bottom: none;
}

.notification-dropdown li small {
    display: block;
    font-size: 12px;
    color: gray;
}
.top-bar {
    display: flex;
    justify-content: flex-end; /* Align content to the right */
    align-items: center;
    padding: 10px 20px;
    background: #f8f9fa;
    position: relative;
}

.notification-wrapper {
    position: relative;
    margin-left: auto; /* Pushes the icon to the right */
}
 /* Hide modal initially */
 #transactionModal {
            display: none;
            position: fixed;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            background: white;
            padding: 20px;
            box-shadow: 0px 0px 10px rgba(0, 0, 0, 0.5);
            z-index: 1000;
        }
        .modal-content {
            text-align: center;
        }
        .close {
            cursor: pointer;
            float: right;
            font-size: 20px;
        }
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
    .Deposits { background: #007bff; } /* Blue */
    .Withdrawals { background: #dc3545; } /* Red */
    .Transfers { background: #28a745; } /* Green */
    .Balance { background: #6f42c1; } /* Purple */
    /* General Styles */
body {
    font-family: Arial, sans-serif;
    margin: 0;
    padding: 0;
    box-sizing: border-box;
}
/* Main Content */
.main-content {
    margin-left: 260px;
    padding: 20px;
    transition: margin-left 0.3s;
}

.top-bar {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 10px;
    background: #f8f9fa;
}

/* Cards */
.financial-summary {
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
}

/* Table */
.latest-transactions table {
    width: 100%;
    border-collapse: collapse;
    margin-top: 20px;
}

.latest-transactions th, .latest-transactions td {
    padding: 10px;
    text-align: left;
    border-bottom: 1px solid #ddd;
}


.latest-transactions tr:hover {
    background: #ddd;
}

@media screen and (max-width: 768px) {
    /* Make Sidebar Responsive */
    .sidebar {
        width: 100%;
        height: auto;
        position: fixed;
        left: -100%;
        top: 0;
        background: #f8f9fa;
        transition: left 0.3s ease-in-out;
    }

    .sidebar.open {
        left: 0;
    }

    .menu-toggle {
        display: block;
        position: absolute;
        top: 15px;
        left: 15px;
        font-size: 24px;
        cursor: pointer;
        z-index: 1001;
    }

    /* Ensure Cards Take Full Width */
    .financial-summary {
        flex-direction: column;
    }

    .card {
        width: 100%;
    }

    /* Make Table Scrollable */
    .latest-transactions table {
        display: block;
        overflow-x: auto;
        white-space: nowrap;
    }

    /* Adjust Notification Dropdown */
    .notification-dropdown {
        width: 100%;
        right: auto;
        left: 0;
    }
}
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
<!-- Mobile Menu Icon -->
<div class="mobile-menu" onclick="toggleSidebar()">☰</div>

<div class="sidebar" id="sidebar">
<div class="logo">
    <img src="../IMAGES/logo.PNG" alt="PaceBank Logo">
    PaceBank
</div>

    <hr style="border: 1px solid black; width: 100%;">
    <ul class="nav">
        <li><a href="../user/client_dashboard.php"><i class="fas fa-tachometer-alt"></i> Dashboard</a></li>
        <li><a href="../user/user_profile.php"><i class="fas fa-user-circle"></i> Profile</a></li>
        <li class="dropdown">
            <a href="#"><i class="fas fa-university"></i> Accounts ▾</a>
            <ul class="dropdown-menu">
                <li><a href="../user/client_open_account.php"><i class="fas fa-folder-plus"></i> Open Acc</a></li>
                <li><a href="../user/user_accounts.php"><i class="fas fa-folder-open"></i> My Accounts</a></li>
            </ul>
        </li>
        <li class="dropdown">
            <a href="#"><i class="fas fa-dollar-sign"></i> Finances ▾</a>
            <ul class="dropdown-menu">
                <li><a href="../user/user_withdrawal.php"><i class="fas fa-wallet"></i> Withdrawals</a></li>
                <li><a href="../user/user_transfer.php"><i class="fas fa-exchange-alt"></i> Transfers</a></li>
                <li><a href="../user/user_balance.php"><i class="fas fa-balance-scale"></i> Balance Enquiries</a></li>
            </ul>
        </li>
        <li><a href="../user/user_transaction.php"><i class="fas fa-money-check-alt"></i> Transactions</a></li>
        <li class="dropdown">
            <br>
            <h4>Advanced Modules</h4>
            <br>
            <a href="#"><i class="fas fa-file-alt"></i> Financial Reports ▾</a>
            <ul class="dropdown-menu">
                <li><a href="../user/transaction_userdeposit.php"><i class="fas fa-file-invoice-dollar"></i> Deposits</a></li>
                <li><a href="../user/transaction_userwithdrawal.php"><i class="fas fa-file-invoice-dollar"></i> Withdrawals</a></li>
                <li><a href="../user/transaction_usertransfer.php"><i class="fas fa-file-invoice-dollar"></i> Transfers</a></li>
            </ul>
        </li>
        <li><a href="../user/limit_check.php"><i class="fas fa-user-circle"></i> Limit Check</a></li>
        <li><a href="../user/user_logout.php"><i class="fas fa-sign-out-alt"></i> Log Out</a></li>
    </ul>
</div>

<div class="main-content">
    <div class="top-bar">
    <h2>User Dashboard</h2>
    <div class="notification-wrapper">
        <div class="notification-icon" onclick="toggleNotifications()">
            <i class="fas fa-bell"></i>
            <?php if (!isset($_SESSION['notifications_read']) || $_SESSION['notifications_read'] === false): ?>
    <span class="notification-badge"><?php echo count($transactions); ?></span>
<?php endif; ?>

        </div>
        <div class="notification-dropdown" id="notificationDropdown">
            <h4>Recent Transactions</h4>
            <ul>
                <?php foreach ($transactions as $t): ?>
                    <li>
                        <strong><?php echo $t['tr_type']; ?>:</strong>
                        ₹<?php echo number_format($t['transaction_amt'], 2); ?>
                        <small>(<?php echo $t['created_at']; ?>)</small>
                    </li>
                <?php endforeach; ?>
            </ul>
        </div>
    </div>
</div>


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
    <section class="charts-container">
    <h3>Account Types</h3>
        <canvas id="accountsChart" class="chart">
        </canvas>
        <h3>Transaction Types</h3>
        <canvas id="transactionTypeChart" class="chart">
        </canvas>
    </section>
    <section class="latest-transactions"> 
        <h3>Recent Transactions</h3>
        <table>
            <thead>
                <tr><th>Transaction ID</th><th>Account</th><th>Type</th><th>Amount</th><th>Rec_Acc_Owner</th><th>Rec_Acc_No</th><th>Date</th></tr>
            </thead>
            <tbody>
                <?php foreach ($transactions as $t): ?>
                    <tr onclick="showTransactionDetails(
    '<?php echo $t['tr_code']; ?>',
    '<?php echo $t['tr_type']; ?>',
    '<?php echo number_format($t['transaction_amt'], 2); ?>',
    '<?php echo $t['account_number']; ?>',
    '<?php echo $t['receiving_acc_name']; ?>',
     '<?php echo $t['receiving_acc_no']; ?>',
    '<?php echo $t['created_at']; ?>'
)">

                        <td><?php echo $t['tr_code']; ?></td>
                        <td><?php echo $t['account_number']; ?></td>
                        <td class="badge <?php echo strtolower($t['tr_type']); ?>"> <?php echo $t['tr_type']; ?></td>
                        <td>₹<?php echo number_format($t['transaction_amt'], 2); ?></td>
                        <td><?php echo $t['receiving_acc_name']; ?></td>
                        <td><?php echo $t['receiving_acc_no']; ?></td>
                        <td><?php echo $t['created_at']; ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </section>
</div>
<div id="transactionModal" class="modal">
        <div class="modal-content">
            <span class="close" onclick="closeModal()">&times;</span>
            <h3>Transaction Details</h3>
            <p><strong>Transaction Id:</strong> <span id="tr_code"></span></p>
            <p><strong>Type:</strong> <span id="tr_type"></span></p>
            <p><strong>Amount:</strong> <span id="tr_amount"></span></p>
            <p><strong>Account Number:</strong> <span id="tr_acc_num"></span></p>
            <p><strong>Receiving Account Name:</strong> <span id="tr_acc_name"></span></p>
            <p><strong>Receiving Account Number:</strong> <span id="tr_acc_no"></span></p>
           <p><strong>Date:</strong> <span id="tr_date"></span></p>
        </div>
    </div>
<script>
   const options = {
    responsive: true,
    maintainAspectRatio: false
};

new Chart(document.getElementById('accountsChart'), {
    type: 'pie',
    data: {
        labels: <?php echo json_encode(array_keys($accountTypes)); ?>,
        datasets: [{
            data: <?php echo json_encode(array_values($accountTypes)); ?>,
            backgroundColor: ['#4caf50', '#2196f3', '#ff9800']
        }]
    },
    options: {
        ...options,
        aspectRatio: 2 // Adjust aspect ratio if needed
    }
});

new Chart(document.getElementById('transactionTypeChart'), {
    type: 'pie',
    data: {
        labels: ['Deposits', 'Withdrawals', 'Transfers'],
        datasets: [{
            data: [<?php echo $depositPercentage; ?>, <?php echo $withdrawalPercentage; ?>, <?php echo $transferPercentage; ?>],
            backgroundColor: ['#4caf50', '#ff9800', '#2196f3']
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        aspectRatio: 2,
        plugins: {
            tooltip: {
                callbacks: {
                    label: function(context) {
                        const label = context.label || '';
                        const value = context.raw || 0;
                        return label + ': ' + value.toFixed(2) + '%'; // Add percentage formatting
                    }
                }
            }
        }
    }
});

function toggleNotifications() {
    let dropdown = document.getElementById("notificationDropdown");
    dropdown.style.display = (dropdown.style.display === "block") ? "none" : "block";
}

// Close dropdown when clicking outside
document.addEventListener("click", function(event) {
    let dropdown = document.getElementById("notificationDropdown");
    let icon = document.querySelector(".notification-icon");

    if (!icon.contains(event.target) && !dropdown.contains(event.target)) {
        dropdown.style.display = "none";
    }
});
function showTransactionDetails(tr_code, tr_type, transaction_amt, account_number, acc_name,receiving_acc_no, created_at) {
            document.getElementById('tr_code').innerText = tr_code;
            document.getElementById('tr_type').innerText = tr_type;
            document.getElementById('tr_amount').innerText = '₹' + transaction_amt;
            document.getElementById('tr_acc_num').innerText = account_number;
            document.getElementById('tr_acc_name').innerText = acc_name;
            document.getElementById('tr_acc_no').innerText = receiving_acc_no;
            document.getElementById('tr_date').innerText = created_at;
            document.getElementById('transactionModal').style.display = 'block';
        }
        
        function closeModal() {
            document.getElementById('transactionModal').style.display = 'none';
        }
    document.addEventListener("DOMContentLoaded", function () {
    const sidebar = document.querySelector(".sidebar");
    const menuToggle = document.createElement("div");
    menuToggle.innerHTML = '<i class="fas fa-bars"></i>';
    menuToggle.classList.add("menu-toggle");

    document.body.prepend(menuToggle);

    menuToggle.addEventListener("click", function () {
        sidebar.classList.toggle("open");
    });
});

function toggleSidebar() {
    document.getElementById("sidebar").classList.toggle("active");
}

// Close sidebar when clicking outside
document.addEventListener("click", function (event) {
    let sidebar = document.getElementById("sidebar");
    let menuButton = document.querySelector(".mobile-menu");

    // Close sidebar if clicking outside of it
    if (!sidebar.contains(event.target) && !menuButton.contains(event.target)) {
        sidebar.classList.remove("active");
    }
});


</script>
</body>
</html>