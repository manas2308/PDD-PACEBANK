<?php
// Database connection

session_start();
include('conf/config.php'); // Includes the $mysqli connection
include('conf/check_login.php');
check_login(); // Ensure the user is logged in

$staff_id = $_SESSION['staff_id']; // Get logged-in staff ID


// Fetch clients data
$sql = "SELECT client_id, name, national_id, phone, address, email FROM clients"; // Include client_id
$result = $mysqli->query($sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Open iBanking Account</title>
    <link rel="stylesheet" href="staff_open_account.css"> <!-- Link to external CSS -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script> <!-- Ensure jQuery is loaded -->
    <script src="https://cdn.datatables.net/1.10.24/js/jquery.dataTables.min.js"></script>
    <link rel="stylesheet" href="https://cdn.datatables.net/1.10.24/css/jquery.dataTables.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
</head>
<body>
    <div class="wrapper">
    <?php include "staff_sidebar.php" ; ?>
        <!-- Main Content -->
        <div class="main-content">
            <div class="container1">
                <h2>Open An iBanking Account</h2>
                <p>Select a client to open an account</p>
                
                <!-- Search Box -->
                <div class="search-box">
                    <label for="search">Search:</label>
                    <input type="text" id="searchInput" placeholder="Search client...">
                </div>

                <!-- Table with Clients Data -->
                <table id="clientsTable" class="display">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Name</th>
                            <th>Client Number</th>
                            <th>ID No.</th>
                            <th>Contact</th>
                            <th>Email</th>
                            <th>Address</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        if ($result->num_rows > 0) {
                            $count = 1;
                            while ($row = $result->fetch_assoc()) {
                                echo "<tr>";
                                echo "<td>" . $count++ . "</td>";
                                echo "<td>" . $row['name'] . "</td>";
                                echo "<td>iBank-CLIENT-" . rand(1000, 9999) . "</td>";
                                echo "<td>" . $row['national_id'] . "</td>";
                                echo "<td>" . $row['phone'] . "</td>";
                                echo "<td>" . $row['email'] . "</td>";
                                echo "<td>" . $row['address'] . "</td>";
                                // Add hidden field to store client_id and redirect to admin_open_client_account.php with client_id
                                echo "<td>
                                        <form method='get' action='staff_open_client_account.php'>
                                            <input type='hidden' name='client_id' value='" . $row['client_id'] . "' />
                                            <button type='submit' class='open-account-btn'>Open Account</button>
                                        </form>
                                    </td>";
                                echo "</tr>";
                            }
                        } else {
                            echo "<tr><td colspan='8'>No clients found.</td></tr>";
                        }
                        ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <script>
        // DataTables initialization
        $(document).ready(function() {
            var table = $('#clientsTable').DataTable(); // Initialize DataTable

            // Search functionality
            $('#searchInput').on('keyup', function() {
                table.search(this.value).draw(); // Update DataTable search
            });
        });
    </script>
</body>
</html>

<?php
$mysqli->close();
?>
