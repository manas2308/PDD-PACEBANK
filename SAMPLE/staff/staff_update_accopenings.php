<?php
session_start();
include 'conf/check_login.php'; // Ensure authentication
include 'conf/config.php'; // Include database configuration
check_login();
$accountId = isset($_GET['id']) ? intval($_GET['id']) : 0;
$accountData = [];
$accountTypes = [];
$message = '';

if ($accountId > 0) {
    // Fetch account details
    $stmt = $mysqli->prepare("SELECT * FROM bankaccounts WHERE account_id = ?");
    $stmt->bind_param('i', $accountId);
    $stmt->execute();
    $accountData = $stmt->get_result()->fetch_assoc();
    $stmt->close();
}

// Fetch account types
$result = $mysqli->query("SELECT acctype_id, name FROM acc_types");
$accountTypes = $result->fetch_all(MYSQLI_ASSOC);
$result->close();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Handle account update
    $acc_name = $_POST['acc_name'] ?? '';
    $account_number = $_POST['account_number'] ?? '';
    $acc_type = $_POST['acc_type'] ?? '';

    $stmt = $mysqli->prepare("UPDATE bankaccounts SET acc_name = ?, account_number = ?, acc_type = ? WHERE account_id = ?");
    $stmt->bind_param('sssi', $acc_name, $account_number, $acc_type, $accountId);

    if ($stmt->execute()) {
        $message = "Account updated successfully.";
    } else {
        $message = "Failed to update account.";
    }
    $stmt->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Update iBanking Account</title>
    <link rel="stylesheet" href="../BACKEND/update_accopenings.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
</head>
<style>
    .container{
        margin-left:300px;
    }
    </style>
<body>
<?php include 'staff_sidebar.php'; ?>
<h1>Update iBanking Account</h1>

<?php if (!empty($message)): ?>
    <div class="alert">
        <p><?= htmlspecialchars($message) ?></p>
    </div>
<?php endif; ?>

<form method="POST">
    <label>Account Name:</label>
    <input type="text" name="acc_name" value="<?= htmlspecialchars($accountData['acc_name'] ?? '') ?>" required><br>

    <label>Account Number:</label>
    <input type="text" name="account_number" value="<?= htmlspecialchars($accountData['account_number'] ?? '') ?>" required><br>

    <label>Account Type:</label>
    <select name="acc_type" required>
        <option value="">Select Account Type</option>
        <?php foreach ($accountTypes as $type): ?>
            <option value="<?= htmlspecialchars($type['name']) ?>" <?= ($accountData['acc_type'] ?? '') === $type['name'] ? 'selected' : '' ?>>
                <?= htmlspecialchars($type['name']) ?>
            </option>
        <?php endforeach; ?>
    </select><br>

    <button type="submit">Update iBanking Account</button>
</form>

<script>
    <?php if (!empty($message)): ?>
        alert("<?= htmlspecialchars($message) ?>");
    <?php endif; ?>
</script>

</body>
</html>
