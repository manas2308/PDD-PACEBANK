<?php
// Database connection
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "banking";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Fetch account type details
if (isset($_GET['acctype_id'])) {
    $acctype_id = $_GET['acctype_id'];
    
    $stmt = $conn->prepare("SELECT name, description, rate, code FROM acc_types WHERE acctype_id = ?");
    $stmt->bind_param("i", $acctype_id);
    $stmt->execute();
    $stmt->bind_result($name, $description, $rate, $code);
    $stmt->fetch();
    $stmt->close();
}

// Handle update form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $_POST['name'];
    $rate = $_POST['rate'];
    $description = $_POST['description'];
    $code = $_POST['code'];
    $acctype_id = $_POST['acctype_id'];

    $stmt = $conn->prepare("UPDATE acc_types SET name = ?, description = ?, rate = ?, code = ? WHERE acctype_id = ?");
    $stmt->bind_param("sssdi", $name, $description, $rate, $code, $acctype_id);

    if ($stmt->execute()) {
        echo "<script>alert('Account Type updated successfully!'); window.location.href='manage_accounttype.php';</script>";
    } else {
        echo "<script>alert('Error: " . $stmt->error . "');</script>";
    }

    $stmt->close();
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Update Account Type</title>
    <link rel="stylesheet" href="addaccount.css">
    <script src="https://cdn.ckeditor.com/4.16.2/standard/ckeditor.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
</head>
<body>
<div class="sidebar">
    <div class="logo">iBank Dashboard</div>
    <hr style="border: 1px solid black; width: 100%;">
    <ul class="nav">
        <li><a href="../FRONTEND/admin_dashboard.php"><i class="fas fa-tachometer-alt"></i> Dashboard</a></li>
        <li><a href="../BACKEND/admin_profile.php"><i class="fas fa-user-circle"></i> Profile</a></li>
        <li class="dropdown">
            <a href="#"><i class="fas fa-users"></i> Clients ▾</a>
            <ul class="dropdown-menu">
                <li><a href="../BACKEND/admin_add_client.php"><i class="fas fa-user-plus"></i> Add Client</a></li>
                <li><a href="../BACKEND/admin_manage_client.php"><i class="fas fa-user-cog"></i> Manage Client</a></li>
            </ul>
        </li>
        <li class="dropdown">
            <a href="#"><i class="fas fa-user-tie"></i> Staff ▾</a>
            <ul class="dropdown-menu">
                <li><a href="../BACKEND/admin_staff.php"><i class="fas fa-user-plus"></i> Add Staff</a></li>
                <li><a href="../BACKEND/admin_manage_staff.php"><i class="fas fa-user-cog"></i> Manage Staff</a></li>
            </ul>
        </li>
        <li class="dropdown">
            <a href="#"><i class="fas fa-university"></i> Accounts ▾</a>
            <ul class="dropdown-menu">
                <li><a href="../BACKEND/add_account_type.php"><i class="fas fa-plus-circle"></i> Add Account type</a></li>
                <li><a href="../BACKEND/manage_accounttype.php"><i class="fas fa-edit"></i> Manage Account type</a></li>
                <li><a href="../BACKEND/admin_open_account.php"><i class="fas fa-folder-plus"></i> Open Acc</a></li>
                <li><a href="../BACKEND/manage_acc_openings.php"><i class="fas fa-folder-open"></i> Manage Acc openings</a></li>
            </ul>
        </li>
        <li class="dropdown">
            <a href="#"><i class="fas fa-dollar-sign"></i> Finances ▾</a>
            <ul class="dropdown-menu">
                <li><a href="../BACKEND/pages_deposits.php"><i class="fas fa-piggy-bank"></i> Deposits</a></li>
                <li><a href="../BACKEND/pages_withdrawal.php"><i class="fas fa-wallet"></i> Withdrawals</a></li>
                <li><a href="../BACKEND/transfer_details.php"><i class="fas fa-exchange-alt"></i> Transfers</a></li>
                <li><a href="../BACKEND/balance_details.php"><i class="fas fa-balance-scale"></i> Balance Enquiries</a></li>
            </ul>
        </li>
        <li><a href="../BACKEND/admintransactions.php"><i class="fas fa-money-check-alt"></i> Transactions</a></li>
        <h4>Advanced Modules</h4>
        <li class="dropdown">
            <a href="#"><i class="fas fa-file-alt"></i> Financial Reports ▾</a>
            <ul class="dropdown-menu">
                <li><a href="../BACKEND/transaction_deposit.php"><i class="fas fa-file-invoice-dollar"></i> Deposits</a></li>
                <li><a href="../BACKEND/transaction_withdrawal.php"><i class="fas fa-file-invoice-dollar"></i> Withdrawals</a></li>
                <li><a href="../BACKEND/transaction_transfer.php"><i class="fas fa-file-invoice-dollar"></i> Transfers</a></li>
            </ul>
        </li>
        <li><a href="#"><i class="fas fa-cogs"></i> System Settings</a></li>
        <li><a href="../BACKEND/admin_logout.php"><i class="fas fa-sign-out-alt"></i> Log Out</a></li>
    </ul>
</div>

<div class="container1">
    <h2>Update Account Type</h2>
    <form method="POST">
        <input type="hidden" name="acctype_id" value="<?php echo $acctype_id; ?>">
        <div class="form-group">
            <label for="categoryName">Account Type Name</label>
            <input type="text" id="categoryName" name="name" value="<?php echo $name; ?>" required>
        </div>
        <div class="form-group">
            <label for="categoryRate">Rate % Per Year</label>
            <input type="number" step="0.01" id="categoryRate" name="rate" value="<?php echo $rate; ?>" required>
        </div>
        <div class="form-group">
            <label for="categoryCode">Code</label>
            <input type="text" id="categoryCode" name="code" value="<?php echo $code; ?>" required readonly>
        </div>
        <div class="form-group">
            <label for="categoryDescription">Description</label>
            <textarea id="categoryDescription" name="description" required><?php echo $description; ?></textarea>
        </div>
        <button type="submit">Update Account Type</button>
    </form>
</div>

<script>
    // Initialize CKEditor for description field
    CKEDITOR.replace('categoryDescription');
</script>
</body>
</html>
