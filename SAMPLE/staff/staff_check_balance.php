<?php
// Include necessary files
session_start();
include 'conf/config.php';
include 'conf/check_login.php';
check_login();
$client_id = $_GET['client_id'];

$query = "SELECT acc_name, account_number, acc_type, acc_rates, created_at FROM bankaccounts WHERE client_id = ? AND acc_type != 'Organization Account'";
$stmt = $mysqli->prepare($query);
$stmt->bind_param('i', $client_id);
$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link rel="stylesheet" href="../BACKEND/styles.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
  <title>Client Bank Accounts</title>
  <style>
    * { box-sizing: border-box; margin: 0; padding: 0; }
    body { font-family: Arial, sans-serif; display: flex; min-height: 100vh; }
    .container { display: flex; width: 100%; }
    .sidebar { width: 250px; background-color: #333; color: white; padding: 20px; position: fixed; top: 0; left: 0; height: 100%; overflow-y: auto; }
    .logo { font-size: 24px; font-weight: bold; text-align: center; margin-bottom: 20px; }
    .nav ul { list-style-type: none; padding: 0; }
    .nav a { display: block; color: white; padding: 10px; text-decoration: none; transition: 0.3s; }
    .nav a:hover { background-color: #575757; }
    .dropdown-menu { display: none; }
    .nav .dropdown:hover .dropdown-menu { display: block; background-color: #444; padding-left: 10px; }
    .main-content { margin-left: 270px; padding: 20px; flex-grow: 1; background-color: white; border-radius: 8px; min-height: 100vh; box-sizing: border-box; width: 100%; }
    h1 { margin-bottom: 20px; }
    table { width: 100%; border-collapse: collapse; }
    th, td { border: 1px solid #ddd; padding: 10px; text-align: left; }
    th { background-color: #f2f2f2; }
    .action-btn { padding: 5px 10px; color: white; background-color: green; border: none; border-radius: 3px; text-decoration: none; }
    .no-data { text-align: center; color: red; }
  </style>
</head>
<body>
<?php include "staff_sidebar.php" ; ?>
  <div class="main-content">
    <h1>Client Bank Accounts</h1>
    <table id="account-table">
      <thead>
        <tr>
          <th>#</th>
          <th>Account Name</th>
          <th>Account Number</th>
          <th>Account Type</th>
          <th>Interest Rate</th>
          <th>Date Created</th>
          <th>Action</th>
        </tr>
      </thead>
      <tbody>
        <?php
        if ($result->num_rows > 0) {
          $cnt = 1;
          while ($row = $result->fetch_assoc()) {
            echo "<tr>";
            echo "<td>" . $cnt . "</td>";
            echo "<td>" . htmlspecialchars($row['acc_name']) . "</td>";
            echo "<td>" . htmlspecialchars($row['account_number']) . "</td>";
            echo "<td>" . htmlspecialchars($row['acc_type']) . "</td>";
            echo "<td>" . htmlspecialchars($row['acc_rates']) . "%</td>";
            echo "<td>" . date("d-M-Y", strtotime($row['created_at'])) . "</td>";
            echo "<td><a class='action-btn' href='staff_check_bankbalance.php?account_number=" . htmlspecialchars($row['account_number']) . "'>Check Balance</a></td>";
            echo "</tr>";
            $cnt++;
          }
        } else {
          echo "<tr><td colspan='7' class='no-data'>No bank accounts found for this client.</td></tr>";
        }
        $stmt->close();
        ?>
      </tbody>
    </table>
  </div>
</body>
</html>
