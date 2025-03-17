<?php
require_once '../staff/conf/config.php'; // Database connection
require_once '../staff/conf/check_login.php'; // Ensure user is logged in

try {
    // Handling Fetch Request
    if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['action']) && $_GET['action'] === 'fetch') {
        $query = "SELECT account_id, acc_name, account_number, acc_type, acc_rates, created_at FROM bankaccounts WHERE acc_type != 'Organization Account'";
        $result = $mysqli->query($query);
        
        $accounts = [];
        while ($row = $result->fetch_assoc()) {
            $accounts[] = $row;
        }
        
        header('Content-Type: application/json');
        echo json_encode($accounts);
        exit;
    }
} catch (Exception $e) {
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
    <title>Manage Withdrawals</title>
    <link rel="stylesheet" href="../BACKEND/pages_withdrawal.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            loadAccounts();
            document.getElementById("search").addEventListener("input", filterAccounts);
        });

        async function loadAccounts() {
            try {
                const response = await fetch("?action=fetch");
                if (!response.ok) throw new Error("Failed to fetch account data.");

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
                        <td><a href="staff_withdrawalmoney.php?id=${account.account_id}" class="manage-btn">Withdraw</a></td>
                    `;
                    tbody.appendChild(row);
                });
            } catch (error) {
                console.error("Error loading accounts:", error);
                alert("Unable to load account data. Please try again.");
            }
        }

        function filterAccounts() {
            const filter = document.getElementById("search").value.toLowerCase();
            document.querySelectorAll("#account-data tr").forEach(row => {
                const name = row.cells[1].textContent.toLowerCase();
                const accNumber = row.cells[2].textContent.toLowerCase();
                row.style.display = name.includes(filter) || accNumber.includes(filter) ? "" : "none";
            });
        }
    </script>
</head>
<style>
    .container{
        margin-left:280px;
        width:100%;
    }
    </style>
<body>
<?php include "staff_sidebar.php" ; ?>
    <div class="container">
        <h1>Manage Withdrawals</h1>
        <p>Select any action options to manage withdrawals</p>
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
