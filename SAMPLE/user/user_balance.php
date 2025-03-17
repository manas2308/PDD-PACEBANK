<?php
session_start();
include('conf/config.php');
include('conf/check_login.php');
check_login();
$client_id = $_SESSION['client_id']; // Get the logged-in client's ID

// Handle PIN verification when the form is submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $account_id = intval($_POST['account_id']);
    $entered_pin = trim($_POST['pin']);

    // Fetch the stored PIN for the given account ID
    $stmt = $mysqli->prepare("SELECT pin FROM bankaccounts WHERE account_id = ?");
    $stmt->bind_param("i", $account_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $account = $result->fetch_assoc();

    if ($account) {
        // Trim both entered PIN and stored PIN to remove any extra spaces
        $stored_pin = trim($account['pin']);
        
        // Debugging: Show both the entered and stored PIN for comparison
        echo "Entered PIN: " . htmlspecialchars($entered_pin) . "<br>";
        echo "Stored PIN: " . htmlspecialchars($stored_pin) . "<br>";

        // Loosely compare the entered PIN with the stored PIN (allowing for type differences)
        if ($entered_pin == $stored_pin) {
            // Success: Redirect to check balance page
            header("Location: user_checkbalance.php?id=$account_id");
            exit();
        } else {
            $error = 'Incorrect PIN. Please try again.';
        }
    } else {
        $error = 'Account not found.';
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Accounts</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="stylesheet" href="user_accounts.css">
    <style>
        .popup {
            display: none;
            position: fixed;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            background-color: white;
            border: 1px solid #ccc;
            z-index: 1000;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
            border-radius: 10px;
            padding: 20px;
        }
        .popup-content {
            padding: 20px;
        }
        .close {
            cursor: pointer;
            float: right;
            font-size: 18px;
        }
        .confirm {
            background-color: #4CAF50;
            color: white;
            border: none;
            padding: 10px 20px;
            cursor: pointer;
        }
        .error {
            color: red;
            margin-top: 10px;
        }
    </style>
</head>
<body>
<?php include "sidebar.php" ;?>
    <!-- Main Content (Table) -->
    <div class="main-content">
        <h1>My Bank Accounts</h1>
        <div class="table-container">
            <table>
                <thead>
                    <tr>
                        <th>Account Name</th>
                        <th>Account Number</th>
                        <th>Account Type</th>
                        <th>Account Rate</th>
                        <th>Client Name</th>
                        <th>Date Opened</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    // SQL query to fetch bank accounts for the logged-in client
                    $sql = "SELECT account_id, acc_name, account_number, acc_type, acc_rates, client_name, created_at FROM bankaccounts WHERE client_id = ?";
                    $stmt = $mysqli->prepare($sql);
                    $stmt->bind_param("i", $client_id);
                    $stmt->execute();
                    $result = $stmt->get_result();
                    
                    while ($row = $result->fetch_assoc()) {
                        echo "<tr>
                                <td>" . htmlspecialchars($row['acc_name']) . "</td>
                                <td>" . htmlspecialchars($row['account_number']) . "</td>
                                <td>" . htmlspecialchars($row['acc_type']) . "</td>
                                <td>" . htmlspecialchars($row['acc_rates']) . "%</td>
                                <td>" . htmlspecialchars($row['client_name']) . "</td>
                                <td>" . date("d-M-Y", strtotime($row['created_at'])) . "</td>
                                <td>
                                    <a href='#' class='btn btn-check-balance' onclick='showPinPopup(" . $row['account_id'] . ")'>
                                        <i class='fas fa-balance-scale'></i> Check Balance
                                    </a>
                                </td>
                              </tr>";
                    }
                    ?>
                </tbody>
            </table>
        </div>
    </div>

    <!-- PIN Popup -->
    <div id="pinPopup" class="popup">
        <div class="popup-content">
            <span class="close" onclick="closePinPopup()">&times;</span>
            <h3>Enter PIN to Check Balance</h3>
            
            <!-- Popup Form for PIN Confirmation -->
            <form method="post">
                <label for="pin">PIN:</label>
                <input type="password" name="pin" id="pinInput" required>

                <input type="hidden" name="account_id" id="accountIdInput">

                <button type="submit" class="confirm">Confirm</button>
            </form>
            <?php if (isset($error)) { echo "<p class='error'>$error</p>"; } ?>
        </div>
    </div>

    <script>
        // Show the PIN popup
        function showPinPopup(accountId) {
            document.getElementById('accountIdInput').value = accountId;
            document.getElementById('pinPopup').style.display = 'block';
        }

        // Close the PIN popup
        function closePinPopup() {
            document.getElementById('pinPopup').style.display = 'none';
        }
    </script>
</body>
</html>

<?php
$mysqli->close();
?>
