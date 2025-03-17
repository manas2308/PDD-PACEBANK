<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">

  <link rel="stylesheet" href="balance_details.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
  <title>Client Details</title>
</head>
<body>
  <div class="container">
    <!-- Sidebar -->
  <?php include "admin_sidebar.php"; ?>

    <!-- Main Content -->
    <div class="main-content">
      <header>
        <h1>Client Details</h1>
        <input type="text" id="search-bar" placeholder="Search clients...">
      </header>
      <table id="client-table">
        <thead>
          <tr>
            <th>#</th>
            <th>Name</th>
            <th>National ID</th>
            <th>Phone</th>
            <th>Address</th>
            <th>Email</th>
            <th>Action</th>
          </tr>
        </thead>
        <tbody>
          <?php
          $mysqli = new mysqli("localhost", "root", "", "banking");
          if ($mysqli->connect_error) {
              die("Connection failed: " . $mysqli->connect_error);
          }
          
          $query = "SELECT client_id, name, national_id, phone, address, email FROM clients ORDER BY name ASC";
          $result = $mysqli->query($query);

          if ($result->num_rows > 0) {
            $cnt = 1;
            while ($row = $result->fetch_assoc()) {
              echo "<tr>";
              echo "<td>" . $cnt . "</td>";
              echo "<td>" . $row['name'] . "</td>";
              echo "<td>" . $row['national_id'] . "</td>";
              echo "<td>" . $row['phone'] . "</td>";
              echo "<td>" . $row['address'] . "</td>";
              echo "<td>" . $row['email'] . "</td>";
              echo "<td><a class='check-balance-btn' href='check_balance.php?client_id=" . $row['client_id'] . "'>Check Bankaccounts</a></td>";
              echo "</tr>";
              $cnt++;
            }
          } else {
            echo "<tr><td colspan='7'>No clients found</td></tr>";
          }
          ?>
        </tbody>
      </table>
    </div>
  </div>

  <script>
    document.getElementById('search-bar').addEventListener('input', function () {
      let filter = this.value.toLowerCase();
      let rows = document.querySelectorAll('#client-table tbody tr');

      rows.forEach(row => {
        let clientName = row.cells[1].textContent.toLowerCase();
        row.style.display = clientName.includes(filter) ? '' : 'none';
      });
    });
  </script>
</body>
</html>