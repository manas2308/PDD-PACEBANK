<?php
$host = 'localhost';
$dbname = 'banking';
$username = 'root';
$password = '';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Handling Fetch Request
    if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['action']) && $_GET['action'] === 'fetch') {
        $stmt = $pdo->query("SELECT account_id, acc_name, account_number, acc_type, acc_rates, created_at FROM bankaccounts");
        $accounts = $stmt->fetchAll(PDO::FETCH_ASSOC);
        header('Content-Type: application/json');
        echo json_encode($accounts);
        exit;
    }
} catch (PDOException $e) {
    header('Content-Type: application/json');
    echo json_encode(["error" => $e->getMessage()]);
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage iBanking Accounts</title>
    <link rel="stylesheet" href="../BACKEND/manage_acc_openings.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            loadAccounts();

            // Attach search functionality once DOM is loaded
            const searchInput = document.getElementById("search");
            searchInput.addEventListener("input", function() {
                const filter = searchInput.value.toLowerCase();
                const rows = document.querySelectorAll("#account-data tr");
                rows.forEach(row => {
                    const name = row.cells[1].textContent.toLowerCase();
                    const accNumber = row.cells[2].textContent.toLowerCase();
                    row.style.display = name.includes(filter) || accNumber.includes(filter) ? "" : "none";
                });
            });
        });

        async function loadAccounts() {
            try {
                const response = await fetch("?action=fetch");
                if (!response.ok) {
                    throw new Error("Failed to fetch account data.");
                }

                const accounts = await response.json();
                const tbody = document.getElementById("account-data");
                tbody.innerHTML = "";

                accounts.forEach((account, index) => {
                    const row = document.createElement("tr");
                    row.innerHTML = `
                        <td>${index + 1}</td>
                        <td>${account.acc_name}</td>
                        <td>${account.account_number}</td>
                        <td>${account.acc_rates}%</td>
                        <td>${account.acc_type}</td>
                        <td>${account.created_at}</td>
                        <td>
                           <a href="deposit_money.php?id=${account.account_id}" class="manage-btn">Deposit</a>
                        </td>

                    `;
                    tbody.appendChild(row);
                });
            } catch (error) {
                console.error("Error loading accounts:", error);
                alert("Unable to load account data. Please try again.");
            }
        }
    </script>
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

<div class="container">
    <h1>Manage iBanking Accounts</h1>
    <p>Select any action options to manage your accounts</p>
    <input type="text" id="search" placeholder="Search by Name or Account Number...">

    <table>
        <thead>
            <tr>
                <th>#</th>
                <th>Name</th>
                <th>Acc Number</th>
                <th>Rate</th>
                <th>Acc Type</th>
                <th>Date Opened</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody id="account-data"></tbody>
    </table>
</div>
</body> 
</html>
