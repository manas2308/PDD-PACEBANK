<?php
session_start();
include('conf/config.php');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $account_id = intval($_POST['account_id']);
    $entered_pin = trim($_POST['pin']);

    // Fetch the stored PIN for the given account ID
    $stmt = $mysqli->prepare("SELECT pin FROM bankaccounts WHERE account_id = ?");
    $stmt->bind_param("i", $account_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $account = $result->fetch_assoc();

    if ($account) {
        // Debugging: print out the entered and stored pins to ensure they're correct
        // echo "Entered PIN: " . $entered_pin . "<br>";
        // echo "Stored PIN: " . $account['pin'] . "<br>";

        // Directly compare the entered PIN with the stored PIN (since it's not hashed)
        if ($entered_pin === $account['pin']) {
            echo 'success'; // The entered PIN is correct
        } else {
            echo 'error'; // Incorrect PIN
        }
    } else {
        echo 'error'; // Account not found
    }
}

$mysqli->close();
?>
