<?php
    session_start();
    unset($_SESSION['admin_id']);
    session_destroy();

    header("Location:../staff/staff_login.php");
    exit;