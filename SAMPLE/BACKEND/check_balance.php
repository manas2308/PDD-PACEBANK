<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link rel="stylesheet" href="styles.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
  <title>Client Bank Accounts</title>
  <style>
   
.sidebar a:hover {
  background-color: #f0f0f0; /* Changes background on hover (optional) */
  color: #4caf50;            /* Changes text color on hover (optional) */
}

.dropdown-menu {
  padding-left: 20px; /* Indents the submenu items */
}

.dropdown a {
  cursor: pointer; /* Adds pointer cursor for dropdown items */
}


.logo {
  font-size: 24px;
  font-weight: bold;
  text-align: center;
  margin-bottom: 20px;
}

.nav ul {
  list-style-type: none;
  padding: 0;
}

.nav a {
  display: block;
  color: white;
  padding: 10px;
  text-decoration: none;
  transition: 0.3s;
}

.nav a:hover {
  background-color: #575757;
}

.dropdown-menu {
  display: none;
}

.nav .dropdown:hover .dropdown-menu {
  display: block;
  background-color: #444;
  padding-left: 10px;
}
body {
  margin: 0;
  font-family: Arial, sans-serif;
  background-color: #f4f4f4;
  color: #333;
}



/* Sidebar Styling (No Change Here) */
.sidebar {
  width: 240px;
  background-color: #333;
  color: white;
  padding: 20px;
  position: fixed;
  top: 0;
  left: 0;
  height: 100%;
}
.main-content {
  margin-left:280px;
  width:100%; /* Remaining space after sidebar */
  min-height: 100vh;
  background-color: #f8f9fa;
  overflow-x: auto;
}
.sidebar ul {
  list-style-type: none;
  padding: 0;
  margin: 0;
}

.sidebar li {
  margin-bottom: 10px;
}

.sidebar a {
  text-decoration: none;
  display: block;
  padding: 10px;
  color: white;
  font-size: 16px;
  font-weight: bold;
}

.sidebar a:hover {
  background-color: transparent;
  color: #4caf50;
}

.dropdown-menu {
  padding-left: 20px;
}

.dropdown a {
  cursor: pointer;
}

/* Header */
header h1 {
  margin-bottom: 5px;
}


    table {
      width: 80%;
      border-collapse: collapse;
    }

    th, td {
      border: 1px solid #ddd;
      padding: 10px;
      text-align: left;
    }

    th {
      background-color: #f2f2f2;
    }

    .action-btn {
      padding: 5px 10px;
      color: white;
      background-color: green;
      border: none;
      border-radius: 3px;
      text-decoration: none;
    }

    .no-data {
      text-align: center;
      color: red;
    }
    .container {
      display: flex;
      width: 100%;
    }
  </style>
</head>
<body>
<?php include "admin_sidebar.php"; ?>


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
        $client_id = $_GET['client_id'];
        $mysqli = new mysqli("localhost", "root", "", "banking");

        if ($mysqli->connect_error) {
            die("Connection failed: " . $mysqli->connect_error);
        }

        $query = "SELECT acc_name, account_number, acc_type, acc_rates, created_at FROM bankaccounts WHERE client_id = ?";
        $stmt = $mysqli->prepare($query);
        $stmt->bind_param('i', $client_id);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
          $cnt = 1;
          while ($row = $result->fetch_assoc()) {
            echo "<tr>";
            echo "<td>" . $cnt . "</td>";
            echo "<td>" . $row['acc_name'] . "</td>";
            echo "<td>" . $row['account_number'] . "</td>";
            echo "<td>" . $row['acc_type'] . "</td>";
            echo "<td>" . $row['acc_rates'] . "%</td>";
            echo "<td>" . date("d-M-Y", strtotime($row['created_at'])) . "</td>";
            echo "<td><a class='action-btn' href='check_bankbalance.php?account_number=" . $row['account_number'] . "'>Check Balance</a></td>";
            echo "</tr>";
            $cnt++;
          }
        } else {
          echo "<tr><td colspan='7' class='no-data'>No bank accounts found for this client.</td></tr>";
        }

        $stmt->close();
        $mysqli->close();
        ?>
      </tbody>
    </table>
  </div>
</body>
</html>