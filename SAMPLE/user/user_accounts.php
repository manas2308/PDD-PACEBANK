<?php
session_start();
include('conf/config.php');
include('conf/check_login.php');
check_login();
$client_id = $_SESSION['client_id']; // Get the logged-in client's ID
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Accounts</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="stylesheet" href="user_accounts.css">
</head>
<body>
<body>

<?php include("sidebar.php"); ?>
    <!-- Main Content (Table) -->
    <div class="main-content">
        <h1>My Bank Accounts</h1>

        <div class="table-container">
            <table>
                <thead>
                    <tr>
                        <th>Nick Name</th>
                        <th>Account Number</th>
                        <th>Account Type</th>
                        <th>Account Rate</th>
                        <th>Owner Name</th>
                        <th>Date Opened</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    // SQL query to fetch bank accounts for the logged-in client
                    $sql = "SELECT acc_name, account_number, acc_type, acc_rates, client_name, created_at FROM bankaccounts WHERE client_id = ?";
                    $stmt = $mysqli->prepare($sql);
                    $stmt->bind_param("i", $client_id);  // Binding the client_id to the query
                    $stmt->execute();
                    $result = $stmt->get_result();
                    
                    // Fetch and display results
                    while ($row = $result->fetch_assoc()) {
                        echo "<tr>
                                <td>" . $row['acc_name'] . "</td>
                                <td>" . $row['account_number'] . "</td>
                                <td>" . $row['acc_type'] . "</td>
                                <td>" . $row['acc_rates'] . "%</td>
                                <td>" . $row['client_name'] . "</td>
                                <td>" . date("d-M-Y", strtotime($row['created_at'])) . "</td>
                              </tr>";
                    }
                    ?>
                </tbody>
            </table>
        </div>
    </div>
</body>
</html>


<?php
$mysqli->close();
?>
