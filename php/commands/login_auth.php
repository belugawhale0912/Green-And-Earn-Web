<?php
session_start();

$username = $_POST["username"];
$password = $_POST["password"];


include "../include/conn.php";

$sql = "SELECT * FROM user WHERE username = '$username'";

$result = mysqli_query($dbConn, $sql);

if (mysqli_num_rows($result)>0) {
    echo "User Found<br>";
    $user_info = mysqli_fetch_assoc($result);
    if (password_verify($password, $user_info['password_hash'])){
        $_SESSION['username'] = $user_info['username']; 
        $_SESSION['role_id'] = $user_info['role_id'];
        $_SESSION['user_id'] = $user_info['user_id'];
        switch($user_info['role_id']){
            case 1:
                header("Location: ../pages/adminDashboard.php");
                break;
            case 2:
                header("Location: ../pages/memberDashboard.php");
                break;
            case 3:
                header("Location: ../pages/moderatorDashboard.php");
                break;
            case 4:
                header("Location: ../pages/partnerDashboard.php");
                break;
        }
        exit();
    }
    else{
        header("Location: ../pages/login.php?error=invalidPassword!");
        exit();
    }
}
else{
    header("Location: ../pages/login.php?error=UserNotFound!");
    exit();
}

