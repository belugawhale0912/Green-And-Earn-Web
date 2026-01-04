<?php
    session_start();
    include "../include/conn.php";

    if(!isset($_SESSION['user_id'])){
        header("Location: ../pages/Landingpage.php");
        exit();
    }

    $friendship_id = $_POST['friendship_id'];
    $action = $_POST['action'];

    if($action=="Accept"){
        $sql = "UPDATE friendship SET status = 'Accepted' WHERE friendship_id = $friendship_id";
        $msg = "You are now friends!";
    }
    else{
        $sql = "DELETE FROM friendship WHERE friendship_id = $friendship_id";
        $msg = "Rejected";
    }

    if(mysqli_query($dbConn,$sql)){
        echo "<script> 
                alert('$msg')
                window.location.href = '../pages/memberFriends.php?view=request'
              </script>";
    }else{
        echo "Error";
    }


?>