<?php
session_start();
include('conf/config.php'); // Includes the $mysqli connection
include('conf/check_login.php');
check_login(); // Ensure the user is logged in

$staff_id = $_SESSION['staff_id']; // Get logged-in staff ID

// Check if delete request has been made for a client
if (isset($_POST['delete_client_id'])) {
    $client_id_to_delete = $_POST['delete_client_id'];

    // SQL to delete the client
    $delete_sql = "DELETE FROM clients WHERE client_id = ?";
    $stmt = $mysqli->prepare($delete_sql);
    $stmt->bind_param("i", $client_id_to_delete);

    if ($stmt->execute()) {
        echo "<script>alert('Client deleted successfully.');</script>";
    } else {
        echo "<script>alert('Error deleting client.');</script>";
    }

    $stmt->close();
}

// Fetch client data from the database (including client_id for deletion)
$sql = "SELECT client_id, name, national_id, phone, address, email FROM clients";
$result = $mysqli->query($sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Clients</title>
    <link rel="stylesheet" href="staff_manage_client.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
</head>
<body>
    <div class="container">
  <?php include "staff_sidebar.php" ; ?>
        <div class="main-content">
            <header>
                <h1>Pace Banking Clients</h1>
                <p>Select any action to manage your clients</p>
            </header>

            <!-- Search Input and Button -->
            <div class="search-container">
                <input type="text" id="searchInput" placeholder="Search for clients..">
                <button id="searchButton" onclick="searchClient()">Search</button>
            </div>

            <table id="clientTable">
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
                    if ($result->num_rows > 0) {
                        $counter = 1;
                        while ($row = $result->fetch_assoc()) {
                            echo "<tr>";
                            echo "<td>" . $counter++ . "</td>";
                            echo "<td>" . $row['name'] . "</td>";
                            echo "<td>" . $row['national_id'] . "</td>";
                            echo "<td>" . $row['phone'] . "</td>";
                            echo "<td>" . $row['address'] . "</td>";
                            echo "<td>" . $row['email'] . "</td>";
                            echo "<td>
                                    <a href='staff_client_profile.php?client_id=" . $row['client_id'] . "' class='manage-btn'>Manage</a>
                                    <form method='POST' onsubmit='return confirmDelete()' style='display:inline;'>
                                        <input type='hidden' name='delete_client_id' value='" . $row['client_id'] . "' />
                                        <button type='submit' class='delete-btn'>Delete</button>
                                    </form>
                                  </td>";
                            echo "</tr>";
                        }
                    } else {
                        echo "<tr><td colspan='7'>No clients found</td></tr>";
                    }
                    ?>
                </tbody>
            </table>

            <div class="pagination">
                <button>Previous</button>
                <button>1</button>
                <button>Next</button>
            </div>
        </div>
    </div>

    <script>
        // Confirmation before deleting client
        function confirmDelete() {
            return confirm('Are you sure you want to delete this client?');
        }

        // Search Functionality
        function searchClient() {
            var input, filter, table, tr, td, i, txtValue;
            input = document.getElementById("searchInput");
            filter = input.value.toUpperCase();
            table = document.getElementById("clientTable");
            tr = table.getElementsByTagName("tr");

            var found = false;

            // Loop through all table rows, and hide those who don't match the search query
            for (i = 1; i < tr.length; i++) { // Skip the header row (i=0)
                tr[i].style.display = "none"; // Default to hide rows
                td = tr[i].getElementsByTagName("td");

                for (var j = 0; j < td.length; j++) { // Loop through each column in the row
                    if (td[j]) {
                        txtValue = td[j].textContent || td[j].innerText;
                        if (txtValue.toUpperCase().indexOf(filter) > -1) {
                            tr[i].style.display = ""; // Show row if match found
                            found = true;
                            break;
                        }
                    }
                }
            }

            // If no matching records found, show a message
            if (!found) {
                var noMatchRow = document.getElementById('noMatchRow');
                if (!noMatchRow) {
                    var tbody = document.querySelector('#clientTable tbody');
                    noMatchRow = document.createElement('tr');
                    noMatchRow.id = 'noMatchRow';
                    noMatchRow.innerHTML = '<td colspan="7">No matching client found</td>';
                    tbody.appendChild(noMatchRow);
                } else {
                    noMatchRow.style.display = "";
                }
            } else {
                var noMatchRow = document.getElementById('noMatchRow');
                if (noMatchRow) {
                    noMatchRow.style.display = "none";
                }
            }
        }
    </script>

    <?php
    $mysqli->close();
    ?>
</body>
</html>
