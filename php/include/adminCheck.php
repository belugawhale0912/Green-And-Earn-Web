<?php
    // Security: Only Admin (Role 1)
    if(!isset($_SESSION['user_id']) || $_SESSION['role_id'] != 1){
        header("Location: ../pages/Landingpage.php");
        exit();
    }
?>