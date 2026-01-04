<?php
    session_start();
    include "../include/conn.php";

    if(!isset($_SESSION['user_id'])){
        header("Location: ../pages/Landingpage.php");
        exit();
    }

    $sql = "SELECT * FROM user WHERE user_id = {$_SESSION['user_id']}";
    $result = mysqli_query($dbConn,$sql);
    $user_info = mysqli_fetch_assoc($result);

    $user_id = $_SESSION['user_id'];

    $new_username = $_POST['username'];
    $new_email = $_POST['email'];
    $new_password = $_POST['password'];
    $password_confirm = $_POST['confirmPassword'];
    if(!empty($new_password)){
        if($new_password == $password_confirm){
            $new_password_hash = password_hash($new_password, PASSWORD_DEFAULT);
        }
        else{
            die("<script>
                    alert('Password Does not match! Update Failed Please Try Again');
                    window.location.href = '../pages/profile.php';
                </script>");
        }
    }
    else{
        $new_password_hash = $user_info['password_hash'];
    }
    $updateSql = "UPDATE user SET
            username = '$new_username',
            email = '$new_email',
            password_hash = '$new_password_hash'
            WHERE user_id = $user_id
            ";

    if(mysqli_query($dbConn,$updateSql)){
        echo "<script>
                alert('Profile Updated Successfully!');
                window.location.href = '../pages/profile.php'; 
              </script>";
    }else{
        echo "Error updating record".mysqli_error($dbConn);
    }
    
?>