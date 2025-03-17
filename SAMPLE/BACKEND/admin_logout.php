<?php
    session_start();
    unset($_SESSION['admin_id']);
    session_destroy();

    header("Location:../FRONTEND/admin_login.html");
    exit;