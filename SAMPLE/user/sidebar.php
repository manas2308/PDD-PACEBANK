<html>
    <head>
    <link rel="stylesheet" href="sidebar.css">
</head>
<style>
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
    <body>
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

<script>
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
