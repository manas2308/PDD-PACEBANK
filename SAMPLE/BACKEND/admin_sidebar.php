<html>
    <head>
    <link rel="stylesheet" href="style.css">
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
    <div class="sidebar">
    <div class="logo">
    <img src="../IMAGES/logo.PNG" alt="PaceBank Logo">PaceBank</div>

    <hr style="border: 1px solid black; width: 100%;">
    <ul class="nav">
        <li><a href="../FRONTEND/admin_dashboard.php"><i class="fas fa-tachometer-alt"></i> Dashboard</a></li>
        <li><a href="../BACKEND/admin_profile.php"><i class="fas fa-user-circle"></i> Profile</a></li>
        <li class="dropdown">
            <a href="#"><i class="fas fa-users"></i> Clients ▾</a>
            <ul class="dropdown-menu">
                <li><a href="../BACKEND/admin_add_client.php"><i class="fas fa-user-plus"></i> Add Client</a></li>
                <li><a href="../BACKEND/admin_manage_client.php"><i class="fas fa-user-cog"></i> Manage Client</a></li>
            </ul>
        </li>
        <li class="dropdown">
            <a href="#"><i class="fas fa-user-tie"></i> Staff ▾</a>
            <ul class="dropdown-menu">
                <li><a href="../BACKEND/admin_staff.php"><i class="fas fa-user-plus"></i> Add Staff</a></li>
                <li><a href="../BACKEND/admin_manage_staff.php"><i class="fas fa-user-cog"></i> Manage Staff</a></li>
            </ul>
        </li>
        <li class="dropdown">
            <a href="#"><i class="fas fa-university"></i> Accounts ▾</a>
            <ul class="dropdown-menu">
                <li><a href="../BACKEND/add_account_type.php"><i class="fas fa-plus-circle"></i> Add Account type</a></li>
                <li><a href="../BACKEND/manage_accounttype.php"><i class="fas fa-edit"></i> Manage Account type</a></li>
                <li><a href="../BACKEND/admin_open_account.php"><i class="fas fa-folder-plus"></i> Open Acc</a></li>
                <li><a href="../BACKEND/manage_acc_openings.php"><i class="fas fa-folder-open"></i> Manage Acc openings</a></li>
            </ul>
        </li>
        <li class="dropdown">
            <a href="#"><i class="fas fa-dollar-sign"></i> Finances ▾</a>
            <ul class="dropdown-menu">
                <li><a href="../BACKEND/pages_deposits.php"><i class="fas fa-piggy-bank"></i> Deposits</a></li>
                <li><a href="../BACKEND/pages_withdrawal.php"><i class="fas fa-wallet"></i> Withdrawals</a></li>
                <li><a href="../BACKEND/transfer_details.php"><i class="fas fa-exchange-alt"></i> Transfers</a></li>
                <li><a href="../BACKEND/balance_details.php"><i class="fas fa-balance-scale"></i> Balance Enquiries</a></li>
            </ul>
        </li>
        <li><a href="../BACKEND/admintransactions.php"><i class="fas fa-money-check-alt"></i> Transactions</a></li>
        <h4>Advanced Modules</h4>
        <li class="dropdown">
            <a href="#"><i class="fas fa-file-alt"></i> Financial Reports ▾</a>
            <ul class="dropdown-menu">
                <li><a href="../BACKEND/transaction_deposit.php"><i class="fas fa-file-invoice-dollar"></i> Deposits</a></li>
                <li><a href="../BACKEND/transaction_withdrawal.php"><i class="fas fa-file-invoice-dollar"></i> Withdrawals</a></li>
                <li><a href="../BACKEND/transaction_transfer.php"><i class="fas fa-file-invoice-dollar"></i> Transfers</a></li>
            </ul>
        </li>
        <li><a href="#"><i class="fas fa-cogs"></i> System Settings</a></li>
        <li><a href="../BACKEND/admin_logout.php"><i class="fas fa-sign-out-alt"></i> Log Out</a></li>
    </ul>
</div>
</body>
</html>
