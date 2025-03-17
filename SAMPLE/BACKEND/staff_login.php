<?php
// Include database connection
include('db_connection.php');

// Start the session
session_start();

// Check if the form is submitted
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Get form data
    $email = trim($_POST['email']);
    $password = trim($_POST['password']);

    // Prepare and execute the SQL query to check if email exists
    $query = "SELECT * FROM staff WHERE email = ? LIMIT 1";
    $stmt = $conn->prepare($query);

    // Check if query preparation failed
    if (!$stmt) {
        die("Query preparation failed: " . $conn->error);
    }

    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows == 1) {
        $staff = $result->fetch_assoc();

        // Verify the password directly (not hashed)
        if ($password === $staff['password']) {
            // Login successful, set session variables
            $_SESSION['staff_id'] = $staff['id'];
            $_SESSION['staff_email'] = $staff['email'];

            header("Location: ../FRONTEND/staff_dashboard.html");
            exit();
        } else {
            echo "<script>alert('Invalid password. Please try again.'); window.history.back();</script>";
        }
    } else {
        echo "<script>alert('Invalid email. Please try again.'); window.history.back();</script>";
    }

    // Close the statement and connection
    $stmt->close();
    $conn->close();
}
?>
