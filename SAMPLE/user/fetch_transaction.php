<?php
include 'config.php';

if (isset($_POST['transaction_id'])) {
    $transaction_id = $_POST['transaction_id'];

    $sql = "SELECT tr_code, account_id, acc_name, account_number, acc_type, acc_amount, tr_type, created_at, receiving_acc_name, receiving_acc_holder 
            FROM transactions 
            WHERE tr_id = ?";
    
    $stmt = $mysqli->prepare($sql);
    $stmt->bind_param("i", $transaction_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $transaction = $result->fetch_assoc();
    $stmt->close();

    if ($transaction) {
        echo json_encode($transaction);
    } else {
        echo json_encode(["error" => "Transaction not found"]);
    }
}

?>
