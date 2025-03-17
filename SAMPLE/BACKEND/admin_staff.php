<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link rel="stylesheet" href="styled.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
  <title>Create Staff Account</title>
</head>
<body>
<div class="container">
<?php include "admin_Sidebar.php"; ?>
  <!-- Main content container -->
  <div class="main-content">
    <h1>Create Staff Account</h1>
    <form id="staffForm" action="" method="POST" enctype="multipart/form-data">
      <label for="staff_name">Staff Name:</label>
      <input type="text" id="staff_name" name="name" required>

      <!-- Staff Number input is hidden since it's generated automatically -->
      <input type="hidden" id="staff_number" name="staff_number">

      <label for="staff_phone">Staff Phone Number:</label>
      <input type="tel" id="staff_phone" name="phone" required>

      <label for="staff_email">Staff Email:</label>
      <input type="email" id="staff_email" name="email" required>

      <label for="staff_gender">Staff Gender:</label>
      <select id="staff_gender" name="sex" required>
        <option value="">Select Gender</option>
        <option value="Male">Male</option>
        <option value="Female">Female</option>
        <option value="Other">Other</option>
      </select>

      <label for="staff_password">Staff Password:</label>
      <input type="password" id="staff_password" name="password" required>

      <label for="staff_picture">Staff Profile Picture:</label>
      <input type="file" id="staff_picture" name="profile_pic" accept="image/*">

      <button type="submit" id="addStaffButton">Add Staff</button>
    </form>
  </div>
</div>

<script>
    // Optionally, you can hide the staff number field in JavaScript if it's still visible
    document.getElementById('staff_number').style.display = 'none';
</script>

  <?php
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
      // Database connection settings
      $host = 'localhost';
      $db = 'banking';
      $user = 'root';
      $password = '';

      $conn = new mysqli($host, $user, $password, $db);

      if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
      }

      // Generate a random staff number (e.g., 6-digit number)
      $staff_number = rand(1000, 9999);

      $name = $_POST['name'] ?? null;
      $phone = $_POST['phone'] ?? null;
      $email = $_POST['email'] ?? null;
      $sex = $_POST['sex'] ?? null;
      // Save the password directly without hashing
      $password = $_POST['password'] ?? null;

      // Handle file upload safely
      $profile_pic_path = null;
      if (isset($_FILES['profile_pic']) && $_FILES['profile_pic']['error'] === UPLOAD_ERR_OK) {
        $profile_pic_tmp = $_FILES['profile_pic']['tmp_name'];
        $profile_pic_name = basename($_FILES['profile_pic']['name']);
        $upload_dir = 'uploads/';
        $profile_pic_path = $upload_dir . $profile_pic_name;

        if (!is_dir($upload_dir)) {
          mkdir($upload_dir, 0777, true);
        }
        move_uploaded_file($profile_pic_tmp, $profile_pic_path);
      }

      // Insert data into the staff table
      if ($name && $phone && $email && $sex && $password) {
        $sql = "INSERT INTO staff (name, staff_number, phone, email, password, sex, profile_pic) 
                VALUES ('$name', '$staff_number', '$phone', '$email', '$password', '$sex', '$profile_pic_path')";

        if ($conn->query($sql) === TRUE) {
          echo "<script>alert('New staff member added successfully!');</script>";
        } else {
          echo "<script>alert('Error: " . $conn->error . "');</script>";
        }
      } else {
        echo "<script>alert('Please fill all the required fields!');</script>";
      }

      $conn->close();
    }
  ?>
</body>
</html>
