<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Staff Profile</title>
    <link rel="stylesheet" href="admin_profile.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
</head>
<body>
    <?php
    // Connect to the database
    $servername = "localhost";
    $username = "root";
    $password = "";
    $dbname = "banking";

    $conn = new mysqli($servername, $username, $password, $dbname);

    // Check connection
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    // Fetch specific staff details based on staff_id
    $staff_id = isset($_GET['staff_id']) ? $_GET['staff_id'] : null;

    if ($staff_id) {
        $sql = "SELECT `name`, `staff_number`, `phone`, `email`, `sex`, `password` FROM `staff` WHERE staff_id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $staff_id);
        $stmt->execute();
        $result = $stmt->get_result();

        // Check if staff exists
        if ($result && $result->num_rows > 0) {
            $staff = $result->fetch_assoc();
        } else {
            $staff = null;
            echo "<script>alert('No staff found with the provided staff_id');</script>";
        }
    } else {
        echo "<script>alert('No staff_id provided');</script>";
    }

    // Handle Profile Update
    if (isset($_POST['update_profile'])) {
        $name = $_POST['name'];
        $email = $_POST['email'];
        $phone = $_POST['phone'];
        $sex = $_POST['sex'];

        // Update query for the staff profile
        $update_sql = "UPDATE staff SET name=?, email=?, phone=?, sex=? WHERE staff_id=?";
        $stmt = $conn->prepare($update_sql);
        $stmt->bind_param("ssssi", $name, $email, $phone, $sex, $staff_id);
        
        if ($stmt->execute()) {
            echo "<script>alert('Staff profile updated successfully');</script>";
            header("Refresh:0");
        } else {
            echo "Error updating staff profile: " . $conn->error;
        }
    }

    // Handle Password Update without Hashing
    if (isset($_POST['update_password'])) {
        $old_password = $_POST['old_password'];
        $new_password = $_POST['new_password'];
        $confirm_password = $_POST['confirm_password'];

        // Verify the old password (in plain text)
        if ($old_password === $staff['password']) {
            // Ensure new password and confirm password match
            if ($new_password === $confirm_password) {
                // Update the password in plain text
                $update_password_sql = "UPDATE staff SET password=? WHERE staff_id=?";
                $stmt = $conn->prepare($update_password_sql);
                $stmt->bind_param("si", $new_password, $staff_id);
                
                if ($stmt->execute()) {
                    echo "<script>alert('Password updated successfully');</script>";
                } else {
                    echo "Error updating password: " . $conn->error;
                }
            } else {
                echo "<script>alert('New passwords do not match. Please try again.');</script>";
            }
        } else {
            echo "<script>alert('Old password is incorrect. Please try again.');</script>";
        }
    }

    $conn->close();
    ?>

    <div class="container">
   <?php include "admin_sidebar.php"; ?>


        <div class="main-content">
            <header>
                <div class="page-title">Manage Staff Profile</div>
                <div class="breadcrumbs">Dashboard / Staff / Manage</div>
            </header>

            <div class="profile-section">
                <!-- Form for Updating Staff Profile -->
                <form method="post" action="">
                    <div class="update-section">
                        <h2>Update Staff Profile</h2>
                        <label for="name">Name:</label>
                        <input type="text" name="name" value="<?php echo $staff['name']; ?>" required>

                        <label for="email">Email:</label>
                        <input type="email" name="email" value="<?php echo $staff['email']; ?>" required>

                        <label for="phone">Phone:</label>
                        <input type="text" name="phone" value="<?php echo $staff['phone']; ?>" required>

                        <label for="sex">Gender:</label>
                        <select name="sex">
                            <option value="M" <?php echo ($staff['sex'] == 'M') ? 'selected' : ''; ?>>Male</option>
                            <option value="F" <?php echo ($staff['sex'] == 'F') ? 'selected' : ''; ?>>Female</option>
                        </select>

                        <button type="submit" name="update_profile">Update Profile</button>
                    </div>
                </form>

                <!-- Form for Updating Password -->
                <form method="post" action="">
                    <div class="update-password-section">
                        <h2>Update Password</h2>
                        <label for="old_password">Old Password:</label>
                        <input type="password" name="old_password" required>

                        <label for="new_password">New Password:</label>
                        <input type="password" name="new_password" required>

                        <label for="confirm_password">Confirm New Password:</label>
                        <input type="password" name="confirm_password" required>

                        <button type="submit" name="update_password">Update Password</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</body>
</html>
