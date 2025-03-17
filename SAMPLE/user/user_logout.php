<?php
    session_start();
    unset($_SESSION['admin_id']);
    session_destroy();

    header("Location:../user/user_login.php");
    exit;