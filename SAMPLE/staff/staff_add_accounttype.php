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

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $_POST['name'];
    $rate = $_POST['rate'];
    $description = $_POST['description'];
    $code = $_POST['code'];

    $stmt = $mysqli->prepare("INSERT INTO acc_types (name, description, rate, code) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("ssss", $name, $description, $rate, $code);

    if ($stmt->execute()) {
        echo "<script>alert('Account Type added successfully!');</script>";
    } else {
        echo "<script>alert('Error: " . $stmt->error . "');</script>";
    }

    $stmt->close();
}

$mysqli->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create Account Category</title>
    <link rel="stylesheet" href="staff_add_accounttype.css">
    <script src="https://cdn.ckeditor.com/4.16.2/standard/ckeditor.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
</head>
<body>
<?php include "staff_sidebar.php" ; ?>

<div class="container1">
    <h2>Create Account Categories</h2>
    <form id="accountForm" method="POST">
        <div class="form-group">
            <label for="categoryName">Account Category Name</label>
            <input type="text" id="categoryName" name="name" required>
        </div>
        <div class="form-group">
            <label for="categoryRate">Account Category Rate % Per Year</label>
            <input type="number" step="0.01" id="categoryRate" name="rate" required>
        </div>
        <div class="form-group">
            <label for="categoryCode">Account Category Code</label>
            <input type="text" id="categoryCode" name="code" value="ACC-CAT-<?php echo substr(str_shuffle('ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789'), 0, 6); ?>" readonly>
        </div>
        <div class="form-group">
            <label for="categoryDescription">Account Category Description</label>
            <textarea id="categoryDescription" name="description" required></textarea>
        </div>
        <button type="submit" id="submitBtn">Add Account Type</button>
    </form>
</div>

<script>
    CKEDITOR.replace('categoryDescription');
</script>
</body>
</html>