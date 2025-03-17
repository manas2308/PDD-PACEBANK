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

// Delete staff if the delete button is clicked
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_staff_id'])) {
    $staff_id = $_POST['delete_staff_id'];

    // Prepare and execute the DELETE query
    $delete_sql = "DELETE FROM staff WHERE staff_id = ?";
    $stmt = $conn->prepare($delete_sql);
    $stmt->bind_param("i", $staff_id);

    if ($stmt->execute()) {
        echo "<script>alert('Staff member deleted successfully!');</script>";
    } else {
        echo "<script>alert('Error deleting staff member: " . $conn->error . "');</script>";
    }

    $stmt->close();
}

// Fetch staff data from the database
$sql = "SELECT `staff_id`, `name`, `staff_number`, `phone`, `email`, `sex` FROM `staff`";
$result = $conn->query($sql);

// Check if query execution was successful
if (!$result) {
    die("Error executing query: " . $conn->error);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Staff</title>
    <link rel="stylesheet" href="manage_staff.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
</head>
<body>
    <div class="container">
  <?php include "admin_sidebar.php"; ?>

        <div class="main-content">
            <header>
                <h1>Manage Staff</h1>
                <p>Select any action to manage your staff members</p>
            </header>

            <div class="search-container">
                <input type="text" id="searchInput" placeholder="Search for staff..">
                <button id="searchButton" onclick="searchStaff()">Search</button>
            </div>

            <table id="staffTable">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Name</th>
                        <th>Staff Number</th>
                        <th>Phone</th>
                        <th>Email</th>
                        <th>Sex</th>
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
                            echo "<td>" . $row['staff_number'] . "</td>";
                            echo "<td>" . $row['phone'] . "</td>";
                            echo "<td>" . $row['email'] . "</td>";
                            echo "<td>" . $row['sex'] . "</td>";
                            echo "<td>
                                    <a href='admin_staff_profile.php?staff_id=" . $row['staff_id'] . "' class='manage-btn'>Manage</a>
                                    <form method='POST' onsubmit='return confirmDelete()' style='display:inline;'>
                                        <input type='hidden' name='delete_staff_id' value='" . $row['staff_id'] . "' />
                                        <button type='submit' class='delete-btn'>Delete</button>
                                    </form>
                                  </td>";
                            echo "</tr>";
                        }
                    } else {
                        echo "<tr><td colspan='7'>No staff found</td></tr>";
                    }
                    ?>
                </tbody>
            </table>
        </div>
    </div>

    <script>
        // Confirmation popup before deleting a staff member
        function confirmDelete() {
            return confirm('Are you sure you want to delete this staff member?');
        }

        // Search functionality
        function searchStaff() {
            var input, filter, table, tr, td, i, txtValue;
            input = document.getElementById("searchInput");
            filter = input.value.toUpperCase();
            table = document.getElementById("staffTable");
            tr = table.getElementsByTagName("tr");

            var found = false;

            for (i = 1; i < tr.length; i++) {
                tr[i].style.display = "none";
                td = tr[i].getElementsByTagName("td");

                for (var j = 0; j < td.length; j++) {
                    if (td[j]) {
                        txtValue = td[j].textContent || td[j].innerText;
                        if (txtValue.toUpperCase().indexOf(filter) > -1) {
                            tr[i].style.display = "";
                            found = true;
                            break;
                        }
                    }
                }
            }

            if (!found) {
                var noMatchRow = document.getElementById('noMatchRow');
                if (!noMatchRow) {
                    var tbody = document.querySelector('#staffTable tbody');
                    noMatchRow = document.createElement('tr');
                    noMatchRow.id = 'noMatchRow';
                    noMatchRow.innerHTML = '<td colspan="7">No matching staff found</td>';
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
</body>
</html>
<?php
$conn->close();
?>
