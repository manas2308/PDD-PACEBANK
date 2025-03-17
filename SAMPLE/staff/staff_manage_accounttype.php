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

// Check if delete request has been made for an account type
if (isset($_POST['delete_acctype_id'])) {
    $acctype_id_to_delete = $_POST['delete_acctype_id'];
    $delete_sql = "DELETE FROM acc_types WHERE acctype_id = ?";
    $stmt = $mysqli->prepare($delete_sql);
    $stmt->bind_param("i", $acctype_id_to_delete);

    if ($stmt->execute()) {
        echo "<script>alert('Account type deleted successfully.');</script>";
    } else {
        echo "<script>alert('Error deleting account type.');</script>";
    }
    $stmt->close();
}

// Fetch account types from the database, excluding "Organization account"
$sql = "SELECT acctype_id, name,  rate, code FROM acc_types WHERE name != 'Organization account'";
$result = $mysqli->query($sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Account Types</title>
    <link rel="stylesheet" href="staff_manage_accounttype.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
</head>
<body>
    <div class="container">
    <?php include "staff_sidebar.php" ; ?>
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
                            echo "<td>" . htmlspecialchars($row['name']) . "</td>";
                            echo "<td>" . htmlspecialchars($row['rate']) . "</td>";
                            echo "<td>" . htmlspecialchars($row['code']) . "</td>";
                            echo "<td>
                                    <a href='staff_update_acctype.php?acctype_id=" . $row['acctype_id'] . "' class='manage-btn'>Manage</a>
                                    <form method='POST' onsubmit='return confirmDelete()' style='display:inline;'>
                                        <input type='hidden' name='delete_acctype_id' value='" . $row['acctype_id'] . "' />
                                        <button type='submit' class='delete-btn'>Delete</button>
                                    </form>
                                  </td>";
                            echo "</tr>";
                        }
                    } else {
                        echo "<tr><td colspan='6'>No account types found</td></tr>";
                    }
                    ?>
                </tbody>
            </table>
        </div>
    </div>

    <script>
        function confirmDelete() {
            return confirm('Are you sure you want to delete this account type?');
        }

        function searchAccountType() {
            var input = document.getElementById("searchInput");
            var filter = input.value.toUpperCase();
            var table = document.getElementById("accountTable");
            var tr = table.getElementsByTagName("tr");

            for (var i = 1; i < tr.length; i++) {
                tr[i].style.display = tr[i].innerText.toUpperCase().includes(filter) ? "" : "none";
            }
        }
    </script>

<?php
$mysqli->close();
?>
</body>
</html>
