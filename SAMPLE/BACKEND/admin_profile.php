<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Profile</title>
    <link rel="stylesheet" href="admin_profile.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
</head>
<body>
    <?php
    // Connect to the database
    $servername = "localhost";
    $username = "root";
    $password = "";
    $dbname = "banking"; // Replace with your actual database name

    $conn = new mysqli($servername, $username, $password, $dbname);

    // Check connection
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    // Fetch Admin Details for Profile
    $admin_id = 2 ; // Assuming admin_id is 1 for demonstration
    $sql = "SELECT admin_id, name, email, number, password, profile_pic FROM admin WHERE admin_id = $admin_id";
    $result = $conn->query($sql);

    // Check if the result exists
    if ($result && $result->num_rows > 0) {
        $admin = $result->fetch_assoc();
    } else {
        $admin = null;
        echo "<script>alert('No admin found with the provided admin_id');</script>";
    }

    // Debug: Display the fetched password to ensure it's correctly retrieved
    // (Make sure to remove this debug line later)
    echo "<script>console.log('Fetched Password: " . $admin['password'] . "');</script>";

    // Handle Profile Update
    if (isset($_POST['update_profile'])) {
        $name = $_POST['name'];
        $email = $_POST['email'];
        $number = $_POST['number'];

        // Update query
        $update_sql = "UPDATE admin SET name='$name', email='$email', number='$number' WHERE admin_id=$admin_id";
        if ($conn->query($update_sql) === TRUE) {
            echo "<script>alert('Profile updated successfully');</script>";
            // Refresh page to show updated profile info
            header("Refresh:0");
        } else {
            echo "Error updating profile: " . $conn->error;
        }
    }

    // Handle Password Change
    if (isset($_POST['change_password'])) {
        $old_password = $_POST['old_password'];
        $new_password = $_POST['new_password'];
        $confirm_password = $_POST['confirm_password'];

        // Make sure we have the correct current password in the $admin array
        if (isset($admin['password']) && !empty($admin['password'])) {
            // Verify the old password (plain text comparison since passwords are stored as plain text)
            if ($old_password === $admin['password']) {
                if ($new_password === $confirm_password) {
                    // Update the new password in the database
                    $update_password_sql = "UPDATE admin SET password='$new_password' WHERE admin_id=$admin_id";
                    if ($conn->query($update_password_sql) === TRUE) {
                        echo "<script>alert('Password changed successfully');</script>";
                    } else {
                        echo "Error changing password: " . $conn->error;
                    }
                } else {
                    echo "<script>alert('New passwords do not match');</script>";
                }
            } else {
                echo "<script>alert('Old password is incorrect');</script>";
            }
        } else {
            echo "<script>alert('Error fetching current password. Please try again.');</script>";
        }
    }

    $conn->close();
    ?>

    <div class="container">
  <?php include "admin_sidebar.php";?>


        <div class="main-content">
            <header>
                <div class="page-title">System Administrator Profile</div>
                <div class="breadcrumbs">Dashboard / Profile / System Administrator</div>
            </header>

            <div class="profile-section">
                <!-- Form for Updating Profile -->
                <form method="post" action="">
                    <div class="update-section">
                        <h2>Update Profile</h2>
                        <label for="name">Name:</label>
                        <input type="text" name="name" value="<?php echo $admin['name'] ?? ''; ?>" required>
                        <label for="email">Email:</label>
                        <input type="email" name="email" value="<?php echo $admin['email'] ?? ''; ?>" required>
                        <label for="number">Number:</label>
                        <input type="text" name="number" value="<?php echo $admin['number'] ?? ''; ?>" required>
                        <button type="submit" name="update_profile" class="update-btn">Update Profile</button>
                    </div>
                </form>

                <!-- Form for Changing Password -->
                <form method="post" action="">
                    <div class="password-section">
                        <h2>Change Password</h2>
                        <label for="old_password">Old Password:</label>
                        <input type="password" name="old_password" required>
                        <label for="new_password">New Password:</label>
                        <input type="password" name="new_password" required>
                        <label for="confirm_password">Confirm New Password:</label>
                        <input type="password" name="confirm_password" required>
                        <button type="submit" name="change_password" class="change-btn">Change Password</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</body>
</html>
