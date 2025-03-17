<?php
session_start();
include('conf/config.php'); // Includes the $mysqli connection
include('conf/check_login.php');
check_login(); // Ensure the user is logged in

// Fetch logged-in client details using session client_id
$client_id = $_SESSION['client_id']; // Get logged-in client ID
$client_sql = "SELECT name, national_id, phone, address, email FROM clients WHERE client_id = ?";
$stmt = $mysqli->prepare($client_sql);
$stmt->bind_param("i", $client_id);
$stmt->execute();
$client_result = $stmt->get_result();
$client = $client_result->fetch_assoc();
$stmt->close();

// Check if client data was fetched successfully
if (!$client) {
    echo "<script>alert('Client details not found.');</script>";
    exit;
}

// Fetch available account types
$acc_types_sql = "SELECT acctype_id, name FROM acc_types";
$acc_types_result = $mysqli->query($acc_types_sql);

// Handle form submission (client opening their own account)
// Handle form submission (client opening their own account)
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $acc_name = $_POST['account_name'];
    $acc_type_id = $_POST['account_type'];
    $account_number = rand(1000000000, 9999999999); // Generate a random account number

    // Check if the client already has an account of this type
    $check_sql = "SELECT 1 FROM bankaccounts WHERE client_id = ? AND acc_type = (SELECT name FROM acc_types WHERE acctype_id = ?)";
    $stmt_check = $mysqli->prepare($check_sql);
    $stmt_check->bind_param("ii", $client_id, $acc_type_id);
    $stmt_check->execute();
    $stmt_check->store_result();

    if ($stmt_check->num_rows > 0) {
        echo "<script>alert('You already have an account of this type. Please choose a different account type.');</script>";
    } else {
        // Fetch selected account type details
        $acc_type_sql = "SELECT name, rate FROM acc_types WHERE acctype_id = ?";
        $stmt_acc = $mysqli->prepare($acc_type_sql);
        $stmt_acc->bind_param("i", $acc_type_id);
        $stmt_acc->execute();
        $acc_type_result = $stmt_acc->get_result();
        $acc_type = $acc_type_result->fetch_assoc();
        $stmt_acc->close();

        // Insert into bankaccounts table
        $insert_sql = "INSERT INTO bankaccounts (acc_name, account_number, acc_type, acc_rates, status, client_id, client_name, client_national_id, client_phoneno, client_number, client_email, client_adr, created_at) 
                       VALUES (?, ?, ?, ?, 'Active', ?, ?, ?, ?, ?, ?, ?, NOW())";
        $stmt_insert = $mysqli->prepare($insert_sql);
        $stmt_insert->bind_param("sssdissssss", $acc_name, $account_number, $acc_type['name'], $acc_type['rate'], $client_id, $client['name'], $client['national_id'], $client['phone'], $client_id, $client['email'], $client['address']);
        
        if ($stmt_insert->execute()) {
            echo "<script>alert('Account opened successfully!');</script>";
        } else {
            echo "<script>alert('Error opening account.');</script>";
        }
        $stmt_insert->close();
    }
    $stmt_check->close();
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Open New iBanking Account</title>
    <link rel="stylesheet" href="client_open_account.css"> <!-- Link to external CSS -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
</head>
<body>
<?php include("sidebar.php"); ?>
<div class="account-container">
    <h2>Open iBanking Account</h2>
    <div class="account-form">
        <form action="" method="POST">
            <!-- Client Details (Pre-filled) -->
            <div class="form-group">
                <label for="client_name">User Name</label>
                <input type="text" id="client_name" name="client_name" value="<?php echo htmlspecialchars($client['name']); ?>" disabled>
            </div>
            <div class="form-group">
                <label for="client_number"> Phone Number</label>
                <input type="text" id="client_number" value="iBank-CLIENT-<?php echo rand(1000, 9999); ?>" disabled>
            </div>
            <div class="form-group">
                <label for="client_national_id">Aadhar ID No.</label>
                <input type="text" id="client_national_id" value="<?php echo htmlspecialchars($client['national_id']); ?>" disabled>
            </div>
            <div class="form-group">
                <label for="client_address"> Address</label>
                <input type="text" id="client_address" value="<?php echo htmlspecialchars($client['address']); ?>" disabled>
            </div>
            <div class="form-group">
                <label for="client_email">Email</label>
                <input type="email" id="client_email" value="<?php echo htmlspecialchars($client['email']); ?>" disabled>
            </div>
            
            <!-- Account Type and Account Name -->
            <div class="form-group">
                <label for="account_type">Account Type</label>
                <select name="account_type" id="account_type" required>
                    <option value="">Select Account type</option>
                    <?php
                    while ($row = $acc_types_result->fetch_assoc()) {
                        echo "<option value='{$row['acctype_id']}'>{$row['name']}</option>";
                    }
                    ?>
                </select>
            </div>
            <div class="form-group">
                <label for="account_name">Account nick Name</label>
                <input type="text" name="account_name" id="account_name" required>
            </div>
            <div class="form-group">
                <label for="account_number">Account Number</label>
                <input type="text" id="account_number" value="<?php echo rand(1000000000, 9999999999); ?>" disabled>
            </div>
            <div class="form-group">
                <button type="submit" class="open-account-btn">Open iBanking Account</button>
            </div>
        </form>
    </div>
</div>
</body>
</html>

<?php
$mysqli->close();
?>
