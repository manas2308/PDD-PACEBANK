<?php
// Start session
session_start();

// Include database configuration and authentication check
include('conf/config.php');
include('conf/check_login.php');
check_login();
// Ensure the admin is logged in
if (!isset($_SESSION['staff_id'])) {
    header("Location: ../staff/staff_login.php");
    exit();
}

// Handling Delete Request
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_GET['action']) && $_GET['action'] === 'delete') {
    $accountId = intval($_GET['id']);
    $stmt = $mysqli->prepare("DELETE FROM bankaccounts WHERE account_id = ?");
    $stmt->bind_param("i", $accountId);
    $stmt->execute();

    echo $stmt->affected_rows > 0 ? "Account deleted successfully." : "Failed to delete account. Account not found.";
    exit;
}

// Handling Fetch Request
if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['action']) && $_GET['action'] === 'fetch') {
    $stmt = $mysqli->query("SELECT account_id, acc_name, account_number, acc_type, acc_rates, created_at FROM bankaccounts");
    $accounts = $stmt->fetch_all(MYSQLI_ASSOC);
    header('Content-Type: application/json');
    echo json_encode($accounts);
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
                            <a href="staff_update_accopenings.php?id=${account.account_id}" class="manage-btn">Manage</a>
                            <button class="close-btn" onclick="deleteAccount(${account.account_id})">Close Account</button>
                        </td>
                    `;
                    tbody.appendChild(row);
                });
            } catch (error) {
                console.error("Error loading accounts:", error);
                alert("Unable to load account data. Please try again.");
            }
        }

        async function deleteAccount(accountId) {
            if (confirm("Are you sure you want to delete this account?")) {
                try {
                    const response = await fetch(`?action=delete&id=${accountId}`, { method: "POST" });
                    const result = await response.text();
                    alert(result);
                    loadAccounts();
                } catch (error) {
                    console.error("Error deleting account:", error);
                }
            }
        }
    </script>
</head>
<style>
    .container{
        padding-left:150px;
    }
    </style>
<body>
<?php include "staff_sidebar.php" ; ?>
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
