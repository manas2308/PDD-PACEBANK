<?php
$mysqli = mysqli_connect("localhost", "root", "", "banking");
if (mysqli_connect_errno()) {
    echo "Failed to connect to MySQL: " . mysqli_connect_error();
    die();
}

date_default_timezone_set('Asia/Kolkata'); // Set timezone to IST (Indian Standard Time)
$error = "";
