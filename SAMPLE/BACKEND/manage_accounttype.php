<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Account Types</title>
    <link rel="stylesheet" href="manage_account.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
</head>
<body>
    <?php
    // Database connection
    $servername = "localhost";
    $username = "root"; // Update with your DB credentials
    $password = "";
    $dbname = "banking"; // Update with your DB name

    $conn = new mysqli($servername, $username, $password, $dbname);

    // Check connection
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    // Check if delete request has been made for an account type
    if (isset($_POST['delete_acctype_id'])) {
        $acctype_id_to_delete = $_POST['delete_acctype_id'];

        // SQL to delete the account type
        $delete_sql = "DELETE FROM acc_types WHERE acctype_id = ?";
        $stmt = $conn->prepare($delete_sql);
        $stmt->bind_param("i", $acctype_id_to_delete);

        if ($stmt->execute()) {
            echo "<script>alert('Account type deleted successfully.');</script>";
        } else {
            echo "<script>alert('Error deleting account type.');</script>";
        }

        $stmt->close();
    }

    // Fetch account types from the database, without the description field
    $sql = "SELECT acctype_id, name, rate, code FROM acc_types";
    $result = $conn->query($sql);
    ?>

    <div class="container">
  <?php include "admin_sidebar.php"; ?>

        <div class="main-content">
            <header>
                <h1>Manage Account Types</h1>
                <p>Manage your account types below</p>
            </header>

            <div class="search-container">
                <input type="text" id="searchInput" placeholder="Search for account types..">
                <button id="searchButton" onclick="searchAccountType()">Search</button>
            </div>

            <table id="accountTable">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Name</th>
                        <th>Rate (%)</th>
                        <th>Code</th>
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
                            echo "<td>" . $row['rate'] . "</td>";
                            echo "<td>" . $row['code'] . "</td>";
                            echo "<td>
                                    <a href='update_acctype.php?acctype_id=" . $row['acctype_id'] . "' class='manage-btn'>Manage</a>
                                    <form method='POST' onsubmit='return confirmDelete()' style='display:inline;'>
                                        <input type='hidden' name='delete_acctype_id' value='" . $row['acctype_id'] . "' />
                                        <button type='submit' class='delete-btn'>Delete</button>
                                    </form>
                                  </td>";
                            echo "</tr>";
                        }
                    } else {
                        echo "<tr><td colspan='5'>No account types found</td></tr>";
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
        // Confirmation before deleting account type
        function confirmDelete() {
            return confirm('Are you sure you want to delete this account type?');
        }

        // Search Functionality
        function searchAccountType() {
            var input, filter, table, tr, td, i, txtValue;
            input = document.getElementById("searchInput");
            filter = input.value.toUpperCase();
            table = document.getElementById("accountTable");
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
                    var tbody = document.querySelector('#accountTable tbody');
                    noMatchRow = document.createElement('tr');
                    noMatchRow.id = 'noMatchRow';
                    noMatchRow.innerHTML = '<td colspan="5">No matching account type found</td>';
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
    $conn->close();
    ?>
</body>
</html>
