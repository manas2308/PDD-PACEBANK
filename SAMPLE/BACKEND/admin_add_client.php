<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create Client Account</title>
    <link rel="stylesheet" href="addclient.css"> 
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css"><!-- Separate CSS file for sidebar and form styling -->
</head>
<body>
    <?php
    // Database connection
    $servername = "localhost";
    $username = "root";
    $password = "";
    $dbname = "banking";

    $conn = new mysqli($servername, $username, $password, $dbname);

    // Check connection
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    // Add client to database
    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        $client_name = $_POST['client_name'];
        $contact = $_POST['contact'];
        $email = $_POST['email'];
        $address = $_POST['address'];
        $national_id = $_POST['national_id'];
        $password = $_POST['password'];

        $hashed_password = password_hash($password, PASSWORD_DEFAULT); // Hash the password for security

        // Insert client details into the clients table
        $sql = "INSERT INTO clients (name, national_id, phone, address, email, password) 
                VALUES (?, ?, ?, ?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ssssss", $client_name, $national_id, $contact, $address, $email, $hashed_password);

        if ($stmt->execute()) {
            echo "<script>alert('Client account added successfully');</script>";
        } else {
            echo "Error: " . $stmt->error;
        }
    }

    $conn->close();
    ?>

    <div class="container">
    <?php include "admin_sidebar.php"; ?>
        <div class="main-content">
            <header>
                <div class="page-title">Create Client Account</div>
                <div class="breadcrumbs">Dashboard / Clients / Add</div>
            </header>

            <div class="form-section">
                <h2>Create Client Account</h2>
                <form method="post" action="">
                    <div class="form-group">
                        <label for="client_name">Client Name:</label>
                        <input type="text" name="client_name" required>
                    </div>

                    <div class="form-group">
                        <label for="contact">Contact:</label>
                        <input type="text" name="contact" required>
                    </div>

                    <div class="form-group">
                        <label for="email">Email:</label>
                        <input type="email" name="email" required>
                    </div>

                    <div class="form-group">
                        <label for="national_id">National ID No.:</label>
                        <input type="text" name="national_id" required>
                    </div>

                    <div class="form-group">
                        <label for="password">Password:</label>
                        <input type="password" name="password" required>
                    </div>

                    <div class="form-group">
                        <label for="address">Address:</label>
                        <input type="text" name="address" required>
                    </div>

                    <button type="submit">Add Client</button>
                </form>
            </div>
        </div>
    </div>

</body>
</html>
