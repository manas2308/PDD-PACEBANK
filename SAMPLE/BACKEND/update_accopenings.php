<?php
$host = 'localhost';
$dbname = 'banking';
$username = 'root';
$password = '';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $accountId = isset($_GET['id']) ? intval($_GET['id']) : 0;
    $accountData = [];
    $accountTypes = [];
    $message = ''; // Variable to store success or error messages

    if ($accountId > 0) {
        // Fetch account details
        $stmt = $pdo->prepare("SELECT * FROM bankaccounts WHERE account_id = :id");
        $stmt->bindParam(':id', $accountId);
        $stmt->execute();
        $accountData = $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // Fetch account types for the dropdown
    $stmt = $pdo->query("SELECT acctype_id, name FROM acc_types");
    $accountTypes = $stmt->fetchAll(PDO::FETCH_ASSOC);

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        // Handle account update
        $acc_name = $_POST['acc_name'] ?? '';
        $account_number = $_POST['account_number'] ?? '';
        $acc_type = $_POST['acc_type'] ?? '';

        $stmt = $pdo->prepare("UPDATE bankaccounts SET 
            acc_name = :acc_name, 
            account_number = :account_number, 
            acc_type = :acc_type 
            WHERE account_id = :id");

        $stmt->bindParam(':acc_name', $acc_name);
        $stmt->bindParam(':account_number', $account_number);
        $stmt->bindParam(':acc_type', $acc_type);
        $stmt->bindParam(':id', $accountId);

        if ($stmt->execute()) {
            $message = "Account updated successfully.";
        } else {
            $message = "Failed to update account.";
        }
    }
} catch (PDOException $e) {
    $message = "Database error: " . $e->getMessage();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Update iBanking Account</title>
    <link rel="stylesheet" href="manage_acc_openings.css">
    <link rel="stylesheet" href="update_accopenings.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
</head>
<body>

<?php include "admin_sidebar.php" ;?>

<h1>Update iBanking Account</h1>

<!-- Display success or error message -->
<?php if (!empty($message)): ?>
    <div class="alert">
        <p><?= $message ?></p>
    </div>
<?php endif; ?>

<!-- Form to update account details -->
<form method="POST">
    <label>Client Name:</label>
    <input type="text" name="client_name" value="<?= $accountData['client_name'] ?? '' ?>" readonly><br>

    <label>Client National ID:</label>
    <input type="text" name="client_national_id" value="<?= $accountData['client_national_id'] ?? '' ?>" readonly><br>

    <label>Client Phone Number:</label>
    <input type="text" name="client_phoneno" value="<?= $accountData['client_phoneno'] ?? '' ?>" readonly><br>

    <label>Client Number:</label>
    <input type="text" name="client_number" value="<?= $accountData['client_number'] ?? '' ?>" readonly><br>

    <label>Client Email:</label>
    <input type="email" name="client_email" value="<?= $accountData['client_email'] ?? '' ?>" readonly><br>

    <label>Client Address:</label>
    <textarea name="client_adr" readonly><?= $accountData['client_adr'] ?? '' ?></textarea><br>

    <label>Account Name:</label>
    <input type="text" name="acc_name" value="<?= $accountData['acc_name'] ?? '' ?>" required><br>

    <label>Account Number:</label>
    <input type="text" name="account_number" value="<?= $accountData['account_number'] ?? '' ?>" required><br>

    <label>Account Type:</label>
    <select name="acc_type" required>
        <option value="">Select Account Type</option>
        <?php foreach ($accountTypes as $type): ?>
            <option value="<?= $type['name'] ?>" <?= ($accountData['acc_type'] ?? '') === $type['name'] ? 'selected' : '' ?>>
                <?= $type['name'] ?>
            </option>
        <?php endforeach; ?>
    </select><br>

    <label>Account Type Rates (%):</label>
    <input type="number" name="acc_rates" value="<?= $accountData['acc_rates'] ?? '' ?>" readonly><br>

    <button type="submit">Update iBanking Account</button>
</form>

<script>
    // JavaScript code to handle pop-up after form submission if needed
    <?php if (!empty($message)): ?>
        alert("<?= $message ?>");
    <?php endif; ?>
</script>

</body>
</html>
